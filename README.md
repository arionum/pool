# Modifications

This repository is based on angelexevior's fork.
System requirements are indentical to the official pool code. 

Warning: because of additions to the config file, do not simply replace the php-files without making sure all variables are added to the config. 

## Mining
- Share count based on accepted share and pool min DL and networm maximum min DL rate
- Multiple pool can work together, (eg. different min DL for Cpu, GPU or other mining settings, but same reward policy)

## Utils
- Update.php: calculate orphan blocks, and overwriter (stealer)'s alias or address

## Display
- Show Orphans % (basen on last 100 block)
- Show Orphan block overwriter ("stealer")
- Expanded informations on main page (need to run /utils/getinfo.sh)
  -> passed blocks from last found
  -> luck % (based on passed blocks and actual hashrate, need to finetune)
  -> Arionum actual price (from arionum.info)
  -> network hashrates (from arionum.info)
- Added some icons, and header
- Added links page (webpages, miners, wallets, pools...)
- Added Cuby's web wallet link

### angelexevior's changes ###
 
## Bigger changes:
- Change: Hashreporting has been removed from payments/poolsanity to a seperate updater. Updating hashrates on the website can be done independently of payment-cycle, and db is no longer only updated when moving to next block. Be aware that the 10minute update interval from the clients is fixed. As is the 'first appearance' in the db after submitting first nonce
- Added: Api.php has been expanded and restructured with more options
- Added: Last payment, payment date, payments in 24h, time of last submitted nonce included on individual miner page and api
- Bugfix: False rejects stale blocks submitted through Dan's Javaminer after PHP upgrade. 
- Bugfix: Workaround for misreporting Dan's Java miner/Android miner
- TODO: Orphaned blocks are no longer removed from the list but marked as orphaned. Pending payments are removed. 
- TODO: Workerid is changed to workername+address to create a unique key without having to alter the database. It prevents changes to client (new address/new workername) not being updated in the workerlist and it also prevents 2 miners using the same workername to be misreported as one. It will NOT change the fact that miners using one single name for many workers causing their hashrate being reported as once worker. This would require a uniqueID being send from the worker
- TODO: implement the possible external argon2 validation through config file
- TODO: either update miner hr more often or insert sum workerHR into dashboard/individual pages/api

## Smaller changes:
- Change: The 'last-update' values of workers has been reduced from 1hr to 720s to keep the calculation of total hashrate more precise, this means workers not having reported in during the last 12 minutes are not added to the miner total hr and pool hr.
- Change: The 'time-out' value of miners has been enlarged to have miners remain in history when they are away for a while (max 24h). For individual workers it's set to 30minutes. 
- Change: Discard of last remaining historical shares has been added to prevent ghost miners remaining in the list indefinely (hist.shares would never reach zero)
- Change: A miner will be deleted from history if the historical share rate drops below 50 AND he has not submitted a nonce in the last 24h
- Change: Only 'active miners' are counted to calculate total HR: those with miner HR > 0
- Added/Change: Next average CPU Blocks HR we now also calculate average GPU blocks HR. Both are now only based on active miners. 
- Change: How far back last payments are displayed on payments page is moved to config
- Change: During payment cycle, how far back is searched for small pending payments is moved to config.
- Change: Manual payment request of all outstanding payments now looks through entire pool history. 
- Change: If no payment message is set in config it takes the pool hostname from config
- Change: Degradation, poolname etc. have been moved to config
- Bugfix: to make poolsanity being able to be started as service through systemd
- Bugfix: fixed erros in hashrate extension determination
- TODO: Bugfix of Ario's total hr calc in indiv.miner page.

## Display changes:

- Change: Small updates to dashboard layout based on reoccuring questions in discord
- Change: On payments page: Removal of long TX addresses in payout page, replaced with links to block explorer
- Change: To remove the constant confusion we are going to be consequent and call it GPU-*blocks* HR and NOT "GPU HR" (and CPU-*blocks* HR instead of "CPU HR") everywhere on the site. 
- Bugfix: template/index.html responsive html code of the purple box showing hashrates caused errors on android. Fixed. Also: C-HR is now displayed left, G-HR right, just as in all other tables. 
- Bugfix: Rounding error hashrates
- Change: Template/Header page: pool name set in config
- Added: On dashboard: average h/s displayed on page
- Added: On blocks won page - Blocks are veryfiable through link to Block Explorer, for transparency
- Change: On individual miner page - best DL is 1000000 replaced with No Nonce submitted yet
- Change: On individual miner page - added time of last submitted nonce
- Bugfix: On individual miner page - when last payment is still in mempool it's shown as Payment In Process
- Bugfix: On individual miner page - when no payment ever last payment date is shown as No Payment Yet
- Change: On info page - old mining info removed, pool details (rewards/degradation/payout/DL) are read from the config file
- Change: On info page - discord handle can be set in config


With the current setup payments are split from updates.

Run payments to crontab every x hours, min payment can remain low, but it will prevent big miners from massive small payments.

Run update in seperate crontab every 1 minute. 

Optional: Run poolsanity as systemd-service

