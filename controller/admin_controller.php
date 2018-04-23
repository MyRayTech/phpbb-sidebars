<?php

/**
 *
 * Sidebars extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2015 RayTech <https://www.myraytech.net>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace raytech\sidebars\controller;

use raytech\sidebars\controller\admin_interface;
use raytech\sidebars\helper\main;
use Symfony\Component\DependencyInjection\ContainerInterface;

class admin_controller implements admin_interface
{

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \raytech\sidebars\operators\blocks */
	protected $blocks_operator;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var ContainerInterface */
	protected $phpbb_container;
	
	/** @var \raytech\sidebars\helper\main */
	protected $text_helper;

	/** @var \phpbb\event\dispatcher_interface */
	protected $phpbb_dispatcher;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var string phpEx */
	protected $php_ext;

	/** @var \raytech\sidebars\entity\blocks */
	protected $entity;

	/** @var string Custom form action */
	protected $u_action;

	/**
	 * Constructor
	 *
	 * @param \phpbb\controller\helper             $helper           Controller helper object
	 * @param \phpbb\log\log                       $log              The phpBB log system
	 * @param \phpbb\request\request               $request          Request object
	 * @param \phpbb\template\template             $template         Template object
	 * @param \phpbb\user                          $user             User object
	 * @param ContainerInterface                   $phpbb_container  Service container interface
	 * @param \phpbb\event\dispatcher_interface    $phpbb_dispatcher Event dispatcher
	 * @param string                               $root_path        phpBB root path
	 * @param string                               $php_ext          phpEx
	 * @access public
	 */
	public function __construct(\phpbb\controller\helper $helper, \phpbb\log\log $log, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, ContainerInterface $phpbb_container, \phpbb\event\dispatcher_interface $phpbb_dispatcher, $root_path, $php_ext)
	{
		$this->helper			 = $helper;
		$this->log				 = $log;
		$this->request			 = $request;
		$this->text_helper			 = new main();
		$this->template			 = $template;
		$this->user				 = $user;
		$this->container		 = $phpbb_container;
		$this->dispatcher		 = $phpbb_dispatcher;
		$this->root_path		 = $root_path;
		$this->php_ext			 = $php_ext;
		$this->entity			 = $this->container->get('raytech.sidebars.entity');
		$this->blocks_operator	 = $this->container->get('raytech.sidebars.operator');
		$this->module			 = $this->request->variable('i', '-raytech-sidebars-acp-sidebars_module');
		$this->mode				 = $this->request->variable('mode', 'settings');
		$this->u_action			 = $this->request->variable('action', 'list', true);
	}

	public function add_block()
	{
		// Process the new page
		$this->add_edit_block_data($this->entity);
		
		foreach ($this->template_select() as $filename)
		{
			$this->template->assign_block_vars('templates', [
				'NAME'	 => $filename,
				'DBNAME' => substr($filename, 0, -6),
			]);
		}

		$this->template->assign_vars([
			'U_ACTION'		 => append_sid('?i=' . $this->module . '&mode=' . $this->mode . '&action=' . $this->u_action),
			'S_ADD_BLOCK'	 => true,
			'PAGE_TITLE'	 => $this->user->lang['ACP_SIDEBARS_ADD']
		]);



		// Set output vars for display in the template
		$this->template->assign_vars(array(
			'S_ADD_BLOCK'	 => true,
			'U_ACTION'		 => "{$this->u_action}&amp;action=add",
			'PAGE_TITLE'	 => $this->user->lang['ACP_SIDEBARS_ADD']
		));
	}

	public function delete_block($block_id)
	{
		if(!empty($block_id))
		{
			$this->blocks_operator->delete_block($block_id);
			
			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'ACP_SIDEBARS_BLOCKS_DELETED_LOG', time(), array($block_id));

			trigger_error($this->user->lang('ACP_SIDEBARS_BLOCKS_DELETE_SUCCESS') . adm_back_link(append_sid('?i=' . $this->module . '&mode=' . $this->mode)), E_USER_NOTICE);
		}
	}

	public function display_blocks()
	{
		$blocks = $this->entity->load()->data;
		foreach ($blocks as $block)
		{
			$side = $this->text_helper->side_text($block['block_side']);
			$this->template->assign_block_vars('blocks', [
				'ID'	 => $block['block_id'],
				'NAME'	 => $block['block_name'],
				'ORDER'	 => $block['block_order'],
				'SIDE'	 => $side,
				'LINK'	 => append_sid("?i={$this->module}&mode={$this->mode}"),
			]);
		}
		$this->template->assign_vars([
			'U_ADD_BLOCK' => append_sid('?i=' . $this->module . '&mode=' . $this->mode . '&action=add'),
		]);
	}

	public function edit_block($block_id)
	{
		$this->entity->loadByID($block_id);
		// Process the new page
		$this->add_edit_block_data($this->entity);
		//grab the block
		$block = $this->entity->loadByID($block_id)->data;

			// find the right block
			if ((int) $block['block_id'] === $block_id)
			{	
				$content = $this->entity->get_content_for_edit();

				$this->template->assign_block_vars('block', [
					'ID'			 => $block['block_id'],
					'NAME'			 => $block['block_name'],
					'CONTENT'		 => $content['text'],
					'BBCODE'		 => ($content['allow_bbcode']) ? true : false ,
					'SMILIES'		 => ($content['allow_smilies']) ? true : false,
					'URL'			 => ($content['allow_urls']) ? true : false,
					'HTML'			 => ($block['block_content_allow_html']) ? true : false,
					'SIDE'			 => (bool) $block['block_side'],
					'ORDER'			 => $block['block_order'],
					'DISPLAY'		 => (bool) $block['block_display'],
					'DISPLAY_GUEST'	 => (bool) $block['block_display_to_guest'],
					'DISPLAY_INDEX'	 => (bool) $block['block_display_on_index'],
				]);

				foreach ($this->template_select() as $filename)
				{
					$file = substr($filename, 0, -6);
					if ($block['block_template'] == $file)
					{
						$this->template->assign_block_vars('templates', [
							'NAME'		 => $filename,
							'DBNAME'	 => $file,
							'SELECTED'	 => true,
						]);
					}
					else
					{

						$this->template->assign_block_vars('templates', [
							'NAME'		 => $filename,
							'DBNAME'	 => $file,
							'SELECTED'	 => false,
						]);
					}
				}
			}
		
		$this->template->assign_vars([
			'S_EDIT_BLOCK'	 => true,
			'PAGE_TITLE'	 => $this->user->lang['ACP_SIDEBARS_EDIT'],
			'U_ACTION'		 => "{$this->u_action}&amp;action=edit&amp;id={$block_id}",
		]);
	}

	public function add_edit_block_data($entity)
	{
		
		// Create an array to collect errors that will be output to the user
		$errors = array();

		// Is the form submitted
		$submit = $this->request->is_set_post('submit');

		// Load posting language file for the BBCode editor
		$this->user->add_lang('posting');

		// Add form key for form validation checks
		add_form_key('add_edit_block');

		// Collect form data
		$data					 = array(
			'block_name'				 => $this->request->variable('block_name', '', true),
			'block_content'				 => $this->request->variable('block_content', '', true),
			'bbcode'					 => $this->request->variable('parse_bbcode', false),
			'magic_url'					 => $this->request->variable('parse_magic_url', false),
			'smilies'					 => $this->request->variable('parse_smilies', false),
			'html'						 => $this->request->variable('parse_html', false),
			'block_side'				 => $this->request->variable('block_side', 0),
			'block_order'				 => $this->request->variable('block_order', 0),
			'block_display'				 => $this->request->variable('block_display', 0),
			'block_template'			 => $this->request->variable('block_template', 'blocks_default'),
			'block_display_to_guests'	 => $this->request->variable('block_display_to_guest', 0),
			'block_display_on_index'	 => $this->request->variable('block_display_on_index', 0)
		);
		// Grab the form data's message parsing options (possible values: 1 or 0)
		// If submit use the data from the form
		// If page edit use data stored in the entity
		// If page add use default values
		$content_parse_options	 = array(
			'bbcode'	 => ($submit) ? $data['bbcode'] : (($entity->get_id()) ? $entity->content_bbcode_enabled() : 1),
			'magic_url'	 => ($submit) ? $data['magic_url'] : (($entity->get_id()) ? $entity->content_magic_url_enabled() : 1),
			'smilies'	 => ($submit) ? $data['smilies'] : (($entity->get_id()) ? $entity->content_smilies_enabled() : 1),
			'html'		 => ($submit) ? $data['html'] : (($entity->get_id()) ? $entity->content_html_enabled() : 0),
		);

		// Set the content parse options in the entity
		foreach ($content_parse_options as $function => $enabled)
		{
			call_user_func(array($entity, ($enabled ? 'content_enable_' : 'content_disable_') . $function));
		}

		// Purge temporary variable
		unset($content_parse_options);
		
		// If the form has been submitted, set all data and save it
		if ($submit)
		{
			// Test if the form is valid
			// Use -1 to allow unlimited time to submit form
			if (!check_form_key('add_edit_block', -1))
			{
				$errors[] = $this->user->lang('FORM_INVALID');
			}

			// Map the form's page data fields to setters
			$map_fields = array(
				'set_name'						 => $data['block_name'],
				'set_side'						 => $data['block_side'],
				'set_content'					 => $data['block_content'],
				'set_template'					 => $data['block_template'],
				'set_order'						 => $data['block_order'],
				'set_block_display'				 => $data['block_display'],
				'set_block_display_to_guests'	 => $data['block_display_to_guests'],
				'set_block_display_on_index'	 => $data['block_display_on_index'],
			);
			
			// Set the mapped page data in the entity
			foreach ($map_fields as $entity_function => $block_data)
			{
				try
				{
					// Calling the $entity_function on the entity and passing it $page_data
					call_user_func_array(array($entity, $entity_function), array($block_data));
				}
				catch (\raytech\sidebars\exception\base $e)
				{
					// Catch exceptions and add them to errors array
					$errors[] = $e->get_message($this->user);
				}
			}
			// Purge temporary variable
			unset($map_fields);
			
			// Insert or update page
			if (empty($errors))
			{
				
				if (isset($entity->data['block_id']))
				{
					//var_dump($entity->data);
					// Save the edited page entity to the database
					$entity->save();

					// Log the action
					$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'ACP_SIDEBARS_BLOCKS_EDITED_LOG', time(), array($entity->get_name()));

					// Show user confirmation of the saved page and provide link back to the previous screen
					trigger_error($this->user->lang('ACP_SIDEBARS_BLOCKS_EDIT_SUCCESS') . adm_back_link(append_sid('?i=' . $this->module . '&mode=' . $this->mode)), E_USER_NOTICE);
				}
				else
				{

					// Add the new page entity to the database
					/* @var $entity \raytech\sidebars\entity\blocks */
					$entity = $this->blocks_operator->add_block($entity);

					// Save the page link location data (now that we can access the new id)
					//$this->blocks_operator->insert_page_links($entity->get_id(), $data['page_links']);
					// Log the action
					$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'ACP_SIDEBARS_BLOCKS_ADDED_LOG', time(), array($entity->get_name()));

					// Show user confirmation of the added page and provide link back to the previous screen
					trigger_error($this->user->lang('ACP_SIDEBARS_BLOCKS_ADD_SUCCESS') . adm_back_link(append_sid('?i=' . $this->module . '&mode=' . $this->mode)), E_USER_NOTICE);
				}
			}
			else
			{
				trigger_error($errors[0] . adm_back_link(append_sid('?i=' . $this->module . '&mode=' . $this->mode . '&action=add')), E_USER_NOTICE);
			}
		}

		// Set output vars for display in the template
		$this->template->assign_vars(array(
			'S_ERROR'					 => (sizeof($errors)) ? true : false,
			'ERROR_MSG'					 => (sizeof($errors)) ? implode('<br />', $errors) : '',
			'BLOCK_TITLE'				 => $entity->get_name(),
			'BLOCK_SIDE'				 => $entity->get_side(),
			'BLOCK_CONTENT'				 => $entity->get_content_for_edit(),
			'BLOCK_TEMPLATE'			 => $entity->get_template(),
			'BLOCK_ORDER'				 => $entity->get_order(),
			'S_BLOCKS_DISPLAY'			 => $entity->get_block_display(),
			'S_BLOCKS_GUEST_DISPLAY'	 => $entity->get_block_display_to_guests(),
			'S_BLOCKS_INDEX_DISPLAY'	 => $entity->get_block_display_on_index(),
			'S_PARSE_BBCODE_CHECKED'	 => $entity->content_bbcode_enabled(),
			'S_PARSE_SMILIES_CHECKED'	 => $entity->content_smilies_enabled(),
			'S_PARSE_MAGIC_URL_CHECKED'	 => $entity->content_magic_url_enabled(),
			'S_PARSE_HTML_CHECKED'		 => $entity->content_html_enabled(),
			'BBCODE_STATUS'				 => $this->user->lang('BBCODE_IS_ON', '<a href="' . append_sid("{$this->root_path}faq.{$this->php_ext}", 'mode=bbcode') . '">', '</a>'),
			'SMILIES_STATUS'			 => $this->user->lang('SMILIES_ARE_ON'),
			'IMG_STATUS'				 => $this->user->lang('IMAGES_ARE_ON'),
			'FLASH_STATUS'				 => $this->user->lang('FLASH_IS_ON'),
			'URL_STATUS'				 => $this->user->lang('URL_IS_ON'),
			'S_BBCODE_ALLOWED'			 => true,
			'S_SMILIES_ALLOWED'			 => true,
			'S_BBCODE_IMG'				 => true,
			'S_BBCODE_FLASH'			 => true,
			'S_LINKS_ALLOWED'			 => true,
			'U_BACK'					 => $this->u_action,
		));

//		// Assigning custom bbcodes
//		include_once($this->root_path . 'includes/functions_display.' . $this->php_ext);
//
//		display_custom_bbcodes();
	}

	public function template_select()
	{
		// Grabs filenames in the blocks folder
		$files = scandir(__DIR__ . '/../blocks/');

		// Removes ./, and ../ from the array
		$files_array = array_splice($files, 2);

		return $files_array;
	}

}