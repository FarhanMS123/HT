<?php
    include __DIR__ . "/../init.php";
    $err_msg = array();

    if(!$current_user){
        header('Location: ../login.php');
        die();
    }

    $isPOST = $_SERVER["REQUEST_METHOD"] == "POST";
    try{
        if($isPOST){
            if(isset($_POST["update"])){
                if(isset($_FILES["cv"]) && preg_match("/\.pdf$/i", $_FILES["cv"]["name"]) && $_FILES["cv"]["size"] <= 4 * 1024 * 1024){
                    $filename = time() . "_" . rand(10000,99999) . "_" . onlyWordDigit($current_user["nama"]) . ".pdf";
                    
                    if(updateUser($current_user["id"], array("cv"=>$filename))){
                        if($current_user["cv"]) unlink($root_path . "/cv/" . $current_user["cv"]);
                        move_uploaded_file($_FILES["cv"]["tmp_name"], $root_path . "/cv/" . $filename);
                        $current_user = getCurrentUser(true);
                    }else array_push($err_msg, "CV tidak dapat diperbarui. Coba sekali lagi.");
                }
            }
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
    <div class="shadow w-100 text-left" style="background:#11181f;">
        <div class="p-4">
            <div class="row">
                <div class="col-12 col-sm-4 col-md-3 col-lg-2">
                    <div class="rounded-circle border d-inline-block overflow-hidden" style="width:8em; height:8em; background:#eeeeee;">
                        <img src="../foto/<?= $current_user["foto"] ?>" style="width:calc(100% + 2px); margin:-1px;"></img>
                    </div>
                </div>
                <div class="col text-white">
                    <h2 class="m-0 p-0"><?= htmlentities($current_user["nama"], ENT_QUOTES) ?></h2>
                    <span><?= htmlentities($current_user["email"], ENT_QUOTES) ?></span>
                    <?php if($team){ ?><p class="p-0 m-0">
                        <?php if($team["verifikasi"]){ ?><span class="badge badge-primary"><?= $team["nama"] ?></span>
                        <?php }else{ ?><span class="badge badge-danger"><?= $team["nama"] ?></span><?php } ?>
                        <?php if($team["ketua"] == $current_user["email"]){ ?><span class="badge badge-primary">Ketua</span><?php } ?>
                    </p><?php } ?>
                    <p class="p-0 m-0">
                        <?php if(!$team){ ?><span class="badge badge-danger">tidak terdaftar</span> <?php } ?>
                        <?php if(!$current_user["cv"]){ ?><span class="badge badge-danger">CV :: belum lengkap</span><?php } ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="p-2">
            <ul class="nav nav-pills">
                <li class="nav-item"><a class="nav-link" href="profile.php">Profil</a></li>
                <li class="nav-item"><a class="nav-link active" href=".">Curiculum Vitae</a></li>
                <li class="nav-item"><a class="nav-link" href="team.php">Tim</a></li>
            </ul>
        </div>
    </div>
    <div class="pt-4 text-center pb-4">
        <div class="d-inline-block col-11 col-lg-8">
            <div class="card w-100 d-inline-block text-left shadow-sm">
            <?php for($i=0; $i<count($err_msg); $i++){ ?>
                <div class="card-header bg-danger text-white"><?= $err_msg[$i] ?></div>
            <?php } ?>
                <div class="card-body">
                    <form id="frmUpdate" class="clearfix" action="" method="POST" enctype="multipart/form-data">
                        <div class="form-group text-left align-bottom">
                            <label for="filePP" class="d-block">Unggah Curiculum Vitae dalam ekstensi PDF.</label>
                            <input type="file" accept=".pdf" class="d-inline-block" id="filePP" name="cv">
                            <button type="submit" class="btn btn-primary" name="update" value="true">Unggah</button>
                        </div>
                    </form>
                    <?php if(strlen($current_user["cv"]) > 0){ ?><div>
                        <iframe class="w-100" style="height:50em;" src="../cv/<?= $current_user["cv"] ?>"></iframe>
                    </div> <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <script src="../global.js"></script>
</body>
</html>