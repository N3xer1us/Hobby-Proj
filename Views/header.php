<!DOCTYPE html>
<html>
<head>
	<title>Course Work</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
	<link rel="stylesheet" type="text/css" href="/coursework/resources/main.css">
</head>
<body>
	<nav class="navbar navbar-inverse navbar-fixed-top">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a href="http://localhost/coursework/" class="navbar-brand">CW</a>
			</div>
			<div class="collapse navbar-collapse" id="navbar">
				<ul class="nav navbar-nav navbar-left">
					<li><a href="<?php echo configRouter::get_link('ticket/index');; ?>">Home</a></li>
<!--					<li><a href="<?php echo configRouter::get_link('ticket/index'); ?>" >Tickets</a></li>-->
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<li><a href="<?php echo configRouter::get_link('user/index'); ?>"><span class="glyphicon glyphicon-user"></span></a></li>
					<li>
						<?php echo (isset($_SESSION['currentUser'])? '<a href="http://localhost/coursework/user/logout">Logout</a>': '<a href="http://localhost/coursework/user/login">Login</a>'); ?>
					</li>
				</ul>
			</div>
		</div>
	</nav>
	<div class="container header-content">
		<div class="page-header">
			<h1>Course Work for Web Server Languages</h1>
		</div>
	</div>

