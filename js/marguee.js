function gomarguee() {
	// 先取得 div#abgne_marquee ul
	// 接著把 ul 中的 li 項目再重覆加入 ul 中(等於有兩組內容)
	// 再來取得 div#abgne_marquee 的高來決定每次跑馬燈移動的距離
	// 設定跑馬燈移動的速度及輪播的速度
	var $marqueeUl = $('div#abgne_marquee ul'),
		$marqueeli = $marqueeUl.append($marqueeUl.html()).children(),
		_height = $('div#abgne_marquee').height() * -1,
		scrollSpeed = 600,
		timer,
		speed = 3000 + scrollSpeed;
 
	// 幫左邊 $marqueeli 加上 hover 事件
	// 當滑鼠移入時停止計時器；反之則啟動
	$marqueeli.hover(function(){
		clearTimeout(timer);
	}, function(){
		timer = setTimeout(showad, speed);
	});
 
	// 控制跑馬燈移動的處理函式
	function showad(){
		var _now = $marqueeUl.position().top / _height;
		_now = (_now + 1) % $marqueeli.length;
 
		// $marqueeUl 移動
		$marqueeUl.animate({
			top: _now * _height
		}, scrollSpeed, function(){
			// 如果已經移動到第二組時...則馬上把 top 設 0 來回到第一組
			// 藉此產生不間斷的輪播
			if(_now == $marqueeli.length / 2){
				$marqueeUl.css('top', 0);
			}
		});
 
		// 再啟動計時器
		timer = setTimeout(showad, speed);
	}
 
	// 啟動計時器
	timer = setTimeout(showad, speed);
 
	$('a').focus(function(){
		this.blur();
	});
}

function getMarguee(pid) {
	$.post('/includes/getMarquee.php',{'pid':pid}, function(txt) {
		if (txt) {
			//alert(txt) ;
			var _txt = '' ;
			var obj = jQuery.parseJSON(txt) ;
			
			$.each(obj, function(key,val) {
				var _arr = val.split('_') ;			//取出id
				var _arr2 = _arr[0].split('(') ;		//取出保證號碼
				_txt = _txt + '<li><a href="#" onclick="openFormedit(\''+_arr[1]+'\',\''+_arr2[0]+'\')">' + _arr[0] + '  入帳待確認</a></li>' ;
			}) ;
			$('#abgne_marquee').css('display','block') ;
			$('#abgne_marquee ul').html(_txt) ;
			gomarguee() ;
		}
		else {
			$('#abgne_marquee').css('display','none') ;
		}
	}) ;
}

function getMarguee2(pid) {
	$.post('/includes/getMarquee.php',{'pid':pid}, function(txt) {
		if (txt) {
			//alert(txt) ;
			var _txt = '' ;
			var obj = jQuery.parseJSON(txt) ;
			
			$.each(obj, function(key,val) {
				var _arr = val.split('_') ;			//取出id
				var _arr2 = _arr[0].split('(') ;		//取出保證號碼
				_txt = _txt + '<li><a href="#" onclick="openFormedit(\''+_arr[1]+'\',\''+_arr2[0]+'\')">' + _arr[0] + '  入帳待確認</a></li>' ;
			}) ;
			$('#abgne_marquee').css('display','block') ;
			$('#abgne_marquee ul').empty().html(_txt) ;
			// gomarguee() ;
		}
		else {
			$('#abgne_marquee ul').empty() ;
			$('#abgne_marquee').css('display','none') ;
		}
	}) ;
}

function openFormedit(id,cid) {
	//alert(id+','+cid) ;
	$('body').append('<form id="openFormedit" method="POST" action="/income/formedit.php"><input type="hidden" name="certifyid" value="'+cid+'"><input type="hidden" name="id" value="'+id+'"></form>') ;
	$('#openFormedit').submit() ;
}
