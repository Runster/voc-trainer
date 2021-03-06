<?php
	if(!isset($f3)) { http_response_code(404); exit; }
	
	if($f3->get("loggedIn") == false)
	{
		$f3->reroute("/login/user");
		exit;
	}
	
	$f3->set("title", "Settings");
	$f3->set("page", "user_settings.htm");
	$f3->set("headline", "Settings");
	
	if(isset($_GET["changepw"]) AND isset($_POST["newPassword"]))
	{
		if(check_password($f3->get("userid"), generate_password($_POST["currentPassword"], $f3->get("userid"))) == true)
		{
			if($_POST["newPassword"] == $_POST["newPasswordRepeating"])
			{
				$newPassword = generate_password($_POST["newPassword"], $f3->get("userid"));
				$f3->db->exec("UPDATE ".$f3->get("prefix")."user SET pw = '".$newPassword."' WHERE id = '". $f3->get("userid") ."' LIMIT 1");
				
				$f3->set("changePasswordState", "success");
			}
			else
			{
				$f3->set("changePasswordState", "notMatching");
			}
			
		}
		else
		{
			$f3->set("changePasswordState", "notCorrect");
		}
	}


	echo View::instance()->render("layout.htm");
?>
