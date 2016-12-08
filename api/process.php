<table>
<?php 


    foreach ($_POST as $key => $value) {
        echo "<tr>";
        echo "<td>";
        echo $key;
        echo "</td>";
        echo "<td>";
        echo $value;
        echo "</td>";
        echo "</tr>";
    }


?>
</table>
<?PHP
if(session_id() == '') {
    session_start();
}
$url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$new_url=str_replace('%2F', '/', $url);
include("admin/security.php");
include('admin/db.php');
$promo=$_SESSION['promo'];
$cart=$_SESSION['cart'];
if(!$cart){
	header("Location:view_cart.php");
}

$promo_discount=0;
$total=0;
$shipping=0;
$license_pd360=0;
$license_lumibook=0;
$taxesTotal=0;
$shippingTotal=0;

$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$headers .= 'From: Order Complete <no-reply@schoolimprovementnetwork.com>' . "\r\n";

$first=html_sanitize($_POST['firstName']);
$last=html_sanitize($_POST['lastName']);
$email=html_sanitize($_POST['email']);
$phone=html_sanitize($_POST['phone']);
$addressOne=html_sanitize($_POST['address']);
$addressTwo=html_sanitize($_POST['addressTwo']);
$city=html_sanitize($_POST['city']);
if($_POST['province']){
	$state=html_sanitize($_POST['province']);
}else{
	$state=html_sanitize($_POST['state']);
}
$zip=html_sanitize($_POST['zip']);
$country=html_sanitize($_POST['country']);

$addressOne2=html_sanitize($_POST['address2']);
$addressTwo2=html_sanitize($_POST['addressTwo2']);
$city2=html_sanitize($_POST['city2']);
if($_POST['province2']){
	$state2=html_sanitize($_POST['province2']);
}else{
	$state2=html_sanitize($_POST['state2']);
}
$zip2=html_sanitize($_POST['zip2']);
$country2=html_sanitize($_POST['country2']);

$district=html_sanitize($_POST['district']);
$school=html_sanitize($_POST['school']);

$district_value=html_sanitize($_POST['new_district']);
$school_value=html_sanitize($_POST['new_school']);

if($school){
	$pd360_email=$email;
	$license_state=$state;
}

$ccnum=html_sanitize($_POST['ccnum']);
$expire=html_sanitize($_POST['expire']);
$cccode=html_sanitize($_POST['cccode']);

$poName=html_sanitize($_POST['poName']);
$poNum=html_sanitize($_POST['poNum']);


$needs_license=html_sanitize($_POST['needs_license']);
$item_count=html_sanitize($_POST['item_count']);
$billing_country=html_sanitize($_POST['billing_country']);
$billing_state=html_sanitize($_POST['billing_state']);
$shipping_country=html_sanitize($_POST['shipping_country']);
$shipping_state=html_sanitize($_POST['shipping_state']);
$cartVal=html_sanitize($_POST['cartVal']);
$totalVal=html_sanitize($_POST['totalVal']);
$taxesTotalVal=html_sanitize($_POST['taxesTotalVal']);
$shipTotalVal=html_sanitize($_POST['shipTotalVal']);
$paypalTrans=html_sanitize($_POST['paypalTrans']);


//insert to DB
if($poName && $poNum){
	$payment_type='Purchase Order';
}else{
	$payment_type='CC';
}
/*$db2 = mysql_connect(DB_HOST, 'mike.thomas', 'OcEr6OabTo') or die(mysql_error());
mysql_select_db('sinet_dev', $db2);
$altertable='ALTER TABLE orders ADD ship_amount int(11)';
mysql_query($altertable, $db2) or die(mysql_error());*/
//id, transaction_id, first, last, email, phone, district, addressOne, addressTwo, city, state, zip, country, first2, last2, addressOne2, addressTwo2, city2, state2, zip2, country2, poName, poNum, order_status, status, date, cart, discountCode, amount, pd360_account, pd360_email, browser, lbState, lbDistrict, lbSchool, license_status, school_id
$SQL="INSERT INTO orders (first, last, email, phone, district, addressOne, addressTwo, city, state, zip, country, addressOne2, addressTwo2, city2, state2, zip2, country2, poName, poNum, order_status, status, date, cart, discountCode, amount, pd360_account, pd360_email, browser, lbState, lbDistrict, lbSchool, school_id, tax_amount, ship_amount) VALUES ('".sanitize($first)."', '".sanitize($last)."', '".sanitize($email)."', '".sanitize($phone)."', '".sanitize($district_value)."', '".sanitize($addressOne)."', '".sanitize($addressTwo)."', '".sanitize($city)."', '".sanitize($state)."', '".sanitize($zip)."', '".sanitize($country)."', '".sanitize($addressOne2)."', '".sanitize($addressTwo2)."', '".sanitize($city2)."', '".sanitize($state2)."', '".sanitize($zip2)."', '".sanitize($country2)."', '".sanitize($poName)."', '".sanitize($poNum)."', 'License Error', '".sanitize($payment_type)."', NOW(), '".sanitize($cart)."', '".sanitize($promo)."', '".sanitize($totalVal)."', '', '".sanitize($email)."', '', '".sanitize($license_state)."', '".sanitize($district_value)."', '".sanitize($school_value)."', '".sanitize($school)."', '".sanitize($taxesTotalVal)."', '".sanitize($shipTotalVal)."')";

mysql_query($SQL);
$id=mysql_insert_id();
$transaction = uniqid(rand(), true ). md5( rand( 0, 1000000000 ). microtime(). date( 'c' ));
mysql_query("UPDATE orders SET transaction_id='". sanitize( $transaction)."' WHERE id='". sanitize( $id)."'");


$query_order=mysql_query("SELECT * FROM orders WHERE transaction_id='".$transaction."'", $db);
$row_order=mysql_fetch_object($query_order);

$promo=$row_order->discountCode;
$cart_new=$row_order->cart;

$items_count = explode('|',$cart_new);
$contents_count = array();
foreach ($items_count as $specs_count) { 
	$cubes_count=explode(',', $specs_count);
	$cube_id_count=$cubes_count[0];
	$cube_num_count=$cubes_count[1];
	$cart_num_count=$cart_num_count+$cube_num_count;
}

//Check if cart has License Requirements
$items2 = explode('|',$cart_new);
$contents2 = array();
foreach ($items2 as $specs2) { 
	$cubes2=explode(',', $specs2);
	$cube_id2=$cubes2[0];
	$cube_num2=$cubes2[1];
	
$cart_query2=mysql_query("SELECT * FROM products WHERE ItemID=".sanitize($cube_id2), $db); // error possible 1
$row_cart2=mysql_fetch_object($cart_query2);
	if($row_cart2->license_type){
		$license=true;
		switch($row_cart2->license_type){
			case '5':
				$license_pd360=TRUE;
				break;
			case '1':
				$license_lumibook=TRUE;
				break;
		}
	}
}


$billing=$first.' '.$last.'<br>';
$billing.='<a href="mailto:'.$email.'">'.$email.'</a><br>';
$billing.='Phone: '.$phone.'<br>';
$billing.=$addressOne.'<br>';
if($addressTwo){ $billing.=$addressTwo.'<br>'; }
$billing.=$city.', '.$state.' '.$zip.'<br>';
$billing.=$country;

if($addressOne2){
	$shipping=$first.' '.$last.'<br>';
	$shipping.=$addressOne2.'<br>';
	if($addressTwo2){ $shipping.=$addressTwo2.'<br>'; }
	$shipping.=$city2.', '.$state2.' '.$zip2.'<br>';
	$shipping.=$country2;
}


//process license
if($license==true){
include('api/license-functions.php');
	if($school){
		echo '<br>create user script';
		echo "<br>createNewUser($first, $last, $email, $school, $password, $new_url)";
		$createNewUser=createNewUser($first, $last, $email, $school, $password, $new_url, $new_url);
		if($createNewUser!='Success'){
			$error_createNewUser=1;
		}else{
			$error_createNewUser=0;
		}
	}
	if($_REQUEST['international']=='yes'){
		echo '<br>create user script';
		echo "<br>createNewUser($first, $last, $email, '360-455186', $password, $new_url)";
		$createNewUser=createNewUser($first, $last, $email, '360-455186', $password, $new_url, $new_url);
		if($createNewUser!='Success'){
			$error_createNewUser=1;
		}else{
			$error_createNewUser=0;
		}
	}
	

	$license_items = explode('|',$cart_new);
	$license_contents = array();
	$license_i=1;
	foreach ($license_items as $license_specs) { 
		$license_cubes=explode(',', $license_specs);
		$license_cube_id=$license_cubes[0];
		$license_cube_num=$license_cubes[1];
		
		$cart_license_query=mysql_query("SELECT * FROM products WHERE ItemID=".sanitize($license_cube_id), $db);
		$row_license_cart=mysql_fetch_object($cart_license_query);
		if($row_license_cart->license_type==1 && $row_license_cart->license_id=='67783'){ //livebook mapping to the core bundle
			echo "<br>Mapping to the core bundle";
			echo "<br>licenseSeat($pd360_email, '67783', $password, $new_url)";
			echo "<br>licenseSeat($pd360_email, '77657', $password, $new_url)";
			$licenseSeat1=licenseSeat($pd360_email, '67783', $password, $new_url, $new_url);
			$licenseSeat2=licenseSeat($pd360_email, '77657', $password, $new_url, $new_url);
			if($licenseSeat1!='Success' || $licenseSeat2!='Success'){
				$error_licenseSeat=1;
			}
		}
		if($row_license_cart->license_type==1 && $row_license_cart->license_id!='67783'){ //livebook
			echo "<br>Add LumiBook license.";
			echo "<br>licenseSeat($email, $row_license_cart->license_id, $password, $new_url)";
			$licenseSeat3=licenseSeat($pd360_email, $row_license_cart->license_id, $password, $new_url, $new_url);
			if($licenseSeat3!='Success'){
				$error_licenseSeat=1;
			}
		}
		if($row_license_cart->license_type==2){ //courses
			echo "<br>Add to Course";
			echo "<br>licenseSeat($email, $row_license_cart->license_id, $password, $new_url)";
			$licenseSeat4=licenseSeat($pd360_email, $row_license_cart->license_id, $password, $new_url, $new_url);
			if($licenseSeat4!='Success'){
				$error_licenseSeat=1;
			}
		}
		if($row_license_cart->license_type==5){ //System PD 360 License and CC License
		$db2 = mysql_connect("slave2.pd360.com", "sinetRO", "lirkyoosfeblaf4") or die(mysql_error());
		mysql_select_db("PD360v2", $db2);
		$query=mysql_query("SELECT * FROM ClientPersonnel WHERE EmailAddress='".mysql_real_escape_string(stripslashes($email))."' AND Remover IS null"); // AND Remover IS null
		$row=mysql_fetch_object($query);
		if($row->PersonnelId){
			echo '<PD360Status><PersonnelId>'.$row->PersonnelId.'</PersonnelId></PD360Status>';
			echo '<br>'.$row->FirstName.'<br>';
			echo $row->LastName.'<br>';
			$pd360_id=$row->PersonnelId;
			echo '<br>Add Individual License';
			$licenseName1=$first.'-'.$last.'-PD360-WEB-STORE';
			$licenseName2=$first.'-'.$last.'-CC360-WEB-STORE';
			echo "<br>pd360License($pd360_id, $licenseName1, '1', '1', $password)";
			echo "<br>pd360License($pd360_id, $licenseName2, '400', '147', $password)";
			$pd360License1=pd360License($pd360_id, $licenseName1, '1', '1', $password);
			$pd360License2=pd360License($pd360_id, $licenseName2, '400', '147', $password);
			if($pd360License1!='Success' || $pd360License2!='Success'){
				$error_pd360License=1;
			}else{
				$error_pd360License=0;
			}
		}else{
			echo '<PD360Status><Error>No account exists</Error></PD360Status>';
			$error_pd360License=1;
		}
			
		}
	}
	$licenseInfo="&license=".$license; 
}

if($error_createNewUser || $error_licenseSeat || $error_pd360License){
	$string='?';
	if($error_createNewUser){
		$string.='new_user_error=1&';
	}
	if($error_licenseSeat){
		$string.='license_error=1&';
	}
	if($error_pd360License){
		$string.='edivate_license_error=1&';
	}
	header("Location:/store/checkout.php".$string);
	die();
}

//email
ob_start(); ?>

<div id="main"><h1>Order Complete</h1></div><br />
<p>Thank you for your order. It has been received.</p>
<?
//$licence
//$school
//$license_pd360
//$license_lumibook
//$shipTotalVal
if($license && $school || $license && $_REQUEST['international']=='yes'){
	echo '<p>Your account has been created. To log in, check your email provided. You should have received a password. To log in to Edivate <a href="https://www.edivate.com/">click here</a></p>';
}
$special='';
if($license_pd360){
	$special.='<p>You now have premium access to the most highly acclaimed on-demand set of professional learning tools and online video library. To log in to Edivate <a href="https://www.edivate.com/">click here</a>. For help with using Edivate <a href="http://help.schoolimprovement.com/courses/essentials/">click here</a></p>';
}if($license_lumibook){
	$special.='<p>Your LumiBook is now available in Edivate. To log in to Edivate <a href="https://www.edivate.com/">click here</a>. For help with using Edivate <a href="http://help.schoolimprovement.com/courses/essentials/">click here</a></p>';
}if($shipTotalVal){
	$special.='<p></p>';
}
echo $special;
?>
<table cellpadding="5" cellspacing="0" align="center" style="padding-bottom:20px;" class="table">
	<tr>
    	<td colspan="4"></td>
    </tr>
    <tr>
    	<td colspan="4" class="bdrtop bdrleft bdrright cellhr"><strong>Billing Information</strong></td>
    </tr>
    <tr>
    	<td colspan="1" class="bdrtop bdrleft bdrright" valign="top">
        	<? echo $billing; ?>
        </td>
        <td colspan="3" class="bdrtop bdrright" valign="top">
<? if($license==true){ ?>
<strong>License Information</strong><br><br>
Email: <? echo $email; ?><br>
<? if($school){  ?>
State: <? echo $state; ?><br>
District: <? echo $district_value; ?><br>
School: <? echo $school_value; ?>
<? }else{ echo '(Edivate Account Verified)'; }?>
<? } ?>
        </td>
    </tr>
    <? if($shipping){ ?>
    <tr>
    	<td colspan="5" class="bdrtop bdrleft bdrright cellhr"><strong>Shipping Information</strong></td>
    </tr>
    <tr>
    	<td colspan="5" class="bdrtop bdrleft bdrright" valign="top"><? echo $shipping; ?></td>
    </tr>
    <? } ?>
    <tr>
    	<td colspan="4" class="bdrtop bdrleft bdrbottom bdrright cellhr"><strong>You purchased the following <? echo $cart_num_count; ?> item(s):</strong></td>
    </tr>
    <tr>
    	<td width="380" class="bdrleft bdrbottom"><strong>Name</strong></td>
        <td class="bdrleft bdrbottom"><strong>QTY</strong></td>
        <td class="bdrleft bdrbottom"><strong>Price</strong></td>
        <td class="bdrleft bdrbottom bdrright"><strong>Total</strong></td>
    </tr>
<?
$items = explode('|',$cart_new);
$contents = array();
$i=1;
foreach ($items as $specs) { 
	$cubes=explode(',', $specs);
	$cube_id=$cubes[0];
	$cube_num=$cubes[1];
	
$cart_query=mysql_query("SELECT * FROM products WHERE ItemID=".sanitize($cube_id), $db); //error possible 2
$row_cart=mysql_fetch_object($cart_query);
if($row_cart->ChargeShipping=='Yes'){
	$shippingTotal=$shippingTotal+$row_cart->Price*$cube_num;
}
if($row_cart->ChargeTaxes=='Yes'){
	$taxesTotal=$taxesTotal+$row_cart->Price*$cube_num;
}
if($row_cart->license_type!=0){
	$license=1;
}
?>
	<tr>
    	<td valign="top" class="bdrleft bdrbottom"><h3><? echo $row_cart->ProductName; if($row_cart->product_message){ ?></h3><p><? echo $row_cart->product_message; ?><? } ?></p></td>
        <td valign="top" class="bdrleft bdrbottom"><? echo $cube_num; ?></td>
        <td valign="top" class="bdrleft bdrbottom"><? echo '$'.number_format($row_cart->Price, 2, '.', ','); ?></td>
        <td valign="top" class="bdrleft bdrbottom bdrright"><? echo '$'.number_format(($row_cart->Price*$cube_num), 2, '.', ','); ?></td>
    </tr>
<? 
$total=$total+($row_cart->Price*$cube_num); $i= $i+1; }
if($promo){
	$query_promo=mysql_query("SELECT * FROM promos WHERE PromoCode='".sanitize($promo)."'", $db);
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
    	<td colspan="4" class="bdrleft bdrbottom bdrright">Promo Codes: <? echo $promo.' '.$row_promo->AmountOff.'% off entire order' ; ?></td>
    </tr>
<? } ?>
	<tr>
    	<td></td>
        <td></td>
        <td class="bdrleft bdrbottom">Cart Total:</td>
        <td class="bdrleft bdrbottom bdrright"><strong><? echo '$'.number_format($total, 2, '.', ','); ?></strong></td>
    </tr>
<? if($promo_discount){ ?>
    <tr>
    	<td></td>
        <td></td>
        <td class="bdrleft bdrbottom">Discounts:</td>
        <td class="bdrleft bdrbottom bdrright"><strong>-$<? echo number_format($promo_discount, 2, '.', ','); ?></strong></td>
    </tr>
<? }if($shipTotalVal){ 

?>
    <tr>
    	<td></td>
        <td></td>
        <td class="bdrleft bdrbottom">Shipping:</td>
        <td class="bdrleft bdrbottom bdrright"><strong>$<? echo number_format($shipTotalVal, 2, '.', ','); ?></strong></td>
    </tr>
<? }
if($taxesTotalVal){
	$taxTotal=$taxesTotalVal;
}else{
	$taxTotal='';
}
if($taxTotal){
?>
    <tr>
    	<td></td>
        <td></td>
        <td class="bdrleft bdrbottom">Tax:</td>
        <td class="bdrleft bdrbottom bdrright"><strong>$<? echo number_format($taxTotal, 2, '.', ','); ?></strong></td>
    </tr>
<? } ?>
    <tr>
    	<td></td>
        <td></td>
        <td class="bdrleft bdrbottom">Total:</td>
        <td class="bdrleft bdrbottom bdrright"><strong>$<? echo number_format(($totalVal), 2, '.', ','); ?></strong></td>
    </tr>
</table>
<?
$printMe=ob_get_contents(); 
$_SESSION['printMe']=$printMe;

$message='';
$message.='<html>';
$message.='<head>';
$message.='<title>Order Complete</title>';
$message.='<style>';
$message.='html{font-family:sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%}body{margin:20px}';
$message.='.alert{padding:15px;margin-bottom:20px;border:1px solid transparent;border-radius:4px}';
$message.='.alert-success{color:#3c763d;background-color:#dff0d8;border-color:#d6e9c6}.alert-success hr{border-top-color:#c9e2b3}.alert-success .alert-link{color:#2b542c}';
$message.='.bdrtop{';
$message.='	border-top:#CCC thin solid;';
$message.='}';
$message.='.bdrright{';
$message.='	border-right:#CCC thin solid;';
$message.='}';
$message.='.bdrbottom{';
$message.='	border-bottom:#CCC thin solid;';
$message.='}';
$message.='.bdrleft{';
$message.='	border-left:#CCC thin solid;';
$message.='}';
$message.='.cellhr{';
$message.='	background-color:#E6E6E6;';
$message.='}';
$message.='</style>';
$message.='</head>';
$message.='<body>';
$message.=$printMe;
$message.='</body>';
$message.='</html>';
//print on the next page.
mail('mike.thomas@schoolimprovement.com', 'Order Complete', $printMe, $headers);
mail('kari.stevenson@schoolimprovement.com', 'Order Complete', $printMe, $headers);
mail($email, 'Your Order Is Complete', $printMe, $headers);

mysql_query("UPDATE orders SET order_status='Processed', email_message='".$printMe."' WHERE transaction_id='". sanitize( $transaction)."'", $db)or die(mysql_error());
$_SESSION['cart']='';
//process payment
if($payment_type=='CC'){
	header("Location:/store/paypal.php?action=charge&transaction=$paypalTrans&amount=$totalVal&transaction_id=$transaction");
}else{
	header("Location:/store/complete.php?transaction_id=$transaction");
}
?>