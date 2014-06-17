jQuery(document).ready(function($) {
	if ( 'undefined' == typeof adminpage ) {
		return;
	}

	if ( ( adminpage == 'post-php' || adminpage == 'post-new-php' ) && typenow == 'post' ) {
		portfolioThemeAdminPointerHelpers($);
		portfolioThemePostOptionsSetup($);
	}

	if ( ( adminpage == 'post-php' || adminpage == 'post-new-php' ) && typenow == 'page' ) {
		portfolioThemePageOptionsSetup($);
	}
});

function portfolioThemeAdminPointerHelpers($) {
	$('html').click(function() {
		$('.ttf-pointer').fadeOut();
	});

	$('body').on('click', '.ttf-pointer', function() {
		$('.ttf-pointer').click(function(event){
			event.stopPropagation();
		});
	});

	$('body').on('click', '.ttf-pointer .close', function(){
		$(this).hide();
	});
}

function portfolioThemePostOptionsSetup($) {
	var standardOnlyElements = $( '#portfolio-options' );
	var portfolioOnlyElements = $( '#portfolio-item-meta' );

	// If the post isn't marked as standard, hide the options related to standard posts
	if ( ! $( '#post-formats-select #post-format-0:checked' ).length ) {
		standardOnlyElements.hide();
	}

	// If the post isn't marked as a portfolio item, hide the options related to portfolio items
	if ( ! $( '#portfolio_portfolio_item:checked' ).length ) {
		portfolioOnlyElements.hide();
	}

	$( '#post-formats-select #post-format-0' ).click(function() {
		standardOnlyElements.show();
	});

	$( '#post-formats-select .post-format' ).not( '#post-format-0' ).click(function() {
		standardOnlyElements.hide();
	});

	$( '#portfolio_portfolio_item' ).click(function() {
		if ( $(this).is( ":checked" ) ) {
			portfolioOnlyElements.show();
		} else {
			portfolioOnlyElements.hide();
		}
	});
}

function portfolioThemePageOptionsSetup($) {
	if ( $( '#pageparentdiv #page_template' ).val() != 'portfolio.php' ) {
		$( '#portfolio_page_option_section' ).hide();
	}

	$( '#pageparentdiv #page_template' ).change(function(){
		if ( $( '#pageparentdiv #page_template' ).val() != 'portfolio.php' ) {
			$( '#portfolio_page_option_section' ).hide();
		} else {
			$( '#portfolio_page_option_section' ).show();
		}
	});

	$('#checked-page-items').sortable({
		receive: function(e, ui) {
			$('#checked-page-items li input').prop("checked", true);
			portfolioThemeAdjustHelpMessages($);
		},
		helper: 'clone'
	});

	$('#unchecked-page-items').sortable({
		receive: function(e, ui) {
			$('#unchecked-page-items li input').prop("checked", false);
			portfolioThemeAdjustHelpMessages($);
		},
		helper: 'clone'
	});

	$('#checked-page-items').sortable('option', 'connectWith', '#unchecked-page-items');
	$('#unchecked-page-items').sortable('option', 'connectWith', '#checked-page-items');

	$('form#post').submit(function() {
		var value = $('#checked-page-items').sortable('serialize');
		var input = $("<input>").attr("type", "hidden").attr("name", portfolio_page_items_meta_key).val(value);
		$('form#post').append(input);
	});
	$('#unchecked-page-items').on('click', 'li input', function(){
		$(this).parent().prependTo('#checked-page-items');
	});
	$('#checked-page-items').on('click', 'li input', function(){
		$(this).parent().prependTo('#unchecked-page-items');
	});
	$('#checked-page-items, #unchecked-page-items').on('click', 'li input', function(){
		portfolioThemeAdjustHelpMessages($);
	});
	portfolioThemeAdjustHelpMessages($);
}

function portfolioThemeAdjustHelpMessages($) {
	var checkedIsEmpty = ($('#checked-page-items li').length == 0);
	$('#drag-items-here-to-add').toggle(checkedIsEmpty);
	$('#checked-page-items').toggleClass('empty', checkedIsEmpty);
	$('.no-page-items').toggle(checkedIsEmpty);
	$('#yes-on-page-note').toggle(! checkedIsEmpty);

	var uncheckedIsEmpty = ($('#unchecked-page-items li').length == 0);
	$('#drag-items-here-to-remove').toggle(uncheckedIsEmpty);
	$('#unchecked-page-items').toggleClass('empty', uncheckedIsEmpty);
	$('#not-on-page-note').toggle(! uncheckedIsEmpty);

	$('.no-items-at-all-section').toggle(checkedIsEmpty && uncheckedIsEmpty);
	$('.yes-on-page-section, .not-on-page-section').toggle(! checkedIsEmpty || ! uncheckedIsEmpty);
}
;
