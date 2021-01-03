#
# Table structure for table 'tt_content'
#
#removed the stupid not nulls
CREATE TABLE tt_content (
tx_gooffotoboek_function varchar(7) default '',
tx_gooffotoboek_path mediumtext,
tx_gooffotoboek_webpath mediumtext
);
#
# Table structure for table 'tx_txgooftest_basket'
#
CREATE TABLE tx_gooffotoboek_basket (
#uid int(11) NOT NULL auto_increment,
img_id int(11) auto_increment,
pid int(11) DEFAULT '0',
tstamp int(11) DEFAULT '0',
crdate int(11) DEFAULT '0',
cruser_id int(11) DEFAULT '0',
deleted tinyint(4) DEFAULT '0',
hidden tinyint(4) DEFAULT '0',
    
    PRIMARY KEY (img_id),
    KEY parent (pid)

    session_id varchar(255) DEFAULT '',
    is_on_page varchar(255) DEFAULT '',
    image varchar(255) DEFAULT '',
    #add_date datetime DEFAULT '0000-00-00 00:00:00',
    #add_date date DEFAULT '0000-00-00',
    add_date int(11) unsigned DEFAULT '0' NOT NULL,
);
