rs.parentForm = null;

rs.removeElement = function(frm, name){
	var ctrl = $('[name='+name+']');  
	jQuery(ctrl).closest('.form-group').remove();
}

rs.hideElement = function(frm, name, disabled = true){
	var ctrl = $('[name='+name+']');  
	jQuery(ctrl).closest('.form-group').hide();
	if(disabled) jQuery(ctrl).prop("disabled", true)
}

rs.showElement = function(frm, name){
	var ctrl = $('[name='+name+']');  
	jQuery(ctrl).closest('.form-group').show();
	jQuery(ctrl).prop("disabled", false)	
}

rs.setElement = function(frm, name, value){

	data = {};
	data[name] = value;
	rs.populateForm(frm, data)
};

rs.triggerChange = function(frm){
	
	$("form#" + frm + " :input").each(function(){
		$(this).trigger("change");
	})
	
}

rs.init("form_setup",function(){
		
	$('.datepicker').datepicker({
		format: 'dd/mm/yyyy',
		autoclose: true,
	});

	
	/* Max Length */
	jQuery(document).ready(function(){
				
		
		rs.formSubmitLoading();
		
		jQuery('textarea[maxlength]').each(function(){

			var maxLabel = jQuery('<span>1 / 100</span>').insertAfter(jQuery(this));
			
			jQuery(this).on("propertychange input", function() {
				
				if (jQuery(this).val().length > jQuery(this).attr('maxlength')) {
					jQuery(this).val() = jQuery(this).val().substring(0, jQuery(this).attr('maxlength'));
				}  
				
				updateMaxLength(jQuery(this), maxLabel);
			})
			
			updateMaxLength(jQuery(this), maxLabel);

		})
		
		function updateMaxLength(textarea, span){

			length = jQuery(textarea).val().length
			max = jQuery(textarea).attr('maxlength');
			jQuery(span).html(length + "/" + max);
			
		}
		
	})

	/* Enable Readonly Padlock */
	jQuery(document).ready(function(){
		jQuery('input[type=text][readonly], select[readonly], textarea[readonly]').each(function(){
			jQuery('<i class="fas fa-lock readonly-padlock"></i>').hide().insertAfter(this).fadeIn(500);
		});
	})

	/* Enable Tool Tips */
	jQuery(document).ready(function(){

		jQuery('input[type=text], select, textarea').filter('[title][title!=""]').each(function(){

			var f = jQuery(this);
			var help = jQuery('<i class="fas fa-question-circle tooltip-button"></i>');
			jQuery(this).parent().find("label").prepend(help);
			
			jQuery(help).hover(function(){
				jQuery(f).tooltip({
					delay: { "show": 500, "hide": 1000 },
				});
				
				jQuery(f).tooltip({placement : 'auto'})
				jQuery(f).tooltip('enable')
				jQuery(f).tooltip('show')
				
			}, function(){

				jQuery(f).tooltip('toggleEnabled')
				jQuery(f).tooltip('hide')			
			});
			
		});
		
	})
	
	/* Form Validation */
	
	jQuery('#password').keyup(function(){
		if(jQuery(this).is(":visible")) {
			
			 if(jQuery(this).val() != null){
				jQuery(this).prop("required","required");
				jQuery('#password-confirm').prop("required","required"); 
			 }
			 
			 if(jQuery(this).val() == ""){			 
				jQuery(this).removeAttr('required').removeClass("validation-fail");
				jQuery('#password-confirm').removeAttr('required').removeClass("validation-fail");
			 }
		}
	})		
	
	jQuery('input,textarea,select').filter('[required]').each(function(){
			jQuery(this).addClass("required");	
	});
	jQuery('input,textarea,select').filter('[required]').change(function(){
		if(jQuery(this).is(":visible") || jQuery(this).hasClass('validate')){
			rs.parentForm = jQuery(this).closest("form");
			rs.validateForm(rs.parentForm);
			rs.clearErrors();
		}
	})
	jQuery('input,textarea,select').filter('[required]').keyup(function(){
		if(jQuery(this).is(":visible") || jQuery(this).hasClass('validate')){
			rs.parentForm = jQuery(this).closest("form");
			rs.validateForm(rs.parentForm);
			rs.clearErrors();
		}
	})	
	jQuery('input,textarea,select').filter('[required]:visible').each(function(){
		jQuery(this).addClass("required");
		label = jQuery(this).parent().find('label')
		
		jQuery(label).append(" *");
		
		rs.parentForm = jQuery(this).closest("form");
		//jQuery(rs.parentForm).find(':submit').prop('disabled', true);
	})
	jQuery(rs.parentForm).submit(function(e){
		
		rs.validateForm(rs.parentForm);
		
		if($(this).hasClass("disabled")){;
			e.preventDefault();

			target = jQuery(this).find('.validation-fail').first();

			$('html, body').stop().animate( {
			  'scrollTop': target.offset().top-140
			}, 500, 'swing', function () {
			  
			} );
					
		}
		
	})

	rs.repeat("validation", 1000, function(){
		
		jQuery('form').each(function(){
			rs.validateForm(this);
		})
		
	}, true);

});
	
rs.clearErrors = function(){
	jQuery('.alert-danger').delay('5000').fadeOut('slow');
}

rs.validateEmail = function(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

rs.validateTelephone = function(telephone){
	if(telephone.length < 11)return false;
	return /\d/.test(String(telephone));
}

rs.validatePasswordConfirm = function(password){
	/* There is no Password Confirm Field */
	if(jQuery('#password-confirm').length == 0 ) {return true;}	
	
	/* if the field is the old password field */
	if(jQuery(password).attr('id').indexOf('old') != -1){
			return true;
	}
	
	/* if the field is the current password field */
	if(jQuery(password).attr('id').indexOf('current') != -1){
			return true;
	}	
	
	/* if field is not confirm field */
	if(jQuery(password).attr('id').indexOf('confirm') == -1){
		/* and the confirm field is not empty */
		if(jQuery('#password-confirm').val().length != 0){
			/* and the password field and confirm field do not match */
			if(jQuery(password).val() != jQuery('#password-confirm').val()){
				/* Fail */
				return false;
			}
			
		}
	}
	
	/* if field is not confirm field */
	if(jQuery(password).attr('id').indexOf('confirm') == -1){	
		/* and the confirm field is empty */
		if(jQuery('#password-confirm').val().length == 0){
			/* Pass */
			return true;
		}
	}

	/* if the field is the confirm field */
	if(jQuery(password).attr('id').indexOf('confirm') != -1){
		/* but confirm field is empty */
		if(jQuery('#password-confirm').val().length == 0){
			return false;
		}
	}	
	
	/* if the field is the confirm field */
	if(jQuery(password).attr('id').indexOf('confirm') != -1){
		/* but confirm field is not empty */
		if(jQuery('#password-confirm').val().length != 0){
			/* and the confirm does not match password */
			if(jQuery(password).val() != jQuery('#password').val()){
				return false;
			}
		}
	}		

	return true
}

rs.validatePasswordStrength = function(password){
	
	/* Just Skip if this is the old password field */
	if(jQuery(password).attr('id').indexOf('old') != -1){return true;}	
	if(jQuery(password).attr('id').indexOf('current') != -1){return true;}	
	
	/* Don't run if no password confirm present */
	if(jQuery('#password-confirm').length == 0 ) {return true;}	
	
	/* Uses the landscape.passwords checker */
	if(typeof checkPass == 'function'){
			if(checkPass(jQuery(password).val()) > 1) {
				return true;
			}else{
				return false;
			}
	}else{ /* "Fool" back, someone didnt include landscape.passwords */
		if(jQuery(password).val().length < 7){return false;}
		if(!jQuery(password).val().match(/[a-z]/)){return false;}
		if(!jQuery(password).val().match(/[A-Z]/)){return false;}
		if(!jQuery(password).val().match(/\d+/)){return false;}		
	}
	return true;
}

rs.validateForm = function(form){
	
	if(jQuery(form).hasClass('no--validate')){
		return true;
	}
	
	var formValidates = true;
	var elementValidates
	jQuery(form).each(function(){
		
		jQuery(this).find("input, textarea, select").filter('[required]').each(function(){
			
			if(!jQuery(this).is('[readonly]') && ( jQuery(this).is(":visible") || jQuery(this).hasClass('validate') )){
			
				elementValidates = true;
				
				/* Email Validation - Checks Only Email Fields */
				if(jQuery(this).attr('type') == 'email'){
					if(!rs.validateEmail(jQuery(this).val())){
						formValidates = false;
						elementValidates = false;
					}
				}

				/* Telephone Validation - Checks Only Telephone Fields */
				if(jQuery(this).attr('type') == 'telephone'){
					if(!rs.validateTelephone(jQuery(this).val())){
						formValidates = false;
						elementValidates = false;
					}
				}
				
				/* Empty input Values */
				if(!jQuery(this).val()){
					formValidates = false;
					elementValidates = false;				
				}
				
				/* Password Confirmation Validation - Check Any Password fields for confirmation match */

				if(jQuery(this).attr('type') == 'password'){
					
					if(!rs.validatePasswordConfirm(this)){
						formValidates = false;	
						elementValidates = false;	
					}
				}
				
				/* Password Strength Validation - Check Any Password fields for Strength */			
				if(jQuery(this).attr('type') == 'password'){
					if(!rs.validatePasswordStrength(this)){
						formValidates = false;
						elementValidates = false;					
					}
				}
				
				/* Max Length Validation - Checks any max lengths do not exceed the provided number */			
				if(jQuery(this).attr('maxlength')){
					if(jQuery(this).val().length > jQuery(this).attr('maxlength')){
						formValidates = false;
						elementValidates = false;					
					}
				}				
				
				if(elementValidates){
					if(!jQuery(this).parent().find('.fa-check').length){
						jQuery('<i class="fas fa-check validation-tick"></i>').hide().insertAfter(this).fadeIn(500);
						jQuery(this).css('border-color','');
						jQuery(this).removeClass('validation-fail');
					}
				}else{				
					jQuery(this).parent().find('.fa-check').remove();
					jQuery(this).css('border-color','#ea5153');
					jQuery(this).addClass('validation-fail');
				}

			}
			
		})
		

		
	})

	if(formValidates){
		//jQuery(form).find(':submit').prop('disabled', false);
		jQuery(form).find(':submit').removeClass("disabled")
		jQuery(form).find(':submit').css('cursor','pointer');
		jQuery(form).removeClass("disabled")
	}else{
		//jQuery(form).find(':submit').prop('disabled', true);
		jQuery(form).find(':submit').addClass("disabled")
		jQuery(form).find(':submit').css('cursor','not-allowed');
		jQuery(form).addClass("disabled")
	}
		
}

rs.formSubmitLoading = function(){
	
	
	$("form button[type=submit]").click(function(e) {
		if($(this).hasClass("disabled")) return;
		$(this).prop("og", $(this).html());
		$(this).prop("disabled", "disabled");		
		$(this).html('<i class="fas fa-circle-notch fa-spin"></i>')
		
		$(this).parents('form:first').submit();
		
		return true;
		
    });
	
}

rs.init("select2", function(){

	$("select").each(function(){
		
		jQuery(this).select2({
			allowClear: true,
		});
	});

	firstEmptySelect = true;

	$("select[multiple]").each(function(){
		jQuery(this).select2({
			allowClear: true,
			escapeMarkup: function(markup) {return markup;},
			templateResult: function(data) {
				
				if(data.element != null && firstEmptySelect){
					
					if(rs.isJson(data.text)){
					d = JSON.parse(data.text);
					var c = jQuery('<div class="row row-select2-header"></div>');
					$.each(d, function(key,value){
						jQuery(c).append('<div class="col-lg col-s col-select2-header"> <b>' + key.toProperCase() + '</b></div>');
					})
						jQuery('#select2-' + data.element.offsetParent.id + '-results').prepend(c);
					}
					
					firstEmptySelect = false;
				}
				
				if(rs.isJson(data.text)){
					d = JSON.parse(data.text);
					var c = jQuery('<div class="row row-select2"></div>');
					$.each(d, function(key,value){
						jQuery(c).append('<div class="col-lg col-s col-select2">' + value + '</div>');
					})
					return c;
				}else{
					return data.text
				}
				
			},
			templateSelection: function(data){
				
				firstEmptySelect = true;
				
				if(rs.isJson(data.text)){
			
					d = JSON.parse(data.text);
					var c = jQuery('<div style="display:inline"></div>');
					$.each(d, function(key,value){
						jQuery(c).append('<span class="badge badge-secondary">' + value + '</span> ');
					})
					return c;
				}else{
					return data.text
				}
			}
		});
		
		
		jQuery(this).on('select2:close', function (e) {
			  firstEmptySelect = true;
		});
		
	});
	
});


String.prototype.toProperCase = function () {
    return this.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
};	

