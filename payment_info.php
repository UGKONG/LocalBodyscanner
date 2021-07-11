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
    <script src="js/jquery-3.5.1.min.js"></script>
    <script src="js/jswLib.js"></script>
    <script src="js/Array.js"></script>
    <script src="js/script.js"></script>
    <script src="js/payment_info.js"></script>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/payment_info.css">
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
                            <p id="uName"><input type="text" name="paymentMember" id="paymentMember" value="전상욱" readonly required></p>
                            <p class="uNum" style="width: 142px;">연락처: <span id="uNum">010-1234-1234</span></p>
                        </div>
                    </article>
                    <article class="con3">
                        <h5>이용권 옵션</h5>
                        <div class="row1">
                            <label for="Payment-select1">결제 담당자</label>
                            <p name="Payment-select1" id="Payment-select1"><!-- js --></p>
                        </div>
                        <div class="row1">
                            <label for="Payment-select1_1">담당강사</label>
                            <p name="Payment-select1_1" id="Payment-select1_1"><!-- js --></p>
                        </div>
                        <div class="row2">
                            <label for="Payment-select2">이용 시작일</label>
                            <p name="Payment-select2" id="Payment-select2"></p>
                        </div>
                        <div class="row2">
                            <label for="Payment-select2_1">이용 만료일</label>
                            <p name="Payment-select2_1" id="Payment-select2_1"></p>
                        </div>
                        <div class="row3">
                            <label for="Payment-select3">이용가능 횟수</label>
                            <p name="Payment-select3" id="Payment-select3"></p>
                        </div>
                        <!-- <div class="row4">
                            <label for="Payment-money">결제금액</label>
                            <span name="Payment-money" id="Payment-money">0</span> 원
                        </div> -->
                    </article>
                    <article class="con2" style="margin-bottom:23px;">
                        <h5>결제 메모</h5>
                        <div class="memoTR">
                            <label for="Payment-memo" class="hid">결제 메모</label>
                            <textarea name="Payment-memo" id="Payment-memo"></textarea>
                        </div>
                    </article>
                    <article class="conBtn">
                        <button class="payment-submit-btn" type="submit" id="optionSaveBtn">수정하기</button>
                    </article>
                </section>
                

                <section class="col2">
                    
                    <article class="con2" style="margin-bottom:22px">
                        <h5>결제 상품 정보</h5>
                        <div class="row1">
                            <p id="PaymentItemName"></p>
                        </div>
                        <div class="row2">
                            <p>상품가격</p>
                            <p id="Payment" class="pointColor_blue data-pay" data-pay="0"></p>
                        </div>
                        <div class="row2">
                            <p>할인 금액</p>
                            <p id="DiscountAmount" class="pointColor_green data-pay" data-pay="0"></p>
                        </div>
                        <div class="row2">
                            <p>지불된 금액</p>
                            <p id="PaymentAmount" class="pointColor_blue data-pay" data-pay="0"></p>
                        </div>
                    </article>
                    <article class="con1" style="margin-bottom:27px;">
                        <h5>결제 진행</h5>
                        <div class="payDetail">
                            <p class="refundOK">※ 양도 또는 환불이 완료된 건입니다.</p>
                            <table class="itemPayTable">
                                <tr>
                                    <th>지불할 금액</th>
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
                    
                    <article class="conBtn">
                        <button class="payment-submit-btn" type="submit" id="paymentSaveBtn">결 제</button>
                    </article>
                </section>

                <section class="col3">
                    <article class="con1">
                        <h5>
                            상품 결제 정보
                            <button type="button" class="">결제내역보기</button>
                        </h5>
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
                                <h6>상품가격</h6>
                                <span id="Receipt_TicketPrice"></span>원
                            </div>
                            <div>
                                <h6>할인금액</h6>
                                <span id="Receipt_Sale2">0</span>원
                            </div>
                            <div>
                                <h6>지불된 금액</h6>
                                <span id="Receipt_Pament_amount">0</span>원
                            </div>
                            <div>
                                <h6>지불할 금액</h6>
                                <span id="Receipt_Pament_yet_amount">0</span>원
                            </div>
                            <div>
                                <h6>결제금액</h6>
                                <span id="Receipt_Payment">0</span>원
                            </div>
                            <div>
                                <h6>미수금</h6>
                                <span id="Receipt_YetPay">0</span>원
                            </div>
                            <div>
                                <h6>결제수단</h6>
                                <ul>
                                    <li>카드 : <span id="Receipt_CardPay">0</span>원</li>
                                    <li>현금 : <span id="Receipt_CashPay">0</span>원</li>
                                    <li>이체 : <span id="Receipt_AccountPay">0</span>원</li>
                                </ul>
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
                                <h6>결제일</h6>
                                <span id="Receipt_PaymentDate"></span>
                            </div>
                            <div>
                                <h6>결제 메모</h6>
                                <span id="Receipt_Memo" style="width: 100%;height: 60px;margin-top: 6px;font-size: 13px;text-indent: 4px;overflow: auto;"></span>
                            </div>
                        </div>
                        <div title="결제내역" class="PaymentList View" hidden>
                            <table>
                                <thead>
                                    <tr>
                                        <th>결제일시</th>
                                        <th>결제수단</th>
                                        <th>결제금액</th>
                                    </tr>
                                </thead>
                                <tbody style="text-align: center;transform: translateY(10px);">
                                    <tr>
                                        <td>
                                            2020-10-04<br>
                                            <small>10:40:21</small>
                                        </td>
                                        <td>카드</td>
                                        <td>1,200,000원</td>
                                        <td class="cancelBtn">
                                            <i class="fas fa-times"></i>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            2020-10-04<br>
                                            <small>10:40:21</small>
                                        </td>
                                        <td>현금</td>
                                        <td>500,000원</td>
                                        <td class="cancelBtn">
                                            <i class="fas fa-times"></i>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            2020-12-02<br>
                                            <small>17:58:51</small>
                                        </td>
                                        <td>이체</td>
                                        <td>1,400,000원</td>
                                        <td class="cancelBtn">
                                            <i class="fas fa-times"></i>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            2020-12-02<br>
                                            <small>17:58:51</small>
                                        </td>
                                        <td>카드</td>
                                        <td>100,000원</td>
                                        <td class="cancelBtn">
                                            <i class="fas fa-times"></i>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </article>
                    <article class="conBtn">
                        <button class="payment-give-btn" type="button"">양도하기</button>
                        <button class="payment-refund-btn" type="button">환불하기</button>
                        <button class="payment-cancel-btn" type="reset">닫 기</button>
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

    <!-- 양도하기 폼 -->
    <div id="giveVoucherFrm" style="display:none;">
        <h2>
            <span>이용권 양도</span>
            <button class="closePopup">
                <div><span></span><span></span></div>
            </button>
        </h2>

        <div class="content">

            <div class="manager">
                <p>
                    <label for="giveVoucherManager">담당자 : </label>
                    <span id="giveVoucherManager" style="font-size:14px;"></span>
                </p>
                <p>
                    <label for="giveVoucherPay">이용권 원가</label>
                    <span id="giveVoucherPay">1,000,000원</span>
                </p>
            </div>

            <div class="voucherInfo">
                <p class="giveType">그룹레슨 이용권</p>
                <p class="giveName">그룹 100회 이용권</p>
                <p class="giveDate">2021-04-21 ~ 2021-12-07 / 담당강사 : 전상욱</p>
                <p class="giveUse">이용일수 13/230일 · 이용횟수 4/100회 · 예약횟수 0회</p>
            </div>

            <div class="changeVoucherInfo">

                <div class="left">  <!-- 왼쪽 -->
                    <h3>양도인</h3>
                    <p>
                        <span class="title">성명</span>
                        <span class="name">박해성</span>
                    </p>
                    <p>
                        <span class="title">연락처</span>
                        <span class="phone">010-0000-0000</span>
                    </p>
                </div>

                <i class="fas fa-arrow-right"></i>

                <div class="right">  <!-- 오른쪽 -->
                    <h3>양수인</h3>
                    <!-- <p>
                        <span class="title">성명</span>
                        <span>전상욱</span>
                    </p>
                    <p>
                        <span class="title">연락처</span>
                        <span>010-0000-0000</span>
                    </p> -->
                    <p><button class="findMemberBtn">회원찾기</button></p>
                </div>

            </div>

            <div class="notice">
                이용권 양도 시에는 양도인은 더이상 해당 이용권을 사용 하실 수 없으며<br>
                모든 예약 건이 취소 됩니다.
            </div>
            
            <div class="btn">
                <button type="button" id="voucherGiveSubmit">양도</button>
                <button type="button" class="closePop">닫기</button>
            </div>
        </div>
    </div>


    <!-- 환불하기 폼 -->
    <div id="refundVoucherFrm" style="display:none;">
        <h2>
            <span>이용권 환불</span>
            <button class="closePopup">
                <div><span></span><span></span></div>
            </button>
        </h2>

        <div class="content">

            <div class="manager">
                <p>
                    <label for="refundVoucherManager">담당자</label>
                    <span id="refundVoucherManager" style="font-size:14px;"></span>
                </p>
            </div>

            <div class="voucherInfo">
                <p class="refundType">그룹레슨 이용권</p>
                <p class="refundName">그룹 100회 이용권</p>
                <p class="refundDate">2021-04-21 ~ 2021-12-07 / 담당강사 : 전상욱</p>
                <p class="refundUse">이용일수 13/230일 · 이용횟수 4/100회 · 예약횟수 0회</p>
            </div>

            <div class="changeVoucherInfo">

                <div class="left">  <!-- 왼쪽 -->
                    <h3>상품 구매 정보</h3>
                    <p>
                        <span class="title">원가</span>
                        <span class="ORIGINAL_PRICE">1,000,000원</span>
                    </p>
                    <p>
                        <span class="title">할인가</span>
                        <span class="DISCOUNT_PRICE">0원</span>
                    </p>
                    <p>
                        <span class="title">구매가</span>
                        <span class="PAYMENT_PRICE">1,000,000원 (혜택가)</span>
                    </p>
                    <p>
                        <span class="title">타입</span>
                        <span class="VOUCHER_TYPE">기간제</span>
                    </p>
                </div>

                <i class="fas fa-arrow-right"></i>

                <div class="right">  <!-- 오른쪽 -->
                    <h3>환불금</h3>
                    <p>
                        <span class="title">
                            <input type="radio" name="calc" id="autoCalc" checked>
                            <label for="autoCalc">자동</label>
                        </span>
                        <span>0원</span>
                    </p>
                    <p>
                        <span class="title">
                            <input type="radio" name="calc" id="inputCalc">
                            <label for="inputCalc">직접입력</label>
                        </span>
                        <span><input type="text" id="inputCalcPay" disabled></span>
                    </p>
                </div>

            </div>

            <div class="calc">

            </div>

            <div class="notice">
                이용권 환불 시에는 환불에 대한 위약금이 발생합니다.<br>
                위약금은 구매가에 10%입니다.<br>
                ※ 구매가 - (사용일/횟수 × 원가 ÷ 총 일/횟수) - 위약금
                
            </div>
            
            <div class="btn">
                <button type="button" id="voucherRefundSubmit">환불</button>
                <button type="button" class="closePop">닫기</button>
            </div>
        </div>
    </div>

    <div id="dark_div"></div>
</body>
</html>