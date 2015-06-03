<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 24.05.15
 * Time: 15:58
 */

namespace Backend\Classes;


use Request\Classes\Request;

abstract class AbstractController extends \Request\Classes\Controller
{
	
	public function init()
	{
		$request = new \Request\Classes\Request(\Request\Classes\Url::get_instance('/backend/menu'));

		$request_model = new \Request\Model\Request($this->get_request()->get_model()->get_connection());
		$request->set_model($request_model);
		$request_model->set_param('module', 'Backend');
		$request_model->set_param('controller', 'Menu');
		$request_model->set_param('action', 'Index');

		$this->get_layout()->set_menu($request->execute());
	}
}