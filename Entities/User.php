<?php

class User implements baseEntity
{
	private $id;
	private $username;
	private $first_name;
	private $last_name;
	private $email;
	private $pass;
	private $DoB;
	private $role_id;

	function __construct($id = null, $username, $first_name, $last_name, $email, $pass, $DoB, $role_id)
	{
		if(!is_null($id))
		{
			$this->id = $id;
		}

		$this->username = $username;
		$this->first_name = $first_name;
		$this->last_name = $last_name;
		$this->email = $email;
		$this->pass = $pass;
		$this->DoB = $DoB;
		$this->role_id = $role_id;
	}

	function get($var_name)
	{
		return $var = $this->$var_name;
	}

	function set($var_name , $var_val)
	{
		$type = gettype($this->$var_name);

		if(gettype($var_val) == $type)
		{
			$this->$var_name = $var_val;
		}
		else
		{
			trigger_error('Var type doesnt match',E_USER_ERROR);
		}
	}

	function checkFields()
	{
		if(is_string($this->username) && is_string($this->first_name) && is_string($this->last_name) && is_string($this->email) && is_string($this->pass) && is_string($this->DoB) && is_int($this->role_id))
		{
			if($this->username != '' && $this->first_name != '' && $this->last_name != '' && ($this->email != '' && configValidate::validateEmail($this->email)) && $this->pass != '' && ($this->DoB != '' && configValidate::validateDate($this->DoB, 'Y-m-d')) && $this->role_id != 0)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	function getAll()
	{
		return array($this->username, $this->first_name, $this->last_name, $this->email, $this->pass, $this->DoB, $this->role_id);
	}

	static function getPropNames()
	{
		return array('username', 'first_name', 'last_name', 'email', 'pass', 'DoB', 'role_id');
	}
}

?>
