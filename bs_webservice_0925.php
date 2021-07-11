<?php
/**
 * 회원관리 Business Logics
 */

require_once 'lib/_init.php';
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
	//Upload
	case "Upload":
		if(isset($_FILES['myimage'])){
			$img = $_FILES['myimage']['name'];
			$tmpimg = $_FILES['myimage']['tmp_name'];
			move_uploaded_file($tmpimg , "./uploaded_images/$img");
			echo "[success] image ($img) uploaded successfully.";
			exit();
		}else{
			echo "[error] there is no data.";
		}
		break;
	//GetImageRoot
	case "GetImageRoot":
		$PICTURE_NM = $_POST["PICTURE_NM"];
		//$MEASUREMENT_SQ = $_POST["MEASUREMENT_SQ"];

		$action = 'GetImageRoot';
		$database->prepare("
			SELECT UPLOAD_ROOT FROM bs_picture WHERE PICTURE_NM = :PICTURE_NM
		");
		$database->bind(':PICTURE_NM', $PICTURE_NM);
		$database->execute();
		$rows = $database->fetch();

		exit(json_encode($rows));
		break;
	//Picture
	case "SetPicture":
		$MEASUREMENT_SQ = getAnyParameter("MEASUREMENT_SQ","");
		$POSE_TYPE = getAnyParameter("POSE_TYPE","");
		$PICTURE_NM = getAnyParameter("PICTURE_NM","");
		$UPLOAD_ROOT = getAnyParameter("UPLOAD_ROOT","");
		
		$action = 'SetPicture';

		// 장비 등록 쿼리
		$database->prepare("
			INSERT bs_picture (MEASUREMENT_SQ,POSE_TYPE,PICTURE_NM, UPLOAD_ROOT, REG_DT)
			values ( :MEASUREMENT_SQ, :POSE_TYPE, :PICTURE_NM, :UPLOAD_ROOT, now() )
		");
		$database->bind(':MEASUREMENT_SQ', $MEASUREMENT_SQ);
		$database->bind(':POSE_TYPE', $POSE_TYPE);
		$database->bind(':PICTURE_NM', $PICTURE_NM);
		$database->bind(':UPLOAD_ROOT', $UPLOAD_ROOT);

		$database->execute();
		error_log('rowCount='.$database->rowCount());
		
		if ($database->rowCount() == 0) {
			$note = 'Picture 등록 실패';
		}
		else {
			$note = 'Picture 등록 성공';
		}

		$ip = getClientIPv4();
		$user_seq = 0;
		
		insert_Log_Person($user_seq, $ip, 0, $action, $note, $database);
		exit(json_encode($note));
		break;
	
	//User
	case 'UserSearch': // 사용자 검색
		$PHONE_NO = getAnyParameter("PHONE_NO","");
		$DEVICE_SQ = getAnyParameter("DEVICE_SQ",0);

		$action = 'UserSearch';
		//$pwd_encrypted = getAnyParameter("pwd_encrypted",0);

		$database->prepare("
			SELECT USER_SQ,a.CENTER_SQ,CENTER_NM,USER_NM,GENDER,PHONE_NO,EMAIL,GRADE,USERIMAGE,BIRTH_DT 
		 	FROM tb_user a
		 	left outer join tb_center b on a.CENTER_SQ=b.CENTER_SQ
		 	WHERE PHONE_NO like CONCAT('%', :PHONE_NO, '%') AND (ISUSE=1)
		");
		
		$database->bind(':PHONE_NO', $PHONE_NO);
		$database->execute();

		$rows = $database->fetchAll();
		if (!$rows) {
			$note = '사용자 검색 실패';
		} else {
			$note = '사용자 검색 성공';
		}

		$ip = getClientIPv4();
		$user_seq = 0;
		
		insert_Log_Person($user_seq, $ip, $DEVICE_SQ, $action, $note, $database);
		exit(json_encode($rows));

		break;
		
	case 'UserRegisterIDCheck': // 사용자 아이디 중복 체크 
		$USERID = getAnyParameter("USERID","");
		$DEVICE_SQ = getAnyParameter("DEVICE_SQ",0);
		$action = 'UserRegisterIDCheck';
		//$pwd_encrypted = getAnyParameter("pwd_encrypted",0);

		$database->prepare("
			SELECT USERID from tb_user where USERID=:USERID;
		");
		$database->bind(':USERID', $USERID);
		$database->execute();
		$row = $database->fetch();
		
		if (!$row) {
			$response_array["result"] = 'success';
			$note = '사용자 아이디 중복 없슴';
		}
		else {
			$response_array["result"] = 'fail';
			$note = '사용자 아이디 중복';
		}

		$ip = getClientIPv4();
		$user_seq = 0;
		
		insert_Log_Person($user_seq, $ip, $DEVICE_SQ, $action, $note, $database);
		exit(json_encode($response_array));

		break;

	case 'UserRegisterSimple': // 사용자 간이 등록 
		$USERID = getAnyParameter("USERID","");
		$PHONE_NO = getAnyParameter("PHONE_NO","");
		$GENDER = getAnyParameter("GENDER","");
		$DEVICE_SQ = getAnyParameter("DEVICE_SQ",1);
		$action = 'UserRegisterSimple';
		//$pwd_encrypted = getAnyParameter("pwd_encrypted",0);

		$database->prepare("
			INSERT tb_user (USERID,PWD_ENCRYPTED,PHONE_NO,GENDER,CENTER_SQ, GRADE, ISUSE, LAST_DT, REG_DT)
			select :USERID, SHA2(:PWD_ENCRYPTED, 256), :PHONE_NO, :GENDER, CENTER_SQ, 1, 1, now(), now() from tb_device where DEVICE_SQ=:DEVICE_SQ
		");
		$database->bind(':USERID', $USERID);
		$database->bind(':PWD_ENCRYPTED', $PHONE_NO);
		$database->bind(':PHONE_NO', $PHONE_NO);
		$database->bind(':GENDER', $GENDER);
		$database->bind(':DEVICE_SQ', $DEVICE_SQ);
		$database->execute();
		error_log('rowCount='.$database->rowCount());
		
		if ($database->rowCount() == 0) {
			$response_array["result"] = 'fail';
			$response_array["USER_SQ"] = 0;
			$note = '사용자 간이등록 실패';
		}
		else {
			$database->prepare("
				select LAST_INSERT_ID() USER_SQ;
			");
			$database->execute();
			$row = $database->fetch();

			$response_array["result"] = 'success';
			$response_array["USER_SQ"] = $row["USER_SQ"];
			$note = '사용자 간이등록 성공';
		}

		$ip = getClientIPv4();
		$user_seq = 0;
		
		insert_Log_Person($user_seq, $ip, $DEVICE_SQ, $action, $note, $database);
		exit(json_encode($response_array));

		break;

	case 'DeviceRegister': // 장비 등록 
		$CENTER_SQ = getAnyParameter("CENTER_SQ","");
		$DEVICE_NM = getAnyParameter("DEVICE_NM","");
		
		$action = 'DeviceRegister';
		$DEVICE_SQ = 0;

		// 장비 등록 쿼리
		$database->prepare("
			INSERT tb_device (CENTER_SQ,DEVICE_NM,DEVICE_ST, LAST_DT, REG_DT)
			values ( :CENTER_SQ, :DEVICE_NM, 1, now(), now() )
		");
		$database->bind(':CENTER_SQ', $CENTER_SQ);
		$database->bind(':DEVICE_NM', $DEVICE_NM);
		$database->execute();
		error_log('rowCount='.$database->rowCount());
		
		if ($database->rowCount() == 0) {
			$response_array["result"] = 'fail';
			$response_array["DEVICE_SQ"] = $DEVICE_SQ;
			$note = '장비 등록 실패';
		}
		else {
			$database->prepare("
				select LAST_INSERT_ID() DEVICE_SQ;
			");
			$database->execute();
			$row = $database->fetch();

			$DEVICE_SQ = $row["DEVICE_SQ"];
			$response_array["result"] = 'success';
			$response_array["DEVICE_SQ"] = $DEVICE_SQ;
			$note = '장비 등록 성공';
		}

		$ip = getClientIPv4();
		$user_seq = 0;
		
		insert_Log_Person($user_seq, $ip, $DEVICE_SQ, $action, $note, $database);
		exit(json_encode($response_array));

		break;

	case 'DeviceStatus': // 장비 상태 변경
		//$user_seq = getAnyParameter("user_seq",0);
		$DEVICE_SQ = getAnyParameter("DEVICE_SQ",0);
		$DEVICE_ST = getAnyParameter("DEVICE_ST",0);
		
		if ($DeviceStatus===1) {
			$action = 'Body Scanner Stop';
		} else if ($DeviceStatus===2){
			$action = 'Body Scanner Start';
		} else {
			$action = 'Body Scanner Error Reporting';
		}

		// 장비 상태 변경 
		$database->prepare("
			update tb_device set DEVICE_ST=:DEVICE_ST, LAST_DT=now() where DEVICE_SQ=:DEVICE_SQ
		");
		$database->bind(':DEVICE_ST', $DEVICE_ST);
		$database->bind(':DEVICE_SQ', $DEVICE_SQ);
		$database->execute();

		//  최종 장비 데이터 취득
		$database->prepare("
			SELECT DEVICE_SQ, CENTER_SQ, DEVICE_NM, DEVICE_ST, LAST_DT FROM tb_device
			WHERE DEVICE_SQ=:DEVICE_SQ
		");
		$database->bind(':DEVICE_SQ', $DEVICE_SQ);
		$database->execute();
		$row = $database->fetch();
		if (!$row) {
			$note = '장비 데이터 취득 실패';
		} else {
			$note = '장비 데이터 취득 성공';
		}

		$ip = getClientIPv4();
		$user_seq = 0;

		insert_Log_Person($user_seq, $ip, $DEVICE_SQ, $action, $note, $database);
		exit(json_encode($row));

		break;

	case "GetPoints":
		$action = 'GetPoints';

		$database->prepare("
			SELECT POINT_SQ , POINT_NM , OPENPOSE_CD 
			FROM bs_point
		");
		
		$database->execute();
		$row = $database->fetchAll();
		if (!$row) {
			$note = 'point 취득 실패';
		} else {
			$note = 'point 취득 성공';
		}

		$ip = getClientIPv4();
		$user_seq = 0;

		insert_Log_Person($user_seq, $ip, 0, $action, $note, $database);
		exit(json_encode($row));
		break;

	
	case 'GetPoseStandard': // 자세측정 체크 기본 데이터 취득

			$action = 'GetPoseStandard';
			$database->prepare("
				SELECT POSESTANDARD_SQ,	POSESTANDARD_NM,FROM_JOINT,TO_JOINT,CENTER_JOINT,CHECK_ORDER
				FROM bs_posestandard 
				order by CHECK_ORDER asc
			");

			$database->execute();
			$rows = $database->fetchAll();
			
			if ($database->rowCount() == 0) {
				$note = '자세 데이터 취득 실패';
			} else {
				$note = '자세 데이터 취득 성공';
			}
	
			$ip = getClientIPv4();
			$user_seq = 0;
	
			insert_Log_Person($user_seq, $ip, 0, $action, $note, $database);
			exit(json_encode($rows));
	
			break;

	case "GetRomStandard": // GET ROM STANDARD 

		$action = 'GetRomStandard';

		$database->prepare("
			SELECT ROMSTANDARD_SQ, ROMSTANDARD_NM , DIRECTION , MOVEMENT ,GOOD_ANGLE,NOTBAD_ANGLE,BAD_ANGLE
			FROM bs_romstandard
		");
				
		$database->execute();
		$rows = $database->fetchAll();
		error_log('rowCount='.$database->rowCount());
				
		if ($database->rowCount() == 0) {
			$note = 'ROM STANDARD 실패';
		}
		else {
			$note = 'ROM STANDARD 성공';
		}
		
		$ip = getClientIPv4();
		$user_seq = 0;
				
		insert_Log_Person($user_seq, $ip, 0, $action, $note, $database);
		exit(json_encode($rows));
		
		break;

	case "GetPoseData": //Get Past Pose Data
		
			$action = 'GetPoseData';
			//bs_measurement
			$USER_SQ = getAnyParameter("USER_SQ","");
			$MEASUREMENT_TYPE = getAnyParameter("MEASUREMENT_TYPE","");
			$DONE = getAnyParameter("DONE","");
			
			//POSE의 모든측정 MEASUREMENT_SQ 수집 후 (DONE==1) 
			//그 MEASUREMENT_SQ로 부터 POSE_DATA의 값 수집
	
			$database->prepare("
			SELECT c.PICTURE_NM ,a.MEASUREMENT_SQ, a.POSESTANDARD_SQ , a.DIRECTION , a.ANGLE  
			FROM bs_posedata a 
			LEFT OUTER JOIN bs_measurement b 
				ON a.MEASUREMENT_SQ = b.MEASUREMENT_SQ 
			LEFT OUTER JOIN bs_picture c 
				ON a.MEASUREMENT_SQ = c.MEASUREMENT_SQ AND a.DIRECTION = c.POSE_TYPE
					WHERE 
						b.USER_SQ = :USER_SQ
						AND b.MEASUREMENT_TYPE = :MEASUREMENT_TYPE
						AND b.DONE = :DONE
			ORDER BY a.POSESTANDARD_SQ, a.MEASUREMENT_SQ ASC
			");
		
			$database->bind(':USER_SQ', $USER_SQ);
			$database->bind(':MEASUREMENT_TYPE', $MEASUREMENT_TYPE);
			$database->bind(':DONE', $DONE);
	
			$database->execute();
			$rows = $database->fetchAll();
			error_log('rowCount='.$database->rowCount());
					
			if ($database->rowCount() == 0) {
				$note = 'GetPoseData 실패';
			}
			else {
				$note = 'GetPoseData 성공';
			}
			
			$ip = getClientIPv4();
			$user_seq = 0;
					
			insert_Log_Person($user_seq, $ip, 0, $action, $note, $database);
			exit(json_encode($rows));
			break;

	case "SetMeasure": // 측정 완료

		$USER_SQ = getAnyParameter("USER_SQ","");
		$DEVICE_SQ = getAnyParameter("DEVICE_SQ",0);
		$MEATUREMENT_TYPE = getAnyParameter("MEASUREMENT_TYPE","");
		$DONE = getAnyParameter("DONE",0);
				
		$action = 'SetMeasure';
		//$pwd_encrypted = getAnyParameter("pwd_encrypted",0);
		
		$database->prepare("
			INSERT bs_measurement (USER_SQ,DEVICE_SQ,MEASUREMENT_TYPE, DONE , REG_DT)
			values (:USER_SQ , :DEVICE_SQ,:MEASUREMENT_TYPE,:DONE , now())
		");
		$database->bind(':USER_SQ', $USER_SQ);
		$database->bind(':DEVICE_SQ', $DEVICE_SQ);
		$database->bind(':MEASUREMENT_TYPE', $MEATUREMENT_TYPE);
		$database->bind(':DONE', $DONE);
		
		$database->execute();
		error_log('rowCount='.$database->rowCount());
			
		if ($database->rowCount() == 0) {
			$note = 'data 실패';
			$response_array["result"] = 'fail';
			$response_array["MEASUREMENT_SQ"] = 0;	
		}
		else {
			$note = 'data 성공';	
			$database->prepare("
				select LAST_INSERT_ID() MEASUREMENT_SQ
			");
			$database->execute();
			$row = $database->fetch();
			$MEASUREMENT_SQ = $row["MEASUREMENT_SQ"];
			$response_array["result"] = 'success';
			$response_array["MEASUREMENT_SQ"] = $MEASUREMENT_SQ;	
		}
				
		$ip = getClientIPv4();
		$user_seq = 0;
				
		insert_Log_Person($user_seq, $ip, $MEASUREMENT_SQ, $action, $note, $database);
		exit(json_encode($response_array));
		
		break;
	
	case "SetMeasureUpdate": // 측정 완료
			$MEASUREMENT_SQ = getAnyParameter("MEASUREMENT_SQ",0);

			$action = 'SetMeasureUpdate';
			//$pwd_encrypted = getAnyParameter("pwd_encrypted",0);
			
			$database->prepare("
			UPDATE `bs_measurement` SET `DONE`=1,`REG_DT`=now()
			WHERE MEASUREMENT_SQ = :MEASUREMENT_SQ
				");
				$database->bind(':MEASUREMENT_SQ', $MEASUREMENT_SQ);
			
				$database->execute();
				error_log('rowCount='.$database->rowCount());
				
				if ($database->rowCount() == 0) {
					$note = 'data update실패';
				}
				else {
					$note = 'data update 성공';	
				}
				
				$ip = getClientIPv4();
				$user_seq = 0;
				
				insert_Log_Person($user_seq, $ip, $MEASUREMENT_SQ, $action, $note, $database);
				exit(json_encode($note));
		
				break;
	case "SetPoseJoint": // pose Joint data insert

			$POINT_SQ = getAnyParameter("POINT_SQ","");
			$MEASUREMENT_SQ = getAnyParameter("MEASUREMENT_SQ","");
			$DIRECTION = getAnyParameter("DIRECTION","");
			$POS_X = getAnyParameter("POS_X","");
			$POS_Y = getAnyParameter("POS_Y","");
			$POS_Z = getAnyParameter("POS_Z","");
			
			$action = 'SetPoseJoint';
			//$pwd_encrypted = getAnyParameter("pwd_encrypted",0);
	
			$database->prepare("
				INSERT bs_posedata_raw (POINT_SQ,MEASUREMENT_SQ,DIRECTION, POS_X , POS_Y,POS_Z , REG_DT)
				values (:POINT_SQ , :MEASUREMENT_SQ,:DIRECTION,:POS_X , :POS_Y, :POS_Z , now())
			");
			$database->bind(':POINT_SQ', $POINT_SQ);
			$database->bind(':MEASUREMENT_SQ', $MEASUREMENT_SQ);
			$database->bind(':DIRECTION', $DIRECTION);
			$database->bind(':POS_X', $POS_X);
			$database->bind(':POS_Y', $POS_Y);
			$database->bind(':POS_Z', $POS_Z);
			
			$database->execute();
			error_log('rowCount='.$database->rowCount());
			
			if ($database->rowCount() == 0) {
				$note = 'pose joint data 실패';
			}
			else {
				$note = 'pose joint data 성공';
			}
	
			$ip = getClientIPv4();
			$user_seq = 0;
			
			insert_Log_Person($user_seq, $ip, 0, $action, $note, $database);
			exit(json_encode($note));
	
			break;


	case "SetPoseAngle": // pose Joint data insert

			$MEASUREMENT_SQ = getAnyParameter("MEASUREMENT_SQ","");
			$POSESTADARD_SQ = getAnyParameter("POSESTADARD_SQ","");
			$DIRECTION = getAnyParameter("DIRECTION","");
			$ANGLE = getAnyParameter("ANGLE","");

			$action = 'SetPoseAngle';
			//$pwd_encrypted = getAnyParameter("pwd_encrypted",0);
			$database->prepare("
			INSERT INTO `bs_posedata`(`MEASUREMENT_SQ`, `POSESTANDARD_SQ`, `DIRECTION`, `ANGLE`, `REG_DT`) 
				VALUES (:MEASUREMENT_SQ,:POSESTADARD_SQ ,:DIRECTION,:ANGLE,now())
			");
			$database->bind(':MEASUREMENT_SQ', $MEASUREMENT_SQ);
			$database->bind(':POSESTADARD_SQ', $POSESTADARD_SQ);
			$database->bind(':DIRECTION', $DIRECTION);
			$database->bind(':ANGLE', $ANGLE);

			$database->execute();
			error_log('rowCount='.$database->rowCount());
			
			if ($database->rowCount() == 0) {
				$note = 'POSE ANGLE Fail';
			}
			else {
				$note = 'POSE ANGLE Success';
			}
	
			$ip = getClientIPv4();
			$user_seq = 0;
			
			insert_Log_Person($user_seq, $ip, 0, $action, $note, $database);
			exit(json_encode($note));
	
			break;

	case "SetROMAngle": 
			$MEASUREMENT_SQ = getAnyParameter("MEASUREMENT_SQ","");
			$ROMSTANDARD_SQ	= getAnyParameter("ROMSTANDARD_SQ","");
			$ANGLE = getAnyParameter("ANGLE","");
			$ROM_GRADE = getAnyParameter("ROM_GRADE","");

			$action = 'SetROMAngle';
			//$pwd_encrypted = getAnyParameter("pwd_encrypted",0);
			$database->prepare("
				INSERT bs_romdata (MEASUREMENT_SQ,ROMSTANDARD_SQ,ANGLE,ROM_GRADE,REG_DT)
				values (:MEASUREMENT_SQ,:ROMSTANDARD_SQ,:ANGLE,:ROM_GRADE,now())
			");
			
			$database->bind(':MEASUREMENT_SQ', $MEASUREMENT_SQ);
			$database->bind(':ROMSTANDARD_SQ', $ROMSTANDARD_SQ);
			$database->bind(':ANGLE', $ANGLE);
			$database->bind(':ROM_GRADE', $ROM_GRADE);
			$database->execute();
			error_log('rowCount='.$database->rowCount());
			
			if ($database->rowCount() == 0) {
				$note = 'ROM ANGLE 실패';
			}
			else {
				$note = 'ROM ANGLE 성공';
			}
	
			$ip = getClientIPv4();
			$user_seq = 0;
			
			insert_Log_Person($user_seq, $ip, 0, $action, $note, $database);
			exit(json_encode($note));
		break;
	
	
	
			// case 'GetromCheckData': // 자세측정 체크 기본 데이터 취득
	// 	//$user_seq = getAnyParameter("user_seq",0);
	// 	$DEVICE_SQ = getAnyParameter("DEVICE_SQ",0);
		
	// 	$action = 'GetromCheckData';

	// 	//  최종 장비 데이터 취득
	// 	$database->prepare("
	// 		SELECT ROMCHECK_SQ,ROMTYPE_NM,DIRECTION_TYPE,FROM_JOINT,TO_JOINT,CENTER_JOINT,CHECK_ORDER,ISUSE
	// 		FROM bs_romcheck where ISUSE=1
	// 		order by CHECK_ORDER asc
	// 	");
	// 	$database->execute();
	// 	$rows = $database->fetchAll();
		
	// 	if ($database->rowCount() == 0) {
	// 		$note = 'ROM 데이터 취득 실패';
	// 	} else {
	// 		$note = 'ROM 데이터 취득 성공';
	// 	}

	// 	$ip = getClientIPv4();
	// 	$user_seq = 0;

	// 	insert_Log_Person($user_seq, $ip, $DEVICE_SQ, $action, $note, $database);
	// 	exit(json_encode($rows));

	// 	break;

	// case 'GetfmsCheckData': // 자세측정 체크 기본 데이터 취득
	// 	//$user_seq = getAnyParameter("user_seq",0);
	// 	$DEVICE_SQ = getAnyParameter("DEVICE_SQ",0);
		
	// 	$action = 'GetfmsCheckData';

	// 	//  최종 장비 데이터 취득
	// 	$database->prepare("
	// 		SELECT FMSCHECK_SQ,FMSCHECK_NM
	// 		FROM bs_fmscheck 
	// 	");
	// 	$database->execute();
	// 	$rows = $database->fetchAll();
		
	// 	if ($database->rowCount() == 0) {
	// 		$note = 'FMS 데이터 취득 실패';
	// 	} else {
	// 		$note = 'FMS 데이터 취득 성공';
	// 	}

	// 	$ip = getClientIPv4();
	// 	$user_seq = 0;

	// 	insert_Log_Person($user_seq, $ip, $DEVICE_SQ, $action, $note, $database);
	// 	exit(json_encode($rows));

	// 	break;
	
	
	//ETC
	case 'device_login': // 로그인
		$userId = getAnyParameter("userId",0);
		$password = getAnyParameter("password",0);
		$Device_Seq = getAnyParameter("Device_Seq",0);
		$action = 'login';
		//$pwd_encrypted = getAnyParameter("pwd_encrypted",0);

		$database->prepare("
			SELECT user_seq,password,userName,birthDate,phoneNum,email,userHeight,userWeight,center_seq,grade FROM user
			WHERE userName = :userName AND password = :password AND (useyn='Y')
		");
		$database->bind(':userName', $userName);
		$database->bind(':password', $password);
		$database->execute();
		$row = $database->fetch();
		if (!$row) {
			$note = '로그인실패';
		} else {
			$note = '로그인성공';
			$user_seq = $row["user_seq"];
		}

		$ip = getClientIPv4();

		insert_Log_Person($user_seq, $ip, $Device_Seq, $action, $note, $database);
		exit(json_encode($row));

		break;

	case 'device_logout': // 로그아웃

		$user_seq = getAnyParameter("user_seq",0);
		$Device_Seq = getAnyParameter("Device_Seq",0);
		$action = 'logout';

		$ip = getClientIPv4();
		$t_ip = "";
		insert_Log_Person($user_seq, $ip, $Device_Seq, $action, $note, $database);

		exit(json_encode($action));

		break;

	case 'user_logout': // 로그아웃

		$regid = $session->user["id"];
		$ip = getClientIPv4();
		$t_ip = "";
		insert_Log_Person($regid, $ip, $t_ip, "로그아웃", $database);

		$session->destroy();
		$redirect_location = 'login.php'; // 로그아웃 이후 이동화면
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

