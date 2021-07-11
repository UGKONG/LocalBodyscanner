<?
function get_personal_allowance($MANAGER_SQ, $UV_SQ, $RESERV_SQ, $ATTEND)
{
	$database->prepare("
		select VOUCHER_NAME from tb_user_voucher 
			WHERE UV_SQ=:UV_SQ
	");

	$database->bind(':UV_SQ', $UV_SQ);
	$database->execute();
	if ($database->rowCount() > 0) {
		$row = $database->fetch();
		$RETURN_STR = '<span class="voucher">'.$row['VOUCHER_NAME'].'</span>';
	}
}

function insert_Log_History($CENTER_SQ,$USER_SQ,$MEMBER_SQ, $DEVICE_SQ, $IP, $GROUP, $CATEGORY, $ACTION,$database)
{
	$database->prepare("
				insert into tb_history ( CENTER_SQ, USER_SQ, MEMBER_SQ, DEVICE_SQ, IP, `GROUP`, CATEGORY, `ACTION`, REG_DT ) values								
				( :CENTER_SQ, :USER_SQ, :MEMBER_SQ, :DEVICE_SQ, :IP, :GROUP, :CATEGORY, :ACTION,  now()  )
	");
	$database->bind(':CENTER_SQ', $CENTER_SQ);
	$database->bind(':USER_SQ', $USER_SQ);
	$database->bind(':MEMBER_SQ', $MEMBER_SQ);
	$database->bind(':DEVICE_SQ', $DEVICE_SQ);
	$database->bind(':IP', $IP);
	$database->bind(':GROUP', $GROUP);
	$database->bind(':CATEGORY', $CATEGORY);
	$database->bind(':ACTION', $ACTION);
	$database->execute();
	
	
		error_log('$ACTION = ' . $ACTION);
}

function Get_UserInfo($USER_SQ,$database)
{
	$RETURN_STR = "";
	$database->prepare("
		select USER_NM, PHONE_NO, CASE WHEN BIRTH_DT is null THEN '-' ELSE CONCAT(CAST(DATEDIFF(now(),BIRTH_DT) / 365 as signed integer), '세') END as AGE 
		from tb_user WHERE USER_SQ=:USER_SQ
	");

	$database->bind(':USER_SQ', $USER_SQ);
	$database->execute();
	if ($database->rowCount() > 0) {
		$row = $database->fetch();
		$RETURN_STR = '<span class="member">'.$row['USER_NM'].'('.$row['PHONE_NO'].','.$row['AGE'].')</span>';
	}
	return $RETURN_STR;
}

function Get_TrainerInfo($USER_SQ,$database)
{
	$RETURN_STR = "";
	$database->prepare("
		select USER_NM from tb_user 
			WHERE USER_SQ=:USER_SQ
	");

	$database->bind(':USER_SQ', $USER_SQ);
	$database->execute();
	if ($database->rowCount() > 0) {
		$row = $database->fetch();
		$RETURN_STR = '<span class="trainer">'.$row['USER_NM'].'</span>';
	}
	return $RETURN_STR;
}

function Get_VoucherInfo($UV_SQ,$database)
{
	$RETURN_STR = "";
	$database->prepare("
		select VOUCHER_NAME from tb_user_voucher 
			WHERE UV_SQ=:UV_SQ
	");

	$database->bind(':UV_SQ', $UV_SQ);
	$database->execute();
	if ($database->rowCount() > 0) {
		$row = $database->fetch();
		$RETURN_STR = '<span class="voucher">'.$row['VOUCHER_NAME'].'</span>';
	}
	return $RETURN_STR;
}

function Get_ReservInfo($RESERV_SQ,$database)
{
	$RETURN_STR = "";
	$database->prepare("
		select DATE_FORMAT(RESERV_DT, '%Y/%m/%d') as RESERV_DT, START_TIME from tb_reservation
			WHERE RESERV_SQ=:RESERV_SQ
	");

	$database->bind(':RESERV_SQ', $RESERV_SQ);
	$database->execute();
	if ($database->rowCount() > 0) {
		$row = $database->fetch();
		$RETURN_STR = '<span class="class">( 시간:'.$row['RESERV_DT'].' '.$row['START_TIME'].' )</span>';
	}
	return $RETURN_STR;
}

function Get_ClassInfo($CLASS_SQ,$database)
{
	$RETURN_STR = "";
	$database->prepare("
		select a.CLASS_NAME, b.ROOM_NAME, DATE_FORMAT(a.CLASS_DT, '%Y/%m/%d') as CLASS_DT, a.START_TIME from tb_class_schedule a
			inner join tb_room b on a.ROOM_SQ=b.ROOM_SQ
			WHERE CLASS_SQ=:CLASS_SQ
	");

	$database->bind(':CLASS_SQ', $CLASS_SQ);
	$database->execute();
	if ($database->rowCount() > 0) {
		$row = $database->fetch();
		$RETURN_STR = '<span class="class">'.$row['CLASS_NAME'].'( 룸:'.$row['ROOM_NAME'].', 시간:'.$row['CLASS_DT'].' '.$row['START_TIME'].' )</span>';
	}
	return $RETURN_STR;
}

function Get_SingleField($TableName, $FieldName, $SeqFieldName, $Seq, $Where, $database)
{
	$RETURN_STR = "";
	$database->prepare("
		select ".$FieldName." as SingleField from ".$TableName."
			WHERE ".$SeqFieldName."=:Seq ".$Where."
	");
	$database->bind(':Seq', $Seq);
	$database->execute();
	if ($database->rowCount() > 0) {
		$row = $database->fetch();
		$RETURN_STR = $row['SingleField'];
	}
	return $RETURN_STR;
}

function insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database)
{
	$database->prepare("
		insert into tb_user_history ( IP, USER_SQ, USERID, DEVICE_SQ, CATEGORY, SUBCATEGORY, ACTION, REG_DT ) values
								( :IP, :USER_SQ, :USERID, :DEVICE_SQ, :CATEGORY, :SUBCATEGORY, :ACTION,  now()  )
	");
	$database->bind(':IP', $IP);
	$database->bind(':USER_SQ', $USER_SQ);
	$database->bind(':USERID', $USERID);
	$database->bind(':DEVICE_SQ', $DEVICE_SQ);
	$database->bind(':CATEGORY', $CATEGORY);
	$database->bind(':SUBCATEGORY', $SUBCATEGORY);
	$database->bind(':ACTION', $ACTION);
	$database->execute();
	//error_log("IP : " . $IP);
	//error_log("USER_SQ : " . $USER_SQ);
	//error_log("USERID : " . $USERID);
	//error_log("DEVICE_SQ : " . $DEVICE_SQ);
	//error_log("ACTION : " . $ACTION);
	//error_log("rowCount2 : " . $database->rowCount());
}

function insert_Log_Person($USER_SQ,$USERID, $IP, $DEVICE_SQ, $ACTION,$database)
{
	$database->prepare("
		insert into tb_user_history ( IP, USER_SQ, USERID, DEVICE_SQ, ACTION, REG_DT ) values
								( :IP, :USER_SQ, :USERID, :DEVICE_SQ, :ACTION,  now()  )
	");
	$database->bind(':IP', $IP);
	$database->bind(':USER_SQ', $USER_SQ);
	$database->bind(':USERID', $USERID);
	$database->bind(':DEVICE_SQ', $DEVICE_SQ);
	$database->bind(':ACTION', $ACTION);
	$database->execute();
	//error_log("IP : " . $IP);
	//error_log("USER_SQ : " . $USER_SQ);
	//error_log("USERID : " . $USERID);
	//error_log("DEVICE_SQ : " . $DEVICE_SQ);
	//error_log("ACTION : " . $ACTION);
	//error_log("rowCount2 : " . $database->rowCount());
}


function getClientIPv4()
{
	foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key)
	{
		if (array_key_exists($key, $_SERVER) === true)
		{
			foreach (explode(',', $_SERVER[$key]) as $ip)
			{
				$ip = trim($ip);
				if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE) !== false)
				{
					return $ip;
				}
			}
		}
	}
}

function getExcelFontArray($size, $bold, $underline, $color)
{
	$Font = array ('font' => array(
									   'bold' => 'true',
									   'underline' => $underline,
									   'size' => $size,
									   'color' => array('rgb'=>$color)
 									)
           			);
	return $Font;
}

function getExcelFillArray($color)
{
	$Font = array ('fill' => array(
						   'type' => PHPExcel_Style_Fill::FILL_SOLID,
						   'color' => array('rgb'=>$color),
						   )
           			);
	return $Font;
}

function getExcelBorderArray($color)
{
	$Border = array ('borders' => array(
 
               		'outline' => array(
                   'style' => PHPExcel_Style_Border::BORDER_THICK,
                   'color' => array('rgb'=>$color)
                   )
               )
           			);
	return $Border;
}

function Hash_Sha256($s_passwd){
	$hash = hash('sha256', $s_passwd);
	return $hash;
}


function Hash_Sha256SALT($salt, $s_passwd){
	$hash = hash('sha256', $s_passwd);
	$hash2 = hash('sha256', $salt . $hash);
	error_log('salt = ' . $salt);
	error_log('s_passwd = ' . $s_passwd);
	error_log('hash = ' . $hash);
	error_log('hash2 = ' . $hash2);
	return $hash2;
}

function getAuthority($USER_SQ,$database)
{
	$database->prepare("
		select AUTH_SQ,USER_SQ,AUTH_CD,b.DESCRIPTION as AUTH_NAME, CREATEDBY,CREATEDDT
				from tb_authority a
				inner join tb_common b on a.AUTH_CD=b.CODE and BASE_CD='CD016' and CODE>0 
				where USER_SQ=:USER_SQ
				order by CODE asc
	");

	$database->bind(':USER_SQ', $USER_SQ);
	$database->execute();

	$rows = $database->fetchAll();
	return json_encode($rows);
}

function getCommonCode($basecode,$database)
{
	$database->prepare("
		select COMMON_SQ,BASE_CD,CODE,NAME,DESCRIPTION
				from tb_common where BASE_CD=:BASECODE and CODE>0 order by CODE asc
	");

	$database->bind(':BASECODE', $basecode);
	$database->execute();

	$rows = $database->fetchAll();
	return json_encode($rows);
}

function checkNEWUSER($USER_NM,$USER_ID, $database)
{
	$database->prepare("
		select USER_NM,USERID
				from tb_user where USERID=:USERID
	");
	$database->bind(':USERID', $USER_ID);
	$database->execute();
	
	if ($database->rowCount() == 0) {
		return 0;
	}
	
	$row = $database->fetch();
	if ($row["USER_NM"]==$USER_NM)
	{
		return 2;
	}
	return 1;
}
?>