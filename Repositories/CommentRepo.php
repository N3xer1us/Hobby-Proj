<?php

class CommentRepo implements baseRepo
{
	private $conn;

	function __construct()
	{
		DBconfig::conn_start();
	}

	function selectAll($colArray = null)
	{
		if(is_null($colArray))
		{
			return DBconfig::select('comments');
		}
		else
		{
			$validCols = array();

			if(in_array('id', $colArray))
			{
				$validCols[] = 'id';
			}

			foreach (Comment::getPropNames() as $prop)
			{
				if(in_array($prop, $colArray))
				{
					$validCols[] = $prop;
				}
			}

			return DBconfig::select('comments',$validCols);
		}
	}


	function selectOneById($id, $colArray = null)
	{
		if(is_int($id) && 0 < $id)
		{
			if(is_null($colArray))
			{
				return DBconfig::select('comments',null,array('id'=>$id));
			}
			else
			{
				$validCols = array();

				if(in_array('id', $colArray))
				{
					$validCols[] = 'id';
				}

				foreach (Comment::getPropNames() as $prop)
				{
					if(in_array($prop, $colArray))
					{
						$validCols[] = $prop;
					}
				}

				return DBconfig::select('comments',$validCols,array('id'=>$id));
			}

		}
		else
		{
			trigger_error('Id need to be integer and more than 0', E_USER_ERROR);
		}
	}

	function selectByFilter($filterArray, $colArray = null)
	{
		if(!is_array($filterArray))
		{
			trigger_error('Array expected', E_USER_ERROR);
		}
		else
		{
			$validFilters = array();

			if(in_array('id', array_keys($filterArray)))
			{
				$validFilters['id'] = $filterArray['id'];
			}

			foreach (Comment::getPropNames() as $prop)
			{
				if(in_array($prop, array_keys($filterArray)))
				{
					$validFilters[$prop] = $filterArray[$prop];
				}
			}

			if(is_null($colArray))
			{
				return DBconfig::select('comments',null,$validFilters);
			}
			else
			{
				$validCols = array();

				if(in_array('id', $colArray))
				{
					$validCols[] = 'id';
				}

				foreach (Comment::getPropNames() as $prop)
				{
					if(in_array($prop, $colArray))
					{
						$validCols[] = $prop;
					}
				}

				return DBconfig::select('comments',$validCols,$validFilters);
			}
		}
	}

	function insert(baseEntity $comment){
		if($comment->checkFields())
		{
			$data = array();

			foreach (Comment::getPropNames() as $prop)
			{
				$data['fields'][] = $prop;
				$data['values'][':'.$prop] = $comment->get($prop);
			}

			DBconfig::insert('comments',$data);
		}
		else
		{
			trigger_error('Invalid comment object');
		}
	}

	function update(baseEntity $comment){
		if($comment->checkFields())
		{
			$data = array();

			foreach (Comment::getPropNames() as $prop)
			{
				$data['fields'][] = $prop;
				$data['values'][':'.$prop] = $comment->get($prop);
			}

			$data['values'][':id'] = $comment->get('id');

			DBconfig::update('comments',$data);
		}
		else
		{
			trigger_error('Invalid comment object');
		}
	}

	function delete($id){

		if(is_int($id) && 0 < $id)
		{
			DBconfig::delete('comments',array(':id'=>$id));
		}
		else
		{
			trigger_error('Id needs to be integer');
		}
	}

	function __destruct()
	{
		DBconfig::conn_end();
	}

}

?>

