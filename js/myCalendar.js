




$(document).ready(function(){
    var now = new Date();
    var year = now.getFullYear();
    var month = now.getMonth() + 1;
    var month_1 = now.getMonth();
    var date = now.getDate();

    var data = [{
        date: year + '-' + month + '-' + (date - 1),
        value: ''
    }, {
        date: year + '-' + month + '-' + date,
        value: ''
    }, {
        date: new Date(year, month - 1, date + 1),
        value: ''
    }, {
        date: '',
        value: ''
    }];

    // inline
    var $ca = $('#one').calendar({
        width: 230,
        height: 230,
        data: data,
        monthArray: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sept','Oct','Nov','Dec'],
        date: new Date(year, month_1, date),
        onSelected: function (view, date, data) {
          let [ today, start, end ] = START_END_DT_RETURN(date);
          getAjaxData({
            CURRENT_DT: today,
            START_DT: start,
            END_DT: end
          });
        },
        viewChange: function (view, y, m) {
            // console.log(view, y, m)
        }
    });

    // picker
    $('#two').calendar({
        trigger: '#dt',
        // offset: [0, 1],
        zIndex: 999,
        data: data,
        onSelected: function (view, date, data) {
            console.log('event: onSelected')
        },
        onClose: function (view, date, data) {
            console.log('event: onClose')
            console.log('view:' + view)
            console.log('date:' + date)
            console.log('data:' + (data || ''));
        }
    });

    // Dynamic elements
    var $demo = $('#demo');
    var UID = 1;
    $('#add').click(function () {
        $demo.append('<input id="input-' + UID + '"><div id="ca-' + UID + '"></div>');
        $('#ca-' + UID).calendar({
            trigger: '#input-' + UID++
        });
    });

});