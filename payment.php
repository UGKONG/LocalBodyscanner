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
    <script src="js/jquery-3.5.1.min.js"></script>
    <script src="js/jswLib.js"></script>
    <script src="js/Array.js"></script>
    <script src="js/script.js"></script>
    <script src="js/payment.js"></script>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/payment.css">
    <title>닥터케어유니온 - 결제</title>
</head>
<body> 
    <script>
        var ITEM_SEQ = '<?=getAnyParameter("seq","")?>';
        var SESSION_SQ = '<?=$USER_SQ?>';
    </script>

    <article class="Pay other">
        <div>
            <h4>이용권 결제</h4>
            <form action="#" method="POST" autocomplete="off" name="paymentFrm" id="paymentFrm">
                <section class="col1">
                    <article class="con1">
                        <h5>회원정보</h5>
                        <div class="row1">
                            <p id="uName">
                                <input type="text" name="paymentMember" id="paymentMember" 
                                       placeholder="회원이 선택해주세요." readonly required 
                                       style="border: none; outline: none; font-size: 14px;">
                            </p>
                            <p class="uNum" style="width: 142px;">연락처: <span id="uNum">
                                <!-- 연락처 View js -->
                            </span></p>
                        </div>
                    </article>
                    <article class="con2">
                        <h5>결제 상품 정보</h5>
                        <div class="row1">
                            <p id="PaymentItemName"></p>
                        </div>
                        <div class="row2">
                            <p>상품가격</p>
                            <p id="Payment" class="pointColor_blue data-pay" data-pay="0"></p>
                        </div>
                        <div class="row5">
                            <label for="Payment-sale">할인선택</label>
                            <select name="Payment-sale-ez" id="Payment-sale-ez">
                                <option value="">할인율</option>
                                <!-- js -->
                            </select>
                            
                            <label for="Payment-sale-data-won" style="display: none;">할인입력</label>
                            <input type="text" id="Payment-sale-data-won" placeholder="할인입력" required>
                            <span id="item_sale_text">%</span>
                        </div>
                    </article>
                    <article class="con3">
                        <h5>결제 옵션</h5>
                        <div class="row1">
                            <label for="Payment-select1">결제 담당자 선택</label>
                            <select name="Payment-select1" id="Payment-select1" required>
                                <option value="">담당직원선택</option>
                                <!-- js -->
                            </select>
                        </div>
                        <div class="row1_1">
                            <label for="Payment-select1_1">강사 선택</label>
                            <select name="Payment-select1_1" id="Payment-select1_1" required>
                                <option value="">강사선택</option>
                                <!-- js -->
                            </select>
                        </div>
                        <div class="row2">
                            <label for="Payment-select2">이용 시작일 선택</label>
                            <input type="date" name="Payment-select2" id="Payment-select2" required>
                        </div>
                        <div class="row2">
                            <label for="Payment-select2_1">이용 만료일</label>
                            <input type="date" name="Payment-select2_1" id="Payment-select2_1" readonly required>
                        </div>
                        <div class="row3">
                            <label for="Payment-select3">이용가능 횟수</label>
                            <input type="text" name="Payment-select3" id="Payment-select3" required readonly>
                        </div>
                    </article>
                </section>
                <section class="col2">
                    <article class="con1">
                        <h5>결제 진행</h5>
                        <div>
                            <table class="itemPayTable">
                                <tr>
                                    <th>상품정가 <!--<small>(할인)</small>-->
                                    </th>
                                    <td class="PayStart"><span id="startPay">0</span> 원</td>
                                </tr>
                                <tr class="null"></tr>
                                <tr class="tableStart">
                                    <th>카 드</th>
                                    <td class="indent">
                                        <input type="text" name="cardPayment" id="cardPayment" maxlength="8" placeholder="숫자만 입력">
                                        <label for="cardPayment"> 원</label>
                                    </td>
                                </tr>
                                <tr>
                                    <th>현 금</th>
                                    <td class="indent">
                                        <input type="text" name="cashPayment" id="cashPayment" maxlength="8" placeholder="숫자만 입력">
                                        <label for="cashPayment"> 원</label>
                                    </td>
                                </tr>
                                <tr>
                                    <th>이 체</th>
                                    <td class="indent">
                                        <input type="text" name="accountPayment" id="accountPayment" maxlength="8" placeholder="숫자만 입력">
                                        <label for="accountPayment"> 원</label>
                                    </td>
                                </tr>
                                <tr>
                                    <th>할 인</th>
                                    <td><span id="salePayment">0</span> 원</td>
                                </tr>
                                <tr>
                                    <th>미 수 금</th>
                                    <td class="pointColor_red"><span id="yetPayment">0</span> 원</td>
                                </tr>
                                <tr class="null"></tr>
                                <tr class="tableEnd">
                                    <th>총 결제금액</th>
                                    <td class="pointColor_blue"><span id="totalPayment">0</span> 원</td>
                                </tr>
                            </table>
                        </div>
                    </article>
                    <article class="con2">
                        <h5>결제 메모</h5>
                        <div class="memoTR">
                            <label for="Payment-memo" class="hid">결제 메모</label>
                            <textarea name="Payment-memo" id="Payment-memo"></textarea>
                        </div>
                    </article>
                </section>
                <section class="col3">
                    <article class="con1">
                        <h5>상품 결제 정보</h5>
                        <div title="영수증" class="Receipt">
                            <div>
                                <h6>회원명</h6>
                                <span id="Receipt_Name">전상욱</span>
                            </div>
                            <div>
                                <h6>결제 상품명</h6>
                                <span id="Receipt_TicketName"></span>
                            </div>
                            <div>
                                <h6>상품정가</h6>
                                <span id="Receipt_TicketPrice"></span>원
                            </div>
                            <div>
                                <h6>할인</h6>
                                <span id="Receipt_Sale1">0</span>원
                            </div>
                            <div>
                                <h6>할인가</h6>
                                <span id="Receipt_Sale2">0</span>원
                            </div>
                            <div>
                                <h6>결제수단</h6>
                                <ul>
                                    <li>카드 : <span id="Receipt_CardPay">0</span>원</li>
                                    <li>현금 : <span id="Receipt_CashPay">0</span>원</li>
                                    <li>이체 : <span id="Receipt_AccountPay">0</span>원</li>
                                </ul>
                            </div>
                            <div>
                                <h6>결제금액</h6>
                                <span id="Receipt_Payment">0</span>원
                            </div>
                            <div>
                                <h6>미수금</h6>
                                <span id="Receipt_YetPay">0</span>원
                            </div>
                            <hr>
                            <div>
                                <h6>결제 담당자</h6>
                                <span id="Receipt_Teacher">미선택</span>
                            </div>
                            <div>
                                <h6>담당 강사</h6>
                                <span id="Receipt_Teacher_1">미선택</span>
                            </div>
                            <div>
                                <h6>이용 시작일</h6>
                                <span id="Receipt_StartDate">미선택</span>
                            </div>
                            <div>
                                <h6>이용 만료일</h6>
                                <span id="Receipt_EndDate">미선택</span>
                            </div>
                            <div>
                                <h6>결제 메모</h6>
                                <span id="Receipt_Memo" style="width: 100%;margin-top: 6px;font-size: 12px;text-indent: 4px;height: 60px;border: 1px solid #bbb;overflow:auto;padding: 3px 2px;"></span>
                            </div>
                        </div>
                    </article>
                    <article class="conBtn">
                        <button class="payment-submit-btn" type="submit">결제하기</button>
                        <button class="payment-cancel-btn" type="reset">취 소</button>
                    </article>
                </section>
            </form>
        </div>
    </article>


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
            <li data-seq="2980">박해성</li>
            <li data-seq="2981">김형준</li>
            <li data-seq="2982">박종남</li>
            <li data-seq="2983">전상욱</li>
            <li data-seq="2984">박해성</li>
            <li data-seq="2985">김형준</li>
            <li data-seq="2986">박종남</li>
            <li data-seq="2987">전상욱</li>
        </ul>
    </div>

</body>
</html>