
$(function(){
    sessionStorage.clear();
    // 우클릭 제한
    $('body').on('selectstart, contextmenu',() => {
        return false;
    });

    var joinBtn = $('#joinBtn');

    joinBtn.click(() => {
        on();
        setTimeout(off, 1500);
    });

    function on(){
        joinBtn.find('span').hide(200);
        joinBtn.find('div').animate({width: '50%', height:'100%'},200);
    }
    function off(){
        joinBtn.find('span').show(200);
        joinBtn.find('div').animate({width: '0%', height:'0%'},200);
    }

});