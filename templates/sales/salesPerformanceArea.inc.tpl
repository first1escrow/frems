<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=9"/>

	<{include file='meta.inc.tpl'}>


    <link rel="stylesheet" type="text/css" href="/css/cmc_loading.css" />
	<script type="text/javascript">
	$(document).ready(function() {
        $( "#tabs" ).tabs();

        let _datatable_setting = {
            "language": {
                "processing": "",
                "loadingRecords": "載入中...",
                "lengthMenu": "顯示 _MENU_ 項結果",
                "zeroRecords": "沒有符合的結果",
                "info": "顯示第 _START_ 至 _END_ 項結果，共 _TOTAL_ 項",
                "infoEmpty": "顯示第 0 至 0 項結果，共 0 項",
                "infoFiltered": "(從 _MAX_ 項結果中過濾)",
                "infoPostFix": "",
                "search": "搜尋:",
                "paginate": {
                    "first": "第一頁",
                    "previous": "上一頁",
                    "next": "下一頁",
                    "last": "最後一頁"
                },
                "aria": {
                    "sortAscending": ": 升冪排列",
                    "sortDescending": ": 降冪排列"
                }
            }
        };

        $('#example-scrivener').DataTable(_datatable_setting);
        $('#example-realty').DataTable(_datatable_setting);

        $('[name="store_transfer_store"]').on('keyup', function () {
            getStoreSales();
        });

        $('.cmc_overlay').hide();
	});

    function detail(_target, _zip) {
		let _url = 'salesPerformanceAreaDetail.php?zip=' + _zip + '&target=' + _target;

		$.colorbox({
            iframe: true, 
            width: "900px", 
            height: "500px", 
            href: _url, 
            onClosed: function() {
                location.reload();
            }
        }) ;
    }

    function transfer() {
        let _tag_sales = $('[name="store_replace_sales"]').val();
        let _tag_store = $('[name="store_transfer_store"]').val();

        let _match = _tag_store.match(/\((.*)\)/); 
        if (!_match) {
            alert('指定對象錯誤或不完整');
            return ;
        } else {
            _tag_store = _match[1];
        }

        if (!_tag_store.match(/^[A-Z]{2}\d{4,5}$/)) {
            alert('請指定目標店家');
             $('[name="store_transfer_store"]').val('').focus().select();
            return;
        }
        
        if (!_tag_sales || (_tag_sales <= 0)) {
            alert('請指定欲轉移業務');
            $('[name="store_replace_sales"]').focus().select();
            return;
        }

        update(_tag_sales, _tag_store, $('[name="sDate"]').val());
    }

    function update(_sales, _store, _date) {
        let _target = "<{$target}>";
        let _url = '/includes/sales/salesPerformanceStoreUpdate.php';

        $.post(_url, {"sales": _sales, "store": _store, "date": _date}, function(response) {
            if (response == 'OK') {
                alert('已更新');
            } else {
                alert('操作異常!請稍後在試');
            }

            location.replace(location.href);
        });
    }

    function getStoreSales() {
        let _tag_store = $('[name="store_transfer_store"]').val();
        
        let _match = _tag_store.match(/\((.*)\)/); 
        if (!_match) {
            return ;
        } else {
            _tag_store = _match[1];
        }

        if (!_tag_store.match(/^[A-Z]{2}\d{4,5}$/)) {
            return;
        }

        $('#from_sales').empty();

        let _url = '/includes/sales/getStoreSales.php';
        $.post(_url, {'store': _tag_store}, function(response) {
            if (response) {
                $('#from_sales').html(response);
            } else {
                return ;
            }
        });
    }

    function showCity(ide='') {
		let type = $(".city" + ide).attr('id');
		let position = $(".city" + ide).position();  
        let x = (position.left);  
        let y = (position.top)+20;  

      	if (type == 'open') {
      		getCity(ide);

      		$(".city" + ide).attr('id', 'close');
      		$(".city1" + ide).css({
	            'background-color': 'rgba(0, 0, 0, 0.5)',
	            'border': '1px solid #CCC',
	            'padding':'5px',
	            'width':'400px',
	            'float': 'right',
	            'z-index':'1',
	            'position':'absolute',
	            'left':x,
	            'top':y,
	            'display':'block',
	            'height':'auto'
	        });
      	} else {
      		closeCityMenu(ide);
      	}
	}

	function closeCityMenu(ide='') {
		$(".city" + ide).empty().attr('id', 'open');
		$(".city1" + ide).empty().hide();
	}

    function getCity(ide='') {
        $.post("/includes/getCity.php", function(response) {
            if (response.status == 200) {
                let data = response.data;

                let _i = 1;
                let _count = 4;
                let _menu = '';
                data.forEach((_value) => {
                    _menu += '<input type="button" value="' + _value +'" class="btnC' + ide + '" onclick="getDistrict(\'' + _value + '\', \'' + ide +　'\')">&nbsp;&nbsp;';

                    if (_i != 1 && (_i % _count == 0)) {
                        _menu += '<br>';
                    }
                    _i ++;
                });
                $(".city1" + ide).empty().html(_menu);
            } else {
                closeCityMenu(ide);
            }
        }, 'json');
    }

    function getDistrict(_city, ide='') {
        $.post("/includes/getDistrict.php", {"city": _city}, function(response) {
            if (response.status == 200) {
                let data = response.data;

                let	_menu = '<input type="button" value="🔄 重選縣市" class="btnC' + ide + '" onclick="getCity(\'' + ide + '\')">&nbsp;&nbsp;';
	            _menu += '<input type="button" value="✅ 確定" class="btnC' + ide + '" onclick="setZip(\'' + ide + '\')">&nbsp;&nbsp;';
	            _menu += '<input type="button" value="❌ 關閉" class="btnC' + ide + '" onclick="closeCityMenu(\'' + ide + '\')">&nbsp;&nbsp;<br>';
	            _menu += '<span class="btnC' + ide + '"><input type="checkbox" name="all' + ide + '" value="' + _city + '" onclick="clickAll(\'' + ide + '\')" >全部</span>';
	            
                let _i = 2;
                let _count = 4;
                let _total = 0;
                $.each(data, function(_key, _value) {
                    _menu += '<span class="btnC' + ide + '"><input type="checkbox" class="zip' + ide + '" onclick="checkClick(\'' + ide + '\')" id=\"' + ide + _value + '\" title=\"' + _value + '\" value="' + _key + '">' + _value + '</span>';
                    if (_i != 1 && (_i % _count == 0)) {
                        _menu += '<br>';
                    }

                    _i ++;
                    _total ++;
                });

                _menu += '<input type="hidden" name="areaCount' + ide + '" value="' + _total + '">';
                $(".city1" + ide).empty().html(_menu);
            } else {
                closeCityMenu(ide);
            }
        }, 'json');
    }

    function clickAll(ide='') {
		let check = $("[name='all" + ide + "']").attr("checked");

		if (check == 'checked') {
			$(".btnC" + ide + " input").attr('checked', 'checked');
		} else {
			$(".btnC" + ide + " input").removeAttr('checked');
		}
	}

	function checkClick(ide='') {
		let count = 0;

		$("[name='all" + ide + "']").removeAttr('checked');
		$(".zip" + ide).each(function() {
			if ($(this).attr("checked") == 'checked') {
				count++;
			}

			if (count == $("[name='areaCount" + ide + "']").val()) {
				$("[name='all" + ide + "']").attr('checked', 'checked');
			}
		});
	}

    function setZip(ide='') {
		let html = $(".area" + ide).html();
		let txt = '';
        let count = 0;

		if ($("[name='all" + ide + "']").attr("checked") == 'checked') {
			txt += '<span class="btnC' + ide +' showZip' + ide + '" id="' + ide + $("[name='all" + ide + "']").val() + '" name="' + $("[name='all" + ide + "']").val() + '"><span class="showPointer" onClick="delZip(\'' + ide + $("[name='all" + ide + "']").val() + '\')" class="del">X</span>' + $("[name='all" + ide + "']").val() + '</span>';
		} else {
			$("[name='all" + ide + "']").removeAttr('checked');
			$(".zip" + ide).each(function() {
				if ($(this).attr("checked") == 'checked') {
					// txt += '<span class="btnC' + ide + ' showZip' + ide + '" id="' + ide + $(this).attr("id") + '" name="' + ide + $(this).val() + '"><span class="showPointer" onClick="delZip(\'' + $(this).attr("id") + '\')" class="del">X</span>' + $(this).attr("id") + '</span>';
					txt += '<span class="btnC' + ide + ' showZip' + ide + '" id="' + $(this).attr("id") + '" name="' + ide + $(this).val() + '"><span class="showPointer" onClick="delZip(\'' + $(this).attr("id") + '\')" class="del">X</span>' + $(this).attr("title") + '</span>';
					count ++;
				}
			})

			if (count == $("[name='areaCount" + ide + "']").val()) {
				txt = "<span class=\"btnC" + ide + " showZip" + ide + "\" id=\""+ide+$("[name='all" + ide + "']").val()+"\"><span onClick=\"delZip('"+$("[name='all" + ide + "']").val()+"')\" class=\"del\">X</span>"+$("[name='all" + ide + "']").val()+"</span>";
			}
		}

		$(".area" + ide).empty().html(html+txt);
        $(".city1" + ide).empty();
		closeCityMenu(ide);
	}

    function delZip(c) {
		$("#"+c).remove();
	}

	function searchS(){
		let str = new Array();
		let i = 0;
		$(".showZip").each(function() {
			str[i] = $(this).attr('name');
			i++;
		});

		$('#menuBar').hide();
		$('#tabs').hide();
        $('.cmc_overlay').show();

		$.ajax({
			url: 'salesScrivenerAreaPerformanceAjax.php',
			type: 'POST',
			dataType: 'html',
			data: {"area":str},
		}).done(function(msg) {
			$("#store").html(msg);
			$(".store1").show();

            $('#menuBar').show();
            $('#tabs').show();
            $('.cmc_overlay').hide();
		});
	}

	function addSales(ide=''){
        let sales = $('[name="sSales' + ide + '"]').val();
        let check = 0;

        if (sales == 0) {
            alert("請選擇業務");
            return false;
        }

        // 限制只能指定一位業務
        let currentCount = $('[name="SalseID' + ide + '[]"]').length;
        if (currentCount >= 1) {
            alert("只能指定一位業務，請先移除原有業務再新增。");
            return false;
        }

        $('[name="SalseID' + ide + '[]"]').each(function(index, el) {
            if ($(this).val() == sales) {
                check = 1;
            }
        });

        if (check == 1) {
            alert("已重複建立業務");
            return false;
        }

        let html = '<span id="sales' + ide + sales + '" class="setSales' + ide + '"><span>' + $("[name='sSales" + ide + "']").find('option:selected').text() + '</span><a href="javascript:void(0)" class="showPointer" onclick="delSetSales(\'' + ide + sales + '\')">X</a><input type="hidden" name="SalseID' + ide + '[]" value="' + sales + '"></span>&nbsp;&nbsp;';
        $("#salesGroup" + ide).append(html);
	}

    function delSetSales(sales) {
        $("#sales"+sales).remove();
    }

    function checkAllStore(ide='') {
		let check = $("[name='storeAll" + ide + "']").attr("checked");

		if (check == 'checked') {
			$(".ckStore" + ide).attr('checked', 'checked');
		} else {
			$(".ckStore" + ide).removeAttr('checked');
		}
	}
    
	function setSales(ide='') {
		var sales = new Array();
		var act = $("[name='act']:checked").val();
		var date = $("[name='date" + ide + "']").val();
		var str = new Array();
		var i = 0;
		$(".ckStore"+ ide).each(function() {
			if ($(this).attr("checked") == 'checked') {
				str[i] = $(this).val();
				i++;
			}
		});

		var salesCount = 0;
		$("[name='SalseID" + ide + "[]']").each(function() {
			sales[salesCount] = $(this).val();
			salesCount++;
		});

		if (date =='') {
			alert("請選擇時間");
			return false;
		}

		if (salesCount == 0) {
			alert("請選擇業務");
			return false;
		}

		if (i == 0) {
			alert("請選擇店家");
			return false;
		}

        $('#menuBar').hide();
        $('#tabs').hide();
        $('.cmc_overlay').show();

        let _url = 'setScrivenerSalesForPerformance.php';
        if (ide == 'R') {
            _url = 'setBranchSalesForPerformance.php';
        }

		$.ajax({
			url: _url,
			type: 'POST',
			dataType: 'html',
			data: {"sales": sales,"branch":str,"cat":act,"date":date},
		})
		.done(function(msg) {
			alert(msg);

            $('#menuBar').show();
            $('#tabs').show();
            $('.cmc_overlay').hide();
			
            if (ide == 'R') {
                searchR();
            } else {
                searchS();
            }
		});
	}

	function delSales(bId, sales, ide='') {
		let str = new Array();
		str[0] = bId;

		if (confirm("確定是否要刪除?")) {
            $('#menuBar').hide();
            $('#tabs').hide();
            $('.cmc_overlay').show();

            let _url = 'setScrivenerSalesForPerformance.php';
            if (ide == 'R') {
                _url = 'setBranchSalesForPerformance.php';
            }

			$.ajax({
				url: _url,
				type: 'POST',
				dataType: 'html',
				data: {"branch":str,"sales":sales,"cat":'del'},
			})
			.done(function(msg) {
				alert(msg);
                
                $('#menuBar').show();
                $('#tabs').show();
                $('.cmc_overlay').hide();

                if (ide == 'R') {
                    searchR();
                } else {
				    searchS();
                }
			});
		}
	}

	function searchR() {
		let str = new Array();
		let i = 0;
		$(".showZipR").each(function() {
			str[i] = $(this).attr('name').replace('R', '');
			i++;
		});

		$('#menuBar').hide();
		$('#tabs').hide();
        $('.cmc_overlay').show();

		$.ajax({
			url: 'salesBranchAreaPerformanceAjax.php',
			type: 'POST',
			dataType: 'html',
			data: {"area":str},
		}).done(function(msg) {
			$("#storeR").html(msg);
			$(".store1R").show();

            $('#menuBar').show();
            $('#tabs').show();
            $('.cmc_overlay').hide();
		});
	}

    function defaultDistinct(target) {
        let _url = 'salesPerformanceAreaDefault.php?target=' + target;

		$.colorbox({
            iframe: true, 
            width: "900px", 
            height: "650px", 
            href: _url, 
            onClosed: function() {
                location.reload();
            }
        }) ;
    }
	</script>
	<style>
	#example-realty td, #example-scrivener td {
        text-align: center;
    }

    .btnC, .btnCR{
        color: #fff;
        font-family: 'Microsoft JhengHei', Arial, sans-serif;
        font-size: 13px;
        font-weight: 500;
        line-height: 16px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        text-align: center;
        display: inline-block;
        padding: 8px 16px;
        border: none;
        border-radius: 8px;
        margin: 3px 5px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }

    .btnC:before, .btnCR:before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }

    .btnC:hover, .btnCR:hover {
        color: #fff;
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    .btnC:hover:before, .btnCR:hover:before {
        left: 100%;
    }

    .btnC:active, .btnCR:active {
        transform: translateY(0);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .btnShow{
        color: #fff;
        font-family: 'Microsoft JhengHei', Arial, sans-serif;
        font-size: 13px;
        font-weight: 500;
        line-height: 16px;
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        text-align: center;
        display: inline-block;
        padding: 8px 16px;
        border: none;
        border-radius: 8px;
        margin: 3px 5px;
        min-width: 80px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }

    .btnShow:hover {
        background: linear-gradient(135deg, #00f2fe 0%, #4facfe 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(79, 172, 254, 0.4);
    }

    .btnShow:active {
        transform: translateY(0);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    /* Modern Button Styles */
    .modern-btn {
        font-family: 'Microsoft JhengHei', Arial, sans-serif;
        font-size: 14px;
        font-weight: 600;
        padding: 12px 24px;
        border: none;
        border-radius: 25px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-block;
        text-align: center;
        min-width: 100px;
        margin: 5px 8px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transform: translateY(0);
    }

    .modern-btn:before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.6s;
    }

    .modern-btn:hover:before {
        left: 100%;
    }

    .modern-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    }

    .modern-btn:active {
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    }

    .modern-btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .modern-btn-primary:hover {
        background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
    }

    .modern-btn-success {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
    }

    .modern-btn-success:hover {
        background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
    }

    .modern-btn-info {
        background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
        color: white;
    }

    .modern-btn-info:hover {
        background: linear-gradient(135deg, #3182ce 0%, #2c5282 100%);
    }

    .modern-btn-warning {
        background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
        color: white;
    }

    .modern-btn-warning:hover {
        background: linear-gradient(135deg, #dd6b20 0%, #c05621 100%);
    }

    .modern-btn-danger {
        background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
        color: white;
    }

    .modern-btn-danger:hover {
        background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
    }

    /* Enhanced styles for selected sales tags */
    .setSales, .setSalesR {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        padding: 8px 12px;
        border-radius: 20px;
        margin: 3px 5px;
        display: inline-block;
        font-size: 13px;
        font-weight: 500;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        position: relative;
        font-family: 'Microsoft JhengHei', Arial, sans-serif;
    }

    .setSales a, .setSalesR a {
        color: white;
        text-decoration: none;
        margin-left: 8px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        width: 18px;
        height: 18px;
        display: inline-block;
        text-align: center;
        line-height: 18px;
        font-size: 12px;
        transition: all 0.3s ease;
    }

    .setSales a:hover, .setSalesR a:hover {
        background: rgba(255, 255, 255, 0.5);
        transform: scale(1.1);
    }

    /* Enhanced checkbox styles */
    .btnC {
        cursor: pointer;
        user-select: none;
        position: relative;
        padding-left: 28px;
        min-height: 24px;
        display: inline-block;
    }
    .btnC input[type="checkbox"] {
        appearance: none;
        width: 18px;
        height: 18px;
        border: 2px solid #ddd;
        border-radius: 4px;
        position: absolute;
        left: 5px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        margin: 0;
        background: white;
        transition: all 0.3s ease;
    }
    .modern-input {
        font-family: 'Microsoft JhengHei', Arial, sans-serif;
        font-size: 15px;
        font-weight: 500;
        padding: 8px 12px;
        border: 2px solid #667eea;
        border-radius: 8px;
        background: linear-gradient(135deg, #f8f9ff 0%, #e8f4fd 100%);
        color: #4a5568;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.08);
        outline: none;
        transition: border-color 0.3s, box-shadow 0.3s;
        min-width: 120px;
    }
    .modern-input:focus {
        border-color: #764ba2;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.18);
        background: linear-gradient(135deg, #e8f4fd 0%, #f8f9ff 100%);
    }

    .btnC input[type="checkbox"]:checked {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #667eea;
    }

    .btnC input[type="checkbox"]:checked::after {
        content: '✓';
        color: white;
        font-size: 12px;
        font-weight: bold;
        position: absolute;
        top: -1px;
        left: 2px;
    }

    /* Enhanced container styles */
    .store div[style*="border: 1px solid #CCC"] {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border: 2px solid #e9ecef !important;
        border-radius: 12px !important;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .store div[style*="border: 1px solid #CCC"]:hover {
        border-color: #667eea !important;
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.1);
    }

    /* Enhanced input field styles */
    input[type="text"].datepickerROC,
    select[name*="Sales"] {
        padding: 10px 15px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-family: 'Microsoft JhengHei', Arial, sans-serif;
        font-size: 14px;
        transition: all 0.3s ease;
        background: white;
        margin: 0 8px;
    }

    input[type="text"].datepickerROC:focus,
    select[name*="Sales"]:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    /* Area selection buttons container */
    .city1, .city1R, .area, .areaR {
        background: transparent;
        padding: 15px;
        border-radius: 10px;
        margin: 10px 0;
    }
    }

    .showPointer {
        cursor: pointer;
    }

    .store{
        overflow-y:scroll;
        overflow-x:hidden;
        height: 700px;
        margin-top: 10px;
        border:1px solid #999;
    }

    .store1{
        overflow-y:hidden;
        overflow-x:hidden;
        height: auto;
        padding: 5px;
        border:1px solid #CCC;
    }

    .storeR{
        overflow-y:scroll;
        overflow-x:hidden;
        height: 700px;
        margin-top: 10px;
        border:1px solid #999;
    }

    .store1R{
        overflow-y:hidden;
        overflow-x:hidden;
        height: auto;
        padding: 5px;
        border:1px solid #CCC;
    }

    .tb th{
        color: rgb(255, 255, 255);
        font-family: 微軟正黑體, "Microsoft JhengHei", 新細明體, PMingLiU, 細明體, MingLiU, 標楷體, DFKai-sb, serif;
        font-size: 1em;
        font-weight: bold;
        background-color: rgb(156, 40, 33);
        border: 1px solid #CCCCCC;
        padding: 6px;
    }

    .tb td{
        padding: 6px;
        border: 1px solid #CCCCCC;
        text-align: left;
    }

    .tb input[type="checkbox"]{
        display: inline-block;
        width: 20px;
        height: 20px;
        margin: -3px 4px 0 0;
        vertical-align: middle;
    }

    .setSales, .setSalesR{
        background-color: white;
        margin-right: 1px;
        border: 1px solid #999;
        padding: 2px;
    }
	</style>
</head>
<body id="dt_example" >
    <div class="cmc_overlay">
        <div class="cmc_overlay__inner">
            <div class="cmc_overlay__content"><span class="cmc_spinner"></span></div>
        </div>
    </div>

    <div id="wrapper">
    <div id="header">
        <table width="1000" border="0" cellpadding="2" cellspacing="2">
            <tr>
                <td width="233" height="72">&nbsp;</td>
                <td width="753">
                    <table width="100%" border="0" align="right" cellpadding="3" cellspacing="3">
                        <tr>
                            <td colspan="3" align="right"><h1><{include file='welcome.inc.tpl'}></h1></td>
                        </tr>
                        <tr>
                            <td width="81%" align="right"><!-- <a href="#" onClick="window.open('/bank/create.php', '_blank', config='height=450,width=650,resizable=yes');"><img src="/images/icon_a1.png" alt="" width="94" height="22" /></a> --></td>
                            <td width="14%" align="center"><h2> 登入者 <{$smarty.session.member_name}></h2></td><td width="5%" height="30" colspan="2"><h3><a href="/includes/member/logout.php">登出</a></h3></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table> 
    </div>
    <{include file='menu1.inc.tpl'}>
    <table width="1000" border="0" cellpadding="4" cellspacing="0">
        <tr>
            <td bgcolor="#DBDBDB">
                <table width="100%" border="0" cellpadding="4" cellspacing="1">
                    <tr>
                        <td height="17" bgcolor="#FFFFFF">
                            <div id="menu-lv2"></div>
                            <br/>                        
                            <h3>&nbsp;</h3>		
                            <div id="container">
                                <h1>區域績效業務歸屬</h1>	
                                <div id="tabs">
                                    <ul>
                                        <li><a href="#tabs-scrivener">地政士區域預設業務設定</a></li>
                                        <li><a href="#tabs-realty">仲介區域預設業務設定</a></li>
                                        <li><a href="#tabs-multiscrivener">地政士多店業務轉移設定</a></li>
                                        <li><a href="#tabs-multirealty">仲介多店業務轉移設定</a></li>
                                    </ul>

                                    <div id="tabs-scrivener">
                                        <div style="padding-bottom: 20px;">
                                            <div class="w-auto p-1.5 pb-2.5">
                                                <button class="modern-btn modern-btn-primary" onclick="defaultDistinct('S')">🏢 區域業務設定</button>
                                            </div>

                                            <table id="example-scrivener" class="display" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>縣市</th>
                                                        <th>地區</th>
                                                        <th>負責業務</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <{foreach from=$scrivener key=key item=item}>
                                                    <tr>
                                                        <td><{$item.city}></td>
                                                        <td><{$item.area}></td>
                                                        <td><{$item.sales}></td>
                                                    </tr>
                                                <{/foreach}>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>縣市</th>
                                                        <th>地區</th>
                                                        <th>負責業務</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>

                                    <div id="tabs-realty">
                                        <div class="w-auto p-1.5 pb-2.5">
                                            <button class="modern-btn modern-btn-primary" onclick="defaultDistinct('R')">🏢 區域業務設定</button>
                                        </div>

                                        <div style="padding-bottom: 20px;">
                                            <table id="example-realty" class="display" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>縣市</th>
                                                        <th>地區</th>
                                                        <th>負責業務</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <{foreach from=$realty key=key item=item}>
                                                    <tr>
                                                        <td><{$item.city}></td>
                                                        <td><{$item.area}></td>
                                                        <td><{$item.sales}></td>
                                                    </tr>
                                                <{/foreach}>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>縣市</th>
                                                        <th>地區</th>
                                                        <th>負責業務</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                    
                                    <div id="tabs-multiscrivener">
                                        <div style="padding-bottom: 20px;">
                                            <div style="border: 1px solid #CCC;padding: 10px;">
                                                縣市：
                                                <input type="button" value="📍 選擇" class="modern-btn modern-btn-primary city" onclick="showCity()" id="open">&nbsp;&nbsp;
                                                <input type="button" value="🔍 查詢" class="modern-btn modern-btn-success" onclick="searchS()">
                                                <div class="city1"></div>
                                                <div class="area"></div>
                                            </div>

                                            <div class="store store1" style="display:none;">
                                                <div class="searchleft1">
                                                    切換時間：<input type="date" name="date" id="" value="" style="width:120px;" class="modern-input">&nbsp;&nbsp;
                                                    業務：<{html_options name=sSales options=$menu_sales }>
                                                    <input type="button" value="➕ 增加" class="modern-btn modern-btn-info" onclick="addSales()">&nbsp;&nbsp;
                                                    <input type="button" value="⚙️ 設定" class="modern-btn modern-btn-warning" onclick="setSales()">

                                                    <div style="margin-top: 20px;margin-bottom: 20px; padding: 15px; background: linear-gradient(135deg, #f8f9ff 0%, #e8f4fd 100%); border-radius: 10px; border: 1px solid #e2e8f0;">
                                                        <span style="color: #4a5568; font-weight: 600; font-size: 14px;">👥 已選擇的業務：</span>
                                                        <span id="salesGroup"></span>
                                                    </div>
                                                </div>
                                                <div class="searchright">
                                                    
                                                </div>
                                                
                                                <div class="store" id="store"></div>	
                                            </div>
                                        </div>
                                    </div>

                                    <div id="tabs-multirealty">
                                        <div style="padding-bottom: 20px;">
                                            <div style="border: 1px solid #CCC;padding: 10px;">
                                                縣市：
                                                <input type="button" value="📍 選擇" class="modern-btn modern-btn-primary cityR" onclick="showCity('R')" id="open">&nbsp;&nbsp;
                                                <input type="button" value="🔍 查詢" class="modern-btn modern-btn-success" onclick="searchR()">
                                                <div class="city1R"></div>
                                                <div class="areaR"></div>
                                            </div>
                                            <div class="storeR store1R" style="display:none;">
                                                
                                                <div class="searchleft1R">
                                                    切換時間：<input type="date" name="dateR" id="" value="" style="width:120px;" class="modern-input">&nbsp;&nbsp;
                                                    業務：<{html_options name=sSalesR options=$menu_sales }>
                                                    <input type="button" value="➕ 增加" class="modern-btn modern-btn-info" onclick="addSales('R')">&nbsp;&nbsp;
                                                    <input type="button" value="⚙️ 設定" class="modern-btn modern-btn-warning" onclick="setSales('R')">

                                                    <div style="margin-top: 20px;margin-bottom: 20px; padding: 15px; background: linear-gradient(135deg, #f8f9ff 0%, #e8f4fd 100%); border-radius: 10px; border: 1px solid #e2e8f0;">
                                                        <span style="color: #4a5568; font-weight: 600; font-size: 14px;">👥 已選擇的業務：</span>
                                                        <span id="salesGroupR"></span>
                                                    </div>
                                                </div>
                                                <div class="searchrightR">
                                                    
                                                </div>
                                                
                                                <div class="storeR" id="storeR"></div>	
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="footer" style="height:50px;">
                                    <p>2012 第一建築經理股份有限公司 版權所有</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    </div>

    <datalist id="stores">
    <{foreach from=$stores key=key item=item}>
    <option value="<{$item.name}>(<{$item.id}>)">
    <{/foreach}>
    </datalist>

</body>
</html>