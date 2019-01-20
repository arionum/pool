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
$tpl->draw('header');

if ($q == "") {
    $current = $aro->row("SELECT * FROM blocks ORDER by height DESC LIMIT 1");
    $last_won = $db->single("SELECT height FROM blocks ORDER by height DESC LIMIT 1");

    $total_shares = 0;
    $shares = [];

    $r = $db->run("SELECT * FROM miners ");
    $miners = count($r);
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

    $total_hr = $db->single("SELECT val FROM info WHERE id='total_hash_rate'");
    $avg_hr = floor($total_hr / $miners);
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

    $total_hr=$db->single("SELECT val FROM info WHERE id='total_gpu_hr'");

    if($total_hr>=1000000){
        $total_gpu_text=number_format($total_hr/1000000,2);
        $total_gpu_ext="MH/s";
    }
    elseif($total_hr>1000&&$total_hr<1000000) {
        $total_gpu_text=number_format($total_hr/1000,2);
        $total_gpu_ext="kH/s";
    } else {
        $total_gpu_text=number_format($total_hr);
        $total_gpu_ext="H/s";
    }

       $tpl->assign("gpu_ext",$total_gpu_ext);
        $tpl->assign("total_gpu",$total_gpu_text);

    $agem = time();    
    $agem = $current['date'];

    $agem = ( time() - $current['date']) ; 
    
    $tpl->assign("avg_hr", $avg_hr);
    $tpl->assign("hr_ext", $total_hr_ext);
    $tpl->assign("total_hr", $total_hr_text);
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


    $r = $db->run("SELECT * FROM miners WHERE id=:miner",  [":miner" => $id] );
    $b = [];
    foreach ($r as $x) {
        $x['hashrate'] = number_format($x['hashrate'], 0);
        $x['gpuhr'] = number_format($x['gpuhr'], 0);

        $x['pending'] = number_format($x['pending'], 2);
        $x['total_paid'] = number_format($x['total_paid'], 2);

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
    $tpl->draw("info");
}

$tpl = new Tpl();
$tpl->assign("q", $q);
$tpl->draw("footer");
