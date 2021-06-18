rs.init("setup_purchases",function(){
	
	rs.formDataLoad();	
	
	rs.select2({
		selectId: "#sale_vendor_id",
		fields: [
			{name: "id", prefix: "SV-"}, 
			{name: "title"},
		],
		model: "VendorSale",
	})	
	
	rs.select2({
		selectId: "#stock_id",
		fields: [
			{name: "id", prefix: "ITM-"}, 
			{name: "title"},
		],
		model: "Stock",
	})

	rs.select2({
		selectId: "#purchase_id",
		fields: [
			{name: "id", prefix: "PUR-"}, 
			{name: "title"},
		],
		model: "Purchase",
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

	rs.api({
		action: 'purchaseStockAction',
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
	
}, "purchase", "manager");

rs.init("update_purchase",function(){
	
	$('#purchase').submit(function(e) {

		
		/* PUT - Update a Record */
		if(rs.id()){
			rs.api({
				action: 'purchase',
				dataType: 'json',
				put:  rs.serialise('#purchase'),
				done: function(data){
					rs.toast('Saved', "Changes have been saved...", 'success');
				}
			});
			e.preventDefault();
			return false;
		}
	})
	
}, "purchase", "manager");

rs.init("insert_purchase",function(){
	
	$('#purchase').submit(function(e) {
		
		if(!rs.id()){
			rs.api({
				action: 'purchase',
				dataType: 'json',
				post:  $( '#purchase' ).serialize(),
				done: function(data){
					rs.toast('Saved', "Changes have been saved...", 'success');
					window.location.href = (window.location.href + '/' + data['id'])
				}
			});	
		}
		e.preventDefault();		
		return false;
		
	})
	
}, "purchase", "manager");