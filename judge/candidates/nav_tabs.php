
<!-- Nav tabs -->
<ul class="nav nav-tabs nav-pills" role="tablist">

<?php
  
  include '../../connection/conn.php';

  $sql = "SELECT * FROM tbl_category AS c
          WHERE c.status = 'Show' 
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
        <a class="nav-link <?php 

                            if ($isFirst == true) {

                              $isFirst = false;
                              $firstCategoryId = $categoryId;

                              echo "active";
                            }
                          ?>" 
        data-toggle="tab" href="#" onclick = "
                                              openScore($('input[name=candId]').val(), '<?php echo $categoryId; ?>');
                                            ">
                    
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

  $(document).ready(function(){
    
    openScore($('input[name=candId]').val(), '<?php echo $firstCategoryId; ?>');
  });

  function openScore(cid, catid) {

    $('#scoreform').html(null);
    $('#tot').html(null);

    $('#scoreform').load('candidates/tab_content.php',{

      candId: cid,
      categoryId: catid

    }, function (data, status) {

      if (status == 'success') {

        getTotal();
        
      } else {

        $.notify('Request Failed ! Please Check your Connection and Try Again', {

          className: 'error',
          globalPosition: 'top right',
          autoHideDelay: 5000
        });
      }

    });

  }

</script>

<?php

  }

  $conn->close();
?>





