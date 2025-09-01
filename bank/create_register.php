<?php
require_once dirname(__DIR__) . '/class/traits/pusher.trait.php';
require_once dirname(__DIR__) . '/tracelog.php';

class CreateBankRegister
{
    use Pusher;

    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * 格式化地政士ID為SC+4碼數字
     * @param int $scrivenerId 地政士ID
     * @return string 格式化後的地政士ID
     */
    private function getFormattedScrivenerId($scrivenerId)
    {
        return 'SC' . str_pad($scrivenerId, 4, '0', STR_PAD_LEFT);
    }

    /**
     * 轉換品牌代碼為顯示文字
     * @param int $brand 品牌代碼
     * @param int $category 類別代碼
     * @return string 品牌顯示文字
     */
    private function getBrandText($brand, $category)
    {
        if ($brand == 1 && $category == 1) {
            return '台屋加盟';
        } elseif ($brand == 1 && $category == 2) {
            return '台屋直營';
        } elseif ($brand == 49) {
            return '優美地產';
        } else {
            return '非仲介成交';
        }
    }

    /**
     * 轉換類別代碼為顯示文字
     * @param int $category 類別代碼
     * @return string 類別顯示文字
     */
    private function getCategoryText($category)
    {
        if ($category == 1) {
            return '加盟';
        } elseif ($category == 2) {
            return '直營';
        } else {
            return '非仲介成交';
        }
    }

    /**
     * 轉換版本代碼為顯示文字
     * @param int $type 版本代碼
     * @return string 版本顯示文字
     */
    private function getTypeText($type)
    {
        if ($type == 1) {
            return '土地';
        } elseif ($type == 2) {
            return '建物';
        } elseif ($type == 3) {
            return '預售屋';
        } else {
            return '未知';
        }
    }

    /**
     * 根據人員ID獲取人員姓名
     * @param int $pId 人員ID
     * @return string 人員姓名，找不到則返回空字串
     */
    private function getPersonName($pId)
    {
        if (empty($pId)) {
            return '';
        }

        $sql = 'SELECT pName FROM tPeopleInfo WHERE pId = "' . $pId . '"';
        $rs  = $this->conn->Execute($sql);

        if (! $rs->EOF && ! empty($rs->fields['pName'])) {
            return $rs->fields['pName'];
        }

        return '';
    }

    /**
     * 申請保證號碼
     * @param string $aId 申請ID
     * @param string $formNo2 表單編號
     * @param string $man 地政士ID
     * @param string $bBrand 品牌
     * @param string $bCategory 類別
     * @param string $bVersion 版本
     * @param string $escrowBank 保號銀行
     * @param int $Snum 土地數量
     * @param int $Lnum 建物數量
     * @param int $Bnum 農地數量
     */
    public function applyBabkCode($aId, $formNo2, $scrivenerId, $brand, $category, $type, $escrowBank, $num, $member_id)
    {
        $sql    = 'UPDATE tApplyBankCode SET aFormNo2 = ' . $formNo2 . ' WHERE aId = ' . $aId . ';';
        $result = $this->conn->Execute($sql);

        // 格式化地政士ID為SC+4碼數字
        $formattedScrivenerId = $this->getFormattedScrivenerId($scrivenerId);

        // 轉換品牌、類別、版本為顯示文字
        $brandText    = $this->getBrandText($brand, $category);
        $categoryText = $this->getCategoryText($category);
        $typeText     = $this->getTypeText($type);

        // 記錄新增操作到 tracelog
        $tracelog = new TraceLog();
        $title    = "申請保證號碼 - 地政士ID: {$formattedScrivenerId}, 品牌: {$brandText}, 類別: {$categoryText}, 版本: {$typeText}, 銀行: {$escrowBank}, 數量: {$num}";
        $tracelog->insertWrite($member_id, $sql, $title);

        return $result;
    }

    /**
     * 獲取符合條件的通知對象列表
     * @return array 符合條件的pId列表
     */
    private function getNotificationTargets()
    {
        $sql = 'SELECT pId FROM tPeopleInfo WHERE pDep = "11" AND pJob = "1"';
        $rs  = $this->conn->Execute($sql);

        $targetIds = [];
        while (! $rs->EOF) {
            $targetIds[] = $rs->fields['pId'];
            $rs->MoveNext();
        }

        return $targetIds;
    }

    /**
     * 發送申請保證號碼的彙總通知
     * @param string $scrivenerId 地政士ID
     * @param string $brand 品牌
     * @param string $category 類別
     * @param string $escrowBank 保號銀行
     * @param array $applications 申請明細 [['type' => '1', 'num' => 1], ['type' => '2', 'num' => 1]]
     * @param int $member_id 會員ID
     */
    public function sendApplySummaryNotification($scrivenerId, $brand, $category, $escrowBank, $applications, $member_id)
    {
        // 格式化地政士ID為SC+4碼數字
        $formattedScrivenerId = $this->getFormattedScrivenerId($scrivenerId);

        // 轉換品牌、類別為顯示文字
        $brandText    = $this->getBrandText($brand, $category);
        $categoryText = $this->getCategoryText($category);

        // 獲取申請人姓名
        $applicantName = $this->getPersonName($member_id);

        // 建立申請明細摘要
        $summaryItems = [];
        $totalCount   = 0;

        foreach ($applications as $app) {
            $typeText = $this->getTypeText($app['type']);

            if ($app['num'] > 0) {
                $summaryItems[] = "{$typeText}: {$app['num']}筆";
                $totalCount += $app['num'];
            }
        }

        // 建立通知訊息
        $summaryText   = implode(', ', $summaryItems);
        $applicantLine = ! empty($applicantName) ? "申請人: {$applicantName}\n" : '';
        $message       = "保證號碼申請完成！\n\n{$applicantLine}地政士ID: {$formattedScrivenerId}\n品牌: {$brandText}\n類別: {$categoryText}\n銀行: {$escrowBank}\n申請明細: {$summaryText}\n總計: {$totalCount}筆";

        // 獲取符合條件的通知對象
        $targetIds = $this->getNotificationTargets();

        // 依序發送通知給符合條件的使用者
        foreach ($targetIds as $targetId) {
            $channel = "first1-notify-{$targetId}";
            $event   = "first1-notify";

            $this->trigger($channel, $event, $message);
        }
    }

}
