<?php
include '../config/config.php'; 
$type = $configRow['based_type'] ?? $row['based_type'];

include '../../connection/conn.php';
$hasAnyCategoryAssigned = false;
$checkCatSql = "SELECT COUNT(*) as cnt FROM tbl_candidates WHERE status = 'Allow' AND cand_category IS NOT NULL AND TRIM(cand_category) != '' AND UPPER(TRIM(cand_category)) != 'NONE'";
$checkCatRes = $conn->query($checkCatSql);
if ($checkCatRes && $checkCatRow = $checkCatRes->fetch_assoc()) {
    if ($checkCatRow['cnt'] > 0) {
        $hasAnyCategoryAssigned = true;
    }
}
?>

<!-- Overall result tab pane -->
<div id="menuOverall" class="container tab-pane fade"><br>
  
  <div class="card mb-3">

    <div class="card-header">
      <i class="fa fa-table"></i> Overall Result

      <!-- Updated to call PrintElem function instead of window.print() -->
      <button class="pull-right btn btn-success" onclick="PrintElem('printableArea', 'Overall Result');">
        <i class="fa fa-print"></i> Print
      </button>

    </div>

    <div class="card-body">

      <div class="table-responsive" id="printableArea">

        <table class="table table-bordered table-hover table-sm dataTable" width="100%" cellspacing="0">

        <!-- Table header -->
        <thead>
          <tr>

            <th><?php echo $type; ?> No</th>
            <th><?php echo $type; ?> Name</th>
            <?php if ($hasAnyCategoryAssigned) { echo "<th>Category</th>"; } ?>

            <?php
              $sqlCategoryHeader = "SELECT * FROM tbl_category 
                                    WHERE status = 'Show' 
                                    ORDER BY category_id ASC";
              
              $resultCategoryHeader = $conn->query($sqlCategoryHeader);
              $categoryList = [];

              if ($resultCategoryHeader && $resultCategoryHeader->num_rows > 0) {
                while($rowCatHeader = $resultCategoryHeader->fetch_assoc()) {
                  $categoryList[] = $rowCatHeader;
                  $categoryName = $rowCatHeader['category_name'];
                  echo "<th>".$categoryName."</th>";
                }
                echo "<th class='bg-light text-primary font-weight-bold'>Grand Total</th>";
              }  
            ?>
          
          </tr>
        </thead>
        <!-- /Table header -->

        <!-- Table body -->
        <tbody>
          
          <?php

            $sqlCand = "SELECT * FROM tbl_candidates 
                        WHERE status = 'Allow' 
                        ORDER BY cand_no ASC";

            $resultCand = $conn->query($sqlCand);

            if ($resultCand->num_rows > 0) {

              while($rowCand = $resultCand->fetch_assoc()) {

                $candId = $rowCand['cand_id']; 
                $candNo = $rowCand['cand_no'];
                $candName = $rowCand['cand_name'];
                $candCategory = strtoupper(trim($rowCand['cand_category'] ?? ''));
                if ($candCategory === '' || $candCategory === 'NONE') {
                    $candCategory = '';
                }

                echo "<tr>";
                echo "<td align='center'>".$candNo."</td>";
                echo "<td align='center'>".htmlspecialchars($candName, ENT_QUOTES, 'UTF-8')."</td>";
                if ($hasAnyCategoryAssigned) {
                    echo "<td align='center'>".$candCategory."</td>";
                }

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

              echo "<td align='center' class='font-weight-bold bg-light text-primary'>".number_format($regularTotal, 2, '.', '')."</td>";
              echo "</tr>";
              
            }
          }

          ?>
        </tbody>

        </table>

        <!-- Signature -->
        <?php

          $sqlJudges = "SELECT DISTINCT u.* FROM tbl_users AS u
                        INNER JOIN tbl_scores AS s ON u.user_id = s.user_id 
                        WHERE u.status = 'Active' 
                        ORDER BY u.user_id ASC";

          $resultJudges = $conn->query($sqlJudges);

          if ($resultJudges && $resultJudges->num_rows > 0) {

        ?>

          <br>
          <h3 class="d-none">Signature</h3>

          <table style='border: none;' class="table">
            <tr>

              <?php
                while($rowJudge = $resultJudges->fetch_assoc()) {
                  $judgeName = $rowJudge['full_name'];
                  $judgeType = $rowJudge['user_type'];
              ?>

                <td style='border: none;'>
                  <center>
                    <u><?php echo htmlspecialchars($judgeName, ENT_QUOTES, 'UTF-8'); ?></u>
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
    <!-- card body -->

    <div class="card-footer small text-muted"></div>
  </div>
  
</div>