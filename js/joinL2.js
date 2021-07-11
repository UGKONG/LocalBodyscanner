if (sessionStorage.join1 == 0) location.href = 'joinL1.php';
sessionStorage.joinName = '';
sessionStorage.joinPhone = '';

$(function(){

    var NAME = $('#JOIN_NAME'),
        NUM0 = $('#JOIN_PHONE0'),
        NUM1 = $('#JOIN_PHONE1'),
        NUM2 = $('#JOIN_PHONE2');
    var ischeck_user = false;

    // 중복확인 버튼 클릭
    $('#User_overlapping_Btn').click(() => {
        // 유저 이름
        var USER_NAME = $('#JOIN_NAME').val();
        // 유저 연락처
        var USER_NUM1 = $('#JOIN_PHONE0').val(),
            USER_NUM2 = $('#JOIN_PHONE1').val(),
            USER_NUM3 = $('#JOIN_PHONE2').val();

        if(USER_NAME == '' || USER_NUM2 == '' || USER_NUM3 == ''){      //입력을 안함.
            alert('이름과 연락처를 입력해주세요.');
            $('#JOIN_NAME').focus();
            return 0;
        }else if(USER_NUM2.length < 3 || USER_NUM3.length < 3){     //자릿수 검사
            alert('연락처를 다시 입력해주세요.');
            $('#JOIN_PHONE1').focus();
            return 0;
        }else if(USER_NAME.search(/\s/) > -1){                  //공백 검사
            alert('공백 없이 입력해주세요.');
            $('#JOIN_NAME').focus();
            return 0;
        }else if(USER_NUM2.search(/[a-z]/ig) > -1 || USER_NUM3.search(/[a-z]/ig) > -1){
            alert('연락처를 숫자로 입력해주세요.');
            $('#JOIN_PHONE1').focus();
            return 0;
        }else{

            let form = new FormData();
                form.append('USERNAME', USER_NAME);
                form.append('TEL', USER_NUM1 + '-' + USER_NUM2 + '-' + USER_NUM3);

            $.ajax({
                url: 'flow_controller.php?task=UserDupCheck',
                method: 'POST',
                data: form,
                contentType: false,
                processData: false,
                success: function (result) {
                    let data = JSON.parse(result);
                    if (data.result == 'Fail') {
                        alert('이미 가입되어 있습니다.\n아이디 또는 비밀번호를 찾아주세요.');
                        ischeck_user = true;
                        return;
                    }
                    ischeck_user = false;

                    // 유효성 검사 종료 DB에서 중복체크
                    if(!ischeck_user){
                        
                        // 중복없음 //
                        var ask = confirm('회원가입이 가능합니다. 가입하시겠습니까?');
                        if(ask){
                            ischeck_user = true;
                            NAME.add(NUM0).add(NUM1).add(NUM2).attr('readonly',true).css({color: '#999'});
                            sessionStorage.join2 = 1;
                        }

                    }else{
                        alert('이미 중복확인을 하셨습니다.');
                        $('#JOIN_ID').focus();
                    }
                },
                error: function (error) {

                }
            });

            
            
            

        }
        
    });


    // 다음 버튼
    $('#JOIN_submitBtn').click(function(){
        if(ischeck_user){
            sessionStorage.joinName = $('#JOIN_NAME').val();
            sessionStorage.joinPhone = $('#JOIN_PHONE0').val() + '-' + $('#JOIN_PHONE1').val() + '-' + $('#JOIN_PHONE2').val();
            location.href='joinL3.php';
        }else{
            alert('중복확인을 해주세요.');
        }
    });

    // 취소 버튼
    $('#JOIN_cancelBtn').click(function(){
        var msg = confirm('입력된 내용이 지워집니다. 취소하시겠습니까?');
        if(msg){
            location.href='login.php';
        }else{
            return false;
        }
    });

});