<?php

/**
 * Staffs trait
 */
trait Staffs
{
    /**
     * 取得員工資訊
     *
     * @param int or null $staff_id 員工編號
     * @return array 員工資訊
     */
    public function staffs($staff_id = null)
    {
        if (empty($this->conn)) {
            throw new Exception('Database connection is missing.');
        }

        $sql = 'SELECT
                    a.pId as staffId,
                    a.pName as staffName,
                    a.pDep as staffDepartmentId,
                    a.pOnBoard as staffOnBoard,
                    b.dDep as staffDepartment,
                    b.dTitle as staffTitle,
                    b.dColor as staffColor
                FROM
                    tPeopleInfo AS a
                JOIN
                    tDepartment AS b ON a.pDep = b.dId
                WHERE
                    pJob = 1
                    AND pId NOT IN (2, 6, 8, 66)
        ';
        if (! empty($staff_id) && is_numeric($staff_id)) {
            $sql .= ' AND a.pId = ' . $staff_id . ' ';
        }

        $sql .= ' ORDER BY a.pOnBoard ASC';

        $rs = $this->conn->all($sql);
        if (empty($rs)) {
            return [];
        }

        $staffs = [];
        foreach ($rs as $row) {
            $staffs[$row['staffId']] = $row;
        }

        return $staffs;
    }
}
