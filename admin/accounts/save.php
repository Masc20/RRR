<?php  

if (!empty($_POST)) {

	include '../../connection/conn.php';

	$sql2 = "SELECT * FROM tbl_config";

	$result2 = $conn->query($sql2);
	$row2 = $result2->fetch_assoc();

	if ($row2['chairman_status'] == 'Close' && $row2['judge_status'] == 'Close') {

		if (isset($_POST['myId']) && isset($_POST['fullName']) && isset($_POST['usn']) && isset($_POST['psw']) &&
			isset($_POST['userType'])) {

			$userId = mysqli_real_escape_string($conn, $_POST['myId']);
			$fullName = mysqli_real_escape_string($conn, $_POST['fullName']);
			$usn = mysqli_real_escape_string($conn, $_POST['usn']);
			$psw = mysqli_real_escape_string($conn, $_POST['psw']);
			$userType = mysqli_real_escape_string($conn, $_POST['userType']);
			
	    	if (empty($userId)) {
	    		
	    		$sql = "INSERT INTO tbl_users (full_name, user_name, pass_word, user_type, status)
						VALUES ('$fullName', '$usn', '$psw', '$userType', 'Active')";

				if ($conn->query($sql) === TRUE) {
				    echo "New Judge Successfully Added !";
				} else {
				    echo "Error: ". $conn->error;
				}

	    	} else {

	    		$sql = "UPDATE tbl_users 
	    				SET full_name = '$fullName', 
	    					user_name = '$usn', 
	    					pass_word = '$psw', 
	    					user_type = '$userType'  
	    				WHERE user_id = '$userId'";

	    		if ($conn->query($sql) === TRUE) {
				    echo "Judge Successfully Updated !";
				} else {
				    echo "Error updating record: " . $conn->error;
				}
	    	}

		} elseif (isset($_POST['id']) && isset($_POST['status'])) {
			
			$id = mysqli_real_escape_string($conn, $_POST['id']);
			$status = mysqli_real_escape_string($conn, $_POST['status']);

			$sql = "UPDATE tbl_users 
					SET status = '$status' 
					WHERE user_id = '$id'";

			if ($conn->query($sql) === TRUE) {
			   
			   echo "Judge Successfully ";
			   echo ($status == 'Active') ? 'Activated' : 'Deactivated' ;
			    
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