<?php

	include '../../connection/conn.php';

	$sql = "SELECT * FROM tbl_candidates ORDER BY cand_no ASC";
	$resultCand = $conn->query($sql);

?>

<div id="cars" class="carousel slide" data-ride="carousel">

  <ul class="carousel-indicators">

  <?php
  	
  	$ctr = 0;

  	while ($resultCand->num_rows >= $ctr) {

  ?>

    <li data-target="#cars" data-slide-to="<?php echo $ctr; ?>" 
    	class = "<?php if ($ctr == 0) echo 'active'; ?>"></li>

  <?php

  		$ctr++;
  	}
  ?>

  </ul>

  <div class="carousel-inner">

  <?php

  	$ctr = 0;
  	while ($row = $resultCand->fetch_assoc()) {

  ?>

    <div class="carousel-item <?php if ($ctr == 0) echo 'active'; ?>">
      <img src="../uploads/<?php echo $row['cand_pic']; ?>" alt="Images">
      
      <?php
      	if ($label == 'Candidate') {	
      ?>

      <div class="carousel-caption">
        <h3><?php echo $label; ?> No. <?php echo $row['cand_no']; ?></h3>
        <h6><?php echo $row['cand_name']; ?></h6>
      </div>

      <?php
      	}
      ?> 

    </div>
   
  <?php

  	$ctr++;
  	}
  ?>

  </div>

  <a class="carousel-control-prev" href="#cars" data-slide="prev">
    <span class="carousel-control-prev-icon"></span>
  </a>
  <a class="carousel-control-next" href="#cars" data-slide="next">
    <span class="carousel-control-next-icon"></span>
  </a>

</div>

<?php
  $conn->close();
?>

<script>
  $("#cars").carousel({interval: 2000});
</script>