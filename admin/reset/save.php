<?php

	if (isset($_POST['adminPass']) && isset($_POST['categoryId'])) {
		
		include '../../connection/conn.php';

		$sql2 = "SELECT * FROM tbl_config";

		$result2 = $conn->query($sql2);
		$row2 = $result2->fetch_assoc();

		if ($row2['chairman_status'] == 'Close' && $row2['judge_status'] == 'Close') {

			$adminPass = mysqli_real_escape_string($conn, $_POST['adminPass']);
			$categoryId = mysqli_real_escape_string($conn, $_POST['categoryId']);

				
			$sql = "SELECT * FROM tbl_users WHERE BINARY pass_word = '$adminPass' ";
			$result = $conn->query($sql);

			if ($result->num_rows > 0) {

				$where = ($categoryId  == 'All') ? " " : " WHERE category_id = '$categoryId'" ;

				$sql = "DELETE FROM tbl_scores ".$where;

				if ($conn->query($sql) === TRUE) {
					
					echo "Scores Successfully Reset !";
				} else {

					echo "Error deleting record: " . $conn->error;
				}
				
			} else {

				echo "Invalid Admin password !";
			}

		} else {

			echo "Status of the Judging is still Opened !";
		}
		
		$conn->close();
	}
?>