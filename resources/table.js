function MaterialTable(id) {
	if (id == undefined) {
		this.view = document.createElement("div");

		$(this.view).addClass('datatable');
		this.id = Math.floor(Math.random() * 100000);
		$(this.view).attr('data', this.id).attr('id', 'table-'+this.id);

		this.table = document.createElement("table");
		$(this.table).attr('id', this.id);
		this.view.appendChild(this.table);
		this.thead = document.createElement("thead");
		this.tbody = document.createElement("tbody");
		this.table.appendChild(this.thead);
		this.table.appendChild(this.tbody);
	}
	else{
		this.view = document.getElementById(id);
		var chars = id.split("-");
		this.id = chars[1];
		this.table = this.view.getElementsByTagName('table')[0];
		this.thead = this.table.tHead;
		this.tbody = this.table.tBodies[0];
	}

	this.addColumns = function(list) {
		for(var col of list){
			var th = document.createElement("th");
			th.innerHTML = col;
			this.thead.appendChild(th);
		}
	}

	this.addRow = function(list) {
		var tr = document.createElement("tr");
		for(var data of list){
			var td = document.createElement("td");
			if (data.tagName != undefined) {
				td.appendChild(data);
			}
			else{
				td.innerHTML = data;
				$(td).attr('title', data);
			}
			tr.appendChild(td);
		}
		this.tbody.appendChild(tr);
	}

	this.setDataTable = function() {
		this.bottom = new Div();
		this.view.appendChild(this.bottom.view);
		this.bottom.addClasses(['clearfix w3-padding']);

		$(this.table).attr('page', 0);
		$(this.table).attr('rows', 5);

		var input = new EditText();
		input.setHint("Search");
		input.addClasses(['datatable-search', 'w3-padding', 'border']);
		input.setAttribute('data', this.id);
		input.view.style.display = 'inline';
		this.bottom.addView(input);

		var right = new Label();
		right.addClasses(['float-right']);
		this.bottom.addView(right);
		var rowspage = new Label();
		rowspage.setText("Rows per page");
		right.addView(rowspage);

		var rows = new Select();
		rows.addClasses(['datatable-select']);
		rows.setAttribute('data', this.id);
		right.addView(rows);
		var values = [
			{text:"5", value:5},
			{text:"10", value:10},
			{text:"15", value:15},
			{text:"20", value:20},
			{text:"50", value:50},
			{text:"100", value:100},
			{text:"200", value:200},
			{text:"500", value:500},
		];
		for(var option of values){
			rows.add(option);
		}

		var current_pos = new Label();
		current_pos.setText("1-10 of 100");
		right.addView(current_pos);

		var pagination1 = new Label();
		pagination1.addClasses(['pagination1']);
		right.addView(pagination1);

		var iconStart = new Icon();
		iconStart.addClasses(['bx', 'bx-arrow-to-left', 'dt-start', 'pointer', 'w3-hover-text-purple']);
		iconStart.setAttribute('data', this.id);

		var previous = new Icon();
		previous.addClasses(['bx', 'bx-chevron-left', 'dt-previous', 'pointer', 'w3-hover-text-purple']);
		previous.setAttribute('data', this.id);
		

		var next = new Icon();
		next.addClasses(['bx', 'bx-chevron-right', 'dt-next', 'pointer', 'w3-hover-text-purple']);
		next.setAttribute('data', this.id);
		

		var iconLast = new Icon();
		iconLast.addClasses(['bx', 'bx-arrow-to-right', 'dt-last', 'pointer', 'w3-hover-text-purple']);
		iconLast.setAttribute('data', this.id);

		pagination1.addAll(iconStart, previous, next, iconLast);
		this.reloadView();
	}

	this.reloadView = function() {
		var page = Number($(this.table).attr('page'));
		var rows = Number($(this.table).attr('rows'));
		var offset = page * rows;
		var limit = offset + rows - 1;

		var trs = this.table.rows;
		for(var i = 0; i < trs.length; i++){
			var tr = trs[i];
			if (i >= offset && i <= limit) {
				$(tr).show();
			}
			else{
				$(tr).hide();
			}
		}
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
}

$(document).on('keyup', '.datatable-search', function(event) {
	var element = this;
	var id = $(element).attr('data');
	var table = document.getElementById(id);
	var text = element.value.toLowerCase();

	var rows = table.getElementsByTagName('tr');
	for(var row of rows){
		var innerText = $(row).text().toLowerCase();

		if (innerText.indexOf(text) != (0-1)) {
			$(row).show();
		}
		else{
			$(row).hide();
		}
	}
})

$(document).on('click', '.dt-start', function(event){
	var element = this;
	var id = $(element).attr('data');

	var table = new MaterialTable('table-'+id);
	$(table.table).attr('page', 0);
	table.reloadView();
})

$(document).on('click', '.dt-previous', function(event){
	var element = this;
	var id = $(element).attr('data');

	var table = new MaterialTable('table-'+id);
	var current = Number($(table.table).attr('page'));
	if (current > 0) {
		$(table.table).attr('page', current -1);
		table.reloadView();
	}
})

$(document).on('click', '.dt-next', function(event){
	var element = this;
	var id = $(element).attr('data');

	var table = new MaterialTable('table-'+id);
	var current = Number($(table.table).attr('page'));
	
	$(table.table).attr('page', current + 1);
	table.reloadView();
})

$(document).on('click', '.dt-last', function(event){
	var element = this;
	var id = $(element).attr('data');

	var table = new MaterialTable('table-'+id);
	var current = Number($(table.table).attr('page'));
	var rowCount = Number($(table.table).attr('rows'));

	var rows = table.table.rows.length;

	var page = Math.floor(rows / rowCount);
	
	$(table.table).attr('page', page);
	table.reloadView();
})


$(document).ready(function(event) {
	/*
	var parent = document.getElementById("newTable");
	var table = new MaterialTable();
	table.addColumns(['#', 'Name', 'price', 'picture', 'resampled', 'qty', 'status', 'views', 'features']);
	parent.appendChild(table.view);

	$.get("handler.php?getProducts", function(res, status) {
		var i = 1;
		for(var row of res){
			table.addRow([
				i, row.name, row.price, row.picture, row.resampled, row.qty, row.status, row.views, row.features
			]); 
			i += 1;
		}
		table.setDataTable();
	}); */
})