<?php

trait Department
{
    /**
     * 取得部門資訊
     *
     * @param int or null $staff_id 員工編號
     * @return array 部門資訊
     */
    public function getDepartment($staff_id = null)
    {
        if (empty($this->conn)) {
            throw new Exception('Database connection is missing.');
        }

        if (empty($staff_id)) {
            $sql = 'SELECT dId, dDep, dTitle, dColor FROM tDepartment WHERE 1 = 1;';
            $rs  = $this->conn->all($sql);

            $dept = [];
            foreach ($rs as $row) {
                $dept[$row['dId']] = $row;
            }
            return $dept;
        }

        $sql = 'SELECT a.dId, a.dDep, a.dTitle, a.dColor, b.pName, b.pId FROM tDepartment AS a JOIN tPeopleInfo AS b ON a.dId = b.pDep WHERE b.pId = :staff_id;';
        return [$staff_id => $this->conn->one($sql, ['staff_id' => $staff_id])];
    }
}
