<?php
//this tells the browser that the page is really a json file
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
 
ini_set("auto_detect_line_endings", true);

function removeComma($myJSONString) {
  $pos = strrpos($myJSONString, ',');  

  $myNewJSONString = substr_replace($myJSONString, '', $pos, strlen(','));
  return $myNewJSONString;
}

$lines = file("coupons.html",FILE_IGNORE_NEW_LINES);
 
$coupons = '';
$rewards = '';
$redeemscript = '';
$couponvars = '';
$couponcookie = '';
$position = 1;
 
$totalCouponCount = (count($lines));
 
foreach ($lines as $line_num => $line) {
  $image = '';
  $offer = '';
  $details = '';
  $barcode = '';
  $couponcodes = '';
  $coupontype = '';
  $contentUrl = '';
  $jsonComma = '';
 
  $coupon = explode("\t",$line);
  $channel = $coupon[0];
  $expiration = $coupon[1];
  $offer = addslashes(htmlentities($coupon[2], ENT_NOQUOTES, 'UTF-8'));
  $itemname = addslashes(htmlentities($coupon[3], ENT_NOQUOTES, 'UTF-8'));
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
  $disclaimer = $coupon[14];
  $mobile = strtolower(trim($coupon[15]));

  if($mobile === 'yes'){  
    // DETERMINE CHANNEL
  	// Omni
  	if(strlen($omnicode) > 0){
  		$coupontype = 'Omni';
  		$couponcodes = '"inStoreCouponCode": "' . $omnicode . '",
      "onlineCouponCode": "' . $omnicode . '",';
  	}
  	// In-store & online
  	elseif(strlen($storecode) > 0 && strlen($onlinecode) > 0){
  		$coupontype = 'Omni';
  		$couponcodes = '"inStoreCouponCode": "' . $storecode . '",
      "onlineCouponCode": "' . $onlinecode . '",';
  	}
  	// Store only
  	elseif(strlen($storecode) > 0 && strlen($onlinecode) == 0){
  		$coupontype = 'In-store';
  		$couponcodes = '"inStoreCouponCode": "' . $storecode . '",
      "onlineCouponCode": "",';
  	}
  	// Online only
  	elseif(strlen($storecode) == 0 && strlen($onlinecode) > 0){
  		$coupontype = 'online';
  		$couponcodes = '"inStoreCouponCode": "",
      "onlineCouponCode": "' . $onlinecode . '",';
  	}
  	
  	// DETERMINE PRODUCT/CATEGORY
  	if(strpos($caturl, 'product_') !== false) {
    	$skuNumber = explode('product_', $caturl);
    	$skuOut = explode('?fids=', $skuNumber[1]);
    	$contentUrl = '
      "contentType": "sku",
      "contentId": "' . $skuOut[0] .'",
      "fids": "' . $skuOut[1] .'",';
  	} else if(strpos($caturl, 'cat_') !== false) {
    	$bundle = explode('cat_', $caturl);
    	$bundleOut = explode('?fids=', $bundle[1]);
    	$contentUrl = '
      "contentType": "category",
      "contentId": "' . $bundleOut[0] .'",
      "fids": "' . $bundleOut[1] .'",';
  	} else {
    	$contentUrl = '';
  	}
   
   
      // EXPIRATION DATE
      $endDate = explode("Expires ", $expiration);
      $endDate = explode(".",$endDate[1]);
      $endDate = explode("/",$endDate[0]);
      $expDate = sprintf("%02d", $endDate[0]) . '/' . sprintf("%02d", $endDate[1]) . '/' . $endDate[2];
   
   
      // Check for last coupon
      if($position != $totalCouponCount) {
          $jsonComma = ",";
      }
      else { $jsonComma = ""; }
     
   
  $coupons .= '  {
      "image": "' . $image . '",
      "offer": "' . $offer . '",
      "itemName": "' . $itemname . '",
      "couponType": "' . $coupontype . '",
      "expiryDate": "' . $expDate . '",
      ' . $couponcodes . '' . $contentUrl . '
      "disclaimer": "' . $disclaimer . '"
    }' . $jsonComma . PHP_EOL;
  } else if($position === $totalCouponCount)
  {
    $coupons = removeComma($coupons);
  }
  $position++;
}

/*"image": "s0928757",
      "offer": "Free",
      "itemName": "$15 iTunes® Gift Card with your purchase of any tax software.",
      "expiryDate": "3/26/16.",
      "couponType": "Omni",
      "inStoreCouponCode": "No Code Required",
      "onlineCouponCode": "72716",
      "contentType": "category",
      "contentId": "CL166056",
      "fids": "4214925692||4214925646",

"contentType": "sku",
      "contentId": "329987",
      "fids": "",
      
      "contentType": "sku",
      "contentId": "1924438",
      "fids": "",
      
      "contentType": "category",
      "contentId": "CL141474",
      "fids": "",*/
      
echo '{';
echo '"coupons": [
';
echo $coupons;
echo "    ]";
echo '}';
?>