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
		$id = $this->get_request()->get_param('menu_id');
		$menu_node = \Page\Classes\Menu\Node::factory_by_id($this->get_database_connection(), $id);

		$menu = \Page\Classes\Menu::factory($menu_node);

		$children = $menu->get_children();

		$children_views = array();
		foreach ($children as $child)
		{
			$request = $this->get_new_child_request();
			$request->set_params(array('controller' => 'Page\Menu', 'action' => 'Index', 'menu_id' => $child->get_id()));

			$children_views[] = $request->execute();
		}

		$this->get_view()->children = $children_views;
		$this->get_view()->menu = $menu;
	}

	public function action_edit()
	{
		$this->action_index();
	}

	public function action_add()
	{
		$this->action_index();
	}
	public function action_children()
	{

	}
}