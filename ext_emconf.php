<?php

########################################################################
# Extension Manager/Repository config file for ext "spamshield".
#
# Auto generated 24-07-2011 20:39
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
	'version' => '0.0.31',
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
			'pagepath' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			'pagepath' => '',
		),
	),
	'_md5_values_when_last_written' => 'a:15:{s:36:"class.tx_spamshield_formmodifier.php";s:4:"0238";s:35:"class.tx_spamshield_varanalyzer.php";s:4:"d8f9";s:21:"ext_conf_template.txt";s:4:"cffa";s:12:"ext_icon.gif";s:4:"8a2a";s:17:"ext_localconf.php";s:4:"986b";s:14:"ext_tables.php";s:4:"283d";s:14:"ext_tables.sql";s:4:"32f7";s:26:"icon_tx_spamshield_log.gif";s:4:"f140";s:13:"locallang.xml";s:4:"9c64";s:9:"style.css";s:4:"414e";s:7:"tca.php";s:4:"3146";s:8:"todo.txt";s:4:"1e05";s:14:"doc/manual.sxw";s:4:"ae00";s:20:"static/constants.txt";s:4:"d41d";s:16:"static/setup.txt";s:4:"9076";}',
);

?>