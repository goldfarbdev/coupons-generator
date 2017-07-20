<html>
<head>
<link rel="stylesheet" href="http://code.jquery.com/jquery.min.js" />
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
	
	
	if($coupon[9] != "" && $coupon[10] == ""){
		$couponcode = 'Coupon code: ' . $coupon[9];
		$printpot = '<a href="#" class="z_print_this z_print_this' . $t . '">Print</a>';
		$shoppot = '';
	}elseif($coupon[9] != "" && $coupon[10] != ""){
		$couponcode = 'In-store coupon code: ' . $coupon[9] . ' <br /> Online or phone coupon code: ' . $coupon[10];
		$printpot = '<a href="#" class="z_print_this z_print_this' . $t . '">Print</a>';
		$shoppot = '<a href="' . $coupon[13] . '">Shop now</a>';
	}elseif($coupon[9] == "" && $coupon[10] != ""){
		$couponcode = 'Online coupon code: ' . $coupon[10];
		$printpot = '';
		$shoppot = '<a href="' . $coupon[13] . '">Shop now</a>';
	}
	
	if($printpot != '')
		$printpot .= ' | ';
	
	if($shoppot != '')
		$shoppot .= ' | ';
	
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
		$image = '<img src="http://s7d5.scene7.com/is/image/Staples/' . $image . '_sc7?$small$" width="120" height="120" align="right" class="z_noprint" />';
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
		$weboffer = str_replace('with coupon.','<span><br />with coupon.</span>',str_replace('$','<sup>$</sup>',str_replace(' off','<sup> OFF</sup>',$coupon[2])));
	}elseif (preg_match("/%/",$coupon[2])){
		$offer = $coupon[2];
		if(preg_match("/back/i",$coupon[2])){
			$weboffer = str_replace('% back','<sup>%</sup> back',$coupon[2]);
		}elseif(preg_match("/off/i",$coupon[2])){
			$weboffer = str_replace('% off','<sup>% OFF</sup>',$coupon[2]);
		}
		
	}else{
		$offer = $coupon[2];
		$weboffer = $coupon[2];
		if(preg_match("/free after /i",$coupon[2])){
			$weboffer = str_replace("Free","FREE<br /><span>",$weboffer) . "</span>";
		}elseif(preg_match("/^FREE /i",$coupon[2])){
			$weboffer = str_replace("FREE ","FREE<br /><span>",$weboffer) . "</span>";
		}
	}
	
	if($coupon[12] && $coupon[13]){
		$onlinecta = ' | <a href="' . $coupon[13] . '">' . $coupon[12] . '</a>';
	}

	$z_coupon_offer = str_replace('&reg;','<span class="z_reg">&reg;</span>',$coupon[3]);
	
	if(($t+1) % 2 == 0){
		$pagebreak = ' z_page_break';
	}else{
		$pagebreak = '';
	}
	
$coupons .= '
<ul class="z_cp_item z_cp_mobile_closed z_noprint' . $pagebreak . '" id="z_c' . $t . '">
	<li class="z_cp_ribbon" id="c' . ($t+1) .'">
		' . $printBox . '
		<div class="z_cp_channel">' . $coupon[0] . '</div>
		<div class="z_cp_valid">' . $coupon[1] . '</div>
	</li>
	<li class="z_cp_inner">
		' . $image . '
		<p class="z_cp_headline ' . $headlineclass . '">' . $weboffer . '</p>

		<h2 class="z_cp_product ' . $headlineclass . '">' . $z_coupon_offer . '<sup class="printonly">*</sup></h2>
		<p class="z_cp_description">' . $restrictions . '</p>

		<p class="z_cp_barcode printonly">Associate scan UPC before completing sale:</p>
		<p class="z_cp_barcode printonly"><img src="http://bar.cheetahmail.com/cgi-bin/barcode/AAAAAAB7SHGCB7TKtJAAAAAAsI/bar.png?bc=' . $coupon[9] . '" /></p>

		<p class="z_cp_coupon_code">' . $couponcode . '</p>

		<p class="z_cp_disclaimer"><sup class="printonly">*</sup> ' . $coupon[11] . '</p>

	</li>
	<li class="z_cp_tools z_noprint">
		' . $printpot . '
		' . $shoppot . '
		<span class="z_cp_disclaimer_toggle"><a href="#disclaimer">See disclaimer</a></span>
	</li>
</ul>
';


$t++;
}
echo $t;
?>
<?php
echo '<textarea cols="50" rows="5">';
echo $coupons;

echo '</textarea>';
?>

<script type="text/javascript">
$("#z_pick_coupon_box_0").click(function(){
	$("#z_pick_coupon_0").css('display','block');	
});

</script>
</body>
</html>