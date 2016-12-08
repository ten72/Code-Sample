<?
if(session_id() == '') {
    session_start();
}
$promo=$_SESSION['promo'];
$cart=$_SESSION['cart'];
$total='';
$error='';
$promo_discount='';
if(!$cart){
	header("Location:view_cart.php");
}
include('admin/db.php');

$product_query=mysql_query("SELECT * FROM products WHERE ProdLabel='". sanitize( $_REQUEST['p'])."'");
$row_product=mysql_fetch_object($product_query);

$title='Checkout';
$secure=1;
include('includes/header.php');


//Check if cart has License Requirements
$items2 = explode('|',$cart);
$contents2 = array();
foreach ($items2 as $specs2) { 
	$cubes2=explode(',', $specs2);
	$cube_id2=$cubes2[0];
	$cube_num2=$cubes2[1];
	
$cart_query2=mysql_query("SELECT * FROM products WHERE ItemID=". sanitize( $cube_id2));
$row_cart2=mysql_fetch_object($cart_query2);
	if($row_cart2->license_type){
		$license=true;
	}
	if($row_cart2->ChargeShipping=='Yes'){
		$shippingTotal=$shippingTotal+$row_cart2->Price*$cube_num;
	}
	if($row_cart2->ChargeTaxes=='Yes'){
		$taxesTotal=$taxesTotal+$row_cart2->Price*$cube_num;
	}
	$totalCost=$totalCost+$row_cart2->Price*$cube_num;
}
if($_REQUEST['new_user_error'] || $_REQUEST['license_error'] || $_REQUEST['edivate_license_error']){
	$error='There was an error processing your license. Please call 866-835-4185 for assistance.';
}
?>
<script type="text/javascript" src="api/license.js"></script>
<style>
.form-control{
	margin-bottom:5px;
}
#noaccount{
	display:none;
}
</style>
<div id="main"><h1>Checkout</h1></div><br />
<script type="text/javascript">



function getShipping(url, field){
	$('#'+field+'').val(url);
<? if($shippingTotal){ ?>
	if(document.getElementById('shipSame').checked){
		var country=$('#country').val();
		var state=$('#billing_state').val();
	}else{
		var country=$('#country2').val();
		var state=$('#shipping_state').val();
	}
<? } ?>
	$.ajax({
	  url: "/store/api/function-fees.php?ship=<? echo $shippingTotal; ?>&country="+country+"&state="+state,
	  success: function(xml) {
		  $(xml).find("Wrap").each(function() {
			  ship=$(this).find("Ship").text();
			  $( "#shipRow" ).removeClass( "alert-warning" );
			  $( "#shipRow" ).addClass( "alert-success" );
			  $( "#shipAmount" ).text('$'+ship);
			  <? if($shippingTotal){ ?>
			  $( "#shipTotalVal" ).val(ship);
			  <? } ?>
			  updateTotal();
		  });
	  }
	});
}
function calculateTax(){
	
	var billing_country=$('#country').val();
	var billing_state=$('#billing_state').val();
	var zip=$('#zip').val();
	$.ajax({
	  url: "/store/api/function-fees.php?tax=<? echo $taxesTotal; ?>&total="+<? echo $totalCost; ?>+"&state="+billing_state+"&zip="+zip,
	  success: function(xml) {
		  $(xml).find("Wrap").each(function() {
			  tax=$(this).find("Tax").text();
			  $( "#taxRow" ).removeClass( "alert-warning" );
			  $( "#taxRow" ).addClass( "alert-success" );
			  $( "#taxAmount" ).text('$'+tax);
			  $( "#taxesTotalVal" ).val(tax);
			  if(tax!='0.00'){
			  	$("#taxRow").slideDown();
			  }else{
			  	$("#taxRow").slideUp();
			  }
			  updateTotal();
		  });
	  }
	});
}
function updateTotal(){
	var cartVal=$( "#cartVal" ).val();
	var totalTax=$( "#taxesTotalVal" ).val();
	var totalShip=$( "#shipTotalVal" ).val();
	var totalPromo=$( "#promoTotalVal" ).val();
	var totalCount=(+cartVal + +totalTax + +totalShip - totalPromo);
	var totalNum= totalCount.toFixed(2);
	$( "#order_total" ).text('$'+totalNum);
	$( "#cc_total" ).text('$'+totalNum);
	$( "#totalVal" ).val(totalNum);
}
function IsEmail(email) {
  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(email);
}
function IsZip(zip) {
  var regex = /^(\d{5})?$/;
  return regex.test(zip);
}
<? if($shippingTotal){ ?>
function displayShipping(){
	if(document.getElementById('shipSame').checked){
		$( "#shipHidden" ).slideUp( "slow", function() { });
	}else{
		$( "#shipHidden" ).slideDown( "slow", function() { });
	}
}
<? } ?>
function displayPayment(){
	if(document.getElementById('payment_cc').checked){
		$( "#displayCC" ).slideDown( "slow", function() { });
		$( "#displayPO" ).slideUp( "slow", function() { });
	}else if (document.getElementById('payment_po').checked){
		$( "#displayPO" ).slideDown( "slow", function() { });
		$( "#displayCC" ).slideUp( "slow", function() { });
	}
}

$( document ).ready(function() {
	$("#email").on("input", function() {
    	var validEmail=IsEmail(this.value);
		if(validEmail){
			accountCheckNew();
		}
	});
	
	$("#zip").on("input", function() {
    	var validZip=IsZip(this.value);
		if(validZip){
			calculateTax(validZip);
		}
	});
});

function validate_required2(field,alerttxt){
	
	selectItem = document.getElementById(field);
	selectValue = selectItem.options[selectItem.selectedIndex].value;
	
	if(selectValue==""||selectValue==null){
		alert(alerttxt);return false;
	}else{
		return true;
	}
}

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

function validate_form(thisform)
{
with (thisform) {
  if (validate_required2('country',"Please select Country")==false){ document.getElementById('country').focus();return false; }
  else if (validate_required(firstName,"Please enter first name")==false){ firstName.focus();return false; }
  else if (validate_required(lastName,"Please enter last name")==false){ lastName.focus();return false; }
  else if (validate_required(phone,"Please enter phone")==false){ phone.focus();return false; }
  else if (validate_required(email,"Please enter email")==false){ email.focus();return false; }
  else if (validate_required(address,"Please enter address")==false){ address.focus();return false; }
  else if (validate_required(city,"Please enter city")==false){ city.focus();return false; }
  var countrySelect = $('#country').val();
  if(countrySelect == 'US'){
	  if(validate_required2('state',"Please select State")==false){ state.focus();return false; }
  }else{
	  if(validate_required(province,"Please enter State")==false){ province.focus();return false; }
  }
  if (validate_required(zip,"Please enter Zip code")==false){ zip.focus();return false; }
  var needsAccount = $('#accountNeeded').val();
  if(needsAccount=='yes'){
	  if(validate_required2('district',"Please select District")==false){ district.focus();return false; }
	  if(validate_required2('school',"Please select School")==false){ school.focus();return false; }
		var districtNew=document.getElementById('district');
		var districtValue2=district.options[districtNew.selectedIndex].text;
		$( "#new_district" ).val(districtValue2);
		var schoolNew=document.getElementById('school');
		var schoolValue2=school.options[schoolNew.selectedIndex].text;
		$( "#new_school" ).val(schoolValue2);
  }
  <? if($shippingTotal){ ?>
  //if shipping
  if(!document.getElementById('shipSame').checked){
	  if (validate_required2('country2',"Please select ship to Country")==false){ country2.focus();return false; }
	  else if (validate_required(addressOne2,"Please enter ship to address")==false){ addressOne2.focus();return false; }
	  else if (validate_required(city2,"Please enter ship to city")==false){ city2.focus();return false; }
	  var countrySelect2 = $('#country2').val();
	  if(countrySelect2 == 'US'){ 
		  if(validate_required2('state2',"Please select ship to State")==false){ state2.focus();return false; }
	  }else{
		  if(validate_required(province2,"Please enter ship to State")==false){ province2.focus();return false; }
	  }
	  if (validate_required(zip2,"Please enter ship to Zip code")==false){ zip2.focus();return false; }
  }
  <? } ?>
  //if po not selected
  if(document.getElementById('payment_cc').checked){
	  //else if po is selected
  		if (validate_required(ccnum,"Please enter Credit Card Number")==false){ ccnum.focus();return false; }
		else if (validate_required(cccode,"Please enter CVC Code")==false){ cccode.focus();return false; }
		else if (credit_check()==false){ ccnum.focus();return false; }
		
  }else if (document.getElementById('payment_po').checked){
	  //else if po is selected
  		if (validate_required(poName,"Please enter Purchase Order Name")==false){ poName.focus();return false; }
  		else if (validate_required(poNum,"Please enter Purchase Order Number")==false){ poNum.focus();return false; }
  }
	}//with this form
}//end function
</script>
<? if($error){ ?>
<div class="alert alert-danger">
<? echo $error; ?>
</div>
<? } ?>
<form id="form1" action="process.php" method="post" name="form1" onsubmit="return validate_form(form1)">
<input type="hidden" id="accountNeeded" name="accountNeeded">
<div class="row pa20">
<div class="col-md-6">
<h3>Billing Details</h3>
<div class="img-rounded grayborder pa20">
	<div class="row">
    	<div class="form-group col-md-12">
        	<label>Country<span class="required">*</span></label>
            <select class="form-control" name="country" id="country" onchange="countryCheckout(this.value)">
                            <option value="">Select Country</option>
                            <option value="US">United States of America</option>
                            <option value="AF">Afghanistan</option>
                            <option value="AL">Albania</option>
                            <option value="DZ">Algeria</option>
                            <option value="AS">American Samoa</option>
                            <option value="AD">Andorra</option>
                            <option value="AG">Angola</option>
                            <option value="AI">Anguilla</option>
                            <option value="AG">Antigua &amp; Barbuda</option>
                            <option value="AR">Argentina</option>
                            <option value="AA">Armenia</option>
                            <option value="AW">Aruba</option>
                            <option value="AU">Australia</option>
                            <option value="AT">Austria</option>
                            <option value="AZ">Azerbaijan</option>
                            <option value="BS">Bahamas</option>
                            <option value="BH">Bahrain</option>
                            <option value="BD">Bangladesh</option>
                            <option value="BB">Barbados</option>
                            <option value="BY">Belarus</option>
                            <option value="BE">Belgium</option>
                            <option value="BZ">Belize</option>
                            <option value="BJ">Benin</option>
                            <option value="BM">Bermuda</option>
                            <option value="BT">Bhutan</option>
                            <option value="BO">Bolivia</option>
                            <option value="BL">Bonaire</option>
                            <option value="BA">Bosnia &amp; Herzegovina</option>
                            <option value="BW">Botswana</option>
                            <option value="BR">Brazil</option>
                            <option value="BC">British Indian Ocean Ter</option>
                            <option value="BN">Brunei</option>
                            <option value="BG">Bulgaria</option>
                            <option value="BF">Burkina Faso</option>
                            <option value="BI">Burundi</option>
                            <option value="KH">Cambodia</option>
                            <option value="CM">Cameroon</option>
                            <option value="CA">Canada</option>
                            <option value="IC">Canary Islands</option>
                            <option value="CV">Cape Verde</option>
                            <option value="KY">Cayman Islands</option>
                            <option value="CF">Central African Republic</option>
                            <option value="TD">Chad</option>
                            <option value="CD">Channel Islands</option>
                            <option value="CL">Chile</option>
                            <option value="CN">China</option>
                            <option value="CI">Christmas Island</option>
                            <option value="CS">Cocos Island</option>
                            <option value="CO">Colombia</option>
                            <option value="CC">Comoros</option>
                            <option value="CG">Congo</option>
                            <option value="CK">Cook Islands</option>
                            <option value="CR">Costa Rica</option>
                            <option value="CT">Cote D'Ivoire</option>
                            <option value="HR">Croatia</option>
                            <option value="CU">Cuba</option>
                            <option value="CB">Curacao</option>
                            <option value="CY">Cyprus</option>
                            <option value="CZ">Czech Republic</option>
                            <option value="DK">Denmark</option>
                            <option value="DJ">Djibouti</option>
                            <option value="DM">Dominica</option>
                            <option value="DO">Dominican Republic</option>
                            <option value="TM">East Timor</option>
                            <option value="EC">Ecuador</option>
                            <option value="EG">Egypt</option>
                            <option value="SV">El Salvador</option>
                            <option value="GQ">Equatorial Guinea</option>
                            <option value="ER">Eritrea</option>
                            <option value="EE">Estonia</option>
                            <option value="ET">Ethiopia</option>
                            <option value="FA">Falkland Islands</option>
                            <option value="FO">Faroe Islands</option>
                            <option value="FJ">Fiji</option>
                            <option value="FI">Finland</option>
                            <option value="FR">France</option>
                            <option value="GF">French Guiana</option>
                            <option value="PF">French Polynesia</option>
                            <option value="FS">French Southern Ter</option>
                            <option value="GA">Gabon</option>
                            <option value="GM">Gambia</option>
                            <option value="GE">Georgia</option>
                            <option value="DE">Germany</option>
                            <option value="GH">Ghana</option>
                            <option value="GI">Gibraltar</option>
                            <option value="GB">Great Britain</option>
                            <option value="GR">Greece</option>
                            <option value="GL">Greenland</option>
                            <option value="GD">Grenada</option>
                            <option value="GP">Guadeloupe</option>
                            <option value="GU">Guam</option>
                            <option value="GT">Guatemala</option>
                            <option value="GN">Guinea</option>
                            <option value="GY">Guyana</option>
                            <option value="HT">Haiti</option>
                            <option value="HW">Hawaii</option>
                            <option value="HN">Honduras</option>
                            <option value="HK">Hong Kong</option>
                            <option value="HU">Hungary</option>
                            <option value="IS">Iceland</option>
                            <option value="IN">India</option>
                            <option value="ID">Indonesia</option>
                            <option value="IA">Iran</option>
                            <option value="IQ">Iraq</option>
                            <option value="IR">Ireland</option>
                            <option value="IM">Isle of Man</option>
                            <option value="IL">Israel</option>
                            <option value="IT">Italy</option>
                            <option value="JM">Jamaica</option>
                            <option value="JP">Japan</option>
                            <option value="JO">Jordan</option>
                            <option value="KZ">Kazakhstan</option>
                            <option value="KE">Kenya</option>
                            <option value="KI">Kiribati</option>
                            <option value="NK">Korea North</option>
                            <option value="KS">Korea South</option>
                            <option value="KW">Kuwait</option>
                            <option value="KG">Kyrgyzstan</option>
                            <option value="LA">Laos</option>
                            <option value="LV">Latvia</option>
                            <option value="LB">Lebanon</option>
                            <option value="LS">Lesotho</option>
                            <option value="LR">Liberia</option>
                            <option value="LY">Libya</option>
                            <option value="LI">Liechtenstein</option>
                            <option value="LT">Lithuania</option>
                            <option value="LU">Luxembourg</option>
                            <option value="MO">Macau</option>
                            <option value="MK">Macedonia</option>
                            <option value="MG">Madagascar</option>
                            <option value="MY">Malaysia</option>
                            <option value="MW">Malawi</option>
                            <option value="MV">Maldives</option>
                            <option value="ML">Mali</option>
                            <option value="MT">Malta</option>
                            <option value="MH">Marshall Islands</option>
                            <option value="MQ">Martinique</option>
                            <option value="MR">Mauritania</option>
                            <option value="MU">Mauritius</option>
                            <option value="ME">Mayotte</option>
                            <option value="MX">Mexico</option>
                            <option value="MI">Midway Islands</option>
                            <option value="MD">Moldova</option>
                            <option value="MC">Monaco</option>
                            <option value="MN">Mongolia</option>
                            <option value="MS">Montserrat</option>
                            <option value="MA">Morocco</option>
                            <option value="MZ">Mozambique</option>
                            <option value="MM">Myanmar</option>
                            <option value="NA">Nambia</option>
                            <option value="NU">Nauru</option>
                            <option value="NP">Nepal</option>
                            <option value="AN">Netherland Antilles</option>
                            <option value="NL">Netherlands (Holland, Europe)</option>
                            <option value="NV">Nevis</option>
                            <option value="NC">New Caledonia</option>
                            <option value="NZ">New Zealand</option>
                            <option value="NI">Nicaragua</option>
                            <option value="NE">Niger</option>
                            <option value="NG">Nigeria</option>
                            <option value="NW">Niue</option>
                            <option value="NF">Norfolk Island</option>
                            <option value="NO">Norway</option>
                            <option value="OM">Oman</option>
                            <option value="PK">Pakistan</option>
                            <option value="PW">Palau Island</option>
                            <option value="PS">Palestine</option>
                            <option value="PA">Panama</option>
                            <option value="PG">Papua New Guinea</option>
                            <option value="PY">Paraguay</option>
                            <option value="PE">Peru</option>
                            <option value="PH">Philippines</option>
                            <option value="PO">Pitcairn Island</option>
                            <option value="PL">Poland</option>
                            <option value="PT">Portugal</option>
                            <option value="PR">Puerto Rico</option>
                            <option value="QA">Qatar</option>
                            <option value="ME">Republic of Montenegro</option>
                            <option value="RS">Republic of Serbia</option>
                            <option value="RE">Reunion</option>
                            <option value="RO">Romania</option>
                            <option value="RU">Russia</option>
                            <option value="RW">Rwanda</option>
                            <option value="NT">St Barthelemy</option>
                            <option value="EU">St Eustatius</option>
                            <option value="HE">St Helena</option>
                            <option value="KN">St Kitts-Nevis</option>
                            <option value="LC">St Lucia</option>
                            <option value="MB">St Maarten</option>
                            <option value="PM">St Pierre &amp; Miquelon</option>
                            <option value="VC">St Vincent &amp; Grenadines</option>
                            <option value="SP">Saipan</option>
                            <option value="SO">Samoa</option>
                            <option value="AS">Samoa American</option>
                            <option value="SM">San Marino</option>
                            <option value="ST">Sao Tome &amp; Principe</option>
                            <option value="SA">Saudi Arabia</option>
                            <option value="SN">Senegal</option>
                            <option value="SC">Seychelles</option>
                            <option value="SL">Sierra Leone</option>
                            <option value="SG">Singapore</option>
                            <option value="SK">Slovakia</option>
                            <option value="SI">Slovenia</option>
                            <option value="SB">Solomon Islands</option>
                            <option value="OI">Somalia</option>
                            <option value="ZA">South Africa</option>
                            <option value="ES">Spain</option>
                            <option value="LK">Sri Lanka</option>
                            <option value="SD">Sudan</option>
                            <option value="SR">Suriname</option>
                            <option value="SZ">Swaziland</option>
                            <option value="SE">Sweden</option>
                            <option value="CH">Switzerland</option>
                            <option value="SY">Syria</option>
                            <option value="TA">Tahiti</option>
                            <option value="TW">Taiwan</option>
                            <option value="TJ">Tajikistan</option>
                            <option value="TZ">Tanzania</option>
                            <option value="TH">Thailand</option>
                            <option value="TG">Togo</option>
                            <option value="TK">Tokelau</option>
                            <option value="TO">Tonga</option>
                            <option value="TT">Trinidad &amp; Tobago</option>
                            <option value="TN">Tunisia</option>
                            <option value="TR">Turkey</option>
                            <option value="TU">Turkmenistan</option>
                            <option value="TC">Turks &amp; Caicos Is</option>
                            <option value="TV">Tuvalu</option>
                            <option value="UG">Uganda</option>
                            <option value="UA">Ukraine</option>
                            <option value="AE">United Arab Emirates</option>
                            <option value="GB">United Kingdom</option>
                            <option value="UY">Uruguay</option>
                            <option value="UZ">Uzbekistan</option>
                            <option value="VU">Vanuatu</option>
                            <option value="VS">Vatican City State</option>
                            <option value="VE">Venezuela</option>
                            <option value="VN">Vietnam</option>
                            <option value="VB">Virgin Islands (Brit)</option>
                            <option value="VA">Virgin Islands (USA)</option>
                            <option value="WK">Wake Island</option>
                            <option value="WF">Wallis &amp; Futana Is</option>
                            <option value="YE">Yemen</option>
                            <option value="ZR">Zaire</option>
                            <option value="ZM">Zambia</option>
                            <option value="ZW">Zimbabwe</option>
                        </select>
        </div>
    </div>
	<div class="row">
    	<div class="form-group col-md-6">
            <label>First Name<span class="required">*</span></label>
            <input id="firstName" name="firstName" type="text" class="form-control" placeholder="First Name" />
        </div>
        <div class="form-group col-md-6">
            <label>Last Name<span class="required">*</span></label>
            <input id="lastName" name="lastName" type="text" class="form-control" placeholder="Last Name" />
        </div>
    </div>
    <div class="row">
    	<div class="form-group col-md-6">
            <label>Phone<span class="required">*</span></label>
            <input id="phone" name="phone" type="text" class="form-control" placeholder="Phone" />
        </div>
        <div class="form-group col-md-6">
            <label>Email<span class="required">*</span></label>
            <input id="email" name="email" type="text" class="form-control" placeholder="Email" />
        </div>
    </div>
    <div class="row">
    	<div class="form-group col-md-12">
            <label>Address<span class="required">*</span></label>
            <input id="address" name="address" type="text" class="form-control" placeholder="Street Address" />
            <input id="addressTwo" name="addressTwo" type="text" class="form-control" placeholder="Apartment, suit, unit etc. (optional)" />
        </div>
    </div>
    <div class="row">
    	<div class="form-group col-md-12">
            <label>City / Town<span class="required">*</span></label>
            <input id="city" name="city" type="text" class="form-control" placeholder="City / Town" />
        </div>
    </div>
    <div class="row">
    	<div class="form-group col-md-6">
        <div id="stateDropdown">
            <label>State<span class="required">*</span></label>
            <select class="form-control" id="state" name="state" onchange="loadDistrictNew(this.value)">
                            <option value="">Select State</option>
                            <option id="USA-AL" value="AL">Alabama (AL)</option>
                            <option id="USA-AK" value="AK">Alaska (AK)</option>
                            <option id="USA-AZ" value="AZ">Arizona (AZ)</option>
                            <option id="USA-AR" value="AR">Arkansas (AR)</option>             
                            <option id="USA-CA" value="CA">California (CA)</option>
                            <option id="USA-CO" value="CO">Colorado (CO)</option>
                            <option id="USA-CT" value="CT">Connecticut (CT)</option>
                            <option id="USA-DE" value="DE">Delaware (DE)</option>
                            <option id="USA-DC" value="DC">District of Columbia (DC)</option>    
                            <option id="USA-FL" value="FL">Florida (FL)</option>
                            <option id="USA-GA" value="GA">Georgia (GA)</option>
                            <option id="USA-GU" value="GU">Guam (GU)</option>
                            <option id="USA-HI" value="HI">Hawaii (HI)</option>
                            <option id="USA-ID" value="ID">Idaho (ID)</option>
                            <option id="USA-IL" value="IL">Illinois (IL)</option>
                            <option id="USA-IN" value="IN">Indiana (IN)</option>
                            <option id="USA-IA" value="IA">Iowa (IA)</option>
                            <option id="USA-KS" value="KS">Kansas (KS)</option>
                            <option id="USA-KY" value="KY">Kentucky (KY)</option>
                            <option id="USA-LA" value="LA">Louisiana (LA)</option>
                            <option id="USA-ME" value="ME">Maine (ME)</option>
                            <option id="USA-MD" value="MD">Maryland (MD)</option>
                            <option id="USA-MA" value="MA">Massachusetts (MA)</option>
                            <option id="USA-MI" value="MI">Michigan (MI)</option>
                            <option id="USA-MN" value="MN">Minnesota (MN)</option>
                            <option id="USA-MS" value="MS">Mississippi (MS)</option>
                            <option id="USA-MO" value="MO">Missouri (MO)</option>
                            <option id="USA-MT" value="MT">Montana (MT)</option>
                            <option id="USA-NE" value="NE">Nebraska (NE)</option>
                            <option id="USA-NV" value="NV">Nevada (NV)</option>
                            <option id="USA-NH" value="NH">New Hampshire (NH)</option>
                            <option id="USA-NJ" value="NJ">New Jersey (NJ)</option>
                            <option id="USA-NM" value="NM">New Mexico (NM)</option>
                            <option id="USA-NY" value="NY">New York (NY)</option>
                            <option id="USA-NC" value="NC">North Carolina (NC)</option>
                            <option id="USA-ND" value="ND">North Dakota (ND)</option>
                            <option id="USA-OH" value="OH">Ohio (OH)</option>
                            <option id="USA-OK" value="OK">Oklahoma (OK)</option>
                            <option id="USA-OR" value="OR">Oregon (OR)</option>
                            <option id="USA-PA" value="PA">Pennsylvania (PA)</option>
                            <option id="USA-PR" value="PR">Puerto Rico (PR)</option>
                            <option id="USA-RI" value="RI">Rhode Island (RI)</option>
                            <option id="USA-SC" value="SC">South Carolina (SC)</option>
                            <option id="USA-SD" value="SD">South Dakota (SD)</option>
                            <option id="USA-TN" value="TN">Tennessee (TN)</option>
                            <option id="USA-TX" value="TX">Texas (TX)</option>
                            <option id="USA-UT" value="UT">Utah (UT)</option>
                            <option id="USA-VT" value="VT">Vermont (VT)</option>
                            <option id="USA-VA" value="VA">Virginia (VA)</option>
                            <option id="USA-VI" value="VI">Virgin Islands (VI)</option>
                            <option id="USA-WA" value="WA">Washington (WA)</option>
                            <option id="USA-WV" value="WV">West Virginia (WV)</option>
                            <option id="USA-WI" value="WI">Wisconsin (WI)</option>
                            <option id="USA-WY" value="WY">Wyoming (WY)</option>
                        </select>
        </div>
        <div id="stateField" style="display:none;">
            <label>State/Province<span class="required">*</span></label>
            <input type="text" class="form-control" name="province" placeholder="State/Province">
        </div>
        </div>
        <div class="form-group col-md-6">
            <label>Zip<span class="required">*</span></label>
            <input id="zip" name="zip" type="text" class="form-control" placeholder="Zip" />
        </div>
    </div>
    <div class="row" id="noaccount">
    	<div class="form-group col-md-6">
            <label>District<span class="required">*</span></label>
            <div id="listdistricts"><select class="form-control" name="district" disabled="disabled" id="district"><option value="">Select District</option></select></div>
        </div>
        <div class="form-group col-md-6">
            <label>School<span class="required">*</span></label>
            <div id="listschools"><select class="form-control" name="school" disabled="disabled" id="school"><option value="">Select School</option></select></div>
        </div>
    </div>
</div>
<? if($shippingTotal){ ?>
<h3>Shipping Details</h3>
<div class="img-rounded grayborder pl20 pr20">
<div class="checkbox"><label>Same as billing address <input type="checkbox" name="shipSame" id="shipSame" checked="checked" onChange="displayShipping();"></label></div>
<div id="shipHidden" style="display:none;">
	<div class="row">
    	<div class="form-group col-md-12">
        	<label>Country<span class="required">*</span></label>
            <select class="form-control" name="country2" id="country2" onchange="countryCheckout2(this.value)">
                            <option value="">Select Country</option>
                            <option value="US">United States of America</option>
                            <option value="AF">Afghanistan</option>
                            <option value="AL">Albania</option>
                            <option value="DZ">Algeria</option>
                            <option value="AS">American Samoa</option>
                            <option value="AD">Andorra</option>
                            <option value="AG">Angola</option>
                            <option value="AI">Anguilla</option>
                            <option value="AG">Antigua &amp; Barbuda</option>
                            <option value="AR">Argentina</option>
                            <option value="AA">Armenia</option>
                            <option value="AW">Aruba</option>
                            <option value="AU">Australia</option>
                            <option value="AT">Austria</option>
                            <option value="AZ">Azerbaijan</option>
                            <option value="BS">Bahamas</option>
                            <option value="BH">Bahrain</option>
                            <option value="BD">Bangladesh</option>
                            <option value="BB">Barbados</option>
                            <option value="BY">Belarus</option>
                            <option value="BE">Belgium</option>
                            <option value="BZ">Belize</option>
                            <option value="BJ">Benin</option>
                            <option value="BM">Bermuda</option>
                            <option value="BT">Bhutan</option>
                            <option value="BO">Bolivia</option>
                            <option value="BL">Bonaire</option>
                            <option value="BA">Bosnia &amp; Herzegovina</option>
                            <option value="BW">Botswana</option>
                            <option value="BR">Brazil</option>
                            <option value="BC">British Indian Ocean Ter</option>
                            <option value="BN">Brunei</option>
                            <option value="BG">Bulgaria</option>
                            <option value="BF">Burkina Faso</option>
                            <option value="BI">Burundi</option>
                            <option value="KH">Cambodia</option>
                            <option value="CM">Cameroon</option>
                            <option value="CA">Canada</option>
                            <option value="IC">Canary Islands</option>
                            <option value="CV">Cape Verde</option>
                            <option value="KY">Cayman Islands</option>
                            <option value="CF">Central African Republic</option>
                            <option value="TD">Chad</option>
                            <option value="CD">Channel Islands</option>
                            <option value="CL">Chile</option>
                            <option value="CN">China</option>
                            <option value="CI">Christmas Island</option>
                            <option value="CS">Cocos Island</option>
                            <option value="CO">Colombia</option>
                            <option value="CC">Comoros</option>
                            <option value="CG">Congo</option>
                            <option value="CK">Cook Islands</option>
                            <option value="CR">Costa Rica</option>
                            <option value="CT">Cote D'Ivoire</option>
                            <option value="HR">Croatia</option>
                            <option value="CU">Cuba</option>
                            <option value="CB">Curacao</option>
                            <option value="CY">Cyprus</option>
                            <option value="CZ">Czech Republic</option>
                            <option value="DK">Denmark</option>
                            <option value="DJ">Djibouti</option>
                            <option value="DM">Dominica</option>
                            <option value="DO">Dominican Republic</option>
                            <option value="TM">East Timor</option>
                            <option value="EC">Ecuador</option>
                            <option value="EG">Egypt</option>
                            <option value="SV">El Salvador</option>
                            <option value="GQ">Equatorial Guinea</option>
                            <option value="ER">Eritrea</option>
                            <option value="EE">Estonia</option>
                            <option value="ET">Ethiopia</option>
                            <option value="FA">Falkland Islands</option>
                            <option value="FO">Faroe Islands</option>
                            <option value="FJ">Fiji</option>
                            <option value="FI">Finland</option>
                            <option value="FR">France</option>
                            <option value="GF">French Guiana</option>
                            <option value="PF">French Polynesia</option>
                            <option value="FS">French Southern Ter</option>
                            <option value="GA">Gabon</option>
                            <option value="GM">Gambia</option>
                            <option value="GE">Georgia</option>
                            <option value="DE">Germany</option>
                            <option value="GH">Ghana</option>
                            <option value="GI">Gibraltar</option>
                            <option value="GB">Great Britain</option>
                            <option value="GR">Greece</option>
                            <option value="GL">Greenland</option>
                            <option value="GD">Grenada</option>
                            <option value="GP">Guadeloupe</option>
                            <option value="GU">Guam</option>
                            <option value="GT">Guatemala</option>
                            <option value="GN">Guinea</option>
                            <option value="GY">Guyana</option>
                            <option value="HT">Haiti</option>
                            <option value="HW">Hawaii</option>
                            <option value="HN">Honduras</option>
                            <option value="HK">Hong Kong</option>
                            <option value="HU">Hungary</option>
                            <option value="IS">Iceland</option>
                            <option value="IN">India</option>
                            <option value="ID">Indonesia</option>
                            <option value="IA">Iran</option>
                            <option value="IQ">Iraq</option>
                            <option value="IR">Ireland</option>
                            <option value="IM">Isle of Man</option>
                            <option value="IL">Israel</option>
                            <option value="IT">Italy</option>
                            <option value="JM">Jamaica</option>
                            <option value="JP">Japan</option>
                            <option value="JO">Jordan</option>
                            <option value="KZ">Kazakhstan</option>
                            <option value="KE">Kenya</option>
                            <option value="KI">Kiribati</option>
                            <option value="NK">Korea North</option>
                            <option value="KS">Korea South</option>
                            <option value="KW">Kuwait</option>
                            <option value="KG">Kyrgyzstan</option>
                            <option value="LA">Laos</option>
                            <option value="LV">Latvia</option>
                            <option value="LB">Lebanon</option>
                            <option value="LS">Lesotho</option>
                            <option value="LR">Liberia</option>
                            <option value="LY">Libya</option>
                            <option value="LI">Liechtenstein</option>
                            <option value="LT">Lithuania</option>
                            <option value="LU">Luxembourg</option>
                            <option value="MO">Macau</option>
                            <option value="MK">Macedonia</option>
                            <option value="MG">Madagascar</option>
                            <option value="MY">Malaysia</option>
                            <option value="MW">Malawi</option>
                            <option value="MV">Maldives</option>
                            <option value="ML">Mali</option>
                            <option value="MT">Malta</option>
                            <option value="MH">Marshall Islands</option>
                            <option value="MQ">Martinique</option>
                            <option value="MR">Mauritania</option>
                            <option value="MU">Mauritius</option>
                            <option value="ME">Mayotte</option>
                            <option value="MX">Mexico</option>
                            <option value="MI">Midway Islands</option>
                            <option value="MD">Moldova</option>
                            <option value="MC">Monaco</option>
                            <option value="MN">Mongolia</option>
                            <option value="MS">Montserrat</option>
                            <option value="MA">Morocco</option>
                            <option value="MZ">Mozambique</option>
                            <option value="MM">Myanmar</option>
                            <option value="NA">Nambia</option>
                            <option value="NU">Nauru</option>
                            <option value="NP">Nepal</option>
                            <option value="AN">Netherland Antilles</option>
                            <option value="NL">Netherlands (Holland, Europe)</option>
                            <option value="NV">Nevis</option>
                            <option value="NC">New Caledonia</option>
                            <option value="NZ">New Zealand</option>
                            <option value="NI">Nicaragua</option>
                            <option value="NE">Niger</option>
                            <option value="NG">Nigeria</option>
                            <option value="NW">Niue</option>
                            <option value="NF">Norfolk Island</option>
                            <option value="NO">Norway</option>
                            <option value="OM">Oman</option>
                            <option value="PK">Pakistan</option>
                            <option value="PW">Palau Island</option>
                            <option value="PS">Palestine</option>
                            <option value="PA">Panama</option>
                            <option value="PG">Papua New Guinea</option>
                            <option value="PY">Paraguay</option>
                            <option value="PE">Peru</option>
                            <option value="PH">Philippines</option>
                            <option value="PO">Pitcairn Island</option>
                            <option value="PL">Poland</option>
                            <option value="PT">Portugal</option>
                            <option value="PR">Puerto Rico</option>
                            <option value="QA">Qatar</option>
                            <option value="ME">Republic of Montenegro</option>
                            <option value="RS">Republic of Serbia</option>
                            <option value="RE">Reunion</option>
                            <option value="RO">Romania</option>
                            <option value="RU">Russia</option>
                            <option value="RW">Rwanda</option>
                            <option value="NT">St Barthelemy</option>
                            <option value="EU">St Eustatius</option>
                            <option value="HE">St Helena</option>
                            <option value="KN">St Kitts-Nevis</option>
                            <option value="LC">St Lucia</option>
                            <option value="MB">St Maarten</option>
                            <option value="PM">St Pierre &amp; Miquelon</option>
                            <option value="VC">St Vincent &amp; Grenadines</option>
                            <option value="SP">Saipan</option>
                            <option value="SO">Samoa</option>
                            <option value="AS">Samoa American</option>
                            <option value="SM">San Marino</option>
                            <option value="ST">Sao Tome &amp; Principe</option>
                            <option value="SA">Saudi Arabia</option>
                            <option value="SN">Senegal</option>
                            <option value="SC">Seychelles</option>
                            <option value="SL">Sierra Leone</option>
                            <option value="SG">Singapore</option>
                            <option value="SK">Slovakia</option>
                            <option value="SI">Slovenia</option>
                            <option value="SB">Solomon Islands</option>
                            <option value="OI">Somalia</option>
                            <option value="ZA">South Africa</option>
                            <option value="ES">Spain</option>
                            <option value="LK">Sri Lanka</option>
                            <option value="SD">Sudan</option>
                            <option value="SR">Suriname</option>
                            <option value="SZ">Swaziland</option>
                            <option value="SE">Sweden</option>
                            <option value="CH">Switzerland</option>
                            <option value="SY">Syria</option>
                            <option value="TA">Tahiti</option>
                            <option value="TW">Taiwan</option>
                            <option value="TJ">Tajikistan</option>
                            <option value="TZ">Tanzania</option>
                            <option value="TH">Thailand</option>
                            <option value="TG">Togo</option>
                            <option value="TK">Tokelau</option>
                            <option value="TO">Tonga</option>
                            <option value="TT">Trinidad &amp; Tobago</option>
                            <option value="TN">Tunisia</option>
                            <option value="TR">Turkey</option>
                            <option value="TU">Turkmenistan</option>
                            <option value="TC">Turks &amp; Caicos Is</option>
                            <option value="TV">Tuvalu</option>
                            <option value="UG">Uganda</option>
                            <option value="UA">Ukraine</option>
                            <option value="AE">United Arab Emirates</option>
                            <option value="GB">United Kingdom</option>
                            <option value="UY">Uruguay</option>
                            <option value="UZ">Uzbekistan</option>
                            <option value="VU">Vanuatu</option>
                            <option value="VS">Vatican City State</option>
                            <option value="VE">Venezuela</option>
                            <option value="VN">Vietnam</option>
                            <option value="VB">Virgin Islands (Brit)</option>
                            <option value="VA">Virgin Islands (USA)</option>
                            <option value="WK">Wake Island</option>
                            <option value="WF">Wallis &amp; Futana Is</option>
                            <option value="YE">Yemen</option>
                            <option value="ZR">Zaire</option>
                            <option value="ZM">Zambia</option>
                            <option value="ZW">Zimbabwe</option>
                        </select>
        </div>
    </div>
    <div class="row">
    	<div class="form-group col-md-12">
            <label>Address<span class="required">*</span></label>
            <input id="addressOne2" name="address2" type="text" class="form-control" placeholder="Street Address" />
            <input id="addressTwo2" name="addressTwo2" type="text" class="form-control" placeholder="Apartment, suit, unit etc. (optional)" />
        </div>
    </div>
    <div class="row">
    	<div class="form-group col-md-12">
            <label>City / Town<span class="required">*</span></label>
            <input id="city2" name="city2" type="text" class="form-control" placeholder="City / Town" />
        </div>
    </div>
    <div class="row">
    	<div class="form-group col-md-6">
        <div id="stateDropdown2">
            <label>State<span class="required">*</span></label>
            <select class="form-control" id="state2" name="state2" onchange="shippingState(this.value)">
                            <option value="">Select State</option>
                            <option id="USA-AL" value="AL">Alabama (AL)</option>
                            <option id="USA-AK" value="AK">Alaska (AK)</option>
                            <option id="USA-AZ" value="AZ">Arizona (AZ)</option>
                            <option id="USA-AR" value="AR">Arkansas (AR)</option>             
                            <option id="USA-CA" value="CA">California (CA)</option>
                            <option id="USA-CO" value="CO">Colorado (CO)</option>
                            <option id="USA-CT" value="CT">Connecticut (CT)</option>
                            <option id="USA-DE" value="DE">Delaware (DE)</option>
                            <option id="USA-DC" value="DC">District of Columbia (DC)</option>    
                            <option id="USA-FL" value="FL">Florida (FL)</option>
                            <option id="USA-GA" value="GA">Georgia (GA)</option>
                            <option id="USA-GU" value="GU">Guam (GU)</option>
                            <option id="USA-HI" value="HI">Hawaii (HI)</option>
                            <option id="USA-ID" value="ID">Idaho (ID)</option>
                            <option id="USA-IL" value="IL">Illinois (IL)</option>
                            <option id="USA-IN" value="IN">Indiana (IN)</option>
                            <option id="USA-IA" value="IA">Iowa (IA)</option>
                            <option id="USA-KS" value="KS">Kansas (KS)</option>
                            <option id="USA-KY" value="KY">Kentucky (KY)</option>
                            <option id="USA-LA" value="LA">Louisiana (LA)</option>
                            <option id="USA-ME" value="ME">Maine (ME)</option>
                            <option id="USA-MD" value="MD">Maryland (MD)</option>
                            <option id="USA-MA" value="MA">Massachusetts (MA)</option>
                            <option id="USA-MI" value="MI">Michigan (MI)</option>
                            <option id="USA-MN" value="MN">Minnesota (MN)</option>
                            <option id="USA-MS" value="MS">Mississippi (MS)</option>
                            <option id="USA-MO" value="MO">Missouri (MO)</option>
                            <option id="USA-MT" value="MT">Montana (MT)</option>
                            <option id="USA-NE" value="NE">Nebraska (NE)</option>
                            <option id="USA-NV" value="NV">Nevada (NV)</option>
                            <option id="USA-NH" value="NH">New Hampshire (NH)</option>
                            <option id="USA-NJ" value="NJ">New Jersey (NJ)</option>
                            <option id="USA-NM" value="NM">New Mexico (NM)</option>
                            <option id="USA-NY" value="NY">New York (NY)</option>
                            <option id="USA-NC" value="NC">North Carolina (NC)</option>
                            <option id="USA-ND" value="ND">North Dakota (ND)</option>
                            <option id="USA-OH" value="OH">Ohio (OH)</option>
                            <option id="USA-OK" value="OK">Oklahoma (OK)</option>
                            <option id="USA-OR" value="OR">Oregon (OR)</option>
                            <option id="USA-PA" value="PA">Pennsylvania (PA)</option>
                            <option id="USA-PR" value="PR">Puerto Rico (PR)</option>
                            <option id="USA-RI" value="RI">Rhode Island (RI)</option>
                            <option id="USA-SC" value="SC">South Carolina (SC)</option>
                            <option id="USA-SD" value="SD">South Dakota (SD)</option>
                            <option id="USA-TN" value="TN">Tennessee (TN)</option>
                            <option id="USA-TX" value="TX">Texas (TX)</option>
                            <option id="USA-UT" value="UT">Utah (UT)</option>
                            <option id="USA-VT" value="VT">Vermont (VT)</option>
                            <option id="USA-VA" value="VA">Virginia (VA)</option>
                            <option id="USA-VI" value="VI">Virgin Islands (VI)</option>
                            <option id="USA-WA" value="WA">Washington (WA)</option>
                            <option id="USA-WV" value="WV">West Virginia (WV)</option>
                            <option id="USA-WI" value="WI">Wisconsin (WI)</option>
                            <option id="USA-WY" value="WY">Wyoming (WY)</option>
                        </select>
        </div>
        <div id="stateField2" style="display:none;">
            <label>State/Province<span class="required">*</span></label>
            <input type="text" class="form-control" name="province2" placeholder="State/Province">
        </div>
        </div>
        <div class="form-group col-md-6">
            <label>Zip<span class="required">*</span></label>
            <input id="zip2" name="zip2" type="text" class="form-control" placeholder="Zip" />
        </div>
    </div>
    </div><!--Shipping initially hidden-->
</div>
<? } ?><!--Shipping initially hidden-->

</div>
<div class="col-md-6">

<h3>Your Order</h3>
	<div class="img-rounded grayborder">
    	<table align="center" class="table">
        	<thead>
            	<tr style="background:#ededed;">
                	<th>Product</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
<?php
$items = explode('|',$cart);
$contents = array();
$i=1;
foreach ($items as $specs) { 
	$cubes=explode(',', $specs);
	$cube_id=$cubes[0];
	$cube_num=$cubes[1];
	
$cart_query=mysql_query("SELECT * FROM products WHERE ItemID=". sanitize( $cube_id));
$row_cart=mysql_fetch_object($cart_query);
?>
            	<tr>
                	<td><? echo $row_cart->ProductName; ?> <strong>x <? echo $cube_num; ?></strong></td>
                    <td><? echo '$'.number_format($row_cart->Price, 2, '.', ','); ?></td>
                    <td><? echo '$'.number_format(($row_cart->Price*$cube_num), 2, '.', ','); ?></td>
                </tr>
<?php $total=$total+($row_cart->Price*$cube_num); $i= $i+1; } 
if($promo){
	$query_promo=mysql_query("SELECT * FROM promos WHERE PromoCode='". sanitize( $promo)."'");
	$row_promo=mysql_fetch_object($query_promo);
	if($row_promo->ExpiresOn >=date('Y-m-d H:i:s')){
		if($row_promo->PromoType=='PercentOff'){
			$percentOff=$row_promo->AmountOff/100;
			$promo_discount=$percentOff*$total;
		}
	}else{
		$promo_discount=0;
	}
}
if($promo_discount){ 
?>
				<tr>
                	<td colspan="3">Promo Codes: <? echo $promo.' '.$row_promo->AmountOff.'% off entire order' ; ?></td>
                </tr>
<? } ?>
				<tr>
                	<td><strong>Cart Subtotal:</strong></td>
                    <td></td>
                    <td><? echo '$'.number_format($total, 2, '.', ','); ?></td>
                </tr>
<?php
if($shippingTotal){ 
?>
				<tr id="shipRow" class="alert alert-warning">
                	<td><strong>Shipping Total:</strong></td>
                    <td></td>
                    <td><div id="shipAmount">Calculating</div></td>
                </tr>
<? } ?>
				<tr id="taxRow" class="alert alert-warning" style="display:none;">
                	<td><strong>Taxes Total:</strong></td>
                    <td></td>
                    <td><div id="taxAmount">Calculating</div></td>
                </tr>
<?php
if($promo_discount){ 
?>
				<tr>
                	<td><strong>Promo Discount:</strong></td>
                    <td></td>
                    <td>-$<? echo number_format($promo_discount, 2, '.', ','); ?></td>
                </tr>
<? } ?>
                <tr style="display:none;">
                	<td><strong>Order Total:</strong></td>
                    <td></td>
                    <td>$<? echo number_format(($total-$promo_discount), 2, '.', ','); ?></td>
                </tr>
                <tr>
                	<td><strong>Order Total:</strong></td>
                    <td></td>
                    <td id="order_total">$<? echo number_format(($total-$promo_discount), 2, '.', ','); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <h3>Payment Options <span class="text-14">(Credit Card or Purchase Order)</span></h3>
    <div class="img-rounded grayborder">
    	<table class="table">
        	<thead>
            <tr>
            	<th style="background:#ededed;"><div class="radio"><label><input name="payment_type" type="radio" value="cc" checked id="payment_cc" onChange="displayPayment();" /><strong>Credit Card</strong></label><img src="/store/images/credit-cards2.png" height="32" style="padding-left:20px;"></div></th>
            </tr>
            </thead>
            <tr>
            	<td>
                <div id="displayCC">
                	<div class="row">
                    	<div class="form-group col-md-12">
                        	<label>Card Number<span class="required">*</span></label>
            				<input id="ccnum" name="ccnum" type="text" class="form-control" placeholder="Credit Card Number" />
                        </div>
                    </div>
                    <div class="row">
                    	<div class="form-group col-md-4">
                        	<label>Exp Month<span class="required">*</span></label>
                            <select name="exp_month" id="exp_month" class="form-control">
                            	<option value="">Select Month</option>
                                    <option value='01'>Janaury (01)</option>
                                    <option value='02'>February (02)</option>
                                    <option value='03'>March (03)</option>
                                    <option value='04'>April (04)</option>
                                    <option value='05'>May (05)</option>
                                    <option value='06'>June (06)</option>
                                    <option value='07'>July (07)</option>
                                    <option value='08'>August (08)</option>
                                    <option value='09'>September (09)</option>
                                    <option value='10'>October (10)</option>
                                    <option value='11'>November (11)</option>
                                    <option value='12'>December (12)</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                        	<label>Exp Year<span class="required">*</span></label>
<?
$year = date ('Y'); 
$years = range ($year, $year + 25); 

echo '<select name="exp_year" id="exp_year" class="form-control">'; 
echo '<option value="">Select Year</option>';
foreach ($years as $value) {echo '<option>' . $value;} 
echo '</select>'; 
?>
                        </div>
                        <div class="form-group col-md-4">
                        	<label>Card Code<span class="required">*</span></label>
            				<input id="cccode" name="cccode" type="text" class="form-control" placeholder="CVC" />
                        </div>
                    </div>
                </div>
                </td>
            </tr>
            <tr>
            	<td style="background:#ededed;"><div class="radio"><label><input name="payment_type" type="radio" value="po" id="payment_po" onChange="displayPayment();" /><strong>Purchase Order</strong></label></div></td>
            </tr>
            <tr>
            	<td>
                <div id="displayPO" style="display:none;">
                	<div class="row">
                    	<div class="form-group col-md-6">
                        	<label>Name on P.O.<span class="required">*</span></label>
            				<input id="poName" name="poName" type="text" class="form-control" placeholder="Name on P.O." />
                        </div>
                        <div class="form-group col-md-6">
                        	<label>P.O. Number<span class="required">*</span></label>
            				<input id="poNum" name="poNum" type="text" class="form-control" placeholder="P.O. Number" />
                        </div>
                    </div>
                </div>
                </td>
            </tr>
        </table>        
    </div>
    
    <div class="row alert alert-success" style="margin-top:10px;padding-left:0px;margin-left:0px;padding-right:0px;margin-right:0px;">
        <div class="col-md-6" style="font-size:20px;">
        	<strong>Total:</strong> <span id="cc_total">$<? echo number_format(($total-$promo_discount), 2, '.', ','); ?></span>
        </div>
        <div class="col-md-6 text-right">
            <input name="" type="submit" value="Place Order" class="btn btn-primary" />
        </div>
    </div>
    
</div>
</div>
	<input type="hidden" name="new_district" id="new_district" placeholder="District">
    <input type="hidden" name="new_school" id="new_school" placeholder="School">
    <input type="hidden" name="international" id="international" placeholder="International">
    <input type="hidden" name="needs_license" id="needs_license" placeholder="License" value="<? echo $license; ?>">
    <input type="hidden" name="item_count" id="item_count" placeholder="Item Count" value="<? echo $cart_num; ?>">
    <input type="hidden" name="billing_country" id="billing_country" placeholder="Billing Country">
    <input type="hidden" name="billing_state" id="billing_state" placeholder="Billing State">
    <input type="hidden" name="shipping_country" id="shipping_country" placeholder="Shipping Country">
    <input type="hidden" name="shipping_state" id="shipping_state" placeholder="Shipping State">
    <input type="hidden" name="cartVal" id="cartVal" placeholder="Cart Total" value="<? echo $total; ?>">
    <input type="hidden" name="totalVal" id="totalVal" placeholder="Total" value="<? echo $total; ?>">
    <input type="hidden" name="taxesTotalVal" id="taxesTotalVal" placeholder="Tax Total">
    <input type="hidden" name="shipTotalVal" id="shipTotalVal" placeholder="Shipping Total">
    <input type="hidden" name="promoTotalVal" id="promoTotalVal" placeholder="Promo Total" value="<? echo $promo_discount; ?>">
    <input type="hidden" name="paypalTrans" id="paypalTrans" placeholder="Paypal Transaction">


</form>
    </div>
<? include('includes/footer.php');?>