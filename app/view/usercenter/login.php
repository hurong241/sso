
请先登录：
    <form action="/login/checklogin" method="post" enctype="multipart/form-data" name="form1" id="form1">
    <p>
        用户名：
        <input name="username" type="text" id="username" value="test3"/>
    </p>
    <p>
        密码：
        <input name="password" type="text" id="password" value="test3"/>
    </p>
    <p>
        <input type="hidden" name="goto" value="<?php echo @$_GET['redirect'];?>">
        <input type="submit" name="button" id="button" value="提交"/>
    </p>
</form>