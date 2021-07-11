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
    <title>닥터케어유니온 - 통계</title>
    <script src="js/jquery-3.5.1.min.js"></script>
    <script src="js/Array.js"></script>
    <script src="js/jswLib.js"></script>
    <script src="js/script.js"></script>
    <script src="js/chart_group.js"></script>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/chart_group.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css">
</head>
<body>
    <div class="dark_div"></div>
    <div id="wrap">
        
<?php require_once 'lib/header.php'; ?>
        <section class="content">
            <h2 class="hid">컨텐츠 영역</h2>

            <div class="top_sMenu">
                <article class="Sorting">
                    <p>
                        <!-- <select name="centerSorting" id="centerSorting">
                            <option value="">센터선택</option>
                        </select>
                        <label for="centerSorting" hidden>센터선택</label> -->

                        <select name="trainerSorting" id="trainerSorting">
                            <!-- js -->
                        </select>
                        <label for="trainerSorting" hidden>강사선택</label>
                        
                        <!-- <select name="classSorting" id="classSorting">
                            <option value="">수업선택</option>
                        </select>
                        <label for="classSorting" hidden>수업선택</label> -->
                    </p>
                    <p>
                        <select name="dateSortingEz" id="dateSortingEz">
                            <option value="today">오늘</option>
                            <option value="yesterday">어제</option>
                            <option value="lastday7">7일전</option>
                            <option value="lastday30">30일전</option>
                            <option value="thismonth">당월</option>
                            <option value="lastmonth">전월</option>
                            <option value="thisyear">당해</option>
                            <option value="lastyear">전해</option>
                        </select>
                        <input type="date" name="dateSorting1" id="dateSorting1">
                        <label for="dateSorting1">부터</label>
                        <input type="date" name="dateSorting2" id="dateSorting2">
                        <label for="dateSorting2">까지</label>
                    </p>
                    <button id="sortingBtn">검색</button>
                </article>
                <article class="solo_group_cho">
                    <button id="solo_chart" onclick="location.href='chart.php'">개인레슨 통계</button>
                    <button id="group_chart" class="active">그룹레슨 통계</button>
                </article>
            </div>

            <article class="container container0">
                <div class="chartCard">
                    <article class="calcEl0">
                        <div>
                            <h3>전체 수업 진행 횟수<i class="fas fa-user-check"></i></h3>
                            <div class="con" id="checkin">
                                <p><span data-count-all="30" data-count="12">0</span>회</p>
                                <p class="count" data-count="120" data-count-all="200" hidden></p>
                                <div class="progressBarBorder">
                                    <p class="progressBar" style="width: 100%;"></p>
                                </div>
                            </div>
                        </div>
                    </article>
                    <p class="equals"><i class="fas fa-times"></i></p>
                    <article class="calcEl1">
                        <div>
                            <!-- <i class="fas fa-coins"></i> -->
                            <h3 class="default">전체 참여 회원수<i class="fas fa-credit-card"></i></h3>
                            <h3 class="tCalc" hidden>그룹레슨 수당 <i class="fas fa-coins"></i></h3>
                            <div class="con" id="card">
                                <p class="default"><span data-count-all="200" data-count="120">0</span>명</p>
                                <p class="tCalc" hidden><input type="text"></p>
                                <button class="tCalc" hidden>계 산</button>
                                <div class="progressBarBorder default">
                                    <p class="progressBar" style="width: 100%;"></p>
                                </div>
                            </div>
                        </div>
                    </article>
                    <p class="equals"><i class="fas fa-equals"></i></p>
                    <article class="calcEl2">
                        <div>
                            <h3>산출결과<i class="fas fa-dollar-sign"></i></h3>
                            <div class="con" id="cash">
                                <p><span data-pay="" data-tax="">0</span>원</p>
                                <div>
                                    <input type="checkbox" name="taxChk" id="taxChk" checked>
                                    <label for="taxChk">VAT포함</label>
                                </div>
                            </div>
                        </div>
                    </article>


                </div>

                <div class="chartListHeader">
                    <div class="view">
                        <p>
                            <label for="searchList">검색 : </label>
                            <input type="text" name="searchList" id="searchList" placeholder="강사명, 수업명 등 검색">
                        </p>
                        <div id="sortingChk">
                            <!-- js -->
                        </div>
                    </div>
                    <table border="1">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>날 짜</th>
                                <th>강사명</th>
                                <th>수업명</th>
                                <th>참여회원수</th>
                                <th>수당</th>
                                <th>메 모</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div id="List">
                    <table border="1">
                        <tbody>
                            <tr>
                                <td colspan="7">로딩중..</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </article>
            
        </section>

    </div>

    <div class="modal">
        <h3>
            <span>회원리스트</span>
            <button class="closeBtn">
                <div>
                    <span></span>
                    <span></span>
                </div>
            </button>
        </h3>
        <div class="con">
            <div class="lengthView">
                <article>
                    <h4>총 참여 회원 수</h4>
                    <p>10명</p>
                </article>
                <article>
                    <h4>출석 회원 수</h4>
                    <p>7명</p>
                </article>
                <article>
                    <h4>결석 회원 수</h4>
                    <p>6명</p>
                </article>
            </div>
            <table border="1">
                <thead>
                    <tr>
                        <th>회원명</th>
                        <th>연락처</th>
                        <th>수업명</th>
                        <th>상태</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="4">로딩중..</td>
                    </tr>    
                </tbody>
            </table>
        </div>
        <button>닫 기</button>
    </div>

    <address>Copyright &copy; Liansoft. Allright Reserved. 2020</address>
    

    
</body>
</html>