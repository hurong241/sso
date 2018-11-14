<!doctype html>
<html xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="utf-8">
    <script language="javascript" src="script/jquery.min.js" /></script>
    <script language="javascript" src="script/jquery.qrcode.min.js" /></script>
    <script language="javascript">
        $(function(){
            $('#code').qrcode("<?php echo $url;?>"); //任意字符串
        });
    </script>
    <title>无标题文档</title>
</head>

<body>
我的产品:<br/>
<a href="http://www.a.com" target="_blank">www.a.com</a><br/>
<a href="http://www.b.com" target="_blank">www.b.com</a><br/>
<a href="http://user.yunindex.com/loginout" target="_blank">退出</a><br/>


<br/>
<br/>
<br/>

<a href="<?php echo $url;?>" target="_blank">绑定微信</a>
<hr/>
扫码绑定：<br/>
<p id="code"></p>
</body>
</html>