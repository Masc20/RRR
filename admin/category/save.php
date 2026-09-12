<?php  

if (!empty($_POST)) {

	include '../../connection/conn.php';

	$sql2 = "SELECT * FROM tbl_config";

	$result2 = $conn->query($sql2);
	$row2 = $result2->fetch_assoc();

	if ($row2['chairman_status'] == 'Close' && $row2['judge_status'] == 'Close') {

		if (isset($_POST['myId']) && isset($_POST['categoryName']) && isset($_POST['percent'])) {
			
			$categoryId = mysqli_real_escape_string($conn, $_POST['myId']);
			$categoryName = mysqli_real_escape_string($conn, $_POST['categoryName']);
			$percent = mysqli_real_escape_string($conn, $_POST['percent']);		

	    	if (empty($categoryId)) {
	    		
	    		$sql = "INSERT INTO tbl_category (category_name, percentage, status)
						VALUES ('$categoryName', '$percent', 'Show')";

				if ($conn->query($sql) === TRUE) {
				    echo "New Category Successfully Added !";
				} else {
				    echo "Error: ". $conn->error;
				}

	    	} else {

	    		$sql = "UPDATE tbl_category 
	    				SET category_name = '$categoryName', percentage = '$percent' 
	    				WHERE category_id = '$categoryId'";

	    		if ($conn->query($sql) === TRUE) {
				    echo "Category Successfully Updated !";
				} else {
				    echo "Error updating record: " . $conn->error;
				}
	    	}

		} elseif (isset($_POST['id']) && isset($_POST['status'])) {
			
			$id = mysqli_real_escape_string($conn, $_POST['id']);
			$status = mysqli_real_escape_string($conn, $_POST['status']);

			$sql = "UPDATE tbl_category 
					SET status = '$status'  
					WHERE category_id = '$id'";

			if ($conn->query($sql) === TRUE) {
			   
			   echo "Category Successfully ";
			   echo ($status == 'Show') ? 'Shown' : 'Hidden' ;
			    
			} else {
			    echo "Error updating record: " . $conn->error;
			}
		}

	} else {

		echo "Status of the Judging is still Opened !";
	}

	$conn->close();
}

?>