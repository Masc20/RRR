<?php  

if (!empty($_POST)) {

	include '../../connection/conn.php';

	$sql2 = "SELECT * FROM tbl_config";

	$result2 = $conn->query($sql2);
	$row2 = $result2->fetch_assoc();

	if ($row2['chairman_status'] == 'Close' && $row2['judge_status'] == 'Close') {

		if (isset($_POST['myId']) && isset($_POST['criteriaName']) && isset($_POST['criteriaDescrp']) && 
			isset($_POST['criteriaPoints']) && isset($_POST['categoryId'])) {
			
			
			$criteriaId = mysqli_real_escape_string($conn, $_POST['myId']);
			$criteriaName = mysqli_real_escape_string($conn, $_POST['criteriaName']);
			$criteriaDescrp = mysqli_real_escape_string($conn, $_POST['criteriaDescrp']);
			$criteriaPoints = mysqli_real_escape_string($conn, $_POST['criteriaPoints']);
			$categoryId = mysqli_real_escape_string($conn, $_POST['categoryId']);


	    	if (empty($criteriaId)) {
	    		
	    		$sql = "INSERT INTO tbl_criteria (criteria_name, criteria_descrp, criteria_points, category_id)
						VALUES ('$criteriaName', '$criteriaDescrp', '$criteriaPoints', '$categoryId')";

				if ($conn->query($sql) === TRUE) {
				    echo "New Criteria Successfully Added !";
				} else {
				    echo "Error: ". $conn->error;
				}

	    	} else {

	    		$sql = "UPDATE tbl_criteria 
	    				SET criteria_name = '$criteriaName', 
	    					criteria_descrp = '$criteriaDescrp', 
	    					criteria_points = '$criteriaPoints', 
	    					category_id = '$categoryId' 
	    				WHERE criteria_id = '$criteriaId'";

	    		if ($conn->query($sql) === TRUE) {
				    echo "Criteria Successfully Updated !";
				} else {
				    echo "Error updating record: " . $conn->error;
				}
	    	}

		}

	} else {

		echo "Status of the Judging is still Opened !";
	}

	$conn->close();
}

?>