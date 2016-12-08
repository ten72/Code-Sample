<?
session_start();
function check_for_product($id, $cart){
	$items = explode('|',$cart);
	foreach ($items as $specs) { 
		$products=explode(',', $specs);
		$product_id=$products[0];
		$product_count=$products[1];
		if($product_id==$id){
			return TRUE;
		}
	}
}
include("includes/secure.php");
function html_sanitize($string){
	return(htmlentities($string));
}

$cart=$_SESSION['cart'];
$action=html_sanitize($_REQUEST['action']);
$id=html_sanitize($_REQUEST['ItemID']);
$num=html_sanitize($_REQUEST['qty']);
$limit=html_sanitize($_REQUEST['limit']);
switch ($action){
//Add item to cart
case 'add':
  if(!$cart){
	  $_SESSION['cart']=$id.','.$num;
  }else{
	  $found=check_for_product($id, $cart);
	  if($found==TRUE){
		echo '<br>Product Was Found in Your Cart';
	  }else{
		$_SESSION['cart']=$cart.'|'.$id.','.$num;
		echo '<br>Product Is Not in Your Cart';
	  }
  }
  header("Location:view_cart.php");
  break;
//Edit cart item
case 'edit':
	$cart=$_SESSION['cart'];
	$items = explode('|',$cart);
	$contents = array();
	$cart_new="";
	foreach ($items as $specs) { 
		$cubes=explode(',', $specs);
		$cube_id=$cubes[0];
		$cube_num=$cubes[1];
		
		if(!$cart_new){
			$cart_new=$cube_id.','.html_sanitize($_REQUEST["num_{$cube_id}"]);
		}else{
			$cart_new=$cart_new.'|'.$cube_id.','.html_sanitize($_REQUEST["num_{$cube_id}"]);
		}
	}
	$_SESSION['cart']=$cart_new;
	if($_REQUEST['promo']){
			$_SESSION['promo']=html_sanitize($_REQUEST['promo']);
	}
	header("Location:view_cart.php");
  break;
//Removes item from cart
case 'delete':
  	$cart=$_SESSION['cart'];
	$items = explode('|',$cart);
	$contents = array();
	$cart_new="";
	foreach ($items as $specs) { 
		$cubes=explode(',', $specs);
		$cube_id=$cubes[0];
		$cube_num=$cubes[1];
		
		if(!$cart_new){
			if($cube_id!=$_REQUEST['id']){
				$cart_new=$cube_id.','.$cube_num;
			}
		}else{
			if($cube_id!=$_REQUEST['id']){
				$cart_new=$cart_new.'|'.$cube_id.','.$cube_num;
			}
		}
	}
	$_SESSION['cart']=$cart_new;
	header("Location:view_cart.php");
  	break;
//Clears cart to nothing
case 'clear':
	$_SESSION['cart']='';
	header("Location:index.php");
	break;
//Display Cart
case 'view':
	echo $cart;
	break;
} 
?>