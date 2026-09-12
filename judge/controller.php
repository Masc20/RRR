<?php

  session_start();

  $userId = $_SESSION["userId"];
  $judgeType = $_SESSION["userType"];


	include '../connection/conn.php';

	$sql = "SELECT * FROM tbl_config WHERE config_id = 1";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {

		$row = $result->fetch_assoc();
    $status = $row[strtolower($judgeType).'_status'];

    // check judging status
		if ($status == 'Open') {
      echo "Opened";
    } else {
      echo $row['prompt_msg'];
    }
	}

  // get current time
  $time = time();

  $sql   = "SELECT * FROM online_judges WHERE user_id = '$userId'"; 
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {

    // update session
    $stmt = $conn->prepare("UPDATE online_judges 
                            SET time = ? 
                            WHERE user_id = ?");

    $stmt->bind_param("ii", $time, $userId);
    $stmt->execute();

    $stmt->close();

  } else {

    // create session
    $stmt = $conn->prepare("INSERT INTO online_judges (user_id, time) 
                            VALUES (?, ?)");

    $stmt->bind_param("ii", $userId, $time);
    $stmt->execute();

    $stmt->close();
  }

  $conn->close();

?>