<!-- Breadcrumbs-->
<ol class="breadcrumb">
	<li class="breadcrumb-item">
	  <a href="">Dashboard</a>
	</li>
	<li class="breadcrumb-item active">My Dashboard</li>
</ol>

<!-- label type -->
<?php 
	include '../config/config.php'; 
	$label = $row['based_type'];
?>

<!-- Icon cards -->
<?php include 'icon-cards.php'; ?>

<div class="row">

	<div class="col">
		<!-- Preview candidates -->
		<div class="card mb-3 border">
			<div class="card-header">
				<i class="fa fa-youtube-play"></i> Preview <?php echo $label; ?>s
			</div>
			<div class="card-body p-5">

				<?php include 'candidates.php'; ?>
			</div>
		</div>
	</div>

	<div class="col">
		<!-- Connected Judges -->
		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-user"></i> Connected Judges
			</div>
			<div class="card-body" id="OnlineJud">
				Loading . . .
			</div>
		</div>
		<!-- Criteria for judging -->
		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-edit"></i> Criteria for Judging
			</div>
			<div class="card-body">

				<?php include 'criteria.php'; ?>
			</div>
		</div>
	</div>

</div>
