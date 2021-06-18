//https://api.upcitemdb.com/prod/trial/lookup?upc=4974365634537


rs.init("barcode_lookup", function(){

	$('.barcode-lookup').click(function(){

		var bc = jQuery(this).prev().val()

		if(bc.length < 12) return;

		$.ajax({
		  method: 'GET',
		  url: "https://cors-anywhere.herokuapp.com/https://api.upcitemdb.com/prod/trial/lookup?upc=" + bc,
		  dataType: 'json',
		}).done(function(data) {
			if(data.message){
				alert(data.message);
			}else{
				jQuery('#title').val(data.items[0].title)
				jQuery('#valuation').val(data.items[0].lowest_recorded_price)
			}

		})
	});

})


