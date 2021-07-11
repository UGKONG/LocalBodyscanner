var CATEGORY_COLOR = [
    'rgb(22, 133, 70)',
    'rgb(106,90,205)',
    'rgb(179, 179, 0)',
    'rgb(255,165,0)',
    'rgb(255, 51, 153)',
    'rgb(0, 0, 255)',
    'rgb(128,128,128)',
    'rgb(255, 51, 0)',
    'rgb(0, 179, 179)',
]
var TRAINER_LIST = [],
    TRAINER_RANK = [],
    TRAINER_STATUS = [];

var CENTERINFO = [],
    COMPANYINFO = [],
    OPERATIONGINFO = [];

var NOTICE_LIST = [];
var SELECTED_NOTICE_SQ;
var DEL_SELECTED_NOTICE = [];
var HOLIDAY_LIST = [];

var TICKET_LIST = [];
var TICKET_CATEGORY_LIST = [];
var TICKET_SUB_CATEGORY_LIST = [];
var SET = {};

var PAYMENT_ITEM = 0;
var SELECTED_SET_ITEM = '';

var LAST_GRADE = [];
var ADD_AUTH = [];
var DELETE_AUTH = [];

var ROOM_LIST = [];


function getTrainerList(){
    $.ajax({
		url: "flow_controller.php?task=getManagerList",  // + USER_SEQ,
		method: "POST",
		contentType: false,
        processData: false,
        
		success: function (e) {
            var data = e.split('|');
            TRAINER_LIST = JSON.parse(data[0]);
            TRAINER_CATEGORY = JSON.parse(data[1]);
            TRAINER_STATUS = JSON.parse(data[2]);

            MAKE_TRAINER_CATEGORY(TRAINER_CATEGORY);
            MAKE_TRAINER_STATUS(TRAINER_STATUS);
            MAKE_TRAINER_LIST(
                TRAINER_LIST.filter(e => e.WORKSTATUS == $('.trainer > h3 > select').val()).filter(e => e.ISUSE != 0)
            );
            
            GET_NOTICE_DATA();      // 공지관리
		},
		error: function (e) {
			console.log(e);
		}
    });
}
// 임직원 카테고리 (관리자/매니저/트레이너/등록대기 등..)
function MAKE_TRAINER_CATEGORY(obj){
    var Category_tag = '', Group_tag = '', Set_tag = '', add_trainer_category_option = '';
    $('#levelList').empty();
    $('#trainerList').empty();
    $('#addTrainer_level').html('<option value="">선택</option>');
    $('div.popup > .setLevel > ul').empty();

    for(let i = 0; i < obj.length; i++){
        Category_tag += '<li data-seq="' + obj[i].WC_SEQ + '" data-rank="' + obj[i].RANK + '" class=""><span>' + obj[i].NAME + '</span><i class="fas fa-angle-up rankUp"></i><i class="fas fa-angle-down rankDown"></i></li>';
        Group_tag += '<li data-seq="' + obj[i].WC_SEQ + '">' + obj[i].NAME + '<ul></ul></li>';
        Set_tag += '<li><label for="' + obj[i].WC_SEQ + '" class="hid">' + obj[i].NAME + '</label>\
            <input type="text" name="' + obj[i].WC_SEQ + '" id="' + obj[i].WC_SEQ + '" value="' + obj[i].NAME + '" required>\
            <button type="button" data-target="' + obj[i].WC_SEQ + '">삭제</button></li>';
        add_trainer_category_option += '<option value="' + obj[i].WC_SEQ + '">' + obj[i].NAME + '</option>'
        }
        
    $('#addTrainer_level').append(add_trainer_category_option);
    $('#levelList').append(Category_tag);
    $('#levelList').append('<li data-seq="0" class="noneTrainerCategory" data-length=""><span>소속없음</span></li>');
    $('#levelList').append('<li data-seq="9999" class="yetTrainerCategory" data-length=""><span>등록대기</span></li>');
    $('#trainerList').append(Group_tag);
    $('#trainerList').append('<li data-seq="0">소속없음<ul></ul></li>')
    $('#trainerList').append('<li data-seq="9999">등록대기<ul></ul></li>')
    $('div.popup > .setLevel > ul').append(Set_tag);
    

    // 직급 클릭 (필터기능)
    $('#levelList > li > span').click(function(){
        var seq = $(this).parent().attr('data-seq');
        if($(this).parent().attr('class').indexOf('active') > -1){
            $('#trainerList > li').show();
        }else{
            $('#trainerList > li[data-seq="' + seq + '"]').show().siblings().hide();
        }
        $(this).parent().toggleClass('active').siblings().removeClass('active');
        
    });
    
    // 직급편집에서 수정 버튼 클릭
    $('#set_trainer_category_submit_btn').click(function(e){
        e.preventDefault();
        var inputList = $('[name="setLevelFrm"] > ul input');
        for(let i = 0; i < inputList.length; i++){
            var SQ = 0, VALUE = '';
            SQ = inputList.eq(i).attr('id');
            VALUE = inputList.eq(i).val();
            SET_TRAINER_CATEGORY(SQ, VALUE);
        }
    });

    // 직급편집에서 직급 삭제 버튼 클릭
    $('div.popup > .setLevel input + button').click(function(){
        var WC_SEQ = $(this).attr('data-target');
        var name = $(this).siblings('input').val();
        var ask = confirm('\'' + name + '\' 직급을 삭제 하시겠습니까?');
        if(ask){

            useAjax('execWorkCategryDelete', (result) => {
                var data = JSON.parse(result);
                if(data.result) return;
                alertApp('O','삭제되었습니다.');
                getTrainerList();
            }, { WC_SEQ: WC_SEQ } );
            
        }
    });

    // 직급 순위 변경
    $('.level ul > li > i').click(function(){
        var SQ = $(this).parent().attr('data-seq');
        var UPDOWN = $(this).attr('class').indexOf('rankUp') > -1 ? -1 : 1;
        TRAINER_CATEGORY_RANK(SQ, UPDOWN);
    });

}

// 임직원 상태 (재직/휴직/퇴사 등..)
function MAKE_TRAINER_STATUS(obj){
    var tag = '';
    $('#trainerStatus').empty();
    for(let i = 0; i < obj.length; i++){
        tag +='<option value="' + obj[i].CODE + '">' + obj[i].DESCRIPTION + '</option>';
    }
    $('#trainerStatus').append(tag);

    
    // 임직원 상태 필터
    $('.trainer > h3 > select').change(function(){
        var status = $(this).val();
        var filtedList = TRAINER_LIST.filter(e => e.WORKSTATUS == status).filter(e => e.ISUSE != 0);
        MAKE_TRAINER_LIST(filtedList);
        // getTrainerList();
    });
}

// 임직원 리스트 화면에 출력
function MAKE_TRAINER_LIST(list){
    var tag = '';
    $('.trainer > ul > li > ul').empty();
    $('#wrap > .content > article#rightSet nav.right_trainerList > .content').empty();

    for(let i = 0; i < list.length; i++){
        var delBtn = list[i].GRADE == '1' ? '<i class="fas fa-trash-alt"> 삭제</i></li>' : '';
        var register = list[i].GRADE == '1' ? '<i class="fas fa-check-circle"></i> 등록' : '<i class="fas fa-info-circle"></i> 상세보기';
        var img = list[i].USERIMAGE == '' || list[i].USERIMAGE == null || list[i].USERIMAGE == undefined ? 'img/trainer_profile/user.png' : list[i].USERIMAGE;

        tag = 
        '<li data-seq="' + list[i].USER_SQ + '">\
            <img src="' + img + '" alt="프로필 이미지">\
            <div class="info">\
                <div><p class="name">' + list[i].USER_NM + '</p><p class="phone">' + list[i].PHONE_NO + '</p></div>\
                <div class="class_info">\
                    <p>담당 회원수: <span>' + (Number(list[i].VOUCHERUSER_COUNT) + Number(list[i].USER_COUNT)) + '</span>명</p>\
                    <p>오늘 스케줄: <span>' + (Number(list[i].PERSONAL_RESERV_COUNT)) + '</span>개</p>\
                </div>\
                <div class="detail">' + register + '</div>'
                 + delBtn + 
            '</div>\
        </li>';
        if(list[i].GRADE == 1){
            $('.trainer > ul > li[data-seq="9999"] > ul').append(tag);
        }else if(list[i].WORKCATEGORY == 0){
            $('.trainer > ul > li[data-seq="0"] > ul').append(tag);
        }else{
            $('.trainer > ul > li[data-seq="' + list[i].WORKCATEGORY + '"] > ul').append(tag);
        }
        
        $('#wrap > .content > article#rightSet nav.right_trainerList > .content').append(
            '<li data-seq="' + list[i].USER_SQ + '" data-grade="' + list[i].GRADE + '">' + list[i].USER_NM + '</li>'
        );

    }

    
    // 상세보기 클릭
    $('div.detail').not('.trainer > ul > li[data-seq="9999"] > ul > li div.detail').click(function(){
        var SQ = $(this).parent().parent().attr('data-seq');
        location.href = 'trainerinfo.php?USER_SQ=' + SQ;
    });

    // 임직원삭제 클릭
    $('.trainer > ul > li > ul > li i.fa-trash-alt').click(function(){
        var SQ = $(this).parent().parent().attr('data-seq');
        var name = $(this).parent().find('p.name').text();
        var ask = confirm('\'' + name + '\' 님을 삭제 하시겠습니까?');
        if(ask){
            DEL_TRAINER(SQ);
            return false;
        }
    });

    // 등록대기
    var yetTrainerCategoryTemp = (TRAINER_LIST.filter(e => e.GRADE == 1)).length;
    if(yetTrainerCategoryTemp == 0){
        $('.yetTrainerCategory').attr('data-length',0);
    }else{
        $('.yetTrainerCategory').attr('data-length',yetTrainerCategoryTemp);
    }

    $('.trainer > ul > li[data-seq="9999"] > ul > li div.detail').click(function(){
        var name = $(this).parent().find('p.name').text();
        var sq = $(this).parent().parent().attr('data-seq');
        var ask = confirm(name + '님을 등록 시키겠습니까?');
        if(ask){
            REGISTER(sq);
            alert('등록되었습니다.');
            return false;
        }else{
            return false;
        }
    });

    
    // 권한 설정 -> 임직원 리스트 클릭
    $('#wrap > .content > article#rightSet nav.right_trainerList > .content > li').click(function(){
        var SQ = $(this).attr('data-seq');
        var GRADE = $(this).attr('data-grade');
        $(this).addClass('active').siblings().removeClass('active');
        
        var formData = new FormData();
            formData.append('USER_SQ', SQ);

        $.ajax({
            url: "flow_controller.php?task=getUserAuthority",
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(result){
                var list = JSON.parse(result);
                THIS_MANAGER_GRADE(list,GRADE);
            },
            error: function (e) {
                alertApp('!', '다시 시도해주세요.');
                return false;
            }
        });
    });
    $('#wrap > .content > article#rightSet nav.right_trainerList > .content > li').eq(0).click();

}

function THIS_MANAGER_GRADE(list, GRADE){
    LAST_GRADE = [];

    if(GRADE >= 3){

        $('#rightSet div.right_rightList > div.content > fieldset > input').prop('checked', true).prop('disabled', true);
        $('#rightSet div.right_rightList > div.content > fieldset > input + label').css('cursor', 'default').attr('title', '최고관리자는 권한을 해지하실 수 없습니다.');
        $('#grade_SubmitBtn').prop('disabled', true).css('cursor','default');

    }else{

        $('#rightSet div.right_rightList > div.content > fieldset > input').prop('checked', false).prop('disabled', false);
        $('#rightSet div.right_rightList > div.content > fieldset > input + label').css('cursor', 'pointer').attr('title', '');
        $('#grade_SubmitBtn').prop('disabled', false).css('cursor','pointer');

        for(let i of list){
            $('#rightSet div.right_rightList > div.content > fieldset > input[data-code="' + i.AUTH_CD + '"]').prop('checked', true);
        }
        $('#rightSet div.right_rightList > div.content > fieldset > input').each(function(){
            if(list.findIndex(x => x.AUTH_CD == $(this).attr('data-code')) > -1){
                LAST_GRADE.push(1);
            }else{
                LAST_GRADE.push(0);
            }
        });
    }
    

    
    
}

// 임직원 카테고리 추가
function ADD_TRAINER_CATEGORY(NAME){
    let formData = new FormData();
    formData.append('NAME',NAME);

    $.ajax({
        url: "flow_controller.php?task=execWorkCategryAdd",
        method: "POST",
        data: formData,
		contentType: false,
        processData: false,
		success: function(result){
            var data = JSON.parse(result);
            
            if(data.result) {       //중복있음.
                alert(data.result);
                $('#levelName').val('').focus();
                return false;
            }
            
            getTrainerList()
            $('#levelName').val('');
            $('.dark_div').add($('.popup')).fadeOut(100);
		},
		error: function (e) {
			console.log(e);
		}
    });
}

// 임직원 카테고리 편집
function SET_TRAINER_CATEGORY(sq, value){
    let formData = new FormData();
    formData.append('WC_SEQ',sq);
    formData.append('NAME',value);

    $.ajax({
        url: "flow_controller.php?task=execWorkCategryModify",
        method: "POST",
        data: formData,
		contentType: false,
        processData: false,
		success: function(result){
            var data = JSON.parse(result);

            if(data.result){ return false }

            getTrainerList();
            $('.dark_div').add($('.popup')).fadeOut(100);
            return false;
		},
		error: function (e) {
			console.log(e);
            alert('수정에 실패하였습니다.');
            return false;
		}
    });

}



// 임직원 카테고리 삭제
function DEL_TRAINER_CATEGORY(sq){
    let formData = new FormData();
    formData.append('WC_SEQ',sq);

    $.ajax({
        url: "flow_controller.php?task=execWorkCategryDelete",
        method: "POST",
        data: formData,
		contentType: false,
        processData: false,
		success: function(result){
            var data = JSON.parse(result);

            if(data.result){ return false }

            getTrainerList();
            alertApp('O','삭제되었습니다.');
            return false;
		},
		error: function (e) {
			console.log(e);
            alertApp('X', '삭제에 실패하였습니다.');
            return false;
		}
    });
}

function DEL_TRAINER(sq){
    let formData = new FormData();
    formData.append('USER_SQ',sq);

    $.ajax({
        url: "flow_controller.php?task=",
        method: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(result){
            getTrainerList();
            alert('삭제되었습니다.');
        },
        error: function(e){
            console.log(e);
            alert('삭제에 실패하였습니다.');
            return false;
        }
    });
}

// 직급 순위 함수
function TRAINER_CATEGORY_RANK(sq, updown){
    let formData = new FormData();
    formData.append('WC_SEQ', sq);
    formData.append('UPDOWN', updown);

    $.ajax({
        url: "flow_controller.php?task=execWorkCategryRankChange",
        method: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(result){
            getTrainerList();
        },
        error: function(e){
            console.log(e);
            return false;
        }
    });
}

function REGISTER(sq){
    let formData = new FormData();
    formData.append('USER_SQ',sq);

    $.ajax({
        url: "flow_controller.php?task=execManagerRegister",
        method: "POST",
        data: formData,
		contentType: false,
        processData: false,
		success: function(result){
            var data = JSON.parse(result);
            
            if(data.result){
                return false;
            }
            
            getTrainerList();
		},
		error: function (e) {
			console.log(e);
		}
    });
}


// 상품관리
function GET_TICKET_DATA(){
    $.ajax({
        url: "flow_controller.php?task=getVoucherList",
        method: "POST",
		contentType: false,
        processData: false,
		success: function(result){
            var data = result.split('|');
            if(data.result)return false;
            TICKET_LIST = (JSON.parse(data[0]));
            TICKET_CATEGORY_LIST = (JSON.parse(data[1]));
            TICKET_SUB_CATEGORY_LIST = (JSON.parse(data[2]));
            
            SET.VOUCHER_TYPE = (JSON.parse(data[3]));
            SET.USE_TYPE = (JSON.parse(data[4]));
            SET.PERIOD_TYPE = (JSON.parse(data[5]));
            SET.PERIOD_UNIT = (JSON.parse(data[6]));
            SET.COUNT_TYPE = (JSON.parse(data[7]));
            SET.SURTAX_TYPE = (JSON.parse(data[8]));
            SET.DISCOUNT_TYPE = (JSON.parse(data[9]));

            // console.log(TICKET_LIST);
            // console.log(TICKET_CATEGORY_LIST);
            // console.log(TICKET_SUB_CATEGORY_LIST);

            // console.log('------옵션------');

            // console.log(SET.VOUCHER_TYPE);
            // console.log(SET.USE_TYPE);
            // console.log(SET.PERIOD_TYPE);
            // console.log(SET.PERIOD_UNIT);
            // console.log(SET.COUNT_TYPE);
            // console.log(SET.SURTAX_TYPE);
            // console.log(SET.DISCOUNT_TYPE);

            MAKE_TICKET_CATEGORY(TICKET_CATEGORY_LIST);
            MAKE_TICKET_SUB_CATEGORY(TICKET_SUB_CATEGORY_LIST);
            MAKE_TICKET_DATA(TICKET_LIST);
            MAKE_ADD_TICKET_OPTION();
            MAKE_TICKET_EVENT();
            
            // Default값;
            ADD_ITEM_CALC();
            ADD_TICKET_RESET();
            $('#itemFrm_service_1').click();
            $('#itemFrm_date').change();
            $('#itemFrm_count').change();
            $('#itemFrm_attr_1').click();
		},
		error: function (e) {
			console.log(e);
		}
    });
}

function ADD_TICKET_RESET(){
    $('#itemFrm_service_1').click();
    $('#itemFrm_name').val('');
    $('#itemFrm_type').val('');
    $('#itemFrm_category').val('');
    $('#itemFrm_attr_1').click();
    $('#itemFrm_date_write').val('');
    $('#itemFrm_date_write_month').val('1');
    $('#itemFrm_count_write').val('');
    $('#itemFrm_dayStop').val('');
    $('#itemFrm_weekStop').val('');
    $('#itemFrm_pay').val('');
    $('#itemFrm_sale').val('1');
    $('#itemFrm_sale_input').val('');
    $('#itemFrm_sale_amount').val('');
    $('#itemFrm_payment').val('');
    ADD_ITEM_CALC();
}

function MAKE_ADD_TICKET_OPTION(){
    // 초기화
    $('div.VOUCHER_TYPE').html('<label for="itemFrm_service" class="require">서비스 종류</label>');
    $('div.USE_TYPE').html('<label for="itemFrm_attr" class="require">상품 속성</label>');
    $('#itemFrm_date').empty();
    $('#itemFrm_count').empty();
    $('#itemFrm_sale').empty();
    $('#itemFrm_date_write_month').empty();

    // DOM생성
    for(let i in SET.VOUCHER_TYPE){
        $('div.VOUCHER_TYPE').append(
            '<input type="radio" name="itemFrm_service" id="itemFrm_service_' + SET.VOUCHER_TYPE[i].CODE + '">' +
            '<label for="itemFrm_service_' + SET.VOUCHER_TYPE[i].CODE + '" class="s">' + SET.VOUCHER_TYPE[i].DESCRIPTION + '</label>'
        );
    };
    for(let i in SET.USE_TYPE){
        $('div.USE_TYPE').append(
            '<input type="radio" name="itemFrm_attr" id="itemFrm_attr_' + SET.USE_TYPE[i].CODE + '">' +
            '<label for="itemFrm_attr_' + SET.USE_TYPE[i].CODE + '" class="s">' + SET.USE_TYPE[i].DESCRIPTION + '제</label>'
        );
    };
    for(let i in SET.PERIOD_TYPE){
        $('#itemFrm_date').append(
            '<option value="' + SET.PERIOD_TYPE[i].CODE + '">' + SET.PERIOD_TYPE[i].DESCRIPTION + '</option>'
        );
    }
    for(let i in SET.PERIOD_UNIT){
        $('#itemFrm_date_write_month').append(
            '<option value="' + SET.PERIOD_UNIT[i].CODE + '">' + SET.PERIOD_UNIT[i].DESCRIPTION + '</option>'
        );
    }
    for(let i in SET.COUNT_TYPE){
        $('#itemFrm_count').append(
            '<option value="' + SET.COUNT_TYPE[i].CODE + '">' + SET.COUNT_TYPE[i].DESCRIPTION + '</option>'
        );
    }
    for(let i in SET.DISCOUNT_TYPE){
        $('#itemFrm_sale').append(
            '<option value="' + SET.DISCOUNT_TYPE[i].CODE + '">' + SET.DISCOUNT_TYPE[i].DESCRIPTION + '</option>'
        );
    }


}

function MAKE_TICKET_DATA(list){
    var tag = '';
    $('ul#itemList').empty();
    for(let i = 0; i < list.length; i++){

        
        if((TICKET_CATEGORY_LIST.filter(e => e.CATEGORY_SQ == list[i].CATEGORY_SQ)).length != 0){
            var CATEGORY_NAME = (TICKET_CATEGORY_LIST.filter(e => e.CATEGORY_SQ == list[i].CATEGORY_SQ))[0].CATEGORY_NAME;
        }else{
            var CATEGORY_NAME = '카테고리 미지정';
        }

        if((TICKET_SUB_CATEGORY_LIST.filter(e => e.SUBCATEGORY_SQ == list[i].SUBCATEGORY_SQ)).length != 0){
            var SUB_CATEGORY_NAME = (TICKET_SUB_CATEGORY_LIST.filter(e => e.SUBCATEGORY_SQ == list[i].SUBCATEGORY_SQ))[0].SUBCATEGORY_NAME;
        }else{
            var SUB_CATEGORY_NAME = '서브카테고리 미지정';
        }
        let ATTR_NAME = (SET.USE_TYPE.filter(e => e.CODE == list[i].USE_TYPE))[0].DESCRIPTION;
        let temp = list[i].PERIOD_UNIT == '1' ? '일' : '개월';
        let DATE = list[i].PERIOD_TYPE == '1' ? '무제한' : list[i].PERIOD + temp;
        let COUNT = list[i].COUNT_TYPE == '1' ? '무제한' : list[i].COUNT + '회';
        let day = list[i].ENTERLIMIT_DAY == '0' ? '무제한' : list[i].ENTERLIMIT_DAY + '일';
        let week = list[i].ENTERLIMIT_WEEK == '0' ? '무제한' : list[i].ENTERLIMIT_WEEK + '일';
        let sale = list[i].DISCOUNT_AMOUNT != 0 ? '<s style="font-size:14px;">' + numberFormat(list[i].PRICE) + '원</s><span style="display:inline-block;font-weight:700;margin-left:10px;color:red">' + numberFormat(list[i].SELLINGPRICE) + '원</span>' : numberFormat(list[i].SELLINGPRICE) + '원';

        tag +=
        '<li data-item_seq="' + list[i].VOUCHER_SQ + '" data-type="' + list[i].CATEGORY_SQ + '" data-category="' + list[i].SUBCATEGORY_SQ + '">' +
            '<div class="top">' +
                '<small>' +
                    '이용횟수제한 : (일일 : <span class="itemDayStop">' + day + '</span> / ' + 
                    '주간 : <span class="itemWeekStop">' + week + '</span>)' +
                '</small>' +
                '<p class="itemType" style="color:' + CATEGORY_COLOR[list[i].CATEGORY_SQ-1] + '">' + CATEGORY_NAME + ' _ ' + SUB_CATEGORY_NAME + '</p>' +
                '<p>' + 
                    '<span class="itemName">' + list[i].VOUCHER_NAME + '</span>' +
                '</p>' +
                '<p>' + ATTR_NAME + '제' + ' <span>(' + DATE + ' / ' + COUNT + ')</span></p>' +
            '</div>' +
            '<div class="bottom">' + 
                '<span class="itemPay" data-pay="' + list[i].SELLINGPRICE + '">' + sale + '</span>' +
                '<div class="btn">' +
                    '<button class="set">수 정</button><button class="del">삭 제</button>' +
                '</div>' +
            '</div>'
        '</li>';
    }
    $('ul#itemList').append(tag);
}

function MAKE_TICKET_CATEGORY(list){
    var categoryTag = '';
    var setCategoryTag = '';
    var categoryOption = '';
    $('#itemFrm_type').html('<option value="">선택</option>');
    $('form[name="setCategoryFrm"] > ul').add($('#categoryList')).empty();
    
    for(let i = 0; i < list.length; i++){
        categoryTag += '<li data-seq="' + list[i].CATEGORY_SQ + '"><a href="#" style="color:' + CATEGORY_COLOR[i] + '">' + list[i].CATEGORY_NAME + '</a><ul></ul><i class="fas fa-plus" data-title="서브카테고리 추가" data-form="addCategorySmall"></i></li>';
        setCategoryTag += '<li data-seq="' + list[i].CATEGORY_SQ + '"><p>' + 
            '<label for="category' + list[i].CATEGORY_SQ + '" class="hid">' + list[i].CATEGORY_NAME + '</label>' + 
            '<input type="text" name="category' + list[i].CATEGORY_SQ + '" id="category' + list[i].CATEGORY_SQ + '" value="' + list[i].CATEGORY_NAME + '" required>' + 
            '<button type="button" class="item_mainCategory" data-seq="' + list[i].CATEGORY_SQ + '">삭제</button>' + 
            '</p><ul data-seq="' + list[i].CATEGORY_SQ + '"></ul></li>';
        categoryOption += '<option value="' + list[i].CATEGORY_SQ + '">' + list[i].CATEGORY_NAME + '</option>';
    }
    $('#categoryList').append(categoryTag);
    $('form[name="setCategoryFrm"] > ul').append(setCategoryTag);
    $('#itemFrm_type').append(categoryOption);
}

function MAKE_TICKET_SUB_CATEGORY(list){
    var subCategoryTag = '';
    var setSubCategoryTag = '';
    
    for(let i = 0; i < list.length; i++){
        subCategoryTag = '<li data-seq="' + list[i].SUBCATEGORY_SQ + '"><a href="#">' + list[i].SUBCATEGORY_NAME + '</a></li>';
        setSubCategoryTag = '<li data-seq="' + list[i].SUBCATEGORY_SQ + '">' + 
            '<label for="category_s' + list[i].SUBCATEGORY_SQ + '" class="hid">' + list[i].SUBCATEGORY_NAME + '</label>' + 
            '<input type="text" name="category_s' + list[i].SUBCATEGORY_SQ + '" id="category_s' + list[i].SUBCATEGORY_SQ + '" value="' + list[i].SUBCATEGORY_NAME + '" required>' + 
            '<button type="button"><i class="fas fa-minus"></i></button></li>';

        $('#categoryList > li[data-seq="' + list[i].CATEGORY_SQ + '"] > ul').append(subCategoryTag);
        $('form[name="setCategoryFrm"] > ul > li[data-seq="' + list[i].CATEGORY_SQ + '"] > ul').append(setSubCategoryTag);
    }
    
}

function SET_ITEM_CATEGORY(sq, value){
    let formData = new FormData();
    formData.append('CATEGORY_SQ',sq);
    formData.append('CATEGORY_NAME',value);

    $.ajax({
        url: "flow_controller.php?task=execCategoryModify",
        method: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(result){
            var data = JSON.parse(result);
            if(data.result){return false}
            GET_TICKET_DATA();
            return false;
        },
        error: function (e) {
            console.log(e);
        }
    });
}

function SET_ITEM_SUB_CATEGORY(sq, value){
    let formData = new FormData();
    formData.append('SUBCATEGORY_SQ',sq);
    formData.append('SUBCATEGORY_NAME',value);

    $.ajax({
        url: "flow_controller.php?task=execSubCategoryModify",
        method: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(result){
            var data = JSON.parse(result);
            if(data.result){return false}
            GET_TICKET_DATA();
            return false;
        },
        error: function (e) {
            console.log(e);
        }
    });
}

// 상품 수정
function SET_ITEM(){

}

function DEL_ITEM(formData){
    $.ajax({
        url: "flow_controller.php?task=execVoucherDelete",
        method: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(result){
            var data = JSON.parse(result);
            if(data.result){return false}
            GET_TICKET_DATA();
            $('.dark_div').add($('popup')).fadeOut(100);
            alert('해당 상품이 삭제되었습니다.');
            return false;
        },
        error: function (e) {
            console.log(e);
            alert('해당 상품 삭제를 실패하였습니다.');
        }
    });
}

function DEL_ITEM_CATEGORY(formData){
    $.ajax({
        url: "flow_controller.php?task=execCategoryDelete",
        method: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(result){
            var data = JSON.parse(result);
            if(data.result){return false}
            GET_TICKET_DATA();
            $('.dark_div').add($('.popup')).fadeOut(100);
            return false;
        },
        error: function (e) {
            console.log(e);
        }
    });
}

function DEL_ITEM_SUB_CATEGORY(formData){
    $.ajax({
        url: "flow_controller.php?task=execSubCategoryDelete",
        method: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(result){
            var data = JSON.parse(result);
            if(data.result){return false}
            GET_TICKET_DATA();
            $('.dark_div').add($('.popup')).fadeOut(100);
            return false;
        },
        error: function (e) {
            console.log(e);
        }
    });
}

function MAKE_TICKET_EVENT(){

    const delCategory = $('form.setCategory > ul > li > p > button');           // 카테고리 삭제
    const delCategory_s = $('form.setCategory > ul > li > ul > li > button');   // 카테고리 소메뉴 삭제
    const categoryList = $('#categoryList > li > a');                           // 카테고리 리스트
    const s_categoryList = categoryList.parent().find('ul > li > a')            // 카테고리 소메뉴 리스트
    const itemSetBtn = $('#itemList button.set');
    const itemDelBtn = $('#itemList button.del');


    // 서브 카테고리 생성모달 켜기
    $('#categoryList > li >i').click(function(e){
        e.preventDefault();
        $('.popup').find('.title > span').text($(this).attr('data-title'));
        $('.popup').fadeIn(200);
        $('.popup').find('form').hide();
        $('.popup').find('form[name="addCategorySmallFrm"]').attr('data-parent_seq',$(this).parent().attr('data-seq'));
        $('.popup').find('form[name="addCategorySmallFrm"]').show();
    });

    $('nav.category').find('a').not('.add').click(function(e){
        e.preventDefault();
        $('nav.category').find('a').parent().removeClass('active');
        $(this).parent().addClass('active');
    });

    $('#itemFrm_type').change(function(){
        var val = $(this).val();
        var list = TICKET_SUB_CATEGORY_LIST.filter(e => e.CATEGORY_SQ == val);
        $('#itemFrm_category').html('<option value="">선택</option>');
        for(let i in list){
            $('#itemFrm_category').append('<option value="' + list[i].SUBCATEGORY_SQ + '">' + list[i].SUBCATEGORY_NAME + '</option>')
        }
    });


    categoryList.click(function(e){         // a태그
        var that = $(this).parent();        // li태그
        var seq = that.attr('data-seq');    // Type Seq
        
        $('#itemList > li').hide();
        $('#itemList > li[data-type="' + seq + '"]').show();

    });
    s_categoryList.not('.add').click(function(){
        var that = $(this).parent();
        var seq = that.attr('data-seq');

        $('#itemList > li').hide();
        $('#itemList > li[data-category="' + seq + '"]').show();
    });
    // 전체보기
    $('.cateAllView').click(function(){
        $('#itemList > li').hide();
        $('#itemList > li').show();
    });


    // 카테고리 삭제
    delCategory.click(function(){
        var seq = $(this).parent().parent('li').attr('data-seq');
        var ask = confirm('해당 카테고리를 삭제하면 하위 카테고리와 카테고리에 속해있는 \n상품이 모두 삭제됩니다. 삭제하시겠습니까?');
        if(ask){
            let formData = new FormData();
            let CATEGORY_SQ = $(this).attr('data-seq');
            formData.append('CATEGORY_SQ',CATEGORY_SQ)
            DEL_ITEM_CATEGORY(formData);
            return false;
        }else{
            return false;
        }
    });
    delCategory_s.click(function(){
        var seq = $(this).parent('li').attr('data-seq');
        var ask = confirm('해당 카테고리를 삭제하면 카테고리에 속해있는 상품이 \n모두 삭제됩니다. 삭제하시겠습니까?');
        if(ask){
            let formData = new FormData();
            let SUBCATEGORY_SQ = $(this).parent().attr('data-seq');
            formData.append('SUBCATEGORY_SQ',SUBCATEGORY_SQ)
            DEL_ITEM_SUB_CATEGORY(formData);
            return false;
        }else{
            return false;
        }
    });


    // 상품 속성 선택
    $('[name="itemFrm_attr"]').click(function(){
        var sq = $(this).attr('id').split('_')[2];
        
        if(sq == '1'){
            $('#itemFrm_date').val('2').prop('disabled',true);
            $('#itemFrm_count').val('1').prop('disabled',true);
        }else{
            $('#itemFrm_date').val('2').prop('disabled',true);
            $('#itemFrm_count').val('2').prop('disabled',true);
        }
        $('#itemFrm_date').add($('#itemFrm_count')).change();
    });

    // 기간/횟수 선택
    $('#itemFrm_date').add($('#itemFrm_count')).change(function(){
        var val = $(this).val();
        switch(val){
            case '1': $(this).siblings('p').hide().find('input,select').prop('disabled',true); break;
            case '2': $(this).siblings('p').show().find('input,select').prop('disabled',false); break;
        }
    });

    // 할인 선택
    $('#itemFrm_sale').change(function(){
        $('#itemFrm_sale_input').val('');
        var val = $(this).val();
        switch(val){
            case '1': $(this).siblings('label[for="itemFrm_sale_input"]').text('%'); break;
            case '2': $(this).siblings('label[for="itemFrm_sale_input"]').text('원'); break;
        }
    });

    // 판매정가 / 할인 / 할인금액 / 판매가 입력 및 계산함수
    $('#itemFrm_pay').keyup(() => ADD_ITEM_CALC());
    $('#itemFrm_tax').click(() => ADD_ITEM_CALC());
    $('#itemFrm_sale').change(() => {
        $('#itemFrm_payment').val($('#itemFrm_pay').val());
        ADD_ITEM_CALC();
    });
    $('#itemFrm_sale_input').keyup(function(){
        var type = $('#itemFrm_sale').val() == '1' ? true : false;
        var val = Number($(this).val());
        if(type){
            if(val >= 100){
                $(this).val(100);
            }
        }else{
            if(val >= $('#itemFrm_pay').val()){
                $(this).val($('#itemFrm_pay').val());
            }
        }
        ADD_ITEM_CALC();
    });


    // 상품 삭제
    $('article#itemSet ul#itemList > li > div.bottom button.del').click(function(){

        var ask = confirm('해당 상품을 삭제하시겠습니까?');
        if(ask){
            let formData = new FormData();
            let VOUCHER_SQ = $(this).parents('li').attr('data-item_seq');
            formData.append('VOUCHER_SQ',VOUCHER_SQ);
            DEL_ITEM(formData);
        }else{
            return false;
        }
    });

    

    // 카테고리 편집
    $('#set_item_category_submit_btn').click(function(e){
        e.preventDefault();
        var checked = false;
        $('div.popup > .setCategory input').each(function(){
            if( $(this).val() == '' ){
                $(this).focus();
                alertApp('!', '카테고리명을 입력해주세요.');
                checked = false;
                return false;
            }
            checked = true;
        });
        if(checked){
            $('div.popup > .setCategory input').each(function(){
                var attr = $(this).attr('id');
                var value = $(this).val();
                var sq = '';
                if(attr.indexOf('_s') > -1){
                    sq = $(this).parent().attr('data-seq');
                    SET_ITEM_SUB_CATEGORY(sq, value);
                }else{
                    sq = $(this).parent().parent().attr('data-seq');
                    SET_ITEM_CATEGORY(sq, value);
                }
                
            });
            alertApp('O', '카테고리 편집이 완료되었습니다.');
            $('.dark_div').add($('.popup')).fadeOut(200);
            return false;
        }
    });

    // 상품 편집
    $('article#itemSet ul#itemList > li > div.bottom button.set').click(function(){
        SELECTED_SET_ITEM = '';
        $('#addItem').click();
        $('#add_item_submit_btn').removeClass('add');
        $('.popup > h2 > span').text('상품 수정');
        $('.popup #add_item_submit_btn').text('수정');
        
        var sq = $(this).parent().parent().parent().attr('data-item_seq');
        var obj = (TICKET_LIST.filter(e => e.VOUCHER_SQ == sq))[0];
        SELECTED_SET_ITEM = sq;
        obj.VOUCHER_TYPE == 1 ? $('#itemFrm_service_1').click() : obj.VOUCHER_TYPE == 2 ? $('#itemFrm_service_2').click() : $('#itemFrm_service_3').click();
        $('#itemFrm_name').val(obj.VOUCHER_NAME);
        $('#itemFrm_type').val(obj.CATEGORY_SQ);    // 카테고리 value 삽입

        var list = TICKET_SUB_CATEGORY_LIST.filter(e => e.CATEGORY_SQ == obj.CATEGORY_SQ);
        $('#itemFrm_category').html('<option value="">선택</option>');
        for(let i in list){
            if(list[i].SUBCATEGORY_SQ == obj.SUBCATEGORY_SQ){
                $('#itemFrm_category').append('<option selected value="' + list[i].SUBCATEGORY_SQ + '">' + list[i].SUBCATEGORY_NAME + '</option>')
            }else{
                $('#itemFrm_category').append('<option value="' + list[i].SUBCATEGORY_SQ + '">' + list[i].SUBCATEGORY_NAME + '</option>')
            }
        }

        obj.USE_TYPE == 1 ? $('#itemFrm_attr_1').click() : $('#itemFrm_attr_2').click();
        $('#itemFrm_date').val(obj.PERIOD_TYPE);
        $('#itemFrm_date_write').val(obj.PERIOD);
        $('#itemFrm_date_write_month').val(obj.PERIOD_UNIT);
        $('#itemFrm_count').val(obj.COUNT_TYPE);
        $('#itemFrm_count_write').val(obj.COUNT);
        $('#itemFrm_dayStop').val(obj.ENTERLIMIT_DAY);
        $('#itemFrm_weekStop').val(obj.ENTERLIMIT_WEEK);
        $('#itemFrm_pay').val(obj.PRICE);
        $('#itemFrm_sale').val(obj.DISCOUNT_TYPE);
        $('#itemFrm_sale_input').val(obj.DISCOUNT_RATIO);
        ADD_ITEM_CALC();

    });

}

// 계산함수
function ADD_ITEM_CALC(){
    var type = $('#itemFrm_sale').val();
    var tax = $('#itemFrm_tax').prop('checked') ? true : false;
    var sale = $('#itemFrm_sale_input').val() != '' ? Number($('#itemFrm_sale_input').val()) : 0;
    var pay = $('#itemFrm_pay').val() != '' ? Number($('#itemFrm_pay').val()) : 0;
    
    switch (type) {
        case '1':
            var saleAmount = parseInt(pay * (sale / 100) / 100) * 100;
            break;
            
        case '2':
            var saleAmount = sale;
            break;
    }
    var result = pay - saleAmount;
    PAYMENT_ITEM = result;
    $('#itemFrm_sale_amount').val(numberFormat(saleAmount));
    $('#itemFrm_payment').val(numberFormat(result));
}

//////////////////////////////////////////////////////////////////////////////////////////
// 예약관리
function GET_TICKETING_DATA(){
    $.ajax({
        url: "flow_controller.php?task=getCurrentReservationSetting",
        method: "POST",
		contentType: false,
        processData: false,
		success: function(result){
            var data = (JSON.parse(result))[0];
            if(data.result){
                return false;
            }
            MAKE_TICKETING_SETTING(data);
		},
		error: function (e) {
			console.log(e);
		}
    });
}

function MAKE_TICKETING_SETTING(obj){
    $('#solo_ticketingPossibleTime_Set-input, #solo_ticketingPossibleTime_Set-select1, #solo_ticketingPossibleTime_Set-select2').val('00');
    if(obj.PSN_RESERV_TYPE == '0'){
        $('#solo_ticketingPossibleTime_Yes').click();
    }else{
        $('#solo_ticketingPossibleTime_Set').click();
    }
    
    var Aminutes = parseInt(obj.PSN_RESERV_TIME) % 60;
    var Atotalhour = parseInt(parseInt(obj.PSN_RESERV_TIME) / 60);
    var Ahour = Atotalhour % 24;
    var Aday = parseInt(Atotalhour/24);

    $('#solo_ticketingPossibleTime_Set-input').val(Aday);
    $('#solo_ticketingPossibleTime_Set-select1').val(Ahour);
    $('#solo_ticketingPossibleTime_Set-select2').val(Aminutes);

    if(obj.PSN_MOD_TYPE == '0'){
        $('#solo_ticketingChangeTime_1').click();
    }else if(obj.PSN_MOD_TYPE == '1'){
        $('#solo_ticketingChangeTime_2').click();
    }else if(obj.PSN_MOD_TYPE == '2'){
        $('#solo_ticketingChangeTime_3').click();
    }else if(obj.PSN_MOD_TYPE == '3'){
        $('#solo_ticketingChangeTime_4').click();
    }
    
    var Bminutes = parseInt(obj.PSN_MOD_TIME) % 60;
    var Btotalhour = parseInt(parseInt(obj.PSN_MOD_TIME) / 60);
    var Bhour = Btotalhour % 24;
    var Bday = parseInt(Btotalhour/24);

    $('#solo_ticketingChangeTime_4-input').val(Bday);
    $('#solo_ticketingChangeTime_4-select1').val(Bhour);
    $('#solo_ticketingChangeTime_4-select2').val(Bminutes);
    
    if(obj.PSN_AUTO_ABSENCE == '0'){
        $('#solo_endClassAuto_No').click();
    }else{
        $('#solo_endClassAuto_Yes').click();
    }

    if(obj.PSN_ABSENCE_TICKET == '0'){
        $('#solo_endClassTicketMinus_Yes').click();
    }else{
        $('#solo_endClassTicketMinus_No').click();
    }


    //group
    if(obj.GRP_RESERV_TYPE == '0'){
        $('#group_ticketingPossibleTime_Yes').click();
    }else{
        $('#group_ticketingPossibleTime_Set').click();
    }

    var Cminutes = parseInt(obj.GRP_RESERV_TIME) % 60;
    var Ctotalhour = parseInt(parseInt(obj.GRP_RESERV_TIME) / 60);
    var Chour = Ctotalhour % 24;
    var Cday = parseInt(Ctotalhour/24);

    $('#group_ticketingPossibleTime_Set-input').val(Cday);
    $('#group_ticketingPossibleTime_Set-select1').val(Chour);
    $('#group_ticketingPossibleTime_Set-select2').val(Cminutes);

    if(obj.GRP_MOD_TYPE == '0'){
        $('#group_ticketingChangeTime_1').click();
    }else if(obj.GRP_MOD_TYPE == '1'){
        $('#group_ticketingChangeTime_2').click();
    }else if(obj.GRP_MOD_TYPE == '2'){
        $('#group_ticketingChangeTime_3').click();
    }else if(obj.GRP_MOD_TYPE == '3'){
        $('#group_ticketingChangeTime_4').click();
    }

    var Dminutes = parseInt(obj.GRP_MOD_TIME) % 60;
    var Dtotalhour = parseInt(parseInt(obj.GRP_MOD_TIME) / 60);
    var Dhour = Dtotalhour % 24;
    var Dday = parseInt(Dtotalhour/24);

    $('#group_ticketingChangeTime_4-input').val(Cday);
    $('#group_ticketingChangeTime_4-select1').val(Chour);
    $('#group_ticketingChangeTime_4-select2').val(Cminutes);

    if(obj.GRP_AUTO_ABSENCE == '0'){
        $('#group_endClassAuto_No').click();
    }else{
        $('#group_endClassAuto_Yes').click();
    }

    if(obj.GRP_ABSENCE_TICKET == '0'){
        $('#group_endClassTicketMinus_Yes').click();
    }else{
        $('#group_endClassTicketMinus_No').click();
    }
}

function SEND_TICKETING_SETTING(){

    var formData = new FormData();
    var PSN_RESERV_TYPE = $('#solo_ticketingPossibleTime_Yes').prop('checked') ? '0' : '1';
    var PSN_RESERV_TIME = ($('#solo_ticketingPossibleTime_Set-input').val() * 1440) + ($('#solo_ticketingPossibleTime_Set-select1').val() * 60) + Number($('#solo_ticketingPossibleTime_Set-select2').val());
    var PSN_MOD_TYPE = $('#solo_ticketingChangeTime_1').prop('checked') ? '0' : $('#solo_ticketingChangeTime_2').prop('checked') ? '1' : $('#solo_ticketingChangeTime_3').prop('checked') ? '2' : $('#solo_ticketingChangeTime_4').prop('checked') ? '3' : '';
    var PSN_MOD_TIME = ($('#solo_ticketingChangeTime_4-input').val() * 1440) + ($('#solo_ticketingChangeTime_4-select1').val() * 60) + Number($('#solo_ticketingChangeTime_4-select2').val());
    var PSN_AUTO_ABSENCE = $('#solo_endClassAuto_No').prop('checked') ? '0' : '1';
    var PSN_ABSENCE_TICKET = $('#solo_endClassTicketMinus_Yes').prop('checked') ? '0' : '1';
    var GRP_RESERV_TYPE = $('#group_ticketingPossibleTime_Yes').prop('checked') ? '0' : '1';
    var GRP_RESERV_TIME = ($('#group_ticketingPossibleTime_Set-input').val() * 1440) + ($('#group_ticketingPossibleTime_Set-select1').val() * 60) + Number($('#group_ticketingPossibleTime_Set-select2').val());
    var GRP_MOD_TYPE = $('#group_ticketingChangeTime_1').prop('checked') ? '0' : $('#group_ticketingChangeTime_2').prop('checked') ? '1' : $('#group_ticketingChangeTime_3').prop('checked') ? '2' : $('#group_ticketingChangeTime_4').prop('checked') ? '3' : '';
    var GRP_MOD_TIME = ($('#group_ticketingChangeTime_4-input').val() * 1440) + ($('#group_ticketingChangeTime_4-select1').val() * 60) + Number($('#group_ticketingChangeTime_4-select2').val());;
    var GRP_AUTO_ABSENCE = $('#group_endClassAuto_No').prop('checked') ? '0' : '1';
    var GRP_ABSENCE_TICKET = $('#group_endClassTicketMinus_Yes').prop('checked') ? '0' : '1';
    
    formData.append('PSN_RESERV_TYPE',PSN_RESERV_TYPE);
    formData.append('PSN_RESERV_TIME',PSN_RESERV_TIME);
    formData.append('PSN_MOD_TYPE',PSN_MOD_TYPE);
    formData.append('PSN_MOD_TIME',PSN_MOD_TIME);
    formData.append('PSN_AUTO_ABSENCE',PSN_AUTO_ABSENCE);
    formData.append('PSN_ABSENCE_TICKET',PSN_ABSENCE_TICKET);
    formData.append('GRP_RESERV_TYPE',GRP_RESERV_TYPE);
    formData.append('GRP_RESERV_TIME',GRP_RESERV_TIME);
    formData.append('GRP_MOD_TYPE',GRP_MOD_TYPE);
    formData.append('GRP_MOD_TIME',GRP_MOD_TIME);
    formData.append('GRP_AUTO_ABSENCE',GRP_AUTO_ABSENCE);
    formData.append('GRP_ABSENCE_TICKET',GRP_ABSENCE_TICKET);
    
    $.ajax({
        url: "flow_controller.php?task=execReservationSettingSave",
        method: "POST",
        data: formData,
		contentType: false,
        processData: false,
		success: function(result){
            var data = (JSON.parse(result))[0];
            
            if(data.result){return false}
            MAKE_TICKETING_SETTING(data);
            $('body > div#wrap > section.content').animate({'scrollTop' : 0},100);
            alert('저장되었습니다.');
            return false;
		},
		error: function (e) {
			console.log(e);
		}
    });
}


//////////////////////////////////////////////////////////////////////////////////////////
// 센터관리
function GET_CENTER_DATA(){
    $.ajax({
        url: "flow_controller.php?task=getCenterInfo",
        method: "POST",
		contentType: false,
        processData: false,
		success: function(result){
            var data = result.split('|');
            if(data.result){return false}
            CENTERINFO = (JSON.parse(data[0]));
            COMPANYINFO = (JSON.parse(data[1]));
            OPERATIONGINFO = (JSON.parse(data[2]));
            HOLIDAY_LIST = (JSON.parse(data[3]));
            ROOM_LIST = (JSON.parse(data[4]));
            $('body > div#wrap > section.content').animate({'scrollTop' : 0},100);
            
            // console.log(CENTERINFO);
            MAKE_COMPANY_DATA(COMPANYINFO[0]);
            MAKE_CENTER_DATA(CENTERINFO[0])
            MAKE_OPERATING_INFO(OPERATIONGINFO[0]);
            MAKE_HOLIDAY_LIST(HOLIDAY_LIST);
            MAKE_ROOM_LIST(ROOM_LIST);
		},
		error: function (e) {
			console.log(e);
		}
    });
}


function MAKE_COMPANY_DATA(obj){
    $('[for="companyName"] + p').text(obj.COMPANY_NAME);
    $('[for="companyNum"] + p').text(obj.COMPANY_REGNO);
    $('[for="ceoName"] + p').text(obj.COMPANY_CEONAME);
    $('[for="companyAttr"] + p').text(obj.COMPANY_TYPE == '0' ? '법인 사업자' : '개인 사업자');
    $('[for="companyType"] + p').text(obj.COMPANY_CONTIDION);
    $('[for="companyAddress"] + p').text(obj.ADDRESS_PROVINCE + ' ' + obj.ADDRESS_CITY + ' ' + obj.ADDRESS_DETAIL + ' ' + obj.COMPANY_NAME);


}

function MAKE_CENTER_DATA(obj) {

    $('.centerProfileImage').attr('src', obj.CENTER_IMAGE_FILE == null || obj.CENTER_IMAGE_FILE == '' ? 'img/no_img.png' : obj.CENTER_IMAGE_FILE);
    $('#centerManager').val(obj.MANAGER_NAME);
    $('#centerName').val(obj.CENTER_NM);
    $('#centerPhone').val(obj.CENTER_PHONE);
    $('#centerPax').val(obj.CENTER_FAX);
    $('#centerPage').val(obj.CENTER_HOMEPAGE);
    $('#centerSns').val(obj.CENTER_SNS);
    $('#centerCome').val(obj.CENTER_VISIT_DETAIL);
    $('#centerEx').val(obj.CENTER_DESCRIPTION);
}


function MAKE_ROOM_LIST(list){

    var tag = '';
    $('.centerRoom #RoomList').empty();

    for(let i of list){
        tag = 
            '<li>\
                <input type="text" data-sq="' + i.ROOM_SQ + '" name="room' + i.ROOM_SQ + '" id="room' + i.ROOM_SQ + '" value="' + i.ROOM_NAME + '">\
                <button type="button" data-sq="' + i.ROOM_SQ + '" class="removeRoomBtn">삭제</button>\
            </li>';
        $('.centerRoom #RoomList').append(tag);
    }

    $('article#centerSet .centerRoom #RoomList > li > input').keydown(function(e){
        if(e.keyCode == 13){
            e.preventDefault();
        }
    });

    $('article#centerSet .centerRoom #RoomList > li > input').blur(function(){
        var sq = $(this).attr('data-sq');
        var val = $(this).val();
        var disc = '';

        let formData = new FormData();
            formData.append('ROOM_SQ', sq);
            formData.append('ROOM_NAME', val);
            formData.append('ROOM_DESC', disc);

        $.ajax({
            url: "flow_controller.php?task=execRoomModify",
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(result){
                var data = JSON.parse(JSON.parse(result));
                if(data.result == 'Fail'){return false}
                alertApp('O', '룸 정보가 수정 되었습니다.');
                MAKE_ROOM_LIST(data);
                return false;
            },
            error: function (e) {
                console.log(e);
                alertApp('X', '룸 정보가 수정되지 않았습니다.');
                return false;
            }
        });
    });

    $('article#centerSet .centerRoom #RoomList > li > button').click(function(){

        var ask = confirm('해당 룸을 삭제하시겠습니까?');
        if (ask) {

            var sq = $(this).attr('data-sq');
            
            let formData = new FormData();
            formData.append('ROOM_SQ', sq);
            
            $.ajax({
                url: "flow_controller.php?task=execRoomDelete",
                method: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(result){
                    var data = JSON.parse(JSON.parse(result));
                    if(data.result == 'Fail'){return false}
                    MAKE_ROOM_LIST(data);
                    alertApp('O', '룸 정보가 삭제되었습니다.');
                    return false;
                },
                error: function (e) {
                    console.log(e);
                    alertApp('X', '룸 정보가 수정되지 않았습니다.');
                    return false;
                }
            });
        }
    });


}

function MAKE_OPERATING_INFO(obj){
    var TIME = {
        MON: obj.MON_OPERTIME.split(':'),
        TUE: obj.TUE_OPERTIME.split(':'),
        WED: obj.WED_OPERTIME.split(':'),
        THU: obj.THU_OPERTIME.split(':'),
        FRI: obj.FRI_OPERTIME.split(':'),
        SAT: obj.SAT_OPERTIME.split(':'),
        SUN: obj.SUN_OPERTIME.split(':'),
    }


    obj.MON_OPER_TYPE == '0' ? $('#TurnTime_Mo_no').click() : $('#TurnTime_Mo_yes').click();
    obj.TUE_OPER_TYPE == '0' ? $('#TurnTime_Tu_no').click() : $('#TurnTime_Tu_yes').click();
    obj.WED_OPER_TYPE == '0' ? $('#TurnTime_We_no').click() : $('#TurnTime_We_yes').click();
    obj.THU_OPER_TYPE == '0' ? $('#TurnTime_Th_no').click() : $('#TurnTime_Th_yes').click();
    obj.FRI_OPER_TYPE == '0' ? $('#TurnTime_Fr_no').click() : $('#TurnTime_Fr_yes').click();
    obj.SAT_OPER_TYPE == '0' ? $('#TurnTime_Sa_no').click() : $('#TurnTime_Sa_yes').click();
    obj.SUN_OPER_TYPE == '0' ? $('#TurnTime_Su_no').click() : $('#TurnTime_Su_yes').click();

    $('#TurnTime_Mo_yes_h1').val(Number(TIME.MON[0]) != null ? Number(TIME.MON[0]) : '0');
    $('#TurnTime_Mo_yes_m1').val(Number(TIME.MON[1]) != null ? Number(TIME.MON[1]) : '0');
    $('#TurnTime_Mo_yes_h2').val(Number(TIME.MON[2]) != null ? Number(TIME.MON[2]) : '0');
    $('#TurnTime_Mo_yes_m2').val(Number(TIME.MON[3]) != null ? Number(TIME.MON[3]) : '0');

    $('#TurnTime_Tu_yes_h1').val(Number(TIME.TUE[0]) != null ? Number(TIME.TUE[0]) : '0');
    $('#TurnTime_Tu_yes_m1').val(Number(TIME.TUE[1]) != null ? Number(TIME.TUE[1]) : '0');
    $('#TurnTime_Tu_yes_h2').val(Number(TIME.TUE[2]) != null ? Number(TIME.TUE[2]) : '0');
    $('#TurnTime_Tu_yes_m2').val(Number(TIME.TUE[3]) != null ? Number(TIME.TUE[3]) : '0');
    
    $('#TurnTime_We_yes_h1').val(Number(TIME.WED[0]) != null ? Number(TIME.WED[0]) : '0');
    $('#TurnTime_We_yes_m1').val(Number(TIME.WED[1]) != null ? Number(TIME.WED[1]) : '0');
    $('#TurnTime_We_yes_h2').val(Number(TIME.WED[2]) != null ? Number(TIME.WED[2]) : '0');
    $('#TurnTime_We_yes_m2').val(Number(TIME.WED[3]) != null ? Number(TIME.WED[3]) : '0');

    $('#TurnTime_Th_yes_h1').val(Number(TIME.THU[0]) != null ? Number(TIME.THU[0]) : '0');
    $('#TurnTime_Th_yes_m1').val(Number(TIME.THU[1]) != null ? Number(TIME.THU[1]) : '0');
    $('#TurnTime_Th_yes_h2').val(Number(TIME.THU[2]) != null ? Number(TIME.THU[2]) : '0');
    $('#TurnTime_Th_yes_m2').val(Number(TIME.THU[3]) != null ? Number(TIME.THU[3]) : '0');

    $('#TurnTime_Fr_yes_h1').val(Number(TIME.FRI[0]) != null ? Number(TIME.FRI[0]) : '0');
    $('#TurnTime_Fr_yes_m1').val(Number(TIME.FRI[1]) != null ? Number(TIME.FRI[1]) : '0');
    $('#TurnTime_Fr_yes_h2').val(Number(TIME.FRI[2]) != null ? Number(TIME.FRI[2]) : '0');
    $('#TurnTime_Fr_yes_m2').val(Number(TIME.FRI[3]) != null ? Number(TIME.FRI[3]) : '0');

    $('#TurnTime_Sa_yes_h1').val(Number(TIME.SAT[0]) != null ? Number(TIME.SAT[0]) : '0');
    $('#TurnTime_Sa_yes_m1').val(Number(TIME.SAT[1]) != null ? Number(TIME.SAT[1]) : '0');
    $('#TurnTime_Sa_yes_h2').val(Number(TIME.SAT[2]) != null ? Number(TIME.SAT[2]) : '0');
    $('#TurnTime_Sa_yes_m2').val(Number(TIME.SAT[3]) != null ? Number(TIME.SAT[3]) : '0');

    $('#TurnTime_Su_yes_h1').val(Number(TIME.SUN[0]) != null ? Number(TIME.SUN[0]) : '0');
    $('#TurnTime_Su_yes_m1').val(Number(TIME.SUN[1]) != null ? Number(TIME.SUN[1]) : '0');
    $('#TurnTime_Su_yes_h2').val(Number(TIME.SUN[2]) != null ? Number(TIME.SUN[2]) : '0');
    $('#TurnTime_Su_yes_m2').val(Number(TIME.SUN[3]) != null ? Number(TIME.SUN[3]) : '0');

}

function SET_COMPANY_DATA(){

    var formData = new FormData();
    formData.append('CENTER_NM',$('#centerName').val());
    formData.append('MANAGER_NAME',$('#centerManager').val());
    formData.append('MANAGER_PHONE',$('#centerPhone').val());
    formData.append('MANAGER_EMAIL','');
    formData.append('CENTER_PHONE',$('#centerPhone').val());
    formData.append('CENTER_FAX',$('#centerPax').val());
    formData.append('CENTER_HOMEPAGE',$('#centerPage').val());
    formData.append('CENTER_SNS',$('#centerSns').val());
    formData.append('CENTER_VISIT_DETAIL',$('#centerCome').val());
    formData.append('CENTER_IMAGE_FILE',$('#centerImage').prop('files')[0]);
    formData.append('CENTER_DESCRIPTION',$('#centerEx').val());

    $.ajax({
        url: "flow_controller.php?task=execCenterInfoModify",
        method: "POST",
        data: formData,
		contentType: false,
        processData: false,
		success: function(result){
            var data = JSON.parse(result);
            if(data.result == 'Fail'){
                alertApp('!', '변경사항이 없습니다.');
                return false;
            }
            $('body > div#wrap > section.content').animate({'scrollTop' : 0},100);
            alertApp('O', '저장되었습니다.');

		},
		error: function (e) {
			console.log(e);
		}
    });

}
function SET_OPERATING_INFO(){
    var Mo = $('#TurnTime_Mo_yes_h1').val() + ':' + $('#TurnTime_Mo_yes_m1').val() + ':' + $('#TurnTime_Mo_yes_h2').val() + ':' + $('#TurnTime_Mo_yes_m2').val();
    var Tu = $('#TurnTime_Tu_yes_h1').val() + ':' + $('#TurnTime_Tu_yes_m1').val() + ':' + $('#TurnTime_Tu_yes_h2').val() + ':' + $('#TurnTime_Tu_yes_m2').val();
    var We = $('#TurnTime_We_yes_h1').val() + ':' + $('#TurnTime_We_yes_m1').val() + ':' + $('#TurnTime_We_yes_h2').val() + ':' + $('#TurnTime_We_yes_m2').val();
    var Th = $('#TurnTime_Th_yes_h1').val() + ':' + $('#TurnTime_Th_yes_m1').val() + ':' + $('#TurnTime_Th_yes_h2').val() + ':' + $('#TurnTime_Th_yes_m2').val();
    var Fr = $('#TurnTime_Fr_yes_h1').val() + ':' + $('#TurnTime_Fr_yes_m1').val() + ':' + $('#TurnTime_Fr_yes_h2').val() + ':' + $('#TurnTime_Fr_yes_m2').val();
    var Sa = $('#TurnTime_Sa_yes_h1').val() + ':' + $('#TurnTime_Sa_yes_m1').val() + ':' + $('#TurnTime_Sa_yes_h2').val() + ':' + $('#TurnTime_Sa_yes_m2').val();
    var Su = $('#TurnTime_Su_yes_h1').val() + ':' + $('#TurnTime_Su_yes_m1').val() + ':' + $('#TurnTime_Su_yes_h2').val() + ':' + $('#TurnTime_Su_yes_m2').val();



    var formData = new FormData();
    formData.append('MON_OPERTIME', Mo);
    formData.append('TUE_OPERTIME', Tu);
    formData.append('WED_OPERTIME', We);
    formData.append('THU_OPERTIME', Th);
    formData.append('FRI_OPERTIME', Fr);
    formData.append('SAT_OPERTIME', Sa);
    formData.append('SUN_OPERTIME', Su);
    formData.append('MON_OPER_TYPE', $('#TurnTime_Mo_no').prop('checked') ? '0' : '1');
    formData.append('TUE_OPER_TYPE', $('#TurnTime_Tu_no').prop('checked') ? '0' : '1');
    formData.append('WED_OPER_TYPE', $('#TurnTime_We_no').prop('checked') ? '0' : '1');
    formData.append('THU_OPER_TYPE', $('#TurnTime_Th_no').prop('checked') ? '0' : '1');
    formData.append('FRI_OPER_TYPE', $('#TurnTime_Fr_no').prop('checked') ? '0' : '1');
    formData.append('SAT_OPER_TYPE', $('#TurnTime_Sa_no').prop('checked') ? '0' : '1');
    formData.append('SUN_OPER_TYPE', $('#TurnTime_Su_no').prop('checked') ? '0' : '1');


    $.ajax({
        url: "flow_controller.php?task=execCenterOperTimeModify",
        method: "POST",
        data: formData,
		contentType: false,
        processData: false,
		success: function(result){
            var data = JSON.parse(result);
            if(data.result == 'Fail'){
                return false;
            }
            $('body > div#wrap > section.content').animate({'scrollTop' : 0},100);
            MAKE_OPERATING_INFO(data[0]);
            alertApp('O', '저장되었습니다.');
		}
    });
}

function MAKE_HOLIDAY_LIST(list){
    $('#calendar td > a.holiday').removeClass('holiday');

    for(let i in list){
        var date = list[i].HOLIDAY.split('-');
        $('#calendar td').each(function(){
            if($(this).attr('data-year') == date[0] && $(this).attr('data-month') == Number(date[1]) - 1 && $(this).attr('data-handler') == 'selectDay'){
                $('#calendar td[data-handler="selectDay"]').eq(Number(date[2])-1).find('a').addClass('holiday');
            }
        });
    }
}

//////////////////////////////////////////////////////////////////////////////////////////
// 공지관리
function GET_NOTICE_DATA(){
    $.ajax({
        url: "flow_controller.php?task=getNoticeList",
        method: "POST",
		    contentType: false,
        processData: false,
        success: function(result){
          var data = JSON.parse(result);
          if(data.result == 'Fail'){
              return false;
          }
          NOTICE_LIST = data;
          MAKE_NOTICE_LIST(data);
          $('.notice > h3 > button.active').click();
		},
		error: function (e) {
			console.log(e);
		}
    });
}

function MAKE_NOTICE_LIST(list){
    $('#noticeList > tbody').empty();
    var tag = '';
    for(let i in list){
        tag +=
        '<tr data-sq="' + list[i].NOTICE_SQ + '"><td><input type="checkbox" name="' + list[i].NOTICE_SQ + '" id="' + list[i].NOTICE_SQ + '"><label for="' + list[i].NOTICE_SQ + '">체크</label></td>' + 
        '<td>' + (i+1) + '</td>' +
        '<td>' + (list[i].NOTICE_TYPE == '1' ? '전체공지' : '직원공지') + '</td>' +
        '<td class="text">' + list[i].NOTICE_TITLE + '</td>' +
        '<td class="text">' + list[i].NOTICE_CONTENTS + '</td>' +
        '<td>' + ((TRAINER_LIST.filter(e => e.USER_SQ == list[i].CREATEDBY))[0] == undefined ? '정보없음' : (TRAINER_LIST.filter(e => e.USER_SQ == list[i].CREATEDBY))[0].USER_NM) + '</td>' +
        '<td>' + list[i].CREATEDDT + '</td></tr>'
    }
    $('#noticeList > tbody').append(tag);

    $('.notice table td input').click(function(){
        var length = $('.notice table td input').length;
        var arr = [];
        $('.notice table td input').each(function(){
            if($(this).prop('checked')){
                arr.push(true);
            }else{
                arr = [];
            }
        });
        if(arr.length == length){
            $('.notice table th input').prop('checked',true);
        }else{
            $('.notice table th input').prop('checked',false);
        }
    });

    $('.notice table > tbody > tr > td').not($('.notice table > tbody > tr > td:first-of-type')).click(function(){
        var sq = $(this).parent().attr('data-sq');
        var data = (NOTICE_LIST.filter(e => e.NOTICE_SQ == sq))[0];
        SELECTED_NOTICE_SQ = sq;
        $('.popup').find('.title > span').text('공지등록');
        $('.popup').add($('.dark_div')).fadeIn(200);
        $('.popup').find('form.addNotice').show().siblings('form').hide();

        $('#new_noticeCategory').val(data.NOTICE_TYPE);
        $('#new_contentTitle').val(data.NOTICE_TITLE);
        $('#new_contentText').val(data.NOTICE_CONTENTS);
        $('.writerInfoTag').show();
        $('#new_writer').val((TRAINER_LIST.filter(e => e.USER_SQ == data.CREATEDBY))[0].USER_NM);
        $('[name="addNoticeFrm"] .submit').removeClass('add').text('수 정');
    });
}

// 공지등록
function SEND_NOTICE_DATA(){
    var formData = new FormData();
    formData.append('NOTICE_TYPE', $('#new_noticeCategory').val());
    formData.append('NOTICE_TITLE',  $('#new_contentTitle').val());
    formData.append('NOTICE_CONTENTS', $('#new_contentText').val());
    
    $.ajax({
        url: "flow_controller.php?task=execNoticeAdd",
        method: "POST",
		contentType: false,
        processData: false,
        data: formData,
		success: function(result){
            var data = JSON.parse(result);
            if(data.result == 'Fail'){
                return false;
            }
            GET_NOTICE_DATA();
            $('div.popup > .addNotice > article.body input, div.popup > .addNotice > article.body select, div.popup > .addNotice > article.body textarea').val('');
            $('.dark_div').add($('.popup')).fadeOut(100);
            alert('공지가 등록되었습니다.');
		},
		error: function (e) {
			console.log(e);
		}
    });
}

function SET_NOTICE_DATA(){
    var formData = new FormData();
    
    formData.append('NOTICE_SQ', SELECTED_NOTICE_SQ);
    formData.append('NOTICE_TYPE', $('#new_noticeCategory').val());
    formData.append('NOTICE_TITLE',  $('#new_contentTitle').val());
    formData.append('NOTICE_CONTENTS', $('#new_contentText').val());
    
    $.ajax({
        url: "flow_controller.php?task=execNoticeModify",
        method: "POST",
		contentType: false,
        processData: false,
        data: formData,
		success: function(result){
            var data = JSON.parse(result);
            if(data.result == 'Fail'){
                return false;
            }
            GET_NOTICE_DATA();
            $('div.popup > .addNotice > article.body input, div.popup > .addNotice > article.body select, div.popup > .addNotice > article.body textarea').val('');
            $('.dark_div').add($('.popup')).fadeOut(100);
            alert('공지가 수정되었습니다.');
		},
		error: function (e) {
			console.log(e);
		}
    });
}

function DEL_NOTICE_DATA(sq){
    var formData = new FormData();
    formData.append('NOTICE_SQ', sq);

    $.ajax({
        url: "flow_controller.php?task=execNoticeDelete",
        method: "POST",
		contentType: false,
        processData: false,
        data: formData,
		success: function(result){
            var data = JSON.parse(result);
            if(data.result == 'Fail'){
                return false;
            }
            MAKE_NOTICE_LIST(data);
		},
		error: function (e) {
			console.log(e);
		}
    });
}


//////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////


// 문서가 로드 된 후..
$(function(){
   
    getTrainerList();       // 임직원관리
    GET_TICKET_DATA();      // 상품관리
    GET_TICKETING_DATA();   // 예약관리
    GET_CENTER_DATA();      // 센터관리

    setTimeout(() => {
        if($USER_GRADE < 3){
            if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 81) == -1){
                if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 82) == -1){
                    if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 83) == -1){
                        if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 84) == -1){
                            if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 85) == -1){
                                if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 86) == -1){
                                    alert('권한이 없습니다.');
                                    history.back();
                                }
                                $('.top_sMenu li').eq(5).click();
                            }
                            $('.top_sMenu li').eq(4).click();
                        }
                        $('.top_sMenu li').eq(3).click();
                    }
                    $('.top_sMenu li').eq(2).click();
                }
                $('.top_sMenu li').eq(1).click();
            }
        }
    },500);


    // 임직원 관리
    const setMenu = $('.top_sMenu li');                                            // 설정 메뉴
    const content = $('#wrap > section.content');                               // 설정 컨텐츠
    const addLevel = $('li[data-title="카테고리(직급) 추가"]');                             // 직급 추가버튼
    const setLevel = $('li[data-title="직급편집"]');                             // 직급 편집버튼
    const delLevel = $('div.popup > .setLevel input + button');                 // 직급 삭제버튼
    const addTrainer = $('.add_Trainer_pop');                                   // 트레이너 등록버튼
    const delTrainer = $('.trainer > ul > li > ul > li i.fa-trash-alt');        // 트레이너 삭제버튼

    // 상품 관리
    const addItem = $('#addItem');                                              // 상품 추가
    const setCategory = $('#setCategory');                                      // 카테고리 편집
    const addCategory = $('#addCategory');                                      // 카테고리 추가
    const AddSmallCategory = $('.AddSmallCategory');

    // 공지 관리
    const addNotice = $('button.add_Notice_pop');
    const addNoticeCategory = $('li[data-title="카테고리 추가"]');
    const setNoticeCategory = $('li[data-title="카테고리 편집"]');
    const noticeList = $('.notice table > tbody > tr > td');

    // 팝업
    const popup = $('div.popup');
    const xBtn = $('div.popup > h2.title > button.closeBtn');
    const level = $('#addTrainer_level');
    const closeBtn = $('div.popup > .content > article.btn > button.close');
    
    

    // 설정 메뉴 클릭
    setMenu.click(function(){
        var i = $(this).index();
        if($USER_GRADE < 3){
            
            if(i == 0){
                if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 81) == -1){
                    alertApp('X', '권한이 없습니다.');
                    return false;
                }
            }else if(i == 1){
                if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 82) == -1){
                    alertApp('X', '권한이 없습니다.');
                    return false;
                }
            }else if(i == 2){
                if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 83) == -1){
                    alertApp('X', '권한이 없습니다.');
                    return false;
                }
            }else if(i == 3){
                if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 84) == -1){
                    alertApp('X', '권한이 없습니다.');
                    return false;
                }
            }else if(i == 4){
                if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 85) == -1){
                    alertApp('X', '권한이 없습니다.');
                    return false;
                }
            }else if(i == 5){
                if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 86) == -1){
                    alertApp('X', '권한이 없습니다.');
                    return false;
                }
            }
        }
        $(this).addClass('active').siblings().removeClass('active');
        content.find('article').hide();
        content.find('article.content_'+i).show();
    });


    
    // 직급추가
    $('#add_trainer_category_submit_btn').click(function(e){
        e.preventDefault();
        if($('#levelName').val() == ''){
            alert('직급의 명칭을 입력해주세요.');
            return false;
        }else{
            ADD_TRAINER_CATEGORY($('#levelName').val());
        }
    });


    $('#TICKETING_SETTING_SUBMIT').click(function(e){
        e.preventDefault();
        SEND_TICKETING_SETTING();
    });

    $('#set_company_submit').click(function(e){
        e.preventDefault();
        if($('#centerManager').val() == ''){
            $('#centerManager').focus();
            alert('센터장을 입력해주세요.');
            return false;
        }
        if($('#centerName').val() == ''){
            $('#centerName').focus();
            alert('센터명을 입력해주세요.');
            return false;
        }
        SET_COMPANY_DATA();
    });


    $('#set_operating_submit').click(function(e){
        e.preventDefault();
        SET_OPERATING_INFO();
    });

    // 임직원 추가
    $('#addManagerSubmitBtn').click(function(){
        var USERID = $('#addTrainer_id').val();
        var USERNAME = $('#addTrainer_name').val();
        var EMAIL = $('#addTrainer_email').val();
        var SEX = $('#addTrainer_gender_male').prop('checked') ? 'M' : 'F';
        var TEL = $('#addTrainer_phone1').val() + '-' + $('#addTrainer_phone2').val() + '-' + $('#addTrainer_phone3').val();
        var BIRTH = $('#addTrainer_birth').val();
        var ADDRESS = $('#addTrainer_address').val();
        var COMMENT = $('#addTrainer_hello').val();
        var WORKCATEGORY = $('#addTrainer_level').val();
        var WORKSTARTDATE = $('#work_start_date').val();
        var myFileUp = $('#addTrainer_img').prop('files').length == 0 ? 
            '' : $('#addTrainer_img').prop('files')[0];

        if(USERNAME == ''){
            alertApp('!', '이름을 입력해주세요.');
            $('#addTrainer_name').focus();
            return false;
        }
        if(USERID == ''){
            alertApp('!', '아이디를 입력해주세요.');
            $('#addTrainer_id').focus();
            return false;
        }
        if($('#addTrainer_phone1').val() == ''){
            alertApp('!', '연락처를 입력해주세요.');
            $('#addTrainer_phone1').focus();
            return false;
        }
        if($('#addTrainer_phone2').val() == ''){
            alertApp('!', '연락처를 입력해주세요.');
            $('#addTrainer_phone2').focus();
            return false;
        }
        if($('#addTrainer_phone3').val() == ''){
            alertApp('!', '연락처를 입력해주세요.');
            $('#addTrainer_phone3').focus();
            return false;
        }
        if(BIRTH == ''){
            alertApp('!', '생일을 입력해주세요.');
            $('#addTrainer_birth').focus();
            return false;
        }


        let formData = new FormData();
            formData.append('USERID', USERID);
            formData.append('USERNAME', USERNAME);
            formData.append('EMAIL', EMAIL);
            formData.append('SEX', SEX);
            formData.append('TEL', TEL);
            formData.append('BIRTH', BIRTH);
            formData.append('ADDRESS', ADDRESS);
            formData.append('COMMENT', COMMENT);
            formData.append('WORKCATEGORY', WORKCATEGORY);
            formData.append('WORKSTARTDATE', WORKSTARTDATE);
            formData.append('myFileUp', myFileUp);

        $.ajax({
            url: 'flow_controller.php?task=execManagerCreate',
            data: formData,
            method: 'POST',
            contentType: false,
            processData: false,
            success: function(r){
                let data = JSON.parse(r);
                if(data.result == 'Fail'){
                    if(data.reason == 'User ID Exist'){
                        alertApp('!', '중복된 아이디입니다.');
                        $('#addTrainer_id').focus();
                        return false;
                    }
                    alertApp('!', '중복된 아이디입니다.');
                    return false;
                }
                TRAINER_LIST = data;
                MAKE_TRAINER_LIST(
                    TRAINER_LIST.filter(e => e.WORKSTATUS == $('.trainer > h3 > select').val()).filter(e => e.ISUSE != 0)
                );
                alertApp('O', '등록되었습니다.');
                $('.popup').add($('.dark_div')).fadeOut(200);
                return false;
            },
            error: function(e){
                alertApp('X', '다시 시도해주세요.');
                return false;
            }
        })
    });




/////////////////////////////////////////////////////////
// 상품 관리
    $('#add_item_submit_btn').click(function(e){
        e.preventDefault();
        var temp = $('#itemFrm_attr_1').prop('checked') ? true : false;
        
        if($('#itemFrm_name').val() == ''){
            alert('상품의 이름을 입력해주세요.');
            return false;
        }
        if($('#itemFrm_type').val() == ''){
            alert('상품의 카테고리를 선택해주세요.');
            return false;
        }
        if($('#itemFrm_dayStop').val() == ''){
            alert('상품의 일일 이용횟수제한을 선택해주세요.');
            return false;    
        }
        if($('#itemFrm_weekStop').val() == ''){
            alert('상품의 주간 이용횟수제한을 선택해주세요.');
            return false;
        }
        if($('#itemFrm_pay').val() == ''){
            alert('상품의 판매정가를 입력해주세요.');
            return false;
        }
        if(temp){
            if($('#itemFrm_date_write').val() == ''){
                alert('상품의 기간을 입력해주세요.');
                return false;
            }
        }else{
            if($('#itemFrm_count_write').val() == ''){
                alert('상품의 횟수를 입력해주세요.');
                return false;
            }
        }


        var CATEGORY_SQ = $('#itemFrm_type').val();
        var SUBCATEGORY_SQ = $('#itemFrm_category').val() == '' ? '' : $('#itemFrm_category').val();
        var VOUCHER_NAME = $('#itemFrm_name').val();
        var VOUCHER_TYPE = $('#itemFrm_service_1').prop('checked') ? '1' : $('#itemFrm_service_2').prop('checked') ? '2' : '3';
        var USE_TYPE = $('#itemFrm_attr_1').prop('checked') ? '1' : '2';
        var PERIOD_TYPE = $('#itemFrm_date').val();
        var PERIOD = $('#itemFrm_date_write').val() != '' ? $('#itemFrm_date_write').val() : 0;
        var PERIOD_UNIT = $('#itemFrm_date_write_month').val();
        var COUNT_TYPE = $('#itemFrm_count').val();
        var COUNT = $('#itemFrm_count_write').val() != '' ? $('#itemFrm_count_write').val() : 0;
        var PRICE = $('#itemFrm_pay').val() != '' ? $('#itemFrm_pay').val() : 0;
        var SURTAX_TYPE = $('#itemFrm_tax').prop('checked') ? '2' : '1'
        var DISCOUNT_TYPE = $('#itemFrm_sale').val();
        var DISCOUNT_RATIO = $('#itemFrm_sale').val() == '1' ? $('#itemFrm_sale_input').val() : 0;
        var DISCOUNT_AMOUNT = $('#itemFrm_sale').val() == '1' ? $('#itemFrm_pay').val() * ($('#itemFrm_sale_input').val() / 100) : $('#itemFrm_sale').val() == '2' ? $('#itemFrm_sale_input').val() : 0;
        var SELLINGPRICE = PAYMENT_ITEM;
        var ENTERLIMIT_DAY = $('#itemFrm_dayStop').val();
        var ENTERLIMIT_WEEK = $('#itemFrm_weekStop').val();

        if($(this).attr('class').indexOf('add') > -1){

            var formData = new FormData();
            formData.append('CATEGORY_SQ',CATEGORY_SQ);
            formData.append('SUBCATEGORY_SQ',SUBCATEGORY_SQ);
            formData.append('VOUCHER_NAME',VOUCHER_NAME);
            formData.append('VOUCHER_TYPE',VOUCHER_TYPE);
            formData.append('USE_TYPE',USE_TYPE);
            formData.append('PERIOD_TYPE',PERIOD_TYPE);
            formData.append('PERIOD',PERIOD);
            formData.append('PERIOD_UNIT',PERIOD_UNIT);
            formData.append('COUNT_TYPE',COUNT_TYPE);
            formData.append('COUNT',COUNT);
            formData.append('PRICE',PRICE);
            formData.append('SURTAX_TYPE',SURTAX_TYPE);
            formData.append('DISCOUNT_TYPE',DISCOUNT_TYPE);
            formData.append('DISCOUNT_RATIO',DISCOUNT_RATIO);
            formData.append('DISCOUNT_AMOUNT',DISCOUNT_AMOUNT);
            formData.append('SELLINGPRICE',SELLINGPRICE);
            formData.append('ENTERLIMIT_DAY',ENTERLIMIT_DAY);
            formData.append('ENTERLIMIT_WEEK',ENTERLIMIT_WEEK);
            
            ADD_ITEM_SEND_DATA(formData);
        }else{

            var formData = new FormData();
            var VOUCHER_SQ = SELECTED_SET_ITEM;
            formData.append('VOUCHER_SQ',VOUCHER_SQ);
            formData.append('CATEGORY_SQ',CATEGORY_SQ);
            formData.append('SUBCATEGORY_SQ',SUBCATEGORY_SQ);
            formData.append('VOUCHER_NAME',VOUCHER_NAME);
            formData.append('VOUCHER_TYPE',VOUCHER_TYPE);
            formData.append('USE_TYPE',USE_TYPE);
            formData.append('PERIOD_TYPE',PERIOD_TYPE);
            formData.append('PERIOD',PERIOD);
            formData.append('PERIOD_UNIT',PERIOD_UNIT);
            formData.append('COUNT_TYPE',COUNT_TYPE);
            formData.append('COUNT',COUNT);
            formData.append('PRICE',PRICE);
            formData.append('SURTAX_TYPE',SURTAX_TYPE);
            formData.append('DISCOUNT_TYPE',DISCOUNT_TYPE);
            formData.append('DISCOUNT_RATIO',DISCOUNT_RATIO);
            formData.append('DISCOUNT_AMOUNT',DISCOUNT_AMOUNT);
            formData.append('SELLINGPRICE',SELLINGPRICE);
            formData.append('ENTERLIMIT_DAY',ENTERLIMIT_DAY);
            formData.append('ENTERLIMIT_WEEK',ENTERLIMIT_WEEK);
            
            SET_ITEM_SEND_DATA(formData);
        }
    });

    function SET_ITEM_SEND_DATA(formData){
        $.ajax({
            url: "flow_controller.php?task=execVoucherModify",
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(result){
                var data = JSON.parse(result);
                if(data.result){return false}
                GET_TICKET_DATA();
                $('.dark_div').add(popup).fadeOut(100);
                // alert('상품이 수정되었습니다.');
                alertApp('O','상품이 수정되었습니다.');
                return false;
            },
            error: function (e) {
                console.log(e);
                // alert('상품이 수정되지 않았습니다.');
                alertApp('X','상품이 수정되지 않았습니다.');
                return false;
            }
        });
    }

    // 상품 추가
    function ADD_ITEM_SEND_DATA(formData){
        $.ajax({
            url: "flow_controller.php?task=execVoucherAdd",
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(result){
                var data = JSON.parse(result);
                if(data.result){return false}
                GET_TICKET_DATA();
                $('.dark_div').add(popup).fadeOut(100);
                alert('상품이 추가되었습니다.');
                return false;
            },
            error: function (e) {
                console.log(e);
                alert('상품이 추가되지 않았습니다.');
            }
        });
    }


    $('#add_item_category_submit_btn').click(function(e){
        e.preventDefault();
        var text = $('#categoryName').val();
        if(text == ''){
            alert('카테고리 명칭을 적어주세요');
            return false;
        }else{
            let formData = new FormData();
            formData.append('CATEGORY_NAME',text);
            ADD_ITEM_CATEGORY(formData);
        }
    });
    // 카테고리 추가
    function ADD_ITEM_CATEGORY(formData){
        $.ajax({
            url: "flow_controller.php?task=execCategoryAdd",
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(result){
                var data = JSON.parse(result);
                if(data.result){return false}
                GET_TICKET_DATA();
                $('.dark_div').add(popup).fadeOut(100);
                $('#categoryName').val('');
                return false;
            },
            error: function (e) {
                console.log(e);
                alert('카테고리가 생성되지 않았습니다.');
                return false;
            }
        });
    }


    $('#add_item_subcategory_submit_btn').click(function(e){
        e.preventDefault();
        var text = $('#categorySmallName').val();
        var parent_sq = $(this).parents('form.addCategorySmall').attr('data-parent_seq');
        if(text == ''){
            alert('서브카테고리 명칭을 적어주세요');
            return false;
        }else{
            let formData = new FormData();
            formData.append('CATEGORY_SQ',parent_sq);
            formData.append('SUBCATEGORY_NAME',text);
            ADD_ITEM_SUB_CATEGORY(formData);
        }
    });

    // 서브카테고리 추가
    function ADD_ITEM_SUB_CATEGORY(formData){
        $.ajax({
            url: "flow_controller.php?task=execSubCategoryAdd",
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(result){
                var data = JSON.parse(result);
                if(data.result){return false}
                GET_TICKET_DATA();
                $('.dark_div').add(popup).fadeOut(100);
                $('#categorySmallName').val('');
                return false;
            },
            error: function (e) {
                console.log(e);
                alert('서브카테고리가 생성되지 않았습니다.');
                return false;
            }
        });
    }







    
/////////////////////////////////////////////////////////
    // 센터 관리
    











/////////////////////////////////////////////////////////
    // 권한 관리
    $('#grade_SubmitBtn').click(function(e){
        e.preventDefault();
        ADD_AUTH = [];
        DELETE_AUTH = [];
        var target = $('#rightSet div.right_rightList > div.content > fieldset > input');
        var len = target.length;

        for(let i = 0; i < len; i++){
            let state = target.eq(i).prop('checked') ? 1 : 0;

            if(state != LAST_GRADE[i]){
                if(LAST_GRADE[i] == 0){
                    ADD_AUTH.push(target.eq(i).attr('data-code'))
                }else{
                    DELETE_AUTH.push(target.eq(i).attr('data-code'))
                }
            }
        }

        let formData = new FormData();
            formData.append('USER_SQ', $('#wrap > .content > article#rightSet nav.right_trainerList > .content > li.active').attr('data-seq'));
            formData.append('ADD_AUTH', ADD_AUTH);
            formData.append('DELETE_AUTH', DELETE_AUTH);

        $.ajax({
            url: "flow_controller.php?task=execAuthorityChange",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(r){
                alertApp('O', '저장되었습니다.');
                $('#wrap > .content > article#rightSet nav.right_trainerList > .content > li.active').click();
                GRADE_YN();
                return false;
            },
            error: function(e){
                alertApp('X', '저장에 실패하였습니다.');
                return false;
            }
        });
    });


// ////////////////////////////////////////////////////////////
    // 팝업 띄우기
    addTrainer.add(addLevel).add(setLevel).add(setCategory).add(addItem).add(addCategory).add(AddSmallCategory).add(addNotice).add(addNoticeCategory).add(setNoticeCategory).click(function(){
        var thisClass = $(this).attr('data-form');
        var thisTitle = $(this).attr('data-title');
        popup.find('.title > span').text(thisTitle);
        popup.add($('.dark_div')).fadeIn(200);
        popup.find('form.'+thisClass).show().siblings('form').hide();
        
        $('#addTrainer_img').val('');
        $('#addTrainer_img').siblings('img').attr('src', 'img/user.png');
        $('#addTrainer_name').val('');
        $('#addTrainer_id').val('');
        $('#addTrainer_email').val('');
        $('#addTrainer_gender').prop('checked', true);
        $('#addTrainer_phone1').val('');
        $('#addTrainer_phone2').val('');
        $('#addTrainer_phone3').val('');
        $('#addTrainer_birth').val('');
        $('#addTrainer_address').val('');
        $('#addTrainer_hello').val('');
        $('#addTrainer_level').val('');
        $('#work_start_date').val('');

        $('#new_noticeCategory').val('');
        $('#new_contentTitle').val('');
        $('#new_contentText').val('');
        $('.writerInfoTag').hide();
        $('[name="addNoticeFrm"] .submit').addClass('add').text('등 록');
        $('[name="addItemFrm"] .submit').addClass('add').text('등 록');

        ADD_TICKET_RESET();
    });


    ////// 팝업내용 //////
    // X버튼 또는 닫기 버튼 클릭시
    xBtn.add(closeBtn).click(()=>{
        $('.dark_div').add(popup).fadeOut(100);
    });

    // 이미지 업로드
    var uSet_img = $('div.popup > .content.addTrainer .row1 > img');
    $("#addTrainer_img").change(function(){
        readImgView(this, 'img[alt="프로필사진"]');
    });

    $("#centerImage").change(function(){
        readImgView(this,'.centerProfileImage');
    });


    $('[name="ticketingSetFrm"]').find('select').empty();

    // 센터운영
    // 월,화,수,목,금,토,일 (시간for문)
    for(var i = 0; i < 24; i++){
        var stri = String(i).length == 1 ? '0'+String(i) : i;
        var tag = '<option value="' + i + '">' + stri + '</option>';
        $('.TurnTime_Hour').append(tag);

        $('[name="ticketingSetFrm"]').find('select[name$="select1"]').append(
            '<option value="' + i + '">' + stri + '</option>'
        );

    }
    // 월,화,수,목,금,토,일 (분for문)
    for(var ii = 0; ii < 60; ii++){
        var S_ii = String(ii).length;
        var strii = S_ii == 1 ? '0'+String(ii) : ii;
        var tag = '<option value="' + ii + '">' + strii + '</option>';
        $('.TurnTime_Minutes').append(tag);

        $('[name="ticketingSetFrm"]').find('select[name$="select2"]').append(
            '<option value="' + ii + '">' + strii + '</option>'
        );

    }

    // datepicker
    var cal = $('#calendar').datepicker({
        dateFormat: 'yy-mm-dd',
        yearSuffix: "년",
        monthNamesShort: ['1','2','3','4','5','6','7','8','9','10','11','12'],
        monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
        dayNamesMin: ['일','월','화','수','목','금','토'],
        dayNames: ['일요일','월요일','화요일','수요일','목요일','금요일','토요일'],

        onSelect: function(dateText){
            console.log(dateText);
            if((HOLIDAY_LIST.filter(e => e.HOLIDAY == dateText)).length != 0){
                var filtedList = (HOLIDAY_LIST.filter(e => e.HOLIDAY == dateText))[0];
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
                    MAKE_HOLIDAY_LIST(HOLIDAY_LIST);
                    return false;
                }
                console.log(ask);
                return false;
            }

        },
        onChangeMonthYear: function(a,b,c,d,e){
            setTimeout(()=>MAKE_HOLIDAY_LIST(HOLIDAY_LIST),100);
        }
    });

    // ROOM 추가버튼
    var addRoomBtn = $('article#centerSet .centerRoom .addRoomBtn');
    var roomList = $('#RoomList');
    var NEW_INPUT = {};
    var NEW_ROOM_BOOL = true;
    addRoomBtn.click(function(e){
        e.preventDefault();
        if(NEW_ROOM_BOOL){
            NEW_ROOM_BOOL = false;
            roomList.append('<li><input class="new" type="text"> <button type="button" class="newBtn">저장</button></li>');
            NEW_INPUT = roomList.find('input.new');
            NEW_INPUT.focus();
        }else{
            roomList.find('input.new').focus();
        }

        NEW_INPUT.blur(function(){
            if($(this).val() == ''){
                $(this).parent().remove();
                NEW_ROOM_BOOL = true;
            }else{
                return false;
            }
        });

        NEW_INPUT.siblings('button.newBtn').click(function(){
            SAVE_ROOM(NEW_INPUT.val());
        });


    });

    function SAVE_ROOM(val){

        let formData = new FormData();
        formData.append('ROOM_NAME',val);

        $.ajax({
            url : 'flow_controller.php?task=execRoomAdd',
            method: 'POST',
            data : formData,
            contentType : false,
            processData : false,
            success : function(result){
                var data = JSON.parse(JSON.parse(result));
                NEW_ROOM_BOOL = true;
                alertApp('O', '룸 정보가 저장 되었습니다.');
                MAKE_ROOM_LIST(data);
                return false;
            },
            error : function(e){
                console.log(e);
            }
        });
        console.log(val);
    }
    

    function SEND_HOLIDAY(dateText,ask){
        
        var formData = new FormData();
        formData.append('HOLIDAY',dateText);
        formData.append('HOLIDAY_NAME',ask);

        $.ajax({
            url: "flow_controller.php?task=execHolidayAdd",
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(result){
                var data = JSON.parse(JSON.parse(result));
                console.log(data);
                if(data.result){return false}
                HOLIDAY_LIST = data;
                MAKE_HOLIDAY_LIST(HOLIDAY_LIST);
                return false;
            },
            error: function (e) {
                console.log(e);
            }
        });
    }


    function DEL_HOLIDAY(sq){
        
        var formData = new FormData();
        formData.append('HOLIDAY_SQ',sq);

        $.ajax({
            url: "flow_controller.php?task=execHolidayDelete",
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(result){
                var data = JSON.parse(result);
                var data = JSON.parse(data);
                if(data.result){return false}

                HOLIDAY_LIST = data;
                MAKE_HOLIDAY_LIST(HOLIDAY_LIST);
                setTimeout(() => {
                    alertApp('O', '휴일이 삭제되었습니다.');
                }, 100);
                return false;
            },
            error: function (e) {
                console.log(e);
            }
        });
    }



    // 정액/정율제 클릭시
    $('[name="solo-pay"]').click(()=>solo_checkDie());
    $('[name="group-pay"]').click(()=>group_checkDie());
    $('[name="solo-no-show-pay"]').click(()=>solo_noShow());
    $('[name="group-no-show-pay"]').click(()=>group_noShow());

    // 랜덤 비밀번호 생성
    function makePW(){
        var pw = '';
        var n1 = Array.from('0123456789');
        var n2 = Array.from('!@#$&!@#$&');

        for(var i = 0; i < 6; i++){
            var random = Math.floor(Math.random()*10);
            if(i == 0){
                pw = n2[random];
            }else if(i == 5){
                pw += n2[random];
            }else{
                pw += n1[random];
            }
        }
        return pw;
    }


    // 정액제/정율제 클릭 함수 정의
    function solo_checkDie(){
        if($('#solo-pay1').prop('checked')){
            $('#solo-pay1-value').prop('disabled',false);
            $('#solo-pay2-value').prop('disabled',true);
        }else if($('#solo-pay2').prop('checked')){
            $('#solo-pay2-value').prop('disabled',false);
            $('#solo-pay1-value').prop('disabled',true);
        }
    }
    function solo_noShow(){
        if($('#solo-no-show-pay2').prop('checked')){
            $('#solo-no-show-pay2-value').prop('disabled',false);
        }else{
            $('#solo-no-show-pay2-value').prop('disabled',true);
        }
    }
    function group_checkDie(){
        if($('#group-pay1').prop('checked')){
            $('#group-pay1-value').prop('disabled',false);
            $('#group-pay2-value').prop('disabled',true);
        }else if($('#group-pay2').prop('checked')){
            $('#group-pay2-value').prop('disabled',false);
            $('#group-pay1-value').prop('disabled',true);
        }
    }
    function group_noShow(){
        if($('#group-no-show-pay2').prop('checked')){
            $('#group-no-show-pay2-value').prop('disabled',false);
        }else{
            $('#group-no-show-pay2-value').prop('disabled',true);
        }
    }


    // 예약 관리
    $('[name="ticketingSetFrm"]').find('input[type="radio"]').click(function(){
        showInput();
    });


    function showInput(){
        if($('#solo_ticketingPossibleTime_Set').prop('checked')){
            $('#solo_ticketingPossibleTime_Set').siblings('span').css('display','inline-block');
        }else{
            $('#solo_ticketingPossibleTime_Set').siblings('span').css('display','none');
        }

        if($('#solo_ticketingChangeTime_4').prop('checked')){
            $('#solo_ticketingChangeTime_4').siblings('span').css('display','inline-block');
        }else{
            $('#solo_ticketingChangeTime_4').siblings('span').css('display','none');
        }

        if($('#group_ticketingPossibleTime_Set').prop('checked')){
            $('#group_ticketingPossibleTime_Set').siblings('span').css('display','inline-block');
        }else{
            $('#group_ticketingPossibleTime_Set').siblings('span').css('display','none');
        }

        if($('#group_ticketingChangeTime_4').prop('checked')){
            $('#group_ticketingChangeTime_4').siblings('span').css('display','inline-block');
        }else{
            $('#group_ticketingChangeTime_4').siblings('span').css('display','none');
        }
    }




    
    // 공지 관리

    // 체크박스 클릭
    $('.notice table th input').click(function(){
        if($(this).prop('checked')){
            $('.notice table td input').prop('checked',true);
        }else{
            $('.notice table td input').prop('checked',false);
        }
    });
    

    $('[name="addNoticeFrm"] .submit').click(function(e){
        e.preventDefault();
        if($('#new_noticeCategory').val() == ''){
            alert('공지 구분을 선택해주세요.');
            return false;
        }else if($('#new_contentTitle').val() == ''){
            alert('공지 제목을 입력해주세요.');
            return false;
        }else if($('#new_contentText').val() == ''){
            alert('공지 내용을 입력해주세요.');
            return false;
        }

        if($(this).attr('class').indexOf('add') > -1){
            SEND_NOTICE_DATA();
        }else{
            SET_NOTICE_DATA();
        }
    });

    // 공지삭제
    $('button.del_Notice_pop').click(function(){
        DEL_SELECTED_NOTICE = [];
        $('.notice table td input').each(function(){
            if($(this).prop('checked')){
                DEL_SELECTED_NOTICE.push($(this).attr('id'));
            }
        });
        
        if(DEL_SELECTED_NOTICE.length == 0){
            alertApp('!', '선택된 공지가 없습니다.');
            return false;
        }else{
            var ask = confirm('선택된 공지를 삭제하시겠습니까?');
            if(ask){

                for(let i in DEL_SELECTED_NOTICE){
                    DEL_NOTICE_DATA(DEL_SELECTED_NOTICE[i]);
                }
                alertApp('O', '삭제되었습니다.');
                return false;

            }else{
                return false;
            }
        }

    });

    // 공지 뷰 버튼 클릭
    $('.notice > h3 > button').click(function(){
        $(this).addClass('active').siblings().removeClass('active');
        var sq = $(this).attr('data-sq');
        MAKE_NOTICE_LIST(NOTICE_LIST.filter(e => e.NOTICE_TYPE == sq));
    });
    $('.notice > h3 > button').eq(0).click();

    //////////////////////////////////////////////////////////////////////////////////////////////////////////

    // default 값
    // setTimeout(() => setMenu.eq(0).click(), 300);
    solo_checkDie();
    group_checkDie();
    solo_noShow();
    group_noShow();
    $('#itemFrm_attr_date').click();
    showInput();


    // 작업용
});