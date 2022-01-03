<div class="container content">
	<div class="row">
		<div class="col-sm-4"></div>
		<div class="col-sm-4">
			<h2>User Profile</h2>
			<div class="row"><h4>UID: <span>[[id]]</span></h4></div>
			<div class="row"><h4>Username: <span>[[username]]</span></h4></div>
			<div class="row"><h4>Name: <span>[[full_name]]</span></h4></div>
			<div class="row"><h4>Role: <span>[[role]]</span></h4></div>
		</div>
		<div class="col-sm-4"></div>
	</div>
	<div class="row">
		<form method="post" action="index">
			<div class="form-group">
				<div class="col-md-2">
					<input type="text" name="ticketName" class="form-control" value="[[nameFilter]]">
				</div>
				<div class="col-md-2">
					<input type="date" name="startDate" class="form-control" value="[[startDateFilter]]">
				</div>
				<div class="col-md-2">
					<input type="date" name="endDate" class="form-control" value="[[endDateFilter]]">
				</div>
				<div class="col-md-2">
					<select class="form-control" name="ticketType">
						<option value="0" selected>Ticket Type</option>
						<option value="1">Office</option>
						<option value="2">Technical</option>
					</select>
				</div>
				<div class="col-md-2">
					<select class="form-control" name="ticketVisibility">
						<option value="0" selected>Visibility</option>
						<option value="1">Public</option>
						<option value="2">Private</option>
					</select>
				</div>
				<div class="col-md-2">
					<input type="submit" value="Filter" name="filter" class="btn btn-primary">
				</div>
			</div>
		</form>
	</div>
	<div class="row">
		[[MyTickets_TABLE]]
	</div>
</div>
