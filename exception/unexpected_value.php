<?php
/**
*
* Sidebars extension for the phpBB Forum Software package.
*
* @copyright (c) 2015 RayTech <https://www.myraytech.net>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace raytech\sidebars\exception;

/**
* UnexpectedValue exception
*/
class unexpected_value extends base
{
	/**
	* Translate this exception
	*
	* @param \phpbb\user $user
	* @return string
	* @access public
	*/
	public function get_message(\phpbb\user $user)
	{
		return $this->translate_portions($user, $this->message_full, 'EXCEPTION_UNEXPECTED_VALUE');
	}
}
