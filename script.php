<?php
/**
* CG Chat Component  - Joomla 4.x Component 
* Version			: 1.0.0
* Package			: CG Chat
* copyright 		: Copyright (C) 2023 ConseilGouz. All rights reserved.
* license    		: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* From Kide ShoutBox
*/
// No direct access to this file
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\File;

class com_cgchatInstallerScript
{
	private $min_joomla_version      = '4.0';
	private $min_php_version         = '8.0';
	private $name                    = 'CG Chat';
	private $exttype                 = 'component';
	private $extname                 = 'cgchat';
	private $previous_version        = '';
	private $dir           = null;
	private $lang = null;
	private $installerName = 'cgchatinstaller';
	public function __construct()
	{
		$this->dir = __DIR__;
		$this->lang = Factory::getLanguage();
		$this->lang->load($this->extname);
	}
    function preflight($type, $parent)
    {

		if ( ! $this->passMinimumJoomlaVersion())
		{
			$this->uninstallInstaller();

			return false;
		}

		if ( ! $this->passMinimumPHPVersion())
		{
			$this->uninstallInstaller();

			return false;
		}
		// To prevent installer from running twice if installing multiple extensions
		if ( ! file_exists($this->dir . '/' . $this->installerName . '.xml'))
		{
			return true;
		}
		$xml = simplexml_load_file(JPATH_ADMIN . '/components/com_'.$this->extname.'/'.$this->extname.'.xml');
		$this->previous_version = $xml->version;
		
    }
    
    function install($parent)
    {
    }
    
    function uninstall($parent)
    {
    }
    
    function update($parent)
    {
    }
    
    function postflight($type, $parent)
    {
		if (($type=='install') || ($type == 'update')) { // remove obsolete dir/files
			$this->postinstall_cleanup();
		}
        switch ($type) {
            case 'install': $message = Text::_('SCRIPT_POSTFLIGHT_INSTALLED'); break;
            case 'uninstall': $message = Text::_('SCRIPT_POSTFLIGHT_UNINSTALLED'); break;
            case 'update': $message = Text::_('SCRIPT_POSTFLIGHT_UPDATED'); break;
            case 'discover_install': $message = Text::_('SCRIPT_POSTFLIGHT_DISC_INSTALLED'); break;
        }
        $message = '<h3>'.Text::sprintf('SCRIPT_POSTFLIGHT',$parent->getManifest()->name,$parent->getManifest()->version,$message).'</h3>';

		Factory::getApplication()->enqueueMessage($message.Text::_('COM_CGCHAT_XML_DESCRIPTION'), 'notice');

		// Uninstall this installer
		$this->uninstallInstaller();

		return true;


//         JFactory::getApplication()->enqueueMessage($message);       
    }
	private function postinstall_cleanup() {
		
/*		$obsoleteFiles = [
			JPATH_ADMINISTRATOR."/components/com_cgchat/updates.txt"
		];
		foreach ($obsoleteFiles as $file) {
			if (@is_file($file)) {
				File::delete($file);
			}
		}
		
		
		
		// remove obsolete update sites
		$db = Factory::getDbo();
		$query = $db->getQuery(true)
			->delete('#__update_sites')
			->where($db->quoteName('location') . ' like "%432473037d.url-de-test.ws/%"');
		$db->setQuery($query);
		$db->execute();
		// Simple Isotope is now on Github
		$query = $db->getQuery(true)
			->delete('#__update_sites')
			->where($db->quoteName('location') . ' like "%conseilgouz.com/updates/com_cgisotope%"');
		$db->setQuery($query);
		$db->execute();
			*/
	}
	// Check if Joomla version passes minimum requirement
	private function passMinimumJoomlaVersion()
	{
		if (version_compare(JVERSION, $this->min_joomla_version, '<'))
		{
			Factory::getApplication()->enqueueMessage(
				'Incompatible Joomla version : found <strong>' . JVERSION . '</strong>, Minimum : <strong>' . $this->min_joomla_version . '</strong>',
				'error'
			);

			return false;
		}

		return true;
	}

	// Check if PHP version passes minimum requirement
	private function passMinimumPHPVersion()
	{

		if (version_compare(PHP_VERSION, $this->min_php_version, '<'))
		{
			Factory::getApplication()->enqueueMessage(
					'Incompatible PHP version : found  <strong>' . PHP_VERSION . '</strong>, Minimum <strong>' . $this->min_php_version . '</strong>',
				'error'
			);
			return false;
		}

		return true;
	}
	
	private function uninstallInstaller()
	{
		if ( ! JFolder::exists(JPATH_PLUGINS . '/system/' . $this->installerName)) {
			return;
		}
		$this->delete([
			JPATH_PLUGINS . '/system/' . $this->installerName . '/language',
			JPATH_PLUGINS . '/system/' . $this->installerName,
		]);
		$db = Factory::getDbo();
		$query = $db->getQuery(true)
			->delete('#__extensions')
			->where($db->quoteName('element') . ' = ' . $db->quote($this->installerName))
			->where($db->quoteName('folder') . ' = ' . $db->quote('system'))
			->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
		$db->setQuery($query);
		$db->execute();
		Factory::getCache()->clean('_system');
	}
	
}