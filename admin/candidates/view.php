<?php include '../config/config.php'; ?>

<div class="table-responsive">

<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">

<thead>
	<tr>
	  <th><?php echo $row['based_type']; ?> No.</th>
	  <th><?php echo $row['based_type']; ?> Name</th>
	  <th>Picture</th>
    <th>Status</th>
	  <th>Actions</th>
	</tr>
</thead>

<tbody>

<?php  

include '../../connection/conn.php';

$sql = "SELECT * FROM tbl_candidates";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
       
      $candId = $row['cand_id'];

      $candNo = $row['cand_no'];
      $candName = $row['cand_name'];
      $candPic = $row['cand_pic'];
      $status = $row['status'];

    	?>

		    <tr>
          <td align="center"><?php echo $candNo; ?></td>
          <td align="center"><?php echo $candName; ?></td>
          <td align="center"><img src="../uploads/<?php echo $candPic; ?>" height="32" width="32"></td>
          <td align="center">
            <div class="btn-group">
            <?php 

              if ($status == 'Allow') {
                ?>
                  <button type="button" class="btn btn-success" disabled>
                    <i class='fa fa-fw fa-check'></i> Allowed
                  </button>
                  <button type="button" class="btn btn-outline-danger" 
                          name="<?php echo $candId; ?>" value="Eliminate" 
                          onclick="updateStatus('candidates', this);">
                    Eliminate
                  </button>
                <?php
              } else {
                ?>
                  <button type="button" class="btn btn-outline-success" 
                          name="<?php echo $candId; ?>" value="Allow" 
                          onclick="updateStatus('candidates', this);">
                    Allow
                  </button>
                  <button type="button" class="btn btn-danger" disabled>
                    Eliminated <i class='fa fa-fw fa-check'></i>
                  </button>
                <?php
              }
            ?>
            </div>
          </td>
          <td align="center">
          	<button type="button" class="btn btn-success btn-circle" data-toggle="modal" data-target="#formModal" 
                    onclick="
                            $('#myform').load('candidates/custom_form.php', function () {
                              
                              populate('<?php echo $candPic; ?>', 
                                    '<?php echo $candId; ?>',
                                    '<?php echo $candNo; ?>',
                                    '<?php echo $candName; ?>');
                              update('candidates', true);
                            });
                            ">
              <i class="fa fa-pencil"></i>
            </button>
          	<button type="button" class="btn btn-danger btn-circle" data-toggle="modal" data-target="#delModal" 
                    onclick="
                            drop('candidates', 
                                '<?php echo $candId; ?>', '<?php echo $candName; ?>');
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

function populate() {
  $('img[name=pic]').attr('src', '../uploads/' + arguments[0]);
  $('input[name=myId]').val(arguments[1]);
  $('input[name=candNo]').val(arguments[2]);
  $('input[name=candName]').val(arguments[3]);
}

</script>

<script>
  $('#dataTable').DataTable( {
      "info": false,
      "paging": false
  } );
</script>
