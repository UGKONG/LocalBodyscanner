<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="js/jquery-3.5.1.min.js"></script>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css">
    <script src="js/login.js"></script>
	<script src="js/sha256.js"></script>
    <title>닥터케어유니온 - Login</title>
	<script type="text/javascript">
		$(document).ready(function() {
			/* var saved_id = $.cookie("saved_id");
			if (saved_id != undefined) {
				$("#id").val(saved_id);
				$("#id_save").prop("checked", true);
            } */
            
            $('body').on('selectstart, contextmenu',() => {
                return false;
            });

			function trimInput() {
				$(this).val($.trim($(this).val()));
			}
			$("#uID").change(trimInput);
			$("#uPW").change(trimInput);

			$("#login").submit(function() {
				/*if ($("#id_save").prop("checked")) {
					$.cookie("saved_id", $("#id").val(), { expires: 7 });
				} else {
					$.removeCookie("saved_id");
				}*/
				if ($("#uID").val() == "") {
					$("#uID").get(0).setCustomValidity("아이디를 입력해주십시오.");
					$("#uID").focus();
					return false;
				} else {
					$("#uID").get(0).setCustomValidity("");
				}
				if ($("#uPW").val() == "") {
					$("#uPW").get(0).setCustomValidity("암호를 입력해주십시오.");
					$("#uPW").focus();
					return false;
				} else {
					$("#uPW").get(0).setCustomValidity("");
				}
				if (this.checkValidity()) {
					if ($('#uPW').val().length > 0)
						$("#pwd_encrypted").val(sha256($('#uPW').val()));
					$('#uPW').val("");
					return true;
				} else {
					return false;
				}
			});
			$("#submit").click(function() {
				$("#login").submit();
				return false;
			});

			<?php if (isset($_GET['msg']) and $_GET['msg'] == 'loginFailure') { ?>
			$("#loginerror").removeClass("hid");
			<?php } else if (isset($_GET['msg']) and $_GET['msg'] == 'loginNotFound') {?>
			$("#logintimeout").removeClass("hid");
			<?php } else if (isset($_GET['msg']) and $_GET['msg'] == 'NoPower') {?>
			$("#nopwer").removeClass("hid");
			<?php } ?>

		});
		function sha256(str) {
			var hashedPassword = CryptoJS.SHA256(str).toString() ; 
			return hashedPassword;
		}
	</script>

</head>
<body>
    
    <div class="dark_div"></div>

    <div id="container">

        <div class="left">
            <div class="content">
                <h1><a href="login.php">Dr.Care Union</a></h1>
                <h2>Sign in</h2>
                <form action="flow_controller.php?task=staff_login" method="POST" name="login" id="login" autocomplete="off">
                    <p>
                        <input type="text" id="uID" name="uID" placeholder="아이디" required>
                        <label for="uID"><i class="fas fa-user"></i></label>
                    </p>
                    <p>
                        <input type="password" id="uPW" name="uPW" placeholder="비밀번호" required>
                        <label for="uPW"><i class="fas fa-lock"></i></label>
                    </p>
					<input type="hidden" id="pwd_encrypted" name="pwd_encrypted">
					<div id="loginerror" name="loginerror" class="login-error hid">
						입력한 아이디나 비밀번호가 존재하지 않거나 사용할 수 없는 상태입니다.
					</div>
					<div id="logintimeout" name="logintimeout" class="login-error hid">
						로그인 정보가 없습니다.
					</div>
					<div id="nopwer" name="nopwer" class="login-error hid">
						권한이 없는 작업을 요청하였습니다. 다시 로그인하십시오. 
					</div>
					<p class="btnSet">
                        <button type="submit" id="loginBtn" name="loginBtn">LOGIN</button>
                        <!-- 바로 index 페이지로 넘어가게 해놨음. -->
                    </p>
                    <div class="loginText">
                        <div id="joinBtn" onclick="location.href='joinL1.php'">
                            <span>회 원 가 입</span>
                        </div>
                        <button onclick="location.href='find.php'">아이디/비밀번호 찾기</button>
                    </div>
                    <div class="addCenter">
                        <button type="button" id="addCenterBtn" name="addCenterBtn" onclick="location.href='addcenter.php'">센 터 등 록</button>
                    </div>

                    <div class="hid">
                        
                    </div>
                </form>
            </div>
        </div>

        <div class="right">
            <div class="bg_circle1">
                <div class="bg_circle2"></div>
                <div class="bg_circle3">
                    <div class="computer"></div>
                </div>
            </div>
            
        </div>
    </div>

    
    <div class="copyright">
        <div>
            <img src="img/logo_1.png" class="logo" alt="리안소프트">
            <img src="img/logo2_1.png" class="logo" alt="닥터케어">
        </div>
        <address>Copyright &copy; Liansoft. Allright Reserved. 2020</address>
    </div>


</body>
</html>