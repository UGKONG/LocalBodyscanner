<!doctype html>
<html lang="ko">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE = edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1">	
	<title></title>
		<link rel="stylesheet" href="css/common.css">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
		<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
		<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
		<script src="js/sha256.js"></script>

		<script type="text/javascript">
			$(document).ready(function() {
				/* var saved_id = $.cookie("saved_id");
				if (saved_id != undefined) {
					$("#id").val(saved_id);
					$("#id_save").prop("checked", true);
				} */
				function trimInput() {
					$(this).val($.trim($(this).val()));
				}
				$("#userid").change(trimInput);
				$("#password").change(trimInput);
				
				$("#login_user_form").submit(function() {
					/*if ($("#id_save").prop("checked")) {
						$.cookie("saved_id", $("#id").val(), { expires: 7 });
					} else {
						$.removeCookie("saved_id");
					}*/
					if ($("#userid").val() == "") {
						$("#userid").get(0).setCustomValidity("아이디를 입력해주십시오.");
						$("#userid").focus();
						return false;
					} else {
						$("#userid").get(0).setCustomValidity("");
					}
					if ($("#password").val() == "") {
						$("#password").get(0).setCustomValidity("암호를 입력해주십시오.");
						$("#password").focus();
						return false;
					} else {
						$("#password").get(0).setCustomValidity("");
					}
					if (this.checkValidity()) {
						if ($('#password').val().length > 0)
							$("#pwd_encrypted").val(sha256($('#password').val()));
						$('#password').val("");
						return true;
					} else {
						return false;
					}
				});
				$("#submit").click(function() {
					$("#login_user_form").submit();
					return false;
				});

				<?php if (isset($_GET['msg']) and $_GET['msg'] == 'loginFailure') { ?>
				$("#loginerror").removeClass("hide");
				<?php } else if (isset($_GET['msg']) and $_GET['msg'] == 'loginNotFound') {?>
				$("#logintimeout").removeClass("hide");
				<?php } else if (isset($_GET['msg']) and $_GET['msg'] == 'NoPower') {?>
				$("#nopwer").removeClass("hide");
				<?php } ?>

			});
			function sha256(str) {
				var hashedPassword = CryptoJS.SHA256(str).toString() ; 
				return hashedPassword;
			}
		</script>
	</head>

<body>
	<div class="main_content">
		<div class="content">
			<h1>Web Service Test</h1>
			<div class="main_centerpart">
				<div>
					<h3>
						USER_SEARCH
					</h3>
				</div>
				<div class="form_part">
					<form class="form_part_login" action="bs_webservice.php?task=UserSearch" method="POST" name="login_user_form" id="login_user_form">
						<input type="text" class="form-control" id="PHONE_NO" name="PHONE_NO" placeholder="전화번호 ">
						<input type="text" class="form-control" id="DEVICE_SQ" name="DEVICE_SQ" placeholder="장비시퀀스 ">
						<button type="submit" id="submit" class="btn btn-primary">검색</a>
					</form>
				</div>
			</div>
			<div class="main_centerpart">
				<div>
					<h3>
						유저등록_아이디중복체크
					</h3>
				</div>
				<div class="form_part">
					<form class="form_part_login" action="bs_webservice.php?task=UserRegisterIDCheck" method="POST" name="login_user_form" id="login_user_form">
						<input type="text" class="form-control" id="USERID" name="USERID" placeholder="사용자 아이디 ">
						<input type="text" class="form-control" id="DEVICE_SQ" name="DEVICE_SQ" placeholder="장비시퀀스 ">
						<button type="submit" id="submit" class="btn btn-primary">중복 체크</a>
					</form>
				</div>
			</div>
			<div class="main_centerpart">
				<div>
					<h3>
						유저등록_간단
					</h3>
				</div>
				<div class="form_part">
					<form class="form_part_login" action="bs_webservice.php?task=UserRegisterSimple" method="POST" name="login_user_form" id="login_user_form">
						<input type="text" class="form-control" id="USERID" name="USERID" placeholder="사용자 아이디 ">
						<input type="text" class="form-control" id="PHONE_NO" name="PHONE_NO" placeholder="전화번호 ">
						<input type="text" class="form-control" id="GENDER" name="GENDER" placeholder="성별(1:남성, 2:여성) ">
						<input type="text" class="form-control" id="DEVICE_SQ" name="DEVICE_SQ" placeholder="장비시퀀스 ">
						<button type="submit" id="submit" class="btn btn-primary">등록</a>
					</form>
				</div>
			</div>
			<div class="main_centerpart">
				<div>
					<h3>
						장비 등록
					</h3>
				</div>
				<div class="form_part">
					<form class="form_part_login" action="bs_webservice.php?task=DeviceRegister" method="POST" name="login_user_form" id="login_user_form">
						<input type="text" class="form-control" id="CENTER_SQ" name="CENTER_SQ" placeholder="센터시퀀스 ">
						<input type="text" class="form-control" id="DEVICE_NM" name="DEVICE_NM" placeholder="장비명 ">
						<button type="submit" id="submit" class="btn btn-primary">등록</a>
					</form>
				</div>
			</div>
			<div class="main_centerpart">
				<div>
					<h3>
						장비 상태 등록
					</h3>
				</div>
				<div class="form_part">
					<form class="form_part_login" action="bs_webservice.php?task=DeviceStatus" method="POST" name="login_user_form" id="login_user_form">
						<input type="text" class="form-control" id="DEVICE_SQ" name="DEVICE_SQ" placeholder="장비시퀀스 ">
						<input type="text" class="form-control" id="DEVICE_ST" name="DEVICE_ST" placeholder="장비상태 (1:Stop, 2:Running)">
						<button type="submit" id="submit" class="btn btn-primary">등록</a>
					</form>
				</div>
			</div>
			<div class="main_centerpart">
				<div>
					<h3>
						Pose Check Data 취득
					</h3>
				</div>
				<div class="form_part">
					<form class="form_part_login" action="bs_webservice.php?task=GetposeCheckData" method="POST" name="login_user_form" id="login_user_form">
						<button type="submit" id="submit" class="btn btn-primary">데이터 가져오기</a>
					</form>
				</div>
			</div>
			<div class="main_centerpart">
				<div>
					<h3>
						ROM Check Data 취득
					</h3>
				</div>
				<div class="form_part">
					<form class="form_part_login" action="bs_webservice.php?task=GetromCheckData" method="POST" name="login_user_form" id="login_user_form">
						<button type="submit" id="submit" class="btn btn-primary">데이터 가져오기</a>
					</form>
				</div>
			</div>
			<div class="main_centerpart">
				<div>
					<h3>
						FMS Check Data 취득
					</h3>
				</div>
				<div class="form_part">
					<form class="form_part_login" action="bs_webservice.php?task=GetfmsCheckData" method="POST" name="login_user_form" id="login_user_form">
						<button type="submit" id="submit" class="btn btn-primary">데이터 가져오기</a>
					</form>
				</div>
			</div>
		</div>
		<footer class="main_footer">
			COPYRIGHT(C) LianSoft.ALLRIGHT RESERVED.2020
		</footer>
	</div>	
</body>
	
</html>
