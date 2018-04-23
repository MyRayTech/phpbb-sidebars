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

class acp_settings extends \phpbb\db\migration\migration
{
	public function update_data()
	{
		return [
			['config.add', ['display_sidebars', '1']],
			['config.add', ['index_display_sidebars', '1']],
			['config.add', ['sidebars_side', '2']],
			['config.add', ['sidebars_html', '1']],
			['config.add', ['sidebars_bbcode', '0']],
			['config.add', ['sidebars_url', '0']],
			['config.add', ['sidebars_smilies', '0']],
		];
	}
}
