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
    <title>닥터케어유니온 - 히스토리</title>
    <script src="js/jquery-3.5.1.min.js"></script>
    <script src="js/Array.js"></script>
    <script src="js/jswLib.js"></script>
    <script src="js/script.js"></script>
    <script src="js/history.js"></script>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/history.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css">
</head>
<body>
    <div class="dark_div"></div>
    <div id="wrap">
    <?php require_once 'lib/header.php'; ?>
        <section class="content">
            <h2 class="hid">컨텐츠 영역</h2>
            <div class="top_sMenu">
                <article class="sorting">
                    <div class="up">
                        <input type="date" name="hi_startDate" id="hi_startDate">
                        <label for="hi_startDate">부터</label>
                        <input type="date" name="hi_endDate" id="hi_endDate">
                        <label for="hi_endDate">까지</label>
                        <button type="button" class="hi_search_btn">조 회</button>
                        <button type="button" class="hi_search_reset">초기화</button>
                    </div>
                </article>
                
                <div class="down">
                    <button class="today">오늘</button>
                    <button class="lastday">어제</button>
                    <button class="lastday7">7일전</button>
                    <button class="lastday30">30일전</button>
                    <button class="nowMonth">당월</button>
                    <button class="lastMonth">전월</button>
                </div>
            </div>

            <article class="list">
                <div class="searchTop">
                    <div class="grade tab" data-sq="1">담당강사관리</div>
                    <div class="grade tab" data-sq="2">수업관리</div>
                    <div class="grade tab" data-sq="3">회원이용권관리</div>
                    <div class="grade tab" data-sq="4">회원출석관리</div>
                    <p>
                        <!-- <label for="seeListCount">페이지 당 
                            <select name="seeListCount" id="seeListCount">
                                <option value="10Count">10</option>
                                <option value="20Count">20</option>
                                <option value="30Count">30</option>
                                <option value="50Count">50</option>
                                <option value="100Count">100</option>
                            </select>
                            개 표시
                        </label> -->
                        <!-- <label class="sortList">
                            정렬
                            <select name="sortList" id="sortList">
                                <option value="10-1">내림차순</option>
                                <option value="1-10">오름차순</option>
                            </select>
                        </label> -->
                    </p>
                    <p>
                        <label for="hi_listSearch">검색</label>
                        <input type="text" name="hi_listSearch" id="hi_listSearch" placeholder="이용내역 검색">
                    </p>
                </div>
                <table class="hi_list_head">
                    <tr>
                        <th class="date">
                            <select name="hi_List_date" id="hi_List_date">
                                <option value="">날짜</option>
                            </select>
                        </th>
                        <th class="time">
                            시간
                        </th>
                        <th class="category">
                            <select name="hi_List_category" id="hi_List_category">
                                <option value="">카테고리</option>
                            </select>
                        </th>
                        <th class="user">
                            <select name="hi_List_user" id="hi_List_user">
                                <option value="">사용자</option>
                            </select>
                        </th>
                        <th class="content">
                            이용내역
                        </th>
                    </tr>
                </table>

                <!-- JS로 테이블 생성 -->
                <div class="listContent">
                    
                </div>
                
            </article>
            
        </section>

    </div>


    <address>Copyright &copy; Liansoft. Allright Reserved. 2020</address>
    

    
</body>
</html>