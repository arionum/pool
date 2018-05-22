<?php
error_reporting(0);
ini_set("display_errors", "off");
require __DIR__.'/vendor/autoload.php';

require_once("config.php");
require_once($pool_config['node_path']."/include/functions.inc.php");
require_once($pool_config['node_path']."/include/db.inc.php");
require_once($pool_config['node_path']."/include/account.inc.php");
require_once($pool_config['node_path']."/include/config.inc.php");

$db = new DB($pool_config['db_connect'], $pool_config['db_user'], $pool_config['db_pass'], 0);
if (!$db) {
    die("Could not connect to the DB backend.");
}

$aro = new DB($_config['db_connect'], $_config['db_user'], $_config['db_pass'], 0);
if (!$aro) {
    die("Could not connect to the ARODB backend.");
}
