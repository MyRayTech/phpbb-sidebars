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
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge(
	$lang, array(
		'LOGIN_REQUIRED'	=> 'You must login to see more of this site.',
		'DETAILS'				=> '<a href="http://www.myraytech.net" style="font-weight: bold;">Sidebars</a> &copy; 2015 <strong style="color: #AA0000;">RayTech</strong>',
	)
);
