<!DOCTYPE html>
<?php
require_once 'lib/_init.php';

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
    <title>닥터케어유니온 - Dr. Care Union</title>
    <script src="js/jquery-3.5.1.min.js"></script>
    <script src="js/Array.js"></script>
    <script src="js/jswLib.js"></script>
    <script src="js/script.js"></script>
    <script src="js/dashboard.js"></script>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/calendar.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css">
    <script src="https://cdn.bootcss.com/jquery/1.9.0/jquery.min.js"></script>
    <script src="js/calendar.js"></script>
    <script src="js/myCalendar.js"></script>
    <script src="js/calendar.js"></script>
</head>
<body>
    
    <div id="wrap">
<?php require_once 'lib/header.php'; ?>
        <section class="content">
            <h2 class="hid">메인영역</h2>

            <article class="dash_board_content">
                <h3 class="hid">대시보드</h3>
                
                <!-- 달력 -->
                <div class="box4 dash_board">
                    <i class="fas fa-question-circle"></i>
                    <div class="cal-info">날짜를 클릭하면 금일데이터를<br>보실 수 있습니다</div>
                    <div class="left">
                        <h4>달 력</h4>
                        <p id="date">1900.01.01 (월)</p>
                        <p id="time">12 : 00 : 00</p>
                        <div>
                            <h5>★공지사항★</h5>
                            <ol>
                                <!-- js -->
                            </ol>
                        </div>
                    </div>
                    <div class="right">
                        <div id="demo">
                            <div id="one"></div>
                        </div>
                    </div>
                </div>

                <!-- 금일 스케줄 달성률 -->
                <div class="box1 dash_board">
                    <h4>금일 스케줄 출석률</h4>
                    <div class="chart_all">
                        <table>
                            <tbody>
                                <tr class="total">
                                    <th>전체레슨</th>
                                    <td>
                                        <div class="progressBarWrap">
                                            <div class="progressBar all">
                                                <span class="percent">0 %</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="count">0 / 0</td>
                                </tr>
                                <tr>
                                    <th>개인레슨</th>
                                    <td>
                                        <div class="progressBarWrap">
                                            <div class="progressBar solo">
                                                <span class="percent">0 %</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="count">0 / 0</td>
                                </tr>
                                <tr>
                                    <th>그룹레슨</th>
                                    <td>
                                        <div class="progressBarWrap">
                                            <div class="progressBar group">
                                                <span class="percent">0 %</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="count">0 / 0</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 금일 체크인 -->
                <div class="box7 dash_board">
                    <h4>금일 체크인</h4>
                    <div class="progressBarTle">
                        <div class="progressBar"></div>
                        <p>0%</p>
                    </div>
                    <div>
                        <p>
                            <span>현재 진행</span>
                            <span class="data">0명</span>
                        </p>
                        <p>
                            <span>오늘 완료</span>
                            <span class="data">0명</span>
                        </p>
                        <p>
                            <span>Total</span>
                            <span class="data"">0명</span>
                        </p>
                    </div>
                </div>

                <!-- 금월 매출 -->
                <div class="box3 dash_board">
                    <h4>금월 매출</h4>
                    <div class="left">
                        <div class="pie">
                            <div class="piecenter">
                                <p>Total</p>
                                <p class="moneyTotal"></p>
                                <p>만원</p>
                            </div>
                        </div>
                        <div class="jusuck">
                            <p><span class="rect"></span>P.T</p>
                            <p><span class="rect"></span>그룹P.T</p>
                            <p><span class="rect"></span>필라테스</p>
                            <p<span class="rect"></span>요가</p0>
                            <p><span class="rect"></span>헬스</p>
                        </div>
                    </div>

                    <div class="right">
                        <table>
                            <tr>
                                <th><span class="rect"></span>P.T</th>
                                <td>
                                    <div class="barTle">
                                        <div class="GaugeBar">
                                            <span title="매출량(%)">
                                                <span class="part_percent">0</span>% (<span class="number">0</span>만원)
                                            </span>
                                        </div>
                                    </div>
                                    <div class="total_info">
                                        <span><b>100%</b></span><br>
                                        (<span class="total_number"></span>원)
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th><span class="rect"></span>그룹P.T</th>
                                <td>
                                    <div class="barTle">
                                        <div class="GaugeBar">
                                            <span title="매출량(%)">
                                                <span class="part_percent">0</span>% (<span class="number">0</span>만원)
                                            </span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th><span class="rect"></span>필라테스</th>
                                <td>
                                    <div class="barTle">
                                        <div class="GaugeBar">
                                            <span title="매출량(%)">
                                                <span class="part_percent">0</span>% (<span class="number">0</span>만원)
                                            </span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th><span class="rect"></span>요가</th>
                                <td>
                                    <div class="barTle">
                                        <div class="GaugeBar">
                                            <span title="매출량(%)">
                                                <span class="part_percent">0</span>% (<span class="number">0</span>만원)
                                            </span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th><span class="rect"></span>헬스</th>
                                <td>
                                    <div class="barTle">
                                        <div class="GaugeBar">
                                            <span title="매출량(%)">
                                                <span class="part_percent">0</span>% (<span class="number">0</span>만원)
                                            </span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <!-- 금월 가입자 -->
                <div class="box2 dash_board">
                    <h4>금월 가입자</h4>
                    <div class="gender">
                        <p class="total">
                            <span>Total</span><br>
                            <b>0명</b>
                        </p>

                        <div>
                            <div class="male">
                                <div></div>
                                <p class="male">남자 <span>0명</span></p>
                            </div>

                            <div class="female">
                                <div></div>
                                <p class="female">여자 <span>0명</span></p>
                            </div>
                        </div>
                    </div>
                    
                </div>

                <!-- 금일 근무자 -->
                <div class="box6 dash_board">
                    <h4>금일 근무자</h4>
                    <ul>
                        <!-- js -->
                    </ul>
                </div>

            </article>
        </section>
    </div>


<?php require_once 'lib/footer.php'; ?>
    
    
</body>
</html>