<?php
include '../config/config.php'; 
$type = $row['based_type'];
?>

<!-- Top Ranking tab pane -->
<div id="menuTopRanking" class="container tab-pane fade"><br>
  
  <div class="card mb-3">

    <div class="card-header">
      <i class="fa fa-trophy"></i> Top Ranking / Highest Scorers

      <button class="pull-right btn btn-success" 
          onclick="PrintElem('printableAreaTopRanking', 'Top Ranking Result');">
        <i class="fa fa-print"></i> Print
      </button>

    </div>

    <div class="card-body">

      <div class="table-responsive" id="printableAreaTopRanking">

        <table class="table table-bordered table-hover table-sm" width="100%" cellspacing="0">

        <!-- Table header -->
        <thead>
          <tr>
            <th width="10%" class="text-center">Rank</th>
            <th class="text-center"><?php echo $type; ?> No</th>
            <th class="text-center"><?php echo $type; ?> Name</th>
            <th class="text-center">Final Score / Percentage</th>
          </tr>
        </thead>
        <!-- /Table header -->

        <!-- Table body with sorting logic -->
        <tbody>
          <?php 
            include '../../connection/conn.php';

            // 1. Fetch categories to compute total percentage weights
            $sqlCat = "SELECT * FROM tbl_category WHERE status = 'Show' ORDER BY category_id ASC";
            $resCat = $conn->query($sqlCat);
            $categoryList = [];
            $totPer = 0;

            if ($resCat && $resCat->num_rows > 0) {
              while($catRow = $resCat->fetch_assoc()) {
                $categoryList[] = $catRow;
                if (strcasecmp(trim($catRow['category_name']), 'total ranking') !== 0) {
                  $totPer += (float)$catRow['percentage'];
                }
              }
            }

            // 2. Fetch candidates and calculate their scores dynamically
            $sqlCand = "SELECT * FROM tbl_candidates WHERE status = 'Allow'";
            $resultCand = $conn->query($sqlCand);
            $rankingData = [];

            if ($resultCand && $resultCand->num_rows > 0) {
              while($candRow = $resultCand->fetch_assoc()) {
                $candId = $candRow['cand_id']; 
                $candNo = $candRow['cand_no'];
                $candName = $candRow['cand_name'];

                $regularTotal = 0;

                foreach ($categoryList as $cat) {
                  $catId = $cat['category_id'];
                  $catName = $cat['category_name'];
                  $percent = (float)$cat['percentage'];

                  if (strcasecmp(trim($catName), 'total ranking') === 0) {
                    continue;
                  }

                  $categoryTotalScore = 0;
                  $sqlCriteria = "SELECT criteria_id FROM tbl_criteria WHERE category_id = '$catId'";
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

                  $regularTotal += (($percent / 100) * $categoryTotalScore);
                }

                $overallScore = ($totPer > 0) ? ($regularTotal / $totPer * 100) : 0;

                // Collect data for sorting
                $rankingData[] = [
                  'cand_no' => $candNo,
                  'cand_name' => $candName,
                  'score' => $overallScore
                ];
              }
            }

            // 3. Sort candidates array in descending order (highest score first)
            usort($rankingData, function($a, $b) {
                return $b['score'] <=> $a['score'];
            });

            // 4. Render sorted rows with rank numbering
            $rank = 1;
            foreach ($rankingData as $data) {
                echo "<tr>";
                echo "<td align='center' class='font-weight-bold'>".$rank."</td>";
                echo "<td align='center'>".$data['cand_no']."</td>";
                echo "<td align='center'>".htmlspecialchars($data['cand_name'], ENT_QUOTES, 'UTF-8')."</td>";
                echo "<td align='center' class='font-weight-bold text-primary'>".number_format($data['score'], 2)."%</td>";
                echo "</tr>";
                $rank++;
            }
          ?>
        </tbody>

        </table>

        <!-- Signature -->
        <?php
          $sqlJudges = "SELECT DISTINCT u.* FROM tbl_users AS u, tbl_scores AS s 
                        WHERE u.user_id = s.user_id AND u.status = 'Active' 
                        ORDER BY u.user_id ASC";
          $resultJudges = $conn->query($sqlJudges);

          if ($resultJudges && $resultJudges->num_rows > 0) {
        ?>
          <br>
          <h3 class="d-none">Signature</h3>
          <table style='border: none;' class="table">
            <tr>
              <?php
                while($row = $resultJudges->fetch_assoc()) {
              ?>
                <td style='border: none;'>
                  <center>
                    <u><?php echo htmlspecialchars($row['full_name'], ENT_QUOTES, 'UTF-8'); ?></u>
                    <br>
                    <strong><?php echo $row['user_type']; ?></strong>
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