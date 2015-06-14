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

	protected $left_boundary_identifier= 'lbd';
	protected $right_boundary_identifier = 'rbd';

	public function get_left_boundary_identifier()
	{
		return $this->left_boundary_identifier;
	}

	public function get_right_boundary_identifier()
	{
		return $this->right_boundary_identifier;
	}

	public function init()
	{
		$this->menu = new \Page\Classes\Menu;
		$node = \Page\Classes\Menu\Node::factory_by_model($this);
		$this->menu->set_node($node);
	}

	public function get_primary_key()
	{
		return 'id';
	}

	public function get_table_name()
	{
		return 'page__menu__node';
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
				->filter(\Db\Classes\Filter\Comparison::factory('request_id', $request_id))
				->get_one(\Db\Classes\Table\Select\All::factory())
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
			->filter(\Db\Classes\Filter\Comparison::factory(\Db\Classes\Expression\Row::factory('id'), $id))
			->get_one(\Db\Classes\Table\Select\All::factory())
			->map_to(get_called_class());
	}

	public function load_children()
	{
		$parent = $this->get_table();
		$parent->debug = 'parent';
		$joined_table = clone $parent;
		$joined_table->debug = 'joined';
		$child_table = clone $parent;
		$child_table->debug = 'child';
		$select = \Db\Classes\Table\Select::factory('COUNT(*)-1', 'depth');
		$parent->join($joined_table,
			\Db\Classes\Filter\Between::factory(
				$joined_table->get_column($this->get_left_boundary_identifier()),
				$parent->get_column($this->get_left_boundary_identifier()),
				$parent->get_column($this->get_right_boundary_identifier())
			)
		)
			->join($child_table,
				\Db\Classes\Filter\Between::factory(
					$child_table->get_column($this->get_left_boundary_identifier()),
					$joined_table->get_column($this->get_left_boundary_identifier()),
					$joined_table->get_column($this->get_right_boundary_identifier())
				)
			)
			->filter(\Db\Classes\Filter\Comparison::factory($parent->get_column('id'), $this->get_id()))
			->group($child_table->get_column($this->get_left_boundary_identifier()))
			->order($select)
			->order($child_table->get_column($this->get_left_boundary_identifier()))
			->select($select)
			->select(\Db\Classes\Table\Select\All::factory($child_table));
		$models = $parent->get_all()->map_to(get_class($this));

		$this->build_tree($models);
	}

	public function get_children()
	{
		if ( ! isset($this->children))
		{
			$this->load_children();
		}

		return $this->children;
	}

	/**
	 * @param \Page\Model\Menu\Node[] $children
	 */
	public function build_tree($children)
	{
		foreach ($children as $child)
		{
			$child_depth = $child->get_depth();
			$current_depth = $this->get_depth();
			if ($child_depth == ($current_depth + 1))
			{
				$child_lbd = $child->get_left_boundary();
				$current_lbd = $this->get_left_boundary();
				$child_rbd =  $child->get_right_boundary();
				$current_rbd = $this->get_right_boundary();
				if (($child_lbd > $current_lbd) AND ($child_rbd < $current_rbd))
				{
					$this->add_child($child);
					$child->build_tree(clone $children);
				}
			}
		}
	}

	public function add_child(\Page\Model\Menu\Node $child)
	{
		$this->children[] = $child;
		$child->set_parent($this);
	}
	public function get_depth()
	{
		return $this->depth;
	}

	public function set_depth($depth)
	{
		$this->depth = $depth;

		return $this;
	}

	public function set_parent(\Page\Model\Menu\Node $parent)
	{
		$this->parent = $parent;

		return $this;
	}

	public function get_parent()
	{
		return $this->parent;
	}
}