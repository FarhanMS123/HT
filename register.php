<?php
    include __DIR__ . "/init.php";
    $err_msg = array();

    if($current_user){
        header('Location: ./user/profile.php');
        die();
    }
    
    $isPOST = $_SERVER["REQUEST_METHOD"] == "POST";
    try{
        if($isPOST){
            if(isset($_POST["register"])){
                if((preg_match("/\.(png|jpg|jpeg)$/i", $_FILES["foto"]["name"]) && $_FILES["foto"]["size"] <= 2 * 1024 * 1024) &&
                strlen($_POST["nama"]) > 0 && preg_match("/^[\w\-\.]+@([\w\-]+\.)+[\w\-]{2,4}$/", $_POST["email"]) &&
                strlen($_POST["tanggal_lahir"]) > 0 && strlen($_POST["line"]) > 0 && preg_match("/^(\+\d+|0)?\d$/", $_POST["telepon"]) &&
                strlen($_POST["password"]) > 0 && $_POST["repassword"] == $_POST["password"]){
                    $filename = time() . "_" . rand(10000,99999) . "_" . onlyWordDigit($_POST["nama"]) . "." . pathinfo($_FILES["foto"]["name"])["extension"];
                    if(createUser($_POST["nama"], $filename, $_POST["email"], $_POST["tanggal_lahir"], $_POST["line"], $_POST["telepon"], $_POST["password"])){
                        move_uploaded_file($_FILES["foto"]["tmp_name"], $root_path . "/foto/" . $filename);
                        header('Location: login.php');
                        die();
                    }else{
                        array_push($err_msg, "Identitas sejenis (email) sudah terdaftarkan pada sistem dan tidak dapat dibuat ulang. Silahkan masuk atau gunakan identitas lainnya.");
                        fixFormsData();
                    }
                }else{
                    array_push($err_msg, "Formulir yang dikirim tidak sesuai. Silahkan isi kembali formulir dibawah ini.");
                    fixFormsData();
                }
            }else{
                array_push($err_msg, "Operasi tidak dikenali. Silakan isi data kembali melalui formulir ini.");
                fixFormsData();
            }
        }
    }catch(\Exception $err){
        array_push($err_msg, $err->getMessage());

        if($isPOST) fixFormsData();
    }

    function fixFormsData(){
        $_POST["nama"] = isset($_POST["nama"]) ? htmlspecialchars($_POST["nama"], ENT_QUOTES) : "";
        $_POST["email"] = isset($_POST["email"]) ? htmlspecialchars($_POST["email"], ENT_QUOTES) : "";
        $_POST["tanggal_lahir"] = isset($_POST["tanggal_lahir"]) ? htmlspecialchars($_POST["tanggal_lahir"], ENT_QUOTES) : "";
        $_POST["line"] = isset($_POST["line"]) ? htmlspecialchars($_POST["line"], ENT_QUOTES) : "";
        $_POST["telepon"] = isset($_POST["telepon"]) ? htmlspecialchars($_POST["telepon"], ENT_QUOTES) : "";
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Hackathon</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
</head>
<body class="p-0 m-0" style="background:#11181f;">
    <div class="w-100 text-center" style="margin-top:2em; margin-bottom:2em;">
        <div class="d-inline-block card text-left shadow" style="width:24em; max-width:calc(100% - 2em);">
        <?php for($i=0; $i<count($err_msg); $i++){ ?>
            <div class="card-header bg-danger text-white"><?= $err_msg[$i] ?></div>
        <?php } ?>
            <div class="card-body">
                <a href="login.php" class="float-right">or try to login</a>
                <h5 class="card-title">Register</h5>
                <form id="frmRegister" class="clearfix" action="register.php" method="POST" enctype="multipart/form-data">
                    <div class="clearfix text-center">
                        <div class="text-center d-inline-block" style="padding:0em 1em;">
                            <div class="rounded-circle border d-inline-block overflow-hidden" style="width:5em; height:5em; background:#eeeeee;">
                                <img id="prevPP" style="width:calc(100% + 2px); margin:-1px;"></img>
                            </div>
                        </div>
                        <div class="form-group d-inline-block text-left align-bottom" style="width:13em;">
                            <label for="filePP">Foto Profil [ 1 : 1 ]</label>
                            <input type="file" accept="image/*" class="form-control-file" id="filePP" name="foto">
                            <div class="invalid-feedback">Masukan foto wajah terbaik Anda dengan ukuran 1 : 1.</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="txtNama">Nama</label>
                        <input type="text" class="form-control" id="txtNama" name="nama"<?php if($isPOST){ ?> value="<?= $_POST["nama"] ?>"<?php } ?>>
                        <div class="invalid-feedback">Masukan nama Anda.</div>
                    </div>
                    <div class="form-group">
                        <label for="txtEmail">Email</label>
                        <input type="email" class="form-control" id="txtEmail" name="email"<?php if($isPOST){ ?> value="<?= $_POST["email"] ?>"<?php } ?>>
                        <div class="invalid-feedback">Masukan <i>email</i> yang masih aktif dengan format yang benar.</div>
                    </div>
                    <div class="form-group">
                        <label for="txtLahir">Tanggal Lahir</label>
                        <input type="date" class="form-control" id="txtLahir" name="tanggal_lahir"<?php if($isPOST){ ?> value="<?= $_POST["tanggal_lahir"] ?>"<?php } ?>>
                        <div class="invalid-feedback">Masukan tanggal lahir Anda.</div>
                    </div>
                    <div class="form-group">
                        <label for="txtLine">Line ID</label>
                        <input type="text" class="form-control" id="txtLine" name="line"<?php if($isPOST){ ?> value="<?= $_POST["line"] ?>"<?php } ?>>
                        <div class="invalid-feedback">Masukan Line ID Anda yang masih aktif.</div>
                    </div>
                    <div class="form-group">
                        <label for="txtPhone">Nomor Telefon</label>
                        <input type="tel" class="form-control" id="txtPhone" name="telepon"<?php if($isPOST){ ?> value="<?= $_POST["telepon"] ?>"<?php } ?>>
                        <div class="invalid-feedback">Masukan nomor telefon Anda.</div>
                    </div>
                    <div class="form-group">
                        <label for="txtPass">Password</label>
                        <input type="password" class="form-control" id="txtPass" name="password">
                        <div class="invalid-feedback">Masukan passowrd terbaik Anda dan jangan beri tahu siapapun.</div>
                    </div>
                    <div class="form-group">
                        <label for="txtRePass">Ketikan Password lagi</label>
                        <input type="password" class="form-control" id="txtRePass" name="repassword">
                        <div class="invalid-feedback">Masukan passowrd yang sama dengan data sebelumnya.</div>
                    </div>
                    <button type="submit" class="btn btn-primary float-right" name="register" value="true">Register</button>
                </form>
            </div>
        </div>
    </div>
    <script src="./global.js"></script>
    <script>
        var frmRegister = document.getElementById("frmRegister"),
            prevPP = document.getElementById("prevPP");
            filePP = document.getElementById("filePP");

        function checkForm(ev){
            var el, i;
            var mustPrevent=false;
            for(i=ev.target.elements.length - 1; i>=0; i--){
                el = ev.target.elements[i];
                switch(el.name){
                    case "foto":
                        el.classList.remove("is-invalid");
                        if(el.files.length == 0 || (/\.(png|jpg|jpeg)$/i).test(el.files[0].name) == false || el.files[0].size > 2 * 1024 * 1024){
                            el.classList.add("is-invalid");
                            el.focus();
                            mustPrevent = true;
                        }
                        break;
                    case "nama":
                        el.classList.remove("is-invalid");
                        if(el.value.length == 0){
                            el.classList.add("is-invalid");
                            el.focus();
                            mustPrevent = true;
                        }
                        break;
                    case "email":
                        el.classList.remove("is-invalid");
                        if(!emailValidation(el.value)){
                            el.classList.add("is-invalid");
                            el.focus();
                            mustPrevent = true;
                        }
                        break;
                    case "tanggal_lahir":
                        el.classList.remove("is-invalid");
                        if(el.value.length == 0){
                            el.classList.add("is-invalid");
                            el.focus();
                            mustPrevent = true;
                        }
                        break;
                    case "line":
                        el.classList.remove("is-invalid");
                        if(el.value.length == 0){
                            el.classList.add("is-invalid");
                            el.focus();
                            mustPrevent = true;
                        }
                        break;
                    case "telepon":
                        el.classList.remove("is-invalid");
                        if(/^(\+)?\d+$/.exec(el.value) == null){
                            el.classList.add("is-invalid");
                            el.focus();
                            mustPrevent = true;
                        }
                        break;
                    case "password":
                        el.classList.remove("is-invalid");
                        if(el.value.length == 0){
                            el.classList.add("is-invalid");
                            el.focus();
                            mustPrevent = true;
                        }
                        break;
                    case "repassword":
                        el.classList.remove("is-invalid");
                        if(el.value != $("#txtPass").val()){
                            el.classList.add("is-invalid");
                            el.focus();
                            mustPrevent = true;
                        }
                        break;
                }
            }
            if(ev && mustPrevent) ev.preventDefault();
        }
        frmRegister.addEventListener("submit", checkForm);

        filePP.addEventListener("input", function(ev){
            if(filePP.files.length == 1 || (/\.(png|jpg|jpeg)$/i).test(filePP.files[0].name) || filePP.files[0].size <= 2 * 1024 * 1024){
                input2image(filePP, prevPP);
            }
        });
    </script>
</body>
</html>