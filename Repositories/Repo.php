<?php

class Repo {

	private static function validateRepo($name)
	{
		$name = ucfirst($name);
		$repoName = $name .'Repo';
		try{
			return new $repoName();
		} catch (Exception $ex) {
			trigger_error('No such repo exists!!', E_USER_ERROR);
		}

	}

	static function selectAll($repoName,$colArray = null){
		$repo = self::validateRepo($repoName);

		if(is_null($colArray) && empty($colArray))
		{
			return $repo->selectAll();
		}
		else
		{
			return $repo->selectAll($colArray);
		}
	}

	static function selectOneById($repoName, $id, $colArray = null){
		$repo = self::validateRepo($repoName);

		if(is_null($colArray) && empty($colArray))
		{
			return $repo->selectOneById($id)[0];
		}
		else
		{
			return $repo-> selectOneById($id, $colArray)[0];
		}
	}

	static function selectByFilter($repoName, $filterArray , $colArray = null){
		$repo = self::validateRepo($repoName);

		if(is_null($colArray) && empty($colArray))
		{
			return $repo->selectByFilter($filterArray);
		}
		else
		{
			return $repo->selectByFilter($filterArray,$colArray);
		}
	}

	static function insert(baseEntity $object){
		$repo = self::validateRepo(get_class($object));
		return $repo->insert($object);
	}

	static function update(baseEntity $object){
		$repo = self::validateRepo(get_class($object));
		return $repo->update($object);
	}

	static function delete($repoName, $id){
		$repo = self::validateRepo($repoName);
		return $repo->delete($id);
	}

}

?>
