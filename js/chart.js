var TRAINER_LIST;
var TOTAL_LIST;

// 통계 첫 DB데이터 GET
function GET_DATA(START_DT, END_DT, MANAGER_SQ, resolve){
    let form = new FormData();
        form.append('START_DT', START_DT);
        form.append('END_DT', END_DT);
        form.append('MANAGER_SQ', MANAGER_SQ);

    $.ajax({
        url: 'flow_controller.php?task=GetManagerPersonalSalaryList',
        data: form,
        method: 'POST',
        processData: false,
        contentType: false,
        success: function(r){
            let data = JSON.parse(r);
            TOTAL_LIST = [...data];
            VIEW_LIST(TOTAL_LIST);
            resolve();
        }
    });
}

// 트레이너 리스트
function GET_TRAINER_LIST() {
    $.ajax({
        url: 'flow_controller.php?task=getManagerList',
        method: 'POST',
        processData: false,
        contentType: false,
        success: function(r){
            let data = r.split('|');
            let trainer = JSON.parse(data[0]);
            TRAINER_LIST = [...trainer];
            VIEW_TRAINER_LIST(trainer);
            
            $('#sortingBtn').click();
        }
    });
}

function VIEW_TRAINER_LIST(list){
    var i = 0, tag = '';

    list.forEach(data => {
        tag += `<option value="${data.USER_SQ}">${data.USER_NM}</option>`
    });

    $('#trainerSorting').html('<option value="0">강사선택</option>').append(tag);
    
}

function VIEW_LIST(list) {
    $('#List > table > tbody').empty();

    for(let i of list){
        $('#List > table > tbody').append(
            `<tr>
                <td>${i.RESERV_DT}</td>
                <td>${i.USER_NM}</td>
                <td>${i.START_TIME} ~ ${i.END_TIME}</td>
                <td>${i.VOUCHER_NAME}</td>
                <td>${i.MANAGER_NAME}</td>
                <td>${i.ATTENDANCE_TYPE_NAME}</td>
                <td>${numberFormat(i.ALLOWANCE)}원</td>
            </tr>`
        );
    }
}

// 3자리 콤마
function numberFormat(n) {
    return String(n).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}



$(function(){

    setTimeout(() => {
        if($USER_GRADE < 3){
            if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 41) == -1){
                if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 42) == -1){
                    history.back();
                    alert('권한이 없습니다.');
                    return false;
                }
                alert('개인레슨 통계 페이지의 권한이 없습니다.');
                location.href = 'chart_group.php';
                return false;
            }
        }
    },500);

    //// 함수 실행 영역 ////
    // totalCalc();
    ////


    //// 변수 영역 ////
    var $date = new Date();
    var dateEz = $('#dateSortingEz');
    var dateChoice1 = $('#dateSorting1');
    var dateChoice2 = $('#dateSorting2');
    var searchBtn = $('#sortingBtn');
    var searchText = $('#searchList');
    var list = $('#List tbody');
    var allChk = $('#listAllChk');
    var listChk = list.find('input');
    var thead = $('div.view').siblings('table');


    //// 이벤트 영역 ////
    dateEz.change(inputDate).val('lastday7').change();

    GET_TRAINER_LIST();
    

    // 솔팅검색버튼
    searchBtn.click(() => { 
        var date = new Date();
        var sortingChk = $('#sortingChk');

        var tSorting = $('#trainerSorting').val();

        var dateSorting1 = $('#dateSorting1').val(),
            dateSorting2 = $('#dateSorting2').val();
        
        sortingChk.html('<h4>검색조건 : </h4>');

        if(tSorting != 0 || dateSorting1 != '' || dateSorting2 != ''){
            
            if(tSorting != 0){
                apd(TRAINER_LIST.filter(e => e.USER_SQ == tSorting)[0].USER_NM);
                trainerCho(dateSorting1, dateSorting2, tSorting);
            }else{
                trainerChoDefault(dateSorting1, dateSorting2, tSorting);
            }
            
            if(dateSorting1 == dateSet(date) && dateSorting2 == dateSet(date)){
                apd('오늘');
            }else{
                apd(dateSorting1 + '부터 ' + dateSorting2 + '까지');
            }

            function apd(data){
                sortingChk.append('<span>' + data + '</span>');
            }

            function trainerCho(START, END, TRAINER){
                var promise = () => {
                    return new Promise((resolve, reject) => {
                        GET_DATA(START, END, TRAINER, resolve);
                    });
                }

                var asyncFn = async () => {
                    var awaitFn = await promise();

                    $('article.calcEl0 span').attr('data-count', TOTAL_LIST.length);
                    var TOTAL_PAY = 0;
                    for(let i of TOTAL_LIST){
                        TOTAL_PAY += Number(i.ALLOWANCE);
                    };
                    $('article.calcEl1 span').attr('data-pay', TOTAL_PAY).text(numberFormat(TOTAL_PAY));

                    var el1 = $('.calcEl1'), el2 = $('.calcEl2');

                    el1.find('.default').hide();
                    el1.find('.tCalc').show();
                    var average = Number(TOTAL_PAY) == 0 ? 0 : (Number(TOTAL_PAY) / TOTAL_LIST.length);
                    $('p.tCalc > input').val(numberFormat(average));
                    $('article.calcEl2 span').attr('data-pay', average * TOTAL_LIST.length).text(numberFormat(average * TOTAL_LIST.length));

                    var countAll = $('article.calcEl0 span').attr('data-count');
                    $('article.calcEl0 h3').html('전체 건수<i class="fas fa-user-check"></i>');
                    $('article.calcEl0 span').text(numberFormat(countAll));
                    $('button.tCalc').click();
                };
                asyncFn();
            }
            function trainerChoDefault(START, END, TRAINER){
                var promise = () => {
                    return new Promise((resolve, reject) => {
                        GET_DATA(START, END, TRAINER, resolve);
                    });
                }

                var asyncFn = async () => {
                    var awaitFn = await promise();

                    $('article.calcEl0 span').attr('data-count', TOTAL_LIST.length);
                    var TOTAL_PAY = 0;
                    for(let i of TOTAL_LIST){
                        TOTAL_PAY += Number(i.ALLOWANCE);
                    };
                    $('article.calcEl1 span').attr('data-pay', TOTAL_PAY).text(numberFormat(TOTAL_PAY));

                    var el1 = $('.calcEl1'), el2 = $('.calcEl2');

                    el1.find('.default').show();
                    el1.find('.tCalc').hide();
                    $('article.calcEl2').fadeOut(300);
                    $('.chartCard > p.equals').css('width','0px');
                    $('p.tCalc > input').val('');
                    $('article.calcEl2 span').attr('data-pay','0').text('0');

                    var countAll = $('article.calcEl0 span').attr('data-count');
                    $('article.calcEl0 h3').html('전체 건수<i class="fas fa-user-check"></i>');
                    $('article.calcEl0 span').text(numberFormat(countAll));
                    $('article.calcEl0 .progressBar').css('width','100%');
                };
                asyncFn();
            }
        }

    });

    // 개인레슨 수당 계산버튼
    $('button.tCalc').click(function(){
        var val = $('p.tCalc > input').val();
        if(val != ''){
            $('.chartCard > p.equals').css('width','70px');
            $('article.calcEl2').fadeIn(500);
            var calc = Number(val.replace(/\,/g, '')) * $('article.calcEl0 span').attr('data-count');
            $('article.calcEl2 span').attr('data-pay',calc);
            $('article.calcEl2 span').attr('data-tax',calc*0.9);
            var pay = $('article.calcEl2 span').attr('data-pay');
            $('article.calcEl2 span').text(numberFormat(pay));
        }else{
            $('p.tCalc > input').prop('placeholder','수당을 입력해주세요.').focus();
            $('article.calcEl2 span').text('0');
        }
    });

    // VAT포함 체크박스
    $('.chartCard > article.calcEl2 div.con > div > input').click(function(){
        var result = $(this).prop('checked');
        var pay = $('article.calcEl2 span').attr('data-pay');
        var tax = $('article.calcEl2 span').attr('data-tax');

        if(result){
            $(this).parent().siblings('p').find('span').text(numberFormat(pay));
        }else{
            $(this).parent().siblings('p').find('span').text(numberFormat(tax));
        }
    })

    // 검색창 입력
    searchText.keyup(function(){
        var text = $(this).val().toLowerCase();

        list.find('tr').filter(function(){
            $(this).toggle($(this).text().toLowerCase().indexOf(text) > -1)
        });
    });

    // 전체 선택 (체크박스)
    allChk.click(allchk);
    // 리스트 선택 (체크박스)
    listChk.click(listchk);

    // 리스트 스크롤
    $('article.container').scroll(function(){
        var nowScr = $(this).scrollTop();
        var ListW = $('#List table').outerWidth();
        if(nowScr >= 222){
            thead.css({
                'position' : 'fixed',
                'width' : ListW,
                'top' : '165px',
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
    })




    // Default값


    //// 함수 영역 ////

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

    // 매출(카드형태) data가 해당 태그의 텍스트로 삽입
    function progressText(){
        var elPay = $('div.con > p > span');
        var elCount = $('div.con > div.count > span');
        for(var num in elPay){
            var data = elPay.eq(num).attr('data-pay');
            elPay.eq(num).text(numberFormat(data));
        }
        var dataCount = elCount.attr('data-count');
        elCount.text(numberFormat(dataCount));
    }

    // 총 매출에 따른 카드,현금,이체,미수금 % 계산
    function totalCalc(){
        var elPay = $('div.con > p > span');
        var elTotalPay = elPay.eq(0);
        var elCount = $('div.con > div.count > span');
        var TotalPay = elTotalPay.attr('data-pay');
        var elCount = $('div.con > div.count > span');
        var dataCount = elCount.attr('data-count');
        elCount.text(numberFormat(dataCount));

        elPay.each(function(){
            $(this).parent().siblings('.progressBarBorder').find('p').css({
                'width' : ($(this).attr('data-pay') / TotalPay * 100) + '%'
            });
        });


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

    // 체크 관련 함수
    function allchk(){
        var bool = allChk.prop('checked');
        
        bool ? listChk.prop('checked',true) : listChk.prop('checked',false);
    }
    function listchk(){
        var arr = [];
        listChk.each(function(){
            var bool = $(this).prop('checked');
            bool ? arr.push(bool) : arr = [] ;
            arr.length == listChk.length ? allChk.prop('checked',true) : allChk.prop('checked',false);
        });
    }

});