<?php

class TicketRepo implements baseRepo
{

	function __construct()
	{
		DBconfig::conn_start();
	}

	function selectAll($colArray = null)
	{
		if(is_null($colArray))
		{
			return DBconfig::select('tickets');
		}
		else
		{
			$validCols = array();

			if(in_array('id', $colArray))
			{
				$validCols[] = 'id';
			}

			foreach (Ticket::getPropNames() as $prop)
			{
				if(in_array($prop, $colArray))
				{
					$validCols[] = $prop;
				}
			}

			return DBconfig::select('tickets',$validCols);
		}
	}


	function selectOneById($id , $colArray = null)
	{
		if(is_int($id) && 0 < $id)
		{
			if(is_null($colArray))
			{
				return DBconfig::select('tickets',null,array('id'=>$id));
			}
			else
			{
				$validCols = array();

				if(in_array('id', $colArray))
				{
					$validCols[] = 'id';
				}

				foreach (Ticket::getPropNames() as $prop)
				{
					if(in_array($prop, $colArray))
					{
						$validCols[] = $prop;
					}
				}

				return DBconfig::select('tickets',$validCols,array('id'=>$id));
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

			foreach (Ticket::getPropNames() as $prop)
			{
				if(in_array($prop, array_keys($filterArray)))
				{
					$validFilters[$prop] = $filterArray[$prop];
				}
			}

			if(is_null($colArray))
			{
				return DBconfig::select('tickets',null,$validFilters);
			}
			else
			{
				$validCols = array();

				if(in_array('id', $colArray))
				{
					$validCols[] = 'id';
				}

				foreach (Ticket::getPropNames() as $prop)
				{
					if(in_array($prop, $colArray))
					{
						$validCols[] = $prop;
					}
				}

				return DBconfig::select('tickets',$validCols,$validFilters);
			}
		}
	}

	function insert(baseEntity $ticket){
		if($ticket->checkFields())
		{
			$data = array();

			foreach (Ticket::getPropNames() as $prop)
			{
				$data['fields'][] = $prop;
				$data['values'][':'.$prop] = $ticket->get($prop);
			}

			DBconfig::insert('tickets',$data);
		}
		else
		{
			trigger_error('Invalid ticket object');
		}
	}

	function update(baseEntity $ticket){
		if($ticket->checkFields())
		{
			$data = array();

			foreach (Ticket::getPropNames() as $prop)
			{
				$data['fields'][] = $prop;
				$data['values'][':'.$prop] = $ticket->get($prop);
			}

			$data['values'][':id'] = $ticket->get('id');

			DBconfig::update('tickets',$data);
		}
		else
		{
			trigger_error('Invalid ticket object');
		}
	}

	function delete($id){

		if(is_int($id) && 0 < $id)
		{
			DBconfig::delete('tickets',array(':id'=>$id));
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

