<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 28.05.15
 * Time: 13:18
 */

namespace Backend\View\Rewrite;

use \Request\Classes as Request_Classes;

class Edit extends Request_Classes\View {

	public function get_html()
	{
		if (isset($this->params))
		{
			$params = $this->params;
		}
		else
		{
			$params = '';
		}
		return $this->get_form().$params;
	}
}