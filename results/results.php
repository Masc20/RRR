
<!-- Breadcrumbs-->
<ol class="breadcrumb">
	<li class="breadcrumb-item">
	  <a href="">Dashboard</a>
	</li>
	<li class="breadcrumb-item active">Results</li>
</ol>

<button class="pull-right btn btn-primary" data-toggle="modal" data-target="#formModal" 
    	style="margin-right: 10px;" id="btnRefresh" 
    	onclick="$('#btnRefresh').attr('disabled', true);
    			$('#records').load('results/view.php', 
    							function (data, status) {
												    	
							    	$('#btnRefresh').attr('disabled', false);

						    		if (status != 'success') {

						    			$.notify('Request Failed ! Please Check your Connection and Try Again', {

											className: 'error',
											globalPosition: 'bottom right',
											autoHideDelay: 5000
			                            });
						    		}

								});">

  <i class="fa fa-refresh"></i> Refresh
</button>
<br>

<div id="records"></div>

<script>

	$('#records').load('results/view.php');

	<?php include '../config/config.php'; ?>

	function PrintElem(elem, labelTitle)
	{
	    var mywindow = window.open('', 'PRINT');

	    mywindow.document.write('<html><head><title>Report</title>');
	    
		mywindow.document.write('<style>');
		mywindow.document.write('table, td, th {border: 1px solid #ddd;text-align: left;}');
		mywindow.document.write('table {border-collapse: collapse;width: 100%;}');
		mywindow.document.write('th, td {padding: 15px;}');
		mywindow.document.write('</style>');

	    mywindow.document.write('</head><body >');
	    mywindow.document.write('<center>');
	    mywindow.document.write('<h1>');
	    mywindow.document.write("<img src='../images/header_logo.png' style='width: 36px; height: 36px;'>");
	    mywindow.document.write(' ACLC House Cup 2023-2024 - ACLC WEEK');
	    mywindow.document.write('</h1>');
	    mywindow.document.write('</center>');

	    mywindow.document.write(document.getElementById(elem).innerHTML);

	    mywindow.document.write('</body></html>');

	    mywindow.document.close(); // necessary for IE >= 10
	    mywindow.focus(); // necessary for IE >= 10*/

	    mywindow.print();
	    mywindow.close();

	    return true;
	}

</script>

