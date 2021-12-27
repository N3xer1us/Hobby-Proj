<div class="container content">
	<div class="col-sm-1"></div>
	<div class="col-sm-10">
		<?php
		if($_SESSION['currentUser']['role_id'] <= 3)
		{ ?>
		<div class="row">
		<h4>My Tickets [[add_ticket_BUTTON]]</h4>
		[[my_ticket_TABLE]]
		</div>
		<?php } ?>
		<hr>
		<div class="row">
		<h4>[[table_title]]</h4>
		[[tickets_TABLE]]
		</div>
	</div>
	<div class="col-sm-1"></div>
</div>
