"use strict";
window.jQuery = window.$ = jQuery;

var header_min = 60,
    header_full = 240,
    set_wrapper_margin = 0,
    current_header = header_min,
    main_wrapper = jQuery('.main_wrapper'),
    main_wrapper_width = main_wrapper.width(),
    main_wrapper_right = parseInt(main_wrapper.css('padding-right')),
    main_wrapper_left = parseInt(main_wrapper.css('padding-left')),
    main_wrapper_top = parseInt(main_wrapper.css('padding-top')),
    main_wrapper_bottom = parseInt(main_wrapper.css('padding-bottom')),
    window_h = jQuery(window).height(),
    window_w = jQuery(window).width(),
    test_window_height = window_h - main_wrapper_top - main_wrapper_bottom,
    content_wrapper = jQuery('.content_wrapper'),
    content_wrapper_h = content_wrapper.height() + main_wrapper_top + main_wrapper_bottom,
    html = jQuery('html'),
    fs_thmb_viewport = jQuery('.fs_thmb_viewport'),
    fs_portfolio_post = jQuery('.fs_portfolio_post'),
    fs_portfolio_part = jQuery('.fs_portfolio_part'),
    header = jQuery('header.main_header'),
	nav = header.find('nav'),
	logo_wrapper = header.find('.logo_wrapper'),
	footer = header.find('.footer'),
    bodyPadding = 0;
if (document.body.clientWidth < '1200' && document.body.clientWidth > '960') {
    header_full = 220;
}

jQuery(document).ready(function ($) {
	"use strict";
	if (header.hasClass('menu-top') && (logo_wrapper.height() < header.height())) {
		logo_wrapper.css({'padding-top' : (header.height() - logo_wrapper.height())/2, 'padding-bottom' : (header.height() - logo_wrapper.height())/2});
	}
		
    jQuery('.header_toggler').click(function () {
        header.toggleClass('fullsize');
        header.toggleClass('short_header');
        html.toggleClass('fullsize_header');
        if (html.hasClass('fullsize_header')) {
            bodyPadding = 240;
        } else {
            bodyPadding = 60;
        }
        if (jQuery('.is_masonry').size() > 0) {
            setTimeout("jQuery('.is_masonry').masonry()", 300);
        }
		if (jQuery('.main_header').hasClass('menu-left')) {
			if (jQuery(window).width() < 1366 && jQuery(window).width() > 960) {
				if (jQuery('html').hasClass('fullsize_header')) {
					bodyPadding = 220;
				} else {
					bodyPadding = 60;
				}
			} else if (jQuery(window).width() < 960 && jQuery(window).width() > 760) {
				//vertical iPad
			} else if (jQuery(window).width() < 760 && jQuery(window).width() > 420) {
				//Horizontal iPhone
			} else if (jQuery(window).width() < 420) {
				//vertical iPhone
			}
		} else {
			bodyPadding = 0;
		}
		if (jQuery('.fs_portfolio_part').size() > 0) {
			setTimeout(function () {
				jQuery('.port-slider-wrapper').css('max-width', jQuery(window).width() - parseInt(jQuery('body').css('padding-left')) - parseInt(fs_portfolio_post.css('padding-left')) - parseInt(fs_portfolio_post.css('padding-right')) - parseInt(fs_portfolio_part.css('padding-left')) - parseInt(fs_portfolio_part.css('padding-right')) + 'px');
			}, 300);
		}
		if (jQuery('.fs_thmb_viewport').size() > 0) {
			jQuery('.fs_thmb_viewport').width(jQuery(window).width() - bodyPadding - jQuery('.fs_controls').width() - 20);
		}
		if (jQuery('.main_header').hasClass('menu-left')) {
			setTimeout("menu_fix(window_w, window_h, nav.height(), logo_wrapper.height(), footer.height(), header, html)",300);
		}
    });

    if (jQuery('.global_center_trigger').size() > 0) {
        if (content_wrapper_h < window_h) {
            main_wrapper.css('top', (window_h - content_wrapper_h) / 2 + 'px');
        } else {
            main_wrapper.css('top', '0');
        }
    } else {
        if (content_wrapper.height() < test_window_height) {
            content_wrapper.css('min-height', test_window_height + 'px');
        }
    }
    jQuery('.pagline_toggler').click(function () {
        jQuery(this).toggleClass('show-pag');
        jQuery('.fw_line').toggleClass('pag-hided');
    });
    if (jQuery('.flickr_badge_image').size() > 0) {
        jQuery('.flickr_badge_image a').each(function () {
            jQuery(this).append('<div class="flickr_fadder"/>');
        });
    }

    jQuery('.header_wrapper').append('<a href="javascript:void(0)" class="menu_toggler"></a>');
    header.append('<div class="mobile_menu_wrapper"><ul class="mobile_menu container"/></div>');
    jQuery('.mobile_menu').html(header.find('.menu').html());
    jQuery('.mobile_menu_wrapper').hide();
    jQuery('.menu_toggler').click(function () {
        jQuery('.mobile_menu_wrapper').slideToggle(300);
        jQuery('.main_header').toggleClass('opened');
    });
	if (jQuery('.main_header').hasClass('menu-left')) {
		menu_fix(window_w, window_h, nav.height(), logo_wrapper.height(), footer.height(), header, html);
	}
	
	// prettyPhoto
	jQuery("a[rel^='prettyPhoto'], .prettyPhoto").prettyPhoto();	
	
	jQuery('a[data-rel]').each(function() {
		$(this).attr('rel', $(this).data('rel'));
	});
	
	/* NivoSlider */
	jQuery('.nivoSlider').each(function(){
		jQuery(this).nivoSlider({
			directionNav: true,
			controlNav: false,
			effect:'fade',
			pauseTime:4000,
			slices: 1
		});
	});
	
	/* Accordion & toggle */
	$('.shortcode_accordion_item_title').click(function(){
		if (!$(this).hasClass('state-active')) {
			$(this).parents('.shortcode_accordion_shortcode').find('.shortcode_accordion_item_body').slideUp('fast');
			$(this).next().slideToggle('fast');
			$(this).parents('.shortcode_accordion_shortcode').find('.state-active').removeClass('state-active');
			$(this).addClass('state-active');
		}
	});
	$('.shortcode_toggles_item_title').click(function(){
		$(this).next().slideToggle('fast');
		$(this).toggleClass('state-active');
	});

	$('.shortcode_accordion_item_title.expanded_yes, .shortcode_toggles_item_title.expanded_yes').each(function( index ) {
		$(this).next().slideDown('fast');
		$(this).addClass('state-active');
	});
	
	/* Counter */
	if (jQuery(window).width() > 760) {
		jQuery('.shortcode_counter').waypoint(function(){							
			var set_count = $(this).find('.stat_count').attr('data-count');
			$(this).find('.stat_temp').stop().animate({width: set_count}, {duration: 3000, step: function(now) {
					var data = Math.floor(now);
					$(this).parents('.counter_wrapper').find('.stat_count').html(data);
				}
			});
			$(this).find('.stat_count');
		},{offset: 'bottom-in-view'});
	} else {
		jQuery('.shortcode_counter').each(function(){							
			var set_count = $(this).find('.stat_count').attr('data-count');
			$(this).find('.stat_temp').animate({width: set_count}, {duration: 3000, step: function(now) {
					var data = Math.floor(now);
					$(this).parents('.counter_wrapper').find('.stat_count').html(data);
				}
			});
			$(this).find('.stat_count');
		},{offset: 'bottom-in-view'});	
	}
	
	/* Tabs */
	$('.shortcode_tabs').each(function(index) {
		/* GET ALL HEADERS */
		var i = 1;
		$(this).find('.shortcode_tab_item_title').each(function(index) {
			$(this).addClass('it'+i); jQuery(this).attr('whatopen', 'body'+i);
			$(this).addClass('head'+i);
			$(this).parents('.shortcode_tabs').find('.all_heads_cont').append(this);
			i++;
		});

		/* GET ALL BODY */
		var i = 1;
		$(this).find('.shortcode_tab_item_body').each(function(index) {
			$(this).addClass('body'+i);
			$(this).addClass('it'+i);
			$(this).parents('.shortcode_tabs').find('.all_body_cont').append(this);
			i++;
		});

		/* OPEN ON START */
		$(this).find('.expand_yes').addClass('active');
		var whatopenOnStart = $(this).find('.expand_yes').attr('whatopen');
		$(this).find('.'+whatopenOnStart).addClass('active');
	});

	$(document).on('click', '.shortcode_tab_item_title', function(){
		$(this).parents('.shortcode_tabs').find('.shortcode_tab_item_body').removeClass('active');
		$(this).parents('.shortcode_tabs').find('.shortcode_tab_item_title').removeClass('active');
		var whatopen = $(this).attr('whatopen');
		$(this).parents('.shortcode_tabs').find('.'+whatopen).addClass('active');
		$(this).addClass('active');
	});
	
	/* Messagebox */
	jQuery('.shortcode_messagebox').find('.box_close').click(function(){
		jQuery(this).parents('.module_messageboxes').fadeOut(400);
	});
	
	/* Skills */
	jQuery('.chart').each(function(){
		jQuery(this).css({'font-size' : jQuery(this).parents('.skills_list').attr('data-fontsize'), 'line-height' : jQuery(this).parents('.skills_list').attr('data-size')});
		jQuery(this).find('span').css('font-size' , jQuery(this).parents('.skills_list').attr('data-fontsize'));
		jQuery(this).parent('.skill_item').css({'padding-left' : (parseInt(jQuery(this).parents('.skills_list').attr('data-size'))+15)+'px', 'min-height' : jQuery(this).parents('ul.skills_list').attr('data-size')});
	});

	if (jQuery(window).width() > 760) {
		jQuery('.skill_li').waypoint(function(){
			jQuery('.chart').each(function(){
				jQuery(this).easyPieChart({
					barColor: jQuery(this).parents('ul.skills_list').attr('data-color'),
					trackColor: jQuery(this).parents('ul.skills_list').attr('data-bg'),
					scaleColor: false,
					lineCap: 'square',
					lineWidth: parseInt(jQuery(this).parents('ul.skills_list').attr('data-width')),
					size: parseInt(jQuery(this).parents('ul.skills_list').attr('data-size')),
					animate: 1500
				});
			});
		},{offset: 'bottom-in-view'});
	} else {
		jQuery('.chart').each(function(){
			jQuery(this).easyPieChart({
				barColor: jQuery(this).parents('ul.skills_list').attr('data-color'),
				trackColor: jQuery(this).parents('ul.skills_list').attr('data-bg'),
				scaleColor: false,
				lineCap: 'square',
				lineWidth: parseInt(jQuery(this).parents('ul.skills_list').attr('data-width')),
				size: parseInt(jQuery(this).parents('ul.skills_list').attr('data-size')),
				animate: 1500
			});
		});
	}
	
	// contact form
	jQuery("#ajax-contact-form").submit(function() {
		var str = $(this).serialize();		
		$.ajax({
			type: "POST",
			url: "guest_book/dopost",
			data: str,
			success: function(msg) {
				// Message Sent - Show the 'Thank You' message and hide the form
				if(msg == 'OK') {
					var result = '<div class="notification_ok">Your message has been sent. Thank you!</div>';
					jQuery("#fields").hide();
				} else {
                    var result = '<div class="notification_ok">Your message has been sent. Thank you!</div>';
					jQuery("#fields").hide();
					//var result = msg;
				}
				jQuery('#note').html(result);
			}
		});
		return false;
	});
	
		
});

jQuery(window).load(function () {
	"use strict";
    window_h = jQuery(window).height();
    window_w = jQuery(window).width();
    test_window_height = window_h - main_wrapper_top - main_wrapper_bottom;
    content_wrapper_h = content_wrapper.height() + main_wrapper_top + main_wrapper_bottom;
    if (jQuery('.global_center_trigger').size() > 0) {
        if (content_wrapper_h < window_h) {
            main_wrapper.css('top', (window_h - content_wrapper_h) / 2 + 'px');
        } else {
            main_wrapper.css('top', '0');
        }
    } else {
        if (content_wrapper.height() < test_window_height) {
            content_wrapper.css('min-height', test_window_height + 'px');
        }
    }
	if (jQuery('.main_header').hasClass('menu-left')) {
		menu_fix(window_w, window_h, nav.height(), logo_wrapper.height(), footer.height(), header, html);
	}
	
});

jQuery(window).resize(function () {
	"use strict";
    main_wrapper_width = main_wrapper.width();
    main_wrapper_right = parseInt(main_wrapper.css('padding-right'));
    main_wrapper_left = parseInt(main_wrapper.css('padding-left'));
    main_wrapper_top = parseInt(main_wrapper.css('padding-top'));
    main_wrapper_bottom = parseInt(main_wrapper.css('padding-bottom'));
    window_h = jQuery(window).height();
    window_w = jQuery(window).width();
    test_window_height = window_h - main_wrapper_top - main_wrapper_bottom;
    content_wrapper_h = content_wrapper.height() + main_wrapper_top + main_wrapper_bottom;

    if (jQuery('.global_center_trigger').size() > 0) {
        if (content_wrapper_h < window_h) {
            main_wrapper.css('top', (window_h - content_wrapper_h) / 2 + 'px');
        } else {
            main_wrapper.css('top', '0');
        }
    } else {
        if (content_wrapper.height() < test_window_height) {
            content_wrapper.height(test_window_height);
        }
    }
	if (jQuery('.main_header').hasClass('menu-left')) {
		menu_fix(window_w, window_h, nav.height(), logo_wrapper.height(), footer.height(), header, html);
	}	
		
});

function menu_fix(winW, winH, navH, logoH, footerH, header_obj, html_obj) {
	"use strict";
	if (winW > 760) {
		if (winH < (navH + logoH + footerH + 36)) {
			header_obj.addClass('overflowed');
			if (html_obj.height() < winH) {
				header_obj.height(winH);
			} else {
				header_obj.height(html_obj.height());
			}
		} else {
			header_obj.removeClass('overflowed');
			header_obj.css('height', '100%');
		}	
	} else {
		header.removeClass('overflowed');
		header.css('height', '100%');
	}		
}