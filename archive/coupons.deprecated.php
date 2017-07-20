<html>
<head>
<link rel="stylesheet" href="http://code.jquery.com/jquery.min.js" />
<meta charset="UTF-8" />
<style type="text/css">

	.printablecoupon p {
/*	clear: both; */
	float:none;
	}
	.printablecoupon .head { 
	font-family:verdana,arial; 
	font-size:14px; 
	font-weight:bold; 
	color:#000000; 
	margin:0 0px 10px 0px; 
	}
	
	.printablecoupon .subhead { 
	font-family:verdana,arial; 
	font-size:11px; 
	font-weight:bold; 
	color:#ffffff; 
	margin:5px 0px 5px 10px; 
	text-align: center;
	}
	
	.printablecoupon .border { 
	border:2px dashed #000000; 
	border-top:0px; }
	.printablecoupon .offer1 { font-family:verdana,arial; font-size:40px; font-weight:bold; color:#CC0000; margin:5px 0px 3px 0px; padding: 5px 0 0 10px; width:370px;float:left;clear:left; }
	.printablecoupon .offer2 { font-family:verdana,arial; font-size:18px; font-weight:bold; color:#000000; margin:0px 0px 8px 0px; padding: 0 0 5px 10px; line-height: 18px; width:370px;float:left;clear:left; }
	.printablecoupon .offer3 { font-family:verdana,arial; font-size:11px; color:#000000; margin:5px 0px 5px 0px; padding-right:20px; width:370px; float:left;clear:left;}
	.printablecoupon .offerb { font-family:verdana,arial; font-size:11px; color:#000000; font-weight:bold; margin:0px 0px 0px 0px; padding: 0 0 5px 10px;float:left;clear:left;}
	.printablecoupon .disclaimer { text-align: left; font-family:verdana,arial; font-size:10px; color:#999999; margin:5px 20px 5px 5px; }
	.printablecoupon {border-spacing:0}
	.printablecoupon th, .printablecoupon td {vertical-align: bottom; padding: 0px;}
	
	.z_coupon {margin-bottom:20px;}
	.z_pick_coupon{
		float:left;
		width:140px;
	}
	
	img.z_coupon_image {
		float:right;
		padding:10px 10px 10px 0;
		width:180px;
		height:180px;
	}
	@media print
	{
	  .printablecoupon .z_coupon_image, .z_noprint, .z_pick_coupon, #toolbar, .layoutWidth12
	  {
	    display:none;
	  }
	}
</style>
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
	$makeprintcoupon = "yes";
	
	$coupon = explode("\t",$line);
	$couponcode = "";
	if($coupon[9] != "" && $coupon[10] == ""){
		$couponcode = 'Coupon code: ' . $coupon[9];
	}elseif($coupon[9] != "" && $coupon[10] != ""){
		$couponcode = 'In-store coupon code: ' . $coupon[9] . ' <br /> Online or phone coupon code: ' . $coupon[10];
	}elseif($coupon[9] == "" && $coupon[10] != ""){
		$couponcode = 'Online coupon code: ' . $coupon[10];
		$makeprintcoupon = "no";
	}
	
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
	
	$restrictions = '<p class="offerb">' . $restrictions . '</p>';
	
	if(preg_match("/s[0-9]{7}/i",$coupon[8])){
		preg_match('/s[0-9]{7}/i', $coupon[8], $matches);
		$image = strtolower($matches[0]);
		$image = '<img src="http://s7d5.scene7.com/is/image/Staples/' . $image . '_sc7?$std$" class="z_coupon_image" />';
	}
	
	if(preg_match("/\\\$/",$coupon[2])){
		$offer = $coupon[2];
		$weboffer = str_replace("Take an extra ","<span>Take an extra<br /></span>",str_replace('with coupon.','<span><br /> with coupon.</span>',str_replace('$','<span>$</span>',str_replace(' off','<span> OFF</span>',$coupon[2]))));
	}elseif (preg_match("/%/",$coupon[2])){
		$offer = $coupon[2];
		$weboffer = str_replace("Buy 1, get 1 at","<span>Buy 1, get 1 at<br /></span>",str_replace('% off','<span>% OFF</span>',$coupon[2]));
	}else{
		$offer = $coupon[2];
		$weboffer = $coupon[2];
		if(preg_match("/^Free/i",$coupon[2])){
			$weboffer = str_replace("Free ","FREE<span><br />",$weboffer) . "</span>";
		}elseif(preg_match("/^Buy 1, get 1 free/i",$coupon[2])){
			$weboffer = str_replace("Buy 1, get 1 free","<span>Buy 1, get 1<br /></span>FREE",$weboffer) . "</span>";
		}
	}
	
	if($coupon[12] && $coupon[13]){
		$onlinecta = ' | <a href="' . $coupon[13] . '">' . $coupon[12] . '</a>';
	}

	$z_coupon_offer = str_replace('&reg;','<span class="z_reg">&reg;</span>',$coupon[3]);
	
	if((($t+1) % 2 == 1) && ($t + 1) > 1){
		$clearleft = ' style="clear:left;"';
	}else{
		$clearleft = '';
	}
	
	if($makeprintcoupon == "yes"){
		$webofferprintaction = '<input type="checkbox" id="z_select_coupon_box' . $t . '">Select to print';
		$webofferctaaction = '<a href="http://www.staples.com/sbd/cre/resources/redemptions/coupon-pages-in-store.html">How to redeem</a> | <a href="#" class="z_print_this z_print_this' . $t . '">Print coupon</a>';
	}else {
		$webofferprintaction = '';
		$webofferctaaction = '';
	}
	
$coupons .= '
<div class="z_coupon_rwd" id="c'. ($t+1) .'"' . $clearleft . '>
<div class="z_coupon_ribbon"><span class="z_select_coupon">' . $webofferprintaction . '</span></div>
<div class="z_coupon_offer"><h2>'.$weboffer.'</h2></div>
<ul>
'.$withcoupondisplay.'
<li class="z_coupon_offer">'.$z_coupon_offer.' <a href="#d' . ($t+1) . '"><sup>' . ($t+1) . '</sup></a></li>
<li class="z_coupon_image">'.$image.'</li>
<li class="z_coupon_valid">'.$coupon[1].'</li>
<li class="z_coupon_channel">'.$coupon[0].'</li>
<li class="z_coupon_restrictions">'.$restrictions.'</li>
<li class="z_coupon_mathstory"></li>
<li class="z_coupon_code">'.$couponcode.'</li>
<li class="z_coupon_cta">' . $webofferctaaction . $onlinecta . '</li>
</ul>
</div>
';

$disclaimers .= '<a name="d' . ($t+1) . '"></a>' . ($t+1) . ') ' . $coupon[11] . ' <a href="#c' . ($t+1) . '">Coupon</a><br /><br />' . PHP_EOL;
if($makeprintcoupon == "yes"){
$printcoupons .= '
<div class="z_coupon">
	<div class="z_print_coupon z_noprint" id="z_select_coupon_' . $t . '">
		<table cellspacing="0" cellpadding="0" border="0" width="665" bgcolor="#ffffff" class="printablecoupon">
			<tr>
				<td valign="top" align="center">
					<table cellspacing="0" cellpadding="0" border="0" width="665" bgcolor="#000000">
						<tr>
							<td align="center">
								<p class="subhead">' . $coupon[0] . '</p>
							</td>
						</tr>
					</table>
					<table cellspacing="0" cellpadding="0" border="0" width="665" class="border">
						<tr>
							<td colspan="2">
								<div class="z_offer_group">
									<p class="offer1">' . $offer . '</p>
									' . $withcoupon . '
									<p class="offer2">' . $coupon[3] . '*</p>
									' . $restrictions . '
									
									<p class="offerb">' . $couponcode . '</p>
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<p class="disclaimer"><b>*' . $coupon[1] . '</b> ' . $coupon[11] . '</p>
							</td>
							<td valign="bottom"><img src="http://www.staples.com/sbd/img/cre/logo/lg100-staples-logo.gif" width="100" height="50" alt="" style="margin:0px 5px 10px 0px"></td>								
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
</div>';
}
$makeprintcoupon = "yes";
$t++;
}
echo $t;
?>
<?php
echo '<textarea cols="50" rows="5">';
echo $coupons;

echo '<div id="z_coupon_disclaimer" style="height:300px;overflow:auto;margin-top:25px;margin-bottom:25px;border:1px solid grey;padding:3px;clear:left;">' . $disclaimers . '</div>';

echo $printcoupons;

echo '</textarea>';
?>

<script type="text/javascript">
$("#z_pick_coupon_box_0").click(function(){
	$("#z_pick_coupon_0").css('display','block');	
});

</script>
</body>
</html>