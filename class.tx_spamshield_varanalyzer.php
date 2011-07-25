<?php

/***************************************************************
*  Copyright notice
*  
*  (c) 2009  Dr. Ronald Steiner <Ronald.Steiner@googlemail.com>
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is 
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
* 
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
* 
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once (PATH_tslib."class.tslib_pibase.php");
require_once (PATH_tslib."class.tslib_content.php");

class tx_spamshield_varanalyzer extends tslib_pibase  {

	var $prefixId = "tx_spamshield_varanalyzer";                       // Same as class name
	var $scriptRelPath = "class.tx_spamshield_varanalyzer.php";        // Path to this script relative to the extension dir.
	var $extKey = "spamshield";        // The extension key.
	
	var $params = false;    	// Complete TS-Config 
	var $pObj = false;			// pObj at time of hook call

	var $conf = false;       	// config from ext_conf_template.txt
	
	var $GETparams;    		// GET variables
	var $POSTparams;    	// POST variables
	var $GPparams;    		// POST variables
	
	var $spamReason = array(); 	// description of the error
	var $spamWeight = 0;   	 	// weight of the spam

	/**
	 * Hook page id lookup before rendering the content.
	 *
	 * @param	object		$_params: parameter array
	 * @param	object		$pObj: partent object
	 * @return	void
	 */
	function main (&$params, &$pObj) {
		$this->params = &$params;
		$this->pObj = &$pObj;
		$this->conf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);

		# set global variables
		$this->GETparams = t3lib_div::_GET();
		$this->POSTparams = t3lib_div::_POST();
		$this->GPparams =  t3lib_div::array_merge_recursive_overrule($this->GETparams,$this->POSTparams,0,0);

		// check Spam according to rules
		if (!$this->conf['rule']) {
			$this->conf['rule'] = "useragent,1;referer,1;javascript,1;honeypot,1;httpbl,1";
		}
		$rules = explode(';',$this->conf['rule']);
		foreach ($rules as $rule) {
			list($function,$weight) = explode(',',$rule);
			$function = trim($function);
			$weight = trim($weight);
			if (method_exists($this,$function)) {
				if ($this->$function()) {
					$this->spamReason[] = $function;
					$this->spamWeight += $weight;
				}
			}
		}
		
		// if spam => dbLog and stopOutput
		if (!$this->conf['weight']) {
			$this->conf['weight'] = 1;
		}
		if ($this->spamWeight >= $this->conf['weight']) {
			$this->dbLog();
			$this->stopOutput(); 
		}	
		else {
			return;  // no spam detected
		}
	}

	/**
	 * Stops TYPO3 output and shows an error page. 
	 * - derivated from mh_httpbl
	 *
	 * @return	void
	 */	
	function stopOutput() {
		if (!$this->conf['message']) {
			$this->conf['message'] = '<strong>you have been blocked.</strong>';
		}
		$output = '
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>TYPO3 - http:BL</title>
	</head>
	<body style="background: #fff; color: #ccc; font-family: \'Verdana\', \'Arial\', sans-serif; text-align: center;">
		'.$this->conf['message'].'
	</body>
</html>
		';
		// Prevent caching on the client side
		header("Expires: 0");                                           // Datum aus Vergangenheit
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");  // immer geändert
		header("Cache-Control: no-store, no-cache, must-revalidate");   // HTTP/1.1
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");                                     // HTTP/1.0
		print $output;
		die();
	}
	
	/**
	 * Put a log entry in the DB if spam is detected
	 *
	 * @param	nothing
	 * @return  boolean
	 */
	function dbLog() {
		if (!$this->conf['logpid']) {
			$this->conf['logpid'] = 0;
		}
		elseif ($this->conf['logpid'] == -1) {
			return; // log is disabled
		}
		if ($this->piVars['refpid']) {
			$ref = $this->piVars['refpid'];
		}
		else {
			$ref = $GLOBALS["TSFE"]->id;
		}
        $db_values = array (
        	'pid' => mysql_escape_string($this->conf['logpid']),  // spam-log storage page
            'tstamp' => time(),
            'crdate' => time(),
            'spamWeight' => mysql_escape_string($this->spamWeight),
            'spamReason' => mysql_escape_string(implode(',',$this->spamReason)),
            'postvalues' => mysql_escape_string(t3lib_utility_Debug::viewArray($this->POSTparams)),  	# Typo3 < 4.5: t3lib_div::view_array(...)
            'getvalues' => mysql_escape_string(t3lib_utility_Debug::viewArray($this->GETparams)),		# Typo3 < 4.5: t3lib_div::view_array(...)
            'pageid' => mysql_escape_string($ref),
			'requesturl' => t3lib_div::getIndpEnv('TYPO3_REQUEST_URL'),
            'ip' => mysql_escape_string(t3lib_div::getIndpEnv('REMOTE_ADDR')),
            'useragent' => mysql_escape_string(t3lib_div::getIndpEnv('HTTP_USER_AGENT')),
            'referer' => mysql_escape_string(t3lib_div::getIndpEnv('HTTP_REFERER'))
        );
        $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_spamshield_log', $db_values); // DB entry
		return;
    }
	
	/*
		from mh_httpbl
		-5 => 'localhost'
		- 3 => 'sonstiges = here possibly whitelist / blacklist'
		-2 => 'no REMOTE_ADDR = no request possible'
		-1 => 'no acesskey = no request possible'
		0 => 'Search Engine',
		1 => 'Suspicious',
		2 => 'Harvester',
		3 => 'Suspicious & Harvester',
		4 => 'Comment Spammer',
		5 => 'Suspicious & Comment Spammer',
		6 => 'Harvester & Comment Spammer',
		7 => 'Suspicious & Harvester & Comment Spammer'
		
		httpbl reccomends to block >= 2
	*/
	function httpbl() {
		if (empty($this->conf['accesskey'])) {
			$type = -1;
		}
		elseif (empty($_SERVER['REMOTE_ADDR'])) {
			$type = -2;
		}
		else {
			$type = -3;
			$codes = array(  # codes used by httpbl.org
		0 => 'Search Engine',
		1 => 'Suspicious',
		2 => 'Harvester',
		3 => 'Suspicious &amp; Harvester',
		4 => 'Comment Spammer',
		5 => 'Suspicious &amp; Comment Spammer',
		6 => 'Harvester &amp; Comment Spammer',
		7 => 'Suspicious &amp; Harvester &amp; Comment Spammer'
			);
			$domain	= 'dnsbl.httpbl.org';
			$request = $this->conf['accesskey'].'.'.implode('.', array_reverse(explode('.', $_SERVER['REMOTE_ADDR']))).'.'.$domain;
			$result = gethostbyname($request);
			if ($result != $request) {
				list($first, $days, $score, $type) = explode('.', $result);  // $type = one of the $codes; higher $score = more active bot
			}
			if ($first != 127 || !array_key_exists($this->type, $codes)) {
				$type = -5;
			}
		}
		if ($type >= $this->conf['type']) {
			return TRUE;  // = Spam
		}
		else {
			return FALSE; // = no Spam
		}
	}
	
	/**
	* useragant
	*
    * Every browser sends a HTTP_USER_AGENT value to a server.
    * 	So a missing HTTP_USER_AGENT value almost always indicates a spammer bot.
    */
	function useragent() {
	    if (t3lib_div::getIndpEnv('HTTP_USER_AGENT') == "") {
		   	return TRUE;  		// = spam
		}
		return FALSE; // no spam;
	}

	/**
	* referer
	*
    * The most of browsers (all modern browsers) send a HTTP_REFERER value,
    * 	... which would contain the submitted form URL.
    *   ... which therefore should be from same domain.
    *  Whereas clever bots send this value, a missing HTTP_REFERER value could mean a bot submitting.
    *  Note. There are several firewall and security products which block HTTP_REFERER by default.
    *  So, none of these people could send a message if you block posting without HTTP_REFERER.
	 */
	function referer() {
		// no form data send => external referers are o.k.
	    if (!is_array($this->POSTparams) || sizeof($this->POSTparams) == 0) {
			return FALSE;  // no spam
		}
		elseif ($this->conf['whitelist']) {
            $whiteList = explode(',',$this->conf['whitelist']);
            foreach ($whiteList as $white) {
				$white = trim($white);
                if (strpos(t3lib_div::getIndpEnv("HTTP_REFERER"),$white)!==false) {
                    return FALSE; // no spam
                }
            }  
        }
		// checking for empty referers or referers that are external of the website
		elseif (
			(!t3lib_div::getIndpEnv('HTTP_REFERER') || t3lib_div::getIndpEnv("HTTP_REFERER") == "")
			||
			($GLOBALS['TSFE']->tmpl->setup['config.']['baseUrl'] &&
			!strstr(t3lib_div::getIndpEnv("HTTP_REFERER"),$GLOBALS['TSFE']->tmpl->setup['config.']['baseURL'])) 
			|| 
			($GLOBALS['TSFE']->tmpl->setup['config.']['absRefPrefix'] &&
		    !strstr(t3lib_div::getIndpEnv("HTTP_REFERER"),$GLOBALS['TSFE']->tmpl->setup['config.']['absRefPrefix']))
			){
			return TRUE; // spam
		}
		else {
			return FALSE;  // no spam
		}
	}

	/**
	 * checks if the java-skript cookie is availabel
	 * some bots don't accept cookies normally
	 *
	 * @param	nothing
	 * @return  boolean  
	 */
	function javascript() {
		// no form data send => new users are wellcome and have no cookie
		return FALSE; // no spam - this check does not work propper in some situations - the cookie gets lost on page changes
###
# to do: find a better java skript check
###
	    if (!is_array($this->POSTparams) || sizeof($this->POSTparams) == 0) {
			return FALSE;  // no spam
		}
		elseif (!$_COOKIE['spamshield']) {
		   	return TRUE; // spam
		}
		else {
			return FALSE; // no spam
		}
	}
	
	/**
	 * checks if cookie is availabel
	 * some bots don't accept cookies normally
	 *
	 * @param	nothing
	 * @return  boolean  
	 */
	function cookie() {
		// no form data send => new users are wellcome and have no cookie
	    if (!is_array($this->POSTparams) || sizeof($this->POSTparams) == 0) {
			return FALSE;  // no spam
		}
		elseif (!$_COOKIE['fe_typo_user']) {
		   	return TRUE; // spam
		}
		else {
			return FALSE; // no spam
		}
	}

	/**
	 * checks if honey pot fields are filled in
	 * bots normally don't read CSS / Java and fill in everything.
	 *
	 * @param	nothing
	 * @return  boolean
	 */
	function honeypot() {
		if ($this->GPparams['email'] || $this->GPparams['e-mail'] || $this->GPparams['name'] || $this->GPparams['first-name']) {
			return TRUE; // spam
		}
		else {
			return FALSE; // no spam
		}
	}	
}

if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/spamshield/class.tx_spamshield_varanalyzer.php"]){
        include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/spamshield/class.tx_spamshield_varanalyzer.php"]);
}

?>
