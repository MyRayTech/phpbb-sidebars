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

use raytech\sidebars\operators\blocks_interface;
use phpbb\cache\driver\driver_interface as cache;
use phpbb\db\driver\driver_interface as db;
use phpbb\extension\manager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class blocks implements blocks_interface
{
	/** \phpbb\cache\driver\drvier_interface **/
	protected $cache;
	
	/** @var \Symfony\Component\DependencyInjection\ContainerInterface **/
	protected $container;
	
	/** @var \phpbb\db\driver\driver_interface **/
	protected $db;
	
	/** @var \phpbb\extension\manager **/
	protected $ext_manager;
	
	/** @var string **/
	protected $blocks_table;

	
	/**
	 * Constructor
	 * 
	 * @param cache $cache
	 * @param ContainerInterface $container
	 * @param db $db
	 * @param manager $ext_manager
	 * @param string $blocks_table
	 */
	public function __construct(cache $cache, ContainerInterface $container, \phpbb\db\driver\driver_interface $db, manager $ext_manager, $blocks_table)
	{
		$this->cache		 = $cache;
		$this->container	 = $container;
		$this->db			 = $db;
		$this->ext_manager	 = $ext_manager;
		$this->blocks_table	 = $blocks_table;
	}

	public function add_block($entity)
	{
		// Insert the page data to the database
		$entity->insert();

		// Get the newly inserted page's identifier
		$block_id = $entity->get_id();

		// Reload the data to return a fresh block entity
		return $entity->load($block_id);
	}

	public function delete_block($block_id)
	{
		// Remove any existing page link data for this page
		// An exception will be thrown if page identifier is invalid
		// Delete the page from the database
		$sql = 'DELETE FROM ' . $this->blocks_table . '
			WHERE block_id = ' . (int) $block_id;
		$this->db->sql_query($sql);

		// Return true/false if a block was deleted
		return (bool) $this->db->sql_affectedrows();
	}
	

	public function get_blocks()
	{
		$entities = array();

		// Load all page data from the database
		$sql	 = 'SELECT *
			FROM ' . $this->blocks_table .'
			ORDER BY block_side ASC, block_order ASC';
		
		$result	 = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			// Import each page row into an entity
			$entities[] = $this->container->get('raytech.sidebars.entity')->import($row);
		}
		$this->db->sql_freeresult($result);
		// Return all page entities
		return $entities;
	}

	protected function find($path, $prefix, $suffix)
	{
		$finder = $this->ext_manager->get_finder();

		return $finder
						->set_extensions(array('raytech/sidebars'))
						->prefix($prefix)
						->suffix($suffix)
						->core_path("$path/")
						->extension_directory("/$path")
						->find()
		;
	}

	protected function block_id_exists($id)
	{
		$sql	 = 'SELECT 1
			FROM ' . $this->blocks_table . '
			WHERE block_id = ' . (int) $id;
		$result	 = $this->db->sql_query_limit($sql, 1);
		$row	 = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return (bool) $row;
	}
	public function get_blocks_templates()
	{
		return $this->find('blocks', 'blocks_', '.phtml');
	}

	

}