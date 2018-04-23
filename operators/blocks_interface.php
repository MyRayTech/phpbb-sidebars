<?php

/**
 *
 * Sidebars extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2015 RayTech <https://www.myraytech.net>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */
namespace raytech\sidebars\operators;

interface blocks_interface {
	
	/**
	 * Constructor
	 * 
	 * @param type $cache
	 * @param type $container
	 * @param type $db
	 * @param type $ext_manager
	 * @param type $blocks_table
	 */
	public function __construct(\phpbb\cache\driver\driver_interface $cache, \Symfony\Component\DependencyInjection\ContainerInterface $container, \phpbb\db\driver\driver_interface $db, \phpbb\extension\manager $ext_manager, $blocks_table);
	
	/**
	 * Add a block
	 * 
	 * @param object $entity Block entity with data to insert
	 * @access public
	 * @return block_interface Added block entry
	 */
	public function add_block($entity);
	
	/**
	 * Delete a Block
	 * 
	 * @param int $block_id The block identifier to delete
	 */
	public function delete_block($block_id);

}