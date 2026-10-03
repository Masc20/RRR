<?php
  
  include '../../connection/conn.php';

  $sql = "SELECT * FROM tbl_config";

  $result = $conn->query($sql);
  $row = $result->fetch_assoc();

  $type = $row['based_type'];
?>


<div class="judge-score-heading">
  <p class="judge-kicker">Judge workspace</p>
  <h1>My scores</h1>
  <p>Review your submitted scores by judging category.</p>
</div>

<div id="scores"></div>


<script>
	$('#scores').load('scores/view.php');
</script>