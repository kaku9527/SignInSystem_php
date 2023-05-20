<?php

require_once('signInSQL.php');

$json_data = array();

function getIPAddress() {
    //whether ip is from the share internet  
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    //whether ip is from the proxy  
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    //whether ip is from the remote address  
    else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    $ip = ($ip == "::1") ? "192.168.0.19" : $ip;
    return $ip;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $sql = "SELECT * FROM `userfile`;";

    $result = mysqli_query($connection, $sql);

    $data = array();
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $data[] = $row;
        // print("ClientIP: " . $row["ClientIP"] . "<br>");
        // print("UserName: " . $row["UserName"] . "<br>");
        // print("Password: " . $row["Password"] . "<br>");
        // print("UpdateTm: " . $row["UpdateTm"] . "<br>");
        // print("<br>");
    }

    mysqli_free_result($result);

    $json_data['sql'] = $sql;
    $json_data['result'] = $data;

} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = $_POST['username'];
    $password = $_POST['password'];
    $updatetm = $_POST['updatetm'];
    $ip = getIPAddress();

    $updatetm = str_replace("T", " ", $updatetm);
    $updatetm = $updatetm . ":00";

    $sql = "INSERT INTO `userfile` VALUES ('{$ip}','{$username}','{$password}','{$updatetm}')";

    $result = mysqli_query($connection, $sql);
    if ($result) {
        $json_data['result'] = mysqli_affected_rows($connection);
    } else {
        if (mysqli_errno($connection) == 1062) {
            $sql = "UPDATE `userfile` SET ";
            $sql .= "`ClientIP`='{$ip}',";
            $sql .= "`Password`='{$password}',";
            $sql .= "`UpdateTm`='{$updatetm}' ";
            $sql .= "WHERE `UserName`='{$username}';";

            $resultUpdate = mysqli_query($connection, $sql);
            $json_data['result'] = ($resultUpdate) ? 1 : 0;
        } else {
            $json_data['result'] = 0;
        }
    }

    $json_data['sql'] = $sql;

} else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {

    $username = $_GET['username'];
    $sql = "DELETE FROM `userfile` WHERE `UserName`='{$username}'";

    $result = mysqli_query($connection, $sql);
    $json_data['result'] = ($result) ? mysqli_affected_rows($connection) : 0;

    $json_data['sql'] = $sql;
}

mysqli_close($connection);

echo json_encode($json_data);
