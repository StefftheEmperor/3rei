<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 17.05.15
 * Time: 15:44
 */

namespace Page\Model\Menu;

class Node extends \Db\Classes\AbstractMPTT {

	protected $request;

	protected $menu = NULL;

	public function __construct()
	{
		$this->menu = new \Page\Classes\Menu;
		$node = \Page\Classes\Menu\Node::factory_by_model($this);
		$this->menu->set_node($node);
	}

	public function get_menu()
	{
		return $this->menu;
	}

	public static function factory_by_request(\Request\Classes\Request $request)
	{
		$request_id = $request->get_model()->get_id();
		if (isset($request_id))
		{
			$called_class = get_called_class();
			$node = \Db\Classes\Table::factory($request->get_model()->get_connection(), 'page__menu__node')
				->filter(\Db\Classes\Filter::factory('request_id', '=', $request_id))
				->get_one()
				->map_to($called_class);

		} else {
			$node = new static;
			$node->set_request($request);
		}

		return $node;
	}

	public static function factory_by_id(\Db\Classes\AbstractConnection $connection, $id)
	{
		return \Db\Classes\Table::factory($connection, 'page__menu__node')
			->filter(\Db\Classes\Filter::factory('id', '=', $id))
			->get_one()
			->map_to(get_called_class());
	}
}