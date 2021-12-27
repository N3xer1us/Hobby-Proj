<?php

spl_autoload_register('autoLoad');

function autoLoad($className)
{
	$ext = '.php';

	if(strpos($className, 'Repo') !== false || $className == 'Repo')
	{
		$folder = 'Repositories/';
	}
	else if(strpos($className, 'config') !== false || $className == 'config')
	{
		$folder = 'config/';
	}
	else if(strpos($className, 'Controller') !== false || $className == 'Controller')
	{
		$folder = 'Controllers/';
	}
	else if(strpos($className, 'view') !== false || $className == 'View')
	{
		$folder = 'Views/';
	}
	else
	{
		$folder = 'Entities/';
	}

	include_once $folder.$className.$ext;
}

?>
