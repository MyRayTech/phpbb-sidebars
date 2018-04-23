<?php

namespace raytech\sidebars\helper;
/**
 * Description of main
 *
 * @author Reaper
 * @class \raytech\sidebars\helper\main
 */
class main
{
	
	public function side_text($side)
	{
		if((int) $side === 0) {
			return 'Right';
		}
		elseif((int) $side === 1) {
			return 'Left';
		}
		elseif((int) $side === 2){
			return 'Center';
		}
	}
}