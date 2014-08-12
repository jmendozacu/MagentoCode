<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
$installer = $this;
$installer->startSetup();

$installer->run("
	ALTER TABLE {$this->getTable('affiliate_campaign')} ADD `created_time` datetime NULL;
	ALTER TABLE {$this->getTable('affiliate_campaign')} ADD `updated_time` datetime NULL;
	ALTER TABLE {$this->getTable('affiliate_campaign')} ADD `priority` int(11) NOT NULL default '0';
	ALTER TABLE {$this->getTable('affiliate_campaign')} ADD `auto_assign` smallint(6) NOT NULL default '1';
");

$installer->endSetup(); 