<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'course_work');
define('DB_USER', 'root');
define('DB_PASS', '');

class DBconfig{

	private static $conn;

	static function conn_start()
	{
		try
		{
			self::$conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER , DB_PASS);
		} catch (PDOException $e) {
			echo 'Coudnt connect to Database';
		}
	}

	static function conn_end()
	{
		self::$conn = null;
	}

	private static function array_alter_keys($array , $column_name)
	{
		$array_result = array();

		foreach ($array as $element)
		{
			$array_result[$element[$column_name]] = $element;
		}

		return $array_result;
	}

	static function select($table_name ,$colArray = null , $filterArray = null )
	{
		$res = false;

		if(is_null($colArray) && empty($colArray))
		{
			$cols = '*';
		}
		else
		{
			$cols = implode(',', $colArray);
		}

		if(is_null($filterArray))
		{
			$res = self::$conn->query('SELECT '.$cols.' FROM '.$table_name, PDO::FETCH_ASSOC);
		}
		else
		{
			$where = [];

			foreach ($filterArray as $k=>$v)
			{
				if(is_array($v))
				{
					$v[0] = strtoupper($v[0]);

					if(str_contains($v[0], 'NOT'))
					{
						$negation = ' NOT';
						$v[0] = trim(substr($v[0], strpos($v[0], 'NOT') + 3));
					}
					else
					{
						$negation = '';
					}

					switch ($v[0])
					{
						case '=':
							$where[]= $k.' = '.self::$conn->quote($v[1]);
							break;
						case '<':
							$where[]= $k.' < '.self::$conn->quote($v[1]);
							break;
						case '>':
							$where[]= $k.' > '.self::$conn->quote($v[1]);
							break;
						case '<=':
							$where[]= $k.' <= '.self::$conn->quote($v[1]);
							break;
						case '>=':
							$where[]= $k.' >= '.self::$conn->quote($v[1]);
							break;
						case '<>':
							$where[]= $k.' <> '.self::$conn->quote($v[1]);
							break;
						case '!=':
							$where[]= $k.' != '.self::$conn->quote($v[1]);
							break;
						case '<=>':
							$where[]= $k.' <=> '.self::$conn->quote($v[1]);
							break;
						case 'LIKE':
							$where[]= $k.$negation.' LIKE '.self::$conn->quote('%'.$v[1].'%');
							break;
						case 'IN':
							if(is_array($v[1]))
							{
								$tmp = array();
								foreach ($v[1] as $el)
								{
									$tmp[] = self::$conn->quote($el);
								}
								$where[]=$k.$negation.' IN('.implode(', ', $tmp).')';
							}
							else
							{
								$where[]= $k.$negation.' IN('.self::$conn->quote($v[1]).')';
							}
							break;
						case 'BETWEEN':
							if(count($v) == 3)
							{
								$where[]= $k.$negation.' BETWEEN '.self::$conn->quote($v[1]).' AND '.self::$conn->quote($v[2]);
							}
							else
							{
								trigger_error('BETWEEN needs two values ,  you gave one', E_WARNING);
							}
							break;
						case 'IS NULL':
							$where[]= $k.' IS'.$negation.' NULL';
							break;
//						case 'EXISTS':
//							break;
					}
				}
				else
				{
					$where[]= $k.' = '.self::$conn->quote($v);
				}
			}

			$res = self::$conn->query('SELECT '.$cols.' FROM '.$table_name.' WHERE '.implode(' AND ', $where) , PDO::FETCH_ASSOC);
		}

		if($res != false && $res->rowCount() > 0)
		{
//			$result = $res->fetchAll();
//			return self::array_key_id($result);
			return $res->fetchAll();
		}
		else
		{
			return 'No matches were found';
		}
	}

	static function insert($table_name, $data)
	{

		$query = self::$conn->prepare('INSERT INTO '.$table_name.'('.implode(',',$data['fields']).') VALUES('.implode(',',array_keys($data['values'])).')');

		$res = $query->execute($data['values']);

		if($res == false)
		{
			trigger_error('There was an Error',E_USER_ERROR);
		}
	}

	static function update($table_name, $data)
	{
		$set = array();

		foreach ($data['fields'] as $v) {
			$set[] = $v .' = :'.$v.' ';
		}

		$query = self::$conn->prepare('UPDATE '.$table_name.' SET '.implode(' , ', $set).' WHERE id = :id');

		$res = $query->execute($data['values']);

		if($res == false)
		{
			trigger_error('There was an Error',E_USER_ERROR);
		}
	}

	static function delete($table_name , $data)
	{

		$query = self::$conn->prepare('UPDATE '.$table_name.' SET is_removed = 1 , removed_on = "' . date('Y/m/d H:i:s') . '" WHERE id = :id');
		$res = $query->execute($data);

		if($res == false)
		{
			trigger_error('There was an Error', E_USER_ERROR);
		}
	}

}
?>