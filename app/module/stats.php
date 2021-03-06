<?php
if(!isset($f3)) { http_response_code(404); exit; }	

function stats_get_level($wordUnitID)
{
	global $f3;
	
	$currentStats = $f3->db->exec("SELECT *  FROM ".$f3->get("prefix")."stats WHERE word_unit = '". $wordUnitID ."' AND user = '". $f3->get("userid") ."' LIMIT 1");
	
	if(!isset($currentStats[0]["id"]))
	{
		return 0; // The user never trained this word
	}
	else
	{
		return $currentStats[0]["section"];
	}
}

function stats_get_level_name($levelNum)
{
	switch($levelNum)
	{
		case 0:
			return "New words";
			break;
		
		case 1:
			return "Level 1";
			break;
			
		case 2:
			return "Level 2";
			break;
			
		case 3:
			return "Level 3";
			break;
			
		case 4:
			return "Level 4";
			break;
	}
}

function stats_get_specific_level_with_wordUnits($level, $purposeAllLevels = false)
{
	global $f3;
	
	$levelWordUnits = array();
	foreach($f3->db->exec("SELECT word_unit FROM ".$f3->get("prefix")."stats WHERE user = '". $f3->get("userid") ."' AND section = '". $level ."'") AS $level)
	{
		if($purposeAllLevels == true)
		{
			$levelWordUnits[$level["word_unit"]] = $level["word_unit"];
		}
		else
		{
			$levelWordUnits[] = $level["word_unit"];
		}
	}
	
	return $levelWordUnits;
}

function stats_get_levels_with_word_amount()
{
	global $f3;

	$levels = array();
	$allWordUnits = array();
	$allWordUnitsWithStats = array();
	
	for($i = 1; $i <= 4; $i++)
	{
		$levelWordUnits = stats_get_specific_level_with_wordUnits($i, true);
		$levels[$i]["title"] = stats_get_level_name($i);
		$levels[$i]["wordUnits"] = $levelWordUnits;
		$levels[$i]["amount"] =  count($levelWordUnits);
		
		$allWordUnitsWithStats = array_merge($allWordUnitsWithStats, $levelWordUnits);
	}
	
	foreach($f3->db->exec("SELECT * FROM ".$f3->get("prefix")."chapter WHERE lang = '". $f3->get("langID") ."'") AS $chapter)
	{
		foreach($f3->db->exec("SELECT id FROM ".$f3->get("prefix")."word_unit WHERE chapter = '". $chapter['id'] ."'") AS $wordUnit)
		{
			$allWordUnits[$wordUnit["id"]] = $wordUnit["id"];
		}
	}

	$level0WordUnits_diff = array_diff($allWordUnits, $allWordUnitsWithStats);
	
	$level0WordUnits = array();
	// The following stupid loop is just that Javascript can access the data easier
	foreach($level0WordUnits_diff AS $leven0WordUnit)
	{
		$level0WordUnits[] = $leven0WordUnit;
	}
	
	$level0 = array();
	$level0[0]["title"] = stats_get_level_name(0);
	$level0[0]["wordUnits"] = $level0WordUnits;
	$level0[0]["amount"] = count($level0WordUnits);
	
	$levels = array_merge($level0, $levels);
	return $levels;
}

function stats_save($wordUnitID, $up = true)
{
	global $f3;
	
	$currentStats = $f3->db->exec("SELECT *  FROM ".$f3->get("prefix")."stats WHERE word_unit = '". $wordUnitID ."' AND user = '". $f3->get("userid") ."' LIMIT 1");
	
	if(!isset($currentStats[0]["id"]))
	{
		if($up == true)
		{
			$newLevel = 2;
		}
		else
		{
			$newLevel = 1;
		}
		
		$f3->db->exec("INSERT INTO ".$f3->get("prefix")."stats (word_unit, user, section) VALUES ('". $wordUnitID ."', '". $f3->get("userid") ."', '". $newLevel ."')");
	}
	else
	{
		if($up == true)
		{
			$newLevel = $currentStats[0]["section"]+1;
		}
		else
		{
			$newLevel =  $currentStats[0]["section"]-1;
		}
		
		if($newLevel < 1)
		{
			$newLevel = 1;
		}
		
		if($newLevel > 4)
		{
			$newLevel = 4;
		}
		
		$f3->db->exec("UPDATE ".$f3->get("prefix")."stats SET section = '". $newLevel ."' WHERE word_unit = '". $wordUnitID ."' AND user = '". $f3->get("userid") ."' LIMIT 1");
	}
}
?>
