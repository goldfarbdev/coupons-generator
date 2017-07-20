<html>
<head>
<meta charset="UTF-8" />
</head>
<body>
<!-- WES WUZ HERE -->
<!-- YET AGAIN! -->

<?php
ini_set("auto_detect_line_endings", true);
$lines = file("coupons.html",FILE_IGNORE_NEW_LINES);


$coupons = '';
$disclaimers ='';
$t = 0;
foreach ($lines as $line_num => $line) {
	$image = "";
	$offer = "";
	$weboffer = "";
	$restrictions = "";
	$withcoupon = "";
	$withcoupondisplay = "";
	$items = "";
	$itemsdisplay = "";
	$onlinecta = "";
	$printBox = "";
	$headlineclass = "";
	$coupon = explode("\t",$line);
	$couponcode = "";
	if(!preg_match('/in U.S. store/',$coupon[0])){
		$printBox = '';
	}else{
		$printBox = '<label class="z_noprint"><input type="checkbox" id="z_select_coupon_box' . $t . '" /> Select to print</label>';
	}

	$barcodeimage = '<p class="z_cp_barcode_image"><img src="http://easy.staples.com/bca/z/image?ct=CODE128&bc=' . $coupon[9] . '&st=N&txtm=0.0" /></p>';
	
	if(preg_match('/Valid in U.S. stores only/',$coupon[0])){
		$couponcode = 'In-Store Coupon Code: <span class="z_coupon_code_bold">' . $coupon[9] . '</span>';
		$printpot = '<a href="#" class="z_print_this z_print_this' . $t . '">Print</a>';
		$shoppot = '';
	}elseif(preg_match('/Valid in U.S. stores, online or by phone/',$coupon[0])){
		if($coupon[9] != "" && trim($coupon[10]) == ""){
			$couponcode = 'In-Store/Online/Phone Coupon Code: <span class="z_coupon_code_bold z_online_coupon_code">' . $coupon[9] . '</span>';
		}elseif($coupon[9] != "" && trim($coupon[10]) != ""){
			$couponcode = 'In-store Coupon Code: <span class="z_coupon_code_bold">' . $coupon[9] . '</span><br />Online or Phone Coupon Code: <span class="z_coupon_code_bold z_online_coupon_code">' . $coupon[10] . '</span>';
		}
		$printpot = '<a href="#" class="z_print_this z_print_this' . $t . '">Print</a>';
		$shoppot = '<a href="' . $coupon[13] . '" class="z_get_offer z_noprint">Shop now</a>';
	}elseif(preg_match('/Not valid in stores/',$coupon[0])){
		$couponcode = 'Online or Phone Coupon Code: <span class="z_coupon_code_bold z_online_coupon_code">' . $coupon[10] . '</span>';
		$printpot = '';
		$shoppot = '<a href="' . $coupon[13] . '" class="z_get_offer z_noprint">Shop now</a>';
		$barcodeimage = '';
	}
	
	if($printpot != '')
		$printpot = ' | ' . $printpot;
	
/*	if($shoppot != '')
		$shoppot = ' | ' . $shoppot; */
	
/*	
DEPRECATED
if($coupon[3]){
		$withcoupon = '<p class="offerb">with coupon.</p>';
		$withcoupondisplay = '<li class="z_coupon_with_coupon">with coupon</li>';
	}
*/		
	
	if($coupon[4]){
		$items = '<p class="offerb">' . $coupon[4] . '</p>';
		$itemsdisplay = $coupon[4] . ' ';
	}
	
	if($coupon[5])
		$restrictions = $itemsdisplay . $coupon[5] . ' ';
	
	if($coupon[6])
		$restrictions .= $coupon[6] . ' ';
	
	if($coupon[7])
		$restrictions .= $coupon[7];
	
	$restrictions = '' . $restrictions . '';
	
	if(preg_match("/S[0-9]{7}/i",$coupon[8])){
		preg_match('/S[0-9]{7}/i', $coupon[8], $matches);
		$image = strtolower($matches[0]);
		$image = '<img src="http://s7d5.scene7.com/is/image/Staples/' . $image . '_sc7?$cprelatedimage$" width="140" height="140" align="right" class="z_noprint" />';
	}
	
	if($image){
		$headlineclass = "z_yes_image";
	}else{
		$headlineclass = "z_no_image";
	}
	
	if(preg_match("/\\\$/",$coupon[2]) && preg_match("/after/",$coupon[2])){
		$offer = $coupon[2];
		$weboffer = str_replace('after','<span><br />after',str_replace('$','<sup>$</sup>',str_replace(' off','<sup> OFF</sup>',$coupon[2]))) . "</span>";
	}elseif(preg_match("/\\\$/",$coupon[2])){
		$offer = $coupon[2];
		$weboffer = str_replace('with coupon.','<span><br />with coupon.</span>',$coupon[2]);
	}elseif (preg_match("/%/",$coupon[2])){
		$offer = $coupon[2];
		if(preg_match("/back/i",$coupon[2])){
			$weboffer = str_replace('% back','% back',$coupon[2]);
		}elseif(preg_match("/off/i",$coupon[2])){
			$weboffer = str_replace('% OFF','% off',$coupon[2]);
		}
		
	}else{
		$offer = $coupon[2];
		$weboffer = $coupon[2];
		if(preg_match("/free after /i",$coupon[2])){
		/*	$weboffer = str_replace("Free","FREE<br /><span>",$weboffer) . "</span>"; */
		}elseif(preg_match("/^FREE /i",$coupon[2])){
			$weboffer = str_replace("FREE ","Free ",$weboffer) . " ";
		}
	}
	
	if($coupon[12] && $coupon[13]){
		$onlinecta = ' | <a href="' . $coupon[13] . '">' . $coupon[12] . '</a>';
	}

	$z_coupon_offer = str_replace('&reg;','<span class="z_reg">&reg;</span>',$coupon[3]);
	
	if(($t+1) % 2 == 0){
		$pagebreak = ' z_page_break';
		$opencouponholder = '';
		$closecouponholder = '</div>';
	}else{
		$pagebreak = '';
		$opencouponholder = '<div class="z_cp_item_holder">';
		$closecouponholder = '';
	}
	
	$endDate = explode("Expires ",$coupon[1]);
	$endDate = explode("/",$endDate[1]);
	$endDate[2] = "20" . $endDate[2];
	$expDate = date("F j, Y", mktime(0, 0, 0,  $endDate[0], $endDate[1] , $endDate[2]));


	
$coupons .= $opencouponholder . '
	<ul class="z_cp_item z_cp_mobile_closed z_noprint' . $pagebreak . '" id="z_c' . $t . '">
		<li class="z_cp_ribbon" id="c' . ($t+1) .'">
			' . $printBox . '
			<div class="z_cp_channel printonly">' . $coupon[0] . '</div>
			<div class="z_cp_valid printonly">' . $coupon[1] . '</div>
		</li>
		<li class="z_cp_inner">
			' . $image . '
			<p class="z_cp_channel z_noprint">' . $coupon[0] . '</p>
			<p class="z_cp_valid z_noprint"><b>Expires ' . $expDate . '</b></p>
	
			<h2 class="z_cp_product">' . $weboffer . '<br /><span class="z_cp_hdl_item">' . $z_coupon_offer . '<sup class="printonly">*</sup></span></h2>
			<p class="z_cp_description">' . $restrictions . '</p>
	
			<p class="z_cp_barcode printonly">Associate scan UPC before completing sale:</p>
			' . $barcodeimage . '
	
			<p class="z_cp_coupon_code">' . $couponcode . '</p>
			
			' . $shoppot . '
	
			<p class="z_cp_disclaimer"><sup class="printonly">*</sup> ' . $coupon[11] . '</p>
	
		</li>
		<li class="z_cp_tools z_noprint">
		<!--	' . $printpot . '
			 -->
			<span class="z_cp_disclaimer_toggle"><a href="#" class="z_hide_view_disclaimer">See disclaimer</a></span>
			' . $printpot . '
		</li>
	</ul>
' . $closecouponholder . PHP_EOL;


$t++;
}
echo $t;
?>
<?php
echo '<textarea cols="50" rows="5">';
echo $coupons;

echo '</textarea>';
?>


</body>
</html>