
<?php

  if (isset($_POST['candId']) && isset($_POST['categoryId'])) {
    
    session_start();

    $judgeId = $_SESSION["userId"];

    include '../../connection/conn.php';

    $categoryId = mysqli_real_escape_string($conn, $_POST['categoryId']);
    $candId = mysqli_real_escape_string($conn, $_POST['candId']);
?>

  <input type="number" name="categoryId" value="<?php echo $categoryId;?>" hidden>

  <?php  

    $sql = "SELECT * FROM tbl_criteria AS c
            WHERE c.category_id = '$categoryId' AND 
            c.status = 'Show' 
            ORDER BY criteria_id ASC";
    $resultCriteria = $conn->query($sql);

    if ($resultCriteria->num_rows > 0) {

      $isUpdateOk = false;
      
      while($row = $resultCriteria->fetch_assoc()) {

        $criteriaId = $row['criteria_id'];
        $criteriaName = $row['criteria_name'];
        $criteriaPoints = $row['criteria_points'];
        $descrp = $row['criteria_descrp'];
  ?>

    <div class="form-group">
      <strong>
      <p style="margin-bottom: 5px;">
        <?php echo $criteriaName; ?> 
        <u class="text-danger">0-<?php echo $criteriaPoints; ?></u> pts
        <?php if (!empty($descrp)) { echo "(".$descrp.")"; } ?>
      </p>
      </strong>

      <?php

        $sql = "SELECT * FROM tbl_scores AS s 
                WHERE s.category_id = '$categoryId' AND 
                      s.criteria_id = '$criteriaId' AND 
                      s.user_id = '$judgeId' AND 
                      s.cand_id = '$candId' ";

        $resultScore = $conn->query($sql);
        $row = $resultScore->fetch_assoc();

        $score = $row['score_points'];

        if ($resultScore->num_rows > 0) {

          $isUpdateOk = true;
        }
      ?>

      <div class="row">

        <div class="col-lg-11 col-md-10 col-sm-10 col-10">
          <input class="items slider" type="range" title="Score" 
              min="0" max="<?php echo $criteriaPoints; ?>" 
              name="<?php echo $criteriaId; ?>" 
              value = "<?php echo (!empty($score)) ? floatval($score) : 0; ?>" 
              oninput="handleChange(this, <?php echo $criteriaPoints; ?>, <?php echo $criteriaId; ?>);" >
        </div>

        <div class="col-lg-1 col-md-2 col-sm-2 col-2" style="margin-top: -7px;">
          <h4>
            <span id="labelPoints<?php echo $criteriaId; ?>" class="badge badge-pill badge-success">
              <?php echo (!empty($score)) ? floatval($score) : 0; ?>
            </span>
          </h4>
        </div>

      </div>
            
    </div>

  <?php

      }

      ?>

        <script>
          $('#btnSave').html("<?php if ($isUpdateOk == true) {echo 'UPDATE';} else {echo 'SAVE';} ?>");
        </script>

      <?php
    }

  $conn->close();
  
  }

  ?>
