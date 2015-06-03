<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 24.05.15
 * Time: 17:26
 */

namespace Request\Interfaces;


interface Renderable {
	public function set_renderer($renderer);
	public function get_renderer();
}