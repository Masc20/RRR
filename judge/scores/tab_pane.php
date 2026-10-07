<?php 
  
  session_start();
  $judgeId = $_SESSION['userId'];


  include '../../connection/conn.php';

  $sql = "SELECT * FROM tbl_config";

  $resultConfig = $conn->query($sql);
  $configRow = $resultConfig->fetch_assoc();
  $type = $configRow['based_type'];

  $allRegularCategories = [];
  $sqlAllCats = "SELECT category_id FROM tbl_category WHERE LOWER(TRIM(category_name)) != 'total ranking' ORDER BY category_id ASC";
  $resAllCats = $conn->query($sqlAllCats);
  if ($resAllCats && $resAllCats->num_rows > 0) {
      while($cRow = $resAllCats->fetch_assoc()) {
          $allRegularCategories[] = $cRow['category_id'];
      }
  }

  $sql = "SELECT * FROM tbl_category ORDER BY category_id ASC";
  $resultCategory = $conn->query($sql);

  if ($resultCategory->num_rows > 0) {
    while($catRow = $resultCategory->fetch_assoc()) {
      $categoryId = $catRow['category_id'];
      $categoryName = $catRow['category_name'];

      $isTotalRankingTab = (strcasecmp(trim($categoryName), 'total ranking') === 0);
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
          $criteriaList = [];
          $sqlCriteria = "SELECT * FROM tbl_criteria WHERE category_id = '$categoryId' AND status = 'Show' ORDER BY criteria_id ASC";
          $resultCriteria = $conn->query($sqlCriteria);

          if ($resultCriteria && $resultCriteria->num_rows > 0) {
              while($critRow = $resultCriteria->fetch_assoc()) {
                  $criteriaList[] = $critRow;
              }
          }
          
          if (!empty($criteriaList)) { 

              // Check if any allowed candidate has a non-empty category
              $hasAnyCategory = false;
              $checkCatSql = "SELECT COUNT(*) as cnt FROM tbl_candidates WHERE status = 'Allow' AND cand_category IS NOT NULL AND TRIM(cand_category) != ''";
              $checkCatRes = $conn->query($checkCatSql);
              if ($checkCatRes && $checkCatRow = $checkCatRes->fetch_assoc()) {
                  if ($checkCatRow['cnt'] > 0) {
                      $hasAnyCategory = true;
                  }
              }
        ?>

        <table class="table table-bordered table-hover table-sm dataTable judge-score-table" width="100%" cellspacing="0">

        <!-- table header -->
        <thead class="thead-light">
          <tr>
            <?php
         
              echo "<th class='text-center'>".$type." No</th>";
              echo "<th class='text-center'>".$type." / House</th>";
              if ($hasAnyCategory) {
                  echo "<th class='text-center'>Category</th>";
              }

              foreach($criteriaList as $crit) { 
                  echo "<th class='text-center'>".htmlspecialchars($crit['criteria_name'], ENT_QUOTES, 'UTF-8')."</th>"; 
              } 

              echo "<th class='text-center bg-light text-primary font-weight-bold'>Total</th>";

            ?>
          </tr>
        </thead>
        <!-- /table header -->

        <!-- table body -->
        <tbody>
          
            <?php
              $sqlCand = "SELECT * FROM tbl_candidates WHERE status = 'Allow' ORDER BY cand_no ASC";
              $resultCand = $conn->query($sqlCand);

              if ($resultCand->num_rows > 0) {
                while($candRow = $resultCand->fetch_assoc()) {
                  $candId = $candRow['cand_id'];
                  $candNo = $candRow['cand_no'];
                  $candName = $candRow['cand_name'];
                  $candCat = trim($candRow['cand_category'] ?? '');

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

                  if ($hasAnyCategory) {
                      echo "<td align='center'>";
                      if (!empty($candCat)) {
                          echo "<span class='badge badge-info'>".htmlspecialchars($candCat, ENT_QUOTES, 'UTF-8')."</span>";
                      }
                      echo "</td>";
                  }

                  $grandTotal = 0;

                  foreach($criteriaList as $index => $crit) {
                      $criteriaId = $crit['criteria_id'];
                      
                      if ($isTotalRankingTab) {
                          $sourceCategoryId = isset($allRegularCategories[$index]) ? $allRegularCategories[$index] : 0;
                          
                          $sqlSubTotal = "SELECT SUM(score_points) as cat_total 
                                          FROM tbl_scores 
                                          WHERE user_id = '$judgeId' 
                                            AND cand_id = '$candId' 
                                            AND category_id = '$sourceCategoryId'";
                                          
                          $resultSubTotal = $conn->query($sqlSubTotal);
                          $scorePoints = 0;
                          
                          if ($resultSubTotal && $subTotalRow = $resultSubTotal->fetch_assoc()) {
                              $scorePoints = $subTotalRow['cat_total'] ?? 0;
                          }
                      } else {
                          $sqlScore = "SELECT score_points FROM tbl_scores 
                                       WHERE user_id = '$judgeId' 
                                         AND cand_id = '$candId' 
                                         AND criteria_id = '$criteriaId' 
                                       LIMIT 1";

                          $resultScore = $conn->query($sqlScore);
                          $scorePoints = 0;

                          if ($resultScore && $resultScore->num_rows > 0) {
                            $scoreRow = $resultScore->fetch_assoc();  
                            $scorePoints = $scoreRow['score_points']; 
                          }
                      }
                      
                      echo "<td align='center'>".number_format($scorePoints, 0)."</td>";
                      $grandTotal += $scorePoints;
                  }

                  echo "<td align='center' class='font-weight-bold bg-light text-primary' style='font-size: 1.05rem;'>".number_format($grandTotal, 0)."</td>";
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