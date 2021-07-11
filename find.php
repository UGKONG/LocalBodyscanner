<!DOCTYPE html>
<html lang="ko" style="overflow:auto">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>닥터케어유니온 - 아이디/비밀번호 찾기</title>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/find.css">
    <script src="js/jquery-3.5.1.min.js"></script>
    <script src="js/find.js"></script>
</head>
<body style="overflow:auto">
    <h1><a href="login.php">Dr.Care Union</a></h1>

    <form action="#" method="post" name="findIDFrm" class="findIDFrm" id="findIDFrm" autocomplete="off">
        <h2>아이디 찾기</h2>
        <div class="name">
            <label for="FIND_ID_NAME">이름</label>
            <p>
                <input type="text" id="FIND_ID_NAME" placeholder="이름을 입력하세요" required>
            </p>
        </div>
        <div class="email">
            <label for="FIND_ID_EMAIL">이메일</label>
            <p>
                <input type="email" id="FIND_ID_EMAIL" placeholder="이메일을 입력하세요" required>
            </p>
            <!-- <p>
                <select name="FIND_ID_BIRTHDAY1" id="FIND_ID_BIRTHDAY1"></select>
                <label for="FIND_ID_BIRTHDAY1">년</label>
                <select name="FIND_ID_BIRTHDAY2" id="FIND_ID_BIRTHDAY2"></select>
                <label for="FIND_ID_BIRTHDAY2">월</label>
                <select name="FIND_ID_BIRTHDAY3" id="FIND_ID_BIRTHDAY3"></select>
                <label for="FIND_ID_BIRTHDAY3">일</label>
            </p> -->
        </div>
        
        <div class="btnSet">
            <button id="FIND_ID">찾 기</button>
        </div>
    </form>

    <form action="#" method="post" name="findPWFrm" class="findPWFrm" id="findPWFrm" autocomplete="off">
        <h2>비밀번호 초기화</h2>

        <div class="email">
            <label for="FIND_PW_EMAIL">이메일</label>
            <p>
                <input type="email" id="FIND_PW_EMAIL" placeholder="이메일을 입력하세요" required>
            </p>
            <!-- <p>
                <select name="FIND_PW_BIRTHDAY1" id="FIND_PW_BIRTHDAY1"></select>
                <label for="FIND_PW_BIRTHDAY1">년</label>
                <select name="FIND_PW_BIRTHDAY2" id="FIND_PW_BIRTHDAY2"></select>
                <label for="FIND_PW_BIRTHDAY2">월</label>
                <select name="FIND_PW_BIRTHDAY3" id="FIND_PW_BIRTHDAY3"></select>
                <label for="FIND_PW_BIRTHDAY3">일</label>
            </p> -->
        </div>
        <div class="id">
            <label for="FIND_PW_ID">아이디</label>
            <p>
                <input type="text" id="FIND_PW_ID" placeholder="아이디를 입력하세요" required>
            </p>
        </div>
        <div class="name">
            <label for="FIND_PW_NAME">이름</label>
            <p>
                <input type="text" id="FIND_PW_NAME" placeholder="이름을 입력하세요" required>
            </p>
        </div>
        <div class="phone">
            <label for="FIND_PHONE1">연락처</label>
            <p>
                <select name="FIND_PHONE1" id="FIND_PHONE1">
                    <option value="010">010</option>
                    <option value="011">011</option>
                    <option value="016">016</option>
                    <option value="017">017</option>
                    <option value="019">019</option>
                </select>
                <label for="FIND_PHONE2">-</label>
                <input type="text" name="FIND_PHONE2" id="FIND_PHONE2" placeholder="OOOO" required>
                <label for="FIND_PHONE3">-</label>
                <input type="text" name="FIND_PHONE3" id="FIND_PHONE3" placeholder="OOOO" required>
            </p>
        </div>
        
        <div class="btnSet">
            <button id="FIND_PW">비밀번호 초기화</button>
        </div>
    </form>
    <p class="go_login">
        <button class="go_login" onclick="location.href='login.php'">로그인 페이지로</button>
    </p>
    <address>Copyright © Liansoft. Allright Reserved. 2020</address>
</body>
</html>