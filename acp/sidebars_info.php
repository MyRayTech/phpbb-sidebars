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

class sidebars_info
{

	public function module()
	{
		return [
			'filename'	 => '\raytech\sidebars\acp\sidebars_module',
			'title'		 => 'ACP_SIDEBARS',
			'modes'		 => [
				'settings'	 => [
					'title'	 => 'ACP_SIDEBARS_SETTINGS',
					'auth'	 => 'ext_raytech/sidebars && acl_a_board',
					'cat'	 => ['ACP_SIDEBARS']
				],
				'blocks'	 => [
					'title'	 => 'ACP_SIDEBARS_BLOCKS',
					'auth'	 => 'ext_raytech/sidebars && acl_a_board',
					'cat'	 => ['ACP_SIDEBARS'],
				]
			],
		];
	}

}