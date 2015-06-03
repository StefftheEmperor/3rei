<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 17.05.15
 * Time: 19:31
 */

namespace Backend\Controller;


class Rewrite extends \Backend\Classes\AbstractController {

	public function action_list()
	{
		$this->set_view('RewriteList');

		$rewrites = \Request\Model\Rewrite::get_all($this->get_request()->get_model()->get_connection());

		$table = \Structure\Classes\Table::factory();
		foreach ($rewrites as $model_rewrite)
		{
			$row = \Structure\Classes\Table\Row::factory();

			$row->add_cell(\Structure\Classes\Table\Row\Cell::factory($model_rewrite->url));
			$edit_link = \Structure\Classes\Link::factory('edit');
			$edit_link->set_href(\Request\Classes\Url::get_instance('/backend/request/edit/'.$model_rewrite->id));
			$row->add_cell(\Structure\Classes\Table\Row\Cell::factory($edit_link));
			$table->add_row($row);
		}
		$this->get_view()->table = $table;
	}

	public function action_edit()
	{

		$rewrite_id = $this->get_request()->get_attribute('rewrite_id');
		$rewrite = \Request\Model\Rewrite::factory_by_id($this->get_database_connection(), $rewrite_id);
		$form = \Structure\Classes\Form::factory('rewrite');
		$form->set_method(\Structure\Classes\Form::METHOD_POST);
		$form->set_action($this->get_request()->get_url());

		$key_value_pair = \Structure\Classes\KeyValuePair::factory(\Structure\Classes\Label::factory('Url'), \Structure\Classes\Form\Input::factory('name', $rewrite->get_url()));
		$form->add($key_value_pair);
		$form->add(\Structure\Classes\Form\Input\Hidden::factory($rewrite_id));
		$form->add(\Structure\Classes\Form\Input\Submit::factory('save', 'save'));

		$form->validate($this->get_request()->get_post());
		$this->get_view()->form = $form;

		$params_request_model = new \Request\Model\Request($this->get_database_connection());
		$params_request = new \Request\Classes\Request;
		$params_request->set_model($params_request_model);
		$params_request->set_params(array('module' => 'Backend', 'controller' => 'Request\Params', 'action' => 'Edit', 'layout' => 'Plain'));
		$params_request->set_attribute('request_id', $rewrite->request_id);

		$this->get_view()->params = $params_request->execute();
	}
}