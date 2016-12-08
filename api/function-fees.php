<?php
include('../admin/db.php');
$total=$_REQUEST['total'];
$taxTotal=$_REQUEST['tax'];
$shippingTotal=$_REQUEST['ship'];
$country=$_REQUEST['country'];
$state=$_REQUEST['state'];
$zip=$_REQUEST['zip'];
header("Content-Type: application/xhtml+xml; charset=utf-8");
echo '<Wrap>'."\n";
echo '<Ship>';
echo shippingTotal($shippingTotal, $country, $state);
echo '</Ship>'."\n";
echo '<Tax>';
if($state=='WA' || $state=='UT' || $state=='CA' || $state=='HI'){
	echo taxTotal($total, $taxTotal, $state, $zip);
}else{
	echo '0.00';
}
echo '</Tax>'."\n";
echo '</Wrap>'."\n";
//shipping Function
function shippingTotal($shippingTotal, $country, $state){
	if($country=='US'){
		if($shippingTotal<=24.99){
			if($state=='AK' || $state=='HI'){
				$shipTotal='13.00';
			}else{
				$shipTotal='6.00';
			}
		}elseif($shippingTotal>=25.00 && $shippingTotal<=74.99){
			if($state=='AK' || $state=='HI'){
				$shipTotal='17.00';
			}else{			
				$shipTotal='9.00';
			}
		}elseif($shippingTotal>=75.00 && $shippingTotal<=124.99){
			if($state=='AK' || $state=='HI'){
				$shipTotal='21.00';
			}else{
				$shipTotal='11.00';
			}
		}elseif($shippingTotal>=125.00 && $shippingTotal<=299.99){
			if($state=='AK' || $state=='HI'){
				$shipTotal='25.00';
			}else{
				$shipTotal='16.00';
			}
		}elseif($shippingTotal>=300.00){
			if($state=='AK' || $state=='HI'){
				$shipTotal=$shippingTotal*0.11;
			}else{
				$shipTotal=$shippingTotal*0.06;
			}
		}
	}elseif($country=='CA'){
		if($shippingTotal<=24.99){
			$shipTotal='13.00';
		}elseif($shippingTotal>=25.00 && $shippingTotal<=74.99){
			$shipTotal='17.00';
		}elseif($shippingTotal>=75.00 && $shippingTotal<=124.99){
			$shipTotal='21.00';
		}elseif($shippingTotal>=125.00 && $shippingTotal<=299.99){
			$shipTotal='25.00';
		}elseif($shippingTotal>=300.00){
			$shipTotal=$shippingTotal*0.11;
		}
	}else{
		if($shippingTotal<=24.99){
			$shipTotal='15.00';
		}elseif($shippingTotal>=25.00 && $shippingTotal<=74.99){
			$shipTotal='21.00';
		}elseif($shippingTotal>=75.00 && $shippingTotal<=124.99){
			$shipTotal='29.00';
		}elseif($shippingTotal>=125.00 && $shippingTotal<=299.99){
			$shipTotal='42.00';
		}elseif($shippingTotal>=300.00){
			$shipTotal=$shippingTotal*0.12;
		}
	}
	return $shipTotal;
}

function taxTotal($total, $taxTotal, $state, $zip){
	$query_tax=mysql_query("SELECT * FROM tax WHERE state='".$state."' AND zip='".$zip."'");
	$row_tax=mysql_fetch_object($query_tax);
	if($state=='CA'){
		$taxTotalVal=$taxTotal*$row_tax->rate;
	}else{
		$taxTotalVal=$total*$row_tax->rate;
	}
	return number_format($taxTotalVal, 2);
}
?>