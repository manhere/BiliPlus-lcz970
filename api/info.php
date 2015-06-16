<form name="input" action="/api/getaid.php" method="get">
<input type="hidden" name="act" value="info">
	<fieldset>
		<legend><b>说明</b></legend>
		<fieldset>
			<legend>解析源</legend>
			此功能可获得投稿源视频文件、MP4视频文件、弹幕文件，请填写投稿AV号及分P页码。<br/>目前仅通过调用哔哩哔哩开放API获取信息，如解析失败请检查您的节操。
		</fieldset>
		<fieldset>
			<legend>提示</legend>
			根据哔哩哔哩服务器情况，点击“解析”后可能需要等待数秒，请稍候。<br/>此功能仅为方便哔哩哔哩弹幕网会员保存视频/弹幕资源用，请严格遵守投稿UP主及哔哩哔哩弹幕网的相关规定使用资源。
		</fieldset>
		<fieldset>
			<legend>说明</legend>
			AV号获取：于上方输入框输入AV号后进入分P选择页面，然后选择要获取的章节<br/>
			CID号获取：于下方输入框输入CID号后进入CID获取页面
		</fieldset>
	</fieldset><br/>
	<fieldset>
		<legend><b>获取分P列表</b></legend>
		AV<input type="tel" name="av" placeholder="B站投稿号(只填写“AV”后面的数字)(试试AV6)" onkeyup="value=value.replace(/[^\d]/g,'')" onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))" style="width:300px;ime-mode:Disabled"><br/>
	</fieldset><br/>	
<center><input type="submit" value="　　　　解析　　　　"></center>
</form>
<form name="input" action="/api/random.php" method="post">
<input type="submit" value="试试手气？">
</form>
<br/><br/><br/><br/>
<form name="input" action="/api/cid.php" method="get">
	<fieldset>
		<legend><b>CID解析</b></legend>
		CID<input type="tel" name="cid" placeholder="B站CID号(AV号请填入上方输入框)" onkeyup="value=value.replace(/[^\d]/g,'')" onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))" style="width:300px;ime-mode:Disabled"><br/>
	</fieldset><br/>	
<center><input type="submit" value="　　　　解析　　　　"></center>
</form>