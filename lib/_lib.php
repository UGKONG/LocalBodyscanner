<?php
function get_personal_unitprice($UV_SQ,$database)
{
	// 매출 단가 계산 : tb_user_voucher
	// use type : 1 period, 2 : count
	$database->prepare("
		select SELLINGPRICE, USE_TYPE, PERIOD, PERIOD_UNIT, COUNT  
		from tb_user_voucher 
		WHERE UV_SQ=:UV_SQ
	");

	$database->bind(':UV_SQ', $UV_SQ);
	$database->execute();
	$row = $database->fetch();
	
	$SELLINGPRICE = $row['SELLINGPRICE'];
	$USE_TYPE = $row['USE_TYPE'];
	$PERIOD = $row['PERIOD'];
	$PERIOD_UNIT = $row['PERIOD_UNIT'];
	$COUNT = $row['COUNT'];
	
	$UNIT_PRICE = 0;
	// 1 일 경우 SELLINGPRICE / 총 기간 : 일 단가 PERIOD_UNIT : 1-, 2-  -> 실제론 없지만 일단 구현.
	if ($USE_TYPE == 1) {
		if ($PERIOD_UNIT==2) {
			$PERIOD = $PERIOD * 30; // 월단위 일경우 30일 기준.
		}
		$UNIT_PRICE = round($SELLINGPRICE / $PERIOD);
	} else {
	// 2 일 경우 SELLINGPRICE / 총횟수 : 회당 단가. 
		$UNIT_PRICE = round($SELLINGPRICE / $COUNT);
	}
	return $UNIT_PRICE;
}

function get_personal_allowance($RESERV_SQ, $UV_SQ, $ATTENDANCE,$database)
{
	// 수당 계산 : tb_user_voucher
	// use type : 1 period, 2 : count
	$database->prepare("
		select PERSONAL_ALLOWANCE_TYPE,PERSONAL_ALLOWANCE_AMOUNT,PERSONAL_ALLOWANCE_RATIO,PERSONAL_ALLOWANCE_TAX_EXCEPT,PERSONAL_NOSHOW_TYPE,PERSONAL_NOSHOW_RATIO
		FROM tb_manager_sub a
		INNER JOIN tb_reservation b on a.USER_SQ = b.MANAGER_SQ 
			WHERE RESERV_SQ=:RESERV_SQ
	");

	$database->bind(':RESERV_SQ', $RESERV_SQ);
	$database->execute();
	$row = $database->fetch();
	
	$PERSONAL_ALLOWANCE_TYPE = $row['PERSONAL_ALLOWANCE_TYPE'];
	$PERSONAL_ALLOWANCE_AMOUNT = $row['PERSONAL_ALLOWANCE_AMOUNT'];
	$PERSONAL_ALLOWANCE_RATIO = $row['PERSONAL_ALLOWANCE_RATIO'];
	$PERSONAL_ALLOWANCE_TAX_EXCEPT = $row['PERSONAL_ALLOWANCE_TAX_EXCEPT'];
	$PERSONAL_NOSHOW_TYPE = $row['PERSONAL_NOSHOW_TYPE'];
	$PERSONAL_NOSHOW_RATIO = $row['PERSONAL_NOSHOW_RATIO'];
	
	$ALLOWANCE = 0;
	// 1 일 경우 정액 계산.
	if ($PERSONAL_ALLOWANCE_TYPE == 1) {
		$ALLOWANCE = $PERSONAL_ALLOWANCE_AMOUNT;
	} else {
	// 2 일 경우 이용권 단가로부텨 계산.
		$UNIT_PRICE = get_personal_unitprice($UV_SQ,$database);
		$ALLOWANCE = round($UNIT_PRICE * $PERSONAL_ALLOWANCE_RATIO / 100);
	}
	
	// 결석시 비율차감 이고, 출석상태가 결석이면...
	if ($PERSONAL_NOSHOW_TYPE == 2 && $ATTENDANCE==3) {
		$ALLOWANCE = round($ALLOWANCE * $PERSONAL_NOSHOW_RATIO / 100);
	}
	
	// 부가세 제외후 정산일 경우...
	if ($PERSONAL_ALLOWANCE_TAX_EXCEPT==1) {
		$ALLOWANCE = round($ALLOWANCE / 11) * 10; //10원 단위로 절사.
	}
	
	return $ALLOWANCE;
}

function get_group_unitprice($CLASS_SQ,$database)
{
	$database->prepare("
		select ALLOWANCE  
		from tb_class_schedule 
		WHERE CLASS_SQ=:CLASS_SQ
	");

	$database->bind(':CLASS_SQ', $CLASS_SQ);
	$database->execute();
	$row = $database->fetch();
	
	$ALLOWANCE = $row['ALLOWANCE'];
	return $ALLOWANCE;
}
	
	// 수당 계산. tb_manager_sub
	// PERSONAL_ALLOWANCE_TYPE : 1 : 금액. 2: 비율 - 회당단가에 대한 비율. 
	// PERSONAL_ALLOWANCE_TAX_EXCEPT <- 총금액에서 부가세를 계산. 1일 경우 부가세 제외해야함.
	
	// 결석일 경우. $ATTEND=4 -> PERSONAL_NOSHOW_TYPE=2 이면 PERSONAL_NOSHOW_RATIO 비율만큼 곱함.

function get_group_allowance($CLASS_SQ, $ATTENDANCE,$database)
{
	// 수당 계산 : tb_user_voucher
	// use type : 1 period, 2 : count
	$database->prepare("
		select GROUP_ALLOWANCE_TAX_EXCEPT,GROUP_NOSHOW_TYPE,GROUP_NOSHOW_RATIO
		FROM tb_manager_sub a
		INNER JOIN tb_class_schedule b on a.USER_SQ = b.MANAGER_SQ 
		WHERE b.CLASS_SQ=:CLASS_SQ
	");

	$database->bind(':CLASS_SQ', $CLASS_SQ);
	$database->execute();
	$row = $database->fetch();
	
	$GROUP_ALLOWANCE_TAX_EXCEPT = $row['GROUP_ALLOWANCE_TAX_EXCEPT'];
	$GROUP_NOSHOW_TYPE = $row['GROUP_NOSHOW_TYPE'];
	$GROUP_NOSHOW_RATIO = $row['GROUP_NOSHOW_RATIO'];
	
	$UNITPRICE = get_group_unitprice($CLASS_SQ,$database);
	$ALLOWANCE = 0;
	
	// 출석, 결석 수를 계산.
	$database->prepare("
		select sum(CASE WHEN RESERV_STATUS=3 THEN 1 ELSE 0 END) ATTEND_COUNT, 
				sum(CASE WHEN RESERV_STATUS=4 THEN 1 ELSE 0 END) ABSENCE_COUNT
			from tb_class_reservation
			WHERE CLASS_SQ=:CLASS_SQ
	");
	$database->bind(':CLASS_SQ', $CLASS_SQ);
	$database->execute();
	$row = $database->fetch();
	
	$ATTEND_COUNT = $row['ATTEND_COUNT'];
	$ABSENCE_COUNT = $row['ABSENCE_COUNT'];

	// 결석시 비율차감 이면...
	if ($GROUP_NOSHOW_TYPE == 2) {
		$ALLOWANCE = round($ALLOWANCE * ($ATTEND_COUNT + ($ABSENCE_COUNT * $PERSONAL_NOSHOW_RATIO / 100)));
	} else {
		
		$ALLOWANCE = round($ALLOWANCE * ($ATTEND_COUNT + $ABSENCE_COUNT));
	}
	
	// 부가세 제외후 정산일 경우...
	if ($GROUP_ALLOWANCE_TAX_EXCEPT==1) {
		$ALLOWANCE = round($ALLOWANCE / 11) * 10; //10원 단위로 절사.
	}
	
	return $ALLOWANCE;
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