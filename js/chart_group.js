$LIST = [];
$TRAINER_LIST = [];

function GET_DATA(MANAGER_SQ, START_DT, END_DT){
    fetch(`flow_controller.php?task=GetManagerGroupSalaryList&MANAGER_SQ=${MANAGER_SQ}&START_DT=${START_DT}&END_DT=${END_DT}`)
        .then((res) => {return res.json()})
        .then((data) => {$LIST = data; return $LIST})
        .then(() => VIEW_LIST())
        .catch((err) => console.error('Error :', err));
}

function getTrainerList(){
    fetch('flow_controller.php?task=getManagerList')
        .then((res) => res.text())
        .then((data) => {
            let sliceData = data.split('|')[0];
            $TRAINER_LIST = JSON.parse(sliceData).filter(e => e.WORKSTATUS == 1);
            $TRAINER_LIST = $TRAINER_LIST.filter(e => e.ISUSE == 1);
            $('#trainerSorting').html('<option value="">강사선택</option>').append(function(){
                var tag = '';
                for(let i of $TRAINER_LIST) {
                    tag += `<option value="${i.USER_SQ}">${i.USER_NM}</option>`;
                }
                return tag;
            });
            sortingClick();
        });
}

function VIEW_LIST () {
    $('#List tbody').empty();
    var count = 0;
    for (let i in $LIST) {
        $('#List tbody').append(
            `<tr data-class-seq="${$LIST[i].CLASS_SQ}">
                <td>${Number(i)+1}</td>
                <td>${$LIST[i].CLASS_DT.split(' ')[0]}</td>
                <td>${$LIST[i].MANAGER_NM}</td>
                <td>${$LIST[i].CLASS_NAME}</td>
                <td>${$LIST[i].MEMBER_COUNT}명<button data-class-name="${$LIST[i].CLASS_NAME}">리스트보기</button></td>
                <td>${numberFormat($LIST[i].ALLOWANCE) + '원'}</td>
                <td title="${$LIST[i].MEMO}"></td>
            </tr>`
        )

        count += Number($LIST[i].MEMBER_COUNT);
    }

    $('#checkin').find('span').attr('data-count-all', $LIST.length);
    $('#checkin').find('span').text($LIST.length);
    $('#checkin').find('.count').text('출석인원 : ' + count + '명');
    $('#card').find('span').attr('data-count-all', count);
    $('#card').find('span').text(count);

    // 회원리스트 보기
    $('#List > table td > button').click(function(){
        var classSeq = $(this).parents('tr').attr('data-class-seq');
        var className = $(this).attr('data-class-name');
        
        const url = 'flow_controller.php?task=getClassReservedUserList';
        const data = {CLASS_SQ: classSeq};
        const callback = (data) => {
            let sliceData = data.split('|')[0];
            let jsonData = JSON.parse(sliceData);
            VIEW_CLASS_MEMBER(jsonData, className);
            $('.dark_div').add($('div.modal')).fadeIn(200);
        }
        useAjax(url, data, callback);

    })
}

// 해당 수업의 회원리스트
function VIEW_CLASS_MEMBER(list, CLASS_NM) {
    const countEl = $('div.modal > .con > .lengthView > article > p')
    countEl.eq(0).html(list.length + '명');
    countEl.eq(1).html(list.filter(e => e.RESERV_STATUS == 3).length + '명');
    countEl.eq(2).html(list.filter(e => e.RESERV_STATUS == 4).length + '명');
    
    $('div.modal table tbody').empty();
    if(list.length == 0){
        $('div.modal table tbody').html(
            `<tr><td colspan="4">리스트가 없습니다.</td></tr>`
        )
        return false;
    }
    for(let i of list){
        $('div.modal table tbody').append(
            `<tr 
                data-class-reserv-sq="${i.CLASS_RESERV_SQ}"
                data-class-sq="${i.CLASS_SQ}"
                data-uv-sq="${i.UV_SQ}"
            >
                <td><span data-user-sq="${i.USER_SQ}" title="상세정보 페이지로 이동">${i.USER_NM}</span></td>
                <td>${i.PHONE_NO}</td>
                <td>${CLASS_NM}</td>
                <td>${i.RESERV_STATUS_NAME}</td>
            </tr>`
        )
    }
    $('div.modal table td > span').click(function(){
        var SQ = $(this).attr('data-user-sq');
        location.href = 'member_info.php?u_seq=' + SQ;
    })
}

// 솔팅버튼 클릭
function sortingClick(){ 
    var date = new Date();
    var sortingChk = $('#sortingChk');
    var tSorting = $('#trainerSorting').val();
    var dateSorting1 = $('#dateSorting1').val(),
        dateSorting2 = $('#dateSorting2').val();
    
    sortingChk.html('<h4>검색조건 : </h4>');

    if(tSorting != ''){
        var tName = $TRAINER_LIST.filter(e => e.USER_SQ == tSorting)[0].USER_NM;
        apd(tName);
        trainerCho();
    }else{
        trainerChoDefault();
    }

    if(dateSorting1 == dateFormat(date) && dateSorting2 == dateFormat(date)){
        apd('오늘');
    }else{
        apd(dateSorting1 + '부터 ' + dateSorting2 + '까지');
    }

    function apd(data){
        sortingChk.append('<span>' + data + '</span>')
    }

    function trainerCho(){
        var el0 = $('.calcEl0'),
            el1 = $('.calcEl1'),
            el2 = $('.calcEl2');

        el1.find('.default').hide();
        el1.find('.tCalc').show();
        // el1.find('h3.tCalc').text('그룹레슨 수당');
        el0.find('p.count').fadeIn(200);

        var countInfo = el0.find('p.count').attr('data-count') + '명/' + el0.find('p.count').attr('data-count-all') + '명'
        el0.find('p.count').text(countInfo);

        $('p.tCalc > input').val('');
        $('article.calcEl2 span').attr('data-pay','0').text('0');
        var countAll1 = el0.find('span').attr('data-count-all');
        var count1 = el0.find('span').attr('data-count');
        el0.find('span').text(numberFormat(count1) + '/' + numberFormat(countAll1));
        
        var countAll2 = el1.find('span').attr('data-count-all');
        var count2 = el1.find('span').attr('data-count');
        el1.find('span').text(numberFormat(count2) + '/' + numberFormat(countAll2));

        var percent1 = (count1 / countAll1) * 100;
        var percent2 = (count2 / countAll2) * 100;
        // el0.find('.progressBar').css('width',percent1+'%');
        // el1.find('.progressBar').css('width',percent2+'%');
    }

    function trainerChoDefault(){
        var el0 = $('.calcEl0'),
            el1 = $('.calcEl1'),
            el2 = $('.calcEl2');

        el1.find('.default').show();
        el1.find('.tCalc').hide();
        // el1.find('h3.tCalc').text('그룹레슨 수당');
        el0.find('p.count').fadeOut(200);
        
        $('.chartCard > p.equals').css('width','0px');
        $('p.tCalc > input').val('');
        $('article.calcEl2 span').attr('data-pay','0').text('0');
        $('article.calcEl2').fadeOut(300);

        var countAll1 = el0.find('span').attr('data-count-all');
        var count1 = el0.find('span').attr('data-count');
        var countAll2 = el1.find('span').attr('data-count-all');
        var count2 = el1.find('span').attr('data-count');
        el0.find('span').text(numberFormat(countAll1));
        el1.find('span').text(numberFormat(countAll2));
        el0.find('.progressBar').css('width','100%');
        el1.find('.progressBar').css('width','100%');
    }

    GET_DATA(tSorting, dateSorting1, dateSorting2);
}



// 3자리 콤마
function numberFormat(n) {
    return String(n).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}



$(function(){
    
    getTrainerList();

    //// 변수 영역 ////
    var $date = new Date();
    var dateEz = $('#dateSortingEz');
    var dateChoice1 = $('#dateSorting1');
    var dateChoice2 = $('#dateSorting2');
    var searchBtn = $('#sortingBtn');
    var searchText = $('#searchList');
    var list = $('#List tbody');
    var thead = $('div.view').siblings('table');

    //// 함수 실행 영역 ////
    setTimeout(() => {
        if($USER_GRADE < 3){
            if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 42) == -1){
                alert('권한이 없습니다.');
                history.back();
                return false;
            }
        }
    },500);
    
    
    
    //// 이벤트 영역 ////
    dateEz.change(inputDate);
    dateEz.val('thismonth').change();
    // sortingClick();
    searchBtn.click(sortingClick);  // 검색버튼 클릭


    // 개인레슨 수당 계산버튼
    $('button.tCalc').click(function(){
        var val = $('p.tCalc > input').val();
        if(val != ''){
            $('.chartCard > p.equals').css('width','70px');
            $('article.calcEl2').fadeIn(500);
            var calc = Number(val) * $('article.calcEl0 span').attr('data-count-all');
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
    });



    // 모달창 관련
    $('div.modal > button').add($('div.modal > h3 > button')).click(function(){
        $('.dark_div').add($('div.modal')).fadeOut(200);
    });



    // Default값
    for(var i = 0 ; i < list.find('tr').length; i++){
        var listMemo = list.find('tr').eq(i).find('td:last-of-type').attr('title');
        list.find('tr').eq(i).find('td:last-of-type').text(listMemo);
    }

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

    // 작업용
    // $('#List > table td > button').click();


});