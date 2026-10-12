<?php
include '../config/config.php'; 
$type = $configRow['based_type'] ?? $row['based_type'];

include '../../connection/conn.php';
require_once __DIR__ . '/../../lib/scorecalculator.php';

$hasAnyCategoryAssigned = ScoreCalculator::hasAnyCategoryAssigned($conn);
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

                $breakdown = ScoreCalculator::getCandidateScoreBreakdown($conn, $candId);
                $regularScores = $breakdown['regular_scores'];
                $regularTotal = $breakdown['grand_total'];

                foreach ($categoryList as $cat) {
                  $catId = $cat['category_id'];
                  $catName = $cat['category_name'];

                  if (ScoreCalculator::isTotalRanking($catName)) {
                    echo "<td align='center'>".ScoreCalculator::format($regularTotal)."</td>";
                  } else {
                    $score = $regularScores[$catId] ?? 0;
                    echo "<td align='center'>".ScoreCalculator::format($score)."</td>";
                  }
                }

                echo "<td align='center' class='font-weight-bold bg-light text-primary'>".ScoreCalculator::format($regularTotal)."</td>";
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