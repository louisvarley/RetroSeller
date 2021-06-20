rs.edit_button = function(id, object){
	return '<a href="' + object + "/manager/" + id + '"><button type="button" class="btn btn-sm btn-primary"><i class="fas fa-pencil-alt"></i></button></a>';
}

/* Terrible just terrible */
rs.delete_button = function(id, object, href = ""){
	return '<button onclick="rs.delete(\'' + object + '\',\'' + id + '\',\'' + href + '\')" type="button" class="btn btn-delete-record btn-sm btn-danger"><i class="fas fa-times-circle"></i></button>';
}


rs.init("table_loaded", function(){

	$('#datatable').on( 'draw.dt', function () {
			jQuery('.datatable-loading').hide();
	} );

	if(rs.urlParams.get("view")){

		$.ajax({
		  method: 'GET',
		  url: "ajax/view?view=" + rs.urlParams.get("view"),
		  dataType: 'json',
		}).done(function(data) {
			console.log(data);
			if(data){

				columns = [];
				ab = false;

				for (const [key, value] of Object.entries(data[0])) {

				 if(key == "id"){
					ab = true;
				 }

				  columns.push({
					data: key,
					name: key,
					title: key
				  });
				}

				if(ab){

				columns.push({
				title: "",
				data: "id",
				render: function(d,t,r,m){
					return `
						<a href="` + rs.controller() + `/manager/` + d + `">
						<button type="button" class="btn btn-sm btn-primary"><i class="fas fa-pencil-alt"></i></button>
						</a>

						<a href="` + rs.controller() + `/manager/` + d + `">
						<button type="button" class="btn btn-sm btn-danger"><i class="fas fa-times"></i></button>
						</a>
					`;
				}
				})

				}
	
				var t = $('#datatable').DataTable({
					"columns": columns,
					"data": data,
				});

			}

		})

	}
		
});