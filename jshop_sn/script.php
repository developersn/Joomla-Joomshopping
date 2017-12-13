<?php
defined('_JEXEC') or die('Restricted access');

class com_jshop_snInstallerScript
{

	function install($parent) 
	{
		
		jimport('joomla.filesystem.path');
		jimport( 'joomla.filesystem.folder' );
		
		$source=JPATH_ADMINISTRATOR . "/components/com_jshop_sn/files/pm_sn/";
		$destination=JPATH_SITE . "/components/com_jshopping/payments/pm_sn/";
		$this->Folder($source , $destination); 		
		
		$source=JPATH_ADMINISTRATOR . "/components/com_jshop_sn/files/pm_sn/checkout/";
		$destination=JPATH_SITE . "/components/com_jshopping/templates/default/checkout/";
		$this->Folder($source , $destination);
		
		echo "<br>install<br>";
	}


	function uninstall($parent) 
	{
	
	}


	function update($parent) 
	{
		jimport('joomla.filesystem.path');
		jimport( 'joomla.filesystem.folder' );
		
		$source=JPATH_ADMINISTRATOR . "/components/com_jshop_sn/files/pm_sn/";
		$destination=JPATH_SITE . "/components/com_jshopping/payments/pm_sn/";
		$this->Folder($source , $destination); 		
		
		$source=JPATH_ADMINISTRATOR . "/components/com_jshop_sn/files/pm_sn/checkout/";
		$destination=JPATH_SITE . "/components/com_jshopping/templates/default/checkout/";
		$this->Folder($source , $destination);
		
		echo "<br>install<br>";
	}

	function Folder($source , $destination){
		JFolder::copy($source, $destination,'', TRUE);   	
	}	
	

	function preflight($type, $parent) 
	{
	
	}


	function postflight($type, $parent) 
	{
	
	}
}
