<?php  

	if (isset($_POST['idel'])) {
		
		include '../../connection/conn.php';

		$sql2 = "SELECT * FROM tbl_config";

		$result2 = $conn->query($sql2);
		$row2 = $result2->fetch_assoc();

		if ($row2['chairman_status'] == 'Close' && $row2['judge_status'] == 'Close') {

			$categoryId = $_POST['idel'];

			$sql = "DELETE FROM tbl_category 
					WHERE category_id = '$categoryId' ";

			if ($conn->query($sql) === TRUE) {
				
				echo "Category Successfully Deleted !";
			} else {

				echo "Category cannot be deleted !";
			}

		} else {

			echo "Status of the Judging is still Opened !";
		}

		$conn->close();
	}

?>