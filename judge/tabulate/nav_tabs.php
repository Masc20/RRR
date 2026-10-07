<?php
  include '../../connection/conn.php';

  $sql = "SELECT * FROM tbl_category AS c
          WHERE c.status = 'Show' AND LOWER(TRIM(c.category_name)) != 'total ranking'
          ORDER BY category_id ASC";
  $resultCategory = $conn->query($sql);
?>

<!-- Nav tabs for Categories -->
<ul class="nav nav-tabs judge-category-tabs" role="tablist">
<?php
  if ($resultCategory->num_rows > 0) {
    $isFirst = true;
    $firstCategoryId = "";
    
    while($row = $resultCategory->fetch_assoc()) {
      $categoryId = $row['category_id'];
      $categoryName = $row['category_name'];
?>
      <li class="nav-item">
        <a class="nav-link judge-score-category-tab <?php echo $isFirst ? 'active' : ''; ?>" 
           href="#" onclick="return changeScoreCategory('<?php echo $categoryId; ?>', this);">
          <?php echo htmlspecialchars($categoryName, ENT_QUOTES, 'UTF-8'); ?>
        </a>
      </li>
<?php
      if ($isFirst) {
        $isFirst = false;
        $firstCategoryId = $categoryId;
      }
    }
  }
?>
</ul>

<!-- Tab panes / Matrix Grid Container -->
<div class="tab-content">
  <div class="container-fluid mt-3">
    <form id="matrixScoreForm">      
      <div id="scoreMatrixContainer">
        <!-- The matrix table will be loaded here via AJAX -->
      </div>
    </form>
  </div>
</div>

<script>
  var scoreCategoryBusy = false;

  $(document).ready(function(){
    // Load the first category matrix by default
    loadCategoryMatrix('<?php echo $firstCategoryId; ?>');
  });

  function loadCategoryMatrix(catid) {
    $('#scoreMatrixContainer').html('<div class="text-center py-5"><i class="fa fa-spinner fa-spin fa-2x"></i><p class="mt-2">Loading tabulation sheet...</p></div>');
    
    $.ajax({
      url: 'candidates/matrix_content.php',
      type: 'POST',
      data: { categoryId: catid },
      success: function(data) {
        $('#scoreMatrixContainer').html(data);
        scoreCategoryBusy = false;
        $('.judge-score-category-tab').removeClass('disabled');
        $('#btnSave').prop('disabled', false);
        // Update global save button text or behavior if needed
        $('#btnSave').html('SAVE ALL SCORES');
      },
      error: function() {
        scoreCategoryBusy = false;
        $('.judge-score-category-tab').removeClass('disabled');
        $('#btnSave').prop('disabled', false);
        $.notify('Request Failed! Please Check your Connection and Try Again', {
          className: 'error',
          globalPosition: 'top right',
          autoHideDelay: 5000
        });
      }
    });
  }

  function changeScoreCategory(catid, tab) {
    if (scoreCategoryBusy) return false;

    var currentCategoryId = $('input[name=categoryId]').val();
    if (currentCategoryId === catid) return false;

    scoreCategoryBusy = true;
    $('.judge-score-category-tab').removeClass('active');
    $(tab).addClass('active');
    $('.judge-score-category-tab').addClass('disabled');
    $('#btnSave').prop('disabled', true);

    // Auto-save current matrix before switching tabs (optional, or just switch)
    loadCategoryMatrix(catid);
    return false;
  }
</script>

<?php
  $conn->close();
?>