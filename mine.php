<?php

require_once __DIR__.'/db.php';
set_time_limit(180);

function curl_post($url, $post)
{
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_URL => $url,
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $post,

    ]);
    $resp = curl_exec($curl);
    curl_close($curl);
    return $resp;
}

$ip = $_SERVER['REMOTE_ADDR'];

$q = san($_GET['q']);
$max_dl = $pool_config['max_deadline'];


if ($q === 'info') {
    $time = time();
    if ($_GET['hashrate'] > 0) {
        $miner = san($_GET['address']);
        if ($miner === '3uj7kyCcy5q6A1s1DQkgb58zXz6mLsjHpaoEkYxL6TjzkRP7muGZaXeGNcqk1bTgpQTVDuwPoKh49dGQn8bMwdBZ' ||
            $miner === '4EtWPLwbUAs2JNnqb8yprvAKYCfA4dU3bJTRm2KjnM6f811MAh9qr7wrHABCHrnWPTdgmEF8iXqRBu2XSPHMuHnR') {
            die('invalid wallet address. This address comes from a broken wallet file!');
        }

        $worker = $_GET['worker'];
        $hr = (int)round($_GET['hashrate'],0);
        $gpuhr = (int)round($_GET['gpuhr'],0) + (int)round($_GET['hrgpu'],0);
        $bind = [
            ':id' => $worker,
            ':hr' => $hr,
            ':hr2' => $hr,
            ':miner' => $miner,
            ':ip' => $ip,
            ':ip2' => $ip,
            ':gpuhr' => $gpuhr,
            ':gpuhr2' => $gpuhr,
        ];

    // fix Dan's misreporting
	if ($gpuhr == 0){
  		$f = file_get_contents('cache/info.txt');
  		$g = json_decode($f, true);
		if ($g['data']['height'] % 2 == 1) {
	        	$db->run(
        		'INSERT INTO workers
             		SET id = :id, updated = UNIX_TIMESTAMP(), miner = :miner, ip = :ip, gpuhr = :hr
             		ON DUPLICATE KEY UPDATE updated = UNIX_TIMESTAMP(), ip = :ip2, gpuhr = :hr2',
            		$bind
        		);
		}		
		if ($g['data']['height'] % 2 == 0) {
		        $db->run(
        		'INSERT INTO workers
             		SET id = :id, hashrate = :hr, updated = UNIX_TIMESTAMP(), miner = :miner, ip = :ip
             		ON DUPLICATE KEY UPDATE updated = UNIX_TIMESTAMP(), ip = :ip2, hashrate = :hr2',
            		$bind
        		);
		}
	}
	if ($gpuhr !== 0){

        $db->run(
            'INSERT INTO workers
             SET id = :id, hashrate = :hr, updated = UNIX_TIMESTAMP(), miner = :miner, ip = :ip, gpuhr = :gpuhr
             ON DUPLICATE KEY UPDATE updated = UNIX_TIMESTAMP(), hashrate = :hr2, ip = :ip2, gpuhr = :gpuhr2',
            $bind
        );
	}
    }


    readfile('cache/info.txt');

    exit;
}

if ($q === 'submitNonce') {
    $reject = $db->single('SELECT COUNT(1) FROM rejects WHERE ip=:ip AND data>UNIX_TIMESTAMP()-20', [':ip' => $ip]);
    if ($reject === 1) {
        api_err('rejected');
    }

    $nonce = san(substr($_POST['nonce'], 0, 120));
    $argon = $_POST['argon'];
    $address = san($_POST['address']);

    $chk = $db->single('SELECT count(1) FROM nonces WHERE nonce=:nonce', [':nonce' => $argon]);
    if ($chk !== 0) {
        $db->run('INSERT into abusers SET miner=:miner, nonce=:nonce', [':miner' => $address, ':nonce' => $argon]);
        api_err('duplicate');
        exit;
    }

    $db->run('INSERT IGNORE into nonces SET nonce=:nonce', [':nonce' => $argon]);

    $f = file_get_contents($pool_config['node_url'].'/mine.php?q=info');
    $g = json_decode($f, true);
    
    if ((int)$_POST['height'] > 1 && $g['data']['height'] !== (int)$_POST['height']) {
        api_err('stale block');
    }

    $public_key = $pool_config['public_key'];


    $argon2 = '$argon2i$v=19$m=524288,t=1,p=1'.$argon;
    if ($g['data']['height'] >= 80000 && $g['data']['height'] % 2 !== 0) {
        $argon2 = '$argon2i$v=19$m=16384,t=4,p=4'.$argon;
    }


    $base = "$public_key-$nonce-".$g['data']['block'].'-'.$g['data']['difficulty'];



    if (isset($pool_config['validator_url'])) { // If validator url exists, use it for hash validation
        $res = file_get_contents($pool_config['validator_url'].'/validate?argon='. $argon2 . '&base=' . $base);
        if ($res !== 'VALID') {
            api_err("Invalid argon - $base - $argon2");
        } 
    } else { // Validate hash old fashion way
        if (!password_verify($base, $argon2)) {
            api_err("Invalid argon - $base - $argon2");
        }
    }


    $hash = $base.$argon2;

    for ($i = 0; $i < 5; $i++) {
        $hash = hash('sha512', $hash, true);
    }
    $hash = hash('sha512', $hash);

    $m = str_split($hash, 2);

    $duration = hexdec($m[10]).hexdec($m[15]).hexdec($m[20]).hexdec($m[23]).
        hexdec($m[31]).hexdec($m[40]).hexdec($m[45]).hexdec($m[55]);
    $duration = ltrim($duration, '0');
    $result = gmp_div($duration, $g['data']['difficulty']);

    if ($result > 0 && $result <= 240) {
        $private_key = $pool_config['private_key'];
        $postdata = http_build_query(
            compact('argon', 'nonce', 'private_key', 'public_key')
        );

        $opts = [
            'http' =>
                [
                    'method' => 'POST',
                    'header' => 'Content-type: application/x-www-form-urlencoded',
                    'content' => $postdata,
                    'timeout' => 120,
                ],
        ];

        $context = stream_context_create($opts);

        $res = file_get_contents($pool_config['node_url'].'/mine.php?q=submitNonce', false, $context);
        $data = json_decode($res, true);
        if ($data['status'] === 'ok') {
            $bl = $aro->row('SELECT * FROM blocks ORDER by height DESC LIMIT 1');
            $added = $db->single('SELECT COUNT(1) FROM blocks WHERE id=:id', [':id' => $bl['id']]);

            if ($added === 0 && $bl['generator'] === $pool_config['address']) {
                $reward = $aro->single(
                    "SELECT val FROM transactions WHERE block=:bl AND message='' AND version=0",
                    [':bl' => $bl['id']]
                );
                if ($reward === 0) {
                    api_err('something went wrong');
                }
                $original_reward = $reward;
                $r = $db->run('SELECT * FROM miners WHERE shares>0 OR historic>0');
                foreach ($r as $x) {
                    $total_shares += $x['shares'];
                    $total_historic += $x['historic'];
                }
                $reward *= (1 - $pool_config['fee']);
                $miner_reward = $pool_config['miner_reward'] * $reward;
                $historic_reward = $pool_config['historic_reward'] * $reward;
                $current_reward = $pool_config['current_reward'] * $reward;

                foreach ($r as $x) {
                    $crw = 0;
                    if ($x['shares'] > 0) {
                        $crw += ($x['shares'] / $total_shares) * $current_reward;
                    }
                    if ($x['historic'] > 0) {
                        $crw += ($x['historic'] / $total_historic) * $historic_reward;
                    }
                    if ($x['id'] === $address) {
                        $crw += $miner_reward;
                    }
                    $db->run(
                        "INSERT into payments SET address=:to, block=:bl, height=:height, val=:val, txn='',done=0",
                        [':val' => $crw, ':height' => $bl['height'], ':bl' => $bl['id'], ':to' => $x['id']]
                    );
                }
                $db->run("INSERT into payments SET address=:to, block=:bl, height=:height, val=:val, txn='',done=0", [
                    ':val' => $original_reward * $pool_config['fee'],
                    ':height' => $bl['height'],
                    ':bl' => $bl['id'],
                    ':to' => $pool_config['fee_address'],
                ]);

                $db->run('INSERT IGNORE into blocks SET reward=:reward, id=:id, height=:height, miner=:miner', [
                    ':id' => $bl['id'],
                    ':miner' => $address,
                    ':height' => $bl['height'],
                    ':reward' => $original_reward,
                ]);

                api_echo('accepted');
            }
            api_err('rejected - block changed - 2');
        }
        api_err('rejected - block changed - 1');
    } elseif ($result > 0 && $result <= $max_dl) {
        $share = ceil(($max_dl - $result) / 100);

        $db->run(
            'INSERT INTO miners
             SET id = :id, shares = shares + :sh, updated = UNIX_TIMESTAMP(), bestdl = :bdl
             ON DUPLICATE KEY UPDATE shares = shares + :sh2, updated = UNIX_TIMESTAMP()',
            [':id' => $address, ':sh' => $share, ':sh2' => $share, ':bdl' => (int)$result]
        );
        $db->run(
            'UPDATE miners SET bestdl=:bdl WHERE id=:id AND bestdl>:bdl2',
            [':id' => $address, ':bdl' => (int)$result, ':bdl2' => (int)$result]
        );
        api_echo('accepted');
    } else {
        $db->run('DELETE FROM nonces WHERE nonce=:nonce', ['nonce' => $argon]);

        $db->run(
            'INSERT into rejects SET ip=:ip, data=UNIX_TIMESTAMP() ON DUPLICATE KEY update data=UNIX_TIMESTAMP()',
            [':ip' => $ip]
        );
        api_err("rejected - $result");
    }

    api_err('rejected - block changed');
}

api_err('invalid command');
