var preview = false;
var showEdit = false;
var hideTimeout;

$(function() {

	//initialize drag and drop for modules
	initDrag();

	//add border to regions
	$('*[role="region"]').css('border', '2px dashed #000').css('padding', '5px');

	//setup modal links
	$('a[rel="sparky_modal"]').live('click', function(e) {
		e.preventDefault();
		buildModal($(this));
	});

	//close the modal
	$('.sparky_modal_close').live('click', function(e) {
		e.preventDefault();
		removeModal();
	});

	//modal navigation
	$('.sparky_modal .modal_nav').live('click', function(e) {
		e.preventDefault();
		var element = $(this);

		// var section = $(this).attr('rel');

		// $('[class^="section_"]').each(function() {
		// 	$(this).hide();
		// });

		// $('.section_'+section).show();
		var url = element.attr('href');

		$.get(url, function(data) {
			$('.sparky_modal div.body').html(data);
			$('.sparky_modal a.active').each(function() {
				$(this).removeClass('active');
			});
			element.addClass('active');
			CKEDITOR.replace('ckeditor');
		});
	});

	//submit the modal form through ajax
	$('#modal_form').live('submit', function(e) {
		e.preventDefault();
		var url = $(this).attr('action');
		$.post(url, $(this).serialize(), function(data) {
			removeModal();
			updateModules();
		});
	});

	//delete module
	$('.sparky_module_delete a').live('click', function(e) {
		e.preventDefault();

		if (confirm('You are about to delete this module, and all the data contained within. Are you sure you want to continue?')) {

			var moduleID = $(this).data('module-id');

			$.post('/backend/deleteModule', { module_id: moduleID }, function(data) {
				updateModules();
			});
		}
	});

	//create elfinder file browser
	$('.file_browser').on('click', function(e) {
		e.preventDefault();

		if ( ! $(this).hasClass('disabled'))
			createElfinder();
	});

	//disable admin styles when preview button is clicked
	$('.preview_page').on('click', function(e) {
		e.preventDefault();

		if (preview === false) {
			removeAdminStyles();
			preview = true;
		} else if (preview === true) {
			restoreAdminStyles();
			preview = false;
		}
	});

	$('.sparky_module_edit').hover(function() {
		editHovered = true;
	},
	function() {
		editHovered = false;
	});
});

//update the modules on the page with their new content
var updateModules = function() {
	$('*[role="region"]').each(function() {
		var region = $(this);
		$.post('/backend/refreshModule/', { region_id: $(this).data('real-id') }, function(data) {
			//destroyDrag();
			region.html('');
			region.html(data);
			initDrag();
			//init();
		});
	});
};

//destroy the draggable areas
var destroyDrag = function() {
	$('[class^="sparky_module_container_"]').draggable('destroy');
	$('.drop_area').droppable('destroy');
}

//initialize draggable areas
var initDrag = function() {
	$('.module').draggable({
		revert: true
	});

	$('[class^="sparky_module_container_"]').draggable({
		revert: true,
		helper: 'clone',
		appendTo: 'body',
		start: function(event, ui) {
			var curWidth = $(this).width();
			var curHeight = $(this).height();
			$(ui.helper).css('height', curHeight);
			$(ui.helper).css('width', curWidth);
			$(ui.helper).css('background', '#FFF');
			$(ui.helper).css('opacity', '0.5');
		}
	});

	$('.drop_area').droppable({
		over: function( event, ui ) {
			$(this).addClass('drop_area_hover');
		},
		out: function( event, ui ) {
			$(this).removeClass('drop_area_hover');
		},
		drop: function( event, ui ) {
			$(this).removeClass('drop_area_hover');

			var cssClass = $(ui.draggable).attr('class');

			if (cssClass.indexOf('sparky_module_container_') != -1) {
				var moduleID = $(ui.draggable).data('module-id');
				var regionID = $(this).parent().data('real-id');

				$.post('/backend/updateModule', { region_id: regionID, module_id: moduleID }, function(data) {
					updateModules();
				});
			} else {
				var moduleType = $(ui.draggable).data('type');
				var regionID = $(this).parent().data('real-id');

				$.post('/backend/updateModule', { region_id: regionID, module_type: moduleType }, function(data) {
					updateModules();
				});
			}
		},
		tolerance: 'pointer'
	});
};


//remove modal box
var removeModal = function () {
	$('.sparky_modal_overlay').remove();
	$('.sparky_modal').remove();
	resetCKEDITOR();
}

var resetCKEDITOR = function() {
	/**
	* Remove all instances of CKEDITOR
	* to prevent CKEDITOR modal bug
	**/
	for (instance in CKEDITOR.instances)
	{
		delete CKEDITOR.instances[instance];
	}
}

//create elfinder
var createElfinder = function() {
	$('<div />').dialogelfinder({
		url: 'backend/elfinder',
		lang: 'en',
		resizable: false
	});
}

//remove admin styles for modules
var removeAdminStyles = function() {
	$('#module_bar').hide();
	$('.sparky_module_header').hide();
	$('.drop_area').hide();
	$('*[role="region"]').css('border', 'none').css('padding', '0');
	$('#admin_bar ul li a').addClass('disabled');
	$('.preview_page').removeClass('disabled');
	$('.preview_page').text('Edit Page');
}

//restore admin styles for modules
var restoreAdminStyles = function() {
	$('#module_bar').show();
	$('.sparky_module_header').show();
	$('.drop_area').show();
	$('*[role="region"]').css('border', '2px dashed #000').css('padding', '5px');
	$('.preview_page').text('Preview Page');
	$('#admin_bar ul li a').removeClass('disabled');
}

var hideModuleEdit = function() {
	if ( ! editHovered)
		$('.sparky_module_edit').hide();
}

var buildModal = function(moduleLink) {

	var html = '<header> \
	                <h1>{{module_name}} Module</h1> \
	                <div class="sparky_modal_close"><a href="#">close</a></div> \
	            </header> \
	            <nav> \
	                <ul> \
	                    <li><a class="modal_nav active" href="/m,{{module_name}},{{module_id}}/edit">Edit</a></li> \
	                </ul> \
	            </nav> \
	            <form method="post" action="/m,{{module_name}},{{module_id}}/save" id="modal_form"> \
	                <div class="body"> \
	                {{content}} \
	                </div> \
	                <input type="hidden" value="{{module_id}}" name="module_id"> \
	                <div class="buttons"> \
	                    <input type="submit" value="Save"> \
	                </div> \
	            </form>';

	var moduleName = moduleLink.parent().parent().find('.sparky_module_header_name').html();
	var moduleID = moduleLink.parent().parent().parent().data('module-id');

	console.log(moduleName);
	console.log(moduleID);

	//if the modal link is not disabled
	if ( ! moduleLink.hasClass('disabled')) {
		var url = moduleLink.attr('href');
		var modalContent;

		//get the content for the URL requested and put it in the modal
		$.get(url, function(data) {
			
			html = html.replace(/\{\{content\}\}/g, data, html);
			html = html.replace(/\{\{module_name\}\}/g, moduleName, html);
			html = html.replace(/\{\{module_id\}\}/g, moduleID, html);

			$('body').append(
				$('<div />').attr('class', 'sparky_modal_overlay')
			).append(
				$('<div />').attr('class', 'sparky_modal').append(html)
			);
			CKEDITOR.replace('ckeditor'); //call our ckeditor
		}).done(function() {
			$(document).trigger('modalReady'); //trigger the modal ready function to call any javascript within the requested modal content
		});
	}
}