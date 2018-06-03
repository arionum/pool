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

register_shutdown_function("shut_down");
###############################################

require_once("db.php");


set_time_limit(0);
if (php_sapi_name() !== 'cli') {
    die("This should only be run as cli");
}

$current = 0;
$ticks = 0;
while (1) {
    $ticks++;
    $ck = $aro->single("SELECT height FROM blocks ORDER by height DESC LIMIT 1");
    if ($ck != $current && $ck) {
        $current = $ck;
        $db->run("UPDATE miners SET historic=historic*0.95+shares, shares=0,bestdl=1000000");
        $db->run("TRUNCATE table nonces");

        $r = $db->run("SELECT * FROM miners WHERE historic>0");
        $total_hr = 0;
        foreach ($r as $x) {
            $thr = $db->single(
                "SELECT SUM(hashrate) FROM workers WHERE miner=:m AND updated>UNIX_TIMESTAMP()-3600",
                [":m" => $x['id']]
            );
            if ($x['historic'] / $thr < 2) {
                $thr = 0;
                echo "$x[id] [$x[historic]] -> ".$x['historic'] / $thr."\n";
            }
            $total_hr += $thr;
        }
        echo "Total hr: $total_hr\n";
        $db->run("UPDATE info SET val=:thr WHERE id='total_hash_rate'", [":thr" => $total_hr]);
    }


    $max_dl = $pool_config['max_deadline'];
    $cache_file = "cache/info.txt";

    $f = file_get_contents($pool_config['node_url']."/mine.php?q=info");
    $g = json_decode($f, true);

    $res = [
        "difficulty" => $g['data']['difficulty'],
        "block"      => $g['data']['block'],
        "height"     => $g['data']['height'],
        "public_key" => $pool_config['public_key'],
        "limit"      => $max_dl,
    ];
    $fin = json_encode(["status" => "ok", "data" => $res, "coin" => "arionum"]);
    echo "\n$fin\n";
    file_put_contents($cache_file, $fin);

    sleep(5);
}
