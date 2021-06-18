rs.api = function (c) {

    var d = {
        action: null,
		get: null,
		put: null,
		post: null,
        dataType: "json",
        done: function () { },
        fail: function (data) { 
			response = $('<div></div>').html( data.responseText ).find("#message");
			rs.toast("Error", response.html(), 'danger') 
		}
    };

    jQuery.extend(d, c);
	
	jQuery.ajax({
        type: (d['post'] ? "POST" : ( d['put'] ? "PUT" : "GET" )),
        url: "/ajax/" + d['action'] + "?" + (d['get'] ? jQuery.param(d['get']) : ""),
        dataType: d['dataType'],
		cache: false,
        traditional: true,
        data: (d['post'] ? d['post'] : ( d['put'] ? d['put'] : "" )),
    }).done(function (data) {
		
        d['done'](data);
    }).fail(function (data) {
        d['fail'](data);
    })	

}