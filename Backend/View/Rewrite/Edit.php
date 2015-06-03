<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 28.05.15
 * Time: 13:18
 */

namespace Backend\View\Rewrite;


class Edit extends \Request\Classes\View {

	public function get_html()
	{
		return $this->get_form().$this->get_params();
	}
}