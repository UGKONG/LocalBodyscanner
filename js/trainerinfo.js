var TRAINERINFO;
var WORK_CATEGORY_LIST;
var WORK_STATUS_LIST;
var MY_MEMBER_LIST;
var MY_MEMBER_VOUCHER_LIST;
var MY_SCHEDULE_LIST;
var MY_SCHEDULE_SET;
var MY_HOLIDAY_LIST;

function GET_TRAINER_INFO(sq){
    let formData = new FormData();
    formData.append('USER_SQ',sq);

    $.ajax({
        url: "flow_controller.php?task=getManagerInfo",
        method: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(result){
            var data = result.split('|');
            TRAINERINFO = JSON.parse(data[0])[0];
            WORK_CATEGORY_LIST = JSON.parse(data[1]);
            WORK_STATUS_LIST = JSON.parse(data[2]);
            MY_NOT_VOUCHER_MEMBER_LIST = JSON.parse(data[3]);
            MY_MEMBER_LIST = JSON.parse(data[4]);
            MY_SCHEDULE_LIST = JSON.parse(data[5]);
            MY_SCHEDULE_SET = JSON.parse(data[6])[0];
            MY_HOLIDAY_LIST = JSON.parse(data[7]);
            MY_SALARY_INFO = JSON.parse(data[8])[0];

            TRAINER_PAGE_RESET();
            MAKE_TRAINER_POSITION(WORK_CATEGORY_LIST);
            MAKE_TRAINER_STATUS(WORK_STATUS_LIST);
            MAKE_TRAINER_INFO(TRAINERINFO);

            MAKE_MY_MEMBER(MY_NOT_VOUCHER_MEMBER_LIST);
            MAKE_MY_VOUCHER_MEMBER(MY_MEMBER_LIST);
            MAKE_MY_SCHEDULE(MY_SCHEDULE_LIST);

            MAKE_MY_SCHEDULE_SET(MY_SCHEDULE_SET);
            MAKE_MY_HOLIDAY(MY_HOLIDAY_LIST);

            MAKE_MY_SALARY_INFO(MY_SALARY_INFO);
        },
        error: function(e){
            console.log(e);
            return false;
        }
    });
}

// 급여 정보
function MAKE_MY_SALARY_INFO(data){

    // RESET
    var tab = $('.tab_menu6');
        tab.find('input').val('');
        tab.find('[type="checkbox"], [type="radio"]').prop('checked', false);

    // 개인수당정보
    var SALARY = $('#pay-month');                                       // 기본급 / 월
    var INSENTIVE = $('#pay-my');                                       // 개인 매출 커미션
    var SALARY_TAX_EXCEPT = $('#pay-afterTax');                         // 부가세 제외 후 정산

    if (data == undefined) {
        $('#pay-month')
            .add($('#pay-my'))
            .add($('#solo-pay1-value'))
            .add($('#solo-pay2-value'))
            .add($('#solo-no-show-pay2-value'))
            .add($('#group-no-show-pay2-value')).val(0);
        $('#solo-pay1').prop('checked', true);
        $('#solo-no-show-pay1').prop('checked', true);
        $('#group-no-show-pay1').prop('checked', true);


        return false;
    }

    // 개인레슨
    var PERSONAL_ALLOWANCE_TYPE = data.PERSONAL_ALLOWANCE_TYPE == 1 ? $('#solo-pay1') : $('#solo-pay2');    // 개인레슨 수당 (정액제/정율제) ★★★ 3항연산자 사용
    var PERSONAL_ALLOWANCE_AMOUNT = $('#solo-pay1-value');              // 정액제 금액
    var PERSONAL_ALLOWANCE_RATIO = $('#solo-pay2-value');               // 정율제 %
    var PERSONAL_ALLOWANCE_TAX_EXCEPT = $('#solo-pay-afterTax_class');  // 부가세 제외 후 정산
    var PERSONAL_NOSHOW_TYPE = data.PERSONAL_NOSHOW_TYPE == 1 ? $('#solo-no-show-pay1') : $('#solo-no-show-pay2');  // No-Show 타입 ★★★ 3항연산자 사용
    var PERSONAL_NOSHOW_RATIO = $('#solo-no-show-pay2-value');          // No-Show %
    // 그룹레슨
    var GROUP_ALLOWANCE_TAX_EXCEPT = $('#group-pay-afterTax_class');    // 부가세 제외 후 정산
    var GROUP_NOSHOW_TYPE = data.GROUP_NOSHOW_TYPE == 1 ? $('#group-no-show-pay1') : $('#group-no-show-pay2');  // No-Show 타입 ★★★ 3항연산자 사용
    var GROUP_NOSHOW_RATIO = $('#group-no-show-pay2-value');            // No-Show %

    SALARY.val(numberFormat(data.SALARY));
    INSENTIVE.val(numberFormat(data.INSENTIVE));
    SALARY_TAX_EXCEPT.prop('checked', data.SALARY_TAX_EXCEPT == 1 ? true : false);

    PERSONAL_ALLOWANCE_TYPE.prop('checked', true);
    PERSONAL_ALLOWANCE_AMOUNT.val(numberFormat(data.PERSONAL_ALLOWANCE_AMOUNT));
    PERSONAL_ALLOWANCE_RATIO.val(numberFormat(data.PERSONAL_ALLOWANCE_RATIO));
    PERSONAL_ALLOWANCE_TAX_EXCEPT.prop('checked', data.PERSONAL_ALLOWANCE_TAX_EXCEPT == 1 ? true : false);
    PERSONAL_NOSHOW_TYPE.prop('checked', true);
    PERSONAL_NOSHOW_RATIO.val(numberFormat(data.PERSONAL_NOSHOW_RATIO));

    GROUP_ALLOWANCE_TAX_EXCEPT.prop('checked', data.GROUP_ALLOWANCE_TAX_EXCEPT == 1 ? true : false);
    GROUP_NOSHOW_TYPE.prop('checked', true);
    GROUP_NOSHOW_RATIO.val(numberFormat(data.GROUP_NOSHOW_RATIO));
}

// 담당 회원 목록 (이용권 무)
function MAKE_MY_MEMBER(list){
    var list = list.filter(e => e.ISUSE == 1);
    var tag = '';
    var counter = 0;
    $('#myMember_list').empty();

    for(let i of list){
        let solo = i.PERSONAL_VOUCHER == 0 || i.PERSONAL_VOUCHER == null ? false : true;
        let group = i.GROUP_VOUCHER == 0 || i.GROUP_VOUCHER == null ? false : true;
        tag = 
            `<li data-seq="${i.USER_SQ}" data-solo="${solo ? true : false}" data-group="${group ? true : false}">
                <div>
                    <img src="${i.USERIMAGE == null || i.USERIMAGE == '' ? 'img/user.png' : i.USERIMAGE}" alt="유저이미지">
                </div>
                <div>
                    <p style="width:100%;">
                        <span class="name">${i.USER_NM}</span>
                        <span class="text">${i.GENDER == 'M' ? '남' : '여'} / ${birth_year(i.BIRTH_DT)} / ${i.PHONE_NO}</span>
                    </p>
                    <p style="width:100%;">
                        <span>등록일 / 수정일</span>
                        <span class="text">${i.REG_DT.split(' ')[0]} / ${i.LAST_DT.split(' ')[0]}</span>
                    </p>
                    <p style="width:100%;">
                        <span>메 모</span>
                        <span class="text">${i.COMMENT == null ? '메모가 없습니다.' : i.COMMENT}</span>
                    </p>
                </div>
                <div class="detail" onclick="location.href='member_info.php?u_seq=${i.USER_SQ}'">
                    <i class="fas fa-info-circle"></i> 상세보기
                </div>

                ${
                    solo ? 
                        '<div class="voucherSoloCount count">이용권 개수: ' + 
                            i.PERSONAL_VOUCHER + 
                        '개</div>' : ''
                }
                ${
                    group ? 
                        '<div class="voucherGroupCount count">이용권 개수: ' +
                            i.GROUP_VOUCHER +
                        '개</div>' : ''
                }
            </li>`;

            $('#myMember_list').append(tag);
            counter++;
    }
    $('#myMemberCount1').text(counter + '명');

}

// 담당 회원 목록 (이용권 유)
function MAKE_MY_VOUCHER_MEMBER(list){
    var list = list.filter(e => e.ISUSE == 1);
    var counter = 0;
    $('#voucherMyMember_list').empty();

    for(let i of list){
        let solo = i.PERSONAL_VOUCHER == 0 || i.PERSONAL_VOUCHER == null ? false : true;
        let group = i.GROUP_VOUCHER == 0 || i.GROUP_VOUCHER == null ? false : true;
            tag = `<li data-seq="${i.USER_SQ}" data-solo="${solo ? true : false}" data-group="${group ? true : false}">
                <div>
                    <img src="${i.USERIMAGE == null || i.USERIMAGE == '' ? 'img/user.png' : i.USERIMAGE}" alt="유저이미지">
                </div>
                <div>
                    <p style="width:100%;">
                        <span class="name">${i.USER_NM}</span>
                        <span class="text">${i.GENDER == 'M' ? '남' : '여'} / ${birth_year(i.BIRTH_DT)} / ${i.PHONE_NO}</span>
                    </p>
                    <p style="width:100%;">
                        <span>등록일 / 수정일</span>
                        <span class="text">${i.REG_DT.split(' ')[0]} / ${i.LAST_DT.split(' ')[0]}</span>
                    </p>
                    <p style="width:100%;">
                        <span>메 모</span>
                        <span class="text">${i.COMMENT == null ? '메모가 없습니다.' : i.COMMENT}</span>
                    </p>
                </div>
                <div class="detail" onclick="location.href='member_info.php?u_seq=${i.USER_SQ}'">
                    <i class="fas fa-info-circle"></i> 상세보기
                </div>

                ${
                    solo ? 
                        '<div class="voucherSoloCount count" style="position:absolute;top:10px;right:10px;font-size:13px;">이용권 개수: ' + 
                            i.PERSONAL_VOUCHER + 
                        '개</div>' : ''
                }
                ${
                    group ? 
                        '<div class="voucherGroupCount count" style="position:absolute;top:10px;right:10px;font-size:13px;">이용권 개수: ' +
                            i.GROUP_VOUCHER +
                        '개</div>' : ''
                }
            </li>`;

            $('#voucherMyMember_list').append(tag);
            counter++;
            
    }
    
    $('#myMemberCount2').text(counter + '명');

    $('#soloGroupFilter').change();
}



// 오늘 스케줄
function MAKE_MY_SCHEDULE(list){
    $('#mySchedule_list').empty();

    var sortList = list.sort((a, b) => {
        let A = Number(a.START_TIME.replace(':',''));
        let B = Number(b.START_TIME.replace(':',''));
        return A - B;
    });

    for(let i of sortList){
        $('#mySchedule_list').append(
            `<li>
                <div>
                    <p>시작<br><span>${i.START_TIME}</span></p>
                    <p>종료<br><span>${i.END_TIME}</span></p>
                    <img src="${i.USERIMAGE == null || i.USERIMAGE == '' ? 'img/user.png' : i.USERIMAGE}" alt="유저이미지" style="background-color:#fff; border:1px solid #aaa;">
                </div>
                <div>
                    <p><span>${i.USER_NM}</span><br><span style="text-indent:2px;">${birth_year(i.BIRTH_DT)} / ${i.GENDER == 'M' ? '남자' : '여자'} / ${i.PHONE_NO}</span></p>
                    <p><span style="width: 90px;">사용 이용권</span><span>${i.VOUCHER_NAME}</span></p>
                </div>
                <div style="position:; width: 130px; display: flex; flex-flow: column;
                            justify-content: space-between; text-align: right;
                            padding: 10px; font-size: 14px; ">
                    <span></span>
                    <span style="cursor:pointer;" onclick="location.href='scheduler.php'">
                        <i class="fas fa-info-circle"></i> 상세보기
                    </span>
                </div>
            </li>`
        );
    }
    $('#myMemberCount3').text(list.length + '명');
}

// 스케줄 설정
function MAKE_MY_SCHEDULE_SET(data){
    if(data == undefined){
        $('article.tab_menu2 > div.content1 ul > li').removeClass('active');
        $('article.tab_menu2 > div.content2 > ul > li').removeClass('off').text('');
        return false;
    }
    var tempTime = data.WORK_TIME.split(':');

    // SETTING
    $('article.tab_menu2 > div.content1 ul > li').attr('class','');
    let MON = [data.MON == '1' ? 'active' : '', data.MON == '1' ? '' : 'off'];
    let TUE = [data.TUE == '1' ? 'active' : '', data.TUE == '1' ? '' : 'off'];
    let WED = [data.WED == '1' ? 'active' : '', data.WED == '1' ? '' : 'off'];
    let THU = [data.THU == '1' ? 'active' : '', data.THU == '1' ? '' : 'off'];
    let FRI = [data.FRI == '1' ? 'active' : '', data.FRI == '1' ? '' : 'off'];
    let SAT = [data.SAT == '1' ? 'active' : '', data.SAT == '1' ? '' : 'off'];
    let SUN = [data.SUN == '1' ? 'active' : '', data.SUN == '1' ? '' : 'off'];

    $('article.tab_menu2 > div.content1 ul > li').eq(0).attr('class', MON[0]);
    $('article.tab_menu2 > div.content1 ul > li').eq(1).attr('class', TUE[0]);
    $('article.tab_menu2 > div.content1 ul > li').eq(2).attr('class', WED[0]);
    $('article.tab_menu2 > div.content1 ul > li').eq(3).attr('class', THU[0]);
    $('article.tab_menu2 > div.content1 ul > li').eq(4).attr('class', FRI[0]);
    $('article.tab_menu2 > div.content1 ul > li').eq(5).attr('class', SAT[0]);
    $('article.tab_menu2 > div.content1 ul > li').eq(6).attr('class', SUN[0]);

    $('#classTime1').val(tempTime[0]);
    $('#classTime2').val(tempTime[1]);
    $('#classTime3').val(tempTime[2]);
    $('#classTime4').val(tempTime[3]);

    // VIEW
    $('article.tab_menu2 > div.content2 > ul > li').attr('class','');
    $('article.tab_menu2 > div.content2 > ul > li').text('');

    $('article.tab_menu2 > div.content2 > ul > li').eq(0).attr('class', MON[1]);
    $('article.tab_menu2 > div.content2 > ul > li').eq(1).attr('class', TUE[1]);
    $('article.tab_menu2 > div.content2 > ul > li').eq(2).attr('class', WED[1]);
    $('article.tab_menu2 > div.content2 > ul > li').eq(3).attr('class', THU[1]);
    $('article.tab_menu2 > div.content2 > ul > li').eq(4).attr('class', FRI[1]);
    $('article.tab_menu2 > div.content2 > ul > li').eq(5).attr('class', SAT[1]);
    $('article.tab_menu2 > div.content2 > ul > li').eq(6).attr('class', SUN[1]);

    $('article.tab_menu2 > div.content2 > ul > li')
        .not($('article.tab_menu2 > div.content2 > ul > li.off'))
        .text(tempTime[0] + ':' + tempTime[1] + ' ~ ' + tempTime[2] + ':' + tempTime[3]);
}

// 휴일 설정
function MAKE_MY_HOLIDAY(list){
    $('#calendar td > a.holiday').removeClass('holiday');

    for(let i in list){
        var date = list[i].HOLIDAY.split('-');
        $('#calendar td').each(function(){
            if($(this).attr('data-year') == date[0] && $(this).attr('data-month') == Number(date[1]) - 1 && $(this).attr('data-handler') == 'selectDay'){
                $('#calendar td[data-handler="selectDay"]').eq(Number(date[2])-1).find('a').addClass('holiday').attr('title',list[i].HOLIDAY_NAME);
            }
        });
    }
}


function TRAINER_PAGE_RESET(){
    $('.content > div.up > div.info_s > p > span.editForm > input').val('');
    $('.content > div.up > div.info_s > p > .name').text('-');
    $('.content > div.up > div.info_s > p > .gender').text('-');
    $('.content > div.up > div.info_s > p > .num').text('-');
    $('.content > div.up > div.info_s > p > .year').text('-');
    $('.content > div.up > div.info_s > p > .age').text('-');
    $('.content > div.up > div.info_s > p > .address').text('-');
    $('.content > div.up > div.info_s > p > .email').text('-');
    $('#myPosition').val('');
    $('#WORKSTARTDATE').text('-');
    $('#WORKENDDATE').text('-');
    $('#myTeam').text('-');
    $('#myWork').text('-');
    $('#input_myWorkEndDate').val('');
    $('#input_workStartDate').val('');
}

function MAKE_TRAINER_INFO(obj){
    var gender = obj.GENDER == 'M' ? '남성' : '여성';
    var status = obj.WORKSTATUS == 3 ? (obj.WORKENDDATE.split(' '))[0] : (WORK_STATUS_LIST.filter(e => e.CODE == obj.WORKSTATUS))[0].DESCRIPTION;
    
    $('.content > div.up > div.info_s > p > .name').text(obj.USER_NM + ' (아이디: ' + obj.USERID + ')');
    $('.content > div.up > div.info_s > p > .gender').text(gender);
    $('.content > div.up > div.info_s > p > .num').text(obj.PHONE_NO);
    $('.content > div.up > div.info_s > p > .year').text(obj.BIRTH_DT);
    $('.content > div.up > div.info_s > p > .age').html(birth_year(obj.BIRTH_DT));
    $('.content > div.up > div.info_s > p > .address').text(obj.ADDRESS);
    $('.content > div.up > div.info_s > p > .email').text(obj.EMAIL);

    $('#edit_name').val(obj.USER_NM);
    $('#edit_gender').val(obj.GENDER);
    $('#edit_num').val(obj.PHONE_NO);
    $('#edit_year').val(obj.BIRTH_DT);
    $('#edit_address').val(obj.ADDRESS);
    $('#edit_email').val(obj.EMAIL);
    if(obj.USERIMAGE == null || obj.USERIMAGE == ''){
        $('.u_img > img.user_face').attr('src', 'img/user.png');
    }else{
        $('.u_img > img.user_face').attr('src', obj.USERIMAGE);
    }
    $('#myPosition').val(obj.WORKCATEGORY);
    $('#myStatus').val(obj.WORKSTATUS);

    // 재직기간
    $('#WORKSTARTDATE').text( obj.WORKSTARTDATE == null ? '0000-00-00' : obj.WORKSTARTDATE.split(' ')[0] );
    $('#input_workStartDate').val( obj.WORKSTARTDATE == null ? '0000-00-00' : obj.WORKSTARTDATE.split(' ')[0] );
    $('#input_myWorkEndDate').val( (obj.WORKENDDATE.split(' '))[0] == '0000-00-00' ? '' : (obj.WORKENDDATE.split(' '))[0] );
    $('#WORKENDDATE').text(status);
    
    obj.WORKSTATUS == 3 ? $('#input_myWorkEndDate').show() : $('#input_myWorkEndDate').hide();

}

function MAKE_TRAINER_POSITION(list){
    var tag = '';
    $('#myPosition').html('<option value="0">소속없음</option>');
    for(let i = 0; i < list.length; i++){
        tag += '<option value=' + list[i].WC_SEQ + '>' + list[i].NAME + '</option>';
    }
    $('#myPosition').append(tag);
}

function MAKE_TRAINER_STATUS(list){
    var tag = '';
    $('#myStatus').empty();
    for(let i = 0; i < list.length; i++){
        tag += '<option value=' + list[i].CODE + '>' + list[i].DESCRIPTION + '</option>';
    }
    $('#myStatus').append(tag);
}

$(function(){
    

    GET_TRAINER_INFO(TRAINER_SEQ);

    // 트레이너 정보 (내정보)
    
    // 뒤로가기
    $('.content > .top_sMenu > i').click(()=>window.history.back());
    
    // 강사소개 버튼
    $('#info_btn').click(()=>{});

    // 비밀번호변경 버튼
    $('#pw_change_btn').click(()=>{
        if($USER_GRADE < 3){
            if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 87) == -1){
                alertApp('X', '권한이 없습니다.');
                return false;
            }
        }
        $('form#pw_change_frm').find('input').val('');
        $('form#pw_change_frm').fadeIn(200)
    });

    $('#changePW_submit').click(function(){
        var PWD = $('#now_pw').val();
        var NEW_PWD = $('#change_pw1').val();
        var NEW_PWD_TEMP = $('#change_pw2').val();
        
        if(PWD == ''){
            alertApp('!', '현재 비밀번호를 입력해주세요.');
            $('#now_pw').focus();
            return false;
        }
        if(NEW_PWD == ''){
            alertApp('!', '변경하실 비밀번호를 입력해주세요.');
            $('#change_pw1').focus();
            return false;
        }
        if(NEW_PWD_TEMP == ''){
            alertApp('!', '변경하실 비밀번호를 입력해주세요.');
            $('#change_pw2').focus();
            return false;
        }
        if(NEW_PWD != NEW_PWD_TEMP){
            alertApp('!', '변경하실 비밀번호가 같지 않습니다.');
            $('#change_pw2').focus();
            return false;
        }

        var formData = new FormData();
            formData.append('USER_SQ', TRAINER_SEQ);
            formData.append('PWD', PWD);
            formData.append('NEW_PWD', NEW_PWD);
        
        $.ajax({
            url: 'flow_controller.php?task=execManagerPassChange',
            data: formData,
            method: 'POST',
            cache : false,
            contentType: false,
            processData: false,
            success: function(r){
                let data = JSON.parse(r);
                if(data.result == 'Fail'){
                    if(data.reason == 'Password Incorrect'){
                        alertApp('X','현재 비밀번호가 다릅니다.');
                        return false;  
                    }else if(data.reason == 'User not Exist!'){
                        alertApp('X','기존 비밀번호로 바꿀수 없습니다.');
                        return false;
                    }
                };
                alertApp('O','비밀번호가 변경되었습니다.');
                $('#pw_change_frm').fadeOut(200);
                return false;
                
            },
            error: function(e){
                alertApp('X','다시 시도해주세요.');
                return false;
            }
        });
    });

    // 임직원 삭제 버튼
    $('#del_btn').click(()=>{
        if($USER_GRADE < 3){
            if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 87) == -1){
                alertApp('X', '권한이 없습니다.');
                return false;
            }
        }
        var ask = confirm('정말 삭제하시겠습니까?');
        if(ask){
            // 임직원삭제
            var formData = new FormData();
            formData.append('USER_SQ', TRAINER_SEQ);
        
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
                            alertApp('X','이미 삭제된 임직원입니다.');
                            return false;
                        }
                        alertApp('X','다시 시도해주세요.');
                        return false;
                    };
                    alertApp('O','삭제되었습니다.');
                    location.href = 'setting.php';
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

    // 회원정보 수정 버튼 클릭
    const editUserBtn = $('.content > div.up > div.info_s > p > button');

    editUserBtn.click(function(){
        if($USER_GRADE < 3){
            if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 87) == -1){
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

            }else{
                resultData();
            }

            function resultData(){
                TRAINERINFO.USER_NM = $('#edit_name').val();
                TRAINERINFO.GENDER = $('#edit_gender').val();
                TRAINERINFO.PHONE_NO = $('#edit_num').val();
                TRAINERINFO.BIRTH_DT = $('#edit_year').val();
                TRAINERINFO.ADDRESS = $('#edit_address').val();
                TRAINERINFO.EMAIL = $('#edit_email').val();
                
                me.removeClass('save').text('수정');
                me.siblings('span').show();
                me.siblings('.editForm').hide();
                EDIT_USER_DATA_SAVE();
            }
        }

    }

    function EDIT_USER_DATA_SAVE(){
        let formData = new FormData();
        formData.append('USER_SQ',TRAINERINFO.USER_SQ);
        formData.append('USER_NM',TRAINERINFO.USER_NM);
        formData.append('GENDER',TRAINERINFO.GENDER);
        formData.append('PHONE_NO',TRAINERINFO.PHONE_NO);
        formData.append('BIRTH_DT',TRAINERINFO.BIRTH_DT);
        formData.append('ADDRESS',TRAINERINFO.ADDRESS);
        formData.append('EMAIL',TRAINERINFO.EMAIL);

        $.ajax({
            url: "flow_controller.php?task=EditManagerInfo", //&amp_=" + Date.now(),
            method: "POST",
            data: formData,
            processData: false, 
            contentType: false,
            cache: false,
            timeout: 600000,
            xhrFields: {
                withCredentials:true
            },
            success: function (result) {
                GET_TRAINER_INFO(TRAINERINFO.USER_SQ);
                return false;
            },
            error: function (e) {
                console.log('ERROR : ', e);
                alert('저장에 실패하였습니다.');
            }
        });	
    }


    // 이용권 보유 회원 목록 개인/그룹레슨
    $('#soloGroupFilter').change(function(){
        var count = 0;
        var val = $(this).val();
        $('#voucherMyMember_list > li').hide();
        $('#voucherMyMember_list > li .count').hide();

        switch(val) {
            case 'solo':
                $('#voucherMyMember_list > li[data-solo="true"]').show();
                $('#voucherMyMember_list > li .count.voucherSoloCount').show();
                count = $('#voucherMyMember_list > li[data-solo="true"]').length;
                break;
            case 'group':
                $('#voucherMyMember_list > li[data-group="true"]').show();
                $('#voucherMyMember_list > li .count.voucherGroupCount').show();
                count = $('#voucherMyMember_list > li[data-group="true"]').length;
                break;
        }
        $('#myMemberCount2').text(count + '명');
    });

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
            formData.append('MEMBER_SQ', TRAINERINFO.USER_SQ);
            formData.append('myFileUp', FILE);
            
        $.ajax({
            url: 'flow_controller.php?task=execUserImageChange',
            data: formData,
            method: 'POST',
            processData: false,
            contentType: false,
            cache: false,
            success: function(r){
                alertApp('O', '이미지가 변경되었습니다.');
                GET_TRAINER_INFO(TRAINERINFO.USER_SQ);
            },
            error: function(e){
                location.reload();
            }
        });
    });

    // 사진파일 읽기, 브라우저에 표출
    function readURL(input) {
        if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('img.user_face').attr('src', e.target.result);        //cover src로 붙여지고
        }
        reader.readAsDataURL(input.files[0]);
        }
    }

    $('#edit_num').keyup(function(){
        var val = $(this).val().toLowerCase();
        var n = val.search(/[a-z,-]/);
        var phone = /^\d{3}-\d{3,4}-\d{4}$/;

        n > -1 ? $(this).val(val.slice(0,n)) : '' ;

        $(this).val( val.replace(/[^0-9]/g, "").replace(/(^02|^0505|^1[0-9]{3}|^0[0-9]{2})([0-9]+)?([0-9]{4})$/,"$1-$2-$3").replace("--", "-") );
        val.length == 13 ? $('#u_email').focus() : '' ;
    });



    //// 스케줄설정
    $('#scheduleSet_saveBtn').click(function(){
        if($USER_GRADE < 3){
            if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 87) == -1){
                alertApp('X', '권한이 없습니다.');
                return false;
            }
        }
        let MEMBER_WORKTIME_SQ = MY_SCHEDULE_SET ? MY_SCHEDULE_SET.MEMBER_WORKTIME_SQ : 0;
        let MANAGER_SQ = TRAINER_SEQ;
        let MON = $('article.tab_menu2 > div.content1 ul > li').eq(0).hasClass('active') ? '1' : '0';
        let TUE = $('article.tab_menu2 > div.content1 ul > li').eq(1).hasClass('active') ? '1' : '0';
        let WED = $('article.tab_menu2 > div.content1 ul > li').eq(2).hasClass('active') ? '1' : '0';
        let THU = $('article.tab_menu2 > div.content1 ul > li').eq(3).hasClass('active') ? '1' : '0';
        let FRI = $('article.tab_menu2 > div.content1 ul > li').eq(4).hasClass('active') ? '1' : '0';
        let SAT = $('article.tab_menu2 > div.content1 ul > li').eq(5).hasClass('active') ? '1' : '0';
        let SUN = $('article.tab_menu2 > div.content1 ul > li').eq(6).hasClass('active') ? '1' : '0';
        let WORK_TIME = $('#classTime1').val() + ':' + $('#classTime2').val() + ':' + $('#classTime3').val() + ':' + $('#classTime4').val();

        let formData = new FormData();
            formData.append('MEMBER_WORKTIME_SQ',MEMBER_WORKTIME_SQ);
            formData.append('MANAGER_SQ',MANAGER_SQ);
            formData.append('MON',MON);
            formData.append('TUE',TUE);
            formData.append('WED',WED);
            formData.append('THU',THU);
            formData.append('FRI',FRI);
            formData.append('SAT',SAT);
            formData.append('SUN',SUN);
            formData.append('WORK_TIME',WORK_TIME);
        
        $.ajax({
            url: 'flow_controller.php?task=execManagerSchedSettingModify',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            success: function(r){
                GET_TRAINER_INFO(TRAINER_SEQ);
                alertApp('O','저장이 완료되었습니다.');
                return false;
            },
            error: function(e){
                console.error(e);
            }
        })
    });


    
    //// 휴일설정
    // datepicker
    var cal = $('#calendar').datepicker({
        dateFormat: 'yy-mm-dd',
        yearSuffix: "년",
        monthNamesShort: ['1','2','3','4','5','6','7','8','9','10','11','12'],
        monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
        dayNamesMin: ['일','월','화','수','목','금','토'],
        dayNames: ['일요일','월요일','화요일','수요일','목요일','금요일','토요일'],

        onSelect: function(dateText){
            if($USER_GRADE < 3){
                if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 87) == -1){
                    alertApp('X', '권한이 없습니다.');
                    GET_TRAINER_INFO(TRAINER_SEQ);
                    return false;
                }
            }
            console.log(dateText);
            if((MY_HOLIDAY_LIST.filter(e => e.HOLIDAY == dateText)).length != 0){
                var filtedList = (MY_HOLIDAY_LIST.filter(e => e.HOLIDAY == dateText))[0];
                DEL_HOLIDAY(filtedList.HOLIDAY_SQ);
            }else{
                var ask = prompt('휴일 사유를 적어주세요.','휴무');
                if(ask == ''){
                    ask = '휴무';
                }
                if(ask != null){
                    SEND_HOLIDAY(dateText,ask);
                }
                if(ask == null){
                    GET_TRAINER_INFO(TRAINER_SEQ);
                    return false;
                }
                console.log(ask);
                return false;
            }

        },
        onChangeMonthYear: function(){
            setTimeout(()=>MAKE_MY_HOLIDAY(MY_HOLIDAY_LIST),100);
        }
    });
    function SEND_HOLIDAY(dateText,ask){
        
        var formData = new FormData();
        formData.append('HOLIDAY',dateText);
        formData.append('HOLIDAY_NAME',ask);
        formData.append('MANAGER_SQ',TRAINER_SEQ);

        $.ajax({
            url: "flow_controller.php?task=execManagerHolidayAdd",
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(result){
                GET_TRAINER_INFO(TRAINER_SEQ);
                return false;
            },
            error: function (e) {
                console.log(e);
            }
        });
    }
    function DEL_HOLIDAY(HOLIDAY_SQ){
        
        var formData = new FormData();
        formData.append('HOLIDAY_SQ',HOLIDAY_SQ);
        formData.append('MANAGER_SQ',TRAINER_SEQ);

        $.ajax({
            url: "flow_controller.php?task=execHolidayDelete",
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(result){
                var data = JSON.parse(result);
                if(data.result){
                    GET_TRAINER_INFO(TRAINER_SEQ);
                    alertApp('X','다시 시도해주세요.');
                    return false;
                }else{
                    GET_TRAINER_INFO(TRAINER_SEQ);
                    alertApp('O','휴일이 삭제되었습니다.');
                    return false;
                }
            },
            error: function (e) {
                console.log(e);
            }
        });
    }


    //// 인사정보
    // 직급 수정
    $('#myPosition').change(function(){
        EDIT_INSA_INFO();
    });

    // 재직구분 수정
    $('#myStatus').change(function(){
        if($(this).val() == 3){
            $('#input_myWorkEndDate').show();
            return false;
        }else{
            $('#input_myWorkEndDate').hide();
            EDIT_INSA_INFO();
            return false;
        }
    });
    // 퇴직날짜 입력
    $('#input_myWorkEndDate').change(function(){
        EDIT_INSA_INFO();
    });

    // 입사날짜 수정
    $('#input_workStartDate').change(function(){
        EDIT_INSA_INFO();
    });

    function EDIT_INSA_INFO(){
        var USER_SQ = TRAINERINFO.USER_SQ;
        var WORKCATEGORY = $('#myPosition').val();
        var WORKSTATUS = $('#myStatus').val();
        var WORKSTARTDATE = $('#input_workStartDate').val();
        var WORKENDDATE = $('#input_myWorkEndDate').val();

        var formData = new FormData();
        formData.append('USER_SQ', USER_SQ);
        formData.append('WORKCATEGORY', WORKCATEGORY);
        formData.append('WORKSTATUS', WORKSTATUS);
        formData.append('WORKSTARTDATE', WORKSTARTDATE);
        formData.append('WORKENDDATE', WORKENDDATE);

        $.ajax({
            url: "flow_controller.php?task=execManagerAddInfoModify",
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(result){
                GET_TRAINER_INFO(TRAINERINFO.USER_SQ);
            },
            error: function(e){
                console.log(e);
                return false;
            }
        });

    }

    // 트레이너 정보 탭 메뉴
    var trainerTab = $('.content > div.down > ul.tab_menu > li');
    var tabContent = $('.content > div.down > article');
    tabContent.not(tabContent.eq(0)).hide();
    trainerTab.click(function(){
        var i = $(this).index();
        $(this).addClass('active').siblings().removeClass('active');
        tabContent.hide();
        tabContent.eq(i).show();
    });


    // 스케줄 설정
    var TICKET_DAY_SET = $('article.tab_menu2 > div.content1 ul > li');
    var TICKET_DAY_TABLE = $('article.tab_menu2 > div.content2 ul > li');
    TICKET_DAY_SET.not(TICKET_DAY_SET.eq(0).add(TICKET_DAY_SET.eq(6))).addClass('active');
    TICKET_DAY_SET.click(function(){
        var i = $(this).index();
        $(this).toggleClass('active');
        TICKET_DAY_TABLE.eq(i).toggleClass('off');
    });
    
    // 휴일 설정
    var TIRINGDay_SET = $('article.tab_menu3 > div#calendar');
    var selectDateWrap = $('article.tab_menu3 > div.content2');
    var cal = TIRINGDay_SET.datepicker({
        onSelect: function(dateText,inst){
            var selectDate = dateText.slice(6,10) + '.' + 
                             dateText.slice(0,2) + '.' + 
                             dateText.slice(3,5);
            selectDateWrap.text().indexOf(selectDate) == -1 ? 
            selectDateWrap.append('<div>' + selectDate + '</div>')
            : '' ;
            selectDateWrap.find('#weekendDay').text(selectDateWrap.find('div').length);
            selectDateWrap.find('div').click(function(){
                $(this).remove();
                selectDateWrap.find('#weekendDay').text(selectDateWrap.find('div').length);
            });
        }
    });

    $('.content > div.down > article.tab_menu6 input[type="text"]').keyup(function(){
        var val = $(this).val().replace(/\,/g,'');
        $(this).val(numberFormat(val));
    });


    // 급여 및 수당정보 저장버튼
    $('#trainerPayInfoSaveBtn').click(function(){

        var MANAGER_SQ = TRAINER_SEQ;
        var SALARY = $('#pay-month').val().replace(/\,/g,'');
        var INSENTIVE = $('#pay-my').val().replace(/\,/g,'');
        var SALARY_TAX_EXCEPT = $('#pay-afterTax').prop('checked') ? 1 : 0;
        var PERSONAL_ALLOWANCE_TYPE = $('#solo-pay1').prop('checked') ? 1 : 2;
        var PERSONAL_ALLOWANCE_AMOUNT = $('#solo-pay1-value').val().replace(/\,/g,'');
        var PERSONAL_ALLOWANCE_RATIO = $('#solo-pay2-value').val().replace(/\,/g,'');
        var PERSONAL_ALLOWANCE_TAX_EXCEPT = $('#solo-pay-afterTax_class').prop('checked') ? 1 : 0;
        var PERSONAL_NOSHOW_TYPE = $('#solo-no-show-pay1').prop('checked') ? 1 : 2;
        var PERSONAL_NOSHOW_RATIO = $('#solo-no-show-pay2-value').val().replace(/\,/g,'');
        var GROUP_ALLOWANCE_TAX_EXCEPT = $('#group-pay-afterTax_class').prop('checked') ? 1 : 0;
        var GROUP_NOSHOW_TYPE = $('#group-no-show-pay1').prop('checked') ? 1 : 2;
        var GROUP_NOSHOW_RATIO = $('#group-no-show-pay2-value').val().replace(/\,/g,'');


        let form = new FormData();
            form.append('MANAGER_SQ', MANAGER_SQ);
            form.append('SALARY', SALARY);
            form.append('INSENTIVE', INSENTIVE);
            form.append('SALARY_TAX_EXCEPT', SALARY_TAX_EXCEPT);
            form.append('PERSONAL_ALLOWANCE_TYPE', PERSONAL_ALLOWANCE_TYPE);
            form.append('PERSONAL_ALLOWANCE_AMOUNT', PERSONAL_ALLOWANCE_AMOUNT);
            form.append('PERSONAL_ALLOWANCE_RATIO', PERSONAL_ALLOWANCE_RATIO);
            form.append('PERSONAL_ALLOWANCE_TAX_EXCEPT', PERSONAL_ALLOWANCE_TAX_EXCEPT);
            form.append('PERSONAL_NOSHOW_TYPE', PERSONAL_NOSHOW_TYPE);
            form.append('PERSONAL_NOSHOW_RATIO', PERSONAL_NOSHOW_RATIO);
            form.append('GROUP_ALLOWANCE_TAX_EXCEPT', GROUP_ALLOWANCE_TAX_EXCEPT);
            form.append('GROUP_NOSHOW_TYPE', GROUP_NOSHOW_TYPE);
            form.append('GROUP_NOSHOW_RATIO', GROUP_NOSHOW_RATIO);

        $.ajax({
            url: "flow_controller.php?task=execManagerSalaryInfoModify",
            method: "POST",
            data: form,
            contentType: false,
            processData: false,
            success: function(r){
                if(r == 'Fail'){
                    alertApp('!', '변경사항이 없습니다.');
                    return false;
                }
                alertApp('O', '저장되었습니다.');
            },
            error: function(e){
                alertApp('X', '다시 시도해주세요.');
                return false;
            }
        })
    });


    // setTimeout(() => {
    //     if($USER_GRADE < 3){
    //         if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 87) == -1){
    //             $('.content > div.down > ul.tab_menu > li').eq(4).hide();
    //             $('.content > div.down > ul.tab_menu > li').eq(5).hide();
    //         }
    //     }
    // },200);
});