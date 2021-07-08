rs.init("sale_status_button", function(){
	
	jQuery('.sale-status-dropdown').click(function(){
		
		
		var statusId = jQuery(this).data("status-id");
		var saleId = jQuery(this).data("sale-id");

		jQuery.ajax({
			url: '/api/sales/saleSetStatus?saleId=' + saleId + '&saleStatusId=' + statusId,
			cache: false,
			contentType: false,
			processData: false,
			method: 'GET',
			type: 'GET',
			success: function(data){
				
				location.reload(); 
				return false;
			},
			fail: function(data){
				rs.throwSuccess("Error...", data['response']['error']);
			}
		});
		
		
		
	});
	
})

rs.init("calculate_fees_button", function(){
	
	jQuery('.btn-calculate-fees').click(function(){
		
		
		var saleVendorId = jQuery('#' + jQuery(this).data("sale-vendor-element-id")).val();
		var paymentVendorId = jQuery('#' + jQuery(this).data("payment-vendor-element-id")).val();
		var amount = jQuery('#' + jQuery(this).data("amount-element-id")).val();
		var input = jQuery('#' + jQuery(this).data("fee-input-id"));

		
		jQuery.ajax({
			url: '/api/sales/saleCalculateFees?saleVendorId=' + saleVendorId + '&paymentVendorId=' + paymentVendorId + '&amount=' + amount,
			cache: false,
			contentType: false,
			processData: false,
			method: 'GET',
			type: 'GET',
			success: function(data){
				
				jQuery(input).val(data.response.message);
				return false;
			},
			fail: function(data){
				rs.throwSuccess("Error...", data['response']['error']);
			}
		});
		
		
		
	});
	
})

rs.init("account_image_upload_button", function(){

	/* Handle Clicked Upload */
	jQuery('#account_logo').find('.btn-image-new').click(function(){	
		var input = jQuery(this).next();
		jQuery(input).click()
		return false;
	});
	
	/* Handle Image Upload Ajax */
	jQuery('#account_logo').find('.image-upload').ajaxfileupload({
		action: '/api/accounts/accountLogo?accountId=' + id,
		valid_extensions : ['jpg','png'],
		onComplete: function(data) {
			location.reload(); 
		},
		onStart: function() {
			rs.throwSuccess("Uploading...","Started Upload...");
		},
		onCancel: function() {
			console.log('no file selected');
		}
	});		
	
	/* Handle Preview Clicked*/
	jQuery('#account_logo').find('.form-image-view').click(function() {
		
		var blobId = jQuery(this).data("id");
		window.open("/blob/" + blobId + '.jpg'); 
	});			
	
	
/* Handle Delete Clicked*/
	jQuery('#account_logo').find('.form-image-delete').click(function() {
	
		var blobId = jQuery(this).data("id");
		var accountId = jQuery('#id').val();

		jQuery.ajax({
			url: '/api/accounts/accountLogo?blobId=' + blobId + '&accountId=' + accountId,
			cache: false,
			contentType: false,
			processData: false,
			method: 'DELETE',
			type: 'DELETE',
			success: function(data){
				
				jQuery('.form-image-' + blobId).fadeOut();
				return false;
			},
			fail: function(data){
				rs.throwSuccess("Error...", data['response']['error']);
			}
		});

	});
	
	/* Handle Rotate Clicked*/
	jQuery('#account_logo').find('.form-image-rotate').click(function() {
	
		var blobId = jQuery(this).data("id");

		jQuery.ajax({
			url: '/api/accounts/accountLogoRotate?blobId=' + blobId,
			cache: false,
			contentType: false,
			processData: false,
			method: 'GET',
			type: 'GET',
			success: function(data){
				var img = jQuery('.preview-image-' + blobId);
				var angle = ($(img).data('angle') + 90) || 90;
				$(img).css({'transform': 'rotate(' + angle + 'deg)'});
				$(img).data('angle', angle);
				
				
				return false;
				
			},
			fail: function(data){
				rs.throwSuccess("Error...", data['response']['error']);
			}
		});

	});	
	
})

rs.init("purchase_image_upload_button", function(){

	/* Handle Clicked Upload */
	jQuery('#purchase_images').find('.btn-image-new').click(function(){	
		var input = jQuery(this).next();
		jQuery(input).click()
		return false;
	});
	
	/* Handle Image Upload Ajax */
	jQuery('#purchase_images').find('.image-upload').ajaxfileupload({
		action: '/api/purchases/purchaseImage?purchaseId=' + id,
		valid_extensions : ['jpg','png'],
		onComplete: function(data) {
	
			if(data.code == 0){
				location.reload(); 
			}else{
				rs.throwSuccess("Error...",data.error);
			}

		},
		onStart: function() {
			if(rs.isMobile()){
				alert("Uploading image from camera");
			}else{
			rs.throwSuccess("Uploading...","Started Upload...");
			}
			
		},
		onCancel: function() {
			console.log('no file selected');
		}
	});		
	
	/* Handle Delete Clicked*/
	jQuery('#purchase_images').find('.form-image-delete').click(function() {
	
		var blobId = jQuery(this).data("id");
		var purchaseId = jQuery('#id').val();

		jQuery.ajax({
			url: '/api/purchases/purchaseImage?blobId=' + blobId + '&purchaseId=' + purchaseId,
			cache: false,
			contentType: false,
			processData: false,
			method: 'DELETE',
			type: 'DELETE',
			success: function(data){
				
				jQuery('.form-image-' + blobId).fadeOut();
				return false;
			},
			fail: function(data){
				rs.throwSuccess("Error...", data['response']['error']);
			}
		});

	});
	
	/* Handle Rotate Clicked*/
	jQuery('#purchase_images').find('.form-image-rotate').click(function() {
	
		var blobId = jQuery(this).data("id");

		jQuery.ajax({
			url: '/api/purchases/purchaseImageRotate?blobId=' + blobId,
			cache: false,
			contentType: false,
			processData: false,
			method: 'GET',
			type: 'GET',
			success: function(data){
				var img = jQuery('.preview-image-' + blobId);
				var angle = ($(img).data('angle') + 90) || 90;
				$(img).css({'transform': 'rotate(' + angle + 'deg)'});
				$(img).data('angle', angle);
				
				
				return false;
				
			},
			fail: function(data){
				rs.throwSuccess("Error...", data['response']['error']);
			}
		});

	});	
	
	/* Handle Preview Clicked*/
	jQuery('#purchase_images').find('.form-image-view').click(function() {
		
		var blobId = jQuery(this).data("id");
		
		window.open("/blob/" + blobId + '.jpg'); 
	});		
	
})