<?php
if (!defined ('TYPO3_MODE'))
	die ('Access denied.');

$TCA['tx_spamshield_log'] = array (
    'ctrl' => array (
        'title'     => 'Spamshield',
        'label'     => 'spamreason',
        'label_alt' => 'requesturl,crdate',
        'tstamp'    => 'tstamp',
        'crdate'    => 'crdate',
        'cruser_id' => 'cruser_id',
        'default_sortby' => "ORDER BY crdate DESC",
        'delete' => 'deleted',
        'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
        'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_spamshield_log.gif',
    ),
    'feInterface' => array (
        'fe_admin_fieldList' => 'spamreason, requesturl, spamweight, postvalues, getvalues, pageid, ip, useragent, referer'
    )
);

t3lib_extMgm::addStaticFile($_EXTKEY,'static/', 'spamshield spam protection');  // for TS template
?>