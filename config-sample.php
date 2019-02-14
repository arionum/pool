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

// The maximum deadline that is allowed for pool miners // cpu block type
$pool_config['max_deadline'] = 1000000;

// The maximum deadline that is allowed for pool miners // gpu block type
$pool_config['max_deadline_gpu'] = 1000000;

// Degradation rate of historical shares
$pool_config['pool_degradation'] = 0.02;

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

// The message included in payments
// If no message is set the poolname will be used
$pool_config['payout_message'] = '';

/*
|--------------------------------------------------------------------------
| Addiontal Pool Information / Settings
|--------------------------------------------------------------------------
*/

$pool_config['pool_url'] = 'http://pooladdress.here';
$pool_config['pool_name'] = 'Your Poolname here';
$pool_config['handle'] = 'your Discord name here'; 

// percentage of historical share degradation per block. 0.05 is best for pool with big miners, 0.02 for pool with small miners
$pool_config['pool_degradation'] = 0.05; 

// how many blocks to look back for pending payments. large pool: set at 500, small pool >2000
// do not forget to occasionally run a manual payment to clear old payments
$pool_config['blocks_paid'] = 5000;

// how long are old payments shown on the website. Delete after x blocks, default 1000. Small pool set much higher
$pool_config['payout_history'] = 1000;

$pool_config['payout_shedule'] = '1 hour';

/*
|--------------------------------------------------------------------------
| Design Settings
|--------------------------------------------------------------------------
*/


$pool_config['template_directory'] = 'template/';


