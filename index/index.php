<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>云端文件存储系统</title>
  <meta name="author" content="Adtile">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="css/styles.css">
  <!--[if lt IE 9]>
  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
  <link rel="stylesheet" href="css/ie.css">
  <![endif]-->
  <script src="js/responsive-nav.js"></script>
</head>

<body>
  <header>
    <?php
    session_start();
    echo "<a href=\"#home\" class=\"logo\" data-scroll>Welcome ".$_SESSION['username']."! </a>";
    if(isset($_SESSION['username']))
    {
      echo "<a href=\"../login/logout.php\" class=\"logo\" data-scroll>log out</a>";
    }
    ?>
    <nav class="nav-collapse">
      <ul>
        <li class="menu-item active"><a href="#home">Home</a></li>
        <li class="menu-item"><a href="#upload">upload</a></li>
        <li class="menu-item"><a href="#download">download</a></li>
        <li class="menu-item"><a href="#verify">verify</a></li>
        <!-- <li class="menu-item"><a href="http://www.google.com" target="_blank">Google</a></li> -->
      </ul>
    </nav>
  </header>

  <section id="home">
    <h1>云端文件存储系统实现功能</h1>
    <?php
    if(!isset($_SESSION["username"]))
    {
      echo "<p><a href=\"../login/c.php\">登录</a>&nbsp或<a href=\"../register/register.html\">&nbsp;注册</a></p>";
    }
    ?>
    <br>
    <br>
    <p><h2>一、注册登录系统</h2><br>
      1、使用https绑定证书到域名而非IP地址<br>
      2、允许用户注册到系统<br>
      用户名的合法字符集范围：中文、英文字母、数字<br>
      类似：-、_、.等合法字符集范围之外的字符不允许使用<br>
      用户口令长度限制在36个字符之内<br>
      对用户输入的口令进行强度校验，禁止使用弱口令<br>
      3、使用合法用户名和口令登录系统<br>
      4、禁止使用明文存储用户口令<br>
      存储的口令即使被公开，也无法还原/解码出原始明文口令<br>
      <h2>二、上传与签名</h2><br>
      1、限制文件大小：< 10MB<br>
      2、限制文件类型：office文档、常见图片类型<br>
      3、匿名用户禁止上传文件<br>
      4、对文件进行对称加密存储到文件系统，禁止明文存储文件 （安全存储对称加密密钥）<br>
      5、系统对加密后文件进行数字签名<br>
      <h2>三、下载与解密</h2><br>
      1、提供匿名用户加密后文件的下载<br>
      客户端对下载后的文件进行数字签名验证<br>
      客户端对下载后的文件可以解密还原到原始文件<br>
      2、提供已登录用户解密后文件下载<br>
      3、下载URL设置有效期（限制时间或限制下载次数），过期后禁止访问 （数字签名 消息认证码）<br>
      4、提供静态文件的散列值下载<br>
    </p><br><br>

    <!-- <p>The code and examples are hosted on GitHub and can be <a href="https://github.com/adtile/fixed-nav">found from here</a>. Read more about the approach from&nbsp;<a href="http://blog.adtile.me/2014/03/03/responsive-fixed-one-page-navigation/">our&nbsp;blog</a>.</p> -->
  </section>

  <section id="upload">
    <h1>文件上传</h1>
    <form action="../up_down/upload.php" method="post" enctype="multipart/form-data">
      <input type="file" name="file" id="file"><br><br><br>
      <input type="submit" name="submit" value="提交">
    </form>
  </section>


  <section id="download">
    <?php
    session_start();
    include ("../connect/connect2.php");
    include ("../sign/pkdecrypt.php");
    include ("../up_down/func/downfuc.php");

    $name=$_SESSION['username'];
    echo "<h1>".$name."的文件列表</h1>";

    $uid=$_SESSION['user_id'];
    $checkfile="select * from filesystem where uid=$uid";
    $check=mysqli_query($con,$checkfile);
    if(mysqli_num_rows($check)>0)
    {
      $i=1;
      while($i<=mysqli_num_rows($check))
      {

        $row=mysqli_fetch_assoc($check);
        $name=$row["forign_name"];
        echo "$i\n";
        echo "$name\n";
        $key=pkDecipher($row["keyc"]);
        $path="../up_down/".downfuc($row,$key);

        $fid=$row["fid"];
        $fhash=$row["fhash"];
        $uuid=$row["uid"];

        echo "<a href=\"../up_down/share.php?fid=$fid&&fhash=$fhash&&uid=$uuid\">分享</a>
        <a href=$path>下载</a>";

        echo "<br>";
        $i+=1;
      }
      //<a href='../login/c.php'>返回主页面</a>
    }
    else {
      echo "还没有上传过文件";
      //  echo "<a href='c.php'>返回主页面</a>";
    }?>
  </section>

  <section id="verify">
  <h1>文件验证</h1>
  <div>
    <form action="../sign/verify.php" method="post" enctype="multipart/form-data">
    <label for="text">上传待验证文件</label><br>
    <input type="file" name="file"><br>
    <label for="text">上传文件哈希值</label><br>
    <input type="file" name="filehash"><br>
    <label for="text">上传文件的签名</label><br>
    <input type="file" name="signature"><br><br><br>
    <input type="submit" name="submit" value="验证" />
    </form>
  </div>
</section>

<script src="js/fastclick.js"></script>
<script src="js/scroll.js"></script>
<script src="js/fixed-responsive-nav.js"></script>
</body>
</html>
