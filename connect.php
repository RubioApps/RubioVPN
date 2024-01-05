<?php

include "constants.php";
include "functions.php";

$newnode = $_POST['node'];
if($newnode) {
	die(start_vpn($newnode));
} else {
	die(false);
}