<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 10.06.15
 * Time: 10:37
 */

namespace Db\Classes\Table;


class Order {
	const DIRECTION_ASCENDING = 'ASCENDING';
	const DIRECTION_DESCENDING = 'DESCENDING';
	protected $select = NULL;
	protected $direction = self::DIRECTION_ASCENDING;

	public static function factory($select, $direction = NULL)
	{
		$order = new static;
		$order->set_select($select);

		if (isset($direction)) {
			$order->set_direction($direction);
		}

		return $order;
	}

	public function get_direction()
	{
		return $this->direction;
	}

	public function set_direction($direction)
	{
		if ($direction === static::DIRECTION_ASCENDING) {
			$this->direction = static::DIRECTION_ASCENDING;
		} elseif ($direction === static::DIRECTION_DESCENDING) {
			$this->direction = static::DIRECTION_DESCENDING;
		} else {
			throw new \Db\Classes\Exception('Ordir direction must be ' . static::DIRECTION_ASCENDING . ' or ' . static::DIRECTION_DESCENDING);
		}

		return $this;
	}

	/**
	 * @return \Db\Classes\Table\Column
	 */
	public function get_select()
	{
		return $this->select;
	}

	public function set_select($select)
	{
		$this->select = $select;

		return $this;
	}
}