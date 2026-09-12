<?php

include '../config/config.php'; 
$type = $row['based_type'];

?>

<!-- Overall result tab pane -->
<div id="menuOverall" class="container tab-pane fade"><br>
  
  <div class="card mb-3">

    <div class="card-header">
      <i class="fa fa-table"></i> Overall Result

      <button class="pull-right btn btn-success" 
          onclick="PrintElem('printableArea', 'Overall Result');">
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

            <?php

              include '../../connection/conn.php';

              $sql = "SELECT * FROM tbl_category 
                      WHERE status = 'Show' 
                      ORDER BY category_id ASC";
              
              $resultCategory = $conn->query($sql);

              if ($resultCategory->num_rows > 0) {

                while($row = $resultCategory->fetch_assoc()) {

                  $categoryName = $row['category_name'];
                  $percent = $row['percentage'];

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

                $total = 0;

                $sql = "SELECT * FROM tbl_category 
                      WHERE status = 'Show' 
                      ORDER BY category_id ASC";

                $resultCategory = $conn->query($sql);

                if ($resultCategory->num_rows > 0) {

                  $overall = 0;

                  while($row = $resultCategory->fetch_assoc()) {

                    $total = 0;

                    $categoryId = $row['category_id'];
                    $percent = $row['percentage']; 

                    $sql = "SELECT * FROM tbl_criteria 
                            WHERE category_id = '$categoryId' 
                            ORDER BY criteria_id ASC";

                    $resultCriteria = $conn->query($sql);

                    if ($resultCriteria->num_rows > 0) {

                      while($row = $resultCriteria->fetch_assoc()) {

                        $criteriaId = $row['criteria_id'];
                    
                        $sql = "SELECT CAST(AVG(s.score_points) AS DECIMAL(10,2)) AS 'score_points' 
                                FROM tbl_scores AS s 
                                WHERE s.cand_id = '$candId' AND s.criteria_id = '$criteriaId'   
                                ORDER BY s.criteria_id ASC";

                        $resultScore = $conn->query($sql);

                        if ($resultScore->num_rows > 0) {

                          $row = $resultScore->fetch_assoc();  

                          $scorePoints = (is_null($row['score_points'])) ? 0 : $row['score_points'];

                        }

                        $total += $scorePoints;
                      }

                    }
                      
                    $total = ($percent / 100) * $total;
                    echo "<td align='center'>".number_format($total, 2, '.', '')."</td>";
                    $overall += $total;
                  }

                  echo "<td align='center'>".number_format($overall, 2, '.', '')."</td>";
                }

                
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