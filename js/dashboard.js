// 기타 전역 변수
var $color = ['#ffc000', '#5b9bd5', '#70ad47', '#a86ed4', '#ed7d31'];
var $day = ['일','월','화','수','목','금','토'];

// 초기 데이터 GET
const getAjaxData = ( params ) => {
  useAjax('getDashboardData', (resData) => {
    let _data = resData.split('|');
    let data = {
      scheduleData: JSON.parse(_data[0]),
      checkinData: JSON.parse(_data[1]),
      sellData: JSON.parse(_data[2]),
      joinData: JSON.parse(_data[3]),
      workerData: JSON.parse(_data[4])
    }
    PRINT_DATA(data);
    useAjax('getNoticeList', (resData) => {
        let data = JSON.parse(resData);
        if (data.result == 'Fail') {
            return;
        }
        $('div.dash_board.box4 > div.left > div > ol').empty();
        data.forEach((data, idx) => {
            if (data.NOTICE_TYPE == '2') {
                $('div.dash_board.box4 > div.left > div > ol').append(`
                    <li>${data.NOTICE_CONTENTS}</li>
                `)
            }
        })
    });
  }, params);
}

function PAY_CUT (pay) {
    let temp = Number(pay);
    let result = 0;
    let unit = '';

    if (temp >= 10000) {
        result = Number(String(temp).slice(0, -4));
        unit = '만원';
    } else {
        result = temp;
        unit = '원';
    }

    return [result, unit];
}

const PRINT_DATA = ( { scheduleData, checkinData, sellData, joinData, workerData } ) => {

  let scheduleEl = {  // 금일 스케줄 출석률
    total: $('.box1 div.chart_all > table td.count').eq(0),
    personal: $('.box1 div.chart_all > table td.count').eq(1),
    group: $('.box1 div.chart_all > table td.count').eq(2),
    total_per: $('.box1 div.chart_all > table td > div.progressBarWrap > .progressBar > .percent').eq(0),
    personal_per: $('.box1 div.chart_all > table td > div.progressBarWrap > .progressBar > .percent').eq(1),
    group_per: $('.box1 div.chart_all > table td > div.progressBarWrap > .progressBar > .percent').eq(2),
    total_progress: $('.box1 div.chart_all > table td > div.progressBarWrap > .progressBar').eq(0),
    personal_progress: $('.box1 div.chart_all > table td > div.progressBarWrap > .progressBar').eq(1),
    group_progress: $('.box1 div.chart_all > table td > div.progressBarWrap > .progressBar').eq(2),
    print: function () {
        let sumTotal = (+scheduleData[0].total) + (+scheduleData[1].total);
        let sumAttend = (+scheduleData[0].attend) + (+scheduleData[1].attend);
        let calcTotalPercent = (sumAttend == 0 && sumTotal == 0) ? 0 : (sumAttend / sumTotal) * 100;
        let calcPersonalPercent = scheduleData[0].attend == 0 && scheduleData[0].total == 0 ? 0 : (scheduleData[0].attend / scheduleData[0].total * 100);
        let calcGroupPercent = scheduleData[1].attend == 0 && scheduleData[1].total == 0 ? 0 : (scheduleData[1].attend / scheduleData[0].total * 100);

        this.personal.text(`${scheduleData[0].attend} / ${scheduleData[0].total}`);
        this.group.text(`${scheduleData[1].attend} / ${scheduleData[1].total}`);
        this.total.text(`${sumAttend} / ${sumTotal}`);
        this.total_per.text(`${~~calcTotalPercent} %`);
        this.personal_per.text(`${~~calcPersonalPercent} %`);
        this.group_per.text(`${~~calcGroupPercent} %`);
        this.total_progress.css('width', calcTotalPercent + '%');
        this.personal_progress.css('width', calcPersonalPercent + '%');
        this.group_progress.css('width', calcGroupPercent + '%');
    }
  }

  let checkinEl = { // 금일 체크인
    progress: $('.box7 > div.progressBarTle > div.progressBar'),
    per: $('.box7 > div.progressBarTle > p'),
    now: $('.box7 > div:last-of-type > p > span:last-of-type').eq(0),
    today: $('.box7 > div:last-of-type > p > span:last-of-type').eq(1),
    total: $('.box7 > div:last-of-type > p > span:last-of-type').eq(2),
    print: function () {
        this.progress.css('width', 0);
        this.per.text('0%');
        this.now.text('0명');
        this.today.text('0명');
        this.total.text('0명');
    }
  }

  let sellEl = {  // 금월 매출
    total_B: $('.box3 .piecenter > p:nth-of-type(2)'),
    total_unit: $('.box3 .piecenter > p:nth-of-type(3)'),
    total_S: $('.box3 .right > table td > div.total_info > span.total_number'),
    list_wrap: $('.box3 .right > table > tbody'),
    chartCircle: $('.box3 > div > div.pie'),
    print: function () {
        var percentArr = [];
        this.list_wrap.empty();
        let sumPay = 0;
        if (sellData.length == 0) {
            this.total_B.text(0);
            this.list_wrap.append('<tr><td class="nonePay">금월의 매출이 없습니다.</td></tr>')
        } else {
            for (let data of sellData) {
                sumPay += parseInt(data['SUM(a.SELLINGPRICE)']);
            }
            // 리스트 하나의 금액 / 총합금액 * 100
            for (let i in sellData) {
                var [calcPay, unit] = PAY_CUT(sellData[i]['SUM(a.SELLINGPRICE)']);
                let percent = parseInt(sellData[i]['SUM(a.SELLINGPRICE)']) / sumPay * 100;
                let tag1 = `<tr><th><span class="rect" style="background-color: `;
                let tag2 = `${$color[i]}"></span>${sellData[i].CATEGORY_NAME}</th>`;
                let tag3 = `<td><div class="barTle"><div class="GaugeBar" style="background: ${$color[i]}; width: ${percent}%">`;
                let tag4 = `<span title="매출량(%)"><span class="part_percent">`;
                let tag5 = `${0}</span>%(<span class="number">${numberFormat(calcPay)}</span>${unit})`;
                let tag6 = `</span></div></div></td></tr>`;
                this.list_wrap.append(tag1 + tag2 + tag3 + tag4 + tag5 + tag6);
                percentArr.push(percent);
            }
            this.total_B.text(numberFormat(PAY_CUT(sumPay)[0]));
            this.total_unit.text(numberFormat(PAY_CUT(sumPay)[1]));
        }
        
        // [ 70%, 30% ]
        // 도넛차트
        var css = '';
        percentArr.forEach((data, idx) => {
            let before = idx == 0 ? 0 : percentArr[idx - 1];
            let after = idx == percentArr.length - 1 ? 100 : percentArr[idx];
            let comma = idx == percentArr.length - 1 ? '' : ',';

            css += `${$color[idx]} ${before}% ${after}% ${comma}`;
        });
        if (percentArr.length == 0) {
            css = '#999 0% 100%';
        } else if (percentArr.length == 1) {
            css = `${$color[0]} 0% 100%`;
        }
        this.chartCircle.css('background', `conic-gradient(${css})`);

    }
  }

  let joinEl = {  // 금월 가입자
    total: $('.box2 > div.gender > p.total > b'),
    male: $('.box2 p.male > span'),
    female: $('.box2 p.female > span'),
    print: function () {
        this.total.text(joinData[0]['count(*)'] + '명');
    }
  }

  let workerEl = {  // 금일 근무자
    listWrap: $('.box6 > ul'),
    print: function () {
        this.listWrap.empty();
        for (let i in workerData) {
            let firstName = workerData[i].USER_NM.split('')[0];
            this.listWrap.append(`
                <li data-sq="${workerData[i].MANAGER_SQ}">
                    <div style="background-color: ${$color[i]}" class="circle">${firstName}</div>
                    <div><p>${workerData[i].USER_NM}</p><p>트레이너</p></div>
                </li>
            `)
        }
    }
  }

  // 메서드 호출
  scheduleEl.print();
  checkinEl.print();
  sellEl.print();
  joinEl.print();
  workerEl.print();

}

$(function () {
  
    // 초기 데이터 GET & 함수 호출 (async, print)
    let [ CURRENT_DT, START_DT, END_DT] = START_END_DT_RETURN(new Date());
    getAjaxData( { CURRENT_DT: CURRENT_DT, START_DT: START_DT, END_DT: END_DT } );

    // 금일 달력 도움말
    $('div.dash_board.box4 > i').click(function(){
        $(this).parent().find('div.cal-info').css({'transform': 'translateY(0)'});
        setTimeout(() => {
            $('div.dash_board.box4').find('div.cal-info').css({
                'transform': 'translateY(-100%)'
            });
        }, 3000);
    }); // 금일 달력 도움말

    // 자동 시간
    setInterval(() => {
        var newDate = new Date();
        $('#date').text(dateFormat(newDate) + ' (' + $day[newDate.getDay()] + ')');
        $('#time').text(timeFormat(newDate));
    },1000); // 자동 시간

}); // document.ready()