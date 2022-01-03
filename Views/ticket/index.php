<div class="container content">
	<div class="col-sm-1"></div>
	<div class="col-sm-10">
		<div class="row">
			<form method="post" action="index">
				<div class="form-group">
					<div class="col-md-2">
						<input type="text" name="ticketName" class="form-control" value="[[nameFilter]]">
					</div>
					<div class="col-md-2">
						<input type="text" name="authorName" class="form-control" value="[[authorFilter]]">
					</div>
					<div class="col-md-2">
						<input type="date" name="startDate" class="form-control" value="[[startDateFilter]]">
					</div>
					<div class="col-md-2">
						<input type="date" name="endDate" class="form-control" value="[[endDateFilter]]">
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
			<h4>[[table_title]]</h4>
			[[tickets_TABLE]]
		</div>
	</div>
	<div class="col-sm-1"></div>
</div>
