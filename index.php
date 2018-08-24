<?php
#die("maintenance");
require("db.php");
$q = san($_GET['q']);

use Rain\Tpl;

Tpl::configure('tpl_dir', 'template/');
Tpl::configure('debug', true);
Tpl::configure('cache_dir', 'cache/template/');
Tpl::configure('path_replace', false);
Tpl::configure('php_enabled', false);

$tpl = new Tpl();
$tpl->assign("q", $q);
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
        $total_hr_ext = "KH/s";
    } else {
        $total_hr_text = number_format($total_hr)." H/s";
        $total_hr_ext = "H/s";
    }

    $total_hr=$db->single("SELECT val FROM info WHERE id='total_gpu_hr'");

    if($total_hr>=1000000){
        $total_gpu_text=number_format($total_hr/1000000,2);
        $total_gpu_ext="MH/s";
    }
    elseif($total_hr>1000&&$total_hr<1000000) {
        $total_gpu_text=number_format($total_hr/1000,2);
        $total_gpu_ext="KH/s";
    } else {
        $total_gpu_text=number_format($total_hr)." H/s";
        $total_gpu_ext="H/s";
    }

       $tpl->assign("gpu_ext",$total_gpu_ext);
        $tpl->assign("total_gpu",$total_gpu_text);

    
    
    $tpl->assign("avg_hr", $avg_hr);
    $tpl->assign("hr_ext", $total_hr_ext);
    $tpl->assign("total_hr", $total_hr_text);
    $tpl->assign("miners", $miners);
    $tpl->assign("total_shares", $total_shares);
    $tpl->assign("total_historic", $total_historic);
    $tpl->assign("height", $current['height']);
    $tpl->assign("lastwon", $last_won);
    $tpl->assign("total_paid", number_format($db->single("SELECT val FROM info WHERE id='total_paid'") / 1000000, 2));
    $tpl->assign("shares", $shares);
    $tpl->assign("historic", $historic);
    $tpl->assign("difficulty", 200000000 - $current['difficulty']);

    $tpl->draw("index");
} elseif ($q == "blocks") {
    $r = $db->run("SELECT * FROM blocks ORDER by height DESC LIMIT 100");
    $b = [];
    foreach ($r as $x) {
        $x['reward'] = number_format($x['reward'], 2);
        $b[] = $x;
    }

    $tpl->assign("blocks", $b);
    $tpl->draw("blocks");
} elseif ($q == "payments") {
    $r = $db->run("SELECT id,address,val,done,txn FROM payments ORDER by id DESC LIMIT 5000");
    $b = [];
    foreach ($r as $x) {
        if ($x['done'] == 0) {
            $x['txn'] = "Pending";
        }
        $b[] = $x;
    }

    $tpl->assign("payments", $b);
    $tpl->draw("payments");
} elseif ($q == "benchmarks") {
    $tpl->draw("benchmarks");
} elseif ($q == "info") {
    $tpl->draw("info");
}

$tpl = new Tpl();
$tpl->assign("q", $q);
$tpl->draw("footer");
