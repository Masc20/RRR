
// call function
function create(link) {
	
	$('img[name=pic]').attr('src', '../uploads/default-user.png');
	$('input').val(null).removeClass('border border-danger');
    $('#btnSave').html('Submit')
    			.attr('onclick', "ajax_createUpdate('" + link + "', true);");
}

function retrieve() {

	for (var i = 0, j = arguments.length; i < j; i++) {

		$("input[name=" + document.getElementById('myform').elements[i].name + "]").val(arguments[i]);
		$("select[name=" + document.getElementById('myform').elements[i].name + "]").val(arguments[i]);
    }
}

function update(link, isReload) {

	$('input').removeClass('border border-danger');
	$('#btnSave').html('Update')
				.attr('onclick', "ajax_createUpdate('" + link + "', " + isReload + ");");
}

function drop(link, id, name) {
	$('#nameLabel').html(name);
	$('#btnYes').attr('onclick', "ajax_drop('" + link + "'," + id + ");");
}

// insert and update ajax function
function ajax_createUpdate(link, isReload) {
	
	$isEmpty = validateIsEmpty();

	if ($isEmpty == false) {

		$btnDefaultLabel = $('#btnSave').html();

		$('#btnSave').attr('disabled', true)
					.html("<i class='fa fa-fw fa-refresh fa-spin'></i> Saving");

		formData = new FormData($('#myform')[0]);
		formData.append('pic', $('img[name=pic]').attr("src"));

		$.ajax({

	        url: link + '/save.php', 
	        type: 'POST',
	        data: formData,
	        processData: false,
	        contentType:false

	    }).done(function(data){
	    
	    	$('#btnSave').attr('disabled', false).html($btnDefaultLabel);

			if (data.toLowerCase().indexOf("success") >= 0) {			

				// $.notify(data, {

				// 	className: 'success',
				// 	globalPosition: 'top right',
				// 	autoHideDelay: 2000
				// });

				$('#formModal').modal('hide');

				if (isReload == true) {
					$('#records').load(link + '/view.php');
				} else {
					location.reload();
				}
				
			} else {

				$.notify(data, {

					className: 'error',
					globalPosition: 'top right',
					autoHideDelay: 2000
				});

				$('.formDialog').animateCss('headShake');
		    }

	    }).fail(function(){

	    	$('#btnSave').attr('disabled', false).html(btnDefaultLabel);

			$.notify('Request Failed ! Please Check your Connection and Try Again', {

				className: 'error',
				globalPosition: 'top right',
				autoHideDelay: 3000
			});

	    });
	    
	} else {
		$('.formDialog').animateCss('headShake');
	}
}

// delete ajax function
function ajax_drop(link, id) {
	
	$('#btnYes').attr('disabled', true);

	$.post(link + '/delete.php',
    {
      idel: id
    },
    function(data, status){

    	$('#btnYes').attr('disabled', false);
    	
        if (data.toLowerCase().indexOf("success") >= 0) {			

			$.notify(data, {

				className: 'success',
				globalPosition: 'bottom right',
				autoHideDelay: 5000
			});
			
			$('#delModal').modal('hide');
			$('#records').load(link + '/view.php');

		} else {

			$('.formDialog').notify(data, {

				className: 'error',
				elementPosition: 'bottom right',
				autoHideDelay: 2000
			});

			$('.formDialog').animateCss('headShake');
	    }

    }).fail(function () {
    	
    	$('#btnYes').attr('disabled', false);

    	$.notify('Request Failed ! Please Check your Connection and Try Again', {

			className: 'error',
			globalPosition: 'top right',
			autoHideDelay: 3000
		});
    });

}

function updateStatus(link, e) {

	$(e).attr('disabled', true);

	$.post(link + '/save.php',
	{
		id: $(e).attr("name"),
		status: $(e).attr("value")
	},
	function(data, status){

		$(e).attr('disabled', false);
		
		if (data.toLowerCase().indexOf("success") >= 0) {	

			$('#records').load(link + '/view.php');

			$.notify(data, {

			  className: 'success',
			  globalPosition: 'bottom right',
			  autoHideDelay: 3000
			});

		} else {

			$.notify(data, {

			  className: 'error',
			  globalPosition: 'bottom right',
			  autoHideDelay: 3000
			});
		}

	}).fail(function () {
		
		$(e).attr('disabled', false);

		$.notify('Request Failed ! Please Check your Connection and Try Again', {

			className: 'error',
			globalPosition: 'top right',
			autoHideDelay: 3000
		});
	});

}