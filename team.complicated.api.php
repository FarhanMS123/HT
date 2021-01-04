<?php

    /**
     * 1. Check gid in user profile
     * 2.1 If filled, Check user email in team by id[gid] from `ketua`, `anggota 1`, `anggota 2`
     *      2.1.1 If exists, show group, user has group
     *      2.1.2 If not exists, show user has been kicked from the group, user hasn't group
     * 2.2 If not filled, check user email in any team.
     *      2.2.1 If exists, create request list
     *      2.2.2 If not exists, user hasn't group and non invited
     */
    function checkUserTeam($email){
        global $db;

        $email = holdQuotes($email);
        
        $db->query("SELECT * FROM `teams` WHERE `ketua`='$email' OR `anggota1`='$email' OR `anggota2`='$email'");
    }

    /**
     * 1. Check user by email from ketua, anggota 1, anggota 2
     * 2.1 If in each profile has same gid with team id, all user is registered
     * 2.2 If there is haven't same gid, show `waiting to be accepted` without show the `cv`
     */
    function checkTeamInfo($gid){
        //
    }

    /**
     * 
     */
    function createGroup($logo, $name, $lead, $member1, $member2){
        //
    }
    function destroyGroup($gid){
        //
    }

    // function assignUser_ToGroup($email, $gid){ // invite user to
    //     //
    // }
    // function unsignUser($email, $gid){ // group kick the user
    //     //
    // }
    // function assignUser_byUser($email, $gid){ // user accept the request
    //     //
    // }
    // function resignUser($email, $gid){ // user exit the group
    //     //
    // }
?>