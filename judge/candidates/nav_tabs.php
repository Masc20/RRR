
<!-- Nav tabs -->
<ul class="nav nav-tabs judge-category-tabs" role="tablist">

<?php
  
  include '../../connection/conn.php';

  $sql = "SELECT * FROM tbl_category AS c
          WHERE c.status = 'Show' AND LOWER(TRIM(c.category_name)) != 'total ranking'
          ORDER BY category_id ASC";
  $resultCategory = $conn->query($sql);

  if ($resultCategory->num_rows > 0) {

    $isFirst = true;
    $firstCategoryId = "";
    
    while($row = $resultCategory->fetch_assoc()) {

      $categoryId = $row['category_id'];
      $categoryName = $row['category_name'];

?>
      <li class="nav-item">
        <a class="nav-link judge-score-category-tab <?php 

                            if ($isFirst == true) {

                              $isFirst = false;
                              $firstCategoryId = $categoryId;

                              echo "active";
                            }
                          ?>" 
        href="#" onclick="return changeScoreCategory('<?php echo $categoryId; ?>', this);">
                    
          <?php echo $categoryName; ?>
        </a>
      </li>

  <?php

    }
  
  ?>

</ul>


<!-- Tab panes -->
<div class="tab-content">

  <div class="container"><br>

    <form id="myform">      
      <input type="number" name="candId" hidden>
      <div id="scoreform"></div>
    </form>

  </div>

</div>


<script>

  var scoreFormDirty = false;
  var scoreCategoryBusy = false;

  $(document).off('input.judgeScoreDirty', '#scoreform .slider')
    .on('input.judgeScoreDirty', '#scoreform .slider', function() {
      scoreFormDirty = true;
    });

  $(document).ready(function(){
    
    openScore($('input[name=candId]').val(), '<?php echo $firstCategoryId; ?>');
  });

  function openScore(cid, catid) {

    $('#scoreform').removeClass('judge-tab-in').addClass('judge-tab-switching');

    $('#scoreform').load('candidates/tab_content.php',{

      candId: cid,
      categoryId: catid

    }, function (data, status) {

      if (status == 'success') {

        getTotal();
        scoreFormDirty = false;
        $('#scoreform').removeClass('judge-tab-switching');
        void $('#scoreform')[0].offsetWidth;
        $('#scoreform').addClass('judge-tab-in');
        scoreCategoryBusy = false;
        $('.judge-score-category-tab').removeClass('disabled');
        $('#btnSave').prop('disabled', false);
        
      } else {

        scoreCategoryBusy = false;
        $('.judge-score-category-tab').removeClass('disabled');
        $('#btnSave').prop('disabled', false);

        $.notify('Request Failed ! Please Check your Connection and Try Again', {

          className: 'error',
          globalPosition: 'top right',
          autoHideDelay: 5000
        });
      }

    });

  }

  function changeScoreCategory(catid, tab) {
    if (scoreCategoryBusy) {
      return false;
    }

    var candidateId = $('input[name=candId]').val();
    var currentCategoryId = $('input[name=categoryId]').val();
    var loadSelectedCategory = function() {
      $('.judge-score-category-tab').removeClass('active');
      $(tab).addClass('active');
      scoreCategoryBusy = true;
      $('.judge-score-category-tab').addClass('disabled');
      $('#btnSave').prop('disabled', true);
      openScore(candidateId, catid);
    };

    if (currentCategoryId === catid) {
      return false;
    }

    if (!scoreFormDirty || !currentCategoryId) {
      loadSelectedCategory();
      return false;
    }

    scoreCategoryBusy = true;
    $('.judge-score-category-tab').addClass('disabled');
    $('#btnSave').prop('disabled', true);
    $.ajax({
      url: 'candidates/save.php',
      type: 'POST',
      data: $('#myform').serialize()
    }).done(function(response) {
      if (response.toLowerCase().indexOf('success') !== -1) {
        scoreFormDirty = false;
        loadSelectedCategory();
        return;
      }

      scoreCategoryBusy = false;
      $('.judge-score-category-tab').removeClass('disabled');
      $('#btnSave').prop('disabled', false);
      $.notify(response, {
        className: 'error',
        globalPosition: 'top right',
        autoHideDelay: 3000
      });
    }).fail(function() {
      scoreCategoryBusy = false;
      $('.judge-score-category-tab').removeClass('disabled');
      $('#btnSave').prop('disabled', false);
      $.notify('Could not save this category. Your scores are still available; please retry.', {
        className: 'error',
        globalPosition: 'top right',
        autoHideDelay: 3000
      });
    });

    return false;
  }

</script>

<?php

  }

  $conn->close();
?>





