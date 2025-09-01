<div id="tabs-ocr">
    <div><font color="red">※限PDF檔案</font></div>
    <div class="upload_area" ondragover="uploadFileDragover(event)" ondrop="uploadFileDropOCR(event)" onclick="fileOcr.click()">
        <div><img src="../images/baseline_upload_file_black_24dp.png" alt="上傳"></div>
        <div style="font-size: 20px;line-height: 30px;">將檔案放至這裡上傳 (或點選)<input type='file' name="fileOcr" id="fileOcr" multiple="multiple" accept=".pdf" style="display: none;" onchange="uploadFileClickOCR()"> </div>
        <div class='uploadProgressOCR' style="display: none">
            <div class='uploadBarOCR' id='uploadBarOCR'></div>
            <div class='uploadPercentOCR' id='uploadPercentOCR'>0%</div>
        </div>
    </div>
</div>

<script>
function uploadFileDropOCR(e) {
    e.preventDefault();
    let files = e.dataTransfer.files;
    uploadFileOCR(files);
}

function uploadFileDragover(e) {
    e.preventDefault();

    // const fileName = e.dataTransfer.files[0].name;
    // console.log(fileName);
}

function uploadFileClickOCR() {
    var files = $("input[name=fileOcr]")[0].files;
    uploadFileOCR(files);
}

function uploadFileOCR(files) {
    $("#uploadBarOCR").width("0%");//歸0
    $(".uploadProgressOCR").show();

    let formData = new FormData();
    let re = /\.(pdf)$/i;
    let check = 0;
    for (let i = 0; i < files.length; i++) {
        if (!re.test(files[i]['name'])) {
            check++;
            alert('不符合可以上傳的檔案');
            return false;
        }

        formData.append("file" + i, files[i]);
    }

    formData.append('cId', $('[name=certifiedid]').val());
    $.ajax({
        type: "POST",
        url: "uploadContractFileOCR.php",
        data:formData,
        cache:false,
        processData: false,
        contentType: false,
        dataType: 'json',   // 回傳的資料格式
        success: function (msg){
            console.log(msg);
            $("#uploadPercentOCR").text('完成');
            $("#uploadBarOCR").width("100%");    // 進度條顏色

        },
        xhr:function(){
            var xhr = new window.XMLHttpRequest(); // 建立xhr(XMLHttpRequest)物件
            xhr.upload.addEventListener("uploadProgress", function(progressEvent){ // ProgressEvent
            console.log(progressEvent)
                if (progressEvent.lengthComputable) {
                var percentComplete = progressEvent.loaded / progressEvent.total;
                var percentVal = Math.round((percentComplete*100)/2) + "%";
                $("#uploadPercentOCR").text(percentVal); // 進度條百分比文字
                $("#uploadBarOCR").width(percentVal);    // 進度條顏色
                }
            }, false);
            return xhr; // 注意必須將xhr(XMLHttpRequest)物件回傳
        }
    }).fail(function(xhr, status, error) {
        $("#uploadPercentOCR").text("0%"); // 錯誤發生進度歸0%
        $("#uploadBarOCR").width("0%");
        alert(xhr.responseText);
    });
}

</script>