<?php include '../config/config.php'; ?>

<!-- Breadcrumbs-->
<ol class="breadcrumb">
	<li class="breadcrumb-item">
	  <a href="">Dashboard</a>
	</li>
	<li class="breadcrumb-item active"><?php echo $row['based_type']; ?>s Entry</li>
</ol>

<!-- Example DataTables Card-->
<div class="card mb-3">
	<div class="card-header">
	  <i class="fa fa-table"></i> <?php echo $row['based_type']; ?>s Information
	  <button class="pull-right btn btn-primary" data-toggle="modal" data-target="#formModal" 
	  		onclick="
	  				$('#myform').load('candidates/custom_form.php', function () {
	  					create('candidates');
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

$('#records').load('candidates/view.php');

function preview_pic(objFileInput) {

    if (objFileInput.files[0]) {

      var fileReader = new FileReader();

      fileReader.onload = function (e) {
        $('img[name=pic]').attr("src", e.target.result);
      }

      fileReader.readAsDataURL(objFileInput.files[0]);
    }
}
</script>
