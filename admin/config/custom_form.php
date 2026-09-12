<?php include '../config/config.php'; ?>

<div class="form-group">
  <label>Event Title</label>
  <input class="form-control validate" type="text" name="title" title="Event title"
      placeholder="Enter event title . . ." maxlength="50" value="<?php echo $row['event_title'];?>">
</div>

<div class="form-group">
  <label>Based Type</label>
  <select class="form-control" name="type">
    <option value="Candidate" <?php if($row['based_type'] == 'Candidate') echo "selected"; ?>>Candidate</option>
    <option value="House" <?php if($row['based_type'] == 'House') echo "selected"; ?>>House</option>
  </select>
</div>

<script>
    $('#formModalLabel').html('Configuration');
</script>