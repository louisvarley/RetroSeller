rs.init("setup_sales",function(){
	
	rs.formDataLoad();	
	
	rs.select2({
		selectId: "#stock_id",
		fields: [
			{name: "id", prefix: "ITM-"}, 
			{name: "title"},
		],
		model: "Stock",
	})

	rs.select2({
		selectId: "#account_id",
		fields: [
			{name: "id", prefix: "ACC-"}, 
			{name: "holder"},
		],
		fieldText: "holder",
		model: "Account",
	})
	
	rs.select2({
		selectId: "#sale_vendor_id",
		fields: [
			{name: "id", prefix: "SV-"}, 
			{name: "title"},
		],
		model: "VendorSale",
	})
	
	rs.select2({
		selectId: "#payment_vendor_id",
		fields: [
			{name: "id", prefix: "VP-"}, 
			{name: "title"},
		],
		model: "VendorPayment",
	})

	rs.api({
		action: 'stockSaleAction',
		dataType: 'json',
		get:  {'id': rs.id()},
		done: function(items){

			var vals=[];
			for(var i=0;i<items.length;i++){
			   vals.push(items[i].stock_id);
			}

			$('#stock_id').val(vals);		
			$('#stock_id').trigger('change'); 

		}
	});

	rs.api({
		action: 'accountSaleAction',
		dataType: 'json',
		get:  {'id': rs.id()},
		done: function(items){

			var vals=[];
			for(var i=0;i<items.length;i++){
			   vals.push(items[i].account_id);
			}

			$('#account_id').val(vals);		
			$('#account_id').trigger('change'); 

		}
	});
	
	
}, "sale", "manager");


rs.init("update_sale",function(){
	
	
	$('#sale').submit(function() {
		
		/* PUT - Update a Record */
		if(rs.id()){
		
			rs.api({
				action: 'sale',
				dataType: 'json',
				put:  rs.serialise('#sale'),
				done: function(data){
						rs.toast('Saved', "Changes have been saved...", 'success');
				}
			});
			
			return false;
		}
	})
	
}, "sale", "manager");

rs.init("insert_sale",function(){
	
	$('#sale').submit(function() {
		
		if(!rs.id()){
			rs.api({
				action: 'sale',
				dataType: 'json',
				post:  $( '#sale' ).serialize(),
				done: function(data){
						rs.toast('Saved', "Changes have been saved...", 'success');
						window.location.href = (window.location.href + '/' + data['id'])
				}
			});	
		}
		
		return false;
		
	})
	
}, "sale", "manager");