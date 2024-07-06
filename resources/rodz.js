var LinearLayout = {
	VERTICAL:"vertical",
	HORIZONTAL:"horizontal"
}

var Size = {
	SM:"sm",
	SMALL:"sm",
	LG:"lg",
	LARGE:"lg"
}

var Variant = {
	TXT:"text",
	TEXT:"text",
	OUTLINED:"outlined",
	CONTAINED:"contained"
}

//attach table script to the page
var tableLink = document.createElement("script");
tableLink.src = "http://localhost/resources/table.js";
document.head.appendChild(tableLink);

const sleep = time => new Promise(resolve => setTimeout(resolve, time));

$(document).on('focus', '.form-control.bb', function(event) {
	var elem = this;
	if ($(elem).hasClass('rodzInput')) {
		//do nothing...
	}
	else{
		//show the shit
		var font = document.createElement("font");
		$(font).addClass('inputLabel');
		font.style.top = (elem.offsetTop+5)+"px";
		font.style.left = (elem.offsetLeft+5)+"px";
		font.innerHTML = $(elem).attr('placeholder');
		$(elem).addClass('rodzInput');
		document.body.appendChild(font);

		$(elem).attr('dataR', $(elem).attr('placeholder')).attr('placeholder', '');
	}
})

$(document).on('blur', '.form-control.bb', function(event) {
	var elem = this;
	
	$(elem).removeClass('rodzInput');
	$(elem).attr('placeholder', $(elem).attr('dataR'))
	$('.inputLabel').remove();
})

$(document).on('focus', '.form-control.ss', function(event) {
	var elem = this;
	if ($(elem).hasClass('rodzInputs')) {
		//do nothing...
	}
	else{
		//show the shit
		var font = document.createElement("font");
		$(font).addClass('inputLabels');
		font.style.top = (elem.offsetTop-7)+"px";
		font.style.left = (elem.offsetLeft+8)+"px";
		font.innerHTML = $(elem).attr('placeholder');
		$(elem).addClass('rodzInputs');
		document.body.appendChild(font);

		$(elem).attr('dataR', $(elem).attr('placeholder')).attr('placeholder', '');
	}
})

$(document).on('blur', '.form-control.ss', function(event) {
	var elem = this;
	
	$(elem).removeClass('rodzInputs');
	$(elem).attr('placeholder', $(elem).attr('dataR'))
	$('.inputLabels').remove();
})

//the div class
	function Div(id) {
		if (id == undefined) {
			this.view = document.createElement("div");
		}
		else{
			this.view = document.getElementById(id);
		}
		this.orientation = "horizontal";

		this.setPadding = function(left, top, right, bottom) {
			this.view.style.padding = top+'px '+left+'px '+bottom+'px '+right+'px';
			this.view.style.paddingLeft = left+"px";
			return this;
		}

		this.renderIn = function(id) {
			document.getElementById(id).appendChild(this.view);
		}

		this.addClass = function(className) {
			$(this.view).addClass(className);
			return this;
		}

		this.addClasses = function(list) {
			for(var c of list){
				$(this.view).addClass(c);
			}
		}

		this.removeClass = function(className) {
			$(this.view).removeClass(className);
			return this;
		}

		this.toggleClass = function(className) {
			$(this.view).toggleClass(className);
			return this;
		}

		this.addAll = function() {
			for(var arg of arguments){
				this.addView(arg);
			}
		}

		this.hasClass = function(className) {
			var classes = this.getAttribute('class').split(" ");
			if (className in classes) {
				return true;
			}
			else{
				return false;
			}
		}

		this.addView = function(elem) {
			this.view.appendChild(elem.view);
			return this;
		}

		this.removeView = function(view1) {
			this.view.removeChild(view);
			return this;
		}

		this.removeAllViews = function() {
			this.view.innerHTML = '';
			return this;
		}

		this.setOrientation = function(orientation) {
			this.orientation = orientation;
			return this;
		}

		this.setHeight = function(height) {
			this.view.style.height = height+"px";
			return this;
		}

		this.setWidth = function(height) {
			this.view.style.width = height+"px";
			return this;
		}

		this.getAttribute = function(name) {
			return $(this.view).attr(name);
		}

		this.setAttribute = function(name, value) {
			$(this.view).attr(name, value);
			return this;
		}

		this.css = function(name, value) {
			$(this.view).css(name, value);
			return this;
		}
	}


	function Span(id) {
		if (id == undefined) {
			this.view = document.createElement("span");
		}
		else{
			this.view = document.getElementById(id);
		}
		this.orientation = "horizontal";

		this.setPadding = function(left, top, right, bottom) {
			this.view.style.padding = top+'px '+left+'px '+bottom+'px '+right+'px';
			this.view.style.paddingLeft = left+"px";
			return this;
		}

		this.renderIn = function(id) {
			document.getElementById(id).appendChild(this.view);
		}

		this.addClass = function(className) {
			$(this.view).addClass(className);
			return this;
		}

		this.addClasses = function(list) {
			for(var c of list){
				$(this.view).addClass(c);
			}
		}

		this.removeClass = function(className) {
			$(this.view).removeClass(className);
			return this;
		}

		this.toggleClass = function(className) {
			$(this.view).toggleClass(className);
			return this;
		}

		this.hasClass = function(className) {
			var classes = this.getAttribute('class').split(" ");
			if (className in classes) {
				return true;
			}
			else{
				return false;
			}
		}

		this.addView = function(elem) {
			if (elem.view.tagName != "TABLE") {
				if (this.orientation == "vertical") {
					elem.view.style.display = "block";
				}
				else{
					elem.view.style.display = "inline";
				}
			}
			this.view.appendChild(elem.view);
			return this;
		}

		this.removeView = function(view1) {
			this.view.removeChild(view);
			return this;
		}

		this.removeAllViews = function() {
			this.view.innerHTML = '';
			return this;
		}

		this.setOrientation = function(orientation) {
			this.orientation = orientation;
			return this;
		}

		this.setHeight = function(height) {
			this.view.style.height = height+"px";
			return this;
		}

		this.setWidth = function(height) {
			this.view.style.width = height+"px";
			return this;
		}

		this.getAttribute = function(name) {
			return $(this.view).attr(name);
		}

		this.setAttribute = function(name, value) {
			$(this.view).attr(name, value);
		}
	}


	function ImageView(id) {
		if (id == undefined) {
			this.view = document.createElement("img");
		}
		else{
			this.view = document.getElementById(id);
		}
		this.orientation = "horizontal";

		this.setPadding = function(left, top, right, bottom) {
			this.view.style.padding = top+'px '+left+'px '+bottom+'px '+right+'px';
			this.view.style.paddingLeft = left+"px";
			return this;
		}

		this.renderIn = function(id) {
			document.getElementById(id).appendChild(this.view);
		}

		this.addClass = function(className) {
			$(this.view).addClass(className);
			return this;
		}

		this.addClasses = function(list) {
			for(var c of list){
				$(this.view).addClass(c);
			}
		}

		this.removeClass = function(className) {
			$(this.view).removeClass(className);
			return this;
		}

		this.toggleClass = function(className) {
			$(this.view).toggleClass(className);
			return this;
		}

		this.hasClass = function(className) {
			var classes = this.getAttribute('class').split(" ");
			if (className in classes) {
				return true;
			}
			else{
				return false;
			}
		}

		this.setHeight = function(height) {
			this.view.style.height = height+"px";
			return this;
		}

		this.setWidth = function(height) {
			this.view.style.width = height+"px";
			return this;
		}

		this.getAttribute = function(name) {
			return $(this.view).attr(name);
		}

		this.setAttribute = function(name, value) {
			$(this.view).attr(name, value);
		}

		this.setSource = function(src){
			this.setAttribute('src', src);
			return this;
		}

		this.getSource = function(src){
			return this.getAttribute('src');
		}

		this.css = function(name, value) {
			$(this.view).css(name, value);
			return this;
		}
	}


	function CheckBox() {
		this.view = document.createElement("font");

		this.span = document.createElement("span");
		$(this.span).addClass('check');

		this.check = document.createElement("input");
		$(this.check).attr('type', 'checkbox');
		var id = Math.floor(Math.random() * 100000);
		$(this.check).attr('id', id);
		$(this.span).attr('for', id);

		this.label = document.createElement('label');
		$(this.label).attr('for', id);
		$(this.label).addClass('roboto')
		this.orientation = "horizontal";

		this.span.appendChild(this.check);

		this.view.appendChild(this.span);
		this.view.appendChild(this.label);

		this.setPadding = function(left, top, right, bottom) {
			this.view.style.padding = top+'px '+left+'px '+bottom+'px '+right+'px';
			this.view.style.paddingLeft = left+"px";
			return this;
		}

		this.renderIn = function(id) {
			document.getElementById(id).appendChild(this.view);
		}

		this.addClass = function(className) {
			$(this.view).addClass(className);
			return this;
		}

		this.addClasses = function(list) {
			for(var c of list){
				$(this.view).addClass(c);
			}
		}

		this.removeClass = function(className) {
			$(this.view).removeClass(className);
			return this;
		}

		this.toggleClass = function(className) {
			$(this.view).toggleClass(className);
			return this;
		}

		this.hasClass = function(className) {
			var classes = this.getAttribute('class').split(" ");
			if (className in classes) {
				return true;
			}
			else{
				return false;
			}
		}

		this.setHeight = function(height) {
			this.view.style.height = height+"px";
			return this;
		}

		this.setWidth = function(height) {
			this.view.style.width = height+"px";
			return this;
		}

		this.getAttribute = function(name) {
			return $(this.view).attr(name);
		}

		this.setAttribute = function(name, value) {
			$(this.view).attr(name, value);
		}

		this.setName = function(name) {
			$(this.check).attr('name', name);
		}

		this.setValue = function(value) {
			$(this.check).attr('value', value);
		}

		this.setText = function(text) {
			$(this.label).html("&nbsp;"+text);
		}

		this.getText = function() {
			return $(this.label).html();
		}

		this.isChecked = function() {
			if(this.check.checked){
				return true;
			}
			else{
				return false;
			}
		}

		this.setChecked = function(bool) {
			if (bool) {
				this.check.checked = 'checked'
			}
			else{
				this.check.checked = '';
			}
		}
	}


	function Form() {
		this.view = document.createElement("form");
		this.orientation = "horizontal";

		this.setPadding = function(left, top, right, bottom) {
			this.view.style.padding = top+'px '+left+'px '+bottom+'px '+right+'px';
			this.view.style.paddingLeft = left+"px";
			return this;
		}

		this.addClass = function(className) {
			$(this.view).addClass(className);
			return this;
		}

		this.renderIn = function(id) {
			document.getElementById(id).appendChild(this.view);
		}

		this.addClasses = function(list) {
			for(var c of list){
				$(this.view).addClass(c);
			}
		}

		this.removeClass = function(className) {
			$(this.view).removeClass(className);
			return this;
		}

		this.toggleClass = function(className) {
			$(this.view).toggleClass(className);
			return this;
		}

		this.hasClass = function(className) {
			var classes = this.getAttribute('class').split(" ");
			if (className in classes) {
				return true;
			}
			else{
				return false;
			}
		}

		this.addView = function(elem) {
			this.view.appendChild(elem.view);
			return this;
		}

		this.removeView = function(view1) {
			this.view.removeChild(view);
			return this;
		}

		this.removeAllViews = function() {
			this.view.innerHTML = '';
			return this;
		}

		this.setOrientation = function(orientation) {
			this.orientation = orientation;
			return this;
		}

		this.setHeight = function(height) {
			this.view.style.height = height+"px";
			return this;
		}

		this.setWidth = function(height) {
			this.view.style.width = height+"px";
			return this;
		}

		this.getAttribute = function(name) {
			return $(this.view).attr(name);
		}

		this.setAttribute = function(name, value) {
			$(this.view).attr(name, value);
		}

		this.addAll = function() {
			for(var arg of arguments){
				this.addView(arg);
			}
		}

		this.onSubmit = function(callback) {
			this.view.addEventListener('submit', function(event) {
				callback(event);
			})
		}
	}


	//the canvas class
	function Canvas() {
		this.view = document.createElement("canvas");

		this.setPadding = function(left, top, right, bottom) {
			this.view.style.paddingLeft = left+"px";
			this.view.style.paddingRight = right+"px";
			this.view.style.paddingTop = top+"px";
			this.view.style.paddingBottom = bottom+"px";
			return this;
		}

		this.addClass = function(className) {
			$(this.view).addClass(className);
			return this;
		}

		this.addClasses = function(list) {
			for(var c of list){
				$(this.view).addClass(c);
			}
		}

		this.removeClass = function(className) {
			$(this.view).removeClass(className);
			return this;
		}

		this.renderIn = function(id) {
			document.getElementById(id).appendChild(this.view);
		}

		this.toggleClass = function(className) {
			$(this.view).toggleClass(className);
			return this;
		}

		this.hasClass = function(className) {
			var classes = this.getAttribute('class').split(" ");
			if (className in classes) {
				return true;
			}
			else{
				return false;
			}
		}

		this.setHeight = function(height) {
			this.view.style.height = height+"px";
			return this;
		}

		this.setWidth = function(height) {
			this.view.style.width = height+"px";
			return this;
		}

		this.getAttribute = function(name) {
			return $(this.view).attr(name);
		}

		this.setAttribute = function(name, value) {
			$(this.view).attr(name, value);
		}
	}


	//the label class
	function Label() {
		this.view = document.createElement("font");

		this.setPadding = function(left, top, right, bottom) {
			this.view.style.padding = top+'px '+left+'px '+bottom+'px '+right+'px';
		}

		this.addClass = function(className) {
			$(this.view).addClass(className);
		}

		this.removeClass = function(className) {
			$(this.view).removeClass(className);
		}

		this.renderIn = function(id) {
			document.getElementById(id).appendChild(this.view);
		}

		this.toggleClass = function(className) {
			$(this.view).toggleClass(className);
		}

		this.addView = function(elem) {
			this.view.appendChild(elem.view);
		}

		this.removeView = function(view1) {
			this.view.removeChild(view);
		}

		this.setText = function(text) {
			this.view.innerHTML = text;
		}

		this.addView = function(elem) {
			this.view.appendChild(elem.view);
			return this;
		}

		this.getText = function(text) {
			return this.view.innerHTML = text;
		}

		this.addClasses = function(list) {
			for(var c of list){
				$(this.view).addClass(c);
			}
		}

		this.getAttribute = function(name) {
			return $(this.view).attr(name);
		}

		this.setAttribute = function(name, value) {
			$(this.view).attr(name, value);
		}

		this.addAll = function() {
			for(var arg of arguments){
				this.addView(arg);
			}
		}

		this.show = function() {
			this.view.style.display = 'inline';
		}

		this.hide = function(){
			this.view.style.display = 'none';
		}
	}


	function Button() {
		this.view = document.createElement("button");

		this.setPadding = function(left, top, right, bottom) {
			this.view.style.padding = top+'px '+left+'px '+bottom+'px '+right+'px';
			return this;
		}

		this.addClass = function(className) {
			$(this.view).addClass(className);
			return this;
		}

		this.removeClass = function(className) {
			$(this.view).removeClass(className);
			return this;
		}

		this.toggleClass = function(className) {
			$(this.view).toggleClass(className);
			return this;
		}

		this.addView = function(elem) {
			this.view.appendChild(elem.view);
			return this;
		}

		this.renderIn = function(id) {
			document.getElementById(id).appendChild(this.view);
		}

		this.removeView = function(view1) {
			this.view.removeChild(view);
			return this;
		}

		this.addClasses = function(list) {
			for(var c of list){
				$(this.view).addClass(c);
			}
		}

		this.getText = function(type) {
			return $(this.view).text();
		}

		this.setText = function(text) {
			$(this.view).html(text);
			return this;
		}

		this.getAttribute = function(name) {
			return $(this.view).attr(name);
		}

		this.setAttribute = function(name, value) {
			$(this.view).attr(name, value);
		}

		this.onClick = function(callback) {
			this.view.addEventListener('click', function(event){
				callback(event);
			})
		}

		this.on = function(e_name, callback) {
			this.view.addEventListener(e_name, function(event) {
				callback(event);
			})
		}
	}

	function MaterialButton() {
		this.view = document.createElement("button");

		this.setPadding = function(left, top, right, bottom) {
			this.view.style.padding = top+'px '+left+'px '+bottom+'px '+right+'px';
			return this;
		}

		this.addClass = function(className) {
			$(this.view).addClass(className);
			return this;
		}

		this.removeClass = function(className) {
			$(this.view).removeClass(className);
			return this;
		}

		this.toggleClass = function(className) {
			$(this.view).toggleClass(className);
			return this;
		}

		this.addView = function(elem) {
			this.view.appendChild(elem.view);
			return this;
		}

		this.renderIn = function(id) {
			document.getElementById(id).appendChild(this.view);
		}

		this.removeView = function(view1) {
			this.view.removeChild(view);
			return this;
		}

		this.addClasses = function(list) {
			for(var c of list){
				$(this.view).addClass(c);
			}
		}

		this.getText = function(type) {
			return $(this.view).text();
		}

		this.setText = function(text) {
			$(this.view).html(text);
			return this;
		}

		this.getAttribute = function(name) {
			return $(this.view).attr(name);
		}

		this.setAttribute = function(name, value) {
			$(this.view).attr(name, value);
		}

		this.onClick = function(callback) {
			this.view.addEventListener('click', function(event){
				callback(event);
			})
		}

		this.on = function(e_name, callback) {
			this.view.addEventListener(e_name, function(event) {
				callback(event);
			})
		}

		this.setVariant = function(variant) {
			switch(variant){
				case "text":
					this.removeClass('btn2').removeClass('btn_outline').addClasses(['btn_text']);
					break;

				case "contained":
					this.removeClass('btn_text').removeClass('btn_outline').addClasses(["btn2"]);
					break;

				case "outlined":
					this.removeClass('btn_text').removeClass('btn2').addClasses(['btn_outline']);
					break;
			}
		}

		this.setSize = function(size) {
			this.addClasses([size]);
		}

		this.show = function() {
			this.view.style.display = 'inline';
		}

		this.hide = function(){
			this.view.style.display = 'none';
		}

		this.addClasses(['btn2']);
	}

	function EditText() {
		this.view = document.createElement("input");
		this.type = "text";

		this.setPadding = function(left, top, right, bottom) {
			this.view.style.padding = top+'px '+left+'px '+bottom+'px '+right+'px';
			return this;
		}

		this.addClass = function(className) {
			$(this.view).addClass(className);
			return this;
		}

		this.removeClass = function(className) {
			$(this.view).removeClass(className);
			return this;
		}

		this.toggleClass = function(className) {
			$(this.view).toggleClass(className);
			return this;
		}

		this.addClasses = function(list) {
			for(var c of list){
				$(this.view).addClass(c);
			}
		}

		this.setType = function(type) {
			$(this.view).attr('type', type);
			this.type = type;
		}

		this.setHint = function(hint) {
			$(this.view).attr('placeholder', hint);
			return this;
		}

		this.getText = function(type) {
			return $(this.view).val();
		}

		this.setText = function(text) {
			$(this.view).val(text);
			return this;
		}

		this.getAttribute = function(name) {
			return $(this.view).attr(name);
		}

		this.setAttribute = function(name, value) {
			$(this.view).attr(name, value);
		}

		this.setName = function(value) {
			$(this.view).attr('name', value);
		}

		this.setType(this.type);
	}

	function TextArea() {
		this.view = document.createElement("textarea");
		this.type = "text";

		this.setPadding = function(left, top, right, bottom) {
			this.view.style.padding = top+'px '+left+'px '+bottom+'px '+right+'px';
			return this;
		}

		this.addClass = function(className) {
			$(this.view).addClass(className);
			return this;
		}

		this.removeClass = function(className) {
			$(this.view).removeClass(className);
			return this;
		}

		this.toggleClass = function(className) {
			$(this.view).toggleClass(className);
			return this;
		}

		this.addClasses = function(list) {
			for(var c of list){
				$(this.view).addClass(c);
			}
		}

		this.setType = function(type) {
			$(this.view).attr('type', type);
			this.type = type;
		}

		this.setHint = function(hint) {
			$(this.view).attr('placeholder', hint);
			return this;
		}

		this.getText = function(type) {
			return $(this.view).val();
		}

		this.setText = function(text) {
			this.view.innerHTML = text;
			return this;
		}

		this.getAttribute = function(name) {
			return $(this.view).attr(name);
		}

		this.setAttribute = function(name, value) {
			$(this.view).attr(name, value);
		}

		this.setName = function(value) {
			$(this.view).attr('name', value);
		}

		this.setType(this.type);
	}

	function Icon() {
		this.view = document.createElement("i");

		this.setPadding = function(left, top, right, bottom) {
			this.view.style.padding = top+'px '+left+'px '+bottom+'px '+right+'px';
			return this;
		}

		this.addClass = function(className) {
			$(this.view).addClass(className);
			return this;
		}

		this.removeClass = function(className) {
			$(this.view).removeClass(className);
			return this;
		}

		this.toggleClass = function(className) {
			$(this.view).toggleClass(className);
			return this;
		}

		this.addClasses = function(list) {
			for(var c of list){
				$(this.view).addClass(c);
			}
		}

		this.getAttribute = function(name) {
			return $(this.view).attr(name);
		}

		this.setAttribute = function(name, value) {
			$(this.view).attr(name, value);
		}

		this.addClass("fa");
	}


	function Modal() {
		this.view = document.createElement("div");
		this.view.style.paddingTop = "20px";
		this.content = new Div();
		this.content.orientation = "vertical";
		this.content.setWidth(450);
		this.content.addClasses(['w3-modal-content', 'shadow', 'w3-round-large'])
		this.view.appendChild(this.content.view);

		this.titleContainer = new Div();
		this.titleContainer.addClasses(['bg-info', 'w3-text-white', 'pl-15', 'pr-15', 'pt-15 pb-15', 'w3-large', 'rounded-top']);

		this.cancel = new Icon();
		this.cancel.addClasses(['fa-times', 'pointer', 'w3-hover-text-red mr-30 w3-right']);
		this.cancel.view.addEventListener("click", function() {
			//alert("hello");
			$('#reusable').html('');
		}, false);
		

		this.titleLabel = new Label();
		this.titleContainer.addView(this.titleLabel);
		this.content.addView(this.titleContainer);
		this.titleContainer.addView(this.cancel);

		this.container = new Div();
		this.container.orientation = "vertical";
		this.content.addView(this.container);

		this.setPadding = function(left, top, right, bottom) {
			this.container.view.style.padding = top+'px '+left+'px '+bottom+'px '+right+'px';
			return this;
		}

		this.setTitle = function(title) {
			this.titleLabel.setText(title);
			return this;
		}

		this.setWidth = function(width) {
			this.content.setWidth(width);
		}

		this.show = function() {
			$(this.view).show();
		}

		this.close = function() {
			$('#reusable').html('');
		}

		this.cancel = function() {
			$('#reusable').html('');
		}

		this.addView = function(elem) {
			if (this.container.orientation == "vertical") {
				elem.view.style.display = "block";
			}
			else{
				elem.view.style.display = "inline";
			}
			this.container.view.appendChild(elem.view);
			return this;
		}

		$(this.view).addClass("w3-modal");
		$('#reusable').html(this.view);
	}

	function BootstrapModal() {
		this.view = document.createElement("div");
		$(this.view).addClass('modal');
		var id = Math.floor(Math.random() * 100000);
		$(this.view).attr('id', id);

		var dialog = document.createElement('div');
		$(dialog).addClass('modal-dialog');
		this.view.appendChild(dialog);
		//this.view.style.paddingTop = "20px";
		this.content = new Div();
		this.content.orientation = "vertical";
		//this.content.setWidth(450);
		this.content.addClasses(['modal-content'])
		dialog.appendChild(this.content.view);

		this.titleContainer = new Div();
		this.titleContainer.addClasses(['modal-header']);

		this.titleLabel = new Label();
		this.titleLabel.addClasses(['h4']);
		this.titleContainer.addView(this.titleLabel);

		this.cancel = new Button();
		this.cancel.setAttribute('type', 'button');
		this.cancel.addClasses(['close']);
		this.cancel.setAttribute('data-dismiss', 'modal');
		this.cancel.setAttribute('rodz', id);
		this.cancel.setText("&times;");
		this.cancel.view.addEventListener('click', function(event){
			var v = $(event.target).attr('rodz');
			//var myModal = new bootstrap.Modal(document.getElementById(v), {});
			//myModal.hide();
			$('#'+v).modal('hide');
			$(".modal").remove();
			$(".modal-backdrop").remove();
		}, false);
		
		this.titleContainer.addView(this.cancel);
		this.content.addView(this.titleContainer);
		

		this.container = new Div();
		this.container.orientation = "vertical";
		this.container.addClasses(['modal-body']);
		this.content.addView(this.container);

		this.setPadding = function(left, top, right, bottom) {
			this.container.view.style.padding = top+'px '+left+'px '+bottom+'px '+right+'px';
			return this;
		}

		this.setTitle = function(title) {
			this.titleLabel.setText(title);
			return this;
		}

		this.setWidth = function(width) {
			dialog.style.width = width+"px";
			dialog.style.maxWidth = width+"px";
		}

		this.setSize = function(size) {
			$(dialog).addClass('modal-'+size);
		}

		this.show = function() {
			//$(this.view).show();
			try{
				/*document.body.appendChild(this.view);
				var myModal = new bootstrap.Modal(this.view, {});
				myModal.show();	*/	
				$(this.view).modal("show");
			}
			catch(E){
				alert(E.toString()+'here');
			}
		}

		this.close = function() {
			$(this.view).modal('hide');
			//var myModal = new bootstrap.Modal(this.view, {});
			//myModal.hide();
			$(".modal").remove();
			$(".modal-backdrop").remove();
		}

		this.addView = function(elem) {
			if (this.container.orientation == "vertical") {
				elem.view.style.display = "block";
			}
			else{
				elem.view.style.display = "inline";
			}
			this.container.view.appendChild(elem.view);
			return this;
		}
	}

	function Table() {
		this.view = document.createElement("table");
		this.view.style.width = '100% !important';
		this.thead = document.createElement("thead");
		this.view.style.width = '100% !important';
		this.tbody = document.createElement("tbody");

		this.view.appendChild(this.thead);
		this.view.appendChild(this.tbody);
		this.cols = 0;

		this.addColumn = function(name) {
			this.cols += 1;
			var elem = document.createElement("th");
			elem.innerHTML  = name;
			this.thead.appendChild(elem);
		}

		this.Label = function(text) {
			var l = new Label();
			l.setText(text);
			return l;
		}

		this.addRow = function(list) {
			var tr = document.createElement("tr");
			for(var name of list){
				var elem = document.createElement("td");
				elem.appendChild(name.view);
				tr.appendChild(elem);
			}
			this.tbody.appendChild(tr);
		}

		this.addColumns = function(list) {
			for(var name of list){
				this.cols += 1;
				var elem = document.createElement("th");
				elem.innerHTML  = name;
				this.thead.appendChild(elem);
			}
		}

		this.addClass = function(className) {
			$(this.view).addClass(className);
			return this;
		}

		this.addClasses = function(list) {
			for(var c of list){
				$(this.view).addClass(c);
			}
		}

		this.removeClass = function(className) {
			$(this.view).removeClass(className);
			return this;
		}

		this.setAttribute = function(name, value) {
			$(this.view).attr(name, value);
			return this;
		}

		this.setId = function(id) {
			$(this.view).attr('id', id);
			return this;
		}

		this.toggleClass = function(className) {
			$(this.view).toggleClass(className);
			return this;
		}

		this.addClasses(["table", 'table-striped']);
	}

	function TabPanel() {
		this.view = document.createElement("div");
		this.head = new Div();
		this.body = new Div();
		this.count = 0;
		this.id = Math.floor(Math.random() * 100000);

		this.view.appendChild(this.head.view);
		this.view.appendChild(this.body.view);
		this.head.addClass('w3-border-bottom');

		this.addTab = function(text) {
			var btn = new Button();
			var unik = Math.floor(Math.random() * 100000);
			btn.setAttribute('linker', unik);
			btn.setAttribute('parent', this.id);
			if(this.count == 0){
				btn.addClasses(['w3-padding', 'active', 'tabButton']);
			}
			else{
				btn.addClasses(['w3-padding', 'w3-white', 'tabButton']);
			}
			btn.setText(text)
			this.head.addView(btn);

			//the content
			var content = new Div();
			content.addClasses(['w3-padding', 'tabContent']);
			content.orientation = "vertical";
			
			content.setAttribute('id', unik);
			content.setAttribute('parent', this.id);
			
			this.body.addView(content);
			if(this.count == 0){
				//btn.addClasses(['w3-padding', 'w3-text-white', 'bg-dark', 'tabButton']);
			}
			else{
				content.view.style.display = 'none';
			}
			this.count += 1;
			return content;
		}
	}

	function Spinner() {
		this.view = document.createElement("select");

		this.addClass = function(className) {
			$(this.view).addClass(className);
			return this;
		}

		this.addClasses = function(list) {
			for(var c of list){
				$(this.view).addClass(c);
			}
		}

		this.removeClass = function(className) {
			$(this.view).removeClass(className);
			return this;
		}

		this.getAttribute = function(name) {
			return $(this.view).attr(name);
		}

		this.setAttribute = function(name, value) {
			$(this.view).attr(name, value);
		}

		this.setValues = function(values) {
			for(var i = 0; i < values[0].length; i++){
				$(this.view).append('<option value="'+values[0][i]+'">'+values[1][i]+'</option>');
			}
		}

		this.addClasses(['form-control']);
	}

	$(document).on('click', '.tabButton', function(event) {
		$('.tabButton').removeClass('active').addClass('w3-white');
		$(this).removeClass('w3-white').addClass('active');
		var id = $(this).attr('linker');
		$('.tabContent').hide();
		$('#'+id).show();

		//animate the bottom bar

		//create a ripple effect
		var dim = $(this).offset();

		var wrap = document.createElement("span");
		this.appendChild(wrap);
		$(wrap).css('position', 'absolute').css('left', '0').css('top', '0').css('width', '100%').css('height', this.clientHeight+"px");
		var x = event.clientX - dim.left;
		var y = event.clientY - dim.top;
		//write the css
		var style = document.getElementById('stylesheet1');
		var keyFrames = "@keyframes ripple{from{width: 0;height: 0;top: "+y+"px;left: "+x+"px;} to{width: 100%;height: 100%;top: 0;left: 0;}}";
		style.innerHTML = keyFrames

		wrap.classList.add('mrip');
		var remove = function() {
			$(wrap).fadeOut(100);
		}

		setTimeout(remove, 500);
	})

var LayoutInflator = {
	inflate : function(resource, elem, callback) {
		var new_str = '';
		for(var i = 0; i < resource.length; i++){
			if (resource.substr(i,1) == ".") {
				new_str += "/";
			}
			else{
				new_str += resource.substr(i,1);
			}
		}
		
		resource = new_str+".html";

		//Toast(resource);

		$.get(resource, function(response, status) {
			//Toast(response);
			elem.innerHTML = response;
			callback();
		})
	}
}

function productView(product){
	var cont = new Div();
	cont.setOrientation(LinearLayout.VERTICAL);
	cont.addClasses(['w3-col', 'w3-padding-small', 'produ', 'wid']);
	cont.setAttribute('views', product.views);
	cont.setAttribute('price', product.price);
	
	var bordered = new Div()
	cont.addView(bordered);
	bordered.addClasses(['w3-white', 'prodShadow', 'ya']);
	bordered.setOrientation(LinearLayout.VERTICAL);
	bordered.setAttribute('data', product.id);

	var ivContainer = new Div();
	ivContainer.addClasses(['w3-padding', 'pointer', 'w3-hover-grey', 'ivContainer', 'cc']);
	ivContainer.setOrientation(LinearLayout.VERTICAL);
	ivContainer.setAttribute('data', product.id);
	
	var iv = new ImageView();
	iv.setAttribute('src', 'products/'+product.resampled);
	iv.setAttribute('width', '100%');
	ivContainer.addView(iv);
	bordered.addView(ivContainer);

	var textContainer = new Div();
	bordered.addView(textContainer);
	textContainer.addClasses(['w3-padding-small', 'cc']);
	textContainer.setOrientation(LinearLayout.VERTICAL);

	var name = new Label();
	name.setText(product.name);
	name.addClasses(['fade-text']);
	textContainer.addView(name);

	var features = new Label();
	features.setText(product.features);
	features.addClasses(['w3-small', 'text-primary', 'fade-text']);
	textContainer.addView(features);

	var bottom = new Div();
	bottom.addClasses(['clearfix', 'pt-10', 'pb-15']);

	var stars = new Label();
	stars.view.innerHTML = '3.5 <i class="fa fa-star"></i>';
	stars.addClasses(['w3-opacity']);
	bottom.addView(stars);

	var price = new Label();
	price.setText("K"+product.price);
	price.addClasses(['float-right']);
	bottom.addView(price);
	textContainer.addView(bottom);

	return cont;
}

$(document).on('click', '.ivContainer, .ya', function(event){
	window.location = 'product-details.php?id='+$(this).attr('data');
});

$(document).on('click', '.switch', function() {
		var toggle = this.getElementsByClassName('toggle')[0];
		var input = this.getElementsByTagName('input')[0];

		if ($(toggle).hasClass('off')) {
			$(toggle).removeClass('off').addClass('on');
			input.checked = 'checked';
		}
		else{
			$(toggle).removeClass('on').addClass('off');
			input.checked = '';
		}

		try{
			toggleSwitchChanged(input);
		}catch(E){
			alert(E.toString());
		}
	});

	function ProgressIndicator() {
		this.view = document.createElement("div");
		this.cont = document.createElement("div");

		$(this.view).addClass('indicator');
		$(this.cont).addClass('indicator-progress');
		this.view.appendChild(this.cont);

		this.setValue = function(percent) {
			this.cont.style.width = percent+'%';
		}
		this.margin = 0;
		this.canLoad = true;

		this.setLoading = function(b) {
			if (b) {
				//show loading
				this.setValue(45);
				this.canLoad = true;				
				this.load();
			}
			else{
				///stop
				this.canLoad = false;
			}
		}

		this.load = async function() {
			if (this.canLoad) {
				this.margin += 3;
				if (this.margin >= 105) {
					this.margin = -20;
				}
				this.cont.style.marginLeft = this.margin+"%";

				await sleep(30);
				this.load();
			}
		}
	}

	function toggleSwitch() {
		this.view = document.createElement('font');
		this.cont = document.createElement('font');
		this.toggle = document.createElement('font');
		this.input = document.createElement('input');
		$(this).attr('type', 'checkbox');
		$(this.view).addClass('switch');
		$(this.cont).addClass('cont');
		$(this.toggle).addClass('toggle').addClass('off');
		this.cont.innerHTML = '&nbsp;'
		this.toggle.innerHTML = '&nbsp;'
		this.view.appendChild(this.cont);
		this.view.appendChild(this.toggle);
		this.view.appendChild(this.input);
		this.changecallback = null;

		this.isChecked = function() {
			if (input.checked) {
				return true;
			}
			return false;
		}

		this.setName = function(name) {
			$(this.input).attr('name', name);
		}

		this.setChecked = function(b) {
			if (b) {
				$(this.toggle).removeClass('off').addClass('on');
				this.input.checked = 'checked';
			}
			else{
				$(this.toggle).removeClass('on').addClass('off');
				this.input.checked = '';
			}
		}
	}

	function Snackbar(obj) {
		var div, font, button;

		div = new Div();
		font = new Label();
		button = new Button();

		div.addClasses(['snackbar', 'alert', 'bg-dark', 'clearfix', 'w3-text-white', 'w3-animate-bottom']);

		font.setText(obj.text);
		font.addClasses(['w3-large']);
		div.addView(font);

		button.setText(obj.buttonText);
		button.addClasses(['btn', 'snackbar-btn', 'float-right']);
		button.view.addEventListener('click', function() {
			$(div.view).remove();
			obj.handler();
		})
		div.addView(button);

		document.body.appendChild(div.view);
		var close = function() {
			$(div.view).remove();
		}
		setTimeout(close, 5000);
	}

	function BottomSheetDialog() {
		var maxZ = Math.max.apply(null, 
		    $.map($('body *'), function(e,n) {
		      if ($(e).css('position') != 'static'){
		        return parseInt($(e).css('z-index')) || 1;
		    }
		}));
		this.view = document.createElement("div");
		$(this.view).css('z-index', maxZ+1).css('position', 'fixed').css('top', '0').css('left', '0').css('width', '100%').css('background', 'rgba(0,0,0,.43');
		this.view.style.height = window.innerHeight+"px";
		$(this.view).addClass('bSheet');

		this.view1 = document.createElement("div");
		this.view.appendChild(this.view1);
		$(this.view1).addClass('bottomSheet').addClass('totop').addClass('w3-padding-bottom').addClass('w3-white');

		this.head = new Div();
		this.head.addClasses(['w3-center', 'w3-padding']);
		this.view1.appendChild(this.head.view);

		this.line = new Label();
		this.line.setText("&nbsp;");
		this.line.addClasses(['sheet-line', 'w3-center']);
		$(this.line.view).on('click', function() {
			$('.sheet-content').html('');
			$('.bottomSheet').removeClass('totop').addClass('tobottom');

			var close = function() {
				$('.bSheet').remove();
			}

			setTimeout(close, 900);
		})
		this.head.addView(this.line);

		this.content = new Div();
		this.content.addClasses(['sheet-content']);
		this.view1.appendChild(this.content.view);

		this.show = function() {
			document.body.appendChild(this.view);
			$(this.view1).show();
		}

		this.addView = function(view) {
			this.content.addView(view);
		}
	}

	function Chips(){
		this.view = document.createElement("button");

		this.startIcon = new Icon();
		this.label = new Label();
		this.endIcon = new Icon();

		this.view.appendChild(this.startIcon.view);
		this.view.appendChild(this.label.view);
		this.view.appendChild(this.endIcon.view);

		this.setPadding = function(left, top, right, bottom) {
			this.view.style.padding = top+'px '+left+'px '+bottom+'px '+right+'px';
			return this;
		}

		this.addClass = function(className) {
			$(this.view).addClass(className);
			return this;
		}

		this.removeClass = function(className) {
			$(this.view).removeClass(className);
			return this;
		}

		this.setName = function(name) {
			$(this.view).attr('name', name);
		}

		this.toggleClass = function(className) {
			$(this.view).toggleClass(className);
			return this;
		}

		this.addView = function(elem) {
			this.view.appendChild(elem.view);
			return this;
		}

		this.renderIn = function(id) {
			document.getElementById(id).appendChild(this.view);
		}

		this.removeView = function(view1) {
			this.view.removeChild(view);
			return this;
		}

		this.addClasses = function(list) {
			for(var c of list){
				$(this.view).addClass(c);
			}
		}

		this.getText = function(type) {
			return $(this.label.view).text();
		}

		this.setText = function(text) {
			$(this.label.view).html(text);
			return this;
		}

		this.getAttribute = function(name) {
			return $(this.view).attr(name);
		}

		this.isActive = function() {
			return $(this.view).hasClass('active');
		}

		this.setAttribute = function(name, value) {
			$(this.view).attr(name, value);
		}

		this.onClick = function(callback) {
			this.view.addEventListener('click', function(event){
				callback(event);
			})
		}

		this.setStartIcon = function(icon) {
			this.startIcon.addClasses(icon.split(" "));
			this.startIcon.addClasses(['mr-10']);
		}

		this.showEndIcon = function() {
			this.endIcon.addClasses(['fa', 'fa-times-circle', 'ml-10', 'w3-hover-text-red']);
		}
		this.addClasses(['chips', 'w3-round-xxlarge', 'w3-light-grey', 'ripple'])
	}

$(document).on('click', '.chips', function() {
	if ($(this).hasClass('active')) {
		$(this).removeClass('active').addClass('w3-light-grey');
		try{
			chipsClicked($(this).attr('name'), true);
		}catch(E){}
	}
	else{
		$(this).removeClass('w3-light-grey').addClass('active');
		try{
			chipsClicked($(this).attr('name'), false);
		}catch(E){}
	}
})

function Card() {
	this.view = document.createElement("div");
	$(this.view).addClass('bs-sm').addClass('rounded').addClass('rcard');

	this.title = new Label();
	this.secondaryText = new Label();

	this.textContainer = new Div();
	this.textContainer.setPadding(16, 8, 16, 8);

	this.supportingText = new Label();

	this.image = new ImageView();
	$(this.image.view).hide();

	this.view.appendChild(this.image.view);
	this.view.appendChild(this.textContainer.view);

	this.textContainer.view.appendChild(this.title.view);
	this.textContainer.view.appendChild(this.secondaryText.view);
	this.textContainer.view.appendChild(this.supportingText.view);

	this.bottom = new Div();
	this.bottom.addClasses(['pt-15']);
	$(this.bottom.view).hide();

	this.okayButtom = new Button();
	this.bottom.addView(this.okayButtom);
	this.cancelButton = new Button();
	this.bottom.addView(this.cancelButton);
	this.textContainer.addView(this.bottom);

	this.setTitle = function(text) {
		this.title.setText(text);
		this.title.addClasses(['h4']);
	}

	this.setSecondaryText = function(text) {
		this.secondaryText.setText(text);
		this.secondaryText.addClasses(['h5', 'w3-opacity']);
	}

	this.setSupportingText = function(text) {
		this.supportingText.setText("<br>"+text);
		this.supportingText.addClasses(['h5','mt-10', 'w3-opacity']);
	}

	this.addClass = function(className) {
		$(this.view).addClass(className);
		return this;
	}

	this.addClasses = function(list) {
		for(var c of list){
			$(this.view).addClass(c);
		}
	}

	this.setImage = function(src) {
		$(this.image.view).show();
		this.image.setAttribute('src', src);
		this.image.view.style.width = '100%';
	}

	this.setPositiveButton = function(text, callback) {
		$(this.bottom.view).show();
		this.okayButtom.setText(text);
		this.okayButtom.addClasses(['rbtn', 'ripple', 'text-primary']);
		//return this.okayButtom.view; 

		this.okayButtom.view.addEventListener('click', function() {
			callback();
		})
	}

	this.setNegativeButton = function(text, callback) {
		$(this.bottom.view).show();
		this.cancelButton.setText(text);
		this.cancelButton.addClasses(['rbtn', 'ripple', 'text-primary', 'ml-10']);
		//return this.okayButtom.view; 

		this.cancelButton.view.addEventListener('click', function() {
			callback();
		})
	}
}

function Gallery() {
	this.view = document.createElement("div");
}

function Divider() {
	this.view = document.createElement("div");

	this.left = new Div();
	this.right = new Div();
	this.view.appendChild(this.left.view);
	this.view.appendChild(this.right.view);

	$(this.view).addClass('w3-row').addClass('divider');

	this.left.addClasses(['w3-col', 's3', 'w3-center']);
	this.right.addClasses(['w3-col', 's9', 'dividerR']);

	this.title = new Label();
	this.secondaryText = new Label();

	this.textContainer = new Div();
	this.textContainer.setPadding(16, 8, 16, 8);

	this.supportingText = new Label();

	this.image = new ImageView();
	$(this.image.view).hide();

	this.left.addView(this.image);
	this.right.addView(this.textContainer);

	this.textContainer.view.appendChild(this.title.view);
	this.textContainer.view.appendChild(this.secondaryText.view);
	this.textContainer.view.appendChild(this.supportingText.view);

	this.setTitle = function(text) {
		this.title.setText(text+"<br>");
		this.title.addClasses(['h4']);
	}

	this.setSecondaryText = function(text) {
		this.secondaryText.setText(text);
		this.secondaryText.addClasses(['h5', 'w3-opacity']);
	}

	this.setSupportingText = function(text) {
		this.supportingText.setText("<br>"+text);
		this.supportingText.addClasses(['h5','mt-10', 'w3-opacity']);
	}

	this.addClass = function(className) {
		$(this.view).addClass(className);
		return this;
	}

	this.addClasses = function(list) {
		for(var c of list){
			$(this.view).addClass(c);
		}
	}

	this.setAttribute = function(name, value) {
		$(this.view).attr(name, value);
		return this;
	}

	this.setImage = function(src) {
		$(this.image.view).show();
		this.image.setAttribute('src', src);
		this.image.view.style.width = '100%';
	}
}


function RateBar(){
	this.view = document.createElement("div");
	$(this.view).addClass('w3-row');
	this.id = Math.floor(Math.random() * 100000);
	$(this.view).attr('id', this.id);
	this.num = 5;
	$(this.view).attr('value', 0);
	this.stars = [];

	this.addClass = function(className) {
		$(this.view).addClass(className);
		return this;
	}

	this.addClasses = function(list) {
		for(var c of list){
			$(this.view).addClass(c);
		}
	}

	this.getValue = function() {
		return $(this.view).attr('value');
	}

	this.setValue = function(value) {
		$(this.view).attr('value', value);
		var pos = value -1;
		var parent = this.id;

		var all = this.stars;

		for(var star of all){
			if($(star).attr('parent') == parent){
				$(star).removeClass('active');
			}
		}

		for(var i = 0; i <= pos; i++){
			$(all[i]).addClass('active');
		}
		//Toast("done, "+parent+", "+pos);
	}

	this.setNumStars = function(num) {
		this.num = num;
		this.reload();
	}

	this.reload = function() {
		this.view.innerHTML = '';
		var width = 100 / this.num;
		this.stars = [];
		for(var i = 0; i < this.num; i++){
			var label = new Label();
			label.addClasses(['w3-col', 'w3-center']);
			label.view.style.width = width+"%";

			var icon = new Icon();
			icon.addClasses(['fa', 'fa-star', 'rate-star']);
			icon.setAttribute('pos', i);
			icon.setAttribute('parent', this.id);
			icon.setAttribute('id', i+""+this.id);
			label.addView(icon);
			this.stars.push(icon.view);
			this.view.appendChild(label.view);
		}
	}

	this.setWidth = function(width) {
		this.view.style.width = width+"px";
	}

	this.reload();
}

$(document).on('click', '.rate-star', function(event) {
	var pos = Number($(this).attr('pos'));
	var parent = $(this).attr('parent');

	var all = document.getElementsByClassName('rate-star');

	for(var star of all){
		if($(star).attr('parent') == parent){
			$(star).removeClass('active');
		}
	}

	for(var i = 0; i <= pos; i++){
		$('#'+i+""+parent).addClass('active');
	}
	pos += 1;

	$('#'+parent).attr('value', pos);
})


function OutlinedEditText() {
	this.view = document.createElement("div");
	$(this.view).addClass('edittext2');
	this.mode = "input";
	this.hint = "";
	this.input;

	this.label = new Label();
	this.label.addClasses(['roboto', 'text-primary'])
	this.view.appendChild(this.label.view);

	this.load = function() {
		if (this.mode == "textarea") {
			this.input = new TextArea();
		}
		else{
			this.input = new EditText();
		}
		this.setHint(this.hint);
		this.view.appendChild(this.input.view);
	}

	this.setMode = function(mode) {
		if (this.input != undefined) {
			$(this.input.view).remove();
		}
		this.mode = mode;
		this.load();
	}

	this.setHint = function(hint) {
		this.hint = hint;
		this.label.setText(hint);
		this.input.setHint(hint);
	}

	this.setName = function(name) {
		this.input.setName(name);
	}

	this.getText = function() {
		return this.input.getText();
	}

	this.setText = function(text) {
		this.input.setText(text);
	}

	this.addClass = function(className) {
		$(this.view).addClass(className);
		return this;
	}

	this.addClasses = function(list) {
		for(var c of list){
			$(this.view).addClass(c);
		}
	}

	this.onKeyUp = function(callback) {
		this.input.view.addEventListener('keyup', function(event) {
			callback(event);
		})
	}

	this.setError = function(error) {
		this.removeError();
		this.input.addClasses(['error']);
		this.errorLabel = new Label();
		this.errorLabel.setText(error);
		this.errorLabel.addClasses(['text-danger']);
		this.view.appendChild(this.errorLabel.view);
		this.errorLabel.view.style.display = 'block';
	}

	this.removeError = function() {
		try{
			this.input.removeClass('error');
			$(this.errorLabel.view).remove();
		}catch(E){

		}
	}

	this.showHint = function() {
		this.label.addClasses(['animate-label']);
		$(this.label.view).show();
	}

	this.load();
}

function FilledEditText() {
	this.view = document.createElement("div");
	$(this.view).addClass('filled-edittext');
	this.mode = "input";
	this.hint = "";
	this.input;

	this.label = new Label();
	this.label.addClasses(['roboto', 'text-primary'])
	this.view.appendChild(this.label.view);

	this.load = function() {
		if (this.mode == "textarea") {
			this.input = new TextArea();
		}
		else{
			this.input = new EditText();
		}
		this.setHint(this.hint);
		this.view.appendChild(this.input.view);
	}

	this.setMode = function(mode) {
		if (this.input != undefined) {
			$(this.input.view).remove();
		}
		this.mode = mode;
		this.load();
	}

	this.setHint = function(hint) {
		this.hint = hint;
		this.label.setText(hint);
		this.input.setHint(hint);
	}

	this.setName = function(name) {
		this.input.setName(name);
	}

	this.getText = function() {
		return this.input.getText();
	}

	this.setText = function(text) {
		this.input.setText(text);
	}

	this.addClass = function(className) {
		$(this.view).addClass(className);
		return this;
	}

	this.addClasses = function(list) {
		for(var c of list){
			$(this.view).addClass(c);
		}
	}

	this.onKeyUp = function(callback) {
		this.input.view.addEventListener('keyup', function(event) {
			callback(event);
		})
	}

	this.setError = function(error) {
		this.removeError();
		this.input.addClasses(['error']);
		this.errorLabel = new Label();
		this.errorLabel.setText(error);
		this.errorLabel.addClasses(['text-danger']);
		this.view.appendChild(this.errorLabel.view);
		this.errorLabel.view.style.display = 'block';
	}

	this.removeError = function() {
		try{
			this.input.removeClass('error');
			$(this.errorLabel.view).remove();
		}catch(E){

		}
	}

	this.load();
}

function StandardEditText() {
	this.view = document.createElement("div");
	$(this.view).addClass('filled-edittext').addClass('tra');
	this.mode = "input";
	this.hint = "";
	this.input;

	this.label = new Label();
	this.label.addClasses(['roboto', 'text-primary'])
	this.view.appendChild(this.label.view);

	this.load = function() {
		if (this.mode == "textarea") {
			this.input = new TextArea();
		}
		else{
			this.input = new EditText();
		}
		this.setHint(this.hint);
		this.view.appendChild(this.input.view);
	}

	this.setMode = function(mode) {
		if (this.input != undefined) {
			$(this.input.view).remove();
		}
		this.mode = mode;
		this.load();
	}

	this.setHint = function(hint) {
		this.hint = hint;
		this.label.setText(hint);
		this.input.setHint(hint);
	}

	this.setName = function(name) {
		this.input.setName(name);
	}

	this.getText = function() {
		return this.input.getText();
	}

	this.setText = function(text) {
		this.input.setText(text);
	}

	this.addClass = function(className) {
		$(this.view).addClass(className);
		return this;
	}

	this.addClasses = function(list) {
		for(var c of list){
			$(this.view).addClass(c);
		}
	}

	this.onKeyUp = function(callback) {
		this.input.view.addEventListener('keyup', function(event) {
			callback(event);
		})
	}

	this.setError = function(error) {
		this.removeError();
		this.input.addClasses(['error']);
		this.errorLabel = new Label();
		this.errorLabel.setText(error);
		this.errorLabel.addClasses(['text-danger']);
		this.view.appendChild(this.errorLabel.view);
		this.errorLabel.view.style.display = 'block';
	}

	this.removeError = function() {
		try{
			this.input.removeClass('error');
			$(this.errorLabel.view).remove();
		}catch(E){

		}
	}

	this.load();
}


$(document).on('focus', '.edittext2 input, .edittext2 textarea, .filled-edittext input', function(event) {
	var div = this.parentElement;
	var label = div.getElementsByTagName('font')[0];
	if ($(div).hasClass("filled-edittext")) {
		$(label).removeClass('animate-labelf').addClass('animate-labelf').addClass('text-primary').show();
	}
	else{
		$(label).removeClass('animate-label').addClass('animate-label').addClass('text-primary').show();
	}
	$(this).attr('placeholder', '');
})

$(document).on('blur', '.edittext2 input, .edittext2 textarea, .filled-edittext input', function(event) {
	if (this.value == "") {
		var div = this.parentElement;
		var label = div.getElementsByTagName('font')[0];
		$(label).hide();
		$(this).attr('placeholder', label.innerHTML);
	}
	else{
		var div = this.parentElement;
		var label = div.getElementsByTagName('font')[0];
		$(label).removeClass('text-primary');
		//$(this).attr('placeholder', label.innerHTML);
	}
});

function ProgressBar() {
	this.view = document.createElement("div");
	$(this.view).addClass('progress');
	this.view.style.height = '10px';
	this.value = 0;

	this.bar = new Div();
	this.bar.addClasses(['progress-bar']);
	this.bar.setHeight(10);
	this.view.appendChild(this.bar.view);

	this.setValue = function(value) {
		this.value = value;
		this.bar.view.style.width = value+"%";
	}

	this.getValue = function() {
		return this.valuel
	}
}

function Select(){
	this.view = document.createElement("select");

	this.setPadding = function(left, top, right, bottom) {
		this.view.style.padding = top+'px '+left+'px '+bottom+'px '+right+'px';
		return this;
	}

	this.addClass = function(className) {
		$(this.view).addClass(className);
		return this;
	}

	this.removeClass = function(className) {
		$(this.view).removeClass(className);
		return this;
	}

	this.toggleClass = function(className) {
		$(this.view).toggleClass(className);
		return this;
	}

	this.addClasses = function(list) {
		for(var c of list){
			$(this.view).addClass(c);
		}
	}

	this.getText = function(type) {
		return $(this.view).val();
	}

	this.setText = function(text) {
		$(this.view).val(text);
		return this;
	}

	this.getAttribute = function(name) {
		return $(this.view).attr(name);
	}

	this.setAttribute = function(name, value) {
		$(this.view).attr(name, value);
	}

	this.setName = function(value) {
		$(this.view).attr('name', value);
	}

	this.add = function(obj) {
		var option = document.createElement("option");
		option.text = obj.text;
		option.value = obj.value;
		this.view.add(option);
	}
}


function inflateContextMenu(event, resource) {
	var new_str = '';
	for(var i = 0; i < resource.length; i++){
		if (resource.substr(i,1) == ".") {
			new_str += "/";
		}
		else{
			new_str += resource.substr(i,1);
		}
	}
	
	resource = new_str+".json";

	//Toast(resource);

	$.get(resource, function(response, status) {
		try{
			//var obj = JSON.parse(response);
			var container2 = new Div();
			container2.addClasses(['menuContainer', 'w3-white']);
			document.body.appendChild(container2.view);
			container2.view.style.top = (event.clientY)+"px";
			container2.view.style.left = (event.clientX)+"px";
			container2.view.style.display = 'block';

			for(var menu of response){
				var dic = new Div();
				dic.addClasses(['w3-padding', 'pointer','w3-hover-light-grey', 'menuC']);
				dic.setAttribute('data', menu.id);

				if(menu.icon != undefined){
					var ic = new Icon();
					ic.addClasses(menu.icon.split(" "));
					ic.addClasses(['mr-15']);
					dic.addView(ic);
				}

				var tit = new Label();
				tit.setText(menu.title);
				dic.addView(tit);

				container2.addView(dic);
			}
		}
		catch(E){
			alert(E.toString()+response);
		}
	})
}

$(document).on('click', function(event) {
	$('.menuContainer').remove();
});

$(document).on('click', '.menuC', function(event) {
	try{
		contextMenuCliked($(this).attr('data'));
	}
	catch(E){
		alert(E.toString());
	}
})

function Hr() {
	this.view = document.createElement("hr");
}


//add ripple effects to material button
$(document).on('click', '.btn2', function(event) {
	//create a ripple effect
	var dim = $(this).offset();

	var wrap = document.createElement("span");
	this.appendChild(wrap);
	$(wrap).css('position', 'absolute').css('left', '0').css('top', '0').css('width', '100%').css('height', this.clientHeight+"px").css('background', 'rgba(255, 255, 255, 0.2)');
	var x = event.clientX - dim.left;
	var y = event.clientY - dim.top;
	//write the css
	var style = document.getElementById('stylesheet1');
	var keyFrames = "@keyframes ripple{from{width: 0;height: 0;top: "+y+"px;left: "+x+"px;} to{width: 100%;height: 100%;top: 0;left: 0;}}";
	style.innerHTML = keyFrames

	wrap.classList.add('btn2-rip');
	var remove = function() {
		$(wrap).fadeOut(100);
	}

	setTimeout(remove, 500);
})


function Dialog() {
	this.view = document.createElement("div");
	this.view.classList.add("w3-modal");
	this.view.addEventListener('click', function(event) {
		var d = event.target;
		$(d).fadeOut(600);
		var cloze = function() {
			$(d).remove();
		}

		setTimeout(cloze, 600);
	})

	this.content = new Div();
	this.content.view.addEventListener('click', function(event) {
		event.stopPropagation();
	})
	this.view.appendChild(this.content.view);
	this.content.addClasses(['w3-modal-content', 'w3-padding-large', 'w3-round w3-white shadow', 'md']);

	this.title = new Label();
	this.title.addClasses(['block', 'h3']);
	this.content.addView(this.title);

	this.message = new Label();
	this.message.addClasses(['text-secondary']);
	this.content.addView(this.message);

	this.external = new Div();
	this.external.addClasses(['pt-10', 'pb-10']);
	this.content.addView(this.external);

	this.bottom = new Div();
	this.bottom.addClasses(['clearfix']);
	this.content.addView(this.bottom);

	this.right = new Span();
	this.right.addClasses(['float-right']);
	this.bottom.addView(this.right);

	this.negativeButton = new MaterialButton();
	this.negativeButton.setVariant("text");
	this.negativeButton.hide();
	this.right.addView(this.negativeButton);
	
	this.positiveButton = new MaterialButton();
	this.positiveButton.setVariant("text");
	this.positiveButton.addClasses(['ml-15']);
	this.positiveButton.hide();
	this.right.addView(this.positiveButton);


	this.setTitle = function(text) {
		this.title.setText(text);
	}

	this.setMessage = function(text) {
		this.message.setText(text);
	}

	this.setPositiveButton = function(text, callback) {
		this.positiveButton.show();
		this.positiveButton.setText(text);
		this.positiveButton.onClick(function() {
			callback();
		})
	}

	this.setNegativeButton = function(text, callback) {
		this.negativeButton.show();
		this.negativeButton.setText(text);
		this.negativeButton.onClick(function() {
			callback();
		})
	}

	this.setView = function(view) {
		this.external.addView(view);
	}

	this.setContentView = function(view) {
		this.external.addView(view);
	}

	this.show = function() {
		document.body.appendChild(this.view);
		this.view.style.display = 'block';
	}

	this.close = function() {
		$(this.view).fadeOut(600);
		var cloze = function() {
			$(this.view).remove();
		}

		setTimeout(cloze, 600);
	}

	this.cancel = function() {
		$(this.view).fadeOut(600);
		var cloze = function() {
			$(this.view).remove();
		}

		setTimeout(cloze, 600);
	}
}