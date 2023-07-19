<?php 

$success = $error = $filled = "";
$username="";
// enter the commands
$conn = mysqli_connect('localhost', 'kavinarvind', 'pass@123', 'UGAC_election_assignment');
if (!$conn) {
    echo mysqli_connect_error();
}

if (isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $pass = mysqli_real_escape_string($conn, $_POST["pass"]);
    $hash = hash('sha256', $pass);
    $type = mysqli_real_escape_string($conn, "admin");
    $sql = "SELECT user_name, user_pwd, user_type FROM login WHERE user_name = '$username';";
    $r = mysqli_query($conn, $sql);
    $result = mysqli_fetch_all($r, MYSQLI_ASSOC);
    mysqli_free_result($r);
    if ($result[0]["user_type"] == $type) {
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

<?php if (!$success): ?>
        <!-- Admin login form -->
        <section id="admin-login-form" style="padding: 3%; " class="container">
            <h1 style="text-align: center;">Admin Log In form</h1>
            <form action="admin.php" method="POST" class="white" style="padding: 1rem;">
                <div class="form-group row">
                    <label for="username" class="col-sm-3 col-form-label">User Name:</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" name="username" id="username" placeholder="Username" required value="<?php echo $username ?>">
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

    <?php else: ?>
        <!-- Admin dashboard section -->
        <div class="container" id="admin-dashboard">
            <h1 style="text-align: center;">Admin Dashboard(Successfully logged in.)</h1>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Position</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($positions as $position): ?>
                    <tr>
                        <td><?php echo $position; ?></td>
                        <td>
                            <form action="admin_candidates.php" method="POST">
                                <input type="text" name="username" value="<?php echo $username ?>" hidden="true">
                                <input type="text" name="hash" value="<?php echo $hash ?>" hidden="true">
                                <input type="text" name="position" value="<?php echo $position; ?>" hidden="true">
                                <input type="submit" name="admin_submit" value="View Candidates"class="btn btn-primary">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <div class="container">
                <a href="candidatecreate.php"><button class="btn btn-primary">Add new candidate</button></a>
            </div>

        </div>
    <?php endif ?>

<?php include("footer.php") ?>
</html>
<?php mysqli_close($conn); ?>
