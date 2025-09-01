<?php
namespace First1\V1\SMS;

require_once dirname(dirname(__DIR__)) . '/.env.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/class/traits/slackIncomingWebhook.trait.php';
require_once __DIR__ . '/traits/FetGateway.trait.php';
require_once __DIR__ . '/traits/SmsTarget.trait.php';
require_once __DIR__ . '/traits/ContentData.trait.php';

use \Exception;
use \First1DB;

class SMS
{
    use \FetGateway, \SmsTarget, \ContentData, \SlackIncomingWebhook;

    public $conn;
    public $log_path;
    public $from_addr = '0936019428'; //發話方號碼

    public function __construct(first1DB $conn, $log_path = '')
    {
        global $env;

        $this->conn     = $conn;
        $this->log_path = empty($log_path) ? dirname(dirname(__DIR__)) . '/log/sms' : $log_path;

        if (! is_dir($this->log_path)) {
            mkdir($this->log_path, 0777, true);
        }

        return $this;
    }

    /**
     * 發送簡訊
     * @param  string $eDepAccount 虛擬帳號
     * @param  string $sid    地政士編號
     * @param  string $bid    仲介店編
     * @param  string $target 入款類別(income, income2)
     */

    //入帳簡訊(保證號碼[16],地政士,仲介店,簡訊樣式,tExpense.id,寄送對象)
    public function incomeSMS($eDepAccount, $sId, $bId, $target, $expenseId)
    {
        if (empty($eDepAccount) || ! is_numeric($eDepAccount)) {
            throw new Exception('eDepAccount is invalid.');
        }

        $cId = strlen($eDepAccount) > 9 ? substr($eDepAccount, -9) : $eDepAccount;
        if (empty($cId)) {
            throw new Exception('CertifiedId is invalid.');
        }

        if (! in_array($target, ['income', 'income2'])) {
            throw new Exception('Target is invalid.');
        }

        if (empty($expenseId) || ! is_numeric($expenseId)) {
            throw new Exception('ExpenseId is invalid.');
        }

        //蒐集地政士電話號碼
        $scrivener_mobiles = $this->getsScrivenerMobile($cId, $sId);

        //蒐集仲介店電話號碼
        $branch_mobiles = $this->getBranchMobile($cId, ['店東', '店長', '經紀人']);

        //蒐集買賣方電話號碼
        $users = $this->getBuyerOwnerMobile($cId);

        //蒐集買賣方代理人電話號碼
        $agents = $this->getBuyerOwnerAgentMobile($cId);

        //蒐集買賣方經紀人電話號碼
        $brokers = $this->getBuyerOwnerBrokerMobile($cId);

        //取得發送文案內容
        $contents = $this->getContentData($target, $expenseId, $users);

        // 取得案件簡訊發送對象
        $targets = $this->getSendList($scrivener_mobiles, $branch_mobiles, $users, $agents, $brokers);

        $smsList = $this->combileIncomeSMS($contents, $targets);

        return $smsList;
    }

    /**
     * 整合入款簡訊
     * @param  array $contents 簡訊內容
     * @param  array $targets  發送對象
     */
    private function combileIncomeSMS($contents, $targets)
    {
        $smsList = [];

        $smsList['owner']     = ['target' => [], 'content' => []];
        $smsList['ownerBoss'] = ['target' => [], 'content' => []];
        $smsList['buyer']     = ['target' => [], 'content' => []];
        $smsList['buyerBoss'] = ['target' => [], 'content' => []];
        $smsList['scrivener'] = ['target' => [], 'content' => []];

        //賣方
        if (! empty($targets['owner'])) {
            foreach ($targets['owner'] as $v) {
                $data = [
                    'title'  => $v['title'],
                    'name'   => $v['name'],
                    'mobile' => $v['mobile'],
                    'serial' => $v['mobile'] . '_' . uniqid(),
                ];

                if (! empty($v['boss']) && ($v['boss'] == 1)) {
                    $content = $contents['owner'][0];

                    $smsList['ownerBoss']['target'][] = $data;

                    //主管簡訊加註地址
                    $content .= ';' . $contents['address'];

                    //店家簡訊備註文字
                    if (! empty($v['smsText'])) {
                        $content .= '(' . $v['smsText'] . ')';
                    }

                    $smsList['ownerBoss']['content'] = $content;
                } else {
                    $smsList['owner']['target'][] = $data;
                    $smsList['owner']['content']  = $contents['owner'][0];
                }
            }
        }

        //買方
        if (! empty($targets['buyer'])) {
            foreach ($targets['buyer'] as $v) {
                $data = [
                    'title'  => $v['title'],
                    'name'   => $v['name'],
                    'mobile' => $v['mobile'],
                    'serial' => $v['mobile'] . '_' . uniqid(),
                ];

                if (! empty($v['boss']) && ($v['boss'] == 1)) {
                    $content = $contents['buyer'][0];

                    $smsList['buyerBoss']['target'][] = $data;

                    //主管簡訊加註地址
                    $content .= ';' . $contents['address'];

                    //店家簡訊備註文字
                    if (! empty($v['smsText'])) {
                        $content .= '(' . $v['smsText'] . ')';
                    }

                    $smsList['buyerBoss']['content'] = $content;
                } else {
                    $smsList['buyer']['target'][] = $data;
                    $smsList['buyer']['content']  = $contents['buyer'][0];
                }
            }
        }

        // 地政士
        $smsList['scrivener']['target']  = $targets['scrivener'];
        $smsList['scrivener']['content'] = $contents['scrivener'];

        return $smsList;
    }

    /**
     * 整合取得發送對象
     * @param  array $scriveners 地政士
     * @param  array $branches   仲介店
     * @param  array $users      買賣方(包含主買賣方與其他買賣方)
     * @param  array $agents     買賣方代理人
     * @param  array $brokers    買賣方經紀人
     * @return array
     */
    private function getSendList($scriveners, $branches, $users, $agents, $brokers)
    {
        $smsList = ['buyer' => [], 'owner' => [], 'scrivener' => []];

        //地政士
        if (! empty($scriveners)) {
            foreach ($scriveners as $v) {
                $data = [
                    'title'  => empty($v['mTitle']) ? '地政士' : $v['mTitle'],
                    'name'   => $v['mName'],
                    'mobile' => $v['mMobile'],
                    'serial' => $v['mobile'] . '_' . uniqid(),
                ];

                $smsList['scrivener'][] = $data;
            }
        }

        //仲介店
        if (! empty($branches)) {
            if (! empty($branches['owner'])) {
                foreach ($branches['owner'] as $branch) {
                    foreach ($branch['smsTarget'] as $v) {
                        $data = [
                            'title'        => empty($v['tTitle']) ? '賣方仲介店' : $v['tTitle'],
                            'name'         => $v['mName'],
                            'mobile'       => $v['mMobile'],
                            'boss'         => empty($v['boss']) ? '' : $v['boss'],
                            'smsText'      => $v['smsText'],
                            'smsTextStyle' => $v['smsTextStyle'],
                            'serial'       => $v['mobile'] . '_' . uniqid(),
                        ];

                        $smsList['owner'][] = $data;
                    }
                }
            }

            if (! empty($branches['buyer'])) {
                foreach ($branches['buyer'] as $branch) {
                    foreach ($branch['smsTarget'] as $v) {
                        $data = [
                            'title'        => empty($v['tTitle']) ? '買方仲介店' : $v['tTitle'],
                            'name'         => $v['mName'],
                            'mobile'       => $v['mMobile'],
                            'serial'       => $v['mobile'] . '_' . uniqid(),
                            'boss'         => empty($v['boss']) ? '' : $v['boss'],
                            'smsText'      => $v['smsText'],
                            'smsTextStyle' => $v['smsTextStyle'],
                        ];

                        $smsList['buyer'][] = $data;
                    }
                }
            }

        }

        //買賣方
        if (! empty($users)) {
            if (! empty($users['owner'])) {
                foreach ($users['owner']['data'] as $v) {
                    $data = [
                        'title'  => empty($v['tTitle']) ? '賣方' : $v['tTitle'],
                        'name'   => $v['mName'],
                        'mobile' => $v['mMobile'],
                        'serial' => $v['mobile'] . '_' . uniqid(),
                    ];

                    $smsList['owner'][] = $data;
                }
            }

            if (! empty($users['buyer'])) {
                foreach ($users['buyer']['data'] as $v) {
                    $data = [
                        'title'  => empty($v['tTitle']) ? '買方' : $v['tTitle'],
                        'name'   => $v['mName'],
                        'mobile' => $v['mMobile'],
                        'serial' => $v['mobile'] . '_' . uniqid(),
                    ];

                    $smsList['buyer'][] = $data;
                }
            }
        }

        //買賣方代理人
        if (! empty($agents)) {
            if (! empty($agents['owner'])) {
                foreach ($agents['owner'] as $v) {
                    $data = [
                        'title'  => empty($v['tTitle']) ? '賣方代理人' : $v['tTitle'],
                        'name'   => $v['mName'],
                        'mobile' => $v['mMobile'],
                        'serial' => $v['mobile'] . '_' . uniqid(),
                    ];

                    $smsList['owner'][] = $data;
                }
            }

            if (! empty($agents['buyer'])) {
                foreach ($agents['buyer'] as $v) {
                    $data = [
                        'title'  => empty($v['tTitle']) ? '買方代理人' : $v['tTitle'],
                        'name'   => $v['mName'],
                        'mobile' => $v['mMobile'],
                        'serial' => $v['mobile'] . '_' . uniqid(),
                    ];

                    $smsList['buyer'][] = $data;
                }
            }
        }

        //買賣方經紀人
        if (! empty($brokers)) {
            if (! empty($brokers['owner'])) {
                foreach ($brokers['owner'] as $v) {
                    $data = [
                        'title'  => empty($v['tTitle']) ? '賣方經紀人' : $v['tTitle'],
                        'name'   => $v['mName'],
                        'mobile' => $v['mMobile'],
                        'serial' => $v['mobile'] . '_' . uniqid(),
                    ];

                    $smsList['owner'][] = $data;
                }
            }

            if (! empty($brokers['buyer'])) {
                foreach ($brokers['buyer'] as $v) {
                    $data = [
                        'title'  => empty($v['tTitle']) ? '買方經紀人' : $v['tTitle'],
                        'name'   => $v['mName'],
                        'mobile' => $v['mMobile'],
                        'serial' => $v['mobile'] . '_' . uniqid(),
                    ];

                    $smsList['buyer'][] = $data;
                }
            }
        }

        return $smsList;
    }

    /**
     * 發送簡訊
     * @param  array $data 發送資料
     * @return string     發送結果
     */
    public function send($data)
    {
        global $env;

        // $data['mobile'] = '0922785490'; //測試用

        if (empty($data['cId']) || ! preg_match("/^[0-9]{9}$/", $data['cId'])) {
            throw new Exception('CertifiedId is invalid.');
        }

        if (empty($data['target']) || ! in_array($data['target'], ['income', 'income2'])) {
            throw new Exception('Target is invalid.');
        }

        if (empty($data['mobile']) || ! preg_match('/^09[0-9]{8}$/', $data['mobile'])) {
            throw new Exception('Mobile is invalid.');
        }

        if (empty($data['content'])) {
            throw new Exception('content is invalid.');
        }

        //記錄一筆待發送簡訊
        $insert_id = $this->insertToSMSCheck($this->conn, $this->from_addr, $data['mobile']);
        $response  = $this->fetSend($env['sms']['fet'], $data['mobile'], $data['content'], $insert_id);
        if (empty($response)) {
            $this->updateSMSCheckStatus($this->conn, $insert_id, 'c');
            throw new Exception('簡訊發送錯誤(Curl error)!! Mobile: ' . $data['mobile'] . ', tSMS_Check insert_id: ' . $insert_id);
        }

        //測試用
        // $response = $this->fetSend($env['sms']['fet'], '0922785490', 'Test SMS');
        // echo '<pre>';
        // print_r($response);exit;
        /* $response = '<?xml version="1.0" encoding="UTF-8"?>
<SubmitRes><ResultCode>00000</ResultCode><ResultText>Request successfully processed.</ResultText><MessageId>11021589614265</MessageId></SubmitRes>'; */

        $parseData = $this->parseOutput($response);

        $parseData['messageId'] = empty($parseData['messageId']) ? 'Fake_' . uniqid() : $parseData['messageId'];
        $messageId              = $parseData['messageId'];

        $this->updateToSMSCheck($this->conn, $insert_id, $parseData);
        $this->insertSMSLog($this->conn, $data['target'], $data['cId'], $data['expenseId'], $data['content'], $data['mobile'], $data['name'], $messageId);

        return $response;
    }
}
