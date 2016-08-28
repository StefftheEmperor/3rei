<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 31.07.16
 * Time: 12:41
 */

namespace Page\Controller\Page;

use Request\Classes\Rewrite\Params;
use Request\Classes\Controller;

/**
 * Class Menu
 * @package Backend\Classes
 */
class Menu extends Controller
{
	public function action_view()
	{

	}

	public function action_index()
	{
		$id = $this->get_request()->get_param('menu_id')->get_value();
		$menu_node = \Page\Classes\Menu\Node::factory_by_id($this->get_database_connection(), $id);

		$menu = \Page\Classes\Menu::factory($menu_node);

		$children = $menu->get_children();

		$children_views = array();
		foreach ($children as $child)
		{
			$request = $this->get_new_child_request();
			$request->set_params(Params::factory(array('controller' => 'Page\Menu', 'action' => 'Index', 'menu_id' => $child->get_id())));

			$children_views[] = $request->execute();
		}

		$this->get_view()->children = $children_views;
		$this->get_view()->menu = $menu;
	}
}