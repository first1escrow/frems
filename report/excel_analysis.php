<?php
require_once dirname(__DIR__) . '/web_addr.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/Writer/Excel2007.php';

//寫入清單標題列資料
if ($branch) { //複選
    $sql = "SELECT
				bId,
				bName,
				bStore,
				CONCAT((SELECT bCode FROM tBrand AS a WHERE a.bId=b.bBrand ),LPAD(bId,5,'0')) as bCode,
				(SELECT bName FROM tBrand AS c WHERE c.bId=b.bBrand) bBrand,
				bStatus
			FROM
				tBranch AS b WHERE b.bId IN(" . @implode(',', $branch) . ")";
    $rs = $conn->Execute($sql);

    $row_title = array();
    while (!$rs->EOF) {
        if (preg_match("/自有品牌/", $rs->fields['bBrand'])) {
            $rs->fields['bBrand'] = '自有品牌';
        }

        if ($rs->fields['bStatus'] == 2) {
            $rs->fields['bStatus'] = "[關店]";
        } elseif ($rs->fields['bStatus'] == 3) {
            $rs->fields['bStatus'] = "[暫停]";
        } else {
            $rs->fields['bStatus'] = '';
        }

        $row_title[$rs->fields['bId']]['storeName'] = $rs->fields['bCode'] . $rs->fields['bBrand'] . $rs->fields['bStore'] . $rs->fields['bStatus'];
        $row_title[$rs->fields['bId']]['titleName'] = $rs->fields['bBrand'] . $rs->fields['bStore'];

        $rs->MoveNext();
    }
}

$analysisData  = array();
$analysisData2 = array();
for ($i = 0; $i < count($data); $i++) {
    $date = '';
    if ($sSignDate) {
        $date = str_replace('/', '', substr($data[$i]['cSignDate'], 0, 7));
    } else if ($sApplyDate) {
        $date = str_replace('/', '', substr($data[$i]['cApplyDate'], 0, 7));
    } else if ($sEndDate) {
        $date = str_replace('/', '', substr($data[$i]['cEndDate'], 0, 7));
    }
    $analysisData2[$date]['totalMoney'] += $data[$i]['cTotalMoney'];
    $analysisData2[$date]['count']++;

    if ($branch || $brand) {
        if ($data[$i]['branch'] > 0) {
            $tmp_Store['b' . $data[$i]['branch']]['cat'] = $data[$i]['branch'];
        }

        if ($data[$i]['branch1'] > 0) {
            $tmp_Store['b' . $data[$i]['branch1']]['cat'] = $data[$i]['branch1'];
        }

        if ($data[$i]['branch2'] > 0) {
            $tmp_Store['b' . $data[$i]['branch2']]['cat'] = $data[$i]['branch2'];
        }

        if ($data[$i]['branch3'] > 0) {
            $tmp_Store['b' . $data[$i]['branch3']]['cat'] = $data[$i]['branch3'];
        }

        if ($branch) {
            $type = branch_type2($conn, $data[$i]);

            if (in_array($data[$i]['branch'], $branch)) {
                if ($data[$i]['cCaseFeedback'] == 0) {
                    $analysisData[$data[$i]['branch']][$date]['feedbackmoney'] += $data[$i]['cCaseFeedBackMoney'];
                    $analysisData2[$date]['feedbackmoney'] += $data[$i]['cCaseFeedBackMoney'];
                }

                if ($type['bid'] == $data[$i]['branch']) {
                    $analysisData[$data[$i]['branch']][$date]['count']++;
                }
            }
            if (in_array($data[$i]['branch1'], $branch)) {
                if ($data[$i]['cCaseFeedback1'] == 0) {
                    $analysisData[$data[$i]['branch1']][$date]['feedbackmoney'] += $data[$i]['cCaseFeedBackMoney1'];
                    $analysisData2[$date]['feedbackmoney'] += $data[$i]['cCaseFeedBackMoney1'];
                }

                if ($type['bid'] == $data[$i]['branch1']) {
                    $analysisData[$data[$i]['branch1']][$date]['count']++;
                }
            }

            if (in_array($data[$i]['branch2'], $branch)) {
                if ($data[$i]['cCaseFeedback2'] == 0) {
                    $analysisData[$data[$i]['branch2']][$date]['feedbackmoney'] += $data[$i]['cCaseFeedBackMoney2'];
                    $analysisData2[$date]['feedbackmoney'] += $data[$i]['cCaseFeedBackMoney2'];
                }

                if ($type['bid'] == $data[$i]['branch2']) {
                    $analysisData[$data[$i]['branch2']][$date]['count']++;
                }
            }

            if (in_array($data[$i]['branch3'], $branch)) {
                if ($data[$i]['cCaseFeedback3'] == 0) {
                    $analysisData[$data[$i]['branch3']][$date]['feedbackmoney'] += $data[$i]['cCaseFeedBackMoney3'];
                    $analysisData2[$date]['feedbackmoney'] += $data[$i]['cCaseFeedBackMoney3'];
                }

                if ($type['bid'] == $data[$i]['branch3']) {
                    $analysisData[$data[$i]['branch3']][$date]['count']++;
                }
            }

            if ($data[$i]['cSpCaseFeedBackMoney'] > 0) {
                $analysisData[$data[$i]['branch']][$date]['feedbackmoney'] += $data[$i]['cSpCaseFeedBackMoney'];
                $analysisData2[$date]['feedbackmoney'] += $data[$i]['cSpCaseFeedBackMoney'];
            }
        } else if ($brand) {
            if ($brand == $data[$i]['brand']) {
                if ($data[$i]['cCaseFeedback'] == 0) {
                    $analysisData[$date]['feedbackmoney'] += $data[$i]['cCaseFeedBackMoney'];
                    $analysisData2[$date]['feedbackmoney'] += $data[$i]['cCaseFeedBackMoney'];
                }

                $analysisData[$date]['count'] += round(1 / count($tmp_Store), 2);
            }

            if ($brand == $data[$i]['brand1']) {
                if ($data[$i]['cCaseFeedback1'] == 0) {
                    $analysisData[$date]['feedbackmoney'] += $data[$i]['cCaseFeedBackMoney1'];
                    $analysisData2[$date]['feedbackmoney'] += $data[$i]['cCaseFeedBackMoney1'];
                }

                $analysisData[$date]['count'] += round(1 / count($tmp_Store), 2);
            }

            if ($brand == $data[$i]['brand2']) {
                if ($data[$i]['cCaseFeedback2'] == 0) {
                    $analysisData[$date]['feedbackmoney'] += $data[$i]['cCaseFeedBackMoney2'];
                    $analysisData2[$date]['feedbackmoney'] += $data[$i]['cCaseFeedBackMoney2'];
                }

                $analysisData[$date]['count'] += round(1 / count($tmp_Store), 2);
            }

            if ($brand == $data[$i]['brand3']) {
                if ($data[$i]['cCaseFeedback3'] == 0) {
                    $analysisData[$date]['feedbackmoney'] += $data[$i]['cCaseFeedBackMoney3'];
                    $analysisData2[$date]['feedbackmoney'] += $data[$i]['cCaseFeedBackMoney3'];
                }

                $analysisData[$date]['count'] += round(1 / count($tmp_Store), 2);
            }

            if ($data[$i]['cSpCaseFeedBackMoney'] > 0) {
                $analysisData[$date][$date]['feedbackmoney'] += $data[$i]['cCaseFeedBackMoney'];
                $analysisData2[$date]['feedbackmoney'] += $data[$i]['cCaseFeedBackMoney'];
            }
        }

        //總回饋金額
        $tmp = getOtherFeed3($data[$i]['cCertifiedId']);
        if (is_array($tmp)) {
            foreach ($tmp as $k => $v) {
                if ($v['fType'] == 2) { //仲介
                    if ($branch) {
                        if (in_array($v['fStoreId'], $branch)) {
                            $analysisData[$v['fStoreId']][$date]['feedbackmoney'] += $v['fMoney'];
                            $analysisData2[$date]['feedbackmoney'] += $v['fMoney'];
                        }
                    } else if ($brand) {
                        if ($v['storeType'] == $brand) {
                            $analysisData[$date]['feedbackmoney'] += $data[$i]['cCaseFeedBackMoney'];
                            $analysisData2[$date]['feedbackmoney'] += $data[$i]['cCaseFeedBackMoney'];
                        }
                    }
                }
            }
        }
        $tmp = null;unset($tmp);

        $tmp = getcCertifiedMoney($data[$i]['cCertifiedMoney'], $tmp_Store);
        if (is_array($tmp)) {
            foreach ($tmp as $k => $v) {
                if ($branch) {
                    if (in_array($v['cat'], $branch)) {
                        $analysisData[$v['cat']][$date]['certifiedMoney'] += $v['money'];
                        $analysisData2[$date]['certifiedMoney'] += $v['money'];
                    }
                } else if ($v['cat'] == $brand) {
                    $analysisData2[$date]['certifiedMoney'] += $v['money'];
                }
            }
        }

        $tmp = $tmp_Store = null;
        unset($tmp, $tmp_Store);
    } else if ($scrivener) {

    } else {
        //總回饋金額
        $tmp = getOtherFeedMoney($data[$i]['cCertifiedId']);
        $analysisData2[$date]['certifiedMoney'] += $data[$i]['cCertifiedMoney'];

        if ($data[$i]['brand'] > 0) {
            if ($data[$i]['cCaseFeedback'] == 0) {
                $analysisData2[$date]['feedbackmoney'] += $data[$i]['cCaseFeedBackMoney'];
            }
        }

        if ($data[$i]['brand1'] > 0) {
            if ($data[$i]['cCaseFeedback1'] == 0) {
                $analysisData2[$date]['feedbackmoney'] += $data[$i]['cCaseFeedBackMoney1'];
            }
        }

        if ($data[$i]['brand2'] > 0) {
            if ($data[$i]['cCaseFeedback2'] == 0) {
                $analysisData2[$date]['feedbackmoney'] += $data[$i]['cCaseFeedBackMoney2'];
            }
        }

        if ($data[$i]['brand3'] > 0) {
            if ($data[$i]['cCaseFeedback3'] == 0) {
                $analysisData2[$date]['feedbackmoney'] += $data[$i]['cCaseFeedBackMoney3'];
            }
        }

        if ($data[$i]['cSpCaseFeedBackMoney'] > 0) {
            $analysisData2[$date]['feedbackmoney'] += $data[$i]['cSpCaseFeedBackMoney'];
        }

        if ($tmp['fMoney'] > 0) {
            $analysisData2[$date]['feedbackmoney'] += $tmp['fMoney'];
        }

        $tmp = null;unset($tmp);
    }
}

$objPHPExcel = new PHPExcel();

//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("案件統計表");
$objPHPExcel->getProperties()->setDescription("第一建經案件統計表");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);
//命名工作表標籤
$objPHPExcel->getActiveSheet()->setTitle('案件統計報表');

$col = 65;
$row = 1;
if ($sSignDate) {
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '簽約月份');
} else if ($sApplyDate) {
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '進案月份');
} else if ($sEndDate) {
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '結案月份');
}

$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '案件總筆數');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '案件買賣總價金');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '保證費金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '回饋金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '收入');

$row++;

$col = 65;
ksort($analysisData2);
foreach ($analysisData2 as $key => $value) {
    $col = 65;
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $key);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $value['count']);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $value['totalMoney']);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $value['certifiedMoney']);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $value['feedbackmoney']);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, ($value['certifiedMoney'] - $value['feedbackmoney']));

    $row++;
}

$row++;

if ($branch) {
    $tabCount = 1;
    foreach ($row_title as $k => $v) {
        $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex($tabCount);
        $objPHPExcel->getActiveSheet()->setTitle($v['titleName']);

        $col = 65;
        $row = 1;

        if ($sSignDate) {
            $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '簽約月份');
        } else if ($sApplyDate) {
            $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '進案月份');
        } else if ($sEndDate) {
            $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '結案月份');
        }

        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '案件總筆數');
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '案件買賣總價金');
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '保證費金額');
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '回饋金額');
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '收入');

        $row++;

        foreach ($analysisData[$k] as $key => $value) {
            $col = 65;
            $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $key);
            $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $value['count']);
            $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $analysisData2[$key]['totalMoney']);
            $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $value['certifiedMoney']);
            $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $value['feedbackmoney']);
            $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, ($value['certifiedMoney'] - $value['feedbackmoney']));

            $row++;
        }

        $tabCount++;
    }
}

$_file = iconv('UTF-8', 'BIG5', '案件統計表-');
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type:application/force-download');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $_file . '.xlsx');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("php://output");

exit;
