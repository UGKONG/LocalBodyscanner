<!DOCTYPE html>
<html lang="ko" style="overflow:auto">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>닥터케어유니온 - 회원가입</title>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/joinL2.css">
    <script src="js/jquery-3.5.1.min.js"></script>
    <script src="js/joinL2.js"></script>
</head>
<body>
    <h1>
        <a href="login.php">Dr.Care Union</a>
        <p>회원가입</p>
    </h1>

    <nav>
        <ul>
            <li>01. 약관동의</li>
            <li class="active">02. 중복확인</li>
            <li>03. 정보입력</li>
            <li>04. 회원가입 완료</li>
        </ul>
    </nav>

    <form action="#" method="POST" name="joinFrm" autocomplete="off" id="joinFrm">
        <div class="name">
            <label for="JOIN_NAME">이 름</label>
            <p>
                <input type="text" name="JOIN_NAME" id="JOIN_NAME" maxlength="10" placeholder="이름을 입력하세요" required>
                <button type="button" id="User_overlapping_Btn">중복확인</button>
            </p>
        </div>
        <div class="phone">
            <label for="JOIN_PHONE1">연락처</label>
            <p>
                <label for="JOIN_PHONE0" class="hid">휴대폰번호 앞자리</label>
                <select name="JOIN_PHONE0" id="JOIN_PHONE0">
                    <option value="010">010</option>
                    <option value="011">011</option>
                    <option value="016">016</option>
                    <option value="017">017</option>
                    <option value="019">019</option>
                </select> -
                <label for="JOIN_PHONE1" class="hid">휴대폰번호 중간자리</label>
                <input type="text" name="JOIN_PHONE1" id="JOIN_PHONE1" placeholder="" maxlength="4" required> -
                <label for="JOIN_PHONE2" class="hid">휴대폰번호 끝자리</label>
                <input type="text" name="JOIN_PHONE2" id="JOIN_PHONE2" placeholder="" maxlength="4" required>
            </p>
        </div>
        
        <div class="join_btn">
            <button type="button" id="JOIN_submitBtn">다 음</button>
            <button type="button" id="JOIN_cancelBtn">취 소</button>
        </div>

    </form>
    <address>Copyright © Liansoft. Allright Reserved. 2020</address>
</body>
</html>