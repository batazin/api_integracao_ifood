<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
if (function_exists('curl_init')) {
    echo "cURL extension is loaded and curl_init() is available.";
} else {
    echo "cURL extension is NOT loaded or curl_init() is NOT available.";
}
?>