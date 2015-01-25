<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 09.09.14
 * Time: 18:17
 */

namespace Db\Classes;


class Column extends \Model\Classes\AbstractModel
{
	protected $is_primary;

	public function set_key($key)
	{
		if ($key === 'PRI')
		{
			$this->is_primary = TRUE;
		}
		else
		{
			$this->is_primary = FALSE;
		}
		parent::set_key($key);

	}

	protected function is_primary()
	{
		return $this->is_primary;
	}
} 