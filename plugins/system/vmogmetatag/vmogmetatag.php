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

/**
* VM OG Meta Tag system plugin class
* Comaptible to Joomla! 2.5 and Joomla! 3
* 
* @since 2.1
*/
class plgSystemVmogmetatag extends JPlugin
{
	private $app;
	private $doc;
	private $docType;
	private $option;
	private $view;
	private $task;
	private $tmpl;
	private $layout;
	
	private static $initialized = false;
	private static $done = false;
	private static $data = array();
	private static $title = '';
	private static $description = '';
	private static $exclude = null;
	private static $ogPreifx = null;
	private static $metaData = array();

	/**
	* Joomla! Plugin Standard Constructor
	* @param undefined $subject
	* @param undefined $params
	* 
	* @return void
	*/
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
	}

	/**
	* Initialize plugin variables
	* 
	* @return void
	*/
	private function _init()
	{
		if(static::$initialized)
		{
			return;
		}
		
		$this->app  = JFactory::getApplication();
		$this->doc  = JFactory::getDocument();
		$this->docType = strtolower($this->doc->getType());
		
		if(version_compare(JVERSION, '3.0.0', 'ge'))
		{
			$input         = $this->app->input;
			$this->option  = strtolower($input->getCmd('option', ''));
			$this->view    = strtolower($input->getCmd('view', ''));
			$this->task    = strtolower($input->getCmd('task', ''));
			$this->task    = $input->post->getCmd('task', '') ?
			                 strtolower($input->post->getCmd('task', '')) :
			                 $this->task;
			$this->tmpl    = strtolower($input->getCmd('tmpl', ''));
			$this->layout  = strtolower($input->getCmd('layout', ''));
		}
		else
		{
			$this->option  = strtolower(JRequest::getCmd('option', ''));
			$this->view    = strtolower(JRequest::getCmd('view', ''));
			$this->task    = strtolower(JRequest::getCmd('task', ''));
			$this->tmpl    = strtolower(JRequest::getCmd('tmpl', ''));
			$this->layout  = strtolower(JRequest::getCmd('layout', ''));
		}
		
		if(!empty($this->option) && !empty($this->view))
		{
			static::$initialized = true;
		}
	}

	/**
	* After route events
	* 
	* @return void
	*/
	public function onAfterRoute()
	{
		$this->_init();
		
		if($this->app->isAdmin() || $this->docType != 'html' || !empty($this->tmpl))
		{
			return;
		}

		if($this->isExcluded())
		{
			return;
		}
		
		if($this->option == 'com_virtuemart' && $this->view == 'category')
		{
			require_once(dirname(__FILE__) . '/vmincludes.php');
			
			$category_id = vRequest::getInt('virtuemart_category_id', -1);
			if($category_id == -1) $category_id = 0;
			$categoryModel = VmModel::getModel('category');
			$category = $categoryModel->getCategory($category_id);
			$categoryModel->addImages($category, 1);
			
			if(!empty($category) && $category->virtuemart_category_id > 0)
			{
				$this->setData('og:type', 'product.group');
				
				if(!empty($category->images) && isset($category->images[0]))
				{
					$image = $category->images[0];
					
					if(!$image->file_is_forSale)
					{
						if(substr($image->file_url, 0, 4) == 'http')
						{
							$this->setData('og:image', $image->file_url);
						} 
						else 
						{
							$media_path = JPath::clean(JPATH_SITE . '/' . $image->file_url);
							
							if (!file_exists($media_path)) 
							{
								$url = $image->theme_url . 'assets/images/vmgeneral/' . VmConfig::get('no_image_set');
								$this->setData('og:image', $url);
							} 
							else 
							{
								$this->setData('og:image', JUri::root() . $image->file_url);
							}
						}
					}
					else
					{
						if(empty($image->file_name))
						{
							if($image->file_is_downloadable)
							{
								$url = $image->theme_url . 'assets/images/vmgeneral/' . VmConfig::get('downloadable', 'zip.png');
							} 
							else 
							{
								$url = $image->theme_url . 'assets/images/vmgeneral/' . VmConfig::get('no_image_set');
							}
							$this->setData('og:image', $url);
						}
						elseif(!empty($image->file_url_thumb))
						{
							if(substr($image->file_url_thumb, 0, 4) == 'http')
							{
								$this->setData('og:image', $image->file_url_thumb);
							} 
							else 
							{
								$media_path = JPath::clean(JPATH_SITE . '/' . $image->file_url_thumb);
								
								if (!file_exists($media_path)) 
								{
									$url = $image->theme_url . 'assets/images/vmgeneral/' . VmConfig::get('no_image_set');
									$this->setData('og:image', $url);
								} 
								else 
								{
									$this->setData('og:image', JUri::root() . $image->file_url);
								}
							}
						}
					}
				}
			}
		}
		elseif($this->option == 'com_contact' && $this->view == 'contact')
		{
			$this->setData('og:type', 'business.business');
		}
	}
	
	/**
	* Before head compile events
	* 
	* @return void
	*/
	public function onBeforeCompileHead()
	{
		$this->_init();
		
		if($this->app->isAdmin() || $this->docType != 'html' || !empty($this->tmpl))
		{
			return;
		}
		
		if($this->isExcluded())
		{
			return;
		}
		
		// Load OG meta data
		$this->loadMetaData();
	}

	/**
	* VirtueMart Product Page Payment Display events
	* 
	* @param  $product                 (object)  Product Object
	* @param  $productDisplayPayments  (array)   Payment display HTML array
	* 
	* @return void
	*/
	public function plgVmOnProductDisplayPayment($product, &$productDisplayPayments)
	{
		$this->_init();
		
		if($this->app->isAdmin() || $this->docType != 'html' || !empty($this->tmpl))
		{
			return;
		}
		
		if($this->view != 'productdetails')
		{
			return;
		}

		if($this->isExcluded())
		{
			return;
		}
	
		$currency = CurrencyDisplay::getInstance();
		
		$this->setData('og:type', 'product');
		
		if(!empty($product->category_name))
		{
			$this->setData('product:category', $product->category_name);
		}
		
		if(!empty($product->manufacturers) && count($product->manufacturers) == 1 && isset($product->manufacturers[0]))
		{
			$this->setData('product:brand', $product->manufacturers[0]->mf_name);
		}
		elseif(!empty($product->mf_name))
		{
			$this->setData('product:brand', $product->mf_name);
		}
		
		if(!empty($product->prices['salesPrice']))
		{
			$this->setData('product:price:amount', $product->prices['salesPrice']);
			$this->setData('product:price:currency', $currency->_vendorCurrency_code_3);
			$this->setData('product:sale_price:amount', $product->prices['salesPrice']);
			$this->setData('product:sale_price:currency', $currency->_vendorCurrency_code_3);
		}
		
		if(!empty($product->product_sku))
		{
			$this->setData('product:retailer_part_no', $product->product_sku);
		}
		
		$stockhandle = VmConfig::get('stockhandle', 'none');
		$product_available_date = substr($product->product_available_date,0,10);
		$current_date = date("Y-m-d");
		
		if ($product_available_date != '0000-00-00' and $current_date < $product_available_date)
		{
			$this->setData('product:availability', 'pending');
		}
		elseif($stockhandle != 'none' && ($product->product_in_stock - $product->product_ordered) < 1)
		{
			$this->setData('product:availability', 'oos');
		}
		else
		{
			$this->setData('product:availability', 'instock');
		}
		
		$product_weight = floatval($product->product_weight);
		
		if(!empty($product_weight))
		{
			$this->setData('product:weight:value', $product_weight);
			$this->setData('product:weight:units', $product->product_weight_uom);
		}
		
		if(!empty($product->images) && isset($product->images[0]))
		{
			$image = $product->images[0];
			
			if(!$image->file_is_forSale)
			{
				if(substr($image->file_url, 0, 4) == 'http')
				{
					$this->setData('og:image', $image->file_url);
				} 
				else 
				{
					$media_path = JPath::clean(JPATH_SITE . '/' . $image->file_url);
					
					if (!file_exists($media_path)) 
					{
						$url = $image->theme_url . 'assets/images/vmgeneral/' . VmConfig::get('no_image_set');
						$this->setData('og:image', $url);
					} 
					else 
					{
						$this->setData('og:image', JUri::root() . $image->file_url);
					}
				}
			}
			else
			{
				if(empty($image->file_name))
				{
					if($image->file_is_downloadable)
					{
						$url = $image->theme_url . 'assets/images/vmgeneral/' . VmConfig::get('downloadable', 'zip.png');
					} 
					else 
					{
						$url = $image->theme_url . 'assets/images/vmgeneral/' . VmConfig::get('no_image_set');
					}
					$this->setData('og:image', $url);
				}
				elseif(!empty($image->file_url_thumb))
				{
					if(substr($image->file_url_thumb, 0, 4) == 'http')
					{
						$this->setData('og:image', $image->file_url_thumb);
					} 
					else 
					{
						$media_path = JPath::clean(JPATH_SITE . '/' . $image->file_url_thumb);
						
						if (!file_exists($media_path)) 
						{
							$url = $image->theme_url . 'assets/images/vmgeneral/' . VmConfig::get('no_image_set');
							$this->setData('og:image', $url);
						} 
						else 
						{
							$this->setData('og:image', JUri::root() . $image->file_url);
						}
					}
				}
			}
		}
	}
	
	/**
	 * Before content display.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function onContentBeforeDisplay($context, &$article, &$params)
	{
		$this->_init();
		
		if($this->app->isAdmin() || $this->docType != 'html' || !empty($this->tmpl))
		{
			return;
		}

		if(static::$done)
		{
			return;
		}

		if($this->isExcluded())
		{
			return;
		}
	
		$active = $this->app->getMenu()->getActive();
		$isHome = isset($active->home) && $active->home;
		
		if($context == 'com_content.category' && $this->layout == 'blog' && !$isHome)
		{
			$this->setData('og:type', 'blog');
		}
		elseif($context == 'com_content.article' && !$isHome)
		{
			$this->setData('og:type', 'article');
			
			if($params->get('show_author', 1))
			{
				$this->setData('article:author', $article->author);
			}
			if($params->get('show_category', 1))
			{
				$this->setData('article:section', $article->category_title);
			}
			if($params->get('show_publish_date', 1))
			{
				$this->setData('article:published_time', $article->publish_up);
			}
			if($params->get('show_modify_date', 1))
			{
				$this->setData('article:modified_time', $article->modified);
			}
			if($params->get('fb_publisher_id', ''))
			{
				$this->setData('article:publisher', $params->get('fb_publisher_id', ''));
			}
			if(!empty($article->images) && is_string($article->images))
			{
				$images = new JRegistry;
				$images->loadString($article->images);
				if($images->get('image_fulltext', ''))
				{
					$this->setData('og:image', $this->getImage($images->get('image_fulltext', '')));
				}
				elseif($images->get('image_intro', ''))
				{
					$this->setData('og:image', $this->getImage($images->get('image_intro', '')));
				}
			}
			
			$this->setFallbackTitle($article->title);
			$this->setFallbackDescription($article->introtext);
		}

		static::$done = true;
	}
	
	/**
	* Private function to set meta tags
	* 
	* @param boolean $addContactData 
	* 
	* @return void
	*/
	private function loadMetaData($addContactData = false)
	{
		$params = new JRegistry;
		$params->set('og:type', $this->params->get('og:type'));
		$title = $this->doc->getTitle() ? $this->doc->getTitle() : static::$title;
		$params->set('og:title', $title);
		$params->set('og:url', JUri::current());
		$params->set('og:site_name', $this->params->get('og:site_name', $this->app->getCfg('sitename')));
		$description = $this->doc->getDescription() ? $this->doc->getDescription() : static::$description;
		$params->set('og:description', $description);
		
		$params->merge($this->params);
		
		$temp = new JRegistry;
		$temp->loadArray(static::$data);
		$params->merge($temp);
		
		$type = $params->get('og:type', 'website');
		if(strpos($type, '.'))
		{
			$parts = explode('.', $type);
			$type = $parts[0];
		}
		if(!empty($type) && $this->params->get('add_og_prefix', 1))
		{
			static::$ogPreifx = 'og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# ' . $type . ': http://ogp.me/ns/' . $type . '#';
		}
		
		if($type == 'business')
		{
			$addContactData = true;
		}
		
		// Get data from params
		$data = $params->toArray();
		
		if(empty($data) && !is_array($data))
		{
			return;
		}
		
		foreach($data as $name => $value)
		{
			$value = trim($value);
			
			if(empty($value))
			{
				continue;
			}
			
			if(strpos($name, ':') !== false)
			{
				if($name == 'og:image')
				{
					$image_url = $this->getImage($value);
					$this->setMetaData('og:image', $image_url);
				}
				elseif(strpos($name, 'business:') === 0 || strpos($name, 'place:') === 0)
				{
					if($addContactData) $this->setMetaData($name, $value);
				}
				else
				{
					$this->setMetaData($name, $value);
				}
			}
		}
	}
	
	public function onAfterRender()
	{
		$this->_init();
		
		if($this->app->isAdmin() || $this->docType != 'html' || !empty($this->tmpl))
		{
			return;
		}
		
		if($this->isExcluded())
		{
			return;
		}
		
		$buffer = JResponse::getBody();
		
		if(!empty(static::$ogPreifx))
		{
			if(strpos($buffer, '<head>') !== false)
			{
				$buffer = preg_replace('/<head>/', '<head prefix="' . static::$ogPreifx . '">', $buffer, 1);
			}
			else
			{
				$buffer = preg_replace('/<head /', '<head prefix="' . static::$ogPreifx . '" ', $buffer, 1);
			}
		}
		
		$tags = $this->getMetaTags();
		
		if(!empty($tags))
		{
			$inEnd = $this->doc->_getLineEnd();
			$buffer = preg_replace('/<\/title>/', '</title>' . $inEnd . $tags, $buffer, 1);
		}
		
		JResponse::setBody($buffer);
	}

	/**
	* Private function to set meta tag string
	* 
	* @param  string  $name  Name of the tag
	* @param  string  $value Tag value
	* 
	* @return void
	*/
	private function setMetaData($name, $value)
	{
		$tab   = $this->doc->_getTab();
		self::$metaData[$name] = $tab . '<meta property="' . $name . '" content="' . $value . '" />';
		
		return self::$metaData;
	}
	
	private function getMetaTags()
	{
		if(empty(self::$metaData))
		{
			return '';
		}
		
		$inEnd = $this->doc->_getLineEnd();
		$metaTags = implode($inEnd, self::$metaData);
		
		return $metaTags;
	}
	
	/**
	* Private function to set meta data
	* 
	* @param  string  $name  Name of the tag
	* @param  string  $value Tag value
	* 
	* @return void
	*/
	private function setData($name, $value)
	{
		if(!empty($value))
		{
			static::$data[$name] = $value;
		}
	}

	/**
	* Private function to remove meta data
	* 
	* @param  string  $name  Name of the tag
	* 
	* @return void
	*/
	private function removeData($name)
	{
		if(isset(static::$data[$name]))
		{
			unset(static::$data[$name]);
		}
	}
	
	/**
	* Private function to set fallback title tag
	* 
	* @param  string  $title  Title tag
	* 
	* @return void
	*/
	private function setFallbackTitle($title)
	{
		if(!empty($title))
		{
			static::$title = $title;
		}
	}

	/**
	* Private function to set fallback description tag
	* 
	* @param  string  $description  Description tag
	* 
	* @return void
	*/
	private function setFallbackDescription($description)
	{
		if(!empty($description))
		{
			$description = strip_tags($description);
			$description = str_replace(array(PHP_EOL, "\t"), array(' ', ' '), $description);
			$description = preg_replace('/\s+/', ' ', $description);
			static::$description = $description;
		}
	}

	/**
	* Private function to get image url
	* 
	* @param  string  $url URL of the image
	* 
	* @return string Absolute URL of the image
	*/
	private function getImage($url)
	{
		$url_out = str_replace(' ', '%20', $url);
		$media_path = JPath::clean(JPATH_ROOT . '/' . $url);
		
		if(file_exists($media_path))
		{
			$root = rtrim(JUri::root(), '/');
			$url_out = $root . '/' . $url_out;
		}

		return $url_out;
	}
	
	/**
	* Private method to know if the current view needs to be excluded
	* 
	* @return boolean True if the view needs to be excluded
	*/
	private function isExcluded()
	{
		if(static::$exclude !== null)
		{
			return static::$exclude;
		}
		
		$items            = $this->params->get('excludes', array());
		static::$exclude  = false;
		$options          = array();
		$views            = array();
		$layouts          = array();

		if(!empty($items))
		{
			if(is_string($items))
			{
				if(strpos($items, ','))
				{
					$items = explode(',', $items);
					$items = array_map('trim', $items);
				}
				else
				{
					$items = array($items);
				}
			}
			foreach($items as &$item)
			{
				$item = json_decode(base64_decode($item), true);
				$options[] = $item['option'];
				$views[] = $item['view'];
				$layouts[] = $item['layout'];
			}
		}

		if(in_array($this->option, $options) && in_array($this->view, $views) && in_array($this->layout, $layouts))
		{
			static::$exclude = true;
		}
		
		return static::$exclude;
	}
}