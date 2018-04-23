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

class create_table extends \phpbb\db\migration\migration
{

	public function update_schema()
	{
		return [
			'add_tables' => [
				$this->table_prefix . 'blocks' => [
					'COLUMNS'		 => [
						'block_id'						 => ['UINT:11', null, 'auto_increment', 0],
						'block_name'					 => ['VCHAR:60', null],
						'block_order'					 => ['UINT:11', 0],
						'block_side'					 => ['BOOL', 0],
						'block_template'				 => ['VCHAR:60', null],
						'block_content'					 => ['TEXT', ''],
						'block_content_bbcode_uid'		 => ['VCHAR:8', ''],
						'block_content_bbcode_bitfield'	 => ['VCHAR:255', ''],
						'block_content_bbcode_options'	 => ['UINT:11', 7],
						'block_display'					 => ['BOOL', 0],
						'block_display_on_index'		 => ['BOOL', 0],
						'block_display_to_guest'		 => ['BOOL', 0],
					],
					'PRIMARY_KEY'	 => 'block_id',
				],
			],
		];
	}

	public function revert_schema()
	{
		return [
			'drop_tables' => [
				$this->table_prefix . 'blocks',
			],
		];
	}

}