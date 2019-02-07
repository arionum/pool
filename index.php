<?php
#die("maintenance");
require("db.php");
$q = san($_GET['q']);
$id = san($_GET['id']);

use Rain\Tpl;

Tpl::configure('tpl_dir', 'template/');
Tpl::configure('debug', true);
Tpl::configure('cache_dir', 'cache/template/');
Tpl::configure('path_replace', false);
Tpl::configure('php_enabled', false);

$tpl = new Tpl();
$tpl->assign("q", $q);
$tpl->assign("id", $id);
$tpl->assign("pool_name", $pool_config['pool_name']);
$tpl->draw('header');

if ($q == "") {
    $current = $aro->row("SELECT * FROM blocks ORDER by height DESC LIMIT 1");
    $last_won = $db->single("SELECT height FROM blocks ORDER by height DESC LIMIT 1");

    $total_shares = 0;
    $shares = [];

    $p = $db->run("SELECT * FROM miners WHERE hashrate>0 OR gpuhr>0");
    $miners = count($p);

    $r = $db->run("SELECT * FROM miners");
    $total_historic = 0;

    $historic = [];
    foreach ($r as $x) {
        $total_shares += $x['shares'];
        $total_historic += $x['historic'];
    }
    foreach ($r as $x) {
        $x['percent'] = number_format(($x['shares'] / $total_shares) * 100, 2);
        if ($x['shares'] > 0) {
            $shares[] = $x;
        }

        $x['percent'] = number_format(($x['historic'] / $total_historic) * 100, 2);
        $x['pending'] = number_format($x['pending'], 2);
        $x['total_paid'] = number_format($x['total_paid'], 2);
        $historic[] = $x;
    }

    $total_hr=$db->single("SELECT val FROM info WHERE id='total_hash_rate'");
    $avg_hr = number_format(($total_hr / $miners), 0);
    if ($miners == 0) {
        $avg_hr = 0;
    }

    if ($total_hr >= 1000000) {
        $total_hr_text = number_format($total_hr / 1000000, 2);
        $total_hr_ext = "MH/s";
    } elseif ($total_hr > 1000 && $total_hr < 1000000) {
        $total_hr_text = number_format($total_hr / 1000, 2);
        $total_hr_ext = "kH/s";
    } else {
        $total_hr_text = number_format($total_hr);
        $total_hr_ext = "H/s";
    }

    $tpl->assign("hr_ext", $total_hr_ext);
    $tpl->assign("total_hr", $total_hr_text);


    $total_gpuhr = $db->single("SELECT val FROM info WHERE id='total_gpu_hr'");
    $avg_gpuhr = number_format($total_gpuhr / $miners, 0);
    if ($miners == 0) {
        $avg_gpuhr = 0;
    }

    if($total_gpuhr>=1000000){
        $total_gpu_text=number_format($total_gpuhr/1000000,2);
        $total_gpu_ext="MH/s";
    }
    elseif($total_gpuhr>1000&&$total_gpuhr<1000000) {
        $total_gpu_text=number_format($total_gpuhr/1000,2);
        $total_gpu_ext="kH/s";
    } else {
        $total_gpu_text=number_format($total_gpuhr);
        $total_gpu_ext="H/s";
    }

    $tpl->assign("gpu_ext",$total_gpu_ext);
    $tpl->assign("total_gpu",$total_gpu_text);

    $agem = time();    
    $agem = $current['date'];

    $agem = ( time() - $current['date']); 
    
    $tpl->assign("avg_hr", $avg_hr);
    $tpl->assign("avg_gpuhr", $avg_gpuhr);
    $tpl->assign("miners", $miners);
    $tpl->assign("total_shares", $total_shares);
    $tpl->assign("total_historic", $total_historic);
    $tpl->assign("height", $current['height']);
    $tpl->assign("lastwon", $last_won);
    $tpl->assign("total_paid", number_format($db->single("SELECT val FROM info WHERE id='total_paid'") / 1000000, 3));
    $tpl->assign("shares", $shares);
    $tpl->assign("historic", $historic);
    $tpl->assign("difficulty", 200000000 - $current['difficulty']);
    if ($current['height'] % 2) $blocktype = "GPU"; else $blocktype = "CPU"; 
    $tpl->assign("blocktype", $blocktype);    
    $tpl->assign("agem", $agem);


    $tpl->draw("index");
} elseif ($q == 'acc') {

    $r = $db->run("SELECT concat(id) AS id, sum(hashrate) AS hashrate, sum(gpuhr) as gpuhr, updated FROM workers WHERE miner=:miner GROUP BY id",  [":miner" => $id] );
    $b = [];
    foreach ($r as $x) {
        $x['hashrate'] = number_format($x['hashrate'], 0);
        $x['gpuhr'] = number_format($x['gpuhr'], 0);
        $x['updated'] = date('Y/m/d H:i:s', $x['updated']);
        
        $b[] = $x;
    }
    $tpl->assign("workers", $b);

	$yesterday=time()-86400;
	$yesterday_block=$aro->single("SELECT height+1 FROM blocks WHERE date<=$yesterday ORDER by height DESC LIMIT 1");
	$last_payment_txn=$db->single("SELECT txn FROM payments WHERE address=:miner AND done=1 ORDER by height DESC LIMIT 1", [":miner" => $id]);
	$last_payment_time=$aro->single("SELECT date FROM transactions WHERE id=$last_payment_txn");
	$last_payment=$db->single("SELECT SUM(val) FROM payments WHERE txn=:lasttxn AND done=1", [":lasttxn" => $last_payment_txn]);
	$past_24h=$db->single("SELECT SUM(val) FROM payments WHERE address=:miner AND height>=$yesterday_block AND done=1", [":miner" => $id]);

    $r = $db->run("SELECT * FROM miners WHERE id=:miner",  [":miner" => $id] );
    $b = [];
    foreach ($r as $x) {
        $x['hashrate'] = number_format($x['hashrate'], 0);
        $x['gpuhr'] = number_format($x['gpuhr'], 0);
        if ($x['bestdl'] == 1000000) {
            $x['bestdl'] = "No nonce submitted";
        }

        $x['pending'] = number_format($x['pending'], 2);
        $x['total_paid'] = number_format($x['total_paid'], 2);
	$x['last_payment'] = number_format($last_payment, 2);
	$x['last_paid'] =  date('Y/m/d H:i:s', $last_payment_time);
        if ($last_payment_time == false) {
            $x['last_paid'] = "Payment in process";
        }
        if ($last_payment == 0 ) {
            $x['last_paid'] = "No payment yet";
        }
	$x['last_txn'] = $last_payment_txn;
	$x['24h_paid'] = number_format($past_24h,2);
        $x['updated'] = date('Y/m/d H:i:s', $x['updated']); 
        $b[] = $x;
    }
    $tpl->assign("account", $b);


    $r = $db->run("SELECT sum(hashrate) / count(id) AS cpuhr, sum(gpuhr) / count(id) as gpuhr FROM workers WHERE miner=:miner GROUP BY id",  [":miner" => $id]);
    $c['cpuhr'] = 0;
    $c['gpuhr'] = 0;
    foreach ($r as $x) {
        $c['cpuhr'] = $c['cpuhr'] + $x['cpuhr'];
        $c['gpuhr'] = $c['gpuhr'] + $x['gpuhr'];
    }

    $tpl->assign("hashrate", $c);


    $tpl->draw("account");

} elseif ($q == "blocks") {
    $r = $db->run("SELECT * FROM blocks ORDER by height DESC LIMIT 1000");
    $b = [];
    foreach ($r as $x) {
        $x['reward'] = number_format($x['reward'], 2);
        $b[] = $x;
    }

    $tpl->assign("blocks", $b);
    $tpl->draw("blocks");
} elseif ($q == "payments") {
    $r = $db->run("SELECT id,address,val,done,height,txn FROM payments ORDER by id DESC LIMIT 5000");
    $b = [];
    foreach ($r as $x) {
        if ($x['done'] == 0) {
            $x['height'] = "Pending";
        }
        $b[] = $x;
    }

    $tpl->assign("payments", $b);
    $tpl->draw("payments");
//} elseif ($q == "benchmarks") {
//    $tpl->draw("benchmarks");
} elseif ($q == "info") {

    $tpl->assign("minpayout", number_format($pool_config['min_payout'],2));
    $tpl->assign("fee", number_format($pool_config['fee']*100,1));
    $tpl->assign("cpu_deadline", number_format($pool_config['max_deadline']));
    $tpl->assign("gpu_deadline", number_format($pool_config['max_deadline_gpu']));
    $tpl->assign("poolwallet", $pool_config['address']);
    $tpl->assign("current_reward", $pool_config['current_reward']*100,0);
    $tpl->assign("miner_reward", $pool_config['miner_reward']*100,0);
    $tpl->assign("historic_reward", $pool_config['historic_reward']*100,0);
    $tpl->assign("server", gethostname());
    $tpl->assign("pool_url", $pool_config['pool_url']);
    $tpl->assign("pool_degradation", number_format($pool_config['pool_degradation']*100,1));
    $tpl->assign("handle", $pool_config['handle']);



    $tpl->draw("info");
}

$tpl = new Tpl();
$tpl->assign("q", $q);
$tpl->draw("footer");
