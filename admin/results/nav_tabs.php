<!-- Nav tabs -->
<ul class="nav nav-tabs nav-pills" role="tablist" id="myTab">

<?php
  
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

    <li class="nav-item">
      <a class="nav-link" 
        data-toggle="tab" href="#menu<?php echo $categoryId;?>">
        <?php echo $categoryName;?>
      </a>
    </li>

    <?php
    }
  }

  $conn->close();

?>

<li class="nav-item">
    <a class="nav-link" data-toggle="tab" href="#menuTop">
    Overall Average
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-toggle="tab" href="#menuOverall">
      Overall Result
    </a>
  </li>



</ul>