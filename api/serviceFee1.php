<?php
require_once __DIR__ . '/serviceFeeData.php';
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>成交資料暨仲介服務費出款申請單</title>

	<style>

		.page{
			width: 595px;
			/*border:1px solid #000;*/
			background-color: white;
			/*font-size: 12px;*/
		}
		.title{
			font-family: "新細明體", "PMingLiU", "細明體", "MingLiU", "serif";
			text-align: center;
			font-size: 18px;
			font-weight:bold;
		}
		.title2{
			font-family: "新細明體", "PMingLiU", "細明體", "MingLiU", "serif";
			text-align: right;
			font-size: 16px;
			font-weight:bold;
		}
		.tb th{
			border: 1px solid #000;
			font-weight:bold;
			/*line-height: 20px;*/
			padding-top: 3px;
			padding-bottom: 3px;
		}
		.tb td{
			border: 1px solid #000;
			/*line-height: 20px;*/
			padding-top: 3px;
			padding-bottom: 3px;
		}
		.vertical-mode {
		    writing-mode: tb-rl;
		    -webkit-writing-mode: vertical-rl;
		    writing-mode: vertical-rl;

		}
		.small{
			font-size: 8px
		}
		.text{
			line-height: 20px;
			text-align: left;
		}
		.line{
			padding-top: 3px;
			padding-bottom:3px;
		}

	</style>
</head>
<body>
	<div class="page">
		<div class="title">成交資料暨仲介服務費出款申請單</div>
		<div class="title2">簽約日期：<?=$data["cSignDate"]?></div>

		<table cellpadding="0" cellspacing="0" width="595px" class="tb">
			<tr>
				<th width="100px">保證編號</th>
				<td>&nbsp;<?=$data["cCertifiedId"]?></td>
				<th width="100px">地  政  士</th>
				<td>&nbsp;<?=$data["Scrivener"]?></td>
				<th width="100px">案件連絡人</th>
				<td width="100px">&nbsp;&nbsp;</td>
			</tr>
			<tr>
				<!-- <th height="80px" rowspan="3"  width="100px"><span class="vertical-mode">客戶資料</span></th> -->
				<th width="150px">買方姓名</th>
				<td colspan="2">&nbsp;<?=$data["buyer"]?></td>
				<th width="150px">賣方姓名</th>
				<td  colspan="2"><?=$data["owner"]?></td>
			</tr>
			<tr>
				<th width="150px">身分證/統一編號</th>
				<td colspan="2">&nbsp;<?=$data["buyerId"]?></td>
				<th width="150px">身分證/統一編號</th>
				<td colspan="2"><?=$data["ownerId"]?></td>
			</tr>
			<tr>
				<th colspan="3" align="left">手機號碼：<?=$data['buyerphone']?><br><span class="small">(若不願收受簡訊則請勿填寫)</span></th>
				<th colspan="3" align="left">手機號碼：<?=$data['ownerphone']?><br><span class="small">(若不願收受簡訊則請勿填寫)</span></th>
			</tr>
			<tr>
				<!-- <th rowspan="2" width="80px">經紀人<br>員資料<br><span class="small">(收受簡訊)</span></th> -->
				<th>買方經紀人員</th>
				<td  colspan="2">&nbsp;<?=$data['buyersale']?></td>
				<th>賣方經紀人員</th>
				<td colspan="2">&nbsp;<?=$data['ownersale']?></td>
			</tr>
			<tr>
				<th>手機號碼</th>
				<td  colspan="2">&nbsp;<?=$data['buyersalephone']?></td>
				<th>手機號碼</th>
				<td colspan="2">&nbsp;<?=$data['ownersalephone']?></td>
			</tr>
			<tr>
				<th width="80px">地址</th>
				<td colspan="5">&nbsp;<?=$data['addr']?></td>
			</tr>
			<tr>
				<td colspan="3">
					<div style="float: left;display:inline;width:50%;">總價：<br><br><u><?=$data['cTotalMoney']?></u></div>
					<div style="float: left;display:inline;width:50%;">
						簽約：<?=$data['cSignMoney']?><br>
						用印：<?=$data['cAffixMoney']?><br>
						完稅：<?=$data['cDutyMoney']?><br>
						尾款：<?=$data['cEstimatedMoney']?><br>
					</div>


				</td>
				<td colspan="3">
					□無□有設定扺押權&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;萬元<br>
					□無□有私人設定&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;萬元<br>
					□無□有解約條款<br>
					□無□有限制登記<br>
				</td>
			</tr>

			<tr>
				<td colspan="3" valign="top">
					<div class="line"><span style="font-size: 18px;">【買方】</span>仲介： <span style="font-size: 12px;">(如配件拆帳 請詳細填寫！)</span></div>
					<div class="line">(1)<?=$buyerBrand[0] . $buyerBranch[0]?></div>
					<div class="line" style="float: left;display:inline;">買方服務費：</div>
					<div class="line" style="float: left;display:inline;width: 180px;border-bottom: 1px solid;text-align: right;">元</div>
					<div class="line" style="clear: both"></div>
					<div class="line" style="float: left;display:inline;">
					<?php if ($buyerBranch[1] != ''): ?>
						(2)<?=$buyerBrand[1] . $buyerBranch[1]?>
					<?php else: ?>
						(2) <span style="padding-left: 100px;border-bottom: 1px solid black;line-height: 14px;"></span> 房屋<span style="padding-left: 100px;border-bottom: 1px solid black;line-height: 14px;"></span>店
					<?php endif?>
					</div>
					<div class="line" style="clear: both"></div>
					<div class="line" style="float: left;display:inline;">買方服務費：</div>
					<div class="line" style="float: left;display:inline;width: 180px;border-bottom: 1px solid;text-align: right;">元</div>
					<div class="line" style="clear: both"></div>

					<?php if ($buyerBranch[2] != ''): ?>
						(3)<?=$buyerBrand[2] . $buyerBranch[2]?>
						<div class="line" style="clear: both"></div>
						<div class="line" style="float: left;display:inline;">買方服務費：</div>
						<div class="line" style="float: left;display:inline;width: 180px;border-bottom: 1px solid;text-align: right;">元</div>
						<div class="line" style="clear: both"></div>
					<?php else: ?>

					<?php endif?>
					</div>


					<div class="line">★【撥款時機】：(須於買方匯入後再為撥付)</div>
					<div class="line">□於簽約後全數撥付 □於交屋時全數撥付</div>
					<div style="float: left;display:inline;">□簽約後先撥付</div>
					<div style="float: left;display:inline;width: 150px;border-bottom: 1px solid;text-align: right;">元，</div>
					<div style="clear: both"></div>
					<div style="float: left;display:inline;">餘款交屋時撥付</div>
					<div style="float: left;display:inline;width: 150px;border-bottom: 1px solid;text-align: right;">元｡</div><br>

				</td>
				<td colspan="3" valign="top">
					<div class="line"><span style="font-size: 18px;">【賣方】</span>仲介： <span style="font-size: 12px;">(如配件拆帳 請詳細填寫！)</span></div>
					<div class="line">(1)<?=$ownerBrand[0] . $ownerBranch[0]?></div>
					<div class="line" style="float: left;display:inline;">賣方服務費：</div>
					<div class="line" style="float: left;display:inline;width: 180px;border-bottom: 1px solid;text-align: right;">元</div>
					<div class="line" style="clear: both"></div>
					<div class="line" style="float: left;display:inline;">
					<?php if ($ownerBranch[1] != ''): ?>
						(2)<?=$ownerBrand[1] . $ownerBranch[1]?>
					<?php else: ?>
						(2) <span style="padding-left: 100px;border-bottom: 1px solid black;line-height: 14px;"></span> 房屋<span style="padding-left: 100px;border-bottom: 1px solid black;line-height: 14px;"></span>店
					<?php endif?>
					</div>
					<div class="line" style="clear: both"></div>
					<div class="line" style="float: left;display:inline;">賣方服務費：</div>
					<div class="line" style="float: left;display:inline;width: 180px;border-bottom: 1px solid;text-align: right;">元</div>
					<div class="line" style="clear: both"></div>

					<?php if ($ownerBranch[2] != ''): ?>
						(3)<?=$ownerBrand[2] . $ownerBranch[2]?>
						<div class="line" style="clear: both"></div>
						<div class="line" style="float: left;display:inline;">買方服務費：</div>
						<div class="line" style="float: left;display:inline;width: 180px;border-bottom: 1px solid;text-align: right;">元</div>
						<div class="line" style="clear: both"></div>
					<?php else: ?>

					<?php endif?>
					</div>


					<div class="line">★【撥款時機】：(須於買方匯入後再為撥付)</div>
					<div class="line">□於簽約後全數撥付 □於交屋時全數撥付</div>
					<div style="float: left;display:inline;">□簽約後先撥付</div>
					<div style="float: left;display:inline;width: 150px;border-bottom: 1px solid;text-align: right;">元，</div>
					<div style="clear: both"></div>
					<div style="float: left;display:inline;">餘款交屋時撥付</div>
					<div style="float: left;display:inline;width: 150px;border-bottom: 1px solid;text-align: right;">元｡</div><br>
				</td>
			</tr>
			<tr>
				<th colspan="3">請蓋仲介公司章</th>
				<th colspan="3">請蓋仲介公司章</th>
			</tr>
			<tr>
				<td colspan="3" height="200px" valign="top">&nbsp;</td>
				<td colspan="3" height="200px" valign="top">&nbsp;</td>
			</tr>

		</table>

		<div class="text">★儘速填寫此表並蓋公司大章後回傳至第一建經，以利後續出款作業</div>
		<div class="text">&#9670;聯絡電話：<?=$tel?></div>
		<div class="text">&#9670;傳真：<?=$undertaker['pFaxNum']?></div>
	</div>



</body>
</html>