<!DOCTYPE html>
<html lang="ko" style="overflow: auto;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>닥터케어유니온 - 회원가입</title>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/joinL1.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css">
    <script src="js/jquery-3.5.1.min.js"></script>
    <script src="js/joinL1.js"></script>
</head>
<body>
    <h1>
        <a href="login.php">Dr.Care Union</a>
        <p>관리자 이용약관 / 개인정보 수집 및 이용</p>
    </h1>

    <nav>
        <ul>
            <li class="active">01. 약관동의</li>
            <li>02. 본인확인</li>
            <li>03. 정보입력</li>
            <li>04. 회원가입 완료</li>
        </ul>
    </nav>

    <form action="#" method="POST" name="termsFrm" autocomplete="off">
        <div>
            <label for="termsText1">닥터케어유니온 이용약관</label>
            <textarea name="termsText1" id="termsText1" class="termsText" readonly></textarea>
            <div>
                <input type="checkbox" name="terms1Check" id="terms1Check" class="termsCheck">
                <label for="terms1Check">이용약관에 동의합니다.</label>
            </div>
        </div>
        <div>
            <label for="termsText2">닥터케어유니온 개인정보동의</label>
            <textarea name="termsText2" id="termsText2" class="termsText" readonly></textarea>
            <div>
                <input type="checkbox" name="terms2Check" id="terms2Check" class="termsCheck">
                <label for="terms2Check">개인정보수집 및 이용에 동의합니다.</label>
            </div>
        </div>
        <div>
            <label for="termsText3">개인정보수집 제3자 동의</label>
            <textarea name="termsText3" id="termsText3" class="termsText" readonly></textarea>
            <div>
                <input type="checkbox" name="terms3Check" id="terms3Check" class="termsCheck">
                <label for="terms3Check">개인정보수집 및 이용에 동의합니다.</label>
            </div>
        </div>
        <p>
            <input type="checkbox" name="termsAllCheck" id="termsAllCheck">
            <label for="termsAllCheck">위 사항을 전제 동의합니다.</label>
        </p>
        <div class="btnSet">
            <button id="termsNo" type="button">취 소</button>
            <button id="termsYes" type="button">확 인</button>
        </div>
    </form>
    <div class="termsTemp1 hid"></div>
    <div class="termsTemp2 hid"></div>
</body>
</html>