# Extend table 'tt_content'
CREATE TABLE tt_content (
  layout varchar(255) DEFAULT '' NOT NULL,
  header_class VARCHAR(255) DEFAULT '' NOT NULL,
  subheader_position VARCHAR(255) DEFAULT '' NOT NULL,
  tx_sitepackage_feedback_item int(11) DEFAULT '0' NOT NULL,
);

# Create table 'tx_sitepackage_feedback_item'
CREATE TABLE tx_sitepackage_feedback_item (
    tt_content int(11) unsigned DEFAULT '0',
    sorting int(11) DEFAULT '0' NOT NULL,
    header varchar(255) DEFAULT '' NOT NULL,
    bodytext text,
    date int(10) unsigned DEFAULT '0' NOT NULL,
    location varchar(255) DEFAULT '' NOT NULL,
);
