<?php
#################  PID SYSTEM #################
$script_name = __FILE__;
$scripta = explode('/', $script_name);
$script_name = $scripta[count($scripta) - 1];
if (empty($script_name)) {
    exit;
}
$pid112 = '/var/run/'.$script_name.'.pid';
$pid_exists = file_exists($pid112);
$pid_time = 0;
if ($pid_exists) {
    $pid_time = filemtime($pid112);
    if (time() - $pid_time > 3600) {
        system("rm -rf $pid112");
    }
    die("\n\n### RUNNING ### -- PID: $pid112\n\n");
}
system("touch $pid112");
function shut_down()
{
    global $pid112;
    system("rm -rf $pid112");
    echo "\n# ShutDown #\n";
}

register_shutdown_function('shut_down');
###############################################

set_time_limit(0);
if (PHP_SAPI !== 'cli') {
    die('This should only be run as cli');
}

require_once __DIR__.'/db.php';

if ($pool_config['blocks_paid'] == null) {
    die('Blocks Paid variable not set in config');
}

echo "\n----------------------------------------------------------------------------------\n";
$current = $aro->single('SELECT height FROM blocks ORDER by height DESC LIMIT 1');
echo "Current block $current\n";

$db->run('DELETE FROM miners WHERE historic + shares <= 30');
$db->run('UPDATE miners
          SET gpuhr = (
            SELECT SUM(gpuhr)
            FROM workers
            WHERE miner = miners.id AND updated > UNIX_TIMESTAMP() - 1000
          )');
$db->run('UPDATE miners
          SET hashrate = (
            SELECT SUM(hashrate)
            FROM workers
            WHERE miner = miners.id AND updated > UNIX_TIMESTAMP() - 1000
          )');
//uit sanity

 $r = $db->run('SELECT * FROM miners WHERE historic + shares >0');
        $total_hr = 0;
        $total_gpu = 0;
        foreach ($r as $x) {
            $thr = $db->row(
                'SELECT SUM(hashrate) AS cpu, SUM(gpuhr) AS gpu
                 FROM workers
                 WHERE miner = :m AND updated > UNIX_TIMESTAMP() - 1000',
                [':m' => $x['id']]
            );
            if ($x['historic'] / $thr['cpu'] < 2 || $x['historic'] / $thr['gpu'] < 2) {
                $thr['cpu'] = 0;
                $thr['gpu'] = 0;
            }
            $total_hr += $thr['cpu'];
            $total_gpu += $thr['gpu'];
        }
        echo "Total hr: $total_hr, total gpuhr: $total_gpu\n";
        $db->run("UPDATE info SET val=:thr WHERE id='total_hash_rate'", [':thr' => $total_hr]);
        $db->run("UPDATE info SET val=:thr WHERE id='total_gpu_hr'", [':thr' => $total_gpu]);

//cleanup

//$db->run('DELETE FROM miners WHERE shares=0 AND historic=0 AND updated<UNIX_TIMESTAMP()-86400');
$db->run('DELETE FROM miners WHERE shares + historic <=50 AND updated<UNIX_TIMESTAMP()-86400');
$db->run('DELETE FROM workers WHERE updated<UNIX_TIMESTAMP()-1000');

// hier wordt het pending op de dashboard aangepast
$db->run(
    'UPDATE miners
     SET pending = (
       SELECT SUM(val)
       FROM payments
       WHERE done = 0 AND payments.address = miners.id AND height >= :h
     )',
    [':h' => $current - $pool_config['blocks_paid']]
);


//count orphans
    $r = $db->run("SELECT * FROM blocks where orphan=0 ORDER by height DESC LIMIT 100");
    foreach ($r as $x) {
        echo("Processing block height:".$x['height'].", orphan:".$x['orphan']." \n");
        if (($pool_config['keep_orphans'] == true) && ($x['orphan'] == 0)) {
            echo("Updating block height:".$x['height'].", orphan:".$x['orphan']." \n");

            $f = file_get_contents($pool_config['node_url'].'/api.php?q=getBlock&height='.$x['height']);
            $g = json_decode($f, true);
            $oheight = $x['height'];

            if ($g['data']['generator']) {
                $x['generator'] = $g['data']['generator'];
                if ( $pool_config['address'] != $g['data']['generator'] ) {

                    //stealer alias
                    $fa = file_get_contents($pool_config['node_url'].'/api.php?q=getAlias&account='.$g['data']['generator']);
                    $ga = json_decode($fa, true);

                    if ( trim($ga['data']) !== '' ){
                      $x['stealer'] = $ga['data'];
                    }else{
                      $x['stealer'] = $g['data']['generator'];
                    }

                    $bind = [
                      ':height' => $x['height'],
                      ':miner' => $x['stealer']
                    ];

                    $db->run("UPDATE blocks SET orphan=1, miner = :miner where height = :height",$bind);
                } else {
                    $bind = [
                      ':height' => $x['height']
                    ];

                   $db->run("UPDATE blocks SET orphan=-1 where height = :height",$bind);
                }
            }
        }
    }




