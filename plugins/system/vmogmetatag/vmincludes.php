<?php
/**
 *---------------------------------------------------------------------------------------
 * @package       VM OG Meta Tag - System Plugin
 *---------------------------------------------------------------------------------------
 * @copyright     Copyright (C) 2012-2015 VirtuePlanet Services LLP. All rights reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 * @authors       Abhishek Das
 * @email         info@virtueplanet.com
 * @link          http://www.virtueplanet.com
 *---------------------------------------------------------------------------------------
 */
defined('_JEXEC') or die('Restricted access');

// Register VirtueMart config to Joomla autoloader
if(!class_exists('VmConfig'))
{
	require JPATH_ROOT . '/administrator/components/com_virtuemart/helpers/config.php';
}

// Load VirtueMart Config and language
VmConfig::loadConfig();
VmConfig::loadJLang('com_virtuemart', true);

// Register all required VirtueMart classes to Joomla autoloader
JLoader::register('VmImage', JPATH_ROOT . '/administrator/components/com_virtuemart/helpers/image.php');
JLoader::register('TableMedias', JPATH_ROOT . '/administrator/components/com_virtuemart/tables/medias.php');
JLoader::register('TableCategories', JPATH_ROOT . '/administrator/components/com_virtuemart/tables/categories.php');
JLoader::register('VirtueMartModelCategory', JPATH_ROOT . '/administrator/components/com_virtuemart/models/category.php');