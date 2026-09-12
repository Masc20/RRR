
<div class="card-body" id="records">

</div>

<?php include '../modal/form_modal.php'; ?>
<?php include '../modal/light_box_modal.php'; ?>

<script>

  $('#records').load('candidates/view.php');

  function handleChange(input, max, lbl) {
    if (input.value < 0 && input.value != "") input.value = 1;
    if (input.value > max) input.value = max;

    $("#labelPoints" + lbl).html(input.value)
    getTotal();
  }

  function getTotal()
  {
    var items = document.getElementsByClassName("items");
    var itemCount = items.length;
    var total = 0;

    for(var i = 0; i < itemCount; i++)
    {
      total += parseFloat(items[i].value) || 0;
    }

    $('#tot').html(total);
  }

</script>