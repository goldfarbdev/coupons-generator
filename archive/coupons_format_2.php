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


$t = 0;
foreach ($lines as $line_num => $line) {
	$image = "";
	$offer = "";
	$restrictions = "";
	$withcoupon = "";
	
	$coupon = explode("\t",$line);
	$couponcode = "";
	if($coupon[9] != "" && $coupon[10] == ""){
		$couponcode = 'Coupon code: ' . $coupon[9];
	}elseif($coupon[9] != "" && $coupon[10] != ""){
		$couponcode = 'In-store coupon code: ' . $coupon[9] . ' <br /> Online or phone coupon code: ' . $coupon[10];
	}elseif($coupon[9] == "" && $coupon[10] != ""){
		$couponcode = 'Coupon code: ' . $coupon[10];
	}
	
	if($coupon[3])
	$withcoupon = '<p class="offerb">with coupon.</p>';
	
	if($coupon[5])
	$items = '<p class="offerb">' . $coupon[5] . '</div>';
	
	if($coupon[6])
	$restrictions = $coupon[6] . ' ';
	
	if($coupon[7])
	$restrictions .= $coupon[7] . ' ';
	
	if($coupon[8])
	$restrictions .= $coupon[8];
	
	$restrictions = '<p class="offerb">' . $restrictions . '</p>';
	
	if(preg_match("/S[0-9]{7}/",$coupon[11])){
		preg_match('/S[0-9]{7}/', $coupon[11], $matches);
		$image = strtolower($matches[0]);

		$image = '<img src="http://s7d5.scene7.com/is/image/Staples/' . $image . '_sc7?$std$" class="z_coupon_image" />';
	}
	
	if(preg_match("/\\\$/",$coupon[2]) || preg_match("/%/",$coupon[2])){
		$offer = $coupon[2];
	}else{
		$offer = "$" . $coupon[2];
	}
	echo '
<div class="z_coupon">
	<div id="z_pick_coupon_box_' . $t . '" class="z_pick_coupon"><input type="checkbox" id="z_pick_coupon_box' . $t . ' "> Print this coupon</div>
	<div class="z_print_coupon z_noprint" id="z_pick_coupon_' . $t . '">
		<table cellspacing="0" cellpadding="0" border="0" width="625" bgcolor="#ffffff" class="printablecoupon">
			<tr>
				<td valign="top" align="center">
					<table cellspacing="0" cellpadding="0" border="0" width="625" bgcolor="#000000">
						<tr>
							<td align="center">
								<p class="subhead">' . $coupon[0] . '</p>
							</td>
						</tr>
					</table>
					<table cellspacing="0" cellpadding="0" border="0" width="625" class="border">
						<tr>
							<td colspan="2">
								' . $image . '
								<p class="offer1"">' . $offer . '</p>
								' . $withcoupon . '
								<p class="offer2">' . $coupon[4] . '*</p>
								' . $restrictions . '
								' . $items . '
								<p class="offerb">' . $couponcode . '</p>
							</td>
						</tr>
						<tr>
							<td>
								<p class="disclaimer"><b>*' . $coupon[1] . '</b> ' . $coupon[12] . '</p>
							</td>
							<td valign="bottom"><img src="http://www.staples.com/sbd/img/cre/logo/lg100-staples-logo.gif" width="100" height="50" alt="" style="margin:0px 5px 10px 0px"></td>								
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
</div>';
for($i=0;$i<=14;$i++)
	$coupon[$i] = "";
$t++;
}
unset($coupon);
?>
<?php

?>
<script type="text/javascript">
$("#z_pick_coupon_box_0").click(function(){
	$("#z_pick_coupon_0").css('display','block');	
});

</script>
</body>
</html>