sessionStorage.page = 'history';
HISTORY_LIST = [];
HISTORY_GROUP = [];
HISTORY_CATEGORY = [];
USER_LIST = [];
SELECTED_GROUP = 1;

function GET_HISTORY(START_DT, END_DT, GROUP){

    var formData = new FormData();
        formData.append('START_DT', START_DT);
        formData.append('END_DT', END_DT);

    $.ajax({
        url: 'flow_controller.php?task=GetHistoryList',
        method: 'POST',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function(r){
            var data = r.split('|');
            HISTORY_LIST = JSON.parse(data[0]);
            HISTORY_GROUP = JSON.parse(data[1]);
            HISTORY_CATEGORY = JSON.parse(data[2]);

            droingList(HISTORY_LIST.filter(e => e.GROUP == GROUP));
        },
        error: function(e){
            location.reload();
        }
    });
}

function droingList(list){
    var table = '';
    var option = {
        date : [],
        time : [],
        category : [],
        user : [],
        content : []
    }
    var optionData_date = '',
        optionData_time = '',
        optionData_category = '',
        optionData_user = '',
        optionData_content = '';

    for(var i of list){
        
        $('div.listContent').empty();

        // 배열에 넣기
        option.date.push(i.REG_DT.split(' ')[0]);
        option.category.push(i.CATEGORY);
        option.user.push(i.USER_SQ);

        table = `
                <tr>
                    <td>${i.REG_DT.split(' ')[0]}</td>
                    <td>${i.REG_DT.split(' ')[1]}</td>
                    <td data-sq="${i.CATEGORY}">${i.CATEGORY_NAME}</td>
                    <td data-sq="${i.USER_SQ}">${i.USER_NM}</td>
                    <td>${i.ACTION}</td>
                </tr>
        ` + table;

        USER_LIST.push({USER_SQ: i.USER_SQ, USER_NM: i.USER_NM});
    }
    
    // 중복 제거
    option.date = Array.from(new Set(option.date));
    option.category = Array.from(new Set(option.category));
    option.user = Array.from(new Set(option.user));

    // 테이블 필터 테이터 변수저장
    for(var i of option.date){
        optionData_date += 
            '<option value="' + i + '">' + 
                i + 
            '</option>';
    }
    for(var i of option.category){
        optionData_category += 
            '<option value="' + HISTORY_CATEGORY.filter(e => e.CODE == i)[0].DESCRIPTION + '">' + 
                HISTORY_CATEGORY.filter(e => e.CODE == i)[0].DESCRIPTION + 
            '</option>';
    }
    for(var i of option.user){
        optionData_user += 
            '<option value="' + USER_LIST.filter(e => e.USER_SQ == i)[0].USER_NM + '">' + 
                USER_LIST.filter(e => e.USER_SQ == i)[0].USER_NM + 
            '</option>';
    }

    // 테이블 필터 DOM 적용
    $('#hi_List_date').html('<option value="">날짜</option>' + optionData_date);
    $('#hi_List_category').html('<option value="">카테고리</option>' + optionData_category);
    $('#hi_List_user').html('<option value="">사용자</option>' + optionData_user);
    
    var table = '<table>' + table + '</table>';
    
    // 히스토리 리스트 DOM 적용
    $('div.listContent').html(table);

}





// 문서가 로드 되고 할 일
$(function(){

    const Tab = $('article.list div.tab');
    const List = $('div.listContent');
    const Filter = $('table.hi_list_head select');
    const Search = $('#hi_listSearch');
    const SortingList = $('#sortList');
    
    const SortingDate1 = $('#hi_startDate'),
          SortingDate2 = $('#hi_endDate');
    const SortingDateBtn = $('.top_sMenu > .down > button');
    const SortingDateSubmitButton = $('.hi_search_btn');
    const SortingDateResetButton = $('.hi_search_reset');

    
    var date = new Date();     //오늘날짜
    var date_Y = date.getFullYear(),
        date_M = date.getMonth()+1,
        date_D = date.getDate();

    var dateSet_date1 = new Date(),
        dateSet_date2 = new Date(),
        dateSet_date3 = new Date(),
        dateSet_date4 = new Date(),
        dateSet_date5 = new Date();

    dateSet_date1.setDate(date_D-1);
    dateSet_date2.setDate(date_D-7);
    dateSet_date3.setDate(date_D-30);
    dateSet_date4.setDate(1);
    dateSet_date5.setDate(1);
    dateSet_date5.setDate(dateSet_date5.getDate()-1);

    // Default값
    SortingDate2.val(
        String(date_Y) + '-' + 
        (String(date_M).length == 1 ? '0' + String(date_M) : String(date_M)) + '-' + 
        (String(date_D).length == 1 ? '0' + String(date_D) : String(date_D))
    );
    SortingDate1.val(
        String(date_Y) + '-' + 
        (String(date_M).length == 1 ? '0' + String(date_M) : String(date_M)) + '-' + '01'
    )

    // GET_HISTORY(SortingDate1.val(), SortingDate2.val(), SELECTED_GROUP);
    

    Tab.click(function(){
        var i = $(this).attr('data-sq');
        var that = $(this);
        var callback = () => {
            that.addClass('active').siblings().removeClass('active');
            SELECTED_GROUP = i;
            GET_HISTORY(SortingDate1.val(), SortingDate2.val(), SELECTED_GROUP);
            $('#hi_listSearch').val('');
        }    
        
        if($USER_GRADE == 3) {
            callback(that);
            return;
        }

        if(i == 1){
            if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 71) == -1){
                alertApp('X', '권한이 없습니다.');
                return false;
            }
        }else if(i == 2){
            if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 72) == -1){
                alertApp('X', '권한이 없습니다.');
                return false;
            }
        }else if(i == 3){
            if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 73) == -1){
                alertApp('X', '권한이 없습니다.');
                return false;
            }
        }else if(i == 4){
            if($USER_GRADE_LIST.findIndex(x => x.AUTH_CD == 74) == -1){
                alertApp('X', '권한이 없습니다.');
                return false;
            }
        }
        callback(that);
    });
    
 
    Filter.change(function(){
        var value = $(this).val();
        Filter.val('');
        $(this).val(value);
        List.find('tr').filter(function(){
            $(this).toggle(
                $(this).text().indexOf(value) > -1
            )
        });
    });

    Search.keyup(function(){
        var text = $(this).val().toLowerCase();
        List.find('tr').filter(function(){
            $(this).toggle(
                $(this).find('td:last-of-type').text().indexOf(text) > -1
            )
        })
    });

    SortingList.change(function(){
        var n = 0;
        for(var i = 0; i < Tab.length; i++){
            Tab.eq(i).attr('class').indexOf('active') > -1 ? n = i : '';
            historyList[i].reverse();
        }
        // droingList(n);

    });

    SortingDate2.change(function(){
        var thisDate = $(this).val().split('-')[0] + $(this).val().split('-')[1] + $(this).val().split('-')[2]
        var lastDate = SortingDate1.val().split('-')[0] + SortingDate1.val().split('-')[1] + SortingDate1.val().split('-')[2]
        if(thisDate < lastDate){
            alertApp('!', '날짜를 뒤로 보실 수 없습니다.\n날짜를 다시 선택해 주세요.');
            $(this).val(SortingDate1.val());
        }
    });

    // [오늘 / 어제 / 금주 / 금월 / 전월] 클릭
    SortingDateBtn.click(function(){
        var name = $(this).attr('class');
        if(name == 'today'){            // 오늘
            SortingDate1.add(SortingDate2).val(
                String(date_Y) + '-' + 
                (String(date_M).length == 1 ? '0' + String(date_M) : String(date_M)) + '-' + 
                (String(date_D).length == 1 ? '0' + String(date_D) : String(date_D))
            );
        }else if(name == 'lastday'){            // 어제
            SortingDate1.add(SortingDate2).val(
                String(dateSet_date1.getFullYear()) + '-' +
                (String(dateSet_date1.getMonth()+1).length == 1 ? '0' + String(dateSet_date1.getMonth()+1) : String(dateSet_date1.getMonth()+1)) + '-' + 
                (String(dateSet_date1.getDate()).length == 1 ? '0' + String(dateSet_date1.getDate()) : String(dateSet_date1.getDate()))
            )
        }else if(name == 'lastday7'){            // 7일전
            SortingDate1.val(
                String(dateSet_date2.getFullYear()) + '-' +
                (String(dateSet_date2.getMonth()+1).length == 1 ? '0' + String(dateSet_date2.getMonth()+1) : String(dateSet_date2.getMonth()+1)) + '-' + 
                (String(dateSet_date2.getDate()).length == 1 ? '0' + String(dateSet_date2.getDate()) : String(dateSet_date2.getDate()))
            );
            SortingDate2.val(
                String(date_Y) + '-' + 
                (String(date_M).length == 1 ? '0' + String(date_M) : String(date_M)) + '-' + 
                (String(date_D).length == 1 ? '0' + String(date_D) : String(date_D))
            );
        }else if(name == 'lastday30'){            // 30일전
            SortingDate1.val(
                String(dateSet_date3.getFullYear()) + '-' +
                (String(dateSet_date3.getMonth()+1).length == 1 ? '0' + String(dateSet_date3.getMonth()+1) : String(dateSet_date3.getMonth()+1)) + '-' + 
                (String(dateSet_date3.getDate()).length == 1 ? '0' + String(dateSet_date3.getDate()) : String(dateSet_date3.getDate()))
            );
            SortingDate2.val(
                String(date_Y) + '-' + 
                (String(date_M).length == 1 ? '0' + String(date_M) : String(date_M)) + '-' + 
                (String(date_D).length == 1 ? '0' + String(date_D) : String(date_D))
            );
        }else if(name == 'nowMonth'){
            SortingDate1.val(
                String(dateSet_date4.getFullYear()) + '-' +
                (String(dateSet_date4.getMonth()+1).length == 1 ? '0' + String(dateSet_date4.getMonth()+1) : String(dateSet_date4.getMonth()+1)) + '-' + 
                (String(dateSet_date4.getDate()).length == 1 ? '0' + String(dateSet_date4.getDate()) : String(dateSet_date4.getDate()))
            );
            SortingDate2.val(
                String(date_Y) + '-' + 
                (String(date_M).length == 1 ? '0' + String(date_M) : String(date_M)) + '-' + 
                (String(date_D).length == 1 ? '0' + String(date_D) : String(date_D))
            );
        }else if(name == 'lastMonth'){
            SortingDate1.val(
                String(dateSet_date4.getFullYear()) + '-' +
                (String(dateSet_date4.getMonth()).length == 1 ? '0' + String(dateSet_date4.getMonth()) : String(dateSet_date4.getMonth())) + '-' + 
                (String(dateSet_date4.getDate()).length == 1 ? '0' + String(dateSet_date4.getDate()) : String(dateSet_date4.getDate()))
            );
            SortingDate2.val(
                String(dateSet_date5.getFullYear()) + '-' +
                (String(dateSet_date5.getMonth()+1).length == 1 ? '0' + String(dateSet_date5.getMonth()+1) : String(dateSet_date5.getMonth()+1)) + '-' + 
                (String(dateSet_date5.getDate()).length == 1 ? '0' + String(dateSet_date5.getDate()) : String(dateSet_date5.getDate()))
            );
        }

        searchDate_SUBMIT();
    });

    // 조회 submit 버튼 클릭
    SortingDateSubmitButton.click(function(){
        searchDate_SUBMIT();
    });

    // 날짜 검색 함수 정의
    function searchDate_SUBMIT(){
        var A = SortingDate1.val().split('-'),
            B = SortingDate2.val().split('-');
        var A = A[0] + A[1] + A[2];
            B = B[0] + B[1] + B[2];
            
        if(A==B){
            List.find('tr').filter(function(){
                $(this).toggle(
                    ($(this).find('td:first-of-type').text().split('-')[0] + 
                    $(this).find('td:first-of-type').text().split('-')[1] + 
                    $(this).find('td:first-of-type').text().split('-')[2]).indexOf(A) > -1
                )
            })
        }else{
            List.find('tr').filter(function(){
                $(this).toggle(
                    ($(this).find('td:first-of-type').text().split('-')[0] + 
                    $(this).find('td:first-of-type').text().split('-')[1] + 
                    $(this).find('td:first-of-type').text().split('-')[2]) >= A 
                    &&
                    ($(this).find('td:first-of-type').text().split('-')[0] + 
                    $(this).find('td:first-of-type').text().split('-')[1] + 
                    $(this).find('td:first-of-type').text().split('-')[2]) <= B
                )
            })
        }
        $('article.list > div.searchTop .tab.active').click();
    }


    SortingDateResetButton.click(function(){
        location.reload();
    });



});