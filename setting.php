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
    <title>닥터케어유니온 - 설정</title>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css">
    <link rel="stylesheet" href="css/setting.css">
    <script src="js/jquery-3.5.1.min.js"></script>
    <script src="js/jquery-ui.js"></script>
    <script src="js/Array.js"></script>
    <script src="js/jswLib.js"></script>
    <script src="js/script.js"></script>
    <script src="js/setting.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
</head>
<body>
    <div class="dark_div"></div>
    <div id="wrap">
        
<?php require_once 'lib/header.php'; ?>

        <section class="content">
            <h2 class="hid">컨텐츠 영역</h2>

            <div class="top_sMenu">
                <div><i class="fas fa-cog"></i> 설 정</div>
                <ul>
                    <li class="active">임직원 관리 <i class="fas fa-angle-right"></i></li>
                    <li>상품 관리 <i class="fas fa-angle-right"></i></li>
                    <li>예약 관리 <i class="fas fa-angle-right"></i></li>
                    <li>센터 관리 <i class="fas fa-angle-right"></i></li>
                    <li>권한 관리 <i class="fas fa-angle-right"></i></li>
                    <li>공지 관리 <i class="fas fa-angle-right"></i></li>
                </ul>
                
            </div>

            <!-- 임직원 관리 -->
            <article class="content_0" id="trainerSet">
                <button class="add_Trainer_pop" data-form="addTrainer" data-title="임직원등록">임직원 등록</button>
                <nav class="level">
                    <h3>카테고리(직급)</h3>
                    <ul id="levelList">
                        <!-- js로 Load -->
                    </ul>
                    
                    <ul>
                        <li class="add" data-form="addLevel" data-title="카테고리(직급) 추가">
                            <a href="#"><i class="fas fa-plus-circle"></i> 추가하기</a>
                        </li>
                        <li class="set" data-form="setLevel" data-title="직급편집">
                            <a href="#"><i class="fas fa-cog"></i> 편집하기</a>
                        </li>
                    </ul>
                </nav>
                <div class="trainer">
                    <h3>
                        리스트
                        <select name="trainerStatus" id="trainerStatus">
                            <!-- js -->
                        </select>
                    </h3>
                    <ul id="trainerList">
                        <!-- js에서 Load -->
                    </ul>
                </div>
            </article>
            
            <!-- 상품 관리 -->
            <article class="content_1" id="itemSet" style="display:none;">
                <button id="addItem" data-form="addItem" data-title="상품 추가">상품 추가</button>

                <nav class="category">
                    <h3>카테고리</h3>
                    <div>
                        <ul>
                            <li class="cateAllView"><a href="#">전 체</a></li>
                        </ul>
                        <ul id="categoryList">
                            <!-- js로 Load -->
                        </ul>
                        <ul>
                            <li id="addCategory" data-form="addCategory" data-title="카테고리 추가">
                                <a href="#"><i class="fas fa-plus"></i> 추가하기</a>
                            </li>
                            <li id="setCategory" data-form="setCategory" data-title="카테고리 편집">
                                <a href="#"><i class="fas fa-cog"></i> 편집하기</a>
                            </li>
                        </ul>
                        
                    </div>
                </nav>
                <div class="itemList">
                    <h3>리스트</h3>
                    <ul id="itemList">
                        <!-- js에서 Load -->
                    </ul>
                </div>
            </article>
            
            <!-- 예약 관리 -->
            <article class="content_2" id="ticketingSet" style="display:none;">
                <form action="#" method="POST" name="ticketingSetFrm" autocomplete="off">

                    <section class="solo">
                        <h3>
                            개인레슨 이용권 예약 시스템 관리
                            <small>PT와 헤어컷과 같이 정해진 시간표 없이 서비스 제공자(강사)별로 일정한 시간 내에 약속시간을 잡아서 진행하는 개인 맞춤형 서비스 입니다.</small>
                        </h3>
                        <div class="content">
                            <h4>예약 시간 관리</h4>
                            <fieldset>
                                
                                <!-- <h4></h4> -->
                                <p>
                                    회원이 가맹점 전용 멤버스 앱을 통하여 개인레슨 이용권를 예약할 수 있는 조건을 설정합니다.<br>
                                    개인레슨 이용권의 예약 가능 시간은 현재 시간부터 설정된 시간 이후로 시간이 비어 있을 경우에만 예약이 가능합니다.<br>
                                    예약 취소 가능 시간이 지나면 회원은 더 이상 예약을 취소하거나 변경할 수 없습니다.
                                </p>
                                <h5>예약 가능 시간</h5>
                                <p>
                                    <input type="radio" name="solo_ticketingPossibleTime" id="solo_ticketingPossibleTime_Yes" value="0">
                                    <label for="solo_ticketingPossibleTime_Yes">수업 시작 전 항상 가능</label>
                                    <input type="radio" name="solo_ticketingPossibleTime" id="solo_ticketingPossibleTime_Set" value="1" checked>
                                    <label for="solo_ticketingPossibleTime_Set">수업 예약 가능한 시간 설정</label>

                                    <span>
                                        <input type="text" name="solo_ticketingPossibleTime_Set-input" id="solo_ticketingPossibleTime_Set-input" required>
                                        <label for="solo_ticketingPossibleTime_Set-input">일</label>
                                        <select name="solo_ticketingPossibleTime_Set-select1" id="solo_ticketingPossibleTime_Set-select1" required>
                                                <!-- js -->
                                        </select>
                                        <label for="solo_ticketingPossibleTime_Set-select1">시간</label>
                                        <select name="solo_ticketingPossibleTime_Set-select2" id="solo_ticketingPossibleTime_Set-select2" required>
                                            <!-- js -->
                                        </select>
                                        <label for="solo_ticketingPossibleTime_Set-select2">분 전까지 예약 가능</label>
                                    </span>
                                </p>
                                <h5>예약 취소 가능 시간</h5>
                                <p>
                                    <input type="radio" name="solo_ticketingChangeTime" id="solo_ticketingChangeTime_1" value="0">
                                    <label for="solo_ticketingChangeTime_1">수업 시간 전 항상 가능</label>
                                    <input type="radio" name="solo_ticketingChangeTime" id="solo_ticketingChangeTime_2" value="1">
                                    <label for="solo_ticketingChangeTime_2">예약 후 취소 불가</label>
                                    <input type="radio" name="solo_ticketingChangeTime" id="solo_ticketingChangeTime_3" value="2">
                                    <label for="solo_ticketingChangeTime_3">당일 취소 불가</label>
                                    <input type="radio" name="solo_ticketingChangeTime" id="solo_ticketingChangeTime_4" value="3" checked>
                                    <label for="solo_ticketingChangeTime_4">수업 취소 가능한 시간 설정</label>

                                    <span>
                                        <input type="text" name="solo_ticketingChangeTime_4-input" id="solo_ticketingChangeTime_4-input" value="1" required>
                                        <label for="solo_ticketingChangeTime_4-input">일</label>
                                        <select name="solo_ticketingChangeTime_4-select1" id="solo_ticketingChangeTime_4-select1" required>
                                                <!-- js -->
                                        </select>
                                        <label for="solo_ticketingChangeTime_4-select1">시간</label>
                                        <select name="solo_ticketingChangeTime_4-select2" id="solo_ticketingChangeTime_4-select2" required>
                                            <!-- js -->
                                        </select>
                                        <label for="solo_ticketingChangeTime_4-select2">분 전까지 예약 가능</label>
                                    </span>
                                </p>
                            </fieldset>
                            <h4>결석 처리 기준 설정</h4>
                            <fieldset>
                                
                                <p>
                                    회원이 예약한 서비스의 종료 전까지 센터에 배치된 입장 체커(터치 스크린)에서 예약한 서비스를 선택하고<br>
                                    입장하지 않거나 서비스 제공자(강사)가 코치 앱을 통해 출결처리를 하지 않은 경우 익일 오전 4시에 자동 결석으로 처리됩니다.
                                </p>
                                <p style="margin-bottom:30px;">
                                    <input type="radio" name="solo_endClassAuto" id="solo_endClassAuto_No" value="0">
                                    <label for="solo_endClassAuto_No" style="margin-right:20px;">자동결석 없음</label>
                                    <input type="radio" name="solo_endClassAuto" id="solo_endClassAuto_Yes" value="1" checked>
                                    <label for="solo_endClassAuto_Yes">수업종료 시 자동결석 처리</label>
                                </p>
                            </fieldset>
                            <h4>결석 시 이용권 차감 유무 설정</h4>
                            <fieldset>
                                
                                <p>
                                    회원이 결석처리가 되었을 경우 예약하기 위해 사용했던 이용권의 남은 횟수가 차감되거나 차감되지 않도록 설정할 수 있습니다.<br>
                                    이용권 차감으로 설정 시, 회원들에게 미리 예약하고 결석하면 남은 횟수가 차감된다는 사실을 알려주시기 바랍니다.
                                </p>
                                <h5>결석 시 이용권 차감 유무 <small>(결석 처리시, 이용권 차감 유무 설정은 회원이 예약했을 때 설정값이 적용됩니다.)</small></h5>
                                <p>
                                    <input type="radio" name="solo_endClassTicketMinus" id="solo_endClassTicketMinus_Yes" value="0" checked>
                                    <label for="solo_endClassTicketMinus_Yes">이용권 차감</label>
                                    <input type="radio" name="solo_endClassTicketMinus" id="solo_endClassTicketMinus_No" value="1">
                                    <label for="solo_endClassTicketMinus_No">이용권 미차감</label>
                                </p>
                            </fieldset>
                        </div>
                    </section>


                    <section class="group">
                        <h3>
                            그룹레슨 이용권 예약 시스템 관리
                            <small>일주일간 수업시간이 미리 정해져 있고 설정한 정원에 따라 여러 명이 참석 가능한 서비스 입니다.</small>
                        </h3>
                        <div class="content">
                            <h4>예약 시간 관리</h4>
                            <fieldset>
                                
                                <p>
                                    회원이 가맹점 전용 멤버스 앱을 통하여 개인레슨 이용권를 예약할 수 있는 조건을 설정합니다.<br>
                                    개인레슨 이용권의 예약 가능 시간은 현재 시간부터 설정된 시간 이후로 시간이 비어 있을 경우에만 예약이 가능합니다.<br>
                                    예약 취소 가능 시간이 지나면 회원은 더 이상 예약을 취소하거나 변경할 수 없습니다.
                                </p>
                                <h5>예약 가능 시간</h5>
                                <p>
                                    <input type="radio" name="group_ticketingPossibleTime" id="group_ticketingPossibleTime_Yes" value="0" checked>
                                    <label for="group_ticketingPossibleTime_Yes">수업 시작 전 항상 가능</label>
                                    <input type="radio" name="group_ticketingPossibleTime" id="group_ticketingPossibleTime_Set" value="1">
                                    <label for="group_ticketingPossibleTime_Set">수업 예약 가능한 시간 설정</label>
                                    
                                    <span>
                                        <input type="text" name="group_ticketingPossibleTime_Set-input" id="group_ticketingPossibleTime_Set-input" value="1" required>
                                        <label for="group_ticketingPossibleTime_Set-input">일</label>
                                        <select name="group_ticketingPossibleTime_Set-select1" id="group_ticketingPossibleTime_Set-select1" required>
                                                <!-- js -->
                                        </select>
                                        <label for="group_ticketingPossibleTime_Set-select1">시간</label>
                                        <select name="group_ticketingPossibleTime_Set-select2" id="group_ticketingPossibleTime_Set-select2" required>
                                            <!-- js -->
                                        </select>
                                        <label for="group_ticketingPossibleTime_Set-select2">분 전까지 예약 가능</label>
                                    </span>
                                </p>
                                <h5>예약 취소 가능 시간</h5>
                                <p>
                                    <input type="radio" name="group_ticketingChangeTime" id="group_ticketingChangeTime_1" value="0">
                                    <label for="group_ticketingChangeTime_1">수업 시간 전 항상 가능</label>
                                    <input type="radio" name="group_ticketingChangeTime" id="group_ticketingChangeTime_2" value="1">
                                    <label for="group_ticketingChangeTime_2">예약 후 취소 불가</label>
                                    <input type="radio" name="group_ticketingChangeTime" id="group_ticketingChangeTime_3" value="2">
                                    <label for="group_ticketingChangeTime_3">당일 취소 불가</label>
                                    <input type="radio" name="group_ticketingChangeTime" id="group_ticketingChangeTime_4" value="3" checked>
                                    <label for="group_ticketingChangeTime_4">수업 취소 가능한 시간 설정</label>
                                    
                                    <span>
                                        <input type="text" name="group_ticketingChangeTime_4-input" id="group_ticketingChangeTime_4-input" value="1" required>
                                        <label for="group_ticketingChangeTime_4-input">일</label>
                                        <select name="group_ticketingChangeTime_4-select1" id="group_ticketingChangeTime_4-select1" required>
                                                <!-- js -->
                                        </select>
                                        <label for="group_ticketingChangeTime_4-select1">시간</label>
                                        <select name="group_ticketingChangeTime_4-select2" id="group_ticketingChangeTime_4-select2" required>
                                            <!-- js -->
                                        </select>
                                        <label for="group_ticketingChangeTime_4-select2">분 전까지 예약 가능</label>
                                    </span>
                                </p>
                            </fieldset>
                            <h4>결석 처리 기준 설정</h4>
                            <fieldset>
                                
                                <p>
                                    회원이 예약한 서비스의 종료 전까지 센터에 배치된 입장 체커(터치 스크린)에서 예약한 서비스를 선택하고<br>
                                    입장하지 않거나 서비스 제공자(강사)가 코치 앱을 통해 출결처리를 하지 않은 경우 익일 오전 4시에 자동 결석으로 처리됩니다.
                                </p>
                                <p style="margin-bottom:30px;">
                                    <input type="radio" name="group_endClassAuto" id="group_endClassAuto_No" value="0">
                                    <label for="group_endClassAuto_No" style="margin-right:20px;">자동결석 없음</label>
                                    <input type="radio" name="group_endClassAuto" id="group_endClassAuto_Yes" value="1" checked>
                                    <label for="group_endClassAuto_Yes">수업종료 시 자동결석 처리</label>
                                </p>
                            </fieldset>
                            <h4>결석 시 이용권 차감 유무 설정</h4>
                            <fieldset>
                                
                                <p>
                                    회원이 결석처리가 되었을 경우 예약하기 위해 사용했던 이용권의 남은 횟수가 차감되거나 차감되지 않도록 설정할 수 있습니다.<br>
                                    이용권 차감으로 설정 시, 회원들에게 미리 예약하고 결석하면 남은 횟수가 차감된다는 사실을 알려주시기 바랍니다.
                                </p>
                                <h5>결석 시 이용권 차감 유무 <small>(결석 처리시, 이용권 차감 유무 설정은 회원이 예약했을 때 설정값이 적용됩니다.)</small></h5>
                                <p>
                                    <input type="radio" name="group_endClassTicketMinus" id="group_endClassTicketMinus_Yes" value="0" checked>
                                    <label for="group_endClassTicketMinus_Yes">이용권 차감</label>
                                    <input type="radio" name="group_endClassTicketMinus" id="group_endClassTicketMinus_No" value="1">
                                    <label for="group_endClassTicketMinus_No">이용권 미차감</label>
                                </p>
                            </fieldset>
                        </div>
                    </section>
                    
                    <div class="btn">
                        <button id="TICKETING_SETTING_SUBMIT" type="submit">적 용</button>
                    </div>
                </form>
            </article>
            
            <!-- 센터 관리 -->
            <article class="content_3" id="centerSet" style="display:none;">
                <form action="#" method="post">
                    <section class="companyInfo">
                        <h3>사업자 등록 정보</h3>
                        <div class="content">
                            <div class="companyName" style="margin-bottom: 15px;">
                                <label for="companyName">업체명</label>
                                <p>업체명</p>
                            </div>
                            <div class="companyNum" style="margin-bottom: 15px;">
                                <label for="companyNum">사업자 번호</label>
                                <p>사업자 번호</p>
                            </div>
                            <div class="ceoInfo" style="margin-bottom: 15px;">
                                <label for="ceoName">대표자명</label>
                                <p>대표자명</p>
                            </div>
                            <div class="companyAttr" style="margin-bottom: 15px;">
                                <label for="companyAttr">사업자 형태</label>
                                <p>법인 사업자 / 개인 사업자</p>
                            </div>
                            <div class="companyType" style="margin-bottom: 15px;">
                                <label for="companyType">업태/종목</label>
                                <p>업태/종목</p>
                            </div>
                            <div class="companyAddress" style="margin-bottom: 15px;">
                                <label for="companyAddress">주소</label>
                                <p>주소</p>
                            </div>
                        </div>
                    </section>
                    <section class="centerInfo">
                        <h3>센터 정보</h3>
                        <div class="content">
                            <div class="centerImage">
                                <label>센터 대표 이미지</label>
                                <img class="centerProfileImage" src="img/no_img.png">
                                    <!-- 이미지 보여지는 곳 -->

                                <label for="centerImage">이미지 선택</label>
                                <input type="file" name="centerImage" id="centerImage" hidden>
                            </div>
                            <div class="centerEx">
                                <label for="centerEx">센터설명</label>
                                <textarea name="centerEx" id="centerEx"></textarea>
                            </div>
                            <div class="centerManager">
                                <label for="centerManager">센터장</label>
                                <input type="text" name="centerManager" id="centerManager">
                            </div>
                            <div class="centerName">
                                <label for="centerName">센터명</label>
                                <input type="text" name="centerName" id="centerName">
                            </div>
                            <div class="centerPhone">
                                <label for="centerPhone">연락처</label>
                                <input type="text" name="centerPhone" id="centerPhone">
                            </div>
                            <div class="centerPax">
                                <label for="centerPax">팩스</label>
                                <input type="text" name="centerPax" id="centerPax">
                            </div>
                            <div class="centerCome">
                                <label for="centerCome">오시는길 설명</label>
                                <textarea name="centerCome" id="centerCome"></textarea>
                            </div>
                            <div class="centerPage" style="clear: both;margin-top:10px;">
                                <label for="centerPage">홈페이지</label>
                                <input type="url" name="centerPage" id="centerPage">
                            </div>
                            <div class="centerSns" style="margin-top:10px;">
                                <label for="centerSns">SNS</label>
                                <input type="text" name="centerSns" id="centerSns">
                            </div>
                        </div>
                        <div class="buttonDiv">
                            <button id="set_company_submit">변경사항 저장</button>
                        </div>
                    </section>

                    <section class="centerTurn">
                        <h3>센터 운영</h3>
                        <div class="content">
                            <div>
                                <h4 style="width:100%;">운영시간</h4>
                                <ul>
                                    <li>
                                        <h5>월</h5>
                                        <input type="radio" name="TurnTime_Mo" id="TurnTime_Mo_no">
                                        <label for="TurnTime_Mo_no">휴무</label>
                                        <input type="radio" name="TurnTime_Mo" id="TurnTime_Mo_yes" checked>
                                        <label for="TurnTime_Mo_yes">영업 시작</label>
                                        <p>
                                            <select name="TurnTime_Mo_yes_h1" id="TurnTime_Mo_yes_h1" class="TurnTime_Hour"></select>
                                            시
                                            <select name="TurnTime_Mo_yes_m1" id="TurnTime_Mo_yes_m1" class="TurnTime_Minutes"></select>
                                            <span>분 부터</span>
                                            <select name="TurnTime_Mo_yes_h2" id="TurnTime_Mo_yes_h2" class="TurnTime_Hour"></select>
                                            시
                                            <select name="TurnTime_Mo_yes_m2" id="TurnTime_Mo_yes_m2" class="TurnTime_Minutes"></select>
                                            분 까지
                                        </p>
                                    </li>
                                    <li>
                                        <h5>화</h5>
                                        <input type="radio" name="TurnTime_Tu" id="TurnTime_Tu_no">
                                        <label for="TurnTime_Tu_no">휴무</label>
                                        <input type="radio" name="TurnTime_Tu" id="TurnTime_Tu_yes" checked>
                                        <label for="TurnTime_Tu_yes">영업 시작</label>
                                        <p>
                                            <select name="TurnTime_Tu_yes_h1" id="TurnTime_Tu_yes_h1" class="TurnTime_Hour"></select>
                                            시
                                            <select name="TurnTime_Tu_yes_m1" id="TurnTime_Tu_yes_m1" class="TurnTime_Minutes"></select>
                                            <span>분 부터</span>
                                            <select name="TurnTime_Tu_yes_h2" id="TurnTime_Tu_yes_h2" class="TurnTime_Hour"></select>
                                            시
                                            <select name="TurnTime_Tu_yes_m2" id="TurnTime_Tu_yes_m2" class="TurnTime_Minutes"></select>
                                            분 까지
                                        </p>
                                    </li>
                                    <li>
                                        <h5>수</h5>
                                        <input type="radio" name="TurnTime_We" id="TurnTime_We_no">
                                        <label for="TurnTime_We_no">휴무</label>
                                        <input type="radio" name="TurnTime_We" id="TurnTime_We_yes" checked>
                                        <label for="TurnTime_We_yes">영업 시작</label>
                                        <p>
                                            <select name="TurnTime_We_yes_h1" id="TurnTime_We_yes_h1" class="TurnTime_Hour"></select>
                                            시
                                            <select name="TurnTime_We_yes_m1" id="TurnTime_We_yes_m1" class="TurnTime_Minutes"></select>
                                            <span>분 부터</span>
                                            <select name="TurnTime_We_yes_h2" id="TurnTime_We_yes_h2" class="TurnTime_Hour"></select>
                                            시
                                            <select name="TurnTime_We_yes_m2" id="TurnTime_We_yes_m2" class="TurnTime_Minutes"></select>
                                            분 까지
                                        </p>
                                    </li>
                                    <li>
                                        <h5>목</h5>
                                        <input type="radio" name="TurnTime_Th" id="TurnTime_Th_no">
                                        <label for="TurnTime_Th_no">휴무</label>
                                        <input type="radio" name="TurnTime_Th" id="TurnTime_Th_yes" checked>
                                        <label for="TurnTime_Th_yes">영업 시작</label>
                                        <p>
                                            <select name="TurnTime_Th_yes_h1" id="TurnTime_Th_yes_h1" class="TurnTime_Hour"></select>
                                            시
                                            <select name="TurnTime_Th_yes_m1" id="TurnTime_Th_yes_m1" class="TurnTime_Minutes"></select>
                                            <span>분 부터</span>
                                            <select name="TurnTime_Th_yes_h2" id="TurnTime_Th_yes_h2" class="TurnTime_Hour"></select>
                                            시
                                            <select name="TurnTime_Th_yes_m2" id="TurnTime_Th_yes_m2" class="TurnTime_Minutes"></select>
                                            분 까지
                                        </p>
                                    </li>
                                    <li>
                                        <h5>금</h5>
                                        <input type="radio" name="TurnTime_Fr" id="TurnTime_Fr_no">
                                        <label for="TurnTime_Fr_no">휴무</label>
                                        <input type="radio" name="TurnTime_Fr" id="TurnTime_Fr_yes" checked>
                                        <label for="TurnTime_Fr_yes">영업 시작</label>
                                        <p>
                                            <select name="TurnTime_Fr_yes_h1" id="TurnTime_Fr_yes_h1" class="TurnTime_Hour"></select>
                                            시
                                            <select name="TurnTime_Fr_yes_m1" id="TurnTime_Fr_yes_m1" class="TurnTime_Minutes"></select>
                                            <span>분 부터</span>
                                            <select name="TurnTime_Fr_yes_h2" id="TurnTime_Fr_yes_h2" class="TurnTime_Hour"></select>
                                            시
                                            <select name="TurnTime_Fr_yes_m2" id="TurnTime_Fr_yes_m2" class="TurnTime_Minutes"></select>
                                            분 까지
                                        </p>
                                    </li>
                                    <li>
                                        <h5>토</h5>
                                        <input type="radio" name="TurnTime_Sa" id="TurnTime_Sa_no">
                                        <label for="TurnTime_Sa_no">휴무</label>
                                        <input type="radio" name="TurnTime_Sa" id="TurnTime_Sa_yes" checked>
                                        <label for="TurnTime_Sa_yes">영업 시작</label>
                                        <p>
                                            <select name="TurnTime_Sa_yes_h1" id="TurnTime_Sa_yes_h1" class="TurnTime_Hour"></select>
                                            시
                                            <select name="TurnTime_Sa_yes_m1" id="TurnTime_Sa_yes_m1" class="TurnTime_Minutes"></select>
                                            <span>분 부터</span>
                                            <select name="TurnTime_Sa_yes_h2" id="TurnTime_Sa_yes_h2" class="TurnTime_Hour"></select>
                                            시
                                            <select name="TurnTime_Sa_yes_m2" id="TurnTime_Sa_yes_m2" class="TurnTime_Minutes"></select>
                                            분 까지
                                        </p>
                                    </li>
                                    <li>
                                        <h5>일</h5>
                                        <input type="radio" name="TurnTime_Su" id="TurnTime_Su_no" checked>
                                        <label for="TurnTime_Su_no">휴무</label>
                                        <input type="radio" name="TurnTime_Su" id="TurnTime_Su_yes">
                                        <label for="TurnTime_Su_yes">영업 시작</label>
                                        <p>
                                            <select name="TurnTime_Su_yes_h1" id="TurnTime_Su_yes_h1" class="TurnTime_Hour"></select>
                                            시
                                            <select name="TurnTime_Su_yes_m1" id="TurnTime_Su_yes_m1" class="TurnTime_Minutes"></select>
                                            <span>분 부터</span>
                                            <select name="TurnTime_Su_yes_h2" id="TurnTime_Su_yes_h2" class="TurnTime_Hour"></select>
                                            시
                                            <select name="TurnTime_Su_yes_m2" id="TurnTime_Su_yes_m2" class="TurnTime_Minutes"></select>
                                            분 까지
                                        </p>
                                    </li>
                                </ul>
                            </div>

                            <div>
                                <h4 class="shil_date" style="width:100%;">휴일설정</h4>
                                <div id="calendar" class="content1">
                                    <!-- 캘린더 -->
                                </div>
                            </div>
                        </div>
                        <div class="buttonDiv" style="width: 600px;">
                            <button id="set_operating_submit">변경사항 저장</button>
                        </div>
                    </section>

                    
                    <!-- ROOM 관리 -->
                    <section class="centerRoom">
                        <h3>센터 Room 관리</h3>
                        <div class="content">
                            <button class="addRoomBtn">Room추가</button>
                            <ul id="RoomList">
                                <li>
                                    <input type="text" name="room01" id="room01">
                                    <button class="RemoveRoom">삭제</button>
                                </li>
                                <li>
                                    <input type="text" name="room02" id="room02">
                                    <button class="RemoveRoom">삭제</button>
                                </li>
                            </ul>
                        </div>
                    </section>
                </form>
            </article>


            <!-- 권한 관리 -->
            <article class="content_4" id="rightSet" style="display:none;">
                <form action="#" method="POST" name="rightSetFrm">
                    <nav class="right_trainerList">
                        <h3>임직원 리스트</h3>
                        <ul class="content">
                            <!-- js -->
                            <li>전상욱
                                <ul>
                                    <li>전상욱</li>
                                </ul>
                            </li>
                        </ul>
                    </nav>
                    <div class="right_rightList">
                        <h3>권한 리스트</h3>
                        <div class="content">
                            <h4>메뉴 접근 권한</h4>
                            <fieldset class="menuGrade">
                                <input type="checkbox" name="menuGrade" id="menuGrade_1" data-code="1">
                                <label for="menuGrade_1">스케줄러 메뉴</label>
                                <input type="checkbox" name="menuGrade" id="menuGrade_2" data-code="2">
                                <label for="menuGrade_2">상품 메뉴</label>
                                <input type="checkbox" name="menuGrade" id="menuGrade_3" data-code="3">
                                <label for="menuGrade_3">통계 메뉴</label>
                                <input type="checkbox" name="menuGrade" id="menuGrade_4" data-code="4">
                                <label for="menuGrade_4">회계 메뉴</label>
                                <input type="checkbox" name="menuGrade" id="menuGrade_5" data-code="5">
                                <label for="menuGrade_5">라커 메뉴</label>
                                <input type="checkbox" name="menuGrade" id="menuGrade_6" data-code="6">
                                <label for="menuGrade_6">히스토리 메뉴</label>
                                <input type="checkbox" name="menuGrade" id="menuGrade_7" data-code="7">
                                <label for="menuGrade_7">설정 메뉴</label>
                            </fieldset>

                            <h4>멤버스 권한</h4>
                            <fieldset class="membersGrade">
                                <input type="checkbox" name="membersGrade" id="membersGrade_1" data-code="10">
                                <label for="membersGrade_1">신규회원 등록</label>
                                <input type="checkbox" name="membersGrade" id="membersGrade_2" data-code="11">
                                <label for="membersGrade_2">회원 삭제</label>
                                <input type="checkbox" name="membersGrade" id="membersGrade_3" data-code="12">
                                <label for="membersGrade_3">기간연장</label>
                                <input type="checkbox" name="membersGrade" id="membersGrade_4" data-code="13">
                                <label for="membersGrade_4">회원정보 수정</label>
                                <input type="checkbox" name="membersGrade" id="membersGrade_5" data-code="14">
                                <label for="membersGrade_5">메모 수정</label>
                                <input type="checkbox" name="membersGrade" id="membersGrade_6" data-code="15">
                                <label for="membersGrade_6">기간횟수 조정</label>
                                <input type="checkbox" name="membersGrade" id="membersGrade_7" data-code="16">
                                <label for="membersGrade_7">강사 변경</label>
                            </fieldset>

                            <h4>스케줄러 권한</h4>
                            <fieldset class="schedulerGrade">
                                <input type="checkbox" name="schedulerGrade" id="schedulerGrade_1" data-code="21">
                                <label for="schedulerGrade_1">스케줄 등록</label>
                                <input type="checkbox" name="schedulerGrade" id="schedulerGrade_2" data-code="22">
                                <label for="schedulerGrade_2">스케줄 수정/삭제</label>
                                <input type="checkbox" name="schedulerGrade" id="schedulerGrade_3" data-code="23">
                                <label for="schedulerGrade_3">수업 생성</label>
                                <input type="checkbox" name="schedulerGrade" id="schedulerGrade_4" data-code="24">
                                <label for="schedulerGrade_4">수업 수정/삭제</label>
                            </fieldset>

                            <h4>상품 권한</h4>
                            <fieldset class="itemGrade">
                                <input type="checkbox" name="itemGrade" id="itemGrade_1" data-code="31">
                                <label for="itemGrade_1">결제</label>
                            </fieldset>

                            <h4>통계 권한</h4>
                            <fieldset class="chartGrade">
                                <input type="checkbox" name="chartGrade" id="chartGrade_1" data-code="41">
                                <label for="chartGrade_1">개인레슨 통계</label>
                                <input type="checkbox" name="chartGrade" id="chartGrade_2" data-code="42">
                                <label for="chartGrade_2">그룹레슨 통계</label>
                            </fieldset>

                            <h4>회계 권한</h4>
                            <fieldset class="accountGrade">
                                <input type="checkbox" name="accountGrade" id="accountGrade_1" data-code="51">
                                <label for="accountGrade_1">상세보기</label>
                                <input type="checkbox" name="accountGrade" id="accountGrade_2" data-code="52">
                                <label for="accountGrade_2">수정하기</label>
                                <input type="checkbox" name="accountGrade" id="accountGrade_3" data-code="53">
                                <label for="accountGrade_3">양도하기</label>
                                <input type="checkbox" name="accountGrade" id="accountGrade_4" data-code="54">
                                <label for="accountGrade_4">환불하기</label>
                            </fieldset>

                            <h4>라커 권한</h4>
                            <fieldset class="rockerGrade">
                                <input type="checkbox" name="rockerGrade" id="rockerGrade_1" data-code="61">
                                <label for="rockerGrade_1">라커설정</label>
                            </fieldset>

                            <h4>히스토리 권한</h4>
                            <fieldset class="historyGrade">
                                <input type="checkbox" name="historyGrade" id="historyGrade_1" data-code="71">
                                <label for="historyGrade_1">담당강사 관리</label>
                                <input type="checkbox" name="historyGrade" id="historyGrade_2" data-code="72">
                                <label for="historyGrade_2">수업 관리</label>
                                <input type="checkbox" name="historyGrade" id="historyGrade_3" data-code="73">
                                <label for="historyGrade_3">회원이용권 관리</label>
                                <input type="checkbox" name="historyGrade" id="historyGrade_4" data-code="74">
                                <label for="historyGrade_4">회원출석 관리</label>
                            </fieldset>

                            <h4>설정 권한</h4>
                            <fieldset class="settingGrade">
                                <input type="checkbox" name="settingGrade" id="settingGrade_1" data-code="81">
                                <label for="settingGrade_1">임직원 관리</label>
                                <input type="checkbox" name="settingGrade" id="settingGrade_2" data-code="82">
                                <label for="settingGrade_2">상품 관리</label>
                                <input type="checkbox" name="settingGrade" id="settingGrade_3" data-code="83">
                                <label for="settingGrade_3">예약 관리</label>
                                <input type="checkbox" name="settingGrade" id="settingGrade_4" data-code="84">
                                <label for="settingGrade_4">센터 관리</label>
                                <input type="checkbox" name="settingGrade" id="settingGrade_5" data-code="85">
                                <label for="settingGrade_5">권한 관리</label>
                                <input type="checkbox" name="settingGrade" id="settingGrade_6" data-code="86">
                                <label for="settingGrade_6">공지 관리</label>
                                <input type="checkbox" name="settingGrade" id="settingGrade_7" data-code="87">
                                <label for="settingGrade_7">임직원정보 수정</label>
                            </fieldset>

                            <button type="submit" id="grade_SubmitBtn">적 용</button>

                        </div>
                    </div>
                </form>
            </article>

            <!-- 공지 관리 -->
            <article class="content_5" id="notice" style="display:none;">
                <button class="del_Notice_pop">공지 삭제</button>
                <button class="add_Notice_pop" data-form="addNotice" data-title="공지등록">공지 등록</button>
                <div class="notice">
                    <h3>공지 리스트
                        <button id="notice_all_view_btn" class="active" data-sq="1">전체공지</button>
                        <button id="notice_admin_view_btn" data-sq="2">직원공지</button>
                    </h3>
                    <div class="con">
                        <table border="1" id="noticeList">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">
                                        <input type="checkbox" name="noticeChk0" id="noticeChk0">
                                        <label for="noticeChk0">전체체크</label>
                                    </th>
                                    <th style="width: 5%;">No</th>
                                    <th style="width: 12%;">구 분</th>
                                    <th style="width: 15%;">제 목</th>
                                    <th style="width: 35%;">내 용</th>
                                    <th style="width: 15%;">작성자</th>
                                    <th style="width: 18%;">일 시</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="noticeChk1" id="noticeChk1">
                                        <label for="noticeChk1">체크</label>
                                    </td>
                                    <td>01</td>
                                    <td>전체 공지</td>
                                    <td class="text">첫 번째 공지사항 입니다.</td>
                                    <td>최고관리자</td>
                                    <td>2020-12-03 14:20:45</td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="noticeChk2" id="noticeChk2">
                                        <label for="noticeChk2">체크</label>
                                    </td>
                                    <td>02</td>
                                    <td>전체 공지</td>
                                    <td class="text">첫 번째 공지사항 입니다.</td>
                                    <td>최고관리자</td>
                                    <td>2020-12-03 14:20:45</td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="noticeChk3" id="noticeChk3">
                                        <label for="noticeChk3">체크</label>
                                    </td>
                                    <td>03</td>
                                    <td>직원 공지</td>
                                    <td class="text">첫 번째 공지사항 입니다.</td>
                                    <td>관리자</td>
                                    <td>2020-12-03 14:20:45</td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="noticeChk4" id="noticeChk4">
                                        <label for="noticeChk4">체크</label>
                                    </td>
                                    <td>04</td>
                                    <td>전체 공지</td>
                                    <td class="text">첫 번째 공지사항 입니다.</td>
                                    <td>최고관리자</td>
                                    <td>2020-12-03 14:20:45</td>
                                </tr>
                            </tbody>
                            <!-- js에서 Load -->
                        </table>
                    </div>
                </div>
            </article>
            
        </section>
        
    </div>
    
<?php require_once 'lib/footer.php'; ?>

    
    <!-- 팝업 -->
    <div class="popup">
        <h2 class="title"><span>임직원추가</span>
            <button class="closeBtn">
                <div>
                    <span></span>
                    <span></span>
                </div>
            </button>
        </h2>

        <form action="#" method="POST" name="addTrainerFrm" class="content addTrainer" autocomplete="off">
            <article>
                <h3>기본 정보</h3>
                <div class="row1">
                    <img class="col1" src="img/user.png" alt="프로필사진">
                    <input type="file" name="addTrainer_img" id="addTrainer_img">
                    <label for="addTrainer_img">이미지 찾기</label>
                </div>
                <div class="row2">
                    <label for="addTrainer_name" class="require">이 름</label>
                    <p>
                        <input type="text" name="addTrainer_name" id="addTrainer_name" required>
                    </p>
                </div>
                <div class="row2_1">
                    <label for="addTrainer_id" class="require">아이디</label>
                    <p>
                        <input type="text" name="addTrainer_id" id="addTrainer_id" placeholder="영문과 숫자만 사용해주세요." required>
                    </p>
                </div>
                <div class="row2_2" style="display: none;">
                    <label for="addTrainer_id">비밀번호</label>
                    <p>
                        <input type="text" name="addTrainer_pw" id="addTrainer_pw" required>
                    </p>
                </div>
                <div class="row3">
                    <label for="addTrainer_gender" class="require">성 별</label>
                    <p>
                        <input type="radio" name="addTrainer_gender" id="addTrainer_gender_male" checked>
                        <label for="addTrainer_gender_male">남자</label>
                        <input type="radio" name="addTrainer_gender" id="addTrainer_gender_female">
                        <label for="addTrainer_gender_female">여자</label>
                    </p>
                </div>
                <div class="row4">
                    <label for="addTrainer_phone" class="require">연락처</label>
                    <p>
                        <select name="addTrainer_phone1" id="addTrainer_phone1" required>
                            <option value="">선택</option>
                            <option value="010">010</option>
                            <option value="010">011</option>
                            <option value="010">016</option>
                            <option value="010">017</option>
                            <option value="010">018</option>
                            <option value="010">019</option>
                        </select>
                        <label for="addTrainer_phone1"> - </label>
                        <input type="text" name="addTrainer_phone2" id="addTrainer_phone2" maxlength="4" required>
                        <label for="addTrainer_phone2"> - </label>
                        <input type="text" name="addTrainer_phone3" id="addTrainer_phone3" maxlength="4" required>
                        <label for="addTrainer_phone3" class="hid"></label>
                    </p>
                </div>
                <div class="row5">
                    <label for="addTrainer_birth" class="require">생년월일</label>
                    <p>
                        <input type="date" name="addTrainer_birth" id="addTrainer_birth" style="width:200px">
                        <!-- <script>$('#addTrainer_birth').datepicker()</script> -->
                    </p>
                </div>
                <div class="row5_1">
                    <label for="addTrainer_email">이메일</label>
                    <p>
                        <input type="email" name="addTrainer_email" id="addTrainer_email" required>
                    </p>
                </div>
                <div class="row5_2">
                    <label for="addTrainer_address">주 소</label>
                    <p>
                        <input type="text" name="addTrainer_address" id="addTrainer_address" placeholder="시/구 까지 적어주세요.">
                    </p>
                </div>
                <div class="row6">
                    <label for="addTrainer_hello">자기소개</label>
                    <p>
                        <textarea name="addTrainer_hello" id="addTrainer_hello"></textarea>
                    </p>
                </div>
            </article>

            <article class="insa">
                <h3>인사 정보</h3>
                <div class="row1">
                    <label for="addTrainer_level">직 급</label>
                    <p>
                        <select name="addTrainer_level" id="addTrainer_level" required></select>
                    </p>
                </div>
                <div class="row2">
                    <label for="work_start_date">입사일</label>
                    <p>
                        <input type="date" name="work_start_date" id="work_start_date">
                    </p>
                </div>
            </article>

            <article class="btn">
                <h3 class="hid">버튼 정보</h3>
                <button type="button" class="submit" id="addManagerSubmitBtn">추 가</button>
                <button type="reset" class="close">닫 기</button>
            </article>

        </form>

        <form action="#" method="POST" name="addLevelFrm" class="content addLevel" autocomplete="off">
            <p>
                <label for="levelName">직급 명칭</label>
                <input type="text" name="levelName" id="levelName" required>
            </p>
            <article class="btn">
                <h3 class="hid">버튼 정보</h3>
                <button type="submit" class="submit" id="add_trainer_category_submit_btn">추 가</button>
                <button type="reset" class="close">닫 기</button>
            </article>
        </form>

        <form action="#" method="post" name="setLevelFrm" class="content setLevel" autocomplete="off">
            <ul>
                <!-- js에서 Load -->
            </ul>

            <article class="btn">
                <h3 class="hid">버튼 정보</h3>
                <button type="submit" class="submit" id="set_trainer_category_submit_btn">수 정</button>
                <button type="reset" class="close">닫 기</button>
            </article>
        </form>
        
        <form action="#" method="POST" name="addCategoryFrm" class="content addCategory" autocomplete="off">
            <p>
                <label for="categoryName">카테고리 명칭</label>
                <input type="text" name="categoryName" id="categoryName" required>
            </p>
            <article class="btn">
                <h3 class="hid">버튼 정보</h3>
                <button type="submit" class="submit" id="add_item_category_submit_btn">추 가</button>
                <button type="reset" class="close">닫 기</button>
            </article>
        </form>

        <form action="#" method="POST" name="addCategorySmallFrm" class="content addCategorySmall" autocomplete="off">
            <p>
                <label for="categorySmallName">서브카테고리 명칭</label>
                <input type="text" name="categorySmallName" id="categorySmallName" required>
            </p>
            <article class="btn">
                <h3 class="hid">버튼 정보</h3>
                <button type="submit" class="submit" id="add_item_subcategory_submit_btn">추 가</button>
                <button type="reset" class="close">닫 기</button>
            </article>
        </form>

        <form action="#" method="post" name="setCategoryFrm" class="content setCategory" autocomplete="off">
            <ul>
                <!-- js에서 Load -->
            </ul>
            <article class="btn">
                <h3 class="hid">버튼 정보</h3>
                <button type="submit" class="submit" id="set_item_category_submit_btn">수 정</button>
                <button type="reset" class="close">닫 기</button>
            </article>
        </form>

        <form action="#" method="post" name="addItemFrm" class="content addItem" autocomplete="off">
            <article class="body">
                <div class="VOUCHER_TYPE">
                    <label for="itemFrm_service">서비스 종류</label>
                    <input type="radio" name="itemFrm_service" id="itemFrm_service_date" checked>
                    <label for="itemFrm_service_date" class="s">개인레슨</label>
                    <input type="radio" name="itemFrm_service" id="itemFrm_service_count">
                    <label for="itemFrm_service_count" class="s">그룹레슨</label>
                </div>
                <div>
                    <label for="itemFrm_name" class="require">상품명</label>
                    <input type="text" name="itemFrm_name" id="itemFrm_name" class="l" required>
                </div>
                <div>
                    <label for="itemFrm_type" class="require">카테고리</label>
                    <select name="itemFrm_type" id="itemFrm_type" class="l" required>
                        <option value="">선택</option> 
                        <!-- js에서 Load -->
                    </select>
                </div>
                <div>
                    <label for="itemFrm_category">서브 카테고리</label>
                    <select name="itemFrm_category" id="itemFrm_category" class="l" required>
                        <option value="">선택</option> 
                        <!-- js에서 Load -->
                    </select>
                </div>
                <div class="USE_TYPE">
                    <label for="itemFrm_attr">상품 속성</label>
                    <input type="radio" name="itemFrm_attr" id="itemFrm_attr_date" checked>
                    <label for="itemFrm_attr_date" class="s">기간제</label>
                    <input type="radio" name="itemFrm_attr" id="itemFrm_attr_count">
                    <label for="itemFrm_attr_count" class="s">횟수제</label>
                </div>
                <div>
                    <label for="itemFrm_date" class="require">기간</label>
                    <select name="itemFrm_date" id="itemFrm_date" class="m" readonly required 
                        style="
                            width: 100px;
                            color: #000;
                            opacity: 1;
                            border: none;

                    ">
                        <option value="infinite">무제한</option>
                        <option value="write">직접입력</option>
                    </select>
                    <p>
                        <input type="text" name="itemFrm_date_write" id="itemFrm_date_write" class="ms">
                        <select name="itemFrm_date_write_month" id="itemFrm_date_write_month">
                            <option value="d">일</option>
                            <option value="m">개월</option>
                        </select>
                    </p>
                </div>
                <div>
                    <label for="itemFrm_count" class="require">횟수</label>
                    <select name="itemFrm_count" id="itemFrm_count" class="m" readonly required 
                        style="
                            width: 100px;
                            color: #000;
                            opacity: 1;
                            border: none;      
                    ">
                        <option value="infinite">무제한</option>
                        <option value="write">직접입력</option>
                    </select>
                    <p>
                        <input type="text" name="itemFrm_count_write" id="itemFrm_count_write" class="ms">
                        <span style="font-size:13.333333px;">회</span>
                    </p>
                </div>
                <div>
                    <label for="itemFrm_dayStop" class="require">이용횟수제한</label>
                    <label for="itemFrm_dayStop">일일</label>
                    <select name="itemFrm_dayStop" id="itemFrm_dayStop" class="s" required>
                        <option value="">선택</option>
                        <option value="0">무제한</option>
                        <option value="1">1회</option>
                        <option value="2">2회</option>
                    </select>
                    <br>
                    <label for="itemFrm_weekStop">주간</label>
                    <select name="itemFrm_weekStop" id="itemFrm_weekStop" class="s" required>
                        <option value="">선택</option>
                        <option value="0">무제한</option>
                        <option value="1">1회</option>
                        <option value="2">2회</option>
                        <option value="3">3회</option>
                        <option value="4">4회</option>
                        <option value="5">5회</option>
                        <option value="6">6회</option>
                        <option value="7">7회</option>
                    </select>
                </div>
                <div>
                    <label for="itemFrm_pay" class="require">판매정가</label>
                    <input type="text" name="itemFrm_pay" id="itemFrm_pay" class="m" required>
                    <input type="checkbox" name="itemFrm_tax" id="itemFrm_tax" checked>
                    <label for="itemFrm_tax" class="s" style="font-size:14px">부가세 포함상품</label>
                </div>
                <div>
                    <label for="itemFrm_sale">할인</label>
                    <select name="itemFrm_sale" id="itemFrm_sale" class="s" style="margin-left: 0;" required>
                        <option value="">선택</option>
                        <option value="input">직접입력</option>
                    </select>
                    <input type="text" name="itemFrm_sale_input" id="itemFrm_sale_input" style="width: 100px;">
                    <label for="itemFrm_sale_input" class="s">%</label>
                </div>
                <div>
                    <label for="itemFrm_sale_amount">할인금액</label>
                    <input type="text" name="itemFrm_sale_amount" id="itemFrm_sale_amount" readonly class="m" style="border: 0;">
                </div>
                <div style="border-top: 1px solid rgba(0,0,0,.1);margin-top: 10px;padding-top: 20px;font-size: 16px;font-weight: 500;">
                    <label for="itemFrm_payment">판매가</label>
                    <input type="text" name="itemFrm_payment" id="itemFrm_payment" readonly class="m" style="font-size: 16px;border: 0;" value="0">원
                </div>
            </article>

            <article class="btn">
                <h3 class="hid">버튼 정보</h3>
                <button type="submit" class="submit" id="add_item_submit_btn">등 록</button>
                <button type="reset" class="close">닫 기</button>
            </article>
        </form>

        <!-- 공지 등록 -->
        <form action="#" method="post" name="addNoticeFrm" class="content addNotice" autocomplete="off">
            <article class="body">
                <p>
                    <label for="new_noticeCategory">공지 구분</label>
                    <select name="new_noticeCategory" id="new_noticeCategory">
                        <option value="">선택</option>
                        <option value="1">전체 공지</option>
                        <option value="2">직원 공지</option>
                    </select>
                </p>
                <p>
                    <label for="new_contentTitle">제목</label>
                    <input style="border: 1px solid rgba(128,128,128,.4);" type="text" name="new_contentTitle" id="new_contentTitle">
                </p>
                <p>
                    <label for="new_contentText">내용</label>
                    <textarea name="new_contentText" id="new_contentText"></textarea>
                </p>
                <p class="writerInfoTag">
                    <label for="new_writer">작성자</label>
                    <input type="text" name="new_writer" id="new_writer" readonly>
                </p>
            </article>
            
            <article class="btn">
                <h3 class="hid">버튼 정보</h3>
                <button type="submit" class="submit">등 록</button>
                <button type="reset" class="close">닫 기</button>
            </article>
        </form>
        
        <!-- 공지 카테고리 추가 -->
        <form action="#" method="post" name="addNoticeCategoryFrm" class="content addNoticeCategory" autocomplete="off">
            <p>
                <label for="noticeCategoryName">카테고리 명칭</label>
                <input type="text" name="noticeCategoryName" id="noticeCategoryName" required>
            </p>
            
            <article class="btn">
                <h3 class="hid">버튼 정보</h3>
                <button type="submit" class="submit">등 록</button>
                <button type="reset" class="close">닫 기</button>
            </article>
        </form>
        
        
    </div>
    
</body>
</html>