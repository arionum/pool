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

require_once __DIR__.'/db.php';
set_time_limit(0);

if (PHP_SAPI !== 'cli') {
    die('This should only be run as cli');
}

if ($pool_config['pool_degradation']==null) {die('Degradation rate not set in config');}

$current = 0;
$ticks = 0;
while (1) {
    $ticks++;
    $ck = $aro->single('SELECT height FROM blocks ORDER by height DESC LIMIT 1');
    if ($ck !== $current && $ck) {
        $current = $ck;
        $db->run('UPDATE miners SET historic=historic+shares-historic*:dr, shares=0,bestdl=1000000', [':dr' => $pool_config['pool_degradation']]);
        $db->run('TRUNCATE table nonces');
    }

    $max_dl = ($current % 2) ? $pool_config['max_deadline_gpu'] : $pool_config['max_deadline'];

    $cache_file = __DIR__."/cache/info.txt";

    $f = file_get_contents($pool_config['node_url'].'/mine.php?q=info');
    $g = json_decode($f, true);

    $res = [
        'difficulty' => $g['data']['difficulty'],
        'block' => $g['data']['block'],
        'height' => $g['data']['height'],
        'public_key' => $pool_config['public_key'],
        'limit' => $max_dl,
        'recommendation' => $g['data']['recommendation'],
        'argon_mem' => $g['data']['argon_mem'],
        'argon_threads' => $g['data']['argon_threads'],
        'argon_time' => $g['data']['argon_time'],
    ];
    $fin = json_encode(['status' => 'ok', 'data' => $res, 'coin' => 'arionum']);
    echo "\n$fin\n";
    file_put_contents($cache_file, $fin);

    sleep(5);
}
