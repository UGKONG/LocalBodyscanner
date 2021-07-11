<?php
/**
 * Session Wrapper Class
 */
require_once 'session.class.php';

/**
 * 통합관리자 권한을 가지고 있는지 조사하고 없으면 경고메시지와 함께 이전 화면으로 복귀
 */
function validateAdmin($session, $grade) {
	if (!isset($session->user)) {
		header('Location: login.php?msg=loginNotFound');
		exit;
	}
	
	error_log("validateAdmin : USERID = ".$session->user['USERID']."  , GRADE = ".$session->user['GRADE']); //,3,"C:/xampp/debuggingLog/debug.log");

	if ($session->user['GRADE'] < $grade) {
		header('Location: login.php?msg=NoPower');
		exit;
	}
}
									

function getProgressSelect($database, $array, $param_name, $default_value) {
	$value = 0;
	$statusstring = '';
	if ($array)
		$value = isset($array[$param_name]) ? $array[$param_name] : $default_value;
	else
		$value = 0;
	$database->prepare("SELECT gcd_id, code_name,value FROM tb_general_code WHERE parent=1 order by code asc");
	$database->execute();
	$rows = $database->fetchAll();
	foreach ($rows as $row)
	{
		//error_log("value=".$value." row[gcd_id]=".$row["gcd_id"]);
		if ($row["value"] == $value)
		{
			$currentstatus = "<option value=\"".$row['value']."\" selected>".$row['code_name']."</option>\r\n";;
		} else {
			$currentstatus = "<option value=\"".$row['value']."\" >".$row['code_name']."</option>\r\n";;
		}

		$statusstring .= $currentstatus;
	}
	return "<select class=\"form-control selbox\" id=\"statuschange\" name=\"statuschange\">$statusstring</select>";
}
function getProgressStatus($database, $array, $param_name, $default_value) {
	$value = 0;
	$statusstring = '';
	if ($array)
		$value = isset($array[$param_name]) ? $array[$param_name] : $default_value;
	else
		$value = 0;
	$database->prepare("SELECT gcd_id, code, code_name, value FROM tb_general_code WHERE group_code='CD001' order by code asc");
	$database->execute();
	$rows = $database->fetchAll();
	foreach ($rows as $row)
	{
		error_log("value=".$value." row[code]=".$row["code"]);
		if ($row["value"] == $value || ($row["code"]=="CD00101" && $value==0))
		{
			$currentstatus = "<font style=\"font-weight: bold;color: #4D8DAD;\">[".$row["code_name"]."]</font>";
		} else {
			$currentstatus = $row["code_name"];
		}

		if ($statusstring == '')
		{
			$statusstring = $currentstatus;
		} else {
			$statusstring .= " - ".$currentstatus;
		}
	}
	return $statusstring;
}

function getAnyParameter($param_name, $default_value) {
	return isset($_GET[$param_name]) ? $_GET[$param_name] :  (isset($_POST[$param_name]) ? $_POST[$param_name] : $default_value);
}

function getGetParameter($param_name, $default_value) {
	return isset($_GET[$param_name]) ? $_GET[$param_name] :  $default_value;
}

function getPostParameter($param_name, $default_value) {
	return isset($_POST[$param_name]) ? $_POST[$param_name] :  $default_value;
}

function getFromArray($array, $param_name, $default_value) {
	if ($array)
		return isset($array[$param_name]) ? $array[$param_name] : $default_value;
	else
		return $default_value;
}


?>