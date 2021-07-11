var o = 0;
var NEW_FORM = false;
var PERIOD_OBJ = {};
var ROOM_LIST = [];
var MEMBER_LIST = [];
var TRAINER_LIST = [];
var TRAINER_NAME_LIST = [];
var RESERVATION_SETTING = {};
var RESERVATION_STATUS_LIST = [];
var IMPORT_CLASS_LIST = [];
var IMPORT_CLASS_LIST_WEEK = [];

var CLASS_LIST = [];
var HOLIDAY_LIST = [];
var USER_VOUCHER_LIST = [];
var RESERVATION_MEMBER_LIST = [];

function ADD_SOLO_CLASS_RESET(){
    $('#groupClassName').add($('#groupClassWhen1')).add($('#groupClassWhen2')).add($('#groupClassTeacher')).add($('#groupClassPay')).add($('#groupClassCountO')).add($('#groupClassCountX')).add($('#groupClassMemo')).val('');
    $('#groupClassWhere').text(
        ROOM_LIST.filter(e => e.ROOM_SQ == $('#whereChoice').val())[0].ROOM_NAME
    ).attr('data-seq', $('#whereChoice').val());
    $('.group > div.input > div > ul > li').removeClass('active');
    $('.group > div.input > div > ul > li').eq(0).addClass('active');
    $('.group > div.input > div > ul > li').eq(1).addClass('active');
    $('.group > div.input > div > ul > li').eq(2).addClass('active');
    $('.group > div.input > div > ul > li').eq(3).addClass('active');
    $('.group > div.input > div > ul > li').eq(4).addClass('active');
}

// DB 스케줄 GET
function GET_SCHEDULE(START_DT, END_DT, ROOM_SQ){
    let formData = new FormData();
        formData.append('START_DT', START_DT);
        formData.append('END_DT', END_DT);
        formData.append('ROOM_SQ', ROOM_SQ);

    $.ajax({
        url: 'flow_controller.php?task=getUserGroupWeeklyScheduleList',
        data: formData,
        method: 'POST',
        contentType: false,
        processData: false,
        success: function(r){
            var data = r.split('|');
            CLASS_LIST = JSON.parse(data[0]);
            HOLIDAY_LIST = JSON.parse(data[1]);

            MAKE_SCHEDULE_LIST_WEEK(CLASS_LIST);

        },
        error: function(e){
            alertApp('!', '스케줄 정보를 로드하지 못하였습니다.');
            return false;
        }
    });
}

// 초기 데이터 취득
function GET_AJAX_DATA(){

	$.ajax({
		url: "flow_controller.php?task=getGroupScheduleInitInfo",
		method: "POST",
		contentType: false,
		processData: false,
		success: function (result) {
			var data = result.split('|');
			MEMBER_LIST = JSON.parse(data[0]);
			TRAINER_LIST = JSON.parse(data[1]);
			ROOM_LIST = JSON.parse(data[2]);
			RESERVATION_SETTING = JSON.parse(data[3])[0];
			RESERVATION_STATUS_LIST = JSON.parse(data[4]);

            // console.group('유저리스트');
            // console.log(MEMBER_LIST);
            // console.groupEnd('유저리스트');

            // console.group('매니저 리스트');
            // console.log(TRAINER_LIST);
            // console.groupEnd('매니저 리스트');

            // console.group('룸리스트');
            // console.log(ROOM_LIST);
            // console.groupEnd('룸리스트');

            // console.group('예약설정값');
            // console.log(RESERVATION_SETTING);
            // console.groupEnd('예약설정값');

            // console.group('공통코드');
            // console.log(RESERVATION_STATUS_LIST);
            // console.groupEnd('공통코드');


            MAKE_ROOM_LIST(ROOM_LIST);
            MAKE_MEMBER_LIST(MEMBER_LIST);
            MAKE_TRAINER_LIST(TRAINER_LIST);

            let temp = addArrayText($('#dateChoice').val());
            GET_SCHEDULE(temp[0].split(' ')[0], temp[temp.length - 1].split(' ')[0], $('#whereChoice').val());

            // setTimeout(() => {
                MAKE_SCHEDULE_LIST_WEEK(CLASS_LIST.filter(e=>e.ROOM_SQ == $('#whereChoice').val()));
            // }, 1000);

		},
		error: function (e) {
            location.reload();
		}
	});
}



// 그룹수업 예약 모달창
function ADD_SOLO_CLASS(a,b,c,d){

    // b => period //
    ADD_SOLO_CLASS_RESET();

    NEW_FORM = false;
    PERIOD_OBJ = {};
    $('.solo.form-pop').removeClass('new').removeClass('edit').removeAttr('data-seq');

    PERIOD_OBJ = b;
    if(b[0] != undefined){     // 신규생성

        if($USER_GRADE < 3){
            if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 23) == -1){
                alertApp('X', '권한이 없습니다.');
                b.remove();
                return false;
            }
        }

        var selectLine = b.parent().parent();
        var selectLineIdx = selectLine.index();
        var selectDate = $('.jswscr2 > div.jqs-grid-day').eq(selectLineIdx).text().split(' ')[0];

        selectRoomObj = ROOM_LIST.filter(e => e.ROOM_SQ == $('#whereChoice').val())[0];

        NEW_FORM = true;
        $('.solo.form-pop').addClass('new');
        $('.jqs-options-remove').hide();
        $('#groupClassWhen1').val(selectDate);

        
    }else{  // 이미 있는 스케줄
        NEW_FORM = false;
        var SQ = b.firstElementChild.lastElementChild.getElementsByClassName('detail_info')[0].attributes[1].value;
        $('.solo.form-pop').addClass('edit').attr('data-seq',SQ);
        $('.jqs-options-remove').show();

        MAKE_CLASS_INFO(CLASS_LIST.filter(e => e.CLASS_SQ == SQ)[0]);
    }

    $('#time_period').text(d);
    $('.time_period').text(d)
                     .attr('data-start', d.split(' ~ ')[0])
                     .attr('data-end', d.split(' ~ ')[1]);

    $('.group').add($('.dark_div')).fadeIn(200);

}

// 이미 있는 수업 -> 모달에 정보 출력 (수정가능)
function MAKE_CLASS_INFO(data){

    // $('#solo_searchName').attr('data-seq', data.MEMBER);
    $('#groupClassName').val(data.CLASS_NAME);        // 그룹 수업명
    $('#groupClassWhere').val(data.ROOM);           // 장소
    $('#groupClassWhen1').val(data.CLASS_DT.split(' ')[0]);   // 기간1
    $('#groupClassWhen2').val(data.END_DT);         // 기간2
    $('#groupClassTeacher').val(data.MANAGER);      // 강사명
    $('#groupClassPay').val(data.MANAGER_PAY);      // 수업수당
    $('#groupClassCountO').val(data.TICKETING_PO_COUNT);      // 예약가능인원
    $('#groupClassCountX').val(data.TICKETING_ST_COUNT);      // 예약대기인원
    $('#pop_groupClassMemo').val(data.MEMO);

}

// 수업삭제
function REMOVE_GROUP_CLASS(SQ){
    var data = CLASS_LIST.filter(e => e.CLASS_SQ == SELECTED_CLASS)[0];
    if(Number(data.RESERV_COUNT) > 0){
        alertApp('!', '예약된 회원이 있습니다.');
        return false;
    }

    let formData = new FormData();
        formData.append('CLASS_SQ', data.CLASS_SQ);

    $.ajax({
        url: 'flow_controller.php?task=execClassDelete',
        method: 'POST',
        contentType: false,
        processData: false,
        data: formData,
        success: function (r){
            alertApp('O', '수업이 삭제되었습니다.');
            var rValue = $('#whereChoice').val();
            var tempDateArr = addArrayText($('#dateChoice').val());
            GET_SCHEDULE(tempDateArr[0],tempDateArr[tempDateArr.length - 1],rValue);
            return false;
        },
        error: function(e){
            alertApp('!', '다시 시도해주세요.');
            return false;
        }
    });
}


function MAKE_ROOM_LIST(list){
    var tag = '';
    $('#whereChoice').empty();

    for(let i in list){
        tag += '<option value="' + list[i].ROOM_SQ + '">' + list[i].ROOM_NAME + '</option>';
    }
    $('#whereChoice').append(tag);

    $('#whereChoice').change(function(){
        var rValue = $(this).val();
        var tempDateArr = addArrayText($('#dateChoice').val());
        GET_SCHEDULE(tempDateArr[0],tempDateArr[tempDateArr.length - 1],rValue);
    });
}

function MAKE_MEMBER_LIST(list){
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
        $('#groupSearchMember').attr('data-seq',seq).val(name);
        $('.mSearch_container').fadeOut(200);

        var tempObj = CLASS_LIST.filter(e => e.CLASS_SQ == SELECTED_CLASS)[0]
        GET_USER_VOUCHER_LIST(seq, tempObj.CLASS_DT.split(' ')[0]);
    });
}

function GET_USER_VOUCHER_LIST(USER_SQ, START_DT){
    USER_VOUCHER_LIST = [];
    let formData = new FormData();
        formData.append('USER_SQ', USER_SQ);
        formData.append('START_DT', START_DT);

    $.ajax({
        url: 'flow_controller.php?task=getUserGroupVoucherListSch',
        data: formData,
        method: 'POST',
        contentType: false,
        processData: false,
        success: function(r){
            let list = JSON.parse(r);
            USER_VOUCHER_LIST = list;
            $('#GticketChoice').html('<option value="">선택해주세요</option>');
            for(let i in list){
                $('#GticketChoice').append('<option value="' + list[i].UV_SQ + '">' + list[i].VOUCHER_NAME + '</option>');
            }
        },
        error: function(e){
            alertApp('!', '스케줄 정보를 로드하지 못하였습니다.');
            return false;
        }
    });
}

function MAKE_TRAINER_LIST(list){
    var tag = '<option value="">선택</option>';
    TRAINER_NAME_LIST = [];
    $('#pop_teacherChoice').add($('#teacherChoice')).add($('#groupClassTeacher')).empty();

    for(let i in list){
        TRAINER_NAME_LIST.push(list[i].USER_NM);
        tag +=
            '<option value="' + list[i].USER_SQ + '">' + list[i].USER_NM + '</option>'
    }

    $('#pop_teacherChoice').add($('#groupClassTeacher')).add($('#teacherChoice')).append(tag);
}


// 형식에 맞게 배열 재구성
function MAKE_IMPORT_CLASS_LIST(list){
    IMPORT_CLASS_LIST = [];

    for (let i in list) {
        var temp = {};

        var tempMemo = list[i].MEMO != '' ? ' (' + list[i].MEMO + ')' : '';
        temp.day = TRAINER_LIST.findIndex(e => e.USER_SQ == list[i].MANAGER);
        temp.periods = [
            {
                start : list[i].START_TIME,
                end : list[i].END_TIME,
                title :
                    '<p class="detail_info" data-seq="' + list[i].CLASS_SQ + '" style="font-size:14px;line-height:16px;font-weight:500">' +
                    MEMBER_LIST.filter(e => e.USER_SQ == list[i].MEMBER)[0].USER_NM + '회원님' +
                    tempMemo + '</p>'
            }
        ]
        IMPORT_CLASS_LIST.push(temp);
    }
}
// 형식에 맞게 배열 재구성
function MAKE_IMPORT_CLASS_LIST_WEEK(list){
    IMPORT_CLASS_LIST_WEEK = [];

    for (let i = 0; i < list.length; i++) {
        var data = list[i];

        if(TRAINER_LIST.filter(e => e.USER_SQ == data.MANAGER_SQ).length == 0){
            continue;
        }

        let date1 = new Date(data.CLASS_DT);

        // 그리기
        var temp = {};
        var day = date1.getDay();
        temp.day = day - 1;
        temp.periods = [
            {
                start : data.START_TIME,
                end : data.END_TIME,
                title :
                    '<p class="detail_info" data-seq="' + data.CLASS_SQ + '" style="padding-top: 2px;font-size:12px;line-height:18px;letter-spacing:1px;">' +
                        data.CLASS_NAME + ' / ' + 
                        ROOM_LIST.filter(e => e.ROOM_SQ == data.ROOM_SQ)[0].ROOM_NAME + '<br>' +
                        TRAINER_LIST.filter(e => e.USER_SQ == data.MANAGER_SQ)[0].USER_NM + ' / ' +
                        '[인원 : ' + (data.RESERV_COUNT == null ? '0' : data.RESERV_COUNT) + '/' + (data.RESERVATION_COUNT == null ? '0' : data.RESERVATION_COUNT) + '명 (대기:' + (data.WAIT_COUNT == null ? '0' : data.WAIT_COUNT) + ')]' +
                    '</p>'

            }
        ]
        IMPORT_CLASS_LIST_WEEK.push(temp);

    }
    return IMPORT_CLASS_LIST_WEEK;
}

function MAKE_SCHEDULE_LIST(tlist,slist){

    var option = {
        daysList: tlist,
        days: tlist.length,

        // onRemovePeriod: REMOVE_SOLO_CLASS,
        onClickPeriod: MAKE_RESERVATION_FORM,
        onMouseUp : ADD_SOLO_CLASS
    }

    $('#schedule-date').jqs(option);

    MAKE_IMPORT_CLASS_LIST(slist);

    $('#schedule-date').jqs('export');

    $('#schedule-date').jqs('import', IMPORT_CLASS_LIST);

    ////////////////////////////////////////////////////////////////////////////////

    $('.jqs-period').click(function(e){
        if (e.button == 2){
            $('#rightClick_div1').slideDown(200);
        }
    });
    $('#schedule-date .jqs-table td, #schedule-date .jqs-grid-day').css('min-width',(100/tlist.length) + '%');
}

function MAKE_RESERVATION_FORM(){
    var data = CLASS_LIST.filter(e => e.CLASS_SQ == SELECTED_CLASS)[0];

    $('#pop_div2').add($('.dark_div')).fadeIn(200);
    $('#pop_div2 > section').hide().eq(0).show();
    $('.jqs-options-remove').hide();

    GROUP_CLASS_TICKETING_RESET();
    $('#pop_div2 > section.reservationPopup > div > p').eq(0).html('이용권을 선택해주세요.');
    $('#pop_div2 > section.reservationPopup > div > p').eq(1).html(data.CLASS_NAME);
    $('#pop_div2 > section.reservationPopup > div > p').eq(2).html(ROOM_LIST.filter(e=>e.ROOM_SQ == data.ROOM_SQ)[0].ROOM_NAME);
    $('#pop_div2 > section.reservationPopup > div > p').eq(3).html(
        '현재 : ' + (data.RESERV_COUNT == null ? '0' : data.RESERV_COUNT) + '/' + data.RESERVATION_COUNT + '명 ' + 
        '- 대기 : ' + (data.WAIT_COUNT == null ? '0' : data.WAIT_COUNT) + '/' + data.WAITING_COUNT + '명'
    );
    $('#pop_div2 > section.reservationPopup > div > p').eq(4).html(
        '<span class="date_period">' +
            data.CLASS_DT.split(' ')[0].split('-')[0] + '년 ' +
            data.CLASS_DT.split(' ')[0].split('-')[1] + '월 ' +
            data.CLASS_DT.split(' ')[0].split('-')[2] + '일 (' +
            $day[new Date(data.CLASS_DT).getDay()] + '요일)' +
        '</span>' + '<span style="display:inline-block;margin:0 4px;">/</span> ' + '<span class="time_eriod">' +
            data.START_TIME + ' ~ ' + data.END_TIME +
        '</span>'
    );
    $('#pop_div2 > section.reservationPopup > div > p').eq(5).html(
        TRAINER_LIST.filter(e => e.USER_SQ == data.MANAGER_SQ)[0].USER_NM
    );
    $('#pop_div2 > section.reservationPopup > div.col9 > p').html(data.MEMO == '' ? '없음' : data.MEMO);
}

function GROUP_CLASS_TICKETING_RESET(){
    $('#groupSearchMember').val('');
    $('#GticketChoice').html('<option value="">선택해주세요</option>').val('');
    $('#pop_div2 > section > div > p').not($('#pop_div2 > section.reservMemberPopup > div > p')).html('');
    $('#groupClassMemo').val('');
}

function MAKE_SCHEDULE_LIST_WEEK(slist){

    $('#schedule-week .jqs-period').remove();

    var option = {
        daysList: addArrayText(dateFormat($('#dateChoice').val())),
        days: 7,

        onClickPeriod: MAKE_RESERVATION_FORM,
        onMouseUp : ADD_SOLO_CLASS
    }

    $('#schedule-week').jqs(option);
    MAKE_IMPORT_CLASS_LIST_WEEK(slist);

    $('#schedule-week').jqs('export');
    $('#schedule-week').jqs('reset');
    $('#schedule-week').jqs('import', IMPORT_CLASS_LIST_WEEK);


    ////////////////////////////////////////////////////////////////////////////////

    $('#schedule-week .jqs-table td, #schedule-week .jqs-grid-day').css('min-width',(100/7) + '%');
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
    return [tempList[0], tempList[tempList.length - 1]];
}

$(function(){
    GET_AJAX_DATA();

    $('.group > div.input > div > ul > li').click(function(){
        $(this).toggleClass('active');
    });

    $('.jqs-options-cancel').click(function(){
        if(NEW_FORM){   // 신규생성이면 할일
            PERIOD_OBJ.remove();
        }
        return false;
    });

////////////////////////////////////////////////////////////////////////////////



    $('#dateChoice').val(dateFormat($now)).change();

    var temp_list = addArrayText($('#dateChoice').val());

    // $('#date').text($now.getFullYear()+'년 '+($now.getMonth()+1)+'월 '+$now.getDate()+'일 ('+$day[$now.getDay()]+'요일)');
    $('#date').text(function(){
        var temp1 = temp_list[0].split('-');
        var temp2 = temp_list[temp_list.length - 1].split('-');

        return temp1[0] + '년 ' + temp1[1] + '월 ' + temp1[2].split(' ')[0] + '일 (' + temp1[2].split(' ')[1] + '요일) ~ ' +
                temp2[0] + '년 ' + temp2[1] + '월 ' + temp2[2].split(' ')[0] + '일 (' + temp2[2].split(' ')[1] + '요일)';
    });

    // $('#dateChoice').change(function(){
    //     $now = new Date($(this).val());
    //     console.log($now);
    //     $('#date').text($now.getFullYear()+'년 '+($now.getMonth()+1)+'월 '+$now.getDate()+'일 ('+$day[$now.getDay()]+'요일)');

    //     var temp_list = addArrayText($(this).val());

    //     for(let i = 0; i < temp_list.length; i++){
    //         let target = $('.jqs-grid-day').eq(i);
    //         let text = temp_list[i];
    //         target[0].html('');
    //         target[0].html(text);
    //     }

    //     GET_AJAX_DATA();



    // });



    var changeStartDate = new Date();
    var changeEndDate = new Date();

    // 날짜--
    $('#datePrev').click(function(){
        PN_date(-7);
    });

    // 날짜++
    $('#dateNext').click(function(){
        PN_date(7);
    });

    // 날짜 하루전/하루후
    function PN_date(n){
        var date = new Date($('#dateChoice').val());
        date.setDate(date.getDate() + n);

        $('#dateChoice').val(dateFormat(date)).change();

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


        // let y = $now.getFullYear();
        // let m = String($now.getMonth() + 1).length == 1 ? '0' + String($now.getMonth() + 1) : String($now.getMonth() + 1);
        // let d = String($now.getDate()).length == 1 ? '0' + String($now.getDate()) : String($now.getDate());
        // let dd = $day[$now.getDay()];
        // let first = y + '년 ' + m + '월 ' + d + '일 (' + dd + '요일)';
        
        // let tempDate = $now;
        // tempDate.setDate(tempDate.getDate() + 6);
        // y = tempDate.getFullYear();
        // m = String(tempDate.getMonth() + 1).length == 1 ? '0' + String(tempDate.getMonth() + 1) : String(tempDate.getMonth() + 1);
        // d = String(tempDate.getDate()).length == 1 ? '0' + String(tempDate.getDate()) : String(tempDate.getDate());
        // dd = $day[tempDate.getDay()];
        // let last = y + '년 ' + m + '월 ' + d + '일 (' + dd + '요일)';
        
        // $('#date').text(first + ' ~ ' + last);
        console.log($('#date').text());
    }

    // 날짜선택할때
    $('#dateChoice').change(function(){
        var dValue = $('#dateChoice').val();
        var rValue = $('#whereChoice').val();
        var tempDateArr = addArrayText(dValue);
        selectedDate = dValue;

        var startEndDT = VIEW_WEEK_DATE(dValue);
        GET_SCHEDULE(startEndDT[0],startEndDT[1],rValue);
    });


    $(document).click(function(){
        $('.rightClick_div').hide();
    });
    $('.rightClick_div').mouseleave(function(){
        $('.rightClick_div').hide();
    });


    // dark_div 클릭
    $('.dark_div').add('.jqs-options-cancel').click(() => {
        if($('.solo.form-pop').attr('class').indexOf('new') > -1){
            PERIOD_OBJ.remove();
        }
        justClose();
    });

    function justClose(){
        $('.dark_div').fadeOut(200);
        $('.form-pop').fadeOut(200);
    }


    function GET_RESERVATION_MEMBER(CLASS_SQ){
        let formData = new FormData();
            formData.append('CLASS_SQ',CLASS_SQ);

        $.ajax({
            url: 'flow_controller.php?task=getClassReservedUserList',
            data: formData,
            method: 'POST',
            processData: false,
            contentType: false,
            success: function (r){
                var data = r.split('|')[0];
                RESERVATION_MEMBER_LIST = JSON.parse(data);
                MAKE_RESERVATION_MEMBER(RESERVATION_MEMBER_LIST);
            },
            error: function(e){

            }
        });
    }
    function MAKE_RESERVATION_MEMBER(list){
        $('#pop_div2 > section.reservMemberPopup > div.table > table > tbody').empty();
        $('#pop_div2 > section.reservMemberPopup > div.count > p > span').text('0명');
        $('#TICKETTING_CHECKALL').prop('checked', false);
        for(let i in list){
            $('#pop_div2 > section.reservMemberPopup > div.table > table > tbody').append(
                `<tr data-class-reserv-sq="${list[i].CLASS_RESERV_SQ}">
                    <td>
                        <label class="hid" for="TICKETTING_CHECK${list[i].CLASS_RESERV_SQ}">선택</label>
                        <input id="TICKETTING_CHECK${list[i].CLASS_RESERV_SQ}" 
                               type="checkbox" 
                               class="TICKETTING_CHECK" 
                               data-seq="${list[i].CLASS_RESERV_SQ}"
                               data-state="${list[i].RESERV_STATUS}"
                               data-uv-seq="${list[i].UV_SQ}"
                        >
                    </td>
                    <td>${list[i].USER_NM}</td>
                    <td>${list[i].PHONE_NO}</td>
                    <td>${list[i].RESERV_DT.split(' ')[0]}</td>
                    <td data-state="${list[i].RESERV_STATUS}">${list[i].RESERV_STATUS_NAME}</td>
                </tr>`
            );
        }
        
        $('#pop_div2 > section.reservMemberPopup > div.count > p > span').eq(0).text(list.length + '명');
        $('#pop_div2 > section.reservMemberPopup > div.count > p > span').eq(1).text(list.filter(e => e.RESERV_STATUS == 3).length + '명');
        $('#pop_div2 > section.reservMemberPopup > div.count > p > span').eq(2).text(list.filter(e => e.RESERV_STATUS == 4).length + '명');

        var ticketting_checkbox_all = $('#TICKETTING_CHECKALL');
        var ticketting_checkbox = $('#pop_div2 div.table input.TICKETTING_CHECK');
        var ticketting_state_btn = $('#pop_div2 > section.reservMemberPopup > .ticketting_btnSet > div > button');

        // 체크가 되어있는 멤버들의 배열
        var list = [];      // 배열정의
        // 체크박스가 클릭 함수
        ticketting_checkbox.click(function(){
            list = [];  //초기화
            ticketting_checkbox.each(function(){
                if($(this).prop('checked')){
                    list.push($(this).attr('data-seq'));
                }
            });

            // 체크박스가 하나라도 체크되어있으면 버튼 visible
            list.length != 0 ? ticketting_state_btn.show() : ticketting_state_btn.hide();

            // 체크박스가 전체 다 체크되어있으면 전체체크
            if(list.length == ticketting_checkbox.length){
                ticketting_checkbox_all.prop('checked',true);
            }else{
                ticketting_checkbox_all.prop('checked',false);
            }

        });//

        // 수업 예약자 목록 전체 체크
        ticketting_checkbox_all.click(function(){
            if($(this).is(':checked')){
                ticketting_checkbox.prop('checked', true);
                ticketting_state_btn.show();
            }else{
                ticketting_checkbox.prop('checked', false);
                ticketting_state_btn.hide();
            }
        });

    }

    $('#pop_div2 > section.reservMemberPopup > .ticketting_btnSet > div > button').click(function(){
        var i = $(this).index();
        var data = CLASS_LIST.filter(e => e.CLASS_SQ == SELECTED_CLASS)[0];
        var formData = new FormData();
        var checkedCheckBox = $('#pop_div2 div.table input.TICKETTING_CHECK:checked');
        var STATE = ['예약상태', '예약', '예약취소', '출석', '결석', '이용중지', '예약대기'];

        var ask = confirm('선택한 ' + checkedCheckBox.length + '명의 회원을 ' + ['출석', '결석', '취소'][i] + '처리 하시겠습니까?');

        if(!ask){
            return false;
        }


        if(i == 0){ // 출석

            for(let ii = 0; ii < checkedCheckBox.length; ii++){
                formData.append('CLASS_RESERV_SQ' ,checkedCheckBox.eq(ii).attr('data-seq'));
                formData.append('UV_SQ' , checkedCheckBox.eq(ii).attr('data-uv-seq'));
                formData.append('CLASS_SQ' , SELECTED_CLASS);

                if(checkedCheckBox.eq(ii).attr('data-state') != 1){
                    alertApp('!','이미 ' + STATE[checkedCheckBox.eq(ii).attr('data-state')] + '된 회원입니다.');
                    return false;
                }

                $.ajax({
                    url: 'flow_controller.php?task=execUserGroupScheduleAttend',
                    data: formData,
                    method: 'POST',
                    processData: false,
                    contentType: false,
                    success: function (r){
                        GET_RESERVATION_MEMBER(SELECTED_CLASS);
                        alertApp('O', '해당 회원이 출석처리 되었습니다.');
                    },
                    error: function(e){

                    }
                });
            }

        }else if(i == 1){   // 결석

            for(let ii = 0; ii < checkedCheckBox.length; ii++){
                formData.append('CLASS_RESERV_SQ' ,checkedCheckBox.eq(ii).attr('data-seq'));
                if(RESERVATION_SETTING.GRP_ABSENCE_TICKET == 0){
                    formData.append('UV_SQ' , checkedCheckBox.eq(ii).attr('data-uv-seq'));
                }else{
                    formData.append('UV_SQ' , '0');
                }
                formData.append('CLASS_SQ' , SELECTED_CLASS);

                if(checkedCheckBox.eq(ii).attr('data-state') != 1){
                    alertApp('!','이미 ' + STATE[checkedCheckBox.eq(ii).attr('data-state')] + '된 회원입니다.');
                    return false;
                }
                
                $.ajax({
                    url: 'flow_controller.php?task=execUserGroupScheduleAbsence',
                    data: formData,
                    method: 'POST',
                    processData: false,
                    contentType: false,
                    success: function (r){
                        GET_RESERVATION_MEMBER(SELECTED_CLASS);
                        alertApp('O', '해당 회원이 결석처리 되었습니다.');
                    },
                    error: function(e){

                    }
                });
            }

        }else if(i == 2){   // 취소

            var timeChk = () => {
                var type = RESERVATION_SETTING.GRP_MOD_TYPE;
                var time = RESERVATION_SETTING.GRP_MOD_TIME;
                var classTime = new Date(data.CLASS_DT);
                    classTime.setHours(data.START_TIME.split(':')[0]);
                    classTime.setMinutes(data.START_TIME.split(':')[1]);
                var tempTime = new Date();

                var classDate = classTime.getFullYear() + (String(classTime.getMonth()).length == 1 ? '0' + String(classTime.getMonth()) : String(classTime.getMonth())) + (String(classTime.getDate()).length == 1 ? '0' + String(classTime.getDate()) : String(classTime.getDate()));
                var nowDate = tempTime.getFullYear() + (String(tempTime.getMonth()).length == 1 ? '0' + String(tempTime.getMonth()) : String(tempTime.getMonth())) + (String(tempTime.getDate()).length == 1 ? '0' + String(tempTime.getDate()) : String(tempTime.getDate()));

                if(type == 0 && classTime - tempTime > 0){
                    return true;
                }else if(type == 1){
                    return false;
                }else if(type == 2 && classDate - nowDate > 0){
                    return true;
                }else if(type == 3){
                    tempTime.setMinutes(tempTime.getMinutes() + Number(time));
                    if(classTime - tempTime > 0){
                        return true;
                    }
                }
                return false;
            }

            if(!timeChk()){
                alertApp('X','현재 취소가 불가능합니다');
                return false;
            }

            for(let ii = 0; ii < checkedCheckBox.length; ii++){
                formData.append('CLASS_RESERV_SQ' ,checkedCheckBox.eq(ii).attr('data-seq'));
                formData.append('CLASS_SQ' , SELECTED_CLASS);
                formData.append('RESERV_STATUS', checkedCheckBox.eq(ii).attr('data-state'));

                if(checkedCheckBox.eq(ii).attr('data-state') != 1){
                    alertApp('!','이미 ' + STATE[checkedCheckBox.eq(ii).attr('data-state')] + '된 회원입니다.');
                    return false;
                }
                
                $.ajax({
                    url: 'flow_controller.php?task=execUserGroupScheduleCancel',
                    data: formData,
                    method: 'POST',
                    processData: false,
                    contentType: false,
                    success: function (r){
                        GET_RESERVATION_MEMBER(SELECTED_CLASS);
                        alertApp('O', '해당 회원의 예약이 취소되었습니다.');
                    },
                    error: function(e){

                    }
                });
            }

        }

    });


    //   우클릭 메뉴 클릭 // 그룹레슨
    $('#rightClick_div2 > li').click(function(){
        var i = $(this).index();
        var data = CLASS_LIST.filter(e => e.CLASS_SQ == SELECTED_CLASS)[0];

        if(i == 0){
            $('#pop_div2').add($('.dark_div')).fadeIn(200);
            $('#pop_div2 > section').hide().eq(i).show();
            $('.jqs-options-remove').hide();

            GROUP_CLASS_TICKETING_RESET();
            $('#pop_div2 > section.reservationPopup > div > p').eq(1).html(data.CLASS_NAME);
            $('#pop_div2 > section.reservationPopup > div > p').eq(2).html(ROOM_LIST.filter(e=>e.ROOM_SQ == data.ROOM_SQ)[0].ROOM_NAME);
            $('#pop_div2 > section.reservationPopup > div > p').eq(3).html(
                '총 예약가능 인원 : ' + data.RESERVATION_COUNT + '명<br>' + 
                '현재 : ' + (data.RESERV_COUNT == null ? 0 : data.RESERV_COUNT) + '명 / ' + 
                '대기 : ' + (data.WAIT_COUNT == null ? 0 : data.WAIT_COUNT) + '명'
            );
            $('#pop_div2 > section.reservationPopup > div > p').eq(4).html(
                '<span class="date_period">' +
                    data.CLASS_DT.split(' ')[0].split('-')[0] + '년 ' +
                    data.CLASS_DT.split(' ')[0].split('-')[1] + '월 ' +
                    data.CLASS_DT.split(' ')[0].split('-')[2] + '일 (' +
                    $day[new Date(data.CLASS_DT.split(' ')[0]).getDay()] + '요일)' +
                '</span> / <span class="time_eriod">' +
                    data.START_TIME + ' ~ ' + data.END_TIME +
                '</span>'
            );
            $('#pop_div2 > section.reservationPopup > div > p').eq(5).html(
                TRAINER_LIST.filter(e => e.USER_SQ == data.MANAGER_SQ)[0].USER_NM
            );
            $('#pop_div2 > section.reservationPopup > div.col9 > p').html(data.MEMO);

        }else if(i == 1){
            $('#pop_div2').add($('.dark_div')).fadeIn(200);
            $('#pop_div2 > section').hide().eq(i).show();
            $('.jqs-options-remove').hide();
            
            $('#pop_div2 > section.reservMemberPopup > div.count > p').eq(0).html(
                '참석회원 수 : <span style="margin-left:4px">' +
                (data.WAIT_COUNT == null ? '0' : data.WAIT_COUNT) + 
                '명</span>'
            );
            $('#pop_div2 > section.reservMemberPopup > div.count > p').eq(1).html(
                '출석 : <span style="margin-left:4px">' +
                // (data.WAIT_COUNT == null ? '0' : data.WAIT_COUNT) + 
                '출석' +
                '명</span>'
            );
            $('#pop_div2 > section.reservMemberPopup > div.count > p').eq(2).html(
                '결석 : <span style="margin-left:4px">' +
                // (data.WAIT_COUNT == null ? '0' : data.WAIT_COUNT) + 
                '결석' +
                '명</span>'
            );

            GET_RESERVATION_MEMBER(data.CLASS_SQ);

        }else if(i == 2){
            $('#pop_div2').add($('.dark_div')).fadeIn(200);
            $('#pop_div2 > section').hide().eq(i).show();
            $('.jqs-options-remove').hide();

            $('#pop_div2 > section.groupClassInfoPopup > div > p').eq(0).html(data.CLASS_NAME);
            $('#pop_div2 > section.groupClassInfoPopup > div > p').eq(1).html(ROOM_LIST.filter(e=>e.ROOM_SQ == data.ROOM_SQ)[0].ROOM_NAME);
            $('#pop_div2 > section.groupClassInfoPopup > div > p').eq(2).html(data.START_TIME + ' ~ ' + data.END_TIME);
            $('#pop_div2 > section.groupClassInfoPopup > div > p').eq(3).html(TRAINER_LIST.filter(e => e.USER_SQ == data.MANAGER_SQ)[0].USER_NM);
            $('#pop_div2 > section.groupClassInfoPopup > div > p').eq(4).html(
                (data.RESERV_COUNT == null ? '0' : data.RESERV_COUNT) + '/' + data.RESERVATION_COUNT + '명'
            );
            $('#pop_div2 > section.groupClassInfoPopup > div > p').eq(5).html(
                (data.WAIT_COUNT == null ? '0' : data.WAIT_COUNT) + '/' + data.WAITING_COUNT + '명'
            );
            $('#pop_div2 > section.groupClassInfoPopup > div > p').eq(6).html(data.MEMO);
            $('#groupClassMemo').val(data.CLASS_MEMO);

            $('div.groupClassMemo').fadeIn(200);
        }else if(i == 3){

            if($USER_GRADE < 3){
                if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 24) == -1){
                    alertApp('X', '권한이 없습니다.');
                    return false;
                }
            }

            let classInfo = CLASS_LIST.filter(e => e.CLASS_SQ == SELECTED_CLASS)[0];
            if(classInfo.RESERV_COUNT == 0 || classInfo.RESERV_COUNT == null){
                
            }else{
                alertApp('!', '예약되어 있는 회원이 있습니다.');
                $('#rightClick_div2').fadeOut(200);
                return false;
            }
            REMOVE_GROUP_CLASS(data.CLASS_SQ);
        }
            

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
    

    // 그룹수업 등록 모달 등록버튼
    $('#addClassBtn').click(function(){
        

        var CLASS_NAME = $('#groupClassName').val();
        var ROOM_SQ = $('#groupClassWhere').attr('data-seq');
        var START_DT = $('#groupClassWhen1').val();
        var END_DT = $('#groupClassWhen2').val();
        var MON = $('.group > div.input > div > ul > li').eq(0).hasClass('active') ? '1' : '0';
        var TUE = $('.group > div.input > div > ul > li').eq(1).hasClass('active') ? '1' : '0';
        var WED = $('.group > div.input > div > ul > li').eq(2).hasClass('active') ? '1' : '0';
        var THU = $('.group > div.input > div > ul > li').eq(3).hasClass('active') ? '1' : '0';
        var FRI = $('.group > div.input > div > ul > li').eq(4).hasClass('active') ? '1' : '0';
        var SAT = $('.group > div.input > div > ul > li').eq(5).hasClass('active') ? '1' : '0';
        var SUN = $('.group > div.input > div > ul > li').eq(6).hasClass('active') ? '1' : '0';
        var START_TIME = $('.group > div.input > div.col4 > p').attr('data-start');
        var END_TIME = $('.group > div.input > div.col4 > p').attr('data-end');
        var MANAGER_SQ = $('#groupClassTeacher').val();
        var BENEFITS = $('#groupClassPay').val();
        var RESERVATION_COUNT = $('#groupClassCountO').val();
        var WAITING_COUNT = $('#groupClassCountX').val();
        var MEMO = $('#pop_groupClassMemo').val();

        if(CLASS_NAME == ''){
            alertApp('!', '수업명을 입력해주세요.');
            return false;
        }
        if(START_DT == ''){
            alertApp('!', '날짜를 선택해주세요.');
            return false;
        }
        if(END_DT == ''){
            alertApp('!', '날짜를 선택해주세요.');
            return false;
        }
        if(MANAGER_SQ == ''){
            alertApp('!', '강사를 선택해주세요.');
            return false;
        }
        if(BENEFITS == ''){
            alertApp('!', '수업수당을 입력해주세요.');
            return false;
        }
        if(RESERVATION_COUNT == ''){
            alertApp('!', '예약가능인원을 입력해주세요.');
            return false;
        }
        if(WAITING_COUNT == ''){
            alertApp('!', '예약대기인원을 입력해주세요.');
            return false;
        }

        let formData = new FormData();
            formData.append('CLASS_NAME', CLASS_NAME);
            formData.append('ROOM_SQ', ROOM_SQ);
            formData.append('START_DT', START_DT);
            formData.append('END_DT', END_DT);
            formData.append('MON', MON);
            formData.append('TUE', TUE);
            formData.append('WED', WED);
            formData.append('THU', THU);
            formData.append('FRI', FRI);
            formData.append('SAT', SAT);
            formData.append('SUN', SUN);
            formData.append('START_TIME', START_TIME);
            formData.append('END_TIME', END_TIME);
            formData.append('MANAGER_SQ', MANAGER_SQ);
            formData.append('BENEFITS', BENEFITS);
            formData.append('RESERVATION_COUNT', RESERVATION_COUNT);
            formData.append('WAITING_COUNT', WAITING_COUNT);
            formData.append('MEMO', MEMO);

        $.ajax({
            url: 'flow_controller.php?task=execClassScheduleSave',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(r){
                let data = JSON.parse(r);
                if(data.result == 'Fail'){
                    alertApp('X', '해당 시간에는 수업을 등록할 수 없습니다.');
                    return false;
                }
                let tempDateArr = addArrayText($('#dateChoice').val());
                let roomSQ = $('#whereChoice').val();
                GET_SCHEDULE(tempDateArr[0], tempDateArr[tempDateArr.length - 1], roomSQ);
                $('.group.form-pop').add($('.dark_div')).fadeOut(200);
                alertApp('O', '그룹레슨이 등록되었습니다.');
                return false;
            },
            error: function(e){
                alertApp('X', '등록 실패하였습니다.');
                return false;
            }
        })


    });


    // 예약
    $('#GticketChoice').change(function(){
        if($(this).val() == ''){
            $('#pop_div2 > section > div.col3 > p').html('이용권을 선택해주세요.');
            return false;
        }
        let obj = USER_VOUCHER_LIST.filter(e => e.UV_SQ == $(this).val())[0];
        $('#pop_div2 > section > div.col3 > p').html(
            '<span style="display:block;width:100%;">담당: ' + obj.SELLER_NM + 
            ' / 만료: ' + obj.USE_LASTDATE.split(' ')[0] + '</span><br>' +
            '<span style="display:block;width:100%;">이용: ' + obj.USEDCOUNT + 
            ' / 잔여: ' + obj.COUNT + ' / 예약: ' + obj.RESERV_COUNT + ' / 예약가능수: ' + (obj.COUNT - obj.USEDCOUNT - obj.RESERV_COUNT) + '</span>'
        );
    });

    // 예약 버튼
    $('#userReservBtn').click(function(){
        var data = CLASS_LIST.filter(e => e.CLASS_SQ == SELECTED_CLASS)[0];

        var timeChk = () => {
            var type = RESERVATION_SETTING.GRP_RESERV_TYPE;
            var time = RESERVATION_SETTING.GRP_RESERV_TIME;
            var classTime = new Date(data.CLASS_DT);
                classTime.setHours(data.START_TIME.split(':')[0]);
                classTime.setMinutes(data.START_TIME.split(':')[1]);
            var tempTime = new Date();

            if(type == 0 && classTime - tempTime > 0){
                return true;
            }else if(type == 1){
                tempTime.setMinutes(tempTime.getMinutes() + Number(time));
                if(classTime - tempTime > 0){
                    return true;
                }
            }
            return false;
        }

        if(!timeChk()){
            alertApp('X','예약이 불가능한 시간입니다.');
            return false;
        }

        if($('#groupSearchMember').val() == ''){
            alertApp('!', '회원을 선택해주세요.');
            return false;
        }
        if($('#GticketChoice').val() == ''){
            alertApp('!', '이용권을 선택해주세요.');
            return false;
        }

        
        
        var CLASS_SQ = SELECTED_CLASS;
        var USER_SQ = $('#groupSearchMember').attr('data-seq');
        var UV_SQ = $('#GticketChoice').val();

        var selectUserVoucher = USER_VOUCHER_LIST.filter(e => e.UV_SQ == UV_SQ)[0];
        var remainCount = Number(selectUserVoucher.COUNT) - Number(selectUserVoucher.USEDCOUNT);
        if(remainCount <= 0){
            alertApp('!', '이용권의 예약가능 횟수가 없습니다.');
            return false;
        }
        if(remainCount - Number(selectUserVoucher.RESERV_COUNT) <= 0){
            alertApp('!', '이용권의 예약가능 횟수가 없습니다.');
            return false;
        }
        
        let formData = new FormData();
            formData.append('CLASS_SQ', CLASS_SQ);
            formData.append('USER_SQ', USER_SQ);
            formData.append('UV_SQ', UV_SQ);

        $.ajax({
            url: 'flow_controller.php?task=execClassReservAdd',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(r){
                if(r.indexOf('|') == -1){
                    let data = JSON.parse(r);

                    if(data.result == 'Fail'){
                        alertApp('X', '예약인원을 초과하였습니다.');
                        return false;
                    }
                }
                
                let tempDateArr = addArrayText($('#dateChoice').val());
                let roomSQ = $('#whereChoice').val();
                GET_SCHEDULE(tempDateArr[0], tempDateArr[tempDateArr.length - 1], roomSQ);

                alertApp('O', '예약이 완료되었습니다.');
                $('.dark_div').add($('#pop_div2')).fadeOut(200);
                return false;
            },
            error: function(e){
                alertApp('X', '예약을 실패하였습니다.');
                return false;
            }
        });
    });



    $('#groupClassWhen2').change(function(){
        var a = new Date($('#groupClassWhen1').val());
        var b = new Date($(this).val());
        if(a == ''){
            alert('시작할 기간을 적어주세요.');
            return false;
        }else{
            if(b-a < 0){
                $(this).val('');
                return false;
            }
        }
    });

    // 그룹수업 예약자 목록 회원검색하기
    $('#ticketting_member_search').keyup(function(){
        var text = $(this).val().toLowerCase().replaceAll(' ','');
        $('div.table tbody > tr').filter(function(){
            $(this).toggle(
                $(this).text().replaceAll(' ','').toLowerCase().indexOf(text) > -1
            );
        });
    });

    // 예약목록
    $('#pop_div2 > section').eq(0).find('.btnSet > button').click(function(){
        $('article.ticketting').fadeIn(200);
    });

    // 수업정보 닫기
    $('section.groupClassInfoPopup > div.closeX').add($('.ticketting_btnSet > .close')).click(function(){
        $('#pop_div2').add($('.dark_div')).add($('div.groupClassMemo')).fadeOut(100);
    });


    // 수업내용메모
    $('#groupClassMemoSetBtn').click(function(){
        if($USER_GRADE < 3){
            if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 24) == -1){
                alertApp('X', '권한이 없습니다.');
                return false;
            }
        }

        if($(this).text() == '수 정'){
            $(this).text('저 장');
            $('#groupClassMemo').focus().prop('readonly',false);
        }else{
            $(this).text('수 정');
            $('#groupClassMemo').prop('readonly',true);
            var val = $('#groupClassMemo').val();
            var formData = new FormData();
                formData.append('CLASS_SQ', SELECTED_CLASS);
                formData.append('CLASS_MEMO', val);

            $.ajax({
                url: 'flow_controller.php?task=execClassMemoModify',
                method: 'POST',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(r){
                    var data = JSON.parse(r);
                    if(data.result == 'Update Fail'){
                        alertApp('X', '메모가 저장되지 않았습니다.');
                        return false;
                    }

                    alertApp('O', '메모가 저장었습니다.');
                    var idx = CLASS_LIST.findIndex(e => e.CLASS_SQ == SELECTED_CLASS);
                    CLASS_LIST[idx].CLASS_MEMO = data[0].CLASS_MEMO;
                },
                error: function(e){
                    alertApp('X', '메모가 저장되지 않았습니다.');
                    return false;
                }
            })
        }
    });


    //(작업중)
    // setTimeout(() => {
    //     $('#teacherChoice').val(73).change();
    // },300);
});
