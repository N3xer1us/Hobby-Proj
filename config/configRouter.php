<?php

define('BASE_URL', 'http://localhost/coursework/');

class configRouter
{
	static $routes = array();

	static function registerController($controllerName , $actionArray)
	{
		if(is_array($actionArray) && is_string($controllerName))
		{
			self::$routes[$controllerName] = $actionArray;
		}
		else
		{
			trigger_error('Invalid controller registration',E_USER_ERROR);
		}
	}

	static function route($path)
	{
		$pathArray = explode('/', $path);

		if(isset($pathArray[2]) && $pathArray[2] != '')
		{
			$controllerName = $pathArray[2].'Controller';
			$controller = new $controllerName();

			if(isset($pathArray[3]) && $pathArray[3] != '')
			{
				if(in_array($pathArray[3], self::$routes[$pathArray[2]]))
				{
					$controller->callAction($pathArray[3]);
				}
			}
			else
			{
				$controller->callAction('index');
			}
		}
	}

	static function get_link($path = null)
	{
		if(isset($path))
		{
			$link = BASE_URL . $path;
		}
		else
		{
			$link = BASE_URL;
		}

		return $link;
	}

	static function goto($path){
		header('Location: '.BASE_URL.$path);
	}
}

?>
