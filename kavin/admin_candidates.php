<?php 
    $success = $error = "";
    $username="";
    $conn = mysqli_connect('localhost', 'kavinarvind', 'pass@123', 'UGAC_election_assignment');
    if (!$conn) {echo mysqli_connect_error();}

    if (isset($_POST['admin_submit'])){
        $username = mysqli_real_escape_string($conn, $_POST["username"]);
        $hash = mysqli_real_escape_string($conn, $_POST["hash"]);
        $position = mysqli_real_escape_string($conn, $_POST["position"]);
        $sql = "SELECT user_name, user_pwd, user_type FROM login WHERE user_name = '$username';";
        $r = mysqli_query($conn, $sql);
        $result = mysqli_fetch_all($r, MYSQLI_ASSOC);
        mysqli_free_result($r);
        if ($result[0]["user_type"] == "admin") {
            if ($result[0]['user_pwd'] == $hash) {
                $error = '';
                $success = "success";
            } else {
                $error = "Recheck the password ";
            }
        } else {
            $success = "";
            $error = "Please go to " . $result[0]['user_type'] . " Log In";
        }
    }




?>

<!DOCTYPE html>
<?php include("header.php") ?>

<?php if($success): ?>
    <?php 
            $sql = "SELECT * FROM candidate_details WHERE position = '$position';";
            $conn = mysqli_connect('localhost', 'kavinarvind', 'pass@123', 'UGAC_election_assignment');
            $result = mysqli_query($conn, $sql);
            $candidates = mysqli_fetch_all($result, MYSQLI_ASSOC);  
    ?>
    
    <div class="container">
        <h1>View Candidates - <?php echo $position; ?></h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Image</th>
                    <th>Manifesto</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($candidates as $candidate): ?>
                    <tr>
                        <td><?php echo $candidate['fname']; ?></td>
                        <td><?php echo $candidate['lname']; ?></td>
                        <td><img src="<?php echo $candidate['image']; ?>" alt="Candidate Image" width="100"></td>
                        <td><a href="<?php echo $candidate['manifesto']; ?>" download="manifesto.pdf">Download Manifesto</a></td>
                        <td>
                            <form action="admin_editdel_candi.php" method="POST">
                                <input type="text" value="admin_success" name="admin_success" hidden="true">
                                <input type="text" value="<?php echo $candidate['user_name']; ?>" name="username" hidden="true">
                                <input type="submit" value="Edit" class="btn btn-primary" name="Edit">
                            </form>
                            <form action="admin_editdel_candi.php" method="POST">
                                <input type="text" value="admin_success" name="admin_success" hidden="true">
                                <input type="text" value="<?php echo $candidate['user_name']; ?>" name="username" hidden="true">
                                <input type="submit" value="Delete" class="btn btn-danger" name="Delete">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="container">
        <h2>You are not permitted to view the content..</h2>
        <p>Only admin's can view it..</p>
    </div>
<?php endif ?>


<?php include("footer.php") ?>
</html>