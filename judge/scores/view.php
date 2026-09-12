
<?php include '../scores/nav_tabs.php'; ?>
  
<div class="tab-content">
  
<?php include '../scores/tab_pane.php'; ?>

</div>


<script>
  
  $('#myTab a:first').tab('show');

  $('.dataTable').DataTable( {
        "order": [[ 0, "asc" ]],
        "searching": false,
        "info": false,
        "paging": false
  } );
 
</script>
