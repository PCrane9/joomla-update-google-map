<?php
/**
 * Copyright info goes here... not good at legal stuff. Will update
 *
 * 
 */
/**
 * A command line script to download alternate Google Map plug in (Embed Google Maps) and update the map Widgetkit
 */
// We are a valid entry point.
const _JEXEC = 1;
// Load system defines
if (file_exists(dirname(__DIR__) . '/defines.php'))
{
	require_once dirname(__DIR__) . '/defines.php';
	
}
if (!defined('_JDEFINES'))
{
	define('JPATH_BASE', dirname(__DIR__));
	require_once JPATH_BASE . '/includes/defines.php';
}
// Get the framework.
require_once JPATH_LIBRARIES . '/import.legacy.php';
// Bootstrap the CMS libraries.
require_once JPATH_LIBRARIES . '/cms.php';
// Configure error reporting to maximum for CLI output.
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Load Library language
$lang = JFactory::getLanguage();
// Try the files_joomla file in the current language (without allowing the loading of the file in the default language)
$lang->load('files_joomla.sys', JPATH_SITE, null, false, false)
// Fallback to the files_joomla file in the default language
|| $lang->load('files_joomla.sys', JPATH_SITE, null, true);

class SetNewMap extends JApplicationCli
{
	/**
	 * Entry point for CLI script
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function doExecute()
	{
		//Prompts user for address
		echo "Please enter address, city state and zip code: ";
		$address = fgets(STDIN);
		
		//Enables new map plug in
		
		$db = JFactory::getDbo();

        $query = $db->getQuery(true);
        
        // Fields to update.
        $fields = array(
            $db->quoteName('enabled') . ' = 1'
        );
        
        // Conditions for which records should be updated.
        $conditions = array(
            $db->quoteName('enabled') . ' = 0', 
            $db->quoteName('name') . ' = ' . $db->quote('PLG_EMBED_GOOGLE_MAP')
        );
        
        $query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);
        
        $db->setQuery($query);
        
        $result = $db->execute();
        
        //Updates Widgetkit
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        
        // Fields to update.
        $fields = array(
            $db->quoteName('type') . ' = ' . 
                $db->quote('custom'),
            $db->quoteName('data') . ' = ' . 
                $db->quote('{"_widget":{"name":"slideshow","data":{"nav":"none","nav_overlay":true,"nav_align":"center","thumbnail_width":"70","thumbnail_height":"70","thumbnail_alt":false,"slidenav":"none","nav_contrast":true,"animation":"fade","slices":"15","duration":"500","autoplay":false,"interval":"7000","autoplay_pause":true,"kenburns":false,"kenburns_animation":"","kenburns_duration":"15","fullscreen":false,"min_height":"300","media":false,"image_width":"auto","image_height":"auto","overlay":"center","overlay_animation":"fade","overlay_background":false,"link_media":false,"title":false,"content":true,"title_size":"h1","content_size":"","link":true,"link_style":"primary","link_text":"Read more","badge":false,"badge_style":"badge","link_target":false,"class":"uk-contrast"}},"items":[{"media":"","options":{"media":[]},"content":"{google_map}'.
                    trim($address). 
                    '|width:1920|height:300{\/google_map}"}],"random":0,"parse_shortcodes":1,"_fields":[]}')
        );
        
        // Conditions for which records should be updated.
        $conditions = array(
            $db->quoteName('data') . ' LIKE ' . $db->quote('%"name":"map"%')
            
        );
        
        $query->update($db->quoteName('#__widgetkit'))->set($fields)->where($conditions);
        
        $db->setQuery($query);
        
        $result = $db->execute();
        
		echo "\033[32m Success! Map update is complete! \033 \n";
	}
}
// Instantiate the application object, passing the class name to JCli::getInstance
// and use chaining to execute the application.
JApplicationCli::getInstance('SetNewMap')->execute();
