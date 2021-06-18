rs.init("Load Strength Meter", function(){
	
  jQuery(window).trigger('resize');
  
  // If Password Field, Check Password and Append Strength Meter
  if(jQuery('#password').length != 0){  
	  append_strength_meter();
	  jQuery("#password").keyup(function() {
		strengthCheck();
	  });
  }

  /* Displaying tooltip on click */
  jQuery(".password-container").on("click tap", ".pass--tooltip", function() {
  	console.log("click");
  	jQuery(".rs-tooltip__text").toggle();
  });
});

/* Append the Strength Metre to the Page */
function append_strength_meter(){
	passwordInput = $('#password')
	$('<div id="rs-meter-wrapper"><div id="meter_match"></div><div id="meter_weak"></div><div id="meter_good"></div><div id="meter_strong"></div></div>').insertAfter(passwordInput)
	
}

function checkPass(password){

	// Banned Chars
	if(password.match(/\s/))return 0
	
	// Check for Stupid phrases that shouldnt be used as a password
	if(password.toLowerCase().includes("password") || password.toLowerCase().includes("qwerty") || password.toLowerCase().includes("logmein")){return 1}
	
	// If the password greater than 6, contains Upper and LowerCase, Number and Sybol, then it's "Strong"
	if(password.length > 6 && password.match(/[a-z]/) && password.match(/\d+/) && password.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/))return 3

	// Password must contain Uppercase, Lowercase and Number to be "Good"
	if(password.length>6 && (password.match(/[a-z]/) && (password.match(/[A-Z]/) && password.match(/\d+/))))return 2
	
	// Must be longer than 6 Characters
	if(password.length <= 6)return 1
	

	
}

function strengthCheck() {
  var val = document.getElementById("password").value;
  var meterWeak = document.getElementById("meter_weak");
  var meterGood = document.getElementById("meter_good");
  var meterStrong = document.getElementById("meter_strong");

  var passInvalid =  "\
	<div class='rs-tooltip'>\
	  <p class='pass--match'></p>\
	  <p class='pass--weak pass--tooltip'>Invalid</p>\
	  <span class='rs-tooltip__text'>Your passsword contains forbidden characters</span>\
	</div>\
  "

  var passWeak =  "\
	<div class='rs-tooltip'>\
	  <p class='pass--match'></p>\
	  <p class='pass--weak pass--tooltip'>Weak</p>\
	  <span class='rs-tooltip__text'>Your passsword must contain more than 6 characters and include upper and lowercase letters and numbers.</span>\
	</div>\
  "

  var passGood =  "\
	<div class='rs-tooltip'>\
	  <p class='pass--match'></p>\
	  <p class='pass--good pass--tooltip'>Good</p>\
	  <span class='rs-tooltip__text'>Your password is good. Include special characters to increase your password strength.</span>\
	</div>\
  "

  var passStrong =  "\
	<div class='rs-tooltip'>\
  	  <p class='pass--match'></p>\
	  <p class='pass--strong pass--tooltip'>Strong</p>\
	 </div>\
  "
  no = checkPass(val);

  if(val != "") {

	if(no == 0) {
	  jQuery("#meter_weak").css({"background-color":"rgb(221, 221, 221)"});
	  jQuery("#meter_good").css({"background-color":"rgb(221, 221, 221)"});
	  jQuery("#meter_strong").css({"background-color":"rgb(221, 221, 221)"});
	  document.getElementById("rs-pass_type").innerHTML = passInvalid;
	  meterGood.style.backgroundColor="#ddd";
	  meterStrong.style.backgroundColor="#ddd";
	}
	
	if(no == 1) {
	  jQuery("#meter_weak").css({"background-color":"rgba(234, 81, 83, 0.5)"});
	  document.getElementById("rs-pass_type").innerHTML = passWeak;
	  meterGood.style.backgroundColor="#ddd";
	  meterStrong.style.backgroundColor="#ddd";
	}

	if(no == 2) {
	  jQuery("#meter_weak").css({"background-color":"rgba(242, 145, 0, 0.5)"});
	  jQuery("#meter_good").css({"background-color":"rgba(242, 145, 0, 0.5)"});
	  document.getElementById("rs-pass_type").innerHTML= passGood;
	  meterStrong.style.backgroundColor="#ddd";
	}

	if(no == 3) {
	  jQuery("#meter_weak").css({"background-color":"rgba(85, 175, 51, 0.5)"});
	  jQuery("#meter_good").css({"background-color":"rgba(85, 175, 51, 0.5)"});
	  jQuery("#meter_strong").css({"background-color":"rgba(85, 175, 51, 0.5)"});
	  document.getElementById("rs-pass_type").innerHTML = passStrong;
	}

  }

  else {
	meterWeak.style.backgroundColor="#fff";
	meterGood.style.backgroundColor="#fff";
	meterStrong.style.backgroundColor="#fff";
	document.getElementById("rs-pass_type").innerHTML="";
  }
}
