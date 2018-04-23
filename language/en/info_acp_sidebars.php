<?php

/**
 *
 * Sidebars extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2015 RayTech <https://www.myraytech.net>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */
/**
 * DO NOT CHANGE
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge(
		$lang, array(
	'ACP_SIDEBARS'						 => 'Side Bars',
	'ACP_SIDEBARS_MAIN'					 => 'Side Bars extension',
	'ACP_SIDEBARS_SETTINGS'				 => 'Settings',
	'ACP_SIDEBARS_TITLE'				 => 'Side Bars',
	'ACP_SIDEBARS_TITLE_EXPLAIN'		 => 'This extension adds sidebars to each side of the forums, You may limit this to one side or the other.',
	'ACP_SIDEBARS_BLOCKS'				 => 'Manage blocks',
	'ACP_SIDEBARS_BLOCKS_EDIT_SUCCESS'	 => 'Edited the block successfully',
	'ACP_SIDEBARS_BLOCKS_ADD_SUCCESS'	 => 'Added a block successfully',
	'ACP_SIDEBARS_BLOCKS_ADDED_LOG'		 => 'Successful Block Add',
	'ACP_SIDEBARS_BLOCKS_EDITED_LOG'	 => 'Successful Block Edit',
	'ACP_SIDEBARS_ADD'					 => 'Add a block',
	'ACP_SIDEBARS_EDIT'					 => 'Edit a block',
	'DISPLAY_SIDEBARS'					 => 'Display Side Bars?',
	'INDEX_DISPLAY_SIDEBARS'			 => 'Display the left side bar on forums',
	'SIDEBARS_SIDE'						 => 'Choose a Side or "Both"',
	'SIDEBARS_HTML'						 => 'Allow HTML?',
	'SIDEBARS_BBCODE'					 => 'Allow BBCode?',
	'SIDEBARS_URL'						 => 'Allow URL\'s?',
	'SIDEBARS_SMILIES'					 => 'Allow Smilies?',
	'BLOCK_LIST'						 => 'List of blocks',
	'BLOCK_NAME'						 => 'Block Title',
	'BLOCK_CONTENT'						 => 'Insert Your Content',
	'BLOCK_ORDER'						 => 'Specify The Display Order',
	'BLOCK_DISPLAY'						 => 'Should this Block be Shown to the public?',
	'BLOCK_SIDE'						 => 'Location of Block',
	'BLOCK_DISPLAY_GUEST'				 => 'Display to guest',
	'BLOCK_DISPLAY_GUEST_EXPLAIN'		 => 'Should Guests be able to see it?.',
	'BLOCK_DISPLAY_INDEX'				 => 'Should this be displayed on the portal page?',
	'PARSE_HTML'						 => 'Parse HTML',
		)
);
