
/**
 * add a framework repeater
 *
 * @param title of the toast
 * @param text what text to show in the toast
 *
 * @return void
 */
rs.toast = function (title, text, type = "primary") {

	var numToasts = $('.toast').length
	

    var toastHTML = ' <div class="toast toast-' + type + '"><strong>' + title + '</strong><br />' + text + '</div>';
	var toast = jQuery(toastHTML)
	
    jQuery(toast).hide();
	jQuery(toast).css("margin-top",numToasts * 100);
    jQuery('#main').append(toast)
    jQuery(toast).show().css("opacity","1");
	window.scrollTo({top: 0, behavior: 'smooth'});
	
    window.setTimeout(function () {
        jQuery('.toast').fadeOut(function () {
           jQuery(this).remove();
        });
    },7000)

}


rs.throwSuccess = function(title, text) {
	rs.toast(title, text, "success");
}

rs.throwError = function(title, text) {
	rs.toast(title, text, "danger");
}

rs.throwNotice = function(title, text) {
	rs.toast(title, text, "notice");
}


rs.throwConfirmation = function(c){

	var d = { 
		success: function(){}, 
		close: function(){}, 
		message: "Are you sure you want to perform this action", 
	};
		
	jQuery.extend(d,c);
	
	let r = Math.random().toString(36).substring(11);
	
	var	e = jQuery(`
	<div id="` + r + `" class="modal" tabindex="-1" role="dialog">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title">Confirm</h5>
			<button type="button" class="close btn-modal-close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<p>` + d['message'] + `</p>
		  </div>
		  <div class="modal-footer">
			<button id="y" type="button" class="btn btn-primary btn-yes">Yes</button>
			<button type="button" class="btn btn-secondary btn-no" data-dismiss="modal">Close</button>
		  </div>
		</div>
	  </div>
	</div>
	`);

	jQuery("body").prepend(e);	

	jQuery(e).modal('show'); 
	

	jQuery(e).find(".btn-yes").click(function(){		
		d['success']();
		jQuery(e).modal('show');
		jQuery(e).remove();		
	});



	jQuery(e).find(".btn-no").click(function(){
		d['close']();
		jQuery(e).modal('hide');
		jQuery(e).remove();
	});	
		
}