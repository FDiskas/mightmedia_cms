<?php
include 'config/head.php';

require 'themes/material/config.php';
require 'themes/material/functions.php';
require 'config/buttons.php';
require 'config/menu.php';

include 'config/functions.php';
require 'themes/material/form.class.php';

//todo: make it safe
if(isset($_GET['a']) && $_GET['a'] === 'ajax') {
	include 'ajax.php';
	exit;
}

require 'themes/material/index.php';