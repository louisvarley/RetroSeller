rs.init("get_vendorSale",function(){
	
	rs.formDataLoad();
	
}, "vendorSale", "manager");

rs.init("update_vendorSale",function(){
	
	
	$('#vendorSale').submit(function() {
		
		/* PUT - Update a Record */
		if(rs.id()){
		
			rs.api({
				action: 'vendorSale',
				dataType: 'json',
				put:  rs.serialise('#vendorSale'),
				done: function(data){
						rs.toast('Saved', "Changes have been saved...", 'success');
				}
			});
			
			return false;
		}
	})
	
}, "vendorSale", "manager");

rs.init("insert_vendorSale",function(){
	
	$('#vendorSale').submit(function() {
		
		if(!rs.id()){
			rs.api({
				action: 'vendorSale',
				dataType: 'json',
				post:  $( '#vendorSale' ).serialize(),
				done: function(data){
						rs.toast('Saved', "Changes have been saved...", 'success');
						window.location.href = (window.location.href + '/' + data['id'])
				}
			});	
		}
		
		return false;
		
	})
	
}, "vendorSale", "manager");