# Modifications

This repository is based on ario's fork of the official arionum pool code.
System requirements are indentical to the official pool code. 

Warning: because of additions to the config file, do not simply replace the php-files without making sure all variables are added to the config. 

## Bigger changes:
- Hashreporting has been removed from payments/poolsanity to a seperate updater. Updating hashrates on the website can be done independently of payment-cycle, and db is no longer only updated when moving to next block. Be aware that the 10minute update interval from the clients is fixed. As is the 'first appearance' in the db after submitting first nonce
- Api.php has been expanded and restructured with more options
- Last payment, payment date, payments in 24h, time of last submitted nonce included on individual miner page and api
- Bugfix: False reporting of stale blocks submitted through Dan's/Cryptogone's miner. 
- TODO: Orphaned blocks are no longer removed from the list but marked as orphaned. Pending payments are removed. 
- TODO: Workerid is changed to workername+address to create a unique key without having to alter the database. It prevents changes to client (new address/new workername) not being updated in the workerlist and it also prevents 2 miners using the same workername to be misreported as one. It will NOT change the fact that miners using one single name for many workers causing their hashrate being reported as once worker. This would require a uniqueID being send from the worker
- TODO: implement the possible external argon2 validation through config file
- TODO: either update miner hr more often or insert sum workerHR into dashboard/individual pages/api
- TODO: workaround for misreporting Dan's miner

## Smaller changes:
- The 'last-update' values of workers has been reduced from 1hr to 720s to keep the calculation of total hashrate more precise, this means workers not having reported in during the last 12 minutes are not added to the miner total hr and pool hr
- The 'time-out' value of miners has been enlarged to have miners remain in history when they are away for a while (max 24h). For individual workers it's set to 30minutes. A miner will be deleted from history if the historical share rate drops below 50 AND he has not submitted a nonce in the last 24h
- The 'cut-off' of last remaining historical shares has been changed to prevent ghost miners remaining in the list indefinely (hist.shares would never reach zero)
- Only 'active miners' are counted to calculate total HR: those with miner HR > 0
- Besides average CPU Blocks HR we now also calculate average GPU blocks HR. Both only based on active miners. 
- How long last payments are shown on payments page is moved to config
- How far back is searched for small pending payments is moved to config 
- If no payment message is set in config it takes the pool hostname from config
- Degradation, last-payments-variable, poolname etc. have been moved to config
- Bugfix to make poolsanity being able to be started as service
- Bugfix in hashrate extension determination

## Display changes:

- Small update to dashboard layout based on reoccuring questions in discord
- To remove the constant confusion we are going to be consequent and call it GPU-*blocks* HR and NOT "GPU HR" (and CPU-*blocks* HR instead of "CPU HR") everywhere on the site. 
- Bugfix in template/index.html of html code the purple box showing hashrates. Also: C-HR is shown left, G-HR right, just as in all tables. 
- Template/Header page: pool name set in config
- On dashboard: average h/s shown on page
- On payments page: Removal of long TX addresses in payout page, replaced with links to block explorer
- On blocks won page: Blocks are veryfiable through link to Block Explorer, for transparency
- On individual miner page: best DL is 1000000 replaced with No Nonce submitted yet
- On individual miner page: added time of last submitted nonce
- On individual miner page: when last payment is still in mempool it's shown as Payment In Process
- On individual miner page: when no payment ever last payment date is shown as No Payment Yet
- On info page: old mining info removed, pool details (rewards/degradation/payout/DL) are read from the config file
- On info page: discord handle can be set in config


With the current setup payments are split from updates.

Run payments to crontab every x hours, min payment can remain low, but it will prevent big miners from massive small payments.

Run update in seperate crontab every 1 minute. 

Optional: Run poolsanity as systemd-service

