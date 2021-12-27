<?php
session_start();

include 'config/autoLoader.php';
include 'Views/header.php';

if(isset($_SESSION['currentUser']) || $_SERVER['REDIRECT_URL'] == '/coursework/user/register' || $_SERVER['REDIRECT_URL'] == '/coursework/user/login')
{
	if(!empty($_SERVER['REDIRECT_URL']))
	{
		configRouter::route($_SERVER['REDIRECT_URL']);
	}
}
else
{
	configRouter::goto('user/login');
}

include 'Views/footer.php';
?>
