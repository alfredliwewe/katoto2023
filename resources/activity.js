var actionBar = new Div();
actionBar.addClasses(['bg-primary', 'w3-text-white', 'w3-padding', 'clearfix']);
document.body.appendChild(actionBar.view);

var iconContainer = new Label();
iconContainer.setPadding(14, 0, 14, 0);
actionBar.addView(iconContainer);
iconContainer.view.style.display = 'none';

var iconTop = new Icon();
iconTop.addClasses(['fa', 'fa-bars', 'pointer', 'nav-toggler', 's']);
iconContainer.addView(iconTop);

var title = new Label();
title.setText("Activity");
actionBar.addView(title);

var menuContainer = new Label();
menuContainer.addClasses(['float-right']);
actionBar.addView(menuContainer);

var body = new Div();
body.addClasses(['w3-row', 'b-c']);

var nav = new Div();
nav.addClasses(['w3-col', 'm2', 'w3-border-right', 'pt-20','pl-15', 'leftNav', 'pr-15', 'w3-animate-left']);
body.addView(nav);

var major = new Div();
major.addClasses(['w3-col', 'm10', 'w3-row']);
body.addView(major);

var mainContent =  new Div();
mainContent.addClasses(['w3-col', 'm10', 'scroll-y']);
mainContent.view.innerHTML = '&nbsp;';
major.addView(mainContent);

var right = new Div();
right.addClasses(['w3-col', 'm2', 'w3-border-left']);
major.addView(right);

document.body.appendChild(body.view);
body.view.style.height = (window.innerHeight - actionBar.view.clientHeight - 1)+"px";
nav.view.style.height = (window.innerHeight - actionBar.view.clientHeight - 2)+"px";
right.view.style.height = (window.innerHeight - actionBar.view.clientHeight - 2)+"px";
mainContent.view.style.height = (window.innerHeight - actionBar.view.clientHeight - 2)+"px";

function setDisplayAsUp(b) {
	if (b) {
		iconContainer.view.style.display = 'inline-block';
	}
	else{
		iconContainer.view.style.display = 'none';
	}
}

function setTitle(str) {
	title.setText(str);
}

function inflateMenu(container, resource) {
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

			var outside = [];
			var inside = [];

			for(var row of response){
				if(row.showIfRoom != undefined){
					outside.push(row);
				}
				else{
					inside.push(row);
				}
			}

			if(outside.length > 0){
				for(var menu of outside){
					if (menu.icon != undefined) {
						var icon = new Icon();
						icon.addClasses(menu.icon.split(" "));
						icon.addClasses(['mr-15', 'ml-15', 'menuB'])
						icon.setAttribute('data', menu.id);
						container.addView(icon);
					}
					else{
						var label = new Label();
						label.setText(menu.title);
						label.setAttribute('data', menu.id);
						container.addView(label);
					}
				}
			}

			if (inside.length > 0) {
				var more = new Icon();
				more.addClasses(['fa', 'fa-ellipsis-h']);
				more.addClasses(['mr-15', 'ml-15', 'menuB'])
				container.addView(more);

				var container2 = new Div();
				container2.addClasses(['menuContainer', 'w3-white']);
				document.body.appendChild(container2.view)

				for(var menu of inside){
					var dic = new Div();
					dic.addClasses(['w3-padding', 'pointer','w3-hover-light-grey', 'menuB']);
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

				more.view.addEventListener('click', function(event) {
					event.stopPropagation();
					container2.view.style.display = 'block';
					container2.view.style.top = (actionBar.view.clientHeight - 3)+"px"
				})
			}
		}
		catch(E){
			alert(E.toString()+response);
		}
	})
}

function inflateDrawer(resource) {
	$.get(resource, function(response, status) {
		try{
			var first = null;
			for(var menu of response){
				var dic = new Button();
				if (first == null) {
					first = dic;
				}
				//dic.addClasses(['w3-padding', 'pointer','w3-hover-light-grey', 'menuB']);
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

				nav.addView(dic);
			}
			if (first != null) {
				first.view.click();
			}

		}
		catch(E){
			alert(E.toString());
		}
	})
}

$(document).on('click', function(event) {
	$('.menuContainer').hide();
});

$(document).on('click', '.menuB', function() {
	try{
		optionsMenuClicked($(this).attr('data'));
	}
	catch(E){}
});

$(document).on('click', '.leftNav button', function(){
	var id = $(this).attr('data');
	
	//do the action
	$('.leftNav button').removeClass('active');
	$(this).addClass('active');

	try{
		navigationButtonClicked(id);
	}
	catch(E){}
});

iconTop.view.addEventListener('click', function(event) {
	if ($(iconTop.view).hasClass('s')) {
		$(nav.view).hide();
		$(major.view).removeClass('m10').addClass('m12');
		$(iconTop.view).removeClass('s')
	}
	else{
		$(major.view).removeClass('m12').addClass('m10');
		$(nav.view).show();	
		$(iconTop.view).addClass('s')
	}
});

function setContentView(view) {
	mainContent.removeAllViews();
	mainContent.addView(view);
}