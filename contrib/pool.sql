SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `abusers` (
  `miner` varchar(128) NOT NULL,
  `nonce` varchar(128) NOT NULL
) ENGINE=MEMORY DEFAULT CHARSET=latin1;

CREATE TABLE `blocks` (
  `id` varbinary(256) NOT NULL,
  `height` int(11) NOT NULL,
  `miner` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `reward` decimal(20,8) NOT NULL DEFAULT 0.00000000
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `info` (
  `id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `val` varchar(128) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `miners` (
  `id` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `shares` bigint(20) NOT NULL DEFAULT 0,
  `historic` bigint(20) DEFAULT 0,
  `total_paid` decimal(20,8) DEFAULT 0.00000000,
  `updated` int(11) NOT NULL,
  `bestdl` int(11) DEFAULT 1000000,
  `pending` decimal(20,8) DEFAULT 0.00000000,
  `hashrate` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `nonces` (
  `nonce` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT ''
) ENGINE=MEMORY DEFAULT CHARSET=latin1;

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `address` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `block` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `val` decimal(20,8) NOT NULL,
  `done` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `txn` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `rejects` (
  `ip` varchar(45) NOT NULL,
  `data` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `workers` (
  `id` varchar(32) NOT NULL,
  `hashrate` int(11) NOT NULL,
  `updated` int(11) NOT NULL,
  `miner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `ip` varchar(45) DEFAULT NULL
) ENGINE=MEMORY DEFAULT CHARSET=latin1;


ALTER TABLE `blocks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `height` (`height`);

ALTER TABLE `info`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `miners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shares` (`shares`),
  ADD KEY `historic` (`historic`);

ALTER TABLE `nonces`
  ADD PRIMARY KEY (`nonce`);

ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `block` (`block`),
  ADD KEY `done` (`done`),
  ADD KEY `address` (`address`),
  ADD KEY `height` (`height`),
  ADD KEY `val` (`val`);

ALTER TABLE `rejects`
  ADD PRIMARY KEY (`ip`),
  ADD KEY `data` (`data`);

ALTER TABLE `workers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `updated` (`updated`),
  ADD KEY `miner` (`miner`),
  ADD KEY `ip` (`ip`);


ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


INSERT INTO `info` (`id`, `val`) VALUES
('total_hash_rate', '0'),
('total_paid', '0');

ALTER TABLE `miners` ADD `gpuhr` INT NULL DEFAULT '0' AFTER `hashrate`; 
ALTER TABLE `workers` ADD `gpuhr` INT NULL DEFAULT '0' AFTER `hashrate`; 
INSERT INTO `info` (`id`, `val`) VALUES ('total_gpu_hr', '');


COMMIT;
