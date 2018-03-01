<?php
$pool_config['db_connect']="mysql:host=localhost;dbname=DB-NAME";
$pool_config['db_user']="DB-USER";
$pool_config['db_pass']="DBPASS";

$pool_config['max_deadline']=1000000; 
$pool_config['node_path']="/var/www/node";  // path to where the node is installed
$pool_config['node_url']="http://127.0.0.1:30000"; // Node's access url
$pool_config['public_key']="your public key";
$pool_config['private_key']="your private key";
$pool_config['address']="pool wallet address";
$pool_config['fee_address']="fee wallet address";
$pool_config['fee']=0.02;   // 0.02 = 2%  |   pool fee
$pool_config['historic_reward']=1; // percentage going to historic shares 1=100%, 0.3=30%, 0=none
$pool_config['current_reward']=0; // percentage going to current shares1=100%, 0.3=30%, 0=none
$pool_config['miner_reward']=0; // percentage going to block winner 1=100%, 0.3=30%, 0=none
$pool_config['min_payout']=3;
?>
