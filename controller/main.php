<?php

namespace raytech\sidebars\controller;

use raytech\sidebars\controller\main_interface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use phpbb\controller\helper;
use phpbb\db\driver\driver_interface;

class main implements main_interface
{

	/** @var \Symfony\Component\DependencyInjection\ContainerInterface */
	protected $container;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var string Blocks table name */
	protected $blocks_table;

	/** @var \raytech\sidebars\entity\blocks */
	protected $entity;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	public function __construct(ContainerInterface $container, driver_interface $db, helper $helper, $blocks_table)
	{
		$this->container	 = $container;
		$this->db			 = $db;
		$this->blocks_table	 = $blocks_table;
		$this->helper		 = $helper;
		$this->entity		 = $this->container->get('raytech.sidebars.entity');
		$this->template		 = $this->container->get('template');
		$this->user			 = $this->container->get('user');
		$this->request		 = $this->container->get('request');
	}

	public function display()
	{
		if ($this->user->data['user_id'] == ANONYMOUS)
		{
			if ($request->is_ajax())
			{
				trigger_error('LOGIN_REQUIRED');
			}
			login_box('', $user->lang['LOGIN_REQUIRED']);
		}
		
		$block_ids = $this->entity->loadidsBySide(2);

		foreach ($block_ids->data as $data)
		{
			$ids[] = (isset($data['block_id'])) ? (int) $data['block_id'] : 0;
		}
		
		foreach ($ids as $id)
		{
			$block = $this->entity->loadByID($id);
			
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
				elseif ($block->content_html_enabled())
				{
					$content = $block->get_raw_content();
				}
				else
				{
					$content = $block->get_content_for_display();
				}

				$this->template->assign_block_vars('centerblock', [
					'NAME'		 => $block->get_name(),
					'CONTENT'	 => html_entity_decode($content),
				]);
			}
		}
		return $this->helper->render('portal.html', 'Portal');
	}

	protected function grab_center_blocks()
	{
		$blocks = $this->entity->loadBySide(2);
		return $blocks;
	}

	protected function read_template($filename)
	{
		$content = '';
		include(__DIR__ . '/../blocks/' . $filename . '.phtml');

		return $content;
	}

}