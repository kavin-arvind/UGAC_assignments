<?php 
$conn=mysqli_connect('localhost','kavinarvind','pass@123','UGAC_election_assignment');
$username = $email = $pass1 = '';
$error = $success=  "";

if(isset($_POST['submit'])){
    $username = mysqli_real_escape_string($conn,$_POST["username"]);
    $email = mysqli_real_escape_string($conn,$_POST["email"]);
    $pass1 = mysqli_real_escape_string($conn,$_POST["pass1"]);
    $pass2 = mysqli_real_escape_string($conn,$_POST["pass2"]);
    $hash = mysqli_real_escape_string($conn,hash('sha256',$_POST["pass1"]));
    if($pass1!=$pass2){
        $error = "passwords don't match";
    }
    else{
        $error = "";
        try {
            $sql= "INSERT INTO `login`(`user_name`, `user_email`, `user_pwd`, `user_type`) VALUES ('$username','$email','$hash','voter')";
            // enter the commands
            if(!$conn){echo mysqli_connect_error();}
            mysqli_query($conn,$sql);

        }
        catch(Exception $e){
            $error = "Try using a different username or email";
        }    
    }
    if(!$error){
        $success = "success";
        header('Location: voter.php');
    }

}


?>
<!DOCTYPE html>
<?php include("header.php") ?>
<div class="container"  id="voter singnin form">
    <section style="padding: 3%; " class="container">
        <h1 style="text-align: center;">Voter's Sign In form</h1>
        <form action="votercreate.php" method="POST" class="white" style="padding: 1rem;">
            <div class="form-group row">
                <label for="username" class="col-sm-3 col-form-label">User Name:</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" name="username" id="username" placeholder="Use alphabets, numbers and symbols. eg- aBc123$*"  required value="<?php echo $username ?>">
                </div>
            </div>
            <div class="form-group row">
                <label for="email" class="col-sm-3 col-form-label">Email Adress:</label>
                <div class="col-sm-8">
                    <input type="email" class="form-control" name="email" id="email" placeholder="abc@def.com" required value="<?php echo $email ?>">
                </div>
            </div>
            <div class="form-group row">
                <label for="pass1" class="col-sm-3 col-form-label">Enter a password:</label>
                <div class="col-sm-8">
                    <input type="password" class="form-control" name="pass1" id="pass1"  required>
                </div>
            </div>
            <div class="form-group row">
                <label for="pass2" class="col-sm-3 col-form-label">Re-enter password:</label>
                <div class="col-sm-8">
                    <input type="password" class="form-control" name="pass2" id="pass2"  required>
                    <div style="color: red; font-size: small;" id="validation">
                        <?php echo $error ?>
                    </div>
                    <div style="color: green; font-size: small;" id="validation">
                        <?php echo $success ?>
                    </div>
                </div>
            </div>
            <div style="text-align: center;">
                <input type="submit" name="submit" value="Submit" class="btn btn-primary">
            </div>
        </form>
    </section>
</div>



<?php include("footer.php") ?>
<?php mysqli_close($conn); ?>

</html>