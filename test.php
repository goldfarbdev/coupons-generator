<?php
$foo = "asdf,c";

$pos = strrpos($foo, ',');  

$bar = substr_replace($foo, '', $pos, strlen(','));
echo $bar;
?>