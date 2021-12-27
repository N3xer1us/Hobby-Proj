<?php

class configValidate
{

	static function validateDate($date, $format='Y-m-d')
	{
		$datetime = DateTime::createFromFormat($format, $date);
		return ($datetime && $datetime->format($format) == $date);
	}

	static function validateEmail($email)
	{
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}
}

?>
