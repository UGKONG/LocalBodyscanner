function make_DOM(){
    for(var i in trainerList){
        var tSeq = trainerList[i].sequence;
        var tName = trainerList[i].name;
        $('#Payment-select1').append('<option value="' + tSeq + '">' + tName + '</option>')
    }

    var saleSel = '';
    for(var i = 0; i <= 100; i += 5){
        $('#Payment-sale-ez').append('<option value="' + i + '">' + i + ' %' + '</option>');
    }
}

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


$(function(){

    // 회원검색
    var seachMember = $('.mSearch_container');
    // 돈관련
    var payOption_1 = $('#Payment-select1'),
        payOption_2 = $('#Payment-select2'),
        payOption_3 = $('#Payment-select2_1'),
        payOption_4 = $('#Payment-select3'),
        payOption_5 = $('#Payment-money'),
        payOption_6_ez = $('#Payment-sale-ez'),
        payOption_6 = $('#Payment-sale-data-won');
    var paying_amount = $('#startPay'),
        paying_card = $('#cardPayment'),
        paying_cash = $('#cashPayment');
        paying_account = $('#accountPayment'),
        paying_sale = $('#salePayment'),
        paying_yet = $('#yetPayment'),
        paying_paymentAmount = $('#totalPayment');
    var receipt_ticketName = $('#Receipt_TicketName'),
        receipt_itemAmount = $('#Receipt_TicketPrice'),
        receipt_sale = $('#Receipt_Sale1'),
        receipt_saleAmount = $('#Receipt_Sale2'),
        receipt_card = $('#Receipt_CardPay'),
        receipt_cash = $('#Receipt_CashPay'),
        receipt_account = $('#Receipt_AccountPay'),
        receipt_payMoney = $('#Receipt_Payment'),
        receipt_payMoneyYet = $('#Receipt_YetPay'),
        receipt_teacher = $('#Receipt_Teacher'),
        receipt_startDate = $('#Receipt_StartDate'),
        receipt_endDate = $('#Receipt_EndDate'),
        receipt_payDate = $('#Receipt_PaymentDate'),
        receipt_memo = $('#Receipt_Memo');
    
    var paymentListView = $('article.Pay.other form section.col3 > article.con1 > h5 > button');

    // 함수 실행 //
    ajaxData();

    
    seachMember.find('li').click(function(){
        var seq = $(this).attr('data-seq');
        var name = memberList[memberList.findIndex(e => e.Sequence == seq)].Name;
        $('#paymentMember').val(name);
    });


    // 결제 담당자 선택
    payOption_1.change(function(){
        if($(this).val() == ''){
            $('#Receipt_Teacher').text('미선택');
        }else{
            var val = $(this).val();
            var idx = trainerList.findIndex(e => e.sequence == val);
            $('#Receipt_Teacher').text(trainerList[idx].name);
        }
    });


    // 할인쉽게 선택하기
    payOption_6_ez.change(function(){
        var val = $(this).val();
        if (val == ''){
            var calc = '';
        }else{
            var calc = Math.floor(($calc.beforeAmount * (val / 100)) / 10) * 10;
        }
        payOption_6.val(calc);
        $calc.saleAmount = Number(calc) == '' ? 0 : Number(calc);
        math();
        printMath();
    });

    // 할인액 직접입력
    payOption_6.keyup(function(){
        payOption_6_ez.val('');
        var val = $(this).val();
        
        if(Number(val) >= Number($calc.beforeAmount)){
            $(this).val($calc.beforeAmount);
            $calc.saleAmount = $calc.beforeAmount;
        }else{
            $calc.saleAmount = Number(val);
        }

        
        math();
        printMath();
    });

    // 결제금액 입력하기
        paying_card.keyup(function(){
            var val = $(this).val();
            $calc.cardPay = val;
            math();
            printMath();
        });
        paying_cash.keyup(function(){
            var val = $(this).val();
            $calc.cashPay = val;
            math();
            printMath();
        });
        paying_account.keyup(function(){
            var val = $(this).val();
            $calc.accountPay = val;
            math();
            printMath();
        });
    //

    // 결제메모
    $('#Payment-memo').blur(function(){
        var text = $(this).val();
        $('#Receipt_Memo').text('- '+text);
    });


    // 결제내역보기 버튼
    paymentListView.click(function(){
        var screen = $('article.Pay.other form section.col3 > article.con1 > div');
        $(this).toggleClass('active');
        var idx = $(this).attr('class') == 'active' ? 1 : 0;
        screen.eq(idx).show().siblings('div').hide();
    });



    // 수정하기 버튼클릭
    $('button.payment-submit-btn').click(function(){
        var confirmAsk = confirm('결제하시겠습니까?');
        if(!confirmAsk){
            return false;
        }
    });

    // 환불하기 버튼클릭
    $('button.payment-refund-btn').click(function(){
        var confirmAsk = confirm('환불하시겠습니까?');
        if(!confirmAsk){
            return false;
        }
    });
    
    // 취소하기 버튼클릭
    $('article.conBtn > .payment-cancel-btn').click(()=>window.close());

    // 숫자로만 입력해야하는 유효성 검사
    paying_card.add(paying_cash).add(paying_account).blur(function(){
        var value = $(this).val().search(/[a-z]/i);
        if(value > -1){
            alert('숫자로 입력해주세요.');
            $(this).val('').focus();
        }
    });

///////////////////////////////////////////////////////////////////////////////////


    // 함수 영역

    // 결제 금액 입력하기 함수 정의
    function printMath(){
        
        paying_amount.text(numberFormat($calc.afterAmount));        // 상품가격 (할인된 금액)
        paying_paymentAmount.text(numberFormat($calc.paymentAmount));
        paying_sale.text(numberFormat($calc.saleAmount));
        paying_yet.text(numberFormat($calc.yetAmount));


        receipt_ticketName.text();
        receipt_itemAmount.text();
        receipt_sale.text(numberFormat($calc.saleAmount));
        receipt_saleAmount.text(numberFormat($calc.afterAmount));
        receipt_card.text(numberFormat($calc.cardPay));
        receipt_cash.text(numberFormat($calc.cashPay));
        receipt_account.text(numberFormat($calc.accountPay));
        receipt_payMoney.text(numberFormat($calc.paymentAmount));
        receipt_payMoneyYet.text(numberFormat($calc.yetAmount));
        receipt_teacher.text();
        receipt_startDate.text();
        receipt_endDate.text();
        receipt_payDate.text();
        receipt_memo.text();
    }
    
    // 기본 데이터
    function defaultData(){

        $calc = {
            lastPayment : 200000,               // 이전 결제 금액
            beforeAmount : $itemData.pay,       // 판매가
            saleAmount : 0,     // 할인가
            cardPay : 0,        // 카드결제가
            cashPay : 0,        // 현금결제가
            accountPay : 0,     // 이체결제가
            afterAmount : 0,       // 할인적용후 판매가,
            paymentAmount : 0,
            yetAmount : 0,     // 미수금
        }

        $('#PaymentItemName').add(receipt_ticketName).text($itemData.name);                 // 상품 이름
        $('#Payment').text(numberFormat($itemData.pay)+'원');    // 상품 금액
        $('#Payment-select3').val($itemData.count == 0 ? '무제한' : $itemData.count + '회');        // 이용가능 횟수
        $('#Receipt_TicketPrice').text(numberFormat($calc.beforeAmount));
        $('#startPay').add($('#Payment-money_')).add($('#Receipt_Sale2')).add($('#yetPayment')).text(numberFormat(Number($calc.beforeAmount) - Number($calc.lastPayment)));
        $('#Payment-money').add($('#Receipt_lastPayment')).text(numberFormat($calc.lastPayment));
        $('#Receipt_PaymentDate').text(dateFormat(new Date()));
    }



    // Ajax 구문이 들어갈 함수
    function ajaxData(){
        
        $itemData = {
            seq : '0005',
            attr : '0',
            date : '6',
            count : '0',
            type : '1',
            category : '0003',
            name : 'PT 6개월',
            dayUseCount : '0',
            weekUseCount : '0',
            taxItem : '0',
            amount : '700000',
            saleAttr : '0',
            saleData : '0',
            pay : '700000'
        }


        // success
        make_DOM();
        defaultData();
    }

    // 계산
    function math(){
        $calc.afterAmount = Number($calc.beforeAmount) - Number($calc.lastPayment) - Number($calc.saleAmount);
        $calc.paymentAmount = Number($calc.cardPay) + Number($calc.cashPay) + Number($calc.accountPay);
        $calc.yetAmount = (Number($calc.afterAmount) - Number($calc.paymentAmount)) >= 0 ? Number($calc.afterAmount) - Number($calc.paymentAmount) : 0;
    }



    // 임시
    $('article.Pay.other form section.col3 > article.con1 > h5 > select').val(2); //2차 결제정보
    paymentListView.click();
});
