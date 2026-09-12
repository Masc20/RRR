<!-- Customized Form Content -->

<input name="myId" type="number" hidden>
		          	
<div class="form-group">
    <label>Category Name</label>
    <input class="form-control validate" type="text" name="categoryName" title="Category name" 
    		placeholder="Enter category name . . ." maxlength="50">
</div>

<div class="form-group">
    <label>Percentage</label>
    <input class="form-control validate" type="number" name="percent" title="Percentage" 
    		placeholder="Enter percentage . . ." min="1" 
    		onkeyup="handleChange(this);" onchange="handleChange(this);" 
    		onkeypress="return isNumberKey(event);">
</div>

<script>
    $('#formModalLabel').html('Category Form');
</script>