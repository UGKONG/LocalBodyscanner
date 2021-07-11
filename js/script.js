sessionStorage.page = null;

$(function(){
    // 사용 할 색상
    // ['#ffc000', '#5b9bd5', '#70ad47', '#a86ed4', '#ed7d31', '#ff6666']

// //////////////////////////////////////////////////////////////////////////////////////

    $(document).bind('keydown',function(e){
        if ( e.keyCode == 123 /* F12 */) {
            e.preventDefault();
            e.returnValue = false;
        }
    });

    $('[name="search_frm"]').submit(function(e){
        e.preventDefault();
    });
    $('#searchBtn').click(function(){
        var val = $('#searchVal').val();
        if(val != ''){
            sessionStorage.searchMemberName;
            sessionStorage.searchMemberName = val;
            location.href = 'members.php';
        }
    });

    // 마우스 우클릭 막기
    $('body').on('selectstart',function(){
        return false;
    });
    $('body').on('contextmenu',function(){
        return false;
    });
    $('body').on('dragstart',function(){
        return false;
    });

    // 버튼효과
    function btnActive(){
        $(this).addClass('active').siblings().removeClass('active');
    }

    // 메뉴에 마우스 오버
    var $menu = $('header > nav > ul > li'),
        $menuBar = $('header > nav > div.menuUnderbar');

    $menu.hover(function(){
        var width = $(this).outerWidth();
        var left = $(this).offset().left;
        $menuBar.css({
            width: width,
            left: left-310,
            opacity: 1
        });
    },function(){
        $menuBar.css({opacity: 0});
    });

    // Top 검색창
    $('#searchVal').focus(function(){
        $(this).siblings('label').hide();
    }).blur(function(){
        if($(this).val() != ''){
            $(this).siblings('label').hide();
        }else{
            $(this).siblings('label').show();
        }
    });

    $('div.loginUser_info > p:nth-of-type(1)').click(function(){
        location.href='trainerinfo.php?USER_SQ=' + $USER_SQ;
    })

    // 로그아웃 (로그인페이지로)
    const $loginUser = $('.loginUser_info');
    $('.loginUser').click(function(e){
        e.preventDefault();
        $loginUser.stop().fadeToggle(200);
    });
    // $loginUser.find('p').eq(2).click(function(){
    //     location.href = "login.php";
    // });


    
    // //////////////////////////////////////////////////////////////////////////////

    


    $('#send_uName').add($('#groupSearchMember')).click(function(){
        $('div.mSearch_container').fadeIn(200);
    });


    // 회원 검색
    $('div.mSearch_container #mSearchText').keyup(function(){
        var text = $(this).val().toLowerCase();
        $('div.mSearch_container ul.name_list li').filter(function(){
            $(this).toggle(
                $(this).text().toLowerCase().indexOf(text) > -1
            );
        });
    });

    // 회원명검색 회원리스트
    function memberList_MakeDOM(r){
        var name = memberList[r].Name;
        var seq = memberList[r].Sequence;
        var age = memberList[r].Age;
        var phone = memberList[r].Phone;
        var list = '<li data-seq="' + seq + '"><div>'+name+'</div><div><p>'+age+'세</p><p>' + phone + '</p></div></li>';
        $('#mSearchText').siblings('.name_list').append(list);
    }

    // 멤버리스트
    // $('#mSearchText').siblings('.name_list').empty();
    // for(var i=0;i<memberList.length;i++){
    //     memberList_MakeDOM(i);
    // }



    $('div.mSearch_container > ul > li').click(function(){
        $(this).parent().parent().fadeOut(200);
        $('#send_uName').val($(this).children('div:first-of-type').text());
        $('.mSearchText').val($(this).children('div:first-of-type').text());
        $('#solo_searchName').val($(this).children('div:first-of-type').text());
        $('#groupSearchMember').val($(this).children('div:first-of-type').text());
        $('#pop_MemberName').val($(this).children('div:first-of-type').text());
        $('#pop_MemberName').attr('data-memseq',$(this).attr('data-seq'));
        $('#pop_MemberNum').val($(this).children('div').find('p:last-of-type').text());
        $('#send_uNum').val($(this).children('div').find('p:last-of-type').text());
        $('.mSearchText').val('');
    });


    $('#send_uTeacher').change(function(){
        $('#send_tNum').val('010-0000-0000');
    });

    // 회원찾기 X 버튼
    $('div.mSearch_container .x_btn').click(function(){
        $('div.mSearch_container').fadeOut(200);
    });

    






    GRADE_YN();


});


function GRADE_YN(){
    if($USER_GRADE < 3){
        var formData = new FormData();
        formData.append('USER_SQ', $USER_SQ);
        
        $.ajax({
            url: "flow_controller.php?task=getUserAuthority",
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(result){
                $USER_GRADE_LIST = JSON.parse(result);
                GARDE_DO();
                GRAGE_CHK(sessionStorage.page, $('.grade.tab'));
            },
            error: function (e) {
                alertApp('!', '새로고침해주세요.');
                return false;
            }
        });
    }
}

function GRAGE_CHK(page, target) {
    if(page == 'null') {
        return false;
    }

    if($USER_GRADE < 3){
        if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 71) == -1){
            if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 72) == -1){
                if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 73) == -1){
                    if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 74) == -1){
                        alert('권한이 없습니다.');
                        history.back();
                        return;
                    }
                    target.eq(3).click();
                }
                target.eq(2).click();
            }
            target.eq(1).click();
        }
        target.eq(0).click();
        return;
    }
    target.eq(0).click();
}

function GARDE_DO(){
    gradeMenuView();

    if($USER_GRADE < 3){
        if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 87) == -1){
            $('.content > div.down > ul.tab_menu > li').eq(4).hide();
            $('.content > div.down > ul.tab_menu > li').eq(5).hide();
        }
    }

    GRAGE_CHK(sessionStorage.page, $('article.list div.tab.grade'));
}

function gradeMenuView(){
    $('#headerApp ul > li').show();
    if($USER_GRADE_LIST.findIndex(e => e.AUTH_CD == 1) == -1){
        $('#headerApp ul > li').eq(2).hide();
    }
    if($USER_GRADE_LIST.findIndex(e => e.AUTH_CD == 2) == -1){
        $('#headerApp ul > li').eq(3).hide();
    }
    if($USER_GRADE_LIST.findIndex(e => e.AUTH_CD == 3) == -1){
        $('#headerApp ul > li').eq(4).hide();
    }
    if($USER_GRADE_LIST.findIndex(e => e.AUTH_CD == 4) == -1){
        $('#headerApp ul > li').eq(5).hide();
    }
    if($USER_GRADE_LIST.findIndex(e => e.AUTH_CD == 5) == -1){
        $('#headerApp ul > li').eq(6).hide();
    }
    if($USER_GRADE_LIST.findIndex(e => e.AUTH_CD == 6) == -1){
        $('#headerApp ul > li').eq(7).hide();
    }
    if($USER_GRADE_LIST.findIndex(e => e.AUTH_CD == 7) == -1){
        $('#headerApp ul > li').eq(8).hide();
    }
}