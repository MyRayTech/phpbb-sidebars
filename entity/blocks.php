<?php

/**
 *
 * Sidebars extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2015 RayTech <https://www.myraytech.net>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace raytech\sidebars\entity;

use raytech\sidebars\entity\blocks_interface;
use phpbb\db\driver\driver_interface;
use phpbb\config\config;
use phpbb\event\dispatcher_interface;

/**
 * Entity for a block
 */
class blocks implements blocks_interface
{

	/**
	 * Data for this entity
	 *
	 * @var array
	 * 	block_id
	 * 	block_name
	 * 	block_order
	 * 	block_content
	 * 	block_content_bbcode_uid
	 * 	block_content_bbcode_bitfield
	 * 	block_content_bbcode_options
	 * 	block_content_allow_html
	 * 	block_display
	 *  block_display_on_index
	 * 	block_display_to_guests
	 * @access public
	 */
	public $data;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\event\dispatcher_interface */
	protected $phpbb_dispatcher;

	/**
	 * The database table the block data is stored in
	 *
	 * @var string
	 */
	protected $blocks_table;

	/**
	 * Constructor
	 *
	 * @param \phpbb\db\driver\driver_interface   $db                    Database object
	 * @param \phpbb\config\config                $config                Config object
	 * @param \phpbb\event\dispatcher_interface   $phpbb_dispatcher      Event dispatcher
	 * @param string                              $blocks_table           Name of the table used to store block data
	 * @access public
	 */
	public function __construct(driver_interface $db, config $config, dispatcher_interface $phpbb_dispatcher, $blocks_table)
	{
		$this->db			 = $db;
		$this->config		 = $config;
		$this->dispatcher	 = $phpbb_dispatcher;
		$this->blocks_table	 = $blocks_table;
	}

	/**
	 * Load the id's from the database for all the blocks
	 *
	 * @param int $id block identifier
	 * @param int $side Side identifier
	 * @return blocks_interface $this object for chaining calls; load()->set()->save()
	 * @access public
	 * @throws \raytech\sidebars\exception\out_of_bounds
	 */
	public function load_ids()
	{
	
		// Get block_id from the database
		$sql		 = 'SELECT block_id
			FROM ' . $this->blocks_table
				. ' ORDER BY block_side ASC, block_order ASC';
		$result		 = $this->db->sql_query($sql);
		$this->data	 = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);
		
		if ($this->data === false)
		{
			// The page does not exist
			throw new \raytech\sidebars\exception\out_of_bounds('block_id');
		}

		return $this;
	}
	/**
	 * Load the data from the database for a page
	 *
	 * @param int $id block identifier
	 * @param int $side Side identifier
	 * @return blocks_interface $this object for chaining calls; load()->set()->save()
	 * @access public
	 * @throws \raytech\sidebars\exception\out_of_bounds
	 */
	public function load()
	{
		
	
		// Get page from the database
		$sql		 = 'SELECT *
			FROM ' . $this->blocks_table
				. ' ORDER BY block_side ASC, block_order ASC';
		$result		 = $this->db->sql_query($sql);
		$this->data	 = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);
		
		if ($this->data === false)
		{
			// The page does not exist
			throw new \raytech\sidebars\exception\out_of_bounds('block_id');
		}

		return $this;
	}
	/**
	 * Load the data from the database for a page
	 *
	 * @param int $id block identifier
	 * @return blocks_interface $this object for chaining calls; load()->set()->save()
	 * @access public
	 * @throws \raytech\sidebars\exception\out_of_bounds
	 */
	public function loadByID($id)
	{

		// Get page from the database
		$sql		 = 'SELECT *
			FROM ' . $this->blocks_table . 
			' WHERE block_id = ' . $id;
		$result		 = $this->db->sql_query($sql);
		$this->data	 = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		
		if ($this->data === false)
		{
			// The page does not exist
			throw new \raytech\sidebars\exception\out_of_bounds('block_id');
		}

		return $this;
	}
	/**
	 * Load the data from the database for a side
	 *
	 * @param int $side Side identifier
	 * @return blocks_interface $this object for chaining calls; load()->set()->save()
	 * @access public
	 * @throws \raytech\sidebars\exception\out_of_bounds
	 */
	public function loadBySide($side)
	{

		// Get page from the database
		$sql		 = 'SELECT *
			FROM ' . $this->blocks_table . 
			' WHERE block_side = ' . $side;
		$result		 = $this->db->sql_query($sql);
		$this->data	 = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);
		
		if ($this->data === false)
		{
			// The page does not exist
			throw new \raytech\sidebars\exception\out_of_bounds('block_id');
		}

		return $this;
	}
	/**
	 * Load the data from the database for a side
	 *
	 * @param int $side Side identifier
	 * @return blocks_interface $this object for chaining calls; load()->set()->save()
	 * @access public
	 * @throws \raytech\sidebars\exception\out_of_bounds
	 */
	public function loadidsBySide($side)
	{

		// Get block_id from the database
		$sql		 = 'SELECT block_id
			FROM ' . $this->blocks_table . 
			' WHERE block_side = ' . $side;
		$result		 = $this->db->sql_query($sql);
		$this->data	 = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);
		
		if ($this->data === false)
		{
			// The block data does not exist
			throw new \raytech\sidebars\exception\out_of_bounds('block_id');
		}

		return $this;
	}

	/**
	 * Import data for a page
	 *
	 * Used when the data is already loaded externally.
	 * Any existing data on this page is over-written.
	 * All data is validated and an exception is thrown if any data is invalid.
	 *
	 * @param array $data Data array, typically from the database
	 * @return page_interface $this object for chaining calls; load()->set()->save()
	 * @access public
	 * @throws \phpbb\pages\exception\base
	 */
	public function import($data)
	{
		// Clear out any saved data
		$this->data = array();

		// All of our fields
		$fields = array(
			// column						=> data type (see settype())
			'block_id'						 => 'integer',
			'block_order'					 => 'set_order', // call set_order()
			'block_name'					 => 'set_name', // call set_name()
			'block_template'				 => 'set_template', // call set_template()
			'block_display'					 => 'set_block_display', // call set_block_display()
			'block_display_on_index'		 => 'set_block_disply_on_index', // call set_block_display_on_index()
			'block_display_to_guests'		 => 'set_block_display_to_guests', // call set_block_display_to_guests()
			// We do not pass to set_content() as generate_text_for_storage would run twice
			'block_content'					 => 'string',
			'block_content_bbcode_uid'		 => 'string',
			'block_content_bbcode_bitfield'	 => 'string',
			'block_content_bbcode_options'	 => 'integer',
			'block_content_allow_html'		 => 'bool',
		);

		// Go through the basic fields and set them to our data array
		foreach ($fields as $field => $type)
		{
			// If the data wasn't sent to us, throw an exception
			if (!isset($data[$field]))
			{
				throw new \raytech\sidebars\exception\invalid_argument(array($field, 'FIELD_MISSING'));
			}

			// If the type is a method on this class, call it
			if (method_exists($this, $type))
			{
				$this->$type($data[$field]);
			}
			else
			{
				// settype passes values by reference
				$value = $data[$field];

				// We're using settype to enforce data types
				settype($value, $type);

				$this->data[$field] = $value;
			}
		}

		// Some fields must be unsigned (>= 0)
		$validate_unsigned = array(
			'block_id',
			'block_content_bbcode_options',
		);

		foreach ($validate_unsigned as $field)
		{
			// If the data is less than 0, it's not unsigned and we'll throw an exception
			if ($this->data[$field] < 0)
			{
				throw new \raytech\sidebars\exception\out_of_bounds($field);
			}
		}

		return $this;
	}

	/**
	 * Insert the page data for the first time
	 *
	 * Will throw an exception if the page was already inserted (call save() instead)
	 *
	 * @return page_interface $this object for chaining calls; load()->set()->save()
	 * @access public
	 * @throws \phpbb\pages\exception\out_of_bounds
	 */
	public function insert()
	{
		if (!empty($this->data['block_id']))
		{
			trigger_error('Block ID is set. Insert did not occur.' . adm_back_link());
			// The block already exists
			// throw new \raytech\sidebars\exception\out_of_bounds('block_id');
		}
		
		// Insert the page data to the database
		$sql = 'INSERT INTO ' . $this->blocks_table . ' ' . $this->db->sql_build_array('INSERT', $this->data);
		$this->db->sql_query($sql);

		// Set the page_id using the id created by the SQL insert
		$this->data['block_id'] = (int) $this->db->sql_nextid();

		return $this;
	}

	/**
	 * Save the current settings to the database
	 *
	 * This must be called before closing or any changes will not be saved!
	 * If adding a page (saving for the first time), you must call insert() or an exeception will be thrown
	 *
	 * @return page_interface $this object for chaining calls; load()->set()->save()
	 * @access public
	 * @throws \phpbb\pages\exception\out_of_bounds
	 */
	public function save()
	{
		if (empty($this->data['block_id']))
		{
			// The block does not exist
			throw new \raytech\sidebars\exception\out_of_bounds('block_id');
		}
		$sql = 'UPDATE ' . $this->blocks_table . '
			SET ' . $this->db->sql_build_array('UPDATE', $this->data) . '
			WHERE block_id = ' . $this->data['block_id'];
		$this->db->sql_query($sql);
		return $this;
	}

	/**
	 * Get id
	 *
	 * @return int Page identifier
	 * @access public
	 */
	public function get_id()
	{
		return (isset($this->data['block_id'])) ? (int) $this->data['block_id'] : 0;
	}

	/**
	 * Get title
	 *
	 * @return string Title
	 * @access public
	 */
	public function get_name()
	{
		return (isset($this->data['block_name'])) ? (string) $this->data['block_name'] : '';
	}
	/**
	 * Get raw content
	 *
	 * @return string Content
	 * @access public
	 */
	public function get_raw_content()
	{
		return (isset($this->data['block_content'])) ? (string) $this->data['block_content'] : '';
	}

	/**
	 * Set name
	 *
	 * @param string $name
	 * @return blocks_interface $this object for chaining calls; load()->set()->save()
	 * @access public
	 * @throws \raytech\sidebars\exception\unexpected_value
	 */
	public function set_name($name)
	{
		// Enforce a string
		$name = (string) $name;

		// Title is a required field
		if ($name == '')
		{
			throw new \raytech\sidebars\exception\unexpected_value(array('name', 'FIELD_MISSING'));
		}

		// We limit the title length to 200 characters
		if (truncate_string($name, 200) != $name)
		{
			throw new \raytech\sidebars\exception\unexpected_value(array('name', 'TOO_LONG'));
		}

		// Set the title on our data array
		$this->data['block_name'] = $name;

		return $this;
	}

	/**
	 * Get side
	 *
	 * @return int order
	 * @access public
	 */
	public function get_side()
	{
		return (isset($this->data['block_side'])) ? (int) $this->data['block_side'] : 0;
	}

	/**
	 * Set side
	 *
	 * @param int $side Page sort order
	 * @return page_interface $this object for chaining calls; load()->set()->save()
	 * @access public
	 * @throws \phpbb\pages\exception\out_of_bounds
	 */
	public function set_side($side)
	{
		// Enforce an integer
		$side = (int) $side;

		/*
		 * If the data is out of range we'll throw an exception. We use 16777215 as a
		 * maximum because it matches the MySQL unsigned mediumint maximum value which
		 * is the lowest amongst the DBMS supported by phpBB.
		 */
		if ($side < 0 || $side > 2)
		{
			throw new \raytech\sidebars\exception\out_of_bounds('block_side');
		}

		// Set the route on our data array
		$this->data['block_side'] = $side;

		return $this;
	}

	/**
	 * Get order
	 *
	 * @return int order
	 * @access public
	 */
	public function get_order()
	{
		return (isset($this->data['block_order'])) ? (int) $this->data['block_order'] : 0;
	}

	/**
	 * Set order
	 *
	 * @param int $order Page sort order
	 * @return page_interface $this object for chaining calls; load()->set()->save()
	 * @access public
	 * @throws \phpbb\pages\exception\out_of_bounds
	 */
	public function set_order($order)
	{
		// Enforce an integer
		$order = (int) $order;

		/*
		 * If the data is out of range we'll throw an exception. We use 16777215 as a
		 * maximum because it matches the MySQL unsigned mediumint maximum value which
		 * is the lowest amongst the DBMS supported by phpBB.
		 */
		if ($order < 0 || $order > 16777215)
		{
			throw new \raytech\sidebars\exception\out_of_bounds('block_order');
		}

		// Set the route on our data array
		$this->data['block_order'] = $order;

		return $this;
	}

	/**
	 * Get page template
	 *
	 * @return string page template
	 * @access public
	 */
	public function get_template()
	{
		return (!empty($this->data['block_template'])) ? (string) $this->data['block_template'] : 'None';
	}

	/**
	 * Set template
	 *
	 * @param int $template Page sort order
	 * @return block_interface $this object for chaining calls; load()->set()->save()
	 * @access public
	 * @throws \phpbb\pages\exception\out_of_bounds
	 */
	public function set_template($template)
	{
		// Enforce an integer
		$template = (string) $template;


		// We limit the title length to 60 characters
		if (truncate_string($template, 60) != $template)
		{
			throw new \raytech\sidebars\exception\unexpected_value(array('template', 'TOO_LONG'));
		}

		// Set the route on our data array
		$this->data['block_template'] = $template;

		return $this;
	}

	/**
	 * Set page template
	 *
	 * @param string $template Page template name
	 * @return page_interface $this object for chaining calls; load()->set()->save()
	 * @access public
	 * @throws \phpbb\pages\exception\unexpected_value
	 */

	/**
	 * Get content for edit
	 *
	 * @return string
	 * @access public
	 */
	public function get_content_for_edit()
	{
		// Use defaults if these haven't been set yet
		$content = (isset($this->data['block_content'])) ? $this->data['block_content'] : '';
		$uid	 = (isset($this->data['block_content_bbcode_uid'])) ? $this->data['block_content_bbcode_uid'] : '';
		$options = (isset($this->data['block_content_bbcode_options'])) ? (int) $this->data['block_content_bbcode_options'] : 0;

		// Generate for edit
		$content_data = generate_text_for_edit($content, $uid, $options);
		
		return $content_data;
	}

	/**
	 * Get content for display
	 *
	 * @param bool $censor_text True to censor the text (Default: true)
	 * @return string
	 * @access public
	 */
	public function get_content_for_display($censor_text = true)
	{
		// If these haven't been set yet; use defaults
		$content	 = (isset($this->data['block_content'])) ? $this->data['block_content'] : '';
		$uid		 = (isset($this->data['block_content_bbcode_uid'])) ? $this->data['block_content_bbcode_uid'] : '';
		$bitfield	 = (isset($this->data['block_content_bbcode_bitfield'])) ? $this->data['block_content_bbcode_bitfield'] : '';
		$options	 = (isset($this->data['block_content_bbcode_options'])) ? (int) $this->data['block_content_bbcode_options'] : 0;

		// Generate for display
		if ($this->content_html_enabled())
		{
			// This is required by s9e text formatter to
			// remove extra xml formatting from the content.
			$content = html_entity_decode($content, ENT_COMPAT);
		}
		else
		{
			$content = generate_text_for_display($content, $uid, $bitfield, $options, $censor_text);
		}
		return $content;
	}

	/**
	 * Set content
	 *
	 * @param string $content
	 * @return page_interface $this object for chaining calls; load()->set()->save()
	 * @access public
	 */
	public function set_content($content)
	{
		// Override maximum post characters limit
		$this->config['max_post_chars'] = 0;

		// Prepare the text for storage
		$uid		 = $bitfield	 = $flags		 = '';
		generate_text_for_storage($content, $uid, $bitfield, $flags, $this->content_bbcode_enabled(), $this->content_magic_url_enabled(), $this->content_smilies_enabled());

		// Set the content to our data array
		$this->data['block_content']				 = $content;
		$this->data['block_content_bbcode_uid']		 = $uid;
		$this->data['block_content_bbcode_bitfield'] = $bitfield;
		// Flags are already set

		return $this;
	}

	/**
	 * Check if bbcode is enabled on the content
	 *
	 * @return bool
	 * @access public
	 */
	public function content_bbcode_enabled()
	{
		return ($this->data['block_content_bbcode_options'] & OPTION_FLAG_BBCODE);
	}

	/**
	 * Enable bbcode on the content
	 * This should be called before set_content(); content_enable_bbcode()->set_content()
	 *
	 * @return page_interface $this object for chaining calls; load()->set()->save()
	 * @access public
	 */
	public function content_enable_bbcode()
	{
		$this->set_content_option(OPTION_FLAG_BBCODE);

		return $this;
	}

	/**
	 * Disable bbcode on the content
	 * This should be called before set_content(); content_disable_bbcode()->set_content()
	 *
	 * @return page_interface $this object for chaining calls; load()->set()->save()
	 * @access public
	 */
	public function content_disable_bbcode()
	{
		$this->set_content_option(OPTION_FLAG_BBCODE, true);

		return $this;
	}

	/**
	 * Check if magic_url is enabled on the content
	 *
	 * @return bool
	 * @access public
	 */
	public function content_magic_url_enabled()
	{
		return ($this->data['block_content_bbcode_options'] & OPTION_FLAG_LINKS);
	}

	/**
	 * Enable magic url on the content
	 * This should be called before set_content(); content_enable_magic_url()->set_content()
	 *
	 * @return page_interface $this object for chaining calls; load()->set()->save()
	 * @access public
	 */
	public function content_enable_magic_url()
	{
		$this->set_content_option(OPTION_FLAG_LINKS);

		return $this;
	}

	/**
	 * Disable magic url on the content
	 * This should be called before set_content(); content_disable_magic_url()->set_content()
	 *
	 * @return page_interface $this object for chaining calls; load()->set()->save()
	 * @access public
	 */
	public function content_disable_magic_url()
	{
		$this->set_content_option(OPTION_FLAG_LINKS, true);

		return $this;
	}

	/**
	 * Check if smilies are enabled on the content
	 *
	 * @return bool
	 * @access public
	 */
	public function content_smilies_enabled()
	{
		return ($this->data['block_content_bbcode_options'] & OPTION_FLAG_SMILIES);
	}

	/**
	 * Enable smilies on the content
	 * This should be called before set_content(); content_enable_smilies()->set_content()
	 *
	 * @return page_interface $this object for chaining calls; load()->set()->save()
	 * @access public
	 */
	public function content_enable_smilies()
	{
		$this->set_content_option(OPTION_FLAG_SMILIES);

		return $this;
	}

	/**
	 * Disable smilies on the content
	 * This should be called before set_content(); content_disable_smilies()->set_content()
	 *
	 * @return page_interface $this object for chaining calls; load()->set()->save()
	 * @access public
	 */
	public function content_disable_smilies()
	{
		$this->set_content_option(OPTION_FLAG_SMILIES, true);

		return $this;
	}

	/**
	 * Check if HTML is allowed on the content
	 *
	 * @return bool allow html
	 * @access public
	 */
	public function content_html_enabled()
	{
		return (isset($this->data['block_content_allow_html'])) ? (bool) $this->data['block_content_allow_html'] : false;
	}

	/**
	 * Enable HTML on the content
	 * This should be called before set_content(); content_enable_html()->set_content()
	 * This should also be called after the bbcode, smilies and magic url setters
	 *
	 * @return page_interface $this object for chaining calls; load()->set()->save()
	 * @access public
	 */
	public function content_enable_html()
	{
		// Disable bbcode, magic url and smiley flags
		$this->content_disable_bbcode()
				->content_disable_smilies()
				->content_disable_magic_url();

		$this->data['block_content_allow_html'] = true;

		return $this;
	}

	/**
	 * Disable HTML on the content
	 * This should be called before set_content(); content_disable_html()->set_content()
	 *
	 * @return page_interface $this object for chaining calls; load()->set()->save()
	 * @access public
	 */
	public function content_disable_html()
	{
		$this->data['block_content_allow_html'] = false;

		return $this;
	}

	/**
	 * Get page display setting
	 *
	 * @return bool display page
	 * @access public
	 */
	public function get_block_display()
	{
		return (isset($this->data['block_display'])) ? (bool) $this->data['block_display'] : false;
	}

	/**
	 * Set page display setting
	 *
	 * @param bool $option Page display setting
	 * @return page_interface $this object for chaining calls; load()->set()->save()
	 * @access public
	 */
	public function set_block_display($option)
	{
		// Enforce boolean
		$option = (bool) $option;

		// Set the route on our data array
		$this->data['block_display'] = $option;

		return $this;
	}

	/**
	 * Get page display to guests setting
	 *
	 * @return bool display page to guests
	 * @access public
	 */
	public function get_block_display_to_guests()
	{
		return (isset($this->data['block_display_to_guest'])) ? (bool) $this->data['block_display_to_guest'] : false;
	}

	/**
	 * Set page display to guests setting
	 *
	 * @param bool $option Page display to guests setting
	 * @return page_interface $this object for chaining calls; load()->set()->save()
	 * @access public
	 */
	public function set_block_display_to_guests($option)
	{
		// Enforce boolean
		$option = (bool) $option;

		// Set the route on our data array
		$this->data['block_display_to_guest'] = $option;

		return $this;
	}

	/**
	 * Get page display to guests setting
	 *
	 * @return bool display page to guests
	 * @access public
	 */
	public function get_block_display_on_index()
	{
		return (isset($this->data['block_display_on_index'])) ? (bool) $this->data['block_display_on_index'] : false;
	}

	/**
	 * Set page display to guests setting
	 *
	 * @param bool $option Page display to guests setting
	 * @return page_interface $this object for chaining calls; load()->set()->save()
	 * @access public
	 */
	public function set_block_display_on_index($option)
	{
		// Enforce boolean
		$option = (bool) $option;

		// Set the route on our data array
		$this->data['block_display_on_index'] = $option;

		return $this;
	}

	/**
	 * Set option helper
	 *
	 * @param int $option_value Value of the option
	 * @param bool $negate Negate (unset) option (Default: False)
	 * @param bool $reparse_content Reparse the content after setting option (Default: True)
	 * @return null
	 * @access protected
	 */
	protected function set_content_option($option_value, $negate = false, $reparse_content = true)
	{
		// Set page_content_bbcode_options to 0 if it does not yet exist
		$this->data['block_content_bbcode_options'] = (isset($this->data['block_content_bbcode_options'])) ? $this->data['block_content_bbcode_options'] : 0;

		// If we're setting the option and the option is not already set
		if (!$negate && !($this->data['block_content_bbcode_options'] & $option_value))
		{
			// Add the option to the options
			$this->data['block_content_bbcode_options'] += $option_value;
		}

		// If we're unsetting the option and the option is already set
		if ($negate && $this->data['block_content_bbcode_options'] & $option_value)
		{
			// Subtract the option from the options
			$this->data['block_content_bbcode_options'] -= $option_value;
		}

		// Reparse the content
		if ($reparse_content && !empty($this->data['block_content']))
		{
			$content = $this->data['block_content'];

			decode_message($content, $this->data['block_content_bbcode_uid']);

			$this->set_content($content);
		}
	}

}