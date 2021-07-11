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
<script src="js/jquery-3.5.1.min.js"></script>
<script>
    var $USER_SQ = <?php echo $USER_SQ ?>;
    var $USER_GRADE = <?php echo $USER_GRADE ?>;
    var $USER_GRADE_LIST = [];
    
</script>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>닥터케어유니온 - 멤버스</title>
	
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/members.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <script src="js/jquery-ui.js"></script>
    <script src="js/jswLib.js"></script>
    <script src="js/script.js"></script>
    <script src="js/members.js"></script>
    <script type="text/javascript">
		member_register = "<?=getAnyParameter("member_register","")?>";
	</script>
</head>
<body>
    <div class="dark_div"></div>
    <div id="wrap">
<?php require_once 'lib/header.php'; ?>

        <section class="content">
            <h2 class="hid">컨텐츠 영역</h2>

            <article class="up">
                <section>
                    <label for="searchBox">검색</label>
                    <input type="text" id="searchBox" name="searchBox" placeholder="회원 이름, 전화번호를 입력하여 검색">
                    <button type="button" id="searchBox_Btn" name="searchBox_Btn">통합검색</button>
                    <button type="button" id="if" name="if">조건검색 &nbsp;<i class="fas fa-caret-down"></i></button>
                    <button type="button" class="tagBtn">잔여횟수 3회 이하 회원</button>
                    <button type="button" class="tagBtn">5일 이내 기간 만료 회원</button>
                    <button type="button" class="tagBtn">이번달 내 기간 만료 회원</button>
                </section>
                <div>
                    <button id="MEAS_DATE_LIST_BTN" style="margin:0 4px">최근측정이력</button>
                    <button id="new_User">신규회원 등록</button>
                    <button id="adddate_User" style="background-color:#665acf;">기간 연장</button>
                    <button id="del_User" type="button">회원 삭제</button>
                </div>
            </article>

            <article class="down">
                <form action="#" method="GET" name="if_search_Box">
                    <section>
                        <h3>회원구분</h3>
                        <input type="radio" name="member" id="all_member" value="all_member" checked>
                        <label for="all_member">전체회원</label>
                        <input type="radio" name="member" id="pay_member" value="pay_member">
                        <label for="pay_member">유료회원</label>
                        <input type="radio" name="member" id="new_member" value="new_member">
                        <label for="new_member">신규회원</label>
                        <input type="radio" name="member" id="stop_member" value="stop_member">
                        <label for="stop_member">중지회원</label>
                        <input type="radio" name="member" id="Rstop_member" value="Rstop_member">
                        <label for="Rstop_member">락커만료회원</label>
                        <input type="radio" name="member" id="npay_member" value="npay_member">
                        <label for="npay_member">미결제회원</label>
                        <input type="radio" name="member" id="end_member" value="end_member">
                        <label for="end_member">만료회원</label>
                    </section>
                    <section>
                        <h3>이용권 속성</h3>
                        <input type="checkbox" name="picket" id="date_count" value="date_count">
                        <label for="date_count">기간제</label>
                        <input type="checkbox" name="picket" id="num_count" value="num_count">
                        <label for="num_count">횟수제</label>
                    </section>
                    <section>
                        <h3>이용권 종류</h3>
                        <input type="checkbox" name="picket_kind" id="solo" value="solo">
                        <label for="solo">개인레슨</label>
                        <input type="checkbox" name="picket_kind" id="group" value="group">
                        <label for="group">그룹수업</label>
                        <input type="checkbox" name="picket_kind" id="place" value="place">
                        <label for="place">장소이용</label>
                    </section>
                    <section>
                        <h3>이용권 선택</h3>
                        <select name="picket_choice" id="picket_choice">
                            <option value="choice">이용권을 선택하세요.</option>
                            <option value="choice1">PT이용권</option>
                            <option value="choice2">SPT이용권</option>
                            <option value="choice3">CRYO이용권</option>
                            <option value="choice1">그룹수업</option>
                        </select>
                    </section>
                    <section>
                        <h3>담당자</h3>
                        <select name="manager" id="manager">
                            <option value="choice">회원관리 담당자</option>
                            <option value="choice1">이성은</option>
                            <option value="choice2">권성은</option>
                            <option value="choice3">최성은</option>
                            <option value="choice3">심성은</option>
                            <option value="choice3">김성은</option>
                        </select>
                    </section>
                    <section>
                        <h3>트레이너</h3>
                        <select name="solo_manager" id="solo_manager">
                            <option value="choice">개인레슨 담당자</option>
                            <option value="choice1">이성은</option>
                            <option value="choice2">권성은</option>
                            <option value="choice3">최성은</option>
                            <option value="choice3">심성은</option>
                            <option value="choice3">김성은</option>
                        </select>
                    </section>
                    
                    <section>
                        <input type="checkbox" name="end_date" id="end_date">
                        <label for="end_date" class="hid">만료일</label>
                        <h3>만료일</h3>
                        <input type="date" name="end_date1" id="end_date1">
                        <label for="end_date1" class="hid">시작날짜</label>
                        <span>~</span>
                        <input type="date" name="end_date2" id="end_date2">
                        <label for="end_date1" class="hid">종료날짜</label>
                    </section>
                    <section>
                        <input type="checkbox" name="end_count" id="end_count">
                        <label for="end_date" class="hid">잔여횟수</label>
                        <h3>잔여횟수</h3>
                        <input type="text" name="count_num" id="count_num" placeholder="잔여횟수를 적어주세요.">
                        <label for="count_num" class="hid">잔여횟수</label>
                        <span>회</span>
                        <select name="updown" id="updown">
                            <option value="down">이하</option>
                            <option value="up">이상</option>
                        </select>
                    </section>
                    <section>
                        <input type="checkbox" name="rocker_end_date" id="rocker_end_date">
                        <label for="rocker_end_date" class="hid">락커 만료일</label>
                        <h3>락커 만료일</h3>
                        <input type="date" name="rocker_end_date1" id="rocker_end_date1">
                        <label for="rocker_end_date1" class="hid">락커 만료일</label>
                        <span>~</span>
                        <input type="date" name="rocker_end_date2" id="rocker_end_date2">
                        <label for="rocker_end_date2" class="hid">락커 만료일</label>
                    </section>
                    <!-- <section>
                        <input type="checkbox" name="visit_date" id="visit_date">
                        <label for="visit_date" class="hid">최근 방문일</label>
                        <h3>최근 만료일</h3>
                        <input type="date" name="visit_date1" id="visit_date1">
                        <label for="visit_date1" class="hid">최근 방문일</label>
                        <span>~</span>
                        <input type="date" name="visit_date2" id="visit_date2">
                        <label for="visit_date2" class="hid">최근 방문일</label>
                    </section> -->
                    <section>
                        <input type="checkbox" name="pay_date" id="pay_date">
                        <label for="pay_date" class="hid">최근 결제일</label>
                        <h3>최근 결제일</h3>
                        <input type="date" name="pay_date1" id="pay_date1">
                        <label for="pay_date1" class="hid">최근 결제일</label>
                        <span>~</span>
                        <input type="date" name="pay_date2" id="pay_date2">
                        <label for="pay_date2" class="hid">최근 결제일</label>
                    </section>
                    <section>
                        <input type="checkbox" name="first_date" id="first_date">
                        <label for="first_date" class="hid">첫 결제일</label>
                        <h3>첫 결제일</h3>
                        <input type="date" name="first_date1" id="first_date1">
                        <label for="first_date1" class="hid">첫 결제일</label>
                        <span>~</span>
                        <input type="date" name="first_date2" id="first_date2">
                        <label for="first_date2" class="hid">첫 결제일</label>
                    </section>
                    <!-- 버튼 -->
                    <section>
                        <button type="submit">설정한 조건으로 회원 검색하기</button>
                        <button type="reset">검색조건 초기화</button>
                    </section>
                </form>
            </article>

            <article class="memberList">
                <form action="#" name="memberFrm" method="POST">
                    <section class="m_list">
                        <h2 class="hid">멤버 리스트</h2>
                        <table>
                            <thead>
                                <tr>
                                    <th>
                                        <label for="member_allCheck" class="hid">전체선택</label>
                                        <input type="checkbox" name="member_allCheck" id="member_allCheck">
                                    </th>
                                    <th>회원번호 <i class="fas fa-sort" data-sort-type="numberSort" data-sort-howSort="USER_SQ"></i></th>
                                    <th>이름</th>
                                    <th>나이</th>
                                    <th>전화번호</th>
                                    <th>등록일 <i class="fas fa-sort" data-sort-type="dateSort" data-sort-howSort="REG_DT"></i></th>
                                    <th>이용권</th>
                                    <th>이용권 만료일 <i class="fas fa-sort" data-sort-type="dateSort" data-sort-howSort="ticketEndDate"></i></th>
                                    <th>이용권 잔여횟수 <i class="fas fa-sort" data-sort-type="numberSort" data-sort-howSort="ticketCount"></i></th>
                                    <th>최근 측정일 <i class="fas fa-sort" data-sort-type="dateSort" data-sort-howSort="MEAS_DATE"></i></th>
                                    <th>라커 만료일 <i class="fas fa-sort" data-sort-type="dateSort" data-sort-howSort="rockerEndDate"></i></th>
                                    <th>담당자</th>
                                    <!-- <i class="fas fa-sort-up"></i> -->
                                    <!-- <i class="fas fa-sort-down"></i> -->
                                </tr>
                            </thead>
                        </table>
                        <div>
                            <table id="memberList_JS">
                                <tbody>
                                    <tr><td colspan="12">로 딩 중 . .</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </section>
                </form>
            </article>
        </section>
    </div>
    
<?php require_once 'lib/footer.php'; ?>    

    <!-- 신규 회원등록 -->
    <div class="newMember">
        <div class="unClick"></div>
        <h2>회원등록</h2>
        <form action="#" method="POST" class="new_wrap" id="user_information" name="user_information" autocomplete="off">
            
            <!-- 경고메시지 -->
            <div class="alert">
                <p>
                    창을 닫으시면 입력하신<br>
                    내용이 지워입니다.<br>
                    현재창을 닫으시겠습니까?
                </p>
                <img src="img/alert.png" alt="경고알림">
                <div>
                    <button type="reset" id="unload">확인</button>
                    <button type="button" onclick="$('.alert').add($('.unClick')).fadeOut(100)">취소</button>
                </div>
            </div>

            <div class="closePOP">
                <span></span>
                <span></span>
            </div>
            <div class="left">
                <img src="img/user.png" alt="유저사진" class="user_face">
                <input type="file" name="myFileUp" id="user-face">
                <label for="user-face">사진등록</label>
            </div>
            
            <div class="right">
                <p>
                    <label for="u_name" class="require">이 름</label>
                    <input type="text" name="u_name" id="u_name" required>
                </p>
                <p>
                    <label for="u_year" class="require">생년월일</label>
                    <input type="date" name="u_year" id="u_year" required>
                </p>
                <p>
                    <label for="u_gender" class="require">성 별</label>
                    <label>
                        <input type="radio" name="u_gender" id="male" value="M" checked> 남자
                    </label>
                    <label>
                        <input type="radio" name="u_gender" id="female" value="F"> 여자
                    </label>
                </p>
                <p>
                    <label for="u_num" class="require">연락처</label>
                    <input type="text" maxlength="13" minlength="12" name="u_num" id="u_num" required placeholder="'-' (하이픈) 빼고 입력">
                </p>
                <p>
                    <label for="u_email">이 메 일</label>
                    <input type="text" name="u_email" id="u_email">
                </p>
                
            </div>

            <div class="clear">
                <p>
                    <label for="u_teacher">담 당 자</label>
					<select type="text" name="u_teacher" id="u_teacher">
						<option value="">선택</option>
					</select>
                </p>
                <p>
                    <label for="u_address">주 소</label>
                    <input type="text" name="u_address" id="u_address">
                </p>
                <p>
                    <label for="u_memo">메 모</label>
                    <textarea name="u_memo" id="u_memo" rows="5"></textarea>
                </p>

                <!-- 버튼 -->
                <div class="btn">
                    <button type="button" id="user_frm_submit" name="user_frm_submit">회원등록</button>
                    <button type="button" id="user_frm_close" name="user_frm_close">닫 기</button>
                </div>
            </div>

        </form>
    </div>
    
    <!-- 회원검색 -->
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
            <li>박해성</li>
            <li>김형준</li>
            <li>박종남</li>
            <li>전상욱</li>
            <li>박해성</li>
            <li>김형준</li>
            <li>박종남</li>
            <li>전상욱</li>
        </ul>
    </div>
    <div id="MEAS_DATE_LIST_FRM">
        <h2>
            최근측정이력
            <div class="xBtn">
                <div>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </h2>
        <div class="content">
            <table class="list" border="1">
                <thead>
                    <tr>
                        <th>최근 측정일</th>
                        <th>회원명</th>
                        <th>POSE 측정일</th>
                        <th>ROM 측정일</th>
                        <th>이동</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- js -->
                </tbody>
            </table>
        </div>
    </div>


    <div id="memberAddVoucherDate">
        <h2>
            <span>이용권 기간 연장</span>
            <button class="closePopup">
                <div><span></span><span></span></div>
            </button>
        </h2>
        <div class="content">
            <div class="con">
                <input type="number" id="addVoucherDate">
                <label for="addVoucherDate">일 연장</label>
                <button class="15d_Btn">15일</button>
                <button class="30d_Btn">30일</button>
                <button class="60d_Btn">60일</button>
                <button class="90d_Btn">90일</button>
            </div>
            <div class="bottom">
                <button type="button" id="AddVoucherDateSubmitBtn">변경</button>
                <button type="button" class="closePop">닫기</button>
            </div>
        </div>
    </div>
</body>
</html>
