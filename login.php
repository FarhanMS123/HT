<?php
    include __DIR__ . "/init.php";
    $err_msg = array();

    if($current_user){
        header('Location: ./user/profile.php');
        die();
    }

    $isPOST = false;
    if($_SERVER["REQUEST_METHOD"] == "POST") $isPOST = true;
    try{
        if($isPOST){
            if(isset($_POST["login"])){
                if(strlen($_POST["email"]) > 0 && strlen($_POST["password"]) > 0){
                    if(login($_POST["email"], $_POST["password"], isset($_POST["remember"]))){
                        header('Location: ./user/profile.php');
                        die();
                    }else{
                        array_push($err_msg, "Kredensial Anda tidak diterima atau akun memang tidak pernah ada. Coba masukan kata sandi Anda kembali atau buat akun baru.");
                        fixFormsData();
                    }
                }else{
                    array_push($err_msg, "Autentikasi yang diminta tidak sesuai. Silakan autentikasi diri Anda kembali.");
                    fixFormsData();
                }
            }else{
                array_push($err_msg, "Operasi tidak dikenali. Silakan autentikasi diri Anda kembali.");
                fixFormsData();
            }
        }
    }catch(\Exception $err){
        array_push($err_msg, $err->getMessage());
        if($isPOST) fixFormsData();
    }

    function fixFormsData(){
        $_POST["email"] = isset($_POST["email"]) ? htmlspecialchars($_POST["email"], ENT_QUOTES) : "";
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Hackathon</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
</head>
<body class="p-0 m-0" style="background:#11181f;">
    <div class="w-100 text-center" style="margin-top:5em; margin-botton:3em;">
        <div class="d-inline-block card text-left" style="width:24em;">
        <?php for($i=0; $i<count($err_msg); $i++){ ?>
            <div class="card-header bg-danger text-white"><?= $err_msg[$i] ?></div>
        <?php } ?>
            <div class="card-body shadow">
                <a href="register.php" class="float-right">or create an account</a>
                <h5 class="card-title">Login</h5>
                <form class="clearfix" action="login.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <div class="form-group">
                        <label for="txtEmail">Email</label>
                        <input type="email" class="form-control" id="txtEmail" name="email"<?php if($isPOST){ ?> value="<?= $_POST["email"] ?>"<?php } ?>>
                    </div>
                    <div class="form-group">
                        <label for="txtPassword">Password</label>
                        <input type="password" class="form-control" id="txtPassword" name="password">
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="chkRemember" name="remember">
                        <label class="form-check-label" for="chkRemember">Remember me</label>
                    </div>
                    <button type="submit" class="btn btn-primary float-right" name="login" value="true">Login</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>