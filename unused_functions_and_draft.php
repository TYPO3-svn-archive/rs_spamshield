<?php
/* 
create a cObj before the rest of FE is loaded
... not working as it should ...
*/
function createCObj($pid = 1){
	if(!defined("PATH_tslib")){
		define('PATH_tslib', PATH_site.'typo3/sysext/cms/tslib/');
	}
	require_once(PATH_site.'typo3/sysext/cms/tslib/class.tslib_fe.php');
	require_once(PATH_site.'t3lib/class.t3lib_userauth.php');
	require_once(PATH_site.'typo3/sysext/cms/tslib/class.tslib_feuserauth.php');
	require_once(PATH_site.'t3lib/class.t3lib_cs.php');
	require_once(PATH_site.'typo3/sysext/cms/tslib/class.tslib_content.php') ;
	require_once(PATH_site.'t3lib/class.t3lib_tstemplate.php');
	require_once(PATH_site.'t3lib/class.t3lib_page.php');
	require_once(PATH_site.'t3lib/class.t3lib_timetrack.php');
 
	// Finds the TSFE classname
	$TSFEclassName = t3lib_div::makeInstanceClassName('tslib_fe');
 
	// Create the TSFE class.
	$GLOBALS['TSFE'] = new $TSFEclassName($GLOBALS['TYPO3_CONF_VARS'], $pid, '0', 0, '','','','');
 
	$temp_TTclassName = t3lib_div::makeInstanceClassName('t3lib_timeTrack');
	$GLOBALS['TT'] = new $temp_TTclassName();
	$GLOBALS['TT']->start();
 
	$GLOBALS['TSFE']->config['config']['language']=$_GET['L'];
 
	// Fire all the required function to get the typo3 FE all set up.
	$GLOBALS['TSFE']->id = $pid;
	$GLOBALS['TSFE']->connectToMySQL();
 
	// Prevent mysql debug messages from messing up the output
	$sqlDebug = $GLOBALS['TYPO3_DB']->debugOutput;
	$GLOBALS['TYPO3_DB']->debugOutput = false;
 
	$GLOBALS['TSFE']->initLLVars();
	$GLOBALS['TSFE']->initFEuser();
 
	// Look up the page
	$GLOBALS['TSFE']->sys_page = t3lib_div::makeInstance('t3lib_pageSelect');
	$GLOBALS['TSFE']->sys_page->init($GLOBALS['TSFE']->showHiddenPage);
 
	// If the page is not found (if the page is a sysfolder, etc), then return no URL, preventing any further processing which would result in an error page.
	$page = $GLOBALS['TSFE']->sys_page->getPage($pid);
 
	if (count($page) == 0) {
		$GLOBALS['TYPO3_DB']->debugOutput = $sqlDebug;
		return false;
	}
 
	// If the page is a shortcut, look up the page to which the shortcut references, and do the same check as above.
	if ($page['doktype']==4 && count($GLOBALS['TSFE']->getPageShortcut($page['shortcut'],$page['shortcut_mode'],$page['uid'])) == 0) {
		$GLOBALS['TYPO3_DB']->debugOutput = $sqlDebug;
		return false;
	}
 
	// Spacer pages and sysfolders result in a page not found page too…
	if ($page['doktype'] == 199 || $page['doktype'] == 254) {
		$GLOBALS['TYPO3_DB']->debugOutput = $sqlDebug;
		return false;
	}
 
	$GLOBALS['TSFE']->getPageAndRootline();
	$GLOBALS['TSFE']->initTemplate();
	$GLOBALS['TSFE']->forceTemplateParsing = 1;
 
	// Find the root template
	$GLOBALS['TSFE']->tmpl->start($GLOBALS['TSFE']->rootLine);
 
	// Fill the pSetup from the same variables from the same location as where tslib_fe->getConfigArray will get them, so they can be checked before this function is called
	$GLOBALS['TSFE']->sPre = $GLOBALS['TSFE']->tmpl->setup['types.'][$GLOBALS['TSFE']->type];        // toplevel - objArrayName
	$GLOBALS['TSFE']->pSetup = $GLOBALS['TSFE']->tmpl->setup[$GLOBALS['TSFE']->sPre.'.'];
 
	// If there is no root template found, there is no point in continuing which would result in a 'template not found' page and then call exit php. Then there would be no clickmenu at all.
	// And the same applies if pSetup is empty, which would result in a "The page is not configured" message.
	if (!$GLOBALS['TSFE']->tmpl->loaded || ($GLOBALS['TSFE']->tmpl->loaded && !$GLOBALS['TSFE']->pSetup)) {
		$GLOBALS['TYPO3_DB']->debugOutput = $sqlDebug;
		return false;
	}
 
	$GLOBALS['TSFE']->getConfigArray();
	$GLOBALS['TSFE']->getCompressedTCarray();
 
	$GLOBALS['TSFE']->inituserGroups();
	$GLOBALS['TSFE']->connectToDB();
	$GLOBALS['TSFE']->determineId();
 
	return $GLOBALS['TSFE']->newCObj();
}
?>