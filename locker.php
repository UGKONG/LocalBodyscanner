<!DOCTYPE html>
<?php
require_once 'lib/_init.php';

$database = new Database();

$session = new Session();
date_default_timezone_set('Asia/Seoul');
validateAdmin($session, 2);
$USER_SQ = $session->user["USER_SQ"];
    $USER_GRADE = $session->user["GRADE"];
    ?>
<script>
    var $USER_SQ = <?php echo $USER_SQ ?>;
    var $USER_GRADE = <?php echo $USER_GRADE ?>;
    var $USER_GRADE_LIST = [];
</script>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>닥터케어유니온 - 라커</title>
    <script src="js/jquery-3.5.1.min.js"></script>
    <script src="js/Array.js"></script>
    <script src="js/script.js"></script>
    <script src="js/rocker.js"></script>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/rocker.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css">
</head>
<body>
    

    <div class="mSearch_container">
        <div class="col-01">
            <label for="mSearchText">회원명 검색</label>
            <div class="x_btn">
                <span></span>
                <span></span>
            </div>
        </div>
        <input type="text" name="mSearchText" id="mSearchText" class="mSearchText" placeholder="회원명을 입력해주세요." maxlength="10">
        <ul class="name_list">
        </ul>
    </div>

    <div id="wrap">
    <?php require_once 'lib/header.php'; ?>
        <section class="content">
            <h2 class="hid">컨텐츠 영역</h2>

            <div class="top_sMenu">
                <div class="boxinfo">
                    <div id="rockerName"></div>
                    <p>
                        <span class="rect"></span>
                        <span>이용중</span>
                    </p>
                    <p>
                        <span class="rect"></span>
                        <span>이용가능</span>
                    </p>
                    <p>
                        <span class="rect"></span>
                        <span>이용불가</span>
                    </p>
                </div>
                <div class="headBtn">
                    <button id="rockerSetBtn">라커관리</button>
                    <label for="rockerType" class="hid">라커종류</label>
                    <select name="rockerType" id="rockerType"></select>
                </div>
            </div>


            <div id="rockerList">
                <!-- 라커 리스트 -->
            </div>
        </section>

    </div>


    
    <form class="popup" id="pop_set" action="#" method="POST" autocomplete="off" style="display: none;">
        <h2 class="title"><span>라커관리</span>
            <button class="closeBtn">
                <div>
                    <span></span>
                    <span></span>
                </div>
            </button>
        </h2>
        <div class="content">
            <p>
                <select>
                    <!-- js에서 Load -->
                </select>
                <input type="text" id="add_rockerSet_name" class="rockerSet_name" placeholder="라커 이름">
                <input type="text" id="add_rockerSet_count" class="rockerSet_count" placeholder="라커 수량">
                <button id="rockerList_add">추가</button>
            </p>
            <ul>
                <!-- js에서 Load -->
            </ul>

            <article class="btn">
                <h3 class="hid">버튼 정보</h3>
                <button type="submit" class="submit">수 정</button>
                <button type="reset" class="close" onclick="$('.dark_div').add($('#pop_set')).fadeOut(200)">닫 기</button>
            </article>
        </div>
    </form>


    <form class="pop_add" action="#" method="POST" autocomplete="off" style="display: none;">
        <h2>No.<span id="pop_rockerNum"></span> 배정</h2>

        <section class="pop_content">
            <div class="pop_rockerAttr">
                <h3>라커 상태변경</h3>
                <div class="choice">
                    <button type="button" class="active">이용가능</button>
                    <button type="button" class="impossibleBtn">이용불가</button>
                </div>
            </div>

            <div class="rowDate">
                <h3>기간설정</h3>
                <div>
                    <label for="pop_rockerStartDate" class="hid">라커 기간 시작</label>
                    <input type="date" name="pop_rockerStartDate" id="pop_rockerStartDate" required>
                    -
                    <label for="pop_rockerEndDate" class="hid">라커 기간 종료</label>
                    <input type="date" name="pop_rockerEndDate" id="pop_rockerEndDate">
                    <br>
                    <button type="button" class="add1m">1개월</button>
                    <button type="button" class="add3m">3개월</button>
                    <button type="button" class="add6m">6개월</button>
                    <button type="button" class="add12m">12개월</button>
                    <button type="button" class="add24m">24개월</button>
                </div>
            </div>

            <div>
                <h3>등록회원</h3>
                <div>
                    <button type="button" id="pop_searchMember">회원검색</button>
                    <span class="notMember_use" title="비회원의 정보를 입력하시려면 클릭해주세요.">비회원이용</span>
                </div>
            </div>

            <div class="pop_memberInfo">
                <h3>회원정보</h3>
                <div>
                    <p>
                        <label for="pop_MemberName">성명</label>
                        <input type="text" id="pop_MemberName" name="pop_MemberName" readonly required>
                    </p>
                    <p>
                        <label for="pop_MemberNum">연락처</label>
                        <input type="text" id="pop_MemberNum" name="pop_MemberNum" readonly required>
                    </p>
                </div>
            </div>

            <div class="btn">
                <button type="button" class="submit">배정하기</button>
                <button type="reset" class="close">닫기</button>
            </div>
        </section>

    </form>

    <div class="pop_info" style="display: none;">
        <h2>No.<span id="pop_rockerNum2"></span> - 라커 사용자 정보</h2>
        <section class="pop_content">
            <div class="left">
                <img class="pop_img" src="img/user.png" alt="유저 이미지">
            </div>
            <div class="right">
                <p class="name"></p>
                <p class="num"></p>
                <p class="date">
                    <label for="info_rockerStartDate">시작일</label>
                    <input type="date" name="info_rockerStartDate" id="info_rockerStartDate" readonly>
                    <br>
                    <label for="info_rockerEndDate">종료일</label>
                    <input type="date" name="info_rockerEndDate" id="info_rockerEndDate" readonly>
                </p>
            </div>
            <div class="btn">
                <button class="info_dateEdit">이용기간 변경</button>
                <button class="info_dateEdit_ok" style="display: none;">변 경</button>
                <button class="info_unuse">이용 해제</button>
                <button class="info_close">닫 기</button>
            </div>
        </section>
    </div>
<?php require_once 'lib/footer.php'; ?>  
    <div class="dark_div"></div>
</body>
</html>