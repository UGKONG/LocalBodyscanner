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
    <title>닥터케어유니온 - 임직원 정보</title>
    <script src="js/jquery-3.5.1.min.js"></script>
    <script src="js/jquery-ui.js"></script>
    <script src="js/jswLib.js"></script>
    <script src="js/Array.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css">
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/trainerinfo.css">
    <script src="js/script.js"></script>
    <script>
        TRAINER_SEQ = '<?=getAnyParameter("USER_SQ","")?>';
    </script>
    <script src="js/trainerinfo.js"></script>
</head>
<body>
    <form id="pw_change_frm" action="#" method="POST" name="pw_change_frm" style="display: none;">
        <h2>비밀번호 변경하기</h2>
        <p>
            <label for="now_pw">현재 비밀번호</label>
            <input type="password" name="now_pw" id="now_pw">
        </p>
        <p>
            <label for="change_pw1">변경 비밀번호</label>
            <input type="password" name="change_pw1" id="change_pw1">
        </p>
        <p>
            <label for="change_pw2">변경 비밀번호 확인</label>
            <input type="password" name="change_pw2" id="change_pw2">
        </p>
        <p>
            <button id="changePW_submit" type="button">변경하기</button>
            <button type="button" onclick="$('form#pw_change_frm').fadeOut(200)">닫 기</button>
        </p>
    </form>
    <div class="dark_div"></div>
    <div id="wrap">

    <?php require_once 'lib/header.php'; ?>

        <section class="content">
            <div class="top_sMenu">
                <i class="fas fa-arrow-left"></i>
                트레이너 정보
            </div>
            <!-- <button id="info_btn"><i class="fas fa-info-circle"></i> 강사소개</button> -->
            <button id="pw_change_btn"><i class="fas fa-key"></i> 비밀번호 변경</button>
            <button id="del_btn"><i class="far fa-trash-alt"></i> 임직원삭제</button>

            <div class="up">
                <div class="u_img">
                    <img class="user_face" src="img/user.png" alt="회원사진">
                    <label for="user-face"><i class="fas fa-camera"></i> 사진변경</label>
                    <input type="file" name="user-face" id="user-face">
                </div>
                <div class="info_s">
                    <p class="u_name">
                        <i class="fas fa-signature"></i>
                        <span class="name">이름</span>
                        <span class="editForm">
                            <input type="text" name="edit_name" id="edit_name" class="editData" autocomplete="off">
                        </span>
                        <button class="editBtn">수정</button>
                    </p>
                    <p class="u_gender">
                        <i class="fas fa-venus-mars"></i>
                        <span class="gender">성별</span>
                        <span class="editForm">
                            <select type="text" name="edit_gender" id="edit_gender" class="editData" autocomplete="off">
                                <option value="M">남자</option>
                                <option value="F">여자</option>
                            </select>
                        </span>
                        <button class="editBtn">수정</button>
                    </p>
                    <p class="u_num">
                        <i class="fas fa-mobile-alt"></i>
                        <span class="num">연락처</span>
                        <span class="editForm">
                            <input type="text" name="edit_num" id="edit_num" class="editData" autocomplete="off">
                        </span>
                        <button class="editBtn">수정</button>
                    </p>
                    <p class="u_year">
                        <i class="far fa-calendar-alt"></i>
                        <span class="year">생년월일</span>
                        <span class="age">나이</span>
                        <span class="editForm">
                            <input type="date" name="edit_year" id="edit_year" class="editData" autocomplete="off">
                        </span>
                        <button class="editBtn">수정</button>
                    </p>
                    <p class="u_address">
                        <i class="fas fa-map-marker-alt"></i>
                        <span class="address">주소</span>
                        <span class="editForm">
                            <input type="text" name="edit_address" id="edit_address" class="editData" autocomplete="off">
                        </span>
                        <button class="editBtn">수정</button>
                    </p>
                    <p class="u_email">
                        <i class="far fa-envelope-open"></i>
                        <span class="email">이메일</span>
                        <span class="editForm">
                            <input type="text" name="edit_email" id="edit_email" class="editData" autocomplete="off">
                        </span>
                        <button class="editBtn">수정</button>
                    </p>
                </div>
            </div>

            <div class="down">
                <ul class="tab_menu">
                    <li class="active">임직원 정보</li>
                    <li>스케줄 설정</li>
                    <li>휴일 설정</li>
                    <li style="max-width:0; height: 0; text-indent:-99999999999999%">예약 설정</li>
                    <li>인사 정보</li>
                    <li style="width:150px;">급여 및 수당정보</li>
                </ul>
                <article class="tab_menu1">
                    <div class="content1">
                        <h3>
                            <p>
                                <span>이용권 미보유 회원 목록</span>
                                <span>
                                    회원수 
                                    <span id="myMemberCount1">0명</span>
                                </span>
                            </p>
                            <p>
                                <!-- <i class="fas fa-sort" data-icon="sortIcon"></i> -->
                            </p>
                        </h3>
                        <ul id="myMember_list">
                            <!-- js -->
                        </ul>
                    </div>
                    <div class="content2">
                        <h3>
                            <p>
                                <span>이용권 보유 회원 목록</span>
                                <span>
                                    회원수 
                                    <span id="myMemberCount2">0명</span>
                                </span>
                            </p>
                            <p>
                                <select name="soloGroupFilter" id="soloGroupFilter">
                                    <option value="solo">개인레슨</option>    
                                    <option value="group">그룹레슨</option>    
                                </select>
                                <!-- <i class="fas fa-sort" data-icon="sortIcon"></i> -->
                            </p>
                        </h3>
                        <ul id="voucherMyMember_list">
                            <!-- js -->
                        </ul>
                    </div>
                    <div class="content3">
                        <h3>
                            <p>
                                <span>오늘 스케줄</span>
                                <span>
                                    오늘 총 스케줄
                                    <span id="myMemberCount3">00명</span>
                                </span>
                            </p>
                        </h3>
                        <ul id="mySchedule_list">
                            <!-- js -->
                        </ul>
                    </div>
                </article>
                <article class="tab_menu2">
                    <div class="content1">
                        <!-- <div>
                            <h3>01. 수업 준비 시간 설정</h3>
                            <p>
                                <label for="readyTime">준비시간</label>
                                <input type="number" name="readyTime" id="readyTime" max="20" min="0">
                                <label for="readyTime">분</label>
                            </p>
                        </div> -->
                        <div>
                            <h3>01. 개인레슨 이용권 수업 요일 설정</h3>
                            <ul>
                                <li>월</li>
                                <li>화</li>
                                <li>수</li>
                                <li>목</li>
                                <li>금</li>
                                <li>토</li>
                                <li>일</li>
                            </ul>
                            <!-- <p>
                                <span>개인레슨 이용권 수업준비 시간은 개인레슨 이용권를 진행하기 위해 필요한 사전 준비 시간을 의미합니다.</span>
                                <span>회원은 다른 개인레슨 이용권 수업이 끝나고 설정된 준비시간 동안은 개인레슨 이용권 예약이 불가능 합니다.</span>
                                <span>개인레슨 이용권 수업을 연달아 진행할 수 있는 경우 수업준비 시간을 0분으로 설정해 주세요.</span>
                            </p> -->
                            <br>
                        </div>
                        <div class="classTime" style="justify-content:flex-start;">
                            <h3>02. 근무 시간 설정</h3>
                            <p style="width:100%;">
                                <label for="classTime1">근무 시간</label>
                                <select name="classTime1" id="classTime1">
                                    <option value="00">00</option>
                                    <option value="01">01</option>
                                    <option value="02">02</option>
                                    <option value="03">03</option>
                                    <option value="04">04</option>
                                    <option value="05">05</option>
                                    <option value="06">06</option>
                                    <option value="07">07</option>
                                    <option value="08">08</option>
                                    <option value="09">09</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                    <option value="13">13</option>
                                    <option value="14">14</option>
                                    <option value="15">15</option>
                                    <option value="16">16</option>
                                    <option value="17">17</option>
                                    <option value="18">18</option>
                                    <option value="19">19</option>
                                    <option value="20">20</option>
                                    <option value="21">21</option>
                                    <option value="22">22</option>
                                    <option value="23">23</option>
                                </select>
                                <label for="classTime2">:</label>
                                <select name="classTime2" id="classTime2">
                                    <option value="00">00</option>
                                    <option value="30">30</option>
                                </select>
                                <label for="classTime3">-</label>
                                <select name="classTime3" id="classTime3">
                                <option value="00">00</option>
                                    <option value="01">01</option>
                                    <option value="02">02</option>
                                    <option value="03">03</option>
                                    <option value="04">04</option>
                                    <option value="05">05</option>
                                    <option value="06">06</option>
                                    <option value="07">07</option>
                                    <option value="08">08</option>
                                    <option value="09">09</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                    <option value="13">13</option>
                                    <option value="14">14</option>
                                    <option value="15">15</option>
                                    <option value="16">16</option>
                                    <option value="17">17</option>
                                    <option value="18">18</option>
                                    <option value="19">19</option>
                                    <option value="20">20</option>
                                    <option value="21">21</option>
                                    <option value="22">22</option>
                                    <option value="23">23</option>
                                </select>
                                <label for="classTime4">:</label>
                                <select name="classTime4" id="classTime4">
                                    <option value="00">00</option>
                                    <option value="30">30</option>
                                </select>
                            </p>
                            <br>
                            <br>
                            <br>
                            <button id="scheduleSet_saveBtn">저 장</button>
                        </div>
                    </div>

                    <div class="content2">
                        <ol>
                            <li>월</li>
                            <li>화</li>
                            <li>수</li>
                            <li>목</li>
                            <li>금</li>
                            <li>토</li>
                            <li>일</li>
                        </ol>
                        <ul>
                            <li class="off"></li>
                            <li class="off">10:00 ~ 23:00</li>
                            <li class="">10:00 ~ 23:00</li>
                            <li class="">10:00 ~ 23:00</li>
                            <li class="">10:00 ~ 23:00</li>
                            <li class="">10:00 ~ 23:00</li>
                            <li class="off"></li>
                        </ul>
                    </div>
                </article>

                <article class="tab_menu3" style="justify-content:flex-start;padding-left:20px;">
                    <div id="calendar" class="content1"></div>
                    <!-- <div class="content2">
                        <h4>지정한 휴일 : <span id="weekendDay">0</span>일</h4>
                    </div> -->
                </article>

                <article class="tab_menu4">
                    <form action="#" method="POST" name="reservation_set_frm">
                        <p>
                            센터의 개인레슨 이용권 예약 설정 값과 다를 시에는 해당 강사의 서비스에 대해서는 아래 설정값이 적용 됩니다.<br>
                            단, 센터의 개인 서비스 예약 설정을 다시 변경하면 아래 설정 값도 자동으로 변경되게 됩니다.
                        </p>
                        <div>
                            <h3>우선 순위 설정</h3>
                            <span>센터의 통합 설정과 강사의 개별 설정에 대한 우선 순위를 결정 합니다.</span>
                            <p>
                                <input type="radio" name="checkbox1" id="checkbox1_1" checked>
                                <span>
                                    <i class="fas fa-check"></i>
                                </span>
                                <label for="checkbox1_1">센터의 통합 설정을 적용</label>
                                <br>
                                <input type="radio" name="checkbox1" id="checkbox1_2">
                                <span>
                                    <i class="fas fa-check"></i>
                                </span>
                                <label for="checkbox1_2">강사의 개별 설정을 적용</label>
                            </p>
                        </div>
                        <div>
                            <h3>예약 시간 설정</h3>
                            <span>
                                회원이 가맹점 전용 멤버스 앱을 통하여 개인레슨 이용권을 예약할 수 있는 조건을 설정합니다.<br>
                                개인레슨 이용권의 예약 가능 시간은 현재 시간부터 설정된 시간 이후로 시간이 비어 있을 경우에만 예약이 가능합니다.<br>
                                예약 변경/취소 가능 시간이 지나면 회원은 더 이상 예약을 취소하거나 변경할 수 없습니다.
                            </span>
                            <h4>예약 가능 시간</h4>
                            <p>
                                <input type="radio" name="checkbox2" id="checkbox2_1" checked>
                                <span>
                                    <i class="fas fa-check"></i>
                                </span>
                                <label for="checkbox2_1">수업 시작 전 항상 가능</label>
                                <br>
                                <input type="radio" name="checkbox2" id="checkbox2_2">
                                <span>
                                    <i class="fas fa-check"></i>
                                </span>
                                <label for="checkbox2_2">수업 예약 가능한 시간 설정</label>
                            </p>
                            <h4>예약 변경/취소 가능 시간</h4>
                            <p>
                                <input type="radio" name="checkbox3" id="checkbox3_1" checked>
                                <span>
                                    <i class="fas fa-check"></i>
                                </span>
                                <label for="checkbox3_1">수업 시작 전 항상 가능</label>
                                <br>
                                <input type="radio" name="checkbox3" id="checkbox3_2">
                                <span>
                                    <i class="fas fa-check"></i>
                                </span>
                                <label for="checkbox3_2">예약 후 변경 및 취소 불가</label>
                                <br>
                                <input type="radio" name="checkbox3" id="checkbox3_3">
                                <span>
                                    <i class="fas fa-check"></i>
                                </span>
                                <label for="checkbox3_3">당일 취소 및 변경 불가</label>
                                <br>
                                <input type="radio" name="checkbox3" id="checkbox3_4">
                                <span>
                                    <i class="fas fa-check"></i>
                                </span>
                                <label for="checkbox3_4">수업 취소/변경 가능한 시간 설정</label>
                            </p>
                        </div>
                        <div>
                            <h3>결석 처리 기준 설정</h3>
                            <span>
                                회원이 예약한 서비스의 종료 전까지 센터에 배치된 입장 체커(터치 스크린)에서 예약한 서비스를 선택하고<br>
                                입장하지 않거나 서비스 제공자(강사)가 코치 앱을 통해 출결처리를 하지 않은 경우 익일 오전 4시에 자동 결석으로 처리됩니다.
                            </span>
                            <p>
                                <input type="checkbox" name="checkbox4" id="checkbox4_1" checked>
                                <span>
                                    <i class="fas fa-check"></i>
                                </span>
                                <label for="checkbox4_1">수업종료 시 자동결석 처리</label>
                            </p>
                        </div>
                        <div>
                            <h3>결석 시 이용권 차감 유무 설정</h3>
                            <span>
                                회원이 결석처리가 되었을 경우 예약하기 위해 사용했던 이용권의 남은 횟수가 차감되거나 차감되지 않도록 설정할 수 있습니다.<br>
                                이용권 차감으로 설정 시, 회원들에게 미리 예약하고 결석하면 남은 횟수가 차감된다는 사실을 알려주시기 바랍니다.
                            </span>
                            <h4>결석 시 이용권 차감 유무 (결석 처리 시, 이용권 차감 유무 설정은 회원이 예약했을 때 설정값이 적용됩니다.)</h4>
                            <p>
                                <input type="radio" name="checkbox5" id="checkbox5_1" checked>
                                <span>
                                    <i class="fas fa-check"></i>
                                </span>
                                <label for="checkbox5_1">이용권 차감</label>
                                <br>
                                <input type="radio" name="checkbox5" id="checkbox5_2">
                                <span>
                                    <i class="fas fa-check"></i>
                                </span>
                                <label for="checkbox5_2">이용권 미차감</label>
                            </p>
                        </div>
                        <div>
                            <h3>앱 출석기능</h3>
                            <span>
                                앱에서 강사/트레이너 선생님이 직접 출석처리할 수 있는 기능을 설정할 수 있습니다.
                            </span>
                            <p>
                                <input type="radio" name="checkbox6" id="checkbox6_1" checked>
                                <span>
                                    <i class="fas fa-check"></i>
                                </span>
                                <label for="checkbox6_1">출석처리 가능</label>
                                <br>
                                <input type="radio" name="checkbox6" id="checkbox6_2">
                                <span>
                                    <i class="fas fa-check"></i>
                                </span>
                                <label for="checkbox6_2">완료 요청만 가능</label>
                            </p>
                        </div>
                        <div>
                            <h3>회원앱 예약 기능</h3>
                            <span>
                                회원앱에서 회원님이 직접 개인레슨 예약 가능 여부를 설정할 수 있습니다.
                            </span>
                            <p>
                                <input type="radio" name="checkbox7" id="checkbox7_1" checked>
                                <span>
                                    <i class="fas fa-check"></i>
                                </span>
                                <label for="checkbox7_1">예약 가능</label>
                                <br>
                                <input type="radio" name="checkbox7" id="checkbox7_2">
                                <span>
                                    <i class="fas fa-check"></i>
                                </span>
                                <label for="checkbox7_2">예약 불가능</label>
                            </p>
                        </div>
                        <div class="btnSet">
                            <button type="submit">설정저장</button>
                            <button type="button">취소</button>
                        </div>
                    </form>

                </article>

                <article class="tab_menu5">
                    <table border="1">
                        <tr>
                            <th>직급</th>
                            <td>
                                <select name="myPosition" id="myPosition">

                                </select>
                            </td>
                            <th>팀/부서</th>
                            <td id="myTeam">-</td>
                        </tr>
                        <tr>
                            <th>직무</th>
                            <td id="myWork">-</td>
                            <th>재직구분</th>
                            <td>
                                <select name="myStatus" id="myStatus">

                                </select>
                                <input type="date" name="input_myWorkEndDate" id="input_myWorkEndDate" class="hid">
                            </td>
                        </tr>
                        <tr>
                            <th>재직기간</th>
                            <td><span id="WORKSTARTDATE">미입력</span> ~ <span id="WORKENDDATE">재직</span></td>
                            <th>입사날짜</th>
                            <td><input type="date" name="input_workStartDate" id="input_workStartDate"></td>
                        </tr>
                    </table>
                </article>

                <article class="tab_menu6">
                    <article class="money-set">
                        <h3>급여 정보</h3>
                        <div class="row1">
                            <label for="pay-month" style="display:inline-block; margin-bottom:4px;">기본급/월</label>
                            <p>
                                <input type="text" name="pay-month" id="pay-month">
                                <label for="pay-month" style="width:30px">원</label>
                            </p>
                        </div>
                        <div class="row2" style="margin-top:10px">
                            <label for="pay-my" style="display:inline-block; margin-bottom:4px;">개인 매출 커미션</label>
                            <p>
                                <input type="text" name="pay-my" id="pay-my">
                                <label for="pay-my" style="text-indent: 0px;width:20px">%</label>
                                <span>
                                    <input type="checkbox" name="pay-afterTax" id="pay-afterTax" style="width: 14px;" checked>
                                    <label for="pay-afterTax" style="font-size: 14px;margin-left: 5px;">부가세 제외 후 정산</label>
                                </span>
                            </p>
                        </div>
                    </article>

                    <article class="class-pay-set">
                        <h3>수당 정보</h3>
                        <div class="info">
                            <p><b>정액제</b> : 회원이 개인레슨 1회 출석 했을 때 지급되는 고정 수당 금액을 설정합니다.</p>
                            <p><b>정율제</b> : 회원이 개인레슨 1회 출석 했을 때 발생하는 센터의 매출(실제 결제 금액에 대한 이용권의 1회 단가)에서 수당 지급 비율(%)을 설정합니다.</p>
                        </div>
                        <div class="solo">
                            <h4>개인레슨 수당 설정</h4>
                            <p>
                                <input type="radio" name="solo-pay" id="solo-pay1" checked>
                                <label for="solo-pay1">정액제</label>
                                <input type="text" name="solo-pay1-value" id="solo-pay1-value">
                                <label for="solo-pay1-value" style="font-size: 15px;">원</label>
                            </p>
                            <p>
                                <input type="radio" name="solo-pay" id="solo-pay2">
                                <label for="solo-pay2">정율제</label>
                                <input type="text" name="solo-pay2-value" id="solo-pay2-value">
                                <label for="solo-pay2-value" style="font-size: 15px;">%</label>
                                <span style="margin-top:10px">
                                    <input type="checkbox" name="solo-pay-afterTax" id="solo-pay-afterTax_class" checked>
                                    <label for="solo-pay-afterTax_class" style="font-size: 14px;margin-left: 5px;">부가세 제외 후 정산</label>
                                </span>
                            </p>
                            <div class="solo-no-show">
                                <label for="solo-no-show">No-Show 정산방법</label>
                                <div class="select">
                                    <p>
                                        <input type="radio" name="solo-no-show-pay" id="solo-no-show-pay1" checked>
                                        <label for="solo-no-show-pay1">고객이 무단 결석으로 차감된 섹션을 출석과 동일한 수당으로 정산합니다.</label>
                                    </p>
                                    <p>
                                        <input type="radio" name="solo-no-show-pay" id="solo-no-show-pay2">
                                        <label for="solo-no-show-pay2">
                                            결석 차감된 섹션은 출석 시 지급하는 수당 금액의 
                                            <input type="text" name="solo-no-show-pay2-value" id="solo-no-show-pay2-value" style="text-align:center">
                                            <label for="solo-no-show-pay2-value">%</label>
                                            로 정산합니다.
                                        </label>
                                    </p>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="group">
                            <h4>그룹레슨 수당 설정</h4>
                            <p>
                                <input type="checkbox" name="group-pay-afterTax" id="group-pay-afterTax_class" checked>
                                <label for="group-pay-afterTax_class" style="font-size: 14px;margin-left: 5px;">부가세 제외 후 정산</label>
                            </p>
                            <div class="group-no-show">
                                <label for="group-no-show">No-Show 정산방법</label>
                                <div class="select">
                                    <p>
                                        <input type="radio" name="group-no-show-pay" id="group-no-show-pay1">
                                        <label for="group-no-show-pay1">고객이 무단 결석으로 차감된 섹션을 출석과 동일한 수당으로 정산합니다.</label>
                                    </p>
                                    <p>
                                        <input type="radio" name="group-no-show-pay" id="group-no-show-pay2">
                                        <label for="group-no-show-pay2">
                                            결석 차감된 섹션은 출석 시 지급하는 수당 금액의 
                                            <input type="text" name="group-no-show-pay2-value" id="group-no-show-pay2-value" style="text-align:center">
                                            <label for="group-no-show-pay2-value">%</label>
                                            로 정산합니다.
                                        </label>
                                    </p>
                                    
                                </div>
                            </div>
                        </div>
                    </article>
                    <article>
                        <button id="trainerPayInfoSaveBtn">저 장</button>
                    </article>
                    
                </article>
            </div>
        </section>

    </div>

    <?php require_once 'lib/footer.php'; ?>
  
</body>
</html>