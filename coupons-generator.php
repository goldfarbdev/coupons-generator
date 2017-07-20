<?php
header('Content-Type: text/plain');

ini_set("auto_detect_line_endings", true);
ini_set('display_errors',1); 
error_reporting(E_ALL);

$date1 = $_POST['date1'];
$date2 = $_POST['date2'];
$couponCount = $_POST['couponCount'];
$coupons1 = $_POST['coupons1'];
$coupons2 = $_POST['coupons2'];
$dir = createDirectory($date1);


//COUPONS FOR FIRST DATE SPAN
$coupon1HTML = createCouponsHTML($date1, $coupons1);
createFile($date1, $coupon1HTML, $dir, 'html');
$couponsAndroid1 = createAndroidJSON($coupons1);
createFile($date1.'_ANDROID', $couponsAndroid1, $dir, 'json');
$couponsIOS1 = createIOSJSON($coupons1);
createFile($date1.'_ANDROID', $couponsIOS1, $dir, 'json');

//COUPONS FOR 2ND DATE SPAN
if($coupons2 !== '')
{
    $coupon2HTML = createCouponsHTML($date2,$coupons2);
    createFile($date2, $coupon2HTML, $dir, 'html');
    $couponsAndroid2 = createAndroidJSON($coupons2);
    createFile($date2.'_IOS', $couponsAndroid1, $dir, 'json');
    $couponsIOS2 = createIOSJSON($coupons2);
    createFile($date2.'_IOS', $couponsIOS2, $dir, 'json');
}


function createCouponsHTML($date, $coupons)
{
    $lines = preg_split('/$\R?^/m', $coupons);

    $coupons = '';
    $rewards = '';
    $redeemscript = '';
    $couponvars = '';
    $couponcookie = '';
    $position = 1;

    foreach ($lines as $line_num => $line) {
        if ($line_num !== 0) {
            $image = "";
            $offer = "";
            $actions = "";
            $barcode = "";
            $couponcode = "";
            $printBox = "";
            $opencouponblock = "";
            $closecouponblock = "";
            $coupon = explode("\t", $line);

            $channel = $coupon[0];
            $expiration = $coupon[1];
            $offer = str_replace('¢', '&cent;', str_replace('™', '&trade;', str_replace('&reg;', '<sup>&reg;</sup>', htmlentities($coupon[2], ENT_NOQUOTES, 'UTF-8'))));
            $itemname = str_replace('™', '&trade;', str_replace('&reg;', '<sup>&reg;</sup>', htmlentities($coupon[3], ENT_NOQUOTES, 'UTF-8')));
            $image = strtolower($coupon[4]);
            $omnicode = $coupon[5];
            $storecode = $coupon[6];
            $onlinecode = $coupon[7];
            $caturl = $coupon[8];

            // hack to remove extra ? from url
            if (stripos($caturl, '?') === false) {
                $query_start = '?';
            } else {
                $query_start = '&';
            }


            $icid = $coupon[9];
            $category = strtolower($coupon[10]);
            $rewards = $coupon[11];
            $featured = $coupon[12];
            $featurecre = $coupon[13];
            $disclaimer = str_replace('™', '&trade;', str_replace('&reg;', '<sup>&reg;</sup>', htmlentities($coupon[14], ENT_NOQUOTES, 'UTF-8')));


            // DETERMINE CHANNEL
            // Omni
            if (strlen($omnicode) > 0) {
                // Check for CPC design/documents site coupons
                if (preg_match('/documents.staples.com/i', $caturl) || preg_match('/design.staples.com/i', $caturl)) {
                    $couponcode = '<div class="z_online_code">In-Store/Online/Phone Coupon Code: <b>' . $omnicode . '</b></div>';
                    $actions = '<a href="https://' . $caturl . '?' . $icid . '" class="z_shop_now z_noprint z_cta" target="_blank">Shop Now</a>
                <div class="z_print_gutter z_noprint">or</div>
                <label class="z_noprint z_print_select"><input type="checkbox" id="z_select_' . $position . '"> Select to print</label>';
                    $barcode = '<div class="z_barcode z_mobile"><img src="https://easy.staples.com/bca/z/image?ct=CODE128&bc=' . $omnicode . '&st=N&txtm=0.0" /></div>';
                } else {
                    $couponcode = '<div class="z_online_code">In-Store/Online/Phone Coupon Code: <b>' . $omnicode . '</b></div>';
                    $actions = '<a href="#" class="z_cta z_redeem z_noprint">Redeem Online</a>
                <div class="z_redeemed_text">Redeemed</div>
                <a href="/' . $caturl . $query_start . $icid . '" class="z_shop_now z_noprint" target="_blank">Shop Now</a>
                <div class="z_print_gutter z_noprint">or</div>
                <label class="z_noprint z_print_select"><input type="checkbox" id="z_select_' . $position . '"> Select to print</label>';
                    $barcode = '<div class="z_barcode z_mobile"><img src="https://easy.staples.com/bca/z/image?ct=CODE128&bc=' . $omnicode . '&st=N&txtm=0.0" /></div>';
                }
            } // In-store & online
            elseif (strlen($storecode) > 0 && strlen($onlinecode) > 0) {
                $couponcode = '<div class="z_store_code">In-Store Coupon Code: <b>' . $storecode . '</b></div>
            <div class="z_online_code">Online/Phone Coupon Code: <b>' . $onlinecode . '</b></div>';
                $actions = '<a href="#" class="z_cta z_redeem z_noprint">Redeem Online</a>
            <div class="z_redeemed_text z_noprint">Redeemed</div>
            <a href="/' . $caturl . $query_start . $icid . '" class="z_shop_now z_noprint" target="_blank">Shop Now</a>
            <div class="z_print_gutter z_noprint">or</div>
            <label class="z_noprint z_print_select"><input type="checkbox" id="z_select_' . $position . '"> Select to print</label>';
                $barcode = '<div class="z_barcode z_mobile"><img src="https://easy.staples.com/bca/z/image?ct=CODE128&bc=' . $storecode . '&st=N&txtm=0.0" /></div>';
            } // Store only
            elseif (strlen($storecode) > 0 && strlen($onlinecode) == 0) {
                $couponcode = '<div class="z_store_code">In-Store Coupon Code: <b>' . $storecode . '</b></div>';
                $actions = '<div class="z_store_only z_noprint">In Store Only</div>
            <label class="z_noprint z_print_select"><input type="checkbox" id="z_select_' . $position . '"> Select to print</label>';
                $barcode = '<div class="z_barcode z_mobile"><img src="https://easy.staples.com/bca/z/image?ct=CODE128&bc=' . $storecode . '&st=N&txtm=0.0" /></div>';
            } // Online only
            elseif (strlen($storecode) == 0 && strlen($onlinecode) > 0) {
                // Online, no phone
                if (preg_match('/\bValid online only\b/i', $channel)) {
                    $couponcode = '<div class="z_online_code">Online Coupon Code: <b>' . $onlinecode . '</b></div>';
                } // Online and phone
                else {
                    $couponcode = '<div class="z_online_code">Online/Phone Coupon Code: <b>' . $onlinecode . '</b></div>';
                }
                $actions = '<a href="#" class="z_cta z_redeem z_noprint">Redeem Online</a>
            <div class="z_redeemed_text z_noprint">Redeemed</div>
            <a href="/' . $caturl . $query_start . $icid . '" class="z_shop_now z_noprint" target="_blank">Shop Now</a>';
                $barcode = '';
            }


            // S-IMAGE
            if (strlen($featured) > 0) {
                $image = '<img src="https://s7d5.scene7.com/is/image/Staples/' . $image . '_sc7?$Advsku$" align="center" class="z_noprint" />';
            } else {
                $image = '<img src="https://s7d5.scene7.com/is/image/Staples/' . $image . '_sc7?$cprelatedimage$" align="center" class="z_noprint" />';
            }


            // OFFER TEXT
            $offer = '<div class="z_savings">' . $offer . '</div>
            <div class="z_offer">' . $itemname . '</div>';


            // EXPIRATION DATE
            $endDate = explode('Expires ', $expiration);
            $expDate = explode('.', $endDate[1]);
            $expDate = $expDate[0];


            // AUTO-ADD JS
            if (strlen($omnicode) > 0) {
                $couponvars = '	// COUPON #' . $position . '
        // Redeem URL & Click function
        var z_couponAtcUrl_' . $position . ' = "/office/supplies/StaplesAddToCart?storeId=10001&ocf=Y&promoName=' . $omnicode . '&URL=' . $caturl . '&' . $icid . '";
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
        // Check for cookie
        var z_couponExists_' . $position . ' = z_getCookie("z_coupon_' . $omnicode . '");
        if(z_couponExists_' . $position . '){
            $("#z_coupon_' . $position . ' .z_actions a.z_redeem").css("opacity", 0).siblings(".z_shop_now").addClass("z_cta");
            $("#z_coupon_' . $position . ' .z_actions a.z_redeem").siblings(".z_redeemed_text").fadeIn();
        }
            ';
            } elseif (strlen($onlinecode) > 0 && strtolower($onlinecode) != 'no code required') {
                $couponvars = '	// COUPON #' . $position . '
        // Redeem URL & Click function
        var z_couponAtcUrl_' . $position . ' = "/office/supplies/StaplesAddToCart?storeId=10001&ocf=Y&promoName=' . $onlinecode . '&URL=' . $caturl . '&' . $icid . '";
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
            if (strlen($rewards) > 0) {
                $opencouponblock = '<div class="z_coupon_block z_rewards z_noprint" id="z_coupon_' . $position . '">';
                $closecouponblock = '</div>';
            } elseif (strlen($featured) > 0) {
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
            <a href="https://www.facebook.com/sharer.php?u=https://www.staples.com/coupons/" class="z_facebook" target="_blank"><img src="/sbd/cre/coupons/images/social.png" /></a>
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


            $position++;
        }
    }



    $output = <<<HTML
        $coupons <script type="text/javascript" class="agencey_dev">
        function z_getCookie(z_cname) {
            var name = z_cname + "=";
            var ca = document.cookie.split(";");
            for(var i=0; i<ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0)==" ") c = c.substring(1);
                if (c.indexOf(name) != -1) return c.substring(name.length,c.length);
            }
            return "";
        } $redeemscript</script>
HTML;

    return $output;
}

function createDirectory($date)
{
    $dir = $_SERVER['DOCUMENT_ROOT'] . '/agency-dev/coupon-page/' . $date . '/';
//    $dir = getcwd()  . '/' . $date . '/';  //THIS IS FOR LOCAL DIR
    if (!file_exists($dir)) {
        mkdir($date, 0777);
    }

    return $dir;
}

function createFile($date, $output, $dir, $ext) {
    $fileNameDate = substr($date, 4 );
    $fileName =  $dir.'index_'.$fileNameDate. "." . $ext;
    fopen($fileName, "w+");

    file_put_contents($fileName,'DIR is: ' .$dir . ' <br/>' . $output);
}

function createAndroidJSON ($couponData)
{

//this tells the browser that the page is really a json file
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');

    ini_set("auto_detect_line_endings", true);

    $lines = preg_split('/$\R?^/m', $couponData);

    $coupons = '';
    $rewards = '';
    $redeemscript = '';
    $couponvars = '';
    $couponcookie = '';
    $position = 1;

    $totalCouponCount = (count($couponData));

    foreach ($lines as $line_num => $line) {
        if ($line_num !== 0) {
            $image = '';
            $offer = '';
            $details = '';
            $barcode = '';
            $couponcodes = '';
            $coupontype = '';
            $contentUrl = '';
            $jsonComma = '';

            $coupon = explode("\t", $line);
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

            if ($mobile === 'yes') {
                // DETERMINE CHANNEL
                // Omni
                if (strlen($omnicode) > 0) {
                    $coupontype = 'Omni';
                    $couponcodes = '"inStoreCouponCode": "' . $omnicode . '",
                      "onlineCouponCode": "' . $omnicode . '",';
                } // In-store & online
                elseif (strlen($storecode) > 0 && strlen($onlinecode) > 0) {
                    $coupontype = 'Omni';
                    $couponcodes = '"inStoreCouponCode": "' . $storecode . '",
                      "onlineCouponCode": "' . $onlinecode . '",';
                } // Store only
                elseif (strlen($storecode) > 0 && strlen($onlinecode) == 0) {
                    $coupontype = 'In-store';
                    $couponcodes = '"inStoreCouponCode": "' . $storecode . '",
                      "onlineCouponCode": "",';
                } // Online only
                elseif (strlen($storecode) == 0 && strlen($onlinecode) > 0) {
                    $coupontype = 'online';
                    $couponcodes = '"inStoreCouponCode": "",
                      "onlineCouponCode": "' . $onlinecode . '",';
                }

                // DETERMINE PRODUCT/CATEGORY
                if (strpos($caturl, 'product_') !== false) {
                    $skuNumber = explode('product_', $caturl);
                    $skuOut = explode('?fids=', $skuNumber[1]);
                    $skuOut1 = isset($skuOut[1]) ? isset($skuOut[1]) : '';
                    $contentUrl = '
                      "contentType": "sku",
                      "contentId": "' . $skuOut[0] . '",
                      "fids": "' . $skuOut1 . '",';
                } else if (strpos($caturl, 'cat_') !== false) {
                    $bundle = explode('cat_', $caturl);
                    $bundleOut = explode('?fids=', $bundle[1]);
                    $contentUrl = '
                      "contentType": "category",
                      "contentId": "' . $bundleOut[0] . '",
                      "fids": "' . $bundleOut[1] . '",';
                } else {
                    $contentUrl = '';
                }


                // EXPIRATION DATE
                $endDate = explode("Expires ", $expiration);
                $endDate = explode(".", $endDate[1]);
                $endDate = explode("/", $endDate[0]);
                $expDate = sprintf("%02d", $endDate[0]) . '/' . sprintf("%02d", $endDate[1]) . '/' . $endDate[2];


                // Check for last coupon
                if ($position != $totalCouponCount) {
                    $jsonComma = ",";
                } else {
                    $jsonComma = "";
                }


                $coupons .= '  {
                  "image": "' . $image . '",
                  "offer": "' . $offer . '",
                  "itemName": "' . $itemname . '",
                  "couponType": "' . $coupontype . '",
                  "expiryDate": "' . $expDate . '",
                  ' . $couponcodes . '' . $contentUrl . '
                  "disclaimer": "' . $disclaimer . '"
                }' . $jsonComma . PHP_EOL;
            } else if ($position === $totalCouponCount) {
                $coupons = removeComma($coupons);
            }
            $position++;
        }
    }

    return '{"coupons": [' . $coupons . ']}';
}

function createIOSJSON ($couponData) {
    ini_set("auto_detect_line_endings", true);

    $lines = preg_split('/$\R?^/m', $couponData);

    $coupons = '';
    $rewards = '';
    $redeemscript = '';
    $couponvars = '';
    $couponcookie = '';
    $position = 1;

    $totalCouponCount = (count($lines));

    foreach ($lines as $line_num => $line) {
        if ($line_num !== 0) {

            $image = '';
            $offer = '';
            $details = '';
            $barcode = '';
            $couponcodes = '';
            $coupontype = '';
            $contentUrl = '';
            $jsonComma = '';

            $coupon = explode("\t", $line);
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
            $disclaimer = addslashes(htmlentities($coupon[14], ENT_NOQUOTES, 'UTF-8'));
            $mobile = strtolower(trim($coupon[15]));

            if ($mobile === 'yes') {

                // DETERMINE CHANNEL
                // Omni
                if (strlen($omnicode) > 0) {
                    $coupontype = 'Omni';
                    $couponcodes = '"couponCode": "' . $omnicode . '",';
                } // In-store & online
                elseif (strlen($storecode) > 0 && strlen($onlinecode) > 0) {
                    $coupontype = 'Omni';
                    $couponcodes = '"inStoreCouponCode": "' . $storecode . '",
                        "onlineCouponCode": "' . $onlinecode . '",';
                } // Store only
                elseif (strlen($storecode) > 0 && strlen($onlinecode) == 0) {
                    $coupontype = 'In-store';
                    $couponcodes = '"couponCode": "' . $storecode . '",';
                } // Online only
                elseif (strlen($storecode) == 0 && strlen($onlinecode) > 0) {
                    $coupontype = 'online';
                    $couponcodes = '"couponCode": "' . $onlinecode . '",';
                }

                // DETERMINE PRODUCT/CATEGORY
                if (strpos($caturl, 'product_') !== false) {
                    $skuNumber = explode('product_', $caturl);
                    $skuOut = explode('?fids', $skuNumber[1]);
                    $contentUrl = '
                        "skuNumber": "' . $skuOut[0] . '",';
                } else if (strpos($caturl, 'cat_') !== false) {
                    $bundle = explode('cat_', $caturl);
                    $bundleOut = explode('?fids', $bundle[1]);
                    $contentUrl = '
                        "contentSourceUrl": "category/identifier/' . $bundleOut[0] . '",';
                } else {
                    $contentUrl = '';
                }


                // EXPIRATION DATE
                $endDate = explode("Expires ", $expiration);
                $endDate = explode(".", $endDate[1]);
                $endDate = explode("/", $endDate[0]);
                $expDate = sprintf("%02d", $endDate[0]) . '/' . sprintf("%02d", $endDate[1]) . '/' . $endDate[2];


                // Check for last coupon
                if ($position != $totalCouponCount) {
                    $jsonComma = ",";
                } else {
                    $jsonComma = "";
                }


                $coupons .= '  {
                    "image": "' . $image . '",
                    "offer": "' . $offer . '",
                    "itemName": "' . $itemname . '",
                    "couponType": "' . $coupontype . '",
                    "expiryDate": "' . $expDate . '",
                    ' . $couponcodes . '' . $contentUrl . '
                    "disclaimer": "' . $disclaimer . '"
                }' . $jsonComma . PHP_EOL;
            } else if ($position === $totalCouponCount) {
                $coupons = removeComma($coupons);
            }

            $position++;
        }
    }

    return '"coupons": [' . $coupons . '],';
}

function removeComma($myJSONString) {
    $pos = strrpos($myJSONString, ',');

    $myNewJSONString = substr_replace($myJSONString, '', $pos, strlen(','));
    return $myNewJSONString;
}

// FINAL OUTPUT
//echo $coupons;
// echo '<script type="text/javascript" class="agencey_dev">
// 	function z_getCookie(z_cname) {
// 	    var name = z_cname + "=";
// 	    var ca = document.cookie.split(";");
// 	    for(var i=0; i<ca.length; i++) {
// 	        var c = ca[i];
// 	        while (c.charAt(0)==" ") c = c.substring(1);
// 	        if (c.indexOf(name) != -1) return c.substring(name.length,c.length);
// 	    }
// 	    return "";
// 	}' . $redeemscript . '</script>';

class HZip
{
    /**
     * Add files and sub-directories in a folder to zip file.
     * @param string $folder
     * @param ZipArchive $zipFile
     * @param int $exclusiveLength Number of text to be exclusived from the file path.
     */
    private static function folderToZip($folder, &$zipFile, $exclusiveLength) {
        $handle = opendir($folder);
        while (false !== $f = readdir($handle)) {
            if ($f != '.' && $f != '..') {
                $filePath = "$folder/$f";
                // Remove prefix from file path before add to zip.
                $localPath = substr($filePath, $exclusiveLength);
                if (is_file($filePath)) {
                    $zipFile->addFile($filePath, $localPath);
                } elseif (is_dir($filePath)) {
                    // Add sub-directory.
                    $zipFile->addEmptyDir($localPath);
                    self::folderToZip($filePath, $zipFile, $exclusiveLength);
                }
            }
        }
        closedir($handle);
    }

    /**
     * Zip a folder (include itself).
     * Usage:
     *   HZip::zipDir('/path/to/sourceDir', '/path/to/out.zip');
     *
     * @param string $sourcePath Path of directory to be zip.
     * @param string $outZipPath Path of output zip file.
     */
    public static function zipDir($sourcePath, $outZipPath)
    {
        $pathInfo = pathInfo($sourcePath);
        $parentPath = $pathInfo['dirname'];
        $dirName = $pathInfo['basename'];

        $z = new ZipArchive();
        $z->open($outZipPath, ZIPARCHIVE::CREATE);
        $z->addEmptyDir($dirName);
        self::folderToZip($sourcePath, $z, strlen("$parentPath/"));
        $z->close();
        header('Location: http://'.$_SERVER['HTTP_HOST'].'/agency-dev/coupon-page/'.$outZipPath);
    }
}

$zipFileName = $date1.'.zip';
HZip::zipDir($dir, $zipFileName);
?>
