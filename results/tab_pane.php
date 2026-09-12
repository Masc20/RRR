<?php 

  include '../config/config.php'; 
  $type = $row['based_type'];


  include '../../connection/conn.php';

  $sql = "SELECT * FROM tbl_category 
          WHERE status = 'Show' 
          ORDER BY category_id ASC";

  $resultCategory = $conn->query($sql);

  if ($resultCategory->num_rows > 0) {

    while($row = $resultCategory->fetch_assoc()) {

    $categoryId = $row['category_id'];
    $categoryName = $row['category_name'];

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

        $sql = "SELECT * FROM tbl_criteria 
                WHERE category_id = '$categoryId' 
                ORDER BY criteria_id ASC";

        $resultToggle = $conn->query($sql);

        if ($resultToggle->num_rows > 0) {
          $ctr = 2;
          while($row = $resultToggle->fetch_assoc()) {
      ?>

      
        <a href="" class="toggle-vis btn btn-success" data-column="<?php echo $ctr; ?>">
          <i class="fa fa-fw fa-check"></i> <?php echo $row['criteria_name']; ?>
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
      ?>
    </div>

    <div class="card-body">
      <div class="table-responsive" id="printableArea<?php echo $categoryId;?>">
      <!-- display: none; -->
        <center style="">
          <h2 id="resTitle">(The Result)</h2>
        </center>
      <?php

        $sql = "SELECT * FROM tbl_criteria 
                WHERE category_id = '$categoryId' 
                ORDER BY criteria_id ASC";

        $resultCriteria = $conn->query($sql);

        if ($resultCriteria->num_rows > 0) {

      ?>

      <table class="table table-bordered table-hover table-sm dataTable" width="100%" cellspacing="0">

      <!-- table header -->
      <thead>
        <tr>
          <?php
       
            echo "<th>".$type." No.</th>";
            echo "<th>".$type." Name</th>";

            while($row = $resultCriteria->fetch_assoc()) {

              $criteriaName = $row['criteria_name'];

              echo "<th>".$criteriaName."</th>";
            }

            echo "<th>Total</th>";

          ?>
        </tr>
      </thead>
      <!-- /table header -->

      <!-- table body -->
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

                      $scorePoints = (is_null($row['score_points'])) ? number_format(0, 2, '.', '') : $row['score_points'];


                      echo "<td align='center'>".$scorePoints."</td>";          
                    }

                    $total += $scorePoints;
                  }

                }

                echo "<td align='center'>".number_format($total, 2, '.', '')."</td>";

                echo "</tr>";
                
              }
            }

          ?>
          
      </tbody>
      <!-- /table body -->

      </table>

      <?php

        }
      ?>

      <!-- Signature -->
      <?php

        $sql = "SELECT DISTINCT u.* FROM tbl_users AS u, tbl_scores AS s 
                WHERE u.user_id = s.user_id AND
                      s.category_id = '$categoryId' AND 
                      u.status = 'Active' 
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
      ?>
      <!-- /signature -->

      </div>
      <!-- end table responsive -->
    </div>
    <!-- end card body -->

    <div class="card-footer small text-muted"></div>
  </div>

</div>
<!-- end div tab -->

<?php
    }
  }

  $conn->close();
?>
