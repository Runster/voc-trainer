<?php
	if(!isset($f3)) { http_response_code(404); exit; }
	
	$f3->set("page", "home.htm");
	$f3->set("title", "Welcome");
	echo View::instance()->render("layout.htm");
?>