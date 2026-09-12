<?php

	if (isset($_POST['msg']) && isset($_POST['chair']) && isset($_POST['judge'])) {
		
		include '../../connection/conn.php';

		$msg = mysqli_real_escape_string($conn, $_POST['msg']);
		$chair = mysqli_real_escape_string($conn, $_POST['chair']);
		$judge = mysqli_real_escape_string($conn, $_POST['judge']);

		$sql = "UPDATE tbl_config 
				SET prompt_msg = '$msg', 
					chairman_status = '$chair', 
					judge_status = '$judge' 
				WHERE config_id = '1'";

		if ($conn->query($sql) === TRUE) {
		    echo "Controller Successfully Updated !";
		} else {
		    echo "Error: " . $sql . "<br>" . $conn->error;
		}

		$conn->close();
	}
?>