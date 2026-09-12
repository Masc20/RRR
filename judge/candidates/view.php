<?php

  session_start();
  $judgeId = $_SESSION['userId'];


  include '../../connection/conn.php';

  $sql = "SELECT * FROM tbl_config";

  $resultConfig = $conn->query($sql);
  $row = $resultConfig->fetch_assoc();

  $type = $row['based_type'];

?>

<div class="row">

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


    <div class="col-xl-4 col-sm-6 mb-3">
      <div class="card">
        <div class="card-header <?php if ($updateOk == 1) echo('text-white bg-primary');?>">
           <h5><?php echo $type; ?> No <span class="badge badge-pill  <?php 
                                                        if ($updateOk == 1) {
                                                          echo('badge-light');
                                                        } else {
                                                          echo('badge-primary');
                                                        }
                                                      ?>">
                                        <?php echo $candNo; ?>
                                      </span></h5>
        </div>
        <div class="card-body">

          <div class="row">
            <div class="col">
              <img src="../uploads/<?php echo $candPic; ?>" width="120" 
                class="img-rounded img-thumbnail img-responsive myImg" 
                alt="<h5>Candidate No. <?php echo $candNo; ?> <br> <?php echo $candName; ?></h5>">
            </div>
            <div class="col">
              <label><?php echo $candName; ?></label>
              <br>
              <button class="btn  <?php 
                                    if ($updateOk == 1) {
                                      echo('btn-primary');
                                    } else {
                                      echo('btn-outline-primary');
                                    }
                                  ?>" 
                      data-toggle="modal" data-target="#formModal"
                      onclick="
                              openTabs('<?php echo $label; ?>', 
                                        '<?php echo $candId; ?>');
                              ">
                <?php 
                  if ($updateOk == 1) {
                    echo("Scored <i class='fa fa-check'></i>");
                  } else {
                    echo("Score <i class='fa fa-star'></i>");
                  }
                ?>
                
              </button>
            </div>
          </div>
          
        </div> 
      </div>
    </div>

<?php

    }
  }

$conn->close();

?>

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
    captionText.innerHTML = this.alt;
  });

  // Get the <span> element that closes the modal
  var span = document.getElementsByClassName("close")[0];

  // When the user clicks on <span> (x), close the modal
  span.onclick = function() {
    modal.style.display = "none";
  }

</script>
