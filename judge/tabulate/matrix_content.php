<?php
  session_start();
  if (!isset($_SESSION["userId"])) exit("Unauthorized");
  $judgeId = $_SESSION["userId"];

  include '../../connection/conn.php';

  if (!isset($_POST['categoryId'])) exit("Category ID missing.");
  $categoryId = mysqli_real_escape_string($conn, $_POST['categoryId']);
  $subCategory = isset($_POST['subCategory']) ? mysqli_real_escape_string($conn, $_POST['subCategory']) : 'ALL';

  // Fetch Criteria
  $critResult = $conn->query("SELECT * FROM tbl_criteria WHERE category_id = '$categoryId' AND status = 'Show' ORDER BY criteria_id ASC");
  if ($critResult->num_rows === 0) {
      echo '<div class="alert alert-warning m-3">No active criteria configured for this category yet.</div>';
      exit;
  }

  $criteriaList = [];
  while($c = $critResult->fetch_assoc()) {
      $criteriaList[] = $c;
  }

  // Fetch Candidates with Sub-category filter logic
  $whereClause = "status = 'Allow'";
  if ($subCategory !== 'ALL') {
      $whereClause .= " AND cand_category = '$subCategory'";
  }
  $candResult = $conn->query("SELECT * FROM tbl_candidates WHERE $whereClause ORDER BY cand_no ASC");

  $configResult = $conn->query("SELECT based_type FROM tbl_config");
  $type = $configResult->fetch_assoc()['based_type'] ?? 'Candidate';
?>

<style>
  /* Sticky Column for House / Category */
  .table-responsive {
    overflow-x: auto;
    position: relative;
  }
  
  .table thead th.sticky-col-name {
    position: sticky;
    left: 0;
    z-index: 5;
    background-color: #343a40 !important;
  }

  .sticky-col-name {
    position: sticky;
    left: 0;
    z-index: 2;
    width: 140px;
  }

  /* Background overrides for sticky cell to prevent text bleeding during scroll */
  tr.house-row-azul .sticky-col-name { background-color: rgba(0, 123, 255, 0.25) !important; }
  tr.house-row-cahel .sticky-col-name { background-color: rgba(255, 140, 0, 0.28) !important; }
  tr.house-row-roxxo .sticky-col-name { background-color: rgba(255, 69, 58, 0.25) !important; }
  tr.house-row-vierrdy .sticky-col-name { background-color: rgba(40, 200, 64, 0.25) !important; }
  tr.house-row-giallo .sticky-col-name { background-color: rgba(255, 215, 0, 0.35) !important; }

  /* Metallic and Shiny House Row Borders & Soft Vibrant Tints */
  .house-row-azul { 
    background-color: rgba(0, 123, 255, 0.15) !important; 
    border-top: 2px solid #70b5ff !important;
    border-bottom: 2px solid #0056b3 !important;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6), 0 2px 4px rgba(0, 123, 255, 0.2);
  }
  .house-row-cahel { 
    background-color: rgba(255, 140, 0, 0.18) !important; 
    border-top: 2px solid #ffb366 !important;
    border-bottom: 2px solid #b35900 !important;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6), 0 2px 4px rgba(255, 140, 0, 0.2);
  }
  .house-row-roxxo { 
    background-color: rgba(255, 69, 58, 0.15) !important; 
    border-top: 2px solid #ff9992 !important;
    border-bottom: 2px solid #c51206 !important;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6), 0 2px 4px rgba(255, 69, 58, 0.2);
  }
  .house-row-vierrdy { 
    background-color: rgba(40, 200, 64, 0.15) !important; 
    border-top: 2px solid #85e094 !important;
    border-bottom: 2px solid #197a29 !important;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6), 0 2px 4px rgba(40, 200, 64, 0.2);
  }
  .house-row-giallo { 
    background-color: rgba(255, 215, 0, 0.22) !important; 
    border-top: 2px solid #fff0b3 !important;
    border-bottom: 2px solid #b39200 !important;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7), 0 2px 4px rgba(255, 215, 0, 0.25);
  }

  .table tbody tr {
    transition: all 0.2s ease;
  }
</style>

<div class="table-responsive shadow-sm bg-white rounded border">
  <table class="table table-bordered table-hover align-middle mb-0">
    <thead class="thead-dark text-center">
      <tr>
        <th style="width: 70px; vertical-align: middle;">No.</th>
        <th class="sticky-col-name text-left" style="width: 140px; vertical-align: middle;"><?php echo htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?> / Category</th>
        <?php foreach ($criteriaList as $crit): ?>
          <th style="min-width: 150px; vertical-align: middle;">
            <?php echo htmlspecialchars($crit['criteria_name'], ENT_QUOTES, 'UTF-8'); ?>
            <br><small class="text-warning font-weight-normal">(Max: <?php echo $crit['criteria_points']; ?> pts)</small>
          </th>
        <?php endforeach; ?>
        <th style="width: 130px; vertical-align: middle;">Total / Status</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if ($candResult->num_rows > 0) {
          while ($cand = $candResult->fetch_assoc()) {
              $candId = $cand['cand_id'];
              $candNo = $cand['cand_no'];
              $candName = trim($cand['cand_name']);
              $candCat = $cand['cand_category'] ?? '';
              $displayName = ($type == 'Candidate') ? "#" . $candNo . " - " . $candName : $candName;

              // Determine house color class based on candidate name
              $houseLower = strtolower($candName);
              $houseClass = "";
              if (strpos($houseLower, 'azul') !== false) {
                  $houseClass = "house-row-azul";
              } elseif (strpos($houseLower, 'cahel') !== false) {
                  $houseClass = "house-row-cahel";
              } elseif (strpos($houseLower, 'roxxo') !== false) {
                  $houseClass = "house-row-roxxo";
              } elseif (strpos($houseLower, 'vierrdy') !== false) {
                  $houseClass = "house-row-vierrdy";
              } elseif (strpos($houseLower, 'giallo') !== false) {
                  $houseClass = "house-row-giallo";
              }
      ?>
      <tr class="<?php echo $houseClass; ?>">
        <td class="text-center font-weight-bold align-middle"><?php echo htmlspecialchars($candNo, ENT_QUOTES, 'UTF-8'); ?></td>
        <td class="sticky-col-name align-middle">
          <div class="font-weight-bold text-dark" style="font-size: 1.05rem;"><?php echo htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8'); ?></div>
          <?php if (!empty($candCat)): ?>
            <span class="badge badge-info mt-1" style="font-size: 10px;"><?php echo htmlspecialchars($candCat, ENT_QUOTES, 'UTF-8'); ?></span>
          <?php endif; ?>
        </td>

        <?php 
        $rowTotal = 0;
        foreach ($criteriaList as $crit) {
            $critId = $crit['criteria_id'];
            $maxPts = floatval($crit['criteria_points']);

            // Fetch existing score from database
            $scoreQuery = "SELECT score_points FROM tbl_scores WHERE user_id = '$judgeId' AND cand_id = '$candId' AND category_id = '$categoryId' AND criteria_id = '$critId'";
            $scoreRes = $conn->query($scoreQuery);
            $existingScore = 0;
            if ($scoreRes->num_rows > 0) {
                $existingScore = floatval($scoreRes->fetch_assoc()['score_points']);
                $rowTotal += $existingScore;
            }
        ?>
          <td class="text-center align-middle">
            <input type="number" 
                   class="form-control form-control-sm text-center matrix-score-input font-weight-bold bg-white" 
                   name="scores[<?php echo $candId; ?>][<?php echo $critId; ?>]" 
                   value="<?php echo $existingScore > 0 ? $existingScore : ''; ?>" 
                   min="0" max="<?php echo $maxPts; ?>" step="any"
                   data-cand-id="<?php echo $candId; ?>" data-max="<?php echo $maxPts; ?>"
                   oninput="validateMatrixInput(this)"
                   onchange="autoSaveCandidateScores(this)">
          </td>
        <?php } ?>

        <td class="text-center align-middle bg-white">
          <span id="rowTotal_<?php echo $candId; ?>" class="font-weight-bold text-primary" style="font-size: 1.1rem;"><?php echo $rowTotal; ?></span>
          <br>
          <span id="saveStatus_<?php echo $candId; ?>" class="badge badge-light border text-muted" style="font-size: 9px;">Ready</span>
        </td>
      </tr>
      <?php
          }
      } else {
          echo '<tr><td colspan="' . (count($criteriaList) + 3) . '" class="text-center text-muted py-4">No contestants found for this category filter.</td></tr>';
      }
      ?>
    </tbody>
  </table>
</div>

<script>
  function validateMatrixInput(input) {
    var max = parseFloat(input.getAttribute('data-max')) || 0;
    var val = parseFloat(input.value);

    if (val < 0) input.value = 0;
    if (val > max) {
      $.notify('Score cannot exceed max points (' + max + ')', { className: 'warn', globalPosition: 'top right', autoHideDelay: 2000 });
      input.value = max;
    }

    var candId = input.getAttribute('data-cand-id');
    recalculateRowTotal(candId);
  }

  function recalculateRowTotal(candId) {
    var sum = 0;
    $('input[name^="scores[' + candId + ']"]').each(function() {
      sum += parseFloat(this.value) || 0;
    });
    $('#rowTotal_' + candId).text(sum);
  }

  function autoSaveCandidateScores(input) {
    var candId = input.getAttribute('data-cand-id');
    var categoryId = $('#activeCategoryId').val();
    var $statusBadge = $('#saveStatus_' + candId);

    var candidateScores = {};
    $('input[name^="scores[' + candId + ']"]').each(function() {
      var nameAttr = $(this).attr('name');
      var match = nameAttr.match(/\[(\d+)\]\[(\d+)\]/);
      if (match) {
        var critId = match[2];
        var val = $(this).val();
        if (val !== '') {
          candidateScores[critId] = val;
        }
      }
    });

    $statusBadge.removeClass('badge-light badge-success badge-danger text-muted').addClass('badge-info text-white').text('Saving...');

    var postData = {
      activeCategoryId: categoryId,
      scores: {}
    };
    postData.scores[candId] = candidateScores;

    $.ajax({
      url: 'tabulate/save.php',
      type: 'POST',
      data: postData,
      success: function(response) {
        $statusBadge.removeClass('badge-info badge-danger text-white').addClass('badge-success text-white').text('Saved ✓');
        setTimeout(function() {
          $statusBadge.removeClass('badge-success text-white').addClass('badge-light border text-muted').text('Saved');
        }, 2000);
      },
      error: function() {
        $statusBadge.removeClass('badge-info badge-success text-white').addClass('badge-danger text-white').text('Failed!');
        $.notify('Could not save score. Please check your connection.', { className: 'error', globalPosition: 'top right', autoHideDelay: 4000 });
      }
    });
  }
</script>