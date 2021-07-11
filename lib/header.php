<script src="https://cdn.jsdelivr.net/npm/vue"></script>
<header id="headerApp">

    <a href="index.php">
        <div class="logobg">
            <h1>Dr. Care Union</h1>
        </div>
    </a>
    <nav>
        <ul>
            <li><a href="index.php">대쉬보드</a></li>
            <li><a href="members.php">멤버스</a></li>
            <li><a href="scheduler.php">스케줄러</a></li>
            <li><a href="item.php" onclick="buyMemSessionReset()">상품</a></li>
            <li><a href="chart.php">통계</a></li>
            <li><a href="accounting.php">회계</a></li>
            <li><a href="locker.php">라커</a></li>
            <li><a href="history.php">히스토리</a></li>
            <li><a href="setting.php">설정</a></li>
        </ul>
    </nav>

    <div class="topIcon">
        <form action="#" name="search_frm" method="GET">
            <label for="searchVal">회원명 검색</label>
            <input type="text" name="searchVal" id="searchVal">
            <button type="submit" name="searchBtn" id="searchBtn">검색</button>
        </form>
        <!-- 유저정보 -->
        <a href="">
            <div class="loginUser"></div>
        </a>
        <div class="loginUser_info">
            <p><?=$session->user["GRADENM"]?></p>
            <p><?=$session->user["USER_NM"]?></p>
            <p><a href="flow_controller.php?task=user_logout">로그아웃</a></p>
        </div>
    </div>
    <script>
        function buyMemSessionReset(){
            sessionStorage.buyMemberSeq = '';
            sessionStorage.buyMemberName = '';
        }
        
        
    </script>
</header>