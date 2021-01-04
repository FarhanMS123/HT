<?php
    //

    /**
     * $current_user;
     * 
     * createUser(...);
     * getUserInfo(query[{}]);
     * updateUser(uid, ...);
     * 
     * createNewToken(uid);
     * getTokenInfo(query);
     * removeToken(token);
     * ----
     * XSS_Resolver();
     */

    // include "config.php";

    /*
        [SQL PROTECTION]
        \" ->> "
        \" -> \\\" ->> \"
        \\\" -> \\\\\" ->> \\"

        \\\" -> \\\\ \\\" ->> \\\"
        \\\\\" -> \\\\ \\\\ \\\" ->> \\ \\ \"
        \\\\\\\" -> \\\\ \\\\ \\\\ \\\" ->> \\\\\\\"

        why don't use html_encoding?
        html_encoding would convert ALL unicode characters to html symbols.
        It would be hard in frontend while wants to render html elements.
        So, these function only protects the injection script, such as
        slashes and apostroph (etc.)

        basically, this would only protect from sql injection.
    */
    function holdQuotes($str){ // \ ' " `
        $str = str_replace("\\", "\\\\", $str);
        $str = str_replace("'",  "\'", $str);
        $str = str_replace("\"", "\\\"", $str);
        $str = str_replace("`",  "\`", $str);
        return $str;
    }
    function reholdQuotes($str){ // \ ' " `
        $str = str_replace("\`",   "`", $str);
        $str = str_replace("\\\"", "\"", $str);
        $str = str_replace("\'",   "'", $str);
        $str = str_replace("\\\\", "\\", $str);
        return $str;
    }

    // ##### [DATABASE] ####################
    $db = new mysqli($db_servername, $db_username, $db_password, $db_dbname);
    if($db->connect_error){
        die("error-connection");
    }

    function createUser($nama, $foto, $email, $tanggal_lahir, $line, $telepon, $password){ // true | false
        global $db;
        global $web_ver;

        $email = holdQuotes($email);

        if($db->query("SELECT COUNT(`email`) AS 'cEmail' FROM `users` WHERE `email` = '$email'")->fetch_assoc()["cEmail"] == 0){
            $nama = holdQuotes($nama);
            $foto = holdQuotes($foto);
            $tanggal_lahir = holdQuotes($tanggal_lahir);
            $line = holdQuotes($line);
            $telepon = holdQuotes($telepon);
            $passhash = password_hash($password, PASSWORD_DEFAULT);
            
            $q = "INSERT INTO `users`(`nama`, `foto`, `email`, `tanggal_lahir`, `line`, `telepon`, `passhash`) 
                  VALUES ('$nama', '$foto', '$email', '$tanggal_lahir', '$line', '$telepon', '$passhash')";
            $req1 = $db->query($q);
            
            if($req1){
                return true;
            }else{
                return false;
            }
        }else{
            throw new Exception("email-exists");
        }
    }

    function getUserInfo($email){
        global $db;

        $email = holdQuotes($email);
        $req1 = $db->query("SELECT * FROM `users` WHERE `email` = '$email'");
        if($req1->num_rows == 1){
            return $req1->fetch_assoc();
        }else if($req1->num_rows == 0){
            return false;
        }else{
            throw new Exception("account-conflict");
        }
    }
    function getUser($uid){
        global $db;

        $uid = (int) $uid;
        $req1 = $db->query("SELECT * FROM `users` WHERE `id` = $uid");
        if($req1->num_rows == 1){
            return $req1->fetch_assoc();
        }else if($req1->num_rows == 0){
            return false;
        }else{
            throw new Exception("account-conflict");
        }
    }

    // function revalidateUser($email, $validation = null){
    //     global $web_ver;

    //     if($validation == null){
    //         $validation = (string) ((time() + rand(10000, 99999)) * rand(1, 9));
    //         $validation = md5($validation);
    //     }else{
    //         $validation = reholdQuotes($validation);
    //     }

    //     $user = getUserInfo($email);
    //     $req1 = updateUser($user["uid"], array("validation"=>$validation));

    //     if($req1){
    //         $html_handle = fopen("./resend.html", "r");
    //         $html = fread($html_handle,filesize("./resend.html"));
    //         fclose($html_handle);

    //         $html = str_replace("{{name}}", htmlspecialchars($user["name"]), $html);
    //         $html = str_replace("{{verf_link}}", $web_ver . $validation, $html);

    //         $header  = "MIME-Version: 1.0\r\n";
    //         $header .= "Content-type:text/html;charset=UTF-8\r\n";
    //         mail($email, "JustStore - Validate Your Account", $html, $header);

    //         return true;
    //     }else{
    //         return false;
    //     }
    // }
    // function validateUser($code){
    //     global $db;
        
    //     $code = holdQuotes($code);
    //     return $db->query("UPDATE `user_info` SET `validation`='1' WHERE `validation`='$code'");
    // }

    function updateUser($uid, $data){ // array("key"=>"value")
        global $db;

        $uid = (int) $uid;
        $query = "UPDATE `users` SET ";
        foreach($data as $key => $val){
            $key = holdQuotes($key);
            $val = holdQuotes($val);
            if($key == "password"){
                $key = "passhash";
                $val = password_hash($val, PASSWORD_DEFAULT);
            }
            $query = $query . "`$key`='$val', ";
        }
        $query = substr($query, 0, -2) . " WHERE `id`='$uid'";
        // echo $query;

        return $db->query($query);
    }

    function checkCredential($email, $password){
        global $db;
        // $user_sql = $db->query("SELECT * FROM `user_info` WHERE `email` = '$email'");
        $user = getUserInfo($email);
        if($user){
            $isValid = password_verify($password, $user["passhash"]);

            if($isValid){
                return $user;
            }else{
                return false;
            }
        }else{
            throw new Exception("user-not-exists");
        }
    }

    function login($email, $password, $activateRememberMe){
        // try{
            $user = checkCredential($email, $password);
            if($user){
                if($activateRememberMe) session_start(array("name"=>"u_sess", "cookie_lifetime" => 1 * 60 * 60));
                    else session_start(array("name"=>"u_sess"));
                $_SESSION["user"] = $user;
                return true;
            }else{
                return false;
            }
        // }catch(Exception $err){
        //     return false;
        // }
        setcookie('u_sess', null, -1, '/');
        return false;
    }

    // always try this action when do sensitive action.
    // read database over and over would make database overload.
    // so, save it in `$_SESSION["user"]`.
    function getCurrentUser($refresh=false){
        if(!isset($_SESSION["user"])) session_start(array("name"=>"u_sess"));
        if(isset($_SESSION["user"])){
            if($refresh) $_SESSION["user"] = getUser($_SESSION["user"]["id"]);
            return $_SESSION["user"];
        }else{
            setcookie('u_sess', null, -1, '/');
            session_destroy();
            return false;
        }
    }

    function logout(){
        setcookie('u_sess', null, -1, '/'); 
        $_SESSION["user"] = null;
        session_destroy();
        return true;
    }

    // https://github.com/FarhanMS123/Lite-LoginPHP
    // may be, if you are interest.
?>