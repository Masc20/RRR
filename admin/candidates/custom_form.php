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
              onkeyup="handleChange(this);" onchange="handleChange(this);" >
    </div>
    <div class="form-group">
        <label><?php echo $row['based_type']; ?> Name</label>
        <select class="form-control validate" name="candName" title="<?php echo $row['based_type']; ?> name">
            <option value="" disabled selected>Select <?php echo $row['based_type']; ?> name . . .</option>
            <option value="AZUL">AZUL</option>
            <option value="ROXXO">ROXXO</option>
            <option value="VIERRDY">VIERRDY</option>
            <option value="GIALLO">GIALLO</option>
            <option value="CAHEL">CAHEL</option>
        </select>
    </div>

    <div class="form-group">
        <label>Category</label>
        <select class="form-control validate" name="candCategory" title="Category">
            <option value="" disabled selected>Select Category . . .</option>
            <option value="NONE">NONE</option>
            <option value="FEMALE">FEMALE</option>
            <option value="MALE">MALE</option>
            <option value="GROUP">GROUP</option>
        </select>
    </div>

  </div>
</div>

<script>
  $('#formModalLabel').html('Candidates Form');
</script>