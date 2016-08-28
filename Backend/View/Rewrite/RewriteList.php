<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 17.05.15
 * Time: 19:33
 */

namespace Backend\View\Rewrite;

use Request\Classes\View;

class RewriteList extends View {

	public function get_html()
	{
		return $this->get_table();
	}
}