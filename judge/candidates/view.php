<?php

  session_start();
  $judgeId = $_SESSION['userId'];


  include '../../connection/conn.php';

  $sql = "SELECT * FROM tbl_config";

  $resultConfig = $conn->query($sql);
  $row = $resultConfig->fetch_assoc();

  $type = $row['based_type'];
  $houseGroups = array();
  $houseTotal = 0;

  if ($type == 'House') {
    $houseResult = $conn->query("SELECT cand_name, COUNT(*) AS house_count
                                 FROM tbl_candidates
                                 WHERE status = 'Allow'
                                 GROUP BY cand_name
                                 ORDER BY cand_name ASC");

    while ($houseRow = $houseResult->fetch_assoc()) {
      $houseGroups[] = $houseRow;
      $houseTotal += (int) $houseRow['house_count'];
    }
  }

?>

<div class="judge-workspace">
  <aside class="judge-rail" aria-label="House and scoring filters">
    <p class="judge-rail-title">Categories</p>
    <div class="judge-filter-list judge-house-list" role="group" aria-label="Filter contestants">
      <button type="button" class="judge-filter judge-house-filter active" data-house-filter="all">
        <span>All houses</span><span class="judge-filter-count"><?php echo $houseTotal; ?></span>
      </button>
      <?php foreach ($houseGroups as $houseGroup) { ?>
        <button type="button" class="judge-filter judge-house-filter" data-house-filter="<?php echo htmlspecialchars(strtolower($houseGroup['cand_name']), ENT_QUOTES, 'UTF-8'); ?>">
          <span><?php echo htmlspecialchars($houseGroup['cand_name'], ENT_QUOTES, 'UTF-8'); ?></span>
          <span class="judge-filter-count"><?php echo (int) $houseGroup['house_count']; ?></span>
        </button>
      <?php } ?>
    </div>

    <p class="judge-rail-title judge-status-title">Scoring status</p>
    <div class="judge-filter-list judge-status-list" role="group" aria-label="Filter by scoring status">
      <button type="button" class="judge-filter judge-status-filter active" data-status-filter="all">
        <span>All contestants</span><span class="judge-filter-count" id="judgeCountAll">0</span>
      </button>
      <button type="button" class="judge-filter judge-status-filter" data-status-filter="pending">
        <span>Needs scoring</span><span class="judge-filter-count" id="judgeCountPending">0</span>
      </button>
      <button type="button" class="judge-filter judge-status-filter" data-status-filter="scored">
        <span>Scored</span><span class="judge-filter-count" id="judgeCountScored">0</span>
      </button>
    </div>
    <div class="judge-progress">
      <div class="judge-progress-head">
        <span>Judging progress</span>
        <span class="judge-progress-value" id="judgeProgressValue">0 / 0</span>
      </div>
      <div class="judge-progress-track" aria-hidden="true">
        <div class="judge-progress-fill" id="judgeProgressFill" style="width: 0%"></div>
      </div>
      <p class="judge-progress-note">Contestants with submitted scores</p>
    </div>
  </aside>

  <section class="judge-board" aria-label="Contestants">
    <header class="judge-board-head">
      <div>
        <h1><?php echo htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?> scoring</h1>
        <p class="judge-board-subtitle">Open a contestant to score each active judging category.</p>
      </div>
      <div class="judge-board-tools">
        <input type="search" id="judgeCandidateSearch" class="judge-search" placeholder="Search contestants" aria-label="Search contestants">
        <span class="judge-result-count" id="judgeResultCount" aria-live="polite"></span>
      </div>
    </header>

    <div class="judge-candidate-grid" id="judgeCandidateGrid">

<?php  

  $sql = "SELECT * FROM tbl_candidates AS c 
          WHERE c.status = 'Allow'
          ORDER BY cand_no ASC";
  $resultCand = $conn->query($sql);

  
  if ($resultCand->num_rows > 0) {

    $sql = "SELECT c.* FROM tbl_category AS c, tbl_criteria AS p 
            WHERE c.status = 'Show' AND 
                  c.category_id = p.category_id 
            ORDER BY c.category_id ASC";
    $resultCategory = $conn->query($sql);

    // return number of category return
    $numCategory = $resultCategory->num_rows;


    // retrieve candidates
    while($row = $resultCand->fetch_assoc()) {
    
      $candId = $row['cand_id'];
      $candNo = $row['cand_no'];
      $candName = $row['cand_name'];
      $candPic = $row['cand_pic'];

      $sql = "SELECT DISTINCT s.* FROM tbl_scores AS s, tbl_category AS c 
              WHERE s.user_id = '$judgeId' AND 
                    s.cand_id = '$candId' AND 
                    s.category_id = c.category_id AND 
                    c.status = 'Show'";
      $resultScore = $conn->query($sql);

      // return number of scores return
      $numScores = $resultScore->num_rows;


      $updateOk = 0;
      // check if the candidate has been scored
      // if ($numCategory == $numScores && $numCategory != 0 && $numScores != 0) {
      //   $updateOk = 1;
      // }
      if ($numScores > 0 && $numCategory != 0 && $numScores != 0) {
        $updateOk = 1;
      }

      $label = "";
      // check the type 
      if ($type == 'Candidate') {
        
        $label = $type." No. ".$candNo;
      } else {
        $label = $type." of ".$candName;
      }

?>


    <article class="judge-candidate <?php echo $updateOk == 1 ? 'is-scored' : 'is-pending'; ?>"
             data-score-state="<?php echo $updateOk == 1 ? 'scored' : 'pending'; ?>"
         data-house="<?php echo $type == 'House' ? htmlspecialchars(strtolower($candName), ENT_QUOTES, 'UTF-8') : ''; ?>"
             data-search="<?php echo htmlspecialchars(strtolower($candName . ' ' . $candNo), ENT_QUOTES, 'UTF-8'); ?>">
      <div class="judge-candidate-photo">
        <img src="../uploads/<?php echo rawurlencode($candPic); ?>" class="myImg"
             alt="<?php echo htmlspecialchars($type . ' ' . $candNo . ' - ' . $candName, ENT_QUOTES, 'UTF-8'); ?>"
             loading="lazy">
        <span class="judge-candidate-number"><?php echo htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?> #<?php echo htmlspecialchars($candNo, ENT_QUOTES, 'UTF-8'); ?></span>
        <span class="judge-candidate-status"><?php echo $updateOk == 1 ? 'Scored' : 'To score'; ?></span>
      </div>
      <div class="judge-candidate-info">
        <h2><?php echo htmlspecialchars($candName, ENT_QUOTES, 'UTF-8'); ?></h2>
        <p><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></p>
        <button type="button" class="judge-score-button" data-toggle="modal" data-target="#formModal"
                data-cand-id="<?php echo htmlspecialchars($candId, ENT_QUOTES, 'UTF-8'); ?>"
                data-cand-label="<?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>">
          <i class="fa <?php echo $updateOk == 1 ? 'fa-pencil' : 'fa-star'; ?>" aria-hidden="true"></i>
          <span><?php echo $updateOk == 1 ? 'Review scores' : 'Score contestant'; ?></span>
        </button>
      </div>
    </article>

<?php

    }
  }

$conn->close();

?>

    </div>
    <div class="judge-empty-state" id="judgeEmptyState">No contestants match this filter.</div>
  </section>
</div>

<script>
  
  function openTabs(lbl, cid) {

    $('#formModalLabel').html(null);
    $('#tot').html(null);
    $('#btnSave').html('Submit');
    
    $('#formContent').load('candidates/nav_tabs.php',function(data, status) {

      if (status == 'success') {

        $('#formModalLabel').html(lbl);
        $('input[name=candId]').val(cid);

      } else {

        $.notify('Request Failed ! Please Check your Connection and Try Again', {

          className: 'error',
          globalPosition: 'top right',
          autoHideDelay: 5000
        });
      }

    });
  }

  function refreshJudgeCandidates(animateCards) {
    var cards = $('.judge-candidate');
    var scored = cards.filter('[data-score-state="scored"]').length;
    var pending = cards.length - scored;
    var activeHouse = $('.judge-house-filter.active').data('house-filter') || 'all';
    var activeStatus = $('.judge-status-filter.active').data('status-filter') || 'all';
    var searchTerm = ($('#judgeCandidateSearch').val() || '').toLowerCase().trim();
    var visibleCount = 0;

    $('#judgeCountAll').text(cards.length);
    $('#judgeCountPending').text(pending);
    $('#judgeCountScored').text(scored);
    $('#judgeProgressValue').text(scored + ' / ' + cards.length);
    $('#judgeProgressFill').css('width', (cards.length ? scored / cards.length * 100 : 0) + '%');

    cards.each(function() {
      var card = $(this);
      var houseMatches = activeHouse === 'all' || card.data('house') === activeHouse;
      var stateMatches = activeStatus === 'all' || card.data('score-state') === activeStatus;
      var searchMatches = !searchTerm || card.data('search').indexOf(searchTerm) !== -1;
      var visible = houseMatches && stateMatches && searchMatches;
      card.toggle(visible);
      if (visible) {
        visibleCount++;
        if (animateCards) {
          card.removeClass('is-entering');
          void card[0].offsetWidth;
          card.addClass('is-entering');
        }
      }
    });

    $('#judgeResultCount').text('Showing ' + visibleCount + ' of ' + cards.length);
    $('#judgeEmptyState').toggleClass('is-visible', visibleCount === 0);
  }

  $('.judge-house-filter').off('click.judge').on('click.judge', function() {
    $('.judge-house-filter').removeClass('active');
    $(this).addClass('active');
    refreshJudgeCandidates(true);
  });

  $('.judge-status-filter').off('click.judge').on('click.judge', function() {
    $('.judge-status-filter').removeClass('active');
    $(this).addClass('active');
    refreshJudgeCandidates(true);
  });

  $('#judgeCandidateSearch').off('input.judge').on('input.judge', refreshJudgeCandidates);

  $('.judge-score-button').off('click.judge').on('click.judge', function() {
    openTabs($(this).data('cand-label'), $(this).data('cand-id'));
  });

  refreshJudgeCandidates();

  // Get the modal
  var modal = document.getElementById('myModal');

  // Get the image and insert it inside the modal - use its "alt" text as a caption
  var img = $('.myImg');
  var modalImg = $("#img01");
  var captionText = document.getElementById("caption");
  $('.myImg').click(function(){
    modal.style.display = "block";
    var newSrc = this.src;
    modalImg.attr('src', newSrc);
    captionText.textContent = this.alt;
  });

  // Get the <span> element that closes the modal
  var span = document.getElementsByClassName("close")[0];

  // When the user clicks on <span> (x), close the modal
  span.onclick = function() {
    modal.style.display = "none";
  }

</script>
