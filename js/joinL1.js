sessionStorage.join1 = 0;
sessionStorage.join2 = 0;
sessionStorage.join3 = 0;
sessionStorage.joinName = '';
sessionStorage.joinPhone = '';


$(function(){

    var termsNo_btn = $('#termsNo'),        // 동의 안함 버튼
        termsYes_btn = $('#termsYes');      // 동의 후 정보입력 버튼
    var check = $('.termsCheck'),
        checkAll = $('#termsAllCheck');
    var checkList = [];

    var termsText1 = $('#termsText1'),
        termsText2 = $('#termsText2'),
        termsText3 = $('#termsText3');
        
    termsText1.load('terms/terms1.txt');
    termsText2.load('terms/terms2.txt');
    termsText3.load('terms/terms3.txt');

    // $('.termsTemp1').load('terms/terms1.txt');
    // $('.termsTemp2').load('terms/terms2.txt');


        
    // 전체체크 클릭
    checkAll.click(function(){
        if($(this).is(':checked')){
            check.prop('checked',true);
        }else{
            check.prop('checked',false);
        }
    });

    // 아무 체크박스 클릭
    $('input[type="checkbox"]').click(function(){
        checkList = [];
        check.each(function(){
            if($(this).is(':checked')){
                checkList.push(true);
            }
        });
    });

    // 이용약관/개인정보동의 체크박스 클릭
    check.click(function(){
        if(checkList[0],checkList[1],checkList[2]){
            checkAll.prop('checked',true);
        }else{
            checkAll.prop('checked',false);
        }
    });


    // -- 버튼 -- //
    termsNo_btn.click(function(){       // 취소
        location.href = 'login.php';
    });

    termsYes_btn.click(function(){      // 확인
        if(checkList[0],checkList[1],checkList[2]){
            sessionStorage.join1 = 1;
            location.href = 'joinL2.php';
        }else{
            alert('이용약관과 개인정보동의에 대한 약관을 동의해주세요.');
            return 0;
        }
    });
    // --------- //

    

});