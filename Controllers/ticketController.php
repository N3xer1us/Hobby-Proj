<?php

class ticketController
{
	function __construct()
	{
		configRouter::registerController('ticket',array('index','create','preview','update','delete'));
	}

	function callAction($actionName)
	{
		$this->$actionName();
	}

	function index()
	{
		$currentRole = $_SESSION['currentUser']['role_id'];

		if($currentRole <= 3)
		{
			$my_tickets = Repo::selectByFilter('ticket',array('author_id'=>$_SESSION['currentUser']['id'], 'is_removed'=>0), array('id','title','content','maintenace_type','created_on'));

			if(is_array($my_tickets))
			{
				$my_ticket_table['rows'] = array();

				foreach ($my_tickets as $mt)
				{
					$mt['title'] = '<a href="'.configRouter::get_link('ticket/preview').'?id='.$mt['id'].'" >'.$mt['title'].'</a>';

					$mt['buttons'] = '<a href="'.configRouter::get_link('ticket/update').'?id='.$mt['id'].'" ><button type="button" name="update" class="btn btn-primary btn-sm" ><span class="glyphicon glyphicon-pencil"></span></button></a> ';
					$mt['buttons'] .= '<a href="'.configRouter::get_link('ticket/delete').'?id='.$mt['id'].'" ><button type="button" name="delete" class="btn btn-primary btn-sm" ><span class="glyphicon glyphicon-trash"></span></button></a>';

					switch ($mt['maintenace_type'])
					{
						case 1:
							$mt['maintenace_type'] = 'Office';
							break;
						case 2:
							$mt['maintenace_type'] = 'Technical';
							break;
					}

					unset($mt['id']);
					$my_ticket_table['rows'][] = $mt;
				}

				$my_ticket_table['columns'] = array('Title','Content','Ticket Type','Created on');
				$my_ticket_table['columns'][] = 'Operations';
			}

			$tickets = Repo::selectByFilter('ticket',array('visibility'=>1, 'is_removed'=>0), array('id','title','content','maintenace_type','author_id','created_on'));

			if(is_array($tickets))
			{
				$tickets_table['rows'] = array();

				foreach ($tickets as $tt)
				{
					if($tt['author_id'] != $_SESSION['currentUser']['id'])
					{
						$tt['title'] = '<a href="'.configRouter::get_link('ticket/preview').'?id='.$tt['id'].'" >'.$tt['title'].'</a>';

						switch ($tt['maintenace_type'])
						{
							case 1:
								$tt['maintenace_type'] = 'Office';
								break;
							case 2:
								$tt['maintenace_type'] = 'Technical';
								break;
						}

						$author = Repo::selectOneById('user',(int)$tt['author_id']);
						$tt['author_id'] = $author['username'];
						unset($tt['id']);
						$tickets_table['rows'][] = $tt;
					}
				}

				if(count($tickets_table['rows']) != 0)
				{
					$tickets_table['columns'] = array('Title','Content','Ticket Type','Author','Created on');
				}
			}

			$data = array(
				'table_title' => 'Public Tickets',
				'user_role_id' => $currentRole,
				'my_ticket_TABLE' => (count($my_ticket_table['rows']) != 0?$my_ticket_table:'There\'s no tickets here'),
				'tickets_TABLE' => (count($tickets_table['rows']) != 0?$tickets_table:'There\'s no tickets here'),
				'add_ticket_BUTTON' => array(
					'btn_type'=>'button',
					'name' => 'Add Ticket',
					'classes'=>'btn-primary btn-sm',
					'link' => configRouter::get_link('ticket/create'),
					'alt_name' => '<span class="glyphicon glyphicon-plus"></span>'
				)
			);
		}
		else
		{
			switch($currentRole)
			{
				case 4:
					$tickets = Repo::selectByFilter('ticket',array('maintenace_type'=>2));
					$role_name = 'Tech ';
					break;
				case 5:
					$tickets = Repo::selectByFilter('ticket',array('maintenace_type'=>1));
					$role_name = 'Office ';
					break;

				default :
					trigger_error('Your role is invalid , how did you manage that even , dude',E_USER_ERROR);
			}

			if(is_array($tickets))
			{
				$tickets_table['columns'] = array_keys($tickets[0]);
				$tickets_table['rows'] = array();

				foreach ($tickets as $tt)
				{
					$tt['title'] = '<a href="'.configRouter::get_link('ticket/preview').'?id='.$tt['id'].'" >'.$tt['title'].'</a>';

					$tt['buttons'] = '<a href="'.configRouter::get_link('ticket/delete').'?id='.$tt['id'].'" ><button type="button" name="delete" class="btn btn-primary btn-sm" ><span class="glyphicon glyphicon-trash"></span></button></a>';

					$tickets_table['rows'][] = $tt;
				}
			}

			$data = array(
				'table_title' => $role_name .'Support Tickets',
				'tickets_TABLE' => (!is_null($tickets_table)?$tickets_table:'There\'s no tickets here'),
				'add_ticket_BUTTON' => array(
					'btn_type'=>'button',
					'name' => 'Add Ticket',
					'classes'=>'btn-primary btn-sm',
					'link'=>'<a href="'.configRouter::get_link('ticket/create').'"><span>New Ticket</span></a>'
				)
			);
		}

		viewLoader::loadView('ticket/index.php',$data);
	}

	function create()
	{
		if(!empty($_POST))
		{

			if(isset($_POST['title']) && $_POST['title'] != '')
			{
				$title = $_POST['title'];
			}
			else
			{
				$message['title'] = 'Please fill the title field';
			}

			if(isset($_POST['content']) && $_POST['content'] != '')
			{
				$content = $_POST['content'];
			}
			else
			{
				$content = null;
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

			if(isset($_POST['visibility']))
			{
				if($_POST['visibility'] > 0 &&  $_POST['visibility'] <= 2)
				{
					$visibility = $_POST['visibility'];
				}
				else
				{
					$message['visibility'] = 'Invalid visibility chosen';
				}
			}
			else
			{
				$message['visibility'] = 'Please choose a visibility';
			}

			if(isset($_POST['support_category']))
			{
				if($_POST['support_category'] > 0 &&  $_POST['support_category'] <= 2)
				{
					$maint_type = $_POST['support_category'];
				}
				else
				{
					$message['maint_type'] = 'Invalid visibility chosen';
				}
			}
			else
			{
				$message['maint_type'] = 'Please choose a visibility';
			}

			if(!isset($message))
			{
				$current_date = date('Y-m-d H:i:s');
				$ticket = new Ticket(null, $title, $content, $image, (int)$visibility, (int)$maint_type, (int)$_SESSION['currentUser']['id'], $current_date, 0);

				Repo::insert($ticket);

				configRouter::goto('ticket/index');
				exit;
			}
		}

		$data = array(
			'ticket_FORM'=> array(
				'method' => 'post',
				'action' => 'create',
				'enctype' => 'multipart/form-data',
				'inputs' => array(
					array(
						'name' => 'title',
						'type' => 'text',
						'value' =>(isset($title)? $title:''),
						'err' => (isset($message['title'])?$message['title']:'')
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
						'name' => 'visibility',
						'type' => 'select',
						'chosen' => (isset($vibility)? $vibility:''),
						'options' => array(
							array(
								'name' => 'public',
								'value' => 1
							),
							array(
								'name' => 'private',
								'value' => 2
							)
						),
						'err' => (isset($message['visibility'])?$message['visibility']:'')
					),
					array(
						'name' => 'support category',
						'type' => 'select',
						'chosen' => (isset($maint_type)? $maint_type:''),
						'options' => array(
							array(
								'name' => 'office',
								'value' => 1
							),
							array(
								'name' => 'technical',
								'value' => 2
							)
						),
						'err' => (isset($message['maint_type'])?$message['maint_type']:'')
					),
					array(
						'name' => 'Create',
						'type' => 'button',
						'btn_type' => 'submit',
						'classes' => 'btn-primary',
						'err' => (isset($message['form_err'])?$message['form_err']:'')
					)
				)
			)
		);

		viewLoader::loadView('ticket/create.php',$data);
	}

	function update()
	{
		if(!empty($_POST))
		{

			if(isset($_POST['title']) && $_POST['title'] != '')
			{
				$title = $_POST['title'];
			}
			else
			{
				$message['title'] = 'Please fill the title field';
			}

			if(isset($_POST['content']) && $_POST['content'] != '')
			{
				$content = $_POST['content'];
			}
			else
			{
				$message['content'] = 'Please fill the content field';
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

			if(isset($_POST['visibility']))
			{
				if($_POST['visibility'] > 0 &&  $_POST['visibility'] <= 2)
				{
					$visibility = $_POST['visibility'];
				}
				else
				{
					$message['visibility'] = 'Invalid visibility chosen';
				}
			}
			else
			{
				$message['visibility'] = 'Please choose a visibility';
			}

			if(isset($_POST['support_category']))
			{
				if($_POST['support_category'] > 0 &&  $_POST['support_category'] <= 2)
				{
					$maint_type = $_POST['support_category'];
				}
				else
				{
					$message['maint_type'] = 'Invalid visibility chosen';
				}
			}
			else
			{
				$message['maint_type'] = 'Please choose a visibility';
			}

			if(!isset($message))
			{
				$ticket = new Ticket($_POST['id'],$title,$content,$image,(int)$visibility,(int)$maint_type,(int)$_SESSION['currentUser']['id'], $_POST['created_on'], 0);

				Repo::update($ticket);

				configRouter::goto('ticket/index');
				exit;
			}
		}

		if(isset($_GET['id']))
		{
			$id = $_GET['id'];

			$ticket = Repo::selectByFilter('ticket',array('id'=>$id, 'is_removed'=>0));
			$title = $ticket['title'];
			$content = $ticket['content'];
			$maint_type = $ticket['maintenace_type'];
			$visibility = $ticket['visibility'];
			$created_on = $ticket['created_on'];
		}
		else
		{
			configRouter::goto('ticket/index');
			exit;
		}

		$data = array(
			'ticket_FORM'=> array(
				'method' => 'post',
				'action' => 'update',
				'enctype' => 'multipart/form-data',
				'inputs' => array(
					array(
						'name' => 'id',
						'type' => 'hidden',
						'value' =>(isset($id)? $id:'')
					),
					array(
						'name' => 'created_on',
						'type' => 'hidden',
						'value' =>(isset($created_on)? $created_on:'')
					),
					array(
						'name' => 'title',
						'type' => 'text',
						'value' =>(isset($title)? $title:''),
						'err' => (isset($message['title'])?$message['title']:'')
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
						'name' => 'visibility',
						'type' => 'select',
						'chosen' => (isset($vibility)? $vibility:''),
						'options' => array(
							array(
								'name' => 'public',
								'value' => 1
							),
							array(
								'name' => 'private',
								'value' => 2
							)
						),
						'err' => (isset($message['visibility'])?$message['visibility']:'')
					),
					array(
						'name' => 'support category',
						'type' => 'select',
						'chosen' => (isset($maint_type)? $maint_type:''),
						'options' => array(
							array(
								'name' => 'office',
								'value' => 1
							),
							array(
								'name' => 'technical',
								'value' => 2
							)
						),
						'err' => (isset($message['maint_type'])?$message['maint_type']:'')
					),
					array(
						'name' => 'Save changes',
						'type' => 'button',
						'btn_type' => 'submit',
						'classes' => 'btn-primary',
						'err' => (isset($message['form_err'])?$message['form_err']:'')
					)
				)
			)
		);

		viewLoader::loadView('ticket/update.php',$data);
	}

	function delete()
	{
		if(isset($_GET['id']))
		{
			$id = $_GET['id'];

			Repo::delete('ticket',(int)$id);
			configRouter::goto('ticket/index');
			exit;
		}
		else
		{
			configRouter::goto('ticket/index');
			exit;
		}
	}

	function preview()
	{
		if(isset($_GET['id']))
		{
			$ticket = Repo::selectByFilter('ticket',array('id'=>$_GET['id'], 'is_removed'=>0))[0];
			if(is_array($ticket))
			{
				$author = Repo::selectOneById('user',(int)$ticket['author_id']);

				switch ($ticket['maintenace_type'])
				{
					case 1:
						$maint_type = 'Office support';
						break;

					case 2:
						$maint_type = 'Tech support';
						break;

					default :
						trigger_error('Ticket has invalid maintenace type');
				}

				$comments_data = Repo::selectByFilter('comment',array('ticket_id'=>$ticket['id'], 'is_removed' => 0));

				if(is_array($comments_data))
				{
					$comments = array();

					foreach($comments_data as $comment)
					{
						$author = Repo::selectOneById('user',(int)$comment['author_id']);

						if(is_null($comment['comment_id']))
						{
							$comment['author_name'] = $author['username'];

							if(!is_null($comment['image']))
							{
								$comment['image'] = '<a href="'.configUpload::get_file_url($comment['image']).'" target="_blank">View embeded image</a>';
							}

							if($comment['author_id'] == $_SESSION['currentUser']['id'])
							{
								$comment['edit_BUTTON'] = array(
								'name' => 'Edit',
								'link' => configRouter::get_link('comment/edit?id='.$comment['id']),
								'classes' => 'btn-primary btn-sm',
								'alt_name' => 'Edit',
								'btn_type' =>'button'
								);
							}
							else
							{
								$comment['edit_BUTTON'] = '';
							}

							$comment['reply_BUTTON'] = array(
								'name' => 'Reply',
								'link' => configRouter::get_link('comment/create?comment_id='.$comment['id'].'&ticket_id='.$_GET['id']),
								'classes' => 'btn-primary btn-sm',
								'alt_name' => 'Reply',
								'btn_type' =>'button'
							);

							$comments[$comment['id']] = $comment;
						}
						else
						{
							$comment['author_name'] = $author['username'];

							$replied_to_id = $comments[$comment['comment_id']]['author_id'];
							$replied_to = Repo::selectOneById('user',(int)$replied_to_id)['username'];
							$comment['content'] = '@'.$replied_to.' '.$comment['content'];

							if(!is_null($comment['image']))
							{
								$comment['image'] = '<a href="'.configUpload::get_file_url($comment['image']).'" target="_blank">View embeded image</a>';
							}

							if($comment['author_id'] == $_SESSION['currentUser']['id'])
							{
								$comment['edit_BUTTON'] = array(
								'name' => 'Edit',
								'link' => configRouter::get_link('comment/edit?id='.$comment['id']),
								'classes' => 'btn-primary btn-sm',
								'alt_name' => 'Edit',
								'btn_type' =>'button'
								);
							}
							else
							{
								$comment['edit_BUTTON'] = '';
							}

							$comment['reply_BUTTON'] = array(
								'name' => 'Reply',
								'link' => configRouter::get_link('comment/create?comment_id='.$comment['id'].'&ticket_id='.$_GET['id']),
								'classes' => 'btn-primary btn-sm',
								'alt_name' => 'Reply',
								'btn_type' =>'button'
							);

							$comments[$comment['id']] = $comment;
						}
					}

//					$comments_array = array();
//					$replies = array();
//					foreach ($comments as $comment)
//					{
//						$author = Repo::selectOneById('user',(int)$comment['author_id']);
//						if(is_null($comment['comment_id']))
//						{
//							$comment_section = '<div class="comment">';
//							$comment_section .= '<h4>'.$author['username'].'</h4>';
//							$comment_section .= '<div class="row"><div class="col-sm-11">'.$comment['content'].'</div></div>';
//							$comment_section .= '<div class="row"><div class="col-sm-11">'.(isset($comment['image'])? '<a href="'.configUpload::get_file_url($comment['image']).'" target="_blank">View embeded image</a>':'').'</div></div><br>';
//
//							if($comment['author_id'] == $_SESSION['currentUser']['id'])
//							{
//								$comment_section .= '<a href="'.configRouter::get_link('comment/edit?id='.$comment['id']).'"><button type="button" class="btn-xs" style="margin-bottom: 10px;">Edit</button></a>';
//							}
//
//							$comments_array[] = array('content' => $comment_section ,'id' => $comment['id']);
//						}
//						else
//						{
//
//							$reply_section = '<div class="row comment">';
//							$reply_section .= '<h5>'.$author['username'].'</h5>';
//							$reply_section .= '<div class="row"><div class="col-sm-11">'.$comment['content'].'</div></div>';
//							$reply_section .= '<div class="row"><div class="col-sm-11">'.(isset($comment['image'])? '<a href="'.configUpload::get_file_url($comment['image']).'" target="_blank">View embeded image</a>':'').'</div></div><br>';
//
//							if($comment['author_id'] == $_SESSION['currentUser']['id'])
//							{
//								$reply_section .= '<a href="'.configRouter::get_link('comment/edit?id='.$comment['id']).'"><button type="button" class="btn-xs" style="margin-bottom: 10px;">Edit</button></a>';
//							}
//
//							$reply_section .= '</div>';
//
//							if(!isset($replies[$comment['comment_id']]))
//							{
//								$replies[$comment['comment_id']] = $reply_section;
//							}
//							else
//							{
//								$replies[$comment['comment_id']] .= $reply_section;
//							}
//						}
//					}
//
//					$comments_html = '';
//					foreach ($comments_array as $c)
//					{
//
//						if(isset($replies[$c['id']]))
//						{
//							$comments_html .= $c['content'];
//							$comments_html .= '<button type="button" class="view_relies btn-xs" data-target="'.$c['id'].'" style="margin-bottom: 10px;">View</button>';
//							$comments_html .= '<a href="'.configRouter::get_link('comment/create?comment_id='.$c['id'].'&ticket_id='.$_GET['id']).'"><button type="button" class="btn-xs" style="margin-bottom: 10px;">Reply</button></a>';
//							$comments_html .= '<div class="replies-'.$c['id'].'" style="display: none;">';
//							$comments_html .= '<div class="col-sm-12 reply-container">';
//							$comments_html .= $replies[$c['id']];
//							$comments_html .= '</div></div></div>';
//						}
//						else
//						{
//							$comments_html .= $c['content'];
//							$comments_html .= '<a href="'.configRouter::get_link('comment/create?comment_id='.$c['id'].'&ticket_id='.$_GET['id']).'"><button type="button" class="btn-xs" style="margin-bottom: 10px;">Reply</button></a></div>';
//						}
			}

				$data = array(
					'ticket_title' => $ticket['title'],
					'ticket_image' => configUpload::get_file_url($ticket['image']),
					'ticket_author' => $author['username'],
					'ticket_content' => $ticket['content'],
					'ticket_maint_type' => $maint_type,
					'comments_TEMPLATE' => array(
						'comment_BUTTON' => array(
						'name' => 'Comment',
						'link' => configRouter::get_link('comment/create?ticket_id='.$_GET['id']),
						'classes' => 'btn-primary btn-sm',
						'alt_name' => '<span class="glyphicon glyphicon-plus"></span>',
						'btn_type' =>'button'
						),
						'template_name' => 'comments',
						'replies_ITERATION' => $comments
					)
				);

				viewLoader::loadView('ticket/preview.php',$data);
			}
			else
			{
				trigger_error('Ticket doesn\'t exist');
			}
		}
		else
		{
			configRouter::goto('ticket/index');
			exit;
		}
	}
}
?>

