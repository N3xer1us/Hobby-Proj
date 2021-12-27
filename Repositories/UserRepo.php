<?php

class UserRepo implements baseRepo
{

	function __construct()
	{
		DBconfig::conn_start();
	}

	function selectAll($colArray = null)
	{
		if(is_null($colArray))
		{
			return DBconfig::select('user');
		}
		else
		{
			$validCols = array();

			if(in_array('id', $colArray))
			{
				$validCols[] = 'id';
			}

			foreach (User::getPropNames() as $prop)
			{
				if(in_array($prop, $colArray))
				{
					$validCols[] = $prop;
				}
			}

			return DBconfig::select('user',$validCols);
		}
	}


	function selectOneById($id , $colArray = null)
	{
		if(is_int($id) && 0 < $id)
		{
			if(is_null($colArray))
			{
				return DBconfig::select('user',null,array('id'=>$id));
			}
			else
			{
				$validCols = array();

				if(in_array('id', $colArray))
				{
					$validCols[] = 'id';
				}

				foreach (User::getPropNames() as $prop)
				{
					if(in_array($prop, $colArray))
					{
						$validCols[] = $prop;
					}
				}

				return DBconfig::select('user',$validCols,array('id'=>$id));
			}

		}
		else
		{
			trigger_error('Id need to be integer and more than 0', E_USER_ERROR);
		}
	}

	function selectByFilter($filterArray , $colArray = null)
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

			foreach (User::getPropNames() as $prop)
			{
				if(in_array($prop, array_keys($filterArray)))
				{
					$validFilters[$prop] = $filterArray[$prop];
				}
			}

			if(is_null($colArray))
			{
				return DBconfig::select('user',null,$validFilters);
			}
			else
			{
				$validCols = array();

				if(in_array('id', $colArray))
				{
					$validCols[] = 'id';
				}

				foreach (User::getPropNames() as $prop)
				{
					if(in_array($prop, $colArray))
					{
						$validCols[] = $prop;
					}
				}

				return DBconfig::select('user',$validCols,$validFilters);
			}
		}
	}

	function insert(baseEntity $user){
		if($user->checkFields())
		{
			$data = array();

			foreach (User::getPropNames() as $prop)
			{
				$data['fields'][] = $prop;
				$data['values'][':'.$prop] = $user->get($prop);
			}

			DBconfig::insert('user',$data);
		}
		else
		{
			trigger_error('Invalid user object');
		}
	}

	function update(baseEntity $user){
		if($user->checkFields())
		{
			$data = array();

			foreach (User::getPropNames() as $prop)
			{
				$data['fields'][] = $prop;
				$data['values'][':'.$prop] = $user->get($prop);
			}

			$data['values'][':id'] = $user->get('id');

			DBconfig::update('user',$data);
		}
		else
		{
			trigger_error('Invalid user object');
		}
	}

	function delete($id){

		if(is_int($id) && 0 < $id)
		{
			DBconfig::delete('user',array(':id'=>$id));
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
