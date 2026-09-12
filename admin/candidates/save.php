<?php  

if (!empty($_POST)) {
	
	include '../../connection/conn.php';

	$sql2 = "SELECT * FROM tbl_config";

	$result2 = $conn->query($sql2);
	$row2 = $result2->fetch_assoc();

	if ($row2['chairman_status'] == 'Close' && $row2['judge_status'] == 'Close') {

		if (isset($_POST['myId']) && isset($_POST['candNo']) && isset($_POST['candName']) && isset($_POST['pic'])) {
			
			$candId = mysqli_real_escape_string($conn, $_POST['myId']);
			$candNo = mysqli_real_escape_string($conn, $_POST['candNo']);
			$candName = mysqli_real_escape_string($conn, $_POST['candName']);
			$candPic = basename($_POST['pic']);


			$target_dir = "../../uploads/";
		    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
		    $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
		    $uploadOk = 1;

		    if (is_uploaded_file($_FILES['fileToUpload']['tmp_name'])) {
		    	
		    	if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
					&& $imageFileType != "gif" ) {

				    echo "Only JPG, JPEG, PNG & GIF files are allowed !";
					$uploadOk = 0;

				} else {

					if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
			        
				        $candPic = basename($_FILES["fileToUpload"]["name"]);
				        $uploadOk = 1;

				    } else {

				        echo "There was an error uploading your file !";
				        $uploadOk = 0;
				    }
				}

		    }


		    if ($uploadOk == 1) {

		    	if (empty($candId)) {
		    		
		    		$sql = "INSERT INTO tbl_candidates (cand_no, cand_name, cand_pic, status)
							VALUES ('$candNo', '$candName', '$candPic', 'Allow')";

					if ($conn->query($sql) === TRUE) {
					    echo "New ".$row2['based_type']." Successfully Added !";
					} else {
					    echo "Error: ". $conn->error;
					}

		    	} else {

		    		$sql = "UPDATE tbl_candidates 
		    				SET cand_no = '$candNo', cand_name = '$candName', cand_pic = '$candPic' 
		    				WHERE cand_id = '$candId'";

		    		if ($conn->query($sql) === TRUE) {
					    echo $row2['based_type']." Successfully Updated !";
					} else {
					    echo "Error updating record: " . $conn->error;
					}
		    	}    	
		    }

		} elseif (isset($_POST['id']) && isset($_POST['status'])) {
			
			$id = mysqli_real_escape_string($conn, $_POST['id']);
			$status = mysqli_real_escape_string($conn, $_POST['status']);

			$sql = "UPDATE tbl_candidates 
					SET status = '$status'  
					WHERE cand_id = '$id'";

			if ($conn->query($sql) === TRUE) {
			   
			   echo $row2['based_type']." Successfully ";
			   echo ($status == 'Allow') ? 'Allowed' : 'Eliminated' ;
			    
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