<?php
include("db.php");

$total_hr=$db->single("SELECT val FROM info WHERE id='total_hash_rate'");

$total_gpu=$db->single("SELECT val FROM info WHERE id='total_gpu_hr'");

$current=$aro->single("SELECT height FROM blocks ORDER by height DESC LIMIT 1");

$miners=$db->single("SELECT COUNT(1) FROM miners");
$last_won=$db->single("SELECT height FROM blocks ORDER by height DESC LIMIT 1");
$last_won_time=$aro->single("SELECT date FROM blocks WHERE height=:h",[":h"=>$last_won]);

echo json_encode(array("cpu_hr"=>$total_hr, "gpu_hr"=>$total_gpu, "current_block_height"=>$current, "last_won_block"=>$last_won, "last_won_block_time"=>$last_won_time, "miners"=>$miners, "fee"=>$pool_config['fee']));
