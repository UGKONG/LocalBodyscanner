var PWval = '';
$(function(){


    var ID = $('#CENTER_ADMIN_ID'),
        DOMAIN = $('#CENTER_ADMIN_DOMAIN'),
        DOMAIN_CHOICE = $('#CENTER_ADMIN_DOMAIN_CHOICE');
    var PW1 = $('#CENTER_ADMIN_PW1'),
        PW2 = $('#CENTER_ADMIN_PW2');
    var CENTER_TYPE = $('#CENTER_TYPE'),
        CENTER_NAME = $('#CENTER_NAME'),
        CENTER_SEQ = $('#CENTER_SEQ'),
        CENTER_KING_NAME = $('#CENTER_KINGNAME'),
        CENTER_KING_NUM1 = $('#CENTER_KINGPHONE1'),
        CENTER_KING_NUM2 = $('#CENTER_KINGPHONE2'),
        CENTER_KING_NUM3 = $('#CENTER_KINGPHONE3');
    var SENDBTN = $('#sendNumBtn'),
        SENDNUM = $('#sendNum'),
        SENDNUMOKBTN = $('#sendNumOKBtn');
    var SUBMIT = $('#submitBtn'),
        CANCEL = $('#cancelBtn');

    var pwChecked = false;
    var sendChecked = false;
    var completeChecked = false;
   
    // 이메일 도메인선택
    DOMAIN_CHOICE.change(function(){
        var val = $(this).val();
        val == '' ? DOMAIN.val('').focus() : DOMAIN.val(val);
    });
    // 도메인을 직접 입력하면 [직접입력]으로 변경
    DOMAIN.keyup(()=>DOMAIN_CHOICE.val(''));

    // 비밀번호 확인
    PW1.keyup(()=>{pw1(PW1);pw2(PW2);}).blur(()=>{pw1(PW1);pw2(PW2);});
    PW2.keyup(()=>{pw2(PW2);}).blur(()=>{pw2(PW2);});

    // 비밀번호 정규식 함수
    function pw1(p){
        PWval = p.val();
        if(PWval.search(/[0-9]/g) == -1 || PWval.search(/[a-z]/ig) == -1 || PWval.indexOf(' ') > -1 || PWval.length < 6 ){
            p.siblings('i').removeClass('ok');
            pwChecked = false;
        }else if(PWval.search(/[0-9]/g) > -1){
            p.siblings('i').addClass('ok');
            pwChecked = true;
        }
    }

    // 비밀번호 확인 함수
    function pw2(p){
        var val = p.val();
        if(val != PWval || PWval == ''){
            p.siblings('i').removeClass('ok');
            pwChecked = false;
        }else{
            p.siblings('i').addClass('ok');
            pwChecked = true;
        }
    }

    // 인증번호 요청 클릭
    SENDBTN.click(() => {
        var email = ID.val() + DOMAIN.val();    // 대표자 email
        if (sendChecked) {
            alert('재전송되었습니다.');
        }else{
            KINGNUM(email);
        }
    });

    // 대표자 연락처 정규식
    function KINGNUM(email){
        var num1 = ID.val(),
            num2 = DOMAIN.val();
    
        if(num1.length < 3){
            ID.val('').focus();
        }else{
            alert('인증번호가 전송되었습니다.');
            sendChecked = true;
            SENDBTN.addClass('ok').text('재 전 송');
        }

    }

    // 인증번호 확인
    SENDNUMOKBTN.click(function(){
        var num = SENDNUM.val();
        // 인증번호를 전송을 하였는지 판단.
        if (sendChecked) {  //인증번호 전송완료
            if (num.search(/[a-zA-Z]/g) > -1 || num.length < 6) {
                alert('인증번호를 다시 입력해주세요.');
                SENDNUM.focus();
                return false;
            }else{
                alert('인증이 완료되었습니다.');
                SENDNUMOKBTN.addClass('complete').text('완 료');
                completeChecked = true;
            }

        }else{
            alert('인증번호를 요청해주세요.');
            return false;
        }
    });


    // 등록버튼
    SUBMIT.click( () => {
        if(!pwChecked){
            alert('비밀번호를 다시 확인해주세요.');
            return false;
        }else if(!sendChecked){
            alert('인증번호를 전송해주세요.');
            return false;
        }else if(!completeChecked){
            alert('인증번호를 완료해주세요.');
            return false;
        }else if(pwChecked,sendChecked,completeChecked == true){
            // $('#centerFrm').submit();
            location.href = 'joincomplete.php';
        }
    });

});