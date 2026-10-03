<?php 
  
  session_start();
  $judgeId = $_SESSION['userId'];


  include '../../connection/conn.php';

  $sql = "SELECT * FROM tbl_config";

  $resultConfig = $conn->query($sql);
  $row = $resultConfig->fetch_assoc();

  $type = $row['based_type'];


  $sql = "SELECT * FROM tbl_category 
          ORDER BY category_id ASC";

  $resultCategory = $conn->query($sql);

  if ($resultCategory->num_rows > 0) {

    while($row = $resultCategory->fetch_assoc()) {

    $categoryId = $row['category_id'];
    $categoryName = $row['category_name']

?>

  <div id="menu<?php echo $categoryId;?>" class="container tab-pane fade judge-score-pane"><br>

    <!-- Example DataTables Card-->
    <div class="card mb-3 judge-score-panel">

      <div class="card-header">
        <i class="fa fa-table"></i> <?php echo $categoryName; ?> Score
        
      </div>

      <div class="card-body">
        <div class="table-responsive" id="printableArea<?php echo $categoryId;?>">

        <?php

          $sql = "SELECT * FROM tbl_criteria 
                  WHERE category_id = '$categoryId' AND 
                        status = 'Show'
                  ORDER BY criteria_id ASC";
          $resultCriteria = $conn->query($sql);

          if ($resultCriteria->num_rows > 0) {

        ?>

        <table class="table table-bordered table-hover table-sm dataTable judge-score-table" width="100%" cellspacing="0">

        <!-- table header -->
        <thead class="thead-light">
          <tr>
            <?php
         
              echo "<th class='text-center'>".$type." No</th>";
              echo "<th class='text-center'>".$type." / House</th>";

              while($row = $resultCriteria->fetch_assoc()) {

                $criteriaName = $row['criteria_name'];
                echo "<th class='text-center'>".$criteriaName."</th>";
              }

              echo "<th class='text-center bg-light text-primary font-weight-bold'>Total</th>";

            ?>
          </tr>
        </thead>
        <!-- /table header -->

        <!-- table body -->
        <tbody>
          
            <?php

              $sql = "SELECT * FROM tbl_candidates 
                      WHERE status = 'Allow'
                      ORDER BY cand_no ASC";

              $resultCand = $conn->query($sql);

              if ($resultCand->num_rows > 0) {

                while($row = $resultCand->fetch_assoc()) {

                  $candId = $row['cand_id'];
                  $candNo = $row['cand_no'];
                  $candName = $row['cand_name'];

                  $houseSlug = strtolower(trim($candName));
                  $genderChar = strtoupper(substr(trim($candNo), -1));
                  $isFemale = ($genderChar === 'F');
                  $isMale = ($genderChar === 'M');

                  echo "<tr>";

                  echo "<td align='center' class='font-weight-bold'>".$candNo."</td>";
                  echo "<td align='center'>";
                  echo "<span class='house-pill house-pill-".$houseSlug."'>";
                  echo "<span class='house-swatch swatch-".$houseSlug."'></span>";
                  echo htmlspecialchars($candName, ENT_QUOTES, 'UTF-8');
                  echo "</span>";
                  if ($isFemale) {
                    echo " <span class='division-pill division-pill-female ml-1'><i class='fa fa-female'></i> F</span>";
                  } else if ($isMale) {
                    echo " <span class='division-pill division-pill-male ml-1'><i class='fa fa-male'></i> M</span>";
                  }
                  echo "</td>";

                  $total = 0;

                  $sql = "SELECT * FROM tbl_criteria 
                          WHERE category_id = '$categoryId' AND 
                                status = 'Show'
                          ORDER BY criteria_id ASC";

                  $resultCriteria = $conn->query($sql);

                  if ($resultCriteria->num_rows > 0) {

                    while($row = $resultCriteria->fetch_assoc()) {

                      $criteriaId = $row['criteria_id'];
                      
                      $sql = "SELECT s.score_points 
                              FROM tbl_scores AS s 
                              WHERE s.user_id = '$judgeId' AND s.cand_id = '$candId' AND s.criteria_id = '$criteriaId'   
                              ORDER BY s.criteria_id ASC";

                      $resultScore = $conn->query($sql);

                      if ($resultScore->num_rows > 0) {

                        $row = $resultScore->fetch_assoc();  

                        $scorePoints = $row['score_points']; 
                        
                      } else {

                        $scorePoints = 0;
                      }
                      
                      echo "<td align='center'>".number_format($scorePoints, 0)."</td>";

                      $total += $scorePoints;
                    }

                  }

                  echo "<td align='center' class='font-weight-bold bg-light text-primary' style='font-size: 1.05rem;'>".number_format($total, 0)."</td>";
                  echo "</tr>";
                  
                }
              }

            ?>
            
        </tbody>
        <!-- /table body -->

        </table>

        <?php

          }
        ?>

        </div>
        
      </div>

      <div class="card-footer small text-muted"></div>
    </div>

  </div>
  <!-- end div tab -->

<?php
    }
  }

  $conn->close();
?>