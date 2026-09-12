<!-- Customized Form Content -->

<input name="myId" type="number" hidden>
		          	
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
	</select>

</div>

<div class="form-group">
    <label>Criteria Name</label>
    <input class="form-control validate" type="text" name="criteriaName" title="Criteria name" 
    		placeholder="Enter criteria name . . ." maxlength="50">
</div>

<div class="form-group">
	<label>Criteria Description (Optional)</label>
    <input class="form-control" type="text" name="criteriaDescrp" 
    		placeholder="Enter criteria description . . .">
</div>

<div class="form-group validate">
	<label>Criteria Points</label>
    <input class="form-control validate" type="number" name="criteriaPoints" title="Criteria points" 
    		min="1" placeholder="Enter criteria points . . ." 
    		onkeyup="handleChange(this);" onchange="handleChange(this);" 
    		onkeypress="return isNumberKey(event);">
</div>


<script>
    $('#formModalLabel').html('Score Criteria Form');
</script>