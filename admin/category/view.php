<div class="table-responsive">

<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">

<thead>
	<tr>
	  <th>Category Name</th>
    <th>Percentage</th>
	  <th>Status</th>
	  <th>Actions</th>
	</tr>
</thead>

<tbody>

<?php  

include '../../connection/conn.php';

$sql = "SELECT * FROM tbl_category";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
      
      $categoryId = $row['category_id'];

      $categoryName = $row['category_name'];
      $percent = $row['percentage'];
      $status = $row['status'];

    	?>

		    <tr>
          <td align="center"><?php echo $categoryName; ?></td>
          <td align="center"><?php echo $percent; ?> %</td>
          <td align="center">
            <div class="btn-group">
            <?php 

              if ($status == 'Show') {
                ?>
                  <button type="button" class="btn btn-success" disabled>
                    <i class='fa fa-fw fa-check'></i> Shown
                  </button>
                  <button type="button" class="btn btn-outline-danger" 
                          name="<?php echo $categoryId; ?>" value="Hide" 
                          onclick="updateStatus('category', this);">
                    Hide
                  </button>
                <?php
              } else {
                ?>
                  <button type="button" class="btn btn-outline-success" 
                          name="<?php echo $categoryId; ?>" value="Show" 
                          onclick="updateStatus('category', this);">
                    Show
                  </button>
                  <button type="button" class="btn btn-danger" disabled>
                    Hidden <i class='fa fa-fw fa-check'></i>
                  </button>
                <?php
              }
            ?>
            </div>
          </td>
          <td align="center">
          	<button type="button" class="btn btn-success btn-circle" data-toggle="modal" data-target="#formModal" 
                    onclick="
                            $('#myform').load('category/custom_form.php', function () {
                              
                              retrieve('<?php echo $categoryId; ?>',
                                    '<?php echo $categoryName; ?>',
                                    '<?php echo $percent; ?>');
                              update('category', true);
                            });
                            
                            ">

              <i class="fa fa-pencil"></i>
            </button>
          	<button type="button" class="btn btn-danger btn-circle" data-toggle="modal" data-target="#delModal" 
                    onclick="
                            drop('category', 
                                '<?php echo $categoryId; ?>', '<?php echo $categoryName; ?>');
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
