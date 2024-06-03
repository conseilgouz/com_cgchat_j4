<?php
/**
* CG Chat Component  - Joomla 4.x/5.x Component
* Version			: 1.0.0
* Package			: CG Chat
* copyright 		: Copyright (C) 2024 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
* From              : Kide ShoutBox
*/

// No direct access to this file
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;

class com_cgchatInstallerScript
{
    private $min_joomla_version      = '4.0';
    private $min_php_version         = '8.0';
    private $extname                 = 'cgchat';
    private $dir           = null;
    private $lang = null;
    private $installerName = 'cgchatinstaller';
    public function __construct()
    {
        $this->dir = __DIR__;
        $this->lang = Factory::getApplication()->getLanguage();
        $this->lang->load($this->extname);
    }
    public function preflight($type, $parent)
    {

        if (! $this->passMinimumJoomlaVersion()) {
            $this->uninstallInstaller();

            return false;
        }

        if (! $this->passMinimumPHPVersion()) {
            $this->uninstallInstaller();

            return false;
        }
        // To prevent installer from running twice if installing multiple extensions
        if (! file_exists($this->dir . '/' . $this->installerName . '.xml')) {
            return true;
        }
        $xml = simplexml_load_file(JPATH_ADMIN . '/components/com_'.$this->extname.'/'.$this->extname.'.xml');
        $this->previous_version = $xml->version;

    }

    public function install($parent)
    {
    }

    public function uninstall($parent)
    {
    }

    public function update($parent)
    {
    }

    public function postflight($type, $parent)
    {
        if (($type == 'install') || ($type == 'update')) { // remove obsolete dir/files
            $this->postinstall_cleanup();
        }
        switch ($type) {
            case 'install': $message = Text::_('SCRIPT_POSTFLIGHT_INSTALLED');
                break;
            case 'uninstall': $message = Text::_('SCRIPT_POSTFLIGHT_UNINSTALLED');
                break;
            case 'update': $message = Text::_('SCRIPT_POSTFLIGHT_UPDATED');
                break;
            case 'discover_install': $message = Text::_('SCRIPT_POSTFLIGHT_DISC_INSTALLED');
                break;
        }
        $message = '<h3>'.Text::sprintf('SCRIPT_POSTFLIGHT', $parent->getManifest()->name, $parent->getManifest()->version, $message).'</h3>';

        Factory::getApplication()->enqueueMessage($message.Text::_('COM_CGCHAT_XML_DESCRIPTION'), 'notice');

        // Uninstall this installer
        $this->uninstallInstaller();

        return true;


    }
    private function postinstall_cleanup()
    {
        $obsloteFolders = ['templates/dark/css', 'templates/default/css','templates/default/images','templates/default/js','templates/default/sound'];
        foreach ($obsloteFolders as $folder) {
            $f = JPATH_SITE . '/components/com_cgchat/' . $folder;
            if (!@file_exists($f) || !is_dir($f) || is_link($f)) {
                continue;
            }
            Folder::delete($f);
        }
    }
    // Check if Joomla version passes minimum requirement
    private function passMinimumJoomlaVersion()
    {
        if (version_compare(JVERSION, $this->min_joomla_version, '<')) {
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

        if (version_compare(PHP_VERSION, $this->min_php_version, '<')) {
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
        if (! is_dir(JPATH_PLUGINS . '/system/' . $this->installerName)) {
            return;
        }
        $this->delete([
            JPATH_PLUGINS . '/system/' . $this->installerName . '/language',
            JPATH_PLUGINS . '/system/' . $this->installerName,
        ]);
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true)
            ->delete('#__extensions')
            ->where($db->quoteName('element') . ' = ' . $db->quote($this->installerName))
            ->where($db->quoteName('folder') . ' = ' . $db->quote('system'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
        $db->setQuery($query);
        $db->execute();
        Factory::getCache()->clean('_system');
    }
    public function delete($files = [])
    {
        foreach ($files as $file) {
            if (is_dir($file)) {
                Folder::delete($file);
            }

            if (is_file($file)) {
                File::delete($file);
            }
        }
    }

}
