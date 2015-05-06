<?php

$query = "SELECT profile_image FROM user_images WHERE user_id=:active_user_id";
$query_params = array(
    ":active_user_id" => $active_user_id
);

$stmt = exec_query($db, $query, $query_params, "Database Error1: Please try again!");
$result = $stmt->fetch();

if(!$result) {
    response_error("Error: Couldn't load image!");
}

response_success("Profile image successfully loaded!", "profile_image", base64_encode($result["profile_image"]));

?>