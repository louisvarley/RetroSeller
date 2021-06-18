rs.init("setup_transactions",function(){

	rs.formDataLoad();	
	
	rs.select2({
		selectId: "#purchase_id",
		fields: [
			{name: "id", prefix: "PUR-"}, 
			{name: "title"},
			{name: "date"},
			{name: "balance", prefix: "£", format: "currency"},
		],
		model: "SummaryPurchase",

	})

	rs.select2({
		selectId: "#sale_id",
		fields: [
			{name: "id", prefix: "SALE-"}, 
			{name: "date"},
			{name: "gross_price", prefix: "£", format: "currency"},
		],
		model: "Sale",
	})
	
	$('#type').select2();	
	$('#type').append($('<option>', { 
		value: "OUT",
		text : "Out"
	}));	
	$('#type').append($('<option>', { 
		value: "IN",
		text : "In"
	}));	
	
	
	$('#account_id').select2();
	rs.api({
			action: 'searchJson',
			get: {
				fields : "id,holder", 
				model: "Account",
			},
			done: function (items) {
				$.each(items, function (i, item) {
					$('#account_id').append($('<option>', { 
						value: item.id,
						text : "ACC-" + item.id + ", " + item.holder 
					}));
				});
				
			}
	})		
	
	
}, "transaction", "manager");

rs.init("update_transaction",function(){
	
	
	$('#transaction').submit(function() {
		
		/* PUT - Update a Record */
		if(rs.id()){
		
			rs.api({
				action: 'transaction',
				dataType: 'json',
				put:  rs.serialise('#transaction'),
				done: function(data){
						rs.toast('Saved', "Changes have been saved...", 'success');
				}
			});
			
			return false;
		}
	})
	
}, "transaction", "manager");

rs.init("insert_transaction",function(){
	
	$('#transaction').submit(function() {
		
		if(!rs.id()){
			rs.api({
				action: 'transaction',
				dataType: 'json',
				post:  $( '#transaction' ).serialize(),
				done: function(data){
						rs.toast('Saved', "Changes have been saved...", 'success');
						window.location.href = (window.location.href + '/' + data['id'])
				}
			});	
		}
		
		return false;
		
	})
	
}, "transaction", "manager");