<?php
/**
 * 회원관리 Business Logics
 */

require_once 'lib/_init.php';
require_once 'lib/_lib.php';
$database = new Database();
$session = new Session();
include_once 'lib/sendmail.class.php';
$mailer = new Mailer();

/**
 * 임시 비밀번호용 랜덤 문자열 생성
 *
 * @param	length	문자열 생성 길이
 * @return	string
 */
function getRandomPassword($length = 10) {
	$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}

$task = getAnyParameter("task",0);
error_log(var_export($_GET, 1));
error_log(var_export($_POST, 1));
error_log($task);

switch ($task) {
							
	case 'getDashboardData':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$CURRENT_DT = getAnyParameter("CURRENT_DT","");
		$START_DT = getAnyParameter("START_DT","");
		$END_DT = getAnyParameter("END_DT","");
		
		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$DEVICE_SQ = -1;
		$GROUP = 7;
		$CATEGORY = 71;
		$ACTION = "사용자 대쉬보드 정보가 조회되었습니다.";
		$IP = getClientIPv4();
		
		// 출석율 정보 
		$database->prepare("
			select name, reserv + attend + absence as total, attend as attend 
			from ( select name, case when reserv is null then 0 else reserv end reserv,
					case when reservcancel is null then 0 else reservcancel end reservcancel,
					case when attend is null then 0 else attend end attend,
					case when absence is null then 0 else absence end absence
					from (
							select 'personal' as name, sum(case when RESERV_STATUS=1 then 1 else 0 end) as reserv,
									sum(case when RESERV_STATUS=2 then 1 else 0 end) as reservcancel,
									sum(case when RESERV_STATUS=3 then 1 else 0 end) as attend,
									sum(case when RESERV_STATUS=4 then 1 else 0 end) as absence
							from tb_reservation
							where RESERV_DT=:RESERV_DT
							UNION
							select 'group' as name,  sum(case when RESERV_STATUS=1 then 1 else 0 end) as reserv,
									sum(case when RESERV_STATUS=2 then 1 else 0 end) as reservcancel,
									sum(case when RESERV_STATUS=3 then 1 else 0 end) as attend,
									sum(case when RESERV_STATUS=4 then 1 else 0 end) as absence
							from tb_class_reservation a
								inner join tb_class_schedule b on a.CLASS_SQ=b.CLASS_SQ 
							where b.CLASS_DT=:CLASS_DT
					) c
			) d
		");
		$database->bind(':RESERV_DT', $CURRENT_DT);
		$database->bind(':CLASS_DT', $CURRENT_DT);
		$database->execute();

		$rows = $database->fetchAll();
		$attendinfo = json_encode($rows);
		
		//	금월 판매량	
		$database->prepare("
			select c.CATEGORY_SQ, c.CATEGORY_NAME, SUM(a.SELLINGPRICE)
			from tb_payment a 
				inner join tb_voucher b on a.VOUCHER_SQ=b.VOUCHER_SQ
				inner join tb_category c on b.CATEGORY_SQ = c.CATEGORY_SQ
			where a.CREATEDDT>=:START_DT and  a.CREATEDDT<=:END_DT and a.SELLINGPRICE>0
			group by c.CATEGORY_SQ, c.CATEGORY_NAME
		");
		$database->bind(':START_DT', $START_DT);
		$database->bind(':END_DT', $END_DT);
		$database->execute();

		$rows = $database->fetchAll();
		$thismonthsales = json_encode($rows);
		
		//	금월 가입자	
		$database->prepare("
			select count(*) from tb_user
			where REG_DT>=:START_DT and  REG_DT<=:END_DT
		");
		$database->bind(':START_DT', $START_DT);
		$database->bind(':END_DT', $END_DT);
		$database->execute();

		$rows = $database->fetchAll();
		$thismonthuser = json_encode($rows);

		//	금일 트레이너 
		$database->prepare("
			select a.MANAGER_SQ, b.USER_NM
			from ( 	select MANAGER_SQ from tb_reservation where RESERV_DT=:RESERV_DT
					UNION
					select MANAGER_SQ from tb_class_schedule where CLASS_DT=:CLASS_DT  ) a
			inner join tb_user b on a.MANAGER_SQ = b.USER_SQ
		");
		$database->bind(':RESERV_DT', $CURRENT_DT);
		$database->bind(':CLASS_DT', $CURRENT_DT);
		$database->execute();

		$rows = $database->fetchAll();
		$todaytrainer = json_encode($rows);

		// 로그 저장
		insert_Log_History($CENTER_SQ,$USER_SQ,0, $DEVICE_SQ, $IP, $GROUP, $CATEGORY, $ACTION,$database);

		exit($attendinfo.'|'.'[]'.'|'.$thismonthsales.'|'.$thismonthuser.'|'.$todaytrainer);
		break;
							
	case 'getPauseInfo':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
	
		$UV_SQ = getAnyParameter("UV_SQ",0);
		
		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$DEVICE_SQ = -1;
		$GROUP = 7;
		$CATEGORY = 71;
		$ACTION = "사용자 이용정지 정보가 조회되었습니다.";
		$IP = getClientIPv4();
		
		$database->prepare("
				select *
				from tb_user_voucher_pause
				where UV_SQ=:UV_SQ 
				ORDER BY UV_SQ desc LIMIT 1
		");
		$database->bind(':UV_SQ', $UV_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$pauseinfo = json_encode($rows);
		
		// 로그 저장
		insert_Log_History($CENTER_SQ,$USER_SQ,0, $DEVICE_SQ, $IP, $GROUP, $CATEGORY, $ACTION,$database);

		exit($pauseinfo);
		break;
								
	case 'getUserVoucherHistory':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
	
		$MEMBER_SQ = getAnyParameter("USER_SQ",0);
		
		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$DEVICE_SQ = -1;
		$GROUP = 7;
		$CATEGORY = 71;
		$ACTION = "사용자 HISTORY 리스트가 조회되었습니다.";
		$IP = getClientIPv4();
		
		$database->prepare("
				select a.UVH_SQ,a.UV_SQ,a.VOUCHER_TYPE,b.VOUCHER_NAME,b.COUNT TOTAL_COUNT,b.USEDCOUNT TOTAL_USEDCOUNT,b.USE_LASTDATE,
						CASE WHEN (a.RESERV_SQ>0) THEN g.MANAGER_SQ ELSE f.MANAGER_SQ END MANAGER_SQ,
						CASE WHEN (a.RESERV_SQ>0) THEN i.USER_NM ELSE h.USER_NM END MANAGER_NM,
						a.ATTENDANCE_TYPE, d.DESCRIPTION ATTENDANCE_NAME, a.USED_COUNT,
						a.CLASS_RESERV_SQ,a.RESERV_SQ,
						CASE WHEN (a.RESERV_SQ>0) THEN g.RESERV_DT ELSE f.CLASS_DT END USED_DT,
						CASE WHEN (a.RESERV_SQ>0) THEN g.START_TIME ELSE f.START_TIME END USED_TIME,
						CASE WHEN (a.RESERV_SQ>0) THEN b.VOUCHER_NAME ELSE f.CLASS_NAME END CLASS_NAME		
				from tb_user_voucher_history a
				inner join tb_user_voucher b on a.UV_SQ=b.UV_SQ 
				inner join tb_common d on a.ATTENDANCE_TYPE=d.CODE and d.BASE_CD='CD019'
				left outer join tb_class_reservation e on a.CLASS_RESERV_SQ=e.CLASS_RESERV_SQ
				left outer join tb_class_schedule f on e.CLASS_SQ=f.CLASS_SQ
				left outer join tb_reservation g on g.RESERV_SQ=a.RESERV_SQ
				left outer join tb_user h on f.MANAGER_SQ=h.USER_SQ
				left outer join tb_user i on g.MANAGER_SQ=i.USER_SQ
				where a.USER_SQ=:USER_SQ
		");
		$database->bind(':USER_SQ', $MEMBER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$voucherhistory = json_encode($rows);
		
		// 로그 저장
		insert_Log_History($CENTER_SQ,$USER_SQ,0, $DEVICE_SQ, $IP, $GROUP, $CATEGORY, $ACTION,$database);

		exit($voucherhistory);
		break;
		
	case 'getReservedList':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
	
		$MEMBER_SQ = getAnyParameter("USER_SQ",0);
		
		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$DEVICE_SQ = -1;
		$GROUP = 7;
		$CATEGORY = 71;
		$ACTION = "사용자 예약 리스트가 조회되었습니다.";
		$IP = getClientIPv4();
		
		$database->prepare("
			select a.RESERV_SQ, 1 as RESERV_TYPE, a.MANAGER_SQ, c.USER_NM MANAGER_NM, a.UV_SQ, a.RESERV_STATUS, a.RESERV_DT, a.START_TIME, a.END_TIME, 0 as CLASS_SQ, d.VOUCHER_NAME CLASS_NAME, 0 ROOM_SQ, '' ROOM_NAME
			from tb_reservation a
			inner join tb_user c on a.MANAGER_SQ=c.USER_SQ
			inner join tb_user_voucher d on a.UV_SQ=d.UV_SQ
			where a.USER_SQ=:USER_SQ
			union
			select a.CLASS_RESERV_SQ RESERV_SQ, 2 as RESERV_TYPE, b.MANAGER_SQ, c.USER_NM MANAGER_NM, a.UV_SQ, a.RESERV_STATUS,b.CLASS_DT RESERV_DT,b.START_TIME,b.END_TIME, b.CLASS_SQ, b.CLASS_NAME,e.ROOM_SQ, e.ROOM_NAME
			from tb_class_reservation a
			inner join tb_class_schedule b on a.CLASS_SQ=b.CLASS_SQ
			inner join tb_user c on b.MANAGER_SQ=c.USER_SQ
			inner join tb_room e on b.ROOM_SQ=e.ROOM_SQ
			where a.USER_SQ=:USER_SQ2
		");
		$database->bind(':USER_SQ', $MEMBER_SQ);
		$database->bind(':USER_SQ2', $MEMBER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$reservation_list = json_encode($rows);
		
		// 로그 저장
		insert_Log_History($CENTER_SQ,$USER_SQ,0, $DEVICE_SQ, $IP, $GROUP, $CATEGORY, $ACTION,$database);

		exit($reservation_list);
		break;
						
	case 'GetManagerPersonalSalaryList':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
	
		$MANAGER_SQ = getAnyParameter("MANAGER_SQ",0);
		$START_DT = getAnyParameter("START_DT","").' 00:00:00';
		$END_DT = getAnyParameter("END_DT","").' 23:59:59';
		
		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$DEVICE_SQ = -1;
		$GROUP = 7;
		$CATEGORY = 71;
		$ACTION = "Manager 수당 리스트가 조회되었습니다.";
		$IP = getClientIPv4();
		
		$database->prepare("
			SELECT a.USER_SQ,b.USER_NM,a.CENTER_SQ,a.UV_SQ,a.MANAGER_SQ,e.USER_NM as MANAGER_NAME,a.VOUCHER_TYPE,d.VOUCHER_NAME, 
						a.ATTENDANCE_TYPE,g.DESCRIPTION ATTENDANCE_TYPE_NAME, a.RESERV_SQ,a.ALLOWANCE,a.USED_COUNT,a.DESCRIPTION,c.RESERV_DT,c.START_TIME,c.END_TIME
			from tb_user_voucher_history a
			inner join tb_user b on a.USER_SQ=b.USER_SQ
			inner join tb_reservation c on a.RESERV_SQ = c.RESERV_SQ
			inner join tb_user_voucher d on d.UV_SQ = c.UV_SQ
			inner join tb_user e on a.MANAGER_SQ=e.USER_SQ
			inner join tb_common g on a.ATTENDANCE_TYPE=g.CODE and g.BASE_CD='CD019'
			where (a.CREATE_DT between :START_DT and :END_DT) 
			and (:MANAGER_SQ=0 or a.MANAGER_SQ=:MANAGER_SQ2) and a.VOUCHER_TYPE=1
			and a.CENTER_SQ=:CENTER_SQ and (a.USED_COUNT>0)
		");
		$database->bind(':START_DT', $START_DT);
		$database->bind(':END_DT', $END_DT);
		$database->bind(':MANAGER_SQ', $MANAGER_SQ);
		$database->bind(':MANAGER_SQ2', $MANAGER_SQ);
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$history_list = json_encode($rows);
		
		// 로그 저장
		insert_Log_History($CENTER_SQ,$USER_SQ,0, $DEVICE_SQ, $IP, $GROUP, $CATEGORY, $ACTION,$database);

		exit($history_list);
		break;
						
	case 'GetManagerGroupSalaryList':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
	
		$MANAGER_SQ = getAnyParameter("MANAGER_SQ",0);
		$START_DT = getAnyParameter("START_DT","").' 00:00:00';
		$END_DT = getAnyParameter("END_DT","").' 23:59:59';
		
		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$DEVICE_SQ = -1;
		$GROUP = 7;
		$CATEGORY = 71;
		$ACTION = "Manager 수당 리스트가 조회되었습니다.";
		$IP = getClientIPv4();
		
		$database->prepare("
			select a.CLASS_SQ,a.ROOM_SQ,a.CLASS_NAME,a.MANAGER_SQ,b.USER_NM MANAGER_NM,a.START_TIME,a.END_TIME,a.CLASS_DT,
						(select count(*) from tb_class_reservation where (RESERV_STATUS=3 or RESERV_STATUS=4) and CLASS_SQ=a.CLASS_SQ) MEMBER_COUNT,
						ALLOWANCE, MEMO
			from tb_class_schedule a
			inner join tb_user b on a.MANAGER_SQ=b.user_sq
			where (CLASS_DT between :START_DT and :END_DT) 
			and (:MANAGER_SQ=0 or a.MANAGER_SQ=:MANAGER_SQ2)
			and a.CENTER_SQ=:CENTER_SQ
		");
		$database->bind(':START_DT', $START_DT);
		$database->bind(':END_DT', $END_DT);
		$database->bind(':MANAGER_SQ', $MANAGER_SQ);
		$database->bind(':MANAGER_SQ2', $MANAGER_SQ);
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$history_list = json_encode($rows);
		
		// 로그 저장
		insert_Log_History($CENTER_SQ,$USER_SQ,0, $DEVICE_SQ, $IP, $GROUP, $CATEGORY, $ACTION,$database);

		exit($history_list);
		break;
						
	case 'GetManagerGroupSalaryListOld':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
	
		$MANAGER_SQ = getAnyParameter("MANAGER_SQ",0);
		$START_DT = getAnyParameter("START_DT","").' 00:00:00';
		$END_DT = getAnyParameter("END_DT","").' 23:59:59';
		
		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$DEVICE_SQ = -1;
		$GROUP = 7;
		$CATEGORY = 71;
		$ACTION = "Manager 수당 리스트가 조회되었습니다.";
		$IP = getClientIPv4();
		
		$database->prepare("
			SELECT a.USER_SQ,b.USER_NM,a.CENTER_SQ,a.UV_SQ,a.MANAGER_SQ,e.USER_NM as MANAGER_NAME,a.VOUCHER_TYPE,d.VOUCHER_NAME, 
						a.ATTENDANCE_TYPE,g.DESCRIPTION ATTENDANCE_TYPE_NAME,a.CLASS_RESERV_SQ,a.ALLOWANCE,a.DESCRIPTION,f.CLASS_DT,f.START_TIME,f.END_TIME
			from tb_user_voucher_history a
			inner join tb_user b on a.USER_SQ=b.USER_SQ
			inner join tb_class_reservation c on a.CLASS_RESERV_SQ = c.CLASS_RESERV_SQ
			inner join tb_class_schedule f on f.CLASS_SQ = c.CLASS_SQ
			inner join tb_user_voucher d on d.UV_SQ = c.UV_SQ
			inner join tb_user e on a.MANAGER_SQ=e.USER_SQ
			inner join tb_common g on a.ATTENDANCE_TYPE=g.CODE and g.BASE_CD='CD015'
			where (a.CREATE_DT between :START_DT and :END_DT) 
			and (:MANAGER_SQ=0 or a.MANAGER_SQ=:MANAGER_SQ2) and a.VOUCHER_TYPE=1
			and a.CENTER_SQ=:CENTER_SQ
		");
		$database->bind(':START_DT', $START_DT);
		$database->bind(':END_DT', $END_DT);
		$database->bind(':MANAGER_SQ', $MANAGER_SQ);
		$database->bind(':MANAGER_SQ2', $MANAGER_SQ);
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$history_list = json_encode($rows);
		
		// 로그 저장
		insert_Log_History($CENTER_SQ,$USER_SQ,0, $DEVICE_SQ, $IP, $GROUP, $CATEGORY, $ACTION,$database);

		exit($history_list);
		break;
						
	case 'GetUserHistoryList':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
	
		$MEMBER_SQ = getAnyParameter("MEMBER_SQ",0);
		$START_DT = getAnyParameter("START_DT","").' 00:00:00';
		$END_DT = getAnyParameter("END_DT","").' 23:59:59';
		
		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$DEVICE_SQ = -1;
		$GROUP = 7;
		$CATEGORY = 71;
		$ACTION = "History가 조회되었습니다.";
		$IP = getClientIPv4();
		
		$database->prepare("
			SELECT a.HIST_SQ,a.CENTER_SQ,a.USER_SQ, CASE WHEN a.USER_SQ is null THEN '시스템' ELSE b.USER_NM END as USER_NM
					,a.DEVICE_SQ,a.IP,a.`ACTION`,a.`GROUP`, c.DESCRIPTION as GROUP_NAME, a.CATEGORY, d.DESCRIPTION as CATEGORY_NAME,a.REG_DT 
			from tb_history a
			inner join tb_user b on a.USER_SQ=b.USER_SQ
			inner join tb_common c on c.CODE=a.`GROUP` and c.BASE_CD='CD017'
			inner join tb_common d on d.CODE=a.CATEGORY and d.BASE_CD='CD018'
			WHERE a.REG_DT>=:START_DT and  a.REG_DT<=:END_DT and a.MEMBER_SQ=:MEMBER_SQ
		");
		$database->bind(':START_DT', $START_DT);
		$database->bind(':END_DT', $END_DT);
		$database->bind(':MEMBER_SQ', $MEMBER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$history_list = json_encode($rows);
		
		// 로그 저장
		insert_Log_History($CENTER_SQ,$USER_SQ,0, $DEVICE_SQ, $IP, $GROUP, $CATEGORY, $ACTION,$database);

		$GroupCode = getCommonCode('CD017', $database);
		$CategoryCode = getCommonCode('CD018', $database);

		exit($history_list."|".$GroupCode."|".$CategoryCode);
		break;
				
	case 'GetHistoryList':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
	
		$START_DT = getAnyParameter("START_DT","").' 00:00:00';
		$END_DT = getAnyParameter("END_DT","").' 23:59:59';
		
		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$DEVICE_SQ = -1;
		$GROUP = 7;
		$CATEGORY = 71;
		$ACTION = "History가 조회되었습니다.";
		$IP = getClientIPv4();
		
		$database->prepare("
			SELECT a.HIST_SQ,a.CENTER_SQ,a.USER_SQ, CASE WHEN a.USER_SQ is null THEN '시스템' ELSE b.USER_NM END as USER_NM
					,a.DEVICE_SQ,a.IP,a.`ACTION`,a.`GROUP`, c.DESCRIPTION as GROUP_NAME, a.CATEGORY, d.DESCRIPTION as CATEGORY_NAME,a.REG_DT from tb_history a
			inner join tb_user b on a.USER_SQ=b.USER_SQ
			inner join tb_common c on c.CODE=a.`GROUP` and c.BASE_CD='CD017'
			inner join tb_common d on d.CODE=a.CATEGORY and d.BASE_CD='CD018'
			WHERE a.REG_DT>=:START_DT and  a.REG_DT<=:END_DT
		");
		$database->bind(':START_DT', $START_DT);
		$database->bind(':END_DT', $END_DT);
		$database->execute();

		$rows = $database->fetchAll();
		$history_list = json_encode($rows);
		
		// 로그 저장
		insert_Log_History($CENTER_SQ,$USER_SQ,0, $DEVICE_SQ, $IP, $GROUP, $CATEGORY, $ACTION,$database);

		$GroupCode = getCommonCode('CD017', $database);
		$CategoryCode = getCommonCode('CD018', $database);

		exit($history_list."|".$GroupCode."|".$CategoryCode);
		break;
				
	case 'execAuthorityChange':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$DELETE_AUTH = getAnyParameter("DELETE_AUTH","");
		$DELETE_AUTH_LIST = explode(',',$DELETE_AUTH);
		$ADD_AUTH = getAnyParameter("ADD_AUTH","");
		$ADD_AUTH_LIST = explode(',',$ADD_AUTH);
		$MEMBER_SQ = getAnyParameter("USER_SQ","");
		
		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$GROUP = 5;
		$CATEGORY = 51;
		$ACTION = Get_UserInfo($MEMBER_SQ, $database) . "회원님의 권한 정보가 추가되었습니다.";
		$IP = getClientIPv4();
		
		// 추가 
		for ($i=0;$i<count($ADD_AUTH_LIST);$i++)
		{
			$database->prepare("insert into tb_authority (USER_SQ,AUTH_CD,CREATEDBY,CREATEDDT )
								values (:MEMBER_SQ, :AUTH_CD, :USER_SQ, NOW())
							");
			$database->bind(':MEMBER_SQ', $MEMBER_SQ);
			$database->bind(':AUTH_CD', $ADD_AUTH_LIST[$i]);
			$database->bind(':USER_SQ', $USER_SQ);
			$database->execute();
		}
		
		// 삭제 
		for ($i=0;$i<count($DELETE_AUTH_LIST);$i++)
		{
			$database->prepare("delete from tb_authority 
									where USER_SQ=:MEMBER_SQ and AUTH_CD=:AUTH_CD
							");
			$database->bind(':MEMBER_SQ', $MEMBER_SQ);
			$database->bind(':AUTH_CD', $DELETE_AUTH_LIST[$i]);
			$database->execute();
		}
				
		$userauth = getAuthority($MEMBER_SQ, $database);

		// 로그 저장
		insert_Log_History($CENTER_SQ,$USER_SQ,$MEMBER_SQ, $DEVICE_SQ, $IP, $GROUP, $CATEGORY, $ACTION,$database);

		exit($userauth);
		break;
		
	case 'getUserAuthority':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$MEMBER_SQ = getAnyParameter("USER_SQ","");
		
		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "휴일 정보";
		$SUBCATEGORY = "휴일 정보 추가";
		$ACTION = $CENTER_SQ . " 휴일 정보가 추가되었습니다.";
		$IP = getClientIPv4();
				
		$userauth = getAuthority($MEMBER_SQ, $database);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($userauth);
		break;
	

	case 'execAuthorityDelete':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$CLASS_SQ = getAnyParameter("CLASS_SQ","");
		
		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "예약 설정 정보";
		$SUBCATEGORY = "예약 설정 정보 저장";
		$ACTION = $CENTER_SQ . " 센터의 예약설정 정보를 저장하였습니다.";
		$IP = getClientIPv4();
		
		$database->prepare("
			delete from tb_class_schedule 
			where CLASS_SQ=:CLASS_SQ
		");
		$database->bind(':CLASS_SQ', $CLASS_SQ);
		$database->execute();
		
		// 저장이 실패하면  종료 FAIL을 리턴 
		$response_array["result"] = 'Success';
		if ($database->rowCount() < 1 ) { 
			$rows = $database->fetchAll();
			$response_array["result"] = 'Fail';
			$reservInfo = json_encode($response_array);
			exit($reservInfo);
		}

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($response_array);
		break;				
				
	case 'execUserGroupScheduleAbsence':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$CLASS_SQ = getAnyParameter("CLASS_SQ","");
		$CLASS_RESERV_SQ = getAnyParameter("CLASS_RESERV_SQ","");
		$UV_SQ = getAnyParameter("UV_SQ","");
		
		// 기본값 설정		
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$GROUP = 4;
		$CATEGORY = 44;
		$MEMBER_SQ = Get_SingleField('tb_class_reservation', 'USER_SQ', 'CLASS_RESERV_SQ', $CLASS_RESERV_SQ, '',$database);
		$CLASS_DESC = Get_ClassInfo($CLASS_SQ,$database);
		$ACTION = Get_UserInfo($MEMBER_SQ,$database)."회원님이 ".$CLASS_DESC." 수업에 결석하셨습니다.";
		$IP = getClientIPv4();
		
		// 예약 또는 대기 상태를 취소.
		$database->prepare("
			UPDATE tb_class_reservation SET RESERV_STATUS=4 WHERE CLASS_RESERV_SQ=:CLASS_RESERV_SQ
		");
		$database->bind(':CLASS_RESERV_SQ', $CLASS_RESERV_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		
		$USED_COUNT = 0;
		if ($UV_SQ>0) {
			$USED_COUNT = 1;
			$database->prepare("
				UPDATE tb_user_voucher SET USEDCOUNT=USEDCOUNT+:USED_COUNT WHERE UV_SQ=:UV_SQ
			");
			$database->bind(':USED_COUNT', $USED_COUNT);
			$database->bind(':UV_SQ', $UV_SQ);
			$database->execute();

			$response_array["result"] = 'Success';

			if ($database->rowCount() < 1) { 
				$response_array["result"] = 'Fail';
				exit(json_encode($response_array));
			}
		} else {
			$UV_SQ = Get_SingleField('tb_class_reservation', 'UV_SQ', 'CLASS_RESERV_SQ', $CLASS_RESERV_SQ, '',$database);
		}
		
		$database->prepare("
			SELECT a.*, b.RESERV_LIST, c.WAITING_LIST, b.RESERV_COUNT, c.WAIT_COUNT
			FROM tb_class_schedule a
			left outer  join (SELECT CLASS_SQ, GROUP_CONCAT(DISTINCT USER_SQ SEPARATOR '.') RESERV_LIST, sum(CASE WHEN RESERV_STATUS=1 THEN 1 ELSE 0 END) as RESERV_COUNT
						FROM tb_class_reservation where CLASS_SQ=:CLASS_SQ and RESERV_STATUS=1
						GROUP BY CLASS_SQ) b 
			on a.CLASS_SQ=b.CLASS_SQ
			left outer  join (SELECT CLASS_SQ, GROUP_CONCAT(DISTINCT USER_SQ SEPARATOR '.') WAITING_LIST, sum(CASE WHEN RESERV_STATUS=5 THEN 1 ELSE 0 END) as WAIT_COUNT
						FROM tb_class_reservation where CLASS_SQ=:CLASS_SQ2 and RESERV_STATUS=5
						GROUP BY CLASS_SQ) c 
			on a.CLASS_SQ=c.CLASS_SQ
			where a.CLASS_SQ=:CLASS_SQ3
		");
		$database->bind(':CLASS_SQ', $CLASS_SQ);
		$database->bind(':CLASS_SQ2', $CLASS_SQ);
		$database->bind(':CLASS_SQ3', $CLASS_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$class_info = json_encode($rows);
		
		// 출결 히스토리 테이블 
		$ATTENDANCE = 3; //결석 
		$ALLOWANCE = 0;
		if ($USED_COUNT!=0)
		{
			$ALLOWANCE = get_group_allowance($CLASS_SQ, $ATTENDANCE,$database);
		}
		
		$MANAGER_SQ = Get_SingleField('tb_class_schedule', 'MANAGER_SQ', 'CLASS_SQ', $CLASS_SQ, '',$database);
		$database->prepare("
			INSERT tb_user_voucher_history (CENTER_SQ,USER_SQ,UV_SQ,MANAGER_SQ,VOUCHER_TYPE,ATTENDANCE_TYPE,USED_COUNT,CLASS_RESERV_SQ,RESERV_SQ,ALLOWANCE,DESCRIPTION,CREATE_DT)
			VALUES (:CENTER_SQ,:MEMBER_SQ,:MANAGER_SQ,:UV_SQ,:VOUCHER_TYPE,:ATTENDANCE_TYPE,:USED_COUNT,:CLASS_RESERV_SQ,:RESERV_SQ,:ALLOWANCE,:DESCRIPTION,NOW())
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':MEMBER_SQ', $MEMBER_SQ);
		$database->bind(':MANAGER_SQ', $MANAGER_SQ);
		$database->bind(':UV_SQ', $UV_SQ);
		$database->bind(':VOUCHER_TYPE', 2);
		$database->bind(':ATTENDANCE_TYPE', $ATTENDANCE);
		$database->bind(':USED_COUNT', $USED_COUNT);
		$database->bind(':CLASS_RESERV_SQ', $CLASS_RESERV_SQ);
		$database->bind(':RESERV_SQ', 0);
		$database->bind(':ALLOWANCE', $ALLOWANCE);
		$database->bind(':DESCRIPTION', $CLASS_DESC);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}

		// 로그 저장
		insert_Log_History($CENTER_SQ,$USER_SQ,$MEMBER_SQ, $DEVICE_SQ, $IP, $GROUP, $CATEGORY, $ACTION,$database);

		exit($class_info);
		break;	

	case 'execUserGroupScheduleAttend':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$CLASS_SQ = getAnyParameter("CLASS_SQ","");
		$CLASS_RESERV_SQ = getAnyParameter("CLASS_RESERV_SQ","");
		$UV_SQ = getAnyParameter("UV_SQ","");
		
		// 기본값 설정		
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$GROUP = 4;
		$CATEGORY = 43;
		$MEMBER_SQ = Get_SingleField('tb_class_reservation', 'USER_SQ', 'CLASS_RESERV_SQ', $CLASS_RESERV_SQ, '',$database);
		$CLASS_DESC = Get_ClassInfo($CLASS_SQ,$database);
		$ACTION = Get_UserInfo($MEMBER_SQ,$database)."회원님이 ".$CLASS_DESC." 수업에 출석하셨습니다.";
		$IP = getClientIPv4();
		
		$database->prepare("
			UPDATE tb_class_reservation SET RESERV_STATUS=3 WHERE CLASS_RESERV_SQ=:CLASS_RESERV_SQ
		");
		$database->bind(':CLASS_RESERV_SQ', $CLASS_RESERV_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		
		// 출결 히스토리 테이블 
		$USED_COUNT = 1;
		$ATTENDANCE = 2; //출석 
		$ALLOWANCE = get_group_allowance($CLASS_SQ, $ATTENDANCE,$database);
		
		$MANAGER_SQ = Get_SingleField('tb_class_schedule', 'MANAGER_SQ', 'CLASS_SQ', $CLASS_SQ, '',$database);
		$database->prepare("
			INSERT tb_user_voucher_history (CENTER_SQ,USER_SQ,MANAGER_SQ,UV_SQ,VOUCHER_TYPE,ATTENDANCE_TYPE,USED_COUNT,CLASS_RESERV_SQ,RESERV_SQ,ALLOWANCE,DESCRIPTION,CREATE_DT)
			VALUES (:CENTER_SQ,:MEMBER_SQ,:MANAGER_SQ,:UV_SQ,:VOUCHER_TYPE,:ATTENDANCE_TYPE,:USED_COUNT,:CLASS_RESERV_SQ,:RESERV_SQ,:ALLOWANCE,:DESCRIPTION,NOW())
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':MEMBER_SQ', $MEMBER_SQ);
		$database->bind(':MANAGER_SQ', $MANAGER_SQ);
		$database->bind(':UV_SQ', $UV_SQ);
		$database->bind(':VOUCHER_TYPE', 2);
		$database->bind(':ATTENDANCE_TYPE', $ATTENDANCE);
		$database->bind(':USED_COUNT', $USED_COUNT);
		$database->bind(':CLASS_RESERV_SQ', $CLASS_RESERV_SQ);
		$database->bind(':RESERV_SQ', 0);
		$database->bind(':ALLOWANCE', $ALLOWANCE);
		$database->bind(':DESCRIPTION', $CLASS_DESC);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
				
		$database->prepare("
			UPDATE tb_user SET LAST_VISIT_DT=now() WHERE USER_SQ in (SELECT USER_SQ FROM tb_class_reservation WHERE CLASS_RESERV_SQ=:CLASS_RESERV_SQ)
		");
		$database->bind(':CLASS_RESERV_SQ', $CLASS_RESERV_SQ);
		$database->execute();

		$database->prepare("
			UPDATE tb_user_voucher SET USEDCOUNT=USEDCOUNT+:USED_COUNT WHERE UV_SQ=:UV_SQ
		");
		$database->bind(':USED_COUNT', $USED_COUNT);
		$database->bind(':UV_SQ', $UV_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		
		$database->prepare("
			SELECT a.*, b.RESERV_LIST, c.WAITING_LIST, b.RESERV_COUNT, c.WAIT_COUNT
			FROM tb_class_schedule a
			left outer  join (SELECT CLASS_SQ, GROUP_CONCAT(DISTINCT USER_SQ SEPARATOR '.') RESERV_LIST, sum(CASE WHEN RESERV_STATUS=1 THEN 1 ELSE 0 END) as RESERV_COUNT
						FROM tb_class_reservation where CLASS_SQ=:CLASS_SQ and RESERV_STATUS=1
						GROUP BY CLASS_SQ) b 
			on a.CLASS_SQ=b.CLASS_SQ
			left outer  join (SELECT CLASS_SQ, GROUP_CONCAT(DISTINCT USER_SQ SEPARATOR '.') WAITING_LIST, sum(CASE WHEN RESERV_STATUS=5 THEN 1 ELSE 0 END) as WAIT_COUNT
						FROM tb_class_reservation where CLASS_SQ=:CLASS_SQ2 and RESERV_STATUS=5
						GROUP BY CLASS_SQ) c 
			on a.CLASS_SQ=c.CLASS_SQ
			where a.CLASS_SQ=:CLASS_SQ3
		");
		$database->bind(':CLASS_SQ', $CLASS_SQ);
		$database->bind(':CLASS_SQ2', $CLASS_SQ);
		$database->bind(':CLASS_SQ3', $CLASS_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$class_info = json_encode($rows);

		// 로그 저장
		insert_Log_History($CENTER_SQ,$USER_SQ,$MEMBER_SQ, $DEVICE_SQ, $IP, $GROUP, $CATEGORY, $ACTION,$database);

		exit($class_info);
		break;	

	case 'execUserGroupScheduleCancel':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$CLASS_RESERV_SQ = getAnyParameter("CLASS_RESERV_SQ","");
		$CLASS_SQ = getAnyParameter("CLASS_SQ","");
		$RESERV_STATUS = getAnyParameter("RESERV_STATUS","");
		
		// 기본값 설정		
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$GROUP = 4;
		$CATEGORY = 42;
		$MEMBER_SQ = Get_SingleField('tb_class_reservation', 'USER_SQ', 'CLASS_RESERV_SQ', $CLASS_RESERV_SQ, '',$database);
		$CLASS_DESC = Get_ClassInfo($CLASS_SQ,$database);
		$ACTION = Get_UserInfo($MEMBER_SQ,$database)."회원님이 ".$CLASS_DESC." 수업에 예약취소하셨습니다.";
		$IP = getClientIPv4();
		
		// 예약 또는 대시상태를 취소.
		$database->prepare("
			UPDATE tb_class_reservation SET RESERV_STATUS=2 WHERE CLASS_RESERV_SQ=:CLASS_RESERV_SQ
		");
		$database->bind(':CLASS_RESERV_SQ', $CLASS_RESERV_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		// 로그 저장
		insert_Log_History($CENTER_SQ,$USER_SQ,$MEMBER_SQ, $DEVICE_SQ, $IP, $GROUP, $CATEGORY, $ACTION,$database);
		
		// 만약 예약자 중에서 예약 취소이면 최초 대기자 1명을 예약상로로변경. 
		if ($RESERV_STATUS==1) {
			$CLASS_RESERV_SQ_ALT = Get_SingleField('tb_class_reservation', 'MIN(CLASS_RESERV_SQ)', 'CLASS_SQ', $CLASS_SQ, ' and RESERV_STATUS=5',$database);
			$database->prepare("
				UPDATE tb_class_reservation SET RESERV_STATUS=1 
				WHERE CLASS_RESERV_SQ=:CLASS_RESERV_SQ_ALT
			");
			$database->bind(':CLASS_RESERV_SQ_ALT', $CLASS_RESERV_SQ_ALT);
			$database->execute();
			
			$CATEGORY = 41;
			$MEMBER_SQ_ALT = Get_SingleField('tb_class_reservation', 'USER_SQ', 'CLASS_RESERV_SQ', $CLASS_RESERV_SQ_ALT, '',$database);
			$ACTION = Get_UserInfo($MEMBER_SQ_ALT,$database)."회원님이 ".Get_ClassInfo($CLASS_SQ,$database)." 수업에 대기에서 예약으로 상태가 변경되었습니다.";
			// 로그 저장
			insert_Log_History($CENTER_SQ,$USER_SQ,$MEMBER_SQ, $DEVICE_SQ, $IP, $GROUP, $CATEGORY, $ACTION,$database);
		}
		
		// 출결 히스토리 테이블 
		$USED_COUNT = 0;
		$ATTENDANCE = 6; //예약취소  
		$ALLOWANCE = 0;
		$UV_SQ = Get_SingleField('tb_class_reservation', 'UV_SQ', 'CLASS_RESERV_SQ', $CLASS_RESERV_SQ, '',$database);
		$MANAGER_SQ = Get_SingleField('tb_class_schedule', 'MANAGER_SQ', 'CLASS_SQ', $CLASS_SQ, '',$database);

		$database->prepare("
			INSERT tb_user_voucher_history (CENTER_SQ,USER_SQ,MANAGER_SQ,UV_SQ,VOUCHER_TYPE,ATTENDANCE_TYPE,USED_COUNT,CLASS_RESERV_SQ,RESERV_SQ,ALLOWANCE,DESCRIPTION,CREATE_DT)
			VALUES (:CENTER_SQ,:MEMBER_SQ,:MANAGER_SQ,:UV_SQ,:VOUCHER_TYPE,:ATTENDANCE_TYPE,:USED_COUNT,:CLASS_RESERV_SQ,:RESERV_SQ,:ALLOWANCE,:DESCRIPTION,NOW())
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':MEMBER_SQ', $MEMBER_SQ);
		$database->bind(':MANAGER_SQ', $MANAGER_SQ);
		$database->bind(':UV_SQ', $UV_SQ);
		$database->bind(':VOUCHER_TYPE', 2);
		$database->bind(':ATTENDANCE_TYPE', $ATTENDANCE);
		$database->bind(':USED_COUNT', $USED_COUNT); // 만약 2개 이상의 이용권 차감하는 항목이 발생하면 수정해야한다.
		$database->bind(':CLASS_RESERV_SQ', $CLASS_RESERV_SQ);
		$database->bind(':RESERV_SQ', 0);
		$database->bind(':ALLOWANCE', $ALLOWANCE);
		$database->bind(':DESCRIPTION', $CLASS_DESC);
		$database->execute();

		// 결과 데이터 취득 
		$database->prepare("
			SELECT a.*, b.RESERV_LIST, c.WAITING_LIST, b.RESERV_COUNT, c.WAIT_COUNT
			FROM tb_class_schedule a
			left outer join (SELECT CLASS_SQ, GROUP_CONCAT(DISTINCT USER_SQ SEPARATOR '.') RESERV_LIST, sum(CASE WHEN RESERV_STATUS=1 THEN 1 ELSE 0 END) as RESERV_COUNT
						FROM tb_class_reservation where CLASS_SQ=:CLASS_SQ and RESERV_STATUS=1
						GROUP BY CLASS_SQ) b 
			on a.CLASS_SQ=b.CLASS_SQ
			left outer join (SELECT CLASS_SQ, GROUP_CONCAT(DISTINCT USER_SQ SEPARATOR '.') WAITING_LIST, sum(CASE WHEN RESERV_STATUS=5 THEN 1 ELSE 0 END) as WAIT_COUNT
						FROM tb_class_reservation where CLASS_SQ=:CLASS_SQ2 and RESERV_STATUS=5
						GROUP BY CLASS_SQ) c 
			on a.CLASS_SQ=c.CLASS_SQ
			where a.CLASS_SQ=:CLASS_SQ3
		");
		$database->bind(':CLASS_SQ', $CLASS_SQ);
		$database->bind(':CLASS_SQ2', $CLASS_SQ);
		$database->bind(':CLASS_SQ3', $CLASS_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$class_info = json_encode($rows);

		exit($class_info);
		break;	

		
	case 'getUserGroupWeeklyScheduleList':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		
		$START_DT = getAnyParameter("START_DT","");
		$END_DT = getAnyParameter("END_DT","");
		$ROOM_SQ = getAnyParameter("ROOM_SQ",0);

		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "스케쥴러";
		$SUBCATEGORY = "스케쥴 조회";
		$ACTION = $CENTER_SQ . " 스케쥴을 조회하였습니다.";
		$IP = getClientIPv4();

		// 휴일취득
		$database->prepare("
			select HOLIDAY, HOLIDAY_NAME, MANAGER_SQ FROM tb_holiday where HOLIDAY>=:START_DT and HOLIDAY<=:END_DT and CENTER_SQ=:CENTER_SQ
		");
		$database->bind(':START_DT', $START_DT);
		$database->bind(':END_DT', $END_DT);
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();
		
		$rows = $database->fetchAll();
		$holidaylist = json_encode($rows);

		//tb_voucher
		//VOUCHER_SQ,CENTER_SQ,CATEGORY_SQ,SUBCATEGORY_SQ,VOUCHER_NAME,VOUCHER_TYPE,USE_TYPE,PERIOD_TYPE,PERIOD,COUNT_TYPE,COUNT,PRICE,SURTAX_TYPE,DISCOUNT_TYPE,DISCOUNT_RATIO,DISCOUNT_AMOUNT,SELLINGPRICE

		$database->prepare("
			SELECT a.*, b.RESERV_LIST, c.WAITING_LIST, b.RESERV_COUNT, c.WAIT_COUNT, d.USER_NM TRAINER_NM, d.PHONE_NO
			FROM tb_class_schedule a
			left outer join (SELECT CLASS_SQ, GROUP_CONCAT(DISTINCT USER_SQ SEPARATOR '.') RESERV_LIST, sum(CASE WHEN RESERV_STATUS=1 THEN 1 ELSE 0 END) as RESERV_COUNT
						FROM tb_class_reservation where  RESERV_STATUS=1
						GROUP BY CLASS_SQ) b 
			on a.CLASS_SQ=b.CLASS_SQ
			left outer join (SELECT CLASS_SQ, GROUP_CONCAT(DISTINCT USER_SQ SEPARATOR '.') WAITING_LIST, sum(CASE WHEN RESERV_STATUS=5 THEN 1 ELSE 0 END) as WAIT_COUNT
						FROM tb_class_reservation where  RESERV_STATUS=5
						GROUP BY CLASS_SQ) c 
			on a.CLASS_SQ=c.CLASS_SQ
			left outer join tb_user d on a.MANAGER_SQ=d.USER_SQ
			WHERE CLASS_DT>=:START_DT AND  CLASS_DT<=:END_DT AND (:ROOM_SQ=0 OR ROOM_SQ=:ROOM_SQ2) AND a.CENTER_SQ=:CENTER_SQ
			ORDER BY CLASS_DT , START_TIME
		");
		$database->bind(':START_DT', $START_DT);
		$database->bind(':END_DT', $END_DT);
		$database->bind(':ROOM_SQ', $ROOM_SQ);
		$database->bind(':ROOM_SQ2', $ROOM_SQ);
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$schedulelist = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($schedulelist.'|'.$holidaylist);
		break;	
						
	case 'execClassReservAdd':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$CLASS_SQ = getAnyParameter("CLASS_SQ","");
		$MEMBER_SQ = getAnyParameter("USER_SQ","");
		$UV_SQ = getAnyParameter("UV_SQ","");
		
		// 기본값 설정		
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$GROUP = 4;
		$CATEGORY = 41;
		$CLASS_DESC = Get_ClassInfo($CLASS_SQ,$database);
		$ACTION = Get_UserInfo($MEMBER_SQ,$database)."회원님이 ".$CLASS_DESC." 수업에 ".Get_VoucherInfo($UV_SQ,$database)." 이용권으로 예약하셨습니다.";
		$IP = getClientIPv4();
		
		// 해당 시퀀스의 예약 숫자가 여유있는지 판별
		$database->prepare("
			SELECT a.CLASS_SQ, a.RESERVATION_COUNT, a.WAITING_COUNT, b.RESERVED_COUNT, b.WAITED_COUNT
			FROM tb_class_schedule a left outer join 
			(SELECT CLASS_SQ, sum(CASE WHEN RESERV_STATUS=1 THEN 1 ELSE 0 END) as RESERVED_COUNT,
					sum(CASE WHEN RESERV_STATUS=5 THEN 1 ELSE 0 END) as WAITED_COUNT
 			FROM tb_class_reservation
			WHERE  CLASS_SQ=:CLASS_SQ) b on a.CLASS_SQ = b.CLASS_SQ
			WHERE a.CLASS_SQ=:CLASS_SQ
		");
		$database->bind(':CLASS_SQ', $CLASS_SQ);
		$database->execute();
		
		// 해당 시퀀스의 대기 숫자가 여유있는지 판별
		$row = $database->fetch();
		$RESERVATION_COUNT = $row["RESERVATION_COUNT"];
		$WAITING_COUNT = $row["WAITING_COUNT"];
		$RESERVED_COUNT = $row["RESERVED_COUNT"];
		$WAITED_COUNT = $row["WAITED_COUNT"];
		
		if ($RESERVATION_COUNT>$RESERVED_COUNT)
		{
			$RESERV_STATUS = 1;
		} else if ($WAITING_COUNT>$WAITED_COUNT)
		{
			$RESERV_STATUS = 5;
		} else {
			$response_array["result"] = 'Fail';
			$response_array["reason"] = 'RESERVATION and WAITING is FULL';
			exit(json_encode($response_array));
		}
		
		// 해당 시퀀스의 예약 숫자가 여유있는지 판별
		$database->prepare("
			SELECT CLASS_SQ, 
 			FROM tb_class_reservation
			WHERE  CLASS_SQ=:CLASS_SQ and USER_SQ=:MEMBER_SQ
		");
		$database->bind(':CLASS_SQ', $CLASS_SQ);
		$database->bind(':MEMBER_SQ', $MEMBER_SQ);
		$database->execute();
				
		if ($database->rowCount() > 0)
		{
			$response_array["result"] = 'Fail';
			$response_array["reason"] = 'This User is already reserved at this class.';
			exit(json_encode($response_array));
		}
		
		// 저장 
		//CLASS_SQ,CLASS_RESERV_SQ,USER_SQ,UV_SQ,RESERV_STATUS,RESERV_ID,RESERV_DT
		$database->prepare("
			INSERT tb_class_reservation (CLASS_SQ, USER_SQ, UV_SQ, RESERV_STATUS, RESERV_ID, RESERV_DT) values
				(:CLASS_SQ, :USER_SQ, :UV_SQ, :RESERV_STATUS, :RESERV_ID, now())
		");
		$database->bind(':CLASS_SQ', $CLASS_SQ);
		$database->bind(':USER_SQ', $MEMBER_SQ);
		$database->bind(':UV_SQ', $UV_SQ);
		$database->bind(':RESERV_STATUS', $RESERV_STATUS);
		$database->bind(':RESERV_ID', $USER_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			$response_array["reason"] = 'INSERT Fail.';
			exit(json_encode($response_array));
		}
						
		// 출결 히스토리 테이블 
		$USED_COUNT = 0;
		$ATTENDANCE = 5; //예약  
		$ALLOWANCE = 0;
		$CLASS_RESERV_SQ = Get_SingleField('tb_class_reservation', 'MAX(CLASS_RESERV_SQ)', 'USER_SQ', $MEMBER_SQ, '',$database);
		$MANAGER_SQ = Get_SingleField('tb_class_schedule', 'MANAGER_SQ', 'CLASS_SQ', $CLASS_SQ, '',$database);

		$database->prepare("
			INSERT tb_user_voucher_history (CENTER_SQ,USER_SQ,MANAGER_SQ,UV_SQ,VOUCHER_TYPE,ATTENDANCE_TYPE,USED_COUNT,CLASS_RESERV_SQ,RESERV_SQ,ALLOWANCE,DESCRIPTION,CREATE_DT)
			VALUES (:CENTER_SQ,:MEMBER_SQ,:MANAGER_SQ,:UV_SQ,:VOUCHER_TYPE,:ATTENDANCE_TYPE,:USED_COUNT,:CLASS_RESERV_SQ,:RESERV_SQ,:ALLOWANCE,:DESCRIPTION,NOW())
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':MEMBER_SQ', $MEMBER_SQ);
		$database->bind(':MANAGER_SQ', $MANAGER_SQ);
		$database->bind(':UV_SQ', $UV_SQ);
		$database->bind(':VOUCHER_TYPE', 2);
		$database->bind(':ATTENDANCE_TYPE', $ATTENDANCE);
		$database->bind(':USED_COUNT', $USED_COUNT); // 만약 2개 이상의 이용권 차감하는 항목이 발생하면 수정해야한다.
		$database->bind(':CLASS_RESERV_SQ', $CLASS_RESERV_SQ);
		$database->bind(':RESERV_SQ', 0);
		$database->bind(':ALLOWANCE', $ALLOWANCE);
		$database->bind(':DESCRIPTION', $CLASS_DESC);
		$database->execute();

		// 데이터 취득 
		$database->prepare("
			SELECT *
 			FROM tb_class_reservation
			WHERE CLASS_RESERV_SQ=LAST_INSERT_ID()
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$reserv_info = json_encode($rows);

		$database->prepare("
			SELECT a.*, b.RESERV_LIST, c.WAITING_LIST, b.RESERV_COUNT, c.WAIT_COUNT
			FROM tb_class_schedule a
			left outer  join (SELECT CLASS_SQ, GROUP_CONCAT(DISTINCT USER_SQ SEPARATOR '.') RESERV_LIST, sum(CASE WHEN RESERV_STATUS=1 THEN 1 ELSE 0 END) as RESERV_COUNT
						FROM tb_class_reservation where CLASS_SQ=:CLASS_SQ and RESERV_STATUS=1
						GROUP BY CLASS_SQ) b 
			on a.CLASS_SQ=b.CLASS_SQ
			left outer  join (SELECT CLASS_SQ, GROUP_CONCAT(DISTINCT USER_SQ SEPARATOR '.') WAITING_LIST, sum(CASE WHEN RESERV_STATUS=5 THEN 1 ELSE 0 END) as WAIT_COUNT
						FROM tb_class_reservation where CLASS_SQ=:CLASS_SQ2 and RESERV_STATUS=5
						GROUP BY CLASS_SQ) c 
			on a.CLASS_SQ=c.CLASS_SQ
			where a.CLASS_SQ=:CLASS_SQ3
		");
		$database->bind(':CLASS_SQ', $CLASS_SQ);
		$database->bind(':CLASS_SQ2', $CLASS_SQ);
		$database->bind(':CLASS_SQ3', $CLASS_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$class_info = json_encode($rows);

		// 로그 저장
		insert_Log_History($CENTER_SQ,$USER_SQ,$MEMBER_SQ, $DEVICE_SQ, $IP, $GROUP, $CATEGORY, $ACTION,$database);

		exit($reserv_info.'|'.$class_info);
		break;
		
	case 'getUserGroupVoucherListSch':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		
		$RESERV_USER_SQ = getAnyParameter("USER_SQ","");
		$START_DT = getAnyParameter("START_DT","");
		
		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "스케쥴러";
		$SUBCATEGORY = "스케쥴 조회";
		$ACTION = $CENTER_SQ . " 스케쥴을 조회하였습니다.";
		$IP = getClientIPv4();
		//tb_voucher
		//VOUCHER_SQ,CENTER_SQ,CATEGORY_SQ,SUBCATEGORY_SQ,VOUCHER_NAME,VOUCHER_TYPE,USE_TYPE,PERIOD_TYPE,PERIOD,COUNT_TYPE,COUNT,PRICE,SURTAX_TYPE,DISCOUNT_TYPE,DISCOUNT_RATIO,DISCOUNT_AMOUNT,SELLINGPRICE

		$database->prepare("
			SELECT UV_SQ,MEMBER_SQ,VOUCHER_SQ,VOUCHER_NAME,VOUCHER_TYPE,b.DESCRIPTION as VOUCHER_TYPE_NAME,USE_TYPE,c.DESCRIPTION as USE_TYPE_NAME,
					PERIOD_TYPE,d.DESCRIPTION as PERIOD_TYPE_NAME,PERIOD,PERIOD_UNIT,f.DESCRIPTION as PERIOD_UNIT_NAME,
					COUNT_TYPE,e.DESCRIPTION as COUNT_TYPE_NAME,COUNT,ENTERLIMIT_DAY,ENTERLIMIT_WEEK,USEDCOUNT,
					(SELECT COUNT(*) FROM tb_class_reservation WHERE USER_SQ=a.MEMBER_SQ and UV_SQ=a.UV_SQ and RESERV_STATUS=1) RESERV_COUNT,
					USE_STATUS,USE_STARTDATE,USE_LASTDATE,SELLER_SQ, g.USER_NM as SELLER_NM,TRAINER_SQ, h.USER_NM as TRAINER_NM
 			FROM tb_user_voucher a
				  left outer join tb_common b on a.VOUCHER_TYPE=b.CODE and b.BASE_CD='CD004'
				  left outer join tb_common c on a.USE_TYPE=c.CODE and c.BASE_CD='CD005'
				  left outer join tb_common d on a.PERIOD_TYPE=d.CODE and d.BASE_CD='CD006'
				  left outer join tb_common e on a.COUNT_TYPE=e.CODE and e.BASE_CD='CD007'
				  left outer join tb_common f on a.PERIOD_UNIT=f.CODE and f.BASE_CD='CD010'
				  left outer join tb_user g on g.USER_SQ=a.SELLER_SQ
				  left outer join tb_user h on h.USER_SQ=a.TRAINER_SQ
			WHERE MEMBER_SQ=:RESERV_USER_SQ and VOUCHER_TYPE=2 and USE_TYPE=2 and ((COUNT_TYPE=2 AND COUNT>USEDCOUNT) OR (COUNT_TYPE=1)) AND USE_STARTDATE<=:START_DT AND USE_LASTDATE>=:START_DT2
		");
		$database->bind(':RESERV_USER_SQ', $RESERV_USER_SQ);
		$database->bind(':START_DT', $START_DT);
		$database->bind(':START_DT2', $START_DT);
		$database->execute();

		$rows = $database->fetchAll();
		$uservoucherlist = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($uservoucherlist);
		break;	
		
	case 'execClassDelete':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$CLASS_SQ = getAnyParameter("CLASS_SQ","");
		
		// 기본값 설정		
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$GROUP = 2;
		$CATEGORY = 23;
		$ACTION = Get_TrainerInfo($USER_SQ,$database)."님이 ".Get_ClassInfo($CLASS_SQ,$database)."수업을 삭제하였습니다.";
		$IP = getClientIPv4();
		
		$database->prepare("
			delete from tb_class_schedule 
			where CLASS_SQ=:CLASS_SQ
		");
		$database->bind(':CLASS_SQ', $CLASS_SQ);
		$database->execute();
		
		// 저장이 실패하면  종료 FAIL을 리턴 
		$response_array["result"] = 'Success';
		if ($database->rowCount() < 1 ) { 
			$rows = $database->fetchAll();
			$response_array["result"] = 'Fail';
			$reservInfo = json_encode($response_array);
			exit($reservInfo);
		}

		// 로그 저장
		insert_Log_History($CENTER_SQ,$USER_SQ,$USER_SQ, $DEVICE_SQ, $IP, $GROUP, $CATEGORY, $ACTION,$database);

		exit(json_encode($response_array));
		break;				
		
	case 'execClassMemoModify':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$CLASS_SQ = getAnyParameter("CLASS_SQ","");
		$CLASS_MEMO = getAnyParameter("CLASS_MEMO","");
		
		// 기본값 설정		
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$GROUP = 2;
		$CATEGORY = 22;
		$ACTION = Get_TrainerInfo($USER_SQ,$database)."님이 ".Get_ClassInfo($CLASS_SQ,$database)."수업메모를 수정하였습니다.";
		$IP = getClientIPv4();
		
		$database->prepare("
			update tb_class_schedule SET CLASS_MEMO=:CLASS_MEMO,MODIFIEDBY=:MODIFIEDBY,MODIFIED=now()
			where CLASS_SQ=:CLASS_SQ
		");
		$database->bind(':CLASS_MEMO', $CLASS_MEMO);
		$database->bind(':MODIFIEDBY', $USER_SQ);
		$database->bind(':CLASS_SQ', $CLASS_SQ);
		$database->execute();
		// 저장이 실패하면  종료 FAIL을 리턴 
		if ($database->rowCount() < 1 ) { 
			$rows = $database->fetchAll();
			$response_array["result"] = 'Update Fail';
			$reservInfo = json_encode($response_array);
			exit($reservInfo);
		}
		
		$database->prepare("
			SELECT a.*, b.RESERV_LIST, c.WAITING_LIST, b.RESERV_COUNT, c.WAIT_COUNT
			FROM tb_class_schedule a
			left outer  join (SELECT CLASS_SQ, GROUP_CONCAT(DISTINCT USER_SQ SEPARATOR '.') RESERV_LIST, sum(CASE WHEN RESERV_STATUS=1 THEN 1 ELSE 0 END) as RESERV_COUNT
						FROM tb_class_reservation where CLASS_SQ=:CLASS_SQ and RESERV_STATUS=1
						GROUP BY CLASS_SQ) b 
			on a.CLASS_SQ=b.CLASS_SQ
			left outer  join (SELECT CLASS_SQ, GROUP_CONCAT(DISTINCT USER_SQ SEPARATOR '.') WAITING_LIST, sum(CASE WHEN RESERV_STATUS=5 THEN 1 ELSE 0 END) as WAIT_COUNT
						FROM tb_class_reservation where CLASS_SQ=:CLASS_SQ2 and RESERV_STATUS=5
						GROUP BY CLASS_SQ) c 
			on a.CLASS_SQ=c.CLASS_SQ
			where a.CLASS_SQ=:CLASS_SQ3
		");
		$database->bind(':CLASS_SQ', $CLASS_SQ);
		$database->bind(':CLASS_SQ2', $CLASS_SQ);
		$database->bind(':CLASS_SQ3', $CLASS_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$class_info = json_encode($rows);

		// 로그 저장
		insert_Log_History($CENTER_SQ,$USER_SQ,$USER_SQ, $DEVICE_SQ, $IP, $GROUP, $CATEGORY, $ACTION,$database);

		exit($class_info);
		break;	
		
	case 'getClassReservedUserList':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$CLASS_SQ = getAnyParameter("CLASS_SQ","");
		
		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "예약 설정 정보";
		$SUBCATEGORY = "예약 설정 정보 저장";
		$ACTION = $CENTER_SQ . " 센터의 예약설정 정보를 저장하였습니다.";
		$IP = getClientIPv4();
		
		$database->prepare("
			SELECT a.CLASS_RESERV_SQ, a.CLASS_SQ, a.UV_SQ, a.USER_SQ, b.USER_NM, b.PHONE_NO, a.RESERV_STATUS, c.DESCRIPTION as RESERV_STATUS_NAME,a.RESERV_DT
			FROM tb_class_reservation a 
			inner join tb_user b on a.USER_SQ=b.USER_SQ
			inner join tb_common c on a.RESERV_STATUS = c.CODE and BASE_CD='CD015'
			where a.CLASS_SQ=:CLASS_SQ and (a.RESERV_STATUS<>2)
			order by a.RESERV_STATUS, a.RESERV_DT
		");
		$database->bind(':CLASS_SQ', $CLASS_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$class_info = json_encode($rows);
		
		$database->prepare("
			SELECT a.*, b.RESERV_COUNT, c.ATTEND_COUNT, d.ABSENCE_COUNT, e.WAIT_COUNT
			FROM tb_class_schedule a
			left outer  join (SELECT CLASS_SQ, sum(CASE WHEN RESERV_STATUS=1 THEN 1 ELSE 0 END) as RESERV_COUNT
						FROM tb_class_reservation where CLASS_SQ=:CLASS_SQ and RESERV_STATUS=1
						GROUP BY CLASS_SQ) b 
			on a.CLASS_SQ=b.CLASS_SQ
			left outer  join (SELECT CLASS_SQ, sum(CASE WHEN RESERV_STATUS=1 THEN 1 ELSE 0 END) as ATTEND_COUNT
						FROM tb_class_reservation where CLASS_SQ=:CLASS_SQ2 and RESERV_STATUS=3
						GROUP BY CLASS_SQ) c 
			on a.CLASS_SQ=c.CLASS_SQ
			left outer  join (SELECT CLASS_SQ, sum(CASE WHEN RESERV_STATUS=1 THEN 1 ELSE 0 END) as ABSENCE_COUNT
						FROM tb_class_reservation where CLASS_SQ=:CLASS_SQ3 and RESERV_STATUS=4
						GROUP BY CLASS_SQ) d 
			on a.CLASS_SQ=d.CLASS_SQ
			left outer  join (SELECT CLASS_SQ, sum(CASE WHEN RESERV_STATUS=5 THEN 1 ELSE 0 END) as WAIT_COUNT
						FROM tb_class_reservation where CLASS_SQ=:CLASS_SQ4 and RESERV_STATUS=5
						GROUP BY CLASS_SQ) e 
			on a.CLASS_SQ=e.CLASS_SQ
			where a.CLASS_SQ=:CLASS_SQ5
		");
		$database->bind(':CLASS_SQ', $CLASS_SQ);
		$database->bind(':CLASS_SQ2', $CLASS_SQ);
		$database->bind(':CLASS_SQ3', $CLASS_SQ);
		$database->bind(':CLASS_SQ4', $CLASS_SQ);
		$database->bind(':CLASS_SQ5', $CLASS_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$class_status = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($class_info.'|'.$class_status);
		break;	
				
	case 'execClassScheduleModify':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$CLASS_NAME = getAnyParameter("CLASS_NAME","");		
		$ROOM_SQ = getAnyParameter("ROOM_SQ","");		
		$START_DT = getAnyParameter("START_DT","");		
		$END_DT = getAnyParameter("END_DT","");
		$START_TIME = getAnyParameter("START_TIME","");
		$END_TIME = getAnyParameter("END_TIME","");
		$MANAGER_SQ = getAnyParameter("MANAGER_SQ","");
		$BENEFITS = getAnyParameter("BENEFITS","");
		$RESERVATION_COUNT = getAnyParameter("RESERVATION_COUNT","");
		$WAITING_COUNT = getAnyParameter("WAITING_COUNT","");
		$MEMO = getAnyParameter("MEMO","");
		
		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "예약 설정 정보";
		$SUBCATEGORY = "예약 설정 정보 저장";
		$ACTION = $CENTER_SQ . " 센터의 예약설정 정보를 저장하였습니다.";
		$IP = getClientIPv4();
		
		// 시작 날과 마지막날 변환 
		$start_date = date_create($START_DT);
		$end_date = date_create($END_DT);
		$diff=date_diff($end_date,$start_date);
		$days = $diff->format("%a");
		
		$dateweek_string = '';
		$date_string = '';
		$cur_date =date_add($start_date,date_interval_create_from_date_string("-1 days"));
		// 전체 이터레이션 
		for ($cur_day = 0; $cur_day<=$days; $cur_day++)
		{
		//http://192.168.0.12:8888/bodyscanner/flow_controller.php?task=execClassScheduleSave&CLASS_NAME=TEST&ROOM_SQ=1&START_DT=2021-03-31&END_DT=2021-04-31&MON=1&TUE=0&WED=1&THU=1&FRI=1&SAT=1&SUN=1&START_TIME=18:00&END_TIME=19:00&MANAGER_SQ=14&BENEFITS=100000&RESERVATION_COUNT=41&WAITING_COUNT=41&MEMO=TEST
		// 해당날짜의 해당시간이 비어있는지를 판단 
			$cur_date = date_add($cur_date,date_interval_create_from_date_string("1 days"));
			$week_day = date_format($cur_date,"w");
			$cur_date_str = date_format($cur_date,'Y-m-d');
			if (($MON==1 && $week_day==1) || ($TUE==1 && $week_day==2) || ($WED==1 && $week_day==3) || ($THU==1 && $week_day==4) 
				|| ($FRI==1 && $week_day==5) || ($SAT==1 && $week_day==6) || ($SUN==1 && $week_day==0))
			{
				$dateweek_string = $dateweek_string.','.$week_day;
				$date_string = $date_string.','.$cur_date_str;
				$database->prepare("
					select CLASS_SQ,CENTER_SQ,ROOM_SQ,CLASS_NAME,MANAGER_SQ,CLASS_DT,START_TIME,END_TIME,MEMO,CREATEDBY,CREATED		
					from tb_class_schedule
					where CENTER_SQ=:CENTER_SQ and 
							(:START < concat(date_format(CLASS_DT, '%Y-%m-%d'), ' ', END_TIME) 
									and :END > concat(date_format(CLASS_DT, '%Y-%m-%d'), ' ', START_TIME) )
				");
				$database->bind(':CENTER_SQ', $CENTER_SQ);
				$database->bind(':START', $cur_date_str.' '.$START_TIME);
				$database->bind(':END', $cur_date_str.' '.$END_TIME);
				$database->execute();
				// 비어있지 않으면 종료 FAIL을 리턴 
				if ($database->rowCount() > 0 ) { 
					$rows = $database->fetchAll();
					$response_array["result"] = 'Fail';
					$response_array["duplicate"] = $rows;
					$reservInfo = json_encode($response_array);
					exit($reservInfo);
				}
			}
		}
		
		// 현재 마지막 시퀀스를 가져온다. 
		$database->prepare("
			select max(CLASS_SQ) as CLASS_SQ		
			from tb_class_schedule 
			where CENTER_SQ=:CENTER_SQ
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();
		$row = $database->fetch();
		$LastSEQ = $row["CLASS_SQ"];
		
		$start_date = date_create($START_DT);
		$cur_date =date_add($start_date,date_interval_create_from_date_string("-1 days"));
		// 전체 이터레이션 - 데이터 저장 
		for ($cur_day = 0; $cur_day<=$days; $cur_day++)
		{
			$cur_date = date_add($cur_date,date_interval_create_from_date_string("1 days"));
			$week_day = date_format($cur_date,"w");
			$cur_date_str = date_format($cur_date,'Y-m-d');
			if (($MON==1 && $week_day==1) || ($TUE==1 && $week_day==2) || ($WED==1 && $week_day==3) || ($THU==1 && $week_day==4) 
				|| ($FRI==1 && $week_day==5) || ($SAT==1 && $week_day==6) || ($SUN==1 && $week_day==0))
			{
				$database->prepare("
					insert into tb_class_schedule
						(CENTER_SQ,ROOM_SQ,CLASS_NAME,MANAGER_SQ,CLASS_DT,START_TIME,END_TIME,MEMO,RESERVATION_COUNT, WAITING_COUNT,CREATEDBY,CREATED) VALUES
						(:CENTER_SQ,:ROOM_SQ,:CLASS_NAME,:MANAGER_SQ,:CLASS_DT,:START_TIME,:END_TIME,:MEMO,:RESERVATION_COUNT,:WAITING_COUNT, :CREATEDBY,now())
				");
				$database->bind(':CENTER_SQ', $CENTER_SQ);
				$database->bind(':ROOM_SQ', $ROOM_SQ);
				$database->bind(':CLASS_NAME', $CLASS_NAME);
				$database->bind(':MANAGER_SQ', $MANAGER_SQ);
				$database->bind(':CLASS_DT', $cur_date_str);
				$database->bind(':START_TIME', $START_TIME);
				$database->bind(':END_TIME', $END_TIME);
				$database->bind(':MEMO', $MEMO);
				$database->bind(':RESERVATION_COUNT', $RESERVATION_COUNT);
				$database->bind(':WAITING_COUNT', $WAITING_COUNT);
				$database->bind(':CREATEDBY', $USER_SQ);
				$database->execute();
				// 저장이 실패하면  종료 FAIL을 리턴 
				if ($database->rowCount() < 1 ) { 
					$rows = $database->fetchAll();
					$response_array["result"] = 'Insert Fail';
					$reservInfo = json_encode($response_array);
					exit($reservInfo);
				}
			}
		}
		
		$database->prepare("
			select CLASS_SQ,CENTER_SQ,ROOM_SQ,CLASS_NAME,MANAGER_SQ,CLASS_DT,START_TIME,END_TIME,MEMO,CREATEDBY,CREATED			
			from tb_class_schedule 
			where CENTER_SQ=:CENTER_SQ and CLASS_SQ>:LastSEQ and START_TIME=:START_TIME and END_TIME=:END_TIME and CREATEDBY=:CREATEDBY
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':LastSEQ', $LastSEQ);
		$database->bind(':START_TIME', $START_TIME);
		$database->bind(':END_TIME', $END_TIME);
		$database->bind(':CREATEDBY', $USER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$reservInfo = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($reservInfo);
		break;	
				
	case 'execClassScheduleSave':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$CLASS_NAME = getAnyParameter("CLASS_NAME","");		
		$ROOM_SQ = getAnyParameter("ROOM_SQ","");		
		$START_DT = getAnyParameter("START_DT","");		
		$END_DT = getAnyParameter("END_DT","");		
		$MON = getAnyParameter("MON","");		
		$TUE = getAnyParameter("TUE","");		
		$WED = getAnyParameter("WED","");		
		$THU = getAnyParameter("THU","");		
		$FRI = getAnyParameter("FRI","");		
		$SAT = getAnyParameter("SAT","");		
		$SUN = getAnyParameter("SUN","");		
		$START_TIME = getAnyParameter("START_TIME","");
		$END_TIME = getAnyParameter("END_TIME","");
		$MANAGER_SQ = getAnyParameter("MANAGER_SQ","");
		$BENEFITS = getAnyParameter("BENEFITS","");
		$RESERVATION_COUNT = getAnyParameter("RESERVATION_COUNT","");
		$WAITING_COUNT = getAnyParameter("WAITING_COUNT","");
		$MEMO = getAnyParameter("MEMO","");
		
		// 기본값 설정		
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$GROUP = 2;
		$CATEGORY = 21;
		$dayofweek = '';
		if ($MON==1){ $dayofweek=$dayofweek.'월';}
		if ($TUE==1){ $dayofweek=$dayofweek.'화';}
		if ($WED==1){ $dayofweek=$dayofweek.'수';}
		if ($THU==1){ $dayofweek=$dayofweek.'목';}
		if ($FRI==1){ $dayofweek=$dayofweek.'금';}
		if ($SAT==1){ $dayofweek=$dayofweek.'토';}
		if ($SUN==1){ $dayofweek=$dayofweek.'일';}
		$ROOM_NAME = Get_SingleField('tb_room', 'ROOM_NAME', 'ROOM_SQ', $ROOM_SQ, '', $database);
		$ACTION = Get_TrainerInfo($USER_SQ,$database)."님이 ".'<span class="class">'.$CLASS_NAME.'( 룸:'.$ROOM_NAME.', 시간:'.$START_DT.'~'.$END_DT.' '.$START_TIME.','.$dayofweek.' )</span>수업을 일괄 등록하였습니다.';
		$IP = getClientIPv4();
		
		// 시작 날과 마지막날 변환 
		$start_date = date_create($START_DT);
		$end_date = date_create($END_DT);
		$diff=date_diff($end_date,$start_date);
		$days = $diff->format("%a");
		
		$dateweek_string = '';
		$date_string = '';
		$cur_date =date_add($start_date,date_interval_create_from_date_string("-1 days"));
		// 전체 이터레이션 
		for ($cur_day = 0; $cur_day<=$days; $cur_day++)
		{
		//http://192.168.0.12:8888/bodyscanner/flow_controller.php?task=execClassScheduleSave&CLASS_NAME=TEST&ROOM_SQ=1&START_DT=2021-03-31&END_DT=2021-04-31&MON=1&TUE=0&WED=1&THU=1&FRI=1&SAT=1&SUN=1&START_TIME=18:00&END_TIME=19:00&MANAGER_SQ=14&BENEFITS=100000&RESERVATION_COUNT=41&WAITING_COUNT=41&MEMO=TEST
		// 해당날짜의 해당시간이 비어있는지를 판단 
			$cur_date = date_add($cur_date,date_interval_create_from_date_string("1 days"));
			$week_day = date_format($cur_date,"w");
			$cur_date_str = date_format($cur_date,'Y-m-d');
			if (($MON==1 && $week_day==1) || ($TUE==1 && $week_day==2) || ($WED==1 && $week_day==3) || ($THU==1 && $week_day==4) 
				|| ($FRI==1 && $week_day==5) || ($SAT==1 && $week_day==6) || ($SUN==1 && $week_day==0))
			{
				$dateweek_string = $dateweek_string.','.$week_day;
				$date_string = $date_string.','.$cur_date_str;
				$database->prepare("
					select CLASS_SQ,CENTER_SQ,ROOM_SQ,CLASS_NAME,MANAGER_SQ,CLASS_DT,START_TIME,END_TIME,MEMO,CREATEDBY,CREATED		
					from tb_class_schedule
					where CENTER_SQ=:CENTER_SQ and ROOM_SQ=:ROOM_SQ and 
							(:START < concat(date_format(CLASS_DT, '%Y-%m-%d'), ' ', END_TIME) 
									and :END > concat(date_format(CLASS_DT, '%Y-%m-%d'), ' ', START_TIME) )
				");
				$database->bind(':CENTER_SQ', $CENTER_SQ);
				$database->bind(':ROOM_SQ', $ROOM_SQ);
				$database->bind(':START', $cur_date_str.' '.$START_TIME);
				$database->bind(':END', $cur_date_str.' '.$END_TIME);
				$database->execute();
				// 비어있지 않으면 종료 FAIL을 리턴 
				if ($database->rowCount() > 0 ) { 
					$rows = $database->fetchAll();
					$response_array["result"] = 'Fail';
					$response_array["duplicate"] = $rows;
					$reservInfo = json_encode($response_array);
					exit($reservInfo);
				}
			}
		}
		
		// 현재 마지막 시퀀스를 가져온다. 
		$database->prepare("
			select max(CLASS_SQ) as CLASS_SQ		
			from tb_class_schedule 
			where CENTER_SQ=:CENTER_SQ
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();
		$row = $database->fetch();
		$LastSEQ = $row["CLASS_SQ"];
		
		$start_date = date_create($START_DT);
		$cur_date =date_add($start_date,date_interval_create_from_date_string("-1 days"));
		// 전체 이터레이션 - 데이터 저장 
		for ($cur_day = 0; $cur_day<=$days; $cur_day++)
		{
			$cur_date = date_add($cur_date,date_interval_create_from_date_string("1 days"));
			$week_day = date_format($cur_date,"w");
			$cur_date_str = date_format($cur_date,'Y-m-d');
			if (($MON==1 && $week_day==1) || ($TUE==1 && $week_day==2) || ($WED==1 && $week_day==3) || ($THU==1 && $week_day==4) 
				|| ($FRI==1 && $week_day==5) || ($SAT==1 && $week_day==6) || ($SUN==1 && $week_day==0))
			{
				$database->prepare("
					insert into tb_class_schedule
						(CENTER_SQ,ROOM_SQ,CLASS_NAME,MANAGER_SQ,CLASS_DT,START_TIME,END_TIME,ALLOWANCE,MEMO,RESERVATION_COUNT, WAITING_COUNT,CREATEDBY,CREATED) VALUES
						(:CENTER_SQ,:ROOM_SQ,:CLASS_NAME,:MANAGER_SQ,:CLASS_DT,:START_TIME,:END_TIME,:ALLOWANCE,:MEMO,:RESERVATION_COUNT,:WAITING_COUNT, :CREATEDBY,now())
				");
				$database->bind(':CENTER_SQ', $CENTER_SQ);
				$database->bind(':ROOM_SQ', $ROOM_SQ);
				$database->bind(':CLASS_NAME', $CLASS_NAME);
				$database->bind(':MANAGER_SQ', $MANAGER_SQ);
				$database->bind(':CLASS_DT', $cur_date_str);
				$database->bind(':START_TIME', $START_TIME);
				$database->bind(':END_TIME', $END_TIME);
				$database->bind(':ALLOWANCE', $BENEFITS);
				$database->bind(':MEMO', $MEMO);
				$database->bind(':RESERVATION_COUNT', $RESERVATION_COUNT);
				$database->bind(':WAITING_COUNT', $WAITING_COUNT);
				$database->bind(':CREATEDBY', $USER_SQ);
				$database->execute();
				// 저장이 실패하면  종료 FAIL을 리턴 
				if ($database->rowCount() < 1 ) { 
					$rows = $database->fetchAll();
					$response_array["result"] = 'Insert Fail';
					$reservInfo = json_encode($response_array);
					exit($reservInfo);
				}
			}
		}
		
		$database->prepare("
			select CLASS_SQ,CENTER_SQ,ROOM_SQ,CLASS_NAME,MANAGER_SQ,CLASS_DT,START_TIME,END_TIME,ALLOWANCE,MEMO,CREATEDBY,CREATED			
			from tb_class_schedule 
			where CENTER_SQ=:CENTER_SQ and CLASS_SQ>:LastSEQ and START_TIME=:START_TIME and END_TIME=:END_TIME and CREATEDBY=:CREATEDBY
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':LastSEQ', $LastSEQ);
		$database->bind(':START_TIME', $START_TIME);
		$database->bind(':END_TIME', $END_TIME);
		$database->bind(':CREATEDBY', $USER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$reservInfo = json_encode($rows);
		
		// 로그 저장
		insert_Log_History($CENTER_SQ,$USER_SQ,$USER_SQ, $DEVICE_SQ, $IP, $GROUP, $CATEGORY, $ACTION,$database);
		exit($reservInfo);
		break;	
		
			
	case 'getGroupScheduleInitInfo':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "스케쥴러";
		$SUBCATEGORY = "스케쥴 조회";
		$ACTION = $CENTER_SQ . " 스케쥴을 조회하였습니다.";
		$IP = getClientIPv4();

		// 여기에 지나간 스케쥴 출결 처리 루틴을 넣을 것.
		
		$database->prepare("
			SELECT ROOM_SQ,ROOM_NAME,ROOM_DESC
 			FROM tb_room
			WHERE CENTER_SQ=:CENTER_SQ and USE_YN=1 ORDER BY ROOM_NAME ASC
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$roomlist = json_encode($rows);		
		
		// DB 조회
		$database->prepare("
			select a.USER_SQ,a.CENTER_SQ,a.USER_NM,a.PHONE_NO,a.EMAIL,a.BIRTH_DT, a.REG_DT, (select REG_DT from bs_measurement where USER_SQ=a.USER_SQ order by REG_DT desc LIMIT 1) as MEAS_DATE
					,a.TRAINER, b.USER_NM TRAINER_NM, a.ISUSE
					from tb_user a  left outer join tb_user b on a.TRAINER=b.USER_SQ
					where a.CENTER_SQ=:CENTER_SQ and a.GRADE=1 order by a.REG_DT desc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$memberlist = json_encode($rows);

		// 최종 결과 취득 
		$database->prepare("
			select a.USER_SQ,a.CENTER_SQ,a.USER_NM,a.PHONE_NO,a.ADDRESS,a.EMAIL,a.BIRTH_DT, a.REG_DT, a.GRADE,
						a.WORKCATEGORY, a.WORKTYPE, a.WORKSTARTDATE, a.WORKENDDATE, a.WORKSTATUS, a.ISUSE
					from tb_user a  left outer join tb_user b on a.TRAINER=b.USER_SQ
					where a.CENTER_SQ=:CENTER_SQ and a.ISMANAGER=1 and a.ISUSE=1 order by a.REG_DT desc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();
		
		$rows = $database->fetchAll();
		$managerlist = json_encode($rows);
		
		$database->prepare("
			select COMMON_SQ,BASE_CD,CODE,NAME,DESCRIPTION
					from tb_common where BASE_CD='CD014' and CODE>0 order by CODE asc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$reservation_status_list = json_encode($rows);

		$database->prepare("
			SELECT RESERVSETTING_SQ,PSN_RESERV_TYPE,PSN_RESERV_TIME,PSN_MOD_TYPE,PSN_MOD_TIME,PSN_AUTO_ABSENCE,PSN_ABSENCE_TICKET,
					GRP_RESERV_TYPE,GRP_RESERV_TIME,GRP_MOD_TYPE,GRP_MOD_TIME,GRP_AUTO_ABSENCE,GRP_ABSENCE_TICKET,CREATEDBY,CREATED
 			FROM tb_reservation_setting
			WHERE CENTER_SQ=:CENTER_SQ AND CREATED <=now()
			ORDER BY CREATED DESC LIMIT 1
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$reservation_setting_list = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($memberlist.'|'.$managerlist.'|'.$roomlist.'|'.$reservation_setting_list.'|'.$reservation_status_list);
		break;	
		

	case 'execRoomModify':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$ROOM_SQ = getAnyParameter("ROOM_SQ","");
		$ROOM_NAME = getAnyParameter("ROOM_NAME","");
		$ROOM_DESC = getAnyParameter("ROOM_DESC","");
		
		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "룸  정보";
		$SUBCATEGORY = "룸 정보 수정";
		$ACTION = $CENTER_SQ . " 룸 정보가 수정되었습니다.";
		$IP = getClientIPv4();
			
		$database->prepare("
			UPDATE tb_room set ROOM_NAME=:ROOM_NAME, ROOM_DESC=:ROOM_DESC, 
						MODIFIED_ID=:MODIFIED_ID, MODIFIED_DT=now()
			where ROOM_SQ = :ROOM_SQ
		");
		$database->bind(':ROOM_NAME', $ROOM_NAME);
		$database->bind(':ROOM_DESC', $ROOM_DESC);
		$database->bind(':MODIFIED_ID', $USER_SQ);
		$database->bind(':ROOM_SQ', $ROOM_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		
		$database->prepare("
			SELECT ROOM_SQ,ROOM_NAME,ROOM_DESC
 			FROM tb_room
			WHERE CENTER_SQ=:CENTER_SQ and USE_YN=1 ORDER BY ROOM_NAME ASC
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$roomlist = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit(json_encode($roomlist));
		break;
				
	case 'execRoomDelete':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$ROOM_SQ = getAnyParameter("ROOM_SQ","");
		
		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "룸 정보";
		$SUBCATEGORY = "룸 정보 삭제";
		$ACTION = $CENTER_SQ . " 룸 정보가 삭제되었습니다.";
		$IP = getClientIPv4();
			
		$database->prepare("
			UPDATE tb_room SET USE_YN=0, 
						MODIFIED_ID=:MODIFIED_ID, MODIFIED_DT=now()
			where ROOM_SQ = :ROOM_SQ
		");
		$database->bind(':ROOM_SQ', $ROOM_SQ);
		$database->bind(':MODIFIED_ID', $USER_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		
		$database->prepare("
			SELECT ROOM_SQ,ROOM_NAME,ROOM_DESC
 			FROM tb_room
			WHERE CENTER_SQ=:CENTER_SQ and USE_YN=1 ORDER BY ROOM_NAME ASC
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$holidaylist = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit(json_encode($holidaylist));
		break;
		
	case 'execRoomAdd':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$ROOM_NAME = getAnyParameter("ROOM_NAME","");
		$ROOM_DESC = getAnyParameter("ROOM_DESC","");
		
		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "휴일 정보";
		$SUBCATEGORY = "휴일 정보 추가";
		$ACTION = $CENTER_SQ . " 휴일 정보가 추가되었습니다.";
		$IP = getClientIPv4();
			
		$database->prepare("
			INSERT tb_room (CENTER_SQ, ROOM_NAME, ROOM_DESC, CREATED_ID, CREATED_DT, USE_YN) values
				(:CENTER_SQ, :ROOM_NAME, :ROOM_DESC, :CREATED_ID, now(), 1)
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':ROOM_NAME', $ROOM_NAME);
		$database->bind(':ROOM_DESC', $ROOM_DESC);
		$database->bind(':CREATED_ID', $USER_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}

		$database->prepare("
			SELECT ROOM_SQ,ROOM_NAME,ROOM_DESC
 			FROM tb_room
			WHERE CENTER_SQ=:CENTER_SQ and USE_YN=1 ORDER BY ROOM_NAME ASC
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$holidaylist = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit(json_encode($holidaylist));
		break;

	case 'getManagerHoliday':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$MANAGER_SQ = getAnyParameter("MANAGER_SQ","");
		$START_DT = getAnyParameter("START_DT","");
		$END_DT = getAnyParameter("END_DT","");
		
		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "휴일 정보";
		$SUBCATEGORY = "휴일 정보 삭제";
		$ACTION = $CENTER_SQ . " 휴일 정보가 삭제되었습니다.";
		$IP = getClientIPv4();
		
		$database->prepare("
			SELECT HOLIDAY_SQ,MANAGER_SQ,CENTER_SQ,HOLIDAY,HOLIDAY_NAME
 			FROM tb_holiday
			WHERE CENTER_SQ=:CENTER_SQ AND MANAGER_SQ=:MANAGER_SQ 
			AND HOLIDAY>=:START_DT AND  HOLIDAY<=:END_DT
			ORDER BY HOLIDAY ASC
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':MANAGER_SQ', $MANAGER_SQ);
		$database->bind(':START_DT', $START_DT);
		$database->bind(':END_DT', $END_DT);
		$database->execute();

		$rows = $database->fetchAll();
		$holidaylist = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit(json_encode($holidaylist));
		break;
		
	case 'execManagerHolidayDelete':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$HOLIDAY_SQ = getAnyParameter("HOLIDAY_SQ","");
		$MANAGER_SQ = getAnyParameter("MANAGER_SQ","");
		
		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "휴일 정보";
		$SUBCATEGORY = "휴일 정보 삭제";
		$ACTION = $CENTER_SQ . " 휴일 정보가 삭제되었습니다.";
		$IP = getClientIPv4();
			
		$database->prepare("
			DELETE from tb_holiday where HOLIDAY_SQ = :HOLIDAY_SQ
		");
		$database->bind(':HOLIDAY_SQ', $HOLIDAY_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		
		$database->prepare("
			SELECT HOLIDAY_SQ,MANAGER_SQ,CENTER_SQ,HOLIDAY,HOLIDAY_NAME
 			FROM tb_holiday
			WHERE CENTER_SQ=:CENTER_SQ AND MANAGER_SQ=:MANAGER_SQ ORDER BY HOLIDAY ASC
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':MANAGER_SQ', $MANAGER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$holidaylist = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit(json_encode($holidaylist));
		break;
		
	case 'execManagerHolidayAdd':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$HOLIDAY = getAnyParameter("HOLIDAY","");
		$HOLIDAY_NAME = getAnyParameter("HOLIDAY_NAME","");
		$MANAGER_SQ = getAnyParameter("MANAGER_SQ","");
		
		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "휴일 정보";
		$SUBCATEGORY = "휴일 정보 추가";
		$ACTION = $CENTER_SQ . " 휴일 정보가 추가되었습니다.";
		$IP = getClientIPv4();
			
		$database->prepare("
			INSERT tb_holiday (CENTER_SQ, MANAGER_SQ, HOLIDAY, HOLIDAY_NAME) values
				(:CENTER_SQ, :MANAGER_SQ, :HOLIDAY, :HOLIDAY_NAME)
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':MANAGER_SQ', $MANAGER_SQ);
		$database->bind(':HOLIDAY', $HOLIDAY);
		$database->bind(':HOLIDAY_NAME', $HOLIDAY_NAME);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}

		$database->prepare("
			SELECT HOLIDAY_SQ,MANAGER_SQ,CENTER_SQ,HOLIDAY,HOLIDAY_NAME
 			FROM tb_holiday
			WHERE CENTER_SQ=:CENTER_SQ AND MANAGER_SQ=:MANAGER_SQ ORDER BY HOLIDAY ASC
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':MANAGER_SQ', $MANAGER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$holidaylist = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit(json_encode($holidaylist));
		break;
				

	case 'execManagerSchedSettingModify': // 임직원 스케쥴 설정 수정 
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$MEMBER_WORKTIME_SQ = getAnyParameter("MEMBER_WORKTIME_SQ",0);
		$MEMBER_SQ = getAnyParameter("MANAGER_SQ",0);
		$MON = getAnyParameter("MON","");
		$TUE = getAnyParameter("TUE","");
		$WED = getAnyParameter("WED","");
		$THU = getAnyParameter("THU","");
		$FRI = getAnyParameter("FRI","");
		$SAT = getAnyParameter("SAT","");
		$SUN = getAnyParameter("SUN","");
		$WORK_TIME = getAnyParameter("WORK_TIME","");
		
		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "예약 설정 정보";
		$SUBCATEGORY = "예약 설정 정보 저장";
		$ACTION = $CENTER_SQ . " 센터의 예약설정 정보를 저장하였습니다.";
		$IP = getClientIPv4();
		
		if ($MEMBER_WORKTIME_SQ==0) {
			$database->prepare("
				INSERT INTO tb_member_work_time (MEMBER_SQ, MON,TUE,WED,THU,FRI,SAT,SUN,WORK_TIME, CREATEDID, CREATEDDT, MODIFIEDID, MODIFIEDDT)
						VALUES (:MANAGER_SQ, :MON,:TUE,:WED,:THU,:FRI,:SAT,:SUN,:WORK_TIME, :CREATEDID, now(), :MODIFIEDID, now())
			");
			$database->bind(':MANAGER_SQ', $MEMBER_SQ);
			$database->bind(':MON', $MON);
			$database->bind(':TUE', $TUE);
			$database->bind(':WED', $WED);
			$database->bind(':THU', $THU);
			$database->bind(':FRI', $FRI);
			$database->bind(':SAT', $SAT);
			$database->bind(':SUN', $SUN);
			$database->bind(':WORK_TIME', $WORK_TIME);
			$database->bind(':CREATEDID', $USER_SQ);
			$database->bind(':MODIFIEDID', $USER_SQ);
			$database->execute();
			
		} else {
			$database->prepare("
				UPDATE tb_member_work_time SET WORK_TIME=:WORK_TIME , MON=:MON, TUE=:TUE, WED=:WED, THU=:THU, FRI=:FRI, 
									SAT=:SAT, SUN=:SUN, MODIFIEDID=:MODIFIEDID, MODIFIEDDT=now()
				WHERE MEMBER_WORKTIME_SQ=:MEMBER_WORKTIME_SQ
			");
			$database->bind(':WORK_TIME', $WORK_TIME);
			$database->bind(':MON', $MON);
			$database->bind(':TUE', $TUE);
			$database->bind(':WED', $WED);
			$database->bind(':THU', $THU);
			$database->bind(':FRI', $FRI);
			$database->bind(':SAT', $SAT);
			$database->bind(':SUN', $SUN);
			$database->bind(':MODIFIEDID', $USER_SQ);
			$database->bind(':MEMBER_WORKTIME_SQ', $MEMBER_WORKTIME_SQ);
			$database->execute();
		}
		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		
		// 결과 취득 
		$database->prepare("
			SELECT MEMBER_WORKTIME_SQ,MEMBER_SQ, MON,TUE,WED,THU,FRI,SAT,SUN,WORK_TIME,MODIFIEDID,MODIFIEDDT
			FROM tb_member_work_time
			WHERE MEMBER_SQ=:MANAGER_SQ
		");
		$database->bind(':MANAGER_SQ', $MANAGER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$member_sch_info = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($member_sch_info);
		break;	


			
	case 'execUserPersonalScheduleAbsence':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$RESERV_SQ = getAnyParameter("RESERV_SQ","");
		$UV_SQ = getAnyParameter("UV_SQ","");
		
		// 기본값 설정		
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$GROUP = 4;
		$CATEGORY = 44;
		$MEMBER_SQ = Get_SingleField('tb_reservation', 'USER_SQ', 'RESERV_SQ', $RESERV_SQ, '',$database);
		$CLASS_DESC = Get_ReservInfo($RESERV_SQ,$database);
		$ACTION = Get_UserInfo($MEMBER_SQ,$database)."회원님이 ".$CLASS_DESC." 예약에 결석하셨습니다.";
		$IP = getClientIPv4();
		
		$database->prepare("
			UPDATE tb_reservation SET RESERV_STATUS=4 WHERE RESERV_SQ=:RESERV_SQ
		");
		$database->bind(':RESERV_SQ', $RESERV_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		
		$USED_COUNT = 0;
		if ($UV_SQ>0) {		
			$USED_COUNT = 1;
			$database->prepare("
				UPDATE tb_user_voucher SET USEDCOUNT=USEDCOUNT+:USED_COUNT WHERE UV_SQ=:UV_SQ
			");
			$database->bind(':USED_COUNT', $USED_COUNT);
			$database->bind(':UV_SQ', $UV_SQ);
			$database->execute();

			$response_array["result"] = 'Success';

			if ($database->rowCount() < 1) { 
				$response_array["result"] = 'Fail';
				exit(json_encode($response_array));
			}
			$ACTION = $ACTION." 이용권 ".Get_VoucherInfo($UV_SQ,$database)."이 1회 차감되었습니다.";
		}
		
		$database->prepare("
			select a.RESERV_SQ,a.CENTER_SQ,a.USER_SQ,a.MANAGER_SQ,a.UV_SQ,a.RESERV_STATUS,a.RESERV_DT,a.START_TIME,a.END_TIME,a.MEMO,
					b.USER_NM, c.USER_NM as MANAGER_NAME, d.DESCRIPTION as RESERV_STATUS_NAME			
			from tb_reservation a  left outer join tb_user b on a.USER_SQ=b.USER_SQ
				  left outer join tb_user c on a.MANAGER_SQ=c.USER_SQ
				  left outer join tb_common d on a.RESERV_STATUS=d.CODE and d.BASE_CD='CD015'
			where a.RESERV_SQ=:RESERV_SQ order by a.RESERV_SQ desc LIMIT 1
		");
		$database->bind(':RESERV_SQ', $RESERV_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$reservInfo = json_encode($rows);
		
		// 출결 히스토리 테이블 
		$ATTENDANCE = 3; //결석
		$ALLOWANCE = 0;
		$MANAGER_SQ = Get_SingleField('tb_reservation', 'MANAGER_SQ', 'RESERV_SQ', $RESERV_SQ, '',$database);
		if ($USED_COUNT>0) {
			$ALLOWANCE = get_personal_allowance($RESERV_SQ, $UV_SQ, $ATTENDANCE,$database);
		}
		$database->prepare("
			INSERT tb_user_voucher_history (CENTER_SQ,USER_SQ,MANAGER_SQ,UV_SQ,VOUCHER_TYPE,ATTENDANCE_TYPE,USED_COUNT,CLASS_RESERV_SQ,RESERV_SQ,ALLOWANCE,DESCRIPTION,CREATE_DT)
			VALUES (:CENTER_SQ,:MEMBER_SQ,:MANAGER_SQ,:UV_SQ,:VOUCHER_TYPE,:ATTENDANCE_TYPE,:USED_COUNT,:CLASS_RESERV_SQ,:RESERV_SQ,:ALLOWANCE,:DESCRIPTION,NOW())
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':MEMBER_SQ', $MEMBER_SQ);
		$database->bind(':MANAGER_SQ', $MANAGER_SQ);
		$database->bind(':UV_SQ', $UV_SQ);
		$database->bind(':VOUCHER_TYPE', 1);
		$database->bind(':ATTENDANCE_TYPE', $ATTENDANCE);
		$database->bind(':USED_COUNT', $USED_COUNT);
		$database->bind(':CLASS_RESERV_SQ', 0);
		$database->bind(':RESERV_SQ', $RESERV_SQ);
		$database->bind(':ALLOWANCE', $ALLOWANCE);
		$database->bind(':DESCRIPTION', $CLASS_DESC);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}

		// 로그 저장
		insert_Log_History($CENTER_SQ,$USER_SQ,$MEMBER_SQ, $DEVICE_SQ, $IP, $GROUP, $CATEGORY, $ACTION,$database);

		exit($reservInfo);
		break;	

	case 'execUserPersonalScheduleAttend':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$RESERV_SQ = getAnyParameter("RESERV_SQ","");
		$UV_SQ = getAnyParameter("UV_SQ","");
		
		// 기본값 설정		
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$GROUP = 4;
		$CATEGORY = 43;
		$MEMBER_SQ = Get_SingleField('tb_reservation', 'USER_SQ', 'RESERV_SQ', $RESERV_SQ, '',$database);
		$CLASS_DESC = Get_ReservInfo($RESERV_SQ,$database);
		$ACTION = Get_UserInfo($MEMBER_SQ,$database)."회원님이 ".$CLASS_DESC." 예약에 출석하셨습니다.";
		$ACTION = $ACTION." 이용권 ".Get_VoucherInfo($UV_SQ,$database)."이 1회 차감되었습니다.";
		$IP = getClientIPv4();
		
		$database->prepare("
			UPDATE tb_reservation SET RESERV_STATUS=3 WHERE RESERV_SQ=:RESERV_SQ
		");
		$database->bind(':RESERV_SQ', $RESERV_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
				
		$database->prepare("
			UPDATE tb_user SET LAST_VISIT_DT=now() WHERE USER_SQ in (SELECT USER_SQ FROM tb_reservation WHERE RESERV_SQ=:RESERV_SQ)
		");
		$database->bind(':RESERV_SQ', $RESERV_SQ);
		$database->execute();

		$USED_COUNT = 1;
		$database->prepare("
			UPDATE tb_user_voucher SET USEDCOUNT=USEDCOUNT+:USED_COUNT WHERE UV_SQ=:UV_SQ
		");
		$database->bind(':USED_COUNT', $USED_COUNT);
		$database->bind(':UV_SQ', $UV_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		
		$database->prepare("
			select a.RESERV_SQ,a.CENTER_SQ,a.USER_SQ,a.MANAGER_SQ,a.UV_SQ,a.RESERV_STATUS,a.RESERV_DT,a.START_TIME,a.END_TIME,a.MEMO,
					b.USER_NM, c.USER_NM as MANAGER_NAME, d.DESCRIPTION as RESERV_STATUS_NAME			
			from tb_reservation a  left outer join tb_user b on a.USER_SQ=b.USER_SQ
				  left outer join tb_user c on a.MANAGER_SQ=c.USER_SQ
				  left outer join tb_common d on a.RESERV_STATUS=d.CODE and d.BASE_CD='CD015'
			where a.RESERV_SQ=:RESERV_SQ order by a.RESERV_SQ desc LIMIT 1
		");
		$database->bind(':RESERV_SQ', $RESERV_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$reservInfo = json_encode($rows);
		
		// 출결 히스토리 테이블 
		$ATTENDANCE = 2; //출석
		$ALLOWANCE = get_personal_allowance($RESERV_SQ, $UV_SQ, $ATTENDANCE,$database);
		$MANAGER_SQ = Get_SingleField('tb_reservation', 'MANAGER_SQ', 'RESERV_SQ', $RESERV_SQ, '',$database);
		$database->prepare("
			INSERT tb_user_voucher_history (CENTER_SQ,USER_SQ,MANAGER_SQ,UV_SQ,VOUCHER_TYPE,ATTENDANCE_TYPE,USED_COUNT,CLASS_RESERV_SQ,RESERV_SQ,ALLOWANCE, DESCRIPTION,CREATE_DT)
			VALUES (:CENTER_SQ,:MEMBER_SQ,:MANAGER_SQ,:UV_SQ,:VOUCHER_TYPE,:ATTENDANCE_TYPE,:USED_COUNT,:CLASS_RESERV_SQ,:RESERV_SQ,:ALLOWANCE,:DESCRIPTION,NOW())
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':MEMBER_SQ', $MEMBER_SQ);
		$database->bind(':MANAGER_SQ', $MANAGER_SQ);
		$database->bind(':UV_SQ', $UV_SQ);
		$database->bind(':VOUCHER_TYPE', 1);
		$database->bind(':ATTENDANCE_TYPE', 2);
		$database->bind(':USED_COUNT', $USED_COUNT);
		$database->bind(':CLASS_RESERV_SQ', 0);
		$database->bind(':RESERV_SQ', $RESERV_SQ);
		$database->bind(':ALLOWANCE', $ALLOWANCE);
		$database->bind(':DESCRIPTION', $CLASS_DESC);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}

		// 로그 저장
		insert_Log_History($CENTER_SQ,$USER_SQ,$MEMBER_SQ, $DEVICE_SQ, $IP, $GROUP, $CATEGORY, $ACTION,$database);

		exit($reservInfo);
		break;	

	case 'execUserPersonalScheduleCancel':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$RESERV_SQ = getAnyParameter("RESERV_SQ","");
		
		// 기본값 설정		
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$GROUP = 4;
		$CATEGORY = 42;
		$MEMBER_SQ = Get_SingleField('tb_reservation', 'USER_SQ', 'RESERV_SQ', $RESERV_SQ, '',$database);
		$CLASS_DESC = Get_ReservInfo($RESERV_SQ,$database);
		$ACTION = Get_UserInfo($MEMBER_SQ,$database)."회원님이 ".$CLASS_DESC." 예약을 취소하셨습니다.";
		$IP = getClientIPv4();
		
		$database->prepare("
			UPDATE tb_reservation SET RESERV_STATUS=2 WHERE RESERV_SQ=:RESERV_SQ
		");
		$database->bind(':RESERV_SQ', $RESERV_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		
		// 출결 히스토리 테이블 
		$USED_COUNT = 0;
		$ATTENDANCE = 6; //예약취소  
		$ALLOWANCE = 0;
		$UV_SQ = Get_SingleField('tb_reservation', 'UV_SQ', 'RESERV_SQ', $RESERV_SQ, '',$database);
		$MANAGER_SQ = Get_SingleField('tb_reservation', 'MANAGER_SQ', 'RESERV_SQ', $RESERV_SQ, '',$database);
		$database->prepare("
			INSERT tb_user_voucher_history (CENTER_SQ,USER_SQ,MANAGER_SQ,UV_SQ,VOUCHER_TYPE,ATTENDANCE_TYPE,USED_COUNT,CLASS_RESERV_SQ,RESERV_SQ,ALLOWANCE,DESCRIPTION,CREATE_DT)
			VALUES (:CENTER_SQ,:MEMBER_SQ,:MANAGER_SQ,:UV_SQ,:VOUCHER_TYPE,:ATTENDANCE_TYPE,:USED_COUNT,:CLASS_RESERV_SQ,:RESERV_SQ,:ALLOWANCE,:DESCRIPTION,NOW())
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':MEMBER_SQ', $MEMBER_SQ);
		$database->bind(':MANAGER_SQ', $MANAGER_SQ);
		$database->bind(':UV_SQ', $UV_SQ);
		$database->bind(':VOUCHER_TYPE', 1);
		$database->bind(':ATTENDANCE_TYPE', $ATTENDANCE);
		$database->bind(':USED_COUNT', $USED_COUNT); // 만약 2개 이상의 이용권 차감하는 항목이 발생하면 수정해야한다.
		$database->bind(':CLASS_RESERV_SQ', 0);
		$database->bind(':RESERV_SQ', $RESERV_SQ);
		$database->bind(':ALLOWANCE', $ALLOWANCE);
		$database->bind(':DESCRIPTION', $CLASS_DESC);
		$database->execute();

		$database->prepare("
			select a.RESERV_SQ,a.CENTER_SQ,a.USER_SQ,a.MANAGER_SQ,a.UV_SQ,a.RESERV_STATUS,a.RESERV_DT,a.START_TIME,a.END_TIME,a.MEMO,
					b.USER_NM, c.USER_NM as MANAGER_NAME, d.DESCRIPTION as RESERV_STATUS_NAME			
			from tb_reservation a  left outer join tb_user b on a.USER_SQ=b.USER_SQ
				  left outer join tb_user c on a.MANAGER_SQ=c.USER_SQ
				  left outer join tb_common d on a.RESERV_STATUS=d.CODE and d.BASE_CD='CD015'
			where a.RESERV_SQ=:RESERV_SQ order by a.RESERV_SQ desc LIMIT 1
		");
		$database->bind(':RESERV_SQ', $RESERV_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$reservInfo = json_encode($rows);

		// 로그 저장
		insert_Log_History($CENTER_SQ,$USER_SQ,$MEMBER_SQ, $DEVICE_SQ, $IP, $GROUP, $CATEGORY, $ACTION,$database);

		exit($reservInfo);
		break;	

	case 'getUserVoucherInfoSch':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		
		$UV_SQ = getAnyParameter("UV_SQ","");
		
		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "스케쥴러";
		$SUBCATEGORY = "스케쥴 조회";
		$ACTION = $CENTER_SQ . " 스케쥴을 조회하였습니다.";
		$IP = getClientIPv4();
		//tb_voucher
		//VOUCHER_SQ,CENTER_SQ,CATEGORY_SQ,SUBCATEGORY_SQ,VOUCHER_NAME,VOUCHER_TYPE,USE_TYPE,PERIOD_TYPE,PERIOD,COUNT_TYPE,COUNT,PRICE,SURTAX_TYPE,DISCOUNT_TYPE,DISCOUNT_RATIO,DISCOUNT_AMOUNT,SELLINGPRICE

		$database->prepare("
			SELECT UV_SQ,MEMBER_SQ,VOUCHER_SQ,VOUCHER_NAME,VOUCHER_TYPE,b.DESCRIPTION as VOUCHER_TYPE_NAME,USE_TYPE,c.DESCRIPTION as USE_TYPE_NAME,
					PERIOD_TYPE,d.DESCRIPTION as PERIOD_TYPE_NAME,PERIOD,PERIOD_UNIT,f.DESCRIPTION as PERIOD_UNIT_NAME,
					COUNT_TYPE,e.DESCRIPTION as COUNT_TYPE_NAME,COUNT,ENTERLIMIT_DAY,ENTERLIMIT_WEEK,USEDCOUNT,
					(SELECT COUNT(*) FROM tb_reservation WHERE USER_SQ=a.MEMBER_SQ and UV_SQ=a.UV_SQ and RESERV_STATUS=1) RESERV_COUNT,
					USE_STATUS,USE_STARTDATE,USE_LASTDATE,SELLER_SQ, g.USER_NM as SELLER_NM,TRAINER_SQ, h.USER_NM as TRAINER_NM
 			FROM tb_user_voucher a
				  left outer join tb_common b on a.VOUCHER_TYPE=b.CODE and b.BASE_CD='CD004'
				  left outer join tb_common c on a.USE_TYPE=c.CODE and c.BASE_CD='CD005'
				  left outer join tb_common d on a.PERIOD_TYPE=d.CODE and d.BASE_CD='CD006'
				  left outer join tb_common e on a.COUNT_TYPE=e.CODE and e.BASE_CD='CD007'
				  left outer join tb_common f on a.PERIOD_UNIT=f.CODE and f.BASE_CD='CD010'
				  left outer join tb_user g on a.SELLER_SQ=g.USER_SQ
				  left outer join tb_user h on a.TRAINER_SQ=h.USER_SQ
			WHERE UV_SQ=:UV_SQ
		");
		$database->bind(':UV_SQ', $UV_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$uservoucherlist = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($uservoucherlist);
		break;			
	case 'getUserVoucherListSch':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		
		$RESERV_USER_SQ = getAnyParameter("USER_SQ","");
		$START_DT = getAnyParameter("START_DT","");
		
		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "스케쥴러";
		$SUBCATEGORY = "스케쥴 조회";
		$ACTION = $CENTER_SQ . " 스케쥴을 조회하였습니다.";
		$IP = getClientIPv4();
		//tb_voucher
		//VOUCHER_SQ,CENTER_SQ,CATEGORY_SQ,SUBCATEGORY_SQ,VOUCHER_NAME,VOUCHER_TYPE,USE_TYPE,PERIOD_TYPE,PERIOD,COUNT_TYPE,COUNT,PRICE,SURTAX_TYPE,DISCOUNT_TYPE,DISCOUNT_RATIO,DISCOUNT_AMOUNT,SELLINGPRICE

		$database->prepare("
			SELECT UV_SQ,MEMBER_SQ,VOUCHER_SQ,VOUCHER_NAME,VOUCHER_TYPE,b.DESCRIPTION as VOUCHER_TYPE_NAME,USE_TYPE,c.DESCRIPTION as USE_TYPE_NAME,
					PERIOD_TYPE,d.DESCRIPTION as PERIOD_TYPE_NAME,PERIOD,PERIOD_UNIT,f.DESCRIPTION as PERIOD_UNIT_NAME,
					COUNT_TYPE,e.DESCRIPTION as COUNT_TYPE_NAME,COUNT,ENTERLIMIT_DAY,ENTERLIMIT_WEEK,USEDCOUNT,
					(SELECT COUNT(*) FROM tb_reservation WHERE USER_SQ=a.MEMBER_SQ and UV_SQ=a.UV_SQ and RESERV_STATUS=1) RESERV_COUNT,
					USE_STATUS,USE_STARTDATE,USE_LASTDATE,SELLER_SQ, g.USER_NM as SELLER_NM,TRAINER_SQ, h.USER_NM as TRAINER_NM
 			FROM tb_user_voucher a
				  left outer join tb_common b on a.VOUCHER_TYPE=b.CODE and b.BASE_CD='CD004'
				  left outer join tb_common c on a.USE_TYPE=c.CODE and c.BASE_CD='CD005'
				  left outer join tb_common d on a.PERIOD_TYPE=d.CODE and d.BASE_CD='CD006'
				  left outer join tb_common e on a.COUNT_TYPE=e.CODE and e.BASE_CD='CD007'
				  left outer join tb_common f on a.PERIOD_UNIT=f.CODE and f.BASE_CD='CD010'
				  left outer join tb_user g on g.USER_SQ=a.SELLER_SQ
				  left outer join tb_user h on h.USER_SQ=a.TRAINER_SQ
			WHERE MEMBER_SQ=:RESERV_USER_SQ and VOUCHER_TYPE=1 and USE_TYPE=2 and ((COUNT_TYPE=2 AND COUNT>USEDCOUNT) OR (COUNT_TYPE=1)) 
			AND USE_STARTDATE<=:START_DT AND USE_LASTDATE>=:START_DT2 AND USE_STATUS=2
		");
		$database->bind(':RESERV_USER_SQ', $RESERV_USER_SQ);
		$database->bind(':START_DT', $START_DT);
		$database->bind(':START_DT2', $START_DT);
		$database->execute();

		$rows = $database->fetchAll();
		$uservoucherlist = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($uservoucherlist);
		break;	
	case 'execUserPersonalScheduleModify':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$RESERV_SQ = getAnyParameter("RESERV_SQ","");		
		$MEMO = getAnyParameter("MEMO","");
				
		// 기본값 설정		
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$GROUP = 4;
		$CATEGORY = 41;
		$MEMBER_SQ = Get_SingleField("tb_reservation", "USER_SQ", "RESERV_SQ", $RESERV_SQ, "", $database);
		$ACTION = Get_UserInfo($MEMBER_SQ,$database)."회원님의 ".Get_ReservInfo($RESERV_SQ,$database)." 예약 메모가 변경되었습니다.";
		$IP = getClientIPv4();
		
		$database->prepare("
			UPDATE tb_reservation SET MEMO=:MEMO WHERE RESERV_SQ=:RESERV_SQ
		");
		$database->bind(':MEMO', $MEMO);
		$database->bind(':RESERV_SQ', $RESERV_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		
		$database->prepare("
			select a.RESERV_SQ,a.CENTER_SQ,a.USER_SQ,a.MANAGER_SQ,a.UV_SQ,a.RESERV_STATUS,a.RESERV_DT,a.START_TIME,a.END_TIME,a.MEMO,
					b.USER_NM, c.USER_NM as MANAGER_NAME, d.DESCRIPTION as RESERV_STATUS_NAME			
			from tb_reservation a  left outer join tb_user b on a.USER_SQ=b.USER_SQ
				  left outer join tb_user c on a.MANAGER_SQ=c.USER_SQ
				  left outer join tb_common d on a.RESERV_STATUS=d.CODE and d.BASE_CD='CD015'
			where a.RESERV_SQ=:RESERV_SQ order by a.RESERV_SQ desc LIMIT 1
		");
		$database->bind(':RESERV_SQ', $RESERV_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$reservInfo = json_encode($rows);

		// 로그 저장
		insert_Log_History($CENTER_SQ,$USER_SQ,$MEMBER_SQ, $DEVICE_SQ, $IP, $GROUP, $CATEGORY, $ACTION,$database);

		exit($reservInfo);
		break;	

	case 'execUnRegUserPersonalScheduleSave':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$START_DT = getAnyParameter("START_DT","");		
		$USER_NM = getAnyParameter("USER_NM","");		
		$PHONE_NO = getAnyParameter("PHONE_NO","");		
		$GENDER = getAnyParameter("GENDER","");
		$MANAGER_SQ = getAnyParameter("MANAGER_SQ","");
		$VOUCHER_SQ = getAnyParameter("VOUCHER_SQ","");
		$START_TIME = getAnyParameter("START_TIME","");
		$END_TIME = getAnyParameter("END_TIME","");
		$MEMO = getAnyParameter("MEMO","");
		
		// 기본값 설정	
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$GROUP = 4;
		$CATEGORY = 41;
		$IP = getClientIPv4();
		
		// 사용자 등록 
		$ID_TEMP = str_replace("-","", $PHONE_NO);
		$USERID = substr($ID_TEMP,strlen($ID_TEMP)-8,8);
		$USERPWD = substr($USERID,strlen($USERID)-4,4);
		$ID_EXIST = checkNEWUSER($USER_NM,$USERID, $database);
		
		if ($ID_EXIST==1)
		{
			$response_array["result"] = 'id exist';
			exit(json_encode($response_array));
		} else if ($ID_EXIST==2) {
			
			$response_array["result"] = 'user exist';
			exit(json_encode($response_array));
		}
		$database->prepare("
			INSERT tb_user (USERID,PWD_ENCRYPTED,USER_NM,PHONE_NO,GENDER,CENTER_SQ, TRAINER, GRADE, ISUSE, LAST_DT, REG_DT)
			values ( :USERID, SHA2(:PWD_ENCRYPTED, 256),:USER_NM, :PHONE_NO, :GENDER, :CENTER_SQ, :MANAGER_SQ, 1, 1, now(), now() )
		");
		$database->bind(':USERID', $USERID);
		$database->bind(':PWD_ENCRYPTED', $USERPWD );
		$database->bind(':USER_NM', $USER_NM);
		$database->bind(':PHONE_NO', $PHONE_NO);
		$database->bind(':GENDER', $GENDER);
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':MANAGER_SQ', $MANAGER_SQ);
		$database->execute();
		error_log('rowCount='.$database->rowCount());
		error_log('USERPWD= '.$USERPWD );
		
		if ($database->rowCount() == 0) {
			$response_array["result"] = 'fail';
			exit(json_encode($response_array));
		}
		else {
			$database->prepare("
				select LAST_INSERT_ID() USER_SQ;
			");
			$database->execute();
			$row = $database->fetch();

			$RESERV_USER_SQ = $row["USER_SQ"];
		}
		$response_array["result"] = 'Success';
		
		// USER VOUCHER CREATE
		$database->prepare("
			INSERT INTO tb_user_voucher (CENTER_SQ,MEMBER_SQ,VOUCHER_SQ,VOUCHER_NAME,VOUCHER_TYPE,USE_TYPE,PERIOD_TYPE,PERIOD,PERIOD_UNIT,COUNT_TYPE,COUNT,ENTERLIMIT_DAY,
										ENTERLIMIT_WEEK,USEDCOUNT,USE_STATUS,USE_STARTDATE,USE_LASTDATE,SELLER_SQ,TRAINER_SQ,CREATEDID,CREATEDDT,MODIFIEDDT) 
			SELECT CENTER_SQ,:MEMBER_SQ,VOUCHER_SQ,VOUCHER_NAME,VOUCHER_TYPE,USE_TYPE,PERIOD_TYPE,PERIOD,PERIOD_UNIT,
				COUNT_TYPE,COUNT,ENTERLIMIT_DAY,ENTERLIMIT_WEEK,0,2,now(),:USE_LASTDATE,:SELLER_SQ,:TRAINER_SQ,:CREATEDID,now(),now() 
			FROM tb_voucher	WHERE CENTER_SQ=:CENTER_SQ and VOUCHER_SQ=:VOUCHER_SQ and USEYN=1
		");
		$database->bind(':MEMBER_SQ', $RESERV_USER_SQ);
		$database->bind(':USE_LASTDATE', $START_DT);
		$database->bind(':SELLER_SQ', $USER_SQ);
		$database->bind(':TRAINER_SQ', $USER_SQ);
		$database->bind(':CREATEDID', $USER_SQ);
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':VOUCHER_SQ', $VOUCHER_SQ);
		$database->execute();

		$response_array["result"] = 'Success';
//echo("proc 2");
		
		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		
		// uv_sq 취득 
		$database->prepare("
			select UV_SQ from tb_user_voucher where CENTER_SQ=:CENTER_SQ and VOUCHER_SQ=:VOUCHER_SQ and MEMBER_SQ=:MEMBER_SQ 
			order by UV_SQ desc LIMIT 1
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':VOUCHER_SQ', $VOUCHER_SQ);
		$database->bind(':MEMBER_SQ', $RESERV_USER_SQ);
		$database->execute();
		
		$response_array["result"] = 'Success';
//echo("proc 3");
		
		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		$row = $database->fetch();
		$UV_SQ = $row["UV_SQ"];
		
		$response_array["UV_SQ"] = $UV_SQ;

		//tb_payment
		//UV_SQ,VOUCHER_SQ,CENTER_SQ,MEMBER_SQ,ORIGINAL_PRICE,DISCOUNT_TYPE,DISCOUNT_RATIO,DISCOUNT_AMOUNT,FINAL_PRICE,PAYED_AMOUNT,
		//						PAY_STATUS,PAY_MEMO,PAYED_STARTDATE,PAYED_LASTDATE,SELLER_SQ,CREATEDID,CREATEDDT,
		$database->prepare("
			INSERT INTO tb_payment (UV_SQ,VOUCHER_SQ,CENTER_SQ,MEMBER_SQ,ORIGINAL_PRICE,DISCOUNT_TYPE,DISCOUNT_RATIO,DISCOUNT_AMOUNT,SELLINGPRICE,PAYED_AMOUNT,
								PAY_STATUS,PAY_MEMO,PAYED_STARTDATE,PAYED_LASTDATE,SELLER_SQ,CREATEDID,CREATEDDT,MODIFIEDDT) 
			SELECT :UV_SQ,VOUCHER_SQ,CENTER_SQ,:MEMBER_SQ,PRICE,1,0,0,PRICE,PRICE,
								2,'',now() ,now() , :SELLER_SQ,:CREATEDID,now() ,now()
			FROM tb_voucher	WHERE CENTER_SQ=:CENTER_SQ and VOUCHER_SQ=:VOUCHER_SQ and USEYN=1
		");
		$database->bind(':UV_SQ', $UV_SQ);
		$database->bind(':MEMBER_SQ', $RESERV_USER_SQ);
		$database->bind(':SELLER_SQ', $USER_SQ);
		$database->bind(':CREATEDID', $USER_SQ);
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':VOUCHER_SQ', $VOUCHER_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		
		// 예약 등록 
		$database->prepare("
			INSERT tb_reservation (CENTER_SQ,USER_SQ,MANAGER_SQ,UV_SQ,RESERV_STATUS,RESERV_DT,START_TIME,END_TIME,MEMO, CREATEDBY, CREATED) VALUES
			(:CENTER_SQ,:RESERV_USER_SQ,:MANAGER_SQ,:UV_SQ,1,:START_DT,:START_TIME,:END_TIME,:MEMO,:CREATEDBY,now())
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':RESERV_USER_SQ', $RESERV_USER_SQ);
		$database->bind(':MANAGER_SQ', $MANAGER_SQ);
		$database->bind(':UV_SQ', $UV_SQ);
		$database->bind(':START_DT', $START_DT);
		$database->bind(':START_TIME', $START_TIME);
		$database->bind(':END_TIME', $END_TIME);
		$database->bind(':MEMO', $MEMO);
		$database->bind(':CREATEDBY', $USER_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		
		// 출결 히스토리 테이블 
		$USED_COUNT = 0;
		$ATTENDANCE = 5; //예약  
		$ALLOWANCE = 0;
		$RESERV_SQ = Get_SingleField('tb_reservation', 'MAX(RESERV_SQ)', 'USER_SQ', $RESERV_USER_SQ, '',$database);
		$CLASS_DESC = Get_ReservInfo($RESERV_SQ,$database);
		$database->prepare("
			INSERT tb_user_voucher_history (CENTER_SQ,USER_SQ,MANAGER_SQ,UV_SQ,VOUCHER_TYPE,ATTENDANCE_TYPE,USED_COUNT,CLASS_RESERV_SQ,RESERV_SQ,ALLOWANCE,DESCRIPTION,CREATE_DT)
			VALUES (:CENTER_SQ,:MEMBER_SQ,:UV_SQ,:MANAGER_SQ,:VOUCHER_TYPE,:ATTENDANCE_TYPE,:USED_COUNT,:CLASS_RESERV_SQ,:RESERV_SQ,:ALLOWANCE,:DESCRIPTION,NOW())
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':MEMBER_SQ', $RESERV_USER_SQ);
		$database->bind(':MANAGER_SQ', $MANAGER_SQ);
		$database->bind(':UV_SQ', $UV_SQ);
		$database->bind(':VOUCHER_TYPE', 1);
		$database->bind(':ATTENDANCE_TYPE', $ATTENDANCE);
		$database->bind(':USED_COUNT', $USED_COUNT); // 만약 2개 이상의 이용권 차감하는 항목이 발생하면 수정해야한다.
		$database->bind(':CLASS_RESERV_SQ', 0);
		$database->bind(':RESERV_SQ', $RESERV_SQ);
		$database->bind(':ALLOWANCE', $ALLOWANCE);
		$database->bind(':DESCRIPTION', $CLASS_DESC);
		$database->execute();

		// 예약 정보 취득 
		$database->prepare("
			select a.RESERV_SQ,a.CENTER_SQ,a.USER_SQ,a.MANAGER_SQ,a.UV_SQ,a.RESERV_STATUS,a.RESERV_DT,a.START_TIME,a.END_TIME,a.MEMO,
					b.USER_NM, c.USER_NM as MANAGER_NAME, d.DESCRIPTION as RESERV_STATUS_NAME			
			from tb_reservation a  left outer join tb_user b on a.USER_SQ=b.USER_SQ
				  left outer join tb_user c on a.MANAGER_SQ=c.USER_SQ
				  left outer join tb_common d on a.RESERV_STATUS=d.CODE and d.BASE_CD='CD015'
			where a.CENTER_SQ=:CENTER_SQ and a.RESERV_STATUS=1 order by a.RESERV_SQ desc LIMIT 1
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$reservInfo = json_encode($rows);

		// 로그 저장
		$ACTION = Get_UserInfo($RESERV_USER_SQ,$database)."회원님의 ".$CLASS_DESC." 예약의 메모가 변경되었습니다.";
		insert_Log_History($CENTER_SQ,$USER_SQ,$RESERV_USER_SQ, $DEVICE_SQ, $IP, $GROUP, $CATEGORY, $ACTION,$database);

		exit($reservInfo);
		break;	
		
	case 'execUserPersonalScheduleSave':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$START_DT = getAnyParameter("START_DT","");		
		$RESERV_USER_SQ = getAnyParameter("USER_SQ","");		
		$MANAGER_SQ = getAnyParameter("MANAGER_SQ","");
		$UV_SQ = getAnyParameter("UV_SQ","");
		$START_TIME = getAnyParameter("START_TIME","");
		$END_TIME = getAnyParameter("END_TIME","");
		$MEMO = getAnyParameter("MEMO","");
		
		// 기본값 설정	
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$GROUP = 4;
		$CATEGORY = 41;
		$IP = getClientIPv4();
		
		$database->prepare("
			INSERT tb_reservation (CENTER_SQ,USER_SQ,MANAGER_SQ,UV_SQ,RESERV_STATUS,RESERV_DT,START_TIME,END_TIME,MEMO, CREATEDBY, CREATED) VALUES
			(:CENTER_SQ,:RESERV_USER_SQ,:MANAGER_SQ,:UV_SQ,1,:START_DT,:START_TIME,:END_TIME,:MEMO,:CREATEDBY,now())
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':RESERV_USER_SQ', $RESERV_USER_SQ);
		$database->bind(':MANAGER_SQ', $MANAGER_SQ);
		$database->bind(':UV_SQ', $UV_SQ);
		$database->bind(':START_DT', $START_DT);
		$database->bind(':START_TIME', $START_TIME);
		$database->bind(':END_TIME', $END_TIME);
		$database->bind(':MEMO', $MEMO);
		$database->bind(':CREATEDBY', $USER_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
				
		// 출결 히스토리 테이블 
		$USED_COUNT = 0;
		$ATTENDANCE = 5; //예약  
		$ALLOWANCE = 0;
		$RESERV_SQ = Get_SingleField('tb_reservation', 'MAX(RESERV_SQ)', 'USER_SQ', $RESERV_USER_SQ, '',$database);
		$CLASS_DESC = Get_ReservInfo($RESERV_SQ,$database);
		$database->prepare("
			INSERT tb_user_voucher_history (CENTER_SQ,USER_SQ,MANAGER_SQ,UV_SQ,VOUCHER_TYPE,ATTENDANCE_TYPE,USED_COUNT,CLASS_RESERV_SQ,RESERV_SQ,ALLOWANCE,DESCRIPTION,CREATE_DT)
			VALUES (:CENTER_SQ,:MEMBER_SQ,:MANAGER_SQ,:UV_SQ,:VOUCHER_TYPE,:ATTENDANCE_TYPE,:USED_COUNT,:CLASS_RESERV_SQ,:RESERV_SQ,:ALLOWANCE,:DESCRIPTION,NOW())
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':MEMBER_SQ', $RESERV_USER_SQ);
		$database->bind(':MANAGER_SQ', $MANAGER_SQ);
		$database->bind(':UV_SQ', $UV_SQ);
		$database->bind(':VOUCHER_TYPE', 1);
		$database->bind(':ATTENDANCE_TYPE', $ATTENDANCE);
		$database->bind(':USED_COUNT', $USED_COUNT); // 만약 2개 이상의 이용권 차감하는 항목이 발생하면 수정해야한다.
		$database->bind(':CLASS_RESERV_SQ', 0);
		$database->bind(':RESERV_SQ', $RESERV_SQ);
		$database->bind(':ALLOWANCE', $ALLOWANCE);
		$database->bind(':DESCRIPTION', $CLASS_DESC);
		$database->execute();

		$database->prepare("
			select a.RESERV_SQ,a.CENTER_SQ,a.USER_SQ,a.MANAGER_SQ,a.UV_SQ,a.RESERV_STATUS,a.RESERV_DT,a.START_TIME,a.END_TIME,a.MEMO,
					b.USER_NM, c.USER_NM as MANAGER_NAME, d.DESCRIPTION as RESERV_STATUS_NAME			
			from tb_reservation a  left outer join tb_user b on a.USER_SQ=b.USER_SQ
				  left outer join tb_user c on a.MANAGER_SQ=c.USER_SQ
				  left outer join tb_common d on a.RESERV_STATUS=d.CODE and d.BASE_CD='CD015'
			where a.CENTER_SQ=:CENTER_SQ and a.RESERV_STATUS=1 order by a.RESERV_SQ desc LIMIT 1
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$reservInfo = json_encode($rows);

		// 로그 저장
		$ACTION = Get_UserInfo($RESERV_USER_SQ,$database)."회원님의 ".$CLASS_DESC." 예약을 하셨습니다.";
		insert_Log_History($CENTER_SQ,$USER_SQ,$RESERV_USER_SQ, $DEVICE_SQ, $IP, $GROUP, $CATEGORY, $ACTION,$database);

		exit($reservInfo);
		break;	
		
	case 'getUserPersonalWeeklyScheduleList':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		
		$START_DT = getAnyParameter("START_DT","");
		$END_DT = getAnyParameter("END_DT","");
		$MANAGER_SQ = getAnyParameter("MANAGER_SQ","");

		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "스케쥴러";
		$SUBCATEGORY = "스케쥴 조회";
		$ACTION = $CENTER_SQ . " 스케쥴을 조회하였습니다.";
		$IP = getClientIPv4();

		// DB 조회
		$database->prepare("
			select a.RESERV_SQ,a.CENTER_SQ,a.USER_SQ,a.MANAGER_SQ,a.UV_SQ,e.VOUCHER_NAME,a.RESERV_STATUS,a.RESERV_DT,a.START_TIME,a.END_TIME,a.MEMO,
					b.USER_NM, c.USER_NM as MANAGER_NAME, d.DESCRIPTION as RESERV_STATUS_NAME			
			from tb_reservation a  left outer join tb_user b on a.USER_SQ=b.USER_SQ
				  left outer join tb_user c on a.MANAGER_SQ=c.USER_SQ
				  left outer join tb_common d on a.RESERV_STATUS=d.CODE and d.BASE_CD='CD015'
				  left outer join tb_user_voucher e on a.UV_SQ=e.UV_SQ
			where a.CENTER_SQ=:CENTER_SQ and a.RESERV_STATUS=1 and a.RESERV_DT>=:START_DT and a.RESERV_DT<=:END_DT and a.MANAGER_SQ=:MANAGER_SQ order by a.CREATED desc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':START_DT', $START_DT);
		$database->bind(':END_DT', $END_DT);
		$database->bind(':MANAGER_SQ', $MANAGER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$schedulelist = json_encode($rows);

		// 휴일취득
		$database->prepare("
			select HOLIDAY, HOLIDAY_NAME, MANAGER_SQ FROM tb_holiday where HOLIDAY>=:START_DT and HOLIDAY<=:END_DT and CENTER_SQ=:CENTER_SQ
		");
		$database->bind(':START_DT', $START_DT);
		$database->bind(':END_DT', $END_DT);
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();
		
		$rows = $database->fetchAll();
		$holidaylist = json_encode($rows);
		
		// DB 조회
		$database->prepare("
			select CENTER_SQ,RESERV_STATUS,COUNT(*)		as CNT
			from tb_reservation
			where CENTER_SQ=:CENTER_SQ and RESERV_DT>=:START_DT and RESERV_DT<=:END_DT and MANAGER_SQ=:MANAGER_SQ
			group by CENTER_SQ, RESERV_STATUS
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':START_DT', $START_DT);
		$database->bind(':END_DT', $END_DT);
		$database->bind(':MANAGER_SQ', $MANAGER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$schedulestatistics = json_encode($rows);
		
		//tb_voucher
		//VOUCHER_SQ,CENTER_SQ,CATEGORY_SQ,SUBCATEGORY_SQ,VOUCHER_NAME,VOUCHER_TYPE,USE_TYPE,PERIOD_TYPE,PERIOD,COUNT_TYPE,COUNT,PRICE,SURTAX_TYPE,DISCOUNT_TYPE,DISCOUNT_RATIO,DISCOUNT_AMOUNT,SELLINGPRICE

		$database->prepare("
			SELECT UV_SQ,MEMBER_SQ,VOUCHER_SQ,VOUCHER_NAME,VOUCHER_TYPE,b.DESCRIPTION as VOUCHER_TYPE_NAME,USE_TYPE,c.DESCRIPTION as USE_TYPE_NAME,
					PERIOD_TYPE,d.DESCRIPTION as PERIOD_TYPE_NAME,PERIOD,PERIOD_UNIT,f.DESCRIPTION as PERIOD_UNIT_NAME,
					COUNT_TYPE,e.DESCRIPTION as COUNT_TYPE_NAME,COUNT,ENTERLIMIT_DAY,ENTERLIMIT_WEEK,USEDCOUNT,
					(SELECT COUNT(*) FROM tb_reservation WHERE USER_SQ=a.MEMBER_SQ and UV_SQ=a.UV_SQ and RESERV_STATUS=1) RESERV_COUNT,
					USE_STATUS,USE_STARTDATE,USE_LASTDATE,SELLER_SQ, g.USER_NM as SELLER_NM,TRAINER_SQ, h.USER_NM as TRAINER_NM
 			FROM tb_user_voucher a
				  left outer join tb_common b on a.VOUCHER_TYPE=b.CODE and b.BASE_CD='CD004'
				  left outer join tb_common c on a.USE_TYPE=c.CODE and c.BASE_CD='CD005'
				  left outer join tb_common d on a.PERIOD_TYPE=d.CODE and d.BASE_CD='CD006'
				  left outer join tb_common e on a.COUNT_TYPE=e.CODE and e.BASE_CD='CD007'
				  left outer join tb_common f on a.PERIOD_UNIT=f.CODE and f.BASE_CD='CD010'
				  left outer join tb_common g on g.USER_SQ and a.SELLER_SQ
				  left outer join tb_common h on h.USER_SQ and a.TRAINER_SQ
			WHERE CENTER_SQ=:CENTER_SQ and VOUCHER_TYPE=1 and USE_TYPE=2 and ((COUNT_TYPE=2 AND COUNT>0) OR (COUNT_TYPE=1)) AND USE_STARTDATE<=:START_DT
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':START_DT', $START_DT);
		$database->execute();

		$rows = $database->fetchAll();
		$uservoucherlist = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($schedulelist.'|'.$holidaylist.'|'.$schedulestatistics);
		break;	
						
	case 'getUserPersonalScheduleList':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		
		$START_DT = getAnyParameter("START_DT","");

		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "스케쥴러";
		$SUBCATEGORY = "스케쥴 조회";
		$ACTION = $CENTER_SQ . " 스케쥴을 조회하였습니다.";
		$IP = getClientIPv4();

		// DB 조회
		$database->prepare("
			select a.RESERV_SQ,a.CENTER_SQ,a.USER_SQ,a.MANAGER_SQ,a.UV_SQ,e.VOUCHER_NAME,a.RESERV_STATUS,a.RESERV_DT,a.START_TIME,a.END_TIME,a.MEMO,
					b.USER_NM, c.USER_NM as MANAGER_NAME, d.DESCRIPTION as RESERV_STATUS_NAME			
			from tb_reservation a  left outer join tb_user b on a.USER_SQ=b.USER_SQ
				  left outer join tb_user c on a.MANAGER_SQ=c.USER_SQ
				  left outer join tb_common d on a.RESERV_STATUS=d.CODE and d.BASE_CD='CD015'
				  left outer join tb_user_voucher e on a.UV_SQ=e.UV_SQ
			where a.CENTER_SQ=:CENTER_SQ and a.RESERV_STATUS!=2 and a.RESERV_DT=:START_DT order by a.CREATED desc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':START_DT', $START_DT);
		$database->execute();

		$rows = $database->fetchAll();
		$schedulelist = json_encode($rows);

		// 휴일취득
		$database->prepare("
			select HOLIDAY, HOLIDAY_NAME,MANAGER_SQ FROM tb_holiday where HOLIDAY=:START_DT and CENTER_SQ=:CENTER_SQ
		");
		$database->bind(':START_DT', $START_DT);
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();
		
		$rows = $database->fetchAll();
		$holidaylist = json_encode($rows);
		
		// DB 조회
		$database->prepare("
			select CENTER_SQ,RESERV_STATUS,COUNT(*)	as CNT
			from tb_reservation
			where CENTER_SQ=:CENTER_SQ and RESERV_DT=:START_DT
			group by CENTER_SQ, RESERV_STATUS
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':START_DT', $START_DT);
		$database->execute();

		$rows = $database->fetchAll();
		$schedulestatistics = json_encode($rows);
		
		//tb_voucher
		//VOUCHER_SQ,CENTER_SQ,CATEGORY_SQ,SUBCATEGORY_SQ,VOUCHER_NAME,VOUCHER_TYPE,USE_TYPE,PERIOD_TYPE,PERIOD,COUNT_TYPE,COUNT,PRICE,SURTAX_TYPE,DISCOUNT_TYPE,DISCOUNT_RATIO,DISCOUNT_AMOUNT,SELLINGPRICE

		$database->prepare("
			SELECT UV_SQ,MEMBER_SQ,VOUCHER_SQ,VOUCHER_NAME,VOUCHER_TYPE,b.DESCRIPTION as VOUCHER_TYPE_NAME,USE_TYPE,c.DESCRIPTION as USE_TYPE_NAME,
					PERIOD_TYPE,d.DESCRIPTION as PERIOD_TYPE_NAME,PERIOD,PERIOD_UNIT,f.DESCRIPTION as PERIOD_UNIT_NAME,
					COUNT_TYPE,e.DESCRIPTION as COUNT_TYPE_NAME,COUNT,ENTERLIMIT_DAY,ENTERLIMIT_WEEK,USEDCOUNT,
					(SELECT COUNT(*) FROM tb_reservation WHERE USER_SQ=a.MEMBER_SQ and UV_SQ=a.UV_SQ and RESERV_STATUS=1) RESERV_COUNT,
					USE_STATUS,USE_STARTDATE,USE_LASTDATE,SELLER_SQ, g.USER_NM as SELLER_NM,TRAINER_SQ, h.USER_NM as TRAINER_NM
 			FROM tb_user_voucher a
				  left outer join tb_common b on a.VOUCHER_TYPE=b.CODE and b.BASE_CD='CD004'
				  left outer join tb_common c on a.USE_TYPE=c.CODE and c.BASE_CD='CD005'
				  left outer join tb_common d on a.PERIOD_TYPE=d.CODE and d.BASE_CD='CD006'
				  left outer join tb_common e on a.COUNT_TYPE=e.CODE and e.BASE_CD='CD007'
				  left outer join tb_common f on a.PERIOD_UNIT=f.CODE and f.BASE_CD='CD010'
				  left outer join tb_user g on g.USER_SQ=a.SELLER_SQ
				  left outer join tb_user h on h.USER_SQ=a.TRAINER_SQ
			WHERE CENTER_SQ=:CENTER_SQ and VOUCHER_TYPE=1 and USE_TYPE=2 and ((COUNT_TYPE=2 AND COUNT>0) OR (COUNT_TYPE=1)) AND USE_STARTDATE<=:START_DT
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':START_DT', $START_DT);
		$database->execute();

		$rows = $database->fetchAll();
		$uservoucherlist = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($schedulelist.'|'.$holidaylist.'|'.$schedulestatistics);
		break;	
			
	case 'getScheduleInitInfo':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "스케쥴러";
		$SUBCATEGORY = "스케쥴 조회";
		$ACTION = $CENTER_SQ . " 스케쥴을 조회하였습니다.";
		$IP = getClientIPv4();

		// 여기에 지나간 스케쥴 출결 처리 루틴을 넣을 것.
		
		
		// DB 조회
		$database->prepare("
			select a.USER_SQ,a.CENTER_SQ,a.USER_NM,a.PHONE_NO,a.EMAIL,a.BIRTH_DT, a.REG_DT, (select REG_DT from bs_measurement where USER_SQ=a.USER_SQ order by REG_DT desc LIMIT 1) as MEAS_DATE
					,a.TRAINER, b.USER_NM TRAINER_NM, a.ISUSE
					from tb_user a  left outer join tb_user b on a.TRAINER=b.USER_SQ
					where a.CENTER_SQ=:CENTER_SQ and a.GRADE=1 order by a.REG_DT desc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$memberlist = json_encode($rows);

		// 최종 결과 취득 
		$database->prepare("
			select a.USER_SQ,a.CENTER_SQ,a.USER_NM,a.PHONE_NO,a.ADDRESS,a.EMAIL,a.BIRTH_DT, a.REG_DT, a.GRADE,
						a.WORKCATEGORY, a.WORKTYPE, a.WORKSTARTDATE, a.WORKENDDATE, a.WORKSTATUS, a.ISUSE
					from tb_user a  left outer join tb_user b on a.TRAINER=b.USER_SQ
					where a.CENTER_SQ=:CENTER_SQ and a.ISMANAGER=1 and a.ISUSE=1 order by a.REG_DT desc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();
		
		$rows = $database->fetchAll();
		$managerlist = json_encode($rows);
		
		//tb_voucher
		//VOUCHER_SQ,CENTER_SQ,CATEGORY_SQ,SUBCATEGORY_SQ,VOUCHER_NAME,VOUCHER_TYPE,USE_TYPE,PERIOD_TYPE,PERIOD,COUNT_TYPE,COUNT,PRICE,SURTAX_TYPE,DISCOUNT_TYPE,DISCOUNT_RATIO,DISCOUNT_AMOUNT,SELLINGPRICE

		$database->prepare("
			SELECT VOUCHER_SQ,CENTER_SQ,CATEGORY_SQ,SUBCATEGORY_SQ,VOUCHER_NAME,VOUCHER_TYPE,USE_TYPE,PERIOD_TYPE,PERIOD,PERIOD_UNIT,
					COUNT_TYPE,COUNT,ENTERLIMIT_DAY,ENTERLIMIT_WEEK,PRICE,SURTAX_TYPE,DISCOUNT_TYPE,DISCOUNT_RATIO,DISCOUNT_AMOUNT,SELLINGPRICE,
					REGID,REGDT,MODIFIEDID,MODIFIEDDT
 			FROM tb_voucher
			WHERE CENTER_SQ=:CENTER_SQ and PRICE=0 and USEYN=1
			ORDER BY CATEGORY_SQ asc , SUBCATEGORY_SQ asc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$freevoucherlist = json_encode($rows);

		$database->prepare("
			select COMMON_SQ,BASE_CD,CODE,NAME,DESCRIPTION
					from tb_common where BASE_CD='CD014' and CODE>0 order by CODE asc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$reservation_status_list = json_encode($rows);

		$database->prepare("
			SELECT RESERVSETTING_SQ,PSN_RESERV_TYPE,PSN_RESERV_TIME,PSN_MOD_TYPE,PSN_MOD_TIME,PSN_AUTO_ABSENCE,PSN_ABSENCE_TICKET,
					GRP_RESERV_TYPE,GRP_RESERV_TIME,GRP_MOD_TYPE,GRP_MOD_TIME,GRP_AUTO_ABSENCE,GRP_ABSENCE_TICKET,CREATEDBY,CREATED
 			FROM tb_reservation_setting
			WHERE CENTER_SQ=:CENTER_SQ AND CREATED <=now()
			ORDER BY CREATED DESC LIMIT 1
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$reservation_setting_list = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($memberlist.'|'.$managerlist.'|'.$freevoucherlist.'|'.$reservation_setting_list.'|'.$reservation_status_list);
		break;	
		
	case 'getUserVoucherInfo':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$UV_SQ = getAnyParameter("UV_SQ","");

		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "상품권 설정";
		$SUBCATEGORY = "상품권 조회";
		$ACTION = $CENTER_SQ . " 상품권을 조회하였습니다.";
		$IP = getClientIPv4();

		//tb_user_voucher
		//CENTER_SQ,MEMBER_SQ,VOUCHER_SQ,VOUCHER_NAME,VOUCHER_TYPE,USE_TYPE,PERIOD_TYPE,PERIOD,PERIOD_UNIT,COUNT_TYPE,COUNT,ENTERLIMIT_DAY,
		//								ENTERLIMIT_WEEK,USEDCOUNT,USE_STATUS,USE_STARTDATE,USE_LASTDATE,SELLER_SQ,CREATEDID,CREATEDDT
		//tb_payment
		//UV_SQ,VOUCHER_SQ,CENTER_SQ,MEMBER_SQ,ORIGINAL_PRICE,DISCOUNT_TYPE,DISCOUNT_RATIO,DISCOUNT_AMOUNT,FINAL_PRICE,PAYED_AMOUNT,
		//						PAY_STATUS,PAY_MEMO,PAYED_STARTDATE,PAYED_LASTDATE,SELLER_SQ,CREATEDID,CREATEDDT,
		//				INSERT INTO tb_payment_detail (PAY_SQ,UV_SQ,MEMBER_SQ,PAY_TYPE,PAY_AMOUNT,CREATEDID,CREATEDDT) VALUES
		$database->prepare("
			SELECT a.UV_SQ,b.PAY_SQ,a.MEMBER_SQ,c.USER_NM,c.PHONE_NO,a.VOUCHER_NAME,a.VOUCHER_TYPE,d.DESCRIPTION as VOUCHER_TYPE_NAME, a.USE_STATUS,b.PAY_STATUS,a.PERIOD_TYPE,a.USE_TYPE,a.PERIOD,a.PERIOD_UNIT,
					b.ORIGINAL_PRICE,b.DISCOUNT_AMOUNT,b.SELLINGPRICE,b.PAYED_AMOUNT,
					(SELECT COUNT(*) FROM tb_reservation WHERE USER_SQ=a.MEMBER_SQ and UV_SQ=a.UV_SQ and RESERV_STATUS=1) RESERV_COUNT,
					b.REFUND_AMOUNT,b.MODIFIEDDT,b.PAY_MEMO,b.SELLER_SQ,a.TRAINER_SQ, a.COUNT, a.USEDCOUNT, a.USE_STARTDATE, a.USE_LASTDATE
 			FROM tb_user_voucher a 
					inner join tb_payment b on a.UV_SQ=b.UV_SQ
					inner join tb_user c on a.MEMBER_SQ=c.USER_SQ
					inner join tb_common d on a.VOUCHER_TYPE=d.CODE and d.BASE_CD='CD004'
			WHERE a.UV_SQ=:UV_SQ
		");
				
		$database->bind(':UV_SQ', $UV_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$paymentinfo = json_encode($rows);

		//tb_payment_detail
		//PAY_SQ,UV_SQ,MEMBER_SQ,PAY_TYPE,PAY_AMOUNT,CREATEDID,CREATEDDT
		$database->prepare("
			SELECT PAYDETAIL_SQ,PAY_SQ,UV_SQ,MEMBER_SQ,PAY_TYPE,PAY_AMOUNT,FUND_TYPE,CREATEDID,CREATEDDT
 			FROM tb_payment_detail
			WHERE UV_SQ = :UV_SQ
			ORDER BY PAYDETAIL_SQ asc
		");
		$database->bind(':UV_SQ', $UV_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$paymentdetaillist = json_encode($rows);
		
		// 사용자 리스트 취득 
		$database->prepare("
			select a.USER_SQ,a.CENTER_SQ,a.USER_NM,a.PHONE_NO,a.ADDRESS,a.EMAIL,a.BIRTH_DT, a.REG_DT, a.GRADE,
						a.WORKCATEGORY, a.WORKTYPE, a.WORKSTARTDATE, a.WORKENDDATE, a.WORKSTATUS, a.ISUSE
					from tb_user a  left outer join tb_user b on a.TRAINER=b.USER_SQ
					where a.CENTER_SQ=:CENTER_SQ and a.ISMANAGER=0 order by a.REG_DT desc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();
		
		$rows = $database->fetchAll();
		$memberlist = json_encode($rows);

		// 최종 결과 취득 
		$database->prepare("
			select a.USER_SQ,a.CENTER_SQ,a.USER_NM,a.PHONE_NO,a.ADDRESS,a.EMAIL,a.BIRTH_DT, a.REG_DT, a.GRADE,
						a.WORKCATEGORY, a.WORKTYPE, a.WORKSTARTDATE, a.WORKENDDATE, a.WORKSTATUS, a.ISUSE
					from tb_user a  left outer join tb_user b on a.TRAINER=b.USER_SQ
					where a.CENTER_SQ=:CENTER_SQ and a.ISMANAGER=1 order by a.REG_DT desc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();
		
		$rows = $database->fetchAll();
		$managerlist = json_encode($rows);
		
		$usestatus = getCommonCode('CD014', $database);
		$paystatus = getCommonCode('CD011', $database);
		$fundtype = getCommonCode('CD012', $database);
		$paytype = getCommonCode('CD013', $database);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($paymentinfo.'|'.$paymentdetaillist.'|'.$managerlist.'|'.$memberlist.'|'.$usestatus.'|'.$paystatus.'|'.$fundtype.'|'.$paytype);
		break;	
		
	case 'getUserVoucherList':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$START_DT = getAnyParameter("START_DT","")." 00:00:00";
		$END_DT = getAnyParameter("END_DT","")." 23:59:59";;

		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "상품권 설정";
		$SUBCATEGORY = "상품권 조회";
		$ACTION = $CENTER_SQ . " 상품권을 조회하였습니다.";
		$IP = getClientIPv4();
		
		//이용상태 업데이트 
		$database->prepare("
			UPDATE tb_user_voucher set USE_STATUS=2 
			WHERE USE_STATUS=1 AND USE_STARTDATE<=now() AND UV_SQ in (SELECT UV_SQ FROM tb_payment where PAY_STATUS=2 AND CENTER_SQ=:CENTER_SQ)
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		//tb_user_voucher
		//CENTER_SQ,MEMBER_SQ,VOUCHER_SQ,VOUCHER_NAME,VOUCHER_TYPE,USE_TYPE,PERIOD_TYPE,PERIOD,PERIOD_UNIT,COUNT_TYPE,COUNT,ENTERLIMIT_DAY,
		//								ENTERLIMIT_WEEK,USEDCOUNT,USE_STATUS,USE_STARTDATE,USE_LASTDATE,SELLER_SQ,CREATEDID,CREATEDDT
		//tb_payment
		//UV_SQ,VOUCHER_SQ,CENTER_SQ,MEMBER_SQ,ORIGINAL_PRICE,DISCOUNT_TYPE,DISCOUNT_RATIO,DISCOUNT_AMOUNT,FINAL_PRICE,PAYED_AMOUNT,
		//						PAY_STATUS,PAY_MEMO,PAYED_STARTDATE,PAYED_LASTDATE,SELLER_SQ,CREATEDID,CREATEDDT,
		//				INSERT INTO tb_payment_detail (PAY_SQ,UV_SQ,MEMBER_SQ,PAY_TYPE,PAY_AMOUNT,CREATEDID,CREATEDDT) VALUES
		$database->prepare("
			SELECT a.UV_SQ,b.PAY_SQ,a.MEMBER_SQ,c.USER_NM,c.PHONE_NO,a.VOUCHER_NAME,a.USE_STATUS,b.PAY_STATUS,b.ORIGINAL_PRICE,b.DISCOUNT_AMOUNT,b.SELLINGPRICE,b.PAYED_AMOUNT,
					b.REFUND_AMOUNT,b.MODIFIEDDT,b.PAY_MEMO,b.SELLER_SQ,a.TRAINER_SQ
 			FROM tb_user_voucher a 
					inner join tb_payment b on a.UV_SQ=b.UV_SQ
					inner join tb_user c on a.MEMBER_SQ=c.USER_SQ
			WHERE a.CENTER_SQ=:CENTER_SQ and b.MODIFIEDDT>=:START_DT and b.MODIFIEDDT<=:END_DT
			ORDER BY MODIFIEDDT desc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':START_DT', $START_DT);
		$database->bind(':END_DT', $END_DT);
		$database->execute();

		$rows = $database->fetchAll();
		$paymentlist = json_encode($rows);

		//tb_payment_detail
		//PAY_SQ,UV_SQ,MEMBER_SQ,PAY_TYPE,PAY_AMOUNT,CREATEDID,CREATEDDT
		$database->prepare("
			SELECT PAYDETAIL_SQ,PAY_SQ,UV_SQ,MEMBER_SQ,PAY_TYPE,PAY_AMOUNT,FUND_TYPE,CREATEDID,CREATEDDT
 			FROM tb_payment_detail
			WHERE UV_SQ in (select UV_SQ from tb_user_voucher where CENTER_SQ=:CENTER_SQ) and PAY_SQ in (select PAY_SQ from tb_payment where MODIFIEDDT>=:START_DT and MODIFIEDDT<=:END_DT)
			ORDER BY PAYDETAIL_SQ asc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':START_DT', $START_DT);
		$database->bind(':END_DT', $END_DT);
		$database->execute();

		$rows = $database->fetchAll();
		$paymentdetaillist = json_encode($rows);

		// 최종 결과 취득 
		$database->prepare("
			select a.USER_SQ,a.CENTER_SQ,a.USER_NM,a.PHONE_NO,a.ADDRESS,a.EMAIL,a.BIRTH_DT, a.REG_DT, a.GRADE,
						a.WORKCATEGORY, a.WORKTYPE, a.WORKSTARTDATE, a.WORKENDDATE, a.WORKSTATUS, a.ISUSE
					from tb_user a  left outer join tb_user b on a.TRAINER=b.USER_SQ
					where a.CENTER_SQ=:CENTER_SQ and a.ISMANAGER=0 order by a.REG_DT desc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();
		
		$rows = $database->fetchAll();
		$memberlist = json_encode($rows);

		// 최종 결과 취득 
		$database->prepare("
			select a.USER_SQ,a.CENTER_SQ,a.USER_NM,a.PHONE_NO,a.ADDRESS,a.EMAIL,a.BIRTH_DT, a.REG_DT, a.GRADE,
						a.WORKCATEGORY, a.WORKTYPE, a.WORKSTARTDATE, a.WORKENDDATE, a.WORKSTATUS, a.ISUSE
					from tb_user a  left outer join tb_user b on a.TRAINER=b.USER_SQ
					where a.CENTER_SQ=:CENTER_SQ and a.ISMANAGER=1 and a.ISUSE=1 order by a.REG_DT desc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();
		
		$rows = $database->fetchAll();
		$managerlist = json_encode($rows);
		
		$usestatus = getCommonCode('CD014', $database);
		$paystatus = getCommonCode('CD011', $database);
		$fundtype = getCommonCode('CD012', $database);
		$paytype = getCommonCode('CD013', $database);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($paymentlist.'|'.$paymentdetaillist.'|'.$memberlist.'|'.$managerlist.'|'.$usestatus.'|'.$paystatus.'|'.$fundtype.'|'.$paytype);
		break;	
		
	case 'execMemoModify':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$UV_SQ = getAnyParameter("UV_SQ","");
		$PAY_MEMO = getAnyParameter("PAY_MEMO","");

		// 기본값 설정		
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$GROUP = 3;
		$CATEGORY = 37;
		$IP = getClientIPv4();
		
		//tb_payment
		//UV_SQ,VOUCHER_SQ,CENTER_SQ,MEMBER_SQ,ORIGINAL_PRICE,DISCOUNT_TYPE,DISCOUNT_RATIO,DISCOUNT_AMOUNT,FINAL_PRICE,PAYED_AMOUNT,
		//						PAY_STATUS,PAY_MEMO,PAYED_STARTDATE,PAYED_LASTDATE,SELLER_SQ,CREATEDID,CREATEDDT,
		$database->prepare("
			UPDATE tb_payment SET PAY_MEMO=:PAY_MEMO
			WHERE UV_SQ=:UV_SQ
		");
		$database->bind(':PAY_MEMO', $PAY_MEMO);
		$database->bind(':UV_SQ', $UV_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}

		// 로그 저장
		$MEMBER_SQ = Get_SingleField("tb_payment", "MEMBER_SQ", "UV_SQ", $UV_SQ, "", $database);
		$ACTION = Get_UserInfo($MEMBER_SQ,$database)."회원님이 ".Get_VoucherInfo($UV_SQ,$database)."이용권의 결제메모를 수정하였습니다.";
		insert_Log_History($CENTER_SQ,$USER_SQ,$MEMBER_SQ, $DEVICE_SQ, $IP, $GROUP, $CATEGORY, $ACTION,$database);
		
		exit(json_encode($response_array));
		break;	
		
	case 'execPurchaseCardCancel':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$UV_SQ = getAnyParameter("UV_SQ","");
		$PAY_SQ = getAnyParameter("PAY_SQ","");
		$PAYDETAIL_SQ = getAnyParameter("PAYDETAIL_SQ","");
		$PAYED_AMOUNT_CARD = getAnyParameter("PAYED_AMOUNT_CARD","");
		// 기본값 설정		
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$GROUP = 3;
		$CATEGORY = 36;
		$IP = getClientIPv4();
		
		$totalpayment = $PAYED_AMOUNT_CARD;
		$USE_STATUS = 1;
		$response_array["UV_SQ"] = $UV_SQ;
		if ($SELLINGPRICE==$totalpayment) {
			$PAY_STATUS = 2;
		} else {
			$PAY_STATUS = 1;
		}

		//tb_payment
		//UV_SQ,VOUCHER_SQ,CENTER_SQ,MEMBER_SQ,ORIGINAL_PRICE,DISCOUNT_TYPE,DISCOUNT_RATIO,DISCOUNT_AMOUNT,FINAL_PRICE,PAYED_AMOUNT,
		//						PAY_STATUS,PAY_MEMO,PAYED_STARTDATE,PAYED_LASTDATE,SELLER_SQ,CREATEDID,CREATEDDT,
		$database->prepare("
			UPDATE tb_payment SET PAYED_AMOUNT=PAYED_AMOUNT+:ADD_PAYED_AMOUNT,PAY_STATUS=:PAY_STATUS,MODIFIEDID=:MODIFIEDID,MODIFIEDDT=now()
			WHERE PAY_SQ=:PAY_SQ
		");
		$database->bind(':ADD_PAYED_AMOUNT', $totalpayment);
		$database->bind(':PAY_STATUS', $PAY_STATUS);
		$database->bind(':MODIFIEDID', $USER_SQ);
		$database->bind(':PAY_SQ', $PAY_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}

		//tb_payment_detail
		//PAY_SQ,UV_SQ,MEMBER_SQ,PAY_TYPE,PAY_AMOUNT,CREATEDID,CREATEDDT
		if ($PAYED_AMOUNT_CARD!=0) {
			$database->prepare("
				DELETE FROM tb_payment_detail WHERE PAYDETAIL_SQ=:PAYDETAIL_SQ
			");
			$database->bind(':PAYDETAIL_SQ', $PAYDETAIL_SQ);
			$database->execute();
		}

		// 로그 저장
		$MEMBER_SQ = Get_SingleField("tb_payment", "MEMBER_SQ", "UV_SQ", $UV_SQ, "", $database);
		$ACTION = Get_UserInfo($MEMBER_SQ,$database)."회원님이 ".Get_VoucherInfo($UV_SQ,$database)."이용권을 일부 환불하였습니다.";
		insert_Log_History($CENTER_SQ,$USER_SQ,$MEMBER_SQ,$DEVICE_SQ, $IP, $GROUP, $CATEGORY, $ACTION,$database);
		
		exit(json_encode($response_array));
		break;	
		
	case 'execPurchaseRefund':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$UV_SQ = getAnyParameter("UV_SQ","");
		$PAY_SQ = getAnyParameter("PAY_SQ","");
		$MEMBER_SQ = getAnyParameter("MEMBER_SQ","");
		$REFUND_CASH = getAnyParameter("REFUND_CASH","");

		// 기본값 설정		
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$GROUP = 3;
		$CATEGORY = 32;
		$IP = getClientIPv4();
		
		$totalpayment = $REFUND_CASH;
		$USE_STATUS = 1;
		$response_array["UV_SQ"] = $UV_SQ;
		$PAY_STATUS = 3;

		//tb_payment
		//UV_SQ,VOUCHER_SQ,CENTER_SQ,MEMBER_SQ,ORIGINAL_PRICE,DISCOUNT_TYPE,DISCOUNT_RATIO,DISCOUNT_AMOUNT,FINAL_PRICE,PAYED_AMOUNT,
		//						PAY_STATUS,PAY_MEMO,PAYED_STARTDATE,PAYED_LASTDATE,SELLER_SQ,CREATEDID,CREATEDDT,
		$database->prepare("
			UPDATE tb_payment SET REFUND_AMOUNT=:REFUND_CASH,PAY_STATUS=:PAY_STATUS,MODIFIEDID=:MODIFIEDID,MODIFIEDDT=now()
			WHERE PAY_SQ=:PAY_SQ
		");
		$database->bind(':REFUND_CASH', $totalpayment);
		$database->bind(':PAY_STATUS', $PAY_STATUS);
		$database->bind(':MODIFIEDID', $USER_SQ);
		$database->bind(':PAY_SQ', $PAY_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}

		//tb_payment_detail
		//PAY_SQ,UV_SQ,MEMBER_SQ,PAY_TYPE,PAY_AMOUNT,CREATEDID,CREATEDDT
		if ($REFUND_CASH!=0) {
			$database->prepare("
				INSERT INTO tb_payment_detail (PAY_SQ,UV_SQ,MEMBER_SQ,PAY_TYPE,FUND_TYPE,PAY_AMOUNT,CREATEDID,CREATEDDT) VALUES
									(:PAY_SQ, :UV_SQ,:MEMBER_SQ,:PAY_TYPE,:FUND_TYPE,:PAY_AMOUNT,:CREATEDID,now() )
			");
			$database->bind(':PAY_SQ', $PAY_SQ);
			$database->bind(':UV_SQ', $UV_SQ);
			$database->bind(':MEMBER_SQ', $MEMBER_SQ);
			$database->bind(':PAY_TYPE', 2);
			$database->bind(':FUND_TYPE', 2);
			$database->bind(':PAY_AMOUNT', $REFUND_CASH);
			$database->bind(':CREATEDID', $USER_SQ);
			$database->execute();
		}
//echo("proc 6");

		// 로그 저장
		$ACTION = Get_UserInfo($MEMBER_SQ,$database)."회원님이 ".Get_VoucherInfo($UV_SQ,$database)."이용권을 환불하였습니다.";
		insert_Log_History($CENTER_SQ,$USER_SQ,$MEMBER_SQ, $DEVICE_SQ, $IP, $GROUP, $CATEGORY, $ACTION,$database);
		
		exit(json_encode($response_array));
		break;	
		
	case 'execPurchaseTransfer':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$UV_SQ = getAnyParameter("UV_SQ","");
		$PAY_SQ = getAnyParameter("PAY_SQ","");
		$MEMBER_SQ = getAnyParameter("MEMBER_SQ","");
		$MEMBER_SQ_TO = getAnyParameter("MEMBER_SQ_TO","MEMBER_SQ_TO");

		// 기본값 설정		
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$GROUP = 3;
		$CATEGORY = 33;
		$IP = getClientIPv4();
		
		// 이용권 복사. 
		$database->prepare("
			INSERT INTO tb_user_voucher (CENTER_SQ,MEMBER_SQ,VOUCHER_SQ,VOUCHER_NAME,VOUCHER_TYPE,USE_TYPE,PERIOD_TYPE,
						PERIOD,PERIOD_UNIT,COUNT_TYPE,`COUNT`,ENTERLIMIT_DAY,ENTERLIMIT_WEEK,USEDCOUNT,
						USE_STATUS,USE_STARTDATE,USE_LASTDATE,SELLER_SQ,TRAINER_SQ,CREATEDID,CREATEDDT,MODIFIEDID,MODIFIEDDT)
			SELECT CENTER_SQ,:MEMBER_SQ,VOUCHER_SQ,VOUCHER_NAME,VOUCHER_TYPE,USE_TYPE,PERIOD_TYPE,
						PERIOD,PERIOD_UNIT,COUNT_TYPE,`COUNT`,ENTERLIMIT_DAY,ENTERLIMIT_WEEK,USEDCOUNT,
						USE_STATUS,USE_STARTDATE,USE_LASTDATE,SELLER_SQ,TRAINER_SQ,:CREATEDID,now(),:MODIFIEDID,now()
			FROM tb_user_voucher
			WHERE UV_SQ=:UV_SQ
		");
		$database->bind(':MEMBER_SQ', $MEMBER_SQ_TO);
		$database->bind(':CREATEDID', $USER_SQ);
		$database->bind(':MODIFIEDID', $USER_SQ);
		$database->bind(':UV_SQ', $UV_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		// 이용권 UV_SQ가져오기.
		$UV_SQ_NEW = Get_SingleField('tb_user_voucher', 'MAX(UV_SQ)', 'MEMBER_SQ', $MEMBER_SQ_TO, '',$database);

		// 정지내역 복사. - 내역이 없어도 실패는 아님.
		$database->prepare("
			INSERT INTO tb_user_voucher_pause (UV_SQ,START_DATE,END_DATE,DAYS)
			SELECT :UV_SQ,START_DATE,END_DATE,DAYS
			FROM tb_user_voucher_pause
			WHERE UV_SQ=:UV_SQ2
		");
		$database->bind(':UV_SQ', $UV_SQ);
		$database->bind(':UV_SQ2', $UV_SQ);
		$database->execute();
		
		// 결제 내역 복사.
		$database->prepare("
			INSERT INTO tb_payment (UV_SQ,VOUCHER_SQ,CENTER_SQ,MEMBER_SQ,ORIGINAL_PRICE,DISCOUNT_TYPE,DISCOUNT_RATIO,
						DISCOUNT_AMOUNT,SELLINGPRICE,PAYED_AMOUNT,REFUND_AMOUNT,PAY_STATUS,PAY_MEMO,
						PAYED_STARTDATE,PAYED_LASTDATE,SELLER_SQ,CREATEDID,CREATEDDT,MODIFIEDID,MODIFIEDDT)
			SELECT :UV_SQ,VOUCHER_SQ,CENTER_SQ,:MEMBER_SQ,ORIGINAL_PRICE,DISCOUNT_TYPE,DISCOUNT_RATIO,
						DISCOUNT_AMOUNT,SELLINGPRICE,PAYED_AMOUNT,REFUND_AMOUNT,PAY_STATUS,PAY_MEMO,
						PAYED_STARTDATE,PAYED_LASTDATE,SELLER_SQ,:CREATEDID,now(),:MODIFIEDID,MODIFIEDDT
			FROM tb_payment
			WHERE PAY_SQ=:PAY_SQ
		");
		$database->bind(':UV_SQ', $UV_SQ_NEW);
		$database->bind(':MEMBER_SQ', $MEMBER_SQ_TO);
		$database->bind(':CREATEDID', $USER_SQ);
		$database->bind(':MODIFIEDID', $USER_SQ);
		$database->bind(':PAY_SQ', $PAY_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		
		// PAY_SQ가져오기.
		$PAY_SQ_NEW = Get_SingleField('tb_payment', 'MAX(PAY_SQ)', 'MEMBER_SQ', $MEMBER_SQ_TO, '',$database);
		
		// 결제 세부내역 복사. 
		$database->prepare("
			INSERT INTO tb_payment_detail (PAY_SQ,UV_SQ,MEMBER_SQ,PAY_TYPE,FUND_TYPE,PAY_AMOUNT,CREATEDID,CREATEDDT)
			SELECT :PAY_SQ,:UV_SQ,:MEMBER_SQ,PAY_TYPE,FUND_TYPE,PAY_AMOUNT,:CREATEDID,NOW()
			FROM tb_payment_detail
			WHERE UV_SQ=:UV_SQ2
		");
		$database->bind(':PAY_SQ', $PAY_SQ_NEW);
		$database->bind(':UV_SQ', $UV_SQ_NEW);
		$database->bind(':MEMBER_SQ', $MEMBER_SQ_TO);
		$database->bind(':CREATEDID', $USER_SQ);
		$database->bind(':UV_SQ2', $UV_SQ);
		$database->execute();
		
		// 기존 이용권 상태 변경 . 
		$database->prepare("
			UPDATE tb_user_voucher SET USE_STATUS=3,MODIFIEDID=:MODIFIEDID,MODIFIEDDT=now()
			WHERE UV_SQ=:UV_SQ
		");
		$database->bind(':MODIFIEDID', $USER_SQ);
		$database->bind(':UV_SQ', $UV_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		// 기존 이용권 결제 내역 상태 변경.		
		$database->prepare("
			UPDATE tb_payment SET PAY_STATUS=4,MODIFIEDID=:MODIFIEDID,MODIFIEDDT=now()
			WHERE PAY_SQ=:PAY_SQ
		");
		$database->bind(':MODIFIEDID', $USER_SQ);
		$database->bind(':PAY_SQ', $PAY_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}

		// 기존 이용권 예약 내역 초기화. 
		$database->prepare("
			UPDATE tb_reservation SET RESERV_STATUS=2
			WHERE USER_SQ=:USER_SQ and (RESERV_STATUS=1 OR RESERV_STATUS=6)
		");
		$database->bind(':USER_SQ', $MEMBER_SQ);
		$database->execute();

		$database->prepare("
			UPDATE tb_class_reservation  SET RESERV_STATUS=2
			WHERE USER_SQ=:USER_SQ and (RESERV_STATUS=1 OR RESERV_STATUS=6)
		");
		$database->bind(':USER_SQ', $MEMBER_SQ);
		$database->execute();

		// 로그 저장
		$ACTION = Get_UserInfo($MEMBER_SQ,$database)."회원님이 ".Get_UserInfo($MEMBER_SQ_TO,$database)."회원님에게 ".Get_VoucherInfo($UV_SQ,$database)."이용권을 양도하였습니다.";
		insert_Log_History($CENTER_SQ,$USER_SQ,$MEMBER_SQ, $DEVICE_SQ, $IP, $GROUP, $CATEGORY, $ACTION,$database);
		
		$ACTION = Get_UserInfo($MEMBER_SQ_TO,$database)."회원님이 ".Get_UserInfo($MEMBER_SQ,$database)."회원님에게 ".Get_VoucherInfo($UV_SQ_NEW,$database)."이용권을 양도받았습니다.";
		insert_Log_History($CENTER_SQ,$USER_SQ,$MEMBER_SQ_TO, $DEVICE_SQ, $IP, $GROUP, $CATEGORY, $ACTION,$database);
		
		exit(json_encode($response_array));
		break;	
		
	case 'execPurchaseModify':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$UV_SQ = getAnyParameter("UV_SQ","");
		$PAY_SQ = getAnyParameter("PAY_SQ","");
		$MEMBER_SQ = getAnyParameter("MEMBER_SQ","");
		$SELLINGPRICE = getAnyParameter("SELLINGPRICE","");
		$PAYED_AMOUNT_CARD = getAnyParameter("PAYED_AMOUNT_CARD","");
		$PAYED_AMOUNT_CASH = getAnyParameter("PAYED_AMOUNT_CASH","");
		$PAYED_AMOUNT_BANK = getAnyParameter("PAYED_AMOUNT_BANK","");

		// 기본값 설정		
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$GROUP = 3;
		$CATEGORY = 35;
		$IP = getClientIPv4();
		
		$totalpayment = $PAYED_AMOUNT_CARD + $PAYED_AMOUNT_CASH + $PAYED_AMOUNT_BANK;
		$USE_STATUS = 1;
		$response_array["UV_SQ"] = $UV_SQ;
		if ($SELLINGPRICE==$totalpayment) {
			$PAY_STATUS = 2;
		} else {
			$PAY_STATUS = 1;
		}

		//tb_payment
		//UV_SQ,VOUCHER_SQ,CENTER_SQ,MEMBER_SQ,ORIGINAL_PRICE,DISCOUNT_TYPE,DISCOUNT_RATIO,DISCOUNT_AMOUNT,FINAL_PRICE,PAYED_AMOUNT,
		//						PAY_STATUS,PAY_MEMO,PAYED_STARTDATE,PAYED_LASTDATE,SELLER_SQ,CREATEDID,CREATEDDT,
		$database->prepare("
			UPDATE tb_payment SET PAYED_AMOUNT=PAYED_AMOUNT+:ADD_PAYED_AMOUNT,PAY_STATUS=:PAY_STATUS,MODIFIEDID=:MODIFIEDID,MODIFIEDDT=now()
			WHERE PAY_SQ=:PAY_SQ
		");
		$database->bind(':ADD_PAYED_AMOUNT', $totalpayment);
		$database->bind(':PAY_STATUS', $PAY_STATUS);
		$database->bind(':MODIFIEDID', $USER_SQ);
		$database->bind(':PAY_SQ', $PAY_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}

		//tb_payment_detail
		//PAY_SQ,UV_SQ,MEMBER_SQ,PAY_TYPE,PAY_AMOUNT,CREATEDID,CREATEDDT
		if ($PAYED_AMOUNT_CARD!=0) {
			$database->prepare("
				INSERT INTO tb_payment_detail (PAY_SQ,UV_SQ,MEMBER_SQ,PAY_TYPE,PAY_AMOUNT,CREATEDID,CREATEDDT) VALUES
									(:PAY_SQ, :UV_SQ,:MEMBER_SQ,:PAY_TYPE,:PAY_AMOUNT,:CREATEDID,now() )
			");
			$database->bind(':PAY_SQ', $PAY_SQ);
			$database->bind(':UV_SQ', $UV_SQ);
			$database->bind(':MEMBER_SQ', $MEMBER_SQ);
			$database->bind(':PAY_TYPE', 1);
			$database->bind(':PAY_AMOUNT', $PAYED_AMOUNT_CARD);
			$database->bind(':CREATEDID', $USER_SQ);
			$database->execute();
		}
		if ($PAYED_AMOUNT_CASH!=0) {
			$database->prepare("
				INSERT INTO tb_payment_detail (PAY_SQ,UV_SQ,MEMBER_SQ,PAY_TYPE,PAY_AMOUNT,CREATEDID,CREATEDDT) VALUES
									(:PAY_SQ, :UV_SQ,:MEMBER_SQ,:PAY_TYPE,:PAY_AMOUNT,:CREATEDID,now() )
			");
			$database->bind(':PAY_SQ', $PAY_SQ);
			$database->bind(':UV_SQ', $UV_SQ);
			$database->bind(':MEMBER_SQ', $MEMBER_SQ);
			$database->bind(':PAY_TYPE', 2);
			$database->bind(':PAY_AMOUNT', $PAYED_AMOUNT_CASH);
			$database->bind(':CREATEDID', $USER_SQ);
			$database->execute();
		}
		if ($PAYED_AMOUNT_BANK!=0) {
			$database->prepare("
				INSERT INTO tb_payment_detail (PAY_SQ,UV_SQ,MEMBER_SQ,PAY_TYPE,PAY_AMOUNT,CREATEDID,CREATEDDT) VALUES
									(:PAY_SQ, :UV_SQ,:MEMBER_SQ,:PAY_TYPE,:PAY_AMOUNT,:CREATEDID,now() )
			");
			$database->bind(':PAY_SQ', $PAY_SQ);
			$database->bind(':UV_SQ', $UV_SQ);
			$database->bind(':MEMBER_SQ', $MEMBER_SQ);
			$database->bind(':PAY_TYPE', 3);
			$database->bind(':PAY_AMOUNT', $PAYED_AMOUNT_BANK);
			$database->bind(':CREATEDID', $USER_SQ);
			$database->execute();
		}
//echo("proc 6");

		// 로그 저장
		$ACTION = Get_UserInfo($MEMBER_SQ,$database)."회원님이 ".Get_VoucherInfo($UV_SQ,$database)."이용권을 추가 결재하였습니다.";
		insert_Log_History($CENTER_SQ,$USER_SQ,$MEMBER_SQ, $DEVICE_SQ, $IP, $GROUP, $CATEGORY, $ACTION,$database);
		
		exit(json_encode($response_array));
		break;	
		
	case 'execPuchaseCreate':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$VOUCHER_SQ = getAnyParameter("VOUCHER_SQ","");
		$MEMBER_SQ = getAnyParameter("MEMBER_SQ","");
		$DISCOUNT_TYPE = getAnyParameter("DISCOUNT_TYPE","");
		$DISCOUNT_RATIO = getAnyParameter("DISCOUNT_RATIO","");
		$DISCOUNT_AMOUNT = getAnyParameter("DISCOUNT_AMOUNT","");
		$SELLINGPRICE = getAnyParameter("SELLINGPRICE","");
		$SELLER_SQ = getAnyParameter("SELLER_SQ","");
		$TRAINER_SQ = getAnyParameter("TRAINER_SQ","");
		$PAYED_AMOUNT_CARD = getAnyParameter("PAYED_AMOUNT_CARD","");
		$PAYED_AMOUNT_CASH = getAnyParameter("PAYED_AMOUNT_CASH","");
		$PAYED_AMOUNT_BANK = getAnyParameter("PAYED_AMOUNT_BANK","");
		$USE_STARTDATE = getAnyParameter("USE_STARTDATE","");
		$USE_LASTDATE = getAnyParameter("USE_LASTDATE","");
		$PAY_MEMO = getAnyParameter("PAY_MEMO","");

		// 기본값 설정		
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$GROUP = 3;
		$CATEGORY = 31;
		$IP = getClientIPv4();
		
		// 최종 결과 취득 
		$database->prepare("
			select PRICE from tb_voucher where CENTER_SQ=:CENTER_SQ and VOUCHER_SQ=:VOUCHER_SQ
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':VOUCHER_SQ', $VOUCHER_SQ);
		$database->execute();
		
		$response_array["result"] = 'Success';
//echo("proc 1");
		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		$row = $database->fetch();
		$totalprice = $row["PRICE"];
		$totalpayment = $PAYED_AMOUNT_CARD + $PAYED_AMOUNT_CASH + $PAYED_AMOUNT_BANK;
		$USE_STATUS = 1;
	//echo("totalpayment=".$totalpayment);

		//tb_user_voucher
		//CENTER_SQ,MEMBER_SQ,VOUCHER_SQ,VOUCHER_NAME,VOUCHER_TYPE,USE_TYPE,PERIOD_TYPE,PERIOD,PERIOD_UNIT,COUNT_TYPE,COUNT,ENTERLIMIT_DAY,
		//								ENTERLIMIT_WEEK,USEDCOUNT,USE_STATUS,USE_STARTDATE,USE_LASTDATE,SELLER_SQ,CREATEDID,CREATEDDT
		$database->prepare("
			INSERT INTO tb_user_voucher (CENTER_SQ,MEMBER_SQ,VOUCHER_SQ,VOUCHER_NAME,VOUCHER_TYPE,USE_TYPE,PERIOD_TYPE,PERIOD,PERIOD_UNIT,COUNT_TYPE,COUNT,ENTERLIMIT_DAY,
										ENTERLIMIT_WEEK,USEDCOUNT,USE_STATUS,USE_STARTDATE,USE_LASTDATE,SELLER_SQ,TRAINER_SQ,CREATEDID,CREATEDDT,MODIFIEDDT) 
			SELECT CENTER_SQ,:MEMBER_SQ,VOUCHER_SQ,VOUCHER_NAME,VOUCHER_TYPE,USE_TYPE,PERIOD_TYPE,PERIOD,PERIOD_UNIT,
				COUNT_TYPE,COUNT,ENTERLIMIT_DAY,ENTERLIMIT_WEEK,0,:USE_STATUS,:USE_STARTDATE,:USE_LASTDATE,:SELLER_SQ,:TRAINER_SQ,:CREATEDID,now(),now() 
			FROM tb_voucher	WHERE CENTER_SQ=:CENTER_SQ and VOUCHER_SQ=:VOUCHER_SQ and USEYN=1
		");
		$database->bind(':MEMBER_SQ', $MEMBER_SQ);
		$database->bind(':USE_STATUS', $USE_STATUS);
		$database->bind(':USE_STARTDATE', $USE_STARTDATE);
		$database->bind(':USE_LASTDATE', $USE_LASTDATE);
		$database->bind(':SELLER_SQ', $SELLER_SQ);
		$database->bind(':TRAINER_SQ', $TRAINER_SQ);
		$database->bind(':CREATEDID', $USER_SQ);
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':VOUCHER_SQ', $VOUCHER_SQ);
		$database->execute();

		$response_array["result"] = 'Success';
//echo("proc 2");
		
		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		
		// uv_sq 취득 
		$database->prepare("
			select UV_SQ from tb_user_voucher where CENTER_SQ=:CENTER_SQ and VOUCHER_SQ=:VOUCHER_SQ and MEMBER_SQ=:MEMBER_SQ 
			order by UV_SQ desc LIMIT 1
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':VOUCHER_SQ', $VOUCHER_SQ);
		$database->bind(':MEMBER_SQ', $MEMBER_SQ);
		$database->execute();
		
		$response_array["result"] = 'Success';
//echo("proc 3");
		
		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		$row = $database->fetch();
		$UV_SQ = $row["UV_SQ"];
		
		$response_array["UV_SQ"] = $UV_SQ;
		if ($SELLINGPRICE==$totalpayment) {
			$PAY_STATUS = 2;
		} else {
			$PAY_STATUS = 1;
		}

		//tb_payment
		//UV_SQ,VOUCHER_SQ,CENTER_SQ,MEMBER_SQ,ORIGINAL_PRICE,DISCOUNT_TYPE,DISCOUNT_RATIO,DISCOUNT_AMOUNT,FINAL_PRICE,PAYED_AMOUNT,
		//						PAY_STATUS,PAY_MEMO,PAYED_STARTDATE,PAYED_LASTDATE,SELLER_SQ,CREATEDID,CREATEDDT,
		$database->prepare("
			INSERT INTO tb_payment (UV_SQ,VOUCHER_SQ,CENTER_SQ,MEMBER_SQ,ORIGINAL_PRICE,DISCOUNT_TYPE,DISCOUNT_RATIO,DISCOUNT_AMOUNT,SELLINGPRICE,PAYED_AMOUNT,
								PAY_STATUS,PAY_MEMO,PAYED_STARTDATE,PAYED_LASTDATE,SELLER_SQ,CREATEDID,CREATEDDT,MODIFIEDDT) 
			SELECT :UV_SQ,VOUCHER_SQ,CENTER_SQ,:MEMBER_SQ,PRICE,:DISCOUNT_TYPE,:DISCOUNT_RATIO,:DISCOUNT_AMOUNT,:SELLINGPRICE,:PAYED_AMOUNT,
								:PAY_STATUS,:PAY_MEMO,now() ,now() , :SELLER_SQ,:CREATEDID,now() ,now()
			FROM tb_voucher	WHERE CENTER_SQ=:CENTER_SQ and VOUCHER_SQ=:VOUCHER_SQ and USEYN=1
		");
		$database->bind(':UV_SQ', $UV_SQ);
		$database->bind(':MEMBER_SQ', $MEMBER_SQ);
		$database->bind(':DISCOUNT_TYPE', $DISCOUNT_TYPE);
		$database->bind(':DISCOUNT_RATIO', $DISCOUNT_RATIO);
		$database->bind(':DISCOUNT_AMOUNT', $DISCOUNT_AMOUNT);
		$database->bind(':SELLINGPRICE', $SELLINGPRICE);
		$database->bind(':PAYED_AMOUNT', $totalpayment);
		$database->bind(':PAY_STATUS', $PAY_STATUS);
		$database->bind(':PAY_MEMO', $PAY_MEMO);
		$database->bind(':SELLER_SQ', $SELLER_SQ);
		$database->bind(':CREATEDID', $USER_SQ);
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':VOUCHER_SQ', $VOUCHER_SQ);
		$database->execute();

		$response_array["result"] = 'Success';
//echo("proc 4");

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
//echo("proc 42");

		// pay_sq 취득 
		$database->prepare("
			select PAY_SQ from tb_payment where CENTER_SQ=:CENTER_SQ and VOUCHER_SQ=:VOUCHER_SQ and MEMBER_SQ=:MEMBER_SQ 
			order by PAY_SQ desc LIMIT 1
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':VOUCHER_SQ', $VOUCHER_SQ);
		$database->bind(':MEMBER_SQ', $MEMBER_SQ);
		$database->execute();
		
		$response_array["result"] = 'Success';
		
		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		$row = $database->fetch();
		$PAY_SQ = $row["PAY_SQ"];

//echo("proc 5");

		//tb_payment_detail
		//PAY_SQ,UV_SQ,MEMBER_SQ,PAY_TYPE,PAY_AMOUNT,CREATEDID,CREATEDDT
		if ($PAYED_AMOUNT_CARD>0) {
			$database->prepare("
				INSERT INTO tb_payment_detail (PAY_SQ,UV_SQ,MEMBER_SQ,PAY_TYPE,PAY_AMOUNT,CREATEDID,CREATEDDT) VALUES
									(:PAY_SQ, :UV_SQ,:MEMBER_SQ,:PAY_TYPE,:PAY_AMOUNT,:CREATEDID,now() )
			");
			$database->bind(':PAY_SQ', $PAY_SQ);
			$database->bind(':UV_SQ', $UV_SQ);
			$database->bind(':MEMBER_SQ', $MEMBER_SQ);
			$database->bind(':PAY_TYPE', 1);
			$database->bind(':PAY_AMOUNT', $PAYED_AMOUNT_CARD);
			$database->bind(':CREATEDID', $USER_SQ);
			$database->execute();
		}
		if ($PAYED_AMOUNT_CASH>0) {
			$database->prepare("
				INSERT INTO tb_payment_detail (PAY_SQ,UV_SQ,MEMBER_SQ,PAY_TYPE,PAY_AMOUNT,CREATEDID,CREATEDDT) VALUES
									(:PAY_SQ, :UV_SQ,:MEMBER_SQ,:PAY_TYPE,:PAY_AMOUNT,:CREATEDID,now() )
			");
			$database->bind(':PAY_SQ', $PAY_SQ);
			$database->bind(':UV_SQ', $UV_SQ);
			$database->bind(':MEMBER_SQ', $MEMBER_SQ);
			$database->bind(':PAY_TYPE', 2);
			$database->bind(':PAY_AMOUNT', $PAYED_AMOUNT_CASH);
			$database->bind(':CREATEDID', $USER_SQ);
			$database->execute();
		}
		if ($PAYED_AMOUNT_BANK>0) {
			$database->prepare("
				INSERT INTO tb_payment_detail (PAY_SQ,UV_SQ,MEMBER_SQ,PAY_TYPE,PAY_AMOUNT,CREATEDID,CREATEDDT) VALUES
									(:PAY_SQ, :UV_SQ,:MEMBER_SQ,:PAY_TYPE,:PAY_AMOUNT,:CREATEDID,now() )
			");
			$database->bind(':PAY_SQ', $PAY_SQ);
			$database->bind(':UV_SQ', $UV_SQ);
			$database->bind(':MEMBER_SQ', $MEMBER_SQ);
			$database->bind(':PAY_TYPE', 3);
			$database->bind(':PAY_AMOUNT', $PAYED_AMOUNT_BANK);
			$database->bind(':CREATEDID', $USER_SQ);
			$database->execute();
		}
//echo("proc 6");

		// 로그 저장
		$ACTION = Get_UserInfo($MEMBER_SQ,$database)."회원님이 ".Get_VoucherInfo($UV_SQ,$database)."이용권을 구매하였습니다.";
		insert_Log_History($CENTER_SQ,$USER_SQ,$MEMBER_SQ,$DEVICE_SQ, $IP, $GROUP, $CATEGORY, $ACTION,$database);
		
		// 로그 저장 : 담당강사지정 
		$GROUP = 1;
		$CATEGORY = 1;
		$ACTION = Get_UserInfo($MEMBER_SQ,$database)."회원님의 ".Get_VoucherInfo($UV_SQ,$database)."이용권의 담당강사를 ".Get_TrainerInfo($TRAINER_SQ,$database)."트레이너로 지정하였습니다.";
		insert_Log_History($CENTER_SQ,$USER_SQ,$MEMBER_SQ,$DEVICE_SQ, $IP, $GROUP, $CATEGORY, $ACTION,$database);

		exit(json_encode($response_array));
		break;	
		
	case 'getVoucherInfo':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$VOUCHER_SQ = getAnyParameter("VOUCHER_SQ","");

		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "상품권 설정";
		$SUBCATEGORY = "상품권 조회";
		$ACTION = $CENTER_SQ . " 상품권을 조회하였습니다.";
		$IP = getClientIPv4();
		
		//tb_category
		//CATEGORY_SQ,CENTER_SQ,CATEGORY_NAME,REGDT,REGID,MODIFIEDDT,MODIFIEDID
		$database->prepare("
			SELECT CATEGORY_SQ,CENTER_SQ,CATEGORY_NAME,REGDT,REGID,MODIFIEDDT,MODIFIEDID
 			FROM tb_category
			WHERE CENTER_SQ=:CENTER_SQ and USEYN=1
			ORDER BY CATEGORY_SQ asc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$categorylist = json_encode($rows);

		//tb_subcategory
		//SUBCATEGORY_SQ,CENTER_SQ,CATEGORY_SQ,SUBCATEGORY_NAME,REGDT,REGID,MODIFIEDDT,MODIFIEDID
		$database->prepare("
			SELECT SUBCATEGORY_SQ,CENTER_SQ,CATEGORY_SQ,SUBCATEGORY_NAME,REGDT,REGID,MODIFIEDDT,MODIFIEDID
 			FROM tb_subcategory
			WHERE CENTER_SQ=:CENTER_SQ and USEYN=1
			ORDER BY SUBCATEGORY_SQ asc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$subcategorylist = json_encode($rows);
		
		//tb_voucher
		//VOUCHER_SQ,CENTER_SQ,CATEGORY_SQ,SUBCATEGORY_SQ,VOUCHER_NAME,VOUCHER_TYPE,USE_TYPE,PERIOD_TYPE,PERIOD,COUNT_TYPE,COUNT,PRICE,SURTAX_TYPE,DISCOUNT_TYPE,DISCOUNT_RATIO,DISCOUNT_AMOUNT,SELLINGPRICE

		$database->prepare("
			SELECT VOUCHER_SQ,CENTER_SQ,CATEGORY_SQ,SUBCATEGORY_SQ,VOUCHER_NAME,VOUCHER_TYPE,USE_TYPE,PERIOD_TYPE,PERIOD,PERIOD_UNIT,
					COUNT_TYPE,COUNT,ENTERLIMIT_DAY,ENTERLIMIT_WEEK,PRICE,SURTAX_TYPE,DISCOUNT_TYPE,DISCOUNT_RATIO,DISCOUNT_AMOUNT,SELLINGPRICE,
					REGID,REGDT,MODIFIEDID,MODIFIEDDT
 			FROM tb_voucher
			WHERE CENTER_SQ=:CENTER_SQ and VOUCHER_SQ=:VOUCHER_SQ and USEYN=1
			ORDER BY CATEGORY_SQ asc , SUBCATEGORY_SQ asc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':VOUCHER_SQ', $VOUCHER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$voucherinfo = json_encode($rows);

		// 최종 결과 취득 
		$database->prepare("
			select a.USER_SQ,a.CENTER_SQ,a.USER_NM,a.PHONE_NO,a.ADDRESS,a.EMAIL,a.BIRTH_DT, a.REG_DT, a.GRADE,
						a.WORKCATEGORY, a.WORKTYPE, a.WORKSTARTDATE, a.WORKENDDATE, a.WORKSTATUS, a.ISUSE
					from tb_user a  left outer join tb_user b on a.TRAINER=b.USER_SQ
					where a.CENTER_SQ=:CENTER_SQ and a.ISMANAGER=0 order by a.REG_DT desc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();
		
		$rows = $database->fetchAll();
		$memberlist = json_encode($rows);

		// 최종 결과 취득 
		$database->prepare("
			select a.USER_SQ,a.CENTER_SQ,a.USER_NM,a.PHONE_NO,a.ADDRESS,a.EMAIL,a.BIRTH_DT, a.REG_DT, a.GRADE,
						a.WORKCATEGORY, a.WORKTYPE, a.WORKSTARTDATE, a.WORKENDDATE, a.WORKSTATUS, a.ISUSE
					from tb_user a  left outer join tb_user b on a.TRAINER=b.USER_SQ
					where a.CENTER_SQ=:CENTER_SQ and a.ISMANAGER=1 and a.ISUSE=1 order by a.REG_DT desc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();
		
		$rows = $database->fetchAll();
		$managerlist = json_encode($rows);
		
		$vouchertype = getCommonCode('CD004', $database);
		$usetype = getCommonCode('CD005', $database);
		$periodtype = getCommonCode('CD006', $database);
		$counttype = getCommonCode('CD007', $database);
		$surtaxtype = getCommonCode('CD008', $database);
		$discounttype = getCommonCode('CD009', $database);
		$periodunit = getCommonCode('CD010', $database);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($voucherinfo.'|'.$categorylist.'|'.$subcategorylist.'|'.$vouchertype.'|'.$usetype.'|'.$periodtype.'|'.$periodunit.'|'.$counttype.'|'.$surtaxtype.'|'.$discounttype.'|'.$memberlist.'|'.$managerlist);
		break;	
		
	case 'execVoucherDelete':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$VOUCHER_SQ = getAnyParameter("VOUCHER_SQ","");

		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "상품권설정";
		$SUBCATEGORY = "서브카테고리 삭제";
		$ACTION = $CENTER_SQ . " 센터 서브카테고리를 삭제하였습니다.";
		$IP = getClientIPv4();

		$database->prepare("
			UPDATE tb_voucher set USEYN=0 where VOUCHER_SQ=:VOUCHER_SQ
		");
		$database->bind(':VOUCHER_SQ', $VOUCHER_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		
		$database->prepare("
			SELECT VOUCHER_SQ,CENTER_SQ,CATEGORY_SQ,SUBCATEGORY_SQ,VOUCHER_NAME,VOUCHER_TYPE,USE_TYPE,PERIOD_TYPE,PERIOD,PERIOD_UNIT,
					COUNT_TYPE,COUNT,ENTERLIMIT_DAY,ENTERLIMIT_WEEK,PRICE,SURTAX_TYPE,DISCOUNT_TYPE,DISCOUNT_RATIO,DISCOUNT_AMOUNT,SELLINGPRICE,
					REGID,REGDT,MODIFIEDID,MODIFIEDDT
 			FROM tb_voucher
			WHERE CENTER_SQ=:CENTER_SQ and USEYN=1
			ORDER BY CATEGORY_SQ asc , SUBCATEGORY_SQ asc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$voucherList = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($voucherList);
		break;	
		
	case 'execVoucherAdd':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$CATEGORY_SQ = getAnyParameter("CATEGORY_SQ","");
		$SUBCATEGORY_SQ = getAnyParameter("SUBCATEGORY_SQ","");
		$VOUCHER_NAME = getAnyParameter("VOUCHER_NAME","");
		$VOUCHER_TYPE = getAnyParameter("VOUCHER_TYPE","");
		$USE_TYPE = getAnyParameter("USE_TYPE","");
		$PERIOD_TYPE = getAnyParameter("PERIOD_TYPE","");
		$PERIOD = getAnyParameter("PERIOD","");
		$PERIOD_UNIT = getAnyParameter("PERIOD_UNIT","");
		$COUNT_TYPE = getAnyParameter("COUNT_TYPE","");
		$COUNT = getAnyParameter("COUNT","");
		$ENTERLIMIT_DAY = getAnyParameter("ENTERLIMIT_DAY","");
		$ENTERLIMIT_WEEK = getAnyParameter("ENTERLIMIT_WEEK","");
		$PRICE = getAnyParameter("PRICE","");
		$SURTAX_TYPE = getAnyParameter("SURTAX_TYPE","");
		$DISCOUNT_TYPE = getAnyParameter("DISCOUNT_TYPE","");
		$DISCOUNT_RATIO = getAnyParameter("DISCOUNT_RATIO","");
		$DISCOUNT_AMOUNT = getAnyParameter("DISCOUNT_AMOUNT","");
		$SELLINGPRICE = getAnyParameter("SELLINGPRICE","");

		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "상품권설정";
		$SUBCATEGORY = "서브카테고리 추가";
		$ACTION = $CENTER_SQ . " 서브카테고리를 추가하였습니다.";
		$IP = getClientIPv4();
		
		//NOTICE_SQ,NOTICE_TYPE,NOTICE_TITLE,NOTICE_CONTENTS,CREATEDBY,MODIFIEDBY,CREATEDDT,MODIFIEDDT
		$database->prepare("
			INSERT tb_voucher ( CENTER_SQ, CATEGORY_SQ,SUBCATEGORY_SQ,VOUCHER_NAME,VOUCHER_TYPE,USE_TYPE,PERIOD_TYPE,PERIOD,PERIOD_UNIT,
					COUNT_TYPE,COUNT,ENTERLIMIT_DAY,ENTERLIMIT_WEEK, PRICE,SURTAX_TYPE,DISCOUNT_TYPE,DISCOUNT_RATIO,DISCOUNT_AMOUNT,SELLINGPRICE, 
					REGID, REGDT ) VALUES
			(:CENTER_SQ,:CATEGORY_SQ,:SUBCATEGORY_SQ,:VOUCHER_NAME,:VOUCHER_TYPE,:USE_TYPE,:PERIOD_TYPE,:PERIOD,:PERIOD_UNIT,
					:COUNT_TYPE,:COUNT,:ENTERLIMIT_DAY,:ENTERLIMIT_WEEK,:PRICE,:SURTAX_TYPE,:DISCOUNT_TYPE,:DISCOUNT_RATIO,:DISCOUNT_AMOUNT,:SELLINGPRICE,:REGID,now() )
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':CATEGORY_SQ', $CATEGORY_SQ);
		$database->bind(':SUBCATEGORY_SQ', $SUBCATEGORY_SQ);
		$database->bind(':VOUCHER_NAME', $VOUCHER_NAME);
		$database->bind(':VOUCHER_TYPE', $VOUCHER_TYPE);
		$database->bind(':USE_TYPE', $USE_TYPE);
		$database->bind(':PERIOD_TYPE', $PERIOD_TYPE);
		$database->bind(':PERIOD', $PERIOD);
		$database->bind(':PERIOD_UNIT', $PERIOD_UNIT);
		$database->bind(':COUNT_TYPE', $COUNT_TYPE);
		$database->bind(':COUNT', $COUNT);
		$database->bind(':ENTERLIMIT_DAY', $ENTERLIMIT_DAY);
		$database->bind(':ENTERLIMIT_WEEK', $ENTERLIMIT_WEEK);
		$database->bind(':PRICE', $PRICE);
		$database->bind(':SURTAX_TYPE', $SURTAX_TYPE);
		$database->bind(':DISCOUNT_TYPE', $DISCOUNT_TYPE);
		$database->bind(':DISCOUNT_RATIO', $DISCOUNT_RATIO);
		$database->bind(':DISCOUNT_AMOUNT', $DISCOUNT_AMOUNT);
		$database->bind(':SELLINGPRICE', $SELLINGPRICE);
		$database->bind(':REGID', $USER_SQ);
		$database->execute();

		$response_array["result"] = 'Success';
		
		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		
		$database->prepare("
			SELECT VOUCHER_SQ,CENTER_SQ,CATEGORY_SQ,SUBCATEGORY_SQ,VOUCHER_NAME,VOUCHER_TYPE,USE_TYPE,PERIOD_TYPE,PERIOD,PERIOD_UNIT,
					COUNT_TYPE,COUNT,ENTERLIMIT_DAY,ENTERLIMIT_WEEK,PRICE,SURTAX_TYPE,DISCOUNT_TYPE,DISCOUNT_RATIO,DISCOUNT_AMOUNT,SELLINGPRICE,
					REGID,REGDT,MODIFIEDID,MODIFIEDDT
 			FROM tb_voucher
			WHERE CENTER_SQ=:CENTER_SQ and USEYN=1
			ORDER BY CATEGORY_SQ asc , SUBCATEGORY_SQ asc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$voucherList = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($voucherList);
		break;	
		
	case 'execVoucherModify':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$VOUCHER_SQ = getAnyParameter("VOUCHER_SQ","");
		$CATEGORY_SQ = getAnyParameter("CATEGORY_SQ","");
		$SUBCATEGORY_SQ = getAnyParameter("SUBCATEGORY_SQ","");
		$VOUCHER_NAME = getAnyParameter("VOUCHER_NAME","");
		$VOUCHER_TYPE = getAnyParameter("VOUCHER_TYPE","");
		$USE_TYPE = getAnyParameter("USE_TYPE","");
		$PERIOD_TYPE = getAnyParameter("PERIOD_TYPE","");
		$PERIOD_UNIT = getAnyParameter("PERIOD_UNIT","");
		$PERIOD = getAnyParameter("PERIOD","");
		$COUNT_TYPE = getAnyParameter("COUNT_TYPE","");
		$COUNT = getAnyParameter("COUNT","");
		$ENTERLIMIT_DAY = getAnyParameter("ENTERLIMIT_DAY","");
		$ENTERLIMIT_WEEK = getAnyParameter("ENTERLIMIT_WEEK","");
		$PRICE = getAnyParameter("PRICE","");
		$SURTAX_TYPE = getAnyParameter("SURTAX_TYPE","");
		$DISCOUNT_TYPE = getAnyParameter("DISCOUNT_TYPE","");
		$DISCOUNT_RATIO = getAnyParameter("DISCOUNT_RATIO","");
		$DISCOUNT_AMOUNT = getAnyParameter("DISCOUNT_AMOUNT","");
		$SELLINGPRICE = getAnyParameter("SELLINGPRICE","");

		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "상품권설정";
		$SUBCATEGORY = "서브카테고리 수정";
		$ACTION = $CENTER_SQ . " 센터 서브카테고리를 수정하였습니다.";
		$IP = getClientIPv4();

		$database->prepare("
			UPDATE tb_voucher SET VOUCHER_SQ=:VOUCHER_SQ, CATEGORY_SQ=:CATEGORY_SQ, SUBCATEGORY_SQ=:SUBCATEGORY_SQ, 
				VOUCHER_NAME=:VOUCHER_NAME, VOUCHER_TYPE=:VOUCHER_TYPE, USE_TYPE=:USE_TYPE, PERIOD_TYPE=:PERIOD_TYPE, 
				PERIOD=:PERIOD, PERIOD_UNIT=:PERIOD_UNIT, COUNT_TYPE=:COUNT_TYPE, COUNT=:COUNT, ENTERLIMIT_DAY=:ENTERLIMIT_DAY,
				ENTERLIMIT_WEEK=:ENTERLIMIT_WEEK,PRICE=:PRICE, 
				DISCOUNT_TYPE=:DISCOUNT_TYPE, DISCOUNT_RATIO=:DISCOUNT_RATIO, DISCOUNT_AMOUNT=:DISCOUNT_AMOUNT, SELLINGPRICE=:SELLINGPRICE, 
				MODIFIEDID=:MODIFIEDID, MODIFIEDDT=now()
			WHERE VOUCHER_SQ=:VOUCHER_SQ
		");
		$database->bind(':VOUCHER_SQ', $VOUCHER_SQ);
		$database->bind(':CATEGORY_SQ', $CATEGORY_SQ);
		$database->bind(':SUBCATEGORY_SQ', $SUBCATEGORY_SQ);
		$database->bind(':VOUCHER_NAME', $VOUCHER_NAME);
		$database->bind(':VOUCHER_TYPE', $VOUCHER_TYPE);
		$database->bind(':USE_TYPE', $USE_TYPE);
		$database->bind(':PERIOD_TYPE', $PERIOD_TYPE);
		$database->bind(':PERIOD', $PERIOD);
		$database->bind(':PERIOD_UNIT', $PERIOD_UNIT);
		$database->bind(':COUNT_TYPE', $COUNT_TYPE);
		$database->bind(':COUNT', $COUNT);
		$database->bind(':ENTERLIMIT_DAY', $ENTERLIMIT_DAY);
		$database->bind(':ENTERLIMIT_WEEK', $ENTERLIMIT_WEEK);
		$database->bind(':PRICE', $PRICE);
		$database->bind(':DISCOUNT_TYPE', $DISCOUNT_TYPE);
		$database->bind(':DISCOUNT_RATIO', $DISCOUNT_RATIO);
		$database->bind(':DISCOUNT_AMOUNT', $DISCOUNT_AMOUNT);
		$database->bind(':SELLINGPRICE', $SELLINGPRICE);
		$database->bind(':MODIFIEDID', $USER_SQ);
		$database->bind(':VOUCHER_SQ', $VOUCHER_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		
		$database->prepare("
			SELECT VOUCHER_SQ,CENTER_SQ,CATEGORY_SQ,SUBCATEGORY_SQ,VOUCHER_NAME,VOUCHER_TYPE,USE_TYPE,PERIOD_TYPE,PERIOD,PERIOD_UNIT,
					COUNT_TYPE,COUNT,ENTERLIMIT_DAY,ENTERLIMIT_WEEK,PRICE,SURTAX_TYPE,DISCOUNT_TYPE,DISCOUNT_RATIO,DISCOUNT_AMOUNT,SELLINGPRICE,
					REGID,REGDT,MODIFIEDID,MODIFIEDDT
 			FROM tb_voucher
			WHERE CENTER_SQ=:CENTER_SQ and USEYN=1
			ORDER BY CATEGORY_SQ asc , SUBCATEGORY_SQ asc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$voucherList = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($voucherList);
		break;			
		
	case 'execVoucherModify':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$VOUCHER_SQ = getAnyParameter("VOUCHER_SQ","");
		$CATEGORY_SQ = getAnyParameter("CATEGORY_SQ","");
		$SUBCATEGORY_SQ = getAnyParameter("SUBCATEGORY_SQ","");
		$VOUCHER_NAME = getAnyParameter("VOUCHER_NAME","");
		$VOUCHER_TYPE = getAnyParameter("VOUCHER_TYPE","");
		$USE_TYPE = getAnyParameter("USE_TYPE","");
		$PERIOD_TYPE = getAnyParameter("PERIOD_TYPE","");
		$PERIOD_UNIT = getAnyParameter("PERIOD_UNIT","");
		$PERIOD = getAnyParameter("PERIOD","");
		$COUNT_TYPE = getAnyParameter("COUNT_TYPE","");
		$COUNT = getAnyParameter("COUNT","");
		$ENTERLIMIT_DAY = getAnyParameter("ENTERLIMIT_DAY","");
		$ENTERLIMIT_WEEK = getAnyParameter("ENTERLIMIT_WEEK","");
		$PRICE = getAnyParameter("PRICE","");
		$SURTAX_TYPE = getAnyParameter("SURTAX_TYPE","");
		$DISCOUNT_TYPE = getAnyParameter("DISCOUNT_TYPE","");
		$DISCOUNT_RATIO = getAnyParameter("DISCOUNT_RATIO","");
		$DISCOUNT_AMOUNT = getAnyParameter("DISCOUNT_AMOUNT","");
		$SELLINGPRICE = getAnyParameter("SELLINGPRICE","");

		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "상품권설정";
		$SUBCATEGORY = "서브카테고리 수정";
		$ACTION = $CENTER_SQ . " 센터 서브카테고리를 수정하였습니다.";
		$IP = getClientIPv4();

		$database->prepare("
			UPDATE tb_voucher SET VOUCHER_SQ=:VOUCHER_SQ, CATEGORY_SQ=:CATEGORY_SQ, SUBCATEGORY_SQ=:SUBCATEGORY_SQ, 
				VOUCHER_NAME=:VOUCHER_NAME, VOUCHER_TYPE=:VOUCHER_TYPE, USE_TYPE=:USE_TYPE, PERIOD_TYPE=:PERIOD_TYPE, 
				PERIOD=:PERIOD, PERIOD_UNIT=:PERIOD_UNIT, COUNT_TYPE=:COUNT_TYPE, COUNT=:COUNT, ENTERLIMIT_DAY=:ENTERLIMIT_DAY,
				ENTERLIMIT_WEEK=:ENTERLIMIT_WEEK,PRICE=:PRICE, 
				DISCOUNT_TYPE=:DISCOUNT_TYPE, DISCOUNT_RATIO=:DISCOUNT_RATIO, DISCOUNT_AMOUNT=:DISCOUNT_AMOUNT, SELLINGPRICE=:SELLINGPRICE, 
				MODIFIEDID=:MODIFIEDID, MODIFIEDDT=now()
			WHERE VOUCHER_SQ=:VOUCHER_SQ
		");
		$database->bind(':VOUCHER_SQ', $VOUCHER_SQ);
		$database->bind(':CATEGORY_SQ', $CATEGORY_SQ);
		$database->bind(':SUBCATEGORY_SQ', $SUBCATEGORY_SQ);
		$database->bind(':VOUCHER_NAME', $VOUCHER_NAME);
		$database->bind(':VOUCHER_TYPE', $VOUCHER_TYPE);
		$database->bind(':USE_TYPE', $USE_TYPE);
		$database->bind(':PERIOD_TYPE', $PERIOD_TYPE);
		$database->bind(':PERIOD', $PERIOD);
		$database->bind(':PERIOD_UNIT', $PERIOD_UNIT);
		$database->bind(':COUNT_TYPE', $COUNT_TYPE);
		$database->bind(':COUNT', $COUNT);
		$database->bind(':ENTERLIMIT_DAY', $ENTERLIMIT_DAY);
		$database->bind(':ENTERLIMIT_WEEK', $ENTERLIMIT_WEEK);
		$database->bind(':PRICE', $PRICE);
		$database->bind(':DISCOUNT_TYPE', $DISCOUNT_TYPE);
		$database->bind(':DISCOUNT_RATIO', $DISCOUNT_RATIO);
		$database->bind(':DISCOUNT_AMOUNT', $DISCOUNT_AMOUNT);
		$database->bind(':SELLINGPRICE', $SELLINGPRICE);
		$database->bind(':MODIFIEDID', $USER_SQ);
		$database->bind(':VOUCHER_SQ', $VOUCHER_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		
		$database->prepare("
			SELECT VOUCHER_SQ,CENTER_SQ,CATEGORY_SQ,SUBCATEGORY_SQ,VOUCHER_NAME,VOUCHER_TYPE,USE_TYPE,PERIOD_TYPE,PERIOD,PERIOD_UNIT,
					COUNT_TYPE,COUNT,ENTERLIMIT_DAY,ENTERLIMIT_WEEK,PRICE,SURTAX_TYPE,DISCOUNT_TYPE,DISCOUNT_RATIO,DISCOUNT_AMOUNT,SELLINGPRICE,
					REGID,REGDT,MODIFIEDID,MODIFIEDDT
 			FROM tb_voucher
			WHERE CENTER_SQ=:CENTER_SQ and USEYN=1
			ORDER BY CATEGORY_SQ asc , SUBCATEGORY_SQ asc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$voucherList = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($voucherList);
		break;			
		
	case 'execSubCategoryDelete':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$SUBCATEGORY_SQ = getAnyParameter("SUBCATEGORY_SQ","");

		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "상품권설정";
		$SUBCATEGORY = "서브카테고리 삭제";
		$ACTION = $CENTER_SQ . " 센터 서브카테고리를 삭제하였습니다.";
		$IP = getClientIPv4();

		$database->prepare("
			UPDATE tb_subcategory set USEYN=0 where SUBCATEGORY_SQ=:SUBCATEGORY_SQ
		");
		$database->bind(':SUBCATEGORY_SQ', $SUBCATEGORY_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		
		$database->prepare("
			SELECT SUBCATEGORY_SQ,CENTER_SQ,CATEGORY_SQ,SUBCATEGORY_NAME,REGDT,REGID,MODIFIEDDT,MODIFIEDID
 			FROM tb_subcategory
			WHERE CENTER_SQ=:CENTER_SQ and USEYN=1
			ORDER BY SUBCATEGORY_SQ asc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$subcategoryList = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($subcategoryList);
		break;	
		
	case 'execSubCategoryAdd':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$CATEGORY_SQ = getAnyParameter("CATEGORY_SQ","");
		$SUBCATEGORY_NAME = getAnyParameter("SUBCATEGORY_NAME","");

		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "상품권설정";
		$SUBCATEGORY = "서브카테고리 추가";
		$ACTION = $CENTER_SQ . " 서브카테고리를 추가하였습니다.";
		$IP = getClientIPv4();
		
		//NOTICE_SQ,NOTICE_TYPE,NOTICE_TITLE,NOTICE_CONTENTS,CREATEDBY,MODIFIEDBY,CREATEDDT,MODIFIEDDT
		$database->prepare("
			INSERT tb_subcategory ( CENTER_SQ, CATEGORY_SQ, SUBCATEGORY_NAME, REGID, REGDT ) VALUES
			(:CENTER_SQ,:CATEGORY_SQ,:SUBCATEGORY_NAME,:REGID,now() )
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':CATEGORY_SQ', $CATEGORY_SQ);
		$database->bind(':SUBCATEGORY_NAME', $SUBCATEGORY_NAME);
		$database->bind(':REGID', $USER_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		
		$database->prepare("
			SELECT SUBCATEGORY_SQ,CENTER_SQ,CATEGORY_SQ,SUBCATEGORY_NAME,REGDT,REGID,MODIFIEDDT,MODIFIEDID
 			FROM tb_subcategory
			WHERE CENTER_SQ=:CENTER_SQ and USEYN=1
			ORDER BY SUBCATEGORY_SQ asc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$subcategoryList = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($subcategoryList);
		break;	
		
	case 'execSubCategoryModify':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$SUBCATEGORY_SQ = getAnyParameter("SUBCATEGORY_SQ","");
		$SUBCATEGORY_NAME = getAnyParameter("SUBCATEGORY_NAME","");
		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "상품권설정";
		$SUBCATEGORY = "서브카테고리 수정";
		$ACTION = $CENTER_SQ . " 센터 서브카테고리를 수정하였습니다.";
		$IP = getClientIPv4();

		$database->prepare("
			UPDATE tb_subcategory SET SUBCATEGORY_NAME=:SUBCATEGORY_NAME, MODIFIEDID=:MODIFIEDID, MODIFIEDDT=now()
			WHERE SUBCATEGORY_SQ=:SUBCATEGORY_SQ
		");
		$database->bind(':SUBCATEGORY_NAME', $SUBCATEGORY_NAME);
		$database->bind(':MODIFIEDID', $USER_SQ);
		$database->bind(':SUBCATEGORY_SQ', $SUBCATEGORY_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		
		$database->prepare("
			SELECT SUBCATEGORY_SQ,CENTER_SQ,CATEGORY_SQ,SUBCATEGORY_NAME,REGDT,REGID,MODIFIEDDT,MODIFIEDID
 			FROM tb_subcategory
			WHERE CENTER_SQ=:CENTER_SQ and USEYN=1
			ORDER BY SUBCATEGORY_SQ asc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$subcategoryList = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($subcategoryList);
		break;		
		
	case 'execCategoryDelete':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$CATEGORY_SQ = getAnyParameter("CATEGORY_SQ","");

		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "상품권설정";
		$SUBCATEGORY = "카테고리 삭제";
		$ACTION = $CENTER_SQ . " 센터 카테고리를 삭제하였습니다.";
		$IP = getClientIPv4();

		$database->prepare("
			UPDATE tb_category set USEYN=0 where CATEGORY_SQ=:CATEGORY_SQ
		");
		$database->bind(':CATEGORY_SQ', $CATEGORY_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		
		$database->prepare("
			SELECT CATEGORY_SQ,CENTER_SQ,CATEGORY_NAME,REGDT,REGID,MODIFIEDDT,MODIFIEDID
 			FROM tb_category
			WHERE CENTER_SQ=:CENTER_SQ and USEYN=1
			ORDER BY CATEGORY_SQ asc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$categoryList = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($categoryList);
		break;	
		
	case 'execCategoryAdd':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$CATEGORY_NAME = getAnyParameter("CATEGORY_NAME","");

		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "상품권설정";
		$SUBCATEGORY = "카테고리 추가";
		$ACTION = $CENTER_SQ . " 카테고리를 추가하였습니다.";
		$IP = getClientIPv4();
		
		//NOTICE_SQ,NOTICE_TYPE,NOTICE_TITLE,NOTICE_CONTENTS,CREATEDBY,MODIFIEDBY,CREATEDDT,MODIFIEDDT
		$database->prepare("
			INSERT tb_category ( CENTER_SQ, CATEGORY_NAME, REGID, REGDT ) VALUES
			(:CENTER_SQ,:CATEGORY_NAME,:REGID,now() )
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':CATEGORY_NAME', $CATEGORY_NAME);
		$database->bind(':REGID', $USER_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		
		$database->prepare("
			SELECT CATEGORY_SQ,CENTER_SQ,CATEGORY_NAME,REGDT,REGID,MODIFIEDDT,MODIFIEDID
 			FROM tb_category
			WHERE CENTER_SQ=:CENTER_SQ and USEYN=1
			ORDER BY CATEGORY_SQ asc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$categoryList = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($categoryList);
		break;	
		
	case 'execCategoryModify':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$CATEGORY_SQ = getAnyParameter("CATEGORY_SQ","");
		$CATEGORY_NAME = getAnyParameter("CATEGORY_NAME","");
		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "상품권설정";
		$SUBCATEGORY = "카테고리 수정";
		$ACTION = $CENTER_SQ . " 센터 카테고리를 수정하였습니다.";
		$IP = getClientIPv4();

		$database->prepare("
			UPDATE tb_category SET CATEGORY_NAME=:CATEGORY_NAME, MODIFIEDID=:MODIFIEDID, MODIFIEDDT=now()
			WHERE CATEGORY_SQ=:CATEGORY_SQ
		");
		$database->bind(':CATEGORY_NAME', $CATEGORY_NAME);
		$database->bind(':MODIFIEDID', $USER_SQ);
		$database->bind(':CATEGORY_SQ', $CATEGORY_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		
		$database->prepare("
			SELECT CATEGORY_SQ,CENTER_SQ,CATEGORY_NAME,REGDT,REGID,MODIFIEDDT,MODIFIEDID
 			FROM tb_category
			WHERE CENTER_SQ=:CENTER_SQ and USEYN=1
			ORDER BY CATEGORY_SQ asc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$categoryList = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($categoryList);
		break;	
		
	case 'getVoucherList':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "상품권 설정";
		$SUBCATEGORY = "상품권 조회";
		$ACTION = $CENTER_SQ . " 상품권을 조회하였습니다.";
		$IP = getClientIPv4();
		//tb_voucher
		//VOUCHER_SQ,CENTER_SQ,CATEGORY_SQ,SUBCATEGORY_SQ,VOUCHER_NAME,VOUCHER_TYPE,USE_TYPE,PERIOD_TYPE,PERIOD,COUNT_TYPE,COUNT,PRICE,SURTAX_TYPE,DISCOUNT_TYPE,DISCOUNT_RATIO,DISCOUNT_AMOUNT,SELLINGPRICE

		$database->prepare("
			SELECT VOUCHER_SQ,CENTER_SQ,CATEGORY_SQ,SUBCATEGORY_SQ,VOUCHER_NAME,VOUCHER_TYPE,USE_TYPE,PERIOD_TYPE,PERIOD,PERIOD_UNIT,
					COUNT_TYPE,COUNT,ENTERLIMIT_DAY,ENTERLIMIT_WEEK,PRICE,SURTAX_TYPE,DISCOUNT_TYPE,DISCOUNT_RATIO,DISCOUNT_AMOUNT,SELLINGPRICE,
					REGID,REGDT,MODIFIEDID,MODIFIEDDT
 			FROM tb_voucher
			WHERE CENTER_SQ=:CENTER_SQ and USEYN=1
			ORDER BY CATEGORY_SQ asc , SUBCATEGORY_SQ asc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$voucherlist = json_encode($rows);

		//tb_category
		//CATEGORY_SQ,CENTER_SQ,CATEGORY_NAME,REGDT,REGID,MODIFIEDDT,MODIFIEDID
		$database->prepare("
			SELECT CATEGORY_SQ,CENTER_SQ,CATEGORY_NAME,REGDT,REGID,MODIFIEDDT,MODIFIEDID
 			FROM tb_category
			WHERE CENTER_SQ=:CENTER_SQ and USEYN=1
			ORDER BY CATEGORY_SQ asc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$categorylist = json_encode($rows);

		//tb_subcategory
		//SUBCATEGORY_SQ,CENTER_SQ,CATEGORY_SQ,SUBCATEGORY_NAME,REGDT,REGID,MODIFIEDDT,MODIFIEDID
		$database->prepare("
			SELECT SUBCATEGORY_SQ,CENTER_SQ,CATEGORY_SQ,SUBCATEGORY_NAME,REGDT,REGID,MODIFIEDDT,MODIFIEDID
 			FROM tb_subcategory
			WHERE CENTER_SQ=:CENTER_SQ and USEYN=1
			ORDER BY SUBCATEGORY_SQ asc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$subcategorylist = json_encode($rows);
		
		$vouchertype = getCommonCode('CD004', $database);
		$usetype = getCommonCode('CD005', $database);
		$periodtype = getCommonCode('CD006', $database);
		$counttype = getCommonCode('CD007', $database);
		$surtaxtype = getCommonCode('CD008', $database);
		$discounttype = getCommonCode('CD009', $database);
		$periodunit = getCommonCode('CD010', $database);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($voucherlist.'|'.$categorylist.'|'.$subcategorylist.'|'.$vouchertype.'|'.$usetype.'|'.$periodtype.'|'.$periodunit.'|'.$counttype.'|'.$surtaxtype.'|'.$discounttype);
		break;	
		
	case 'execNoticeDelete':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$NOTICE_SQ = getAnyParameter("NOTICE_SQ","");

		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "공지사항";
		$SUBCATEGORY = "공지사항 삭제";
		$ACTION = $CENTER_SQ . " 센터 공지사항을 삭제하였습니다.";
		$IP = getClientIPv4();

		$database->prepare("
			UPDATE tb_notice set USEYN=0 where NOTICE_SQ=:NOTICE_SQ
		");
		$database->bind(':NOTICE_SQ', $NOTICE_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		
		$database->prepare("
			SELECT NOTICE_SQ,NOTICE_TYPE,NOTICE_TITLE,NOTICE_CONTENTS,COUNT, CREATEDBY,MODIFIEDBY,CREATEDDT,MODIFIEDDT
 			FROM tb_notice
			WHERE CENTER_SQ=:CENTER_SQ and USEYN=1
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$noticeList = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($noticeList);
		break;	
		
	case 'getNoticeList':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$NOTICE_TYPE = getAnyParameter("NOTICE_TYPE",-1);
		
		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "공지사항";
		$SUBCATEGORY = "공지사항 조회";
		$ACTION = $CENTER_SQ . " 공지사항을 조회하였습니다.";
		$IP = getClientIPv4();
		
		$WHERE = '';
		if ($NOTICE_TYPE!= -1)
		{
			$WHERE = ' and NOTICE_TYPE='.$NOTICE_TYPE;
		}
		
		$database->prepare("
			SELECT NOTICE_SQ,NOTICE_TYPE,NOTICE_TITLE,NOTICE_CONTENTS,COUNT, CREATEDBY,MODIFIEDBY,CREATEDDT,MODIFIEDDT
 			FROM tb_notice
			WHERE CENTER_SQ=:CENTER_SQ and USEYN=1
			".$WHERE
		);
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$noticeList = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($noticeList);
		break;	
		
	case 'execNoticeAdd':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$NOTICE_TYPE = getAnyParameter("NOTICE_TYPE","");
		$NOTICE_TITLE = getAnyParameter("NOTICE_TITLE","");
		$NOTICE_CONTENTS = str_replace("\'", "\\\'", getAnyParameter("NOTICE_CONTENTS",""));

		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "공지사항";
		$SUBCATEGORY = "공지사항 추가";
		$ACTION = $CENTER_SQ . " 공지사항을 추가하였습니다.";
		$IP = getClientIPv4();
		
		//NOTICE_SQ,NOTICE_TYPE,NOTICE_TITLE,NOTICE_CONTENTS,CREATEDBY,MODIFIEDBY,CREATEDDT,MODIFIEDDT
		$database->prepare("
			INSERT tb_notice ( CENTER_SQ, NOTICE_TYPE,  NOTICE_TITLE, NOTICE_CONTENTS, CREATEDBY, CREATEDDT ) VALUES
			(:CENTER_SQ,:NOTICE_TYPE,:NOTICE_TITLE,:NOTICE_CONTENTS,:CREATEDBY,now() )
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':NOTICE_TYPE', $NOTICE_TYPE);
		$database->bind(':NOTICE_TITLE', $NOTICE_TITLE);
		$database->bind(':NOTICE_CONTENTS', $NOTICE_CONTENTS);
		$database->bind(':CREATEDBY', $USER_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		
		$database->prepare("
			SELECT NOTICE_SQ,NOTICE_TYPE,NOTICE_TITLE,NOTICE_CONTENTS,COUNT, CREATEDBY,MODIFIEDBY,CREATEDDT,MODIFIEDDT
 			FROM tb_notice
			WHERE CENTER_SQ=:CENTER_SQ and USEYN=1
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$noticeList = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($noticeList);
		break;	
		
	case 'execNoticeModify':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$NOTICE_SQ = getAnyParameter("NOTICE_SQ","");
		$NOTICE_TYPE = getAnyParameter("NOTICE_TYPE","");
		$NOTICE_TITLE = getAnyParameter("NOTICE_TITLE","");
		$NOTICE_CONTENTS = str_replace("\'", "\\\'", getAnyParameter("NOTICE_CONTENTS",""));

		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "공지사항";
		$SUBCATEGORY = "공지사항 수정";
		$ACTION = $CENTER_SQ . " 센터 공지사항을 수정하였습니다.";
		$IP = getClientIPv4();

		$database->prepare("
			UPDATE tb_notice SET NOTICE_TYPE=:NOTICE_TYPE,  NOTICE_TITLE=:NOTICE_TITLE, NOTICE_CONTENTS=:NOTICE_CONTENTS,
				MODIFIEDBY=:MODIFIEDBY, MODIFIEDDT=now()
			WHERE NOTICE_SQ=:NOTICE_SQ
		");
		$database->bind(':NOTICE_TYPE', $NOTICE_TYPE);
		$database->bind(':NOTICE_TITLE', $NOTICE_TITLE);
		$database->bind(':NOTICE_CONTENTS', $NOTICE_CONTENTS);
		$database->bind(':MODIFIEDBY', $USER_SQ);
		$database->bind(':NOTICE_SQ', $NOTICE_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		
		$database->prepare("
			SELECT NOTICE_SQ,NOTICE_TYPE,NOTICE_TITLE,NOTICE_CONTENTS,COUNT, CREATEDBY,MODIFIEDBY,CREATEDDT,MODIFIEDDT
 			FROM tb_notice
			WHERE CENTER_SQ=:CENTER_SQ and USEYN=1
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$noticeList = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($noticeList);
		break;	
		
	case 'execCompanyInfoModify':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$COMPANY_SQ = getAnyParameter("COMPANY_SQ","");
		$COMPANY_NAME = getAnyParameter("COMPANY_NAME","");
		$COMPANY_CEONAME = getAnyParameter("COMPANY_CEONAME","");
		$COMPANY_REGNO = getAnyParameter("COMPANY_REGNO","");
		$COMPANY_TYPE = getAnyParameter("COMPANY_TYPE","");
		$COMPANY_CONTIDION = getAnyParameter("COMPANY_CONTIDION","");
		$ADDRESS_PROVINCE = getAnyParameter("ADDRESS_PROVINCE","");
		$ADDRESS_CITY = getAnyParameter("ADDRESS_CITY","");
		$ADDRESS_DETAIL = getAnyParameter("ADDRESS_DETAIL","");

		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "센터 설정 정보";
		$SUBCATEGORY = "센터 설정 정보 저장";
		$ACTION = $CENTER_SQ . " 센터 정보를 저장하였습니다.";
		$IP = getClientIPv4();
		
		$database->prepare("
			UPDATE tb_company SET COMPANY_NAME=:COMPANY_NAME,  COMPANY_CEONAME=:COMPANY_CEONAME, COMPANY_REGNO=:COMPANY_REGNO, COMPANY_TYPE=:COMPANY_TYPE, COMPANY_CONTIDION=:COMPANY_CONTIDION, 
				ADDRESS_PROVINCE=:ADDRESS_PROVINCE, ADDRESS_CITY=:ADDRESS_CITY, ADDRESS_DETAIL=:ADDRESS_DETAIL
			WHERE COMPANY_SQ=:COMPANY_SQ, 
		");
		$database->bind(':COMPANY_NAME', $COMPANY_NAME);
		$database->bind(':COMPANY_CEONAME', $COMPANY_CEONAME);
		$database->bind(':COMPANY_REGNO', $COMPANY_REGNO);
		$database->bind(':COMPANY_TYPE', $COMPANY_TYPE);
		$database->bind(':COMPANY_CONTIDION', $COMPANY_CONTIDION);
		$database->bind(':ADDRESS_PROVINCE', $ADDRESS_PROVINCE);
		$database->bind(':ADDRESS_CITY', $ADDRESS_CITY);
		$database->bind(':ADDRESS_DETAIL', $ADDRESS_DETAIL);
		$database->bind(':COMPANY_SQ', $COMPANY_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		
		$database->prepare("
			SELECT CENTER_SQ,CENTER_NM,MANAGER_NAME,MANAGER_PHONE,MANAGER_EMAIL,CENTER_PHONE,CENTER_FAX,
					CENTER_HOMEPAGE,CENTER_SNS,CENTER_VISIT_DETAIL,CENTER_IMAGE_FILE,CENTER_DESCRIPTION
 			FROM tb_center
			WHERE CENTER_SQ=:CENTER_SQ
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$reservSetting = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($reservSetting);
		break;	
		
	case 'execCenterInfoModify':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$CENTER_NM = getAnyParameter("CENTER_NM","");
		$CENTER_HOMEPAGE = getAnyParameter("CENTER_HOMEPAGE","");
		$MANAGER_NAME = getAnyParameter("MANAGER_NAME","");
		$MANAGER_PHONE = getAnyParameter("MANAGER_PHONE","");
		$MANAGER_EMAIL = getAnyParameter("MANAGER_EMAIL","");
		$CENTER_PHONE = getAnyParameter("CENTER_PHONE","");
		$CENTER_FAX = getAnyParameter("CENTER_FAX","");
		$CENTER_SNS = getAnyParameter("CENTER_SNS","");
		$CENTER_VISIT_DETAIL = getAnyParameter("CENTER_VISIT_DETAIL","");
		$ADDRESS_PROVINCE = getAnyParameter("ADDRESS_PROVINCE","");
		$ADDRESS_CITY = getAnyParameter("ADDRESS_CITY","");
		$ADDRESS_DETAIL = getAnyParameter("ADDRESS_DETAIL","");
		//$CENTER_IMAGE_FILE = getAnyParameter("CENTER_IMAGE_FILE","");
		$CENTER_DESCRIPTION = getAnyParameter("CENTER_DESCRIPTION","");
		
		$CENTER_IMAGE_FILE = "";
			
		// File Manipulation
		if (isset($_FILES["CENTER_IMAGE_FILE"]))
		{
			$upload_dir = "uploadfiles/";
			$upload_fileheader = $upload_dir."centerimg_".date("YmdHis")."_";

			$upload_file = $upload_fileheader.str_replace(" ", "_", $_FILES["CENTER_IMAGE_FILE"]["name"]);
			$filename=iconv("utf-8","CP949",$upload_file);
			$type = $_FILES["CENTER_IMAGE_FILE"]["type"];
			$arr = explode('/',$type);

			if ($arr[0] == "image" && move_uploaded_file($_FILES["CENTER_IMAGE_FILE"]["tmp_name"], $filename))
			{
				$CENTER_IMAGE_FILE = $upload_file;
			}
		}

		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "센터 설정 정보";
		$SUBCATEGORY = "센터 설정 정보 저장";
		$ACTION = $CENTER_SQ . " 센터 정보를 저장하였습니다.";
		$IP = getClientIPv4();
		
		$database->prepare("
			UPDATE tb_center SET CENTER_NM=:CENTER_NM,  MANAGER_NAME=:MANAGER_NAME, MANAGER_PHONE=:MANAGER_PHONE, MANAGER_EMAIL=:MANAGER_EMAIL, 
				CENTER_HOMEPAGE=:CENTER_HOMEPAGE, CENTER_PHONE=:CENTER_PHONE, CENTER_FAX=:CENTER_FAX, 
				CENTER_SNS=:CENTER_SNS, CENTER_VISIT_DETAIL=:CENTER_VISIT_DETAIL, CENTER_IMAGE_FILE=:CENTER_IMAGE_FILE, CENTER_DESCRIPTION=:CENTER_DESCRIPTION,
				ADDRESS_PROVINCE=:ADDRESS_PROVINCE, ADDRESS_CITY=:ADDRESS_CITY, ADDRESS_DETAIL=:ADDRESS_DETAIL
			WHERE CENTER_SQ=:CENTER_SQ
		");
		$database->bind(':CENTER_NM', $CENTER_NM);
		$database->bind(':MANAGER_NAME', $MANAGER_NAME);
		$database->bind(':MANAGER_PHONE', $MANAGER_PHONE);
		$database->bind(':MANAGER_EMAIL', $MANAGER_EMAIL);
		$database->bind(':CENTER_HOMEPAGE', $CENTER_HOMEPAGE);
		$database->bind(':CENTER_PHONE', $CENTER_PHONE);
		$database->bind(':CENTER_FAX', $CENTER_FAX);
		$database->bind(':CENTER_SNS', $CENTER_SNS);
		$database->bind(':CENTER_VISIT_DETAIL', $CENTER_VISIT_DETAIL);
		$database->bind(':CENTER_IMAGE_FILE', $CENTER_IMAGE_FILE);
		$database->bind(':CENTER_DESCRIPTION', $CENTER_DESCRIPTION);
		$database->bind(':ADDRESS_PROVINCE', $ADDRESS_PROVINCE);
		$database->bind(':ADDRESS_CITY', $ADDRESS_CITY);
		$database->bind(':ADDRESS_DETAIL', $ADDRESS_DETAIL);
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		
		$database->prepare("
			SELECT CENTER_SQ,CENTER_NM,MANAGER_NAME,MANAGER_PHONE,MANAGER_EMAIL,CENTER_PHONE,CENTER_FAX,ADDRESS_PROVINCE,ADDRESS_CITY,ADDRESS_DETAIL,
					CENTER_HOMEPAGE,CENTER_SNS,CENTER_VISIT_DETAIL,CENTER_IMAGE_FILE,CENTER_DESCRIPTION
 			FROM tb_center
			WHERE CENTER_SQ=:CENTER_SQ
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$reservSetting = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($reservSetting);
		break;	
		
	case 'execCenterOperTimeModify':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$MON_OPER_TYPE = getAnyParameter("MON_OPER_TYPE","");
		$TUE_OPER_TYPE = getAnyParameter("TUE_OPER_TYPE","");
		$WED_OPER_TYPE = getAnyParameter("WED_OPER_TYPE","");
		$THU_OPER_TYPE = getAnyParameter("THU_OPER_TYPE","");
		$FRI_OPER_TYPE = getAnyParameter("FRI_OPER_TYPE","");
		$SAT_OPER_TYPE = getAnyParameter("SAT_OPER_TYPE","");
		$SUN_OPER_TYPE = getAnyParameter("SUN_OPER_TYPE","");
		$MON_OPERTIME = getAnyParameter("MON_OPERTIME","");
		$TUE_OPERTIME = getAnyParameter("TUE_OPERTIME","");
		$WED_OPERTIME = getAnyParameter("WED_OPERTIME","");
		$THU_OPERTIME = getAnyParameter("THU_OPERTIME","");
		$FRI_OPERTIME = getAnyParameter("FRI_OPERTIME","");
		$SAT_OPERTIME = getAnyParameter("SAT_OPERTIME","");
		$SUN_OPERTIME = getAnyParameter("SUN_OPERTIME","");

		error_log($MON_OPERTIME);
		error_log($TUE_OPERTIME);
		error_log($MON_OPERTIME);

		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "운영시간 설정 정보";
		$SUBCATEGORY = "운영시간 설정 정보 저장";
		$ACTION = $CENTER_SQ . " 센터의 운영시간설정 정보를 저장하였습니다.";
		$IP = getClientIPv4();
		
		$database->prepare("
			INSERT tb_center_oper_time (CENTER_SQ,MON_OPER_TYPE,TUE_OPER_TYPE,WED_OPER_TYPE,THU_OPER_TYPE,FRI_OPER_TYPE,SAT_OPER_TYPE,SUN_OPER_TYPE,
					MON_OPERTIME,TUE_OPERTIME,WED_OPERTIME,THU_OPERTIME,FRI_OPERTIME,SAT_OPERTIME,SUN_OPERTIME,CREATEDBY,CREATED) VALUES
			(:CENTER_SQ,
				:MON_OPER_TYPE,:TUE_OPER_TYPE,:WED_OPER_TYPE,:THU_OPER_TYPE,:FRI_OPER_TYPE,:SAT_OPER_TYPE,:SUN_OPER_TYPE,
				:MON_OPERTIME,:TUE_OPERTIME,:WED_OPERTIME,:THU_OPERTIME,:FRI_OPERTIME,:SAT_OPERTIME,:SUN_OPERTIME,
				:CREATEDBY,now())
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':MON_OPER_TYPE', $MON_OPER_TYPE);
		$database->bind(':TUE_OPER_TYPE', $TUE_OPER_TYPE);
		$database->bind(':WED_OPER_TYPE', $WED_OPER_TYPE);
		$database->bind(':THU_OPER_TYPE', $THU_OPER_TYPE);
		$database->bind(':FRI_OPER_TYPE', $FRI_OPER_TYPE);
		$database->bind(':SAT_OPER_TYPE', $SAT_OPER_TYPE);
		$database->bind(':SUN_OPER_TYPE', $SUN_OPER_TYPE);
		$database->bind(':MON_OPERTIME', $MON_OPERTIME);
		$database->bind(':TUE_OPERTIME', $TUE_OPERTIME);
		$database->bind(':WED_OPERTIME', $WED_OPERTIME);
		$database->bind(':THU_OPERTIME', $THU_OPERTIME);
		$database->bind(':FRI_OPERTIME', $FRI_OPERTIME);
		$database->bind(':SAT_OPERTIME', $SAT_OPERTIME);
		$database->bind(':SUN_OPERTIME', $SUN_OPERTIME);
		$database->bind(':CREATEDBY', $USER_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		
		$database->prepare("
			SELECT OPERTIME_SQ,CENTER_SQ,MON_OPER_TYPE,TUE_OPER_TYPE,WED_OPER_TYPE,THU_OPER_TYPE,FRI_OPER_TYPE,SAT_OPER_TYPE,SUN_OPER_TYPE,
					MON_OPERTIME,TUE_OPERTIME,WED_OPERTIME,THU_OPERTIME,FRI_OPERTIME,SAT_OPERTIME,SUN_OPERTIME,CREATEDBY,CREATED
 			FROM tb_center_oper_time
			WHERE CENTER_SQ=:CENTER_SQ ORDER BY OPERTIME_SQ DESC LIMIT 1
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$reservSetting = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($reservSetting);
		break;	
		
	case 'execHolidayDelete':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$HOLIDAY_SQ = getAnyParameter("HOLIDAY_SQ","");
		
		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "휴일 정보";
		$SUBCATEGORY = "휴일 정보 삭제";
		$ACTION = $CENTER_SQ . " 휴일 정보가 삭제되었습니다.";
		$IP = getClientIPv4();
			
		$database->prepare("
			DELETE from tb_holiday where HOLIDAY_SQ = :HOLIDAY_SQ
		");
		$database->bind(':HOLIDAY_SQ', $HOLIDAY_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		
		$database->prepare("
			SELECT HOLIDAY_SQ,CENTER_SQ,HOLIDAY,HOLIDAY_NAME
 			FROM tb_holiday
			WHERE CENTER_SQ=:CENTER_SQ AND MANAGER_SQ=0 ORDER BY HOLIDAY ASC
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$holidaylist = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit(json_encode($holidaylist));
		break;
		
	case 'execHolidayAdd':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$HOLIDAY = getAnyParameter("HOLIDAY","");
		$HOLIDAY_NAME = getAnyParameter("HOLIDAY_NAME","");
		
		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "휴일 정보";
		$SUBCATEGORY = "휴일 정보 추가";
		$ACTION = $CENTER_SQ . " 휴일 정보가 추가되었습니다.";
		$IP = getClientIPv4();
			
		$database->prepare("
			INSERT tb_holiday (CENTER_SQ, HOLIDAY, HOLIDAY_NAME) values
				(:CENTER_SQ, :HOLIDAY, :HOLIDAY_NAME)
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':HOLIDAY', $HOLIDAY);
		$database->bind(':HOLIDAY_NAME', $HOLIDAY_NAME);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}

		$database->prepare("
			SELECT HOLIDAY_SQ,CENTER_SQ,HOLIDAY,HOLIDAY_NAME
 			FROM tb_holiday
			WHERE CENTER_SQ=:CENTER_SQ AND MANAGER_SQ=0 ORDER BY HOLIDAY ASC
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$holidaylist = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit(json_encode($holidaylist));
		break;
				
	case 'getCenterInfo':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$CENTER_SQ = $session->user["CENTER_SQ"];

		// 기본값 설정
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "센터 정보";
		$SUBCATEGORY = "센터 정보 취득";
		$ACTION = $CENTER_SQ . " 센터의 정보를 취득하였습니다.";
		$IP = getClientIPv4();
			
		$database->prepare("
			SELECT CENTER_SQ,CENTER_NM,MANAGER_NAME,MANAGER_PHONE,MANAGER_EMAIL,CENTER_PHONE,CENTER_FAX,ADDRESS_PROVINCE,ADDRESS_CITY,ADDRESS_DETAIL,
					CENTER_HOMEPAGE,CENTER_SNS,CENTER_VISIT_DETAIL,CENTER_IMAGE_FILE,CENTER_DESCRIPTION
 			FROM tb_center
			WHERE CENTER_SQ=:CENTER_SQ
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$centerinfo = json_encode($rows);

		$database->prepare("
			SELECT ROOM_SQ,ROOM_NAME,ROOM_DESC
 			FROM tb_room
			WHERE CENTER_SQ=:CENTER_SQ and USE_YN=1 ORDER BY ROOM_NAME ASC
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$roomlist = json_encode($rows);
		
		$database->prepare("
			SELECT a.COMPANY_SQ,a.COMPANY_NAME,a.COMPANY_CEONAME,a.COMPANY_REGNO,a.COMPANY_TYPE,a.COMPANY_CONTIDION,
					a.ADDRESS_PROVINCE,a.ADDRESS_CITY,a.ADDRESS_DETAIL,a.PHONE,a.FAX,a.HOMEPAGE,a.SNS,a.VISIT_DETAIL
 			FROM tb_company a inner join tb_center b on a.COMPANY_SQ=b.COMPANY_SQ
			WHERE b.CENTER_SQ=:CENTER_SQ
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$companyinfo = json_encode($rows);
			
		$database->prepare("
			SELECT OPERTIME_SQ,CENTER_SQ,MON_OPER_TYPE,TUE_OPER_TYPE,WED_OPER_TYPE,THU_OPER_TYPE,FRI_OPER_TYPE,SAT_OPER_TYPE,SUN_OPER_TYPE,
					MON_OPERTIME,TUE_OPERTIME,WED_OPERTIME,THU_OPERTIME,FRI_OPERTIME,SAT_OPERTIME,SUN_OPERTIME,CREATEDBY,CREATED
 			FROM tb_center_oper_time
			WHERE CENTER_SQ=:CENTER_SQ ORDER BY OPERTIME_SQ DESC LIMIT 1
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$opertimeinfo = json_encode($rows);
			
		$database->prepare("
			SELECT HOLIDAY_SQ,CENTER_SQ,HOLIDAY,HOLIDAY_NAME
 			FROM tb_holiday
			WHERE CENTER_SQ=:CENTER_SQ AND MANAGER_SQ=0 ORDER BY HOLIDAY ASC
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$holidaylist = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($centerinfo.'|'.$companyinfo.'|'.$opertimeinfo.'|'.$holidaylist.'|'.$roomlist);
		break;	
				
	case 'getCenterList':
		// 기본값 설정
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "센터 리스트";
		$SUBCATEGORY = "센터 리스트 취득";
		$ACTION = " 센터의 리스트를 취득하였습니다.";
		$IP = getClientIPv4();
			
		$database->prepare("
			SELECT CENTER_SQ,CENTER_NM,MANAGER_NAME,MANAGER_PHONE,MANAGER_EMAIL,CENTER_PHONE,CENTER_FAX,ADDRESS_PROVINCE,ADDRESS_CITY,ADDRESS_DETAIL,
					CENTER_HOMEPAGE,CENTER_SNS,CENTER_VISIT_DETAIL,CENTER_IMAGE_FILE,CENTER_DESCRIPTION
 			FROM tb_center
			WHERE ISUSE=1
		");
		$database->execute();

		$rows = $database->fetchAll();
		$centerlist = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($centerlist);
		break;	

	case 'execReservationSettingSave':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$PSN_RESERV_TYPE = getAnyParameter("PSN_RESERV_TYPE","");
		$PSN_RESERV_TIME = getAnyParameter("PSN_RESERV_TIME","");
		$PSN_MOD_TYPE = getAnyParameter("PSN_MOD_TYPE","");
		$PSN_MOD_TIME = getAnyParameter("PSN_MOD_TIME","");
		$PSN_AUTO_ABSENCE = getAnyParameter("PSN_AUTO_ABSENCE","");
		$PSN_ABSENCE_TICKET = getAnyParameter("PSN_ABSENCE_TICKET","");
		$GRP_RESERV_TYPE = getAnyParameter("GRP_RESERV_TYPE","");
		$GRP_RESERV_TIME = getAnyParameter("GRP_RESERV_TIME","");
		$GRP_MOD_TYPE = getAnyParameter("GRP_MOD_TYPE","");
		$GRP_MOD_TIME = getAnyParameter("GRP_MOD_TIME","");
		$GRP_AUTO_ABSENCE = getAnyParameter("GRP_AUTO_ABSENCE","");
		$GRP_ABSENCE_TICKET = getAnyParameter("GRP_ABSENCE_TICKET","");

		// 기본값 설정
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "예약 설정 정보";
		$SUBCATEGORY = "예약 설정 정보 저장";
		$ACTION = $CENTER_SQ . " 센터의 예약설정 정보를 저장하였습니다.";
		$IP = getClientIPv4();
		
		$database->prepare("
			INSERT tb_reservation_setting (CENTER_SQ,
				PSN_RESERV_TYPE,PSN_RESERV_TIME,PSN_MOD_TYPE,PSN_MOD_TIME,PSN_AUTO_ABSENCE,PSN_ABSENCE_TICKET,
				GRP_RESERV_TYPE,GRP_RESERV_TIME,GRP_MOD_TYPE,GRP_MOD_TIME,GRP_AUTO_ABSENCE,GRP_ABSENCE_TICKET,
				CREATEDBY,CREATED) VALUES
			(:CENTER_SQ,
				:PSN_RESERV_TYPE,:PSN_RESERV_TIME,:PSN_MOD_TYPE,:PSN_MOD_TIME,:PSN_AUTO_ABSENCE,:PSN_ABSENCE_TICKET,
				:GRP_RESERV_TYPE,:GRP_RESERV_TIME,:GRP_MOD_TYPE,:GRP_MOD_TIME,:GRP_AUTO_ABSENCE,:GRP_ABSENCE_TICKET,
				:CREATEDBY,now())
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':PSN_RESERV_TYPE', $PSN_RESERV_TYPE);
		$database->bind(':PSN_RESERV_TIME', $PSN_RESERV_TIME);
		$database->bind(':PSN_MOD_TYPE', $PSN_MOD_TYPE);
		$database->bind(':PSN_MOD_TIME', $PSN_MOD_TIME);
		$database->bind(':PSN_AUTO_ABSENCE', $PSN_AUTO_ABSENCE);
		$database->bind(':PSN_ABSENCE_TICKET', $PSN_ABSENCE_TICKET);
		$database->bind(':GRP_RESERV_TYPE', $GRP_RESERV_TYPE);
		$database->bind(':GRP_RESERV_TIME', $GRP_RESERV_TIME);
		$database->bind(':GRP_MOD_TYPE', $GRP_MOD_TYPE);
		$database->bind(':GRP_MOD_TIME', $GRP_MOD_TIME);
		$database->bind(':GRP_AUTO_ABSENCE', $GRP_AUTO_ABSENCE);
		$database->bind(':GRP_ABSENCE_TICKET', $GRP_ABSENCE_TICKET);
		$database->bind(':CREATEDBY', $USER_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}
		
		$database->prepare("
			SELECT RESERVSETTING_SQ,CENTER_SQ,
				PSN_RESERV_TYPE,PSN_RESERV_TIME,PSN_MOD_TYPE,PSN_MOD_TIME,PSN_AUTO_ABSENCE,PSN_ABSENCE_TICKET,
				GRP_RESERV_TYPE,GRP_RESERV_TIME,GRP_MOD_TYPE,GRP_MOD_TIME,GRP_AUTO_ABSENCE,GRP_ABSENCE_TICKET,
				CREATEDBY,CREATED
 			FROM tb_reservation_setting
			WHERE CENTER_SQ=:CENTER_SQ ORDER BY RESERVSETTING_SQ DESC LIMIT 1
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$reservSetting = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($reservSetting);
		break;	
				
	case 'getReservationSetting':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$CREATED = getAnyParameter("CREATED","");

		// 기본값 설정
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "예약 설정 정보";
		$SUBCATEGORY = "예약 설정 정보 취득";
		$ACTION = $CENTER_SQ . " 센터의 예약설정 정보를 취득하였습니다.";
		$IP = getClientIPv4();
			
		$database->prepare("
			SELECT RESERVSETTING_SQ,CENTER_SQ,
				PSN_RESERV_TYPE,PSN_RESERV_TIME,PSN_MOD_TYPE,PSN_MOD_TIME,PSN_AUTO_ABSENCE,PSN_ABSENCE_TICKET,
				GRP_RESERV_TYPE,GRP_RESERV_TIME,GRP_MOD_TYPE,GRP_MOD_TIME,GRP_AUTO_ABSENCE,GRP_ABSENCE_TICKET,
				CREATEDBY,CREATED
 			FROM tb_reservation_setting
			WHERE CENTER_SQ=:CENTER_SQ AND CREATED < :CREATED ORDER BY RESERVSETTING_SQ DESC LIMIT 1
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':CREATED', $CREATED);
		$database->execute();

		$rows = $database->fetchAll();
		$reservSetting = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($reservSetting);
		break;	
		
	case 'getCurrentReservationSetting':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$CENTER_SQ = $session->user["CENTER_SQ"];

		// 기본값 설정
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "예약 설정 정보";
		$SUBCATEGORY = "예약 설정 정보 취득";
		$ACTION = $CENTER_SQ . " 센터의 예약설정 정보를 취득하였습니다.";
		$IP = getClientIPv4();
			
		$database->prepare("
			SELECT RESERVSETTING_SQ,CENTER_SQ,
				PSN_RESERV_TYPE,PSN_RESERV_TIME,PSN_MOD_TYPE,PSN_MOD_TIME,PSN_AUTO_ABSENCE,PSN_ABSENCE_TICKET,
				GRP_RESERV_TYPE,GRP_RESERV_TIME,GRP_MOD_TYPE,GRP_MOD_TIME,GRP_AUTO_ABSENCE,GRP_ABSENCE_TICKET,
				CREATEDBY,CREATED
 			FROM tb_reservation_setting
			WHERE CENTER_SQ=:CENTER_SQ ORDER BY RESERVSETTING_SQ DESC LIMIT 1
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$reservSetting = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($reservSetting);
		break;
		
	case 'EditManagerInfo':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$MANAGER_SQ = getAnyParameter("USER_SQ","");
		$USER_NM = getAnyParameter("USER_NM","");
		$GENDER = getAnyParameter("GENDER","");
		$PHONE_NO = getAnyParameter("PHONE_NO","");
		$BIRTH_DT = getAnyParameter("BIRTH_DT","");
		$ADDRESS = getAnyParameter("ADDRESS","");
		$EMAIL = getAnyParameter("EMAIL","");

		// 기본값 설정
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "임직원 정보";
		$SUBCATEGORY = "임직원 정보 수정";
		$ACTION = $MANAGER_SQ . " 임직원의 정보가 수정되었습니다.";
		$IP = getClientIPv4();
			
		$database->prepare("
			UPDATE tb_user SET 
			USER_NM = :USER_NM, 
			GENDER = :GENDER, 
			PHONE_NO = :PHONE_NO, 
			BIRTH_DT = :BIRTH_DT, 
			ADDRESS = :ADDRESS, 
			EMAIL = :EMAIL
			where USER_SQ=:MANAGER_SQ
		");
		$database->bind(':USER_NM', $USER_NM);
		$database->bind(':GENDER', $GENDER);
		$database->bind(':PHONE_NO', $PHONE_NO);
		$database->bind(':BIRTH_DT', $BIRTH_DT);
		$database->bind(':ADDRESS', $ADDRESS);
		$database->bind(':EMAIL', $EMAIL);
		$database->bind(':MANAGER_SQ', $MANAGER_SQ);
		$database->execute();

		$result = '1';

		if ($database->rowCount() < 1) { 
			$result = '0';
			exit($result);
		}

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($result);
		break;
		
	case 'getManagerInfo': // 사용자 리스트 취득 
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		// 파라메터 취득
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$MANAGER_SQ = getAnyParameter("USER_SQ",0);

		// 기본값 설정
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "멤버리스트";
		$SUBCATEGORY = "";
		$ACTION = $MANAGER_SQ."사용자 상세정보를 조회하였습니다.";
		$IP = getClientIPv4();
		
		// DB 조회
		$database->prepare("
			select USER_SQ,USERID,CENTER_SQ,USER_NM,PHONE_NO,GENDER,ADDRESS,EMAIL,BIRTH_DT, REG_DT, GRADE, USERIMAGE,
						WORKCATEGORY, WORKTYPE, WORKSTARTDATE, WORKENDDATE, WORKSTATUS, COMMENT, LAST_DT, ISUSE
					from tb_user
					where USER_SQ=:USER_SQ  and GRADE>1 and ISUSE=1 order by REG_DT desc
		");
		$database->bind(':USER_SQ', $MANAGER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$managerinfo = json_encode($rows);
		
		// 담당 회원 정보
		$database->prepare("
		
					select a.USER_SQ,a.CENTER_SQ,a.USER_NM,a.PHONE_NO,a.GENDER,a.ADDRESS,a.EMAIL,a.BIRTH_DT, a.REG_DT, a.GRADE,a.USERIMAGE, a.ISUSE,
						a.REG_DT, a.LAST_VISIT_DT, COMMENT, LAST_DT
					from tb_user a 
					where a.TRAINER=:USER_SQ order by REG_DT desc		
					
					");
		$database->bind(':USER_SQ', $MANAGER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$userlist = json_encode($rows);
		
		// 담당 회원 정보
		$database->prepare("
		
					select a.USER_SQ,a.CENTER_SQ,a.USER_NM,a.PHONE_NO,a.GENDER,a.ADDRESS,a.EMAIL,a.BIRTH_DT, a.REG_DT, a.GRADE,a.USERIMAGE, a.REG_DT, a.LAST_VISIT_DT,
						(SELECT MAX(USE_LASTDATE) FROM tb_user_voucher WHERE MEMBER_SQ=a.USER_SQ)  USE_LASTDATE,
						(SELECT COUNT(USE_LASTDATE) FROM tb_user_voucher WHERE VOUCHER_TYPE=1 AND MEMBER_SQ=a.USER_SQ) PERSONAL_VOUCHER,
						(SELECT COUNT(USE_LASTDATE) FROM tb_user_voucher WHERE VOUCHER_TYPE=2 AND MEMBER_SQ=a.USER_SQ) GROUP_VOUCHER, COMMENT, LAST_DT, a.ISUSE
					from tb_user a 
					where :USER_SQ in (SELECT TRAINER_SQ FROM tb_user_voucher b WHERE b.MEMBER_SQ=a.USER_SQ) and GRADE=1 order by REG_DT desc		
					
					");
		$database->bind(':USER_SQ', $MANAGER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$voucheruserlist = json_encode($rows);
		
		// 직원 휴가 정보
		$database->prepare("
			SELECT HOLIDAY_SQ,CENTER_SQ,HOLIDAY,HOLIDAY_NAME,MANAGER_SQ
 			FROM tb_holiday
			WHERE MANAGER_SQ=:MANAGER_SQ ORDER BY HOLIDAY ASC
		");
		$database->bind(':MANAGER_SQ', $MANAGER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$holidaylist = json_encode($rows);
		
		// 직원 스케쥴 - 개인이용권 
		$database->prepare("
			select a.RESERV_SQ,a.CENTER_SQ,a.USER_SQ,a.MANAGER_SQ,a.UV_SQ,e.VOUCHER_NAME,a.RESERV_STATUS,a.RESERV_DT,a.START_TIME,a.END_TIME,a.MEMO,
					b.USER_NM, b.USERIMAGE, b.BIRTH_DT, b.GENDER, b.PHONE_NO, c.USER_NM as MANAGER_NAME, d.DESCRIPTION as RESERV_STATUS_NAME			
			from tb_reservation a  left outer join tb_user b on a.USER_SQ=b.USER_SQ
				  left outer join tb_user c on a.MANAGER_SQ=c.USER_SQ
				  left outer join tb_common d on a.RESERV_STATUS=d.CODE and d.BASE_CD='CD015'
				  left outer join tb_user_voucher e on a.UV_SQ=e.UV_SQ
			where a.CENTER_SQ=:CENTER_SQ  and a.RESERV_DT=CURDATE() and a.MANAGER_SQ=:MANAGER_SQ order by a.CREATED desc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':MANAGER_SQ', $MANAGER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$schedulelist = json_encode($rows);
		
		// 직원 휴가 설정 
		$database->prepare("
			SELECT MEMBER_WORKTIME_SQ,MEMBER_SQ, MON,TUE,WED,THU,FRI,SAT,SUN,WORK_TIME,MODIFIEDID,MODIFIEDDT
			FROM tb_member_work_time
			WHERE MEMBER_SQ=:MANAGER_SQ
		");
		$database->bind(':MANAGER_SQ', $MANAGER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$schedulesetting = json_encode($rows);
		// 카테고리 정보 
		$database->prepare("
			select WC_SEQ,CENTER_SQ,NAME,RANK
					from tb_work_category where CENTER_SQ=:CENTER_SQ order by RANK asc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$workcategorylist = json_encode($rows);

		// Work Ststus
		$database->prepare("
			select COMMON_SQ,BASE_CD,CODE,NAME,DESCRIPTION
					from tb_common where BASE_CD='CD003' and CODE>0 order by CODE asc
		");
		$database->execute();

		$rows = $database->fetchAll();
		$workstatuslist = json_encode($rows);
		
		// 급여 및 수당 정보정보
		$database->prepare("
		
					select SALARY,INSENTIVE,SALARY_TAX_EXCEPT,
						PERSONAL_ALLOWANCE_TYPE, b.DESCRIPTION PERSONAL_ALLOWANCE_TYPE_NAME,
						PERSONAL_ALLOWANCE_AMOUNT,PERSONAL_ALLOWANCE_RATIO,PERSONAL_ALLOWANCE_TAX_EXCEPT,
						PERSONAL_NOSHOW_TYPE, c.DESCRIPTION PERSONAL_NOSHOW_TYPE_NAME,PERSONAL_NOSHOW_RATIO,
						GROUP_ALLOWANCE_TAX_EXCEPT, GROUP_NOSHOW_TYPE, d.DESCRIPTION GROUP_NOSHOW_TYPE_NAME,
						GROUP_NOSHOW_RATIO
					from tb_manager_sub a
					inner join tb_common b on a.PERSONAL_ALLOWANCE_TYPE = b.code and b.BASE_CD='CD020'
					inner join tb_common c on a.PERSONAL_NOSHOW_TYPE = c.code and c.BASE_CD='CD021'
					inner join tb_common d on a.GROUP_NOSHOW_TYPE = d.code and d.BASE_CD='CD021'
					where USER_SQ=:USER_SQ 
					
					");
		$database->bind(':USER_SQ', $MANAGER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$salaryinfo = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($managerinfo.'|'.$workcategorylist.'|'.$workstatuslist.'|'.$userlist.'|'.$voucheruserlist.'|'.$schedulelist.'|'.$schedulesetting.'|'.$holidaylist.'|'.$salaryinfo);
		break;
	
	case 'execManagerSalaryInfoModify': // 임직원 관리정보 수정 
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		// 파라메터 취득
		$MANAGER_SQ = getAnyParameter("MANAGER_SQ",0);
		$SALARY = getAnyParameter("SALARY",0);
		$INSENTIVE = getAnyParameter("INSENTIVE",0);
		$SALARY_TAX_EXCEPT = getAnyParameter("SALARY_TAX_EXCEPT",0);
		$PERSONAL_ALLOWANCE_TYPE = getAnyParameter("PERSONAL_ALLOWANCE_TYPE",0);
		$PERSONAL_ALLOWANCE_AMOUNT = getAnyParameter("PERSONAL_ALLOWANCE_AMOUNT",0);
		$PERSONAL_ALLOWANCE_RATIO = getAnyParameter("PERSONAL_ALLOWANCE_RATIO",0);
		$PERSONAL_ALLOWANCE_TAX_EXCEPT = getAnyParameter("PERSONAL_ALLOWANCE_TAX_EXCEPT",0);
		$PERSONAL_NOSHOW_TYPE = getAnyParameter("PERSONAL_NOSHOW_TYPE",0);
		$PERSONAL_NOSHOW_RATIO = getAnyParameter("PERSONAL_NOSHOW_RATIO",0);
		$GROUP_ALLOWANCE_TAX_EXCEPT = getAnyParameter("GROUP_ALLOWANCE_TAX_EXCEPT",0);
		$GROUP_NOSHOW_TYPE = getAnyParameter("GROUP_NOSHOW_TYPE",0);
		$GROUP_NOSHOW_RATIO = getAnyParameter("GROUP_NOSHOW_RATIO",0);

		// 기본값 설정
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "임직원 설정";
		$SUBCATEGORY = "임직원 관리정보 변경";
		$ACTION = $MANAGER_SQ . " 임직원 관리정보를 변경하였습니다.";
		$IP = getClientIPv4();
		
		// DB 실행
		$database->prepare("
			update tb_manager_sub set 
				SALARY=:SALARY, 
				INSENTIVE=:INSENTIVE, 
				SALARY_TAX_EXCEPT=:SALARY_TAX_EXCEPT, 
				PERSONAL_ALLOWANCE_TYPE=:PERSONAL_ALLOWANCE_TYPE, 
				PERSONAL_ALLOWANCE_AMOUNT=:PERSONAL_ALLOWANCE_AMOUNT, 
				PERSONAL_ALLOWANCE_RATIO=:PERSONAL_ALLOWANCE_RATIO, 
				PERSONAL_ALLOWANCE_TAX_EXCEPT=:PERSONAL_ALLOWANCE_TAX_EXCEPT, 
				PERSONAL_NOSHOW_TYPE=:PERSONAL_NOSHOW_TYPE, 
				PERSONAL_NOSHOW_RATIO=:PERSONAL_NOSHOW_RATIO, 
				GROUP_ALLOWANCE_TAX_EXCEPT=:GROUP_ALLOWANCE_TAX_EXCEPT, 
				GROUP_NOSHOW_TYPE=:GROUP_NOSHOW_TYPE, 
				GROUP_NOSHOW_RATIO=:GROUP_NOSHOW_RATIO
			where USER_SQ=:MANAGER_SQ
		");
		$database->bind(':SALARY', $SALARY);
		$database->bind(':INSENTIVE', $INSENTIVE);
		$database->bind(':SALARY_TAX_EXCEPT', $SALARY_TAX_EXCEPT);
		$database->bind(':PERSONAL_ALLOWANCE_TYPE', $PERSONAL_ALLOWANCE_TYPE);
		$database->bind(':PERSONAL_ALLOWANCE_AMOUNT', $PERSONAL_ALLOWANCE_AMOUNT);
		$database->bind(':PERSONAL_ALLOWANCE_RATIO', $PERSONAL_ALLOWANCE_RATIO);
		$database->bind(':PERSONAL_ALLOWANCE_TAX_EXCEPT', $PERSONAL_ALLOWANCE_TAX_EXCEPT);
		$database->bind(':PERSONAL_NOSHOW_TYPE', $PERSONAL_NOSHOW_TYPE);
		$database->bind(':PERSONAL_NOSHOW_RATIO', $PERSONAL_NOSHOW_RATIO);
		$database->bind(':GROUP_ALLOWANCE_TAX_EXCEPT', $GROUP_ALLOWANCE_TAX_EXCEPT);
		$database->bind(':GROUP_NOSHOW_TYPE', $GROUP_NOSHOW_TYPE);
		$database->bind(':GROUP_NOSHOW_RATIO', $GROUP_NOSHOW_RATIO);
		$database->bind(':MANAGER_SQ', $MANAGER_SQ);
		$database->execute();
				
        $result = 'Success';

        if ($database->rowCount() < 1) { 
            $result = 'Fail';
 			exit($result);
       	}
		
		// 급여 및 수당 정보정보
		$database->prepare("
		
					select SALARY,INSENTIVE,SALARY_TAX_EXCEPT,
						PERSONAL_ALLOWANCE_TYPE, b.DESCRIPTION PERSONAL_ALLOWANCE_TYPE_NAME,
						PERSONAL_ALLOWANCE_AMOUNT,PERSONAL_ALLOWANCE_RATIO,PERSONAL_ALLOWANCE_TAX_EXCEPT,
						PERSONAL_NOSHOW_TYPE, c.DESCRIPTION PERSONAL_NOSHOW_TYPE_NAME,PERSONAL_NOSHOW_RATIO,
						GROUP_ALLOWANCE_TAX_EXCEPT, GROUP_NOSHOW_TYPE, d.DESCRIPTION GROUP_NOSHOW_TYPE_NAME,
						GROUP_NOSHOW_RATIO
					from tb_manager_sub a
					inner join tb_common b on a.PERSONAL_ALLOWANCE_TYPE = b.code and b.BASE_CD='CD020'
					inner join tb_common c on a.PERSONAL_NOSHOW_TYPE = c.code and c.BASE_CD='CD021'
					inner join tb_common d on a.GROUP_NOSHOW_TYPE = d.code and d.BASE_CD='CD021'
					where USER_SQ=:USER_SQ 
					
					");
		$database->bind(':USER_SQ', $MANAGER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$salaryinfo = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($salaryinfo);
		break;
	
	case 'execManagerAddInfoModify': // 임직원 관리정보 수정 
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		// 파라메터 취득
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$MANAGER_SQ = getAnyParameter("USER_SQ",0);
		$WORKCATEGORY = getAnyParameter("WORKCATEGORY",0);
		$WORKSTATUS = getAnyParameter("WORKSTATUS",0);
		$WORKSTARTDATE = getAnyParameter("WORKSTARTDATE","");
		$WORKENDDATE = getAnyParameter("WORKENDDATE","");

		// 기본값 설정
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "임직원 설정";
		$SUBCATEGORY = "임직원 관리정보 변경";
		$ACTION = $MANAGER_SQ . " 임직원 관리정보를 변경하였습니다.";
		$IP = getClientIPv4();
		
		// DB 실행
		if (strlen($WORKSTARTDATE) == 0) {
			$WORKSTARTDATE='0000-00-00';
		}
		if (strlen($WORKENDDATE) == 0) {
			$WORKENDDATE='0000-00-00';
		}
		$database->prepare("
			update tb_user set WORKCATEGORY=:WORKCATEGORY, WORKSTATUS=:WORKSTATUS, WORKSTARTDATE=:WORKSTARTDATE, WORKENDDATE=:WORKENDDATE  where USER_SQ=:USER_SQ
		");
		$database->bind(':WORKCATEGORY', $WORKCATEGORY);
		$database->bind(':WORKSTATUS', $WORKSTATUS);
		$database->bind(':WORKSTARTDATE', $WORKSTARTDATE);
		$database->bind(':WORKENDDATE', $WORKENDDATE);
		$database->bind(':USER_SQ', $MANAGER_SQ);
		$database->execute();
		
        $result = '1';

        if ($database->rowCount() < 1) { 
            $result = '0';
            exit($result);
        }
		
		// 최종 결과 취득 
		$database->prepare("
			select a.USER_SQ,a.CENTER_SQ,a.USER_NM,a.PHONE_NO,a.ADDRESS,a.EMAIL,a.BIRTH_DT, a.REG_DT, a.GRADE,
						a.WORKCATEGORY, a.WORKTYPE, a.WORKSTARTDATE, a.WORKENDDATE, a.WORKSTATUS, a.ISUSE
					from tb_user a  left outer join tb_user b on a.TRAINER=b.USER_SQ
					where a.CENTER_SQ=:CENTER_SQ and a.ISMANAGER= and a.ISUSE=1 order by a.REG_DT desc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();
		
		$rows = $database->fetchAll();
		$memberlist = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($memberlist);
		break;
			
	case 'execWorkCategryDelete': // 카테고리 수정  
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		// 파라메터 취득
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$WC_SEQ = getAnyParameter("WC_SEQ",0);

		// 기본값 설정
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "임직원 설정";
		$SUBCATEGORY = "카테고리 삭제";
		$ACTION = $WC_SEQ . " 카테고리를 삭제하였습니다.";
		$IP = getClientIPv4();
		
		// DB 실행
		$database->prepare("
			delete from tb_work_category where WC_SEQ=:WC_SEQ
		");
		$database->bind(':WC_SEQ', $WC_SEQ);
		$database->execute();
		
        $result = '1';

        if ($database->rowCount() < 1) { 
            $result = '0';
            exit($result);
        }
		
		// DB 다시 취득.
		$database->prepare("
			select WC_SEQ,CENTER_SQ,NAME,RANK
					from tb_work_category where CENTER_SQ=:CENTER_SQ order by RANK asc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();
		$rows = $database->fetchAll();
		
		$ind = 0;
		foreach ($rows as $row)
		{
			// DB 실행 - 하나씩 랭크를 재조정한다.
			$database->prepare("
				update tb_work_category SET RANK=:RANK
						where WC_SEQ=:WC_SEQ
			");
			$database->bind(':RANK', $ind++);
			$database->bind(':WC_SEQ', $row["WC_SEQ"]);
			$database->execute();
		}
		
		// DB 실행
		$database->prepare("
			update tb_user set WorkCategory=0 where WorkCategory=:WC_SEQ and CENTER_SQ=:CENTER_SQ
		");
		$database->bind(':WC_SEQ', $WC_SEQ);
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		// 최종 결과 취득 
		$database->prepare("
			select WC_SEQ,CENTER_SQ,NAME,RANK
					from tb_work_category where CENTER_SQ=:CENTER_SQ order by RANK asc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();
		$rows = $database->fetchAll();
		$workcategorylist = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($workcategorylist);
		break;
		
	case 'execWorkCategryModify': // 카테고리 수정  
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		// 파라메터 취득
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$WC_SEQ = getAnyParameter("WC_SEQ",0);
		$NAME = getAnyParameter("NAME","");

		// 기본값 설정
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "임직원 설정";
		$SUBCATEGORY = "카테고리 명칭 변경";
		$ACTION = $WC_SEQ . " 카테고리 명칭을 변경하였습니다.";
		$IP = getClientIPv4();
		
		// DB 실행
		$database->prepare("
			update tb_work_category set NAME=:NAME where WC_SEQ=:WC_SEQ
		");
		$database->bind(':NAME', $NAME);
		$database->bind(':WC_SEQ', $WC_SEQ);
		$database->execute();
		
        $result = '1';

        if ($database->rowCount() < 1) { 
            $result = '0';
            exit($result);
        }

		// 최종 결과 취득 
		$database->prepare("
			select WC_SEQ,CENTER_SQ,NAME,RANK
					from tb_work_category where CENTER_SQ=:CENTER_SQ order by RANK asc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();
		$rows = $database->fetchAll();
		$workcategorylist = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($workcategorylist);
		break;

	case 'execWorkCategryAdd': // 카테고리 생성  
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		// 파라메터 취득
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$NAME = getAnyParameter("NAME",0);

		// 기본값 설정
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "임직원 설정";
		$SUBCATEGORY = "카테고리 생성";
		$ACTION = $NAME . " 카테고리를 생성하였습니다.";
		$IP = getClientIPv4();
		
		// 존재여부확인 
		$database->prepare("
			select WC_SEQ from tb_work_category 
					where CENTER_SQ=:CENTER_SQ AND NAME=:NAME
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':NAME', $NAME);
		$database->execute();
		
        $result = '1';

        if ($database->rowCount() >0) { 
            $result = '동일한 이름이 존재합니다.';
            exit("{\"result\":\"".$result."\"}");
        }

		// DB 실행
		$database->prepare("
			insert tb_work_category (CENTER_SQ, NAME, RANK)
					SELECT :CENTER_SQ, :NAME, MAX(RANK)+1 FROM tb_work_category WHERE CENTER_SQ=:CENTER_SQ2
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':NAME', $NAME);
		$database->bind(':CENTER_SQ2', $CENTER_SQ);
		$database->execute();
		
        $result = '1';

        if ($database->rowCount() < 1) { 
            $result = '0';
            exit($result);
        }

		// DB 다시 취득.
		$database->prepare("
			select WC_SEQ,CENTER_SQ,NAME,RANK
					from tb_work_category where CENTER_SQ=:CENTER_SQ order by RANK asc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();
		$rows = $database->fetchAll();
		$workcategorylist = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($workcategorylist);
		break;

	case 'execWorkCategryRankChange': // 사용자 리스트 취득 
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		// 파라메터 취득
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$WC_SEQ = getAnyParameter("WC_SEQ",0);
		$UPDOWN = getAnyParameter("UPDOWN",1);

		// 기본값 설정
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "임직원 설정";
		$SUBCATEGORY = "카테고리 순서 변경";
		$ACTION = $WC_SEQ . " 카테고리 순서를 변경하였습니다.";
		$IP = getClientIPv4();
		
		// DB 실행
		$database->prepare("
			update tb_work_category SET RANK=RANK+(:RANK)
					where WC_SEQ=:WC_SEQ
		");
		$database->bind(':RANK', $UPDOWN * 1.5);
		$database->bind(':WC_SEQ', $WC_SEQ);
		$database->execute();
		error_log($UPDOWN * 0.5);

        $result = '1';

        if ($database->rowCount() < 1) { 
            $result = '0';
            exit($result);
        }

		// DB 다시 취득.
		$database->prepare("
			select WC_SEQ,CENTER_SQ,NAME,RANK
					from tb_work_category where CENTER_SQ=:CENTER_SQ order by RANK asc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();
		$rows = $database->fetchAll();
		
		$ind = 0;
		foreach ($rows as $row)
		{
			error_log($row["WC_SEQ"]);
			error_log($row["RANK"]);
			// DB 실행 - 하나씩 랭크를 재조정한다.
			$database->prepare("
				update tb_work_category SET RANK=:RANK
						where WC_SEQ=:WC_SEQ
			");
			$database->bind(':RANK', $ind++);
			$database->bind(':WC_SEQ', $row["WC_SEQ"]);
			$database->execute();
		}

		// 최종 결과 취득 
		$database->prepare("
			select WC_SEQ,CENTER_SQ,NAME,RANK
					from tb_work_category where CENTER_SQ=:CENTER_SQ order by RANK asc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();
		$rows = $database->fetchAll();
		$workcategorylist = json_encode($rows);
			error_log($CENTER_SQ);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($workcategorylist);
		break;

	case 'execManagerRegister': // 관리 사이트 사용자로 등록 
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		// 파라메터 취득
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$MANAGER_SEQ = getAnyParameter("USER_SQ",0);

		// 기본값 설정
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "임직원 설정";
		$SUBCATEGORY = "임직원 등록";
		$ACTION = $USER_SQ . " 임직원을 등록하였습니다.";
		$IP = getClientIPv4();
		
		// DB 실행 
		$database->prepare("
			UPDATE tb_user SET GRADE=2 
					where CENTER_SQ=:CENTER_SQ and USER_SQ=:MANAGER_SEQ
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':MANAGER_SEQ', $MANAGER_SEQ);
		$database->execute();
		
        $result = '1';

        if ($database->rowCount() < 1) { 
            $result = '0';
            exit($result);
        }

		// DB 조회
		$database->prepare("
			select a.USER_SQ,a.CENTER_SQ,a.USER_NM,a.PHONE_NO,a.ADDRESS,a.EMAIL,a.BIRTH_DT, a.REG_DT, a.GRADE,
						a.WORKCATEGORY, a.WORKTYPE, a.WORKSTARTDATE, a.WORKENDDATE, a.WORKSTATUS, a.ISUSE
					from tb_user a  left outer join tb_user b on a.TRAINER=b.USER_SQ
					where a.CENTER_SQ=:CENTER_SQ and a.ISMANAGER=1 and a.ISUSE=1 order by a.REG_DT desc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();
		
		$rows = $database->fetchAll();
		$managerlist = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($managerlist);
		break;
	
	case 'getManagerList': // 사용자 리스트 취득 
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		// 파라메터 취득
		$CENTER_SQ = $session->user["CENTER_SQ"];

		// 기본값 설정
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "멤버리스트";
		$SUBCATEGORY = "";
		$ACTION = "사용자 리스트를 조회하였습니다.";
		$IP = getClientIPv4();
		
		// DB 조회
		$database->prepare("
			select a.USER_SQ,a.CENTER_SQ,a.USER_NM,a.PHONE_NO,a.ADDRESS,a.EMAIL,a.BIRTH_DT, a.REG_DT, a.GRADE,
						a.WORKCATEGORY, a.WORKTYPE, a.WORKSTARTDATE, a.WORKENDDATE, a.WORKSTATUS, a.ISUSE,
						(select count(*) from tb_user where TRAINER=a.USER_SQ and ISUSE=1) USER_COUNT,
						(select count(distinct USER_SQ) from tb_user b where a.USER_SQ in (SELECT TRAINER_SQ FROM tb_user_voucher c WHERE c.MEMBER_SQ=b.USER_SQ) and GRADE=1 and ISUSE=1) VOUCHERUSER_COUNT,
						(select count(*) from tb_reservation c where c.RESERV_DT=CURDATE() and c.MANAGER_SQ=a.USER_SQ) PERSONAL_RESERV_COUNT
					from tb_user a 
					where a.CENTER_SQ=:CENTER_SQ and a.ISMANAGER=1 and a.ISUSE=1 order by a.REG_DT desc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$managerlist = json_encode($rows);

		$database->prepare("
			select WC_SEQ,CENTER_SQ,NAME,RANK
					from tb_work_category where CENTER_SQ=:CENTER_SQ order by RANK asc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$workcategorylist = json_encode($rows);

		$database->prepare("
			select COMMON_SQ,BASE_CD,CODE,NAME,DESCRIPTION
					from tb_common where BASE_CD='CD003' and CODE>0 order by CODE asc
		");
		$database->execute();

		$rows = $database->fetchAll();
		$workstatuslist = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($managerlist.'|'.$workcategorylist.'|'.$workstatuslist);
		break;

    case 'AllCenter':
 		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
        $CENTER_LIST = [];
        $DB = mysqli_connect("localhost","liansoft2","liansoft2!","liansoft2");
        $SQL = "SELECT * FROM tb_center";
        $result = mysqli_query($DB,$SQL);
        while($row = mysqli_fetch_array($result)){
            $data = new stdClass();
            $data -> CENTER_SQ = $row[0];
            $data -> CENTER_NM = $row[1];
            $CENTER_LIST[] = $data;
            unset($data);
        }
        $CENTER_LIST = json_encode($CENTER_LIST);
        exit($CENTER_LIST);

    case 'EditMemberInfo':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
        $MEMBER_SQ = getAnyParameter("MEMBER_SQ","");
        $USER_NM = getAnyParameter("USER_NM","");		
		$PWD = getAnyParameter("PWD", "");
		$NEW_PWD = getAnyParameter("NEW_PWD", "");	
        $GENDER = getAnyParameter("GENDER","");
        $PHONE_NO = getAnyParameter("PHONE_NO","");
        $BIRTH_DT = getAnyParameter("BIRTH_DT","");
        $EMAIL = getAnyParameter("EMAIL","");
        $NEW_CENTER_SQ = $session->user["CENTER_SQ"];

        //$CENTER_SQ = $session->user["CENTER_SQ"];
		
		// 기본값 설정
        $CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "사용자 정보";
		$SUBCATEGORY = "사용자 정보 수정";
		$ACTION = $USERID . " 회원의 정보가 수정되었습니다.";
        $IP = getClientIPv4();
		// 비밀번호 암호화 
		$PWD_ENCRYPTED = Hash_Sha256($PWD);
		$NEWPWD_ENCRYPTED = Hash_Sha256($NEW_PWD);
            
        $database->prepare("
            SELECT USERID FROM tb_user where USER_SQ=:MEMBER_SQ and PWD_ENCRYPTED=:PWD_ENCRYPTED
		");
		$database->bind(':MEMBER_SQ', $MEMBER_SQ);
		$database->bind(':PWD_ENCRYPTED', $PWD_ENCRYPTED);
        $database->execute();

		$response_array["result"] = 'Success';

        if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			$response_array["reason"] = 'Password Incorrect';
            exit(json_encode($response_array));
        }
		$response_array["reason"] = 'Password correct';
        
		$Update = 0;
        $database->prepare("
            UPDATE tb_user SET 
            USER_NM = :USER_NM, 
            GENDER = :GENDER, 
            PHONE_NO = :PHONE_NO, 
            BIRTH_DT = :BIRTH_DT, 
            EMAIL = :EMAIL, 
            CENTER_SQ = :CENTER_SQ
            where USER_SQ=:MEMBER_SQ
		");
		$database->bind(':USER_NM', $USER_NM);
		$database->bind(':GENDER', $GENDER);
		$database->bind(':PHONE_NO', $PHONE_NO);
		$database->bind(':BIRTH_DT', $BIRTH_DT);
		$database->bind(':EMAIL', $EMAIL);
		$database->bind(':CENTER_SQ', $NEW_CENTER_SQ);
		$database->bind(':MEMBER_SQ', $MEMBER_SQ);
        $database->execute();

		if ($database->rowCount() > 0) { 
			$Update = $Update+1;
        }

		if ($NEW_PWD != "") {
			$database->prepare("
				UPDATE tb_user SET 
				PWD_ENCRYPTED = :PWD_ENCRYPTED
				where USER_SQ=:MEMBER_SQ
			");
			$database->bind(':PWD_ENCRYPTED', $NEWPWD_ENCRYPTED);
			$database->bind(':MEMBER_SQ', $MEMBER_SQ);
			$database->execute();

			if ($database->rowCount() > 0) { 
				$Update = $Update+1;
			}
		}
		
		if ($Update == 0) { 
			$response_array["result"] = 'Fail';
			$response_array["reason"] = 'Not Updated';
            exit(json_encode($response_array));
        }
		$response_array["reason"] = 'Update Complete';

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit(json_encode($response_array));
        break;

    case 'EditUserInfo':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
        $MEMBER_SQ = getAnyParameter("MEMBER_SQ","");
        $USER_NM = getAnyParameter("USER_NM","");
        $GENDER = getAnyParameter("GENDER","");
        $PHONE_NO = getAnyParameter("PHONE_NO","");
        $BIRTH_DT = getAnyParameter("BIRTH_DT","");
        $EMAIL = getAnyParameter("EMAIL","");
        //$CENTER_SQ = $session->user["CENTER_SQ"];

		// 기본값 설정
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "사용자 정보";
		$SUBCATEGORY = "사용자 정보 수정";
		$ACTION = $USERID . " 회원의 정보가 수정되었습니다.";
        $IP = getClientIPv4();
            
        $database->prepare("
            UPDATE tb_user SET 
            USER_NM = :USER_NM, 
            GENDER = :GENDER, 
            PHONE_NO = :PHONE_NO, 
            BIRTH_DT = :BIRTH_DT, 
            EMAIL = :EMAIL
            where USER_SQ=:MEMBER_SQ
		");
		$database->bind(':USER_NM', $USER_NM);
		$database->bind(':GENDER', $GENDER);
		$database->bind(':PHONE_NO', $PHONE_NO);
		$database->bind(':BIRTH_DT', $BIRTH_DT);
		$database->bind(':EMAIL', $EMAIL);
		$database->bind(':MEMBER_SQ', $MEMBER_SQ);
        $database->execute();

        $result = '1';

        if ($database->rowCount() < 1) {    
            $result = '0';
            exit($result);
        }

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($result);
        break;
        
    case 'UpdateComment':
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
        $COMMENT = getAnyParameter("COMMENT","");
        $MEMBER_SQ = getAnyParameter("USER_SQ","");
        $CENTER_SQ = $session->user["CENTER_SQ"];

		// 기본값 설정
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "사용자 정보";
		$SUBCATEGORY = "사용자 메모 수정";
		$ACTION = $USERID . " 회원의 메모가 수정되었습니다.";
        $IP = getClientIPv4();
            
        $database->prepare("
			update tb_user SET COMMENT = :COMMENT where USER_SQ=:MEMBER_SQ
		");
		$database->bind(':COMMENT', $COMMENT);
		$database->bind(':MEMBER_SQ', $MEMBER_SQ);
        $database->execute();

        $result = true;

        if ($database->rowCount() < 1) { 
            $result = false;
            exit($result);
        }

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($result);
		break;
	
	case 'DeleteMeasurement': // 사용자 리스트 취득 
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		// 파라메터 취득
		$MEASUREMENT_TYPE = getAnyParameter("MEASUREMENT_TYPE","");
		$MEASUREMENT_SQ = getAnyParameter("MEASUREMENT_SQ","");
		$CENTER_SQ = $session->user["CENTER_SQ"];

		// 기본값 설정
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "건강정보";
		$SUBCATEGORY = "측정정보";
		$ACTION = $USERID . " 회원 자세측정정보를 삭제하였습니다.";
        $IP = getClientIPv4();
        
        //bs_measurement
		//bs_posedata, bs_romdata
        // 날짜 정보 데이터
        $result = json_encode("Success");

		$database->prepare("
			delete from bs_measurement where MEASUREMENT_SQ=:MEASUREMENT_SQ
		");
		$database->bind(':MEASUREMENT_SQ', $MEASUREMENT_SQ);
        $database->execute();
        
        if ($database->rowCount() < 1) { 
            $result = json_encode("Fail");
            exit($result);
        }
        
        switch ($MEASUREMENT_TYPE){
            // POSE 데이터
            case 'pose' :
                $database->prepare("
                    delete from bs_posedata where MEASUREMENT_SQ=:MEASUREMENT_SQ
                ");
                $database->bind(':MEASUREMENT_SQ', $MEASUREMENT_SQ);
                $database->execute();

		        if ($database->rowCount() < 1) { 
                    $result = json_encode("Fail1");
                    exit($result);
                }
                break;
            
            // ROM 데이터
            case 'rom' :
                $database->prepare("
                    delete from bs_romdata where MEASUREMENT_SQ=:MEASUREMENT_SQ
                ");
                $database->bind(':MEASUREMENT_SQ', $MEASUREMENT_SQ);
                $database->execute();

		        if ($database->rowCount() < 1) { 
                    $result = json_encode("Fail2");
                    exit($result);
                }
                break;
        }
		
		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($result);
		break;

    case 'SaveMedicalExamData': // ajax - select optionbox 를 채운다.
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$USER_SEQ = getAnyParameter("USER_SEQ",""); // 저장할 대상자의 회원일련번호
		$desaInfo_Date = getAnyParameter("desaInfo_Date","");
		$HR = getAnyParameter("desaInfo_HR",0);
		$SBP = getAnyParameter("desaInfo_SBP",0);
		$DBP = getAnyParameter("desaInfo_DBP",0);
		$Glucose = getAnyParameter("desaInfo_Glucose",0);
		$HbA1c = getAnyParameter("desaInfo_HbA1c",0);
		$TC = getAnyParameter("desaInfo_TC",0);
		$HDL = getAnyParameter("desaInfo_HDL",0);
		$LDL = getAnyParameter("desaInfo_LDL",0);
		$TG = getAnyParameter("desaInfo_TG",0);
		$Lactate = getAnyParameter("desaInfo_Lactate",0);
		
		// 기본값 설정
		$USER_SQ = $session->user["USER_SQ"];  //로그인 사용자의 회원일련번호 
		$USERID = "";
		$DEVICE_SQ = -1;
		$CATEGORY = "사용자 정보등록";
		$SUBCATEGORY = "사용자 대사검진 정보등록";
		$ACTION = $USER_SEQ . " 사용자의 대사검진 정보를 등록하였습니다.";
		$IP = getClientIPv4();

		$database->prepare("insert into bs_measurement ( USER_SQ, DEVICE_SQ, MEASUREMENT_TYPE, DONE, REG_DT )
								values ( :USER_SQ, 0, 'HEALTH', 1, :REG_DT ); SELECT LAST_INSERT_ID() as MEASUREMENT_SQ
						");
		$database->bind(':USER_SQ', $USER_SEQ);
		$database->bind(':REG_DT', $desaInfo_Date);
		$database->execute();

		$database->prepare("select MEASUREMENT_SQ from bs_measurement 
								where  USER_SQ=:USER_SQ ORDER BY MEASUREMENT_SQ DESC LIMIT 1
						");
		$database->bind(':USER_SQ', $USER_SEQ);
		$database->execute();
		$row = $database->fetch();
		$MEASUREMENT_SQ = $row["MEASUREMENT_SQ"];

		error_log('$MEASUREMENT_SQ='. $MEASUREMENT_SQ);
		
		$database->prepare("insert into bs_health ( MEASUREMENT_SQ,HR,SBP,DBP,Glucose, HbA1c,TC,HDL,LDL,TG,Lactate,REG_DT )
								values ( :MEASUREMENT_SQ, :HR, :SBP, :DBP, :Glucose, :HbA1c, :TC, :HDL, :LDL, :TG, :Lactate, :REG_DT )
						");
		$database->bind(':MEASUREMENT_SQ', $MEASUREMENT_SQ);
		$database->bind(':HR', $HR);
		$database->bind(':SBP', $SBP);
		$database->bind(':DBP', $DBP);
		$database->bind(':Glucose', $Glucose);
		$database->bind(':HbA1c', $HbA1c);
		$database->bind(':TC', $TC);
		$database->bind(':HDL', $HDL);
		$database->bind(':LDL', $LDL);
		$database->bind(':TG', $TG);
		$database->bind(':Lactate', $Lactate);
		$database->bind(':REG_DT', $desaInfo_Date);
		$database->execute();
		
		error_log($database->rowCount());
		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'fail';
			exit(json_encode($response_array));
		}
				
		// HEALTH 리스트
		$database->prepare("
			select MEASUREMENT_SQ,USER_SQ,DEVICE_SQ,MEASUREMENT_TYPE,DONE,REG_DT
					from bs_measurement where DONE=1 AND MEASUREMENT_TYPE='HEALTH' AND USER_SQ=:USER_SQ order by REG_DT DESC
		");
		$database->bind(':USER_SQ', $USER_SEQ);
		$database->execute();

		$rows = $database->fetchAll();
		$healthlist = json_encode($rows);

		// HEALTH 데이터 
		$database->prepare("
			SELECT MEASUREMENT_SQ,REG_DT,HR,SBP,DBP,GLUCOSE,HbA1c,TC,HDL,LDL,TG,Lactate FROM bs_health 
				WHERE MEASUREMENT_SQ in (SELECT MEASUREMENT_SQ FROM bs_measurement WHERE USER_SQ=:USER_SQ) 
			order by reg_dt asc;
		");
		$database->bind(':USER_SQ', $USER_SEQ);
		$database->execute();

		$rows = $database->fetchAll();
		$healthdata = json_encode($rows);

		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		error_log(json_encode($healthlist.'|'.$healthdata));
		exit($healthlist.'|'.$healthdata);
		break;
			
	case 'SaveInbodyData': // ajax - select optionbox 를 채운다.
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$USER_SEQ = getAnyParameter("USER_SEQ",""); // 저장할 대상자의 회원일련번호
		$bodyInfo_Date = getAnyParameter("bodyInfo_Date","");
		$bodyInfo_Height = getAnyParameter("bodyInfo_Height",0);
		$bodyInfo_Weight = getAnyParameter("bodyInfo_Weight",0);
		$bodyInfo_Fat = getAnyParameter("bodyInfo_Fat",0);
		$bodyInfo_Muscle = getAnyParameter("bodyInfo_Muscle",0);
		
		// 기본값 설정
		$USER_SQ = $session->user["USER_SQ"];  //로그인 사용자의 회원일련번호 
		$USERID = "";
		$DEVICE_SQ = -1;
		$CATEGORY = "사용자 정보등록";
		$SUBCATEGORY = "사용자 신체정보등록";
		$ACTION = $USER_SEQ . " 사용자의 신체정보를 등록하였습니다.";
		$IP = getClientIPv4();

		$database->prepare("insert into bs_measurement ( USER_SQ, DEVICE_SQ, MEASUREMENT_TYPE, DONE, REG_DT )
								values ( :USER_SQ, 0, 'INBODY', 1, :REG_DT ); SELECT LAST_INSERT_ID() as MEASUREMENT_SQ
						");
		$database->bind(':USER_SQ', $USER_SEQ);
		$database->bind(':REG_DT', $bodyInfo_Date);
		$database->execute();

		$database->prepare("select MEASUREMENT_SQ from bs_measurement 
								where  USER_SQ=:USER_SQ ORDER BY MEASUREMENT_SQ DESC LIMIT 1
						");
		$database->bind(':USER_SQ', $USER_SEQ);
		$database->execute();
		$row = $database->fetch();
		$MEASUREMENT_SQ = $row["MEASUREMENT_SQ"];
		
		error_log('$row = '.var_export($row, 1));	
		error_log($database->rowCount());
		error_log('$MEASUREMENT_SQ='. $MEASUREMENT_SQ);
		
		$database->prepare("insert into bs_inbody ( MEASUREMENT_SQ,HEIGHT,WEIGHT,FAT,MUSCLE,REG_DT )
								values ( :MEASUREMENT_SQ, :HEIGHT, :WEIGHT, :FAT, :MUSCLE, :REG_DT )
						");
		$database->bind(':MEASUREMENT_SQ', $MEASUREMENT_SQ);
		$database->bind(':HEIGHT', $bodyInfo_Height);
		$database->bind(':WEIGHT', $bodyInfo_Weight);
		$database->bind(':FAT', $bodyInfo_Fat);
		$database->bind(':MUSCLE', $bodyInfo_Muscle);
		$database->bind(':REG_DT', $bodyInfo_Date);
		$database->execute();
		
		error_log($database->rowCount());
		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'fail';
			exit(json_encode($response_array));
		}
				
		// 신체정보 리스트
		$database->prepare("
			select MEASUREMENT_SQ,USER_SQ,DEVICE_SQ,MEASUREMENT_TYPE,DONE,REG_DT
					from bs_measurement where DONE=1 AND MEASUREMENT_TYPE='INBODY' AND USER_SQ=:USER_SQ order by REG_DT DESC
		");
		$database->bind(':USER_SQ', $USER_SEQ);
		$database->execute();

		$rows = $database->fetchAll();
		$inbodylist = json_encode($rows);
		
		// INBODY 데이터 
		$database->prepare("
			SELECT MEASUREMENT_SQ,REG_DT,HEIGHT,WEIGHT,FAT,MUSCLE FROM bs_inbody 
					WHERE MEASUREMENT_SQ in (SELECT MEASUREMENT_SQ FROM bs_measurement WHERE USER_SQ=:USER_SQ)
			order by reg_dt asc;
		");
		$database->bind(':USER_SQ', $USER_SEQ);
		$database->execute();

		$rows = $database->fetchAll();
		$inbodydata = json_encode($rows);

		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		error_log(json_encode($inbodylist.'|'.$inbodydata));
		exit($inbodylist.'|'.$inbodydata);
		break;
		
	case 'execUserImageChange': // 직원 로그인
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		// 파라메터 취득
		$MEMBER_SQ = getAnyParameter("MEMBER_SQ", "");
		$file_name["name"] = "";
		
		$CENTER_SQ = $session->user["CENTER_SQ"];
		// 기본값 설정
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = "";
		$DEVICE_SQ = -1;
		$CATEGORY = "멤버리스트";
		$SUBCATEGORY = "사용자간이등록";
		$ACTION = " 사용자 이미지 변경하였습니다.";
		$IP = getClientIPv4();

		// File Manipulation
		if (isset($_FILES["myFileUp"]))
		{
			$upload_dir = "uploadfiles/";
			$upload_fileheader = $upload_dir."img_".date("YmdHis")."_";

			$upload_file = $upload_fileheader.str_replace(" ", "_", $_FILES["myFileUp"]["name"]);
			$filename=iconv("utf-8","CP949",$upload_file);
			$type = $_FILES["myFileUp"]["type"];
			$arr = explode('/',$type);

			if ($arr[0] == "image" && move_uploaded_file($_FILES["myFileUp"]["tmp_name"], $filename))
			{
				$file_name["name"] = $upload_file;
			}
		}
		
		// DB 조회
		$database->prepare("
			UPDATE tb_user SET USERIMAGE=:USERIMAGE
					WHERE USER_SQ=:USER_SQ
		");
		$database->bind(':USERIMAGE', $file_name["name"]);
		$database->bind(':USER_SQ', $MEMBER_SQ);
		$database->execute();

		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);
		
		// 사용자 정보 취득 
		$database->prepare("
			select a.USER_SQ,a.CENTER_SQ,c.CENTER_NM,a.USER_NM,a.GENDER, a.PHONE_NO,a.EMAIL,a.BIRTH_DT, a.REG_DT, 
					(select REG_DT from bs_measurement where USER_SQ=a.USER_SQ order by REG_DT desc LIMIT 1) as MEAS_DATE
					,a.TRAINER, b.USER_NM TRAINER_NM,REPLACE(a.COMMENT,'|','_') as COMMENT,a.USERIMAGE
			from tb_user a  left outer join tb_user b on a.TRAINER=b.USER_SQ inner join tb_center c on a.CENTER_SQ = c.CENTER_SQ
			where a.USER_SQ=:USER_SQ
		");
		$database->bind(':USER_SQ', $MEMBER_SQ);
		$database->execute();

		$row = $database->fetch();
		$memberinfo = json_encode($row);
		
		exit($memberinfo);
		break;
		
	case 'UserRegSimple': // 직원 로그인
		
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		// 파라메터 취득
		$u_name = getAnyParameter("u_name","");
		$u_year = getAnyParameter("u_year","");
		$u_gender = getAnyParameter("u_gender","");
		$u_num = getAnyParameter("u_num","");
		$u_email = getAnyParameter("u_email","");
		$u_teacher = getAnyParameter("u_teacher","");
		$u_address = getAnyParameter("u_address","");
		$u_memo = getAnyParameter("u_memo","");
		$file_name["name"] = "";
		
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$u_id = str_replace("-","", $u_num);
		if (strlen($u_id) >10 )
			$u_id = substr($u_id,strlen($u_id)-8, 8);

		$pwd_encrypted = Hash_Sha256(substr($u_id,strlen($u_id)-4, 4));
		
		// 기본값 설정
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = "";
		$DEVICE_SQ = -1;
		$CATEGORY = "멤버리스트";
		$SUBCATEGORY = "사용자간이등록";
		$ACTION = $u_name . " 사용자를 등록하였습니다.";
		$IP = getClientIPv4();

		$database->prepare("SELECT USERID FROM tb_user WHERE USERID=:USER_ID
					");
		$database->bind(':USER_ID', $u_id);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() > 0) { 
			$redirect_location = 'members.php?member_register=fail'; // 로그아웃 이후 이동화면
			break;
			$response_array["result"] = 'Fail';
			$response_array["reason"] = 'User ID Exist';
			exit(json_encode($response_array));
		}

		// File Manipulation
		if (isset($_FILES["myFileUp"]))
		{
			$upload_dir = "uploadfiles/";
			$upload_fileheader = $upload_dir."img_".date("YmdHis")."_";

			$upload_file = $upload_fileheader.str_replace(" ", "_", $_FILES["myFileUp"]["name"]);
			$filename=iconv("utf-8","CP949",$upload_file);
			$type = $_FILES["myFileUp"]["type"];
			$arr = explode('/',$type);

			if ($arr[0] == "image" && move_uploaded_file($_FILES["myFileUp"]["tmp_name"], $filename))
			{
				$file_name["name"] = $upload_file;
			}
		}
		
		// DB 조회
		$database->prepare("
			INSERT tb_user (CENTER_SQ,USERID,GENDER,USER_NM,PHONE_NO,EMAIL,BIRTH_DT,TRAINER,ADDRESS,`COMMENT`, PWD_ENCRYPTED, GRADE, ISUSE, ISMANAGER, USERIMAGE, REG_DT) VALUES
					(:CENTER_SQ,:USERID,:GENDER,:USER_NM,:PHONE_NO,:EMAIL,:BIRTH_DT,:TRAINER,:ADDRESS,:COMMENT, :PWD_ENCRYPTED, 1, 1, 0,:filename, now()  )
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':USERID', $u_id);
		$database->bind(':GENDER', $u_gender);
		$database->bind(':USER_NM', $u_name);
		$database->bind(':PHONE_NO', $u_num);
		$database->bind(':EMAIL', $u_email);
		$database->bind(':BIRTH_DT', $u_year);
		$database->bind(':TRAINER', $u_teacher);
		$database->bind(':ADDRESS', $u_address);
		$database->bind(':COMMENT', $u_memo);
		$database->bind(':PWD_ENCRYPTED', $pwd_encrypted);
		$database->bind(':filename', $file_name["name"]);
		$database->execute();
		
		if ($database->rowCount() > 0) {
			$redirect_location = 'members.php?member_register=success'; // 로그아웃 이후 이동화면
		}
		else {
			$redirect_location = 'members.php?member_register=fail'; // 로그아웃 이후 이동화면
		}

		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		break;
		
	case 'UserReg': // 회원가입 
		
		// 파라메터 취득
		$u_name = getAnyParameter("u_name","");
		$u_year = getAnyParameter("u_year","");
		$u_gender = getAnyParameter("u_gender","");
		$u_num = getAnyParameter("u_num","");
		$u_email = getAnyParameter("u_email","");
		$u_teacher = getAnyParameter("u_teacher","");
		$u_address = getAnyParameter("u_address","");
		$u_memo = getAnyParameter("u_memo","");
		$file_name["name"] = "";
		
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$u_id = str_replace("-","", $u_num);
		if (strlen($u_id) >10 )
			$u_id = substr($u_id,strlen($u_id)-8, 8);

		$pwd_encrypted = Hash_Sha256(substr($u_id,strlen($u_id)-4, 4));
		
		// 기본값 설정
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = "";
		$DEVICE_SQ = -1;
		$CATEGORY = "멤버리스트";
		$SUBCATEGORY = "사용자간이등록";
		$ACTION = $u_name . " 사용자를 등록하였습니다.";
		$IP = getClientIPv4();

		$database->prepare("SELECT USERID FROM tb_user WHERE USERID=:USER_ID
					");
		$database->bind(':USER_ID', $u_id);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() > 0) { 
			$redirect_location = 'members.php?member_register=fail'; // 로그아웃 이후 이동화면
			break;
			$response_array["result"] = 'Fail';
			$response_array["reason"] = 'User ID Exist';
			exit(json_encode($response_array));
		}

		// File Manipulation
		if (isset($_FILES["myFileUp"]))
		{
			$upload_dir = "uploadfiles/";
			$upload_fileheader = $upload_dir."img_".date("YmdHis")."_";

			$upload_file = $upload_fileheader.str_replace(" ", "_", $_FILES["myFileUp"]["name"]);
			$filename=iconv("utf-8","CP949",$upload_file);
			$type = $_FILES["myFileUp"]["type"];
			$arr = explode('/',$type);

			if ($arr[0] == "image" && move_uploaded_file($_FILES["myFileUp"]["tmp_name"], $filename))
			{
				$file_name["name"] = $upload_file;
			}
		}
		
		// DB 조회
		$database->prepare("
			INSERT tb_user (CENTER_SQ,USERID,GENDER,USER_NM,PHONE_NO,EMAIL,BIRTH_DT,TRAINER,ADDRESS,`COMMENT`, PWD_ENCRYPTED, GRADE, ISUSE, ISMANAGER, USERIMAGE, REG_DT) VALUES
					(:CENTER_SQ,:USERID,:GENDER,:USER_NM,:PHONE_NO,:EMAIL,:BIRTH_DT,:TRAINER,:ADDRESS,:COMMENT, :PWD_ENCRYPTED, 1, 1, 0,:filename, now()  )
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':USERID', $u_id);
		$database->bind(':GENDER', $u_gender);
		$database->bind(':USER_NM', $u_name);
		$database->bind(':PHONE_NO', $u_num);
		$database->bind(':EMAIL', $u_email);
		$database->bind(':BIRTH_DT', $u_year);
		$database->bind(':TRAINER', $u_teacher);
		$database->bind(':ADDRESS', $u_address);
		$database->bind(':COMMENT', $u_memo);
		$database->bind(':PWD_ENCRYPTED', $pwd_encrypted);
		$database->bind(':filename', $file_name["name"]);
		$database->execute();
		
		if ($database->rowCount() > 0) {
			$redirect_location = 'members.php?member_register=success'; // 로그아웃 이후 이동화면
		}
		else {
			$redirect_location = 'members.php?member_register=fail'; // 로그아웃 이후 이동화면
		}

		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		break;
		
	case 'execUserReg': // 회원가입 
		
		// 파라메터 취득
		$CENTER_SQ = getAnyParameter("CENTER_SQ","");
		$u_name = getAnyParameter("USERNAME","");
		$u_year = getAnyParameter("BIRTH_DT","");
		$u_gender = getAnyParameter("GENDER","");
		$u_num = getAnyParameter("TEL","");
		$u_email = getAnyParameter("EMAIL","");
		$u_teacher = getAnyParameter("TRAINER","");
		$u_address = getAnyParameter("ADDRESS","");
		$u_memo = getAnyParameter("COMMENT","");
		$file_name["name"] = "";
		
		$u_id = str_replace("-","", $u_num);
		if (strlen($u_id) >10 )
			$u_id = substr($u_id,strlen($u_id)-8, 8);

		$pwd_encrypted = Hash_Sha256(substr($u_id,strlen($u_id)-4, 4));
		
		// 기본값 설정
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = "";
		$DEVICE_SQ = -1;
		$CATEGORY = "멤버리스트";
		$SUBCATEGORY = "사용자회원가입";
		$ACTION = $u_name . " 사용자를 등록하였습니다.";
		$IP = getClientIPv4();

		$database->prepare("SELECT USERID FROM tb_user WHERE USERID=:USER_ID
					");
		$database->bind(':USER_ID', $u_id);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() > 0) { 
			$response_array["result"] = 'Fail';
			$response_array["reason"] = 'User ID Exist';
			exit(json_encode($response_array));
		}

		// File Manipulation
		if (isset($_FILES["myFileUp"]))
		{
			$upload_dir = "uploadfiles/";
			$upload_fileheader = $upload_dir."img_".date("YmdHis")."_";

			$upload_file = $upload_fileheader.str_replace(" ", "_", $_FILES["myFileUp"]["name"]);
			$filename=iconv("utf-8","CP949",$upload_file);
			$type = $_FILES["myFileUp"]["type"];
			$arr = explode('/',$type);

			if ($arr[0] == "image" && move_uploaded_file($_FILES["myFileUp"]["tmp_name"], $filename))
			{
				$file_name["name"] = $upload_file;
			}
		}
		
		// DB 조회
		$database->prepare("
			INSERT tb_user (CENTER_SQ,USERID,GENDER,USER_NM,PHONE_NO,EMAIL,BIRTH_DT,TRAINER,ADDRESS,`COMMENT`, PWD_ENCRYPTED, GRADE, ISUSE, ISMANAGER, USERIMAGE, REG_DT) VALUES
					(:CENTER_SQ,:USERID,:GENDER,:USER_NM,:PHONE_NO,:EMAIL,:BIRTH_DT,:TRAINER,:ADDRESS,:COMMENT, :PWD_ENCRYPTED, 1, 1, 0,:filename, now()  )
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':USERID', $u_id);
		$database->bind(':GENDER', $u_gender);
		$database->bind(':USER_NM', $u_name);
		$database->bind(':PHONE_NO', $u_num);
		$database->bind(':EMAIL', $u_email);
		$database->bind(':BIRTH_DT', $u_year);
		$database->bind(':TRAINER', $u_teacher);
		$database->bind(':ADDRESS', $u_address);
		$database->bind(':COMMENT', $u_memo);
		$database->bind(':PWD_ENCRYPTED', $pwd_encrypted);
		$database->bind(':filename', $file_name["name"]);
		$database->execute();
			
		if ($database->rowCount() > 0) {
			$response_array["result"] = 'Success';
			$response_array["reason"] = $u_id;
			exit(json_encode($response_array));
		}
		else {
			$response_array["result"] = 'Fail';
			$response_array["reason"] = 'User ID Exist';
			exit(json_encode($response_array));
		}

		break;
		
	case 'execManagerReg': // 회원가입 
		
		// 파라메터 취득
		$CENTER_SQ = getAnyParameter("CENTER_SQ","");
		$u_name = getAnyParameter("USERNAME","");
		$u_year = getAnyParameter("BIRTH_DT","");
		$u_gender = getAnyParameter("GENDER","");
		$u_num = getAnyParameter("TEL","");
		$u_email = getAnyParameter("EMAIL","");
		$u_teacher = getAnyParameter("TRAINER","");
		$u_address = getAnyParameter("ADDRESS","");
		$u_memo = getAnyParameter("COMMENT","");
		$file_name["name"] = "";
		
		$u_id = str_replace("-","", $u_num);
		if (strlen($u_id) >10 )
			$u_id = substr($u_id,strlen($u_id)-8, 8);

		$pwd_encrypted = Hash_Sha256(substr($u_id,strlen($u_id)-4, 4));
		
		// 기본값 설정
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = "";
		$DEVICE_SQ = -1;
		$CATEGORY = "멤버리스트";
		$SUBCATEGORY = "사용자회원가입";
		$ACTION = $u_name . " 사용자를 등록하였습니다.";
		$IP = getClientIPv4();

		$database->prepare("SELECT USERID FROM tb_user WHERE USERID=:USER_ID
					");
		$database->bind(':USER_ID', $u_id);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() > 0) { 
			$response_array["result"] = 'Fail';
			$response_array["reason"] = 'User ID Exist';
			exit(json_encode($response_array));
		}

		// File Manipulation
		if (isset($_FILES["myFileUp"]))
		{
			$upload_dir = "uploadfiles/";
			$upload_fileheader = $upload_dir."img_".date("YmdHis")."_";

			$upload_file = $upload_fileheader.str_replace(" ", "_", $_FILES["myFileUp"]["name"]);
			$filename=iconv("utf-8","CP949",$upload_file);
			$type = $_FILES["myFileUp"]["type"];
			$arr = explode('/',$type);

			if ($arr[0] == "image" && move_uploaded_file($_FILES["myFileUp"]["tmp_name"], $filename))
			{
				$file_name["name"] = $upload_file;
			}
		}
		
		// DB 조회
		$database->prepare("
			INSERT tb_user (CENTER_SQ,USERID,GENDER,USER_NM,PHONE_NO,EMAIL,BIRTH_DT,TRAINER,ADDRESS,`COMMENT`, PWD_ENCRYPTED, GRADE, ISUSE, ISMANAGER, WorkStatus, USERIMAGE, REG_DT) VALUES
					(:CENTER_SQ,:USERID,:GENDER,:USER_NM,:PHONE_NO,:EMAIL,:BIRTH_DT,:TRAINER,:ADDRESS,:COMMENT, :PWD_ENCRYPTED, 1, 1, 1, 1,:filename, now()  )
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':USERID', $u_id);
		$database->bind(':GENDER', $u_gender);
		$database->bind(':USER_NM', $u_name);
		$database->bind(':PHONE_NO', $u_num);
		$database->bind(':EMAIL', $u_email);
		$database->bind(':BIRTH_DT', $u_year);
		$database->bind(':TRAINER', $u_teacher);
		$database->bind(':ADDRESS', $u_address);
		$database->bind(':COMMENT', $u_memo);
		$database->bind(':PWD_ENCRYPTED', $pwd_encrypted);
		$database->bind(':filename', $file_name["name"]);
		$database->execute();
		
		if ($database->rowCount() > 0) {
			$response_array["result"] = 'Success';
			$response_array["reason"] = $u_id;
			exit(json_encode($response_array));
		}
		else {
			$response_array["result"] = 'Fail';
			$response_array["reason"] = 'User ID Exist';
			exit(json_encode($response_array));
		}

		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		break;
		
	case 'execCenterReg': // 회원가입 
		
		// 파라메터 취득
		$center_email = getAnyParameter("CENTER_EMAIL","");
		$center_password = getAnyParameter("PASSWD","");
		$center_name = getAnyParameter("CENTER_NAME","");
		$company_type = getAnyParameter("COMPANY_TYPE","");
		$company_name = getAnyParameter("COMPANY_NAME","");
		$company_ceoname = getAnyParameter("CEONAME","");
		$company_regno = getAnyParameter("REGNO","");
		$company_tel = getAnyParameter("TEL","");
		
		$pwd_encrypted = Hash_Sha256($center_password);
		
		// 기본값 설정
		$USER_SQ = isset($session->user["USER_SQ"]) ? $session->user["USER_SQ"] : 0;
		$USERID = "";
		$DEVICE_SQ = -1;
		$CATEGORY = "센터등록";
		$SUBCATEGORY = "사용자회원가입";
		$ACTION = $u_name . " 사용자를 등록하였습니다.";
		$IP = getClientIPv4();

		$database->prepare("SELECT CENTER_NAME FROM tb_center WHERE CENTER_NAME=:CENTER_NAME and  CENTER_NAME=:CENTER_NAME
					");
		$database->bind(':USER_ID', $u_id);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() > 0) { 
			$redirect_location = 'members.php?member_register=fail'; // 로그아웃 이후 이동화면
			break;
			$response_array["result"] = 'Fail';
			$response_array["reason"] = 'User ID Exist';
			exit(json_encode($response_array));
		}

		// File Manipulation
		if (isset($_FILES["myFileUp"]))
		{
			$upload_dir = "uploadfiles/";
			$upload_fileheader = $upload_dir."img_".date("YmdHis")."_";

			$upload_file = $upload_fileheader.str_replace(" ", "_", $_FILES["myFileUp"]["name"]);
			$filename=iconv("utf-8","CP949",$upload_file);
			$type = $_FILES["myFileUp"]["type"];
			$arr = explode('/',$type);

			if ($arr[0] == "image" && move_uploaded_file($_FILES["myFileUp"]["tmp_name"], $filename))
			{
				$file_name["name"] = $upload_file;
			}
		}
		
		// DB 조회
		$database->prepare("
			INSERT tb_user (CENTER_SQ,USERID,GENDER,USER_NM,PHONE_NO,EMAIL,BIRTH_DT,TRAINER,ADDRESS,`COMMENT`, PWD_ENCRYPTED, GRADE, ISUSE, ISMANAGER, USERIMAGE, REG_DT) VALUES
					(:CENTER_SQ,:USERID,:GENDER,:USER_NM,:PHONE_NO,:EMAIL,:BIRTH_DT,:TRAINER,:ADDRESS,:COMMENT, :PWD_ENCRYPTED, 1, 1, 1,:filename, now()  )
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':USERID', $u_id);
		$database->bind(':GENDER', $u_gender);
		$database->bind(':USER_NM', $u_name);
		$database->bind(':PHONE_NO', $u_num);
		$database->bind(':EMAIL', $u_email);
		$database->bind(':BIRTH_DT', $u_year);
		$database->bind(':TRAINER', $u_teacher);
		$database->bind(':ADDRESS', $u_address);
		$database->bind(':COMMENT', $u_memo);
		$database->bind(':PWD_ENCRYPTED', $pwd_encrypted);
		$database->bind(':filename', $file_name["name"]);
		$database->execute();
		
		if ($database->rowCount() > 0) {
			$redirect_location = 'members.php?member_register=success'; // 로그아웃 이후 이동화면
		}
		else {
			$redirect_location = 'members.php?member_register=fail'; // 로그아웃 이후 이동화면
		}

		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		break;
		
	case 'UserDupCheck': // 사용자 중복체크 
		
		// 파라메터 취득
		$USERNAME = getAnyParameter("USERNAME", "");
		$EMAIL = getAnyParameter("EMAIL", "");
		$TEL = getAnyParameter("TEL", "");

		// 기본값 설정
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = "";
		$DEVICE_SQ = -1;
		$CATEGORY = "멤버리스트";
		$SUBCATEGORY = "사용자간이등록";
		$ACTION = $USERNAME . " 사용자를 등록하였습니다.";
		$IP = getClientIPv4();

		$database->prepare("SELECT USERID FROM tb_user WHERE USER_NM=:USERNAME and  PHONE_NO=:TEL
					");
		$database->bind(':USERNAME', $USERNAME);
		$database->bind(':TEL', $TEL);
		$database->execute();

		$response_array["result"] = 'Success';
		$response_array["reason"] = 'User Not Exist';

		if ($database->rowCount() > 0) { 
			$response_array["result"] = 'Fail';
			$response_array["reason"] = 'User Exist';
		}

		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);
		exit(json_encode($response_array));

		break;
		
	case 'FindUserID': // 사용자 중복체크 

		// 파라메터 취득
		$USERNAME = getAnyParameter("USERNAME", "");
		$EMAIL = getAnyParameter("EMAIL", "");

		// 기본값 설정
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = "";
		$DEVICE_SQ = -1;
		$CATEGORY = "멤버리스트";
		$SUBCATEGORY = "사용자아이디찾기";
		$ACTION = $USERNAME . " 사용자 아이디를 탐색하였습니다.";
		$IP = getClientIPv4();

		$database->prepare("SELECT USERID FROM tb_user WHERE USER_NM=:USERNAME and  EMAIL=:EMAIL
					");
		$database->bind(':USERNAME', $USERNAME);
		$database->bind(':EMAIL', $EMAIL);
		$database->execute();

		$response_array["result"] = 'Fail';
		$response_array["reason"] = 'User Not Exist';

		if ($database->rowCount() > 0) { 
			$response_array["result"] = 'Success';
			$response_array["reason"] = 'User Exist';
		}

		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);
		exit(json_encode($response_array));

		break;
		
	case 'FindUserPASS': // 사용자 중복체크 

		// 파라메터 취득
		$USERID = getAnyParameter("USERID", "");
		$USERNAME = getAnyParameter("USERNAME", "");
		$EMAIL = getAnyParameter("EMAIL", "");

		// 기본값 설정
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = "";
		$DEVICE_SQ = -1;
		$CATEGORY = "멤버리스트";
		$SUBCATEGORY = "사용자아이디찾기";
		$ACTION = $USERNAME . " 사용자 암호를 초기화하였습니다.";
		$IP = getClientIPv4();

		$database->prepare("SELECT USERID FROM tb_user WHERE USER_NM=:USERNAME and  USERID=:USERID and  EMAIL=:EMAIL
					");
		$database->bind(':USERNAME', $USERNAME);
		$database->bind(':USERID', $USERID);
		$database->bind(':EMAIL', $EMAIL);
		$database->execute();

		$response_array["result"] = 'Fail';
		$response_array["reason"] = 'User Not Exist';

		if ($database->rowCount() > 0) { 
			$response_array["result"] = 'Success';
			$response_array["reason"] = 'User Exist';
		}

		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);
		exit(json_encode($response_array));

		break;
		
	case 'execManagerCreate': // 직원 등록
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_FILES = ' . var_export($_FILES, 1));
		error_log('$_POST = ' . var_export($_POST, 1));
		$USER_ID = getAnyParameter("USERID", "");
		$USERNAME = getAnyParameter("USERNAME", "");
		$EMAIL = getAnyParameter("EMAIL", "");
		$SEX = getAnyParameter("SEX", "");
		$TEL = getAnyParameter("TEL", "");
		$BIRTH = getAnyParameter("BIRTH", "");
		$ADDRESS = getAnyParameter("ADDRESS", "");
		$COMMENT = getAnyParameter("COMMENT", "");
		
		$WORKCATEGORY = getAnyParameter("WORKCATEGORY", "");
		$WORKSTARTDATE = getAnyParameter("WORKSTARTDATE", "");
		$file_name["name"] = "";
		
		$PWD_ENCRYPTED = Hash_Sha256(substr($TEL,strlen($TEL)-4, 4));
			
		// 기본값 설정		
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "회원정보";
		$SUBCATEGORY = "";
		$ACTION = $USERID . " 회원 상세정보를 조회하였습니다.";
		$IP = getClientIPv4();

		$database->prepare("SELECT USERID FROM tb_user WHERE USERID=:USER_ID
					");
		$database->bind(':USER_ID', $USER_ID);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() > 0) { 
			$response_array["result"] = 'Fail';
			$response_array["reason"] = 'User ID Exist';
			exit(json_encode($response_array));
		}

		// File Manipulation
		if (isset($_FILES["myFileUp"]))
		{
			$upload_dir = "uploadfiles/";
			$upload_fileheader = $upload_dir."img_".date("YmdHis")."_";

			$upload_file = $upload_fileheader.str_replace(" ", "_", $_FILES["myFileUp"]["name"]);
			$filename=iconv("utf-8","CP949",$upload_file);
			$type = $_FILES["myFileUp"]["type"];
			$arr = explode('/',$type);

			if ($arr[0] == "image" && move_uploaded_file($_FILES["myFileUp"]["tmp_name"], $filename))
			{
				$file_name["name"] = $upload_file;
			}
		}
		$database->prepare("INSERT INTO tb_user (CENTER_SQ, USERID, PWD_ENCRYPTED, USER_NM, BIRTH_DT, GENDER, PHONE_NO, EMAIL, TRAINER, ADDRESS, COMMENT, 
											WORKCATEGORY, WORKSTATUS, WORKSTARTDATE, GRADE, ISMANAGER, USERIMAGE, ISUSE, REG_DT ) 
					VALUES (:CENTER_SQ, :USER_ID, :PWD_ENCRYPTED, :USERNAME, :BIRTH, :SEX, :TEL, :EMAIL, :TRAINER, :ADDRESS, :COMMENT, :WORKCATEGORY,1, :WORKSTARTDATE, 2, 1,:filename, 1, now() ) 
					");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':USER_ID', $USER_ID);
		$database->bind(':PWD_ENCRYPTED', $PWD_ENCRYPTED);
		$database->bind(':USERNAME', $USERNAME);
		$database->bind(':BIRTH', $BIRTH);
		$database->bind(':SEX', $SEX);
		$database->bind(':TEL', $TEL);
		$database->bind(':EMAIL', $EMAIL);
		$database->bind(':TRAINER', $USER_SQ);
		$database->bind(':ADDRESS', $ADDRESS);
		$database->bind(':COMMENT', $COMMENT);
		$database->bind(':WORKCATEGORY', $WORKCATEGORY);
		$database->bind(':WORKSTARTDATE', $WORKSTARTDATE);
		$database->bind(':filename', $file_name["name"]);

		$database->execute();

		$MANAGER_SQ = Get_SingleField('tb_user', 'MAX(USER_SQ)', 'CENTER_SQ', $CENTER_SQ, " and USERID='".$USER_ID."' ",$database);
		$database->prepare("INSERT INTO tb_manager_sub (USER_SQ ) 
					VALUES (:USER_SQ ) 
					");
		$database->bind(':USER_SQ', $MANAGER_SQ);		
		$database->execute();

		
		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			exit(json_encode($response_array));
		}

		
		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);
		// DB 조회
		$database->prepare("
			select USER_SQ,CENTER_SQ,USER_NM,PHONE_NO,GENDER,ADDRESS,EMAIL,BIRTH_DT, REG_DT, GRADE,
						WORKCATEGORY, WORKTYPE, WORKSTARTDATE, WORKENDDATE, WORKSTATUS, COMMENT, LAST_DT, ISUSE
					from tb_user
					where GRADE>1 and ISUSE=1 order by REG_DT desc
		");
		$database->execute();

		$rows = $database->fetchAll();
		$memberlist = json_encode($rows);
		
		exit($memberlist);
		
		break;
		
	case 'execManagerPassChange': // 직원 암호변경
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_FILES = ' . var_export($_FILES, 1));
		error_log('$_POST = ' . var_export($_POST, 1));
		$MANAGER_SQ = getAnyParameter("USER_SQ", "");
		$PWD = getAnyParameter("PWD", "");
		$NEW_PWD = getAnyParameter("NEW_PWD", "");
		
		$PWD_ENCRYPTED = Hash_Sha256($PWD);
		$NEWPWD_ENCRYPTED = Hash_Sha256($NEW_PWD);
			
		// 기본값 설정
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "회원정보";
		$SUBCATEGORY = "";
		$ACTION = $USERID . " 회원 상세정보를 조회하였습니다.";
		$IP = getClientIPv4();

		$database->prepare("SELECT USERID FROM tb_user WHERE USER_SQ=:USER_SQ AND PWD_ENCRYPTED=:PWD_ENCRYPTED
					");
		$database->bind(':USER_SQ', $MANAGER_SQ);
		$database->bind(':PWD_ENCRYPTED', $PWD_ENCRYPTED);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			$response_array["reason"] = 'Password Incorrect';
			exit(json_encode($response_array));
		}

		$database->prepare("UPDATE tb_user SET PWD_ENCRYPTED = :PWD_ENCRYPTED where USER_SQ=:USER_SQ
					");
		$database->bind(':PWD_ENCRYPTED', $NEWPWD_ENCRYPTED);
		$database->bind(':USER_SQ', $MANAGER_SQ);
		
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			$response_array["reason"] = 'User not Exist!';
			exit(json_encode($response_array));
		}
		
		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);
		// DB 조회
		$database->prepare("
			select USER_SQ,CENTER_SQ,USER_NM,PHONE_NO,GENDER,ADDRESS,EMAIL,BIRTH_DT, REG_DT, GRADE,
						WORKCATEGORY, WORKTYPE, WORKSTARTDATE, WORKENDDATE, WORKSTATUS, COMMENT, LAST_DT, ISUSE
					from tb_user
					where GRADE>1 and ISUSE=1 order by REG_DT desc
		");
		$database->execute();

		$rows = $database->fetchAll();
		$memberlist = json_encode($rows);
		
		exit($memberlist);
		
		break;
		
	case 'execManagerDelete': // 직원 암호변경
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_FILES = ' . var_export($_FILES, 1));
		error_log('$_POST = ' . var_export($_POST, 1));
		$MANAGER_SQ = getAnyParameter("USER_SQ", "");
			
		// 기본값 설정		
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "회원정보";
		$SUBCATEGORY = "";
		$ACTION = $USERID . " 회원 상세정보를 조회하였습니다.";
		$IP = getClientIPv4();

		$database->prepare("UPDATE tb_user SET ISUSE = 0 where USER_SQ=:USER_SQ
					");
		$database->bind(':USER_SQ', $MANAGER_SQ);
		
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			$response_array["reason"] = 'User Already Disabled!';
			exit(json_encode($response_array));
		}
		
		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);
		// DB 조회
		$database->prepare("
			select USER_SQ,CENTER_SQ,USER_NM,PHONE_NO,GENDER,ADDRESS,EMAIL,BIRTH_DT, REG_DT, GRADE,
						WORKCATEGORY, WORKTYPE, WORKSTARTDATE, WORKENDDATE, WORKSTATUS, COMMENT, LAST_DT, ISUSE
					from tb_user
					where GRADE>1 and ISUSE=1 order by REG_DT desc
		");
		$database->execute();

		$rows = $database->fetchAll();
		$memberlist = json_encode($rows);
		
		exit($memberlist);
		
		break;
		
	case 'execMemberDelete': // 직원 암호변경
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_FILES = ' . var_export($_FILES, 1));
		error_log('$_POST = ' . var_export($_POST, 1));
		$MEMBERS_SQ = getAnyParameter("MEMBERS_SQ", "");
			
		// 기본값 설정		
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "회원정보";
		$SUBCATEGORY = "";
		$ACTION = $USERID . " 회원 상세정보를 조회하였습니다.";
		$IP = getClientIPv4();

		$database->prepare("UPDATE tb_user SET ISUSE = 0 where USER_SQ in (".$MEMBERS_SQ.")
					");
		$database->bind(':USER_SQ', $MEMBERS_SQ);
		
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			$response_array["reason"] = 'User Already Disabled!';
			exit(json_encode($response_array));
		}
		
		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		// DB 조회
		$database->prepare("
					select a.USER_SQ,a.CENTER_SQ,a.USER_NM,a.PHONE_NO,a.EMAIL,a.BIRTH_DT, a.REG_DT, 
										(SELECT MAX(REG_DT) FROM bs_measurement where USER_SQ=a.USER_SQ ) as MEAS_DATE,
										(SELECT MAX(REG_DT) FROM bs_measurement where USER_SQ=a.USER_SQ and MEASUREMENT_TYPE='Pose') as POSE_DT,
										(SELECT MAX(REG_DT) FROM bs_measurement where USER_SQ=a.USER_SQ and MEASUREMENT_TYPE='ROM') as ROM_DT,
										a.TRAINER, b.USER_NM TRAINER_NM, a.ISUSE
					from tb_user a  
							left outer join tb_user b on a.TRAINER=b.USER_SQ
					where a.CENTER_SQ=:CENTER_SQ and a.GRADE=1 order by a.REG_DT desc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$memberlist = json_encode($rows);
		
		exit($memberlist);
		
		break;
		
	case 'execUV_PeriodExtend': // 직원 암호변경
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_FILES = ' . var_export($_FILES, 1));
		error_log('$_POST = ' . var_export($_POST, 1));
		$MEMBERS_SQ = getAnyParameter("MEMBERS_SQ", "");
		$DAYS = getAnyParameter("DAYS", "");
			
		// 기본값 설정		
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "회원정보";
		$SUBCATEGORY = "";
		$ACTION = $USERID . " 회원 상세정보를 조회하였습니다.";
		$IP = getClientIPv4();

		$database->prepare("UPDATE tb_user_voucher SET USE_LASTDATE = DATE_ADD(USE_LASTDATE, INTERVAL :DAYS DAY) 
					WHERE MEMBER_SQ IN (".$MEMBERS_SQ.") AND USE_LASTDATE>=NOW()
					");
		$database->bind(':DAYS', $DAYS);
		
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			$response_array["reason"] = 'User voucher Not Exist!';
			exit(json_encode($response_array));
		}
		
		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		// DB 조회
		$database->prepare("
					select a.USER_SQ,a.CENTER_SQ,a.USER_NM,a.PHONE_NO,a.EMAIL,a.BIRTH_DT, a.REG_DT, 
										(SELECT MAX(REG_DT) FROM bs_measurement where USER_SQ=a.USER_SQ ) as MEAS_DATE,
										(SELECT MAX(REG_DT) FROM bs_measurement where USER_SQ=a.USER_SQ and MEASUREMENT_TYPE='Pose') as POSE_DT,
										(SELECT MAX(REG_DT) FROM bs_measurement where USER_SQ=a.USER_SQ and MEASUREMENT_TYPE='ROM') as ROM_DT,
										a.TRAINER, b.USER_NM TRAINER_NM, a.ISUSE
					from tb_user a  
							left outer join tb_user b on a.TRAINER=b.USER_SQ
					where a.CENTER_SQ=:CENTER_SQ and a.GRADE=1 order by a.REG_DT desc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$memberlist = json_encode($rows);

		exit($memberlist);
		
		break;
			
	case 'execUV_PeriodPauseCancel': // 이용정지 취소 
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_FILES = ' . var_export($_FILES, 1));
		error_log('$_POST = ' . var_export($_POST, 1));
		$UV_SQ = getAnyParameter("UV_SQ", "");
		$MEMBER_SQ = getAnyParameter("MEMBER_SQ", "");
		$START_DATE = getAnyParameter("START_DATE", "");
			
		// 기본값 설정		
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "회원정보";
		$SUBCATEGORY = "";
		$ACTION = $USERID . " 회원 상세정보를 조회하였습니다.";
		$IP = getClientIPv4();

		// 현재 정지기간 조회.
		$database->prepare("SELECT PAUSE_SQ,UV_SQ,START_DATE,END_DATE,DAYS 
						FROM tb_user_voucher_pause 
						WHERE UV_SQ=:UV_SQ AND ISUSE=1
						ORDER BY PAUSE_SQ DESC LIMIT 1
					");
		$database->bind(':UV_SQ', $UV_SQ);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() == 0) { 
			$response_array["result"] = 'Fail';
			$response_array["reason"] = 'Pause Not Exist';
			exit(json_encode($response_array));
		}
		$row = $database->fetch();
		$END_DATE_OLD = $row["END_DATE"];
		$START_DATE_OLD = $row["START_DATE"];
		$DAYS_OLD = $row["DAYS"];
		$PAUSE_SQ = $row["PAUSE_SQ"];
		
		// 정지기간을 전날까지로 재설정
		//$END_DATE = date("Y-m-d", time());
		//$END_DATE = date("Y-m-d", strtotime($END_DATE." -1 day"))."00:00:00";
		$END_DATE = date("Y-m-d", strtotime("today - 1 day"))." 00:00:00";

		// 이미 정지기간이 지났으면 오류.
		$response_array["result"] = 'Success';
		if ($END_DATE_OLD < $END_DATE) { 
			$response_array["result"] = 'Fail';
			$response_array["reason"] = 'The day already past!';
			exit(json_encode($response_array));
		}

		$olddays = (intval((strtotime($END_DATE_OLD)-strtotime($START_DATE))/86400))+1;
		// 되돌려야는날짜를 계산. 
		$restoredays = (intval((strtotime($END_DATE_OLD)-strtotime($END_DATE))/86400));
		$pausecancel = 0;
		$pausedays = 0;

		error_log('PAUSE_SQ = ' . $PAUSE_SQ);
		error_log('pausecancel = ' . $pausecancel);
		// 정지 기간이 시작 전이면 취소 
		if ($START_DATE_OLD>=$END_DATE) {
			$restoredays = $DAYS_OLD;
			$pausecancel = 1;
		} else {
			$pausedays = (intval((strtotime($END_DATE)-strtotime($START_DATE_OLD))/86400))+1;
		}
		
		$database->prepare("UPDATE tb_user_voucher SET USE_LASTDATE = DATE_ADD(USE_LASTDATE, INTERVAL :DAYS DAY), USE_STATUS=2
					WHERE UV_SQ =:UV_SQ AND USE_LASTDATE>=:START_DATE
					");
		$database->bind(':DAYS', -$restoredays);
		$database->bind(':UV_SQ', $UV_SQ);
		$database->bind(':START_DATE', $START_DATE);
		
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			$response_array["reason"] = 'Voucher Not Valid!';
			exit(json_encode($response_array));
		}
		
		if ($pausecancel==0) {
			// 이용권 정지를중단 
			$database->prepare("UPDATE tb_user_voucher_pause SET END_DATE=:END_DATE, DAYS=:DAY, ISUSE=0
								WHERE PAUSE_SQ=:PAUSE_SQ
						");
			$database->bind(':END_DATE', $END_DATE);
			$database->bind(':DAY', $pausedays);
			$database->bind(':PAUSE_SQ', $PAUSE_SQ);

			$database->execute();

			$response_array["result"] = 'Success';

			if ($database->rowCount() < 1) { 
				$response_array["result"] = 'Fail';
				$response_array["reason"] = 'Pause Not Updated! Not Restarted.';
				exit(json_encode($response_array));
			}
		} else {
			// 이용권 정지를 취소
			$database->prepare("UPDATE tb_user_voucher_pause SET ISUSE=0
								WHERE PAUSE_SQ=:PAUSE_SQ
						");
			$database->bind(':PAUSE_SQ', $PAUSE_SQ);

			$database->execute();

			$response_array["result"] = 'Success';

			if ($database->rowCount() < 1) { 
				$response_array["result"] = 'Fail';
				$response_array["reason"] = 'Pause Not Updated! Not Canceled.';
				exit(json_encode($response_array));
			}
		}
		
		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		// 사용자 이용권 리스트 
		$database->prepare("
			SELECT UV_SQ,MEMBER_SQ,VOUCHER_SQ,VOUCHER_NAME,VOUCHER_TYPE,b.DESCRIPTION as VOUCHER_TYPE_NAME,USE_TYPE,c.DESCRIPTION as USE_TYPE_NAME,
					PERIOD_TYPE,d.DESCRIPTION as PERIOD_TYPE_NAME,PERIOD,PERIOD_UNIT,f.DESCRIPTION as PERIOD_UNIT_NAME,
					COUNT_TYPE,e.DESCRIPTION as COUNT_TYPE_NAME,COUNT,ENTERLIMIT_DAY,ENTERLIMIT_WEEK,USEDCOUNT,
					(SELECT COUNT(*) FROM tb_reservation WHERE USER_SQ=a.MEMBER_SQ and UV_SQ=a.UV_SQ and RESERV_STATUS=1) RESERV_COUNT,
					USE_STATUS,USE_STARTDATE,USE_LASTDATE,SELLER_SQ, g.USER_NM as SELLER_NM,TRAINER_SQ, h.USER_NM as TRAINER_NM
 			FROM tb_user_voucher a
				  left outer join tb_common b on a.VOUCHER_TYPE=b.CODE and b.BASE_CD='CD004'
				  left outer join tb_common c on a.USE_TYPE=c.CODE and c.BASE_CD='CD005'
				  left outer join tb_common d on a.PERIOD_TYPE=d.CODE and d.BASE_CD='CD006'
				  left outer join tb_common e on a.COUNT_TYPE=e.CODE and e.BASE_CD='CD007'
				  left outer join tb_common f on a.PERIOD_UNIT=f.CODE and f.BASE_CD='CD010'
				  left outer join tb_user g on g.USER_SQ=a.SELLER_SQ
				  left outer join tb_user h on h.USER_SQ=a.TRAINER_SQ
			WHERE MEMBER_SQ=:MEMBER_SQ
			ORDER BY UV_SQ
		");
		$database->bind(':MEMBER_SQ', $MEMBER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$uservoucherlist = json_encode($rows);

		
		exit($uservoucherlist);
		
		break;
	
	case 'execUV_PeriodPause': // 직원 암호변경
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_FILES = ' . var_export($_FILES, 1));
		error_log('$_POST = ' . var_export($_POST, 1));
		$UV_SQ = getAnyParameter("UV_SQ", "");
		$MEMBER_SQ = getAnyParameter("MEMBER_SQ", "");
		$DAYS = getAnyParameter("DAYS", 0);
		$START_DATE = getAnyParameter("START_DATE", "");
			
		// 기본값 설정		
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "회원정보";
		$SUBCATEGORY = "";
		$ACTION = $USERID . " 회원 상세정보를 조회하였습니다.";
		$IP = getClientIPv4();

		// 기간 중복 조회 
		$database->prepare("SELECT UV_SQ FROM tb_user_voucher_pause WHERE UV_SQ=:UV_SQ AND 
						((START_DATE<=:START_DATE AND END_DATE>=:START_DATE2) OR (START_DATE<=DATE_ADD(:START_DATE3, INTERVAL :DAYS DAY) AND END_DATE>=DATE_ADD(:START_DATE4, INTERVAL :DAYS2 DAY)))
						AND ISUSE=1
					");
		$database->bind(':UV_SQ', $UV_SQ);
		$database->bind(':START_DATE', $START_DATE);
		$database->bind(':START_DATE2', $START_DATE);
		$database->bind(':START_DATE3', $START_DATE);
		$database->bind(':DAYS', $DAYS-1);
		$database->bind(':START_DATE4', $START_DATE);
		$database->bind(':DAYS2', $DAYS-1);
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() >0) { 
			$response_array["result"] = 'Fail';
			$response_array["reason"] = 'Pause Period Overlapped';
			exit(json_encode($response_array));
		}

		$database->prepare("UPDATE tb_user_voucher SET USE_LASTDATE = DATE_ADD(USE_LASTDATE, INTERVAL :DAYS DAY), USE_STATUS=4
					WHERE UV_SQ =:UV_SQ AND USE_LASTDATE>=:START_DATE
					");
		$database->bind(':DAYS', $DAYS);
		$database->bind(':UV_SQ', $UV_SQ);
		$database->bind(':START_DATE', $START_DATE);
		
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			$response_array["reason"] = 'User voucher Not Valid!';
			exit(json_encode($response_array));
		}

		$database->prepare("INSERT into tb_user_voucher_pause (UV_SQ,START_DATE,END_DATE,DAYS)
								VALUES (:UV_SQ,:START_DATE,DATE_ADD(:START_DATE1, INTERVAL :DAYS DAY),:DAYS1)
					");
		$database->bind(':UV_SQ', $UV_SQ);
		$database->bind(':START_DATE', $START_DATE);
		$database->bind(':START_DATE1', $START_DATE);
		$database->bind(':DAYS', $DAYS-1);
		$database->bind(':DAYS1', $DAYS);
		
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			$response_array["reason"] = 'User voucher Not Valid!';
			exit(json_encode($response_array));
		}
		
		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		// 사용자 이용권 리스트 
		$database->prepare("
			SELECT UV_SQ,MEMBER_SQ,VOUCHER_SQ,VOUCHER_NAME,VOUCHER_TYPE,b.DESCRIPTION as VOUCHER_TYPE_NAME,USE_TYPE,c.DESCRIPTION as USE_TYPE_NAME,
					PERIOD_TYPE,d.DESCRIPTION as PERIOD_TYPE_NAME,PERIOD,PERIOD_UNIT,f.DESCRIPTION as PERIOD_UNIT_NAME,
					COUNT_TYPE,e.DESCRIPTION as COUNT_TYPE_NAME,COUNT,ENTERLIMIT_DAY,ENTERLIMIT_WEEK,USEDCOUNT,
					(SELECT COUNT(*) FROM tb_reservation WHERE USER_SQ=a.MEMBER_SQ and UV_SQ=a.UV_SQ and RESERV_STATUS=1) RESERV_COUNT,
					USE_STATUS,USE_STARTDATE,USE_LASTDATE,SELLER_SQ, g.USER_NM as SELLER_NM,TRAINER_SQ, h.USER_NM as TRAINER_NM
 			FROM tb_user_voucher a
				  left outer join tb_common b on a.VOUCHER_TYPE=b.CODE and b.BASE_CD='CD004'
				  left outer join tb_common c on a.USE_TYPE=c.CODE and c.BASE_CD='CD005'
				  left outer join tb_common d on a.PERIOD_TYPE=d.CODE and d.BASE_CD='CD006'
				  left outer join tb_common e on a.COUNT_TYPE=e.CODE and e.BASE_CD='CD007'
				  left outer join tb_common f on a.PERIOD_UNIT=f.CODE and f.BASE_CD='CD010'
				  left outer join tb_user g on g.USER_SQ=a.SELLER_SQ
				  left outer join tb_user h on h.USER_SQ=a.TRAINER_SQ
			WHERE MEMBER_SQ=:MEMBER_SQ
			ORDER BY UV_SQ
		");
		$database->bind(':MEMBER_SQ', $MEMBER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$uservoucherlist = json_encode($rows);

		
		exit($uservoucherlist);
		
		break;
	
	case 'execUV_PeriodChange': // 직원 암호변경
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_FILES = ' . var_export($_FILES, 1));
		error_log('$_POST = ' . var_export($_POST, 1));
		
		$MEMBER_SQ = getAnyParameter("MEMBER_SQ", "");
		$UV_SQ = getAnyParameter("UV_SQ", "");
		$USE_STARTDATE = getAnyParameter("USE_STARTDATE", "");
		$USE_LASTDATE = getAnyParameter("USE_LASTDATE", "");
		$COUNT = getAnyParameter("COUNT", "");
			
		// 기본값 설정		
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "회원정보";
		$SUBCATEGORY = "";
		$ACTION = $USERID . " 회원 상세정보를 조회하였습니다.";
		$IP = getClientIPv4();

		$database->prepare("UPDATE tb_user_voucher SET USE_STARTDATE = :USE_STARTDATE,
								USE_LASTDATE=:USE_LASTDATE, COUNT=:COUNT
							where UV_SQ=:UV_SQ
					");
		$database->bind(':USE_STARTDATE', $USE_STARTDATE);
		$database->bind(':USE_LASTDATE', $USE_LASTDATE);
		$database->bind(':COUNT', $COUNT);
		$database->bind(':UV_SQ', $UV_SQ);
		
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			$response_array["reason"] = 'User voucher Not Exist!';
			exit(json_encode($response_array));
		}
		
		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);
		// DB 조회
		
		// 사용자 이용권 리스트 
		$database->prepare("
			SELECT UV_SQ,MEMBER_SQ,VOUCHER_SQ,VOUCHER_NAME,VOUCHER_TYPE,b.DESCRIPTION as VOUCHER_TYPE_NAME,USE_TYPE,c.DESCRIPTION as USE_TYPE_NAME,
					PERIOD_TYPE,d.DESCRIPTION as PERIOD_TYPE_NAME,PERIOD,PERIOD_UNIT,f.DESCRIPTION as PERIOD_UNIT_NAME,
					COUNT_TYPE,e.DESCRIPTION as COUNT_TYPE_NAME,COUNT,ENTERLIMIT_DAY,ENTERLIMIT_WEEK,USEDCOUNT,
					(SELECT COUNT(*) FROM tb_reservation WHERE USER_SQ=a.MEMBER_SQ and UV_SQ=a.UV_SQ and RESERV_STATUS=1) RESERV_COUNT,
					USE_STATUS,USE_STARTDATE,USE_LASTDATE,SELLER_SQ, g.USER_NM as SELLER_NM,TRAINER_SQ, h.USER_NM as TRAINER_NM
 			FROM tb_user_voucher a
				  left outer join tb_common b on a.VOUCHER_TYPE=b.CODE and b.BASE_CD='CD004'
				  left outer join tb_common c on a.USE_TYPE=c.CODE and c.BASE_CD='CD005'
				  left outer join tb_common d on a.PERIOD_TYPE=d.CODE and d.BASE_CD='CD006'
				  left outer join tb_common e on a.COUNT_TYPE=e.CODE and e.BASE_CD='CD007'
				  left outer join tb_common f on a.PERIOD_UNIT=f.CODE and f.BASE_CD='CD010'
				  left outer join tb_user g on g.USER_SQ=a.SELLER_SQ
				  left outer join tb_user h on h.USER_SQ=a.TRAINER_SQ
			WHERE MEMBER_SQ=:MEMBER_SQ
			ORDER BY UV_SQ
		");
		$database->bind(':MEMBER_SQ', $MEMBER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$uservoucherlist = json_encode($rows);
		
		exit($uservoucherlist);
		
		break;
		
	case 'execTrainerChange': // 직원 암호변경
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_FILES = ' . var_export($_FILES, 1));
		error_log('$_POST = ' . var_export($_POST, 1));
		$MEMBER_SQ = getAnyParameter("MEMBER_SQ", "");
		$TRAINER_SQ = getAnyParameter("TRAINER_SQ", "");

		// 기본값 설정		
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$GROUP = 1;
		$CATEGORY = 2;
		$ACTION = Get_UserInfo($MEMBER_SQ,$database)."회원님의 담당자를 ".Get_TrainerInfo($TRAINER_SQ,$database)."트레이너로 변경하였습니다.";
		$IP = getClientIPv4();

		$database->prepare("UPDATE tb_user SET TRAINER = :TRAINER_SQ
							where USER_SQ=:MEMBER_SQ
					");
		$database->bind(':TRAINER_SQ', $TRAINER_SQ);
		$database->bind(':MEMBER_SQ', $MEMBER_SQ);

		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			$response_array["reason"] = 'Trainer Not Changed!';
			exit(json_encode($response_array));
		}

		// 로그 저장
		insert_Log_History($CENTER_SQ,$USER_SQ,$MEMBER_SQ, $DEVICE_SQ, $IP, $GROUP, $CATEGORY, $ACTION,$database);
		// DB 조회
		
		exit(json_encode($response_array));

		break;
		
	case 'execUV_TrainerChange': // 직원 암호변경
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_FILES = ' . var_export($_FILES, 1));
		error_log('$_POST = ' . var_export($_POST, 1));
		$MEMBER_SQ = getAnyParameter("MEMBER_SQ", "");
		$UV_SQ = getAnyParameter("UV_SQ", "");
		$TRAINER_SQ = getAnyParameter("TRAINER_SQ", "");
			
		// 기본값 설정		
		$CENTER_SQ = $session->user["CENTER_SQ"];
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$GROUP = 1;
		$CATEGORY = 2;
		$ACTION = Get_UserInfo($MEMBER_SQ,$database)."회원님의 ".Get_VoucherInfo($UV_SQ,$database)."이용권의 담당강사를 ".Get_TrainerInfo($TRAINER_SQ,$database)."트레이너로 변경하였습니다.";
		$IP = getClientIPv4();

		$database->prepare("UPDATE tb_user_voucher SET TRAINER_SQ = :TRAINER_SQ
							where UV_SQ=:UV_SQ
					");
		$database->bind(':TRAINER_SQ', $TRAINER_SQ);
		$database->bind(':UV_SQ', $UV_SQ);
		
		$database->execute();

		$response_array["result"] = 'Success';

		if ($database->rowCount() < 1) { 
			$response_array["result"] = 'Fail';
			$response_array["reason"] = 'User voucher Not Exist!';
			exit(json_encode($response_array));
		}
		
		// 로그 저장
		insert_Log_History($CENTER_SQ,$USER_SQ,$MEMBER_SQ, $DEVICE_SQ, $IP, $GROUP, $CATEGORY, $ACTION,$database);
		// DB 조회
		
		// 사용자 이용권 리스트 
		$database->prepare("
			SELECT UV_SQ,MEMBER_SQ,VOUCHER_SQ,VOUCHER_NAME,VOUCHER_TYPE,b.DESCRIPTION as VOUCHER_TYPE_NAME,USE_TYPE,c.DESCRIPTION as USE_TYPE_NAME,
					PERIOD_TYPE,d.DESCRIPTION as PERIOD_TYPE_NAME,PERIOD,PERIOD_UNIT,f.DESCRIPTION as PERIOD_UNIT_NAME,
					COUNT_TYPE,e.DESCRIPTION as COUNT_TYPE_NAME,COUNT,ENTERLIMIT_DAY,ENTERLIMIT_WEEK,USEDCOUNT,
					(SELECT COUNT(*) FROM tb_reservation WHERE USER_SQ=a.MEMBER_SQ and UV_SQ=a.UV_SQ and RESERV_STATUS=1) RESERV_COUNT,
					USE_STATUS,USE_STARTDATE,USE_LASTDATE,SELLER_SQ, g.USER_NM as SELLER_NM,TRAINER_SQ, h.USER_NM as TRAINER_NM, h.PHONE_NO
 			FROM tb_user_voucher a
				  left outer join tb_common b on a.VOUCHER_TYPE=b.CODE and b.BASE_CD='CD004'
				  left outer join tb_common c on a.USE_TYPE=c.CODE and c.BASE_CD='CD005'
				  left outer join tb_common d on a.PERIOD_TYPE=d.CODE and d.BASE_CD='CD006'
				  left outer join tb_common e on a.COUNT_TYPE=e.CODE and e.BASE_CD='CD007'
				  left outer join tb_common f on a.PERIOD_UNIT=f.CODE and f.BASE_CD='CD010'
				  left outer join tb_user g on g.USER_SQ=a.SELLER_SQ
				  left outer join tb_user h on h.USER_SQ=a.TRAINER_SQ
			WHERE MEMBER_SQ=:MEMBER_SQ
			ORDER BY UV_SQ
		");
		$database->bind(':MEMBER_SQ', $MEMBER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$uservoucherlist = json_encode($rows);

		
		exit($uservoucherlist);
		
		break;
	
	case 'getUserData': // 사용자 리스트 취득 
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		// 파라메터 취득
		$MEMBER_SQ = getAnyParameter("u_seq","");
		$CENTER_SQ = $session->user["CENTER_SQ"];

		// 기본값 설정
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "회원정보";
		$SUBCATEGORY = "";
		$ACTION = $USERID . " 회원 상세정보를 조회하였습니다.";
		$IP = getClientIPv4();
		
		// 사용자 정보 취득 
		$database->prepare("
			select a.USER_SQ,a.CENTER_SQ,c.CENTER_NM,a.USER_NM,a.USERID, a.GENDER, a.PHONE_NO,a.EMAIL,a.BIRTH_DT, a.REG_DT, 
					(select REG_DT from bs_measurement where USER_SQ=a.USER_SQ order by REG_DT desc LIMIT 1) as MEAS_DATE
					,a.TRAINER, b.USER_NM TRAINER_NM,REPLACE(a.COMMENT,'|','_') as COMMENT,a.USERIMAGE
			from tb_user a  left outer join tb_user b on a.TRAINER=b.USER_SQ inner join tb_center c on a.CENTER_SQ = c.CENTER_SQ
			where a.USER_SQ=:USER_SQ
		");
		$database->bind(':USER_SQ', $MEMBER_SQ);
		$database->execute();

		$row = $database->fetch();
		$memberinfo = json_encode($row);
		
		// 신체정보 리스트
		$database->prepare("
			select MEASUREMENT_SQ,USER_SQ,DEVICE_SQ,MEASUREMENT_TYPE,DONE, REG_DT
					from bs_measurement where DONE=1 AND MEASUREMENT_TYPE='INBODY' AND USER_SQ=:USER_SQ order by REG_DT DESC
		");
		$database->bind(':USER_SQ', $MEMBER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$inbodylist = json_encode($rows);
		
		// POSE 정보 리스트
		$database->prepare("
			select MEASUREMENT_SQ,USER_SQ,DEVICE_SQ,MEASUREMENT_TYPE,DONE,REG_DT
					from bs_measurement where DONE=1 AND MEASUREMENT_TYPE='Pose' AND USER_SQ=:USER_SQ order by REG_DT DESC
		");
		$database->bind(':USER_SQ', $MEMBER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$poselist = json_encode($rows);
		
		// ROM 리스트
		$database->prepare("
			select MEASUREMENT_SQ,USER_SQ,DEVICE_SQ,MEASUREMENT_TYPE,DONE,REG_DT
					from bs_measurement where DONE=1 AND MEASUREMENT_TYPE='ROM' AND USER_SQ=:USER_SQ order by REG_DT DESC
		");
		$database->bind(':USER_SQ', $MEMBER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$romlist = json_encode($rows);
		
		// 건강정보  리스트
		$database->prepare("
			select MEASUREMENT_SQ,USER_SQ,DEVICE_SQ,MEASUREMENT_TYPE,DONE,REG_DT
					from bs_measurement where DONE=1 AND MEASUREMENT_TYPE='HEALTH' AND USER_SQ=:USER_SQ order by REG_DT DESC
		");
		$database->bind(':USER_SQ', $MEMBER_SQ);
		$database->execute();
		
		$rows = $database->fetchAll();
		$healthlist = json_encode($rows);
		
		// INBODY 데이터 
		$database->prepare("
			SELECT MEASUREMENT_SQ,REG_DT,HEIGHT,WEIGHT,FAT,MUSCLE FROM bs_inbody 
					WHERE MEASUREMENT_SQ in (SELECT MEASUREMENT_SQ FROM bs_measurement WHERE USER_SQ=:USER_SQ)
			order by REG_DT asc;
		");
		$database->bind(':USER_SQ', $MEMBER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$inbodydata = json_encode($rows);
		
		// 포즈 측정 결과 데이터
		$database->prepare("
			select a.MEASUREMENT_SQ, a.REG_DT, 
                CASE WHEN (select angle from bs_posedata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and POSESTANDARD_SQ=1) = 0
                        THEN (select angle from bs_posedata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and POSESTANDARD_SQ=2)
                        ELSE (select angle from bs_posedata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and POSESTANDARD_SQ=1) end as front_Neck,
				(select angle from bs_posedata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and POSESTANDARD_SQ=3) front_RShoulder,
				(select angle from bs_posedata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and POSESTANDARD_SQ=4) front_LShoulder,
				(select angle from bs_posedata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and POSESTANDARD_SQ=5) front_RPelvis,
				(select angle from bs_posedata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and POSESTANDARD_SQ=6) front_LPelvis,
				(select angle from bs_posedata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and POSESTANDARD_SQ=7) front_RLeg,
				(select angle from bs_posedata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and POSESTANDARD_SQ=8) front_LLeg,
				(select angle from bs_posedata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and POSESTANDARD_SQ=9) side_Neck,
				(select angle from bs_posedata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and POSESTANDARD_SQ=11) side_Shoulder,
				(select angle from bs_posedata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and POSESTANDARD_SQ=13) side_Pelvis,
				(select angle from bs_posedata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and POSESTANDARD_SQ=15) side_Leg
			from bs_measurement a  
			where user_sq=:USER_SQ AND MEASUREMENT_TYPE='Pose' and done=1
			order by a.REG_DT asc;
		");
		$database->bind(':USER_SQ', $MEMBER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$posedata = json_encode($rows);
		
		// 포즈 측정 결과 이미지 데이터
		$database->prepare("
			select a.MEASUREMENT_SQ, a.REG_DT, 
				(select PICTURE_NM from bs_picture where MEASUREMENT_SQ=a.MEASUREMENT_SQ and POSE_TYPE='front') FRONT_PICTURE,
				(select PICTURE_NM from bs_picture where MEASUREMENT_SQ=a.MEASUREMENT_SQ and POSE_TYPE='side') SIDE_PICTURE,
				(select UPLOAD_ROOT from bs_picture where MEASUREMENT_SQ=a.MEASUREMENT_SQ and POSE_TYPE='front') UPLOAD_ROOT
			from bs_measurement a  
			where user_sq=:USER_SQ AND MEASUREMENT_TYPE='Pose' and done=1
			order by a.REG_DT asc;
		");
		$database->bind(':USER_SQ', $MEMBER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$posepicture = json_encode($rows);
        
		// ROM 데이터
		$database->prepare("
			select a.MEASUREMENT_SQ, a.REG_DT, 
				(select angle from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=2) front_Neck_right,
				(select angle from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=1) front_Neck_left,
				(select angle from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=4) front_Shoulder_right,
				(select angle from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=3) front_Shoulder_left,
				(select angle from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=6) front_Waist_right,
				(select angle from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=5) front_Waist_left,
				(select angle from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=8) front_Hip_right,
				(select angle from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=7) front_Hip_left,
				(select angle from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=9) side_Neck_front,
				(select angle from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=10) side_Neck_back,
				(select angle from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=11) side_ShoulderL_front,
				(select angle from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=12) side_ShoulderL_back,
				(select angle from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=13) side_ShoulderR_front,
				(select angle from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=14) side_ShoulderR_back,
				(select angle from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=15) side_Waist_front,
				(select angle from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=16) side_Waist_back,
				(select angle from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=17) side_HipL_front,
				(select angle from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=18) side_HipL_back,
				(select angle from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=19) side_HipR_front,
				(select angle from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=20) side_HipR_back,
				(select ROM_GRADE from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=2) front_Neck_right_grade,
				(select ROM_GRADE from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=1) front_Neck_left_grade,
				(select ROM_GRADE from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=4) front_Shoulder_right_grade,
				(select ROM_GRADE from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=3) front_Shoulder_left_grade,
				(select ROM_GRADE from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=6) front_Waist_right_grade,
				(select ROM_GRADE from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=5) front_Waist_left_grade,
				(select ROM_GRADE from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=8) front_Hip_right_grade,
				(select ROM_GRADE from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=7) front_Hip_left_grade,
				(select ROM_GRADE from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=9) side_Neck_front_grade,
				(select ROM_GRADE from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=10) side_Neck_back_grade,
				(select ROM_GRADE from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=11) side_ShoulderL_front_grade,
				(select ROM_GRADE from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=12) side_ShoulderL_back_grade,
				(select ROM_GRADE from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=13) side_ShoulderR_front_grade,
				(select ROM_GRADE from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=14) side_ShoulderR_back_grade,
				(select ROM_GRADE from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=15) side_Waist_front_grade,
				(select ROM_GRADE from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=16) side_Waist_back_grade,
				(select ROM_GRADE from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=17) side_HipL_front_grade,
				(select ROM_GRADE from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=18) side_HipL_back_grade,
				(select ROM_GRADE from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=19) side_HipR_front_grade,
				(select ROM_GRADE from bs_romdata where MEASUREMENT_SQ=a.MEASUREMENT_SQ and ROMSTANDARD_SQ=20) side_HipR_back_grade
			from bs_measurement a  
			where user_sq=:USER_SQ AND MEASUREMENT_TYPE='ROM' and done=1
			order by a.REG_DT asc;
		");
		$database->bind(':USER_SQ', $MEMBER_SQ);
		$database->execute();

		$rows = $database->fetchAll();

		$romdata = json_encode($rows);

		// HEALTH 데이터 
		$database->prepare("
			SELECT MEASUREMENT_SQ,REG_DT,HR,SBP,DBP,GLUCOSE,HbA1c,TC,HDL,LDL,TG,Lactate FROM bs_health 
				WHERE MEASUREMENT_SQ in (SELECT MEASUREMENT_SQ FROM bs_measurement WHERE USER_SQ=:USER_SQ) 
			order by REG_DT asc;
		");
		$database->bind(':USER_SQ', $MEMBER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$healthdata = json_encode($rows);
		
		// 포즈 표준 데이터
		$database->prepare("
			select 'caution' level, 
				(select caution from bs_posestandard where POSESTANDARD_SQ=1) front_Neck,
				(select caution from bs_posestandard where POSESTANDARD_SQ=3) front_RShoulder,
				(select caution from bs_posestandard where POSESTANDARD_SQ=4) front_LShoulder,
				(select caution from bs_posestandard where POSESTANDARD_SQ=5) front_RPelvis,
				(select caution from bs_posestandard where POSESTANDARD_SQ=6) front_LPelvis,
				(select caution from bs_posestandard where POSESTANDARD_SQ=7) front_RLeg,
				(select caution from bs_posestandard where POSESTANDARD_SQ=8) front_LLeg,
				(select caution from bs_posestandard where POSESTANDARD_SQ=9) side_Neck,
				(select caution from bs_posestandard where POSESTANDARD_SQ=11) side_Shoulder,
				(select caution from bs_posestandard where POSESTANDARD_SQ=13) side_Pelvis,
				(select caution from bs_posestandard where POSESTANDARD_SQ=15) side_Leg
			union
			select 'danger' level,
				(select danger from bs_posestandard where POSESTANDARD_SQ=1) front_Neck,
				(select danger from bs_posestandard where POSESTANDARD_SQ=3) front_RShoulder,
				(select danger from bs_posestandard where POSESTANDARD_SQ=4) front_LShoulder,
				(select danger from bs_posestandard where POSESTANDARD_SQ=5) front_RPelvis,
				(select danger from bs_posestandard where POSESTANDARD_SQ=6) front_LPelvis,
				(select danger from bs_posestandard where POSESTANDARD_SQ=7) front_RLeg,
				(select danger from bs_posestandard where POSESTANDARD_SQ=8) front_LLeg,
				(select danger from bs_posestandard where POSESTANDARD_SQ=9) side_Neck,
				(select danger from bs_posestandard where POSESTANDARD_SQ=11) side_Shoulder,
				(select danger from bs_posestandard where POSESTANDARD_SQ=13) side_Pelvis,
				(select danger from bs_posestandard where POSESTANDARD_SQ=15) side_Leg
		");
		$database->bind(':USER_SQ', $MEMBER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$posestandard = json_encode($rows);

		// ROM 표준 데이터 
		$database->prepare("
			select 'caution' level, 
				(select notbad_angle from bs_romstandard where ROMSTANDARD_SQ=1) front_Neck_right,
				(select notbad_angle from bs_romstandard where ROMSTANDARD_SQ=2) front_Neck_left,
				(select notbad_angle from bs_romstandard where ROMSTANDARD_SQ=3) front_Shoulder_right,
				(select notbad_angle from bs_romstandard where ROMSTANDARD_SQ=4) front_Shoulder_left,
				(select notbad_angle from bs_romstandard where ROMSTANDARD_SQ=5) front_Waist_right,
				(select notbad_angle from bs_romstandard where ROMSTANDARD_SQ=6) front_Waist_left,
				(select notbad_angle from bs_romstandard where ROMSTANDARD_SQ=7) front_Hip_right,
				(select notbad_angle from bs_romstandard where ROMSTANDARD_SQ=8) front_Hip_left,
				(select notbad_angle from bs_romstandard where ROMSTANDARD_SQ=9) side_Neck_front,
				(select notbad_angle from bs_romstandard where ROMSTANDARD_SQ=10) side_Neck_back,
				(select notbad_angle from bs_romstandard where ROMSTANDARD_SQ=11) side_ShoulderL_front,
				(select notbad_angle from bs_romstandard where ROMSTANDARD_SQ=12) side_ShoulderL_back,
				(select notbad_angle from bs_romstandard where ROMSTANDARD_SQ=13) side_ShoulderR_front,
				(select notbad_angle from bs_romstandard where ROMSTANDARD_SQ=14) side_ShoulderR_back,
				(select notbad_angle from bs_romstandard where ROMSTANDARD_SQ=15) side_Waist_front,
				(select notbad_angle from bs_romstandard where ROMSTANDARD_SQ=16) side_Waist_back,
				(select notbad_angle from bs_romstandard where ROMSTANDARD_SQ=17) side_HipL_front,
				(select notbad_angle from bs_romstandard where ROMSTANDARD_SQ=18) side_HipL_back,
				(select notbad_angle from bs_romstandard where ROMSTANDARD_SQ=19) side_HipR_front,
				(select notbad_angle from bs_romstandard where ROMSTANDARD_SQ=20) side_HipR_back
			union
			select 'danger' level,
				(select bad_angle from bs_romstandard where ROMSTANDARD_SQ=1) front_Neck_right,
				(select bad_angle from bs_romstandard where ROMSTANDARD_SQ=2) front_Neck_left,
				(select bad_angle from bs_romstandard where ROMSTANDARD_SQ=3) front_Shoulder_right,
				(select bad_angle from bs_romstandard where ROMSTANDARD_SQ=4) front_Shoulder_left,
				(select bad_angle from bs_romstandard where ROMSTANDARD_SQ=5) front_Waist_right,
				(select bad_angle from bs_romstandard where ROMSTANDARD_SQ=6) front_Waist_left,
				(select bad_angle from bs_romstandard where ROMSTANDARD_SQ=7) front_Hip_right,
				(select bad_angle from bs_romstandard where ROMSTANDARD_SQ=8) front_Hip_left,
				(select bad_angle from bs_romstandard where ROMSTANDARD_SQ=9) side_Neck_front,
				(select bad_angle from bs_romstandard where ROMSTANDARD_SQ=10) side_Neck_back,
				(select bad_angle from bs_romstandard where ROMSTANDARD_SQ=11) side_ShoulderL_front,
				(select bad_angle from bs_romstandard where ROMSTANDARD_SQ=12) side_ShoulderL_back,
				(select bad_angle from bs_romstandard where ROMSTANDARD_SQ=13) side_ShoulderR_front,
				(select bad_angle from bs_romstandard where ROMSTANDARD_SQ=14) side_ShoulderR_back,
				(select bad_angle from bs_romstandard where ROMSTANDARD_SQ=15) side_Waist_front,
				(select bad_angle from bs_romstandard where ROMSTANDARD_SQ=16) side_Waist_back,
				(select bad_angle from bs_romstandard where ROMSTANDARD_SQ=17) side_HipL_front,
				(select bad_angle from bs_romstandard where ROMSTANDARD_SQ=18) side_HipL_back,
				(select bad_angle from bs_romstandard where ROMSTANDARD_SQ=19) side_HipR_front,
				(select bad_angle from bs_romstandard where ROMSTANDARD_SQ=20) side_HipR_back
		");
		$database->bind(':USER_SQ', $MEMBER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$romstandard = json_encode($rows);
		
		// 사용자 이용권 리스트 
		$database->prepare("
			SELECT UV_SQ,MEMBER_SQ,VOUCHER_SQ,VOUCHER_NAME,VOUCHER_TYPE,b.DESCRIPTION as VOUCHER_TYPE_NAME,USE_TYPE,c.DESCRIPTION as USE_TYPE_NAME,
					PERIOD_TYPE,d.DESCRIPTION as PERIOD_TYPE_NAME,PERIOD,PERIOD_UNIT,f.DESCRIPTION as PERIOD_UNIT_NAME,
					COUNT_TYPE,e.DESCRIPTION as COUNT_TYPE_NAME,COUNT,ENTERLIMIT_DAY,ENTERLIMIT_WEEK,USEDCOUNT,
					(SELECT COUNT(*) FROM tb_reservation WHERE USER_SQ=a.MEMBER_SQ and UV_SQ=a.UV_SQ and RESERV_STATUS=1) RESERV_COUNT,
					USE_STATUS,USE_STARTDATE,USE_LASTDATE,SELLER_SQ, g.USER_NM as SELLER_NM,TRAINER_SQ, h.USER_NM as TRAINER_NM, h.PHONE_NO
 			FROM tb_user_voucher a
				  left outer join tb_common b on a.VOUCHER_TYPE=b.CODE and b.BASE_CD='CD004'
				  left outer join tb_common c on a.USE_TYPE=c.CODE and c.BASE_CD='CD005'
				  left outer join tb_common d on a.PERIOD_TYPE=d.CODE and d.BASE_CD='CD006'
				  left outer join tb_common e on a.COUNT_TYPE=e.CODE and e.BASE_CD='CD007'
				  left outer join tb_common f on a.PERIOD_UNIT=f.CODE and f.BASE_CD='CD010'
				  left outer join tb_user g on g.USER_SQ=a.SELLER_SQ
				  left outer join tb_user h on h.USER_SQ=a.TRAINER_SQ
			WHERE MEMBER_SQ=:MEMBER_SQ
			ORDER BY UV_SQ
		");
		$database->bind(':MEMBER_SQ', $MEMBER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$uservoucherlist = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($memberinfo.'|'.$inbodylist.'|'.$poselist.'|'.$romlist.'|'.$healthlist.'|'.$inbodydata.'|'.$posedata.'|'.$romdata.'|'.$healthdata.'|'.$posestandard.'|'.$romstandard.'|'.$posepicture.'|'.$uservoucherlist);
		break;
	
	case 'getPoseData': // 사용자 리스트 취득 
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		// 파라메터 취득
		$MEASUREMENT_SQ = getAnyParameter("MEASUREMENT_SQ","");
		$CENTER_SQ = $session->user["CENTER_SQ"];

		// 기본값 설정
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$DEVICE_SQ = -1;
		$CATEGORY = "건강정보";
		$SUBCATEGORY = "자세측정정";
		$ACTION = $USERID . " 회원 자세측정정보를 조회하였습니다.";
		$IP = getClientIPv4();
		
		// 신체정보 리스트
		$database->prepare("
			select MEASUREMENT_SQ,USER_SQ,DEVICE_SQ,MEASUREMENT_TYPE,DONE,REG_DT
					from bs_measurement where DONE=1 AND MEASUREMENT_TYPE='INBODY' AND USER_SQ=:user_sq order by REG_DT DESC
		");
		$database->bind(':USER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$inbodylist = json_encode($rows);
		
		// POSE 정보 리스트
		$database->prepare("
			select MEASUREMENT_SQ,USER_SQ,DEVICE_SQ,MEASUREMENT_TYPE,DONE,REG_DT
					from bs_measurement where DONE=1 AND MEASUREMENT_TYPE='Pose' AND USER_SQ=:user_sq order by REG_DT DESC
		");
		$database->bind(':USER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$inbodylist = json_encode($rows);
		
		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($inbodylist.'|'.$inbodylist);
		break;

	case 'getUserList': // 사용자 리스트 취득 
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		// 파라메터 취득
		$CENTER_SQ = $session->user["CENTER_SQ"];

		// 기본값 설정
		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "멤버리스트";
		$SUBCATEGORY = "";
		$ACTION = "사용자 리스트를 조회하였습니다.";
		$IP = getClientIPv4();
		
		// DB 조회
		$database->prepare("
				Select c.USER_SQ, c.CENTER_SQ,c.USER_NM,c.PHONE_NO,c.EMAIL,c.BIRTH_DT, c.REG_DT, 
							CASE WHEN c.UV_SQ is null then 0 else c.UV_SQ end UV_SQ, 
							d.VOUCHER_NAME, d.USE_LASTDATE, d.COUNT - d.USEDCOUNT as REMAINCOUNT,
							CASE WHEN c.MEAS_DATE is null then '' else c.MEAS_DATE end MEAS_DATE, 
							CASE WHEN c.POSE_DT is null then '' else c.POSE_DT end POSE_DT, 
							CASE WHEN c.ROM_DT is null then '' else c.ROM_DT end ROM_DT,
							CASE WHEN c.TRAINER_NM is null then '' else c.TRAINER_NM end TRAINER_NM,
							c.TRAINER, c.ISUSE
				FROM	(select a.USER_SQ,a.CENTER_SQ,a.USER_NM,a.PHONE_NO,a.EMAIL,a.BIRTH_DT, a.REG_DT, 
											(SELECT MAX(UV_SQ) FROM tb_user_voucher where MEMBER_SQ=a.USER_SQ ) as UV_SQ,
											(SELECT MAX(REG_DT) FROM bs_measurement where USER_SQ=a.USER_SQ ) as MEAS_DATE,
											(SELECT MAX(REG_DT) FROM bs_measurement where USER_SQ=a.USER_SQ and MEASUREMENT_TYPE='Pose') as POSE_DT,
											(SELECT MAX(REG_DT) FROM bs_measurement where USER_SQ=a.USER_SQ and MEASUREMENT_TYPE='ROM') as ROM_DT,
											a.TRAINER, b.USER_NM TRAINER_NM, a.ISUSE
						from tb_user a  
								left outer join tb_user b on a.TRAINER=b.USER_SQ
						where a.CENTER_SQ=:CENTER_SQ and a.GRADE=1) c 
				left outer join tb_user_voucher d on c.uv_sq=d.uv_sq 
				order by c.REG_DT desc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$memberlist = json_encode($rows);

		$database->prepare("
			select USER_SQ,CENTER_SQ,USER_NM,PHONE_NO,EMAIL,REG_DT, (select REG_DT from bs_measurement where USER_SQ=tb_user.USER_SQ order by REG_DT desc LIMIT 1) as MEAS_DATE
					from tb_user where CENTER_SQ=:CENTER_SQ and GRADE>1 and ISUSE=1 order by USER_NM desc
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->execute();

		$rows = $database->fetchAll();
		$trainerlist = json_encode($rows);

		// 로그 저장
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		exit($memberlist.'|'.$trainerlist);
		break;

	case 'staff_login': // 직원 로그인
		
		// 파라메터 취득
		$LOGINTYPE = getAnyParameter("LOGINTYPE",0);
		$USERID = getAnyParameter("uID",0);
		$PASSWORD = getAnyParameter("uPW",0);
		$PWD_ENCRYPTED = getAnyParameter("pwd_encrypted",0);

		// 기본값 설정
		$USER_SQ = 0;
		$CATEGORY = "";
		$SUBCATEGORY = "";
		$ACTION = "";
		$DEVICE_SQ = -1;
		
		// DB 조회
		if ($LOGINTYPE == 0) {
			$database->prepare("
				SELECT USER_SQ, a.CENTER_SQ, USERID, USER_NM, c.NAME as GRADENM, BIRTH_DT, PHONE_NO, EMAIL, USERHEIGHT, USERWEIGHT, GRADE, b.CENTER_NM 
				FROM tb_user a left outer join tb_center b on a.CENTER_SQ=b.CENTER_SQ left outer join tb_common c on a.GRADE=c.CODE and c.BASE_CD='CD001'
				WHERE USERID = :USERID AND PWD_ENCRYPTED = :PWD_ENCRYPTED AND (a.ISUSE=1) AND a.GRADE>1 AND ISMANAGER=1
			");
		} else {
			$database->prepare("
				SELECT USER_SQ, a.CENTER_SQ, USERID, USER_NM, c.NAME as GRADENM, BIRTH_DT, PHONE_NO, EMAIL, USERHEIGHT, USERWEIGHT, GRADE, b.CENTER_NM 
				FROM tb_user a left outer join tb_center b on a.CENTER_SQ=b.CENTER_SQ left outer join tb_common c on a.GRADE=c.CODE and c.BASE_CD='CD001'
				WHERE USERID = :USERID AND PWD_ENCRYPTED = :PWD_ENCRYPTED AND (a.ISUSE=1) AND ISMANAGER<>1 
			");
		}
		$database->bind(':USERID', $USERID);
		$database->bind(':PWD_ENCRYPTED', $PWD_ENCRYPTED);
		$database->execute();
		
		// 결과 처리
		$CATEGORY = "직원 로그인";
		$IP = getClientIPv4();

		$row = $database->fetch();
		if (!$row) {
			$redirect_location = 'login.php?msg=loginFailure';
			$SUBCATEGORY = "로그인 실패";
			$ACTION = "$IP PC에서 $USERID 사용자가 로그인에 실패하였습니다.";
		} else {
			if ($row["GRADE"]=='')
				$row["GRADE"]=='1';
			$SUBCATEGORY = "로그인 성공";
			$ACTION = "$IP PC에서 $USERID 사용자가 로그인에 성공하였습니다.";
			$USER_SQ = $row["USER_SQ"];
			$session->user = $row;
			$redirect_location = 'index.php'; // 로그인 이후 이동화면
		}
		// 로그 저장
		//insert_Log_Person($USER_SQ,$USERID, $IP, $DEVICE_SQ, $ACTION,$database);
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);
		error_log("USERID = ".$USERID); //,3,"C:/xampp/debuggingLog/debug.log");
		error_log("PASSWORD = ".$PWD_ENCRYPTED); //,3,"C:/xampp/debuggingLog/debug.log");
		break;
	
	case 'user_login': // 로그인
		// 파라메터 취득
		$USERID = getAnyParameter("uID",0);
		$PASSWORD = getAnyParameter("uPW",0);
		$PWD_ENCRYPTED = getAnyParameter("pwd_encrypted",0);

		// 기본값 설정
		$USER_SQ = 0;
		$CATEGORY = "";
		$SUBCATEGORY = "";
		$ACTION = "";
		$DEVICE_SQ = -1;
		
		// DB 조회
		$database->prepare("
			SELECT USER_SQ, a.CENTER_SQ, USERID, USER_NM, c.NAME as GRADENM, BIRTH_DT, PHONE_NO, EMAIL, USERHEIGHT, USERWEIGHT, GRADE, b.CENTER_NM 
			FROM tb_user a left outer join tb_center b on a.CENTER_SQ=b.CENTER_SQ left outer join tb_common c on a.GRADE=c.CODE and c.BASE_CD='CD001'
			WHERE USERID = :USERID AND PWD_ENCRYPTED = :PWD_ENCRYPTED AND (ISUSE=1)
		");
		$database->bind(':USERID', $USERID);
		$database->bind(':PWD_ENCRYPTED', $PWD_ENCRYPTED);
		$database->execute();
		
		// 결과 처리
		$CATEGORY = "회원로그인";
		$IP = getClientIPv4();

		$row = $database->fetch();
		if (!$row) {
			$redirect_location = 'login.php?msg=loginFailure';
			$SUBCATEGORY = "로그인 실패";
			$ACTION = "$IP PC에서 $USERID 사용자가 로그인에 실패하였습니다.";
		} else {
			if ($row["GRADE"]=='')
				$row["GRADE"]=='1';
			$SUBCATEGORY = "로그인 성공";
			$ACTION = "$IP PC에서 $USERID 사용자가 로그인에 성공하였습니다.";
			$USER_SQ = $row["USER_SQ"];
			$session->user = $row;
			$redirect_location = 'index.php'; // 로그인 이후 이동화면
		}
		// 로그 저장
		//insert_Log_Person($USER_SQ,$USERID, $IP, $DEVICE_SQ, $ACTION,$database);
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);
		//error_log("USERID = ".$USERID); //,3,"C:/xampp/debuggingLog/debug.log");
		//error_log("PASSWORD = ".$PASSWORD); //,3,"C:/xampp/debuggingLog/debug.log");
		break;

	case 'user_logout': // 로그아웃

		$USER_SQ = $session->user["USER_SQ"];
		$USERID = $session->user["USERID"];
		$DEVICE_SQ = -1;
		$CATEGORY = "로그아웃";
		$SUBCATEGORY = "";
		$ACTION = $session->user["USER_NM"] + " 사용자가 로그아웃하였습니다.";
		$IP = getClientIPv4();
		insert_Log_PersonNew($USER_SQ,$USERID, $IP, $DEVICE_SQ, $CATEGORY, $SUBCATEGORY, $ACTION,$database);

		$session->destroy();
		$redirect_location = 'login.php'; // 로그아웃 이후 이동화면
		break;

		// OLD
	case 'user_register': // 사용자 등록 
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_FILES = ' . var_export($_FILES, 1));
		error_log('$_POST = ' . var_export($_POST, 1));
		$USER_SQ = $session->user["USER_SQ"];
		$CENTER_SQ = $session->user["CENTER_SQ"];

		$userid = getAnyParameter("userid", "");
		$pwd_encrypted = getAnyParameter("pwd_encrypted", "");
		$username = getAnyParameter("username", "");
		$birth = getAnyParameter("birth", "");
		$sex = getAnyParameter("sex", "");
		$tel = getAnyParameter("tel", "");
		$email = getAnyParameter("email", "");
		$person = getAnyParameter("person", "");
		$address = getAnyParameter("address", "");
		$comment = getAnyParameter("comment", "");
		$file_name["name"] = "";
			
		// File Manipulation
		if (isset($_FILES["myFileUp"]))
		{
			$upload_dir = "uploadfiles/";
			$upload_fileheader = $upload_dir."img_".date("YmdHis")."_";

			$upload_file = $upload_fileheader.str_replace(" ", "_", $_FILES["myFileUp"]["name"]);
			$filename=iconv("utf-8","CP949",$upload_file);
			$type = $_FILES["myFileUp"]["type"];
			$arr = explode('/',$type);

			if ($arr[0] == "image" && move_uploaded_file($_FILES["myFileUp"]["tmp_name"], $filename))
			{
				$file_name["name"] = $upload_file;
			}
		}
		$database->prepare("INSERT INTO tb_user (CENTER_SQ, USERID, PWD_ENCRYPTED, USER_NM, BIRTH_DT, GENDER, PHONE_NO, EMAIL, TRAINER, ADDRESS, COMMENT, GRADE, USERIMAGE, ISUSE, REG_DT ) 
					VALUES (:CENTER_SQ, :userid, :pwd_encrypted, :username, :birth, :sex, :tel, :email, :person, :address, :comment, 1, :filename, 1, now() ) 
					");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':userid', $userid);
		$database->bind(':pwd_encrypted', $pwd_encrypted);
		$database->bind(':username', $username);
		$database->bind(':birth', $birth);
		$database->bind(':sex', $sex);
		$database->bind(':tel', $tel);
		$database->bind(':email', $email);
		$database->bind(':person', $person);
		$database->bind(':address', $address);
		$database->bind(':comment', $comment);
		$database->bind(':filename', $file_name["name"]);
		
		$database->execute();
		if ($database->rowCount() > 0) {
			$redirect_location = 'index.php?member_register=success'; // 로그아웃 이후 이동화면
		}
		else {
			$redirect_location = 'index.php?member_register=fail'; // 로그아웃 이후 이동화면
		}
		
		$USER_SQ = $session->user["USER_SQ"];
		$DEVICE_SQ = -1;
		$ACTION = "사용자 신규 등록";
		$IP = getClientIPv4();
		insert_Log_Person($USER_SQ,$userid, $IP, $DEVICE_SQ, $ACTION,$database);
		
		break;

	case 'UserActionReport': // ajax - select optionbox 를 채운다.
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_POST = ' . var_export($_POST, 1));
		
		$sdate = getAnyParameter("dpuserreportSDate",0);
		$edate = getAnyParameter("dpuserreportEDate",0);
		$userid = getAnyParameter("userreportID",0);

		$database->prepare("	select @ROWNUM := @ROWNUM + 1 AS no, date_format(a.regdt, '%Y-%m-%d %H:%i:%s') regdt  
									,a.ip ,a.t_ip ,a.note ,a.regid 
									,(select name from tb_jang where ip = a.IP) name 
									,(select name from tb_person where id = a.id) person 
									,(select name from tb_jang where ip = a.t_IP) t_name 
									,(select name from tb_person where id = a.regid) reg_person 
								from tb_log_person a , (SELECT @ROWNUM := 0) R 
								where (:regid1='' or a.regid = :regid2) 
									and (:sdate1='' or date_format(a.regdt, '%Y-%m-%d') between :sdate2 and :edate)
								order by a.regdt asc, a.regid asc 
						");
		$database->bind(':regid1', $userid);
		$database->bind(':regid2', $userid);
		$database->bind(':sdate1', $sdate);
		$database->bind(':sdate2', $sdate);
		$database->bind(':edate', $edate);
		$database->execute();
		$rows = $database->fetchAll();

		require_once './lib/PHPExcel.php';
		require_once './lib/PHPExcel/IOFactory.php';

	
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()
			->setCreator("5훈비")
			->setLastModifiedBy("5훈비")
			->setTitle("사용자 이력 보고서")
			->setSubject("사용자 이력 보고서")
			->setDescription("주기장관제시스템")
			->setKeywords("사용자 이력 보고서")
			->setCategory("사용자 이력 보고서");
		// 타이틀 
		$ind = 1;
		$sheet = $objPHPExcel->setActiveSheetIndex(0);
		$sheet->setTitle( '사용자' );
		$sheet->setCellValue("A$ind", "사용자 이력 보고서");
		$sheet->getStyle("A$ind")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->mergeCells("A$ind:F$ind");
		$sheet->getStyle("A$ind")->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
		$sheet->getStyle("A$ind")->getFont()->setSize(16);
		// 출력자, 출력일. 
		$ind++;
		$sheet->setCellValue("A$ind", "출력자");
		$sheet->getStyle("A$ind")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->getStyle("A$ind")->applyFromArray(getExcelFillArray('b2beb5'));
		$sheet->setCellValue("B$ind", $session->user['name']);
		$sheet->getStyle("B$ind")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$sheet->mergeCells("B$ind:I$ind");
		$ind++;
		$sheet->setCellValue("A$ind", "출력일");
		$sheet->getStyle("A$ind")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->getStyle("A$ind")->applyFromArray(getExcelFillArray('b2beb5'));
		$sheet->setCellValue("B$ind", date("Y/m/d"));
		$sheet->getStyle("B$ind")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$sheet->mergeCells("B$ind:I$ind");

		$ind++;$ind++;
		$sheet->setCellValue("A$ind", "번호");
		$sheet->setCellValue("B$ind", "사용PC");
		$sheet->setCellValue("C$ind", "사용자");
		$sheet->setCellValue("D$ind", "설정장비");
		$sheet->setCellValue("E$ind", "내용");
		$sheet->setCellValue("F$ind", "수정일자");
		$sheet->getStyle("A$ind:F$ind")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->getStyle("A$ind:F$ind")->applyFromArray(getExcelFillArray('b2beb5'));
		
		//$objPHPExcel->getActiveSheet()->getStyle( 'A4:E10')->applyFromArray(getExcelFontArray())
		//function getExcelFontArray($size, $bold, $underline, $color)
		//function getExcelFillArray($color)
		//function getExcelBorderArray($color)
		$ind++;
		foreach ($rows as $row)
		{
			$sheet->setCellValue("A$ind", $row["no"]);
			$sheet->getStyle("A$ind")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->setCellValue("B$ind", $row["name"]);
			$sheet->setCellValue("C$ind", $row["regid"]);
			$sheet->setCellValue("D$ind", $row["t_name"]);
			$sheet->setCellValue("E$ind", $row["note"]);
			$sheet->setCellValue("F$ind", $row["regdt"]);
			
			$sheet->getStyle("F$ind")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$ind++;
		}
		$sheet->getColumnDimension('A')->setWidth(10);
		$sheet->getColumnDimension('B')->setWidth(25);
		$sheet->getColumnDimension('C')->setWidth(12);
		$sheet->getColumnDimension('D')->setWidth(18);
		$sheet->getColumnDimension('E')->setWidth(40);
		$sheet->getColumnDimension('F')->setAutoSize(true); 
		
		//$objPHPExcel->setActiveSheetIndex(0);
		//$sheet->setSelectedCellByColumnAndRow(0, 1);

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

		header("Pragma:public");
		header('Content-Description: File Transfer');
		//header("Content-Type: application/octet-stream");
		header("Content-Type: application/vnd.ms-excel;");
		if (preg_match("/MSIE/i", getenv("HTTP_USER_AGENT")))
		{
			header('Content-Disposition: filename=사용자이력보고서.xls');// 파일이름 설정
		} else 
		{
			header('Content-Disposition: filename=사용자이력보고서.xls');// 파일이름 설정
		}
		ob_clean();
		flush();
		//header('Content-Length: ' . filesize('result.xls'));
		//header('Cache-Control: max-age=0');
		//readfile('result.xls');
		$objWriter->save('php://output');

		
		$regid = $session->user["id"];
		$ip = getClientIPv4();
		$t_ip = "";
		insert_Log_Person($regid, $ip, $t_ip, "사용자 이력 출력", $database);
		exit;
		break;

	case 'EquipListReport': // ajax - select optionbox 를 채운다.
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_POST = ' . var_export($_POST, 1));
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		
		$gucd = getAnyParameter("EquipListReport_GUCD",0);

		$database->prepare("	select @ROWNUM := @ROWNUM + 1 AS NO, seq, gucd, pow, icon, connect, fin, useyn
								,(select name from tb_code where gucd = 'J1' and cdcd = a.gucd) as gunm
								, ip, id, name, p_x, p_y, dist, t_r, t_rp, note
								, pow, connect, fin, useyn, ca_hei, d_t, d_st, d_et, cell, tilt
								,if(pow = '1', 'ON', 'OFF') as pow_st
								,if(connect = '1', '정상', '장애') as connect_st
								,if(fin = '1', '탐지', '미탐지') as fin_st
								,if(useyn = 'Y', '사용', '미사용') as usenm
								,case when d_t = '1' or d_t = '2' then '감지' else '배제' end as d_tnm 
								,date_format(a.updt, '%Y-%m-%d %H:%i:%s') updt
								from tb_jang a, (SELECT @ROWNUM := 0) R
								where (:eventequip = 0 or a.gucd = :eventequip1) 
								order by gucd asc, name asc
						");
		$database->bind(':eventequip', $gucd);
		$database->bind(':eventequip1', $gucd);
		$database->execute();
		$rows = $database->fetchAll();

		require_once './lib/PHPExcel.php';
		require_once './lib/PHPExcel/IOFactory.php';
	
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()
			->setCreator("5훈비")
			->setLastModifiedBy("5훈비")
			->setTitle("장비 관리 내역 보고서")
			->setSubject("장비 관리 내역 보고서")
			->setDescription("주기장관제시스템")
			->setKeywords("장비 관리 내역 보고서")
			->setCategory("장비 관리 내역 보고서");
		// 타이틀 
		$ind = 1;
		$sheet = $objPHPExcel->setActiveSheetIndex(0);
		$sheet->setTitle( '장비' );
		$sheet->setCellValue("A$ind", "장비 관리 내역 보고서");
		$sheet->getStyle("A$ind")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->mergeCells("A$ind:I$ind");
		$sheet->getStyle("A$ind")->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
		$sheet->getStyle("A$ind")->getFont()->setSize(16);
		// 출력자, 출력일. 
		$ind++;
		$sheet->setCellValue("A$ind", "출력자");
		$sheet->getStyle("A$ind")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->getStyle("A$ind")->applyFromArray(getExcelFillArray('b2beb5'));
		$sheet->setCellValue("B$ind", $session->user['name']);
		$sheet->getStyle("B$ind")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$sheet->mergeCells("B$ind:I$ind");
		$ind++;
		$sheet->setCellValue("A$ind", "출력일");
		$sheet->getStyle("A$ind")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->getStyle("A$ind")->applyFromArray(getExcelFillArray('b2beb5'));
		$sheet->setCellValue("B$ind", date("Y/m/d"));
		$sheet->getStyle("B$ind")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$sheet->mergeCells("B$ind:I$ind");

		$ind++;$ind++;
		$sheet->setCellValue("A$ind", "번호");
		$sheet->setCellValue("B$ind", "IP");
		$sheet->setCellValue("C$ind", "감시지역");
		$sheet->setCellValue("D$ind", "장비");
		$sheet->setCellValue("E$ind", "장비명");
		$sheet->setCellValue("F$ind", "설치좌표");
		$sheet->setCellValue("G$ind", "수정일자");
		$sheet->setCellValue("H$ind", "전원");
		$sheet->setCellValue("I$ind", "연결상태");
		$sheet->getStyle("A$ind:I$ind")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->getStyle("A$ind:I$ind")->applyFromArray(getExcelFillArray('b2beb5'));
		
		//$objPHPExcel->getActiveSheet()->getStyle( 'A4:E10')->applyFromArray(getExcelFontArray())
		//function getExcelFontArray($size, $bold, $underline, $color)
		//function getExcelFillArray($color)
		//function getExcelBorderArray($color)
		$ind++;
		foreach ($rows as $row)
		{
			$sheet->setCellValue("A$ind", $row["NO"]);
			$sheet->getStyle("A$ind")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->setCellValue("B$ind", $row["ip"]);
			$sheet->setCellValue("C$ind", $row["note"]);
			$sheet->setCellValue("D$ind", $row["gunm"]);
			$sheet->setCellValue("E$ind", $row["name"]);
			if ($row["p_x"]=="")
				$sheet->setCellValue("F$ind", "");
			else {
				$sheet->setCellValue("F$ind", $row["p_x"].", ".$row["p_y"]);
			}
			$sheet->getStyle("F$ind")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->setCellValue("G$ind", $row["updt"]);
			$sheet->getStyle("G$ind")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->setCellValue("H$ind", $row["pow_st"]);
			$sheet->getStyle("H$ind")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			if ($row["connect"]=="1")
				$sheet->setCellValue("I$ind", "정상");
			else if ($row["connect"]=="2")
				$sheet->setCellValue("I$ind", "장애");
			else
				$sheet->setCellValue("I$ind", "");
			$sheet->getStyle("I$ind")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$ind++;
		}
		$sheet->getColumnDimension('A')->setWidth(10);
		$sheet->getColumnDimension('B')->setAutoSize(true); 

		//$sheet->getColumnDimension('C')->setAutoSize(true); 
		//$sheet->calculateColumnWidths();
		//$calculatedWidth = round( $sheet->getColumnDimensionByColumn('C')->getWidth(), 0 );
		//error_log('$calculatedWidth = ' . $calculatedWidth);
		//$sheet->getColumnDimension('C')->setAutoSize(false); 
		//$calculatedWidth2 = round( $sheet->getColumnDimensionByColumn('C')->getWidth(), 0 );
		//error_log('calculatedWidth2 = ' . $calculatedWidth2);
		//if( $calculatedWidth < 9 ) {
		//	$sheet->getColumnDimensionByColumn('C')->setWidth( round(1.8*$calculatedWidth, 0) );
		//}
		$sheet->getColumnDimension('C')->setWidth(48);
		$sheet->getColumnDimension('D')->setWidth(15);
		$sheet->getColumnDimension('E')->setWidth(19);
		$sheet->getColumnDimension('F')->setAutoSize(true); 
		$sheet->getColumnDimension('G')->setAutoSize(true); 
		$sheet->getColumnDimension('H')->setWidth(imap_utf7_encode());
		$sheet->getColumnDimension('I')->setWidth(13);
		
		//$objPHPExcel->setActiveSheetIndex(0);
		//$sheet->setSelectedCellByColumnAndRow(0, 1);

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

		header("Pragma:public");
		header('Content-Description: File Transfer');
		//header("Content-Type: application/octet-stream");
		header("Content-Type: application/vnd.ms-excel;");
		if (preg_match("/MSIE/i", getenv("HTTP_USER_AGENT")))
		{
			header('Content-Disposition: filename=장비관리내역보고서.xls');// 파일이름 설정
		} else 
		{
			header('Content-Disposition: filename=장비관리내역보고서.xls');// 파일이름 설정
		}
		ob_clean();
		flush();
		//header('Content-Length: ' . filesize('result.xls'));
		//header('Cache-Control: max-age=0');
		//readfile('result.xls');
		$objWriter->save('php://output');

		$regid = $session->user["id"];
		$ip = getClientIPv4();
		$t_ip = "";
		insert_Log_Person($regid, $ip, $t_ip, "시스템 장비내역 출력", $database);
		exit;
		break;

	case 'EventReport': // ajax - select optionbox 를 채운다.
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_POST = ' . var_export($_POST, 1));
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		
		$sdate = getAnyParameter("dpeventreportSDate",0);
		$edate = getAnyParameter("dpeventreportEDate",0);
		$eventequip = getAnyParameter("eventreportequip",0);
		$eventtype = getAnyParameter("eventreporttype",0);

		$database->prepare("	select a.* 
									,if(a.gucd = '1' ,(SELECT p_x FROM  tb_log_fin_pt c WHERE a.ip = c.ip AND a.gro = c.GRO ORDER BY NO ASC LIMIT 1), '') s_p_x
									,if(a.gucd = '1' ,(SELECT p_y FROM  tb_log_fin_pt c WHERE a.ip = c.ip AND a.gro = c.GRO order BY NO ASC LIMIT 1), '') s_p_y 
									,if(a.gucd = '1' ,(SELECT p_x FROM  tb_log_fin_pt c WHERE a.ip = c.ip AND a.gro = c.GRO ORDER BY NO DESC LIMIT 1), '') e_p_x 
									,if(a.gucd = '1' ,(SELECT p_y FROM  tb_log_fin_pt c WHERE a.ip = c.ip AND a.gro = c.GRO order BY NO DESC LIMIT 1), '') e_p_y 
									,if(a.gucd = '1' ,(SELECT speed FROM  tb_log_fin_pt c WHERE a.ip = c.ip AND a.gro = c.GRO order BY NO DESC LIMIT 1), '') speed 
								from ( select @ROWNUM := @ROWNUM + 1 AS NO ,a.seq ,date_format(a.regdt, '%Y-%m-%d %H:%i:%s') regdt ,date_format(a.updt, '%Y-%m-%d %H:%i:%s') updt 
										,b.name as na2 ,b.note as na3 ,a.ip ,a.id ,a.gro ,a.note ,a.result ,(select name from tb_code where gucd = 'R1' and cdcd = a.result) as resultnm 
										,a.upid ,(SELECT NAME FROM tb_person WHERE a.upid = id) upnm ,(SELECT NAME FROM tb_code WHERE gucd = 'J1' and cdcd = b.gucd) as na1 ,b.gucd 
									from tb_log_fin a, tb_jang b , (SELECT @ROWNUM := 0) R 
									where a.useyn = 'Y' and a.ip = b.ip  and ((:sdate = '' or :edate = '') or date_format(a.regdt, '%Y-%m-%d') between :sdate1 and :edate1)
										and (:eventequip = 0 or b.gucd = :eventequip1) 
										and (:eventtype = 2 or a.result = :eventtype1)
									order by a.regdt asc )as a 
						");
		$database->bind(':sdate', $sdate);
		$database->bind(':edate', $edate);
		$database->bind(':sdate1', $sdate);
		$database->bind(':edate1', $edate);
		$database->bind(':eventequip', $eventequip);
		$database->bind(':eventequip1', $eventequip);
		$database->bind(':eventtype', $eventtype);
		$database->bind(':eventtype1', $eventtype);
		$database->execute();
		$rows = $database->fetchAll();

		require_once './lib/PHPExcel.php';
		require_once './lib/PHPExcel/IOFactory.php';

		$ind = 1;
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()
			->setCreator("5훈비")
			->setLastModifiedBy("5훈비")
			->setTitle("경보상황 보고서")
			->setSubject("경보상황 보고서")
			->setDescription("주기장관제시스템")
			->setKeywords("경보상황 보고서")
			->setCategory("경보상황 보고서");
		// 타이틀 
		$sheet = $objPHPExcel->setActiveSheetIndex(0);
		$sheet->setTitle( '이벤트' );
		$sheet->setCellValue("A$ind", "경보상황 일일 보고서");
		$sheet->getStyle("A$ind")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->mergeCells("A$ind:K$ind");
		$sheet->getStyle("A$ind")->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
		$sheet->getStyle("A$ind")->getFont()->setSize(16);
		// 출력자, 출력일. 
		$ind++;
		$sheet->setCellValue("A$ind", "출력자");
		$sheet->getStyle("A$ind")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->getStyle("A$ind")->applyFromArray(getExcelFillArray('b2beb5'));
		$sheet->setCellValue("B$ind", $session->user['name']);
		$sheet->getStyle("B$ind")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$sheet->mergeCells("B$ind:K$ind");
		$ind++;
		$sheet->setCellValue("A$ind", "출력일");
		$sheet->getStyle("A$ind")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->getStyle("A$ind")->applyFromArray(getExcelFillArray('b2beb5'));
		$sheet->setCellValue("B$ind", date("Y/m/d"));
		$sheet->getStyle("B$ind")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$sheet->mergeCells("B$ind:K$ind");
		$ind++;
		$sheet->setCellValue("A$ind", "기간");
		$sheet->getStyle("A$ind")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->getStyle("A$ind")->applyFromArray(getExcelFillArray('b2beb5'));
		$sheet->setCellValue("B$ind", "$sdate~$edate");
		$sheet->getStyle("B$ind")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$sheet->mergeCells("B$ind:K$ind");

		$ind++;$ind++;
		$sheet->setCellValue("A$ind", "번호");
		$sheet->setCellValue("B$ind", "발생일시");
		$sheet->setCellValue("C$ind", "조치일시");
		$sheet->setCellValue("D$ind", "감지지역");
		$sheet->setCellValue("E$ind", "장비");
		$sheet->setCellValue("F$ind", "장비명");
		$sheet->setCellValue("G$ind", "감시좌표");
		$sheet->setCellValue("H$ind", "속도");
		$sheet->setCellValue("I$ind", "조치내용");
		$sheet->setCellValue("J$ind", "처리");
		$sheet->setCellValue("K$ind", "담당자");
		$sheet->getStyle("A$ind:K$ind")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->getStyle("A$ind:K$ind")->applyFromArray(getExcelFillArray('b2beb5'));
		
		//$objPHPExcel->getActiveSheet()->getStyle( 'A4:E10')->applyFromArray(getExcelFontArray())
		//function getExcelFontArray($size, $bold, $underline, $color)
		//function getExcelFillArray($color)
		//function getExcelBorderArray($color)
		$ind++;
		foreach ($rows as $row)
		{
			$sheet->setCellValue("A$ind", $row["NO"]);
			$sheet->setCellValue("B$ind", $row["regdt"]);
			$sheet->setCellValue("C$ind", $row["updt"]);
			$sheet->getStyle("A$ind:C$ind")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->setCellValue("D$ind", $row["na3"]);
			$sheet->setCellValue("E$ind", $row["na1"]);
			$sheet->setCellValue("F$ind", $row["na2"]);
			if ($row["s_p_x"]=="")
				$sheet->setCellValue("G$ind", "");
			else {
				$sheet->setCellValue("G$ind", $row["s_p_x"].", ".$row["s_p_y"]."  ->  ".$row["e_p_x"].", ".$row["e_p_y"]);
			}
			$sheet->getStyle("G$ind")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->setCellValue("H$ind", $row["speed"]);
			$sheet->setCellValue("I$ind", $row["note"]);
			$sheet->setCellValue("J$ind", $row["resultnm"]);
			$sheet->getStyle("J$ind")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->setCellValue("K$ind", $row["upid"]);
			$ind++;
		}
		$sheet->getColumnDimension('A')->setWidth(10);
		$sheet->getColumnDimension('B')->setAutoSize(true); 
		$sheet->getColumnDimension('C')->setAutoSize(true); 
		$sheet->getColumnDimension('D')->setWidth(48);
		$sheet->getColumnDimension('E')->setWidth(25);
		$sheet->getColumnDimension('F')->setWidth(18);
		$sheet->getColumnDimension('G')->setAutoSize(true); 
		$sheet->getColumnDimension('H')->setWidth(7);
		$sheet->getColumnDimension('I')->setWidth(20);
		$sheet->getColumnDimension('J')->setWidth(12);
		$sheet->getColumnDimension('K')->setWidth(11);
		
		//$objPHPExcel->setActiveSheetIndex(0);
		//$sheet->setSelectedCellByColumnAndRow(0, 1);

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

		header("Pragma:public");
		header('Content-Description: File Transfer');
		//header("Content-Type: application/octet-stream");
		header("Content-Type: application/vnd.ms-excel;");
		if (preg_match("/MSIE/i", getenv("HTTP_USER_AGENT")))
		{
			header('Content-Disposition: filename=경보상황보고서.xls');// 파일이름 설정
		} else 
		{
			header('Content-Disposition: filename=경보상황보고서.xls');// 파일이름 설정
		}
		ob_clean();
		flush();
		//header('Content-Length: ' . filesize('result.xls'));
		//header('Cache-Control: max-age=0');
		//readfile('result.xls');
		$objWriter->save('php://output');
		
		$regid = $session->user["id"];
		$ip = getClientIPv4();
		$t_ip = "";
		insert_Log_Person($regid, $ip, $t_ip, "경보상황 보고서 출력", $database);
		exit;
		break;
		
	case 'SettingSave': // ajax - select optionbox 를 채운다.
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_POST = ' . var_export($_POST, 1));
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}

		$seq = getAnyParameter("seq","");
		$rimg_r = getAnyParameter("rimg_r","");
		$rimg_fp = getAnyParameter("rimg_fp","");
		$rimg_fl = getAnyParameter("rimg_fl","");
		$rimg_o = getAnyParameter("rimg_o","");
		$rimg_index = getAnyParameter("rimg_index","");
		$rimg_ac = getAnyParameter("rimg_ac","");
		$rimg_unc = getAnyParameter("rimg_unc","");
		$simg_r = getAnyParameter("simg_r","");
		$simg_ac = getAnyParameter("simg_ac","");
		$cimg_r = getAnyParameter("cimg_r","");
		$cimg_o = getAnyParameter("cimg_o","");
		$cimg_ac = getAnyParameter("cimg_ac","");
		$ss_m = getAnyParameter("ss_m","");
		$se_m = getAnyParameter("se_m","");
		$ss_t = getAnyParameter("ss_t","");
		$se_t = getAnyParameter("se_t","");
		$ws_m = getAnyParameter("ws_m","");
		$we_m = getAnyParameter("we_m","");
		$ws_t = getAnyParameter("ws_t","");
		$we_t = getAnyParameter("we_t","");

		error_log("ast : " . $ast);
		$regid = $session->user["id"];
		
		$database->prepare("update tb_pc_set set  
								rimg_r = :rimg_r, rimg_fp = :rimg_fp, rimg_fl = :rimg_fl, rimg_o = :rimg_o,  rimg_index = :rimg_index,  rimg_ac = :rimg_ac, rimg_unc = :rimg_unc, 
								simg_r = :simg_r, simg_ac = :simg_ac, cimg_r = :cimg_r, cimg_o = :cimg_o,  cimg_ac = :cimg_ac, 
								ss_m = :ss_m, se_m = :se_m, ss_t = :ss_t, se_t = :se_t,  ws_m = :ws_m,  we_m = :we_m,  ws_t = :ws_t,  we_t = :we_t, 
								upid = :upid, updt = now() 
							where seq = :seq
						");
		$database->bind(':rimg_r', $rimg_r);
		$database->bind(':rimg_fp', $rimg_fp);
		$database->bind(':rimg_fl', $rimg_fl);
		$database->bind(':rimg_o', $rimg_o);
		$database->bind(':rimg_index', $rimg_index);
		$database->bind(':rimg_ac', $rimg_ac);
		$database->bind(':rimg_unc', $rimg_unc);
		$database->bind(':simg_r', $simg_r);
		$database->bind(':simg_ac', $simg_ac);
		$database->bind(':cimg_r', $cimg_r);
		$database->bind(':cimg_o', $cimg_o);
		$database->bind(':cimg_ac', $cimg_ac);
		$database->bind(':ss_m', $ss_m);
		$database->bind(':se_m', $se_m);
		$database->bind(':ss_t', $ss_t);
		$database->bind(':se_t', $se_t);
		$database->bind(':ws_m', $ws_m);
		$database->bind(':we_m', $we_m);
		$database->bind(':ws_t', $ws_t);
		$database->bind(':we_t', $we_t);
		$database->bind(':upid', $regid);
		$database->bind(':seq', $seq);
		$database->execute();

		error_log($database->rowCount());
		
		if ($database->rowCount() > 0)
			$response_array["result"] = 'success';
		else
			$response_array["result"] = 'fail';

		$ip = getClientIPv4();
		$t_ip = "";
		insert_Log_Person($regid, $ip, $t_ip, "환경 저장", $database);
		error_log(json_encode($response_array));
		exit(json_encode($response_array));
		break;
		
	case 'EquipIconChange': // ajax - select optionbox 를 채운다.
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_POST = ' . var_export($_POST, 1));
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$seq = getAnyParameter("seq","");
		$gucd = getAnyParameter("gucd","");
		$t_ip = getAnyParameter("ip","");
		$icon = getAnyParameter("icon","");

		$regid = $session->user["id"];
		
		$database->prepare("update tb_jang set  
								icon = :icon, 
								upid = :upid, updt = now() 
							where seq = :seq
						");
		$database->bind(':icon', $icon);
		$database->bind(':upid', $regid);
		$database->bind(':seq', $seq);

		$database->execute();
		
		error_log($database->rowCount());
		
		if ($database->rowCount() > 0)
			$response_array["result"] = 'success';
		else
			$response_array["result"] = 'fail';

		$ip = getClientIPv4();
		$t_ip = $t_ip;
		$reason = "";
		if ($gucd=="1")
		{
			$reason = "레이더 아이콘 수정";
		} else if ($gucd=="2")
		{
			$reason = "복합센서 아이콘 수정";
		} else if ($gucd=="3")
		{
			$reason = "카메라 아이콘 수정";
		} else if ($gucd=="4")
		{
			$reason = "경광등 아이콘 수정";
		} else if ($gucd=="5")
		{
			$reason = "서치라이트 아이콘 수정";
		}
		insert_Log_Person($regid, $ip, $t_ip, $reason, $database);
		error_log(json_encode($response_array));
		exit(json_encode($response_array));
		break;
		
	case 'EquipIconAll': // ajax - select optionbox 를 채운다.
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_POST = ' . var_export($_POST, 1));
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$gucd = getAnyParameter("gucd","");
		$icon = getAnyParameter("icon","");

		$regid = $session->user["id"];
		
		$database->prepare("update tb_jang set  
								icon = :icon, 
								upid = :upid, updt = now() 
							where gucd = :gucd
						");
		$database->bind(':icon', $icon);
		$database->bind(':upid', $regid);
		$database->bind(':gucd', $gucd);

		$database->execute();
		
		error_log($gucd);
		error_log($icon);
		error_log($regid);
		error_log($database->rowCount());
		
		if ($database->rowCount() > 0)
			$response_array["result"] = 'success';
		else
			$response_array["result"] = 'fail';

		$ip = getClientIPv4();
		$t_ip = "";
		$reason = "";
		if ($gucd=="1")
		{
			$reason = "레이더 아이콘 일괄 수정";
		} else if ($gucd=="2")
		{
			$reason = "복합센서 아이콘 일괄 수정";
		} else if ($gucd=="3")
		{
			$reason = "카메라 아이콘 일괄 수정";
		} else if ($gucd=="4")
		{
			$reason = "경광등 아이콘 일괄 수정";
		} else if ($gucd=="5")
		{
			$reason = "서치라이트 아이콘 일괄 수정";
		}
		insert_Log_Person($regid, $ip, $t_ip, $reason, $database);
		error_log(json_encode($response_array));
		exit(json_encode($response_array));
		break;
		
	case 'AlarmSave': // ajax - select optionbox 를 채운다.
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_POST = ' . var_export($_POST, 1));
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$seq = getAnyParameter("seq","");
		$al = getAnyParameter("al","");
		$rimg_aec = getAnyParameter("rimg_aec","");
		$simg_aec = getAnyParameter("simg_aec","");
		$at = getAnyParameter("at","");
		$asv = getAnyParameter("asv","");
		$ast = getAnyParameter("ast","");

		error_log("ast : " . $ast);
		$regid = $session->user["id"];
		
		$database->prepare("update tb_pc_set set  
								al = :al, rimg_aec = :rimg_aec, simg_aec = :simg_aec, at = :at,  asv = :asv,  ast = :ast, 
								upid = :upid, updt = now() 
							where seq = :seq
						");
		$database->bind(':al', $al);
		$database->bind(':rimg_aec', $rimg_aec);
		$database->bind(':simg_aec', $simg_aec);
		$database->bind(':at', $at);
		$database->bind(':asv', $asv);
		$database->bind(':ast', $ast);
		$database->bind(':upid', $regid);
		$database->bind(':seq', $seq);
		$database->execute();
		
		error_log($database->rowCount());
		
		if ($database->rowCount() > 0)
			$response_array["result"] = 'success';
		else
			$response_array["result"] = 'fail';

		$ip = getClientIPv4();
		$t_ip = "";
		insert_Log_Person($regid, $ip, $t_ip, "이벤트알림 설정 수정", $database);
		error_log(json_encode($response_array));
		exit(json_encode($response_array));
		break;
		
	case 'CodeDelete': // ajax - select optionbox 를 채운다.
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_POST = ' . var_export($_POST, 1));
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		$gucd = getAnyParameter("gucd","");
		$cdcd = getAnyParameter("cdcd","");

		error_log("gucd : " . $gucd);
		error_log("cdcd : " . $cdcd);
		$regid = $session->user["id"];
		
		$database->prepare("delete from tb_code 
							where gucd = :gucd and cdcd = :cdcd 
						");
		$database->bind(':gucd', $gucd);
		$database->bind(':cdcd', $cdcd);
		$database->execute();
		
		error_log("rowCount : " . $database->rowCount());
		
		if ($database->rowCount() > 0)
			$response_array["result"] = 'success';
		else
			$response_array["result"] = 'fail';

		$ip = getClientIPv4();
		$t_ip = "";
		insert_Log_Person($regid, $ip, $t_ip, "기초코드 삭제", $database);
		error_log(json_encode($response_array));
		exit(json_encode($response_array));
		break;

	case 'CodeEdit': // ajax - select optionbox 를 채운다.
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_POST = ' . var_export($_POST, 1));
	// db field gucd, note, cdcd, name, odby, useyn, if(useyn = 'Y', '사용', '미사용') as usenm 
		$gucd = getAnyParameter("gucd","");
		$note = getAnyParameter("note","");
		$cdcd = getAnyParameter("cdcd","");
		$name = getAnyParameter("name","");
		$odby = getAnyParameter("odby","");
		$useyn = getAnyParameter("useyn","");

		error_log("cdcd : " . $cdcd);
		$regid = $session->user["id"];
		
		$database->prepare("update tb_code set 
								note = :note, name = :name, useyn = :useyn, odby = :odby, upid = :upid, updt = now() 
							where gucd = :gucd and cdcd = :cdcd 
						");
		$database->bind(':note', $note);
		$database->bind(':name', $name);
		$database->bind(':useyn', $useyn);
		$database->bind(':odby', $odby);
		$database->bind(':upid', $regid);
		$database->bind(':gucd', $gucd);
		$database->bind(':cdcd', $cdcd);
		$database->execute();
		
		error_log($database->rowCount());
		
		if ($database->rowCount() > 0)
			$response_array["result"] = 'success';
		else
			$response_array["result"] = 'fail';

		$ip = getClientIPv4();
		$t_ip = "";
		insert_Log_Person($regid, $ip, $t_ip, "기초코드 수정", $database);
		error_log(json_encode($response_array));
		exit(json_encode($response_array));
		break;
		
	case 'CodeSave': // ajax - select optionbox 를 채운다.
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_POST = ' . var_export($_POST, 1));
	// db field gucd, note, cdcd, name, odby, useyn, if(useyn = 'Y', '사용', '미사용') as usenm 
		$gucd = getAnyParameter("gucd","");
		$note = getAnyParameter("note","");
		$cdcd = getAnyParameter("cdcd","");
		$name = getAnyParameter("name","");
		$odby = getAnyParameter("odby","");
		$useyn = getAnyParameter("useyn","");

		error_log("cdcd : " . $cdcd);
		$regid = $session->user["id"];
		
		$database->prepare("insert into tb_code( gucd, note, cdcd, name, useyn, odby, regid, regdt )
								values ( :gucd, :note, :cdcd, :name, :useyn, :odby, :regid, now() )
						");
		$database->bind(':note', $note);
		$database->bind(':name', $name);
		$database->bind(':useyn', $useyn);
		$database->bind(':odby', $odby);
		$database->bind(':regid', $regid);
		$database->bind(':gucd', $gucd);
		$database->bind(':cdcd', $cdcd);
		$database->execute();
		
		error_log($database->rowCount());
		
		if ($database->rowCount() > 0)
			$response_array["result"] = 'success';
		else
			$response_array["result"] = 'fail';

		$ip = getClientIPv4();
		$t_ip = "";
		insert_Log_Person($regid, $ip, $t_ip, "기초코드 등록", $database);
		error_log(json_encode($response_array));
		exit(json_encode($response_array));
		break;
		
	case 'UserDelete': // ajax - select optionbox 를 채운다.
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_POST = ' . var_export($_POST, 1));

		$id = getAnyParameter("id","");

		error_log("id : " . $id);
		$regid = $session->user["id"];
		
		$database->prepare("update tb_person set useyn = 'N', delid = :delid, deldt = now() 
							where id=:id
						");
		$database->bind(':id', $id);
		$database->bind(':delid', $regid);
		$database->execute();
		
		error_log("rowCount : " . $database->rowCount());
		
		if ($database->rowCount() > 0)
			$response_array["result"] = 'success';
		else
			$response_array["result"] = 'fail';

		$ip = getClientIPv4();
		$t_ip = "";
		insert_Log_Person($regid, $ip, $t_ip, "사용자 삭제", $database);
		error_log(json_encode($response_array));
		exit(json_encode($response_array));
		break;

	case 'UserEdit': // ajax - select optionbox 를 채운다.
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_POST = ' . var_export($_POST, 1));
	// db field id, name, belong, class, power,useyn , pwchange

		$id = getAnyParameter("id","");
		$name = getAnyParameter("name","");
		$belong = getAnyParameter("belong","");
		$class = getAnyParameter("class","");
		$power = getAnyParameter("power","");
		$useyn = getAnyParameter("useyn","");
		$pwd = getAnyParameter("pwd","");
		$pwchange = getAnyParameter("pwchange","");

		error_log("id : " . $id);
		$regid = $session->user["id"];
		
		if ($pwchange == "true")
		{
			$database->prepare("update tb_person set name=:name, pwd=:pwd, class=:class, power=:power,
											belong = :belong, upid = :upid, updt = now()
									where id = :id
							");
			$database->bind(':pwd', $pwd);
		} else 
		{
			$database->prepare("update tb_person set name=:name, class=:class, power=:power,
											belong = :belong, upid = :upid, updt = now()
									where id = :id
							");
		}
		$database->bind(':id', $id);
		$database->bind(':name', $name);
		$database->bind(':class', $class);
		$database->bind(':belong', $belong);
		$database->bind(':power', $power);
		$database->bind(':upid', $regid);
		$database->execute();
		
		error_log($database->rowCount());
		
		if ($database->rowCount() > 0)
			$response_array["result"] = 'success';
		else
			$response_array["result"] = 'fail';

		$ip = getClientIPv4();
		$t_ip = "";
		insert_Log_Person($regid, $ip, $t_ip, "사용자 수정", $database);
		error_log(json_encode($response_array));
		exit(json_encode($response_array));
		break;
		

	case 'UserSave': // ajax - select optionbox 를 채운다.
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_POST = ' . var_export($_POST, 1));
	// db field id, name, belong, class, power,useyn , pwchange

		$id = getAnyParameter("id","");
		$name = getAnyParameter("name","");
		$belong = getAnyParameter("belong","");
		$class = getAnyParameter("class","");
		$power = getAnyParameter("power","");
		$useyn = getAnyParameter("useyn","");
		$pwd = getAnyParameter("pwd","");
		$pwchange = getAnyParameter("pwchange","");

		error_log("id : " . $id);
		$regid = $session->user["id"];
		
		$database->prepare("insert into tb_person( id, name, pwd, class, belong, power, useyn, regid, regdt ) values
							(:id, :name, :pwd, :class, :belong, :power, 'Y', :regid, now() )
						");
		$database->bind(':id', $id);
		$database->bind(':name', $name);
		$database->bind(':pwd', $pwd);
		$database->bind(':class', $class);
		$database->bind(':belong', $belong);
		$database->bind(':power', $power);
		$database->bind(':regid', $regid);
		$database->execute();
		
		error_log($database->rowCount());
		
		if ($database->rowCount() > 0)
			$response_array["result"] = 'success';
		else
			$response_array["result"] = 'fail';

		$ip = getClientIPv4();
		$t_ip = "";
		insert_Log_Person($regid, $ip, $t_ip, "사용자 추가", $database);
		error_log(json_encode($response_array));
		exit(json_encode($response_array));
		break;
		
	case 'ExceptDelete': // ajax - select optionbox 를 채운다.
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_POST = ' . var_export($_POST, 1));

		$delete = getAnyParameter("delete","");
		error_log('delete = ' . var_export($delete, 1));
		$regid = $session->user["id"];
		
		if ($delete!="")
		{
			for ($i=0;$i<count($delete);$i++)
			{
				$database->prepare("delete from tb_jang_pt 
									where ip = :ip and n = :n and disp = 'N'
								");
				$database->bind(':ip', $delete[$i]["ip"]);
				$database->bind(':n', $delete[$i]["n"]);
				$database->execute();
				error_log('delete = ' . $database->rowCount());
				error_log('$delete[$i][ip] = ' .$delete[$i]["ip"]);
				error_log('$delete[$i][n] = ' . $delete[$i]["n"]);
			}
		}

		error_log($database->rowCount());
		
		if ($database->rowCount() > 0)
			$response_array["result"] = 'success';
		else
			$response_array["result"] = 'fail';

		error_log(json_encode($response_array));
		$ip = getClientIPv4();
		$t_ip = "";
		insert_Log_Person($regid, $ip, $t_ip, "배제구역 삭제", $database);
		exit(json_encode($response_array));
		break;
		
	case 'ExceptSave': // ajax - select optionbox 를 채운다.
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_POST = ' . var_export($_POST, 1));

		$update = getAnyParameter("update","");
		$insert = getAnyParameter("insert","");
		error_log('$update = ' . var_export($update, 1));
		error_log('$insert = ' . var_export($insert, 1));
		$regid = $session->user["id"];
		if ($update!="")
		{
			for ($i=0;$i<count($update);$i++)
			{
				$database->prepare("update tb_jang_pt set  name=:name, useyn=:useyn, 
											upid = :upid, updt = now()
									where ip = :ip and n = :n and disp = :disp
								");
				$database->bind(':name', $update[$i]["name"]);
				$database->bind(':useyn', $update[$i]["useyn"]);
				$database->bind(':upid', $regid);
				$database->bind(':ip', $update[$i]["ip"]);
				$database->bind(':n', $update[$i]["n"]);
				$database->bind(':disp', $update[$i]["disp"]);
				$database->execute();
			}
		}
		if ($insert!="")
		{
			for ($i=0;$i<count($insert);$i++)
			{
				$database->prepare("insert into tb_jang_pt( name, useyn, ip, p_x, p_y, n, ord, disp, regid, regdt )
										values ( :name, :useyn, :ip, :p_x, :p_y, :n, :ord, :disp, :regid, now() )
								");
				$database->bind(':name', $insert[$i]["name"]);
				$database->bind(':useyn', $insert[$i]["useyn"]);
				$database->bind(':ip', $insert[$i]["ip"]);
				$database->bind(':p_x', $insert[$i]["p_x"]);
				$database->bind(':p_y', $insert[$i]["p_y"]);
				$database->bind(':n', $insert[$i]["n"]);
				$database->bind(':ord', $insert[$i]["ord"]);
				$database->bind(':disp', $insert[$i]["disp"]);
				$database->bind(':regid', $regid);
				$database->execute();
			}
		}

		error_log($database->rowCount());
		
		if ($database->rowCount() > 0)
			$response_array["result"] = 'success';
		else
			$response_array["result"] = 'fail';

		error_log(json_encode($response_array));
		$ip = getClientIPv4();
		$t_ip = "";
		insert_Log_Person($regid, $ip, $t_ip, "배제구역 저장", $database);
		exit(json_encode($response_array));
		break;

	case 'EventLogResultSave': // ajax - select optionbox 를 채운다.
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_POST = ' . var_export($_POST, 1));

		$seq = getAnyParameter("seq","");
		$note = getAnyParameter("note","");
		$result = getAnyParameter("result","");

		$regid = $session->user["id"];

		$database->prepare("update tb_log_fin set  note = :note, result = '1',
									upid = :upid, updt = now()
							where seq = :seq
						");
		$database->bind(':note', $note);
		$database->bind(':upid', $regid);
		$database->bind(':seq', $seq);
		$database->execute();

		error_log($database->rowCount());
		
		if ($database->rowCount() > 0)
			$response_array["result"] = 'success';
		else
			$response_array["result"] = 'fail';
		
		error_log(json_encode($response_array));
		$ip = getClientIPv4();
		$t_ip = "";

		$database->prepare("select ip from tb_log_fin
							where seq = :seq
						");
		$database->bind(':seq', $seq);
		$database->execute();
		$rows = $database->fetchAll();
		$t_ip = $rows[0]["ip"];

		insert_Log_Person($regid, $ip, $t_ip, $reason, $database);
		exit(json_encode($response_array));
		break;

	case 'PowerSave': // ajax - select optionbox 를 채운다.
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_POST = ' . var_export($_POST, 1));

		$seq = getAnyParameter("seq","");
		$pow = getAnyParameter("pow","");

		$regid = $session->user["id"];
		for ($i=0;$i<count($seq);$i++)
		{
			$database->prepare("update tb_jang set pow=:pow,
										upid = :upid, updt = now()
								where seq = :seq
							");
			$database->bind(':pow', $pow);
			$database->bind(':upid', $regid);
			$database->bind(':seq', $seq[$i]);
			$database->execute();
		}

		error_log($database->rowCount());
		
		if ($database->rowCount() > 0)
			$response_array["result"] = 'success';
		else
			$response_array["result"] = 'fail';

		error_log(json_encode($response_array));
		$ip = getClientIPv4();
		$t_ip = "";
		$reason = "";
		if ($pow=="1")
		{
			$reason = "파워 ON";
		} else
		{
			$reason = "파워 OFF";
		} 
		if (count($seq)==1)
		{
			$database->prepare("select ip from tb_jang
								where seq = :seq
							");
			$database->bind(':seq', $seq[0]);
			$database->execute();
			$rows = $database->fetchAll();
			$t_ip = $rows[0]["ip"];
		}
		insert_Log_Person($regid, $ip, $t_ip, $reason, $database);
		exit(json_encode($response_array));
		break;

	case 'SensingSave': // ajax - select optionbox 를 채운다.
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_POST = ' . var_export($_POST, 1));

		$seq = getAnyParameter("seq","");
		$d_t = getAnyParameter("d_t","");
		$d_st = getAnyParameter("d_st","");
		$d_et = getAnyParameter("d_et","");

		$regid = $session->user["id"];
		
		$ip = getClientIPv4();
		$t_ip = "";
		$reason = "";
		if ($d_t=="1")
		{
			$reason = "감지 매일";
		} else if ($d_t=="2")
		{
			$reason = "감지 시간별";
		} else
		{
			$reason = "감지 배제";
		}

		$failCount =0;
		for ($i=0;$i<count($seq);$i++)
		{
			$database->prepare("update tb_jang set d_t=:d_t, d_st=:d_st, d_et=:d_et,
										upid = :upid, updt = now()
								where seq = :seq
							");
			$database->bind(':d_t', $d_t);
			$database->bind(':d_st', $d_st);
			$database->bind(':d_et', $d_et);
			$database->bind(':upid', $regid);
			$database->bind(':seq', $seq[$i]);
			$database->execute();
			
			if ($database->rowCount() == 0)
			{
				$failCount++;
			}
			$database->prepare("select ip from tb_jang
								where seq = :seq
							");
			$database->bind(':seq', $seq[$i]);
			$database->execute();
			$rows = $database->fetchAll();
			if ($rows)
			{
				$t_ip = $rows[0]["ip"];
				insert_Log_Person($regid, $ip, $t_ip, $reason, $database);
			}
		}

		error_log("$failCount=".$failCount);
		
		if ($failCount == 0)
			$response_array["result"] = 'success';
		else
			$response_array["result"] = 'fail';

		error_log(json_encode($response_array));

		exit(json_encode($response_array));
		break;

	case 'EquipDelete': // ajax - select optionbox 를 채운다.
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_POST = ' . var_export($_POST, 1));

		$seq = getAnyParameter("seq","");

		error_log("seq : " . $ip);
		$regid = $session->user["id"];
		
		$database->prepare("delete from tb_jang 
							where seq=:seq
						");
		$database->bind(':seq', $seq);
		$database->execute();
		
		error_log($database->rowCount());
		
		if ($database->rowCount() > 0)
			$response_array["result"] = 'success';
		else
			$response_array["result"] = 'fail';

		error_log(json_encode($response_array));
		$ip = getClientIPv4();
		$t_ip = "";
		$reason = "장비삭제";

		$database->prepare("select ip from tb_jang
							where seq = :seq
						");
		$database->bind(':seq', $seq[0]);
		$database->execute();
		$rows = $database->fetchAll();
		$t_ip = $rows[0]["ip"];

		insert_Log_Person($regid, $ip, $t_ip, $reason, $database);
		
		exit(json_encode($response_array));
		break;

	case 'EquipEdit': // ajax - select optionbox 를 채운다.
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_POST = ' . var_export($_POST, 1));

		$seq = getAnyParameter("seq","");
		$gucd = getAnyParameter("gucd","");
		$name = getAnyParameter("name","");
		$ip = getAnyParameter("ip","");
		$id = getAnyParameter("id","");
		$tilt = getAnyParameter("tilt","");
		$p_x = getAnyParameter("p_x","");
		$p_y = getAnyParameter("p_y","");
		$dist = getAnyParameter("dist","");
		$t_r = getAnyParameter("t_r","");
		$t_rp = getAnyParameter("t_rp","");
		$ca_hei = getAnyParameter("ca_hei","");
		$note = getAnyParameter("note","");
		$useyn = getAnyParameter("useyn","");
		$cell = getAnyParameter("cell","");

		error_log("seq : " . $ip);
		$regid = $session->user["id"];
		
		$database->prepare("update tb_jang set gucd=:gucd, id=:id, ip=:ip, name=:name, note=:note 
										,p_x=:p_x, p_y=:p_y, dist=:dist, t_r=:t_r, t_rp=:t_rp 
										,useyn=:useyn, cell=:cell, ca_hei=:ca_hei, tilt=:tilt, upid=:upid, updt=now()
							where seq=:seq
						");
		$database->bind(':gucd', $gucd);
		$database->bind(':id', $id);
		$database->bind(':ip', $ip);
		$database->bind(':name', $name);
		$database->bind(':note', $note);
		$database->bind(':p_x', $p_x);
		$database->bind(':p_y', $p_y);
		$database->bind(':dist', $dist);
		$database->bind(':t_r', $t_r);
		$database->bind(':t_rp', $t_rp);
		$database->bind(':useyn', $useyn);
		$database->bind(':cell', $cell);
		$database->bind(':ca_hei', $ca_hei);
		$database->bind(':tilt', $tilt);
		$database->bind(':upid', $regid);
		$database->bind(':seq', $seq);
		$database->execute();
		
		error_log($database->rowCount());
		
		if ($database->rowCount() > 0)
			$response_array["result"] = 'success';
		else
			$response_array["result"] = 'fail';

		error_log(json_encode($response_array));
		$ip = getClientIPv4();
		$t_ip = $ip;
		$reason = "장비수정";
		insert_Log_Person($regid, $ip, $t_ip, $reason, $database);

		exit(json_encode($response_array));
		break;

	case 'EquipSave': // ajax - select optionbox 를 채운다.
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_POST = ' . var_export($_POST, 1));

		$gucd = getAnyParameter("gucd","");
		$name = getAnyParameter("name","");
		$ip = getAnyParameter("ip","");
		$id = getAnyParameter("id","");
		$tilt = getAnyParameter("tilt","");
		$p_x = getAnyParameter("p_x","");
		$p_y = getAnyParameter("p_y","");
		$dist = getAnyParameter("dist","");
		$t_r = getAnyParameter("t_r","");
		$t_rp = getAnyParameter("t_rp","");
		$ca_hei = getAnyParameter("ca_hei","");
		$note = getAnyParameter("note","");
		$useyn = getAnyParameter("useyn","");
		$cell = getAnyParameter("cell","");

		error_log("ip : " . $ip);
		$regid = $session->user["id"];
		
		$database->prepare("insert into tb_jang(gucd, id, ip, name, note ,p_x, p_y, dist, t_r, t_rp, 
										useyn, cell, ca_hei, tilt, regid, regdt, connect) values
							(:gucd, :id, :ip, :name, :note, :p_x, :p_y, :dist, :t_r, :t_rp, :useyn, :cell, :ca_hei, :tilt, :regid, now(), 2)
						");
		$database->bind(':gucd', $gucd);
		$database->bind(':id', $id);
		$database->bind(':ip', $ip);
		$database->bind(':name', $name);
		$database->bind(':note', $note);
		$database->bind(':p_x', $p_x);
		$database->bind(':p_y', $p_y);
		$database->bind(':dist', $dist);
		$database->bind(':t_r', $t_r);
		$database->bind(':t_rp', $t_rp);
		$database->bind(':useyn', $useyn);
		$database->bind(':cell', $cell);
		$database->bind(':ca_hei', $ca_hei);
		$database->bind(':tilt', $tilt);
		$database->bind(':regid', $regid);
		$database->execute();
		
		error_log($database->rowCount());
		
		if ($database->rowCount() > 0)
			$response_array["result"] = 'success';
		else
			$response_array["result"] = 'fail';

		error_log(json_encode($response_array));
		
		$ip = getClientIPv4();
		$t_ip = $ip;
		$reason = "장비등록";
		insert_Log_Person($regid, $ip, $t_ip, $reason, $database);

		exit(json_encode($response_array));
		break;

	case 'relEquipSave': // ajax - select optionbox 를 채운다.
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_POST = ' . var_export($_POST, 1));

		$ip = getAnyParameter("up_ip","");
		$ip_list = getAnyParameter("ip_list","");
		$gucd_list = getAnyParameter("gucd_list","");
		error_log("ip_list : " . $ip_list[0]);
		$regid = $session->user["id"];
		
		$database->prepare("delete from tb_jang_link
							where ip=:ip
						");
		$database->bind(':ip', $ip);
		$database->execute();
		
		//$link_ips = split(',',$link_ip_list);
		
		for ($i=0;$i<count($ip_list);$i++)
		{
			$database->prepare("insert into tb_jang_link (ip, gucd, link_ip, regid, regdt )
								values (:ip, :gucd, :link_ip, :regid, NOW())
							");
			$database->bind(':ip', $ip);
			$database->bind(':gucd', $gucd_list[$i]);
			$database->bind(':link_ip', $ip_list[$i]);
			$database->bind(':regid', $regid);
			$database->execute();
		}
		
		error_log($database->rowCount());
		
		if ($database->rowCount() > 0)
			$response_array["result"] = 'success';
		else
			$response_array["result"] = 'fail';

		error_log(json_encode($response_array));
		$ip = getClientIPv4();
		$t_ip = $ip;
		$reason = "연동장비 변경";
		insert_Log_Person($regid, $ip, $t_ip, $reason, $database);
		exit(json_encode($response_array));
		break;

	case 'ExceptSubLoad': // ajax - select optionbox 를 채운다.
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_POST = ' . var_export($_POST, 1));

		$database->prepare("select  seq, ip, p_x, p_y, disp, n, ord , name, useyn 
							from tb_jang_pt
							order by ip asc, disp desc, n asc, ord asc
						");
		$database->execute();
		$rows = $database->fetchAll();

		error_log(json_encode($rows));
		exit(json_encode($rows));
		break;

	case 'ExceptLoad': // ajax - select optionbox 를 채운다.
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_POST = ' . var_export($_POST, 1));

		$database->prepare("select  ip, disp, n, name, if(disp = 'Y', '감지', '배제') disp_nm , useyn 
							from tb_jang_pt
							where disp='N'
							group by ip, disp, n, name
							order by ip asc, disp desc, n asc
						");
		$database->execute();
		$rows = $database->fetchAll();

		error_log(json_encode($rows));
		exit(json_encode($rows));
		break;

	case 'CodeLoad': // ajax - select optionbox 를 채운다.
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_POST = ' . var_export($_POST, 1));

		$database->prepare("select gucd, note, cdcd, name, odby, useyn, if(useyn = 'Y', '사용', '미사용') as usenm 
							from tb_code 
							order by gucd asc, cdcd asc, odby asc 
						");
		$database->execute();
		$rows = $database->fetchAll();

		error_log(json_encode($rows));
		exit(json_encode($rows));
		break;

	case 'UserLoad': // ajax - select optionbox 를 채운다.
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_POST = ' . var_export($_POST, 1));

		$database->prepare("select @ROWNUM := @ROWNUM + 1 AS NO , id, name, belong, class, power,useyn
							from tb_person , (SELECT @ROWNUM := 0) R 
							where useyn = 'Y' 
							order by id asc 
						");
		$database->execute();
		$rows = $database->fetchAll();

		error_log(json_encode($rows));
		exit(json_encode($rows));
		break;

	case 'DeletedUserLoad': // ajax - select optionbox 를 채운다.
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_POST = ' . var_export($_POST, 1));

		$database->prepare("select @ROWNUM := @ROWNUM + 1 AS NO , id, name, belong, class, power,useyn
							from tb_person , (SELECT @ROWNUM := 0) R 
							where useyn = 'N' 
							order by id asc 
						");
		$database->execute();
		$rows = $database->fetchAll();

		error_log(json_encode($rows));
		exit(json_encode($rows));
		break;

	case 'SettingDataLoad': // ajax - select optionbox 를 채운다.
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_POST = ' . var_export($_POST, 1));

		$database->prepare("select seq, 
								rimg, rimg_o, rimg_oc, rimg_r, rimg_rc, rimg_aoc, rimg_ac, rimg_aec, rimg_fp, rimg_fl, rimg_nc, rimg_unc, rimg_unc_s, rimg_index, 
								simg, simg_o, simg_oc, simg_r, simg_rc, simg_aoc, simg_ac, simg_aec, 
								cimg, cimg_o, cimg_oc, cimg_r, cimg_rc, cimg_aoc, cimg_ac, cimg_aec, al, at, asv, ast,
								concat(date_format(now(), '%Y'), '-', date_format(ws_m, '%m-%d')) ws_m, 
								concat(date_format(date_add(now(), interval 12 month), '%Y'), '-', date_format(we_m, '%m-%d')) we_m, 
								concat(date_format(now(), '%Y'), '-', date_format(ss_m, '%m-%d')) ss_m, 
								concat(date_format(now(), '%Y'), '-', date_format(se_m, '%m-%d')) se_m
								, left(ws_t, 5) ws_t
								, left(we_t, 5) we_t
								, left(ss_t, 5) ss_t
								, left(se_t, 5) se_t
								from tb_pc_set
						");
		$database->execute();
		$rows = $database->fetchAll();

		error_log(json_encode($rows));
		exit(json_encode($rows));
		break;

	case 'EventPointLoad': // ajax - select optionbox 를 채운다.
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_POST = ' . var_export($_POST, 1));
		
		$ip = getAnyParameter("ip",0);
		$gro = getAnyParameter("gro",0);

		$database->prepare("select no, p_x, p_y from tb_log_fin_pt where ip = :ip and gro = :gro
						");
		$database->bind(':ip', $ip);
		$database->bind(':gro', $gro);
		$database->execute();
		$rows = $database->fetchAll();

		//error_log(json_encode($rows));
		exit(json_encode($rows));
		break;
		
	case 'EventDataLoad': // ajax - select optionbox 를 채운다.
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_POST = ' . var_export($_POST, 1));
		
		$sdate = getAnyParameter("sdate",0);
		$edate = getAnyParameter("edate",0);
		$eventequip = getAnyParameter("eventequip",0);
		$eventtype = getAnyParameter("eventtype",0);

		$database->prepare("select @ROWNUM := @ROWNUM + 1 AS NO, a.seq
								,date_format(a.regdt, '%Y-%m-%d %H:%i:%s') regdt ,date_format(a.updt, '%Y-%m-%d %H:%i:%s') updt 
								,b.name as na2, b.note as na3, a.ip, a.id, a.gro, a.note ,a.result, b.gucd
								,(select name from tb_code where gucd = 'R1' and cdcd = a.result) as resultnm
								,(SELECT NAME FROM tb_code WHERE gucd = 'J1' and cdcd = b.gucd) as na1
								from tb_log_fin a inner join tb_jang b on a.ip = b.ip, (SELECT @ROWNUM := 0) R
								where a.useyn = 'Y' and date_format(a.regdt, '%Y-%m-%d') between :sdate and :edate
									and (:eventequip = 0 or b.gucd = :eventequip1) 
									and (:eventtype = 2 or a.result = :eventtype1) 
								order by a.regdt desc
						");
		$database->bind(':sdate', $sdate);
		$database->bind(':edate', $edate);
		$database->bind(':eventequip', $eventequip);
		$database->bind(':eventequip1', $eventequip);
		$database->bind(':eventtype', $eventtype);
		$database->bind(':eventtype1', $eventtype);
		$database->execute();
		$rows = $database->fetchAll();

		//error_log(json_encode($rows));
		exit(json_encode($rows));
		break;

	case 'EquipRelationDataLoad': // ajax - select optionbox 를 채운다.
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_POST = ' . var_export($_POST, 1));
		
		$database->prepare("select 
								(select gucd from tb_jang where ip = a.ip) as up_gucd 
								,gucd ,ip as up_ip ,link_ip 
								,(select p_x from tb_jang where ip = a.link_ip) as sx 
								,(select p_y from tb_jang where ip = a.link_ip) as sy 
								,(select name from tb_code where gucd = 'J1' and cdcd = a.gucd) as gunm
								,(select name from tb_jang where ip = a.link_ip) as name 
								from tb_jang_link a
								order by ip asc, link_ip asc 
						");
		$database->execute();
		$rows = $database->fetchAll();

		//error_log(json_encode($rows));
		exit(json_encode($rows));
		break;

	case 'EquipDataLoad': // ajax - select optionbox 를 채운다.
		if (!isset($session->user)) {
			$response_array["result"] = 'sessionexpire';
			exit(json_encode($response_array));
		}
		error_log('$_GET = ' . var_export($_GET, 1));
		error_log('$_POST = ' . var_export($_POST, 1));
		
		$database->prepare("select @ROWNUM := @ROWNUM + 1 AS NO, seq, gucd, pow, icon, connect, fin, useyn
								,(select name from tb_code where gucd = 'J1' and cdcd = a.gucd) as gunm
								, ip, id, name, p_x, p_y, dist, t_r, t_rp, note
								, pow, connect, fin, useyn, ca_hei, d_t, d_st, d_et, cell, tilt
								,if(pow = '1', 'ON', 'OFF') as pow_st
								,if(connect = '1', '정상', '장애') as connect_st
								,if(fin = '1', '탐지', '미탐지') as fin_st
								,if(useyn = 'Y', '사용', '미사용') as usenm
								, sens_normal, sens_bad, sens_current
								,if(sens_use = '0', '평상시', '악천후') as sens_mode
								,case when d_t = '1' or d_t = '2' then '감지' else '배제' end as d_tnm 
								,date_format(a.updt, '%Y-%m-%d %H:%i:%s') updt
								from tb_jang a, (SELECT @ROWNUM := 0) R
								order by gucd asc, name asc
						");
		$database->execute();
		$rows = $database->fetchAll();

		//error_log(json_encode($rows));
		exit(json_encode($rows));
		break;

	default:

		$redirect_location = 'abnormal-access.html';

}

header('Location: ' . $redirect_location);
?>