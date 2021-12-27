<?php

class Ticket implements baseEntity
{
	private $id;
	private $title;
	private $content;
	private $image;
	private $visibility;
	private $maintenace_type;
	private $author_id;
	private $created_on;
	private $is_removed;

	function __construct($id = null , $title , $content , $image , $visibility , $maint_type , $author_id, $created_on, $is_removed)
	{
		if(!is_null($id))
		{
			$this->id = $id;
		}

		$this->title = $title;
		$this->content = $content;
		$this->image = $image;
		$this->visibility = $visibility;
		$this->maintenace_type  = $maint_type;
		$this->author_id = $author_id;
		$this->created_on = $created_on;
		$this->is_removed = $is_removed;
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
			rtrigger_error('Var type doesnt match',E_USER_ERROR);
		}
	}

	function checkFields()
	{
		if(is_string($this->title) && is_string($this->content) && (is_string($this->image) || is_null($this->image)) && is_int($this->visibility) && is_int($this->maintenace_type) && is_int($this->author_id) && is_string($this->created_on) && is_int($this->is_removed))
		{
			if($this->title != '' && $this->content != '' && $this->visibility != -1 && $this->maintenace_type != -1 && $this->author_id != -1 && ($this->created_on != '' && configValidate::validateDate($this->created_on , 'Y-m-d H:i:s')) && $this->is_removed != -1)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}

	function getAll()
	{
		return array($this->title, $this->content, $this->image, $this->visibility, $this->maintenace_type, $this->author_id, $this->created_on, $this->is_removed);
	}

	static function getPropNames()
	{
		return array('title','content','image','visibility','maintenace_type','author_id','created_on','is_removed');
	}

}


?>
