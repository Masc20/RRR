<?php  

	include __DIR__ . '/../../connection/conn.php';

	$sql = "SELECT * FROM tbl_config";
	$result = $conn->query($sql);

	$row = $result->fetch_assoc();
?>