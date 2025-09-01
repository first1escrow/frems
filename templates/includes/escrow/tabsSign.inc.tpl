<div id="tabs-sign">
    <div>
        <div>請選取文件：</div>
        <div>
            <select id="select-document" class="form-control" onchange="getSignDocument()">
                <option value="">請選擇</option>
                <option value="動撥買賣價金協議書">動撥買賣價金協議書</option>
            </select>
        </div>
    </div>
    <div id="tabs-sign-show-area">

    </div>
</div>

<script>
function getSignDocument() {
    console.log($('select-document').val());
}
</script>

<style>
#tabs-sign-show-area {
    width:99%;
    height:100px;
    border:1px solid #CCC;
    margin-top:20px;
    border-radius:5px;
}
</style>