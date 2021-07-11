var PAYMENT_LIST = []; 
var PAYMENT_DETAIL_LIST = [];
var MEMBER_LIST = [];
var TRAINER_LIST = [];
var SET = {};

var FILTER = {};


function AJAX_DATA(START_DT, END_DT, resolve){

    let formData = new FormData();
    formData.append('START_DT',START_DT);
    formData.append('END_DT',END_DT);
    $.ajax({
        url: "flow_controller.php?task=getUserVoucherList",
        method: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(result){
            var result = result.split('|');

            PAYMENT_LIST = JSON.parse(result[0]);
            PAYMENT_DETAIL_LIST = JSON.parse(result[1]);
            MEMBER_LIST = JSON.parse(result[2]);
            TRAINER_LIST = JSON.parse(result[3]).filter(e => e.ISUSE == 1);
            SET.USE_STATUS = JSON.parse(result[4]);
            SET.PAY_STATUS = JSON.parse(result[5]);
            SET.FUND_TYPE = JSON.parse(result[6]);
            SET.PAY_TYPE = JSON.parse(result[7]);

            MAKE_OPTION_LIST(TRAINER_LIST, SET.PAY_TYPE,SET.PAY_STATUS);
            
            if(resolve != undefined) {
                resolve();
            }
        },
        error: function(e){
            console.log(e)
        }
    })
}

// 강사/결제수단 리스트 제작 함수
function MAKE_OPTION_LIST(trainerList,payType,payStatus){

    var tag = '';
    $('#trainerSorting').html('<option value="">모든강사</option>');
    for(let i in trainerList){
        tag += '<option value="' + trainerList[i].USER_SQ + '">' + trainerList[i].USER_NM + '</option>';
    }
    $('#trainerSorting').append(tag);

    var tag = '';
    $('#payHowSorting').html('<option value="">모든결제수단</option>');
    for(let i in payType){
        tag += '<option value="' + payType[i].CODE + '">' + payType[i].DESCRIPTION + '</option>';
    }
    $('#payHowSorting').append(tag);

    var tag = '';
    $('#yetPayment_or_No').html('<option value="">전체내역</option>');
    for(let i in payStatus){
        tag += '<option value="' + payStatus[i].CODE + '">' + payStatus[i].DESCRIPTION + '</option>';
    }
    $('#yetPayment_or_No').append(tag);

    
    $('#memberSorting').val(FILTER.MEMBER);
    $('#trainerSorting').val(FILTER.TRAINER);
    $('#payHowSorting').val(FILTER.PAY_TYPE);
    $('#yetPayment_or_No').val(FILTER.YET_LIST);

}

// 리스트 제작 함수
function MAKE_PAYMENT_LIST(list){
    
    var tag = '';
    $('#List tbody').empty();

    for(let i in list){
        var SQ = list[i].PAY_SQ;
        var UV_SQ = list[i].UV_SQ;
        var DATE = list[i].MODIFIEDDT.split(' ')[0];
        var NAME = list[i].USER_NM;
        var PHONE = list[i].PHONE_NO;
        var VOUCHER_NAME = list[i].VOUCHER_NAME;
        var STATUS = list[i].USE_STATUS == 0 ? '?' : list[i].USE_STATUS == 1 ? '이용전' : list[i].USE_STATUS == 2 ? '이용중' : '이용완료';
        var ORIGINAL_PRICE = list[i].ORIGINAL_PRICE;
        var DISCOUNT_AMOUNT = list[i].DISCOUNT_AMOUNT;
        var SELLING_PRICE = list[i].SELLINGPRICE;
        var PAY_STATUS = list[i].PAY_STATUS;
        var PAY_STATUS_NAME = PAY_STATUS == 0 ? '?' : PAY_STATUS == 1 ? '부분결제' : PAY_STATUS == 2 ? '결제완료' : PAY_STATUS == 3 ? '환불' : '양도';
        var PAYED_AMOUNT = list[i].PAYED_AMOUNT;
        var YET_PRICE = list[i].SELLINGPRICE - list[i].PAYED_AMOUNT;
        var TRAINER = (TRAINER_LIST.filter(e => e.USER_SQ == list[i].SELLER_SQ)).length != 0 ?
                      (TRAINER_LIST.filter(e => e.USER_SQ == list[i].SELLER_SQ))[0].USER_NM : '정보없음';
        var PAY_MEMO = list[i].PAY_MEMO != '' ? '<button class="memo">메모보기</button>' : '';

        tag = 
            '<tr data-sq="' + SQ + '" data-uv-seq="' + UV_SQ + '" style="background-color:' + (PAY_STATUS == 3 ? '#ffcccc50' : PAY_STATUS == 4 ? '#ccffcc70' : '') + '">' + 
                '<td>' + DATE + '</td>' +
                '<td>' + NAME + '</td>' +
                '<td>' + PHONE + '</td>' +
                '<td>' + VOUCHER_NAME + '</td>' +
                '<td>' + STATUS + '</td>' +
                '<td>' + numberFormat(ORIGINAL_PRICE) + '원</td>' +
                '<td>' + numberFormat(DISCOUNT_AMOUNT) + '원</td>' +
                '<td>' + numberFormat(SELLING_PRICE) + '원</td>' + 
                '<td><span style="color:' + (PAY_STATUS == 3 ? 'gray' : 'black') + '">' + PAY_STATUS_NAME + '</span></td>' +
                '<td class="fundType1_1">-</td>' +
                '<td class="fundType1_2">-</td>' +
                '<td>' + 
                    (numberFormat(YET_PRICE) == '0' ? 
                        '-' : 
                        (PAY_STATUS == 3 ? '<s style="color:gray">' : '') 
                        + (numberFormat(YET_PRICE)) + '원') + 
                    (PAY_STATUS == 3 ? '</s>' : '') + 
                '</td>' +
                '<td class="fundType2_1">-</td>' +
                '<td class="fundType2_2">-</td>' +
                '<td>' + TRAINER + '</td>' +
                '<td>' + PAY_MEMO + '</td>' +
            '</tr>';

        $('#List tbody').append(tag);
    }


    $('#List table').find('td').each(function(){
        $(this).attr('title', $(this).text());
    });

    // 리스트 클릭
    $('#List table').find('td').not('td:last-of-type').not('td:nth-of-type(12)').click(function(){
        
        if($USER_GRADE < 3){
            if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 51) == -1){
                alertApp('X', '권한이 없습니다.');
                return false;
            }
        }

        var seq = $(this).parent().attr('data-uv-seq');
        var option = 'top=100, left=100, width=1200, height=714, menubar=no, toolbar=no, resizable=no, scrollbars=no, status=no';
        window.open('payment_info.php?seq=' + seq,'결제창',option);
    });

    // 메모보기 버튼
    $('#List table').find('button.memo').click(function(){
        var PAY_SQ = $(this).parent().parent().attr('data-sq');
        var data = (PAYMENT_LIST.filter(e => e.PAY_SQ == PAY_SQ))[0];
        var STATUS = data.USE_STATUS == '1' ? '이용전' : data.USE_STATUS == '2' ? '이용중' : '이용완료';

        $('.dark_div').add($('#popMemo')).fadeIn(200);
        $('#popMemo').find('.memoDate > span').text(data.MODIFIEDDT.split(' ')[0]);
        $('#popMemo').find('.memoMemberName > span').text(data.USER_NM);
        $('#popMemo').find('.memoPhone > span').text(data.PHONE_NO);
        $('#popMemo').find('.memoTeacher > span').text(function() {
            let obj = TRAINER_LIST.filter(e => e.USER_SQ == data.SELLER_SQ);
            if(obj.length == 0) {
                return '정보없음';
            }else {
                return obj[0].USER_NM;
            }
        });
        $('#popMemo').find('.memoItemName > span').text(data.VOUCHER_NAME);
        $('#popMemo').find('.memoState > span').text(STATUS);
        $('#popMemo').find('.memoAmount > span').text(numberFormat(data.ORIGINAL_PRICE) + '원');
        $('#popMemo').find('.memoSaleAmount > span').text(numberFormat(data.DISCOUNT_AMOUNT) + '원');
        $('#popMemo').find('.memoAfterAmount > span').text(numberFormat(data.SELLINGPRICE) + '원');
        $('#popMemo').find('.memoMemo > p').text(data.PAY_MEMO);

    });

    MAKE_PAYMENT_DETAIL_LIST(PAYMENT_DETAIL_LIST);
    TOTAL_CALC(list);

}

function TOTAL_CALC(list){
    var ORIGINAL_PRICE = 0;
    var SELL_AMOUNT = 0;
    var PAY_AMOUNT = 0;
    var YET_AMOUNT = 0;
    var REFUND_AMOUNT = 0;
    var TRUE_AMOUNT = 0;
    var temp = 0;

    for(let i in list){

        // 양도 상태이면 바로 스킾
        if(list[i].PAY_STATUS == 4){
            continue;
        }

        ORIGINAL_PRICE += Number(list[i].ORIGINAL_PRICE);
        SELL_AMOUNT += Number(list[i].SELLINGPRICE);
        PAY_AMOUNT += Number(list[i].PAYED_AMOUNT);
        temp += Number(list[i].PAYED_AMOUNT) + Number(list[i].REFUND_AMOUNT);
        
        if(list[i].PAY_STATUS != 3){
            YET_AMOUNT += (Number(list[i].SELLINGPRICE) - Number(list[i].PAYED_AMOUNT));
        }
        
        REFUND_AMOUNT += Number(list[i].REFUND_AMOUNT) * -1;
    }
    // TRUE_AMOUNT = SELL_AMOUNT - YET_AMOUNT - REFUND_AMOUNT;
    TRUE_AMOUNT = PAY_AMOUNT - REFUND_AMOUNT;

    TOTAL_CALC_PRINT(ORIGINAL_PRICE,SELL_AMOUNT,PAY_AMOUNT,YET_AMOUNT,REFUND_AMOUNT,temp);
}

function TOTAL_CALC_PRINT(o,s,p,y,r,t){
    $('#Total-pay span').attr('data-pay',o).text(numberFormat(o));
    $('#allTotal-pay span').attr('data-pay',s).text(numberFormat(s));
    $('#Total-yet span').attr('data-pay',y).text(numberFormat(y));
    $('#Total-re span').attr('data-pay',r).text(numberFormat(r));
    $('#Total-true span').attr('data-pay',t).text(numberFormat(t));
}

function MAKE_PAYMENT_DETAIL_LIST(list){
    $('.fundType1_1 , .fundType1_2, .fundType2_1, .fundType2_2').empty();
    for(let i in list){
        
        var PAY_SQ = list[i].PAY_SQ;
        var FUND_TYPE = list[i].FUND_TYPE;
        var PAY_TYPE = list[i].PAY_TYPE;
        var PAY_AMOUNT = list[i].PAY_AMOUNT;

        if(FUND_TYPE == 1){
            if(PAY_TYPE == 1){  // 카드
                $('#List tbody > tr[data-sq="' + PAY_SQ + '"] .fundType1_1').append('카드<br>');
            }else if(PAY_TYPE == 2){    // 현금
                $('#List tbody > tr[data-sq="' + PAY_SQ + '"] .fundType1_1').append('현금<br>');
            }else{      // 이체
                $('#List tbody > tr[data-sq="' + PAY_SQ + '"] .fundType1_1').append('이체<br>');
            }
            $('#List tbody > tr[data-sq="' + PAY_SQ + '"] .fundType1_2').append(numberFormat(list[i].PAY_AMOUNT) + '원' + '<br>');
            
        }else if(FUND_TYPE == 2){
            if(PAY_TYPE == 1){  // 카드
                $('#List tbody > tr[data-sq="' + PAY_SQ + '"] .fundType2_1').append('카드<br>');
            }else if(PAY_TYPE == 2){    // 현금
                $('#List tbody > tr[data-sq="' + PAY_SQ + '"] .fundType2_1').append('현금<br>');
            }else{      // 이체
                $('#List tbody > tr[data-sq="' + PAY_SQ + '"] .fundType2_1').append('이체<br>');
            }
            $('#List tbody > tr[data-sq="' + PAY_SQ + '"] .fundType2_2').append(numberFormat(list[i].PAY_AMOUNT * -1) + '원' + '<br>');
        }
    }
}




$(function(){
    //// 변수 영역 ////
    var $date = new Date();
    var dateEz = $('#dateSortingEz');
    var dateChoice1 = $('#dateSorting1');
    var dateChoice2 = $('#dateSorting2');
    var searchBtn = $('#sortingBtn');

    var container = $('article.container');
    var list = $('#List table');
    var thead = $('div.view').siblings('table');

    var popup = $('#popMemo');
    var popupClose = popup.find('button.closeBtn');
    var popupCloseBtn = popup.find('div.button > button')

    //// 함수 이벤트 영역 ////
    dateEz.change(inputDate).change();

    var promiseFn = () => {
        return new Promise((resolve, reject) => {
            AJAX_DATA($('#dateSorting1').val(), $('#dateSorting2').val(), resolve);
        });
    }
    var asyncFn = async () => {
        await promiseFn();
        searchBtn.click();
    }
    asyncFn();

    // 솔팅검색버튼
    searchBtn.click(() => {

        FILTER.MEMBER = $('#memberSorting').val();
        FILTER.TRAINER = $('#trainerSorting').val();
        FILTER.PAY_TYPE = $('#payHowSorting').val();
        FILTER.YET_LIST = $('#yetPayment_or_No').val();

        AJAX_DATA($('#dateSorting1').val(), $('#dateSorting2').val());

        var date = new Date();
        var sortingChk = $('#sortingChk');
        var mSorting = $('#memberSorting').val(),
            tSorting = $('#trainerSorting').val(),
            pSorting = $('#payHowSorting').val(),
            lSorting = $('#yetPayment_or_No').val();
        var dateSorting1 = $('#dateSorting1').val(),
            dateSorting2 = $('#dateSorting2').val();
        var SEARCH_LIST = [];
        SEARCH_LIST = PAYMENT_LIST;


        sortingChk.html('<h4>검색조건 : </h4>');

        if(mSorting != '' || tSorting != '' || pSorting != '' || lSorting != '' || dateSorting1 != '' || dateSorting2 != ''){
            
            if(mSorting != ''){
                SEARCH_LIST = (SEARCH_LIST.filter(e => e.USER_NM.indexOf(mSorting) > -1));
                apd('검색어 : ' + mSorting);
            }
            
            if(tSorting != ''){
                SEARCH_LIST = SEARCH_LIST.filter(e => e.SELLER_SQ == tSorting);
                let temp = (TRAINER_LIST.filter(e => e.USER_SQ == tSorting))[0].USER_NM;
                apd(temp);
            }

            if(pSorting != ''){
                
                var a = PAYMENT_DETAIL_LIST.filter(e => e.PAY_TYPE == pSorting);
                SEARCH_LIST = SEARCH_LIST.filter((e) => {
                    let temp = a.filter(ev => ev.PAY_SQ == e.PAY_SQ);
                    return temp.length == 0 ? false : true;
                });


                let temp = '';
                switch (pSorting) {
                    case '':
                        temp = '모든결제수단';
                        break;
                    case '1':
                        temp = '카드결제';
                        break;
                    case '2':
                        temp = '현금결제';
                        break;
                    case '3':
                        temp = '계좌이체';
                        break;
                }
                apd(temp);
            }

            if(lSorting != ''){
                SEARCH_LIST = SEARCH_LIST.filter(e => e.PAY_STATUS == lSorting);

                let temp = '';
                if(lSorting == ''){
                    temp = '모든매출 조회';
                }else{
                    temp = SET.PAY_STATUS.filter(e => e.CODE == lSorting)[0].DESCRIPTION
                }
                
                apd(temp);
            }

            if(dateSorting1 == dateSet(date) && dateSorting2 == dateSet(date)){
                apd('오늘');
            }else{
                apd(dateSorting1 + '부터 ' + dateSorting2 + '까지');
            }


            MAKE_PAYMENT_LIST(SEARCH_LIST);

            function apd(data){
                sortingChk.append('<span>' + data + '</span>')
            }

        }

    });

    

    // 메모보기 팝업닫기
    popupClose.add(popupCloseBtn).click(() => {
        $('.dark_div').add(popup).fadeOut(200); 
    })

    
    // 리스트 스크롤
    container.scroll(function(){
        var nowScr = $(this).scrollTop();
        var ListW = list.outerWidth();
        if(nowScr >= 222){
            thead.css({
                'position' : 'fixed',
                'width' : ListW,
                'top' : '166px',
                'left' : '20px'
            });
        }else{
            thead.css({'position':'unset'});
        }
    });

    // 브라우저 사이즈 조절
    $(window).resize(function(){
        var ListW = $('#List table').outerWidth();
        thead.css({'width' : ListW})
    });


    
    // 날짜 쉽게 기입하는 드롭박스
    function inputDate(){
        var data = $(this).val();
        switch (data) {
            case 'today':           // 오늘
                $date = new Date();
                $date.setDate($date.getDate());
                dateChoice1.add(dateChoice2).val(dateSet($date));
                break;
            case 'yesterday':       // 어제
                $date = new Date();
                $date.setDate($date.getDate()-1);
                dateChoice1.add(dateChoice2).val(dateSet($date));
                break;
            case 'lastday7':        // 7일전
                $date = new Date();
                $date.setDate($date.getDate()-7);
                dateChoice1.val(dateSet($date));
                $date = new Date();
                dateChoice2.val(dateSet($date));
                break;
            case 'lastday30':       // 30일전
                $date = new Date();
                $date.setDate($date.getDate()-30);
                dateChoice1.val(dateSet($date));
                $date = new Date();
                dateChoice2.val(dateSet($date));
                break;
            case 'thismonth':       // 30일전
                $date = new Date();
                $date.setDate(1);
                dateChoice1.val(dateSet($date));
                $date = new Date();
                dateChoice2.val(dateSet($date));
                break;
            case 'lastmonth':
                $date = new Date();
                $date.setDate(1);
                $date.setMonth($date.getMonth()-1);
                dateChoice1.val(dateSet($date));
                $date = new Date();
                $date.setDate(1);
                $date.setDate($date.getDate()-1);
                dateChoice2.val(dateSet($date));
                break;
            case 'thisyear':
                $date = new Date();
                $date.setMonth(0);
                $date.setDate(1);
                dateChoice1.val(dateSet($date));
                $date = new Date();
                dateChoice2.val(dateSet($date));
                break;
            case 'lastyear':
                $date = new Date();
                $date.setFullYear($date.getFullYear()-1);
                $date.setMonth(0);
                $date.setDate(1);
                dateChoice1.val(dateSet($date));
                $date = new Date();
                $date.setMonth(0);
                $date.setDate(1);
                $date.setDate($date.getDate()-1);
                dateChoice2.val(dateSet($date));
                break;
        }
    }


    // 날짜 문자열로 바꿔서 YYYY-MM-DD 형식으로
    function dateSet(date){
        dateY = String(date.getFullYear()),
        dateM = String(date.getMonth()+1).length == 1 ? '0' + String(date.getMonth()+1) : String(date.getMonth()+1),
        dateD = String(date.getDate()).length == 1 ? '0' + String(date.getDate()) : String(date.getDate()),
        dateh = String(date.getHours()).length == 1 ? '0' + String(date.getHours()) : String(date.getHours()),
        datem = String(date.getMinutes()).length == 1 ? '0' + String(date.getMinutes()) : String(date.getMinutes());
        return dateY + '-' + dateM + '-' + dateD;
    }


});


// 3자리 콤마 함수
function numberFormat(n) {
    return String(n).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function WINDOW_RELOAD(){
    location.reload();
}