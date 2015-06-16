<style type="text/css">
a{text-decoration:none}
a:link{color:#1E90FF}
a:visited{color:#1E90FF}
a:hover{color:#BBBBBB}
a:active{color:#BBBBBB}
</style>
<form name="input" action="/api/getaid.php" method="get">
<input type="hidden" name="act" value="play">
	<fieldset>
		<legend><b>说明</b></legend>
		<fieldset>
			<legend>播放器</legend>
			目前仅使用BiliPlayer<br/>
		</fieldset>
		<fieldset>
			<legend>使用</legend>
			AV号播放：于上方输入框输入AV号后进入分P列表，选择分P后播放<br/>
			CID号播放：于下方输入框输入CID号后进入播放页面<br/>
		</fieldset>
	</fieldset><br/>
	<fieldset>
		<legend><b>获取分P列表</b></legend>
		AV<input type="tel" name="av" placeholder="B站投稿号(只填写“AV”后面的数字)" onkeyup="value=value.replace(/[^\d]/g,'')" onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))" style="width:320px;ime-mode:Disabled"><br/>
	</fieldset><br/>	
<center><input type="submit" value="　　　　播放　　　　"></center>
</form>
<br/><br/><br/><br/>
<form name="input" action="/api/cidplay.php" method="get">
	<fieldset>
		<legend><b>CID播放</b></legend>
		CID<input type="tel" name="cid" placeholder="B站CID号(AV号请填入上方输入框)" onkeyup="value=value.replace(/[^\d]/g,'')" onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))" style="width:300px;ime-mode:Disabled"><br/>
	</fieldset><br/>	
	<input type="hidden" name="player" value="bilibili">
<center><input type="submit" value="　　　　播放　　　　"></center>
</form>