<?php
namespace Selfiesign;

require_once dirname(dirname(__DIR__)) . '/configs/selfiesignSetting.php';
require_once dirname(__DIR__) . '/traits/Logger.traits.php';
require_once __DIR__ . '/createToken.trait.php';

class Selfiesign
{
    use createToken, Logger;

    /**
     * 取得範本的詳細資訊(JSON格式)
     * param $template_id 範本編號 ex: '20240126_01'
     * param $version     範本版本 ex: '1.0'
     * param $format      回傳格式 ex: 'CreateJobJSON'
     * param $allcontrol  是否回傳所有控制項 ex: 'N'
     *
     * return $response or false
     */
    public function getTemplate($template_id, $version, $format = 'CreateJobJSON', $allcontrol = 'N')
    {
        $endpoint = '/Template/get_template_config';
        $data     = [
            'templateid'  => $template_id,
            'templatever' => $version,
            'format'      => $format,
            'allcontrol'  => $allcontrol,
        ];

        return $this->send($endpoint, $data, 'GET');
    }

    /**
     * 建立簽署文件並填入同意書預設值
     * param $data 簽署文件資料；內容可依照 getTemplate 回傳的資料格式填寫
     */
    public function createJob($data)
    {
        $endpoint = '/Job/create_job';

        return $this->send($endpoint, $data);
    }

    /**
     * 經由文件編號查詢簽署文件GET
     * param $job_id 簽署文件編號 ex: 'S1392943e480f0e'
     */
    public function queryJob($job_id)
    {
        $endpoint = '/Job/query_job';
        $data     = [
            'jobid'              => $job_id,
            'querycontrolvalues' => 'Y',
        ];

        $response = $this->send($endpoint, $data, 'GET');
        if (empty($response) || empty($response['jobs'])) {
            return false;
        }

        $signer_status = [];
        $signer_status = array_map(function ($signer) {
            return [
                'sequnce' => $signer['SIGN_SEQ'],
                'signer'  => $signer['SIGNER_ID'],
                'name'    => $signer['SIGNER_NAME'],
                'status'  => $signer['SIGN_STATUS'],
            ];
        }, $response['jobs'][0]['jobsignhists']);

        return [
            'job'    => $response['jobs'][0]['jobinfo']['JOB_STATUS'],
            'sign'   => $signer_status,
            'origin' => $response,
        ];
    }

    /**
     * 經由日期區間查詢簽署文件
     * param $start_date 開始日期 ex: '2024-01-01'
     * param $end_date   結束日期 ex: '2024-02-28'
     */
    public function queryJobByDate($start_date, $end_date)
    {
        $endpoint = '/Job/query_job';
        $data     = [
            'datefrom' => $start_date,
            'dateto'   => $end_date,
        ];

        return $this->send($endpoint, $data, 'GET');
    }

    /**
     * 使用更多條件查詢簽署文件
     * param $start_date    開始日期 ex: '2024-01-01'
     * param $end_date      結束日期 ex: '2024-02-28'
     * param $template_name 範本名稱 ex: '動撥買賣價金協議書'
     * param $signer_id     簽署人ID(身分證字號) ex: 'R123521392'
     * param $signer_name   簽署人姓名 ex: '王小明'
     */
    public function queryJobByCondition($start_date, $end_date, $template_name = null, $signer_id = null, $signer_name = null)
    {
        $endpoint = '/Job/query_job';
        $data     = [
            'datefrom'           => $start_date,
            'dateto'             => $end_date,
            'querycontrolvalues' => 'Y',
        ];

        if (!empty($template_name)) {
            $data['templatename'] = $template_name;
        }

        if (!empty($signer_id)) {
            $data['signerid'] = $signer_id;
        }

        if (!empty($signer_name)) {
            $data['signername'] = $signer_name;
        }

        return $this->send($endpoint, $data);
    }

    /**
     * 取得簽署網頁網址
     * param $job_id   簽署文件編號 ex: 'S1392943e480f0e'
     * param $password 開啟簽署文件連結密碼 ex: '130123086'
     */
    public function getSignUrl($job_id, $password = null)
    {
        $endpoint = '/Job/get_signdoc_url';

        $redirect_url = preg_match("/local/iu", $_SERVER['HTTP_HOST']) ? 'http://' : 'https://';
        $redirect_url .= $_SERVER['HTTP_HOST'] . '/demo/signNextUrl.php?job_id=' . $job_id;
        $redirect_url .= empty($password) ? '' : '&code=' . $password;

        $data = [
            'jobid'     => $job_id,
            'expire'    => 604800, //60秒 * 60分鐘 * 24小時 * 7天,
            'shorturl'  => 'Y',
            'customurl' => $redirect_url,
        ];

        if (!empty($password)) {
            $data['protect'] = $password;
        }

        return $this->send($endpoint, $data, 'GET');
    }

    /**
     * 查詢單筆文件簽署進度
     * param $job_id 簽署文件編號 ex: 'S1392943e480f0e'
     */
    public function querySignStatus($job_id)
    {
        $endpoint = '/Job/query_job_signstatus';

        if (is_array($job_id)) {
            $job_id = implode(',', $job_id);
        }

        $data = [
            'jobids' => $job_id,
        ];

        return $this->send($endpoint, $data, 'GET');
    }

    /**
     * 取得 PDF 文件的下載連結
     * param $doc_id 文件編號 ex: 'TWHG-TWHG-20240129-S1392943e480f0e'
     */
    public function getPDFLink($doc_id)
    {
        $endpoint = '/Sign/get_download_url';

        $data = [
            'docid' => $doc_id,
        ];

        return $this->send($endpoint, $data, 'GET');
    }

    /**
     * 下載 PDF 文件
     * param $doc_id 文件編號 ex: 'TWHG-TWHG-20240129-S1392943e480f0e'
     */
    public function downloadPDF($doc_id)
    {
        $endpoint = '/Sign/getPDFFile';

        $data = [
            'docid'      => $doc_id,
            'isdownload' => 'Y',
        ];

        return $this->send($endpoint, $data, 'GET');
    }

    /**
     * 下載 CSV 文件
     * param $job_id 簽署文件編號 ex: 'S1392943e480f0e'
     */
    public function downloadCSV($job_id)
    {
        $endpoint = '/Report/downloadCSV';

        $data = [
            'jobid' => $job_id,
            'title' => 'N',
        ];

        return $this->send($endpoint, $data, 'GET');

    }

    /**
     * 刪除文件
     * param $job_id 簽署文件編號 ex: 'S1392943e480f0e'
     */
    public function deleteJob($job_id)
    {
        $endpoint = '/Job/delete_job';

        $data = [
            'jobid' => $job_id,
        ];

        return $this->send($endpoint, $data, 'GET');
    }

    /**
     * curl api 發送請求
     * param $endpoint api路徑 ex: '/Template/get_template_config'
     * param $data     api參數 ex: ['templateid' => '20240126_01']
     * param $method   api方法 ex: 'POST' or 'GET'
     *
     * return $response or false
     */
    public function send($endpoint, $data, $method = 'POST')
    {
        $url = URL . $endpoint;

        $method  = strtoupper($method);
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->getToken(),
        ];

        $ch = curl_init();

        if ($method == 'POST') {
            $body = json_encode($data);
        } else if ($method == 'GET') {
            $url .= '?' . http_build_query($data);
            $body = '';
        }

        $params = [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ];

        curl_setopt_array($ch, $params);

        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }

        $result = curl_exec($ch);
        if ($errno = curl_errno($ch)) {
            $error_message = curl_strerror($errno);

            $content = 'End-point: ' . $url . PHP_EOL . 'Request: ' . $body . PHP_EOL . 'Error message: ' . $error_message . PHP_EOL . PHP_EOL;
            Logger::log($content, dirname(dirname(__DIR__)) . '/log/selfiesign_error_' . date('Ymd') . '.log');

            curl_close($ch);

            return false;
        }

        curl_close($ch);

        $content = 'End-point: ' . $url . PHP_EOL . 'Request: ' . $body . PHP_EOL . 'Response: ' . $result . PHP_EOL . PHP_EOL;
        Logger::log($content, dirname(dirname(__DIR__)) . '/log/selfiesign_' . date('Ymd') . '.log');

        $response = json_decode($result, true);
        return empty($response) ? false : $response;
    }
}