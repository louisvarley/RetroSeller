rs.init("setup_accounts",function(){
	
	rs.formDataLoad();	

}, "account", "manager");


rs.init("update_account",function(){
	
	
	$('#account').submit(function() {
		
		/* PUT - Update a Record */
		if(rs.id()){
		
			rs.api({
				action: 'accounts',
				dataType: 'json',
				put:  rs.serialise('#account'),
				done: function(data){
						rs.toast('Saved', "Changes have been saved...", 'success');
				}
			});
			
			return false;
		}
	})
	
}, "account", "manager");

rs.init("insert_account",function(){
	
	$('#account').submit(function() {
		
		if(!rs.id()){
			rs.api({
				action: 'accounts',
				dataType: 'json',
				post:  $( '#account' ).serialize(),
				done: function(data){
						rs.toast('Saved', "Changes have been saved...", 'success');
						window.location.href = (window.location.href + '/' + data['id'])
				}
			});	
		}
		
		return false;
		
	})
	
}, "account", "manager");