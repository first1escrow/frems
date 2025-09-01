<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=9"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://code.jquery.com/jquery-3.6.2.min.js" integrity="sha256-2krYZKh//PcchRtd+H+VyyQoZ/e3EcrkxhM8ycwASPA=" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
	<script type="text/javascript">
    $(document).ready(function() {
        // 設定預設日期為今天
        let today = new Date().toISOString().split('T')[0];
        $('#switch_date_input').val(today);
    });
	</script>
	<style>
    .tbl tr:nth-child(odd) {
        background-color: #f8f9fa;
    }

    .tbl tr:nth-child(even) {
        background-color: #ffffff;
    }

    .tbl th {
        background-color: #e9ecef;
        font-weight: 600;
    }

    .tbl tr:hover {
        background-color: #e3f2fd;
    }

    html {
        font-size: 16px;
    }

    /* Enhanced date input styles */
    input[type="date"] {
        background: white;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    input[type="date"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
        padding: 2px;
        border-radius: 3px;
        transition: background-color 0.3s ease;
    }

    input[type="date"]::-webkit-calendar-picker-indicator:hover {
        background-color: rgba(59, 130, 246, 0.1);
    }

    /* Modern checkbox styles */
    input[type="checkbox"] {
        appearance: none;
        width: 18px;
        height: 18px;
        border: 2px solid #d1d5db;
        border-radius: 4px;
        background: white;
        cursor: pointer;
        position: relative;
        transition: all 0.3s ease;
    }

    input[type="checkbox"]:checked {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        border-color: #3b82f6;
    }

    input[type="checkbox"]:checked::after {
        content: '✓';
        color: white;
        font-size: 12px;
        font-weight: bold;
        position: absolute;
        top: -1px;
        left: 2px;
    }

    /* Modern radio button styles */
    input[type="radio"] {
        appearance: none;
        width: 18px;
        height: 18px;
        border: 2px solid #d1d5db;
        border-radius: 50%;
        background: white;
        cursor: pointer;
        position: relative;
        transition: all 0.3s ease;
    }

    input[type="radio"]:checked {
        border-color: #3b82f6;
        background: #3b82f6;
    }

    input[type="radio"]:checked::after {
        content: '';
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: white;
        position: absolute;
        top: 3px;
        left: 3px;
    }
	</style>
</head>
<body>
    <div class="w-11/12">
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white p-4 rounded-lg shadow-lg mb-4">
            <h2 class="text-xl font-bold flex items-center">
                <{if $target == 'S'}>
                🏢 地政士區域調整
                <{else}>
                🏘️ 仲介區域調整
                <{/if}>
            </h2>
            <p class="text-blue-100 text-sm mt-1">設定業務負責區域及切換時間</p>
        </div>
    </div>
    <div id="default-sales" class="p-4 border-2 border-gray-200 rounded-lg w-11/12 hidden bg-gradient-to-r from-gray-50 to-blue-50" >
        <div class="text-lg font-semibold mb-3 text-gray-700">👥 選擇業務：</div>
        <div class="grid grid-cols-4 gap-3 mb-4">
            <{foreach from=$sales key=index item=val}>
            <label class="flex items-center p-2 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow cursor-pointer border border-gray-200 hover:border-blue-300">
                <input type="radio" name="sales" value="<{$val.pId}>" class="mr-3">
                <span class="text-sm font-medium text-gray-700"><{$val.pName}></span>
            </label>
            <{/foreach}>
        </div>

        <div class="mb-4 p-3 bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="text-sm font-medium mb-2 text-gray-700 flex items-center">
                <span>📅 切換日期：</span>
            </div>
            <div class="flex items-center">
                <input type="date" id="switch_date_input" name="switch_date" 
                       class="px-3 py-2 border border-gray-300 rounded-md focus:border-blue-500 focus:outline-none text-sm">
            </div>
        </div>
        
        <div class="w-auto text-right border-t border-gray-200 pt-4">
            <button class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md shadow-sm transition-colors" onclick="setup()">⚙️ 設定</button>
        </div>
    </div>
    <div>
        <div class="w-auto m-2 p-2">
            <div class="flex items-end gap-4 mb-4">
                <div>
                    <label for="city" class="block text-sm font-medium mb-1">縣市：</label>
                    <select class="w-32 px-3 py-2 border-2 border-gray-300 rounded-md focus:border-blue-500 focus:outline-none" id="city">
                        <option></option>
                        <{foreach from=$city item=val}>
                        <option value="<{$val}>"><{$val}></option>
                        <{/foreach}>
                    </select>
                </div>
                <div>
                    <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md shadow-sm transition-colors" onclick="query()">🔍 查詢</button>
                </div>
            </div>
        </div>
        <div id="content"></div>
    </div>
</body>
</html>
<script>
function query() {
    let city = $('#city').val();
    if (!city) {
        alert('請選擇縣市');
        return;
    }
    
    clearSalesSelect();
    let url = '/includes/sales/getAreaSales.php';
    $.post(url, {city: city}, function(data) {
        let el = '<table class="tbl w-11/12 border-2 border-collapse">';
        el += '<tr>';
        el += '<th class="py-2.5 w-40"><label class=""><input type="checkbox" id="area-all" onclick="selectAll()"><span id="select-all-text" class="ml-1.5">全選</span></label></th>';
        el += '<th class="py-2.5">區域</th>';
        el += '<th class="py-2.5">仲介業務</th>';
        el += '<th class="py-2.5">地政士業務</th>';
        el += '</tr>';
        $.each(data, function(key, val) {
            el += '<tr class="border">';
            el += '<td class="p-1.5 text-center"><input type="checkbox" name="area[]" value="' + val.zZip + '"></td>';
            el += '<td class="p-1.5 text-center"><span class="ml-1.5">' + val.zArea + '</span></td>';
            el += '<td class="p-1.5 text-center">' + val.zPerformanceSalesName + '</td>';
            el += '<td class="p-1.5 text-center">' + val.zPerformanceScrivenerSalesName + '</td>';
            el += '</tr>';
        });
        el += '</table>';
        $('#content').empty().html(el);
        $('#default-sales').show();
    }, 'json').fail(function() {
        alert('查詢失敗');
    });
}

function clearSalesSelect() {
    $('input[name="sales"]').prop('checked', false);
}

function readSelectedArea() {
    let area = [];
    $('input[name="area[]"]:checked').each(function() {
        area.push($(this).val());
    });
    return area;
}

function selectAll() {
    if ($('#area-all').prop('checked')) {
        selectAllText(false);
        $('input[name="area[]"]').prop('checked', true);
    } else {
        selectAllText(true)
        $('input[name="area[]"]').prop('checked', false);
    }
}

function selectAllText(all) {
    if (all) {
        $('#select-all-text').text('全選');
    } else {
        $('#select-all-text').text('取消全選');
    }
}

function setup() {
    if (confirm('確定要變更設定嗎？') == false) {
        return;
    }

    let sales = $('input[name="sales"]:checked').val();
    let area = readSelectedArea();
    let switchDate = $('#switch_date_input').val();

    if (!sales) {
        alert('請選擇業務');
        return;
    }

    if (area.length === 0) {
        alert('請選擇區域');
        return;
    }

    if (!switchDate) {
        alert('請選擇切換日期');
        return;
    }

    // 檢查日期不能早於今天
    let today = new Date().toISOString().split('T')[0];
    if (switchDate < today) {
        alert('切換日期不能早於今天');
        return;
    }

    let postData = {
        target: "<{$target}>", 
        sales: sales, 
        area: area,
        switch_date: switchDate
    };

    let url = '/includes/sales/setupAreaSales.php';
    $.post(url, postData, function(data) {
        let dateObj = new Date(switchDate);
        let todayObj = new Date(today);
        
        if (dateObj.getTime() === todayObj.getTime()) {
            alert('設定成功，立即生效');
        } else {
            alert('設定成功，將於 ' + switchDate + ' 生效');
        }
        location.reload();
    }).fail(function(xhr) {
        console.error('Error:', xhr.responseText);
        alert('設定失敗: ' + xhr.responseText);
    });
}
</script>