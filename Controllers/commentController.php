<?php

class commentController
{

	function __construct()
	{
		configRouter::registerController('comment',array('index','create','edit'));
	}

	function callAction($actionName)
	{
		$this->$actionName();
	}

	function index()
	{
	}

	function create()
	{

		if(isset($_GET['ticket_id']))
		{
			$ticket_id = $_GET['ticket_id'];

			if(isset($_GET['comment_id']))
			{
				$comment_id = $_GET['comment_id'];
			}
			else
			{
				$comment_id = null;
			}
		}
		else
		{
			configRouter::goto('ticket/index');
			exit;
		}


		if(!empty($_POST))
		{
			if(isset($_POST['comment_id']) && $_POST['comment_id'] > 0)
			{
				$comment_id = (int)$_POST['comment_id'];
			}
			else
			{
				$comment_id = null;
			}

			if(isset($_POST['ticket_id']) && $_POST['ticket_id'] > 0)
			{
				$ticket_id = $_POST['ticket_id'];
			}
			else
			{
				trigger_error('Missing ticket refference');
			}

			if(isset($_POST['content']) && $_POST['content'] != '')
			{
				$content = $_POST['content'];
			}
			else
			{
				$message['content'] = 'Content field can\'t be empty';
			}

			if($_FILES['image']['error'] == 0)
			{
				$output = configUpload::upload($_FILES['image']);

				if(strpos($output, '.')!==false)
				{
					$image = $output;
				}
				else
				{
					$message['image'] = $output;
				}
			}
			else
			{
				$image = null;
			}

			if(!isset($message))
			{
				$created_on = date('Y-m-d H:i:s');
				$comment = new Comment(null,$content,$image,(int)$_SESSION['currentUser']['id'],(int)$ticket_id,$comment_id,$created_on,0);

				Repo::insert($comment);

				configRouter::goto('ticket/preview?id='.$ticket_id);
				exit;
			}
		}

		$data = array(
			'comment_FORM'=> array(
				'method' => 'post',
				'action' => 'create?ticket_id='.$ticket_id,
				'enctype' => 'multipart/form-data',
				'inputs' => array(
					array(
						'name' => 'ticket_id',
						'type' => 'hidden',
						'value' => $ticket_id
					),
					array(
						'name' => 'comment_id',
						'type' => 'hidden',
						'value' => (isset($comment_id)?$comment_id:'')
					),
					array(
						'name' => 'content',
						'type' => 'textarea',
						'value' => (isset($content)?$content:''),
						'err' => (isset($message['content'])?$message['content']:'')
					),
					array(
						'name' => 'image',
						'type' => 'file',
						'err' => (isset($message['image'])?$message['image']:'')
					),
					array(
						'name' => 'Submit',
						'type' => 'button',
						'btn_type' => 'submit',
						'classes' => 'btn-primary',
						'err' => (isset($message['form_err'])?$message['form_err']:'')
					)
				)
			)
		);

		viewLoader::loadView('comment/create.php',$data);
	}

	function edit()
	{
		if(isset($_GET['id']))
		{
			$id = $_GET['id'];

			$comment = Repo::selectOneById('comment',(int)$id);

			if(is_array($comment))
			{
				$content = $comment['content'];
				$comment_id = $comment['comment_id'];
				$ticket_id = $comment['ticket_id'];
				$created_on = $comment['created_on'];
			}
		}
		else
		{
			configRouter::goto('ticket/index');
			exit;
		}


		if(!empty($_POST))
		{
			if(isset($_POST['comment_id']) && $_POST['comment_id'] > 0)
			{
				$comment_id = (int)$_POST['comment_id'];
			}
			else
			{
				$comment_id = null;
			}

			if(isset($_POST['ticket_id']) && $_POST['ticket_id'] > 0)
			{
				$ticket_id = $_POST['ticket_id'];
			}
			else
			{
				trigger_error('Missing ticket refference');
			}

			if(isset($_POST['content']) && $_POST['content'] != '')
			{
				$content = $_POST['content'];
			}
			else
			{
				$message['content'] = 'Content field can\'t be empty';
			}

			if($_FILES['image']['error'] == 0)
			{
				$output = configUpload::upload($_FILES['image']);

				if(strpos($output, '.')!==false)
				{
					$image = $output;
				}
				else
				{
					$message['image'] = $output;
				}
			}
			else
			{
				$image = null;
			}

			if(!isset($message))
			{
				$comment = new Comment($id,$content,$image,(int)$_SESSION['currentUser']['id'],(int)$ticket_id,$comment_id,$_POST['created_on'],0);

				Repo::update($comment);

				configRouter::goto('ticket/preview?id='.$ticket_id);
				exit;
			}
		}

		$data = array(
			'comment_FORM'=> array(
				'method' => 'post',
				'action' => 'edit?id='.$id,
				'enctype' => 'multipart/form-data',
				'inputs' => array(
					array(
						'name' => 'ticket_id',
						'type' => 'hidden',
						'value' => $ticket_id
					),
					array(
						'name' => 'created_on',
						'type' => 'hidden',
						'value' => $created_on
					),
					array(
						'name' => 'comment_id',
						'type' => 'hidden',
						'value' => (isset($comment_id)?$comment_id:'')
					),
					array(
						'name' => 'content',
						'type' => 'textarea',
						'value' => (isset($content)?$content:''),
						'err' => (isset($message['content'])?$message['content']:'')
					),
					array(
						'name' => 'image',
						'type' => 'file',
						'err' => (isset($message['image'])?$message['image']:'')
					),
					array(
						'name' => 'Save',
						'type' => 'button',
						'btn_type' => 'submit',
						'classes' => 'btn-primary',
						'err' => (isset($message['form_err'])?$message['form_err']:'')
					)
				)
			)
		);

		viewLoader::loadView('comment/edit.php',$data);
	}

}

?>

