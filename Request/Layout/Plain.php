<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 24.05.15
 * Time: 16:47
 */

namespace Request\Layout;


class Plain extends \Template\Classes\Layout {

	public function get_html()
	{
		return $this->get_content();
	}
}