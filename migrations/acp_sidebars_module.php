<?php

/**
 *
 * Sidebars extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2015 RayTech <https://www.myraytech.net>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace raytech\sidebars\migrations;

use phpbb\db\migration\migration;

class acp_sidebars_module extends migration
{
	public function update_data()
	{
		return array(
			array('module.add', array(
					'acp',
					'ACP_CAT_DOT_MODS',
					'ACP_SIDEBARS')),
			array('module.add', array(
					'acp',
					'ACP_SIDEBARS',
				array(
						
						'module_basename'	=> '\raytech\sidebars\acp\sidebars_module',
						'modes'				=> array('settings', 'blocks'),
					),
				)
			),
		);
	}
}
