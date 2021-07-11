var memberList = [];
var memberSearchListData;
var trainerList = [];
var useitemList = [];
var $i = 0;

function getMembers() {
	$.ajax({
		url: "flow_controller.php?task=getUserList&amp_=" + Date.now(),
		method: "GET",
		contentType: false,
		processData: false,
		success: function (e) {
			// console.log(e);
			var data = e.split('|');
			memberList = JSON.parse(data[0]);
			trainerList = JSON.parse(data[1]);
			memberSearchListData = memberList;
			makeList_DOM(memberSearchListData);
			trainerDropBox_DOM(trainerList);

            MAKE_MEAS_DATE_LIST(memberList.filter(e => e.MEAS_DATE != null));
		},
		error: function (e) {
			console.log(e);
		}
	});
}

function MAKE_MEAS_DATE_LIST(listData){
    var list = listData.filter(e => e.ISUSE == 1);
    var tag = '';
    $('#MEAS_DATE_LIST_FRM tbody').empty();
    let sortedList = (() =>{
        let li = [];
        for(let i of list){
            i.MEAS_DATE = i.MEAS_DATE.replace(/-/gi, "");
            li.push(i);
        }
        return li;
    })();
    sortedList = sortedList.filter(e => e.MEAS_DATE != '');
    sortedList.sort((a,b) => {
        return Number(b.MEAS_DATE) - Number(a.MEAS_DATE);
    });

    for(let i of sortedList){
        i.MEAS_DATE = i.MEAS_DATE.slice(0,4) + '-' + i.MEAS_DATE.slice(4,6) + '-' + i.MEAS_DATE.slice(6,8);
        tag += 
            `<tr data-seq="${i.USER_SQ}">
                <td>${i.MEAS_DATE}</td>
                <td>${i.USER_NM}</td>
                <td>${i.POSE_DT == null ? '-' : i.POSE_DT.split(' ')[0]}</td>
                <td>${i.ROM_DT == null ? '-' : i.ROM_DT.split(' ')[0]}</td>
                <td>
                    <button>
                        이동
                        <i class="fas fa-external-link-alt"></i>
                    </button>
                </td>
            </tr>`
    }
    $('#MEAS_DATE_LIST_FRM tbody').html(tag);

    $('#MEAS_DATE_LIST_FRM > .content table td > button').click(function(){
        var seq = $(this).parents('tr').attr('data-seq');
        sessionStorage.memberbodyLink;
        sessionStorage.memberbodyLink = 1;
        location.href = 'member_info.php?u_seq=' + seq;
    });

}

function trainerDropBox_DOM(list){
	$('#u_teacher').html('<option value="">선택</option>');
	for(var i in list){
		$('#u_teacher').append(
			'<option value="' + list[i].USER_SQ + '">' + list[i].USER_NM + '</option>'
		);
	}
}
function member_DOM(list){

    if (list.length == 0) {
        $('#memberList_JS').find('tbody').html(`
            <tr>
                <td colspan="12">회원이 없습니다.</td>
            </tr>
        `)
    }

    for(var i of list){
        // i.teacherSeq = trainerList[ trainerList.filter(i => i.Sequence == i.teacherSeq) ][0].Name;
        i.ticket = i.VOUCHER_NAME == null ? '-' : i.VOUCHER_NAME;
        i.ticketEndDate = i.USE_LASTDATE == null ? '-' : i.USE_LASTDATE.split(' ')[0];
        i.ticketCount = i.REMAINCOUNT == null ? '-' : i.REMAINCOUNT;
        i.rockerEndDate = '-';

        // 최근 측정일
        i.MEAS_DATE = i.MEAS_DATE == null || i.MEAS_DATE == '' ? '-' : i.MEAS_DATE ;
        
        // 트레이너 SEQ로 트레이너 이름찾기
        var temp = [];
        temp = trainerList.filter(e => e.USER_SQ == i.TRAINER);
        temp.length > 0 ? i.TRAINER_NM = temp[0].USER_NM : i.TRAINER_NM = '-';
        
        var BIRTH_DT_CALC = 0;
        BIRTH_DT_CALC = birth_year(i.BIRTH_DT);
        
        // 날짜시간 DATA (날짜만 분리)
        i.REG_DT = i.REG_DT != '-' ? i.REG_DT.split(' ')[0] : '-' ;
        i.MEAS_DATE = i.MEAS_DATE != '-' ? i.MEAS_DATE.split(' ')[0] : '-' ;
        i.rockerEndDate = i.rockerEndDate != '-' ? i.rockerEndDate.split(' ')[0] : '-' ;

    //


        var Tag =
            '<tr data-seq="' + i.USER_SQ + '" data-seq="' + i.CENTER_SQ + '">\
                <td>\
                    <input type="checkbox" name="chList' + i.USER_SQ + '" id="chList' + i.USER_SQ + '">\
                    <label for="chList' + i.USER_SQ + '" class="hid">체크</label>\
                </td>\
                <td>' + i.USER_SQ + '</td>\
                <td>' + i.USER_NM + '</td>\
                <td>' + BIRTH_DT_CALC + '</td>\
                <td>' + i.PHONE_NO + '</td>\
                <td>' + i.REG_DT + '</td>\
                <td>' + i.ticket + '</td>\
                <td>' + i.ticketEndDate + '</td>\
                <td>' + i.ticketCount + '</td>\
                <td>' + i.MEAS_DATE + '</td>\
                <td>' + i.rockerEndDate + '</td>\
                <td>' + i.TRAINER_NM + '</td>\
            </tr>';
        $('#memberList_JS').find('tbody').append(Tag);
    }

}
// memberSearchListData 파라미터
function makeList_DOM(list){

    // 회원 리스트 생성시키는 함수
    $('#memberList_JS').find('tbody').empty();
    var templist = list.filter(x => x.ISUSE == '1');
    
    member_DOM(templist);
    
	
	// 전체선택
    $('.memberList .m_list > table > thead th > input').click(function(){
        if($(this).is(':checked')){
            $(this).parents('.m_list').find('tbody input:checkbox').prop('checked', true);
        }else{
            $(this).parents('.m_list').find('tbody input:checkbox').prop('checked', false);
        }
    });

    // 회원정보로 가기
    $('table#memberList_JS td').not($('table#memberList_JS td:first-of-type')).click(function(){
        location.href = 'member_info.php?u_seq=' + $(this).parent().attr('data-seq') ;
    });

}

function totalSearch(data){
    if(data){
        var temp1 = memberList.filter((e) => e.USER_NM.search(data) > -1);
        var temp2 = memberList.filter((e) => e.PHONE_NO.search(data) > -1);
        memberSearchListData = temp1.concat(temp2);
        if(memberSearchListData.length == 0){
            alert('검색결과가 없습니다.');
            $('#searchBox').val('').focus();
            memberSearchListData = memberList;
            makeList_DOM(memberSearchListData);
            return false;
        }else{
            makeList_DOM(memberSearchListData);
        }
    }else{
        // $('#searchBox').focus();
        memberSearchListData = memberList;
        makeList_DOM(memberSearchListData);
        return false;
    }
}

// 사진파일 읽기, 브라우저에 표출
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


// 문서가 전부 Load 되면 할 일..
$(function(){

    // session
    if(sessionStorage.searchMemberName){
        $('#searchBox').val(sessionStorage.searchMemberName);
        setTimeout(() => $('#searchBox_Btn').click(),300);
        delete sessionStorage.searchMemberName;
    }

    $('#user_frm_submit').click(function(){
        
        var myFileUp = $('#user-face').prop('files')[0];
        var u_name = $('#u_name').val();
        var u_year = $('#u_year').val();
        var u_gender = $('#male').prop('checked') ? 'M' : 'F';
        var u_num = $('#u_num').val();
        var u_email = $('#u_email').val();
        var u_teacher = $('#u_teacher').val();
        var u_address = $('#u_address').val();
        var u_memo = $('#u_memo').val();

        if(u_name == ''){
            alertApp('!', '이름을 입력해주세요.');
            $('#u_name').focus();
            return false;
        }
        if(u_year == ''){
            alertApp('!', '생년월일을 입력해주세요.');
            $('#u_year').focus();
            return false;
        }
        if(u_num == ''){
            alertApp('!', '연락처을 입력해주세요.');
            $('#u_num').focus();
            return false;
        }

        let form = new FormData();
            form.append('myFileUp', myFileUp);
            form.append('u_name', u_name);
            form.append('u_year', u_year);
            form.append('u_gender', u_gender);
            form.append('u_num', u_num);
            form.append('u_email', u_email);
            form.append('u_teacher', u_teacher);
            form.append('u_address', u_address);
            form.append('u_memo', u_memo);

        $.ajax({
            url: "flow_controller.php?task=UserRegSimple&amp_=" + Date.now(),
            method: "POST",
            data: form,
            contentType: false,
            processData: false,
            success: function(r){
                if(r.indexOf('member_register = "fail"') > -1){
                    alertApp('X', '연락처가 중복됩니다.');
                    return false;
                }
                alertApp('O', '회원이 등록되었습니다.');
                $('.newMember').add($('.dark_div')).fadeOut(200);
                getMembers();
            }
        });
    });
    
	getMembers();

	// 조검검색 열고 닫기 버튼
    $('#if').click(function(){
        alertApp('!', '서비스 준비중입니다.');
        return;
        var searchBox_h = $('article.down').css('display');
        var icon = $('#if').find('i');
        if(searchBox_h == 'none'){
            icon.css({'transform': 'rotateX(-180deg)'});
        }else{
            icon.css({'transform': 'rotateX(0deg)'});
        }
        $('article.down').stop().slideToggle(200);
    });
    
    $('#searchBox').keydown(function(e){
        e.keyCode == 13 ? $('#searchBox_Btn').click() : '' ;
    });

    //통합검색
    $('#searchBox_Btn').click(function(){
        var textValue = $('#searchBox').val().toLowerCase();
        if (textValue == '') {
            $('#searchBox').focus();
        }
        totalSearch(textValue);
    });
    
    // 신규 회원등록창 열기
    $('#new_User').click(function(){
        if($USER_GRADE < 3){
            if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 10) == -1){
                alertApp('X', '권한이 없습니다.');
                return false;
            }
        }
        $('.newMember > form.new_wrap p > input').val('');
        $('#male').click();
        $('#user-face').val('');
        $('.user_face').attr('src', 'img/user.png');
        $('#u_teacher').val('');
        $('#u_memo').val('');
        $('.newMember').add($('.dark_div')).fadeIn(200);
    });

    // 신규 회원등록창 닫기
    $('.closePOP').add($('#user_frm_close')).click(function(){
            $u_name = $('#u_name').val(),
            $u_year = $('#u_year').val(),
            $u_num = $('#u_num').val(),
            $u_email = $('#u_email').val(),
            $u_teacher = $('#u_teacher').val(),
            $u_address = $('#u_address').val(),
            $u_memo = $('#u_memo').val();
            
        if($u_name != '' || $u_year != '' || $u_num != '' || $u_email != '' || $u_teacher != '' || $u_address != '' || $u_memo != '' || $('.user_face').attr('src') != 'img/user.png'){
            $('.alert').add($('.unClick')).show();
        }else{
            $('.newMember').add($('.dark_div')).fadeOut(200);
        }
    });

    // 기간 연장
    $('#adddate_User').click(function(){

        if($USER_GRADE < 3){
            if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 12) == -1){
                alertApp('X', '권한이 없습니다.');
                return false;
            }
        }

        if(MEMBER_CHECKED_YN() == false){
            alertApp('!', '회원을 선택해주세요');
            return false;
        }

        $('#addVoucherDate').val(0);
        $('#memberAddVoucherDate').add($('.dark_div')).fadeIn(200);

    });

    $('#addVoucherDate').keyup(function(){
        if($(this).val() == '' || $(this).val() == NaN){
            return false;
        }
        var val = parseInt($(this).val());
        $(this).val(val);
    });

    $('#memberAddVoucherDate > .content > .con button').click(function(){
        var days = parseInt($(this).attr('class'));
        $('#addVoucherDate').val(days);
    });

    $('#AddVoucherDateSubmitBtn').click(function(){

        var val = $('#addVoucherDate').val();

        if(val == '' || val == '0'){
            $('#addVoucherDate').val('0');
            alertApp('!', '이용권 기간이 연장되지 않았습니다.');
            return false;
        }

        var DAYS = Number($('#addVoucherDate').val());

        var formData = new FormData();
            formData.append('MEMBERS_SQ', MEMBER_CHECKED_YN());
            formData.append('DAYS', DAYS);

        $.ajax({
            url: 'flow_controller.php?task=execUV_PeriodExtend',
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            cache: false,
            success: function(r){
                var data = JSON.parse(r);
                if(data.result == 'Fail'){
                    alertApp('X', '이용권 기간이 연장되지 않았습니다.');
                    return false;
                }
                alert('이용권 기간이 연장되었습니다.');
                location.href = 'members.php';
                return false;
            },
            error: function(e){
                alertApp('X', '다시 시도해주세요.');
                return false;
            }
        });
    });

    $('#AddVoucherDateSubmitBtn').siblings('.closePop').add($('.closePopup')).click(function(){
        $('#memberAddVoucherDate').add($('.dark_div')).fadeOut(200);
    });

    // 회원 삭제
    $('#del_User').click(function(){
        if($USER_GRADE < 3){
            if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 11) == -1){
                alertApp('X', '권한이 없습니다.');
                return false;
            }
        }

        if(MEMBER_CHECKED_YN() == false){
            alertApp('!', '회원을 선택해주세요');
            return false;
        }

        var ask = confirm('선택한 회원을 삭제하시겠습니까?');
        if(ask){

            var formData = new FormData();
                formData.append('MEMBERS_SQ', MEMBER_CHECKED_YN());

            $.ajax({
                url: 'flow_controller.php?task=execMemberDelete',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                cache: false,
                success: function(r){
                    var data = JSON.parse(r);
                    if(data.result == 'Fail'){
                        if(data.reason == 'User Already Disabled!'){
                            alertApp('X', '이미 삭제된 회원입니다.');
                            return false;
                        }else{
                            alertApp('X', '다시 시도해주세요.');
                            return false;
                        }
                    }
                    alertApp('O', '삭제가 완료되었습니다.');
                    getMembers();
                    return false;
                },
                error: function(e){
                    alertApp('X', '다시 시도해주세요.');
                    return false;
                }
            });
                  
        }
    });

    function MEMBER_CHECKED_YN(){
        var chkList = [];

        $('[id^="chList"]').each(function(){
            if($(this).prop('checked')){
                let temp = $(this).attr('id').split('chList');
                let SQ = temp[temp.length - 1];
                chkList.push(SQ);
            }
        });

        if(chkList.length == 0){
            return false;
        }
        return chkList;
    }

    // 회원등록 경고창 (확인버튼)
    $('#unload').click(function(){
        $('.user_face').attr('src','img/user.png');
        $('.alert').add($('.unClick')).add($('.newMember')).add($('.dark_div')).fadeOut(200);
    });

    // 이미지 업로드
    $("#user-face").change(function(){
        readURL(this);
    });
    

    // 생년월일 입력시
    $('#u_year').change(function(){
        var date = new Date();
        var date_Y = String(date.getFullYear());
        var date_M = String(date.getMonth() + 1).length == 1 ? '0' + String(date.getMonth() + 1) : String(date.getMonth() + 1);
        var date_D = String(date.getDate()).length == 1 ? '0' + String(date.getDate()) : String(date.getDate());
        var u_date = String($(this).val());
        date = Number(date_Y + date_M + date_D);
        u_date = u_date.split('-');
        u_date = u_date[0] + u_date[1] + u_date[2];
        var result = u_date - date;
        if(result >= 0){
            alert('오늘보다 이전 날짜를 선택해주세요.');
            $(this).val('');
            return false;
        }
    });

    // 전화번호 유효성에 맞게 입력
    $('#u_num').keyup(function(){
        var val = $(this).val().toLowerCase();
        var n = val.search(/[a-z,-]/);
        var phone = /^\d{3}-\d{3,4}-\d{4}$/;

        n > -1 ? $(this).val(val.slice(0,n)) : '' ;

        $(this).val( val.replace(/[^0-9]/g, "").replace(/(^02|^0505|^1[0-9]{3}|^0[0-9]{2})([0-9]+)?([0-9]{4})$/,"$1-$2-$3").replace("--", "-") );
        val.length == 13 ? $('#u_email').focus() : '' ;
    });

    // 최근측정이력 버튼 클릭
    $('#MEAS_DATE_LIST_BTN').click(function(){
        $('#MEAS_DATE_LIST_FRM, .dark_div').fadeIn(200);
    });

    $('#MEAS_DATE_LIST_FRM > h2 > .xBtn').click(function(){
        $('#MEAS_DATE_LIST_FRM, .dark_div').fadeOut(200);
    });



    // 멤버스 리스트 정렬기능 //
    $('.memberList .m_list > table th > i').click(function(){
        var type = $(this).attr('data-sort-type');
        var howSort = $(this).attr('data-sort-howSort');
        var className = 
            $(this).attr('class') == 'fas fa-sort' ? 'fas fa-caret-up' : 
            $(this).attr('class') == 'fas fa-caret-up' ? 'fas fa-caret-down' : 
            'fas fa-caret-up';
        var UpAndDown = className == 'fas fa-caret-up' ? 'up' : 'down';

        $(this).attr('class',className)
               .parent().siblings().find('i').attr('class','fas fa-sort');
        var result = [];

        switch (type) {
            case 'numberSort':
                result = numberSortFn(memberSearchListData, howSort, UpAndDown);
                break;
        
            case 'dateSort':
                result = dateSortFn(memberSearchListData, howSort, UpAndDown);
                break;
        }

        makeList_DOM(result);

    });
});


// 숫자 솔팅 함수
function numberSortFn(list, howSort, UpAndDown){
    var tempList = list.sort(function(a, b){
        let temp_a = a[howSort];
        let temp_b = b[howSort];
        return UpAndDown == 'up' ? 
               temp_a - temp_b : 
               temp_b - temp_a ;
    });
    return tempList;
}


// 날짜 솔팅 함수
function dateSortFn(list, howSort, UpAndDown){
    var tempList = list.sort(function(a, b){
        let temp_a = a[howSort] == '-' ? 0 : a[howSort];
        let temp_b = b[howSort] == '-' ? 0 : b[howSort];
        let aDT = new Date(temp_a);
        let bDT = new Date(temp_b);
        return UpAndDown == 'up' ? aDT - bDT : bDT - aDT;
    });
    return tempList;
}