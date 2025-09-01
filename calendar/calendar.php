<?php
    require_once dirname(__DIR__) . '/configs/config.class.php';
    require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
    require_once dirname(__DIR__) . '/web_addr.php';
    require_once dirname(__DIR__) . '/session_check.php';
    require_once dirname(__DIR__) . '/openadodb.php';

    $staffId  = $_SESSION['member_id'];
    $staffDep = $_SESSION['member_pDep'];

    if (! in_array($staffDep, ['1', '2', '4', '7', '13']) && $staffId != 13) {
        exit;
    }

    $staff = $_SESSION['member_name'];

    $today = $_REQUEST['d'];
    $today = preg_match("/^\d{4}\-\d{2}-\d{2}$/", $today) ? $today : date("Y-m-d");

    if ($_SESSION['calendar_count'] != '1') {
        $_SESSION['calendar_count'] = '1';

        //計算尚未建立地政士資料的數量
        $scrNo = 0;
        $sql   = 'SELECT COUNT(cId) as scrNo FROM tCalendar WHERE cCreator="' . $staffId . '" AND cScrivenerId="" AND cScrivener<>"" AND cErease = "1" ORDER BY cId ASC;';
        $rs    = $conn->Execute($sql);

        $scrNo = $rs->fields['scrNo'];
        ##

        //計算尚未建立店家資料的數量
        $stNo = 0;
        $sql  = 'SELECT COUNT(cId) as stNo FROM tCalendar WHERE cCreator="' . $staffId . '" AND cStoreId="" AND cStore<>"" AND cErease = "1" ORDER BY cId ASC;';
        $rs   = $conn->Execute($sql);

        $stNo = $rs->fields['stNo'];
        ##
    }

    //設定顯示顏色class
    $sql = 'SELECT pId, pCalenderClass FROM tPeopleInfo WHERE pDep = 7 and pJob= 1;';
    $rs  = $conn->Execute($sql);

    $displayClass = null;
    while (! $rs->EOF) {
        $displayClass[] = '.user' . $rs->fields['pId'] . ' {
		background-color: ' . $rs->fields['pCalenderClass'] . ';
	}' . "\n";

        $rs->MoveNext();
    }

    ##
?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="X-UA-Compatible" content="IE=11; IE=10; IE=9; IE=8; IE=7" />
    <title>第一建經行事曆系統</title>
    <link rel='stylesheet' href='lib/cupertino/jquery-ui.min.css' />
    <link href='fullcalendar.css' rel='stylesheet' />
    <link href='fullcalendar.print.css' rel='stylesheet' media='print' />
    <link rel="stylesheet" href="/css/colorbox.css" />
    <script src='lib/moment.min.js'></script>
    <script src='lib/jquery.min.js'></script>
    <script src='fullcalendar.min.js'></script>
    <script src='lang-all.js'></script>
    <script src='gcal.js'></script>
    <link rel="stylesheet" href="colorbox.css" />
    <script src="jquery.colorbox-min.js"></script>

    <script src="lib/jquery-ui.js"></script>

    <script type='text/Javascript'>
        $(document).ready(function() {
<?php
    if (($scrNo > 0) or ($stNo > 0)) {
        $str = '尚缺';
        $arr = [];
        if ($scrNo > 0) {
            $arr[] = '"地政士：' . $scrNo . ' 筆"';
        }

        if ($stNo > 0) {
            $arr[] = '"店家：' . $stNo . ' 筆"';
        }

        $str .= ' ' . implode('、', $arr) . ' 資料未建立!!\n請盡速建立!!';
    ?>
	//$( "#dialog" ).html('<?php //echo $str ?>//') ;
	//$( "#dialog" ).dialog({
	//	modal: true,
	//	buttons: {
	//		Ok: function() {
	//			$(this).dialog( "close" );
	//			$( "#dialog" ).html('') ;
	//		}
	//	},
	//}) ;
<?php
    }
?>

	renderCalendar() ;

	function renderCalendar() {
		$('#calendar').fullCalendar({
			theme: true,
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
            defaultView: '<?php echo in_array($staffId, [2, 3, 6, 13, 129]) ? "agendaDay" : "agendaWeek"; ?>',
			defaultDate: '<?php echo $today ?>',
			lang: 'zh-tw',
			buttonIcons: false, // show the prev/next text
			weekNumbers: true,
			editable: true,
			eventLimit: true, // allow "more" link when too many events

			dayClick: function(date,allDay,event,view) {
                var clickedTime = date.format('HH:mm'); // 獲取點擊時間
                if ((clickedTime >= '12:00' && clickedTime < '13:00') || (clickedTime >= '18:00' && clickedTime < '18:30')) {
                    //公司休息用餐時刻，不填入行程
                    return false;
                } else {
                    var url = 'eventViewAdd.php?date=' + date.format();
                    $.colorbox({
                        iframe: true,
                        width: "700px",
                        height: "100%",
                        href: url,
                        onClosed: function () {
                            recalendar();
                        }
                    });

                    return false;
                }
			},

			eventResize: function(event, delta, reverFunc) {
				var url = 'eventViewModify.php' ;
				var ss = event.start.format() ;
				var ee = event.end.format() ;
				var sn = event.id ;

				$.post(url, {'id':sn, 'sdate':ss, 'edate':ee}, function(txt) {
					//alert(txt) ;
				}) ;

			},

			eventClick: function(event) {
                if(event.className != 'leave'){ //非假勤資料
                    var url = 'eventView.php?id=' + event.id ;
                    $.colorbox({
                        iframe:true,
                        width:"700px",
                        height:"100%",
                        href:url,
                        onClosed: function(){
                            recalendar() ;
                        }
                    }) ;
                }
				return false;
			},

			eventRender: function(event) {
				$(this).fullCalendar('render')
			},

            viewRender: function(view) {
                // 公司用餐休息時刻
                $('.fc-slats tr').each(function() {
                    var timeText = $(this).find('.fc-axis').text().trim();
                    if (timeText === "中午12") {
                        $(this).addClass('disabled-time');
                        $(this).next().addClass('disabled-time');
                    } else if (timeText === "晚上6") {
                        $(this).addClass('disabled-time');
                    }
                });
            },

			events: 'getSchedule.php'
		});
	}
});

function recalendar() {
	var nowdate = $('#calendar').fullCalendar('getDate') ;
	$('[name="d"]').val(nowdate.format()) ;
	$('[name="myform"]').submit() ;
}
</script>
    <style>
    <?php #$colorArr=array('#FFFFFF', '#DDDDDD', '#AAAAAA', '#888888', '#666666', '#444444', '#000000', '#FFB7DD', '#FF88C2', '#FF44AA', '#FF0088', '#C10066', '#A20055', '#8C0044', '#FFCCCC', '#FF8888', '#FF3333', '#FF0000', '#CC0000', '#AA0000', '#880000', '#FFC8B4', '#FFA488', '#FF7744', '#FF5511', '#E63F00', '#C63300', '#A42D00', '#FFDDAA', '#FFBB66', '#FFAA33', '#FF8800', '#EE7700', '#CC6600', '#BB5500', '#FFEE99', '#FFDD55', '#FFCC22', '#FFBB00', '#DDAA00', '#AA7700', '#886600', '#FFFFBB', '#FFFF77', '#FFFF33', '#FFFF00', '#EEEE00', '#BBBB00', '#888800', '#EEFFBB', '#DDFF77', '#CCFF33', '#BBFF00', '#99DD00', '#88AA00', '#668800', '#CCFF99', '#BBFF66', '#99FF33', '#77FF00', '#66DD00', '#55AA00', '#227700', '#99FF99', '#66FF66', '#33FF33', '#00FF00', '#00DD00', '#00AA00', '#008800', '#BBFFEE', '#77FFCC', '#33FFAA', '#00FF99', '#00DD77', '#00AA55', '#008844', '#AAFFEE', '#77FFEE', '#33FFDD', '#00FFCC', '#00DDAA', '#00AA88', '#008866', '#99FFFF', '#66FFFF', '#33FFFF', '#00FFFF', '#00DDDD', '#00AAAA', '#008888', '#CCEEFF', '#77DDFF', '#33CCFF', '#00BBFF', '#009FCC', '#0088A8', '#007799', '#CCDDFF', '#99BBFF', '#5599FF', '#0066FF', '#0044BB', '#003C9D', '#003377', '#CCCCFF', '#9999FF', '#5555FF', '#0000FF', '#0000CC', '#0000AA', '#000088', '#CCBBFF', '#9F88FF', '#7744FF', '#5500FF', '#4400CC', '#2200AA', '#220088', '#D1BBFF', '#B088FF', '#9955FF', '#7700FF', '#5500DD', '#4400B3', '#3A0088', '#E8CCFF', '#D28EFF', '#B94FFF', '#9900FF', '#7700BB', '#66009D', '#550088', '#F0BBFF', '#E377FF', '#D93EFF', '#CC00FF', '#A500CC', '#7A0099', '#660077', '#FFB3FF', '#FF77FF', '#FF3EFF', '#FF00FF', '#CC00CC', '#990099', '#770077');

    ?><?php foreach ($displayClass as $class) {
        echo $class . "\n";
    }

    ?>body {
        margin: 40px 10px;
        padding: 0;
        font-family: "Lucida Grande", Helvetica, Arial, Verdana, sans-serif;
        font-size: 14px;
    }

    #calendar {
        max-width: 900px;
        margin: 0 auto;
    }

    .disabled-time {
        background-color: #e0e0e0;
        pointer-events: none;
        opacity: 0.7;
    }
    </style>
</head>

<body>
    <div id='calendar'></div>
    <form method="POST" name="myform">
        <input type="hidden" name="d">
    </form>
    <div id="dialog" title="注意"></div>
</body>

</html>