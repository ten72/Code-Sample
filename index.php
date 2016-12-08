<?php
/*
This is the main store page, it runs a query to display the featured products.
*/
include('admin/db.php');
$feature_query=mysql_query("SELECT * FROM products WHERE featured=1 ORDER BY order_num_featured");
$title='School Improvement Network Store';
$selected='Featured';
include('includes/header.php');
?>
    </div>
    <div id="sitebox">
        <table cellpadding="0" cellspacing="0">
        	<tr>
            	<td id="main"><h1>Featured</h1></td>
            </tr>
        </table>
        <div id="featured" class="row">
        <? while($row_feature=mysql_fetch_object($feature_query)){ ?>
        	<div id="arrayorder_<?php echo $row_feature->ItemID; ?>" class="product col-md-4 col-lg-4 col-sm-4 col-xs-12">
                <a href="product.php?p=<? echo $row_feature->ProdLabel; ?>" class="productImage">
                    <img src="<? echo $row_feature->ImageURL; ?>" width="65" border="0" />
                </a>
                <a href="product.php?p=<? echo $row_feature->ProdLabel; ?>" class="productText">
                    <? echo $row_feature->ProductName; ?>
                </a><br />
                <a href="product.php?p=<? echo $row_feature->ProdLabel; ?>" class="productPrice">
                    <? if($row_feature->Price=='0'){ echo 'FREE'; }else{ ?>$<? echo number_format($row_feature->Price, 2); } ?>
                </a>
            </div>
        <? } ?>
        </div>
    </div>
<? include('includes/footer.php');?>