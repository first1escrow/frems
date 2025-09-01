<?php
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

trait SalesDatalist
{
    public function Sales($dep = [7], $job = 1)
    {
        $conn  = new first1DB;
        $sales = [];

        $sql = 'SELECT a.pId, a.pName FROM tPeopleInfo AS a WHERE a.pDep IN (' . implode(',', $dep) . ') AND pJob = ' . $job . ';';
        $rs  = $conn->all($sql);
        foreach ($rs as $v) {
            $sales[$v['pId']] = [
                'id'   => $v['pId'],
                'name' => $v['pName'],
            ];
        }

        return $sales;
    }
}
