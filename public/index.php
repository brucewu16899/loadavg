<?php
/**
* LoadAvg - Server Monitoring & Analytics
* http://www.loadavg.com
*
* Main index file
* 
* @version SVN: $Id$
* @link https://github.com/loadavg/loadavg
* @author Karsten Becker
* @copyright 2014 Sputnik7
*
* This file is licensed under the Affero General Public License version 3 or
* later.
*/

require_once '../globals.php';

/* Session */
ob_start(); 
session_start();

/* Initialize LoadAvg Utility Class */ 
include 'class.Utility.php';

/* Initialize LoadAvg */ 
include 'class.LoadAvg.php';
$loadavg = new LoadAvg();

/* Initialize LoadAvg Charts module */ 
include 'class.Modules.php';
$loadModules = new LoadModules();

/* initialize timer */
include 'class.Timer.php';
$timer = new Timer();

//grab core settings
$settings = LoadAvg::$_settings->general;

//get plugins
$plugins = LoadAvg::$_plugins; 


//draw the header
require_once APP_PATH . '/layout/header.php';

/* 
 * check for successful installation
 */	

//check if installation is complete passed over by installer
if ( isset( $_GET['check'] ) ) 
{
	$loadavg->cleanUpInstaller();
} else {
	//check installation has been cleaned up for security reasons
	$loadavg->checkInstall();
}


/*
 * start polling time to generate charts
 */

$timer->setStartTime(); // Setting page load start time

/*
 * draw the current page view
 */

//array of modules and status either on or off
$loaded = LoadModules::$_settings->general['modules']; 

//grab the log diretory
$logdir = LOG_PATH;


//check to see if ip is banned before going on
$banned = false;
$flooding = false;

if ( isset($settings['ban_ip']) && $settings['ban_ip'] == "true" ) {

	$banned = $loadavg->checkIpBan();

	if ( $banned ) {
		//clean up session
		$loadavg->logOut();      
	}
}

//used for remember me at login time
//if no session exists check for cookies
if (  (!isset($_SESSION['logged_in']) || ($_SESSION['logged_in'] == false)) && ($banned == false) )
{
	//if cookies are here and match log them in
	if ($loadavg->checkCookies()) {
		$_SESSION['logged_in'] = true;        
	    header("Location: /index.php");
	}
}


//security check for all access
if ( (isset($settings['settings']['allow_anyone']) && $settings['settings']['allow_anyone'] == "false" && !$loadavg->isLoggedIn())  || ($banned == true)   ) 
{
	include( APP_PATH . '/views/login.php');
} 
else 
{

	//first lets see if a name has been set...
	$pageName = "";

	if ( isset($_GET['page']) && ($_GET['page'] != "") ) 
		$pageName = $_GET['page'];


	//first check to see if its a plugin
	if (in_array($pageName, $plugins)) 
    {
    	echo 'PLUGIN: ' . $_GET['page'] . '<br>';
		require_once PLUGIN_PATH .  $pageName  . '/' . $pageName . '.php';
    }

	//if not check to see if its a view page
	else if ( file_exists( APP_PATH . '/views/' . $pageName . '.php' ) ) 
	{
		//echo 'PAGE: ' . $pageName . '<br>';
		require_once APP_PATH . '/views/' . $pageName . '.php';
	} 

	//if not draw default index page
	else
	{
		//if page doesnt exist redirect to index, can be modified to a page not found if need be
		require_once APP_PATH . '/views/index.php';
	}
}

/* 
 * finish polling time to generate charts
 */

// set page load finish time
$timer->setFinishTime(); 

// Calculating total page load time
$page_load = $timer->getPageLoadTime(); 

//draw the footer
require_once APP_PATH . '/layout/footer.php'; 
?>