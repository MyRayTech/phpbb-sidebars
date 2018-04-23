<?php
/**
*
* Pages extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace raytech\sidebars\controller;

/**
* Interface for our admin controller
*
* This describes all of the methods we'll use for the admin front-end of this extension
*/
interface admin_interface
{
	/**
	* Display the pages
	*
	* @return null
	* @access public
	*/
	public function display_blocks();

	/**
	* Add a page
	*
	* @return null
	* @access public
	*/
	public function add_block();

	/**
	* Edit a page
	*
	* @param int $block_id The page identifier to edit
	* @return null
	* @access public
	*/
	public function edit_block($block_id);

	/**
	* Delete a page
	*
	* @param int $block_id The page identifier to delete
	* @return null
	* @access public
	*/
	public function delete_block($block_id);
}
