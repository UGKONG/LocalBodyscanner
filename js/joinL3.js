if (sessionStorage.join2 == 0) location.href = 'joinL1.php';

const defaultValue = () => {
    let [name, num1, num2, num3] = [$('#JOIN_NAME'), $('#JOIN_PHONE0'), $('#JOIN_PHONE1'), $('#JOIN_PHONE2')];
    let [tempNum1, tempNum2, tempNum3] = sessionStorage.joinPhone.split('-');
    name.val(sessionStorage.joinName);
    num1.val(tempNum1);
    num2.val(tempNum2);
    num3.val(tempNum3);
    
    name.add(num1).add(num2).add(num3).prop('disabled', true);
    $('#JOIN_PW1').val(tempNum3);

    useAjax('getCenterList', (data) => {
        let json = JSON.parse(data);
        json.forEach((center) => {
            $('#CENTER_NAME ul').append(`<li data-sq="${center.CENTER_SQ}">${center.CENTER_NM}</li>`);
        });
        $('#CENTER_NAME ul > li').click(function(){
          let sq = $(this).attr('data-sq');
          let text = $(this).text();

          $('.centerLabel').text(text).attr('data-sq', sq);
          $('#CENTER_NAME').removeClass('on');
        });
    })
}

$(function(){
    
    defaultValue();

    $('#JOIN_PW1').hover(function(){
        $(this).prop('type', 'text');
    }, function(){
        $(this).prop('type', 'password');
    });

    var ischeck_id = false;
    const ID_alert = $('#id_alert');
    const PW_alert = $('#pw_alert');
    var DATE = new Date();

    // 마우스 우클릭 막기
    $('body').on('selectstart, contextmenu',function(){
        return false;
    });

    // 아이디 중복확인 버튼 클릭
    $('#id_overlapping_Btn').click(() => {
        var id = $('#JOIN_ID').val();
        if(id == ''){
            ID_alert.html('<span></span>이메일을 입력해주세요.');
            ID_alert.animate({height: '20px'},200);
            $('#JOIN_ID').focus();
            return 0;
        }else if(id.length < 3){
            ID_alert.html('<span></span>4자리 이상으로 입력해주세요.');
            ID_alert.animate({height: '20px'},200);
            $('#JOIN_ID').focus();
            return 0;
        }else if(id.search(/[@]/g) == -1){
            ID_alert.html('<span></span>이메일(@) 양식으로 입력해주세요.');
            ID_alert.animate({height: '20px'},200);
            $('#JOIN_ID').focus();
            return 0;
        }else{
            // DB에서 중복검사 실행
            if(!ischeck_id){
                            
                // 중복없음 //
                var ask = confirm('회원가입이 가능한 아이디입니다. 사용하시겠습니까?');
                if(ask){
                    ischeck_id = true;
                    $('#JOIN_ID').attr('readonly',true).css({color: '#999'});
                    $('#CENTER_NAME').focus();
                }

                // 중복있음 //
                // alert('이미 가입되어있는 아이디입니다.\n다른 아이디를 입력해주세요.');
                // return 0;

            }else{
                alert('이미 중복확인이 완료되었습니다.');
            }


        }
    });

    // 연락처 뒷자리 입력할때
    $('#JOIN_PHONE2').keyup(function(){
        var value = $(this).val();
        var pwText1 = $('#JOIN_PW1');
        pwText1.val(value);
        value.length != 0 ? PW_alert.animate({height:'20px'},200) : PW_alert.animate({height:0},200);
    });

    // 아이디를 키보드로 입력할때
    $('#JOIN_ID').keyup(() => {
        ID_alert.html('<span></span>');
        ID_alert.animate({height:0},200);
    });


    // 생년월일
    var JOIN_Y = $('#JOIN_YEAR'),
        JOIN_M = $('#JOIN_MONTH');
        
    JOIN_Y.append('<option value="">선택</option>');
    for(y = 1900; y < DATE.getFullYear()+1; y++){
        JOIN_Y.append('<option value="' + y + '">' + y + '</option>');
    }

    JOIN_M.append('<option value="">선택</option>');
    for(m = 1; m < 13; m++){
        JOIN_M.append('<option value="' + m + '">' + m + '</option>');
    }

    
    $('#date_time').text(
        DATE.getFullYear()+'-'+DATE.getMonth()+'-'+DATE.getDate()+' '+
        DATE.getHours()+':'+DATE.getMinutes()+':'+DATE.getSeconds()
    );
    setTimeout(() => {
        var joinDate = new Date();
        $('#date_time').text(
            joinDate.getFullYear()+'-'+joinDate.getMonth()+'-'+joinDate.getDate()+' '+
            joinDate.getHours()+':'+joinDate.getMinutes()+':'+joinDate.getSeconds()
        );
    }, 3000);

    $('#JOIN_submitBtn').click(function(){
        ischeck_id = true;      // 임시 (이메일 중복?인증?)

        var NAME = $('#JOIN_NAME'),
            NUM0 = $('#JOIN_PHONE0'),
            NUM1 = $('#JOIN_PHONE1'),
            NUM2 = $('#JOIN_PHONE2');

        var Y = String($('#JOIN_YEAR').val()),
            M = String($('#JOIN_MONTH').val()).length == 1 ? '0' + String($('#JOIN_MONTH').val()) : String($('#JOIN_MONTH').val()),
            D = String($('#JOIN_DAY').val()).length == 1 ? '0' + String($('#JOIN_DAY').val()) : String($('#JOIN_DAY').val());

        if(!ischeck_id){
            alert('이메일 계정에 대한 중복확인을 해주세요.');
            $('#JOIN_ID').focus();
            return false;
        }else if($('#JOIN_ID').val() == '') {
            alert('이메일을 입력해주세요.');
            return false;
        }else if(!$('.centerLabel').attr('data-sq')){
            alert('센터를 선택해주세요.');
            return false;
        }else if($('#JOIN_YEAR').val() != '' || $('#JOIN_MONTH').val() != '') {
          if($('#JOIN_DAY').val() == '') {
            alert('생년월일을 정확하게 입력해주세요.');
            return false;
          }
        }

        console.log('회원가입실행');
        const joinCallback = (resData) => {
          let data = JSON.parse(resData);
          if(data.result == 'Fail') {
            alert('회원가입 오류!!\n다시 진행해주세요.');
            location.href = 'joinL1.php';
            return;
          }else if(data.result == 'Success') {
            sessionStorage.join3 = 1;
            sessionStorage.id = data.reason;
            location.href = 'joincomplete.php';
          }
        }
        useAjax('execManagerReg', joinCallback, {
          CENTER_SQ: $('#CENTER_NAME').val(),
          USERNAME: NAME.val(),
          BIRTH_DT: Y + '-' + M + '-' + D,
          GENDER: $('#male').prop('checked') ? 'M' : 'F',
          TEL: NUM0.val() + '-' + NUM1.val() + '-' + NUM2.val(),
          EMAIL: $('#JOIN_ID').val(),
          ADDRESS: $('#JOIN_ADDRESS4').val()
        });

        // $('#joinFrm').submit();
        // location.href = 'joincomplete.php';
        

        
    });

    $('#JOIN_cancelBtn').click(function(){
        var msg = confirm('입력된 내용이 지워집니다. 취소하시겠습니까?');
        if(msg){
            location.href='login.php';
        }else{
            return false;
        }
    });

    $('h2.choice_input').click(function(){
        $(this).add($(this).find('i')).toggleClass('on');
        $('form > .choice_input_section').slideToggle(300);
        // $('html').toggleClass('on');
    });

    ///////////////////////////////////////////////////////////
    
    $('.centerLabel').click(function(){
      $(this).parent().toggleClass('on');
    });
    $('#centerSearch').focus(function(){
      $('#CENTER_NAME').addClass('on');
    });
    $('#centerSearch').keyup(function(){
      let val = $(this).val().toLowerCase();

      const li = $('ul#CENTER_NAME ul > li');

      li.each(function(){
        let liText = $(this).text();
        if (liText.indexOf(val) > -1) {
          $(this).show();
        } else {
          $(this).hide();
        }
      });
      
    });

});