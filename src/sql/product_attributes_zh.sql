SET FOREIGN_KEY_CHECKS=0;

--
-- Dumping data for table `xc_attribute_option_translations`
--

INSERT INTO `%%XC%%_attribute_option_translations` (`label_id`, `id`, `name`, `code`) VALUES
(1, 1, 'S', 'en'),
(2, 2, 'M', 'en'),
(3, 3, 'L', 'en'),
(4, 4, 'XL', 'en'),
(5, 5, 'XXL', 'en'),
(8, 8, 'XXXL', 'en'),
(3165, 165, '16', 'en'),
(3166, 166, '64', 'en'),
(3167, 167, '128', 'en'),
(3168, 168, 'Silver', 'en'),
(3169, 169, 'Space Gray', 'en'),
(3170, 170, 'Gold', 'en'),
(3171, 171, 'A8 chip with 64‑bit architecture', 'en'),
(3172, 172, 'Nano-SIM', 'en'),
(3173, 173, 'Retina HD display', 'en'),
(3174, 174, '4.7', 'en'),
(3176, 176, 'Built-in rechargeable lithium-ion battery', 'en'),
(3177, 177, 'Up to 14 hours on 3G', 'en'),
(3178, 178, 'Up to 10 days (250 hours)', 'en'),
(3179, 179, 'Up to 11 hours', 'en'),
(3180, 180, 'Up to 50 hours', 'en'),
(3181, 181, 'GSM/EDGE/LTE', 'en'),
(3182, 182, '802.11a/b/g/n/ac', 'en'),
(3183, 183, 'Bluetooth 4.2', 'en'),
(3184, 184, '5.5', 'en'),
(3186, 186, '32', 'en'),
(3187, 187, 'A7 chip with 64-bit architecture', 'en'),
(3188, 188, 'White', 'en'),
(3189, 189, 'Black', 'en'),
(3190, 190, '4', 'en'),
(3192, 192, 'Up to 10 hours on 3G', 'en'),
(3193, 193, 'Up to 250 hours', 'en'),
(3194, 194, 'Up to 10 hours', 'en'),
(3195, 195, 'Up to 40 hours', 'en'),
(3196, 196, 'GSM/EDGE', 'en'),
(3197, 197, '802.11a/b/g/n', 'en'),
(3198, 198, 'Up to 24 hours on 3G', 'en'),
(3199, 199, 'Up to 16 days (384 hours)', 'en'),
(3200, 200, 'A9 chip with 64‑bit architecture', 'en'),
(3209, 205, 'Rose Gold', 'en');

--
-- Dumping data for table `xc_attribute_options`
--

INSERT INTO `%%XC%%_attribute_options` (`id`, `attribute_id`, `addToNew`, `position`) VALUES
(1, 1, 1, 0),
(2, 1, 1, 0),
(3, 1, 1, 0),
(4, 1, 1, 0),
(5, 1, 1, 0),
(8, 1, 1, 0),
(165, 71, 1, 0),
(166, 71, 1, 0),
(167, 71, 0, 0),
(168, 73, 0, 0),
(169, 73, 0, 0),
(170, 73, 0, 0),
(171, 72, 0, 0),
(172, 74, 0, 0),
(173, 75, 0, 0),
(174, 76, 0, 0),
(176, 78, 0, 0),
(177, 79, 0, 0),
(178, 80, 0, 0),
(179, 81, 0, 0),
(180, 82, 0, 0),
(181, 83, 0, 0),
(182, 84, 0, 0),
(183, 85, 0, 0),
(184, 76, 0, 0),
(186, 71, 0, 0),
(187, 72, 0, 0),
(188, 73, 0, 0),
(189, 73, 0, 0),
(190, 76, 0, 0),
(192, 79, 0, 0),
(193, 80, 0, 0),
(194, 81, 0, 0),
(195, 82, 0, 0),
(196, 83, 0, 0),
(197, 84, 0, 0),
(198, 79, 0, 0),
(199, 80, 0, 0),
(200, 72, 0, 0),
(205, 73, 0, 0),
(207, 74, 0, 0),
(209, 76, 0, 0),
(211, 78, 0, 0),
(212, 79, 0, 0),
(213, 80, 0, 0),
(214, 81, 0, 0),
(215, 82, 0, 0),
(217, 84, 0, 0);

--
-- Dumping data for table `xc_attribute_translations`
--

INSERT INTO `%%XC%%_attribute_translations` (`label_id`, `id`, `name`, `unit`, `code`) VALUES
(1, 1, 'Size', '', 'en'),
(141, 71, 'Capacity, GB', '', 'en'),
(142, 72, 'Chip', '', 'en'),
(143, 73, 'Color', '', 'en'),
(144, 74, 'Sim card', '', 'en'),
(145, 75, 'Display type', '', 'en'),
(146, 76, 'Dimension, inches', '', 'en'),
(148, 78, 'Battery type', '', 'en'),
(149, 79, 'Talk time', '', 'en'),
(150, 80, 'Standby time', '', 'en'),
(151, 81, 'Video playback', '', 'en'),
(152, 82, 'Audio playback', '', 'en'),
(153, 83, 'GSM model', '', 'en'),
(154, 84, 'Wi-Fi', '', 'en'),
(155, 85, 'Bluetooth', '', 'en'),
(156, 86, 'GPS', '', 'en'),
(175, 71, '内存', '', 'zh'),
(176, 72, '芯片', '', 'zh'),
(177, 73, '颜色', '', 'zh'),
(178, 74, 'Sim卡', '', 'zh'),
(179, 75, '显示器类型', '', 'zh'),
(180, 76, '尺寸', '', 'zh'),
(181, 78, '电池类型', '', 'zh'),
(182, 79, '通话时间', '', 'zh'),
(183, 80, '待机时间', '', 'zh'),
(184, 81, '录像回放', '', 'zh'),
(185, 82, '录音回放', '', 'zh'),
(186, 83, 'GSM模式', '', 'zh'),
(187, 84, 'Wi-Fi', '', 'zh'),
(188, 85, '蓝牙', '', 'zh'),
(189, 86, 'GPS', '', 'zh');

--
-- Dumping data for table `xc_attributes`
--

INSERT INTO `%%XC%%_attributes` (`id`, `product_class_id`, `attribute_group_id`, `product_id`, `visible`, `position`, `decimals`, `type`, `addToNew`) VALUES
(1, 1, NULL, NULL, 1, 0, 0, 'S', ''),
(71, 2, NULL, NULL, 1, 0, 0, 'S', ''),
(72, 2, NULL, NULL, 1, 0, 0, 'S', ''),
(73, 2, NULL, NULL, 1, 0, 0, 'S', ''),
(74, 2, NULL, NULL, 0, 0, 0, 'S', ''),
(75, 2, 1, NULL, 1, 0, 0, 'S', ''),
(76, 2, 1, NULL, 1, 0, 0, 'S', ''),
(78, 2, 3, NULL, 0, 0, 0, 'S', ''),
(79, 2, 3, NULL, 0, 0, 0, 'S', ''),
(80, 2, 3, NULL, 0, 0, 0, 'S', ''),
(81, 2, 3, NULL, 0, 0, 0, 'S', ''),
(82, 2, 3, NULL, 0, 0, 0, 'S', ''),
(83, 2, 2, NULL, 1, 0, 0, 'S', ''),
(84, 2, 2, NULL, 1, 0, 0, 'S', ''),
(85, 2, 2, NULL, 0, 0, 0, 'S', ''),
(86, 2, 2, NULL, 1, 0, 0, 'C', '');

--
-- Dumping data for table `xc_product_classes`
--

INSERT INTO `%%XC%%_product_classes` (`id`, `position`) VALUES
(1, 0),
(2, 0);

--
-- Dumping data for table `xc_product_class_translations`
--

INSERT INTO `%%XC%%_product_class_translations` (`label_id`, `id`, `name`, `code`) VALUES
(1, 1, 'Apparel', 'en'),
(5, 2, 'Mobile phone', 'en'),
(7, 1, '服饰', 'zh'),
(8, 2, '手机', 'zh');


--
-- Dumping data for table `xc_attribute_groups`
--

INSERT INTO `%%XC%%_attribute_groups` (`id`, `product_class_id`, `position`) VALUES
(1, 2, 10),
(2, 2, 20),
(3, 2, 30);

--
-- Dumping data for table `xc_attribute_group_translations`
--

INSERT INTO `%%XC%%_attribute_group_translations` (`label_id`, `id`, `name`, `code`) VALUES
(1, 1, 'Display', 'en'),
(2, 2, 'Cellular and Wireless', 'en'),
(3, 3, 'Power and Battery', 'en'),
(7, 1, '显示', 'zh'),
(8, 2, '无线和数据', 'zh'),
(9, 3, '电力电量', 'zh');


UPDATE `%%XC%%_products` SET lowLimitEnabled = 1;
UPDATE `%%XC%%_products` SET lowLimitAmount = 1 WHERE lowLimitAmount = 0;

--
-- Arrival date
--

UPDATE `%%XC%%_products` SET arrivalDate = UNIX_TIMESTAMP() - (86400 * 150);
UPDATE `%%XC%%_products` SET arrivalDate = UNIX_TIMESTAMP() - (86400 * 10) WHERE product_id IN (3);
UPDATE `%%XC%%_products` SET arrivalDate = UNIX_TIMESTAMP() + (86400 * 30) WHERE product_id IN (7);