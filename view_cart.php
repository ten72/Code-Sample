<?PHP
if(session_id() == '') {
    session_start();
}
$promo=$_SESSION['promo'];
$cart=$_SESSION['cart'];
include('admin/db.php');
$total='';
$promo_discount='';
$count='';
$product_query=mysql_query("SELECT * FROM products WHERE ProdLabel='". sanitize( $_REQUEST['p'])."'");
$row_product=mysql_fetch_object($product_query);

$title='My Cart';

include('includes/header.php');
?>
<div id="main"><h1>My Cart</h1></div><br /><br />
<? if($cart){ ?>
<div>
			<form id="form1" name="form1" action="cart.php?action=edit" method="post">
<script type="text/javascript">
<!--
    function toggle_visibility(id) {
       var e = document.getElementById(id);
       if(e.style.display == 'block')
          e.style.display = 'none';
       else
          e.style.display = 'block';
    }
//-->
</script>
            
<div class="img-rounded grayborder">
            <table align="center" class="table">
<thead>
        <tr>
          <th></th>
          <th></th>
          <th>Product</th>
          <th>Price</th>
          <th>Quantity</th>
        </tr>
      </thead>
      <tbody>
<?
$items = explode('|',$cart);
$contents = array();
$i=1;
foreach ($items as $specs) { 
	$cubes=explode(',', $specs);
	$cube_id=$cubes[0];
	$cube_num=$cubes[1];
	
$cart_query=mysql_query("SELECT * FROM products WHERE ItemID=". sanitize( $cube_id));
$row_cart=mysql_fetch_object($cart_query);
if($row_cart){
?>
            	<tr>
                    <td valign="top"><a href="cart.php?action=delete&id=<? echo $cube_id; ?>"><img src="images/delete.png" width="15" /></a></td>
                    <td valign="top"><a href="product.php?p=<? echo $row_cart->ProdLabel; ?>" class="productImage"><img src="<? echo $row_cart->ImageURL; ?>" width="100" /></a></td>
                    <td valign="top"><a href="product.php?p=<? echo $row_cart->ProdLabel; ?>" class="productText"><? echo $row_cart->ProductName; ?></a></td>
                    <td valign="top"><span class="productPrice2"><? echo '$'.number_format($row_cart->Price, 2, '.', ','); ?></span></td>
                  	<td valign="top"><? if($row_cart->license_type){ ?><input name="num_<? echo $cube_id; ?>" type="hidden" value="<? echo $cube_num; ?>" /><? } ?><input name="num_<? echo $cube_id; ?>" type="text" size="4" maxlength="4" value="<? echo $cube_num; ?>" <? if($row_cart->license_type){ echo ' disabled="disabled"'; }?> /><? $count=$cube_num+$count; ?>
</td>
            	</tr>
				<? $total=$total+($row_cart->Price*$cube_num); $i= $i+1; }} ?>
                <tr>
                	<td colspan="5"><div class="input-group input-group-sm" style="float:left;width:200px;"><input name="promo" id="promo"  class="form-control" placeholder="Promo Code" type="text" value="<? echo $promo; ?>" /><span class="input-group-btn"><button class="btn btn-default" type="submit">Apply Code</button></span></div><div style="float:right;width:270px;"><input name="update" type="submit" value="Update Cart" class="btn btn-sm btn-default" alt="Update Cart" style="margin-right:15px;" /><a href="checkout.php" class="btn btn-primary text-white">Proceed to Checkout</a></div></td>
                </tr>
<? 

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
                	<td colspan="5" align="right" style="font-size:13px;"><strong>Promo Code Accepted:</strong> -$<? echo number_format($promo_discount, 2, '.', ','); ?></td>
                </tr>
<? } ?>
  				<tr>
                	<td colspan="5" align="right" style="font-size:18px;"><div id="the_total"><strong>Total:</strong> $<? echo number_format(($total-$promo_discount), 2, '.', ','); ?></div><input type="hidden" value="<? echo $total; ?>" id="total1" /></td>
                </tr>
                </tbody>
            </table>
            </div>

            </form>
<? }else{ ?>
<div style="margin-left:40px;">No Items in your cart.<br />
<br />
<br />
<br />
<? } ?>
</div>
    </div>
<? include('includes/footer.php');?>