<?php
	
	if (isset($_POST['title']) && isset($_POST['type'])) {
		
		include '../../connection/conn.php';

		$sql2 = "SELECT * FROM tbl_config";

		$result2 = $conn->query($sql2);
		$row2 = $result2->fetch_assoc();

		if ($row2['chairman_status'] == 'Close' && $row2['judge_status'] == 'Close') {

			$title = mysqli_real_escape_string($conn, $_POST['title']);
			$type = mysqli_real_escape_string($conn, $_POST['type']);
			
			$sql = "UPDATE tbl_config 
					SET event_title = '$title',
						based_type = '$type' 
					WHERE config_id = 1";

			if ($conn->query($sql) === TRUE) {
			    echo "Configuration Successfully Updated !";
			} else {
			    echo "Error updating record: " . $conn->error;
			}

		} else {

			echo "Status of the Judging is still Opened !";
		}

		$conn->close();
	}

?>