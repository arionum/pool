<?php

require __DIR__.'/db.php';

$totalCpuHashrate = $db->single("SELECT val FROM info WHERE id = 'total_hash_rate'");

$totalGpuHashrate = $db->single("SELECT val FROM info WHERE id = 'total_gpu_hr'");

$currentBlockHeight = $aro->single('SELECT height FROM blocks ORDER by height DESC LIMIT 1');

$miners = $db->single('SELECT COUNT(1) FROM miners');

$lastWon = $db->single('SELECT height FROM blocks ORDER by height DESC LIMIT 1');
$lastWonTime = $aro->single('SELECT date FROM blocks WHERE height = :h', [':h' => $lastWon]);

echo json_encode([
    'cpu_hr' => $totalCpuHashrate,
    'gpu_hr' => $totalGpuHashrate,
    'current_block_height' => $currentBlockHeight,
    'last_won_block' => $lastWon,
    'last_won_block_time' => $lastWonTime,
    'miners' => $miners,
    'fee' => $pool_config['fee'],
]);
