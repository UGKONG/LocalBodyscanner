<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>닥터케어유니온 - 센터등록</title>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/addcenter.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css">
    <script src="js/jquery-3.5.1.min.js"></script>
    <script src="js/addcenter.js"></script>
</head>
<body>
    
    <h1>
        <a href="login.php">Dr.Care Union</a>
        <p>센터등록</p>
    </h1>

    <form action="#" method="POST" name="centerFrm" id="centerFrm" autocomplete="off">
        <div class="CENTER_ADMIN_ID_div" style="/*height: 135px;*/" >
            <label for="CENTER_ADMIN_ID">로그인 계정 생성</label>
            <p>
                <input type="text" name="CENTER_ADMIN_ID" id="CENTER_ADMIN_ID" required style="width: 190px;">
                <label for="CENTER_ADMIN_DOMAIN" class="sLabel"> @ </label>
                <input type="text" name="CENTER_ADMIN_DOMAIN" id="CENTER_ADMIN_DOMAIN" required style="width: 240px;">
                <label for="CENTER_ADMIN_DOMAIN_CHOICE" class="hid">도메인선택</label>
                <select name="CENTER_ADMIN_DOMAIN_CHOICE" id="CENTER_ADMIN_DOMAIN_CHOICE">
                    <option value="">직접입력</option>
                    <option value="gmail.com">구글</option>
                    <option value="hanmail.net">한메일</option>
                    <option value="hotmail.com">핫메일</option>
                    <option value="kakao.com">카카오</option>
                    <option value="korea.com">코리아</option>
                    <option value="nate.com">네이트</option>
                    <option value="naver.com">네이버</option>
                    <option value="paran.com">파란</option>
                </select>
                <!-- <button id="sendNumBtn" type="button">인증번호 요청</button> -->
            </p>
            <!-- <p style="justify-content: flex-start; margin-top: 16px;">
                <label for="sendNum" class="hid">인증번호입력</label>
                <input type="text" name="sendNum" id="sendNum" maxlength="6" placeholder="인증번호 입력" required>
                <button type="button" id="sendNumOKBtn" class="">인증번호 확인</button>
            </p> -->
        </div>
        <div class="CENTER_ADMIN_PW_div1">
            <label for="CENTER_ADMIN_PW1">로그인 비밀번호</label>
            <p>
                <input type="password" name="CENTER_ADMIN_PW1" id="CENTER_ADMIN_PW1" placeholder="영문과 숫자로 6자리 이상" required>
                <i class="far fa-check-circle"></i>
            </p>
        </div>
        <div class="CENTER_ADMIN_PW_div2">
            <label for="CENTER_ADMIN_PW2">로그인 비밀번호 확인</label>
            <p>
                <input type="password" name="CENTER_ADMIN_PW2" id="CENTER_ADMIN_PW2" required>
                <i class="far fa-check-circle"></i>
            </p>
        </div>

        <div class="CONPANY_div" style="height: 196px;">
            <label for="COMPANY_NAME">사업자 정보</label>
            <p>
                <label for="COMPANY_TYPE" class="hid">센터종류</label>
                <select name="COMPANY_TYPE" id="COMPANY_TYPE" required style="width: 170px;">
                    <option value="">사업자 종류</option>
                    <option value="s_TYPE">개인사업자</option>
                    <option value="c_TYPE">법인사업자</option>
                    <option value="n_TYPE">미사업자</option>
                </select>
                <input type="text" name="COMPANY_NAME" id="COMPANY_NAME" maxlength="20" placeholder="사업자명을 입력하세요" required style="width: 420px;">
            </p>
            <p>
                <input type="text" name="COMPANY_SEQ" id="COMPANY_SEQ" placeholder="사업자 등록번호" required style="width: 328px;">
                <input type="text" name="CENTER_KINGNAME" id="CENTER_KINGNAME" required style="width:260px;" placeholder="대표자명">
            </p>
            <p style="justify-content: flex-start;">
                <select name="CENTER_KINGPHONE1" id="CENTER_KINGPHONE1" required>
                    <option value="">연락처</option>
                    <option value="010">010</option>
                    <option value="010">011</option>
                    <option value="010">016</option>
                    <option value="010">017</option>
                    <option value="010">019</option>
                </select>
                <label for="CENTER_KINGPHONE2" class="sLabel"> - </label>
                <input type="text" name="CENTER_KINGPHONE2" id="CENTER_KINGPHONE2" maxlength="4" required>
                <label for="CENTER_KINGPHONE3" class="sLabel"> - </label>
                <input type="text" name="CENTER_KINGPHONE3" id="CENTER_KINGPHONE3" maxlength="4" required>
                <!-- <button id="sendNumBtn" type="button">인증번호 요청</button> -->
            </p>
        </div>

        <div class="CENTER_NAME_div">
            <label for="CENTER_NAME">센터 정보</label>
            <p>
                <input type="text" name="CENTER_NAME" id="CENTER_NAME" maxlength="20" placeholder="센터명을 입력하세요" required>
            </p>
        </div>

        <!-- <div class="CENTER_KINGPHONE_div">
            <label for="CENTER_KINGPHONE1">연락처</label>
            <p>
                <select name="CENTER_KINGPHONE1" id="CENTER_KINGPHONE1" required>
                    <option value="">선택</option>
                    <option value="010">010</option>
                    <option value="010">011</option>
                    <option value="010">016</option>
                    <option value="010">017</option>
                    <option value="010">019</option>
                </select>
                <label for="CENTER_KINGPHONE2" class="sLabel"> - </label>
                <input type="text" name="CENTER_KINGPHONE2" id="CENTER_KINGPHONE2" maxlength="4" required>
                <label for="CENTER_KINGPHONE3" class="sLabel"> - </label>
                <input type="text" name="CENTER_KINGPHONE3" id="CENTER_KINGPHONE3" maxlength="4" required>
                <button id="sendNumBtn" type="button">인증번호 요청</button>
            </p>
        </div> -->
        <div class="btnSet">
            <button type="submit" id="submitBtn">등 록</button>
            <button type="button" id="cancelBtn" onclick="location.href='login.php'">취 소</button>
        </div>


    </form>
</body>
</html>