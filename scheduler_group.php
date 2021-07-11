<!DOCTYPE html>

<?php require_once 'lib/_init.php';

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
    <title>닥터케어유니온 - 스케줄러</title>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/scheduler.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css">
    <link rel="stylesheet" href="js/lib/schedule/dist/jquery.schedule.css">     <!-- 스케줄러 라이브러리 -->
    <script src="js/jquery-3.5.1.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="js/jswLib.js"></script>
    <script src="js/script.js"></script>
    <script src="js/lib/schedule/dist/jquery.schedule_group.js"></script>
    <script src="js/scheduler_group.js"></script>
</head>
<body>
    <div class="dark_div"></div>
    <div id="wrap">
        <?php require_once 'lib/header.php'; ?>
        <section class="content">
            <h2 class="hid">컨텐츠 영역</h2>
            <div class="top_sMenu">
                <h3 class="hid">스케줄러 옵션</h3>
            
                <div class="topleft">
                    <!-- <button>주</button>
                    <button class="active">일</button> -->
                    <label class="hid" for="dateChoice">날짜 선택</label>
                    <input type="date" name="dateChoice" id="dateChoice">
                    <select name="whereChoice" id="whereChoice">
                        <!-- js -->
                    </select>
                </div>

                <div class="topcenter">
                  <i class="fas fa-chevron-circle-left" id="datePrev"></i>
                  <span id="date">1990년 1월 1일 월요일</span>
                  <i class="fas fa-chevron-circle-right" id="dateNext"></i>
                </div>

                <div class="topright">
                    <!-- <p>예약 <span id="TICKETTING_COUNT">25</span></p>
                    <p>출석 <span id="TICKETTING_IN">2</span></p>
                    <p>결석 <span id="TICKETTING_NO">0</span></p>
                    <p>취소 <span id="TICKETTING_cancel">30</span></p>
                    <p>휴일 <span class="rect"></span></p> -->
                    <button id="solo_s_view_button" onclick="location.href='scheduler.php'">개인레슨 스케줄</button>
                    <button id="group_s_view_button" onclick="location.href='scheduler_group.php'" class="active">그룹레슨 스케줄</button>
                </div>
            </div>

            <!-- 스케줄러 -->
            <!-- <div id="schedule-date" class="jqs-demo mb-3"></div> -->
            <div id="schedule-week" class="jqs-demo mb-3"></div>
            

        </section>

    </div>


    <address>Copyright &copy; Liansoft. Allright Reserved. 2021</address>



    
<!-- ////////////////////////////////////////////////////// -->


<ul class="rightClick_div" id="rightClick_div1">
    <li>출석 상태로 변경</li>
    <li>결석 상태로 변경</li>
    <li>취소 상태로 변경</li>
    <li>회원 관리</li>
</ul>
<ul class="rightClick_div" id="rightClick_div2">
    <li>수업 예약</li>
    <li>예약 목록</li>
    <li>수업 정보</li>
    <li>수업 삭제</li>
</ul>

<div id="pop_div1" class="form-pop">
    <section class="state_sec">
        <h2 class="solo_Title">출석 상태로 변경</h2>
        <p>1900년 01월 01일 00:00 ~ 00:00</p>
        <p><span>전상욱</span> 회원님의</p>
        <p class="pop_ticket">PT 이용권</p>
        <p>예약을 <span class="pop_state">출석</span>으로 처리하시겠습니까?</p>

        <div>
            <span class="pop_state">출석</span>처리 시 이용권의 남은 횟수가 <span class="color">1회 차감</span>됩니다.
        </div>

        <div>
            <p><span class="pop_ticket">PT이용권</span>의 남은 횟수가 아래와 같이 변경됩니다.</p>
            <div>
                <div class="now">
                    <p>현재</p>
                    <p><span class="pop_before">10</span>회</p>
                </div>
                <i class="fas fa-arrow-right"></i>
                <div class="after">
                    <p>변경 후</p>
                    <p><span class="pop_after">9</span>회</p>
                </div>
            </div>
            <div>
                <button type="button" class="pop_submit">적 용</button>
                <button type="button" class="pop_close">닫 기</button>
            </div>
        </div>
    </section>
    <section class="state_sec">
        <h2 class="solo_Title">결석 상태로 변경</h2>
        <p>1900년 01월 01일 00:00 ~ 00:00</p>
        <p><span>전상욱</span> 회원님의</p>
        <p class="pop_ticket">PT 이용권</p>
        <p>예약을 <span class="pop_state">결석</span>으로 처리하시겠습니까?</p>

        <div>
            <span class="pop_state">결석</span>처리 시 이용권의 남은 횟수가 <span class="color">1회 차감</span>됩니다.
        </div>

        <div>
            <p><span class="pop_ticket">PT이용권</span>의 남은 횟수가 아래와 같이 변경됩니다.</p>
            <div>
                <div class="now">
                    <p>현재</p>
                    <p><span class="pop_before">10</span>회</p>
                </div>
                <i class="fas fa-arrow-right"></i>
                <div class="after">
                    <p>변경 후</p>
                    <p><span class="pop_after">9</span>회</p>
                </div>
            </div>
            <div>
                <button type="button" class="pop_submit">적 용</button>
                <button type="button" class="pop_close">닫 기</button>
            </div>
        </div>
    </section>
    <section class="state_sec">
        <h2 class="solo_Title">취소 상태로 변경</h2>
        <p>1900년 01월 01일 00:00 ~ 00:00</p>
        <p><span>전상욱</span> 회원님의</p>
        <p class="pop_ticket">PT 이용권</p>
        <p>예약을 <span class="pop_state">취소</span>으로 처리하시겠습니까?</p>

        <div>
            <span class="pop_state">취소</span>처리 시 이용권의 남은 횟수가 <span class="color">유지</span>됩니다.
        </div>

        <div>
            <p><span class="pop_ticket">PT이용권</span>의 남은 횟수가 아래와 같이 변경됩니다.</p>
            <div>
                <div class="now">
                    <p>현재</p>
                    <p><span class="pop_before">10</span>회</p>
                </div>
                <i class="fas fa-arrow-right"></i>
                <div class="after">
                    <p>변경 후</p>
                    <p><span class="pop_after">10</span>회</p>
                </div>
            </div>
            <div>
                <button type="button" class="pop_submit">적 용</button>
                <button type="button" class="pop_close">닫 기</button>
            </div>
        </div>
    </section>
</div>



<!-- 그룹레슨 스케줄 우클릭 메뉴 팝업 -->
<div id="pop_div2" class="form-pop">
    <section class="reservationPopup">   <!-- 그룹수업예약 -->
        <h2 class="group_Title">그룹수업 예약</h2>
        <div class="col1">
            <label for="groupSearchMember">회원검색</label>
            <input type="text" name="groupSearchMember" id="groupSearchMember" placeholder="클릭해주세요" readonly>
        </div>
        <div class="col2">
            <label for="GticketChoice">이용권</label>
            <select name="GticketChoice" id="GticketChoice">
                <option value="">회원을 먼저 선택해주세요</option>
            </select>
        </div>
        <div class="col3">
            <h3>이용권 정보</h3>
            <p>
                <span>담당: 권성은 / 만료: 2020.12.30</span><br>
                <span>이용: 3 / 잔여: 31 / 예약: 0</span>
            </p>
        </div>
        <div class="col4">
            <h3>수업명</h3>
            <p>체력증진 & 체형교정</p>
        </div>
        <div class="col5">
            <h3>수업장소</h3>
            <p>ROOM1</p>
        </div>
        <div class="col6">
            <h3>예약인원</h3>
            <p>40명 (예약 28명 / 잔여 2명 / 대기 10명)</p>
        </div>
        <div class="col7">
            <h3>예약일</h3>
            <p>
                <span class="date_period">1990년 1월 1일 (월요일)</span>
                /
                <span class="time_period">00:00 ~ 00:00</span>
            </p>
        </div>
        <div class="col8">
            <h3>관리자</h3>
            <p>
                <span>이성은</span>
            </p>
        </div>
        <div class="col9">
            <h3>메 모</h3>
            <p>
                ★★★★☆<br>
                운동강도 중간<br>
                체력증진 및 체형교정 프로그램<br>
                체력증진 및 체형교정 프로그램
            </p>
        </div>
        <div class="btnSet">
            <!-- <button>예약목록</button> -->
            <div class="jqs-options-close" id="userReservBtn">예 약</div>
            <div class="jqs-options-remove">삭 제</div>
            <div class="jqs-options-cancel">닫 기</div>
        </div>

    </section>

    <section class="reservMemberPopup hid">   <!-- 그룹수업예약목록 -->
        <h3>수업 예약자 목록</h3>
        <div class="search">
            <label for="ticketting_member_search">회원검색</label>
            <input type="text" id="ticketting_member_search" placeholder="이름 또는 휴대전화번호">
        </div>
        <div class="count">
            <p>참석회원 수 : <span style="margin-left:4px"> 0명</span></p>
            <p>출석 : <span style="margin-left:4px"> 0명</span></p>
            <p>예약결석 : <span style="margin-left:4px"> 0명</span></p>
        </div>
        <div class="table">
            <table>
                <thead>
                    <tr>
                        <th>
                            <label for="TICKETTING_CHECKALL" class="hid">전체선택</label>
                            <input id="TICKETTING_CHECKALL" type="checkbox">
                        </th>
                        <th>이름</th>
                        <th>연락처</th>
                        <th>예약일</th>
                        <th>상태</th>
                    </tr>
                </thead>
                
                <tbody>
                    <!-- js -->
                </tbody>
            
            </table>
        </div>
        <div class="ticketting_btnSet">
            <div>
                <button class="ok">출석처리</button>
                <button class="no">결석처리</button>
                <button class="cancel">예약취소</button>
            </div>
            <button class="close">닫 기</button>
        </div>
    </section>

    <section class="groupClassInfoPopup hid">   <!-- 그룹수업정보 -->
        <div class="closeX">
            <div>
                <span></span>
                <span></span>
            </div>
        </div>
        <h2 class="group_Title">그룹수업 정보</h2>
        <div>
            <h3>그룹 수업명</h3>
            <p>다이어트 & 상하체</p>
        </div>
        <div>
            <h3>장 소</h3>
            <p>ROOM1</p>
        </div>
        <!-- <div>
            <h3>기 간</h3>
            <p>2020-09-21 ~ 2020-09-25</p>
        </div> -->
        <!-- <div>
            <h3>반복요일</h3>
            <p>월, 화, 수, 목, 금</p>
        </div> -->
        <div>
            <h3>수업시간</h3>
            <p class="time_period">00:00 ~ 00:00</p>
        </div>
        <div>
            <h3>강사정보</h3>
            <p>이성은</p>
        </div>
        <!-- <div>
            <h3>수업수당</h3>
            <p>5만원</p>
        </div> -->
        <div>
            <h3>예약가능인원</h3>
            <p>20명</p>
        </div>
        <div>
            <h3>예약대기인원</h3>
            <p>0명</p>
        </div>
        <div>
            <h3>메 모</h3>
            <p>
                ★★★★☆<br>
                운동강도 중간<br>
                체력증진 및 체형교정 프로그램<br>
                체력증진 및 체형교정 프로그램
            </p>
        </div>
    </section>
</div>

<!-- 그룹수업메모 -->
<div class="groupClassMemo form-pop">
    <h2>수업내용 메모</h2>
    <textarea name="groupClassMemo" id="groupClassMemo" readonly>
2020.07.20
스트레칭
2020.07.31
하체운동 실시
OOO회원 허리아파함.
</textarea>
    <button id="groupClassMemoSetBtn">수 정</button>
</div>


<div class="solo form-pop">
    <div class="solo_Pop">   <!--팝업에 들어갈 내용-->
        <h2 class="solo_Title">개인수업 예약</h2>
        <div class="solo_topBtn">
            <button class="active">등록회원</button>
            <button>미등록회원</button>
        </div>
        <div class="input">
            <div class="col1">
                <label for="solo_searchName">회원검색</label>
                <input type="text" id="solo_searchName" class="solo_searchName" placeholder="클릭해주세요." readonly maxlength="10">
            </div>
            <div class="phone-num" style="display: none;">
                <label for="solo_PhoneN">연락처</label>
                <input type="text" name="solo_PhoneN" id="solo_PhoneN" placeholder="'-'(하이픈) 제외">
            </div>
            <div class="col2">
                <label for="pop_teacherChoice">담당자</label>
                <select name="pop_teacherChoice" id="pop_teacherChoice">
                    <option value="">선택해주세요</option>
                    <option value="teacher1">이성은</option>
                    <option value="teacher2">권성은</option>
                    <option value="teacher3">최성은</option>
                    <option value="teacher4">심성은</option>
                    <option value="teacher5">김성은</option>
                    <option value="teacher6">박성은</option>
                    <option value="teacher7">홍성은</option>
                </select>
            </div>
            <div class="col3">
                <label for="ticketChoice">이용권</label>
                <select name="ticketChoice" id="ticketChoice">
                    <option value="">선택해주세요</option>
                    <option value="teacher1">PT 이용권 70분</option>
                    <option value="teacher2">PT 이용권 60분</option>
                    <option value="teacher3">PT 이용권 50분</option>
                    <option value="teacher4">PT 이용권 40분</option>
                    <option value="teacher5">PT 이용권 30분</option>
                    <option value="teacher6">PT 이용권 20분</option>
                    <option value="teacher7">PT 이용권 10분</option>
                </select>
            </div>
            <div class="col4">
                <h3>이용권 정보</h3><br>
                <p>
                    <span>담당: 권성은 / 만료: 2020.12.30</span><br>
                    <span>이용: 3 / 잔여: 31 / 예약: 0</span>
                </p>
            </div>
            <div class="jqs-options-time">
                <h3>시 간</h3>
                <p>
                    <span class="date_period">1990년 1월 1일 (월요일)</span>
                    <span id="time_period"></span>
                </p>
            </div>
            <div class="col5">
                <label for="jqs-memo">메 모</label>
                <textarea rows="4" name="jqs-memo" id="jqs-memo"></textarea>
            </div>
            <div class="btnSet">
                <div class="jqs-options-close">확 인</div>
                <div class="jqs-options-remove">삭 제</div>
                <div class="jqs-options-cancel">닫 기</div>
            </div>
        </div>
    </div>
</div>

<div class="group form-pop">
    <h2 class="group_Title">그룹수업 등록</h2>
    <div class="input">
        <div class="col1">
            <label for="groupClassName">그룹 수업명</label>
            <input type="text" name="groupClassName" id="groupClassName" placeholder="입력해주세요">
        </div>
        <div class="col2">
            <label for="groupClassWhere">장 소</label>
            <p id="groupClassWhere" style="width:300px">ROOM1</p>
            
        </div>
        <div class="jqs-options-time">
            <h3>기 간</h3>
            <p>
                <label for="groupClassWhen1" class="hid">시작기간</label>
                <label for="groupClassWhen2" class="hid">종료기간</label>
                <input type="date" name="groupClassWhen1" id="groupClassWhen1"> ~ 
                <input type="date" name="groupClassWhen2" id="groupClassWhen2">
                
            </p>
        </div>
        <div class="col3">
            <h3>요일</h3>
            <ul>
                <li class="active">월</li>
                <li class="active">화</li>
                <li class="active">수</li>
                <li class="active">목</li>
                <li class="active">금</li>
                <li>토</li>
                <li>일</li>
            </ul>
        </div>
        <div class="col4">
            <h3>수업시간</h3>
            <p class="time_period"></p>
        </div>
        <div class="col5">
            <h3>강사정보</h3>
            <p>
                <label for="groupClassTeacher" class="hid">강사정보</label>
                <select name="groupClassTeacher" id="groupClassTeacher">
                    <!-- js -->
                </select>
                <label for="groupClassPay" class="hid">수업수당</label>
                <input type="text" name="groupClassPay" id="groupClassPay" placeholder="수업수당">원
            </p>
        </div>
        <div class="col6">
            <label for="groupClassCountO">예약가능인원</label>
            <input type="text" name="groupClassCountO" id="groupClassCountO" placeholder="숫자로 입력해주세요">
        </div>
        <div class="col7">
            <label for="groupClassCountX">예약대기인원</label>
            <input type="text" name="groupClassCountX" id="groupClassCountX" placeholder="숫자로 입력해주세요">
        </div>
        <div class="col8">
            <label for="pop_groupClassMemo">메 모</label>
            <textarea rows="4" name="pop_groupClassMemo" id="pop_groupClassMemo"></textarea>
        </div>
        <div class="btnSet">
            <div class="jqs-options-close" id="addClassBtn">확 인</div>
            <div class="jqs-options-remove">삭 제</div>
            <div class="jqs-options-cancel">닫 기</div>
            
        </div>
    </div>
</div>


<!-- 회원검색 -->
<div class="mSearch_container form-pop">
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



</body>
</html>