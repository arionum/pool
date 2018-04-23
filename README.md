# pool

A proof of concept pool for Arionum

Live version: http://aropool.com

The pool requires a full node running on the same server (on a different subdomain) as it uses it's libraries and db connection.

## Install

The requirements are the same as for the arionum node:

- php 7.2 (with argon2)
- php-openssl
- php-gmp
- php-bcmath

## Usage

1. Create a new database for the pool (separate from the node one).
2. Edit the `config.php` and follow the instructions inside.
3. Import the `contrib/pool.sql` to your NEW mysql database.
4. Chmod the cache directory using `chmod 777 cache -R`.
5. Create a cron entry using the following format:
   ```bash
   */10 * * * * user php /path/to/pool/payments.php
   ```

Start the poolsanity by running:

```bash
php /path/to/pool/poolsanity.php &>/dev/null &
```

## Notes

For the template system, we use [raintpl3].

Please use a new name instead of [aropool] to avoid user confusion!

This project is early alpha, bugs may be found, functions might not work properly etc.

[aropool]: https://aropool.com
[raintpl3]: https://github.com/feulf/raintpl3
