<?
/*
This is a page, it displays the details of a specific product and allows you to add it to your cart.
*/
//Check for session
if(session_id() == '') {
    session_start();
}
include('admin/db.php');
// select specific product from p 
$product_query=mysql_query("SELECT * FROM products WHERE ProdLabel='". sanitize( $_REQUEST['p'])."'");
$row_product=mysql_fetch_object($product_query);
if($row_product->hidden==1){
	header("Location:/store/");
}
	
$title=$row_product->ProductName;
$url=$row_product->try_page;

include('includes/header.php');
?>
<div class="row" style="padding:20px;">
	
    <div class="col-md-4 col-lg-4 col-sm-4 col-xs-12 text-center" style="padding:10px;">
            	<img src="<? echo $row_product->ImageURL; ?>" width="200" border="0" alt="<? echo $row_product->ProductName; ?>" /><br>
                <form action="/store/cart.php?ItemID=<? echo $row_product->ItemID; ?>&action=add" method="post">
                <table width="100%">
                	<tr>
                    	<td class="text-center"><? if($row_product->license_type){ echo '<input name="qty" type="hidden" value="1" size="3" maxlength="3" /><input name="limit" type="hidden" value="1" size="3" maxlength="3" />'; }else{ ?><span class="qty">QTY: </span><input name="qty" type="text" value="1" size="3" maxlength="3" /><? } ?></td>
                        <td><span class="productPrice2 text-center"><? echo '$'.number_format($row_product->Price, 2); ?></span></td>
                    </tr>
                    <tr>
                    	<td colspan="2" class="text-center"><input name="" type="image" src="images/add-to-cart.png" /></td>
                    </tr>
                </table>
                </form>
<? if($row_product->license_type==1){ ?>
<div style="padding:10px; border:thin solid#330066;margin:20px 0px;">
<h3 align="center">What&rsquo;s a LumiBook?</h3>
        <h4 align="center"><em>The ebook that&rsquo;s   so much more</em></h4>
<ul style="text-align:left;">
  <li>An ebook with the power of the web, including: </li>
  <ul>
    <li>Videos, </li>
    <li>Downloadable documents </li>
    <li>Interactive communities </li>
    <li>Webinars</li>
    <li>And much more</li>
  </ul>
  <li>Accessible on your computer or mobile device</li>
  <li>Join live conversations with readers &amp; the author</li>
  <li>Revisions appear automatically, so you always have the newest edition</li>
</ul>
<span style="color:#0099FF">For bulk orders call 866-835-4185</span>
</div>

        <? }if($row_product->ItemID=='1544'){ ?>
<div class="panel panel-default" style="margin:20px 50px;">
  <div class="panel-heading">Awards</div>
  <div class="panel-body">
    <img src="/store/images/Awards.png" width="150">
  </div>
</div>
        <? } ?>
                <? } ?>
    </div>
    
    <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
            	<? if($row_product->ProductName){ ?><h1><? echo $row_product->ProductName; ?></h1><? } ?>
            	<? if($row_product->ProductID){ ?><span class="sku"><? echo $row_product->ProductID; ?></span><br><? } ?>
            	<? if($row_product->Author){ ?><span class="author"><? echo $row_product->Author; ?></span><br><br><? } ?>
                <? if($row_product->video1){ ?>
                <script type="text/javascript" src="http://www.schoolimprovement.com/flowplayer10/flowplayer-3.2.9.min.js"></script>
 
				<!-- player container--> 
				<div align="center">
				<a href="<? echo $row_product->video1; ?>" 
	style="display:block;width:600px;height:360px;padding:10px 0px;clear:both;" 
	id="player"><img src="<? echo $row_product->video2; ?>" alt="Video Image" /><br /><br />
</a>
</div>
 
<script>flowplayer("player", "http://www.schoolimprovement.com/flowplayer10/flowplayer-3.2.10.swf");</script>
                <div style="clear:both;"></div><br><br>
				<? } ?>
            	<span class="details"><? echo $row_product->Details; ?></span>

    </div>
    
</div>
    </div>
<? include('includes/footer.php');?>