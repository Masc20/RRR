
<?php include '../results/nav_tabs.php'; ?>


<div class="tab-content">
  
<?php include '../results/tab_pane.php'; ?>
<?php include '../results/overall.php'; ?>
<?php include '../results/top_rank.php'; ?>

</div>

<script>

	$('#myTab a:first').tab('show');

	var table = $('.dataTable').DataTable({
	    "order": [[ 0, "asc" ]],
	    "searching": false,
	    "info": false,
	    "paging": false
	});

	$('a.toggle-vis').on( 'click', function (e) {
        e.preventDefault();
 
        // Get the column API object
        var column = table.column( $(this).attr('data-column') );
        // Toggle the visibility
        column.visible( ! column.visible() );
		
		if (column.visible()) {
			$(this).removeClass("btn-outline-success").addClass("btn-success");
		} else {
			$(this).removeClass("btn-success").addClass("btn-outline-success");
		}
    });
 
</script>
