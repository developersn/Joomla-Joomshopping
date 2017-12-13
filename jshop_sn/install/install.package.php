<?php

  function com_install()
  {
	jimport('joomla.filesystem.path');
	jimport( 'joomla.filesystem.folder' );
	$source=JPATH_ADMINISTRATOR . "/components/com_jshop_sn/files/pm_sn/";
    $destination=JPATH_SITE . "/components/com_jshopping/payments/pm_sn/";
	$path=JPATH_ADMINISTRATOR . "/components/com_jshop_sn/";
	Folder($source , $destination); 
	}
function Folder($source , $destination){
    JFolder::copy($source, $destination,'', TRUE);   	
}
function delete($path)
{
        if (!$path) {
                JError::raiseWarning(500, 'JFolder::delete: ' . JText::_('Attempt to delete base directory') );
                return false;
        }
 
        jimport('joomla.client.helper');
        $ftpOptions = JClientHelper::getCredentials('ftp');
 
        $path = JPath::clean($path);
 
        if (!is_dir($path)) {
                JError::raiseWarning(21, 'JFolder::delete: ' . JText::_('Path is not a folder'), 'Path: ' . $path);
                return false;
        }
 
        $files = JFolder::files($path, '.', false, true, array());
        if (!empty($files)) {
                jimport('joomla.filesystem.file');
                if (JFile::delete($files) !== true) {
                        return false;
                }
        }
 
        $folders = JFolder::folders($path, '.', false, true, array());
        foreach ($folders as $folder) {
                if (is_link($folder)) {
                        jimport('joomla.filesystem.file');
                        if (JFile::delete($folder) !== true) {
                                return false;
                        }
                } elseif (JFolder::delete($folder) !== true) {
                        return false;
                }
        }
 
        if ($ftpOptions['enabled'] == 1) {
                jimport('joomla.client.ftp');
                $ftp = &JFTP::getInstance(
                        $ftpOptions['host'], $ftpOptions['port'], null,
                        $ftpOptions['user'], $ftpOptions['pass']
                );
        }
 
        if (@rmdir($path)) {
                $ret = true;
        } elseif ($ftpOptions['enabled'] == 1) {
                $path = JPath::clean(str_replace(JPATH_ROOT, $ftpOptions['root'], $path), '/');
                $ret = $ftp->delete($path);
        } else {
                JError::raiseWarning(
                        'SOME_ERROR_CODE',
                        'JFolder::delete: ' . JText::_('Could not delete folder'),
                        'Path: ' . $path
                );
                $ret = false;
        }
        return $ret;
}	
?>
