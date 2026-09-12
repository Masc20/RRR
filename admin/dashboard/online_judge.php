<?php 

  $time       = time();
  $time_check = $time - 5;
?>
<table class="table-hover table" width="100%" cellspacing="0" style="font-size: 18px;">

	<?php

    include '../../connection/conn.php';

    // delete judges that has been offline for 5 sec
    $sql = "DELETE FROM online_judges WHERE time < $time_check";
    $conn->query($sql);

    // retrieve judges
		$sql = "SELECT * FROM tbl_users WHERE user_type != 'Admin' AND status = 'Active'";

		$resultJudges = $conn->query($sql);

		if ($resultJudges->num_rows > 0) {

			while($row = $resultJudges->fetch_assoc()) {

        $userId = $row['user_id'];
        $fullName = $row['full_name'];

        $sql = "SELECT * FROM online_judges WHERE user_id = '$userId'";

        $resultOnline = $conn->query($sql);

        if ($resultOnline->num_rows > 0) {

          $row = $resultOnline->fetch_assoc();

          ?>
            <tr>
              <td><i class="fa fa-circle text-success"></i> <?php echo $fullName; ?></td>
              <td class="text-success"><strong>Connected</strong></td>
            </tr>
          <?php
        } else {
          ?>
            <tr>
              <td><i class="fa fa-circle text-dark"></i> <?php echo $fullName; ?></td>
              <td class="text-danger"><strong>Disconnected</strong></td>
            </tr>
          <?php
        }


			}

  	}

    $conn->close();
	?>

</table>