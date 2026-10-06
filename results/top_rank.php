<?php

include '../config/config.php'; 
$type = $row['based_type'];

?>

<!-- Overall result tab pane -->
<div id="menuTop" class="container tab-pane fade"><br>
  
  <div class="card mb-3">

    <div class="card-header">
      <i class="fa fa-table"></i> OVERALL AVERANGE

      <button class="pull-right btn btn-success" 
          onclick="PrintElem('printableAreas', 'Overall Result');">
        <i class="fa fa-print"></i> Print
      </button>

    </div>

    <div class="card-body">

      <div class="table-responsive" id="printableAreas">

        <table class="table table-bordered table-hover table-sm dataTable" width="100%" cellspacing="0">

        <!-- Table header -->
        <thead>
          <tr>

            <th><?php echo $type; ?> No</th>
            <th><?php echo $type; ?> Name</th>

            <?php

              include '../../connection/conn.php';

              $sql = "SELECT * FROM tbl_category 
                      WHERE status = 'Show' 
                      ORDER BY category_id ASC";
              
              $resultCategory = $conn->query($sql);
              $categoryList = [];

              if ($resultCategory && $resultCategory->num_rows > 0) {
                while($row = $resultCategory->fetch_assoc()) {
                  $categoryList[] = $row;
                  $categoryName = $row['category_name'];
                  echo "<th>".$categoryName."</th>";
                }
                echo "<th>Total</th>";
              }  
            ?>
          
          </tr>
        </thead>
        <!-- /Table header -->

        <!-- Table body -->
        <tbody>
          <?php 
            $totPer = 0;
            foreach ($categoryList as $cat) {
              if (strcasecmp(trim($cat['category_name']), 'total ranking') !== 0) {
                $totPer += (float)$cat['percentage'];
              }
            }
          ?>
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

                echo "<tr>";
                
                echo "<td align='center'>".$candNo."</td>";
                echo "<td align='center'>".$candName."</td>";

                $regularScores = [];
                $regularTotal = 0;

                foreach ($categoryList as $cat) {
                  $catId = $cat['category_id'];
                  $catName = $cat['category_name'];
                  $percent = (float)$cat['percentage'];

                  if (strcasecmp(trim($catName), 'total ranking') === 0) {
                    continue;
                  }

                  $categoryTotalScore = 0;
                  $sqlCriteria = "SELECT criteria_id FROM tbl_criteria 
                                  WHERE category_id = '$catId' 
                                  ORDER BY criteria_id ASC";
                  $resultCriteria = $conn->query($sqlCriteria);

                  if ($resultCriteria && $resultCriteria->num_rows > 0) {
                    while($rowCrit = $resultCriteria->fetch_assoc()) {
                      $criteriaId = $rowCrit['criteria_id'];
                      $sqlScore = "SELECT CAST(AVG(s.score_points) AS DECIMAL(10,2)) AS score_points 
                                   FROM tbl_scores AS s 
                                   WHERE s.cand_id = '$candId' AND s.criteria_id = '$criteriaId'";
                      $resultScore = $conn->query($sqlScore);
                      if ($resultScore && $resultScore->num_rows > 0) {
                        $rowScore = $resultScore->fetch_assoc();
                        $scorePoints = (is_null($rowScore['score_points'])) ? 0 : (float)$rowScore['score_points'];
                        $categoryTotalScore += $scorePoints;
                      }
                    }
                  }

                  $factoredCategoryTotal = ($percent / 100) * $categoryTotalScore;
                  $regularScores[$catId] = $factoredCategoryTotal;
                  $regularTotal += $factoredCategoryTotal;
                }

                foreach ($categoryList as $cat) {
                  $catId = $cat['category_id'];
                  $catName = $cat['category_name'];

                  if (strcasecmp(trim($catName), 'total ranking') === 0) {
                    echo "<td align='center'>".number_format($regularTotal, 2, '.', '')."</td>";
                  } else {
                    $score = $regularScores[$catId] ?? 0;
                    echo "<td align='center'>".number_format($score, 2, '.', '')."</td>";
                  }
                }

                $overall2 = ($totPer > 0) ? ($regularTotal / $totPer * 100) : 0;
                echo "<td align='center'>".number_format($overall2, 2)."%</td>";
                echo "</tr>";
                
              }
            }

          ?>
        </tbody>

        </table>

        <!-- Signature -->
        <?php

          $sql = "SELECT DISTINCT u.* FROM tbl_users AS u, tbl_scores AS s 
                  WHERE u.user_id = s.user_id AND u.status = 'Active' 
                  ORDER BY u.user_id ASC";

          $resultJudges = $conn->query($sql);

          if ($resultJudges->num_rows > 0) {

        ?>

          <br>
          <h3 class="d-none">Signature</h3>

          <table style='border: none;' class="table">
            <tr>

              <?php

                while($row = $resultJudges->fetch_assoc()) {

                  $judgeName = $row['full_name'];
                  $judgeType = $row['user_type'];
              ?>

                <td style='border: none;'>
                  <center>
                    <u><?php echo $judgeName; ?></u>
                    <br>
                    <strong><?php echo $judgeType; ?></strong>
                  </center>
                </td>

              <?php

                }
              ?>

            </tr>
          </table>

        <?php

          }

          $conn->close();
        ?>
        <!-- /signature -->


      </div>
      <!-- table responsive -->
    </div>
    <!-- card bady -->

    <div class="card-footer small text-muted"></div>
  </div>
  
</div>