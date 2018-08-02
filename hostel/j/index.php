<!doctype html>
<html lang="en">
<head>
	<title>Hostel J</title>
	<?php require_once('meta-tags.html'); ?>
<style>
.Hostel-name{padding:20px; }
.TOD{text-align:left; padding:5px; }
</style>
</head>
<?php 
session_start();
$_SESSION['_pdoConnect']=1; require_once('../../php/connect/_pdoConnect.php');
?>
<body style="width:100%">
	<section class="MainContent" style="width:100%">
		<h1 class="Hostel-name">Hostel J</h1><hr/>
		<div class="TOD">
			<p class="ID-item">Date : 21/01/2018</p>
			<p class="ID-item">Day : Sunday</p>
		</div>
		<section class="TVS">
			<h3 class="TVS-title">Breakfast</h3>
			<div class="TVS-div">
				<div class="TVS-summary">
					<p class="TVS-info">Timing : 08:30 AM to 09:30 AM</p>
					<p class="TVS-info">Items : Aloo Paratha, Poha, Daliya, Omlet</p>
				</div><hr/>
				</div>
			</div>
		</section>
		<section class="TVS">
			<h3 class="TVS-title">Lunch</h3>
			<div class="TVS-div">
				<div class="TVS-summary">
					<p class="TVS-info">Timing : 01:00 PM to 02:00 PM</p>
					<p class="TVS-info">Items : Matar Panner, Yellow Dal, Mixed Veg, Roti, Chawal</p>
				</div><hr/>
				</div>
			</div>
		</section>
		<section class="TVS">
			<h3 class="TVS-title">Dinner</h3>
			<div class="TVS-div">
				<div class="TVS-summary">
					<p class="TVS-info">Timing : 08:00 PM to 09:00 AM</p>
					<p class="TVS-info">Items : Kofta, Roti, Tomato Soup</p>
				</div><hr/>
				</div>
			</div>
		</section>
	</section>
</body>