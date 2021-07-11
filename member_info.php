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
    <title>닥터케어유니온 - 회원정보</title>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/member_info.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css">
    <script src="js/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
    <script src="js/Array.js"></script>
    <script src="js/jswLib.js"></script>
    <script src="js/script.js"></script>
    <script src="js/member_info.js"></script>
    <script type="text/javascript">
		member_register = "<?=getAnyParameter("member_register","")?>";
	</script>

</head>
<body>
    <script>
        USER_SEQ = '<?=getAnyParameter("u_seq","")?>';
    </script>
    <div id="wrap">
        
    <?php require_once 'lib/header.php'; ?>

        <section class="content">
            <div class="top_sMenu"><i class="fas fa-arrow-left back"></i>회원정보</div>
            <button id="buy_ticket_btn"><i class="fas fa-ticket-alt"></i> 이용권 구매</button>
            <button id="pw_change_btn"><i class="fas fa-key"></i> 비밀번호 변경</button>
            <button id="member_del" onclick=""><i class="far fa-trash-alt"></i> 회원삭제</button>
            <div class="up">
                <div class="u_img">
                    <img class="user_face" src="img/user.png" alt="회원사진">
                    <label for="user-face"><i class="fas fa-camera"></i> 사진변경</label>
                    <input type="file" name="user-face" id="user-face" accept="image/*">
                </div>
                <div class="info_s">
                    <p class="u_name">
                        <i class="fas fa-signature"></i>
                        <span class="name"></span>
                        <span class="editForm">
                            <input type="text" name="edit_name" id="edit_name" class="editData" autocomplete="off">
                        </span>
                        <button class="editBtn">수정</button>
                    </p>
                    <p class="u_gender">
                        <i class="fas fa-venus-mars"></i>
                        <span class="gender"></span>
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
                        <span class="num"></span>
                        <span class="editForm">
                            <input type="text" name="edit_num" id="edit_num" class="editData" autocomplete="off">
                        </span>
                        <button class="editBtn">수정</button>
                    </p>
                    <p class="u_year">
                        <i class="far fa-calendar-alt"></i>
                        <span class="y"></span>
                        <span class="age"></span>
                        <span class="editForm">
                            <input type="date" name="edit_year" id="edit_year" class="editData" autocomplete="off">
                        </span>
                        <button class="editBtn">수정</button>
                    </p>
                    <p class="u_email">
                        <i class="far fa-envelope-open"></i>
                        <span class="email"></span>
                        <span class="editForm">
                            <input type="text" name="edit_email" id="edit_email" class="editData" autocomplete="off">
                        </span>
                        <button class="editBtn">수정</button>
                    </p>
                    <p>
                    <!-- <p class="u_center"> -->
                        <!-- <i class="fas fa-house-user"></i>
                        <span class="center"></span>
                        <span class="editForm">
                            <select name="edit_center" id="edit_center" class="editData" autocomplete="off">
                                <option value="1">닥터케어(본점)</option>
                            </select>
                        </span>
                        <button class="editBtn">수정</button> -->
                    </p>
                </div>
            </div>
            <div class="down">
                <div class="tab_btn">
                    <button class="active">회원정보</button>
                    <button>건강관리 데이터</button>
                </div>

                <div class="tabContainer">
                    <!-- 회원정보 -->
                    <article class="tabPage0">
                        <h3 class="hid">회원정보</h3>
                        <div class="card1">
                            <h4>개인정보</h4>
                            <div class="content x">
                                <table>
                                    <tr>
                                        <th>회원번호</th>
                                        <td class="member_seq"></td>
                                    </tr>
                                    <tr>
                                        <th>등록일</th>
                                        <td class="member_joinDate"></td>
                                    </tr>
                                    <tr>
                                        <th>담당자</th>
                                        <td class="member_teacher">
                                            <select id="member_manager">
                                                <option value="">담당자 미지정</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>회원용APP</th>
                                        <td class="member_appUse"></td>
                                    </tr>
                                    <tr>
                                        <th>라커번호</th>
                                        <td class="member_rockerNum"></td>
                                    </tr>
                                    <tr>
                                        <th>바코드 번호</th>
                                        <td class="member_barcode"></td>
                                    </tr>
                                    <tr>
                                        <th>메모</th>
                                        <td><button type="button" id="memoSet">메모수정</button></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <textarea name="memo" id="memo" rows="5" disabled="true" class="member_memo"></textarea>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="card2">
                            <h4>구매이용권</h4>
                            <div class="content">

                                <!-- <article class="card">
                                    <h5>        
                                        <span></span>
                                        <span></span>
                                        이용권
                                        <p>사용중</p>
                                    </h5>
                                    <div>
                                        <p>P.T 이용권 (1개월 10회)</p>
                                        <p>담당강사 <span>박해성</span></p>
                                        <p>
                                            2020.08.11 ~ 2020.09.11<br>
                                            이용일수 20/200일 · 이용횟수 8/60회 · 예약건수 6회
                                        </p>
                                    </div>
                                    <i class="fas fa-wrench"> 옵션</i>
                                    <div class="optionBg">
                                        <div id="dateTime">
                                            <i class="far fa-calendar-minus"></i>
                                            <span>기간 횟수<br>조정</span>
                                        </div>
                                        <div id="useList">
                                            <i class="far fa-file-alt"></i>
                                            <span>사용내역<br>보기</span>
                                        </div>
                                        <div id="changeTeacher">
                                            <i class="fas fa-chalkboard-teacher"></i>
                                            <span>강사 변경</span>
                                        </div>
                                        <div id="for_ticketing">
                                            <i class="fas fa-user-clock"></i>
                                            <span>일괄 예약</span>
                                        </div>
                                        <div id="setClose">
                                            <i class="fas fa-times"></i>
                                            <span>닫 기</span>
                                        </div>
                                    </div>
                                </article> -->

                            </div>
                        </div>
                        <div class="card3">
                            <h4>히스토리</h4>
                            <div class="content x">
                                <table>
                                    <tr>
                                        <td>
                                            <div class="wrap">
                                                <div class="up">
                                                    <p>담당강사 변경</p>
                                                    <p>2021-05-17 11:14:51</p>
                                                </div>
                                                <div class="down">
                                                    <span class="member">전상욱 test(010-4722-9330,23세)</span>회원님의 이용권의 담당강사를 <span class="trainer">박종남</span>트레이너로 변경하였습니다.
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </article>

                    <!-- 건강관리 데이터 -->
                    <article class="tabPage1">
                        <h3 class="hid">건강관리 데이터</h3>
                        <div class="card4">
                            <h4>신체정보</h4>
                            <div class="date_choice">
                                <h5>측정일</h5>
                                <button class="WriteFrm_Btn" id="bodyInfo_WriteFrm_Btn">직접입력</button>
                                <select name="check_date_body" id="check_date_body" data-measurement_type="BODY" class="DB_DATE_OUTPUT">
                                    <option name="date" value="20200829">2020.08.29</option>
                                    <option name="date" value="20200831">2020.08.31</option>
                                    <option name="date" value="20200902">2020.09.02</option>
                                </select>
                            </div>
                            <div class="content">
                                <table>
                                    <tr>
                                        <th>항목</th>
                                        <th>입력</th>
                                    </tr>
                                    <tr>
                                        <td>신장</td>
                                        <td id="u_HEIGHT" class="data">
                                            <span>180 Cm</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>체중</td>
                                        <td id="u_WEIGHT" class="data">
                                            <span>70 Kg</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>체지방량</td>
                                        <td id="u_FAT" class="data">
                                            <span>23 Kg</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>근육량</td>
                                        <td id="u_MUSCLE" class="data">
                                            <span>20 Kg</span>
                                        </td>
                                    </tr>
                                    <tr class="tr_btn">
                                        <td colspan="2">
                                            <button type="button" class="Open_pop" title="신체정보자세히">
                                                자세히 보기
                                            </button>
                                            <button type="button" class="Open_pop" title="신체정보변화도">
                                                변화도 그래프 보기
                                            </button>
                                        </td> 
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="card5 bodyinfo">
                            <h4>체형정보</h4>
                            <div class="date_choice">
                                <h5>측정일</h5>
                                <select name="check_date_pose" id="check_date_pose" data-measurement_type="POSE" class="DB_DATE_OUTPUT">
                                    <option name="date" value="20200829">2020.08.29</option>
                                    <option name="date" value="20200831">2020.08.31</option>
                                    <option name="date" value="20200902">2020.09.02</option>
                                </select>
                            </div>
                            <div class="content">
                                <table>
                                    <tr>
                                        <th rowspan="2" style="height:30px">부위</th>
                                        <th colspan="2" class="h25">정면</th>
                                        <th colspan="2" class="h25">측면</th>
                                    </tr>
                                    <tr>
                                        <th class="h25">Right</th>
                                        <th class="h25">Left</th>
                                        <th class="h25">Front</th>
                                        <th class="h25">Back</th>
                                    </tr>
                                    <tr>
                                        <td>목</td>
                                        <td colspan="2" id="u_FRONT_HEAD_POSE" class="data"><i class="fas fa-arrow-left"></i><span>0.1˚</span></td>
                                        <td colspan="2" id="u_SIDE_HEAD_POSE" class="data"><i class="fas fa-arrow-left"></i><span>0.1˚</span></td>
                                    </tr>
                                    <tr>
                                        <td>어깨</td>
                                        <td id="u_FRONT_SHOULDER_RIGHT_POSE" class="data"><i class="fas fa-arrow-down"></i><span>0.2˚</span></td>
                                        <td id="u_FRONT_SHOULDER_LEFT_POSE" class="data"><span>1.2˚</span><i class="fas fa-arrow-down"></i></td>
                                        <td colspan="2" id="u_SIDE_SHOULDER_POSE" class="data"><i class="fas fa-arrow-down"></i><span>0.1˚</span></td>
                                    </tr>
                                    <tr>
                                        <td>골반</td>
                                        <td id="u_FRONT_PELVIS_RIGHT_POSE" class="data"><i class="fas fa-arrow-down"></i><span>0.0˚</span></td>
                                        <td id="u_FRONT_PELVIS_LEFT_POSE" class="data"><span>0.1˚</span><i class="fas fa-arrow-down"></i></td>
                                        <td colspan="2" id="u_SIDE_PELVIS_POSE" class="data"><i class="fas fa-arrow-down"></i><span>0.0˚</span></td>
                                    </tr>
                                    <tr>
                                        <td>다리</td>
                                        <td id="u_FRONT_LEG_RIGHT_POSE" class="data"><i class="fas fa-arrow-left"></i><span>0.2˚</span></td>
                                        <td id="u_FRONT_LEG_LEFT_POSE" class="data"><span>0.3˚</span><i class="fas fa-arrow-right"></i></td>
                                        <td colspan="2" id="u_SIDE_LEG_POSE" class="data"><i class="fas fa-arrow-left"></i><span>1.2˚</span></td>
                                    </tr>
                                    <tr class="tr_btn">
                                        <td colspan="5">
                                            <button type="button" class="Open_pop" title="체형정보자세히">
                                                자세히 보기
                                            </button>
                                            <button type="button" class="Open_pop" title="체형정보변화도">
                                                변화도 그래프 보기
                                            </button>
                                        </td> 
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="card6 bodyinfo">
                            <h4>ROM정보</h4>
                            <div class="date_choice">
                                <h5>측정일</h5>
                                <select name="check_date_rom" id="check_date_rom" data-measurement_type="ROM" class="DB_DATE_OUTPUT">
                                    <option name="date" value="20200829">2020.08.29</option>
                                    <option name="date" value="20200831">2020.08.31</option>
                                    <option name="date" value="20200902">2020.09.02</option>
                                </select>
                            </div>
                            <div class="content">
                                <table>
                                    <tr>
                                        <th rowspan="2" class="h25">부위</th>
                                        <th colspan="2" class="h25">정면</th>
                                        <th colspan="2" class="h25">측면</th>
                                    </tr>
                                    <tr>
                                        <th class="h25">Right</th>
                                        <th class="h25">Left</th>
                                        <th class="h25">Front</th>
                                        <th class="h25">Back</th>
                                    </tr>
                                    <tr>
                                        <td>목</td>
                                        <td id="u_FRONT_HEAD_RIGHT_ROM" class="data">40.0˚</td>
                                        <td id="u_FRONT_HEAD_LEFT_ROM" class="data">33.7˚</td>
                                        <td id="u_SIDE_HEAD_FRONT_ROM" class="data">32.0˚</td>
                                        <td id="u_SIDE_HEAD_BACK_ROM" class="data">21.1˚</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div style="width:65%;height:100%;float:left;line-height:61px;border-right:1px solid var(--mainWW)">어깨</div>    
                                            <div style="width:35%;height:100%;float:right;">
                                                <div style="height:50%;line-height:30.5px;font-size:13px;">[R]</div>
                                                <div style="height:50%;line-height:30.5px;border-top:1px solid var(--mainWW);font-size :14px;">[L]</div>
                                            </div>
                                        </td>
                                        <td id="u_FRONT_SHOULDER_RIGHT_ROM" class="data">173.9˚</td>
                                        <td id="u_FRONT_SHOULDER_LEFT_ROM" class="data">179.3˚</td>
                                        <td id="u_SIDE_SHOULDER_FRONT_ROM" class="data">
                                            [R] 183.9˚<br>
                                            [L] 183.9˚
                                        </td>
                                        <td id="u_SIDE_SHOULDER_BACK_ROM" class="data">
                                            [R] 79.1˚<br>
                                            [L] 79.1˚
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>허리</td>
                                        <td id="u_FRONT_PELVIS_RIGHT_ROM" class="data">40.5˚</td>
                                        <td id="u_FRONT_PELVIS_LEFT_ROM" class="data">33.7˚</td>
                                        <td id="u_SIDE_PELVIS_FRONT_ROM" class="data">82.1˚</td>
                                        <td id="u_SIDE_PELVIS_BACK_ROM" class="data">33.7˚</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div style="width:65%;height:100%;float:left;line-height:61px;border-right:1px solid var(--mainWW)">다리</div>    
                                            <div style="width:35%;height:100%;float:right;">
                                                <div style="height:50%;line-height:30.5px;font-size:13px;">[R]</div>
                                                <div style="height:50%;line-height:30.5px;border-top:1px solid var(--mainWW);font-size :14px;">[L]</div>
                                            </div>
                                        </td>
                                        <td id="u_FRONT_LEG_RIGHT_ROM" class="data">45.0˚</td>
                                        <td id="u_FRONT_LEG_LEFT_ROM" class="data">42.0˚</td>
                                        <td id="u_SIDE_LEG_FRONT_ROM" class="data">
                                            [R] 39.1˚<br>
                                            [L] 35.8˚
                                        </td>
                                        <td id="u_SIDE_LEG_BACK_ROM" class="data">
                                            [R] 49.3˚<br>
                                            [L] 49.5˚
                                        </td>
                                    </tr>
                                    <tr class="tr_btn">
                                        <td colspan="5">
                                            <button type="button" class="Open_pop" title="ROM정보자세히">
                                                자세히 보기
                                            </button>
                                            <button type="button" class="Open_pop" title="ROM정보변화도">
                                                변화도 그래프 보기
                                            </button>
                                        </td> 
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="card7 desa">
                            <h4>대사질환정보</h4>
                            <div class="date_choice">
                                <h5>측정일</h5>
                                <button class="WriteFrm_Btn" id="desaInfo_WriteFrm_Btn">직접입력</button>
                                <select name="check_date_desa" id="check_date_desa" data-measurement_type="DESA" class="DB_DATE_OUTPUT">
                                    <option name="date" value="20200829">2020.08.29</option>
                                    <option name="date" value="20200831">2020.08.31</option>
                                    <option name="date" value="20200902">2020.09.02</option>
                                </select>
                            </div>
                            <div class="content">
                                <table>
                                    <tr>
                                        <th>항목</th>
                                        <th>정상범위</th>
                                        <th colspan="2">결과</th>
                                    </tr>
                                    <tr>
                                        <td>안정시 심박수 (HR)</td>
                                        <td>60 ~ 70</td>
                                        <td id="u_DESA_DATA0" class="data">67</td>
                                        <td class="data"><span class="g"></span></td>
                                    </tr>
                                    <tr style="height: 30px;">
                                        <td>혈압 (SBP/DBP)</td>
                                        <td>90 &lt 120 / 60 &lt 80</td>
                                        <td id="u_DESA_DATA1" class="data">128/87</td>
                                        <td class="data"><span class="y"></span></td>
                                    </tr>
                                    <tr>
                                        <td>혈당 (Glucose)</td>
                                        <td>70 &lt 100</td>
                                        <td id="u_DESA_DATA2" class="data">98</td>
                                        <td class="data"><span class="y"></span></td>
                                    </tr>
                                    <tr>
                                        <td>당화혈색소 (HbA1c)</td>
                                        <td>&lt 5.6</td>
                                        <td id="u_DESA_DATA3" class="data">3.8</td>
                                        <td class="data"><span class="g"></span></td>
                                    </tr>
                                    <tr>
                                        <td>총콜레스테롤 (TC)</td>
                                        <td>&lt 200</td>
                                        <td id="u_DESA_DATA4" class="data">165</td>
                                        <td class="data"><span class="r"></span></td>
                                    </tr>
                                    <tr>
                                        <td>콜레스테롤 (HDL/LDL)</td>
                                        <td>40 ~ 60 / &lt 100</td>
                                        <td id="u_DESA_DATA5" class="data">45 / 136</td>
                                        <td class="data"><span class="y"></span></td>
                                    </tr>
                                    <tr>
                                        <td>중성지방 (TG)</td>
                                        <td>&lt 150</td>
                                        <td id="u_DESA_DATA6" class="data">93</td>
                                        <td class="data"><span class="g"></span></td>
                                    </tr>
                                    <tr>
                                        <td>젖산 (Lactate)</td>
                                        <td>0.5 ~ 2.0</td>
                                        <td id="u_DESA_DATA7" class="data">0.6</td>
                                        <td class="data"><span class="g"></span></td>
                                    </tr>
                                    <tr class="tr_btn">
                                        <td colspan="5">
                                            <button type="button" class="" title="대사질환정보자세히">
                                                자세히 보기
                                            </button>
                                            <button type="button" class="" title="대사질환정보변화도">
                                                변화도 그래프 보기
                                            </button>
                                        </td> 
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </section>
    </div>

    <!-- 신체정보 자세히 보기 -->
    <div class="pop">
        <h2>신체정보 상세보기</h2>
        <div class="content">
            <article class="pop1">
                <h3>신체구성 검사 상세결과</h3>
                <!-- <img src="img/inbody.jpg" alt="인바디 정보"> -->
                <div class="left">
                    <h4>신체구성 변화</h4>
                    <div class="con">
                        <div class="changeText1">
                            <h5>체중 변화량</h5>
                            <p class="red">00 Kg <i class="fas fa-caret-up"></i></p>
                        </div>
                        <div class="changeText2">
                            <h5>체지방 변화량</h5>
                            <p class="red">00 Kg <i class="fas fa-caret-up"></i></p>
                        </div>
                        <div class="changeText3">
                            <h5>근육 변화량</h5>
                            <p class="blue">00 Kg <i class="fas fa-caret-down"></i></p>
                        </div>
                        <div class="changeText4">
                            <h5>기초대사량</h5>
                            <p class="blue">00 Kcal <i class="fas fa-caret-down"></i></p>
                        </div>
                    </div>
                </div>
                <div class="right">
                    <h4>신체구성 상태</h4>
                    <p>
                        <label for="inbody_form_date">날짜선택</label>
                        <select name="inbody_form_date" id="inbody_form_date" class="DB_DATE_OUTPUT" data-measurement_type="BODY_DETAIL">
                            <option value="">선택</option>
                            <option value="2020-12-01">2020-12-01</option>
                            <option value="2020-12-02">2020-12-02</option>
                            <option value="2020-12-03">2020-12-03</option>
                            <option value="2020-12-04">2020-12-04</option>
                        </select>
                    </p>
                    <div class="con">
                        <div>
                            <p>Under</p>
                            <p>Normal</p>
                            <p>Over</p>
                        </div>
                        <table>
                            <tbody>
                                <tr>
                                    <th style="width: 90px;">신장</th>
                                    <td style="width: 120px;" class="myBody_HEIGHT">70 Kg</td>
                                    <td>
                                        <div class="circle"></div>
                                        <div class="bar"></div>
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>체중</th>
                                    <td style="width: 120px;" class="myBody_WEIGHT">70 Kg</td>
                                    <td>
                                        <div class="circle"></div>
                                        <div class="bar"></div>
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>BMI</th>
                                    <td class="myBody_BMI">25 Kg</td>
                                    <td>
                                        <div class="circle"></div>
                                        <div class="bar"></div>
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>체지방량</th>
                                    <td class="myBody_FAT">14.0 %</td>
                                    <td>
                                        <div class="circle"></div>
                                        <div class="bar"></div>
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>근육량</th>
                                    <td class="myBody_MUSCLE">21 Kg</td>
                                    <td>
                                        <div class="circle"></div>
                                        <div class="bar"></div>
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </article>
            <article class="pop2">
                <h3 class="hid">신체정보변화도</h3>
                <div class="btn">
                    <button data-date="all" class="active">전체</button>
                    <button data-date="3m">3개월</button>
                    <button data-date="6m">6개월</button>
                    <button data-date="12m">12개월</button>
                </div>
                <div class="part_btn">
                    <button data-what="HEIGHT" class="active">신장</button>
                    <button data-what="WEIGHT">체중</button>
                    <button data-what="BMI">BMI</button>
                    <button data-what="FAT">체지방량</button>
                    <button data-what="MUSCLE">근육량</button>
                </div>

                <!-- 차트 -->
                <canvas id="myChart1" width="900" height="650"></canvas>
                    
            </article>

            <article class="pop3">
                <h3 class="hid">체형정보자세히</h3>
                <div class="info_btn">
                    <button class="active">체형검사 상세결과</button>
                    <button>체형검사 비교결과</button>
                </div>
                <button class="errorBodyView">문제근육상세보기</button>
                <div class="info">
                    <section class="pose_info">
                        <h4><i class="far fa-camera"></i> 전신측정 결과</h4>
                        <p>
                            <label for="pose_date">날짜선택</label>
                            <select name="pose_date" id="pose_date" class="DB_DATE_OUTPUT" data-measurement_type="POSE_DETAIL">
                                <!-- js -->
                            </select>
                            <button id="pose_date_del" data-type="pose">삭제</button>
                        </p>
                        <div>
                            <div title="체형검사정면사진"></div>
                            <div title="체형검사측면사진"></div>
                            <table>
                                <thead>
                                    <tr>
                                        <td colspan="6"><i class="fas fa-table"></i> 측정 결과표</td>
                                    </tr>
                                    <tr>
                                        <th colspan="3">Front</th>
                                        <th colspan="3">Side</th>
                                    </tr>
                                    <tr>
                                        <th>Part</th>
                                        <th>Right</th>
                                        <th>Left</th>
                                        <th>Part</th>
                                        <th>Front</th>
                                        <th>Back</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>목</th>
                                        <td id="front-neck-" class="data" colspan="2"><span>0.1˚</span><i class="fas fa-arrow-left"></i></td>
                                        <th>목</th>
                                        <td id="side-neck-" class="data" colspan="2"><span>1.2˚</span><i class="fas fa-arrow-right"></i></td>
                                    </tr>
                                    <tr>
                                        <th>어깨</th>
                                        <td id="front-shoulder-right" class="data"><span>0.2˚</span><i class="fas fa-arrow-up"></i></td>
                                        <td id="front-shoulder-left" class="data"><span>1.2˚</span><i class="fas fa-arrow-up"></i></td>
                                        <th>어깨</th>
                                        <td id="side-shoulder-" class="data" colspan="2"><span>2.3˚</span><i class="fas fa-arrow-up"></i></td>
                                    </tr>
                                    <tr>
                                        <th>골반</th>
                                        <td id="front-pelvis-right" class="data"><span>2.2˚</span><i class="fas fa-arrow-up"></i></td>
                                        <td id="front-pelvis-left" class="data"><span>0.4˚</span><i class="fas fa-arrow-up"></i></td>
                                        <th>골반</th>
                                        <td id="side-pelvis-" class="data" colspan="2"><span>0.0˚</span><i class="fas fa-arrow-up"></i></td>
                                    </tr>
                                    <tr>
                                        <th>다리</th>
                                        <td id="front-leg-right" class="data"><span>10.3˚</span><i class="fas fa-arrow-up"></i></td>
                                        <td id="front-leg-left" class="data"><span>15.2˚</span><i class="fas fa-arrow-up"></i></td>
                                        <th>다리</th>
                                        <td id="side-leg-" class="data" colspan="2"><span>2.3˚</span><i class="fas fa-arrow-up"></i></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <section class="pose_change">
                        <!-- <h4>비교결과</h4> -->
                        <h4><i class="far fa-camera"></i> 비교 결과</h4>
                        <div class="option">
                            <button class="active">정면</button>
                            <button>측면</button>

                            <label for="before-date">이전 측정일</label>
                            <select name="before-date" id="before-date" class="DB_DATE_OUTPUT" data-measurement_type="POSE_COMPARE_BEFORE">

                            </select>
                            <label for="after-date">최근 측정일</label>
                            <select name="after-date" id="after-date" class="DB_DATE_OUTPUT" data-measurement_type="POSE_COMPARE_AFTER">

                            </select>
                        </div>
                        <div class="content">
                            <section class="img">
                                <h5 class="hid">이미지영역</h5>
                                <article class="last">
                                    <h6>이전측정일</h6>
                                    <div title="체형검사이전측정일"></div>
                                </article>
                                <article class="now">
                                    <h6 class="active">최근측정일</h6>
                                    <div title="체형검사최근측정일"></div>
                                </article>
                            </section>
                            <section class="table">
                                <h5><i class="fas fa-table"></i> 측정 결과표</h5>
                                <table border="1">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" style="width: 110px;">Direction</th>
                                            <th rowspan="2" style="width: 110px;">Part</th>
                                            <th colspan="2" class="tableDate before">2020.05.05</th>
                                            <th colspan="2" class="tableDate after">2020.09.07</th>
                                        </tr>
                                        <tr>
                                            <th class="part1" style="width: 94.75px;">Right</th>
                                            <th class="part2" style="width: 94.75px;">Left</th>
                                            <th class="part3" style="width: 94.75px;">Right</th>
                                            <th class="part4" style="width: 94.75px;">Left</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th rowspan="4" class="FB_change">정면</th>
                                            <th>목</th>
                                            <td id="neck_before" colspan="2" class="data"><i class="fas fa-arrow-right"></i> 1.2˚</td>
                                            <td id="neck_after" colspan="2" class="data">1.0˚ <span>(<i class="fas fa-arrow-left">0.2</i>)</span></td>
                                        </tr>
                                        <tr>
                                            <th>어깨</th>
                                            <td id="shoulder_before1" class="col-hop data"><i class="fas fa-arrow-up"></i> 2.2˚</td>
                                            <td id="shoulder_before2" class="data"><i class="fas fa-arrow-down"></i> 2.0˚</td>
                                            <td id="shoulder_after1" class="col-hop data">2.0˚ <span>(<i class="fas fa-arrow-down">0.2</i>)</span></td>
                                            <td id="shoulder_after2" class="data">2.0˚ <span>(<i class="fas fa-arrow-up">4</i>)</span></td>
                                        </tr>
                                        <tr>
                                            <th>골반</th>
                                            <td id="pelvis_before1" class="col-hop data"><i class="fas fa-arrow-down"></i> 1.0˚</td>
                                            <td id="pelvis_before2" class="data"><i class="fas fa-arrow-up"></i> 1.0˚</td>
                                            <td id="pelvis_after1" class="col-hop data">1.2˚ <span>(<i class="fas fa-arrow-down">0.2</i>)</span></td>
                                            <td id="pelvis_after2" class="data">1.1˚ <span>(<i class="fas fa-arrow-up">0.1</i>)</span></td>
                                        </tr>
                                        <tr>
                                            <th>다리</th>
                                            <td id="leg_before1" class="col-hop data"><i class="fas fa-arrow-up"></i> 0.1˚</td>
                                            <td id="leg_before2" class="data"><i class="fas"></i> 0.0˚</td>
                                            <td id="leg_after1" class="col-hop data">0.9˚ <span>(<i class="fas fa-arrow-down">1.0</i>)</span></td>
                                            <td id="leg_after2" class="data">1.0˚ <span>(<i class="fas fa-arrow-up">1.0</i>)</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </section>
                        </div>
                    </section>
                </div>
            </article>

            <article class="pop4">
                <h3 class="hid">체형정보변화도</h3>
                <div class="btn">
                    <button data-date="all" class="active date">전체</button>
                    <button data-date="3m" class="date">3개월</button>
                    <button data-date="6m" class="date">6개월</button>
                    <button data-date="12m" class="date">12개월</button>
                </div>
                <div class="part_btn">
                    <select name="f_b-choice" id="f_b-choice">
                        <option value="front">정면</option>
                        <option value="side">측면</option>
                    </select>
                    <button data-what="Neck" class="active position">목</button>
                    <button data-what="Shoulder" class="position">어깨</button>
                    <button data-what="Pelvis" class="position">골반</button>
                    <button data-what="Leg" class="position">다리</button>
                </div>

                <!-- 차트 -->
                <canvas id="myChart2" width="900" height="650"></canvas>
                    
            </article>

            <article class="pop5">
                <h3 class="hid">ROM정보자세히</h3>
                <div class="info_btn">
                    <button class="active">ROM검사 상세결과</button>
                    <button>ROM검사 비교결과</button>
                </div>
                <div class="info">
                    <section class="rom_info">
                        <!-- <h4><i class="far fa-camera"></i> 아바타</h4> -->
                        <p>
                            <button class="active">정면</button>
                            <button>측면</button>
                            <label for="rom_date">날짜선택</label>
                            <select name="rom_date" id="rom_date" class="DB_DATE_OUTPUT" data-measurement_type="ROM_DETAIL">
                                <!-- js -->
                            </select>
                            <button id="rom_date_del" data-type="rom">삭제</button>
                        </p>
                        <div>
                            <table>
                                <thead>
                                    <tr>
                                        <td colspan="5"><i class="fas fa-table"></i> 측정 결과표</td>
                                    </tr>
                                    <tr>
                                        <th colspan="5" style="color: #fff;">ROM Result</th>
                                    </tr>
                                    <tr>
                                        <th style="width: 160px;">Part</th>
                                        <th style="width: 120px;" class="front">Right</th>
                                        <th style="width: 0;">pain</th>
                                        <th style="width: 120px;" class="back">Left</th>
                                        <th style="width: 0;">pain</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- 정면 -->
                                    <tr>
                                        <th>목</th>
                                        <td id="ROM-front-neck-right" class="data numData"><span>40.0˚</span></td>
                                        <td id="ROM-front-neck-right-pain" class="data">
                                            <img src="img/good.png" alt="pain아이콘">                                                        
                                        </td>
                                        <td id="ROM-front-neck-left" class="data numData"><span>33.7˚</span></td>
                                        <td id="ROM-front-neck-left-pain" class="data">
                                            <img src="img/normal.png" alt="pain아이콘">                                                        
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>어깨</th>
                                        <td id="ROM-front-shoulder-right" class="data numData"><span>173.9˚</span></td>
                                        <td id="ROM-front-shoulder-right-pain" class="data">
                                            <img src="img/normal.png" alt="pain아이콘">                                                        
                                        </td>
                                        <td id="ROM-front-shoulder-left" class="data numData"><span>179.3˚</span></td>
                                        <td id="ROM-front-shoulder-left-pain" class="data">
                                            <img src="img/good.png" alt="pain아이콘">                                                        
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>허리</th>
                                        <td id="ROM-front-pelvis-right" class="data numData"><span>21.5˚</span></td>
                                        <td id="ROM-front-pelvis-right-pain" class="data">
                                            <img src="img/bad.png" alt="pain아이콘">                                                        
                                        </td>
                                        <td id="ROM-front-pelvis-left" class="data numData"><span>27.1˚</span></td>
                                        <td id="ROM-front-pelvis-left-pain" class="data">
                                            <img src="img/normal.png" alt="pain아이콘">                                                    
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>다리</th>
                                        <td id="ROM-front-leg-right" class="data numData"><span>45.0˚</span></td>
                                        <td id="ROM-front-leg-right-pain" class="data">
                                            <img src="img/good.png" alt="pain아이콘">                                                        
                                        </td>
                                        <td id="ROM-front-leg-left" class="data numData"><span>42.0˚</span></td>
                                        <td id="ROM-front-leg-left-pain" class="data">
                                            <img src="img/good.png" alt="pain아이콘">                                                        
                                        </td>
                                    </tr>
                                    <!-- 측면 .eq(4) -->
                                    <tr>
                                        <th>목</th>
                                        <td id="ROM-side-neck-front" class="data numData"><span>60.0˚</span></td>
                                        <td id="ROM-side-neck-front-pain" class="data">
                                            <img src="img/good.png" alt="pain아이콘">                                                        
                                        </td>
                                        <td id="ROM-side-neck-back" class="data numData"><span>15.2˚</span></td>
                                        <td id="ROM-side-neck-back-pain" class="data">
                                            <img src="img/bad.png" alt="pain아이콘">                                                        
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="Shoulder-side-table-th" style="position:relative;">
                                        <span style="position: absolute; top: 50%; left: calc(50% - 18px); transform: translate(-50%,-50%);">어깨</span>
                                            <div style="width:36px;height:100px;float:right">
                                                <div style="height:50%; line-height:unset; position:relative; border-left:1px solid var(--mainWW); border-bottom: 1px solid var(--mainWW);"><span style="position:absolute; top:50%; left: 50%; transform: translate(-50%, -50%); ">[L]</span></div>
                                                <div style="height:50%; line-height:unset; position:relative; border-left:1px solid var(--mainWW); "><span style="position:absolute; top:50%; left: 50%; transform: translate(-50%, -50%); ">[R]</span></div>
                                            </div>
                                        </th>
                                        <td id="ROM-side-shoulder-left-front" class="data numData"><span>179.6˚</span></td>
                                        <td id="ROM-side-shoulder-left-front-pain" class="data">
                                            <img src="img/good.png" alt="pain아이콘">
                                        </td>
                                        <td id="ROM-side-shoulder-left-back" class="data numData"><span>50.2˚</span></td>
                                        <td id="ROM-side-shoulder-left-back-pain" class="data">
                                            <img src="img/good.png" alt="pain아이콘">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="Shoulder-side-display-none" style="border-top:none">어깨 (R)</th>
                                        <td id="ROM-side-shoulder-right-front" class="data numData"><span>179.9˚</span></td>
                                        <td id="ROM-side-shoulder-right-front-pain" class="data">
                                            <img src="img/good.png" alt="pain아이콘">                                                        
                                        </td>
                                        <td id="ROM-side-shoulder-right-back" class="data numData"><span>60.0˚</span></td>
                                        <td id="ROM-side-shoulder-right-back-pain" class="data">
                                            <img src="img/good.png" alt="pain아이콘">                                                        
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>허리</th>
                                        <td id="ROM-side-pelvis-front" class="data numData"><span>61.8˚</span></td>
                                        <td id="ROM-side-pelvis-front-pain" class="data">
                                            <img src="img/good.png" alt="pain아이콘">                                                        
                                        </td>
                                        <td id="ROM-side-pelvis-back" class="data numData"><span>25.0˚</span></td>
                                        <td id="ROM-side-pelvis-back-pain" class="data">
                                            <img src="img/good.png" alt="pain아이콘">                                                    
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="Shoulder-side-table-th" style="position:relative;">
                                        <span style="position: absolute; top: 50%; left: calc(50% - 18px); transform: translate(-50%,-50%);">다리</span>
                                            <div style="width:36px;height:100px;float:right">
                                                <div style="height:50%; line-height:unset; position:relative; border-left:1px solid var(--mainWW); border-bottom: 1px solid var(--mainWW);"><span style="position:absolute; top:50%; left: 50%; transform: translate(-50%, -50%); ">[L]</span></div>
                                                <div style="height:50%; line-height:unset; position:relative; border-left:1px solid var(--mainWW); "><span style="position:absolute; top:50%; left: 50%; transform: translate(-50%, -50%); ">[R]</span></div>
                                            </div>
                                        </th>
                                        <td id="ROM-side-leg-left-front" class="data numData"><span>40.7˚</span></td>
                                        <td id="ROM-side-leg-left-front-pain" class="data">
                                            <img src="img/bad.png" alt="pain아이콘">                                                        
                                        </td>
                                        <td id="ROM-side-leg-left-back" class="data numData"><span>30.0˚</span></td>
                                        <td id="ROM-side-leg-left-back-pain" class="data">
                                            <img src="img/good.png" alt="pain아이콘">                                                        
                                        </td>
                                    </tr>
                                    <tr>
                                        <td id="ROM-side-leg-right-front" class="data"><span>74.2˚</span></td>
                                        <td id="ROM-side-leg-right-front-pain" class="data">
                                            <img src="img/normal.png" alt="pain아이콘">                                                        
                                        </td>
                                        <td id="ROM-side-leg-right-back" class="data"><span>30.0˚</span></td>
                                        <td id="ROM-side-leg-right-back-pain" class="data">
                                            <img src="img/good.png" alt="pain아이콘">                                                        
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="balance">
                                <thead>
                                    <tr>
                                        <td colspan="6"><i class="fas fa-balance-scale-right"></i> 벨런스</td>
                                    </tr>
                                    <tr>
                                        <th colspan="3" style="width: 50%;color: #fff;" class="front">Right</th>
                                        <th colspan="3" style="width: 50%;color: #fff;" class="back">Left</th>
                                    </tr>
                                    <tr>
                                        <th>Good</th>
                                        <th>Normal</th>
                                        <th>Bad</th>
                                        <th>Bad</th>
                                        <th>Normal</th>
                                        <th>Good</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td class="data-chart bar1"><div></div></td>
                                        <td class="data-chart bar2"><div></div></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td class="data-chart bar3"><div></div></td>
                                        <td class="data-chart bar4"><div></div></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td class="data-chart bar5"><div></div></td>
                                        <td class="data-chart bar6"><div></div></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td class="data-chart bar7"><div></div></td>
                                        <td class="data-chart bar8"><div></div></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td class="data-chart bar9"><div></div></td>
                                        <td class="data-chart bar10"><div></div></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td class="data-chart bar11"><div></div></td>
                                        <td class="data-chart bar12"><div></div></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <section class="rom_change">
                        <h4><i class="fas fa-table"></i> 측정 결과표</h4>
                        <div class="option">
                            <button class="active">정면</button>
                            <button>측면</button>

                            <label for="before-date-rom">이전 측정일</label>
                            <select name="before-date-rom" id="before-date-rom" class="DB_DATE_OUTPUT" data-measurement_type="ROM_COMPARE_BEFORE">
                                <!-- js -->
                            </select>

                            <label for="after-date-rom">최근 측정일</label>
                            <select name="after-date-rom" id="after-date-rom" class="DB_DATE_OUTPUT" data-measurement_type="ROM_COMPARE_AFTER">
                                <!-- js -->
                            </select>
                        </div>
                        <div class="content">
                            <section class="right_table">
                                <table border="1">
                                    <thead>
                                        <tr>
                                            <th>Direction</th>
                                            <th>Part</th>
                                            <th class="ROM_BEFORE_DATE_LAST">2020-12-12</th>
                                            <th class="ROM_AFTER_DATE_NOW">2020-12-20</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th class="titleRowspan" rowspan="6">
                                                Right<br>
                                                (Flexion)
                                            </th>
                                            <th>목</th>
                                            <td id="ROM_RIGHT_BEFORE_NECK">20.0</td>
                                            <td id="ROM_RIGHT_AFTER_NECK">22.0 (▲2.0)</td>
                                        </tr>
                                        <tr>
                                            <th class="rom-shoulder-name-change">Shoulder</th>
                                            <td id="ROM_RIGHT_BEFORE_ShOULDER">20.0</td>
                                            <td id="ROM_RIGHT_AFTER_ShOULDER">20.0</td>
                                        </tr>
                                        <tr class="sideTR-ROM">
                                            <th>L - 어깨</th>
                                            <td id="ROM_RIGHT_BEFORE_ShOULDER2">20.0</td>
                                            <td id="ROM_RIGHT_AFTER_ShOULDER2">20.0</td>
                                        </tr>
                                        <tr>
                                            <th>허리</th>
                                            <td id="ROM_RIGHT_BEFORE_TRUNK">20.0</td>
                                            <td id="ROM_RIGHT_AFTER_TRUNK">20.0</td>
                                        </tr>
                                        <tr>
                                            <th class="rom-leg-name-change">Leg</th>
                                            <td id="ROM_RIGHT_BEFORE_LEG">20.0</td>
                                            <td id="ROM_RIGHT_AFTER_LEG">20.0</td>
                                        </tr>
                                        <tr class="sideTR-ROM">
                                            <th>L - 다리</th>
                                            <td id="ROM_RIGHT_BEFORE_LEG2">20.0</td>
                                            <td id="ROM_RIGHT_AFTER_LEG2">20.0</td>
                                        </tr>

                                    </tbody>
                                </table>
                            </section>
                            <section class="left_table">
                                <table border="1">
                                    <thead>
                                        <tr>
                                            <th>Direction</th>
                                            <th>Part</th>
                                            <th class="ROM_BEFORE_DATE_LAST">2020-12-12</th>
                                            <th class="ROM_AFTER_DATE_NOW">2020-12-20</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th class="titleRowspan" rowspan="6">
                                                Left<br>
                                                (Flexion)
                                            </th>
                                            <th>목</th>
                                            <td id="ROM_LEFT_BEFORE_NECK">20.0</td>
                                            <td id="ROM_LEFT_AFTER_NECK">22.0 (▲2.0)</td>
                                        </tr>
                                        <tr>
                                            <th class="rom-shoulder-name-change">어깨</th>
                                            <td id="ROM_LEFT_BEFORE_ShOULDER">20.0</td>
                                            <td id="ROM_LEFT_AFTER_ShOULDER">20.0</td>
                                        </tr>
                                        <tr class="sideTR-ROM">
                                            <th>L - 어깨</th>
                                            <td id="ROM_LEFT_BEFORE_ShOULDER2">20.0</td>
                                            <td id="ROM_LEFT_AFTER_ShOULDER2">20.0</td>
                                        </tr>
                                        <tr>
                                            <th>허리</th>
                                            <td id="ROM_LEFT_BEFORE_TRUNK">20.0</td>
                                            <td id="ROM_LEFT_AFTER_TRUNK">20.0</td>
                                        </tr>
                                        <tr>
                                            <th class="rom-leg-name-change">Leg</th>
                                            <td id="ROM_LEFT_BEFORE_LEG">20.0</td>
                                            <td id="ROM_LEFT_AFTER_LEG">20.0</td>
                                        </tr>
                                        <tr class="sideTR-ROM">
                                            <th>L - 다리</th>
                                            <td id="ROM_LEFT_BEFORE_LEG2">20.0</td>
                                            <td id="ROM_LEFT_AFTER_LEG2">20.0</td>
                                        </tr>

                                    </tbody>
                                </table>
                            </section>
                        </div>
                    </section>
                </div>
            </article>

            <article class="pop6">
                <h3 class="hid">ROM정보변화도</h3>
                <div class="btn">
                    <button data-date="all" class="active date">전체</button>
                    <button data-date="3m" class="date">3개월</button>
                    <button data-date="6m" class="date">6개월</button>
                    <button data-date="12m" class="date">12개월</button>
                </div>
                <div class="part_btn">
                    <select name="f_b-choice2" id="f_b-choice2">
                        <option value="front">정면</option>
                        <option value="side">측면</option>
                    </select>
                    <button data-what="Neck" class="active position">목</button>
                    <button data-what="Shoulder" class="position">어깨</button>
                    <button data-what="Pelvis" class="position">허리</button>
                    <button data-what="Leg" class="position">다리</button>
                </div>

                <!-- 차트 -->
                <canvas id="myChart3" width="900" height="650"></canvas>
                    
            </article>

            <!-- 임시 -->
            <article class="pop7">
                <h3 class="hid">대사질환정보자세히</h3>
            </article>
            <article class="pop8">
                <h3 class="hid">대사질환정보변화도</h3>
            </article>
        </div>
        <button type="button" class="Close_pop">Close</button>
    </div>

    <!-- 비밀번호 변경 -->
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
            <button>변경하기</button>
            <button type="button" onclick="$('form#pw_change_frm').fadeOut(200)">닫 기</button>
        </p>
    </form>


    <!-- 뒷배경 -->
    <div class="gray_div"></div>
    
    <!-- 이용권 설정 모달 -->
    <div id="itemListSetPopup" hidden>
        <h2>
            <span></span>
            <button class="closePopup">
                <div><span></span><span></span></div>
            </button>
        </h2>
        <form action="#" method="post" class="dateTimeSetFrm" name="dateTimeSetFrm" id="dateTimeSetFrm">
            <div class="_pop dateTimeSet_POP">
                <div class="left">
                    <h3>현재</h3>
                    <div class="info beforeInfo">
                        <h4>기간</h4>
                        <span class="date">2020-11-01<span>~</span>2020-12-01</span>
                        <br>
                        <h4>전체횟수</h4>
                        <span class="allCount">20회</span>
                        <br>
                        <h4>잔여횟수</h4>
                        <span class="count">19회</span>
                    </div>
                </div>
                <i class="fas fa-arrow-right" style="margin: 0 24px;"></i>
                <div class="right">
                    <h3>변경후</h3>
                    <div class="info afterInfo">
                        <div>
                            <h4>기간</h4>
                            <input type="date" name="itemAfterDate1" id="itemAfterDate1">
                            ~
                            <input type="date" name="itemAfterDate2" id="itemAfterDate2">
                            <p>
                                <input type="checkbox" name="useDateChk" id="useDateChk" checked>
                                <label for="useDateChk">이용일수 유지</label>
                            </p>
                            <br>

                            <h4>전체횟수</h4>
                            <input type="text" name="itemAfterAllCount" id="itemAfterAllCount"> 회

                            <br>

                            <h4>잔여횟수</h4>
                            <input type="text" name="itemAfterCount" id="itemAfterCount"> 회
                        </div>
                    </div>
                </div>
            </div>
            <div class="bottom">
                <button type="button" id="modifyVoucherDateCountBtn">수정</button>
                <button type="button" class="closePop">닫기</button>
            </div>
        </form>
        <form action="#" method="post" class="useListSetFrm" name="useListSetFrm" id="useListSetFrm">
            <div class="con">
                <table border="1">
                    <thead>
                        <th>No</th>
                        <th>사용일시</th>
                        <th>상태</th>
                        <th>담당강사</th>
                    </thead>
                    <tbody>
                        <tr><td>01</td><td>2020-12-01 15:31:25</td><td>예약</td><td>전상욱</td></tr>
                        <tr><td>02</td><td>2020-12-01 15:31:25</td><td>예약</td><td>전상욱</td></tr>
                        <tr><td>03</td><td>2020-12-01 15:31:25</td><td>예약</td><td>전상욱</td></tr>
                        <tr><td>04</td><td>2020-12-01 15:31:25</td><td>예약</td><td>전상욱</td></tr>
                        <tr><td>05</td><td>2020-12-01 15:31:25</td><td>예약</td><td>전상욱</td></tr>
                        <tr><td>06</td><td>2020-12-01 15:31:25</td><td>예약</td><td>전상욱</td></tr>
                        <tr><td>07</td><td>2020-12-01 15:31:25</td><td>예약</td><td>전상욱</td></tr>
                        <tr><td>08</td><td>2020-12-01 15:31:25</td><td>예약</td><td>전상욱</td></tr>
                        <tr><td>09</td><td>2020-12-01 15:31:25</td><td>예약</td><td>전상욱</td></tr>
                        <tr><td>10</td><td>2020-12-01 15:31:25</td><td>예약</td><td>전상욱</td></tr>
                        <tr><td>11</td><td>2020-12-01 15:31:25</td><td>예약</td><td>전상욱</td></tr>
                        <tr><td>12</td><td>2020-12-01 15:31:25</td><td>예약</td><td>전상욱</td></tr>
                        <tr><td>13</td><td>2020-12-01 15:31:25</td><td>예약</td><td>전상욱</td></tr>
                        <tr><td>14</td><td>2020-12-01 15:31:25</td><td>예약</td><td>전상욱</td></tr>
                        <tr><td>15</td><td>2020-12-01 15:31:25</td><td>예약</td><td>전상욱</td></tr>
                        <tr><td>16</td><td>2020-12-01 15:31:25</td><td>예약</td><td>전상욱</td></tr>
                        <tr><td>17</td><td>2020-12-01 15:31:25</td><td>예약</td><td>전상욱</td></tr>
                        <tr><td>18</td><td>2020-12-01 15:31:25</td><td>예약</td><td>전상욱</td></tr>
                        <tr><td>19</td><td>2020-12-01 15:31:25</td><td>예약</td><td>전상욱</td></tr>
                        <tr><td>20</td><td>2020-12-01 15:31:25</td><td>예약</td><td>전상욱</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="bottom">
                <button type="button" class="closePop">닫기</button>
            </div>
        </form>
        <form action="#" method="post" class="changeTeacherSetFrm" name="changeTeacherSetFrm" id="changeTeacherSetFrm">
            <div class="con">
                <p>
                    <label for="beforeTrainer">현재 강사</label>
                    <input type="text" name="beforeTrainer" id="beforeTrainer" readonly>
                </p>
                <p>
                    <label for="afterTrainer">변경 강사</label>
                    <select name="afterTrainer" id="afterTrainer">
                    </select>
                </p>
                
            </div>
            <div class="bottom">
                <button type="button" id="trainerChangeBtn">변경</button>
                <button type="button" class="closePop">닫기</button>
            </div>
        </form>
        <form action="#" method="post" class="for_ticketingSetFrm" name="for_ticketingSetFrm" id="for_ticketingSetFrm">
            <div class="con">
                <p>
                    <label>이용권 기간</label>
                    <span>2020-11-01 ~ 2020-12-01</span>
                    <span class="ticketingCountText" data-count="28" data-ticketingcount="1" style="margin-left: 20px;">
                        <!-- js -->
                    </span>
                </p>
                <p>
                    <label for="for_itemDate1">일괄예약 기간</label>
                    <input type="date" name="for_itemDate1" id="for_itemDate1">
                    <span>~</span>
                    <input type="date" name="for_itemDate2" id="for_itemDate2">
                    <label for="for_itemDate2" style="display: none;">일괄예약 기간</label>
                </p>
                <p>
                    <label for="ticketingCount">예약진행 횟수</label>
                    <select name="ticketingCount" id="ticketingCount">
                        <!-- js -->
                    </select><span>회</span>
                </p>
                <p class="day">
                    <label>일괄예약 요일</label>
                    <span>
                        <input type="checkbox" name="for_ticketingDay1" id="for_ticketingDay1" hidden>
                        <label for="for_ticketingDay1">월</label>
                        <input type="checkbox" name="for_ticketingDay2" id="for_ticketingDay2" hidden>
                        <label for="for_ticketingDay2">화</label>
                        <input type="checkbox" name="for_ticketingDay3" id="for_ticketingDay3" hidden>
                        <label for="for_ticketingDay3">수</label>
                        <input type="checkbox" name="for_ticketingDay4" id="for_ticketingDay4" hidden>
                        <label for="for_ticketingDay4">목</label>
                        <input type="checkbox" name="for_ticketingDay5" id="for_ticketingDay5" hidden>
                        <label for="for_ticketingDay5">금</label>
                        <input type="checkbox" name="for_ticketingDay6" id="for_ticketingDay6" hidden>
                        <label for="for_ticketingDay6">토</label>
                        <input type="checkbox" name="for_ticketingDay7" id="for_ticketingDay7" hidden>
                        <label for="for_ticketingDay7">일</label>
                    </span>
                </p>
                <p>
                    <label for="classStartTime1">수업 시작시간</label>
                    <select name="classStartTime1" id="classStartTime1" style="width: 50px;">
                        <!-- js -->
                    </select>
                    <span>:</span>
                    <label for="classStartTime2" style="display: none;">수업 시작시간</label>
                    <select name="classStartTime2" id="classStartTime2" style="width: 50px;">
                        <!-- js -->
                    </select>

                </p>
            </div>
            <div class="bottom">
                <button>수업 검색</button>
                <button type="button" class="closePop">닫기</button>
            </div>
        </form>

        <form action="#" method="post" class="stopTicketSetFrm" name="stopTicketSetFrm" id="stopTicketSetFrm">
            <div class="con">
                <h3>선택 이용권</h3>
                <div class="voucherInfo">
                    <p class="stopType">개인레슨 이용권</p>
                    <p class="stopName">P.T. 20회 이용권</p>
                    <p class="stopDate">2021-05-01 ~ 2021-05-31 / 담당강사 : 전상욱</p>
                    <p class="stopUse">이용일수 0/31일 · 이용횟수 0/20회 · 예약횟수 0회</p>
                </div>

                <div class="stopOption">
                    <h3>정지 옵션</h3>
                    <div>
                        <span>이용권 정지 시작 날짜 </span>
                        <input type="date" name="whereStopDate1" id="whereStopDate1">
                        <br>
                        <span>이용권 정지 종료 날짜 </span>
                        <input type="date" name="whereStopDate2" id="whereStopDate2">
                    </div>
                </div>

                <div class="stopResult">
                    <h3>결과</h3>
                    <div>
                        <p>
                            <span class="title">이용권 정지 시작 날짜</span>
                            <span class="start">2021-05-04</span>
                        </p>
                        <p>
                            <span class="title">이용권 정지 종료 날짜</span>
                            <span class="end">2021-06-03</span>
                        </p>
                        <p>
                            <span class="title">이용권 정지 일수</span>
                            <span class="range">30일</span>
                        </p>
                        <p>
                            <span class="title">이용권 재개 날짜</span>
                            <span class="reStart">2021-06-04</span>
                        </p>
                    </div>
                </div>
                
            </div>
            <div class="bottom">
                <button type="button" id="ticketStopBtn">정지</button>
                <button type="button" class="closePop">닫기</button>
            </div>
        </form>

        <form action="#" method="post" class="startTicketSetFrm" name="startTicketSetFrm" id="startTicketSetFrm">
            <div class="con">
            <h3>선택 이용권</h3>
                <div class="voucherInfo">
                    <p class="startType">개인레슨 이용권</p>
                    <p class="startName">P.T. 20회 이용권</p>
                    <p class="startDate">2021-05-01 ~ 2021-05-31 / 담당강사 : 전상욱</p>
                    <p class="startUse">이용일수 0/31일 · 이용횟수 0/20회 · 예약횟수 0회</p>
                </div>

                <div class="startResult">
                    <h3>정지 상태</h3>
                    <div>
                        <p>
                            <span class="title">이용권 정지 시작 날짜</span>
                            <span class="start">2021-05-04</span>
                        </p>
                        <p>
                            <span class="title">이용권 정지 종료 날짜</span>
                            <span class="end">2021-06-03</span>
                        </p>
                        <p>
                            <span class="title">이용권 정지 일수</span>
                            <span class="range">30일</span>
                        </p>
                        <!-- <p>
                            <span class="title">이용권 재개 날짜</span>
                            <span class="reStart">2021-06-04</span>
                        </p> -->
                    </div>
                </div>
            </div>
            <div class="bottom">
                <button type="button" id="ticketStartBtn">재개</button>
                <button type="button" class="closePop">닫기</button>
            </div>
        </form>
    </div>


    <!-- 문제근육 상세보기 모달 -->
    <div id="errorMuscleView_pop">
        <h2>문제근육자세히</h2>

        <section class="front">
            <h3>정면 결과</h3>
            <div class="pic">
                <img src="img/NOT_MEASUREMENT_IMAGE.png" alt="정면체형이미지" title="문제근육정면체형이미지">
            </div>
            <div class="table">
                <table border="1">
                    <thead>
                        <tr>
                            <th>부 위</th>
                            <th colspan="4">상 태</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>전 체</th>
                            <td colspan="3">이상 없음</td>
                            <td><span class="rect" id="front-errorMuscle_dot_none"></span></td>
                        </tr>
                        <tr>
                            <th>목</th>
                            <td class="cursor front_Neck" data-svg="Neck_right_blue Neck_right_red">R - 목 불균형</td>
                            <td><span class="rect front" id="front-errorMuscle_dot_R_Neck"></span></td>
                            <td class="cursor front_Neck" data-svg="Neck_left_blue Neck_left_red">L - 목 불균형</td>
                            <td><span class="rect front" id="front-errorMuscle_dot_L_Neck"></span></td>
                        </tr>
                        <tr>
                            <th>어깨</th>
                            <td class="cursor front_RShoulder" data-svg="Shoulder_right_up_blue Shoulder_right_up_red Shoulder_right_down_blue Shoulder_right_down_red">R - 어깨 불균형</td>
                            <td><span class="rect front" id="front-errorMuscle_dot_R_Shoulder"></span></td>
                            <td class="cursor front_LShoulder" data-svg="Shoulder_left_up_blue Shoulder_left_up_red Shoulder_left_down_blue Shoulder_left_down_red">L - 어깨 불균형</td>
                            <td><span class="rect front" id="front-errorMuscle_dot_L_Shoulder"></span></td>
                        </tr>
                        <tr>
                            <th>골반</th>
                            <td class="cursor front_RPelvis" data-svg="Pelvis_right_up_blue Pelvis_right_up_red Pelvis_right_down_blue Pelvis_right_down_red">R - 골반 불균형</td>
                            <td><span class="rect front" id="front-errorMuscle_dot_R_Pelvis"></span></td>
                            <td class="cursor front_LPelvis" data-svg="Pelvis_left_up_blue Pelvis_left_up_red Pelvis_left_down_blue Pelvis_left_down_red">L - 골반 불균형</td>
                            <td><span class="rect front" id="front-errorMuscle_dot_L_Pelvis"></span></td>
                        </tr>
                        <tr>
                            <th>다리</th>
                            <td class="cursor front_Leg" data-svg="Leg_right_o_blue Leg_right_o_red Leg_left_o_blue Leg_left_o_red">O 다리</td>
                            <td><span class="rect front" id="front-errorMuscle_dot_O_Leg"></span></td>
                            <td class="cursor front_Leg" data-svg="Leg_right_x_blue Leg_right_x_red Leg_left_x_blue Leg_left_x_red">X 다리</td>
                            <td><span class="rect front" id="front-errorMuscle_dot_X_Leg"></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="muscle">
            <div class="R_L_name">
                <div>
                    <span>R</span>
                    <span>L</span>
                </div>
                <div>
                    <span>L</span>
                    <span>R</span>
                </div>
            </div>
            <svg version="1.1" id="body_SVG_front" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px"
                    y="0px" viewBox="0 0 3000 3000" style="enable-background:new 0 0 3000 3000;" xml:space="preserve">
                    <style type="text/css">
                        .st0{fill:#FFFFFF;}
                    </style>
                    <path d="M1967.8,970.6c-2.8,4.6-4.6,7.4-6.2,10.2c-9.4,16.9-18.7,33.8-28.3,50.6c-3.3,5.7-7.2,11.2-11.1,16.5
                        c-4.3,5.8-7.3,12.2-6.5,19.4c2.3,21.5-4.9,40.5-14.2,59.3c-9,18.2-17.2,36.8-24.5,55.7c-20.3,52.6-44.2,103.5-70.4,153.3
                        c-14.3,27.1-28.3,54.3-42.9,81.3c-3.7,6.9-8.7,13.4-13.7,19.5c-3,3.6-4.4,7.2-4.5,11.7c-0.3,10-1.2,20-0.9,30
                        c0.7,22.9-4.7,44.1-16,64c-16.2,28.4-31.9,57-47.5,85.6c-6.6,12.1-15.7,20.3-29.8,22.6c-3.8,0.6-7.9,3.7-10.5,6.8
                        c-11.4,13.5-25.2,18.7-42.7,15.2c-5.4-1.1-11.3-0.5-16.9-0.2c-21.5,1-31.8-6.4-36.3-27.2c-1.9-8.9-4.7-17.2-9.3-25.1
                        c-5.7-9.7-5.1-19.9-0.7-29.8c5.6-12.8,11.8-25.3,17.7-38c1.7-3.6,3.2-7.2,5.4-12.1c-5.2,0.6-9.1,1.2-13,1.6
                        c-20.6,2.4-37.3-5.9-47-23.3c-8.3-14.8-4.3-30.1,11-37c26-11.7,46.4-29.4,63.3-52c11.6-15.5,26.7-27,44.5-34.7
                        c7.3-3.2,11.7-8,14.3-15.5c16.9-48,36.1-95.2,49.7-144.4c3.3-12,9.3-22.7,16.6-32.8c3.4-4.6,5.8-10.6,6.6-16.2
                        c2.4-17.5,3.8-35.1,5.6-52.6c2.4-24.2,9.7-47,21.3-68.2c10.3-19,21.7-37.5,32.6-56.2c0.7-1.1,1.2-2.5,2.2-3.3
                        c10.1-7.7,11.7-18.7,12.4-30.3c1-16.1,5.6-31.3,11.3-46.4c1.9-5.1,3.4-11.3,2.4-16.4c-3.5-16.9-0.6-33.2,3-49.5
                        c4.1-18.6,10.2-36.6,19.4-53.3c9.9-17.8,14.7-36.9,17.6-56.9c1.8-12.2,4.1-24.3,7-36.2c8.5-34.8,28.7-61.8,56.5-83.7
                        c32.5-25.7,69.9-41,109.9-50.3c6.5-1.5,12.9-3.3,19.4-4.6c13.5-2.8,25.9-8.1,37.7-15.2c14.8-9,29.9-17.5,45.1-25.7
                        c9.1-4.9,13.7-12.2,14.5-22.3c0.2-3,0.8-5.9,1.3-8.9c1.6-8.9-0.5-16.7-7.5-22.5c-6.3-5.2-9.6-11.7-10.4-19.7
                        c-1-9.6-2.6-19.1-3.3-28.8c-0.5-7.9-3.2-13.6-9.7-18.6c-5.1-3.9-9.6-9.3-12.6-15c-7-13.2-13.4-26.8-19.2-40.6
                        c-8.5-20.1-4.4-35.4,13.1-48.3c8.8-6.5,11.5-15.2,11.7-25.2c0.3-15.1,2.2-29.9,7-44.3c17.3-52.1,51.4-86.5,106.2-97.6
                        c46.9-9.4,111.3,7.8,142.7,64.1c12.4,22.1,20.2,45.6,21.1,71.1c0.1,2.7,0.5,5.3,0.4,8c-0.4,10.7,3.2,18.3,12.9,24.8
                        c16.4,11,20.3,30.8,12,48.8c-6,13-12.3,25.8-18.3,38.9c-3,6.6-7.1,12-13.5,15.5c-6.5,3.6-9.1,9.1-9.7,16.3
                        c-0.9,10.6-2.5,21.2-3.6,31.7c-0.8,7.3-3.9,13.5-9.4,18.2c-7.9,6.8-8.9,14.9-8.3,25.1c1,16.8,9.1,26.8,23.6,33.7
                        c9.6,4.6,18.5,10.6,27.3,16.5c14,9.4,29.3,15.7,45.4,20.1c16.7,4.6,33.4,9.1,49.9,14.4c36.4,11.8,68.8,30.6,95.4,58.3
                        c25.6,26.7,38.6,59.4,43.4,95.7c1,7.3,1.6,14.6,2.4,21.9c1.1,9.7,3.8,18.7,8.6,27.4c15.7,28.2,26.6,58.1,30.1,90.3
                        c0.7,6.9,0.5,14-0.4,20.8c-1,8.1-0.9,15.7,2.4,23.4c6.4,15.1,10,31,11.2,47.4c0.7,10.2,4.3,18.4,10.7,27
                        c29.3,39.8,53,82.4,58.5,132.8c1.8,16.5,2.9,33.2,4.6,49.7c0.4,3.6,1.1,7.5,2.9,10.5c17.2,28.8,27.2,60.6,38.2,92.1
                        c10.6,30.5,21.9,60.7,32.8,91.1c2,5.6,4.9,9.9,10.7,12.4c22.8,9.5,40,25.9,55.2,44.9c6.2,7.8,12.6,15.7,20.1,22.1
                        c6.7,5.8,15,9.8,22.7,14.3c3.7,2.2,7.9,3.5,11.7,5.7c17,9.6,20.7,25.1,10.1,41.3c-10.9,16.6-26.8,21.9-45.8,20.1
                        c-3.5-0.3-7.1-0.9-12-1.6c2.3,5.3,3.8,9.3,5.6,13.2c5,10.5,10.3,20.9,15.3,31.4c6,12.8,6.4,25.4-0.4,38.3
                        c-3.5,6.6-5.5,14.2-7.2,21.6c-5.3,23.1-22.4,31.3-39.2,28c-5.4-1.1-11.3-0.2-16.8,0.7c-14.3,2.3-26.6-1.2-36.2-12
                        c-6-6.8-12.7-11.5-21.8-12.8c-7.8-1.1-13.2-6.2-17.2-12.6c-8.1-12.6-16.4-25.1-23.8-38.1c-9.9-17.3-19.1-35.1-28.7-52.6
                        c-13.8-25.3-18.3-52.6-16.8-81.1c0.3-4.7,0.4-9.3,0.1-14c-0.4-6.5-2.4-12.2-8-16.5c-3-2.4-5.4-5.9-7.2-9.4
                        c-27.7-53.9-55.8-107.6-82.7-161.9c-11.5-23.2-19.9-47.9-30.2-71.7c-9.8-22.6-19.7-45.2-30.4-67.3c-8.9-18.4-14.1-37.2-12.8-57.8
                        c0.3-4.3-1-9.4-3.2-13.1c-13.4-22.6-27.2-45-40.9-67.4c-0.8-1.4-1.8-2.7-3.5-5.3c-1.2,3.1-2.2,4.9-2.6,6.9
                        c-3.9,19.6-6.9,39.4-11.8,58.7c-4.1,16.4-10.1,32.3-15.7,48.3c-1.9,5.5-2.6,9.5,2.2,14.3c3.6,3.5,6.2,8.8,7.5,13.7
                        c4.9,19.4,9.5,38.8,13.3,58.4c1.2,6.3,0.2,13.2-0.9,19.7c-1,5.4-1.3,10.3,0.8,15.6c11.3,28.7,18.1,58.6,22.1,89
                        c2,15,0.8,30.6-0.6,45.8c-0.9,10.1-0.4,18.3,8.4,24.7c6.9,5,9.2,12.9,10.4,20.9c8.1,54.1,12.3,108.6,11.6,163.3
                        c-0.7,58-4.1,115.9-12.8,173.3c-8.6,56.9-17.6,113.8-27.3,170.6c-4.1,24.3-3.5,48.4-2.1,72.7c1.5,25.3,2.8,50.6-1,75.8
                        c-0.5,3.6-0.8,7.5-2.3,10.7c-4.6,9.6-2.8,18.7,0.9,28c8,19.7,16.2,39.4,23.8,59.3c11.2,29.4,17.9,60,20.9,91.2
                        c1,10.2,0.4,20.7-0.4,30.9c-1.3,15.9-3.3,31.8-5.1,47.6c-1.8,16-6.6,31.2-11.5,46.4c-19.3,58.9-33.9,119-47.4,179.5
                        c-4,18-3.9,36.3-1.5,54.7c1.8,13.9,3.2,27.8,4.9,41.7c0.4,3.5,1.3,7,2,10.9c10.9,0,21.2-0.2,31.4,0.1c8,0.2,16,0.7,23.9,1.9
                        c30.6,4.3,58.5,31.9,62,62.4c3.1,26.8-8.6,47.1-30.4,62c-11.7,8-25.1,12.3-38.8,15.4c-16.8,3.8-30,12.1-39.2,26.9
                        c-6.4,10.3-16.1,16.8-27.4,20.7c-25,8.6-50.6,11.1-76.8,6.5c-16.3-2.9-27.1-11.8-32.2-27.7c-6.4-19.8-10.5-40.1-9.2-60.9
                        c1.6-25.4,0.3-50.6-3.7-75.7c-4-25.4-1.6-50.5,3.2-75.5c2.9-15,6.7-29.9,10.8-44.6c5.2-18.7,5.7-37.3,1.7-56.3
                        c-7.4-35.2-14.1-70.5-21.8-105.6c-2-9.3-5.4-18.6-9.7-27.1c-15.9-30.8-21.7-63.2-18.9-97.7c2.1-25.8,8.1-50.6,15.6-75.1
                        c3.5-11.4,6.9-22.9,10.4-34.4c2-6.5,1.5-12.3-3.5-17.6c-11.2-11.8-18.1-26.2-23.8-41.2c-14.8-39-24.8-79.4-33.8-120
                        c-6.2-27.9-13.3-55.7-18.1-83.9c-4-23.6-6.4-47.6-7.5-71.5c-2.3-48.6-3.4-97.2-4.9-145.9c0-1-0.1-2-1.3-3.1
                        c-0.7,6.2-1.7,12.4-1.9,18.6c-1.5,41.6-3.3,83.2-4,124.9c-0.7,41.2-5.9,81.6-16.2,121.5c-10.2,39.6-19.5,79.5-30.4,119
                        c-5.5,19.8-13.3,39-20.8,58.2c-2.2,5.7-6,11.3-10.3,15.7c-9.9,10.1-11,21-6.9,34c6.8,21.9,13.8,43.9,19,66.2
                        c7.3,31.1,8,62.7,1.1,94.1c-1.7,7.7-4.2,15.6-8,22.5c-11.8,20.9-18.1,43.5-22.5,66.9c-5.8,31.1-11.9,62.1-17.8,93.2
                        c-2.9,15.1-1.1,30,3.9,44.3c9.7,27.9,15.1,56.7,15.4,86.2c0.2,16.9-2.3,33.8-3.8,50.8c-1.6,18.6-3.7,37-1.6,55.8
                        c2.8,25.4-2.5,49.7-13,72.9c-3.5,7.8-9.1,12.8-17.1,15.7c-17.7,6.5-36,6.7-54.3,4.6c-10.8-1.3-21.5-4.2-32.1-7.3
                        c-12.9-3.8-23.2-11.5-30.5-22.9c-7.5-11.8-18.1-19.3-31.4-23.2c-7-2.1-14.1-3.8-21.2-5.9c-13.7-4-25.7-11-35.9-21
                        c-21.1-20.6-25.3-51.8-10-77c15.1-24.8,37.3-38.5,66.4-39.9c9.3-0.5,18.7,0.1,28,0.2c4.3,0,8.6,0,13.7,0c1.6-11.1,3.2-21.5,4.5-32
                        c1.7-13.2,3.8-26.4,4.7-39.7c1.1-16.8-2.3-33.2-6.1-49.4c-14.6-61.5-30-122.8-49.9-182.9c-3.3-10-4.8-20.7-6-31.3
                        c-2.3-20.2-4.4-40.4-5.3-60.6c-1.2-26.5,3.7-52.4,11.5-77.7c9.9-32.1,19.5-64.4,35-94.4c2.5-4.8,2.7-9.6,1.1-14.5
                        c-7.4-21.9-8.3-44.4-6.9-67.2c0.9-15,1.9-29.9,2.8-44.9c1.2-19.1-1.4-37.9-4.4-56.7c-8.3-52.9-16.8-105.9-25.4-158.8
                        c-12.5-77.3-14.3-155.2-13.2-233.2c0.5-36,4.1-71.8,9.7-107.4c1.9-12.1,5.3-23.4,16-31.1c4-2.9,4.7-7,4.3-11.5
                        c-0.9-9.6-1.6-19.3-2.8-28.8c-1.2-10.1-0.4-19.8,2-29.7c3.9-16.5,6.9-33.2,11.1-49.7c3-11.9,7-23.6,11-35.2
                        c1.9-5.5,2.6-10.5,0.7-16.4c-1.7-5.5-2.3-11.9-1.2-17.5c3.7-19.6,8.3-39.1,12.6-58.5c1.5-6.7,4.2-12.7,9.7-17
                        c3.6-2.9,4.6-6.2,2.6-10.5c-17-37.9-23-78.4-28.6-119C1969.5,975.3,1969,974.1,1967.8,970.6z M2472.6,958.7c0.7-0.2,1.4-0.4,2.1-0.6
                        c1.7,2.7,3.5,5.4,5.1,8.2c9.7,17.5,19,35.2,29.1,52.4c6.4,10.9,13.8,21.2,21.3,31.3c2.8,3.8,4.3,7.4,3.6,11.9
                        c-3.3,22.5,3.4,42.5,13.4,62.3c9.8,19.6,18.1,40,26.7,60.2c9.7,22.6,17.9,46,28.8,68c27.1,54.9,55.4,109.3,83.4,163.8
                        c2.8,5.5,7.7,9.8,11.3,15c2.4,3.4,5.6,7,6.1,10.8c1.1,10.2,1.6,20.7,1,30.9c-1,19,2.3,37.2,9.6,54.6c15.1,35.7,35.5,68.6,55.7,101.5
                        c4.1,6.7,10.1,11.9,18.2,13c9,1.1,15.5,5.3,20.7,12.4c7.8,10.5,18.1,13.5,30.9,11.5c7.4-1.1,15.2-1,22.6-0.1
                        c12,1.4,21.2-3,25.5-14.3c2-5.2,2.5-11,3.9-16.5c0.9-3.5,1.4-7.6,3.6-10.2c9.8-11.4,9.8-23.7,3.9-36.4
                        c-7.1-15.4-14.9-30.5-22.3-45.8c-1.8-3.7-3.4-7.5-5.7-12.6c4.1,0.5,6.4,0.7,8.6,1c7.6,1.2,15.1,3.1,22.7,3.5
                        c16.2,1,28.3-6.2,36-20.5c5.4-10,3.4-18.9-5.9-25.4c-4.9-3.4-10.4-5.9-15.9-8.3c-15.6-6.9-28.3-17.2-38.7-30.7
                        c-6.3-8.2-13-16-20-23.6c-9.8-10.6-21-19.6-34.7-24.8c-9.5-3.6-15.2-10.3-18.6-19.6c-11.5-32.3-23.3-64.5-34.5-96.9
                        c-9.8-28.4-19.6-56.6-34.9-82.7c-1.8-3.1-2.9-6.8-3.3-10.3c-1.5-11.9-2.9-23.8-3.9-35.7c-1.6-21.3-4.4-42.4-11.7-62.6
                        c-11-30.3-27.8-57.5-45.5-84.1c-1.1-1.6-2.6-3.1-4.1-4.3c-6.1-4.6-8.4-10.8-8.7-18.2c-0.7-20.9-5.1-40.9-13.9-59.9
                        c-2.5-5.4-2.3-10.6-1-16.5c1.5-7,2.3-14.5,1.6-21.7c-2.2-22.7-9.1-44.3-18.1-65.2c-4.1-9.5-8.1-19.1-13.6-27.7
                        c-3.8-5.9-5.9-11.8-6.7-18.4c-1.5-11.9-2.6-23.8-4.3-35.7c-5.6-37.9-21.4-70.6-50.5-96.1c-23.3-20.4-49.4-36.2-79.1-45.5
                        c-20.9-6.6-42.2-12.3-63.2-18.6c-5.7-1.7-11.7-3.3-16.8-6.2c-19.7-11.2-39.2-22.8-58.6-34.4c-7.5-4.5-12.3-11.3-13.7-19.9
                        c-1.4-8.9-2.2-17.8-3.1-26.8c-0.5-5.1,2-8.9,6-11.9c8.5-6.4,12.7-15.2,13.8-25.6c1.1-10.6,2.4-21.2,3.5-31.8
                        c0.5-4.8,1.4-9.6,6.6-11.4c9.1-3.1,13.5-10.4,17.4-18.3c6.3-12.9,12.2-25.9,18.5-38.8c4-8.1,3.6-15.9-0.2-24
                        c-3.7-7.8-8.6-14.3-16.6-17.9c-7.4-3.3-9.7-8.7-10-16.6c-0.6-14.3-1.3-28.7-4-42.7c-11.2-57.5-57.2-99.3-114.7-105.1
                        c-57.4-5.8-109.9,25.6-132.7,79.6c-8.9,21.1-13.1,43.3-12.7,66.2c0.1,8.9-3.3,14.9-11.1,19.6c-16.2,9.7-21.2,24.7-13.8,42
                        c6.1,14.4,13.1,28.4,19.8,42.5c2.6,5.5,6.4,10.1,12.6,12.2c6.3,2.1,8.3,6.9,8.8,13.1c0.8,10,2.2,19.9,3.2,29.8
                        c1.1,11.1,5.1,20.6,14.4,27.4c4.1,3,6.1,7,5.6,12.1c-0.9,8.3-1.7,16.6-2.8,24.8c-1.4,9.9-6.7,17.2-15.3,22.2
                        c-8.9,5.3-17.6,10.7-26.5,16c-26,15.5-53.6,26.3-83.4,32.3c-33,6.7-63.4,19.8-91.3,38.8c-42.5,28.9-67.5,68.1-72,119.8
                        c-2.1,24.1-7.8,46.9-20.6,68.1c-12,19.8-17.5,42.4-20.9,65.3c-1.4,9.5-2.9,19.3,0.6,28.7c2.2,5.8,1.5,10.9-1.1,16.3
                        c-8.4,18-12.5,37.2-13.7,57c-0.6,9-2.7,17-10.5,22.7c-1.8,1.3-3.1,3.5-4.3,5.5c-11,18.7-22.2,37.2-32.7,56.1
                        c-9.9,17.9-16.7,37-19.1,57.5c-2.2,18.2-4,36.4-5.6,54.7c-0.9,10.2-3.2,19.5-10,27.6c-7.2,8.5-10.9,18.8-14.5,29.3
                        c-17.1,50.1-34.6,100-52,150c-1.8,5.3-5.1,9.2-10.6,11.2c-21.7,8-38.2,22.4-51.9,40.7c-15.5,20.6-34.8,36.7-58.7,46.9
                        c-1.2,0.5-2.4,1.1-3.6,1.8c-9.1,5.2-12.5,13.4-8.5,22.8c5.6,13.1,15.1,22.1,29.8,23.4c6.2,0.6,12.6-0.4,18.8-1.1
                        c6.2-0.7,12.3-1.8,20.3-3.1c-2.5,5.5-4.2,9.4-6.1,13.2c-4.7,9.2-10.1,18.1-14.3,27.5c-4.4,9.7-8,19.7-11.4,29.7
                        c-2,5.9-1.1,12.2,2.7,17.2c6.1,8,8.5,17.1,9.7,26.8c1.7,13.8,10.6,21.3,24.3,21.4c8.7,0.1,17.4-0.4,26,0.5
                        c15.2,1.6,26.5-3.6,35-16.3c2-3,6.2-6.3,9.4-6.3c14.4-0.2,22.4-8.5,28.8-19.9c10.6-18.5,21.2-37,32.2-55.2
                        c11.1-18.3,20.1-37.6,26.3-58.1c2.7-8.8,3.4-18.3,3.9-27.5c0.7-13.3,0.4-26.7,0.3-40c0-4.3,0.8-8,4.7-10.3
                        c6.7-3.9,10.4-10.1,13.8-16.7c10.8-20.6,21.8-41.2,32.6-61.9c24.8-47.5,50.2-94.7,69.6-144.9c13.1-33.8,25.6-67.9,42.9-99.9
                        c7.3-13.5,9.3-28.5,7-43.7c-1.1-7.2,0.9-12.8,5.1-18.5c6.9-9.4,13.9-18.8,19.4-29c12-21.9,23.2-44.2,34.7-66.4
                        c1.4-2.7,1.9-6.3,6.7-7.2c2.6,55.7,12.4,109.4,35.2,160.6c-9,5.1-15.4,11.5-17.6,21.3c-4,18.2-8.2,36.4-12,54.6
                        c-1.3,6.2-1.7,12.6,2,18.4c2.7,4.3,2.3,8.9,0.4,13.4c-9.4,21.9-14.3,45-19.5,68c-4.7,20.8-9.5,41.5-5.2,63.1
                        c1.3,6.5,1.3,13.2,2,19.9c0.4,4.3-0.4,7.9-4.4,10.3c-10,6-14.5,15.3-15.7,26.5c-3.3,30.1-7.8,60.2-9.3,90.4
                        c-3.9,79.4,0.1,158.5,10,237.4c7,55.2,16.1,110,26.5,164.7c5.6,29.3,7.3,58.8,3.3,88.5c-3.6,27.4-3.4,54.4,4.8,81.2
                        c3.2,10.5,1.8,21.1-3.2,31.1c-18.3,36.2-28.6,75.2-38.8,114.2c-1.7,6.4-2.5,13.1-3.1,19.7c-2.4,25.7,0,51.3,3.2,76.7
                        c1.9,14.8,3.3,30,7.9,44.1c18.9,58,33.9,117,48.1,176.2c6.3,26.4,9,53.1,5,80.3c-2.1,14.5-2.9,29.1-4.4,43.7c-0.4,3.5-1.2,7-2,11.1
                        c-4.4,0-8,0.1-11.7,0c-11.3-0.3-22.7-1.1-34-0.9c-32.6,0.6-58.2,18.3-68.9,47c-5.5,14.9-4.7,29.5,3.1,43.4
                        c7.7,13.9,18.7,24.2,33.8,29.7c8.4,3.1,17,6.1,25.6,8.3c17.9,4.6,32.3,13.8,42.1,29.7c4.5,7.3,11,12.6,19,15.7
                        c26.4,10.2,53.4,13.8,81.3,7.4c11-2.5,18.2-8.7,22.3-19.3c7.9-20.2,11.9-40.9,10.1-62.5c-1.8-22-0.6-43.9,2.7-65.7
                        c4.2-27.8,4.7-55.8-1.6-83.5c-3.7-16.6-8.1-33-12.2-49.4c-4.1-16.7-4.8-33.5-1.3-50.5c7.9-37.8,15.6-75.6,23.1-113.5
                        c1.8-9.3,5-17.8,9.7-26c9.5-16.7,14.9-34.7,17.3-53.8c3.7-28.9,0.1-57.2-7.2-85.1c-5.4-20.6-11.8-40.9-18-61.3
                        c-3-9.8-1.2-17.7,5.6-25.8c6.9-8.3,13.2-17.7,17.4-27.7c7-16.5,12.9-33.6,17.8-50.8c8.5-30.1,15.8-60.5,23.6-90.8
                        c12.5-48.5,20.9-97.7,22.5-147.8c1.6-49.3,3.6-98.6,4.5-147.9c0.5-27.3-0.4-54.6-1.5-81.9c-1.1-28-3.1-55.9-5-83.8
                        c-0.4-5.6-2-11.1-2.6-16.6c-0.4-3.1-1-7.1,0.5-9.3c4.8-7,10.5-13.5,16.5-20.8c5.8,6.9,10.6,12.8,15.7,18.5c3.3,3.7,3.2,7.2,2.2,11.9
                        c-1.9,9.1-3,18.3-3.9,27.6c-0.9,9.6-1.2,19.3-1.6,28.9c-1.6,34.3-4.6,68.6-4.5,102.8c0.2,51,2.7,101.9,4.3,152.9
                        c1,32,2.4,63.9,8,95.5c10.9,61.4,26.6,121.6,44.4,181.2c6.7,22.6,15.2,44,31.6,61.6c5.1,5.4,6.3,12.1,4,19.6
                        c-3.5,11.1-6.4,22.4-9.9,33.5c-9.1,29-16.8,58.1-17.3,88.9c-0.4,29.3,5,56.8,19.4,82.6c4,7.2,7,15.2,8.8,23.2
                        c7.7,35.8,14.7,71.7,22.2,107.6c3.3,15.8,5.5,31.6,2.1,47.6c-2.3,10.7-5.3,21.3-8.3,31.9c-10.8,39.4-13.3,79.1-6.4,119.7
                        c2.7,15.6,3.2,32,1.8,47.7c-2.1,23.1,0.9,45.1,7.7,66.9c4.9,15.5,15.6,23.3,31.2,25.5c20.4,2.8,40.6,1.5,60.4-4
                        c16.3-4.5,29.7-13.3,38.7-28c5.7-9.3,13.7-15.2,24-18.3c8.3-2.5,16.6-4.8,24.8-7.6c12.1-4,23.7-8.9,33.4-17.5
                        c22.1-19.6,26.4-49.7,9.8-74c-14.6-21.4-34.9-33.5-61.4-33.5c-12.7,0-25.3,0.7-38,0.9c-3.2,0.1-6.4-0.2-8.3-0.3
                        c-2.1-19-3.6-36.5-6.2-53.9c-3.2-21.7-4-43.3,1.3-64.5c11.9-48.1,23.3-96.4,37-144.1c11.1-38.8,23.5-77,25.9-117.7
                        c1-17.3,2.2-34.6-0.3-51.8c-5.8-40.1-17.8-78.5-34-115.5c-3.9-8.8-7.8-17.7-11.6-26.5c-2.8-6.4-3-12.8-0.4-19.4
                        c5.2-13.2,7-27.1,6.6-41.2c-0.6-20.6-1.9-41.3-2.5-61.9c-0.4-12.6-1.8-25.5,0.1-37.9c7.6-50,16-99.9,24.5-149.8
                        c8.6-50.3,14.1-101,16.4-151.9c2.3-48.6,3.7-97.3,0-145.9c-2-25.5-5.2-51-8.4-76.4c-1.4-11.2-5.8-21.1-16.3-27.3
                        c-2.1-1.2-3.9-4.9-3.8-7.5c0.1-10,1.1-19.9,1.7-29.9c0.4-7,2.1-14.1,1.1-20.8c-4.9-34.3-11.3-68.3-24.7-100.5
                        c-2.5-5.9-3.1-10.8-0.1-17c2.1-4.4,2.5-10.5,1.5-15.4c-3.7-18.6-8.4-37-12.3-55.6c-1.6-7.6-4.4-13.9-11.2-18.1
                        c-3.8-2.4-4.5-5.5-3-9.4c1-2.5,2-4.9,3.1-7.4c10.7-25.6,18.2-52.1,22.7-79.5C2467.2,994.7,2469.8,976.7,2472.6,958.7z
                        M2222.2,1628.1c0.4-0.1,0.8-0.1,1.2-0.2c1.5-27.5,3-55.1,4.7-82.6c0.9-14.3,0.8-28.8,3.7-42.6c2.4-11.3-0.1-18.6-9.5-24.9
                        c-5.9,4.8-12.8,9.7-10.2,18.6c3.6,12.1,3,24.3,3.7,36.6c1.5,28.2,3.1,56.5,4.7,84.7C2220.7,1621.2,2221.6,1624.6,2222.2,1628.1z"/>
                    <path d="M761.7,1476.4c-5.6,0-10.1,0-15.6,0c-0.2,3.7-0.6,6.9-0.5,10.1c1.5,38.1-2.6,75.8-7.1,113.6c-7,59-18.5,117-32.6,174.6
                        c-1.7,7.1-5.1,13.7-7,20.7c-1.4,5.3-3.2,11.2-2.4,16.4c5.5,37.1,6.2,74.4,4.8,111.7c-1.8,48.1-9,95.5-23.6,141.5
                        c-4.9,15.5-10.4,30.9-16.6,46c-6.2,14.9-7.9,30.1-6.6,46c2.6,30.2,7.4,60.1,13.7,89.8c9.2,43.3,7.2,86.1-5.6,128.4
                        c-9.3,30.6-19.9,60.7-29.1,91.3c-4.6,15.6-7.9,31.6-10.7,47.7c-3.1,18,0.3,35.9,3.6,53.6c4.1,22.2,8.7,44.4,12.3,66.8
                        c1.1,7-0.4,14.5-1,21.8c-1.8,22.2-3.6,44.5-5.7,66.7c-1.9,21-7.2,41.3-13.9,61.2c-2.8,8.2-7.7,14.7-15.3,19
                        c-6.4,3.5-12.6,7.7-19.4,10.2c-15.8,5.9-29.9,14.5-42.8,25.3c-11.9,9.9-25.2,17.3-40,22c-20.7,6.5-41.5,6.7-62,0.5
                        c-16.8-5.1-33.4-11.4-49.5-18.4c-17.3-7.5-23.2-21.5-17.1-39.4c2.4-7.1,6.1-14.1,10.5-20.2c13.9-19.4,30.8-35.9,50.9-48.9
                        c18.6-12.1,34.6-27.1,48.5-44.3c2.7-3.4,5.1-7.1,8.4-9.9c10.4-8.8,14.2-20.3,15.8-33.4c2.9-23.1,6.3-46.2,9.6-69.3
                        c0.6-4.3,1.5-8.6,3.1-12.6c2.3-5.7,1.9-10.8-0.7-16.3c-18.5-38.8-29.1-80-35.8-122.2c-5.1-31.6-10.3-63.2-13.1-95
                        c-2.2-24.1-1.6-48.6-0.6-72.9c1.5-35,5.7-69.8,15.7-103.5c5.4-18.2,11.9-36,18.8-53.6c4.2-10.7,6.9-21.3,6.7-32.9
                        c-0.6-34.6-0.9-69.3-1.2-104c-0.1-16.2-3-31.7-7.7-47.1c-12.6-41.4-25-82.8-37.3-124.3c-9.2-30.7-13-62.5-18.2-94.1
                        c-4.7-28.8-5.5-57.7-2.8-86.7c2.2-23.5,4.8-47.1,7.8-70.5c3.5-27.1,6.3-54.3,11.9-81c13.3-63.2,30.2-125.6,53.6-185.9
                        c3.5-9,4.6-17.5,2.9-27.1c-3.6-20.9-1.8-41.9,2.1-62.6c3-15.7,6.4-31.3,10.1-46.8c1.9-7.9,1.8-14.5-3.5-21.5
                        c-10.1-13.6-11.8-30.1-13.4-46.4c-2.6-27.2-5.4-54.4-8.1-81.5c-0.2-1.5-0.7-3-1.5-6.1c-2.3,2.9-3.8,4.4-4.8,6.2
                        c-8.5,15.4-17,30.9-25.4,46.4c-3.2,6-4.6,12.1-3,19.2c3.6,16.4,1.1,32.3-4.2,48.1c-8.8,26.5-17.2,53.2-25.6,79.8
                        c-14.2,45.2-31.3,89.2-50.6,132.4c-20.2,45.3-42.1,89.8-63.5,134.6c-4.5,9.4-6.2,18.4-3.8,28.8c5,21.9,0.4,42.5-12,61
                        c-21.4,32.2-43.3,64-64.9,96c-5.9,8.7-13.1,14.7-24.2,15.6c-3.5,0.3-6.8,3.6-9.9,5.8c-1.3,0.9-1.9,2.7-2.9,4.1
                        c-8.5,11.8-19.9,17.2-34.5,15.8c-10.3-1-20.6-1.6-30.8-2.4c-15.6-1.2-26.9-11.4-28.7-26.9c-1.1-8.8-2.9-16.6-8.7-23.8
                        c-6.7-8.4-6.7-18.7-3.5-28.6c1.8-5.7,4.4-11.1,7.3-16.3c6.3-11.4,13-22.5,19.5-33.7c1.6-2.8,3-5.7,5.2-10c-4.3,0-7.1-0.2-9.9,0
                        c-16.6,1.3-29.9-4.8-40.3-17.7c-13-16.2-6.7-39,11.6-45.2c12.5-4.2,22.8-12.2,32.1-21.5c16.5-16.5,33-33,49.5-49.5
                        c2.3-2.3,4.9-4.7,7.8-6.1c7.6-3.8,12.4-9.9,15.8-17.4c18.6-42,37.4-84.1,44.9-129.9c3.4-20.7,6.6-41.4,8.8-62.3
                        c5.7-55.7,23-107.2,54.9-153.5c1.5-2.2,2.8-4.8,4.8-6.4c8.8-7.2,8.4-16,4.6-25.3c-6.5-15.7-8.3-32.1-7.5-49
                        c1.9-41,14-78.5,39.2-111.3c5.4-7,7.8-14.4,8-23.1c0.4-21.3,0.8-42.7,1.8-63.9c2-43.7,21.8-78.9,53.3-108.3
                        c26.1-24.4,57-38.9,92.3-43.9c13-1.8,25.4-5.2,37.3-10.5c28.6-12.8,57-26,85.5-38.7c7.6-3.4,11.2-8.8,11.9-16.9
                        c1-9.9,2.2-19.9,3.6-29.7c0.6-4.5,0.2-8.4-2.7-12.2c-7.1-9-9.4-19.7-10.3-30.8c-0.6-7-1.4-13.9-1.9-20.9c-0.4-5.1-2.4-8.9-7.1-11.1
                        c-7.6-3.6-12.3-9.9-15.8-17.4c-5.7-12.4-11.9-24.6-17.2-37.2c-9-21.1-4.7-37.3,13.7-51c7.6-5.6,10-12.7,10.1-21.5
                        c0.3-30.8,9.1-59,26-84.8c22.6-34.6,54.5-53.4,95.4-58.4c55.4-6.7,109.1,20.3,135.6,69.3c10,18.4,15.3,38.2,16.9,59.1
                        c0.4,5.6,0.9,11.3,1,17c0.1,9,3.7,15.9,11.3,21c6.7,4.6,12,10.6,15,18.3c3.4,8.8,2.8,17.8-0.5,26.4c-4.9,13.1-10.2,26-15.8,38.8
                        c-3.8,8.7-10,15.8-18.6,20.3c-4.3,2.3-6.3,5.5-6.6,10.3c-0.4,6.3-1.4,12.6-1.6,18.9c-0.5,11.6-3.4,22.2-9.9,32
                        c-2.2,3.3-3.2,8.1-2.9,12.1c0.9,11.6,3,23.1,4.1,34.7c0.7,7.4,4.3,12.3,11,15.3c30.4,13.7,60.4,28.1,91.2,40.7
                        c11.8,4.8,25.2,6,37.9,8.2c36.8,6.3,68,23,92.9,50.7c10.2,11.4,19.3,23.8,27.9,36.4c12.7,18.5,16.1,40,17.3,61.8
                        c1.2,20.6,1.7,41.3,2.1,61.9c0.2,10.4,2.9,19.7,9.1,28c24.5,32.8,36.7,69.9,38.5,110.6c0.8,17.8-1.7,35.3-8.7,51.8
                        c-3.1,7.4-2.2,13.1,2.7,19.3c34.9,44,53,94.8,60.9,149.9c4.7,33.3,9.3,66.6,16,99.5c7.3,35.7,21.3,69.4,39.2,101.1
                        c5,8.9,11.8,17,18.9,24.3c18.3,18.9,37.3,37.1,55.9,55.8c6.9,6.9,14.4,12.8,23.9,15.8c4.1,1.3,7.9,3.5,11.6,5.8
                        c13.8,8.8,17.6,24.2,9.3,38.2c-9.4,15.9-23.8,22.6-41.9,22.3c-3,0-5.9-0.1-8.9-0.2c-0.3,0-0.5,0.3-1.5,1c1.5,3.1,2.9,6.4,4.7,9.5
                        c6.1,10.7,12.5,21.2,18.4,32.1c3.2,5.8,5.8,12,8.1,18.2c3.8,10.4,2.3,20.6-4.4,29.3c-4.9,6.4-7,13.2-7.7,21.1
                        c-1.3,15.1-11.5,26-26.7,28c-12.2,1.6-24.5,2.5-36.9,2.8c-11.6,0.3-21.7-4.1-28.6-13.8c-5.5-7.7-11.5-13.2-21.8-13.8
                        c-6.9-0.4-11.7-5.9-15.5-11.6c-11.2-16.6-22.2-33.2-33.4-49.8c-9.5-14.1-18.8-28.3-28.8-42c-15-20.4-21.7-42.6-16.3-67.7
                        c2.4-11,0.3-20.9-4.5-30.9c-22.3-46.5-45.3-92.8-65.9-140.1c-16.6-38.1-30.7-77.3-44.9-116.4c-6.6-18.1-10.6-37.1-16.2-55.6
                        c-2.8-9.2-6.3-18.2-9.9-27.2c-6.4-16.1-10.1-32.5-6.8-49.8c2.1-10.8-0.1-20.2-5.8-29.5c-7-11.3-12.9-23.3-19.4-35
                        c-1.6-2.9-3.3-5.7-6.1-10.5c-1.1,4.5-2,7.1-2.3,9.7c-3.5,31.4-6.9,62.9-10.5,94.3c-1.1,10.1-4.1,19.7-10.4,27.7
                        c-6,7.7-6.4,15.3-3.7,24.3c9.2,30.9,13.9,62.4,12.2,94.7c-0.1,2.7,0.1,5.4-0.5,8c-3.3,13.3-0.2,25.4,5.3,37.7
                        c19.8,44.3,31.8,91,43.5,137.9c12.1,48.6,18.9,98,24.1,147.7c1.9,17.6,3.8,35.1,6,52.6c2.5,19.7,0.2,39.3-1.3,58.8
                        c-4.6,61.6-22.7,120.2-40,179c-7.4,25.2-14.8,50.5-21.9,75.8c-1.8,6.7-3,13.7-3.2,20.6c-0.9,39-1.3,78-2.1,116.9
                        c-0.2,9.9,1.8,19.1,5.9,28c17.8,38.8,27.4,79.8,32.5,122.1c4,33.2,5.8,66.5,3.5,99.8c-1.6,22.9-4.8,45.7-7.9,68.5
                        c-4.2,31.3-9.3,62.6-17.5,93.2c-5.8,21.6-12.8,42.7-22.3,63c-3.9,8.3-4.8,15.8-1.5,24.9c3.2,9,3.8,18.9,5.2,28.4
                        c2.9,19.8,5.2,39.6,8.3,59.3c0.8,5.1,2.8,10.6,5.9,14.5c9.6,11.9,19.4,23.8,30.4,34.5c10.7,10.4,22.3,20,34.6,28.6
                        c20.2,14.1,37.5,30.9,52.1,50.6c7.4,9.9,11.9,20.7,11,33.4c-0.7,9.7-4.8,17.2-13.5,21.9c-25.4,13.8-52.1,23.9-81.3,25.5
                        c-21.6,1.2-41.7-3.9-59.9-15.7c-8.1-5.3-15.8-11.1-23.4-17c-9.8-7.6-20.3-14-32.2-17.7c-23.8-7.3-38.5-23.1-43.4-47.5
                        c-3.2-16-6.1-32-8.4-48.2c-2.3-16.8-3.3-33.8-5.5-50.7c-3.1-23.8-0.8-47.1,4.5-70.4c3.5-15.2,6.6-30.6,9.2-46
                        c4.3-26.2,0.9-51.9-7-76.9c-8.3-26.3-17.5-52.4-26-78.7c-10.8-33.5-17.1-67.8-16.8-103.1c0.2-21.4,5.1-42.3,8.4-63.3
                        c4-25.3,7.5-50.7,10.5-76.1c1.8-15.1-0.9-29.6-6.8-43.9c-23.1-55.9-35.5-114.2-39-174.6c-2.2-38.4,0.4-76.6,3.9-114.7
                        c1-10.7,0.8-20.7-4.4-30.9c-4.4-8.7-5.6-19.1-7.6-28.8c-7.4-35.5-15.2-71-21.6-106.7c-4.4-24.6-7.3-49.4-9.9-74.2
                        c-2.4-23.2-3.7-46.4-5.1-69.7c-0.4-6.9,0.8-14,1.1-20.9C761.9,1485.5,761.7,1481.6,761.7,1476.4z M508.2,951.5
                        c0.8,0.1,1.5,0.2,2.3,0.4c0.5,3.9,1.1,7.8,1.4,11.7c3.5,39.2,6.6,78.3,10.4,117.5c1.3,13.4,5.4,26.2,14.3,36.7
                        c3.9,4.6,4.6,9.3,3.2,14.8c-2.3,9-4.7,18.1-6.8,27.1c-7.1,30.6-12.4,61.5-6.7,93c1.2,6.5,0.4,12.5-2,18.5
                        c-14.2,36-25.5,72.9-35.6,110.1c-10.3,37.6-20.5,75.2-25.6,113.9c-4.1,30.7-7.8,61.5-11,92.3c-2.1,19.9-3.5,39.8-4.1,59.8
                        c-0.8,27.5,4.3,54.4,9.1,81.3c3.9,21.9,7.9,44,13.9,65.4c12.5,44.5,26.6,88.6,39.6,133c2.5,8.5,3.9,17.6,4.2,26.5
                        c0.9,26.3,1.1,52.6,1.6,79c0.2,11,0.2,22,0.7,33c0.6,12.9-1.7,25.1-7.1,36.9c-20.4,44.8-30.1,92.3-33.8,141.1
                        c-2.3,30.3-3.7,60.6-0.1,90.8c3,24.8,6.2,49.6,10,74.2c7.6,48.8,18.2,96.9,40.3,141.6c2.4,4.8,2.5,9.2,0.6,14.3
                        c-2.2,6.2-4,12.7-5,19.2c-3.7,24-7,48.1-10.5,72.2c-1.3,9.1-4.6,17.3-11.3,24c-4.5,4.5-8.7,9.2-12.7,14.1
                        c-14.4,17.7-30.9,33-49.9,45.8c-8,5.4-15.8,11.2-22.9,17.7c-13.4,12.1-25.8,25.3-33.9,41.6c-9,18-4.4,31.1,14,38.9
                        c15.3,6.4,31,12.2,46.9,16.9c19.6,5.8,39.5,5.2,59-1.3c15.1-5,28.2-13.5,40.2-23.7c11.6-9.8,24.4-17.3,38.8-22.1
                        c3.1-1.1,6.3-2.3,9.2-3.8c12.1-6,20.8-14.8,24.9-28.3c6.5-21.4,11.2-43.1,12.5-65.5c1.2-21.3,4.1-42.4,5.9-63.6
                        c0.5-5.6,0.1-11.3-0.9-16.8c-3.3-18.7-6.6-37.4-10.7-55.9c-8.2-37.1-6.5-73.3,6.2-109.2c8.3-23.5,15.9-47.3,23.7-71
                        c16.7-50.4,21-101.4,9.5-153.6c-7.1-32.2-12-64.8-12.9-97.9c-0.2-8.8,1-17.2,4.5-25.3c27.7-64.1,41-131.3,43.3-200.9
                        c1.2-35.7-1.3-71.3-5.7-106.7c-0.8-6.6-0.1-12.3,4-17.9c2.4-3.4,3.6-7.8,4.6-12c16-67.7,30.6-135.8,37.1-205.2
                        c3-31.8,7.2-63.6,2.6-95.7c-0.4-2.5,0-5.1,0-8c11.4,0,21.9,0,33,0c-0.4,6-0.8,11.3-1.1,16.5c-0.6,11-1.7,21.9-1.7,32.9
                        c0.1,30.4,3.6,60.5,7.6,90.6c7.5,57.2,18.6,113.7,32.8,169.5c0.9,3.5,1.7,7.4,3.9,10.1c5.1,6.3,5.6,13.2,4.6,20.7
                        c-4.5,32.4-6,65-5.4,97.7c0.9,44.1,5.7,87.7,16.5,130.5c6.1,23.9,14.2,47.1,23.6,69.9c3.7,9.1,7.1,18.9,7.8,28.6
                        c0.9,11.1-0.7,22.6-2.3,33.8c-3.6,25.4-7.8,50.7-12,75.9c-4.1,24.4-7.2,48.7-5.1,73.6c1.9,22.4,6.5,44.1,13.3,65.4
                        c9.6,30.1,19.9,60,29.4,90.2c9.9,31.5,11.2,63.4,3.2,95.6c-2.5,10-4.8,20.1-7,30.1c-5.2,24-6.4,48.1-1.9,72.4
                        c0.8,4.2,0.9,8.6,1.2,12.9c1.8,29.7,5.8,59,13.9,87.6c3.5,12.5,10.1,22.8,22.7,27.4c23.5,8.6,44.1,21.7,63.7,36.8
                        c11,8.4,23.3,14.2,37,16.9c38.3,7.4,72.3-5.4,105-23.2c8.8-4.8,11.8-14,8.5-23.4c-2.8-8.1-6.5-16.2-11.3-23.1
                        c-12.9-18.5-29-33.9-47.9-46.2c-18.6-12.1-34.7-26.9-48.8-44c-5.3-6.4-10.7-12.8-16.5-18.8c-4.8-5-7.6-10.6-8.6-17.5
                        c-3.7-26.7-7.5-53.4-11.6-80.1c-0.7-4.9-1.8-9.9-4-14.3c-2.9-5.7-2.8-10.7,0.1-16.1c9.8-18.7,16.3-38.6,22.4-58.7
                        c9.4-31,14.1-63,19.6-94.8c5.1-29.9,9.6-59.9,9.8-90.3c0.1-18-0.9-36-1.7-53.9c-2.1-51.4-12.1-101.1-33.3-148.2
                        c-5.1-11.2-7.5-22.5-7.2-34.8c0.8-36.6,1.3-73.3,1.7-110c0.2-13.1,2.1-25.8,5.8-38.3c8.9-29.6,17.2-59.5,26-89.1
                        c15.7-53.1,30.1-106.4,33.7-161.9c1.4-21,2.2-41.9-0.4-62.9c-2.1-16.9-4.2-33.7-6.4-50.6c-3.6-27.7-6.1-55.7-11.3-83.1
                        c-13-68.1-30.2-135.1-58-199c-3.7-8.5-4.4-17.3-2.5-26.4c0.9-4.5,1.3-9.2,1.4-13.9c0.7-33.3-3.7-65.8-14.2-97.4
                        c-2.5-7.7-1.8-14.1,3.4-20.5c7.8-9.6,11.7-20.9,12.9-33c3.6-37.5,7-75,10.4-112.4c0.5-5.4,1.3-10.7,2-16.1c2.8,1.9,4,3.8,4.8,5.9
                        c10.3,26.4,22.4,52,38.5,75.4c3.6,5.2,3.6,10.5,2.2,16.2c-4.3,18.1-2,35.6,4.6,52.9c4.9,12.7,9.4,25.6,13.5,38.6
                        c3.5,11.1,5.1,22.8,9.1,33.7c13.7,37.2,26.6,74.7,42.4,110.9c22.9,52.5,47.9,104,72.2,155.9c5.6,11.8,8.1,23.5,5,36.7
                        c-4.4,18.7-0.8,36.5,9.4,52.7c4.6,7.3,10.1,14.1,15,21.2c10.6,15.7,21.1,31.5,31.6,47.3c7.2,10.8,14.4,21.6,21.7,32.3
                        c4.4,6.5,10.4,10.2,18.4,9.9c6-0.2,10,3,13.3,7.6c2,2.7,3.7,5.6,5.7,8.2c6.9,8.5,16,12.3,26.6,10.1c9.2-1.8,18.3-2.5,27.7-2.1
                        c13.9,0.5,22.4-7.3,24.8-21c0.4-2.3,0.6-4.6,0.7-6.9c0.1-6.3,2.1-11.6,6.6-16.2c7.3-7.4,8.4-16.5,5.2-26c-2.2-6.6-5.2-13-8.6-19.1
                        c-7.7-14-16-27.7-23.9-41.6c-1.9-3.4-3.5-6.9-6.4-12.6c6.9,1.1,11.8,1.4,16.4,2.6c15.4,4.2,28.4-0.3,39.3-11
                        c13.1-12.9,10.3-29.3-6-37.7c-1.5-0.8-2.9-1.6-4.5-2.1c-14.4-4.3-24.3-14.2-34.3-24.8c-15.6-16.4-31.8-32.2-48.4-47.6
                        c-8.1-7.5-19.2-12.3-20.6-25.2c0-0.3-0.3-0.6-0.4-0.9c-30.8-56.7-47.7-117.6-53.9-181.6c-3.1-32.3-8.1-64.1-20.1-94.4
                        c-11.8-29.5-25.4-58-47.3-81.6c-3.6-3.8-3.5-7.4-1.2-11.8c6.2-12,9.8-24.9,10.5-38.3c2.6-45.1-7.7-86.7-35.2-123.2
                        c-8.1-10.7-11.2-22-11.4-35.2c-0.4-23.6-1.6-47.3-3.3-70.9c-1.3-17.8-6-34.9-16.3-49.8c-8.1-11.8-16.7-23.3-26.2-34
                        c-23-25.6-51.5-42.1-85.7-47.4c-27.3-4.2-52.9-12.9-77.7-24.8c-19.2-9.2-38.6-18.1-58.2-26.5c-8.8-3.8-12.6-9.1-13.1-18.8
                        c-0.6-12.6-2.6-25.1-5.3-37.5c-1-4.8-1.4-8,2-11.6c6.7-7,10.1-15.6,11.2-25.1c1.1-9.9,2.1-19.9,2.8-29.8c0.4-5.1,2-8.9,7.2-10.8
                        c8.6-3.2,14.3-9.7,17.9-17.9c5.2-11.5,10.5-23.1,14.8-34.9c6.9-18.9,3.1-31-13.6-42.4c-8.7-5.9-12.3-13.4-11.7-23.6
                        c0.8-14.1-0.1-28.1-4.2-41.6c-13.5-45.2-40-78.6-86.4-93.2c-16.7-5.3-34-6.2-51.4-5.2c-22.9,1.2-43.9,8.2-62.1,22.1
                        c-28.3,21.6-46.7,50-53.7,85.1c-2.3,11.3-2.6,23.1-3.2,34.8c-0.5,9.4-4.1,16.4-12.1,21.5c-16.1,10.3-20.4,23.8-13,41.2
                        c6,14.1,12.8,27.8,19.4,41.6c2.7,5.6,6.5,10.2,12.6,12.1c6.4,2,8.8,6.6,9.2,12.8c0.6,8.6,1.5,17.3,2.1,25.9
                        c0.7,10.6,3.7,20.1,11.2,28.1c1.9,2,3.4,6.4,2.6,8.9c-4.1,12.6-4.7,25.5-5.1,38.5c-0.2,8.6-4,14.1-12,17.7
                        c-25.9,11.4-51.5,23.4-77.3,35c-19.8,8.9-40.4,15.6-61.9,18.9c-20.5,3.1-39.2,10.4-56.1,22.3c-18.1,12.8-33.6,28.3-46.8,46.1
                        c-14,18.9-22.4,40.1-24.1,63.6c-1.6,21.9-3.1,43.9-3.5,65.9c-0.3,15.3-4.7,28.7-12.6,41.4c-5,7.9-9.9,15.8-14.2,24.1
                        c-15.5,29.6-23.8,60.9-19.3,94.4c1.8,13.1,5.7,25.9,9.6,38.6c2,6.5,1.3,11.2-3.1,16.4c-5.8,6.8-11.2,14.1-16.1,21.6
                        c-29,44.5-43.2,93.9-48.8,146.4c-2.9,26.4-6.9,52.9-12.5,78.9c-8.9,40.9-27,78.6-43.7,116.7c-2,4.5-6.8,7.9-10.7,11.4
                        c-4.4,4-9.7,7-13.9,11.2c-16.1,15.9-32.1,32-47.9,48.2c-7.8,8-16,15-27.1,18.1c-3.1,0.9-6.1,2.7-8.9,4.4c-13.1,7.8-15.7,21-6.4,33
                        c10.4,13.4,24.1,18.2,40.7,15c5.3-1,10.6-1.9,17.8-3.1c-2.7,5.2-4.3,8.5-6.1,11.6c-8.6,15-17.3,30-25.9,45.1
                        c-2.3,4-4.6,8.2-6.1,12.6c-3.8,11-4.2,21.5,4.8,30.7c4.7,4.8,5.8,10.9,6.1,17.5c0.7,18.1,12.5,27.9,30.3,25.9
                        c5.2-0.6,10.6-0.4,15.7,0.6c19.6,4.2,28.4,1,38.8-15.6c3.5-5.6,7.4-9,14.4-8.6c6.8,0.4,12.4-2.7,16.4-8c6.4-8.5,13.2-16.9,18.4-26.1
                        c11.6-20.7,24.7-40.3,39-59.2c3.8-5,7.5-10.2,11.1-15.4c11.5-17.2,15-36,10.6-56.3c-2.4-11.1-1.4-21.8,3.9-32
                        c3.7-7.1,7-14.4,10.5-21.6c26.6-55.5,54.7-110.4,76.3-168.2c14-37.4,29.2-74.3,38.3-113.4c2.6-11.3,6.4-22.5,11-33.1
                        c8.3-19.2,12.1-38.8,7.1-59.4c-1.6-6.7-0.2-12.3,3.6-17.9c15.4-22.7,27.2-47.3,36.9-72.9C505.4,957.4,506.9,954.5,508.2,951.5z"/>
                    <path class="st0" d="M2472.6,958.7c-2.8,18-5.4,36-8.4,54c-4.5,27.4-12,53.9-22.7,79.5c-1,2.5-2.1,4.9-3.1,7.4
                        c-1.5,3.9-0.9,7.1,3,9.4c6.7,4.1,9.6,10.5,11.2,18.1c3.8,18.6,8.6,37,12.3,55.6c1,4.9,0.6,11-1.5,15.4c-2.9,6.2-2.3,11,0.1,17
                        c13.4,32.2,19.8,66.2,24.7,100.5c1,6.7-0.7,13.9-1.1,20.8c-0.6,10-1.6,19.9-1.7,29.9c0,2.5,1.7,6.2,3.8,7.5
                        c10.5,6.2,14.9,16.1,16.3,27.3c3.2,25.4,6.5,50.9,8.4,76.4c3.7,48.6,2.3,97.3,0,145.9c-2.4,51-7.8,101.6-16.4,151.9
                        c-8.5,49.9-16.9,99.8-24.5,149.8c-1.9,12.4-0.5,25.3-0.1,37.9c0.6,20.6,1.9,41.3,2.5,61.9c0.4,14.1-1.4,27.9-6.6,41.2
                        c-2.6,6.6-2.3,13,0.4,19.4c3.8,8.9,7.7,17.7,11.6,26.5c16.2,37.1,28.2,75.4,34,115.5c2.5,17.3,1.3,34.5,0.3,51.8
                        c-2.4,40.6-14.8,78.9-25.9,117.7c-13.6,47.6-25.1,95.9-37,144.1c-5.3,21.3-4.5,42.9-1.3,64.5c2.6,17.4,4.1,35,6.2,53.9
                        c1.9,0.1,5.1,0.3,8.3,0.3c12.7-0.3,25.3-0.9,38-0.9c26.5-0.1,46.7,12.1,61.4,33.5c16.7,24.3,12.3,54.4-9.8,74
                        c-9.7,8.6-21.3,13.5-33.4,17.5c-8.2,2.7-16.5,5.1-24.8,7.6c-10.3,3.1-18.4,9-24,18.3c-9,14.7-22.4,23.5-38.7,28
                        c-19.8,5.5-40,6.8-60.4,4c-15.6-2.2-26.4-10-31.2-25.5c-6.9-21.8-9.8-43.8-7.7-66.9c1.4-15.8,0.9-32.1-1.8-47.7
                        c-6.9-40.6-4.4-80.3,6.4-119.7c2.9-10.6,5.9-21.2,8.3-31.9c3.4-16,1.2-31.8-2.1-47.6c-7.4-35.9-14.4-71.8-22.2-107.6
                        c-1.7-8-4.8-16-8.8-23.2c-14.4-25.8-19.8-53.3-19.4-82.6c0.5-30.7,8.2-59.9,17.3-88.9c3.5-11.1,6.4-22.4,9.9-33.5
                        c2.4-7.4,1.1-14.1-4-19.6c-16.4-17.6-24.9-39-31.6-61.6c-17.8-59.7-33.5-119.9-44.4-181.2c-5.6-31.6-7-63.5-8-95.5
                        c-1.6-51-4-101.9-4.3-152.9c-0.2-34.3,2.8-68.5,4.5-102.8c0.5-9.6,0.7-19.3,1.6-28.9c0.9-9.2,1.9-18.5,3.9-27.6
                        c1-4.7,1.1-8.2-2.2-11.9c-5.1-5.6-9.9-11.6-15.7-18.5c-6,7.4-11.7,13.8-16.5,20.8c-1.5,2.2-0.8,6.2-0.5,9.3
                        c0.6,5.6,2.3,11.1,2.6,16.6c1.8,27.9,3.9,55.8,5,83.8c1.1,27.3,2,54.6,1.5,81.9c-0.9,49.3-2.9,98.6-4.5,147.9
                        c-1.6,50.2-10,99.3-22.5,147.8c-7.8,30.3-15.1,60.7-23.6,90.8c-4.9,17.3-10.8,34.3-17.8,50.8c-4.2,9.9-10.5,19.4-17.4,27.7
                        c-6.8,8.1-8.6,16-5.6,25.8c6.2,20.4,12.7,40.7,18,61.3c7.3,27.9,10.9,56.2,7.2,85.1c-2.4,19.1-7.8,37.1-17.3,53.8
                        c-4.7,8.2-7.9,16.7-9.7,26c-7.5,37.9-15.2,75.7-23.1,113.5c-3.5,17-2.8,33.7,1.3,50.5c4.1,16.5,8.4,32.9,12.2,49.4
                        c6.3,27.7,5.8,55.6,1.6,83.5c-3.3,21.9-4.5,43.7-2.7,65.7c1.8,21.7-2.2,42.4-10.1,62.5c-4.1,10.6-11.4,16.7-22.3,19.3
                        c-27.9,6.4-54.9,2.8-81.3-7.4c-7.9-3.1-14.4-8.3-19-15.7c-9.8-15.9-24.2-25.2-42.1-29.7c-8.7-2.2-17.2-5.2-25.6-8.3
                        c-15.1-5.5-26.1-15.8-33.8-29.7c-7.7-13.9-8.6-28.5-3.1-43.4c10.6-28.8,36.3-46.4,68.9-47c11.3-0.2,22.6,0.6,34,0.9
                        c3.6,0.1,7.2,0,11.7,0c0.8-4.1,1.6-7.6,2-11.1c1.5-14.6,2.3-29.2,4.4-43.7c4-27.2,1.3-53.9-5-80.3c-14.2-59.2-29.1-118.2-48.1-176.2
                        c-4.6-14.1-6-29.3-7.9-44.1c-3.2-25.5-5.6-51-3.2-76.7c0.6-6.6,1.5-13.3,3.1-19.7c10.2-39,20.5-77.9,38.8-114.2
                        c5-9.9,6.4-20.5,3.2-31.1c-8.2-26.7-8.4-53.8-4.8-81.2c3.9-29.7,2.2-59.3-3.3-88.5c-10.4-54.6-19.5-109.5-26.5-164.7
                        c-9.9-78.8-14-158-10-237.4c1.5-30.2,6-60.3,9.3-90.4c1.2-11.1,5.7-20.5,15.7-26.5c4-2.4,4.9-6,4.4-10.3c-0.7-6.6-0.7-13.4-2-19.9
                        c-4.2-21.6,0.5-42.3,5.2-63.1c5.2-23.1,10.1-46.2,19.5-68c1.9-4.5,2.4-9.1-0.4-13.4c-3.7-5.8-3.3-12.2-2-18.4
                        c3.9-18.2,8-36.4,12-54.6c2.2-9.8,8.6-16.3,17.6-21.3c-22.8-51.1-32.6-104.9-35.2-160.6c-4.9,0.9-5.3,4.5-6.7,7.2
                        c-11.5,22.1-22.7,44.5-34.7,66.4c-5.6,10.2-12.5,19.6-19.4,29c-4.2,5.7-6.2,11.3-5.1,18.5c2.4,15.2,0.3,30.2-7,43.7
                        c-17.3,32.1-29.8,66.1-42.9,99.9c-19.4,50.1-44.8,97.3-69.6,144.9c-10.8,20.7-21.7,41.2-32.6,61.9c-3.4,6.5-7.2,12.8-13.8,16.7
                        c-3.9,2.3-4.8,5.9-4.7,10.3c0.1,13.3,0.4,26.7-0.3,40c-0.5,9.2-1.2,18.8-3.9,27.5c-6.2,20.4-15.2,39.7-26.3,58.1
                        c-11.1,18.2-21.7,36.7-32.2,55.2c-6.5,11.3-14.5,19.7-28.8,19.9c-3.2,0-7.4,3.4-9.4,6.3c-8.5,12.7-19.8,17.9-35,16.3
                        c-8.6-0.9-17.3-0.5-26-0.5c-13.7-0.1-22.6-7.6-24.3-21.4c-1.2-9.8-3.7-18.8-9.7-26.8c-3.8-5-4.7-11.2-2.7-17.2
                        c3.4-10,7-20.1,11.4-29.7c4.2-9.4,9.6-18.3,14.3-27.5c1.9-3.8,3.6-7.7,6.1-13.2c-8,1.2-14.1,2.4-20.3,3.1
                        c-6.3,0.7-12.6,1.6-18.8,1.1c-14.8-1.3-24.3-10.3-29.8-23.4c-4-9.5-0.6-17.7,8.5-22.8c1.2-0.7,2.4-1.2,3.6-1.8
                        c23.9-10.2,43.2-26.3,58.7-46.9c13.8-18.3,30.3-32.7,51.9-40.7c5.5-2,8.7-5.9,10.6-11.2c17.4-50,34.9-99.9,52-150
                        c3.6-10.5,7.2-20.8,14.5-29.3c6.9-8.1,9.1-17.4,10-27.6c1.6-18.2,3.5-36.5,5.6-54.7c2.4-20.4,9.2-39.6,19.1-57.5
                        c10.5-18.9,21.7-37.4,32.7-56.1c1.2-2,2.5-4.1,4.3-5.5c7.8-5.7,9.9-13.6,10.5-22.7c1.2-19.8,5.3-38.9,13.7-57
                        c2.5-5.4,3.3-10.4,1.1-16.3c-3.6-9.3-2.1-19.1-0.6-28.7c3.4-22.9,8.9-45.5,20.9-65.3c12.8-21.2,18.5-44,20.6-68.1
                        c4.5-51.7,29.5-90.9,72-119.8c27.9-19,58.3-32.2,91.3-38.8c29.7-6,57.4-16.9,83.4-32.3c8.9-5.3,17.6-10.7,26.5-16
                        c8.5-5.1,13.9-12.3,15.3-22.2c1.1-8.2,1.9-16.5,2.8-24.8c0.5-5.1-1.5-9.1-5.6-12.1c-9.3-6.8-13.3-16.3-14.4-27.4
                        c-1-9.9-2.3-19.9-3.2-29.8c-0.5-6.2-2.5-10.9-8.8-13.1c-6.1-2.1-9.9-6.7-12.6-12.2c-6.7-14.1-13.7-28.1-19.8-42.5
                        c-7.3-17.3-2.4-32.3,13.8-42c7.8-4.7,11.2-10.7,11.1-19.6c-0.4-22.9,3.8-45.1,12.7-66.2c22.8-53.9,75.3-85.3,132.7-79.6
                        c57.5,5.8,103.5,47.6,114.7,105.1c2.7,14,3.4,28.4,4,42.7c0.3,8,2.7,13.3,10,16.6c7.9,3.6,12.9,10.2,16.6,17.9
                        c3.8,8.1,4.2,15.8,0.2,24c-6.3,12.9-12.2,25.9-18.5,38.8c-3.8,7.9-8.3,15.1-17.4,18.3c-5.1,1.8-6.1,6.6-6.6,11.4
                        c-1.1,10.6-2.4,21.2-3.5,31.8c-1.1,10.4-5.2,19.2-13.8,25.6c-4,3-6.5,6.8-6,11.9c0.9,8.9,1.7,17.9,3.1,26.8
                        c1.4,8.7,6.2,15.5,13.7,19.9c19.4,11.6,38.9,23.2,58.6,34.4c5.1,2.9,11.1,4.5,16.8,6.2c21,6.3,42.3,12,63.2,18.6
                        c29.7,9.3,55.8,25.2,79.1,45.5c29.1,25.4,44.9,58.2,50.5,96.1c1.8,11.9,2.9,23.8,4.3,35.7c0.8,6.7,2.9,12.5,6.7,18.4
                        c5.5,8.6,9.5,18.3,13.6,27.7c9,20.9,15.9,42.5,18.1,65.2c0.7,7.1-0.1,14.6-1.6,21.7c-1.2,5.8-1.5,11,1,16.5
                        c8.8,19,13.2,39.1,13.9,59.9c0.2,7.4,2.6,13.6,8.7,18.2c1.6,1.2,3.1,2.6,4.1,4.3c17.7,26.7,34.5,53.8,45.5,84.1
                        c7.3,20.3,10.1,41.3,11.7,62.6c0.9,11.9,2.3,23.9,3.9,35.7c0.5,3.5,1.5,7.3,3.3,10.3c15.3,26,25.1,54.3,34.9,82.7
                        c11.2,32.4,23,64.6,34.5,96.9c3.3,9.3,9.1,16,18.6,19.6c13.7,5.2,24.9,14.2,34.7,24.8c7,7.6,13.8,15.4,20,23.6
                        c10.4,13.5,23.2,23.8,38.7,30.7c5.5,2.4,11,4.9,15.9,8.3c9.3,6.5,11.3,15.4,5.9,25.4c-7.6,14.2-19.8,21.4-36,20.5
                        c-7.6-0.5-15.1-2.3-22.7-3.5c-2.2-0.4-4.5-0.5-8.6-1c2.3,5,3.9,8.8,5.7,12.6c7.4,15.3,15.2,30.4,22.3,45.8c5.9,12.7,5.8,25-3.9,36.4
                        c-2.2,2.5-2.7,6.7-3.6,10.2c-1.4,5.5-1.9,11.2-3.9,16.5c-4.4,11.3-13.5,15.7-25.5,14.3c-7.5-0.9-15.2-1-22.6,0.1
                        c-12.8,1.9-23.1-1-30.9-11.5c-5.2-7.1-11.8-11.2-20.7-12.4c-8-1-14.1-6.3-18.2-13c-20.1-33-40.6-65.8-55.7-101.5
                        c-7.3-17.4-10.6-35.6-9.6-54.6c0.5-10.3,0.1-20.7-1-30.9c-0.4-3.8-3.7-7.4-6.1-10.8c-3.6-5.1-8.5-9.5-11.3-15
                        c-28.1-54.5-56.3-108.8-83.4-163.8c-10.9-22-19.1-45.4-28.8-68c-8.6-20.2-16.9-40.6-26.7-60.2c-9.9-19.8-16.7-39.9-13.4-62.3
                        c0.7-4.5-0.8-8.1-3.6-11.9c-7.5-10.2-14.9-20.5-21.3-31.3c-10.1-17.2-19.4-34.9-29.1-52.4c-1.6-2.8-3.4-5.5-5.1-8.2
                        C2474,958.3,2473.3,958.5,2472.6,958.7z M2541,794.5c13.4,3.6,27.1,2.6,40.6,0.9c11.3-1.4,13.3-3.7,12.1-14.9
                        c-1.6-15.5-3.1-31.2-6.1-46.5c-6.3-32.4-21.8-59.7-47.3-81.2c-22.5-18.9-47.9-32.3-75.3-42.3c-11.4-4.1-22.1-3.2-32.8,2.2
                        c-23.1,11.8-37.1,30.7-43.5,55.5c-2.4,9.3-0.2,17,7.8,22.9c28.6,20.9,53.9,45.5,78.5,70.9c12.8,13.1,28.2,22.1,45.6,27.9
                        c4.4,1.5,8.7,3.2,13,4.7c-6.8-1-13.6-1.9-20.9-3c0.9,15.4,10.9,26.2,14.9,39.3c-2.6-2.2-4.6-4.7-6.4-7.3c-8-11.5-15.8-23-24-34.4
                        c-2.5-3.5-5.4-6.8-8.8-9.3c-5.8-4.3-11.2-3.2-15.7,2.3c-1.8,2.2-3.4,4.7-5.5,7.7c-3.8-8.8-7.3-16.7-10.7-24.6
                        c-11.4-26.8-29.4-48.1-53-64.9c-14.6-10.5-22.1-7.6-25.5,9.9c-0.1,0.7-0.2,1.3-0.3,2c-5.4,27.1-16.1,51.9-31.5,74.9
                        c-27.4,40.9-54.2,82.3-81.3,123.4c-1.5,2.2-2.8,4.5-4.2,6.8c-5.8,9.9-5.5,19.7,1.2,29c19.8,27.7,43.6,51.1,73.2,68.3
                        c16.4,9.5,33.8,11.2,52,5.5c13-4,24.3-10.7,34.3-19.8c2.3-2.1,4.7-4.1,7.1-6.1c-7.7,17.8-20.6,29.1-39.9,31.8
                        c-16.8,2.3-33.1-0.2-49-6c-2.9-1.1-5.9-3.7-9.7,0.5c11.1,24,23.7,47.9,29.6,74.3c-1.6-2.7-3.9-5.3-4.8-8.2
                        c-7.5-24.9-19.4-47.5-36-67.3c-17.5-20.9-36-41-54.5-61.1c-11.8-12.9-19.6-11-25.9,5.5c-8.6,22.6-12.9,46-13,70.3
                        c-0.2,57.9-0.9,115.8-1.2,173.7c-0.2,55-0.1,109.9,0,164.9c0,3.3,0.1,6.7,0.9,9.9c1.4,5,5.1,6.3,9,3c2.7-2.3,5-5.3,7.1-8.3
                        c15.5-22.2,27.9-46.1,36.7-71.8c8-23.6,16-47.2,24.1-70.8c6.4-18.6,14.4-36.5,25.6-52.8c7-10.1,15.7-17.9,27.4-22.3
                        c10.5-3.9,11.5-6,10.1-17.4c-1.7-13.9-3.5-27.7-5.2-41.6c5,18.8,10.1,37.6,15.2,56.4c10.4-3.3,16.5-7.7,25.5-18.4
                        c11.7-13.9,20.3-29.6,27.5-46.2c14.6-33.9,23.1-69.4,27.8-105.9c1.4-10.7,5.1-21.5,0-32.4c-0.7-1.5,0.2-3.9,0.7-5.8
                        c4.4-16.7,8.9-33.4,13.5-50.1c0.1-0.4,0.9-0.6,1.3-0.8c0.3,0.3,0.7,0.4,0.8,0.7c1.2,3.1,2.5,6.2,3.7,9.3c9.3,23.7,23.3,44.2,43.4,60
                        c11.9,9.4,24.4,5.5,28.8-9.1c2.3-7.6,3.5-15.6,4.1-23.5c1.8-21.2-1.1-41.9-7.4-62.1c-1.1-3.6-1.8-7.3-2.7-11
                        c11.8,19.1,27.8,33.9,45,47.6c-9.6-1.2-16.6-9.3-27.3-9.5c0,3.9,0,7.2,0,10.5c-0.1,9.7-0.1,19.3-0.4,29
                        c-0.4,11.9-5.3,21.9-14.1,29.9c-8.8,8-16.5,8.8-26,1.7c-5.3-4-9.9-8.9-14.8-13.4c-2.9-2.7-5.6-5.8-8.8-8.1c-1.2-0.9-4.2-0.7-5.4,0.2
                        c-1.2,0.9-1.7,3.4-1.6,5.2c0.1,1.5,1.3,3,2.1,4.4c13,22.9,25.9,45.8,39,68.6c2.5,4.3,5.5,8.3,8.4,12.4
                        c10.1,13.5,23.8,19.2,40.4,20.4c18.3,1.3,34.7-4.6,51.6-9.9c5.4-1.7,9.7-4.1,12.5-9c-0.5,4.3-0.8,8.6-1.7,12.8
                        c-2,9.1-0.4,17.1,6.2,24c3.9,4.1,7.7,8.3,11.4,12.6c1.7,2,3.2,4.2,4.8,6.3c-1.8,0.2-2.8-0.1-3.5-0.7c-8.4-6.6-16.8-13.1-25-19.9
                        c-14.7-12-15.2-14.3-36.2-2.9c-0.9,0.5-1.7,1-2.6,1.5c-7.9,5.2-9.1,8.1-6.9,17.6c0.3,1.2,0.5,2.4,0.8,3.6c-4.8-3.6-8.4-7.8-12-12.1
                        c-5.1-6.1-10.2-12.4-15.4-18.4c-3-3.5-6.2-8-11.5-6.3c-6,1.9-5,7.9-5.4,12.8c-0.1,1.3,0.1,2.7,0.1,4c0.3,12.6,4,24.2,9.5,35.4
                        c14.2,28.3,27,57.1,38.3,86.7c7.3,19.3,15.5,38.3,24.5,56.8c19.9,40.7,40.6,81,61.1,121.3c6.5,12.8,13.3,25.4,20.2,37.9
                        c4.5,8.1,7.6,9.1,16.4,5.8c6.8-2.6,13.5-5.6,20.2-8.6c10.8-4.7,22-7.7,33.7-8.4c1.9-0.1,3.9,0.5,5.9,0.8
                        c-16.9,2.7-31.1,11.1-46.1,17.4c-9.4,4-12.9,10.5-12.5,20.3c0.4,11,0.6,22,0.2,33c-0.7,19.4,2.7,37.9,11.8,55
                        c15.1,28.2,30.5,56.2,46.1,84.1c2.9,5.1,7,9.8,11.2,13.9c3.7,3.7,9.1,3.3,12.8,0.4c4.2-3.2,2.8-6.9,1.1-10.7
                        c-4.2-9-8.5-18.1-12.6-27.2c-0.9-2-1.2-4.4-1.8-6.6c3.4,2.7,5.2,5.8,6.7,9.1c6.5,14.2,12.6,28.7,19.7,42.6c3.1,6,8,11.4,12.9,16.2
                        c3.9,3.8,9.1,3.4,14,0.9c4.6-2.3,5.9-6,5.3-10.8c-0.2-1.3,0-2.7,0-4.1c5.1,4.9,9,10.1,14.1,13.4c9.5,6.1,19.3,0.7,19.6-10.6
                        c0.1-4.8-1.3-9.9-3.3-14.3c-3.9-8.8-8.8-17.2-13.2-25.8c-2.5-4.9-5-9.8-7.4-14.7c3,1.6,4.5,4,6,6.5c3.7,6.3,7.1,12.8,11.2,18.9
                        c4,5.9,8.2,7.7,12.4,6.2c5-1.7,7.8-7.2,6.9-14.1c-0.3-2.3-1-4.6-1.9-6.7c-3.1-7.4-6-14.8-9.6-21.9c-10-19.6-20.4-38.9-30.6-58.4
                        c-1.6-3-4.7-6-1.8-9.8c0.9-0.1,1.6-0.4,2.2-0.3c1,0.2,2,0.5,2.8,1c15.8,10.6,34.1,12.7,52.3,14.9c10.1,1.3,17.3-3.5,22.5-11.8
                        c3.2-5.2,2.6-8.5-2.6-11.8c-4.8-3-9.9-5.4-15.1-7.7c-14.2-6.4-26.3-15.6-36.3-27.6c-3.8-4.6-7.1-9.7-11.1-14.2
                        c-18.7-21.5-39.7-39.5-69.6-43.4c4.9-7.2,5-7.2,2.6-15.6c-1.1-3.8-2.6-7.6-3.9-11.3c-17.6-48.1-36.3-95.9-49.6-145.5
                        c-0.3-1.3-0.6-2.6-1.2-3.8c-3-6.6-7.8-7.4-11.8-1.3c-2.6,3.9-3.9,9.1-4.2,13.9c-0.6,11-0.2,22-0.2,33c0,2.3-0.2,4.7-0.3,7
                        c-2.2-3.6-2.8-7.2-2.8-10.7c-0.1-19,0.1-38-0.1-57c-0.1-15.4-2.3-30.5-7.4-45.1c-1.9-5.4-4.2-10.3,0.5-15.8c1.7-2,0.9-6.4,0.7-9.7
                        c-0.3-3.6-1.3-7.2-1.7-10.8c-4.6-44.1-28-79.9-51.1-115.8c-3.2-4.9-9.3-4.2-12.1,1.1c-1.4,2.6-2,5.6-2.8,8.5
                        c-1.3,5.1-2.5,10.1-3.8,15.3c0.4-5.3,0.2-10.7,1.1-15.9c5.2-27.4,2.4-54.3-5.6-80.8c-2.8-9.4-6.4-18.3-15.9-24.2
                        c12.4-12,11-26.4,8.7-41.2c-4.2-26.3-14.6-50.3-26-74c-2.9-6.1-7.8-7.4-14.1-6.6c-14.3,2-28.5,1.9-42.7-1.1
                        C2545.3,797.4,2543.2,795.7,2541,794.5z M1798.8,1032.6c-1.2-3-2.4-6.1-3.7-9.1c-1.6-4-2.7-9.6-7.7-9.2c-3.1,0.2-6.9,3.9-8.8,7
                        c-10.3,16.7-20.5,33.6-29.9,50.8c-10.9,20.1-17.2,41.8-20.3,64.4c-1.1,7.9-2.2,16.1,4.3,22.3c-2.3,7.7-4.7,15-6.6,22.4
                        c-4.1,16.3-4.9,32.9-4.9,49.6c0.1,20.3,0,40.5,0,58.9c-1-16.1-2.2-34-3.4-51.9c-0.4-6.3-2.1-11.8-8.6-14.2
                        c-6.1,2.2-7.4,7.6-8.9,12.6c-3.4,10.8-6.3,21.7-10,32.4c-13.1,37.7-26.5,75.3-39.7,113c-1.4,4.1-2.9,8.2-3.8,12.4
                        c-1.4,6.5,1,9.5,7.5,10c2,0.2,4,0.1,6-0.3c13.2-2.2,25.9,0.2,38.2,4.8c8.1,3,16,6.6,24,9.9c13.4,5.5,15.7,4.6,22.8-8.3
                        c0.6-1.2,1.3-2.3,1.9-3.5c16.1-30.7,32.1-61.3,48.1-92c19.5-37.5,38.4-75.3,53.4-114.9c12.6-33.3,25.3-66.6,42.5-97.9
                        c6.4-11.7,5.4-24.6,4.1-37.2c-0.6-5.3-5.1-7.1-9.7-4.3c-2.5,1.5-4.6,3.8-6.6,6c-7.1,8.4-14.1,16.9-21.2,25.3
                        c-1.5,1.8-3.2,3.6-5.9,6.6c3.3-16.1,1-20.6-12.4-26.9c-1.8-0.9-3.7-1.5-5.5-2.5c-8.2-4.2-15.2-2.5-22,3.5
                        c-7.5,6.6-15.5,12.6-23.4,18.7c-1.5,1.2-3.4,1.8-5.2,2.6c2.8-5.2,6.1-9.4,9.8-13.2c7.6-7.8,10.2-16.8,7.5-27.5
                        c-0.9-3.5-0.7-7.2-1-10.9c3.8,5.4,9.1,8.5,15.5,9.9c4.5,1,9.1,2.2,13.4,3.9c11.5,4.4,23.4,5.3,35.4,3.9c7.5-0.8,15.7-1.6,22.1-5.1
                        c9.9-5.4,18.5-13.4,24.3-23.3c13.7-23.6,27-47.5,40.3-71.3c1.9-3.3,4.8-7.8,0.8-10.9c-4.4-3.4-7,1.5-9.8,4.1
                        c-6.9,6.3-13.6,12.8-20.7,18.7c-5.7,4.8-12,4.7-18.4,0.5c-11.3-7.3-18.6-17-19.1-30.9c-0.4-11.3-0.5-22.6-0.8-34
                        c-0.1-2.8-0.5-5.6-0.8-9.6c-9.5,4.4-17.9,8.2-26.3,12.1c16.4-14.9,32.3-29.5,43-48.8c-2.4,14.4-5.7,28.3-7.8,42.4
                        c-2.8,18.6-3.5,37.2,3.7,55.2c5.7,14.3,17.5,17.6,29.3,7.7c23.9-20.1,40.7-44.7,46.4-76c0.9-4.9,1.6-9.8,2.4-14.8
                        c3.4,26.9,7.7,53.4,19.1,78.3c-5,5.2-5,11-3.9,17.2c2.2,12.5,4,25,6.2,37.4c5,28.6,11.8,56.7,23.9,83.3c9.6,21,20.4,41.2,37.9,57
                        c10.9,9.8,15.2,8.5,18.5-5.4c0.3-1.3,0.7-2.6,1-3.9c7.1-38.9,18.8-76.2,36.6-111.6c1.4-2.7,1.8-5.9,3.1-10.3
                        c-5.3,1.6-8.8,2.5-12.2,3.6c-15.4,5.5-31.2,6.7-47.3,4.7c-17.7-2.2-33.1-15-38.6-32.8c11,11.5,23.1,19.5,36.8,25.1
                        c18.4,7.4,36.5,6.9,54-2.3c32.4-17,56.6-42.9,77.7-72.1c5.3-7.3,5.7-15.5,1.2-23.6c-3.1-5.5-6.1-11.1-9.7-16.3
                        c-25-36.5-47.3-74.9-73.4-110.7c-18.6-25.6-31.6-53.9-36-85.7c-0.3-2-0.8-3.9-1.6-5.8c-2.8-6.7-7.5-9-14.4-6.6c-2.5,0.8-5,2-7.2,3.5
                        c-36.8,26-60,60.8-67.3,105.6c-1.1,6.5-1.3,13.2-1.9,19.8c-1.4-7.8-1.3-15.6-1.5-23.5c-0.1-4.6-0.3-9.4-1.5-13.8
                        c-2.3-8.9-11.4-12.2-18.8-6.7c-3.7,2.7-6.8,6.4-9.4,10.2c-8.3,11.7-16.2,23.6-24.4,35.4c-1.3,1.9-3,3.7-4.4,5.5
                        c3.7-13.2,13.3-23.9,13.7-37.9c-2-0.9-3.4-1.5-5.7-2.5c24.6-10.3,43.3-26.7,60.6-45c18.6-19.6,37.6-38.8,60.1-54
                        c10.6-7.2,12.6-12.3,9.4-24.5c-6.4-24.1-20.4-42.3-42.5-54.2c-10.6-5.7-21.4-6.8-32.8-2.8c-23.1,8.1-44.7,18.9-64.6,33.2
                        c-31.4,22.6-52.3,51.8-58.5,90.6c-2.5,16.1-5,32.2-7,48.4c-1,8.7,0.9,10.8,9.4,12c5.3,0.8,10.6,1.2,15.9,1.3
                        c10.8,0.2,21.5,0.1,32,0.1c-16.8,4.3-34.3,5.7-51.9,3.1c-9.3-1.3-12.4-0.1-16.3,7.7c-10.6,21.6-19.8,43.7-24.5,67.4
                        c-3.3,16.5-7.1,33.7,9.1,47.2c-9.1,3.9-12.7,10.9-15.5,18.6C1795.2,970.1,1795,1001.2,1798.8,1032.6z M2023.6,1443.2
                        c-0.6-1.5-1.3-2.9-1.7-4.5c-3.4-13.4-8.4-25.9-17.3-36.7c-7.9-9.6-17.6-16.2-30.1-18c-12.3-1.8-18.4,2.1-21,14.2
                        c-2.3,10.4-3.9,21-5.3,31.5c-5.2,38.7-6.6,77.6-6.3,116.7c0.3,44.7,1.3,89.3,6,133.8c2.8,26.1,6.3,52.2,10.2,78.2
                        c6,39.8,12.3,79.6,19.3,119.3c5.4,30.6,8.8,61.3,6.6,92.4c-0.9,12.6-2.5,25.3-2.3,37.9c0.3,23.1-0.7,46.7,12.7,67.6
                        c-0.5,1.2-0.9,2.4-1.5,3.6c-20.6,39.1-34.7,80.7-45.2,123.5c-7.6,31-10.5,62.4-0.7,93.7c0.6,1.8,0.6,4,0.3,5.9
                        c-2.4,13.6,0.4,26.6,4.6,39.3c20.3,61.7,36.6,124.5,51,187.8c5,21.9,8.3,43.9,5,66.4c-1.8,12.5-3.6,25.1-5.2,37.6
                        c-1.1,8.2-3.3,16.6,3.5,23.3c-7.4,16.9-8.9,17.3-26.4,16.2c-14.9-1-30-1.6-44.9-0.5c-20.7,1.5-36.2,12.4-46.3,30.6
                        c-9.6,17.4-8.4,34.1,4.8,49c4.9,5.6,11.5,10.4,18.2,13.5c9.6,4.5,19.9,7.5,30.1,10.5c18.2,5.4,33.7,14.4,44.1,30.7
                        c5.7,9,13.6,14.2,23.4,17.5c18.4,6.1,37.2,7.2,56.3,5.8c14-1,22.5-8.3,26.4-21.8c5.8-20.2,7.1-40.6,5.5-61.3
                        c-0.7-8.6-1-17.3-0.3-25.9c1.8-20.6,4.2-41.1,6.4-61.6c1.7-16-0.6-31.2-7.6-45.7c-4.8-9.9-12.4-16.3-23.4-18.4
                        c-20.2-3.9-41.7,7.9-49.2,27c-2.3,5.8-5,11.4-7.5,17.1c-1.1-8.9,1.7-16.4,6.2-23c4.3-6.2,9.1-12.6,15-17.1
                        c5.4-4.1,12.3-6.7,18.9-8.4c14.7-3.9,26.9,3.9,40.5,11.6c-0.7-5.2-0.8-8.5-1.6-11.6c-1.4-5.8-3-11.6-5-17.3
                        c-7.2-21.3-8.7-42.9-4.2-65c7.5-36.9,14.7-73.8,21.9-110.7c2.3-11.5,5-22.9,11.2-32.9c12.9-20.7,18-43.4,18.2-67.6
                        c0.2-33.1-6.9-64.8-17.6-95.8c-9.3-27-14.7-54.6-13.4-83.3c0.7-15.3,1.3-30.6,1.6-46c0.1-5.6-0.1-11.4-1.2-16.8
                        c-1.2-5.7-4.8-7.5-10.5-6.1c-2.6,0.6-5.1,1.6-7.4,2.8c-17.9,8.9-30.4,22.4-35.9,41.9c-1.6,5.8-3,11.6-4.4,17.4
                        c-11.7,50.9-16.3,102.5-17.6,154.6c-0.7,27.5,1.7,54.6,11.7,80.6c6.4,16.7,16.3,30.4,31.7,40c1.7,1,2.9,2.7,4.4,4
                        c-4.3,0-7.6-1.3-10.6-3.1c-10.4-6.6-19.6-15.1-23.2-26.7c-4.7-14.7-13.1-20-28.1-18.4c-6.7,0.7-13.7-2.2-20.5-3.5
                        c-4.2-0.8-8.3-1.8-13.1-2.8c-0.3,3.9-0.6,6.8-0.8,10.1c-5.9-9.9-11.6-19.3-18.2-30.1c4.4,2,7.2,3.6,10.3,4.6
                        c7.2,2.3,14.5,5.2,21.9,6.3c14.7,2.1,23.7-4.6,26.8-18.9c0.8-3.5,1-7.2,1.3-10.9c2.9-35.2,5.2-70.4,8.7-105.6
                        c4.3-43.4,9.7-86.7,14.6-130c1-8.9-1-17.2-5.2-25c-7.5-13.9-14.2-14.3-23-1.2c-3.1,4.6-6.1,9.2-9.2,13.9c8.7-23,18.7-45.1,28.9-67.1
                        c17.6-38,26.7-78,29.2-119.6c1.3-21.9,1.3-44,2.8-65.9c3.3-50.9,7.3-101.7,10.6-152.6c1.6-24.6,1.7-49.3-1.4-73.9
                        c-1.5-11.7-5.4-14.7-16.6-12c-8.1,1.9-15.9,4.9-24,6.8c-5.5,1.3-11.1,2.4-16.7,2.3c-7.1,0-9.3-3.3-8.1-10.5c0.2-1.3,0.7-2.6,1.1-3.9
                        c8.7-36.2,13.7-72.7,7.9-109.8c0.8,1.8,2,3.5,2.4,5.3c3.7,19.2,3.2,38.4,1.3,57.7c-1.3,12.6-2.9,25.2-3.5,37.8
                        c-0.5,11.3,3.7,15.1,15.1,14.3c4.9-0.4,9.8-1.6,14.5-3.3c20.1-7,40.1-14.3,60.2-21.4c1.9-0.7,3.9-0.8,5.8-1.2
                        c-17.4,16.3-22.8,37-24.9,59c-2.2,22.5-4.8,45-6.6,67.6c-4.2,54.8-8.1,109.6-12.1,164.4c-0.9,12-1.3,24-2.4,35.9
                        c-3,31.2-6.2,62.3-9.4,93.4c-0.6,6.1,0,12.7-7.3,16c-1.5,0.7-2.1,3.3-3,5c-4.1,7.6-8.1,15.3-12.4,22.8c-5.1,8.8-5.3,17.5-1.3,26.7
                        c4.9,11.3,9.3,22.8,14.1,34.1c1.2,2.9,2.8,5.7,4.8,9.7c9.6-7.8,18.2-14.5,26.5-21.7c7-6,10.4-7.9,4.9-19.6c-0.2-0.3,0.5-1.1,0.8-1.7
                        c13.5,12.7,21.9,27.5,20.3,46.7c-0.5,6-0.1,12-0.1,18c0.1,13,0,26,0.2,39c0.1,3.7,0.5,7.9,4.8,9c4.2,1.1,6.8-2.1,8.5-5.5
                        c3.1-6.2,6.4-12.4,9-18.9c17.7-44,29.2-89.8,40.6-135.7c10.2-41.5,18.7-83.3,21.2-125.9c3.3-56.2,4.5-112.5,6.3-168.8
                        c1.5-46.7-1.9-93.2-4.3-139.8c-0.5-10.3-1.2-20.6-1.7-30.9c-0.2-4.9-1.1-9.3-5.9-11.8c-5.1-2.6-8.9,0.3-12.8,3
                        c-0.3,0.2-0.5,0.4-0.8,0.6c-11.4,9.5-24.7,15.6-38.4,20.8c-1.8,0.7-3.8,1-5.7,1.5c8.4-6.7,17.2-11.9,26.1-17.1
                        c21-12.3,38.8-28,52.1-48.7c15.5-24.3,15.4-34.9-1.9-57.8c-33.8-44.8-60.4-93.2-76.6-147.2c-1.3-4.5-5.2-8.2-8-12.2
                        c-1.8-2.6-4.8-4.8-5.7-7.7c-3-9.8-5.4-19.8-7.7-29.8c-3.6-15.1-12.8-26.1-24.7-35.3c-7-5.5-15.2-7.3-23.6-5.2
                        c-9.3,2.3-18.3,5.9-27.4,9c-1.8,0.6-3.6,1.4-5.4,2.1c-0.1-0.3-0.2-0.6-0.3-0.9c6.4-5.1,12.8-10.2,19.6-15.7c-3.5-4.3-6.1-7.8-8.8-11
                        c-6.9-8.1-13.6-16.4-20.9-24.2c-5.5-6-10.5-4.6-13.3,3c-0.5,1.2-0.8,2.5-1.1,3.8c-3.8,17.2-7.7,34.4-11.3,51.7
                        c-2.5,11.8-1,13.4,10.8,13.9c0.4,0,0.9,0.7,1.7,1.4c-1.5,2.4-3.3,4.8-4.6,7.4c-4.8,10.2-11,20.1-13.9,30.8
                        c-6.8,25.4-12,51.2-17.7,76.8c-0.7,3.2-0.7,6.7-0.3,9.9c0.9,6,4.7,7.9,10,4.7c2.8-1.7,5.2-4.2,7.2-6.8c3.4-4.5,6.4-9.3,9.3-14.2
                        c9.9-16.6,23.1-30,39.1-40.8c5.2-3.5,10.5-6.6,17.1-7.1c-1.4,2-3,3.4-4.8,4.6c-17,11.5-31.4,25.7-43.4,42.3
                        c-12.2,16.8-21.5,35.2-28.8,54.6c-3.5,9.2-2,13,6.3,18.5c3.6,2.4,7.4,4.5,11.1,6.7C2014.1,1398.4,2021.1,1419.6,2023.6,1443.2z
                        M2407,2148.3c-0.9-1.2-2.4-2.3-2.7-3.7c-1.6-7.5-2.7-15.1-4.4-22.5c-4.2-17.9-8.8-35.5-19.2-51.1c-6.6-9.9-15.3-16.5-26.4-20.3
                        c-11.8-4.1-16.2-1.1-16.8,11.2c-0.1,1.3-0.1,2.7,0,4c0.5,12.3,0.8,24.6,1.4,37c1.6,34.6-1.7,68.6-13.7,101.3
                        c-8.2,22.3-14,45.1-16.1,68.8c-3.2,35.1,1.1,68.4,20.7,98.8c3.3,5.1,4.9,11.6,6.2,17.7c6.8,32.2,13,64.6,19.9,96.8
                        c7.2,33.5,8.8,66.6-4.7,99.1c-1.9,4.6-1.6,10.1-2.6,17.4c18.7-12.5,36.2-18.2,55.1-7.2c15.7,9.1,28.4,30.7,27,47.9
                        c-2.8-6.6-5.5-11.6-7.1-16.9c-5.9-20.1-20-29.7-40.1-32c-16.8-1.9-29.3,5.4-35.4,21.2c-4.5,11.6-7.1,23.6-6.2,36.1
                        c1,13.6,2,27.2,3.4,40.8c2.3,22.2,4.2,44.4,1.8,66.8c-1.8,16.4,1,32.5,6.2,48.2c5.4,16.1,12,21.6,29.2,21.9c12.9,0.2,26-0.4,38.8-2
                        c18-2.2,32.7-10.8,42.5-26.6c6.6-10.7,16.2-17.4,28.1-21.2c6.7-2.1,13.6-3.6,20-6.2c10.4-4.3,21.3-8.2,30.7-14.2
                        c19.2-12.3,25.2-34.7,15.1-54.3c-10.2-19.6-26.2-32.1-48.8-33.3c-14.6-0.8-29.3-0.1-43.9,1c-14.9,1.1-21.4-2.4-26.5-16.9
                        c6.1-6.7,6.3-14.7,4-23.2c-0.8-2.9-1.1-5.9-1.6-8.8c-5.3-29.8-7.4-59.7-0.2-89.3c10.9-44.9,22.6-89.7,34.5-134.4
                        c4.9-18.6,12.4-36.7,16.4-55.5c4.9-22.7,7.3-46,10.7-69c3-20.6,2.6-41.2-2.4-61.5c-10.4-41.7-22.7-82.8-41.1-121.8
                        c-2-4.2-3.6-8.6-5.6-12.8c-1.5-3.2-1.9-6.2-0.1-9.5c6.5-11.7,8.6-24.6,8.9-37.6c0.4-19.6-0.5-39.3-0.7-58.9
                        c-0.2-18-2.5-36.2-0.2-53.9c5.4-40.2,13.2-80.1,19.8-120.2c9.1-55.2,17-110.6,19.7-166.6c3.1-64.3,4.5-128.6-2.6-192.7
                        c-1.5-13.9-3.7-27.7-6.2-41.5c-2.2-12.2-8.2-16.2-20.5-14.8c-2.3,0.3-4.6,0.9-6.8,1.5c-15.1,4.4-24.5,15.4-32.2,28.3
                        c-1.7,2.9-3.3,5.9-5,8.9c2.6-16.9,11.7-29.3,26-38c3.7-2.3,7.6-4.1,11.2-6.6c7.8-5.3,9.3-9.1,5.9-17.8c-15-39.4-37.2-73.6-72.8-97.7
                        c-1.6-1.1-2.9-2.5-4.4-3.7c2.2-0.6,3.8-0.2,5.2,0.4c19,8,33.5,21.5,45,38.4c4.5,6.6,8.6,13.5,13.3,20c2.5,3.4,5.6,6.7,9.1,9
                        c5.8,3.9,9.6,1.9,10.2-4.9c0.3-3.3,0.3-6.7-0.4-9.9c-5.8-25.6-11.7-51.3-17.9-76.8c-3.3-13.4-9.4-25.6-17.9-36.5
                        c-1.7-2.2-3.4-4.3-5.1-6.5c1.3-0.1,1.9,0.2,2.3,0.7c3.3,3.1,6.9,5.8,11.5,3c4.5-2.6,4.1-7.2,3.5-11.6c-0.1-1-0.3-2-0.6-2.9
                        c-3.7-16.9-7.4-33.8-11.2-50.7c-0.4-1.9-0.8-3.9-1.6-5.7c-2.5-5.6-6.9-7.1-10.9-2.7c-10.9,11.9-21.2,24.4-32,36.9
                        c5.7,4.5,10.2,8,14.7,11.6c-9-1.2-17.2-3.8-25.4-5.7c-6.5-1.5-13.3-2.1-19.6,1.2c-11.4,6.1-21.3,14.1-26.2,26.4
                        c-5.2,13.3-10.9,26.5-12.3,41c-0.2,2.6-1.8,6-3.8,7.3c-7.1,4.5-9.9,11.3-12.1,18.9c-11.4,39.4-29.2,75.7-51.9,109.8
                        c-8.7,13-18.1,25.5-27.2,38.3c-10.2,14.2-11.3,29.2-1.8,43.9c6.8,10.6,14.4,20.8,22.8,30.2c13.5,15,30.4,25.8,47.9,35.7
                        c1.4,0.8,3,1.5,4.4,2.5c1.3,0.9,2.5,1.9,3.8,2.9c-14.3-3.5-27.1-9.5-38.5-18.3c-4.4-3.4-8.4-8.1-15-5.2c-6.7,2.9-6,9.1-6.3,14.6
                        c-1.7,26.6-3.3,53.2-4.6,79.8c-1,21-2.6,42-2.1,62.9c1.5,61,3.8,121.9,6.2,182.8c0.7,16.3,2.3,32.6,4.7,48.7
                        c7.7,51.4,20.6,101.7,33.9,151.9c7.8,29.4,18.9,57.5,30.1,85.6c2,5,4.3,12.3,10.9,11.3c7.3-1.1,5.5-8.7,5.5-14.1
                        c0.2-19.7-0.1-39.3,0.5-59c0.2-6.8,1.3-14.4,4.6-20.1c5.6-9.7,13.2-18.3,19.9-27.4c0.1,3.5-1.4,6-2.8,8.5
                        c-5.8,10.1-5.5,12.2,3.1,19.5c6.6,5.6,13.3,11.1,20.1,16.5c2,1.5,4.4,2.5,7.7,4.3c7.1-16.7,14.4-32.3,20.3-48.5
                        c1.7-4.8,1-11.7-1.3-16.4c-5.9-12.2-11-25-21.2-34.7c-1.5-1.4-2.3-4-2.6-6.1c-3.5-32.8-7.2-65.6-10-98.4c-2-23.5-2-47.3-4-70.8
                        c-5.8-69-11.9-138.1-18.4-207c-1.6-17.6-8.3-33.5-20.7-46.6c-1.4-1.5-2.8-3-4.2-4.6c9.6,1.9,18.3,5.2,27,8.3
                        c15.7,5.5,31.2,11.7,47.2,16.1c18.5,5,24.8-1.2,22.6-20.4c-1.6-13.6-3.2-27.1-4.9-40.7c-2.3-18.9-0.4-40.3,5.1-53.8
                        c0,2,0.2,3.3,0,4.6c-4,25.6-3.1,51.1,1.9,76.5c2.2,11.4,4.9,22.8,6.8,34.3c1.2,7.4-1,10-8.5,9.9c-5.3-0.1-10.6-1.1-15.7-2.3
                        c-7.7-1.9-15.3-4.7-23-6.7c-12.2-3.1-16.4-0.7-17.7,11.6c-1.6,15.2-3.1,30.6-2.4,45.9c2,44.6,5.1,89.1,7.7,133.7
                        c1.9,31.6,4,63.2,5.3,94.8c1.8,42.9,7.8,84.9,24.6,124.8c10,23.6,21.1,46.7,31.6,70.1c1.3,2.9,2.4,5.9,3.6,8.8
                        c-4.7-4.6-8-9.9-11.6-14.9c-7.5-10.3-13.8-9.8-20.1,1.4c-5.6,9.9-8.1,20.6-6.9,31.7C2400.2,2093.6,2403.7,2121,2407,2148.3
                        L2407,2148.3z M2212.6,976.3c-2.7-6.4-5.1-12.9-8.1-19.1c-4.1-8.3-9.2-9.8-16.9-4.5c-4.3,3-8.2,6.8-11.7,10.7
                        c-14.9,16.6-29.9,33.2-44.3,50.2c-7.7,9.1-14.8,18.9-21,29.1c-19.1,31.8-27.9,66.7-29.4,103.6c-0.3,8.3,0.5,9.9,7.3,12.1
                        c13,4.3,22.2,13,29.9,23.9c10.4,14.9,17.7,31.3,23.7,48.3c10.1,28.6,20.1,57.1,30.1,85.7c7.7,22.2,18.5,42.7,32.4,61.6
                        c1.6,2.1,3.1,4.4,5.1,6.2c5.3,4.9,10.1,3.5,11.5-3.4c0.6-3.2,0.5-6.6,0.5-10c-0.2-59.3-0.4-118.6-0.7-178
                        c-0.3-56.3-0.5-112.6-1.1-169c-0.1-11.9-4.1-24.1-2.5-35.7c1.6-12.1,3.1-24.3,4.1-36.5c0.5-6,0.4-12,0.3-18
                        c-0.2-19.3-0.5-38.6-0.8-58c-0.6-53.3-1.1-106.6-1.6-159.9c-0.4-33.5-8.5-65.2-22.2-95.6c-9.7-21.4-26.2-32.9-49.8-34.1
                        c-5.5-0.3-11-1.5-16.5-2.3c29.5-5.3,54.5,6.2,69.7,31c9.3,15.2,15.4,31.7,17.9,49.4c0.8,5.8,2.1,11.5,3.1,17.3
                        c2.7-1.8,3.9-3.4,4.4-5.2c2.1-7.7,4.1-15.4,5.9-23.2c4-17.1,10.7-33,21.7-46.8c16.3-20.6,38.4-26,63.3-23.2
                        c-5.3,1.5-10.8,2.6-16.4,3c-22.7,1.3-39.1,11.9-48.7,32.6c-3.3,7.2-6.1,14.7-9.2,22.1c-3.4,8.1-8.3,16-9.7,24.5
                        c-3.2,19.3-5.8,38.9-6.5,58.5c-1.5,45-2,89.9-2.2,134.9c-0.2,36,0.6,72,1.2,107.9c0,3.3-0.7,7.1,3,10.9c1.4-3.3,2.9-5.5,3.4-7.9
                        c4.8-26.1,16.5-49.1,31.6-70.5c25.5-36.2,51.2-72.3,76.8-108.5c17-24,28.2-50.4,34.4-79.1c3.5-16.3,7.8-32.4,11.9-48.5
                        c6-23.1,18.1-41.3,40.7-51.2c3.5-1.5,6.2-3.8,6.3-9.2c-23.7-8.7-48-15-73.6-18.9c3.8-1.6,6.8-2.9,11.2-4.8c-3.8-2.8-6.1-4.6-8.6-6.1
                        c-11.1-6.7-22.2-13.4-33.5-20c-12.3-7.2-19.9-17.6-21.7-31.8c-1.3-10.6-2.1-21.2-3.5-31.8c-0.9-6.4,0.9-10.8,7-13.8
                        c8.1-4,12.1-11.1,13.4-19.9c0.8-5.3,1.3-10.6,1.9-15.9c1-9.6,2.1-19.2,3-28.8c0.5-5.4,2.8-8.7,8.6-8.9c5.8-0.2,10.4-2.9,12.9-8
                        c7-14.4,13.9-28.8,20.3-43.4c3.2-7.4,1.4-14.7-5-19.8c-4.1-3.3-8.6-6.4-13.5-8.3c-6.7-2.5-8.6-6.4-8.9-13.5
                        c-0.5-16.3-1.1-32.7-3.7-48.7c-7.2-44.3-43.8-92.5-105.4-97.8c-53.6-4.6-101.4,25.9-120.1,77.4c-8.2,22.5-11.5,45.8-9.6,69.7
                        c0.8,9.8,0.3,10.4-9.1,13.4c-0.6,0.2-1.3,0.4-1.9,0.6c-15.4,5-20.8,16.2-14.4,31.1c5.1,11.9,11,23.5,16.4,35.3
                        c3.1,6.9,7.6,11.6,15.6,12c4.7,0.2,7.3,2.7,7.8,7.4c0.3,3.3,0.8,6.6,1.2,9.9c1.2,11.6,2.4,23.2,3.7,34.8c1.1,9.5,5.3,17.2,14.2,21.5
                        c5.8,2.8,7.2,7.1,6.5,13c-1.2,9.9-2,19.9-3.1,29.8c-1.7,15.8-9.7,27.2-23.5,35c-10.1,5.7-20.1,11.8-30,17.9c-3.3,2-6.3,4.5-10.1,7.2
                        c2.7,1.7,4.5,2.8,6.3,3.9c-21.8,5.6-43.3,10.9-64.7,16.7c-2.8,0.8-6.5,3-7.4,5.4c-1.4,3.9,2.7,5.6,5.8,6.8
                        c19.6,7.4,31.7,21.8,37.6,41.3c6.2,20.4,12,40.9,16.6,61.6c5.5,24.8,15.1,47.3,29.8,68c26,36.7,51.3,73.8,77,110.8
                        c10.5,15.1,19.8,30.8,26.2,48.1C2209,952.7,2212.4,964,2212.6,976.3z M1654.3,1583.9c0.8,0.3,1.6,0.5,2.4,0.8
                        c-1.3,3.3-2.4,6.6-3.9,9.8c-3.6,7.9-7.3,15.7-11.1,23.5c-1.8,3.8-1.6,7.1,1.7,9.9c3.4,2.9,7.4,3.3,11,0.7c2.7-1.9,5-4.3,7.1-6.9
                        c2.5-3.1,4.7-6.5,6.7-10c13.2-23.1,26.7-46.1,39.2-69.6c5.7-10.8,9.6-22.6,14-34.1c1.5-4,2.5-8.4,2.7-12.6c0.8-19,1.1-38,1.6-56.9
                        c0.2-7.6-3.3-12.6-10-15.7c-5.8-2.6-11.5-5.3-17.2-8c-32.1-15.1-63-12.3-91.2,8.8c-11.3,8.4-21.3,19.3-30,30.5
                        c-13.7,17.6-29.8,31.2-50.4,39.7c-3.1,1.3-6.1,2.8-8.8,4.7c-4.6,3.3-5.3,6.2-2.3,11.1c5.2,8.6,12.4,13.9,23.1,12.5
                        c5.9-0.8,11.8-1.9,17.7-2.9c12.2-2.1,24.1-5.1,34.5-12.4c1.2-0.8,3-0.7,4.6-1c2.9,4.4-0.1,7.4-1.7,10.5
                        c-10.2,19.5-20.7,38.8-30.7,58.3c-3.9,7.7-7.4,15.7-10.3,23.8c-1.3,3.5-1.6,8-0.6,11.6c2.1,8.1,10.5,10.1,16.2,4
                        c2.9-3.1,5-7,7.3-10.7c3-4.8,5.8-9.8,8.6-14.7c0.7,2.9-0.1,4.9-1.1,6.9c-5.2,10.1-10.5,20.1-15.5,30.2c-1.7,3.5-3.2,7.4-3.7,11.2
                        c-0.9,7.5,2.5,13.5,8,15.7c6.3,2.5,11-0.8,15.1-4.9c3.4-3.4,6.5-7.2,10.5-11.7c0,3,0,4.6,0,6.2c0.2,9,5.6,13.3,14.4,11.4
                        c6.9-1.5,12.1-5.5,15.3-11.8c3-5.9,5.8-12,8.6-18C1642.1,1610.4,1648.2,1597.2,1654.3,1583.9z"/>
                    <path class="st0" d="M2222.2,1628.1c-0.6-3.5-1.5-7-1.7-10.5c-1.6-28.2-3.1-56.4-4.7-84.7c-0.7-12.2-0.1-24.5-3.7-36.6
                        c-2.6-8.8,4.3-13.8,10.2-18.6c9.4,6.3,11.9,13.6,9.5,24.9c-3,13.8-2.9,28.4-3.7,42.6c-1.7,27.5-3.1,55.1-4.7,82.6
                        C2223,1628,2222.6,1628.1,2222.2,1628.1z"/>
                    <path class="st0" d="M508.2,951.5c-1.3,3-2.7,5.9-3.9,8.9c-9.7,25.6-21.5,50.2-36.9,72.9c-3.8,5.6-5.2,11.2-3.6,17.9
                        c4.9,20.6,1.2,40.1-7.1,59.4c-4.6,10.6-8.4,21.8-11,33.1c-9,39.1-24.3,76-38.3,113.4c-21.6,57.8-49.7,112.6-76.3,168.2
                        c-3.5,7.2-6.8,14.5-10.5,21.6c-5.3,10.2-6.3,20.9-3.9,32c4.3,20.2,0.9,39-10.6,56.3c-3.5,5.3-7.2,10.4-11.1,15.4
                        c-14.4,18.8-27.4,38.5-39,59.2c-5.2,9.2-12,17.6-18.4,26.1c-4,5.3-9.7,8.3-16.4,8c-7-0.4-10.9,3-14.4,8.6
                        c-10.4,16.6-19.2,19.8-38.8,15.6c-5.1-1.1-10.6-1.2-15.7-0.6c-17.8,2-29.7-7.8-30.3-25.9c-0.2-6.6-1.4-12.7-6.1-17.5
                        c-9-9.1-8.6-19.7-4.8-30.7c1.5-4.4,3.8-8.5,6.1-12.6c8.5-15.1,17.3-30,25.9-45.1c1.8-3.1,3.3-6.4,6.1-11.6
                        c-7.2,1.3-12.5,2.1-17.8,3.1c-16.6,3.2-30.3-1.6-40.7-15c-9.4-12-6.8-25.3,6.4-33c2.9-1.7,5.8-3.5,8.9-4.4
                        c11.1-3.1,19.3-10.1,27.1-18.1c15.8-16.2,31.8-32.3,47.9-48.2c4.2-4.2,9.5-7.2,13.9-11.2c3.9-3.5,8.8-6.9,10.7-11.4
                        c16.7-38.1,34.8-75.8,43.7-116.7c5.7-26,9.7-52.4,12.5-78.9c5.7-52.5,19.8-101.9,48.8-146.4c4.9-7.5,10.3-14.8,16.1-21.6
                        c4.4-5.2,5.1-9.9,3.1-16.4c-3.9-12.7-7.9-25.5-9.6-38.6c-4.6-33.5,3.7-64.8,19.3-94.4c4.3-8.2,9.3-16.2,14.2-24.1
                        c8-12.7,12.4-26.1,12.6-41.4c0.4-22,1.9-43.9,3.5-65.9c1.7-23.5,10.1-44.7,24.1-63.6c13.2-17.8,28.7-33.3,46.8-46.1
                        c16.9-11.9,35.6-19.2,56.1-22.3c21.5-3.2,42.1-10,61.9-18.9c25.8-11.6,51.5-23.6,77.3-35c8.1-3.6,11.8-9,12-17.7
                        c0.3-13,1-25.9,5.1-38.5c0.8-2.5-0.6-6.8-2.6-8.9c-7.6-8-10.5-17.5-11.2-28.1c-0.6-8.6-1.4-17.3-2.1-25.9
                        c-0.5-6.2-2.8-10.7-9.2-12.8c-6.1-1.9-10-6.5-12.6-12.1c-6.5-13.8-13.4-27.6-19.4-41.6c-7.4-17.4-3.1-30.9,13-41.2
                        c8.1-5.2,11.7-12.1,12.1-21.5c0.6-11.6,0.9-23.4,3.2-34.8c7-35.1,25.4-63.5,53.7-85.1c18.2-13.9,39.2-20.9,62.1-22.1
                        c17.4-0.9,34.7,0,51.4,5.2c46.4,14.6,73,48,86.4,93.2c4,13.5,5,27.5,4.2,41.6c-0.6,10.3,3,17.7,11.7,23.6
                        c16.7,11.4,20.5,23.4,13.6,42.4c-4.3,11.9-9.6,23.4-14.8,34.9c-3.6,8.2-9.3,14.7-17.9,17.9c-5.2,2-6.8,5.7-7.2,10.8
                        c-0.7,10-1.7,19.9-2.8,29.8c-1.1,9.5-4.5,18.1-11.2,25.1c-3.5,3.6-3,6.9-2,11.6c2.7,12.3,4.6,24.9,5.3,37.5
                        c0.5,9.7,4.3,15,13.1,18.8c19.6,8.4,38.9,17.3,58.2,26.5c24.8,11.9,50.4,20.6,77.7,24.8c34.2,5.3,62.7,21.8,85.7,47.4
                        c9.5,10.6,18.1,22.2,26.2,34c10.3,14.9,14.9,32,16.3,49.8c1.8,23.6,2.9,47.2,3.3,70.9c0.2,13.2,3.3,24.5,11.4,35.2
                        c27.5,36.4,37.8,78.1,35.2,123.2c-0.8,13.4-4.3,26.3-10.5,38.3c-2.3,4.3-2.4,7.9,1.2,11.8c21.9,23.6,35.6,52.1,47.3,81.6
                        c12.1,30.3,17,62.2,20.1,94.4c6.2,64,23,124.9,53.9,181.6c0.2,0.3,0.4,0.6,0.4,0.9c1.4,12.9,12.5,17.7,20.6,25.2
                        c16.6,15.4,32.8,31.2,48.4,47.6c10,10.5,20,20.5,34.3,24.8c1.6,0.5,3,1.3,4.5,2.1c16.3,8.5,19.1,24.8,6,37.7
                        c-10.9,10.8-23.9,15.2-39.3,11c-4.6-1.2-9.4-1.5-16.4-2.6c2.9,5.7,4.4,9.2,6.4,12.6c7.9,13.9,16.2,27.6,23.9,41.6
                        c3.4,6.1,6.3,12.5,8.6,19.1c3.2,9.4,2.1,18.5-5.2,26c-4.5,4.6-6.5,9.9-6.6,16.2c0,2.3-0.3,4.7-0.7,6.9c-2.5,13.7-10.9,21.5-24.8,21
                        c-9.4-0.4-18.5,0.3-27.7,2.1c-10.6,2.1-19.7-1.6-26.6-10.1c-2.1-2.6-3.8-5.5-5.7-8.2c-3.3-4.5-7.3-7.8-13.3-7.6
                        c-8.1,0.3-14.1-3.4-18.4-9.9c-7.2-10.8-14.5-21.6-21.7-32.3c-10.5-15.8-21-31.6-31.6-47.3c-4.8-7.2-10.3-13.9-15-21.2
                        c-10.2-16.2-13.8-34-9.4-52.7c3.1-13.2,0.6-24.8-5-36.7c-24.3-51.8-49.3-103.4-72.2-155.9c-15.8-36.3-28.8-73.8-42.4-110.9
                        c-4-10.9-5.6-22.6-9.1-33.7c-4-13-8.6-25.9-13.5-38.6c-6.6-17.3-8.9-34.7-4.6-52.9c1.3-5.7,1.3-11-2.2-16.2
                        c-16.2-23.4-28.2-49-38.5-75.4c-0.8-2.1-2-4-4.8-5.9c-0.7,5.4-1.5,10.7-2,16.1c-3.5,37.5-6.8,75-10.4,112.4
                        c-1.2,12.1-5,23.5-12.9,33c-5.2,6.3-6,12.8-3.4,20.5c10.5,31.6,14.9,64.1,14.2,97.4c-0.1,4.6-0.5,9.3-1.4,13.9
                        c-1.9,9.1-1.2,18,2.5,26.4c27.9,63.8,45,130.9,58,199c5.3,27.4,7.7,55.4,11.3,83.1c2.2,16.8,4.3,33.7,6.4,50.6
                        c2.6,20.9,1.8,41.9,0.4,62.9c-3.6,55.6-18,108.9-33.7,161.9c-8.8,29.7-17.1,59.5-26,89.1c-3.8,12.6-5.7,25.2-5.8,38.3
                        c-0.4,36.7-0.9,73.3-1.7,110c-0.3,12.3,2.1,23.5,7.2,34.8c21.2,47.1,31.1,96.8,33.3,148.2c0.7,18,1.8,36,1.7,53.9
                        c-0.2,30.4-4.6,60.4-9.8,90.3c-5.5,31.8-10.2,63.8-19.6,94.8c-6.1,20.1-12.6,40-22.4,58.7c-2.8,5.4-3,10.4-0.1,16.1
                        c2.2,4.3,3.3,9.4,4,14.3c4,26.7,7.9,53.4,11.6,80.1c1,7,3.8,12.5,8.6,17.5c5.8,6,11.2,12.3,16.5,18.8c14.1,17.1,30.2,31.9,48.8,44
                        c19,12.3,35,27.7,47.9,46.2c4.9,7,8.5,15.1,11.3,23.1c3.3,9.4,0.3,18.6-8.5,23.4c-32.7,17.8-66.7,30.7-105,23.2
                        c-13.6-2.6-26-8.4-37-16.9c-19.6-15.1-40.3-28.3-63.7-36.8c-12.6-4.6-19.1-14.9-22.7-27.4c-8.1-28.7-12.1-58-13.9-87.6
                        c-0.3-4.3-0.4-8.7-1.2-12.9c-4.5-24.3-3.3-48.4,1.9-72.4c2.2-10.1,4.5-20.1,7-30.1c7.9-32.2,6.7-64.1-3.2-95.6
                        c-9.5-30.2-19.8-60.1-29.4-90.2c-6.8-21.3-11.4-43.1-13.3-65.4c-2.1-24.8,1-49.2,5.1-73.6c4.2-25.3,8.4-50.6,12-75.9
                        c1.6-11.2,3.2-22.6,2.3-33.8c-0.8-9.7-4.1-19.5-7.8-28.6c-9.4-22.8-17.5-46-23.6-69.9c-10.9-42.8-15.7-86.4-16.5-130.5
                        c-0.6-32.7,0.8-65.3,5.4-97.7c1.1-7.6,0.5-14.4-4.6-20.7c-2.1-2.7-3-6.6-3.9-10.1c-14.2-55.9-25.3-112.3-32.8-169.5
                        c-3.9-30.1-7.5-60.2-7.6-90.6c0-11,1.1-21.9,1.7-32.9c0.3-5.3,0.7-10.5,1.1-16.5c-11.2,0-21.6,0-33,0c0,2.9-0.3,5.5,0,8
                        c4.6,32,0.4,63.8-2.6,95.7c-6.5,69.5-21.1,137.5-37.1,205.2c-1,4.1-2.2,8.6-4.6,12c-4.1,5.6-4.8,11.3-4,17.9
                        c4.4,35.4,6.9,71,5.7,106.7c-2.3,69.6-15.6,136.8-43.3,200.9c-3.5,8.1-4.8,16.5-4.5,25.3c0.9,33.1,5.9,65.7,12.9,97.9
                        c11.4,52.2,7.2,103.2-9.5,153.6c-7.9,23.7-15.4,47.5-23.7,71c-12.7,35.9-14.4,72.1-6.2,109.2c4.1,18.5,7.4,37.2,10.7,55.9
                        c1,5.5,1.3,11.3,0.9,16.8c-1.8,21.2-4.7,42.4-5.9,63.6c-1.3,22.5-6.1,44.1-12.5,65.5c-4.1,13.5-12.8,22.3-24.9,28.3
                        c-3,1.5-6.1,2.7-9.2,3.8c-14.4,4.9-27.2,12.4-38.8,22.1c-12,10.2-25.1,18.7-40.2,23.7c-19.5,6.5-39.4,7.1-59,1.3
                        c-15.9-4.7-31.6-10.5-46.9-16.9c-18.4-7.7-23-20.8-14-38.9c8.1-16.4,20.5-29.6,33.9-41.6c7.2-6.4,14.9-12.3,22.9-17.7
                        c18.9-12.8,35.5-28.1,49.9-45.8c4-4.9,8.2-9.6,12.7-14.1c6.7-6.7,10-14.9,11.3-24c3.6-24,6.8-48.1,10.5-72.2c1-6.5,2.8-13,5-19.2
                        c1.8-5.1,1.8-9.5-0.6-14.3c-22.1-44.7-32.7-92.7-40.3-141.6c-3.8-24.7-7.1-49.4-10-74.2c-3.6-30.3-2.2-60.6,0.1-90.8
                        c3.7-48.8,13.4-96.2,33.8-141.1c5.3-11.8,7.7-24,7.1-36.9c-0.5-11-0.5-22-0.7-33c-0.5-26.3-0.6-52.7-1.6-79
                        c-0.3-8.9-1.8-18-4.2-26.5c-13-44.4-27.1-88.5-39.6-133c-6-21.4-10-43.4-13.9-65.4c-4.7-26.9-9.9-53.9-9.1-81.3
                        c0.6-19.9,2.1-39.9,4.1-59.8c3.2-30.8,6.9-61.6,11-92.3c5.1-38.7,15.4-76.3,25.6-113.9c10.2-37.3,21.4-74.2,35.6-110.1
                        c2.4-6.1,3.1-12,2-18.5c-5.7-31.5-0.4-62.4,6.7-93c2.1-9.1,4.4-18.1,6.8-27.1c1.4-5.5,0.8-10.2-3.2-14.8
                        c-8.9-10.5-13-23.3-14.3-36.7c-3.8-39.1-7-78.3-10.4-117.5c-0.3-3.9-0.9-7.8-1.4-11.7C509.7,951.8,508.9,951.7,508.2,951.5z
                        M772.1,832.2c2.5,2.5,5.1,4.9,7.4,7.6c11.1,13.5,24.7,23.3,41.8,27.6c14.3,3.6,28.9,5,43.6,3.2c6.5-0.8,12.9-1.7,19.4-2.6
                        c-4.6,3-9.6,4.6-14.5,6.3c-9.6,3.5-12.4,8-11.5,18.3c0.1,1.7,0.5,3.3,0.8,4.9c6.3,31,12.5,62,19.1,92.9c2.8,13.2,8.6,25.2,17.6,35.4
                        c7.3,8.2,14.7,16.4,22.3,24.3c14.6,15,26.4,31.7,32.4,52c0.9,3,2.6,5.8,4.3,9.2c5.9-4.3,9.2-9.3,11-15c1.9-6,3.2-12.2,4-18.5
                        c1.8-14.5,2.6-29.2,4.7-43.7c7.3-51.4,7.1-103,3-154.6c-0.8-9.6-1.5-19.3-2.9-28.8c-1.5-10.1-4.7-12.1-14.3-9.2
                        c-7.6,2.3-15,5.4-22.6,8c-6.6,2.3-13.2,4.4-19.7,6.6c0,0,0.2,0.2,0.2,0.2c2.5-1.9,4.7-4.2,7.4-5.6c20-10.9,37.9-24.6,54.7-39.9
                        c7.8-7.1,9.9-15.6,6.3-25.3c-3.2-8.4-6.2-16.9-10.6-24.7c-16.4-29.3-32.2-58.9-44.3-90.3c-6.4-16.5-18-28.6-34.9-35
                        c-6.7-2.5-13.5-3.6-20.4-1.2c-27.3,9.6-54.6,19-81.7,29c-19.2,7.1-29.8,21.6-32.9,41.6c-4,25.7-5.1,51.6-4.2,77.6
                        c0.5,15.6,4.8,30.1,12.4,43.7c1.1,2,1.7,4.2,2.5,6.3c-4.4-5.8-8.7-11.6-13.2-17.3c-5.7-7.1-9.8-7.4-15.1-0.3
                        c-6.2,8.2-11.6,17-17.2,25.6c-2.7,4.2-3.1,8.9-0.3,13.2c5.8,8.9,11.5,17.9,17.9,26.4c4.7,6.2,10,6.2,14.8,0
                        c6.4-8.4,12.3-17.4,18.1-26.2c2.6-4,2.2-8-0.4-12.1C775.1,838.8,773.7,835.4,772.1,832.2z M900,2643.9c-0.7,1.8-1.3,3.7-2.3,5.3
                        c-5.7,9-8.2,18.8-7.8,29.4c1.2,29,4.3,57.7,11.3,86c4,16,13.1,26.5,28.9,31.3c14.5,4.5,27.4,12,39.3,21.4c5.5,4.3,10.7,9,16.6,12.9
                        c18.5,12.2,38.3,19.6,61.2,15.7c21.9-3.7,42.5-10.9,62.3-20.7c7.8-3.9,9.5-8.1,6.3-16.4c-2.4-6.1-5.7-12.1-9.6-17.4
                        c-11.9-16.2-26.4-30-43.3-41c-19.1-12.5-35.9-27.6-50.5-45.1c-6.2-7.4-12.7-14.5-19.3-21.6c-4.6-5-8-10.5-9-17.4
                        c-4-28-8.1-56-12.2-84c-0.3-2.3-0.5-4.8-1.5-6.8c-1.2-2.5-3-6-5.1-6.5c-2-0.5-4.8,2.3-7.1,4c-1,0.7-1.4,2.2-2.1,3.3
                        c-7.9,11.2-15.1,22.9-23.8,33.3c-10.2,12.2-21.7,23.2-32.6,34.7c1.5-2.5,2.8-5.2,4.6-7.5c8-10.2,16-20.5,24.4-30.4
                        c31-36.5,51-78.4,63.7-124.3c10.4-37.7,17-76,21.5-114.7c2.8-24.1,5.4-48.4,5.8-72.6c0.4-21.6-1.7-43.2-3.6-64.8
                        c-3.9-44.8-14.6-87.9-33.5-128.9c-10.2-22.2-22.4-43.1-36.6-62.9c-2.7-3.8-5.2-7.9-11.5-6.4c0,2.4-0.3,4.7,0,6.9
                        c1.5,10.2,3.1,20.4,4.8,30.6c5.9,35.2,10.1,70.6,8.7,106.4c-0.6,16-1.5,31.9-2.2,47.9c-2.6,62.2-5.2,124.5-7.9,186.7
                        c-0.7,15.3-1.5,30.5-2.3,45.8c-2.1-12.3-2.6-24.6-3.7-36.9c-2.3-24.9-3.7-49.9-7.7-74.5c-4.9-29.9-11.6-59.4-18.3-89
                        c-8.7-38.3-18.1-76.4-27.2-114.6c-0.9-3.6-0.8-7.9-5.4-10.1c-0.6,1.2-1.3,2-1.4,2.8c-0.4,3-0.7,6-0.8,8.9
                        c-1.1,29.4-5,58.3-10.6,87.2c-3.5,18.3-6.5,36.8-8.3,55.3c-3.3,33.7,2.7,66.4,12.9,98.4c9.6,30.1,20,60,29.4,90.2
                        c10.7,34.5,11.1,69.3,2.4,104.5c-4.5,18.4-7.4,37.2-10.8,55.9c-1.1,6.1-1.7,12.4,3.2,17.8C893.2,2649,896.6,2646.5,900,2643.9z
                        M608.1,2645.9c3.5,1.8,7,3.6,11.6,6c0-7.2,0.7-12.6-0.1-17.7c-3.3-19.7-6.6-39.4-10.9-58.8c-7.4-33.5-7.8-66.7,2.4-99.6
                        c9-28.9,18.6-57.6,28-86.3c16-48.8,19.9-98.2,8.8-148.6c-7.2-32.6-13.1-65.3-12.5-98.8c0.1-3.3-1.3-6.6-2-9.9
                        c-0.8,0-1.6-0.1-2.3-0.1c-5.6,8.9-5.9,20-10.8,29.3c0.5-5.3,1.6-10.4,2.7-15.5c5.8-25.4,10.4-50.9,8.4-77.1
                        c-1.1-14.6-1.7-29.3-3.8-43.8c-2.4-16.8-6.2-33.3-9.4-50c-0.8-4.4-3-7.5-7.7-7.9c-4.3-0.3-6.7,2.4-8.3,5.9c-3,6.7-6.5,13.2-8.7,20.1
                        c-10.4,32.6-20.9,65.2-27.6,98.9c-0.3,1.6-2,2.9-3,4.3l0,0c4-21.3,6.9-42.7,9-64.7c-8,1.5-8,1.5-11.7,6.5
                        c-14,19.5-26.1,40.2-36.2,62c-22.6,48.7-33.4,100.1-36.1,153.5c-1.6,32.7-2.1,65.3,2.3,97.7c3.4,25.1,7.7,50,11.5,75
                        c3.9,25.4,9,50.4,17.5,74.7c17.9,50.7,46.7,94.7,81.7,135c2.8,3.2,5,6.8,7.5,10.3c-1.1-0.6-2.4-0.9-3.2-1.7
                        c-7.3-7.7-14.6-15.5-21.9-23.3c-12-13-23.4-26.4-31.8-42.1c-2.5-4.6-4.6-10.9-11.1-9.3c-5.9,1.4-5.3,7.8-6,12.6
                        c-4.1,27.3-8.2,54.6-12,82c-1.1,8-4.6,14.4-9.9,20.2c-6.5,7.1-13.1,14.2-19.2,21.6c-14.1,17.1-30.4,31.7-49,43.7
                        c-18.1,11.7-33.1,26.7-46,43.9c-3,3.9-5.4,8.4-7.3,13c-4.2,10.2-2.1,15.7,8.2,19.7c15.5,6,31.1,11.9,47,16.7
                        c14.2,4.2,28.9,4.2,43.5,1.2c12.7-2.6,23.7-8.6,33.7-16.5c18.9-14.9,38.7-28.2,62-35.6c6-1.9,10.4-6,13.2-11.5
                        c2.2-4.4,4.1-9.1,5.5-13.8c8-26.3,9.7-53.6,11.9-80.8c1.1-13.7,0.1-26.5-8-38.1C607.7,2647.4,608.1,2646.5,608.1,2645.9z
                        M1049.8,977.9c1.5,3.2,3.2,6.3,4.3,9.6c10.6,31.7,23.4,62.3,43.7,89.3c0.9,1.2,1.4,2.6,2.6,4.8c-1.7-0.6-2.4-0.7-2.7-1
                        c-4.6-5.3-9-10.7-13.6-16c-5-5.8-9.9-11.8-15.3-17.2c-7-7.1-13-5.6-16.4,3.9c-1.3,3.7-2,7.7-2.5,11.7c-1.6,12.6,1.3,24.4,5.6,36.2
                        c4.5,12.2,9.3,24.3,12.8,36.8c15.9,57.2,35.9,112.9,60,167.1c20.2,45.3,42.3,89.7,63.6,134.5c0.6,1.2,1,2.6,1.9,3.5
                        c1.4,1.2,3.6,3.1,4.8,2.7c1.8-0.6,3.5-2.7,4.5-4.5c0.8-1.3,0.7-3.3,0.5-4.9c-2-26.2-2.8-52.6-6.5-78.6c-7.2-50.2-20.1-99.1-37-146.9
                        c-7.4-21-15.6-41.7-23.1-62.8c-1.7-4.8-3.7-10.9-2.2-15.3c2.6-7.6,2.6-15,2.7-22.6c0.1-4.4,0-8.9,0-13.3c0.9-0.2,1.8-0.3,2.8-0.5
                        c6.3,81.3,35.7,154.2,79,222.3c-5.3-5.6-9.4-11.9-14.1-17.7c-4.6-5.7-7.1-13.3-16.3-17.9c0.7,8.2,0.9,14.7,1.9,21.1
                        c2.9,19.7,6.5,39.4,9.1,59.2c2.1,16.2,2.1,32.6,4.5,48.7c3.5,23.2,1.7,45.7-5.3,68.1c-4.9,15.7-2.8,31.1,5.4,45.3
                        c4,6.9,9,13.2,13.4,19.8c11.9,17.7,23.7,35.4,35.5,53.1c6.3,9.4,12.1,19.1,18.7,28.3c2,2.8,5.3,5.5,8.5,6.4c2.7,0.8,6.8-0.3,8.9-2.1
                        c1.7-1.4,2.3-5.6,1.5-8c-1.9-5.6-4.9-10.9-7.3-16.4c-0.8-1.7-1.1-3.7-1.7-5.5c3.1,2.7,4.9,5.9,6.5,9.2c5.9,12.3,11.9,24.6,17.7,36.9
                        c3.4,7.1,8.6,11.8,16.6,12.7c9.9,1.1,14.6-4.2,12-13.8c-0.9-3.4-2.1-6.7-3.2-10c3.2,2.1,5,4.9,6.9,7.6c2.1,3,4.1,6.1,6.6,8.8
                        c5.4,5.8,12,6.7,18,3c5.5-3.5,7.7-9.7,5.2-16.6c-2.3-6.2-5.4-12.1-8.1-18.2c-0.8-1.8-1.2-3.8-2.2-7.4c2.8,2.3,4.1,3.3,5.4,4.3
                        c3.9,3,8.1,5.2,12.9,2.4c4.5-2.6,5.4-7.2,5.6-12.1c0.3-6.7-2.3-12.3-5.5-17.8c-12.9-22.2-25.7-44.4-38.5-66.6
                        c-1.7-3-3.1-6.3-4.6-9.3c7.6-4.5,7.6-4.5,13.9-3.2c9.8,2.1,19.5,4.4,29.3,6.4c11.6,2.4,22.3-2.2,27.6-11.5c3.1-5.4,2.5-9.5-2.8-12.9
                        c-3.9-2.5-8.2-4.4-12.6-6c-9.9-3.7-18.1-9.5-25.4-17.2c-15.7-16.7-32.1-33-47.9-49.6c-7.3-7.7-16-12.9-26.1-16c-3.1-1-6.6-2-8.9-4.1
                        c-12.2-10.9-23-23-30.9-37.5l0.6-0.6c7.3,7.7,14.5,15.5,21.9,23.1c2.7,2.8,5.8,7.1,10,4.2c4.4-3,1-7.3-0.7-10.7
                        c-32-61.2-46.9-127-53.4-195.2c-4.9-52.2-22.7-99.7-53.2-142.4c-2.1-3-4.5-5.9-7.1-8.3c-5.9-5.3-10.1-5.1-15.6,0.4
                        c-1.8,1.8-3.4,3.9-5.9,7c0.4-10.1,4.9-17.1,9.7-23.8c10.3-14.6,15.3-30.9,16.2-48.6c2-42.2-9.4-80.2-35-114.1
                        c-8.6-11.4-17.5-21.7-30.7-27.7c-1.2-0.5-2.1-1.6-3.1-2.4c0.3-0.3,0.5-0.7,0.8-1c6.2,2.9,12.3,5.9,20.1,9.6
                        c0.6-5.3,1.5-9.1,1.3-12.8c-1-23-2.2-45.9-3.3-68.9c-0.9-18.9-5.3-36.8-15.6-53.1c-28.3-44.7-68.2-71.2-121-77.4
                        c-13.7-1.6-26.5-5.3-39-11.3c-28.5-13.7-57.4-26.5-86.2-39.5c-12-5.4-14.5-4.8-20.8,6.5c-6.9,12.3-15.3,23.4-24.9,33.7
                        c-9.5,10.2-18.9,20.6-28.3,31c-1.8,2-3.7,3.9-5,6.2c-3,5.2-0.8,9.5,5.1,9.6c3.3,0.1,6.6-0.7,9.8-1.5c25.1-6.4,50.3-12.8,75.4-19.6
                        c12.3-3.3,24.8-4.7,36.1,1.4c11.9,6.5,20.6,5.7,29.5-4.7c3.8-4.5,9.3-8.1,18.2-7.1c-6.4,4.8-11.4,8.5-16.4,12.3
                        c-6.3,4.9-9.9,11.3-7.8,19.2c2.6,9.9,5.2,20.2,9.7,29.3c14.1,28.3,29.2,56.1,43.9,84.1c4.9,9.3,12.3,15.5,22.3,18.9
                        c6.6,2.3,13.2,4.6,19.8,7c1.9,0.7,3.6,2,7.5,4.1c-17.3,3.5-19.2,14.6-17.5,27.3c2.1,16.1,4.7,32.2,7,48.3c-1.1-2.3-2.7-4.5-3.2-6.9
                        c-2.3-10.7-4.1-21.5-6.3-32.3c-1.1-5.3-2.8-10.4-4.3-16c-5.8,2.9-6.1,7.4-6.9,11.6c-0.7,3.9-1.1,7.9-1.7,11.9
                        c-3.7,26.2-4.5,52.4,0.2,78.5c6.5,35.6,20.9,67.9,39.8,98.4c3,4.8,6.1,9.5,9.4,14.2c2,2.9,4.8,5.2,8.4,3.7c3.9-1.6,3.6-5.5,2.9-8.7
                        c-1.4-6.5-3.3-12.9-4.9-19.3C1054.4,997.9,1052.1,987.9,1049.8,977.9z M471.8,915.8c0.2-2.7,0-5.4,0.5-8
                        c3.5-17.3,7.6-34.4,10.7-51.8c2.2-12.7,3.7-25.7,4.5-38.6c0.7-10.8-2.6-14.7-12.8-18.8c-1.2-0.5-2.4-0.9-4.8-1.8
                        c5.3-2.1,9.5-3.9,13.8-5.6c23.5-9.1,23.5-9.1,38.4-26.6c-1.8,5.9-4.3,11.5-6.7,17.1c-5.6,13.5-4.2,20.6,6.6,30.2
                        c15.9,14.2,32.7,27.4,51.5,37.7c1.7,0.9,3.2,2.1,4.8,3.2c-4,0-7.3-1.3-10.7-2.6c-8.4-3.2-16.7-6.7-25.2-9.4
                        c-7-2.2-10.6,0.2-12.5,7.4c-0.4,1.6-0.8,3.3-1,4.9c-3.4,28.8-6.6,57.6-5.5,86.7c1,25.6,3,51.2,5.1,76.8c1.9,22.9,4.2,45.8,7,68.6
                        c0.8,6.4,3.9,12.6,6.4,18.7c0.8,1.8,3.5,4.2,5,4c2.2-0.4,4.3-2.5,5.8-4.5c1.1-1.5,1.2-3.8,1.8-5.6c4.9-14,12-26.7,22-37.7
                        c5-5.4,9.8-10.9,15.1-15.9c21.8-20.7,35.9-45.2,38.8-75.5c0.1-0.7,0.4-1.3,0.5-1.9c4.7-23.5,9.6-46.9,14.1-70.5
                        c2.7-14.3-0.2-18.5-14-22.7c-3.9-1.2-7.8-2.6-11.7-3.9c1.7-1.4,3-1.7,4.3-1.4c13.5,3.1,27.1,2.9,40.7,1.8
                        c39.4-3.3,74.2-38.5,77.5-77.9c2.2-26.4,0.5-52.6-1.9-78.8c-2.3-25.9-15.3-43.7-39.4-52.7c-24-8.9-48.3-17.2-72.6-25
                        c-6.1-2-13.3-2.3-19.6-1.2c-14.2,2.5-24.7,11-32.5,23c-1.9,2.9-4.1,5.6-6.2,8.4l0.2,0.2c0.4-2.2,0.6-4.5,1.2-6.7
                        c3.9-13.1,2.7-17.1-8.2-25.9c-1.8-1.5-3.7-2.8-5.5-4.3c-1.5-1.2-2.9-2.5-6-5.2c10,2,15.1,7.4,19.9,12.8c3.5,4,6.9,4.5,11.4,1.9
                        c17.3-10,35.3-9.5,54.1-4.1c22.7,6.5,45.7,12,68.5,18.2c5.5,1.5,10.3,1.3,14.9-2.4c0.5-5.4-2.4-9.1-5.7-12.5
                        c-4.4-4.6-8.9-9-13.2-13.7c-13.4-14.4-26.9-28.7-36.9-45.8c-10.4-17.5-10.4-17.5-29.3-8.9c-30.6,13.9-61.2,27.8-91.7,41.9
                        c-8.3,3.8-16.8,5.9-25.9,6.9c-25.6,2.6-49.5,10.3-70.7,25.2c-31.9,22.3-55.8,50.7-63.4,89.9c-5.8,29.5-3.3,59.8-4.7,89.7
                        c-0.1,2.7,2.4,5.5,4,8.8c-1.8,1.9-4.4,4.4-6.7,7.2c-23.6,28.3-39,60.3-43.3,97.1c-3.5,30.4,0.3,59.2,19.3,84.5
                        c4.4,5.9,7.3,12.5,7.7,22c-2.6-4-4.1-6.5-5.8-8.9c-6.2-8.7-12.1-9.3-19.5-1.3c-4,4.4-7.4,9.4-10.8,14.4
                        c-30.3,44.3-44,94.1-49.7,146.9c-2.6,24.1-6.3,48.2-11.2,71.9c-8,38.6-22.2,75.2-40,110.4c-1.9,3.7-4.9,7.5-0.1,12.8
                        c2.8-1.7,6.2-3,8.5-5.3c7-7,13.7-14.5,20.4-21.8c0.3,0.2,0.5,0.5,0.8,0.7c-8.1,10.5-15.5,21.5-24.4,31.3c-4.3,4.7-10.8,8.1-17,9.9
                        c-8.8,2.5-15.5,7.3-21.7,13.6c-14.6,15-29.6,29.8-44,45.1c-10.5,11.1-22,20.3-36.6,25.3c-1.9,0.6-3.7,1.5-5.4,2.5
                        c-6.5,3.9-7.5,8.7-3.2,15c5.6,8.2,13.6,11.8,23.3,10.2c11.2-1.8,22.2-4.3,33.3-6.6c5-1,9.9-1.8,13.9,4c-1.7,3-3.4,6.2-5.2,9.3
                        c-12.2,21-24.5,42-36.7,63c-2,3.4-4.5,6.9-5.4,10.6c-1.2,4.7-2.1,9.9-1.3,14.6c1.6,9.3,10,11.8,17,5.7c2.1-1.8,4.1-3.8,6.1-5.7
                        c-0.1,3.7-1.3,6.6-2.6,9.5c-2.6,5.8-5.6,11.4-7.7,17.3c-2.3,6.7-0.2,12.5,4.9,16c5.2,3.6,11.2,3.7,16.6-0.9
                        c3.5-2.9,6.2-6.9,8.6-10.8c4-6.6,7.5-13.4,11.2-20.2c-1.6,6.9-4.7,13.1-7.4,19.4c-2.1,4.8-2.9,9.7,1,14c4,4.3,9,3.5,14,2.4
                        c7.3-1.6,11-7,14-13.2c8-16.4,16-32.9,24.1-49.3c0.9-1.9,2.3-3.6,3.5-5.5c-2.6,9.2-6.7,17.5-10.6,26c-1.2,2.7-2.4,5.6-2.8,8.5
                        c-0.7,5.5,3.4,9.5,8.5,7.8c3.8-1.3,7.9-3.9,10.3-7.1c7.4-9.8,14.2-20.1,21.1-30.3c13.2-19.6,25.8-39.6,39.7-58.8
                        c13.9-19.2,17.8-39.3,11.3-62.2c-4.7-16.3-7.9-33.3-6.2-50.3c3-30.8,6.8-61.6,10.6-92.3c1.5-11.9,4.2-23.6,6.1-35.4
                        c0.7-4.3,2-9-2.7-13.4c-6.7,9.2-13,17.7-19.3,26.3c-0.6-0.3-1.2-0.7-1.8-1c38.3-63,66.3-129.7,74-203.9c1.6,6.7,0.8,13.8,3.2,19.5
                        c4.4,10.5,1.2,19.4-2.5,28.7c-1.2,3.1-2.3,6.3-3.4,9.4c-14.2,39.8-28.7,79.5-39.5,120.4c-11.2,42.7-17.2,86.1-21.2,129.9
                        c-0.7,7.6-0.4,15.3-0.2,22.9c0.1,3.9-0.2,9.1,4.6,10c5.1,1,5.9-4.4,7.5-7.8c6.5-13.8,12.9-27.8,19.4-41.6
                        c15.1-31.9,31.3-63.3,45.3-95.6c18.9-43.7,37-87.7,49.9-133.7c6.9-24.7,13.3-49.4,23.8-73c5.9-13.3,4.3-27.5,0.6-41.2
                        c-2.1-7.7-9.4-9.7-15.7-4.7c-2.6,2-4.7,4.7-6.8,7.3c-7.8,9.3-15.5,18.7-23.3,28.1c-0.4-2.8,0.7-4.6,1.8-6.4
                        c5.5-9.1,11.4-18,16.5-27.3c15.4-27.8,25.5-57.8,34.3-88.2c1-3.3,2.7-6.5,4.1-9.7c-0.4,2.6-0.6,5.3-1.3,7.8
                        c-5.8,21.5-11.6,43-17.4,64.5c-0.6,2.2-1.8,4.6-1.6,6.7c0.2,2.4,1.3,5.8,3,6.5c1.8,0.8,5-0.6,7-2c2.1-1.5,3.5-4,4.9-6.2
                        c26.7-39.9,44.5-83.2,48.4-131.5c1.8-22.4,0.1-44.6-4.3-66.5c-0.8-3.9-0.9-8.6-6.3-11.4c-1.4,3.9-3,7.2-3.7,10.7
                        c-2.4,12-4.2,24.2-6.9,36.2C480.2,881.7,475.9,898.8,471.8,915.8z M809.6,1199.6c-0.2-0.3-0.4-0.7-0.7-1c9.3-2.3,18.7-4.4,28-6.8
                        c8.6-2.3,15.8-6.8,20.5-14.8c11-19,17.7-39.4,20.5-61.1c1.5-11.2-2.4-19.1-12.3-24.9c-29.3-17.2-60.9-22.4-94.3-19.2
                        c-7.7,0.7-12.5,4.7-13.8,12.2c-1.6,9.1-3.5,18.3-3.9,27.6c-0.6,16.3-0.2,32.7-0.1,49c0.1,10,0.1,20,0.7,30
                        c0.8,13.9,7.5,19.1,21.1,16.8c3-0.5,6-1,9-1.5c-0.7,2.3-1.8,3.1-3,3.4c-15.5,3.8-22.3,14.2-23.4,29.6c-1.1,15.9-2.9,31.8-4.4,47.7
                        c-0.3,2.7-0.9,5.5-1.3,8.2c-2.4-10.1-3.3-20-4.2-29.9c-1.1-11.6-2-23.2-3.4-34.8c-1.1-9-6.3-15.8-15.1-18.1
                        c-23.8-6.3-47.6-12.2-71.6-17.8c-6.1-1.4-11.3-3.3-15.6-8c-10.6-11.9-18.2-25.3-22.4-40.8c-5.8-21.6-5.6-43.6-2.9-65.5
                        c1.6-12.9,4.5-25.6,6.6-38.4c0.7-4.1,2.4-8.5-1.3-12c-6.1-1.1-9.6,2.7-13,6.3c-12.6,13.3-25.3,26.6-37.8,39.9
                        c-15.2,16.2-17.9,32.5-7.6,52.1c6.5,12.4,13.7,24.4,21.7,35.9c12.2,17.5,28.6,29.6,49.6,34.8c1.2,0.3,2.3,1.2,4,2
                        c-1.5,26.4,2.3,52.4,6.3,78.4c4,26.3,10.9,51.8,19.3,78.6c-1.9-1.5-2.5-1.7-2.7-2.2c-9.1-23.6-18.1-47.3-22-72.5
                        c-3.3-21.4-5.3-43-7.7-64.5c-1.1-10-5.8-17.7-14.6-22.5c-24-13-40.4-33.2-53.6-56.5c-0.8-1.4-1.4-3.1-2.4-4.4c-3.5-4.7-8.5-4.3-11,1
                        c-1.1,2.4-1.7,5.1-2.2,7.7c-3.3,17.3-6.8,34.6-9.8,52c-5.5,31.8-1.6,62.2,12.4,91.5c24.8,51.8,60.1,94.9,105.9,129.4
                        c6.3,4.8,13.5,8.5,20.7,11.9c4.3,2,9.4,2.5,14.5,3.8c0.9-3.5,1.5-5.8,2.4-9.2c14.6,19.6,31.6,33.7,56,34
                        c24.2,0.3,41.8-13.1,56.6-30.6c7.8,6.5,8.2,6.9,16.4,2.7c8.6-4.4,17.3-8.8,25-14.5c47.7-35.3,82.8-80.7,107-134.6
                        c5.4-11.9,8.7-24.4,10.3-37.4c4.5-36.2-3.3-70.9-13.1-105.3c-0.9-3.1-3.5-5.7-6-9.6c-2.5,3.6-4.1,5.7-5.3,7.9
                        c-13.5,25.6-31.2,47.1-56.9,61.4c-7.7,4.3-11.9,11.5-13,20.3c-0.6,4.3-1.3,8.6-1.4,12.9c-0.7,42.6-13,82.6-27.3,122.2
                        c-0.5,1.4-1.4,2.6-2.1,3.9c13.4-51.6,24.8-103.3,24.3-157.3c2.5-0.9,4.9-1.9,7.4-2.7c16.4-5,29.8-14.5,41.4-27
                        c12.2-13.1,20.7-28.4,27.7-44.8c6.8-15.9,4.8-30.4-5.1-44.3c-12.2-17.3-26.5-32.5-43.1-45.6c-3.7-3-8.3-4.8-14.9-8.5
                        c1,7.8,1.3,13.1,2.4,18.1c5.8,25.8,9.2,51.8,6.9,78.3c-2.1,23.1-9.1,44.4-25.3,61.6c-3.7,3.9-8.8,7.9-13.9,9
                        C835.9,1196.3,822.7,1197.6,809.6,1199.6z M983,1943.7c0.7-0.3,1.4-0.5,2.1-0.8c1.9-6.9,4-13.8,5.8-20.8
                        c19.2-74.1,40.9-147.5,50.2-223.9c3.6-29.9,6.9-59.5,3-89.6c-2.1-16.5-4.5-33-6.1-49.6c-4-42.8-9.8-85.4-20.3-127.1
                        c-12.4-49.4-25.4-98.6-38.2-147.8c-0.6-2.3-2.4-4.3-3.6-6.3c-6.2,1.5-6.2,5.5-6.3,9.6c-0.8,31.3-1.8,62.6-2.6,93.9
                        c-1.2,48.8,5,97.1,9.4,145.5c4.7,52.4,9.9,104.8,14.4,157.2c3,34.9-0.8,69.7-2.3,104.6c-0.7-5.7-0.6-11.3-0.7-17
                        c-0.7-34.6-0.8-69.3-2.2-103.9c-1-23.3-3.6-46.5-5.8-69.7c-3.6-39.1-7.7-78.2-11.1-117.4c-2-22.9-4-45.8-4.8-68.8
                        c-0.9-24.3-0.2-48.6-0.5-73c-0.1-4.2-1-8.6-2.7-12.4c-1.7-3.8-5.5-4.1-8.1-0.8c-2.2,2.8-3.5,6.3-4.6,9.7c-2.4,7.2-4.4,14.5-6.6,21.8
                        c-0.1-7.9,1.2-15.4,2.4-22.9c0.5-3,0.7-6.4-2.7-7.8c-3.7-1.4-5.4,1.6-7,4.1c-4.4,7.1-8.4,14.4-13.1,21.3
                        c-30.7,44.4-69.8,79.5-117.7,104.4c-15.3,7.9-23.4,19.1-23.9,36c-0.1,3.3-0.6,6.7-0.4,10c1.8,27.2,2.5,54.6,5.9,81.7
                        c4.6,36.3,10.7,72.4,17.3,108.5c5.2,28.5,11.9,56.7,18,85c0.9,4.1,2.3,8.2,7.3,9.1c14.1-57.8,27.6-115,42.1-172
                        c21.8-85.9,43.3-157.2,50.4-169.3c-0.4,2.8-0.4,4.4-0.8,5.9c-13.5,54.2-27,108.5-40.5,162.7c-7.7,31.1-14.1,62.4-14.8,94.5
                        c-1.3,56.8,4.3,113.1,14.4,169c5.7,31.6,14.6,62.2,30.3,90.5c17.5,31.5,38.4,60.8,59.1,90.2c1.9,2.7,4.8,4.7,8.9,8.5
                        c0.4-7.5,0.8-12.4,0.9-17.4c0.8-35.3,1.5-70.6,2.3-105.9c0-1.1,0.5-2.3,1-4C981.9,1941.4,982.5,1942.5,983,1943.7z M529.9,2064.1
                        c0.9,0.2,1.8,0.5,2.7,0.7c3-3.5,6.3-6.8,8.9-10.6c15.3-22.6,30.6-45.2,45.5-68.1c15.9-24.5,27.6-51,34.2-79.4
                        c16.1-69.1,23.2-139.3,20.3-210.2c-0.7-17.8-3.5-35.9-7.6-53.3c-15.7-66-32.4-131.8-48.6-197.7c-0.6-2.3-0.8-4.6-1.1-6.9
                        c38.2,114.5,67.9,231,93.2,348.8c5.9-1.4,7.4-5.1,8.4-9c1.2-4.5,2-9.1,3.1-13.6c18.1-74.2,31.2-149.2,36.5-225.5
                        c1.1-15.3,1.3-30.6,1.2-45.9c-0.1-15.7-7.9-27.1-21.2-35.1c-4-2.4-8.1-4.6-12.1-6.9c-48.8-28.2-89.1-65.2-117.5-114.4
                        c-1.5-2.6-3.2-5.1-5.2-7.3c-2.1-2.4-4.8-3.6-8.8-0.1c1.4,9.4,2.8,19.5,4.3,29.6c-3.7-7.4-5.7-15-7.7-22.7c-0.8-2.9-1.1-6.2-2.7-8.5
                        c-1.5-2.1-4.7-4.6-6.7-4.3c-2,0.3-4.2,3.7-5.1,6.1c-1,2.7-0.6,5.9-0.6,8.9c0,14.7,0.5,29.3-0.1,44c-1.2,28.6-2.7,57.2-4.7,85.8
                        c-1.5,20.2-4.3,40.4-6.1,60.6c-3.3,35.5-6.5,71-9.3,106.5c-2,25.6-4.4,51.2-4.5,76.8c-0.2,35,1.7,69.9,2.5,104.9
                        c0.3,16.3,0.1,32.5,0.1,48.8c-0.7-5.9-1-11.8-1.3-17.8c-1.8-35.9-3.9-71.8-5.4-107.8c-1.7-40.4,2.1-80.5,6.2-120.7
                        c3.1-30.8,6.1-61.6,9.2-92.5c2.1-20.5,5.1-41,6.3-61.6c1.8-33.6,3.1-67.2,3.2-100.9c0.1-26.3-1.9-52.6-3.1-78.9
                        c-0.1-2.8-1.3-5.5-1.9-8.1c-7,0.5-7.2,5.2-8.2,9.3c-10.1,39-20.2,78-30.4,116.9c-8.1,30.9-16,61.9-20.2,93.6
                        c-3.3,25.1-6.3,50.2-9.1,75.4c-2.3,20.5-4.7,41.1-5.8,61.6c-2,39.6,6.1,78.3,13.2,116.9c6.6,35.9,16.5,71.3,25.2,106.8
                        c6.3,25.5,13,50.9,19.6,76.4c0.7,2.8,0.5,6.6,5.7,7.8c0-11.3,0-21.9,0-32.5C529.3,1961.4,526.8,2012.9,529.9,2064.1z M869.9,305.1
                        c0.5-0.1,0.9-0.1,1.4-0.2c-1.8-12.8-2.8-25.8-5.7-38.4c-10.7-45.5-49.2-80.5-95.8-88.1c-46-7.5-91.8,13.6-116.3,53.8
                        c-14.6,24-21.4,50.2-20.2,78.2c0.6,13.5-3.8,22.9-16.7,28.6c-11,4.9-14.5,13.7-10,24.8c5.8,14.2,12.6,27.9,19.2,41.7
                        c2.3,4.7,6.1,7.7,11.6,8.4c9.1,1.3,9.8,2.2,10.6,11c1.1,11.6,2.4,23.2,3.1,34.8c0.8,13.5,6.8,23.8,18.7,29.8
                        c16.9,8.5,34,16.9,51.6,23.8c11.8,4.7,24.6,5.2,37.4,3.4c28.9-4,54.9-15.6,79.8-30.2c8.4-4.9,12.8-12.3,13.6-22
                        c1.2-13.6,2.5-27.2,3.8-40.8c0.8-7.9,1.5-8.8,9.5-9.9c7.3-1,12.4-5,15.3-11.4c5.7-12.8,11.2-25.6,16.2-38.6c3.2-8.3,1-15.8-6.1-21.4
                        c-4.2-3.3-8.5-6.4-12.9-9.4c-5.4-3.6-8.4-8.3-8-15C870.1,313.7,869.9,309.4,869.9,305.1z M757.5,624.5c0.2,4.4,0.2,7.8,0.6,11
                        c1,8.7,4.9,11.2,12.9,7c6.1-3.2,12-7.2,17.1-11.9c19-17.8,34.9-38.4,49.5-59.9c5.8-8.6,8-18.3,7.3-28.5c-0.6-8.9-1.6-17.9-2.9-26.8
                        c-0.6-3.9-2-7.7-3.8-11.3c-3-6.2-5.2-7-11.9-4.7c-12.9,4.5-25.5,9.7-35.9,19c-1.7,1.5-4.6,2.5-7,2.5c-20.6,0.4-41.2,0.6-61.8,0.7
                        c-2.7,0-6.3-0.6-7.9-2.3c-12.3-12.8-28.8-17.1-44.9-23.5c-1,1.9-2.1,3.3-2.4,4.8c-1.9,9.1-3.8,18.2-5.3,27.3
                        c-3,18.3-1.5,35.8,10.4,51.2c1.6,2.1,2.8,4.5,4.2,6.8c14.4,23,34.1,40.7,56.2,56c8.2,5.6,13.1,3.2,14.1-6.5c0.4-3.5,0.4-7.1,0.6-11
                        C750.3,624.5,753.5,624.5,757.5,624.5z M849.4,1728.3c-2.8,4.8-4.8,7.4-5.8,10.3c-2.1,6.6-3.9,13.4-5.5,20.2
                        c-9.9,42.4-13.7,85.4-14.6,128.8c-1.2,57.3,5.2,113.7,22.6,168.6c5,15.9,10.7,31.5,16.2,47.2c1.4,4,2.7,8.7,9.6,5.6
                        c0-3.8,0-7.8,0-11.8c0-16-0.3-32,0.1-48c0.5-22.7,2.1-45.4,8.6-67.2c6.2-20.9,5.7-40.9,0.3-61.9c-12.8-49.8-20.8-100.4-23.4-151.8
                        c-0.5-10.3-2.1-20.5-3.6-30.7C853.6,1735,851.6,1732.7,849.4,1728.3z M657.1,1728.3c-2.7,4.8-4.8,7.4-5.5,10.2
                        c-1,4.5-1.1,9.2-1.5,13.9c-2.4,24.9-3.7,49.9-7.4,74.6c-4.3,28.9-10.5,57.6-16.1,86.3c-6.3,32.2-11.6,33.2-0.7,70.5
                        c0.1,0.3,0.1,0.7,0.1,1c8.7,38.4,9.2,77.3,7.9,116.3c-0.1,2-0.5,4.1,0,5.9c0.3,1.3,1.9,2.8,3.1,3.1c1.3,0.3,3.2-0.4,4.4-1.3
                        c0.9-0.7,1.3-2.3,1.8-3.5c23-55.6,35.3-113.7,38.5-173.7c2.1-39,1.3-77.9-3.9-116.7c-3.4-25.4-7.9-50.6-15.2-75.3
                        C661.8,1736.4,659.7,1733.5,657.1,1728.3z M930.7,2334.2c4.9-2.3,5.7-6.7,6-10.9c0.8-9.6,1.3-19.3,2-28.9
                        c3.4-50.5,7.1-101,5.5-151.6c-0.5-15.6-1.4-31.3-3.8-46.7c-5.8-36.9-17.4-72.4-29.2-107.7c-2-5.9-5-11.6-8.1-17.1
                        c-3.2-5.5-8.1-6-12-0.9c-2.3,3-3.8,7-4.7,10.7c-8.7,33.4-11.4,67.6-11.9,101.9c-0.2,12.9,1.1,26,3.7,38.6
                        c6.7,32.2,14.7,64.2,22,96.2c8,35,15.9,70,23.7,105C924.9,2327.5,925.8,2332,930.7,2334.2z M749.6,1146c0.2,0,0.4,0,0.6,0
                        c0.2-4,0.5-8,0.6-12c0.2-17,0.3-34-4.6-50.5c-2.7-9-5.6-11.3-15-11.5c-11-0.1-22-0.2-33,0.2c-22.8,1-43.6,8.3-62.8,20.6
                        c-7.9,5.1-11.1,11.6-9.7,21.1c3,20.2,8.4,39.6,18,57.8c6,11.3,14.8,18.4,27.3,21.4c15.8,3.8,31.5,8.2,47.3,12.1
                        c4.5,1.1,9.1,2.1,13.7,2.4c10,0.8,14.2-2.3,16.3-12c0.9-4.2,1.2-8.6,1.2-12.9C749.7,1170.6,749.6,1158.3,749.6,1146z M754.2,1022.4
                        c-0.1,0-0.3,0-0.4,0c0,9.3-0.3,18.6,0.1,27.9c0.4,11.9,3,14.7,14.9,15.1c12,0.5,24,0.1,35.9,0.4c20.7,0.5,40,5.7,57.8,16.5
                        c2.5,1.5,5.2,3.2,8,3.9c4.8,1.2,8.6-1.6,9.3-6.5c0.3-2.3,0.2-4.7-0.1-7c-2-15.2-4-30.3-6.2-45.5c-1.4-9.5-5.2-17.8-12.5-24.4
                        c-25.3-22.7-55-32.9-88.9-31c-11.8,0.7-16.9,5.5-17.8,17.4C753.6,1000.5,754.2,1011.5,754.2,1022.4z M871.3,996.3
                        c0-5.9,0.8-10.3-0.1-14.3c-6.2-27.3-12.6-54.5-19.3-81.6c-1.9-7.7-6.6-14.1-14-17.4c-15.4-7.1-31-13.8-46.7-20.2
                        c-6.5-2.7-10.6-1.2-14.7,4.6c-5.4,7.6-11.3,15-15.4,23.3c-3.9,7.9-7.6,16.7-8.4,25.3c-1.1,11.8,0.5,23.9,1.2,35.8
                        c0.3,5.5,3.6,9.3,8.9,10.8c2.9,0.8,5.8,1.3,8.8,1.7c31.1,3.6,60.8,11.5,88.2,27.1C862.8,992.8,865.9,993.9,871.3,996.3z M717,1064.9
                        c0,0.2,0,0.5,0.1,0.7c6.3,0,12.6,0.2,19-0.1c10.2-0.5,12.6-3,13.9-13.1c2.5-19.8,3.5-39.6,0.3-59.6c-2.6-15.9-5.3-19.4-21.2-20.6
                        c-1.3-0.1-2.7-0.1-4-0.1c-31.6-1.1-58.5,10.5-81.9,30.9c-4.1,3.5-7.1,8.8-9.2,13.9c-7.6,18.1-10,37.2-9.4,56.6
                        c0.4,14.9,3.5,16.4,16.7,8.8c11.3-6.5,22.9-12.3,36-13.8C690.5,1067,703.7,1066.1,717,1064.9z M749.9,926.7
                        c4.2-17.6-4.3-34.5-16.4-50.3c-1.8-2.4-3.3-5-5.1-7.4c-5.4-7.6-8.3-9-16.8-6c-11.6,4-23.2,8.3-34.4,13.1
                        c-14.6,6.3-24.6,17.5-28.4,32.8c-6,23.8-10.9,48-16.1,72c-0.9,4.2-1.9,9.7,2.8,11.5c2.9,1.1,7.3-1.4,10.9-2.4c0.9-0.3,1.7-1,2.6-1.5
                        c16.1-8.9,33.1-15.6,51.2-18.9c13.1-2.4,26.2-4.5,39.2-7.3c7.8-1.7,10-4.5,10.3-12.4C750.2,943.2,749.9,936.6,749.9,926.7z
                        M752.3,709.7c2.2-4.3,4-7,5.1-10c4.1-11.3,9.2-22.1,16.7-31.5c1.2-1.5,2.1-3.4,2.8-5.3c1.4-4.3-0.4-7.1-5-7.7
                        c-1.9-0.2-3.9,0.4-5.9,0.4c-8.6,0.2-17.2,0.4-25.7,0.5c-4.5,0.1-9.1,0-14.3,0c0.5,2.5,0.5,4.3,1.2,5.6c7.1,14.6,14.2,29.1,21.4,43.7
                        C749.1,706.5,750.2,707.3,752.3,709.7z"/>
                    <path d="M2407,2148.4c-3.3-27.4-6.7-54.8-9.7-82.2c-1.2-11.2,1.3-21.9,6.9-31.7c6.4-11.2,12.6-11.7,20.1-1.4c3.6,5,7,10.3,11.6,14.9
                        c-1.2-3-2.3-5.9-3.6-8.8c-10.6-23.4-21.7-46.5-31.6-70.1c-16.8-39.8-22.8-81.9-24.6-124.8c-1.3-31.6-3.5-63.2-5.3-94.8
                        c-2.6-44.6-5.7-89.1-7.7-133.7c-0.7-15.2,0.8-30.6,2.4-45.9c1.3-12.3,5.5-14.7,17.7-11.6c7.7,2,15.3,4.8,23,6.7
                        c5.1,1.3,10.5,2.2,15.7,2.3c7.5,0.1,9.7-2.5,8.5-9.9c-1.9-11.5-4.6-22.8-6.8-34.3c-4.9-25.4-5.9-50.9-1.9-76.5c0.2-1.3,0-2.6,0-4.6
                        c-5.5,13.5-7.5,35-5.1,53.8c1.7,13.6,3.3,27.1,4.9,40.7c2.2,19.2-4.1,25.4-22.6,20.4c-16-4.3-31.5-10.6-47.2-16.1
                        c-8.7-3.1-17.3-6.4-27-8.3c1.4,1.5,2.8,3.1,4.2,4.6c12.4,13.1,19.1,29,20.7,46.6c6.4,69,12.6,138,18.4,207c2,23.5,2,47.3,4,70.8
                        c2.8,32.9,6.6,65.6,10,98.4c0.2,2.1,1.1,4.7,2.6,6.1c10.2,9.7,15.3,22.5,21.2,34.7c2.3,4.7,3,11.6,1.3,16.4
                        c-5.9,16.1-13.2,31.8-20.3,48.5c-3.3-1.8-5.8-2.8-7.7-4.3c-6.8-5.4-13.5-10.9-20.1-16.5c-8.6-7.3-8.8-9.4-3.1-19.5
                        c1.4-2.5,2.8-5,2.8-8.5c-6.7,9.1-14.4,17.6-19.9,27.4c-3.3,5.7-4.3,13.3-4.6,20.1c-0.7,19.6-0.3,39.3-0.5,59
                        c-0.1,5.4,1.7,13-5.5,14.1c-6.6,1-8.8-6.3-10.9-11.3c-11.2-28.1-22.3-56.3-30.1-85.6c-13.4-50.2-26.2-100.5-33.9-151.9
                        c-2.4-16.1-4.1-32.4-4.7-48.7c-2.4-60.9-4.7-121.9-6.2-182.8c-0.5-20.9,1.1-42,2.1-62.9c1.3-26.6,3-53.2,4.6-79.8
                        c0.3-5.5-0.3-11.7,6.3-14.6c6.6-2.9,10.6,1.8,15,5.2c11.4,8.9,24.2,14.8,38.5,18.3c-1.3-1-2.5-2-3.8-2.9c-1.4-0.9-2.9-1.6-4.4-2.5
                        c-17.4-9.9-34.4-20.8-47.9-35.7c-8.4-9.3-16-19.6-22.8-30.2c-9.5-14.7-8.4-29.6,1.8-43.9c9.1-12.7,18.5-25.3,27.2-38.3
                        c22.6-34,40.5-70.4,51.9-109.8c2.2-7.5,5-14.3,12.1-18.9c2.1-1.3,3.6-4.7,3.8-7.3c1.4-14.5,7.1-27.7,12.3-41
                        c4.8-12.4,14.7-20.4,26.2-26.4c6.2-3.3,13-2.8,19.6-1.2c8.3,2,16.4,4.6,25.4,5.7c-4.5-3.6-9-7.1-14.7-11.6
                        c10.8-12.5,21.1-25,32-36.9c4-4.4,8.5-2.9,10.9,2.7c0.8,1.8,1.2,3.8,1.6,5.7c3.7,16.9,7.4,33.8,11.2,50.7c0.2,1,0.4,2,0.6,2.9
                        c0.5,4.4,0.9,9-3.5,11.6c-4.6,2.7-8.2,0-11.5-3c-0.5-0.5-1-0.8-2.3-0.7c1.7,2.2,3.4,4.3,5.1,6.5c8.6,10.9,14.7,23.1,17.9,36.5
                        c6.2,25.5,12.1,51.2,17.9,76.8c0.7,3.2,0.7,6.7,0.4,9.9c-0.7,6.8-4.4,8.7-10.2,4.9c-3.5-2.4-6.6-5.6-9.1-9
                        c-4.7-6.4-8.8-13.4-13.3-20c-11.5-16.8-25.9-30.3-45-38.4c-1.5-0.6-3-1-5.2-0.4c1.5,1.2,2.8,2.6,4.4,3.7
                        c35.7,24.1,57.8,58.3,72.8,97.7c3.3,8.7,1.9,12.4-5.9,17.8c-3.6,2.4-7.5,4.3-11.2,6.6c-14.2,8.7-23.3,21.1-26,38c1.7-3,3.3-6,5-8.9
                        c7.7-12.9,17.1-23.8,32.2-28.3c2.2-0.6,4.5-1.3,6.8-1.5c12.4-1.4,18.3,2.6,20.5,14.8c2.5,13.7,4.7,27.6,6.2,41.5
                        c7.1,64.1,5.7,128.4,2.6,192.7c-2.7,56-10.6,111.4-19.7,166.6c-6.6,40.1-14.4,80-19.8,120.2c-2.4,17.6-0.1,35.9,0.2,53.9
                        c0.3,19.6,1.1,39.3,0.7,58.9c-0.2,13-2.4,25.9-8.9,37.6c-1.8,3.3-1.4,6.2,0.1,9.5c2,4.2,3.6,8.6,5.6,12.8
                        c18.4,39,30.7,80.1,41.1,121.8c5.1,20.3,5.5,40.9,2.4,61.5c-3.4,23-5.9,46.3-10.7,69c-4,18.8-11.5,36.8-16.4,55.5
                        c-11.9,44.7-23.6,89.5-34.5,134.4c-7.2,29.6-5.1,59.6,0.2,89.3c0.5,3,0.8,6,1.6,8.8c2.3,8.5,2,16.5-4,23.2
                        c5.2,14.6,11.6,18,26.5,16.9c14.6-1.1,29.3-1.8,43.9-1c22.6,1.2,38.6,13.7,48.8,33.3c10.1,19.6,4.1,42-15.1,54.3
                        c-9.4,6-20.3,9.9-30.7,14.2c-6.4,2.6-13.4,4.1-20,6.2c-11.8,3.8-21.4,10.5-28.1,21.2c-9.8,15.8-24.5,24.4-42.5,26.6
                        c-12.8,1.6-25.9,2.2-38.8,2c-17.1-0.3-23.8-5.8-29.2-21.9c-5.2-15.7-8-31.8-6.2-48.2c2.4-22.4,0.5-44.6-1.8-66.8
                        c-1.4-13.6-2.4-27.2-3.4-40.8c-0.9-12.5,1.7-24.5,6.2-36.1c6.1-15.9,18.7-23.2,35.4-21.2c20.1,2.3,34.2,11.9,40.1,32
                        c1.6,5.3,4.3,10.3,7.1,16.9c1.3-17.2-11.3-38.8-27-47.9c-19-11-36.4-5.3-55.1,7.2c1-7.4,0.7-12.9,2.6-17.4
                        c13.5-32.6,11.9-65.6,4.7-99.1c-6.9-32.2-13.1-64.6-19.9-96.8c-1.3-6.1-2.9-12.6-6.2-17.7c-19.5-30.4-23.9-63.7-20.7-98.8
                        c2.2-23.7,8-46.5,16.1-68.8c12-32.7,15.3-66.7,13.7-101.3c-0.6-12.3-1-24.6-1.4-37c0-1.3,0-2.7,0-4c0.6-12.2,5-15.3,16.8-11.2
                        c11,3.8,19.8,10.4,26.4,20.3c10.4,15.6,15,33.3,19.2,51.1c1.7,7.4,2.8,15,4.4,22.5C2404.5,2146,2406,2147.1,2407,2148.4
                        c-0.3,4-0.5,8-0.9,13.5c1.1-1.9,1.7-2.4,1.6-2.9C2407.5,2155.4,2407.2,2151.9,2407,2148.4z M2409.1,2181c0.3-3.9,0.6-7.8,0.9-11.7
                        c-0.7,0-1.3,0-2,0C2408.3,2173.3,2408.7,2177.1,2409.1,2181c4.2,37.7,6.1,75.6,4.7,113.6c-0.7,18.4-4.6,36.3-11.5,53.4
                        c-6.5,16.2-16.5,29.6-31.4,39c-1.4,0.9-2.5,2.3-3.7,3.4c4.4-0.2,8.1-1.6,11.3-3.7c12.5-8.1,20.7-19.6,25.8-33.4
                        c2.7-7.2,7.1-10.4,14.8-10.9c9.3-0.6,18.5-2.3,27.7-3.6c5.1-0.7,10.2-1.7,16.3-2.7c-0.8,6.5-1.4,12-2.4,19.8
                        c3.6-8.4,5.9-14.9,9.1-20.9c3.1-6,7.1-11.5,11.7-18.8c-6,2.3-9.9,4-13.9,5.3c-6.3,2-12.6,4.4-19.1,5.4c-13.3,2.1-23.7-5.5-26.1-18.7
                        c-1.2-6.5-1.7-13.2-2-19.9c-1.1-34-3.5-67.8-8.2-101.5C2411.6,2184.8,2410,2183,2409.1,2181z M2448.6,2060.5c1,0,2,0.1,3,0.1
                        c1.1-3.6,2.9-7.1,3.1-10.7c1-18.9,2-37.9,2-56.9c0-14.6-1.7-29.3-2.3-43.9c-0.4-9-1.1-18.2,0.3-26.9c6.4-40.4,13.7-80.7,20.4-121.1
                        c8.8-53.2,16.6-106.6,19.5-160.6c3.3-61.3,5.2-122.5-0.5-183.7c-1.7-18.2-4.1-36.4-6.8-54.5c-1.8-12.1-7-15.1-19-12.3
                        c-9.8,2.3-17.5,7.9-23.7,15.7c-8.6,10.9-14.1,23.3-16.7,37c-5.7,30.7-3.3,61,3.1,91.3c1.7,8.1,3.8,16.2,4.9,24.5
                        c1.2,9.4-2.6,14.2-12.1,14.2c-7.9,0-15.8-1.8-23.6-3.4c-6.2-1.3-12-3.9-18.2-5.3c-8.5-1.9-11.1-0.1-12,8.4
                        c-0.8,7.3-1.8,14.6-1.6,21.9c1.1,31.9,2.4,63.9,4.2,95.8c2.9,52.9,6.8,105.7,9.2,158.6c2,45.4,9.5,89.3,29.2,130.6
                        c11.3,23.7,21.9,47.8,32.8,71.7C2445.3,2054.2,2447,2057.3,2448.6,2060.5z M2256.1,1503.1c-0.7,7.9-1.4,14.5-1.8,21.1
                        c-1.3,22.6-3,45.2-3.7,67.8c-1.1,32.3-2.4,64.6-2,96.9c0.5,39,2.8,77.9,4,116.9c1.1,35.6,2.1,71.3,9.9,106.3
                        c8.4,37.7,16.9,75.3,25.9,112.9c7.1,29.5,16,58.4,27.9,86.3c2.4,5.6,5.5,10.8,8.2,16.2c0.7-0.2,1.5-0.4,2.2-0.6
                        c0.3-3.6,0.8-7.1,0.9-10.7c0.3-16,0.5-32,0.7-48c0.1-16,5.1-30,16.2-41.8c12.3-13,20.7-28.4,26.9-45.2c4.4-11.8,5.7-23.9,4.3-36.3
                        c-2.2-18.9-4.8-37.7-6.5-56.6c-2.8-31.2-5.1-62.4-7.3-93.6c-2.5-35.6-3.9-71.2-7-106.7c-3-34.8-7.6-69.5-11-104.3
                        c-2.7-27.6-16-46.5-42.4-56.2c-13.2-4.8-25.6-11.1-36.8-19.8C2262.7,1506,2259.8,1505.1,2256.1,1503.1z M2335.5,1245.4
                        c-10.8-0.4-14,1.7-17.5,11.1c-1.2,3.1-2.4,6.2-3.3,9.4c-7.2,23.3-16.3,45.7-27.6,67.3c-13.8,26.3-30.2,50.9-48.1,74.6
                        c-12.3,16.2-12.7,27.6-2.2,45c9.2,15.3,21.1,28.2,35.3,39.1c38.9,29.9,83.3,48.1,130.4,60.4c1,0.3,1.9,0.5,2.9,0.7
                        c7.6,1.1,10.7-1.2,10.8-8.8c0.1-4.6-0.5-9.3-0.9-13.9c-1.2-13.3-2.9-26.5-3.7-39.7c-1.5-25.9,2-51.1,10.7-75.6
                        c5.5-15.3,14.8-27.4,29.2-35.3c2.9-1.6,5.8-3.3,8.5-5.2c6.5-4.5,7.8-6.8,4.3-14.2c-7.1-15-14-30.2-22.7-44.3
                        c-14.4-23.2-33.8-41.9-57.9-55.3C2368.2,1252.2,2351.9,1246.8,2335.5,1245.4z M2351.9,2768c-0.2,14.2,3.4,27.6,7.6,41
                        c3,9.6,9.5,13.4,18.9,15c17.6,3,34.5-0.1,51.3-4c11.5-2.6,20.8-8.9,27.1-19c9.3-15,22.9-23.8,39.5-28.6c9.3-2.7,18.5-5.4,27.6-8.6
                        c11.3-3.9,20.7-10.5,27.3-20.8c6.4-9.9,7.5-20.3,3.3-31.4c-7.8-20.5-22.8-32.1-44.2-33.7c-16.5-1.2-33.3-0.5-49.8,0.6
                        c-10.6,0.7-18.3-2-23-11.7c-6.6-13.4-13.7-26.6-19.5-40.3c-7.6-18-21.8-27.8-41.2-26.8c-9.4,0.5-17.2,4.3-20.9,13
                        c-3.6,8.5-7.1,17.5-7.7,26.6c-1,13.9,0.2,27.9,1.1,41.9c1.3,20.3,3.4,40.5,4.4,60.7C2354.1,2750.7,2352.6,2759.4,2351.9,2768z
                        M2344.1,2053.4c-0.4,7-0.9,11.6-0.8,16.2c0.2,19,0.5,37.9,0.7,56.9c0.4,26.2-4.3,51.5-13,76.3c-11.8,33.5-19.8,67.7-17.1,103.6
                        c2,27,10.1,51.5,28.6,71.8c8.9,9.7,15.2,10.4,26.4,3.5c14.2-8.7,23.2-21.7,29.1-37c7.9-20.4,11.1-41.7,11.4-63.5
                        c0.8-62.2-5.9-123.5-21.6-183.8c-4.2-16.1-12.7-29.3-27.3-37.8C2356,2057.2,2350.8,2056,2344.1,2053.4z M2342.7,2392.5
                        c0.7,5.2,1,8.5,1.6,11.7c6.1,30.3,12.2,60.7,18.5,91c5.9,28.3,6.2,56.2-3.9,83.7c-1,2.8-1.2,6-2,10.2c4.7-1.2,7.5-2.2,10.4-2.5
                        c7.2-0.8,14.6-2.2,21.7-1.5c15.2,1.6,26.2,10.5,35,22.6c2.8,3.8,4.9,8.3,9.4,11.7c0.2-2,0.5-3,0.4-4c-0.3-2.6-0.7-5.3-1.1-7.9
                        c-3.1-20.6-4.8-41.3,0.2-61.6c13.6-55.2,27.9-110.3,42.2-165.4c3.9-15.1,9.4-29.8,13.9-44.8c1.6-5.2,3.9-10.6,0.3-16.8
                        c-10.6,7.7-16.7,17.7-19.9,29.5c-8.7,32.1-17.4,64.2-25.7,96.5c-5,19.7-9.2,39.5-13.7,59.4c-1,4.2-1.8,8.3-8.3,9.4
                        c2.5-14.8,4.3-29,7.3-42.9c8.3-39.4,17.1-78.7,25.7-118c0.6-2.7,0.8-5.6,1.3-9.2c-4.1,0.6-7,1.1-9.9,1.5c-7.6,1-15.1,2.5-22.7,2.8
                        c-11.3,0.4-11.4,0-15.9,10.8c-6.1,14.6-15.8,26.1-29.4,34.3c-7.8,4.7-16.1,6.1-24.9,3C2350.4,2394.8,2347.2,2393.9,2342.7,2392.5z
                        M2415.4,2030.6c-7.6,5.5-10.8,12.6-11.5,20.7c-0.6,7.6-1.1,15.4-0.3,22.9c2.7,25.2,5.6,50.3,9,75.3c6.5,47.2,11.5,94.5,13.1,142.2
                        c0.2,5,0.6,10,1.7,14.8c2.4,11.2,7.3,15,18.7,15.3c22.8,0.6,49.1-17.6,50.9-45.3c1.5-23.4,1.1-46.8-4.3-69.6
                        c-13.6-57-34.5-111.1-66.2-160.8C2423.2,2041,2419.3,2036,2415.4,2030.6z M2468,1322.3c0.6-0.3,1.3-0.6,1.9-0.9
                        c0-3.4,0.6-7-0.1-10.3c-4.6-21.8-9.1-43.6-14.3-65.2c-4.8-19.7-13.4-37.5-27.7-52.2c-12.9-13.3-28.3-22.3-46.9-23.8
                        c-5.3-0.4-11.6,0.6-16.1,3.1c-10.9,6.2-18.9,15.6-23,27.8c-2.3,6.9-4.7,13.8-6.9,20.8c-3.6,11.5-2.7,13.1,8.7,16.1
                        c12.9,3.3,25.8,6.5,38.6,9.9c29.7,7.9,52.3,25.3,68.6,51.2c3.5,5.6,7.1,11.2,11,16.6C2463.4,1318,2465.8,1320.1,2468,1322.3z
                        M2383.6,2058c6.5-14.6,12.5-27.8,18.4-41.1c2-4.6,1.3-9.1-1-13.4c-3.7-7-7.3-14.1-11.2-21.1c-1-1.8-2.9-3.1-4.7-5
                        c-2.5,5.7-4.3,10.7-6.7,15.4c-5.9,11.9-12.1,23.6-18.1,35.4c-3.5,6.9-3.4,8,2.3,12.7C2369.2,2046.5,2376,2051.8,2383.6,2058z
                        M2432.5,1123.7c-10.6,12.6-19.6,23.3-29.1,34.7c7.8,5.8,14.5,11,21.6,16c7.2,5,11.5,13.7,22.4,17.1
                        C2444.5,1168.2,2438.9,1147,2432.5,1123.7z"/>
                    <path d="M2212.6,976.3c-0.2-12.2-3.6-23.6-7.8-34.8c-6.4-17.3-15.7-33-26.2-48.1c-25.6-37-50.9-74.2-77-110.8
                        c-14.7-20.7-24.3-43.3-29.8-68c-4.6-20.8-10.4-41.3-16.6-61.6c-5.9-19.5-18.1-33.9-37.6-41.3c-3.1-1.2-7.2-2.9-5.8-6.8
                        c0.9-2.4,4.5-4.7,7.4-5.4c21.4-5.8,42.9-11.1,64.7-16.7c-1.8-1.1-3.6-2.2-6.3-3.9c3.7-2.7,6.8-5.2,10.1-7.2
                        c9.9-6.1,19.9-12.2,30-17.9c13.8-7.8,21.8-19.2,23.5-35c1.1-9.9,1.8-19.9,3.1-29.8c0.8-6-0.7-10.2-6.5-13
                        c-8.8-4.3-13.1-12-14.2-21.5c-1.3-11.6-2.4-23.2-3.7-34.8c-0.4-3.3-0.9-6.6-1.2-9.9c-0.5-4.8-3-7.2-7.8-7.4
                        c-8.1-0.3-12.5-5.1-15.6-12c-5.3-11.8-11.2-23.4-16.4-35.3c-6.4-14.9-1-26.1,14.4-31.1c0.6-0.2,1.3-0.4,1.9-0.6
                        c9.4-3,9.9-3.7,9.1-13.4c-1.9-24,1.4-47.2,9.6-69.7c18.7-51.4,66.5-82,120.1-77.4c61.6,5.3,98.2,53.5,105.4,97.8
                        c2.6,16,3.2,32.4,3.7,48.7c0.2,7,2.2,11,8.9,13.5c4.9,1.8,9.4,4.9,13.5,8.3c6.4,5.2,8.2,12.4,5,19.8c-6.4,14.6-13.3,29-20.3,43.4
                        c-2.5,5.1-7.2,7.8-12.9,8c-5.8,0.2-8.1,3.5-8.6,8.9c-0.9,9.6-1.9,19.2-3,28.8c-0.6,5.3-1.1,10.6-1.9,15.9
                        c-1.3,8.7-5.4,15.9-13.4,19.9c-6.1,3-7.8,7.3-7,13.8c1.4,10.6,2.2,21.2,3.5,31.8c1.8,14.2,9.4,24.6,21.7,31.8
                        c11.2,6.6,22.3,13.3,33.5,20c2.5,1.5,4.8,3.4,8.6,6.1c-4.4,1.9-7.4,3.2-11.2,4.8c25.6,3.8,49.9,10.2,73.6,18.9
                        c-0.1,5.4-2.8,7.7-6.3,9.2c-22.6,9.9-34.7,28.2-40.7,51.2c-4.2,16.1-8.4,32.2-11.9,48.5c-6.2,28.7-17.5,55.1-34.4,79.1
                        c-25.6,36.2-51.2,72.3-76.8,108.5c-15.1,21.4-26.8,44.4-31.6,70.5c-0.4,2.4-1.9,4.7-3.4,7.9c-3.6-3.8-2.9-7.6-3-10.9
                        c-0.5-36-1.3-72-1.2-107.9c0.2-45,0.7-90,2.2-134.9c0.6-19.6,3.3-39.2,6.5-58.5c1.4-8.5,6.4-16.3,9.7-24.5c3-7.4,5.8-14.9,9.2-22.1
                        c9.6-20.7,26-31.3,48.7-32.6c5.5-0.3,11-1.4,16.4-3c-24.9-2.8-47,2.6-63.3,23.2c-10.9,13.8-17.6,29.7-21.7,46.8
                        c-1.8,7.8-3.8,15.5-5.9,23.2c-0.5,1.8-1.6,3.4-4.4,5.2c-1-5.8-2.3-11.5-3.1-17.3c-2.5-17.8-8.5-34.2-17.9-49.4
                        c-15.2-24.8-40.2-36.3-69.7-31c5.5,0.8,10.9,2,16.5,2.3c23.7,1.2,40.2,12.6,49.8,34.1c13.7,30.4,21.8,62.1,22.2,95.6
                        c0.6,53.3,1.1,106.6,1.6,159.9c0.2,19.3,0.6,38.6,0.8,58c0.1,6,0.2,12-0.3,18c-1,12.2-2.5,24.4-4.1,36.5
                        c-1.5,11.6,2.4,23.8,2.5,35.7c0.6,56.3,0.8,112.6,1.1,169c0.3,59.3,0.5,118.6,0.7,178c0,3.3,0.2,6.7-0.5,10c-1.4,7-6.2,8.4-11.5,3.4
                        c-1.9-1.8-3.5-4-5.1-6.2c-14-18.9-24.7-39.4-32.4-61.6c-10-28.6-20-57.2-30.1-85.7c-6-17-13.3-33.4-23.7-48.3
                        c-7.6-10.9-16.9-19.6-29.9-23.9c-6.8-2.3-7.6-3.8-7.3-12.1c1.5-36.9,10.3-71.8,29.4-103.6c6.2-10.2,13.3-20,21-29.1
                        c14.4-17.1,29.4-33.6,44.3-50.2c3.5-3.9,7.4-7.8,11.7-10.7c7.7-5.3,12.8-3.8,16.9,4.5C2207.5,963.4,2209.9,969.9,2212.6,976.3z
                        M2307.5,468.3c8.4-3.4,9.6-4.3,10.7-12.2c2.2-15.5,4-31,5.8-46.6c1.2-10.5,1.1-10.7,11.3-12.5c5.7-1,9.9-3.8,12.4-9
                        c5.9-12.3,11.7-24.7,17.4-37c2.1-4.5,2.6-9.4-0.8-13.2c-3.2-3.6-7.1-7.3-11.4-9.2c-4.3-1.8-9.5-1.4-15-2.1c0-4.2-0.1-9.6,0-14.9
                        c0.4-20.7-0.3-41.3-6-61.4c-15.9-56.1-70.7-91-127.8-80.4c-43.1,8-70.9,34.5-85.9,75c-8,21.6-9.3,44.1-8.4,66.9
                        c0.5,12.6,0.1,13-12.3,16.2c-13.7,3.6-18.5,12.5-13.1,25.5c4.5,10.7,9.8,21.1,14.5,31.8c3.1,7.1,7.8,11.1,15.7,12
                        c7.7,0.9,8.1,1.7,9.1,9.2c0.9,6.9,1.5,13.9,2.4,20.8c1.2,9.9,2.3,19.8,3.9,29.7c1.2,7.6,3.1,9.1,10.8,10.6c0.4-1.5,0.9-3.1,1.2-4.6
                        c3.9-17.5,7.5-35.1,11.7-52.6c3.1-13.1,10.8-23.4,21.6-31.4c20.1-15,42.4-20.7,66.9-14.3c27.7,7.2,47.9,22.6,54,52.4
                        C2299.7,433.8,2303.6,450.6,2307.5,468.3z M2212.9,945.6c0.6,0,1.2-0.1,1.8-0.1c0.4-4.1,1.2-8.1,1.2-12.2
                        c-0.3-44.3-0.5-88.6-1.2-132.9c-0.5-34.6-1.2-69.2-2.8-103.8c-1.3-28.7-10.1-55.5-23.4-80.9c-5.5-10.5-13.5-18.6-25.2-21.3
                        c-22.5-5.1-45.4-8.9-68.3-5.4c-22.9,3.6-45.6,9.3-68.3,14.1c-1.4,0.3-2.8,1.1-5.2,2.1c2.4,2,3.7,3.5,5.3,4.4
                        c19.8,10.6,30.8,28,36.8,48.9c3.2,11.2,6.2,22.5,8.2,33.9c6.6,36.6,20.1,69.9,42.6,100c22.1,29.6,42.6,60.4,63.8,90.7
                        c12.8,18.3,24.4,37.3,32.5,58.3C2211.2,942.8,2212.1,944.2,2212.9,945.6z M2230,944.1c0.6,0.1,1.2,0.3,1.8,0.4c0.7-1,1.6-2,2-3.2
                        c9.4-23.4,22.9-44.3,37.7-64.6c18.5-25.3,36.9-50.6,55.4-75.9c21.3-29.1,36.4-61,44.1-96.4c3.8-17.5,8.6-34.9,13.4-52.2
                        c5-17.7,15.5-31.5,31.5-41c2.4-1.4,4.5-3.3,8.5-6.3c-10.8-3.1-19.6-5.6-28.4-8.2c-35.6-10.5-71.5-11.1-107.7-3.3
                        c-11.8,2.6-21.5,8.5-26.8,19c-8.3,16.5-16,33.3-22.6,50.6c-3.3,8.5-4.4,18.2-4.9,27.4c-1.5,25.6-2.7,51.2-3.1,76.9
                        c-0.8,56.3-1.1,112.6-1.5,169C2229.4,938.9,2229.8,941.5,2230,944.1z M2212.9,1378.9c0.8-0.2,1.5-0.3,2.3-0.5
                        c0.2-3.2,0.5-6.4,0.5-9.5c0-21,0.1-42,0-62.9c-0.5-96.2-1.1-192.5-1.6-288.7c-0.1-19.3-5.2-37.5-13.3-54.9
                        c-3.6-7.8-6.9-8.5-13.5-2.9c-1.3,1.1-2.4,2.3-3.5,3.5c-15.8,17.6-31.9,35-47.4,52.8c-31.2,35.7-45.8,78.2-50.3,124.7
                        c-1,9.9-0.1,11.3,9.2,14.9c9.6,3.7,17.5,9.5,23.1,18.2c12.5,19.3,23.5,39.4,30.9,61.3c6.1,18.3,12.4,36.5,19.1,54.6
                        c6.8,18.4,13.7,36.8,21.5,54.7C2195.6,1357.1,2203.2,1368.8,2212.9,1378.9z M2162.4,512.4c-0.8-0.3-1.6-0.6-2.4-0.9
                        c4.6-34.7,11.4-69.2,8.9-104.8c-3.2,2.8-5.4,5.8-7.1,9.2c-7.4,14.6-8.6,30.6-9.9,46.6c-1.5,18.6-3.2,37.2-5,55.7
                        c-1.9,18.6-11.4,32.3-27.8,41.2c-4,2.2-7.8,4.7-12.2,7.4c1.6,0.7,2.2,1.1,2.7,1.1c1.6-0.2,3.3-0.6,4.9-1c24.1-5.4,48.4-9.6,73-10.6
                        c8.3-0.3,12.9-3.5,16.1-10.5c6.2-13.5,8.8-27.9,10.5-42.5c3.8-32.6,2.5-65.2,1.4-97.8c-0.3-9.2-1.4-18.3-2.2-27.4
                        c-20.2,0.4-28.4,7.9-29.9,26.5c-0.4,5-0.8,10-1.1,14.9c-1.8,27-7,53.4-14.3,79.5C2166.6,503.6,2164.3,507.9,2162.4,512.4z
                        M2339.8,567.9c0.2-0.6,0.4-1.1,0.6-1.7c-4.8-2.9-9.6-5.7-14.4-8.7c-13.6-8.3-22-20.1-24.3-36c-0.7-4.6-1.2-9.2-1.6-13.9
                        c-2-21.6-3.8-43.1-5.9-64.7c-1.1-11.4-4.9-22.1-10.4-32.1c-1-1.7-2.8-3-5.9-6.1c-1.2,37.6,5.7,72.2,10.4,109.2
                        c-6.9-7-8.1-14.3-9.9-21.2c-7.8-29.4-12.1-59.3-13.9-89.7c-1-17.4-10.8-25.4-29.8-25c-0.4,6.2-0.9,12.4-1.3,18.6
                        c-2.3,36.3-3.2,72.6,1,108.8c1.6,14.2,4.3,28.3,10.9,41.3c2.9,5.9,6.7,8.8,13.8,9.2c15.3,1,30.5,2.9,45.6,5.1
                        C2316.4,563.1,2328.1,565.7,2339.8,567.9z M2224.8,481.1c-0.6,0-1.2,0-1.8,0c-0.3,3.3-0.4,6.5-0.8,9.7c-1.4,12.9-2.5,25.9-4.5,38.7
                        c-1.2,7.5-3.9,14.8-6.2,22c-1.6,4.8-5.3,7.8-10.3,8.3c-14.9,1.5-29.8,3.1-44.7,4.2c-19.1,1.4-37.6,5.7-55.7,11.8
                        c7.2-0.6,14.1-2.6,21.2-3.6c23.7-3.3,47.5-6.3,71.3-9.3c11.9-1.5,15.3,0.3,20.5,11.2c6.5,13.8,8.9,28.5,8.6,43.7
                        c-0.1,6.4,0,12.9,0,19.3c0.9,0,1.9,0,2.8,0c0.2-6.8,0.5-13.5,0.5-20.3c0.1-15.9,2.7-31.1,10-45.4c3.5-6.8,8.6-9.7,16.2-8.9
                        c29.7,3.4,59.8,4.6,88.8,13.1c2,0.6,4.4,0.3,6.5,0.4c-25.6-8.9-62-14.9-95.3-15.8c-9.2-0.3-14.7-4-16.9-12.4
                        c-2.7-10.6-5.2-21.3-6.8-32.2C2226.4,504.2,2225.8,492.6,2224.8,481.1z M2323.1,576c-0.1-0.8-0.3-1.6-0.4-2.3
                        c-21.8-1.9-43.6-3.9-65.5-5.5c-12-0.9-14.9,1.2-19.5,12.5c-5.1,12.5-6.7,25.7-7.7,39c-0.4,4.7-0.1,9.5-0.1,14.3
                        c9.8-17.2,18.2-34.9,35.3-46.2C2282.9,576,2303.1,576.8,2323.1,576z M2217.6,634c1-17.3-0.9-34-6-50.3c-1-3.1-2.6-6.1-4.2-9
                        c-2.7-4.6-6.4-7.2-12.3-6.7c-13.6,1.2-27.2,1.8-40.8,3c-9.8,0.9-19.6,2.3-29.4,3.5c19.5,2.4,39.4,1.5,56.9,12.6
                        C2199.4,598.2,2208.1,616.1,2217.6,634z M2270.5,413.4c0.4,0,0.8,0,1.2,0.1c0.5-2.6,1.1-5.1,1.4-7.7c0.5-4,0.5-8,1.1-11.9
                        c0.8-5.2-1.5-8.8-5.4-12c-23.4-19.3-66.1-19.4-89.5,0.1c-2.2,1.8-4.9,4.4-5.2,6.9c-0.9,8.2,0.1,16.3,2.8,24.1
                        c1.2-5.7,2-11.3,2.5-16.9c0.8-8.4,5.2-14.2,12.7-17.8c21.2-10,42.4-10,63.6,0c7.5,3.5,11.9,9.3,12.7,17.7
                        C2269.1,401.8,2269.8,407.6,2270.5,413.4z M2223.9,415c2.9-13.8,2.9-32.9,0.7-38.6C2221.1,379.9,2220.6,395.2,2223.9,415z"/>
                    <path d="M2023.4,1442.9c5.8,37.2,0.8,73.7-7.9,109.9c-0.3,1.3-0.8,2.5-1.1,3.9c-1.2,7.2,0.9,10.4,8.1,10.5c5.6,0,11.3-1,16.7-2.3
                        c8.1-2,15.9-4.9,24-6.8c11.3-2.7,15.2,0.3,16.6,12c3.1,24.6,3,49.3,1.4,73.9c-3.2,50.9-7.3,101.7-10.6,152.6
                        c-1.4,21.9-1.5,44-2.8,65.9c-2.5,41.6-11.6,81.6-29.2,119.6c-10.2,22-20.2,44.1-28.9,67.1c3.1-4.6,6.1-9.3,9.2-13.9
                        c8.8-13.1,15.5-12.7,23,1.2c4.2,7.8,6.2,16.1,5.2,25c-4.9,43.3-10.3,86.6-14.6,130c-3.5,35.1-5.8,70.4-8.7,105.6
                        c-0.3,3.6-0.5,7.3-1.3,10.9c-3.2,14.3-12.2,21-26.8,18.9c-7.5-1.1-14.7-4-21.9-6.3c-3-1-5.9-2.6-10.3-4.6
                        c6.5,10.8,12.2,20.2,18.2,30.1c0.3-3.3,0.5-6.1,0.8-10.1c4.8,1,8.9,2,13.1,2.8c6.8,1.3,13.9,4.2,20.5,3.5c15-1.5,23.4,3.7,28.1,18.4
                        c3.7,11.6,12.9,20.1,23.2,26.7c3,1.9,6.3,3.2,10.6,3.1c-1.5-1.4-2.7-3-4.4-4c-15.3-9.6-25.3-23.3-31.7-40
                        c-9.9-26-12.4-53.1-11.7-80.6c1.4-52,6-103.7,17.6-154.6c1.3-5.8,2.7-11.7,4.4-17.4c5.5-19.5,18-33,35.9-41.9
                        c2.4-1.2,4.9-2.2,7.4-2.8c5.7-1.4,9.3,0.3,10.5,6.1c1.1,5.5,1.4,11.2,1.2,16.8c-0.3,15.3-0.9,30.6-1.6,46
                        c-1.3,28.7,4.1,56.3,13.4,83.3c10.7,31,17.9,62.7,17.6,95.8c-0.2,24.2-5.2,46.8-18.2,67.6c-6.3,10-9,21.4-11.2,32.9
                        c-7.2,36.9-14.5,73.8-21.9,110.7c-4.5,22.1-3,43.7,4.2,65c1.9,5.7,3.5,11.5,5,17.3c0.8,3.1,0.9,6.4,1.6,11.6
                        c-13.6-7.7-25.8-15.5-40.5-11.6c-6.6,1.8-13.6,4.3-18.9,8.4c-5.9,4.5-10.7,10.9-15,17.1c-4.5,6.6-7.2,14.1-6.2,23
                        c2.5-5.7,5.3-11.3,7.5-17.1c7.5-19.1,29.1-30.9,49.2-27c11,2.1,18.6,8.5,23.4,18.4c7,14.5,9.3,29.7,7.6,45.7
                        c-2.2,20.5-4.7,41-6.4,61.6c-0.7,8.6-0.3,17.3,0.3,25.9c1.6,20.8,0.3,41.2-5.5,61.3c-3.9,13.6-12.4,20.8-26.4,21.8
                        c-19.1,1.4-37.9,0.4-56.3-5.8c-9.8-3.3-17.7-8.5-23.4-17.5c-10.3-16.3-25.9-25.3-44.1-30.7c-10.2-3-20.5-6-30.1-10.5
                        c-6.7-3.2-13.3-8-18.2-13.5c-13.2-14.9-14.4-31.6-4.8-49c10.1-18.2,25.6-29.1,46.3-30.6c14.9-1.1,29.9-0.5,44.9,0.5
                        c17.5,1.1,19,0.8,26.4-16.2c-6.8-6.7-4.5-15.1-3.5-23.3c1.7-12.5,3.4-25.1,5.2-37.6c3.3-22.5,0-44.5-5-66.4
                        c-14.5-63.3-30.7-126.1-51-187.8c-4.2-12.7-7-25.7-4.6-39.3c0.3-1.9,0.3-4-0.3-5.9c-9.8-31.3-7-62.6,0.7-93.7
                        c10.5-42.8,24.5-84.4,45.2-123.5c0.6-1.2,1-2.4,1.5-3.6c-13.4-20.9-12.4-44.5-12.7-67.6c-0.2-12.6,1.5-25.3,2.3-37.9
                        c2.2-31.1-1.2-61.8-6.6-92.4c-6.9-39.7-13.2-79.5-19.3-119.3c-3.9-26-7.5-52.1-10.2-78.2c-4.7-44.4-5.7-89.1-6-133.8
                        c-0.3-39,1-78,6.3-116.7c1.4-10.6,3-21.1,5.3-31.5c2.6-12.1,8.7-16,21-14.2c12.5,1.8,22.2,8.5,30.1,18c8.9,10.8,13.9,23.3,17.3,36.7
                        c0.4,1.5,1.1,3,1.7,4.5L2023.4,1442.9z M2072.7,1561.8c-5.8,1.3-9.7,2-13.6,3c-4.8,1.2-9.6,2.9-14.4,4c-6.5,1.6-12.9,3.4-19.5,4.1
                        c-12,1.2-17.6-4.8-15.8-16.7c0.8-5.6,2.3-11.1,3.5-16.6c6.7-30.5,9.8-61.2,5.1-92.2c-2.5-16.5-8.6-31.4-19.6-44.1
                        c-6.8-7.8-15.3-12.8-25.7-14c-7.8-0.9-12,1.7-14.2,9.3c-1.2,4.1-1.8,8.5-2.4,12.7c-6.6,42.6-8.6,85.5-8.8,128.5
                        c-0.3,59,2.4,117.9,10.5,176.4c6.7,48.1,14.2,96.1,21.7,144.2c2.9,18.7,6.8,37.3,10.2,56c3.6,19.6,0.7,39.2-0.8,58.8
                        c-1.9,25-2.6,49.9,1,74.8c0.6,4.2,1,8.5,5.6,11.7c1.6-2.9,3.3-5.4,4.5-8c12.7-27.9,25.6-55.6,37.9-83.6c12.1-27.3,20-55.8,22.3-85.6
                        c1.9-23.9,2.6-47.9,4.2-71.8c3.6-55.2,7.8-110.3,11-165.5c1.2-21.2,0.6-42.6,0.3-63.9C2075.8,1576.3,2073.9,1569.5,2072.7,1561.8z
                        M2098.5,2650.7c0.1-12.6-1.8-24.6-8-35.7c-5.8-10.5-14.5-16.2-26.9-15.1c-17.4,1.6-30.4,9.3-36.3,26.5
                        c-5.2,15.2-12.6,29.3-21.1,42.8c-4.8,7.6-11,10.2-20.1,9.6c-16.9-1.1-33.9-1.8-50.8-1c-21.3,1.1-35.6,13.7-44.3,32.5
                        c-5.8,12.5-4.4,25.2,4.9,35.4c5.7,6.3,12.8,11.8,20.3,15.7c7.8,4.1,16.8,6.2,25.4,8.6c20.5,5.8,37.8,16.4,49.8,34.4
                        c4.7,7,11,11.2,18.8,14c18.3,6.4,36.9,8.2,56.1,5.8c10.1-1.2,16.3-6.1,19.5-15.8c5.3-16.4,6.8-33.1,6.7-50.2
                        c-0.1-20,0.4-39.9,1.5-59.9C2094.8,2682.4,2096.9,2666.6,2098.5,2650.7z M2101.3,2055c-10.5,0.3-17.9,4.5-24.5,10.3
                        c-7.9,6.9-13.8,15.4-17.2,25.4c-3,8.8-5.6,17.8-7.6,26.8c-12.6,55.8-16.9,112.5-16.3,169.6c0.2,21.3,4.7,41.6,12.6,61.2
                        c5.5,13.6,14.1,24.8,26.6,32.8c12.4,8,19.1,6.9,28.5-4.1c18.6-21.7,26.7-47.2,27.8-75.3c1.3-30.7-4.6-60.1-14.2-89.2
                        c-5.3-16-9.6-32.5-13.1-49c-4.7-22-3-44.4-2.7-66.7C2101.4,2083.1,2101.3,2069.5,2101.3,2055z M2086.9,2589.3c0.6-1.5,1.2-2.2,1-2.7
                        c-0.9-3.5-1.7-7.1-2.9-10.5c-7.7-22.2-9.6-44.9-5.2-67.9c6.2-32.7,13-65.2,19.4-97.9c1.1-5.7,1.8-11.4,2.8-17.7
                        c-2.3,0.5-3.4,0.6-4.3,1c-16.6,7.4-30.3,2.5-42.7-9.6c-8.2-8-14.9-17.1-18.7-28c-2.1-5.9-5.7-8.3-12-8.3c-5.3,0-10.6-1.1-15.9-1.7
                        c-6.5-0.8-12.9-1.7-20.2-2.6c1.1,5.6,1.9,9.8,2.8,14c9.9,47.2,19.8,94.4,29.7,141.6c0.9,4.4,2.2,8.8,0.6,13.7
                        c-6.5-1.9-6.2-7.1-7.3-11.4c-12.6-50-25.1-100-37.7-150c-2.9-11.6-8.7-21.5-17.1-29.9c-1.5-1.5-3.5-2.4-7.4-5.1c2,9.1,2.9,16,5,22.5
                        c18.8,57.2,33.8,115.5,48,174c7.4,30.6,12.2,61.4,7.1,93c-0.6,4-1.2,8-1.8,12C2029.3,2589.7,2053.2,2576.1,2086.9,2589.3z
                        M2029.4,2030.2c-1,1.1-1.5,1.5-1.9,2.1c-30.3,47.7-55,97.9-69.9,152.8c-8.1,29.7-11.8,59.3-9.8,89.8c1.8,27,24.4,47.9,51.2,47.1
                        c11.4-0.3,17.2-5.2,18.8-16.6c0.9-6.6,1.5-13.3,1.7-19.9c1.9-58,10.1-115.4,17.5-172.8c2.4-18.8,3.2-37.8,4.2-56.7
                        C2041.9,2045.7,2038.1,2037.1,2029.4,2030.2z"/>
                    <path d="M1798.8,1032.6c-3.8-31.4-3.6-62.5,7.4-92.7c2.8-7.7,6.4-14.7,15.5-18.6c-16.2-13.5-12.4-30.7-9.1-47.2
                        c4.7-23.6,13.9-45.8,24.5-67.4c3.9-7.8,7-9.1,16.3-7.7c17.7,2.5,35.1,1.2,51.9-3.1c-10.5,0-21.3,0.1-32-0.1
                        c-5.3-0.1-10.7-0.5-15.9-1.3c-8.5-1.2-10.4-3.3-9.4-12c1.9-16.2,4.4-32.3,7-48.4c6.1-38.8,27.1-68,58.5-90.6
                        c19.9-14.3,41.5-25.1,64.6-33.2c11.3-4,22.2-2.9,32.8,2.8c22.1,11.9,36.1,30.1,42.5,54.2c3.2,12.2,1.3,17.4-9.4,24.5
                        c-22.6,15.2-41.5,34.4-60.1,54c-17.3,18.3-36,34.7-60.6,45c2.3,1,3.8,1.7,5.7,2.5c-0.3,14.1-10,24.8-13.7,37.9
                        c1.5-1.8,3.1-3.6,4.4-5.5c8.1-11.8,16.1-23.7,24.4-35.4c2.7-3.8,5.8-7.5,9.4-10.2c7.4-5.5,16.5-2.2,18.8,6.7
                        c1.2,4.4,1.3,9.2,1.5,13.8c0.2,7.8,0.1,15.6,1.5,23.5c0.6-6.6,0.8-13.3,1.9-19.8c7.4-44.8,30.6-79.5,67.3-105.6
                        c2.1-1.5,4.7-2.6,7.2-3.5c6.9-2.4,11.6-0.1,14.4,6.6c0.8,1.8,1.3,3.8,1.6,5.8c4.4,31.8,17.4,60.1,36,85.7
                        c26.1,35.9,48.3,74.2,73.4,110.7c3.6,5.2,6.6,10.8,9.7,16.3c4.5,8.1,4.1,16.2-1.2,23.6c-21,29.2-45.3,55.1-77.7,72.1
                        c-17.5,9.2-35.6,9.7-54,2.3c-13.7-5.5-25.7-13.5-36.8-25.1c5.5,17.8,20.9,30.6,38.6,32.8c16.1,2,31.9,0.7,47.3-4.7
                        c3.4-1.2,6.8-2.1,12.2-3.6c-1.3,4.5-1.7,7.6-3.1,10.3c-17.7,35.4-29.5,72.7-36.6,111.6c-0.2,1.3-0.7,2.6-1,3.9
                        c-3.3,13.9-7.6,15.2-18.5,5.4c-17.5-15.7-28.4-36-37.9-57c-12.1-26.6-18.9-54.7-23.9-83.3c-2.2-12.5-4-25-6.2-37.4
                        c-1.1-6.2-1.1-12,3.9-17.2c-11.4-24.9-15.7-51.4-19.1-78.3c-0.8,4.9-1.5,9.9-2.4,14.8c-5.7,31.3-22.5,55.9-46.4,76
                        c-11.8,9.9-23.6,6.7-29.3-7.7c-7.1-18-6.4-36.6-3.7-55.2c2.1-14.1,5.3-28,7.8-42.4c-10.7,19.4-26.7,33.9-43,48.8
                        c8.4-3.9,16.7-7.7,26.3-12.1c0.4,3.9,0.7,6.7,0.8,9.6c0.3,11.3,0.4,22.6,0.8,34c0.5,13.9,7.8,23.6,19.1,30.9
                        c6.5,4.2,12.7,4.3,18.4-0.5c7.2-5.9,13.9-12.4,20.7-18.7c2.8-2.6,5.4-7.4,9.8-4.1c4.1,3.1,1.1,7.5-0.8,10.9
                        c-13.3,23.9-26.6,47.7-40.3,71.3c-5.8,10-14.4,17.9-24.3,23.3c-6.4,3.5-14.6,4.2-22.1,5.1c-12,1.3-23.9,0.5-35.4-3.9
                        c-4.3-1.7-8.9-2.9-13.4-3.9c-6.5-1.4-11.7-4.5-15.5-9.9C1799.6,1038.8,1799.2,1035.7,1798.8,1032.6z M1978.4,864
                        c1.7,14.3,3,31,5.8,47.3c5.1,29.8,17.4,56.5,37.7,79.3c27.6,30.9,64.2,35.4,98.2,12c20.5-14.1,37.7-31.7,53.1-51.1
                        c14.2-18,14.4-21.1,1.7-40.5c-25.6-39-51.3-77.9-77.1-116.7c-13.6-20.4-24.3-42.1-30.8-65.7c-1.8-6.4-3-13-4.7-19.4
                        c-2.6-10-5.9-11.2-14.8-5.7c-0.6,0.4-1.1,0.7-1.7,1.1c-32.1,23.1-54.2,53.5-61.4,92.6C1980.4,818.2,1980.4,840.1,1978.4,864z
                        M1853.5,775.9c0,0.9,0,3.5,0,6.2c-0.1,4.3,2,7.2,6.2,7.6c8.3,0.9,16.6,2.5,24.8,2c28-1.6,53.5-10.5,74.6-29.8
                        c10.8-9.9,21.1-20.3,31.4-30.8c15.9-16.2,32-32.1,51.4-44.3c6.2-3.9,8.2-8.9,6.5-16.1c-5.8-25-20.4-43.1-43.2-54.4
                        c-8.4-4.2-17.5-4.6-26.1-0.7c-16,7.3-32.2,14.5-47.8,22.7c-15.8,8.2-29.5,19.4-41,33.1C1865.4,701.2,1856.6,736.6,1853.5,775.9z
                        M1878.2,906.1c-5.1,2.2-8.1,3.3-11,4.8c-13.5,7.2-26.8,14.7-42.5,16.5c-6.3,0.7-9.9,5.3-12.1,10.8c-12.4,32-13.4,64.8-7.5,98.2
                        c0.8,4.8,3.4,7.8,8.1,9c13.6,3.3,27.1,6.8,40.7,9.6c4.4,0.9,9.3,0.7,13.9,0c17.5-2.4,29.9-12.7,38.6-27.3c11-18.2,21.1-37,31.6-55.5
                        c0.9-1.6,1.3-3.5,2.8-7.6c-6.8,4.7-11.5,8.1-16.4,11.2c-10,6.4-18,6.1-27.7-0.9c-12.5-9.1-18.2-21.7-18.6-36.9
                        C1878,928,1878.2,918.1,1878.2,906.1z M1968.2,832.2c0-10.3,0-20.6,0-30.9c0-3,0.1-6-0.3-9c-0.5-3.7-1.5-8-5.8-8.3
                        c-2.8-0.2-5.9,1.9-8.8,3.3c-1.1,0.5-1.8,1.9-2.6,3c-9.1,12.7-18.5,25.2-27.2,38.2c-17.3,25.6-27,54-28.3,84.9
                        c-0.6,15-0.9,30,4.7,44.3c4.1,10.6,11.9,12.8,20.3,5.5c21.5-18.7,38.2-40.8,44.1-69.4C1968.4,873.4,1968.4,852.8,1968.2,832.2z
                        M1997.8,978.1c-0.9,0.3-1.8,0.7-2.7,1c1.6,10.5,3,21,4.8,31.4c6.2,35.6,16.4,69.9,34.2,101.6c7.1,12.6,15.4,24.1,26.6,33.4
                        c1.9,1.6,4.1,2.8,6.8,4.7c7.9-42.9,19.6-83.5,38.8-123.4c-5.1,1-8.3,1.4-11.5,2.2c-14.4,3.5-28.9,4-43.6,1.4
                        c-13.6-2.4-23.9-9.5-31.5-20.8c-5.7-8.6-11.4-17.2-17.2-25.7C2001.2,981.8,1999.4,980,1997.8,978.1z M1922.2,799.5
                        c-4.4,0.2-6.7,0-8.9,0.5c-20.6,4.3-41.2,7.7-62.3,4.1c-5.2-0.9-7.9,2.1-9.8,6.5c-4.7,11.4-9.6,22.6-14.3,34
                        c-7.1,17.1-9.8,35.1-11.5,53.4c-0.6,6.9,1.2,12.5,7.4,16.2c6.4,3.8,12.2,2.1,17.6-2c7.5-5.6,14.8-11.3,22.3-16.9
                        c14.6-11,25.7-24.9,33.6-41.4c6.6-13.8,13.7-27.3,20.4-41.1C1918.6,809,1920,804.9,1922.2,799.5z"/>
                    <path d="M2642.1,1041.6c1.3-5.1,2.4-10.2,3.8-15.2c0.8-2.9,1.4-5.9,2.8-8.5c2.8-5.3,8.9-6.1,12.1-1.1
                        c23.1,35.9,46.5,71.7,51.1,115.8c0.4,3.6,1.5,7.2,1.7,10.8c0.2,3.3,1,7.7-0.7,9.7c-4.7,5.6-2.4,10.5-0.5,15.8
                        c5.1,14.6,7.3,29.7,7.4,45.1c0.2,19,0,38,0.1,57c0,3.6,0.6,7.1,2.8,10.7c0.1-2.3,0.3-4.7,0.3-7c0-11-0.4-22,0.2-33
                        c0.3-4.7,1.6-10,4.2-13.9c4.1-6.2,8.8-5.3,11.8,1.3c0.6,1.2,0.8,2.5,1.2,3.8c13.2,49.6,31.9,97.4,49.6,145.5
                        c1.4,3.7,2.8,7.5,3.9,11.3c2.4,8.4,2.3,8.4-2.6,15.6c29.9,3.8,50.8,21.8,69.6,43.4c3.9,4.5,7.2,9.6,11.1,14.2
                        c10,12,22,21.2,36.3,27.6c5.2,2.3,10.3,4.7,15.1,7.7c5.2,3.3,5.8,6.6,2.6,11.8c-5.1,8.3-12.3,13.1-22.5,11.8
                        c-18.2-2.2-36.5-4.3-52.3-14.9c-0.8-0.5-1.8-0.8-2.8-1c-0.6-0.1-1.3,0.2-2.2,0.3c-2.9,3.8,0.2,6.8,1.8,9.8
                        c10.2,19.5,20.6,38.8,30.6,58.4c3.6,7.1,6.5,14.6,9.6,21.9c0.9,2.1,1.6,4.4,1.9,6.7c0.9,6.8-1.9,12.3-6.9,14.1
                        c-4.2,1.5-8.4-0.3-12.4-6.2c-4.1-6.1-7.5-12.6-11.2-18.9c-1.4-2.4-2.9-4.8-6-6.5c2.5,4.9,4.9,9.8,7.4,14.7c4.4,8.6,9.3,17,13.2,25.8
                        c2,4.4,3.4,9.5,3.3,14.3c-0.3,11.3-10.1,16.7-19.6,10.6c-5.1-3.3-9-8.4-14.1-13.4c0,1.4-0.1,2.8,0,4.1c0.6,4.8-0.7,8.5-5.3,10.8
                        c-4.9,2.5-10.2,2.9-14-0.9c-4.9-4.8-9.8-10.2-12.9-16.2c-7.1-13.9-13.2-28.4-19.7-42.6c-1.5-3.2-3.2-6.4-6.7-9.1
                        c0.6,2.2,0.9,4.5,1.8,6.6c4.1,9.1,8.3,18.1,12.6,27.2c1.8,3.8,3.1,7.5-1.1,10.7c-3.7,2.9-9.1,3.2-12.8-0.4
                        c-4.2-4.2-8.3-8.8-11.2-13.9c-15.6-27.9-30.9-55.9-46.1-84.1c-9.2-17.1-12.6-35.6-11.8-55c0.4-11,0.2-22-0.2-33
                        c-0.4-9.8,3.2-16.4,12.5-20.3c15-6.3,29.2-14.7,46.1-17.4c-2-0.3-4-1-5.9-0.8c-11.8,0.7-23,3.7-33.7,8.4c-6.7,3-13.4,6-20.2,8.6
                        c-8.8,3.3-11.9,2.3-16.4-5.8c-7-12.5-13.8-25.1-20.2-37.9c-20.5-40.4-41.2-80.7-61.1-121.3c-9.1-18.5-17.2-37.5-24.5-56.8
                        c-11.3-29.6-24.1-58.5-38.3-86.7c-5.6-11.2-9.2-22.8-9.5-35.4c0-1.3-0.2-2.7-0.1-4c0.5-4.9-0.6-10.9,5.4-12.8
                        c5.3-1.7,8.4,2.8,11.5,6.3c5.2,6.1,10.3,12.3,15.4,18.4c3.6,4.3,7.2,8.6,12,12.1c-0.3-1.2-0.5-2.4-0.8-3.6c-2.1-9.5-1-12.4,6.9-17.6
                        c0.8-0.5,1.7-1,2.6-1.5c21-11.4,21.5-9.1,36.2,2.9c8.2,6.8,16.7,13.3,25,19.9c0.7,0.6,1.7,0.9,3.5,0.7c-1.6-2.1-3.1-4.3-4.8-6.3
                        c-3.7-4.3-7.5-8.5-11.4-12.6c-6.6-6.9-8.2-14.9-6.2-24c0.9-4.2,1.1-8.5,1.7-12.8L2642.1,1041.6z M2730.1,1231.1
                        c-0.8,9.1-2.1,16.7-2.1,24.3c0,25,0.6,49.9,0.7,74.9c0,4,2.1,8.7-2.2,12.2c-5.5-0.9-5.4-5.6-6-9.2c-1.4-9.2-2.8-18.5-3.1-27.8
                        c-1-26.6-2.1-53.3-2-79.9c0.1-45.4-14.8-84.2-49-114.7c-13.7-12.2-28-23.6-42-35.4c-4.6-3.9-9.4-4.7-14.7-1.9
                        c-3.5,1.9-7.2,3.4-10.5,5.7c-6.4,4.3-7.4,7.5-3.8,14.3c4,7.6,8.5,15.1,13.3,22.2c9.3,13.6,16.4,28.1,21.3,43.8
                        c10.2,32.4,20.7,64.6,31,96.9c6.4,19.9,12.8,39.9,19.3,59.8c1.2,3.7,3.1,7.2-2.2,11c-2.3-5.9-4.5-11.1-6.2-16.4
                        c-16.6-49.5-32.9-99.1-50-148.4c-4.1-11.8-9.5-23.7-16.7-33.8c-14.2-19.7-30-38.2-45.3-57.2c-1.7-2.1-4.2-3.6-6.7-5.7
                        c-3.4,14.9-2.8,22.1,3.9,35.9c16.3,33.5,31.1,67.7,44.1,102.6c5.2,14,11.3,27.8,18,41.1c26.9,53.5,54.2,106.8,81.4,160.2
                        c0.9,1.8,1.9,3.5,2.8,5.3c2.5,4.5,5.8,6,10.8,3.6c3.9-1.9,8-3.3,11.9-5.1c17.8-8.2,36.1-13.7,56-11.7c2.3,0.2,4.8-1,7.1-1.6
                        c-18.6-54.9-36.8-108.7-55.1-162.5C2734.1,1233.1,2732.8,1232.7,2730.1,1231.1z M2787.4,1570.9c3.8,7.8,7.3,14.9,10.6,22.1
                        c6.3,13.6,12.3,27.3,19,40.7c2.5,4.9,6.4,9.3,10.6,12.9c1.9,1.6,6.5,1.6,9.1,0.5c3.7-1.6,3-5.4,1.5-8.7c-1.8-3.9-3.6-7.9-5.5-11.8
                        c-9.1-19.3-18.2-38.5-27.2-57.8c-3.8-8.1-3.3-8.9,5.3-12.7c1.7,3.1,3.5,6.2,5.1,9.5c10.5,20.8,20.8,41.7,31.4,62.4
                        c2.6,5,5.7,9.8,9.2,14.2c4.3,5.5,8,6.5,11.9,4.4c4.1-2.3,5.5-7.2,3.4-13.3c-1.1-3.1-2.7-6.1-4.2-9.1c-13.1-25.8-26.3-51.6-39.4-77.4
                        c-3.7-7.3-3.4-7.7,4.5-12c2,3.4,4.2,6.7,6.1,10.1c10.3,17.9,20.6,35.8,30.9,53.7c1.5,2.6,2.7,5.5,4.8,7.5c1.9,1.8,5.3,4.1,7.2,3.5
                        c3.6-1,3.7-4.9,2.7-8.4c-0.5-1.6-1-3.2-1.7-4.7c-3.3-7.3-6.3-14.7-9.9-21.8c-10.2-19.8-20.8-39.4-31.1-59.2
                        c-5.4-10.5-4.7-12.1,7-17.6c12.5,11.9,29.4,13.5,45.5,17.1c4.1,0.9,8.7,1.2,12.9,0.7c6.9-1,12.7-3.9,14-12.2
                        c-1.4-1.1-2.5-2.5-3.9-3.1c-25.6-9.5-44.8-26.8-61-48.3c-8.9-11.7-19.6-21.8-32.2-29.7c-16.7-10.5-34.7-14.6-54.4-10.9
                        c-14,2.6-26.2,9.5-38.9,15.1c-6.7,2.9-9.5,7.6-9.1,14.9c0.6,10.6,1.4,21.3,1,31.9c-0.9,20.2,3,39.2,12.4,56.9
                        c13.7,25.8,28.2,51.3,42.4,76.9c2.4,4.3,5.4,8.4,8.2,12.5c2.4,3.5,5.4,5.5,10.7,2.6c-5.1-11.2-10.1-22-15-32.9
                        C2777.1,1579.7,2777.3,1579.2,2787.4,1570.9z M2655.5,1021.4c-1.5,1.9-2.5,2.5-2.7,3.4c-2.4,10.3-4.8,20.7-6.8,31.1
                        c-1.5,7.7,0.9,14.4,6.2,20.1c18.3,19.4,34.8,40.2,49.5,62.4c1.2,1.9,3.3,3.2,6.6,6.2C2704.1,1096.1,2681.8,1058.3,2655.5,1021.4z"/>
                    <path d="M2023.6,1443.2c-2.5-23.6-9.5-44.9-31.3-58.1c-3.7-2.2-7.5-4.3-11.1-6.7c-8.3-5.5-9.7-9.3-6.3-18.5
                        c7.3-19.4,16.6-37.8,28.8-54.6c12-16.6,26.4-30.8,43.4-42.3c1.8-1.2,3.4-2.6,4.8-4.6c-6.5,0.5-11.9,3.6-17.1,7.1
                        c-16,10.8-29.2,24.2-39.1,40.8c-2.9,4.9-5.9,9.7-9.3,14.2c-2,2.6-4.4,5.1-7.2,6.8c-5.2,3.2-9.1,1.3-10-4.7c-0.5-3.2-0.4-6.7,0.3-9.9
                        c5.7-25.7,10.9-51.5,17.7-76.8c2.9-10.7,9.1-20.6,13.9-30.8c1.2-2.6,3.1-5,4.6-7.4c-0.8-0.7-1.3-1.4-1.7-1.4
                        c-11.8-0.6-13.3-2.2-10.8-13.9c3.6-17.3,7.5-34.5,11.3-51.7c0.3-1.3,0.7-2.6,1.1-3.8c2.9-7.6,7.8-9,13.3-3
                        c7.2,7.8,14,16.1,20.9,24.2c2.8,3.3,5.4,6.7,8.8,11c-6.8,5.5-13.2,10.6-19.6,15.7c0.1,0.3,0.2,0.6,0.3,0.9c1.8-0.7,3.6-1.5,5.4-2.1
                        c9.1-3.1,18.1-6.7,27.4-9c8.4-2.1,16.6-0.3,23.6,5.2c11.8,9.2,21.1,20.3,24.7,35.3c2.4,10,4.8,20,7.7,29.8c0.9,2.9,3.9,5.1,5.7,7.7
                        c2.8,4,6.7,7.7,8,12.2c16.1,54,42.8,102.4,76.6,147.2c17.3,22.9,17.4,33.5,1.9,57.8c-13.2,20.7-31.1,36.3-52.1,48.7
                        c-8.9,5.2-17.7,10.4-26.1,17.1c1.9-0.5,3.9-0.8,5.7-1.5c13.8-5.2,27-11.3,38.4-20.8c0.3-0.2,0.5-0.4,0.8-0.6c3.9-2.7,7.7-5.6,12.8-3
                        c4.8,2.5,5.7,6.9,5.9,11.8c0.4,10.3,1.1,20.6,1.7,30.9c2.3,46.6,5.8,93.1,4.3,139.8c-1.8,56.3-3,112.6-6.3,168.8
                        c-2.5,42.6-11,84.4-21.2,125.9c-11.3,45.9-22.9,91.7-40.6,135.7c-2.6,6.5-5.8,12.7-9,18.9c-1.7,3.4-4.3,6.6-8.5,5.5
                        c-4.3-1.1-4.7-5.3-4.8-9c-0.2-13-0.2-26-0.2-39c0-6-0.4-12,0.1-18c1.5-19.3-6.8-34-20.3-46.7c-0.3,0.7-1,1.4-0.8,1.7
                        c5.6,11.7,2.1,13.6-4.9,19.6c-8.3,7.1-16.9,13.9-26.5,21.7c-2-4-3.6-6.8-4.8-9.7c-4.7-11.4-9.2-22.8-14.1-34.1
                        c-4-9.2-3.8-18,1.3-26.7c4.4-7.5,8.3-15.2,12.4-22.8c1-1.8,1.6-4.4,3-5c7.2-3.3,6.7-9.9,7.3-16c3.2-31.1,6.4-62.3,9.4-93.4
                        c1.1-11.9,1.6-23.9,2.4-35.9c4-54.8,7.8-109.6,12.1-164.4c1.7-22.6,4.4-45.1,6.6-67.6c2.1-22.1,7.5-42.7,24.9-59
                        c-1.9,0.4-4,0.5-5.8,1.2c-20.1,7.1-40.1,14.4-60.2,21.4c-4.7,1.6-9.6,2.9-14.5,3.3c-11.4,0.8-15.7-3-15.1-14.3
                        c0.6-12.6,2.2-25.2,3.5-37.8c1.9-19.3,2.4-38.5-1.3-57.7c-0.4-1.9-1.6-3.6-2.4-5.3C2023.4,1442.9,2023.6,1443.2,2023.6,1443.2z
                        M2187.4,1502.9c-8.3,5.5-14.4,10.2-21.1,13.7c-9.4,4.9-19.2,9.1-29,13.4c-18.3,8.2-30,21.6-33.9,41.5c-1.3,6.5-2.6,13.1-3.4,19.7
                        c-3.3,30.1-6.7,60.2-9.5,90.4c-2.6,27.8-4.7,55.7-6.6,83.7c-1.3,18.6-1.2,37.3-2.8,55.9c-3.3,38.8-6.6,77.6-11.1,116.3
                        c-3,25.8,2.4,48.9,16.7,70.2c5.8,8.5,12.1,16.7,18.7,24.7c7,8.6,10.6,18.1,10.7,29.3c0.1,19.3,0.8,38.6,1.3,57.9
                        c0.1,2.1,0.6,4.1,1.4,8.7c3.3-4.6,5.7-7,6.8-9.8c6.4-16.8,13.4-33.4,18.4-50.6c9.2-32,17.1-64.3,25.5-96.5
                        c12.3-46.8,20.7-94.3,22.4-142.8c1.5-41.6,2.9-83.2,4-124.8c0.6-23,1.6-46,0.7-68.9c-1.5-40.9-4.2-81.8-6.5-122.7
                        C2189.9,1509.5,2188.5,1506.9,2187.4,1502.9z M2217.1,1430.6c-2.1-5.5-3.7-10.9-6.3-15.8c-2.3-4.4-5.6-8.2-8.6-12.2
                        c-32.9-43.7-58.7-91.1-74.5-143.7c-3.8-12.7-6.8-14.6-20.5-13.1c-25.6,2.8-47.7,13.9-67.7,29.6c-26.1,20.5-43,47.8-56,77.8
                        c-7.1,16.4-7.1,16.4,8.6,25.4c14.5,8.2,25,19.8,30.5,35.6c9.4,27.1,13.4,54.9,10.4,83.5c-1.5,14.2-3,28.5-4.4,42.7
                        c-1,10.1,2.8,13.9,12.7,12c7.8-1.5,15.5-3.9,23.1-6.3c37.6-12.1,73.5-27.7,105.3-51.6c16.7-12.5,31.3-27,40.8-45.9
                        C2213.2,1443.1,2214.8,1437,2217.1,1430.6z M1974,1322.5c9.3-4.5,12.7-11,16.4-17.3c18.5-31.5,44.8-52.1,81.1-59.5
                        c11.4-2.3,22.6-5.8,33.7-9c7.2-2.1,8.1-3.8,6-10.8c-2.7-9.2-5-18.8-9.1-27.4c-12.4-25.4-28.4-35.3-54.3-24.5
                        c-6.4,2.7-12.8,5.8-18.5,9.7c-19.4,13.5-31.5,32.2-37.1,55.1c-6,24.6-11.6,49.2-17.3,73.9C1974.3,1315.1,1974.4,1318,1974,1322.5z
                        M2061.1,2058c7.4-5.9,13.5-10.5,19.3-15.4c7.9-6.6,7.6-7.6,2.9-16.6c-7.3-14.1-14-28.5-20.9-42.8c-1-2-2-3.9-3.6-7.1
                        c-5.6,10.3-11,19.4-15.5,28.9c-1.4,3-1.8,7.5-0.6,10.6C2048.2,2029.3,2054.4,2042.8,2061.1,2058z M1999.2,1190.3
                        c15.3-9.8,28.2-21.2,41.6-32.8c-9.7-11.6-18.5-22.3-28.3-34C2006,1146.3,2001.2,1167.8,1999.2,1190.3z M2023.1,1181.3
                        c-0.4-0.5-0.9-1-1.3-1.5c-2.4,2.2-4.8,4.4-7.2,6.6c0.4,0.5,0.9,0.9,1.3,1.4C2018.3,1185.7,2020.7,1183.5,2023.1,1181.3z"/>
                    <path d="M2642.4,1041.3c-2.9,4.9-7.2,7.3-12.5,9c-16.8,5.3-33.3,11.2-51.6,9.9c-16.6-1.2-30.4-6.8-40.4-20.4c-3-4-6-8-8.4-12.4
                        c-13.1-22.8-26-45.7-39-68.6c-0.8-1.4-2.1-2.9-2.1-4.4c-0.1-1.8,0.4-4.3,1.6-5.2c1.2-0.9,4.2-1.1,5.4-0.2c3.2,2.3,5.9,5.3,8.8,8.1
                        c4.9,4.5,9.5,9.4,14.8,13.4c9.5,7.1,17.2,6.4,26-1.7c8.8-8,13.7-17.9,14.1-29.9c0.3-9.7,0.3-19.3,0.4-29c0-3.3,0-6.6,0-10.5
                        c10.7,0.2,17.7,8.3,27.3,9.5c-17.2-13.7-33.2-28.4-45-47.6c0.9,3.7,1.6,7.4,2.7,11c6.3,20.3,9.2,41,7.4,62.1
                        c-0.7,7.9-1.9,15.9-4.1,23.5c-4.4,14.6-16.8,18.6-28.8,9.1c-20-15.9-34.1-36.3-43.4-60c-1.2-3.1-2.4-6.2-3.7-9.3
                        c-0.1-0.3-0.5-0.4-0.8-0.7c-0.4,0.3-1.2,0.5-1.3,0.8c-4.5,16.7-9,33.4-13.5,50.1c-0.5,1.9-1.4,4.3-0.7,5.8c5.1,10.9,1.3,21.8,0,32.4
                        c-4.7,36.5-13.2,72-27.8,105.9c-7.2,16.6-15.7,32.3-27.5,46.2c-9,10.7-15,15.1-25.5,18.4c-5.1-18.8-10.1-37.6-15.2-56.4
                        c-0.1-1.8-0.1-3.7-0.2-5.5c-5.9-26.4-18.5-50.3-29.6-74.3c3.7-4.2,6.7-1.6,9.7-0.5c15.9,5.8,32.2,8.3,49,6
                        c19.3-2.7,32.2-13.9,39.9-31.8c-2.4,2-4.8,4-7.1,6.1c-10,9-21.3,15.8-34.3,19.8c-18.2,5.6-35.6,4-52-5.5
                        c-29.6-17.1-53.4-40.6-73.2-68.3c-6.7-9.3-7.1-19.1-1.2-29c1.4-2.3,2.7-4.6,4.2-6.8c27-41.2,53.8-82.5,81.3-123.4
                        c15.4-23,26.1-47.8,31.5-74.9c0.1-0.7,0.2-1.3,0.3-2c3.5-17.5,10.9-20.4,25.5-9.9c23.5,16.8,41.6,38.2,53,64.9
                        c3.4,7.9,6.9,15.8,10.7,24.6c2.1-3,3.7-5.4,5.5-7.7c4.5-5.5,10-6.6,15.7-2.3c3.4,2.5,6.3,5.9,8.8,9.3c8.2,11.3,16,22.9,24,34.4
                        c1.8,2.6,3.8,5.1,6.4,7.3c-4-13.1-14-23.9-14.9-39.3c7.3,1,14.1,2,20.9,3c2.5-0.1,5-0.1,7.5-0.2c2.2,1.2,4.3,3,6.7,3.5
                        c14.1,3,28.4,3,42.7,1.1c6.3-0.9,11.1,0.5,14.1,6.6c11.4,23.7,21.8,47.7,26,74c2.4,14.9,3.8,29.2-8.7,41.2
                        c9.5,5.9,13,14.8,15.9,24.2c8.1,26.5,10.8,53.3,5.6,80.8c-1,5.2-0.8,10.6-1.1,15.9C2642.1,1041.6,2642.4,1041.3,2642.4,1041.3z
                        M2465.9,846.6c-0.9-14.6-0.5-27.7-2.6-40.5c-7-43.6-29.5-77.8-65.7-103.1c-1.1-0.8-2.3-1.4-3.5-2c-4.8-2-7.5-1.1-9.5,3.8
                        c-1.3,3.4-2.1,7-2.8,10.6c-4.9,25.9-15.3,49.4-29.7,71.4c-27.5,41.7-54.7,83.6-82.1,125.3c-1.6,2.5-3.2,5-4.7,7.7
                        c-4.6,8.4-4.2,16.6,1.6,24.3c19.4,26.2,42.3,48.8,70.5,65.6c15.5,9.2,31.9,10.7,49,5.2c15.9-5.1,29.2-14.3,40-27.1
                        c12.3-14.7,21.4-31.3,27.7-49.4C2464.7,908.2,2467,876.8,2465.9,846.6z M2499.7,962.5c0.7,3.2,0.7,3.9,1,4.5
                        c11.6,20.2,23.2,40.4,34.7,60.6c12.3,21.5,31.1,30.5,55.2,27.1c12.1-1.7,23.8-5.7,35.8-8.4c6.9-1.5,10.5-5.7,10.9-12.4
                        c1-16.9,2.6-33.9,2.2-50.7c-0.4-15.6-4.5-30.9-10.4-45.5c-1.9-4.7-4.1-9.2-10.1-9.9c-15.5-1.8-29.2-8.7-42.7-16
                        c-3.1-1.7-6.4-2.9-11.1-5.1c-0.5,11.5-1.1,21.4-1.4,31.3c-0.4,14-5.2,25.9-16.1,35.1c-11.5,9.6-20,10.1-32.4,1.3
                        C2510.6,970.9,2506,967.2,2499.7,962.5z M2548.4,923.3c-1.1-22.3-5.7-43.7-13.5-64.4c-8.3-21.8-21.9-40.2-35.8-58.5
                        c-3.6-4.8-7-9.8-11.2-14.1c-5-5.2-12-3-13.6,4c-0.7,3.2-0.6,6.6-0.6,9.9c0.1,20.3,0.4,40.6,0.2,60.9c-0.3,40.6,16.5,73.4,45.8,100.3
                        c10.4,9.5,20.2,7,23.9-6.8C2546.3,944.5,2546.9,933.8,2548.4,923.3z M2378.6,1150.5c5.3-4.4,9.3-7.2,12.7-10.7
                        c12.3-12.5,21.1-27.3,28.4-43.1c17.1-36.9,25.7-76,31.3-116c0.2-1.6-0.6-3.4-1.2-6.9c-3.4,5.1-5.8,8.6-8.1,12.2
                        c-3.5,5.6-6.8,11.4-10.3,17.1c-9.1,14.5-21.9,24.4-38.9,27.1c-9.4,1.5-19.1,1.1-28.7,0.7c-7.2-0.3-14.3-1.7-23-2.9
                        C2359,1067.3,2369.6,1107.4,2378.6,1150.5z M2519.2,798.4c2.1,4.9,3.2,8,4.7,11c7.1,14.6,14.3,29.3,21.5,43.9
                        c12.9,25.9,33.3,44.7,57.5,59.5c12.5,7.7,22.3,2.2,23.3-12.6c0.4-6.6-0.1-13.3-1.1-19.9c-3.7-23.9-13.1-46-23.2-67.7
                        c-4.1-8.7-5.1-8.6-14.7-8.2c-11.2,0.5-22.6,0.7-33.8-0.3C2542.6,803.1,2531.9,800.6,2519.2,798.4z"/>
                    <path d="M1798.8,1032.6c0.4,3.1,0.8,6.3,1.2,9.4c0.3,3.6,0.1,7.4,1,10.9c2.7,10.7,0.2,19.7-7.5,27.5c-3.7,3.8-7,8-9.8,13.2
                        c1.7-0.9,3.7-1.5,5.2-2.6c7.9-6.1,15.9-12.2,23.4-18.7c6.8-5.9,13.8-7.7,22-3.5c1.8,0.9,3.7,1.6,5.5,2.5
                        c13.4,6.3,15.7,10.8,12.4,26.9c2.7-3,4.4-4.7,5.9-6.6c7.1-8.4,14-16.9,21.2-25.3c1.9-2.3,4.1-4.5,6.6-6c4.5-2.8,9.1-1,9.7,4.3
                        c1.3,12.6,2.3,25.5-4.1,37.2c-17.2,31.4-29.8,64.6-42.5,97.9c-15,39.6-33.9,77.4-53.4,114.9c-16,30.7-32.1,61.4-48.1,92
                        c-0.6,1.2-1.3,2.4-1.9,3.5c-7.1,12.9-9.4,13.8-22.8,8.3c-8-3.3-15.9-6.9-24-9.9c-12.3-4.6-25-7-38.2-4.8c-2,0.3-4,0.4-6,0.3
                        c-6.5-0.5-8.9-3.5-7.5-10c0.9-4.2,2.4-8.3,3.8-12.4c13.2-37.7,26.6-75.3,39.7-113c3.7-10.7,6.6-21.6,10-32.4
                        c1.6-5,2.8-10.4,8.9-12.6c6.6,2.4,8.2,7.9,8.6,14.2c1.2,17.9,2.3,35.8,3.4,51.9c0-18.4,0.1-38.7,0-58.9c-0.1-16.7,0.8-33.3,4.9-49.6
                        c1.9-7.4,4.3-14.7,6.6-22.4c-6.6-6.2-5.4-14.4-4.3-22.3c3.1-22.6,9.4-44.4,20.3-64.4c9.4-17.2,19.6-34.1,29.9-50.8
                        c1.9-3.1,5.7-6.8,8.8-7c4.9-0.3,6,5.3,7.7,9.2C1796.3,1026.5,1797.5,1029.6,1798.8,1032.6z M1708.3,1231.4c-1,2.4-1.8,4.1-2.3,6
                        c-3.2,10.5-5.8,21.2-9.4,31.5c-13.7,39.3-27.6,78.4-41.5,117.7c-1.2,3.3-2.1,6.7-3.4,10.7c3.6,0.3,5.9,0.9,8.1,0.7
                        c18.5-2.1,35.7,2.6,52.3,10c4.9,2.1,9.7,4.4,14.7,6.3c6.6,2.6,8.5,1.9,12.2-4.2c1.7-2.8,3.2-5.8,4.8-8.8
                        c14.6-28,29.1-56.1,43.9-84.1c22.1-41.8,43.1-84.2,59.6-128.6c10.3-27.8,21.8-55,35.2-81.5c2.5-5,4.9-10.3,6.5-15.6
                        c2.6-8.5,3.6-17.1-0.6-27c-3.4,3.9-6.1,6.7-8.7,9.7c-7.4,9-14.5,18.3-22.3,27c-20.2,22.4-33.9,48.4-43.3,76.9
                        c-14.6,44.6-29.4,89.1-44.2,133.6c-1.3,3.8-2.7,7.5-4.4,11.1c-0.7,1.4-2.5,2.3-5.6,4.9c0.5-4.7,0.3-7.3,1-9.7
                        c16.7-51.9,33.5-103.8,50.3-155.7c6.5-20.1,15.3-39,28.3-55.8c3.2-4.1,5.3-9.2,7.4-14.1c2-4.6,1.2-9.1-2.6-12.8
                        c-9.1-9-21.8-9.2-31.2-0.8c-4.7,4.2-9.7,8.1-14.5,12.2c-9.1,7.8-18.8,14.9-27.1,23.5c-19.3,19.8-34.7,42.1-41,69.7
                        c-4.4,19.4-3.9,39.1-3.3,58.7c0.8,26.1-0.9,51.9-4,77.8c-0.7,5.6-1.4,11.2-2.5,16.8c-0.6,3.2-2,5.8-6.8,3.9
                        c-0.2-3.3-0.5-6.9-0.5-10.5c-0.1-29.3-0.1-58.6-0.2-88C1713.4,1239,1713.7,1234.8,1708.3,1231.4z M1788.4,1021
                        c-25.9,22.8-56.1,94.5-51.3,120c0.9-0.6,1.9-0.9,2.3-1.5c16-23.4,33.6-45.6,52.7-66.6c2.5-2.7,4.5-7.2,4.3-10.7
                        C1795.5,1048.6,1792.1,1035.4,1788.4,1021z"/>
                    <path d="M1654.3,1583.9c-6.1,13.2-12.2,26.5-18.4,39.7c-2.8,6-5.6,12.1-8.6,18c-3.2,6.3-8.4,10.3-15.3,11.8
                        c-8.7,1.9-14.2-2.5-14.4-11.4c0-1.6,0-3.2,0-6.2c-4,4.5-7,8.4-10.5,11.7c-4.1,4.1-8.8,7.4-15.1,4.9c-5.5-2.2-8.9-8.3-8-15.7
                        c0.5-3.8,1.9-7.7,3.7-11.2c5-10.2,10.3-20.1,15.5-30.2c1-1.9,1.8-4,1.1-6.9c-2.9,4.9-5.6,9.9-8.6,14.7c-2.3,3.7-4.3,7.6-7.3,10.7
                        c-5.8,6-14.1,4-16.2-4c-0.9-3.6-0.7-8.1,0.6-11.6c2.9-8.1,6.3-16.1,10.3-23.8c10-19.5,20.5-38.9,30.7-58.3c1.6-3,4.5-6,1.7-10.5
                        c-1.5,0.3-3.4,0.2-4.6,1c-10.4,7.3-22.3,10.3-34.5,12.4c-5.9,1-11.8,2.1-17.7,2.9c-10.6,1.4-17.9-4-23.1-12.5
                        c-3-4.9-2.3-7.8,2.3-11.1c2.7-1.9,5.7-3.4,8.8-4.7c20.5-8.5,36.7-22.1,50.4-39.7c8.7-11.2,18.7-22.1,30-30.5
                        c28.2-21,59.1-23.8,91.2-8.8c5.7,2.7,11.4,5.4,17.2,8c6.8,3.1,10.3,8.1,10,15.7c-0.6,19-0.9,38-1.6,56.9c-0.2,4.3-1.2,8.6-2.7,12.6
                        c-4.3,11.5-8.2,23.3-14,34.1c-12.5,23.5-26,46.4-39.2,69.6c-2,3.5-4.2,6.9-6.7,10c-2,2.6-4.4,5-7.1,6.9c-3.7,2.6-7.6,2.2-11-0.7
                        c-3.3-2.8-3.5-6.1-1.7-9.9c3.8-7.8,7.5-15.6,11.1-23.5c1.5-3.2,2.6-6.5,3.9-9.8C1655.9,1584.5,1655.1,1584.2,1654.3,1583.9z
                        M1631,1556.4c8.7,2.6,9.3,3.5,6.9,9.8c-0.8,2.2-1.9,4.2-3,6.3c-9.5,19.8-19,39.6-28.4,59.5c-2.2,4.7-4.9,9.6-1.1,16.4
                        c3.5-1.1,7.9-1.2,10.4-3.5c4.1-3.6,7.8-8.2,10.2-13.1c8.2-16.7,15.8-33.8,23.7-50.7c1.5-3.2,3.3-6.2,5.1-9.3
                        c10.4,6.8,10.7,7.6,6.3,17.1c-2.8,6-5.8,12-8.7,18c-2.4,5-4.5,10.1-6.8,15.3c6.9,1.8,7,1.9,9.4-0.9c1.5-1.8,2.8-3.7,4-5.7
                        c11.1-19,21.8-38.2,33.3-56.9c10.9-17.7,18.7-36.7,25.3-56.3c1-2.8,1.7-5.8,1.7-8.8c0.3-18.3,0.4-36.7,0.6-55
                        c0.1-5.2-2-8.9-6.8-11.1c-8.5-3.8-16.7-8.3-25.4-11.6c-27-10.3-52.3-6.4-75.6,10.6c-11.7,8.5-21.6,18.8-30.2,30.4
                        c-13.9,18.5-31.1,32.8-52.6,41.7c-3.2,1.3-6.1,3.5-9,5.2c4.8,11.3,12.8,14.3,23.2,12.2c15.9-3.3,32.2-5.5,46-15.4
                        c3.2-2.3,6.5-3.2,10-0.2c3.8,3.3,4.7,7.1,2.5,11.6c-1.1,2.4-2.5,4.7-3.7,7.1c-13,25.5-25.9,51-38.8,76.6c-1.2,2.4-2.6,5-2.6,7.5
                        c0,2.3,1.1,5.6,2.8,6.5c1.5,0.8,5-0.4,6.6-1.8c2.1-1.9,3.4-4.9,4.9-7.5c11-19,21.9-38.1,32.9-57.1c1.8-3.1,3.8-6,5.8-9.1
                        c6.9,5.6,7.1,5.7,4.2,11.6c-5.1,10.5-10.5,20.8-15.7,31.2c-8.4,16.6-17.1,33.2-25.2,49.9c-1.9,4-3.3,8.9-3,13.3
                        c0.7,7.7,7.5,10,12.7,4.4c4.5-4.8,8.4-10.4,11.5-16.2c9-17.1,17.4-34.5,26.1-51.7C1624,1569.8,1627.5,1563.1,1631,1556.4z"/>
                    <path d="M2359.5,1094.8c0.1,1.8,0.1,3.7,0.2,5.5c1.7,13.9,3.5,27.7,5.2,41.6c1.4,11.4,0.4,13.5-10.1,17.4
                        c-11.7,4.4-20.4,12.2-27.4,22.3c-11.2,16.3-19.2,34.2-25.6,52.8c-8.1,23.6-16,47.2-24.1,70.8c-8.7,25.7-21.2,49.5-36.7,71.8
                        c-2.1,3-4.3,6-7.1,8.3c-3.9,3.3-7.7,2.1-9-3c-0.9-3.2-0.9-6.6-0.9-9.9c-0.1-55-0.2-109.9,0-164.9c0.2-57.9,0.9-115.8,1.2-173.7
                        c0.1-24.3,4.4-47.6,13-70.3c6.3-16.5,14-18.3,25.9-5.5c18.5,20.1,37,40.2,54.5,61.1c16.6,19.8,28.5,42.5,36,67.3
                        C2355.7,1089.6,2357.9,1092.1,2359.5,1094.8z M2230.9,1380.6c3.7-4.6,6.1-7.2,8-10c11.8-17,21.8-35.1,29-54.6
                        c7.8-21.2,14.9-42.7,22.3-64.1c7.6-22,14.8-44.2,27-64.3c9-14.8,19.1-28.1,36.6-33.7c4.4-1.4,6.3-4.7,5.9-9.2
                        c-0.5-5-0.9-10-1.5-14.9c-3.8-35.8-14.4-69.4-36.9-97.8c-19.2-24.3-40.5-46.8-60.9-70.2c-0.7-0.7-1.4-1.4-2.2-2.1
                        c-6.9-5.8-10-5.3-13.4,3c-6.9,16.7-12.2,34-12.8,52.1c-0.9,28.6-1,57.3-1.1,86c-0.4,90-0.7,180-1,270
                        C2229.9,1373.1,2230.4,1375.5,2230.9,1380.6z"/>
                    <path d="M2541,794.5c-2.5,0.1-5,0.1-7.5,0.2c-4.3-1.6-8.6-3.3-13-4.7c-17.3-5.8-32.8-14.8-45.6-27.9c-24.6-25.3-49.9-49.9-78.5-70.9
                        c-8.1-5.9-10.2-13.6-7.8-22.9c6.4-24.8,20.4-43.7,43.5-55.5c10.7-5.4,21.4-6.3,32.8-2.2c27.5,9.9,52.8,23.4,75.3,42.3
                        c25.6,21.5,41,48.8,47.3,81.2c3,15.3,4.5,30.9,6.1,46.5c1.2,11.2-0.8,13.6-12.1,14.9C2568.1,797,2554.5,798.1,2541,794.5z
                        M2560.6,792.1c5.8-0.6,12.8-1.2,19.7-2.1c7.7-1,9-2.5,8.2-10.5c-1.3-12.2-2.6-24.5-4.6-36.7c-5.9-36.7-22.4-67.3-52.2-90.2
                        c-20.8-16-43.6-28.1-68.2-37c-10.4-3.7-20.2-2.9-29.8,2.3c-19.5,10.5-32.2,26.5-38.7,47.5c-3.7,12-1.6,16.1,8.3,23.8
                        c12.4,9.5,24.6,19.3,36.1,29.9c13,11.9,25.1,24.9,37.6,37.3C2499.7,779.2,2527.6,790.2,2560.6,792.1z"/>
                    <path d="M462.2,949.9c-1.4,3.2-3.2,6.3-4.1,9.7c-8.8,30.4-18.9,60.4-34.3,88.2c-5.2,9.3-11,18.2-16.5,27.3c-1.1,1.8-2.1,3.7-1.8,6.4
                        c7.8-9.4,15.5-18.8,23.3-28.1c2.1-2.5,4.2-5.2,6.8-7.3c6.3-5,13.6-3,15.7,4.7c3.7,13.7,5.3,27.9-0.6,41.2
                        c-10.4,23.6-16.9,48.3-23.8,73c-12.8,46-31,90-49.9,133.7c-14,32.4-30.2,63.7-45.3,95.6c-6.6,13.8-12.9,27.8-19.4,41.6
                        c-1.6,3.4-2.5,8.8-7.5,7.8c-4.8-0.9-4.5-6.1-4.6-10c-0.2-7.6-0.5-15.3,0.2-22.9c4-43.8,10-87.2,21.2-129.9
                        c10.7-41,25.3-80.6,39.5-120.4c1.1-3.1,2.1-6.3,3.4-9.4c3.7-9.3,6.9-18.2,2.5-28.7c-2.4-5.8-1.6-12.9-3.2-19.5
                        c-7.6,74.2-35.6,140.9-74,203.9c0.6,0.3,1.2,0.7,1.8,1c6.3-8.6,12.6-17.2,19.3-26.3c4.7,4.4,3.3,9.1,2.7,13.4
                        c-1.9,11.8-4.6,23.5-6.1,35.4c-3.8,30.7-7.6,61.5-10.6,92.3c-1.7,17.1,1.5,34,6.2,50.3c6.5,22.9,2.6,43-11.3,62.2
                        c-13.8,19.1-26.4,39.2-39.7,58.8c-6.9,10.2-13.7,20.5-21.1,30.3c-2.4,3.2-6.5,5.8-10.3,7.1c-5.1,1.7-9.2-2.4-8.5-7.8
                        c0.4-2.9,1.5-5.8,2.8-8.5c3.9-8.4,7.9-16.8,10.6-26c-1.2,1.8-2.5,3.5-3.5,5.5c-8.1,16.4-16.1,32.9-24.1,49.3
                        c-3,6.2-6.7,11.5-14,13.2c-5,1.1-10,1.9-14-2.4c-4-4.3-3.1-9.3-1-14c2.7-6.3,5.8-12.5,7.4-19.4c-3.7,6.8-7.2,13.6-11.2,20.2
                        c-2.4,3.9-5.2,7.9-8.6,10.8c-5.4,4.6-11.4,4.5-16.6,0.9c-5.1-3.5-7.2-9.3-4.9-16c2.1-5.9,5.1-11.6,7.7-17.3c1.3-2.9,2.6-5.8,2.6-9.5
                        c-2,1.9-4,3.9-6.1,5.7c-7,6.1-15.4,3.6-17-5.7c-0.8-4.7,0.1-9.9,1.3-14.6c1-3.8,3.4-7.2,5.4-10.6c12.2-21,24.5-42,36.7-63
                        c1.8-3.1,3.5-6.3,5.2-9.3c-4-5.8-8.9-5-13.9-4c-11.1,2.3-22.1,4.8-33.3,6.6c-9.7,1.6-17.7-2.1-23.3-10.2c-4.3-6.3-3.3-11.1,3.2-15
                        c1.7-1,3.5-1.9,5.4-2.5c14.6-5,26.1-14.2,36.6-25.3c14.4-15.3,29.3-30,44-45.1c6.1-6.3,12.9-11,21.7-13.6c6.2-1.8,12.7-5.2,17-9.9
                        c8.9-9.7,16.4-20.8,24.4-31.3c2-2.3,4-4.6,5.9-7c-0.6-0.5-1.2-0.9-1.8-1.4c-1.6,2.5-3.3,5.1-4.9,7.6c-6.8,7.3-13.4,14.7-20.4,21.8
                        c-2.3,2.3-5.7,3.6-8.5,5.3c-4.8-5.3-1.8-9.1,0.1-12.8c17.8-35.2,32-71.8,40-110.4c4.9-23.7,8.6-47.8,11.2-71.9
                        c5.7-52.8,19.4-102.6,49.7-146.9c3.4-4.9,6.7-10,10.8-14.4c7.4-8,13.3-7.5,19.5,1.3c1.7,2.4,3.2,4.9,5.8,8.9c-0.5-9.4-3.3-16-7.7-22
                        c-19-25.3-22.8-54.1-19.3-84.5c4.3-36.8,19.7-68.8,43.3-97.1c2.3-2.8,5-5.3,6.7-7.2c-1.6-3.3-4.1-6.1-4-8.8
                        c1.4-29.9-1-60.1,4.7-89.7c7.6-39.2,31.5-67.6,63.4-89.9c21.2-14.8,45.2-22.5,70.7-25.2c9.1-0.9,17.6-3,25.9-6.9
                        c30.5-14.1,61.1-27.9,91.7-41.9c19-8.6,19-8.6,29.3,8.9c10.1,17.1,23.5,31.4,36.9,45.8c4.3,4.6,8.8,9.1,13.2,13.7
                        c3.2,3.4,6.1,7,5.7,12.5c-4.6,3.7-9.4,3.9-14.9,2.4c-22.8-6.2-45.8-11.7-68.5-18.2c-18.7-5.4-36.7-5.9-54.1,4.1
                        c-4.5,2.6-7.9,2.1-11.4-1.9c-4.8-5.5-9.8-10.8-19.9-12.8c3.1,2.7,4.5,4,6,5.2c1.8,1.5,3.7,2.8,5.5,4.3c10.8,8.8,12,12.8,8.2,25.9
                        c-0.6,2.2-0.8,4.5-1.2,6.7c-1.3,3.4-2.5,6.8-3.8,10.2c-1.4,2.5-2.8,4.9-4.2,7.4c0.6,0.3,1.3,0.5,1.9,0.8c0.7-2.8,1.4-5.6,2.1-8.4
                        c1.3-3.4,2.5-6.8,3.8-10.2c2.1-2.8,4.3-5.5,6.2-8.4c7.8-11.9,18.4-20.5,32.5-23c6.3-1.1,13.5-0.7,19.6,1.2
                        c24.4,7.8,48.6,16.1,72.6,25c24.2,9,37.1,26.8,39.4,52.7c2.3,26.2,4.1,52.4,1.9,78.8c-3.3,39.4-38.2,74.6-77.5,77.9
                        c-13.7,1.2-27.3,1.3-40.7-1.8c-1.2-0.3-2.6,0-4.3,1.4c3.9,1.3,7.8,2.7,11.7,3.9c13.8,4.2,16.7,8.4,14,22.7
                        c-4.5,23.5-9.4,47-14.1,70.5c-0.1,0.7-0.4,1.3-0.5,1.9c-2.9,30.4-17,54.9-38.8,75.5c-5.3,5-10.2,10.5-15.1,15.9
                        c-10,11-17.2,23.7-22,37.7c-0.7,1.9-0.7,4.2-1.8,5.6c-1.5,1.9-3.7,4.1-5.8,4.5c-1.5,0.2-4.2-2.2-5-4c-2.6-6.1-5.6-12.3-6.4-18.7
                        c-2.8-22.8-5.1-45.7-7-68.6c-2.1-25.6-4.1-51.1-5.1-76.8c-1.1-29,2.1-57.9,5.5-86.7c0.2-1.7,0.5-3.3,1-4.9c1.9-7.2,5.5-9.6,12.5-7.4
                        c8.6,2.7,16.8,6.2,25.2,9.4c3.3,1.3,6.7,2.6,10.7,2.6c-1.6-1.1-3.1-2.3-4.8-3.2c-18.8-10.3-35.6-23.5-51.5-37.7
                        c-10.8-9.6-12.2-16.8-6.6-30.2c2.4-5.7,4.8-11.3,6.7-17.1c-14.9,17.5-14.9,17.5-38.4,26.6c-4.3,1.7-8.5,3.4-13.8,5.6
                        c2.4,0.9,3.6,1.3,4.8,1.8c10.1,4,13.4,8,12.8,18.8c-0.8,12.9-2.3,25.9-4.5,38.6c-3,17.4-7.1,34.5-10.7,51.8c-0.5,2.6-0.4,5.3-0.5,8
                        c-0.6,1.7-1.1,3.4-1.7,5c-1.4,5.1-2.8,10.3-4.2,15.4c0,0,0.2-0.5,0.2-0.5c-1.4,4.8-2.8,9.7-4.2,14.5
                        C461.9,950.3,462.2,949.9,462.2,949.9z M737.9,753.6c-1-14.5-1.6-27.5-2.9-40.4c-2.3-23.9-15.3-39.9-37.8-48
                        c-22.8-8.2-45.7-16.3-68.7-24.1c-12.2-4.2-23.6-2.1-34.1,5.4c-9.5,6.7-16,15.6-20.7,26.3c-7.7,17.7-15.9,35.1-24.2,52.5
                        c-9.8,20.4-20.1,40.5-30,60.8c-3.8,7.9-2.2,14.9,4.3,20.7c20.9,18.8,43.2,35.5,68.9,47.2c19.4,8.8,39.6,13.4,61,12.2
                        c42.7-2.3,70.7-24.9,80.9-64.4C739,785.2,737.7,768.6,737.9,753.6z M172.7,1650.9c12,1.4,15.5-0.4,20.6-10.6
                        c9-17.8,17.7-35.8,26.6-53.6c1.6-3.2,3.4-6.2,5.1-9.2c9.7,4.8,10.2,5.7,6.6,13.7c-3.3,7.3-7.1,14.3-10.5,21.5
                        c-1.6,3.4-2.6,7.1-3.9,10.7c0.6,0.5,1.2,1.1,1.8,1.6c2.6-1.3,6-2.1,7.8-4.2c4.7-5.6,9.5-11.4,12.8-17.8c13.1-25.2,29.6-48.2,47-70.5
                        c14.4-18.5,17.6-39.1,10.1-61.3c-4.7-13.8-6.4-27.9-5.1-42.3c3.6-39.4,4.9-79.1,14-117.9c0.8-3.4,2.5-7.5-0.9-12.2
                        c-2.7,3.5-5,6.3-7,9.2c-18.7,26.3-37.4,52.7-56.1,79c-7.8,11-17.6,18.7-30.9,22.2c-4.6,1.2-9.2,4.1-12.6,7.4
                        c-17.7,17.2-35.1,34.6-52.3,52.3c-9.2,9.4-19,17.6-31.6,22c-3.9,1.4-7.3,3.9-10.8,5.9c6.6,12.5,12.1,15.2,24.8,12.6
                        c9.8-2,19.5-4.5,29.2-6.5c8-1.6,15,0.1,20.7,6.7c-2,3.5-3.7,6.7-5.5,9.9c-13.1,22.8-26.4,45.5-39.4,68.4c-2.4,4.3-4.1,9-5.7,13.7
                        c-1.2,3.6-1.7,7.8,2.2,9.8c4.5,2.3,7.5-1.2,10-4.4c2.4-3.2,4.5-6.6,6.6-10c12.5-20.9,24.9-41.8,37.3-62.6c2-3.3,4.2-6.5,6.4-9.8
                        c7.4,5.6,7.5,5.6,3.3,13.2c-12.6,22.7-25.4,45.3-37.8,68.1c-5.1,9.3-9.6,19-14.1,28.7c-3.3,7.2-2.4,11.4,2.1,14.1
                        c4.4,2.5,8.6,1.1,13.4-5.2c3-4,5.5-8.3,7.9-12.7c12.3-22.8,24.5-45.8,36.8-68.6c1.8-3.4,4-6.6,6.1-10.1c7.4,7.1,7.8,7.9,4.3,14.8
                        c-2.9,6-6.3,11.7-9.3,17.7c-9.1,18.1-18.3,36.2-27.1,54.5C174.2,1642.4,173.9,1646.4,172.7,1650.9z M371.8,1116.1
                        c0.8,0.3,1.6,0.5,2.5,0.8c3.6-5.5,7.4-10.8,10.7-16.5c14.4-24.7,30.6-48.6,42.6-74.4c26.3-56.7,42.6-116.7,51.8-178.6
                        c1.5-10.2,2-20.6,2.2-30.9c0.2-10.5-4.9-14.4-15.2-12.8c-1.3,0.2-2.6,0.4-3.9,0.9c-16.4,5.5-32.9,10.7-49.2,16.5
                        c-15.7,5.6-28.9,15.3-39.1,28.4c-24.6,31.8-37.7,67.8-37.3,108.3c0.2,19.4,4.5,37.7,15.9,53.7c10,14.1,14.4,29.7,15.2,46.7
                        C369,1077.7,370.6,1096.9,371.8,1116.1z M386.1,822.8c4.2-2.4,7.7-4.3,11.1-6.2c17.1-9.8,35.2-17.1,54.3-22.3
                        c14.7-4,29.3-8.8,43.8-13.4c7.6-2.4,13.7-7,17.6-14c13.7-24.7,27.8-49.2,40.6-74.4c5.8-11.5,9.1-24.2,13.3-36.5
                        c2.5-7.2,0.1-13.1-5.8-17.6c-4.8-3.6-9.6-7.2-14.4-10.8c-7.4-5.5-15.6-7-24.6-5.2c-5.5,1.1-11.1,2.2-16.6,3.2
                        c-51.2,9.1-86.1,39.4-109.2,84.6c-3.1,6.1-5.1,13-6.5,19.8c-5.9,29.2-6.4,58.8-5.1,88.5C384.5,819.5,385.1,820.4,386.1,822.8z
                        M546.6,1098.4c0.8-0.6,1.6-0.8,1.9-1.3c0.8-1.4,1.6-2.9,2.1-4.4c6.2-16.9,16.5-31,28.9-43.8c7-7.1,13.8-14.4,20.5-21.8
                        c11.7-12.8,19.3-27.7,22.5-44.9c2.2-11.4,4.7-22.8,6.9-34.2c3.4-17,6.7-33.9,9.9-50.9c2.4-12.8,1-15-11-19.1
                        c-9.1-3.1-18.2-6.3-27.2-9.7c-17.8-6.6-35.4-13.4-53.2-20c-11.1-4.1-13.2-2.7-14.4,9.4c-1.3,13.2-2.6,26.5-3.3,39.8
                        c-3.3,62.7,2.5,125,9.7,187.2C540.7,1089.8,541.6,1095.2,546.6,1098.4z M306.8,1428.6c1.8-1,2.6-1.1,2.9-1.6c1.1-1.7,2.1-3.4,3-5.2
                        c38.4-75.8,73.8-152.9,101.3-233.4c3.9-11.3,7.2-22.8,10.2-34.4c5.2-20.3,10.5-40.6,19.7-59.6c5.6-11.6,5.5-23.9,3.4-36.2
                        c-0.6-3.2-2.8-6.2-4.9-10.5c-3.6,3.4-6.1,5.4-8.2,7.9c-10,12-19.8,24.2-29.9,36.3c-14.4,17.3-25.7,36.5-33.5,57.6
                        c-10.3,27.8-20.9,55.5-30.1,83.6c-17.4,53.3-29.7,107.8-33.8,163.9C306.2,1407.1,306.8,1417.4,306.8,1428.6z M230,1375.1
                        c0.5,0.3,0.9,0.7,1.4,1c0.8-0.5,1.8-0.8,2.4-1.5c13.3-17.5,27.2-34.5,39.6-52.7c47.5-69.6,78.9-145.3,85.7-230.2
                        c1.4-16.9-0.1-33.3-6.6-48.9c-1.6-3.7-4.2-8.4-7.5-9.6c-5.1-1.9-7.8,3.6-10.5,7.3c-23.9,32.7-40.3,68.9-48.1,108.6
                        c-4.1,21.2-5.9,42.8-8.7,64.2c-6.8,52.5-20.8,102.9-43.1,150.9C232.8,1367.9,231.5,1371.5,230,1375.1z M551.5,614.4
                        c6.9,4.8,13.4,8.4,18.8,13.2c5.9,5.3,11.5,5.8,18.2,2.5c13.5-6.6,27.5-6.7,41.8-2.8c22.5,6.1,44.9,12,67.5,17.8
                        c5,1.3,10.2,2,17.5,3.4c-2.8-3.9-4-6.2-5.7-8c-19.6-21.2-40.8-41.2-54.1-67.5c-1.8-3.5-4.9-4.2-8.5-2.9c-1.9,0.7-3.7,1.5-5.5,2.3
                        c-26.9,12.3-53.8,24.6-80.7,37C557.8,610.8,555,612.5,551.5,614.4z M459.5,799.5c-14.6,0.4-39.6,9.2-48.6,16.7
                        C427.7,810.4,443.6,805,459.5,799.5z M548.5,717.6c-0.8-0.4-1.5-0.9-2.3-1.3c-4.7,9.3-9.3,18.5-14,27.8c0.6,0.3,1.2,0.6,1.8,0.9
                        C538.8,735.9,543.7,726.7,548.5,717.6z"/>
                    <path d="M1023.4,872.9c-2.4-16.1-4.9-32.2-7-48.3c-1.6-12.6,0.3-23.8,17.5-27.3c-3.8-2.1-5.5-3.4-7.5-4.1c-6.5-2.4-13.1-4.8-19.8-7
                        c-9.9-3.4-17.4-9.6-22.3-18.9c-14.7-28-29.8-55.8-43.9-84.1c-4.6-9.1-7.1-19.3-9.7-29.3c-2.1-7.9,1.6-14.3,7.8-19.2
                        c4.9-3.8,10-7.5,16.4-12.3c-8.9-1.1-14.4,2.6-18.2,7.1c-8.8,10.4-17.6,11.2-29.5,4.7c-11.2-6.2-23.7-4.8-36.1-1.4
                        c-25.1,6.8-50.2,13.2-75.4,19.6c-3.2,0.8-6.5,1.6-9.8,1.5c-5.9-0.2-8.1-4.4-5.1-9.6c1.3-2.3,3.3-4.2,5-6.2
                        c9.4-10.4,18.8-20.7,28.3-31c9.6-10.3,17.9-21.4,24.9-33.7c6.3-11.3,8.8-11.9,20.8-6.5c28.8,13,57.7,25.8,86.2,39.5
                        c12.5,6,25.3,9.7,39,11.3c52.8,6.2,92.7,32.7,121,77.4c10.3,16.2,14.6,34.1,15.6,53.1c1.1,23,2.3,45.9,3.3,68.9
                        c0.2,3.7-0.7,7.5-1.3,12.8c-7.8-3.7-13.9-6.7-20.1-9.6c-0.3,0.3-0.5,0.7-0.8,1c1,0.8,2,1.8,3.1,2.4c13.2,6,22.2,16.4,30.7,27.7
                        c25.6,33.9,36.9,71.9,35,114.1c-0.8,17.7-5.8,34-16.2,48.6c-4.8,6.7-9.3,13.7-9.7,23.8c2.6-3.1,4.1-5.1,5.9-7
                        c5.4-5.5,9.7-5.7,15.6-0.4c2.7,2.4,5,5.4,7.1,8.3c30.6,42.7,48.3,90.2,53.2,142.4c6.5,68.2,21.5,134,53.4,195.2
                        c1.8,3.4,5.1,7.7,0.7,10.7c-4.2,2.9-7.3-1.4-10-4.2c-7.4-7.6-14.6-15.4-21.9-23.1c-1.5-2.5-3-5-4.5-7.5c-0.6,0.4-1.2,0.9-1.8,1.3
                        c1.9,2.3,3.9,4.5,5.8,6.8c7.9,14.5,18.7,26.6,30.9,37.5c2.3,2.1,5.8,3.1,8.9,4.1c10.1,3.1,18.7,8.4,26.1,16
                        c15.9,16.6,32.2,32.8,47.9,49.6c7.3,7.7,15.5,13.5,25.4,17.2c4.3,1.6,8.7,3.5,12.6,6c5.2,3.4,5.8,7.4,2.8,12.9
                        c-5.3,9.3-15.9,13.9-27.6,11.5c-9.8-2-19.5-4.3-29.3-6.4c-6.3-1.3-6.3-1.3-13.9,3.2c1.5,3.1,2.9,6.3,4.6,9.3
                        c12.8,22.2,25.7,44.4,38.5,66.6c3.2,5.5,5.8,11.2,5.5,17.8c-0.2,4.9-1,9.5-5.6,12.1c-4.8,2.7-9,0.6-12.9-2.4c-1.3-1-2.6-2.1-5.4-4.3
                        c1.1,3.6,1.4,5.6,2.2,7.4c2.6,6.1,5.8,12,8.1,18.2c2.5,6.9,0.3,13.1-5.2,16.6c-5.9,3.8-12.6,2.8-18-3c-2.5-2.7-4.5-5.8-6.6-8.8
                        c-1.9-2.7-3.7-5.4-6.9-7.6c1.1,3.3,2.3,6.6,3.2,10c2.5,9.6-2.2,14.9-12,13.8c-8-0.9-13.2-5.6-16.6-12.7
                        c-5.8-12.3-11.8-24.6-17.7-36.9c-1.6-3.3-3.3-6.4-6.5-9.2c0.6,1.8,0.9,3.8,1.7,5.5c2.4,5.5,5.5,10.7,7.3,16.4c0.8,2.4,0.2,6.6-1.5,8
                        c-2.1,1.8-6.3,2.9-8.9,2.1c-3.2-0.9-6.5-3.6-8.5-6.4c-6.5-9.2-12.4-18.9-18.7-28.3c-11.8-17.7-23.7-35.4-35.5-53.1
                        c-4.5-6.6-9.4-13-13.4-19.8c-8.3-14.2-10.4-29.6-5.4-45.3c7-22.4,8.8-44.8,5.3-68.1c-2.4-16.1-2.4-32.5-4.5-48.7
                        c-2.5-19.8-6.1-39.4-9.1-59.2c-0.9-6.4-1.2-12.9-1.9-21.1c9.2,4.5,11.7,12.2,16.3,17.9c4.7,5.8,8.8,12.1,14.1,17.7
                        c-43.3-68-72.7-141-79-222.3c-0.9,0.2-1.8,0.3-2.8,0.5c0,4.4,0,8.9,0,13.3c-0.1,7.6,0,15-2.7,22.6c-1.5,4.3,0.5,10.4,2.2,15.3
                        c7.4,21,15.6,41.7,23.1,62.8c16.9,47.8,29.9,96.7,37,146.9c3.7,26,4.4,52.4,6.5,78.6c0.1,1.6,0.2,3.6-0.5,4.9
                        c-1.1,1.8-2.7,3.9-4.5,4.5c-1.2,0.4-3.4-1.5-4.8-2.7c-0.9-0.8-1.3-2.3-1.9-3.5c-21.3-44.8-43.4-89.2-63.6-134.5
                        c-24.1-54.1-44.2-109.9-60-167.1c-3.5-12.5-8.3-24.6-12.8-36.8c-4.3-11.7-7.2-23.6-5.6-36.2c0.5-3.9,1.2-7.9,2.5-11.7
                        c3.4-9.5,9.4-11,16.4-3.9c5.4,5.4,10.3,11.4,15.3,17.2c4.6,5.3,9,10.7,13.6,16c0.3,0.3,1,0.4,2.7,1c-1.2-2.2-1.7-3.6-2.6-4.8
                        c-20.3-26.9-33.1-57.6-43.7-89.3c-1.1-3.3-2.9-6.4-4.3-9.6c0,0,0.3,0.4,0.3,0.4c-1.4-4.8-2.8-9.6-4.2-14.4c-0.3-1.9-0.6-3.8-0.9-5.8
                        c-1.7-4.8-3.5-9.6-5.2-14.3l0.2,0.4c-1.4-5.1-2.8-10.2-4.2-15.4c-0.3-2.2-0.6-4.5-0.8-6.7c-3.7-13.8-7.4-27.5-11.2-41.3
                        C1023.8,878.2,1023.6,875.6,1023.4,872.9z M1084,812c-3.4-1.3-6.8-2.6-10.1-3.9c-7.5-4.9-11.2-5.9-20.2-4.5c8.3,1.7,14.3,3,20.4,4.3
                        c3.4,1.3,6.8,2.6,10.1,3.9c3.2,3.7,7.6,4.7,12.5,4.2C1092.4,814.7,1088.2,813.4,1084,812z M1313.8,1525.3c2.1,3.2,4.3,6.5,6.3,9.9
                        c12.8,21.5,25.5,42.9,38.3,64.4c1.9,3.1,3.9,6.2,6.3,9c2.2,2.5,5.5,5.4,8.5,2.7c2-1.8,3.3-6,2.8-8.7c-1-4.8-3.2-9.4-5.6-13.7
                        c-13-22.9-26.2-45.6-39.3-68.5c-2.1-3.7-3.8-7.5-5.8-11.4c8.1-6.7,16.4-6.9,25.2-4.9c9.4,2.1,18.8,4.6,28.3,6
                        c11.1,1.7,17.2-2.3,21.2-13.5c-5.7-2.8-11.6-5.5-17.3-8.5c-5.8-3.1-13-5.3-16.8-10.1c-16.8-20.9-36.8-38.6-55.8-57.2
                        c-5.1-5-11.6-9.6-18.3-11.7c-10.7-3.5-18.9-9.4-25.7-17.9c-4.6-5.7-9.6-11.1-13.8-17.1c-15.9-22.2-31.5-44.7-47.2-67
                        c-2.2-3.1-4.8-5.9-7.2-8.8c-1.4,3.2-1.3,5.4-0.9,7.6c1.7,8.8,4,17.6,5,26.5c3.5,30.4,6.9,60.9,9.7,91.4c1.3,14.2,1.5,28.5-3.6,42.5
                        c-9.8,26.8-4.9,51,13.4,72.7c15.7,18.7,28.9,39,40.4,60.6c4.2,7.9,9.9,14.9,15.3,22.2c2.5,3.3,5.6,5.8,10,1.2
                        c-5.2-10.6-10.4-21-15.3-31.5c-3.9-8.3-2.9-10.1,6.7-13.8c2,3.7,4.1,7.4,6,11.2c8.5,17,16.8,34,25.5,50.9c1.9,3.7,4.8,7.3,8.2,9.6
                        c2.7,1.8,7.7,3,10,1.7c4.2-2.4,1.8-7,0.3-10.7c-0.6-1.5-1.3-3.1-2.1-4.5c-10-19.6-20-39.2-29.9-58.7c-2-3.9-4.3-7.6-6-11.5
                        c-2.7-6.4-1.9-7.9,6.1-12.4c1.7,3.1,3.5,6.2,5.3,9.4c12.6,23.5,25.2,46.9,37.9,70.3c2.4,4.4,5.1,8.6,8.3,12.4
                        c4.2,5.1,8.1,5.9,12.1,3.3c4.5-3,4.5-7.1,2.6-11.5c-1.5-3.7-2.9-7.4-4.8-10.9c-15-27.8-30.1-55.6-45.2-83.4
                        c-1.4-2.6-3.1-5.2-4.3-7.9C1305.9,1529.5,1306.3,1529.1,1313.8,1525.3z M1129.8,1116.7c0.6-0.2,1.2-0.4,1.8-0.6
                        c0.4-3.5,1-7,1.1-10.5c0.5-12.6,0.7-25.3,1.3-37.9c1-21.3,6-41.3,18.5-59.2c8.6-12.2,12.8-26.2,13.8-41.2c2.6-39.4-8-75-29.5-107.8
                        c-10.9-16.6-25-29.8-43.4-37c-18.5-7.2-37.7-12.9-56.8-18.6c-8.6-2.6-13.5,1.2-14.5,10.1c-0.6,4.9-0.3,10,0.2,14.9
                        c5.4,48.1,15.7,95.1,31.6,140.9c11.4,32.7,22.4,65.5,41.8,94.7c9.8,14.7,19,29.7,28.5,44.6C1126,1111.8,1128,1114.2,1129.8,1116.7z
                        M1118.4,822.5c0.7-2.3,1.1-3.2,1.2-4.1c0.5-29,0.4-57.9-5.1-86.6c-2.6-13.3-7.4-25.4-14.8-36.7c-25.9-40-62.3-63.3-109.3-71.5
                        c-20-3.5-35.1,3-49,15.9c-4.9,4.6-6.5,10-4.9,16.3c1.2,4.8,2.8,9.6,4.4,14.3c12.8,35.6,32.3,67.8,52.2,99.7
                        c2.6,4.1,7.6,7.7,12.3,9.5c13,5,26.3,9.4,39.7,13.1c22.8,6.4,45.1,14,65.5,26.3C1112.7,820.1,1115.2,821,1118.4,822.5z
                        M1195.8,1429.7c0.8-0.2,1.5-0.4,2.3-0.7c0-3.2,0.3-6.5,0-9.6c-2.8-26.8-4.5-53.8-8.9-80.3c-10.6-64.6-32-126.3-55.2-187.3
                        c-14.3-37.6-39.7-68.3-66-98.1c-1.1-1.2-2.4-3-3.8-3c-2-0.1-4.8,0.4-5.8,1.7c-1.5,2-2,4.9-2.4,7.5c-1.8,12.8-0.4,25.1,4.3,37.2
                        c4.4,11.1,8.6,22.4,12.1,33.8c4.6,14.9,7.3,30.5,12.8,45.1c17.3,46.1,34.2,92.4,53.6,137.6c16.4,38.2,36,74.9,54.3,112.3
                        C1193.9,1427.2,1194.9,1428.4,1195.8,1429.7z M1158.2,1032.4c-1.9,2.5-4.3,5.2-6.1,8.2c-5.7,10.2-7.1,21.3-7.3,32.8
                        c-0.4,28.1,3.1,55.8,10,83.1c20,79.6,58.4,149.7,110.7,212.4c1.6,1.9,3.6,3.6,5.4,5.3c0.4-3.8-0.6-6.9-1.9-9.7
                        c-19.7-42.9-33.8-87.4-39.7-134.4c-3.2-26.1-6.7-52.2-11.6-78c-7.5-40.1-23.8-76.8-47.6-110
                        C1167.4,1038,1164.8,1033.5,1158.2,1032.4z M949,614.3c-2.4-1.5-3.4-2.4-4.6-2.9c-29-13.3-58-26.5-87-39.7
                        c-7.8-3.5-8.8-2.9-13.9,4.2c-9.3,12.9-18.4,26-28.3,38.5c-6.8,8.6-14.8,16.2-22.2,24.3c-2.1,2.3-3.8,5-5.7,7.5
                        c0.4,0.7,0.8,1.3,1.2,2c5-1.1,10.1-1.9,15-3.2c22.8-5.9,45.7-11.7,68.4-17.9c13.4-3.7,26.2-3.4,38.9,2.5c2.4,1.1,4.9,2.5,7.4,2.8
                        c2.4,0.3,5.6,0.3,7.5-1C933.3,626.2,940.6,620.5,949,614.3z"/>
                    <path d="M809.6,1199.6c13.1-2,26.3-3.3,39.2-6.3c5.1-1.2,10.2-5.1,13.9-9c16.3-17.2,23.3-38.5,25.3-61.6c2.4-26.5-1.1-52.5-6.9-78.3
                        c-1.1-5.1-1.4-10.3-2.4-18.1c6.5,3.7,11.1,5.5,14.9,8.5c16.6,13.1,30.9,28.4,43.1,45.6c9.9,13.9,11.9,28.4,5.1,44.3
                        c-7,16.4-15.5,31.7-27.7,44.8c-11.5,12.5-25,21.9-41.4,27c-2.5,0.8-4.9,1.8-7.4,2.7c0.5,54-10.9,105.7-24.3,157.3
                        c0.7-1.3,1.6-2.6,2.1-3.9c14.2-39.6,26.5-79.6,27.3-122.2c0.1-4.3,0.8-8.6,1.4-12.9c1.2-8.8,5.4-16,13-20.3
                        c25.7-14.3,43.5-35.8,56.9-61.4c1.2-2.3,2.8-4.3,5.3-7.9c2.5,3.9,5.1,6.5,6,9.6c9.7,34.4,17.5,69.1,13.1,105.3
                        c-1.6,13-4.9,25.5-10.3,37.4c-24.3,53.9-59.4,99.3-107,134.6c-7.7,5.7-16.4,10.2-25,14.5c-8.2,4.2-8.6,3.8-16.4-2.7
                        c-14.8,17.4-32.4,30.9-56.6,30.6c-24.5-0.3-41.4-14.4-56-34c-0.9,3.4-1.5,5.7-2.4,9.2c-5.1-1.3-10.2-1.8-14.5-3.8
                        c-7.2-3.4-14.4-7.1-20.7-11.9c-45.8-34.5-81.1-77.6-105.9-129.4c-14-29.3-17.9-59.7-12.4-91.5c3-17.4,6.5-34.7,9.8-52
                        c0.5-2.6,1.1-5.3,2.2-7.7c2.5-5.3,7.5-5.7,11-1c1,1.3,1.5,2.9,2.4,4.4c13.1,23.3,29.6,43.5,53.6,56.5c8.8,4.8,13.5,12.5,14.6,22.5
                        c2.4,21.5,4.4,43.1,7.7,64.5c3.8,25.1,12.9,48.8,22,72.5c0.2,0.4,0.8,0.7,2.7,2.2c-8.5-26.8-15.3-52.3-19.3-78.6
                        c-4-26-7.8-52-6.3-78.4c-1.7-0.9-2.7-1.7-4-2c-21-5.2-37.5-17.3-49.6-34.8c-8-11.5-15.2-23.5-21.7-35.9
                        c-10.3-19.7-7.5-35.9,7.6-52.1c12.5-13.4,25.2-26.7,37.8-39.9c3.4-3.6,6.9-7.4,13-6.3c3.8,3.6,2,7.9,1.3,12
                        c-2.2,12.8-5,25.5-6.6,38.4c-2.7,21.9-2.9,43.9,2.9,65.5c4.2,15.4,11.8,28.9,22.4,40.8c4.2,4.7,9.5,6.6,15.6,8
                        c23.9,5.6,47.8,11.5,71.6,17.8c8.8,2.3,13.9,9.1,15.1,18.1c1.5,11.6,2.3,23.2,3.4,34.8c0.9,9.9,1.9,19.8,4.2,29.9
                        c0.4-2.7,1-5.5,1.3-8.2c1.5-15.9,3.3-31.8,4.4-47.7c1.1-15.4,7.9-25.8,23.4-29.6c1.2-0.3,2.2-1.1,3-3.4c-3,0.5-6,1-9,1.5
                        c-13.6,2.3-20.3-2.9-21.1-16.8c-0.6-10-0.6-20-0.7-30c-0.1-16.3-0.5-32.7,0.1-49c0.4-9.2,2.2-18.4,3.9-27.6
                        c1.3-7.4,6.1-11.4,13.8-12.2c33.4-3.2,65,2,94.3,19.2c9.9,5.8,13.8,13.7,12.3,24.9c-2.9,21.7-9.5,42.1-20.5,61.1
                        c-4.7,8.1-11.8,12.5-20.5,14.8c-9.3,2.5-18.6,4.6-28,6.8C809.1,1199,809.3,1199.3,809.6,1199.6z M746.4,1451.7
                        c0.3-6.1,0.9-11.7,0.9-17.2c-0.1-34-0.3-68-0.7-101.9c-0.4-32-2.1-63.9-6.4-95.6c-1.7-12.6-8.2-19.8-20.3-22.7
                        c-15.2-3.8-30.3-7.9-45.4-11.6c-5.1-1.3-10.4-2.4-15.7-2.8c-11.6-0.8-15.3,2.5-15.1,14.2c0.1,9,1.2,17.9,2.1,26.9
                        c4,39.2,11.2,77.7,23.9,115.1c8.3,24.4,18.4,47.8,33.8,68.7C714.1,1438.9,728,1447.6,746.4,1451.7z M756.9,1451.7
                        c18.8-3.6,31.7-12.3,42-25.2c10.7-13.4,18.4-28.5,25.1-44.2c18.8-44.2,28.9-90.6,33.9-138.1c1.1-10.3,1.8-20.6,1.7-30.9
                        c-0.1-10.9-5-14.8-15.9-14.2c-1.3,0.1-2.6,0.4-4,0.5c-21.9,3-42.8,9.8-63.4,17.5c-6.5,2.4-10.5,7-11.4,13.9
                        c-1.8,14.2-4.7,28.3-5.1,42.6c-1.4,49.9-2.1,99.9-2.9,149.9C756.8,1432.3,756.9,1441.2,756.9,1451.7z M816.3,1423.5
                        c9.8,1.2,16.3-3.6,22.7-8.2c49.2-35.4,86.1-81,111.3-136.1c8.4-18.3,12.1-37.3,11.6-57.2c-0.6-24.1-4.5-47.7-10.2-71.1
                        c-0.9-3.6-2.7-7.1-4.6-12.1c-3.6,5.7-6.3,9.9-8.9,14c-11.3,18-25,33.9-43.7,44.4c-12.9,7.2-17.6,17.4-18.6,31.7
                        c-1.3,18.9-3.6,37.8-7.2,56.4c-8.5,43.4-25,84.1-45.2,123.3C820.9,1413.3,818.8,1418.1,816.3,1423.5z M687.8,1423.6
                        c-1.6-3.6-2.9-6.9-4.5-10.2c-4.7-9.6-9.6-19-14.1-28.7c-18.1-38.4-32.1-78.2-38.1-120.3c-2-14.1-2.7-28.5-3.6-42.7
                        c-0.5-8.5-3.1-15.7-10.9-20c-23.2-12.6-40.2-31.4-53.8-53.6c-1.6-2.6-3.7-4.9-6.7-8.7c-1.5,4.9-2.7,7.6-3.2,10.5
                        c-3.1,16.7-6.3,33.3-9.2,50c-5.1,29.5-0.3,57.6,12.4,84.5c15.9,33.7,36.7,64.1,62.6,90.9c15.8,16.3,32.8,31.2,51.7,43.9
                        C675.4,1422.5,680.4,1425.9,687.8,1423.6z M759.9,1145.1c-0.1,0-0.2,0-0.3,0c0,14-0.2,28,0,42c0.2,14.4,4.4,17.5,18.8,14.1
                        c1.9-0.5,3.9-0.9,5.8-1.4c15.8-4,31.6-8.2,47.4-12.1c11.4-2.8,19.5-9.4,24.7-20c7.8-15.6,13-32.1,15.7-49.3
                        c2.2-13.8,0.5-17.1-11.9-24.2c-23.8-13.6-49.6-18.9-76.8-18.1c-18.6,0.6-21.6,1.8-23.3,22C758.9,1113.7,759.9,1129.4,759.9,1145.1z
                        M865.3,1192.1c2.9-0.5,4.5-0.7,6-1.1c14-4.3,26-11.8,36.1-22.4c13.1-13.6,22.6-29.5,29.5-47c5.3-13.6,3.8-25.9-4.6-37.7
                        c-11.4-16.2-24.7-30.5-40-43c-1.5-1.2-3.6-1.8-5.6-2.9c-0.3,1.6-0.6,2.3-0.5,2.9c0.2,1.6,0.5,3.3,0.9,4.9
                        c5.4,23.1,7.7,46.6,6.8,70.3c-1,25.6-7.4,49.3-24.4,69.2C868,1187,867.2,1189.1,865.3,1192.1z M617.9,1039.1
                        c-0.7-0.3-1.4-0.5-2.1-0.8c-1.1,0.7-2.4,1.1-3.2,2c-13.9,14.8-27.9,29.5-41.5,44.6c-9.2,10.2-10,22.3-5,34.6
                        c7.6,18.6,18,35.6,31.7,50.5c8,8.7,17.5,15.4,28.4,19.9c3.5,1.4,7.4,1.8,11.3,2.7c0-1.9,0.2-2.7,0-3.2c-1-1.7-2.2-3.3-3.4-4.9
                        c-10.7-12.8-17.3-27.4-20.7-43.7c-6-28.9-3.9-57.6,1.8-86.2C616.1,1049.4,616.9,1044.2,617.9,1039.1z"/>
                    <path d="M983,1943.7c-0.6-1.1-1.2-2.2-2.2-4.1c-0.4,1.7-1,2.9-1,4c-0.8,35.3-1.5,70.6-2.3,105.9c-0.1,4.9-0.5,9.8-0.9,17.4
                        c-4.1-3.9-7-5.8-8.9-8.5c-20.7-29.4-41.7-58.7-59.1-90.2c-15.7-28.3-24.6-58.9-30.3-90.5c-10.1-55.9-15.6-112.2-14.4-169
                        c0.7-32.1,7.1-63.5,14.8-94.5c13.5-54.2,27-108.5,40.5-162.7c0.4-1.5,0.4-3.2,0.8-5.9c-7.2,12.2-28.6,83.4-50.4,169.3
                        c-14.5,57-28,114.2-42.1,172c-5.1-0.9-6.4-5-7.3-9.1c-6.1-28.3-12.8-56.5-18-85c-6.5-36-12.7-72.2-17.3-108.5
                        c-3.4-27-4.1-54.4-5.9-81.7c-0.2-3.3,0.3-6.7,0.4-10c0.5-17,8.6-28.1,23.9-36c47.8-24.9,87-59.9,117.7-104.4
                        c4.7-6.8,8.7-14.2,13.1-21.3c1.6-2.6,3.3-5.6,7-4.1c3.5,1.4,3.2,4.7,2.7,7.8c-1.2,7.5-2.5,15-2.4,22.9c2.2-7.3,4.2-14.6,6.6-21.8
                        c1.1-3.4,2.5-7,4.6-9.7c2.6-3.3,6.4-3,8.1,0.8c1.7,3.8,2.6,8.2,2.7,12.4c0.3,24.3-0.4,48.7,0.5,73c0.8,22.9,2.8,45.9,4.8,68.8
                        c3.5,39.1,7.5,78.2,11.1,117.4c2.2,23.2,4.8,46.4,5.8,69.7c1.4,34.6,1.6,69.3,2.2,103.9c0.1,5.7,0,11.3,0.7,17
                        c1.5-34.9,5.3-69.7,2.3-104.6c-4.5-52.4-9.7-104.8-14.4-157.2c-4.4-48.4-10.5-96.7-9.4-145.5c0.7-31.3,1.7-62.6,2.6-93.9
                        c0.1-4.1,0.1-8.1,6.3-9.6c1.2,2,3,4,3.6,6.3c12.9,49.3,25.8,98.5,38.2,147.8c10.5,41.7,16.3,84.3,20.3,127.1
                        c1.5,16.6,4,33.1,6.1,49.6c3.9,30,0.6,59.7-3,89.6c-9.3,76.3-31.1,149.8-50.2,223.9c-1.8,7-3.8,13.9-5.8,20.8
                        C984.5,1943.1,983.8,1943.4,983,1943.7z M983,1862.4c0.9-2.7,2.4-5.4,2.6-8.1c0.9-12.3,1.6-24.6,2.1-36.9c0.2-4.9,0-9.8,0-14.7
                        c-2.1,17.3-2.9,34.6-3.7,51.9C983.9,1857.2,983.4,1859.8,983,1862.4c0,10.6,0,21.1,0,31.6C983,1883.5,983,1873,983,1862.4z
                        M968.8,2049.5c0.8-0.3,1.6-0.5,2.5-0.8c0.2-3.9,0.5-7.8,0.7-11.7c0.5-16,0.9-32,1.5-47.9c2.8-68.6,5.6-137.1,8.5-205.7
                        c1.3-31.6,3-63.2,0.3-94.9c-1.7-19.2-2.6-38.5-4.2-57.8c-2.9-33.5-6-67-9.2-100.5c-2-20.2-5.4-40.3-6.4-60.6
                        c-2.2-42.9-3.2-85.9-4.7-128.8c-0.1-1.7-0.5-3.4-0.8-5.1c-3.5,3.6-5.6,7.3-6.6,11.3c-22.6,91.4-45.2,182.8-67.6,274.2
                        c-9,36.7-14.2,73.8-13.5,111.7c0.9,48.7,6.3,97,14.8,144.9c6.7,38.1,18.8,74.1,40.8,106.4c12.9,19,25.4,38.3,38.1,57.5
                        C964.7,2044.5,966.8,2047,968.8,2049.5z M824.6,1767.7c0.7,0,1.4,0,2.1,0c1-3.3,2.4-6.6,3.1-10c22.6-104,49.8-206.9,81.7-308.5
                        c10.4-33,19.2-66.4,24.9-100.6c0.4-2.3,0-4.7,0-7.1c-3.1,2-4.6,4.1-6.1,6.3c-6.9,9.8-12.9,20.4-20.8,29.3
                        c-29.8,34-63.4,63.7-103.9,84.8c-2.9,1.5-5.8,3.3-8.6,5c-8.3,4.8-11.7,11.8-11.7,21.5c-0.1,34.7,2,69.1,6.1,103.6
                        c6.8,56.2,16.1,111.9,30.4,166.8C822.6,1761.8,823.7,1764.8,824.6,1767.7z M989.2,1894.2c0.8,0.1,1.5,0.2,2.3,0.3
                        c1.6-5,3.3-10,4.7-15.1c3.8-13.8,7.5-27.6,11.1-41.4c14.4-55.4,27.6-111.1,31.7-168.4c1.5-21.6,2.2-43.2-0.5-64.8
                        c-2.3-18.5-4.2-37-6.6-55.5c-2.9-22.8-5.1-45.7-9.5-68.2c-11.6-59.1-27.9-117.1-44-175.1c-0.5-1.7-1.4-3.2-2-4.8
                        c-1.9,2.3-2.3,4.4-2.4,6.5c-0.5,12.3-1.2,24.6-1.4,36.9c-0.8,40.6-1.4,81.2,3.3,121.7c3,26.1,5.7,52.3,8.3,78.4
                        c3.3,33.1,6.8,66.2,9.1,99.4c2.1,29.2,4,58.5,3.5,87.7c-0.8,42.3-3.9,84.5-6,126.7C990.1,1870.5,989.7,1882.3,989.2,1894.2z"/>
                    <path d="M529.9,2064.1c-3.1-51.3-0.7-102.7-5.9-154c0,10.6,0,21.2,0,32.5c-5.2-1.2-5-5-5.7-7.8c-6.6-25.4-13.4-50.8-19.6-76.4
                        c-8.7-35.5-18.6-70.9-25.2-106.8c-7.1-38.6-15.2-77.3-13.2-116.9c1-20.6,3.5-41.1,5.8-61.6c2.8-25.2,5.7-50.3,9.1-75.4
                        c4.2-31.7,12.1-62.7,20.2-93.6c10.2-38.9,20.3-77.9,30.4-116.9c1.1-4.2,1.3-8.8,8.2-9.3c0.7,2.6,1.8,5.4,1.9,8.1
                        c1.2,26.3,3.2,52.6,3.1,78.9c-0.1,33.6-1.4,67.3-3.2,100.9c-1.1,20.6-4.2,41.1-6.3,61.6c-3.1,30.8-6.1,61.6-9.2,92.5
                        c-4.1,40.1-7.9,80.3-6.2,120.7c1.5,35.9,3.6,71.9,5.4,107.8c0.3,5.9,0.7,11.9,1.3,17.8c0-16.3,0.3-32.5-0.1-48.8
                        c-0.7-35-2.7-69.9-2.5-104.9c0.1-25.6,2.5-51.2,4.5-76.8c2.8-35.5,6-71,9.3-106.5c1.9-20.2,4.7-40.4,6.1-60.6
                        c2.1-28.6,3.5-57.2,4.7-85.8c0.6-14.6,0.1-29.3,0.1-44c0-3-0.3-6.2,0.6-8.9c0.9-2.5,3.1-5.8,5.1-6.1c2-0.3,5.2,2.2,6.7,4.3
                        c1.6,2.3,1.9,5.6,2.7,8.5c2,7.6,4,15.3,7.7,22.7c-1.5-10.1-2.9-20.2-4.3-29.6c4-3.5,6.7-2.3,8.8,0.1c2,2.2,3.7,4.7,5.2,7.3
                        c28.4,49.2,68.7,86.2,117.5,114.4c4,2.3,8.2,4.5,12.1,6.9c13.2,7.9,21.1,19.4,21.2,35.1c0.1,15.3-0.1,30.7-1.2,45.9
                        c-5.4,76.3-18.5,151.3-36.5,225.5c-1.1,4.5-1.9,9.1-3.1,13.6c-1,3.9-2.5,7.6-8.4,9c-25.3-117.8-55.1-234.3-93.2-348.8
                        c0.4,2.3,0.6,4.7,1.1,6.9c16.3,65.9,32.9,131.7,48.6,197.7c4.1,17.4,6.9,35.4,7.6,53.3c2.9,70.9-4.2,141.1-20.3,210.2
                        c-6.6,28.5-18.3,54.9-34.2,79.4c-14.9,22.9-30.2,45.5-45.5,68.1c-2.6,3.8-5.9,7.1-8.9,10.6C531.7,2064.6,530.8,2064.3,529.9,2064.1z
                        M535.3,2049.6c0.8,0,1.6,0.1,2.3,0.1c1.6-2,3.4-4,4.8-6.1c13.5-20.2,27.2-40.3,40.3-60.8c15.8-24.5,27.1-51.1,33.8-79.6
                        c10.1-43.2,16.4-87.1,18.8-131.3c1.7-31.6,3.2-63.3-1.3-94.8c-3-20.4-7.2-40.6-12.1-60.6c-22.3-91.5-45.1-182.8-67.8-274.2
                        c-0.8-3.2-1.9-6.3-2.9-9.5c-0.8,0.1-1.6,0.3-2.3,0.4c-0.3,4.2-0.8,8.4-0.9,12.6c-1.4,39.6-2.3,79.3-4.2,118.8
                        c-0.9,18.3-4.2,36.4-5.8,54.6c-3.7,41.1-6.5,82.4-10.6,123.5c-5.6,56.2-5.1,112.4-2.1,168.7c1.3,25.6,3.5,51.2,4.4,76.8
                        c1.8,50.6,2.9,101.2,4.3,151.9C534.2,2043.2,534.9,2046.4,535.3,2049.6z M571.6,1342.5c-0.7,0.2-1.3,0.4-2,0.7c0.2,3,0,6,0.7,8.8
                        c6.6,27.5,12.1,55.3,20.2,82.4c23.9,79.3,46.8,158.9,66.3,239.5c6.8,27.8,12.8,55.8,19.3,83.6c0.9,3.9,2.3,7.6,3.5,11.5
                        c2.8-3.5,3.7-6.7,4.4-9.9c2.4-10.7,4.6-21.5,7-32.2c17.1-76.2,29.8-152.9,30.4-231.3c0.1-14.9-5-24.8-18.5-32.2
                        c-51.4-28.5-95.7-65.2-127.3-115.6C574.5,1345.8,573,1344.2,571.6,1342.5z M531.8,1302.1c-0.9-0.2-1.8-0.3-2.7-0.5c-1,3-2.2,6-3,9.1
                        c-2.4,8.6-4.8,17.3-7.1,26c-16.5,62.7-35.6,124.8-42,189.8c-2.3,23.5-6.1,46.9-8.8,70.4c-5.2,45.4-1.5,90.4,6.4,135.2
                        c9.7,54.8,24.7,108.3,39.3,161.9c0.4,1.5,1.6,2.9,2.4,4.3c0.3-17.4-0.5-34.3-1.4-51.3c-1.5-27.9-3.4-55.9-4.8-83.8
                        c-2.2-44.7-0.8-89.2,4-133.7c3.2-29.5,6.1-59,9-88.4c3-29.2,7-58.2,8.6-87.5c1.9-34.9,1.9-69.9,2.1-104.8
                        C533.9,1333.2,532.5,1317.6,531.8,1302.1z M522.8,1892.3c0,0,0.1,0.2,0.2,0.4c0.1-0.6,0.4-1.2,0.4-1.8c0-2.7,0-5.5,0-8.2
                        c-0.2,0-0.4,0-0.6,0C522.8,1885.8,522.8,1889,522.8,1892.3z"/>
                    <path d="M608.5,2646.2c-2.5-3.4-4.7-7.1-7.5-10.3c-34.9-40.4-63.8-84.4-81.7-135c-8.6-24.3-13.7-49.3-17.5-74.7
                        c-3.8-25-8.2-49.9-11.5-75c-4.4-32.5-3.9-65.1-2.3-97.7c2.7-53.4,13.6-104.8,36.1-153.5c10.1-21.8,22.2-42.5,36.2-62
                        c3.6-5,3.6-5,11.7-6.5c-2.1,22-5,43.4-9,64.7c-0.2,4.1-0.4,8.2-0.7,12.3c0.6,0,1.2,0,1.7,0c-0.4-4.1-0.7-8.2-1.1-12.3
                        c1-1.4,2.7-2.8,3-4.3c6.7-33.7,17.2-66.3,27.6-98.9c2.2-6.9,5.7-13.4,8.7-20.1c1.6-3.6,4.1-6.3,8.3-5.9c4.7,0.4,6.9,3.5,7.7,7.9
                        c3.2,16.7,7,33.2,9.4,50c2.1,14.5,2.6,29.2,3.8,43.8c2,26.2-2.6,51.7-8.4,77.1c-1.2,5.1-2.2,10.2-2.7,15.5c5-9.3,5.2-20.4,10.8-29.3
                        c0.8,0,1.6,0.1,2.3,0.1c0.7,3.3,2.1,6.6,2,9.9c-0.7,33.6,5.3,66.3,12.5,98.8c11.1,50.4,7.2,99.9-8.8,148.6
                        c-9.4,28.8-19.1,57.4-28,86.3c-10.2,32.9-9.8,66.1-2.4,99.6c4.3,19.5,7.6,39.2,10.9,58.8c0.9,5.1,0.1,10.6,0.1,17.7
                        c-4.6-2.4-8.1-4.2-11.6-6C608.1,2645.9,608.5,2646.2,608.5,2646.2z M564.7,2046.2c-0.9-0.2-1.7-0.5-2.6-0.7
                        c-2.3,3.2-4.7,6.3-6.8,9.6c-13.1,19.8-24,40.7-33.1,62.6c-16.3,39.1-24.5,80.1-28,122.2c-2.6,31.6-4.1,63.3-0.3,94.8
                        c2.9,24.5,5.9,49,10.3,73.2c5.2,28.1,10.7,56.3,18.3,83.8c14.1,51.6,43.2,94.8,78.4,134.2c0.5,0.6,1.4,0.8,3.2,1.7
                        c-1.5-4.1-2.5-7.2-3.7-10.2c-4.1-10.5-8.5-21-12.3-31.6c-16.9-46.9-23-95.6-24.2-145.2c-0.8-35.3-1.5-70.6-3.1-105.9
                        c-2.1-46.6-5.1-93.2-7.3-139.8c-2-41.7,0-83.2,7.9-124.4C563,2062.5,563.6,2054.3,564.7,2046.2z M630.1,2159.2
                        c-0.6-0.2-1.2-0.4-1.7-0.6c-1.3,4.4-2.8,8.7-3.8,13.2c-8.8,38.6-17.8,77.1-26,115.8c-5.4,25-11.3,50.1-14,75.5
                        c-3.8,35.4-5.1,71-6.7,106.6c-0.6,12.6,0.4,25.3,1.7,37.9c3.5,33.9,13.8,66.4,22.8,99c1.8,6.4,4.2,12.8,10.5,19.5
                        c-0.7-5.4-0.9-8.6-1.5-11.8c-2.8-14.4-5.5-28.7-8.6-43c-8.3-38.1-5.4-75.3,7.2-112.1c8.6-25.2,16.9-50.4,25.2-75.7
                        c15.4-47.2,18.7-95,7.1-143.6c-6-25.3-10.9-50.8-11.2-76.9C631,2161.7,630.4,2160.4,630.1,2159.2z M612.4,1972.6
                        c-9,5.9-11.7,14.1-14.1,22.3c-6.7,22.3-13.3,44.6-19.7,67c-7.3,25.4-11.2,51.3-11.3,77.8c-0.2,57.3,2.2,114.6,6.7,171.7
                        c0.1,1.7,0.7,3.3,1.1,5c0.7,0,1.5,0.1,2.2,0.1c1.5-5.3,3.3-10.5,4.6-15.8c10-43.1,20.1-86.2,29.9-129.4c4.5-20.1,9.4-40.2,12.3-60.6
                        c5.2-36.3,1.4-72.5-5-108.3C617.1,1992.7,614.7,1983.1,612.4,1972.6z M612.4,2200.7c-0.6-0.1-1.3-0.2-1.9-0.2
                        c-6.2,26.5-12.3,53-18.5,79.5c-3.8,16.2-7.5,32.4-11.3,48.6c-0.7,2.9-1.2,6.4-5.2,6.2c-4.3-0.2-5.1-3.6-5.3-7.1c-0.1-1-0.2-2-0.2-3
                        c-1.5-21.6-3.2-43.2-4.6-64.8c-2.8-44.9-5.5-89.8-3.4-134.8c0.1-2.4-0.3-4.8-0.4-7.2c-1.7,10.9-3,21.9-3.1,32.8
                        c0,17.3,0.6,34.6,1.2,51.9c1,26.3,2.2,52.6,3.4,78.9c1.6,35.3,3.5,70.6,4.9,105.8c1,26.3,1.3,52.6,2,78.9c0.1,3,0.7,6,1.1,9
                        c0.7-0.1,1.4-0.2,2.1-0.3c0-8.9-0.1-17.8,0-26.7c0.6-40.7,3.1-81.3,11.3-121.2c7-34.2,15.1-68.2,22.7-102.3
                        C608.9,2216.7,610.6,2208.7,612.4,2200.7z M613.8,2187.4c0.5,0.1,1.1,0.2,1.6,0.3c1-4.4,1.9-8.8,2.9-13.2c-0.5-0.1-1.1-0.2-1.6-0.3
                        C615.7,2178.6,614.7,2183,613.8,2187.4z"/>
                    <path d="M900,2643.9c-3.4,2.5-6.7,5-10.6,8c-5-5.4-4.3-11.7-3.2-17.8c3.4-18.7,6.3-37.5,10.8-55.9c8.7-35.2,8.3-70-2.4-104.5
                        c-9.4-30.2-19.8-60.1-29.4-90.2c-10.2-32-16.2-64.7-12.9-98.4c1.8-18.5,4.8-37,8.3-55.3c5.6-28.8,9.5-57.8,10.6-87.2
                        c0.1-3,0.5-6,0.8-8.9c0.1-0.9,0.8-1.6,1.4-2.8c4.6,2.2,4.5,6.5,5.4,10.1c9.2,38.2,18.6,76.3,27.2,114.6c6.7,29.5,13.4,59.1,18.3,89
                        c4,24.6,5.4,49.6,7.7,74.5c1.1,12.2,1.6,24.5,3.7,36.9c0.8-15.3,1.7-30.5,2.3-45.8c2.7-62.2,5.2-124.5,7.9-186.7
                        c0.7-16,1.6-31.9,2.2-47.9c1.3-35.8-2.9-71.2-8.7-106.4c-1.7-10.2-3.3-20.4-4.8-30.6c-0.3-2.2,0-4.5,0-6.9c6.4-1.4,8.8,2.7,11.5,6.4
                        c14.2,19.8,26.4,40.8,36.6,62.9c18.9,41,29.6,84.1,33.5,128.9c1.9,21.6,4,43.2,3.6,64.8c-0.5,24.2-3,48.5-5.8,72.6
                        c-4.5,38.7-11.2,77.1-21.5,114.7c-12.6,45.9-32.7,87.9-63.7,124.3c-8.4,9.9-16.3,20.2-24.4,30.4c-1.8,2.3-3.1,5-4.6,7.5L900,2643.9z
                        M903.6,2624.3c0.7,0.5,1.4,1,2.1,1.5c9.2-11.3,18.6-22.5,27.5-34c21.2-27.4,37.2-57.5,47.3-90.7c14.9-48.9,24.9-98.7,30-149.5
                        c5.2-52.3,4.8-104.4-4.4-156.3c-9-50.5-27-97.4-55.3-140.2c-2.1-3.2-4.6-6.1-7-9.1c-0.7,0.3-1.5,0.6-2.2,0.9c1.1,8,2,16.1,3.3,24.1
                        c5.6,34.6,9.8,69.3,8.6,104.4c-0.9,28-2.8,55.9-4.1,83.9c-1.9,39.9-3.7,79.9-5.6,119.8c-1.8,37.9-2.9,75.9-5.9,113.7
                        c-3.4,42.4-13.6,83.3-31,122.4C905.6,2618.1,904.7,2621.2,903.6,2624.3z M893.3,2625c0.7,0.1,1.3,0.3,2,0.4c0.9-1.7,2.1-3.4,2.7-5.2
                        c5.2-15.1,10.7-30,15.2-45.3c9.8-33.4,16.4-67.2,14.6-102.3c-1-18.6-1.3-37.3-2.1-55.9c-0.7-16.3-0.7-32.7-3-48.8
                        c-3.2-22.7-7.8-45.2-12.7-67.6c-9.6-44.2-19.7-88.3-29.6-132.4c-0.5-2.1-1.5-4.1-3-7.9c-4,23.7-7.7,45-11.1,66.4
                        c-2.9,17.7-6.1,35.4-8.1,53.3c-3.9,35.7,1.8,70.3,12.9,104.2c8.6,26.2,17.4,52.4,26,78.7c7.3,22.3,11.3,45.1,11.2,68.7
                        c-0.1,18.1-2.8,35.8-7.5,53.2C896.9,2597.6,894.2,2611.2,893.3,2625z"/>
                    <path d="M918.1,856.1c6.6-2.2,13.2-4.3,19.7-6.6c7.5-2.6,14.9-5.7,22.6-8c9.6-2.9,12.8-0.9,14.3,9.2c1.4,9.5,2.1,19.2,2.9,28.8
                        c4.1,51.6,4.3,103.2-3,154.6c-2.1,14.5-2.9,29.2-4.7,43.7c-0.8,6.2-2.1,12.5-4,18.5c-1.8,5.7-5.1,10.7-11,15
                        c-1.6-3.5-3.4-6.2-4.3-9.2c-6.1-20.3-17.9-37-32.4-52c-7.7-7.9-15-16-22.3-24.3c-9.1-10.2-14.8-22.2-17.6-35.4
                        c-6.6-30.9-12.8-61.9-19.1-92.9c-0.3-1.6-0.7-3.3-0.8-4.9c-0.9-10.3,1.9-14.8,11.5-18.3c4.9-1.8,9.9-3.3,14.5-6.3
                        c-6.5,0.9-12.9,1.8-19.4,2.6c-14.7,1.8-29.3,0.4-43.6-3.2c-17.1-4.3-30.7-14-41.8-27.6c-2.2-2.7-4.9-5.1-7.4-7.6l0.4,0.4
                        c-0.8-2.1-1.4-4.4-2.5-6.3c-7.6-13.6-11.8-28.1-12.4-43.7c-0.9-26,0.3-51.9,4.2-77.6c3.1-20,13.7-34.5,32.9-41.6
                        c27.1-10,54.4-19.4,81.7-29c6.9-2.4,13.7-1.4,20.4,1.2c16.9,6.4,28.5,18.5,34.9,35c12.1,31.5,27.9,61,44.3,90.3
                        c4.3,7.8,7.4,16.3,10.6,24.7c3.6,9.7,1.5,18.2-6.3,25.3c-16.8,15.2-34.7,29-54.7,39.9c-2.7,1.5-5,3.7-7.4,5.6c-2.7,0.6-5.3,1.3-8,2
                        c0.2,0.6,0.5,1.2,0.7,1.8C913.4,858.7,915.7,857.4,918.1,856.1z M762.5,758.3c0.4,0,0.8,0,1.1,0c0,9.6-0.4,19.3,0.1,28.9
                        c1.6,36.7,29.1,69.1,64.9,76.5c17.2,3.6,34.4,3.2,51.5-0.8c39.3-9.2,70.6-31.9,99.1-59.1c4.3-4.1,5.4-9,3-14.5
                        c-4.2-9.8-7.8-19.9-12.7-29.2c-15.6-29.4-31.3-58.7-43.7-89.7c-5.1-12.7-14.1-21.6-26-27.8c-8-4.2-16.4-5.4-25.1-2.3
                        c-24.8,8.7-49.7,17.1-74.3,26.2c-19.1,7-30.8,21.2-33.8,41.1C764.1,724.3,763.8,741.4,762.5,758.3z M957.2,1100.1
                        c4.7-6.3,6-13,6.8-19.9c2.9-26.5,5.4-53,8.7-79.4c6.1-49,1.7-97.7-3-146.5c-0.8-7.9-3.3-9.5-10.5-6.9c-29.1,10.5-58.1,21.2-87.1,32
                        c-7.9,3-9,5.3-8.2,13.7c0.2,2,0.6,3.9,1,5.9c5.4,26.4,11.5,52.7,16.1,79.2c3.7,21.5,13.1,39.6,28.2,55c4,4,8,8,11.9,12.1
                        c14.3,15.1,27.2,31,33.5,51.4C955,1097.8,956.1,1098.7,957.2,1100.1z"/>
                    <path d="M869.9,305.1c0,4.3,0.2,8.7,0,13c-0.4,6.7,2.6,11.4,8,15c4.4,2.9,8.8,6.1,12.9,9.4c7.1,5.6,9.3,13.2,6.1,21.4
                        c-5,13-10.5,25.9-16.2,38.6c-2.9,6.4-7.9,10.4-15.3,11.4c-8,1.1-8.7,2.1-9.5,9.9c-1.3,13.6-2.6,27.2-3.8,40.8
                        c-0.8,9.7-5.3,17-13.6,22c-24.9,14.6-50.8,26.2-79.8,30.2c-12.8,1.8-25.7,1.3-37.4-3.4c-17.6-7-34.6-15.3-51.6-23.8
                        c-11.9-6-17.9-16.3-18.7-29.8c-0.7-11.6-2-23.2-3.1-34.8c-0.8-8.8-1.5-9.7-10.6-11c-5.5-0.8-9.3-3.7-11.6-8.4
                        c-6.6-13.8-13.4-27.6-19.2-41.7c-4.5-11.1-1-19.9,10-24.8c12.9-5.7,17.3-15.1,16.7-28.6c-1.1-28,5.6-54.2,20.2-78.2
                        C678,192,723.7,171,769.7,178.4c46.6,7.6,85.1,42.5,95.8,88.1c3,12.5,3.9,25.6,5.7,38.4C870.8,304.9,870.3,305,869.9,305.1z
                        M747.5,512.4c5-0.5,9.9-0.9,14.9-1.7c23.4-3.6,44.3-13.7,65.3-23.8c13.6-6.5,19.7-16.9,20.4-31.5c0.6-11.6,1.9-23.2,3.2-34.8
                        c1.1-9.9,1.3-10.1,11-12c6.7-1.3,12-4.3,14.8-10.6c5.1-11.6,10-23.2,14.5-35c2.5-6.5,1-12.5-4.7-17c-3.7-2.9-7.3-5.9-11.4-8.1
                        c-7.9-4.4-10.7-10.9-10.9-19.7c-0.3-13.6-0.6-27.3-2.4-40.8c-6.8-49.3-49-89.7-100.6-94.4c-49-4.4-96.4,25.2-113.6,72.3
                        c-7.3,19.9-10.6,40.6-9.8,61.7c0.4,11.2-2,19.8-14,23.1c-0.9,0.3-1.7,0.9-2.6,1.5c-10.8,6.4-13.1,12.5-8.2,24.1
                        c4.1,9.8,9,19.3,13,29.2c3.4,8.5,8.7,14,18.4,14.3c5.2,0.1,7.9,2.8,8.2,8c0.2,3.7,0.7,7.3,1.1,10.9c1.2,12.6,2.4,25.2,3.6,37.8
                        c0.7,7.2,4.3,12.5,10.7,15.8c14.8,7.6,29.5,15.4,44.4,22.8C723.5,510,735.3,512.1,747.5,512.4z"/>
                    <path d="M757.5,624.5c-4,0-7.2,0-11.1,0c-0.2,3.9-0.2,7.4-0.6,11c-1,9.7-5.9,12.1-14.1,6.5c-22.1-15.3-41.7-33-56.2-56
                        c-1.4-2.3-2.6-4.7-4.2-6.8c-11.9-15.4-13.4-32.8-10.4-51.2c1.5-9.2,3.4-18.2,5.3-27.3c0.3-1.5,1.4-2.9,2.4-4.8
                        c16,6.4,32.5,10.7,44.9,23.5c1.7,1.7,5.3,2.3,7.9,2.3c20.6,0,41.2-0.3,61.8-0.7c2.4,0,5.3-1,7-2.5c10.5-9.3,23-14.5,35.9-19
                        c6.7-2.3,8.9-1.6,11.9,4.7c1.7,3.6,3.2,7.4,3.8,11.3c1.3,8.9,2.3,17.8,2.9,26.8c0.7,10.2-1.5,19.9-7.3,28.5
                        c-14.6,21.5-30.5,42.1-49.5,59.9c-5,4.7-10.9,8.7-17.1,11.9c-7.9,4.2-11.9,1.7-12.9-7C757.7,632.3,757.7,628.9,757.5,624.5z
                        M671.8,501.9c-1.5,4.6-3.4,7.9-3.7,11.4c-1.2,12.9-2.2,25.9-2.5,38.8c-0.1,4,1.6,8.6,3.7,12.1c6.9,11.4,14,22.7,21.8,33.4
                        c10.5,14.3,23.4,26.4,38.2,36.3c2.8,1.9,6.1,3.1,10.7,5.5c0.4-7.6,1.2-13.2,0.9-18.7c-2-31.4-12.2-60.5-25.6-88.6
                        c-3.8-8.1-9.6-14.3-17.8-18.2C689.4,510.2,681.3,506.4,671.8,501.9z M764.3,637.8c11.5-2.7,18.7-7.3,27.7-17.9
                        c11.9-14,23.9-27.8,35-42.3c4.7-6.2,8.4-13.7,10.5-21.2c4.3-15.5,0.8-31.1-2.5-46.4c-1.2-5.4-3.7-6.9-8.8-4.7
                        c-9.1,4-18.2,8.2-26.6,13.5c-4,2.5-7.5,7-9.4,11.3c-7.6,17.7-15,35.6-21.3,53.8C763,601.1,762,619.1,764.3,637.8z M750.6,615.8
                        c0.7,0.1,1.4,0.1,2.1,0.2c1-3,2.4-5.9,2.8-8.9c3.1-24.5,10.1-48,19.4-70.8c1.1-2.8,1.4-6,2.2-9.4c-17.8,0-34.1,0-51.2,0
                        c5.1,15.2,10.5,29.4,14.6,44C744.5,585.7,747.3,600.9,750.6,615.8z"/>
                    <path d="M899.6,2644.3c10.9-11.5,22.4-22.5,32.6-34.7c8.7-10.4,15.9-22.2,23.8-33.3c0.8-1.1,1.2-2.6,2.1-3.3c2.3-1.7,5.1-4.5,7.1-4
                        c2.1,0.5,3.8,4,5.1,6.5c1,2,1.1,4.5,1.5,6.8c4.1,28,8.2,56,12.2,84c1,6.9,4.4,12.4,9,17.4c6.5,7.1,13.1,14.2,19.3,21.6
                        c14.6,17.6,31.4,32.6,50.5,45.1c16.9,11.1,31.4,24.8,43.3,41c3.9,5.3,7.2,11.2,9.6,17.4c3.3,8.3,1.5,12.5-6.3,16.4
                        c-19.8,9.8-40.4,17-62.3,20.7c-22.8,3.9-42.6-3.5-61.2-15.7c-5.8-3.8-11.1-8.5-16.6-12.9c-11.8-9.4-24.8-16.9-39.3-21.4
                        c-15.8-4.8-24.9-15.3-28.9-31.3c-7.1-28.3-10.1-57-11.3-86c-0.5-10.7,2.1-20.4,7.8-29.4c1-1.6,1.5-3.5,2.3-5.3
                        C900,2643.9,899.6,2644.3,899.6,2644.3z M964.7,2575.4c-2.2,2.5-3.7,3.7-4.5,5.2c-9.7,17.7-22.5,32.9-36.4,47.4
                        c-5.8,6-11.4,12.1-16.9,18.3c-8.3,9.4-12.4,20.3-11.3,33c0.3,3.3,0.2,6.7,0.5,10c1.8,24.2,3.9,48.4,10,72.1
                        c3.8,14.8,12.3,26,27.4,30.4c15.3,4.5,28.6,12.4,40.7,22.5c4.6,3.8,9.4,7.4,14.3,10.9c16.3,11.9,34.4,16.6,54.5,15.4
                        c21.4-1.3,40.7-9.2,59.6-18.4c10.6-5.2,11.6-8.5,5.6-18.5c-1.5-2.6-3.3-5-5.1-7.4c-12-16.2-26.2-30.1-43.1-41.2
                        c-15.9-10.5-30.6-22.4-43.4-36.6c-9.2-10.1-17.9-20.6-27.2-30.6c-6.7-7.1-9.9-15.3-11.3-24.9
                        C973.9,2634.4,969.4,2605.9,964.7,2575.4z"/>
                    <path d="M608.1,2645.9c-0.1,0.6-0.4,1.5-0.2,1.9c8.2,11.6,9.1,24.4,8,38.1c-2.2,27.2-4,54.5-11.9,80.8c-1.4,4.7-3.3,9.4-5.5,13.8
                        c-2.8,5.5-7.2,9.6-13.2,11.5c-23.2,7.5-43,20.7-62,35.6c-10,7.9-21.1,13.9-33.7,16.5c-14.6,3-29.3,3.1-43.5-1.2
                        c-15.9-4.7-31.5-10.7-47-16.7c-10.3-4-12.4-9.4-8.2-19.7c1.9-4.6,4.3-9,7.3-13c12.9-17.1,27.9-32.1,46-43.9
                        c18.6-12.1,34.9-26.6,49-43.7c6.1-7.4,12.7-14.5,19.2-21.6c5.3-5.8,8.8-12.3,9.9-20.2c3.8-27.3,7.9-54.6,12-82
                        c0.7-4.8,0.1-11.2,6-12.6c6.6-1.5,8.7,4.7,11.1,9.3c8.4,15.7,19.8,29.1,31.8,42.1c7.2,7.8,14.5,15.6,21.9,23.3
                        c0.8,0.8,2.1,1.2,3.2,1.7C608.5,2646.2,608.1,2645.9,608.1,2645.9z M541.7,2575.3c-1,3.7-1.5,5.2-1.8,6.8c-4,27.3-8.2,54.6-11.9,82
                        c-1.3,9.3-4.6,17.1-11.2,23.8c-7.4,7.6-14.3,15.8-21.2,23.9c-13.7,16.1-29.1,30.2-46.9,41.6c-17.9,11.5-32.6,26.3-45.7,42.8
                        c-2.3,2.8-4.2,6-5.8,9.3c-4.5,9.4-3.2,13.2,6.4,17c12,4.8,24.2,9.2,36.5,13.5c31,10.7,59.2,4.7,84.3-15.6
                        c16.2-13.1,33.1-24.4,53.2-30.7c10-3.1,17-10.1,19.8-20.7c1.4-5.1,3.1-10.2,4.6-15.3c7.3-24.9,6.4-50.7,8.6-76.2
                        c1-11.3-3.7-21.6-10.7-30.4c-3.5-4.4-7.5-8.5-11.5-12.5c-15.9-16.2-30.8-33.2-42-53.1C545.4,2579.8,544,2578.4,541.7,2575.3z"/>
                    <path d="M849.4,1728.3c2.3,4.5,4.2,6.8,4.6,9.3c1.5,10.2,3.1,20.5,3.6,30.7c2.5,51.4,10.5,102,23.4,151.8c5.4,21,5.9,41-0.3,61.9
                        c-6.5,21.8-8.1,44.5-8.6,67.2c-0.3,16-0.1,32-0.1,48c0,4,0,7.9,0,11.8c-6.9,3-8.2-1.6-9.6-5.6c-5.5-15.7-11.2-31.3-16.2-47.2
                        c-17.4-54.8-23.8-111.2-22.6-168.6c0.9-43.4,4.7-86.5,14.6-128.8c1.6-6.8,3.4-13.6,5.5-20.2C844.6,1735.6,846.6,1733.1,849.4,1728.3
                        z M849.7,1748.6c-0.6-0.1-1.2-0.1-1.8-0.2c-0.7,1.7-1.6,3.2-2,5c-8.5,33-13.7,66.5-15.9,100.5c-2.8,41-1,81.9,4.1,122.7
                        c4.4,35.5,12.3,70.1,26,103.3c0.9,2.1,2.2,4.1,3.4,6.2c2.2-7.7,2.6-14.9,2.8-22.2c0.9-29.7,2.4-59.3,10.4-88.1
                        c4.6-16.6,4.3-32.6,0.3-49.2c-13-52.8-22.6-106.2-24.8-160.7C851.8,1760.1,850.5,1754.3,849.7,1748.6z"/>
                    <path d="M657.1,1728.3c2.7,5.2,4.7,8.1,5.7,11.3c7.3,24.6,11.8,49.8,15.2,75.3c5.1,38.8,5.9,77.7,3.9,116.7
                        c-3.2,60-15.5,118-38.5,173.7c-0.5,1.2-0.8,2.8-1.8,3.5c-1.2,0.9-3.1,1.6-4.4,1.3c-1.3-0.3-2.8-1.8-3.1-3.1c-0.5-1.8-0.1-3.9,0-5.9
                        c1.3-39,0.8-77.9-7.9-116.3c-0.1-0.3-0.1-0.7-0.1-1c-10.9-37.3-5.6-38.3,0.7-70.5c5.6-28.7,11.8-57.4,16.1-86.3
                        c3.7-24.7,5-49.7,7.4-74.6c0.4-4.6,0.5-9.4,1.5-13.9C652.2,1735.7,654.4,1733.1,657.1,1728.3z M640.2,2090.6c1,0.1,2,0.3,3,0.4
                        c29.1-78.3,39.1-159.2,32.8-242.6c-2.8-36.9-12.7-94.2-19.6-102.6c-0.9,8.7-1.8,16.5-2.5,24.3c-2.4,23.8-3.6,47.9-7.5,71.5
                        c-4.6,28.5-11.3,56.7-17.4,85c-3.6,16.6-3.7,32.9,0.3,49.4c2.7,11.3,5.4,22.7,6.6,34.2c1.8,18.9,2.3,37.8,3.3,56.8
                        C639.7,2074.9,639.9,2082.8,640.2,2090.6z"/>
                    <path d="M930.7,2334.2c-4.8-2.1-5.8-6.6-6.8-11.2c-7.9-35-15.8-70-23.7-105c-7.3-32.1-15.3-64-22-96.2c-2.6-12.6-3.9-25.7-3.7-38.6
                        c0.5-34.3,3.2-68.5,11.9-101.9c1-3.8,2.4-7.7,4.7-10.7c3.9-5.1,8.8-4.6,12,0.9c3.2,5.4,6.1,11.1,8.1,17.1
                        c11.8,35.3,23.4,70.8,29.2,107.7c2.4,15.4,3.3,31.1,3.8,46.7c1.6,50.6-2.1,101.1-5.5,151.6c-0.6,9.6-1.1,19.3-2,28.9
                        C936.3,2327.5,935.6,2331.9,930.7,2334.2z M929.5,2319.2c0.6-0.1,1.2-0.3,1.8-0.4c0.2-1,0.5-1.9,0.6-2.9
                        c2.5-48.2,5.4-96.5,7.3-144.7c0.7-18.3,0-36.7-1.8-54.9c-4.5-46.4-19.1-90.4-34.6-134c-1.3-3.8-2.8-7.6-8.5-8.5
                        c-1.4,4.6-3,9.2-4.1,14c-6,26.1-8.6,52.6-10.1,79.2c-1.2,22.7,0.5,45.2,5.8,67.5c14.1,59.9,27.9,119.9,41.9,179.9
                        C928.2,2315.9,928.9,2317.5,929.5,2319.2z"/>
                    <path d="M1035.1,922.1c0.3,2.2,0.6,4.5,0.8,6.7c1.4,5.1,2.8,10.2,4.2,15.4l-0.2-0.4c0.5,5.2,0.6,10.6,5.2,14.3
                        c0.3,1.9,0.6,3.8,0.9,5.8c1.4,4.8,2.8,9.6,4.2,14.4c0,0-0.3-0.4-0.3-0.4c2.3,10,4.5,20,6.9,30c1.5,6.5,3.5,12.8,4.9,19.3
                        c0.7,3.2,1,7.1-2.9,8.7c-3.7,1.5-6.4-0.8-8.4-3.7c-3.2-4.6-6.4-9.3-9.4-14.2c-18.9-30.5-33.4-62.8-39.8-98.4
                        c-4.7-26.2-3.9-52.4-0.2-78.5c0.6-4,0.9-7.9,1.7-11.9c0.8-4.2,1.1-8.7,6.9-11.6c1.5,5.6,3.2,10.7,4.3,16c2.2,10.7,4.1,21.5,6.3,32.3
                        c0.5,2.4,2.1,4.6,3.2,6.9c0.2,2.6,0.4,5.3,0.6,7.9c2.9,13.2,5.8,26.5,8.8,39.6C1032.9,921.2,1034.3,921.6,1035.1,922.1z
                        M1008.9,848.2c-0.8,0.1-1.6,0.3-2.4,0.4c-5.1,30.3-3.3,60.2,4.9,89.8c9.1,32.8,29,72.1,41.7,81
                        C1038.2,961.6,1023.6,904.9,1008.9,848.2z"/>
                    <path d="M470.1,920.9c0.6-1.7,1.1-3.4,1.7-5c4.1-17.1,8.5-34.1,12.3-51.2c2.7-12,4.5-24.2,6.9-36.2c0.7-3.4,2.3-6.7,3.7-10.7
                        c5.4,2.9,5.5,7.5,6.3,11.4c4.4,22,6.1,44.1,4.3,66.5c-3.9,48.4-21.7,91.6-48.4,131.5c-1.5,2.2-2.9,4.7-4.9,6.2c-1.9,1.4-5.2,2.8-7,2
                        c-1.7-0.7-2.7-4.2-3-6.5c-0.2-2.2,1-4.5,1.6-6.7c5.8-21.5,11.6-43,17.4-64.5c0.7-2.5,0.9-5.2,1.3-7.8c0,0-0.3,0.3-0.3,0.3
                        c1.4-4.8,2.8-9.7,4.2-14.5c0,0-0.2,0.5-0.2,0.5C467.3,931.1,468.7,926,470.1,920.9z M449.5,1022.9c0.7,0.3,1.4,0.6,2.1,0.8
                        c1.3-1.4,2.8-2.8,3.9-4.4c3.2-4.6,6.7-9.2,9.5-14.1c25.2-45.6,37.5-94.4,35-146.6c-0.3-5.4-1.8-10.7-2.7-16c-0.8,0-1.7,0-2.5,0
                        C479.6,902.7,464.6,962.8,449.5,1022.9z"/>
                    <path d="M749.6,1146c0,12.3,0.1,24.6-0.1,36.9c-0.1,4.3-0.3,8.7-1.2,12.9c-2.1,9.7-6.2,12.8-16.3,12c-4.6-0.4-9.2-1.3-13.7-2.4
                        c-15.8-4-31.5-8.3-47.3-12.1c-12.5-3-21.3-10.1-27.3-21.4c-9.6-18.2-15-37.6-18-57.8c-1.4-9.5,1.8-16,9.7-21.1
                        c19.2-12.3,40-19.6,62.8-20.6c11-0.5,22-0.4,33-0.2c9.4,0.1,12.3,2.4,15,11.5c4.9,16.5,4.8,33.5,4.6,50.5c-0.1,4-0.4,8-0.6,12
                        C750,1146,749.8,1146,749.6,1146z M744,1155.9c0.2,0,0.3,0,0.5,0c0.2-4,0.1-8,0.6-12c2.2-18.4-0.2-36.5-3.1-54.6
                        c-1.6-10.2-3-11.7-13.5-11.9c-10.6-0.2-21.3,0-32,0.5c-20.5,0.9-38.9,8.1-56.3,18.5c-6.6,4-9.9,9.5-8.9,17.5
                        c2.7,21.1,8.9,40.9,19.5,59.3c3.8,6.6,9.2,11.1,16.9,13c19.7,5,39.3,10.2,58.9,15.3c1.3,0.3,2.6,0.6,3.9,0.7
                        c9,1.2,12.1-1.1,13.1-10.4c0.4-3.3,0.4-6.7,0.4-10C744,1173.2,744,1164.5,744,1155.9z"/>
                    <path d="M754.2,1022.4c0-11-0.6-22,0.2-32.9c0.9-11.9,6-16.8,17.8-17.4c33.8-1.9,63.6,8.2,88.9,31c7.3,6.5,11.1,14.9,12.5,24.4
                        c2.2,15.1,4.2,30.3,6.2,45.5c0.3,2.3,0.5,4.7,0.1,7c-0.8,4.9-4.5,7.6-9.3,6.5c-2.8-0.7-5.5-2.3-8-3.9c-17.8-10.8-37.1-16-57.8-16.5
                        c-12-0.3-24,0.1-35.9-0.4c-11.9-0.5-14.5-3.2-14.9-15.1c-0.3-9.3-0.1-18.6-0.1-27.9C753.9,1022.4,754,1022.4,754.2,1022.4z
                        M872.9,1080.8c0-6.3,0.4-10.9-0.1-15.5c-1.3-11.9-3.4-23.7-4.6-35.6c-0.9-9.5-4.7-17.3-11.7-23.4c-23.6-20.7-51.1-30.6-82.6-28.7
                        c-11.5,0.7-14.2,3.1-14.4,14.5c-0.4,19.3-0.3,38.6-0.1,57.9c0.1,8.6,1.4,9.7,10.1,10c9.3,0.3,18.6,0.3,28,0.4
                        c23.3,0.1,45.6,4.4,66.1,16.3C865.9,1078.1,868.7,1079,872.9,1080.8z"/>
                    <path d="M871.3,996.3c-5.4-2.4-8.6-3.5-11.4-5.1c-27.4-15.6-57.1-23.4-88.2-27.1c-3-0.3-5.9-0.9-8.8-1.7c-5.4-1.5-8.6-5.2-8.9-10.8
                        c-0.7-11.9-2.3-24-1.2-35.8c0.8-8.7,4.4-17.4,8.4-25.3c4.1-8.3,10-15.7,15.4-23.3c4.2-5.8,8.3-7.3,14.7-4.6
                        c15.7,6.4,31.3,13.2,46.7,20.2c7.3,3.4,12.1,9.7,14,17.4c6.7,27.1,13.1,54.3,19.3,81.6C872.1,986,871.3,990.4,871.3,996.3z
                        M865.4,987.4c-0.4-5.1-0.3-8.3-1-11.3c-5.5-23.9-11.1-47.9-16.9-71.8c-2.1-8.5-7.2-15-15.3-18.6c-13.1-5.7-26.2-11.4-39.4-16.8
                        c-7.5-3-9.3-2.1-14.1,4.6c-19.3,27.1-19.3,27.1-19.3,60.5c0,1,0,2,0,3c0,20.5,0,20.3,20.8,22.9c26.3,3.3,51.5,10.1,74.9,22.7
                        C857.8,984.2,860.7,985.3,865.4,987.4z"/>
                    <path d="M717,1064.9c-13.2,1.2-26.5,2.1-39.7,3.6c-13.1,1.5-24.7,7.3-36,13.8c-13.2,7.6-16.3,6.1-16.7-8.8
                        c-0.6-19.4,1.7-38.6,9.4-56.6c2.1-5.1,5.2-10.4,9.2-13.9c23.4-20.4,50.3-32,81.9-30.9c1.3,0,2.7,0,4,0.1
                        c15.9,1.2,18.6,4.7,21.2,20.6c3.2,20,2.2,39.7-0.3,59.6c-1.3,10.1-3.7,12.6-13.9,13.1c-6.3,0.3-12.6,0.1-19,0.1
                        C717,1065.4,717,1065.1,717,1064.9z M630.1,1081.1c5.1-2.3,8.7-3.6,12-5.5c12.9-7.4,26.6-12.3,41.4-13.4c15.6-1.1,31.2-1.5,46.9-2.2
                        c3.9-0.2,7.8-0.5,11.9-0.7c0.8-2.6,1.5-4.4,1.8-6.3c3.9-21.9,1.8-43.8-0.7-65.6c-0.8-6.8-3.9-8.9-11.9-9.5
                        c-29.2-2.3-55.2,6.1-78.5,23.4c-10.3,7.7-16.7,18-18.8,30.7c-1.6,9.8-3.1,19.7-4,29.7C629.7,1067.5,630.1,1073.4,630.1,1081.1z"/>
                    <path d="M749.9,926.7c0,9.9,0.3,16.5-0.1,23.2c-0.4,7.9-2.5,10.8-10.3,12.4c-13,2.8-26.1,4.9-39.2,7.3
                        c-18.2,3.4-35.1,10.1-51.2,18.9c-0.9,0.5-1.7,1.2-2.6,1.5c-3.7,1-8,3.5-10.9,2.4c-4.7-1.8-3.7-7.3-2.8-11.5c5.2-24,10-48.2,16.1-72
                        c3.9-15.3,13.8-26.5,28.4-32.8c11.3-4.8,22.8-9.1,34.4-13.1c8.4-2.9,11.4-1.6,16.8,6c1.7,2.4,3.2,5.1,5.1,7.4
                        C745.6,892.2,754.1,909.1,749.9,926.7z M637.6,988.1c5.2-2.6,8.5-4.2,11.7-5.8c18.4-9.7,37.8-16.3,58.3-19.7
                        c8.9-1.4,17.7-2.7,26.6-4.4c8.8-1.7,10.6-3.7,11.1-12.5c0.4-6.3,0-12.6,0.5-18.9c1-15.3-3-28.9-12.4-40.9c-3.1-3.9-5.5-8.3-8.3-12.4
                        c-4.5-6.4-6.8-7.3-14.3-4.5c-9,3.3-17.9,7.2-26.9,10.4c-17.6,6.3-27.5,18.8-31.4,36.9c-4.2,19.8-8.9,39.5-13.3,59.3
                        C638.3,978.9,638.2,982.5,637.6,988.1z"/>
                    <path d="M772.1,832.2c1.6,3.2,2.9,6.6,4.9,9.6c2.7,4.1,3.1,8.2,0.4,12.1c-5.9,8.9-11.7,17.8-18.1,26.2c-4.8,6.2-10.1,6.3-14.8,0
                        c-6.4-8.5-12.1-17.5-17.9-26.4c-2.8-4.3-2.5-9,0.3-13.2c5.6-8.6,11.1-17.4,17.2-25.6c5.3-7.1,9.4-6.8,15.1,0.3
                        c4.6,5.6,8.8,11.5,13.2,17.3C772.5,832.6,772.1,832.2,772.1,832.2z M750.2,814.9c-1.3,1.9-2.3,3.3-3.2,4.6c-3.1,4.3-6.3,8.5-9.2,13
                        c-9.6,14.9-9.7,15,0.5,29.9c4.2,6.1,7.2,13.5,15.3,17.5c5.6-8.4,11.1-16.6,16.8-24.8c3.5-5,3.5-9.7,0-14.7
                        c-4.5-6.5-8.8-13.2-13.5-19.7C755.5,818.8,753.1,817.4,750.2,814.9z"/>
                    <path d="M752.3,709.7c-2.1-2.3-3.2-3.2-3.7-4.3c-7.2-14.5-14.3-29.1-21.4-43.7c-0.7-1.4-0.7-3.1-1.2-5.6c5.2,0,9.7,0.1,14.3,0
                        c8.6-0.1,17.2-0.3,25.7-0.5c2,0,4-0.7,5.9-0.4c4.6,0.5,6.4,3.4,5,7.7c-0.6,1.9-1.5,3.7-2.8,5.3c-7.6,9.5-12.7,20.2-16.7,31.5
                        C756.3,702.6,754.5,705.4,752.3,709.7z M752.2,695.7c6.2-12,11.4-22.1,17.2-33.2c-12.7,0-23.7,0-35.7,0
                        C739.8,673.6,745.5,683.8,752.2,695.7z"/>
                    <!-- BACK -->
                    <path id="front_B_20" class="st0" d="M2448.6,2060.5c-1.6-3.2-3.4-6.4-4.9-9.6c-10.9-23.9-21.5-48-32.8-71.7c-19.7-41.4-27.1-85.2-29.2-130.6
                        c-2.4-52.9-6.3-105.7-9.2-158.6c-1.8-31.9-3-63.9-4.2-95.8c-0.3-7.3,0.8-14.6,1.6-21.9c0.9-8.4,3.5-10.3,12-8.4
                        c6.1,1.4,12,4,18.2,5.3c7.8,1.6,15.7,3.3,23.6,3.4c9.5,0,13.3-4.8,12.1-14.2c-1.1-8.2-3.2-16.3-4.9-24.5
                        c-6.5-30.2-8.8-60.6-3.1-91.3c2.6-13.7,8-26.1,16.7-37c6.2-7.8,13.9-13.4,23.7-15.7c11.9-2.8,17.1,0.2,19,12.3
                        c2.7,18.1,5.1,36.3,6.8,54.5c5.7,61.2,3.8,122.4,0.5,183.7c-2.9,54-10.7,107.3-19.5,160.6c-6.7,40.4-14,80.7-20.4,121.1
                        c-1.4,8.8-0.6,18-0.3,26.9c0.6,14.6,2.3,29.3,2.3,43.9c0,19-1,37.9-2,56.9c-0.2,3.6-2,7.2-3.1,10.7
                        C2450.6,2060.6,2449.6,2060.6,2448.6,2060.5z"/>
                    <path id="front_B_19" class="st0" d="M2256.1,1503.1c3.8,1.9,6.6,2.9,8.8,4.6c11.2,8.7,23.6,15,36.8,19.8c26.4,9.6,39.7,28.5,42.4,56.2
                        c3.4,34.8,8,69.5,11,104.3c3,35.5,4.5,71.2,7,106.7c2.2,31.2,4.5,62.5,7.3,93.6c1.7,18.9,4.3,37.7,6.5,56.6
                        c1.4,12.5,0.1,24.5-4.3,36.3c-6.3,16.7-14.6,32.1-26.9,45.2c-11.1,11.8-16.1,25.8-16.2,41.8c-0.1,16-0.4,32-0.7,48
                        c-0.1,3.6-0.6,7.1-0.9,10.7c-0.7,0.2-1.5,0.4-2.2,0.6c-2.8-5.4-5.8-10.7-8.2-16.2c-11.9-27.9-20.9-56.9-27.9-86.3
                        c-9-37.5-17.5-75.2-25.9-112.9c-7.8-35-8.8-70.6-9.9-106.3c-1.2-39-3.5-77.9-4-116.9c-0.4-32.3,0.9-64.6,2-96.9
                        c0.8-22.6,2.4-45.2,3.7-67.8C2254.7,1517.6,2255.4,1511,2256.1,1503.1z"/>
                    <path id="front_B_16" class="st0" d="M2335.5,1245.4c16.4,1.4,32.7,6.7,48.1,15.2c24.1,13.3,43.5,32,57.9,55.3c8.7,14,15.7,29.3,22.7,44.3
                        c3.5,7.4,2.2,9.7-4.3,14.2c-2.7,1.9-5.6,3.6-8.5,5.2c-14.4,7.9-23.8,19.9-29.2,35.3c-8.7,24.5-12.2,49.7-10.7,75.6
                        c0.8,13.3,2.5,26.5,3.7,39.7c0.4,4.6,1,9.3,0.9,13.9c-0.1,7.6-3.2,9.9-10.8,8.8c-1-0.1-2-0.4-2.9-0.7
                        c-47.1-12.3-91.5-30.5-130.4-60.4c-14.1-10.9-26-23.8-35.3-39.1c-10.5-17.5-10.1-28.8,2.2-45c17.9-23.7,34.4-48.2,48.1-74.6
                        c11.3-21.6,20.5-44,27.6-67.3c1-3.2,2.2-6.3,3.3-9.4C2321.5,1247.2,2324.7,1245.1,2335.5,1245.4z"/>
                    <path class="st0" d="M2351.9,2768c0.6-8.6,2.2-17.3,1.7-25.9c-1-20.3-3.1-40.5-4.4-60.7c-0.9-13.9-2.1-28-1.1-41.9
                        c0.6-9,4.1-18.1,7.7-26.6c3.7-8.7,11.5-12.5,20.9-13c19.4-1,33.7,8.8,41.2,26.8c5.8,13.7,13,26.9,19.5,40.3
                        c4.7,9.7,12.3,12.4,23,11.7c16.6-1.1,33.3-1.8,49.8-0.6c21.4,1.5,36.4,13.2,44.2,33.7c4.3,11.2,3.1,21.5-3.3,31.4
                        c-6.6,10.2-16,16.9-27.3,20.8c-9.1,3.1-18.4,5.9-27.6,8.6c-16.6,4.8-30.2,13.6-39.5,28.6c-6.3,10.1-15.6,16.4-27.1,19
                        c-16.9,3.8-33.8,7-51.3,4c-9.5-1.6-15.9-5.4-18.9-15C2355.3,2795.7,2351.8,2782.2,2351.9,2768z"/>
                    <path id="front_B_23" class="st0" d="M2344.1,2053.4c6.7,2.5,11.9,3.8,16.3,6.3c14.6,8.5,23.1,21.6,27.3,37.8c15.8,60.3,22.4,121.6,21.6,183.8
                        c-0.3,21.8-3.5,43.1-11.4,63.5c-5.9,15.3-14.9,28.2-29.1,37c-11.2,6.9-17.5,6.2-26.4-3.5c-18.5-20.3-26.6-44.9-28.6-71.8
                        c-2.6-35.9,5.3-70.1,17.1-103.6c8.7-24.8,13.4-50,13-76.3c-0.3-19-0.6-37.9-0.7-56.9C2343.3,2065.1,2343.7,2060.5,2344.1,2053.4z"/>
                    <path id="front_B_26" class="st0" d="M2342.7,2392.5c4.5,1.4,7.6,2.3,10.7,3.4c8.8,3.1,17.1,1.7,24.9-3c13.6-8.2,23.2-19.7,29.4-34.3
                        c4.5-10.8,4.6-10.4,15.9-10.8c7.6-0.3,15.2-1.8,22.7-2.8c2.9-0.4,5.8-0.9,9.9-1.5c-0.5,3.6-0.7,6.4-1.3,9.2
                        c-8.6,39.3-17.3,78.6-25.7,118c-2.9,13.9-4.8,28.1-7.3,42.9c6.5-1.1,7.3-5.2,8.3-9.4c4.5-19.8,8.6-39.7,13.7-59.4
                        c8.2-32.2,17-64.3,25.7-96.5c3.2-11.8,9.3-21.8,19.9-29.5c3.6,6.2,1.3,11.6-0.3,16.8c-4.5,15-9.9,29.7-13.9,44.8
                        c-14.4,55-28.6,110.1-42.2,165.4c-5,20.3-3.3,41.1-0.2,61.6c0.4,2.6,0.8,5.3,1.1,7.9c0.1,1-0.2,2-0.4,4c-4.6-3.3-6.7-7.8-9.4-11.7
                        c-8.8-12.1-19.9-21-35-22.6c-7.1-0.7-14.5,0.7-21.7,1.5c-2.9,0.3-5.8,1.4-10.4,2.5c0.8-4.2,0.9-7.4,2-10.2
                        c10.1-27.5,9.8-55.4,3.9-83.7c-6.3-30.3-12.4-60.6-18.5-91C2343.7,2401,2343.5,2397.7,2342.7,2392.5z"/>
                    <path id="front_B_24" class="st0" d="M2415.4,2030.6c3.9,5.4,7.8,10.4,11.1,15.6c31.7,49.6,52.6,103.7,66.2,160.8c5.4,22.8,5.9,46.2,4.3,69.6
                        c-1.8,27.8-28.1,45.9-50.9,45.3c-11.5-0.3-16.4-4.1-18.7-15.3c-1-4.8-1.5-9.9-1.7-14.8c-1.6-47.7-6.6-95-13.1-142.2
                        c-3.5-25.1-6.4-50.2-9-75.3c-0.8-7.6-0.3-15.3,0.3-22.9C2404.6,2043.2,2407.8,2036.1,2415.4,2030.6z"/>
                    <path id="front_B_14" class="st0" d="M2468,1322.3c-2.1-2.3-4.6-4.3-6.4-6.8c-3.8-5.4-7.4-11-11-16.6c-16.2-25.9-38.9-43.3-68.6-51.2
                        c-12.8-3.4-25.7-6.6-38.6-9.9c-11.4-3-12.3-4.6-8.7-16.1c2.2-7,4.5-13.9,6.9-20.8c4.1-12.2,12.1-21.6,23-27.8
                        c4.6-2.6,10.8-3.6,16.1-3.1c18.7,1.6,34,10.5,46.9,23.8c14.3,14.7,23,32.6,27.7,52.2c5.3,21.6,9.7,43.4,14.3,65.2
                        c0.7,3.3,0.1,6.9,0.1,10.3C2469.2,1321.7,2468.6,1322,2468,1322.3z"/>
                    <path class="st0" d="M2409,2181.1c1,1.9,2.6,3.7,2.9,5.6c4.8,33.6,7.2,67.5,8.2,101.5c0.2,6.6,0.8,13.3,2,19.9
                        c2.3,13.2,12.8,20.8,26.1,18.7c6.5-1,12.8-3.4,19.1-5.4c4-1.3,8-3,13.9-5.3c-4.6,7.3-8.6,12.9-11.7,18.8c-3.2,6-5.5,12.5-9.1,20.9
                        c0.9-7.8,1.6-13.3,2.4-19.8c-6.1,1-11.2,2-16.3,2.7c-9.2,1.3-18.4,3-27.7,3.6c-7.6,0.5-12.1,3.7-14.8,10.9
                        c-5.1,13.7-13.3,25.3-25.8,33.4c-3.2,2.1-6.8,3.5-11.3,3.7c1.2-1.2,2.3-2.5,3.7-3.4c15-9.4,24.9-22.8,31.4-39
                        c6.9-17.1,10.8-35,11.5-53.4c1.4-38-0.5-75.9-4.6-113.7L2409,2181.1z"/>
                    <path class="st0" d="M2383.6,2058c-7.7-6.2-14.5-11.5-21-17.1c-5.6-4.8-5.8-5.8-2.3-12.7c6-11.8,12.2-23.5,18.1-35.4
                        c2.4-4.7,4.2-9.7,6.7-15.4c1.8,1.9,3.7,3.2,4.7,5c3.9,6.9,7.5,14,11.2,21.1c2.3,4.3,3,8.8,1,13.4
                        C2396.1,2030.2,2390.2,2043.4,2383.6,2058z"/>
                    <path class="st0" d="M2432.5,1123.7c6.4,23.4,12,44.5,14.8,67.7c-10.9-3.3-15.2-12.1-22.4-17.1c-7-4.9-13.8-10.2-21.6-16
                        C2412.9,1147,2421.9,1136.3,2432.5,1123.7z"/>
                    <path class="st0" d="M2409.1,2180.9c-0.4-3.8-0.8-7.7-1.2-11.5c0.7,0,1.3,0,2,0c-0.3,3.9-0.6,7.8-0.9,11.7
                        C2409,2181.1,2409.1,2180.9,2409.1,2180.9z"/>
                    <path class="st0" d="M2407,2148.4c0.3,3.5,0.6,7,0.8,10.6c0,0.5-0.6,1-1.6,2.9C2406.4,2156.4,2406.7,2152.4,2407,2148.4
                        C2407,2148.3,2407,2148.4,2407,2148.4z"/>
                    <path class="st0" d="M2307.5,468.3c-3.9-17.6-7.8-34.4-11.3-51.3c-6.1-29.8-26.3-45.2-54-52.4c-24.5-6.4-46.8-0.7-66.9,14.3
                        c-10.8,8-18.5,18.3-21.6,31.4c-4.2,17.5-7.8,35.1-11.7,52.6c-0.3,1.6-0.8,3.1-1.2,4.6c-7.7-1.4-9.6-3-10.8-10.6
                        c-1.6-9.9-2.7-19.8-3.9-29.7c-0.9-6.9-1.4-13.9-2.4-20.8c-1-7.5-1.4-8.3-9.1-9.2c-7.9-0.9-12.6-5-15.7-12
                        c-4.6-10.7-10-21.1-14.5-31.8c-5.4-13-0.6-21.9,13.1-25.5c12.3-3.2,12.8-3.6,12.3-16.2c-0.9-22.8,0.4-45.4,8.4-66.9
                        c15-40.5,42.8-67,85.9-75c57.1-10.6,111.9,24.3,127.8,80.4c5.7,20.1,6.4,40.7,6,61.4c-0.1,5.3,0,10.6,0,14.9
                        c5.5,0.7,10.7,0.3,15,2.1c4.4,1.9,8.2,5.5,11.4,9.2c3.4,3.8,2.9,8.7,0.8,13.2c-5.7,12.4-11.5,24.7-17.4,37c-2.5,5.2-6.7,7.9-12.4,9
                        c-10.3,1.8-10.1,2-11.3,12.5c-1.8,15.5-3.7,31.1-5.8,46.6C2317.1,463.9,2315.9,464.9,2307.5,468.3z"/>
                    <path id="front_B_4" class="st0" d="M2212.9,945.6c-0.7-1.4-1.6-2.7-2.2-4.2c-8.1-21-19.7-40-32.5-58.3c-21.2-30.3-41.7-61.1-63.8-90.7
                        c-22.4-30.1-36-63.4-42.6-100c-2.1-11.4-5-22.7-8.2-33.9c-6-20.9-17-38.2-36.8-48.9c-1.6-0.9-2.9-2.4-5.3-4.4c2.4-1,3.7-1.8,5.2-2.1
                        c22.8-4.8,45.4-10.5,68.3-14.1c22.9-3.6,45.9,0.2,68.3,5.4c11.7,2.7,19.7,10.8,25.2,21.3c13.2,25.4,22,52.2,23.4,80.9
                        c1.6,34.6,2.2,69.2,2.8,103.8c0.7,44.3,0.9,88.6,1.2,132.9c0,4.1-0.8,8.1-1.2,12.2C2214.1,945.5,2213.5,945.5,2212.9,945.6z"/>
                    <path id="front_B_5" class="st0" d="M2230,944.1c-0.2-2.6-0.6-5.2-0.6-7.7c0.4-56.3,0.7-112.6,1.5-169c0.4-25.6,1.6-51.3,3.1-76.9
                        c0.5-9.2,1.7-18.9,4.9-27.4c6.6-17.2,14.3-34.1,22.6-50.6c5.3-10.5,14.9-16.4,26.8-19c36.2-7.8,72.1-7.2,107.7,3.3
                        c8.8,2.6,17.6,5.1,28.4,8.2c-4,3-6.1,4.9-8.5,6.3c-15.9,9.5-26.5,23.3-31.5,41c-4.9,17.3-9.6,34.6-13.4,52.2
                        c-7.7,35.4-22.8,67.3-44.1,96.4c-18.5,25.3-36.9,50.6-55.4,75.9c-14.8,20.2-28.3,41.2-37.7,64.6c-0.5,1.1-1.3,2.1-2,3.2
                        C2231.2,944.4,2230.6,944.2,2230,944.1z"/>
                    <path id="front_B_10" class="st0" d="M2212.9,1378.9c-9.7-10.1-17.4-21.8-22.9-34.6c-7.8-18-14.7-36.4-21.5-54.7c-6.7-18.1-13-36.3-19.1-54.6
                        c-7.4-21.9-18.4-42-30.9-61.3c-5.6-8.7-13.5-14.5-23.1-18.2c-9.3-3.6-10.2-5-9.2-14.9c4.5-46.5,19.1-89,50.3-124.7
                        c15.5-17.8,31.6-35.2,47.4-52.8c1.1-1.2,2.3-2.4,3.5-3.5c6.6-5.6,9.9-5,13.5,2.9c8.1,17.4,13.2,35.5,13.3,54.9
                        c0.5,96.2,1.1,192.5,1.6,288.7c0.1,21,0,42,0,62.9c0,3.2-0.3,6.4-0.5,9.5C2214.4,1378.6,2213.7,1378.7,2212.9,1378.9z"/>
                    <path id="front_B_1" class="st0" d="M2162.4,512.4c1.9-4.4,4.2-8.7,5.5-13.3c7.3-26,12.5-52.4,14.3-79.5c0.3-5,0.7-10,1.1-14.9
                        c1.5-18.6,9.7-26.1,29.9-26.5c0.7,9.1,1.8,18.2,2.2,27.4c1.1,32.6,2.4,65.3-1.4,97.8c-1.7,14.6-4.3,28.9-10.5,42.5
                        c-3.2,7-7.8,10.2-16.1,10.5c-24.7,0.9-49,5.2-73,10.6c-1.6,0.4-3.2,0.8-4.9,1c-0.5,0.1-1.1-0.4-2.7-1.1c4.4-2.7,8.2-5.3,12.2-7.4
                        c16.4-8.9,25.9-22.6,27.8-41.2c1.9-18.6,3.5-37.1,5-55.7c1.3-16,2.5-31.9,9.9-46.6c1.7-3.3,3.9-6.4,7.1-9.2
                        c2.5,35.6-4.3,70.1-8.9,104.8C2160.8,511.8,2161.6,512.1,2162.4,512.4z"/>
                    <path id="front_B_2" class="st0" d="M2339.8,567.9c-11.7-2.2-23.4-4.9-35.2-6.6c-15.1-2.2-30.4-4.2-45.6-5.1c-7.1-0.5-10.9-3.3-13.8-9.2
                        c-6.5-13-9.3-27.1-10.9-41.3c-4.2-36.2-3.3-72.5-1-108.8c0.4-6.2,0.9-12.5,1.3-18.6c19-0.5,28.8,7.6,29.8,25
                        c1.8,30.4,6.1,60.3,13.9,89.7c1.8,6.9,3,14.1,9.9,21.2c-4.7-36.9-11.6-71.5-10.4-109.2c3,3.1,4.9,4.3,5.9,6.1
                        c5.5,10,9.3,20.7,10.4,32.1c2.1,21.5,3.9,43.1,5.9,64.7c0.4,4.6,1,9.3,1.6,13.9c2.4,15.9,10.7,27.7,24.3,36
                        c4.8,2.9,9.6,5.8,14.4,8.7C2340.2,566.8,2340,567.4,2339.8,567.9z"/>
                    <path class="st0" d="M2224.8,481.1c1.1,11.5,1.6,23.1,3.3,34.6c1.6,10.8,4.1,21.5,6.8,32.2c2.1,8.4,7.7,12.1,16.9,12.4
                        c33.3,0.9,69.6,6.9,95.3,15.8c-2.2-0.1-4.5,0.2-6.5-0.4c-29-8.5-59-9.7-88.8-13.1c-7.5-0.9-12.7,2-16.2,8.9
                        c-7.3,14.3-9.9,29.5-10,45.4c0,6.8-0.3,13.5-0.5,20.3c-0.9,0-1.9,0-2.8,0c0-6.4-0.1-12.9,0-19.3c0.3-15.2-2.1-29.9-8.6-43.7
                        c-5.1-10.9-8.5-12.7-20.5-11.2c-23.8,2.9-47.5,6-71.3,9.3c-7.1,1-14,3-21.2,3.6c18.1-6,36.6-10.3,55.7-11.8
                        c14.9-1.1,29.8-2.7,44.7-4.2c5.1-0.5,8.8-3.5,10.3-8.3c2.4-7.3,5-14.5,6.2-22c2.1-12.8,3.1-25.8,4.5-38.7c0.4-3.2,0.5-6.5,0.8-9.7
                        C2223.6,481.1,2224.2,481.1,2224.8,481.1z"/>
                    <path id="front_B_2_1" class="st0" d="M2323.1,576c-19.9,0.8-40.1-0.1-57.8,11.6c-17.1,11.3-25.5,29-35.3,46.2c0-4.8-0.3-9.5,0.1-14.3
                        c1-13.3,2.6-26.5,7.7-39c4.6-11.3,7.4-13.4,19.5-12.5c21.8,1.6,43.7,3.7,65.5,5.5C2322.8,574.5,2322.9,575.3,2323.1,576z"/>
                    <path id="front_B_1_1" class="st0" d="M2217.6,634c-9.4-17.8-18.2-35.7-35.9-46.9c-17.5-11.1-37.3-10.2-56.9-12.6c9.8-1.2,19.6-2.6,29.4-3.5
                        c13.6-1.2,27.2-1.9,40.8-3c5.8-0.5,9.6,2.1,12.3,6.7c1.7,2.9,3.2,5.9,4.2,9C2216.7,599.9,2218.5,616.7,2217.6,634z"/>
                    <path class="st0" d="M2270.5,413.4c-0.7-5.8-1.4-11.6-2-17.4c-0.8-8.4-5.2-14.2-12.7-17.7c-21.2-10-42.4-10-63.6,0
                        c-7.5,3.5-11.9,9.3-12.7,17.8c-0.5,5.6-1.2,11.2-2.5,16.9c-2.7-7.8-3.8-16-2.8-24.1c0.3-2.5,3-5.1,5.2-6.9
                        c23.3-19.4,66.1-19.3,89.5-0.1c3.9,3.2,6.2,6.8,5.4,12c-0.6,3.9-0.6,8-1.1,11.9c-0.3,2.6-0.9,5.1-1.4,7.7
                        C2271.3,413.4,2270.9,413.4,2270.5,413.4z"/>
                    <path class="st0" d="M2223.9,415c-3.3-19.7-2.9-35,0.7-38.6C2226.9,382,2226.8,401.2,2223.9,415z"/>
                    <path id="front_B_17" class="st0" d="M2072.7,1561.8c1.2,7.7,3,14.5,3.1,21.3c0.3,21.3,0.9,42.7-0.3,63.9c-3.2,55.2-7.4,110.3-11,165.5
                        c-1.6,23.9-2.3,47.9-4.2,71.8c-2.3,29.8-10.2,58.3-22.3,85.6c-12.4,28-25.2,55.8-37.9,83.6c-1.2,2.7-2.8,5.1-4.5,8
                        c-4.6-3.3-4.9-7.6-5.6-11.7c-3.7-24.9-2.9-49.8-1-74.8c1.5-19.5,4.4-39.1,0.8-58.8c-3.5-18.7-7.3-37.2-10.2-56
                        c-7.5-48-15-96-21.7-144.2c-8.1-58.5-10.9-117.4-10.5-176.4c0.2-43,2.2-85.9,8.8-128.5c0.7-4.3,1.3-8.6,2.4-12.7
                        c2.1-7.6,6.4-10.2,14.2-9.3c10.4,1.2,18.9,6.2,25.7,14c11,12.7,17.2,27.6,19.6,44.1c4.7,31.1,1.6,61.8-5.1,92.2
                        c-1.2,5.5-2.7,11-3.5,16.6c-1.7,11.9,3.8,17.9,15.8,16.7c6.6-0.7,13-2.5,19.5-4.1c4.8-1.2,9.6-2.8,14.4-4
                        C2063,1563.8,2066.9,1563.1,2072.7,1561.8z"/>
                    <path class="st0" d="M2098.5,2650.7c-1.6,15.9-3.7,31.7-4.6,47.7c-1.1,19.9-1.6,39.9-1.5,59.9c0.1,17.1-1.4,33.8-6.7,50.2
                        c-3.2,9.7-9.4,14.5-19.5,15.8c-19.2,2.4-37.8,0.6-56.1-5.8c-7.8-2.7-14.1-7-18.8-14c-12-18-29.3-28.5-49.8-34.4
                        c-8.6-2.4-17.6-4.5-25.4-8.6c-7.5-3.9-14.6-9.4-20.3-15.7c-9.3-10.3-10.7-22.9-4.9-35.4c8.7-18.8,23-31.4,44.3-32.5
                        c16.9-0.9,33.9-0.1,50.8,1c9.1,0.6,15.3-2,20.1-9.6c8.6-13.5,15.9-27.6,21.1-42.8c5.9-17.2,18.8-24.9,36.3-26.5
                        c12.5-1.1,21.1,4.5,26.9,15.1C2096.7,2626.1,2098.6,2638.1,2098.5,2650.7z"/>
                    <path id="front_B_22" class="st0" d="M2101.3,2055c0,14.6,0.1,28.2,0,41.8c-0.2,22.3-2,44.7,2.7,66.7c3.5,16.5,7.8,33,13.1,49
                        c9.6,29,15.5,58.5,14.2,89.2c-1.2,28.1-9.3,53.6-27.8,75.3c-9.5,11.1-16.1,12.1-28.5,4.1c-12.5-8-21.1-19.3-26.6-32.8
                        c-7.9-19.6-12.4-39.9-12.6-61.2c-0.6-57.1,3.7-113.8,16.3-169.6c2-9.1,4.6-18,7.6-26.8c3.4-9.9,9.2-18.4,17.2-25.4
                        C2083.4,2059.5,2090.8,2055.2,2101.3,2055z"/>
                    <path id="front_B_25" class="st0" d="M2086.9,2589.3c-33.7-13.3-57.6,0.3-76.6,28.6c0.6-4,1.1-8,1.8-12c5.1-31.6,0.3-62.4-7.1-93
                        c-14.2-58.5-29.2-116.8-48-174c-2.1-6.5-3-13.4-5-22.5c3.9,2.7,5.9,3.6,7.4,5.1c8.4,8.4,14.2,18.4,17.1,29.9
                        c12.6,50,25.1,100,37.7,150c1.1,4.3,0.8,9.5,7.3,11.4c1.5-4.9,0.3-9.3-0.6-13.7c-9.9-47.2-19.8-94.4-29.7-141.6
                        c-0.9-4.2-1.7-8.4-2.8-14c7.3,0.9,13.8,1.8,20.2,2.6c5.3,0.7,10.6,1.7,15.9,1.7c6.2,0,9.9,2.4,12,8.3c3.8,10.9,10.5,19.9,18.7,28
                        c12.3,12,26.1,17,42.7,9.6c0.9-0.4,1.9-0.5,4.3-1c-1,6.3-1.7,12-2.8,17.7c-6.4,32.6-13.2,65.2-19.4,97.9c-4.4,23-2.4,45.7,5.2,67.9
                        c1.2,3.4,2,7,2.9,10.5C2088,2587.2,2087.5,2587.8,2086.9,2589.3z"/>
                    <path id="front_B_21" class="st0" d="M2029.4,2030.2c8.7,6.9,12.5,15.5,11.9,25.7c-1.1,18.9-1.8,37.9-4.2,56.7c-7.4,57.5-15.6,114.8-17.5,172.8
                        c-0.2,6.6-0.7,13.3-1.7,19.9c-1.6,11.4-7.4,16.2-18.8,16.6c-26.8,0.8-49.5-20.1-51.2-47.1c-2-30.5,1.8-60.2,9.8-89.8
                        c14.9-54.9,39.6-105.1,69.9-152.8C2027.8,2031.7,2028.4,2031.3,2029.4,2030.2z"/>
                    <path id="front_B_7" class="st0" d="M1978.4,864c2-23.9,2-45.8,5.9-66.9c7.3-39.1,29.3-69.4,61.4-92.6c0.5-0.4,1.1-0.8,1.7-1.1
                        c8.9-5.6,12.2-4.4,14.8,5.7c1.7,6.4,2.9,13,4.7,19.4c6.5,23.6,17.3,45.3,30.8,65.7c25.8,38.8,51.5,77.8,77.1,116.7
                        c12.8,19.4,12.5,22.5-1.7,40.5c-15.4,19.5-32.6,37-53.1,51.1c-34,23.4-70.6,19-98.2-12c-20.3-22.8-32.5-49.4-37.7-79.3
                        C1981.4,894.9,1980.2,878.3,1978.4,864z"/>
                    <path id="front_B_3" class="st0" d="M1853.5,775.9c3.1-39.4,11.9-74.7,36.8-104.4c11.5-13.7,25.2-24.9,41-33.1c15.6-8.1,31.8-15.4,47.8-22.7
                        c8.7-3.9,17.8-3.5,26.1,0.7c22.8,11.3,37.4,29.4,43.2,54.4c1.7,7.2-0.3,12.2-6.5,16.1c-19.3,12.2-35.4,28.1-51.4,44.3
                        c-10.3,10.5-20.6,20.9-31.4,30.8c-21,19.3-46.5,28.2-74.6,29.8c-8.2,0.5-16.6-1.1-24.8-2c-4.3-0.5-6.3-3.3-6.2-7.6
                        C1853.6,779.4,1853.5,776.8,1853.5,775.9z"/>
                    <path class="st0" d="M1878.2,906.1c0,12-0.2,22,0,31.9c0.4,15.2,6,27.8,18.6,36.9c9.6,7,17.7,7.4,27.7,0.9
                        c4.9-3.1,9.6-6.5,16.4-11.2c-1.5,4.1-1.9,6-2.8,7.6c-10.4,18.6-20.6,37.3-31.6,55.5c-8.7,14.5-21.1,24.8-38.6,27.3
                        c-4.6,0.6-9.4,0.9-13.9,0c-13.7-2.9-27.2-6.4-40.7-9.6c-4.8-1.1-7.3-4.2-8.1-9c-5.9-33.4-4.9-66.3,7.5-98.2
                        c2.1-5.5,5.8-10.1,12.1-10.8c15.7-1.8,29-9.2,42.5-16.5C1870,909.4,1873.1,908.3,1878.2,906.1z"/>
                    <path class="st0" d="M1968.2,832.2c0.2,20.6,0.2,41.2-4,61.6c-5.9,28.7-22.6,50.7-44.1,69.4c-8.4,7.4-16.2,5.1-20.3-5.5
                        c-5.5-14.3-5.3-29.3-4.7-44.3c1.3-31,11-59.3,28.3-84.9c8.8-12.9,18.1-25.5,27.2-38.2c0.8-1.1,1.5-2.4,2.6-3
                        c2.8-1.4,5.9-3.6,8.8-3.3c4.2,0.3,5.3,4.6,5.8,8.3c0.4,2.9,0.3,6,0.3,9C1968.2,811.6,1968.2,821.9,1968.2,832.2z"/>
                    <path id="front_B_9" class="st0" d="M1997.8,978.1c1.6,1.9,3.3,3.7,4.7,5.8c5.8,8.5,11.5,17.2,17.2,25.7c7.6,11.3,17.9,18.4,31.5,20.8
                        c14.7,2.6,29.1,2.2,43.6-1.4c3.2-0.8,6.4-1.2,11.5-2.2c-19.1,39.9-30.9,80.5-38.8,123.4c-2.7-1.8-4.9-3.1-6.8-4.7
                        c-11.1-9.3-19.5-20.9-26.6-33.4c-17.8-31.7-28.1-66-34.2-101.6c-1.8-10.4-3.2-20.9-4.8-31.4C1996,978.7,1996.9,978.4,1997.8,978.1z"
                        />
                    <path class="st0" d="M1922.2,799.5c-2.2,5.5-3.6,9.5-5.5,13.3c-6.7,13.7-13.8,27.3-20.4,41.1c-7.9,16.5-19,30.4-33.6,41.4
                        c-7.4,5.6-14.8,11.3-22.3,16.9c-5.4,4-11.2,5.8-17.6,2c-6.2-3.7-8-9.2-7.4-16.2c1.7-18.3,4.3-36.3,11.5-53.4
                        c4.7-11.3,9.6-22.6,14.3-34c1.8-4.4,4.5-7.4,9.8-6.5c21.1,3.6,41.7,0.2,62.3-4.1C1915.5,799.5,1917.8,799.7,1922.2,799.5z"/>
                    <path class="st0" d="M2730.1,1231.1c2.7,1.7,3.9,2,4.2,2.7c18.3,53.8,36.5,107.6,55.1,162.5c-2.3,0.5-4.8,1.8-7.1,1.6
                        c-19.9-2-38.2,3.5-56,11.7c-3.9,1.8-8.1,3.2-11.9,5.1c-4.9,2.4-8.3,0.9-10.8-3.6c-1-1.7-1.9-3.5-2.8-5.3
                        c-27.2-53.4-54.5-106.7-81.4-160.2c-6.7-13.3-12.8-27.1-18-41.1c-13-35-27.8-69.1-44.1-102.6c-6.7-13.8-7.3-21-3.9-35.9
                        c2.6,2.1,5,3.6,6.7,5.7c15.3,18.9,31.1,37.4,45.3,57.2c7.2,10.1,12.6,22,16.7,33.8c17.1,49.3,33.3,98.9,50,148.4
                        c1.8,5.3,4,10.4,6.2,16.4c5.2-3.7,3.4-7.3,2.2-11c-6.5-19.9-12.9-39.9-19.3-59.8c-10.4-32.3-20.8-64.6-31-96.9
                        c-4.9-15.7-12-30.2-21.3-43.8c-4.9-7.1-9.3-14.6-13.3-22.2c-3.6-6.9-2.6-10,3.8-14.3c3.3-2.2,7-3.8,10.5-5.7
                        c5.4-2.8,10.2-2,14.7,1.9c14,11.8,28.4,23.2,42,35.4c34.2,30.5,49.1,69.2,49,114.7c-0.1,26.6,1,53.3,2,79.9
                        c0.3,9.3,1.7,18.6,3.1,27.8c0.5,3.6,0.5,8.3,6,9.2c4.2-3.5,2.2-8.1,2.2-12.2c-0.1-25-0.7-49.9-0.7-74.9
                        C2728,1247.8,2729.3,1240.2,2730.1,1231.1z"/>
                    <path class="st0" d="M2787.4,1570.9c-10.1,8.3-10.3,8.8-5.9,18.7c4.9,10.9,9.9,21.7,15,32.9c-5.3,2.9-8.3,0.9-10.7-2.6
                        c-2.8-4.1-5.8-8.2-8.2-12.5c-14.3-25.6-28.7-51-42.4-76.9c-9.4-17.7-13.3-36.7-12.4-56.9c0.5-10.6-0.3-21.3-1-31.9
                        c-0.4-7.3,2.4-11.9,9.1-14.9c12.8-5.6,24.9-12.5,38.9-15.1c19.7-3.6,37.7,0.5,54.4,10.9c12.6,7.9,23.3,18,32.2,29.7
                        c16.2,21.4,35.4,38.7,61,48.3c1.4,0.5,2.5,2,3.9,3.1c-1.3,8.3-7.1,11.3-14,12.2c-4.2,0.6-8.7,0.3-12.9-0.7
                        c-16.1-3.6-33-5.3-45.5-17.1c-11.7,5.4-12.4,7.1-7,17.6c10.3,19.8,20.9,39.4,31.1,59.2c3.7,7.1,6.7,14.5,9.9,21.8
                        c0.7,1.5,1.2,3.1,1.7,4.7c1,3.4,0.9,7.3-2.7,8.4c-2,0.6-5.3-1.7-7.2-3.5c-2.1-2-3.3-4.9-4.8-7.5c-10.3-17.9-20.6-35.8-30.9-53.7
                        c-2-3.4-4.1-6.8-6.1-10.1c-7.9,4.3-8.2,4.7-4.5,12c13,25.8,26.2,51.6,39.4,77.4c1.5,3,3.1,5.9,4.2,9.1c2.1,6.1,0.7,11-3.4,13.3
                        c-3.9,2.2-7.5,1.1-11.9-4.4c-3.5-4.4-6.6-9.2-9.2-14.2c-10.6-20.7-21-41.6-31.4-62.4c-1.6-3.2-3.4-6.4-5.1-9.5
                        c-8.6,3.7-9.1,4.5-5.3,12.7c9,19.3,18.1,38.5,27.2,57.8c1.8,3.9,3.6,7.9,5.5,11.8c1.5,3.3,2.2,7.1-1.5,8.7c-2.6,1.1-7.2,1.1-9.1-0.5
                        c-4.2-3.6-8.1-8-10.6-12.9c-6.8-13.3-12.7-27.1-19-40.7C2794.7,1585.8,2791.2,1578.7,2787.4,1570.9z"/>
                    <path class="st0" d="M2655.5,1021.4c26.2,36.9,48.6,74.6,52.8,123.1c-3.3-3-5.4-4.3-6.6-6.2c-14.7-22.2-31.2-43-49.5-62.4
                        c-5.4-5.7-7.8-12.4-6.2-20.1c2-10.4,4.4-20.7,6.8-31.1C2653,1024,2654,1023.3,2655.5,1021.4z"/>
                    <path id="front_B_18" class="st0" d="M2187.4,1502.9c1.2,4,2.5,6.6,2.7,9.3c2.3,40.9,5,81.8,6.5,122.7c0.8,22.9-0.1,46-0.7,68.9
                        c-1.1,41.6-2.6,83.2-4,124.8c-1.7,48.5-10.1,95.9-22.4,142.8c-8.4,32.2-16.3,64.5-25.5,96.5c-5,17.2-12,33.8-18.4,50.6
                        c-1.1,2.8-3.5,5.2-6.8,9.8c-0.8-4.6-1.3-6.7-1.4-8.7c-0.5-19.3-1.2-38.6-1.3-57.9c0-11.2-3.7-20.7-10.7-29.3
                        c-6.5-8-12.9-16.2-18.7-24.7c-14.4-21.2-19.8-44.4-16.7-70.2c4.5-38.7,7.8-77.5,11.1-116.3c1.6-18.6,1.5-37.3,2.8-55.9
                        c1.9-27.9,4-55.8,6.6-83.7c2.8-30.2,6.2-60.3,9.5-90.4c0.7-6.6,2.1-13.1,3.4-19.7c3.9-19.9,15.7-33.3,33.9-41.5
                        c9.7-4.3,19.6-8.5,29-13.4C2173,1513.1,2179,1508.4,2187.4,1502.9z"/>
                    <path id="front_B_15" class="st0" d="M2217.1,1430.6c-2.3,6.3-4,12.5-6.8,18c-9.5,18.8-24.1,33.3-40.8,45.9c-31.8,23.9-67.7,39.5-105.3,51.6
                        c-7.6,2.4-15.3,4.8-23.1,6.3c-9.9,1.9-13.7-1.9-12.7-12c1.4-14.2,2.9-28.5,4.4-42.7c3.1-28.7-0.9-56.4-10.4-83.5
                        c-5.5-15.8-16-27.4-30.5-35.6c-15.7-9-15.7-9.1-8.6-25.4c13-30,29.8-57.3,56-77.8c20-15.7,42.1-26.8,67.7-29.6
                        c13.7-1.5,16.7,0.3,20.5,13.1c15.8,52.6,41.6,100,74.5,143.7c3,4,6.3,7.9,8.6,12.2C2213.4,1419.7,2215,1425.1,2217.1,1430.6z"/>
                    <path id="front_B_13" class="st0" d="M1974,1322.5c0.4-4.4,0.3-7.3,0.9-10c5.7-24.6,11.3-49.3,17.3-73.9c5.6-22.8,17.7-41.6,37.1-55.1
                        c5.7-3.9,12.1-7,18.5-9.7c25.9-10.8,41.9-1,54.3,24.5c4.2,8.6,6.4,18.2,9.1,27.4c2.1,7.1,1.2,8.7-6,10.8c-11.2,3.3-22.3,6.7-33.7,9
                        c-36.3,7.4-62.6,28-81.1,59.5C1986.7,1311.4,1983.3,1317.9,1974,1322.5z"/>
                    <path class="st0" d="M2061.1,2058c-6.7-15.2-12.9-28.7-18.4-42.5c-1.2-3-0.8-7.6,0.6-10.6c4.5-9.5,9.9-18.6,15.5-28.9
                        c1.6,3.2,2.6,5.2,3.6,7.1c7,14.3,13.6,28.7,20.9,42.8c4.7,9.1,5,10-2.9,16.6C2074.5,2047.4,2068.5,2052,2061.1,2058z"/>
                    <path class="st0" d="M1999.2,1190.3c2.1-22.5,6.8-44,13.3-66.8c9.8,11.8,18.6,22.4,28.3,34
                        C2027.4,1169.1,2014.4,1180.5,1999.2,1190.3z"/>
                    <path class="st0" d="M2023.1,1181.3c-2.4,2.2-4.8,4.4-7.3,6.6c-0.4-0.5-0.9-0.9-1.3-1.4c2.4-2.2,4.8-4.4,7.2-6.6
                        C2022.2,1180.4,2022.7,1180.9,2023.1,1181.3z"/>
                    <path id="front_B_8" class="st0" d="M2465.9,846.6c1.1,30.2-1.2,61.6-11.7,92c-6.3,18.1-15.4,34.6-27.7,49.4c-10.7,12.8-24,21.9-40,27.1
                        c-17.1,5.5-33.6,4-49-5.2c-28.2-16.7-51.1-39.4-70.5-65.6c-5.8-7.8-6.2-15.9-1.6-24.3c1.4-2.6,3-5.2,4.7-7.7
                        c27.3-41.8,54.6-83.6,82.1-125.3c14.5-21.9,24.9-45.5,29.7-71.4c0.7-3.6,1.4-7.2,2.8-10.6c1.9-4.9,4.7-5.8,9.5-3.8
                        c1.2,0.5,2.4,1.2,3.5,2c36.2,25.2,58.7,59.4,65.7,103.1C2465.3,818.9,2465,832,2465.9,846.6z"/>
                    <path class="st0" d="M2499.7,962.5c6.3,4.8,10.9,8.4,15.7,11.8c12.4,8.7,20.8,8.3,32.4-1.3c10.9-9.2,15.7-21.1,16.1-35.1
                        c0.3-9.9,0.9-19.7,1.4-31.3c4.8,2.2,8,3.5,11.1,5.1c13.5,7.3,27.1,14.2,42.7,16c6,0.7,8.2,5.2,10.1,9.9
                        c5.9,14.6,10.1,29.8,10.4,45.5c0.4,16.9-1.2,33.8-2.2,50.7c-0.4,6.7-4,10.9-10.9,12.4c-12,2.7-23.7,6.7-35.8,8.4
                        c-24.2,3.4-42.9-5.6-55.2-27.1c-11.6-20.2-23.2-40.4-34.7-60.6C2500.4,966.4,2500.4,965.7,2499.7,962.5z"/>
                    <path class="st0" d="M2548.4,923.3c-1.5,10.5-2.1,21.2-4.8,31.4c-3.6,13.8-13.5,16.3-23.9,6.8c-29.4-26.9-46.1-59.7-45.8-100.3
                        c0.1-20.3-0.2-40.6-0.2-60.9c0-3.3-0.2-6.7,0.6-9.9c1.6-7,8.6-9.2,13.6-4c4.1,4.3,7.5,9.3,11.2,14.1c13.9,18.3,27.6,36.7,35.8,58.5
                        C2542.7,879.6,2547.2,900.9,2548.4,923.3z"/>
                    <path id="front_B_12" class="st0" d="M2378.6,1150.5c-9-43.1-19.6-83.2-37.8-122.4c8.7,1.1,15.8,2.6,23,2.9c9.6,0.3,19.3,0.8,28.7-0.7
                        c17-2.7,29.7-12.6,38.9-27.1c3.5-5.6,6.8-11.4,10.3-17.1c2.3-3.6,4.7-7.1,8.1-12.2c0.7,3.4,1.5,5.2,1.2,6.9
                        c-5.5,40-14.2,79.1-31.3,116c-7.3,15.8-16.2,30.6-28.4,43.1C2387.9,1143.3,2383.9,1146.1,2378.6,1150.5z"/>
                    <path class="st0" d="M2519.2,798.4c12.7,2.2,23.4,4.7,34.2,5.6c11.2,0.9,22.5,0.7,33.8,0.3c9.6-0.4,10.6-0.4,14.7,8.2
                        c10.1,21.7,19.5,43.8,23.2,67.7c1,6.5,1.5,13.3,1.1,19.9c-1,14.8-10.8,20.3-23.3,12.6c-24.2-14.8-44.6-33.6-57.5-59.5
                        c-7.3-14.6-14.4-29.2-21.5-43.9C2522.4,806.5,2521.3,803.4,2519.2,798.4z"/>
                    <path class="st0" d="M1708.3,1231.4c5.4,3.4,5.1,7.6,5.1,11.5c0.1,29.3,0.1,58.6,0.2,88c0,3.6,0.4,7.2,0.5,10.5
                        c4.8,1.9,6.3-0.7,6.8-3.9c1-5.6,1.8-11.2,2.5-16.8c3.1-25.8,4.8-51.7,4-77.8c-0.6-19.6-1.1-39.3,3.3-58.7
                        c6.3-27.6,21.7-49.9,41-69.7c8.3-8.5,18-15.7,27.1-23.5c4.8-4.1,9.8-8,14.5-12.2c9.4-8.4,22-8.2,31.2,0.8c3.8,3.7,4.5,8.2,2.6,12.8
                        c-2.1,4.9-4.2,9.9-7.4,14.1c-13,16.8-21.8,35.8-28.3,55.8c-16.9,51.9-33.6,103.8-50.3,155.7c-0.7,2.3-0.5,4.9-1,9.7
                        c3.1-2.6,4.9-3.5,5.6-4.9c1.8-3.6,3.2-7.3,4.4-11.1c14.8-44.5,29.6-89,44.2-133.6c9.4-28.5,23.1-54.4,43.3-76.9
                        c7.8-8.7,14.8-18,22.3-27c2.5-3,5.2-5.9,8.7-9.7c4.2,9.8,3.1,18.5,0.6,27c-1.6,5.4-4,10.6-6.5,15.6c-13.3,26.5-24.9,53.7-35.2,81.5
                        c-16.4,44.4-37.5,86.7-59.6,128.6c-14.8,28-29.3,56.1-43.9,84.1c-1.5,3-3,5.9-4.8,8.8c-3.7,6.1-5.7,6.8-12.2,4.2
                        c-4.9-2-9.8-4.2-14.7-6.3c-16.7-7.3-33.9-12-52.3-10c-2.2,0.2-4.6-0.3-8.1-0.7c1.2-4,2.2-7.3,3.4-10.7
                        c13.8-39.2,27.8-78.4,41.5-117.7c3.6-10.4,6.3-21,9.4-31.5C1706.5,1235.5,1707.4,1233.8,1708.3,1231.4z"/>
                    <path class="st0" d="M1788.4,1021c3.7,14.4,7.1,27.6,8,41.1c0.2,3.5-1.8,8-4.3,10.7c-19,21-36.7,43.1-52.7,66.6
                        c-0.5,0.7-1.5,1-2.3,1.5C1732.3,1115.5,1762.5,1043.8,1788.4,1021z"/>
                    <path class="st0" d="M1631,1556.4c-3.4,6.7-7,13.5-10.4,20.3c-8.7,17.2-17.1,34.6-26.1,51.7c-3.1,5.8-7,11.4-11.5,16.2
                        c-5.2,5.6-12,3.3-12.7-4.4c-0.4-4.3,1-9.3,3-13.3c8.1-16.8,16.8-33.3,25.2-49.9c5.3-10.4,10.6-20.7,15.7-31.2
                        c2.9-5.9,2.7-6-4.2-11.6c-2,3.1-4,6-5.8,9.1c-11,19-21.9,38.1-32.9,57.1c-1.5,2.6-2.7,5.6-4.9,7.5c-1.6,1.4-5.1,2.6-6.6,1.8
                        c-1.7-0.9-2.8-4.3-2.8-6.5c0-2.5,1.4-5.1,2.6-7.5c12.9-25.5,25.9-51.1,38.8-76.6c1.2-2.4,2.5-4.7,3.7-7.1c2.1-4.5,1.3-8.3-2.5-11.6
                        c-3.5-3-6.8-2.1-10,0.2c-13.8,9.9-30.1,12.1-46,15.4c-10.4,2.1-18.4-0.9-23.2-12.2c2.9-1.7,5.8-3.9,9-5.2
                        c21.5-8.9,38.7-23.2,52.6-41.7c8.6-11.5,18.5-21.9,30.2-30.4c23.3-17,48.6-20.9,75.6-10.6c8.7,3.3,16.9,7.8,25.4,11.6
                        c4.8,2.2,6.9,5.9,6.8,11.1c-0.2,18.3-0.3,36.7-0.6,55c-0.1,2.9-0.8,6-1.7,8.8c-6.6,19.6-14.4,38.6-25.3,56.3
                        c-11.5,18.7-22.2,37.9-33.3,56.9c-1.2,2-2.5,4-4,5.7c-2.4,2.7-2.5,2.7-9.4,0.9c2.3-5.2,4.5-10.3,6.8-15.3c2.8-6,5.9-12,8.7-18
                        c4.4-9.5,4.1-10.3-6.3-17.1c-1.7,3.2-3.6,6.2-5.1,9.3c-7.9,16.9-15.4,33.9-23.7,50.7c-2.4,4.9-6.1,9.5-10.2,13.1
                        c-2.5,2.3-6.9,2.4-10.4,3.5c-3.8-6.8-1.1-11.7,1.1-16.4c9.4-19.9,18.9-39.7,28.4-59.5c1-2.1,2.1-4.2,3-6.3
                        C1640.3,1559.9,1639.6,1559,1631,1556.4z"/>
                    <path id="front_B_11" class="st0" d="M2230.9,1380.6c-0.6-5.1-1.1-7.5-1.1-10c0.3-90,0.6-180,1-270c0.1-28.7,0.2-57.3,1.1-86
                        c0.6-18.1,5.9-35.4,12.8-52.1c3.4-8.3,6.5-8.8,13.4-3c0.8,0.6,1.5,1.3,2.2,2.1c20.4,23.3,41.8,45.9,60.9,70.2
                        c22.4,28.4,33,62,36.9,97.8c0.5,5,1,9.9,1.5,14.9c0.4,4.6-1.5,7.8-5.9,9.2c-17.4,5.6-27.6,18.9-36.6,33.7
                        c-12.2,20.1-19.4,42.3-27,64.3c-7.3,21.4-14.5,42.9-22.3,64.1c-7.1,19.5-17.1,37.5-29,54.6C2237,1373.4,2234.7,1376,2230.9,1380.6z"
                        />
                    <path id="front_B_6" class="st0" d="M2560.6,792.1c-33-1.8-60.8-12.8-83.6-35.5c-12.5-12.5-24.6-25.4-37.6-37.3c-11.5-10.6-23.7-20.4-36.1-29.9
                        c-9.9-7.6-12-11.8-8.3-23.8c6.4-21.1,19.2-37,38.7-47.5c9.6-5.2,19.4-6.1,29.8-2.3c24.6,8.9,47.4,21,68.2,37
                        c29.8,22.9,46.3,53.5,52.2,90.2c2,12.2,3.3,24.4,4.6,36.7c0.9,8-0.4,9.5-8.2,10.5C2573.3,790.9,2566.4,791.5,2560.6,792.1z"/>
                    
                    <!-- FRONT -->
                    <path id="front_F_6" class="st0" d="M737.9,753.6c-0.1,15,1.1,31.6-3.1,48c-10.2,39.5-38.3,62-80.9,64.4c-21.4,1.2-41.6-3.4-61-12.2
                        c-25.7-11.7-48-28.4-68.9-47.2c-6.5-5.9-8.1-12.8-4.3-20.7c9.9-20.3,20.2-40.4,30-60.8c8.4-17.4,16.5-34.8,24.2-52.5
                        c4.7-10.7,11.2-19.5,20.7-26.3c10.5-7.5,21.9-9.5,34.1-5.4c23,7.8,45.8,15.9,68.7,24.1c22.5,8.1,35.4,24.1,37.8,48
                        C736.3,726.1,736.9,739.1,737.9,753.6z"/>
                    <path class="st0" d="M172.7,1650.9c1.1-4.5,1.4-8.5,3.1-11.8c8.9-18.3,18-36.4,27.1-54.5c3-5.9,6.4-11.7,9.3-17.7
                        c3.4-6.9,3.1-7.8-4.3-14.8c-2.1,3.5-4.3,6.7-6.1,10.1c-12.3,22.9-24.5,45.8-36.8,68.6c-2.4,4.4-4.9,8.7-7.9,12.7
                        c-4.7,6.3-9,7.8-13.4,5.2c-4.6-2.6-5.4-6.8-2.1-14.1c4.4-9.7,9-19.3,14.1-28.7c12.4-22.8,25.2-45.4,37.8-68.1
                        c4.2-7.6,4.1-7.7-3.3-13.2c-2.1,3.3-4.4,6.5-6.4,9.8c-12.5,20.9-24.9,41.8-37.3,62.6c-2,3.4-4.2,6.8-6.6,10c-2.5,3.2-5.5,6.8-10,4.4
                        c-3.9-2-3.4-6.3-2.2-9.8c1.6-4.7,3.3-9.4,5.7-13.7c13-22.9,26.2-45.6,39.4-68.4c1.8-3.1,3.5-6.3,5.5-9.9c-5.8-6.6-12.8-8.3-20.7-6.7
                        c-9.8,2-19.5,4.5-29.2,6.5c-12.8,2.6-18.3-0.1-24.8-12.6c3.6-2,7-4.5,10.8-5.9c12.6-4.5,22.5-12.6,31.6-22
                        c17.2-17.6,34.6-35.1,52.3-52.3c3.4-3.3,8.1-6.2,12.6-7.4c13.3-3.5,23.2-11.3,30.9-22.2c18.7-26.4,37.4-52.7,56.1-79
                        c2.1-2.9,4.3-5.7,7-9.2c3.3,4.7,1.7,8.8,0.9,12.2c-9.2,38.8-10.4,78.5-14,117.9c-1.3,14.4,0.4,28.5,5.1,42.3
                        c7.5,22.1,4.2,42.8-10.1,61.3c-17.4,22.4-33.9,45.3-47,70.5c-3.3,6.4-8.1,12.2-12.8,17.8c-1.8,2.1-5.1,2.8-7.8,4.2
                        c-0.6-0.5-1.2-1.1-1.8-1.6c1.3-3.6,2.3-7.2,3.9-10.7c3.4-7.2,7.2-14.3,10.5-21.5c3.6-8,3.1-8.9-6.6-13.7c-1.7,3-3.5,6.1-5.1,9.2
                        c-8.9,17.9-17.7,35.8-26.6,53.6C188.3,1650.5,184.8,1652.4,172.7,1650.9z"/>
                    <path class="st0" d="M371.8,1116.1c-1.3-19.2-2.8-38.5-3.8-57.7c-0.8-17-5.3-32.7-15.2-46.7c-11.4-16-15.7-34.3-15.9-53.7
                        c-0.4-40.5,12.7-76.5,37.3-108.3c10.2-13.2,23.3-22.8,39.1-28.4c16.3-5.8,32.8-11.1,49.2-16.5c1.3-0.4,2.6-0.7,3.9-0.9
                        c10.4-1.6,15.5,2.3,15.2,12.8c-0.2,10.3-0.7,20.7-2.2,30.9c-9.2,61.9-25.5,121.9-51.8,178.6c-12,25.8-28.2,49.7-42.6,74.4
                        c-3.3,5.6-7.1,11-10.7,16.5C373.5,1116.6,372.7,1116.4,371.8,1116.1z"/>
                    <path id="front_F_5" class="st0" d="M386.1,822.8c-1-2.4-1.6-3.3-1.7-4.1c-1.2-29.7-0.8-59.3,5.1-88.5c1.4-6.8,3.3-13.7,6.5-19.8
                        c23.1-45.2,58-75.5,109.2-84.6c5.6-1,11.1-2.1,16.6-3.2c9-1.8,17.2-0.3,24.6,5.2c4.8,3.6,9.6,7.2,14.4,10.8c6,4.5,8.3,10.4,5.8,17.6
                        c-4.2,12.2-7.4,25-13.3,36.5c-12.8,25.2-26.9,49.7-40.6,74.4c-3.9,7-10,11.6-17.6,14c-14.6,4.6-29.1,9.4-43.8,13.4
                        c-19.1,5.2-37.2,12.5-54.3,22.3C393.8,818.5,390.3,820.4,386.1,822.8z"/>
                    <path id="front_F_9" class="st0" d="M546.6,1098.4c-5-3.2-5.9-8.6-6.5-13.7c-7.3-62.2-13-124.5-9.7-187.2c0.7-13.3,2-26.5,3.3-39.8
                        c1.2-12.1,3.3-13.5,14.4-9.4c17.8,6.6,35.4,13.4,53.2,20c9,3.4,18.1,6.5,27.2,9.7c12.1,4.1,13.5,6.3,11,19.1
                        c-3.2,17-6.6,33.9-9.9,50.9c-2.3,11.4-4.8,22.8-6.9,34.2c-3.2,17.1-10.9,32-22.5,44.9c-6.7,7.4-13.6,14.7-20.5,21.8
                        c-12.5,12.7-22.8,26.9-28.9,43.8c-0.6,1.5-1.3,3-2.1,4.4C548.2,1097.6,547.4,1097.8,546.6,1098.4z"/>
                    <path class="st0" d="M306.8,1428.6c0-11.3-0.6-21.5,0.1-31.6c4.2-56.1,16.4-110.6,33.8-163.9c9.2-28.1,19.8-55.8,30.1-83.6
                        c7.8-21.1,19.1-40.3,33.5-57.6c10-12,19.8-24.2,29.9-36.3c2.1-2.5,4.6-4.5,8.2-7.9c2.1,4.3,4.3,7.3,4.9,10.5
                        c2.2,12.3,2.2,24.5-3.4,36.2c-9.2,19-14.4,39.3-19.7,59.6c-3,11.6-6.3,23.1-10.2,34.4c-27.5,80.5-62.9,157.6-101.3,233.4
                        c-0.9,1.8-1.9,3.5-3,5.2C309.5,1427.5,308.7,1427.7,306.8,1428.6z"/>
                    <path class="st0" d="M230,1375.1c1.5-3.6,2.9-7.2,4.5-10.8c22.2-48.1,36.2-98.5,43.1-150.9c2.8-21.4,4.5-43.1,8.7-64.2
                        c7.8-39.7,24.2-75.9,48.1-108.6c2.7-3.7,5.4-9.2,10.5-7.3c3.3,1.2,6,5.9,7.5,9.6c6.5,15.7,8,32,6.6,48.9
                        c-6.8,84.8-38.2,160.5-85.7,230.2c-12.4,18.1-26.3,35.2-39.6,52.7c-0.5,0.7-1.6,1-2.4,1.5C230.9,1375.8,230.4,1375.5,230,1375.1z"/>
                    <path id="front_F_3" class="st0" d="M551.5,614.4c3.5-1.9,6.2-3.6,9.2-5c26.9-12.4,53.8-24.7,80.7-37c1.8-0.8,3.7-1.6,5.5-2.3
                        c3.6-1.3,6.7-0.7,8.5,2.9c13.3,26.3,34.5,46.3,54.1,67.5c1.7,1.8,2.9,4.1,5.7,8c-7.3-1.4-12.4-2.1-17.5-3.4
                        c-22.5-5.8-45-11.8-67.5-17.8c-14.3-3.9-28.3-3.8-41.8,2.8c-6.7,3.3-12.3,2.8-18.2-2.5C564.9,622.8,558.4,619.2,551.5,614.4z"/>
                    <path class="st0" d="M459.5,799.5c-15.9,5.5-31.8,10.9-48.6,16.7C419.9,808.7,444.9,799.8,459.5,799.5z"/>
                    <path class="st0" d="M548.5,717.6c-4.8,9.1-9.7,18.3-14.5,27.4c-0.6-0.3-1.2-0.6-1.8-0.9c4.7-9.3,9.3-18.5,14-27.8
                        C547,716.7,547.7,717.2,548.5,717.6z"/>
                    <path class="st0" d="M470.1,920.9c-1.4,5.1-2.8,10.3-4.2,15.4C467.3,931.1,468.7,926,470.1,920.9z"/>
                    <path class="st0" d="M466.1,935.8c-1.4,4.8-2.8,9.7-4.2,14.5C463.3,945.4,464.7,940.6,466.1,935.8z"/>
                    <path class="st0" d="M566,676.1c-0.7,2.8-1.4,5.6-2.1,8.4c-0.6-0.3-1.3-0.5-1.9-0.8c1.4-2.5,2.8-4.9,4.2-7.4
                        C566.2,676.2,566,676.1,566,676.1z"/>
                    <path class="st0" d="M251.5,1361.6c1.6-2.5,3.3-5.1,4.9-7.6c0.6,0.5,1.2,0.9,1.8,1.4c-2,2.3-4,4.6-5.9,7
                        C252.1,1362.1,251.8,1361.8,251.5,1361.6z"/>
                    <path class="st0" d="M569.8,665.8c-1.3,3.4-2.5,6.8-3.8,10.2c0,0,0.2,0.2,0.2,0.2c1.3-3.4,2.5-6.8,3.8-10.2
                        C570.1,666,569.8,665.8,569.8,665.8z"/>
                    <path class="st0" d="M1313.8,1525.3c-7.5,3.7-7.8,4.2-5.4,9.5c1.2,2.7,2.9,5.2,4.3,7.9c15.1,27.8,30.2,55.6,45.2,83.4
                        c1.9,3.5,3.3,7.3,4.8,10.9c1.8,4.4,1.8,8.5-2.6,11.5c-3.9,2.6-7.9,1.8-12.1-3.3c-3.2-3.8-5.9-8.1-8.3-12.4
                        c-12.7-23.4-25.3-46.9-37.9-70.3c-1.7-3.2-3.5-6.3-5.3-9.4c-8.1,4.5-8.9,6-6.1,12.4c1.7,4,4,7.7,6,11.5c10,19.6,20,39.1,29.9,58.7
                        c0.8,1.5,1.4,3,2.1,4.5c1.5,3.7,3.9,8.3-0.3,10.7c-2.3,1.3-7.3,0.1-10-1.7c-3.4-2.3-6.3-5.9-8.2-9.6c-8.7-16.8-17-33.9-25.5-50.9
                        c-1.9-3.8-4-7.6-6-11.2c-9.7,3.7-10.6,5.5-6.7,13.8c4.9,10.5,10.2,20.9,15.3,31.5c-4.4,4.6-7.5,2.1-10-1.2
                        c-5.3-7.2-11.1-14.3-15.3-22.2c-11.5-21.6-24.7-41.9-40.4-60.6c-18.3-21.8-23.1-46-13.4-72.7c5.1-13.9,4.9-28.2,3.6-42.5
                        c-2.8-30.5-6.2-61-9.7-91.4c-1-8.9-3.4-17.6-5-26.5c-0.4-2.2-0.5-4.4,0.9-7.6c2.4,2.9,5,5.7,7.2,8.8c15.8,22.3,31.4,44.7,47.2,67
                        c4.2,6,9.2,11.4,13.8,17.1c6.8,8.5,15,14.5,25.7,17.9c6.7,2.2,13.2,6.7,18.3,11.7c19,18.6,39,36.3,55.8,57.2c3.9,4.8,11,7,16.8,10.1
                        c5.7,3.1,11.7,5.8,17.3,8.5c-4,11.1-10.1,15.1-21.2,13.5c-9.5-1.4-18.9-3.9-28.3-6c-8.8-2-17.1-1.8-25.2,4.9c2,3.9,3.7,7.8,5.8,11.4
                        c13.1,22.8,26.3,45.6,39.3,68.5c2.4,4.3,4.6,9,5.6,13.7c0.6,2.7-0.8,6.9-2.8,8.7c-3,2.7-6.3-0.2-8.5-2.7c-2.4-2.7-4.4-5.8-6.3-9
                        c-12.8-21.4-25.6-42.9-38.3-64.4C1318,1531.8,1315.8,1528.6,1313.8,1525.3z"/>
                    <path class="st0" d="M1129.8,1116.7c-1.9-2.5-3.9-4.9-5.6-7.6c-9.5-14.8-18.8-29.9-28.5-44.6c-19.4-29.2-30.4-62-41.8-94.7
                        c-16-45.8-26.2-92.8-31.6-140.9c-0.6-4.9-0.8-10-0.2-14.9c1-9,5.9-12.7,14.5-10.1c19.1,5.7,38.3,11.4,56.8,18.6
                        c18.4,7.2,32.5,20.4,43.4,37c21.5,32.8,32.1,68.4,29.5,107.8c-1,15-5.2,28.9-13.8,41.2c-12.5,17.9-17.5,37.9-18.5,59.2
                        c-0.6,12.6-0.8,25.3-1.3,37.9c-0.1,3.5-0.7,7-1.1,10.5C1131,1116.3,1130.4,1116.5,1129.8,1116.7z"/>
                    <path id="front_F_8" class="st0" d="M1118.4,822.5c-3.2-1.5-5.6-2.4-7.8-3.8c-20.4-12.4-42.6-20-65.5-26.3c-13.4-3.7-26.7-8.1-39.7-13.1
                        c-4.7-1.8-9.7-5.3-12.3-9.5c-19.9-31.9-39.4-64.1-52.2-99.7c-1.7-4.7-3.3-9.4-4.4-14.3c-1.5-6.3,0-11.7,4.9-16.3
                        c13.9-12.9,29-19.4,49-15.9c47,8.2,83.4,31.5,109.3,71.5c7.3,11.3,12.2,23.4,14.8,36.7c5.5,28.7,5.6,57.6,5.1,86.6
                        C1119.5,819.4,1119,820.3,1118.4,822.5z"/>
                    <path class="st0" d="M1195.8,1429.7c-0.9-1.3-1.9-2.5-2.6-3.9c-18.2-37.4-37.9-74.1-54.3-112.3c-19.4-45.2-36.3-91.5-53.6-137.6
                        c-5.5-14.6-8.2-30.1-12.8-45.1c-3.5-11.4-7.7-22.7-12.1-33.8c-4.8-12.1-6.1-24.5-4.3-37.2c0.4-2.6,0.9-5.5,2.4-7.5
                        c1-1.3,3.9-1.9,5.8-1.7c1.3,0.1,2.7,1.8,3.8,3c26.3,29.8,51.7,60.5,66,98.1c23.2,61,44.6,122.6,55.2,187.3
                        c4.4,26.6,6.1,53.5,8.9,80.3c0.3,3.2,0,6.4,0,9.6C1197.3,1429.3,1196.6,1429.5,1195.8,1429.7z"/>
                    <path class="st0" d="M1158.2,1032.4c6.6,1.2,9.2,5.6,12.1,9.6c23.8,33.2,40,69.9,47.6,110c4.9,25.8,8.3,51.9,11.6,78
                        c5.8,47,20,91.5,39.7,134.4c1.3,2.9,2.3,5.9,1.9,9.7c-1.8-1.8-3.8-3.4-5.4-5.3c-52.4-62.8-90.7-132.8-110.7-212.4
                        c-6.9-27.2-10.4-54.9-10-83.1c0.2-11.5,1.6-22.6,7.3-32.8C1153.8,1037.5,1156.3,1034.9,1158.2,1032.4z"/>
                    <path id="front_F_4" class="st0" d="M949,614.3c-8.4,6.2-15.7,11.9-23.4,17.1c-1.9,1.3-5,1.3-7.5,1c-2.6-0.3-5-1.7-7.4-2.8
                        c-12.6-5.9-25.5-6.2-38.9-2.5c-22.7,6.2-45.6,12-68.4,17.9c-5,1.3-10,2.1-15,3.2c-0.4-0.7-0.8-1.3-1.2-2c1.9-2.5,3.6-5.2,5.7-7.5
                        c7.4-8.1,15.4-15.7,22.2-24.3c9.9-12.5,19-25.6,28.3-38.5c5.1-7,6.2-7.7,13.9-4.2c29.1,13.1,58,26.4,87,39.7
                        C945.6,611.9,946.6,612.8,949,614.3z"/>
                    <path class="st0" d="M1035.1,922.1c-0.8-0.5-2.2-0.9-2.4-1.6c-3-13.2-5.9-26.4-8.8-39.6C1027.7,894.6,1031.4,908.4,1035.1,922.1z"/>
                    <path id="front_F_" class="st0" d="M1074,807.9c-6-1.3-12.1-2.6-20.4-4.3c9.1-1.4,12.7-0.4,20.2,4.5C1073.9,808.1,1074,807.9,1074,807.9z"/>
                    <path id="front_F_" class="st0" d="M1084,812c4.2,1.3,8.4,2.7,12.6,4c-4.9,0.5-9.2-0.5-12.5-4.2L1084,812z"/>
                    <path class="st0" d="M1045.1,958.1c-4.6-3.7-4.7-9.1-5.2-14.3C1041.6,948.6,1043.4,953.4,1045.1,958.1z"/>
                    <path class="st0" d="M1040.1,944.2c-1.4-5.1-2.8-10.2-4.2-15.4C1037.3,934,1038.7,939.1,1040.1,944.2z"/>
                    <path class="st0" d="M1050.1,978.2c-1.4-4.8-2.8-9.6-4.2-14.4C1047.4,968.7,1048.7,973.5,1050.1,978.2z"/>
                    <path class="st0" d="M1084.2,811.8c-3.4-1.3-6.8-2.6-10.1-3.9c0,0-0.2,0.2-0.2,0.2c3.4,1.3,6.8,2.6,10.1,3.9
                        C1084,812,1084.2,811.8,1084.2,811.8z"/>
                    <path class="st0" d="M1249.6,1360.4c-1.9-2.3-3.9-4.5-5.8-6.8c0.6-0.4,1.2-0.9,1.8-1.3c1.5,2.5,3,5,4.5,7.5L1249.6,1360.4z"/>
                    <path id="front_F_16" class="st0" d="M746.4,1451.7c-18.4-4.1-32.4-12.8-42.9-27.1c-15.4-20.9-25.5-44.4-33.8-68.7c-12.7-37.4-20-75.9-23.9-115.1
                        c-0.9-8.9-1.9-17.9-2.1-26.9c-0.2-11.7,3.5-15,15.1-14.2c5.3,0.4,10.5,1.5,15.7,2.8c15.2,3.8,30.3,7.9,45.4,11.6
                        c12,3,18.6,10.1,20.3,22.7c4.3,31.8,6,63.6,6.4,95.6c0.4,34,0.5,67.9,0.7,101.9C747.4,1440,746.8,1445.6,746.4,1451.7z"/>
                    <path id="front_F_17" class="st0" d="M756.9,1451.7c0-10.5-0.1-19.4,0-28.3c0.9-50,1.5-99.9,2.9-149.9c0.4-14.2,3.2-28.4,5.1-42.6
                        c0.9-6.9,4.9-11.5,11.4-13.9c20.6-7.7,41.5-14.5,63.4-17.5c1.3-0.2,2.6-0.5,4-0.5c10.9-0.5,15.8,3.3,15.9,14.2
                        c0.1,10.3-0.6,20.6-1.7,30.9c-5,47.6-15.1,93.9-33.9,138.1c-6.7,15.7-14.3,30.8-25.1,44.2C788.6,1439.4,775.7,1448.1,756.9,1451.7z"
                        />
                    <path id="front_F_18" class="st0" d="M816.3,1423.5c2.5-5.4,4.6-10.2,7.1-14.9c20.2-39.2,36.8-79.9,45.2-123.3c3.6-18.6,5.9-37.5,7.2-56.4
                        c1-14.2,5.8-24.5,18.6-31.7c18.7-10.5,32.4-26.4,43.7-44.4c2.6-4.2,5.3-8.3,8.9-14c1.9,5,3.7,8.5,4.6,12.1
                        c5.7,23.3,9.6,47,10.2,71.1c0.5,19.9-3.2,38.9-11.6,57.2c-25.2,55.2-62.1,100.7-111.3,136.1C832.5,1419.9,826.1,1424.7,816.3,1423.5
                        z"/>
                    <path id="front_F_15" class="st0" d="M687.8,1423.6c-7.4,2.3-12.4-1.1-17.4-4.4c-18.9-12.6-35.9-27.5-51.7-43.9c-25.9-26.8-46.7-57.2-62.6-90.9
                        c-12.7-26.9-17.5-55-12.4-84.5c2.9-16.7,6-33.4,9.2-50c0.5-2.8,1.7-5.6,3.2-10.5c3,3.8,5.1,6.1,6.7,8.7c13.6,22.2,30.6,41,53.8,53.6
                        c7.8,4.3,10.4,11.4,10.9,20c0.9,14.3,1.6,28.6,3.6,42.7c6,42.2,20,81.9,38.1,120.3c4.5,9.6,9.4,19.1,14.1,28.7
                        C684.9,1416.7,686.2,1420,687.8,1423.6z"/>
                    <path id="front_F_11_3" class="st0" d="M759.9,1145.1c0-15.6-1-31.4,0.3-46.9c1.7-20.2,4.6-21.5,23.3-22c27.2-0.8,53,4.4,76.8,18.1
                        c12.4,7.1,14.1,10.4,11.9,24.2c-2.7,17.2-7.9,33.6-15.7,49.3c-5.2,10.6-13.3,17.2-24.7,20c-15.8,3.9-31.6,8-47.4,12.1
                        c-1.9,0.5-3.9,1-5.8,1.4c-14.4,3.4-18.6,0.4-18.8-14.1c-0.2-14,0-28,0-42C759.7,1145.1,759.8,1145.1,759.9,1145.1z"/>
                    <path id="front_F_14" class="st0" d="M865.3,1192.1c1.8-3.1,2.7-5.1,4.1-6.8c17-20,23.4-43.6,24.4-69.2c0.9-23.7-1.4-47.2-6.8-70.3
                        c-0.4-1.6-0.7-3.3-0.9-4.9c-0.1-0.6,0.2-1.3,0.5-2.9c2,1,4.1,1.6,5.6,2.9c15.3,12.5,28.6,26.8,40,43c8.4,11.9,9.9,24.2,4.6,37.7
                        c-6.9,17.6-16.4,33.4-29.5,47c-10.1,10.5-22.1,18.1-36.1,22.4C869.9,1191.5,868.3,1191.6,865.3,1192.1z"/>
                    <path id="front_F_13" class="st0" d="M617.9,1039.1c-0.9,5.2-1.7,10.4-2.8,15.5c-5.7,28.6-7.7,57.3-1.8,86.2c3.4,16.3,9.9,31,20.7,43.7
                        c1.3,1.5,2.4,3.2,3.4,4.9c0.3,0.5,0,1.3,0,3.2c-3.9-0.9-7.8-1.3-11.3-2.7c-11-4.4-20.4-11.2-28.4-19.9
                        c-13.7-14.9-24.1-31.8-31.7-50.5c-5-12.3-4.2-24.4,5-34.6c13.6-15.1,27.6-29.8,41.5-44.6c0.8-0.9,2.1-1.4,3.2-2
                        C616.5,1038.5,617.2,1038.8,617.9,1039.1z"/>
                    <path id="front_F_23" class="st0" d="M968.8,2049.5c-2-2.6-4.1-5-5.9-7.7c-12.7-19.1-25.1-38.5-38.1-57.5c-22-32.3-34.1-68.3-40.8-106.4
                        c-8.5-47.9-13.8-96.2-14.8-144.9c-0.7-37.9,4.5-75.1,13.5-111.7c22.4-91.4,45-182.8,67.6-274.2c1-4,3.1-7.7,6.6-11.3
                        c0.3,1.7,0.7,3.4,0.8,5.1c1.5,42.9,2.5,85.9,4.7,128.8c1,20.2,4.4,40.4,6.4,60.6c3.3,33.5,6.3,67,9.2,100.5
                        c1.7,19.2,2.6,38.5,4.2,57.8c2.7,31.6,1,63.2-0.3,94.9c-2.9,68.5-5.7,137.1-8.5,205.7c-0.6,16-1,32-1.5,47.9
                        c-0.1,3.9-0.4,7.8-0.7,11.7C970.4,2049,969.6,2049.3,968.8,2049.5z"/>
                    <path id="front_F_22" class="st0" d="M824.6,1767.7c-0.9-3-2-5.9-2.8-9c-14.3-54.8-23.6-110.5-30.4-166.8c-4.2-34.5-6.2-68.9-6.1-103.6
                        c0-9.7,3.5-16.7,11.7-21.5c2.9-1.7,5.7-3.5,8.6-5c40.4-21.1,74-50.8,103.9-84.8c7.8-9,13.9-19.5,20.8-29.3c1.5-2.1,3-4.3,6.1-6.3
                        c0,2.4,0.3,4.8,0,7.1c-5.7,34.2-14.6,67.6-24.9,100.6c-31.9,101.6-59,204.4-81.7,308.5c-0.7,3.4-2.1,6.7-3.1,10
                        C826,1767.7,825.3,1767.7,824.6,1767.7z"/>
                    <path id="front_F_24" class="st0" d="M989.2,1894.2c0.5-11.9,0.9-23.7,1.5-35.6c2.1-42.2,5.2-84.4,6-126.7c0.5-29.2-1.4-58.5-3.5-87.7
                        c-2.3-33.2-5.9-66.3-9.1-99.4c-2.6-26.2-5.3-52.3-8.3-78.4c-4.7-40.5-4.1-81.1-3.3-121.7c0.3-12.3,0.9-24.6,1.4-36.9
                        c0.1-2.1,0.5-4.2,2.4-6.5c0.7,1.6,1.6,3.2,2,4.8c16.1,58,32.4,116,44,175.1c4.4,22.5,6.6,45.4,9.5,68.2c2.4,18.5,4.3,37,6.6,55.5
                        c2.7,21.6,2,43.2,0.5,64.8c-4.1,57.4-17.3,113-31.7,168.4c-3.6,13.8-7.3,27.6-11.1,41.4c-1.4,5.1-3.1,10.1-4.7,15.1
                        C990.7,1894.4,990,1894.3,989.2,1894.2z"/>
                    <path class="st0" d="M983,1862.4c0.4-2.6,0.9-5.2,1.1-7.8c0.9-17.3,1.6-34.6,3.7-51.9c0,4.9,0.2,9.8,0,14.7
                        c-0.6,12.3-1.2,24.6-2.1,36.9C985.4,1857,983.9,1859.7,983,1862.4C983,1862.4,983,1862.4,983,1862.4z"/>
                    <path class="st0" d="M983,1862.4c0,10.5,0,21.1,0,31.6C983,1883.5,983,1873,983,1862.4C983,1862.4,983,1862.4,983,1862.4z"/>
                    <path id="front_F_20" class="st0" d="M535.3,2049.6c-0.4-3.2-1.1-6.4-1.2-9.6c-1.4-50.6-2.6-101.3-4.3-151.9c-0.9-25.6-3.1-51.2-4.4-76.8
                        c-2.9-56.3-3.5-112.5,2.1-168.7c4.1-41.1,6.9-82.3,10.6-123.5c1.6-18.2,4.9-36.4,5.8-54.6c2-39.6,2.8-79.2,4.2-118.8
                        c0.1-4.2,0.6-8.4,0.9-12.6c0.8-0.1,1.6-0.3,2.3-0.4c1,3.2,2.1,6.3,2.9,9.5c22.6,91.4,45.4,182.8,67.8,274.2
                        c4.9,20,9.1,40.3,12.1,60.6c4.6,31.5,3.1,63.2,1.3,94.8c-2.4,44.3-8.7,88.1-18.8,131.3c-6.7,28.5-17.9,55-33.8,79.6
                        c-13.2,20.4-26.8,40.6-40.3,60.8c-1.4,2.2-3.2,4.1-4.8,6.1C536.8,2049.6,536.1,2049.6,535.3,2049.6z"/>
                    <path id="front_F_21" class="st0" d="M571.6,1342.5c1.4,1.7,2.9,3.4,4.1,5.2c31.6,50.4,75.9,87.2,127.3,115.6c13.5,7.4,18.6,17.3,18.5,32.2
                        c-0.5,78.4-13.3,155.1-30.4,231.3c-2.4,10.7-4.6,21.5-7,32.2c-0.7,3.2-1.6,6.4-4.4,9.9c-1.2-3.8-2.6-7.6-3.5-11.5
                        c-6.5-27.9-12.5-55.8-19.3-83.6c-19.6-80.5-42.4-160.1-66.3-239.5c-8.2-27-13.7-54.9-20.2-82.4c-0.7-2.8-0.5-5.9-0.7-8.8
                        C570.3,1342.9,571,1342.7,571.6,1342.5z"/>
                    <path id="front_F_19" class="st0" d="M531.8,1302.1c0.7,15.5,2.2,31.1,2.1,46.6c-0.2,35-0.2,70-2.1,104.8c-1.6,29.2-5.6,58.3-8.6,87.5
                        c-3,29.5-5.9,59-9,88.4c-4.8,44.5-6.2,89-4,133.7c1.4,27.9,3.2,55.9,4.8,83.8c0.9,16.9,1.7,33.9,1.4,51.3c-0.8-1.4-2-2.8-2.4-4.3
                        c-14.6-53.6-29.7-107.1-39.3-161.9c-7.9-44.8-11.7-89.8-6.4-135.2c2.7-23.5,6.5-46.8,8.8-70.4c6.4-65,25.6-127.1,42-189.8
                        c2.3-8.7,4.6-17.4,7.1-26c0.9-3.1,2-6.1,3-9.1C529.9,1301.7,530.8,1301.9,531.8,1302.1z"/>
                    <path class="st0" d="M522.8,1892.3c0-3.3,0-6.5,0-9.6c0.2,0,0.4,0,0.6,0c0,2.7,0,5.5,0,8.2c0,0.6-0.3,1.2-0.4,1.8
                        C522.9,1892.5,522.8,1892.3,522.8,1892.3z"/>
                    <path id="front_F_29" class="st0" d="M564.7,2046.2c-1,8.1-1.6,16.4-3.2,24.4c-7.9,41.1-9.9,82.7-7.9,124.4c2.2,46.6,5.3,93.2,7.3,139.8
                        c1.6,35.3,2.3,70.6,3.1,105.9c1.1,49.6,7.3,98.3,24.2,145.2c3.8,10.6,8.2,21.1,12.3,31.6c1.2,3,2.2,6.1,3.7,10.2
                        c-1.8-0.9-2.7-1.1-3.2-1.7c-35.2-39.4-64.4-82.7-78.4-134.2c-7.5-27.6-13.1-55.7-18.3-83.8c-4.4-24.2-7.4-48.7-10.3-73.2
                        c-3.8-31.6-2.3-63.3,0.3-94.8c3.5-42,11.7-83,28-122.2c9.1-21.9,20-42.9,33.1-62.6c2.2-3.3,4.6-6.4,6.8-9.6
                        C563,2045.7,563.8,2046,564.7,2046.2z"/>
                    <path id="front_F_30" class="st0" d="M630.1,2159.2c0.3,1.3,0.9,2.5,0.9,3.8c0.3,26.1,5.2,51.6,11.2,76.9c11.5,48.6,8.3,96.4-7.1,143.6
                        c-8.3,25.3-16.6,50.5-25.2,75.7c-12.6,36.7-15.6,74-7.2,112.1c3.1,14.3,5.8,28.7,8.6,43c0.6,3.2,0.9,6.4,1.5,11.8
                        c-6.3-6.7-8.8-13.1-10.5-19.5c-9-32.7-19.3-65.1-22.8-99c-1.3-12.6-2.2-25.3-1.7-37.9c1.6-35.6,2.9-71.2,6.7-106.6
                        c2.7-25.4,8.6-50.5,14-75.5c8.3-38.7,17.3-77.2,26-115.8c1-4.5,2.5-8.8,3.8-13.2C628.9,2158.7,629.5,2158.9,630.1,2159.2z"/>
                    <path id="front_F_27" class="st0" d="M612.4,1972.6c2.3,10.5,4.7,20.1,6.5,29.9c6.4,35.9,10.3,72,5,108.3c-2.9,20.4-7.8,40.5-12.3,60.6
                        c-9.7,43.2-19.8,86.3-29.9,129.4c-1.2,5.3-3,10.5-4.6,15.8c-0.7,0-1.5-0.1-2.2-0.1c-0.4-1.7-1-3.3-1.1-5
                        c-4.5-57.2-6.9-114.4-6.7-171.7c0.1-26.5,4-52.4,11.3-77.8c6.4-22.4,13-44.7,19.7-67C600.7,1986.7,603.4,1978.4,612.4,1972.6z"/>
                    <path class="st0" d="M612.4,2200.7c-1.7,8-3.4,16-5.2,24.1c-7.6,34.1-15.7,68.1-22.7,102.3c-8.2,39.9-10.7,80.5-11.3,121.2
                        c-0.1,8.9,0,17.8,0,26.7c-0.7,0.1-1.4,0.2-2.1,0.3c-0.4-3-1-6-1.1-9c-0.7-26.3-1-52.6-2-78.9c-1.3-35.3-3.3-70.6-4.9-105.8
                        c-1.2-26.3-2.4-52.6-3.4-78.9c-0.7-17.3-1.3-34.6-1.2-51.9c0-10.9,1.3-21.8,3.1-32.8c0.1,2.4,0.5,4.8,0.4,7.2
                        c-2.1,45,0.6,89.9,3.4,134.8c1.4,21.6,3,43.2,4.6,64.8c0.1,1,0.2,2,0.2,3c0.2,3.6,1,6.9,5.3,7.1c4,0.2,4.5-3.2,5.2-6.2
                        c3.8-16.2,7.6-32.4,11.3-48.6c6.2-26.5,12.3-53,18.5-79.5C611.1,2200.5,611.7,2200.6,612.4,2200.7z"/>
                    <path class="st0" d="M613.8,2187.4c0.9-4.4,1.9-8.8,2.8-13.2c0.5,0.1,1.1,0.2,1.6,0.3c-1,4.4-1.9,8.8-2.9,13.2
                        C614.8,2187.6,614.3,2187.5,613.8,2187.4z"/>
                    <path id="front_F_" class="st0" d="M562.9,2096.3c0.4,4.1,0.7,8.2,1.1,12.3c-0.6,0-1.2,0-1.7,0C562.5,2104.5,562.7,2100.4,562.9,2096.3
                        C562.9,2096.3,562.9,2096.3,562.9,2096.3z"/>
                    <path id="front_F_32" class="st0" d="M903.6,2624.3c1.1-3,2.1-6.1,3.4-9.1c17.4-39,27.6-80,31-122.4c3-37.8,4.1-75.8,5.9-113.7
                        c1.9-39.9,3.7-79.9,5.6-119.8c1.3-28,3.2-55.9,4.1-83.9c1.2-35.1-3-69.8-8.6-104.4c-1.3-8-2.2-16.1-3.3-24.1
                        c0.7-0.3,1.5-0.6,2.2-0.9c2.3,3,4.9,5.9,7,9.1c28.3,42.8,46.3,89.7,55.3,140.2c9.2,51.9,9.6,104,4.4,156.3
                        c-5.1,50.9-15.1,100.7-30,149.5c-10.1,33.2-26.1,63.3-47.3,90.7c-8.9,11.5-18.3,22.7-27.5,34C905,2625.3,904.3,2624.8,903.6,2624.3z
                        "/>
                    <path id="front_F_31" class="st0" d="M893.3,2625c1-13.8,3.7-27.4,7.3-40.7c4.8-17.4,7.4-35.1,7.5-53.2c0.2-23.6-3.9-46.4-11.2-68.7
                        c-8.6-26.3-17.4-52.4-26-78.7c-11.1-33.9-16.8-68.6-12.9-104.2c2-17.8,5.2-35.5,8.1-53.3c3.5-21.3,7.1-42.6,11.1-66.4
                        c1.5,3.8,2.5,5.8,3,7.9c9.9,44.1,20,88.2,29.6,132.4c4.8,22.4,9.4,44.9,12.7,67.6c2.3,16.1,2.3,32.5,3,48.8
                        c0.9,18.6,1.2,37.3,2.1,55.9c1.8,35.1-4.8,68.9-14.6,102.3c-4.5,15.3-10.1,30.2-15.2,45.3c-0.6,1.8-1.8,3.5-2.7,5.2
                        C894.6,2625.2,893.9,2625.1,893.3,2625z"/>
                    <path id="front_F_7" class="st0" d="M762.5,758.3c1.3-16.9,1.6-34,4.1-50.7c2.9-19.9,14.6-34.1,33.8-41.1c24.7-9,49.6-17.5,74.3-26.2
                        c8.7-3.1,17.1-1.9,25.1,2.3c11.9,6.3,21,15.2,26,27.8c12.4,31,28,60.3,43.7,89.7c5,9.4,8.5,19.5,12.7,29.2c2.4,5.6,1.3,10.4-3,14.5
                        c-28.6,27.2-59.8,49.9-99.1,59.1c-17.1,4-34.3,4.4-51.5,0.8c-35.7-7.4-63.2-39.8-64.9-76.5c-0.4-9.6-0.1-19.3-0.1-28.9
                        C763.3,758.3,762.9,758.3,762.5,758.3z"/>
                    <path id="front_F_12" class="st0" d="M957.2,1100.1c-1.1-1.5-2.2-2.4-2.6-3.5c-6.2-20.3-19.2-36.3-33.5-51.4c-3.9-4.1-7.9-8.1-11.9-12.1
                        c-15.1-15.4-24.5-33.4-28.2-55c-4.6-26.5-10.7-52.8-16.1-79.2c-0.4-2-0.8-3.9-1-5.9c-0.8-8.4,0.3-10.7,8.2-13.7
                        c29-10.8,58-21.5,87.1-32c7.2-2.6,9.7-1,10.5,6.9c4.7,48.7,9,97.5,3,146.5c-3.3,26.4-5.8,52.9-8.7,79.4
                        C963.2,1087.1,961.9,1093.9,957.2,1100.1z"/>
                    <path class="st0" d="M918.1,856.1c-2.4,1.3-4.7,2.7-7.1,4c-0.2-0.6-0.5-1.2-0.7-1.8c2.7-0.7,5.3-1.3,8-2
                        C918.3,856.3,918.1,856.1,918.1,856.1z"/>
                    <path id="front_F_" class="st0" d="M747.5,512.4c-12.2-0.3-24-2.5-34.9-7.9c-14.9-7.4-29.6-15.2-44.4-22.8c-6.4-3.3-10-8.6-10.7-15.8
                        c-1.2-12.6-2.4-25.2-3.6-37.8c-0.4-3.6-0.9-7.3-1.1-10.9c-0.3-5.2-3-7.9-8.2-8c-9.6-0.3-14.9-5.7-18.4-14.3c-4-9.9-8.9-19.3-13-29.2
                        c-4.9-11.6-2.6-17.7,8.2-24.1c0.9-0.5,1.7-1.2,2.6-1.5c12-3.3,14.4-11.9,14-23.1c-0.8-21.1,2.5-41.8,9.8-61.7
                        c17.2-47.1,64.6-76.8,113.6-72.3c51.6,4.7,93.9,45.1,100.6,94.4c1.9,13.5,2.2,27.2,2.4,40.8c0.2,8.8,3,15.3,10.9,19.7
                        c4.1,2.2,7.7,5.2,11.4,8.1c5.7,4.5,7.2,10.5,4.7,17c-4.5,11.8-9.4,23.4-14.5,35c-2.7,6.2-8,9.3-14.8,10.6c-9.7,1.8-9.8,2-11,12
                        c-1.3,11.6-2.6,23.2-3.2,34.8c-0.7,14.7-6.8,25-20.4,31.5c-21,10.1-42,20.2-65.3,23.8C757.4,511.5,752.4,511.9,747.5,512.4z"/>
                    <path id="front_F_1" class="st0" d="M671.8,501.9c9.5,4.5,17.6,8.3,25.7,12.1c8.2,3.8,14,10.1,17.8,18.2c13.4,28.1,23.6,57.2,25.6,88.6
                        c0.3,5.5-0.5,11.1-0.9,18.7c-4.7-2.4-7.9-3.6-10.7-5.5c-14.8-9.9-27.7-22-38.2-36.3c-7.9-10.7-14.9-22-21.8-33.4
                        c-2.2-3.5-3.8-8.1-3.7-12.1c0.3-13,1.3-25.9,2.5-38.8C668.4,509.8,670.3,506.5,671.8,501.9z"/>
                    <path id="front_F_2" class="st0" d="M764.3,637.8c-2.3-18.8-1.3-36.7,4.7-53.9c6.3-18.2,13.8-36,21.3-53.8c1.9-4.4,5.4-8.9,9.4-11.3
                        c8.4-5.2,17.5-9.5,26.6-13.5c5.1-2.2,7.6-0.8,8.8,4.7c3.3,15.3,6.8,30.9,2.5,46.4c-2.1,7.5-5.8,15-10.5,21.2
                        C816,592.2,803.9,606,792,620C783,630.6,775.8,635.1,764.3,637.8z"/>
                    <path id="front_F_" class="st0" d="M750.6,615.8c-3.3-15-6.1-30.1-10.2-44.8c-4.1-14.6-9.5-28.8-14.6-44c17.1,0,33.4,0,51.2,0
                        c-0.7,3.4-1,6.5-2.2,9.4c-9.3,22.8-16.3,46.2-19.4,70.8c-0.4,3-1.8,6-2.8,8.9C752,616,751.3,615.9,750.6,615.8z"/>
                    <path id="front_F_" class="st0" d="M964.7,2575.4c4.7,30.5,9.2,59.1,13.3,87.7c1.4,9.5,4.6,17.8,11.3,24.9c9.3,10,18,20.5,27.2,30.6
                        c12.8,14.1,27.5,26.1,43.4,36.6c16.9,11.1,31.1,25,43.1,41.2c1.8,2.4,3.6,4.8,5.1,7.4c6,10,5,13.4-5.6,18.5
                        c-18.9,9.2-38.2,17.1-59.6,18.4c-20.1,1.2-38.3-3.5-54.5-15.4c-4.8-3.5-9.7-7.1-14.3-10.9c-12.1-10.1-25.4-17.9-40.7-22.5
                        c-15-4.4-23.6-15.6-27.4-30.4c-6.1-23.6-8.2-47.8-10-72.1c-0.2-3.3-0.2-6.7-0.5-10c-1.1-12.7,3-23.6,11.3-33
                        c5.5-6.2,11.2-12.3,16.9-18.3c13.9-14.4,26.7-29.7,36.4-47.4C961,2579,962.5,2577.8,964.7,2575.4z"/>
                    <path id="front_F_" class="st0" d="M541.7,2575.3c2.4,3.1,3.7,4.5,4.7,6.2c11.2,19.9,26.1,36.9,42,53.1c4,4,8,8.1,11.5,12.5
                        c7,8.9,11.7,19.1,10.7,30.4c-2.2,25.5-1.3,51.3-8.6,76.2c-1.5,5.1-3.2,10.1-4.6,15.3c-2.8,10.6-9.8,17.6-19.8,20.7
                        c-20.1,6.3-37,17.6-53.2,30.7c-25.1,20.3-53.3,26.3-84.3,15.6c-12.3-4.2-24.4-8.7-36.5-13.5c-9.6-3.8-10.8-7.7-6.4-17
                        c1.6-3.3,3.5-6.5,5.8-9.3c13.2-16.5,27.8-31.3,45.7-42.8c17.8-11.4,33.2-25.6,46.9-41.6c6.9-8.1,13.8-16.3,21.2-23.9
                        c6.6-6.8,9.9-14.6,11.2-23.8c3.7-27.4,7.9-54.7,11.9-82C540.1,2580.5,540.7,2579,541.7,2575.3z"/>
                    <path id="front_F_26" class="st0" d="M849.7,1748.6c0.8,5.7,2.1,11.5,2.3,17.2c2.2,54.5,11.8,107.9,24.8,160.7c4.1,16.6,4.3,32.7-0.3,49.2
                        c-8,28.8-9.5,58.4-10.4,88.1c-0.2,7.2-0.6,14.5-2.8,22.2c-1.1-2-2.5-4-3.4-6.2c-13.7-33.1-21.6-67.8-26-103.3
                        c-5.1-40.8-6.8-81.6-4.1-122.7c2.3-34,7.4-67.5,15.9-100.5c0.4-1.7,1.4-3.3,2-5C848.5,1748.5,849.1,1748.5,849.7,1748.6z"/>
                    <path id="front_F_25" class="st0" d="M640.2,2090.6c-0.3-7.9-0.6-15.7-1-23.6c-1-18.9-1.5-37.9-3.3-56.8c-1.1-11.5-3.8-22.9-6.6-34.2
                        c-4-16.5-3.8-32.9-0.3-49.4c6.1-28.3,12.7-56.4,17.4-85c3.8-23.6,5.1-47.6,7.5-71.5c0.8-7.8,1.6-15.6,2.5-24.3
                        c6.8,8.4,16.8,65.6,19.6,102.6c6.3,83.3-3.7,164.3-32.8,242.6C642.3,2090.9,641.2,2090.8,640.2,2090.6z"/>
                    <path id="front_F_28" class="st0" d="M929.5,2319.2c-0.6-1.7-1.4-3.3-1.8-5c-14-60-27.8-119.9-41.9-179.9c-5.2-22.2-7-44.7-5.8-67.5
                        c1.4-26.6,4.1-53.1,10.1-79.2c1.1-4.7,2.7-9.4,4.1-14c5.7,0.9,7.2,4.7,8.5,8.5c15.5,43.7,30.1,87.6,34.6,134
                        c1.8,18.2,2.5,36.6,1.8,54.9c-1.9,48.3-4.8,96.5-7.3,144.7c-0.1,1-0.4,1.9-0.6,2.9C930.7,2318.9,930.1,2319,929.5,2319.2z"/>
                    <path id="front_F_" class="st0" d="M1008.9,848.2c14.6,56.7,29.3,113.4,44.2,171.2c-12.7-8.9-32.6-48.2-41.7-81c-8.2-29.6-10-59.5-4.9-89.8
                        C1007.3,848.5,1008.1,848.3,1008.9,848.2z"/>
                    <path id="front_F_" class="st0" d="M449.5,1022.9c15.1-60.1,30.1-120.2,45.2-180.2c0.8,0,1.7,0,2.5,0c0.9,5.3,2.5,10.6,2.7,16
                        c2.6,52.2-9.7,101-35,146.6c-2.7,4.9-6.2,9.5-9.5,14.1c-1.1,1.6-2.6,2.9-3.9,4.4C450.9,1023.4,450.2,1023.2,449.5,1022.9z"/>
                    <path id="front_F_10_3" class="st0" d="M744,1155.9c0,8.7,0,17.3,0,26c0,3.3,0,6.7-0.4,10c-1,9.3-4.1,11.7-13.1,10.4c-1.3-0.2-2.6-0.4-3.9-0.7
                        c-19.6-5.1-39.3-10.3-58.9-15.3c-7.6-1.9-13-6.4-16.9-13c-10.7-18.4-16.8-38.3-19.5-59.3c-1-7.9,2.2-13.5,8.9-17.5
                        c17.4-10.4,35.9-17.6,56.3-18.5c10.6-0.5,21.3-0.7,32-0.5c10.5,0.2,11.9,1.8,13.5,11.9c2.9,18.1,5.3,36.2,3.1,54.6
                        c-0.5,4-0.4,8-0.6,12C744.3,1155.9,744.1,1155.9,744,1155.9z"/>
                    <path id="front_F_11_2" class="st0" d="M872.9,1080.8c-4.1-1.8-6.9-2.7-9.4-4.1c-20.4-11.9-42.7-16.2-66.1-16.3c-9.3,0-18.6-0.1-28-0.4
                        c-8.6-0.3-10-1.4-10.1-10c-0.2-19.3-0.3-38.6,0.1-57.9c0.2-11.4,2.9-13.9,14.4-14.5c31.5-1.9,58.9,8,82.6,28.7
                        c7,6.2,10.8,13.9,11.7,23.4c1.1,11.9,3.2,23.7,4.6,35.6C873.3,1069.9,872.9,1074.5,872.9,1080.8z"/>
                    <path id="front_F_11_1" class="st0" d="M865.4,987.4c-4.7-2.1-7.7-3.2-10.5-4.7c-23.5-12.6-48.7-19.4-74.9-22.7c-20.7-2.6-20.8-2.5-20.8-22.9
                        c0-1,0-2,0-3c0-33.4,0-33.4,19.3-60.5c4.8-6.8,6.7-7.7,14.1-4.6c13.2,5.4,26.3,11.1,39.4,16.8c8.2,3.6,13.3,10,15.3,18.6
                        c5.8,23.9,11.4,47.8,16.9,71.8C865.1,979.1,865,982.3,865.4,987.4z"/>
                    <path id="front_F_10_2" class="st0" d="M630.1,1081.1c0-7.7-0.5-13.6,0.1-19.4c1-9.9,2.4-19.8,4-29.7c2.1-12.7,8.4-23.1,18.8-30.7
                        c23.3-17.3,49.4-25.7,78.5-23.4c8,0.6,11.1,2.7,11.9,9.5c2.5,21.8,4.6,43.7,0.7,65.6c-0.3,1.9-1.1,3.7-1.8,6.3
                        c-4.1,0.3-8,0.6-11.9,0.7c-15.6,0.7-31.3,1.1-46.9,2.2c-14.8,1.1-28.5,5.9-41.4,13.4C638.9,1077.5,635.2,1078.8,630.1,1081.1z"/>
                    <path id="front_F_10_1" class="st0" d="M637.6,988.1c0.6-5.5,0.7-9.2,1.4-12.6c4.4-19.8,9.1-39.5,13.3-59.3c3.9-18,13.8-30.5,31.4-36.9
                        c9.1-3.3,17.9-7.1,26.9-10.4c7.5-2.7,9.8-1.9,14.3,4.5c2.9,4.1,5.2,8.5,8.3,12.4c9.5,12.1,13.5,25.7,12.4,40.9
                        c-0.4,6.3-0.1,12.6-0.5,18.9c-0.5,8.9-2.3,10.8-11.1,12.5c-8.8,1.7-17.7,2.9-26.6,4.4c-20.5,3.4-39.9,10-58.3,19.7
                        C646.1,983.9,642.9,985.4,637.6,988.1z"/>
                    <path class="st0" d="M750.2,814.9c2.9,2.5,5.2,3.8,6.7,5.8c4.7,6.4,9,13.1,13.5,19.7c3.4,4.9,3.5,9.7,0,14.7
                        c-5.7,8.1-11.1,16.4-16.8,24.8c-8.1-4-11.1-11.3-15.3-17.5c-10.2-14.8-10.1-15-0.5-29.9c2.9-4.4,6.1-8.6,9.2-13
                        C748,818.2,748.9,816.9,750.2,814.9z"/>
                    <path class="st0" d="M752.2,695.7c-6.7-12-12.4-22.2-18.6-33.2c12.1,0,23,0,35.7,0C763.5,673.7,758.3,683.8,752.2,695.7z"/>
                </svg>
            </div>
        </section>

        <section class="back">
            <h3>측면 결과</h3>
            <div class="pic">
                <img src="img/NOT_MEASUREMENT_IMAGE.png" alt="측면체형이미지" title="문제근육측면체형이미지">
            </div>
            <div class="table">
                <table border="1">
                    <thead>
                        <tr>
                            <th>부 위</th>
                            <th colspan="4">상 태</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>전 체</th>
                            <td colspan="3">이상 없음</td>
                            <td><span class="rect" id="side-errorMuscle_dot_none"></span></td>
                        </tr>
                        <tr>
                            <th>목</th>
                            <td class="cursor side_Neck" data-svg="Neck_front_blue Neck_front_red" colspan="3">거북목</td>
                            <td><span class="rect side" id="side-errorMuscle_dot_Neck"></span></td>
                        </tr>
                        <tr>
                            <th>어깨</th>
                            <td class="cursor side_Shoulder" data-svg="Shoulder_front_blue Shoulder_front_red" colspan="3">굽은등</td>
                            <td><span class="rect side" id="side-errorMuscle_dot_Shoulder"></span></td>
                        </tr>
                        <tr>
                            <th>골반</th>
                            <td class="cursor side_Pelvis" data-svg="Pelvis_front_blue Pelvis_front_red">전방경사</td>
                            <td><span class="rect side" id="side-errorMuscle_dot_f_Pelvis"></span></td>
                            <td class="cursor side_Pelvis" data-svg="Pelvis_back_blue Pelvis_back_red">후방경사</td>
                            <td><span class="rect side" id="side-errorMuscle_dot_b_Pelvis"></span></td>
                        </tr>
                        <tr>
                            <th>다리</th>
                            <td class="cursor side_Leg" data-svg="Leg_back_blue Leg_back_red" colspan="3">반장슬</td>
                            <td><span class="rect side" id="side-errorMuscle_dot_Leg"></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="muscle">
            <div class="R_L_name">
                <div>
                    <span>R</span>
                    <span>L</span>
                </div>
                <div>
                    <span>L</span>
                    <span>R</span>
                </div>
            </div>
            <svg version="1.1" id="body_SVG_side" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px"
                    y="0px" viewBox="0 0 3000 3000" style="enable-background:new 0 0 3000 3000;" xml:space="preserve">
                    <style type="text/css">
                        .st0{fill:#FFFFFF;}
                    </style>
                    <path d="M1967.8,970.6c-2.8,4.6-4.6,7.4-6.2,10.2c-9.4,16.9-18.7,33.8-28.3,50.6c-3.3,5.7-7.2,11.2-11.1,16.5
                        c-4.3,5.8-7.3,12.2-6.5,19.4c2.3,21.5-4.9,40.5-14.2,59.3c-9,18.2-17.2,36.8-24.5,55.7c-20.3,52.6-44.2,103.5-70.4,153.3
                        c-14.3,27.1-28.3,54.3-42.9,81.3c-3.7,6.9-8.7,13.4-13.7,19.5c-3,3.6-4.4,7.2-4.5,11.7c-0.3,10-1.2,20-0.9,30
                        c0.7,22.9-4.7,44.1-16,64c-16.2,28.4-31.9,57-47.5,85.6c-6.6,12.1-15.7,20.3-29.8,22.6c-3.8,0.6-7.9,3.7-10.5,6.8
                        c-11.4,13.5-25.2,18.7-42.7,15.2c-5.4-1.1-11.3-0.5-16.9-0.2c-21.5,1-31.8-6.4-36.3-27.2c-1.9-8.9-4.7-17.2-9.3-25.1
                        c-5.7-9.7-5.1-19.9-0.7-29.8c5.6-12.8,11.8-25.3,17.7-38c1.7-3.6,3.2-7.2,5.4-12.1c-5.2,0.6-9.1,1.2-13,1.6
                        c-20.6,2.4-37.3-5.9-47-23.3c-8.3-14.8-4.3-30.1,11-37c26-11.7,46.4-29.4,63.3-52c11.6-15.5,26.7-27,44.5-34.7
                        c7.3-3.2,11.7-8,14.3-15.5c16.9-48,36.1-95.2,49.7-144.4c3.3-12,9.3-22.7,16.6-32.8c3.4-4.6,5.8-10.6,6.6-16.2
                        c2.4-17.5,3.8-35.1,5.6-52.6c2.4-24.2,9.7-47,21.3-68.2c10.3-19,21.7-37.5,32.6-56.2c0.7-1.1,1.2-2.5,2.2-3.3
                        c10.1-7.7,11.7-18.7,12.4-30.3c1-16.1,5.6-31.3,11.3-46.4c1.9-5.1,3.4-11.3,2.4-16.4c-3.5-16.9-0.6-33.2,3-49.5
                        c4.1-18.6,10.2-36.6,19.4-53.3c9.9-17.8,14.7-36.9,17.6-56.9c1.8-12.2,4.1-24.3,7-36.2c8.5-34.8,28.7-61.8,56.5-83.7
                        c32.5-25.7,69.9-41,109.9-50.3c6.5-1.5,12.9-3.3,19.4-4.6c13.5-2.8,25.9-8.1,37.7-15.2c14.8-9,29.9-17.5,45.1-25.7
                        c9.1-4.9,13.7-12.2,14.5-22.3c0.2-3,0.8-5.9,1.3-8.9c1.6-8.9-0.5-16.7-7.5-22.5c-6.3-5.2-9.6-11.7-10.4-19.7
                        c-1-9.6-2.6-19.1-3.3-28.8c-0.5-7.9-3.2-13.6-9.7-18.6c-5.1-3.9-9.6-9.3-12.6-15c-7-13.2-13.4-26.8-19.2-40.6
                        c-8.5-20.1-4.4-35.4,13.1-48.3c8.8-6.5,11.5-15.2,11.7-25.2c0.3-15.1,2.2-29.9,7-44.3c17.3-52.1,51.4-86.5,106.2-97.6
                        c46.9-9.4,111.3,7.8,142.7,64.1c12.4,22.1,20.2,45.6,21.1,71.1c0.1,2.7,0.5,5.3,0.4,8c-0.4,10.7,3.2,18.3,12.9,24.8
                        c16.4,11,20.3,30.8,12,48.8c-6,13-12.3,25.8-18.3,38.9c-3,6.6-7.1,12-13.5,15.5c-6.5,3.6-9.1,9.1-9.7,16.3
                        c-0.9,10.6-2.5,21.2-3.6,31.7c-0.8,7.3-3.9,13.5-9.4,18.2c-7.9,6.8-8.9,14.9-8.3,25.1c1,16.8,9.1,26.8,23.6,33.7
                        c9.6,4.6,18.5,10.6,27.3,16.5c14,9.4,29.3,15.7,45.4,20.1c16.7,4.6,33.4,9.1,49.9,14.4c36.4,11.8,68.8,30.6,95.4,58.3
                        c25.6,26.7,38.6,59.4,43.4,95.7c1,7.3,1.6,14.6,2.4,21.9c1.1,9.7,3.8,18.7,8.6,27.4c15.7,28.2,26.6,58.1,30.1,90.3
                        c0.7,6.9,0.5,14-0.4,20.8c-1,8.1-0.9,15.7,2.4,23.4c6.4,15.1,10,31,11.2,47.4c0.7,10.2,4.3,18.4,10.7,27
                        c29.3,39.8,53,82.4,58.5,132.8c1.8,16.5,2.9,33.2,4.6,49.7c0.4,3.6,1.1,7.5,2.9,10.5c17.2,28.8,27.2,60.6,38.2,92.1
                        c10.6,30.5,21.9,60.7,32.8,91.1c2,5.6,4.9,9.9,10.7,12.4c22.8,9.5,40,25.9,55.2,44.9c6.2,7.8,12.6,15.7,20.1,22.1
                        c6.7,5.8,15,9.8,22.7,14.3c3.7,2.2,7.9,3.5,11.7,5.7c17,9.6,20.7,25.1,10.1,41.3c-10.9,16.6-26.8,21.9-45.8,20.1
                        c-3.5-0.3-7.1-0.9-12-1.6c2.3,5.3,3.8,9.3,5.6,13.2c5,10.5,10.3,20.9,15.3,31.4c6,12.8,6.4,25.4-0.4,38.3
                        c-3.5,6.6-5.5,14.2-7.2,21.6c-5.3,23.1-22.4,31.3-39.2,28c-5.4-1.1-11.3-0.2-16.8,0.7c-14.3,2.3-26.6-1.2-36.2-12
                        c-6-6.8-12.7-11.5-21.8-12.8c-7.8-1.1-13.2-6.2-17.2-12.6c-8.1-12.6-16.4-25.1-23.8-38.1c-9.9-17.3-19.1-35.1-28.7-52.6
                        c-13.8-25.3-18.3-52.6-16.8-81.1c0.3-4.7,0.4-9.3,0.1-14c-0.4-6.5-2.4-12.2-8-16.5c-3-2.4-5.4-5.9-7.2-9.4
                        c-27.7-53.9-55.8-107.6-82.7-161.9c-11.5-23.2-19.9-47.9-30.2-71.7c-9.8-22.6-19.7-45.2-30.4-67.3c-8.9-18.4-14.1-37.2-12.8-57.8
                        c0.3-4.3-1-9.4-3.2-13.1c-13.4-22.6-27.2-45-40.9-67.4c-0.8-1.4-1.8-2.7-3.5-5.3c-1.2,3.1-2.2,4.9-2.6,6.9
                        c-3.9,19.6-6.9,39.4-11.8,58.7c-4.1,16.4-10.1,32.3-15.7,48.3c-1.9,5.5-2.6,9.5,2.2,14.3c3.6,3.5,6.2,8.8,7.5,13.7
                        c4.9,19.4,9.5,38.8,13.3,58.4c1.2,6.3,0.2,13.2-0.9,19.7c-1,5.4-1.3,10.3,0.8,15.6c11.3,28.7,18.1,58.6,22.1,89
                        c2,15,0.8,30.6-0.6,45.8c-0.9,10.1-0.4,18.3,8.4,24.7c6.9,5,9.2,12.9,10.4,20.9c8.1,54.1,12.3,108.6,11.6,163.3
                        c-0.7,58-4.1,115.9-12.8,173.3c-8.6,56.9-17.6,113.8-27.3,170.6c-4.1,24.3-3.5,48.4-2.1,72.7c1.5,25.3,2.8,50.6-1,75.8
                        c-0.5,3.6-0.8,7.5-2.3,10.7c-4.6,9.6-2.8,18.7,0.9,28c8,19.7,16.2,39.4,23.8,59.3c11.2,29.4,17.9,60,20.9,91.2
                        c1,10.2,0.4,20.7-0.4,30.9c-1.3,15.9-3.3,31.8-5.1,47.6c-1.8,16-6.6,31.2-11.5,46.4c-19.3,58.9-33.9,119-47.4,179.5
                        c-4,18-3.9,36.3-1.5,54.7c1.8,13.9,3.2,27.8,4.9,41.7c0.4,3.5,1.3,7,2,10.9c10.9,0,21.2-0.2,31.4,0.1c8,0.2,16,0.7,23.9,1.9
                        c30.6,4.3,58.5,31.9,62,62.4c3.1,26.8-8.6,47.1-30.4,62c-11.7,8-25.1,12.3-38.8,15.4c-16.8,3.8-30,12.1-39.2,26.9
                        c-6.4,10.3-16.1,16.8-27.4,20.7c-25,8.6-50.6,11.1-76.8,6.5c-16.3-2.9-27.1-11.8-32.2-27.7c-6.4-19.8-10.5-40.1-9.2-60.9
                        c1.6-25.4,0.3-50.6-3.7-75.7c-4-25.4-1.6-50.5,3.2-75.5c2.9-15,6.7-29.9,10.8-44.6c5.2-18.7,5.7-37.3,1.7-56.3
                        c-7.4-35.2-14.1-70.5-21.8-105.6c-2-9.3-5.4-18.6-9.7-27.1c-15.9-30.8-21.7-63.2-18.9-97.7c2.1-25.8,8.1-50.6,15.6-75.1
                        c3.5-11.4,6.9-22.9,10.4-34.4c2-6.5,1.5-12.3-3.5-17.6c-11.2-11.8-18.1-26.2-23.8-41.2c-14.8-39-24.8-79.4-33.8-120
                        c-6.2-27.9-13.3-55.7-18.1-83.9c-4-23.6-6.4-47.6-7.5-71.5c-2.3-48.6-3.4-97.2-4.9-145.9c0-1-0.1-2-1.3-3.1
                        c-0.7,6.2-1.7,12.4-1.9,18.6c-1.5,41.6-3.3,83.2-4,124.9c-0.7,41.2-5.9,81.6-16.2,121.5c-10.2,39.6-19.5,79.5-30.4,119
                        c-5.5,19.8-13.3,39-20.8,58.2c-2.2,5.7-6,11.3-10.3,15.7c-9.9,10.1-11,21-6.9,34c6.8,21.9,13.8,43.9,19,66.2
                        c7.3,31.1,8,62.7,1.1,94.1c-1.7,7.7-4.2,15.6-8,22.5c-11.8,20.9-18.1,43.5-22.5,66.9c-5.8,31.1-11.9,62.1-17.8,93.2
                        c-2.9,15.1-1.1,30,3.9,44.3c9.7,27.9,15.1,56.7,15.4,86.2c0.2,16.9-2.3,33.8-3.8,50.8c-1.6,18.6-3.7,37-1.6,55.8
                        c2.8,25.4-2.5,49.7-13,72.9c-3.5,7.8-9.1,12.8-17.1,15.7c-17.7,6.5-36,6.7-54.3,4.6c-10.8-1.3-21.5-4.2-32.1-7.3
                        c-12.9-3.8-23.2-11.5-30.5-22.9c-7.5-11.8-18.1-19.3-31.4-23.2c-7-2.1-14.1-3.8-21.2-5.9c-13.7-4-25.7-11-35.9-21
                        c-21.1-20.6-25.3-51.8-10-77c15.1-24.8,37.3-38.5,66.4-39.9c9.3-0.5,18.7,0.1,28,0.2c4.3,0,8.6,0,13.7,0c1.6-11.1,3.2-21.5,4.5-32
                        c1.7-13.2,3.8-26.4,4.7-39.7c1.1-16.8-2.3-33.2-6.1-49.4c-14.6-61.5-30-122.8-49.9-182.9c-3.3-10-4.8-20.7-6-31.3
                        c-2.3-20.2-4.4-40.4-5.3-60.6c-1.2-26.5,3.7-52.4,11.5-77.7c9.9-32.1,19.5-64.4,35-94.4c2.5-4.8,2.7-9.6,1.1-14.5
                        c-7.4-21.9-8.3-44.4-6.9-67.2c0.9-15,1.9-29.9,2.8-44.9c1.2-19.1-1.4-37.9-4.4-56.7c-8.3-52.9-16.8-105.9-25.4-158.8
                        c-12.5-77.3-14.3-155.2-13.2-233.2c0.5-36,4.1-71.8,9.7-107.4c1.9-12.1,5.3-23.4,16-31.1c4-2.9,4.7-7,4.3-11.5
                        c-0.9-9.6-1.6-19.3-2.8-28.8c-1.2-10.1-0.4-19.8,2-29.7c3.9-16.5,6.9-33.2,11.1-49.7c3-11.9,7-23.6,11-35.2
                        c1.9-5.5,2.6-10.5,0.7-16.4c-1.7-5.5-2.3-11.9-1.2-17.5c3.7-19.6,8.3-39.1,12.6-58.5c1.5-6.7,4.2-12.7,9.7-17
                        c3.6-2.9,4.6-6.2,2.6-10.5c-17-37.9-23-78.4-28.6-119C1969.5,975.3,1969,974.1,1967.8,970.6z M2472.6,958.7c0.7-0.2,1.4-0.4,2.1-0.6
                        c1.7,2.7,3.5,5.4,5.1,8.2c9.7,17.5,19,35.2,29.1,52.4c6.4,10.9,13.8,21.2,21.3,31.3c2.8,3.8,4.3,7.4,3.6,11.9
                        c-3.3,22.5,3.4,42.5,13.4,62.3c9.8,19.6,18.1,40,26.7,60.2c9.7,22.6,17.9,46,28.8,68c27.1,54.9,55.4,109.3,83.4,163.8
                        c2.8,5.5,7.7,9.8,11.3,15c2.4,3.4,5.6,7,6.1,10.8c1.1,10.2,1.6,20.7,1,30.9c-1,19,2.3,37.2,9.6,54.6c15.1,35.7,35.5,68.6,55.7,101.5
                        c4.1,6.7,10.1,11.9,18.2,13c9,1.1,15.5,5.3,20.7,12.4c7.8,10.5,18.1,13.5,30.9,11.5c7.4-1.1,15.2-1,22.6-0.1
                        c12,1.4,21.2-3,25.5-14.3c2-5.2,2.5-11,3.9-16.5c0.9-3.5,1.4-7.6,3.6-10.2c9.8-11.4,9.8-23.7,3.9-36.4
                        c-7.1-15.4-14.9-30.5-22.3-45.8c-1.8-3.7-3.4-7.5-5.7-12.6c4.1,0.5,6.4,0.7,8.6,1c7.6,1.2,15.1,3.1,22.7,3.5
                        c16.2,1,28.3-6.2,36-20.5c5.4-10,3.4-18.9-5.9-25.4c-4.9-3.4-10.4-5.9-15.9-8.3c-15.6-6.9-28.3-17.2-38.7-30.7
                        c-6.3-8.2-13-16-20-23.6c-9.8-10.6-21-19.6-34.7-24.8c-9.5-3.6-15.2-10.3-18.6-19.6c-11.5-32.3-23.3-64.5-34.5-96.9
                        c-9.8-28.4-19.6-56.6-34.9-82.7c-1.8-3.1-2.9-6.8-3.3-10.3c-1.5-11.9-2.9-23.8-3.9-35.7c-1.6-21.3-4.4-42.4-11.7-62.6
                        c-11-30.3-27.8-57.5-45.5-84.1c-1.1-1.6-2.6-3.1-4.1-4.3c-6.1-4.6-8.4-10.8-8.7-18.2c-0.7-20.9-5.1-40.9-13.9-59.9
                        c-2.5-5.4-2.3-10.6-1-16.5c1.5-7,2.3-14.5,1.6-21.7c-2.2-22.7-9.1-44.3-18.1-65.2c-4.1-9.5-8.1-19.1-13.6-27.7
                        c-3.8-5.9-5.9-11.8-6.7-18.4c-1.5-11.9-2.6-23.8-4.3-35.7c-5.6-37.9-21.4-70.6-50.5-96.1c-23.3-20.4-49.4-36.2-79.1-45.5
                        c-20.9-6.6-42.2-12.3-63.2-18.6c-5.7-1.7-11.7-3.3-16.8-6.2c-19.7-11.2-39.2-22.8-58.6-34.4c-7.5-4.5-12.3-11.3-13.7-19.9
                        c-1.4-8.9-2.2-17.8-3.1-26.8c-0.5-5.1,2-8.9,6-11.9c8.5-6.4,12.7-15.2,13.8-25.6c1.1-10.6,2.4-21.2,3.5-31.8
                        c0.5-4.8,1.4-9.6,6.6-11.4c9.1-3.1,13.5-10.4,17.4-18.3c6.3-12.9,12.2-25.9,18.5-38.8c4-8.1,3.6-15.9-0.2-24
                        c-3.7-7.8-8.6-14.3-16.6-17.9c-7.4-3.3-9.7-8.7-10-16.6c-0.6-14.3-1.3-28.7-4-42.7c-11.2-57.5-57.2-99.3-114.7-105.1
                        c-57.4-5.8-109.9,25.6-132.7,79.6c-8.9,21.1-13.1,43.3-12.7,66.2c0.1,8.9-3.3,14.9-11.1,19.6c-16.2,9.7-21.2,24.7-13.8,42
                        c6.1,14.4,13.1,28.4,19.8,42.5c2.6,5.5,6.4,10.1,12.6,12.2c6.3,2.1,8.3,6.9,8.8,13.1c0.8,10,2.2,19.9,3.2,29.8
                        c1.1,11.1,5.1,20.6,14.4,27.4c4.1,3,6.1,7,5.6,12.1c-0.9,8.3-1.7,16.6-2.8,24.8c-1.4,9.9-6.7,17.2-15.3,22.2
                        c-8.9,5.3-17.6,10.7-26.5,16c-26,15.5-53.6,26.3-83.4,32.3c-33,6.7-63.4,19.8-91.3,38.8c-42.5,28.9-67.5,68.1-72,119.8
                        c-2.1,24.1-7.8,46.9-20.6,68.1c-12,19.8-17.5,42.4-20.9,65.3c-1.4,9.5-2.9,19.3,0.6,28.7c2.2,5.8,1.5,10.9-1.1,16.3
                        c-8.4,18-12.5,37.2-13.7,57c-0.6,9-2.7,17-10.5,22.7c-1.8,1.3-3.1,3.5-4.3,5.5c-11,18.7-22.2,37.2-32.7,56.1
                        c-9.9,17.9-16.7,37-19.1,57.5c-2.2,18.2-4,36.4-5.6,54.7c-0.9,10.2-3.2,19.5-10,27.6c-7.2,8.5-10.9,18.8-14.5,29.3
                        c-17.1,50.1-34.6,100-52,150c-1.8,5.3-5.1,9.2-10.6,11.2c-21.7,8-38.2,22.4-51.9,40.7c-15.5,20.6-34.8,36.7-58.7,46.9
                        c-1.2,0.5-2.4,1.1-3.6,1.8c-9.1,5.2-12.5,13.4-8.5,22.8c5.6,13.1,15.1,22.1,29.8,23.4c6.2,0.6,12.6-0.4,18.8-1.1
                        c6.2-0.7,12.3-1.8,20.3-3.1c-2.5,5.5-4.2,9.4-6.1,13.2c-4.7,9.2-10.1,18.1-14.3,27.5c-4.4,9.7-8,19.7-11.4,29.7
                        c-2,5.9-1.1,12.2,2.7,17.2c6.1,8,8.5,17.1,9.7,26.8c1.7,13.8,10.6,21.3,24.3,21.4c8.7,0.1,17.4-0.4,26,0.5
                        c15.2,1.6,26.5-3.6,35-16.3c2-3,6.2-6.3,9.4-6.3c14.4-0.2,22.4-8.5,28.8-19.9c10.6-18.5,21.2-37,32.2-55.2
                        c11.1-18.3,20.1-37.6,26.3-58.1c2.7-8.8,3.4-18.3,3.9-27.5c0.7-13.3,0.4-26.7,0.3-40c0-4.3,0.8-8,4.7-10.3
                        c6.7-3.9,10.4-10.1,13.8-16.7c10.8-20.6,21.8-41.2,32.6-61.9c24.8-47.5,50.2-94.7,69.6-144.9c13.1-33.8,25.6-67.9,42.9-99.9
                        c7.3-13.5,9.3-28.5,7-43.7c-1.1-7.2,0.9-12.8,5.1-18.5c6.9-9.4,13.9-18.8,19.4-29c12-21.9,23.2-44.2,34.7-66.4
                        c1.4-2.7,1.9-6.3,6.7-7.2c2.6,55.7,12.4,109.4,35.2,160.6c-9,5.1-15.4,11.5-17.6,21.3c-4,18.2-8.2,36.4-12,54.6
                        c-1.3,6.2-1.7,12.6,2,18.4c2.7,4.3,2.3,8.9,0.4,13.4c-9.4,21.9-14.3,45-19.5,68c-4.7,20.8-9.5,41.5-5.2,63.1
                        c1.3,6.5,1.3,13.2,2,19.9c0.4,4.3-0.4,7.9-4.4,10.3c-10,6-14.5,15.3-15.7,26.5c-3.3,30.1-7.8,60.2-9.3,90.4
                        c-3.9,79.4,0.1,158.5,10,237.4c7,55.2,16.1,110,26.5,164.7c5.6,29.3,7.3,58.8,3.3,88.5c-3.6,27.4-3.4,54.4,4.8,81.2
                        c3.2,10.5,1.8,21.1-3.2,31.1c-18.3,36.2-28.6,75.2-38.8,114.2c-1.7,6.4-2.5,13.1-3.1,19.7c-2.4,25.7,0,51.3,3.2,76.7
                        c1.9,14.8,3.3,30,7.9,44.1c18.9,58,33.9,117,48.1,176.2c6.3,26.4,9,53.1,5,80.3c-2.1,14.5-2.9,29.1-4.4,43.7c-0.4,3.5-1.2,7-2,11.1
                        c-4.4,0-8,0.1-11.7,0c-11.3-0.3-22.7-1.1-34-0.9c-32.6,0.6-58.2,18.3-68.9,47c-5.5,14.9-4.7,29.5,3.1,43.4
                        c7.7,13.9,18.7,24.2,33.8,29.7c8.4,3.1,17,6.1,25.6,8.3c17.9,4.6,32.3,13.8,42.1,29.7c4.5,7.3,11,12.6,19,15.7
                        c26.4,10.2,53.4,13.8,81.3,7.4c11-2.5,18.2-8.7,22.3-19.3c7.9-20.2,11.9-40.9,10.1-62.5c-1.8-22-0.6-43.9,2.7-65.7
                        c4.2-27.8,4.7-55.8-1.6-83.5c-3.7-16.6-8.1-33-12.2-49.4c-4.1-16.7-4.8-33.5-1.3-50.5c7.9-37.8,15.6-75.6,23.1-113.5
                        c1.8-9.3,5-17.8,9.7-26c9.5-16.7,14.9-34.7,17.3-53.8c3.7-28.9,0.1-57.2-7.2-85.1c-5.4-20.6-11.8-40.9-18-61.3
                        c-3-9.8-1.2-17.7,5.6-25.8c6.9-8.3,13.2-17.7,17.4-27.7c7-16.5,12.9-33.6,17.8-50.8c8.5-30.1,15.8-60.5,23.6-90.8
                        c12.5-48.5,20.9-97.7,22.5-147.8c1.6-49.3,3.6-98.6,4.5-147.9c0.5-27.3-0.4-54.6-1.5-81.9c-1.1-28-3.1-55.9-5-83.8
                        c-0.4-5.6-2-11.1-2.6-16.6c-0.4-3.1-1-7.1,0.5-9.3c4.8-7,10.5-13.5,16.5-20.8c5.8,6.9,10.6,12.8,15.7,18.5c3.3,3.7,3.2,7.2,2.2,11.9
                        c-1.9,9.1-3,18.3-3.9,27.6c-0.9,9.6-1.2,19.3-1.6,28.9c-1.6,34.3-4.6,68.6-4.5,102.8c0.2,51,2.7,101.9,4.3,152.9
                        c1,32,2.4,63.9,8,95.5c10.9,61.4,26.6,121.6,44.4,181.2c6.7,22.6,15.2,44,31.6,61.6c5.1,5.4,6.3,12.1,4,19.6
                        c-3.5,11.1-6.4,22.4-9.9,33.5c-9.1,29-16.8,58.1-17.3,88.9c-0.4,29.3,5,56.8,19.4,82.6c4,7.2,7,15.2,8.8,23.2
                        c7.7,35.8,14.7,71.7,22.2,107.6c3.3,15.8,5.5,31.6,2.1,47.6c-2.3,10.7-5.3,21.3-8.3,31.9c-10.8,39.4-13.3,79.1-6.4,119.7
                        c2.7,15.6,3.2,32,1.8,47.7c-2.1,23.1,0.9,45.1,7.7,66.9c4.9,15.5,15.6,23.3,31.2,25.5c20.4,2.8,40.6,1.5,60.4-4
                        c16.3-4.5,29.7-13.3,38.7-28c5.7-9.3,13.7-15.2,24-18.3c8.3-2.5,16.6-4.8,24.8-7.6c12.1-4,23.7-8.9,33.4-17.5
                        c22.1-19.6,26.4-49.7,9.8-74c-14.6-21.4-34.9-33.5-61.4-33.5c-12.7,0-25.3,0.7-38,0.9c-3.2,0.1-6.4-0.2-8.3-0.3
                        c-2.1-19-3.6-36.5-6.2-53.9c-3.2-21.7-4-43.3,1.3-64.5c11.9-48.1,23.3-96.4,37-144.1c11.1-38.8,23.5-77,25.9-117.7
                        c1-17.3,2.2-34.6-0.3-51.8c-5.8-40.1-17.8-78.5-34-115.5c-3.9-8.8-7.8-17.7-11.6-26.5c-2.8-6.4-3-12.8-0.4-19.4
                        c5.2-13.2,7-27.1,6.6-41.2c-0.6-20.6-1.9-41.3-2.5-61.9c-0.4-12.6-1.8-25.5,0.1-37.9c7.6-50,16-99.9,24.5-149.8
                        c8.6-50.3,14.1-101,16.4-151.9c2.3-48.6,3.7-97.3,0-145.9c-2-25.5-5.2-51-8.4-76.4c-1.4-11.2-5.8-21.1-16.3-27.3
                        c-2.1-1.2-3.9-4.9-3.8-7.5c0.1-10,1.1-19.9,1.7-29.9c0.4-7,2.1-14.1,1.1-20.8c-4.9-34.3-11.3-68.3-24.7-100.5
                        c-2.5-5.9-3.1-10.8-0.1-17c2.1-4.4,2.5-10.5,1.5-15.4c-3.7-18.6-8.4-37-12.3-55.6c-1.6-7.6-4.4-13.9-11.2-18.1
                        c-3.8-2.4-4.5-5.5-3-9.4c1-2.5,2-4.9,3.1-7.4c10.7-25.6,18.2-52.1,22.7-79.5C2467.2,994.7,2469.8,976.7,2472.6,958.7z
                        M2222.2,1628.1c0.4-0.1,0.8-0.1,1.2-0.2c1.5-27.5,3-55.1,4.7-82.6c0.9-14.3,0.8-28.8,3.7-42.6c2.4-11.3-0.1-18.6-9.5-24.9
                        c-5.9,4.8-12.8,9.7-10.2,18.6c3.6,12.1,3,24.3,3.7,36.6c1.5,28.2,3.1,56.5,4.7,84.7C2220.7,1621.2,2221.6,1624.6,2222.2,1628.1z"/>
                    <path d="M761.7,1476.4c-5.6,0-10.1,0-15.6,0c-0.2,3.7-0.6,6.9-0.5,10.1c1.5,38.1-2.6,75.8-7.1,113.6c-7,59-18.5,117-32.6,174.6
                        c-1.7,7.1-5.1,13.7-7,20.7c-1.4,5.3-3.2,11.2-2.4,16.4c5.5,37.1,6.2,74.4,4.8,111.7c-1.8,48.1-9,95.5-23.6,141.5
                        c-4.9,15.5-10.4,30.9-16.6,46c-6.2,14.9-7.9,30.1-6.6,46c2.6,30.2,7.4,60.1,13.7,89.8c9.2,43.3,7.2,86.1-5.6,128.4
                        c-9.3,30.6-19.9,60.7-29.1,91.3c-4.6,15.6-7.9,31.6-10.7,47.7c-3.1,18,0.3,35.9,3.6,53.6c4.1,22.2,8.7,44.4,12.3,66.8
                        c1.1,7-0.4,14.5-1,21.8c-1.8,22.2-3.6,44.5-5.7,66.7c-1.9,21-7.2,41.3-13.9,61.2c-2.8,8.2-7.7,14.7-15.3,19
                        c-6.4,3.5-12.6,7.7-19.4,10.2c-15.8,5.9-29.9,14.5-42.8,25.3c-11.9,9.9-25.2,17.3-40,22c-20.7,6.5-41.5,6.7-62,0.5
                        c-16.8-5.1-33.4-11.4-49.5-18.4c-17.3-7.5-23.2-21.5-17.1-39.4c2.4-7.1,6.1-14.1,10.5-20.2c13.9-19.4,30.8-35.9,50.9-48.9
                        c18.6-12.1,34.6-27.1,48.5-44.3c2.7-3.4,5.1-7.1,8.4-9.9c10.4-8.8,14.2-20.3,15.8-33.4c2.9-23.1,6.3-46.2,9.6-69.3
                        c0.6-4.3,1.5-8.6,3.1-12.6c2.3-5.7,1.9-10.8-0.7-16.3c-18.5-38.8-29.1-80-35.8-122.2c-5.1-31.6-10.3-63.2-13.1-95
                        c-2.2-24.1-1.6-48.6-0.6-72.9c1.5-35,5.7-69.8,15.7-103.5c5.4-18.2,11.9-36,18.8-53.6c4.2-10.7,6.9-21.3,6.7-32.9
                        c-0.6-34.6-0.9-69.3-1.2-104c-0.1-16.2-3-31.7-7.7-47.1c-12.6-41.4-25-82.8-37.3-124.3c-9.2-30.7-13-62.5-18.2-94.1
                        c-4.7-28.8-5.5-57.7-2.8-86.7c2.2-23.5,4.8-47.1,7.8-70.5c3.5-27.1,6.3-54.3,11.9-81c13.3-63.2,30.2-125.6,53.6-185.9
                        c3.5-9,4.6-17.5,2.9-27.1c-3.6-20.9-1.8-41.9,2.1-62.6c3-15.7,6.4-31.3,10.1-46.8c1.9-7.9,1.8-14.5-3.5-21.5
                        c-10.1-13.6-11.8-30.1-13.4-46.4c-2.6-27.2-5.4-54.4-8.1-81.5c-0.2-1.5-0.7-3-1.5-6.1c-2.3,2.9-3.8,4.4-4.8,6.2
                        c-8.5,15.4-17,30.9-25.4,46.4c-3.2,6-4.6,12.1-3,19.2c3.6,16.4,1.1,32.3-4.2,48.1c-8.8,26.5-17.2,53.2-25.6,79.8
                        c-14.2,45.2-31.3,89.2-50.6,132.4c-20.2,45.3-42.1,89.8-63.5,134.6c-4.5,9.4-6.2,18.4-3.8,28.8c5,21.9,0.4,42.5-12,61
                        c-21.4,32.2-43.3,64-64.9,96c-5.9,8.7-13.1,14.7-24.2,15.6c-3.5,0.3-6.8,3.6-9.9,5.8c-1.3,0.9-1.9,2.7-2.9,4.1
                        c-8.5,11.8-19.9,17.2-34.5,15.8c-10.3-1-20.6-1.6-30.8-2.4c-15.6-1.2-26.9-11.4-28.7-26.9c-1.1-8.8-2.9-16.6-8.7-23.8
                        c-6.7-8.4-6.7-18.7-3.5-28.6c1.8-5.7,4.4-11.1,7.3-16.3c6.3-11.4,13-22.5,19.5-33.7c1.6-2.8,3-5.7,5.2-10c-4.3,0-7.1-0.2-9.9,0
                        c-16.6,1.3-29.9-4.8-40.3-17.7c-13-16.2-6.7-39,11.6-45.2c12.5-4.2,22.8-12.2,32.1-21.5c16.5-16.5,33-33,49.5-49.5
                        c2.3-2.3,4.9-4.7,7.8-6.1c7.6-3.8,12.4-9.9,15.8-17.4c18.6-42,37.4-84.1,44.9-129.9c3.4-20.7,6.6-41.4,8.8-62.3
                        c5.7-55.7,23-107.2,54.9-153.5c1.5-2.2,2.8-4.8,4.8-6.4c8.8-7.2,8.4-16,4.6-25.3c-6.5-15.7-8.3-32.1-7.5-49
                        c1.9-41,14-78.5,39.2-111.3c5.4-7,7.8-14.4,8-23.1c0.4-21.3,0.8-42.7,1.8-63.9c2-43.7,21.8-78.9,53.3-108.3
                        c26.1-24.4,57-38.9,92.3-43.9c13-1.8,25.4-5.2,37.3-10.5c28.6-12.8,57-26,85.5-38.7c7.6-3.4,11.2-8.8,11.9-16.9
                        c1-9.9,2.2-19.9,3.6-29.7c0.6-4.5,0.2-8.4-2.7-12.2c-7.1-9-9.4-19.7-10.3-30.8c-0.6-7-1.4-13.9-1.9-20.9c-0.4-5.1-2.4-8.9-7.1-11.1
                        c-7.6-3.6-12.3-9.9-15.8-17.4c-5.7-12.4-11.9-24.6-17.2-37.2c-9-21.1-4.7-37.3,13.7-51c7.6-5.6,10-12.7,10.1-21.5
                        c0.3-30.8,9.1-59,26-84.8c22.6-34.6,54.5-53.4,95.4-58.4c55.4-6.7,109.1,20.3,135.6,69.3c10,18.4,15.3,38.2,16.9,59.1
                        c0.4,5.6,0.9,11.3,1,17c0.1,9,3.7,15.9,11.3,21c6.7,4.6,12,10.6,15,18.3c3.4,8.8,2.8,17.8-0.5,26.4c-4.9,13.1-10.2,26-15.8,38.8
                        c-3.8,8.7-10,15.8-18.6,20.3c-4.3,2.3-6.3,5.5-6.6,10.3c-0.4,6.3-1.4,12.6-1.6,18.9c-0.5,11.6-3.4,22.2-9.9,32
                        c-2.2,3.3-3.2,8.1-2.9,12.1c0.9,11.6,3,23.1,4.1,34.7c0.7,7.4,4.3,12.3,11,15.3c30.4,13.7,60.4,28.1,91.2,40.7
                        c11.8,4.8,25.2,6,37.9,8.2c36.8,6.3,68,23,92.9,50.7c10.2,11.4,19.3,23.8,27.9,36.4c12.7,18.5,16.1,40,17.3,61.8
                        c1.2,20.6,1.7,41.3,2.1,61.9c0.2,10.4,2.9,19.7,9.1,28c24.5,32.8,36.7,69.9,38.5,110.6c0.8,17.8-1.7,35.3-8.7,51.8
                        c-3.1,7.4-2.2,13.1,2.7,19.3c34.9,44,53,94.8,60.9,149.9c4.7,33.3,9.3,66.6,16,99.5c7.3,35.7,21.3,69.4,39.2,101.1
                        c5,8.9,11.8,17,18.9,24.3c18.3,18.9,37.3,37.1,55.9,55.8c6.9,6.9,14.4,12.8,23.9,15.8c4.1,1.3,7.9,3.5,11.6,5.8
                        c13.8,8.8,17.6,24.2,9.3,38.2c-9.4,15.9-23.8,22.6-41.9,22.3c-3,0-5.9-0.1-8.9-0.2c-0.3,0-0.5,0.3-1.5,1c1.5,3.1,2.9,6.4,4.7,9.5
                        c6.1,10.7,12.5,21.2,18.4,32.1c3.2,5.8,5.8,12,8.1,18.2c3.8,10.4,2.3,20.6-4.4,29.3c-4.9,6.4-7,13.2-7.7,21.1
                        c-1.3,15.1-11.5,26-26.7,28c-12.2,1.6-24.5,2.5-36.9,2.8c-11.6,0.3-21.7-4.1-28.6-13.8c-5.5-7.7-11.5-13.2-21.8-13.8
                        c-6.9-0.4-11.7-5.9-15.5-11.6c-11.2-16.6-22.2-33.2-33.4-49.8c-9.5-14.1-18.8-28.3-28.8-42c-15-20.4-21.7-42.6-16.3-67.7
                        c2.4-11,0.3-20.9-4.5-30.9c-22.3-46.5-45.3-92.8-65.9-140.1c-16.6-38.1-30.7-77.3-44.9-116.4c-6.6-18.1-10.6-37.1-16.2-55.6
                        c-2.8-9.2-6.3-18.2-9.9-27.2c-6.4-16.1-10.1-32.5-6.8-49.8c2.1-10.8-0.1-20.2-5.8-29.5c-7-11.3-12.9-23.3-19.4-35
                        c-1.6-2.9-3.3-5.7-6.1-10.5c-1.1,4.5-2,7.1-2.3,9.7c-3.5,31.4-6.9,62.9-10.5,94.3c-1.1,10.1-4.1,19.7-10.4,27.7
                        c-6,7.7-6.4,15.3-3.7,24.3c9.2,30.9,13.9,62.4,12.2,94.7c-0.1,2.7,0.1,5.4-0.5,8c-3.3,13.3-0.2,25.4,5.3,37.7
                        c19.8,44.3,31.8,91,43.5,137.9c12.1,48.6,18.9,98,24.1,147.7c1.9,17.6,3.8,35.1,6,52.6c2.5,19.7,0.2,39.3-1.3,58.8
                        c-4.6,61.6-22.7,120.2-40,179c-7.4,25.2-14.8,50.5-21.9,75.8c-1.8,6.7-3,13.7-3.2,20.6c-0.9,39-1.3,78-2.1,116.9
                        c-0.2,9.9,1.8,19.1,5.9,28c17.8,38.8,27.4,79.8,32.5,122.1c4,33.2,5.8,66.5,3.5,99.8c-1.6,22.9-4.8,45.7-7.9,68.5
                        c-4.2,31.3-9.3,62.6-17.5,93.2c-5.8,21.6-12.8,42.7-22.3,63c-3.9,8.3-4.8,15.8-1.5,24.9c3.2,9,3.8,18.9,5.2,28.4
                        c2.9,19.8,5.2,39.6,8.3,59.3c0.8,5.1,2.8,10.6,5.9,14.5c9.6,11.9,19.4,23.8,30.4,34.5c10.7,10.4,22.3,20,34.6,28.6
                        c20.2,14.1,37.5,30.9,52.1,50.6c7.4,9.9,11.9,20.7,11,33.4c-0.7,9.7-4.8,17.2-13.5,21.9c-25.4,13.8-52.1,23.9-81.3,25.5
                        c-21.6,1.2-41.7-3.9-59.9-15.7c-8.1-5.3-15.8-11.1-23.4-17c-9.8-7.6-20.3-14-32.2-17.7c-23.8-7.3-38.5-23.1-43.4-47.5
                        c-3.2-16-6.1-32-8.4-48.2c-2.3-16.8-3.3-33.8-5.5-50.7c-3.1-23.8-0.8-47.1,4.5-70.4c3.5-15.2,6.6-30.6,9.2-46
                        c4.3-26.2,0.9-51.9-7-76.9c-8.3-26.3-17.5-52.4-26-78.7c-10.8-33.5-17.1-67.8-16.8-103.1c0.2-21.4,5.1-42.3,8.4-63.3
                        c4-25.3,7.5-50.7,10.5-76.1c1.8-15.1-0.9-29.6-6.8-43.9c-23.1-55.9-35.5-114.2-39-174.6c-2.2-38.4,0.4-76.6,3.9-114.7
                        c1-10.7,0.8-20.7-4.4-30.9c-4.4-8.7-5.6-19.1-7.6-28.8c-7.4-35.5-15.2-71-21.6-106.7c-4.4-24.6-7.3-49.4-9.9-74.2
                        c-2.4-23.2-3.7-46.4-5.1-69.7c-0.4-6.9,0.8-14,1.1-20.9C761.9,1485.5,761.7,1481.6,761.7,1476.4z M508.2,951.5
                        c0.8,0.1,1.5,0.2,2.3,0.4c0.5,3.9,1.1,7.8,1.4,11.7c3.5,39.2,6.6,78.3,10.4,117.5c1.3,13.4,5.4,26.2,14.3,36.7
                        c3.9,4.6,4.6,9.3,3.2,14.8c-2.3,9-4.7,18.1-6.8,27.1c-7.1,30.6-12.4,61.5-6.7,93c1.2,6.5,0.4,12.5-2,18.5
                        c-14.2,36-25.5,72.9-35.6,110.1c-10.3,37.6-20.5,75.2-25.6,113.9c-4.1,30.7-7.8,61.5-11,92.3c-2.1,19.9-3.5,39.8-4.1,59.8
                        c-0.8,27.5,4.3,54.4,9.1,81.3c3.9,21.9,7.9,44,13.9,65.4c12.5,44.5,26.6,88.6,39.6,133c2.5,8.5,3.9,17.6,4.2,26.5
                        c0.9,26.3,1.1,52.6,1.6,79c0.2,11,0.2,22,0.7,33c0.6,12.9-1.7,25.1-7.1,36.9c-20.4,44.8-30.1,92.3-33.8,141.1
                        c-2.3,30.3-3.7,60.6-0.1,90.8c3,24.8,6.2,49.6,10,74.2c7.6,48.8,18.2,96.9,40.3,141.6c2.4,4.8,2.5,9.2,0.6,14.3
                        c-2.2,6.2-4,12.7-5,19.2c-3.7,24-7,48.1-10.5,72.2c-1.3,9.1-4.6,17.3-11.3,24c-4.5,4.5-8.7,9.2-12.7,14.1
                        c-14.4,17.7-30.9,33-49.9,45.8c-8,5.4-15.8,11.2-22.9,17.7c-13.4,12.1-25.8,25.3-33.9,41.6c-9,18-4.4,31.1,14,38.9
                        c15.3,6.4,31,12.2,46.9,16.9c19.6,5.8,39.5,5.2,59-1.3c15.1-5,28.2-13.5,40.2-23.7c11.6-9.8,24.4-17.3,38.8-22.1
                        c3.1-1.1,6.3-2.3,9.2-3.8c12.1-6,20.8-14.8,24.9-28.3c6.5-21.4,11.2-43.1,12.5-65.5c1.2-21.3,4.1-42.4,5.9-63.6
                        c0.5-5.6,0.1-11.3-0.9-16.8c-3.3-18.7-6.6-37.4-10.7-55.9c-8.2-37.1-6.5-73.3,6.2-109.2c8.3-23.5,15.9-47.3,23.7-71
                        c16.7-50.4,21-101.4,9.5-153.6c-7.1-32.2-12-64.8-12.9-97.9c-0.2-8.8,1-17.2,4.5-25.3c27.7-64.1,41-131.3,43.3-200.9
                        c1.2-35.7-1.3-71.3-5.7-106.7c-0.8-6.6-0.1-12.3,4-17.9c2.4-3.4,3.6-7.8,4.6-12c16-67.7,30.6-135.8,37.1-205.2
                        c3-31.8,7.2-63.6,2.6-95.7c-0.4-2.5,0-5.1,0-8c11.4,0,21.9,0,33,0c-0.4,6-0.8,11.3-1.1,16.5c-0.6,11-1.7,21.9-1.7,32.9
                        c0.1,30.4,3.6,60.5,7.6,90.6c7.5,57.2,18.6,113.7,32.8,169.5c0.9,3.5,1.7,7.4,3.9,10.1c5.1,6.3,5.6,13.2,4.6,20.7
                        c-4.5,32.4-6,65-5.4,97.7c0.9,44.1,5.7,87.7,16.5,130.5c6.1,23.9,14.2,47.1,23.6,69.9c3.7,9.1,7.1,18.9,7.8,28.6
                        c0.9,11.1-0.7,22.6-2.3,33.8c-3.6,25.4-7.8,50.7-12,75.9c-4.1,24.4-7.2,48.7-5.1,73.6c1.9,22.4,6.5,44.1,13.3,65.4
                        c9.6,30.1,19.9,60,29.4,90.2c9.9,31.5,11.2,63.4,3.2,95.6c-2.5,10-4.8,20.1-7,30.1c-5.2,24-6.4,48.1-1.9,72.4
                        c0.8,4.2,0.9,8.6,1.2,12.9c1.8,29.7,5.8,59,13.9,87.6c3.5,12.5,10.1,22.8,22.7,27.4c23.5,8.6,44.1,21.7,63.7,36.8
                        c11,8.4,23.3,14.2,37,16.9c38.3,7.4,72.3-5.4,105-23.2c8.8-4.8,11.8-14,8.5-23.4c-2.8-8.1-6.5-16.2-11.3-23.1
                        c-12.9-18.5-29-33.9-47.9-46.2c-18.6-12.1-34.7-26.9-48.8-44c-5.3-6.4-10.7-12.8-16.5-18.8c-4.8-5-7.6-10.6-8.6-17.5
                        c-3.7-26.7-7.5-53.4-11.6-80.1c-0.7-4.9-1.8-9.9-4-14.3c-2.9-5.7-2.8-10.7,0.1-16.1c9.8-18.7,16.3-38.6,22.4-58.7
                        c9.4-31,14.1-63,19.6-94.8c5.1-29.9,9.6-59.9,9.8-90.3c0.1-18-0.9-36-1.7-53.9c-2.1-51.4-12.1-101.1-33.3-148.2
                        c-5.1-11.2-7.5-22.5-7.2-34.8c0.8-36.6,1.3-73.3,1.7-110c0.2-13.1,2.1-25.8,5.8-38.3c8.9-29.6,17.2-59.5,26-89.1
                        c15.7-53.1,30.1-106.4,33.7-161.9c1.4-21,2.2-41.9-0.4-62.9c-2.1-16.9-4.2-33.7-6.4-50.6c-3.6-27.7-6.1-55.7-11.3-83.1
                        c-13-68.1-30.2-135.1-58-199c-3.7-8.5-4.4-17.3-2.5-26.4c0.9-4.5,1.3-9.2,1.4-13.9c0.7-33.3-3.7-65.8-14.2-97.4
                        c-2.5-7.7-1.8-14.1,3.4-20.5c7.8-9.6,11.7-20.9,12.9-33c3.6-37.5,7-75,10.4-112.4c0.5-5.4,1.3-10.7,2-16.1c2.8,1.9,4,3.8,4.8,5.9
                        c10.3,26.4,22.4,52,38.5,75.4c3.6,5.2,3.6,10.5,2.2,16.2c-4.3,18.1-2,35.6,4.6,52.9c4.9,12.7,9.4,25.6,13.5,38.6
                        c3.5,11.1,5.1,22.8,9.1,33.7c13.7,37.2,26.6,74.7,42.4,110.9c22.9,52.5,47.9,104,72.2,155.9c5.6,11.8,8.1,23.5,5,36.7
                        c-4.4,18.7-0.8,36.5,9.4,52.7c4.6,7.3,10.1,14.1,15,21.2c10.6,15.7,21.1,31.5,31.6,47.3c7.2,10.8,14.4,21.6,21.7,32.3
                        c4.4,6.5,10.4,10.2,18.4,9.9c6-0.2,10,3,13.3,7.6c2,2.7,3.7,5.6,5.7,8.2c6.9,8.5,16,12.3,26.6,10.1c9.2-1.8,18.3-2.5,27.7-2.1
                        c13.9,0.5,22.4-7.3,24.8-21c0.4-2.3,0.6-4.6,0.7-6.9c0.1-6.3,2.1-11.6,6.6-16.2c7.3-7.4,8.4-16.5,5.2-26c-2.2-6.6-5.2-13-8.6-19.1
                        c-7.7-14-16-27.7-23.9-41.6c-1.9-3.4-3.5-6.9-6.4-12.6c6.9,1.1,11.8,1.4,16.4,2.6c15.4,4.2,28.4-0.3,39.3-11
                        c13.1-12.9,10.3-29.3-6-37.7c-1.5-0.8-2.9-1.6-4.5-2.1c-14.4-4.3-24.3-14.2-34.3-24.8c-15.6-16.4-31.8-32.2-48.4-47.6
                        c-8.1-7.5-19.2-12.3-20.6-25.2c0-0.3-0.3-0.6-0.4-0.9c-30.8-56.7-47.7-117.6-53.9-181.6c-3.1-32.3-8.1-64.1-20.1-94.4
                        c-11.8-29.5-25.4-58-47.3-81.6c-3.6-3.8-3.5-7.4-1.2-11.8c6.2-12,9.8-24.9,10.5-38.3c2.6-45.1-7.7-86.7-35.2-123.2
                        c-8.1-10.7-11.2-22-11.4-35.2c-0.4-23.6-1.6-47.3-3.3-70.9c-1.3-17.8-6-34.9-16.3-49.8c-8.1-11.8-16.7-23.3-26.2-34
                        c-23-25.6-51.5-42.1-85.7-47.4c-27.3-4.2-52.9-12.9-77.7-24.8c-19.2-9.2-38.6-18.1-58.2-26.5c-8.8-3.8-12.6-9.1-13.1-18.8
                        c-0.6-12.6-2.6-25.1-5.3-37.5c-1-4.8-1.4-8,2-11.6c6.7-7,10.1-15.6,11.2-25.1c1.1-9.9,2.1-19.9,2.8-29.8c0.4-5.1,2-8.9,7.2-10.8
                        c8.6-3.2,14.3-9.7,17.9-17.9c5.2-11.5,10.5-23.1,14.8-34.9c6.9-18.9,3.1-31-13.6-42.4c-8.7-5.9-12.3-13.4-11.7-23.6
                        c0.8-14.1-0.1-28.1-4.2-41.6c-13.5-45.2-40-78.6-86.4-93.2c-16.7-5.3-34-6.2-51.4-5.2c-22.9,1.2-43.9,8.2-62.1,22.1
                        c-28.3,21.6-46.7,50-53.7,85.1c-2.3,11.3-2.6,23.1-3.2,34.8c-0.5,9.4-4.1,16.4-12.1,21.5c-16.1,10.3-20.4,23.8-13,41.2
                        c6,14.1,12.8,27.8,19.4,41.6c2.7,5.6,6.5,10.2,12.6,12.1c6.4,2,8.8,6.6,9.2,12.8c0.6,8.6,1.5,17.3,2.1,25.9
                        c0.7,10.6,3.7,20.1,11.2,28.1c1.9,2,3.4,6.4,2.6,8.9c-4.1,12.6-4.7,25.5-5.1,38.5c-0.2,8.6-4,14.1-12,17.7
                        c-25.9,11.4-51.5,23.4-77.3,35c-19.8,8.9-40.4,15.6-61.9,18.9c-20.5,3.1-39.2,10.4-56.1,22.3c-18.1,12.8-33.6,28.3-46.8,46.1
                        c-14,18.9-22.4,40.1-24.1,63.6c-1.6,21.9-3.1,43.9-3.5,65.9c-0.3,15.3-4.7,28.7-12.6,41.4c-5,7.9-9.9,15.8-14.2,24.1
                        c-15.5,29.6-23.8,60.9-19.3,94.4c1.8,13.1,5.7,25.9,9.6,38.6c2,6.5,1.3,11.2-3.1,16.4c-5.8,6.8-11.2,14.1-16.1,21.6
                        c-29,44.5-43.2,93.9-48.8,146.4c-2.9,26.4-6.9,52.9-12.5,78.9c-8.9,40.9-27,78.6-43.7,116.7c-2,4.5-6.8,7.9-10.7,11.4
                        c-4.4,4-9.7,7-13.9,11.2c-16.1,15.9-32.1,32-47.9,48.2c-7.8,8-16,15-27.1,18.1c-3.1,0.9-6.1,2.7-8.9,4.4c-13.1,7.8-15.7,21-6.4,33
                        c10.4,13.4,24.1,18.2,40.7,15c5.3-1,10.6-1.9,17.8-3.1c-2.7,5.2-4.3,8.5-6.1,11.6c-8.6,15-17.3,30-25.9,45.1
                        c-2.3,4-4.6,8.2-6.1,12.6c-3.8,11-4.2,21.5,4.8,30.7c4.7,4.8,5.8,10.9,6.1,17.5c0.7,18.1,12.5,27.9,30.3,25.9
                        c5.2-0.6,10.6-0.4,15.7,0.6c19.6,4.2,28.4,1,38.8-15.6c3.5-5.6,7.4-9,14.4-8.6c6.8,0.4,12.4-2.7,16.4-8c6.4-8.5,13.2-16.9,18.4-26.1
                        c11.6-20.7,24.7-40.3,39-59.2c3.8-5,7.5-10.2,11.1-15.4c11.5-17.2,15-36,10.6-56.3c-2.4-11.1-1.4-21.8,3.9-32
                        c3.7-7.1,7-14.4,10.5-21.6c26.6-55.5,54.7-110.4,76.3-168.2c14-37.4,29.2-74.3,38.3-113.4c2.6-11.3,6.4-22.5,11-33.1
                        c8.3-19.2,12.1-38.8,7.1-59.4c-1.6-6.7-0.2-12.3,3.6-17.9c15.4-22.7,27.2-47.3,36.9-72.9C505.4,957.4,506.9,954.5,508.2,951.5z"/>
                    <path class="st0" d="M2472.6,958.7c-2.8,18-5.4,36-8.4,54c-4.5,27.4-12,53.9-22.7,79.5c-1,2.5-2.1,4.9-3.1,7.4
                        c-1.5,3.9-0.9,7.1,3,9.4c6.7,4.1,9.6,10.5,11.2,18.1c3.8,18.6,8.6,37,12.3,55.6c1,4.9,0.6,11-1.5,15.4c-2.9,6.2-2.3,11,0.1,17
                        c13.4,32.2,19.8,66.2,24.7,100.5c1,6.7-0.7,13.9-1.1,20.8c-0.6,10-1.6,19.9-1.7,29.9c0,2.5,1.7,6.2,3.8,7.5
                        c10.5,6.2,14.9,16.1,16.3,27.3c3.2,25.4,6.5,50.9,8.4,76.4c3.7,48.6,2.3,97.3,0,145.9c-2.4,51-7.8,101.6-16.4,151.9
                        c-8.5,49.9-16.9,99.8-24.5,149.8c-1.9,12.4-0.5,25.3-0.1,37.9c0.6,20.6,1.9,41.3,2.5,61.9c0.4,14.1-1.4,27.9-6.6,41.2
                        c-2.6,6.6-2.3,13,0.4,19.4c3.8,8.9,7.7,17.7,11.6,26.5c16.2,37.1,28.2,75.4,34,115.5c2.5,17.3,1.3,34.5,0.3,51.8
                        c-2.4,40.6-14.8,78.9-25.9,117.7c-13.6,47.6-25.1,95.9-37,144.1c-5.3,21.3-4.5,42.9-1.3,64.5c2.6,17.4,4.1,35,6.2,53.9
                        c1.9,0.1,5.1,0.3,8.3,0.3c12.7-0.3,25.3-0.9,38-0.9c26.5-0.1,46.7,12.1,61.4,33.5c16.7,24.3,12.3,54.4-9.8,74
                        c-9.7,8.6-21.3,13.5-33.4,17.5c-8.2,2.7-16.5,5.1-24.8,7.6c-10.3,3.1-18.4,9-24,18.3c-9,14.7-22.4,23.5-38.7,28
                        c-19.8,5.5-40,6.8-60.4,4c-15.6-2.2-26.4-10-31.2-25.5c-6.9-21.8-9.8-43.8-7.7-66.9c1.4-15.8,0.9-32.1-1.8-47.7
                        c-6.9-40.6-4.4-80.3,6.4-119.7c2.9-10.6,5.9-21.2,8.3-31.9c3.4-16,1.2-31.8-2.1-47.6c-7.4-35.9-14.4-71.8-22.2-107.6
                        c-1.7-8-4.8-16-8.8-23.2c-14.4-25.8-19.8-53.3-19.4-82.6c0.5-30.7,8.2-59.9,17.3-88.9c3.5-11.1,6.4-22.4,9.9-33.5
                        c2.4-7.4,1.1-14.1-4-19.6c-16.4-17.6-24.9-39-31.6-61.6c-17.8-59.7-33.5-119.9-44.4-181.2c-5.6-31.6-7-63.5-8-95.5
                        c-1.6-51-4-101.9-4.3-152.9c-0.2-34.3,2.8-68.5,4.5-102.8c0.5-9.6,0.7-19.3,1.6-28.9c0.9-9.2,1.9-18.5,3.9-27.6
                        c1-4.7,1.1-8.2-2.2-11.9c-5.1-5.6-9.9-11.6-15.7-18.5c-6,7.4-11.7,13.8-16.5,20.8c-1.5,2.2-0.8,6.2-0.5,9.3
                        c0.6,5.6,2.3,11.1,2.6,16.6c1.8,27.9,3.9,55.8,5,83.8c1.1,27.3,2,54.6,1.5,81.9c-0.9,49.3-2.9,98.6-4.5,147.9
                        c-1.6,50.2-10,99.3-22.5,147.8c-7.8,30.3-15.1,60.7-23.6,90.8c-4.9,17.3-10.8,34.3-17.8,50.8c-4.2,9.9-10.5,19.4-17.4,27.7
                        c-6.8,8.1-8.6,16-5.6,25.8c6.2,20.4,12.7,40.7,18,61.3c7.3,27.9,10.9,56.2,7.2,85.1c-2.4,19.1-7.8,37.1-17.3,53.8
                        c-4.7,8.2-7.9,16.7-9.7,26c-7.5,37.9-15.2,75.7-23.1,113.5c-3.5,17-2.8,33.7,1.3,50.5c4.1,16.5,8.4,32.9,12.2,49.4
                        c6.3,27.7,5.8,55.6,1.6,83.5c-3.3,21.9-4.5,43.7-2.7,65.7c1.8,21.7-2.2,42.4-10.1,62.5c-4.1,10.6-11.4,16.7-22.3,19.3
                        c-27.9,6.4-54.9,2.8-81.3-7.4c-7.9-3.1-14.4-8.3-19-15.7c-9.8-15.9-24.2-25.2-42.1-29.7c-8.7-2.2-17.2-5.2-25.6-8.3
                        c-15.1-5.5-26.1-15.8-33.8-29.7c-7.7-13.9-8.6-28.5-3.1-43.4c10.6-28.8,36.3-46.4,68.9-47c11.3-0.2,22.6,0.6,34,0.9
                        c3.6,0.1,7.2,0,11.7,0c0.8-4.1,1.6-7.6,2-11.1c1.5-14.6,2.3-29.2,4.4-43.7c4-27.2,1.3-53.9-5-80.3c-14.2-59.2-29.1-118.2-48.1-176.2
                        c-4.6-14.1-6-29.3-7.9-44.1c-3.2-25.5-5.6-51-3.2-76.7c0.6-6.6,1.5-13.3,3.1-19.7c10.2-39,20.5-77.9,38.8-114.2
                        c5-9.9,6.4-20.5,3.2-31.1c-8.2-26.7-8.4-53.8-4.8-81.2c3.9-29.7,2.2-59.3-3.3-88.5c-10.4-54.6-19.5-109.5-26.5-164.7
                        c-9.9-78.8-14-158-10-237.4c1.5-30.2,6-60.3,9.3-90.4c1.2-11.1,5.7-20.5,15.7-26.5c4-2.4,4.9-6,4.4-10.3c-0.7-6.6-0.7-13.4-2-19.9
                        c-4.2-21.6,0.5-42.3,5.2-63.1c5.2-23.1,10.1-46.2,19.5-68c1.9-4.5,2.4-9.1-0.4-13.4c-3.7-5.8-3.3-12.2-2-18.4
                        c3.9-18.2,8-36.4,12-54.6c2.2-9.8,8.6-16.3,17.6-21.3c-22.8-51.1-32.6-104.9-35.2-160.6c-4.9,0.9-5.3,4.5-6.7,7.2
                        c-11.5,22.1-22.7,44.5-34.7,66.4c-5.6,10.2-12.5,19.6-19.4,29c-4.2,5.7-6.2,11.3-5.1,18.5c2.4,15.2,0.3,30.2-7,43.7
                        c-17.3,32.1-29.8,66.1-42.9,99.9c-19.4,50.1-44.8,97.3-69.6,144.9c-10.8,20.7-21.7,41.2-32.6,61.9c-3.4,6.5-7.2,12.8-13.8,16.7
                        c-3.9,2.3-4.8,5.9-4.7,10.3c0.1,13.3,0.4,26.7-0.3,40c-0.5,9.2-1.2,18.8-3.9,27.5c-6.2,20.4-15.2,39.7-26.3,58.1
                        c-11.1,18.2-21.7,36.7-32.2,55.2c-6.5,11.3-14.5,19.7-28.8,19.9c-3.2,0-7.4,3.4-9.4,6.3c-8.5,12.7-19.8,17.9-35,16.3
                        c-8.6-0.9-17.3-0.5-26-0.5c-13.7-0.1-22.6-7.6-24.3-21.4c-1.2-9.8-3.7-18.8-9.7-26.8c-3.8-5-4.7-11.2-2.7-17.2
                        c3.4-10,7-20.1,11.4-29.7c4.2-9.4,9.6-18.3,14.3-27.5c1.9-3.8,3.6-7.7,6.1-13.2c-8,1.2-14.1,2.4-20.3,3.1
                        c-6.3,0.7-12.6,1.6-18.8,1.1c-14.8-1.3-24.3-10.3-29.8-23.4c-4-9.5-0.6-17.7,8.5-22.8c1.2-0.7,2.4-1.2,3.6-1.8
                        c23.9-10.2,43.2-26.3,58.7-46.9c13.8-18.3,30.3-32.7,51.9-40.7c5.5-2,8.7-5.9,10.6-11.2c17.4-50,34.9-99.9,52-150
                        c3.6-10.5,7.2-20.8,14.5-29.3c6.9-8.1,9.1-17.4,10-27.6c1.6-18.2,3.5-36.5,5.6-54.7c2.4-20.4,9.2-39.6,19.1-57.5
                        c10.5-18.9,21.7-37.4,32.7-56.1c1.2-2,2.5-4.1,4.3-5.5c7.8-5.7,9.9-13.6,10.5-22.7c1.2-19.8,5.3-38.9,13.7-57
                        c2.5-5.4,3.3-10.4,1.1-16.3c-3.6-9.3-2.1-19.1-0.6-28.7c3.4-22.9,8.9-45.5,20.9-65.3c12.8-21.2,18.5-44,20.6-68.1
                        c4.5-51.7,29.5-90.9,72-119.8c27.9-19,58.3-32.2,91.3-38.8c29.7-6,57.4-16.9,83.4-32.3c8.9-5.3,17.6-10.7,26.5-16
                        c8.5-5.1,13.9-12.3,15.3-22.2c1.1-8.2,1.9-16.5,2.8-24.8c0.5-5.1-1.5-9.1-5.6-12.1c-9.3-6.8-13.3-16.3-14.4-27.4
                        c-1-9.9-2.3-19.9-3.2-29.8c-0.5-6.2-2.5-10.9-8.8-13.1c-6.1-2.1-9.9-6.7-12.6-12.2c-6.7-14.1-13.7-28.1-19.8-42.5
                        c-7.3-17.3-2.4-32.3,13.8-42c7.8-4.7,11.2-10.7,11.1-19.6c-0.4-22.9,3.8-45.1,12.7-66.2c22.8-53.9,75.3-85.3,132.7-79.6
                        c57.5,5.8,103.5,47.6,114.7,105.1c2.7,14,3.4,28.4,4,42.7c0.3,8,2.7,13.3,10,16.6c7.9,3.6,12.9,10.2,16.6,17.9
                        c3.8,8.1,4.2,15.8,0.2,24c-6.3,12.9-12.2,25.9-18.5,38.8c-3.8,7.9-8.3,15.1-17.4,18.3c-5.1,1.8-6.1,6.6-6.6,11.4
                        c-1.1,10.6-2.4,21.2-3.5,31.8c-1.1,10.4-5.2,19.2-13.8,25.6c-4,3-6.5,6.8-6,11.9c0.9,8.9,1.7,17.9,3.1,26.8
                        c1.4,8.7,6.2,15.5,13.7,19.9c19.4,11.6,38.9,23.2,58.6,34.4c5.1,2.9,11.1,4.5,16.8,6.2c21,6.3,42.3,12,63.2,18.6
                        c29.7,9.3,55.8,25.2,79.1,45.5c29.1,25.4,44.9,58.2,50.5,96.1c1.8,11.9,2.9,23.8,4.3,35.7c0.8,6.7,2.9,12.5,6.7,18.4
                        c5.5,8.6,9.5,18.3,13.6,27.7c9,20.9,15.9,42.5,18.1,65.2c0.7,7.1-0.1,14.6-1.6,21.7c-1.2,5.8-1.5,11,1,16.5
                        c8.8,19,13.2,39.1,13.9,59.9c0.2,7.4,2.6,13.6,8.7,18.2c1.6,1.2,3.1,2.6,4.1,4.3c17.7,26.7,34.5,53.8,45.5,84.1
                        c7.3,20.3,10.1,41.3,11.7,62.6c0.9,11.9,2.3,23.9,3.9,35.7c0.5,3.5,1.5,7.3,3.3,10.3c15.3,26,25.1,54.3,34.9,82.7
                        c11.2,32.4,23,64.6,34.5,96.9c3.3,9.3,9.1,16,18.6,19.6c13.7,5.2,24.9,14.2,34.7,24.8c7,7.6,13.8,15.4,20,23.6
                        c10.4,13.5,23.2,23.8,38.7,30.7c5.5,2.4,11,4.9,15.9,8.3c9.3,6.5,11.3,15.4,5.9,25.4c-7.6,14.2-19.8,21.4-36,20.5
                        c-7.6-0.5-15.1-2.3-22.7-3.5c-2.2-0.4-4.5-0.5-8.6-1c2.3,5,3.9,8.8,5.7,12.6c7.4,15.3,15.2,30.4,22.3,45.8c5.9,12.7,5.8,25-3.9,36.4
                        c-2.2,2.5-2.7,6.7-3.6,10.2c-1.4,5.5-1.9,11.2-3.9,16.5c-4.4,11.3-13.5,15.7-25.5,14.3c-7.5-0.9-15.2-1-22.6,0.1
                        c-12.8,1.9-23.1-1-30.9-11.5c-5.2-7.1-11.8-11.2-20.7-12.4c-8-1-14.1-6.3-18.2-13c-20.1-33-40.6-65.8-55.7-101.5
                        c-7.3-17.4-10.6-35.6-9.6-54.6c0.5-10.3,0.1-20.7-1-30.9c-0.4-3.8-3.7-7.4-6.1-10.8c-3.6-5.1-8.5-9.5-11.3-15
                        c-28.1-54.5-56.3-108.8-83.4-163.8c-10.9-22-19.1-45.4-28.8-68c-8.6-20.2-16.9-40.6-26.7-60.2c-9.9-19.8-16.7-39.9-13.4-62.3
                        c0.7-4.5-0.8-8.1-3.6-11.9c-7.5-10.2-14.9-20.5-21.3-31.3c-10.1-17.2-19.4-34.9-29.1-52.4c-1.6-2.8-3.4-5.5-5.1-8.2
                        C2474,958.3,2473.3,958.5,2472.6,958.7z M2541,794.5c13.4,3.6,27.1,2.6,40.6,0.9c11.3-1.4,13.3-3.7,12.1-14.9
                        c-1.6-15.5-3.1-31.2-6.1-46.5c-6.3-32.4-21.8-59.7-47.3-81.2c-22.5-18.9-47.9-32.3-75.3-42.3c-11.4-4.1-22.1-3.2-32.8,2.2
                        c-23.1,11.8-37.1,30.7-43.5,55.5c-2.4,9.3-0.2,17,7.8,22.9c28.6,20.9,53.9,45.5,78.5,70.9c12.8,13.1,28.2,22.1,45.6,27.9
                        c4.4,1.5,8.7,3.2,13,4.7c-6.8-1-13.6-1.9-20.9-3c0.9,15.4,10.9,26.2,14.9,39.3c-2.6-2.2-4.6-4.7-6.4-7.3c-8-11.5-15.8-23-24-34.4
                        c-2.5-3.5-5.4-6.8-8.8-9.3c-5.8-4.3-11.2-3.2-15.7,2.3c-1.8,2.2-3.4,4.7-5.5,7.7c-3.8-8.8-7.3-16.7-10.7-24.6
                        c-11.4-26.8-29.4-48.1-53-64.9c-14.6-10.5-22.1-7.6-25.5,9.9c-0.1,0.7-0.2,1.3-0.3,2c-5.4,27.1-16.1,51.9-31.5,74.9
                        c-27.4,40.9-54.2,82.3-81.3,123.4c-1.5,2.2-2.8,4.5-4.2,6.8c-5.8,9.9-5.5,19.7,1.2,29c19.8,27.7,43.6,51.1,73.2,68.3
                        c16.4,9.5,33.8,11.2,52,5.5c13-4,24.3-10.7,34.3-19.8c2.3-2.1,4.7-4.1,7.1-6.1c-7.7,17.8-20.6,29.1-39.9,31.8
                        c-16.8,2.3-33.1-0.2-49-6c-2.9-1.1-5.9-3.7-9.7,0.5c11.1,24,23.7,47.9,29.6,74.3c-1.6-2.7-3.9-5.3-4.8-8.2
                        c-7.5-24.9-19.4-47.5-36-67.3c-17.5-20.9-36-41-54.5-61.1c-11.8-12.9-19.6-11-25.9,5.5c-8.6,22.6-12.9,46-13,70.3
                        c-0.2,57.9-0.9,115.8-1.2,173.7c-0.2,55-0.1,109.9,0,164.9c0,3.3,0.1,6.7,0.9,9.9c1.4,5,5.1,6.3,9,3c2.7-2.3,5-5.3,7.1-8.3
                        c15.5-22.2,27.9-46.1,36.7-71.8c8-23.6,16-47.2,24.1-70.8c6.4-18.6,14.4-36.5,25.6-52.8c7-10.1,15.7-17.9,27.4-22.3
                        c10.5-3.9,11.5-6,10.1-17.4c-1.7-13.9-3.5-27.7-5.2-41.6c5,18.8,10.1,37.6,15.2,56.4c10.4-3.3,16.5-7.7,25.5-18.4
                        c11.7-13.9,20.3-29.6,27.5-46.2c14.6-33.9,23.1-69.4,27.8-105.9c1.4-10.7,5.1-21.5,0-32.4c-0.7-1.5,0.2-3.9,0.7-5.8
                        c4.4-16.7,8.9-33.4,13.5-50.1c0.1-0.4,0.9-0.6,1.3-0.8c0.3,0.3,0.7,0.4,0.8,0.7c1.2,3.1,2.5,6.2,3.7,9.3c9.3,23.7,23.3,44.2,43.4,60
                        c11.9,9.4,24.4,5.5,28.8-9.1c2.3-7.6,3.5-15.6,4.1-23.5c1.8-21.2-1.1-41.9-7.4-62.1c-1.1-3.6-1.8-7.3-2.7-11
                        c11.8,19.1,27.8,33.9,45,47.6c-9.6-1.2-16.6-9.3-27.3-9.5c0,3.9,0,7.2,0,10.5c-0.1,9.7-0.1,19.3-0.4,29
                        c-0.4,11.9-5.3,21.9-14.1,29.9c-8.8,8-16.5,8.8-26,1.7c-5.3-4-9.9-8.9-14.8-13.4c-2.9-2.7-5.6-5.8-8.8-8.1c-1.2-0.9-4.2-0.7-5.4,0.2
                        c-1.2,0.9-1.7,3.4-1.6,5.2c0.1,1.5,1.3,3,2.1,4.4c13,22.9,25.9,45.8,39,68.6c2.5,4.3,5.5,8.3,8.4,12.4
                        c10.1,13.5,23.8,19.2,40.4,20.4c18.3,1.3,34.7-4.6,51.6-9.9c5.4-1.7,9.7-4.1,12.5-9c-0.5,4.3-0.8,8.6-1.7,12.8
                        c-2,9.1-0.4,17.1,6.2,24c3.9,4.1,7.7,8.3,11.4,12.6c1.7,2,3.2,4.2,4.8,6.3c-1.8,0.2-2.8-0.1-3.5-0.7c-8.4-6.6-16.8-13.1-25-19.9
                        c-14.7-12-15.2-14.3-36.2-2.9c-0.9,0.5-1.7,1-2.6,1.5c-7.9,5.2-9.1,8.1-6.9,17.6c0.3,1.2,0.5,2.4,0.8,3.6c-4.8-3.6-8.4-7.8-12-12.1
                        c-5.1-6.1-10.2-12.4-15.4-18.4c-3-3.5-6.2-8-11.5-6.3c-6,1.9-5,7.9-5.4,12.8c-0.1,1.3,0.1,2.7,0.1,4c0.3,12.6,4,24.2,9.5,35.4
                        c14.2,28.3,27,57.1,38.3,86.7c7.3,19.3,15.5,38.3,24.5,56.8c19.9,40.7,40.6,81,61.1,121.3c6.5,12.8,13.3,25.4,20.2,37.9
                        c4.5,8.1,7.6,9.1,16.4,5.8c6.8-2.6,13.5-5.6,20.2-8.6c10.8-4.7,22-7.7,33.7-8.4c1.9-0.1,3.9,0.5,5.9,0.8
                        c-16.9,2.7-31.1,11.1-46.1,17.4c-9.4,4-12.9,10.5-12.5,20.3c0.4,11,0.6,22,0.2,33c-0.7,19.4,2.7,37.9,11.8,55
                        c15.1,28.2,30.5,56.2,46.1,84.1c2.9,5.1,7,9.8,11.2,13.9c3.7,3.7,9.1,3.3,12.8,0.4c4.2-3.2,2.8-6.9,1.1-10.7
                        c-4.2-9-8.5-18.1-12.6-27.2c-0.9-2-1.2-4.4-1.8-6.6c3.4,2.7,5.2,5.8,6.7,9.1c6.5,14.2,12.6,28.7,19.7,42.6c3.1,6,8,11.4,12.9,16.2
                        c3.9,3.8,9.1,3.4,14,0.9c4.6-2.3,5.9-6,5.3-10.8c-0.2-1.3,0-2.7,0-4.1c5.1,4.9,9,10.1,14.1,13.4c9.5,6.1,19.3,0.7,19.6-10.6
                        c0.1-4.8-1.3-9.9-3.3-14.3c-3.9-8.8-8.8-17.2-13.2-25.8c-2.5-4.9-5-9.8-7.4-14.7c3,1.6,4.5,4,6,6.5c3.7,6.3,7.1,12.8,11.2,18.9
                        c4,5.9,8.2,7.7,12.4,6.2c5-1.7,7.8-7.2,6.9-14.1c-0.3-2.3-1-4.6-1.9-6.7c-3.1-7.4-6-14.8-9.6-21.9c-10-19.6-20.4-38.9-30.6-58.4
                        c-1.6-3-4.7-6-1.8-9.8c0.9-0.1,1.6-0.4,2.2-0.3c1,0.2,2,0.5,2.8,1c15.8,10.6,34.1,12.7,52.3,14.9c10.1,1.3,17.3-3.5,22.5-11.8
                        c3.2-5.2,2.6-8.5-2.6-11.8c-4.8-3-9.9-5.4-15.1-7.7c-14.2-6.4-26.3-15.6-36.3-27.6c-3.8-4.6-7.1-9.7-11.1-14.2
                        c-18.7-21.5-39.7-39.5-69.6-43.4c4.9-7.2,5-7.2,2.6-15.6c-1.1-3.8-2.6-7.6-3.9-11.3c-17.6-48.1-36.3-95.9-49.6-145.5
                        c-0.3-1.3-0.6-2.6-1.2-3.8c-3-6.6-7.8-7.4-11.8-1.3c-2.6,3.9-3.9,9.1-4.2,13.9c-0.6,11-0.2,22-0.2,33c0,2.3-0.2,4.7-0.3,7
                        c-2.2-3.6-2.8-7.2-2.8-10.7c-0.1-19,0.1-38-0.1-57c-0.1-15.4-2.3-30.5-7.4-45.1c-1.9-5.4-4.2-10.3,0.5-15.8c1.7-2,0.9-6.4,0.7-9.7
                        c-0.3-3.6-1.3-7.2-1.7-10.8c-4.6-44.1-28-79.9-51.1-115.8c-3.2-4.9-9.3-4.2-12.1,1.1c-1.4,2.6-2,5.6-2.8,8.5
                        c-1.3,5.1-2.5,10.1-3.8,15.3c0.4-5.3,0.2-10.7,1.1-15.9c5.2-27.4,2.4-54.3-5.6-80.8c-2.8-9.4-6.4-18.3-15.9-24.2
                        c12.4-12,11-26.4,8.7-41.2c-4.2-26.3-14.6-50.3-26-74c-2.9-6.1-7.8-7.4-14.1-6.6c-14.3,2-28.5,1.9-42.7-1.1
                        C2545.3,797.4,2543.2,795.7,2541,794.5z M1798.8,1032.6c-1.2-3-2.4-6.1-3.7-9.1c-1.6-4-2.7-9.6-7.7-9.2c-3.1,0.2-6.9,3.9-8.8,7
                        c-10.3,16.7-20.5,33.6-29.9,50.8c-10.9,20.1-17.2,41.8-20.3,64.4c-1.1,7.9-2.2,16.1,4.3,22.3c-2.3,7.7-4.7,15-6.6,22.4
                        c-4.1,16.3-4.9,32.9-4.9,49.6c0.1,20.3,0,40.5,0,58.9c-1-16.1-2.2-34-3.4-51.9c-0.4-6.3-2.1-11.8-8.6-14.2
                        c-6.1,2.2-7.4,7.6-8.9,12.6c-3.4,10.8-6.3,21.7-10,32.4c-13.1,37.7-26.5,75.3-39.7,113c-1.4,4.1-2.9,8.2-3.8,12.4
                        c-1.4,6.5,1,9.5,7.5,10c2,0.2,4,0.1,6-0.3c13.2-2.2,25.9,0.2,38.2,4.8c8.1,3,16,6.6,24,9.9c13.4,5.5,15.7,4.6,22.8-8.3
                        c0.6-1.2,1.3-2.3,1.9-3.5c16.1-30.7,32.1-61.3,48.1-92c19.5-37.5,38.4-75.3,53.4-114.9c12.6-33.3,25.3-66.6,42.5-97.9
                        c6.4-11.7,5.4-24.6,4.1-37.2c-0.6-5.3-5.1-7.1-9.7-4.3c-2.5,1.5-4.6,3.8-6.6,6c-7.1,8.4-14.1,16.9-21.2,25.3
                        c-1.5,1.8-3.2,3.6-5.9,6.6c3.3-16.1,1-20.6-12.4-26.9c-1.8-0.9-3.7-1.5-5.5-2.5c-8.2-4.2-15.2-2.5-22,3.5
                        c-7.5,6.6-15.5,12.6-23.4,18.7c-1.5,1.2-3.4,1.8-5.2,2.6c2.8-5.2,6.1-9.4,9.8-13.2c7.6-7.8,10.2-16.8,7.5-27.5
                        c-0.9-3.5-0.7-7.2-1-10.9c3.8,5.4,9.1,8.5,15.5,9.9c4.5,1,9.1,2.2,13.4,3.9c11.5,4.4,23.4,5.3,35.4,3.9c7.5-0.8,15.7-1.6,22.1-5.1
                        c9.9-5.4,18.5-13.4,24.3-23.3c13.7-23.6,27-47.5,40.3-71.3c1.9-3.3,4.8-7.8,0.8-10.9c-4.4-3.4-7,1.5-9.8,4.1
                        c-6.9,6.3-13.6,12.8-20.7,18.7c-5.7,4.8-12,4.7-18.4,0.5c-11.3-7.3-18.6-17-19.1-30.9c-0.4-11.3-0.5-22.6-0.8-34
                        c-0.1-2.8-0.5-5.6-0.8-9.6c-9.5,4.4-17.9,8.2-26.3,12.1c16.4-14.9,32.3-29.5,43-48.8c-2.4,14.4-5.7,28.3-7.8,42.4
                        c-2.8,18.6-3.5,37.2,3.7,55.2c5.7,14.3,17.5,17.6,29.3,7.7c23.9-20.1,40.7-44.7,46.4-76c0.9-4.9,1.6-9.8,2.4-14.8
                        c3.4,26.9,7.7,53.4,19.1,78.3c-5,5.2-5,11-3.9,17.2c2.2,12.5,4,25,6.2,37.4c5,28.6,11.8,56.7,23.9,83.3c9.6,21,20.4,41.2,37.9,57
                        c10.9,9.8,15.2,8.5,18.5-5.4c0.3-1.3,0.7-2.6,1-3.9c7.1-38.9,18.8-76.2,36.6-111.6c1.4-2.7,1.8-5.9,3.1-10.3
                        c-5.3,1.6-8.8,2.5-12.2,3.6c-15.4,5.5-31.2,6.7-47.3,4.7c-17.7-2.2-33.1-15-38.6-32.8c11,11.5,23.1,19.5,36.8,25.1
                        c18.4,7.4,36.5,6.9,54-2.3c32.4-17,56.6-42.9,77.7-72.1c5.3-7.3,5.7-15.5,1.2-23.6c-3.1-5.5-6.1-11.1-9.7-16.3
                        c-25-36.5-47.3-74.9-73.4-110.7c-18.6-25.6-31.6-53.9-36-85.7c-0.3-2-0.8-3.9-1.6-5.8c-2.8-6.7-7.5-9-14.4-6.6c-2.5,0.8-5,2-7.2,3.5
                        c-36.8,26-60,60.8-67.3,105.6c-1.1,6.5-1.3,13.2-1.9,19.8c-1.4-7.8-1.3-15.6-1.5-23.5c-0.1-4.6-0.3-9.4-1.5-13.8
                        c-2.3-8.9-11.4-12.2-18.8-6.7c-3.7,2.7-6.8,6.4-9.4,10.2c-8.3,11.7-16.2,23.6-24.4,35.4c-1.3,1.9-3,3.7-4.4,5.5
                        c3.7-13.2,13.3-23.9,13.7-37.9c-2-0.9-3.4-1.5-5.7-2.5c24.6-10.3,43.3-26.7,60.6-45c18.6-19.6,37.6-38.8,60.1-54
                        c10.6-7.2,12.6-12.3,9.4-24.5c-6.4-24.1-20.4-42.3-42.5-54.2c-10.6-5.7-21.4-6.8-32.8-2.8c-23.1,8.1-44.7,18.9-64.6,33.2
                        c-31.4,22.6-52.3,51.8-58.5,90.6c-2.5,16.1-5,32.2-7,48.4c-1,8.7,0.9,10.8,9.4,12c5.3,0.8,10.6,1.2,15.9,1.3
                        c10.8,0.2,21.5,0.1,32,0.1c-16.8,4.3-34.3,5.7-51.9,3.1c-9.3-1.3-12.4-0.1-16.3,7.7c-10.6,21.6-19.8,43.7-24.5,67.4
                        c-3.3,16.5-7.1,33.7,9.1,47.2c-9.1,3.9-12.7,10.9-15.5,18.6C1795.2,970.1,1795,1001.2,1798.8,1032.6z M2023.6,1443.2
                        c-0.6-1.5-1.3-2.9-1.7-4.5c-3.4-13.4-8.4-25.9-17.3-36.7c-7.9-9.6-17.6-16.2-30.1-18c-12.3-1.8-18.4,2.1-21,14.2
                        c-2.3,10.4-3.9,21-5.3,31.5c-5.2,38.7-6.6,77.6-6.3,116.7c0.3,44.7,1.3,89.3,6,133.8c2.8,26.1,6.3,52.2,10.2,78.2
                        c6,39.8,12.3,79.6,19.3,119.3c5.4,30.6,8.8,61.3,6.6,92.4c-0.9,12.6-2.5,25.3-2.3,37.9c0.3,23.1-0.7,46.7,12.7,67.6
                        c-0.5,1.2-0.9,2.4-1.5,3.6c-20.6,39.1-34.7,80.7-45.2,123.5c-7.6,31-10.5,62.4-0.7,93.7c0.6,1.8,0.6,4,0.3,5.9
                        c-2.4,13.6,0.4,26.6,4.6,39.3c20.3,61.7,36.6,124.5,51,187.8c5,21.9,8.3,43.9,5,66.4c-1.8,12.5-3.6,25.1-5.2,37.6
                        c-1.1,8.2-3.3,16.6,3.5,23.3c-7.4,16.9-8.9,17.3-26.4,16.2c-14.9-1-30-1.6-44.9-0.5c-20.7,1.5-36.2,12.4-46.3,30.6
                        c-9.6,17.4-8.4,34.1,4.8,49c4.9,5.6,11.5,10.4,18.2,13.5c9.6,4.5,19.9,7.5,30.1,10.5c18.2,5.4,33.7,14.4,44.1,30.7
                        c5.7,9,13.6,14.2,23.4,17.5c18.4,6.1,37.2,7.2,56.3,5.8c14-1,22.5-8.3,26.4-21.8c5.8-20.2,7.1-40.6,5.5-61.3
                        c-0.7-8.6-1-17.3-0.3-25.9c1.8-20.6,4.2-41.1,6.4-61.6c1.7-16-0.6-31.2-7.6-45.7c-4.8-9.9-12.4-16.3-23.4-18.4
                        c-20.2-3.9-41.7,7.9-49.2,27c-2.3,5.8-5,11.4-7.5,17.1c-1.1-8.9,1.7-16.4,6.2-23c4.3-6.2,9.1-12.6,15-17.1
                        c5.4-4.1,12.3-6.7,18.9-8.4c14.7-3.9,26.9,3.9,40.5,11.6c-0.7-5.2-0.8-8.5-1.6-11.6c-1.4-5.8-3-11.6-5-17.3
                        c-7.2-21.3-8.7-42.9-4.2-65c7.5-36.9,14.7-73.8,21.9-110.7c2.3-11.5,5-22.9,11.2-32.9c12.9-20.7,18-43.4,18.2-67.6
                        c0.2-33.1-6.9-64.8-17.6-95.8c-9.3-27-14.7-54.6-13.4-83.3c0.7-15.3,1.3-30.6,1.6-46c0.1-5.6-0.1-11.4-1.2-16.8
                        c-1.2-5.7-4.8-7.5-10.5-6.1c-2.6,0.6-5.1,1.6-7.4,2.8c-17.9,8.9-30.4,22.4-35.9,41.9c-1.6,5.8-3,11.6-4.4,17.4
                        c-11.7,50.9-16.3,102.5-17.6,154.6c-0.7,27.5,1.7,54.6,11.7,80.6c6.4,16.7,16.3,30.4,31.7,40c1.7,1,2.9,2.7,4.4,4
                        c-4.3,0-7.6-1.3-10.6-3.1c-10.4-6.6-19.6-15.1-23.2-26.7c-4.7-14.7-13.1-20-28.1-18.4c-6.7,0.7-13.7-2.2-20.5-3.5
                        c-4.2-0.8-8.3-1.8-13.1-2.8c-0.3,3.9-0.6,6.8-0.8,10.1c-5.9-9.9-11.6-19.3-18.2-30.1c4.4,2,7.2,3.6,10.3,4.6
                        c7.2,2.3,14.5,5.2,21.9,6.3c14.7,2.1,23.7-4.6,26.8-18.9c0.8-3.5,1-7.2,1.3-10.9c2.9-35.2,5.2-70.4,8.7-105.6
                        c4.3-43.4,9.7-86.7,14.6-130c1-8.9-1-17.2-5.2-25c-7.5-13.9-14.2-14.3-23-1.2c-3.1,4.6-6.1,9.2-9.2,13.9c8.7-23,18.7-45.1,28.9-67.1
                        c17.6-38,26.7-78,29.2-119.6c1.3-21.9,1.3-44,2.8-65.9c3.3-50.9,7.3-101.7,10.6-152.6c1.6-24.6,1.7-49.3-1.4-73.9
                        c-1.5-11.7-5.4-14.7-16.6-12c-8.1,1.9-15.9,4.9-24,6.8c-5.5,1.3-11.1,2.4-16.7,2.3c-7.1,0-9.3-3.3-8.1-10.5c0.2-1.3,0.7-2.6,1.1-3.9
                        c8.7-36.2,13.7-72.7,7.9-109.8c0.8,1.8,2,3.5,2.4,5.3c3.7,19.2,3.2,38.4,1.3,57.7c-1.3,12.6-2.9,25.2-3.5,37.8
                        c-0.5,11.3,3.7,15.1,15.1,14.3c4.9-0.4,9.8-1.6,14.5-3.3c20.1-7,40.1-14.3,60.2-21.4c1.9-0.7,3.9-0.8,5.8-1.2
                        c-17.4,16.3-22.8,37-24.9,59c-2.2,22.5-4.8,45-6.6,67.6c-4.2,54.8-8.1,109.6-12.1,164.4c-0.9,12-1.3,24-2.4,35.9
                        c-3,31.2-6.2,62.3-9.4,93.4c-0.6,6.1,0,12.7-7.3,16c-1.5,0.7-2.1,3.3-3,5c-4.1,7.6-8.1,15.3-12.4,22.8c-5.1,8.8-5.3,17.5-1.3,26.7
                        c4.9,11.3,9.3,22.8,14.1,34.1c1.2,2.9,2.8,5.7,4.8,9.7c9.6-7.8,18.2-14.5,26.5-21.7c7-6,10.4-7.9,4.9-19.6c-0.2-0.3,0.5-1.1,0.8-1.7
                        c13.5,12.7,21.9,27.5,20.3,46.7c-0.5,6-0.1,12-0.1,18c0.1,13,0,26,0.2,39c0.1,3.7,0.5,7.9,4.8,9c4.2,1.1,6.8-2.1,8.5-5.5
                        c3.1-6.2,6.4-12.4,9-18.9c17.7-44,29.2-89.8,40.6-135.7c10.2-41.5,18.7-83.3,21.2-125.9c3.3-56.2,4.5-112.5,6.3-168.8
                        c1.5-46.7-1.9-93.2-4.3-139.8c-0.5-10.3-1.2-20.6-1.7-30.9c-0.2-4.9-1.1-9.3-5.9-11.8c-5.1-2.6-8.9,0.3-12.8,3
                        c-0.3,0.2-0.5,0.4-0.8,0.6c-11.4,9.5-24.7,15.6-38.4,20.8c-1.8,0.7-3.8,1-5.7,1.5c8.4-6.7,17.2-11.9,26.1-17.1
                        c21-12.3,38.8-28,52.1-48.7c15.5-24.3,15.4-34.9-1.9-57.8c-33.8-44.8-60.4-93.2-76.6-147.2c-1.3-4.5-5.2-8.2-8-12.2
                        c-1.8-2.6-4.8-4.8-5.7-7.7c-3-9.8-5.4-19.8-7.7-29.8c-3.6-15.1-12.8-26.1-24.7-35.3c-7-5.5-15.2-7.3-23.6-5.2
                        c-9.3,2.3-18.3,5.9-27.4,9c-1.8,0.6-3.6,1.4-5.4,2.1c-0.1-0.3-0.2-0.6-0.3-0.9c6.4-5.1,12.8-10.2,19.6-15.7c-3.5-4.3-6.1-7.8-8.8-11
                        c-6.9-8.1-13.6-16.4-20.9-24.2c-5.5-6-10.5-4.6-13.3,3c-0.5,1.2-0.8,2.5-1.1,3.8c-3.8,17.2-7.7,34.4-11.3,51.7
                        c-2.5,11.8-1,13.4,10.8,13.9c0.4,0,0.9,0.7,1.7,1.4c-1.5,2.4-3.3,4.8-4.6,7.4c-4.8,10.2-11,20.1-13.9,30.8
                        c-6.8,25.4-12,51.2-17.7,76.8c-0.7,3.2-0.7,6.7-0.3,9.9c0.9,6,4.7,7.9,10,4.7c2.8-1.7,5.2-4.2,7.2-6.8c3.4-4.5,6.4-9.3,9.3-14.2
                        c9.9-16.6,23.1-30,39.1-40.8c5.2-3.5,10.5-6.6,17.1-7.1c-1.4,2-3,3.4-4.8,4.6c-17,11.5-31.4,25.7-43.4,42.3
                        c-12.2,16.8-21.5,35.2-28.8,54.6c-3.5,9.2-2,13,6.3,18.5c3.6,2.4,7.4,4.5,11.1,6.7C2014.1,1398.4,2021.1,1419.6,2023.6,1443.2z
                        M2407,2148.3c-0.9-1.2-2.4-2.3-2.7-3.7c-1.6-7.5-2.7-15.1-4.4-22.5c-4.2-17.9-8.8-35.5-19.2-51.1c-6.6-9.9-15.3-16.5-26.4-20.3
                        c-11.8-4.1-16.2-1.1-16.8,11.2c-0.1,1.3-0.1,2.7,0,4c0.5,12.3,0.8,24.6,1.4,37c1.6,34.6-1.7,68.6-13.7,101.3
                        c-8.2,22.3-14,45.1-16.1,68.8c-3.2,35.1,1.1,68.4,20.7,98.8c3.3,5.1,4.9,11.6,6.2,17.7c6.8,32.2,13,64.6,19.9,96.8
                        c7.2,33.5,8.8,66.6-4.7,99.1c-1.9,4.6-1.6,10.1-2.6,17.4c18.7-12.5,36.2-18.2,55.1-7.2c15.7,9.1,28.4,30.7,27,47.9
                        c-2.8-6.6-5.5-11.6-7.1-16.9c-5.9-20.1-20-29.7-40.1-32c-16.8-1.9-29.3,5.4-35.4,21.2c-4.5,11.6-7.1,23.6-6.2,36.1
                        c1,13.6,2,27.2,3.4,40.8c2.3,22.2,4.2,44.4,1.8,66.8c-1.8,16.4,1,32.5,6.2,48.2c5.4,16.1,12,21.6,29.2,21.9c12.9,0.2,26-0.4,38.8-2
                        c18-2.2,32.7-10.8,42.5-26.6c6.6-10.7,16.2-17.4,28.1-21.2c6.7-2.1,13.6-3.6,20-6.2c10.4-4.3,21.3-8.2,30.7-14.2
                        c19.2-12.3,25.2-34.7,15.1-54.3c-10.2-19.6-26.2-32.1-48.8-33.3c-14.6-0.8-29.3-0.1-43.9,1c-14.9,1.1-21.4-2.4-26.5-16.9
                        c6.1-6.7,6.3-14.7,4-23.2c-0.8-2.9-1.1-5.9-1.6-8.8c-5.3-29.8-7.4-59.7-0.2-89.3c10.9-44.9,22.6-89.7,34.5-134.4
                        c4.9-18.6,12.4-36.7,16.4-55.5c4.9-22.7,7.3-46,10.7-69c3-20.6,2.6-41.2-2.4-61.5c-10.4-41.7-22.7-82.8-41.1-121.8
                        c-2-4.2-3.6-8.6-5.6-12.8c-1.5-3.2-1.9-6.2-0.1-9.5c6.5-11.7,8.6-24.6,8.9-37.6c0.4-19.6-0.5-39.3-0.7-58.9
                        c-0.2-18-2.5-36.2-0.2-53.9c5.4-40.2,13.2-80.1,19.8-120.2c9.1-55.2,17-110.6,19.7-166.6c3.1-64.3,4.5-128.6-2.6-192.7
                        c-1.5-13.9-3.7-27.7-6.2-41.5c-2.2-12.2-8.2-16.2-20.5-14.8c-2.3,0.3-4.6,0.9-6.8,1.5c-15.1,4.4-24.5,15.4-32.2,28.3
                        c-1.7,2.9-3.3,5.9-5,8.9c2.6-16.9,11.7-29.3,26-38c3.7-2.3,7.6-4.1,11.2-6.6c7.8-5.3,9.3-9.1,5.9-17.8c-15-39.4-37.2-73.6-72.8-97.7
                        c-1.6-1.1-2.9-2.5-4.4-3.7c2.2-0.6,3.8-0.2,5.2,0.4c19,8,33.5,21.5,45,38.4c4.5,6.6,8.6,13.5,13.3,20c2.5,3.4,5.6,6.7,9.1,9
                        c5.8,3.9,9.6,1.9,10.2-4.9c0.3-3.3,0.3-6.7-0.4-9.9c-5.8-25.6-11.7-51.3-17.9-76.8c-3.3-13.4-9.4-25.6-17.9-36.5
                        c-1.7-2.2-3.4-4.3-5.1-6.5c1.3-0.1,1.9,0.2,2.3,0.7c3.3,3.1,6.9,5.8,11.5,3c4.5-2.6,4.1-7.2,3.5-11.6c-0.1-1-0.3-2-0.6-2.9
                        c-3.7-16.9-7.4-33.8-11.2-50.7c-0.4-1.9-0.8-3.9-1.6-5.7c-2.5-5.6-6.9-7.1-10.9-2.7c-10.9,11.9-21.2,24.4-32,36.9
                        c5.7,4.5,10.2,8,14.7,11.6c-9-1.2-17.2-3.8-25.4-5.7c-6.5-1.5-13.3-2.1-19.6,1.2c-11.4,6.1-21.3,14.1-26.2,26.4
                        c-5.2,13.3-10.9,26.5-12.3,41c-0.2,2.6-1.8,6-3.8,7.3c-7.1,4.5-9.9,11.3-12.1,18.9c-11.4,39.4-29.2,75.7-51.9,109.8
                        c-8.7,13-18.1,25.5-27.2,38.3c-10.2,14.2-11.3,29.2-1.8,43.9c6.8,10.6,14.4,20.8,22.8,30.2c13.5,15,30.4,25.8,47.9,35.7
                        c1.4,0.8,3,1.5,4.4,2.5c1.3,0.9,2.5,1.9,3.8,2.9c-14.3-3.5-27.1-9.5-38.5-18.3c-4.4-3.4-8.4-8.1-15-5.2c-6.7,2.9-6,9.1-6.3,14.6
                        c-1.7,26.6-3.3,53.2-4.6,79.8c-1,21-2.6,42-2.1,62.9c1.5,61,3.8,121.9,6.2,182.8c0.7,16.3,2.3,32.6,4.7,48.7
                        c7.7,51.4,20.6,101.7,33.9,151.9c7.8,29.4,18.9,57.5,30.1,85.6c2,5,4.3,12.3,10.9,11.3c7.3-1.1,5.5-8.7,5.5-14.1
                        c0.2-19.7-0.1-39.3,0.5-59c0.2-6.8,1.3-14.4,4.6-20.1c5.6-9.7,13.2-18.3,19.9-27.4c0.1,3.5-1.4,6-2.8,8.5
                        c-5.8,10.1-5.5,12.2,3.1,19.5c6.6,5.6,13.3,11.1,20.1,16.5c2,1.5,4.4,2.5,7.7,4.3c7.1-16.7,14.4-32.3,20.3-48.5
                        c1.7-4.8,1-11.7-1.3-16.4c-5.9-12.2-11-25-21.2-34.7c-1.5-1.4-2.3-4-2.6-6.1c-3.5-32.8-7.2-65.6-10-98.4c-2-23.5-2-47.3-4-70.8
                        c-5.8-69-11.9-138.1-18.4-207c-1.6-17.6-8.3-33.5-20.7-46.6c-1.4-1.5-2.8-3-4.2-4.6c9.6,1.9,18.3,5.2,27,8.3
                        c15.7,5.5,31.2,11.7,47.2,16.1c18.5,5,24.8-1.2,22.6-20.4c-1.6-13.6-3.2-27.1-4.9-40.7c-2.3-18.9-0.4-40.3,5.1-53.8
                        c0,2,0.2,3.3,0,4.6c-4,25.6-3.1,51.1,1.9,76.5c2.2,11.4,4.9,22.8,6.8,34.3c1.2,7.4-1,10-8.5,9.9c-5.3-0.1-10.6-1.1-15.7-2.3
                        c-7.7-1.9-15.3-4.7-23-6.7c-12.2-3.1-16.4-0.7-17.7,11.6c-1.6,15.2-3.1,30.6-2.4,45.9c2,44.6,5.1,89.1,7.7,133.7
                        c1.9,31.6,4,63.2,5.3,94.8c1.8,42.9,7.8,84.9,24.6,124.8c10,23.6,21.1,46.7,31.6,70.1c1.3,2.9,2.4,5.9,3.6,8.8
                        c-4.7-4.6-8-9.9-11.6-14.9c-7.5-10.3-13.8-9.8-20.1,1.4c-5.6,9.9-8.1,20.6-6.9,31.7C2400.2,2093.6,2403.7,2121,2407,2148.3
                        L2407,2148.3z M2212.6,976.3c-2.7-6.4-5.1-12.9-8.1-19.1c-4.1-8.3-9.2-9.8-16.9-4.5c-4.3,3-8.2,6.8-11.7,10.7
                        c-14.9,16.6-29.9,33.2-44.3,50.2c-7.7,9.1-14.8,18.9-21,29.1c-19.1,31.8-27.9,66.7-29.4,103.6c-0.3,8.3,0.5,9.9,7.3,12.1
                        c13,4.3,22.2,13,29.9,23.9c10.4,14.9,17.7,31.3,23.7,48.3c10.1,28.6,20.1,57.1,30.1,85.7c7.7,22.2,18.5,42.7,32.4,61.6
                        c1.6,2.1,3.1,4.4,5.1,6.2c5.3,4.9,10.1,3.5,11.5-3.4c0.6-3.2,0.5-6.6,0.5-10c-0.2-59.3-0.4-118.6-0.7-178
                        c-0.3-56.3-0.5-112.6-1.1-169c-0.1-11.9-4.1-24.1-2.5-35.7c1.6-12.1,3.1-24.3,4.1-36.5c0.5-6,0.4-12,0.3-18
                        c-0.2-19.3-0.5-38.6-0.8-58c-0.6-53.3-1.1-106.6-1.6-159.9c-0.4-33.5-8.5-65.2-22.2-95.6c-9.7-21.4-26.2-32.9-49.8-34.1
                        c-5.5-0.3-11-1.5-16.5-2.3c29.5-5.3,54.5,6.2,69.7,31c9.3,15.2,15.4,31.7,17.9,49.4c0.8,5.8,2.1,11.5,3.1,17.3
                        c2.7-1.8,3.9-3.4,4.4-5.2c2.1-7.7,4.1-15.4,5.9-23.2c4-17.1,10.7-33,21.7-46.8c16.3-20.6,38.4-26,63.3-23.2
                        c-5.3,1.5-10.8,2.6-16.4,3c-22.7,1.3-39.1,11.9-48.7,32.6c-3.3,7.2-6.1,14.7-9.2,22.1c-3.4,8.1-8.3,16-9.7,24.5
                        c-3.2,19.3-5.8,38.9-6.5,58.5c-1.5,45-2,89.9-2.2,134.9c-0.2,36,0.6,72,1.2,107.9c0,3.3-0.7,7.1,3,10.9c1.4-3.3,2.9-5.5,3.4-7.9
                        c4.8-26.1,16.5-49.1,31.6-70.5c25.5-36.2,51.2-72.3,76.8-108.5c17-24,28.2-50.4,34.4-79.1c3.5-16.3,7.8-32.4,11.9-48.5
                        c6-23.1,18.1-41.3,40.7-51.2c3.5-1.5,6.2-3.8,6.3-9.2c-23.7-8.7-48-15-73.6-18.9c3.8-1.6,6.8-2.9,11.2-4.8c-3.8-2.8-6.1-4.6-8.6-6.1
                        c-11.1-6.7-22.2-13.4-33.5-20c-12.3-7.2-19.9-17.6-21.7-31.8c-1.3-10.6-2.1-21.2-3.5-31.8c-0.9-6.4,0.9-10.8,7-13.8
                        c8.1-4,12.1-11.1,13.4-19.9c0.8-5.3,1.3-10.6,1.9-15.9c1-9.6,2.1-19.2,3-28.8c0.5-5.4,2.8-8.7,8.6-8.9c5.8-0.2,10.4-2.9,12.9-8
                        c7-14.4,13.9-28.8,20.3-43.4c3.2-7.4,1.4-14.7-5-19.8c-4.1-3.3-8.6-6.4-13.5-8.3c-6.7-2.5-8.6-6.4-8.9-13.5
                        c-0.5-16.3-1.1-32.7-3.7-48.7c-7.2-44.3-43.8-92.5-105.4-97.8c-53.6-4.6-101.4,25.9-120.1,77.4c-8.2,22.5-11.5,45.8-9.6,69.7
                        c0.8,9.8,0.3,10.4-9.1,13.4c-0.6,0.2-1.3,0.4-1.9,0.6c-15.4,5-20.8,16.2-14.4,31.1c5.1,11.9,11,23.5,16.4,35.3
                        c3.1,6.9,7.6,11.6,15.6,12c4.7,0.2,7.3,2.7,7.8,7.4c0.3,3.3,0.8,6.6,1.2,9.9c1.2,11.6,2.4,23.2,3.7,34.8c1.1,9.5,5.3,17.2,14.2,21.5
                        c5.8,2.8,7.2,7.1,6.5,13c-1.2,9.9-2,19.9-3.1,29.8c-1.7,15.8-9.7,27.2-23.5,35c-10.1,5.7-20.1,11.8-30,17.9c-3.3,2-6.3,4.5-10.1,7.2
                        c2.7,1.7,4.5,2.8,6.3,3.9c-21.8,5.6-43.3,10.9-64.7,16.7c-2.8,0.8-6.5,3-7.4,5.4c-1.4,3.9,2.7,5.6,5.8,6.8
                        c19.6,7.4,31.7,21.8,37.6,41.3c6.2,20.4,12,40.9,16.6,61.6c5.5,24.8,15.1,47.3,29.8,68c26,36.7,51.3,73.8,77,110.8
                        c10.5,15.1,19.8,30.8,26.2,48.1C2209,952.7,2212.4,964,2212.6,976.3z M1654.3,1583.9c0.8,0.3,1.6,0.5,2.4,0.8
                        c-1.3,3.3-2.4,6.6-3.9,9.8c-3.6,7.9-7.3,15.7-11.1,23.5c-1.8,3.8-1.6,7.1,1.7,9.9c3.4,2.9,7.4,3.3,11,0.7c2.7-1.9,5-4.3,7.1-6.9
                        c2.5-3.1,4.7-6.5,6.7-10c13.2-23.1,26.7-46.1,39.2-69.6c5.7-10.8,9.6-22.6,14-34.1c1.5-4,2.5-8.4,2.7-12.6c0.8-19,1.1-38,1.6-56.9
                        c0.2-7.6-3.3-12.6-10-15.7c-5.8-2.6-11.5-5.3-17.2-8c-32.1-15.1-63-12.3-91.2,8.8c-11.3,8.4-21.3,19.3-30,30.5
                        c-13.7,17.6-29.8,31.2-50.4,39.7c-3.1,1.3-6.1,2.8-8.8,4.7c-4.6,3.3-5.3,6.2-2.3,11.1c5.2,8.6,12.4,13.9,23.1,12.5
                        c5.9-0.8,11.8-1.9,17.7-2.9c12.2-2.1,24.1-5.1,34.5-12.4c1.2-0.8,3-0.7,4.6-1c2.9,4.4-0.1,7.4-1.7,10.5
                        c-10.2,19.5-20.7,38.8-30.7,58.3c-3.9,7.7-7.4,15.7-10.3,23.8c-1.3,3.5-1.6,8-0.6,11.6c2.1,8.1,10.5,10.1,16.2,4
                        c2.9-3.1,5-7,7.3-10.7c3-4.8,5.8-9.8,8.6-14.7c0.7,2.9-0.1,4.9-1.1,6.9c-5.2,10.1-10.5,20.1-15.5,30.2c-1.7,3.5-3.2,7.4-3.7,11.2
                        c-0.9,7.5,2.5,13.5,8,15.7c6.3,2.5,11-0.8,15.1-4.9c3.4-3.4,6.5-7.2,10.5-11.7c0,3,0,4.6,0,6.2c0.2,9,5.6,13.3,14.4,11.4
                        c6.9-1.5,12.1-5.5,15.3-11.8c3-5.9,5.8-12,8.6-18C1642.1,1610.4,1648.2,1597.2,1654.3,1583.9z"/>
                    <path class="st0" d="M2222.2,1628.1c-0.6-3.5-1.5-7-1.7-10.5c-1.6-28.2-3.1-56.4-4.7-84.7c-0.7-12.2-0.1-24.5-3.7-36.6
                        c-2.6-8.8,4.3-13.8,10.2-18.6c9.4,6.3,11.9,13.6,9.5,24.9c-3,13.8-2.9,28.4-3.7,42.6c-1.7,27.5-3.1,55.1-4.7,82.6
                        C2223,1628,2222.6,1628.1,2222.2,1628.1z"/>
                    <path class="st0" d="M508.2,951.5c-1.3,3-2.7,5.9-3.9,8.9c-9.7,25.6-21.5,50.2-36.9,72.9c-3.8,5.6-5.2,11.2-3.6,17.9
                        c4.9,20.6,1.2,40.1-7.1,59.4c-4.6,10.6-8.4,21.8-11,33.1c-9,39.1-24.3,76-38.3,113.4c-21.6,57.8-49.7,112.6-76.3,168.2
                        c-3.5,7.2-6.8,14.5-10.5,21.6c-5.3,10.2-6.3,20.9-3.9,32c4.3,20.2,0.9,39-10.6,56.3c-3.5,5.3-7.2,10.4-11.1,15.4
                        c-14.4,18.8-27.4,38.5-39,59.2c-5.2,9.2-12,17.6-18.4,26.1c-4,5.3-9.7,8.3-16.4,8c-7-0.4-10.9,3-14.4,8.6
                        c-10.4,16.6-19.2,19.8-38.8,15.6c-5.1-1.1-10.6-1.2-15.7-0.6c-17.8,2-29.7-7.8-30.3-25.9c-0.2-6.6-1.4-12.7-6.1-17.5
                        c-9-9.1-8.6-19.7-4.8-30.7c1.5-4.4,3.8-8.5,6.1-12.6c8.5-15.1,17.3-30,25.9-45.1c1.8-3.1,3.3-6.4,6.1-11.6
                        c-7.2,1.3-12.5,2.1-17.8,3.1c-16.6,3.2-30.3-1.6-40.7-15c-9.4-12-6.8-25.3,6.4-33c2.9-1.7,5.8-3.5,8.9-4.4
                        c11.1-3.1,19.3-10.1,27.1-18.1c15.8-16.2,31.8-32.3,47.9-48.2c4.2-4.2,9.5-7.2,13.9-11.2c3.9-3.5,8.8-6.9,10.7-11.4
                        c16.7-38.1,34.8-75.8,43.7-116.7c5.7-26,9.7-52.4,12.5-78.9c5.7-52.5,19.8-101.9,48.8-146.4c4.9-7.5,10.3-14.8,16.1-21.6
                        c4.4-5.2,5.1-9.9,3.1-16.4c-3.9-12.7-7.9-25.5-9.6-38.6c-4.6-33.5,3.7-64.8,19.3-94.4c4.3-8.2,9.3-16.2,14.2-24.1
                        c8-12.7,12.4-26.1,12.6-41.4c0.4-22,1.9-43.9,3.5-65.9c1.7-23.5,10.1-44.7,24.1-63.6c13.2-17.8,28.7-33.3,46.8-46.1
                        c16.9-11.9,35.6-19.2,56.1-22.3c21.5-3.2,42.1-10,61.9-18.9c25.8-11.6,51.5-23.6,77.3-35c8.1-3.6,11.8-9,12-17.7
                        c0.3-13,1-25.9,5.1-38.5c0.8-2.5-0.6-6.8-2.6-8.9c-7.6-8-10.5-17.5-11.2-28.1c-0.6-8.6-1.4-17.3-2.1-25.9
                        c-0.5-6.2-2.8-10.7-9.2-12.8c-6.1-1.9-10-6.5-12.6-12.1c-6.5-13.8-13.4-27.6-19.4-41.6c-7.4-17.4-3.1-30.9,13-41.2
                        c8.1-5.2,11.7-12.1,12.1-21.5c0.6-11.6,0.9-23.4,3.2-34.8c7-35.1,25.4-63.5,53.7-85.1c18.2-13.9,39.2-20.9,62.1-22.1
                        c17.4-0.9,34.7,0,51.4,5.2c46.4,14.6,73,48,86.4,93.2c4,13.5,5,27.5,4.2,41.6c-0.6,10.3,3,17.7,11.7,23.6
                        c16.7,11.4,20.5,23.4,13.6,42.4c-4.3,11.9-9.6,23.4-14.8,34.9c-3.6,8.2-9.3,14.7-17.9,17.9c-5.2,2-6.8,5.7-7.2,10.8
                        c-0.7,10-1.7,19.9-2.8,29.8c-1.1,9.5-4.5,18.1-11.2,25.1c-3.5,3.6-3,6.9-2,11.6c2.7,12.3,4.6,24.9,5.3,37.5
                        c0.5,9.7,4.3,15,13.1,18.8c19.6,8.4,38.9,17.3,58.2,26.5c24.8,11.9,50.4,20.6,77.7,24.8c34.2,5.3,62.7,21.8,85.7,47.4
                        c9.5,10.6,18.1,22.2,26.2,34c10.3,14.9,14.9,32,16.3,49.8c1.8,23.6,2.9,47.2,3.3,70.9c0.2,13.2,3.3,24.5,11.4,35.2
                        c27.5,36.4,37.8,78.1,35.2,123.2c-0.8,13.4-4.3,26.3-10.5,38.3c-2.3,4.3-2.4,7.9,1.2,11.8c21.9,23.6,35.6,52.1,47.3,81.6
                        c12.1,30.3,17,62.2,20.1,94.4c6.2,64,23,124.9,53.9,181.6c0.2,0.3,0.4,0.6,0.4,0.9c1.4,12.9,12.5,17.7,20.6,25.2
                        c16.6,15.4,32.8,31.2,48.4,47.6c10,10.5,20,20.5,34.3,24.8c1.6,0.5,3,1.3,4.5,2.1c16.3,8.5,19.1,24.8,6,37.7
                        c-10.9,10.8-23.9,15.2-39.3,11c-4.6-1.2-9.4-1.5-16.4-2.6c2.9,5.7,4.4,9.2,6.4,12.6c7.9,13.9,16.2,27.6,23.9,41.6
                        c3.4,6.1,6.3,12.5,8.6,19.1c3.2,9.4,2.1,18.5-5.2,26c-4.5,4.6-6.5,9.9-6.6,16.2c0,2.3-0.3,4.7-0.7,6.9c-2.5,13.7-10.9,21.5-24.8,21
                        c-9.4-0.4-18.5,0.3-27.7,2.1c-10.6,2.1-19.7-1.6-26.6-10.1c-2.1-2.6-3.8-5.5-5.7-8.2c-3.3-4.5-7.3-7.8-13.3-7.6
                        c-8.1,0.3-14.1-3.4-18.4-9.9c-7.2-10.8-14.5-21.6-21.7-32.3c-10.5-15.8-21-31.6-31.6-47.3c-4.8-7.2-10.3-13.9-15-21.2
                        c-10.2-16.2-13.8-34-9.4-52.7c3.1-13.2,0.6-24.8-5-36.7c-24.3-51.8-49.3-103.4-72.2-155.9c-15.8-36.3-28.8-73.8-42.4-110.9
                        c-4-10.9-5.6-22.6-9.1-33.7c-4-13-8.6-25.9-13.5-38.6c-6.6-17.3-8.9-34.7-4.6-52.9c1.3-5.7,1.3-11-2.2-16.2
                        c-16.2-23.4-28.2-49-38.5-75.4c-0.8-2.1-2-4-4.8-5.9c-0.7,5.4-1.5,10.7-2,16.1c-3.5,37.5-6.8,75-10.4,112.4
                        c-1.2,12.1-5,23.5-12.9,33c-5.2,6.3-6,12.8-3.4,20.5c10.5,31.6,14.9,64.1,14.2,97.4c-0.1,4.6-0.5,9.3-1.4,13.9
                        c-1.9,9.1-1.2,18,2.5,26.4c27.9,63.8,45,130.9,58,199c5.3,27.4,7.7,55.4,11.3,83.1c2.2,16.8,4.3,33.7,6.4,50.6
                        c2.6,20.9,1.8,41.9,0.4,62.9c-3.6,55.6-18,108.9-33.7,161.9c-8.8,29.7-17.1,59.5-26,89.1c-3.8,12.6-5.7,25.2-5.8,38.3
                        c-0.4,36.7-0.9,73.3-1.7,110c-0.3,12.3,2.1,23.5,7.2,34.8c21.2,47.1,31.1,96.8,33.3,148.2c0.7,18,1.8,36,1.7,53.9
                        c-0.2,30.4-4.6,60.4-9.8,90.3c-5.5,31.8-10.2,63.8-19.6,94.8c-6.1,20.1-12.6,40-22.4,58.7c-2.8,5.4-3,10.4-0.1,16.1
                        c2.2,4.3,3.3,9.4,4,14.3c4,26.7,7.9,53.4,11.6,80.1c1,7,3.8,12.5,8.6,17.5c5.8,6,11.2,12.3,16.5,18.8c14.1,17.1,30.2,31.9,48.8,44
                        c19,12.3,35,27.7,47.9,46.2c4.9,7,8.5,15.1,11.3,23.1c3.3,9.4,0.3,18.6-8.5,23.4c-32.7,17.8-66.7,30.7-105,23.2
                        c-13.6-2.6-26-8.4-37-16.9c-19.6-15.1-40.3-28.3-63.7-36.8c-12.6-4.6-19.1-14.9-22.7-27.4c-8.1-28.7-12.1-58-13.9-87.6
                        c-0.3-4.3-0.4-8.7-1.2-12.9c-4.5-24.3-3.3-48.4,1.9-72.4c2.2-10.1,4.5-20.1,7-30.1c7.9-32.2,6.7-64.1-3.2-95.6
                        c-9.5-30.2-19.8-60.1-29.4-90.2c-6.8-21.3-11.4-43.1-13.3-65.4c-2.1-24.8,1-49.2,5.1-73.6c4.2-25.3,8.4-50.6,12-75.9
                        c1.6-11.2,3.2-22.6,2.3-33.8c-0.8-9.7-4.1-19.5-7.8-28.6c-9.4-22.8-17.5-46-23.6-69.9c-10.9-42.8-15.7-86.4-16.5-130.5
                        c-0.6-32.7,0.8-65.3,5.4-97.7c1.1-7.6,0.5-14.4-4.6-20.7c-2.1-2.7-3-6.6-3.9-10.1c-14.2-55.9-25.3-112.3-32.8-169.5
                        c-3.9-30.1-7.5-60.2-7.6-90.6c0-11,1.1-21.9,1.7-32.9c0.3-5.3,0.7-10.5,1.1-16.5c-11.2,0-21.6,0-33,0c0,2.9-0.3,5.5,0,8
                        c4.6,32,0.4,63.8-2.6,95.7c-6.5,69.5-21.1,137.5-37.1,205.2c-1,4.1-2.2,8.6-4.6,12c-4.1,5.6-4.8,11.3-4,17.9
                        c4.4,35.4,6.9,71,5.7,106.7c-2.3,69.6-15.6,136.8-43.3,200.9c-3.5,8.1-4.8,16.5-4.5,25.3c0.9,33.1,5.9,65.7,12.9,97.9
                        c11.4,52.2,7.2,103.2-9.5,153.6c-7.9,23.7-15.4,47.5-23.7,71c-12.7,35.9-14.4,72.1-6.2,109.2c4.1,18.5,7.4,37.2,10.7,55.9
                        c1,5.5,1.3,11.3,0.9,16.8c-1.8,21.2-4.7,42.4-5.9,63.6c-1.3,22.5-6.1,44.1-12.5,65.5c-4.1,13.5-12.8,22.3-24.9,28.3
                        c-3,1.5-6.1,2.7-9.2,3.8c-14.4,4.9-27.2,12.4-38.8,22.1c-12,10.2-25.1,18.7-40.2,23.7c-19.5,6.5-39.4,7.1-59,1.3
                        c-15.9-4.7-31.6-10.5-46.9-16.9c-18.4-7.7-23-20.8-14-38.9c8.1-16.4,20.5-29.6,33.9-41.6c7.2-6.4,14.9-12.3,22.9-17.7
                        c18.9-12.8,35.5-28.1,49.9-45.8c4-4.9,8.2-9.6,12.7-14.1c6.7-6.7,10-14.9,11.3-24c3.6-24,6.8-48.1,10.5-72.2c1-6.5,2.8-13,5-19.2
                        c1.8-5.1,1.8-9.5-0.6-14.3c-22.1-44.7-32.7-92.7-40.3-141.6c-3.8-24.7-7.1-49.4-10-74.2c-3.6-30.3-2.2-60.6,0.1-90.8
                        c3.7-48.8,13.4-96.2,33.8-141.1c5.3-11.8,7.7-24,7.1-36.9c-0.5-11-0.5-22-0.7-33c-0.5-26.3-0.6-52.7-1.6-79
                        c-0.3-8.9-1.8-18-4.2-26.5c-13-44.4-27.1-88.5-39.6-133c-6-21.4-10-43.4-13.9-65.4c-4.7-26.9-9.9-53.9-9.1-81.3
                        c0.6-19.9,2.1-39.9,4.1-59.8c3.2-30.8,6.9-61.6,11-92.3c5.1-38.7,15.4-76.3,25.6-113.9c10.2-37.3,21.4-74.2,35.6-110.1
                        c2.4-6.1,3.1-12,2-18.5c-5.7-31.5-0.4-62.4,6.7-93c2.1-9.1,4.4-18.1,6.8-27.1c1.4-5.5,0.8-10.2-3.2-14.8
                        c-8.9-10.5-13-23.3-14.3-36.7c-3.8-39.1-7-78.3-10.4-117.5c-0.3-3.9-0.9-7.8-1.4-11.7C509.7,951.8,508.9,951.7,508.2,951.5z
                        M772.1,832.2c2.5,2.5,5.1,4.9,7.4,7.6c11.1,13.5,24.7,23.3,41.8,27.6c14.3,3.6,28.9,5,43.6,3.2c6.5-0.8,12.9-1.7,19.4-2.6
                        c-4.6,3-9.6,4.6-14.5,6.3c-9.6,3.5-12.4,8-11.5,18.3c0.1,1.7,0.5,3.3,0.8,4.9c6.3,31,12.5,62,19.1,92.9c2.8,13.2,8.6,25.2,17.6,35.4
                        c7.3,8.2,14.7,16.4,22.3,24.3c14.6,15,26.4,31.7,32.4,52c0.9,3,2.6,5.8,4.3,9.2c5.9-4.3,9.2-9.3,11-15c1.9-6,3.2-12.2,4-18.5
                        c1.8-14.5,2.6-29.2,4.7-43.7c7.3-51.4,7.1-103,3-154.6c-0.8-9.6-1.5-19.3-2.9-28.8c-1.5-10.1-4.7-12.1-14.3-9.2
                        c-7.6,2.3-15,5.4-22.6,8c-6.6,2.3-13.2,4.4-19.7,6.6c0,0,0.2,0.2,0.2,0.2c2.5-1.9,4.7-4.2,7.4-5.6c20-10.9,37.9-24.6,54.7-39.9
                        c7.8-7.1,9.9-15.6,6.3-25.3c-3.2-8.4-6.2-16.9-10.6-24.7c-16.4-29.3-32.2-58.9-44.3-90.3c-6.4-16.5-18-28.6-34.9-35
                        c-6.7-2.5-13.5-3.6-20.4-1.2c-27.3,9.6-54.6,19-81.7,29c-19.2,7.1-29.8,21.6-32.9,41.6c-4,25.7-5.1,51.6-4.2,77.6
                        c0.5,15.6,4.8,30.1,12.4,43.7c1.1,2,1.7,4.2,2.5,6.3c-4.4-5.8-8.7-11.6-13.2-17.3c-5.7-7.1-9.8-7.4-15.1-0.3
                        c-6.2,8.2-11.6,17-17.2,25.6c-2.7,4.2-3.1,8.9-0.3,13.2c5.8,8.9,11.5,17.9,17.9,26.4c4.7,6.2,10,6.2,14.8,0
                        c6.4-8.4,12.3-17.4,18.1-26.2c2.6-4,2.2-8-0.4-12.1C775.1,838.8,773.7,835.4,772.1,832.2z M900,2643.9c-0.7,1.8-1.3,3.7-2.3,5.3
                        c-5.7,9-8.2,18.8-7.8,29.4c1.2,29,4.3,57.7,11.3,86c4,16,13.1,26.5,28.9,31.3c14.5,4.5,27.4,12,39.3,21.4c5.5,4.3,10.7,9,16.6,12.9
                        c18.5,12.2,38.3,19.6,61.2,15.7c21.9-3.7,42.5-10.9,62.3-20.7c7.8-3.9,9.5-8.1,6.3-16.4c-2.4-6.1-5.7-12.1-9.6-17.4
                        c-11.9-16.2-26.4-30-43.3-41c-19.1-12.5-35.9-27.6-50.5-45.1c-6.2-7.4-12.7-14.5-19.3-21.6c-4.6-5-8-10.5-9-17.4
                        c-4-28-8.1-56-12.2-84c-0.3-2.3-0.5-4.8-1.5-6.8c-1.2-2.5-3-6-5.1-6.5c-2-0.5-4.8,2.3-7.1,4c-1,0.7-1.4,2.2-2.1,3.3
                        c-7.9,11.2-15.1,22.9-23.8,33.3c-10.2,12.2-21.7,23.2-32.6,34.7c1.5-2.5,2.8-5.2,4.6-7.5c8-10.2,16-20.5,24.4-30.4
                        c31-36.5,51-78.4,63.7-124.3c10.4-37.7,17-76,21.5-114.7c2.8-24.1,5.4-48.4,5.8-72.6c0.4-21.6-1.7-43.2-3.6-64.8
                        c-3.9-44.8-14.6-87.9-33.5-128.9c-10.2-22.2-22.4-43.1-36.6-62.9c-2.7-3.8-5.2-7.9-11.5-6.4c0,2.4-0.3,4.7,0,6.9
                        c1.5,10.2,3.1,20.4,4.8,30.6c5.9,35.2,10.1,70.6,8.7,106.4c-0.6,16-1.5,31.9-2.2,47.9c-2.6,62.2-5.2,124.5-7.9,186.7
                        c-0.7,15.3-1.5,30.5-2.3,45.8c-2.1-12.3-2.6-24.6-3.7-36.9c-2.3-24.9-3.7-49.9-7.7-74.5c-4.9-29.9-11.6-59.4-18.3-89
                        c-8.7-38.3-18.1-76.4-27.2-114.6c-0.9-3.6-0.8-7.9-5.4-10.1c-0.6,1.2-1.3,2-1.4,2.8c-0.4,3-0.7,6-0.8,8.9
                        c-1.1,29.4-5,58.3-10.6,87.2c-3.5,18.3-6.5,36.8-8.3,55.3c-3.3,33.7,2.7,66.4,12.9,98.4c9.6,30.1,20,60,29.4,90.2
                        c10.7,34.5,11.1,69.3,2.4,104.5c-4.5,18.4-7.4,37.2-10.8,55.9c-1.1,6.1-1.7,12.4,3.2,17.8C893.2,2649,896.6,2646.5,900,2643.9z
                        M608.1,2645.9c3.5,1.8,7,3.6,11.6,6c0-7.2,0.7-12.6-0.1-17.7c-3.3-19.7-6.6-39.4-10.9-58.8c-7.4-33.5-7.8-66.7,2.4-99.6
                        c9-28.9,18.6-57.6,28-86.3c16-48.8,19.9-98.2,8.8-148.6c-7.2-32.6-13.1-65.3-12.5-98.8c0.1-3.3-1.3-6.6-2-9.9
                        c-0.8,0-1.6-0.1-2.3-0.1c-5.6,8.9-5.9,20-10.8,29.3c0.5-5.3,1.6-10.4,2.7-15.5c5.8-25.4,10.4-50.9,8.4-77.1
                        c-1.1-14.6-1.7-29.3-3.8-43.8c-2.4-16.8-6.2-33.3-9.4-50c-0.8-4.4-3-7.5-7.7-7.9c-4.3-0.3-6.7,2.4-8.3,5.9c-3,6.7-6.5,13.2-8.7,20.1
                        c-10.4,32.6-20.9,65.2-27.6,98.9c-0.3,1.6-2,2.9-3,4.3l0,0c4-21.3,6.9-42.7,9-64.7c-8,1.5-8,1.5-11.7,6.5
                        c-14,19.5-26.1,40.2-36.2,62c-22.6,48.7-33.4,100.1-36.1,153.5c-1.6,32.7-2.1,65.3,2.3,97.7c3.4,25.1,7.7,50,11.5,75
                        c3.9,25.4,9,50.4,17.5,74.7c17.9,50.7,46.7,94.7,81.7,135c2.8,3.2,5,6.8,7.5,10.3c-1.1-0.6-2.4-0.9-3.2-1.7
                        c-7.3-7.7-14.6-15.5-21.9-23.3c-12-13-23.4-26.4-31.8-42.1c-2.5-4.6-4.6-10.9-11.1-9.3c-5.9,1.4-5.3,7.8-6,12.6
                        c-4.1,27.3-8.2,54.6-12,82c-1.1,8-4.6,14.4-9.9,20.2c-6.5,7.1-13.1,14.2-19.2,21.6c-14.1,17.1-30.4,31.7-49,43.7
                        c-18.1,11.7-33.1,26.7-46,43.9c-3,3.9-5.4,8.4-7.3,13c-4.2,10.2-2.1,15.7,8.2,19.7c15.5,6,31.1,11.9,47,16.7
                        c14.2,4.2,28.9,4.2,43.5,1.2c12.7-2.6,23.7-8.6,33.7-16.5c18.9-14.9,38.7-28.2,62-35.6c6-1.9,10.4-6,13.2-11.5
                        c2.2-4.4,4.1-9.1,5.5-13.8c8-26.3,9.7-53.6,11.9-80.8c1.1-13.7,0.1-26.5-8-38.1C607.7,2647.4,608.1,2646.5,608.1,2645.9z
                        M1049.8,977.9c1.5,3.2,3.2,6.3,4.3,9.6c10.6,31.7,23.4,62.3,43.7,89.3c0.9,1.2,1.4,2.6,2.6,4.8c-1.7-0.6-2.4-0.7-2.7-1
                        c-4.6-5.3-9-10.7-13.6-16c-5-5.8-9.9-11.8-15.3-17.2c-7-7.1-13-5.6-16.4,3.9c-1.3,3.7-2,7.7-2.5,11.7c-1.6,12.6,1.3,24.4,5.6,36.2
                        c4.5,12.2,9.3,24.3,12.8,36.8c15.9,57.2,35.9,112.9,60,167.1c20.2,45.3,42.3,89.7,63.6,134.5c0.6,1.2,1,2.6,1.9,3.5
                        c1.4,1.2,3.6,3.1,4.8,2.7c1.8-0.6,3.5-2.7,4.5-4.5c0.8-1.3,0.7-3.3,0.5-4.9c-2-26.2-2.8-52.6-6.5-78.6c-7.2-50.2-20.1-99.1-37-146.9
                        c-7.4-21-15.6-41.7-23.1-62.8c-1.7-4.8-3.7-10.9-2.2-15.3c2.6-7.6,2.6-15,2.7-22.6c0.1-4.4,0-8.9,0-13.3c0.9-0.2,1.8-0.3,2.8-0.5
                        c6.3,81.3,35.7,154.2,79,222.3c-5.3-5.6-9.4-11.9-14.1-17.7c-4.6-5.7-7.1-13.3-16.3-17.9c0.7,8.2,0.9,14.7,1.9,21.1
                        c2.9,19.7,6.5,39.4,9.1,59.2c2.1,16.2,2.1,32.6,4.5,48.7c3.5,23.2,1.7,45.7-5.3,68.1c-4.9,15.7-2.8,31.1,5.4,45.3
                        c4,6.9,9,13.2,13.4,19.8c11.9,17.7,23.7,35.4,35.5,53.1c6.3,9.4,12.1,19.1,18.7,28.3c2,2.8,5.3,5.5,8.5,6.4c2.7,0.8,6.8-0.3,8.9-2.1
                        c1.7-1.4,2.3-5.6,1.5-8c-1.9-5.6-4.9-10.9-7.3-16.4c-0.8-1.7-1.1-3.7-1.7-5.5c3.1,2.7,4.9,5.9,6.5,9.2c5.9,12.3,11.9,24.6,17.7,36.9
                        c3.4,7.1,8.6,11.8,16.6,12.7c9.9,1.1,14.6-4.2,12-13.8c-0.9-3.4-2.1-6.7-3.2-10c3.2,2.1,5,4.9,6.9,7.6c2.1,3,4.1,6.1,6.6,8.8
                        c5.4,5.8,12,6.7,18,3c5.5-3.5,7.7-9.7,5.2-16.6c-2.3-6.2-5.4-12.1-8.1-18.2c-0.8-1.8-1.2-3.8-2.2-7.4c2.8,2.3,4.1,3.3,5.4,4.3
                        c3.9,3,8.1,5.2,12.9,2.4c4.5-2.6,5.4-7.2,5.6-12.1c0.3-6.7-2.3-12.3-5.5-17.8c-12.9-22.2-25.7-44.4-38.5-66.6
                        c-1.7-3-3.1-6.3-4.6-9.3c7.6-4.5,7.6-4.5,13.9-3.2c9.8,2.1,19.5,4.4,29.3,6.4c11.6,2.4,22.3-2.2,27.6-11.5c3.1-5.4,2.5-9.5-2.8-12.9
                        c-3.9-2.5-8.2-4.4-12.6-6c-9.9-3.7-18.1-9.5-25.4-17.2c-15.7-16.7-32.1-33-47.9-49.6c-7.3-7.7-16-12.9-26.1-16c-3.1-1-6.6-2-8.9-4.1
                        c-12.2-10.9-23-23-30.9-37.5l0.6-0.6c7.3,7.7,14.5,15.5,21.9,23.1c2.7,2.8,5.8,7.1,10,4.2c4.4-3,1-7.3-0.7-10.7
                        c-32-61.2-46.9-127-53.4-195.2c-4.9-52.2-22.7-99.7-53.2-142.4c-2.1-3-4.5-5.9-7.1-8.3c-5.9-5.3-10.1-5.1-15.6,0.4
                        c-1.8,1.8-3.4,3.9-5.9,7c0.4-10.1,4.9-17.1,9.7-23.8c10.3-14.6,15.3-30.9,16.2-48.6c2-42.2-9.4-80.2-35-114.1
                        c-8.6-11.4-17.5-21.7-30.7-27.7c-1.2-0.5-2.1-1.6-3.1-2.4c0.3-0.3,0.5-0.7,0.8-1c6.2,2.9,12.3,5.9,20.1,9.6
                        c0.6-5.3,1.5-9.1,1.3-12.8c-1-23-2.2-45.9-3.3-68.9c-0.9-18.9-5.3-36.8-15.6-53.1c-28.3-44.7-68.2-71.2-121-77.4
                        c-13.7-1.6-26.5-5.3-39-11.3c-28.5-13.7-57.4-26.5-86.2-39.5c-12-5.4-14.5-4.8-20.8,6.5c-6.9,12.3-15.3,23.4-24.9,33.7
                        c-9.5,10.2-18.9,20.6-28.3,31c-1.8,2-3.7,3.9-5,6.2c-3,5.2-0.8,9.5,5.1,9.6c3.3,0.1,6.6-0.7,9.8-1.5c25.1-6.4,50.3-12.8,75.4-19.6
                        c12.3-3.3,24.8-4.7,36.1,1.4c11.9,6.5,20.6,5.7,29.5-4.7c3.8-4.5,9.3-8.1,18.2-7.1c-6.4,4.8-11.4,8.5-16.4,12.3
                        c-6.3,4.9-9.9,11.3-7.8,19.2c2.6,9.9,5.2,20.2,9.7,29.3c14.1,28.3,29.2,56.1,43.9,84.1c4.9,9.3,12.3,15.5,22.3,18.9
                        c6.6,2.3,13.2,4.6,19.8,7c1.9,0.7,3.6,2,7.5,4.1c-17.3,3.5-19.2,14.6-17.5,27.3c2.1,16.1,4.7,32.2,7,48.3c-1.1-2.3-2.7-4.5-3.2-6.9
                        c-2.3-10.7-4.1-21.5-6.3-32.3c-1.1-5.3-2.8-10.4-4.3-16c-5.8,2.9-6.1,7.4-6.9,11.6c-0.7,3.9-1.1,7.9-1.7,11.9
                        c-3.7,26.2-4.5,52.4,0.2,78.5c6.5,35.6,20.9,67.9,39.8,98.4c3,4.8,6.1,9.5,9.4,14.2c2,2.9,4.8,5.2,8.4,3.7c3.9-1.6,3.6-5.5,2.9-8.7
                        c-1.4-6.5-3.3-12.9-4.9-19.3C1054.4,997.9,1052.1,987.9,1049.8,977.9z M471.8,915.8c0.2-2.7,0-5.4,0.5-8
                        c3.5-17.3,7.6-34.4,10.7-51.8c2.2-12.7,3.7-25.7,4.5-38.6c0.7-10.8-2.6-14.7-12.8-18.8c-1.2-0.5-2.4-0.9-4.8-1.8
                        c5.3-2.1,9.5-3.9,13.8-5.6c23.5-9.1,23.5-9.1,38.4-26.6c-1.8,5.9-4.3,11.5-6.7,17.1c-5.6,13.5-4.2,20.6,6.6,30.2
                        c15.9,14.2,32.7,27.4,51.5,37.7c1.7,0.9,3.2,2.1,4.8,3.2c-4,0-7.3-1.3-10.7-2.6c-8.4-3.2-16.7-6.7-25.2-9.4
                        c-7-2.2-10.6,0.2-12.5,7.4c-0.4,1.6-0.8,3.3-1,4.9c-3.4,28.8-6.6,57.6-5.5,86.7c1,25.6,3,51.2,5.1,76.8c1.9,22.9,4.2,45.8,7,68.6
                        c0.8,6.4,3.9,12.6,6.4,18.7c0.8,1.8,3.5,4.2,5,4c2.2-0.4,4.3-2.5,5.8-4.5c1.1-1.5,1.2-3.8,1.8-5.6c4.9-14,12-26.7,22-37.7
                        c5-5.4,9.8-10.9,15.1-15.9c21.8-20.7,35.9-45.2,38.8-75.5c0.1-0.7,0.4-1.3,0.5-1.9c4.7-23.5,9.6-46.9,14.1-70.5
                        c2.7-14.3-0.2-18.5-14-22.7c-3.9-1.2-7.8-2.6-11.7-3.9c1.7-1.4,3-1.7,4.3-1.4c13.5,3.1,27.1,2.9,40.7,1.8
                        c39.4-3.3,74.2-38.5,77.5-77.9c2.2-26.4,0.5-52.6-1.9-78.8c-2.3-25.9-15.3-43.7-39.4-52.7c-24-8.9-48.3-17.2-72.6-25
                        c-6.1-2-13.3-2.3-19.6-1.2c-14.2,2.5-24.7,11-32.5,23c-1.9,2.9-4.1,5.6-6.2,8.4l0.2,0.2c0.4-2.2,0.6-4.5,1.2-6.7
                        c3.9-13.1,2.7-17.1-8.2-25.9c-1.8-1.5-3.7-2.8-5.5-4.3c-1.5-1.2-2.9-2.5-6-5.2c10,2,15.1,7.4,19.9,12.8c3.5,4,6.9,4.5,11.4,1.9
                        c17.3-10,35.3-9.5,54.1-4.1c22.7,6.5,45.7,12,68.5,18.2c5.5,1.5,10.3,1.3,14.9-2.4c0.5-5.4-2.4-9.1-5.7-12.5
                        c-4.4-4.6-8.9-9-13.2-13.7c-13.4-14.4-26.9-28.7-36.9-45.8c-10.4-17.5-10.4-17.5-29.3-8.9c-30.6,13.9-61.2,27.8-91.7,41.9
                        c-8.3,3.8-16.8,5.9-25.9,6.9c-25.6,2.6-49.5,10.3-70.7,25.2c-31.9,22.3-55.8,50.7-63.4,89.9c-5.8,29.5-3.3,59.8-4.7,89.7
                        c-0.1,2.7,2.4,5.5,4,8.8c-1.8,1.9-4.4,4.4-6.7,7.2c-23.6,28.3-39,60.3-43.3,97.1c-3.5,30.4,0.3,59.2,19.3,84.5
                        c4.4,5.9,7.3,12.5,7.7,22c-2.6-4-4.1-6.5-5.8-8.9c-6.2-8.7-12.1-9.3-19.5-1.3c-4,4.4-7.4,9.4-10.8,14.4
                        c-30.3,44.3-44,94.1-49.7,146.9c-2.6,24.1-6.3,48.2-11.2,71.9c-8,38.6-22.2,75.2-40,110.4c-1.9,3.7-4.9,7.5-0.1,12.8
                        c2.8-1.7,6.2-3,8.5-5.3c7-7,13.7-14.5,20.4-21.8c0.3,0.2,0.5,0.5,0.8,0.7c-8.1,10.5-15.5,21.5-24.4,31.3c-4.3,4.7-10.8,8.1-17,9.9
                        c-8.8,2.5-15.5,7.3-21.7,13.6c-14.6,15-29.6,29.8-44,45.1c-10.5,11.1-22,20.3-36.6,25.3c-1.9,0.6-3.7,1.5-5.4,2.5
                        c-6.5,3.9-7.5,8.7-3.2,15c5.6,8.2,13.6,11.8,23.3,10.2c11.2-1.8,22.2-4.3,33.3-6.6c5-1,9.9-1.8,13.9,4c-1.7,3-3.4,6.2-5.2,9.3
                        c-12.2,21-24.5,42-36.7,63c-2,3.4-4.5,6.9-5.4,10.6c-1.2,4.7-2.1,9.9-1.3,14.6c1.6,9.3,10,11.8,17,5.7c2.1-1.8,4.1-3.8,6.1-5.7
                        c-0.1,3.7-1.3,6.6-2.6,9.5c-2.6,5.8-5.6,11.4-7.7,17.3c-2.3,6.7-0.2,12.5,4.9,16c5.2,3.6,11.2,3.7,16.6-0.9
                        c3.5-2.9,6.2-6.9,8.6-10.8c4-6.6,7.5-13.4,11.2-20.2c-1.6,6.9-4.7,13.1-7.4,19.4c-2.1,4.8-2.9,9.7,1,14c4,4.3,9,3.5,14,2.4
                        c7.3-1.6,11-7,14-13.2c8-16.4,16-32.9,24.1-49.3c0.9-1.9,2.3-3.6,3.5-5.5c-2.6,9.2-6.7,17.5-10.6,26c-1.2,2.7-2.4,5.6-2.8,8.5
                        c-0.7,5.5,3.4,9.5,8.5,7.8c3.8-1.3,7.9-3.9,10.3-7.1c7.4-9.8,14.2-20.1,21.1-30.3c13.2-19.6,25.8-39.6,39.7-58.8
                        c13.9-19.2,17.8-39.3,11.3-62.2c-4.7-16.3-7.9-33.3-6.2-50.3c3-30.8,6.8-61.6,10.6-92.3c1.5-11.9,4.2-23.6,6.1-35.4
                        c0.7-4.3,2-9-2.7-13.4c-6.7,9.2-13,17.7-19.3,26.3c-0.6-0.3-1.2-0.7-1.8-1c38.3-63,66.3-129.7,74-203.9c1.6,6.7,0.8,13.8,3.2,19.5
                        c4.4,10.5,1.2,19.4-2.5,28.7c-1.2,3.1-2.3,6.3-3.4,9.4c-14.2,39.8-28.7,79.5-39.5,120.4c-11.2,42.7-17.2,86.1-21.2,129.9
                        c-0.7,7.6-0.4,15.3-0.2,22.9c0.1,3.9-0.2,9.1,4.6,10c5.1,1,5.9-4.4,7.5-7.8c6.5-13.8,12.9-27.8,19.4-41.6
                        c15.1-31.9,31.3-63.3,45.3-95.6c18.9-43.7,37-87.7,49.9-133.7c6.9-24.7,13.3-49.4,23.8-73c5.9-13.3,4.3-27.5,0.6-41.2
                        c-2.1-7.7-9.4-9.7-15.7-4.7c-2.6,2-4.7,4.7-6.8,7.3c-7.8,9.3-15.5,18.7-23.3,28.1c-0.4-2.8,0.7-4.6,1.8-6.4
                        c5.5-9.1,11.4-18,16.5-27.3c15.4-27.8,25.5-57.8,34.3-88.2c1-3.3,2.7-6.5,4.1-9.7c-0.4,2.6-0.6,5.3-1.3,7.8
                        c-5.8,21.5-11.6,43-17.4,64.5c-0.6,2.2-1.8,4.6-1.6,6.7c0.2,2.4,1.3,5.8,3,6.5c1.8,0.8,5-0.6,7-2c2.1-1.5,3.5-4,4.9-6.2
                        c26.7-39.9,44.5-83.2,48.4-131.5c1.8-22.4,0.1-44.6-4.3-66.5c-0.8-3.9-0.9-8.6-6.3-11.4c-1.4,3.9-3,7.2-3.7,10.7
                        c-2.4,12-4.2,24.2-6.9,36.2C480.2,881.7,475.9,898.8,471.8,915.8z M809.6,1199.6c-0.2-0.3-0.4-0.7-0.7-1c9.3-2.3,18.7-4.4,28-6.8
                        c8.6-2.3,15.8-6.8,20.5-14.8c11-19,17.7-39.4,20.5-61.1c1.5-11.2-2.4-19.1-12.3-24.9c-29.3-17.2-60.9-22.4-94.3-19.2
                        c-7.7,0.7-12.5,4.7-13.8,12.2c-1.6,9.1-3.5,18.3-3.9,27.6c-0.6,16.3-0.2,32.7-0.1,49c0.1,10,0.1,20,0.7,30
                        c0.8,13.9,7.5,19.1,21.1,16.8c3-0.5,6-1,9-1.5c-0.7,2.3-1.8,3.1-3,3.4c-15.5,3.8-22.3,14.2-23.4,29.6c-1.1,15.9-2.9,31.8-4.4,47.7
                        c-0.3,2.7-0.9,5.5-1.3,8.2c-2.4-10.1-3.3-20-4.2-29.9c-1.1-11.6-2-23.2-3.4-34.8c-1.1-9-6.3-15.8-15.1-18.1
                        c-23.8-6.3-47.6-12.2-71.6-17.8c-6.1-1.4-11.3-3.3-15.6-8c-10.6-11.9-18.2-25.3-22.4-40.8c-5.8-21.6-5.6-43.6-2.9-65.5
                        c1.6-12.9,4.5-25.6,6.6-38.4c0.7-4.1,2.4-8.5-1.3-12c-6.1-1.1-9.6,2.7-13,6.3c-12.6,13.3-25.3,26.6-37.8,39.9
                        c-15.2,16.2-17.9,32.5-7.6,52.1c6.5,12.4,13.7,24.4,21.7,35.9c12.2,17.5,28.6,29.6,49.6,34.8c1.2,0.3,2.3,1.2,4,2
                        c-1.5,26.4,2.3,52.4,6.3,78.4c4,26.3,10.9,51.8,19.3,78.6c-1.9-1.5-2.5-1.7-2.7-2.2c-9.1-23.6-18.1-47.3-22-72.5
                        c-3.3-21.4-5.3-43-7.7-64.5c-1.1-10-5.8-17.7-14.6-22.5c-24-13-40.4-33.2-53.6-56.5c-0.8-1.4-1.4-3.1-2.4-4.4c-3.5-4.7-8.5-4.3-11,1
                        c-1.1,2.4-1.7,5.1-2.2,7.7c-3.3,17.3-6.8,34.6-9.8,52c-5.5,31.8-1.6,62.2,12.4,91.5c24.8,51.8,60.1,94.9,105.9,129.4
                        c6.3,4.8,13.5,8.5,20.7,11.9c4.3,2,9.4,2.5,14.5,3.8c0.9-3.5,1.5-5.8,2.4-9.2c14.6,19.6,31.6,33.7,56,34
                        c24.2,0.3,41.8-13.1,56.6-30.6c7.8,6.5,8.2,6.9,16.4,2.7c8.6-4.4,17.3-8.8,25-14.5c47.7-35.3,82.8-80.7,107-134.6
                        c5.4-11.9,8.7-24.4,10.3-37.4c4.5-36.2-3.3-70.9-13.1-105.3c-0.9-3.1-3.5-5.7-6-9.6c-2.5,3.6-4.1,5.7-5.3,7.9
                        c-13.5,25.6-31.2,47.1-56.9,61.4c-7.7,4.3-11.9,11.5-13,20.3c-0.6,4.3-1.3,8.6-1.4,12.9c-0.7,42.6-13,82.6-27.3,122.2
                        c-0.5,1.4-1.4,2.6-2.1,3.9c13.4-51.6,24.8-103.3,24.3-157.3c2.5-0.9,4.9-1.9,7.4-2.7c16.4-5,29.8-14.5,41.4-27
                        c12.2-13.1,20.7-28.4,27.7-44.8c6.8-15.9,4.8-30.4-5.1-44.3c-12.2-17.3-26.5-32.5-43.1-45.6c-3.7-3-8.3-4.8-14.9-8.5
                        c1,7.8,1.3,13.1,2.4,18.1c5.8,25.8,9.2,51.8,6.9,78.3c-2.1,23.1-9.1,44.4-25.3,61.6c-3.7,3.9-8.8,7.9-13.9,9
                        C835.9,1196.3,822.7,1197.6,809.6,1199.6z M983,1943.7c0.7-0.3,1.4-0.5,2.1-0.8c1.9-6.9,4-13.8,5.8-20.8
                        c19.2-74.1,40.9-147.5,50.2-223.9c3.6-29.9,6.9-59.5,3-89.6c-2.1-16.5-4.5-33-6.1-49.6c-4-42.8-9.8-85.4-20.3-127.1
                        c-12.4-49.4-25.4-98.6-38.2-147.8c-0.6-2.3-2.4-4.3-3.6-6.3c-6.2,1.5-6.2,5.5-6.3,9.6c-0.8,31.3-1.8,62.6-2.6,93.9
                        c-1.2,48.8,5,97.1,9.4,145.5c4.7,52.4,9.9,104.8,14.4,157.2c3,34.9-0.8,69.7-2.3,104.6c-0.7-5.7-0.6-11.3-0.7-17
                        c-0.7-34.6-0.8-69.3-2.2-103.9c-1-23.3-3.6-46.5-5.8-69.7c-3.6-39.1-7.7-78.2-11.1-117.4c-2-22.9-4-45.8-4.8-68.8
                        c-0.9-24.3-0.2-48.6-0.5-73c-0.1-4.2-1-8.6-2.7-12.4c-1.7-3.8-5.5-4.1-8.1-0.8c-2.2,2.8-3.5,6.3-4.6,9.7c-2.4,7.2-4.4,14.5-6.6,21.8
                        c-0.1-7.9,1.2-15.4,2.4-22.9c0.5-3,0.7-6.4-2.7-7.8c-3.7-1.4-5.4,1.6-7,4.1c-4.4,7.1-8.4,14.4-13.1,21.3
                        c-30.7,44.4-69.8,79.5-117.7,104.4c-15.3,7.9-23.4,19.1-23.9,36c-0.1,3.3-0.6,6.7-0.4,10c1.8,27.2,2.5,54.6,5.9,81.7
                        c4.6,36.3,10.7,72.4,17.3,108.5c5.2,28.5,11.9,56.7,18,85c0.9,4.1,2.3,8.2,7.3,9.1c14.1-57.8,27.6-115,42.1-172
                        c21.8-85.9,43.3-157.2,50.4-169.3c-0.4,2.8-0.4,4.4-0.8,5.9c-13.5,54.2-27,108.5-40.5,162.7c-7.7,31.1-14.1,62.4-14.8,94.5
                        c-1.3,56.8,4.3,113.1,14.4,169c5.7,31.6,14.6,62.2,30.3,90.5c17.5,31.5,38.4,60.8,59.1,90.2c1.9,2.7,4.8,4.7,8.9,8.5
                        c0.4-7.5,0.8-12.4,0.9-17.4c0.8-35.3,1.5-70.6,2.3-105.9c0-1.1,0.5-2.3,1-4C981.9,1941.4,982.5,1942.5,983,1943.7z M529.9,2064.1
                        c0.9,0.2,1.8,0.5,2.7,0.7c3-3.5,6.3-6.8,8.9-10.6c15.3-22.6,30.6-45.2,45.5-68.1c15.9-24.5,27.6-51,34.2-79.4
                        c16.1-69.1,23.2-139.3,20.3-210.2c-0.7-17.8-3.5-35.9-7.6-53.3c-15.7-66-32.4-131.8-48.6-197.7c-0.6-2.3-0.8-4.6-1.1-6.9
                        c38.2,114.5,67.9,231,93.2,348.8c5.9-1.4,7.4-5.1,8.4-9c1.2-4.5,2-9.1,3.1-13.6c18.1-74.2,31.2-149.2,36.5-225.5
                        c1.1-15.3,1.3-30.6,1.2-45.9c-0.1-15.7-7.9-27.1-21.2-35.1c-4-2.4-8.1-4.6-12.1-6.9c-48.8-28.2-89.1-65.2-117.5-114.4
                        c-1.5-2.6-3.2-5.1-5.2-7.3c-2.1-2.4-4.8-3.6-8.8-0.1c1.4,9.4,2.8,19.5,4.3,29.6c-3.7-7.4-5.7-15-7.7-22.7c-0.8-2.9-1.1-6.2-2.7-8.5
                        c-1.5-2.1-4.7-4.6-6.7-4.3c-2,0.3-4.2,3.7-5.1,6.1c-1,2.7-0.6,5.9-0.6,8.9c0,14.7,0.5,29.3-0.1,44c-1.2,28.6-2.7,57.2-4.7,85.8
                        c-1.5,20.2-4.3,40.4-6.1,60.6c-3.3,35.5-6.5,71-9.3,106.5c-2,25.6-4.4,51.2-4.5,76.8c-0.2,35,1.7,69.9,2.5,104.9
                        c0.3,16.3,0.1,32.5,0.1,48.8c-0.7-5.9-1-11.8-1.3-17.8c-1.8-35.9-3.9-71.8-5.4-107.8c-1.7-40.4,2.1-80.5,6.2-120.7
                        c3.1-30.8,6.1-61.6,9.2-92.5c2.1-20.5,5.1-41,6.3-61.6c1.8-33.6,3.1-67.2,3.2-100.9c0.1-26.3-1.9-52.6-3.1-78.9
                        c-0.1-2.8-1.3-5.5-1.9-8.1c-7,0.5-7.2,5.2-8.2,9.3c-10.1,39-20.2,78-30.4,116.9c-8.1,30.9-16,61.9-20.2,93.6
                        c-3.3,25.1-6.3,50.2-9.1,75.4c-2.3,20.5-4.7,41.1-5.8,61.6c-2,39.6,6.1,78.3,13.2,116.9c6.6,35.9,16.5,71.3,25.2,106.8
                        c6.3,25.5,13,50.9,19.6,76.4c0.7,2.8,0.5,6.6,5.7,7.8c0-11.3,0-21.9,0-32.5C529.3,1961.4,526.8,2012.9,529.9,2064.1z M869.9,305.1
                        c0.5-0.1,0.9-0.1,1.4-0.2c-1.8-12.8-2.8-25.8-5.7-38.4c-10.7-45.5-49.2-80.5-95.8-88.1c-46-7.5-91.8,13.6-116.3,53.8
                        c-14.6,24-21.4,50.2-20.2,78.2c0.6,13.5-3.8,22.9-16.7,28.6c-11,4.9-14.5,13.7-10,24.8c5.8,14.2,12.6,27.9,19.2,41.7
                        c2.3,4.7,6.1,7.7,11.6,8.4c9.1,1.3,9.8,2.2,10.6,11c1.1,11.6,2.4,23.2,3.1,34.8c0.8,13.5,6.8,23.8,18.7,29.8
                        c16.9,8.5,34,16.9,51.6,23.8c11.8,4.7,24.6,5.2,37.4,3.4c28.9-4,54.9-15.6,79.8-30.2c8.4-4.9,12.8-12.3,13.6-22
                        c1.2-13.6,2.5-27.2,3.8-40.8c0.8-7.9,1.5-8.8,9.5-9.9c7.3-1,12.4-5,15.3-11.4c5.7-12.8,11.2-25.6,16.2-38.6c3.2-8.3,1-15.8-6.1-21.4
                        c-4.2-3.3-8.5-6.4-12.9-9.4c-5.4-3.6-8.4-8.3-8-15C870.1,313.7,869.9,309.4,869.9,305.1z M757.5,624.5c0.2,4.4,0.2,7.8,0.6,11
                        c1,8.7,4.9,11.2,12.9,7c6.1-3.2,12-7.2,17.1-11.9c19-17.8,34.9-38.4,49.5-59.9c5.8-8.6,8-18.3,7.3-28.5c-0.6-8.9-1.6-17.9-2.9-26.8
                        c-0.6-3.9-2-7.7-3.8-11.3c-3-6.2-5.2-7-11.9-4.7c-12.9,4.5-25.5,9.7-35.9,19c-1.7,1.5-4.6,2.5-7,2.5c-20.6,0.4-41.2,0.6-61.8,0.7
                        c-2.7,0-6.3-0.6-7.9-2.3c-12.3-12.8-28.8-17.1-44.9-23.5c-1,1.9-2.1,3.3-2.4,4.8c-1.9,9.1-3.8,18.2-5.3,27.3
                        c-3,18.3-1.5,35.8,10.4,51.2c1.6,2.1,2.8,4.5,4.2,6.8c14.4,23,34.1,40.7,56.2,56c8.2,5.6,13.1,3.2,14.1-6.5c0.4-3.5,0.4-7.1,0.6-11
                        C750.3,624.5,753.5,624.5,757.5,624.5z M849.4,1728.3c-2.8,4.8-4.8,7.4-5.8,10.3c-2.1,6.6-3.9,13.4-5.5,20.2
                        c-9.9,42.4-13.7,85.4-14.6,128.8c-1.2,57.3,5.2,113.7,22.6,168.6c5,15.9,10.7,31.5,16.2,47.2c1.4,4,2.7,8.7,9.6,5.6
                        c0-3.8,0-7.8,0-11.8c0-16-0.3-32,0.1-48c0.5-22.7,2.1-45.4,8.6-67.2c6.2-20.9,5.7-40.9,0.3-61.9c-12.8-49.8-20.8-100.4-23.4-151.8
                        c-0.5-10.3-2.1-20.5-3.6-30.7C853.6,1735,851.6,1732.7,849.4,1728.3z M657.1,1728.3c-2.7,4.8-4.8,7.4-5.5,10.2
                        c-1,4.5-1.1,9.2-1.5,13.9c-2.4,24.9-3.7,49.9-7.4,74.6c-4.3,28.9-10.5,57.6-16.1,86.3c-6.3,32.2-11.6,33.2-0.7,70.5
                        c0.1,0.3,0.1,0.7,0.1,1c8.7,38.4,9.2,77.3,7.9,116.3c-0.1,2-0.5,4.1,0,5.9c0.3,1.3,1.9,2.8,3.1,3.1c1.3,0.3,3.2-0.4,4.4-1.3
                        c0.9-0.7,1.3-2.3,1.8-3.5c23-55.6,35.3-113.7,38.5-173.7c2.1-39,1.3-77.9-3.9-116.7c-3.4-25.4-7.9-50.6-15.2-75.3
                        C661.8,1736.4,659.7,1733.5,657.1,1728.3z M930.7,2334.2c4.9-2.3,5.7-6.7,6-10.9c0.8-9.6,1.3-19.3,2-28.9
                        c3.4-50.5,7.1-101,5.5-151.6c-0.5-15.6-1.4-31.3-3.8-46.7c-5.8-36.9-17.4-72.4-29.2-107.7c-2-5.9-5-11.6-8.1-17.1
                        c-3.2-5.5-8.1-6-12-0.9c-2.3,3-3.8,7-4.7,10.7c-8.7,33.4-11.4,67.6-11.9,101.9c-0.2,12.9,1.1,26,3.7,38.6
                        c6.7,32.2,14.7,64.2,22,96.2c8,35,15.9,70,23.7,105C924.9,2327.5,925.8,2332,930.7,2334.2z M749.6,1146c0.2,0,0.4,0,0.6,0
                        c0.2-4,0.5-8,0.6-12c0.2-17,0.3-34-4.6-50.5c-2.7-9-5.6-11.3-15-11.5c-11-0.1-22-0.2-33,0.2c-22.8,1-43.6,8.3-62.8,20.6
                        c-7.9,5.1-11.1,11.6-9.7,21.1c3,20.2,8.4,39.6,18,57.8c6,11.3,14.8,18.4,27.3,21.4c15.8,3.8,31.5,8.2,47.3,12.1
                        c4.5,1.1,9.1,2.1,13.7,2.4c10,0.8,14.2-2.3,16.3-12c0.9-4.2,1.2-8.6,1.2-12.9C749.7,1170.6,749.6,1158.3,749.6,1146z M754.2,1022.4
                        c-0.1,0-0.3,0-0.4,0c0,9.3-0.3,18.6,0.1,27.9c0.4,11.9,3,14.7,14.9,15.1c12,0.5,24,0.1,35.9,0.4c20.7,0.5,40,5.7,57.8,16.5
                        c2.5,1.5,5.2,3.2,8,3.9c4.8,1.2,8.6-1.6,9.3-6.5c0.3-2.3,0.2-4.7-0.1-7c-2-15.2-4-30.3-6.2-45.5c-1.4-9.5-5.2-17.8-12.5-24.4
                        c-25.3-22.7-55-32.9-88.9-31c-11.8,0.7-16.9,5.5-17.8,17.4C753.6,1000.5,754.2,1011.5,754.2,1022.4z M871.3,996.3
                        c0-5.9,0.8-10.3-0.1-14.3c-6.2-27.3-12.6-54.5-19.3-81.6c-1.9-7.7-6.6-14.1-14-17.4c-15.4-7.1-31-13.8-46.7-20.2
                        c-6.5-2.7-10.6-1.2-14.7,4.6c-5.4,7.6-11.3,15-15.4,23.3c-3.9,7.9-7.6,16.7-8.4,25.3c-1.1,11.8,0.5,23.9,1.2,35.8
                        c0.3,5.5,3.6,9.3,8.9,10.8c2.9,0.8,5.8,1.3,8.8,1.7c31.1,3.6,60.8,11.5,88.2,27.1C862.8,992.8,865.9,993.9,871.3,996.3z M717,1064.9
                        c0,0.2,0,0.5,0.1,0.7c6.3,0,12.6,0.2,19-0.1c10.2-0.5,12.6-3,13.9-13.1c2.5-19.8,3.5-39.6,0.3-59.6c-2.6-15.9-5.3-19.4-21.2-20.6
                        c-1.3-0.1-2.7-0.1-4-0.1c-31.6-1.1-58.5,10.5-81.9,30.9c-4.1,3.5-7.1,8.8-9.2,13.9c-7.6,18.1-10,37.2-9.4,56.6
                        c0.4,14.9,3.5,16.4,16.7,8.8c11.3-6.5,22.9-12.3,36-13.8C690.5,1067,703.7,1066.1,717,1064.9z M749.9,926.7
                        c4.2-17.6-4.3-34.5-16.4-50.3c-1.8-2.4-3.3-5-5.1-7.4c-5.4-7.6-8.3-9-16.8-6c-11.6,4-23.2,8.3-34.4,13.1
                        c-14.6,6.3-24.6,17.5-28.4,32.8c-6,23.8-10.9,48-16.1,72c-0.9,4.2-1.9,9.7,2.8,11.5c2.9,1.1,7.3-1.4,10.9-2.4c0.9-0.3,1.7-1,2.6-1.5
                        c16.1-8.9,33.1-15.6,51.2-18.9c13.1-2.4,26.2-4.5,39.2-7.3c7.8-1.7,10-4.5,10.3-12.4C750.2,943.2,749.9,936.6,749.9,926.7z
                        M752.3,709.7c2.2-4.3,4-7,5.1-10c4.1-11.3,9.2-22.1,16.7-31.5c1.2-1.5,2.1-3.4,2.8-5.3c1.4-4.3-0.4-7.1-5-7.7
                        c-1.9-0.2-3.9,0.4-5.9,0.4c-8.6,0.2-17.2,0.4-25.7,0.5c-4.5,0.1-9.1,0-14.3,0c0.5,2.5,0.5,4.3,1.2,5.6c7.1,14.6,14.2,29.1,21.4,43.7
                        C749.1,706.5,750.2,707.3,752.3,709.7z"/>
                    <path d="M2407,2148.4c-3.3-27.4-6.7-54.8-9.7-82.2c-1.2-11.2,1.3-21.9,6.9-31.7c6.4-11.2,12.6-11.7,20.1-1.4c3.6,5,7,10.3,11.6,14.9
                        c-1.2-3-2.3-5.9-3.6-8.8c-10.6-23.4-21.7-46.5-31.6-70.1c-16.8-39.8-22.8-81.9-24.6-124.8c-1.3-31.6-3.5-63.2-5.3-94.8
                        c-2.6-44.6-5.7-89.1-7.7-133.7c-0.7-15.2,0.8-30.6,2.4-45.9c1.3-12.3,5.5-14.7,17.7-11.6c7.7,2,15.3,4.8,23,6.7
                        c5.1,1.3,10.5,2.2,15.7,2.3c7.5,0.1,9.7-2.5,8.5-9.9c-1.9-11.5-4.6-22.8-6.8-34.3c-4.9-25.4-5.9-50.9-1.9-76.5c0.2-1.3,0-2.6,0-4.6
                        c-5.5,13.5-7.5,35-5.1,53.8c1.7,13.6,3.3,27.1,4.9,40.7c2.2,19.2-4.1,25.4-22.6,20.4c-16-4.3-31.5-10.6-47.2-16.1
                        c-8.7-3.1-17.3-6.4-27-8.3c1.4,1.5,2.8,3.1,4.2,4.6c12.4,13.1,19.1,29,20.7,46.6c6.4,69,12.6,138,18.4,207c2,23.5,2,47.3,4,70.8
                        c2.8,32.9,6.6,65.6,10,98.4c0.2,2.1,1.1,4.7,2.6,6.1c10.2,9.7,15.3,22.5,21.2,34.7c2.3,4.7,3,11.6,1.3,16.4
                        c-5.9,16.1-13.2,31.8-20.3,48.5c-3.3-1.8-5.8-2.8-7.7-4.3c-6.8-5.4-13.5-10.9-20.1-16.5c-8.6-7.3-8.8-9.4-3.1-19.5
                        c1.4-2.5,2.8-5,2.8-8.5c-6.7,9.1-14.4,17.6-19.9,27.4c-3.3,5.7-4.3,13.3-4.6,20.1c-0.7,19.6-0.3,39.3-0.5,59
                        c-0.1,5.4,1.7,13-5.5,14.1c-6.6,1-8.8-6.3-10.9-11.3c-11.2-28.1-22.3-56.3-30.1-85.6c-13.4-50.2-26.2-100.5-33.9-151.9
                        c-2.4-16.1-4.1-32.4-4.7-48.7c-2.4-60.9-4.7-121.9-6.2-182.8c-0.5-20.9,1.1-42,2.1-62.9c1.3-26.6,3-53.2,4.6-79.8
                        c0.3-5.5-0.3-11.7,6.3-14.6c6.6-2.9,10.6,1.8,15,5.2c11.4,8.9,24.2,14.8,38.5,18.3c-1.3-1-2.5-2-3.8-2.9c-1.4-0.9-2.9-1.6-4.4-2.5
                        c-17.4-9.9-34.4-20.8-47.9-35.7c-8.4-9.3-16-19.6-22.8-30.2c-9.5-14.7-8.4-29.6,1.8-43.9c9.1-12.7,18.5-25.3,27.2-38.3
                        c22.6-34,40.5-70.4,51.9-109.8c2.2-7.5,5-14.3,12.1-18.9c2.1-1.3,3.6-4.7,3.8-7.3c1.4-14.5,7.1-27.7,12.3-41
                        c4.8-12.4,14.7-20.4,26.2-26.4c6.2-3.3,13-2.8,19.6-1.2c8.3,2,16.4,4.6,25.4,5.7c-4.5-3.6-9-7.1-14.7-11.6
                        c10.8-12.5,21.1-25,32-36.9c4-4.4,8.5-2.9,10.9,2.7c0.8,1.8,1.2,3.8,1.6,5.7c3.7,16.9,7.4,33.8,11.2,50.7c0.2,1,0.4,2,0.6,2.9
                        c0.5,4.4,0.9,9-3.5,11.6c-4.6,2.7-8.2,0-11.5-3c-0.5-0.5-1-0.8-2.3-0.7c1.7,2.2,3.4,4.3,5.1,6.5c8.6,10.9,14.7,23.1,17.9,36.5
                        c6.2,25.5,12.1,51.2,17.9,76.8c0.7,3.2,0.7,6.7,0.4,9.9c-0.7,6.8-4.4,8.7-10.2,4.9c-3.5-2.4-6.6-5.6-9.1-9
                        c-4.7-6.4-8.8-13.4-13.3-20c-11.5-16.8-25.9-30.3-45-38.4c-1.5-0.6-3-1-5.2-0.4c1.5,1.2,2.8,2.6,4.4,3.7
                        c35.7,24.1,57.8,58.3,72.8,97.7c3.3,8.7,1.9,12.4-5.9,17.8c-3.6,2.4-7.5,4.3-11.2,6.6c-14.2,8.7-23.3,21.1-26,38c1.7-3,3.3-6,5-8.9
                        c7.7-12.9,17.1-23.8,32.2-28.3c2.2-0.6,4.5-1.3,6.8-1.5c12.4-1.4,18.3,2.6,20.5,14.8c2.5,13.7,4.7,27.6,6.2,41.5
                        c7.1,64.1,5.7,128.4,2.6,192.7c-2.7,56-10.6,111.4-19.7,166.6c-6.6,40.1-14.4,80-19.8,120.2c-2.4,17.6-0.1,35.9,0.2,53.9
                        c0.3,19.6,1.1,39.3,0.7,58.9c-0.2,13-2.4,25.9-8.9,37.6c-1.8,3.3-1.4,6.2,0.1,9.5c2,4.2,3.6,8.6,5.6,12.8
                        c18.4,39,30.7,80.1,41.1,121.8c5.1,20.3,5.5,40.9,2.4,61.5c-3.4,23-5.9,46.3-10.7,69c-4,18.8-11.5,36.8-16.4,55.5
                        c-11.9,44.7-23.6,89.5-34.5,134.4c-7.2,29.6-5.1,59.6,0.2,89.3c0.5,3,0.8,6,1.6,8.8c2.3,8.5,2,16.5-4,23.2
                        c5.2,14.6,11.6,18,26.5,16.9c14.6-1.1,29.3-1.8,43.9-1c22.6,1.2,38.6,13.7,48.8,33.3c10.1,19.6,4.1,42-15.1,54.3
                        c-9.4,6-20.3,9.9-30.7,14.2c-6.4,2.6-13.4,4.1-20,6.2c-11.8,3.8-21.4,10.5-28.1,21.2c-9.8,15.8-24.5,24.4-42.5,26.6
                        c-12.8,1.6-25.9,2.2-38.8,2c-17.1-0.3-23.8-5.8-29.2-21.9c-5.2-15.7-8-31.8-6.2-48.2c2.4-22.4,0.5-44.6-1.8-66.8
                        c-1.4-13.6-2.4-27.2-3.4-40.8c-0.9-12.5,1.7-24.5,6.2-36.1c6.1-15.9,18.7-23.2,35.4-21.2c20.1,2.3,34.2,11.9,40.1,32
                        c1.6,5.3,4.3,10.3,7.1,16.9c1.3-17.2-11.3-38.8-27-47.9c-19-11-36.4-5.3-55.1,7.2c1-7.4,0.7-12.9,2.6-17.4
                        c13.5-32.6,11.9-65.6,4.7-99.1c-6.9-32.2-13.1-64.6-19.9-96.8c-1.3-6.1-2.9-12.6-6.2-17.7c-19.5-30.4-23.9-63.7-20.7-98.8
                        c2.2-23.7,8-46.5,16.1-68.8c12-32.7,15.3-66.7,13.7-101.3c-0.6-12.3-1-24.6-1.4-37c0-1.3,0-2.7,0-4c0.6-12.2,5-15.3,16.8-11.2
                        c11,3.8,19.8,10.4,26.4,20.3c10.4,15.6,15,33.3,19.2,51.1c1.7,7.4,2.8,15,4.4,22.5C2404.5,2146,2406,2147.1,2407,2148.4
                        c-0.3,4-0.5,8-0.9,13.5c1.1-1.9,1.7-2.4,1.6-2.9C2407.5,2155.4,2407.2,2151.9,2407,2148.4z M2409.1,2181c0.3-3.9,0.6-7.8,0.9-11.7
                        c-0.7,0-1.3,0-2,0C2408.3,2173.3,2408.7,2177.1,2409.1,2181c4.2,37.7,6.1,75.6,4.7,113.6c-0.7,18.4-4.6,36.3-11.5,53.4
                        c-6.5,16.2-16.5,29.6-31.4,39c-1.4,0.9-2.5,2.3-3.7,3.4c4.4-0.2,8.1-1.6,11.3-3.7c12.5-8.1,20.7-19.6,25.8-33.4
                        c2.7-7.2,7.1-10.4,14.8-10.9c9.3-0.6,18.5-2.3,27.7-3.6c5.1-0.7,10.2-1.7,16.3-2.7c-0.8,6.5-1.4,12-2.4,19.8
                        c3.6-8.4,5.9-14.9,9.1-20.9c3.1-6,7.1-11.5,11.7-18.8c-6,2.3-9.9,4-13.9,5.3c-6.3,2-12.6,4.4-19.1,5.4c-13.3,2.1-23.7-5.5-26.1-18.7
                        c-1.2-6.5-1.7-13.2-2-19.9c-1.1-34-3.5-67.8-8.2-101.5C2411.6,2184.8,2410,2183,2409.1,2181z M2448.6,2060.5c1,0,2,0.1,3,0.1
                        c1.1-3.6,2.9-7.1,3.1-10.7c1-18.9,2-37.9,2-56.9c0-14.6-1.7-29.3-2.3-43.9c-0.4-9-1.1-18.2,0.3-26.9c6.4-40.4,13.7-80.7,20.4-121.1
                        c8.8-53.2,16.6-106.6,19.5-160.6c3.3-61.3,5.2-122.5-0.5-183.7c-1.7-18.2-4.1-36.4-6.8-54.5c-1.8-12.1-7-15.1-19-12.3
                        c-9.8,2.3-17.5,7.9-23.7,15.7c-8.6,10.9-14.1,23.3-16.7,37c-5.7,30.7-3.3,61,3.1,91.3c1.7,8.1,3.8,16.2,4.9,24.5
                        c1.2,9.4-2.6,14.2-12.1,14.2c-7.9,0-15.8-1.8-23.6-3.4c-6.2-1.3-12-3.9-18.2-5.3c-8.5-1.9-11.1-0.1-12,8.4
                        c-0.8,7.3-1.8,14.6-1.6,21.9c1.1,31.9,2.4,63.9,4.2,95.8c2.9,52.9,6.8,105.7,9.2,158.6c2,45.4,9.5,89.3,29.2,130.6
                        c11.3,23.7,21.9,47.8,32.8,71.7C2445.3,2054.2,2447,2057.3,2448.6,2060.5z M2256.1,1503.1c-0.7,7.9-1.4,14.5-1.8,21.1
                        c-1.3,22.6-3,45.2-3.7,67.8c-1.1,32.3-2.4,64.6-2,96.9c0.5,39,2.8,77.9,4,116.9c1.1,35.6,2.1,71.3,9.9,106.3
                        c8.4,37.7,16.9,75.3,25.9,112.9c7.1,29.5,16,58.4,27.9,86.3c2.4,5.6,5.5,10.8,8.2,16.2c0.7-0.2,1.5-0.4,2.2-0.6
                        c0.3-3.6,0.8-7.1,0.9-10.7c0.3-16,0.5-32,0.7-48c0.1-16,5.1-30,16.2-41.8c12.3-13,20.7-28.4,26.9-45.2c4.4-11.8,5.7-23.9,4.3-36.3
                        c-2.2-18.9-4.8-37.7-6.5-56.6c-2.8-31.2-5.1-62.4-7.3-93.6c-2.5-35.6-3.9-71.2-7-106.7c-3-34.8-7.6-69.5-11-104.3
                        c-2.7-27.6-16-46.5-42.4-56.2c-13.2-4.8-25.6-11.1-36.8-19.8C2262.7,1506,2259.8,1505.1,2256.1,1503.1z M2335.5,1245.4
                        c-10.8-0.4-14,1.7-17.5,11.1c-1.2,3.1-2.4,6.2-3.3,9.4c-7.2,23.3-16.3,45.7-27.6,67.3c-13.8,26.3-30.2,50.9-48.1,74.6
                        c-12.3,16.2-12.7,27.6-2.2,45c9.2,15.3,21.1,28.2,35.3,39.1c38.9,29.9,83.3,48.1,130.4,60.4c1,0.3,1.9,0.5,2.9,0.7
                        c7.6,1.1,10.7-1.2,10.8-8.8c0.1-4.6-0.5-9.3-0.9-13.9c-1.2-13.3-2.9-26.5-3.7-39.7c-1.5-25.9,2-51.1,10.7-75.6
                        c5.5-15.3,14.8-27.4,29.2-35.3c2.9-1.6,5.8-3.3,8.5-5.2c6.5-4.5,7.8-6.8,4.3-14.2c-7.1-15-14-30.2-22.7-44.3
                        c-14.4-23.2-33.8-41.9-57.9-55.3C2368.2,1252.2,2351.9,1246.8,2335.5,1245.4z M2351.9,2768c-0.2,14.2,3.4,27.6,7.6,41
                        c3,9.6,9.5,13.4,18.9,15c17.6,3,34.5-0.1,51.3-4c11.5-2.6,20.8-8.9,27.1-19c9.3-15,22.9-23.8,39.5-28.6c9.3-2.7,18.5-5.4,27.6-8.6
                        c11.3-3.9,20.7-10.5,27.3-20.8c6.4-9.9,7.5-20.3,3.3-31.4c-7.8-20.5-22.8-32.1-44.2-33.7c-16.5-1.2-33.3-0.5-49.8,0.6
                        c-10.6,0.7-18.3-2-23-11.7c-6.6-13.4-13.7-26.6-19.5-40.3c-7.6-18-21.8-27.8-41.2-26.8c-9.4,0.5-17.2,4.3-20.9,13
                        c-3.6,8.5-7.1,17.5-7.7,26.6c-1,13.9,0.2,27.9,1.1,41.9c1.3,20.3,3.4,40.5,4.4,60.7C2354.1,2750.7,2352.6,2759.4,2351.9,2768z
                        M2344.1,2053.4c-0.4,7-0.9,11.6-0.8,16.2c0.2,19,0.5,37.9,0.7,56.9c0.4,26.2-4.3,51.5-13,76.3c-11.8,33.5-19.8,67.7-17.1,103.6
                        c2,27,10.1,51.5,28.6,71.8c8.9,9.7,15.2,10.4,26.4,3.5c14.2-8.7,23.2-21.7,29.1-37c7.9-20.4,11.1-41.7,11.4-63.5
                        c0.8-62.2-5.9-123.5-21.6-183.8c-4.2-16.1-12.7-29.3-27.3-37.8C2356,2057.2,2350.8,2056,2344.1,2053.4z M2342.7,2392.5
                        c0.7,5.2,1,8.5,1.6,11.7c6.1,30.3,12.2,60.7,18.5,91c5.9,28.3,6.2,56.2-3.9,83.7c-1,2.8-1.2,6-2,10.2c4.7-1.2,7.5-2.2,10.4-2.5
                        c7.2-0.8,14.6-2.2,21.7-1.5c15.2,1.6,26.2,10.5,35,22.6c2.8,3.8,4.9,8.3,9.4,11.7c0.2-2,0.5-3,0.4-4c-0.3-2.6-0.7-5.3-1.1-7.9
                        c-3.1-20.6-4.8-41.3,0.2-61.6c13.6-55.2,27.9-110.3,42.2-165.4c3.9-15.1,9.4-29.8,13.9-44.8c1.6-5.2,3.9-10.6,0.3-16.8
                        c-10.6,7.7-16.7,17.7-19.9,29.5c-8.7,32.1-17.4,64.2-25.7,96.5c-5,19.7-9.2,39.5-13.7,59.4c-1,4.2-1.8,8.3-8.3,9.4
                        c2.5-14.8,4.3-29,7.3-42.9c8.3-39.4,17.1-78.7,25.7-118c0.6-2.7,0.8-5.6,1.3-9.2c-4.1,0.6-7,1.1-9.9,1.5c-7.6,1-15.1,2.5-22.7,2.8
                        c-11.3,0.4-11.4,0-15.9,10.8c-6.1,14.6-15.8,26.1-29.4,34.3c-7.8,4.7-16.1,6.1-24.9,3C2350.4,2394.8,2347.2,2393.9,2342.7,2392.5z
                        M2415.4,2030.6c-7.6,5.5-10.8,12.6-11.5,20.7c-0.6,7.6-1.1,15.4-0.3,22.9c2.7,25.2,5.6,50.3,9,75.3c6.5,47.2,11.5,94.5,13.1,142.2
                        c0.2,5,0.6,10,1.7,14.8c2.4,11.2,7.3,15,18.7,15.3c22.8,0.6,49.1-17.6,50.9-45.3c1.5-23.4,1.1-46.8-4.3-69.6
                        c-13.6-57-34.5-111.1-66.2-160.8C2423.2,2041,2419.3,2036,2415.4,2030.6z M2468,1322.3c0.6-0.3,1.3-0.6,1.9-0.9
                        c0-3.4,0.6-7-0.1-10.3c-4.6-21.8-9.1-43.6-14.3-65.2c-4.8-19.7-13.4-37.5-27.7-52.2c-12.9-13.3-28.3-22.3-46.9-23.8
                        c-5.3-0.4-11.6,0.6-16.1,3.1c-10.9,6.2-18.9,15.6-23,27.8c-2.3,6.9-4.7,13.8-6.9,20.8c-3.6,11.5-2.7,13.1,8.7,16.1
                        c12.9,3.3,25.8,6.5,38.6,9.9c29.7,7.9,52.3,25.3,68.6,51.2c3.5,5.6,7.1,11.2,11,16.6C2463.4,1318,2465.8,1320.1,2468,1322.3z
                        M2383.6,2058c6.5-14.6,12.5-27.8,18.4-41.1c2-4.6,1.3-9.1-1-13.4c-3.7-7-7.3-14.1-11.2-21.1c-1-1.8-2.9-3.1-4.7-5
                        c-2.5,5.7-4.3,10.7-6.7,15.4c-5.9,11.9-12.1,23.6-18.1,35.4c-3.5,6.9-3.4,8,2.3,12.7C2369.2,2046.5,2376,2051.8,2383.6,2058z
                        M2432.5,1123.7c-10.6,12.6-19.6,23.3-29.1,34.7c7.8,5.8,14.5,11,21.6,16c7.2,5,11.5,13.7,22.4,17.1
                        C2444.5,1168.2,2438.9,1147,2432.5,1123.7z"/>
                    <path d="M2212.6,976.3c-0.2-12.2-3.6-23.6-7.8-34.8c-6.4-17.3-15.7-33-26.2-48.1c-25.6-37-50.9-74.2-77-110.8
                        c-14.7-20.7-24.3-43.3-29.8-68c-4.6-20.8-10.4-41.3-16.6-61.6c-5.9-19.5-18.1-33.9-37.6-41.3c-3.1-1.2-7.2-2.9-5.8-6.8
                        c0.9-2.4,4.5-4.7,7.4-5.4c21.4-5.8,42.9-11.1,64.7-16.7c-1.8-1.1-3.6-2.2-6.3-3.9c3.7-2.7,6.8-5.2,10.1-7.2
                        c9.9-6.1,19.9-12.2,30-17.9c13.8-7.8,21.8-19.2,23.5-35c1.1-9.9,1.8-19.9,3.1-29.8c0.8-6-0.7-10.2-6.5-13
                        c-8.8-4.3-13.1-12-14.2-21.5c-1.3-11.6-2.4-23.2-3.7-34.8c-0.4-3.3-0.9-6.6-1.2-9.9c-0.5-4.8-3-7.2-7.8-7.4
                        c-8.1-0.3-12.5-5.1-15.6-12c-5.3-11.8-11.2-23.4-16.4-35.3c-6.4-14.9-1-26.1,14.4-31.1c0.6-0.2,1.3-0.4,1.9-0.6
                        c9.4-3,9.9-3.7,9.1-13.4c-1.9-24,1.4-47.2,9.6-69.7c18.7-51.4,66.5-82,120.1-77.4c61.6,5.3,98.2,53.5,105.4,97.8
                        c2.6,16,3.2,32.4,3.7,48.7c0.2,7,2.2,11,8.9,13.5c4.9,1.8,9.4,4.9,13.5,8.3c6.4,5.2,8.2,12.4,5,19.8c-6.4,14.6-13.3,29-20.3,43.4
                        c-2.5,5.1-7.2,7.8-12.9,8c-5.8,0.2-8.1,3.5-8.6,8.9c-0.9,9.6-1.9,19.2-3,28.8c-0.6,5.3-1.1,10.6-1.9,15.9
                        c-1.3,8.7-5.4,15.9-13.4,19.9c-6.1,3-7.8,7.3-7,13.8c1.4,10.6,2.2,21.2,3.5,31.8c1.8,14.2,9.4,24.6,21.7,31.8
                        c11.2,6.6,22.3,13.3,33.5,20c2.5,1.5,4.8,3.4,8.6,6.1c-4.4,1.9-7.4,3.2-11.2,4.8c25.6,3.8,49.9,10.2,73.6,18.9
                        c-0.1,5.4-2.8,7.7-6.3,9.2c-22.6,9.9-34.7,28.2-40.7,51.2c-4.2,16.1-8.4,32.2-11.9,48.5c-6.2,28.7-17.5,55.1-34.4,79.1
                        c-25.6,36.2-51.2,72.3-76.8,108.5c-15.1,21.4-26.8,44.4-31.6,70.5c-0.4,2.4-1.9,4.7-3.4,7.9c-3.6-3.8-2.9-7.6-3-10.9
                        c-0.5-36-1.3-72-1.2-107.9c0.2-45,0.7-90,2.2-134.9c0.6-19.6,3.3-39.2,6.5-58.5c1.4-8.5,6.4-16.3,9.7-24.5c3-7.4,5.8-14.9,9.2-22.1
                        c9.6-20.7,26-31.3,48.7-32.6c5.5-0.3,11-1.4,16.4-3c-24.9-2.8-47,2.6-63.3,23.2c-10.9,13.8-17.6,29.7-21.7,46.8
                        c-1.8,7.8-3.8,15.5-5.9,23.2c-0.5,1.8-1.6,3.4-4.4,5.2c-1-5.8-2.3-11.5-3.1-17.3c-2.5-17.8-8.5-34.2-17.9-49.4
                        c-15.2-24.8-40.2-36.3-69.7-31c5.5,0.8,10.9,2,16.5,2.3c23.7,1.2,40.2,12.6,49.8,34.1c13.7,30.4,21.8,62.1,22.2,95.6
                        c0.6,53.3,1.1,106.6,1.6,159.9c0.2,19.3,0.6,38.6,0.8,58c0.1,6,0.2,12-0.3,18c-1,12.2-2.5,24.4-4.1,36.5
                        c-1.5,11.6,2.4,23.8,2.5,35.7c0.6,56.3,0.8,112.6,1.1,169c0.3,59.3,0.5,118.6,0.7,178c0,3.3,0.2,6.7-0.5,10c-1.4,7-6.2,8.4-11.5,3.4
                        c-1.9-1.8-3.5-4-5.1-6.2c-14-18.9-24.7-39.4-32.4-61.6c-10-28.6-20-57.2-30.1-85.7c-6-17-13.3-33.4-23.7-48.3
                        c-7.6-10.9-16.9-19.6-29.9-23.9c-6.8-2.3-7.6-3.8-7.3-12.1c1.5-36.9,10.3-71.8,29.4-103.6c6.2-10.2,13.3-20,21-29.1
                        c14.4-17.1,29.4-33.6,44.3-50.2c3.5-3.9,7.4-7.8,11.7-10.7c7.7-5.3,12.8-3.8,16.9,4.5C2207.5,963.4,2209.9,969.9,2212.6,976.3z
                        M2307.5,468.3c8.4-3.4,9.6-4.3,10.7-12.2c2.2-15.5,4-31,5.8-46.6c1.2-10.5,1.1-10.7,11.3-12.5c5.7-1,9.9-3.8,12.4-9
                        c5.9-12.3,11.7-24.7,17.4-37c2.1-4.5,2.6-9.4-0.8-13.2c-3.2-3.6-7.1-7.3-11.4-9.2c-4.3-1.8-9.5-1.4-15-2.1c0-4.2-0.1-9.6,0-14.9
                        c0.4-20.7-0.3-41.3-6-61.4c-15.9-56.1-70.7-91-127.8-80.4c-43.1,8-70.9,34.5-85.9,75c-8,21.6-9.3,44.1-8.4,66.9
                        c0.5,12.6,0.1,13-12.3,16.2c-13.7,3.6-18.5,12.5-13.1,25.5c4.5,10.7,9.8,21.1,14.5,31.8c3.1,7.1,7.8,11.1,15.7,12
                        c7.7,0.9,8.1,1.7,9.1,9.2c0.9,6.9,1.5,13.9,2.4,20.8c1.2,9.9,2.3,19.8,3.9,29.7c1.2,7.6,3.1,9.1,10.8,10.6c0.4-1.5,0.9-3.1,1.2-4.6
                        c3.9-17.5,7.5-35.1,11.7-52.6c3.1-13.1,10.8-23.4,21.6-31.4c20.1-15,42.4-20.7,66.9-14.3c27.7,7.2,47.9,22.6,54,52.4
                        C2299.7,433.8,2303.6,450.6,2307.5,468.3z M2212.9,945.6c0.6,0,1.2-0.1,1.8-0.1c0.4-4.1,1.2-8.1,1.2-12.2
                        c-0.3-44.3-0.5-88.6-1.2-132.9c-0.5-34.6-1.2-69.2-2.8-103.8c-1.3-28.7-10.1-55.5-23.4-80.9c-5.5-10.5-13.5-18.6-25.2-21.3
                        c-22.5-5.1-45.4-8.9-68.3-5.4c-22.9,3.6-45.6,9.3-68.3,14.1c-1.4,0.3-2.8,1.1-5.2,2.1c2.4,2,3.7,3.5,5.3,4.4
                        c19.8,10.6,30.8,28,36.8,48.9c3.2,11.2,6.2,22.5,8.2,33.9c6.6,36.6,20.1,69.9,42.6,100c22.1,29.6,42.6,60.4,63.8,90.7
                        c12.8,18.3,24.4,37.3,32.5,58.3C2211.2,942.8,2212.1,944.2,2212.9,945.6z M2230,944.1c0.6,0.1,1.2,0.3,1.8,0.4c0.7-1,1.6-2,2-3.2
                        c9.4-23.4,22.9-44.3,37.7-64.6c18.5-25.3,36.9-50.6,55.4-75.9c21.3-29.1,36.4-61,44.1-96.4c3.8-17.5,8.6-34.9,13.4-52.2
                        c5-17.7,15.5-31.5,31.5-41c2.4-1.4,4.5-3.3,8.5-6.3c-10.8-3.1-19.6-5.6-28.4-8.2c-35.6-10.5-71.5-11.1-107.7-3.3
                        c-11.8,2.6-21.5,8.5-26.8,19c-8.3,16.5-16,33.3-22.6,50.6c-3.3,8.5-4.4,18.2-4.9,27.4c-1.5,25.6-2.7,51.2-3.1,76.9
                        c-0.8,56.3-1.1,112.6-1.5,169C2229.4,938.9,2229.8,941.5,2230,944.1z M2212.9,1378.9c0.8-0.2,1.5-0.3,2.3-0.5
                        c0.2-3.2,0.5-6.4,0.5-9.5c0-21,0.1-42,0-62.9c-0.5-96.2-1.1-192.5-1.6-288.7c-0.1-19.3-5.2-37.5-13.3-54.9
                        c-3.6-7.8-6.9-8.5-13.5-2.9c-1.3,1.1-2.4,2.3-3.5,3.5c-15.8,17.6-31.9,35-47.4,52.8c-31.2,35.7-45.8,78.2-50.3,124.7
                        c-1,9.9-0.1,11.3,9.2,14.9c9.6,3.7,17.5,9.5,23.1,18.2c12.5,19.3,23.5,39.4,30.9,61.3c6.1,18.3,12.4,36.5,19.1,54.6
                        c6.8,18.4,13.7,36.8,21.5,54.7C2195.6,1357.1,2203.2,1368.8,2212.9,1378.9z M2162.4,512.4c-0.8-0.3-1.6-0.6-2.4-0.9
                        c4.6-34.7,11.4-69.2,8.9-104.8c-3.2,2.8-5.4,5.8-7.1,9.2c-7.4,14.6-8.6,30.6-9.9,46.6c-1.5,18.6-3.2,37.2-5,55.7
                        c-1.9,18.6-11.4,32.3-27.8,41.2c-4,2.2-7.8,4.7-12.2,7.4c1.6,0.7,2.2,1.1,2.7,1.1c1.6-0.2,3.3-0.6,4.9-1c24.1-5.4,48.4-9.6,73-10.6
                        c8.3-0.3,12.9-3.5,16.1-10.5c6.2-13.5,8.8-27.9,10.5-42.5c3.8-32.6,2.5-65.2,1.4-97.8c-0.3-9.2-1.4-18.3-2.2-27.4
                        c-20.2,0.4-28.4,7.9-29.9,26.5c-0.4,5-0.8,10-1.1,14.9c-1.8,27-7,53.4-14.3,79.5C2166.6,503.6,2164.3,507.9,2162.4,512.4z
                        M2339.8,567.9c0.2-0.6,0.4-1.1,0.6-1.7c-4.8-2.9-9.6-5.7-14.4-8.7c-13.6-8.3-22-20.1-24.3-36c-0.7-4.6-1.2-9.2-1.6-13.9
                        c-2-21.6-3.8-43.1-5.9-64.7c-1.1-11.4-4.9-22.1-10.4-32.1c-1-1.7-2.8-3-5.9-6.1c-1.2,37.6,5.7,72.2,10.4,109.2
                        c-6.9-7-8.1-14.3-9.9-21.2c-7.8-29.4-12.1-59.3-13.9-89.7c-1-17.4-10.8-25.4-29.8-25c-0.4,6.2-0.9,12.4-1.3,18.6
                        c-2.3,36.3-3.2,72.6,1,108.8c1.6,14.2,4.3,28.3,10.9,41.3c2.9,5.9,6.7,8.8,13.8,9.2c15.3,1,30.5,2.9,45.6,5.1
                        C2316.4,563.1,2328.1,565.7,2339.8,567.9z M2224.8,481.1c-0.6,0-1.2,0-1.8,0c-0.3,3.3-0.4,6.5-0.8,9.7c-1.4,12.9-2.5,25.9-4.5,38.7
                        c-1.2,7.5-3.9,14.8-6.2,22c-1.6,4.8-5.3,7.8-10.3,8.3c-14.9,1.5-29.8,3.1-44.7,4.2c-19.1,1.4-37.6,5.7-55.7,11.8
                        c7.2-0.6,14.1-2.6,21.2-3.6c23.7-3.3,47.5-6.3,71.3-9.3c11.9-1.5,15.3,0.3,20.5,11.2c6.5,13.8,8.9,28.5,8.6,43.7
                        c-0.1,6.4,0,12.9,0,19.3c0.9,0,1.9,0,2.8,0c0.2-6.8,0.5-13.5,0.5-20.3c0.1-15.9,2.7-31.1,10-45.4c3.5-6.8,8.6-9.7,16.2-8.9
                        c29.7,3.4,59.8,4.6,88.8,13.1c2,0.6,4.4,0.3,6.5,0.4c-25.6-8.9-62-14.9-95.3-15.8c-9.2-0.3-14.7-4-16.9-12.4
                        c-2.7-10.6-5.2-21.3-6.8-32.2C2226.4,504.2,2225.8,492.6,2224.8,481.1z M2323.1,576c-0.1-0.8-0.3-1.6-0.4-2.3
                        c-21.8-1.9-43.6-3.9-65.5-5.5c-12-0.9-14.9,1.2-19.5,12.5c-5.1,12.5-6.7,25.7-7.7,39c-0.4,4.7-0.1,9.5-0.1,14.3
                        c9.8-17.2,18.2-34.9,35.3-46.2C2282.9,576,2303.1,576.8,2323.1,576z M2217.6,634c1-17.3-0.9-34-6-50.3c-1-3.1-2.6-6.1-4.2-9
                        c-2.7-4.6-6.4-7.2-12.3-6.7c-13.6,1.2-27.2,1.8-40.8,3c-9.8,0.9-19.6,2.3-29.4,3.5c19.5,2.4,39.4,1.5,56.9,12.6
                        C2199.4,598.2,2208.1,616.1,2217.6,634z M2270.5,413.4c0.4,0,0.8,0,1.2,0.1c0.5-2.6,1.1-5.1,1.4-7.7c0.5-4,0.5-8,1.1-11.9
                        c0.8-5.2-1.5-8.8-5.4-12c-23.4-19.3-66.1-19.4-89.5,0.1c-2.2,1.8-4.9,4.4-5.2,6.9c-0.9,8.2,0.1,16.3,2.8,24.1
                        c1.2-5.7,2-11.3,2.5-16.9c0.8-8.4,5.2-14.2,12.7-17.8c21.2-10,42.4-10,63.6,0c7.5,3.5,11.9,9.3,12.7,17.7
                        C2269.1,401.8,2269.8,407.6,2270.5,413.4z M2223.9,415c2.9-13.8,2.9-32.9,0.7-38.6C2221.1,379.9,2220.6,395.2,2223.9,415z"/>
                    <path d="M2023.4,1442.9c5.8,37.2,0.8,73.7-7.9,109.9c-0.3,1.3-0.8,2.5-1.1,3.9c-1.2,7.2,0.9,10.4,8.1,10.5c5.6,0,11.3-1,16.7-2.3
                        c8.1-2,15.9-4.9,24-6.8c11.3-2.7,15.2,0.3,16.6,12c3.1,24.6,3,49.3,1.4,73.9c-3.2,50.9-7.3,101.7-10.6,152.6
                        c-1.4,21.9-1.5,44-2.8,65.9c-2.5,41.6-11.6,81.6-29.2,119.6c-10.2,22-20.2,44.1-28.9,67.1c3.1-4.6,6.1-9.3,9.2-13.9
                        c8.8-13.1,15.5-12.7,23,1.2c4.2,7.8,6.2,16.1,5.2,25c-4.9,43.3-10.3,86.6-14.6,130c-3.5,35.1-5.8,70.4-8.7,105.6
                        c-0.3,3.6-0.5,7.3-1.3,10.9c-3.2,14.3-12.2,21-26.8,18.9c-7.5-1.1-14.7-4-21.9-6.3c-3-1-5.9-2.6-10.3-4.6
                        c6.5,10.8,12.2,20.2,18.2,30.1c0.3-3.3,0.5-6.1,0.8-10.1c4.8,1,8.9,2,13.1,2.8c6.8,1.3,13.9,4.2,20.5,3.5c15-1.5,23.4,3.7,28.1,18.4
                        c3.7,11.6,12.9,20.1,23.2,26.7c3,1.9,6.3,3.2,10.6,3.1c-1.5-1.4-2.7-3-4.4-4c-15.3-9.6-25.3-23.3-31.7-40
                        c-9.9-26-12.4-53.1-11.7-80.6c1.4-52,6-103.7,17.6-154.6c1.3-5.8,2.7-11.7,4.4-17.4c5.5-19.5,18-33,35.9-41.9
                        c2.4-1.2,4.9-2.2,7.4-2.8c5.7-1.4,9.3,0.3,10.5,6.1c1.1,5.5,1.4,11.2,1.2,16.8c-0.3,15.3-0.9,30.6-1.6,46
                        c-1.3,28.7,4.1,56.3,13.4,83.3c10.7,31,17.9,62.7,17.6,95.8c-0.2,24.2-5.2,46.8-18.2,67.6c-6.3,10-9,21.4-11.2,32.9
                        c-7.2,36.9-14.5,73.8-21.9,110.7c-4.5,22.1-3,43.7,4.2,65c1.9,5.7,3.5,11.5,5,17.3c0.8,3.1,0.9,6.4,1.6,11.6
                        c-13.6-7.7-25.8-15.5-40.5-11.6c-6.6,1.8-13.6,4.3-18.9,8.4c-5.9,4.5-10.7,10.9-15,17.1c-4.5,6.6-7.2,14.1-6.2,23
                        c2.5-5.7,5.3-11.3,7.5-17.1c7.5-19.1,29.1-30.9,49.2-27c11,2.1,18.6,8.5,23.4,18.4c7,14.5,9.3,29.7,7.6,45.7
                        c-2.2,20.5-4.7,41-6.4,61.6c-0.7,8.6-0.3,17.3,0.3,25.9c1.6,20.8,0.3,41.2-5.5,61.3c-3.9,13.6-12.4,20.8-26.4,21.8
                        c-19.1,1.4-37.9,0.4-56.3-5.8c-9.8-3.3-17.7-8.5-23.4-17.5c-10.3-16.3-25.9-25.3-44.1-30.7c-10.2-3-20.5-6-30.1-10.5
                        c-6.7-3.2-13.3-8-18.2-13.5c-13.2-14.9-14.4-31.6-4.8-49c10.1-18.2,25.6-29.1,46.3-30.6c14.9-1.1,29.9-0.5,44.9,0.5
                        c17.5,1.1,19,0.8,26.4-16.2c-6.8-6.7-4.5-15.1-3.5-23.3c1.7-12.5,3.4-25.1,5.2-37.6c3.3-22.5,0-44.5-5-66.4
                        c-14.5-63.3-30.7-126.1-51-187.8c-4.2-12.7-7-25.7-4.6-39.3c0.3-1.9,0.3-4-0.3-5.9c-9.8-31.3-7-62.6,0.7-93.7
                        c10.5-42.8,24.5-84.4,45.2-123.5c0.6-1.2,1-2.4,1.5-3.6c-13.4-20.9-12.4-44.5-12.7-67.6c-0.2-12.6,1.5-25.3,2.3-37.9
                        c2.2-31.1-1.2-61.8-6.6-92.4c-6.9-39.7-13.2-79.5-19.3-119.3c-3.9-26-7.5-52.1-10.2-78.2c-4.7-44.4-5.7-89.1-6-133.8
                        c-0.3-39,1-78,6.3-116.7c1.4-10.6,3-21.1,5.3-31.5c2.6-12.1,8.7-16,21-14.2c12.5,1.8,22.2,8.5,30.1,18c8.9,10.8,13.9,23.3,17.3,36.7
                        c0.4,1.5,1.1,3,1.7,4.5L2023.4,1442.9z M2072.7,1561.8c-5.8,1.3-9.7,2-13.6,3c-4.8,1.2-9.6,2.9-14.4,4c-6.5,1.6-12.9,3.4-19.5,4.1
                        c-12,1.2-17.6-4.8-15.8-16.7c0.8-5.6,2.3-11.1,3.5-16.6c6.7-30.5,9.8-61.2,5.1-92.2c-2.5-16.5-8.6-31.4-19.6-44.1
                        c-6.8-7.8-15.3-12.8-25.7-14c-7.8-0.9-12,1.7-14.2,9.3c-1.2,4.1-1.8,8.5-2.4,12.7c-6.6,42.6-8.6,85.5-8.8,128.5
                        c-0.3,59,2.4,117.9,10.5,176.4c6.7,48.1,14.2,96.1,21.7,144.2c2.9,18.7,6.8,37.3,10.2,56c3.6,19.6,0.7,39.2-0.8,58.8
                        c-1.9,25-2.6,49.9,1,74.8c0.6,4.2,1,8.5,5.6,11.7c1.6-2.9,3.3-5.4,4.5-8c12.7-27.9,25.6-55.6,37.9-83.6c12.1-27.3,20-55.8,22.3-85.6
                        c1.9-23.9,2.6-47.9,4.2-71.8c3.6-55.2,7.8-110.3,11-165.5c1.2-21.2,0.6-42.6,0.3-63.9C2075.8,1576.3,2073.9,1569.5,2072.7,1561.8z
                        M2098.5,2650.7c0.1-12.6-1.8-24.6-8-35.7c-5.8-10.5-14.5-16.2-26.9-15.1c-17.4,1.6-30.4,9.3-36.3,26.5
                        c-5.2,15.2-12.6,29.3-21.1,42.8c-4.8,7.6-11,10.2-20.1,9.6c-16.9-1.1-33.9-1.8-50.8-1c-21.3,1.1-35.6,13.7-44.3,32.5
                        c-5.8,12.5-4.4,25.2,4.9,35.4c5.7,6.3,12.8,11.8,20.3,15.7c7.8,4.1,16.8,6.2,25.4,8.6c20.5,5.8,37.8,16.4,49.8,34.4
                        c4.7,7,11,11.2,18.8,14c18.3,6.4,36.9,8.2,56.1,5.8c10.1-1.2,16.3-6.1,19.5-15.8c5.3-16.4,6.8-33.1,6.7-50.2
                        c-0.1-20,0.4-39.9,1.5-59.9C2094.8,2682.4,2096.9,2666.6,2098.5,2650.7z M2101.3,2055c-10.5,0.3-17.9,4.5-24.5,10.3
                        c-7.9,6.9-13.8,15.4-17.2,25.4c-3,8.8-5.6,17.8-7.6,26.8c-12.6,55.8-16.9,112.5-16.3,169.6c0.2,21.3,4.7,41.6,12.6,61.2
                        c5.5,13.6,14.1,24.8,26.6,32.8c12.4,8,19.1,6.9,28.5-4.1c18.6-21.7,26.7-47.2,27.8-75.3c1.3-30.7-4.6-60.1-14.2-89.2
                        c-5.3-16-9.6-32.5-13.1-49c-4.7-22-3-44.4-2.7-66.7C2101.4,2083.1,2101.3,2069.5,2101.3,2055z M2086.9,2589.3c0.6-1.5,1.2-2.2,1-2.7
                        c-0.9-3.5-1.7-7.1-2.9-10.5c-7.7-22.2-9.6-44.9-5.2-67.9c6.2-32.7,13-65.2,19.4-97.9c1.1-5.7,1.8-11.4,2.8-17.7
                        c-2.3,0.5-3.4,0.6-4.3,1c-16.6,7.4-30.3,2.5-42.7-9.6c-8.2-8-14.9-17.1-18.7-28c-2.1-5.9-5.7-8.3-12-8.3c-5.3,0-10.6-1.1-15.9-1.7
                        c-6.5-0.8-12.9-1.7-20.2-2.6c1.1,5.6,1.9,9.8,2.8,14c9.9,47.2,19.8,94.4,29.7,141.6c0.9,4.4,2.2,8.8,0.6,13.7
                        c-6.5-1.9-6.2-7.1-7.3-11.4c-12.6-50-25.1-100-37.7-150c-2.9-11.6-8.7-21.5-17.1-29.9c-1.5-1.5-3.5-2.4-7.4-5.1c2,9.1,2.9,16,5,22.5
                        c18.8,57.2,33.8,115.5,48,174c7.4,30.6,12.2,61.4,7.1,93c-0.6,4-1.2,8-1.8,12C2029.3,2589.7,2053.2,2576.1,2086.9,2589.3z
                        M2029.4,2030.2c-1,1.1-1.5,1.5-1.9,2.1c-30.3,47.7-55,97.9-69.9,152.8c-8.1,29.7-11.8,59.3-9.8,89.8c1.8,27,24.4,47.9,51.2,47.1
                        c11.4-0.3,17.2-5.2,18.8-16.6c0.9-6.6,1.5-13.3,1.7-19.9c1.9-58,10.1-115.4,17.5-172.8c2.4-18.8,3.2-37.8,4.2-56.7
                        C2041.9,2045.7,2038.1,2037.1,2029.4,2030.2z"/>
                    <path d="M1798.8,1032.6c-3.8-31.4-3.6-62.5,7.4-92.7c2.8-7.7,6.4-14.7,15.5-18.6c-16.2-13.5-12.4-30.7-9.1-47.2
                        c4.7-23.6,13.9-45.8,24.5-67.4c3.9-7.8,7-9.1,16.3-7.7c17.7,2.5,35.1,1.2,51.9-3.1c-10.5,0-21.3,0.1-32-0.1
                        c-5.3-0.1-10.7-0.5-15.9-1.3c-8.5-1.2-10.4-3.3-9.4-12c1.9-16.2,4.4-32.3,7-48.4c6.1-38.8,27.1-68,58.5-90.6
                        c19.9-14.3,41.5-25.1,64.6-33.2c11.3-4,22.2-2.9,32.8,2.8c22.1,11.9,36.1,30.1,42.5,54.2c3.2,12.2,1.3,17.4-9.4,24.5
                        c-22.6,15.2-41.5,34.4-60.1,54c-17.3,18.3-36,34.7-60.6,45c2.3,1,3.8,1.7,5.7,2.5c-0.3,14.1-10,24.8-13.7,37.9
                        c1.5-1.8,3.1-3.6,4.4-5.5c8.1-11.8,16.1-23.7,24.4-35.4c2.7-3.8,5.8-7.5,9.4-10.2c7.4-5.5,16.5-2.2,18.8,6.7
                        c1.2,4.4,1.3,9.2,1.5,13.8c0.2,7.8,0.1,15.6,1.5,23.5c0.6-6.6,0.8-13.3,1.9-19.8c7.4-44.8,30.6-79.5,67.3-105.6
                        c2.1-1.5,4.7-2.6,7.2-3.5c6.9-2.4,11.6-0.1,14.4,6.6c0.8,1.8,1.3,3.8,1.6,5.8c4.4,31.8,17.4,60.1,36,85.7
                        c26.1,35.9,48.3,74.2,73.4,110.7c3.6,5.2,6.6,10.8,9.7,16.3c4.5,8.1,4.1,16.2-1.2,23.6c-21,29.2-45.3,55.1-77.7,72.1
                        c-17.5,9.2-35.6,9.7-54,2.3c-13.7-5.5-25.7-13.5-36.8-25.1c5.5,17.8,20.9,30.6,38.6,32.8c16.1,2,31.9,0.7,47.3-4.7
                        c3.4-1.2,6.8-2.1,12.2-3.6c-1.3,4.5-1.7,7.6-3.1,10.3c-17.7,35.4-29.5,72.7-36.6,111.6c-0.2,1.3-0.7,2.6-1,3.9
                        c-3.3,13.9-7.6,15.2-18.5,5.4c-17.5-15.7-28.4-36-37.9-57c-12.1-26.6-18.9-54.7-23.9-83.3c-2.2-12.5-4-25-6.2-37.4
                        c-1.1-6.2-1.1-12,3.9-17.2c-11.4-24.9-15.7-51.4-19.1-78.3c-0.8,4.9-1.5,9.9-2.4,14.8c-5.7,31.3-22.5,55.9-46.4,76
                        c-11.8,9.9-23.6,6.7-29.3-7.7c-7.1-18-6.4-36.6-3.7-55.2c2.1-14.1,5.3-28,7.8-42.4c-10.7,19.4-26.7,33.9-43,48.8
                        c8.4-3.9,16.7-7.7,26.3-12.1c0.4,3.9,0.7,6.7,0.8,9.6c0.3,11.3,0.4,22.6,0.8,34c0.5,13.9,7.8,23.6,19.1,30.9
                        c6.5,4.2,12.7,4.3,18.4-0.5c7.2-5.9,13.9-12.4,20.7-18.7c2.8-2.6,5.4-7.4,9.8-4.1c4.1,3.1,1.1,7.5-0.8,10.9
                        c-13.3,23.9-26.6,47.7-40.3,71.3c-5.8,10-14.4,17.9-24.3,23.3c-6.4,3.5-14.6,4.2-22.1,5.1c-12,1.3-23.9,0.5-35.4-3.9
                        c-4.3-1.7-8.9-2.9-13.4-3.9c-6.5-1.4-11.7-4.5-15.5-9.9C1799.6,1038.8,1799.2,1035.7,1798.8,1032.6z M1978.4,864
                        c1.7,14.3,3,31,5.8,47.3c5.1,29.8,17.4,56.5,37.7,79.3c27.6,30.9,64.2,35.4,98.2,12c20.5-14.1,37.7-31.7,53.1-51.1
                        c14.2-18,14.4-21.1,1.7-40.5c-25.6-39-51.3-77.9-77.1-116.7c-13.6-20.4-24.3-42.1-30.8-65.7c-1.8-6.4-3-13-4.7-19.4
                        c-2.6-10-5.9-11.2-14.8-5.7c-0.6,0.4-1.1,0.7-1.7,1.1c-32.1,23.1-54.2,53.5-61.4,92.6C1980.4,818.2,1980.4,840.1,1978.4,864z
                        M1853.5,775.9c0,0.9,0,3.5,0,6.2c-0.1,4.3,2,7.2,6.2,7.6c8.3,0.9,16.6,2.5,24.8,2c28-1.6,53.5-10.5,74.6-29.8
                        c10.8-9.9,21.1-20.3,31.4-30.8c15.9-16.2,32-32.1,51.4-44.3c6.2-3.9,8.2-8.9,6.5-16.1c-5.8-25-20.4-43.1-43.2-54.4
                        c-8.4-4.2-17.5-4.6-26.1-0.7c-16,7.3-32.2,14.5-47.8,22.7c-15.8,8.2-29.5,19.4-41,33.1C1865.4,701.2,1856.6,736.6,1853.5,775.9z
                        M1878.2,906.1c-5.1,2.2-8.1,3.3-11,4.8c-13.5,7.2-26.8,14.7-42.5,16.5c-6.3,0.7-9.9,5.3-12.1,10.8c-12.4,32-13.4,64.8-7.5,98.2
                        c0.8,4.8,3.4,7.8,8.1,9c13.6,3.3,27.1,6.8,40.7,9.6c4.4,0.9,9.3,0.7,13.9,0c17.5-2.4,29.9-12.7,38.6-27.3c11-18.2,21.1-37,31.6-55.5
                        c0.9-1.6,1.3-3.5,2.8-7.6c-6.8,4.7-11.5,8.1-16.4,11.2c-10,6.4-18,6.1-27.7-0.9c-12.5-9.1-18.2-21.7-18.6-36.9
                        C1878,928,1878.2,918.1,1878.2,906.1z M1968.2,832.2c0-10.3,0-20.6,0-30.9c0-3,0.1-6-0.3-9c-0.5-3.7-1.5-8-5.8-8.3
                        c-2.8-0.2-5.9,1.9-8.8,3.3c-1.1,0.5-1.8,1.9-2.6,3c-9.1,12.7-18.5,25.2-27.2,38.2c-17.3,25.6-27,54-28.3,84.9
                        c-0.6,15-0.9,30,4.7,44.3c4.1,10.6,11.9,12.8,20.3,5.5c21.5-18.7,38.2-40.8,44.1-69.4C1968.4,873.4,1968.4,852.8,1968.2,832.2z
                        M1997.8,978.1c-0.9,0.3-1.8,0.7-2.7,1c1.6,10.5,3,21,4.8,31.4c6.2,35.6,16.4,69.9,34.2,101.6c7.1,12.6,15.4,24.1,26.6,33.4
                        c1.9,1.6,4.1,2.8,6.8,4.7c7.9-42.9,19.6-83.5,38.8-123.4c-5.1,1-8.3,1.4-11.5,2.2c-14.4,3.5-28.9,4-43.6,1.4
                        c-13.6-2.4-23.9-9.5-31.5-20.8c-5.7-8.6-11.4-17.2-17.2-25.7C2001.2,981.8,1999.4,980,1997.8,978.1z M1922.2,799.5
                        c-4.4,0.2-6.7,0-8.9,0.5c-20.6,4.3-41.2,7.7-62.3,4.1c-5.2-0.9-7.9,2.1-9.8,6.5c-4.7,11.4-9.6,22.6-14.3,34
                        c-7.1,17.1-9.8,35.1-11.5,53.4c-0.6,6.9,1.2,12.5,7.4,16.2c6.4,3.8,12.2,2.1,17.6-2c7.5-5.6,14.8-11.3,22.3-16.9
                        c14.6-11,25.7-24.9,33.6-41.4c6.6-13.8,13.7-27.3,20.4-41.1C1918.6,809,1920,804.9,1922.2,799.5z"/>
                    <path d="M2642.1,1041.6c1.3-5.1,2.4-10.2,3.8-15.2c0.8-2.9,1.4-5.9,2.8-8.5c2.8-5.3,8.9-6.1,12.1-1.1
                        c23.1,35.9,46.5,71.7,51.1,115.8c0.4,3.6,1.5,7.2,1.7,10.8c0.2,3.3,1,7.7-0.7,9.7c-4.7,5.6-2.4,10.5-0.5,15.8
                        c5.1,14.6,7.3,29.7,7.4,45.1c0.2,19,0,38,0.1,57c0,3.6,0.6,7.1,2.8,10.7c0.1-2.3,0.3-4.7,0.3-7c0-11-0.4-22,0.2-33
                        c0.3-4.7,1.6-10,4.2-13.9c4.1-6.2,8.8-5.3,11.8,1.3c0.6,1.2,0.8,2.5,1.2,3.8c13.2,49.6,31.9,97.4,49.6,145.5
                        c1.4,3.7,2.8,7.5,3.9,11.3c2.4,8.4,2.3,8.4-2.6,15.6c29.9,3.8,50.8,21.8,69.6,43.4c3.9,4.5,7.2,9.6,11.1,14.2
                        c10,12,22,21.2,36.3,27.6c5.2,2.3,10.3,4.7,15.1,7.7c5.2,3.3,5.8,6.6,2.6,11.8c-5.1,8.3-12.3,13.1-22.5,11.8
                        c-18.2-2.2-36.5-4.3-52.3-14.9c-0.8-0.5-1.8-0.8-2.8-1c-0.6-0.1-1.3,0.2-2.2,0.3c-2.9,3.8,0.2,6.8,1.8,9.8
                        c10.2,19.5,20.6,38.8,30.6,58.4c3.6,7.1,6.5,14.6,9.6,21.9c0.9,2.1,1.6,4.4,1.9,6.7c0.9,6.8-1.9,12.3-6.9,14.1
                        c-4.2,1.5-8.4-0.3-12.4-6.2c-4.1-6.1-7.5-12.6-11.2-18.9c-1.4-2.4-2.9-4.8-6-6.5c2.5,4.9,4.9,9.8,7.4,14.7c4.4,8.6,9.3,17,13.2,25.8
                        c2,4.4,3.4,9.5,3.3,14.3c-0.3,11.3-10.1,16.7-19.6,10.6c-5.1-3.3-9-8.4-14.1-13.4c0,1.4-0.1,2.8,0,4.1c0.6,4.8-0.7,8.5-5.3,10.8
                        c-4.9,2.5-10.2,2.9-14-0.9c-4.9-4.8-9.8-10.2-12.9-16.2c-7.1-13.9-13.2-28.4-19.7-42.6c-1.5-3.2-3.2-6.4-6.7-9.1
                        c0.6,2.2,0.9,4.5,1.8,6.6c4.1,9.1,8.3,18.1,12.6,27.2c1.8,3.8,3.1,7.5-1.1,10.7c-3.7,2.9-9.1,3.2-12.8-0.4
                        c-4.2-4.2-8.3-8.8-11.2-13.9c-15.6-27.9-30.9-55.9-46.1-84.1c-9.2-17.1-12.6-35.6-11.8-55c0.4-11,0.2-22-0.2-33
                        c-0.4-9.8,3.2-16.4,12.5-20.3c15-6.3,29.2-14.7,46.1-17.4c-2-0.3-4-1-5.9-0.8c-11.8,0.7-23,3.7-33.7,8.4c-6.7,3-13.4,6-20.2,8.6
                        c-8.8,3.3-11.9,2.3-16.4-5.8c-7-12.5-13.8-25.1-20.2-37.9c-20.5-40.4-41.2-80.7-61.1-121.3c-9.1-18.5-17.2-37.5-24.5-56.8
                        c-11.3-29.6-24.1-58.5-38.3-86.7c-5.6-11.2-9.2-22.8-9.5-35.4c0-1.3-0.2-2.7-0.1-4c0.5-4.9-0.6-10.9,5.4-12.8
                        c5.3-1.7,8.4,2.8,11.5,6.3c5.2,6.1,10.3,12.3,15.4,18.4c3.6,4.3,7.2,8.6,12,12.1c-0.3-1.2-0.5-2.4-0.8-3.6c-2.1-9.5-1-12.4,6.9-17.6
                        c0.8-0.5,1.7-1,2.6-1.5c21-11.4,21.5-9.1,36.2,2.9c8.2,6.8,16.7,13.3,25,19.9c0.7,0.6,1.7,0.9,3.5,0.7c-1.6-2.1-3.1-4.3-4.8-6.3
                        c-3.7-4.3-7.5-8.5-11.4-12.6c-6.6-6.9-8.2-14.9-6.2-24c0.9-4.2,1.1-8.5,1.7-12.8L2642.1,1041.6z M2730.1,1231.1
                        c-0.8,9.1-2.1,16.7-2.1,24.3c0,25,0.6,49.9,0.7,74.9c0,4,2.1,8.7-2.2,12.2c-5.5-0.9-5.4-5.6-6-9.2c-1.4-9.2-2.8-18.5-3.1-27.8
                        c-1-26.6-2.1-53.3-2-79.9c0.1-45.4-14.8-84.2-49-114.7c-13.7-12.2-28-23.6-42-35.4c-4.6-3.9-9.4-4.7-14.7-1.9
                        c-3.5,1.9-7.2,3.4-10.5,5.7c-6.4,4.3-7.4,7.5-3.8,14.3c4,7.6,8.5,15.1,13.3,22.2c9.3,13.6,16.4,28.1,21.3,43.8
                        c10.2,32.4,20.7,64.6,31,96.9c6.4,19.9,12.8,39.9,19.3,59.8c1.2,3.7,3.1,7.2-2.2,11c-2.3-5.9-4.5-11.1-6.2-16.4
                        c-16.6-49.5-32.9-99.1-50-148.4c-4.1-11.8-9.5-23.7-16.7-33.8c-14.2-19.7-30-38.2-45.3-57.2c-1.7-2.1-4.2-3.6-6.7-5.7
                        c-3.4,14.9-2.8,22.1,3.9,35.9c16.3,33.5,31.1,67.7,44.1,102.6c5.2,14,11.3,27.8,18,41.1c26.9,53.5,54.2,106.8,81.4,160.2
                        c0.9,1.8,1.9,3.5,2.8,5.3c2.5,4.5,5.8,6,10.8,3.6c3.9-1.9,8-3.3,11.9-5.1c17.8-8.2,36.1-13.7,56-11.7c2.3,0.2,4.8-1,7.1-1.6
                        c-18.6-54.9-36.8-108.7-55.1-162.5C2734.1,1233.1,2732.8,1232.7,2730.1,1231.1z M2787.4,1570.9c3.8,7.8,7.3,14.9,10.6,22.1
                        c6.3,13.6,12.3,27.3,19,40.7c2.5,4.9,6.4,9.3,10.6,12.9c1.9,1.6,6.5,1.6,9.1,0.5c3.7-1.6,3-5.4,1.5-8.7c-1.8-3.9-3.6-7.9-5.5-11.8
                        c-9.1-19.3-18.2-38.5-27.2-57.8c-3.8-8.1-3.3-8.9,5.3-12.7c1.7,3.1,3.5,6.2,5.1,9.5c10.5,20.8,20.8,41.7,31.4,62.4
                        c2.6,5,5.7,9.8,9.2,14.2c4.3,5.5,8,6.5,11.9,4.4c4.1-2.3,5.5-7.2,3.4-13.3c-1.1-3.1-2.7-6.1-4.2-9.1c-13.1-25.8-26.3-51.6-39.4-77.4
                        c-3.7-7.3-3.4-7.7,4.5-12c2,3.4,4.2,6.7,6.1,10.1c10.3,17.9,20.6,35.8,30.9,53.7c1.5,2.6,2.7,5.5,4.8,7.5c1.9,1.8,5.3,4.1,7.2,3.5
                        c3.6-1,3.7-4.9,2.7-8.4c-0.5-1.6-1-3.2-1.7-4.7c-3.3-7.3-6.3-14.7-9.9-21.8c-10.2-19.8-20.8-39.4-31.1-59.2
                        c-5.4-10.5-4.7-12.1,7-17.6c12.5,11.9,29.4,13.5,45.5,17.1c4.1,0.9,8.7,1.2,12.9,0.7c6.9-1,12.7-3.9,14-12.2
                        c-1.4-1.1-2.5-2.5-3.9-3.1c-25.6-9.5-44.8-26.8-61-48.3c-8.9-11.7-19.6-21.8-32.2-29.7c-16.7-10.5-34.7-14.6-54.4-10.9
                        c-14,2.6-26.2,9.5-38.9,15.1c-6.7,2.9-9.5,7.6-9.1,14.9c0.6,10.6,1.4,21.3,1,31.9c-0.9,20.2,3,39.2,12.4,56.9
                        c13.7,25.8,28.2,51.3,42.4,76.9c2.4,4.3,5.4,8.4,8.2,12.5c2.4,3.5,5.4,5.5,10.7,2.6c-5.1-11.2-10.1-22-15-32.9
                        C2777.1,1579.7,2777.3,1579.2,2787.4,1570.9z M2655.5,1021.4c-1.5,1.9-2.5,2.5-2.7,3.4c-2.4,10.3-4.8,20.7-6.8,31.1
                        c-1.5,7.7,0.9,14.4,6.2,20.1c18.3,19.4,34.8,40.2,49.5,62.4c1.2,1.9,3.3,3.2,6.6,6.2C2704.1,1096.1,2681.8,1058.3,2655.5,1021.4z"/>
                    <path d="M2023.6,1443.2c-2.5-23.6-9.5-44.9-31.3-58.1c-3.7-2.2-7.5-4.3-11.1-6.7c-8.3-5.5-9.7-9.3-6.3-18.5
                        c7.3-19.4,16.6-37.8,28.8-54.6c12-16.6,26.4-30.8,43.4-42.3c1.8-1.2,3.4-2.6,4.8-4.6c-6.5,0.5-11.9,3.6-17.1,7.1
                        c-16,10.8-29.2,24.2-39.1,40.8c-2.9,4.9-5.9,9.7-9.3,14.2c-2,2.6-4.4,5.1-7.2,6.8c-5.2,3.2-9.1,1.3-10-4.7c-0.5-3.2-0.4-6.7,0.3-9.9
                        c5.7-25.7,10.9-51.5,17.7-76.8c2.9-10.7,9.1-20.6,13.9-30.8c1.2-2.6,3.1-5,4.6-7.4c-0.8-0.7-1.3-1.4-1.7-1.4
                        c-11.8-0.6-13.3-2.2-10.8-13.9c3.6-17.3,7.5-34.5,11.3-51.7c0.3-1.3,0.7-2.6,1.1-3.8c2.9-7.6,7.8-9,13.3-3
                        c7.2,7.8,14,16.1,20.9,24.2c2.8,3.3,5.4,6.7,8.8,11c-6.8,5.5-13.2,10.6-19.6,15.7c0.1,0.3,0.2,0.6,0.3,0.9c1.8-0.7,3.6-1.5,5.4-2.1
                        c9.1-3.1,18.1-6.7,27.4-9c8.4-2.1,16.6-0.3,23.6,5.2c11.8,9.2,21.1,20.3,24.7,35.3c2.4,10,4.8,20,7.7,29.8c0.9,2.9,3.9,5.1,5.7,7.7
                        c2.8,4,6.7,7.7,8,12.2c16.1,54,42.8,102.4,76.6,147.2c17.3,22.9,17.4,33.5,1.9,57.8c-13.2,20.7-31.1,36.3-52.1,48.7
                        c-8.9,5.2-17.7,10.4-26.1,17.1c1.9-0.5,3.9-0.8,5.7-1.5c13.8-5.2,27-11.3,38.4-20.8c0.3-0.2,0.5-0.4,0.8-0.6c3.9-2.7,7.7-5.6,12.8-3
                        c4.8,2.5,5.7,6.9,5.9,11.8c0.4,10.3,1.1,20.6,1.7,30.9c2.3,46.6,5.8,93.1,4.3,139.8c-1.8,56.3-3,112.6-6.3,168.8
                        c-2.5,42.6-11,84.4-21.2,125.9c-11.3,45.9-22.9,91.7-40.6,135.7c-2.6,6.5-5.8,12.7-9,18.9c-1.7,3.4-4.3,6.6-8.5,5.5
                        c-4.3-1.1-4.7-5.3-4.8-9c-0.2-13-0.2-26-0.2-39c0-6-0.4-12,0.1-18c1.5-19.3-6.8-34-20.3-46.7c-0.3,0.7-1,1.4-0.8,1.7
                        c5.6,11.7,2.1,13.6-4.9,19.6c-8.3,7.1-16.9,13.9-26.5,21.7c-2-4-3.6-6.8-4.8-9.7c-4.7-11.4-9.2-22.8-14.1-34.1
                        c-4-9.2-3.8-18,1.3-26.7c4.4-7.5,8.3-15.2,12.4-22.8c1-1.8,1.6-4.4,3-5c7.2-3.3,6.7-9.9,7.3-16c3.2-31.1,6.4-62.3,9.4-93.4
                        c1.1-11.9,1.6-23.9,2.4-35.9c4-54.8,7.8-109.6,12.1-164.4c1.7-22.6,4.4-45.1,6.6-67.6c2.1-22.1,7.5-42.7,24.9-59
                        c-1.9,0.4-4,0.5-5.8,1.2c-20.1,7.1-40.1,14.4-60.2,21.4c-4.7,1.6-9.6,2.9-14.5,3.3c-11.4,0.8-15.7-3-15.1-14.3
                        c0.6-12.6,2.2-25.2,3.5-37.8c1.9-19.3,2.4-38.5-1.3-57.7c-0.4-1.9-1.6-3.6-2.4-5.3C2023.4,1442.9,2023.6,1443.2,2023.6,1443.2z
                        M2187.4,1502.9c-8.3,5.5-14.4,10.2-21.1,13.7c-9.4,4.9-19.2,9.1-29,13.4c-18.3,8.2-30,21.6-33.9,41.5c-1.3,6.5-2.6,13.1-3.4,19.7
                        c-3.3,30.1-6.7,60.2-9.5,90.4c-2.6,27.8-4.7,55.7-6.6,83.7c-1.3,18.6-1.2,37.3-2.8,55.9c-3.3,38.8-6.6,77.6-11.1,116.3
                        c-3,25.8,2.4,48.9,16.7,70.2c5.8,8.5,12.1,16.7,18.7,24.7c7,8.6,10.6,18.1,10.7,29.3c0.1,19.3,0.8,38.6,1.3,57.9
                        c0.1,2.1,0.6,4.1,1.4,8.7c3.3-4.6,5.7-7,6.8-9.8c6.4-16.8,13.4-33.4,18.4-50.6c9.2-32,17.1-64.3,25.5-96.5
                        c12.3-46.8,20.7-94.3,22.4-142.8c1.5-41.6,2.9-83.2,4-124.8c0.6-23,1.6-46,0.7-68.9c-1.5-40.9-4.2-81.8-6.5-122.7
                        C2189.9,1509.5,2188.5,1506.9,2187.4,1502.9z M2217.1,1430.6c-2.1-5.5-3.7-10.9-6.3-15.8c-2.3-4.4-5.6-8.2-8.6-12.2
                        c-32.9-43.7-58.7-91.1-74.5-143.7c-3.8-12.7-6.8-14.6-20.5-13.1c-25.6,2.8-47.7,13.9-67.7,29.6c-26.1,20.5-43,47.8-56,77.8
                        c-7.1,16.4-7.1,16.4,8.6,25.4c14.5,8.2,25,19.8,30.5,35.6c9.4,27.1,13.4,54.9,10.4,83.5c-1.5,14.2-3,28.5-4.4,42.7
                        c-1,10.1,2.8,13.9,12.7,12c7.8-1.5,15.5-3.9,23.1-6.3c37.6-12.1,73.5-27.7,105.3-51.6c16.7-12.5,31.3-27,40.8-45.9
                        C2213.2,1443.1,2214.8,1437,2217.1,1430.6z M1974,1322.5c9.3-4.5,12.7-11,16.4-17.3c18.5-31.5,44.8-52.1,81.1-59.5
                        c11.4-2.3,22.6-5.8,33.7-9c7.2-2.1,8.1-3.8,6-10.8c-2.7-9.2-5-18.8-9.1-27.4c-12.4-25.4-28.4-35.3-54.3-24.5
                        c-6.4,2.7-12.8,5.8-18.5,9.7c-19.4,13.5-31.5,32.2-37.1,55.1c-6,24.6-11.6,49.2-17.3,73.9C1974.3,1315.1,1974.4,1318,1974,1322.5z
                        M2061.1,2058c7.4-5.9,13.5-10.5,19.3-15.4c7.9-6.6,7.6-7.6,2.9-16.6c-7.3-14.1-14-28.5-20.9-42.8c-1-2-2-3.9-3.6-7.1
                        c-5.6,10.3-11,19.4-15.5,28.9c-1.4,3-1.8,7.5-0.6,10.6C2048.2,2029.3,2054.4,2042.8,2061.1,2058z M1999.2,1190.3
                        c15.3-9.8,28.2-21.2,41.6-32.8c-9.7-11.6-18.5-22.3-28.3-34C2006,1146.3,2001.2,1167.8,1999.2,1190.3z M2023.1,1181.3
                        c-0.4-0.5-0.9-1-1.3-1.5c-2.4,2.2-4.8,4.4-7.2,6.6c0.4,0.5,0.9,0.9,1.3,1.4C2018.3,1185.7,2020.7,1183.5,2023.1,1181.3z"/>
                    <path d="M2642.4,1041.3c-2.9,4.9-7.2,7.3-12.5,9c-16.8,5.3-33.3,11.2-51.6,9.9c-16.6-1.2-30.4-6.8-40.4-20.4c-3-4-6-8-8.4-12.4
                        c-13.1-22.8-26-45.7-39-68.6c-0.8-1.4-2.1-2.9-2.1-4.4c-0.1-1.8,0.4-4.3,1.6-5.2c1.2-0.9,4.2-1.1,5.4-0.2c3.2,2.3,5.9,5.3,8.8,8.1
                        c4.9,4.5,9.5,9.4,14.8,13.4c9.5,7.1,17.2,6.4,26-1.7c8.8-8,13.7-17.9,14.1-29.9c0.3-9.7,0.3-19.3,0.4-29c0-3.3,0-6.6,0-10.5
                        c10.7,0.2,17.7,8.3,27.3,9.5c-17.2-13.7-33.2-28.4-45-47.6c0.9,3.7,1.6,7.4,2.7,11c6.3,20.3,9.2,41,7.4,62.1
                        c-0.7,7.9-1.9,15.9-4.1,23.5c-4.4,14.6-16.8,18.6-28.8,9.1c-20-15.9-34.1-36.3-43.4-60c-1.2-3.1-2.4-6.2-3.7-9.3
                        c-0.1-0.3-0.5-0.4-0.8-0.7c-0.4,0.3-1.2,0.5-1.3,0.8c-4.5,16.7-9,33.4-13.5,50.1c-0.5,1.9-1.4,4.3-0.7,5.8c5.1,10.9,1.3,21.8,0,32.4
                        c-4.7,36.5-13.2,72-27.8,105.9c-7.2,16.6-15.7,32.3-27.5,46.2c-9,10.7-15,15.1-25.5,18.4c-5.1-18.8-10.1-37.6-15.2-56.4
                        c-0.1-1.8-0.1-3.7-0.2-5.5c-5.9-26.4-18.5-50.3-29.6-74.3c3.7-4.2,6.7-1.6,9.7-0.5c15.9,5.8,32.2,8.3,49,6
                        c19.3-2.7,32.2-13.9,39.9-31.8c-2.4,2-4.8,4-7.1,6.1c-10,9-21.3,15.8-34.3,19.8c-18.2,5.6-35.6,4-52-5.5
                        c-29.6-17.1-53.4-40.6-73.2-68.3c-6.7-9.3-7.1-19.1-1.2-29c1.4-2.3,2.7-4.6,4.2-6.8c27-41.2,53.8-82.5,81.3-123.4
                        c15.4-23,26.1-47.8,31.5-74.9c0.1-0.7,0.2-1.3,0.3-2c3.5-17.5,10.9-20.4,25.5-9.9c23.5,16.8,41.6,38.2,53,64.9
                        c3.4,7.9,6.9,15.8,10.7,24.6c2.1-3,3.7-5.4,5.5-7.7c4.5-5.5,10-6.6,15.7-2.3c3.4,2.5,6.3,5.9,8.8,9.3c8.2,11.3,16,22.9,24,34.4
                        c1.8,2.6,3.8,5.1,6.4,7.3c-4-13.1-14-23.9-14.9-39.3c7.3,1,14.1,2,20.9,3c2.5-0.1,5-0.1,7.5-0.2c2.2,1.2,4.3,3,6.7,3.5
                        c14.1,3,28.4,3,42.7,1.1c6.3-0.9,11.1,0.5,14.1,6.6c11.4,23.7,21.8,47.7,26,74c2.4,14.9,3.8,29.2-8.7,41.2
                        c9.5,5.9,13,14.8,15.9,24.2c8.1,26.5,10.8,53.3,5.6,80.8c-1,5.2-0.8,10.6-1.1,15.9C2642.1,1041.6,2642.4,1041.3,2642.4,1041.3z
                        M2465.9,846.6c-0.9-14.6-0.5-27.7-2.6-40.5c-7-43.6-29.5-77.8-65.7-103.1c-1.1-0.8-2.3-1.4-3.5-2c-4.8-2-7.5-1.1-9.5,3.8
                        c-1.3,3.4-2.1,7-2.8,10.6c-4.9,25.9-15.3,49.4-29.7,71.4c-27.5,41.7-54.7,83.6-82.1,125.3c-1.6,2.5-3.2,5-4.7,7.7
                        c-4.6,8.4-4.2,16.6,1.6,24.3c19.4,26.2,42.3,48.8,70.5,65.6c15.5,9.2,31.9,10.7,49,5.2c15.9-5.1,29.2-14.3,40-27.1
                        c12.3-14.7,21.4-31.3,27.7-49.4C2464.7,908.2,2467,876.8,2465.9,846.6z M2499.7,962.5c0.7,3.2,0.7,3.9,1,4.5
                        c11.6,20.2,23.2,40.4,34.7,60.6c12.3,21.5,31.1,30.5,55.2,27.1c12.1-1.7,23.8-5.7,35.8-8.4c6.9-1.5,10.5-5.7,10.9-12.4
                        c1-16.9,2.6-33.9,2.2-50.7c-0.4-15.6-4.5-30.9-10.4-45.5c-1.9-4.7-4.1-9.2-10.1-9.9c-15.5-1.8-29.2-8.7-42.7-16
                        c-3.1-1.7-6.4-2.9-11.1-5.1c-0.5,11.5-1.1,21.4-1.4,31.3c-0.4,14-5.2,25.9-16.1,35.1c-11.5,9.6-20,10.1-32.4,1.3
                        C2510.6,970.9,2506,967.2,2499.7,962.5z M2548.4,923.3c-1.1-22.3-5.7-43.7-13.5-64.4c-8.3-21.8-21.9-40.2-35.8-58.5
                        c-3.6-4.8-7-9.8-11.2-14.1c-5-5.2-12-3-13.6,4c-0.7,3.2-0.6,6.6-0.6,9.9c0.1,20.3,0.4,40.6,0.2,60.9c-0.3,40.6,16.5,73.4,45.8,100.3
                        c10.4,9.5,20.2,7,23.9-6.8C2546.3,944.5,2546.9,933.8,2548.4,923.3z M2378.6,1150.5c5.3-4.4,9.3-7.2,12.7-10.7
                        c12.3-12.5,21.1-27.3,28.4-43.1c17.1-36.9,25.7-76,31.3-116c0.2-1.6-0.6-3.4-1.2-6.9c-3.4,5.1-5.8,8.6-8.1,12.2
                        c-3.5,5.6-6.8,11.4-10.3,17.1c-9.1,14.5-21.9,24.4-38.9,27.1c-9.4,1.5-19.1,1.1-28.7,0.7c-7.2-0.3-14.3-1.7-23-2.9
                        C2359,1067.3,2369.6,1107.4,2378.6,1150.5z M2519.2,798.4c2.1,4.9,3.2,8,4.7,11c7.1,14.6,14.3,29.3,21.5,43.9
                        c12.9,25.9,33.3,44.7,57.5,59.5c12.5,7.7,22.3,2.2,23.3-12.6c0.4-6.6-0.1-13.3-1.1-19.9c-3.7-23.9-13.1-46-23.2-67.7
                        c-4.1-8.7-5.1-8.6-14.7-8.2c-11.2,0.5-22.6,0.7-33.8-0.3C2542.6,803.1,2531.9,800.6,2519.2,798.4z"/>
                    <path d="M1798.8,1032.6c0.4,3.1,0.8,6.3,1.2,9.4c0.3,3.6,0.1,7.4,1,10.9c2.7,10.7,0.2,19.7-7.5,27.5c-3.7,3.8-7,8-9.8,13.2
                        c1.7-0.9,3.7-1.5,5.2-2.6c7.9-6.1,15.9-12.2,23.4-18.7c6.8-5.9,13.8-7.7,22-3.5c1.8,0.9,3.7,1.6,5.5,2.5
                        c13.4,6.3,15.7,10.8,12.4,26.9c2.7-3,4.4-4.7,5.9-6.6c7.1-8.4,14-16.9,21.2-25.3c1.9-2.3,4.1-4.5,6.6-6c4.5-2.8,9.1-1,9.7,4.3
                        c1.3,12.6,2.3,25.5-4.1,37.2c-17.2,31.4-29.8,64.6-42.5,97.9c-15,39.6-33.9,77.4-53.4,114.9c-16,30.7-32.1,61.4-48.1,92
                        c-0.6,1.2-1.3,2.4-1.9,3.5c-7.1,12.9-9.4,13.8-22.8,8.3c-8-3.3-15.9-6.9-24-9.9c-12.3-4.6-25-7-38.2-4.8c-2,0.3-4,0.4-6,0.3
                        c-6.5-0.5-8.9-3.5-7.5-10c0.9-4.2,2.4-8.3,3.8-12.4c13.2-37.7,26.6-75.3,39.7-113c3.7-10.7,6.6-21.6,10-32.4
                        c1.6-5,2.8-10.4,8.9-12.6c6.6,2.4,8.2,7.9,8.6,14.2c1.2,17.9,2.3,35.8,3.4,51.9c0-18.4,0.1-38.7,0-58.9c-0.1-16.7,0.8-33.3,4.9-49.6
                        c1.9-7.4,4.3-14.7,6.6-22.4c-6.6-6.2-5.4-14.4-4.3-22.3c3.1-22.6,9.4-44.4,20.3-64.4c9.4-17.2,19.6-34.1,29.9-50.8
                        c1.9-3.1,5.7-6.8,8.8-7c4.9-0.3,6,5.3,7.7,9.2C1796.3,1026.5,1797.5,1029.6,1798.8,1032.6z M1708.3,1231.4c-1,2.4-1.8,4.1-2.3,6
                        c-3.2,10.5-5.8,21.2-9.4,31.5c-13.7,39.3-27.6,78.4-41.5,117.7c-1.2,3.3-2.1,6.7-3.4,10.7c3.6,0.3,5.9,0.9,8.1,0.7
                        c18.5-2.1,35.7,2.6,52.3,10c4.9,2.1,9.7,4.4,14.7,6.3c6.6,2.6,8.5,1.9,12.2-4.2c1.7-2.8,3.2-5.8,4.8-8.8
                        c14.6-28,29.1-56.1,43.9-84.1c22.1-41.8,43.1-84.2,59.6-128.6c10.3-27.8,21.8-55,35.2-81.5c2.5-5,4.9-10.3,6.5-15.6
                        c2.6-8.5,3.6-17.1-0.6-27c-3.4,3.9-6.1,6.7-8.7,9.7c-7.4,9-14.5,18.3-22.3,27c-20.2,22.4-33.9,48.4-43.3,76.9
                        c-14.6,44.6-29.4,89.1-44.2,133.6c-1.3,3.8-2.7,7.5-4.4,11.1c-0.7,1.4-2.5,2.3-5.6,4.9c0.5-4.7,0.3-7.3,1-9.7
                        c16.7-51.9,33.5-103.8,50.3-155.7c6.5-20.1,15.3-39,28.3-55.8c3.2-4.1,5.3-9.2,7.4-14.1c2-4.6,1.2-9.1-2.6-12.8
                        c-9.1-9-21.8-9.2-31.2-0.8c-4.7,4.2-9.7,8.1-14.5,12.2c-9.1,7.8-18.8,14.9-27.1,23.5c-19.3,19.8-34.7,42.1-41,69.7
                        c-4.4,19.4-3.9,39.1-3.3,58.7c0.8,26.1-0.9,51.9-4,77.8c-0.7,5.6-1.4,11.2-2.5,16.8c-0.6,3.2-2,5.8-6.8,3.9
                        c-0.2-3.3-0.5-6.9-0.5-10.5c-0.1-29.3-0.1-58.6-0.2-88C1713.4,1239,1713.7,1234.8,1708.3,1231.4z M1788.4,1021
                        c-25.9,22.8-56.1,94.5-51.3,120c0.9-0.6,1.9-0.9,2.3-1.5c16-23.4,33.6-45.6,52.7-66.6c2.5-2.7,4.5-7.2,4.3-10.7
                        C1795.5,1048.6,1792.1,1035.4,1788.4,1021z"/>
                    <path d="M1654.3,1583.9c-6.1,13.2-12.2,26.5-18.4,39.7c-2.8,6-5.6,12.1-8.6,18c-3.2,6.3-8.4,10.3-15.3,11.8
                        c-8.7,1.9-14.2-2.5-14.4-11.4c0-1.6,0-3.2,0-6.2c-4,4.5-7,8.4-10.5,11.7c-4.1,4.1-8.8,7.4-15.1,4.9c-5.5-2.2-8.9-8.3-8-15.7
                        c0.5-3.8,1.9-7.7,3.7-11.2c5-10.2,10.3-20.1,15.5-30.2c1-1.9,1.8-4,1.1-6.9c-2.9,4.9-5.6,9.9-8.6,14.7c-2.3,3.7-4.3,7.6-7.3,10.7
                        c-5.8,6-14.1,4-16.2-4c-0.9-3.6-0.7-8.1,0.6-11.6c2.9-8.1,6.3-16.1,10.3-23.8c10-19.5,20.5-38.9,30.7-58.3c1.6-3,4.5-6,1.7-10.5
                        c-1.5,0.3-3.4,0.2-4.6,1c-10.4,7.3-22.3,10.3-34.5,12.4c-5.9,1-11.8,2.1-17.7,2.9c-10.6,1.4-17.9-4-23.1-12.5
                        c-3-4.9-2.3-7.8,2.3-11.1c2.7-1.9,5.7-3.4,8.8-4.7c20.5-8.5,36.7-22.1,50.4-39.7c8.7-11.2,18.7-22.1,30-30.5
                        c28.2-21,59.1-23.8,91.2-8.8c5.7,2.7,11.4,5.4,17.2,8c6.8,3.1,10.3,8.1,10,15.7c-0.6,19-0.9,38-1.6,56.9c-0.2,4.3-1.2,8.6-2.7,12.6
                        c-4.3,11.5-8.2,23.3-14,34.1c-12.5,23.5-26,46.4-39.2,69.6c-2,3.5-4.2,6.9-6.7,10c-2,2.6-4.4,5-7.1,6.9c-3.7,2.6-7.6,2.2-11-0.7
                        c-3.3-2.8-3.5-6.1-1.7-9.9c3.8-7.8,7.5-15.6,11.1-23.5c1.5-3.2,2.6-6.5,3.9-9.8C1655.9,1584.5,1655.1,1584.2,1654.3,1583.9z
                        M1631,1556.4c8.7,2.6,9.3,3.5,6.9,9.8c-0.8,2.2-1.9,4.2-3,6.3c-9.5,19.8-19,39.6-28.4,59.5c-2.2,4.7-4.9,9.6-1.1,16.4
                        c3.5-1.1,7.9-1.2,10.4-3.5c4.1-3.6,7.8-8.2,10.2-13.1c8.2-16.7,15.8-33.8,23.7-50.7c1.5-3.2,3.3-6.2,5.1-9.3
                        c10.4,6.8,10.7,7.6,6.3,17.1c-2.8,6-5.8,12-8.7,18c-2.4,5-4.5,10.1-6.8,15.3c6.9,1.8,7,1.9,9.4-0.9c1.5-1.8,2.8-3.7,4-5.7
                        c11.1-19,21.8-38.2,33.3-56.9c10.9-17.7,18.7-36.7,25.3-56.3c1-2.8,1.7-5.8,1.7-8.8c0.3-18.3,0.4-36.7,0.6-55
                        c0.1-5.2-2-8.9-6.8-11.1c-8.5-3.8-16.7-8.3-25.4-11.6c-27-10.3-52.3-6.4-75.6,10.6c-11.7,8.5-21.6,18.8-30.2,30.4
                        c-13.9,18.5-31.1,32.8-52.6,41.7c-3.2,1.3-6.1,3.5-9,5.2c4.8,11.3,12.8,14.3,23.2,12.2c15.9-3.3,32.2-5.5,46-15.4
                        c3.2-2.3,6.5-3.2,10-0.2c3.8,3.3,4.7,7.1,2.5,11.6c-1.1,2.4-2.5,4.7-3.7,7.1c-13,25.5-25.9,51-38.8,76.6c-1.2,2.4-2.6,5-2.6,7.5
                        c0,2.3,1.1,5.6,2.8,6.5c1.5,0.8,5-0.4,6.6-1.8c2.1-1.9,3.4-4.9,4.9-7.5c11-19,21.9-38.1,32.9-57.1c1.8-3.1,3.8-6,5.8-9.1
                        c6.9,5.6,7.1,5.7,4.2,11.6c-5.1,10.5-10.5,20.8-15.7,31.2c-8.4,16.6-17.1,33.2-25.2,49.9c-1.9,4-3.3,8.9-3,13.3
                        c0.7,7.7,7.5,10,12.7,4.4c4.5-4.8,8.4-10.4,11.5-16.2c9-17.1,17.4-34.5,26.1-51.7C1624,1569.8,1627.5,1563.1,1631,1556.4z"/>
                    <path d="M2359.5,1094.8c0.1,1.8,0.1,3.7,0.2,5.5c1.7,13.9,3.5,27.7,5.2,41.6c1.4,11.4,0.4,13.5-10.1,17.4
                        c-11.7,4.4-20.4,12.2-27.4,22.3c-11.2,16.3-19.2,34.2-25.6,52.8c-8.1,23.6-16,47.2-24.1,70.8c-8.7,25.7-21.2,49.5-36.7,71.8
                        c-2.1,3-4.3,6-7.1,8.3c-3.9,3.3-7.7,2.1-9-3c-0.9-3.2-0.9-6.6-0.9-9.9c-0.1-55-0.2-109.9,0-164.9c0.2-57.9,0.9-115.8,1.2-173.7
                        c0.1-24.3,4.4-47.6,13-70.3c6.3-16.5,14-18.3,25.9-5.5c18.5,20.1,37,40.2,54.5,61.1c16.6,19.8,28.5,42.5,36,67.3
                        C2355.7,1089.6,2357.9,1092.1,2359.5,1094.8z M2230.9,1380.6c3.7-4.6,6.1-7.2,8-10c11.8-17,21.8-35.1,29-54.6
                        c7.8-21.2,14.9-42.7,22.3-64.1c7.6-22,14.8-44.2,27-64.3c9-14.8,19.1-28.1,36.6-33.7c4.4-1.4,6.3-4.7,5.9-9.2
                        c-0.5-5-0.9-10-1.5-14.9c-3.8-35.8-14.4-69.4-36.9-97.8c-19.2-24.3-40.5-46.8-60.9-70.2c-0.7-0.7-1.4-1.4-2.2-2.1
                        c-6.9-5.8-10-5.3-13.4,3c-6.9,16.7-12.2,34-12.8,52.1c-0.9,28.6-1,57.3-1.1,86c-0.4,90-0.7,180-1,270
                        C2229.9,1373.1,2230.4,1375.5,2230.9,1380.6z"/>
                    <path d="M2541,794.5c-2.5,0.1-5,0.1-7.5,0.2c-4.3-1.6-8.6-3.3-13-4.7c-17.3-5.8-32.8-14.8-45.6-27.9c-24.6-25.3-49.9-49.9-78.5-70.9
                        c-8.1-5.9-10.2-13.6-7.8-22.9c6.4-24.8,20.4-43.7,43.5-55.5c10.7-5.4,21.4-6.3,32.8-2.2c27.5,9.9,52.8,23.4,75.3,42.3
                        c25.6,21.5,41,48.8,47.3,81.2c3,15.3,4.5,30.9,6.1,46.5c1.2,11.2-0.8,13.6-12.1,14.9C2568.1,797,2554.5,798.1,2541,794.5z
                        M2560.6,792.1c5.8-0.6,12.8-1.2,19.7-2.1c7.7-1,9-2.5,8.2-10.5c-1.3-12.2-2.6-24.5-4.6-36.7c-5.9-36.7-22.4-67.3-52.2-90.2
                        c-20.8-16-43.6-28.1-68.2-37c-10.4-3.7-20.2-2.9-29.8,2.3c-19.5,10.5-32.2,26.5-38.7,47.5c-3.7,12-1.6,16.1,8.3,23.8
                        c12.4,9.5,24.6,19.3,36.1,29.9c13,11.9,25.1,24.9,37.6,37.3C2499.7,779.2,2527.6,790.2,2560.6,792.1z"/>
                    <path d="M462.2,949.9c-1.4,3.2-3.2,6.3-4.1,9.7c-8.8,30.4-18.9,60.4-34.3,88.2c-5.2,9.3-11,18.2-16.5,27.3c-1.1,1.8-2.1,3.7-1.8,6.4
                        c7.8-9.4,15.5-18.8,23.3-28.1c2.1-2.5,4.2-5.2,6.8-7.3c6.3-5,13.6-3,15.7,4.7c3.7,13.7,5.3,27.9-0.6,41.2
                        c-10.4,23.6-16.9,48.3-23.8,73c-12.8,46-31,90-49.9,133.7c-14,32.4-30.2,63.7-45.3,95.6c-6.6,13.8-12.9,27.8-19.4,41.6
                        c-1.6,3.4-2.5,8.8-7.5,7.8c-4.8-0.9-4.5-6.1-4.6-10c-0.2-7.6-0.5-15.3,0.2-22.9c4-43.8,10-87.2,21.2-129.9
                        c10.7-41,25.3-80.6,39.5-120.4c1.1-3.1,2.1-6.3,3.4-9.4c3.7-9.3,6.9-18.2,2.5-28.7c-2.4-5.8-1.6-12.9-3.2-19.5
                        c-7.6,74.2-35.6,140.9-74,203.9c0.6,0.3,1.2,0.7,1.8,1c6.3-8.6,12.6-17.2,19.3-26.3c4.7,4.4,3.3,9.1,2.7,13.4
                        c-1.9,11.8-4.6,23.5-6.1,35.4c-3.8,30.7-7.6,61.5-10.6,92.3c-1.7,17.1,1.5,34,6.2,50.3c6.5,22.9,2.6,43-11.3,62.2
                        c-13.8,19.1-26.4,39.2-39.7,58.8c-6.9,10.2-13.7,20.5-21.1,30.3c-2.4,3.2-6.5,5.8-10.3,7.1c-5.1,1.7-9.2-2.4-8.5-7.8
                        c0.4-2.9,1.5-5.8,2.8-8.5c3.9-8.4,7.9-16.8,10.6-26c-1.2,1.8-2.5,3.5-3.5,5.5c-8.1,16.4-16.1,32.9-24.1,49.3
                        c-3,6.2-6.7,11.5-14,13.2c-5,1.1-10,1.9-14-2.4c-4-4.3-3.1-9.3-1-14c2.7-6.3,5.8-12.5,7.4-19.4c-3.7,6.8-7.2,13.6-11.2,20.2
                        c-2.4,3.9-5.2,7.9-8.6,10.8c-5.4,4.6-11.4,4.5-16.6,0.9c-5.1-3.5-7.2-9.3-4.9-16c2.1-5.9,5.1-11.6,7.7-17.3c1.3-2.9,2.6-5.8,2.6-9.5
                        c-2,1.9-4,3.9-6.1,5.7c-7,6.1-15.4,3.6-17-5.7c-0.8-4.7,0.1-9.9,1.3-14.6c1-3.8,3.4-7.2,5.4-10.6c12.2-21,24.5-42,36.7-63
                        c1.8-3.1,3.5-6.3,5.2-9.3c-4-5.8-8.9-5-13.9-4c-11.1,2.3-22.1,4.8-33.3,6.6c-9.7,1.6-17.7-2.1-23.3-10.2c-4.3-6.3-3.3-11.1,3.2-15
                        c1.7-1,3.5-1.9,5.4-2.5c14.6-5,26.1-14.2,36.6-25.3c14.4-15.3,29.3-30,44-45.1c6.1-6.3,12.9-11,21.7-13.6c6.2-1.8,12.7-5.2,17-9.9
                        c8.9-9.7,16.4-20.8,24.4-31.3c2-2.3,4-4.6,5.9-7c-0.6-0.5-1.2-0.9-1.8-1.4c-1.6,2.5-3.3,5.1-4.9,7.6c-6.8,7.3-13.4,14.7-20.4,21.8
                        c-2.3,2.3-5.7,3.6-8.5,5.3c-4.8-5.3-1.8-9.1,0.1-12.8c17.8-35.2,32-71.8,40-110.4c4.9-23.7,8.6-47.8,11.2-71.9
                        c5.7-52.8,19.4-102.6,49.7-146.9c3.4-4.9,6.7-10,10.8-14.4c7.4-8,13.3-7.5,19.5,1.3c1.7,2.4,3.2,4.9,5.8,8.9c-0.5-9.4-3.3-16-7.7-22
                        c-19-25.3-22.8-54.1-19.3-84.5c4.3-36.8,19.7-68.8,43.3-97.1c2.3-2.8,5-5.3,6.7-7.2c-1.6-3.3-4.1-6.1-4-8.8
                        c1.4-29.9-1-60.1,4.7-89.7c7.6-39.2,31.5-67.6,63.4-89.9c21.2-14.8,45.2-22.5,70.7-25.2c9.1-0.9,17.6-3,25.9-6.9
                        c30.5-14.1,61.1-27.9,91.7-41.9c19-8.6,19-8.6,29.3,8.9c10.1,17.1,23.5,31.4,36.9,45.8c4.3,4.6,8.8,9.1,13.2,13.7
                        c3.2,3.4,6.1,7,5.7,12.5c-4.6,3.7-9.4,3.9-14.9,2.4c-22.8-6.2-45.8-11.7-68.5-18.2c-18.7-5.4-36.7-5.9-54.1,4.1
                        c-4.5,2.6-7.9,2.1-11.4-1.9c-4.8-5.5-9.8-10.8-19.9-12.8c3.1,2.7,4.5,4,6,5.2c1.8,1.5,3.7,2.8,5.5,4.3c10.8,8.8,12,12.8,8.2,25.9
                        c-0.6,2.2-0.8,4.5-1.2,6.7c-1.3,3.4-2.5,6.8-3.8,10.2c-1.4,2.5-2.8,4.9-4.2,7.4c0.6,0.3,1.3,0.5,1.9,0.8c0.7-2.8,1.4-5.6,2.1-8.4
                        c1.3-3.4,2.5-6.8,3.8-10.2c2.1-2.8,4.3-5.5,6.2-8.4c7.8-11.9,18.4-20.5,32.5-23c6.3-1.1,13.5-0.7,19.6,1.2
                        c24.4,7.8,48.6,16.1,72.6,25c24.2,9,37.1,26.8,39.4,52.7c2.3,26.2,4.1,52.4,1.9,78.8c-3.3,39.4-38.2,74.6-77.5,77.9
                        c-13.7,1.2-27.3,1.3-40.7-1.8c-1.2-0.3-2.6,0-4.3,1.4c3.9,1.3,7.8,2.7,11.7,3.9c13.8,4.2,16.7,8.4,14,22.7
                        c-4.5,23.5-9.4,47-14.1,70.5c-0.1,0.7-0.4,1.3-0.5,1.9c-2.9,30.4-17,54.9-38.8,75.5c-5.3,5-10.2,10.5-15.1,15.9
                        c-10,11-17.2,23.7-22,37.7c-0.7,1.9-0.7,4.2-1.8,5.6c-1.5,1.9-3.7,4.1-5.8,4.5c-1.5,0.2-4.2-2.2-5-4c-2.6-6.1-5.6-12.3-6.4-18.7
                        c-2.8-22.8-5.1-45.7-7-68.6c-2.1-25.6-4.1-51.1-5.1-76.8c-1.1-29,2.1-57.9,5.5-86.7c0.2-1.7,0.5-3.3,1-4.9c1.9-7.2,5.5-9.6,12.5-7.4
                        c8.6,2.7,16.8,6.2,25.2,9.4c3.3,1.3,6.7,2.6,10.7,2.6c-1.6-1.1-3.1-2.3-4.8-3.2c-18.8-10.3-35.6-23.5-51.5-37.7
                        c-10.8-9.6-12.2-16.8-6.6-30.2c2.4-5.7,4.8-11.3,6.7-17.1c-14.9,17.5-14.9,17.5-38.4,26.6c-4.3,1.7-8.5,3.4-13.8,5.6
                        c2.4,0.9,3.6,1.3,4.8,1.8c10.1,4,13.4,8,12.8,18.8c-0.8,12.9-2.3,25.9-4.5,38.6c-3,17.4-7.1,34.5-10.7,51.8c-0.5,2.6-0.4,5.3-0.5,8
                        c-0.6,1.7-1.1,3.4-1.7,5c-1.4,5.1-2.8,10.3-4.2,15.4c0,0,0.2-0.5,0.2-0.5c-1.4,4.8-2.8,9.7-4.2,14.5
                        C461.9,950.3,462.2,949.9,462.2,949.9z M737.9,753.6c-1-14.5-1.6-27.5-2.9-40.4c-2.3-23.9-15.3-39.9-37.8-48
                        c-22.8-8.2-45.7-16.3-68.7-24.1c-12.2-4.2-23.6-2.1-34.1,5.4c-9.5,6.7-16,15.6-20.7,26.3c-7.7,17.7-15.9,35.1-24.2,52.5
                        c-9.8,20.4-20.1,40.5-30,60.8c-3.8,7.9-2.2,14.9,4.3,20.7c20.9,18.8,43.2,35.5,68.9,47.2c19.4,8.8,39.6,13.4,61,12.2
                        c42.7-2.3,70.7-24.9,80.9-64.4C739,785.2,737.7,768.6,737.9,753.6z M172.7,1650.9c12,1.4,15.5-0.4,20.6-10.6
                        c9-17.8,17.7-35.8,26.6-53.6c1.6-3.2,3.4-6.2,5.1-9.2c9.7,4.8,10.2,5.7,6.6,13.7c-3.3,7.3-7.1,14.3-10.5,21.5
                        c-1.6,3.4-2.6,7.1-3.9,10.7c0.6,0.5,1.2,1.1,1.8,1.6c2.6-1.3,6-2.1,7.8-4.2c4.7-5.6,9.5-11.4,12.8-17.8c13.1-25.2,29.6-48.2,47-70.5
                        c14.4-18.5,17.6-39.1,10.1-61.3c-4.7-13.8-6.4-27.9-5.1-42.3c3.6-39.4,4.9-79.1,14-117.9c0.8-3.4,2.5-7.5-0.9-12.2
                        c-2.7,3.5-5,6.3-7,9.2c-18.7,26.3-37.4,52.7-56.1,79c-7.8,11-17.6,18.7-30.9,22.2c-4.6,1.2-9.2,4.1-12.6,7.4
                        c-17.7,17.2-35.1,34.6-52.3,52.3c-9.2,9.4-19,17.6-31.6,22c-3.9,1.4-7.3,3.9-10.8,5.9c6.6,12.5,12.1,15.2,24.8,12.6
                        c9.8-2,19.5-4.5,29.2-6.5c8-1.6,15,0.1,20.7,6.7c-2,3.5-3.7,6.7-5.5,9.9c-13.1,22.8-26.4,45.5-39.4,68.4c-2.4,4.3-4.1,9-5.7,13.7
                        c-1.2,3.6-1.7,7.8,2.2,9.8c4.5,2.3,7.5-1.2,10-4.4c2.4-3.2,4.5-6.6,6.6-10c12.5-20.9,24.9-41.8,37.3-62.6c2-3.3,4.2-6.5,6.4-9.8
                        c7.4,5.6,7.5,5.6,3.3,13.2c-12.6,22.7-25.4,45.3-37.8,68.1c-5.1,9.3-9.6,19-14.1,28.7c-3.3,7.2-2.4,11.4,2.1,14.1
                        c4.4,2.5,8.6,1.1,13.4-5.2c3-4,5.5-8.3,7.9-12.7c12.3-22.8,24.5-45.8,36.8-68.6c1.8-3.4,4-6.6,6.1-10.1c7.4,7.1,7.8,7.9,4.3,14.8
                        c-2.9,6-6.3,11.7-9.3,17.7c-9.1,18.1-18.3,36.2-27.1,54.5C174.2,1642.4,173.9,1646.4,172.7,1650.9z M371.8,1116.1
                        c0.8,0.3,1.6,0.5,2.5,0.8c3.6-5.5,7.4-10.8,10.7-16.5c14.4-24.7,30.6-48.6,42.6-74.4c26.3-56.7,42.6-116.7,51.8-178.6
                        c1.5-10.2,2-20.6,2.2-30.9c0.2-10.5-4.9-14.4-15.2-12.8c-1.3,0.2-2.6,0.4-3.9,0.9c-16.4,5.5-32.9,10.7-49.2,16.5
                        c-15.7,5.6-28.9,15.3-39.1,28.4c-24.6,31.8-37.7,67.8-37.3,108.3c0.2,19.4,4.5,37.7,15.9,53.7c10,14.1,14.4,29.7,15.2,46.7
                        C369,1077.7,370.6,1096.9,371.8,1116.1z M386.1,822.8c4.2-2.4,7.7-4.3,11.1-6.2c17.1-9.8,35.2-17.1,54.3-22.3
                        c14.7-4,29.3-8.8,43.8-13.4c7.6-2.4,13.7-7,17.6-14c13.7-24.7,27.8-49.2,40.6-74.4c5.8-11.5,9.1-24.2,13.3-36.5
                        c2.5-7.2,0.1-13.1-5.8-17.6c-4.8-3.6-9.6-7.2-14.4-10.8c-7.4-5.5-15.6-7-24.6-5.2c-5.5,1.1-11.1,2.2-16.6,3.2
                        c-51.2,9.1-86.1,39.4-109.2,84.6c-3.1,6.1-5.1,13-6.5,19.8c-5.9,29.2-6.4,58.8-5.1,88.5C384.5,819.5,385.1,820.4,386.1,822.8z
                        M546.6,1098.4c0.8-0.6,1.6-0.8,1.9-1.3c0.8-1.4,1.6-2.9,2.1-4.4c6.2-16.9,16.5-31,28.9-43.8c7-7.1,13.8-14.4,20.5-21.8
                        c11.7-12.8,19.3-27.7,22.5-44.9c2.2-11.4,4.7-22.8,6.9-34.2c3.4-17,6.7-33.9,9.9-50.9c2.4-12.8,1-15-11-19.1
                        c-9.1-3.1-18.2-6.3-27.2-9.7c-17.8-6.6-35.4-13.4-53.2-20c-11.1-4.1-13.2-2.7-14.4,9.4c-1.3,13.2-2.6,26.5-3.3,39.8
                        c-3.3,62.7,2.5,125,9.7,187.2C540.7,1089.8,541.6,1095.2,546.6,1098.4z M306.8,1428.6c1.8-1,2.6-1.1,2.9-1.6c1.1-1.7,2.1-3.4,3-5.2
                        c38.4-75.8,73.8-152.9,101.3-233.4c3.9-11.3,7.2-22.8,10.2-34.4c5.2-20.3,10.5-40.6,19.7-59.6c5.6-11.6,5.5-23.9,3.4-36.2
                        c-0.6-3.2-2.8-6.2-4.9-10.5c-3.6,3.4-6.1,5.4-8.2,7.9c-10,12-19.8,24.2-29.9,36.3c-14.4,17.3-25.7,36.5-33.5,57.6
                        c-10.3,27.8-20.9,55.5-30.1,83.6c-17.4,53.3-29.7,107.8-33.8,163.9C306.2,1407.1,306.8,1417.4,306.8,1428.6z M230,1375.1
                        c0.5,0.3,0.9,0.7,1.4,1c0.8-0.5,1.8-0.8,2.4-1.5c13.3-17.5,27.2-34.5,39.6-52.7c47.5-69.6,78.9-145.3,85.7-230.2
                        c1.4-16.9-0.1-33.3-6.6-48.9c-1.6-3.7-4.2-8.4-7.5-9.6c-5.1-1.9-7.8,3.6-10.5,7.3c-23.9,32.7-40.3,68.9-48.1,108.6
                        c-4.1,21.2-5.9,42.8-8.7,64.2c-6.8,52.5-20.8,102.9-43.1,150.9C232.8,1367.9,231.5,1371.5,230,1375.1z M551.5,614.4
                        c6.9,4.8,13.4,8.4,18.8,13.2c5.9,5.3,11.5,5.8,18.2,2.5c13.5-6.6,27.5-6.7,41.8-2.8c22.5,6.1,44.9,12,67.5,17.8
                        c5,1.3,10.2,2,17.5,3.4c-2.8-3.9-4-6.2-5.7-8c-19.6-21.2-40.8-41.2-54.1-67.5c-1.8-3.5-4.9-4.2-8.5-2.9c-1.9,0.7-3.7,1.5-5.5,2.3
                        c-26.9,12.3-53.8,24.6-80.7,37C557.8,610.8,555,612.5,551.5,614.4z M459.5,799.5c-14.6,0.4-39.6,9.2-48.6,16.7
                        C427.7,810.4,443.6,805,459.5,799.5z M548.5,717.6c-0.8-0.4-1.5-0.9-2.3-1.3c-4.7,9.3-9.3,18.5-14,27.8c0.6,0.3,1.2,0.6,1.8,0.9
                        C538.8,735.9,543.7,726.7,548.5,717.6z"/>
                    <path d="M1023.4,872.9c-2.4-16.1-4.9-32.2-7-48.3c-1.6-12.6,0.3-23.8,17.5-27.3c-3.8-2.1-5.5-3.4-7.5-4.1c-6.5-2.4-13.1-4.8-19.8-7
                        c-9.9-3.4-17.4-9.6-22.3-18.9c-14.7-28-29.8-55.8-43.9-84.1c-4.6-9.1-7.1-19.3-9.7-29.3c-2.1-7.9,1.6-14.3,7.8-19.2
                        c4.9-3.8,10-7.5,16.4-12.3c-8.9-1.1-14.4,2.6-18.2,7.1c-8.8,10.4-17.6,11.2-29.5,4.7c-11.2-6.2-23.7-4.8-36.1-1.4
                        c-25.1,6.8-50.2,13.2-75.4,19.6c-3.2,0.8-6.5,1.6-9.8,1.5c-5.9-0.2-8.1-4.4-5.1-9.6c1.3-2.3,3.3-4.2,5-6.2
                        c9.4-10.4,18.8-20.7,28.3-31c9.6-10.3,17.9-21.4,24.9-33.7c6.3-11.3,8.8-11.9,20.8-6.5c28.8,13,57.7,25.8,86.2,39.5
                        c12.5,6,25.3,9.7,39,11.3c52.8,6.2,92.7,32.7,121,77.4c10.3,16.2,14.6,34.1,15.6,53.1c1.1,23,2.3,45.9,3.3,68.9
                        c0.2,3.7-0.7,7.5-1.3,12.8c-7.8-3.7-13.9-6.7-20.1-9.6c-0.3,0.3-0.5,0.7-0.8,1c1,0.8,2,1.8,3.1,2.4c13.2,6,22.2,16.4,30.7,27.7
                        c25.6,33.9,36.9,71.9,35,114.1c-0.8,17.7-5.8,34-16.2,48.6c-4.8,6.7-9.3,13.7-9.7,23.8c2.6-3.1,4.1-5.1,5.9-7
                        c5.4-5.5,9.7-5.7,15.6-0.4c2.7,2.4,5,5.4,7.1,8.3c30.6,42.7,48.3,90.2,53.2,142.4c6.5,68.2,21.5,134,53.4,195.2
                        c1.8,3.4,5.1,7.7,0.7,10.7c-4.2,2.9-7.3-1.4-10-4.2c-7.4-7.6-14.6-15.4-21.9-23.1c-1.5-2.5-3-5-4.5-7.5c-0.6,0.4-1.2,0.9-1.8,1.3
                        c1.9,2.3,3.9,4.5,5.8,6.8c7.9,14.5,18.7,26.6,30.9,37.5c2.3,2.1,5.8,3.1,8.9,4.1c10.1,3.1,18.7,8.4,26.1,16
                        c15.9,16.6,32.2,32.8,47.9,49.6c7.3,7.7,15.5,13.5,25.4,17.2c4.3,1.6,8.7,3.5,12.6,6c5.2,3.4,5.8,7.4,2.8,12.9
                        c-5.3,9.3-15.9,13.9-27.6,11.5c-9.8-2-19.5-4.3-29.3-6.4c-6.3-1.3-6.3-1.3-13.9,3.2c1.5,3.1,2.9,6.3,4.6,9.3
                        c12.8,22.2,25.7,44.4,38.5,66.6c3.2,5.5,5.8,11.2,5.5,17.8c-0.2,4.9-1,9.5-5.6,12.1c-4.8,2.7-9,0.6-12.9-2.4c-1.3-1-2.6-2.1-5.4-4.3
                        c1.1,3.6,1.4,5.6,2.2,7.4c2.6,6.1,5.8,12,8.1,18.2c2.5,6.9,0.3,13.1-5.2,16.6c-5.9,3.8-12.6,2.8-18-3c-2.5-2.7-4.5-5.8-6.6-8.8
                        c-1.9-2.7-3.7-5.4-6.9-7.6c1.1,3.3,2.3,6.6,3.2,10c2.5,9.6-2.2,14.9-12,13.8c-8-0.9-13.2-5.6-16.6-12.7
                        c-5.8-12.3-11.8-24.6-17.7-36.9c-1.6-3.3-3.3-6.4-6.5-9.2c0.6,1.8,0.9,3.8,1.7,5.5c2.4,5.5,5.5,10.7,7.3,16.4c0.8,2.4,0.2,6.6-1.5,8
                        c-2.1,1.8-6.3,2.9-8.9,2.1c-3.2-0.9-6.5-3.6-8.5-6.4c-6.5-9.2-12.4-18.9-18.7-28.3c-11.8-17.7-23.7-35.4-35.5-53.1
                        c-4.5-6.6-9.4-13-13.4-19.8c-8.3-14.2-10.4-29.6-5.4-45.3c7-22.4,8.8-44.8,5.3-68.1c-2.4-16.1-2.4-32.5-4.5-48.7
                        c-2.5-19.8-6.1-39.4-9.1-59.2c-0.9-6.4-1.2-12.9-1.9-21.1c9.2,4.5,11.7,12.2,16.3,17.9c4.7,5.8,8.8,12.1,14.1,17.7
                        c-43.3-68-72.7-141-79-222.3c-0.9,0.2-1.8,0.3-2.8,0.5c0,4.4,0,8.9,0,13.3c-0.1,7.6,0,15-2.7,22.6c-1.5,4.3,0.5,10.4,2.2,15.3
                        c7.4,21,15.6,41.7,23.1,62.8c16.9,47.8,29.9,96.7,37,146.9c3.7,26,4.4,52.4,6.5,78.6c0.1,1.6,0.2,3.6-0.5,4.9
                        c-1.1,1.8-2.7,3.9-4.5,4.5c-1.2,0.4-3.4-1.5-4.8-2.7c-0.9-0.8-1.3-2.3-1.9-3.5c-21.3-44.8-43.4-89.2-63.6-134.5
                        c-24.1-54.1-44.2-109.9-60-167.1c-3.5-12.5-8.3-24.6-12.8-36.8c-4.3-11.7-7.2-23.6-5.6-36.2c0.5-3.9,1.2-7.9,2.5-11.7
                        c3.4-9.5,9.4-11,16.4-3.9c5.4,5.4,10.3,11.4,15.3,17.2c4.6,5.3,9,10.7,13.6,16c0.3,0.3,1,0.4,2.7,1c-1.2-2.2-1.7-3.6-2.6-4.8
                        c-20.3-26.9-33.1-57.6-43.7-89.3c-1.1-3.3-2.9-6.4-4.3-9.6c0,0,0.3,0.4,0.3,0.4c-1.4-4.8-2.8-9.6-4.2-14.4c-0.3-1.9-0.6-3.8-0.9-5.8
                        c-1.7-4.8-3.5-9.6-5.2-14.3l0.2,0.4c-1.4-5.1-2.8-10.2-4.2-15.4c-0.3-2.2-0.6-4.5-0.8-6.7c-3.7-13.8-7.4-27.5-11.2-41.3
                        C1023.8,878.2,1023.6,875.6,1023.4,872.9z M1084,812c-3.4-1.3-6.8-2.6-10.1-3.9c-7.5-4.9-11.2-5.9-20.2-4.5c8.3,1.7,14.3,3,20.4,4.3
                        c3.4,1.3,6.8,2.6,10.1,3.9c3.2,3.7,7.6,4.7,12.5,4.2C1092.4,814.7,1088.2,813.4,1084,812z M1313.8,1525.3c2.1,3.2,4.3,6.5,6.3,9.9
                        c12.8,21.5,25.5,42.9,38.3,64.4c1.9,3.1,3.9,6.2,6.3,9c2.2,2.5,5.5,5.4,8.5,2.7c2-1.8,3.3-6,2.8-8.7c-1-4.8-3.2-9.4-5.6-13.7
                        c-13-22.9-26.2-45.6-39.3-68.5c-2.1-3.7-3.8-7.5-5.8-11.4c8.1-6.7,16.4-6.9,25.2-4.9c9.4,2.1,18.8,4.6,28.3,6
                        c11.1,1.7,17.2-2.3,21.2-13.5c-5.7-2.8-11.6-5.5-17.3-8.5c-5.8-3.1-13-5.3-16.8-10.1c-16.8-20.9-36.8-38.6-55.8-57.2
                        c-5.1-5-11.6-9.6-18.3-11.7c-10.7-3.5-18.9-9.4-25.7-17.9c-4.6-5.7-9.6-11.1-13.8-17.1c-15.9-22.2-31.5-44.7-47.2-67
                        c-2.2-3.1-4.8-5.9-7.2-8.8c-1.4,3.2-1.3,5.4-0.9,7.6c1.7,8.8,4,17.6,5,26.5c3.5,30.4,6.9,60.9,9.7,91.4c1.3,14.2,1.5,28.5-3.6,42.5
                        c-9.8,26.8-4.9,51,13.4,72.7c15.7,18.7,28.9,39,40.4,60.6c4.2,7.9,9.9,14.9,15.3,22.2c2.5,3.3,5.6,5.8,10,1.2
                        c-5.2-10.6-10.4-21-15.3-31.5c-3.9-8.3-2.9-10.1,6.7-13.8c2,3.7,4.1,7.4,6,11.2c8.5,17,16.8,34,25.5,50.9c1.9,3.7,4.8,7.3,8.2,9.6
                        c2.7,1.8,7.7,3,10,1.7c4.2-2.4,1.8-7,0.3-10.7c-0.6-1.5-1.3-3.1-2.1-4.5c-10-19.6-20-39.2-29.9-58.7c-2-3.9-4.3-7.6-6-11.5
                        c-2.7-6.4-1.9-7.9,6.1-12.4c1.7,3.1,3.5,6.2,5.3,9.4c12.6,23.5,25.2,46.9,37.9,70.3c2.4,4.4,5.1,8.6,8.3,12.4
                        c4.2,5.1,8.1,5.9,12.1,3.3c4.5-3,4.5-7.1,2.6-11.5c-1.5-3.7-2.9-7.4-4.8-10.9c-15-27.8-30.1-55.6-45.2-83.4
                        c-1.4-2.6-3.1-5.2-4.3-7.9C1305.9,1529.5,1306.3,1529.1,1313.8,1525.3z M1129.8,1116.7c0.6-0.2,1.2-0.4,1.8-0.6
                        c0.4-3.5,1-7,1.1-10.5c0.5-12.6,0.7-25.3,1.3-37.9c1-21.3,6-41.3,18.5-59.2c8.6-12.2,12.8-26.2,13.8-41.2c2.6-39.4-8-75-29.5-107.8
                        c-10.9-16.6-25-29.8-43.4-37c-18.5-7.2-37.7-12.9-56.8-18.6c-8.6-2.6-13.5,1.2-14.5,10.1c-0.6,4.9-0.3,10,0.2,14.9
                        c5.4,48.1,15.7,95.1,31.6,140.9c11.4,32.7,22.4,65.5,41.8,94.7c9.8,14.7,19,29.7,28.5,44.6C1126,1111.8,1128,1114.2,1129.8,1116.7z
                        M1118.4,822.5c0.7-2.3,1.1-3.2,1.2-4.1c0.5-29,0.4-57.9-5.1-86.6c-2.6-13.3-7.4-25.4-14.8-36.7c-25.9-40-62.3-63.3-109.3-71.5
                        c-20-3.5-35.1,3-49,15.9c-4.9,4.6-6.5,10-4.9,16.3c1.2,4.8,2.8,9.6,4.4,14.3c12.8,35.6,32.3,67.8,52.2,99.7
                        c2.6,4.1,7.6,7.7,12.3,9.5c13,5,26.3,9.4,39.7,13.1c22.8,6.4,45.1,14,65.5,26.3C1112.7,820.1,1115.2,821,1118.4,822.5z
                        M1195.8,1429.7c0.8-0.2,1.5-0.4,2.3-0.7c0-3.2,0.3-6.5,0-9.6c-2.8-26.8-4.5-53.8-8.9-80.3c-10.6-64.6-32-126.3-55.2-187.3
                        c-14.3-37.6-39.7-68.3-66-98.1c-1.1-1.2-2.4-3-3.8-3c-2-0.1-4.8,0.4-5.8,1.7c-1.5,2-2,4.9-2.4,7.5c-1.8,12.8-0.4,25.1,4.3,37.2
                        c4.4,11.1,8.6,22.4,12.1,33.8c4.6,14.9,7.3,30.5,12.8,45.1c17.3,46.1,34.2,92.4,53.6,137.6c16.4,38.2,36,74.9,54.3,112.3
                        C1193.9,1427.2,1194.9,1428.4,1195.8,1429.7z M1158.2,1032.4c-1.9,2.5-4.3,5.2-6.1,8.2c-5.7,10.2-7.1,21.3-7.3,32.8
                        c-0.4,28.1,3.1,55.8,10,83.1c20,79.6,58.4,149.7,110.7,212.4c1.6,1.9,3.6,3.6,5.4,5.3c0.4-3.8-0.6-6.9-1.9-9.7
                        c-19.7-42.9-33.8-87.4-39.7-134.4c-3.2-26.1-6.7-52.2-11.6-78c-7.5-40.1-23.8-76.8-47.6-110
                        C1167.4,1038,1164.8,1033.5,1158.2,1032.4z M949,614.3c-2.4-1.5-3.4-2.4-4.6-2.9c-29-13.3-58-26.5-87-39.7
                        c-7.8-3.5-8.8-2.9-13.9,4.2c-9.3,12.9-18.4,26-28.3,38.5c-6.8,8.6-14.8,16.2-22.2,24.3c-2.1,2.3-3.8,5-5.7,7.5
                        c0.4,0.7,0.8,1.3,1.2,2c5-1.1,10.1-1.9,15-3.2c22.8-5.9,45.7-11.7,68.4-17.9c13.4-3.7,26.2-3.4,38.9,2.5c2.4,1.1,4.9,2.5,7.4,2.8
                        c2.4,0.3,5.6,0.3,7.5-1C933.3,626.2,940.6,620.5,949,614.3z"/>
                    <path d="M809.6,1199.6c13.1-2,26.3-3.3,39.2-6.3c5.1-1.2,10.2-5.1,13.9-9c16.3-17.2,23.3-38.5,25.3-61.6c2.4-26.5-1.1-52.5-6.9-78.3
                        c-1.1-5.1-1.4-10.3-2.4-18.1c6.5,3.7,11.1,5.5,14.9,8.5c16.6,13.1,30.9,28.4,43.1,45.6c9.9,13.9,11.9,28.4,5.1,44.3
                        c-7,16.4-15.5,31.7-27.7,44.8c-11.5,12.5-25,21.9-41.4,27c-2.5,0.8-4.9,1.8-7.4,2.7c0.5,54-10.9,105.7-24.3,157.3
                        c0.7-1.3,1.6-2.6,2.1-3.9c14.2-39.6,26.5-79.6,27.3-122.2c0.1-4.3,0.8-8.6,1.4-12.9c1.2-8.8,5.4-16,13-20.3
                        c25.7-14.3,43.5-35.8,56.9-61.4c1.2-2.3,2.8-4.3,5.3-7.9c2.5,3.9,5.1,6.5,6,9.6c9.7,34.4,17.5,69.1,13.1,105.3
                        c-1.6,13-4.9,25.5-10.3,37.4c-24.3,53.9-59.4,99.3-107,134.6c-7.7,5.7-16.4,10.2-25,14.5c-8.2,4.2-8.6,3.8-16.4-2.7
                        c-14.8,17.4-32.4,30.9-56.6,30.6c-24.5-0.3-41.4-14.4-56-34c-0.9,3.4-1.5,5.7-2.4,9.2c-5.1-1.3-10.2-1.8-14.5-3.8
                        c-7.2-3.4-14.4-7.1-20.7-11.9c-45.8-34.5-81.1-77.6-105.9-129.4c-14-29.3-17.9-59.7-12.4-91.5c3-17.4,6.5-34.7,9.8-52
                        c0.5-2.6,1.1-5.3,2.2-7.7c2.5-5.3,7.5-5.7,11-1c1,1.3,1.5,2.9,2.4,4.4c13.1,23.3,29.6,43.5,53.6,56.5c8.8,4.8,13.5,12.5,14.6,22.5
                        c2.4,21.5,4.4,43.1,7.7,64.5c3.8,25.1,12.9,48.8,22,72.5c0.2,0.4,0.8,0.7,2.7,2.2c-8.5-26.8-15.3-52.3-19.3-78.6
                        c-4-26-7.8-52-6.3-78.4c-1.7-0.9-2.7-1.7-4-2c-21-5.2-37.5-17.3-49.6-34.8c-8-11.5-15.2-23.5-21.7-35.9
                        c-10.3-19.7-7.5-35.9,7.6-52.1c12.5-13.4,25.2-26.7,37.8-39.9c3.4-3.6,6.9-7.4,13-6.3c3.8,3.6,2,7.9,1.3,12
                        c-2.2,12.8-5,25.5-6.6,38.4c-2.7,21.9-2.9,43.9,2.9,65.5c4.2,15.4,11.8,28.9,22.4,40.8c4.2,4.7,9.5,6.6,15.6,8
                        c23.9,5.6,47.8,11.5,71.6,17.8c8.8,2.3,13.9,9.1,15.1,18.1c1.5,11.6,2.3,23.2,3.4,34.8c0.9,9.9,1.9,19.8,4.2,29.9
                        c0.4-2.7,1-5.5,1.3-8.2c1.5-15.9,3.3-31.8,4.4-47.7c1.1-15.4,7.9-25.8,23.4-29.6c1.2-0.3,2.2-1.1,3-3.4c-3,0.5-6,1-9,1.5
                        c-13.6,2.3-20.3-2.9-21.1-16.8c-0.6-10-0.6-20-0.7-30c-0.1-16.3-0.5-32.7,0.1-49c0.4-9.2,2.2-18.4,3.9-27.6
                        c1.3-7.4,6.1-11.4,13.8-12.2c33.4-3.2,65,2,94.3,19.2c9.9,5.8,13.8,13.7,12.3,24.9c-2.9,21.7-9.5,42.1-20.5,61.1
                        c-4.7,8.1-11.8,12.5-20.5,14.8c-9.3,2.5-18.6,4.6-28,6.8C809.1,1199,809.3,1199.3,809.6,1199.6z M746.4,1451.7
                        c0.3-6.1,0.9-11.7,0.9-17.2c-0.1-34-0.3-68-0.7-101.9c-0.4-32-2.1-63.9-6.4-95.6c-1.7-12.6-8.2-19.8-20.3-22.7
                        c-15.2-3.8-30.3-7.9-45.4-11.6c-5.1-1.3-10.4-2.4-15.7-2.8c-11.6-0.8-15.3,2.5-15.1,14.2c0.1,9,1.2,17.9,2.1,26.9
                        c4,39.2,11.2,77.7,23.9,115.1c8.3,24.4,18.4,47.8,33.8,68.7C714.1,1438.9,728,1447.6,746.4,1451.7z M756.9,1451.7
                        c18.8-3.6,31.7-12.3,42-25.2c10.7-13.4,18.4-28.5,25.1-44.2c18.8-44.2,28.9-90.6,33.9-138.1c1.1-10.3,1.8-20.6,1.7-30.9
                        c-0.1-10.9-5-14.8-15.9-14.2c-1.3,0.1-2.6,0.4-4,0.5c-21.9,3-42.8,9.8-63.4,17.5c-6.5,2.4-10.5,7-11.4,13.9
                        c-1.8,14.2-4.7,28.3-5.1,42.6c-1.4,49.9-2.1,99.9-2.9,149.9C756.8,1432.3,756.9,1441.2,756.9,1451.7z M816.3,1423.5
                        c9.8,1.2,16.3-3.6,22.7-8.2c49.2-35.4,86.1-81,111.3-136.1c8.4-18.3,12.1-37.3,11.6-57.2c-0.6-24.1-4.5-47.7-10.2-71.1
                        c-0.9-3.6-2.7-7.1-4.6-12.1c-3.6,5.7-6.3,9.9-8.9,14c-11.3,18-25,33.9-43.7,44.4c-12.9,7.2-17.6,17.4-18.6,31.7
                        c-1.3,18.9-3.6,37.8-7.2,56.4c-8.5,43.4-25,84.1-45.2,123.3C820.9,1413.3,818.8,1418.1,816.3,1423.5z M687.8,1423.6
                        c-1.6-3.6-2.9-6.9-4.5-10.2c-4.7-9.6-9.6-19-14.1-28.7c-18.1-38.4-32.1-78.2-38.1-120.3c-2-14.1-2.7-28.5-3.6-42.7
                        c-0.5-8.5-3.1-15.7-10.9-20c-23.2-12.6-40.2-31.4-53.8-53.6c-1.6-2.6-3.7-4.9-6.7-8.7c-1.5,4.9-2.7,7.6-3.2,10.5
                        c-3.1,16.7-6.3,33.3-9.2,50c-5.1,29.5-0.3,57.6,12.4,84.5c15.9,33.7,36.7,64.1,62.6,90.9c15.8,16.3,32.8,31.2,51.7,43.9
                        C675.4,1422.5,680.4,1425.9,687.8,1423.6z M759.9,1145.1c-0.1,0-0.2,0-0.3,0c0,14-0.2,28,0,42c0.2,14.4,4.4,17.5,18.8,14.1
                        c1.9-0.5,3.9-0.9,5.8-1.4c15.8-4,31.6-8.2,47.4-12.1c11.4-2.8,19.5-9.4,24.7-20c7.8-15.6,13-32.1,15.7-49.3
                        c2.2-13.8,0.5-17.1-11.9-24.2c-23.8-13.6-49.6-18.9-76.8-18.1c-18.6,0.6-21.6,1.8-23.3,22C758.9,1113.7,759.9,1129.4,759.9,1145.1z
                        M865.3,1192.1c2.9-0.5,4.5-0.7,6-1.1c14-4.3,26-11.8,36.1-22.4c13.1-13.6,22.6-29.5,29.5-47c5.3-13.6,3.8-25.9-4.6-37.7
                        c-11.4-16.2-24.7-30.5-40-43c-1.5-1.2-3.6-1.8-5.6-2.9c-0.3,1.6-0.6,2.3-0.5,2.9c0.2,1.6,0.5,3.3,0.9,4.9
                        c5.4,23.1,7.7,46.6,6.8,70.3c-1,25.6-7.4,49.3-24.4,69.2C868,1187,867.2,1189.1,865.3,1192.1z M617.9,1039.1
                        c-0.7-0.3-1.4-0.5-2.1-0.8c-1.1,0.7-2.4,1.1-3.2,2c-13.9,14.8-27.9,29.5-41.5,44.6c-9.2,10.2-10,22.3-5,34.6
                        c7.6,18.6,18,35.6,31.7,50.5c8,8.7,17.5,15.4,28.4,19.9c3.5,1.4,7.4,1.8,11.3,2.7c0-1.9,0.2-2.7,0-3.2c-1-1.7-2.2-3.3-3.4-4.9
                        c-10.7-12.8-17.3-27.4-20.7-43.7c-6-28.9-3.9-57.6,1.8-86.2C616.1,1049.4,616.9,1044.2,617.9,1039.1z"/>
                    <path d="M983,1943.7c-0.6-1.1-1.2-2.2-2.2-4.1c-0.4,1.7-1,2.9-1,4c-0.8,35.3-1.5,70.6-2.3,105.9c-0.1,4.9-0.5,9.8-0.9,17.4
                        c-4.1-3.9-7-5.8-8.9-8.5c-20.7-29.4-41.7-58.7-59.1-90.2c-15.7-28.3-24.6-58.9-30.3-90.5c-10.1-55.9-15.6-112.2-14.4-169
                        c0.7-32.1,7.1-63.5,14.8-94.5c13.5-54.2,27-108.5,40.5-162.7c0.4-1.5,0.4-3.2,0.8-5.9c-7.2,12.2-28.6,83.4-50.4,169.3
                        c-14.5,57-28,114.2-42.1,172c-5.1-0.9-6.4-5-7.3-9.1c-6.1-28.3-12.8-56.5-18-85c-6.5-36-12.7-72.2-17.3-108.5
                        c-3.4-27-4.1-54.4-5.9-81.7c-0.2-3.3,0.3-6.7,0.4-10c0.5-17,8.6-28.1,23.9-36c47.8-24.9,87-59.9,117.7-104.4
                        c4.7-6.8,8.7-14.2,13.1-21.3c1.6-2.6,3.3-5.6,7-4.1c3.5,1.4,3.2,4.7,2.7,7.8c-1.2,7.5-2.5,15-2.4,22.9c2.2-7.3,4.2-14.6,6.6-21.8
                        c1.1-3.4,2.5-7,4.6-9.7c2.6-3.3,6.4-3,8.1,0.8c1.7,3.8,2.6,8.2,2.7,12.4c0.3,24.3-0.4,48.7,0.5,73c0.8,22.9,2.8,45.9,4.8,68.8
                        c3.5,39.1,7.5,78.2,11.1,117.4c2.2,23.2,4.8,46.4,5.8,69.7c1.4,34.6,1.6,69.3,2.2,103.9c0.1,5.7,0,11.3,0.7,17
                        c1.5-34.9,5.3-69.7,2.3-104.6c-4.5-52.4-9.7-104.8-14.4-157.2c-4.4-48.4-10.5-96.7-9.4-145.5c0.7-31.3,1.7-62.6,2.6-93.9
                        c0.1-4.1,0.1-8.1,6.3-9.6c1.2,2,3,4,3.6,6.3c12.9,49.3,25.8,98.5,38.2,147.8c10.5,41.7,16.3,84.3,20.3,127.1
                        c1.5,16.6,4,33.1,6.1,49.6c3.9,30,0.6,59.7-3,89.6c-9.3,76.3-31.1,149.8-50.2,223.9c-1.8,7-3.8,13.9-5.8,20.8
                        C984.5,1943.1,983.8,1943.4,983,1943.7z M983,1862.4c0.9-2.7,2.4-5.4,2.6-8.1c0.9-12.3,1.6-24.6,2.1-36.9c0.2-4.9,0-9.8,0-14.7
                        c-2.1,17.3-2.9,34.6-3.7,51.9C983.9,1857.2,983.4,1859.8,983,1862.4c0,10.6,0,21.1,0,31.6C983,1883.5,983,1873,983,1862.4z
                        M968.8,2049.5c0.8-0.3,1.6-0.5,2.5-0.8c0.2-3.9,0.5-7.8,0.7-11.7c0.5-16,0.9-32,1.5-47.9c2.8-68.6,5.6-137.1,8.5-205.7
                        c1.3-31.6,3-63.2,0.3-94.9c-1.7-19.2-2.6-38.5-4.2-57.8c-2.9-33.5-6-67-9.2-100.5c-2-20.2-5.4-40.3-6.4-60.6
                        c-2.2-42.9-3.2-85.9-4.7-128.8c-0.1-1.7-0.5-3.4-0.8-5.1c-3.5,3.6-5.6,7.3-6.6,11.3c-22.6,91.4-45.2,182.8-67.6,274.2
                        c-9,36.7-14.2,73.8-13.5,111.7c0.9,48.7,6.3,97,14.8,144.9c6.7,38.1,18.8,74.1,40.8,106.4c12.9,19,25.4,38.3,38.1,57.5
                        C964.7,2044.5,966.8,2047,968.8,2049.5z M824.6,1767.7c0.7,0,1.4,0,2.1,0c1-3.3,2.4-6.6,3.1-10c22.6-104,49.8-206.9,81.7-308.5
                        c10.4-33,19.2-66.4,24.9-100.6c0.4-2.3,0-4.7,0-7.1c-3.1,2-4.6,4.1-6.1,6.3c-6.9,9.8-12.9,20.4-20.8,29.3
                        c-29.8,34-63.4,63.7-103.9,84.8c-2.9,1.5-5.8,3.3-8.6,5c-8.3,4.8-11.7,11.8-11.7,21.5c-0.1,34.7,2,69.1,6.1,103.6
                        c6.8,56.2,16.1,111.9,30.4,166.8C822.6,1761.8,823.7,1764.8,824.6,1767.7z M989.2,1894.2c0.8,0.1,1.5,0.2,2.3,0.3
                        c1.6-5,3.3-10,4.7-15.1c3.8-13.8,7.5-27.6,11.1-41.4c14.4-55.4,27.6-111.1,31.7-168.4c1.5-21.6,2.2-43.2-0.5-64.8
                        c-2.3-18.5-4.2-37-6.6-55.5c-2.9-22.8-5.1-45.7-9.5-68.2c-11.6-59.1-27.9-117.1-44-175.1c-0.5-1.7-1.4-3.2-2-4.8
                        c-1.9,2.3-2.3,4.4-2.4,6.5c-0.5,12.3-1.2,24.6-1.4,36.9c-0.8,40.6-1.4,81.2,3.3,121.7c3,26.1,5.7,52.3,8.3,78.4
                        c3.3,33.1,6.8,66.2,9.1,99.4c2.1,29.2,4,58.5,3.5,87.7c-0.8,42.3-3.9,84.5-6,126.7C990.1,1870.5,989.7,1882.3,989.2,1894.2z"/>
                    <path d="M529.9,2064.1c-3.1-51.3-0.7-102.7-5.9-154c0,10.6,0,21.2,0,32.5c-5.2-1.2-5-5-5.7-7.8c-6.6-25.4-13.4-50.8-19.6-76.4
                        c-8.7-35.5-18.6-70.9-25.2-106.8c-7.1-38.6-15.2-77.3-13.2-116.9c1-20.6,3.5-41.1,5.8-61.6c2.8-25.2,5.7-50.3,9.1-75.4
                        c4.2-31.7,12.1-62.7,20.2-93.6c10.2-38.9,20.3-77.9,30.4-116.9c1.1-4.2,1.3-8.8,8.2-9.3c0.7,2.6,1.8,5.4,1.9,8.1
                        c1.2,26.3,3.2,52.6,3.1,78.9c-0.1,33.6-1.4,67.3-3.2,100.9c-1.1,20.6-4.2,41.1-6.3,61.6c-3.1,30.8-6.1,61.6-9.2,92.5
                        c-4.1,40.1-7.9,80.3-6.2,120.7c1.5,35.9,3.6,71.9,5.4,107.8c0.3,5.9,0.7,11.9,1.3,17.8c0-16.3,0.3-32.5-0.1-48.8
                        c-0.7-35-2.7-69.9-2.5-104.9c0.1-25.6,2.5-51.2,4.5-76.8c2.8-35.5,6-71,9.3-106.5c1.9-20.2,4.7-40.4,6.1-60.6
                        c2.1-28.6,3.5-57.2,4.7-85.8c0.6-14.6,0.1-29.3,0.1-44c0-3-0.3-6.2,0.6-8.9c0.9-2.5,3.1-5.8,5.1-6.1c2-0.3,5.2,2.2,6.7,4.3
                        c1.6,2.3,1.9,5.6,2.7,8.5c2,7.6,4,15.3,7.7,22.7c-1.5-10.1-2.9-20.2-4.3-29.6c4-3.5,6.7-2.3,8.8,0.1c2,2.2,3.7,4.7,5.2,7.3
                        c28.4,49.2,68.7,86.2,117.5,114.4c4,2.3,8.2,4.5,12.1,6.9c13.2,7.9,21.1,19.4,21.2,35.1c0.1,15.3-0.1,30.7-1.2,45.9
                        c-5.4,76.3-18.5,151.3-36.5,225.5c-1.1,4.5-1.9,9.1-3.1,13.6c-1,3.9-2.5,7.6-8.4,9c-25.3-117.8-55.1-234.3-93.2-348.8
                        c0.4,2.3,0.6,4.7,1.1,6.9c16.3,65.9,32.9,131.7,48.6,197.7c4.1,17.4,6.9,35.4,7.6,53.3c2.9,70.9-4.2,141.1-20.3,210.2
                        c-6.6,28.5-18.3,54.9-34.2,79.4c-14.9,22.9-30.2,45.5-45.5,68.1c-2.6,3.8-5.9,7.1-8.9,10.6C531.7,2064.6,530.8,2064.3,529.9,2064.1z
                        M535.3,2049.6c0.8,0,1.6,0.1,2.3,0.1c1.6-2,3.4-4,4.8-6.1c13.5-20.2,27.2-40.3,40.3-60.8c15.8-24.5,27.1-51.1,33.8-79.6
                        c10.1-43.2,16.4-87.1,18.8-131.3c1.7-31.6,3.2-63.3-1.3-94.8c-3-20.4-7.2-40.6-12.1-60.6c-22.3-91.5-45.1-182.8-67.8-274.2
                        c-0.8-3.2-1.9-6.3-2.9-9.5c-0.8,0.1-1.6,0.3-2.3,0.4c-0.3,4.2-0.8,8.4-0.9,12.6c-1.4,39.6-2.3,79.3-4.2,118.8
                        c-0.9,18.3-4.2,36.4-5.8,54.6c-3.7,41.1-6.5,82.4-10.6,123.5c-5.6,56.2-5.1,112.4-2.1,168.7c1.3,25.6,3.5,51.2,4.4,76.8
                        c1.8,50.6,2.9,101.2,4.3,151.9C534.2,2043.2,534.9,2046.4,535.3,2049.6z M571.6,1342.5c-0.7,0.2-1.3,0.4-2,0.7c0.2,3,0,6,0.7,8.8
                        c6.6,27.5,12.1,55.3,20.2,82.4c23.9,79.3,46.8,158.9,66.3,239.5c6.8,27.8,12.8,55.8,19.3,83.6c0.9,3.9,2.3,7.6,3.5,11.5
                        c2.8-3.5,3.7-6.7,4.4-9.9c2.4-10.7,4.6-21.5,7-32.2c17.1-76.2,29.8-152.9,30.4-231.3c0.1-14.9-5-24.8-18.5-32.2
                        c-51.4-28.5-95.7-65.2-127.3-115.6C574.5,1345.8,573,1344.2,571.6,1342.5z M531.8,1302.1c-0.9-0.2-1.8-0.3-2.7-0.5c-1,3-2.2,6-3,9.1
                        c-2.4,8.6-4.8,17.3-7.1,26c-16.5,62.7-35.6,124.8-42,189.8c-2.3,23.5-6.1,46.9-8.8,70.4c-5.2,45.4-1.5,90.4,6.4,135.2
                        c9.7,54.8,24.7,108.3,39.3,161.9c0.4,1.5,1.6,2.9,2.4,4.3c0.3-17.4-0.5-34.3-1.4-51.3c-1.5-27.9-3.4-55.9-4.8-83.8
                        c-2.2-44.7-0.8-89.2,4-133.7c3.2-29.5,6.1-59,9-88.4c3-29.2,7-58.2,8.6-87.5c1.9-34.9,1.9-69.9,2.1-104.8
                        C533.9,1333.2,532.5,1317.6,531.8,1302.1z M522.8,1892.3c0,0,0.1,0.2,0.2,0.4c0.1-0.6,0.4-1.2,0.4-1.8c0-2.7,0-5.5,0-8.2
                        c-0.2,0-0.4,0-0.6,0C522.8,1885.8,522.8,1889,522.8,1892.3z"/>
                    <path d="M608.5,2646.2c-2.5-3.4-4.7-7.1-7.5-10.3c-34.9-40.4-63.8-84.4-81.7-135c-8.6-24.3-13.7-49.3-17.5-74.7
                        c-3.8-25-8.2-49.9-11.5-75c-4.4-32.5-3.9-65.1-2.3-97.7c2.7-53.4,13.6-104.8,36.1-153.5c10.1-21.8,22.2-42.5,36.2-62
                        c3.6-5,3.6-5,11.7-6.5c-2.1,22-5,43.4-9,64.7c-0.2,4.1-0.4,8.2-0.7,12.3c0.6,0,1.2,0,1.7,0c-0.4-4.1-0.7-8.2-1.1-12.3
                        c1-1.4,2.7-2.8,3-4.3c6.7-33.7,17.2-66.3,27.6-98.9c2.2-6.9,5.7-13.4,8.7-20.1c1.6-3.6,4.1-6.3,8.3-5.9c4.7,0.4,6.9,3.5,7.7,7.9
                        c3.2,16.7,7,33.2,9.4,50c2.1,14.5,2.6,29.2,3.8,43.8c2,26.2-2.6,51.7-8.4,77.1c-1.2,5.1-2.2,10.2-2.7,15.5c5-9.3,5.2-20.4,10.8-29.3
                        c0.8,0,1.6,0.1,2.3,0.1c0.7,3.3,2.1,6.6,2,9.9c-0.7,33.6,5.3,66.3,12.5,98.8c11.1,50.4,7.2,99.9-8.8,148.6
                        c-9.4,28.8-19.1,57.4-28,86.3c-10.2,32.9-9.8,66.1-2.4,99.6c4.3,19.5,7.6,39.2,10.9,58.8c0.9,5.1,0.1,10.6,0.1,17.7
                        c-4.6-2.4-8.1-4.2-11.6-6C608.1,2645.9,608.5,2646.2,608.5,2646.2z M564.7,2046.2c-0.9-0.2-1.7-0.5-2.6-0.7
                        c-2.3,3.2-4.7,6.3-6.8,9.6c-13.1,19.8-24,40.7-33.1,62.6c-16.3,39.1-24.5,80.1-28,122.2c-2.6,31.6-4.1,63.3-0.3,94.8
                        c2.9,24.5,5.9,49,10.3,73.2c5.2,28.1,10.7,56.3,18.3,83.8c14.1,51.6,43.2,94.8,78.4,134.2c0.5,0.6,1.4,0.8,3.2,1.7
                        c-1.5-4.1-2.5-7.2-3.7-10.2c-4.1-10.5-8.5-21-12.3-31.6c-16.9-46.9-23-95.6-24.2-145.2c-0.8-35.3-1.5-70.6-3.1-105.9
                        c-2.1-46.6-5.1-93.2-7.3-139.8c-2-41.7,0-83.2,7.9-124.4C563,2062.5,563.6,2054.3,564.7,2046.2z M630.1,2159.2
                        c-0.6-0.2-1.2-0.4-1.7-0.6c-1.3,4.4-2.8,8.7-3.8,13.2c-8.8,38.6-17.8,77.1-26,115.8c-5.4,25-11.3,50.1-14,75.5
                        c-3.8,35.4-5.1,71-6.7,106.6c-0.6,12.6,0.4,25.3,1.7,37.9c3.5,33.9,13.8,66.4,22.8,99c1.8,6.4,4.2,12.8,10.5,19.5
                        c-0.7-5.4-0.9-8.6-1.5-11.8c-2.8-14.4-5.5-28.7-8.6-43c-8.3-38.1-5.4-75.3,7.2-112.1c8.6-25.2,16.9-50.4,25.2-75.7
                        c15.4-47.2,18.7-95,7.1-143.6c-6-25.3-10.9-50.8-11.2-76.9C631,2161.7,630.4,2160.4,630.1,2159.2z M612.4,1972.6
                        c-9,5.9-11.7,14.1-14.1,22.3c-6.7,22.3-13.3,44.6-19.7,67c-7.3,25.4-11.2,51.3-11.3,77.8c-0.2,57.3,2.2,114.6,6.7,171.7
                        c0.1,1.7,0.7,3.3,1.1,5c0.7,0,1.5,0.1,2.2,0.1c1.5-5.3,3.3-10.5,4.6-15.8c10-43.1,20.1-86.2,29.9-129.4c4.5-20.1,9.4-40.2,12.3-60.6
                        c5.2-36.3,1.4-72.5-5-108.3C617.1,1992.7,614.7,1983.1,612.4,1972.6z M612.4,2200.7c-0.6-0.1-1.3-0.2-1.9-0.2
                        c-6.2,26.5-12.3,53-18.5,79.5c-3.8,16.2-7.5,32.4-11.3,48.6c-0.7,2.9-1.2,6.4-5.2,6.2c-4.3-0.2-5.1-3.6-5.3-7.1c-0.1-1-0.2-2-0.2-3
                        c-1.5-21.6-3.2-43.2-4.6-64.8c-2.8-44.9-5.5-89.8-3.4-134.8c0.1-2.4-0.3-4.8-0.4-7.2c-1.7,10.9-3,21.9-3.1,32.8
                        c0,17.3,0.6,34.6,1.2,51.9c1,26.3,2.2,52.6,3.4,78.9c1.6,35.3,3.5,70.6,4.9,105.8c1,26.3,1.3,52.6,2,78.9c0.1,3,0.7,6,1.1,9
                        c0.7-0.1,1.4-0.2,2.1-0.3c0-8.9-0.1-17.8,0-26.7c0.6-40.7,3.1-81.3,11.3-121.2c7-34.2,15.1-68.2,22.7-102.3
                        C608.9,2216.7,610.6,2208.7,612.4,2200.7z M613.8,2187.4c0.5,0.1,1.1,0.2,1.6,0.3c1-4.4,1.9-8.8,2.9-13.2c-0.5-0.1-1.1-0.2-1.6-0.3
                        C615.7,2178.6,614.7,2183,613.8,2187.4z"/>
                    <path d="M900,2643.9c-3.4,2.5-6.7,5-10.6,8c-5-5.4-4.3-11.7-3.2-17.8c3.4-18.7,6.3-37.5,10.8-55.9c8.7-35.2,8.3-70-2.4-104.5
                        c-9.4-30.2-19.8-60.1-29.4-90.2c-10.2-32-16.2-64.7-12.9-98.4c1.8-18.5,4.8-37,8.3-55.3c5.6-28.8,9.5-57.8,10.6-87.2
                        c0.1-3,0.5-6,0.8-8.9c0.1-0.9,0.8-1.6,1.4-2.8c4.6,2.2,4.5,6.5,5.4,10.1c9.2,38.2,18.6,76.3,27.2,114.6c6.7,29.5,13.4,59.1,18.3,89
                        c4,24.6,5.4,49.6,7.7,74.5c1.1,12.2,1.6,24.5,3.7,36.9c0.8-15.3,1.7-30.5,2.3-45.8c2.7-62.2,5.2-124.5,7.9-186.7
                        c0.7-16,1.6-31.9,2.2-47.9c1.3-35.8-2.9-71.2-8.7-106.4c-1.7-10.2-3.3-20.4-4.8-30.6c-0.3-2.2,0-4.5,0-6.9c6.4-1.4,8.8,2.7,11.5,6.4
                        c14.2,19.8,26.4,40.8,36.6,62.9c18.9,41,29.6,84.1,33.5,128.9c1.9,21.6,4,43.2,3.6,64.8c-0.5,24.2-3,48.5-5.8,72.6
                        c-4.5,38.7-11.2,77.1-21.5,114.7c-12.6,45.9-32.7,87.9-63.7,124.3c-8.4,9.9-16.3,20.2-24.4,30.4c-1.8,2.3-3.1,5-4.6,7.5L900,2643.9z
                        M903.6,2624.3c0.7,0.5,1.4,1,2.1,1.5c9.2-11.3,18.6-22.5,27.5-34c21.2-27.4,37.2-57.5,47.3-90.7c14.9-48.9,24.9-98.7,30-149.5
                        c5.2-52.3,4.8-104.4-4.4-156.3c-9-50.5-27-97.4-55.3-140.2c-2.1-3.2-4.6-6.1-7-9.1c-0.7,0.3-1.5,0.6-2.2,0.9c1.1,8,2,16.1,3.3,24.1
                        c5.6,34.6,9.8,69.3,8.6,104.4c-0.9,28-2.8,55.9-4.1,83.9c-1.9,39.9-3.7,79.9-5.6,119.8c-1.8,37.9-2.9,75.9-5.9,113.7
                        c-3.4,42.4-13.6,83.3-31,122.4C905.6,2618.1,904.7,2621.2,903.6,2624.3z M893.3,2625c0.7,0.1,1.3,0.3,2,0.4c0.9-1.7,2.1-3.4,2.7-5.2
                        c5.2-15.1,10.7-30,15.2-45.3c9.8-33.4,16.4-67.2,14.6-102.3c-1-18.6-1.3-37.3-2.1-55.9c-0.7-16.3-0.7-32.7-3-48.8
                        c-3.2-22.7-7.8-45.2-12.7-67.6c-9.6-44.2-19.7-88.3-29.6-132.4c-0.5-2.1-1.5-4.1-3-7.9c-4,23.7-7.7,45-11.1,66.4
                        c-2.9,17.7-6.1,35.4-8.1,53.3c-3.9,35.7,1.8,70.3,12.9,104.2c8.6,26.2,17.4,52.4,26,78.7c7.3,22.3,11.3,45.1,11.2,68.7
                        c-0.1,18.1-2.8,35.8-7.5,53.2C896.9,2597.6,894.2,2611.2,893.3,2625z"/>
                    <path d="M918.1,856.1c6.6-2.2,13.2-4.3,19.7-6.6c7.5-2.6,14.9-5.7,22.6-8c9.6-2.9,12.8-0.9,14.3,9.2c1.4,9.5,2.1,19.2,2.9,28.8
                        c4.1,51.6,4.3,103.2-3,154.6c-2.1,14.5-2.9,29.2-4.7,43.7c-0.8,6.2-2.1,12.5-4,18.5c-1.8,5.7-5.1,10.7-11,15
                        c-1.6-3.5-3.4-6.2-4.3-9.2c-6.1-20.3-17.9-37-32.4-52c-7.7-7.9-15-16-22.3-24.3c-9.1-10.2-14.8-22.2-17.6-35.4
                        c-6.6-30.9-12.8-61.9-19.1-92.9c-0.3-1.6-0.7-3.3-0.8-4.9c-0.9-10.3,1.9-14.8,11.5-18.3c4.9-1.8,9.9-3.3,14.5-6.3
                        c-6.5,0.9-12.9,1.8-19.4,2.6c-14.7,1.8-29.3,0.4-43.6-3.2c-17.1-4.3-30.7-14-41.8-27.6c-2.2-2.7-4.9-5.1-7.4-7.6l0.4,0.4
                        c-0.8-2.1-1.4-4.4-2.5-6.3c-7.6-13.6-11.8-28.1-12.4-43.7c-0.9-26,0.3-51.9,4.2-77.6c3.1-20,13.7-34.5,32.9-41.6
                        c27.1-10,54.4-19.4,81.7-29c6.9-2.4,13.7-1.4,20.4,1.2c16.9,6.4,28.5,18.5,34.9,35c12.1,31.5,27.9,61,44.3,90.3
                        c4.3,7.8,7.4,16.3,10.6,24.7c3.6,9.7,1.5,18.2-6.3,25.3c-16.8,15.2-34.7,29-54.7,39.9c-2.7,1.5-5,3.7-7.4,5.6c-2.7,0.6-5.3,1.3-8,2
                        c0.2,0.6,0.5,1.2,0.7,1.8C913.4,858.7,915.7,857.4,918.1,856.1z M762.5,758.3c0.4,0,0.8,0,1.1,0c0,9.6-0.4,19.3,0.1,28.9
                        c1.6,36.7,29.1,69.1,64.9,76.5c17.2,3.6,34.4,3.2,51.5-0.8c39.3-9.2,70.6-31.9,99.1-59.1c4.3-4.1,5.4-9,3-14.5
                        c-4.2-9.8-7.8-19.9-12.7-29.2c-15.6-29.4-31.3-58.7-43.7-89.7c-5.1-12.7-14.1-21.6-26-27.8c-8-4.2-16.4-5.4-25.1-2.3
                        c-24.8,8.7-49.7,17.1-74.3,26.2c-19.1,7-30.8,21.2-33.8,41.1C764.1,724.3,763.8,741.4,762.5,758.3z M957.2,1100.1
                        c4.7-6.3,6-13,6.8-19.9c2.9-26.5,5.4-53,8.7-79.4c6.1-49,1.7-97.7-3-146.5c-0.8-7.9-3.3-9.5-10.5-6.9c-29.1,10.5-58.1,21.2-87.1,32
                        c-7.9,3-9,5.3-8.2,13.7c0.2,2,0.6,3.9,1,5.9c5.4,26.4,11.5,52.7,16.1,79.2c3.7,21.5,13.1,39.6,28.2,55c4,4,8,8,11.9,12.1
                        c14.3,15.1,27.2,31,33.5,51.4C955,1097.8,956.1,1098.7,957.2,1100.1z"/>
                    <path d="M869.9,305.1c0,4.3,0.2,8.7,0,13c-0.4,6.7,2.6,11.4,8,15c4.4,2.9,8.8,6.1,12.9,9.4c7.1,5.6,9.3,13.2,6.1,21.4
                        c-5,13-10.5,25.9-16.2,38.6c-2.9,6.4-7.9,10.4-15.3,11.4c-8,1.1-8.7,2.1-9.5,9.9c-1.3,13.6-2.6,27.2-3.8,40.8
                        c-0.8,9.7-5.3,17-13.6,22c-24.9,14.6-50.8,26.2-79.8,30.2c-12.8,1.8-25.7,1.3-37.4-3.4c-17.6-7-34.6-15.3-51.6-23.8
                        c-11.9-6-17.9-16.3-18.7-29.8c-0.7-11.6-2-23.2-3.1-34.8c-0.8-8.8-1.5-9.7-10.6-11c-5.5-0.8-9.3-3.7-11.6-8.4
                        c-6.6-13.8-13.4-27.6-19.2-41.7c-4.5-11.1-1-19.9,10-24.8c12.9-5.7,17.3-15.1,16.7-28.6c-1.1-28,5.6-54.2,20.2-78.2
                        C678,192,723.7,171,769.7,178.4c46.6,7.6,85.1,42.5,95.8,88.1c3,12.5,3.9,25.6,5.7,38.4C870.8,304.9,870.3,305,869.9,305.1z
                        M747.5,512.4c5-0.5,9.9-0.9,14.9-1.7c23.4-3.6,44.3-13.7,65.3-23.8c13.6-6.5,19.7-16.9,20.4-31.5c0.6-11.6,1.9-23.2,3.2-34.8
                        c1.1-9.9,1.3-10.1,11-12c6.7-1.3,12-4.3,14.8-10.6c5.1-11.6,10-23.2,14.5-35c2.5-6.5,1-12.5-4.7-17c-3.7-2.9-7.3-5.9-11.4-8.1
                        c-7.9-4.4-10.7-10.9-10.9-19.7c-0.3-13.6-0.6-27.3-2.4-40.8c-6.8-49.3-49-89.7-100.6-94.4c-49-4.4-96.4,25.2-113.6,72.3
                        c-7.3,19.9-10.6,40.6-9.8,61.7c0.4,11.2-2,19.8-14,23.1c-0.9,0.3-1.7,0.9-2.6,1.5c-10.8,6.4-13.1,12.5-8.2,24.1
                        c4.1,9.8,9,19.3,13,29.2c3.4,8.5,8.7,14,18.4,14.3c5.2,0.1,7.9,2.8,8.2,8c0.2,3.7,0.7,7.3,1.1,10.9c1.2,12.6,2.4,25.2,3.6,37.8
                        c0.7,7.2,4.3,12.5,10.7,15.8c14.8,7.6,29.5,15.4,44.4,22.8C723.5,510,735.3,512.1,747.5,512.4z"/>
                    <path d="M757.5,624.5c-4,0-7.2,0-11.1,0c-0.2,3.9-0.2,7.4-0.6,11c-1,9.7-5.9,12.1-14.1,6.5c-22.1-15.3-41.7-33-56.2-56
                        c-1.4-2.3-2.6-4.7-4.2-6.8c-11.9-15.4-13.4-32.8-10.4-51.2c1.5-9.2,3.4-18.2,5.3-27.3c0.3-1.5,1.4-2.9,2.4-4.8
                        c16,6.4,32.5,10.7,44.9,23.5c1.7,1.7,5.3,2.3,7.9,2.3c20.6,0,41.2-0.3,61.8-0.7c2.4,0,5.3-1,7-2.5c10.5-9.3,23-14.5,35.9-19
                        c6.7-2.3,8.9-1.6,11.9,4.7c1.7,3.6,3.2,7.4,3.8,11.3c1.3,8.9,2.3,17.8,2.9,26.8c0.7,10.2-1.5,19.9-7.3,28.5
                        c-14.6,21.5-30.5,42.1-49.5,59.9c-5,4.7-10.9,8.7-17.1,11.9c-7.9,4.2-11.9,1.7-12.9-7C757.7,632.3,757.7,628.9,757.5,624.5z
                        M671.8,501.9c-1.5,4.6-3.4,7.9-3.7,11.4c-1.2,12.9-2.2,25.9-2.5,38.8c-0.1,4,1.6,8.6,3.7,12.1c6.9,11.4,14,22.7,21.8,33.4
                        c10.5,14.3,23.4,26.4,38.2,36.3c2.8,1.9,6.1,3.1,10.7,5.5c0.4-7.6,1.2-13.2,0.9-18.7c-2-31.4-12.2-60.5-25.6-88.6
                        c-3.8-8.1-9.6-14.3-17.8-18.2C689.4,510.2,681.3,506.4,671.8,501.9z M764.3,637.8c11.5-2.7,18.7-7.3,27.7-17.9
                        c11.9-14,23.9-27.8,35-42.3c4.7-6.2,8.4-13.7,10.5-21.2c4.3-15.5,0.8-31.1-2.5-46.4c-1.2-5.4-3.7-6.9-8.8-4.7
                        c-9.1,4-18.2,8.2-26.6,13.5c-4,2.5-7.5,7-9.4,11.3c-7.6,17.7-15,35.6-21.3,53.8C763,601.1,762,619.1,764.3,637.8z M750.6,615.8
                        c0.7,0.1,1.4,0.1,2.1,0.2c1-3,2.4-5.9,2.8-8.9c3.1-24.5,10.1-48,19.4-70.8c1.1-2.8,1.4-6,2.2-9.4c-17.8,0-34.1,0-51.2,0
                        c5.1,15.2,10.5,29.4,14.6,44C744.5,585.7,747.3,600.9,750.6,615.8z"/>
                    <path d="M899.6,2644.3c10.9-11.5,22.4-22.5,32.6-34.7c8.7-10.4,15.9-22.2,23.8-33.3c0.8-1.1,1.2-2.6,2.1-3.3c2.3-1.7,5.1-4.5,7.1-4
                        c2.1,0.5,3.8,4,5.1,6.5c1,2,1.1,4.5,1.5,6.8c4.1,28,8.2,56,12.2,84c1,6.9,4.4,12.4,9,17.4c6.5,7.1,13.1,14.2,19.3,21.6
                        c14.6,17.6,31.4,32.6,50.5,45.1c16.9,11.1,31.4,24.8,43.3,41c3.9,5.3,7.2,11.2,9.6,17.4c3.3,8.3,1.5,12.5-6.3,16.4
                        c-19.8,9.8-40.4,17-62.3,20.7c-22.8,3.9-42.6-3.5-61.2-15.7c-5.8-3.8-11.1-8.5-16.6-12.9c-11.8-9.4-24.8-16.9-39.3-21.4
                        c-15.8-4.8-24.9-15.3-28.9-31.3c-7.1-28.3-10.1-57-11.3-86c-0.5-10.7,2.1-20.4,7.8-29.4c1-1.6,1.5-3.5,2.3-5.3
                        C900,2643.9,899.6,2644.3,899.6,2644.3z M964.7,2575.4c-2.2,2.5-3.7,3.7-4.5,5.2c-9.7,17.7-22.5,32.9-36.4,47.4
                        c-5.8,6-11.4,12.1-16.9,18.3c-8.3,9.4-12.4,20.3-11.3,33c0.3,3.3,0.2,6.7,0.5,10c1.8,24.2,3.9,48.4,10,72.1
                        c3.8,14.8,12.3,26,27.4,30.4c15.3,4.5,28.6,12.4,40.7,22.5c4.6,3.8,9.4,7.4,14.3,10.9c16.3,11.9,34.4,16.6,54.5,15.4
                        c21.4-1.3,40.7-9.2,59.6-18.4c10.6-5.2,11.6-8.5,5.6-18.5c-1.5-2.6-3.3-5-5.1-7.4c-12-16.2-26.2-30.1-43.1-41.2
                        c-15.9-10.5-30.6-22.4-43.4-36.6c-9.2-10.1-17.9-20.6-27.2-30.6c-6.7-7.1-9.9-15.3-11.3-24.9
                        C973.9,2634.4,969.4,2605.9,964.7,2575.4z"/>
                    <path d="M608.1,2645.9c-0.1,0.6-0.4,1.5-0.2,1.9c8.2,11.6,9.1,24.4,8,38.1c-2.2,27.2-4,54.5-11.9,80.8c-1.4,4.7-3.3,9.4-5.5,13.8
                        c-2.8,5.5-7.2,9.6-13.2,11.5c-23.2,7.5-43,20.7-62,35.6c-10,7.9-21.1,13.9-33.7,16.5c-14.6,3-29.3,3.1-43.5-1.2
                        c-15.9-4.7-31.5-10.7-47-16.7c-10.3-4-12.4-9.4-8.2-19.7c1.9-4.6,4.3-9,7.3-13c12.9-17.1,27.9-32.1,46-43.9
                        c18.6-12.1,34.9-26.6,49-43.7c6.1-7.4,12.7-14.5,19.2-21.6c5.3-5.8,8.8-12.3,9.9-20.2c3.8-27.3,7.9-54.6,12-82
                        c0.7-4.8,0.1-11.2,6-12.6c6.6-1.5,8.7,4.7,11.1,9.3c8.4,15.7,19.8,29.1,31.8,42.1c7.2,7.8,14.5,15.6,21.9,23.3
                        c0.8,0.8,2.1,1.2,3.2,1.7C608.5,2646.2,608.1,2645.9,608.1,2645.9z M541.7,2575.3c-1,3.7-1.5,5.2-1.8,6.8c-4,27.3-8.2,54.6-11.9,82
                        c-1.3,9.3-4.6,17.1-11.2,23.8c-7.4,7.6-14.3,15.8-21.2,23.9c-13.7,16.1-29.1,30.2-46.9,41.6c-17.9,11.5-32.6,26.3-45.7,42.8
                        c-2.3,2.8-4.2,6-5.8,9.3c-4.5,9.4-3.2,13.2,6.4,17c12,4.8,24.2,9.2,36.5,13.5c31,10.7,59.2,4.7,84.3-15.6
                        c16.2-13.1,33.1-24.4,53.2-30.7c10-3.1,17-10.1,19.8-20.7c1.4-5.1,3.1-10.2,4.6-15.3c7.3-24.9,6.4-50.7,8.6-76.2
                        c1-11.3-3.7-21.6-10.7-30.4c-3.5-4.4-7.5-8.5-11.5-12.5c-15.9-16.2-30.8-33.2-42-53.1C545.4,2579.8,544,2578.4,541.7,2575.3z"/>
                    <path d="M849.4,1728.3c2.3,4.5,4.2,6.8,4.6,9.3c1.5,10.2,3.1,20.5,3.6,30.7c2.5,51.4,10.5,102,23.4,151.8c5.4,21,5.9,41-0.3,61.9
                        c-6.5,21.8-8.1,44.5-8.6,67.2c-0.3,16-0.1,32-0.1,48c0,4,0,7.9,0,11.8c-6.9,3-8.2-1.6-9.6-5.6c-5.5-15.7-11.2-31.3-16.2-47.2
                        c-17.4-54.8-23.8-111.2-22.6-168.6c0.9-43.4,4.7-86.5,14.6-128.8c1.6-6.8,3.4-13.6,5.5-20.2C844.6,1735.6,846.6,1733.1,849.4,1728.3
                        z M849.7,1748.6c-0.6-0.1-1.2-0.1-1.8-0.2c-0.7,1.7-1.6,3.2-2,5c-8.5,33-13.7,66.5-15.9,100.5c-2.8,41-1,81.9,4.1,122.7
                        c4.4,35.5,12.3,70.1,26,103.3c0.9,2.1,2.2,4.1,3.4,6.2c2.2-7.7,2.6-14.9,2.8-22.2c0.9-29.7,2.4-59.3,10.4-88.1
                        c4.6-16.6,4.3-32.6,0.3-49.2c-13-52.8-22.6-106.2-24.8-160.7C851.8,1760.1,850.5,1754.3,849.7,1748.6z"/>
                    <path d="M657.1,1728.3c2.7,5.2,4.7,8.1,5.7,11.3c7.3,24.6,11.8,49.8,15.2,75.3c5.1,38.8,5.9,77.7,3.9,116.7
                        c-3.2,60-15.5,118-38.5,173.7c-0.5,1.2-0.8,2.8-1.8,3.5c-1.2,0.9-3.1,1.6-4.4,1.3c-1.3-0.3-2.8-1.8-3.1-3.1c-0.5-1.8-0.1-3.9,0-5.9
                        c1.3-39,0.8-77.9-7.9-116.3c-0.1-0.3-0.1-0.7-0.1-1c-10.9-37.3-5.6-38.3,0.7-70.5c5.6-28.7,11.8-57.4,16.1-86.3
                        c3.7-24.7,5-49.7,7.4-74.6c0.4-4.6,0.5-9.4,1.5-13.9C652.2,1735.7,654.4,1733.1,657.1,1728.3z M640.2,2090.6c1,0.1,2,0.3,3,0.4
                        c29.1-78.3,39.1-159.2,32.8-242.6c-2.8-36.9-12.7-94.2-19.6-102.6c-0.9,8.7-1.8,16.5-2.5,24.3c-2.4,23.8-3.6,47.9-7.5,71.5
                        c-4.6,28.5-11.3,56.7-17.4,85c-3.6,16.6-3.7,32.9,0.3,49.4c2.7,11.3,5.4,22.7,6.6,34.2c1.8,18.9,2.3,37.8,3.3,56.8
                        C639.7,2074.9,639.9,2082.8,640.2,2090.6z"/>
                    <path d="M930.7,2334.2c-4.8-2.1-5.8-6.6-6.8-11.2c-7.9-35-15.8-70-23.7-105c-7.3-32.1-15.3-64-22-96.2c-2.6-12.6-3.9-25.7-3.7-38.6
                        c0.5-34.3,3.2-68.5,11.9-101.9c1-3.8,2.4-7.7,4.7-10.7c3.9-5.1,8.8-4.6,12,0.9c3.2,5.4,6.1,11.1,8.1,17.1
                        c11.8,35.3,23.4,70.8,29.2,107.7c2.4,15.4,3.3,31.1,3.8,46.7c1.6,50.6-2.1,101.1-5.5,151.6c-0.6,9.6-1.1,19.3-2,28.9
                        C936.3,2327.5,935.6,2331.9,930.7,2334.2z M929.5,2319.2c0.6-0.1,1.2-0.3,1.8-0.4c0.2-1,0.5-1.9,0.6-2.9
                        c2.5-48.2,5.4-96.5,7.3-144.7c0.7-18.3,0-36.7-1.8-54.9c-4.5-46.4-19.1-90.4-34.6-134c-1.3-3.8-2.8-7.6-8.5-8.5
                        c-1.4,4.6-3,9.2-4.1,14c-6,26.1-8.6,52.6-10.1,79.2c-1.2,22.7,0.5,45.2,5.8,67.5c14.1,59.9,27.9,119.9,41.9,179.9
                        C928.2,2315.9,928.9,2317.5,929.5,2319.2z"/>
                    <path d="M1035.1,922.1c0.3,2.2,0.6,4.5,0.8,6.7c1.4,5.1,2.8,10.2,4.2,15.4l-0.2-0.4c0.5,5.2,0.6,10.6,5.2,14.3
                        c0.3,1.9,0.6,3.8,0.9,5.8c1.4,4.8,2.8,9.6,4.2,14.4c0,0-0.3-0.4-0.3-0.4c2.3,10,4.5,20,6.9,30c1.5,6.5,3.5,12.8,4.9,19.3
                        c0.7,3.2,1,7.1-2.9,8.7c-3.7,1.5-6.4-0.8-8.4-3.7c-3.2-4.6-6.4-9.3-9.4-14.2c-18.9-30.5-33.4-62.8-39.8-98.4
                        c-4.7-26.2-3.9-52.4-0.2-78.5c0.6-4,0.9-7.9,1.7-11.9c0.8-4.2,1.1-8.7,6.9-11.6c1.5,5.6,3.2,10.7,4.3,16c2.2,10.7,4.1,21.5,6.3,32.3
                        c0.5,2.4,2.1,4.6,3.2,6.9c0.2,2.6,0.4,5.3,0.6,7.9c2.9,13.2,5.8,26.5,8.8,39.6C1032.9,921.2,1034.3,921.6,1035.1,922.1z
                        M1008.9,848.2c-0.8,0.1-1.6,0.3-2.4,0.4c-5.1,30.3-3.3,60.2,4.9,89.8c9.1,32.8,29,72.1,41.7,81
                        C1038.2,961.6,1023.6,904.9,1008.9,848.2z"/>
                    <path d="M470.1,920.9c0.6-1.7,1.1-3.4,1.7-5c4.1-17.1,8.5-34.1,12.3-51.2c2.7-12,4.5-24.2,6.9-36.2c0.7-3.4,2.3-6.7,3.7-10.7
                        c5.4,2.9,5.5,7.5,6.3,11.4c4.4,22,6.1,44.1,4.3,66.5c-3.9,48.4-21.7,91.6-48.4,131.5c-1.5,2.2-2.9,4.7-4.9,6.2c-1.9,1.4-5.2,2.8-7,2
                        c-1.7-0.7-2.7-4.2-3-6.5c-0.2-2.2,1-4.5,1.6-6.7c5.8-21.5,11.6-43,17.4-64.5c0.7-2.5,0.9-5.2,1.3-7.8c0,0-0.3,0.3-0.3,0.3
                        c1.4-4.8,2.8-9.7,4.2-14.5c0,0-0.2,0.5-0.2,0.5C467.3,931.1,468.7,926,470.1,920.9z M449.5,1022.9c0.7,0.3,1.4,0.6,2.1,0.8
                        c1.3-1.4,2.8-2.8,3.9-4.4c3.2-4.6,6.7-9.2,9.5-14.1c25.2-45.6,37.5-94.4,35-146.6c-0.3-5.4-1.8-10.7-2.7-16c-0.8,0-1.7,0-2.5,0
                        C479.6,902.7,464.6,962.8,449.5,1022.9z"/>
                    <path d="M749.6,1146c0,12.3,0.1,24.6-0.1,36.9c-0.1,4.3-0.3,8.7-1.2,12.9c-2.1,9.7-6.2,12.8-16.3,12c-4.6-0.4-9.2-1.3-13.7-2.4
                        c-15.8-4-31.5-8.3-47.3-12.1c-12.5-3-21.3-10.1-27.3-21.4c-9.6-18.2-15-37.6-18-57.8c-1.4-9.5,1.8-16,9.7-21.1
                        c19.2-12.3,40-19.6,62.8-20.6c11-0.5,22-0.4,33-0.2c9.4,0.1,12.3,2.4,15,11.5c4.9,16.5,4.8,33.5,4.6,50.5c-0.1,4-0.4,8-0.6,12
                        C750,1146,749.8,1146,749.6,1146z M744,1155.9c0.2,0,0.3,0,0.5,0c0.2-4,0.1-8,0.6-12c2.2-18.4-0.2-36.5-3.1-54.6
                        c-1.6-10.2-3-11.7-13.5-11.9c-10.6-0.2-21.3,0-32,0.5c-20.5,0.9-38.9,8.1-56.3,18.5c-6.6,4-9.9,9.5-8.9,17.5
                        c2.7,21.1,8.9,40.9,19.5,59.3c3.8,6.6,9.2,11.1,16.9,13c19.7,5,39.3,10.2,58.9,15.3c1.3,0.3,2.6,0.6,3.9,0.7
                        c9,1.2,12.1-1.1,13.1-10.4c0.4-3.3,0.4-6.7,0.4-10C744,1173.2,744,1164.5,744,1155.9z"/>
                    <path d="M754.2,1022.4c0-11-0.6-22,0.2-32.9c0.9-11.9,6-16.8,17.8-17.4c33.8-1.9,63.6,8.2,88.9,31c7.3,6.5,11.1,14.9,12.5,24.4
                        c2.2,15.1,4.2,30.3,6.2,45.5c0.3,2.3,0.5,4.7,0.1,7c-0.8,4.9-4.5,7.6-9.3,6.5c-2.8-0.7-5.5-2.3-8-3.9c-17.8-10.8-37.1-16-57.8-16.5
                        c-12-0.3-24,0.1-35.9-0.4c-11.9-0.5-14.5-3.2-14.9-15.1c-0.3-9.3-0.1-18.6-0.1-27.9C753.9,1022.4,754,1022.4,754.2,1022.4z
                        M872.9,1080.8c0-6.3,0.4-10.9-0.1-15.5c-1.3-11.9-3.4-23.7-4.6-35.6c-0.9-9.5-4.7-17.3-11.7-23.4c-23.6-20.7-51.1-30.6-82.6-28.7
                        c-11.5,0.7-14.2,3.1-14.4,14.5c-0.4,19.3-0.3,38.6-0.1,57.9c0.1,8.6,1.4,9.7,10.1,10c9.3,0.3,18.6,0.3,28,0.4
                        c23.3,0.1,45.6,4.4,66.1,16.3C865.9,1078.1,868.7,1079,872.9,1080.8z"/>
                    <path d="M871.3,996.3c-5.4-2.4-8.6-3.5-11.4-5.1c-27.4-15.6-57.1-23.4-88.2-27.1c-3-0.3-5.9-0.9-8.8-1.7c-5.4-1.5-8.6-5.2-8.9-10.8
                        c-0.7-11.9-2.3-24-1.2-35.8c0.8-8.7,4.4-17.4,8.4-25.3c4.1-8.3,10-15.7,15.4-23.3c4.2-5.8,8.3-7.3,14.7-4.6
                        c15.7,6.4,31.3,13.2,46.7,20.2c7.3,3.4,12.1,9.7,14,17.4c6.7,27.1,13.1,54.3,19.3,81.6C872.1,986,871.3,990.4,871.3,996.3z
                        M865.4,987.4c-0.4-5.1-0.3-8.3-1-11.3c-5.5-23.9-11.1-47.9-16.9-71.8c-2.1-8.5-7.2-15-15.3-18.6c-13.1-5.7-26.2-11.4-39.4-16.8
                        c-7.5-3-9.3-2.1-14.1,4.6c-19.3,27.1-19.3,27.1-19.3,60.5c0,1,0,2,0,3c0,20.5,0,20.3,20.8,22.9c26.3,3.3,51.5,10.1,74.9,22.7
                        C857.8,984.2,860.7,985.3,865.4,987.4z"/>
                    <path d="M717,1064.9c-13.2,1.2-26.5,2.1-39.7,3.6c-13.1,1.5-24.7,7.3-36,13.8c-13.2,7.6-16.3,6.1-16.7-8.8
                        c-0.6-19.4,1.7-38.6,9.4-56.6c2.1-5.1,5.2-10.4,9.2-13.9c23.4-20.4,50.3-32,81.9-30.9c1.3,0,2.7,0,4,0.1
                        c15.9,1.2,18.6,4.7,21.2,20.6c3.2,20,2.2,39.7-0.3,59.6c-1.3,10.1-3.7,12.6-13.9,13.1c-6.3,0.3-12.6,0.1-19,0.1
                        C717,1065.4,717,1065.1,717,1064.9z M630.1,1081.1c5.1-2.3,8.7-3.6,12-5.5c12.9-7.4,26.6-12.3,41.4-13.4c15.6-1.1,31.2-1.5,46.9-2.2
                        c3.9-0.2,7.8-0.5,11.9-0.7c0.8-2.6,1.5-4.4,1.8-6.3c3.9-21.9,1.8-43.8-0.7-65.6c-0.8-6.8-3.9-8.9-11.9-9.5
                        c-29.2-2.3-55.2,6.1-78.5,23.4c-10.3,7.7-16.7,18-18.8,30.7c-1.6,9.8-3.1,19.7-4,29.7C629.7,1067.5,630.1,1073.4,630.1,1081.1z"/>
                    <path d="M749.9,926.7c0,9.9,0.3,16.5-0.1,23.2c-0.4,7.9-2.5,10.8-10.3,12.4c-13,2.8-26.1,4.9-39.2,7.3
                        c-18.2,3.4-35.1,10.1-51.2,18.9c-0.9,0.5-1.7,1.2-2.6,1.5c-3.7,1-8,3.5-10.9,2.4c-4.7-1.8-3.7-7.3-2.8-11.5c5.2-24,10-48.2,16.1-72
                        c3.9-15.3,13.8-26.5,28.4-32.8c11.3-4.8,22.8-9.1,34.4-13.1c8.4-2.9,11.4-1.6,16.8,6c1.7,2.4,3.2,5.1,5.1,7.4
                        C745.6,892.2,754.1,909.1,749.9,926.7z M637.6,988.1c5.2-2.6,8.5-4.2,11.7-5.8c18.4-9.7,37.8-16.3,58.3-19.7
                        c8.9-1.4,17.7-2.7,26.6-4.4c8.8-1.7,10.6-3.7,11.1-12.5c0.4-6.3,0-12.6,0.5-18.9c1-15.3-3-28.9-12.4-40.9c-3.1-3.9-5.5-8.3-8.3-12.4
                        c-4.5-6.4-6.8-7.3-14.3-4.5c-9,3.3-17.9,7.2-26.9,10.4c-17.6,6.3-27.5,18.8-31.4,36.9c-4.2,19.8-8.9,39.5-13.3,59.3
                        C638.3,978.9,638.2,982.5,637.6,988.1z"/>
                    <path d="M772.1,832.2c1.6,3.2,2.9,6.6,4.9,9.6c2.7,4.1,3.1,8.2,0.4,12.1c-5.9,8.9-11.7,17.8-18.1,26.2c-4.8,6.2-10.1,6.3-14.8,0
                        c-6.4-8.5-12.1-17.5-17.9-26.4c-2.8-4.3-2.5-9,0.3-13.2c5.6-8.6,11.1-17.4,17.2-25.6c5.3-7.1,9.4-6.8,15.1,0.3
                        c4.6,5.6,8.8,11.5,13.2,17.3C772.5,832.6,772.1,832.2,772.1,832.2z M750.2,814.9c-1.3,1.9-2.3,3.3-3.2,4.6c-3.1,4.3-6.3,8.5-9.2,13
                        c-9.6,14.9-9.7,15,0.5,29.9c4.2,6.1,7.2,13.5,15.3,17.5c5.6-8.4,11.1-16.6,16.8-24.8c3.5-5,3.5-9.7,0-14.7
                        c-4.5-6.5-8.8-13.2-13.5-19.7C755.5,818.8,753.1,817.4,750.2,814.9z"/>
                    <path d="M752.3,709.7c-2.1-2.3-3.2-3.2-3.7-4.3c-7.2-14.5-14.3-29.1-21.4-43.7c-0.7-1.4-0.7-3.1-1.2-5.6c5.2,0,9.7,0.1,14.3,0
                        c8.6-0.1,17.2-0.3,25.7-0.5c2,0,4-0.7,5.9-0.4c4.6,0.5,6.4,3.4,5,7.7c-0.6,1.9-1.5,3.7-2.8,5.3c-7.6,9.5-12.7,20.2-16.7,31.5
                        C756.3,702.6,754.5,705.4,752.3,709.7z M752.2,695.7c6.2-12,11.4-22.1,17.2-33.2c-12.7,0-23.7,0-35.7,0
                        C739.8,673.6,745.5,683.8,752.2,695.7z"/>
                    <!-- BACK -->
                    <path id="side_B_20" class="st0" d="M2448.6,2060.5c-1.6-3.2-3.4-6.4-4.9-9.6c-10.9-23.9-21.5-48-32.8-71.7c-19.7-41.4-27.1-85.2-29.2-130.6
                        c-2.4-52.9-6.3-105.7-9.2-158.6c-1.8-31.9-3-63.9-4.2-95.8c-0.3-7.3,0.8-14.6,1.6-21.9c0.9-8.4,3.5-10.3,12-8.4
                        c6.1,1.4,12,4,18.2,5.3c7.8,1.6,15.7,3.3,23.6,3.4c9.5,0,13.3-4.8,12.1-14.2c-1.1-8.2-3.2-16.3-4.9-24.5
                        c-6.5-30.2-8.8-60.6-3.1-91.3c2.6-13.7,8-26.1,16.7-37c6.2-7.8,13.9-13.4,23.7-15.7c11.9-2.8,17.1,0.2,19,12.3
                        c2.7,18.1,5.1,36.3,6.8,54.5c5.7,61.2,3.8,122.4,0.5,183.7c-2.9,54-10.7,107.3-19.5,160.6c-6.7,40.4-14,80.7-20.4,121.1
                        c-1.4,8.8-0.6,18-0.3,26.9c0.6,14.6,2.3,29.3,2.3,43.9c0,19-1,37.9-2,56.9c-0.2,3.6-2,7.2-3.1,10.7
                        C2450.6,2060.6,2449.6,2060.6,2448.6,2060.5z"/>
                    <path id="side_B_19" class="st0" d="M2256.1,1503.1c3.8,1.9,6.6,2.9,8.8,4.6c11.2,8.7,23.6,15,36.8,19.8c26.4,9.6,39.7,28.5,42.4,56.2
                        c3.4,34.8,8,69.5,11,104.3c3,35.5,4.5,71.2,7,106.7c2.2,31.2,4.5,62.5,7.3,93.6c1.7,18.9,4.3,37.7,6.5,56.6
                        c1.4,12.5,0.1,24.5-4.3,36.3c-6.3,16.7-14.6,32.1-26.9,45.2c-11.1,11.8-16.1,25.8-16.2,41.8c-0.1,16-0.4,32-0.7,48
                        c-0.1,3.6-0.6,7.1-0.9,10.7c-0.7,0.2-1.5,0.4-2.2,0.6c-2.8-5.4-5.8-10.7-8.2-16.2c-11.9-27.9-20.9-56.9-27.9-86.3
                        c-9-37.5-17.5-75.2-25.9-112.9c-7.8-35-8.8-70.6-9.9-106.3c-1.2-39-3.5-77.9-4-116.9c-0.4-32.3,0.9-64.6,2-96.9
                        c0.8-22.6,2.4-45.2,3.7-67.8C2254.7,1517.6,2255.4,1511,2256.1,1503.1z"/>
                    <path id="side_B_16" class="st0" d="M2335.5,1245.4c16.4,1.4,32.7,6.7,48.1,15.2c24.1,13.3,43.5,32,57.9,55.3c8.7,14,15.7,29.3,22.7,44.3
                        c3.5,7.4,2.2,9.7-4.3,14.2c-2.7,1.9-5.6,3.6-8.5,5.2c-14.4,7.9-23.8,19.9-29.2,35.3c-8.7,24.5-12.2,49.7-10.7,75.6
                        c0.8,13.3,2.5,26.5,3.7,39.7c0.4,4.6,1,9.3,0.9,13.9c-0.1,7.6-3.2,9.9-10.8,8.8c-1-0.1-2-0.4-2.9-0.7
                        c-47.1-12.3-91.5-30.5-130.4-60.4c-14.1-10.9-26-23.8-35.3-39.1c-10.5-17.5-10.1-28.8,2.2-45c17.9-23.7,34.4-48.2,48.1-74.6
                        c11.3-21.6,20.5-44,27.6-67.3c1-3.2,2.2-6.3,3.3-9.4C2321.5,1247.2,2324.7,1245.1,2335.5,1245.4z"/>
                    <path class="st0" d="M2351.9,2768c0.6-8.6,2.2-17.3,1.7-25.9c-1-20.3-3.1-40.5-4.4-60.7c-0.9-13.9-2.1-28-1.1-41.9
                        c0.6-9,4.1-18.1,7.7-26.6c3.7-8.7,11.5-12.5,20.9-13c19.4-1,33.7,8.8,41.2,26.8c5.8,13.7,13,26.9,19.5,40.3
                        c4.7,9.7,12.3,12.4,23,11.7c16.6-1.1,33.3-1.8,49.8-0.6c21.4,1.5,36.4,13.2,44.2,33.7c4.3,11.2,3.1,21.5-3.3,31.4
                        c-6.6,10.2-16,16.9-27.3,20.8c-9.1,3.1-18.4,5.9-27.6,8.6c-16.6,4.8-30.2,13.6-39.5,28.6c-6.3,10.1-15.6,16.4-27.1,19
                        c-16.9,3.8-33.8,7-51.3,4c-9.5-1.6-15.9-5.4-18.9-15C2355.3,2795.7,2351.8,2782.2,2351.9,2768z"/>
                    <path id="side_B_23" class="st0" d="M2344.1,2053.4c6.7,2.5,11.9,3.8,16.3,6.3c14.6,8.5,23.1,21.6,27.3,37.8c15.8,60.3,22.4,121.6,21.6,183.8
                        c-0.3,21.8-3.5,43.1-11.4,63.5c-5.9,15.3-14.9,28.2-29.1,37c-11.2,6.9-17.5,6.2-26.4-3.5c-18.5-20.3-26.6-44.9-28.6-71.8
                        c-2.6-35.9,5.3-70.1,17.1-103.6c8.7-24.8,13.4-50,13-76.3c-0.3-19-0.6-37.9-0.7-56.9C2343.3,2065.1,2343.7,2060.5,2344.1,2053.4z"/>
                    <path id="side_B_26" class="st0" d="M2342.7,2392.5c4.5,1.4,7.6,2.3,10.7,3.4c8.8,3.1,17.1,1.7,24.9-3c13.6-8.2,23.2-19.7,29.4-34.3
                        c4.5-10.8,4.6-10.4,15.9-10.8c7.6-0.3,15.2-1.8,22.7-2.8c2.9-0.4,5.8-0.9,9.9-1.5c-0.5,3.6-0.7,6.4-1.3,9.2
                        c-8.6,39.3-17.3,78.6-25.7,118c-2.9,13.9-4.8,28.1-7.3,42.9c6.5-1.1,7.3-5.2,8.3-9.4c4.5-19.8,8.6-39.7,13.7-59.4
                        c8.2-32.2,17-64.3,25.7-96.5c3.2-11.8,9.3-21.8,19.9-29.5c3.6,6.2,1.3,11.6-0.3,16.8c-4.5,15-9.9,29.7-13.9,44.8
                        c-14.4,55-28.6,110.1-42.2,165.4c-5,20.3-3.3,41.1-0.2,61.6c0.4,2.6,0.8,5.3,1.1,7.9c0.1,1-0.2,2-0.4,4c-4.6-3.3-6.7-7.8-9.4-11.7
                        c-8.8-12.1-19.9-21-35-22.6c-7.1-0.7-14.5,0.7-21.7,1.5c-2.9,0.3-5.8,1.4-10.4,2.5c0.8-4.2,0.9-7.4,2-10.2
                        c10.1-27.5,9.8-55.4,3.9-83.7c-6.3-30.3-12.4-60.6-18.5-91C2343.7,2401,2343.5,2397.7,2342.7,2392.5z"/>
                    <path id="side_B_24" class="st0" d="M2415.4,2030.6c3.9,5.4,7.8,10.4,11.1,15.6c31.7,49.6,52.6,103.7,66.2,160.8c5.4,22.8,5.9,46.2,4.3,69.6
                        c-1.8,27.8-28.1,45.9-50.9,45.3c-11.5-0.3-16.4-4.1-18.7-15.3c-1-4.8-1.5-9.9-1.7-14.8c-1.6-47.7-6.6-95-13.1-142.2
                        c-3.5-25.1-6.4-50.2-9-75.3c-0.8-7.6-0.3-15.3,0.3-22.9C2404.6,2043.2,2407.8,2036.1,2415.4,2030.6z"/>
                    <path id="side_B_14" class="st0" d="M2468,1322.3c-2.1-2.3-4.6-4.3-6.4-6.8c-3.8-5.4-7.4-11-11-16.6c-16.2-25.9-38.9-43.3-68.6-51.2
                        c-12.8-3.4-25.7-6.6-38.6-9.9c-11.4-3-12.3-4.6-8.7-16.1c2.2-7,4.5-13.9,6.9-20.8c4.1-12.2,12.1-21.6,23-27.8
                        c4.6-2.6,10.8-3.6,16.1-3.1c18.7,1.6,34,10.5,46.9,23.8c14.3,14.7,23,32.6,27.7,52.2c5.3,21.6,9.7,43.4,14.3,65.2
                        c0.7,3.3,0.1,6.9,0.1,10.3C2469.2,1321.7,2468.6,1322,2468,1322.3z"/>
                    <path class="st0" d="M2409,2181.1c1,1.9,2.6,3.7,2.9,5.6c4.8,33.6,7.2,67.5,8.2,101.5c0.2,6.6,0.8,13.3,2,19.9
                        c2.3,13.2,12.8,20.8,26.1,18.7c6.5-1,12.8-3.4,19.1-5.4c4-1.3,8-3,13.9-5.3c-4.6,7.3-8.6,12.9-11.7,18.8c-3.2,6-5.5,12.5-9.1,20.9
                        c0.9-7.8,1.6-13.3,2.4-19.8c-6.1,1-11.2,2-16.3,2.7c-9.2,1.3-18.4,3-27.7,3.6c-7.6,0.5-12.1,3.7-14.8,10.9
                        c-5.1,13.7-13.3,25.3-25.8,33.4c-3.2,2.1-6.8,3.5-11.3,3.7c1.2-1.2,2.3-2.5,3.7-3.4c15-9.4,24.9-22.8,31.4-39
                        c6.9-17.1,10.8-35,11.5-53.4c1.4-38-0.5-75.9-4.6-113.7L2409,2181.1z"/>
                    <path class="st0" d="M2383.6,2058c-7.7-6.2-14.5-11.5-21-17.1c-5.6-4.8-5.8-5.8-2.3-12.7c6-11.8,12.2-23.5,18.1-35.4
                        c2.4-4.7,4.2-9.7,6.7-15.4c1.8,1.9,3.7,3.2,4.7,5c3.9,6.9,7.5,14,11.2,21.1c2.3,4.3,3,8.8,1,13.4
                        C2396.1,2030.2,2390.2,2043.4,2383.6,2058z"/>
                    <path class="st0" d="M2432.5,1123.7c6.4,23.4,12,44.5,14.8,67.7c-10.9-3.3-15.2-12.1-22.4-17.1c-7-4.9-13.8-10.2-21.6-16
                        C2412.9,1147,2421.9,1136.3,2432.5,1123.7z"/>
                    <path class="st0" d="M2409.1,2180.9c-0.4-3.8-0.8-7.7-1.2-11.5c0.7,0,1.3,0,2,0c-0.3,3.9-0.6,7.8-0.9,11.7
                        C2409,2181.1,2409.1,2180.9,2409.1,2180.9z"/>
                    <path class="st0" d="M2407,2148.4c0.3,3.5,0.6,7,0.8,10.6c0,0.5-0.6,1-1.6,2.9C2406.4,2156.4,2406.7,2152.4,2407,2148.4
                        C2407,2148.3,2407,2148.4,2407,2148.4z"/>
                    <path class="st0" d="M2307.5,468.3c-3.9-17.6-7.8-34.4-11.3-51.3c-6.1-29.8-26.3-45.2-54-52.4c-24.5-6.4-46.8-0.7-66.9,14.3
                        c-10.8,8-18.5,18.3-21.6,31.4c-4.2,17.5-7.8,35.1-11.7,52.6c-0.3,1.6-0.8,3.1-1.2,4.6c-7.7-1.4-9.6-3-10.8-10.6
                        c-1.6-9.9-2.7-19.8-3.9-29.7c-0.9-6.9-1.4-13.9-2.4-20.8c-1-7.5-1.4-8.3-9.1-9.2c-7.9-0.9-12.6-5-15.7-12
                        c-4.6-10.7-10-21.1-14.5-31.8c-5.4-13-0.6-21.9,13.1-25.5c12.3-3.2,12.8-3.6,12.3-16.2c-0.9-22.8,0.4-45.4,8.4-66.9
                        c15-40.5,42.8-67,85.9-75c57.1-10.6,111.9,24.3,127.8,80.4c5.7,20.1,6.4,40.7,6,61.4c-0.1,5.3,0,10.6,0,14.9
                        c5.5,0.7,10.7,0.3,15,2.1c4.4,1.9,8.2,5.5,11.4,9.2c3.4,3.8,2.9,8.7,0.8,13.2c-5.7,12.4-11.5,24.7-17.4,37c-2.5,5.2-6.7,7.9-12.4,9
                        c-10.3,1.8-10.1,2-11.3,12.5c-1.8,15.5-3.7,31.1-5.8,46.6C2317.1,463.9,2315.9,464.9,2307.5,468.3z"/>
                    <path id="side_B_4" class="st0" d="M2212.9,945.6c-0.7-1.4-1.6-2.7-2.2-4.2c-8.1-21-19.7-40-32.5-58.3c-21.2-30.3-41.7-61.1-63.8-90.7
                        c-22.4-30.1-36-63.4-42.6-100c-2.1-11.4-5-22.7-8.2-33.9c-6-20.9-17-38.2-36.8-48.9c-1.6-0.9-2.9-2.4-5.3-4.4c2.4-1,3.7-1.8,5.2-2.1
                        c22.8-4.8,45.4-10.5,68.3-14.1c22.9-3.6,45.9,0.2,68.3,5.4c11.7,2.7,19.7,10.8,25.2,21.3c13.2,25.4,22,52.2,23.4,80.9
                        c1.6,34.6,2.2,69.2,2.8,103.8c0.7,44.3,0.9,88.6,1.2,132.9c0,4.1-0.8,8.1-1.2,12.2C2214.1,945.5,2213.5,945.5,2212.9,945.6z"/>
                    <path id="side_B_5" class="st0" d="M2230,944.1c-0.2-2.6-0.6-5.2-0.6-7.7c0.4-56.3,0.7-112.6,1.5-169c0.4-25.6,1.6-51.3,3.1-76.9
                        c0.5-9.2,1.7-18.9,4.9-27.4c6.6-17.2,14.3-34.1,22.6-50.6c5.3-10.5,14.9-16.4,26.8-19c36.2-7.8,72.1-7.2,107.7,3.3
                        c8.8,2.6,17.6,5.1,28.4,8.2c-4,3-6.1,4.9-8.5,6.3c-15.9,9.5-26.5,23.3-31.5,41c-4.9,17.3-9.6,34.6-13.4,52.2
                        c-7.7,35.4-22.8,67.3-44.1,96.4c-18.5,25.3-36.9,50.6-55.4,75.9c-14.8,20.2-28.3,41.2-37.7,64.6c-0.5,1.1-1.3,2.1-2,3.2
                        C2231.2,944.4,2230.6,944.2,2230,944.1z"/>
                    <path id="side_B_10" class="st0" d="M2212.9,1378.9c-9.7-10.1-17.4-21.8-22.9-34.6c-7.8-18-14.7-36.4-21.5-54.7c-6.7-18.1-13-36.3-19.1-54.6
                        c-7.4-21.9-18.4-42-30.9-61.3c-5.6-8.7-13.5-14.5-23.1-18.2c-9.3-3.6-10.2-5-9.2-14.9c4.5-46.5,19.1-89,50.3-124.7
                        c15.5-17.8,31.6-35.2,47.4-52.8c1.1-1.2,2.3-2.4,3.5-3.5c6.6-5.6,9.9-5,13.5,2.9c8.1,17.4,13.2,35.5,13.3,54.9
                        c0.5,96.2,1.1,192.5,1.6,288.7c0.1,21,0,42,0,62.9c0,3.2-0.3,6.4-0.5,9.5C2214.4,1378.6,2213.7,1378.7,2212.9,1378.9z"/>
                    <path id="side_B_1" class="st0" d="M2162.4,512.4c1.9-4.4,4.2-8.7,5.5-13.3c7.3-26,12.5-52.4,14.3-79.5c0.3-5,0.7-10,1.1-14.9
                        c1.5-18.6,9.7-26.1,29.9-26.5c0.7,9.1,1.8,18.2,2.2,27.4c1.1,32.6,2.4,65.3-1.4,97.8c-1.7,14.6-4.3,28.9-10.5,42.5
                        c-3.2,7-7.8,10.2-16.1,10.5c-24.7,0.9-49,5.2-73,10.6c-1.6,0.4-3.2,0.8-4.9,1c-0.5,0.1-1.1-0.4-2.7-1.1c4.4-2.7,8.2-5.3,12.2-7.4
                        c16.4-8.9,25.9-22.6,27.8-41.2c1.9-18.6,3.5-37.1,5-55.7c1.3-16,2.5-31.9,9.9-46.6c1.7-3.3,3.9-6.4,7.1-9.2
                        c2.5,35.6-4.3,70.1-8.9,104.8C2160.8,511.8,2161.6,512.1,2162.4,512.4z"/>
                    <path id="side_B_2" class="st0" d="M2339.8,567.9c-11.7-2.2-23.4-4.9-35.2-6.6c-15.1-2.2-30.4-4.2-45.6-5.1c-7.1-0.5-10.9-3.3-13.8-9.2
                        c-6.5-13-9.3-27.1-10.9-41.3c-4.2-36.2-3.3-72.5-1-108.8c0.4-6.2,0.9-12.5,1.3-18.6c19-0.5,28.8,7.6,29.8,25
                        c1.8,30.4,6.1,60.3,13.9,89.7c1.8,6.9,3,14.1,9.9,21.2c-4.7-36.9-11.6-71.5-10.4-109.2c3,3.1,4.9,4.3,5.9,6.1
                        c5.5,10,9.3,20.7,10.4,32.1c2.1,21.5,3.9,43.1,5.9,64.7c0.4,4.6,1,9.3,1.6,13.9c2.4,15.9,10.7,27.7,24.3,36
                        c4.8,2.9,9.6,5.8,14.4,8.7C2340.2,566.8,2340,567.4,2339.8,567.9z"/>
                    <path class="st0" d="M2224.8,481.1c1.1,11.5,1.6,23.1,3.3,34.6c1.6,10.8,4.1,21.5,6.8,32.2c2.1,8.4,7.7,12.1,16.9,12.4
                        c33.3,0.9,69.6,6.9,95.3,15.8c-2.2-0.1-4.5,0.2-6.5-0.4c-29-8.5-59-9.7-88.8-13.1c-7.5-0.9-12.7,2-16.2,8.9
                        c-7.3,14.3-9.9,29.5-10,45.4c0,6.8-0.3,13.5-0.5,20.3c-0.9,0-1.9,0-2.8,0c0-6.4-0.1-12.9,0-19.3c0.3-15.2-2.1-29.9-8.6-43.7
                        c-5.1-10.9-8.5-12.7-20.5-11.2c-23.8,2.9-47.5,6-71.3,9.3c-7.1,1-14,3-21.2,3.6c18.1-6,36.6-10.3,55.7-11.8
                        c14.9-1.1,29.8-2.7,44.7-4.2c5.1-0.5,8.8-3.5,10.3-8.3c2.4-7.3,5-14.5,6.2-22c2.1-12.8,3.1-25.8,4.5-38.7c0.4-3.2,0.5-6.5,0.8-9.7
                        C2223.6,481.1,2224.2,481.1,2224.8,481.1z"/>
                    <path id="side_B_2_1" class="st0" d="M2323.1,576c-19.9,0.8-40.1-0.1-57.8,11.6c-17.1,11.3-25.5,29-35.3,46.2c0-4.8-0.3-9.5,0.1-14.3
                        c1-13.3,2.6-26.5,7.7-39c4.6-11.3,7.4-13.4,19.5-12.5c21.8,1.6,43.7,3.7,65.5,5.5C2322.8,574.5,2322.9,575.3,2323.1,576z"/>
                    <path id="side_B_1_1" class="st0" d="M2217.6,634c-9.4-17.8-18.2-35.7-35.9-46.9c-17.5-11.1-37.3-10.2-56.9-12.6c9.8-1.2,19.6-2.6,29.4-3.5
                        c13.6-1.2,27.2-1.9,40.8-3c5.8-0.5,9.6,2.1,12.3,6.7c1.7,2.9,3.2,5.9,4.2,9C2216.7,599.9,2218.5,616.7,2217.6,634z"/>
                    <path class="st0" d="M2270.5,413.4c-0.7-5.8-1.4-11.6-2-17.4c-0.8-8.4-5.2-14.2-12.7-17.7c-21.2-10-42.4-10-63.6,0
                        c-7.5,3.5-11.9,9.3-12.7,17.8c-0.5,5.6-1.2,11.2-2.5,16.9c-2.7-7.8-3.8-16-2.8-24.1c0.3-2.5,3-5.1,5.2-6.9
                        c23.3-19.4,66.1-19.3,89.5-0.1c3.9,3.2,6.2,6.8,5.4,12c-0.6,3.9-0.6,8-1.1,11.9c-0.3,2.6-0.9,5.1-1.4,7.7
                        C2271.3,413.4,2270.9,413.4,2270.5,413.4z"/>
                    <path class="st0" d="M2223.9,415c-3.3-19.7-2.9-35,0.7-38.6C2226.9,382,2226.8,401.2,2223.9,415z"/>
                    <path id="side_B_17" class="st0" d="M2072.7,1561.8c1.2,7.7,3,14.5,3.1,21.3c0.3,21.3,0.9,42.7-0.3,63.9c-3.2,55.2-7.4,110.3-11,165.5
                        c-1.6,23.9-2.3,47.9-4.2,71.8c-2.3,29.8-10.2,58.3-22.3,85.6c-12.4,28-25.2,55.8-37.9,83.6c-1.2,2.7-2.8,5.1-4.5,8
                        c-4.6-3.3-4.9-7.6-5.6-11.7c-3.7-24.9-2.9-49.8-1-74.8c1.5-19.5,4.4-39.1,0.8-58.8c-3.5-18.7-7.3-37.2-10.2-56
                        c-7.5-48-15-96-21.7-144.2c-8.1-58.5-10.9-117.4-10.5-176.4c0.2-43,2.2-85.9,8.8-128.5c0.7-4.3,1.3-8.6,2.4-12.7
                        c2.1-7.6,6.4-10.2,14.2-9.3c10.4,1.2,18.9,6.2,25.7,14c11,12.7,17.2,27.6,19.6,44.1c4.7,31.1,1.6,61.8-5.1,92.2
                        c-1.2,5.5-2.7,11-3.5,16.6c-1.7,11.9,3.8,17.9,15.8,16.7c6.6-0.7,13-2.5,19.5-4.1c4.8-1.2,9.6-2.8,14.4-4
                        C2063,1563.8,2066.9,1563.1,2072.7,1561.8z"/>
                    <path class="st0" d="M2098.5,2650.7c-1.6,15.9-3.7,31.7-4.6,47.7c-1.1,19.9-1.6,39.9-1.5,59.9c0.1,17.1-1.4,33.8-6.7,50.2
                        c-3.2,9.7-9.4,14.5-19.5,15.8c-19.2,2.4-37.8,0.6-56.1-5.8c-7.8-2.7-14.1-7-18.8-14c-12-18-29.3-28.5-49.8-34.4
                        c-8.6-2.4-17.6-4.5-25.4-8.6c-7.5-3.9-14.6-9.4-20.3-15.7c-9.3-10.3-10.7-22.9-4.9-35.4c8.7-18.8,23-31.4,44.3-32.5
                        c16.9-0.9,33.9-0.1,50.8,1c9.1,0.6,15.3-2,20.1-9.6c8.6-13.5,15.9-27.6,21.1-42.8c5.9-17.2,18.8-24.9,36.3-26.5
                        c12.5-1.1,21.1,4.5,26.9,15.1C2096.7,2626.1,2098.6,2638.1,2098.5,2650.7z"/>
                    <path id="side_B_22" class="st0" d="M2101.3,2055c0,14.6,0.1,28.2,0,41.8c-0.2,22.3-2,44.7,2.7,66.7c3.5,16.5,7.8,33,13.1,49
                        c9.6,29,15.5,58.5,14.2,89.2c-1.2,28.1-9.3,53.6-27.8,75.3c-9.5,11.1-16.1,12.1-28.5,4.1c-12.5-8-21.1-19.3-26.6-32.8
                        c-7.9-19.6-12.4-39.9-12.6-61.2c-0.6-57.1,3.7-113.8,16.3-169.6c2-9.1,4.6-18,7.6-26.8c3.4-9.9,9.2-18.4,17.2-25.4
                        C2083.4,2059.5,2090.8,2055.2,2101.3,2055z"/>
                    <path id="side_B_25" class="st0" d="M2086.9,2589.3c-33.7-13.3-57.6,0.3-76.6,28.6c0.6-4,1.1-8,1.8-12c5.1-31.6,0.3-62.4-7.1-93
                        c-14.2-58.5-29.2-116.8-48-174c-2.1-6.5-3-13.4-5-22.5c3.9,2.7,5.9,3.6,7.4,5.1c8.4,8.4,14.2,18.4,17.1,29.9
                        c12.6,50,25.1,100,37.7,150c1.1,4.3,0.8,9.5,7.3,11.4c1.5-4.9,0.3-9.3-0.6-13.7c-9.9-47.2-19.8-94.4-29.7-141.6
                        c-0.9-4.2-1.7-8.4-2.8-14c7.3,0.9,13.8,1.8,20.2,2.6c5.3,0.7,10.6,1.7,15.9,1.7c6.2,0,9.9,2.4,12,8.3c3.8,10.9,10.5,19.9,18.7,28
                        c12.3,12,26.1,17,42.7,9.6c0.9-0.4,1.9-0.5,4.3-1c-1,6.3-1.7,12-2.8,17.7c-6.4,32.6-13.2,65.2-19.4,97.9c-4.4,23-2.4,45.7,5.2,67.9
                        c1.2,3.4,2,7,2.9,10.5C2088,2587.2,2087.5,2587.8,2086.9,2589.3z"/>
                    <path id="side_B_21" class="st0" d="M2029.4,2030.2c8.7,6.9,12.5,15.5,11.9,25.7c-1.1,18.9-1.8,37.9-4.2,56.7c-7.4,57.5-15.6,114.8-17.5,172.8
                        c-0.2,6.6-0.7,13.3-1.7,19.9c-1.6,11.4-7.4,16.2-18.8,16.6c-26.8,0.8-49.5-20.1-51.2-47.1c-2-30.5,1.8-60.2,9.8-89.8
                        c14.9-54.9,39.6-105.1,69.9-152.8C2027.8,2031.7,2028.4,2031.3,2029.4,2030.2z"/>
                    <path id="side_B_7" class="st0" d="M1978.4,864c2-23.9,2-45.8,5.9-66.9c7.3-39.1,29.3-69.4,61.4-92.6c0.5-0.4,1.1-0.8,1.7-1.1
                        c8.9-5.6,12.2-4.4,14.8,5.7c1.7,6.4,2.9,13,4.7,19.4c6.5,23.6,17.3,45.3,30.8,65.7c25.8,38.8,51.5,77.8,77.1,116.7
                        c12.8,19.4,12.5,22.5-1.7,40.5c-15.4,19.5-32.6,37-53.1,51.1c-34,23.4-70.6,19-98.2-12c-20.3-22.8-32.5-49.4-37.7-79.3
                        C1981.4,894.9,1980.2,878.3,1978.4,864z"/>
                    <path id="side_B_3" class="st0" d="M1853.5,775.9c3.1-39.4,11.9-74.7,36.8-104.4c11.5-13.7,25.2-24.9,41-33.1c15.6-8.1,31.8-15.4,47.8-22.7
                        c8.7-3.9,17.8-3.5,26.1,0.7c22.8,11.3,37.4,29.4,43.2,54.4c1.7,7.2-0.3,12.2-6.5,16.1c-19.3,12.2-35.4,28.1-51.4,44.3
                        c-10.3,10.5-20.6,20.9-31.4,30.8c-21,19.3-46.5,28.2-74.6,29.8c-8.2,0.5-16.6-1.1-24.8-2c-4.3-0.5-6.3-3.3-6.2-7.6
                        C1853.6,779.4,1853.5,776.8,1853.5,775.9z"/>
                    <path class="st0" d="M1878.2,906.1c0,12-0.2,22,0,31.9c0.4,15.2,6,27.8,18.6,36.9c9.6,7,17.7,7.4,27.7,0.9
                        c4.9-3.1,9.6-6.5,16.4-11.2c-1.5,4.1-1.9,6-2.8,7.6c-10.4,18.6-20.6,37.3-31.6,55.5c-8.7,14.5-21.1,24.8-38.6,27.3
                        c-4.6,0.6-9.4,0.9-13.9,0c-13.7-2.9-27.2-6.4-40.7-9.6c-4.8-1.1-7.3-4.2-8.1-9c-5.9-33.4-4.9-66.3,7.5-98.2
                        c2.1-5.5,5.8-10.1,12.1-10.8c15.7-1.8,29-9.2,42.5-16.5C1870,909.4,1873.1,908.3,1878.2,906.1z"/>
                    <path class="st0" d="M1968.2,832.2c0.2,20.6,0.2,41.2-4,61.6c-5.9,28.7-22.6,50.7-44.1,69.4c-8.4,7.4-16.2,5.1-20.3-5.5
                        c-5.5-14.3-5.3-29.3-4.7-44.3c1.3-31,11-59.3,28.3-84.9c8.8-12.9,18.1-25.5,27.2-38.2c0.8-1.1,1.5-2.4,2.6-3
                        c2.8-1.4,5.9-3.6,8.8-3.3c4.2,0.3,5.3,4.6,5.8,8.3c0.4,2.9,0.3,6,0.3,9C1968.2,811.6,1968.2,821.9,1968.2,832.2z"/>
                    <path id="side_B_9" class="st0" d="M1997.8,978.1c1.6,1.9,3.3,3.7,4.7,5.8c5.8,8.5,11.5,17.2,17.2,25.7c7.6,11.3,17.9,18.4,31.5,20.8
                        c14.7,2.6,29.1,2.2,43.6-1.4c3.2-0.8,6.4-1.2,11.5-2.2c-19.1,39.9-30.9,80.5-38.8,123.4c-2.7-1.8-4.9-3.1-6.8-4.7
                        c-11.1-9.3-19.5-20.9-26.6-33.4c-17.8-31.7-28.1-66-34.2-101.6c-1.8-10.4-3.2-20.9-4.8-31.4C1996,978.7,1996.9,978.4,1997.8,978.1z"
                        />
                    <path class="st0" d="M1922.2,799.5c-2.2,5.5-3.6,9.5-5.5,13.3c-6.7,13.7-13.8,27.3-20.4,41.1c-7.9,16.5-19,30.4-33.6,41.4
                        c-7.4,5.6-14.8,11.3-22.3,16.9c-5.4,4-11.2,5.8-17.6,2c-6.2-3.7-8-9.2-7.4-16.2c1.7-18.3,4.3-36.3,11.5-53.4
                        c4.7-11.3,9.6-22.6,14.3-34c1.8-4.4,4.5-7.4,9.8-6.5c21.1,3.6,41.7,0.2,62.3-4.1C1915.5,799.5,1917.8,799.7,1922.2,799.5z"/>
                    <path class="st0" d="M2730.1,1231.1c2.7,1.7,3.9,2,4.2,2.7c18.3,53.8,36.5,107.6,55.1,162.5c-2.3,0.5-4.8,1.8-7.1,1.6
                        c-19.9-2-38.2,3.5-56,11.7c-3.9,1.8-8.1,3.2-11.9,5.1c-4.9,2.4-8.3,0.9-10.8-3.6c-1-1.7-1.9-3.5-2.8-5.3
                        c-27.2-53.4-54.5-106.7-81.4-160.2c-6.7-13.3-12.8-27.1-18-41.1c-13-35-27.8-69.1-44.1-102.6c-6.7-13.8-7.3-21-3.9-35.9
                        c2.6,2.1,5,3.6,6.7,5.7c15.3,18.9,31.1,37.4,45.3,57.2c7.2,10.1,12.6,22,16.7,33.8c17.1,49.3,33.3,98.9,50,148.4
                        c1.8,5.3,4,10.4,6.2,16.4c5.2-3.7,3.4-7.3,2.2-11c-6.5-19.9-12.9-39.9-19.3-59.8c-10.4-32.3-20.8-64.6-31-96.9
                        c-4.9-15.7-12-30.2-21.3-43.8c-4.9-7.1-9.3-14.6-13.3-22.2c-3.6-6.9-2.6-10,3.8-14.3c3.3-2.2,7-3.8,10.5-5.7
                        c5.4-2.8,10.2-2,14.7,1.9c14,11.8,28.4,23.2,42,35.4c34.2,30.5,49.1,69.2,49,114.7c-0.1,26.6,1,53.3,2,79.9
                        c0.3,9.3,1.7,18.6,3.1,27.8c0.5,3.6,0.5,8.3,6,9.2c4.2-3.5,2.2-8.1,2.2-12.2c-0.1-25-0.7-49.9-0.7-74.9
                        C2728,1247.8,2729.3,1240.2,2730.1,1231.1z"/>
                    <path class="st0" d="M2787.4,1570.9c-10.1,8.3-10.3,8.8-5.9,18.7c4.9,10.9,9.9,21.7,15,32.9c-5.3,2.9-8.3,0.9-10.7-2.6
                        c-2.8-4.1-5.8-8.2-8.2-12.5c-14.3-25.6-28.7-51-42.4-76.9c-9.4-17.7-13.3-36.7-12.4-56.9c0.5-10.6-0.3-21.3-1-31.9
                        c-0.4-7.3,2.4-11.9,9.1-14.9c12.8-5.6,24.9-12.5,38.9-15.1c19.7-3.6,37.7,0.5,54.4,10.9c12.6,7.9,23.3,18,32.2,29.7
                        c16.2,21.4,35.4,38.7,61,48.3c1.4,0.5,2.5,2,3.9,3.1c-1.3,8.3-7.1,11.3-14,12.2c-4.2,0.6-8.7,0.3-12.9-0.7
                        c-16.1-3.6-33-5.3-45.5-17.1c-11.7,5.4-12.4,7.1-7,17.6c10.3,19.8,20.9,39.4,31.1,59.2c3.7,7.1,6.7,14.5,9.9,21.8
                        c0.7,1.5,1.2,3.1,1.7,4.7c1,3.4,0.9,7.3-2.7,8.4c-2,0.6-5.3-1.7-7.2-3.5c-2.1-2-3.3-4.9-4.8-7.5c-10.3-17.9-20.6-35.8-30.9-53.7
                        c-2-3.4-4.1-6.8-6.1-10.1c-7.9,4.3-8.2,4.7-4.5,12c13,25.8,26.2,51.6,39.4,77.4c1.5,3,3.1,5.9,4.2,9.1c2.1,6.1,0.7,11-3.4,13.3
                        c-3.9,2.2-7.5,1.1-11.9-4.4c-3.5-4.4-6.6-9.2-9.2-14.2c-10.6-20.7-21-41.6-31.4-62.4c-1.6-3.2-3.4-6.4-5.1-9.5
                        c-8.6,3.7-9.1,4.5-5.3,12.7c9,19.3,18.1,38.5,27.2,57.8c1.8,3.9,3.6,7.9,5.5,11.8c1.5,3.3,2.2,7.1-1.5,8.7c-2.6,1.1-7.2,1.1-9.1-0.5
                        c-4.2-3.6-8.1-8-10.6-12.9c-6.8-13.3-12.7-27.1-19-40.7C2794.7,1585.8,2791.2,1578.7,2787.4,1570.9z"/>
                    <path class="st0" d="M2655.5,1021.4c26.2,36.9,48.6,74.6,52.8,123.1c-3.3-3-5.4-4.3-6.6-6.2c-14.7-22.2-31.2-43-49.5-62.4
                        c-5.4-5.7-7.8-12.4-6.2-20.1c2-10.4,4.4-20.7,6.8-31.1C2653,1024,2654,1023.3,2655.5,1021.4z"/>
                    <path id="side_B_18" class="st0" d="M2187.4,1502.9c1.2,4,2.5,6.6,2.7,9.3c2.3,40.9,5,81.8,6.5,122.7c0.8,22.9-0.1,46-0.7,68.9
                        c-1.1,41.6-2.6,83.2-4,124.8c-1.7,48.5-10.1,95.9-22.4,142.8c-8.4,32.2-16.3,64.5-25.5,96.5c-5,17.2-12,33.8-18.4,50.6
                        c-1.1,2.8-3.5,5.2-6.8,9.8c-0.8-4.6-1.3-6.7-1.4-8.7c-0.5-19.3-1.2-38.6-1.3-57.9c0-11.2-3.7-20.7-10.7-29.3
                        c-6.5-8-12.9-16.2-18.7-24.7c-14.4-21.2-19.8-44.4-16.7-70.2c4.5-38.7,7.8-77.5,11.1-116.3c1.6-18.6,1.5-37.3,2.8-55.9
                        c1.9-27.9,4-55.8,6.6-83.7c2.8-30.2,6.2-60.3,9.5-90.4c0.7-6.6,2.1-13.1,3.4-19.7c3.9-19.9,15.7-33.3,33.9-41.5
                        c9.7-4.3,19.6-8.5,29-13.4C2173,1513.1,2179,1508.4,2187.4,1502.9z"/>
                    <path id="side_B_15" class="st0" d="M2217.1,1430.6c-2.3,6.3-4,12.5-6.8,18c-9.5,18.8-24.1,33.3-40.8,45.9c-31.8,23.9-67.7,39.5-105.3,51.6
                        c-7.6,2.4-15.3,4.8-23.1,6.3c-9.9,1.9-13.7-1.9-12.7-12c1.4-14.2,2.9-28.5,4.4-42.7c3.1-28.7-0.9-56.4-10.4-83.5
                        c-5.5-15.8-16-27.4-30.5-35.6c-15.7-9-15.7-9.1-8.6-25.4c13-30,29.8-57.3,56-77.8c20-15.7,42.1-26.8,67.7-29.6
                        c13.7-1.5,16.7,0.3,20.5,13.1c15.8,52.6,41.6,100,74.5,143.7c3,4,6.3,7.9,8.6,12.2C2213.4,1419.7,2215,1425.1,2217.1,1430.6z"/>
                    <path id="side_B_13" class="st0" d="M1974,1322.5c0.4-4.4,0.3-7.3,0.9-10c5.7-24.6,11.3-49.3,17.3-73.9c5.6-22.8,17.7-41.6,37.1-55.1
                        c5.7-3.9,12.1-7,18.5-9.7c25.9-10.8,41.9-1,54.3,24.5c4.2,8.6,6.4,18.2,9.1,27.4c2.1,7.1,1.2,8.7-6,10.8c-11.2,3.3-22.3,6.7-33.7,9
                        c-36.3,7.4-62.6,28-81.1,59.5C1986.7,1311.4,1983.3,1317.9,1974,1322.5z"/>
                    <path class="st0" d="M2061.1,2058c-6.7-15.2-12.9-28.7-18.4-42.5c-1.2-3-0.8-7.6,0.6-10.6c4.5-9.5,9.9-18.6,15.5-28.9
                        c1.6,3.2,2.6,5.2,3.6,7.1c7,14.3,13.6,28.7,20.9,42.8c4.7,9.1,5,10-2.9,16.6C2074.5,2047.4,2068.5,2052,2061.1,2058z"/>
                    <path class="st0" d="M1999.2,1190.3c2.1-22.5,6.8-44,13.3-66.8c9.8,11.8,18.6,22.4,28.3,34
                        C2027.4,1169.1,2014.4,1180.5,1999.2,1190.3z"/>
                    <path class="st0" d="M2023.1,1181.3c-2.4,2.2-4.8,4.4-7.3,6.6c-0.4-0.5-0.9-0.9-1.3-1.4c2.4-2.2,4.8-4.4,7.2-6.6
                        C2022.2,1180.4,2022.7,1180.9,2023.1,1181.3z"/>
                    <path id="side_B_8" class="st0" d="M2465.9,846.6c1.1,30.2-1.2,61.6-11.7,92c-6.3,18.1-15.4,34.6-27.7,49.4c-10.7,12.8-24,21.9-40,27.1
                        c-17.1,5.5-33.6,4-49-5.2c-28.2-16.7-51.1-39.4-70.5-65.6c-5.8-7.8-6.2-15.9-1.6-24.3c1.4-2.6,3-5.2,4.7-7.7
                        c27.3-41.8,54.6-83.6,82.1-125.3c14.5-21.9,24.9-45.5,29.7-71.4c0.7-3.6,1.4-7.2,2.8-10.6c1.9-4.9,4.7-5.8,9.5-3.8
                        c1.2,0.5,2.4,1.2,3.5,2c36.2,25.2,58.7,59.4,65.7,103.1C2465.3,818.9,2465,832,2465.9,846.6z"/>
                    <path class="st0" d="M2499.7,962.5c6.3,4.8,10.9,8.4,15.7,11.8c12.4,8.7,20.8,8.3,32.4-1.3c10.9-9.2,15.7-21.1,16.1-35.1
                        c0.3-9.9,0.9-19.7,1.4-31.3c4.8,2.2,8,3.5,11.1,5.1c13.5,7.3,27.1,14.2,42.7,16c6,0.7,8.2,5.2,10.1,9.9
                        c5.9,14.6,10.1,29.8,10.4,45.5c0.4,16.9-1.2,33.8-2.2,50.7c-0.4,6.7-4,10.9-10.9,12.4c-12,2.7-23.7,6.7-35.8,8.4
                        c-24.2,3.4-42.9-5.6-55.2-27.1c-11.6-20.2-23.2-40.4-34.7-60.6C2500.4,966.4,2500.4,965.7,2499.7,962.5z"/>
                    <path class="st0" d="M2548.4,923.3c-1.5,10.5-2.1,21.2-4.8,31.4c-3.6,13.8-13.5,16.3-23.9,6.8c-29.4-26.9-46.1-59.7-45.8-100.3
                        c0.1-20.3-0.2-40.6-0.2-60.9c0-3.3-0.2-6.7,0.6-9.9c1.6-7,8.6-9.2,13.6-4c4.1,4.3,7.5,9.3,11.2,14.1c13.9,18.3,27.6,36.7,35.8,58.5
                        C2542.7,879.6,2547.2,900.9,2548.4,923.3z"/>
                    <path id="side_B_12" class="st0" d="M2378.6,1150.5c-9-43.1-19.6-83.2-37.8-122.4c8.7,1.1,15.8,2.6,23,2.9c9.6,0.3,19.3,0.8,28.7-0.7
                        c17-2.7,29.7-12.6,38.9-27.1c3.5-5.6,6.8-11.4,10.3-17.1c2.3-3.6,4.7-7.1,8.1-12.2c0.7,3.4,1.5,5.2,1.2,6.9
                        c-5.5,40-14.2,79.1-31.3,116c-7.3,15.8-16.2,30.6-28.4,43.1C2387.9,1143.3,2383.9,1146.1,2378.6,1150.5z"/>
                    <path class="st0" d="M2519.2,798.4c12.7,2.2,23.4,4.7,34.2,5.6c11.2,0.9,22.5,0.7,33.8,0.3c9.6-0.4,10.6-0.4,14.7,8.2
                        c10.1,21.7,19.5,43.8,23.2,67.7c1,6.5,1.5,13.3,1.1,19.9c-1,14.8-10.8,20.3-23.3,12.6c-24.2-14.8-44.6-33.6-57.5-59.5
                        c-7.3-14.6-14.4-29.2-21.5-43.9C2522.4,806.5,2521.3,803.4,2519.2,798.4z"/>
                    <path class="st0" d="M1708.3,1231.4c5.4,3.4,5.1,7.6,5.1,11.5c0.1,29.3,0.1,58.6,0.2,88c0,3.6,0.4,7.2,0.5,10.5
                        c4.8,1.9,6.3-0.7,6.8-3.9c1-5.6,1.8-11.2,2.5-16.8c3.1-25.8,4.8-51.7,4-77.8c-0.6-19.6-1.1-39.3,3.3-58.7
                        c6.3-27.6,21.7-49.9,41-69.7c8.3-8.5,18-15.7,27.1-23.5c4.8-4.1,9.8-8,14.5-12.2c9.4-8.4,22-8.2,31.2,0.8c3.8,3.7,4.5,8.2,2.6,12.8
                        c-2.1,4.9-4.2,9.9-7.4,14.1c-13,16.8-21.8,35.8-28.3,55.8c-16.9,51.9-33.6,103.8-50.3,155.7c-0.7,2.3-0.5,4.9-1,9.7
                        c3.1-2.6,4.9-3.5,5.6-4.9c1.8-3.6,3.2-7.3,4.4-11.1c14.8-44.5,29.6-89,44.2-133.6c9.4-28.5,23.1-54.4,43.3-76.9
                        c7.8-8.7,14.8-18,22.3-27c2.5-3,5.2-5.9,8.7-9.7c4.2,9.8,3.1,18.5,0.6,27c-1.6,5.4-4,10.6-6.5,15.6c-13.3,26.5-24.9,53.7-35.2,81.5
                        c-16.4,44.4-37.5,86.7-59.6,128.6c-14.8,28-29.3,56.1-43.9,84.1c-1.5,3-3,5.9-4.8,8.8c-3.7,6.1-5.7,6.8-12.2,4.2
                        c-4.9-2-9.8-4.2-14.7-6.3c-16.7-7.3-33.9-12-52.3-10c-2.2,0.2-4.6-0.3-8.1-0.7c1.2-4,2.2-7.3,3.4-10.7
                        c13.8-39.2,27.8-78.4,41.5-117.7c3.6-10.4,6.3-21,9.4-31.5C1706.5,1235.5,1707.4,1233.8,1708.3,1231.4z"/>
                    <path class="st0" d="M1788.4,1021c3.7,14.4,7.1,27.6,8,41.1c0.2,3.5-1.8,8-4.3,10.7c-19,21-36.7,43.1-52.7,66.6
                        c-0.5,0.7-1.5,1-2.3,1.5C1732.3,1115.5,1762.5,1043.8,1788.4,1021z"/>
                    <path class="st0" d="M1631,1556.4c-3.4,6.7-7,13.5-10.4,20.3c-8.7,17.2-17.1,34.6-26.1,51.7c-3.1,5.8-7,11.4-11.5,16.2
                        c-5.2,5.6-12,3.3-12.7-4.4c-0.4-4.3,1-9.3,3-13.3c8.1-16.8,16.8-33.3,25.2-49.9c5.3-10.4,10.6-20.7,15.7-31.2
                        c2.9-5.9,2.7-6-4.2-11.6c-2,3.1-4,6-5.8,9.1c-11,19-21.9,38.1-32.9,57.1c-1.5,2.6-2.7,5.6-4.9,7.5c-1.6,1.4-5.1,2.6-6.6,1.8
                        c-1.7-0.9-2.8-4.3-2.8-6.5c0-2.5,1.4-5.1,2.6-7.5c12.9-25.5,25.9-51.1,38.8-76.6c1.2-2.4,2.5-4.7,3.7-7.1c2.1-4.5,1.3-8.3-2.5-11.6
                        c-3.5-3-6.8-2.1-10,0.2c-13.8,9.9-30.1,12.1-46,15.4c-10.4,2.1-18.4-0.9-23.2-12.2c2.9-1.7,5.8-3.9,9-5.2
                        c21.5-8.9,38.7-23.2,52.6-41.7c8.6-11.5,18.5-21.9,30.2-30.4c23.3-17,48.6-20.9,75.6-10.6c8.7,3.3,16.9,7.8,25.4,11.6
                        c4.8,2.2,6.9,5.9,6.8,11.1c-0.2,18.3-0.3,36.7-0.6,55c-0.1,2.9-0.8,6-1.7,8.8c-6.6,19.6-14.4,38.6-25.3,56.3
                        c-11.5,18.7-22.2,37.9-33.3,56.9c-1.2,2-2.5,4-4,5.7c-2.4,2.7-2.5,2.7-9.4,0.9c2.3-5.2,4.5-10.3,6.8-15.3c2.8-6,5.9-12,8.7-18
                        c4.4-9.5,4.1-10.3-6.3-17.1c-1.7,3.2-3.6,6.2-5.1,9.3c-7.9,16.9-15.4,33.9-23.7,50.7c-2.4,4.9-6.1,9.5-10.2,13.1
                        c-2.5,2.3-6.9,2.4-10.4,3.5c-3.8-6.8-1.1-11.7,1.1-16.4c9.4-19.9,18.9-39.7,28.4-59.5c1-2.1,2.1-4.2,3-6.3
                        C1640.3,1559.9,1639.6,1559,1631,1556.4z"/>
                    <path id="side_B_11" class="st0" d="M2230.9,1380.6c-0.6-5.1-1.1-7.5-1.1-10c0.3-90,0.6-180,1-270c0.1-28.7,0.2-57.3,1.1-86
                        c0.6-18.1,5.9-35.4,12.8-52.1c3.4-8.3,6.5-8.8,13.4-3c0.8,0.6,1.5,1.3,2.2,2.1c20.4,23.3,41.8,45.9,60.9,70.2
                        c22.4,28.4,33,62,36.9,97.8c0.5,5,1,9.9,1.5,14.9c0.4,4.6-1.5,7.8-5.9,9.2c-17.4,5.6-27.6,18.9-36.6,33.7
                        c-12.2,20.1-19.4,42.3-27,64.3c-7.3,21.4-14.5,42.9-22.3,64.1c-7.1,19.5-17.1,37.5-29,54.6C2237,1373.4,2234.7,1376,2230.9,1380.6z"
                        />
                    <path id="side_B_6" class="st0" d="M2560.6,792.1c-33-1.8-60.8-12.8-83.6-35.5c-12.5-12.5-24.6-25.4-37.6-37.3c-11.5-10.6-23.7-20.4-36.1-29.9
                        c-9.9-7.6-12-11.8-8.3-23.8c6.4-21.1,19.2-37,38.7-47.5c9.6-5.2,19.4-6.1,29.8-2.3c24.6,8.9,47.4,21,68.2,37
                        c29.8,22.9,46.3,53.5,52.2,90.2c2,12.2,3.3,24.4,4.6,36.7c0.9,8-0.4,9.5-8.2,10.5C2573.3,790.9,2566.4,791.5,2560.6,792.1z"/>
                    
                    <!-- FRONT -->
                    <path id="side_F_6" class="st0" d="M737.9,753.6c-0.1,15,1.1,31.6-3.1,48c-10.2,39.5-38.3,62-80.9,64.4c-21.4,1.2-41.6-3.4-61-12.2
                        c-25.7-11.7-48-28.4-68.9-47.2c-6.5-5.9-8.1-12.8-4.3-20.7c9.9-20.3,20.2-40.4,30-60.8c8.4-17.4,16.5-34.8,24.2-52.5
                        c4.7-10.7,11.2-19.5,20.7-26.3c10.5-7.5,21.9-9.5,34.1-5.4c23,7.8,45.8,15.9,68.7,24.1c22.5,8.1,35.4,24.1,37.8,48
                        C736.3,726.1,736.9,739.1,737.9,753.6z"/>
                    <path class="st0" d="M172.7,1650.9c1.1-4.5,1.4-8.5,3.1-11.8c8.9-18.3,18-36.4,27.1-54.5c3-5.9,6.4-11.7,9.3-17.7
                        c3.4-6.9,3.1-7.8-4.3-14.8c-2.1,3.5-4.3,6.7-6.1,10.1c-12.3,22.9-24.5,45.8-36.8,68.6c-2.4,4.4-4.9,8.7-7.9,12.7
                        c-4.7,6.3-9,7.8-13.4,5.2c-4.6-2.6-5.4-6.8-2.1-14.1c4.4-9.7,9-19.3,14.1-28.7c12.4-22.8,25.2-45.4,37.8-68.1
                        c4.2-7.6,4.1-7.7-3.3-13.2c-2.1,3.3-4.4,6.5-6.4,9.8c-12.5,20.9-24.9,41.8-37.3,62.6c-2,3.4-4.2,6.8-6.6,10c-2.5,3.2-5.5,6.8-10,4.4
                        c-3.9-2-3.4-6.3-2.2-9.8c1.6-4.7,3.3-9.4,5.7-13.7c13-22.9,26.2-45.6,39.4-68.4c1.8-3.1,3.5-6.3,5.5-9.9c-5.8-6.6-12.8-8.3-20.7-6.7
                        c-9.8,2-19.5,4.5-29.2,6.5c-12.8,2.6-18.3-0.1-24.8-12.6c3.6-2,7-4.5,10.8-5.9c12.6-4.5,22.5-12.6,31.6-22
                        c17.2-17.6,34.6-35.1,52.3-52.3c3.4-3.3,8.1-6.2,12.6-7.4c13.3-3.5,23.2-11.3,30.9-22.2c18.7-26.4,37.4-52.7,56.1-79
                        c2.1-2.9,4.3-5.7,7-9.2c3.3,4.7,1.7,8.8,0.9,12.2c-9.2,38.8-10.4,78.5-14,117.9c-1.3,14.4,0.4,28.5,5.1,42.3
                        c7.5,22.1,4.2,42.8-10.1,61.3c-17.4,22.4-33.9,45.3-47,70.5c-3.3,6.4-8.1,12.2-12.8,17.8c-1.8,2.1-5.1,2.8-7.8,4.2
                        c-0.6-0.5-1.2-1.1-1.8-1.6c1.3-3.6,2.3-7.2,3.9-10.7c3.4-7.2,7.2-14.3,10.5-21.5c3.6-8,3.1-8.9-6.6-13.7c-1.7,3-3.5,6.1-5.1,9.2
                        c-8.9,17.9-17.7,35.8-26.6,53.6C188.3,1650.5,184.8,1652.4,172.7,1650.9z"/>
                    <path class="st0" d="M371.8,1116.1c-1.3-19.2-2.8-38.5-3.8-57.7c-0.8-17-5.3-32.7-15.2-46.7c-11.4-16-15.7-34.3-15.9-53.7
                        c-0.4-40.5,12.7-76.5,37.3-108.3c10.2-13.2,23.3-22.8,39.1-28.4c16.3-5.8,32.8-11.1,49.2-16.5c1.3-0.4,2.6-0.7,3.9-0.9
                        c10.4-1.6,15.5,2.3,15.2,12.8c-0.2,10.3-0.7,20.7-2.2,30.9c-9.2,61.9-25.5,121.9-51.8,178.6c-12,25.8-28.2,49.7-42.6,74.4
                        c-3.3,5.6-7.1,11-10.7,16.5C373.5,1116.6,372.7,1116.4,371.8,1116.1z"/>
                    <path id="side_F_5" class="st0" d="M386.1,822.8c-1-2.4-1.6-3.3-1.7-4.1c-1.2-29.7-0.8-59.3,5.1-88.5c1.4-6.8,3.3-13.7,6.5-19.8
                        c23.1-45.2,58-75.5,109.2-84.6c5.6-1,11.1-2.1,16.6-3.2c9-1.8,17.2-0.3,24.6,5.2c4.8,3.6,9.6,7.2,14.4,10.8c6,4.5,8.3,10.4,5.8,17.6
                        c-4.2,12.2-7.4,25-13.3,36.5c-12.8,25.2-26.9,49.7-40.6,74.4c-3.9,7-10,11.6-17.6,14c-14.6,4.6-29.1,9.4-43.8,13.4
                        c-19.1,5.2-37.2,12.5-54.3,22.3C393.8,818.5,390.3,820.4,386.1,822.8z"/>
                    <path id="side_F_9" class="st0" d="M546.6,1098.4c-5-3.2-5.9-8.6-6.5-13.7c-7.3-62.2-13-124.5-9.7-187.2c0.7-13.3,2-26.5,3.3-39.8
                        c1.2-12.1,3.3-13.5,14.4-9.4c17.8,6.6,35.4,13.4,53.2,20c9,3.4,18.1,6.5,27.2,9.7c12.1,4.1,13.5,6.3,11,19.1
                        c-3.2,17-6.6,33.9-9.9,50.9c-2.3,11.4-4.8,22.8-6.9,34.2c-3.2,17.1-10.9,32-22.5,44.9c-6.7,7.4-13.6,14.7-20.5,21.8
                        c-12.5,12.7-22.8,26.9-28.9,43.8c-0.6,1.5-1.3,3-2.1,4.4C548.2,1097.6,547.4,1097.8,546.6,1098.4z"/>
                    <path class="st0" d="M306.8,1428.6c0-11.3-0.6-21.5,0.1-31.6c4.2-56.1,16.4-110.6,33.8-163.9c9.2-28.1,19.8-55.8,30.1-83.6
                        c7.8-21.1,19.1-40.3,33.5-57.6c10-12,19.8-24.2,29.9-36.3c2.1-2.5,4.6-4.5,8.2-7.9c2.1,4.3,4.3,7.3,4.9,10.5
                        c2.2,12.3,2.2,24.5-3.4,36.2c-9.2,19-14.4,39.3-19.7,59.6c-3,11.6-6.3,23.1-10.2,34.4c-27.5,80.5-62.9,157.6-101.3,233.4
                        c-0.9,1.8-1.9,3.5-3,5.2C309.5,1427.5,308.7,1427.7,306.8,1428.6z"/>
                    <path class="st0" d="M230,1375.1c1.5-3.6,2.9-7.2,4.5-10.8c22.2-48.1,36.2-98.5,43.1-150.9c2.8-21.4,4.5-43.1,8.7-64.2
                        c7.8-39.7,24.2-75.9,48.1-108.6c2.7-3.7,5.4-9.2,10.5-7.3c3.3,1.2,6,5.9,7.5,9.6c6.5,15.7,8,32,6.6,48.9
                        c-6.8,84.8-38.2,160.5-85.7,230.2c-12.4,18.1-26.3,35.2-39.6,52.7c-0.5,0.7-1.6,1-2.4,1.5C230.9,1375.8,230.4,1375.5,230,1375.1z"/>
                    <path id="side_F_3" class="st0" d="M551.5,614.4c3.5-1.9,6.2-3.6,9.2-5c26.9-12.4,53.8-24.7,80.7-37c1.8-0.8,3.7-1.6,5.5-2.3
                        c3.6-1.3,6.7-0.7,8.5,2.9c13.3,26.3,34.5,46.3,54.1,67.5c1.7,1.8,2.9,4.1,5.7,8c-7.3-1.4-12.4-2.1-17.5-3.4
                        c-22.5-5.8-45-11.8-67.5-17.8c-14.3-3.9-28.3-3.8-41.8,2.8c-6.7,3.3-12.3,2.8-18.2-2.5C564.9,622.8,558.4,619.2,551.5,614.4z"/>
                    <path class="st0" d="M459.5,799.5c-15.9,5.5-31.8,10.9-48.6,16.7C419.9,808.7,444.9,799.8,459.5,799.5z"/>
                    <path class="st0" d="M548.5,717.6c-4.8,9.1-9.7,18.3-14.5,27.4c-0.6-0.3-1.2-0.6-1.8-0.9c4.7-9.3,9.3-18.5,14-27.8
                        C547,716.7,547.7,717.2,548.5,717.6z"/>
                    <path class="st0" d="M470.1,920.9c-1.4,5.1-2.8,10.3-4.2,15.4C467.3,931.1,468.7,926,470.1,920.9z"/>
                    <path class="st0" d="M466.1,935.8c-1.4,4.8-2.8,9.7-4.2,14.5C463.3,945.4,464.7,940.6,466.1,935.8z"/>
                    <path class="st0" d="M566,676.1c-0.7,2.8-1.4,5.6-2.1,8.4c-0.6-0.3-1.3-0.5-1.9-0.8c1.4-2.5,2.8-4.9,4.2-7.4
                        C566.2,676.2,566,676.1,566,676.1z"/>
                    <path class="st0" d="M251.5,1361.6c1.6-2.5,3.3-5.1,4.9-7.6c0.6,0.5,1.2,0.9,1.8,1.4c-2,2.3-4,4.6-5.9,7
                        C252.1,1362.1,251.8,1361.8,251.5,1361.6z"/>
                    <path class="st0" d="M569.8,665.8c-1.3,3.4-2.5,6.8-3.8,10.2c0,0,0.2,0.2,0.2,0.2c1.3-3.4,2.5-6.8,3.8-10.2
                        C570.1,666,569.8,665.8,569.8,665.8z"/>
                    <path class="st0" d="M1313.8,1525.3c-7.5,3.7-7.8,4.2-5.4,9.5c1.2,2.7,2.9,5.2,4.3,7.9c15.1,27.8,30.2,55.6,45.2,83.4
                        c1.9,3.5,3.3,7.3,4.8,10.9c1.8,4.4,1.8,8.5-2.6,11.5c-3.9,2.6-7.9,1.8-12.1-3.3c-3.2-3.8-5.9-8.1-8.3-12.4
                        c-12.7-23.4-25.3-46.9-37.9-70.3c-1.7-3.2-3.5-6.3-5.3-9.4c-8.1,4.5-8.9,6-6.1,12.4c1.7,4,4,7.7,6,11.5c10,19.6,20,39.1,29.9,58.7
                        c0.8,1.5,1.4,3,2.1,4.5c1.5,3.7,3.9,8.3-0.3,10.7c-2.3,1.3-7.3,0.1-10-1.7c-3.4-2.3-6.3-5.9-8.2-9.6c-8.7-16.8-17-33.9-25.5-50.9
                        c-1.9-3.8-4-7.6-6-11.2c-9.7,3.7-10.6,5.5-6.7,13.8c4.9,10.5,10.2,20.9,15.3,31.5c-4.4,4.6-7.5,2.1-10-1.2
                        c-5.3-7.2-11.1-14.3-15.3-22.2c-11.5-21.6-24.7-41.9-40.4-60.6c-18.3-21.8-23.1-46-13.4-72.7c5.1-13.9,4.9-28.2,3.6-42.5
                        c-2.8-30.5-6.2-61-9.7-91.4c-1-8.9-3.4-17.6-5-26.5c-0.4-2.2-0.5-4.4,0.9-7.6c2.4,2.9,5,5.7,7.2,8.8c15.8,22.3,31.4,44.7,47.2,67
                        c4.2,6,9.2,11.4,13.8,17.1c6.8,8.5,15,14.5,25.7,17.9c6.7,2.2,13.2,6.7,18.3,11.7c19,18.6,39,36.3,55.8,57.2c3.9,4.8,11,7,16.8,10.1
                        c5.7,3.1,11.7,5.8,17.3,8.5c-4,11.1-10.1,15.1-21.2,13.5c-9.5-1.4-18.9-3.9-28.3-6c-8.8-2-17.1-1.8-25.2,4.9c2,3.9,3.7,7.8,5.8,11.4
                        c13.1,22.8,26.3,45.6,39.3,68.5c2.4,4.3,4.6,9,5.6,13.7c0.6,2.7-0.8,6.9-2.8,8.7c-3,2.7-6.3-0.2-8.5-2.7c-2.4-2.7-4.4-5.8-6.3-9
                        c-12.8-21.4-25.6-42.9-38.3-64.4C1318,1531.8,1315.8,1528.6,1313.8,1525.3z"/>
                    <path class="st0" d="M1129.8,1116.7c-1.9-2.5-3.9-4.9-5.6-7.6c-9.5-14.8-18.8-29.9-28.5-44.6c-19.4-29.2-30.4-62-41.8-94.7
                        c-16-45.8-26.2-92.8-31.6-140.9c-0.6-4.9-0.8-10-0.2-14.9c1-9,5.9-12.7,14.5-10.1c19.1,5.7,38.3,11.4,56.8,18.6
                        c18.4,7.2,32.5,20.4,43.4,37c21.5,32.8,32.1,68.4,29.5,107.8c-1,15-5.2,28.9-13.8,41.2c-12.5,17.9-17.5,37.9-18.5,59.2
                        c-0.6,12.6-0.8,25.3-1.3,37.9c-0.1,3.5-0.7,7-1.1,10.5C1131,1116.3,1130.4,1116.5,1129.8,1116.7z"/>
                    <path id="side_F_8" class="st0" d="M1118.4,822.5c-3.2-1.5-5.6-2.4-7.8-3.8c-20.4-12.4-42.6-20-65.5-26.3c-13.4-3.7-26.7-8.1-39.7-13.1
                        c-4.7-1.8-9.7-5.3-12.3-9.5c-19.9-31.9-39.4-64.1-52.2-99.7c-1.7-4.7-3.3-9.4-4.4-14.3c-1.5-6.3,0-11.7,4.9-16.3
                        c13.9-12.9,29-19.4,49-15.9c47,8.2,83.4,31.5,109.3,71.5c7.3,11.3,12.2,23.4,14.8,36.7c5.5,28.7,5.6,57.6,5.1,86.6
                        C1119.5,819.4,1119,820.3,1118.4,822.5z"/>
                    <path class="st0" d="M1195.8,1429.7c-0.9-1.3-1.9-2.5-2.6-3.9c-18.2-37.4-37.9-74.1-54.3-112.3c-19.4-45.2-36.3-91.5-53.6-137.6
                        c-5.5-14.6-8.2-30.1-12.8-45.1c-3.5-11.4-7.7-22.7-12.1-33.8c-4.8-12.1-6.1-24.5-4.3-37.2c0.4-2.6,0.9-5.5,2.4-7.5
                        c1-1.3,3.9-1.9,5.8-1.7c1.3,0.1,2.7,1.8,3.8,3c26.3,29.8,51.7,60.5,66,98.1c23.2,61,44.6,122.6,55.2,187.3
                        c4.4,26.6,6.1,53.5,8.9,80.3c0.3,3.2,0,6.4,0,9.6C1197.3,1429.3,1196.6,1429.5,1195.8,1429.7z"/>
                    <path class="st0" d="M1158.2,1032.4c6.6,1.2,9.2,5.6,12.1,9.6c23.8,33.2,40,69.9,47.6,110c4.9,25.8,8.3,51.9,11.6,78
                        c5.8,47,20,91.5,39.7,134.4c1.3,2.9,2.3,5.9,1.9,9.7c-1.8-1.8-3.8-3.4-5.4-5.3c-52.4-62.8-90.7-132.8-110.7-212.4
                        c-6.9-27.2-10.4-54.9-10-83.1c0.2-11.5,1.6-22.6,7.3-32.8C1153.8,1037.5,1156.3,1034.9,1158.2,1032.4z"/>
                    <path id="side_F_4" class="st0" d="M949,614.3c-8.4,6.2-15.7,11.9-23.4,17.1c-1.9,1.3-5,1.3-7.5,1c-2.6-0.3-5-1.7-7.4-2.8
                        c-12.6-5.9-25.5-6.2-38.9-2.5c-22.7,6.2-45.6,12-68.4,17.9c-5,1.3-10,2.1-15,3.2c-0.4-0.7-0.8-1.3-1.2-2c1.9-2.5,3.6-5.2,5.7-7.5
                        c7.4-8.1,15.4-15.7,22.2-24.3c9.9-12.5,19-25.6,28.3-38.5c5.1-7,6.2-7.7,13.9-4.2c29.1,13.1,58,26.4,87,39.7
                        C945.6,611.9,946.6,612.8,949,614.3z"/>
                    <path class="st0" d="M1035.1,922.1c-0.8-0.5-2.2-0.9-2.4-1.6c-3-13.2-5.9-26.4-8.8-39.6C1027.7,894.6,1031.4,908.4,1035.1,922.1z"/>
                    <path id="side_F_" class="st0" d="M1074,807.9c-6-1.3-12.1-2.6-20.4-4.3c9.1-1.4,12.7-0.4,20.2,4.5C1073.9,808.1,1074,807.9,1074,807.9z"/>
                    <path id="side_F_" class="st0" d="M1084,812c4.2,1.3,8.4,2.7,12.6,4c-4.9,0.5-9.2-0.5-12.5-4.2L1084,812z"/>
                    <path class="st0" d="M1045.1,958.1c-4.6-3.7-4.7-9.1-5.2-14.3C1041.6,948.6,1043.4,953.4,1045.1,958.1z"/>
                    <path class="st0" d="M1040.1,944.2c-1.4-5.1-2.8-10.2-4.2-15.4C1037.3,934,1038.7,939.1,1040.1,944.2z"/>
                    <path class="st0" d="M1050.1,978.2c-1.4-4.8-2.8-9.6-4.2-14.4C1047.4,968.7,1048.7,973.5,1050.1,978.2z"/>
                    <path class="st0" d="M1084.2,811.8c-3.4-1.3-6.8-2.6-10.1-3.9c0,0-0.2,0.2-0.2,0.2c3.4,1.3,6.8,2.6,10.1,3.9
                        C1084,812,1084.2,811.8,1084.2,811.8z"/>
                    <path class="st0" d="M1249.6,1360.4c-1.9-2.3-3.9-4.5-5.8-6.8c0.6-0.4,1.2-0.9,1.8-1.3c1.5,2.5,3,5,4.5,7.5L1249.6,1360.4z"/>
                    <path id="side_F_16" class="st0" d="M746.4,1451.7c-18.4-4.1-32.4-12.8-42.9-27.1c-15.4-20.9-25.5-44.4-33.8-68.7c-12.7-37.4-20-75.9-23.9-115.1
                        c-0.9-8.9-1.9-17.9-2.1-26.9c-0.2-11.7,3.5-15,15.1-14.2c5.3,0.4,10.5,1.5,15.7,2.8c15.2,3.8,30.3,7.9,45.4,11.6
                        c12,3,18.6,10.1,20.3,22.7c4.3,31.8,6,63.6,6.4,95.6c0.4,34,0.5,67.9,0.7,101.9C747.4,1440,746.8,1445.6,746.4,1451.7z"/>
                    <path id="side_F_17" class="st0" d="M756.9,1451.7c0-10.5-0.1-19.4,0-28.3c0.9-50,1.5-99.9,2.9-149.9c0.4-14.2,3.2-28.4,5.1-42.6
                        c0.9-6.9,4.9-11.5,11.4-13.9c20.6-7.7,41.5-14.5,63.4-17.5c1.3-0.2,2.6-0.5,4-0.5c10.9-0.5,15.8,3.3,15.9,14.2
                        c0.1,10.3-0.6,20.6-1.7,30.9c-5,47.6-15.1,93.9-33.9,138.1c-6.7,15.7-14.3,30.8-25.1,44.2C788.6,1439.4,775.7,1448.1,756.9,1451.7z"
                        />
                    <path id="side_F_18" class="st0" d="M816.3,1423.5c2.5-5.4,4.6-10.2,7.1-14.9c20.2-39.2,36.8-79.9,45.2-123.3c3.6-18.6,5.9-37.5,7.2-56.4
                        c1-14.2,5.8-24.5,18.6-31.7c18.7-10.5,32.4-26.4,43.7-44.4c2.6-4.2,5.3-8.3,8.9-14c1.9,5,3.7,8.5,4.6,12.1
                        c5.7,23.3,9.6,47,10.2,71.1c0.5,19.9-3.2,38.9-11.6,57.2c-25.2,55.2-62.1,100.7-111.3,136.1C832.5,1419.9,826.1,1424.7,816.3,1423.5
                        z"/>
                    <path id="side_F_15" class="st0" d="M687.8,1423.6c-7.4,2.3-12.4-1.1-17.4-4.4c-18.9-12.6-35.9-27.5-51.7-43.9c-25.9-26.8-46.7-57.2-62.6-90.9
                        c-12.7-26.9-17.5-55-12.4-84.5c2.9-16.7,6-33.4,9.2-50c0.5-2.8,1.7-5.6,3.2-10.5c3,3.8,5.1,6.1,6.7,8.7c13.6,22.2,30.6,41,53.8,53.6
                        c7.8,4.3,10.4,11.4,10.9,20c0.9,14.3,1.6,28.6,3.6,42.7c6,42.2,20,81.9,38.1,120.3c4.5,9.6,9.4,19.1,14.1,28.7
                        C684.9,1416.7,686.2,1420,687.8,1423.6z"/>
                    <path id="side_F_11_3" class="st0" d="M759.9,1145.1c0-15.6-1-31.4,0.3-46.9c1.7-20.2,4.6-21.5,23.3-22c27.2-0.8,53,4.4,76.8,18.1
                        c12.4,7.1,14.1,10.4,11.9,24.2c-2.7,17.2-7.9,33.6-15.7,49.3c-5.2,10.6-13.3,17.2-24.7,20c-15.8,3.9-31.6,8-47.4,12.1
                        c-1.9,0.5-3.9,1-5.8,1.4c-14.4,3.4-18.6,0.4-18.8-14.1c-0.2-14,0-28,0-42C759.7,1145.1,759.8,1145.1,759.9,1145.1z"/>
                    <path id="side_F_14" class="st0" d="M865.3,1192.1c1.8-3.1,2.7-5.1,4.1-6.8c17-20,23.4-43.6,24.4-69.2c0.9-23.7-1.4-47.2-6.8-70.3
                        c-0.4-1.6-0.7-3.3-0.9-4.9c-0.1-0.6,0.2-1.3,0.5-2.9c2,1,4.1,1.6,5.6,2.9c15.3,12.5,28.6,26.8,40,43c8.4,11.9,9.9,24.2,4.6,37.7
                        c-6.9,17.6-16.4,33.4-29.5,47c-10.1,10.5-22.1,18.1-36.1,22.4C869.9,1191.5,868.3,1191.6,865.3,1192.1z"/>
                    <path id="side_F_13" class="st0" d="M617.9,1039.1c-0.9,5.2-1.7,10.4-2.8,15.5c-5.7,28.6-7.7,57.3-1.8,86.2c3.4,16.3,9.9,31,20.7,43.7
                        c1.3,1.5,2.4,3.2,3.4,4.9c0.3,0.5,0,1.3,0,3.2c-3.9-0.9-7.8-1.3-11.3-2.7c-11-4.4-20.4-11.2-28.4-19.9
                        c-13.7-14.9-24.1-31.8-31.7-50.5c-5-12.3-4.2-24.4,5-34.6c13.6-15.1,27.6-29.8,41.5-44.6c0.8-0.9,2.1-1.4,3.2-2
                        C616.5,1038.5,617.2,1038.8,617.9,1039.1z"/>
                    <path id="side_F_23" class="st0" d="M968.8,2049.5c-2-2.6-4.1-5-5.9-7.7c-12.7-19.1-25.1-38.5-38.1-57.5c-22-32.3-34.1-68.3-40.8-106.4
                        c-8.5-47.9-13.8-96.2-14.8-144.9c-0.7-37.9,4.5-75.1,13.5-111.7c22.4-91.4,45-182.8,67.6-274.2c1-4,3.1-7.7,6.6-11.3
                        c0.3,1.7,0.7,3.4,0.8,5.1c1.5,42.9,2.5,85.9,4.7,128.8c1,20.2,4.4,40.4,6.4,60.6c3.3,33.5,6.3,67,9.2,100.5
                        c1.7,19.2,2.6,38.5,4.2,57.8c2.7,31.6,1,63.2-0.3,94.9c-2.9,68.5-5.7,137.1-8.5,205.7c-0.6,16-1,32-1.5,47.9
                        c-0.1,3.9-0.4,7.8-0.7,11.7C970.4,2049,969.6,2049.3,968.8,2049.5z"/>
                    <path id="side_F_22" class="st0" d="M824.6,1767.7c-0.9-3-2-5.9-2.8-9c-14.3-54.8-23.6-110.5-30.4-166.8c-4.2-34.5-6.2-68.9-6.1-103.6
                        c0-9.7,3.5-16.7,11.7-21.5c2.9-1.7,5.7-3.5,8.6-5c40.4-21.1,74-50.8,103.9-84.8c7.8-9,13.9-19.5,20.8-29.3c1.5-2.1,3-4.3,6.1-6.3
                        c0,2.4,0.3,4.8,0,7.1c-5.7,34.2-14.6,67.6-24.9,100.6c-31.9,101.6-59,204.4-81.7,308.5c-0.7,3.4-2.1,6.7-3.1,10
                        C826,1767.7,825.3,1767.7,824.6,1767.7z"/>
                    <path id="side_F_24" class="st0" d="M989.2,1894.2c0.5-11.9,0.9-23.7,1.5-35.6c2.1-42.2,5.2-84.4,6-126.7c0.5-29.2-1.4-58.5-3.5-87.7
                        c-2.3-33.2-5.9-66.3-9.1-99.4c-2.6-26.2-5.3-52.3-8.3-78.4c-4.7-40.5-4.1-81.1-3.3-121.7c0.3-12.3,0.9-24.6,1.4-36.9
                        c0.1-2.1,0.5-4.2,2.4-6.5c0.7,1.6,1.6,3.2,2,4.8c16.1,58,32.4,116,44,175.1c4.4,22.5,6.6,45.4,9.5,68.2c2.4,18.5,4.3,37,6.6,55.5
                        c2.7,21.6,2,43.2,0.5,64.8c-4.1,57.4-17.3,113-31.7,168.4c-3.6,13.8-7.3,27.6-11.1,41.4c-1.4,5.1-3.1,10.1-4.7,15.1
                        C990.7,1894.4,990,1894.3,989.2,1894.2z"/>
                    <path class="st0" d="M983,1862.4c0.4-2.6,0.9-5.2,1.1-7.8c0.9-17.3,1.6-34.6,3.7-51.9c0,4.9,0.2,9.8,0,14.7
                        c-0.6,12.3-1.2,24.6-2.1,36.9C985.4,1857,983.9,1859.7,983,1862.4C983,1862.4,983,1862.4,983,1862.4z"/>
                    <path class="st0" d="M983,1862.4c0,10.5,0,21.1,0,31.6C983,1883.5,983,1873,983,1862.4C983,1862.4,983,1862.4,983,1862.4z"/>
                    <path id="side_F_20" class="st0" d="M535.3,2049.6c-0.4-3.2-1.1-6.4-1.2-9.6c-1.4-50.6-2.6-101.3-4.3-151.9c-0.9-25.6-3.1-51.2-4.4-76.8
                        c-2.9-56.3-3.5-112.5,2.1-168.7c4.1-41.1,6.9-82.3,10.6-123.5c1.6-18.2,4.9-36.4,5.8-54.6c2-39.6,2.8-79.2,4.2-118.8
                        c0.1-4.2,0.6-8.4,0.9-12.6c0.8-0.1,1.6-0.3,2.3-0.4c1,3.2,2.1,6.3,2.9,9.5c22.6,91.4,45.4,182.8,67.8,274.2
                        c4.9,20,9.1,40.3,12.1,60.6c4.6,31.5,3.1,63.2,1.3,94.8c-2.4,44.3-8.7,88.1-18.8,131.3c-6.7,28.5-17.9,55-33.8,79.6
                        c-13.2,20.4-26.8,40.6-40.3,60.8c-1.4,2.2-3.2,4.1-4.8,6.1C536.8,2049.6,536.1,2049.6,535.3,2049.6z"/>
                    <path id="side_F_21" class="st0" d="M571.6,1342.5c1.4,1.7,2.9,3.4,4.1,5.2c31.6,50.4,75.9,87.2,127.3,115.6c13.5,7.4,18.6,17.3,18.5,32.2
                        c-0.5,78.4-13.3,155.1-30.4,231.3c-2.4,10.7-4.6,21.5-7,32.2c-0.7,3.2-1.6,6.4-4.4,9.9c-1.2-3.8-2.6-7.6-3.5-11.5
                        c-6.5-27.9-12.5-55.8-19.3-83.6c-19.6-80.5-42.4-160.1-66.3-239.5c-8.2-27-13.7-54.9-20.2-82.4c-0.7-2.8-0.5-5.9-0.7-8.8
                        C570.3,1342.9,571,1342.7,571.6,1342.5z"/>
                    <path id="side_F_19" class="st0" d="M531.8,1302.1c0.7,15.5,2.2,31.1,2.1,46.6c-0.2,35-0.2,70-2.1,104.8c-1.6,29.2-5.6,58.3-8.6,87.5
                        c-3,29.5-5.9,59-9,88.4c-4.8,44.5-6.2,89-4,133.7c1.4,27.9,3.2,55.9,4.8,83.8c0.9,16.9,1.7,33.9,1.4,51.3c-0.8-1.4-2-2.8-2.4-4.3
                        c-14.6-53.6-29.7-107.1-39.3-161.9c-7.9-44.8-11.7-89.8-6.4-135.2c2.7-23.5,6.5-46.8,8.8-70.4c6.4-65,25.6-127.1,42-189.8
                        c2.3-8.7,4.6-17.4,7.1-26c0.9-3.1,2-6.1,3-9.1C529.9,1301.7,530.8,1301.9,531.8,1302.1z"/>
                    <path class="st0" d="M522.8,1892.3c0-3.3,0-6.5,0-9.6c0.2,0,0.4,0,0.6,0c0,2.7,0,5.5,0,8.2c0,0.6-0.3,1.2-0.4,1.8
                        C522.9,1892.5,522.8,1892.3,522.8,1892.3z"/>
                    <path id="side_F_29" class="st0" d="M564.7,2046.2c-1,8.1-1.6,16.4-3.2,24.4c-7.9,41.1-9.9,82.7-7.9,124.4c2.2,46.6,5.3,93.2,7.3,139.8
                        c1.6,35.3,2.3,70.6,3.1,105.9c1.1,49.6,7.3,98.3,24.2,145.2c3.8,10.6,8.2,21.1,12.3,31.6c1.2,3,2.2,6.1,3.7,10.2
                        c-1.8-0.9-2.7-1.1-3.2-1.7c-35.2-39.4-64.4-82.7-78.4-134.2c-7.5-27.6-13.1-55.7-18.3-83.8c-4.4-24.2-7.4-48.7-10.3-73.2
                        c-3.8-31.6-2.3-63.3,0.3-94.8c3.5-42,11.7-83,28-122.2c9.1-21.9,20-42.9,33.1-62.6c2.2-3.3,4.6-6.4,6.8-9.6
                        C563,2045.7,563.8,2046,564.7,2046.2z"/>
                    <path id="side_F_30" class="st0" d="M630.1,2159.2c0.3,1.3,0.9,2.5,0.9,3.8c0.3,26.1,5.2,51.6,11.2,76.9c11.5,48.6,8.3,96.4-7.1,143.6
                        c-8.3,25.3-16.6,50.5-25.2,75.7c-12.6,36.7-15.6,74-7.2,112.1c3.1,14.3,5.8,28.7,8.6,43c0.6,3.2,0.9,6.4,1.5,11.8
                        c-6.3-6.7-8.8-13.1-10.5-19.5c-9-32.7-19.3-65.1-22.8-99c-1.3-12.6-2.2-25.3-1.7-37.9c1.6-35.6,2.9-71.2,6.7-106.6
                        c2.7-25.4,8.6-50.5,14-75.5c8.3-38.7,17.3-77.2,26-115.8c1-4.5,2.5-8.8,3.8-13.2C628.9,2158.7,629.5,2158.9,630.1,2159.2z"/>
                    <path id="side_F_27" class="st0" d="M612.4,1972.6c2.3,10.5,4.7,20.1,6.5,29.9c6.4,35.9,10.3,72,5,108.3c-2.9,20.4-7.8,40.5-12.3,60.6
                        c-9.7,43.2-19.8,86.3-29.9,129.4c-1.2,5.3-3,10.5-4.6,15.8c-0.7,0-1.5-0.1-2.2-0.1c-0.4-1.7-1-3.3-1.1-5
                        c-4.5-57.2-6.9-114.4-6.7-171.7c0.1-26.5,4-52.4,11.3-77.8c6.4-22.4,13-44.7,19.7-67C600.7,1986.7,603.4,1978.4,612.4,1972.6z"/>
                    <path class="st0" d="M612.4,2200.7c-1.7,8-3.4,16-5.2,24.1c-7.6,34.1-15.7,68.1-22.7,102.3c-8.2,39.9-10.7,80.5-11.3,121.2
                        c-0.1,8.9,0,17.8,0,26.7c-0.7,0.1-1.4,0.2-2.1,0.3c-0.4-3-1-6-1.1-9c-0.7-26.3-1-52.6-2-78.9c-1.3-35.3-3.3-70.6-4.9-105.8
                        c-1.2-26.3-2.4-52.6-3.4-78.9c-0.7-17.3-1.3-34.6-1.2-51.9c0-10.9,1.3-21.8,3.1-32.8c0.1,2.4,0.5,4.8,0.4,7.2
                        c-2.1,45,0.6,89.9,3.4,134.8c1.4,21.6,3,43.2,4.6,64.8c0.1,1,0.2,2,0.2,3c0.2,3.6,1,6.9,5.3,7.1c4,0.2,4.5-3.2,5.2-6.2
                        c3.8-16.2,7.6-32.4,11.3-48.6c6.2-26.5,12.3-53,18.5-79.5C611.1,2200.5,611.7,2200.6,612.4,2200.7z"/>
                    <path class="st0" d="M613.8,2187.4c0.9-4.4,1.9-8.8,2.8-13.2c0.5,0.1,1.1,0.2,1.6,0.3c-1,4.4-1.9,8.8-2.9,13.2
                        C614.8,2187.6,614.3,2187.5,613.8,2187.4z"/>
                    <path id="side_F_" class="st0" d="M562.9,2096.3c0.4,4.1,0.7,8.2,1.1,12.3c-0.6,0-1.2,0-1.7,0C562.5,2104.5,562.7,2100.4,562.9,2096.3
                        C562.9,2096.3,562.9,2096.3,562.9,2096.3z"/>
                    <path id="side_F_32" class="st0" d="M903.6,2624.3c1.1-3,2.1-6.1,3.4-9.1c17.4-39,27.6-80,31-122.4c3-37.8,4.1-75.8,5.9-113.7
                        c1.9-39.9,3.7-79.9,5.6-119.8c1.3-28,3.2-55.9,4.1-83.9c1.2-35.1-3-69.8-8.6-104.4c-1.3-8-2.2-16.1-3.3-24.1
                        c0.7-0.3,1.5-0.6,2.2-0.9c2.3,3,4.9,5.9,7,9.1c28.3,42.8,46.3,89.7,55.3,140.2c9.2,51.9,9.6,104,4.4,156.3
                        c-5.1,50.9-15.1,100.7-30,149.5c-10.1,33.2-26.1,63.3-47.3,90.7c-8.9,11.5-18.3,22.7-27.5,34C905,2625.3,904.3,2624.8,903.6,2624.3z
                        "/>
                    <path id="side_F_31" class="st0" d="M893.3,2625c1-13.8,3.7-27.4,7.3-40.7c4.8-17.4,7.4-35.1,7.5-53.2c0.2-23.6-3.9-46.4-11.2-68.7
                        c-8.6-26.3-17.4-52.4-26-78.7c-11.1-33.9-16.8-68.6-12.9-104.2c2-17.8,5.2-35.5,8.1-53.3c3.5-21.3,7.1-42.6,11.1-66.4
                        c1.5,3.8,2.5,5.8,3,7.9c9.9,44.1,20,88.2,29.6,132.4c4.8,22.4,9.4,44.9,12.7,67.6c2.3,16.1,2.3,32.5,3,48.8
                        c0.9,18.6,1.2,37.3,2.1,55.9c1.8,35.1-4.8,68.9-14.6,102.3c-4.5,15.3-10.1,30.2-15.2,45.3c-0.6,1.8-1.8,3.5-2.7,5.2
                        C894.6,2625.2,893.9,2625.1,893.3,2625z"/>
                    <path id="side_F_7" class="st0" d="M762.5,758.3c1.3-16.9,1.6-34,4.1-50.7c2.9-19.9,14.6-34.1,33.8-41.1c24.7-9,49.6-17.5,74.3-26.2
                        c8.7-3.1,17.1-1.9,25.1,2.3c11.9,6.3,21,15.2,26,27.8c12.4,31,28,60.3,43.7,89.7c5,9.4,8.5,19.5,12.7,29.2c2.4,5.6,1.3,10.4-3,14.5
                        c-28.6,27.2-59.8,49.9-99.1,59.1c-17.1,4-34.3,4.4-51.5,0.8c-35.7-7.4-63.2-39.8-64.9-76.5c-0.4-9.6-0.1-19.3-0.1-28.9
                        C763.3,758.3,762.9,758.3,762.5,758.3z"/>
                    <path id="side_F_12" class="st0" d="M957.2,1100.1c-1.1-1.5-2.2-2.4-2.6-3.5c-6.2-20.3-19.2-36.3-33.5-51.4c-3.9-4.1-7.9-8.1-11.9-12.1
                        c-15.1-15.4-24.5-33.4-28.2-55c-4.6-26.5-10.7-52.8-16.1-79.2c-0.4-2-0.8-3.9-1-5.9c-0.8-8.4,0.3-10.7,8.2-13.7
                        c29-10.8,58-21.5,87.1-32c7.2-2.6,9.7-1,10.5,6.9c4.7,48.7,9,97.5,3,146.5c-3.3,26.4-5.8,52.9-8.7,79.4
                        C963.2,1087.1,961.9,1093.9,957.2,1100.1z"/>
                    <path class="st0" d="M918.1,856.1c-2.4,1.3-4.7,2.7-7.1,4c-0.2-0.6-0.5-1.2-0.7-1.8c2.7-0.7,5.3-1.3,8-2
                        C918.3,856.3,918.1,856.1,918.1,856.1z"/>
                    <path id="side_F_" class="st0" d="M747.5,512.4c-12.2-0.3-24-2.5-34.9-7.9c-14.9-7.4-29.6-15.2-44.4-22.8c-6.4-3.3-10-8.6-10.7-15.8
                        c-1.2-12.6-2.4-25.2-3.6-37.8c-0.4-3.6-0.9-7.3-1.1-10.9c-0.3-5.2-3-7.9-8.2-8c-9.6-0.3-14.9-5.7-18.4-14.3c-4-9.9-8.9-19.3-13-29.2
                        c-4.9-11.6-2.6-17.7,8.2-24.1c0.9-0.5,1.7-1.2,2.6-1.5c12-3.3,14.4-11.9,14-23.1c-0.8-21.1,2.5-41.8,9.8-61.7
                        c17.2-47.1,64.6-76.8,113.6-72.3c51.6,4.7,93.9,45.1,100.6,94.4c1.9,13.5,2.2,27.2,2.4,40.8c0.2,8.8,3,15.3,10.9,19.7
                        c4.1,2.2,7.7,5.2,11.4,8.1c5.7,4.5,7.2,10.5,4.7,17c-4.5,11.8-9.4,23.4-14.5,35c-2.7,6.2-8,9.3-14.8,10.6c-9.7,1.8-9.8,2-11,12
                        c-1.3,11.6-2.6,23.2-3.2,34.8c-0.7,14.7-6.8,25-20.4,31.5c-21,10.1-42,20.2-65.3,23.8C757.4,511.5,752.4,511.9,747.5,512.4z"/>
                    <path id="side_F_1" class="st0" d="M671.8,501.9c9.5,4.5,17.6,8.3,25.7,12.1c8.2,3.8,14,10.1,17.8,18.2c13.4,28.1,23.6,57.2,25.6,88.6
                        c0.3,5.5-0.5,11.1-0.9,18.7c-4.7-2.4-7.9-3.6-10.7-5.5c-14.8-9.9-27.7-22-38.2-36.3c-7.9-10.7-14.9-22-21.8-33.4
                        c-2.2-3.5-3.8-8.1-3.7-12.1c0.3-13,1.3-25.9,2.5-38.8C668.4,509.8,670.3,506.5,671.8,501.9z"/>
                    <path id="side_F_2" class="st0" d="M764.3,637.8c-2.3-18.8-1.3-36.7,4.7-53.9c6.3-18.2,13.8-36,21.3-53.8c1.9-4.4,5.4-8.9,9.4-11.3
                        c8.4-5.2,17.5-9.5,26.6-13.5c5.1-2.2,7.6-0.8,8.8,4.7c3.3,15.3,6.8,30.9,2.5,46.4c-2.1,7.5-5.8,15-10.5,21.2
                        C816,592.2,803.9,606,792,620C783,630.6,775.8,635.1,764.3,637.8z"/>
                    <path id="side_F_" class="st0" d="M750.6,615.8c-3.3-15-6.1-30.1-10.2-44.8c-4.1-14.6-9.5-28.8-14.6-44c17.1,0,33.4,0,51.2,0
                        c-0.7,3.4-1,6.5-2.2,9.4c-9.3,22.8-16.3,46.2-19.4,70.8c-0.4,3-1.8,6-2.8,8.9C752,616,751.3,615.9,750.6,615.8z"/>
                    <path id="side_F_" class="st0" d="M964.7,2575.4c4.7,30.5,9.2,59.1,13.3,87.7c1.4,9.5,4.6,17.8,11.3,24.9c9.3,10,18,20.5,27.2,30.6
                        c12.8,14.1,27.5,26.1,43.4,36.6c16.9,11.1,31.1,25,43.1,41.2c1.8,2.4,3.6,4.8,5.1,7.4c6,10,5,13.4-5.6,18.5
                        c-18.9,9.2-38.2,17.1-59.6,18.4c-20.1,1.2-38.3-3.5-54.5-15.4c-4.8-3.5-9.7-7.1-14.3-10.9c-12.1-10.1-25.4-17.9-40.7-22.5
                        c-15-4.4-23.6-15.6-27.4-30.4c-6.1-23.6-8.2-47.8-10-72.1c-0.2-3.3-0.2-6.7-0.5-10c-1.1-12.7,3-23.6,11.3-33
                        c5.5-6.2,11.2-12.3,16.9-18.3c13.9-14.4,26.7-29.7,36.4-47.4C961,2579,962.5,2577.8,964.7,2575.4z"/>
                    <path id="side_F_" class="st0" d="M541.7,2575.3c2.4,3.1,3.7,4.5,4.7,6.2c11.2,19.9,26.1,36.9,42,53.1c4,4,8,8.1,11.5,12.5
                        c7,8.9,11.7,19.1,10.7,30.4c-2.2,25.5-1.3,51.3-8.6,76.2c-1.5,5.1-3.2,10.1-4.6,15.3c-2.8,10.6-9.8,17.6-19.8,20.7
                        c-20.1,6.3-37,17.6-53.2,30.7c-25.1,20.3-53.3,26.3-84.3,15.6c-12.3-4.2-24.4-8.7-36.5-13.5c-9.6-3.8-10.8-7.7-6.4-17
                        c1.6-3.3,3.5-6.5,5.8-9.3c13.2-16.5,27.8-31.3,45.7-42.8c17.8-11.4,33.2-25.6,46.9-41.6c6.9-8.1,13.8-16.3,21.2-23.9
                        c6.6-6.8,9.9-14.6,11.2-23.8c3.7-27.4,7.9-54.7,11.9-82C540.1,2580.5,540.7,2579,541.7,2575.3z"/>
                    <path id="side_F_26" class="st0" d="M849.7,1748.6c0.8,5.7,2.1,11.5,2.3,17.2c2.2,54.5,11.8,107.9,24.8,160.7c4.1,16.6,4.3,32.7-0.3,49.2
                        c-8,28.8-9.5,58.4-10.4,88.1c-0.2,7.2-0.6,14.5-2.8,22.2c-1.1-2-2.5-4-3.4-6.2c-13.7-33.1-21.6-67.8-26-103.3
                        c-5.1-40.8-6.8-81.6-4.1-122.7c2.3-34,7.4-67.5,15.9-100.5c0.4-1.7,1.4-3.3,2-5C848.5,1748.5,849.1,1748.5,849.7,1748.6z"/>
                    <path id="side_F_25" class="st0" d="M640.2,2090.6c-0.3-7.9-0.6-15.7-1-23.6c-1-18.9-1.5-37.9-3.3-56.8c-1.1-11.5-3.8-22.9-6.6-34.2
                        c-4-16.5-3.8-32.9-0.3-49.4c6.1-28.3,12.7-56.4,17.4-85c3.8-23.6,5.1-47.6,7.5-71.5c0.8-7.8,1.6-15.6,2.5-24.3
                        c6.8,8.4,16.8,65.6,19.6,102.6c6.3,83.3-3.7,164.3-32.8,242.6C642.3,2090.9,641.2,2090.8,640.2,2090.6z"/>
                    <path id="side_F_28" class="st0" d="M929.5,2319.2c-0.6-1.7-1.4-3.3-1.8-5c-14-60-27.8-119.9-41.9-179.9c-5.2-22.2-7-44.7-5.8-67.5
                        c1.4-26.6,4.1-53.1,10.1-79.2c1.1-4.7,2.7-9.4,4.1-14c5.7,0.9,7.2,4.7,8.5,8.5c15.5,43.7,30.1,87.6,34.6,134
                        c1.8,18.2,2.5,36.6,1.8,54.9c-1.9,48.3-4.8,96.5-7.3,144.7c-0.1,1-0.4,1.9-0.6,2.9C930.7,2318.9,930.1,2319,929.5,2319.2z"/>
                    <path id="side_F_" class="st0" d="M1008.9,848.2c14.6,56.7,29.3,113.4,44.2,171.2c-12.7-8.9-32.6-48.2-41.7-81c-8.2-29.6-10-59.5-4.9-89.8
                        C1007.3,848.5,1008.1,848.3,1008.9,848.2z"/>
                    <path id="side_F_" class="st0" d="M449.5,1022.9c15.1-60.1,30.1-120.2,45.2-180.2c0.8,0,1.7,0,2.5,0c0.9,5.3,2.5,10.6,2.7,16
                        c2.6,52.2-9.7,101-35,146.6c-2.7,4.9-6.2,9.5-9.5,14.1c-1.1,1.6-2.6,2.9-3.9,4.4C450.9,1023.4,450.2,1023.2,449.5,1022.9z"/>
                    <path id="side_F_10_3" class="st0" d="M744,1155.9c0,8.7,0,17.3,0,26c0,3.3,0,6.7-0.4,10c-1,9.3-4.1,11.7-13.1,10.4c-1.3-0.2-2.6-0.4-3.9-0.7
                        c-19.6-5.1-39.3-10.3-58.9-15.3c-7.6-1.9-13-6.4-16.9-13c-10.7-18.4-16.8-38.3-19.5-59.3c-1-7.9,2.2-13.5,8.9-17.5
                        c17.4-10.4,35.9-17.6,56.3-18.5c10.6-0.5,21.3-0.7,32-0.5c10.5,0.2,11.9,1.8,13.5,11.9c2.9,18.1,5.3,36.2,3.1,54.6
                        c-0.5,4-0.4,8-0.6,12C744.3,1155.9,744.1,1155.9,744,1155.9z"/>
                    <path id="side_F_11_2" class="st0" d="M872.9,1080.8c-4.1-1.8-6.9-2.7-9.4-4.1c-20.4-11.9-42.7-16.2-66.1-16.3c-9.3,0-18.6-0.1-28-0.4
                        c-8.6-0.3-10-1.4-10.1-10c-0.2-19.3-0.3-38.6,0.1-57.9c0.2-11.4,2.9-13.9,14.4-14.5c31.5-1.9,58.9,8,82.6,28.7
                        c7,6.2,10.8,13.9,11.7,23.4c1.1,11.9,3.2,23.7,4.6,35.6C873.3,1069.9,872.9,1074.5,872.9,1080.8z"/>
                    <path id="side_F_11_1" class="st0" d="M865.4,987.4c-4.7-2.1-7.7-3.2-10.5-4.7c-23.5-12.6-48.7-19.4-74.9-22.7c-20.7-2.6-20.8-2.5-20.8-22.9
                        c0-1,0-2,0-3c0-33.4,0-33.4,19.3-60.5c4.8-6.8,6.7-7.7,14.1-4.6c13.2,5.4,26.3,11.1,39.4,16.8c8.2,3.6,13.3,10,15.3,18.6
                        c5.8,23.9,11.4,47.8,16.9,71.8C865.1,979.1,865,982.3,865.4,987.4z"/>
                    <path id="side_F_10_2" class="st0" d="M630.1,1081.1c0-7.7-0.5-13.6,0.1-19.4c1-9.9,2.4-19.8,4-29.7c2.1-12.7,8.4-23.1,18.8-30.7
                        c23.3-17.3,49.4-25.7,78.5-23.4c8,0.6,11.1,2.7,11.9,9.5c2.5,21.8,4.6,43.7,0.7,65.6c-0.3,1.9-1.1,3.7-1.8,6.3
                        c-4.1,0.3-8,0.6-11.9,0.7c-15.6,0.7-31.3,1.1-46.9,2.2c-14.8,1.1-28.5,5.9-41.4,13.4C638.9,1077.5,635.2,1078.8,630.1,1081.1z"/>
                    <path id="side_F_10_1" class="st0" d="M637.6,988.1c0.6-5.5,0.7-9.2,1.4-12.6c4.4-19.8,9.1-39.5,13.3-59.3c3.9-18,13.8-30.5,31.4-36.9
                        c9.1-3.3,17.9-7.1,26.9-10.4c7.5-2.7,9.8-1.9,14.3,4.5c2.9,4.1,5.2,8.5,8.3,12.4c9.5,12.1,13.5,25.7,12.4,40.9
                        c-0.4,6.3-0.1,12.6-0.5,18.9c-0.5,8.9-2.3,10.8-11.1,12.5c-8.8,1.7-17.7,2.9-26.6,4.4c-20.5,3.4-39.9,10-58.3,19.7
                        C646.1,983.9,642.9,985.4,637.6,988.1z"/>
                    <path class="st0" d="M750.2,814.9c2.9,2.5,5.2,3.8,6.7,5.8c4.7,6.4,9,13.1,13.5,19.7c3.4,4.9,3.5,9.7,0,14.7
                        c-5.7,8.1-11.1,16.4-16.8,24.8c-8.1-4-11.1-11.3-15.3-17.5c-10.2-14.8-10.1-15-0.5-29.9c2.9-4.4,6.1-8.6,9.2-13
                        C748,818.2,748.9,816.9,750.2,814.9z"/>
                    <path class="st0" d="M752.2,695.7c-6.7-12-12.4-22.2-18.6-33.2c12.1,0,23,0,35.7,0C763.5,673.7,758.3,683.8,752.2,695.7z"/>
                </svg>
            </div>
        </section>
        <div class="btn">
            <button>Close</button>
        </div>
    </div>

    <!-- 신체정보 직접입력 모달 -->
    <form action="#" method="POST" name="bodyInfo_Write" id="bodyInfo_Write">
        <h2>신체정보 직접입력</h2>
        <div class="con">
            <p>
                <label for="bodyInfo_Date">날짜 선택</label>
                <input type="date" name="bodyInfo_Date" id="bodyInfo_Date">
            </p>
            <fieldset>
                <legend>신체정보</legend>
                <p>
                    <label for="bodyInfo_Height">신장</label>
                    <input type="text" name="bodyInfo_Height" id="bodyInfo_Height" placeholder="직접입력">
                    <small>
                        <span>최근 데이터 : </span>
                        <span class="data writeHEIGHT">180 Cm</span>
                    </small>
                </p>
                <p>
                    <label for="bodyInfo_Weight">체중</label>
                    <input type="text" name="bodyInfo_Weight" id="bodyInfo_Weight" placeholder="직접입력">
                    <small>
                        <span>최근 데이터 : </span>
                        <span class="data writeWEIGHT">70 Kg</span>
                    </small>
                </p>
                <p>
                    <label for="bodyInfo_Fat">체지방량</label>
                    <input type="text" name="bodyInfo_Fat" id="bodyInfo_Fat" placeholder="직접입력">
                    <small>
                        <span>최근 데이터 : </span>
                        <span class="data writeFAT">23 Kg</span>
                    </small>
                </p>
                <p>
                    <label for="bodyInfo_Muscle">근육량</label>
                    <input type="text" name="bodyInfo_Muscle" id="bodyInfo_Muscle" placeholder="직접입력">
                    <small>
                        <span>최근 데이터 : </span>
                        <span class="data writeMUSCLE">20 Kg</span>
                    </small>
                </p>

            </fieldset>
        </div>
        <div class="btn">
            <button class="close" type="button">닫 기</button>
            <button id="bodyInfo_Submit" class="save">저 장</button>
        </div>
    </form>
    
    <!-- 대사질환정보 직접입력 모달 -->
    <form action="#" method="POST" name="desaInfo_Write" id="desaInfo_Write">
        <h2>대사질환정보 직접입력</h2>
        <div class="con">
            <p>
                <label for="desaInfo_Date">날짜 선택</label>
                <input type="date" name="desaInfo_Date" id="desaInfo_Date">
            </p>
            <fieldset>
                <legend>대사질환정보</legend>
                <p>
                    <label for="desaInfo_HR">안정시 심박수</label>
                    <input type="text" name="desaInfo_HR" id="desaInfo_HR" placeholder="HR">
                    <small>
                        <span>최근 데이터 : </span>
                        <span class="data lastDesa_HR">67 HR</span>
                    </small>
                </p>
                <p>
                    <label for="desaInfo_SBP">혈압</label>
                    <input type="text" name="desaInfo_SBP" id="desaInfo_SBP" placeholder="SBP">
                    / <input type="text" name="desaInfo_DBP" id="desaInfo_DBP" placeholder="DBP">
                    <small>
                        <span>최근 데이터 : </span>
                        <span class="data lastDesa_SBP_DBP">128 SBP / 87 DBP</span>
                    </small>
                </p>
                <p>
                    <label for="desaInfo_Glucose">혈당</label>
                    <input type="text" name="desaInfo_Glucose" id="desaInfo_Glucose" placeholder="Glucose">
                    <small>
                        <span>최근 데이터 : </span>
                        <span class="data lastDesa_Glucose">98 mg/dl</span>
                    </small>
                </p>
                <p>
                    <label for="desaInfo_HbA1c">당화혈색소</label>
                    <input type="text" name="desaInfo_HbA1c" id="desaInfo_HbA1c" placeholder="HbA1c">
                    <small>
                        <span>최근 데이터 : </span>
                        <span class="data lastDesa_HbA1c">3.8 %</span>
                    </small>
                </p>
                <p>
                    <label for="desaInfo_TC">총콜레스테롤</label>
                    <input type="text" name="desaInfo_TC" id="desaInfo_TC" placeholder="TC">
                    <small>
                        <span>최근 데이터 : </span>
                        <span class="data lastDesa_TC">165 TC</span>
                    </small>
                </p>
                <p>
                    <label for="desaInfo_HDL">콜레스테롤</label>
                    <input type="text" name="desaInfo_HDL" id="desaInfo_HDL" placeholder="HDL">
                    / <input type="text" name="desaInfo_LDL" id="desaInfo_LDL" placeholder="LDL">
                    <small>
                        <span>최근 데이터 : </span>
                        <span class="data lastDesa_HDL_LDL">45 HDL / 136 LDL</span>
                    </small>
                </p>
                <p>
                    <label for="desaInfo_TG">중성지방</label>
                    <input type="text" name="desaInfo_TG" id="desaInfo_TG" placeholder="TG">
                    <small>
                        <span>최근 데이터 : </span>
                        <span class="data lastDesa_TG">93 TG</span>
                    </small>
                </p>
                <p>
                    <label for="desaInfo_Lactate">젖산</label>
                    <input type="text" name="desaInfo_Lactate" id="desaInfo_Lactate" placeholder="Lactate">
                    <small>
                        <span>최근 데이터 : </span>
                        <span class="data lastDesa_Lactate">0.6 Lactate</span>
                    </small>
                </p>

            </fieldset>
        </div>
        <div class="btn">
            <button class="close" type="button">닫 기</button>
            <button id="desaInfo_Submit" class="save">저 장</button>
        </div>
    </form>
    
    <?php require_once 'lib/footer.php'; ?> 

</body>
</html>