function myFunctionX(button) {
	$('.w3-dropdown-content').hide();
    var parent = button.parentElement;
    var div = parent.getElementsByTagName('div')[1];
    $(div).slideToggle();
}

function _(id) {
	return document.getElementById(id);
}

function niceSelect(mode) {
	var formulate = function(id, width, values) {
		var content = '<p><div class="w3-dropdown-click" style="width:100% !important"><div onclick="myFunctionX(this)" class="w3-light-grey w3-border rounded w3-padding-large">'+values[0].innerHTML+' <i class="fa fa-caret-down"></i></div><div class="w3-dropdown-content w3-bar-block w3-animate-zoom rounded w3-border" style="z-index:12 !important;width:100%;" data="'+id+'">';
		for(var val of values){
			if ($(val).attr('value') != null) {
				var m_v = $(val).attr('value');
			}
			else{
				var m_v = val.innerHTML;
				$(val).attr('value', m_v);
			}
			content += '<a href="#" class="w3-bar-item w3-button drOption" data="'+$(val).attr('value')+'">'+val.innerHTML+'</a>';
		}
		content += '</div></div></div></p>';
		return content;
	}
	
	switch(mode){
		case 1:
			//do all
			var all_select = document.getElementsByTagName('select');
			var i = 1;
			for (var sel of all_select){
				var width = sel.clientWidth;
				$(sel).before('<div>').after(formulate(i, width, sel.getElementsByTagName('option'))).hide();
				$(sel).attr('data', "sel"+i);
				i += 1;
			}
			break;
		default:
			//do selected
			break;
	}

	$(document).on('click', '.w3-dropdown-click', function(event) {
		event.stopPropagation();
	})

	$(document).on('click', function(event) {
		$('.w3-dropdown-content').hide();
	});

	$(document).on('click', '.drOption', function(event) {
		var value = $(this).attr('data');
		var id = $(this.parentElement).attr('data');
		var placeholder = $('[data="sel'+id+'"] option').first().text();
		this.parentElement.parentElement.getElementsByTagName('div')[0].innerHTML = '<font class="w3-small w3-opacity">'+placeholder+'</font> <i class="fa fa-caret-down"></i><br>' + this.innerHTML;
		$('[data="sel'+id+'"]').val(value);
		$(this.parentElement).hide();
	})
}

$(document).ready(function() {
	niceSelect(1);
})