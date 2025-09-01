<?php
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

trait RealtyDatalist
{
    public function Realty($sales = null)
    {
        if (empty($sales)) {
            return $this->statusRealty(1);
        }

        if (!is_array($sales)) {
            $sales = [$sales];
            return $this->salesRealty($sales);
        }

        return $this->salesRealty($sales);
    }

    public function statusRealty($status = 1)
    {
        $conn   = new first1DB;
        $stores = [];

        $sql = 'SELECT a.bId, a.bStore, b.bCode, b.bName AS brand FROM tBranch AS a JOIN tBrand AS b ON a.bBrand = b.bId WHERE a.bStatus = :status;';
        $rs  = $conn->all($sql, ['status' => $status]);
        foreach ($rs as $v) {
            $stores[] = [
                'id'   => $v['bCode'] . str_pad($v['bId'], 5, '0', STR_PAD_LEFT),
                'name' => $v['brand'] . $v['bStore'],
            ];
        }

        return $stores;
    }

    public function salesRealty($sales)
    {
        $conn   = new first1DB;
        $stores = [];

        // $sql = 'SELECT a.bId, a.bStore, b.bCode, b.bName AS brand, c.bSales FROM tBranch AS a JOIN tBrand AS b ON a.bBrand = b.bId JOIN tBranchSales AS c ON a.bId = c.bBranch WHERE a.bStatus = 1 AND c.bSales IN (' . implode(',', $sales) . ');';
        $sql = 'SELECT a.bId, a.bStore, b.bCode, b.bName AS brand, c.bSales FROM tBranch AS a JOIN tBrand AS b ON a.bBrand = b.bId JOIN tBranchSalesForPerformance AS c ON a.bId = c.bBranch WHERE a.bStatus = 1 AND c.bSales IN (' . implode(',', $sales) . ');';
        $rs  = $conn->all($sql, ['status' => $status]);
        foreach ($rs as $v) {
            $stores[] = [
                'id'   => $v['bCode'] . str_pad($v['bId'], 5, '0', STR_PAD_LEFT),
                'name' => $v['brand'] . $v['bStore'],
            ];
        }

        return $stores;
    }

    public function allStatusRealty()
    {
        $conn   = new first1DB;
        $stores = [];

        $sql = 'SELECT a.bId, a.bStore, b.bCode, b.bName AS brand FROM tBranch AS a JOIN tBrand AS b ON a.bBrand = b.bId ;';
        $rs  = $conn->all($sql);
        foreach ($rs as $v) {
            $stores[] = [
                'id'   => $v['bCode'] . str_pad($v['bId'], 5, '0', STR_PAD_LEFT),
                'name' => $v['brand'] . $v['bStore'],
            ];
        }

        return $stores;
    }
}
