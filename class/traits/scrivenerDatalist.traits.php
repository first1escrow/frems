<?php
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

trait ScrivenerDatalist
{
    public function Scrivener($sales = null)
    {
        if (empty($sales)) {
            return $this->statusScrivener(1);
        }

        if (!is_array($sales)) {
            $sales = [$sales];
            return $this->salesScrivener($sales);
        }

        return $this->salesScrivener($sales);
    }

    public function statusScrivener($status = 1)
    {
        $conn   = new first1DB;
        $stores = [];

        $sql = 'SELECT sId, sName, sOffice FROM tScrivener WHERE sStatus = :status;';
        $rs  = $conn->all($sql, ['status' => $status]);
        foreach ($rs as $v) {
            $stores[] = [
                'id'   => 'SC' . str_pad($v['sId'], 4, '0', STR_PAD_LEFT),
                'name' => empty($v['sOffice']) ? $v['sName'] : $v['sOffice'],
            ];
        }

        return $stores;
    }

    public function salesScrivener($sales)
    {
        $conn   = new first1DB;
        $stores = [];

        // $sql = 'SELECT a.sSales, b.sId, b.sName, b.sOffice FROM tScrivenerSales AS a JOIN tScrivener AS b ON a.sScrivener = b.sId WHERE a.sSales IN (' . implode(',', $sales) . ') AND b.sStatus = 1;';
        $sql = 'SELECT a.sSales, b.sId, b.sName, b.sOffice FROM tScrivenerSalesForPerformance AS a JOIN tScrivener AS b ON a.sScrivener = b.sId WHERE a.sSales IN (' . implode(',', $sales) . ') AND b.sStatus = 1;';
        $rs  = $conn->all($sql);
        foreach ($rs as $v) {
            $stores[] = [
                'id'   => 'SC' . str_pad($v['sId'], 4, '0', STR_PAD_LEFT),
                'name' => empty($v['sOffice']) ? $v['sName'] : $v['sOffice'],
            ];
        }

        return $stores;
    }

    public function allStatusScrivener()
    {
        $conn   = new first1DB;
        $stores = [];

        $sql = 'SELECT sId, sName, sOffice FROM tScrivener ;';
        $rs  = $conn->all($sql);
        foreach ($rs as $v) {
            $stores[] = [
                'id'   => 'SC' . str_pad($v['sId'], 4, '0', STR_PAD_LEFT),
                'name' => empty($v['sOffice']) ? $v['sName'] : $v['sOffice'],
            ];
        }

        return $stores;
    }
}
