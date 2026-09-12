<!-- Breadcrumbs-->
<ol class="breadcrumb">
	<li class="breadcrumb-item">
	  <a href="">Dashboard</a>
	</li>
	<li class="breadcrumb-item active">Categories</li>
</ol>

<!-- Example DataTables Card-->
<div class="card mb-3">
	<div class="card-header">
	  <i class="fa fa-table"></i> Categories Information
	  <button class="pull-right btn btn-primary" data-toggle="modal" data-target="#formModal" 
	  		onclick="
  					$('#myform').load('category/custom_form.php', function () {
						create('category');
					});
  					">
	  	<i class="fa fa-plus"></i> Add New
	  </button>
	</div>
	<div class="card-body" id="records">
	
	</div>
	<div class="card-footer small text-muted"></div>
</div>

<script>
	$('#records').load('category/view.php');
</script>

