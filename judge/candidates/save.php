<?php  

	if (isset($_POST['candId']) && isset($_POST['categoryId'])) {
		
		include '../../connection/conn.php';

		$sql2 = "SELECT * FROM tbl_config";

		$result2 = $conn->query($sql2);
		$row2 = $result2->fetch_assoc();

		session_start();

		$judgeId = $_SESSION["userId"];
		$candId = mysqli_real_escape_string($conn, $_POST['candId']);
		$categoryId = mysqli_real_escape_string($conn, $_POST['categoryId']);
		
		// clear current scores
		$sql = "DELETE FROM tbl_scores 
				WHERE user_id = '$judgeId' AND 
					cand_id = '$candId' AND 
					category_id = '$categoryId'";

		if ($conn->query($sql) === TRUE) {

			// prepare and bind
			$stmt = $conn->prepare("INSERT INTO tbl_scores (category_id, criteria_id, user_id, cand_id, score_points) 
									VALUES (?, ?, ?, ?, ?)");

			$stmt->bind_param("iiiid", $categoryId, $criteria, $judgeId, $candId, $score);

			$total = 0;

			foreach ($_POST as $name => $value) {

				// escape reference id's
				if (!is_numeric($name)) continue;

				// set parameters and execute
				$criteria = mysqli_real_escape_string($conn, $name);
				$score = mysqli_real_escape_string($conn, $value);

				// check if score is null
				// if ($score == 0) continue;

				$total += $score;
				
				// save new score
				$stmt->execute();
			}

			echo $row2['based_type']." Scores Successfully Saved !";
			// if ($total > 0) {

			// }

			$stmt->close();
		}
		
		$conn->close();
	}
?>