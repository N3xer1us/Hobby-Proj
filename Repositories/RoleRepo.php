<?php

class RoleRepo implements baseRepo
{

	function __construct()
	{
		DBconfig::conn_start();
	}

	function selectAll($colArray = null)
	{
		if(is_null($colArray))
		{
			return DBconfig::select('roles');
		}
		else
		{
			$validCols = array();

			if(in_array('id', $colArray))
			{
				$validCols[] = 'id';
			}

			foreach (Role::getPropNames() as $prop)
			{
				if(in_array($prop, $colArray))
				{
					$validCols[] = $prop;
				}
			}

			return DBconfig::select('roles',$validCols);
		}
	}


	function selectOneById($id, $colArray = null)
	{
		if(is_int($id) && 0 < $id)
		{
			if(is_null($colArray))
			{
				return DBconfig::select('roles',null,array('id'=>$id));
			}
			else
			{
				$validCols = array();

				if(in_array('id', $colArray))
				{
					$validCols[] = 'id';
				}

				foreach (Role::getPropNames() as $prop)
				{
					if(in_array($prop, $colArray))
					{
						$validCols[] = $prop;
					}
				}

				return DBconfig::select('roles',$validCols,array('id'=>$id));
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

			foreach (Role::getPropNames() as $prop)
			{
				if(in_array($prop, array_keys($filterArray)))
				{
					$validFilters[$prop] = $filterArray[$prop];
				}
			}

			if(is_null($colArray))
			{
				return DBconfig::select('roles',null,$validFilters);
			}
			else
			{
				$validCols = array();

				if(in_array('id', $colArray))
				{
					$validCols[] = 'id';
				}

				foreach (Role::getPropNames() as $prop)
				{
					if(in_array($prop, $colArray))
					{
						$validCols[] = $prop;
					}
				}

				return DBconfig::select('roles',$validCols,$validFilters);
			}
		}
	}

	function insert(baseEntity $role){
		if($role->checkFields())
		{
			$data = array();

			foreach (Role::getPropNames() as $prop)
			{
				$data['fields'][] = $prop;
				$data['values'][':'.$prop] = $role->get($prop);
			}

			DBconfig::insert('roles',$data);
		}
		else
		{
			trigger_error('Invalid role object');
		}
	}

	function update(baseEntity $role){
		if($role->checkFields())
		{
			$data = array();

			foreach (Role::getPropNames() as $prop)
			{
				$data['fields'][] = $prop;
				$data['values'][':'.$prop] = $role->get($prop);
			}

			$data['values'][':id'] = $role->get('id');

			DBconfig::update('roles',$data);
		}
		else
		{
			trigger_error('Invalid role object');
		}
	}

	function delete($id){

		if(is_int($id) && 0 < $id)
		{
			DBconfig::delete('roles',array(':id'=>$id));
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

