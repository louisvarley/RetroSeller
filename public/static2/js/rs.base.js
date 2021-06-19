var rs = {};

rs.repeaters = {};

rs.urlParams = new URLSearchParams(window.location.search);

/**
 * add an framework init
 *
 * @param name used as reference
 * @param func function to run on init
 * @param controller only execute if this is the controller
 * @param action only execute if this is the action
 *
 * @return void
 */
rs.init = function(name, func, controller = null, action = null) {
	
	if(
		(controller == null || controller == rs.controller() || (rs.controller() == undefined && controller == "index")) && 
		(action == null || action == rs.action() || (rs.action() == undefined && action == "index"))
	){

		jQuery(document).ready(function () {
			func();
		})
	}

}

rs.controller = function(){
	return controller
}

rs.action = function(){
	return action;
}

rs.id = function(){
	return id;
}


/**
 * add a framework repeater
 *
 * @param name used as reference
 * @param interval how often to run in ms
 * @param func function to run on repeat
 * @param initial should this run straight away
 *
 * @return void
 */
rs.repeat = function(name, interval, func, initial) {

    if (initial) {func()}

    rs.repeaters[name] = (setTimeout(function () {

        func();
        rs.repeat(name,interval,func,false)

    }, interval));

    return name;

}

rs.isJson = function(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}


/* Every A Link with a data attribute of confirm will require a confirmation to proceed to the link */
jQuery(document).ready(function(){

	jQuery('a').each(function(){
		
		if (typeof jQuery(this).data('confirmation') !== 'undefined') {
		
			jQuery(this).click(function(e){
				
				var href = this.href;
				var message = jQuery(this).data('confirmation')
				
				e.preventDefault();

				rs.throwConfirmation({
					'message': message,
					'success': function(){
						location.href = href;
					}
				
				})

				return false;
			
			});
		
		}
		
	})
	
})

rs.init("datatables", function(){
	
	jQuery('.list-table').dataTable({
		"searching": true,
	});
	
});

rs.init("colorpicker", function(){
	jQuery('.colorpicker').minicolors();
});


rs.init("subtable", function(){
	jQuery('.btn-sub-table').click(function(){
		
		T = $("tr[data-id='" + $(this).data("id") +"']");
		
		if(T.is(":visible")){
			jQuery(this).html('<i class="fas fa-plus"></i>');
			T.hide();
		}else{
			jQuery(this).html('<i class="fas fa-minus"></i>');
			T.show();			
		}
		
	});
});


rs.init("tooltips", function(){	

jQuery('[data-tooltip]').each(function(){
	
	var t = jQuery(this).data("tooltip");
	jQuery(this).prop("title", t);
	jQuery(this).tooltip();

	
});




	
});
