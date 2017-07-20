<?php
header('Content-Type: text/plain');

ini_set("auto_detect_line_endings", true);
ini_set('display_errors',1); 
error_reporting(E_ALL);
$lines = file("coupons.html",FILE_IGNORE_NEW_LINES);


$coupons = '';
$rewards = '';
$redeemscript = '';
$couponvars = '';
$couponcookie = '';
$position = 1;

foreach ($lines as $line_num => $line) {
	$image = "";
	$offer = "";
	$actions = "";
	$barcode = "";
	$couponcode = "";
	$printBox = "";
	$opencouponblock = "";
	$closecouponblock = "";
	$coupon = explode("\t",$line);
	
  	$channel = $coupon[0];
  	$expiration = $coupon[1];
  	$offer = str_replace('¢','&cent;', str_replace('™','&trade;', str_replace('&reg;','<	sup>&reg;</sup>', htmlentities($coupon[2], ENT_NOQUOTES, 'UTF-8'))));
  	$itemname = str_replace('™','&trade;', str_replace('&reg;','<sup>&reg;</sup>', 	htmlentities($coupon[3], ENT_NOQUOTES, 'UTF-8')));
  	$image = strtolower($coupon[4]);
  	$omnicode = $coupon[5];
  	$storecode = $coupon[6];
  	$onlinecode = $coupon[7];
  	$caturl = $coupon[8];
  	$icid = $coupon[9];
  	$category = strtolower($coupon[10]);
  	$rewards = $coupon[11];
  	$featured = $coupon[12];
  	$featurecre = $coupon[13];
  	$disclaimer = str_replace('™','&trade;', str_replace('&reg;','<sup>&reg;</sup>', 	htmlentities($coupon[14], ENT_NOQUOTES, 'UTF-8')));
  	$mobile = strtolower(trim($coupon[15]));

  	if($mobile === 'no'){  

		// DETERMINE CHANNEL
		// Omni
		if(strlen($omnicode) > 0){
		  	// Check for CPC design/documents site coupons
		  	if(preg_match('/documents.staples.com/i', $caturl) || preg_match('/design.staples.com/i', $caturl)) {
		    	$couponcode = '<div class="z_online_code">In-Store/Online/Phone Coupon Code: <b>' . $omnicode . '</b></div>';
		  		$actions = '<a href="http://' . $caturl . '?' . $icid . '" class="z_shop_now z_noprint z_cta" target="_blank">Shop Now</a>
		  		<div class="z_print_gutter z_noprint">or</div>
		  		<label class="z_noprint z_print_select"><input type="checkbox" id="z_select_' . $position . '"> Select to print</label>';
		  		$barcode = '<div class="z_barcode z_mobile"><img src="http://easy.staples.com/bca/z/image?ct=CODE128&bc=' . $omnicode . '&st=N&txtm=0.0" /></div>';
		  	} else {
		  		$couponcode = '<div class="z_online_code">In-Store/Online/Phone Coupon Code: <b>' . $omnicode . '</b></div>';
		  		$actions = '<a href="#" class="z_cta z_redeem z_noprint">Redeem Online</a>
		  		<div class="z_redeemed_text">Redeemed</div>
		  		<a href="/' . $caturl . '?' . $icid . '" class="z_shop_now z_noprint" target="_blank">Shop Now</a>
		  		<div class="z_print_gutter z_noprint">or</div>
		  		<label class="z_noprint z_print_select"><input type="checkbox" id="z_select_' . $position . '"> Select to print</label>';
		  		$barcode = '<div class="z_barcode z_mobile"><img src="http://easy.staples.com/bca/z/image?ct=CODE128&bc=' . $omnicode . '&st=N&txtm=0.0" /></div>';
		    }
		}
		// In-store & online
		elseif(strlen($storecode) > 0 && strlen($onlinecode) > 0){
			$couponcode = '<div class="z_store_code">In-Store Coupon Code: <b>' . $storecode . '</b></div>
			<div class="z_online_code">Online/Phone Coupon Code: <b>' . $onlinecode . '</b></div>';
			$actions = '<a href="#" class="z_cta z_redeem z_noprint">Redeem Online</a>
			<div class="z_redeemed_text z_noprint">Redeemed</div>
			<a href="/' . $caturl . '?' . $icid . '" class="z_shop_now z_noprint" target="_blank">Shop Now</a>
			<div class="z_print_gutter z_noprint">or</div>
			<label class="z_noprint z_print_select"><input type="checkbox" id="z_select_' . $position . '"> Select to print</label>';
			$barcode = '<div class="z_barcode z_mobile"><img src="http://easy.staples.com/bca/z/image?ct=CODE128&bc=' . $storecode . '&st=N&txtm=0.0" /></div>';
		}
		// Store only
		elseif(strlen($storecode) > 0 && strlen($onlinecode) == 0){
			$couponcode = '<div class="z_store_code">In-Store Coupon Code: <b>' . $storecode . '</b></div>';
			$actions = '<div class="z_store_only z_noprint">In Store Only</div>
			<label class="z_noprint z_print_select"><input type="checkbox" id="z_select_' . $position . '"> Select to print</label>';
			$barcode = '<div class="z_barcode z_mobile"><img src="http://easy.staples.com/bca/z/image?ct=CODE128&bc=' . $storecode . '&st=N&txtm=0.0" /></div>';
		}
		// Online only
		elseif(strlen($storecode) == 0 && strlen($onlinecode) > 0){
	  	// Online, no phone
	  	if(preg_match('/\bValid online only\b/i', $channel)) {
	    	$couponcode = '<div class="z_online_code">Online Coupon Code: <b>' . $onlinecode . '</b></div>';
	  	}
	  	// Online and phone
	  	else {
	    	$couponcode = '<div class="z_online_code">Online/Phone Coupon Code: <b>' . $onlinecode . '</b></div>';
	  	}
			$actions = '<a href="#" class="z_cta z_redeem z_noprint">Redeem Online</a>
			<div class="z_redeemed_text z_noprint">Redeemed</div>
			<a href="/' . $caturl . '?' . $icid . '" class="z_shop_now z_noprint" target="_blank">Shop Now</a>';
			$barcode = '';
		}


		// S-IMAGE
		if(strlen($featured) > 0) {
	  		$image = '<img src="http://s7d5.scene7.com/is/image/Staples/' . $image . '_sc7?$Advsku$" align="center" class="z_noprint" />';
	  	} else {
	    	$image = '<img src="http://s7d5.scene7.com/is/image/Staples/' . $image . '_sc7?$cprelatedimage$" align="center" class="z_noprint" />';
		}


		// OFFER TEXT
		$offer = '<div class="z_savings">' . $offer . '</div>
			<div class="z_offer">' . $itemname . '</div>';


		// EXPIRATION DATE
		$endDate = explode('Expires ', $expiration);
		$expDate = explode('.', $endDate[1]);
		$expDate = $expDate[0];


		// AUTO-ADD JS
		if(strlen($omnicode) > 0){
			$couponvars = '	// COUPON #' . $position . '
		// Redeem URL & Click function
		var z_couponAtcUrl_' . $position . ' = "/office/supplies/StaplesAddToCart?storeId=10001&ocf=Y&promoName=' . $omnicode . '&' . $icid'";
		if(document.location.hostname.match(/(m\.staples)/)) {
			$("#z_coupon_' . $position . ' .z_actions a.z_redeem").prop({href: z_couponAtcUrl_' . $position . ', target: "_blank"});
			$("#z_coupon_' . $position . ' .z_actions a.z_redeem").on("click", function(){
				document.cookie="z_coupon_' . $omnicode . '=' . $omnicode . '; path=/";
			});
		} else {
			$("#z_coupon_' . $position . ' .z_actions a.z_redeem").on("click", function(e){
				jQuery.ajax({
					url: z_couponAtcUrl_' . $position . ',
					dataType: "html",
					success: function(response) {
						document.cookie="z_coupon_' . $omnicode . '=' . $omnicode . '; path=/";
					}
				});
				e.preventDefault();
			});
		}
		// Check for cookie
		var z_couponExists_' . $position . ' = z_getCookie("z_coupon_' . $omnicode . '");
		if(z_couponExists_' . $position . '){
			$("#z_coupon_' . $position . ' .z_actions a.z_redeem").css("opacity", 0).siblings(".z_shop_now").addClass("z_cta");
			$("#z_coupon_' . $position . ' .z_actions a.z_redeem").siblings(".z_redeemed_text").fadeIn();
		}
			';
		} elseif(strlen($onlinecode) > 0 && strtolower($onlinecode) != 'no code required') {
			$couponvars = '	// COUPON #' . $position . '
		// Redeem URL & Click function
		var z_couponAtcUrl_' . $position . ' = "/office/supplies/StaplesAddToCart?storeId=10001&ocf=Y&promoName=' . $onlinecode . '&' . $icid . '";
		if(document.location.hostname.match(/(m\.staples)/)) {
			$("#z_coupon_' . $position . ' .z_actions a.z_redeem").prop({href: z_couponAtcUrl_' . $position . ', target: "_blank"});
			$("#z_coupon_' . $position . ' .z_actions a.z_redeem").on("click", function(){
				document.cookie="z_coupon_' . $onlinecode . '=' . $onlinecode . '; path=/";
			});
		} else {
			$("#z_coupon_' . $position . ' .z_actions a.z_redeem").on("click", function(e){
				jQuery.ajax({
					url: z_couponAtcUrl_' . $position . ',
					dataType: "html",
					success: function(response) {
						document.cookie="z_coupon_' . $onlinecode . '=' . $onlinecode . '; path=/";
					}
				});
				e.preventDefault();
			});
		}
		// Check for cookie
		var z_couponExists_' . $position . ' = z_getCookie("z_coupon_' . $onlinecode . '");
		if(z_couponExists_' . $position . '){
			$("#z_coupon_' . $position . ' .z_actions a.z_redeem").css("opacity", 0).siblings(".z_shop_now").addClass("z_cta");
			$("#z_coupon_' . $position . ' .z_actions a.z_redeem").siblings(".z_redeemed_text").fadeIn();
		}
			';
		} else {
			$couponvars = '';
		}


		// REWARDS & FEATURED DEALS
		if(strlen($rewards) > 0) {
			$opencouponblock = '<div class="z_coupon_block z_rewards z_noprint" id="z_coupon_' . $position . '">';
			$closecouponblock = '</div>';
		} elseif(strlen($featured) > 0) {
			$opencouponblock = '<div class="z_coupon_block z_featured_coupon z_noprint" id="z_coupon_' . $position . '">';
			$closecouponblock = '</div>';
		} else {
			$opencouponblock = '<div class="z_coupon_block z_noprint z_' . $category . '" id="z_coupon_' . $position . '">';
			$closecouponblock = '</div>';
		}


		// OUTPUT SETUP
		$coupons .= $opencouponblock . '
			<div class="z_top">
				<div class="z_image z_noprint">' . $image . '</div>
				' . $offer . '
			</div>
			<div class="z_actions">
				' . $actions . '
				' . $barcode . '
			</div>
			<div class="z_share z_noprint">SHARE
				<a href="https://www.facebook.com/sharer.php?u=http://www.staples.com/coupons/" class="z_facebook" target="_blank"><img src="/sbd/cre/coupons/images/social.png" /></a>
				<a href="#" class="z_twitter" target="_blank"><img src="/sbd/cre/coupons/images/social.png" /></a>
				<a href="#" class="z_email"><img src="/sbd/cre/coupons/images/social.png" /></a>
				<a href="#" class="z_sms"><img src="/sbd/cre/coupons/images/social.png" /></a>
			</div>
			<div class="z_details">
				<div class="z_exp">Expires <b>' . $expDate . '</b></div>
				' . $couponcode . '
			</div>
			<div class="z_disclaimer">
				<a href="#" class="z_disclaimer_link z_noprint">See Disclaimer</a>
				<p class="z_disclaimer_copy">' . $disclaimer . '</p>
			</div>
		' . $closecouponblock . '
		' . PHP_EOL;
		$redeemscript .= $couponvars . PHP_EOL;
	}

	$position++;
}
?>

<?php
// FINAL OUTPUT
echo $coupons;
echo '<script type="text/javascript" class="agencey_dev">
	function z_getCookie(z_cname) {
	    var name = z_cname + "=";
	    var ca = document.cookie.split(";");
	    for(var i=0; i<ca.length; i++) {
	        var c = ca[i];
	        while (c.charAt(0)==" ") c = c.substring(1);
	        if (c.indexOf(name) != -1) return c.substring(name.length,c.length);
	    }
	    return "";
	}' . $redeemscript . '</script>';
?>
