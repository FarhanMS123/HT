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
            if(isset($_POST["logout"])){
                logout();
                header('Location: ../login.php');
                die();
            }else if(isset($_POST["update"])){
                $data = array();
                if(isset($_FILES["foto"]) && preg_match("/\.(png|jpg|jpeg)$/i", $_FILES["foto"]["name"]) && $_FILES["foto"]["size"] <= 2 * 1024 * 1024){
                    $filename = time() . "_" . rand(10000,99999) . "_" . onlyWordDigit($_POST["nama"]) . "." . pathinfo($_FILES["foto"]["name"])["extension"];
                    $data["foto"] = $filename;
                }
                if(isset($_POST["nama"]) && strlen($_POST["nama"]) > 0) $data["nama"] = $_POST["nama"];
                if(isset($_POST["email"]) && preg_match("/^[\w\-\.]+@([\w\-]+\.)+[\w\-]{2,4}$/", $_POST["email"])) $data["email"] = $_POST["email"];
                if(isset($_POST["tanggal_lahir"]) && strlen($_POST["tanggal_lahir"]) > 0) $data["tanggal_lahir"] = $_POST["tanggal_lahir"];
                if(isset($_POST["line"]) && strlen($_POST["line"]) > 0) $data["line"] = $_POST["line"];
                if(isset($_POST["telepon"]) && preg_match("/^(\+\d+|0)?\d$/", $_POST["telepon"])) $data["telepon"] = $_POST["telepon"];
                if(isset($_POST["password"]) && strlen($_POST["password"]) > 0){
                    if(checkCredential($current_user["email"], $_POST["password"])){
                        if(isset($_POST["new_password"]) && isset($_POST["new_repassword"]) && strlen($_POST["new_password"]) > 0 && 
                           strlen($_POST["new_repassword"]) && $_POST["new_password"] == $_POST["new_repassword"]){
                            $data["password"] = $_POST["new_password"];
                        }else{
                            array_push($err_msg, "Password baru tidak saling cocok. Password tidak dapat diubah");
                        }
                    }else array_push($err_msg, "Password salah. Password tidak dapat diubah");
                }
                
                // print_r([isset($_FILES["foto"]), preg_match("/\.(png|jpg|jpeg)$/i", $_FILES["foto"]["name"]), $_FILES["foto"]["size"] <= 2 * 1024 * 1024]);
                // print_r($data);
                // print_r(count($data));
                // print_r($current_user);
                // die();

                if(count($data) > 0 && updateUser($current_user["id"], $data)){
                    if(isset($data["foto"])){
                        unlink($root_path . "/foto/" . $current_user["foto"]);
                        move_uploaded_file($_FILES["foto"]["tmp_name"], $root_path . "/foto/" . $data["foto"]);
                    }
                    $current_user = getCurrentUser(true);
                }else array_push($err_msg, "Profil tidak dapat diubah. Coba sekali lagi.");
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
                <li class="nav-item"><a class="nav-link active" href=".">Profil</a></li>
                <li class="nav-item"><a class="nav-link" href="cv.php">Curiculum Vitae</a></li>
                <li class="nav-item"><a class="nav-link" href="team.php">Tim</a></li>
            </ul>
        </div>
    </div>
    <div class="pt-4 text-center pb-4">
        <div class="d-inline-block col-11 col-lg-8">
            <div class="card d-inline-block text-left shadow-sm w-100">
                <div class="card-header clearfix">
                    <form action="" method="POST" enctype="application/x-www-form-urlencoded">
                        <button type="submit" class="btn btn-danger float-right" name="logout" value="true">Keluar</button>
                    </form>
                </div>
            <?php for($i=0; $i<count($err_msg); $i++){ ?>
                <div class="card-header bg-danger text-white"><?= $err_msg[$i] ?></div>
            <?php } ?>
                <div class="card-body">
                    <form id="frmUpdate" class="clearfix" action="" method="POST" enctype="multipart/form-data">
                        <div class="clearfix text-left">
                            <div class="text-left d-inline-block" style="padding:0em 1em;">
                                <div class="rounded-circle border d-inline-block overflow-hidden position-relative" style="width:5em; height:5em; background:#eeeeee;">
                                    <img src="../foto/<?= $current_user["foto"] ?>" id="prevPP" style="width:calc(100% + 2px); margin:-1px;"></img>
                                    <img id="newPP" class="rounded-circle position-absolute shadow d-none" style="width:calc(100% + 2px); margin:-1px; left:1.5em;"></img>
                                </div>
                            </div>
                            <div class="form-group d-inline-block text-left align-bottom" style="width:13em;">
                                <label for="filePP">Foto Profil [ 1 : 1 ]</label>
                                <input type="file" accept="image/*" class="form-control-file" id="filePP" name="foto">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="txtNama">Nama</label>
                            <input type="text" class="form-control" id="txtNama" name="nama" value="<?= $current_user["nama"] ?>">
                        </div>
                        <div class="form-group">
                            <label for="txtEmail">Email</label>
                            <input type="email" class="form-control" id="txtEmail" name="email" value="<?= $current_user["email"] ?>">
                        </div>
                        <div class="form-group">
                            <label for="txtLahir">Tanggal Lahir</label>
                            <input type="date" class="form-control" id="txtLahir" name="tanggal_lahir" value="<?= $current_user["tanggal_lahir"] ?>">
                        </div>
                        <div class="form-group">
                            <label for="txtLine">Line ID</label>
                            <input type="text" class="form-control" id="txtLine" name="line"value="<?= $current_user["line"] ?>">
                        </div>
                        <div class="form-group">
                            <label for="txtPhone">Nomor Telefon</label>
                            <input type="tel" class="form-control" id="txtPhone" name="telepon"value="<?= $current_user["telepon"] ?>">
                        </div>
                        <div class="form-group">
                            <label for="txtPass">Password Lama</label>
                            <input type="password" class="form-control" id="txtPass" name="password">
                            <small id="emailHelp" class="form-text text-muted">
                                Masukan ini dibutuhkan apabila Anda ingin mengubah password Anda. Dengan tidak mengisi ini, masukan dibawah ini akan diabaikan.
                            </small>
                        </div>
                        <div class="form-group">
                            <label for="txtPass">Password Baru</label>
                            <input type="password" class="form-control" id="txtPass" name="new_password">
                        </div>
                        <div class="form-group">
                            <label for="txtRePass">Ketikan Password baru lagi</label>
                            <input type="password" class="form-control" id="txtRePass" name="new_repassword">
                        </div>
                        <p class="p-1" style="background:#eee">
                            Masukan apapun yang tidak berisi (kosong) atau tidak memenuhi kriteria akan diabaikan dan tidak mengubah informasi 
                            yang telah disimpan di dalam sistem. Masukan lainnya yang memenuhi kriteria akan tetap diproses.
                        </p>
                        <button type="submit" class="btn btn-primary float-right" name="update" value="true">Perbarui</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="../global.js"></script>
    <script>
        var newPP = document.getElementById("newPP");
        var filePP = document.getElementById("filePP");
        filePP.addEventListener("input", function(ev){
            if(filePP.files.length == 1 || (/\.(png|jpg|jpeg)$/i).test(filePP.files[0].name) || filePP.files[0].size <= 2 * 1024 * 1024){
                input2image(filePP, newPP);
                newPP.classList.remove("d-none");
            }
        });
    </script>
</body>
</html>