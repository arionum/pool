# pool
A proof of concept pool for Arionum

Live version: http://aropool.com

The pool requires a full node running on the same server (on a different subdomain) as it uses it's libraries and db connection.

Edit the config.php and follow the instructions inside.

Create a cron entry on the format */10 * * * * user php /path/to/pool/payments.php

Start the poolsanity by running: php /path/to/pool/poolsanity.php &>/dev/null &

The requirements are the same as for the arionum node: php 7.2 (with argon2), php-openssl, php-gmp, php-bcmath

For the template system, we use raintpl3

Please use a new name instead of aropool to avoid user confusion!
