<?php
/*
Usage: 

/api?q=status			returns current status of the pool
/api?q=miner&id=<wallet>		returns total hashrate and shares of miner
/api?q=payments&id=<wallet>	returns payments of miner
/api?id=<wallet>			returns total hashrate and shares and payments of miner // do we need this, want this?
/api.php without flags 		also returns pool status at the moment for compatibility reasons

*/

header('Content-Type: application/json');
require_once __DIR__.'/db.php';

$q = san($_GET['q']);


if ($q == "miner") {

	$id = san($_GET['id']);

	if ($id == null) {

			api_err("Invalid request");

		} else {

			$hashrate=$db->single("SELECT hashrate FROM miners WHERE id=:id",[":id"=>$id]);
			$gpu_hr=$db->single("SELECT gpuhr FROM miners WHERE id=:id",[":id"=>$id]);
			$historic=$db->single("SELECT historic FROM miners WHERE id=:id",[":id"=>$id]);
			$shares=$db->single("SELECT (shares FROM miners WHERE id=:id",[":id"=>$id]);
			$update=$db->single("SELECT updated FROM miners WHERE id=:id",[":id"=>$id]);
				if ($update == false) {
					$update = "No nonce submitted";
				}
				if ($shares == false) {
					$shares = "0";
				}
				if ($historic == false) {
					$historic = "0";
				}
			if ($hashrate == false) {
					$hashrate = "0";
				}
			if ($gpu_hr == false) {
					$gpu_hr = "0";
				}

			echo json_encode(array("miner"=>$id, "cpu_hr"=>$hashrate, "gpu_hr"=>$gpu_hr, "historic shares"=>$historic, "current shares"=>$shares, "last nonce submitted"=>$update));
		}

	}

elseif ($q == "status") {

		$total_hr=$db->single("SELECT val FROM info WHERE id='total_hash_rate'");
		$total_gpu=$db->single("SELECT val FROM info WHERE id='total_gpu_hr'");
		$current=$aro->single("SELECT height FROM blocks ORDER by height DESC LIMIT 1");
		$miners=$db->single("SELECT COUNT(1) FROM miners WHERE hashrate>0 OR gpuhr>0");
		$last_won=$db->single("SELECT height FROM blocks ORDER by height DESC LIMIT 1");
		$last_won_time=$aro->single("SELECT date FROM blocks WHERE height=:h",[":h"=>$last_won]);

		if ($last_won_time == false) {
			$last_won_time = "Never";
		}
			
		$avg_gpuhr = number_format($total_gpu / $miners, 0);
		$avg_hr = number_format(($total_hr / $miners), 0);

		if ($miners == 0) {
			$avg_gpuhr = 0;
			$avg_hr = 0;
		}

		echo json_encode(array("cpu_hr"=>$total_hr, "gpu_hr"=>$total_gpu, "current_block_height"=>$current, "last_won_block"=>$last_won, "last_won_block_time"=>$last_won_time, "active miners"=>$miners, "avg_hr"=>$avg_hr, "avg_gpuhr"=>$avg_gpuhr, "fee"=>$pool_config['fee']));
	
	} elseif ($q == "payments") { 

		$id = san($_GET['id']);

		if ($id == null) {

			api_err("Invalid request");
		
		} else {
				$yesterday=time()-86400;
				$yesterday_block=$aro->single("SELECT height+1 FROM blocks WHERE date<=$yesterday ORDER by height DESC LIMIT 1");
				$last_payment_txn=$db->single("SELECT txn FROM payments WHERE address=:id AND done=1 ORDER by height DESC LIMIT 1",[":id"=>$id]);
				$last_payment_time=$aro->single("SELECT date FROM transactions WHERE id=$last_payment_txn");
				$miner=$m;
				$total_paid=$db->single("SELECT total_paid FROM miners WHERE id=:id",[":id"=>$id]);
				$pending=$db->single("SELECT pending FROM miners WHERE id=:id",[":id"=>$id]);
				$last_payment=$db->single("SELECT SUM(val) FROM payments WHERE txn=:lasttxn AND done=1",[":lasttxn"=>$last_payment_txn]);

				if ($last_payment_time == false) {
					$last_payment_time = "Payment in process";
				}

				if ($last_payment == 0 ) {
					$last_payment_time = "No payment yet";
				}

				$past_24h=$db->single("SELECT SUM(val) FROM payments WHERE address=:id AND height>=$yesterday_block AND done=1",[":id"=>$id]);

				echo json_encode(array("miner"=>$id, "total paid"=>$total_paid, "pending"=>$pending, "past_24h"=>$past_24h, "last_payment"=>$last_payment, "last_payment_date"=>$last_payment_time));
			}
		}


	else {

		$id = san($_GET['id']);

        if ($id == null) {

			// we keep this here as this is the old aropool.com api, for compatibility
			$total_hr=$db->single("SELECT val FROM info WHERE id='total_hash_rate'");
			$total_gpu=$db->single("SELECT val FROM info WHERE id='total_gpu_hr'");
			$current=$aro->single("SELECT height FROM blocks ORDER by height DESC LIMIT 1");
			$miners=$db->single("SELECT COUNT(1) FROM miners");
			$last_won=$db->single("SELECT height FROM blocks ORDER by height DESC LIMIT 1");
			$last_won_time=$aro->single("SELECT date FROM blocks WHERE height=:h",[":h"=>$last_won]);
			echo json_encode(array("cpu_hr"=>$total_hr, "gpu_hr"=>$total_gpu, "current_block_height"=>$current, "last_won_block"=>$last_won, "last_won_block_time"=>$last_won_time, "miners"=>$miners, "fee"=>$pool_config['fee']));
		
		} else {

			$hashrate=$db->single("SELECT hashrate FROM miners WHERE id=:id",[":id"=>$id]);
			$gpu_hr=$db->single("SELECT gpuhr FROM miners WHERE id=:id",[":id"=>$id]);
			$historic=$db->single("SELECT historic FROM miners WHERE id=:id",[":id"=>$id]);
			$shares=$db->single("SELECT shares FROM miners WHERE id=:id",[":id"=>$id]);
			$update=$db->single("SELECT updated FROM miners WHERE id=:id",[":id"=>$id]);

			if ($update == false) {
				$update = "No nonce submitted";
			}

			if ($shares == false) {
				$shares = "0";
			}

			if ($historic == false) {
				$historic = "0";
			}

			if ($hashrate == false) {
				$hashrate = "0";
			}

			if ($gpu_hr == false) {
				$gpu_hr = "0";
			}
		
			$yesterday=time()-86400;
			$yesterday_block=$aro->single("SELECT height+1 FROM blocks WHERE date<=$yesterday ORDER by height DESC LIMIT 1");
			$last_payment_txn=$db->single("SELECT txn FROM payments WHERE address=:id AND done=1 ORDER by height DESC LIMIT 1",[":id"=>$id]);
			$last_payment_time=$aro->single("SELECT date FROM transactions WHERE id=$last_payment_txn");
			$total_paid=$db->single("SELECT total_paid FROM miners WHERE id=:id",[":id"=>$id]);
			$pending=$db->single("SELECT pending FROM miners WHERE id=:id",[":id"=>$id]);
			$last_payment=$db->single("SELECT SUM(val) FROM payments WHERE txn=:lasttxn AND done=1",[":lasttxn"=>$last_payment_txn]);
			
			if ($last_payment_time == false) {
				$last_payment_time = "Payment in process";
			}

			if ($last_payment == 0 ) {
				$last_payment_time = "No payment yet";
			}

			$past_24h=$db->single("SELECT SUM(val) FROM payments WHERE address=:id AND height>=$yesterday_block AND done=1",[":id"=>$id]);

			echo json_encode(array("miner"=>$id, "cpu_hr"=>$hashrate, "gpu_hr"=>$gpu_hr, "historic shares"=>$historic, "current shares"=>$shares, "last nonce submited"=>$update, "total paid"=>$total_paid, "pending"=>$pending, "past_24h"=>$past_24h, "last_payment"=>$last_payment, "last_payment_date"=>$last_payment_time));
			
		}


}

