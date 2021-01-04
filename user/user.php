<?php
    include __DIR__ . "/../init.php";
    $err_msg = array();

    if(!$current_user){
        header('Location: ../login.php');
        die();
    }

    $isPOST = $_SERVER["REQUEST_METHOD"] == "POST";
    $user = false;
    try{
        if(isset($_GET["tim"]) && preg_match("/^(ketua|anggota1|anggota2)$/", $_GET["tim"])){
            $user = getUserInfo($team[$_GET["tim"]]);
        }else{
            array_push($err_msg, "masukan tim harus diisi. kembali ke <a href=\"team.php\">halaman tim</a>.");
            // echo "masukan tim harus diisi. kembali ke <a href=\"team.php\">halaman tim</a>.";
            // die();
        }
    }catch(\Exception $err){
        array_push($err_msg, $err->getMessage());
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil | Hackathon</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
</head>
<body class="p-0 m-0" style="background:#eeeeee;">
    <div class="pt-4 text-center pb-4">
        <div class="d-inline-block col-11 col-lg-8">
        <?php for($i=0; $i<count($err_msg); $i++){ ?>
            <div class="alert alert-danger" role="alert"><?= $err_msg[$i] ?></div>
        <?php } ?>
        <?php if($user){ ?>
            <div class="card d-inline-block text-left shadow-sm w-100">
                <div class="card-body">
                    <div class="clearfix text-left">
                        <div class="text-left d-inline-block" style="padding:0em 1em;">
                            <div class="rounded-circle border d-inline-block overflow-hidden position-relative" style="width:5em; height:5em; background:#eeeeee;">
                                <img src="../foto/<?= $user["foto"] ?>" id="prevPP" style="width:calc(100% + 2px); margin:-1px;"></img>
                            </div>
                        </div>
                        <div class="d-inline-block text-left align-bottom" style="width:25em; max-width:100%;">
                            <h3><?= $user["nama"] ?></h3>
                            <label><?= $user["tanggal_lahir"] ?></label>
                        </div>
                        <table class="my-4">
                            <tr>
                                <td>Email</td>
                                <td><?= $user["email"] ?></td>
                            </tr>
                            <tr>
                                <td>LINE ID</td>
                                <td><?= $user["line"] ?></td>
                            </tr>
                            <tr>
                                <td>telepon</td>
                                <td><?= $user["telepon"] ?></td>
                            </tr>
                        </table>
                    <?php if(strlen($user["cv"]) > 0){ ?><div>
                        <iframe class="w-100" style="height:50em;" src="../cv/<?= $user["cv"] ?>"></iframe>
                    </div> <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    </div>
    <script src="../global.js"></script>
</body>
</html>