<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 30.11.14
 * Time: 04:18
 */

namespace Db;


class ResultSet extends \AbstractModel
{
	public function find($other, $strict = FALSE)
	{
		if ($strict)
		{
			foreach ($this as $row)
			{
				if ($row === $other) {
					return $row;
				}
			}
		}
		else
		{
			foreach ($this as $row)
			{
				if ($row === $other) {
					return $row;
				}
			}
		}
		return NULL;
	}
} 