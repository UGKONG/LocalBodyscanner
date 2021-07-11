<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>닥터케어유니온 - 회원가입</title>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/joinL3.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css">
    <script src="js/jquery-3.5.1.min.js"></script>
    <script src="js/jswLib.js"></script>
    <script src="js/joinL3.js"></script>
</head>
<body style="height: unset; padding-bottom: 100px;">
    <h1>
        <a href="login.php">Dr.Care Union</a>
        <p>회원가입</p>
    </h1>

    <nav>
        <ul>
            <li>01. 약관동의</li>
            <li>02. 본인확인</li>
            <li class="active">03. 정보입력</li>
            <li>04. 회원가입 완료</li>
        </ul>
    </nav>

    <form action="#" method="POST" name="joinFrm" autocomplete="off" id="joinFrm">
        <div class="joinTime hid">
            <h3>등록일</h3>
            <p>
                <span id="date_time">날짜와 시간</span>
            </p>
        </div>
        <h2>필수입력</h2>
        
        <div class="name">
            <label for="JOIN_NAME">이 름</label>
            <p>
                <input type="text" name="JOIN_NAME" id="JOIN_NAME" maxlength="10" placeholder="이름을 입력하세요" required>
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
        <div class="id">
            <label for="JOIN_ID">이메일</label>
            <p>
                <input type="email" name="JOIN_ID" id="JOIN_ID" placeholder="이메일계정을 입력하세요" maxlength="30" required style="width:100%">
                <!-- <button type="button" id="id_overlapping_Btn">중복확인</button> -->
            </p>
        </div>
        <span class="alert" id="id_alert"><span></span>이메일계정을 입력해주세요.</span>
        <div class="pw1">
            <label for="JOIN_PW1">비밀번호</label>
            <p>
                <input type="password" name="JOIN_PW1" id="JOIN_PW1" maxlength="20" readonly required placeholder="비밀번호는 휴대폰 뒷4자리로 자동으로 설정됩니다.">
            </p>
        </div>
        <p style="padding-left: 150px; font-size:14px; color:#ff6060">비밀번호는 연락처 뒷4자리로 자동생성됩니다.</p>
        <!-- <div class="pw2">
            <label for="JOIN_PW2">비밀번호 확인</label>
            <p>
                <input type="password" name="JOIN_PW2" id="JOIN_PW2" placeholder="비밀번호를 다시 입력하세요" maxlength="20" required>
            </p>
        </div> -->
        <span class="alert" id="pw_alert"><span></span>비밀번호는 휴대폰 뒷4자리로 자동으로 설정됩니다.</span>
        <div class="gender">
            <h3>성 별</h3>
            <p>
                <span>
                    <input type="radio" id="male" name="gender" checked>
                    <label for="male">남자</label>
                </span>
                <span>
                    <input type="radio" id="female" name="gender">
                    <label for="female">여자</label>
                </span>
            </p>
        </div>
        <h2 class="Infomation">센터정보</h2>
        <div class="centerName" style="align-items: flex-start;height: auto;">
            <label for="CENTER_NAME" style="padding-top: 5px;">센터명</label>
            <div>
              <ul id="CENTER_NAME" class="">
                <li class="centerLabel">* 센터선택</li>
                <li><input type="text" id="centerSearch" placeholder="센터명 검색"></li>
                <li class="centerList">
                  <ul>
                    <!-- js -->
                  </ul>
                </li>
              </ul>

                <!-- <select name="CENTER_NAME" id="CENTER_NAME">
                    <option value="">선택하세요</option>
                    <option value="centerName1">센터명1</option>
                    <option value="centerName2">센터명2</option>
                    <option value="centerName3">센터명3</option>
                </select> -->
            </div>
        </div>
        <div class="center_alert hid">
            <h3></h3>
            <p>센터명이 뜨지 않으시면 <a href="addcenter.php" target="_blank">여기</a>를 눌러주세요.</p>
        </div>  

        <h2 class="choice_input">선택입력<i class="fas fa-chevron-down on"></i></h2>
        <section class="choice_input_section" style="display: none;">
            <h3 class="hid">선택입력 컨텐츠</h3>
            <div class="year">
                <label for="JOIN_YEAR">생년월일</label>
                <p>
                    <select name="JOIN_YEAR" id="JOIN_YEAR"></select>
                    <label for="JOIN_YEAR">년</label> 
                    
                    <select name="JOIN_MONTH" id="JOIN_MONTH"></select>
                    <label for="JOIN_MONTH">월</label> 

                    <input type="text" name="JOIN_DAY" id="JOIN_DAY">
                    <label for="JOIN_DAY">일</label> 
                </p>
            </div>
            <div class="address">
                <label for="JOIN_ADDRESS1">주 소</label>
                <p>
                    <label for="JOIN_ADDRESS4" class="hid">주소</label>
                    <input type="text" name="JOIN_ADDRESS4" id="JOIN_ADDRESS4" placeholder="주소">
                </p>
            </div>
        </section>

        <div class="join_btn">
            <button type="button" id="JOIN_submitBtn">완 료</button>
            <button type="button" id="JOIN_cancelBtn">취 소</button>
        </div>

    </form>
    <address>Copyright © Liansoft. Allright Reserved. 2020</address>
</body>
</html>