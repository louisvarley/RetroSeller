rs.init("setup_stock",function(){
	
	rs.formDataLoad();		
	
	rs.select2({
		selectId: "#purchase_id",
		fields: [
			{name: "id", prefix: "PUR-"}, 
			{name: "title"},
			{name: "date"},
		],
		model: "Purchase",
	})	

$('#status').select2();	

	$('#type').append($('<option>', { 
		value: "SALE",
		text : "For Sale"
	}));	

	$('#type').append($('<option>', { 
		value: "HELD",
		text : "Held"
	}));	

	$('#type').append($('<option>', { 
		value: "WRITEOFF",
		text : "Write Off"
	}));


	$("#type option[value='']").each(function() {
		$(this).remove();
	});
	
}, "stock", "manager");


rs.init("update_stock",function(){
	
	$('#stock').submit(function() {
		
		/* PUT - Update a Record */
		if(rs.id()){

			rs.api({
				action: 'stock',
				dataType: 'json',
				put:  rs.serialise('#stock'),
				done: function(data){
						rs.toast('Saved', "Changes have been saved...", 'success');
				}
			});
			
			return false;
		}
	})	
}, "stock", "manager");


	rs.imageUpload = function(id){

			var formData = new FormData();
			formData.append($('#stock').find("input[type='file']").attr("name"), $('#stock').find("input[type='file']")[0].files[0]);

			$.ajax({
				   url : '/ajax/stockImageuploadAction?id=' + rs.id(),
				   type : 'POST',
				   data : formData,
				   processData: false,  // tell jQuery not to process the data
				   contentType: false,  // tell jQuery not to set contentType
				   success : function(data) {
		
				   }
			});

	}


rs.init("insert_stock",function(){
	
	$('#stock').submit(function() {
		
		if(!rs.id()){
			rs.api({
				action: 'stock',
				dataType: 'json',
				post:  $( '#stock' ).serialize(),
				done: function(data){
						rs.toast('Saved', "Changes have been saved...", 'success');
						window.location.href = (window.location.href + '/' + data['id'])
				}
			});	
		}
		
		return false;
		
	})
	
}, "stock", "manager");