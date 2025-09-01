<?php
    require_once dirname(__DIR__) . '/configs/config.class.php';
    require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
    require_once dirname(__DIR__) . '/web_addr.php';
    require_once dirname(__DIR__) . '/session_check.php';
    require_once dirname(__DIR__) . '/openadodb.php';
    require_once dirname(__DIR__) . '/calendar/calendarOverTime.php';

    //產製時間選單
    function timeMenu($patt = '', $kind = '')
    {
        $ampm = '';
        $val  = '<option value=""></option>' . "\n";

        if ($patt) {
            for ($i = 0; $i < 24; $i++) {
                $hrVal = str_pad($i, 2, '0', STR_PAD_LEFT);

                if ($i < 12) {
                    $hr   = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $ampm = 'AM';
                } else if ($i == 12) {
                    $hr   = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $ampm = 'PM';
                } else {
                    $hr   = str_pad(($i - 12), 2, '0', STR_PAD_LEFT);
                    $ampm = 'PM';
                }

                //設定公司休息時刻不能填行程
                if ($hrVal == 12) {
                    if ($kind == 'start') {
                        $isDisabled_0 = 'disabled="disabled"';
                    }
                    $isDisabled_30 = 'disabled="disabled"';
                } else if ($hrVal == 13) {
                    if ($kind == 'end') {
                        $isDisabled_0 = 'disabled="disabled"';
                    } else {
                        $isDisabled_0 = '';
                    }
                    $isDisabled_30 = '';
                } else if ($hrVal == 18) {
                    if ($kind == 'start') {
                        $isDisabled_0  = 'disabled="disabled"';
                        $isDisabled_30 = '';
                    } else if ($kind == 'end') {
                        $isDisabled_0  = '';
                        $isDisabled_30 = 'disabled="disabled"';
                    }
                } else {
                    $isDisabled_0  = '';
                    $isDisabled_30 = '';
                }

                //整點
                $val .= '<option value="' . $hrVal . ':00:00" ' . $isDisabled_0;
                if ($patt == $hrVal . ':00:00') {
                    $val .= ' selected="selected"';
                }

                $val .= '>' . $hr . ':00 ' . $ampm . "</option>\n";
                ##

                //半點
                $val .= '<option value="' . $hrVal . ':30:00" ' . $isDisabled_30;
                if ($patt == $hrVal . ':30:00') {
                    $val .= ' selected="selected"';
                }

                $val .= '>' . $hr . ':30 ' . $ampm . "</option>\n";
                ##
            }
        }

        return $val;
    }

    $id     = $_REQUEST['id'];
    $delid  = $_POST['delid'];
    $saveid = $_POST['saveid'];

    //新增行程
    if ($saveid) {
        $val = $_POST;
        $_s  = $val['startDate'] . ' ' . $val['startTime'];
        $_e  = $val['endDate'] . ' ' . $val['endTime'];

        //確認是否進入加班行程
        $isOverTime  = false;
        $checkOutput = checkOvertime($_s, $_e);
        if (isset($checkOutput['isOverTime'])) {
            $isOverTime = $checkOutput['isOverTime'];
        }

        //由店名稱推出店編號
        if ($val['cStoreId'] == '') {
            $sql = 'SELECT bId FROM tBranch WHERE bBrand="' . $val['cBrand'] . '" AND bCategory="' . $val['cCategory'] . '" AND bStore="' . $val['cStore'] . '" AND bStore <> "";';
            $rs  = $conn->Execute($sql);

            $val['cStoreId'] = empty($rs->fields['bId']) ? '' : $rs->fields['bId'];
        }
        ##

        //由代書名稱推出代書編號
        if (! $val['cScrivenerId']) {
            $sql = 'SELECT sId FROM tScrivener WHERE sName="' . $val['cScrivener'] . '" AND sName <> "";';
            $rs  = $conn->Execute($sql);

            $val['cScrivenerId'] = empty($rs->fields['sId']) ? '' : $rs->fields['sId'];
        }
        ##

        $sql = 'UPDATE
                tCalendar
            SET
                cClass="' . $val['cClass'] . '",
                cSubject="' . $val['cSubject'] . '",
                cDescription="' . $val['cDescription'] . '",
                cBrand="' . $val['cBrand'] . '",
                cCategory="' . $val['cCategory'] . '",
                cStore="' . $val['cStore'] . '",
                cStoreId="' . $val['cStoreId'] . '",
                cScrivener="' . $val['cScrivener'] . '",
                cScrivenerId="' . $val['cScrivenerId'] . '",
                cStartDateTime="' . $_s . '",
                cEndDateTime="' . $_e . '",
                cCity = "' . $val['city'] . '"
            WHERE
                cId="' . $saveid . '";';
        $conn->Execute($sql);

        $id = $saveid;

        //加班行程串接到假勤系統
        if ($isOverTime) {
            //行程類別選單
            $calendarClass = '';
            $sql_class     = 'SELECT cName FROM tCalendarClass WHERE cId =' . $val['cClass'];
            $rs_class      = $conn->Execute($sql_class);
            if (! $rs_class->EOF) {
                $calendarClass = $rs_class->fields['cName'];
            }

            calendarOverTime([
                'calendarId'   => $saveid,
                'staffId'      => $val['cCreator'],
                'staffName'    => $val['staff'],
                'fromDateTime' => $_s,
                'toDateTime'   => $_e,
                'reason'       => $calendarClass . '：' . $val['cDescription'],
            ]);
        } else if ($_POST['isOverTimeOrigin']) {
            if (! $isOverTime) {
                calendarOverTime([
                    'calendarId'   => $saveid,
                    'staffId'      => $val['cCreator'],
                    'staffName'    => $val['staff'],
                    'fromDateTime' => '',
                    'toDateTime'   => '',
                    'reason'       => '',
                ]);
            }
        }
    }

    //刪除行程
    if ($delid) {
        $sql = 'DELETE FROM tCalendar WHERE cId="' . $delid . '";';
        $conn->Execute($sql);

        //刪除加班申請
        if ($_POST['isOverTimeOrigin']) {
            calendarOverTime([
                'calendarId'   => $delid,
                'staffId'      => $_POST['cCreator'],
                'staffName'    => $_POST['staff'],
                'fromDateTime' => '',
                'toDateTime'   => '',
                'reason'       => '',
            ]);
        }

        exit('<script>parent.$.colorbox.close();</script>');
    }

    //行程記錄
    $sql = 'SELECT
            a.*,
            b.bName as brand,
            c.cName as category,
            (SELECT pName FROM tPeopleInfo WHERE pId=a.cCreator) as staff,
            a.cCreator
        FROM
            tCalendar AS a
        LEFT JOIN
            tBrand AS b ON a.cBrand=b.bId
        LEFT JOIN
            tCategoryBranch AS c ON a.cCategory=c.cId
        WHERE
            a.cId="' . $id . '" AND a.cErease = "1";';
    $rs = $conn->Execute($sql);

    $list = [];
    if (! $rs->EOF) {
        $list = $rs->fields;
    }

    //確認店家資料是否已補上
    if (($list['cStoreId'] == '') && ($list['cStore'] != '')) {
        $sql = 'SELECT bId FROM tBranch WHERE bStore="' . $list['cStore'] . '" AND bBrand="' . $list['brand'] . '" AND bStore <> "";';
        $rs  = $conn->Execute($sql);
        if ($rs->fields['bId']) {
            $list['cStoreId'] = $rs->fields['bId'];

            //更新店編號
            $conn->Execute('UPDATE tCalendar SET cStoreId="' . $rs->fields['bId'] . '" WHERE cId="' . $list['cId'] . '";');
        }
    }

    //確認代書資料是否已補上
    if (($list['cScrivenerId'] == '') && ($list['cScrivener'] != '')) {
        $sql = 'SELECT sId FROM tScrivener WHERE sName="' . $list['cScrivener'] . '" AND sName <> "";';
        $rs  = $conn->Execute($sql);
        if ($rs->fields['sId']) {
            $list['cScrivenerId'] = $rs->fields['sId'];

            //更新代書編號
            $conn->Execute('UPDATE tCalendar SET cScrivenerId="' . $rs->fields['sId'] . '" WHERE cId="' . $list['cId'] . '";');
        }
    }

    //行程類別選單
    $cClassMenu = [];

    $sql = 'SELECT * FROM tCalendarClass WHERE cId <> 4 ORDER BY cId ASC;';
    $rs  = $conn->Execute($sql);
    while (! $rs->EOF) {
        $cClassMenu[] = $rs->fields;
        $rs->MoveNext();
    }

    //行程主題選單
    $cSubjectMenu = [];

    $sql = 'SELECT * FROM tCalendarSubject ORDER BY cId ASC;';
    $rs  = $conn->Execute($sql);
    while (! $rs->EOF) {
        $cSubjectMenu[] = $rs->fields;
        $rs->MoveNext();
    }

    //取得店家品牌名稱
    $sql = 'SELECT bId, bName FROM tBrand ORDER BY bId ASC;';
    $rs  = $conn->Execute($sql);

    $bMenu = '<option value="">品牌</option>' . "\n";
    while (! $rs->EOF) {
        $bMenu .= '<option value="' . $rs->fields['bId'] . '"';
        $bMenu .= ($list['cBrand'] == $rs->fields['bId']) ? ' selected="selected"' : '';
        $bMenu .= '>' . $rs->fields['bName'] . "</option>\n";

        $rs->MoveNext();
    }

    //建立時間選單
    $sTime = timeMenu(substr($list['cStartDateTime'], 11), 'start');
    $eTime = timeMenu(substr($list['cEndDateTime'], 11), 'end');

    //地區
    $sql = "SELECT zCity FROM tZipArea GROUP BY zCity ORDER BY nid";
    $rs  = $conn->Execute($sql);
    while (! $rs->EOF) {
        $menu_City[$rs->fields['zCity']] = $rs->fields['zCity'];
        $rs->MoveNext();
    }

    //確認原資料是否是加班行程
    $isOverTimeOrigin = false;
    $checkOutput      = checkOvertime($list['cStartDateTime'], $list['cEndDateTime']);
    if (isset($checkOutput['isOverTime'])) {
        $isOverTimeOrigin = $checkOutput['isOverTime'];
    }
?>
<!DOCTYPE html>
<html>

<head>
    <title>行程摘要</title>
    <meta charset='utf-8' />
    <link rel='stylesheet' href='lib/cupertino/jquery-ui.min.css' />
    <link href='fullcalendar.css' rel='stylesheet' />
    <link href='fullcalendar.print.css' rel='stylesheet' media='print' />
    <link href="lib/jquery-ui.css" rel="stylesheet">

    <script src='lib/moment.min.js'></script>
    <script src='lib/jquery.min.js'></script>
    <script src="lib/jquery-ui.js"></script>
    <script src='fullcalendar.min.js'></script>
    <script src='lang-all.js'></script>
    <script src='gcal.js'></script>

    <script type='text/Javascript'>
        $(document).ready(function() {
	$( "input[type=button], input[type=submit]" ).button();
	$( "input, select, textarea" ).change( function() {
		$('#change').val(1) ;
	}) ;

	$("#startDate").datepicker({
		dateFormat:"yy-mm-dd",
		selectOtherMonth: true
	}) ;

	$("#endDate").datepicker({
		dateFormat:"yy-mm-dd",
		selectOtherMonth: true
	}) ;

	$( "#dialog" ).dialog({
		modal: true,
		autoOpen: false,
		buttons: {
			Ok: function() {
				$( this ).dialog( "close" );
			}
		},
		close: function() {
			$("#dialog").html('') ;
		}
	}) ;
    <?php
        if ($saveid) {
        ?>
    $("#dialog").html('儲存完成!!') ;
    $("#dialog").dialog("open") ;
    <?php
        }
    ?>
});

function del() {
	$("#dialog1").html('請確認是否要刪除本筆行程?') ;
	$("#dialog1").dialog({
		resizable: false,
		height: 200,
		modal: true,
		buttons: {
			"刪除": function() {
				$(this).dialog("close") ;
				$('[name="delfrm"]').submit() ;
			},
 			"取消": function() {
				$(this).dialog("close") ;
			}
        }
    });
}

function getStore() {
	let _b = $('[name="cBrand"]').val() ;
	let _c = $('[name="cCategory"]').val() ;
	let url = 'getStore.php?b=' + _b + '&c=' + _c ;

	if (_b == '' || _c == '') {
		$("#dialog").html('請先輸入品牌名稱與店家種類!!') ;
		$("#dialog").dialog("open") ;
	}

	clearStore() ;

	$('[name="cStore"]').autocomplete({
		source: url
	}) ;
}

function clearStore() {
	$('[name="cStore"]').val('') ;
	$('#change').val(1) ;
}

function getScrivener() {
	let url = 'getScrivener.php' ;

	clearScrivener() ;
	$('[name="cScrivener"]').autocomplete({
		source: url
	}) ;
}

function clearScrivener() {
	$('[name="cScrivener"]').val('') ;
	$('#change').val(1) ;
}

function listStore(no) {
	window.open("storeDetail.php?s="+no,"info",config="width=550px, height=800px") ;
}

function listScrivener(no) {
	window.open("scrDetail.php?s="+no,"info",config="width=550px, height=800px") ;
}

function winclose() {
	if ($('#change').val() == '1') {
		$("#dialog1").html('行程記錄已變更!!請確認是否放棄修改!?') ;
		$("#dialog1").dialog({
			resizable: false,
			height: 200,
			modal: true,
			buttons: {
				"返回修改": function() {
					$("#dialog1").dialog("close") ;
					$("#dialog1").html('') ;
				},
				"放棄離開": function() {
					$("#dialog1").dialog("close") ;
					parent.$.colorbox.close()
				}
			}
		});
	} else {
		parent.$.colorbox.close()
	}
}

function insertBasic() {
	if ($("#change").val() == '1') {
		$("#dialog").html('行程內容已異動!!&nbsp;請先儲存後再回寫紀錄!!') ;
		$("#dialog").dialog("open") ;

        return false ;
	} else {
		let cc = $('[name="cClass"]').val() ;
		if (cc == '1') {
			if ($('[name="cStoreId"]').val() != '') {
				sendBasic(cc, $('[name="cStoreId"]').val()) ;
			} else {
				$("#dialog").html('新開發店家!&nbsp尚未建檔!!') ;
				$("#dialog").dialog("open") ;

                return false ;
			}
		} else if (cc == '2') {
			if ($('[name="cScrivenerId"]').val() != '') {
				sendBasic(cc, $('[name="cScrivenerId"]').val()) ;
			} else {
				$("#dialog").html('新開發地政士!&nbsp尚未建檔!!') ;
				$("#dialog").dialog("open") ;

                return false ;
			}
		} else {
			$("#dialog").html('其他行程類別!!&nbsp;無法寫入!!') ;
			$("#dialog").dialog("open") ;

            return false ;
		}
	}
}

function sendBasic(tp,sn) {
	let url = 'sendBasic.php' ;
	let desc = $('[name="cDescription"]').val() ;

	$.post(url,{c:tp, s:sn, t:desc},function(txt) {
		if (txt == 'T') {
			$("#dialog").html('回寫更新完成!!') ;
			$("#dialog").dialog("open") ;
		} else {
			$("#dialog").html('寫入失敗!!請洽詢相關人員!!') ;
			$("#dialog").dialog("open") ;
		}
	});
}
</script>
    <style>
    .custom-combobox {
        position: relative;
        display: inline-block;
    }

    .custom-combobox-toggle {
        position: absolute;
        top: 0;
        bottom: 0;
        margin-left: -1px;
        padding: 0;
    }

    .custom-combobox-input {
        margin: 0;
        padding: 5px 10px;
    }

    body {
        margin: 40px 10px;
        padding: 0;
        font-family: "Lucida Grande", Helvetica, Arial, Verdana, sans-serif;
        font-size: 14px;
    }

    div {
        margin-top: 10px;
        margin-bottom: 10px;
    }

    #mytb td {
        margin: 0px;
        padding: 5px;
        min-width: 120px;
    }

    .lineB {
        float: left;
        width: 160px;
    }

    .lineE {
        float: left;
    }

    .img01 {
        background: url(images/201503300605482_easyicon_net_256.png);
    }

    .lineT {
        background-color: #AED0EA;
        text-align: right;
        min-width: 80px;
        border-bottom-width: 1px;
        border-bottom-style: solid;
        border-bottom-color: #FFF;
    }
    </style>
</head>

<body>
    <center>
        <div id="detail" style="width:500px;">
            <form method="POST" name="savefrm">
                <input type="hidden" name="saveid" value="<?php echo $id?>">
                <input type="hidden" name="staff" value="<?php echo $list['staff']?>">
                <input type="hidden" name="cCreator" value="<?php echo $list['cCreator']?>">
                <input type="hidden" name="isOverTimeOrigin" value="<?php echo $isOverTimeOrigin?>">
                <div style="text-align:center;font-size:14pt;font-weight:bold;color:#550088;">編修行程</div>
                <hr>
                <table id="mytb" cellspacing="0" width="400px;">
                    <tr>
                        <td class="lineT">行程時間：</td>
                        <td>
                            <input type="text" name="startDate" id="startDate" style="width:100px;"
                                value="<?php echo substr($list['cStartDateTime'], 0, 10)?>">
                            <select name="startTime" id="startTime" style="width:100px;">
                                <?php echo $sTime?>
                            </select>（起）
                        </td>
                    </tr>
                    <tr>
                        <td class="lineT">&nbsp;</td>
                        <td>
                            <input type="text" name="endDate" id="endDate" style="width:100px;"
                                value="<?php echo substr($list['cEndDateTime'], 0, 10)?>">
                            <select name="endTime" id="endTime" style="width:100px;">
                                <?php echo $eTime?>
                            </select>（迄）
                        </td>
                    </tr>
                    <tr>
                        <td class="lineT">行程類別：</td>
                        <td>
                            <select name="cClass" id="cClass" style="width:200px;">
                                <?php
                        foreach ($cClassMenu as $k => $v) {
                            echo '<option value="' . $v['cId'] . '"';
                            echo($v['cId'] == $list['cClass']) ? ' selected="selected"' : '';
                            echo '>' . $v['cName'] . "</option>\n";
                        }
                    ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="lineT">行程主題：</td>
                        <td>
                            <select name="cSubject" style="width:200px;">
                                <?php
                        foreach ($cSubjectMenu as $k => $v) {
                            echo '<option value="' . $v['cId'] . '"';
                            echo($v['cId'] == $list['cSubject']) ? ' selected="selected"' : '';
                            echo '>' . $v['cName'] . "</option>\n";
                        }
                    ?>
                            </select>
                        </td>
                    </tr>
                    <?php
                if ($list['cStoreId']) {
                ?>
                    <tr>
                        <td class="lineT">拜訪店家：</td>
                        <td>
                            <a href="#"
                                onclick="listStore('<?php echo $list['cStoreId']?>')"><?php echo str_replace('自有品牌(類型點選加盟)', "", $list['brand']) . ' ' . $list['cStore']?></a>
                            <input type="hidden" name="cBrand" value="<?php echo $list['cBrand']?>">
                            <input type="hidden" name="cCategory" value="<?php echo $list['cCategory']?>">
                            <input type="hidden" name="cStore" value="<?php echo $list['cStore']?>">
                            <input type="hidden" name="cStoreId" value="<?php echo $list['cStoreId']?>">
                        </td>
                    </tr>
                    <?php
                } else if ($list['cStore'] && ! $list['cStoreId']) {
                ?>
                    <tr>
                        <td class="lineT">拜訪店家：</td>
                        <td>
                            <select name="cBrand" style="width:100px;" onchange="clearStore()">
                                <?php echo $bMenu?>
                            </select>

                            <select name="cCategory" style="width:60px;" onchange="clearStore()">
                                <option value="">類型</option>
                                <option <?php echo($list['cCategory'] == '1') ? 'selected="selected"' : ''; ?>value="1">
                                    加盟</option>
                                <option <?php echo($list['cCategory'] == '2') ? 'selected="selected"' : ''; ?>value="2">
                                    直營</option>
                                <option <?php echo($list['cCategory'] == '3') ? 'selected="selected"' : ''; ?>value="3">
                                    非仲介成交</option>
                            </select>

                            <input type="text" name="cStore" style="width:120px;" value="<?php echo $list['cStore']?>"
                                onclick="getStore()" onselect="clearStore()">
                            <input type="hidden" name="cStoreId" value="<?php echo $list['cStoreId']?>">
                        </td>
                    </tr>
                    <?php
                }

                if ($list['cScrivenerId']) {
                ?>
                    <tr>
                        <td class="lineT">拜訪代書：</td>
                        <td>
                            <a href="#"
                                onclick="listScrivener('<?php echo $list['cScrivenerId']?>')"><?php echo $list['cScrivener']?></a>
                            <input type="hidden" name="cScrivener" value="<?php echo $list['cScrivener']?>">
                            <input type="hidden" name="cScrivenerId" value="<?php echo $list['cScrivenerId']?>">
                        </td>
                    </tr>
                    <?php
                } else if ($list['cScrivener'] && ! $list['cScrivenerId']) {
                ?>
                    <tr>
                        <td class="lineT">拜訪代書：</td>
                        <td>
                            <input type="text" name="cScrivener" style="width:120px;"
                                value="<?php echo $list['cScrivener']?>" onclick="getScrivener()"
                                onselect="clearScrivener()">
                            <input type="hidden" name="cScrivenerId" value="<?php echo $list['cScrivenerId']?>">
                        </td>
                    </tr>
                    <?php
                }
            ?>
                    <?php if ($list['cClass'] != '4') {?>
                    <tr>
                        <td class="lineT">拜訪縣市：</td>
                        <td>
                            <select name="city" id="">
                                <option value="">請選擇</option>
                                <?php foreach ($menu_City as $key => $value): ?>
                                <?php if ($value == $list['cCity']): ?>
                                <option value="<?php echo $key?>" selected="selected="><?php echo $value?></option>
                                <?php else: ?>
                                <option value="<?php echo $key?>"><?php echo $value?></option>
                                <?php endif?>
                                <?php endforeach?>
                            </select>
                        </td>
                    </tr>
                    <?php }?>
                    <tr>
                        <td nowrap valign="top" class="lineT">
                            <div style="border:0px solid;margin-top:0px;">內容簡述：</div>
                        </td>
                        <td>
                            <textarea cols="35" rows="10" name="cDescription"
                                maxlength="200"><?php echo $list['cDescription']?></textarea>
                        </td>
                    </tr>
                </table>
                <hr>
                <div style="float:left;margin:-2px 0 0 5px;text-align:left;">
                    行程建立：<?php echo $list['staff']?>
                </div>
                <div style="float:right;margin:-2px 0 0 5px;text-align:right;">
                    建立時間：<?php echo $list['cCreateDateTime']?>
                </div>
                <div style="clear:both;"></div>
                <div style="margin-top:50px;">
                    <input type="submit" style="margin-left:2px;" value="儲存" title="儲存已修改的行程記錄">
                    <?php
                if ($_SESSION['member_id'] == '6') {
                ?>
                    <input type="button" style="margin-left:2px;" value="回寫內容" onclick="insertBasic()"
                        title="將簡述內容回寫至基本資料中">
                    <?php
                }
            ?>
                    <input type="button" style="margin-left:2px;" value="刪除" onclick="del(<?php echo $id?>)"
                        title="刪除本筆行程紀錄">
                    <input type="button" style="margin-left:2px;" value="返回" onclick="winclose()" title="返回行事曆主頁">
                </div>
            </form>
        </div>
    </center>

    <div id="dialog" title="注意"></div>
    <div id="dialog1" title="注意"></div>
    <input type="hidden" id="change" value="">
    <form method="POST" name="delfrm">
        <input type="hidden" name="delid" value="<?php echo $id?>">
        <input type="hidden" name="staff" value="<?php echo $list['staff']?>">
        <input type="hidden" name="cCreator" value="<?php echo $list['cCreator']?>">
        <input type="hidden" name="isOverTimeOrigin" value="<?php echo $isOverTimeOrigin?>">
    </form>
</body>

</html>