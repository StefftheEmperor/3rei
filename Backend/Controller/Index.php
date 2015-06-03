<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 25.01.15
 * Time: 19:14
 */

namespace Backend\Controller;


use Request\Classes\Url;

class Index extends \Backend\Classes\AbstractController {

	public function action_index()
	{

		$menu_node = \Page\Classes\Menu\Node::factory_by_request($this->get_request());

		//$this->get_layout()->menu = $menu_node->get_menu()->get_view();
	}

	public function action_list()
	{

	}
}