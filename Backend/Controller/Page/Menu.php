<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 31.05.15
 * Time: 16:46
 */

namespace Backend\Controller\Page;


class Menu extends \Backend\Classes\AbstractController
{

	public function action_index()
	{
		$menu_node = \Page\Classes\Menu\Node::factory_by_id($this->get_database_connection(), 1);

		$menu = \Page\Classes\Menu::factory($menu_node);

		$this->get_view()->menu = $menu;
	}
}