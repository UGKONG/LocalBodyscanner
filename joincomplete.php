<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>닥터케어유니온 - 회원가입</title>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/joincomplete.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css">
    <script>
        

    </script>
    <script src="js/jquery-3.5.1.min.js"></script>
    <style>
        .st0{
            fill:none;
            stroke:var(--main);
            stroke-width:25;
            stroke-linecap:round;
            stroke-linejoin:round;
            stroke-miterlimit:10;
            stroke-dashoffset: 804;
            stroke-dasharray: 804;
            transition: .6s linear;
        }
        .st1{
            fill:none;
            stroke:var(--main);
            stroke-width:26;
            stroke-linecap:round;
            stroke-linejoin:round;
            stroke-miterlimit:10;
            stroke-dashoffset: 267;
            stroke-dasharray: 267;
            transition: .6s ease-out;
            transition-delay: .5s;
        }
        
    </style>
    <script>
        $(function(){
          if (!sessionStorage.join3) {
            alert('정상적인 경로로 접근해주세요.');
            location.href = 'joinL1.php';
          }
          if (sessionStorage.id == 'undifined') location.href = 'joinL1.php';
          if (sessionStorage.joinName == 'undifined') location.href = 'joinL1.php';

          var circleLine = $('.st0'), checkLine = $('.st1');
          setTimeout(() => {
              circleLine.add(checkLine).css({'stroke-dashoffset' : '0'});
          }, 1000);


          $('#completeName').text(sessionStorage.joinName);
          $('#completeID').text(sessionStorage.id);

        });
    </script>
</head>
<body>
    <h1>
        <a href="login.php">Dr.Care Union</a>
        <p>회원가입</p>
    </h1>

    <nav>
        <ul>
            <li>01. 약관동의</li>
            <li>02. 본인확인</li>
            <li>03. 정보입력</li>
            <li class="active">04. 회원가입 완료</li>
        </ul>
    </nav>

    <section id="main">
        <div class="title">
            <svg version="1.1" id="check_icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px"
                y="0px" viewBox="0 0 250 250" style="enable-background:new 0 0 250 250;" xml:space="preserve">
                <path class="st0" d="M224.86,80.46c6.54,13.98,10.2,29.59,10.2,46.04c0,60.08-48.7,108.78-108.78,108.78S17.5,186.58,17.5,126.5
                S66.2,17.72,126.28,17.72c14.99,0,29.26,3.03,42.25,8.51"/>
                <path class="st1" d="M65.56,99.04c0,0,29.33,51.56,40.3,78.81c0,0,50.96-83.85,107.56-140.74"/>
            </svg>
            <h2>닥터케어 유니온 <span>회원가입을 환영</span>합니다.</h2>
        </div>
        <div class="notify">
            <b class="completeMember_info">
                [<span id="completeName"></span>]님의 로그인 계정은 [<span id="completeID"></span>] 입니다.
            </b>
            <ul>
                <li>개인정보 변경이 필요한 경우 [내정보] 페이지에서 변경이 가능합니다.</li>
                <li>비밀번호 변경이 필요한 경우 [내정보] 페이지에서 [비밀번호 변경] 버튼을 눌러 변경이 가능합니다.</li>
                <li><b>초기 비밀번호는 휴대폰번호 뒷 4자리로 생성되고 개인정보를 위해 로그인 후 비밀번호를 <b style="color: red;">반드시 변경</b>해 주세요.</b></li>
            </ul>
        </div>
    </section>

    <div class="btnSet">
        <button type="button" onclick="location.href = 'login.php'">로그인</button>
    </div>

    </form>
    <address>Copyright © Liansoft. Allright Reserved. 2020</address>
</body>
</html>