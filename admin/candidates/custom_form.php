<!-- Customized Form Content -->

<?php include '../config/config.php'; ?>

<div class="form-row">
  <div class="col-md-6">
  	
  	<div class="form-group">
      	<center>
      		<img  src="../uploads/default-user.png" class="img-rounded img-thumbnail img-responsive" 
      			width="200" height="200" name="pic">
          <div style="width: 200px;">
          	
          	<label for="files" class="btn btn-success btn-block">Choose Picture</label>
            <input id = "files" name="fileToUpload" type="file" accept="image/*" style="display: none" 
                  onchange="preview_pic(this);">
          </div>
      	</center>			              	
  	</div>

  </div>
  <div class="col-md-6">
  
	<input name="myId" type="number" hidden>

    <div class="form-group">
        <label><?php echo $row['based_type']; ?> No.</label>
        <input class="form-control validate" type="number" name="candNo" 
              title = "<?php echo $row['based_type']; ?> no." min = "1" 
        		  placeholder="Enter <?php echo $row['based_type']; ?> no . . ." 
              onkeyup="handleChange(this);" onchange="handleChange(this);" 
              onkeypress="return isNumberKey(event);">
    </div>
    <div class="form-group">
    	<label><?php echo $row['based_type']; ?> Name</label>
        <input class="form-control validate" type="text" name="candName" title = "<?php echo $row['based_type']; ?> name"
        		placeholder="Enter <?php echo $row['based_type']; ?> name . . ." maxlength="50">
    </div>

  </div>
</div>

<script>
  $('#formModalLabel').html('Candidates Form');
</script>