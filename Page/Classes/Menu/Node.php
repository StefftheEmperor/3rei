<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 24.05.15
 * Time: 15:08
 */

namespace Page\Classes\Menu;


class Node {

	protected $model = NULL;

	public static function factory_by_model(\Page\Model\Menu\Node $model)
	{
		$node = new static;
		$node->set_model($model);
		return $node;
	}

	public static function factory_by_request(\Request\Classes\Request $request)
	{
		$model = \Page\Model\Menu\Node::factory_by_request($request);
		return static::factory_by_model($model);
	}

	public static function factory_by_id(\Db\Classes\AbstractConnection $connection, $id)
	{
		$model = \Page\Model\Menu\Node::factory_by_id($connection, $id);

		return static::factory_by_model($model);
	}

	public function set_model(\Page\Model\Menu\Node $model)
	{
		$this->model = $model;
		return $this;
	}

	public function get_model()
	{
		return $this->model;
	}
}