<?php
/*
Usage: 

/api?q=status			returns current status of the pool
/api?q=miner&m=<wallet>		returns total hashrate and shares of miner
/api?q=payments&m=<wallet>	returns payments of miner
/api.php without flags 		also returns pool status at the moment for compatibility reasons

*/

header('Content-Type: application/json');
include("db.php");

$q = $_GET['q'];


if ($q == "miner") {

	$m = $_GET['m'];

	if ($m == null) {
                echo "Invalid request";
                }

        else {
		$miner=$m;
		$hashrate=$db->single("SELECT hashrate FROM miners WHERE id='$m'");
		$gpu_hr=$db->single("SELECT gpuhr FROM miners WHERE id='$m'");
		$historic=$db->single("SELECT historic FROM miners WHERE id='$m'");
		$shares=$db->single("SELECT shares FROM miners WHERE id='$m'");
		echo json_encode(array("miner"=>$miner, "cpu_hr"=>$hashrate, "gpu_hr"=>$gpu_hr, "historic shares"=>$historic, "current shares"=>$shares));
		}

	}

elseif ($q == "status") {

	$total_hr=$db->single("SELECT val FROM info WHERE id='total_hash_rate'");
	$total_gpu=$db->single("SELECT val FROM info WHERE id='total_gpu_hr'");
	$current=$aro->single("SELECT height FROM blocks ORDER by height DESC LIMIT 1");
	$miners=$db->single("SELECT COUNT(1) FROM miners");
	$last_won=$db->single("SELECT height FROM blocks ORDER by height DESC LIMIT 1");
	$last_won_time=$aro->single("SELECT date FROM blocks WHERE height=:h",[":h"=>$last_won]);
	echo json_encode(array("cpu_hr"=>$total_hr, "gpu_hr"=>$total_gpu, "current_block_height"=>$current, "last_won_block"=>$last_won, "last_won_block_time"=>$last_won_time, "miners"=>$miners, "fee"=>$pool_config['fee']));
	} 


elseif ($q == "payments") { 

	$m = $_GET['m'];

	if ($m == null) {
                echo "Invalid request";
                }

	else {
		$yesterday=time()-86400;
		$yesterday_block=$aro->single("SELECT height+1 FROM blocks WHERE date<=$yesterday ORDER by height DESC LIMIT 1");
		$last_payment_txn=$db->single("SELECT txn FROM payments WHERE address='$m' AND done=1 ORDER by height DESC LIMIT 1");
		$last_payment_time=$aro->single("SELECT date FROM transactions WHERE id=$last_payment_txn");
		$last_payment_humantime=date("d.m.y - H:i:s", $last_payment_time);
		$miner=$m;
		$total_paid=$db->single("SELECT total_paid FROM miners WHERE id='$m'");
		$pending=$db->single("SELECT pending FROM miners WHERE id='$m'");
		$last_payment=$db->single("SELECT val FROM payments WHERE address='$m' AND done=1 ORDER by height DESC LIMIT 1");
		$past_24h=$db->single("SELECT SUM(val) FROM payments WHERE address='$m' AND height>=$yesterday_block AND done=1");
		echo json_encode(array("miner"=>$miner, "total paid"=>$total_paid, "pending"=>$pending, "past_24h"=>$past_24h, "last_payment"=>$last_payment, "last_payment_date"=>$last_payment_humantime, "last_payment_unixtime"=>$last_payment_time));
		}
	}

else {
	// we keep this here as this is the old aropool.com api, for compatibility
	$total_hr=$db->single("SELECT val FROM info WHERE id='total_hash_rate'");
	$total_gpu=$db->single("SELECT val FROM info WHERE id='total_gpu_hr'");
	$current=$aro->single("SELECT height FROM blocks ORDER by height DESC LIMIT 1");
	$miners=$db->single("SELECT COUNT(1) FROM miners");
	$last_won=$db->single("SELECT height FROM blocks ORDER by height DESC LIMIT 1");
	$last_won_time=$aro->single("SELECT date FROM blocks WHERE height=:h",[":h"=>$last_won]);
	echo json_encode(array("cpu_hr"=>$total_hr, "gpu_hr"=>$total_gpu, "current_block_height"=>$current, "last_won_block"=>$last_won, "last_won_block_time"=>$last_won_time, "miners"=>$miners, "fee"=>$pool_config['fee']));
	echo "\n\nUsage api.php/q=status or /q=miner&m=walletaddress or /q=payments&m=walletaddress";
	} 
