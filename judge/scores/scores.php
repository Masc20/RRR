<?php
  
  include '../../connection/conn.php';

  $sql = "SELECT * FROM tbl_config";

  $result = $conn->query($sql);
  $row = $result->fetch_assoc();

  $type = $row['based_type'];
?>


<!-- Breadcrumbs-->
<ol class="breadcrumb">
  <li class="breadcrumb-item">
    <a href=""><?php echo $type; ?>s</a>
  </li>
  <li class="breadcrumb-item active">Scores</li>
</ol>

<div id="scores"></div>


<script>
	$('#scores').load('scores/view.php');
</script>