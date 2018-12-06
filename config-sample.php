<?php

/*
|--------------------------------------------------------------------------
| General Configuration
|--------------------------------------------------------------------------
*/

// The full path to a local node install
$pool_config['node_path'] = '/var/www/node';

// The access URL for node
$pool_config['node_url'] = 'http://127.0.0.1:30000';

// The maximum deadline that is allowed for pool miners
$pool_config['max_deadline'] = 1000000;

/*
|--------------------------------------------------------------------------
| Database Configuration
|--------------------------------------------------------------------------
*/

// The database DSN
$pool_config['db_connect'] = 'mysql:host=localhost;dbname=DB-NAME';

// The database username
$pool_config['db_user'] = 'DB-USER';

// The database password
$pool_config['db_pass'] = 'DBPASS';

/*
|--------------------------------------------------------------------------
| Mining Configuration
|--------------------------------------------------------------------------
*/

// The pool public key
$pool_config['public_key'] = 'your public key';

// The pool private key
$pool_config['private_key'] = 'your private key';

// The pool wallet address
$pool_config['address'] = 'pool wallet address';

// The pool fee wallet address
$pool_config['fee_address'] = 'fee wallet address';

// The fee that the pool takes from the funds (default is 2%)
$pool_config['fee'] = 0.02;

/*
|--------------------------------------------------------------------------
| Payments Configuration
|--------------------------------------------------------------------------
*/

// The percentage to reward to historic shares (default is 100%)
$pool_config['historic_reward'] = 1;

// The percentage to reward to current shares (default is 0%)
$pool_config['current_reward'] = 0;

// The percentage to reward to the block winner (default is 0%)
$pool_config['miner_reward'] = 0;

// The minimum payout that is required
$pool_config['min_payout'] = 3;
