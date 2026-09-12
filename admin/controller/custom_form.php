<?php include '../config/config.php'; ?>

<h6 class="dropdown-header" style="font-size: 16px;">Controller</h6>
<div class="dropdown-divider"></div>

<div class="form-group" style="padding: 5px 20px 5px 20px;">  
    <label>Prompt Message</label>
    <input class="form-control" type="text" name="promptMsg" 
          placeholder="Enter prompt message . . ." maxlength="100" value="<?php echo $row['prompt_msg']; ?>">
</div>

<div class="dropdown-divider"></div>

<div class="form-group" style="padding: 5px 20px 5px 20px;">
  <div class="row">
    <div class="col">
      <label>Chairman</label>
      <button class="btn btn-block 
                    <?php
                      if ($row['chairman_status'] == 'Open') {

                        echo "btn-success";
                      } else {
                        echo "btn-outline-success";
                      }
                    ?>" 
            name="chairStatus" value="<?php echo $row['chairman_status']; ?>" 
            onclick="changeStatus(this);">
            <?php
              if ($row['chairman_status'] == 'Open') {

                echo "<i class='fa fa-fw fa-toggle-on'></i> Opened";
              } else {
                echo "<i class='fa fa-fw fa-toggle-off'></i> Closed";
              }
            ?>
      </button>
    </div>
    <div class="col">
      <label>Judges</label>
      <button class="btn btn-block 
                      <?php
                      if ($row['judge_status'] == 'Open') {

                        echo "btn-success";
                      } else {
                        echo "btn-outline-success";
                      }
                    ?>" 
            name="judgeStatus" value="<?php echo $row['judge_status']; ?>" 
            onclick="changeStatus(this);">
            <?php
              if ($row['judge_status'] == 'Open') {

                echo "<i class='fa fa-fw fa-toggle-on'></i> Opened";
              } else {
                echo "<i class='fa fa-fw fa-toggle-off'></i> Closed";
              }
            ?>
      </button>
    </div>
  </div>
</div>

<div class="dropdown-divider"></div>

<div class="form-group" style="padding: 5px 20px 0px 20px;">  

  <button class="btn btn-primary btn-block" onclick="ajax_controller(this);">
    <i class="fa fa-save"></i> Update
  </button>
</div>

<script>

function changeStatus(e) {

  if ($(e).val() == 'Open') {

    $(e).val('Close')
        .removeClass('btn-success')
        .addClass('btn-outline-success')
        .html("<i class='fa fa-fw fa-toggle-off'></i> Closed");

  } else {

    $(e).val('Open')
        .removeClass('btn-outline-success')
        .addClass('btn-success')
        .html("<i class='fa fa-fw fa-toggle-on'></i> Opened");
  }
  
}

function ajax_controller(e) {
  
  $(e).attr('disabled', true);

  $.post('controller/save.php',
  {
    msg: $('input[name=promptMsg]').val(),
    chair: $('button[name=chairStatus]').val(),
    judge: $('button[name=judgeStatus]').val()
  },
  function(data, status){

    $(e).attr('disabled', false);
    
    $.notify(data, {

      className: 'success',
      globalPosition: 'bottom right',
      autoHideDelay: 3000
    });

  });
}

</script>