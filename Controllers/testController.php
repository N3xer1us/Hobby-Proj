<?php

class testController
{

	function __construct()
	{
		configRouter::registerController('test',array('index'));
	}

	function callAction($actionName)
	{
		$this->$actionName();
	}

	function index()
	{
		$button = array(
						'name' => 'Comment',
						'link' => configRouter::get_link('comment/create?ticket_id='.$_GET['id']),
						'classes' => 'btn-primary btn-sm',
						'alt_name' => '<span class="glyphicon glyphicon-plus"></span>',
						'btn_type' =>'button'
					);

		$data['TEMPLATE_test'] = array(
			'template_name' => 'comments',
			'BUTTON_start' => $button,
			'ITERATION_test' => array(
				array('word' => 'apple','BUTTON_test' => $button),
				array('word' => 'banana','BUTTON_test' => $button),
				array('word' => 'pear','BUTTON_test' => $button),
			)
		);
		viewLoader::loadView('test/test.php',$data);
	}

}

?>
