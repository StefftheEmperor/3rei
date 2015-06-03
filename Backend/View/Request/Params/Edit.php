<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 28.05.15
 * Time: 18:20
 */

namespace Backend\View\Request\Params;


class Edit extends \Request\Classes\View {
	public function get_html()
	{
		return $this->get_form();
	}
}