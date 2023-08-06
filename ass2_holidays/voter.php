<?php 
$success=$error="";
// enter the commands
$conn=mysqli_connect('localhost','kavinarvind','pass@123','UGAC_election_assignment');
if(!$conn){echo mysqli_connect_error();}

if(isset($_POST['submit'])){
    $username = mysqli_real_escape_string($conn,$_POST["username"]);
    $pass = mysqli_real_escape_string($conn,$_POST["pass"]);
    $hash = mysqli_real_escape_string($conn,hash('sha256',$_POST["pass"]));
    $type = mysqli_real_escape_string($conn,"voter");
    $sql= "SELECT user_name,user_pwd,user_type FROM login WHERE user_name = '$username';";
    $r = mysqli_query($conn,$sql);
    $result = mysqli_fetch_all($r, MYSQLI_ASSOC);
    mysqli_free_result($r);
    if( $result[0]["user_type"] == $type ){
        if($result[0]['user_pwd'] == $hash){
            $error='';
            $success="success";
        }
        else{
            $error="Recheck the password";
        }
    }
    else{
        $success="";
        if($result[0]['user_type']){
            $error= "Please go to ".$result[0]['user_type']." Log In";
        }
        else{
            $error= "Account does not seem to exist";
        }
    }
    $sql= "SELECT voted FROM login WHERE user_name = '$username';";
    $r = mysqli_query($conn,$sql);
    $result = mysqli_fetch_all($r, MYSQLI_ASSOC);
    mysqli_free_result($r);
    $voted=$result[0]["voted"];

}
if(isset($_POST['vote_submit']) && $voted==0){
    $sql= "SELECT * FROM candidate_details;";
    $conn=mysqli_connect('localhost','kavinarvind','pass@123','UGAC_election_assignment');
    $r = mysqli_query($conn,$sql);
    $result = mysqli_fetch_all($r, MYSQLI_ASSOC);
    mysqli_free_result($r);
    $username = mysqli_real_escape_string($conn,$_POST["username"]);
    foreach($result as $row){
        $x= $_POST["vote_".$row["candi_id"]];
        if($x){
            $sql= "UPDATE candidate_details SET ".$x."_votes = "."$x"."_votes + 1 WHERE candi_id = '".$row["candi_id"]."';";
            if(!mysqli_query($conn,$sql)){echo "Some error occured";};
    
        }
    }

    $sql= "UPDATE login SET voted= 1 WHERE user_name='$username';";
    if(!mysqli_query($conn,$sql)){echo "Some error occured";};
    $voted = 1;

    $sql= "SELECT * FROM NOTA;";
    $r = mysqli_query($conn,$sql);
    $result = mysqli_fetch_all($r, MYSQLI_ASSOC);
    mysqli_free_result($r);
    foreach($result as $nota_rows){
        $nota_id=$nota_rows["NOTA_id"];
        if(isset($_POST["vote_nota_"."$nota_id"])){
            $sql= "UPDATE NOTA SET votes = votes + 1 WHERE NOTA_id = '$nota_id';";
            if(!mysqli_query($conn,$sql)){echo "Some error occured";};    
        }

    }


}



?>
<!DOCTYPE html>
<?php include("header.php") ?>

<?php if(!$success): ?>
    <section id="voter login form" style="padding: 3%; " class="container">
        <h1 style="text-align: center;">Voter's Log In form</h1>
        <form action="voter.php" method="POST" class="white" style="padding: 1rem;">
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

    <div class="container" style="text-align: center;">
        Does not have an account?
        <a href="votercreate.php"><button type="button" class="btn btn-primary">Create a new account?</button></a>
        
    </div>
<?php else: ?>
    <h3>Successfully Logged in...</h3>
    <?php 
    $conn=mysqli_connect('localhost','kavinarvind','pass@123','UGAC_election_assignment');
    if(!$conn){echo mysqli_connect_error();}
    $sql= "SELECT user_name,user_pwd,user_type,voted FROM login WHERE user_name = '$username';";
    $r = mysqli_query($conn,$sql);
    $result = mysqli_fetch_all($r, MYSQLI_ASSOC);
    mysqli_free_result($r);
    $voted = $result[0]["voted"];
    ?>
    <?php if($voted): ?>
        <p>Sorry.. You have voted already.. You cannot vote more than once.</p>
    <?php else: ?>
        <section id="voting-section" class="container" style="text-align: center; margin: auto; padding: 1rem">
            <h1>Voting Section</h1>
            <form action="voter.php" method="POST">
            <input type="text" name="username" value="<?php echo $username ?>" hidden="true">
                <?php for ($i = 0; $i < count($positions); $i++): ?>
                    <div class="container card" style="padding: 1rem; margin-top: 1rem; background-color: grey;">
                    <?php $position = $positions[$i]; ?>
                    <h3  style="text-align: center;"><u><?php echo $position; ?></u></h3>
                    <?php
                    $sql = "SELECT * FROM candidate_details WHERE position='$position';";
                    $result = mysqli_query($conn, $sql);
                    ?>
                    <div class="card-group justify-content-center" style="margin: auto;">
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <div class="card text-center" style="max-width: 30rem; border-width: 1rem;">
                                <!-- Display candidate details here -->
                                <img src="<?php echo $row['image']; ?>" alt="Candidate Image" class="card-img-top">
                                <h4><?php echo $row['fname'] . ' ' . $row['lname']; ?></h4>
                                <p>Manifesto:                     <a href="<?php echo $row['manifesto']; ?>" download="manifesto">Download PDF</a></p>
                                <label for="vote_<?php echo $row['candi_id']; ?>">Vote:</label>
                                <select name="vote_<?php echo $row['candi_id']; ?>" id="vote_<?php echo $row['candi_id']; ?>">
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                    <option value="neutral">Neutral</option>
                                </select>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    <!-- Add NOTA option here -->
                    <?php 
                    $sql = "SELECT * FROM candidate_details WHERE position='$position';";
                    $result = mysqli_query($conn, $sql);
                     ?>
                    <?php if(count(mysqli_fetch_all($result))>1): ?>
                    <div class="container" style="max-width: 25rem; margin: auto;">
                        <input class="form-check-input" type="checkbox" name="<?php echo 'vote_nota_'.$positions_id[$i]; ?>" id="vote_nota_<?php echo $positions_id[$i]; ?>">
                        <label for="vote_nota_<?php echo $positions_id[$i]; ?>">NOTA</label>
                        
                    </div>
                    <?php endif; ?>
                    </div>
                <?php endfor; ?>
                <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#actual_vote_submit" aria-expanded="false" aria-controls="actual_vote_submit" onclick="preview()">
                    Submit
                </button>

                <div class="modal fade" id="actual_vote_submit" tabindex="-1" role="dialog" aria-labelledby="Confirm vote" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm the vote</h5>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to submit your votes?</p>
                        <div id="votesForReview"></div>
                            <script>
                                function preview(){
                                    let a=document.getElementById("vote_<?php echo $row['candi_id']; ?>").value;
                                    <?php for($i=0;$i<count($positions);$i++): ?>

                                    <?php endfor ?>
                                    document.getElementById("votesForReview").innerHTML=a;

                                }
                            </script>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
                        <input type="submit" id="actual_vote_submit" name="vote_submit" value="Submit" class="btn btn-primary">
                    </div>
                    </div>
                </div>
                </div>

            </form>
        </section>
    <?php endif ?>
<?php endif ?>
<?php include("footer.php") ?>

<script>
    // Function to display the selected votes in the modal
    function showVotesForReview() {
        // Clear any previous content in the modal
        $('#votesForReview').empty();

        // Loop through each select element and get the selected value
        $('select[name^="vote_"]').each(function() {
            const candidateName = $(this).closest('.card').find('h4').text();
            const selectedValue = $(this).val();

            // Append the selected vote to the modal content
            $('#votesForReview').append(`<p>${candidateName}: ${selectedValue}</p>`);
        });
    }
</script>


</html>

<?php mysqli_close($conn); ?>

