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

rs.init("image_upload_button", function(){

	/* Handle Clicked Upload */
	jQuery('.btn-image-new').click(function(){	
		var input = jQuery(this).next();
		jQuery(input).click()
		return false;
	});
	
	/* Handle Image Upload Ajax */
	$('.image-upload').ajaxfileupload({
		action: '/api/purchases/purchaseImage?purchaseId=' + id,
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
	
	/* Handle Delete Clicked*/
	$('body').on('click', '.form-image-delete', function() {
	
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
	jQuery('.form-image-rotate').click(function() {
	
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
	$('body').on('click', '.form-image-view', function() {
		
		var blobId = jQuery(this).data("id");
		
		window.open("/blob/" + blobId + '.jpg'); 
	});		
	
})