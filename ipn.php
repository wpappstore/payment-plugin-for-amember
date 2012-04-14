<?php 
require_once("../../../config.inc.php");

$pl = & instantiate_plugin('payment', 'wpappstore');
$pl->handle_postback($_POST);
