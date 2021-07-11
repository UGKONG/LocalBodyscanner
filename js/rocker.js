var ROCKER_TYPE = [
    {
        SQ : '01',
        ATTR : '01',
        DISCRIPTION : '신발라커',
        COUNT : 20
    }
];
var ROCKER_LIST = [
    {
        ROCKER_SQ : '01',
        ROCKER_TYPE : '01',
        
    }
];
var MEMBER_LIST = [];
var TRAINER_LIST = [];

function GET_AJAX_DATA(){
    $.ajax({
        url : '',
        data : '',
        contentType : false,
		processData : false,
        success : function(result){
            console.log(result);
        },
        error : function(e){
            console.log(e);
        }
    })
}

// 라커종류 드롭박스 리스트 생성
function TypeSelect(w,n){
    var typeText = SET.rockerSet.Type[n][0];
    var typeCount = SET.rockerSet.Type[n][1];
    var attrText = SET.rockerSet.Attr[SET.rockerSet.Type[n][2]][0];
    var attrSeq = SET.rockerSet.Attr[SET.rockerSet.Type[n][2]][1];
    var rockerSeq = SET.rockerSet.Type[n][3];
    var typeSeq = n;
    w.append('<option value="'+typeSeq+'">' + attrText + ' ' + typeText+' ('+typeCount+'칸)' + '</option>');

    for(var i in SET.rockerSet.Attr){
        var seq = SET.rockerSet.Attr[i][1];
        var name = SET.rockerSet.Attr[i][0];
        var AttrTag;
        AttrTag += '<option value="' + seq + '">' + name + ' </option>';
    }

    // 라커관리 POPUP
    var type = SET.rockerSet.Type[n][2];
    var attrIdx = SET.rockerSet.Attr.findIndex(index => index[1] == type);
    var tag = 
    '<li data-seq="' + rockerSeq + '">\
        <select>' + AttrTag + '</select>\
        <label for="rockerName' + rockerSeq + '" class="hid">' + rockerSeq + '</label>\
        <input type="text" id="rockerName' + rockerSeq + '" class="rockerSet_name" value="' + typeText + '" placeholder="라커 이름">\
        <label for="rockerCount' + rockerSeq + '" class="hid">' + rockerSeq + '</label>\
        <input type="text" id="rockerCount' + rockerSeq + '" class="rockerSet_count" value="' + typeCount + '" placeholder="라커 수량">\
        <button id="rockerDel' + rockerSeq + '" type="button">삭제</button>\
    </li>';
    $('#pop_set').find('ul').append(tag);
    $('#pop_set > div.content > p > select').html(AttrTag);

    $('#pop_set ul > li').eq(n).find('select').val(String(attrIdx));

}


// 라커리스트 DOM생성
function droingRocker(a){
    $('#rockerList').empty();
    for(var i = 0; i < SET.rockerSet.Type[a][1]; i++){
        var possible = rockerList[a][i][3] == 0 ? possible = 'impossible' : possible = 'possible';
        var use = rockerList[a][i][4] == 0 ? use = 'noUse' : use = 'yesUse';
        var rockerStartTag = '<article class="rocker ' + use + ' ' + possible + '" data-seq="' + rockerList[a][i][0] + '" data-use="' + rockerList[a][i][4] + '"><div>';
        var rockerEndTag = '</div></article>';
        var rocker = $('article.rocker');
        var SEQ = parseInt(rockerList[a][i][5][0]);
        var SEQ_Num = 0;

        for(var ii = 0; ii < memberList.length; ii++){
            memberList[ii].sequence == SEQ ? SEQ_Num = ii : '';
        }

        var content = {
            use : rockerList[a][i][4] == 0 ? 
                    this.use = '' : 
                    this.use = '<p class="useInfo" data-memseq="' + memberList[SEQ_Num].sequence + '">' + 
                                memberList[SEQ_Num].name + '<br><span>이용중</span></p>',
            rockerNum : '<p class="rockerNum">' + (i+1) + '</p>',
            doit : function(){
                $('#rockerList').append(rockerStartTag + this.rockerNum + this.use + rockerEndTag);
                $('.impossible').attr('title','클릭하여 관리가능 합니다.');
            }
        }
        content.doit();
    }
    
    var pop = $('form.pop_add'),
        pop_date_start = $('form.pop_add #pop_rockerStartDate'),
        pop_date_end = $('form.pop_add #pop_rockerEndDate'),
        pop_memName = $('form.pop_add #pop_MemberName'),
        pop_memNum = $('form.pop_add #pop_MemberNum'),
        pop_submit = $('form.pop_add button.submit'),
        pop_close = $('form.pop_add button.close'),
        chanImpossible = $('.impossibleBtn');

    var pop_info = $('div.pop_info'),
        pop_info_img = $('div.pop_info .pop_img'),
        pop_info_name = $('div.pop_info .name'),
        pop_info_num = $('div.pop_info .num'),
        pop_info_date_start = $('div.pop_info #info_rockerStartDate'),
        pop_info_date_end = $('div.pop_info #info_rockerEndDate'),
        pop_info_btn_edit = $('div.pop_info .info_dateEdit'),
        pop_info_btn_editOK = $('div.pop_info .info_dateEdit_ok'),
        pop_info_btn_unuse = $('div.pop_info .info_unuse'),
        pop_info_btn_close = $('div.pop_info .info_close');

    var $Date = new Date();
    var $DateY = $Date.getFullYear(),
        $DateM = String($Date.getMonth()+1).length < 2 ? '0' + $Date.getMonth()+1 : $Date.getMonth()+1,
        $DateD = String($Date.getDate()).length < 2 ? '0' + $Date.getDate() : $Date.getDate();
    var $$Date = $DateY + '-' + $DateM + '-' + $DateD;

    // 라커클릭
    rocker.click(function(){
        var use = parseInt($(this).attr('data-use'));   // 현재사용중인지 (0ㄴㄴ / 1ㅇㅇ)
        var rockerSEQ = $(this).attr('data-seq');    // 라커SEQ (문자열)
        var memberSEQ = $(this).find('.useInfo').attr('data-memseq');
        var rockerListIndex;
        var memberListIndex;
        var a = $('#rockerType').val();     // 현재라커속성
        var impossible;
        var use = use == 0 ? true : false;  // 현재사용중인지 (true이용가능 / false회원 이용중)
        
        for(var i = 0; i < rockerList[a].length; i++){
            if(rockerSEQ == rockerList[a][i][0]){
                var impossible = rockerList[a][i][3] == 0 ? 0 : 1;
                var rockerListIndex = i;
                // 이용불가는 아닌지 (true이용불가 / false이용가능)
                break;
            }
        }
        
        for(var m = 0; m < memberList.length; m++){
            if(memberSEQ == memberList[m].sequence){
                var memberListIndex = m;
                break;
            }
        }

        // 팝업데이터 첨부 / Default값
        $('.pop_add > h2').html('No.<span id="pop_rockerNum"></span> - ' + SET.rockerSet.Attr[a][0] + ' ' + SET.rockerSet.Type[SET.rockerSet.Attr[a][1]][0]);
        $('#pop_rockerNum, #pop_rockerNum2').text(parseInt(rockerSEQ));
        pop_date_start.add(pop_date_end).val($Date);


        if(impossible == 0){
            var ask = confirm('현재 사용이 불가능한 라커입니다.\n활성화 하시겠습니까?');
            if(ask){
                rockerList[$('#rockerType').val()][rockerListIndex][3] = 1;
            }

        }else{

            if(use){    // 이용가능
                // 라커 지정 모듈창 띄우기 (이용하기, 이용불가변경)
                pop_memName.add(pop_memNum).val('');
                pop.add($('.dark_div')).fadeIn(200);
                pop_date_start.add(pop_date_end).val($$Date);

            }else{      // 이용중
                // 현재 사용라커 정보보기 (정보보기,이용해지)
                pop_info.add($('.dark_div')).fadeIn(200);
                pop_info_name.html(memberList[memberListIndex].name + ' <small>(' + memberList[memberListIndex].age + '세)</small>');
                pop_info_num.text(memberList[memberListIndex].phone);
                pop_info_date_start.val(rockerList[a][rockerListIndex][5][1]);
                pop_info_date_end.val(rockerList[a][rockerListIndex][5][2]);
                pop_info_date_start.add(pop_info_date_end).removeClass('edit');
                pop_info_btn_edit.show();
                pop_info_btn_editOK.hide();
            }
        }

        $('.pop_add > section.pop_content > div.rowDate button').click(function(){
            var m = parseInt($(this).text());
            var arrDate = pop_date_start.val().split('-');
            var date = new Date(arrDate);
            
            date.setMonth(date.getMonth()+m);
            var dateM = String(date.getMonth()+1).length < 2 ? '0'+(date.getMonth()+1) : date.getMonth()+1;
            var dateD = String(date.getDate()).length < 2 ? '0'+date.getDate() : date.getDate();

            pop_date_end.val(date.getFullYear() + '-' + dateM + '-' + dateD);
            console.log(date.getFullYear() + '-' + dateM + '-' + dateD);
        });


        // 배정 하기 (submit)
        pop_submit.click(() => {
            rockerList[a][rockerListIndex][4] = 1;
            rockerList[a][rockerListIndex][5][0] = Number(pop_memName.attr('data-memseq'));
            rockerList[a][rockerListIndex][5][1] = pop_date_start.val();
            rockerList[a][rockerListIndex][5][2] = pop_date_end.val();
            pop.add($('.dark_div')).fadeOut(150);

            droingRocker(a);
        });

        // 이용중인 회원라커 -> 이용기간 변경
        pop_info_btn_edit.click(() => {
            pop_info_btn_edit.hide(); pop_info_btn_editOK.show();
            pop_info_date_start.add(pop_info_date_end).addClass('edit').removeAttr('readonly');
        });

        // 변경완료
        pop_info_btn_editOK.click(() => {
            rockerList[a][rockerListIndex][5][1] = pop_info_date_start.val();
            rockerList[a][rockerListIndex][5][2] = pop_info_date_end.val();
            pop_info.add($('.dark_div')).fadeOut(150);
            alert('변경되었습니다.');
            droingRocker(a);
        });

        // 이용중인 회원라커 -> 이용해제
        pop_info_btn_unuse.click(() => {
            if(confirm('현재이용중인 라커를 해제하시겠습니까?\n해제하시면 이용중인 회원의 정보는 삭제됩니다.')){
                rockerList[a][rockerListIndex][4] = 0;
                rockerList[a][rockerListIndex][5] = [0];
                pop_info.add($('.dark_div')).fadeOut(150);
                droingRocker(a);
            }else{
                return false;
            }
        });



        // 팝업 닫기
        pop_close.click(() => pop.add($('.dark_div')).fadeOut(150) );
        pop_info_btn_close.click(() => pop_info.add($('.dark_div')).fadeOut(150) );

        // 이용불가 만들기
        chanImpossible.click(() => {
            // var ask = ;
            if(confirm('해당라커를 이용불가로 하시겠습니까?')){
                rockerList[a][rockerListIndex][3] = 0;
                pop_close.click();
                droingRocker(a);
                return false;
            }else{
                return false;
            }
        });
        
        droingRocker(a);


    });

    
}


// 문서가 로드 되고 할일
$(function(){
    
    
    // DOM 변수
    const rockerType = $('#rockerType');
    const rockerName = $('#rockerName');
    const rockerSetBtn = $('#rockerSetBtn');

    // default 값
    droingRocker(0);
    rockerName.text(SET.rockerSet.Type[0][0] + ' (' + SET.rockerSet.Attr[SET.rockerSet.Type[0][2]][0] + ')');

    // 라커관리 버튼
    rockerSetBtn.click(function(){
        $('.dark_div').add($('#pop_set')).fadeIn(200);
    })

    rockerType.empty();
    for(var i=0; i<SET.rockerSet.Type.length; i++){
        TypeSelect(rockerType,i);
    }

    

    rockerType.change(function(){
        var val = $(this).val();
        rockerName.text(
            SET.rockerSet.Type[val][0] + 
            ' (' + SET.rockerSet.Attr[SET.rockerSet.Type[val][2]][0] + ') '
        );
        droingRocker(val);
    });



    // 회원검색 클릭시
    $('#pop_searchMember').click(()=>{
        $('.mSearch_container').delay(100).fadeIn(200);
        $('#pop_MemberName, #pop_MemberNum').prop('readonly',true).removeClass('input');
    });

    // 비회원이용 클릭시
    $('.notMember_use').click(function(){
        $('#pop_MemberName, #pop_MemberNum').val('');
        $('#pop_MemberName, #pop_MemberNum').prop('readonly',false).addClass('input');
        $('#pop_MemberName').focus();
    });

    
    






// 작업용
// rockerSetBtn.click();



});