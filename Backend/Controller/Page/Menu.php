<?php

namespace Backend\Controller\Page;

use \Page\Classes\Menu as Page_Menu_Class;

use Backend\Classes\AbstractController;

use \Page\Classes as Page_Classes;

use \Request\Classes as Request_Classes;

class Menu extends AbstractController
{

	public function action_index()
	{
		$id = $this->get_request()->get_param('menu_id')->get_value();
		$menu_node = Page_Menu_Class\Node::factory_by_id($this->get_database_connection(), $id);

		$menu = Page_Classes\Menu::factory($menu_node);

		$children = $menu->get_children();

		$children_views = array();
		foreach ($children as $child)
		{
			$request = $this->get_new_child_request();
			$request->set_params(Request_Classes\Rewrite\Params::factory(array('controller' => 'Page\Menu', 'action' => 'Index', 'menu_id' => $child->get_id())));

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
