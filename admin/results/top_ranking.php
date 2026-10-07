<?php
include '../config/config.php';
$type = $row['based_type'];
?>

<!-- Top Ranking tab pane -->
<div id="menuTopRanking" class="container tab-pane fade"><br>
  
  <div class="card mb-3">

    <div class="card-header">
      <i class="fa fa-trophy"></i> Top Ranking — Overall & Per Category

      <button class="pull-right btn btn-success" 
          onclick="PrintElem('printableAreaTopRanking', 'Top Ranking Result');">
        <i class="fa fa-print"></i> Print
      </button>

    </div>

    <div class="card-body">

      <div class="table-responsive" id="printableAreaTopRanking">

        <?php 
          include '../../connection/conn.php';

          // 1. Fetch categories to compute total percentage weights for overall score
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
          $allCandidates = [];

          if ($resultCand && $resultCand->num_rows > 0) {
            while($candRow = $resultCand->fetch_assoc()) {
              $allCandidates[] = $candRow;
            }
          }

          $hasAnyRanking = false;

          // Helper function mapping none/empty to a blank string group key
          function getGroupKey($candCat) {
              $val = strtoupper(trim($candCat ?? ''));
              if ($val === '' || $val === 'NONE') {
                  return '';
              }
              return $val;
          }

          // ==========================================
          // PART A: OVERALL RANKINGS
          // ==========================================
          $overallGroups = [
              '' => [],
              'FEMALE' => [],
              'MALE' => [],
              'GROUP' => []
          ];

          foreach ($allCandidates as $candRow) {
              $candId = $candRow['cand_id']; 
              $candNo = $candRow['cand_no'];
              $candName = $candRow['cand_name'];
              $candCategory = getGroupKey($candRow['cand_category']);
              
              if (!array_key_exists($candCategory, $overallGroups)) {
                  $candCategory = '';
              }

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

              $overallGroups[$candCategory][] = $candidateEntry;
          }

          foreach ($overallGroups as $groupKey => $candList) {
              if (empty($candList)) {
                  continue;
              }

              $hasAnyRanking = true;

              usort($candList, function($a, $b) {
                  return $b['score'] <=> $a['score'];
              });

              $headerTitle = ($groupKey === '') ? 'Overall Ranking' : 'Overall Ranking — ' . $groupKey;
        ?>

          <h4 class="text-secondary mt-4 mb-2">
            <i class="fa fa-trophy"></i> <?php echo $headerTitle; ?>
          </h4>
          
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
                foreach ($candList as $data) {
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
          } // end foreach overallGroups


          // ==========================================
          // PART B: CRITERIA-BASED CATEGORY RANKINGS
          // ==========================================
          $sqlCatCriteria = "SELECT DISTINCT c.* FROM tbl_category c
                             INNER JOIN tbl_criteria cr ON c.category_id = cr.category_id
                             WHERE c.status = 'Show'
                             ORDER BY c.category_id ASC";
          $resCatCriteria = $conn->query($sqlCatCriteria);
          $criteriaCategoryList = [];

          if ($resCatCriteria && $resCatCriteria->num_rows > 0) {
            while($catRow = $resCatCriteria->fetch_assoc()) {
              if (strcasecmp(trim($catRow['category_name']), 'total ranking') !== 0) {
                $criteriaCategoryList[] = $catRow;
              }
            }
          }

          foreach ($criteriaCategoryList as $cat) {
              $catId = $cat['category_id'];
              $catName = $cat['category_name'];

              $categoriesData = [
                  '' => [],
                  'FEMALE' => [],
                  'MALE' => [],
                  'GROUP' => []
              ];

              foreach ($allCandidates as $candRow) {
                  $candId = $candRow['cand_id']; 
                  $candNo = $candRow['cand_no'];
                  $candName = $candRow['cand_name'];
                  $candCategory = getGroupKey($candRow['cand_category']);
                  
                  if (!array_key_exists($candCategory, $categoriesData)) {
                      $candCategory = '';
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

                  $candidateEntry = [
                    'cand_no' => $candNo,
                    'cand_name' => $candName,
                    'score' => $categoryTotalScore
                  ];

                  $categoriesData[$candCategory][] = $candidateEntry;
              }

              foreach ($categoriesData as $groupKey => $catCandidates) {
                  if (empty($catCandidates)) {
                      continue;
                  }

                  $hasAnyRanking = true;

                  usort($catCandidates, function($a, $b) {
                      return $b['score'] <=> $a['score'];
                  });

                  $headerTitle = ($groupKey === '') ? htmlspecialchars($catName, ENT_QUOTES, 'UTF-8') : htmlspecialchars($catName, ENT_QUOTES, 'UTF-8') . ' — ' . $groupKey;
        ?>

          <h4 class="text-secondary mt-4 mb-2">
            <i class="fa fa-trophy"></i> <?php echo $headerTitle; ?>
          </h4>
          
          <table class="table table-bordered table-hover table-sm mb-4" width="100%" cellspacing="0">
            <thead>
              <tr>
                <th width="10%" class="text-center">Rank</th>
                <th class="text-center"><?php echo $type; ?> No</th>
                <th class="text-center"><?php echo $type; ?> Name</th>
                <th class="text-center">Category Score</th>
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
                    echo "<td align='center' class='font-weight-bold text-primary'>".number_format($data['score'], 2)."</td>";
                    echo "</tr>";
                    $rank++;
                }
              ?>
            </tbody>
          </table>

        <?php 
              } // end foreach groupKey
          } // end foreach criteriaCategoryList

          if (!$hasAnyRanking) {
              echo '<div class="alert alert-warning text-center">No rankings available because no criteria, scores, or categorized candidates are present.</div>';
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