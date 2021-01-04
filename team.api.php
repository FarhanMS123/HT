<?php

    function checkUserTeam($email){
        global $db;

        $email = holdQuotes($email);
        
        $req1 = $db->query("SELECT * FROM `teams` WHERE `ketua`='$email' OR `anggota1`='$email' OR `anggota2`='$email'");

        if($req1->num_rows == 1){
            return $req1->fetch_assoc();
        }else if($req1->num_rows == 0){
            return false;
        }else{
            throw new Exception("team-conflict");
        }
    }

    function checkTeamInfo($gid){
        global $db;

        $gid = (int) $gid;

        $req1 = $db->query("SELECT * FROM `teams` WHERE `id`='$gid'");
        if($req1->num_rows == 1){
            return $req1->fetch_assoc();
        }else if($req1->num_rows == 0){
            return false;
        }else{
            throw new Exception("team-conflict");
        }
    }

    /**
     * 
     */
    function createGroup($name, $lead, $member1, $member2){
        global $db;

        $name = holdQuotes($name);
        $lead = holdQuotes($lead);
        $member1 = holdQuotes($member1);
        $member2 = holdQuotes($member2);

        $q = "SELECT * FROM `teams` WHERE `nama` = '$name' OR `ketua` IN ('$lead', '$member1', '$member2') OR 
                `anggota1` IN ('$lead', '$member1', '$member2') OR `anggota2` IN ('$lead', '$member1', '$member2')";
        $req1 = $db->query($q);
        if($req1->num_rows == 0){
            $q = "SELECT * FROM `users` WHERE `email` IN ('$lead', '$member1', '$member2')";
            $req2 = $db->query($q);
            // print_r([$q, $req2]);
            // die();
            if($req2->num_rows == 3){
                return $db->query("INSERT INTO `teams`(`nama`, `ketua`, `anggota1`, `anggota2`) VALUES ('$name', '$lead', '$member1', '$member2')");
            }else{
                throw new Exception("user-not-found");
            }
        }else{
            // throw new Exception("team-conflict");
            return false;
        }
    }
    function updateGroup($gid, $data){ // array("key"=>"value")
        global $db;

        $gid = (int) $gid;
        $query = "UPDATE `teams` SET ";
        foreach($data as $key => $val){
            $key = holdQuotes($key);
            $val = holdQuotes($val);
            $query = $query . "`$key`='$val', ";
        }
        $query = substr($query, 0, -2) . " WHERE `id`='$gid'";
        // echo $query;

        return $db->query($query);
    }
    function destroyGroup($gid){
        global $db;

        $gid = (int) $gid;
        return $db->query("DELETE FROM `teams` WHERE `id`=$gid");
    }
?>