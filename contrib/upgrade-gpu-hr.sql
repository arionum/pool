ALTER TABLE `miners` ADD `gpuhr` INT NULL DEFAULT '0' AFTER `hashrate`; 
ALTER TABLE `workers` ADD `gpuhr` INT NULL DEFAULT '0' AFTER `hashrate`; 
INSERT INTO `info` (`id`, `val`) VALUES ('total_gpu_hr', '');
