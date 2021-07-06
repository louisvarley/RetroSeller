
rs.init("datatables", function(){
	
	search = rs.urlParams.get('search');	

    var table = $('.list-table').DataTable( {
		"searching": true,
		"pageLength": 25,
		"order": []		
    } );	
	
	if(search){
		table.search(search).draw();
	}
	
});

rs.contrastColor = function(hexcolor){
	
    hexcolor = hexcolor.replace("#", "");
    var r = parseInt(hexcolor.substr(0,2),16);
    var g = parseInt(hexcolor.substr(2,2),16);
    var b = parseInt(hexcolor.substr(4,2),16);
    var yiq = ((r*299)+(g*587)+(b*114))/1000;
    
	if(yiq == 0) return '#000';
	
	return (yiq >= 128) ? '#000' : '#fff';
		
}

rs.rgb2hex = function (rgb) {
	
var hexDigits =["0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f"]; 
var hex= function(x) {
  return isNaN(x) ? "00" : hexDigits[(x - x % 16) / 16] + hexDigits[x % 16];
}	
	
rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
 return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
}

rs.rgba2hex = function (rgba) {
	
	function trim (str) {
	  return str.replace(/^\s+|\s+$/gm,'');
	}	
		
	  var inParts = rgba.substring(rgba.indexOf("(")).split(","),
		  r = parseInt(trim(inParts[0].substring(1)), 10),
		  g = parseInt(trim(inParts[1]), 10),
		  b = parseInt(trim(inParts[2]), 10),
		  a = parseFloat(trim(inParts[3].substring(0, inParts[3].length - 1))).toFixed(2);
	  var outParts = [
		r.toString(16),
		g.toString(16),
		b.toString(16),
		Math.round(a * 255).toString(16).substring(0, 2)
	  ];

	  // Pad single-digit output values
	  outParts.forEach(function (part, i) {
		if (part.length === 1) {
		  outParts[i] = '0' + part;
		}
	  })

	  return ('#' + outParts.join(''));
  
}

rs.checkListColumnColours = function(){
	
	
	$('.list-table').find("tr").each(function(){
		
		var rgbString = jQuery(this).css("background-color");
		
		if(rgbString.startsWith("rgba")){
			jQuery(this).css("color",rs.contrastColor(rs.rgba2hex(rgbString)));
		}else{
			jQuery(this).css("color",rs.contrastColor(rs.rgb2hex(rgbString)));			
		}
		
	})
	
}

rs.init("auto-color",function(){

	rs.checkListColumnColours();
	
	
	jQuery('.paginate_button').click(function(){
		
		rs.checkListColumnColours();
		
	});
	
})
	
	
	
