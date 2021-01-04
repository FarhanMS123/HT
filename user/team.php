<?php
    include __DIR__ . "/../init.php";
    $err_msg = array();

    if(!$current_user){
        header('Location: ../login.php');
        die();
    }

    // $team = false;
    // print_r($team);
    // die();

    $isPOST = $_SERVER["REQUEST_METHOD"] == "POST";
    try{
        if($isPOST){
            if(!$team && isset($_POST["buat"])){
                if(strlen($_POST["name"]) > 0 && checkEmail($_POST["ketua"]) && checkEmail($_POST["anggota1"]) && checkEmail($_POST["anggota2"]) && 
                   array_search($current_user["email"], array($_POST["ketua"], $_POST["anggota1"], $_POST["anggota2"])) >= 0){
                    if(createGroup($_POST["name"], $_POST["ketua"], $_POST["anggota1"], $_POST["anggota2"])){
                        $team = checkUserTeam($current_user["email"]);
                    }else{
                        array_push($err_msg, "Pengguna tidak ditemukan atau teman Anda telah bergabung dengan kelompok lain.");
                    }
                }else{
                    array_push($err_msg, "Masukkan tidak sesuai. Silakan mengisi informasi grup dengan format yang diminta.");
                }
            }else if($team && isset($_POST["bayar"]) && isset($_FILES["pembayaran"]) && 
                     preg_match("/\.(png|jpg|jpeg)$/i", $_FILES["pembayaran"]["name"]) && $_FILES["pembayaran"]["size"] <= 2 * 1024 * 1024){
                
                $data = array();
                $filename = time() . "_" . rand(10000,99999) . "_" . onlyWordDigit($team["nama"]) . "." . pathinfo($_FILES["pembayaran"]["name"])["extension"];
                $data["pembayaran"] = $filename;
                if(updateGroup($team["id"], $data)){
                    if($team["pembayaran"]) unlink($root_path . "/pembayaran/" . $team["pembayaran"]);
                    move_uploaded_file($_FILES["pembayaran"]["tmp_name"], $root_path . "/pembayaran/" . $data["pembayaran"]);
                }else{
                    array_push($err_msg, "Tidak dapat mengunggah pembayaran, silakan coba lagi.");
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
                <li class="nav-item"><a class="nav-link" href="cv.php">Curiculum Vitae</a></li>
                <li class="nav-item"><a class="nav-link active" href="team.php">Tim</a></li>
            </ul>
        </div>
    </div>
    <div class="pt-4 text-center pb-4">
        <div class="d-inline-block col-11 col-lg-8 text-left">
            
        <?php for($i=0; $i<count($err_msg); $i++){ ?>
            <div class="alert alert-danger" role="alert"><?= $err_msg[$i] ?></div>
        <?php } ?>

        <?php if(!$team){ ?>
            <div class="card w-100 d-inline-block shadow-sm mb-4">
                <div class="card-body">
                    <form id="frGroups" class="clearfix" action="team.php" method="POST" enctype="application/x-www-form-urlencoded">
                        <p>Buat kelompok baru</p>
                        <div class="form-group mw-100" style="width:20em;">
                            <input type="text" class="form-control" name="name" id="" placeholder="Nama Kelompok">
                        </div>
                        <div class="mb-3"></div>
                        <div class="form-group">
                            <label for="">Email Ketua</label>
                            <input type="email" class="form-control" name="ketua" id="" placeholder="">
                        </div>
                        <div class="form-group">
                            <label for="">Email Anggota 1</label>
                            <input type="email" class="form-control" name="anggota1" id="" placeholder="">
                        </div>
                        <div class="form-group">
                            <label for="">Email Anggota 2</label>
                            <input type="email" class="form-control" name="anggota2" id="" placeholder="">
                        </div>
                        <div class="clearfix">
                            <button type="submit" class="btn btn-primary float-right" name="buat" value="true">Buat</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php }else{ 
                $ketua = getUserInfo($team["ketua"]);
                $anggota1 = getUserInfo($team["anggota1"]);
                $anggota2 = getUserInfo($team["anggota2"]);
        ?>
            <div class="card w-100 d-inline-block shadow-sm mb-4">
                <div class="card-body">
                    <p><h1 class="d-inline"><?= $team["nama"] ?></h1> <span class="badge badge-danger">belum terverifikasi</span></p>
                    <div class="mb-3">
                        <form action="team.php" class="" method="post" enctype="multipart/form-data">
                            <div class="float-right">
                                <?php if($team["pembayaran"]){ ?><a target="_blank" class="d-inline-block" href="../pembayaran/<?= $team["pembayaran"] ?>" role="button">Cek bukti pembayaran</a><?php } ?>
                                <button type="submit" class="btn btn-primary ml-3" name="bayar" value="true">Unggah Pembayaran</button>
                            </div>
                            <div class="form-group">
                                <label for="">Upload bukti pembayaran</label>
                                <input type="file" class="form-control-file" name="pembayaran" accept="image/*">
                            </div>
                        </form>
                    </div>
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <td scope="row">Ketua</td>
                                <td style="width:7em;"><img class="w-100" src="../foto/<?= $ketua["foto"] ?>" /></td>
                                <td>
                                    <?= $ketua["nama"] ?><br />
                                    <a href="user.php?tim=ketua" target="_blank"><?= $ketua["email"] ?></a>
                                </td>
                                <td><?= $ketua["telepon"] ?></td>
                                <td><?php if($ketua["cv"]){ ?><a href="../cv/<?= $ketua["cv"] ?>">Curiculum Vitae</a><?php }else{ ?><span class="text-danger">Belum Lengkap</span><?php } ?></td>
                            </tr>
                            <tr>
                                <td scope="row">Anggota 1</td>
                                <td style="width:7em;"><img class="w-100" src="../foto/<?= $anggota1["foto"] ?>" /></td>
                                <td>
                                    <?= $anggota1["nama"] ?><br />
                                    <a href="user.php?tim=anggota1" target="_blank"><?= $anggota1["email"] ?></a>
                                </td>
                                <td><?= $anggota1["telepon"] ?></td>
                                <td><?php if($anggota1["cv"]){ ?><a href="../cv/<?= $anggota1["cv"] ?>">Curiculum Vitae</a><?php }else{ ?><span class="text-danger">Belum Lengkap</span><?php } ?></td>
                            </tr>
                            <tr>
                                <td scope="row">Anggota 2</td>
                                <td style="width:7em;"><img class="w-100" src="../foto/<?= $anggota2["foto"] ?>" /></td>
                                <td>
                                    <?= $anggota2["nama"] ?><br />
                                    <a href="user.php?tim=anggota2" target="_blank"><?= $anggota2["email"] ?></a>
                                </td>
                                <td><?= $anggota2["telepon"] ?></td>
                                <td><?php if($anggota2["cv"]){ ?><a href="../cv/<?= $anggota2["cv"] ?>">Curiculum Vitae</a><?php }else{ ?><span class="text-danger">Belum Lengkap</span><?php } ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php } ?>
        </div>
    </div>
    <script src="../global.js"></script>
</body>
</html>