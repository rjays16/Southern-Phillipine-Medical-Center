CREATE TABLE `care_pharma_main_migration` (
  `unit` varchar(12) NOT NULL,
  `item_name` varchar(50) NOT NULL,
  `item_code` varchar(50) NOT NULL,
  `barcode` varchar(50) NOT NULL,
  `bestellnum` varchar(12) NOT NULL DEFAULT '',
  `artikelnum` tinytext NOT NULL,
  `industrynum` tinytext NOT NULL,
  `artikelname` tinytext NOT NULL,
  `generic` tinytext NOT NULL,
  `description` text NOT NULL,
  `packing` tinytext NOT NULL,
  `prod_class` enum('M','S') NOT NULL DEFAULT 'M',
  `minorder` int(4) NOT NULL DEFAULT '0',
  `maxorder` int(4) NOT NULL DEFAULT '0',
  `proorder` tinytext NOT NULL,
  `picfile` tinytext NOT NULL,
  `encoder` tinytext NOT NULL,
  `enc_date` tinytext NOT NULL,
  `enc_time` tinytext NOT NULL,
  `lock_flag` tinyint(1) NOT NULL DEFAULT '0',
  `is_socialized` tinyint(1) NOT NULL DEFAULT '0',
  `is_restricted` tinyint(1) NOT NULL DEFAULT '0',
  `classification` int(11) DEFAULT NULL,
  `medgroup` text NOT NULL,
  `cave` tinytext NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT '',
  `price_cost` decimal(10,4) DEFAULT NULL,
  `price_cash` decimal(10,4) DEFAULT NULL,
  `price_charge` decimal(10,4) DEFAULT NULL,
  `history` text NOT NULL,
  `modify_id` varchar(35) NOT NULL DEFAULT '',
  `modify_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `create_id` varchar(35) NOT NULL DEFAULT '',
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ref_source` varchar(10) NOT NULL DEFAULT 'PH',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `category_id` int(4) NOT NULL,
  `remarks` text,
  `is_in_inventory` tinyint(1) DEFAULT '0',
  `is_fs` int(1) DEFAULT NULL,
  `is_restrictAntibio` int(1) DEFAULT NULL,
  `drug_code` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`bestellnum`),
  KEY `FK_care_pharma_products_main_testing2` (`classification`),
  KEY `prodclass_index` (`prod_class`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;



DELIMITER $$

DROP TRIGGER /*!50032 IF EXISTS */ `tg_care_pharma_main_migration_ai`$$

CREATE
    /*!50017 DEFINER = 'seniordev'@'%' */
    TRIGGER `tg_care_pharma_main_migration_ai` AFTER INSERT ON `care_pharma_main_migration`
    FOR EACH ROW BEGIN
	 IF EXISTS
	  (SELECT
	    *
	  FROM
	    care_pharma_products_main a
	  WHERE a.bestellnum = new.bestellnum)
	  THEN
	  UPDATE
	    care_pharma_products_main
	  SET
	    drug_code = new.drug_code
	  WHERE bestellnum = new.bestellnum;
  END IF ;
    END;
$$

DELIMITER ;