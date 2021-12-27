<?php

class Comment implements baseEntity
{
	private $id;
	private $content;
	private $image;
	private $author_id;
	private $ticket_id;
	private $comment_id;
	private $created_on;
	private $is_removed;

	function __construct($id = null , $content , $image , $author_id , $ticket_id , $comment_id = null, $created_on, $is_removed)
	{
		if(!is_null($id))
		{
			$this->id = $id;
		}

		if(!is_null($comment_id))
		{
			$this->comment_id = $comment_id;
		}

		$this->content = $content;
		$this->image = $image;
		$this->author_id = $author_id;
		$this->ticket_id = $ticket_id;
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
			trigger_error('Var type doesnt match',E_USER_ERROR);
		}
	}

	function checkFields()
	{
		if(is_string($this->content) && (is_string($this->image) || is_null($this->image)) && (is_int($this->comment_id) || is_null($this->comment_id)) && is_int($this->author_id) && is_int($this->ticket_id) && (is_string($this->created_on) && configValidate::validateDate($this->created_on, 'Y-m-d H:i:s')) && is_int($this->is_removed))
		{
			if($this->content != '' && $this->author_id != -1 && $this->ticket_id != -1 && $this->created_on != '' && $this->is_removed != -1)
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
		return array($this->content, $this->image, $this->author_id, $this->ticket_id, $this->comment_id, $this->created_on, $this->is_removed);
	}

	static function getPropNames()
	{
		return array('content','image','author_id','ticket_id','comment_id','created_on','is_removed');
	}

}

?>
