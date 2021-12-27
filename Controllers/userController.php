<?php

class userController
{
	function __construct()
	{
		configRouter::registerController('user',array('index','login','logout','register'));
	}

	function callAction($actionName)
	{
		$this->$actionName();
	}

	function index()
	{
		if(isset($_SESSION['currentUser']))
		{
			$full_name = $_SESSION['currentUser']['first_name'].' '.$_SESSION['currentUser']['last_name'];

			$role_name = Repo::selectByFilter('role',array('id'=>$_SESSION['currentUser']['role_id']))[0]['role_name'];

			$data= array(
				'id' => $_SESSION['currentUser']['id'],
				'username' => $_SESSION['currentUser']['username'],
				'full_name' => $full_name,
				'role' => $role_name
			);

			viewLoader::loadView('user/index.php',$data);
		}
	}

	function login()
	{
		if(!isset($_SESSION['currentUser']))
		{
			if(!empty($_POST))
			{
				if(isset($_POST['username']) && $_POST['username'] != '')
				{
					$username = $_POST['username'];
				}
				else
				{
					$message['username'] = 'Please fill the username field';
				}

				if(isset($_POST['password']) && $_POST['password'] != '')
				{
					$pass = $_POST['password'];
				}
				else
				{
					$message['pass'] = 'Please fill the password field';
				}

				if(isset($username) && isset($pass))
				{
					$user = Repo::selectByFilter('user',array('username' => $username))[0];

					if(is_array($user))
					{
						if($user['pass'] === $pass)
						{
							$_SESSION['currentUser'] = $user;

							configRouter::goto('ticket/index');
							exit;
						}
						else
						{
							$message['form_err'] = 'Wrong password';
						}
					}
					else
					{
						$message['form_err'] = 'User doesnt exist';
					}
				}
			}

			$form = array(
				'login_FORM' => array(
					'method' => 'post',
					'action' => 'login',
					'inputs' => array(
						array(
							'name' => 'username',
							'type' => 'text',
							'value' =>(isset($username)? $username:''),
							'err' => (isset($message['username'])?$message['username']:'')
						),
						array(
							'name' => 'password',
							'type' => 'password',
							'err' => (isset($message['pass'])?$message['pass']:'')
						),
						array(
							'name' => 'Login',
							'type' => 'button',
							'btn_type' => 'submit',
							'classes' => 'btn-primary',
							'err' => (isset($message['form_err'])?$message['form_err']:'')
						)
					)
				)
			);


			viewLoader::loadView('user/login.php',$form);
		}
		else
		{
			configRouter::goto('ticket/index');
			exit;
		}
	}

	function logout()
	{
		unset($_SESSION['currentUser']);
		configRouter::goto('user/login');
		exit;
	}

	function register()
	{
		if(!isset($_SESSION['currentUser']))
		{
			if(!empty($_POST))
			{

				if(isset($_POST['first_name']) && $_POST['first_name'] != '')
				{
					$fname = $_POST['first_name'];
				}
				else
				{
					$message['fname'] = 'Please fill the first name field';
				}

				if(isset($_POST['last_name']) && $_POST['last_name'] != '')
				{
					$lname = $_POST['last_name'];
				}
				else
				{
					$message['lname'] = 'Please fill the last name field';
				}

				if(isset($_POST['email']) && $_POST['email'] != '')
				{
					$email = $_POST['email'];
				}
				else
				{
					$message['email'] = 'Please fill the email field';
				}

				if(isset($_POST['username']) && $_POST['username'] != '')
				{
					$username = $_POST['username'];

					$usernames = Repo::selectByFilter('user',array('username'=>$username));

					if(is_array($usernames))
					{
						$message['username'] = 'Username already in use';
					}
				}
				else
				{
					$message['username'] = 'Please fill the username field';
				}

				if(isset($_POST['password']) && $_POST['password'] != '')
				{
					$pass = $_POST['password'];
				}
				else
				{
					$message['pass'] = 'Please fill the password field';
				}

				if(isset($_POST['password_confirm']) && $_POST['password_confirm'] != '')
				{
					$pass_confirm = $_POST['password_confirm'];
				}
				else
				{
					$message['pass_conf'] = 'Please fill the password confirm field';
				}

				if(isset($_POST['date_of_birth']) && $_POST['date_of_birth'] != '')
				{
					$DoB = $_POST['date_of_birth'];
				}
				else
				{
					$message['DoB'] = 'Please fill the date of birth field';
				}

				if(isset($_POST['role']))
				{
					$role_check = Repo::selectByFilter('role',array('id'=>$_POST['role']));

					if(is_array($role_check))
					{
						$role_opt = $_POST['role'];
					}
					else
					{
						$message['role'] = 'Invalid role chosen';
					}
				}
				else
				{
					$message['role'] = 'Please choose a role';
				}

				if(!isset($message))
				{
					if($pass === $pass_confirm)
					{
						$user = new User(null, $username, $fname, $lname, $email, $pass, $DoB, (int)$role_opt);
						Repo::insert($user);

						configRouter::goto('user/login');
						exit;
					}
					else
					{
						$message['form_err'] = "The passwords don't match";
					}
				}
			}

			$roles = Repo::selectAll('role');

			$select_options = array();

			foreach ($roles as $role)
			{
				$select_options[] = array(
				'name' => $role['role_name'],
				'value' => $role['id']
				);
			}

			$form = array(
				'register_FORM' => array(
					'method' => 'post',
					'action' => 'register',
					'inputs' => array(
						array(
							'name' => 'first name',
							'type' => 'text',
							'value' =>(isset($fname)? $fname:''),
							'err' => (isset($message['fname'])?$message['fname']:'')
						),
						array(
							'name' => 'last name',
							'type' => 'text',
							'value' =>(isset($lname)? $lname:''),
							'err' => (isset($message['lname'])?$message['lname']:'')
						),
						array(
							'name' => 'date of birth',
							'type' => 'date',
							'value' =>(isset($DoB)? $DoB:''),
							'err' => (isset($message['DoB'])?$message['DoB']:'')
						),
						array(
							'name' => 'email',
							'type' => 'email',
							'value' =>(isset($email)? $email:''),
							'err' => (isset($message['email'])?$message['email']:'')
						),
						array(
							'name' => 'username',
							'type' => 'text',
							'value' =>(isset($username)? $username:''),
							'err' => (isset($message['username'])?$message['username']:'')
						),
						array(
							'name' => 'password',
							'type' => 'password',
							'err' => (isset($message['pass'])?$message['pass']:'')
						),
						array(
							'name' => 'password confirm',
							'type' => 'password',
							'err' => (isset($message['pass_conf'])?$message['pass_conf']:'')
						),
						array(
							'name' => 'role',
							'type' => 'select',
							'chosen' => (isset($role_opt)? $role_opt:''),
							'options' => $select_options,
							'err' => (isset($message['role'])?$message['role']:'')
						),
						array(
							'name' => 'Register',
							'type' => 'button',
							'btn_type' => 'submit',
							'classes' => 'btn-primary form-control',
							'err' => (isset($message['form_err'])?$message['form_err']:'')
						),
						array(
							'name' => 'Cancel',
							'type' => 'button',
							'btn_type' => 'reset',
							'classes' => 'btn-primary form-control',
						)
					)
				)
			);

			viewLoader::loadView('user/register.php',$form);
		}
		else
		{
			configRouter::goto('ticket/index');
			exit;
		}
	}

}

?>
