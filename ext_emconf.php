<?php

########################################################################
# Extension Manager/Repository config file for ext "spamshield".
#
# Auto generated 09-08-2011 18:32
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'spamshield',
	'description' => 'Universal invisible Spamshield for TYPO3',
	'category' => 'fe',
	'shy' => 0,
	'version' => '1.0.0',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Ronald P. Steiner, Alexander Kellner',
	'author_email' => 'ronald.steiner@googlemail.com, Alexander.Kellner@einpraegsam.net',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.5.0-4.6.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			'sr_freecap' => '',
			'captcha' => '',
		),
	),
	'_md5_values_when_last_written' => 'a:25:{s:36:"class.tx_spamshield_formmodifier.php";s:4:"33dd";s:35:"class.tx_spamshield_varanalyzer.php";s:4:"379d";s:16:"ext_autoload.php";s:4:"b50b";s:21:"ext_conf_template.txt";s:4:"136d";s:12:"ext_icon.gif";s:4:"8a2a";s:17:"ext_localconf.php";s:4:"fe41";s:14:"ext_tables.php";s:4:"95d9";s:14:"ext_tables.sql";s:4:"e006";s:26:"icon_tx_spamshield_log.gif";s:4:"f140";s:13:"locallang.xml";s:4:"a986";s:17:"locallang_csh.xml";s:4:"44b9";s:16:"locallang_db.xml";s:4:"4a13";s:9:"style.css";s:4:"414e";s:7:"tca.php";s:4:"c960";s:14:"doc/manual.sxw";s:4:"5182";s:14:"pi1/ce_wiz.gif";s:4:"02b6";s:31:"pi1/class.tx_spamshield_pi1.php";s:4:"036c";s:39:"pi1/class.tx_spamshield_pi1_wizicon.php";s:4:"31de";s:13:"pi1/clear.gif";s:4:"cc11";s:17:"pi1/locallang.xml";s:4:"10bd";s:19:"res/sr_freecap.html";s:4:"d1e0";s:20:"static/constants.txt";s:4:"d41d";s:16:"static/setup.txt";s:4:"3cf6";s:40:"tasks/class.tx_spamshield_logcleaner.php";s:4:"02c0";s:57:"tasks/class.tx_spamshield_logcleaner_additionalfields.php";s:4:"23d8";}',
	'suggests' => array(
	),
);

?>