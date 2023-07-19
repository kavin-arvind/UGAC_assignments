<?php 

$admin_success= "";
if(isset($_POST["edit_submit"])){
    $conn = mysqli_connect('localhost', 'kavinarvind', 'pass@123', 'UGAC_election_assignment');
    $admin_success=$_POST["admin_success"]."Edit";
    $fname=$_POST["fname"];$lname=$_POST["lname"];$email=$_POST["email"];$username=$_POST["username"];
    $sql= "UPDATE candidate_details SET fname='$fname',lname='$lname',user_email='$email' WHERE user_name='$username';";
    if(!mysqli_query($conn,$sql)){echo "Some error occured";};

    $sql= "SELECT * FROM candidate_details WHERE user_name = '$username';";
    $r = mysqli_query($conn,$sql);
    $result = mysqli_fetch_all($r, MYSQLI_ASSOC);
    mysqli_free_result($r);

    if(isset($_POST["edit_image"])){
        if (file_exists($result[0]["image"])) {
            if (unlink($result[0]["image"])) {echo "Image deleted successfully.";}
            else {echo "Failed to delete the image.";}
        }else{echo "image not found.";}  
        // for image upload
        $directory_img = "candi_image/";
        $uploadedimg = $_FILES['image']['name'];
        $filesimg=explode(".",$uploadedimg);
        $extensionimg = end($filesimg);
        $candi_img_name = $directory_img."candi".$result[0]["candi_id"].".".$extensionimg;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $candi_img_name)) {
            $sql= "UPDATE candidate_details SET image='$candi_img_name' WHERE user_name='$username'";
            mysqli_query($conn,$sql);        
        }else{echo "Error uploading file.";}

    }
    if(isset($_POST["edit_manifesto"])){
        if (file_exists($result[0]["manifesto"])) {
            if (unlink($result[0]["manifesto"])) {echo "Manifesto deleted successfully.";}
            else {echo "Failed to delete the manifesto.";}
        }else{echo "manifesto not found.";}
        // for manifesto upload
        $directory_pdf = "candi_manifesto/";
        $uploadedpdf = $_FILES['manifesto']['name'];
        $filespdf=explode(".",$uploadedpdf);
        $extensionpdf = end($filespdf);
        $candi_pdf_name = $directory_pdf."candi".$result[0]["candi_id"].".".$extensionpdf;
        if (move_uploaded_file($_FILES["manifesto"]["tmp_name"], $candi_pdf_name)) {
            $sql= "UPDATE candidate_details SET manifesto='$candi_pdf_name' WHERE user_name='$username'";
            mysqli_query($conn,$sql);        
        }else{echo "Error uploading file.";}

        
    }


}
if(isset($_POST["Edit"])){
    $conn = mysqli_connect('localhost', 'kavinarvind', 'pass@123', 'UGAC_election_assignment');
    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $admin_success=$_POST["admin_success"]."Edit";

}

if(isset($_POST["Delete"])){
    $conn = mysqli_connect('localhost', 'kavinarvind', 'pass@123', 'UGAC_election_assignment');
    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $admin_success=$_POST["admin_success"]."Delete";
    $sql = "SELECT * FROM candidate_details WHERE user_name = '$username';";
    $r = mysqli_query($conn, $sql);
    $result = mysqli_fetch_all($r, MYSQLI_ASSOC);
    if (file_exists($result[0]["image"])) {
        if (unlink($result[0]["image"])) {echo "Image deleted successfully.";}
        else {echo "Failed to delete the image.";}
    }else{echo "image not found.";}  
    $sql="DELETE FROM candidate_details WHERE user_name='$username';";
    if(mysqli_query($conn, $sql));
    if (file_exists($result[0]["manifesto"])) {
        if (unlink($result[0]["manifesto"])) {echo "Manifesto deleted successfully.";}
        else {echo "Failed to delete the manifesto.";}
    }else{echo "manifesto not found.";}
    $sql="DELETE FROM login WHERE user_name='$username';";
    if(mysqli_query($conn, $sql));
    header('Location: ' . $_SERVER['PHP_SELF']);
}

?>

<!DOCTYPE html>
<?php include("header.php") ?>

<?php if($admin_success): ?>
    <div class="container">
        <?php if(str_contains($admin_success,"Edit"))://Edit ?>
            <?php 
                $sql= "SELECT * FROM `candidate_details` WHERE user_name='$username'";
                $conn=mysqli_connect('localhost','kavinarvind','pass@123','UGAC_election_assignment');
                $r = mysqli_query($conn,$sql);
                $result = mysqli_fetch_all($r, MYSQLI_ASSOC);
                mysqli_free_result($r);
                $fname=$result[0]["fname"];$lname=$result[0]["lname"];$email=$result[0]["user_email"];
            ?>
            <h4>Please Fill in your details</h4>
            <form action="admin_editdel_candi.php" method="post" class="white" style="padding: 1rem;" enctype="multipart/form-data">
                <input type="text" value="admin_success" name="admin_success" hidden="true">
                <input type="text" name="username" value="<?php echo $username ?>" hidden="true">
                <input type="text" name="hash" value="<?php echo $hash ?>" hidden="true">
                    <div class="form-group row">
                        <div class="col">
                            <label for="fname">First Name:</label>
                            <input type="text" class="form-control" name="fname" id="fname" placeholder="First Name" required value="<?php echo $fname ?>">
                        </div>
                        <div class="col">
                            <label for="lname">Last Name:</label>
                            <input type="text" class="form-control" name="lname" id="lname" placeholder="Last Name" value="<?php echo $lname ?>">
                        </div>
                    </div>
                    <div class="col">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" name="email" id="email" placeholder="Email" value="<?php echo $email ?>">
                    </div>
                    <div class="form-group row">
                        <div class="input-group">
                            <input class="form-check-input" type="checkbox" name="edit_image" id="edit_image">
                            <label for="edit_image">Edit image</label>
                            <label for="image" class="input-group-text">Upload your image:</label>
                            <input type="file" class="form-control" name="image" id="image">
                        </div>
                        <div class="input-group">
                            <input class="form-check-input" type="checkbox" name="edit_manifesto" id="edit_manifesto">
                            <label for="edit_manifesto">Edit manifesto</label>
                            <label for="manifesto" class="input-group-text">Upload your manifesto:</label>
                            <input type="file" class="form-control" name="manifesto" id="manifesto" accept="application/pdf">
                        </div>
                    </div>
                    <input type="submit" name="edit_submit" value="Submit" class="btn btn-primary">
            </form>

        <?php elseif(str_contains($admin_success,"Delete"))://Delete ?>

        <?php endif ?>
    </div>
<?php else: ?>
    <div class="container">
        <h2>You are not permitted to view the content..</h2>
        <p>Only admin's can view it..</p>
    </div>
<?php endif ?>
<?php include("footer.php") ?>
</html>

<?php mysqli_close($conn); ?>
