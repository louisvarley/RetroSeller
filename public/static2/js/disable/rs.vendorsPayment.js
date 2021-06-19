rs.init("get_vendorPayment",function(){
	
	rs.formDataLoad();
	
}, "vendorPayment", "manager");


rs.init("update_vendorPayment",function(){
	
	
	$('#vendorPayment').submit(function() {
		
		/* PUT - Update a Record */
		if(rs.id()){
		
			rs.api({
				action: 'vendorPayment',
				dataType: 'json',
				put:  rs.serialise('#vendorPayment'),
				done: function(data){
						rs.toast('Saved', "Changes have been saved...", 'success');
				}
			});
			
			return false;
		}
	})
	
}, "vendorPayment", "manager");

rs.init("insert_vendorPayment",function(){
	
	$('#vendorPayment').submit(function() {
		
		if(!rs.id()){
			rs.api({
				action: 'vendorPayment',
				dataType: 'json',
				post:  $( '#vendorPayment' ).serialize(),
				done: function(data){
						rs.toast('Saved', "Changes have been saved...", 'success');
						window.location.href = (window.location.href + '/' + data['id'])
				}
			});	
		}
		
		return false;
		
	})
	
}, "vendorPayment", "manager");