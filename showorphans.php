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

//count orphans
    $r = $db->run("SELECT * FROM blocks ORDER by height DESC LIMIT 100");
    $oc = 0;
    foreach ($r as $x) {
        echo("Processing block height:".$x['height'].", orphan:".$x['orphan']." \n");
        if (($pool_config['keep_orphans'] == true) && ($x['orphan'] < 2)) {
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

                    //$db->run("UPDATE blocks SET orphan=1, miner = :miner where height = :height",$bind);
                    $oc = $oc + 1;
                    echo("ORPHAN:".$x['height']." \n");
                } else {
                    $bind = [
                      ':height' => $x['height']
                    ];

                   //$db->run("UPDATE blocks SET orphan=-1 where height = :height",$bind);
                }
            }
        }
    }
echo("ORPHAN COUNT: $oc \n");




