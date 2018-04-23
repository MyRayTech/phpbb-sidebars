<?php

/**
 *
 * Sidebars extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2015 RayTech <https://www.myraytech.net>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace raytech\sidebars\event;

use phpbb\auth\auth;
use phpbb\config\config;
use phpbb\config\db_text;
use phpbb\db\driver\driver_interface;
use phpbb\controller\helper;
use phpbb\request\request;
use phpbb\template\template;
use phpbb\extension\manager;
use raytech\sidebars\operators\blocks;
use phpbb\user;
use phpbb\exception\http_exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{

	protected $auth;
	protected $config;
	protected $config_text;
	protected $db;
	protected $helper;
	protected $request;
	protected $template;
	protected $ext_mgr;
	protected $container;
	protected $blocks_operator;
	protected $user;

	/** @var \raytech\sidebars\entity\blocks */
	protected $entity;
	protected $blocks_table;

	public function __construct(auth $auth, config $config, db_text $config_text, driver_interface $db, helper $helper, request $request, template $template, manager $ext_manager, ContainerInterface $container, blocks $operator, user $user, $blocks_table)
	{
		$this->auth				 = $auth;
		$this->config			 = $config;
		$this->config_text		 = $config_text;
		$this->db				 = $db;
		$this->helper			 = $helper;
		$this->request			 = $request;
		$this->template			 = $template;
		$this->ext_mgr			 = $ext_manager;
		$this->container		 = $container;
		$this->blocks_operator	 = $operator;
		$this->user				 = $user;
		$this->blocks_table		 = $blocks_table;
		$this->entity			 = $this->container->get('raytech.sidebars.entity');
	}

	/**
	 * Assign functions defined in this class to event listeners in the core
	 *
	 * @return array
	 * @static
	 * @access public
	 */
	static public function getSubscribedEvents()
	{
		return [
			'core.page_header' => 'page_header',
		];
	}

	public function page_header()
	{
		$this->user->add_lang_ext('raytech/sidebars', 'sidebars');

		$left	 = false;
		$right	 = false;
		$blocks	 = null;
		if ((int) $this->config['sidebars_side'] === 2)
		{
			$block_ids = $this->load_blocks_ids();
			
			foreach ($block_ids->data as $data){
				$ids[] = (isset($data['block_id'])) ? (int) $data['block_id'] : 0;
			}
			
			if ($ids !== null)
			{
				$left = true;
				
				if($this->request->server('PHP_SELF') !== '/app.php')
				{
					$right = false;
				}
				else
				{
					$right = true;
				}
			}
			foreach ($ids as $id)
			{
				$block = $this->entity->loadByID($id);
				//left block side
				if ($block->get_side() == '0' && $this->request->server('PHP_SELF') === '/app.php')
				{
					// Enabled?
					if ($block->get_block_display())
					{
						// Display to guests?
						if (!$block->get_block_display_to_guests() && $this->user->data['user_id'] === ANONYMOUS)
						{
							continue;
						}
						
						// Display on index page?
						if (!$block->get_block_display_on_index() && $this->request->server('REQUEST_URI') === '/')
						{
							continue;
						}

						// Loads template if defined
						if ($block->get_template() != 'None')
						{
							$content = $this->read_template($block->get_template());
						}
						elseif($block->content_html_enabled())
						{
							$content = $block->get_raw_content();
						}
						else
						{
							$content = $block->get_content_for_display();
						}

						$this->template->assign_block_vars('rightblock', [
							'NAME'		 => $block->get_name(),
							'CONTENT'	 => html_entity_decode($content),
						]);
					}
				}
				// Right block side
				elseif ($block->get_side() == '1')
				{
					// Enabled?
					if ($block->get_block_display())
					{
						// Display to guests?
						if (!$block->get_block_display_to_guests() && $this->user->data['user_id'] === ANONYMOUS)
						{
							continue;
						}
						
						// Display on index page?
						if (!$block->get_block_display_on_index() && $this->request->server('REQUEST_URI') === '/')
						{
							continue;
						}

						// Loads template if defined
						if ($block->get_template() != 'None')
						{
							$content = $this->read_template($block->get_template());
						}
						elseif($block->content_html_enabled())
						{
							$content = $block->get_raw_content();
						}
						else
						{
							$content = $block->get_content_for_display();
						}
						//var_dump($block->content_html_enabled());
						$this->template->assign_block_vars('leftblock', [
							'NAME'		 => $block->get_name(),
							'CONTENT'	 => html_entity_decode($content),
						]);
					}
				}
			}
		}
		elseif ((int) $this->config['sidebars_side'] === 0)
		{
			$blocks = $this->load_blocks_data(0);
			if ($blocks !== null)
			{
				$left	 = false;
				$right	 = true;
			}
			foreach ($blocks->data as $block)
			{
				if ($block['block_side'] == '0')
				{
					if ($block->get_block_display())
					{
						// Display to guests?
						if (!$block->get_block_display_to_guests() && $this->user->data['user_id'] !== ANONYMOUS)
						{
							continue;
						}

						// Display on index page?
						if (!$block->get_block_display_on_index() && $this->request->server('REQUEST_URI') !== '/')
						{
							continue;
						}
					
						// Loads template if defined
						if ($block->get_template() != 'None')
						{
							$content = $this->read_template($block->get_template());
						}
						elseif($block->content_html_enabled())
						{
							$content = $block->get_raw_content();
						}
						else
						{
							$content = $block->get_content_for_display();
						}

						$this->template->assign_block_vars('rightblock', [
							'NAME'		 => $block['block_name'],
							'CONTENT'	 => html_entity_decode($content),
						]);
					}
				}
			}
		}
		elseif ((int) $this->config['sidebars_side'] === 1)
		{
			$blocks = $this->load_blocks_data(1);
			if ($blocks !== null)
			{
				$left	 = true;
				$right	 = false;
			}
			foreach ($blocks->data as $block)
			{
				if ($block['block_side'] == '1')
				{
					if ($block->get_block_display())
					{
						// Display to guests?
						if (!$block->get_block_display_to_guests() && $this->user->data['user_id'] !== ANONYMOUS)
						{
							continue;
						}

						// Display on index page?
						if (!$block->get_block_display_on_index() && $this->request->server('REQUEST_URI') !== '/')
						{
							continue;
						}

						// Loads template if defined
						if (!empty($block['block_template']) || $block['block_template'] !== 'None')
						{
							$content = $this->read_template($block['block_template']);
						}
						else
						{
							$content = html_entity_decode($block['block_content']);
						}

						$this->template->assign_block_vars('leftblock', [
							'NAME'		 => $block['block_name'],
							'CONTENT'	 => $content,
						]);
					}
				}
			}
		}
		if($this->request->server('SCRIPT_NAME') !== "/app.php") 
		{
			$this->template->assign_var('S_SIDEBAR_FORUM', true);
		}

		$this->template->assign_vars([
			'S_LEFT_SIDEBAR'	 => $left,
			'S_RIGHT_SIDEBAR'	 => $right,
			'S_DISPLAY_BLOCKS'	 => (bool) $this->config['display_sidebars'],
		]);
	}
	public function load_blocks_ids($side = null)
	{
		try
		{
			$this->entity->load_ids();
		} 
		catch (\raytech\sidebars\exception\base $e)
		{
			throw new http_exception(404, 'BLOCK_NOT_AVAILABLE');
		}
		return $this->entity;
	}

	public function load_blocks_data($side = null)
	{
		try
		{
			if ($side)
			{
				$this->entity->loadBySide((int) $side);
			}
			else
			{
				$this->entity->load();
			}
		}
		catch (\raytech\sidebars\exception\base $e)
		{
			throw new http_exception(404, 'BLOCK_NOT_AVAILABLE');
		}

		return $this->entity;
	}

	public function read_template($filename)
	{
		$content = '';
		include(__DIR__ . '/../blocks/' . $filename . '.phtml');

		return $content;
	}

}