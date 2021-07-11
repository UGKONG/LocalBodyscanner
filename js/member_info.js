var TRAINER_LIST;
var MEMBERINFO;
var MEASUREMENT_BODY_DATE;
var MEASUREMENT_POSE_DATE;
var MEASUREMENT_ROM_DATE;
var MEASUREMENT_DESA_DATE;
var MEASUREMENT_BODY_DATA;
var MEASUREMENT_POSE_DATA;
var MEASUREMENT_ROM_DATA;
var MEASUREMENT_DESA_DATA;
var RANGE_POSE;
var RANGE_ROM;
var ALL_CENTER;
var MEASUREMENT_BODY_IMG;
var errorMuscle = {
    frontRightNeck : false,
    frontLeftNect : false,
    frontRightShoulder : false,
    frontLeftShoulder : false,
    frontRightPelvis : false,
    frontRightLeg : false,
    frontLeftLeg : false,
    sideFrontNeck : false,
    sideBackNeck : false,
    sideFrontShoulder_R : false,
    sideBackShoulder_R : false,
    sideFrontShoulder_L : false,
    sideBackShoulder_L : false,
    sideFrontPelvis_R : false,
    sideBackPelvis_R : false,
    sideFrontPelvis_L : false,
    sideBackPelvis_L : false,
    sideFrontLeg_R : false,
    sideBackLeg_R : false,
    sideFrontLeg_L : false,
    sideBackLeg_L : false
}
// Chart 전역변수
var CHART_TYPE_BODY;
var timeFormat = 'YYYY-MM-DD';

var bodyGraph;
var poseGraph;
var poseGraph1;
var poseGraph2;

var GraphLineName = {
    front_Neck : '머리',
    front_RShoulder : '오른쪽',
    front_LShoulder : '왼쪽',
    front_RPelvis : '오른쪽',
    front_LPelvis : '왼쪽',
    front_RLeg : '오른쪽',
    front_LLeg : '왼쪽',
    side_Neck : '머리',
    side_Shoulder : '어깨',
    side_Pelvis : '골반',
    side_Leg : '다리',
    HEIGHT : '신장',
    WEIGHT : '체중',
    BMI : 'BMI',
    FAT : '체지방량',
    MUSCLE : '근육량',

    front_Neck_right : '오른쪽',
    front_Neck_left : '왼쪽',
    front_Shoulder_right : '오른쪽',
    front_Shoulder_left : '왼쪽',
    front_Waist_right : '오른쪽',
    front_Waist_left : '왼쪽',
    front_Hip_right : '오른쪽',
    front_Hip_left : '왼쪽',
    side_Neck_front : 'Front',
    side_Neck_back : 'Back',
    side_ShoulderR_front : '오른쪽 Front',
    side_ShoulderR_back : '오른쪽 Back',
    side_ShoulderL_front : '왼쪽 Front',
    side_ShoulderL_back : '왼쪽 Back',
    side_Waist_front : 'Front',
    side_Waist_back : 'Back',
    side_HipR_front : '오른쪽 Front',
    side_HipR_back : '오른쪽 Back',
    side_HipL_front : '왼쪽 Front',
    side_HipL_back : '왼쪽 Back'
};

var muscleSVG = {
    Neck_right_blue : ['front_F_2','front_F_4','front_F_10_1','front_F_10_2','front_F_10_3','front_F_11_1','front_F_11_2','front_F_11_3','front_F_16','front_F_17','front_B_1','front_B_4'],
    Neck_right_red : ['front_F_1','front_F_3','front_B_2','front_B_5'],
    Neck_left_blue : ['front_F_1','front_F_3','front_F_10_1','front_F_10_2','front_F_10_3','front_F_11_1','front_F_11_2','front_F_16','front_F_17','front_F_11_3','front_B_2','front_B_5'],
    Neck_left_red : ['front_F_2','front_F_4','front_B_1','front_B_4'],
    Shoulder_right_up_blue : ['front_B_9','front_B_13','front_B_2','front_F_10_1','front_F_10_2','front_F_10_3','front_B_5','front_B_8','front_B_11'],
    Shoulder_right_up_red : ['front_F_3','front_F_5','front_F_6','front_B_2','front_B_6'],
    Shoulder_right_down_blue : ['front_F_3','front_F_6','front_F_9','front_F_13','front_F_10_1','front_F_10_2','front_F_10_3','front_B_2','front_B_5','front_B_8'],
    Shoulder_right_down_red : ['front_B_11','front_B_14'],
    Shoulder_left_up_blue : ['front_F_11_1','front_F_11_2','front_F_11_3','front_F_12','front_F_14','front_B_4','front_B_7','front_B_10'],
    Shoulder_left_up_red : ['front_F_4','front_F_7','front_F_8','front_B_1','front_B_3'],
    Shoulder_left_down_blue : ['front_F_4','front_F_7','front_F_11_1','front_F_11_2','front_F_11_3','front_F_12','front_F_14','front_B_1','front_B_4','front_B_7'],
    Shoulder_left_down_red : ['front_B_10','front_B_13'],
    Pelvis_right_up_blue : ['front_F_15','front_F_16','front_F_17','front_F_18','front_F_22','front_B_10','front_B_13','front_B_15'],
    Pelvis_right_up_red : ['front_F_13','front_F_21','front_B_11','front_B_14','front_B_16'],
    Pelvis_right_down_blue : ['front_F_15','front_F_16','front_F_17','front_F_18','front_F_21','front_B_11','front_B_14','front_B_16','front_B_19','front_B_20'],
    Pelvis_right_down_red : ['front_F_23','front_F_24','front_B_13'],
    Pelvis_left_up_blue : ['front_F_15','front_F_16','front_F_17','front_F_18','front_F_21','front_B_11','front_B_14','front_B_16'],
    Pelvis_left_up_red : ['front_F_14','front_F_22','front_B_10','front_B_13','front_B_15'],
    Pelvis_left_down_blue : ['front_F_15','front_F_16','front_F_17','front_F_18','front_F_22','front_B_10','front_B_13','front_B_15','front_B_17','front_B_18'],
    Pelvis_left_down_red : ['front_F_19','front_F_20','front_B_14'],
    Leg_right_o_blue : ['front_F_15','front_F_16','front_F_17','front_F_18','front_F_21','front_B_14','front_B_16','front_B_19'],
    Leg_right_o_red : ['front_F_19','front_F_20','front_B_20'],
    Leg_left_o_blue : ['front_F_15','front_F_16','front_F_17','front_F_18','front_F_22','front_B_13','front_B_15','front_B_18'],
    Leg_left_o_red : ['front_F_23','front_F_24','front_B_17'],
    Leg_right_x_blue : ['front_F_15','front_F_16','front_F_17','front_F_18','front_F_20','front_B_16','front_B_19','front_B_20'],
    Leg_right_x_red : ['front_F_21','front_F_19','front_F_25','front_B_23','front_B_24'],
    Leg_left_x_blue : ['front_F_15','front_F_16','front_F_17','front_F_18','front_F_23','front_B_15','front_B_17','front_B_18'],
    Leg_left_x_red : ['front_F_22','front_F_24','front_F_26','front_B_21','front_B_22'],

    Neck_front_blue : ['side_F_3','side_F_4','side_B_4','side_B_5','side_B_7','side_B_8','side_B_10','side_B_11'],
    Neck_front_red : ['side_F_1','side_F_2','side_F_6','side_F_7','side_B_1','side_B_2'],
    Shoulder_front_blue : ['side_F_9','side_F_12','side_F_16','side_F_17','side_B_4','side_B_5','side_B_7','side_B_8','side_B_10','side_B_11'],
    Shoulder_front_red : ['side_F_1','side_F_2','side_F_6','side_F_7','side_B_1','side_B_2','side_B_1_1','side_B_2_1'],
    Pelvis_front_blue : ['side_F_16','side_F_17','side_B_15','side_B_16','side_B_17','side_B_18','side_B_19','side_B_20'],
    Pelvis_front_red : ['side_F_20','side_F_21','side_F_22','side_F_23','side_B_10','side_B_11'],
    Pelvis_back_blue : ['side_F_16','side_F_17','side_F_21','side_F_22','side_F_20','side_F_23','side_F_25','side_F_26','side_B_10','side_B_11','side_B_15','side_B_16'],
    Pelvis_back_red : ['side_B_17','side_B_18','side_B_19','side_B_20'],
    Leg_back_blue : ['side_F_15','side_F_16','side_F_17','side_F_18','side_F_20','side_F_23','side_F_25','side_F_26','side_F_29','side_F_30','side_F_31','side_F_32','side_B_15','side_B_16','side_B_17','side_B_18','side_B_19','side_B_20'],
    Leg_back_red : ['side_F_19','side_F_24','side_B_21','side_B_22','side_B_23','side_B_24']
}

function trainerList_DOM(){
    var tag = '<option value="">강사 선택</option>';
    for(var i in trainerList){
        var tName = trainerList[i].name;
        var tSeq = trainerList[i].sequence;
        tag += '<option value="' + tSeq + '">' + tName + '</option>';
    }
    $('#itemListSetPopup #changeTeacherSetFrm p > select').append(tag);
}

function editDefault(){
    $('#edit_name').val(MEMBERINFO.USER_NM);
    $('#edit_gender').val(MEMBERINFO.GENDER);
    $('#edit_num').val(MEMBERINFO.PHONE_NO);
    $('#edit_year').val(MEMBERINFO.BIRTH_DT);
    $('#edit_email').val(MEMBERINFO.EMAIL);
    $('#edit_center').val(MEMBERINFO.CENTER_SQ);
}
function getAllCenter(){
    $.ajax({
		url: "flow_controller.php?task=AllCenter",
		method: "GET",
		contentType: false,
        processData: false,

		success: function (e) {
            ALL_CENTER = JSON.parse(e);
            makeCenterList(ALL_CENTER);
            MEMBERINFO.CENTER_NM = (ALL_CENTER.filter(e => e.CENTER_SQ == MEMBERINFO.CENTER_SQ))[0].CENTER_NM;
		},
		error: function (e) {
			console.log(e);
		}
    });
}
function makeCenterList(list){
    $('#edit_center').empty();
    for(let i = 0; i < list.length; i++){
        $('#edit_center').append(
            '<option value="' + list[i].CENTER_SQ + '">' + list[i].CENTER_NM + '</option>'
        )
    }
}
function getMember() {
	$.ajax({
		url: "flow_controller.php?task=getUserData&u_seq=" + USER_SEQ,
		method: "GET",
		contentType: false,
        processData: false,

		success: function (e) {
			// console.log(e);

            var data = e.split('|');
            MEMBERINFO = JSON.parse(data[0]);

            // 날짜 데이터
            MEASUREMENT_BODY_DATE = JSON.parse(data[1]) // 신체 정보
            MEASUREMENT_POSE_DATE = JSON.parse(data[2]) // 체형 정보
            MEASUREMENT_ROM_DATE = JSON.parse(data[3])  // ROM 정보
            MEASUREMENT_DESA_DATE = JSON.parse(data[4]) // 대사 정보
            // 측정 데이터
            MEASUREMENT_BODY_DATA = JSON.parse(data[5]); // 신체 데이터
            MEASUREMENT_POSE_DATA = JSON.parse(data[6]); // 체형 데이터
            MEASUREMENT_ROM_DATA = JSON.parse(data[7]);  // ROM 데이터
            MEASUREMENT_DESA_DATA = JSON.parse(data[8]); // 대사 데이터
            // 기준 데이터 (범위)
            RANGE_POSE = JSON.parse(data[9]);
            RANGE_ROM = JSON.parse(data[10]);
            // 측정 이미지
            MEASUREMENT_BODY_IMG = JSON.parse(data[11]);
            // 보유 이용권
            USER_VOUCHER_LIST = JSON.parse(data[12]);
            defaultDateOUTPUT();
            editDefault();
            // getAllCenter();

            getTrainerList(MEMBERINFO);
            MEMBERINFO_DOM(MEMBERINFO);     // 회원 정보
            CHART_TYPE_BODY = MAKE_BMI(MEASUREMENT_BODY_DATA);
            myChart_Data_BODY(CHART_TYPE_BODY,'HEIGHT','all',$('#myChart1')[0]);   // 신체정보 변화도
            myChart_Data_POSE(MEASUREMENT_POSE_DATA,'Neck','all',$('#myChart2')[0], "REG_DT", 'front');   // 체형정보 변화도
            myChart_Data_ROM(MEASUREMENT_ROM_DATA,'Neck','all',$('#myChart3')[0], "REG_DT", 'front');   // 체형정보 변화도

            MAKE_USER_VOUCHER_LIST(USER_VOUCHER_LIST);
            
            // GET_USER_HISTORY(MEMBERINFO.USER_SQ);
            useAjax('GetUserHistoryList', (r) => {
                    var data = r.split('|');
                    var data = JSON.parse(data[0]);
                    MAKE_USER_HISTORY(data);
                },
                {START_DT: '2020-01-01', END_DT: '3000-01-01', MEMBER_SQ: MEMBERINFO.USER_SQ }
            );

		},
		error: function (e) {
			console.log(e);
		}
    });

}


function GET_USER_HISTORY(USER_SQ){
    let formData = new FormData();
        formData.append('START_DT', '2020-01-01');
        formData.append('END_DT', '3000-01-01');
        formData.append('MEMBER_SQ', USER_SQ);

    $.ajax({
        url: 'flow_controller.php?task=GetUserHistoryList',
        method: "POST",
        data: formData,
		contentType: false,
        processData: false,
		success: function(r){
            var data = r.split('|');
            var data = JSON.parse(data[0]);
            MAKE_USER_HISTORY(data);
        }
    });
}

function MAKE_USER_HISTORY(list){
    $('div.card3 table').empty();

    for(i of list){
        $('div.card3 table').prepend(
            `<tr>
                <td>
                    <div class="wrap">
                        <div class="up">
                            <p>${i.CATEGORY_NAME}</p>
                            <p>${i.REG_DT}</p>
                        </div>
                        <div class="down">
                            ${i.ACTION}
                        </div>
                    </div>
                </td>
            </tr>`
        );
    }
}

function getTrainerList(memInfo){
    $.ajax({
		url: "flow_controller.php?task=getManagerList",
		method: "POST",
		contentType: false,
        processData: false,
		success: function (e) {
            var data = e.split('|');
            TRAINER_LIST = JSON.parse(data[0]).filter(e => e.WORKSTATUS == 1);
            TRAINER_LIST = TRAINER_LIST.filter(e => e.ISUSE == 1);
            MAKE_TRAINER_LIST(TRAINER_LIST, memInfo);
		},
		error: function (e) {
			console.log(e);
		}
    });
}

function MAKE_TRAINER_LIST(list, data){
    var tag = '';
    var find = true;

    $('#afterTrainer').html('<option value="">선택</option>');
    $('#member_manager').html('<option value="">담당자 미지정</option>');

    for(let i in list){
        tag = '<option value="' + list[i].USER_SQ + '">' + list[i].USER_NM + '</option>';
        $('#afterTrainer').append(tag);

        $('#member_manager').append(`
            <option value="${list[i].USER_SQ}">${list[i].USER_NM}</option>
        `)
    }

    if(list.filter(e => e.USER_SQ == data.TRAINER).length == 0) find = false;

    $('.member_teacher > select').val(data.TRAINER == 0 || data.TRAINER == null || data.TRAINER == undefined || find == false ? '' : data.TRAINER);

}

var SELECTED_UV_SQ = '';

// 보유 이용권 그리기
function MAKE_USER_VOUCHER_LIST(list){
    var tag = '';
    $('.tabContainer > article > div.card2 > div.content').empty();
    if(list.length == 0){
        $('.tabContainer > article > div.card2 > div.content').html(
            '<p>보유중인 이용권이 없습니다.</p>'
        );
        return false;
    }
    for(let i of list){
        let dt = new Date();
            dt.setHours(0);dt.setMinutes(0);dt.setSeconds(0);dt.setMilliseconds(0);
        let startDate = new Date(i.USE_STARTDATE);
        let lastDate = new Date(i.USE_LASTDATE);
        let startCalc = ((dt - startDate) / 1000 / 60 / 60 / 24);
        let lastCalc = ((dt - lastDate) / 1000 / 60 / 60 / 24);
        let allCalc = ((lastDate - startDate) / 1000 / 60 / 60 / 24);
        
        if(i.USE_STATUS == 1){          // 이용전
            var result = ['이용전','stopVoucher',['flex','none','flex','none','flex','none']];
        }else if(i.USE_STATUS == 2){    // 이용중
            var result = ['이용중','activeVoucher',['flex','flex','flex','none','flex','none']];
        }else if(i.USE_STATUS == 3){    // 이용완료
            var result = ['만 료','lastVoucher',['none','flex','none','none','none','none']];
        }else{                          // 이용중지
            var result = ['이용중지','lastVoucher',['flex','flex','flex','none','none','flex']];
        }

        var tag = 
            `<article class="card ${result[1]}" data-uv_sq="${i.UV_SQ}" data-voucher_sq="${i.VOUCHER_SQ}">
                <h5><span></span><span></span>이용권<p>${result[0]}</p></h5>
                <div>
                    <p>${i.VOUCHER_TYPE_NAME}</p>
                    <p>${i.VOUCHER_NAME}</p>
                    <p>
                        ${i.USE_STARTDATE.split(' ')[0]} ~ ${i.USE_LASTDATE.split(' ')[0]} / 담당강사 : <span>${i.TRAINER_NM == null ? i.SELLER_NM : i.TRAINER_NM}</span><br>
                        이용일수 ${startCalc}/${allCalc}일 · 이용횟수 ${i.USEDCOUNT}/${i.COUNT}회 · 예약횟수 ${i.RESERV_COUNT}회
                    </p>
                </div>
                <i class="fas fa-wrench"> 옵션</i>
                <div class="optionBg">
                    <div class="dateTime" style="display:${result[2][0]}">
                        <i class="far fa-calendar-minus"></i><span>기간 횟수<br>조정</span>
                    </div>
                    <div class="useList" style="display:${result[2][1]}">
                        <i class="far fa-file-alt"></i><span>사용내역<br>보기</span>
                    </div>
                    <div class="changeTeacher" style="display:${result[2][2]}">
                        <i class="fas fa-chalkboard-teacher"></i><span>강사 변경</span>
                    </div>
                    <div class="for_ticketing" style="display:${result[2][3]}">
                        <i class="fas fa-user-clock"></i><span>일괄 예약</span>
                    </div>
                    <div class="stopTicket" style="display:${result[2][4]}">
                        <i class="fas fa-ban"></i><span>이용권<br>정지</span>
                    </div>
                    <div class="startTicket" style="display:${result[2][5]}">
                        <i class="far fa-play-circle"></i><span>이용권<br>재개</span>
                    </div>
                    <div class="setClose"><i class="fas fa-times"></i><span>닫 기</span></div>
                </div>
            </article>`;
        
        $('.tabContainer > article > div.card2 > div.content').prepend(tag);
    }

    
    // 구매이용권 설정 열기
    $('div.card2 > div.content > article > i').click(function(){
        $(this).parent().siblings().find('div.optionBg').css({left : '100%'})
        $(this).siblings('div.optionBg').css({left : 0});
        SELECTED_UV_SQ = $(this).parents('.card').attr('data-uv_sq');
    });
    // 구매이용권 설정 닫기
    $('div.card2 > div.content > article > div.optionBg .setClose').click(function(){
        $(this).parent('div.optionBg').css('left','100%');
    });
    
    $('div.card2 > div.content > article > div.optionBg > div')
    .not($('div.card2 > div.content > article > div.optionBg .setClose'))
    .click(function(){
        var thisName = $(this);
        var today = dateFormat(new Date());
        var selectData = USER_VOUCHER_LIST.filter(x => x.UV_SQ == SELECTED_UV_SQ)[0];
        
        let dt = new Date();
            dt.setHours(0);dt.setMinutes(0);dt.setSeconds(0);dt.setMilliseconds(0);
        let startDate = new Date(selectData.USE_STARTDATE);
        let lastDate = new Date(selectData.USE_LASTDATE);
        let startCalc = (dt - startDate) / 1000 / 60 / 60 / 24;
        let lastCalc = (dt - lastDate) / 1000 / 60 / 60 / 24;
        let allCalc = (lastDate - startDate) / 1000 / 60 / 60 / 24;

        if($USER_GRADE < 3){
            if(thisName.attr('class') == 'dateTime'){
                if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 15) == -1){
                    alertApp('X', '권한이 없습니다.');
                    return false;
                }
            }
            if(thisName.attr('class') == 'changeTeacher'){
                if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 16) == -1){
                    alertApp('X', '권한이 없습니다.');
                    return false;
                }
            }
        }

        var thisId = thisName.attr('class');

        $('#itemListSetPopup').find('h2 > span').text(thisName.text());
        $('#itemListSetPopup').find('form').hide();
        $('#itemListSetPopup').find('form[name="' + thisId + 'SetFrm"]').show();

        // 내용 입력
        $('#itemListSetPopup').find('.date').text(
            selectData.USE_STARTDATE.split(' ')[0] +
            ' ~ '  +
            selectData.USE_LASTDATE.split(' ')[0]
        );
        $('#itemListSetPopup').find('.allCount').text(selectData.COUNT + '회');
        $('#itemListSetPopup').find('.count').text(
            Number(selectData.COUNT) - Number(selectData.USEDCOUNT) + '회'
        );
        $('#itemAfterDate1').val(selectData.USE_STARTDATE.split(' ')[0]);
        $('#itemAfterDate2').val(selectData.USE_LASTDATE.split(' ')[0]);
        $('#itemAfterAllCount').val(selectData.COUNT);
        $('#itemAfterCount').val(Number(selectData.COUNT) - Number(selectData.USEDCOUNT));
        
        $('#beforeTrainer').val(selectData.TRAINER_NM);
        setTimeout(() => $('#afterTrainer').val(selectData.TRAINER_SQ), 200);

        $('#stopTicketSetFrm').find('.stopType').text(selectData.VOUCHER_TYPE_NAME);
        $('#stopTicketSetFrm').find('.stopName').text(selectData.VOUCHER_NAME);
        $('#stopTicketSetFrm').find('.stopDate').text(
            selectData.USE_STARTDATE.split(' ')[0] + ' ~ ' + selectData.USE_LASTDATE.split(' ')[0] +
            ' / 담당강사 : ' + selectData.TRAINER_NM
        );
        $('#stopTicketSetFrm').find('.stopUse').text(
            '이용일수 ' + startCalc + '/' + allCalc + '일 · ' +
            '이용횟수 ' + selectData.USEDCOUNT + '/' + selectData.COUNT + '회 · ' +
            '예약횟수 ' + selectData.RESERV_COUNT + '회'
        );
        $('#stopTicketSetFrm').find('#whereStopDateEZ').val('');
        $('#stopTicketSetFrm').find('#whereStopDate1').val(today);
        $('#stopTicketSetFrm').find('.stopResult .start').text(today);
        $('#stopTicketSetFrm').find('#whereStopDate2').val('');
        $('#stopTicketSetFrm').find('.stopResult .end').text('-');
        $('#stopTicketSetFrm').find('.stopResult .range').text('-');
        $('#stopTicketSetFrm').find('.stopResult .reStart').text('-');

        //////////////////////////////////////////////////////////////////

        $('#startTicketSetFrm').find('.voucherInfo').attr('data-uv-sq', selectData.UV_SQ);
        $('#startTicketSetFrm').find('.startType').text(selectData.VOUCHER_TYPE_NAME);
        $('#startTicketSetFrm').find('.startName').text(selectData.VOUCHER_NAME);
        $('#startTicketSetFrm').find('.startDate').text(
            selectData.USE_STARTDATE.split(' ')[0] + ' ~ ' + selectData.USE_LASTDATE.split(' ')[0] +
            ' / 담당강사 : ' + selectData.TRAINER_NM
        );
        $('#startTicketSetFrm').find('.startUse').text(
            '이용일수 ' + startCalc + '/' + allCalc + '일 · ' +
            '이용횟수 ' + selectData.USEDCOUNT + '/' + selectData.COUNT + '회 · ' +
            '예약횟수 ' + selectData.RESERV_COUNT + '회'
        );

        if(thisId == 'startTicket'){
            let form = new FormData();
            form.append('UV_SQ', SELECTED_UV_SQ);
            $.ajax({
                url: 'flow_controller.php?task=getPauseInfo',
                data: form,
                method: 'POST',
                contentType: false,
                processData: false,
                success: function(r){
                    let data = JSON.parse(r)[0];
                    $('#startTicketSetFrm').find('.startResult .start').text(data.START_DATE.split(' ')[0]);
                    $('#startTicketSetFrm').find('.startResult .end').text(data.END_DATE.split(' ')[0]);
                    $('#startTicketSetFrm').find('.startResult .range').text(data.DAYS + '일');
                },
                error: function(){
                    alertApp('X', '오류');
                    return false;
                }
            });
        }

        //////////////////////////////////////////////////////////////////

        $('.gray_div').add($('#itemListSetPopup')).fadeIn(200);

        $('#afterTrainer').val('');
        $('#for_itemDate1').val('');
        $('#for_itemDate2').val('');
        $('#ticketingCount').val('1')
        $('#classStartTime1').val('00');
        $('#classStartTime2').val('00');
    });

}

// BMI 만들기
function MAKE_BMI(data){
    for(let i in data){
        var calc;
        if (data[i].WEIGHT != '0' && data[i].HEIGHT != '0') {
            calc = Number(data[i].WEIGHT) / Math.pow( Number( data[i].HEIGHT / 100 ) , 2 );
        }else{
            calc = 0
        }
        data[i].BMI = calc.toFixed(1);
    }
    return data;
}


// 신체정보 변화도 (차트데이터)
function myChart_Data_BODY(data,title,date,where, fieldname){
    var title2 = [title];

    if(date != 'all'){
        var data = getfilteredDATA(data, date, fieldname);
    }
    var data_Obj = makeDATAobject(title2, data, 'REG_DT')

    // 차트 그리기
    if (bodyGraph) {
        bodyGraph.destroy();
    }
    bodyGraph = MAKE_CHART(data_Obj, where);
}
// 체형정보 변화도 (차트데이터)
function myChart_Data_POSE(data,title,date,where, fieldname, dir){
    var title2;
    if(date != 'all'){
        var data = getfilteredDATA(data, date, fieldname);
    }

    switch(title){
        case 'Neck':       // 정면
            if (dir=='front'){
                title2 = ['front_Neck'];
            } else {
                title2 = ['side_Neck'];
            }
            break;

        case 'Shoulder':
            if (dir=='front'){
                title2 = ['front_RShoulder', 'front_LShoulder'];
            } else {
                title2 = ['side_Shoulder'];
            }
            break;

        case 'Pelvis':
            if (dir=='front'){
                title2 = ['front_RPelvis', 'front_LPelvis'];
            } else {
                title2 = ['side_Pelvis'];
            }
            break;

        case 'Leg':
            if (dir=='front'){
                title2 = ['front_RLeg', 'front_LLeg'];
            } else {
                title2 = ['side_Leg'];
            }
            break;


    }


   var data_Obj = makeDATAobject(title2, data, 'REG_DT')

   // 차트 그리기
   if (poseGraph1) {
       poseGraph1.destroy();
   }
   poseGraph1 = MAKE_CHART(data_Obj, where);

}
// 체형정보 변화도 (차트데이터)
function myChart_Data_ROM(data,title,date,where, fieldname, dir){
    var title2;
    if(date != 'all'){
        var data = getfilteredDATA(data, date, fieldname);
    }

    switch(title){
        case 'Neck':       // 정면
            if (dir=='front'){
                title2 = ['front_Neck_right','front_Neck_left'];
            } else {
                title2 = ['side_Neck_front','side_Neck_back'];
            }
            break;

        case 'Shoulder':
            if (dir=='front'){
                title2 = ['front_Shoulder_right', 'front_Shoulder_left'];
            } else {
                title2 = ['side_ShoulderR_front','side_ShoulderR_back','side_ShoulderL_front','side_ShoulderL_back'];
            }
            break;

        case 'Pelvis':
            if (dir=='front'){
                title2 = ['front_Waist_right', 'front_Waist_left'];
            } else {
                title2 = ['side_Waist_front','side_Waist_back'];
            }
            break;

        case 'Leg':
            if (dir=='front'){
                title2 = ['front_Hip_right', 'front_Hip_left'];
            } else {
                title2 = ['side_HipR_front','side_HipR_back','side_HipL_front','side_HipL_back'];
            }
            break;


    }


   var data_Obj = makeDATAobject(title2, data, 'REG_DT')

    // 차트 그리기
    if (poseGraph2) {
        poseGraph2.destroy();
    }
    poseGraph2 = MAKE_CHART(data_Obj, where);

}

var SeriesColorTable = ['rgba(54, 162, 235, 1)', 'rgba(200, 162, 235, 1)','rgba(100, 202, 135, 1)','rgba(200, 162, 135, 1)'];

// 전체 데이터
function makeDATAobject(title, data, date_field){
    var chart_data = [];
    var labels = [];
    for(let i = 0; i < data.length; i++){
        labels.push(data[i][date_field]);
    }
    for (let ind in title){
        chart_data.push(make_SeriesData(title[ind], data, SeriesColorTable[ind]));
    }
    var data_Obj = {
        labels: labels,
        datasets: chart_data
    }
    return data_Obj;
}

function make_SeriesData(title, data, SeriesColor){

    var chart_data = [];
    for(let i = 0; i < data.length; i++){
        chart_data.push(data[i][title]);
    }
    var SeriesData = {
        label: GraphLineName[title],
        data: chart_data,
        backgroundColor: SeriesColor,
        borderColor: SeriesColor,
        borderWidth: 2,
        fill: false,
        lineTension: 0
    }

    return SeriesData;
}

// 3개월/6개월/12개월
function getfilteredDATA(originalData, m, fieldname){
    var $DATE = new Date();
    $DATE.setMonth($DATE.getMonth() - parseInt(m));

    var filteredData = originalData.filter(e => new Date(e[fieldname]) >= $DATE);

    return filteredData;
}
// var dataGraph;
// 신체정보 변화도 그래프 만들기
function MAKE_CHART(data,to){
    dataGraph = new Chart(to, {
        type: 'line',
        data: data,
        options: {
            scales: {
                xAxes: [{
                    type: 'time',
                    time: {
                        parser: timeFormat,
                        unit: 'month'
                    }
                }],
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
    return dataGraph;
}

// 회원정보
function MEMBERINFO_DOM(data){
    $('.u_name').find('.name').text(data.USER_NM);
    data.GENDER == 'M' ? $('.u_gender').find('.gender').text('남자') : $('.u_gender').find('.gender').text('여자');
    $('.u_num').find('.num').text(data.PHONE_NO ? data.PHONE_NO : '-');
    $('.u_year').find('span.y').text(data.BIRTH_DT ? data.BIRTH_DT : '-');
    $('.u_year').find('span.age').text(data.BIRTH_DT ? birth_year(data.BIRTH_DT) : '');
    $('.u_email').find('.email').text(data.EMAIL ? data.EMAIL : '-');
    $('.u_center').find('.center').text(
        data.CENTER_NM ? data.CENTER_NM : '-'
    );
    if(data.USERIMAGE == null || data.USERIMAGE == ''){
        $('.user_face[alt="회원사진"]').attr('src', 'img/user.png');
    }else{
        $('.user_face[alt="회원사진"]').attr('src', data.USERIMAGE);
    }

    $('.member_seq').text(data.USER_SQ);
    $('.member_joinDate').text(data.REG_DT.split(' ')[0]);
    
    $('.member_appUse').text('이용중 아님');
    $('.member_rockerNum').text('이용중 아님');
    $('.member_barcode').text(
        pwEncode(data.USER_SQ + '|' + data.USER_NM)
    ).css('letter-spacing', '1px');
    $('.member_memo').val(data.COMMENT ? data.COMMENT : '');

}

// 측정 날짜 정보 (DOM 생성)
function MEASUREMENT_DATE(obj, data){
    if(data.length == 0){
        obj.empty().html('<option value="">측정정보 없음</option>');
    }else{
        for(var i in data){
            obj.append('<option value="' + data[i].MEASUREMENT_SQ + '">' + data[i].REG_DT.split(' ')[0] + '</option>');
        }
    }
    // 밖에 있는 테이블 체인치
    $('.DB_DATE_OUTPUT').change(function(){
        SELECT_CHANGE($(this));
    });
}

// 디폴트 함수
function defaultDateOUTPUT(){
    // 데이터 초기화 (날짜, 테이블)
    MEASUREMENT_DATA_EMPTY();
    // 날짜 OPTION 생성
    MEASUREMENT_DATE($('#check_date_body'), MEASUREMENT_BODY_DATE);    // 신체 정보
    MEASUREMENT_DATE($('#inbody_form_date'), MEASUREMENT_BODY_DATE);    // 신체 정보

    MEASUREMENT_DATE($('#check_date_pose'), MEASUREMENT_POSE_DATE);    // 체형 정보
    MEASUREMENT_DATE($('#pose_date'), MEASUREMENT_POSE_DATE);    // 체형 정보
    MEASUREMENT_DATE($('#before-date'), MEASUREMENT_POSE_DATE);    // 체형 정보
    MEASUREMENT_DATE($('#after-date'), MEASUREMENT_POSE_DATE);    // 체형 정보

    MEASUREMENT_DATE($('#check_date_rom'), MEASUREMENT_ROM_DATE);    // ROM 정보
    MEASUREMENT_DATE($('#rom_date'), MEASUREMENT_ROM_DATE);    // ROM 정보
    MEASUREMENT_DATE($('#before-date-rom'), MEASUREMENT_ROM_DATE);    // ROM 정보
    MEASUREMENT_DATE($('#after-date-rom'), MEASUREMENT_ROM_DATE);    // ROM 정보

    MEASUREMENT_DATE($('#check_date_desa'), MEASUREMENT_DESA_DATE);    // 대사 정보

    if(MEASUREMENT_POSE_DATE.length != 0){
        if(MEASUREMENT_POSE_DATE[1]){
            $('#before-date').val(MEASUREMENT_POSE_DATE[1].MEASUREMENT_SQ);
        }
        if(MEASUREMENT_ROM_DATE[1]){
            $('#before-date-rom').val(MEASUREMENT_ROM_DATE[1].MEASUREMENT_SQ);
        }
    }


    $('.DB_DATE_OUTPUT').each(function(){
        SELECT_CHANGE($(this));
    });
}

function resetMuscle(data){
    for(let i in data){
        $('#' + data[i]).css('fill','#fff');
    }
}
function resetMuscleTotal(){
    resetMuscle(muscleSVG.Neck_right_blue);
    resetMuscle(muscleSVG.Neck_right_red);
    resetMuscle(muscleSVG.Neck_left_blue);
    resetMuscle(muscleSVG.Neck_left_red);
    resetMuscle(muscleSVG.Shoulder_right_up_blue);
    resetMuscle(muscleSVG.Shoulder_right_up_red);
    resetMuscle(muscleSVG.Shoulder_right_down_blue);
    resetMuscle(muscleSVG.Shoulder_right_down_red);
    resetMuscle(muscleSVG.Shoulder_left_up_blue);
    resetMuscle(muscleSVG.Shoulder_left_up_red);
    resetMuscle(muscleSVG.Shoulder_left_down_blue);
    resetMuscle(muscleSVG.Shoulder_left_down_red);
    resetMuscle(muscleSVG.Pelvis_right_up_blue);
    resetMuscle(muscleSVG.Pelvis_right_up_red);
    resetMuscle(muscleSVG.Pelvis_right_down_blue);
    resetMuscle(muscleSVG.Pelvis_right_down_red);
    resetMuscle(muscleSVG.Pelvis_left_up_blue);
    resetMuscle(muscleSVG.Pelvis_left_up_red);
    resetMuscle(muscleSVG.Pelvis_left_down_blue);
    resetMuscle(muscleSVG.Pelvis_left_down_red);
    resetMuscle(muscleSVG.Leg_right_o_blue);
    resetMuscle(muscleSVG.Leg_right_o_red);
    resetMuscle(muscleSVG.Leg_left_o_blue);
    resetMuscle(muscleSVG.Leg_left_o_red);
    resetMuscle(muscleSVG.Leg_right_x_blue);
    resetMuscle(muscleSVG.Leg_right_x_red);
    resetMuscle(muscleSVG.Leg_left_x_blue);
    resetMuscle(muscleSVG.Leg_left_x_red);
    resetMuscle(muscleSVG.Neck_front_blue);
    resetMuscle(muscleSVG.Neck_front_red);
    resetMuscle(muscleSVG.Shoulder_front_blue);
    resetMuscle(muscleSVG.Shoulder_front_red);
    resetMuscle(muscleSVG.Pelvis_front_blue);
    resetMuscle(muscleSVG.Pelvis_front_red);
    resetMuscle(muscleSVG.Pelvis_back_blue);
    resetMuscle(muscleSVG.Pelvis_back_red);
    resetMuscle(muscleSVG.Leg_back_blue);
    resetMuscle(muscleSVG.Leg_back_red);

    $('svg path').attr({'stroke':'none','stroke-width':'0'});
}



// 날짜 변경에 맞춰 함수 실행
function SELECT_CHANGE(that){
    var val = that.val();
    var type = that.attr('data-measurement_type');

    switch(type){
        // 밖 Select
        case 'BODY' :
            PRINT_BODY_DATA(MEASUREMENT_BODY_DATA, val);
            break;
        case 'POSE' :
            PRINT_POSE_DATA(MEASUREMENT_POSE_DATA, val);
            break;
        case 'ROM' :
            PRINT_ROM_DATA(MEASUREMENT_ROM_DATA, val);
            break;
        case 'DESA' :
            PRINT_DESA_DATA(MEASUREMENT_DESA_DATA, val);
            break;

        // 자세히보기 Select
        case 'BODY_DETAIL' :
            PRINT_BODY_DETAIL_DATA(MEASUREMENT_BODY_DATA, val);
            break;
        case 'POSE_DETAIL' :
            PRINT_POSE_DETAIL_DATA(MEASUREMENT_POSE_DATA, val);
            break;
        case 'ROM_DETAIL' :
            PRINT_ROM_DETAIL_DATA(MEASUREMENT_ROM_DATA, val);
            break;
        case 'DESA_DETAIL' :
            PRINT_DESA_DETAIL_DATA(MEASUREMENT_DESA_DATA, val);
            break;

        // 비교결과 Select
        case 'POSE_COMPARE_BEFORE' :
            var dateTemp = MEASUREMENT_POSE_DATA.filter(e => e.MEASUREMENT_SQ == val)[0];
            if(dateTemp){
                $('.tableDate.before').html(dateTemp.REG_DT.split(' ')[0]);
            }

            if ($('.pose_change > div.option > button.active').index() == 0){
                PRINT_POSE_FRONT_BEFORE(MEASUREMENT_POSE_DATA, val);
            }else{
                PRINT_POSE_SIDE_BEFORE(MEASUREMENT_POSE_DATA, val);
            }
            break;
        case 'POSE_COMPARE_AFTER' :
            var dateTemp = MEASUREMENT_POSE_DATA.filter(e => e.MEASUREMENT_SQ == val)[0];
            if(dateTemp){
                $('.tableDate.after').html(dateTemp.REG_DT.split(' ')[0]);
            }

            if ($('.pose_change > div.option > button.active').index() == 0){
                PRINT_POSE_FRONT_AFTER(MEASUREMENT_POSE_DATA, val);
            }else{
                PRINT_POSE_SIDE_AFTER(MEASUREMENT_POSE_DATA, val);
            }
            break;

        // ROM 비교결과
        case 'ROM_COMPARE_BEFORE' :
            var dateTemp = MEASUREMENT_ROM_DATA.filter(e => e.MEASUREMENT_SQ == val)[0];
            if(dateTemp){
                $('.ROM_BEFORE_DATE_LAST').html(dateTemp.REG_DT.split(' ')[0]);
            }

            if($('.rom_change > div.option > button.active').index() == 0){
                PRINT_ROM_FRONT_BEFORE(MEASUREMENT_ROM_DATA, val);
            }else{
                PRINT_ROM_SIDE_BEFORE(MEASUREMENT_ROM_DATA, val);
            }
            break;

        case 'ROM_COMPARE_AFTER' :
            var dateTemp = MEASUREMENT_ROM_DATA.filter(e => e.MEASUREMENT_SQ == val)[0];
            if(dateTemp){
                $('.ROM_AFTER_DATE_NOW').html(dateTemp.REG_DT.split(' ')[0]);
            }

            if($('.rom_change > div.option > button.active').index() == 0){
                PRINT_ROM_FRONT_AFTER(MEASUREMENT_ROM_DATA, val);
            }else{
                PRINT_ROM_SIDE_AFTER(MEASUREMENT_ROM_DATA, val);
            }
            break;
    }
}

// 날짜, 테이블 데이터 초기화 함수
function MEASUREMENT_DATA_EMPTY(){
    $('.DB_DATE_OUTPUT').empty();
    $('td.data').html('-');
    $('td.data-chart').html('<div></div>');
    $('.tableDate').text('-');
    $('div.content > section.table > table > tbody > tr > td').html('-');
    $('.myBody_WEIGHT').empty();
    $('.myBody_BMI').empty();
    $('.myBody_MUSCLE').empty();
    $('.myBody_FAT').empty();
    $('.myBody_DESA').empty();
    $('[title="체형검사정면사진"]').css({
        'background-image': 'url(../img/NOT_MEASUREMENT_IMAGE.png)'
    });
    $('[title="체형검사측면사진"]').css({
        'background-image': 'url(../img/NOT_MEASUREMENT_IMAGE.png)'
    });
    $('[title="체형검사이전측정일"]').css({
        'background-image': 'url(../img/NOT_MEASUREMENT_IMAGE.png)'
    });
    $('[title="체형검사최근측정일"]').css({
        'background-image': 'url(../img/NOT_MEASUREMENT_IMAGE.png)'
    });
    // 직접입력 모달 => 최근데이터
    $('#bodyInfo_Write span.data').html('-');
    $('.lastDesa_HR').html('-');
    $('.lastDesa_SBP_DBP').html('-');
    $('.lastDesa_Glucose').html('-');
    $('.lastDesa_HbA1c').html('-');
    $('.lastDesa_TC').html('-');
    $('.lastDesa_HDL_LDL').html('-');
    $('.lastDesa_TG').html('-');
    $('.lastDesa_Lactate').html('-');
}

// 정보데이터 표출 함수
function PRINT_BODY_DATA(data, val){    // 신체정보
    if(data.length == 0){
        data = false;
    }else{

        var THIS_DATA = data.filter(e => e.MEASUREMENT_SQ == val)[0];

        $('#u_HEIGHT').text(THIS_DATA.HEIGHT + ' Cm');
        $('#u_WEIGHT').text(THIS_DATA.WEIGHT + ' Kg');
        $('#u_FAT').text(THIS_DATA.FAT + ' Kg');
        $('#u_MUSCLE').text(THIS_DATA.MUSCLE + ' Kg');

    }
}
function PRINT_POSE_DATA(data, val){    // 체형정보
    if(data.length == 0){
        data = false;
    }else{
        var THIS_DATA = data.filter(e => e.MEASUREMENT_SQ == val)[0];

        $('#u_FRONT_HEAD_POSE').html(arrowPo_row(THIS_DATA.front_Neck) + PRINT_DANGER_RANGE_POSE(Number(THIS_DATA.front_Neck).toFixed(1),RANGE_POSE[0].front_Neck,RANGE_POSE[1].front_Neck));
        $('#u_FRONT_SHOULDER_RIGHT_POSE').html(arrowPo_col(THIS_DATA.front_RShoulder,"left") + PRINT_DANGER_RANGE_POSE(Number(THIS_DATA.front_RShoulder).toFixed(1),RANGE_POSE[0].front_RShoulder,RANGE_POSE[1].front_RShoulder));
        $('#u_FRONT_SHOULDER_LEFT_POSE').html(arrowPo_col(THIS_DATA.front_LShoulder,"right") + PRINT_DANGER_RANGE_POSE(Number(THIS_DATA.front_LShoulder).toFixed(1),RANGE_POSE[0].front_LShoulder,RANGE_POSE[1].front_LShoulder));
        $('#u_FRONT_PELVIS_RIGHT_POSE').html(arrowPo_col(THIS_DATA.front_RPelvis,"left") + PRINT_DANGER_RANGE_POSE(Number(THIS_DATA.front_RPelvis).toFixed(1),RANGE_POSE[0].front_RPelvis,RANGE_POSE[1].front_RPelvis));
        $('#u_FRONT_PELVIS_LEFT_POSE').html(arrowPo_col(THIS_DATA.front_LPelvis,"right") + PRINT_DANGER_RANGE_POSE(Number(THIS_DATA.front_LPelvis).toFixed(1),RANGE_POSE[0].front_LPelvis,RANGE_POSE[1].front_LPelvis));
        $('#u_FRONT_LEG_RIGHT_POSE').html(arrowPo_row_R_LEG(THIS_DATA.front_RLeg) + PRINT_DANGER_RANGE_POSE(Number(Math.abs(THIS_DATA.front_RLeg)-180).toFixed(1),RANGE_POSE[0].front_RLeg,RANGE_POSE[1].front_RLeg));
        $('#u_FRONT_LEG_LEFT_POSE').html(arrowPo_row_L_LEG(THIS_DATA.front_LLeg) + PRINT_DANGER_RANGE_POSE(Number(Math.abs(THIS_DATA.front_LLeg)-180).toFixed(1),RANGE_POSE[0].front_LLeg,RANGE_POSE[1].front_LLeg));
        $('#u_SIDE_HEAD_POSE').html(arrowPo_row(THIS_DATA.side_Neck) + PRINT_DANGER_RANGE_POSE(Number(THIS_DATA.side_Neck).toFixed(1),RANGE_POSE[0].side_Neck,RANGE_POSE[1].side_Neck));
        $('#u_SIDE_SHOULDER_POSE').html(arrowPo_row(THIS_DATA.side_Shoulder) + PRINT_DANGER_RANGE_POSE(Number(THIS_DATA.side_Shoulder).toFixed(1),RANGE_POSE[0].side_Shoulder,RANGE_POSE[1].side_Shoulder));
        $('#u_SIDE_PELVIS_POSE').html(arrowPo_row(THIS_DATA.side_Pelvis) + PRINT_DANGER_RANGE_POSE(Number(THIS_DATA.side_Pelvis).toFixed(1),RANGE_POSE[0].side_Pelvis,RANGE_POSE[1].side_Pelvis));
        $('#u_SIDE_LEG_POSE').html(arrowPo_row(THIS_DATA.side_Leg) + PRINT_DANGER_RANGE_POSE(Number(THIS_DATA.side_Leg).toFixed(1),RANGE_POSE[0].side_Leg,RANGE_POSE[1].side_Leg));
    }
}

function PRINT_ROM_DATA(data, val){     // ROM정보
    if(data.length == 0){
        data = false;
    }else{
        var THIS_DATA = data.filter(e => e.MEASUREMENT_SQ == val)[0];

        $('#u_FRONT_HEAD_RIGHT_ROM').html( Number(THIS_DATA.front_Neck_right).toFixed(1) + '˚' + PRINT_DANGER_RANGE_ROM(THIS_DATA.front_Neck_right,RANGE_ROM[1].front_Neck_right,RANGE_ROM[0].front_Neck_right));
        $('#u_FRONT_HEAD_LEFT_ROM').html( Number(THIS_DATA.front_Neck_left).toFixed(1) + '˚' + PRINT_DANGER_RANGE_ROM(THIS_DATA.front_Neck_left,RANGE_ROM[1].front_Neck_left,RANGE_ROM[0].front_Neck_left));
        $('#u_FRONT_SHOULDER_RIGHT_ROM').html( Number(THIS_DATA.front_Shoulder_right).toFixed(1) + '˚' + PRINT_DANGER_RANGE_ROM(THIS_DATA.front_Shoulder_right,RANGE_ROM[1].front_Shoulder_right,RANGE_ROM[0].front_Shoulder_right));
        $('#u_FRONT_SHOULDER_LEFT_ROM').html( Number(THIS_DATA.front_Shoulder_left).toFixed(1) + '˚' + PRINT_DANGER_RANGE_ROM(THIS_DATA.front_Shoulder_left,RANGE_ROM[1].front_Shoulder_left,RANGE_ROM[0].front_Shoulder_left));
        $('#u_FRONT_PELVIS_RIGHT_ROM').html( Number(THIS_DATA.front_Waist_right).toFixed(1) + '˚' + PRINT_DANGER_RANGE_ROM(THIS_DATA.front_Waist_right,RANGE_ROM[1].front_Waist_right,RANGE_ROM[0].front_Waist_right));
        $('#u_FRONT_PELVIS_LEFT_ROM').html( Number(THIS_DATA.front_Waist_left).toFixed(1) + '˚' + PRINT_DANGER_RANGE_ROM(THIS_DATA.front_Waist_left,RANGE_ROM[1].front_Waist_left,RANGE_ROM[0].front_Waist_left));
        $('#u_FRONT_LEG_RIGHT_ROM').html( Number(THIS_DATA.front_Hip_right).toFixed(1) + '˚' + PRINT_DANGER_RANGE_ROM(THIS_DATA.front_Hip_right,RANGE_ROM[1].front_Hip_right,RANGE_ROM[0].front_Hip_right));
        $('#u_FRONT_LEG_LEFT_ROM').html( Number(THIS_DATA.front_Hip_left).toFixed(1) + '˚' + PRINT_DANGER_RANGE_ROM(THIS_DATA.front_Hip_left,RANGE_ROM[1].front_Hip_left,RANGE_ROM[0].front_Hip_left));
        $('#u_SIDE_HEAD_FRONT_ROM').html( Number(THIS_DATA.side_Neck_front).toFixed(1) + '˚' + PRINT_DANGER_RANGE_ROM(THIS_DATA.side_Neck_front,RANGE_ROM[1].side_Neck_front,RANGE_ROM[0].side_Neck_front));
        $('#u_SIDE_HEAD_BACK_ROM').html( Number(THIS_DATA.side_Neck_back).toFixed(1) + '˚' + PRINT_DANGER_RANGE_ROM(THIS_DATA.side_Neck_back,RANGE_ROM[1].side_Neck_back,RANGE_ROM[0].side_Neck_back));
        $('#u_SIDE_SHOULDER_FRONT_ROM').html(
            '<div class="tableRows up">' + Number(THIS_DATA.side_ShoulderR_front).toFixed(1) + '˚' + PRINT_DANGER_RANGE_ROM(THIS_DATA.side_ShoulderR_front,RANGE_ROM[1].side_ShoulderR_front,RANGE_ROM[0].side_ShoulderR_front) + '</div>' +
            '<div class="tableRows down">' + Number(THIS_DATA.side_ShoulderL_front).toFixed(1) + '˚' + PRINT_DANGER_RANGE_ROM(THIS_DATA.side_ShoulderL_front,RANGE_ROM[1].side_ShoulderL_front,RANGE_ROM[0].side_ShoulderL_front) + '</div>'
        );
        $('#u_SIDE_SHOULDER_BACK_ROM').html(
            '<div class="tableRows up">' + Number(THIS_DATA.side_ShoulderR_back).toFixed(1) + '˚' + PRINT_DANGER_RANGE_ROM(THIS_DATA.side_ShoulderR_back,RANGE_ROM[1].side_ShoulderR_back,RANGE_ROM[0].side_ShoulderR_back) + '</div>' +
            '<div class="tableRows down">' + Number(THIS_DATA.side_ShoulderL_back).toFixed(1) + '˚' + PRINT_DANGER_RANGE_ROM(THIS_DATA.side_ShoulderL_back,RANGE_ROM[1].side_ShoulderL_back,RANGE_ROM[0].side_ShoulderL_back) + '</div>'
        );
        $('#u_SIDE_PELVIS_FRONT_ROM').html( Number(THIS_DATA.side_Waist_front).toFixed(1) + '˚' + PRINT_DANGER_RANGE_ROM(THIS_DATA.side_Waist_front,RANGE_ROM[1].side_Waist_front,RANGE_ROM[0].side_Waist_front));
        $('#u_SIDE_PELVIS_BACK_ROM').html( Number(THIS_DATA.side_Waist_back).toFixed(1) + '˚' + PRINT_DANGER_RANGE_ROM(THIS_DATA.side_Waist_back,RANGE_ROM[1].side_Waist_back,RANGE_ROM[0].side_Waist_back));
        $('#u_SIDE_LEG_FRONT_ROM').html(
            '<div class="tableRows up">' + Number(THIS_DATA.side_HipR_front).toFixed(1) + '˚' + PRINT_DANGER_RANGE_ROM(THIS_DATA.side_HipR_front,RANGE_ROM[1].side_HipR_front,RANGE_ROM[0].side_HipR_front) + '</div>' +
            '<div class="tableRows down">' + Number(THIS_DATA.side_HipL_front).toFixed(1) + '˚' + PRINT_DANGER_RANGE_ROM(THIS_DATA.side_HipL_front,RANGE_ROM[1].side_HipL_front,RANGE_ROM[0].side_HipL_front) + '</div>'
        );
        $('#u_SIDE_LEG_BACK_ROM').html(
            '<div class="tableRows up">' + Number(THIS_DATA.side_HipR_back).toFixed(1) + '˚' + PRINT_DANGER_RANGE_ROM(THIS_DATA.side_HipR_back,RANGE_ROM[1].side_HipR_back,RANGE_ROM[0].side_HipR_back) + '</div>' +
            '<div class="tableRows down">' + Number(THIS_DATA.side_HipL_back).toFixed(1) + '˚' + PRINT_DANGER_RANGE_ROM(THIS_DATA.side_HipL_back,RANGE_ROM[1].side_HipL_back,RANGE_ROM[0].side_HipL_back) + '</div>'
        );
    }
}
function PRINT_DESA_DATA(data, val){    // 대사질환정보
    if(data.length == 0){
        data = false;
    }else{
        var THIS_DATA = data.filter(e => e.MEASUREMENT_SQ == val)[0];
        // 결과data
        $('#u_DESA_DATA0').text( THIS_DATA.HR )
            .next().html('<span class="desaCircle ' + DESA_DATA_ONE(60,70,THIS_DATA.HR) + '"></span>');
        $('#u_DESA_DATA1').text( THIS_DATA.SBP + ' / ' + THIS_DATA.DBP )
            .next().html('<span class="desaCircle ' + DESA_DATA_TWO(90,120,THIS_DATA.SBP, 60,80,THIS_DATA.DBP) + '"></span>');
        $('#u_DESA_DATA2').text( THIS_DATA.GLUCOSE )
            .next().html('<span class="desaCircle ' + DESA_DATA_ONE(70,100,THIS_DATA.GLUCOSE) + '"></span>');
        $('#u_DESA_DATA3').text( THIS_DATA.HbA1c )
            .next().html('<span class="desaCircle ' + DESA_DATA_ONE(0,5.6,THIS_DATA.HbA1c) + '"></span>');
        $('#u_DESA_DATA4').text( THIS_DATA.TC )
            .next().html('<span class="desaCircle ' + DESA_DATA_ONE(0,200,THIS_DATA.TC) + '"></span>');
        $('#u_DESA_DATA5').text( THIS_DATA.HDL + ' / ' + THIS_DATA.LDL )
            .next().html('<span class="desaCircle ' + DESA_DATA_TWO(40,60,THIS_DATA.HDL, 0,100,THIS_DATA.LDL) + '"></span>');
        $('#u_DESA_DATA6').text( THIS_DATA.TG )
            .next().html('<span class="desaCircle ' + DESA_DATA_ONE(0,150,THIS_DATA.TG) + '"></span>');
        $('#u_DESA_DATA7').text( THIS_DATA.Lactate )
            .next().html('<span class="desaCircle ' + DESA_DATA_ONE(0.5,2.0,THIS_DATA.Lactate) + '"></span>');
    }
}

function PRINT_BODY_DETAIL_DATA(data, val){

    if(data.length == 0){
        data = false;
    }else{
        var THIS_DATA = data.filter(e => e.MEASUREMENT_SQ == val)[0];

        $('.myBody_HEIGHT').html(THIS_DATA.HEIGHT + ' Kg');
        $('.myBody_WEIGHT').html(THIS_DATA.WEIGHT + ' Kg');
        $('.myBody_BMI').html((THIS_DATA.WEIGHT / ((THIS_DATA.HEIGHT/100) + (THIS_DATA.HEIGHT/100))).toFixed(1) + ' Kg');
        $('.myBody_FAT').html(THIS_DATA.FAT + ' Kg');
        $('.myBody_MUSCLE').html(THIS_DATA.MUSCLE + ' Kg');

    }

}
function PRINT_POSE_DETAIL_DATA(data, val){

    resetMuscleTotal();

    if(data.length == 0){
        data = false;
    }else{
        var THIS_DATA = data.filter(e => e.MEASUREMENT_SQ == val)[0];

        $('#front-neck-').html(arrowPo_row(THIS_DATA.front_Neck) + PRINT_DANGER_RANGE_POSE(Number(THIS_DATA.front_Neck).toFixed(1),RANGE_POSE[0].front_Neck,RANGE_POSE[1].front_Neck));
        $('#front-shoulder-right').html(arrowPo_col(THIS_DATA.front_RShoulder,"left") + PRINT_DANGER_RANGE_POSE(Number(THIS_DATA.front_RShoulder).toFixed(1),RANGE_POSE[0].front_RShoulder,RANGE_POSE[1].front_RShoulder));
        $('#front-shoulder-left').html(arrowPo_col(THIS_DATA.front_LShoulder,"right") + PRINT_DANGER_RANGE_POSE(Number(THIS_DATA.front_LShoulder).toFixed(1),RANGE_POSE[0].front_LShoulder,RANGE_POSE[1].front_LShoulder));
        $('#front-pelvis-right').html(arrowPo_col(THIS_DATA.front_RPelvis,"left") + PRINT_DANGER_RANGE_POSE(Number(THIS_DATA.front_RPelvis).toFixed(1),RANGE_POSE[0].front_RPelvis,RANGE_POSE[1].front_RPelvis));
        $('#front-pelvis-left').html(arrowPo_col(THIS_DATA.front_LPelvis,"right") + PRINT_DANGER_RANGE_POSE(Number(THIS_DATA.front_LPelvis).toFixed(1),RANGE_POSE[0].front_LPelvis,RANGE_POSE[1].front_LPelvis));
        $('#front-leg-right').html(arrowPo_row_R_LEG(THIS_DATA.front_RLeg) + PRINT_DANGER_RANGE_POSE(Number(Math.abs(THIS_DATA.front_RLeg)-180).toFixed(1),RANGE_POSE[0].front_RLeg,RANGE_POSE[1].front_RLeg));
        $('#front-leg-left').html(arrowPo_row_L_LEG(THIS_DATA.front_LLeg) + PRINT_DANGER_RANGE_POSE(Number(Math.abs(THIS_DATA.front_LLeg)-180).toFixed(1),RANGE_POSE[0].front_LLeg,RANGE_POSE[1].front_LLeg));
        $('#side-neck-').html(arrowPo_row(THIS_DATA.side_Neck) + PRINT_DANGER_RANGE_POSE(Number(THIS_DATA.side_Neck).toFixed(1),RANGE_POSE[0].side_Neck,RANGE_POSE[1].side_Neck));
        $('#side-shoulder-').html(arrowPo_row(THIS_DATA.side_Shoulder) + PRINT_DANGER_RANGE_POSE(Number(THIS_DATA.side_Shoulder).toFixed(1),RANGE_POSE[0].side_Shoulder,RANGE_POSE[1].side_Shoulder));
        $('#side-pelvis-').html(arrowPo_row(THIS_DATA.side_Pelvis) + PRINT_DANGER_RANGE_POSE(Number(THIS_DATA.side_Pelvis).toFixed(1),RANGE_POSE[0].side_Pelvis,RANGE_POSE[1].side_Pelvis));
        $('#side-leg-').html(arrowPo_row(THIS_DATA.side_Leg) + PRINT_DANGER_RANGE_POSE(Number(THIS_DATA.side_Leg).toFixed(1),RANGE_POSE[0].side_Leg,RANGE_POSE[1].side_Leg));

        $('[title="체형검사정면사진"]').css({
            'background-image' : 'url(' + GET_PICTURE(val,'FRONT') + ')'
        });
        $('[title="체형검사측면사진"]').css({
            'background-image' : 'url(' + GET_PICTURE(val,'SIDE') + ')'
        });
        $('[title="문제근육정면체형이미지"]').attr('src', GET_PICTURE(val,'FRONT'));
        $('[title="문제근육측면체형이미지"]').attr('src', GET_PICTURE(val,'SIDE'));

        $('#errorMuscleView_pop > section table td > .rect').removeClass('yellow');
        $('#errorMuscleView_pop > section table td > .rect').removeClass('red');


        // 문제 근육 도트 표시
        $('#front-errorMuscle_dot_none').addClass('red');
        $('#front-errorMuscle_dot_R_Neck').addClass(errorMuscle(Number(THIS_DATA.front_Neck).toFixed(1) >= 0 ? Number(THIS_DATA.front_Neck).toFixed(1) : '',RANGE_POSE[0].front_Neck,RANGE_POSE[1].front_Neck));
        $('#front-errorMuscle_dot_L_Neck').addClass(errorMuscle(Number(THIS_DATA.front_Neck).toFixed(1) < 0 ? Number(THIS_DATA.front_Neck).toFixed(1) : '',RANGE_POSE[0].front_Neck,RANGE_POSE[1].front_Neck));
        $('#front-errorMuscle_dot_R_Shoulder').addClass(errorMuscle(Number(THIS_DATA.front_RShoulder).toFixed(1),RANGE_POSE[0].front_RShoulder,RANGE_POSE[1].front_RShoulder));
        $('#front-errorMuscle_dot_L_Shoulder').addClass(errorMuscle(Number(THIS_DATA.front_LShoulder).toFixed(1),RANGE_POSE[0].front_LShoulder,RANGE_POSE[1].front_LShoulder));
        $('#front-errorMuscle_dot_R_Pelvis').addClass(errorMuscle(Number(THIS_DATA.front_RPelvis).toFixed(1),RANGE_POSE[0].front_RPelvis,RANGE_POSE[1].front_RPelvis));
        $('#front-errorMuscle_dot_L_Pelvis').addClass(errorMuscle(Number(THIS_DATA.front_LPelvis).toFixed(1),RANGE_POSE[0].front_LPelvis,RANGE_POSE[1].front_LPelvis));
        $('#front-errorMuscle_dot_O_Leg').addClass(errorMuscle(Number(Math.abs(THIS_DATA.front_RLeg)-180).toFixed(1) >= 0 ? Number(Math.abs(THIS_DATA.front_RLeg)-180).toFixed(1) : '',RANGE_POSE[0].front_RLeg,RANGE_POSE[1].front_RLeg));
        $('#front-errorMuscle_dot_O_Leg').addClass(errorMuscle(Number(Math.abs(THIS_DATA.front_LLeg)-180).toFixed(1) >= 0 ? Number(Math.abs(THIS_DATA.front_LLeg)-180).toFixed(1) : '',RANGE_POSE[0].front_LLeg,RANGE_POSE[1].front_LLeg));
        $('#front-errorMuscle_dot_X_Leg').addClass(errorMuscle(Number(Math.abs(THIS_DATA.front_RLeg)-180).toFixed(1) < 0 ? Number(Math.abs(THIS_DATA.front_RLeg)-180).toFixed(1) : '',RANGE_POSE[0].front_RLeg,RANGE_POSE[1].front_RLeg));
        $('#front-errorMuscle_dot_X_Leg').addClass(errorMuscle(Number(Math.abs(THIS_DATA.front_LLeg)-180).toFixed(1) < 0 ? Number(Math.abs(THIS_DATA.front_LLeg)-180).toFixed(1) : '',RANGE_POSE[0].front_LLeg,RANGE_POSE[1].front_LLeg));

        $('#side-errorMuscle_dot_none').addClass('red');
        $('#side-errorMuscle_dot_Neck').addClass(errorMuscle(THIS_DATA.side_Neck,RANGE_POSE[0].side_Neck,RANGE_POSE[1].side_Neck));
        $('#side-errorMuscle_dot_Shoulder').addClass(errorMuscle(THIS_DATA.side_Shoulder,RANGE_POSE[0].side_Shoulder,RANGE_POSE[1].side_Shoulder));
        $('#side-errorMuscle_dot_f_Pelvis').addClass(errorMuscle(Number(THIS_DATA.side_Pelvis) >= 0 ? Number(THIS_DATA.side_Pelvis) : '',RANGE_POSE[0].side_Pelvis,RANGE_POSE[1].side_Pelvis));
        $('#side-errorMuscle_dot_b_Pelvis').addClass(errorMuscle(Number(THIS_DATA.side_Pelvis) < 0 ? Number(THIS_DATA.side_Pelvis) : '',RANGE_POSE[0].side_Pelvis,RANGE_POSE[1].side_Pelvis));
        $('#side-errorMuscle_dot_Leg').addClass(errorMuscle(Number(THIS_DATA.side_Leg) < 0 ? Number(THIS_DATA.side_Leg) : '',RANGE_POSE[0].side_Leg,RANGE_POSE[1].side_Leg));

        $('#errorMuscleView_pop > section table td > .front').each(function(){
            if($(this).attr('class').indexOf('red') > -1 || $(this).attr('class').indexOf('yellow') > -1){
                $('#front-errorMuscle_dot_none').removeClass('red');
            }
        });
        $('#errorMuscleView_pop > section table td > .side').each(function(){
            if($(this).attr('class').indexOf('red') > -1 || $(this).attr('class').indexOf('yellow') > -1){
                $('#side-errorMuscle_dot_none').removeClass('red');
            }
        });





        function errorMuscle(data, min, max){
            if(Math.abs(Number(data)).toFixed(1) < Number(min)){
                return '';
            }else if(Number(min) <= Math.abs(Number(data)).toFixed(1) && Math.abs(Number(data)).toFixed(1) < Number(max)){
                return 'yellow';
            }else if(Number(max) <= Math.abs(Number(data)).toFixed(1)){
                return 'red';
            }
        }


        $('#errorMuscleView_pop > section table td.cursor').click(function(){
            resetMuscleTotal();
            var filterClass = $(this).attr('class');
            if(filterClass.indexOf(' ') > -1){
                $('#errorMuscleView_pop > section table td.cursor').removeClass('active');
                $(this).addClass('active');

                filterClass = filterClass.split(' ');
                filterClass = filterClass[filterClass.length - 1];
                var chkDanger = $(this).next().find('.rect').attr('class');
                resetMuscleTotal();
                var data = $(this).attr('data-svg');
                if(data){
                    data = data.split(' ');
                    for(let i = 0; i < data.length; i++){
                        for(let ii = 0; ii < muscleSVG[data[i]].length; ii++){
                            $('#' + muscleSVG[data[i]][ii]).attr({
                                'stroke':'#f6a450',
                                'stroke-width':'14'
                            });
                        }
                    }
                }
                if(chkDanger.indexOf('yellow') > -1 || chkDanger.indexOf('red') > -1){
                    switch(filterClass){
                        case 'front_Neck':
                            errorMuscleFill_blue(Number(THIS_DATA.front_Neck).toFixed(1),RANGE_POSE[0].front_Neck,muscleSVG.Neck_right_blue,muscleSVG.Neck_left_blue);
                            errorMuscleFill_red(Number(THIS_DATA.front_Neck).toFixed(1),RANGE_POSE[0].front_Neck,muscleSVG.Neck_right_red,muscleSVG.Neck_left_red);
                            break;
                        case 'front_RShoulder':
                            errorMuscleFill_blue(Number(THIS_DATA.front_RShoulder).toFixed(1),RANGE_POSE[0].front_RShoulder,muscleSVG.Shoulder_right_up_blue,muscleSVG.Shoulder_right_down_blue);
                            errorMuscleFill_red(Number(THIS_DATA.front_RShoulder).toFixed(1),RANGE_POSE[0].front_RShoulder,muscleSVG.Shoulder_right_up_red,muscleSVG.Shoulder_right_down_red);
                            break;
                        case 'front_LShoulder':
                            errorMuscleFill_blue(Number(THIS_DATA.front_LShoulder).toFixed(1),RANGE_POSE[0].front_LShoulder,muscleSVG.Shoulder_left_up_blue,muscleSVG.Shoulder_left_down_blue);
                            errorMuscleFill_red(Number(THIS_DATA.front_LShoulder).toFixed(1),RANGE_POSE[0].front_LShoulder,muscleSVG.Shoulder_left_up_red,muscleSVG.Shoulder_left_down_red);
                            break;
                        case 'front_RPelvis':
                            errorMuscleFill_blue(Number(THIS_DATA.front_RPelvis).toFixed(1),RANGE_POSE[0].front_RPelvis,muscleSVG.Pelvis_right_up_blue,muscleSVG.Pelvis_right_down_blue);
                            errorMuscleFill_red(Number(THIS_DATA.front_RPelvis).toFixed(1),RANGE_POSE[0].front_RPelvis,muscleSVG.Pelvis_right_up_red,muscleSVG.Pelvis_right_down_red);
                            break;
                        case 'front_LPelvis':
                            errorMuscleFill_blue(Number(THIS_DATA.front_LPelvis).toFixed(1),RANGE_POSE[0].front_LPelvis,muscleSVG.Pelvis_left_up_blue,muscleSVG.Pelvis_left_down_blue);
                            errorMuscleFill_red(Number(THIS_DATA.front_LPelvis).toFixed(1),RANGE_POSE[0].front_LPelvis,muscleSVG.Pelvis_left_up_red,muscleSVG.Pelvis_left_down_red);
                            break;
                        case 'front_Leg':
                            errorMuscleFill_blue(Number(Math.abs(THIS_DATA.front_RLeg)-180).toFixed(1),RANGE_POSE[0].front_RLeg,muscleSVG.Leg_right_o_blue,muscleSVG.Leg_right_x_blue);
                            errorMuscleFill_red(Number(Math.abs(THIS_DATA.front_RLeg)-180).toFixed(1),RANGE_POSE[0].front_RLeg,muscleSVG.Leg_right_o_red,muscleSVG.Leg_right_x_red);
                            errorMuscleFill_blue(Number(Math.abs(THIS_DATA.front_LLeg)-180).toFixed(1),RANGE_POSE[0].front_LLeg,muscleSVG.Leg_left_o_blue,muscleSVG.Leg_left_x_blue);
                            errorMuscleFill_red(Number(Math.abs(THIS_DATA.front_LLeg)-180).toFixed(1),RANGE_POSE[0].front_LLeg,muscleSVG.Leg_left_o_red,muscleSVG.Leg_left_x_red);
                            break;
                        // case 'front_Leg':


                            // break;
                        case 'side_Neck':
                            errorMuscleFill_blue(Number(THIS_DATA.side_Neck).toFixed(1),RANGE_POSE[0].side_Neck,muscleSVG.Neck_front_blue,'');
                            errorMuscleFill_red(Number(THIS_DATA.side_Neck).toFixed(1),RANGE_POSE[0].side_Neck,muscleSVG.Neck_front_red,'');
                            break;
                        case 'side_Shoulder':
                            errorMuscleFill_blue(Number(THIS_DATA.side_Shoulder).toFixed(1),RANGE_POSE[0].side_Shoulder,muscleSVG.Shoulder_front_blue,'');
                            errorMuscleFill_red(Number(THIS_DATA.side_Shoulder).toFixed(1),RANGE_POSE[0].side_Shoulder,muscleSVG.Shoulder_front_red,'');
                            break;
                        case 'side_Pelvis':
                            errorMuscleFill_blue(Number(THIS_DATA.side_Pelvis).toFixed(1),RANGE_POSE[0].side_Pelvis,muscleSVG.Pelvis_front_blue,muscleSVG.Pelvis_back_blue);
                            errorMuscleFill_red(Number(THIS_DATA.side_Pelvis).toFixed(1),RANGE_POSE[0].side_Pelvis,muscleSVG.Pelvis_front_red,muscleSVG.Pelvis_back_red);
                            break;
                        case 'side_Leg':
                            errorMuscleFill_blue(Number(THIS_DATA.side_Leg).toFixed(1),RANGE_POSE[0].side_Leg,'',muscleSVG.Leg_back_blue);
                            errorMuscleFill_red(Number(THIS_DATA.side_Leg).toFixed(1),RANGE_POSE[0].side_Leg,'',muscleSVG.Leg_back_red);
                            break;

                    }
                }
            }else{
                return false;
            }

        });

    }

    function errorMuscleFill_blue(data,min,plus,minus){
        if(Number(data) >= 0){
            if(Math.abs(Number(data)) >= Number(min)){
                for(let i in plus){
                    $('#' + plus[i]).css('fill','rgb(45, 8, 212)');
                }
            }
        }else{
            if(Math.abs(Number(data)) >= Number(min)){
                for(let i in minus){
                    $('#' + minus[i]).css('fill','rgb(45, 8, 212)');
                }
            }
        }
    }

    function errorMuscleFill_red(data,min,plus,minus){

        if(Number(data) >= 0){
            if(Math.abs(Number(data)) >= Number(min)){
                for(let i in plus){
                    $('#' + plus[i]).css('fill','rgb(238, 40, 40)');
                }
            }
        }else{
            if(Math.abs(Number(data)) >= Number(min)){
                for(let i in minus){
                    $('#' + minus[i]).css('fill','rgb(238, 40, 40)');
                }
            }
        }

    }

}

function PRINT_ROM_DETAIL_DATA(data, val){
    if(data.length == 0){
        data = false;
    }else{
        var THIS_DATA = data.filter(e => e.MEASUREMENT_SQ == val)[0];

        // DATA
        $('#ROM-front-neck-right').html(PRINT_DANGER_RANGE_ROM(Number(THIS_DATA.front_Neck_right).toFixed(1), RANGE_ROM[1].front_Neck_right, RANGE_ROM[0].front_Neck_right) + Number(THIS_DATA.front_Neck_right).toFixed(1) + '˚');
        $('#ROM-front-neck-left').html(PRINT_DANGER_RANGE_ROM(Number(THIS_DATA.front_Neck_left).toFixed(1), RANGE_ROM[1].front_Neck_left, RANGE_ROM[0].front_Neck_left) + Number(THIS_DATA.front_Neck_left).toFixed(1) + '˚');
        $('#ROM-front-shoulder-right').html(PRINT_DANGER_RANGE_ROM(Number(THIS_DATA.front_Shoulder_right).toFixed(1), RANGE_ROM[1].front_Shoulder_right, RANGE_ROM[0].front_Shoulder_right) + Number(THIS_DATA.front_Shoulder_right).toFixed(1) + '˚');
        $('#ROM-front-shoulder-left').html(PRINT_DANGER_RANGE_ROM(Number(THIS_DATA.front_Shoulder_left).toFixed(1), RANGE_ROM[1].front_Shoulder_left, RANGE_ROM[0].front_Shoulder_left) + Number(THIS_DATA.front_Shoulder_left).toFixed(1) + '˚');
        $('#ROM-front-pelvis-right').html(PRINT_DANGER_RANGE_ROM(Number(THIS_DATA.front_Waist_right).toFixed(1), RANGE_ROM[1].front_Waist_right, RANGE_ROM[0].front_Waist_right) + Number(THIS_DATA.front_Waist_right).toFixed(1) + '˚');
        $('#ROM-front-pelvis-left').html(PRINT_DANGER_RANGE_ROM(Number(THIS_DATA.front_Waist_left).toFixed(1), RANGE_ROM[1].front_Waist_left, RANGE_ROM[0].front_Waist_left) + Number(THIS_DATA.front_Waist_left).toFixed(1) + '˚');
        $('#ROM-front-leg-right').html(PRINT_DANGER_RANGE_ROM(Number(THIS_DATA.front_Hip_right).toFixed(1), RANGE_ROM[1].front_Hip_right, RANGE_ROM[0].front_Hip_right) + Number(THIS_DATA.front_Hip_right).toFixed(1) + '˚');
        $('#ROM-front-leg-left').html(PRINT_DANGER_RANGE_ROM(Number(THIS_DATA.front_Hip_left).toFixed(1), RANGE_ROM[1].front_Hip_left, RANGE_ROM[0].front_Hip_left) + Number(THIS_DATA.front_Hip_left).toFixed(1) + '˚') + '˚';
        $('#ROM-side-neck-front').html(PRINT_DANGER_RANGE_ROM(Number(THIS_DATA.side_Neck_front).toFixed(1), RANGE_ROM[1].side_Neck_front, RANGE_ROM[0].side_Neck_front) + Number(THIS_DATA.side_Neck_front).toFixed(1) + '˚');
        $('#ROM-side-neck-back').html(PRINT_DANGER_RANGE_ROM(Number(THIS_DATA.side_Neck_back).toFixed(1), RANGE_ROM[1].side_Neck_back, RANGE_ROM[0].side_Neck_back) + Number(THIS_DATA.side_Neck_back).toFixed(1) + '˚');
        $('#ROM-side-shoulder-left-front').html(PRINT_DANGER_RANGE_ROM(Number(THIS_DATA.side_ShoulderL_front).toFixed(1), RANGE_ROM[1].side_ShoulderL_front, RANGE_ROM[0].side_ShoulderL_front) + Number(THIS_DATA.side_ShoulderL_front).toFixed(1) + '˚');
        $('#ROM-side-shoulder-left-back').html(PRINT_DANGER_RANGE_ROM(Number(THIS_DATA.side_ShoulderL_back).toFixed(1), RANGE_ROM[1].side_ShoulderL_back, RANGE_ROM[0].side_ShoulderL_back) + Number(THIS_DATA.side_ShoulderL_back).toFixed(1) + '˚');
        $('#ROM-side-shoulder-right-front').html(PRINT_DANGER_RANGE_ROM(Number(THIS_DATA.side_ShoulderR_front).toFixed(1), RANGE_ROM[1].side_ShoulderR_front, RANGE_ROM[0].side_ShoulderR_front) + Number(THIS_DATA.side_ShoulderR_front).toFixed(1) + '˚');
        $('#ROM-side-shoulder-right-back').html(PRINT_DANGER_RANGE_ROM(Number(THIS_DATA.side_ShoulderR_back).toFixed(1), RANGE_ROM[1].side_ShoulderR_back, RANGE_ROM[0].side_ShoulderR_back) + Number(THIS_DATA.side_ShoulderR_back).toFixed(1) + '˚');
        $('#ROM-side-pelvis-front').html(PRINT_DANGER_RANGE_ROM(Number(THIS_DATA.side_Waist_front).toFixed(1), RANGE_ROM[1].side_Waist_front, RANGE_ROM[0].side_Waist_front) + Number(THIS_DATA.side_Waist_front).toFixed(1) + '˚');
        $('#ROM-side-pelvis-back').html(PRINT_DANGER_RANGE_ROM(Number(THIS_DATA.side_Waist_back).toFixed(1), RANGE_ROM[1].side_Waist_back, RANGE_ROM[0].side_Waist_back) + Number(THIS_DATA.side_Waist_back).toFixed(1) + '˚');
        $('#ROM-side-leg-left-front').html(PRINT_DANGER_RANGE_ROM(Number(THIS_DATA.side_HipL_front).toFixed(1), RANGE_ROM[1].side_HipL_front, RANGE_ROM[0].side_HipL_front) + Number(THIS_DATA.side_HipL_front).toFixed(1) + '˚');
        $('#ROM-side-leg-left-back').html(PRINT_DANGER_RANGE_ROM(Number(THIS_DATA.side_HipL_back).toFixed(1), RANGE_ROM[1].side_HipL_back, RANGE_ROM[0].side_HipL_back) + Number(THIS_DATA.side_HipL_back).toFixed(1) + '˚');
        $('#ROM-side-leg-right-front').html(PRINT_DANGER_RANGE_ROM(Number(THIS_DATA.side_HipR_front).toFixed(1), RANGE_ROM[1].side_HipR_front, RANGE_ROM[0].side_HipR_front) + Number(THIS_DATA.side_HipR_front).toFixed(1) + '˚');
        $('#ROM-side-leg-right-back').html(PRINT_DANGER_RANGE_ROM(Number(THIS_DATA.side_HipR_back).toFixed(1), RANGE_ROM[1].side_HipR_back, RANGE_ROM[0].side_HipR_back) + Number(THIS_DATA.side_HipR_back).toFixed(1) + '˚');
        // $('div.pop article.pop5 > div.info > section.rom_info > div > table > tbody td.numData').append('<span class="dotted"></span>');

        // ICON
        $('#ROM-front-neck-right-pain').html(ROM_PAIN_ICON(THIS_DATA.front_Neck_right_grade));
        $('#ROM-front-neck-left-pain').html(ROM_PAIN_ICON(THIS_DATA.front_Neck_left_grade));
        $('#ROM-front-shoulder-right-pain').html(ROM_PAIN_ICON(THIS_DATA.front_Shoulder_right_grade));
        $('#ROM-front-shoulder-left-pain').html(ROM_PAIN_ICON(THIS_DATA.front_Shoulder_left_grade));
        $('#ROM-front-pelvis-right-pain').html(ROM_PAIN_ICON(THIS_DATA.front_Waist_right_grade));
        $('#ROM-front-pelvis-left-pain').html(ROM_PAIN_ICON(THIS_DATA.front_Waist_left_grade));
        $('#ROM-front-leg-right-pain').html(ROM_PAIN_ICON(THIS_DATA.front_Hip_right_grade));
        $('#ROM-front-leg-left-pain').html(ROM_PAIN_ICON(THIS_DATA.front_Hip_left_grade));
        $('#ROM-side-neck-front-pain').html(ROM_PAIN_ICON(THIS_DATA.side_Neck_front_grade));
        $('#ROM-side-neck-back-pain').html(ROM_PAIN_ICON(THIS_DATA.side_Neck_back_grade));
        $('#ROM-side-shoulder-left-front-pain').html(ROM_PAIN_ICON(THIS_DATA.side_ShoulderL_front_grade));
        $('#ROM-side-shoulder-left-back-pain').html(ROM_PAIN_ICON(THIS_DATA.side_ShoulderL_back_grade));
        $('#ROM-side-shoulder-right-front-pain').html(ROM_PAIN_ICON(THIS_DATA.side_ShoulderR_front_grade));
        $('#ROM-side-shoulder-right-back-pain').html(ROM_PAIN_ICON(THIS_DATA.side_ShoulderR_back_grade));
        $('#ROM-side-pelvis-front-pain').html(ROM_PAIN_ICON(THIS_DATA.side_Waist_front_grade));
        $('#ROM-side-pelvis-back-pain').html(ROM_PAIN_ICON(THIS_DATA.side_Waist_back_grade));
        $('#ROM-side-leg-left-front-pain').html(ROM_PAIN_ICON(THIS_DATA.side_HipL_front_grade));
        $('#ROM-side-leg-left-back-pain').html(ROM_PAIN_ICON(THIS_DATA.side_HipL_back_grade));
        $('#ROM-side-leg-right-front-pain').html(ROM_PAIN_ICON(THIS_DATA.side_HipR_front_grade));
        $('#ROM-side-leg-right-back-pain').html(ROM_PAIN_ICON(THIS_DATA.side_HipR_back_grade));

        // 벨런스
        if($('div.pop article.pop5 > div.info > section.rom_info > p > button.active').index() == 0){       // 정면
            MAKE_PROGRESS_BAR(THIS_DATA.front_Neck_right, RANGE_ROM[1].front_Neck_right, RANGE_ROM[0].front_Neck_right,$('.data-chart.bar1 > div'));
            MAKE_PROGRESS_BAR(THIS_DATA.front_Neck_left, RANGE_ROM[1].front_Neck_left, RANGE_ROM[0].front_Neck_left,$('.data-chart.bar2 > div'));
            MAKE_PROGRESS_BAR(THIS_DATA.front_Shoulder_right, RANGE_ROM[1].front_Shoulder_right, RANGE_ROM[0].front_Shoulder_right,$('.data-chart.bar3 > div'));
            MAKE_PROGRESS_BAR(THIS_DATA.front_Shoulder_left, RANGE_ROM[1].front_Shoulder_left, RANGE_ROM[0].front_Shoulder_left,$('.data-chart.bar4 > div'));
            MAKE_PROGRESS_BAR(THIS_DATA.front_Waist_right, RANGE_ROM[1].front_Waist_right, RANGE_ROM[0].front_Waist_right,$('.data-chart.bar5 > div'));
            MAKE_PROGRESS_BAR(THIS_DATA.front_Waist_left, RANGE_ROM[1].front_Waist_left, RANGE_ROM[0].front_Waist_left,$('.data-chart.bar6 > div'));
            MAKE_PROGRESS_BAR(THIS_DATA.front_Hip_right, RANGE_ROM[1].front_Hip_right, RANGE_ROM[0].front_Hip_right,$('.data-chart.bar7 > div'));
            MAKE_PROGRESS_BAR(THIS_DATA.front_Hip_left, RANGE_ROM[1].front_Hip_left, RANGE_ROM[0].front_Hip_left,$('.data-chart.bar8 > div'));
        }else{                                                                                              // 측면
            MAKE_PROGRESS_BAR(THIS_DATA.side_Neck_front, RANGE_ROM[1].side_Neck_front, RANGE_ROM[0].side_Neck_front,$('.data-chart.bar1 > div'));
            MAKE_PROGRESS_BAR(THIS_DATA.side_Neck_back, RANGE_ROM[1].side_Neck_back, RANGE_ROM[0].side_Neck_back,$('.data-chart.bar2 > div'));
            MAKE_PROGRESS_BAR(THIS_DATA.side_ShoulderL_front, RANGE_ROM[1].side_ShoulderL_front, RANGE_ROM[0].side_ShoulderL_front,$('.data-chart.bar3 > div'));
            MAKE_PROGRESS_BAR(THIS_DATA.side_ShoulderL_back, RANGE_ROM[1].side_ShoulderL_back, RANGE_ROM[0].side_ShoulderL_back,$('.data-chart.bar4 > div'));
            MAKE_PROGRESS_BAR(THIS_DATA.side_ShoulderR_front, RANGE_ROM[1].side_ShoulderR_front, RANGE_ROM[0].side_ShoulderR_front,$('.data-chart.bar5 > div'));
            MAKE_PROGRESS_BAR(THIS_DATA.side_ShoulderR_back, RANGE_ROM[1].side_ShoulderR_back, RANGE_ROM[0].side_ShoulderR_back,$('.data-chart.bar6 > div'));
            MAKE_PROGRESS_BAR(THIS_DATA.side_Waist_front, RANGE_ROM[1].side_Waist_front, RANGE_ROM[0].side_Waist_front,$('.data-chart.bar7 > div'));
            MAKE_PROGRESS_BAR(THIS_DATA.side_Waist_back, RANGE_ROM[1].side_Waist_back, RANGE_ROM[0].side_Waist_back,$('.data-chart.bar8 > div'));
            MAKE_PROGRESS_BAR(THIS_DATA.side_HipL_front, RANGE_ROM[1].side_HipL_front, RANGE_ROM[0].side_HipL_front,$('.data-chart.bar9 > div'));
            MAKE_PROGRESS_BAR(THIS_DATA.side_HipL_back, RANGE_ROM[1].side_HipL_back, RANGE_ROM[0].side_HipL_back,$('.data-chart.bar10 > div'));
            MAKE_PROGRESS_BAR(THIS_DATA.side_HipR_front, RANGE_ROM[1].side_HipR_front, RANGE_ROM[0].side_HipR_front,$('.data-chart.bar11 > div'));
            MAKE_PROGRESS_BAR(THIS_DATA.side_HipR_back, RANGE_ROM[1].side_HipR_back, RANGE_ROM[0].side_HipR_back,$('.data-chart.bar12 > div'));

        }



    }
}

function MAKE_PROGRESS_BAR(data, min, max, where){
    var color = ['rgb(5,207,5)','rgb(255, 196, 0)','rgb(255, 38, 21)'];
    var test = '';
    var calc = '';
    if(Math.abs(Number(data)) < Number(min)){
        test = 'Bad';
        calc = '100%';

    }else if(Number(min) <= Math.abs(Number(data)) && Math.abs(Number(data)) < Number(max)){
        test = 'Normal';
        calc = '200%';

    }else if(Number(max) <= Math.abs(Number(data))){
        test = 'Good';
        calc = '300%';
    }

    switch(test){
        case 'Good':
            where.css({'width': calc ,'background-color' : color[0]});
            break;

        case 'Normal':
            where.css({'width': calc ,'background-color' : color[1]});
            break;

        case 'Bad':
            where.css({'width': calc ,'background-color' : color[2]});
            break;
    }
}

function PRINT_DESA_DETAIL_DATA(data, val){

}

// 경고 범위
function PRINT_DANGER_RANGE_POSE(data, min, max){
    var tag = [
        '<div class="dotted" style="background-color:transparent"></div>',
        '<div class="dotted" style="background-color:rgb(255, 196, 0)"></div>',
        '<div class="dotted" style="background-color:rgb(255, 38, 21)"></div>'
    ];
    if(Math.abs(Number(data)) < Number(min)){
        return tag[0];
    }else if(Number(min) <= Math.abs(Number(data)) && Math.abs(Number(data)) < Number(max)){
        return tag[1];
    }else if(Number(max) <= Math.abs(Number(data))){
        return tag[2];
    }
}
function PRINT_DANGER_RANGE_ROM(data, min, max){
    var tag = [
        '<div class="dotted" style="background-color:transparent"></div>',
        '<div class="dotted" style="background-color:rgb(255, 196, 0)"></div>',
        '<div class="dotted" style="background-color:rgb(255, 38, 21)"></div>'
    ];
    if(Math.abs(Number(data)) < Number(min)){
        return tag[2];
    }else if(Number(min) <= Math.abs(Number(data)) && Math.abs(Number(data)) < Number(max)){
        return tag[1];
    }else if(Number(max) <= Math.abs(Number(data))){
        return tag[0];
    }
}

// 이전 측정일과 최근 측정일 계산 함수
function BEFORE_AFTER_CALC(){
    var front = $('.pose_change > div.option > button.active').index() == 0 ? true : false;
    var before = MEASUREMENT_POSE_DATA.filter(e => e.MEASUREMENT_SQ == $('#before-date').val())[0];
    var after = MEASUREMENT_POSE_DATA.filter(e => e.MEASUREMENT_SQ == $('#after-date').val())[0];

    $('span.calc').remove();
    if(front){
        $('#neck_after').append(
            print_row(before.front_Neck,after.front_Neck)
        );
        $('#shoulder_after1').append(
            print_col(before.front_RShoulder,after.front_RShoulder)
        );
        $('#shoulder_after2').append(
            print_col(before.front_LShoulder,after.front_LShoulder)
        );
        $('#pelvis_after1').append(
            print_col(before.front_RPelvis,after.front_RPelvis)
        );
        $('#pelvis_after2').append(
            print_col(before.front_LPelvis,after.front_LPelvis)
        );
        $('#leg_after1').append(
            print_row(Number(before.front_RLeg - 180),Number(after.front_RLeg - 180))
        );
        $('#leg_after2').append(
            print_row(Number(before.front_LLeg - 180),Number(after.front_LLeg - 180))
        );

    }else{
        $('#neck_after').append(
            print_row(before.side_Neck,after.side_Neck)
        );
        $('#shoulder_after1').append(
            print_row(before.side_Shoulder,after.side_Shoulder)
        );
        $('#pelvis_after1').append(
            print_row(before.side_Pelvis,after.side_Pelvis)
        );
        $('#leg_after1').append(
            print_row(before.side_Leg,after.side_Leg)
        );


    }



    function print_row_leg(num1, num2){
        var result = (Number(Number(num1).toFixed(1))-180) - (Number(Number(num2).toFixed(1))-180);
        if(result == 0){
            return '';
        }else if(result < 0){
            return '<span class="calc"><small>(<i class="fas fa-caret-left"></i>' + Math.abs(result.toFixed(1)) + '˚)</small></span>';
        }else if(result > 0){
            return '<span class="calc"><small>(<i class="fas fa-caret-right"></i>' + Math.abs(result.toFixed(1)) + '˚)</small></span>';
        }
    }


    function print_row(num1, num2){
        var result = Number(Number(num1).toFixed(1)) - Number(Number(num2).toFixed(1));
        if(result == 0){
            return '';
        }else if(result < 0){
            return '<span class="calc"><small>(<i class="fas fa-caret-left"></i>' + Math.abs(result.toFixed(1)) + '˚)</small></span>';
        }else if(result > 0){
            return '<span class="calc"><small>(<i class="fas fa-caret-right"></i>' + Math.abs(result.toFixed(1)) + '˚)</small></span>';
        }
    }

    function print_col(num1, num2){
        var result = Number(Number(num1).toFixed(1)) - Number(Number(num2).toFixed(1));
        if(result == 0){
            return '';
        }else if(result < 0){
            return '<span class="calc"><small>(<i class="fas fa-caret-up"></i>' + Math.abs(result.toFixed(1)) + '˚)</small></span>';
        }else if(result > 0){
            return '<span class="calc"><small>(<i class="fas fa-caret-down"></i>' + Math.abs(result.toFixed(1)) + '˚)</small></span>';
        }
    }
}

// ROM검사 pain 아이콘 변경 함수
function ROM_PAIN_ICON(pain){
    switch(pain){
        case 'GOOD' :
            return '<div class="pain" style="background-image:url(img/good.png)"></div>';
        case 'NOT BAD' :
            return '<div class="pain" style="background-image:url(img/normal.png)"></div>';
        case 'BAD' :
            return '<div class="pain" style="background-image:url(img/bad.png)"></div>';
    }


}

// 측정 이미지 가져오기
function GET_PICTURE(val,dir){
    var find = MEASUREMENT_BODY_IMG.filter(e => e.MEASUREMENT_SQ == val)[0];

    switch(dir){
        case 'FRONT' :
            return find.UPLOAD_ROOT + find.FRONT_PICTURE + '.png';

        case 'SIDE' :
            return find.UPLOAD_ROOT + find.SIDE_PICTURE + '.png';
    }
}

// 신체정보 직접입력시 최근데이터
function PRINT_lastData_BODY(){
    var data = MEASUREMENT_BODY_DATA[MEASUREMENT_BODY_DATA.length - 1];
    if(data){
        $('.writeHEIGHT').text(data.HEIGHT + ' Cm');
        $('.writeWEIGHT').text(data.WEIGHT + ' Kg');
        $('.writeFAT').text(data.FAT + ' Kg');
        $('.writeMUSCLE').text(data.MUSCLE + ' Kg');
    }
}
function PRINT_lastData_DESA(){
    var data = MEASUREMENT_DESA_DATA[MEASUREMENT_DESA_DATA.length - 1];
    if(data){
        $('.lastDesa_HR').text(data.HR + ' HR');
        $('.lastDesa_SBP_DBP').text(data.SBP + ' SBP / ' + data.DBP + ' DBP');
        $('.lastDesa_Glucose').text(data.GLUCOSE + ' mg/dl');
        $('.lastDesa_HbA1c').text(data.HbA1c + ' %');
        $('.lastDesa_TC').text(data.TC + ' TC');
        $('.lastDesa_HDL_LDL').text(data.HDL + ' HDL / ' + data.LDL + ' LDL');
        $('.lastDesa_TG').text(data.TG + ' TG');
        $('.lastDesa_Lactate').text(data.Lactate + ' Lactate');
    }
}

// 비교결과 날짜 선택 함수
function PRINT_POSE_FRONT_BEFORE(data, val){
    if(data.length == 0){
        data = false;
    }else{
        var THIS_DATA = data.filter(e => e.MEASUREMENT_SQ == val)[0];
        $('#neck_before').html(arrowPo_row(THIS_DATA.front_Neck));
        $('#shoulder_before1').html(arrowPo_col(THIS_DATA.front_RShoulder,"left"));
        $('#shoulder_before2').html(arrowPo_col(THIS_DATA.front_LShoulder,"right"));
        $('#pelvis_before1').html(arrowPo_col(THIS_DATA.front_RPelvis,"left"));
        $('#pelvis_before2').html(arrowPo_col(THIS_DATA.front_LPelvis,"right"));
        $('#leg_before1').html(arrowPo_row_R_LEG(THIS_DATA.front_RLeg));
        $('#leg_before2').html(arrowPo_row_L_LEG(THIS_DATA.front_LLeg));

        $('[title="체형검사이전측정일"]').css({
            'background-image' : 'url(' + GET_PICTURE(val,'FRONT') + ')'
        });
        BEFORE_AFTER_CALC();
    }
}
function PRINT_POSE_FRONT_AFTER(data, val){
    if(data.length == 0){
        data = false;
    }else{
        var THIS_DATA = data.filter(e => e.MEASUREMENT_SQ == val)[0];
        $('#neck_after').html(arrowPo_row(THIS_DATA.front_Neck));
        $('#shoulder_after1').html(arrowPo_col(THIS_DATA.front_RShoulder,"left"));
        $('#shoulder_after2').html(arrowPo_col(THIS_DATA.front_LShoulder,"right"));
        $('#pelvis_after1').html(arrowPo_col(THIS_DATA.front_RPelvis,"left"));
        $('#pelvis_after2').html(arrowPo_col(THIS_DATA.front_LPelvis,"right"));
        $('#leg_after1').html(arrowPo_row_R_LEG(THIS_DATA.front_RLeg));
        $('#leg_after2').html(arrowPo_row_L_LEG(THIS_DATA.front_LLeg));

        $('[title="체형검사최근측정일"]').css({
            'background-image' : 'url(' + GET_PICTURE(val,'FRONT') + ')'
        });
        BEFORE_AFTER_CALC();
    }
}
function PRINT_POSE_SIDE_BEFORE(data, val){
    if(data.length == 0){
        data = false;
    }else{
        var THIS_DATA = data.filter(e => e.MEASUREMENT_SQ == val)[0];
        $('#neck_before').html(arrowPo_row(THIS_DATA.side_Neck));
        $('#shoulder_before1').html(arrowPo_row(THIS_DATA.side_Shoulder));
        $('#pelvis_before1').html(arrowPo_row(THIS_DATA.side_Pelvis));
        $('#leg_before1').html(arrowPo_row(THIS_DATA.side_Leg));

        $('[title="체형검사이전측정일"]').css({
            'background-image' : 'url(' + GET_PICTURE(val,'SIDE') + ')'
        });
        BEFORE_AFTER_CALC();
    }
}
function PRINT_POSE_SIDE_AFTER(data, val){
    if(data.length == 0){
        data = false;
    }else{
        var THIS_DATA = data.filter(e => e.MEASUREMENT_SQ == val)[0];
        $('#neck_after').html(arrowPo_row(THIS_DATA.side_Neck));
        $('#shoulder_after1').html(arrowPo_row(THIS_DATA.side_Shoulder));
        $('#pelvis_after1').html(arrowPo_row(THIS_DATA.side_Pelvis));
        $('#leg_after1').html(arrowPo_row(THIS_DATA.side_Leg));

        $('[title="체형검사최근측정일"]').css({
            'background-image' : 'url(' + GET_PICTURE(val,'SIDE') + ')'
        });
        BEFORE_AFTER_CALC();
    }
}


// ROM검사결과 날짜 선택 함수
function PRINT_ROM_FRONT_BEFORE(data, val){
    if(data){
        var THIS_DATA = data.filter(e => e.MEASUREMENT_SQ == val)[0];
        if(THIS_DATA){
            $('#ROM_RIGHT_BEFORE_NECK').html(Number(THIS_DATA.front_Neck_right).toFixed(1) + '˚');
            $('#ROM_RIGHT_BEFORE_ShOULDER').html(Number(THIS_DATA.front_Shoulder_right).toFixed(1) + '˚');
            $('#ROM_RIGHT_BEFORE_TRUNK').html(Number(THIS_DATA.front_Waist_right).toFixed(1) + '˚');
            $('#ROM_RIGHT_BEFORE_LEG').html(Number(THIS_DATA.front_Hip_right).toFixed(1) + '˚');
            $('#ROM_LEFT_BEFORE_NECK').html(Number(THIS_DATA.front_Neck_left).toFixed(1) + '˚');
            $('#ROM_LEFT_BEFORE_ShOULDER').html(Number(THIS_DATA.front_Shoulder_left).toFixed(1) + '˚');
            $('#ROM_LEFT_BEFORE_TRUNK').html(Number(THIS_DATA.front_Waist_left).toFixed(1) + '˚');
            $('#ROM_LEFT_BEFORE_LEG').html(Number(THIS_DATA.front_Hip_left).toFixed(1) + '˚');
            BEFORE_AFTER_CALC_ROM();
        }
    }
}
function PRINT_ROM_FRONT_AFTER(data, val){
    if(data){
        var THIS_DATA = data.filter(e => e.MEASUREMENT_SQ == val)[0];
        if(THIS_DATA){
            $('#ROM_RIGHT_AFTER_NECK').html(Number(THIS_DATA.front_Neck_right).toFixed(1) + '˚');
            $('#ROM_RIGHT_AFTER_ShOULDER').html(Number(THIS_DATA.front_Shoulder_right).toFixed(1) + '˚');
            $('#ROM_RIGHT_AFTER_TRUNK').html(Number(THIS_DATA.front_Waist_right).toFixed(1) + '˚');
            $('#ROM_RIGHT_AFTER_LEG').html(Number(THIS_DATA.front_Hip_right).toFixed(1) + '˚');
            $('#ROM_LEFT_AFTER_NECK').html(Number(THIS_DATA.front_Neck_left).toFixed(1) + '˚');
            $('#ROM_LEFT_AFTER_ShOULDER').html(Number(THIS_DATA.front_Shoulder_left).toFixed(1) + '˚');
            $('#ROM_LEFT_AFTER_TRUNK').html(Number(THIS_DATA.front_Waist_left).toFixed(1) + '˚');
            $('#ROM_LEFT_AFTER_LEG').html(Number(THIS_DATA.front_Hip_left).toFixed(1) + '˚');
            BEFORE_AFTER_CALC_ROM();
        }
    }
}
function PRINT_ROM_SIDE_BEFORE(data, val){
    if(data){
        var THIS_DATA = data.filter(e => e.MEASUREMENT_SQ == val)[0];
        if(THIS_DATA){
            $('#ROM_RIGHT_BEFORE_NECK').html(Number(THIS_DATA.side_Neck_front).toFixed(1) + '˚');
            $('#ROM_RIGHT_BEFORE_ShOULDER').html(Number(THIS_DATA.side_ShoulderR_front).toFixed(1) + '˚');
            $('#ROM_RIGHT_BEFORE_ShOULDER2').html(Number(THIS_DATA.side_ShoulderL_front).toFixed(1) + '˚');
            $('#ROM_RIGHT_BEFORE_TRUNK').html(Number(THIS_DATA.side_Waist_front).toFixed(1) + '˚');
            $('#ROM_RIGHT_BEFORE_LEG').html(Number(THIS_DATA.side_HipR_front).toFixed(1) + '˚');
            $('#ROM_RIGHT_BEFORE_LEG2').html(Number(THIS_DATA.side_HipL_front).toFixed(1) + '˚');

            $('#ROM_LEFT_BEFORE_NECK').html(Number(THIS_DATA.side_Neck_back).toFixed(1) + '˚');
            $('#ROM_LEFT_BEFORE_ShOULDER').html(Number(THIS_DATA.side_ShoulderR_back).toFixed(1) + '˚');
            $('#ROM_LEFT_BEFORE_ShOULDER2').html(Number(THIS_DATA.side_ShoulderL_back).toFixed(1) + '˚');
            $('#ROM_LEFT_BEFORE_TRUNK').html(Number(THIS_DATA.side_Waist_back).toFixed(1) + '˚');
            $('#ROM_LEFT_BEFORE_LEG').html(Number(THIS_DATA.side_HipR_back).toFixed(1) + '˚');
            $('#ROM_LEFT_BEFORE_LEG2').html(Number(THIS_DATA.side_HipL_back).toFixed(1) + '˚');
            BEFORE_AFTER_CALC_ROM();
        }
    }
}
function PRINT_ROM_SIDE_AFTER(data, val){
    if(data){
        var THIS_DATA = data.filter(e => e.MEASUREMENT_SQ == val)[0];
        if(THIS_DATA){
            $('#ROM_RIGHT_AFTER_NECK').html(Number(THIS_DATA.side_Neck_front).toFixed(1) + '˚');
            $('#ROM_RIGHT_AFTER_ShOULDER').html(Number(THIS_DATA.side_ShoulderR_front).toFixed(1) + '˚');
            $('#ROM_RIGHT_AFTER_ShOULDER2').html(Number(THIS_DATA.side_ShoulderL_front).toFixed(1) + '˚');
            $('#ROM_RIGHT_AFTER_TRUNK').html(Number(THIS_DATA.side_Waist_front).toFixed(1) + '˚');
            $('#ROM_RIGHT_AFTER_LEG').html(Number(THIS_DATA.side_HipR_front).toFixed(1) + '˚');
            $('#ROM_RIGHT_AFTER_LEG2').html(Number(THIS_DATA.side_HipL_front).toFixed(1) + '˚');

            $('#ROM_LEFT_AFTER_NECK').html(Number(THIS_DATA.side_Neck_back).toFixed(1) + '˚');
            $('#ROM_LEFT_AFTER_ShOULDER').html(Number(THIS_DATA.side_ShoulderR_back).toFixed(1) + '˚');
            $('#ROM_LEFT_AFTER_ShOULDER2').html(Number(THIS_DATA.side_ShoulderL_back).toFixed(1) + '˚');
            $('#ROM_LEFT_AFTER_TRUNK').html(Number(THIS_DATA.side_Waist_back).toFixed(1) + '˚');
            $('#ROM_LEFT_AFTER_LEG').html(Number(THIS_DATA.side_HipR_back).toFixed(1) + '˚');
            $('#ROM_LEFT_AFTER_LEG2').html(Number(THIS_DATA.side_HipL_back).toFixed(1) + '˚');
            BEFORE_AFTER_CALC_ROM();
        }
    }
}

function BEFORE_AFTER_CALC_ROM(){
    var front = $('.rom_change > div.option > button.active').index() == 0 ? true : false;
    var before = MEASUREMENT_ROM_DATA.filter(e => e.MEASUREMENT_SQ == $('#before-date-rom').val())[0];
    var after = MEASUREMENT_ROM_DATA.filter(e => e.MEASUREMENT_SQ == $('#after-date-rom').val())[0];

    $('span.calc-rom').remove();
    if(front){
        $('#ROM_RIGHT_AFTER_NECK').append(
            calc(before.front_Neck_right, after.front_Neck_right)
        );
        $('#ROM_RIGHT_AFTER_ShOULDER').append(
            calc(before.front_Shoulder_right, after.front_Shoulder_right)
        );
        $('#ROM_RIGHT_AFTER_TRUNK').append(
            calc(before.front_Waist_right, after.front_Waist_right)
        );
        $('#ROM_RIGHT_AFTER_LEG').append(
            calc(before.front_Hip_right, after.front_Hip_right)
        );

        $('#ROM_LEFT_AFTER_NECK').append(
            calc(before.front_Neck_left, after.front_Neck_left)
        );
        $('#ROM_LEFT_AFTER_ShOULDER').append(
            calc(before.front_Shoulder_left, after.front_Shoulder_left)
        );
        $('#ROM_LEFT_AFTER_TRUNK').append(
            calc(before.front_Waist_left, after.front_Waist_left)
        );
        $('#ROM_LEFT_AFTER_LEG').append(
            calc(before.front_Hip_left, after.front_Hip_left)
        );

    }else{
        $('#ROM_RIGHT_AFTER_NECK').append(
            calc(before.side_Neck_front, after.side_Neck_front)
        );
        $('#ROM_RIGHT_AFTER_ShOULDER').append(
            calc(before.side_ShoulderR_front, after.side_ShoulderR_front)
        );
        $('#ROM_RIGHT_AFTER_ShOULDER2').append(
            calc(before.side_ShoulderL_front, after.side_ShoulderL_front)
        );
        $('#ROM_RIGHT_AFTER_TRUNK').append(
            calc(before.side_Waist_front, after.side_Waist_front)
        );
        $('#ROM_RIGHT_AFTER_LEG').append(
            calc(before.side_HipR_front, after.side_HipR_front)
        );
        $('#ROM_RIGHT_AFTER_LEG2').append(
            calc(before.side_HipL_front, after.side_HipL_front)
        );

        $('#ROM_LEFT_AFTER_NECK').append(
            calc(before.side_Neck_back, after.side_Neck_back)
        );
        $('#ROM_LEFT_AFTER_ShOULDER').append(
            calc(before.side_ShoulderR_back, after.side_ShoulderR_back)
        );
        $('#ROM_LEFT_AFTER_ShOULDER2').append(
            calc(before.side_ShoulderL_back, after.side_ShoulderL_back)
        );
        $('#ROM_LEFT_AFTER_TRUNK').append(
            calc(before.side_Waist_back, after.side_Waist_back)
        );
        $('#ROM_LEFT_AFTER_LEG').append(
            calc(before.side_HipR_back, after.side_HipR_back)
        );
        $('#ROM_LEFT_AFTER_LEG2').append(
            calc(before.side_HipL_back, after.side_HipL_back)
        );

    }


    function calc(num1, num2){
        var result = Number(Number(num1).toFixed(1)) - Number(Number(num2).toFixed(1));
        if(result == 0){
            return '';
        }else if(result < 0){
            return '<span class="calc-rom"><small>(<i class="fas fa-caret-up" style="color:red"></i>' + Math.abs(result.toFixed(1)) + '˚)</small></span>';
        }else if(result > 0){
            return '<span class="calc-rom"><small>(<i class="fas fa-caret-down" style="color:red"></i>' + Math.abs(result.toFixed(1)) + '˚)</small></span>';
        }
    }
}




// 화살표 방향 찾기 함수
function arrowPo_col(data,derection){
    var temp = Number(data).toFixed(1);
    var span = '<span>' + Math.abs(temp).toFixed(1) + '˚</span>';

    switch(derection){
        case "left" :
            if(temp == 0){
                return '<i class=""></i>' + span;
            }else if(temp > 0){
                return '<i class="fas fa-arrow-up"></i>' + span;
            }else if(temp < 0){
                return '<i class="fas fa-arrow-down"></i>' + span;
            }
            break;

        case "right" :
            if(temp == 0){
                return span + '<i class=""></i>';
            }else if(temp > 0){
                return span + '<i class="fas fa-arrow-up"></i>';
            }else if(temp < 0){
                return span + '<i class="fas fa-arrow-down"></i>';
            }
            break;
    }

}
function arrowPo_row(data){
    var temp = Number(data).toFixed(1);
    var span = '<span>' + Math.abs(temp).toFixed(1) + '˚</span>';

    if(Math.abs(temp) < 90){
        if(temp == 0){
            return span;
        }else if(temp > 0){
            return '<i class="fas fa-arrow-left"></i>' + span;
        }else if(temp < 0){
            return span + '<i class="fas fa-arrow-right"></i>';
        }
    }else{
        let temp2 = (Math.abs(Number(temp)) - 180).toFixed(1);
        let span2 = '<span>' + Math.abs(temp2).toFixed(1) + '˚</span>';

        if(temp2 == 0){
            return span2;
        }else if(temp2 > 0){
            return span2 + '<i class="fas fa-arrow-right"></i>';
        }else if(temp2 < 0){
            return '<i class="fas fa-arrow-left"></i>' + span2;
        }
    }
}

// 다리 방향 화살표
function arrowPo_row_R_LEG(data){
    var temp = Number(data).toFixed(1);
    var span = '<span>' + Math.abs(temp).toFixed(1) + '˚</span>';

    if(Math.abs(temp) < 90){
        if(temp == 0){
            return span;
        }else if(temp > 0){
            return '<i class="fas fa-arrow-left"></i>' + span;
        }else if(temp < 0){
            return span2 + '<i class="fas fa-arrow-right"></i>';
        }
    }else{
        let temp2 = (Math.abs(Number(temp)) - 180).toFixed(1);
        let span2 = '<span>' + Math.abs(temp2).toFixed(1) + '˚</span>';

        if(temp2 == 0){
            return span2;
        }else if(temp2 > 0){
            return '<i class="fas fa-arrow-left"></i>' + span2;
        }else if(temp2 < 0){
            return span2 + '<i class="fas fa-arrow-right"></i>';
        }
    }
}
function arrowPo_row_L_LEG(data){
    var temp = Number(data).toFixed(1);
    var span = '<span>' + Math.abs(temp).toFixed(1) + '˚</span>';

    if(Math.abs(temp) < 90){
        if(temp == 0){
            return span;
        }else if(temp > 0){
            return span + '<i class="fas fa-arrow-right"></i>';
        }else if(temp < 0){
            return '<i class="fas fa-arrow-left"></i>' + span;
        }
    }else{
        let temp2 = (Math.abs(Number(temp)) - 180).toFixed(1);
        let span2 = '<span>' + Math.abs(temp2).toFixed(1) + '˚</span>';

        if(temp2 == 0){
            return span2;
        }else if(temp2 > 0){
            return span2 + '<i class="fas fa-arrow-right"></i>';
        }else if(temp2 < 0){
            return '<i class="fas fa-arrow-left"></i>' + span2;
        }
    }
}

// 보유중인 이용권 찾기
function findUseItem(){
    // var useItem = useitemList.filter((e)=>e.userSeq == USER_SEQ);
    // var userObj = memberList.filter((e)=>e.sequence == USER_SEQ);
    // const useItemList = $('div.card2 > div.content');
    // useItemList.empty();

    // if(useItem.length == 0){       // 이용권 없음.
    //     useItemList.html('<p>보유중인 이용권이 없습니다.</p>');

    // }else{

    //     for(var i in useItem){
    //         var temp = itemList.filter((e) => e.seq == useItem[i].itemSeq);
    //         makeUseItem_DOM(temp[0], useItem[i], userObj[0], i);
    //     }
    // }

    // function makeUseItem_DOM(itemData, useData, userData, i){
    //     itemData.date = itemData.date == 0 ? '무제한' : itemData.date + '개월 ';
    //     itemData.count = itemData.count == 0 ? '무제한' : itemData.count + '회';
    //     var TAG = '<article class="card">' +
    //     '<h5><span></span><span></span>이용권<p>사용중</p></h5>' +
    //     '<div>\
    //         <p>' + SET.itemSet.itemCategory[SET.itemSet.itemCategory.findIndex((e) => e[2] == itemData.category)][0] +
    //         ' (' + itemData.date + itemData.count + ')</p>\
    //         <p>담당강사 <span>' + trainerList.filter((e)=>e.sequence == userData.teacherSeq)[0].name + '</span></p>\
    //         <p>' + userData.ticketingTime.split(' ')[0] + ' ~ ' + userData.ticketEndDate + '<br>\
    //         이용일수 ' + useData.useDate + '일 · 이용횟수 ' + useData.useCount + '회 · 예약건수 ' + useData.ticketingCount + '회</p>' +
    //     '</div><i class="fas fa-wrench"> 옵션</i><div class="optionBg"><div id="dateTime"><i class="far fa-calendar-minus"></i><span>기간 횟수<br>조정</span></div><div id="useList"><i class="far fa-file-alt"></i><span>사용내역<br>보기</span></div><div id="changeTeacher"><i class="fas fa-chalkboard-teacher"></i><span>강사 변경</span></div><div id="for_ticketing"><i class="fas fa-user-clock"></i><span>일괄 예약</span></div><div id="setClose"><i class="fas fa-times"></i><span>닫 기</span></div></div>\
    //     </article>';


    //     useItemList.append(TAG);

    // }
}


// 문서가 전부 로드 된 후..
$(function(){

    getMember();


    // 회원상세정보 이미지 업로드
    var uSet_img = $('div#wrap > section.content .u_img');
    $("#user-face").change(function(){
        if($(this).val() == ''){
            return false;
        }
        var FILE = $(this).prop('files')[0];
        if(FILE.name.indexOf('.') > -1){
            var tempFileName = FILE.name.split('.');
            if(tempFileName[tempFileName.length - 1] == ('php' || 'asp' || 'aspx' || 'jsp')){
                alertApp('X', '이미지만 업로드 해주세요.');
                $(this).val('');
                return false;    
            }
        }else{
            alertApp('X', '이미지만 업로드 해주세요.');
            $(this).val('');
            return false;
        }
        if(Number(FILE.size) > 5242880){
            alertApp('X', '5MB 이하 이미지를 업로드 해주세요.');
            $(this).val('');
            return false;
        }

        readURL(this);
        var formData = new FormData();
            formData.append('MEMBER_SQ', USER_SEQ);
            formData.append('myFileUp', FILE);
            
        $.ajax({
            url: 'flow_controller.php?task=execUserImageChange',
            data: formData,
            method: 'POST',
            processData: false,
            contentType: false,
            cache: false,
            success: function(r){
                var data = JSON.parse(r);
                MEMBERINFO_DOM(data);
            },
            error: function(e){
                location.reload();
            }
        });
    });
    function readURL(input) {
        if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('img.user_face').attr('src', e.target.result);        //cover src로 붙여지고
            $('#fileName').val(input.files[0].name);    //파일선택 form으로 파일명이 들어온다
        }
        reader.readAsDataURL(input.files[0]);
        }
    }


    // 회원정보 Tab
    var tabButton = $('.tab_btn > button');
    $('.tabPage0').show().siblings().hide();  //초기값
    tabButton.click(function(){
        var i = $(this).index();
        $(this).addClass('active').siblings().removeClass('active');
        if(i == 0){                 //회원정보
            $('.tabPage0').show().siblings().hide();
        }else if(i == 1){           //건강관리 데이터
            $('.tabPage1').show().siblings().hide();
        }
    });
    
    if(sessionStorage.memberbodyLink){
        tabButton.eq(sessionStorage.memberbodyLink).click();
        delete sessionStorage.memberbodyLink;
    }

    // 회원정보 수정 버튼 클릭
    const editUserBtn = $('.content > div.up > div.info_s > p > button');
    editUserBtn.click(function(){
        if($USER_GRADE < 3){
            if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 13) == -1){
                alertApp('X', '권한이 없습니다.');
                return false;
            }
        }
        USER_INFO_EDIT($(this));
    });

    function USER_INFO_EDIT(me){
        var state = me.attr('class').indexOf('save') > -1 ? true : false;

        if(!state){ // 수정하기

            me.addClass('save').text('저장');
            me.siblings('span').hide();
            me.siblings('.editForm').show();

        }else{      // 저장하기

            let parent = me.parent().attr('class');
            var input = me.siblings('.editForm').find('.editData');
            if(parent == 'u_name'){

                if(input.val() == ''){
                    alert('이름을 입력해주세요.');
                    input.focus();
                    return false;
                }else{
                    resultData();
                }
            }else if(parent == 'u_num'){

                if(input.val() == ''){
                    alert('연락처를 입력해주세요.');
                    input.focus();
                    return false;
                }else{
                    resultData();
                }

            }else if(parent == 'u_year'){
                if(input.val() != ''){
                    var temp = (input.val()).split('-');
                    var temp = temp[0]+temp[1]+temp[2];
                    var today = (dateFormat(new Date()).split('-'));
                    var today = today[0]+today[1]+today[2];
                    if(temp >= today){
                        alert('정확한 생년월일을 입력해주세요.');
                        input.focus();
                        return false;
                    }else{
                        resultData();
                    }
                }else{
                    resultData();
                }

            // }else if(parent == 'u_center'){
            //     MEMBERINFO.CENTER_NM = (ALL_CENTER.filter(e => e.CENTER_SQ == input.val()))[0].CENTER_NM;
            //     resultData();
            }else{
                resultData();
            }

            function resultData(){
                MEMBERINFO.USER_NM = $('#edit_name').val();
                MEMBERINFO.GENDER = $('#edit_gender').val();
                MEMBERINFO.PHONE_NO = $('#edit_num').val();
                MEMBERINFO.BIRTH_DT = $('#edit_year').val();
                MEMBERINFO.EMAIL = $('#edit_email').val();
                MEMBERINFO.CENTER_SQ = $('#edit_center').val();

                me.removeClass('save').text('수정');
                me.siblings('span').show();
                me.siblings('.editForm').hide();
                EDIT_USER_DATA_SAVE();
            }
        }

    }

    function EDIT_USER_DATA_SAVE(){
        let formData = new FormData();
        formData.append('MEMBER_SQ',MEMBERINFO.USER_SQ);
        formData.append('USER_NM',MEMBERINFO.USER_NM);
        formData.append('GENDER',MEMBERINFO.GENDER);
        formData.append('PHONE_NO',MEMBERINFO.PHONE_NO);
        formData.append('BIRTH_DT',MEMBERINFO.BIRTH_DT);
        formData.append('EMAIL',MEMBERINFO.EMAIL);
        formData.append('CENTER_SQ',MEMBERINFO.CENTER_SQ);

        $.ajax({
            url: "flow_controller.php?task=EditUserInfo", //&amp_=" + Date.now(),
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000,
            success: function (result) {
                if(result == 0) {
                    alertApp('X', '저장에 실패하였습니다.');
                    return;
                }
                alertApp('O', '저장되었습니다.');
                return;
            },
            error: function (e) {
                console.log('ERROR : ', e);
                alert('저장에 실패하였습니다.');
            }
        });
        MEMBERINFO_DOM(MEMBERINFO);
    }

    $('#edit_num').keyup(function(){
        var val = $(this).val().toLowerCase();
        var n = val.search(/[a-z,-]/);
        var phone = /^\d{3}-\d{3,4}-\d{4}$/;

        n > -1 ? $(this).val(val.slice(0,n)) : '' ;

        $(this).val( val.replace(/[^0-9]/g, "").replace(/(^02|^0505|^1[0-9]{3}|^0[0-9]{2})([0-9]+)?([0-9]{4})$/,"$1-$2-$3").replace("--", "-") );
        val.length == 13 ? $('#u_email').focus() : '' ;
    });

    $('#member_manager').change(function(){
        var val = $(this).val();
        useAjax('execTrainerChange', (data)=> {
            var data = JSON.parse(data);
            if(data.result == 'Fail') {
                alertApp('!', '변경사항이 없습니다.');
                $('#member_manager').val(MEMBERINFO.TRAINER);
                return false;
            }
            console.log(data);
        }, {MEMBER_SQ: USER_SEQ, TRAINER_SQ: val});
    });

    // 메모수정
    $('#memoSet').click(function(){
        if($USER_GRADE < 3){
            if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 14) == -1){
                alertApp('X', '권한이 없습니다.');
                return false;
            }
        }
        var COMMENT = $('textarea#memo').val();
        if($(this).text() == '메모수정'){
            $(this).text('메모저장');
            $('textarea#memo').attr('disabled', false).addClass('on');
        }else{
            $(this).text('메모수정');
            $('textarea#memo').attr('disabled', true).removeClass('on');
            SaveUserMemo(COMMENT);
        }

    });

    function SaveUserMemo(memo){

        // get Data
        let formdata = new FormData();
        formdata.append("COMMENT", memo);
        formdata.append("USER_SQ", MEMBERINFO.USER_SQ);

        $.ajax({
            url: "flow_controller.php?task=UpdateComment", //&amp_=" + Date.now(),
            method: "POST",
            data: formdata,
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000,
            success: function (result) {
                console.log(result);
            },
            error: function (e) {
                console.log('ERROR : ', e);
                alert('메모저장을 실패하였습니다.');
            }
        });
    }



    // 멤버스 페이지로 이동
    $('.top_sMenu > i.back').click(function(){
        location.href='members.php';
    });

    // 이용권 구매
    $('#buy_ticket_btn').click(function(){
        location.href='item.php';
        sessionStorage.buyMemberSeq = MEMBERINFO.USER_SQ;
        sessionStorage.buyMemberName = MEMBERINFO.USER_NM;
    });

    // 비밀번호 변경
    $('#pw_change_btn').click(function(){
        $('form#pw_change_frm').fadeIn(150);
    });

    // 회원 삭제
    $('#member_del').click(function(){
        var ask = confirm('회원의 정보를 삭제하시겠습니까?');
        if(ask){
            var formData = new FormData();
            formData.append('USER_SQ', USER_SEQ);
        
            $.ajax({
                url: 'flow_controller.php?task=execManagerDelete',
                data: formData,
                method: 'POST',
                cache : false,
                contentType: false,
                processData: false,
                success: function(r){
                    let data = JSON.parse(r);
                    if(data.result == 'Fail'){
                        if(data.reason == 'User Already Disabled!'){
                            alertApp('X','이미 삭제된 회원입니다.');
                            return false;
                        }
                        alertApp('X','다시 시도해주세요.');
                        return false;
                    };
                    alertApp('O','삭제되었습니다.');
                    location.href = 'members.php';
                    return false;
                    
                },
                error: function(e){
                    alertApp('X','다시 시도해주세요.');
                    return false;
                }
            });

        }else{
            return false;
        }
    });


    // 구매이용권 리스트 설정
    const itemListSetPopupEl = $('#itemListSetPopup');
    const itemListSetPopupClose = itemListSetPopupEl.find('.closePopup');
    const itemListSetPopupCloseBtn = itemListSetPopupEl.find('.closePop');
    var itemListSetBtn = $('div.card2 > div.content > article > i');
    var itemListSetCon = $('div.card2 > div.content > article > div.optionBg');
    var itemListSetOpenPopBtn = $('div.card2 > div.content > article > div.optionBg > div');
    var setClose = itemListSetCon.find('#setClose');
    var itemChange_date = $('#itemListSetPopup #dateTimeSetFrm > .dateTimeSet_POP > div .beforeInfo > span.date').text().split('~');
    var itemChange_AllCount = parseInt($('#itemListSetPopup #dateTimeSetFrm > .dateTimeSet_POP > div .beforeInfo > span.allCount').text());
    var itemChange_Count = parseInt($('#itemListSetPopup #dateTimeSetFrm > .dateTimeSet_POP > div .beforeInfo > span.count').text());


    // Default
    $('#itemAfterDate1').val(itemChange_date[0]);
    $('#itemAfterDate2').val(itemChange_date[1]);
    $('#itemAfterAllCount').val(itemChange_AllCount);
    $('#itemAfterCount').val(itemChange_Count).css({
        border: 'none',
        color: '#000',
        background: '#fff'
    }).attr(
        'disabled', true
    );



    // 팝업 닫기
    itemListSetPopupClose.add(itemListSetPopupCloseBtn).click(() => { $('.gray_div').add(itemListSetPopupEl).fadeOut(200) });
    // 팝업띄우기
    itemListSetOpenPopBtn.not(setClose).click( itemListSetPopup );


    // 이용권 기간 횟수 조정 수정 버튼 클릭
    $('#modifyVoucherDateCountBtn').click(function(){
        var UV_SQ = SELECTED_UV_SQ;
        var USE_STARTDATE = $('#itemAfterDate1').val();
        var USE_LASTDATE = $('#itemAfterDate2').val();
        var COUNT = $('#itemAfterAllCount').val();
        var MEMBER_SQ = USER_SEQ;

        var formData = new FormData();
            formData.append('UV_SQ', UV_SQ);
            formData.append('USE_STARTDATE', USE_STARTDATE);
            formData.append('USE_LASTDATE', USE_LASTDATE);
            formData.append('COUNT', COUNT);
            formData.append('MEMBER_SQ', MEMBER_SQ);

        $.ajax({
            url: 'flow_controller.php?task=execUV_PeriodChange',
            method: 'POST',
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            success: function(r){
                let data = JSON.parse(r);
                if(data.result == 'Fail'){
                    alertApp('X','다시 시도해주세요.');
                    return false;
                }
                USER_VOUCHER_LIST = data;
                MAKE_USER_VOUCHER_LIST(USER_VOUCHER_LIST);
                $('#itemListSetPopup').add($('.gray_div')).fadeOut(200);
                alertApp('O','변경되었습니다.');
                GET_USER_HISTORY(MEMBERINFO.USER_SQ);

            },
            error: function(e){
                alertApp('X','다시 시도해주세요.');
                return false;
            }
        })
    });

    // 강사 변경 버튼 클릭
    $('#trainerChangeBtn').click(function(){
        var TRAINER_SQ = $('#afterTrainer').val();
        var MEMBER_SQ = USER_SEQ;

        if(TRAINER_SQ == ''){
            alertApp('!', '강사를 선택해주세요.');
            return false;
        }

        var formData = new FormData();
            formData.append('MEMBER_SQ', MEMBER_SQ);
            formData.append('UV_SQ', SELECTED_UV_SQ);
            formData.append('TRAINER_SQ', TRAINER_SQ);

        $.ajax({
            url: "flow_controller.php?task=execUV_TrainerChange",  // + USER_SEQ,
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (r) {
                let data = JSON.parse(r);
                if(data.result == 'Fail'){
                    if(data.reason == 'User voucher Not Exist!'){
                        alertApp('X','같은 강사로 변경할 수 없습니다.');
                        return false;
                    }else{
                        alertApp('X','다시 시도해주세요.');
                        return false;
                    }
                }
                
                USER_VOUCHER_LIST = data;
                MAKE_USER_VOUCHER_LIST(USER_VOUCHER_LIST);
                $('#itemListSetPopup').add($('.gray_div')).fadeOut(200);
                alertApp('O','변경되었습니다.');
                GET_USER_HISTORY(MEMBERINFO.USER_SQ);

            },
            error: function (e) {
                console.log(e);
            }
        });

    });



    // 이용권 정지
    $('#whereStopDate1').change(function(){
        var val = $(this).val();
        if(val == ''){
            var newDate = new Date();
            alertApp('!', '반드시 선택해야 하는 항목입니다.');
            $(this).val(dateFormat(newDate)).focus();
            return false;
        }
        var nowDT = new Date(dateFormat(new Date()));
        var startDT = new Date(val);
        if(nowDT - startDT > 0){
            alertApp('!', '이전 날짜를 선택할 수 없습니다.');
            $(this).val(dateFormat(nowDT));
            return false;
        }
        $('#whereStopDate2').focus();
        $('#stopTicketSetFrm').find('.stopResult .start').text(val);

        if($('#whereStopDate2').val() != ''){
            let tempStartDate = new Date($(this).val());
            let tempEndDate = new Date($('#whereStopDate2').val());
            if(tempEndDate - tempStartDate < 0){
                $('#whereStopDate2').val($(this).val());
            }
            $('#whereStopDate2').change();
        }

    });
    $('#whereStopDate2').change(function(){
        var val = $(this).val();
        if(val == ''){
            $('#stopTicketSetFrm').find('.stopResult .end').text('-');
            $('#stopTicketSetFrm').find('.stopResult .range').text('-');
            $('#stopTicketSetFrm').find('.stopResult .reStart').text('-');
            return false;
        }
        var tempStartDate = new Date($('#whereStopDate1').val());
        var tempEndDate = new Date(val);
        if(tempEndDate - tempStartDate < 0){
            alertApp('!', '이전 날짜를 선택할 수 없습니다.');
            $(this).val($('#whereStopDate1').val()).change();
            return false;
        }
        $('#whereStopDateEZ').val('');
        $('#stopTicketSetFrm').find('.stopResult .end').text(val);
        $('#stopTicketSetFrm').find('.stopResult .range').text(
            ((tempEndDate - tempStartDate) / 1000 / 60 / 60 / 24) + 1 + '일'
        );
        $('#stopTicketSetFrm').find('.stopResult .reStart').text(
            dateFormat(tempEndDate.setDate(tempEndDate.getDate() + 1))
        );
        
    });

    $('#ticketStopBtn').click(function(){
        var startDT = $('#whereStopDate1').val();
        var endDT = $('#whereStopDate2').val();
        if(startDT == ''){
            alertApp('!', '이용권 정지 시작 날짜를 선택해주세요.');
            $('#whereStopDate1').focus();
            return false;
        }
        if(endDT == ''){
            alertApp('!', '이용권 정지 종료 날짜를 선택해주세요.');
            $('#whereStopDate2').focus();
            return false;
        }

        var UV_SQ = SELECTED_UV_SQ;
        var START_DATE = $('#whereStopDate1').val();
        var END_DATE = $('#whereStopDate2').val();

        var tempData1 = new Date(START_DATE);
        var tempData2 = new Date(END_DATE);
        var DAYS = ((tempData2 - tempData1) / 1000 / 60 / 60 / 24) + 1

        var formData = new FormData();
            formData.append('UV_SQ', UV_SQ);
            formData.append('START_DATE', START_DATE);
            formData.append('DAYS', DAYS);
            formData.append('MEMBER_SQ', USER_SEQ);

        $.ajax({
            url: 'flow_controller.php?task=execUV_PeriodPause',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            success: function(r){
                var data = JSON.parse(r);
                if(data.result == 'Fail'){
                    if(data.reason == 'Pause Period Overlapped'){
                        alertApp('X', '이용권의 정지기간이 겹칩니다.');
                        return false;
                    }else{
                        alertApp('X', '이용권이 유효하지 않습니다.');
                        return false;
                    }
                }
                
                USER_VOUCHER_LIST = data;
                MAKE_USER_VOUCHER_LIST(USER_VOUCHER_LIST);
                $('#itemListSetPopup').add($('.gray_div')).fadeOut(200);
                alertApp('O', '해당 이용권이 정지처리 되었습니다.');
                GET_USER_HISTORY(MEMBERINFO.USER_SQ);
            },
            error: function(e){
                alertApp('X', '다시 시도해주세요.');
                return false;
            }
        });
    });

    $('#ticketStartBtn').click(function(){
        // alert('준비중..');
        useAjax('execUV_PeriodPauseCancel', (data) => {
            var data = JSON.parse(data);
            if(data.result == 'Fail') {
                alertApp('X', '이용권이 재개되지 않았습니다.');
                return false;
            }
            
            USER_VOUCHER_LIST = data;
            $('#itemListSetPopup').add($('.gray_div')).fadeOut(200);
            alertApp('O', '이용권이 재개되었습니다.');
            MAKE_USER_VOUCHER_LIST(USER_VOUCHER_LIST);
            GET_USER_HISTORY(MEMBERINFO.USER_SQ);
        }, {
            UV_SQ: $('#startTicketSetFrm .voucherInfo').attr('data-uv-sq'),
            MEMBER_SQ: USER_SEQ,
            START_DATE: dateFormat(new Date()),
        })
        return false;
    });


    // ex) 잔여횟수 28회 / 예약중인횟수 1회
    $('.ticketingCountText').text(
        '잔여횟수 ' + $('.ticketingCountText').attr('data-count') +
        '회 / 예약중인횟수 ' + $('.ticketingCountText').attr('data-ticketingcount') + '회'
    );


    for(var i = 0; i < $('.ticketingCountText').attr('data-count')-$('.ticketingCountText').attr('data-ticketingcount'); i++){
        $('#ticketingCount').append('<option value="' + (i+1) + '">' + (i+1) + '</option>')
    }

    for(var i = 0; i < 24; i++){
        var temp = String(i).length == 1 ? '0'+String(i) : String(i);
        $('#classStartTime1').append('<option value="' + temp + '">' + temp + '</option>')
    }
    for(var i = 0; i < 60; i++){
        var temp = String(i).length == 1 ? '0'+String(i) : String(i);
        $('#classStartTime2').append('<option value="' + temp + '">' + temp + '</option>')
    }



/////////////////////////////////////////////////////////////////////////////////////////////////////////


    var pop_content = $('div.pop > .content > article');
    var pop_nameH = $('div.pop > h2');
    var pop_name = [
        '신체정보자세히','신체정보변화도',
        '체형정보자세히','체형정보변화도',
        'ROM정보자세히','ROM정보변화도',
        'FMS정보자세히','FMS정보변화도'
    ];
    // 팝업 열기

    $('[title="신체정보자세히"]').click(function(){
        $('div.pop').removeClass('chart');
        Open_pop(0);
        pop_nameH.text(pop_name[0]);
    });
    $('[title="신체정보변화도"]').click(function(){
        $('div.pop').addClass('chart');
        Open_pop(1);
        pop_nameH.text(pop_name[1]);
    });
    $('[title="체형정보자세히"]').click(function(){
        $('div.pop').removeClass('chart');
        Open_pop(2);
        pop_nameH.text(pop_name[2]);
        $('div.pop article.pop3 > div.info_btn > button').eq(0).click();
        $('.pose_change > div.option > button').eq(0).click();
    });
    $('[title="체형정보변화도"]').click(function(){
        $('div.pop').addClass('chart');
        Open_pop(3);
        pop_nameH.text(pop_name[3]);
    });
    $('[title="ROM정보자세히"]').click(function(){
        $('div.pop').removeClass('chart');
        Open_pop(4);
        pop_nameH.text(pop_name[4]);
        $('div.pop article.pop5 > div.info_btn > button').eq(0).click();
        $('div.pop article.pop5 > div.info > section.rom_info > p > button').eq(0).click();
    });
    $('[title="ROM정보변화도"]').click(function(){
        $('div.pop').addClass('chart');
        Open_pop(5);
        pop_nameH.text(pop_name[5]);
    });
    $('[title="FMS정보자세히"]').click(function(){
        $('div.pop').removeClass('chart');
        Open_pop(6);
        pop_nameH.text(pop_name[6]);
    });
    $('[title="FMS정보변화도"]').click(function(){
        $('div.pop').addClass('chart');
        Open_pop(7);
        pop_nameH.text(pop_name[7]);
    });

    // 체형정보 테이블 // 도트표시

    // 팝업 닫기
    $('.Close_pop').click(function(){
        if($('.content > div.up > div.u_img img').attr('class') == 'user_face active'){
            location.reload();
            console.log($('.content > div.up > div.u_img').attr('class'))
        }else{
            Close_pop();
        }
    });

    // 팝업 열고 닫기 (함수 정의)
    function Open_pop(e){
        $('div.pop').add('.gray_div').fadeIn(100);
        pop_content.hide();
        pop_content.eq(e).show();
    }
    function Close_pop(){
        $('div.pop').add('.gray_div').fadeOut(100);
    }


    // 신체정보변화도

    // 버튼 Active
    var pop2_Btn1 = $('div.pop > .content article.pop2 > .btn').find('button');        // 전체/12개월/6개월/3개월
    var pop2_Btn2 = $('div.pop > .content article.pop2 > .part_btn').find('button');   // 전체/몸무게/근육량/체지방량/체지방률/BMI/기초대사량
    pop2_Btn1.click(function(){
        $(this).addClass('active').siblings().removeClass('active');
       var type = $('div.pop > .content article.pop2 > .part_btn button.active').attr('data-what');
       var date = $(this).attr('data-date');
       myChart_Data_BODY(CHART_TYPE_BODY, type, date, $('#myChart1')[0], "REG_DT");
    });
    pop2_Btn2.click(function(){
        $(this).addClass('active').siblings().removeClass('active');
        var type = $(this).attr('data-what');
        var date = $('div.pop > .content article.pop2 > .btn button.active').attr('data-date');
        myChart_Data_BODY(CHART_TYPE_BODY, type, date, $('#myChart1')[0], "REG_DT");

    });
    // pop2_Btn1.eq(0).add(pop2_Btn2.eq(0)).click();

    // 체형정보 팝업
    var pop3Btn = $('article.pop3 > div.info_btn > button');
    var pop3Content = $('article.pop3 > div.info > section');
    pop3Content.eq(0).css('display','flex').siblings().hide();
    pop3Btn.click(function(){
        var i = $(this).index();
        if(MEASUREMENT_POSE_DATA.length < 2){
            if(i == 1){
                alert('이전 측정결과가 없습니다.');
                return false;
            }
        }else{
            i == 0 ? $('.errorBodyView').show() : $('.errorBodyView').hide();
            $(this).addClass('active').siblings().removeClass('active');
            pop3Content.eq(i).css('display','flex').siblings().hide();
        }
    });

    // 체형검사 비교결과
    $('.pose_change > div.option > button').click(function(){

        $(this).addClass('active').siblings('button').removeClass('active');
        if($(this).text() == '정면'){
            $('table th.part1').add($('table th.part3')).text('Right');
            $('table th.part2').add($('table th.part4')).text('Left');
            $('table th.FB_change').text('Front');
            $('td.col-hop').attr('colspan','0').next('td').show();

            PRINT_POSE_FRONT_BEFORE(MEASUREMENT_POSE_DATA, $('#before-date').val());
            PRINT_POSE_FRONT_AFTER(MEASUREMENT_POSE_DATA, $('#after-date').val());

        }else if($(this).text() == '측면'){
            $('table th.part1').add($('table th.part3')).text('Front');
            $('table th.part2').add($('table th.part4')).text('Back');
            $('table th.FB_change').text('Side');
            $('td.col-hop').attr('colspan','2').next('td').hide();


            PRINT_POSE_SIDE_BEFORE(MEASUREMENT_POSE_DATA, $('#before-date').val());
            PRINT_POSE_SIDE_AFTER(MEASUREMENT_POSE_DATA, $('#after-date').val());

        }

    });

    // 비교결과 측정일 선택
    $('#before-date').change(function(){
        BEFORE_AFTER_CALC();
    });
    $('#after-date').change(function(){
        BEFORE_AFTER_CALC();
    });
    $('#before-date-rom').change(function(){
        BEFORE_AFTER_CALC_ROM();
    });
    $('#after-date-rom').change(function(){
        BEFORE_AFTER_CALC_ROM();
    });


    $('#f_b-choice').change(function(){
        var date = $('div.pop > div.content > article.pop4 button.date.active').attr('data-date');
        var type = $('div.pop > div.content > article.pop4 button.position.active').attr('data-what');
        var dir = $(this).val();
        myChart_Data_POSE(MEASUREMENT_POSE_DATA, type, date, $('#myChart2')[0], "REG_DT", dir);
    });



    var pop4_Btn1 = $('div.pop > .content article.pop4 > .btn').find('button');        // 전체/12개월/6개월/3개월
    var pop4_Btn2 = $('div.pop > .content article.pop4 > .part_btn').find('button');   // 전체/몸무게/근육량/체지방량/체지방률/BMI/기초대사량
    var pop5_FS_Btn = $('div.pop article.pop5 > div.info > section.rom_info > p > button');
    pop4_Btn1.eq(0).add(pop4_Btn2.eq(0)).addClass('active');
    pop4_Btn1.add(pop4_Btn2).add(pop5_FS_Btn).not($('#rom_date_del')).click(function(){
        $(this).addClass('active').siblings().removeClass('active');
    });
    pop4_Btn1.click(function(){
        var date = $(this).attr('data-date');
        var type = $('div.pop > div.content > article.pop4 button.position.active').attr('data-what');
        var dir = $('#f_b-choice').val();
        myChart_Data_POSE(MEASUREMENT_POSE_DATA, type, date, $('#myChart2')[0], "REG_DT", dir);
    });
    pop4_Btn2.click(function(){
        var date = $('div.pop > div.content > article.pop4 button.date.active').attr('data-date');
        var type = $(this).attr('data-what');
        var dir = $('#f_b-choice').val();
        myChart_Data_POSE(MEASUREMENT_POSE_DATA, type, date, $('#myChart2')[0], "REG_DT", dir);
    });


    // ROM검사 상세결과
    var pop5Btn = $('article.pop5 > div.info_btn > button');
    var pop5Content = $('article.pop5 > div.info > section');
    pop5Content.eq(0).css('display','flex').siblings().hide();

    pop5Btn.click(function(){
        i = $(this).index();
        if(MEASUREMENT_ROM_DATA.length < 2){
            if(i == 1){
                alert('이전 측정결과가 없습니다.');
                return false;
            }
        }else{
            $(this).addClass('active').siblings().removeClass('active');
            pop5Content.eq(i).css('display','flex').siblings().hide();
        }
    });


    //
    var tr = $('section.rom_info > div > table tbody > tr');
    var trProgress = $('section.rom_info > div > table.balance tbody > tr');
    var painColor = { bad : '#f15354', normal : '#ffcb27', good : '#21a755' };     //pain색상

    function front(){
        tr.eq(0).show();
        tr.eq(1).show();
        tr.eq(2).show();
        tr.eq(3).show();
        tr.eq(4).hide();
        tr.eq(5).hide();
        tr.eq(6).hide();
        tr.eq(7).hide();
        tr.eq(8).hide();
        tr.eq(9).hide();
        trProgress.eq(4).hide();
        trProgress.eq(5).hide();
        // $('.Shoulder-side-table-th').css('border-bottom','');
        // $('.Shoulder-side-table-th').css('line-height','unset');
        $('.Shoulder-side-table-th').attr('rowspan','1');
        $('.Shoulder-side-display-none').show();
        $('.Shoulder-side-table-th > div').hide();
    }

    function side(){
        tr.eq(0).hide();
        tr.eq(1).hide();
        tr.eq(2).hide();
        tr.eq(3).hide();
        tr.eq(4).show();
        tr.eq(5).show();
        tr.eq(6).show();
        tr.eq(7).show();
        tr.eq(8).show();
        tr.eq(9).show();
        trProgress.eq(4).show();
        trProgress.eq(5).show();
        // $('.Shoulder-side-table-th').css('border-bottom','none');
        // $('.Shoulder-side-table-th').css('line-height','150px');
        $('.Shoulder-side-table-th').attr('rowspan','2');
        $('.Shoulder-side-display-none').hide();
        $('.Shoulder-side-table-th > div').show();
    }

    pop5_FS_Btn.not($('#rom_date_del')).click(function(){
        if($(this).index() == 0){
            $('.pop5 .front').text('Right');
            $('.pop5 .back').text('Left');
            front();
        }else if($(this).index() == 1){
            $('.pop5 .front').text('Front');
            $('.pop5 .back').text('Back');
            side();
        }
        PRINT_ROM_DETAIL_DATA(MEASUREMENT_ROM_DATA, $('#rom_date').val());
    });


    $('.rom_change > div.option > button').click(function(){

        $(this).addClass('active').siblings('button').removeClass('active');
        if($(this).text() == '정면'){
            $('table th.part1').add($('table th.part3')).text('Right');
            $('table th.part2').add($('table th.part4')).text('Left');
            $('table th.FB_change').text('Front');
            $('td.col-hop').attr('colspan','0').next('td').show();
            $('.titleRowspan').attr('rowspan',4);
            $('.titleRowspan').eq(0).html('Right<br>(Flexion)');
            $('.titleRowspan').eq(1).html('Left<br>(Flexion)');
            $('.rom-shoulder-name-change').text('Shoulder');
            $('.rom-leg-name-change').text('Leg');
            $('.sideTR-ROM').hide();

            PRINT_ROM_FRONT_BEFORE(MEASUREMENT_ROM_DATA, $('#before-date-rom').val());
            PRINT_ROM_FRONT_AFTER(MEASUREMENT_ROM_DATA, $('#after-date-rom').val());

        }else if($(this).text() == '측면'){
            $('table th.part1').add($('table th.part3')).text('Front');
            $('table th.part2').add($('table th.part4')).text('Back');
            $('table th.FB_change').text('Side');
            $('td.col-hop').attr('colspan','2').next('td').hide();
            $('.titleRowspan').attr('rowspan',6);
            $('.titleRowspan').eq(0).html('Front<br>(Flexion)');
            $('.titleRowspan').eq(1).html('Back<br>(Extension)');
            $('.rom-shoulder-name-change').text('R - 어깨');
            $('.rom-leg-name-change').text('R - 다리');
            $('.sideTR-ROM').show();

            PRINT_ROM_SIDE_BEFORE(MEASUREMENT_ROM_DATA, $('#before-date-rom').val());
            PRINT_ROM_SIDE_AFTER(MEASUREMENT_ROM_DATA, $('#after-date-rom').val());

        }

    });
    $('.rom_change > div.option > button').eq(0).click();

    // ROM정보 변화도
    var pop6_Btn1 = $('div.pop > .content article.pop6 > .btn').find('button');        // 전체/12개월/6개월/3개월
    var pop6_Btn2 = $('div.pop > .content article.pop6 > .part_btn').find('button');   // 전체/몸무게/근육량/체지방량/체지방률/BMI/기초대사량

    $('#f_b-choice2').change(function(){
        var date = $('div.pop > div.content > article.pop6 button.date.active').attr('data-date');
        var type = $('div.pop > div.content > article.pop6 button.position.active').attr('data-what');
        var dir = $(this).val();
        myChart_Data_ROM(MEASUREMENT_ROM_DATA, type, date, $('#myChart3')[0], "REG_DT", dir);
    });

    pop6_Btn1.eq(0).add(pop6_Btn2.eq(0)).addClass('active');
    pop6_Btn1.click(function(){
        $(this).addClass('active').siblings().removeClass('active');
        var date = $(this).attr('data-date');
        var type = $('div.pop > div.content > article.pop6 button.position.active').attr('data-what');
        var dir = $('#f_b-choice2').val();
        myChart_Data_ROM(MEASUREMENT_ROM_DATA, type, date, $('#myChart3')[0], "REG_DT", dir);
    });
    pop6_Btn2.click(function(){
        $(this).addClass('active').siblings().removeClass('active');
        var date = $('div.pop > div.content > article.pop6 button.date.active').attr('data-date');
        var type = $(this).attr('data-what');
        var dir = $('#f_b-choice2').val();
        myChart_Data_ROM(MEASUREMENT_ROM_DATA, type, date, $('#myChart3')[0], "REG_DT", dir);
    });



    // 체형검사 데이터 삭제, ROM결과 데이터 삭제
    $('#pose_date_del').add($('#rom_date_del')).click(function(){
        var selectId = $(this).attr('id').split('_del')[0];
        var seq = document.getElementById(selectId).value;
        var type = $(this).attr('data-type');
        var filterPoseDate = '';
        type == 'pose'
        ? filterPoseDate = (MEASUREMENT_POSE_DATE.filter(e => e.MEASUREMENT_SQ == seq)[0]).REG_DT
        : filterPoseDate = (MEASUREMENT_ROM_DATE.filter(e => e.MEASUREMENT_SQ == seq)[0]).REG_DT;
        var ask = confirm(filterPoseDate + '의 측정 데이터를 정말 삭제 하시겠습니까?');
        // alert('준비중입니다.');return false;
        if(ask){
            // 삭제버튼 클릭됬을 때
            MEASUREMENT_DATA_DEL(type, seq);
        }else{
            return false;
        }
    });


    function MEASUREMENT_DATA_DEL(type, seq){

        // get Data
        let formdata = new FormData();
        formdata.append("MEASUREMENT_TYPE", type);
        formdata.append("MEASUREMENT_SQ", seq);

        $.ajax({
            url: "flow_controller.php?task=DeleteMeasurement&amp_=" + Date.now(),
            method: "POST",
            data: formdata,
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000,
            success: function (result) {
                console.log('전송완료 : ', result);
                getMember();
            },
            error: function (e) {
                console.log('ERROR : ', e);
            }
        });
    }



    // svg 모달
    var errorBodyViewBtn = $('.errorBodyView');
    var errorMusclePopup = $('#errorMuscleView_pop');
    var errorMuscleCloseBtn = $('#errorMuscleView_pop > div.btn > button');
    var errorTableTd = $('#errorMuscleView_pop > section table td.cursor');
    errorBodyViewBtn.click(() => {
        // 임시
        //alert('준비중입니다..');
        //return false;
        errorMusclePopup.fadeIn(200);
    });
    errorMuscleCloseBtn.click(() => {
        errorMusclePopup.fadeOut(200);
    });

    //errorTableTd.hover(function(){
    //    var data = $(this).attr('data-svg');
    //    resetMuscleTotal();
    //    if(data){
    //        data = data.split(' ');
    //        for(let i = 0; i < data.length; i++){
    //            for(let ii = 0; ii < muscleSVG[data[i]].length; ii++){
    //                $('#' + muscleSVG[data[i]][ii]).attr({
    //                    'stroke':'#f6a450',
    //                    'stroke-width':'14'
    //                });
    //            }
    //        }
    //    }
    //},function(){
    //    $('svg path').attr({
    //        'stroke':'none',
    //        'stroke-width':'0'
    //    });
    //});


    // 신체정보 직접입력 모달
    var bodyInfoWriteBtn = $('#bodyInfo_WriteFrm_Btn');
    var bodyInfoWriteFrm = $('#bodyInfo_Write');
    var bodyInfo_Submit = $('#bodyInfo_Submit');
    var bodyInfoWriteCloseBtn = $('#bodyInfo_Write > div.btn > button');

    bodyInfoWriteBtn.click(function(){
        bodyInfoWriteFrm.find('input').val('');
        bodyInfoWriteFrm.find('input[type="date"]').val(dateFormat(new Date()));
        $('.gray_div').add(bodyInfoWriteFrm).fadeIn(200);

        PRINT_lastData_BODY();
    });
    bodyInfoWriteCloseBtn.click(() => {
        $('.gray_div').add(bodyInfoWriteFrm).fadeOut(200);
    });
	bodyInfo_Submit.click(() => {
        SaveInbodyData();
		return false;
    });

    // 대사질환정보 직접입력 모달
    var desaInfoWriteBtn = $('#desaInfo_WriteFrm_Btn');
    var desaInfoWriteFrm = $('#desaInfo_Write');
    var desaInfo_Submit = $('#desaInfo_Submit');
    var desaInfoWriteCloseBtn = $('#desaInfo_Write > div.btn > button');

    desaInfoWriteBtn.click(() => {
        desaInfoWriteFrm.find('input').val('');
        desaInfoWriteFrm.find('input[type="date"]').val(dateFormat(new Date()));
        $('.gray_div').add(desaInfoWriteFrm).fadeIn(200);

        PRINT_lastData_DESA();
    });
    desaInfoWriteCloseBtn.click(() => {
        $('.gray_div').add(desaInfoWriteFrm).fadeOut(200);
    });
	desaInfo_Submit.click(() => {
        SaveMedicalExamData();
		return false;
    });


    // 임시
    // $('[title="ROM정보변화도"]').click();




});


// 대사질환 결과 도트 색상 1
function DESA_DATA_ONE(min, max, value){
    if(min <= value && value < max){ // 정상
        return 'g';
    }else{ // 비정상
        return 'r';
    }
}

// 대사질환 결과 도트 색상 2
function DESA_DATA_TWO(min1, max1, value1, min2, max2, value2){
    var temp1 = min1 < value1 && value1 < max1 ? true : false;
    var temp2 = min2 < value2 && value2 < max2 ? true : false;
    if(temp1 && temp2){
        return 'g';
    }else{
        return 'r';
    }
}

// 소수점 자르기 함수
function floatCut(num,len){
    var l = Math.pow(10,len);
    var result = Math.round(parseFloat(num) * l) / 100;
    return result;
}

// 날짜를 숫자로
function dateSetNumber(date){
    var dateFormat;
    if(date.indexOf(' ') > -1){
        dateFormat = date.split(' ')[0];
        return dateFormat.split('-')[0] + dateFormat.split('-')[1] + dateFormat.split('-')[2];
    }else{
        return date.split('-')[0] + date.split('-')[1] + date.split('-')[2]
    }
}

function SaveInbodyData() {
    if($('#bodyInfo_Height').val() != '' && $('#bodyInfo_Weight').val() != '' && $('#bodyInfo_Fat').val() != '' && $('#bodyInfo_Muscle').val() != ''){

        // get Data
        let formdata = new FormData();
        formdata.append("USER_SEQ", USER_SEQ);
        formdata.append("bodyInfo_Date", $("#bodyInfo_Date").val());
        formdata.append("bodyInfo_Height", $("#bodyInfo_Height").val());
        formdata.append("bodyInfo_Weight", $("#bodyInfo_Weight").val());
        formdata.append("bodyInfo_Fat", $("#bodyInfo_Fat").val());
        formdata.append("bodyInfo_Muscle", $("#bodyInfo_Muscle").val());

        $.ajax({
            url: "flow_controller.php?task=SaveInbodyData&amp_=" + Date.now(),
            method: "POST",
            data: formdata,
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000,
            success: function (result) {
                console.log('전송완료 : ', result);
                MEASUREMENT_BODY_DATE = JSON.parse(result.split('|')[0]);
                MEASUREMENT_BODY_DATA = JSON.parse(result.split('|')[1]);
                defaultDateOUTPUT();
            },
            error: function (e) {
                console.log('ERROR : ', e);
            }
        });
    }else{
        alert('신체정보를 빈칸없이 입력해주세요');
        $('#bodyInfo_WriteFrm_Btn').click();
        return false;
    }
}

function SaveMedicalExamData() {
    if($('#desaInfo_HR').val() != '' && $('#desaInfo_SBP').val() != '' && $('#desaInfo_DBP').val() != '' && $('#desaInfo_Glucose').val() != '' && $('#desaInfo_HbA1c').val() != '' && $('#desaInfo_TC').val() != '' && $('#desaInfo_HDL').val() != '' && $('#desaInfo_LDL').val() != '' && $('#desaInfo_TG').val() != '' && $('#desaInfo_Lactate').val() != '' ){
        // get Data
        let formdata = new FormData();USER_SEQ
        formdata.append("USER_SEQ", USER_SEQ);
        formdata.append("desaInfo_Date", $("#desaInfo_Date").val());
        formdata.append("desaInfo_HR", $("#desaInfo_HR").val());
        formdata.append("desaInfo_SBP", $("#desaInfo_SBP").val());
        formdata.append("desaInfo_DBP", $("#desaInfo_DBP").val());
        formdata.append("desaInfo_Glucose", $("#desaInfo_Glucose").val());
        formdata.append("desaInfo_HbA1c", $("#desaInfo_HbA1c").val());
        formdata.append("desaInfo_TC", $("#desaInfo_TC").val());
        formdata.append("desaInfo_HDL", $("#desaInfo_HDL").val());
        formdata.append("desaInfo_LDL", $("#desaInfo_LDL").val());
        formdata.append("desaInfo_TG", $("#desaInfo_TG").val());
        formdata.append("desaInfo_Lactate", $("#desaInfo_Lactate").val());

        $.ajax({
            url: "flow_controller.php?task=SaveMedicalExamData&amp_=" + Date.now(),
            method: "POST",
            data: formdata,
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000,
            success: function (result) {
                console.log('전송완료 : ', result);
                MEASUREMENT_DESA_DATE = JSON.parse(result.split('|')[0]);
                MEASUREMENT_DESA_DATA = JSON.parse(result.split('|')[1]);
                defaultDateOUTPUT();

            },
            error: function (e) {
                console.log('ERROR : ', e);
            }
        });
    }else{
        alert('대사질환정보를 빈칸없이 입력해주세요');
        $('#desaInfo_WriteFrm_Btn').click();
        return false;
    }
}
