<html>
<head>
<meta charset="UTF-8" />
</head>
<body>

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

	// Store Code and Print Select Box
	if(strlen($coupon[10]) > 0) {
		$storecode = '<p class="z_store_coupon_code">Store Code: <strong>' . $coupon[10] . '</strong></p>';
		$printBox = '<div class="z_select_one" id="z_select_coupon_box' . $t . '"><span>Select to print</span></div>';
	} else {
		$storecode = '';
		$printBox = '';
	}
	
	if(strlen($coupon[11]) > 0) {
		$onlinecode = '<p class="z_online_coupon_code">Online Code: <strong>' . $coupon[11] . '</strong></p>';
		$shoppot = '<a href="' . $coupon[14] . '" class="z_get_offer z_noprint">Shop now</a>';
	} else {
		$onlinecode = '';
		$shoppot = '';
	}
	
	
	if($coupon[5]){
		$items = '<p class="offerb">' . $coupon[5] . '</p>';
		$itemsdisplay = $coupon[5] . ' ';
	}
	
	if($coupon[6])
		$restrictions = $itemsdisplay . $coupon[6] . ' ';
	
	if($coupon[7])
		$restrictions .= $coupon[7] . ' ';
	
	if($coupon[8])
		$restrictions .= $coupon[8];
	
	$restrictions = '' . $restrictions . '';
	
	if(preg_match("/S[0-9]{7}/i",$coupon[9])){
		preg_match('/S[0-9]{7}/i', $coupon[9], $matches);
		$image = strtolower($matches[0]);
		$image = '<img src="http://s7d5.scene7.com/is/image/Staples/' . $image . '_sc7?$cprelatedimage$" width="140" height="140" align="center" class="z_s_image z_noprint" />';
	}
	
	if($image){
		$headlineclass = "z_yes_image";
	}else{
		$headlineclass = "z_no_image";
	}
	
	if($coupon[13] && $coupon[14]){
		$onlinecta = ' | <a href="' . $coupon[14] . '">' . $coupon[13] . '</a>';
	}
	
	$z_coupon_offer = str_replace('&reg;','<span class="z_reg">&reg;</span>',$coupon[4]);
	
	if(($t+1) % 2 == 0){
		$pagebreak = ' z_page_break';
		$opencouponholder = '';
		$closecouponholder = '';
	}else{
		$pagebreak = '';
		$opencouponholder = '';
		$closecouponholder = '';
	}
	
	$barcodeimage = '<div class="z_barcode z_mobile printonly"><img src="http://easy.staples.com/bca/z/image?ct=CODE128&bc=' . $coupon[10] . '&st=N&txtm=0.0" /></div>';
	
	$endDate = explode("Expires ",$coupon[1]);
	$endDate = explode("/",$endDate[1]);
	$endDate[2] = "20" . $endDate[2];
	$expDate = date("F j, Y", mktime(0, 0, 0,  $endDate[0], $endDate[1] , $endDate[2]));

	
$coupons .= $opencouponholder . '
	<div class="z_cp_item z_noprint" id="z_c' . $t . '">
		<div class="z_print_ready z_noprint"></div>
		<div class="z_cp_inner">
			<div class="z_offer_block">
				<div class="z_product">' . $coupon[2] . '</div>
				<div class="z_savings">' . $coupon[3] . '</div>
				<div class="z_details">' . $coupon[4] . '<sup class="printonly">*</sup></div>
				' . $image . '
				<div class="z_show_more z_mobile">
					<span class="z_more_text">Show More</span>
					<span class="z_arrow"></span>
				</div>
			</div>
			<div class="z_cp_ribbon" id="c' . ($t+1) .'">
				<div class="z_redeem_options">
					' . $onlinecode . '
					' . $shoppot . '
					' . $storecode . '
					' . $printBox . '
					' . $barcodeimage . '
				</div>			
				<p class="z_cp_channel printonly">' . $coupon[0] . '</p>
				<p class="z_cp_valid printonly">Expires <span>' . $expDate . '</span></p>
				<div class="z_social">
					<a href="#" class="facebook"><img src="images/social.png"></a>
					<a href="#" class="twitter"><img src="images/social.png"></a>
					<a href="#" class="email"><img src="images/social.png"></a>
				</div>
				<p class="z_cp_disclaimer"><sup class="printonly">*</sup> ' . $coupon[12] . '</p>
				<span class="z_cp_disclaimer_toggle"><a href="#" class="z_hide_view_disclaimer">See disclaimer</a></span>
				<p class="z_countdown"></p>
			</div>
		</div>
	</div>
		
	
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