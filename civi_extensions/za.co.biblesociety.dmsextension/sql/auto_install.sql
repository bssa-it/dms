/*Table structure for table `civicrm_dms_bank_accounts` */

DROP TABLE IF EXISTS `civicrm_dms_bank_accounts`;

CREATE TABLE `civicrm_dms_bank_accounts` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Every table has an auto_incremented id',
  `bank_id` INT(11) NOT NULL COMMENT 'Bank Id from the civicrm_dms_banks table',
  `name` VARCHAR(150) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Name of Bank',
  `branch_code` VARCHAR(10) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Branch Code',
  `account_no` VARCHAR(50) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Bank account number',
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `civicrm_dms_bank_accounts` */

LOCK TABLES `civicrm_dms_bank_accounts` WRITE;

UNLOCK TABLES;

/*Table structure for table `civicrm_dms_banks` */

DROP TABLE IF EXISTS `civicrm_dms_banks`;

CREATE TABLE `civicrm_dms_banks` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Every table has an auto_incremented id',
  `name` VARCHAR(150) CHARACTER SET utf8 DEFAULT NULL COMMENT 'bank name',
  `type` VARCHAR(50) CHARACTER SET utf8 DEFAULT NULL COMMENT 'type',
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `civicrm_dms_banks` */

LOCK TABLES `civicrm_dms_banks` WRITE;

UNLOCK TABLES;

/*Table structure for table `civicrm_dms_batch_entry` */

DROP TABLE IF EXISTS `civicrm_dms_batch_entry`;

CREATE TABLE `civicrm_dms_batch_entry` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Every table has an auto_incremented id',
  `batch_id` INT(11) NOT NULL COMMENT 'id in the civicrm_dms_batch_header table',
  `received_datetime` DATETIME DEFAULT NULL COMMENT 'date and time the batch entry was created',
  `received_by` INT(11) DEFAULT NULL COMMENT 'User Id that created the batch entry',
  `receipt_no` INT(11) NOT NULL COMMENT 'Receipt no for the entry',
  `receipt_amount` DECIMAL(24,4) DEFAULT NULL COMMENT 'Amount Received',
  `receipt_type` VARCHAR(50) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Receipt/Document type',
  PRIMARY KEY (`id`),
  KEY `batch_entry_receipt_no` (`receipt_no`,`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `civicrm_dms_batch_entry` */

LOCK TABLES `civicrm_dms_batch_entry` WRITE;

UNLOCK TABLES;

/*Table structure for table `civicrm_dms_batch_header` */

DROP TABLE IF EXISTS `civicrm_dms_batch_header`;

CREATE TABLE `civicrm_dms_batch_header` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Every table has an auto_incremented id',
  `statement_id` INT(11) DEFAULT NULL COMMENT 'transaction in the statement table to which this batch is linked',
  `statement_date` DATE DEFAULT NULL COMMENT 'Date on statement',
  `statement_reference` VARCHAR(150) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Reference on statement',
  `statement_value` DECIMAL(24,4) DEFAULT NULL COMMENT 'Value on statement',
  `created_datetime` DATETIME DEFAULT NULL COMMENT 'date and time the batch created',
  `created_by` INT(11) DEFAULT NULL COMMENT 'User Id that created the batch',
  `batch_title` VARCHAR(150) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Title for the batch',
  `batch_type` VARCHAR(50) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Type of batch',
  `batch_status` VARCHAR(50) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Status of Batch',
  `batch_total` DECIMAL(24,4) DEFAULT NULL COMMENT 'Total value for all entries in batch',
  `office_id` INT(11) DEFAULT NULL COMMENT 'Office where batch was processed',
  `bank_account_id` INT(11) DEFAULT NULL COMMENT 'Bank Account to which the deposit was made',
  `deposit_date` DATETIME DEFAULT NULL COMMENT 'Date cash and cheques were taken to the bank',
  `completed_date` DATETIME DEFAULT NULL COMMENT 'Date and time the batch was completed',
  `completed_by` INT(11) DEFAULT NULL COMMENT 'User Id that completed the batch',
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `civicrm_dms_batch_header` */

LOCK TABLES `civicrm_dms_batch_header` WRITE;

UNLOCK TABLES;

/*Table structure for table `civicrm_dms_budget` */

DROP TABLE IF EXISTS `civicrm_dms_budget`;

CREATE TABLE `civicrm_dms_budget` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Every table has an auto_incremented id',
  `bud_region` INT(2) DEFAULT NULL COMMENT 'region that the budget belongs to',
  `bud_department` VARCHAR(2) DEFAULT NULL COMMENT 'Department the budget belongs to',
  `bud_category` INT(4) DEFAULT NULL COMMENT 'Category the budget belongs to',
  `bud_amount` DECIMAL(20,4) DEFAULT NULL COMMENT 'Budget Amount',
  `bud_insert_user` VARCHAR(200) DEFAULT NULL COMMENT 'User who inserted the budget',
  `bud_dateinserted` DATETIME DEFAULT NULL COMMENT 'date and time the budget was inserted',
  `bud_datelastupdated` DATETIME DEFAULT NULL COMMENT 'date and time the budget was last updated',
  `bud_update_user` VARCHAR(200) DEFAULT NULL COMMENT 'User who last updated the budget',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNQ1` (`bud_region`,`bud_department`,`bud_category`)
) ENGINE=INNODB AUTO_INCREMENT=472 DEFAULT CHARSET=latin1;

/*Data for the table `civicrm_dms_budget` */

LOCK TABLES `civicrm_dms_budget` WRITE;

UNLOCK TABLES;

/*Table structure for table `civicrm_dms_department` */

DROP TABLE IF EXISTS `civicrm_dms_department`;

CREATE TABLE `civicrm_dms_department` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Every table has an auto_incremented id',
  `dep_id` VARCHAR(2) CHARACTER SET utf8 NOT NULL COMMENT 'Department Id code',
  `dep_name` VARCHAR(250) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Department name',
  `dep_office_id` INT(11) DEFAULT NULL COMMENT 'office id where contact resides',
  `dep_is_national` VARCHAR(1) CHARACTER SET utf8 DEFAULT 'N' COMMENT 'Is this a national department (for reporting)',
  `dep_budget_allocation` DECIMAL(24,4) DEFAULT NULL COMMENT 'Allocated Budget',
  `dep_chart_color` VARCHAR(10) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Chart Color',
  `dep_fromEmailName` VARCHAR(250) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'From Email address name',
  `dep_fromEmailAddress` VARCHAR(250) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'From Email Address',
  `dep_contact_id` INT(11) DEFAULT NULL COMMENT 'contact id to whom this department belongs',
  PRIMARY KEY (`id`),
  KEY `dep_id` (`dep_id`)
) ENGINE=INNODB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `civicrm_dms_department` */

LOCK TABLES `civicrm_dms_department` WRITE;

INSERT  INTO `civicrm_dms_department`(`id`,`dep_id`,`dep_name`,`dep_office_id`,`dep_is_national`,`dep_budget_allocation`,`dep_chart_color`,`dep_fromEmailName`,`dep_fromEmailAddress`,`dep_contact_id`) VALUES (36,'1','Reg Sec W Cape',2,'N',5006989.0000,'#c0362c',NULL,NULL,NULL),(37,'2','Reg Sec KZN-S',3,'N',1841111.0000,'#2cabe2',NULL,NULL,NULL),(38,'3','Reg Sec KAS',4,'N',3245496.0000,'#e28bc5',NULL,NULL,NULL),(39,'4','Reg Sec BFN',5,'N',1439023.0000,'#90f030',NULL,NULL,NULL),(40,'5','Reg Sec E CAPE',6,'N',494038.0000,'#bf8fec',NULL,NULL,NULL),(41,'6','Reg Sec MTHATHA',6,'N',0.0000,'#01a05f',NULL,NULL,NULL),(42,'7','Reg Sec KZN-N',3,'N',42186.0000,'#0c3707',NULL,NULL,NULL),(43,'8','Not Used',1,'N',0.0000,'#a7993f',NULL,NULL,NULL),(44,'9','Head Office - BAM',1,'Y',4012166.0000,'#76275f','Biblia','biblia@biblesociety.co.za',1),(45,'A','Dr QE Heine',2,'N',4470231.0000,'#ff0000','Quintus Heine','heine@biblesociety.co.za',303406),(46,'B','Dr R Jonas',2,'N',885071.0000,'#999999','Ruth Jonas','jonas@biblesociety.co.za',157093),(47,'C','Pastor T Louw',2,'Y',892366.0000,'#db4c4c','Theuns Louw','louw@biblesociety.co.za',157094),(48,'D','Ds E Lesch',2,'N',305526.0000,'#ff6600','Eddie Lesch','e.lesch@biblesociety.co.za',157089),(49,'E','Rev C van Rooyen',3,'Y',528447.0000,'#000066','Clive van Rooyen','vanrooyen@biblesociety.co.za',157102),(50,'F','Pastor MP Roodt',4,'Y',1969065.0000,'#ffb27f','Mike Roodt','roodt@biblesociety.co.za',157098),(51,'G','Dr F Sieberhagen',4,'Y',3633486.0000,'#56a83c	','Francois Sieberhagen','sieberhagen@biblesociety.co.za',157099),(52,'H','Dr K Papp & Dr S Hoffman',4,'Y',2902312.0000,'#99cccc','Dr Kalmann Papp','papp@biblesociety.co.za',49830),(53,'I','Joos Test',1,'N',0.0000,'#333333','Joos Maree','joosm@biblesociety.co.za',157046),(54,'J','Mr HL Dekker',4,'Y',1876188.0000,'#654321','Hennie Dekker','dekker@biblesociety.co.za',157088),(55,'K','Pastor G Thompson & Pastor B Francke',4,'Y',5268757.0000,'#9999cc','George Thompson','thompson@biblesociety.co.za',157100),(56,'L','Rev H Barnard',4,'Y',1051252.0000,'#4e69a2','Clive van Rooyen (Rev)','vanrooyen@biblesociety.co.za',157102),(57,'M','Rev F Mokoena',4,'Y',438089.0000,'#224318','Feli Mokoena','mokoena@biblesociety.co.za',157096),(58,'N','Ds J Peyper',4,'Y',2673119.0000,'#e06666','Johan Peyper','peyper@biblesociety.co.za',157097),(59,'O','Not Used',1,'N',0.0000,'#323284',NULL,NULL,NULL),(60,'P','DoNotUse Ex J de Wet',1,'N',0.0000,'#000000',NULL,NULL,NULL),(61,'Q','Not Used',1,'N',0.0000,'#6c6c6c','Chezre Fredericks','fredericks@biblesociety.co.za',155626),(62,'R','Ds G van Dyk',5,'Y',3030834.0000,'#470000','Ds Gerrie van Dyk','vandyk@biblesociety.co.za',157101),(63,'S','Ds BP Fourie',6,'N',1490183.0000,'#4c934c','Ben Fourie','fourie@biblesociety.co.za',157104),(64,'T','Development Dept P.E.',6,'N',377885.0000,'#d11919','Matie van Niekerk','matie@biblesociety.co.za',157104),(65,'U','Rev M Saliwa',6,'N',104718.0000,'#918010','Mveleli Saliwa','saliwa@biblesociety.co.za',157129),(66,'V','Not Used',1,'N',0.0000,'#005b00',NULL,NULL,NULL),(67,'W','Dr AJ Boshoff',3,'Y',525350.0000,'#5b5b7a','Andries Boshoff','boshoff@biblesociety.co.za',157087),(68,'X','DBN Sundry Churches',3,'Y',0.0000,'#cca3a3','Brian Gopaul','gopaul@biblesociety.co.za',157030),(69,'Y','Rev S Fraser',4,'Y',453411.0000,'#4c667f','Rev Fr Shane Fraser','fraser@biblesociety.co.za',157090),(70,'Z','Rev J Mazibuko',3,'N',0.0000,'#adadad','Josiah Mazibuko','mazibuko@biblesociety.co.za',157095),(99,'0','Unknown',1,'N',0.0000,'#254B7C','Biblia','biblia@biblesociety.co.za',1);

UNLOCK TABLES;

/*Table structure for table `civicrm_dms_motivation` */

DROP TABLE IF EXISTS `civicrm_dms_motivation`;

CREATE TABLE `civicrm_dms_motivation` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Every table has an auto_incremented id',
  `motivation_id` INT(11) NOT NULL COMMENT 'Motivation Code Id',
  `description` VARCHAR(150) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Motivation Code description',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mot_id` (`motivation_id`)
) ENGINE=INNODB AUTO_INCREMENT=130 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `civicrm_dms_motivation` */

LOCK TABLES `civicrm_dms_motivation` WRITE;

INSERT  INTO `civicrm_dms_motivation`(`id`,`motivation_id`,`description`) VALUES (1,1000,'Bibliathon 91'),(2,1001,'Bibliathon 94'),(3,1002,'CTLA'),(4,1003,'Straatmark 2003/2004'),(5,1004,'Kolbe Disaster'),(6,1005,'In Memoriam'),(7,1006,'Straatmark / Street Market'),(8,1007,'Belgiese Bybels/Belgian Bibles'),(9,1008,'Bybels in Rwanda'),(10,1009,'BSSA 175'),(11,1010,'Comrades'),(12,1011,'BYBELSTAP (YSTERPLAAT)'),(13,1012,'STRAATMARK STRAND'),(14,1015,'NDEBELE PROJECT'),(15,1016,'Zulu Translation'),(16,1018,'WYNLAND MARATHON 10 NOVEMBER 2007'),(17,1019,'VOET VAN AFRIKA MARATHON (13OKT2007)'),(18,1020,'Two Oceans Marathon'),(19,1033,'Bybels vir Soldate/Bibles for Soldiers'),(20,1065,'Stranex Walk'),(21,1075,'Afrikaans 75'),(22,1090,'Bybels vir Uganda'),(23,1120,'Mosambiek Vloed'),(24,1125,'Tsunami'),(25,1126,'Philippine Typhoon'),(26,1130,'Bible a Month'),(27,1170,'BLP (Bible Literacy Program)'),(28,1180,'BYBELS VIR MASEDONIE'),(29,1190,'BIRTHDAY 190'),(30,1210,'Olimpiese Spele 2008 Beijing'),(31,1400,'EBD - English Bible for the Deaf'),(32,1404,'Argus Trap vir Bybels'),(33,1501,'Bible in Afrikaans'),(34,1502,'Bible in Southern Ndebele'),(35,1503,'Bibles for Children 2009'),(36,1504,'Bibles for Prisoners'),(37,1505,'Bibles for the Poor'),(38,1506,'Bibles for the Blind 2009'),(39,1507,'Bibles for Defence Force 2009 GnG'),(40,1508,'Affordable Bibles for All'),(41,1510,'Project Haiti'),(42,1511,'Bible Paper for China'),(43,1512,'Bibles for Japan'),(44,1517,'Southern African Bible Societies'),(45,1520,'Selfoon Bybel/Cellphone Bible'),(46,1530,'Bible Programmes for Human Needs'),(47,1533,'Literacy Programme'),(48,1535,'Bibles for Grade 7'),(49,1601,'Bybels vir Suid-Soedan'),(50,1712,'WWDP/WBVV'),(51,1800,'Translation - Gerrit van Steenbergen'),(52,1901,'Cycle for FCBH GnG'),(53,1953,'AFR 1953 Vertaling'),(54,1973,'Fundraiser/Fondswerwer'),(55,2000,'Bibliathon 2000'),(56,2001,'Nuwe Donateurs na Saaier 137'),(57,2003,'Kersfees 2003'),(58,2011,'MALAWI PROJEK'),(59,2019,'Comrades 2000'),(60,2020,'Gholfhemde'),(61,2021,'Balonne'),(62,3006,'GHOLFDAG / GOLF DAG'),(63,3014,'Word Riders'),(64,4011,'Jason de Wet Kinderbybels'),(65,1,'Brian'),(66,2,'Sambulo'),(67,4,'Hire of meeting room'),(68,5,'Cages in warehouse'),(69,53,'Clickatell'),(70,1100,'Kransie Briewe'),(71,1101,'Callie Human'),(72,1127,'I WAS A STRANGER'),(73,1212,'BYBELSE ETE'),(74,1213,'BYBELSE ETE 1997'),(75,1536,'Large-Print Bibles'),(76,1537,'Scholary Publications'),(77,2002,'Bybelstap'),(78,2004,'Dorpstappe'),(79,2005,'Skole/Jeugstappe'),(80,2006,'Gemeentestappe'),(81,2007,'Bybelse feesmale'),(82,2008,'Bybelleesmarathon'),(83,2009,'Bybel - oorskryf'),(84,2010,'Telethon 2010'),(85,2014,'Dinee 31 Okt 2014 Brand Pretorius'),(86,2015,'Ontw Sakesektor Heila v Wyk'),(87,2210,'Telethon 2011'),(88,2211,'Bikers for Bibles 2011'),(89,3001,'Golf Day 2003'),(90,3002,'Ds JM Louw'),(91,3003,'BYBELS VIR DIE BLINDES'),(92,3004,'GOLFDAY 2004/GOLFDAG 2004'),(93,3005,'Sunflower Project'),(94,3007,'Bybels vir Saudi Arabie'),(95,3008,'Duifvlieg vir Bybels'),(96,3009,'Middelburg Fees'),(97,3010,'bybelete (Bybelse feesmale - jaarliks)'),(98,3011,'Music Project'),(99,3012,'RUSTENBURG SAFARI 2009'),(100,3013,'Golf Day / Gholfdag'),(101,3015,'Noordwes Trap vir Bybels'),(102,3016,'Kilimanjaro Projek'),(103,3020,'pretdraf (Pretloop/draf - jaarliks)'),(104,3030,'Gholfdag (Gholfdag - jaarliks)'),(105,3033,''),(106,3040,'saaiers (response op saaier-April/Mei99)'),(107,4001,'Kalahari- Feesstap'),(108,4002,'Welkom - Fietstrap'),(109,4003,'Petrusburg-Fietstrap'),(110,4004,'WELKOM BERGFIETSTRAP(PIET DU TOIT)'),(111,4005,'Dawie Bornman Fietstrap'),(112,4012,''),(113,4013,'Bybelleesrooster'),(114,5000,'BYBELBUSSIES'),(115,5001,'Bible a Month'),(116,5005,'Bergfietstoer Oos-Kaap');

UNLOCK TABLES;

/*Table structure for table `civicrm_dms_next_budget` */

DROP TABLE IF EXISTS `civicrm_dms_next_budget`;

CREATE TABLE `civicrm_dms_next_budget` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Every table has an auto_incremented id',
  `bud_region` INT(2) DEFAULT NULL COMMENT 'region that the budget belongs to',
  `bud_department` VARCHAR(2) DEFAULT NULL COMMENT 'Department the budget belongs to',
  `bud_category` INT(4) DEFAULT NULL COMMENT 'Category the budget belongs to',
  `bud_amount` DECIMAL(20,4) DEFAULT NULL COMMENT 'Budget Amount',
  `bud_insert_user` VARCHAR(200) DEFAULT NULL COMMENT 'User who inserted the budget',
  `bud_dateinserted` DATETIME DEFAULT NULL COMMENT 'date and time the budget was inserted',
  `bud_datelastupdated` DATETIME DEFAULT NULL COMMENT 'date and time the budget was last updated',
  `bud_update_user` VARCHAR(200) DEFAULT NULL COMMENT 'User who last updated the budget',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNQ1` (`bud_region`,`bud_department`,`bud_category`)
) ENGINE=INNODB AUTO_INCREMENT=472 DEFAULT CHARSET=latin1;

/*Data for the table `civicrm_dms_next_budget` */

LOCK TABLES `civicrm_dms_next_budget` WRITE;

UNLOCK TABLES;

/*Table structure for table `civicrm_dms_office` */

DROP TABLE IF EXISTS `civicrm_dms_office`;

CREATE TABLE `civicrm_dms_office` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Every table has an auto_incremented id',
  `business_manager_contact_id` INT(11) DEFAULT NULL COMMENT 'Contact Id for the business manager',
  `name` VARCHAR(150) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Name of office',
  `address_eng` VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Address in English',
  `address_afr` VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Address in Afrikaans',
  `telephone` VARCHAR(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Telephone number for signatures',
  `fax` VARCHAR(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Fax',
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `civicrm_dms_office` */

LOCK TABLES `civicrm_dms_office` WRITE;

INSERT  INTO `civicrm_dms_office`(`id`,`business_manager_contact_id`,`name`,`address_eng`,`address_afr`,`telephone`,`fax`) VALUES (1,157033,'Head Office','134 edward street, bellville / po box 5500 tyger valley 7536','edwardstraat 134, bellville / posbus 5500 tygervallei 7536','+27 21 910 8766','+27 21 910 8799'),(2,157017,'Western Cape Office','134 edward street, bellville / po box 5500 tyger valley 7536','edwardstraat 134, bellville / posbus 5500 tygervallei 7536','+27 21 910 8766','+27 21 910 8799'),(3,157027,'Kwazulu Natal Office','70-76 ramsay avenue, mayville / po box 30801 mayville 4058','ramsaylaan 70-76, mayville / posbus 30801 mayville 4058','+27 31 207 4933','+27 31 207 4933'),(4,157060,'Kempton Park Administrative Center','8 anemoon road, glen marais ext 1 / po box 2002 kempton park 1620','anemoonweg 8, glen marais uitbr 1 / posbus 2002 kempton park 1620','+27 11 970 4010','+27 11 970 2506'),(5,157006,'Free State Office','220 nelson mandela drive, bloemfontein / po box 12149 brandhof 9324','nelson mandela rylaan 220, bloemfontein / posbus 12149 brandhof 9324','+27 51 444 5980','+27 51 444 5988'),(6,157108,'Eastern Cape Office','31 cotswold avenue, port elizabeth / po box 7579 newton park 6055','cotswoldlaan 31, port elizabeth / posbus 7579 newtonpark 6055','+27 41 364 1138','+27 41 365 2634'),(7,157108,'Mthatha Office','49 madeira street / po box 65 madeira 5099','madeirastraat 49, mthatha / posbus 265 mthatha 5099 ','+27 47 532 6402','+27 47 532 5719');

UNLOCK TABLES;

/*Table structure for table `civicrm_dms_office_bank` */

DROP TABLE IF EXISTS `civicrm_dms_office_bank`;

CREATE TABLE `civicrm_dms_office_bank` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Every table has an auto_incremented id',
  `office_id` INT(11) NOT NULL COMMENT 'Office Id from the civicrm_dms_office table',
  `bank_account_id` INT(11) NOT NULL COMMENT 'bank_account id from the civicrm_dms_bank_accounts table',
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `civicrm_dms_office_bank` */

LOCK TABLES `civicrm_dms_office_bank` WRITE;

UNLOCK TABLES;

/*Table structure for table `civicrm_dms_orders` */

DROP TABLE IF EXISTS `civicrm_dms_orders`;

CREATE TABLE `civicrm_dms_orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Every table has an auto_incremented id',
  `beneficiary_contact_id` INT(11) NOT NULL COMMENT 'contact id who is the beneficiary of contributions from this bank account',
  `owner_contact_id` INT(11) NOT NULL COMMENT 'contact id of the contact who owns the bank account',
  `type` VARCHAR(50) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Order Type:  Debit Order/Stop Order',
  `order_date` DATE DEFAULT NULL COMMENT 'Date this order will execute',
  `amount` DECIMAL(24,4) DEFAULT NULL COMMENT 'Contribution amount',
  `reference` VARCHAR(50) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Reference',
  `motivation_id` INT(11) DEFAULT NULL COMMENT 'Motivation code',
  `approved` VARCHAR(10) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Approved',
  `bank_id` INT(11) DEFAULT NULL COMMENT 'Bank account id from civicrm_dms_banks',
  `acccount_type` VARCHAR(50) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Type of bank account',
  `account_no` VARCHAR(50) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Bank account number',
  `account_branch_code` VARCHAR(20) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Bank account branch number',
  `cvv` VARCHAR(10) CHARACTER SET utf8 DEFAULT NULL COMMENT 'CVV number for credit cards',
  `expiry_date` VARCHAR(10) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Credit Card expiry date',
  `credit_card_type` VARCHAR(50) CHARACTER SET utf8 DEFAULT NULL COMMENT 'VISA/Mastercard',
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `civicrm_dms_orders` */

LOCK TABLES `civicrm_dms_orders` WRITE;

UNLOCK TABLES;

/*Table structure for table `civicrm_dms_region` */

DROP TABLE IF EXISTS `civicrm_dms_region`;

CREATE TABLE `civicrm_dms_region` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Every table has an auto_incremented id',
  `reg_id` INT(11) NOT NULL COMMENT 'region id',
  `reg_name` VARCHAR(50) CHARACTER SET utf8 DEFAULT NULL COMMENT 'region name',
  `reg_consol_id` INT(11) DEFAULT NULL COMMENT 'id for the consol file for the consol report.',
  `reg_joomla_group_id` INT(4) DEFAULT NULL COMMENT 'Group id linked to the region',
  `reg_office_id` INT(11) DEFAULT NULL COMMENT 'Office Id that this region is linked to',
  PRIMARY KEY (`id`),
  UNIQUE KEY `reg_id` (`reg_id`)
) ENGINE=INNODB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `civicrm_dms_region` */

LOCK TABLES `civicrm_dms_region` WRITE;

INSERT  INTO `civicrm_dms_region`(`id`,`reg_id`,`reg_name`,`reg_consol_id`,`reg_joomla_group_id`,`reg_office_id`) VALUES (1,0,'Unknown',0,NULL,1),(2,10,'Western Cape',1,NULL,2),(3,20,'Kwazulu Natal South',2,NULL,3),(4,31,'Gauteng',3,NULL,4),(5,32,'Limpopo',4,NULL,4),(6,33,'Mpumalanga',5,NULL,4),(7,34,'North West Province',6,NULL,4),(8,41,'Northern Cape',7,NULL,5),(9,42,'Free State',8,NULL,5),(10,50,'Eastern Cape',9,NULL,6),(11,60,'Transkei',10,NULL,6),(12,70,'Kwazulu Natal North',11,NULL,3);

UNLOCK TABLES;

/*Table structure for table `civicrm_dms_statement` */

DROP TABLE IF EXISTS `civicrm_dms_statement`;

CREATE TABLE `civicrm_dms_statement` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Every table has an auto_incremented id',
  `deposit_date` DATE DEFAULT NULL COMMENT 'Date of deposit',
  `deposit_reference` VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Reference made by depositor',
  `deposit_amount` DECIMAL(24,4) DEFAULT NULL COMMENT 'Amount deposited',
  `balance` DECIMAL(24,4) DEFAULT NULL COMMENT 'Balance amount after transaction',
  `document_name` VARCHAR(150) CHARACTER SET utf8 DEFAULT NULL COMMENT 'path to txt file impoted',
  `imported_datetime` DATETIME DEFAULT NULL COMMENT 'Date and time the document was imported',
  `imported_by` INT(11) DEFAULT NULL COMMENT 'User id to import the document',
  `batch_id` INT(11) DEFAULT NULL COMMENT 'Batch Id from civicrm_dms_batch_header table',
  `reconciled` VARCHAR(50) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Status Flag for the transaction',
  `reconciled_datetime` DATETIME DEFAULT NULL COMMENT 'Date and time transaction was reconciled',
  `reconciled_by` INT(11) DEFAULT NULL COMMENT 'User id to reconcile the transaction',
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `civicrm_dms_statement` */

LOCK TABLES `civicrm_dms_statement` WRITE;

UNLOCK TABLES;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

/*Table structure for table `civicrm_dms_acknowledgement` */

DROP TABLE IF EXISTS `civicrm_dms_acknowledgement`;

CREATE TABLE `civicrm_dms_acknowledgement` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Every table has an auto_incremented id',
  `contribution_id` int(11) NOT NULL COMMENT 'contribution id for the contribution being acknowledged',
  `acknowledgement_datetime` datetime DEFAULT NULL COMMENT 'Date and time contact was acknowledged for contribution',
  `usr_id` int(11) DEFAULT NULL COMMENT 'user id who acknowledged the contact',
  `method` varchar(100) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Method of acknowledgement',
  `document` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Document name and path of acknowledgement',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ack_contribution_id` (`contribution_id`,`id`)
) ENGINE=InnoDB AUTO_INCREMENT=255514 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `civicrm_dms_acknowledgement_preferences` */

DROP TABLE IF EXISTS `civicrm_dms_acknowledgement_preferences`;

CREATE TABLE `civicrm_dms_acknowledgement_preferences` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Every table has an auto_incremented id',
  `contact_id` int(11) NOT NULL COMMENT 'contact id to whom the preference belongs',
  `must_acknowledge` varchar(1) CHARACTER SET utf8 DEFAULT 'Y' COMMENT 'flag to identify if contact would like to be acknowledged',
  `frequency` smallint(6) DEFAULT NULL COMMENT 'how often must contact be acknowledged',
  `preferred_method` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT 'which communication method must be used for acknowledgements',
  `last_acknowledgement_date` datetime DEFAULT NULL COMMENT 'the last time the contact was acknowledged',
  `last_acknowledgement_contribution_id` int(11) DEFAULT NULL COMMENT 'the last contribution id acknowledged',
  `unacknowledged_total` decimal(20,4) DEFAULT '0.0000' COMMENT 'sum of contributions since last acknowledgement',
  `last_contribution_date` datetime DEFAULT NULL COMMENT 'Last contribution date of contact',
  PRIMARY KEY (`id`),
  UNIQUE KEY `apr_contact_id` (`contact_id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `civicrm_dms_category` */

DROP TABLE IF EXISTS `civicrm_dms_category`;

CREATE TABLE `civicrm_dms_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Every table has an auto_incremented id',
  `cat_id` varchar(4) CHARACTER SET utf8 NOT NULL COMMENT '4 character category id  (numeric)',
  `cat_name` varchar(100) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Category Name',
  `cat_departments` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Departments allowed to use this category in their budgets',
  PRIMARY KEY (`id`),
  UNIQUE KEY `cat_id` (`cat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=256 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `civicrm_dms_contact_other_data` */

DROP TABLE IF EXISTS `civicrm_dms_contact_other_data`;

CREATE TABLE `civicrm_dms_contact_other_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Every table has an auto_incremented id',
  `contact_id` int(11) DEFAULT NULL COMMENT 'Contact Id from the civicrm_contact table',
  `do_not_thank` tinyint(4) DEFAULT '0' COMMENT 'Temporary Acknowledgement Flag - Does Contact want to be acknowledged for contributions?',
  `reminder_month` tinyint(4) DEFAULT '0' COMMENT 'Month contact must reminded of debit order renewal',
  `id_number` varchar(15) CHARACTER SET utf8 DEFAULT NULL COMMENT 'South African ID number',
  `last_contribution_date` datetime DEFAULT NULL COMMENT 'Last time contact made a contribution',
  `last_contribution_amount` decimal(18,4) DEFAULT NULL COMMENT 'Last contribution amount',
  `inserted_by_contact_id` int(11) DEFAULT NULL COMMENT 'User who created the contact',
  `modified_by_contact_id` int(11) DEFAULT NULL COMMENT 'User who last modified the contact',
  PRIMARY KEY (`id`),
  UNIQUE KEY `other_data_contact_id` (`contact_id`)
) ENGINE=InnoDB AUTO_INCREMENT=939471 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `civicrm_dms_contact_reporting_code` */

DROP TABLE IF EXISTS `civicrm_dms_contact_reporting_code`;

CREATE TABLE `civicrm_dms_contact_reporting_code` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Every table has an auto_incremented id',
  `contact_id` int(11) DEFAULT NULL COMMENT 'Contact Id from the civicrm_contact table',
  `organisation_id` varchar(8) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Organisation Id',
  `category_id` varchar(4) CHARACTER SET utf8 DEFAULT NULL COMMENT '4 numeric character category id',
  PRIMARY KEY (`id`),
  UNIQUE KEY `report_code_contact_id` (`contact_id`)
) ENGINE=InnoDB AUTO_INCREMENT=886805 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `civicrm_dms_denomination` */

DROP TABLE IF EXISTS `civicrm_dms_denomination`;

CREATE TABLE `civicrm_dms_denomination` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Every table has an auto_incremented id',
  `den_id` varchar(2) NOT NULL COMMENT '2 character denomination id  (numeric)',
  `den_name` varchar(300) DEFAULT NULL COMMENT 'Denomination name',
  `den_consol_category` varchar(4) DEFAULT NULL COMMENT 'This field is the old denomination category xref',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=128 DEFAULT CHARSET=latin1;

/*Table structure for table `civicrm_dms_motivation` */

DROP TABLE IF EXISTS `civicrm_dms_motivation`;

CREATE TABLE `civicrm_dms_motivation` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Every table has an auto_incremented id',
  `motivation_id` int(11) NOT NULL COMMENT 'Motivation Code Id',
  `description` varchar(150) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Motivation Code description',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mot_id` (`motivation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=130 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `civicrm_dms_next_budget` */

DROP TABLE IF EXISTS `civicrm_dms_next_budget`;

CREATE TABLE `civicrm_dms_next_budget` (
  `bud_id` int(11) NOT NULL AUTO_INCREMENT,
  `bud_region` int(2) DEFAULT NULL,
  `bud_department` varchar(2) DEFAULT NULL,
  `bud_category` int(4) DEFAULT NULL,
  `bud_amount` decimal(20,4) DEFAULT NULL,
  `bud_insert_user` varchar(200) DEFAULT NULL,
  `bud_dateinserted` datetime DEFAULT NULL,
  `bud_datelastupdated` datetime DEFAULT NULL,
  `bud_update_user` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`bud_id`),
  UNIQUE KEY `UNQ1` (`bud_region`,`bud_department`,`bud_category`)
) ENGINE=InnoDB AUTO_INCREMENT=472 DEFAULT CHARSET=latin1;

/*Table structure for table `civicrm_dms_organisation` */

DROP TABLE IF EXISTS `civicrm_dms_organisation`;

CREATE TABLE `civicrm_dms_organisation` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Every table has an auto_incremented id',
  `org_id` varchar(15) CHARACTER SET utf8 NOT NULL COMMENT 'Organisation Id',
  `org_name` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Organisation code description',
  `org_region` int(11) DEFAULT NULL COMMENT 'region to which the organisation belongs (civicrm_dms_region)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `org_id` (`org_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16592 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `civicrm_dms_transaction` */

DROP TABLE IF EXISTS `civicrm_dms_transaction`;

CREATE TABLE `civicrm_dms_transaction` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Every table has an auto_incremented id',
  `contribution_id` int(11) DEFAULT NULL COMMENT 'contribution id from the civicrm_contribution table',
  `motivation_id` int(11) DEFAULT NULL COMMENT 'motivation id from the civicrm_dms_motivation table',
  `category_id` varchar(4) CHARACTER SET utf8 DEFAULT NULL COMMENT 'category id from the civicrm_dms_contact_reporting_code table',
  `region_id` int(11) DEFAULT NULL COMMENT 'region id from the civicrm_dms_organisation table',
  `organisation_id` varchar(15) CHARACTER SET utf8 DEFAULT NULL COMMENT 'category id from the civicrm_dms_contact_reporting_code table',
  `must_acknowledge` varchar(1) CHARACTER SET utf8 DEFAULT NULL COMMENT 'must the contact be acknowledged for this contribution',
  `completed_date` datetime DEFAULT NULL COMMENT 'date the contribution was completed',
  `completed_by_user_id` int(11) DEFAULT NULL COMMENT 'contribution completed by user',
  `batch_id` int(11) DEFAULT NULL COMMENT 'batch id',
  `batch_entry_id` int(11) DEFAULT NULL COMMENT 'batch entry id',
  `contact_id` int(11) DEFAULT NULL COMMENT 'Contact Id to whom the contribution belongs',
  PRIMARY KEY (`id`),
  UNIQUE KEY `trns_contribution_id` (`contribution_id`),
  KEY `trns_category_contribution` (`contribution_id`,`category_id`),
  KEY `trns_organisation_id` (`contribution_id`,`organisation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3629018 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
