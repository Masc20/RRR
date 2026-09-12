<!-- Nav tabs -->
<ul class="nav nav-tabs nav-pills" role="tablist" id="myTab">

<?php
  
  include '../../connection/conn.php';
  
  $sql = "SELECT * FROM tbl_category 
          WHERE status = 'Show' 
          ORDER BY category_id ASC";
  $resultCategory = $conn->query($sql);

  if ($resultCategory->num_rows > 0) {

    while($row5 = $resultCategory->fetch_assoc()) {

      $categoryId = $row5['category_id'];
    ?>

    <li class="nav-item">
      <a class="nav-link" data-toggle="tab" href="#menu<?php echo $categoryId;?>">
        <?php echo $row5['category_name'];?>
      </a>
    </li>

    <?php
    }
  }

  $conn->close();

?>

</ul>