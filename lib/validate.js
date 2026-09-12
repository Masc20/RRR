// validate empty fields
function validateIsEmpty() {

	var isError = false;

	var items = $(".validate");
	var itemCount = items.length;

	for(var i = 0; i < itemCount; i++)
	{
		if (items[i].value == "") {

			$('#'+items[i].name).addClass('border border-danger');
			
			$('input[name='+items[i].name+']').addClass('border border-danger')
											.notify(items[i].title+" is required !", {

				className: 'error',
				elementPosition: 'right',
				autoHideDelay: 2000
			});

			isError = true;
		} else {

			$('#'+items[i].name).removeClass('border-danger');
			$('input[name='+items[i].name+']').removeClass('border border-danger');
		}
	}

	return isError;
}
