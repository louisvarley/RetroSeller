
rs.init("datatables", function(){
	
	order = rs.urlParams.get('order');
	orderBy = rs.urlParams.get('orderby');
	search = rs.urlParams.get('search');	

    var table = $('.list-table').DataTable( {
		"searching": true,
		"pageLength": 100	
    } );	
	
	if(order && orderBy){
		table.order([ orderBy, order ]).draw();
	}
	
	if(search){
		table.search(search).draw();
	}
	
});
