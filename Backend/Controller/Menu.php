<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 17.05.15
 * Time: 19:24
 */

namespace Backend\Controller;


use Request\Classes\Request;

class Menu extends \Request\Classes\Controller {

	public function init()
	{
		$this->set_layout(NULL);
	}


}