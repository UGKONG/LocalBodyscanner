var TODAY_DATE = new Date();
var SET = {};
var ITEM_DATA = {};
var MEMBER_LIST = [];
var TRAINER_LIST = [];

var SELECTED_ITEM = {};
var SELECTED_MEMBER_SQ = 0;
var SELECTED_TRAINER_SQ = 0;
var SELECTED_TRAINER_SQ1 = 0;

var CALC = {};

var CHECKED = false;

function AJAX_DATA(itemSq){
    let formData = new FormData();
    formData.append('VOUCHER_SQ',itemSq);
    $.ajax({
        url: "flow_controller.php?task=getVoucherInfo",
        method: "POST",
        data: formData,
		contentType: false,
        processData: false,
		success: function(result){
            var data = result.split('|');
            if(data.result)return false;
            ITEM_DATA = JSON.parse(data[0]);
            SET.VOUCHER_TYPE = JSON.parse(data[3]);
            SET.USE_TYPE = JSON.parse(data[4]);
            SET.PERIOD_TYPE = JSON.parse(data[5]);
            SET.PERIOD_UNIT = JSON.parse(data[6]);
            SET.COUNT_TYPE = JSON.parse(data[7]);
            SET.SURTAX_TYPE = JSON.parse(data[8]);
            SET.DISCOUNT_TYPE = JSON.parse(data[9]);
            MEMBER_LIST = JSON.parse(data[10]);
            TRAINER_LIST = JSON.parse(data[11]);
            MAKE_OPTION_LIST(SET.DISCOUNT_TYPE);
            MAKE_ITEM_DATA(ITEM_DATA[0]);
            MAKE_MEMBER_LIST(MEMBER_LIST);
            MAKE_TRAINER_LIST(TRAINER_LIST);

            $('#paymentMember').val('회원명 : ' + sessionStorage.buyMemberName);
            $('#Receipt_Name').text(sessionStorage.buyMemberName);
            SELECTED_MEMBER_SQ = sessionStorage.buyMemberSeq;
            $('#uNum').text(
                MEMBER_LIST.filter(e => e.USER_SQ == SELECTED_MEMBER_SQ)[0].PHONE_NO
            );

            $('#Payment-select2').val(dateFormat(TODAY_DATE)).change();
		},
		error: function (e){
			console.log(e);
		}
    });
}

function MAKE_OPTION_LIST(list){
    $('#Payment-sale-ez').empty();

    for(let i in list){
        var tag = '<option value="' + list[i].CODE + '">' + list[i].DESCRIPTION + '</option>';
        $('#Payment-sale-ez').append(tag);
    }

}

function PAYMENT_PAGE_RESET(){
    $('#paymentMember').val('');
    $('#uNum').text('')
    $('#Payment-sale-data-won').val('');
    $('#Payment-select2').val('');
    $('#Payment-select2_1').val('');
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
    PAYMENT_PAGE_RESET();

    SELECTED_ITEM.SQ = obj.VOUCHER_SQ;
    SELECTED_ITEM.ATTR = obj.USE_TYPE;
    SELECTED_ITEM.NAME = obj.VOUCHER_NAME;
    SELECTED_ITEM.PRICE = Number(obj.PRICE);
    // 기간/횟수
    SELECTED_ITEM.PERIOD_TYPE = obj.PERIOD_TYPE;
    SELECTED_ITEM.PERIOD_UNIT = obj.PERIOD_UNIT;
    SELECTED_ITEM.PERIOD = obj.PERIOD;
    SELECTED_ITEM.COUNT_TYPE = obj.COUNT_TYPE;
    SELECTED_ITEM.COUNT = obj.COUNT;
    SELECTED_ITEM.DISCOUNT_AMOUNT = Number(obj.DISCOUNT_AMOUNT);
    SELECTED_ITEM.DISCOUNT_TYPE = obj.DISCOUNT_TYPE;
    SELECTED_ITEM.DISCOUNT_RATIO = Number(obj.DISCOUNT_RATIO);

    CALC.SALE = obj.DISCOUNT_AMOUNT;
    CALC.CARD = 0;
    CALC.CASH = 0;
    CALC.ACCOUNT = 0;
    CALC.DISCOUNT_PRICE = Number(SELECTED_ITEM.PRICE) - Number(CALC.SALE);
    CALC.YET_PRICE = 0;
    CALC.PAYMENT_PRICE = 0;
    CALC.END_DATE = '0000-00-00';

    // 이용가능 횟수 계산
    CALC.COUNT = (function(attr,count){
        switch(attr){
            case '1' :  // 기간제
                return '무제한';

            case '2' :  // 횟수제
                return count + '회';
        }
    })(
        SELECTED_ITEM.ATTR,
        SELECTED_ITEM.COUNT
    );

    $('#Payment-sale-ez').val(SELECTED_ITEM.DISCOUNT_TYPE);
    $('#Payment-sale-data-won').val(
        SELECTED_ITEM.DISCOUNT_TYPE == 1 ? 
        SELECTED_ITEM.DISCOUNT_RATIO : 
        SELECTED_ITEM.DISCOUNT_AMOUNT
    ).keyup();
    $('#Payment-sale-ez').change();
    $('#PaymentItemName').add($('#Receipt_TicketName')).text(SELECTED_ITEM.NAME);
    $('#Receipt_TicketPrice').text(numberFormat(SELECTED_ITEM.PRICE));
    $('#startPay').text(numberFormat(SELECTED_ITEM.PRICE));
    $('#Payment').text(numberFormat(SELECTED_ITEM.PRICE) + '원');
    $('#Payment-select3').val(CALC.COUNT);
    RESULT_CALC();
}

function MAKE_MEMBER_LIST(list){
    $('.mSearch_container > ul.name_list').empty();

    for(let i in list){
        if(list[i].BIRTH_DT == null || list[i].BIRTH_DT == undefined || list[i].BIRTH_DT == ''){
            var age = '나이 정보없음';
        }else{
            var age = birth_year(list[i].BIRTH_DT);
        }
        
        var tag = '<li data-seq="' + list[i].USER_SQ + '">' + list[i].USER_NM + 
            '<div><p>' + age + '</p><p>' + list[i].PHONE_NO + '</p></div>\
            </li>';
        $('.mSearch_container > ul.name_list').append(tag);
    }

        
    $('.mSearch_container > ul.name_list > li').click(function(){
        var seq = $(this).attr('data-seq');
        
        var selectedMember = (MEMBER_LIST.filter(e => e.USER_SQ == SELECTED_MEMBER_SQ))[0];
        $('#paymentMember').val(selectedMember.USER_NM);
        $('.mSearch_container .x_btn').click();
        $('#Receipt_Name').text(selectedMember.USER_NM);
        $('#uNum').text(selectedMember.PHONE_NO)
    });

}

function MAKE_TRAINER_LIST(list){
    $('#Payment-select1').add($('#Payment-select1_1')).html('<option value="">선택</option>');

    for(let i in list){
        var tag = list[i].USER_SQ == SESSION_SQ ? 
            '<option selected value="' + list[i].USER_SQ + '">' + list[i].USER_NM + '</option>' : 
            '<option value="' + list[i].USER_SQ + '">' + list[i].USER_NM + '</option>' ;
            
        $('#Payment-select1').append(tag);
        $('#Payment-select1_1').append(tag);
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
    });

    $('#Payment-select1_1').change(function(){
        SELECTED_TRAINER_SQ1 = $(this).val();
        if(SELECTED_TRAINER_SQ1 == ''){
            $('#Receipt_Teacher_1').text('미선택');
        }else{
            $('#Receipt_Teacher_1').text(
                (TRAINER_LIST.filter(e => e.USER_SQ == SELECTED_TRAINER_SQ1))[0].USER_NM
            );
        }
    });

    $('#Payment-select1').change();
    $('#Payment-select1_1').change();
}

function RESULT_CALC(){
    CALC.DISCOUNT_PRICE = Number(SELECTED_ITEM.PRICE) - Number(SELECTED_ITEM.DISCOUNT_AMOUNT);
    CALC.PAYMENT_PRICE = Number(CALC.CARD) + Number(CALC.CASH) + Number(CALC.ACCOUNT);
    CALC.YET_PRICE = CALC.DISCOUNT_PRICE - CALC.PAYMENT_PRICE >= 0 ? CALC.DISCOUNT_PRICE - CALC.PAYMENT_PRICE : 0;
    // console.log(CALC.DISCOUNT_PRICE);
    // console.log(CALC.PAYMENT_PRICE);
    // console.log(CALC.YET_PRICE);

    RESULT_PRINT();
}
function RESULT_PRINT(){
    // $('#startPay').text(numberFormat(CALC.DISCOUNT_PRICE));
    $('#salePayment').text(numberFormat(SELECTED_ITEM.DISCOUNT_AMOUNT));
    $('#yetPayment').text(numberFormat(CALC.YET_PRICE));
    $('#totalPayment').text(numberFormat(CALC.PAYMENT_PRICE));

    // 영수증
    $('#Receipt_Sale1').text(numberFormat(SELECTED_ITEM.DISCOUNT_AMOUNT));
    $('#Receipt_Sale2').text(numberFormat(SELECTED_ITEM.PRICE - SELECTED_ITEM.DISCOUNT_AMOUNT));
    $('#Receipt_CardPay').text(numberFormat(CALC.CARD));
    $('#Receipt_CashPay').text(numberFormat(CALC.CASH));
    $('#Receipt_AccountPay').text(numberFormat(CALC.ACCOUNT));
    $('#Receipt_Payment').text(numberFormat(CALC.PAYMENT_PRICE));
    $('#Receipt_YetPay').text(numberFormat(CALC.YET_PRICE));

}

$(function(){

    AJAX_DATA(ITEM_SEQ);
    RESULT_CALC();
    

    // 할인선택
    $('#Payment-sale-ez').change(function(){
        var val = $(this).val();
        
        var inputVal = $('#Payment-sale-data-won').val().replace(/\,/g, '') != '' ? $('#Payment-sale-data-won').val().replace(/\,/g, '') : 0;
        var result = 0;

        switch (val) {
            case '1':
                if($('#Payment-sale-data-won').val().replace(/\,/g, '') > 100){
                    $('#Payment-sale-data-won').val('100');
                }else if($('#Payment-sale-data-won').val().replace(/\,/g, '') < 0){
                    $('#Payment-sale-data-won').val('0');
                }
                var inputVal = $('#Payment-sale-data-won').val().replace(/\,/g, '') == '' ? 0 : $('#Payment-sale-data-won').val().replace(/\,/g, '') >= 100 ? 100 : $('#Payment-sale-data-won').val().replace(/\,/g, '');
                $('#item_sale_text').text('%');
                result = Math.floor(SELECTED_ITEM.PRICE * (inputVal/100));
                break;
        
            case '2':
                $('#item_sale_text').text('원');
                result = inputVal;
                break;
        }
        SELECTED_ITEM.DISCOUNT_AMOUNT = result;
        RESULT_CALC();
    });

    // 할인입력
    $('#Payment-sale-data-won').keyup(function(){
        var val = $(this).val().replace(/\,/g, '');
        $(this).val(numberFormat(val));
        var saleType = $('#Payment-sale-ez').val();
        
        if(saleType == '1'){
            if($(this).val().replace(/\,/g, '') > 100){
                $(this).val('100');
            }else if($(this).val().replace(/\,/g, '') < 0){
                $(this).val('0');
            }
        }else{
            if($(this).val().replace(/\,/g, '') > SELECTED_ITEM.PRICE){
                $(this).val(numberFormat(SELECTED_ITEM.PRICE));
            }else if($(this).val().replace(/\,/g, '') < 0){
                $(this).val('0');
            }
            SELECTED_ITEM.DISCOUNT_AMOUNT = val;
        }
        $('#Payment-sale-ez').change();
        RESULT_CALC();
    });

    // 이용 시작일 선택
    $('#Payment-select2').change(function(){
        var START_DATE = $(this).val();
        var START_DATE = new Date(START_DATE);
        var tempDate = new Date(START_DATE);
        
        // 이용만료일 계산
        CALC.END_DATE = (function(attr,periodType,unit,period){
            switch(attr){

                case 1 :  // 기간제

                    if(unit == 1){    // 일
                        tempDate = new Date(START_DATE);
                        tempDate.setDate(tempDate.getDate() + period);
                        return dateFormat(tempDate);
                        
                    }else{              // 개월
                        tempDate = new Date(START_DATE);
                        return MONTH_CALC(tempDate,period);
                    }

                case 2 :  // 횟수제

                    if(periodType == 1){// 무제한
                        return '2099-01-01';
                    }else{// 기간지정

                        if(unit == 1){// 일
                            tempDate = new Date(START_DATE);
                            tempDate.setDate(tempDate.getDate() + period);
                            return dateFormat(tempDate);
                        }else{// 개월
                            tempDate = new Date(START_DATE);
                            return MONTH_CALC(tempDate,period);
                        }

                    }

            }
        })(
            Number(SELECTED_ITEM.ATTR),
            Number(SELECTED_ITEM.PERIOD_TYPE),
            Number(SELECTED_ITEM.PERIOD_UNIT),
            Number(SELECTED_ITEM.PERIOD)
        );

        $('#Payment-select2_1').val(dateFormat(tempDate));
        $('#Receipt_StartDate').text($(this).val());
        $('#Receipt_EndDate').text(dateFormat(tempDate));
    });
    

    function MONTH_CALC(date,month){
        date.setMonth(date.getMonth() + month);
        date.setDate(date.getDate() - 1);
        var y = date.getFullYear();
        var m = date.getMonth() + month; m = (m < 10) ? '0' + m : m;
        var d = date.getDate(); d = (d < 10) ? '0' + d : d;

        return y + '-' + m + '-' + d;
    }

    // 결제메모
    $('#Payment-memo').blur(function(){
        var text = $(this).val();
        $('#Receipt_Memo').text(text);
    });

    // 회원검색
    // $('#paymentMember').click(()=>{
    //     $('#mSearchText').val('').keyup();
    //     $('.mSearch_container').fadeIn(100);
    // });

    function AJAX_ITEM_PAYMENT(){
        let formData = new FormData();
        var VOUCHER_SQ = ITEM_DATA[0].VOUCHER_SQ;
        var MEMBER_SQ = SELECTED_MEMBER_SQ;
        var DISCOUNT_TYPE = $('#Payment-sale-ez').val();
        var DISCOUNT_RATIO = $('#Payment-sale-ez').val() == 1 ? $('#Payment-sale-data-won').val() : 0;
        var DISCOUNT_AMOUNT = SELECTED_ITEM.DISCOUNT_AMOUNT;
        var SELLINGPRICE = CALC.DISCOUNT_PRICE;
        var SELLER_SQ = SELECTED_TRAINER_SQ;
        var TRAINER_SQ = SELECTED_TRAINER_SQ1;
        var PAYED_AMOUNT_CARD = CALC.CARD;
        var PAYED_AMOUNT_CASH = CALC.CASH;
        var PAYED_AMOUNT_BANK = CALC.ACCOUNT;
        var USE_STARTDATE = $('#Payment-select2').val();
        var USE_LASTDATE = $('#Payment-select2_1').val();
        var PAY_MEMO = $('#Payment-memo').val();

        formData.append('VOUCHER_SQ',VOUCHER_SQ);
        formData.append('MEMBER_SQ',MEMBER_SQ);
        formData.append('DISCOUNT_TYPE',DISCOUNT_TYPE);
        formData.append('DISCOUNT_RATIO',DISCOUNT_RATIO);
        formData.append('DISCOUNT_AMOUNT',DISCOUNT_AMOUNT);
        formData.append('SELLINGPRICE',SELLINGPRICE);
        formData.append('SELLER_SQ',SELLER_SQ);
        formData.append('TRAINER_SQ',TRAINER_SQ);
        formData.append('PAYED_AMOUNT_CARD',PAYED_AMOUNT_CARD);
        formData.append('PAYED_AMOUNT_CASH',PAYED_AMOUNT_CASH);
        formData.append('PAYED_AMOUNT_BANK',PAYED_AMOUNT_BANK);
        formData.append('USE_STARTDATE',USE_STARTDATE);
        formData.append('USE_LASTDATE',USE_LASTDATE);
        formData.append('PAY_MEMO',PAY_MEMO);

        $.ajax({
            url: "flow_controller.php?task=execPuchaseCreate",
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(result){
                var result = JSON.parse(result);
                if(result.result == "Success"){
                    alert('결제가 완료되었습니다.');
                }else{
                    alert('결제를 실패하였습니다.');
                }
                window.close();
                return false;
            },
            error: function(){

            }

        });
    }
    
    // 결제하기 버튼클릭
    $('button.payment-submit-btn').click(function(){

        if(!CHECKED){alert('결제금액을 확인해주세요');return false;}
        if($('#paymentMember').val() == ''){alert('회원정보를 확인해주세요');return false;}
        if($('#Payment-select1').val() == ''){alert('결제 담당자를 선택해주세요');return false;}
        if($('#Payment-select1_1').val() == ''){alert('담당강사를 선택해주세요');return false;}
        if($('#Payment-select2').val() == ''){alert('이용 시작일을 선택해주세요.');return false;}
        if($('#Payment-select2').val() == ''){alert('이용 시작일을 선택해주세요.');return false;}
        if($('#Payment-select2').val() == ''){alert('이용 시작일을 선택해주세요.');return false;}
        if($('#cardPayment').val() == ''){$('#cardPayment').val('0');}
        if($('#cashPayment').val() == ''){$('#cashPayment').val('0');}
        if($('#accountPayment').val() == ''){$('#accountPayment').val('0');}
        if($('#Payment-sale-data-won').val() == ''){$('#Payment-sale-data-won').val('0');}

        var confirmAsk = confirm('상품명 : ' + SELECTED_ITEM.NAME + '\n상품금액 : ' + SELECTED_ITEM.PRICE + '원\n이용 시작일 : ' + $('#Payment-select2').val() + '\n이용권 만료일 : ' + $('#Payment-select2_1').val() + '\n---------------------------------\n결제금액 : ' + CALC.PAYMENT_PRICE + '원\n미수금액 : ' + CALC.YET_PRICE + '원\n결제하시겠습니까?');
        if(confirmAsk){
            AJAX_ITEM_PAYMENT();
            return false;
        }else{
            return false;
        }
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
        // $(this).val(numberFormat($(this).val().replace(/\,/g,'')));
        var value = $(this).val().search(/[a-z]/i);
        if(value > -1){
            alert('숫자로 입력해주세요.');
            $(this).val('').focus();
        }
        if($(this).val() == ''){
            $(this).val('0');
        }
    });

    $('#cardPayment').add($('#cashPayment')).add($('#accountPayment')).keyup(function(){
        var val = $(this).val().replace(/\,/g, '');
        $(this).attr('data-number', val);
        $(this).val(numberFormat(val));

        // $('#cardPayment').val(Number($('#cardPayment').attr('data-number') ? $('#cardPayment').attr('data-number') : 0));
        // $('#cashPayment').val(Number($('#cashPayment').attr('data-number') ? $('#cashPayment').attr('data-number') : 0));
        // $('#accountPayment').val(Number($('#accountPayment').attr('data-number') ? $('#accountPayment').attr('data-number') : 0));

        CALC.CARD = Number($('#cardPayment').attr('data-number'));
        CALC.CASH = Number($('#cashPayment').attr('data-number'));
        CALC.ACCOUNT = Number($('#accountPayment').attr('data-number'));
        
        INPUT_CALC_CHK();
        RESULT_CALC();
    });
    $('#cardPayment').add($('#cashPayment')).add($('#accountPayment')).keyup();


    function INPUT_CALC_CHK(){
        $('#totalPayment').parent().find('small').remove();
        if(CALC.CARD + CALC.CASH + CALC.ACCOUNT > CALC.DISCOUNT_PRICE){
            CHECKED = false;
            $('#totalPayment').before('<small>(결제금액이 상품가격보다 높습니다.)</small>');
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