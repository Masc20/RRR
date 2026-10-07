
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

    <div class="form-group mb-3 pb-2 border-bottom">
      <div class="d-flex align-items-center justify-content-between mb-2">
        <label class="font-weight-bold text-dark mb-0">
          <?php echo htmlspecialchars($criteriaName, ENT_QUOTES, 'UTF-8'); ?>
          <?php if (!empty($descrp)) { ?>
            <small class="text-muted font-weight-normal ml-1">(<?php echo htmlspecialchars($descrp, ENT_QUOTES, 'UTF-8'); ?>)</small>
          <?php } ?>
        </label>
        <span class="badge badge-light border text-secondary font-weight-bold">
          Range: 0 - <?php echo $criteriaPoints; ?> pts
        </span>
      </div>

      <?php

        $sql = "SELECT * FROM tbl_scores AS s 
                WHERE s.category_id = '$categoryId' AND 
                      s.criteria_id = '$criteriaId' AND 
                      s.user_id = '$judgeId' AND 
                      s.cand_id = '$candId' ";

        $resultScore = $conn->query($sql);

        if ($resultScore->num_rows > 0) {
            $row = $resultScore->fetch_assoc();
            $score = $row['score_points'];
            $isUpdateOk = true;
        } else {
            $score = null;
        }
        
      ?>

      <div class="row align-items-center mb-1">

        <div class="col-9 col-sm-10">
          <div class="judge-ruler-slider-container">
            <input class="items slider judge-ruler-slider" type="range" 
                title="Score for <?php echo htmlspecialchars($criteriaName, ENT_QUOTES, 'UTF-8'); ?>" 
                min="0" max="<?php echo $criteriaPoints; ?>" 
                name="<?php echo $criteriaId; ?>" 
                value="<?php echo (!empty($score)) ? floatval($score) : 0; ?>" 
                aria-label="<?php echo htmlspecialchars($criteriaName, ENT_QUOTES, 'UTF-8'); ?>"
                oninput="handleChange(this, <?php echo $criteriaPoints; ?>, <?php echo $criteriaId; ?>);" >

            <div class="ruler-scale" aria-hidden="true">
              <?php
                $stepCount = 4;
                for ($step = 0; $step <= $stepCount; $step++) {
                  $pct = ($step / $stepCount) * 100;
                  $val = round(($criteriaPoints / $stepCount) * $step);
              ?>
                <div class="ruler-milestone" style="left: <?php echo $pct; ?>%;">
                  <span class="ruler-tick-major"></span>
                  <span class="ruler-value"><?php echo $val; ?></span>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>

        <div class="col-3 col-sm-2 text-right">
          <div class="judge-score-badge-wrapper">
            <span id="labelPoints<?php echo $criteriaId; ?>" class="judge-score-display-badge">
              <?php echo (!empty($score)) ? floatval($score) : 0; ?>
            </span>
            <small class="d-block text-muted font-weight-bold mt-1" style="font-size: 9px; letter-spacing: 0.05em;">/ <?php echo $criteriaPoints; ?> PTS</small>
          </div>
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
