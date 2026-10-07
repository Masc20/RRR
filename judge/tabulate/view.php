<?php
  session_start();
  if (!isset($_SESSION['userId'])) {
      exit("Unauthorized");
  }
  $judgeId = $_SESSION['userId'];

  include '../../connection/conn.php';

  $sqlConfig = "SELECT * FROM tbl_config";
  $resultConfig = $conn->query($sqlConfig);
  $rowConfig = $resultConfig->fetch_assoc();
  $type = $rowConfig['based_type']; // 'Candidate' or 'House'

  // Fetch active categories (excluding total ranking)
  $sqlCat = "SELECT * FROM tbl_category WHERE status = 'Show' AND LOWER(TRIM(category_name)) != 'total ranking' ORDER BY category_id ASC";
  $resultCat = $conn->query($sqlCat);

  // Automatically check which sub-categories actually exist among allowed candidates
  $subCatResult = $conn->query("SELECT DISTINCT cand_category FROM tbl_candidates WHERE status = 'Allow' AND cand_category IS NOT NULL AND TRIM(cand_category) != ''");
  $availableSubCats = [];
  while($scRow = $subCatResult->fetch_assoc()) {
      $availableSubCats[] = strtoupper(trim($scRow['cand_category']));
  }
?>

<div class="card mb-3">
  <div class="card-header bg-dark text-white d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3">
    <h5 class="mb-2 mb-md-0"><i class="fa fa-table"></i> Master Tabulation Sheet (<?php echo htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?>s)</h5>
    <span class="badge badge-success px-3 py-2 font-weight-bold" id="globalAutoSaveStatus" style="font-size: 0.9rem;">
      <i class="fa fa-check-circle"></i> Auto-save Active
    </span>
  </div>
  
  <div class="card-body px-2 px-md-3">
    <!-- Category Tabs Navigation -->
    <ul class="nav nav-tabs judge-category-tabs mb-3" role="tablist" id="matrixCategoryTabs">
      <?php
      if ($resultCat->num_rows > 0) {
          $isFirst = true;
          $firstCatId = "";
          while($cat = $resultCat->fetch_assoc()) {
              $catId = $cat['category_id'];
              $catName = $cat['category_name'];
              if ($isFirst) {
                  $firstCatId = $catId;
              }
      ?>
        <li class="nav-item">
          <a class="nav-link tab-switch-link <?php echo $isFirst ? 'active' : ''; ?>" 
             href="#" 
             data-category-id="<?php echo $catId; ?>" 
             onclick="switchMatrixCategory(this, '<?php echo $catId; ?>'); return false;">
            <?php echo htmlspecialchars($catName, ENT_QUOTES, 'UTF-8'); ?>
          </a>
        </li>
      <?php
              $isFirst = false;
          }
      }
      ?>
    </ul>

    <!-- Automatic & Fully Responsive Sub-Category Filter Bar -->
    <?php if (!empty($availableSubCats)): ?>
    <div class="bg-light p-2 mb-3 rounded border">
      <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between">
        <span class="font-weight-bold text-secondary mb-2 mb-sm-0 ml-1" style="font-size: 0.85rem;"><i class="fa fa-filter"></i> Filter:</span>
        <div class="btn-group btn-group-toggle flex-wrap w-100 w-sm-auto" data-toggle="buttons" style="gap: 4px;">
          <label class="btn btn-outline-primary active btn-sm font-weight-bold flex-fill mb-1 mb-sm-0">
            <input type="radio" name="subCategoryFilter" value="ALL" checked onchange="filterMatrixSubCategory('ALL')"> ALL
          </label>
          
          <?php if (in_array('MALE', $availableSubCats)): ?>
          <label class="btn btn-outline-primary btn-sm font-weight-bold flex-fill mb-1 mb-sm-0">
            <input type="radio" name="subCategoryFilter" value="MALE" onchange="filterMatrixSubCategory('MALE')"> MALE
          </label>
          <?php endif; ?>

          <?php if (in_array('FEMALE', $availableSubCats)): ?>
          <label class="btn btn-outline-primary btn-sm font-weight-bold flex-fill mb-1 mb-sm-0">
            <input type="radio" name="subCategoryFilter" value="FEMALE" onchange="filterMatrixSubCategory('FEMALE')"> FEMALE
          </label>
          <?php endif; ?>

          <?php if (in_array('GROUP', $availableSubCats)): ?>
          <label class="btn btn-outline-primary btn-sm font-weight-bold flex-fill mb-1 mb-sm-0">
            <input type="radio" name="subCategoryFilter" value="GROUP" onchange="filterMatrixSubCategory('GROUP')"> GROUP
          </label>
          <?php endif; ?>

          <?php foreach ($availableSubCats as $sc): ?>
            <?php if (!in_array($sc, ['MALE', 'FEMALE', 'GROUP'])): ?>
            <label class="btn btn-outline-primary btn-sm font-weight-bold flex-fill mb-1 mb-sm-0">
              <input type="radio" name="subCategoryFilter" value="<?php echo htmlspecialchars($sc, ENT_QUOTES, 'UTF-8'); ?>" onchange="filterMatrixSubCategory('<?php echo htmlspecialchars($sc, ENT_QUOTES, 'UTF-8'); ?>')"> <?php echo htmlspecialchars($sc, ENT_QUOTES, 'UTF-8'); ?>
            </label>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- Form housing the matrix grid inputs -->
    <form id="masterMatrixForm">
      <input type="hidden" name="activeCategoryId" id="activeCategoryId" value="<?php echo $firstCatId; ?>">
      <input type="hidden" name="activeSubCategory" id="activeSubCategory" value="ALL">
      <div id="matrixTableContainer">
        <!-- Matrix table content loads here via AJAX -->
      </div>
    </form>
  </div>
</div>

<script>
  $(document).ready(function() {
    if ('<?php echo $firstCatId; ?>') {
      loadMatrixContent('<?php echo $firstCatId; ?>', 'ALL');
    }
  });

  function switchMatrixCategory(element, catId) {
    $('#matrixCategoryTabs .tab-switch-link').removeClass('active');
    $(element).addClass('active');
    $('#activeCategoryId').val(catId);
    var activeSubCat = $('input[name="subCategoryFilter"]:checked').val() || 'ALL';
    loadMatrixContent(catId, activeSubCat);
  }

  function filterMatrixSubCategory(subCat) {
    $('#activeSubCategory').val(subCat);
    var catId = $('#activeCategoryId').val();
    loadMatrixContent(catId, subCat);
  }

  function loadMatrixContent(catId, subCat = 'ALL') {
    $('#matrixTableContainer').html('<div class="text-center py-5"><i class="fa fa-spinner fa-spin fa-3x text-primary"></i><p class="mt-2 font-weight-bold">Loading criteria grid...</p></div>');

    $.ajax({
      url: 'tabulate/matrix_content.php',
      type: 'POST',
      data: { categoryId: catId, subCategory: subCat },
      success: function(response) {
        $('#matrixTableContainer').html(response);
      },
      error: function() {
        $.notify('Failed to load tabulation sheet grid.', { className: 'error', globalPosition: 'top right' });
      }
    });
  }
</script>

<?php $conn->close(); ?>