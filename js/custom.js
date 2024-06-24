(function($){

	$('.navbar-nav li a').each(function() {
		if (this.href.indexOf(location.pathname) > -1 && location.pathname.split("/").pop() != "" ) {
			$(this).parents('li').addClass('active');
		}
	});

	createDatetimePicker();

	var $win = $(window);
	var windowsize = $win.width();
	var wHeight = $win.outerHeight();
	// Hide Header on on scroll down
	var didScroll;
	var lastScrollTop = 0;
	var delta = 5;
	var navbarHeight = $('header').outerHeight();

	$(window).scroll(function(event){
		didScroll = true;
	});

	setInterval(function() {
		if (didScroll) {
			hasScrolled();
			didScroll = false;
		}
	}, 250);

	function hasScrolled() {
		var st = $(this).scrollTop();
		
		if (Math.abs(lastScrollTop - st) <= delta) return;
		
		if (st > lastScrollTop && st > navbarHeight) {
			// Scroll Down
			$('header').removeClass('header-down').addClass('header-up');
		} else {
			// Scroll Up
			if (st + wHeight < $(document).height()) {
				$('header').removeClass('header-up').addClass('header-down');
			}

			if ( st <= 87 ) {
				$('header').removeClass('header-down')
			}
		}
		lastScrollTop = st;
	}

	function containerHeight() {
		var othersHeight = $('header').outerHeight() + $('.page-head').outerHeight() + $('.footer').outerHeight();
		wHeight = (wHeight - 36) - othersHeight;
		
		$('.page-head ~ section.container-fluid > div').css('min-height',wHeight);
	}
	containerHeight();
	$(window).resize(containerHeight);

	$('.uploadfile').each(function(){
		$(this).on('change', function(){
			$(this).next().find('.form-control').val($(this).val().split(/[\\|/]/).pop());
		})
	});

	$('select').on('change', function(e) {
		e.preventDefault();
		$(this).blur();
		//console.info("change");
		$(this).css('color', '#464a4c');
	});
	$('select').on("focusout", function(e){
		//console.info("focusout");
		return $(this).css('color', '#464a4c');
	});
	//$('select').change();

	$('.navbar-collapse').on('show.bs.collapse', function () {
		$('#header').addClass('fixed-mb-nav');
		$('body').addClass('hidden');
	});
	$('.navbar-collapse').on('hide.bs.collapse', function () {
		$('#header').removeClass('fixed-mb-nav');
		$('body').removeClass('hidden');
	});
	
	$(window).resize(function(){
		if( windowsize > 991 ){
			$('#header').removeClass('fixed-mb-nav');
			$('body').removeClass('hidden');
		}
	});


})(jQuery);
function createDatetimePicker() {
	$('.datetimepicker-input').datetimepicker({
		//format: 'DD-MM-YYYY',
		useCurrent: false,
		allowInputToggle : true,
		icons: {
			up: "up-arrow",
			down: "down-arrow",
			next: 'right-arrow',
			previous: 'left-arrow',
			time: 'clock',
			date: 'flat-date'
		},
		ignoreReadonly : true,
		/*widgetPositioning: {
			horizontal: "left",
			vertical: "auto"
		},*/
		//debug: true
	})
	
	$('.form-control.datetimepicker-input').attr('readonly', 'readonly');
	// Add field value via JavaScript
	$('.form-control.datetimepicker-input').click(function() {
		$(this).siblings().click();
	});
}

function create_datatable(ajaxurl){
	sFilter = getFilter();
	oTable = $('#dtList').dataTable({
		/*responsive: true,
		pageLength: ilength,
		displayStart: istart,
		processing: true,
		lengthChange: true,
		serverSide: true,
		asSorting: [ 'asc', 'desc' ],
		pagingType: 'simple_numbers',
		searching: false,
		autoWidth: false,
		search: {"sSearch": sFilter},*/
		responsive: true,
		"iDisplayLength": ilength,
		"iDisplayStart": istart,
		"bServerSide": true,
		"bProcessing": true,
		"bLengthChange": true,
		"asSorting": [ 'asc', 'desc' ],
		"sPaginationType": "full_numbers",
		"bFilter": false,
		"bAutoWidth": false,
		"oSearch": {"sSearch": sFilter},
		ajax: ajaxurl,
		fnDrawCallback: drawCallback
	});
}
$("html").easeScroll({stepSize:200});