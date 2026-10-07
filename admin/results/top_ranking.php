<?php
include '../config/config.php'; 
$type = $row['based_type'];
?>

<!-- Top Ranking tab pane -->
<div id="menuTopRanking" class="container tab-pane fade"><br>
  
  <div class="card mb-3">

    <div class="card-header">
      <i class="fa fa-trophy"></i> Top Ranking / Highest Scorers (By Category)

      <button class="pull-right btn btn-success" 
          onclick="PrintElem('printableAreaTopRanking', 'Top Ranking Result');">
        <i class="fa fa-print"></i> Print
      </button>

    </div>

    <div class="card-body">

      <div class="table-responsive" id="printableAreaTopRanking">

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

          // 2. Fetch all allowed candidates
          $sqlCand = "SELECT * FROM tbl_candidates WHERE status = 'Allow'";
          $resultCand = $conn->query($sqlCand);
          
          // Categorized arrays
          $categoriesData = [
              'FEMALE' => [],
              'MALE' => [],
              'GROUP' => []
          ];

          if ($resultCand && $resultCand->num_rows > 0) {
            while($candRow = $resultCand->fetch_assoc()) {
              $candId = $candRow['cand_id']; 
              $candNo = $candRow['cand_no'];
              $candName = $candRow['cand_name'];
              // Normalize category key to uppercase (default to FEMALE or handle empty if needed)
              $candCategory = strtoupper(trim($candRow['cand_category'] ?? ''));

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

              $candidateEntry = [
                'cand_no' => $candNo,
                'cand_name' => $candName,
                'score' => $overallScore
              ];

              // Push into corresponding category group if it matches
              if (array_key_exists($candCategory, $categoriesData)) {
                  $categoriesData[$candCategory][] = $candidateEntry;
              }
            }
          }

          // Flag to check if any category has candidates
          $hasAnyCategory = false;

          // Loop through each category to sort and render tables dynamically
          foreach ($categoriesData as $catName => $catCandidates) {
              // Skip rendering if no candidates exist for this category
              if (empty($catCandidates)) {
                  continue;
              }

              $hasAnyCategory = true;

              // Sort candidates in descending order (highest score first)
              usort($catCandidates, function($a, $b) {
                  return $b['score'] <=> $a['score'];
              });
        ?>

          <!-- Category Header -->
          <h4 class="text-secondary mt-4 mb-2"><i class="fa fa-angle-right"></i> Category: <?php echo $catName; ?></h4>
          
          <table class="table table-bordered table-hover table-sm mb-4" width="100%" cellspacing="0">
            <thead>
              <tr>
                <th width="10%" class="text-center">Rank</th>
                <th class="text-center"><?php echo $type; ?> No</th>
                <th class="text-center"><?php echo $type; ?> Name</th>
                <th class="text-center">Final Score / Percentage</th>
              </tr>
            </thead>
            <tbody>
              <?php 
                $rank = 1;
                foreach ($catCandidates as $data) {
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

        <?php 
          } // end foreach category

          if (!$hasAnyCategory) {
              echo '<div class="alert alert-warning text-center">No rankings available because no candidates have been categorized or allowed yet.</div>';
          }
        ?>

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