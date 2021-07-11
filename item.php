<!DOCTYPE html>
<?php require_once 'lib/_init.php';

$database = new Database();

$session = new Session();
date_default_timezone_set('Asia/Seoul');
validateAdmin($session, 2);
$USER_SQ = $session->user["USER_SQ"];
    $USER_GRADE = $session->user["GRADE"];
    ?>
<script>
    var $USER_SQ = <?php echo $USER_SQ ?>;
    var $USER_GRADE = <?php echo $USER_GRADE ?>;
    var $USER_GRADE_LIST = [];
</script>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>닥터케어유니온 - 상품</title>
    <script src="js/jquery-3.5.1.min.js"></script>
    <script src="js/vue.js"></script>
    <script src="js/Array.js"></script>
    <script src="js/script.js"></script>
    <script src="js/item.js"></script>
    <script src="js/jswLib.js"></script>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/item.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css">
</head>
<body>

    <div class="dark_div"></div>
    <div id="wrap">
        
<?php require_once 'lib/header.php'; ?>

        <!-- 이용권 리스트 -->
        <section class="content">
            <h2 class="hid">컨텐츠 영역</h2>
            <div class="top_sMenu">
                <p>
                    <label for="item_search_Box">이용권 검색</label>
                    <input type="text" name="item_search_Box" id="item_search_Box" placeholder="검색어를 입력해주세요.">
                </p>
                <p>
                    <label for="item_array">정렬</label>
                    <select name="item_array" id="item_array">
                        <option value="new">최근수정일순</option>
                        <option value="up">높은가격순</option>
                        <option value="down">낮은가격순</option>
                        <option value="name">이름순</option>
                        <option value="hot">인기순</option>
                    </select>
                </p>
                <div id="m_search_div" style="float: right;margin-right: 0;position: relative;">
                    <label for="selectMember">결제할 회원명 : </label>
                    <input type="text" @click="GET_M" 
                           :data-seq="selectMemberSeq" :class="selectMemberName != '' ? 'active' : ''" 
                           :value="selectMemberName != '' ? selectMemberName + ' 회원' : ''" name="selectMember" id="selectMember" 
                           placeholder="회원을 선택해주세요." readonly 
                           style="cursor:pointer;font-size: 14px;font-weight: 500;">
                    <div id="searchHelpBox">
                        <input type="text" 
                               name="m_searchBox" 
                               id="m_searchBox" 
                               placeholder="회원명 검색" 
                               autocomplete="off"
                               style="width: 150px;margin: 5px auto;display: block;"
                               v-model="searchText">
                        <ul>
                            <template v-for="member in memberList">
                                <li v-if="member.USER_NM.indexOf(searchText) > -1 || member.PHONE_NO.indexOf(searchText) > -1" :data-seq="member.USER_SQ" 
                                    @click="SELECT_M(member.USER_SQ,member.USER_NM)">
                                        {{member.USER_NM}} <small>{{member.PHONE_NO}}</small>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>

                <script>
                    const app = new Vue({
                        el : '#m_search_div',
                        data : {
                            memberList : [],
                            filtedList : [],
                            selectMemberSeq : '',
                            selectMemberName : '',
                            searchText : '',
                        },
                        methods : {
                            GET_M () {
                                this.searchText = '';
                                $.ajax({
                                    url: "flow_controller.php?task=getUserList",
                                    method: "POST",
                                    contentType: false,
                                    processData: false,
                                    success: (r) => {
                                        var data = r.split('|');
                                        this.memberList = JSON.parse(data[0]).filter(e => e.ISUSE == 1);
                                    }
                                });
                                
                                if(sessionStorage.buyMemberSeq != undefined){
                                    this.selectMemberSeq = sessionStorage.buyMemberSeq;
                                    this.selectMemberName = sessionStorage.buyMemberName; 
                                }
                            },
                            SELECT_M (sq,nm) {
                                this.selectMemberSeq = sq;
                                this.selectMemberName = nm;
                                sessionStorage.buyMemberSeq = sq;
                                sessionStorage.buyMemberName = nm;
                                $('#searchHelpBox').fadeOut(200);
                            },
                            SEARCH_M () {
                                this.filtedList
                            }
                        }
                    });
                </script>

            </div>
            <nav>
                <ul>
                    <li>
                        <a href="#" id="allCategory">
                            <span>전 체</span>
                        </a>
                    </li>
                </ul>
                <ul id="Category">
                    
                </ul>
                <div>
                    <p class="item_all_count">총 상품 수 : 0개</p>
                </div>
            </nav>
            <ul id="itemList">
                <!-- js로 Load -->
            </ul>
            
        </section>

    </div>

<?php require_once 'lib/footer.php'; ?>

</body>
</html>