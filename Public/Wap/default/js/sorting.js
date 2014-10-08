/* SORTING */ 

jQuery(function(){
  "use strict";	
  var $container = $('.portfolio_sorting');

  $container.isotope({
	itemSelector : '.element'
  });
    
  var $optionSets = jQuery('.optionset'),
	  $optionLinks = $optionSets.find('a');

  $optionLinks.click(function(){
	var $this = jQuery(this);
	// don't proceed if already selected
	if ( $this.parent('li').hasClass('selected') ) {
	  return false;
	}
	var $optionSet = $this.parents('.optionset');
	$optionSet.find('.selected').removeClass('selected');
	$optionSet.find('.fltr_before').removeClass('fltr_before');
	$optionSet.find('.fltr_after').removeClass('fltr_after');
	$this.parent('li').addClass('selected');
	$this.parent('li').next('li').addClass('fltr_after');
	$this.parent('li').prev('li').addClass('fltr_before');

	// make option object dynamically, i.e. { filter: '.my-filter-class' }
	var options = {},
		key = $optionSet.attr('data-option-key'),
		value = $this.attr('data-option-value');
	// parse 'false' as false boolean
	value = value === 'false' ? false : value;
	options[ key ] = value;
	if ( key === 'layoutMode' && typeof changeLayoutMode === 'function' ) {
	  // changes in layout modes need extra logic
	  changeLayoutMode( $this, options )
	} else {
	  // otherwise, apply new options
	  $container.isotope(options);	  
	}
	setTimeout("jQuery('.portfolio_sorting').isotope('reLayout')", 500);
	return false;				
  });
  
   jQuery('.portfolio_sorting').find('img').load(function(){
		jQuery('.portfolio_sorting').isotope('reLayout');
   }); 
   	
});

jQuery(document).ready(function () {
	"use strict";
	jQuery('.header_toggler').click(function () {
		setTimeout("jQuery('.portfolio_sorting').isotope('reLayout')", 500);
	});	
});

jQuery(window).load(function(){
	"use strict";
	jQuery('.portfolio_sorting').isotope('reLayout');
	setTimeout("jQuery('.portfolio_sorting').isotope('reLayout')", 500);	
});

jQuery(window).resize(function(){
	"use strict";
	jQuery('.portfolio_sorting').isotope('reLayout');	
});
