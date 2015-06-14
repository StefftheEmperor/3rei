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
			$edit_link = \Structure\Classes\Link::factory('edit', 'edit');
			$edit_link->set_href(\Request\Classes\Url::get_instance('/backend/rewrite/edit/'.$model_rewrite->id));
			$row->add_cell(\Structure\Classes\Table\Row\Cell::factory($edit_link));

			$delete_link = \Structure\Classes\Link::factory('delete', 'delete');
			$delete_link->set_href(\Request\Classes\Url::get_instance('/backend/rewrite/delete/'.$model_rewrite->id));
			$row->add_cell(\Structure\Classes\Table\Row\Cell::factory($delete_link));

			$table->add_row($row);
		}

		$row = \Structure\Classes\Table\Row::factory();
		$row->add_cell(\Structure\Classes\Table\Row\Cell::factory(\Structure\Classes\Link::factory('add','add')->set_href('/backend/rewrite/add')));
		$table->add_row($row);
		$this->get_view()->table = $table;
	}

	public function action_edit()
	{

		$rewrite_id = $this->get_request()->get_attribute('rewrite_id');
		if (isset($rewrite_id)) {
			$rewrite = \Request\Model\Rewrite::factory_by_id($this->get_database_connection(), $rewrite_id);
		} else {
			$rewrite = new \Request\Model\Rewrite($this->get_database_connection());
		}

		$form = \Structure\Classes\Form::factory('rewrite');
		$form->set_method(\Structure\Classes\Form::METHOD_POST);
		$form->set_action($this->get_request()->get_url());

		$rewrite_url = '';
		if (isset($rewrite->url))
		{
			$rewrite_url = $rewrite->get_url();
		}
		$rewrite_url_input = \Structure\Classes\Form\Input::factory('rewrite_url', $rewrite_url);
		$key_value_pair = \Structure\Classes\KeyValuePair::factory(\Structure\Classes\Label::factory('Url'), $rewrite_url_input);
		$form->add($key_value_pair);
		$rewrite_id_input = \Structure\Classes\Form\Input\Hidden::factory('rewrite_id', $rewrite_id);
		$form->add($rewrite_id_input);
		$form->add(\Structure\Classes\Form\Input\Submit::factory('save', 'save'));

		$form->validate($this->get_request()->get_post_data());
		if ($form->is_submitted($this->get_request()->get_post_data()))
		{
			$rewrite->set_id($form->get_value_of('rewrite_id'));
			$rewrite->set_url($rewrite_url_input->get_value());
			$rewrite->save();

			$rewrite_id_input->set_value($rewrite->get_id());
		}
		$this->get_view()->form = $form;

		$params_request = $this->get_new_child_request();
		$params_request->set_params(array('module' => 'Backend', 'controller' => 'Request\Params', 'action' => 'Edit', 'layout' => 'Plain'));

		if (isset($rewrite->request_id)) {
			$params_request->set_attribute('request_id', $rewrite->request_id);
		}

		$this->get_view()->params = $params_request->execute();
	}

	public function action_add()
	{
		$this->get_request()->set_param('view', 'Edit');
		$this->action_edit();
	}
}