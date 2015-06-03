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

	public function action_index()
	{
		$backend_root_request = \Request\Classes\Request::factory_by_url($this->get_database_connection(), \Request\Classes\Url::get_instance('/backend'));
		$menu_node = \Page\Classes\Menu\Node::factory_by_request($backend_root_request);
		$menu = \Page\Classes\Menu::factory($menu_node);

		$this->get_view()->menu = $menu;
	}
}