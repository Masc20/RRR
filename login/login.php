<?php  

if (isset($_POST['usn']) && isset($_POST['psw'])) {

	include '../connection/conn.php';

	$usn = mysqli_real_escape_string($conn, $_POST['usn']);
	$psw = mysqli_real_escape_string($conn, $_POST['psw']);

	
	$sql = "SELECT * FROM tbl_users WHERE user_name = '$usn' AND BINARY pass_word = '$psw' ";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {

		$row = $result->fetch_assoc();

		if ($row['status'] == 'Active') {
			
			session_start();

			$_SESSION["userType"] = $row['user_type'];
			$_SESSION["userId"] = $row['user_id'];

			echo "success";

		} else {
			
			echo "Your Account is Deactivated !";
		}
		
	} else {

		echo "Invalid Username or Password !";
	}

	$conn->close();

}

?>