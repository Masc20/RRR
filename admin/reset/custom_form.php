<div class="row">

  <div class="col">
    <div class="form-group">

      <label>Category</label>
      <select class="form-control" name="categoryId">

    <?php
      
      include '../../connection/conn.php';

      $sql = "SELECT * FROM tbl_category";
      $result = $conn->query($sql);

      if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
    ?>
        
        <option value="<?php echo $row['category_id'];?>">
          <?php echo $row['category_name']; ?>
        </option>

    <?php
        }

      }

      $conn->close();

    ?>
        <option value="All">All</option>
      </select>

    </div>
  </div>

  <div class="col">
    <div class="form-group">
      <label>Admin Password</label>
      <input class="form-control validate" type="password" name="adminPass" title="Admin password" 
          placeholder="Enter admin password . . ." maxlength="50">
    </div>
  </div>

</div>

<script>
    $('#formModalLabel').html('Reset Scores');
</script>
