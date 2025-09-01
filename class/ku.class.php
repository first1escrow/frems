<?php
namespace First1\V1\KU;

require_once dirname(__DIR__) . '/first1DB.php';

class Ku
{
    private $conn;

    public function __construct()
    {
        $this->conn = new \first1DB;
        return $this;
    }

    public function getCPI($date)
    {
        if (empty($date)) {
            return false;
        }

        $tmp  = explode('/', $date);
        $date = date('Y-m-d', mktime(0, 0, 0, $tmp[1], 1, $tmp[0] + 1911)); //將民國年轉為西元年
        $tmp  = null;unset($tmp);

        $sql = 'SELECT cCPI FROM tCPITax WHERE cDate = :date;';
        $rs  = $this->conn->one($sql, ['date' => $date]);

        return empty($rs) ? false : $rs['cCPI'];
    }

    public function getCPITax($date)
    {
        if (empty($date)) {
            return false;
        }

        $tmp  = explode('/', $date);
        $date = date('Y-m-d', mktime(0, 0, 0, $tmp[1], 1, $tmp[0] + 1911)); //將民國年轉為西元年
        $tmp  = null;unset($tmp);

        $sql = 'SELECT cCPI FROM tCPITax WHERE cDate = :date;';
        $rs  = $this->conn->one($sql, ['date' => $date]);

        return empty($rs) ? false : $rs['cCPI'];
    }

    public function getBuildings($cId)
    {
        $sql = 'SELECT
                    a.*,
                    (SELECT bTypeName FROM tBuildingMaterials WHERE bTypeId = a.cBudMaterial) as material,
                    (SELECT zCity FROM tZipArea WHERE zZip = a.cZip) as city,
                    (SELECT zArea FROM tZipArea WHERE zZip = a.cZip) as district
                FROM
                    tContractProperty AS a
                WHERE
                    a.cCertifiedId = :cId;';
        $buildings = $this->conn->all($sql, ['cId' => $cId]);

        if (empty($buildings)) {
            throw new \Exception('No Building Data Founded In Database.');
        }

        $extra = $this->getContractPropertyBuildingLandNo($cId, array_column($buildings, 'cItem'));
        $level = $this->getContractPropertyLevel($cId, array_column($buildings, 'cItem'));

        foreach ($buildings as $k => $v) {
            $cItem = $v['cItem'];

            $buildings[$k]['extra'] = array_values(array_filter($extra, function ($item) use ($cItem) {
                if ($item['cItem'] == $cItem) {
                    return $item;
                }
            }));

            $buildings[$k]['level'] = array_values(array_filter($level, function ($item) use ($cItem) {
                if (($item['cBuildItem'] == $cItem) && preg_match("/層/iu", $item['cLevelUse']) && ($item['cCategory'] == '1')) {
                    return $item;
                }
            }));

            $buildings[$k]['sub'] = array_values(array_filter($level, function ($item) use ($cItem) {
                if (($item['cBuildItem'] == $cItem) && ($item['cCategory'] == '2')) {
                    return $item;
                }
            }));

            $buildings[$k]['share'] = array_values(array_filter($level, function ($item) use ($cItem) {
                if (($item['cBuildItem'] == $cItem) && ($item['cCategory'] == '3')) {
                    return $item;
                }
            }));

        }
        $buildings[0]['parking'] = $this->getContractParking($cId);

        // print_r($buildings);exit;

        return $buildings;
    }

    public function getRealties($cId)
    {
        $realty_id = $this->getRealtyId($cId);
        if (empty($realty_id)) {
            return [];
        }

        $sql = 'SELECT a.bName as company, a.bSerialnum, a.bTelArea, a.bTelMain FROM tBranch AS a WHERE a.bId IN (' . implode(',', $realty_id) . ');';
        $rs  = $this->conn->all($sql);

        if (empty($rs)) {
            return [];
        }

        $rs = array_map(function ($item) {
            $tel = empty($item['bTelArea']) ? '' : $item['bTelArea'];
            $tel .= empty($tel) ? '' : '-';
            $tel .= empty($item['bTelMain']) ? '' : $item['bTelMain'];

            return [
                'company' => $item['company'],
                'serial'  => $item['bSerialnum'],
                'tel'     => $tel,
            ];
        }, $rs);

        return $rs;
    }

    private function getRealtyId($cId)
    {
        $sql = 'SELECT cBranchNum, cBranchNum1, cBranchNum2, cBranchNum3 FROM tContractRealestate WHERE cCertifyId = :cId;';
        $rs  = $this->conn->one($sql, ['cId' => $cId]);

        if (empty($rs)) {
            return [];
        }

        $realtyId = [];
        if (! empty($rs['cBranchNum']) && $rs['cBranchNum'] != '505') {
            $realtyId[] = $rs['cBranchNum'];
        }

        if (! empty($rs['cBranchNum1']) && $rs['cBranchNum1'] != '505') {
            $realtyId[] = $rs['cBranchNum1'];
        }

        if (! empty($rs['cBranchNum2']) && $rs['cBranchNum2'] != '505') {
            $realtyId[] = $rs['cBranchNum2'];
        }

        if (! empty($rs['cBranchNum3']) && $rs['cBranchNum3'] != '505') {
            $realtyId[] = $rs['cBranchNum3'];
        }

        return $realtyId;
    }

    private function getContractParking($cId)
    {
        $sql = 'SELECT * FROM tContractParking WHERE cCertifiedId = :cId';
        return $this->conn->all($sql, ['cId' => $cId]);
    }

    private function getContractPropertyLevel($cId, $items)
    {
        $sql = 'SELECT * FROM tContractPropertyObject WHERE cCertifiedId = :cId AND cBuildItem IN (' . implode(',', $items) . ');';
        $rs  = $this->conn->all($sql, ['cId' => $cId]);

        $level = [];
        if (! empty($rs)) {
            $level = array_filter($rs, function ($item) {
                if (! empty($item['cCategory']) && ! empty($item['cLevelUse']) && ! empty($item['cMeasureMain']) && ! empty($item['cMeasureTotal'])) {
                    return $item;
                }
            });
        }

        return $level;
    }

    private function getContractPropertyBuildingLandNo($cId, $items)
    {
        $sql = 'SELECT * FROM tContractPropertyBuildingLandNo WHERE cCertifiedId = :cId AND cItem IN (' . implode(',', $items) . ');';
        return $this->conn->all($sql, ['cId' => $cId]);
    }

    public function getLands($cId)
    {
        $sql = 'SELECT
                    a.cItem,
                    a.cZip,
                    a.cAddr,
                    a.cLand1,
                    a.cLand2,
                    a.cLand3,
                    a.cMeasure,
                    a.cMoney,
                    a.cPower1,
                    a.cPower2,
                    (SELECT cTotalMoney FROM tContractIncome WHERE cCertifiedId = a.cCertifiedId) as totalMoney,
                    (SELECT zCity FROM tZipArea WHERE zZip = a.cZip) as city,
                    (SELECT zArea FROM tZipArea WHERE zZip = a.cZip) as district
                FROM
                    tContractLand AS a
                WHERE
                    a.cCertifiedId = :cId AND a.cLand3 <> "";';
        $lands = $this->conn->all($sql, ['cId' => $cId]);

        if (empty($lands)) {
            // throw new \Exception('No Land Data Founded In Database.');
            return [];
        }

        // $extra = $this->getContractLandPrice($cId, array_column($lands, 'cItem'));
        $extra = $this->tContractTransferAreaBefore($cId, array_column($lands, 'cItem'));

        foreach ($lands as $k => $v) {
            $cItem = $v['cItem'];

            if (! empty($extra)) {
                foreach ($extra as $item) {
                    if ($item['cLandItem'] == $cItem) { //如果土地主項目($cItem) = 前次紀錄($item['cLandItem'])
                        $item['cLand3']                            = $v['cLand3'];
                        list($item['cIdentifyId'], $item['cName']) = $this->convertBuyerOwnerIdentityId($cId, $item['cTarget'], $item['cIdentifyId']);
                        $lands[$k]['extra'][]                      = $item;
                    }
                }
            }

            $lands[$k]['one'] = (empty($lands[$k]['extra']) || (count($lands[$k]['extra']) <= 1)) ? true : false; //每筆地號僅有一筆前次移轉(true)或多筆(false)
        }

        return $lands;
    }

    private function convertBuyerOwnerIdentityId($cId, $target, $identify_id)
    {
        return in_array($target, [1, 2, 5]) ? $this->getOtherIdentityId($cId, $target, $identify_id) : $this->getBuyerOwnerIdentityId($cId, $target, $identify_id);
    }

    private function getOtherIdentityId($cId, $target, $id)
    {
        $sql = 'SELECT cName, cIdentifyId FROM tContractOthers WHERE cCertifiedId = :cId AND cIdentity = :target AND cIdentifyId = :id;';
        $rs  = $this->conn->one($sql, ['cId' => $cId, 'target' => $target, 'id' => $id]);

        return empty($rs['cIdentifyId']) ? [null, null] : [$rs['cIdentifyId'], $rs['cName']];
    }

    private function getBuyerOwnerIdentityId($cId, $target, $id)
    {
        $table = ($target == 3) ? 'tContractBuyer' : 'tContractOwner';

        $sql = 'SELECT cName, cIdentifyId FROM ' . $table . ' WHERE cCertifiedId = :cId AND cIdentifyId = :id;';
        $rs  = $this->conn->one($sql, ['cId' => $cId, 'id' => $id]);

        return empty($rs['cIdentifyId']) ? [null, null] : [$rs['cIdentifyId'], $rs['cName']];
    }

    private function getContractLandPrice($cId, $items)
    {
        $sql = 'SELECT cLandItem, cItem, cMoveDate, cLandPrice, cPower1, cPower2 FROM tContractLandPrice WHERE cCertifiedId = :cId AND cLandItem IN (' . implode(',', $items) . ') AND cDel = 0;';
        $rs  = $this->conn->all($sql, ['cId' => $cId]);

        $extra = [];
        $extra = array_filter($rs, function ($v) {
            if (! empty($v['cMoveDate']) && ! empty($v['cLandPrice']) && ! empty($v['cPower1']) && ! empty($v['cPower2'])) {
                return $v;
            }
        });

        return $extra;
    }

    private function tContractTransferAreaBefore($cId, $items)
    {
        $sql = 'SELECT
                    a.cTarget,
                    a.cIdentifyId,
                    b.cCertifiedId,
                    b.cLandItem,
                    b.cItem,
                    b.cPower1,
                    b.cPower2,
                    CONVERT(b.cMoveDate, CHAR) AS cMoveDate,
                    b.cLandPrice
                FROM
                    tContractLandPrice AS b
                LEFT JOIN
                    tContractTransferAreaBefore AS a ON a.cCertifiedId = b.cCertifiedId
                WHERE
                    b.cCertifiedId = "' . $cId . '"
                    AND b.cLandItem IN (' . implode(',', $items) . ')
                ORDER BY
                    a.cLandItem, a.cIdentifyId, b.cMoveDate
                ASC;';
        $rs = $this->conn->all($sql, ['cId' => $cId]);

        $extra = [];
        $extra = array_filter($rs, function ($v) {
            if (! empty($v['cMoveDate']) && ! empty($v['cLandPrice']) && ! empty($v['cPower1']) && ! empty($v['cPower2'])) {
                return $v;
            }
        });

        foreach ($extra as $k => $v) {
            $v['cMoveDate'] = ($v['cMoveDate'] == '0000-00-00') ? '' : $v['cMoveDate'];

            if (! empty($v['cMoveDate']) && preg_match("/^\d{4}\-\d{2}\-\d{2}$/", $v['cMoveDate'])) {
                $tmp = explode('-', $v['cMoveDate']);
                $tmp[0] -= 1911;
                $tmp[2] = ('00' == $tmp[2]) ? '01' : $tmp[2]; //如果日為00，則改為01

                $extra[$k]['cMoveDate'] = str_pad($tmp[0], 4, '0', STR_PAD_LEFT) . '/' . $tmp[1] . '/' . $tmp[2];
                $tmp                    = null;unset($tmp);
            }
        }

        return $extra;
    }

    public function getOwners($cId)
    {
        return $this->getBuyerOwners($cId, 2);
    }

    public function getBuyers($cId)
    {
        $buyers = $this->getRegisterBuyer($cId); //買方登記名義人

        return empty($buyers) ? $this->getBuyerOwners($cId, 1) : $buyers; //如果無買方登記名義人，則取得買方
    }

    private function getRegisterBuyer($cId)
    {
        //買方登記名義人 tContractOthers -> cIdentity = 5
        $sql = 'SELECT
                    a.*,
                    (SELECT zCity FROM tZipArea WHERE zZip = a.cRegistZip) as registCity,
                    (SELECT zArea FROM tZipArea WHERE zZip = a.cRegistZip) as registDistrict,
                    (SELECT zCity FROM tZipArea WHERE zZip = a.cBaseZip) as baseCity,
                    (SELECT zArea FROM tZipArea WHERE zZip = a.cBaseZip) as baseDistrict
                FROM
                    tContractOthers AS a
                WHERE
                    a.cIdentity = 5
                    AND a.cCertifiedId = :cId;';
        $rs = $this->conn->all($sql, ['cId' => $cId]);

        if (empty($rs)) {
            return [];
        }

        $buyers = [];
        foreach ($rs as $v) {
            $land     = $this->getTransferArea($cId, 'L', $v['cId']);
            $building = $this->getTransferArea($cId, 'B', $v['cId']);
            $item     = $this->getObjectItems($land, $building);
            $tel1     = $this->formatTelephoneNo($rs['cTelArea1'], $rs['cTelMain1']);
            $tel2     = $this->formatTelephoneNo($rs['cTelArea2'], $rs['cTelMain2']);

            $buyers[] = [
                'name'           => $v['cName'],
                'identifyId'     => $v['cIdentifyId'],
                'land'           => $land,
                'building'       => $building,
                'item'           => $item,
                'registZip'      => $v['cRegistZip'],
                'registCity'     => $v['registCity'],
                'registDistrict' => $v['registDistrict'],
                'registAddr'     => $v['cRegistAddr'],
                'baseZip'        => $v['cBaseZip'],
                'baseCity'       => $v['baseCity'],
                'baseDistrict'   => $v['baseDistrict'],
                'baseAddr'       => $v['cBaseAddr'],
                'birthday'       => $v['cBirthdayDay'],
                'tel1'           => $tel1,
                'tel2'           => $tel2,
                'mobile'         => $this->formatMobile($v['cMobileNum']),
            ];
        }

        return $buyers;
    }

    private function getBuyerOwners($cId, $target)
    {
        $table = ($target == '1') ? 'tContractBuyer' : 'tContractOwner';

        $sql = 'SELECT
                    a.*,
                    (SELECT zCity FROM tZipArea WHERE zZip = a.cRegistZip) as registCity,
                    (SELECT zArea FROM tZipArea WHERE zZip = a.cRegistZip) as registDistrict,
                    (SELECT zCity FROM tZipArea WHERE zZip = a.cBaseZip) as baseCity,
                    (SELECT zArea FROM tZipArea WHERE zZip = a.cBaseZip) as baseDistrict
                FROM
                    ' . $table . ' AS a
                WHERE
                    a.cCertifiedId = :cId;';
        $rs = $this->conn->one($sql, ['cId' => $cId]);

        $land     = $this->getTransferArea($cId, 'L', $rs['cId']);
        $building = $this->getTransferArea($cId, 'B', $rs['cId']);
        $item     = $this->getObjectItems($land, $building);
        $tel1     = $this->formatTelephoneNo($rs['cTelArea1'], $rs['cTelMain1']);
        $tel2     = $this->formatTelephoneNo($rs['cTelArea2'], $rs['cTelMain2']);

        $people = empty($rs) ?
        [] : [[
            'name'           => $rs['cName'],
            'identifyId'     => $rs['cIdentifyId'],
            'land'           => $land,
            'building'       => $building,
            'item'           => $item,
            'registZip'      => $rs['cRegistZip'],
            'registCity'     => $rs['registCity'],
            'registDistrict' => $rs['registDistrict'],
            'registAddr'     => $rs['cRegistAddr'],
            'baseZip'        => $rs['cBaseZip'],
            'baseCity'       => $rs['baseCity'],
            'baseDistrict'   => $rs['baseDistrict'],
            'baseAddr'       => $rs['cBaseAddr'],
            'birthday'       => $rs['cBirthdayDay'],
            'tel1'           => $tel1,
            'tel2'           => $tel2,
            'mobile'         => $this->formatMobile($rs['cMobileNum']),
        ]];

        $land = $building = $item = $tel1 = $tel2 = null;
        unset($land, $building, $item, $tel1, $tel2);

        $others = $this->getOthers($cId, $target);

        return array_merge($people, $others);
    }

    private function getOthers($cId, $target)
    {
        $sql = 'SELECT
                    a.*,
                    (SELECT zCity FROM tZipArea WHERE zZip = a.cRegistZip) as registCity,
                    (SELECT zArea FROM tZipArea WHERE zZip = a.cRegistZip) as registDistrict,
                    (SELECT zCity FROM tZipArea WHERE zZip = a.cBaseZip) as baseCity,
                    (SELECT zArea FROM tZipArea WHERE zZip = a.cBaseZip) as baseDistrict
                FROM
                    tContractOthers AS a
                WHERE
                    a.cCertifiedId = :cId
                    AND a.cIdentity = :target;';
        $rs = $this->conn->all($sql, ['cId' => $cId, 'target' => $target]);

        if (empty($rs)) {
            return [];
        }

        $owners = [];
        foreach ($rs as $v) {
            $land     = $this->getTransferArea($cId, 'L', $v['cId']);
            $building = $this->getTransferArea($cId, 'B', $v['cId']);
            $item     = $this->getObjectItems($land, $building);
            $tel1     = '';
            $tel2     = '';

            $owners[] = [
                'name'           => $v['cName'],
                'identifyId'     => $v['cIdentifyId'],
                'land'           => $land,
                'building'       => $building,
                'item'           => $item,
                'registZip'      => $v['cRegistZip'],
                'registCity'     => $v['registCity'],
                'registDistrict' => $v['registDistrict'],
                'registAddr'     => $v['cRegistAddr'],
                'baseZip'        => $v['cBaseZip'],
                'baseCity'       => $v['baseCity'],
                'baseDistrict'   => $v['baseDistrict'],
                'baseAddr'       => $v['cBaseAddr'],
                'birthday'       => $v['cBirthdayDay'],
                'tel1'           => $tel1,
                'tel2'           => $tel2,
                'mobile'         => $this->formatMobile($v['cMobileNum']),
            ];

            $land = $building = $item = $tel1 = $tel2 = null;
            unset($land, $building, $item, $tel1, $tel2);
        }

        return $owners;
    }

    public function getObjectUse($id = null)
    {
        $sql = empty($id) ? '' : ' WHERE uId = "' . $id . '"';
        $sql = 'SELECT uId, uName FROM tObjUse' . $sql . ';';
        $rs  = $this->conn->all($sql);

        $use = [];
        foreach ($rs as $v) {
            $use[$v['uId']] = $v['uName'];
        }

        return $use;
    }

    private function getTransferArea($cId, $type, $idenitify_id)
    {
        if (! empty($idenitify_id)) {
            $sql = ' AND cIdentifyId = :idenitify_id ';
        }

        $sql = 'SELECT cTarget, cTransferType, cTransferItem, cTranferPower1, cTranferPower2 FROM tContractTransferArea WHERE cCertifiedId = :cId AND cTransferType = :type ' . $sql . ';';
        return $this->conn->all($sql, ['cId' => $cId, 'type' => $type, 'idenitify_id' => $idenitify_id]);
    }

    private function getObjectItems($land, $building)
    {
        $_item = array_unique(array_merge(array_column($land, 'cTransferItem'), array_column($building, 'cTransferItem')));

        $item = [];
        if (! empty($_item)) {
            foreach ($_item as $v) {
                $_lands     = [];
                $_buildings = [];

                if (! empty($land)) {
                    $_lands = array_values(array_filter($land, function ($val) use ($v) {
                        if ($val['cTransferItem'] == $v) {
                            return $val;
                        }
                    }));
                }

                if (! empty($building)) {
                    $_buildings = array_values(array_filter($building, function ($val) use ($v) {
                        if ($val['cTransferItem'] == $v) {
                            return $val;
                        }
                    }));
                }

                $item[$v] = [
                    'land'     => $_lands,
                    'building' => $_buildings,
                ];

                $_lands = $_buildings = null;
                unset($_lands, $_buildings);
            }
        }

        return $item;
    }

    private function formatTelephoneNo($tel_area, $tel_main)
    {
        $tel = empty($tel_area) ? '' : $tel_area;
        $tel .= empty($tel) ? '' : '-';
        $tel .= empty($tel_main) ? '' : $tel_main;

        return preg_match("/\-$/", $tel) ? str_replace('-', '', $tel) : $tel;
    }

    private function formatMobile($mobile)
    {
        $mobile = str_replace('-', '', $mobile);
        if (empty($mobile) || ! preg_match("/^09\d{8}$/", $mobile)) {
            return '';
        }

        return substr($mobile, 0, 4) . '-' . substr($mobile, 4);
    }
}
