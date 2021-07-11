var TODAY_DATE = new Date();
var SET = {};
var PAYMENT_INFO = {};
var TRAINER_LIST = [];

var SELECTED_ITEM = {};
var SELECTED_MEMBER_SQ = 0;
var SELECTED_TRAINER_SQ = 0;

var CALC = {};

var CHECKED = false;

var RECEIVE_CHOICE = false;

function AJAX_DATA(itemSq){
    let formData = new FormData();
    formData.append('UV_SQ',itemSq);
    $.ajax({
        url: "flow_controller.php?task=getUserVoucherInfo",
        method: "POST",
        data: formData,
		contentType: false,
        processData: false,
		success: function(result){
            var data = result.split('|');
            if(data.result)return false;
            PAYMENT_INFO = JSON.parse(data[0]);
            PAYMENT_DETAIL_LIST = JSON.parse(data[1]);
            TRAINER_LIST = JSON.parse(data[2]);
            MEMBER_LIST = JSON.parse(data[3]).filter(e => e.ISUSE == 1);

            SET.USE_STATUS = JSON.parse(data[4]);
            SET.PAY_STATUS = JSON.parse(data[5]);
            SET.FUND_TYPE = JSON.parse(data[6]);
            SET.PAY_TYPE = JSON.parse(data[7]);

            MAKE_TRAINER_LIST(TRAINER_LIST);
            MAKE_MEMBER_LIST(MEMBER_LIST);
            MAKE_ITEM_DATA(PAYMENT_INFO[0]);
            PAYED_LIST(PAYMENT_DETAIL_LIST);

		},
		error: function (e){
            location.reload();
			console.log(e);
		}
    });
}

function MAKE_TRAINER_LIST(list){
    $('#Payment-select1').add($('#Payment-select1_1')).html('<option value="">선택</option>');

    for(let i in list){
        var tag = '<option value="' + list[i].USER_SQ + '">' + list[i].USER_NM + '</option>';
        $('#Payment-select1').add($('#Payment-select1_1')).append(tag);
    }

    $('#Payment-select1').change(function(){
        SELECTED_TRAINER_SQ = $(this).val();
        if(SELECTED_TRAINER_SQ == ''){
            $('#Receipt_Teacher').text('미선택');
        }else{
            $('#Receipt_Teacher').text(
                (TRAINER_LIST.filter(e => e.USER_SQ == SELECTED_TRAINER_SQ))[0].USER_NM
            );
        }
    }).change();

    $('#Payment-select1_1').change(function(){
        SELECTED_TRAINER_SQ = $(this).val();
        if(SELECTED_TRAINER_SQ == ''){
            $('#Receipt_Teacher_1').text('미선택');
        }else{
            $('#Receipt_Teacher_1').text(
                (TRAINER_LIST.filter(e => e.USER_SQ == SELECTED_TRAINER_SQ))[0].USER_NM
            );
        }
    }).change();
}
function PAYMENT_PAGE_RESET(){
    $('#paymentMember').val('');
    $('#uNum').text('')
    $('#Payment-sale-data-won').val('');
    $('#Payment-select2').text('');
    $('#Payment-select2_1').text('');
    $('#Receipt_TicketName').text('');
    $('#Payment-memo').val('');
    $('#Receipt_Name').text('');
    $('#Receipt_Sale1').text('0');
    $('#Receipt_Sale2').text('0');
    $('#Receipt_CardPay').text('0');
    $('#Receipt_CashPay').text('0');
    $('#Receipt_AccountPay').text('0');
    $('#Receipt_Payment').text('0');
    $('#Receipt_YetPay').text('0');
    $('#Receipt_Teacher').text('미선택');
    $('#Receipt_StartDate').text('미선택');
    $('#Receipt_EndDate').text('미선택');
    $('#Receipt_PaymentDate').text('');
    $('#Receipt_Memo').text('');
}

function MAKE_ITEM_DATA(obj){
    SELECTED_SELLER_SQ = obj.SELLER_SQ;
    SELECTED_TRAINER_SQ = obj.TRAINER_SQ;
    PAYMENT_PAGE_RESET();

    SELECTED_ITEM.SQ = obj.UV_SQ;
    SELECTED_ITEM.ATTR = obj.USE_TYPE;
    SELECTED_ITEM.PERIOD_TYPE = obj.PERIOD_TYPE;
    SELECTED_ITEM.PERIOD_UNIT = obj.PERIOD_UNIT;
    SELECTED_ITEM.PERIOD = obj.PERIOD;
    SELECTED_ITEM.NAME = obj.VOUCHER_NAME;
    SELECTED_ITEM.PRICE = Number(obj.SELLINGPRICE);
    SELECTED_ITEM.PAYED_AMOUNT = Number(obj.PAYED_AMOUNT);
    SELECTED_ITEM.PAY_STATUS = obj.PAY_STATUS;

    CALC.SALE = 0;
    CALC.CARD = 0;
    CALC.CASH = 0;
    CALC.ACCOUNT = 0;
    CALC.DISCOUNT_PRICE = 0;
    CALC.YET_PRICE = 0;
    CALC.PAYMENT_PRICE = 0;
    CALC.SALE = 0;
    CALC.END_DATE = '0000-00-00';

    $('#paymentMember').val(obj.USER_NM);
    $('#Receipt_Name').text(obj.USER_NM);
    $('#uNum').text(obj.PHONE_NO);
    $('#DiscountAmount').text(numberFormat(obj.DISCOUNT_AMOUNT) + '원');
    $('#Receipt_Sale2').text(numberFormat(obj.DISCOUNT_AMOUNT));
    $('#PaymentItemName').add($('#Receipt_TicketName')).text(obj.VOUCHER_NAME);
    $('#PaymentAmount').text(numberFormat(obj.PAYED_AMOUNT) + '원');
    $('#Receipt_Pament_amount').text(numberFormat(obj.PAYED_AMOUNT));
    $('#Receipt_TicketPrice').add($('#startPay')).text(numberFormat(obj.ORIGINAL_PRICE));
    $('#Payment').text(numberFormat(obj.ORIGINAL_PRICE) + '원');
    $('#Payment-select3').text(obj.COUNT == 0 ? '무제한' : obj.COUNT + '회');
    setTimeout(()=>{
        $('#Receipt_Teacher')
        .add($('#Payment-select1'))
        .add($('#giveVoucherManager'))
        .add($('#refundVoucherManager')).text(
            (TRAINER_LIST.filter(e => e.USER_SQ == SELECTED_SELLER_SQ))[0].USER_NM
        );
        $('#Receipt_Teacher_1').add($('#Payment-select1_1')).text(
            TRAINER_LIST.filter(e => e.USER_SQ == SELECTED_TRAINER_SQ).length == 0 ? 
            TRAINER_LIST.filter(e => e.USER_SQ == SELECTED_SELLER_SQ)[0].USER_NM :
            TRAINER_LIST.filter(e => e.USER_SQ == SELECTED_TRAINER_SQ)[0].USER_NM
        );
    },100);
    $('#Payment-select2').text(obj.USE_STARTDATE.split(' ')[0]);
    $('#Payment-select2_1').text(obj.USE_LASTDATE.split(' ')[0]);
    $('#Receipt_StartDate').text(obj.USE_STARTDATE.split(' ')[0]);
    $('#Receipt_EndDate').text(obj.USE_LASTDATE.split(' ')[0]);
    $('#Receipt_PaymentDate').text(obj.MODIFIEDDT);
    $('#Payment-memo').val(obj.PAY_MEMO);
    $('#Receipt_Memo').text(obj.PAY_MEMO);
    
    RESULT_CALC();
    

    if((SELECTED_ITEM.PRICE - SELECTED_ITEM.PAYED_AMOUNT) == '0'){    // 완납
        $('#paymentSaveBtn').hide();
        $('.payment-give-btn').show();
        $('.payment-refund-btn').show();
    }else{
        $('#paymentSaveBtn').show();
        $('.payment-give-btn').hide();
        $('.payment-refund-btn').show();
    }

    if(SELECTED_ITEM.PAY_STATUS == 3 || SELECTED_ITEM.PAY_STATUS == 4){
        $('#paymentSaveBtn').hide();
        $('.payment-give-btn').hide();
        $('.payment-refund-btn').hide();
        $('div.payDetail > p.refundOK').show();

        $('#cardPayment').attr('disabled', true).attr('placeholder','입력 불가');
        $('#cashPayment').attr('disabled', true).attr('placeholder','입력 불가');
        $('#accountPayment').attr('disabled', true).attr('placeholder','입력 불가');

    }

}




function RESULT_CALC(){
    CALC.DISCOUNT_PRICE = Number(SELECTED_ITEM.PRICE) - Number(SELECTED_ITEM.PAYED_AMOUNT);
    CALC.PAYMENT_PRICE = Number(CALC.CARD) + Number(CALC.CASH) + Number(CALC.ACCOUNT);
    // CALC.YET_PRICE = CALC.DISCOUNT_PRICE - CALC.PAYMENT_PRICE >= 0 ? CALC.DISCOUNT_PRICE - CALC.PAYMENT_PRICE : 0;
    CALC.YET_PRICE = 
        (SELECTED_ITEM.PRICE - SELECTED_ITEM.PAYED_AMOUNT) - CALC.PAYMENT_PRICE >= 0 ? 
        (CALC.DISCOUNT_PRICE - CALC.PAYMENT_PRICE) : 0;

    RESULT_PRINT();
}
function RESULT_PRINT(){
    $('#startPay').text(numberFormat(CALC.DISCOUNT_PRICE));
    $('#salePayment').text(numberFormat(CALC.SALE));
    $('#yetPayment').text(numberFormat(SELECTED_ITEM.PRICE - SELECTED_ITEM.PAYED_AMOUNT));
    $('#totalPayment').text(numberFormat(CALC.PAYMENT_PRICE));
    $('#yetPayment').text(numberFormat(CALC.YET_PRICE));
    // 영수증
    $('#Receipt_Pament_yet_amount').text(numberFormat(CALC.DISCOUNT_PRICE));
    $('#Receipt_CardPay').text(numberFormat(CALC.CARD));
    $('#Receipt_CashPay').text(numberFormat(CALC.CASH));
    $('#Receipt_AccountPay').text(numberFormat(CALC.ACCOUNT));
    $('#Receipt_Payment').text(numberFormat(CALC.PAYMENT_PRICE));
    $('#Receipt_YetPay').text(numberFormat(CALC.YET_PRICE));

}

// 결제내역보기
function PAYED_LIST(list){
    var tag = '';
    $('[title="결제내역"] > table > tbody').empty();

    for(let i in list){
        KO_PAY_TYPE = list[i].PAY_TYPE == 1 ? '카드' : list[i].PAY_TYPE == 2 ? '현금' : '이체';
        var STATE = (SELECTED_ITEM.PAY_STATUS == 3 || SELECTED_ITEM.PAY_STATUS == 4) ? 'none' : '';
        var xBtn = list[i].PAY_TYPE == 1 ? 
            '<td class="cancelBtn"\
                data-pay-sq="' + list[i].PAY_SQ + '"\
                data-uv-sq="' + list[i].UV_SQ + '"\
                data-pay-detail-sq="' + list[i].PAYDETAIL_SQ + '"\
                data-pay-amount="' + list[i].PAY_AMOUNT + '"\
            >\
            <!--<i class="fas fa-times" style="display:' + STATE + '"></i></td>-->' : '';

        tag +=
            '<tr>\
                <td>' + list[i].CREATEDDT.split(' ')[0] + '<br><small>' + list[i].CREATEDDT.split(' ')[1] + '</small></td>\
                <td>' + KO_PAY_TYPE + '</td>\
                <td>' + numberFormat(list[i].PAY_AMOUNT) + '원</td>' + 
                xBtn +
            '</tr>'
    }
    $('[title="결제내역"] > table > tbody').append(tag);

    $('[title="결제내역"] > table > tbody .cancelBtn > i').click(function(){
        var ask = confirm('카드로 결제한 금액을 취소처리 하시겠습니까?');
        if(ask){

            var UV_SQ = $(this).parent().attr('data-uv-sq');
            var PAY_SQ = $(this).parent().attr('data-pay-sq');
            var PAYDETAIL_SQ = $(this).parent().attr('data-pay-detail-sq');
            var PAYED_AMOUNT_CARD = $(this).parent().attr('data-pay-amount');

            var formData = new FormData();
                formData.append('UV_SQ', UV_SQ);
                formData.append('PAY_SQ', PAY_SQ);
                formData.append('PAYDETAIL_SQ', PAYDETAIL_SQ);
                formData.append('PAYED_AMOUNT_CARD(카드취소금액)', PAYED_AMOUNT_CARD);

            $.ajax({
                url: 'flow_controller.php?task=execPurchaseCardCancel',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(r){
                    var data = JSON.parse(r);
                    console.log(data);
                    if(data.result == 'Fail'){
                        alertApp('X', '취소 오류! 다시 시도해주세요.');
                        return false;
                    }
                    alertApp('O', '카드결제가 취소되었습니다.');
                    location.reload();
                },
                error: function(e){
                    alertApp('X', '다시 시도해주세요');
                    return false;
                }
            });

        }else{
            return false;
        }
    });
}


// 멤버리스트 제작
function MAKE_MEMBER_LIST(list){
    
    $('.mSearch_container .name_list').empty();

    for(let i of list){
        $('.mSearch_container .name_list').append(
            `<li data-sq="${i.USER_SQ}"
                 data-phone="${i.PHONE_NO}"
                 data-name="${i.USER_NM}"
            >
                <div class="name">${i.USER_NM}</div>
                <div>
                    <p>${birth_year(i.BIRTH_DT)}</p>
                    <p>${i.PHONE_NO}</p>
                </div>
            </li>`
        );
    }

    $('.mSearch_container li').click(function(){
        RECEIVE_CHOICE = true;
        var SQ = $(this).attr('data-sq');
        var PHONE = $(this).attr('data-phone');
        var NAME = $(this).attr('data-name');

        $('#giveVoucherFrm .right').html(
            `<h3>양수인<button>변경</button></h3>
            <p>
                <span class="title">성명</span>
                <span class="name" data-sq="${SQ}">${NAME}</span>
            </p>
            <p>
                <span class="title">연락처</span>
                <span class="phone">${PHONE}</span>
            </p>`
        )
        $('.mSearch_container').fadeOut(200);

        $('#giveVoucherFrm .right h3 button').click(function(){
            $('.mSearch_container').fadeIn(200);
        });
    });
}


$(function(){

    AJAX_DATA(ITEM_SEQ);
    RESULT_CALC();


    // 이용 시작일 선택
    $('#Payment-select2').change(function(){
        var START_DATE = new Date($(this).val());
        
        // 이용만료일 계산
        CALC.END_DATE = (function(attr,periodType,unit,period){
            switch(attr){

                case 1 :  // 기간제

                    if(unit == 1){    // 일
                        START_DATE.setDate(START_DATE.getDate() + period);
                        return dateFormat(START_DATE);
                        
                    }else{              // 개월
                        return MONTH_CALC(START_DATE,period);
                    }

                case 2 :  // 횟수제

                    if(periodType == 1){// 무제한
                        return '2099-01-01';
                    }else{// 기간지정

                        if(unit == 1){// 일
                            START_DATE.setDate(START_DATE.getDate() + period);
                            return dateFormat(START_DATE);
                        }else{// 개월
                            return MONTH_CALC(START_DATE,period);
                        }

                    }

            }
        })(
            Number(SELECTED_ITEM.ATTR),
            Number(SELECTED_ITEM.PERIOD_TYPE),
            Number(SELECTED_ITEM.PERIOD_UNIT),
            Number(SELECTED_ITEM.PERIOD)
        );
            console.log(CALC.END_DATE);
        $('#Payment-select2_1').val(dateFormat(CALC.END_DATE));
        $('#Receipt_StartDate').text($(this).val());
        $('#Receipt_EndDate').text(dateFormat(CALC.END_DATE));
    });

    // 개월 계산
    function MONTH_CALC(date,month){
        date.setMonth(date.getMonth() + month);
        date.setDate(date.getDate() - 1);
        var y = date.getFullYear();
        var m = String(date.getMonth() + 1).length == 1 ? '0' + String(date.getMonth() + 1) : String(date.getMonth() + 1);
        var d = String(date.getDate()).length == 1 ? '0' + String(date.getDate()) : String(date.getDate());
    
        console.log(y + '-' + m + '-' + d);
        return y + '-' + m + '-' + d;
    }


    // 결제메모
    $('#Payment-memo').blur(function(){
        var text = $(this).val();
        $('#Receipt_Memo').text(text);
    });

    // 양도하기 버튼클릭
    $('button.payment-give-btn').click(function(){
        RECEIVE_CHOICE = false;
        if($USER_GRADE < 3){
            if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 53) == -1){
                alertApp('X', '권한이 없습니다.');
                return false;
            }
        }

        var now = new Date();
        now.setHours(0);now.setMinutes(0);now.setSeconds(0);now.setMilliseconds(0);
        var startDt = new Date(PAYMENT_INFO[0].USE_STARTDATE);
        var endDt = new Date(PAYMENT_INFO[0].USE_LASTDATE);
        var calcDt = (now - startDt) / 1000 / 24 / 60 / 60;
        var allDt = (endDt - startDt) / 1000 / 24 / 60 / 60;
    
        $('#giveVoucherPay').text(
            numberFormat(PAYMENT_INFO[0].ORIGINAL_PRICE) + '원'
        );
        $('#giveVoucherFrm .giveType').text(
            PAYMENT_INFO[0].VOUCHER_TYPE_NAME
        );
        $('#giveVoucherFrm .giveName').text(
            PAYMENT_INFO[0].VOUCHER_NAME
        );
        $('#giveVoucherFrm .giveDate').text(
            PAYMENT_INFO[0].USE_STARTDATE.split(' ')[0] + ' ~ ' + 
            PAYMENT_INFO[0].USE_LASTDATE.split(' ')[0] + ' / 담당강사 : ' +
            TRAINER_LIST.filter(x => x.USER_SQ == PAYMENT_INFO[0].TRAINER_SQ)[0].USER_NM
        );
        $('#giveVoucherFrm .giveUse').text(
            '이용일수 ' + (calcDt < 0 ? 0 : calcDt) + '/' + allDt + '일 · ' + 
            '이용횟수 ' + (PAYMENT_INFO[0].USE_TYPE == 1 ? '무제한' : PAYMENT_INFO[0].USEDCOUNT + '/' + PAYMENT_INFO[0].COUNT + '회') + ' · ' +
            '예약횟수 ' + PAYMENT_INFO[0].RESERV_COUNT + '회'
        );

        $('#giveVoucherFrm div.left').find('.name')
            .attr('data-sq', PAYMENT_INFO[0].MEMBER_SQ)
            .text(PAYMENT_INFO[0].USER_NM);
        
        $('#giveVoucherFrm div.left').find('.phone')
            .text(PAYMENT_INFO[0].PHONE_NO);

        $('#giveVoucherFrm .right').html(
            `<h3>양수인</h3><p><button class="findMemberBtn">회원찾기</button></p>`
        );
        
        $('#giveVoucherFrm').add($('#dark_div')).fadeIn(200);
        
        
        // 회원찾기
        $('#giveVoucherFrm .findMemberBtn').click(function(){
            $('.mSearch_container').fadeIn(200);
        });
    });

    // 양도 Submit
    $('#voucherGiveSubmit').click(function(){
        if(!RECEIVE_CHOICE){
            alertApp('!', '양수인을 선택해주세요.');
            return false;
        }

        var UV_SQ = PAYMENT_INFO[0].UV_SQ;
        var PAY_SQ = PAYMENT_INFO[0].PAY_SQ;
        var MEMBER_SQ = PAYMENT_INFO[0].MEMBER_SQ;
        var MEMBER_SQ_TO = $('#giveVoucherFrm .right .name').attr('data-sq');

        var formData = new FormData();
            formData.append('UV_SQ', UV_SQ);
            formData.append('PAY_SQ', PAY_SQ);
            formData.append('MEMBER_SQ', MEMBER_SQ);
            formData.append('MEMBER_SQ_TO', MEMBER_SQ_TO);

        $.ajax({
            url: 'flow_controller.php?task=execPurchaseTransfer',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(r){
                var data = JSON.parse(r);
                if(data.result != 'Success'){
                    alertApp('X', '이용권 양도가 불가합니다.');
                    return false;
                }
                console.log(data);               
                alert('양도가 완료되었습니다.');
                window.close();
                opener.WINDOW_RELOAD();
                return false;
            },
            error: function(e){
                alertApp('X', '다시 시도해주세요');
                return false;
            }
        });

    });

    // 양도 모달 끄기
    $('#giveVoucherFrm .closePopup')
    .add($('#dark_div'))
    .add($('#giveVoucherFrm .closePop'))
    .add($('#refundVoucherFrm .closePop'))
    .add($('#refundVoucherFrm .closePopup'))
    .click(function(){
        $('#giveVoucherFrm')
        .add($('#refundVoucherFrm'))
        .add($('#dark_div'))
        .add($('.mSearch_container'))
        .fadeOut(200);
    });

    // 옵션 수정하기 버튼클릭
    $('#optionSaveBtn').click(function(e){
        e.preventDefault();
        if($USER_GRADE < 3){
            if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 52) == -1){
                alertApp('X', '권한이 없습니다.');
                return false;
            }
        }

        const callback = (r) => {
            let res = JSON.parse(r);
            if(res.result == 'Fail') {
                alertApp('X', '결제 실패하였습니다.');
                return false;
            }
            alertApp('O','저장되었습니다.');
            return false;
        }

        useAjax('execMemoModify', callback, {
            UV_SQ: PAYMENT_INFO[0].UV_SQ,
            PAY_MEMO: $('#Payment-memo').val()
        });
        

    });

    $('#paymentSaveBtn').click(function(){
        
        if($USER_GRADE < 3){
            if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 52) == -1){
                alertApp('X', '권한이 없습니다.');
                return false;
            }
        }

        if(!CHECKED){alertApp('!','결제금액을 확인해주세요');return false;}
        if($('#cardPayment').val() == ''){$('#cardPayment').val('0');}
        if($('#cashPayment').val() == ''){$('#cashPayment').val('0');}
        if($('#accountPayment').val() == ''){$('#accountPayment').val('0');}

        if($('#cardPayment').val() == 0 && $('#cashPayment').val() == 0 && $('#accountPayment').val() == 0){
            alertApp('!', '결제금액을 입력해주세요.');
            return false;
        }
        
        var ask = confirm(
            '상품명 : ' + SELECTED_ITEM.NAME + 
            '\n상품금액 : ' + numberFormat(SELECTED_ITEM.PRICE) + 
            '원\n이용 시작일 : ' + $('#Payment-select2').text() + 
            '\n이용권 만료일 : ' + $('#Payment-select2_1').text() + 
            '\n---------------------------------\n결제금액 : ' + numberFormat(CALC.PAYMENT_PRICE) + 
            '원\n미수금액 : ' + numberFormat(CALC.YET_PRICE) + 
            '원\n결제하시겠습니까?'
        );
        if(ask){
            AJAX_SUBMIT_ADD_PAYMENT(
                PAYMENT_INFO[0].UV_SQ,
                PAYMENT_INFO[0].PAY_SQ,
                PAYMENT_INFO[0].MEMBER_SQ,
                CALC.YET_PRICE,
                CALC.CARD,
                CALC.CASH,
                CALC.ACCOUNT
            );
            return false;
        }else{
            return false;
        }

    });

    function AJAX_SUBMIT_ADD_PAYMENT(UV_SQ, PAY_SQ, MEMBER_SQ, SELLINGPRICE, PAYED_AMOUNT_CARD, PAYED_AMOUNT_CASH, PAYED_AMOUNT_BANK){
        var formData = new FormData();
            formData.append('UV_SQ', UV_SQ);
            formData.append('PAY_SQ', PAY_SQ);
            formData.append('MEMBER_SQ', MEMBER_SQ);
            formData.append('SELLINGPRICE', SELLINGPRICE);
            formData.append('PAYED_AMOUNT_CARD', PAYED_AMOUNT_CARD);
            formData.append('PAYED_AMOUNT_CASH', PAYED_AMOUNT_CASH);
            formData.append('PAYED_AMOUNT_BANK', PAYED_AMOUNT_BANK);

        $.ajax({
            url: 'flow_controller.php?task=execPurchaseModify',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(r){
                var data = JSON.parse(r);
                if(data.result == 'Fail'){
                    alertApp('X', '결제 실패! 다시 시도해주세요.');
                    return false;
                }
                alert('미수금이 결제되었습니다.');
                opener.WINDOW_RELOAD();
                close();
            },
            error: function(e){
                console.error(e);
            }
        })
    }

    refundData = {
        USE_COUNT: 0,
        TOTAL_COUNT: 0,
        ORIGINAL_PRICE: 0,
        DISCOUNT_PRICE: 0,
        PAYMENT_PRICE: 0,
        RESULT: function(){
            var calc = this.PAYMENT_PRICE - (this.USE_COUNT * (this.ORIGINAL_PRICE / this.TOTAL_COUNT)) - (this.PAYMENT_PRICE * 0.1);
            $('#autoCalc').parent().siblings().text(numberFormat(Math.round(calc / 10) * 10) + '원');
            return calc;
        }
    }

    // 환불하기 버튼클릭
    $('button.payment-refund-btn').click(function(){
        if($USER_GRADE < 3){
            if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 54) == -1){
                alertApp('X', '권한이 없습니다.');
                return false;
            }
        }

        var now = new Date();
            now.setHours(0);now.setMinutes(0);now.setSeconds(0);now.setMilliseconds(0);
        var startDt = new Date(PAYMENT_INFO[0].USE_STARTDATE);
        var endDt = new Date(PAYMENT_INFO[0].USE_LASTDATE);
        var calcDt = (now - startDt) / 1000 / 24 / 60 / 60;
        var allDt = (endDt - startDt) / 1000 / 24 / 60 / 60;

        $('#refundVoucherManager').add($('#classPay')).add($('#inputCalcPay')).add($('#classPay')).val('');
        $('#autoCalc').prop('checked', true);
        $('#inputCalcPay').prop('disabled', true);

        $('#refundVoucherFrm .refundType').text(
            PAYMENT_INFO[0].VOUCHER_TYPE_NAME
        );
        $('#refundVoucherFrm .refundName').text(
            PAYMENT_INFO[0].VOUCHER_NAME
        );
        $('#refundVoucherFrm .refundDate').text(
            PAYMENT_INFO[0].USE_STARTDATE.split(' ')[0] + ' ~ ' + 
            PAYMENT_INFO[0].USE_LASTDATE.split(' ')[0] + ' / 담당강사 : ' +
            TRAINER_LIST.filter(x => x.USER_SQ == PAYMENT_INFO[0].TRAINER_SQ)[0].USER_NM
        );
        $('#refundVoucherFrm .refundUse').text(
            '이용일수 ' + (calcDt < 0 ? 0 : calcDt) + '/' + allDt + '일 · ' + 
            '이용횟수 ' + (PAYMENT_INFO[0].USE_TYPE == 1 ? '무제한' : PAYMENT_INFO[0].USEDCOUNT + '/' + PAYMENT_INFO[0].COUNT + '회') + ' · ' +
            '예약횟수 ' + PAYMENT_INFO[0].RESERV_COUNT + '회'
        );

        refundData.USE_COUNT = PAYMENT_INFO[0].USE_TYPE == 1 ? (calcDt < 0 ? 0 : calcDt) : Number(PAYMENT_INFO[0].USEDCOUNT);
        refundData.TOTAL_COUNT = PAYMENT_INFO[0].USE_TYPE == 1 ? allDt : Number(PAYMENT_INFO[0].COUNT);
        refundData.ORIGINAL_PRICE = Number(PAYMENT_INFO[0].ORIGINAL_PRICE);
        refundData.DISCOUNT_PRICE = Number(PAYMENT_INFO[0].DISCOUNT_AMOUNT);
        refundData.PAYMENT_PRICE = Number(PAYMENT_INFO[0].PAYED_AMOUNT);
        refundData.RESULT();

        $('#refundVoucherFrm .left > p').find('span.ORIGINAL_PRICE').text(
            numberFormat(refundData.ORIGINAL_PRICE) + '원'
        );
        $('#refundVoucherFrm .left > p').find('span.DISCOUNT_PRICE').text(
            numberFormat(refundData.DISCOUNT_PRICE) + '원'
        );
        $('#refundVoucherFrm .left > p').find('span.PAYMENT_PRICE').text(
            numberFormat(refundData.PAYMENT_PRICE) + '원 (혜택가)'
        );
        $('#refundVoucherFrm .left > p').find('span.VOUCHER_TYPE').text(
            PAYMENT_INFO[0].USE_TYPE == 1 ? '기간제' : '횟수제'
        );
        
        $('#refundVoucherFrm').add($('#dark_div')).fadeIn(200);
    });

    // 환불하기 계산식    
    $('#inputCalcPay').keyup(function(){
        var val = $(this).val() == '-' ? 0 : Number($(this).val().replace(/\,/g,''));
        $(this).val(numberFormat($(this).val().replace(/\,/g,'')));
    });


    $('[name="calc"]').click(function(){
        var id = $(this).attr('id');
        $('#inputCalcPay').val('');
        if(id == 'inputCalc'){
            $('#inputCalcPay').prop('disabled', false).focus();
        }else{
            $('#inputCalcPay').prop('disabled', true);
        }
    });
    
    // 환불 submit
    $('#voucherRefundSubmit').click(function(){
        if($('#inputCalc').prop('checked') && $('#inputCalc').val() == ''){
            alertApp('!', '환불금을 적어주세요.');
            return false;
        }

        var UV_SQ = PAYMENT_INFO[0].UV_SQ;
        var PAY_SQ = PAYMENT_INFO[0].PAY_SQ;
        var MEMBER_SQ = PAYMENT_INFO[0].MEMBER_SQ;
        var REFUND_CASH = $('#autoCalc').prop('checked') ? 
                            refundData.RESULT() : 
                            $('#inputCalcPay').val().replace(/\,/g,'');
        
        var formData = new FormData();
            formData.append('UV_SQ', UV_SQ);
            formData.append('PAY_SQ', PAY_SQ);
            formData.append('MEMBER_SQ', MEMBER_SQ);
            formData.append('REFUND_CASH', REFUND_CASH * -1);

        $.ajax({
            url: 'flow_controller.php?task=execPurchaseRefund',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(r){
                var data = JSON.parse(r);
                if(data.result == 'Fail'){
                    alertApp('X', '다시 시도해주세요.');
                    return false;
                }

                alert('환불되었습니다.');
                opener.WINDOW_RELOAD();
                close();
            },
            error: function(e){
                console.log(e);
            }
        });
    });


    // 취소하기 버튼클릭
    $('article.conBtn > .payment-cancel-btn').click(()=>window.close());

    $('#cardPayment').add($('#cashPayment')).add($('#accountPayment')).add($('#Payment-sale-data-won')).focus(function(){
        var value = $(this).val();
        if(value == '0'){
            $(this).val('');
        }
        return false;
    });

    // 숫자로만 입력해야하는 유효성 검사
    $('#cardPayment').add($('#cashPayment')).add($('#accountPayment')).add($('#Payment-sale-data-won')).blur(function(){
        var value = $(this).val().search(/[a-z]/i);
        if(value > -1){
            alertApp('!','숫자로 입력해주세요.');
            $(this).val('').focus();
        }
        if($(this).val() == ''){
            $(this).val('0');
        }
    });
    $('#cardPayment').add($('#cashPayment')).add($('#accountPayment')).keyup(function(){
        // $('#cardPayment').val(Number($('#cardPayment').val()));
        // $('#cashPayment').val(Number($('#cashPayment').val()));
        // $('#accountPayment').val(Number($('#accountPayment').val()));
        $(this).val(numberFormat($(this).val().replace(/\,/g, '')));

        CALC.CARD = Number($('#cardPayment').val().replace(/\,/g, ''));
        CALC.CASH = Number($('#cashPayment').val().replace(/\,/g, ''));
        CALC.ACCOUNT = Number($('#accountPayment').val().replace(/\,/g, ''));
        
        INPUT_CALC_CHK();
        RESULT_CALC();
    });
    $('#cardPayment').add($('#cashPayment')).add($('#accountPayment')).keyup();

    function INPUT_CALC_CHK(){
        $('#totalPayment').parent().find('small').remove();
        if(CALC.CARD + CALC.CASH + CALC.ACCOUNT > CALC.DISCOUNT_PRICE){
            CHECKED = false;
            $('#totalPayment').before('<small>(결제금액이 지불할 금액보다 높습니다.)</small>');
            $('#totalPayment').siblings('small').css({
                'position' : 'absolute',
                'bottom' : '0px',
                'left' : '0px',
                'font-size' : '11px',
                'color' : 'red',
                'text-align' : 'center',
                'width' : '100%'
            });
        }else{
            CHECKED = true;
            $('#totalPayment').parent().find('small').remove();
        }
    }

    // 결제내역보기
    $('article.Pay.other form section.col3 > article.con1 > h5 > button').click(function(){
        var screen = $('article.Pay.other form section.col3 > article.con1 > div');
        $(this).toggleClass('active');
        var idx = $(this).attr('class') == 'active' ? 1 : 0;
        screen.eq(idx).show().siblings('div').hide();
    });

    // 회원찾기
    $('#giveVoucherFrm .findMemberBtn').click(function(){
        $('.mSearch_container').fadeIn(200);
    });
});



// 날짜 포멧변경 함수
function dateFormat(value){
    var date = new Date(value),
        year = date.getFullYear(),
        month = String(date.getMonth()+1).length == 1 ? '0' + String(date.getMonth()+1) : String(date.getMonth()+1),
        day = String(date.getDate()).length == 1 ? '0' + String(date.getDate()) : String(date.getDate());

    return year + '-' + month + '-' + day;
}

// 숫자 포멧변경 함수
function numberFormat(value) {
    return String(value).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
