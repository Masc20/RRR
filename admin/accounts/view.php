<div class="table-responsive">

<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">

<thead>
	<tr>
	  <th>Judge name</th>
	  <th>Username</th>
    <th>Password</th>
    <th>Role</th>
    <th>Status</th>
	  <th>Actions</th>
	</tr>
</thead>

<tbody>

<?php  

include '../../connection/conn.php';

$sql = "SELECT * FROM tbl_users WHERE user_type != 'Admin'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        
        $userId = $row['user_id'];

        $fullName = $row['full_name'];
        $username = $row['user_name'];
        $password = $row['pass_word'];
        $userType = $row['user_type'];
        $status = $row['status'];

    	?>

		    <tr>
          <td align="center"><?php echo $fullName; ?></td>
          <td align="center"><?php echo $username; ?></td>
          <td align="center"><?php echo $password; ?></td>
          <td align="center"><?php echo $userType; ?></td>
          <td align="center">
            <div class="btn-group">
            <?php 

              if ($status == 'Active') {
                ?>
                  <button type="button" class="btn btn-success" disabled>
                    <i class='fa fa-fw fa-check'></i> Active
                  </button>
                  <button type="button" class="btn btn-outline-danger" 
                          name="<?php echo $userId; ?>" value="Inactive" 
                          onclick="updateStatus('accounts', this);">
                    Deactivate
                  </button>
                <?php
              } else {
                ?>
                  <button type="button" class="btn btn-outline-success" 
                          name="<?php echo $userId; ?>" value="Active" 
                          onclick="updateStatus('accounts', this);">
                    Activate
                  </button>
                  <button type="button" class="btn btn-danger" disabled>
                    Inactive <i class='fa fa-fw fa-check'></i>
                  </button>
                <?php
              }
            ?>
            </div>
          </td>
          <td align="center">
          	<button type="button" class="btn btn-success btn-circle" data-toggle="modal" data-target="#formModal" 
                    onclick="
                            $('#myform').load('accounts/custom_form.php', function () {
                              
                              retrieve('<?php echo $userId; ?>',
                                    '<?php echo $fullName; ?>',
                                    '<?php echo $username; ?>',
                                    '<?php echo $password; ?>',
                                    '<?php echo $userType; ?>');
                              update('accounts', true);
                            });
                          ">

              <i class="fa fa-pencil"></i>
            </button>
          	<button type="button" class="btn btn-danger btn-circle" data-toggle="modal" data-target="#delModal" 
                    onclick="
                            drop('accounts', 
                                '<?php echo $userId; ?>', '<?php echo $fullName; ?>');
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
