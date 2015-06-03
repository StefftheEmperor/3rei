<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 17.05.15
 * Time: 19:33
 */

namespace Backend\View\Rewrite;


class RewriteList extends \Request\Classes\View {

	public function get_html()
	{
		return $this->get_table();
	}
}