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
	protected $children = NULL;
	protected $parent = NULL;
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

	/**
	 * @return \Page\Model\Menu\Node
	 */
	public function get_model()
	{
		return $this->model;
	}

	public function load_children()
	{
		$model = $this->get_model();

		$children_models = $model->get_children();

		if (isset($children_models)) {
			foreach ($children_models as $child_model) {
				$this->add_child(\Page\Classes\Menu\Node::factory_by_model($child_model));
			}
		}
		return $this;
	}

	public function add_child(\Page\Classes\Menu\Node $node)
	{
		$this->children[] = $node;
		$node->set_parent($this);
	}

	public function get_children()
	{
		if ( ! isset($this->children))
		{
			$this->load_children();
		}

		return $this->children;
	}

	public function set_parent(\Page\Classes\Menu\Node $parent)
	{
		$this->parent = $parent;

		return $this;
	}

	public function get_parent()
	{
		return $this->parent;
	}

	public function get_id()
	{
		return $this->get_model()->get_id();
	}
}