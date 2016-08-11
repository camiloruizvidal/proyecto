<?php
/**
 *---------------------------------------------------------------------------------------
 * @package       VP Framework for Joomla!
 *---------------------------------------------------------------------------------------
 * @copyright     Copyright (C) 2012-2015 VirtuePlanet Services LLP. All rights reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 * @authors       Abhishek Das
 * @email         info@virtueplanet.com
 * @link          http://www.virtueplanet.com
 *---------------------------------------------------------------------------------------
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.folder');

class JFormFieldComponentList extends JFormField
{

	protected $type = 'ComponentList';

	protected function getInput()
	{
		jimport('joomla.filesystem.file');
		
		if(version_compare(JVERSION, '3.0.0', 'ge'))
		{
			JHtml::_('formbehavior.chosen', 'select');
		}
		
		$lang = JFactory::getLanguage();
		$list = array();
		// Get the list of components.
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('name, element AS ' . $db->quoteName('option'))
			->from('#__extensions')
			->where('type = ' . $db->quote('component'))
			->where('enabled = 1')
			->order('name ASC');
		$db->setQuery($query);
		$components = $db->loadObjectList();
		$optItems = '<select id="' . $this->id . '" name="' . $this->name . '" multiple>';

		if(is_string($this->value))
		{
			if(strpos($this->value, ','))
			{
				$values = explode(',', $this->value);
				$values = array_map('trim', $values);
			}
			else
			{
				$values = array($this->value);
			}
		}
		else
		{
			$values = $this->value;
		}
		
		$vmConfig = JPath::clean(JPATH_ROOT . '/administrator/components/com_virtuemart/helpers/config.php');
		
		if(file_exists($vmConfig))
		{
			if(!class_exists('VmConfig'))
			{
				require $vmConfig;
			}
			
			VmConfig::loadConfig();
			VmConfig::loadJLang('com_virtuemart', true);
			VmConfig::loadJLang('com_virtuemart_config', true);
			VmConfig::loadJLang('com_virtuemart.sys', true);
		}
		
		$values = is_array($values) ? $values : array();
		
		foreach ($components as $component)
		{
			if ($options = $this->getTypeOptionsByComponent($component->option))
			{
				$component_name = (strpos($component->name, 'com_') === 0) ? $component->name : 'com_' . $component->name;
				
				$lang->load($component_name.'.sys', JPATH_ADMINISTRATOR, null, false, false)
			||	$lang->load($component_name.'.sys', JPATH_ADMINISTRATOR.'/components/'.$component_name, null, false, false)
			||	$lang->load($component_name.'.sys', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
			||	$lang->load($component_name.'.sys', JPATH_ADMINISTRATOR.'/components/'.$component_name, $lang->getDefault(), false, false);
				
				$list[$component->name] = $options;
				// Create the reverse lookup for link-to-name.	
				$optItems .=  '<optgroup label="' . JText::_(strtoupper($component->name)) . '">';
				$loaded = array();
				$count = count($options);
				$i=1;
				foreach ($options as $option)
				{
					$layout = isset($option->request['layout']) ? $option->request['layout'] : '';
					
					$opt_value = array(
										"option" => $option->request['option'],
										"view" => $option->request['view'],
										"layout" => $layout
									  );
									  
					$opt_value = base64_encode(json_encode($opt_value));
					$selected = (in_array($opt_value, $values))? " selected=selected" : "";
					
					if(!in_array($opt_value, $loaded))
					{
						$optItems .= '<option value="'.$opt_value.'"'.$selected.' >' . JText::_($option->title) . '</option>';
						$loaded[] = $opt_value;
					}
					
					if($i == $count) 
					{
						$optItems .= '</optgroup>';
					}
					$i++;					
				}
			}
		}

		$optItems .= '</select>';	

		return $optItems;
	}
	
	
	protected function getTypeOptionsByComponent($component)
	{
		$options = array();

		$mainXML = JPATH_SITE.'/components/'.$component.'/metadata.xml';

		if (is_file($mainXML))
		{
			$options = $this->getTypeOptionsFromXML($mainXML, $component);
		}

		if (empty($options))
		{
			$options = $this->getTypeOptionsFromMVC($component);
		}

		return $options;
	}

	protected function getTypeOptionsFromXML($file, $component)
	{
		$options = array();

		// Attempt to load the xml file.
		if (!$xml = simplexml_load_file($file))
		{
			return false;
		}

		// Look for the first menu node off of the root node.
		if (!$menu = $xml->xpath('menu[1]'))
		{
			return false;
		}
		else
		{
			$menu = $menu[0];
		}

		// If we have no options to parse, just add the base component to the list of options.
		if (!empty($menu['options']) && $menu['options'] == 'none')
		{
			// Create the menu option for the component.
			$o = new JObject;
			$o->title		= (string) $menu['name'];
			$o->description	= (string) $menu['msg'];
			$o->request		= array('option' => $component);

			$options[] = $o;

			return $options;
		}

		// Look for the first options node off of the menu node.
		if (!$optionsNode = $menu->xpath('options[1]'))
		{
			return false;
		}
		else
		{
			$optionsNode = $optionsNode[0];
		}

		// Make sure the options node has children.
		if (!$children = $optionsNode->children())
		{
			return false;
		}
		else
		{
			// Process each child as an option.
			foreach ($children as $child)
			{
				if ($child->getName() == 'option')
				{
					// Create the menu option for the component.
					$o = new JObject;
					$o->title		= (string) $child['name'];
					$o->description	= (string) $child['msg'];
					$o->request		= array('option' => $component, (string) $optionsNode['var'] => (string) $child['value']);

					$options[] = $o;
				}
				elseif ($child->getName() == 'default')
				{
					// Create the menu option for the component.
					$o = new JObject;
					$o->title		= (string) $child['name'];
					$o->description	= (string) $child['msg'];
					$o->request		= array('option' => $component);

					$options[] = $o;
				}
			}
		}

		return $options;
	}

	protected function getTypeOptionsFromMVC($component)
	{
		$options = array();

		// Get the views for this component.
		$path = JPATH_SITE . '/components/' . $component . '/views';

		if (is_dir($path))
		{
			$views = JFolder::folders($path);
		}
		else
		{
			return false;
		}

		foreach ($views as $view)
		{
			// Ignore private views.
			if (strpos($view, '_') !== 0)
			{
				// Determine if a metadata file exists for the view.
				$file = $path.'/'.$view.'/metadata.xml';

				if (is_file($file))
				{
					// Attempt to load the xml file.
					if ($xml = simplexml_load_file($file))
					{
						// Look for the first view node off of the root node.
						if ($menu = $xml->xpath('view[1]'))
						{
							$menu = $menu[0];

							// If the view is hidden from the menu, discard it and move on to the next view.
							if (!empty($menu['hidden']) && $menu['hidden'] == 'true')
							{
								unset($xml);
								continue;
							}

							// Do we have an options node or should we process layouts?
							// Look for the first options node off of the menu node.
							if ($optionsNode = $menu->xpath('options[1]'))
							{
								$optionsNode = $optionsNode[0];

								// Make sure the options node has children.
								if ($children = $optionsNode->children())
								{
									// Process each child as an option.
									foreach ($children as $child)
									{
										if ($child->getName() == 'option')
										{
											// Create the menu option for the component.
											$o = new JObject;
											$o->title		= (string) $child['name'];
											$o->description	= (string) $child['msg'];
											$o->request		= array('option' => $component, 'view' => $view, (string) $optionsNode['var'] => (string) $child['value']);

											$options[] = $o;
										}
										elseif ($child->getName() == 'default')
										{
											// Create the menu option for the component.
											$o = new JObject;
											$o->title		= (string) $child['name'];
											$o->description	= (string) $child['msg'];
											$o->request		= array('option' => $component, 'view' => $view);

											$options[] = $o;
										}
									}
								}
							}
							else {
								$options = array_merge($options, (array) $this->getTypeOptionsFromLayouts($component, $view));
							}
						}
						unset($xml);
					}

				}
				else {
					$options = array_merge($options, (array) $this->getTypeOptionsFromLayouts($component, $view));
				}
			}
		}

		return $options;
	}

	protected function getTypeOptionsFromLayouts($component, $view)
	{
		$options = array();
		$layouts = array();
		$layoutNames = array();
		$templateLayouts = array();
		$lang = JFactory::getLanguage();

		// Get the layouts from the view folder.
		$path = JPATH_SITE . '/components/' . $component . '/views/' . $view . '/tmpl';
		if (is_dir($path))
		{
			$layouts = array_merge($layouts, JFolder::files($path, '.xml$', false, true));
		}
		else
		{
			return $options;
		}

		// build list of standard layout names
		foreach ($layouts as $layout)
		{
			// Ignore private layouts.
			if (strpos(basename($layout), '_') === false)
			{
				$file = $layout;
				// Get the layout name.
				$layoutNames[] = basename($layout, '.xml');
			}
		}

		// get the template layouts
		// TODO: This should only search one template -- the current template for this item (default of specified)
		$folders = JFolder::folders(JPATH_SITE . '/templates', '', false, true);
		// Array to hold association between template file names and templates
		$templateName = array();
		foreach ($folders as $folder)
		{
			if (is_dir($folder . '/html/' . $component . '/' . $view))
			{
				$template = basename($folder);
				$lang->load('tpl_'.$template.'.sys', JPATH_SITE, null, false, false)
				||	$lang->load('tpl_'.$template.'.sys', JPATH_SITE.'/templates/'.$template, null, false, false)
				||	$lang->load('tpl_'.$template.'.sys', JPATH_SITE, $lang->getDefault(), false, false)
				||	$lang->load('tpl_'.$template.'.sys', JPATH_SITE.'/templates/'.$template, $lang->getDefault(), false, false);

				$templateLayouts = JFolder::files($folder . '/html/' . $component . '/' . $view, '.xml$', false, true);

				foreach ($templateLayouts as $layout)
				{
					$file = $layout;
					// Get the layout name.
					$templateLayoutName = basename($layout, '.xml');

					// add to the list only if it is not a standard layout
					if (array_search($templateLayoutName, $layoutNames) === false)
					{
						$layouts[] = $layout;
						// Set template name array so we can get the right template for the layout
						$templateName[$layout] = basename($folder);
					}
				}
			}
		}

		// Process the found layouts.
		foreach ($layouts as $layout)
		{
			// Ignore private layouts.
			if (strpos(basename($layout), '_') === false)
			{
				$file = $layout;
				// Get the layout name.
				$layout = basename($layout, '.xml');

				// Create the menu option for the layout.
				$o = new JObject;
				$o->title		= ucfirst($layout);
				$o->description	= '';
				$o->request		= array('option' => $component, 'view' => $view);

				// Only add the layout request argument if not the default layout.
				if ($layout != 'default')
				{
					// If the template is set, add in format template:layout so we save the template name
					$o->request['layout'] = (isset($templateName[$file])) ? $templateName[$file] . ':' . $layout : $layout;
				}

				// Load layout metadata if it exists.
				if (is_file($file))
				{
					// Attempt to load the xml file.
					if ($xml = simplexml_load_file($file))
					{
						// Look for the first view node off of the root node.
						if ($menu = $xml->xpath('layout[1]'))
						{
							$menu = $menu[0];

							// If the view is hidden from the menu, discard it and move on to the next view.
							if (!empty($menu['hidden']) && $menu['hidden'] == 'true')
							{
								unset($xml);
								unset($o);
								continue;
							}

							// Populate the title and description if they exist.
							if (!empty($menu['title']))
							{
								$o->title = trim((string) $menu['title']);
							}

							if (!empty($menu->message[0]))
							{
								$o->description = trim((string) $menu->message[0]);
							}
						}
					}
				}

				// Add the layout to the options array.
				$options[] = $o;
			}
		}

		return $options;
	}

}
