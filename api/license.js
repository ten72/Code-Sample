//if validation required, checks if dropdown is empty
function validate_required2(field,alerttxt){
	
	selectItem = document.getElementById(field);
	selectValue = selectItem.options[selectItem.selectedIndex].value;
	
	if(selectValue==""||selectValue==null){
		alert(alerttxt);return false;
	}else{
		return true;
	}
}
//if validation required, checks if text field is empty
function validate_required(field,alerttxt)
{
with (field)
  {
  if (value==null||value=="")
    {
    alert(alerttxt);return false;
    }
  else
    {
    return true;
    }
  }
}
//credit_check
function credit_check(){
		var totalVal=document.getElementById('totalVal');
		var ccnum=document.getElementById('ccnum');
		var cccode=document.getElementById('cccode');
		var exp_month=document.getElementById('exp_month');
		var exp_year=document.getElementById('exp_year');
		var firstName=document.getElementById('firstName');
		var lastName=document.getElementById('lastName');
		var address=document.getElementById('address');
		var city=document.getElementById('city');
		var zip=	document.getElementById('zip');
		var stateNew	=document.getElementById('state');
		var stateValue=state.options[stateNew.selectedIndex].value;
		var countryNew=document.getElementById('country');
		var countryValue	=country.options[countryNew.selectedIndex].value;
		var transaction='';
		var error='';
		var url='/store/paypal.php?action=verify&totalVal='+totalVal.value+'&ccnum='+ccnum.value+'&cccod='+cccode.value+'&expire='+exp_month.value+exp_year.value+'&firstName='+firstName.value+'&lastName='+lastName.value+'&address='+address.value+'&city='+city.value+'&state='+stateValue+'&zip='+zip.value+'&country='+countryValue;
	var status;
	$.ajax({
	  url: url,
	  success: function(xml) {
		  
		  $(xml).find("Response").each(function() {
			  status=$(this).find("Status").text();
			  transaction=$(this).find("Transaction").text();
			  error=$(this).find("Error").text();
		  });
		if(status=='Success'){
	  		$( "#paypalTrans" ).val(transaction);
	  		document.forms.form1.submit();
		}else if(status=='Fail'){
	  		alert(error);
	  		$( "#paypalTrans" ).val(error);
			
		}
	  }
	});
	return false;
}
//
//emailVerify
//
function accountCheckNew(){
	var x=document.getElementById("email");
	accountEmail=x.value;
	checkPD360Email(accountEmail);
}
//check if email account exists
function checkPD360Email(email){
	$.ajax({
	  url: "/store/api/email-check.php?email="+email,
	  success: function(data) {
		  PD360Verify2(data,email);
	  }
	});
	return false;
}
//if email does not exist more info needed to create account
function PD360Verify2(xml,email)
{
  $(xml).find("PD360Status").each(function() {
    accountStatusString=$(this).find("Status").text();
  });
	var n=accountStatusString.search("True");
	var account=(n == '-1' ? 'False' : 'True');
	var countrySelect = $('#country').val();
	if(account == 'False' && countrySelect == 'US'){
		$( "#noaccount" ).slideDown( "slow", function() { });
		$('#accountNeeded').val('yes');
	}else if(account == 'False' && countrySelect != 'US'){
		$('#international').val('yes');
	}else{
		$( "#noaccount" ).slideUp( "slow", function() { });
		$('#accountNeeded').val('no');
	}
	
}

//check if email account exists
function emailVerify(){
	var x=document.getElementById("pd360email");
	accountEmail=x.value;
	getPD360Email(accountEmail);
}
//form will not submit on enter key, must click button
function noenter() { return !(window.event && window.event.keyCode == 13); }

function getPD360Email(email){
	$.ajax({
	  url: "/store/api/email-check.php?email="+email,
	  success: function(data) {
		  PD360Verify(data,email);
	  }
	});
	return false;
}

function PD360Verify(xml,email)
{
  $(xml).find("PD360Status").each(function() {
    accountStatusString=$(this).find("Status").text();
  });
	var n=accountStatusString.search("True");
	if(n=='-1'){ accountStatus='False'; }else accountStatus='True';
  (accountStatus == 'False')? $( "#countryList" ).slideDown( "slow", function() { }) : licenseOn(email, '12345');
}


function licenseOn(login,loginID){
	$.ajax({
	  url: "api/license-on.php?email="+login+"&loginID="+loginID,
	  success: function(data) {
		document.forms.form4.submit();
  		}
	});
}


function licenseOnNew(login,state,district,school,schoolID){
	$.ajax({
	  url: "api/license-on.php?email="+login+"&stateNew="+state+"&districtNew="+district+"&schoolNew="+school+"&schoolID="+schoolID,
	  success: function(data) {
		document.forms.form4.submit();
  		}
	});
}

function countryCheck(url){
	if(url=='US'){
		$( "#noaccount" ).slideDown( "slow");
	}else{
		var x=document.getElementById("pd360email");
		accountEmail=x.value;
			$.ajax({
			  url: "api/license-on.php?email="+accountEmail+"&stateNew="+url+"&districtNew=International LumiBook&schoolNew=International LumiBook&schoolID=360-455186",
			  success: function(data) {
				document.forms.form4.submit();
				}
			});
	}
	return false;
}

function countryCheckout(url){
	if(url=='US'){
		$( "#stateDropdown" ).show();
		$( "#stateField" ).hide();
	}else{
		$( "#stateDropdown" ).hide();
		$( "#stateField" ).show();
		getShipping(url, 'billing_country');
	}
	return false;
}

function countryCheckout2(url){
	if(url=='US'){
		$( "#stateDropdown2" ).show();
		$( "#stateField2" ).hide();
	}else{
		$( "#stateDropdown2" ).hide();
		$( "#stateField2" ).show();
		getShipping(url, 'shipping_country');
	}
	return false;
}

//shipping state
function shippingState(url){
	getShipping(url, 'shipping_state');
}

//
//Load Districts
//
function loadDistrictNew(url){
		getShipping(url, 'billing_state');
	$.ajax({
	  url: "api/districts.php?state="+url,
	  success: district
	});
	return false;
}

function loadDistrict(url){
	$.ajax({
	  url: "api/districts.php?state="+url,
	  success: district
	});
	return false;
}

//
//Load District
//
function district(xml)
{
  var district='<select class="form-control" name="district" id="district" onchange="loadSchool(this.value)">';
  district=district+'<option value="">Select District</option>';
  $(xml).find("District").each(function()
  {
    district=district+'<option value="' + $(this).find("DistrictNCESId").text() + '">'+ $(this).find("DistrictName").text() + '</option>';
  });
  district=district+'</select>';
  document.getElementById('listdistricts').innerHTML=district;
}



//
//Load Schools
//

function loadSchool(url){
	$.ajax({
	  url: "api/schools.php?did="+url,
	  success: schools
	});
	return false;
}

function schools(xml)
{
  var school='<select class="form-control" name="school" id="school">';
  school=school+'<option value="">Select School</option>';
  $(xml).find("School").each(function()
  {
    school=school+'<option value="' + $(this).find("SchoolNCESId").text() + '">'+ $(this).find("SchoolName").text() + '</option>';
  });
  school=school+'</select>';
  document.getElementById('listschools').innerHTML=school;
}



//
//Validate Form
//

function validate(thisform){
	with (thisform){
		if (!validate_required(pd360email,'Please enter Email')){
			pd360email.focus();
		}else if (!validate_required2('state','Please select State')){ 
			state.focus(); 
		}else if (!validate_required2('district','Please select District')){
			district.focus(); 
		}else if (!validate_required2('school','Please select School')){
			school.focus();
		}else{
			var emailNew			= document.getElementById('pd360email').value;
			var stateNew			= document.getElementById('state');
			var stateValue		= state.options[stateNew.selectedIndex].value;
			var stateValue2		= state.options[stateNew.selectedIndex].text;
			var districtNew		= document.getElementById('district');
			var districtValue	= district.options[districtNew.selectedIndex].value;
			var districtValue2	= district.options[districtNew.selectedIndex].text;
			var schoolNew		= document.getElementById('school');
			var schoolValue		= school.options[schoolNew.selectedIndex].value;
			var schoolValue2		= school.options[schoolNew.selectedIndex].text;
			licenseOnNew(emailNew,stateValue,districtValue2,schoolValue2,schoolValue);	
		}
		return false;
	}
}