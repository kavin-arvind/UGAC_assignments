<?php 
$success=$error="";
$filled = 0;


// enter the commands
$conn=mysqli_connect('localhost','kavinarvind','pass@123','UGAC_election_assignment');
if(!$conn){echo mysqli_connect_error();}

// $results gives the results for $sql


if(isset($_POST['submit'])){
    $username = mysqli_real_escape_string($conn,$_POST["username"]);
    $pass = mysqli_real_escape_string($conn,$_POST["pass"]);
    $hash = mysqli_real_escape_string($conn,hash('sha256',$_POST["pass"]));
    $type = mysqli_real_escape_string($conn,"candidate");
    $sql= "SELECT user_name,user_pwd,user_type FROM login WHERE user_name = '$username';";
    $r = mysqli_query($conn,$sql);
    $result = mysqli_fetch_all($r, MYSQLI_ASSOC);
    mysqli_free_result($r);
    if( $result[0]["user_type"] == $type ){
        if($result[0]['user_pwd'] == $hash){
            $error='';
            $success="success";
            $sql= "SELECT fname, lname, image, manifesto, position FROM candidate_details WHERE user_name = '$username';";
            $r = mysqli_query($conn,$sql);
            $result = mysqli_fetch_all($r, MYSQLI_ASSOC);
            mysqli_free_result($r);
            if($result[0]["fname"]){
                $filled = 1;
            }
        }
        else{
            $error="Recheck the password";
        }
    }
    else{
        $success="";
        $error="Please go to ".$result[0]['user_type']." Log In";
    }
}
if(isset($_POST['info_submit'])){
    $fname= mysqli_real_escape_string($conn,$_POST["fname"]);
    $lname= mysqli_real_escape_string($conn,$_POST["lname"]);
    $username = mysqli_real_escape_string($conn,$_POST["username"]);
    $position = mysqli_real_escape_string($conn,$_POST["position"]);
    $sql= "SELECT candi_id FROM candidate_details WHERE user_name = '$username';";
    $r = mysqli_query($conn,$sql);
    $result = mysqli_fetch_all($r, MYSQLI_ASSOC);
    mysqli_free_result($r);
    $candi_id = $result[0]["candi_id"];
    $hash = mysqli_real_escape_string($conn,$_POST["hash"]);
    // for image upload
    $directory_img = "candi_image/";
    $uploadedimg = $_FILES['image']['name'];
    $filesimg=explode(".",$uploadedimg);
    $extensionimg = end($filesimg);
    $candi_img_name = $directory_img."candi".$candi_id.".".$extensionimg;
    // for manifesto upload
    $directory_pdf = "candi_manifesto/";
    $uploadedpdf = $_FILES['manifesto']['name'];
    $filespdf=explode(".",$uploadedpdf);
    $extensionpdf = end($filespdf);
    $candi_pdf_name = $directory_pdf."candi".$candi_id.".".$extensionpdf;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $candi_img_name) && move_uploaded_file($_FILES["manifesto"]["tmp_name"], $candi_pdf_name)) {
            echo "Informations Successfully updated";
            $sql= "UPDATE candidate_details SET fname='$fname',lname='$lname',image='$candi_img_name',manifesto='$candi_pdf_name',position='$position' WHERE user_name='$username'";
            mysqli_query($conn,$sql);        
          } else{
            echo "Error uploading file.";
          }
        
           
}


?>
<!DOCTYPE html>
<?php include("header.php") ?>

<?php if(!$success): ?>
    <div class="containter">
        <h3><?php echo $_GET["candi_created_now"] ?></h3>
    </div>
<section id="candidate login form" style="padding: 3%; " class="container">
    <h1 style="text-align: center;">Candidate's Log In form</h1>
    <form action="candidate.php" method="POST" class="white" style="padding: 1rem;">
        <div class="form-group row">
            <label for="username" class="col-sm-3 col-form-label">User Name:</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" name="username" id="username" placeholder="Use alphabets, numbers and symbols. eg- aBc123$*" required value="<?php echo $username ?>">
            </div>
        </div>
        <div class="form-group row">
            <label for="pass" class="col-sm-3 col-form-label">Enter your password:</label>
            <div class="col-sm-8">
                <input type="password" class="form-control" name="pass" id="pass" required>
            </div>
        </div> 
        <div style="color: red; font-size: small;" id="validation">
            <?php echo $error ?>
        </div>
        <div style="color: green; font-size: small;" id="validation">
            <?php echo $success ?>
        </div>
 
        <div style="text-align: center;">
                <input type="submit" name="submit" value="Submit" class="btn btn-primary">
        </div>

    </form>
</section>

<div class="container" style="text-align: center;">
    Does not have an account?
    <a href="candidatecreate.php"><button type="button" class="btn btn-primary">Create a new account?</button></a>    
</div>
<?php endif ?>
<?php if($success): ?>
    <section id="candidate loggedin form" style="padding: 3%; " class="container">
        <h3>Successfully logged in..</h3><br>
        <?php if(!$filled): ?>
        <h4>Please Fill in your details</h4>
        <form action="candidate.php" method="post" class="white" style="padding: 1rem;" enctype="multipart/form-data">
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
            <div class="form-group row">
                <div class="input-group">
                    <label for="image" class="input-group-text">Upload your image:</label>
                    <input type="file" class="form-control" name="image" id="image">
                </div>
                <div class="input-group">
                    <label for="manifesto" class="input-group-text">Upload your manifesto:</label>
                    <input type="file" class="form-control" name="manifesto" id="manifesto" accept="application/pdf">
                </div>
            </div>
            <div class="input-group mb-3" style="margin: auto;">
            <div class="input-group-prepend">
                <label class="input-group-text" for="position">Post:</label>
            </div>
                <select name="position" id="position" class="custom-select">
                    <?php for($i = 0; $i < count($positions); $i++){ ?>
                        <option value= "<?php echo $positions[$i]; ?>"><?php echo $positions[$i]; ?></option>
                    <?php } ?>
                </select>
            </div>
            <input type="submit" name="info_submit" value="Submit" class="btn btn-primary">
        </form>
        <?php else: ?>
            <?php 
                $sql= "SELECT * FROM candidate_details WHERE user_name = '$username';";
                $conn=mysqli_connect('localhost','kavinarvind','pass@123','UGAC_election_assignment');
                $r = mysqli_query($conn,$sql);
                $result = mysqli_fetch_all($r, MYSQLI_ASSOC);
                mysqli_free_result($r);
             ?>
            <div style="width: 50%; text-align: center; margin: auto;">
            <div class="card mb-3">

                <img src="<?php echo $result[0]['image']; ?>" class="card-img-top" alt="Candidate Image">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $result[0]['fname']." ".$result[0]['lname']; ?></h5>
                    <p class="card-text">Position: <?php echo $result[0]['position']; ?></p>
                    <p class="card-text">Manifesto:
                    <a href="<?php echo $result[0]['manifesto']; ?>" download="manifesto">Download PDF</a>
                    </p>
                </div>
            </div>
            <p style="text-align: end; color: red;">*Please contact the admin to edit or delete candidature.</p>
            </div>
        <?php endif ?>
    </section>

<?php endif ?>

<?php include("footer.php") ?>


</html>

<?php mysqli_close($conn); ?>