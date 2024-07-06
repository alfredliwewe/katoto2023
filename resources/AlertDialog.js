function AlertDialog(obj) {
	var title, text, input, okayText, cancelText, icon, inputValue;
	title = obj.title
	text = obj.text
	input = obj.input
	okayText = obj.okayText
	cancelText = obj.cancelText
	icon = obj.icon
	inputValue = obj.inputValue

	var html = "<div class='w3-modal' style='display:block' onclick=\"$('#reusable').html('');\">"+
		"<div class='w3-modal-content shadow w3-padding-large w3-round-xlarge w3-animate-zoom' style='width:370px' onclick='event.stopPropagation();'>"+
			"<br><font class='w3-xxlarge'>"+title+"</font>"+
			"<p style='margin-top:20px;'><font class='w3-large'>"+text+"</font></p>";

			switch(input){
				case 'select':
					html += "<select class='form-control alertInput'>";
					var i = 0;
					for(var opt of obj.options[0]){
						html += '<option value="'+obj.options[1][i]+'">'+opt+'</option>';
						i += 1;
					}
					html += "</select>";
					break;

				case 'checkbox':
					var i = 0;
					for(var opt of obj.options[0]){
						html += '<input type="checkbox" class="alertCheck" name="'+obj.options[1][i]+'" id="for'+obj.options[1][i]+'"> <label for="for'+obj.options[1][i]+'">'+opt+'</label><br>';
						i += 1;
					}
					break;

				case 'file':
					html += '<input type="file" id="alertFileInput">';
					break;

				case 'number':
					html += '<input type="number" class="form-control alertInput">';
					break;

				case 'date':
					if(obj.value != null && obj.value != undefined){
						html += '<input type="date" value="'+obj.value+'" class="form-control alertInput">';
					}
					else{
						html += '<input type="date" class="form-control alertInput">';
					}
					break;

				case 'text':
					if(obj.inputValue != null && obj.inputValue != undefined){
						html += '<input type="text" value="'+obj.inputValue+'" class="form-control alertInput">';
					}
					else{
						html += '<input type="text" class="form-control alertInput">';
					}
					break;
			}
			html += "<div class='clearfix w3-padding-top w3-padding-bottom w3-marging-top' style='margin-top:20px;'><button class='btn bg-dark w3-text-white' id='alertPositiveButton'>Okay</button> <button class='btn btn-danger float-right' onclick=\"$('#reusable').html('');\">Cancel</button></div>"+
		"</div>"+
	"</div>";
	$('#reusable').html(html);

	var button = document.getElementById('alertPositiveButton');
	button.onclick = function(event) {
		switch(input){
			case 'select':
				var res = document.getElementsByClassName('alertInput')[0].value;
				$('#reusable').html('');
				return obj.success(res);
				break;

			case 'checkbox':
				var select = [];
				var all = document.getElementsByClassName('alertCheck');
				for(var elem of all){
					if (elem.checked) {
						select.push($(elem).attr('name'));
					}
				}
				$('#reusable').html('');
				return obj.success(select.toString());
				break;

			case 'number':
				var res = document.getElementsByClassName('alertInput')[0].value;
				$('#reusable').html('');
				return obj.success(res);
				break;

			case 'date':
				var res = document.getElementsByClassName('alertInput')[0].value;
				$('#reusable').html('');
				return obj.success(res);
				break;

			case 'text':
				var res = document.getElementsByClassName('alertInput')[0].value;
				$('#reusable').html('');
				return obj.success(res);
				break;

			case 'file':
				var res = document.getElementById('alertFileInput').files[0];
				$('#reusable').html('');
				return obj.success(res);
				break;

			default:
				$('#reusable').html('');
				return obj.success("");
				break;
		}
	};
}