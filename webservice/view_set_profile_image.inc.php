<?php

/*** check if a file was submitted ***/
$file = "new_profile_image";

if(isset($_FILES[$file])) {
    // check if a file was uploaded
    if( is_uploaded_file($_FILES[$file]['tmp_name']) && 
        getimagesize($_FILES[$file]['tmp_name']) != false) {

        // get image info
        $size = getimagesize($_FILES[$file]['tmp_name']);
        $type = $size['mime'];
        $size = $size[3];
        $name = $_FILES[$file]['name'];
        $img = fopen($_FILES[$file]['tmp_name'], 'rb');

        echo $type;

        try {
            $query = "INSERT INTO user_images (profile_image, user_id) VALUES (?, ?)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(1, $img, PDO::PARAM_LOB);
            $stmt->bindParam(2, $active_user_id);
            $stmt->execute();
        }
        catch(PDOException $e) {
            response_exception($e->getMessage(), "Database Error1: Please try again!");
        }

        response_success("Profile image changed!");
    }
}
?>

<form enctype="multipart/form-data" action="index.php?view=set_profile_image" method="post">
  <input name="new_profile_image" type="file" /><br><br>
  <input type="submit" value="Upload profile image" />
</form>