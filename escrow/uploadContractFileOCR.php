<?php
header('Content-Type: application/json');

require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/class/traits/Tokens.traits.php';
// require_once dirname(__DIR__) . '/class/traits/OcrParser.traits.php';
// require_once dirname(__DIR__) . '/tracelog.php';

//upload_max_filesiz = 10M

// $tlog = new TraceLog();
// $tlog->insertWrite($_SESSION['member_id'], json_encode($_GET), '謄本檔案上傳');
// $tlog->insertWrite($_SESSION['member_id'], json_encode($_FILES), '謄本檔案上傳');

use Tokens;

//回應
function response($status, $message, $data = null)
{
    http_response_code($status);

    $response = [
        'status'   => $status,
        'response' => $message,
    ];

    if (!empty($data)) {
        $response['data'] = $data;
    }

    exit(json_encode($response, JSON_UNESCAPED_UNICODE));
}
##

$id = $_POST['cId'];
if (!preg_match("/^\d{9}$/", $id)) {
    response(400, 'Empty CertifiedId!!');
}

if (!empty($_FILES)) {
    $uploadData = array();

    $saveUrl = dirname(__DIR__) . "/public/ocrFile/" . $id;
    checkFile($_FILES);

    foreach ($_FILES as $k => $v) {
        //沒有資料夾就建立資料夾
        if (!is_dir($saveUrl)) {
            mkdir($saveUrl . "/", 0777, true);
        }
        ##

        $fileName   = 'OCR' . $id . '_' . date('YmdHis') . "." . pathinfo($v['name'], PATHINFO_EXTENSION);
        $uploadfile = $saveUrl . '/' . $fileName;

        if (move_uploaded_file($v['tmp_name'], $uploadfile)) {
            //20240418 檔案回寫至 www.first1.com.tw
            $response = uploadOCRfile($id, $uploadfile);

            if (!empty($response)) {
                if ($response['status'] == 200) {
                    response(200, 'OK', $response['data']);
                }

                response($response['status'], $response['response']);
            }

            response(400, '其他失敗');
        }

        response(403, print_r($v, true));
    }
}

function checkFile($fileData)
{
    $file_ext = array('pdf');

    foreach ($fileData as $k => $v) {
        $extension = strtolower(pathinfo($v['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $file_ext)) {
            exit("不符合可以上傳的檔案類型");
        }
    }
}

function uploadOCRfile($cId, $file)
{
    $url = 'https://www.first1.com.tw/api/uploadOCRfile.php';

    $dir = dirname(__DIR__) . '/log/uploadOCRfile';
    if (!is_dir($dir)) {
        mkdir($dir . "/", 0777, true);
    }

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => '',
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => 'POST',
        CURLOPT_POSTFIELDS     => array('file' => new CURLFILE($file), 'cId' => $cId, 'token' => Tokens::timeGapTokenGenerate($cId)),
    ));

    $response = curl_exec($curl);
    file_put_contents($dir . '/response_' . date("Ymd") . '.log', date('Y-m-d H:i:s ') . print_r($response, true) . PHP_EOL, FILE_APPEND);
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($curl);
        file_put_contents($dir . '/error_msg.log', print_r($error_msg, true) . PHP_EOL, FILE_APPEND);

        return false;
    }

    curl_close($curl);
    return json_decode($response, true);
}
