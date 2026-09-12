<!-- Customized Form Content -->

<input name="myId" type="number" hidden>

<div class="form-group">	
    <label>Judge name</label>
    <input class="form-control validate" type="text" name="fullName" title="Judge Name" 
    		placeholder="Enter judge name . . ." maxlength="50">
</div>

<div class="form-group">
	<label>Username</label>
    <input class="form-control validate" type="text" name="usn" title="Username" 
    		placeholder="Enter username . . .">
</div>

<div class="form-group">
	<label>Password</label>
    <input class="form-control validate" type="text" name="psw" title="Password" 
    		placeholder="Enter password . . .">
</div>
    
<div class="form-group">
    <label>Role</label>
    <select class="form-control" name="userType">
        <option value="Judge">Judge</option>
        <option value="Chairman">Chairman</option>
    </select>
</div>

<script>
    $('#formModalLabel').html('Judges Account Form');
</script>