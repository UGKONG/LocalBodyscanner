$(function(){

    // 아이디 찾기 버튼
    $('#findIDFrm').submit(function(e){
        var name = $('#FIND_ID_NAME').val();
        var email = $('#FIND_ID_EMAIL').val();

        var form = new FormData();
            form.append('USERNAME', name);
            form.append('EMAIL', email);

        e.preventDefault();

        $.ajax({
            url: 'flow_controller.php?task=FindUserID',
            data: form,
            method: 'POST',
            contentType: false,
            processData: false,
            success: function (result) {
                let data = JSON.parse(result);
                if (data.result == 'Fail') {
                    alert('존재하지 않는 사용자입니다.\n회원가입을 진행해주세요.');
                    return;
                }
                alert(name + '님의 아이디를 ' + email + '로 전송하였습니다.');
            },
            error: function (error) {
                console.error(error);
            }
        });
        
    });

    // 임시비밀번호 발급 버튼
    $('#findPWFrm').submit(function(){
        alert('임시비밀번호가 이메일로 전송되었습니다.');
    });
});