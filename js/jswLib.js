// 연락처 자동으로 하이픈 추가
function autoHyphen($,data){
    $.val( data.replace(/[^0-9]/g, "").replace(/(^02|^0505|^1[0-9]{3}|^0[0-9]{2})([0-9]+)?([0-9]{4})$/,"$1-$2-$3").replace("--", "-") );
}

// 날짜 포멧변경 함수
function dateFormat(value){
    var date = new Date(value),
        year = date.getFullYear(),
        month = String(date.getMonth()+1).length == 1 ? '0' + String(date.getMonth()+1) : String(date.getMonth()+1),
        day = String(date.getDate()).length == 1 ? '0' + String(date.getDate()) : String(date.getDate());

    return year + '-' + month + '-' + day;
}

// 시간 포멧변경 함수
function timeFormat(value){
    var time = new Date(value),
        hours = String(time.getHours()).length == 1 ? '0' + String(time.getHours()) : String(time.getHours()),
        minutes = String(time.getMinutes()).length == 1 ? '0' + String(time.getMinutes()) : String(time.getMinutes()),
        seconds = String(time.getSeconds()).length == 1 ? '0' + String(time.getSeconds()) : String(time.getSeconds());

    return hours + ' : ' + minutes + ' : ' + seconds;
}

// 숫자 포멧변경 함수 (3자리 콤마)
function numberFormat(value) {
    return String(value).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// 이미지 선택시 이미지뷰어
function fileImageView(input,img) {
    var fileEl = input[0];
    if (fileEl.files && fileEl.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            img.attr('src', e.target.result);
        }
        reader.readAsDataURL(fileEl.files[0]);
    }
}

// 생년월일로 나이계산 함수
function birth_year(date){
    if(date == ""){
        return '-';
    }else if(date != null){
        var yourYear = Number(date.split('-')[0]);
        var nowYear = Number(new Date().getFullYear());
        return (nowYear - yourYear + 1) + '세';
    }else{
        return '-';
    }
    
}

// 해당 날짜가 속한 주 날짜 전부 불러오기
function addArrayText(startDate){
    var date = new Date(startDate);
    if(date.getDay() == 0){
        date.setDate(date.getDate() - date.getDay() - 7 + 1);
    }else{
        date.setDate(date.getDate() - date.getDay() + 1);
    }
    var tempArr = [];

    for(let i = 0; i < 7; i++){
        tempArr.push(dateFormat(date) + ' ' + ['월','화','수','목','금','토','일'][i]);
        date.setDate(date.getDate() + 1);
    }
    return tempArr;
}

// 알림 함수
function alertApp(icon,text){
    $('#alertApp').remove();
    const set = {
        speed : 400,
        wait : 3000,
        width: 400,
        height: 80,
    };
    var tag = '';
        tag += '<div id="alertApp">',
        tag += '<div class="wrap">',
        tag += '<i></i><div class="text"></div>',
        tag += '</div></div></div>';
    $('body').append(tag);
    const el = $('#alertApp');
    const elIcon = el.find('i');
    const elText = el.find('.text');
    var iCode = 
        icon == 'O' ? 'fas fa-check' :
        icon == '!' ? 'fas fa-exclamation-triangle' :
        icon == 'X' ? 'far fa-times-circle' :
                      'fas fa-question';
    el.css({
        width : set.width + 'px',
        height : set.height + 'px',
        right : '30px',
        bottom : -1 * (set.height + 10) + 'px',
        opacity : 0,
        position : 'fixed',
        display : 'block',
        'overflow' : 'hidden',
        'background-color' : '#fff',
        'border-radius' : '4px',
        'box-shadow' : '-1px 2px 5px #00000030',
        'border-top' : '1px solid #186b3d',
        'border-bottom' : '1px solid #186b3d',
        'border-right' : '1px solid #186b3d',
        'z-index' : '9999999999999999999999999999999999999999999',
    });
    el.find('.wrap').css({
        'position' : 'relative',
        'width' : '100%',
        'height' : '100%',
        'padding-right' : '20px'
    });

    elIcon.attr('class',iCode);
    elText.text(text);
    el.stop().animate({
        bottom : 10,
        opacity : 1
    },{
        duration : set.apeed,
        complete : function(){
            setTimeout(() => {
                el.stop().animate({
                    bottom : -1 * set.height + 'px',
                    opacity : 0
                },{
                    duration : set.apeed,
                    complete : function(){
                        el.remove();
                    }
                });
                
            }, set.wait);
        }
    });
}

// 이미지 업로드 후 미리보기
function readImgView(input, to) {
    if (input.files && input.files[0]) {
    var reader = new FileReader();
        reader.onload = function (e) {
            $(to).attr('src', e.target.result);        //cover src로 붙여지고
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// 암호화 (인코딩/디코딩)
function pwEncode(str){
    var str = btoa(unescape(encodeURIComponent(str)));
    return str;
}
function pwDecode(str){
    var str = decodeURIComponent(escape(atob(str)));
    return str;
}

// 비동기 서버호출 함수
function useAjax(taskName, callbackFn, ...otherParams) {     // URL, CallBack, FormData
    let chk1 = taskName == undefined;
    let chk2 = callbackFn == undefined;
    let chk3 = (typeof taskName).toLowerCase() != "string";
    let chk4 = (typeof callbackFn).toLowerCase() != "function";

    if(chk1 || chk2 || chk3 || chk4){
        console.error('Ajax의 콜백함수 형식에 맞지않습니다.');
        console.group('Argument');
        console.log('taskName, CallBack(Function), formData Object (생략가능)');
        console.groupEnd('Argument');
        return false;
    }

    let [form] = otherParams.length == 1 ? otherParams : [null];

    fetch('flow_controller.php?task=' + taskName, {
        method: 'POST',
        cache: 'no-cache',
        body: new URLSearchParams(form || {})
    }).then((response) => response.text()).then((responseData) => {
        callbackFn(responseData, otherParams);
    });
}

// 해당 월 [첫날, 끝날]
function START_END_DT_RETURN(dt) {
  let TODAY = dateFormat(dt);
  let date = dt;
  date.setDate(1);
  date.setMonth(date.getMonth() + 1);
  date.setDate(1);
  date.setDate(date.getDate() - 1);

  let END_DT = dateFormat(date);

  date.setDate(1);
  let START_DT = dateFormat(date);
;
  return [TODAY, START_DT, END_DT];
}
