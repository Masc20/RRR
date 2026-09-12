<?php  

  include '../../connection/conn.php';

  $sql = "SELECT (SELECT COUNT(*) FROM tbl_candidates) AS 'numCand', 
  					(SELECT COUNT(*) FROM tbl_criteria) AS 'numCrit', 
  					(SELECT COUNT(*) FROM tbl_category) AS 'numCat',
  					(SELECT COUNT(*) FROM tbl_users 
  									WHERE user_type = 'Judge' OR user_type = 'Chairman') AS 'numJud' ";
  $resultStats = $conn->query($sql);

  $row = $resultStats->fetch_assoc();

  $conn->close();

?>

<!-- Icon Cards-->
<div class="row">
	<div class="col-xl-3 col-sm-6 mb-3">
	  <div class="card text-white bg-primary o-hidden h-100">
	    <div class="card-body">
	      <div class="card-body-icon">
	        <i class="fa fa-fw fa-group"></i>
	      </div>
	      <div class="mr-5"><?php echo $row['numCand']; ?> <?php echo $label; ?>s</div>
	    </div>
	    <a class="card-footer text-white clearfix small z-1" href="javascript:void(0);" 
	    	onclick="
	    			$('#content').load('candidates/candidates.php');
	    			$('.navbar-sidenav').find('.active').removeClass('active');
	    			$('#cand').addClass('active');
	    			">
	      <span class="float-left">View Details</span>
	      <span class="float-right">
	        <i class="fa fa-angle-right"></i>
	      </span>
	    </a>
	  </div>
	</div>
	<div class="col-xl-3 col-sm-6 mb-3">
	  <div class="card text-white bg-warning o-hidden h-100">
	    <div class="card-body">
	      <div class="card-body-icon">
	        <i class="fa fa-fw fa-star"></i>
	      </div>
	      <div class="mr-5"><?php echo $row['numCat']; ?> Categories</div>
	    </div>
	    <a class="card-footer text-white clearfix small z-1" href="javascript:void(0);"
	    	onclick="
	    		$('#content').load('category/category.php');
    			$('.navbar-sidenav').find('.active').removeClass('active');
    			$('#cat').addClass('active');
	    	">
	      <span class="float-left">View Details</span>
	      <span class="float-right">
	        <i class="fa fa-angle-right"></i>
	      </span>
	    </a>
	  </div>
	</div>
	<div class="col-xl-3 col-sm-6 mb-3">
	  <div class="card text-white bg-success o-hidden h-100">
	    <div class="card-body">
	      <div class="card-body-icon">
	        <i class="fa fa-fw fa-edit"></i>
	      </div>
	      <div class="mr-5"><?php echo $row['numCrit']; ?> Score Criteria</div>
	    </div>
	    <a class="card-footer text-white clearfix small z-1" href="javascript:void(0);"
	    	onclick="
	    		$('#content').load('criteria/criteria.php');
    			$('.navbar-sidenav').find('.active').removeClass('active');
    			$('#crit').addClass('active');
	    	">
	      <span class="float-left">View Details</span>
	      <span class="float-right">
	        <i class="fa fa-angle-right"></i>
	      </span>
	    </a>
	  </div>
	</div>
	<div class="col-xl-3 col-sm-6 mb-3">
	  <div class="card text-white bg-danger o-hidden h-100">
	    <div class="card-body">
	      <div class="card-body-icon">
	        <i class="fa fa-fw fa-address-card"></i>
	      </div>
	      <div class="mr-5"><?php echo $row['numJud']; ?> Judges Account</div>
	    </div>
	    <a class="card-footer text-white clearfix small z-1" href="javascript:void(0);"
	    	onclick="
	    		$('#content').load('accounts/accounts.php');
    			$('.navbar-sidenav').find('.active').removeClass('active');
    			$('#jud').addClass('active');
	    	">
	      <span class="float-left">View Details</span>
	      <span class="float-right">
	        <i class="fa fa-angle-right"></i>
	      </span>
	    </a>
	  </div>
	</div>

</div>
