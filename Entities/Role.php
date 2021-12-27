<?php

class Role implements baseEntity
{
	private $id;
	private $role_name;

	function __construct($id = null , $role_name)
	{
		
		if(!is_null($id))
		{
			$this->id = $id;
		}

		$this->role_name = $role_name;
	}

	function get($var_name)
	{
		return $this->$var_name;
	}

	function set($var_name, $var_val)
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
		if(is_string($this->role_name))
		{
			if($this->role_name != '')
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
		return array($this->role_name);
	}

	static function getPropNames()
	{
		return array('role_name');
	}
}

?>
