<?php

/**
 *
 * Sidebars extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2015 RayTech <https://www.myraytech.net>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace raytech\sidebars\acp;

use raytech\sidebars\helper\main;

class sidebars_module
{

	/** @var string The currenct action */
	public $u_action;

	/** @var \phpbb\config\config */
	public $new_config = [];

	/** @var string form key */
	public $form_key;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \raytech\sidebars\helper\main Description */
	protected $helper;

	/** @var \raytech\sidebars\entity\blocks */
	protected $blocks;

	/** @var \raytech\sidebars\controller\admin_controller * */
	protected $admin_controller;

	public function main($id, $mode)
	{
		global $phpbb_container, $table_prefix;

		if (!defined('BLOCKS_TABLE'))
		{
			$blocks_table = $table_prefix . 'blocks';
			define('BLOCKS_TABLE', $blocks_table);
		}
		// Initialization
		$this->id				 = $id;
		$this->mode				 = $mode;
		$this->container		 = $phpbb_container;
		$this->helper			 = new main();
		$this->auth				 = $this->container->get('auth');
		$this->config			 = $this->container->get('config');
		$this->db				 = $this->container->get('dbal.conn');
		$this->user				 = $this->container->get('user');
		$this->template			 = $this->container->get('template');
		$this->request			 = $this->container->get('request');
		$this->admin_controller	 = $this->container->get('raytech.sidebars.admin.controller');
		$this->module			 = $this->request->variable('i', '-raytech-sidebars-acp-sidebars_module');
		$this->u_action			 = $this->request->variable('action', 'list', true);
		$submit					 = ($this->request->is_set_post('submit')) ? true : false;
		$this->form_key			 = 'acp_sidebars';

		$this->user->add_lang_ext('raytech/sidebars', 'info_acp_sidebars');
		add_form_key($this->form_key);

		switch ($this->mode)
		{
			case 'blocks':
				$id = $this->request->variable('id', 0);
				switch ($this->u_action)
				{
					case 'add':
						$this->admin_controller->add_block();
						// Set Page Title
						$this->page_title = 'ACP_SIDEBARS_ADD';
						// Output page template file
						$this->tpl_name	 = 'sidebars_blocks_add';
						break;
					case 'edit':
						$this->admin_controller->edit_block($id);
						// Set Page Title
						$this->page_title = 'ACP_SIDEBARS_EDIT';
						$this->tpl_name = 'sidebars_blocks_add';
						break;
					case 'delete':
						if ($this->admin_controller->delete_block($id))
						{
							trigger_error('ACP_SIDEBARS_BLOCK_DELETED' . adm_back_link(append_sid('?i=' . $this->module . '&mode=' . $this->mode . '&action=add')), E_USER_NOTICE);
						}
						break;
					default:
					case 'list':
						$this->admin_controller->display_blocks();

						// Set Page Title
						$this->page_title = 'ACP_SIDEBARS_MAIN';
						// Output page template file
						$this->tpl_name = 'sidebars_blocks';
						break;
				}
				break;

			default:
			case 'settings':
				$display_vars	 = [
					'title'	 => 'ACP_SIDEBARS_MAIN',
					'vars'	 => [
						'legend1'				 => 'ACP_SIDEBARS_SETTINGS',
						'display_sidebars'		 => ['lang' => 'DISPLAY_SIDEBARS', 'validate' => 'bool', 'type' => 'radio:enabled_enabled', 'explain' => false],
						'index_display_sidebars' => ['lang' => 'INDEX_DISPLAY_SIDEBARS', 'validate' => 'bool', 'type' => 'radio:enabled_enabled', 'explain' => true],
						'sidebars_side'			 => ['lang' => 'SIDEBARS_SIDE', 'validate' => 'int', 'type' => 'custom', 'explain' => false, 'method' => 'page_side_selector'],
						'sidebars_html'			 => ['lang' => 'SIDEBARS_HTML', 'validate' => 'bool', 'type' => 'radio:enabled_enabled', 'explain' => false],
						'sidebars_bbcode'		 => ['lang' => 'SIDEBARS_BBCODE', 'validate' => 'bool', 'type' => 'radio:enabled_enabled', 'explain' => false],
						'sidebars_url'			 => ['lang' => 'SIDEBARS_URL', 'validate' => 'bool', 'type' => 'radio:enabled_enabled', 'explain' => false],
						'sidebars_smilies'		 => ['lang' => 'SIDEBARS_SMILIES', 'validate' => 'bool', 'type' => 'radio:enabled_enabled', 'explain' => false],
						'legend2'				 => 'ACP_SUBMIT_CHANGES'
					],
				];
				// Output page template file
				$this->tpl_name	 = 'sidebars';
				break;
		}

		#region Submit
		if ($submit && $this->mode === 'settings')
		{
			$submit = $this->do_submit_stuff($display_vars);

			// If the submit was valid, so still submitted
			if ($submit)
			{
				trigger_error($this->user->lang('CONFIG_UPDATED') . adm_back_link(append_sid('?i=' . $this->module . '&mode=' . $this->mode . '&action=add')), E_USER_NOTICE);
			}
		}
		elseif ($submit && $this->mode === 'blocks')
		{
			$submit = $this->do_submit_blocks();

			if ($submit)
			{
				trigger_error($this->user->lang('CONFIG_UPDATED') . adm_back_link(append_sid('?i=' . $this->module . '&mode=' . $this->mode . '&action=add')), E_USER_NOTICE);
			}
		}
		#endregion

		// Generate Template except for block mode
		if ($this->mode !== 'blocks')
		{
			$this->generate_stuff_for_cfg_template($display_vars);
			// Output page title
			$this->page_title = $this->user->lang($display_vars['title']);
		}
	}

	/**
	 * Abstracted method to do the submit part of the acp. Checks values, saves them
	 * and displays the message.
	 * If error happens, Error is shown and config not saved. (so this method quits and returns false.
	 *
	 * @param array $display_vars The display vars for this acp site
	 * @param array $special_functions Assoziative Array with config values where special functions should run on submit instead of simply save the config value. Array should contain 'config_value' => function ($this) { function code here }, or 'config_value' => null if no function should run.
	 * @return bool Submit valid or not.
	 */
	protected function do_submit_stuff($display_vars, $special_functions = [])
	{
		$this->new_config	 = $this->config;
		$cfg_array			 = ($this->request->is_set('config')) ? $this->request->variable('config', ['' => ''], true) : $this->new_config;
		$error				 = isset($error) ? $error : [];

		validate_config_vars($display_vars['vars'], $cfg_array, $error);

		if (!check_form_key($this->form_key))
		{
			$error[] = $this->user->lang['FORM_INVALID'];
		}

		// Do not write values if there is an error
		if (sizeof($error))
		{
			$submit = false;
			return false;
		}

		// We go through the display_vars to make sure no one is trying to set variables he/she is not allowed to...
		foreach ($display_vars['vars'] as $config_name => $null)
		{
			// We want to skip that, or run the function. (We do this before checking if there is a request value set for it,
			// cause maybe we want to run a function anyway, based on whatever. We can check stuff manually inside this function)
			if (is_array($special_functions) && array_key_exists($config_name, $special_functions))
			{
				$func = $special_functions[$config_name];
				if (isset($func) && is_callable($func))
				{
					$func();
				}
				continue;
			}
			if (!isset($cfg_array[$config_name]) || strpos($config_name, 'legend') !== false)
			{
				continue;
			}
			// Sets the config value
			$this->new_config[$config_name] = $cfg_array[$config_name];
			$this->config->set($config_name, $cfg_array[$config_name]);
		}
		return true;
	}

	protected function do_submit_blocks()
	{

		return true;
	}

	/**
	 * Abstracted method to generate acp configuration pages out of a list of display vars, using
	 * the function build_cfg_template().
	 * Build configuration template for acp configuration pages
	 *
	 * @param array $display_vars The display vars for this acp site
	 */
	protected function generate_stuff_for_cfg_template($display_vars)
	{
		$this->new_config	 = $this->config;
		$cfg_array			 = ($this->request->is_set('config')) ? $this->request->variable('config', ['' => ''], true) : $this->new_config;
		$error				 = isset($error) ? $error : [];

		validate_config_vars($display_vars['vars'], $cfg_array, $error);

		foreach ($display_vars['vars'] as $config_key => $vars)
		{
			if (!is_array($vars) && strpos($config_key, 'legend') === false)
			{
				continue;
			}

			if (strpos($config_key, 'legend') !== false)
			{
				$this->template->assign_block_vars('options', [
					'S_LEGEND'	 => true,
					'LEGEND'	 => (isset($this->user->lang[$vars])) ? $this->user->lang[$vars] : $vars]
				);

				continue;
			}

			$type = explode(':', $vars['type']);

			$l_explain = '';
			if ($vars['explain'] && isset($vars['lang_explain']))
			{
				$l_explain = (isset($this->user->lang[$vars['lang_explain']])) ? $this->user->lang[$vars['lang_explain']] : $vars['lang_explain'];
			}
			else if ($vars['explain'])
			{
				$l_explain = (isset($this->user->lang[$vars['lang'] . '_EXPLAIN'])) ? $this->user->lang[$vars['lang'] . '_EXPLAIN'] : '';
			}

			$content = build_cfg_template($type, $config_key, $this->new_config, $config_key, $vars);

			if (empty($content))
			{
				continue;
			}

			$this->template->assign_block_vars('options', [
				'KEY'			 => $config_key,
				'TITLE'			 => (isset($this->user->lang[$vars['lang']])) ? $this->user->lang[$vars['lang']] : $vars['lang'],
				'S_EXPLAIN'		 => $vars['explain'],
				'TITLE_EXPLAIN'	 => $l_explain,
				'CONTENT'		 => $content,
			]);

			//unset($display_vars['vars'][$config_key]);
		}

		$this->template->assign_vars([
			'S_ERROR'	 => (sizeof($error)) ? true : false,
			'ERROR_MSG'	 => implode('<br />', $error),
			'U_ACTION'	 => $this->u_action]
		);
	}

	public function side_selector()
	{
		$content = '<select id="block_side" name="block_side">'
				. '<option value="0">Right</option>'
				. '<option value="1">Left</option>'
				. '<option value="2">Center</option>'
				. '</select>';
		return $content;
	}

	public function page_side_selector()
	{
		$content = '<select id="sidebars_side" name="config[sidebars_side]">'
				. '<option value="2">Both</option>'
				. '<option value="0">Just Right</option>'
				. '<option value="1">Just Left</option>'
				. '</select>';
		return $content;
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