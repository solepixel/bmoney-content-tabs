/*
Project: ContentTabs
Author: Brian DiChiara
Version: 1.0
Usage: Activates Tabbed navigation
*/


(function( $ ) {
	
	$.fn.contenttabs = function(opts) {
		
		var activated = false;
		
		$(':first', this).addClass('active');
		
		var options = {
			parent_selector : 'ul',
			link_active_class : 'active',
			on_tab_change: function(){}
		}
	
		return this.each(function(){
			$link = $(this);
			
			init($link, opts);
		});
		
		function init($link, opts){
			_setopts(opts);
			
			$link.click(function(){
				var href = $link.attr('href').substr($link.attr('href').indexOf('#'), $link.attr('href').length);
				$target = $(href);
				var css_class = $target.attr('class');
				$('.'+css_class).hide();
				$target.show();
				
				$link.parents(options.parent_selector+':first').find('a').removeClass(options.link_active_class);
				$link.addClass(options.link_active_class);
				
				options.on_tab_change($link);
				return false;
			});
			
			if(!activated){
				$link.parents(options.parent_selector+':first').find('a:first').addClass(options.link_active_class).click();
				activated = true;
			}
		}
		
		
		function _setopts(opts){
			if(typeof(opts) == 'object'){
				for(var opt in opts){
					options[opt] = opts[opt];
				}
			}
		}
		
	} // end plugin
})( jQuery );