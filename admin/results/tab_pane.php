<?php 

  include '../config/config.php'; 
  $type = $row['based_type'];

  include '../../connection/conn.php';

  $sql = "SELECT * FROM tbl_category 
          WHERE status = 'Show' 
          ORDER BY category_id ASC";

  $resultCategory = $conn->query($sql);

  if ($resultCategory->num_rows > 0) {

    while($rowCatMain = $resultCategory->fetch_assoc()) {

    $categoryId = $rowCatMain['category_id'];
    $categoryName = $rowCatMain['category_name'];
    $isTotalRanking = (strcasecmp(trim($categoryName), 'total ranking') === 0);

?>

<!-- Tab pane for each category -->
<div id="menu<?php echo $categoryId;?>" class="container tab-pane fade"><br>

  <div class="card mb-3">

    <div class="card-header">
      <i class="fa fa-table"></i> <?php echo $categoryName; ?> Result

      <button class="pull-right btn btn-success" 
          onclick="PrintElem('printableArea<?php echo $categoryId;?>', '<?php echo $categoryName; ?> Result');">
        <i class="fa fa-print"></i> Print
      </button>
      
    </div>

    <div class="container mt-4 ml-3">
      <?php
        if ($isTotalRanking) {
            // For Total Ranking, list the other active categories as buttons/toggles
            $sqlOther = "SELECT * FROM tbl_category WHERE status = 'Show' AND category_id != '$categoryId' ORDER BY category_id ASC";
            $resOther = $conn->query($sqlOther);
            $ctr = 2;
            if ($resOther && $resOther->num_rows > 0) {
                while($rOther = $resOther->fetch_assoc()) {
      ?>
                  <a href="" class="toggle-vis btn btn-success" data-column="<?php echo $ctr; ?>">
                    <i class="fa fa-fw fa-check"></i> <?php echo $rOther['category_name']; ?>
                  </a>
      <?php
                  $ctr++;
                }
            }
            echo '<a href="" class="toggle-vis btn btn-success" data-column="'.$ctr.'"><i class="fa fa-fw fa-check"></i>Total</a>';
        } else {
            // Standard category criteria buttons
            $sql = "SELECT * FROM tbl_criteria 
                    WHERE category_id = '$categoryId' 
                    ORDER BY criteria_id ASC";

            $resultToggle = $conn->query($sql);

            if ($resultToggle->num_rows > 0) {
              $ctr = 2;
              while($rowCritToggle = $resultToggle->fetch_assoc()) {
        ?>
              <a href="" class="toggle-vis btn btn-success" data-column="<?php echo $ctr; ?>">
                <i class="fa fa-fw fa-check"></i> <?php echo $rowCritToggle['criteria_name']; ?>
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

            if ($isTotalRanking) {
                $sqlHeaderCats = "SELECT category_name FROM tbl_category WHERE status = 'Show' AND category_id != '$categoryId' ORDER BY category_id ASC";
                $resHeaderCats = $conn->query($sqlHeaderCats);
                if ($resHeaderCats && $resHeaderCats->num_rows > 0) {
                    while($rHead = $resHeaderCats->fetch_assoc()) {
                        echo "<th>".$rHead['category_name']."</th>";
                    }
                }
                echo "<th>Total</th>";
            } else {
                $sql = "SELECT * FROM tbl_criteria 
                        WHERE category_id = '$categoryId' 
                        ORDER BY criteria_id ASC";
                $resultCriteria = $conn->query($sql);

                if ($resultCriteria && $resultCriteria->num_rows > 0) {
                  while($rowCritHead = $resultCriteria->fetch_assoc()) {
                    echo "<th>".$rowCritHead['criteria_name']."</th>";
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
            $sqlCand = "SELECT * FROM tbl_candidates 
                        WHERE status = 'Allow' 
                        ORDER BY cand_no ASC";

            $resultCand = $conn->query($sqlCand);

            if ($resultCand->num_rows > 0) {
              while($rowCand = $resultCand->fetch_assoc()) {

                $candId = $rowCand['cand_id']; 
                $candNo = $rowCand['cand_no'];
                $candName = $rowCand['cand_name'];

                echo "<tr>";
                echo "<td align='center'>".$candNo."</td>";
                echo "<td align='center'>".htmlspecialchars($candName, ENT_QUOTES, 'UTF-8')."</td>";

                if ($isTotalRanking) {
                    // Calculate and display scores of all other categories combined for Total Ranking tab
                    $sqlOtherCats = "SELECT category_id, percentage FROM tbl_category WHERE status = 'Show' AND category_id != '$categoryId' ORDER BY category_id ASC";
                    $resOtherCats = $conn->query($sqlOtherCats);
                    
                    $grandCategoryTotal = 0;

                    if ($resOtherCats && $resOtherCats->num_rows > 0) {
                        while($rCat = $resOtherCats->fetch_assoc()) {
                            $oCatId = $rCat['category_id'];
                            $oPercent = (float)$rCat['percentage'];
                            
                            $subCatScore = 0;
                            $sqlCrit = "SELECT criteria_id FROM tbl_criteria WHERE category_id = '$oCatId'";
                            $resCrit = $conn->query($sqlCrit);
                            if ($resCrit && $resCrit->num_rows > 0) {
                                while($rCrit = $resCrit->fetch_assoc()) {
                                    $critId = $rCrit['criteria_id'];
                                    $sqlSc = "SELECT AVG(score_points) as pts FROM tbl_scores WHERE cand_id = '$candId' AND criteria_id = '$critId'";
                                    $resSc = $conn->query($sqlSc);
                                    if ($resSc && $resSc->num_rows > 0) {
                                        $rSc = $resSc->fetch_assoc();
                                        $subCatScore += (float)($rSc['pts'] ?? 0);
                                    }
                                }
                            }
                            $factoredSubScore = ($oPercent / 100) * $subCatScore;
                            $grandCategoryTotal += $factoredSubScore;
                            
                            echo "<td align='center'>".number_format($factoredSubScore, 2, '.', '')."</td>";
                        }
                    }
                    echo "<td align='center' class='font-weight-bold'>".number_format($grandCategoryTotal, 2, '.', '')."</td>";

                } else {
                    // Standard Category Loop
                    $total = 0;
                    $sql = "SELECT * FROM tbl_criteria 
                            WHERE category_id = '$categoryId' 
                            ORDER BY criteria_id ASC";

                    $resultCriteriaBody = $conn->query($sql);

                    if ($resultCriteriaBody && $resultCriteriaBody->num_rows > 0) {
                      while($rowCritBody = $resultCriteriaBody->fetch_assoc()) {

                        $criteriaId = $rowCritBody['criteria_id'];
                        
                        $sqlScore = "SELECT CAST(AVG(s.score_points) AS DECIMAL(10,2)) AS 'score_points' 
                                    FROM tbl_scores AS s 
                                    WHERE s.cand_id = '$candId' AND s.criteria_id = '$criteriaId'   
                                    ORDER BY s.criteria_id ASC";

                        $resultScore = $conn->query($sqlScore);
                        $scorePoints = 0;

                        if ($resultScore && $resultScore->num_rows > 0) {
                          $rowScoreData = $resultScore->fetch_assoc();  
                          $scorePoints = (is_null($rowScoreData['score_points'])) ? 0 : (float)$rowScoreData['score_points'];
                        }

                        echo "<td align='center'>".number_format($scorePoints, 2, '.', '')."</td>";          
                        $total += $scorePoints;
                      }
                    }

                    echo "<td align='center'>".number_format($total, 2, '.', '')."</td>";
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

  $conn->close();
?>