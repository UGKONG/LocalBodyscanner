<!doctype html>
<html lang="ko">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE = edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
		<link rel="stylesheet" href="css/common.css">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">		
		<link rel="stylesheet" href="css/jquery-ui.css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css">
		<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
		<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>	
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
		<script src="https://kit.fontawesome.com/ac1d8bfbb6.js" crossorigin="anonymous"></script>
		<script src="js/jquery-ui.js"></script>
		<script src="js/jquery-ui-timespinner.js"></script>
		<script src="js/moment.min.js"></script>
		<!-- chartResources  -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.4/Chart.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
	
	</head>
<body>
	<div class="member_content">
		<header class="border_bottom_black">
			<nav class="index_navbar">
				<div class="index_nav_link_part">
					<ul class="">
						<li class="nav-item index_arlam_part">
							<span class="index_arlam_point_black"></span>
							<a class="" href="#user"><i class="fa fa-bell"></i>리안소프트</a>
						</li>
						<li class="nav-item">
							<a class="index_time" href="#time"><i class="fa fa-clock"></i>09:24:42</a>
						</li>
						<li class="nav-item">
							<a class="" href="login.php">로그아웃</a>
						</li>
						<span class="Text_line_black">&#124;</span>
						<li class="nav-item sign_text">
							<a class="" href="signup.php">회원등록</a>
						</li>
					</ul>
				</div>
			</nav>
		</header>
		<div class="nav-part">
			<nav class="navbar navbar-expand-md navbar-light navbar2">
				<div class="logo_part">
					<a class="navbar-brand" href="index.php"><!--<img src="#" alt="">-->BODY SCANNER</a>
					<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar" aria-controls="collapsibleNavbar" aria-expanded="false" aria-label="Toggle navigation">
						<span class="navbar-toggler-icon"></span>
					</button>
				</div>	
				<div class="collapse navbar-collapse nav-button nav_link_part2" id="collapsibleNavbar">
					<ul class="navbar-nav">
						<li class="nav_item">
							<a class="nav-link scroll_move blue underline active" href="#memberslist">멤버스</a>
						</li>
						<li class="nav_item">
							<a class="nav-link scroll_move blue underline" href="#javascript:void(0);">스케줄러</a>
						</li>
						<li class="nav_item">
							<a class="nav-link scroll_move blue underline" href="#javascript:void(0);">상품</a>
						</li>
						<li class="nav_item">
							<a class="nav-link scroll_move blue underline" href="#javascript:void(0);">통계</a>
						</li>
						<li class="nav_item">
							<a class="nav-link scroll_move blue underline" href="#javascript:void(0);">라커</a>
						</li>
						<li class="nav_item">
							<a class="nav-link scroll_move blue underline" href="#javascript:void(0);">히스토리</a>
						</li>
						<li class="nav_item">
							<a class="nav-link scroll_move blue underline disabled" href="#javascript:void(0);">체크인</a>
						</li>
						<li class="nav_item">
							<a class="nav-link scroll_move blue underline" href="#javascript:void(0);">설정</a>
						</li>
					</ul>
				</div>
			</nav>
		</div>
		<div class="mainlist">
			<div class="listtitle">
				<h1>MEMBER</h1>
			</div>
			<div class="user_Data">
				<div class="user_Data_form">
					<div class="user_photo">
						<img src="images/manicon.png" alt="">
					</div>
					<div class="user_text_Data">
						<div class="user_Data_name"><span>임재훈</span></div>
						<div class="user_Data_gender"><i class="fa fa-mars"></i><span>남성</span></div>
						<div class="user_Data_phone"><i class="fa fa-phone-alt"></i><span>010-4505-2961</span></div>
						<div class="user_Data_birth"><span>79.06.17 (41세)</span></div>
						<div class="user_Data_adress"><i class="fa fa-map-marker-alt"></i><span>인천시 남동구 서창동</span></div>
						<div class="user_Data_email"><i class="fa fa-envelope"></i><span>tsunami@liansoft.co.kr</span></div>
					</div>
				</div>
				<div class="container_fluid user_tab_data">
					<!-- Nav pills -->
					<ul class="nav nav-pills" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" data-toggle="pill" href="#home">회원정보</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" data-toggle="pill" href="#menu1">건강관리 데이터</a>
						</li>
					</ul>

					<!-- Tab panes -->
					<div class="tab-content">
						<div id="home" class="container_fluid user_card_Data tab-pane active">
							<div class="user_card_part">
								<div class="card card_section">
									<div class="card-body">
										<h4 class="card-title">개인정보</h4>
										<div class="card_personal_Data">
											<div class="card_data_list">
												<div class="card_data_history">
													<p class="card-text user_history_name">회원번호</p>
													<p class="card-text user_history_day">2961</p>
												</div>
												<div class="card_data_history">
													<p class="card-text user_history_name">등록일</p>
													<p class="card-text user_history_day">2020.04.09</p>
												</div>
												<div class="card_data_history">
													<p class="card-text user_history_name">담당자</p>
													<p class="card-text user_history_day">김형준</p>
												</div>
												<div class="card_data_history">
													<p class="card-text user_history_name">회원용 APP</p>
													<p class="card-text user_history_day">이용중 아님</p>
												</div>
												<div class="card_data_history">
													<p class="card-text user_history_name">라커번호</p>
													<p class="card-text user_history_day">09</p>
												</div>
												<div class="card_data_history">
													<p class="card-text user_history_name">바코드 번호</p>
													<p class="card-text user_history_day">등록된 바코드가 없습니다.</p>
												</div>
											</div>
										</div>
										<div class="card_textarea_Data">
											<h5 class="card_bottom">메모</h5>
											<a class="textarea_change_button click" id="inputbutton" type="button" href="#"><img src="../코딩/images/edit.png" alt=""><span>메모수정</span></a>
											<textarea name="inputbutton" class="form-control" rows="1" cols="" id="comment" placeholder="">거북목</textarea>
										</div>
									</div>
								</div>
								<div class="card card_section">
									<div class="card-body">
										<h4 class="card-title">구매이용권</h4>
										<div class="card_overflow overflow-auto">
											<div class="card_data_list_store">
												<div class="card card_item">
													<div class="card-body">
														<div class="card_title_part">
															<p class="card-title item_card_title"><i class="fa fa-grip-lines-vertical"></i><span>이용권</span></p>
															<p class="teaching">사용중</p>
														</div>	
														<div class="card_data_list item_card_data_list">
															<p class="card-text item_card_data1">P.T 이용권 (1개월 10회)</p>
															<p class="card-text item_card_data2">담당강사 <span>김형준</span></p>			
															<p class="card-text item_card_data3">2020.04.09 &#126; 2020.05.09 <span>(23일 남음)</span></p>
															<p class="card-text item_card_data4">예약가능<span>8</span> &#183; 취소가능<span>10</span> &#183; 잔여횟수<span>8</span></p>
														</div>
													</div>
												</div>	
												<div class="card card_item person_item_card">
													<div class="card-body">
														<div class="card_title_part">
															<p class="card-title item_card_title"><i class="fa fa-grip-lines-vertical"></i><span>이용권</span></p>
															<p class="teaching">사용중</p>
														</div>	
														<div class="card_data_list item_card_data_list">
															<p class="card-text item_card_data1">P.T 이용권 (1개월 10회)</p>
															<p class="card-text item_card_data2">담당강사 <span>김형준</span></p>			
															<p class="card-text item_card_data3">2020.04.09 &#126; 2020.05.09 <span>(23일 남음)</span></p>
															<p class="card-text item_card_data4">예약가능<span>8</span> &#183; 취소가능<span>10</span> &#183; 잔여횟수<span>8</span></p>
														</div>
													</div>
												</div>
												
												<div class="card card_item person_item_card">
													<div class="card-body">
														<div class="card_title_part">
															<p class="card-title item_card_title"><i class="fa fa-grip-lines-vertical"></i><span>이용권</span></p>
															<p class="teaching">사용중</p>
														</div>	
														<div class="card_data_list item_card_data_list">
															<p class="card-text item_card_data1">P.T 이용권 (1개월 10회)</p>
															<p class="card-text item_card_data2">담당강사 <span>김형준</span></p>			
															<p class="card-text item_card_data3">2020.04.09 &#126; 2020.05.09 <span>(23일 남음)</span></p>
															<p class="card-text item_card_data4">예약가능<span>8</span> &#183; 취소가능<span>10</span> &#183; 잔여횟수<span>8</span></p>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="card card_section">
									<div class="card-body">
										<h4 class="card-title">히스토리</h4>
										<div class="card_overflow overflow-auto">
											<div class="card_data_list">
												<div class="card_data_history">
													<p class="card-text user_history_name">입장</p>
													<p class="card-text user_history_day">2020.04.09 <span class="user_history_time">09:12:09</span></p>
												</div>
												<div class="card_data_history">
													<p class="card-text user_history_name">입장</p>
													<p class="card-text user_history_day">2020.04.09 <span class="user_history_time">09:12:09</span></p>
												</div>
												<div class="card_data_history">
													<p class="card-text user_history_name">입장</p>
													<p class="card-text user_history_day">2020.04.09 <span class="user_history_time">09:12:09</span></p>
												</div>
												<div class="card_data_history">
													<p class="card-text user_history_name">입장</p>
													<p class="card-text user_history_day">2020.04.09 <span class="user_history_time">09:12:09</span></p>
												</div>
												<div class="card_data_history">
													<p class="card-text user_history_name">입장</p>
													<p class="card-text user_history_day">2020.04.09 <span class="user_history_time">09:12:09</span></p>
												</div>
												<div class="card_data_history">
													<p class="card-text user_history_name">입장</p>
													<p class="card-text user_history_day">2020.04.09 <span class="user_history_time">09:12:09</span></p>
												</div>
												<div class="card_data_history">
													<p class="card-text user_history_name">입장</p>
													<p class="card-text user_history_day">2020.04.09 <span class="user_history_time">09:12:09</span></p>
												</div>
												<div class="card_data_history">
													<p class="card-text user_history_name">입장</p>
													<p class="card-text user_history_day">2020.04.09 <span class="user_history_time">09:12:09</span></p>
												</div>
												<div class="card_data_history">
													<p class="card-text user_history_name">입장</p>
													<p class="card-text user_history_day">2020.04.09 <span class="user_history_time">09:12:09</span></p>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div id="menu1" class="container_fluid tab-pane user_card_Data tab-pane fade">
							<div class="user_card_part">
								<div class="card health_data_card">
									<div class="card-body">
										<h4 class="card-title">신체정보</h4>
										<div class="card_overflow overflow-auto">
											<div class="health_card_list">
												<div class="card health_card_item">
													<div class="card-body">
														<div class="health_card_title_part">
															<p class="card-title item_card_title"><i class="fa fa-grip-lines-vertical"></i><span>측정일</span></p>
															<p class="choice_Month">
																<form>
																	<select class="health_data_month">
																		<option selected>2020.05.15</option>
																		<option value="this month">2020.04.15</option>
																		<option value="last month">2020.03.15</option>
																	</select>
																</form>
															</p>
														</div>	
														<div class="card_data_list healthitem_card_data_list" type="button" data-toggle="modal" data-target="#myModal_healthdata">
															<div class="card_data_health">
																<p class="card-text user_health_name">키</p>
																<p class="card-text user_health_data">162 cm</p>
															</div>
															<div class="card_data_health">
																<p class="card-text user_health_name">체지방량</p>
																<p class="card-text user_health_data">14 kg</p>
															</div>
															<div class="card_data_health">
																<p class="card-text user_health_name">몸무게</p>
																<p class="card-text user_health_data">49 kg</p>
															</div>
															<div class="card_data_health">
																<p class="card-text user_health_name">체지방률</p>
																<p class="card-text user_health_data">18 %</p>
															</div>
															<div class="card_data_health">
																<p class="card-text user_health_name">근육량</p>
																<p class="card-text user_health_data">23 kg</p>
															</div>
															<div class="card_data_health">
																<p class="card-text user_health_name">기초대사량</p>
																<p class="card-text user_health_data">1900 kcal</p>
															</div>
															<div class="card_data_health">
																<p class="card-text user_health_name">BMI</p>
																<p class="card-text user_health_data">20 kg/m&#178;</p>
															</div>
															<div class="card_data_health">
																<p class="card-text user_health_name">등급</p>
																<p class="card-text user_health_data">1등급</p>
															</div>
														</div>
													</div>
												</div>	
												<div class="graph_button">
													<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal_health_graph">변화도 그래프 보기</button>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="card health_data_card">
									<div class="card-body">
										<h4 class="card-title">체형정보</h4>
										<div class="card_overflow overflow-auto">
											<div class="health_card_list">
												<div class="card health_card_item bodydata">
													<div class="card-body">
														<div class="health_card_title_part">
															<p class="card-title item_card_title"><i class="fa fa-grip-lines-vertical"></i><span>측정일</span></p>
															<p class="choice_Month">
																<form>
																	<select class="health_data_month">
																		<option selected>2020.05.15</option>
																		<option value="this month">2020.04.15</option>
																		<option value="last month">2020.03.15</option>
																	</select>
																</form>
															</p>
														</div>	
														<div class="health_card_title">
															<p class="card-text user_health_title">정면</p>
															<p class="card-text user_health_title">측면</p>
														</div>
														<div class="card_data_list healthitem_card_data_list" type="button" data-toggle="modal" data-target="#myModal_bodydata">
															
															<div class="health_card_data">
																<p class="card-text user_health_name">머리</p>
																<p class="card-text user_health_data">-0.1&#176;</p>
															</div>
															<div class="health_card_data">
																<p class="card-text user_health_name">머리</p>
																<p class="card-text user_health_data">-0.1&#176;</p>
															</div>
															<div class="health_card_data">
																<p class="card-text user_health_name">어깨</p>
																<p class="card-text user_health_data">-0.2&#176; / 1.2&#176;</p>
															</div>
															<div class="health_card_data">
																<p class="card-text user_health_name">어깨</p>
																<p class="card-text user_health_data">1&#176;</p>
															</div>
															<div class="health_card_data">
																<p class="card-text user_health_name">골반</p>
																<p class="card-text user_health_data">2.2&#176; / 0&#176;</p>
															</div>
															<div class="health_card_data">
																<p class="card-text user_health_name">골반</p>
																<p class="card-text user_health_data">1.1&#176;</p>
															</div>
															<div class="health_card_data">
																<p class="card-text user_health_name">다리</p>
																<p class="card-text user_health_data">0.1&#176; / 0&#176;</p>
															</div>
															<div class="health_card_data">
																<p class="card-text user_health_name">다리</p>
																<p class="card-text user_health_data">0&#176;</p>
															</div>
														</div>
													</div>
												</div>	
												<div class="graph_button1">
													<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal_body_graph">변화도 그래프 보기</button>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="card health_data_card">
									<div class="card-body">
										<h4 class="card-title">ROM정보</h4>
										<div class="card_overflow overflow-auto">
											<div class="health_card_list">
												<div class="card health_card_item">
													<div class="card-body">
														<div class="health_card_title_part">
															<p class="card-title item_card_title"><i class="fa fa-grip-lines-vertical"></i><span>측정일</span></p>
															<p class="choice_Month">
																<form>
																	<select class="health_data_month">
																		<option selected>2020.05.15</option>
																		<option value="this month">2020.04.15</option>
																		<option value="last month">2020.03.15</option>
																	</select>
																</form>
															</p>
														</div>
														<div class="health_card_title">
															<p class="card-text user_health_title">정면</p>
															<p class="card-text user_health_title">측면</p>
														</div>
														<div class="card_data_list healthitem_card_data_list" type="button" data-toggle="modal" data-target="#myModal_romdata">
															<div class="health_card_data">
																<p class="card-text user_health_name">머리</p>
																<p class="card-text user_health_data">-0.1&#176;</p>
															</div>
															<div class="health_card_data">
																<p class="card-text user_health_name">머리</p>
																<p class="card-text user_health_data">-0.1&#176;</p>
															</div>
															<div class="health_card_data">
																<p class="card-text user_health_name">어깨</p>
																<p class="card-text user_health_data">-0.2&#176; / 1.2&#176;</p>
															</div>
															<div class="health_card_data">
																<p class="card-text user_health_name">어깨</p>
																<p class="card-text user_health_data">1&#176;</p>
															</div>
															<div class="health_card_data">
																<p class="card-text user_health_name">골반</p>
																<p class="card-text user_health_data">2.2&#176; / 0&#176;</p>
															</div>
															<div class="health_card_data">
																<p class="card-text user_health_name">골반</p>
																<p class="card-text user_health_data">1.1&#176;</p>
															</div>
															<div class="health_card_data">
																<p class="card-text user_health_name">다리</p>
																<p class="card-text user_health_data">0.1&#176; / 0&#176;</p>
															</div>
															<div class="health_card_data">
																<p class="card-text user_health_name">다리</p>
																<p class="card-text user_health_data">0&#176;</p>
															</div>
														</div>
													</div>
												</div>	
												<div class="graph_button">
													<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal_rom_graph">변화도 그래프 보기</button>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="card health_data_card disabled" disabled="disabled">
									<div class="card-body">
										<h4 class="card-title">FMS정보</h4>
										<div class="card_overflow overflow-auto">
											<div class="card_data_list">
					
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>	
	</div>
	
	<!-- 모달part -->
	<!-- 신체정보 모달 -->
	<div class="modal fade modal_center1" id="myModal_healthdata">
		<div class="modal-dialog modal-lg modal_center1 modal_graph1" style="width: 600px;">
			<div class="modal-content modal_graph1">
				<!-- Modal Header -->
				<div class="modal-header">
					<h4 class="modal-title">신체정보 상세보기</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<!-- Modal body -->
				<div class="modal-body">
					<div class="inbody_img">
						<img src="images/770_result01.jpg" alt="인바디 검사용지">
					</div>
				</div>        
				<!-- Modal footer -->
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>        
			</div>
		</div>
	</div>
	<!-- 신체정보 변화도 그래프 모달 -->
	<div class="modal fade modal_center" id="myModal_health_graph">
		<div class="modal-dialog modal-xl modal_center modal_graph">
			<div class="modal-content modal_graph">
				<!-- Modal Header -->
				<div class="modal-header">
					<h4 class="modal-title">신체정보 변화도 보기</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<!-- Modal body -->
				<div class="modal-body">
					<div class="garphdata">
						<div class="graphdataheader">
							<div class="datepicker">
								<button class="btn btn-group-sm btn-light">3개월</button>
								<button class="btn btn-group-sm btn-light">6개월</button>
								<button class="btn btn-group-sm btn-light">1년</button>
								<button class="btn btn-group-sm btn-light active">전체</button>
							</div>
							<div class="graphbutton">
								<button class="btn  btn-group-sm btn-light active">몸무게</button>
								<button class="btn btn-group-sm btn-light">근육량</button>
								<button class="btn btn-group-sm btn-light">체지방량</button>
								<button class="btn btn-group-sm btn-light">체지방률</button>
								<button class="btn btn-group-sm btn-light">BMI</button>
								<button class="btn btn-group-sm btn-light">기초대사량</button>
							</div>
						</div>
					</div>	
					<div class="graphpart" style="">
						<canvas class="graph" id="canvas3"></canvas>
					</div>
				</div>        
				<!-- Modal footer -->
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>        
			</div>
		</div>
	</div>
	<!-- 체형정보 모달 -->
	<div class="modal fade modal_center1" id="myModal_bodydata">
		<div class="modal-dialog modal-xl modal_center1 modal_graph1">
			<div class="modal-content modal_graph1">
				<!-- Modal Header -->
				<div class="modal-header">
					<h4 class="modal-title">체형정보 상세보기</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<!-- Modal body -->
				<div class="modal-body">
					<ul class="nav nav-pills bodydata" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" data-toggle="pill" href="#bodydata_tab1">체형검사 상세결과 보기</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" data-toggle="pill" href="#bodydata_tab2">체형검사 비교결과 보기</a>
						</li>
					</ul>

					<!-- Tab panes -->
					<div class="tab-content">
						<div id="bodydata_tab1" class="container_fluid tab-pane active">
							<div>
								<div class="allmeasurement_title">
									<div class="rectangle">
									</div>
									<label>전신 측정 결과</label>
								</div>
								<div class="day_choice">
									<label>날짜선택</label>
									<select name="direction" class="custom-select">
										<option value="today" selected>2020.05.13</option>
										<option value="1month">2020.04.13</option>
										<option value="2month">2020.03.13</option>
										<option value="3month">2020.02.13</option>
									</select>
								</div>
								<div class="allmeasurement">
									<div class="all_data_view">
										<div class="frontdata">
											<label>정면</label>
											<img src="images/234.jpg" alt="">
										</div>
										<div class="sidedata">
											<label>측면</label>
											<img src="images/123.jpg" alt="">
										</div>
										<div class="chartdata">
											<label><i class="fa fa-table"></i>측정 결과표</label>
											<table class="table table-bordered table-hover">
												<thead>
													<tr>
														<th rowspan="2">방향</th>
														<th rowspan="2">부위</th>
														<th colspan="2">결과</th>
														<th rowspan="2">방향</th>
														<th rowspan="2">부위</th>
														<th colspan="2">결과</th>
													</tr>
													<tr class="leftright">
														<th>Left</th>
														<th>Right</th>
														<th>Left</th>
														<th>Right</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<th rowspan="4">정면</th>
														<td class="second_title">머리</td>
														<td class="arrow_left_green"><i class="fa fa-arrow-left"></i>0.1</td>
														<td class="arrow_left_sam"><i class="fa fa-minus"></i>0.0</td>
														<th rowspan="4">측면</th>
														<td class="second_title">머리</td>
														<td class="arrow_left_red"><i class="fa fa-arrow-left"></i>1.2</td>
														<td class="arrow_left_sam"><i class="fa fa-minus"></i>0.0</td>
													</tr>
													<tr>
														<td class="second_title">어깨</td>
														<td class="arrow_left_green"><i class="fa fa-arrow-down"></i>0.2</td>
														<td class="arrow_left_yellow"><i class="fa fa-arrow-up"></i>1.2</td>
														<td class="second_title">어깨</td>
														<td class="arrow_left_red"><i class="fa fa-arrow-left"></i>2.3</td>
														<td class="arrow_left_sam"><i class="fa fa-minus"></i>0.0</td>
													</tr>
													<tr>
														<td class="second_title">골반</td>
														<td class="arrow_left_red"><i class="fa fa-arrow-up"></i>2.2</td>
														<td class="arrow_left_green"><i class="fa fa-arrow-down"></i>0.4</td>
														<td class="second_title">골반</td>
														<td class="arrow_left_sam"><i class="fa fa-minus"></i>0.0</td>
														<td class="arrow_left_red"><i class="fa fa-arrow-right"></i>3.5</td>
													</tr>
													<tr>
														<td class="second_title">다리</td>
														<td class="arrow_left_green"><i class="fa fa-arrow-left"></i>10.3</td>
														<td class="arrow_left_yellow"><i class="fa fa-arrow-right"></i>15.2</td>
														<td class="second_title">무릎</td>
														<td class="arrow_left_red"><i class="fa fa-arrow-left"></i>2.3</td>
														<td class="arrow_left_sam"><i class="fa fa-minus"></i>0.0</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
							<div>
								<div class="allmeasurement_title">
									<div class="rectangle">
									</div>
									<label>부위별 측정 결과</label>
								</div>
								<div class="part_data_button">
									<button class="btn btn-light active">머리</button>
									<button class="btn btn-light">어깨</button>
									<button class="btn btn-light">골반</button>
									<button class="btn btn-light">다리</button>
								</div>
								<div class="part_data_img">
									<img class="part_data_img_front" src="images/front.jpg" alt="정면이미지">
									<img class="part_data_img_side" src="images/side.jpg" alt="측면이미지">
									<img class="part_data_img_back" src="images/back.jpg" alt="후면이미지">
								</div>
							</div>
						</div>
						<div id="bodydata_tab2" class="container_fluid tab-pane fade">
							<div class="tab2_data_part">
								<div class="allmeasurement_title">
									<div class="rectangle">
									</div>
									<label>비교 결과</label>
								</div>
								<div class="part_data_button search_button_modal">
									<button class="btn btn-info btn_class">검색</button>
								</div>
								<div class="day_choice2">
									<label>이전 측정일</label>
									<select name="direction" class="custom-select">
										<option value="today" selected>2020.04.15</option>
										<option value="1month">2020.04.15</option>
										<option value="2month">2020.05.16</option>
										<option value="3month">2020.06.14</option>
									</select>
									<label>최근 측정일</label>
									<select name="direction" class="custom-select">
										<option value="today" selected>2020.06.14</option>
										<option value="1month">2020.04.15</option>
										<option value="2month">2020.05.16</option>
										<option value="3month">2020.06.14</option>
									</select>
								</div>
								<div class="part_data_button">
									<button class="btn btn-light active">정면</button>
									<button class="btn btn-light">측면</button>
								</div>
								<div class="allmeasurement">
									<div class="all_data_view">
										<div class="frontdata">
											<label>이전 측정일</label>
											<img src="images/234.jpg" alt="">
										</div>
										<div class="sidedata">
											<label>최근 측정일</label>
											<img src="images/234.jpg" alt="">
										</div>
										<div class="chartdata">
											<label><i class="fa fa-table"></i>측정 결과표</label>
											<table class="table table-bordered table-hover">
												<thead>
													<tr>
														<th rowspan="2">방향</th>
														<th rowspan="2">부위</th>
														<th colspan="2">2020.04.15</th>
														<th colspan="2">2020.06.14</th>
													</tr>
													<tr class="leftright">
														<th>Left</th>
														<th>Right</th>
														<th>Left</th>
														<th>Right</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<th rowspan="4">정면</th>
														<td class="second_title">머리</td>
														<td class="box_color">1.2</td>
														<td class="box_color">1.0</td>
														<td class="arrow_left_green"><i class="fa fa-arrow-right"></i>1.0</td>
														<td class="arrow_left_green"><i class="fa fa-arrow-left"></i>0.8</td>
													</tr>
													<tr>
														<td class="second_title">어깨</td>
														<td class="box_color">2.2</td>
														<td class="box_color">2.0</td>
														<td class="arrow_left_green"><i class="fa fa-arrow-down"></i>2.0</td>
														<td class="arrow_left_sam"><i class="fa fa-minus"></i>2.0</td>
													</tr>
													<tr>
														<td class="second_title">골반</td>
														<td class="box_color">1.0</td>
														<td class="box_color">1.0</td>
														<td class="arrow_left_green"><i class="fa fa-arrow-up"></i>1.2</td>
														<td class="arrow_left_green"><i class="fa fa-arrow-down"></i>1.1</td>
													</tr>
													<tr>
														<td class="second_title">다리</td>
														<td class="box_color">0.1</td>
														<td class="box_color">0.0</td>
														<td class="arrow_left_red"><i class="fa fa-arrow-right"></i>3.0</td>
														<td class="arrow_left_yellow"><i class="fa fa-arrow-left"></i>2.0</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>						
						</div>
					</div>	
				</div>        
				<!-- Modal footer -->
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>        
			</div>
		</div>
	</div>
	<!-- 체형정보 변화도 그래프 모달 -->
	<div class="modal fade modal_center" id="myModal_body_graph">
		<div class="modal-dialog modal-xl modal_center modal_graph">
			<div class="modal-content modal_graph">
				<!-- Modal Header -->
				<div class="modal-header">
					<h4 class="modal-title">체형정보 변화도 보기</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<!-- Modal body -->
				<div class="modal-body">
					<div class="garphdata">
						<div class="graphdataheader">
							<div class="datepicker">
								<button class="btn btn-group-sm btn-light">3개월</button>
								<button class="btn btn-group-sm btn-light">6개월</button>
								<button class="btn btn-group-sm btn-light">1년</button>
								<button class="btn btn-group-sm btn-light active">전체</button>
							</div>
							<div class="graphbutton">
								<select name="direction" class="custom-select">
									<option value="front" selected>정면</option>
									<option value="side">측면</option>
								</select>
								<button class="btn  btn-group-sm btn-light active">머리</button>
								<button class="btn btn-group-sm btn-light">어깨</button>
								<button class="btn btn-group-sm btn-light">골반</button>
								<button class="btn btn-group-sm btn-light">다리</button>
							</div>
						</div>
					</div>	
					<div class="graphpart" style="">
						<canvas class="graph" id="canvas2"></canvas>
					</div>
				</div>        
				<!-- Modal footer -->
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>        
			</div>
		</div>
	</div>
	<!-- rom정보 모달 -->
	<div class="modal fade modal_center1" id="myModal_romdata">
		<div class="modal-dialog modal-xl modal_center1 modal_graph1">
			<div class="modal-content modal_graph1">
				<!-- Modal Header -->
				<div class="modal-header">
					<h4 class="modal-title">ROM정보 상세보기</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<!-- Modal body -->
				<div class="modal-body">
					<ul class="nav nav-pills bodydata" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" data-toggle="pill" href="#bodydata_tab3">ROM검사 상세결과 보기</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" data-toggle="pill" href="#bodydata_tab4">ROM검사 비교결과 보기</a>
						</li>
					</ul>
					<!-- Tab panes -->
					<div class="tab-content">
						<div id="bodydata_tab3" class="container_fluid tab-pane active">
							<div class="allmeasurement_title">
								<div class="rectangle">
								</div>
								<label>ROM측정 결과</label>
							</div>
							<div class="day_choice">
								<label>날짜선택</label>
								<select name="direction" class="custom-select">
									<option value="today" selected>2020.05.13</option>
									<option value="1month">2020.04.13</option>
									<option value="2month">2020.03.13</option>
									<option value="3month">2020.02.13</option>
								</select>
							</div>
							<div class="part_data_button">
								<button class="btn btn-light active">정면</button>
								<button class="btn btn-light">측면</button>
							</div>
							<div class="allmeasurement">
								<div class="all_data_view">
									<div class="image_part_title">
										<div class="leftdata_img">
											<label><i class="fa fa-file-image"></i>측정 부위</label>
											<img src="images/front.jpg" alt=""><span class="romdata_font">목</span>
											<img src="images/front.jpg" alt=""><span class="romdata_font">R-어깨</span>
											<img src="images/front.jpg" alt=""><span class="romdata_font">R-다리</span>
										</div>
										<div class="rightdata_img">
											<img src="images/side.jpg" alt=""><span class="romdata_font">허리</span>
											<img src="images/side.jpg" alt=""><span class="romdata_font">L-어깨</span>
											<img src="images/side.jpg" alt=""><span class="romdata_font">L-다리</span>
										</div>
									</div>	
									<div class="chartdata rom_Chartdata">
										<label><i class="fa fa-table"></i>측정 결과표</label>
										<table class="table table-bordered table-hover">
											<thead>
												<tr>
													<th rowspan="2">부위</th>
													<th colspan="2" class="twodata">결과</th>
													<th rowspan="2">밸런스</th>
												</tr>
												<tr class="leftright">
													<th>Flexion</th>
													<th>Extension</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td>목</td>
													<td class="ROM_data_flexion">40<span class="ROM_icon_good">GOOD</span></td>
													<td class="ROM_data_extension">36<span class="ROM_icon_nomal">NOMAL</span></td>
													<td class="good_balance_nomal">NOMAL</td>
												</tr>
												<tr>
													<td>허리</td>
													<td class="ROM_data_flexion">90<span class="ROM_icon_good">GOOD</span></td>
													<td class="ROM_data_extension">10<span class="ROM_icon_bad">BAD</span></td>
													<td class="good_balance_bad">BAD</td>
												</tr>
												<tr>
													<td>R-어깨</td>
													<td colspan="2" class="ROM_data_flexion">90<span class="ROM_icon_good">GOOD</span></td>
													<td class="good_balance_good">GOOD</td>
												</tr>
												<tr>
													<td>L-어깨</td>
													<td colspan="2" class="ROM_data_flexion">90<span class="ROM_icon_good">GOOD</span></td>
													<td class="good_balance_good">GOOD</td>
												</tr>
												<tr>
													<td>R-다리</td>
													<td colspan="2" class="ROM_data_flexion">90<span class="ROM_icon_good">GOOD</span></td>
													<td class="good_balance_good">GOOD</td>
												</tr>
												<tr>
													<td>L-다리</td>
													<td colspan="2" class="ROM_data_flexion">90<span class="ROM_icon_good">GOOD</span></td>
													<td class="good_balance_good">GOOD</td>
												</tr>
											</tbody>
										</table>
										<!-- 측면 데이
										<table class="table table-bordered table-hover">
											<thead>
												<tr>
													<th rowspan="2">부위</th>
													<th colspan="2" class="twodata">결과</th>
													<th rowspan="2">밸런스</th>
												</tr>
												<tr class="leftright">
													<th>Flexion</th>
													<th>Extension</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td>목</td>
													<td class="ROM_data_flexion">40<span class="ROM_icon_good">GOOD</span></td>
													<td class="ROM_data_extension">36<span class="ROM_icon_nomal">NOMAL</span></td>
													<td class="good_balance_nomal">NOMAL</td>
												</tr>
												<tr>
													<td>허리</td>
													<td class="ROM_data_flexion">90<span class="ROM_icon_good">GOOD</span></td>
													<td class="ROM_data_extension">10<span class="ROM_icon_bad">BAD</span></td>
													<td class="good_balance_bad">BAD</td>
												</tr>
												<tr>
													<td>R-어깨</td>
													<td colspan="2" class="ROM_data_flexion">90<span class="ROM_icon_good">GOOD</span></td>
													<td class="ROM_data_extension">10<span class="ROM_icon_bad">BAD</span></td>
													<td class="good_balance_good">GOOD</td>
												</tr>
												<tr>
													<td>L-어깨</td>
													<td colspan="2" class="ROM_data_flexion">90<span class="ROM_icon_good">GOOD</span></td>
													<td class="ROM_data_extension">10<span class="ROM_icon_bad">BAD</span></td>
													<td class="good_balance_good">GOOD</td>
												</tr>
												<tr>
													<td>R-다리</td>
													<td colspan="2" class="ROM_data_flexion">90<span class="ROM_icon_good">GOOD</span></td>
													<td class="ROM_data_extension">10<span class="ROM_icon_bad">BAD</span></td>
													<td class="good_balance_good">GOOD</td>
												</tr>
												<tr>
													<td>L-다리</td>
													<td colspan="2" class="ROM_data_flexion">90<span class="ROM_icon_good">GOOD</span></td>
													<td class="ROM_data_extension">10<span class="ROM_icon_bad">BAD</span></td>
													<td class="good_balance_good">GOOD</td>
												</tr>
											</tbody>
										</table>  -->
									</div>
								</div>
							</div>
						</div>
						<div id="bodydata_tab4" class="container_fluid tab-pane fade">
							<div class="allmeasurement_title">
								<div class="rectangle">
								</div>
								<label>ROM비교 결과</label>
							</div>
							<div class="part_data_button search_button_modal">
								<button class="btn btn-info btn_class">검색</button>
							</div>
							<div class="day_choice2">
								<label>이전 측정일</label>
								<select name="direction" class="custom-select">
									<option value="today" selected>2020.04.15</option>
									<option value="1month">2020.04.15</option>
									<option value="2month">2020.05.16</option>
									<option value="3month">2020.06.14</option>
								</select>
								<label>최근 측정일</label>
								<select name="direction" class="custom-select">
									<option value="today" selected>2020.06.14</option>
									<option value="1month">2020.04.15</option>
									<option value="2month">2020.05.16</option>
									<option value="3month">2020.06.14</option>
								</select>
							</div>
							<div class="part_data_button">
								<button class="btn btn-light active">정면</button>
								<button class="btn btn-light">측면</button>
							</div>
							<div class="allmeasurement">
								<div class="ROM_data_view">
									<div class="graph_title">
										<label><i class="fa fa-chart-pie"></i>비교 그래프</label>
										<canvas id="myChart"></canvas>
									</div>
									<div class="chartdata rom_Chartdata_table">
										<div class="left_table">
											<label><i class="fa fa-table"></i>이전 측정표</label>
											<table class="table table-bordered table-hover" style="border: 1px solid red;">
												<thead>
													<tr>
														<th rowspan="2">방향</th>
														<th rowspan="2">부위</th>
														<th colspan="2" class="twodata">결과</th>
														<th rowspan="2">밸런스</th>
													</tr>
													<tr class="leftright">
														<th>Flexion</th>
														<th>Extension</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<th rowspan="6">정면</th>
														<td>목</td>
														<td class="ROM_data_flexion">40</td>
														<td class="ROM_data_extension">36</td>
														<td class="">NOMAL</td>
													</tr>
													<tr>
														<td>허리</td>
														<td class="ROM_data_flexion">90</td>
														<td class="ROM_data_extension">10</td>
														<td class="">BAD</td>
													</tr>
													<tr>
														<td>R-어깨</td>
														<td colspan="2" class="ROM_data_flexion">90</td>
														<td class="">GOOD</td>
													</tr>
													<tr>
														<td>L-어깨</td>
														<td colspan="2" class="ROM_data_flexion">90</td>
														<td class="">GOOD</td>
													</tr>
													<tr>
														<td>R-다리</td>
														<td colspan="2" class="ROM_data_flexion">90</td>
														<td class="">GOOD</td>
													</tr>
													<tr>
														<td>L-다리</td>
														<td colspan="2" class="ROM_data_flexion">90</td>
														<td class="">GOOD</td>
													</tr>
												</tbody>
											</table>
										</div>
										<div class="right_table">
											<label><i class="fa fa-table"></i>최근 측정표</label>
											<table class="table table-bordered table-hover">
												<thead>
													<tr>
														<th rowspan="2">방향</th>
														<th rowspan="2">부위</th>
														<th colspan="2" class="twodata">결과</th>
														<th rowspan="2">밸런스</th>
													</tr>
													<tr class="leftright">
														<th>Flexion</th>
														<th>Extension</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<th rowspan="6">정면</th>
														<td>목</td>
														<td class="ROM_data_flexion">40</td>
														<td class="ROM_data_extension">36</td>
														<td class="">NOMAL</td>
													</tr>
													<tr>
														<td>허리</td>
														<td class="ROM_data_flexion">90</td>
														<td class="ROM_data_extension">10</td>
														<td class="">BAD</td>
													</tr>
													<tr>
														<td>R-어깨</td>
														<td colspan="2" class="ROM_data_flexion">90</td>
														<td class="">GOOD</td>
													</tr>
													<tr>
														<td>L-어깨</td>
														<td colspan="2" class="ROM_data_flexion">90</td>
														<td class="">GOOD</td>
													</tr>
													<tr>
														<td>R-다리</td>
														<td colspan="2" class="ROM_data_flexion">90</td>
														<td class="">GOOD</td>
													</tr>
													<tr>
														<td>L-다리</td>
														<td colspan="2" class="ROM_data_flexion">90</td>
														<td class="">GOOD</td>
													</tr>
												</tbody>
											</table>
										</div>	
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- Modal footer -->
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>        
			</div>
		</div>
	</div>
	<!-- rom정보 변화도 그래프 모달 -->
	<div class="modal fade modal_center" id="myModal_rom_graph">
		<div class="modal-dialog modal-xl modal_center modal_graph">
			<div class="modal-content modal_graph">
				<!-- Modal Header -->
				<div class="modal-header">
					<h4 class="modal-title">ROM정보 변화도 보기</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<!-- Modal body -->
				<div class="modal-body">
					<div class="garphdata">
						<div class="graphdataheader">
							<div class="datepicker">
								<button class="btn btn-group-sm btn-light">3개월</button>
								<button class="btn btn-group-sm btn-light">6개월</button>
								<button class="btn btn-group-sm btn-light">1년</button>
								<button class="btn btn-group-sm btn-light active">전체</button>
							</div>
							<div class="graphbutton">
								<select name="direction" class="custom-select">
									<option value="front" selected>정면</option>
									<option value="side">측면</option>
								</select>
								<button class="btn  btn-group-sm btn-light active">머리</button>
								<button class="btn btn-group-sm btn-light">어깨</button>
								<button class="btn btn-group-sm btn-light">골반</button>
								<button class="btn btn-group-sm btn-light">다리</button>
							</div>
						</div>
					</div>	
					<div class="graphpart">
						<canvas class="graph" id="canvas1"></canvas>
					</div>
				</div>        
				<!-- Modal footer -->
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>        
			</div>
		</div>
	</div>
	
	
	<footer class="footer">
		COPYRIGHT(C) LianSoft.ALLRIGHT RESERVED.2020
	</footer>
	<script type="text/javascript">
		
		// Demo using plain javascript
		var comment = document.getElementById("comment");
		var clickBtn = document.getElementsByClassName('click')[0];
		// Disable the button on initial page load
		comment.disabled = true;
		//add event listener
		clickBtn.addEventListener('click', function(event) {
			comment.disabled = !comment.disabled;
		});

	</script>
	<script>
		var ctx = document.getElementById("myChart");
		var myChart = new Chart(ctx, {
		  type: 'radar',
		  data: {
			labels: ["목", "R-어깨", "R-다리", "허리", "L-다리", "L-어깨"],
			datasets: [{
			  label: '2020.04.15',
			  backgroundColor: "rgba(100,100,100,0.2)",
			  borderColor: "rgba(200,200,200,0.6)",
			  data: [50, 30, 90, 50, 80, 50, 80]
			}, {
			  label: '2020.06.14',
			  backgroundColor: "rgba(3,114,189,0.1)",
			  borderColor: "rgba(3,114,189,0.5)",
			  data: [90, 60, 90, 90, 90, 90, 60]
			}]
		  }
		});
		var ctx = document.getElementById('canvas1').getContext('2d');
		var myChart = new Chart(ctx, {
		  type: 'line',
		  data: {
			labels: ['2020.02.10', '2020.03.11', '2020.04.16', '2020.05.15', '2020.06.14'],
			datasets: [{
			  label: 'left',
			  data: [12, 19, 3, 17, 6],
			  backgroundColor: "rgba(100,100,100,0.2)",
			  borderColor: "(200,200,200,0.6)"
			}, {
			  label: 'right',
			  data: [2, 29, 5, 5, 2],
			  backgroundColor: "rgba(3,114,189,0.1)",
			  borderColor: "rgba(3,114,189,0.5)"
			}]
		  }
		});
		var ctx = document.getElementById('canvas2').getContext('2d');
		var myChart = new Chart(ctx, {
		  type: 'line',
		  data: {
			labels: ['2020.02.10', '2020.03.11', '2020.04.16', '2020.05.15', '2020.06.14'],
			datasets: [{
			  label: 'left',
			  data: [12, 19, 3, 17, 6],
			  backgroundColor: "rgba(100,100,100,0.2)",
			  borderColor: "(200,200,200,0.6)"
			}, {
			  label: 'right',
			  data: [2, 29, 5, 5, 2],
			  backgroundColor: "rgba(3,114,189,0.1)",
			  borderColor: "rgba(3,114,189,0.5)"
			}]
		  }
		});
		var ctx = document.getElementById('canvas3').getContext('2d');
		var myChart = new Chart(ctx, {
		  type: 'line',
		  data: {
			labels: ['2020.02.10', '2020.03.11', '2020.04.16', '2020.05.15', '2020.06.14'],
			datasets: [{
			  label: 'weight',
			  data: [90, 85, 87, 82, 80],
			  backgroundColor: "rgba(255,255,255,0)",
			  borderColor: "rgba(3,114,189,0.5)"
			}]
		  }
		});
	</script>
</body>
	
</html>
