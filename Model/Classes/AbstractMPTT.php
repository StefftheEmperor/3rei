<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 17.05.15
 * Time: 15:39
 */

namespace Model\Classes;


abstract class AbstractMPTT extends AbstractModel
{
	protected $children = NULL;

	protected $parent = NULL;

	protected $depth = 0;


}