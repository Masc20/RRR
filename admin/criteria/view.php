<div class="table-responsive">

<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">

<thead>
	<tr>
    <th>Category</th>
	  <th>Criteria Name</th>
    <th>Description</th> 
	  <th>Criteria Points</th>
	  <th>Actions</th>
	</tr>
</thead>

<tbody>

<?php  

include '../../connection/conn.php';

$sql = "SELECT * FROM tbl_criteria AS r, tbl_category AS c 
        WHERE r.category_id = c.category_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        
      $criteriaId = $row['criteria_id'];

      $categoryId = $row['category_id'];
      $categoryName = $row['category_name'];

      $criteriaName = $row['criteria_name'];
      $descrp = $row['criteria_descrp'];
      $points = $row['criteria_points'];

    	?>

		    <tr>
          <td align="center"><?php echo $categoryName; ?></td>
          <td align="center"><?php echo $criteriaName; ?></td>
          <td align="center"><?php echo $descrp; ?></td>
          <td align="center"><?php echo $points; ?></td>
          
          <td align="center">
          	<button type="button" class="btn btn-success btn-circle" data-toggle="modal" data-target="#formModal" 
                    onclick="
                            $('#myform').load('criteria/custom_form.php', function () {
                              
                              retrieve('<?php echo $criteriaId; ?>',
                                    '<?php echo $categoryId; ?>',
                                    '<?php echo $criteriaName; ?>',
                                    '<?php echo $descrp; ?>',
                                    '<?php echo $points; ?>');
                              update('criteria', true);
                            });
                            ">
              <i class="fa fa-pencil"></i>
            </button>
          	<button type="button" class="btn btn-danger btn-circle" data-toggle="modal" data-target="#delModal" 
                    onclick="
                            drop('criteria', 
                                '<?php echo $criteriaId; ?>', '<?php echo $criteriaName; ?>');
                            ">
              <i class="fa fa-remove"></i>
            </button>
          </td>
        </tr>

    	<?php
    }

} 

$conn->close();

?>

</tbody>

</table>

</div>

<script>
  $('#dataTable').DataTable( {
      "info": false,
      "paging": false
  } );
</script>
