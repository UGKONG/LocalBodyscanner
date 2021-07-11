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
var ITEM_LIST = [];
var ITEM_CATEGORY_LIST = [];
var ITEM_SUB_CATEGORY_LIST = [];
var SET = {};
var MEMBER_LIST = [];


function GET_ITEM_DATA(){
    $.ajax({
        url: "flow_controller.php?task=getVoucherList",
        method: "POST",
		contentType: false,
        processData: false,
        success: function(result){
            var data = result.split('|');
            if(data.result) return false;
            ITEM_CATEGORY_LIST = (JSON.parse(data[1]));
            ITEM_SUB_CATEGORY_LIST = (JSON.parse(data[2]));
            SET.USE_TYPE = (JSON.parse(data[4]));
            ITEM_LIST = (JSON.parse(data[0]));

            MAKE_ITEM_LIST(ITEM_LIST);
            MAKE_CATEGORY_LIST(ITEM_CATEGORY_LIST);
            MAKE_SUB_CATEGORY_LIST(ITEM_SUB_CATEGORY_LIST);

            ADD_EVENT_LISTENER();
        },
        error: function(e){
            console.log(e);
        }
    });
}

function MAKE_ITEM_LIST(list){
    var tag = '';
    $('ul#itemList').empty();
    for(let i = 0; i < list.length; i++){
        if((ITEM_CATEGORY_LIST.filter(e => e.CATEGORY_SQ == list[i].CATEGORY_SQ)).length != 0){
            var CATEGORY_NAME = (ITEM_CATEGORY_LIST.filter(e => e.CATEGORY_SQ == list[i].CATEGORY_SQ))[0].CATEGORY_NAME;
        }else{
            var CATEGORY_NAME = '카테고리 미지정';
        }
        if((ITEM_SUB_CATEGORY_LIST.filter(e => e.SUBCATEGORY_SQ == list[i].SUBCATEGORY_SQ)).length != 0){
            var SUB_CATEGORY_NAME = (ITEM_SUB_CATEGORY_LIST.filter(e => e.SUBCATEGORY_SQ == list[i].SUBCATEGORY_SQ))[0].SUBCATEGORY_NAME;
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
                '<span class="itemPay">' + sale + '</span>' +
                '<div class="btn">' +
                    '<button class="buy">결 제</button>' +
                '</div>' +
            '</div>'
        '</li>';
    }
    $('ul#itemList').append(tag);

    $('#itemList > li button.buy').click(function(){
        if($USER_GRADE < 3){
            if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 31) == -1){
                alertApp('X', '권한이 없습니다.');
                return false;
            }
        }
        
        if($('#selectMember').val() == '' || $('#selectMember').attr('data-seq') == ''){
            alertApp('!', '회원을 선택하여주세요.');
            $('#selectMember').focus();
            return false;
        }
        var sq = $(this).parent().parent().parent().attr('data-item_seq');
        var option = 'top=100, left=100, width=1200, height=714, menubar=no, toolbar=no, resizable=no, scrollbars=no, status=no';
        window.open('payment.php?seq=' + sq ,'결제창',option);
    });

    $('.item_all_count').text('총 상품 수 : ' + list.length + '개');
}

function MAKE_CATEGORY_LIST(list){
    var categoryTag = '';
    $('#Category').empty();
    
    for(let i = 0; i < list.length; i++){
        categoryTag = '<li data-seq="' + list[i].CATEGORY_SQ + '"><a href="#" style="color:' + CATEGORY_COLOR[i] + '">' + list[i].CATEGORY_NAME + '</a><ul></ul></li>';
        $('#Category').append(categoryTag);
    }
}

function MAKE_SUB_CATEGORY_LIST(list){
    var subCategoryTag = '';
    
    for(let i = 0; i < list.length; i++){
        subCategoryTag = '<li data-seq="' + list[i].SUBCATEGORY_SQ + '"><a href="#">' + list[i].SUBCATEGORY_NAME + '</a></li>';
        $('#Category > li[data-seq="' + list[i].CATEGORY_SQ + '"] > ul').append(subCategoryTag);
    }
}


function ADD_EVENT_LISTENER(){
    
    // 전체보기
    $('#allCategory').click(function(e){
        e.preventDefault();
        $('#item_search_Box').val('');
        $('#itemList > li').hide();
        $('#itemList > li').show();
        $('#Category li').removeClass('active');
    });
    $('#Category > li > a').click(function(e){         // a태그
        e.preventDefault();
        $('#item_search_Box').val('');
        var that = $(this).parent();        // li태그
        var seq = that.attr('data-seq');    // Type Seq
        
        $('#Category li').removeClass('active');
        $(this).parent().addClass('active');

        $('#itemList > li').hide();
        $('#itemList > li[data-type="' + seq + '"]').show();

    });
    $('#Category > li > ul > li > a').click(function(e){
        e.preventDefault();
        $('#item_search_Box').val('');
        var that = $(this).parent();
        var seq = that.attr('data-seq');

        $('#Category li').removeClass('active');
        $(this).parent().addClass('active');

        $('#itemList > li').hide();
        $('#itemList > li[data-category="' + seq + '"]').show();
    });
}


// 문서가 전부 로드 되고 할일
$(function(){

    GET_ITEM_DATA();

    sessionStorage.buyMemberSeq;
    if(sessionStorage.buyMemberSeq == undefined || sessionStorage.buyMemberSeq == ''){
        sessionStorage.buyMemberSeq = '';
    }else{
        $('#selectMember').
        attr('data-seq',sessionStorage.buyMemberSeq).
        val(sessionStorage.buyMemberName + ' 회원');
    }

    $('#mSearchText').siblings('.name_list').find('li').click(function(){
        var i;
        var seq = $(this).attr('data-seq');
        for(var a = 0; a < memberList.length; a++){
            if(seq == memberList[a].sequence){
                var i = a;
                break;
            }
        }
        $('.mSearch_container').fadeOut(100);
        $('#paymentMember').val(memberList[i].name);
        $('#uNum').text(memberList[i].phone)
        $('#Receipt_Name').text(memberList[i].name);
    });


    // 이용권 검색
    $('#item_search_Box').keyup(function(){
        var text = $(this).val().toLowerCase();
        $('#itemList > li').filter(function(){
            $(this).toggle($(this).find('.itemName').text().toLowerCase().search(text) > -1);
        });
    });

    // 정렬 드롭박스
    $('#item_array').change(function(){
        var val = $(this).val();
        var list = ITEM_LIST;
        var sortList = [];

        switch (val) {
            case 'new':
                sortList = list.sort(function(a, b){
                    return DATE(a.MODIFIEDDT) > DATE(b.MODIFIEDDT) ? -1 : DATE(a.MODIFIEDDT) < DATE(b.MODIFIEDDT) ? 1 : 0;
                });
                break;

            case 'up':
                sortList = list.sort(function(a, b){
                    return a.SELLINGPRICE > b.SELLINGPRICE ? -1 : a.SELLINGPRICE < b.SELLINGPRICE ? 1 : 0;
                });
                break;

            case 'down':
                sortList = list.sort(function(a, b){
                    return a.SELLINGPRICE < b.SELLINGPRICE ? -1 : a.SELLINGPRICE > b.SELLINGPRICE ? 1 : 0;
                });
                break;

            case 'name':
                sortList = list.sort(function(a, b){
                    return a.VOUCHER_NAME < b.VOUCHER_NAME ? -1 : a.VOUCHER_NAME > b.VOUCHER_NAME ? 1 : 0;
                });
                break;

            case 'hot':
                alert('아직 준비중입니다.');
                $('#item_array').val('new').change();
                return false;
                break;
        }
        MAKE_ITEM_LIST(sortList);
        console.log(sortList);
        
        function DATE(text){
            var date = new Date(text);
            return date;
        }
    });

    $('#m_search_div').mouseleave(function(){
        $('#searchHelpBox').fadeOut(150);
    });
    $('#selectMember').click(function(){
        $('#searchHelpBox').fadeIn(150);
        $('#searchHelpBox').find('#m_searchBox').focus();
    });
    $('#searchHelpBox > ul > li').click(function(){
        var seq = $(this).attr('data-seq');

    }); 





    // // 작업중
});
