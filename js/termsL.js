$(function(){

    var termsNo_btn = $('#termsNo'),        // 동의 안함 버튼
        termsYes_btn = $('#termsYes');      // 동의 후 정보입력 버튼
    var check = $('.termsCheck'),
        checkAll = $('#termsAllCheck');
    var checkList = [];

    var termsText1 = $('#termsText1'),
        termsText2 = $('#termsText2');

    $('.termsTemp1').load('terms/terms1.txt');
    $('.termsTemp2').load('terms/terms2.txt');

    termsText1.load('terms/terms1.txt');
    termsText2.load('terms/terms2.txt');

        
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
        if(checkList[0],checkList[1]){
            checkAll.prop('checked',true);
        }else{
            checkAll.prop('checked',false);
        }
    });


    // -- 버튼 -- //
    termsNo_btn.click(function(){       // 동의 안함
        alert('이용약관과 개인정보동의에 대한 약관을 동의해주세요.');
    });

    termsYes_btn.click(function(){      // 정보 입력
        if(checkList[0],checkList[1]){
            location.href = 'joinL.html';
        }else{
            alert('이용약관과 개인정보동의에 대한 약관을 동의해주세요.');
            return 0;
        }
    });
    // --------- //

    

});