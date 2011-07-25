<?php
if(!defined('TYPO3_MODE'))
	die ('Access denied.');

$TCA["tx_spamshield_log"] = array (
    "ctrl" => $TCA["tx_spamshield_log"]["ctrl"],
    "interface" => array (
        "showRecordFieldList" => "spamreason,requesturl,spamweight,postvalues,getvalues,pageid,ip,useragent,referer"
    ),
    "feInterface" => $TCA["tx_spamshield_log"]["feInterface"],
    "columns" => array (
        "spamreason" => Array (
            "exclude" => 1,
            "label" => "LLL:EXT:spamshield/locallang.xml:tx_spamshield_log.spamreason",
            "config" => Array (
                "type" => "input",
                "size" => "30",
            )
        ),
        "requesturl" => Array (
            "exclude" => 1,
            "label" => "LLL:EXT:spamshield/locallang.xml:tx_spamshield_log.requesturl",
            "config" => Array (
                "type" => "input",
                "size" => "80",
            )
        ),
		"spamweight" => Array (
            "exclude" => 1,
            "label" => "LLL:EXT:spamshield/locallang.xml:tx_spamshield_log.spamweight",
            "config" => Array (
                "type" => "input",
                "size" => "5",
            )
        ),
        "postvalues" => array (
            "exclude" => 1,
            "label" => "LLL:EXT:spamshield/locallang.xml:tx_spamshield_log.postvalues",
            "config" => array (
                "type" => "text",
                "cols" => "30",
                "rows" => "5",
                "wizards" => array (
                    "_PADDING" => 2,
                    "RTE" => array(
                        "notNewRecords" => 1,
                        "RTEonly" => 1,
                        "type" => "script",
                        "title" => "RTE content",
                        "icon" => "wizard_rte2.gif",
                        "script" => "wizard_rte.php",
                    ),
                ),
            )
        ),
        "getvalues" => array (
            "exclude" => 1,
            "label" => "LLL:EXT:spamshield/locallang.xml:tx_spamshield_log.getvalues",
            "config" => array (
                "type" => "text",
                "cols" => "30",
                "rows" => "5",
                "wizards" => array (
                    "_PADDING" => 2,
                    "RTE" => array(
                        "notNewRecords" => 1,
                        "RTEonly" => 1,
                        "type" => "script",
                        "title" => "RTE content",
                        "icon" => "wizard_rte2.gif",
                        "script" => "wizard_rte.php",
                    ),
                ),
            )
        ),
        "pageid" => Array (
            "exclude" => 1,
            "label" => "LLL:EXT:spamshield/locallang.xml:tx_spamshield_log.pageid",
            "config" => Array (
                "type" => "input",
                "size" => "5",
            )
        ),
        "ip" => Array (
            "exclude" => 1,
            "label" => "LLL:EXT:spamshield/locallang.xml:tx_spamshield_log.ip",
            "config" => Array (
                "type" => "input",
                "size" => "30",
            )
        ),
        "useragent" => Array (
            "exclude" => 1,
            "label" => "LLL:EXT:spamshield/locallang.xml:tx_spamshield_log.useragent",
            "config" => Array (
                "type" => "input",
                "size" => "30",
            )
        ),
        "referer" => Array (
            "exclude" => 1,
            "label" => "LLL:EXT:spamshield/locallang.xml:tx_spamshield_log.referer",
            "config" => Array (
                "type" => "input",
                "size" => "30",
            )
        ),
    ),
    "types" => array (
        "0" => array("showitem" => "spamreason,requesturl,spamweight, postvalues;;;richtext[], getvalues;;;richtext[], pageid, ip, useragent, referer")
    ),
    "palettes" => array (
        "1" => array("showitem" => "")
    )
);
?>