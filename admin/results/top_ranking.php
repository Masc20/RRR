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
          require_once __DIR__ . '/../../lib/scorecalculator.php';

          $hasAnyRanking = false;

          // ==========================================
          // PART A: OVERALL RANKINGS
          // ==========================================
          $divisionGroups = ['', 'FEMALE', 'MALE', 'GROUP'];

          foreach ($divisionGroups as $groupKey) {
              $candList = ScoreCalculator::getOverallRankings($conn, $groupKey);
              if (empty($candList)) {
                  continue;
              }

              $hasAnyRanking = true;
              $headerTitle = ($groupKey === '') ? 'Overall Ranking' : 'Overall Ranking - ' . $groupKey;
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
                foreach ($candList as $data) {
                    echo "<tr>";
                    echo "<td align='center' class='font-weight-bold'>".$data['rank']."</td>";
                    echo "<td align='center'>".$data['cand_no']."</td>";
                    echo "<td align='center'>".htmlspecialchars($data['cand_name'], ENT_QUOTES, 'UTF-8')."</td>";
                    echo "<td align='center' class='font-weight-bold text-primary'>".ScoreCalculator::format($data['score'])."%</td>";
                    echo "</tr>";
                }
              ?>
            </tbody>
          </table>

        <?php 
          } // end foreach divisionGroups


          // ==========================================
          // PART B: CRITERIA-BASED CATEGORY RANKINGS
          // ==========================================
          $criteriaCategoryList = ScoreCalculator::getActiveCategories($conn, false);

          foreach ($criteriaCategoryList as $cat) {
              $catId = $cat['category_id'];
              $catName = $cat['category_name'];

              foreach ($divisionGroups as $groupKey) {
                  $catCandidates = ScoreCalculator::getCategoryRankings($conn, $catId, $groupKey);
                  if (empty($catCandidates)) {
                      continue;
                  }

                  $hasAnyRanking = true;
                  $headerTitle = ($groupKey === '') ? htmlspecialchars($catName, ENT_QUOTES, 'UTF-8') : htmlspecialchars($catName, ENT_QUOTES, 'UTF-8') . ' - ' . $groupKey;
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
                foreach ($catCandidates as $data) {
                    echo "<tr>";
                    echo "<td align='center' class='font-weight-bold'>".$data['rank']."</td>";
                    echo "<td align='center'>".$data['cand_no']."</td>";
                    echo "<td align='center'>".htmlspecialchars($data['cand_name'], ENT_QUOTES, 'UTF-8')."</td>";
                    echo "<td align='center' class='font-weight-bold text-primary'>".ScoreCalculator::format($data['score'])."</td>";
                    echo "</tr>";
                }
              ?>
            </tbody>
          </table>

        <?php 
              } // end foreach divisionGroups
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