WebFontConfig = {
    google: { families: [ 'Open+Sans::latin' ] }
  };
  (function() {
    var wf = document.createElement('script');
    wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
      '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
    wf.type = 'text/javascript';
    wf.async = 'true';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(wf, s);
  })();

 /**
 * The main Mobile.Nav script 
 */
jQuery(document).ready(function( $ ) {


	var bar = $('#sdrn_bar'), //top bar that shows/hides the menu
		bar_height = bar.outerHeight(true), //the bar height
		from_width = bar.attr('data-from_width'),
		menu = $('#sdrn_menu'), //the menu div
		menu_ul = $('#sdrn_menu_ul'), //the menu ul
		menu_a = menu.find('a'), //single menu link
		body = $('body'),
		html = $('html'),
		animation_speed = 300,
		ab = $('#wpadminbar'),
		menu_enabled = (bar.length > 0 && menu.length > 0)? true : false,
		menu_width = menu.width(),
		expand_sub_with_parent = menu.attr('data-expand_sub_with_parent'),
		target_height = (window.innerHeight < body.height())? body.height() : window.innerHeight,
		target_width = (window.innerWidth < body.width())? body.width() : window.innerWidth,
		fixed_elements = [],
		fixed_elements_were_adjusted = false;


	//detecting browser
	var nAgt = navigator.userAgent,
		chrome = false,
		mozilla = false;
		//Safari,Opera,MSIE(ie<11),Trident(IE 11),Safari[or]AppleWebkit
    if(nAgt.indexOf("Chrome") != -1) chrome = true;
    if(nAgt.indexOf("Firefox") != -1) mozilla = true;


    html.addClass('sdrn_'+Detect({ useUA: true }));

    function is_visible(element) {
    	if(element.css("display") == 'none') {
    		return false;
    	}
    	return true;
    }




	if(menu_enabled) {


		menu_ul.find('li').first().css({'border-top':'none'});


		/**
		 * Close menu on click outside
		 */
		$(document).mouseup(function (e) {
    		if (!menu.is(e.target) && menu.has(e.target).length === 0) {
        		if(is_visible(menu)) {

    				if(menu.hasClass('sdrn_jquery')) {
    					close_sub_uls();
    					$.sidr('close', 'sdrn_menu');
    					bar.removeClass('menu_is_opened');
						body.removeClass('menu_is_opened');
			        }
			        if(menu.hasClass('sdrn_css3')) {
			          	close_sub_uls();
						bar.removeClass('menu_is_opened');
						body.removeClass('menu_is_opened');
						$('.fixed_animation').removeClass('fixed_animation_moved');
			        }
    			}
    		}
		});





		//ENABLE NESTING

		//add arrow element to the parent li items and chide its child uls
		menu.find('ul.sub-menu').each(function() {
			var sub_ul = $(this),
				parent_a = sub_ul.prev('a'),
				parent_li = parent_a.parent('li').first();

			parent_a.addClass('sdrn_parent_item');
			parent_li.addClass('sdrn_parent_item_li');

			var expand = parent_a.before('<span class="sdrn_icon sdrn_icon_par icon_default '+menu.attr('data-custom_icon')+'"></span> ').find('.sdrn_icon_par');

			sub_ul.hide();
		});
		if(menu.hasClass('sdrn_custom_icons')) $('#sdrn_menu span.sdrn_icon').removeClass('icon_default');


		//MAYBE APPLY CUSTOM ICONS FOR ITEMS

		menu.find('#sdrn_menu_ul a[data-cii=1]').each(function() {
			var t = $(this),
				fa_icon = t.attr('data-fa_icon'),
				icon_color = t.attr('data-icon_color'),
				icon_container = t.find('div').first(),
				icon_src = t.attr('data-icon_src');

			//enable fontawesome icon
			if(!icon_src) {
				icon_container.addClass('sdrn_item_custom_icon');
				icon_container.addClass('sdrn_item_custom_icon_fa');
				icon_container.addClass(fa_icon);
				icon_container.css({'color': icon_color});
			}

			//enable image icon
			if(icon_src) {
				icon_container.addClass('sdrn_item_custom_icon');
				icon_container.html('<img src="'+icon_src+'" width="23" height="23" alt="cii">');
			}
		});




		//adjust the a width on parent uls so iyt fits nicely with th eicon elemnt
		function adjust_expandable_items() {
			$('.sdrn_parent_item_li').each(function() {
				var t = $(this),
					main_ul_width = 0,
					icon = t.find('.sdrn_icon_par').first(),
					link = t.find('a.sdrn_parent_item').first();

				if(menu.hasClass('top')) {
					main_ul_width = window.innerWidth;
				} else {
					main_ul_width = menu_ul.innerWidth();
				}

				if(t.find('.sdrn_clear').length == 0) link.after('<br class="sdrn_clear"/>');
			});
		}
		adjust_expandable_items();





		//expand / collapse action (SUBLEVELS)
		$('.sdrn_icon_par').on('click',function(e) {
			e.preventDefault();
			var t = $(this),
				child_ul = t.parent('li').find('ul.sub-menu').first();

			child_ul.slideToggle(300);
			t.toggleClass('sdrn_par_opened');
			if(menu.attr('data-custom_icon') != '') {
				t.toggleClass(menu.attr('data-custom_icon_open'));
				if(menu.attr('data-custom_icon_open') != '') t.toggleClass(menu.attr('data-custom_icon'));
			}
			t.parent('li').first().toggleClass('sdrn_no_border_bottom');
		});





		//helper - close all submenus when menu is hiding
		function close_sub_uls() {
			menu.find('ul.sub-menu').each(function() {
				var ul = $(this),
					icon = ul.parent('li').find('.sdrn_icon_par'),
					li = ul.parent('li');

				if(is_visible(ul)) ul.slideUp(300);
				icon.removeClass('sdrn_par_opened');
				icon.removeClass(menu.attr('data-custom_icon_open'));
				icon.addClass(menu.attr('data-custom_icon'));
				li.removeClass('sdrn_no_border_bottom');
			});
		}



		

		//fix the scaling issue by adding/replacing viewport metatag
		var mt = $('meta[name=viewport]');
		mt = mt.length ? mt : $('<meta name="viewport" />').appendTo('head');
		if(menu.attr('data-zooming') == 'no') {
			mt.attr('content', 'user-scalable=no, width=device-width, maximum-scale=1, minimum-scale=1');
		} else {
			mt.attr('content', 'user-scalable=yes, width=device-width, initial-scale=1.0, minimum-scale=1');
		}


		//Additional fixes on change device orientation
		if(mozilla) {
			screen.addEventListener("orientationchange", function() {updateOrientation()}); //firefox
		} else {
			window.addEventListener('orientationchange', updateOrientation, false);
		}
		function updateOrientation() {

			//trigger page scroll to hide the IOS browser UI
			window.scrollBy(1,1);
			window.scrollBy(-1,-1);

			//update widths for new orientation
			menu_width = menu.width();
			target_height = (window.innerHeight < body.height())? body.height() : window.innerHeight;
			target_width = (window.innerWidth < body.width())? body.width() : window.innerWidth;

			//top menu does not need adjustments

			//reset for left & right menu (if opened while changing orientation)
			if(body.hasClass('menu_is_opened')) {
				if(menu.hasClass('sdrn_jquery')) {
					close_sub_uls();
					//additional adjustment only for 'left' pos
					if(body.hasClass('sdrn_left')) {
						body.css({'left':menu_width});
						body.scrollLeft(0);
					}
					$.sidr('close', 'sdrn_menu');
					bar.removeClass('menu_is_opened');
					body.removeClass('menu_is_opened');
				}
				if(menu.hasClass('sdrn_css3')) {
		          	close_sub_uls();
					bar.removeClass('menu_is_opened');
					body.removeClass('menu_is_opened');
		        }
			}

		}



		function update_fixed_elements(fixed_elements) {
			//if fixed elements are on the page
			if(fixed_elements.length > 0) {
				//loop trogh them and
				$.each(fixed_elements, function(index, el) {
					//this adjustment is applied only once - at the begining
					if(fixed_elements_were_adjusted == false) {
						var el_top = (parseInt(el.css("top"),0) >= 0)? parseInt(el.css("top"),0) : 0,
							adminbar = $('#wpadminbar');

						//move fixed el down: ADMIN BAR - YES | MENU BAR - YES
						if(is_visible(adminbar) && is_visible(bar)) el.css({'top':el_top+42+adminbar.height()});
						
						//move fixed el down: ADMIN BAR - YES | MENU BAR - NO 
						if(is_visible(adminbar) && !is_visible(bar)) el.css({'top':el_top+adminbar.height()});
						
						//move fixed el down (by bar height): ADMIN BAR - NO | MENU BAR - YES
						if(!is_visible(adminbar) && is_visible(bar)) el.css({'top':el_top+42});
						
						//move fixed el down (by bar height): ADMIN BAR - NO | MENU BAR - NO
						if(!is_visible(adminbar) && !is_visible(bar)) el.css({'top':el_top});
						
						el.css({'z-index':900});	
						el.addClass('fixed_animation');
					}
				});
			}
			fixed_elements_were_adjusted = true;
		}




		function adjust_to_adminbar() {
			var adminbar = $('#wpadminbar');
			if(is_visible(adminbar)) {
				$('#sdrn_menu.left ul#sdrn_menu_ul, #sdrn_menu.right ul#sdrn_menu_ul').css({'padding-top': (42+adminbar.height())});
				$('#sdrn_bar').css({'top':adminbar.height()});
			}
		}








		//The left/right menu
		if(menu.hasClass('left') || menu.hasClass('right')) {


			//appy sidr only if animation == jquery 
			if(menu.hasClass('sdrn_jquery')) {
				var hor_pos = (menu.hasClass('left'))? 'left' : 'right';
				bar.sidr({
					name:'sdrn_menu',
					side: hor_pos,
					speed: animation_speed
				});
			}


			//adjust the bar % menu position according to the WP admin bar + bar top distance (if present)
			adjust_to_adminbar();
			

			//css animation setup
			if(menu.hasClass('sdrn_css3')) {
				menu.show();
				
				var wrapper = body.wrapInner('<div id="sdrn_wrapper">').find('#sdrn_wrapper');
				var inner_wrapper = wrapper.wrapInner('<div id="sdrn_wrapper_inner">').find('#sdrn_wrapper_inner');

				menu.insertBefore(wrapper);
				bar.insertBefore(wrapper);
				ab.insertBefore(wrapper);

				//move out fixed elements from the wrapper and append them to the body
				var x = inner_wrapper.find('> *').filter(function() { return $(this).css("position") === 'fixed'; }).each(function() {
					var t = $(this);
					body.append(t);
					fixed_elements.push(t);
				});
				update_fixed_elements(fixed_elements);

				//copy css background from <body> to wrapper and adjust the wrapper to window size if needed
				if(body.height() < $(window).height()) {
					body.height($(window).height());
					wrapper.height($(window).height());
				} 
				//if no background or if background color is transparent ends with ...0)
				(body.css('background') == '' || body.css('background-color').indexOf("0)") >= 0)? wrapper.css({'background':'#fff'}) : wrapper.css({'background':body.css('background')});			}



			//if menu is open  - on bar click: close sub ul-s
			bar.on('click', function() {
				close_sub_uls();
				bar.toggleClass('_opened_'); //the normal toggleClass did not work so we are using th e addintional _opened_ class to indicate if we should show or hide the menu.
				if(bar.hasClass('_opened_')) {
					bar.addClass('menu_is_opened');
					body.addClass('menu_is_opened');
					$('.fixed_animation').addClass('fixed_animation_moved');
				} else {
					bar.removeClass('menu_is_opened');
					body.removeClass('menu_is_opened');
					$('.fixed_animation').removeClass('fixed_animation_moved');
				}
			});


			//when link is clicked - hide the menu first and then change location to new page
			menu_a.on('click', function(e) {
				e.preventDefault();

				if(expand_sub_with_parent == 'yes' && $(this).parent().hasClass('sdrn_parent_item_li')) {
					$(this).prev('span.sdrn_icon').trigger("click");
					return;
				}

				var url = $(this).attr('href');
				if(menu.hasClass('sdrn_jquery')) $.sidr('close', 'sdrn_menu');
				if(menu.hasClass('sdrn_css3')) {
					close_sub_uls();
					bar.removeClass('menu_is_opened');
					body.removeClass('menu_is_opened');
					$('.fixed_animation').removeClass('fixed_animation_moved');
				}
				setTimeout(function() {
					window.location.href = url;
				}, animation_speed);
			});



			if(menu.attr('data-swipe_actions') == 'yes') {
	    		$(window).touchwipe({
			        wipeLeft: function() {
			        	// Close if menu on left
			        	if(menu.hasClass('left')) close_leftright_menu();
			        	//open if menu on right
			        	if(menu.hasClass('right')) open_leftright_menu();
			        },
			        wipeRight: function() {
			        	// Open if menu on left
			        	if(menu.hasClass('left')) open_leftright_menu();
			        	// Close if menu on right
			        	if(menu.hasClass('right')) close_leftright_menu();
			        },
			        preventDefaultEvents: false
		      	});
		    }


	      	$(window).resize(function(){
	      		target_width = (window.innerWidth < body.width())? body.width() : window.innerWidth;

	      		//hide menu when resize window above trigger value
				if(target_width > from_width && is_visible(menu)) {
					close_sub_uls();
    				if(menu.hasClass('sdrn_jquery')) $.sidr('close', 'sdrn_menu');
    				if(menu.hasClass('sdrn_css3')) {
						bar.removeClass('menu_is_opened');
						body.removeClass('menu_is_opened');
						$('.fixed_animation').removeClass('fixed_animation_moved');
			        }
				}

				adjust_to_adminbar();

				//update position for fixed elements when resizing BELOW trigger value
				update_fixed_elements(fixed_elements);
			});


		} else if(menu.hasClass('top')) { //The top positioned menu


			body.prepend(menu);

			//adjust the bar % menu position according to the WP admin bar + bar top distance (if present)
			adjust_to_adminbar();

			//show / hide the menu
			bar.on('click', function(e) {

				//scroll window top
				$("html, body").animate({ scrollTop: 0 }, animation_speed);

				bar.toggleClass('_opened_'); //the normal toggleClass did not work so we are using th e addintional _opened_ class to indicate if we should show or hide the menu.
				if(bar.hasClass('_opened_')) {
					bar.addClass('menu_is_opened');
					body.addClass('menu_is_opened');
				} else {
					bar.removeClass('menu_is_opened');
					body.removeClass('menu_is_opened');
				}
 
				close_sub_uls();
				menu.stop(true, false).slideToggle(animation_speed);

			});


			//when link is clicked - hide the menu first and then change location to new page
			menu_a.on('click', function(e) {
				e.preventDefault();

				if(expand_sub_with_parent == 'yes' && $(this).parent().hasClass('sdrn_parent_item_li')) {
					console.log('s');
					$(this).prev('span.sdrn_icon_par').trigger("click");
					return;
				}

				var url = $(this).attr('href');

				menu.slideUp(animation_speed,function() {
					//go to the url from the link
					window.location.href = url;
				});
			});


			$(window).resize(function(){
	      		target_width = (window.innerWidth < body.width())? body.width() : window.innerWidth;
				if(target_width > from_width && is_visible(menu)) {
					close_sub_uls();
    				menu.slideUp(animation_speed, function() {});
				}
			});


		} //end if class left / top /right


		function close_leftright_menu() {
			if(menu.hasClass('sdrn_jquery')) {
				$.sidr('close', 'sdrn_menu');
				bar.removeClass('menu_is_opened');
				body.removeClass('menu_is_opened');
			}
			if(menu.hasClass('sdrn_css3')) {
				close_sub_uls();
				bar.removeClass('menu_is_opened');
				body.removeClass('menu_is_opened');
				$('.fixed_animation').removeClass('fixed_animation_moved');
			}
		}
		function open_leftright_menu() {
			if(menu.hasClass('sdrn_jquery')) {
				$.sidr('open', 'sdrn_menu');
				bar.addClass('menu_is_opened');
				body.addClass('menu_is_opened');
			}
				if(menu.hasClass('sdrn_css3')) {
				bar.addClass('menu_is_opened');
				body.addClass('menu_is_opened');
				$('.fixed_animation').addClass('fixed_animation_moved');
			}
		}

	} //end if menu enabled

});












/**
 * Device detection taken from Miguel Fonseca Github Gist.
 * Source: https://gist.github.com/mcsf/3803021
 * Author Miguel Fonseca (https://gist.github.com/mcsf)
 */
var Detect = function(options) {
    if (options === undefined) options = {};
 
    this.init = function() {
        this.mm = window.matchMedia;
 
        if (this.mm && !options.useUA) {
            this.method = 'media queries';
            this.type   = mq();
        }
        else {
            this.method = 'user agent strings';
            this.type   = ua();
        }
 
        return options.verbose ? [ this.type, this.method ] : this.type;
    };
 
 
    /**
     * Media queries
     */
 
    var mq = function() {
        if (minMatch(320) && maxMatch(480))
            return 'smartphone';
 
        if (minMatch(768) && maxMatch(1024))
            return 'tablet';
 
        return 'desktop';
    };
 
    var match = function(m, width) {
        return window.matchMedia(
                'screen and (' + m + '-device-width: '
                    + width + 'px)').matches;
    };
 
    var minMatch = function(width) {
        return match('min', width);
    };
 
    var maxMatch = function(width) {
        return match('max', width);
    };
 
 
    /**
     * User agent strings
     */
 
    var ua = function() {
        var uas = navigator.userAgent;
 
        tablets = [ /Android 3/i, /iPad/i ];
        for (i=0, l=tablets.length; i<l; i++)
            if (uas.match(tablets[i])) return 'tablet';
 
        smartphones = [
            /Mobile/i,
            /Android/i,
            /iPhone/i,
            /BlackBerry/i,
            /Windows Phone/i,
            /Windows Mobile/i,
            /Maemo/i,
            /PalmSource/i,
            /SymbianOS/i,
            /SymbOS/i,
            /Nokia/i,
            /MOT-/i,
            /JDME/i,
            /Series 60/i,
            /S60/i,
            /SonyEricsson/i,
        ];
        for (i=0, l=smartphones.length; i<l; i++)
            if (uas.match(smartphones[i])) return 'smartphone';
 
        return 'desktop';
    };
 
    return this.init();
};
