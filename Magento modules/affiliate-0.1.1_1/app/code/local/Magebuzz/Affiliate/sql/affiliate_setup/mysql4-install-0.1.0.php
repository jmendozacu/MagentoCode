<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
$installer = $this;
$installer->startSetup();

$installer->run("
	-- DROP TABLE IF EXISTS {$this->getTable('affiliate_campaign')};
	CREATE TABLE {$this->getTable('affiliate_campaign')} (
	  `campaign_id` int(10) unsigned NOT NULL auto_increment,
	  `campaign_title` varchar(255) NOT NULL default '',
	  `commission_type` int(11) NOT NULL default '1',
	  `amount` DECIMAL( 12, 4 ) NOT NULL,
	  `rule` int(10) unsigned NOT NULL,
	  `date_start` datetime NULL,
	  `date_end` datetime NULL,
	  `status` smallint(6) NOT NULL default '0',
	  `description` text NOT NULL,
	  `store_id` int(11) NOT NULL default '0',
	  PRIMARY KEY (`campaign_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	
	-- DROP TABLE IF EXISTS {$this->getTable('affiliate_campaign_rule')};
	CREATE TABLE {$this->getTable('affiliate_campaign_rule')} (
	  `rule_id` int(10) unsigned NOT NULL auto_increment,
	  `actions_serialized` text NOT NULL,
	  PRIMARY KEY  (`rule_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	
	-- DROP TABLE IF EXISTS {$this->getTable('affiliate_affiliate')};
	CREATE TABLE {$this->getTable('affiliate_affiliate')} (
	  `affiliate_id` int(11) unsigned NOT NULL auto_increment,
	  `username` varchar(255) NOT NULL default '',
		`referral_code` varchar(255) NOT NULL default '',
	  `firstname` varchar(255) NOT NULL default '',
		`lastname` varchar(255) NOT NULL default '',
		`email` varchar(255) NOT NULL default '',
		`telephone` varchar(255) NOT NULL default '',
		`current_balance` decimal(12,4) NULL,
		`total_withdrawn` decimal(12,4) NULL,
		`address_id` int(11) unsigned NOT NULL default '0',
		`customer_id` int(11) unsigned NOT NULL default '0',
	  `created_at` datetime NULL,
	  `status` smallint(6) NOT NULL default '0',
		UNIQUE(`customer_id`),
		UNIQUE(`username`),
		UNIQUE(`referral_code`),
		INDEX(`customer_id`),
		FOREIGN KEY (`customer_id`) REFERENCES {$this->getTable('customer/entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,  
	  PRIMARY KEY (`affiliate_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	
	-- DROP TABLE IF EXISTS {$this->getTable('affiliate_campaign_relation')};
	CREATE TABLE {$this->getTable('affiliate_campaign_relation')} (
	  `relation_id` int(11) unsigned NOT NULL auto_increment,
		`affiliate_id` int(11) unsigned NOT NULL default '0',
		`campaign_id` int(11) unsigned NOT NULL default '0',
		FOREIGN KEY (`affiliate_id`) REFERENCES {$this->getTable('affiliate_affiliate')} (`affiliate_id`) ON DELETE CASCADE ON UPDATE CASCADE,  
		FOREIGN KEY (`campaign_id`) REFERENCES {$this->getTable('affiliate_campaign')} (`campaign_id`) ON DELETE CASCADE ON UPDATE CASCADE,  
	  PRIMARY KEY (`relation_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	
	-- DROP TABLE IF EXISTS {$this->getTable('affiliate_banner')};
	CREATE TABLE {$this->getTable('affiliate_banner')} (
	  `banner_id` int(11) unsigned NOT NULL auto_increment,
		`title` varchar(255) NOT NULL default '',
		`file` varchar(255) NOT NULL default '',
		`status` smallint(6) NOT NULL default '1',
	  PRIMARY KEY (`banner_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	
	-- DROP TABLE IF EXISTS {$this->getTable('affiliate_transaction')};
	CREATE TABLE {$this->getTable('affiliate_transaction')} (
	  `transaction_id` int(11) unsigned NOT NULL auto_increment,
		`campaign_id` int(11) unsigned NOT NULL default '0',
		`affiliate_id` int(11) unsigned NOT NULL default '0',
		`affiliate_name` varchar(255) NULL,
		`affiliate_email` varchar(255) NULL,
		`order_id` int(11) NOT NULL default '0',
		`item_id` int(11) NOT NULL default '0',
		`store_id` int(11) NOT NULL default '0',
		`commission` decimal(12,4) NULL,
		`total_amount` decimal(12,4) NULL,
		`created` datetime null,
		`status` smallint(6) NOT NULL default '0',
		FOREIGN KEY (`affiliate_id`) REFERENCES {$this->getTable('affiliate_affiliate')} (`affiliate_id`) ON DELETE CASCADE ON UPDATE CASCADE,
	  PRIMARY KEY (`transaction_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	
	-- DROP TABLE IF EXISTS {$this->getTable('affiliate_commission')};
	CREATE TABLE {$this->getTable('affiliate_commission')} (
		`commission_id` int(11) unsigned NOT NULL auto_increment,
		`affiliate_id` int(11) unsigned NOT NULL default '0',
		`transaction_id`  int(11) unsigned NOT NULL,
		`amount` decimal(12,4) NULL,
		`date_added` datetime null,
		`status` smallint(6) NOT NULL default '0',
		FOREIGN KEY (`affiliate_id`) REFERENCES {$this->getTable('affiliate_affiliate')} (`affiliate_id`) ON DELETE CASCADE ON UPDATE CASCADE,  		
	    FOREIGN KEY (`transaction_id`) REFERENCES {$this->getTable('affiliate_transaction')} (`transaction_id`) ON DELETE CASCADE ON UPDATE CASCADE,  
	  PRIMARY KEY (`commission_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	
	-- DROP TABLE IF EXISTS {$this->getTable('affiliate_campaign_store')};
	CREATE TABLE {$this->getTable('affiliate_campaign_store')} (
		`campaign_id` int(11) unsigned NOT NULL,
		`store_id` smallint(5) unsigned NOT NULL,
		PRIMARY KEY (`campaign_id`,`store_id`),
		CONSTRAINT `FK_MB_CAMPAIGN_STORE_CAMPAIGN` FOREIGN KEY (`campaign_id`) REFERENCES {$this->getTable('affiliate_campaign')} (`campaign_id`) ON UPDATE CASCADE ON DELETE CASCADE,
		CONSTRAINT `FK_MB_CAMPAIGN_STORE_STORE` FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core_store')} (`store_id`) ON UPDATE CASCADE ON DELETE CASCADE
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
	-- DROP TABLE IF EXISTS {$this->getTable('affiliate_payment_request')};
	CREATE TABLE {$this->getTable('affiliate_payment_request')} (
		`request_id` int(11) unsigned NOT NULL auto_increment,
		`affiliate_id` int(11) unsigned NOT NULL,
		`request_amount` decimal(12, 4) NOT NULL,
		`request_time` datetime NOT NULL,
		`payment_method` smallint(6) NOT NULL,
		`status` smallint(6) NOT NULL,
		`request_note`	text,
		`response_note` text,
		FOREIGN KEY (`affiliate_id`) REFERENCES {$this->getTable('affiliate_affiliate')} (`affiliate_id`) ON DELETE CASCADE ON UPDATE CASCADE,  
		PRIMARY KEY (`request_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup(); 