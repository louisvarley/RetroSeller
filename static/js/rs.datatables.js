
rs.init("datatables", function(){
	
	jQuery('.list-table').dataTable({
		"searching": true,
		"pageLength": 100,
	});
	
});

rs.init("subtable", function(){
	jQuery('.btn-sub-table').click(function(){
		
		T = $("tr[data-id='" + $(this).data("id") +"']");
		
		if(T.is(":visible")){
			jQuery(this).html('<i class="fas fa-plus"></i>');
			T.hide();
		}else{
			jQuery(this).html('<i class="fas fa-minus"></i>');
			T.show();			
		}
		
	});
	
	
});