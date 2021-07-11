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
    <title>닥터케어유니온 - 회계</title>
    <script src="js/jquery-3.5.1.min.js"></script>
    <script src="js/Array.js"></script>
    <script src="js/jswLib.js"></script>
    <script src="js/script.js"></script>
    <script src="js/accounting.js"></script>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/accounting.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css">
</head>
<body>
    <div id="wrap">
        
<?php require_once 'lib/header.php'; ?>

        <section class="content">
            <h2 class="hid">컨텐츠 영역</h2>
            <div class="top_sMenu">
                <article class="Sorting">
                    <p>
                        <input type="text" name="memberSorting" id="memberSorting" placeholder="회원명으로 검색">
                        <label for="memberSorting" hidden>회원검색</label>

                        <select name="trainerSorting" id="trainerSorting">
                            <!-- 강사List _ JS -->
                        </select>
                        <label for="trainerSorting" hidden>강사선택</label>

                        <select name="payHowSorting" id="payHowSorting">
                            <option value="">모든결제수단</option>
                            <option value="card">카드결제</option>
                            <option value="cash">현금결제</option>
                            <option value="account">계좌이체</option>
                        </select>
                        <label for="trainerSorting" hidden>결제수단</label>

                        <select name="yetPayment_or_No" id="yetPayment_or_No">
                            <option value="">전체내역</option>
                            <option value="2">미수내역</option>
                        </select>
                    </p>
                    <p>
                        <select name="dateSortingEz" id="dateSortingEz">
                            <option value="today">오늘</option>
                            <option value="yesterday">어제</option>
                            <option value="lastday7" selected>7일전</option>
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
                    <button id="sortingResetBtn">초기화</button>
                </article>
            </div>

            <article class="container">
                <div class="chartCard">
                    <article class="calcEl0">
                        <div>
                            <h3 class="default">총 판매정가<i class="fas fa-credit-card"></i></h3>
                            <div class="con" id="Total-pay">
                                <p class="default"><span data-pay="0">0</span>원</p>
                                <div class="progressBarBorder default">
                                    <p class="progressBar" style="width: 100%;"></p>
                                </div>
                            </div>
                        </div>
                    </article>
                    <article class="calcEl1">
                        <div>
                            <h3>총 판매금액<i class="fas fa-user-check"></i></h3>
                            <div class="con" id="allTotal-pay">
                                <p><span data-pay="400" data-count="0">0</span>원</p>
                                <div class="progressBarBorder">
                                    <p class="progressBar" style="width: 100%;"></p>
                                </div>
                            </div>
                        </div>
                    </article>
                    <p class="equals"><i class="fas fa-minus"></i></p>
                    <article class="calcEl2">
                        <div>
                            <h3 class="default">총 미수금액<i class="fas fa-credit-card"></i></h3>
                            <div class="con" id="Total-yet">
                                <p class="default"><span data-pay="0">0</span>원</p>
                                <div class="progressBarBorder default">
                                    <p class="progressBar" style="width: 100%;"></p>
                                </div>
                            </div>
                        </div>
                    </article>
                    <p class="equals"><i class="fas fa-minus"></i></p>
                    <article class="calcEl3">
                        <div>
                            <h3 class="default">총 환불금액<i class="fas fa-credit-card"></i></h3>
                            <div class="con" id="Total-re">
                                <p class="default"><span data-pay="0">0</span>원</p>
                                <div class="progressBarBorder default">
                                    <p class="progressBar" style="width: 100%;"></p>
                                </div>
                            </div>
                        </div>
                    </article>
                    <p class="equals"><i class="fas fa-equals"></i></p>
                    <article class="calcEl4">
                        <div>
                            <h3 class="default">총 수익금액<i class="fas fa-credit-card"></i></h3>
                            <div class="con" id="Total-true">
                                <p class="default"><span data-pay="0">0</span>원</p>
                                <div class="progressBarBorder default">
                                    <p class="progressBar" style="width: 100%;"></p>
                                </div>
                            </div>
                        </div>
                    </article>

                </div>

                <div class="chartListHeader">
                    <div class="view">
                        <div id="sortingChk">
                            <!-- js -->
                        </div>
                    </div>
                    <table border="1">
                        <thead>
                            <tr>
                                <th>결제일시</th>
                                <th>회원명</th>
                                <th>연락처</th>
                                <th>판매상품</th>
                                <th>상태</th>
                                <th>정가</th>
                                <th>할인</th>
                                <th>판매가</th>
                                <th>결제분류</th>
                                <th>결제수단</th>
                                <th>결제금액</th>
                                <th>잔여미수금</th>
                                <th>환불수단</th>
                                <th>환불 지급액</th>
                                <th>담당자</th>
                                <th>메모</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div id="List">
                    <table border="1">
                        <tbody>
                            <tr>
                                <td colspan="16">내역이 없습니다.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </article>
            
        </section>

    </div>

    <?php require_once 'lib/footer.php'; ?>
    
    <div class="dark_div"></div>

    <div id="popMemo">
        <h2>
            <span>요약보기</span>
            <button class="closeBtn">
                <div>
                    <span></span>
                    <span></span>
                </div>
            </button>
        </h2>
        <div class="con">
            <div class="memoDate">
                <h3>결제일시</h3>
                <span>2020-12-01</span>
            </div>
            <div class="memoMemberName">
                <h3>회원명</h3>
                <span>전상욱</span>
            </div>
            <div class="memoPhone">
                <h3>연락처</h3>
                <span>010-1111-2222</span>
            </div>
            <div class="memoTeacher">
                <h3>담당자</h3>
                <span>박해성</span>
            </div>

            <h2>상품정보</h2>
            <hr>
            <div class="memoItemName">
                <h3>판매상품</h3>
                <span>PT 30회</span>
            </div>
            <div class="memoState">
                <h3>상 태</h3>
                <span>판매</span>
            </div>
            <div class="memoAmount">
                <h3>정 가</h3>
                <span>1,500,000원</span>
            </div>
            <div class="memoSaleAmount">
                <h3>할 인</h3>
                <span>0원</span>
            </div>
            <div class="memoAfterAmount">
                <h3>판매가</h3>
                <span>1,500,000원</span>
            </div>

            <h2>메 모</h2>
            <hr>
            <div class="memoMemo">
                <p>
                    메모입니다. 메모입니다. 메모입니다. 메모입니다. 메모입니다. 메모입니다. 메모입니다.
                    메모입니다. 메모입니다. 메모입니다. 메모입니다. 메모입니다. 메모입니다. 메모입니다.
                    메모입니다. 메모입니다. 메모입니다. 메모입니다. 메모입니다. 메모입니다. 메모입니다.
                </p>
            </div>
            <div class="button">
                <button>닫 기</button>
            </div>
        </div>
    </div>
    
</body>
</html>