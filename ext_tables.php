<?php
if (!defined ('TYPO3_MODE'))
	die ('Access denied.');

#####################################################
## FE-Plugin                                  #######
#####################################################
t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages';
t3lib_extMgm::addPlugin(array(
	'LLL:EXT:spamshield/locallang_db.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');
if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_spamshield_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_spamshield_pi1_wizicon.php';
}
#####################################################

#####################################################
## TCA to show DB-Table in BE                 #######
#####################################################
$TCA['tx_spamshield_log'] = array (
    'ctrl' => array (
        'title'     => 'Spamshield',
        'label'     => 'spamreason',
        'label_alt' => 'pageid,solved',
		'label_alt_force' => 1,
        'tstamp'    => 'tstamp',
        'crdate'    => 'crdate',
        'cruser_id' => 'cruser_id',
        'default_sortby' => "ORDER BY crdate DESC",
        'delete' => 'deleted',
        'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
        'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_spamshield_log.gif',
    ),
    'feInterface' => array (
        'fe_admin_fieldList' => 'spamreason, spamweight, postvalues, getvalues, requesturl, pageid, referer, ip, useragent, solved'
    )
);
#####################################################

#####################################################
## Make TS available                          #######
#####################################################
t3lib_extMgm::addStaticFile($_EXTKEY,'static/', 'spamshield spam protection');  // for TS template
#####################################################
?>