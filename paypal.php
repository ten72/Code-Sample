<?
header("Content-Type: application/xhtml+xml; charset=utf-8");
echo '<?xml version="1.0" encoding="UTF-8"?>';

$sandbox=false;
$action=$_REQUEST['action'];

if($sandbox){
$api_username="mike-facilitator_api1.ten72.com";
$api_password="7E998CL62W2JA64Z";
$api_signature="AXgA4TjFHUajaCv5ZXVGgghBPVkcATcYeu0gr6K2JVOpruUHUwzM.Ns7";
$url = 'https://api-3t.sandbox.paypal.com/nvp';
}else{
$api_username="jared.mair_api1.schoolimprovement.com";
$api_password="DQCYGWTWPURL8TTP";
$api_signature="Ag88BHGfnOFX0eLCDwjRgXXzkwTMA5YNdF1oznu3KoHpQ2Cfmc9LK1e7";
$url = 'https://api-3t.paypal.com/nvp';
}

$amount=$_REQUEST['totalVal'];
$ccnum=$_REQUEST['ccnum'];
$card_type=$_REQUEST['type'];
$cvv2=$_REQUEST['cccode'];
$exp=$_REQUEST['expire']; //052015    #Expiration date of the credit card
$firstname=$_REQUEST['firstName'];
$lastname=$_REQUEST['lastName'];
$street=$_REQUEST['address'];
$city=$_REQUEST['city'];
$state=$_REQUEST['state'];
$zip=$_REQUEST['zip'];
$country=$_REQUEST['country'];

if($action=='verify'){
	$post_data['USER'] = $api_username;
	$post_data['PWD'] = $api_password;
	$post_data['SIGNATURE'] = $api_signature;
	$post_data['METHOD'] = 'DoDirectPayment';
	$post_data['IPADDRESS'] = '127.0.0.1';
	$post_data['VERSION'] = '86';
	$post_data['PAYMENTACTION'] = 'Authorization';
	$post_data['AMT'] = $amount;
	$post_data['ACCT'] = $ccnum;
	$post_data['EXPDATE'] = $exp;
	$post_data['CREDITCARDTYPE'] = $card_type;
	$post_data['CVV2'] = $cvv2;
	$post_data['FIRSTNAME'] = $firstname;
	$post_data['LASTNAME'] = $lastname;
	$post_data['STREET'] = $street;
	$post_data['CITY'] = $city;
	$post_data['STATE'] = $state;
	$post_data['ZIP'] = $zip;
	$post_data['COUNTRYCODE'] = $country;
	$post_data['CURRENCYCODE'] = 'USD';
	
	
	$query_string = http_build_query($post_data);
	
	
	//Check to see if cURL is installed ...
	if (!function_exists('curl_init')){
		die('Sorry cURL is not installed!');
	}
	
	//Open cURL connection
	$ch = curl_init();
	
	//Set the url, number of POST vars, POST data
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
	
	//Set some settings that make it all work :)
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
	
	//Execute SalesForce web to lead PHP cURL
	$result = curl_exec($ch);
	
	//Parse $Response and handle values
	$decoderesponse = explode ('&', $result);
	
	foreach($decoderesponse as $key => $value){
		$getvalues = explode ('=', $value);
			switch ($getvalues[0]){
				case "ACK":
				$ack = htmlspecialchars(urldecode($getvalues[1]));
					if($ack=='Success'){
						$success='Success';
					}else{
						$success='Fail';
					}
				break;
				case "TRANSACTIONID":
				$transaction = htmlspecialchars(urldecode($getvalues[1]));
				break; 
				case "L_ERRORCODE0":
				$error_code = htmlspecialchars(urldecode($getvalues[1]));
				break;
				case "L_SHORTMESSAGE0":
				$error_short = htmlspecialchars(urldecode($getvalues[1]));
				break;  
				case "L_LONGMESSAGE0":
				$error_long = htmlspecialchars(urldecode($getvalues[1]));
				break;   
				}
			}
	if($success=='Success'){
		echo '<Response>';
		
		echo '<Status>';
		echo 'Success';
		echo '</Status>';
		echo '<Transaction>';
		echo $transaction;
		echo '</Transaction>';
		echo '</Response>';
		
	}else{
		echo '<Response>';
		echo '<Status>';
		echo 'Fail';
		echo '</Status>';
		echo '<CodeError>';
		echo $error_code;
		echo '</CodeError>';
		echo '<ShortError>';
		echo $error_short;
		echo '</ShortError>';
		echo '<Error>';
		echo $error_long;
		echo '</Error>';
		echo '</Response>';
	}
	//Your code to display or handle values returned.........
	
	//close cURL connection
	curl_close($ch);
}
if($action=='charge'){
	$transaction=$_REQUEST['transaction'];
	$transaction_id=$_REQUEST['transaction_id'];
	$amount=$_REQUEST['amount'];
	$success='Success';
	if($success=='Success' && $transaction){
	
		$post_data2['USER'] = $api_username;
		$post_data2['PWD'] = $api_password;
		$post_data2['SIGNATURE'] = $api_signature;
		$post_data2['METHOD'] = 'DoCapture';
		$post_data2['VERSION'] = '86';
		$post_data2['AUTHORIZATIONID'] = $transaction;
		$post_data2['AMT'] = $amount;
		$post_data2['CURRENCYCODE'] = 'USD';
		$post_data2['COMPLETETYPE'] = 'Complete';
		
		
		$query_string2 = http_build_query($post_data2);
		
		//Open cURL connection
		$ch2 = curl_init();
		
		//Set the url, number of POST vars, POST data
		curl_setopt($ch2, CURLOPT_URL, $url);
		curl_setopt($ch2, CURLOPT_POST, 1);
		curl_setopt($ch2, CURLOPT_POSTFIELDS, $query_string2);
		
		//Set some settings that make it all work :)
		curl_setopt($ch2, CURLOPT_HEADER, FALSE);
		curl_setopt($ch2, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, FALSE);
		
		//Execute SalesForce web to lead PHP cURL
		$result2 = curl_exec($ch2);
		
		$decoderesponse2 = explode ('&', $result2);
		
		echo '<Response>';
		foreach($decoderesponse2 as $key2 => $value2){
			$getvalues2 = explode ('=', $value2);
			echo '<'.$getvalues2[0].'>'.htmlspecialchars(urldecode($getvalues2[1])).'</'.$getvalues2[0].'>';
		}
		echo '<Status>';
		echo 'Success';
		echo '</Status>';
		echo '</Response>';
		header("Location:complete.php?transaction_id=$transaction_id");
	}else{
		echo '<Response>';
		foreach($decoderesponse as $key => $value){
			$getvalues = explode ('=', $value);
			echo '<'.$getvalues[0].'>'.htmlspecialchars(urldecode($getvalues[1])).'</'.$getvalues[0].'>';
		}
		echo '<Status>';
		echo 'Fail';
		echo '</Status>';
		echo '<Error>';
		echo $error_long;
		echo '</Error>';
		echo '</Response>';
	}
}



if($action=='subscription'){
	$date_start=date("Y-m-d").'T00:00:00Z';
	$post_data2['USER'] = $api_username;
	$post_data2['PWD'] = $api_password;
	$post_data2['SIGNATURE'] = $api_signature;
	$post_data2['METHOD'] = 'CreateRecurringPaymentsProfile';
	$post_data2['VERSION'] = '86';
	$post_data2['PROFILESTARTDATE'] = $date_start;//2012-05-11T00:00:00Z    #Billing date start, in UTC/GMT format
	$post_data2['DESC'] = 'Edivate Subscription';
	$post_data2['BILLINGPERIOD'] = 'Day';
	$post_data2['BILLINGFREQUENCY'] = '1';
	$post_data2['AMT'] = '9.99';
	$post_data2['MAXFAILEDPAYMENTS'] = '3';
	$post_data2['ACCT'] = '379635332181003';
	//$post_data2['ACCT'] = $ccnum;
	$post_data2['EXPDATE'] = '032019';
	//$post_data2['EXPDATE'] = $exp;
	$post_data2['CREDITCARDTYPE'] = $card_type;
	$post_data2['CVV2'] = '9845';
	//$post_data2['CVV2'] = $cvv2;
	$post_data2['FIRSTNAME'] = 'Mike';
	$post_data2['LASTNAME'] = 'Thomas';
	$post_data2['STREET'] = '7933 N Smith Ranch Rd';
	$post_data2['CITY'] = 'Eagle Mountain';
	$post_data2['STATE'] = 'UT';
	$post_data2['ZIP'] = '84005';
	$post_data2['COUNTRYCODE'] = 'US';
	/*
	$post_data2['FIRSTNAME'] = $firstname;
	$post_data2['LASTNAME'] = $lastname;
	$post_data2['STREET'] = $street;
	$post_data2['CITY'] = $city;
	$post_data2['STATE'] = $state;
	$post_data2['ZIP'] = $zip;
	$post_data2['COUNTRYCODE'] = $country;
	*/
	$post_data2['CURRENCYCODE'] = 'USD';
	
	
	$query_string2 = http_build_query($post_data2);
	
	//Open cURL connection
	$ch2 = curl_init();
	
	//Set the url, number of POST vars, POST data
	curl_setopt($ch2, CURLOPT_URL, $url);
	curl_setopt($ch2, CURLOPT_POST, 1);
	curl_setopt($ch2, CURLOPT_POSTFIELDS, $query_string2);
	
	//Set some settings that make it all work :)
	curl_setopt($ch2, CURLOPT_HEADER, FALSE);
	curl_setopt($ch2, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, FALSE);
	
	//Execute SalesForce web to lead PHP cURL
	$result2 = curl_exec($ch2);
	
	$decoderesponse2 = explode ('&', $result2);
	
	echo '<Response>';
	foreach($decoderesponse2 as $key2 => $value2){
		$getvalues2 = explode ('=', $value2);
		echo '<'.$getvalues2[0].'>'.htmlspecialchars(urldecode($getvalues2[1])).'</'.$getvalues2[0].'>';
	}
	echo '<Status>';
	echo 'Success';
	echo '</Status>';
	echo '</Response>';
}
/*
Request 
-------
Endpoint URL: https://api-3t.sandbox.paypal.com/nvp
HTTP method: POST
POST data:
USER=insert_merchant_user_name_here
&PWD=insert_merchant_password_here
&SIGNATURE=insert_merchant_signature_value_here
&METHOD=CreateRecurringPaymentsProfile
&PROFILESTARTDATE=2012-05-11T00:00:00Z    #Billing date start, in UTC/GMT format
&DESC=RacquetClubMembership    #Profile description - same value as a billing agreement description
&BILLINGPERIOD=Month    #Period of time between billings
&BILLINGFREQUENCY=1    #Frequency of charges 
&AMT=10    #The amount the buyer will pay in a payment period
&MAXFAILEDPAYMENTS=3    #Maximum failed payments before suspension of the profile
&ACCT=4641631486853053    #The credit card number
&CREDITCARDTYPE=VISA    #The type of credit card 
&CVV2=123    #The CVV2 number
&FIRSTNAME=James
&LASTNAME=Smith
&STREET=FirstStreet
&CITY=SanJose
&STATE=CA
&ZIP=95131
&COUNTRYCODE=US    #The country code, e.g. US  
&CURRENCYCODE=USD    #The currency, e.g. US dollars
&EXPDATE=052015     #Expiration date of the credit card

Response 
-------
PROFILEID=I%2dWMA886VL1234
&PROFILESTATUS=ActiveProfile
&ACK=Success
...
*/
?>