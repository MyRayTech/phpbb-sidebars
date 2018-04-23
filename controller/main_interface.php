<?php

namespace raytech\sidebars\controller;

/**
 * Main portal Interface 
 * 
 * @author Reaper
 */
interface main_interface
{
	public function __construct(\Symfony\Component\DependencyInjection\ContainerInterface $container, \phpbb\db\driver\driver_interface $db, \phpbb\controller\helper $helper, $blocks_table);
	public function display();
}