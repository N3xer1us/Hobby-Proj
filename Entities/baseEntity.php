<?php

interface baseEntity
{
	function get($var_name);
	function set($var_name , $var_val);
	function checkFields();
	static function getPropNames();
	function getAll();
}

?>
