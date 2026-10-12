<?php 

  if (file_exists(__DIR__ . '/../admin/config/config.php')) {
    include __DIR__ . '/../admin/config/config.php';
  } elseif (file_exists(__DIR__ . '/../config/config.php')) {
    include __DIR__ . '/../config/config.php';
  }
  $type = $row['based_type'] ?? 'Candidate';

  if (!isset($conn) || !$conn instanceof mysqli || @!$conn->stat()) {
    include __DIR__ . '/../connection/conn.php';
  }
  require_once __DIR__ . '/../lib/scorecalculator.php';

  $categories = ScoreCalculator::getActiveCategories($conn, true);

  if (!empty($categories)) {

    foreach ($categories as $rowCatMain) {

    $categoryId = $rowCatMain['category_id'];
    $categoryName = $rowCatMain['category_name'];
    $isTotalRanking = ScoreCalculator::isTotalRanking($categoryName);

    // 1. Fetch allowed candidates and check if any have an assigned category
    $hasAnyCategoryAssigned = ScoreCalculator::hasAnyCategoryAssigned($conn);
    $sqlCand = "SELECT * FROM tbl_candidates WHERE status = 'Allow' ORDER BY cand_no ASC";
    $resultCand = $conn->query($sqlCand);
    $allCandidates = [];

    if ($resultCand && $resultCand->num_rows > 0) {
        while($rowCand = $resultCand->fetch_assoc()) {
            $allCandidates[] = $rowCand;
        }
    }

?>

<!-- Tab pane for each category -->
<div id="menu<?php echo $categoryId;?>" class="container tab-pane fade"><br>

  <div class="card mb-3">

    <div class="card-header">
      <i class="fa fa-table"></i> <?php echo htmlspecialchars($categoryName); ?> Result

      <button class="pull-right btn btn-success" 
          onclick="PrintElem('printableArea<?php echo $categoryId;?>', '<?php echo htmlspecialchars($categoryName); ?> Result');">
        <i class="fa fa-print"></i> Print
      </button>
      
    </div>

    <div class="container mt-4 ml-3">
      <?php
        // Dynamically set starting column index for toggles based on whether Category column exists
        $ctr = $hasAnyCategoryAssigned ? 3 : 2;

        if ($isTotalRanking) {
            $otherCats = ScoreCalculator::getActiveCategories($conn, false);
            foreach ($otherCats as $rOther) {
      ?>
                  <a href="" class="toggle-vis btn btn-success" data-column="<?php echo $ctr; ?>">
                    <i class="fa fa-fw fa-check"></i> <?php echo htmlspecialchars($rOther['category_name']); ?>
                  </a>
      <?php
                  $ctr++;
            }
            echo '<a href="" class="toggle-vis btn btn-success" data-column="'.$ctr.'"><i class="fa fa-fw fa-check"></i>Total</a>';
        } else {
            $sql = "SELECT * FROM tbl_criteria 
                    WHERE category_id = '$categoryId' 
                    ORDER BY criteria_id ASC";

            $resultToggle = $conn->query($sql);

            if ($resultToggle && $resultToggle->num_rows > 0) {
              while($rowCritToggle = $resultToggle->fetch_assoc()) {
        ?>
              <a href="" class="toggle-vis btn btn-success" data-column="<?php echo $ctr; ?>">
                <i class="fa fa-fw fa-check"></i> <?php echo htmlspecialchars($rowCritToggle['criteria_name']); ?>
              </a>
        <?php
                $ctr++;
              }
              ?>
                <a href="" class="toggle-vis btn btn-success" data-column="<?php echo $ctr; ?>">
                  <i class="fa fa-fw fa-check"></i>Total
                </a>
              <?php
            }
        }
      ?>
    </div>

    <div class="card-body">
      <div class="table-responsive" id="printableArea<?php echo $categoryId;?>">
        <center>
          <h2 id="resTitle">(The Result)</h2>
        </center>

      <table class="table table-bordered table-hover table-sm dataTable" width="100%" cellspacing="0">

      <!-- table header -->
      <thead>
        <tr>
          <?php
            echo "<th>".$type." No.</th>";
            echo "<th>".$type." Name</th>";
            
            if ($hasAnyCategoryAssigned) {
                echo "<th>Category</th>";
            }

            if ($isTotalRanking) {
                $otherCats = ScoreCalculator::getActiveCategories($conn, false);
                foreach ($otherCats as $rHead) {
                    echo "<th>".htmlspecialchars($rHead['category_name'])."</th>";
                }
                echo "<th>Total</th>";
            } else {
                $sql = "SELECT * FROM tbl_criteria 
                        WHERE category_id = '$categoryId' 
                        ORDER BY criteria_id ASC";
                $resultCriteria = $conn->query($sql);

                if ($resultCriteria && $resultCriteria->num_rows > 0) {
                  while($rowCritHead = $resultCriteria->fetch_assoc()) {
                    echo "<th>".htmlspecialchars($rowCritHead['criteria_name'])."</th>";
                  }
                }
                echo "<th>Total</th>";
            }
          ?>
        </tr>
      </thead>
      <!-- /table header -->

      <!-- table body -->
      <tbody>
          <?php
            if (!empty($allCandidates)) {
              foreach ($allCandidates as $rowCand) {

                $candId = $rowCand['cand_id']; 
                $candNo = $rowCand['cand_no'];
                $candName = $rowCand['cand_name'];
                $candCategory = ScoreCalculator::determineDivision($candNo, $rowCand['cand_category'] ?? null);

                echo "<tr>";
                echo "<td align='center'>".$candNo."</td>";
                echo "<td align='center'>".htmlspecialchars($candName, ENT_QUOTES, 'UTF-8')."</td>";
                
                if ($hasAnyCategoryAssigned) {
                    echo "<td align='center'>".$candCategory."</td>";
                }

                if ($isTotalRanking) {
                    $breakdown = ScoreCalculator::getCandidateScoreBreakdown($conn, $candId);
                    
                    foreach ($breakdown['regular_scores'] as $catScore) {
                        echo "<td align='center'>".ScoreCalculator::format($catScore)."</td>";
                    }
                    echo "<td align='center' class='font-weight-bold'>".ScoreCalculator::format($breakdown['grand_total'])."</td>";

                } else {
                    $total = 0;
                    $sqlCrit = "SELECT criteria_id FROM tbl_criteria 
                                WHERE category_id = '$categoryId' 
                                ORDER BY criteria_id ASC";

                    $resultCriteriaBody = $conn->query($sqlCrit);

                    if ($resultCriteriaBody && $resultCriteriaBody->num_rows > 0) {
                      while($rowCritBody = $resultCriteriaBody->fetch_assoc()) {
                        $criteriaId = $rowCritBody['criteria_id'];
                        $scorePoints = ScoreCalculator::getCriteriaAverage($conn, $candId, $criteriaId);
                        echo "<td align='center'>".ScoreCalculator::format($scorePoints)."</td>";          
                        $total += $scorePoints;
                      }
                    }

                    echo "<td align='center'>".ScoreCalculator::format($total)."</td>";
                }

                echo "</tr>";
              }
            }
          ?>
      </tbody>
      <!-- /table body -->

      </table>

      <!-- Signature -->
      <?php
        $sqlJudges = "SELECT DISTINCT u.* FROM tbl_users AS u, tbl_scores AS s 
                      WHERE u.user_id = s.user_id AND
                            s.category_id = '$categoryId' AND 
                            u.status = 'Active' 
                      ORDER BY u.user_id ASC";

        $resultJudges = $conn->query($sqlJudges);

        if ($resultJudges && $resultJudges->num_rows > 0) {
      ?>
          <br>
          <h3 class="d-none">Signatures</h3>

          <table style='border: none;' class="table">
            <tr>
              <?php
                while($rowJudge = $resultJudges->fetch_assoc()) {
                  $judgeName = $rowJudge['full_name'];
                  $judgeType = $rowJudge['user_type'];
              ?>
                  <td style='border: none;'>
                    <center>
                      <u><?php echo htmlspecialchars($judgeName); ?></u>
                      <br>
                      <strong><?php echo htmlspecialchars($judgeType); ?></strong>
                    </center>
                  </td>
              <?php
                }
              ?>
            </tr>
          </table>
      <?php
        }
      ?>
      <!-- /signature -->

      </div>
    </div>

    <div class="card-footer small text-muted"></div>
  </div>

</div>
<!-- end div tab -->

<?php
    }
  }
?>
