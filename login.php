<?php

require_once('signInSQL.php');

$json_data = array();
$account = isset($_POST['account']) ? $_POST['account'] : "";

$sql = "SELECT Password FROM `userfile` ";
$sql = $sql . "WHERE UserName = '" . $account . "';";

$result = mysqli_query($connection, $sql);

$data = array();
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $data[] = $row["Password"];
    // print("ClientIP: " . $row["ClientIP"] . "<br>");
    // print("UserName: " . $row["UserName"] . "<br>");
    // print("Password: " . $row["Password"] . "<br>");
    // print("UpdateTm: " . $row["UpdateTm"] . "<br>");
    // print("<br>");
}

mysqli_free_result($result);

mysqli_close($connection);

$json_data['result'] = $data;

$json_data['sql'] = $sql;

echo json_encode($json_data);