<!-- Nav Tabs -->
<ul class="nav nav-tabs nav-pills" role="tablist" id="myTab">

<?php

include '../../connection/conn.php';

$sql = "SELECT * FROM tbl_category 
        ORDER BY category_id ASC";
$resultCat = $conn->query($sql);

if ($resultCat->num_rows > 0) {

  while($row = $resultCat->fetch_assoc()) {

    $categoryId = $row['category_id'];
  ?>

  <li class="nav-item">
    <a class="nav-link" data-toggle="tab" href="#menu<?php echo $categoryId;?>">
      <?php echo $row['category_name'];?>
    </a>
  </li>

  <?php
  }
}

?>

</ul>

<!-- Tab Content -->
<div class="tab-content">
				  
  <?php 

    $resultCat = $conn->query($sql);

    if ($resultCat->num_rows > 0) {

      while($row = $resultCat->fetch_assoc()) {

      $categoryId = $row['category_id'];

  ?>

  <div id="menu<?php echo $categoryId;?>" class="container tab-pane fade"><br>
  
  	<table class="table-hover table" width="100%" cellspacing="0" style="font-size: 18px;">

  		<?php

  			$sql = "SELECT * FROM tbl_criteria 
  					WHERE category_id = '$categoryId'";

  			$resultCriteria = $conn->query($sql);

  			if ($resultCriteria->num_rows > 0) {

  				$tot = 0;

      			while($row = $resultCriteria->fetch_assoc()) {

      				$tot += $row['criteria_points'];
  				?>
  					<tr>
			  			<td><?php echo $row['criteria_name']; ?></td>
			  			<td align="right">
			  				<span class="text-danger"><?php echo $row['criteria_points']; ?> </span>pts	
			  			</td>
			  		</tr>
  				<?php
      			}

      			?>

      				<tr>
			  			<td><strong>Total</strong></td>
			  			<td align="right">
			  				<strong><span class="text-danger"><?php echo $tot; ?> </span>pts</strong>	
			  			</td>
			  		</tr>
      			<?php
      		}

  		?>

  	</table>
  
  </div>
  <!-- end div tab -->

  <?php
      }
    }

    $conn->close();
  ?>

</div>


<script>
  $('#myTab a:first').tab('show');
</script>