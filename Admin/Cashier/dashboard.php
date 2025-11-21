<body>
	<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
		<div class="row">
			<ol class="breadcrumb">
				<li><a href="#">
					<!-- Placeholder for home icon -->
					<img src="img/house.png" alt="Home Icon" style="width: 20px; height: 20px;">
				</a></li>
				<li class="active">Dashboard</li>
			</ol>
		</div><!--/.row-->

		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">Dashboard</h1>
			</div>
		</div>

		<div class="panel panel-container">
			<div class="row">
				<div class="col-xs-6 col-md-3 col-lg-3 no-padding">
					<div class="panel panel-teal panel-widget border-right">
						<div class="row no-padding">
							<img src="img/images.png" alt="Room Icon" style="width: 200px; height: 200px;">
							<div class="large"><?php include 'counters/room-count.php'?></div>
							<div class="text-muted"></div>
						</div>
					</div>
				</div>
				<div class="col-xs-6 col-md-3 col-lg-3 no-padding">
					<div class="panel panel-blue panel-widget border-right">
						<div class="row no-padding">
							<img src="img/reserved.png" alt="Reservation Icon" style="width: 180px; height: 200px;">
							<div class="large"><?php include 'counters/reserve-count.php'?></div>
							<div class="text-muted"></div>
						</div>
					</div>
				</div>
				<div class="col-xs-6 col-md-3 col-lg-3 no-padding">
					<div class="panel panel-orange panel-widget border-right">
						<div class="row no-padding">
							<img src="img/grouping.png" alt="Staff Icon" style="width: 200px; height: 200px;">
							<div class="large"><?php include 'counters/staff-count.php'?></div>
							<div class="text-muted"></div>
						</div>
					</div>
				</div>
				<div class="col-xs-6 col-md-3 col-lg-3 no-padding">
					<div class="panel panel-red panel-widget">
						<div class="row no-padding">
							<img src="img/feedback.png" alt="Feedback Icon" style="width: 200px; height: 200px;">
							<div class="large"><?php include 'counters/complaints-count.php'?></div>
							<div class="text-muted"></div>
						</div>
					</div>
				</div>
			</div><!--/.row-->

			<hr>

			<div class="row">
				<div class="col-xs-6 col-md-3 col-lg-3 no-padding">
					<div class="panel panel-teal panel-widget border-right">
						<div class="row no-padding">
							<img src="img/9150508.png" alt="Booked Rooms Icon" style="width: 200px; height: 200px;">
							<div class="large"><?php include 'counters/bookedroom-count.php'?></div>
							<div class="text-muted"></div>
						</div>
					</div>
				</div>
				<div class="col-xs-6 col-md-3 col-lg-3 no-padding">
					<div class="panel panel-blue panel-widget border-right">
						<div class="row no-padding">
							<img src="img/available.png" alt="Available Rooms Icon" style="width: 200px; height: 200px;">
							<div class="large"><?php include 'counters/avrooms-count.php'?></div>
							<div class="text-muted"></div>
						</div>
					</div>
				</div>
				<div class="col-xs-6 col-md-3 col-lg-3 no-padding">
					<div class="panel panel-orange panel-widget border-right">
						<div class="row no-padding">
							<img src="img/61e7cd50c8a56a9b30e57c94_self-check-in.png" alt="Checked In Icon" style="width: 200px; height: 200px;">
							<div class="large"><?php include 'counters/checkedin-count.php'?></div>
							<div class="text-muted"></div>
						</div>
					</div>
				</div>
			</div>
			<hr>

			<!--<div class="row">
				<div class="col-xs-6 col-md-4 col-lg-4 no-padding">
					<div class="panel panel-red panel-widget border-right">
						<div class="row no-padding">
							<img src="" alt="Income Icon" style="width: 40px; height: 40px;">
							<div class="large">P<?php include 'counters/income-count.php'?></div>
							<div class="text-muted">Total Earnings</div>
						</div>
					</div>
				</div>
				<div class="col-xs-6 col-md-4 col-lg-4 no-padding">
					<div class="panel panel-orange panel-widget">
						<div class="row no-padding">
							<img src="" alt="Pending Payment Icon" style="width: 40px; height: 40px;">
							<div class="large">P<?php include 'counters/pendingpayment.php'?></div>
							<div class="text-muted">Pending Payment</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div></.main-->
</body>
</html>
