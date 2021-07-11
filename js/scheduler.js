var o = 0;
var $i = 0;
var NEW_FORM = false;
var PERIOD_OBJ = {};
var MEMBER_LIST = [];
var TRAINER_LIST = [];
var TRAINER_NAME_LIST = [];
var FREE_VOUCHER_LIST = [];
var RESERVATION_SETTING_LIST = [];
var RESERVATION_STATUS_LIST = [];
var IMPORT_CLASS_LIST = [];
var IMPORT_CLASS_LIST_WEEK = [];
var HAVE_VOUCHER = [];
var PAGE_COUNT = 1;
var SELECTED_DT = '';
var EDIT_SCHEDULE = {};

// session대신 글로벌 변수
var selectedView = undefined;
var selectedDate = undefined;
var selectedTrainer = undefined;

// 스케줄 출석처리
function SCH_O(RESERV_SQ, UV_SQ){
    let formData = new FormData();
    formData.append('RESERV_SQ', RESERV_SQ);
    formData.append('UV_SQ', UV_SQ);

    $.ajax({
        url: "flow_controller.php?task=execUserPersonalScheduleAttend",
        method: "POST",
        data: formData,
        contentType: false,
        processData: false,
        data: formData,
        success: function(result){
            alertApp('O','해당 스케줄이 출석으로 처리되었습니다.');
            GET_AJAX_DATA();
        }
    });
}

// 스케줄 결석처리
function SCH_X(RESERV_SQ, UV_SQ){
    let formData = new FormData();
    formData.append('RESERV_SQ', RESERV_SQ);
    formData.append('UV_SQ', UV_SQ);

    $.ajax({
        url: "flow_controller.php?task=execUserPersonalScheduleAbsence",
        method: "POST",
        data: formData,
        contentType: false,
        processData: false,
        data: formData,
        success: function(result){
            alertApp('O','해당 스케줄이 결석으로 처리되었습니다.');
            GET_AJAX_DATA();
        }
    });
}
// 스케줄 취소처리
function SCH_C(RESERV_SQ, UV_SQ){
    let formData = new FormData();
    formData.append('RESERV_SQ', RESERV_SQ);
    formData.append('UV_SQ', UV_SQ);

    $.ajax({
        url: "flow_controller.php?task=execUserPersonalScheduleCancel",
        method: "POST",
        data: formData,
        contentType: false,
        processData: false,
        data: formData,
        success: function(result){
            alertApp('O','해당 스케줄이 취소되었습니다');
            GET_AJAX_DATA();
        }
    });
}
// 초기 데이터 GET_AJAX
function GET_AJAX_DATA(){

	$.ajax({
		url: "flow_controller.php?task=getScheduleInitInfo",
		method: "POST",
        // data: dateData,
		contentType: false,
		processData: false,
		success: function (result) {
			var data = result.split('|');
			MEMBER_LIST = JSON.parse(data[0]);
			TRAINER_LIST = JSON.parse(data[1]);
			FREE_VOUCHER_LIST = JSON.parse(data[2]);
			RESERVATION_SETTING_LIST = JSON.parse(data[3])[0];
			RESERVATION_STATUS_LIST = JSON.parse(data[4]);

            MAKE_MEMBER_LIST(MEMBER_LIST);
            MAKE_TRAINER_LIST(TRAINER_LIST.filter(e => e.ISUSE == 1));
            MAKE_FREE_VOUCHER(FREE_VOUCHER_LIST);
            MAKE_SCHEDULE_LIST(TRAINER_NAME_LIST,SCHEDULE_LIST);
            LAST_SESSION();
            if(selectedView == undefined){
                $('.topleft button').eq(1).click();
            }else{
                $('.topleft button').eq(selectedView).click();
            }
            $('#pop_div1, .dark_div').fadeOut(200);
		},
		error: function (e) {
            console.log(e);
		}
	});
}
// 무료이용권 리턴 함수
function MAKE_FREE_VOUCHER(list){
    var tag = '';
    
    for(let i of list){
        tag += '<option value="' + i.VOUCHER_SQ + '">' + i.VOUCHER_NAME + '</option>';
    }

    return tag;
}
function ADD_SOLO_CLASS_RESET(){
    $('#solo_searchName').add($('#solo_PhoneN')).add($('#ticketChoice')).add($('#jqs-memo')).val('');
    $('.solo_Pop > div.input > div.col4 > p').html('<span>이용권을 선택해주세요.</span>');
}


// 개인수업 예약 모달창
function ADD_SOLO_CLASS(a,b,c,d){


    var dateChk = function(){
        var nowDate = new Date();
        var nowTime = String(nowDate.getHours()) + String(nowDate.getMinutes()) + String(nowDate.getSeconds());

        if($('section.content > div.top_sMenu > .topleft button.active').index() == 1){
            var date = $('#date').text();
                date = date.split(' ');
                date = parseInt(date[0]) + '-' + parseInt(date[1]) + '-' + parseInt(date[2]);
                date = new Date(date);
        }else{
            var date = SELECTED_DATE.split(' ')[0];
                date = new Date(date);
        }
        var setH = d.split(' ~ ')[0].split(':')[0];
        var setM = d.split(' ~ ')[0].split(':')[1];
        date.setHours(setH);
        date.setMinutes(setM);
        console.log(d);
        if(RESERVATION_SETTING_LIST.PSN_RESERV_TYPE == 1){
            var setH = d.split(' ~ ')[0].split(':')[0];
            var setM = d.split(' ~ ')[0].split(':')[1];
            date.setHours(setH);
            date.setMinutes(setM);
            nowDate.setMinutes(nowDate.getMinutes() + Number(RESERVATION_SETTING_LIST.PSN_RESERV_TIME));
        }

        if(date - nowDate < 0){
            return false;
        }else{
            return true;
        }
    }


    // b => period //
    ADD_SOLO_CLASS_RESET();

    NEW_FORM = false;
    PERIOD_OBJ = {};
    $('.solo.form-pop').removeClass('new').removeClass('edit').removeAttr('data-seq');
    $('#ticketChoiceLabel').hide();
    $('#ticketChoice').show();

    PERIOD_OBJ = b;
    if(b[0] != undefined){     // 신규생성

        if($USER_GRADE < 3){
            if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 21) == -1){
                alertApp('X', '권한이 없습니다.');
                b.remove();
                return false;
            }
        }

        var selectLine = b.parent().parent();
        var selectLineIdx = selectLine.index();
        // var selectTrainerObj;
        
        if($('#teacherChoice').val() == ''){
            selectTrainerObj = TRAINER_LIST.filter(e => e.ISUSE == 1)[selectLineIdx];
        }else{
            selectTrainerObj = TRAINER_LIST.filter(e => e.USER_SQ == $('#teacherChoice').val())[0];
        }

        if(dateChk()){   
            NEW_FORM = true;
            $('.solo.form-pop').addClass('new');
            $('.jqs-options-remove').hide();
            
            $('.solo_Pop > div.input > .whiteFrame').hide();
            $('.solo_topBtn > button').eq(0).click();

            $('#pop_teacherChoice').val(selectTrainerObj.USER_NM).attr('data-seq',selectTrainerObj.USER_SQ);

        }else{
            alertApp('X','선택한 시간에 스케줄을 생성하실 수 없습니다');
            b.remove();
            return false;
        }

    }else{      // 이미있는 스케줄
        $('.solo_topBtn > button').eq(0).click();
        NEW_FORM = false;
        var SQ = b.firstElementChild.lastElementChild.getElementsByClassName('detail_info')[0].attributes[1].value;
        $('.solo.form-pop').addClass('edit').attr('data-seq',SQ);
        // $('.jqs-options-remove').show();
        $('.jqs-options-remove').hide();

        $('.solo_Pop > div.input > .whiteFrame').show();
        $('.solo_Pop div.input > .col1 .NotMemberGender').hide();

        let info = SCHEDULE_LIST.filter(e => e.RESERV_SQ == SQ)[0];

        MAKE_CLASS_INFO(info);

        EDIT_SCHEDULE.RESERV_SQ = info.RESERV_SQ;
    }

    $('#time_period').text(d);

    var so_gr_btn = $('section.content > div.top_sMenu > .topright > button');

    if(so_gr_btn.eq(0).attr('class')){          // 개인레슨 스케줄

        if($('section.content > div.top_sMenu > .topleft button.active').index() == 1){
            $('.jqs-options-time span.date_period').text($('#date').text());
        }else{
            if(SELECTED_DATE != ''){
                $('.jqs-options-time span.date_period').text(
                    SELECTED_DATE.split('-')[0] + '년 ' + SELECTED_DATE.split('-')[1] + '월 ' + (SELECTED_DATE.split('-')[2]).split(' ')[0] + '일 (' + (SELECTED_DATE.split('-')[2]).split(' ')[1] + '요일)'
                );
            }
        }
            $('.solo').add($('.dark_div')).fadeIn(200);

            $('.jqs-options-cancel').click(function(){
                $('.solo').add($('.group')).add($('.dark_div')).fadeOut(100);
            });
    }else{                                      // 그룹레슨 스케줄
        $('.group').add($('.dark_div')).fadeIn(200);
        $('.group > div.input > div > ul > li').click(function(){
            $(this).toggleClass('active');
        });
    }

    
    let nowSelectDate = $('span.date_period').eq(1).text().split(' ');
        dateY = String(parseInt(nowSelectDate[0]));
        dateM = String(parseInt(nowSelectDate[1])).length == 1 ? '0' + String(parseInt(nowSelectDate[1])) : String(parseInt(nowSelectDate[1]));
        dateD = String(parseInt(nowSelectDate[2])).length == 1 ? '0' + String(parseInt(nowSelectDate[2])) : String(parseInt(nowSelectDate[2]));
        nowSelectDate = dateY + '-' + dateM + '-' + dateD;

        SELECTED_DT = nowSelectDate;

}
// 이미 있는 수업 -> 모달에 정보 출력 (수정가능)
function MAKE_CLASS_INFO(data){
    $('#ticketChoiceLabel').show();
    $('#ticketChoice').hide();

    $('div.mSearch_container > ul.name_list > li[data-seq="' + data.USER_SQ + '"]').click();
    // $('#solo_searchName').attr('data-seq', data.USER_SQ);
    $('#solo_searchName').val(data.USER_NM);
    $('#pop_teacherChoice').val(data.MANAGER_NAME).attr('data-seq',data.MANAGER_SQ);
    $('#jqs-memo').val(data.MEMO);

    let formData = new FormData();
    formData.append('UV_SQ', data.UV_SQ);

    $.ajax({
        url : "flow_controller.php?task=getUserVoucherInfoSch",
        processData: false,
        contentType: false,
        method: "POST",
        data : formData,
        success : function(result){
            let data = JSON.parse(result)[0];
            $('#ticketChoiceLabel').val(data.VOUCHER_NAME);
            
            var MANAGER = TRAINER_LIST.filter(e => e.USER_SQ == data.SELLER_SQ)[0];

            $('p.UV_detail_info').css('flex-wrap','wrap').html(
                `<span style="display:block;width:100%;">담당: ${MANAGER.USER_NM} / 만료: ${data.USE_LASTDATE.split(' ')[0]}</span><br>
                <span style="display:block;width:100%;">이용: ${data.USEDCOUNT} / 잔여: ${Number(data.COUNT) - (Number(data.USEDCOUNT) + Number(data.RESERV_COUNT))} / 예약: ${data.RESERV_COUNT}</span>`
            );
        }
    });


}
function REMOVE_SOLO_CLASS(){
    console.log('삭제');
}
// 멤버 리스트 만들기
function MAKE_MEMBER_LIST(list, date){
    var tag = '';
    $('.mSearch_container .name_list').empty();

    for (let i in list){
        tag +=
            '<li data-seq="' + list[i].USER_SQ + '">\
                <div class="name">' + list[i].USER_NM + '</div>\
                <div>\
                    <p>' + birth_year(list[i].BIRTH_DT)  + '</p>\
                    <p>' + list[i].PHONE_NO + '</p>\
                </div>\
            </li>';
    }
    $('.mSearch_container .name_list').append(tag);


    // EVENT
    $('.mSearch_container .name_list > li').click(function(){
        var seq = $(this).attr('data-seq');
        var name = $(this).find('.name').text();
        $('#solo_searchName').attr('data-seq',seq);
        $('#solo_searchName').val(name);
        $('.mSearch_container').fadeOut(200);

        let formData = new FormData();
        formData.append('USER_SQ', seq);
        formData.append('START_DT', SELECTED_DT);
        $.ajax({
            url : "flow_controller.php?task=getUserVoucherListSch",
            method: "POST",
            contentType: false,
            processData: false,
            data: formData,
            success: function(result){
                var data = JSON.parse(result);
                var tag = '';

                HAVE_VOUCHER = data;
                $('#ticketChoice').empty();
                for(let i in data){
                    tag += '<option value="' + data[i].UV_SQ + '">' + data[i].VOUCHER_NAME + '</option>';
                }
                $('#ticketChoice').prepend(tag)
                                  .prepend('<option value="">선택해주세요</option>')
                                  .append(MAKE_FREE_VOUCHER(FREE_VOUCHER_LIST))
                                  .val('');
            }
        });
        
    });


    

}
function CHOICE_USER_VOUCHER(list){

    var tag = '';
    for(let i of list){
        tag +=
            `<option value="${i.UV_SQ}">${i.VOUCHER_NAME}</option>`;
    }
    return tag;
}
// 트레이너 리스트 만들기
function MAKE_TRAINER_LIST(list){
    var tag = '<option value="">선택</option>';
    TRAINER_NAME_LIST = [];
    $('#teacherChoice').empty();

    for(let i in list){
        TRAINER_NAME_LIST.push(list[i].USER_NM);
        tag +=
            '<option value="' + list[i].USER_SQ + '">' + list[i].USER_NM + '</option>'
    }

    $('#teacherChoice').append(tag);


    var tag = '';
    $('choiceManager > ul').empty();

    for(let i of list){
        tag +=
            `<li data-seq="${i.USER_SQ}">${i.USER_NM} / ${birth_year(i.BIRTH_DT)}</li>`
    }

    $('choiceManager > ul').append(tag);

    $('choiceManager > ul > li').click(function(){
        var dValue = $('#dateChoice').val();
        var tValue = $(this).attr('data-seq');
        var tempDateArr = addArrayText(dValue);
        var btn = $('section.content > div.top_sMenu > .topleft button');

        // 주/일 스케줄러 교체
        $('#schedule-week').show();
        $('#schedule-date').hide();

        let tempList = SCHEDULE_LIST.filter(e => e.MANAGER_SQ == tValue);
        TICKETTING_COUNT(tempList);  // 통계 표출

        GET_WEEK_SCHEDULE(
            tempDateArr[0].split(' ')[0],
            tempDateArr[tempDateArr.length - 1].split(' ')[0],
            tValue
        );
        VIEW_WEEK_DATE(dValue);
        btn.eq(0).addClass('active').siblings().removeClass('active');
        $('#teacherChoice').val(tValue);
        $('choicemanager, .dark_div').fadeOut(200);
        
        selectedTrainer = $('#teacherChoice').val();
        selectedView = $('.topleft').find('button.active').index();
        $('.jswscr2').css('width', '100%');
    });
    
}
// 형식에 맞게 배열 재구성
function MAKE_IMPORT_CLASS_LIST(list){
    IMPORT_CLASS_LIST = [];

    for (let i in list) {

        // 존재하지 않는 트레이너 화면표출 금지
        if(TRAINER_LIST.filter(e => e.USER_SQ == list[i].MANAGER_SQ).length == 0){
            continue;
        }
        
        var temp = {};
        var tempMemo = list[i].MEMO != '' ? ' / ' + list[i].MEMO : '';

        temp.day = TRAINER_LIST.findIndex(e => e.USER_SQ == list[i].MANAGER_SQ);
        temp.periods = [
            {
                start : list[i].START_TIME,
                end : list[i].END_TIME,
                title :
                    '<p class="detail_info" data-seq="' + list[i].RESERV_SQ + '" style="font-size:12px;line-height:20px;color:#fff;white-space:nowrap;letter-spacing:1px;">' +
                    list[i].USER_NM + ' / ' + list[i].RESERV_STATUS_NAME + ' / ' + list[i].VOUCHER_NAME + tempMemo + '</p>'
            }
        ]
        IMPORT_CLASS_LIST.push(temp);
    }

    return IMPORT_CLASS_LIST;
}
// 형식에 맞게 배열 재구성
function MAKE_IMPORT_CLASS_LIST_WEEK(list){
    IMPORT_CLASS_LIST_WEEK = [];

    for (let i = 0; i < list.length; i++) {
        var temp = {};

        var tempMemo = list[i].MEMO != '' ? ' / ' + list[i].MEMO : '';
        var day = new Date(list[i].RESERV_DT).getDay() - 1;
        temp.day = day;
        temp.periods = [
            {
                start : list[i].START_TIME,
                end : list[i].END_TIME,
                title :
                    '<p class="detail_info" data-seq="' + list[i].RESERV_SQ + '" style="font-size:12px;line-height: 20px;color: #fff;white-space: nowrap;letter-spacing:1px;">' +
                    list[i].USER_NM + '회원님' + tempMemo + '</p>'
            }
        ]
        IMPORT_CLASS_LIST_WEEK.push(temp);
    }
    return IMPORT_CLASS_LIST_WEEK;
}
// 스케줄 그리기 (일)
function MAKE_SCHEDULE_LIST(tlist,slist){

    $('#schedule-date .jqs-period').remove();

    var option = {
        daysList: tlist,
        // days: tlist.length,
        days: tlist.length <= 7 ? 7 : sevenPlus(tlist.length),

        // onRemovePeriod: REMOVE_SOLO_CLASS,
        onClickPeriod: ADD_SOLO_CLASS,
        onMouseUp : ADD_SOLO_CLASS
    }

    PAGE_COUNT = Math.ceil((option.daysList.length) / 7);

    $('#schedule-date').jqs(option);

    MAKE_IMPORT_CLASS_LIST(slist);

    if($('section.content > div.top_sMenu > .topleft button.active').index() == 1){
        $('#schedule-date').jqs('export');
        $('#schedule-date').jqs('import', IMPORT_CLASS_LIST);
    }else{
        $('#schedule-week').jqs('export');
        $('#schedule-week').jqs('import', IMPORT_CLASS_LIST);
    }
    $('.jqs-table td').css({
        'width' : (($('#schedule-date').width()) / 7) + 'px'
    });
    $('.jqs-table td').css({
        'width' : ($('.jqs-table').width() / PAGE_COUNT / 7) + 'px'
    });
    $('.jqs-grid-day').each(function(){
        var name = $(this).text() == "undefined" ? false : true;
        if(!name){
            $(this).text('').addClass('null');
        }
    });
    $('.jqs-grid-head').css({
        width : $('#schedule-date .jqs-table').outerWidth() + 'px'
    });
    PAGE_COUNT == 1 ? $('.jqs-demo > i.arrowIcon').hide() : $('.jqs-demo > i.arrowIcon').show();

    TICKETTING_COUNT(slist);
}
// 스케줄 그리기 (주)
function MAKE_SCHEDULE_LIST_WEEK(tlist,slist){

    $('#schedule-week .jqs-period').remove();

    var option = {
        daysList: addArrayText(dateFormat($now)),
        days: 7,
        onClickPeriod: ADD_SOLO_CLASS,
        onMouseUp : ADD_SOLO_CLASS
    }
    $('#schedule-week').jqs(option);
    MAKE_IMPORT_CLASS_LIST_WEEK(slist);

    $('#schedule-week').jqs('export');
    $('#schedule-week').jqs('reset');
    $('#schedule-week').jqs('import', IMPORT_CLASS_LIST_WEEK);
    $('#schedule-week .jqs-table td, #schedule-week .jqs-grid-day').css('min-width',(100/7) + '%');

    TICKETTING_COUNT(slist);
}
// 통계 리셋
function TICKETTING_COUNT_RESET(){
    $('#TICKETTING_COUNT').text(0);
    $('#TICKETTING_IN').text(0);
    $('#TICKETTING_NO').text(0);
    $('#TICKETTING_cancel').text(0);
}
// 통계 데이터 표출
function TICKETTING_COUNT(sch){
    TICKETTING_COUNT_RESET();

    var COUNT = $('#TICKETTING_COUNT');
    var IN = $('#TICKETTING_IN');
    var NO = $('#TICKETTING_NO');
    var CANCEL = $('#TICKETTING_cancel');
    
    COUNT.text(filterLen(sch,1));
    IN.text(filterLen(sch,3));
    NO.text(filterLen(sch,4));
    CANCEL.text(filterLen(sch,2));

    
}
// 필터 한 후 리스트 개수 알기 함수
function filterLen(list,fil){
    var tempList = [];
        tempList = list.filter(e => e.RESERV_STATUS == fil);
    return tempList.length;
}
// 일단위 보기 날짜 표출
function VIEW_DATE_DATE(date){
    var toDay = new Date(date);

    if($('#dateChoice').val() == ''){
        $('#dateChoice').val(dateFormat(toDay));
    }

    $('#date').text(function(){
        let y = toDay.getFullYear() + '년 ',
            m = (String(toDay.getMonth() + 1).length == 1 ? '0' + String(toDay.getMonth() + 1) : String(toDay.getMonth() + 1)) + '월 ',
            d = (String(toDay.getDate()).length == 1 ? '0' + String(toDay.getDate()) : String(toDay.getDate())) + '일 (',
            w = $day[toDay.getDay()] + '요일)';

        return y + m + d + w;
    });
}
// 주단위 보기 날짜 표출
function VIEW_WEEK_DATE(date){
    var tempList = addArrayText(date);

    $('#date').text(function(){
        var temp1 = tempList[0].split('-');
        var temp2 = tempList[tempList.length - 1].split('-');

        return temp1[0] + '년 ' + temp1[1] + '월 ' + temp1[2].split(' ')[0] + '일 (' + temp1[2].split(' ')[1] + '요일) ~ ' +
                temp2[0] + '년 ' + temp2[1] + '월 ' + temp2[2].split(' ')[0] + '일 (' + temp2[2].split(' ')[1] + '요일)';
    });

    for(let i = 0; i < tempList.length; i++){
        let target = $('#schedule-week .jqs-grid-day').eq(i);
        let text = tempList[i];
        target.html(text);
    }
}
// ajax 개인레슨 스케줄 (일)
function GET_DATE_SCHEDULE(startDT){

    let formData = new FormData();
    formData.append('START_DT',startDT);

    $.ajax({
        url: "flow_controller.php?task=getUserPersonalScheduleList",
        method: "POST",
        contentType: false,
        processData: false,
        data: formData,
        success: function(result){
            var data = result.split('|');

            SCHEDULE_LIST = JSON.parse(data[0]);
            HOLIDAY_LIST = JSON.parse(data[1]);
            SCHEDULESTATISTICS = JSON.parse(data[2]);
            TARGET_DATE = dateFormat($now);
            
            MAKE_SCHEDULE_LIST(TRAINER_NAME_LIST,SCHEDULE_LIST);
        }
    });
}
// ajax 개인레슨 스케줄 (주)
function GET_WEEK_SCHEDULE(startDT,endDT,trainer){

    let formData = new FormData();
    formData.append('START_DT',startDT);
    formData.append('END_DT',endDT);
    formData.append('MANAGER_SQ',trainer);

    $.ajax({
        url: "flow_controller.php?task=getUserPersonalWeeklyScheduleList",
        method: "POST",
        contentType: false,
        processData: false,
        data: formData,
        success: function(result){
            var data = result.split('|');

            SCHEDULE_LIST = JSON.parse(data[0]);
            HOLIDAY_LIST = JSON.parse(data[1]);
            SCHEDULESTATISTICS = JSON.parse(data[2]);
            TARGET_DATE = dateFormat($now);
            
            MAKE_SCHEDULE_LIST_WEEK(TRAINER_NAME_LIST,SCHEDULE_LIST);
        }
    });
}
// 세션 유지 함수
function LAST_SESSION(){

    if(selectedView == undefined){
        selectedView = 1;
    }
    if(selectedDate == undefined){
        selectedDate = dateFormat($now);
    }
    if(selectedTrainer == undefined){
        selectedTrainer = $('#teacherChoice').val();
    }

    $('#dateChoice').val(selectedDate);
    $('#teacherChoice').val(selectedTrainer);
    $('.topleft button').eq(selectedView).click();
}
// 출석/결석/취소 모달 정보 GET
function AJAX_UV_DATA(IDX, SCHE_DATA,UV_SQ){
    let formData = new FormData();
    formData.append('UV_SQ', UV_SQ);

    $.ajax({
        url: "flow_controller.php?task=getUserVoucherInfoSch",
        method: "POST",
        data: formData,
        contentType: false,
        processData: false,
        data: formData,
        success: function(result){
            var DATA = JSON.parse(result)[0];
            RESERV_CHANGE_COUNT(IDX, SCHE_DATA, DATA);
        }
    });    
}
// 출석/결석/취소 모달 정보표출
function RESERV_CHANGE_COUNT(idx, SCHE_data, UV_data){

    var count = Number(UV_data.COUNT) - Number(UV_data.USEDCOUNT);
    var tempCount = count;
    var set = RESERVATION_SETTING_LIST;

    $('#pop_div1 > section.state_sec > p:nth-of-type(2) > span').text(SCHE_data.USER_NM);
    $('#pop_div1 > section.state_sec > div:last-of-type > div > div > p:last-of-type').eq(0).add(
        $('#pop_div1 > section.state_sec > div:last-of-type > div > div > p:last-of-type').eq(2)
    ).add(
        $('#pop_div1 > section.state_sec > div:last-of-type > div > div > p:last-of-type').eq(4)
    ).text(
        count + '회'
    );

    switch (idx) {

        case 0: // 출석
            $('#pop_div1 > section.state_sec > div:first-of-type > span.color').eq(0).text('1회 차감');
            $('#pop_div1 > section.state_sec > div:last-of-type > div > div > p:last-of-type').eq(1).text(count - 1 + '회');
            break;
            
        case 1: // 결석
            if(set.PSN_ABSENCE_TICKET == 0){        // 차감
                $('#pop_div1 > section.state_sec > div:first-of-type > span.color').eq(1).text('1회 차감');
                tempCount -= 1;
            }else{
                $('#pop_div1 > section.state_sec > div:first-of-type > span.color').eq(1).text('0회 차감');
            }
            $('#pop_div1 > section.state_sec > div:last-of-type > div > div > p:last-of-type').eq(3).text(tempCount + '회');
            break;
            
        case 2: // 취소
            $('#pop_div1 > section.state_sec > div:first-of-type > span.color').eq(2).text('유지');
            $('#pop_div1 > section.state_sec > div:last-of-type > div > div > p:last-of-type').eq(5).text(tempCount + '회');
            break;
            
    }

}
// 수업 예약하기 함수
function RESERVATION(START_DT, USER_SQ, MANAGER_SQ, UV_SQ, START_TIME, END_TIME, MEMO){

    let formData = new FormData();
        formData.append('START_DT', START_DT);
        formData.append('USER_SQ', USER_SQ);
        formData.append('MANAGER_SQ', MANAGER_SQ);
        formData.append('UV_SQ', UV_SQ);
        formData.append('START_TIME', START_TIME);
        formData.append('END_TIME', END_TIME);
        formData.append('MEMO', MEMO);

    $.ajax({
        url : "flow_controller.php?task=execUserPersonalScheduleSave",
        method: "POST",
        contentType: false,
        processData: false,
        data : formData,
        success : function(){
            
            $('.solo.form-pop').add($('div.dark_div')).fadeOut(200);
            alertApp('O','개인수업이 등록되었습니다');

            selectedView = $('section.content > div.top_sMenu > .topleft button.active').index();
            selectedDate = $('#dateChoice').val();
            selectedTrainer = $('#teacherChoice').val();

            GET_AJAX_DATA();
            return false;
        },
        error : function(e){
            console.log(e);
        }
    });

}
// 무료이용권 예약할시 이용권 구매 루틴
function AJAX_ITEM_PAYMENT(a,b,c,d,e,f,g,h,i,j,k,l,m){
    var temp = $('.date_period').eq(1).text().split(' ');
    var tempClean = parseInt(temp[0]) + '-' + (String(parseInt(temp[1])).length == 1 ? '0' + String(parseInt(temp[1])) : String(parseInt(temp[1]))) + '-' + (String(parseInt(temp[2])).length == 1 ? '0' + String(parseInt(temp[2])) : String(parseInt(temp[2])));

    let formData = new FormData();
        formData.append('VOUCHER_SQ',a);
        formData.append('MEMBER_SQ',b);
        formData.append('DISCOUNT_TYPE',c);
        formData.append('DISCOUNT_RATIO',d);
        formData.append('DISCOUNT_AMOUNT',e);
        formData.append('SELLINGPRICE',f);
        formData.append('SELLER_SQ',g);
        formData.append('TRAINER_SQ',g);
        formData.append('PAYED_AMOUNT_CARD',h);
        formData.append('PAYED_AMOUNT_CASH',i);
        formData.append('PAYED_AMOUNT_BANK',j);
        formData.append('USE_STARTDATE',k);
        formData.append('USE_LASTDATE',l);
        formData.append('PAY_MEMO',m);

    $.ajax({
        url: "flow_controller.php?task=execPuchaseCreate",
        method: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(result){
            let r = JSON.parse(result);
            RESERVATION(
                tempClean,
                $('#solo_searchName').attr('data-seq'),
                $('#pop_teacherChoice').attr('data-seq'),
                r.UV_SQ,
                $('#time_period').text().split(' ~ ')[0],
                $('#time_period').text().split(' ~ ')[1],
                $('#jqs-memo').val()
            );      // 예약!!
        }
    });
}
// 미등록회원 스케줄 예약
function NOT_MEMBER_RESERVATION(START_DT,USER_NM,PHONE_NO,GENDER,MANAGER_SQ,VOUCHER_SQ,START_TIME,END_TIME,MEMO){

    let formData = new FormData();
        formData.append('START_DT',START_DT);
        formData.append('USER_NM',USER_NM);
        formData.append('PHONE_NO',PHONE_NO);
        formData.append('GENDER',GENDER);
        formData.append('MANAGER_SQ',MANAGER_SQ);
        formData.append('VOUCHER_SQ',VOUCHER_SQ);
        formData.append('START_TIME',START_TIME);
        formData.append('END_TIME',END_TIME);
        formData.append('MEMO',MEMO);

    $.ajax({
        url : 'flow_controller.php?task=execUnRegUserPersonalScheduleSave',
        method: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(result){
            $('.solo.form-pop').add($('div.dark_div')).fadeOut(200);
            alertApp('O','개인수업이 등록되었습니다');
            GET_AJAX_DATA();
            return false;
        },error: function(e){

        }

    });
}
// 개인수업예약 초기화
function solo_frm_reset(){
    $('.solo_Pop input').not($('#pop_teacherChoice')).val('');
    $('.solo_Pop select').val('');
}
// 그룹수업등록 초기화
function Addgroup_frm_reset(){
    $('.group input').val('');
    $('.group select').val('');
    $('.group .col3 li').removeClass('active');
}
// 그룹수업예약 초기화
function group_frm_reset(){
    $('#groupSearchMember').val('');
}
/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////o0////////////
//////////////////////////////document.ready()///////////////////////////////
/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
$(function(){
    GET_AJAX_DATA();
    

    $('.jqs-options-cancel').click(function(){
        if(NEW_FORM){   // 신규생성이면 할일
            PERIOD_OBJ.remove();
        }
        return false;
    });


////////////////////////////////////////////////////////////////////////////////

    

    PN_date(0);
    
    if($('#dateChoice').val() == ''){
        let toDay = new Date();
        $('#dateChoice').val(dateFormat(toDay));
    }

    // 날짜 변경 (화살표 클릭)
    $('section.content > div.top_sMenu > .topcenter > i').click(function(){
        var WDidx = $('section.content > div.top_sMenu > .topleft button.active').index() == 0 ? 'week' : 'day';
        var Wdir = $(this)[0].id == 'datePrev' ? -7 : 7;
        var Ddir = $(this)[0].id == 'datePrev' ? -1 : 1;

        switch(WDidx){
            case 'week': PN_date(Wdir,WDidx);
                         break;
            case 'day':  PN_date(Ddir,WDidx);
                         break;
        }
        
    });

    // 날짜 하루전/하루후
    function PN_date(n,WDidx){
        var date = new Date($('#dateChoice').val());
        date.setDate(date.getDate() + n);

        $('#dateChoice').val(dateFormat(date)).change();

        if(WDidx == 'week'){
            $('#date').text(() => {
                let el = $('#schedule-week .jqs-grid-day');
                let firstDay = el.eq(0).text().split(' ');
                    firstDay = firstDay[0].split('-').concat(firstDay[1]);
                    firstDay = firstDay[0] + '년 ' + firstDay[1] + '월 ' + firstDay[2] + '일 (' + firstDay[3] + '요일) ~ ';
                let lastDay = el.eq(el.length - 1).text().split(' ');
                    lastDay = lastDay[0].split('-').concat(lastDay[1]);
                    lastDay = lastDay[0] + '년 ' + lastDay[1] + '월 ' + lastDay[2] + '일 (' + lastDay[3] + '요일)';
                return firstDay + lastDay;
            });
        }else{
            $('#date').text(
                date.getFullYear()+'년 '+
                (String(date.getMonth()+1).length == 1 ? '0' + String(date.getMonth()+1) : String(date.getMonth()+1)) +'월 '+
                (String(date.getDate()).length == 1 ? '0' + String(date.getDate()) : String(date.getDate())) +'일 ' +
                '('+$day[date.getDay()]+'요일)'
            );
        }



    }

    // 주 / 일 버튼
    $('.topleft').find('button').click(function(){
        
        var idx = $(this).index();
        var dValue = $('#dateChoice').val();
        var tValue = $('#teacherChoice').val();
        var tempDateArr = addArrayText(dValue);
        var btn = $('section.content > div.top_sMenu > .topleft button');
        TICKETTING_COUNT_RESET();
        
        if(idx == 0){  // [주] 버튼 클릭
            // 강사 선택 안함.
            if(tValue == ''){
                $('choiceManager').add($('.dark_div')).fadeIn(200);
                return false;
            }else{
                // 주/일 스케줄러 교체
                $('#schedule-date').hide();
                $('#schedule-week').show();
                btn.eq(idx).addClass('active').siblings().removeClass('active');
            }

            let tempList = SCHEDULE_LIST.filter(e => e.MANAGER_SQ == tValue);
            TICKETTING_COUNT(tempList);  // 통계 표출
            GET_WEEK_SCHEDULE(
                tempDateArr[0].split(' ')[0],
                tempDateArr[tempDateArr.length - 1].split(' ')[0],
                tValue
            );

            VIEW_WEEK_DATE(dValue);
            $('.jswscr2').css('width', '100%');
        }else{  // [일] 버튼 클릭

            $('#teacherChoice').val('');
            // 주/일 스케줄러 교체
            $('#schedule-week').hide();
            $('#schedule-date').show();
            btn.eq(idx).addClass('active').siblings().removeClass('active');

            TICKETTING_COUNT(SCHEDULE_LIST);     // 통계 표출

            GET_DATE_SCHEDULE(
                dValue
            );

            VIEW_DATE_DATE(dValue);
            
        }
        
        selectedView = idx;

    });

    // 강사선택할때
    $('#teacherChoice').change(function(){
        
        var dValue = $('#dateChoice').val();
        var tValue = $('#teacherChoice').val();
        var tempDateArr = addArrayText(dValue);
        var btn = $('section.content > div.top_sMenu > .topleft button');

        if(tValue != ''){      // 주

            // 주/일 스케줄러 교체
            $('#schedule-week').show();
            $('#schedule-date').hide();

            let tempList = SCHEDULE_LIST.filter(e => e.MANAGER_SQ == tValue);
            TICKETTING_COUNT(tempList);  // 통계 표출

            GET_WEEK_SCHEDULE(
                tempDateArr[0].split(' ')[0],
                tempDateArr[tempDateArr.length - 1].split(' ')[0],
                tValue
            );
            VIEW_WEEK_DATE(dValue);
            btn.eq(0).addClass('active').siblings().removeClass('active');

        }else{  // 일

            // 주/일 스케줄러 교체
            $('#schedule-date').show();
            $('#schedule-week').hide();

            TICKETTING_COUNT(SCHEDULE_LIST);     // 통계 표출

            GET_DATE_SCHEDULE(
                dValue
            );
            VIEW_DATE_DATE(dValue);
            btn.eq(1).addClass('active').siblings().removeClass('active');

        }

        selectedTrainer = $('#teacherChoice').val();
        selectedView = $('.topleft').find('button.active').index();
        
    });

    // 날짜선택할때
    $('#dateChoice').change(function(){
        var dValue = $('#dateChoice').val();
        var tValue = $('#teacherChoice').val();
        var tempDateArr = addArrayText(dValue);
        var btn = $('section.content > div.top_sMenu > .topleft button');
        var btnBool = btn.eq(0).hasClass('active');
        selectedDate = dValue;

        if(btnBool){        // [주] 보기
            GET_WEEK_SCHEDULE(
                tempDateArr[0].split(' ')[0],
                tempDateArr[tempDateArr.length - 1].split(' ')[0],
                tValue
            );
            VIEW_WEEK_DATE(dValue);
        }else{              // [일] 보기
            GET_DATE_SCHEDULE(
                dValue
            );
            VIEW_DATE_DATE(dValue);
        }

        // 날짜 세션 유지
        
        // $(this).val(selectedDate);
        // selectedDate = $(this).val();
        // selectedTrainer = $('#teacherChoice').val();
        // selectedView = $('.topleft').find('button.active').index();
    });



    $(document).click(function(){
        $('.rightClick_div').hide();
    });
    $('.rightClick_div').mouseleave(function(){
        $('.rightClick_div').hide();
    });

    // 트레이너 선택 모달 닫기 버튼 클릭
    $('choiceManager .btn > button').click(function(){
        $('choiceManager, .dark_div').fadeOut(200);
    });

    // dark_div 클릭
    $('.dark_div').add('.jqs-options-cancel').click(() => {
        if($('.solo.form-pop').attr('class').indexOf('new') > -1){
            PERIOD_OBJ.remove();
        }
        justClose();
        $('choicemanager').fadeOut(200);
    });

    function justClose(){
        $('.dark_div').fadeOut(200);
        $('.form-pop').fadeOut(200);
    }


    //   우클릭 메뉴 클릭 // 개인레슨
    $('#rightClick_div1 > li').click(function(){
        var i = $(this).index();

        if(i < $('#pop_div1 > section.state_sec').length){       // 상태변경(출석/결석/취소)
            var data = SCHEDULE_LIST.filter(e => e.RESERV_SQ == SELECTED_CLASS)[0];
            $('#pop_div1 > section.state_sec > p:nth-of-type(1)').text(
                data.RESERV_DT.split('-')[0] + '년 ' +
                data.RESERV_DT.split('-')[1] + '월 ' +
                data.RESERV_DT.split('-')[2] + '일 ' +
                data.START_TIME + ' ~ ' + data.END_TIME
            );
            // console.log(data);
            var RESERV_STATUS = data.RESERV_STATUS;
            if(i == 0){         // 출석
                if(RESERV_STATUS == 3){
                    alertApp('!','이미 출석처리 되었습니다.');
                    $('#rightClick_div1').fadeOut(200);
                    return false;
                }
            }else if(i == 1){   // 결석
                if(RESERV_STATUS == 4){
                    alertApp('!','이미 결석처리 되었습니다.');
                    $('#rightClick_div1').fadeOut(200);
                    return false;
                }
            }
            
            if(RESERV_STATUS != 1){
                if($USER_GRADE < 3){
                    if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 22) == -1){
                        alertApp('X', '권한이 없습니다.');
                        return false;
                    }
                }
                if(RESERV_STATUS == 3){
                    alertApp('!','이미 출석처리 되었습니다.');
                    $('#rightClick_div1').fadeOut(200);
                    return false;
                }else{
                    alertApp('!','이미 결석처리 되었습니다.');
                    $('#rightClick_div1').fadeOut(200);
                    return false;
                }
            }
            AJAX_UV_DATA(i, data, data.UV_SQ);

            $('#pop_div1').add($('.dark_div')).fadeIn(200);
            $('#pop_div1 > section').hide().eq(i).show();
            
        }else if(i == 3){       // 회원 이용권 관리
            var SELECTED_MEMBER_SQ = SCHEDULE_LIST.filter(e => e.RESERV_SQ == SELECTED_CLASS)[0].USER_SQ;

            window.location.href = 'member_info.php?u_seq=' + SELECTED_MEMBER_SQ;
        }else{
            return false;
        }

    });


    // 팝업 적용 버튼 클릭
    $('#pop_div1 > section.state_sec > div:last-of-type > div:last-of-type > button.pop_submit').click(function(){
        var clData = SCHEDULE_LIST.filter(e => e.RESERV_SQ == SELECTED_CLASS)[0];
        var clName = $(this)[0].className;
            clName = clName.split('pop_submit ')[1];
        var set = RESERVATION_SETTING_LIST;
        var NOW_TIME = new Date();
        var START_TIME = new Date(clData.RESERV_DT);
            START_TIME.setHours(clData.START_TIME.split(':')[0]);
            START_TIME.setMinutes(clData.START_TIME.split(':')[1]);
        
        switch(clName){

            case 'chulsuk' :    // 출석 적용
                SCH_O(clData.RESERV_SQ, clData.UV_SQ);
                break;

            case 'gyeolsuk' :   // 결석 적용
                let uv_sq = set.PSN_ABSENCE_TICKET == '0' ? clData.UV_SQ : 0;
                if(clData.RESERV_STATUS == 4){
                    alertApp('!','이미 결석처리 되었습니다');
                    return false;
                }
                SCH_X(clData.RESERV_SQ, uv_sq);
                break;

            case 'cancel' :     // 취소 적용
                switch (set.PSN_MOD_TYPE) {
                    case '0':   // 수업시간 전 항상 가능
                        if(START_TIME - NOW_TIME > 0){
                            SCH_C(clData.RESERV_SQ, clData.UV_SQ);
                        }else{
                            alertApp('X','현재 취소가 불가능합니다');
                        }
                        break;
                
                    case '1':   // 예약 후 변경 및 취소 불가
                        alertApp('X','현재 취소가 불가능합니다');
                        break;
                
                    case '2':   // 당일 취소 및 변경 불가
                        let nowDate = NOW_TIME.getFullYear() + (String(NOW_TIME.getMonth()).length == 1 ? '0' + String(NOW_TIME.getMonth()) : String(NOW_TIME.getMonth())) + (String(NOW_TIME.getDate()).length == 1 ? '0' + String(NOW_TIME.getDate()) : String(NOW_TIME.getDate()));
                        let startDate = START_TIME.getFullYear() + (String(START_TIME.getMonth()).length == 1 ? '0' + String(START_TIME.getMonth()) : String(START_TIME.getMonth())) + (String(START_TIME.getDate()).length == 1 ? '0' + String(START_TIME.getDate()) : String(START_TIME.getDate()));
                        if(startDate - nowDate > 0){
                            SCH_C(clData.RESERV_SQ, clData.UV_SQ);
                        }else{
                            alertApp('X','현재 취소가 불가능합니다');
                        }
                        break;
                
                    case '3':   // 수업 취소 및 변경 가능한 시간 설정 (수업시간 전 몇분..)
                        NOW_TIME.setMinutes(NOW_TIME.getMinutes() + Number(set.PSN_MOD_TIME))
                        if(START_TIME - NOW_TIME > 0){
                            SCH_C(clData.RESERV_SQ, clData.UV_SQ);
                        }else{
                            alertApp('X','현재 취소가 불가능합니다');
                        }
                        break;
                }
                break;

        }

    });
    

    // 미등록회원 연락처 입력
    $('#solo_PhoneN').keyup(function(){
        autoHyphen($(this),this.value);
    });

    //   팝업 닫기
    $('button.pop_close').click(function(){
        $('#pop_div1').add($('.dark_div')).fadeOut(100);
    });

    //////////////////////////////////////

    // 회원검색 클릭
    $('#solo_searchName').click(function(){
        if($('.solo_topBtn > button.active').index() == 0){
            $('div.mSearch_container').fadeIn(200);
            $('div.mSearch_container').find('input').val('');
            $('div.mSearch_container').find('input').keyup();
            $('div.mSearch_container').find('input').focus();
        }else{
            return false;
        }

    });


    $('#ticketChoice').change(function(){
        var seq = $(this).val();
        $(this).attr('data-seq',seq);

        if(seq == ''){
            $('p.UV_detail_info').css('flex-wrap','wrap').html(
                `<span>이용권을 선택해주세요.</span>`
            );
        }else{    
            if(HAVE_VOUCHER.filter(e => e.UV_SQ == seq).length != 0){
                let data = HAVE_VOUCHER.filter(e => e.UV_SQ == seq)[0];
                var MANAGER = TRAINER_LIST.filter(e => e.USER_SQ == data.SELLER_SQ)[0];
                $('p.UV_detail_info').css('flex-wrap','wrap').html(
                    `<span style="display:block;width:100%;">담당: ${MANAGER.USER_NM} / 만료: ${data.USE_LASTDATE.split(' ')[0]}</span><br>
                    <span style="display:block;width:100%;">이용: ${data.USEDCOUNT} / 잔여: ${Number(data.COUNT) - (Number(data.USEDCOUNT) + Number(data.RESERV_COUNT))} / 예약: ${data.RESERV_COUNT}</span>`
                );
            }else{
                $('p.UV_detail_info').css('flex-wrap','wrap').html(
                    `<span>무료이용권을 선택하셨습니다.</span>`
                );
            }
        }
    });


    // 스케줄 등록 
    $('.jqs-options-close').click(function(){
        var Yes_or_No_Member = $('.solo_topBtn > button.active').index();

        if(NEW_FORM){       // 등록

            if($('#solo_searchName').val() == ''){
                alertApp('!','회원을 선택해주세요');
                return false;
            }
            if($('#ticketChoice').val() == ''){
                alertApp('!','이용권을 선택해주세요');
                return false;
            }
    
            
            var temp = $('.date_period').eq(1).text().split(' ');
            var tempClean = parseInt(temp[0]) + '-' + (String(parseInt(temp[1])).length == 1 ? '0' + String(parseInt(temp[1])) : String(parseInt(temp[1]))) + '-' + (String(parseInt(temp[2])).length == 1 ? '0' + String(parseInt(temp[2])) : String(parseInt(temp[2])));
            let tempFreeList = FREE_VOUCHER_LIST.filter(e => e.VOUCHER_SQ == $('#ticketChoice').val());
            let FREE$ = tempFreeList.length > 0 ? true : false

            if(Yes_or_No_Member){   // 미등록 회원 스케줄 예약
                if($('#solo_PhoneN').val() == ''){
                    alertApp('!','연락처를 입력해주세요');
                    return false;
                }
                NOT_MEMBER_RESERVATION(
                    tempClean,
                    $('#solo_searchName').val(),
                    $('#solo_PhoneN').val(),
                    $('#NotMemberGender_male').prop('checked') ? 'M' : 'F',
                    $('#pop_teacherChoice').attr('data-seq'),
                    $('#ticketChoice').val(),
                    $('#time_period').text().split(' ~ ')[0],
                    $('#time_period').text().split(' ~ ')[1],
                    $('#jqs-memo').val()
                );
                return false;
            }

            if(FREE$){
                AJAX_ITEM_PAYMENT(
                    $('#ticketChoice').val(), 
                    $('#solo_searchName').attr('data-seq'), 
                    1, 0, 0, 0, 
                    $('#pop_teacherChoice').attr('data-seq'), 
                    0, 0, 0,
                    SELECTED_DT,
                    SELECTED_DT,
                    ''
                );
            }else{
                RESERVATION(
                    tempClean,
                    $('#solo_searchName').attr('data-seq'),
                    $('#pop_teacherChoice').attr('data-seq'),
                    $('#ticketChoice').val(),
                    $('#time_period').text().split(' ~ ')[0],
                    $('#time_period').text().split(' ~ ')[1],
                    $('#jqs-memo').val()
                );      // 예약!!
            }


        }else{      // 수정

            if($USER_GRADE < 3){
                if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 22) == -1){
                    alertApp('X', '권한이 없습니다.');
                    return false;
                }
            }

            let formData = new FormData();
                formData.append('RESERV_SQ', EDIT_SCHEDULE.RESERV_SQ);
                formData.append('MEMO', $('#jqs-memo').val());
                
            $.ajax({
                url: "flow_controller.php?task=execUserPersonalScheduleModify",
                method: "POST",
                contentType: false,
                processData: false,
                data: formData,
                success: function(){
                    alertApp('O','수정되었습니다');
                    $('.solo.form-pop.edit').fadeOut(200);
                    GET_AJAX_DATA();
                }
            });

        }

    });


    //개인수업 예약 (버튼)
    $('.solo_topBtn > button').click(function(){
        $(this).addClass('active').siblings().removeClass('active');    // 버튼 Active
        solo_frm_reset();       // RESET
        if($(this).index() == 0){   //등록회원

            $('#solo_searchName').css('width', '300px');
            $('.solo_Pop div.input > .col1 .NotMemberGender').hide();
            
            $('.solo_Pop div.input > .col1 > label').text('회원검색');
            $('.solo_Pop div.input > .col1 #solo_searchName').attr('readonly',true);
            $('.solo_Pop div.input > .col1 #solo_searchName').attr('placeholder','클릭해주세요.');
            $('.solo_Pop div.input > .col4').show();
            $('.solo_Pop div.phone-num').hide();
            $('#ticketChoice').empty().append(
                '<option value="">선택해주세요</option>'
            ).append(MAKE_FREE_VOUCHER(FREE_VOUCHER_LIST));
        }else{  //미등록회원

            $('#solo_searchName').css('width', '150px');
            $('.solo_Pop div.input > .col1 .NotMemberGender').show();

            $('.solo_Pop div.input > .col1 > label').text('회원명');
            $('.solo_Pop div.input > .col1 #solo_searchName').removeAttr('readonly', false);
            $('.solo_Pop div.input > .col1 #solo_searchName').attr('placeholder','이름을 입력하세요.');
            $('.solo_Pop div.input > .col4').hide();
            $('.solo_Pop div.phone-num').show();
            $('#ticketChoice').empty().append(
                MAKE_FREE_VOUCHER(FREE_VOUCHER_LIST)
            );
        }
    });


    // // 그룹수업 등록 (버튼)
    // $('.group .btnSet .jqs-options-close').add($('.group .btnSet .jqs-options-remove')).click(function(){    //등록
    //     $('.group').add($('.dark_div')).fadeOut(100);
    //     Addgroup_frm_reset();
    // });

    // // 그룹수업 예약 (버튼)
    // $('#pop_div2 .jqs-options-close').add($('#pop_div2 .jqs-options-cancel')).click(function(){     //예약
    //     $('#pop_div2').add($('.dark_div')).fadeOut(100);
    //     group_frm_reset();
    // });

    // // 그룹수업 예약자 목록 회원검색하기
    // $('#ticketting_member_search').keyup(function(){
    //     var text = $(this).val().toLowerCase();
    //     $('article.ticketting > div.table tbody > tr').filter(function(){
    //         $(this).toggle(
    //             $(this).text().toLowerCase().indexOf(text) > -1
    //         );
    //     });
    // });

    // // 예약목록
    // $('#pop_div2 > section').eq(0).find('.btnSet > button').click(function(){
    //     $('article.ticketting').fadeIn(200);
    // });

    // // 수업정보 닫기
    // $('#pop_div2 > section:nth-of-type(2) > div.closeX').click(function(){
    //     $('#pop_div2').add($('.dark_div')).add($('div.groupClassMemo')).fadeOut(100);
    // });

    // 수업 예약자 목록 생성 (From)
    // for(i=0; i<memberList.length; i++){
    //     $('article.ticketting > div.table table > tbody').append(
    //         '<tr><td><label class="hid" for="TICKETTING_CHECK' + i + '">선택</label>' +
    //         '<input id="TICKETTING_CHECK' + memberList[i].Sequence + '" type="checkbox" class="TICKETTING_CHECK" data-seq="'+ memberList[i].Sequence +'"></input></td><td>' +
    //         memberList[i].Name +
    //         '</td><td>' +
    //         memberList[i].Phone +
    //         '</td><td>' +
    //         memberList[i].TicketingTime +
    //         '</td><td>' +
    //         memberList[i].State +
    //         '</td></tr>'
    //     );
    // }


    // var ticketting_checkbox_all = $('article.ticketting table input#TICKETTING_CHECKALL');
    // var ticketting_checkbox = $('article.ticketting table input.TICKETTING_CHECK');
    // var ticketting_state_btn = $('.ticketting_btnSet > div > button');

    // // 체크가 되어있는 멤버들의 배열
    // var list = [];      // 배열정의
    // // 체크박스가 클릭 함수
    // ticketting_checkbox.click(function(){
    //     list = [];  //초기화
    //     ticketting_checkbox.each(function(){
    //         if($(this).prop('checked')){
    //             list.push($(this).attr('data-seq'));
    //         }
    //     });

    //     // 체크박스가 하나라도 체크되어있으면 버튼 visible
    //     list.length != 0 ? ticketting_state_btn.show() : ticketting_state_btn.hide();

    //     // 체크박스가 전체 다 체크되어있으면 전체체크
    //     if(list.length == ticketting_checkbox.length){
    //         ticketting_checkbox_all.prop('checked',true);
    //     }else{
    //         ticketting_checkbox_all.prop('checked',false);
    //     }

    // });//

    // // 수업 예약자 목록 전체 체크
    // ticketting_checkbox_all.click(function(){
    //     if($(this).is(':checked')){
    //         ticketting_checkbox.prop('checked', true);
    //         ticketting_state_btn.show();
    //     }else{
    //         ticketting_checkbox.prop('checked', false);
    //         ticketting_state_btn.hide();
    //     }
    // });

    // // 수업 예약자 목록창 (출석/결석/예약취소) 버튼
    // $('.ticketting_btnSet > div > button').click(function(){
    //     var i = $(this).index();
    //     $('#pop_div2').css('z-index','99');
    //     $('#pop_div1').add($('.dark_div')).fadeIn(200);
    //     $('#pop_div1 > section').eq(i).show().siblings().hide();
    // });


    // // 수업 예약자 목록창 닫기
    // $('.ticketting_btnSet').find('button.close').click(function(){
    //     $('article.ticketting').fadeOut(100);
    //     $('#ticketting_member_search').val('');
    //     ticketting_checkbox_all.add(ticketting_checkbox).prop('checked',false);
    // });


    // 수업내용메모
    // $('#groupClassMemoSetBtn').click(function(){
    //     if($(this).text() == '수 정'){
    //         $(this).text('저 장');
    //         $('#groupClassMemo').prop('readonly',false);
    //     }else{
    //         $(this).text('수 정');
    //         $('#groupClassMemo').prop('readonly',true);
    //     }
    // });


    //(작업중)
    // setTimeout(() => {
    //     $('#teacherChoice').val(73).change();
    // },300);

    $('.jqs-demo > i.arrowIcon').click(function(){
        var clName = $(this).hasClass('left');

        if(clName){
            if($i > 0){ $i-- }
        }else{
            if($i < PAGE_COUNT - 1){ $i++ }
        }
        
        $('.jswscr1').animate({'left' : $i * -100 + '%'},300);
        $('.jswscr2').animate({'left' : $i * -100 + '%'},300);

    });
    


});


function sevenPlus(num){
    o += 7;
    var temp = o + 7;
        
    if(num > temp){
        sevenPlus(num);
    }
    return Number(temp);
}