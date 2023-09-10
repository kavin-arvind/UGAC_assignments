<?php 
$sql= "SELECT * FROM `login`";
// enter the commands
$conn=mysqli_connect('localhost','kavinarvind','pass@123','UGAC_election_assignment');
if(!$conn){echo mysqli_connect_error();}
$r = mysqli_query($conn,$sql);
$result = mysqli_fetch_all($r, MYSQLI_ASSOC);
mysqli_free_result($r);
mysqli_close($conn);
// $results gives the results for $sql

?>

<!DOCTYPE html>

<?php include("header.php") ?>
<body   style="background-image: url('/images/background.jpg');">
    <div class="hero bg-secondary text-white text-center py-5">
        <h1>Welcome to Voter's Portal</h1>
        <p class="lead">Cast your vote and make your voice heard!</p>
    </div>



<?php include("footer.php") ?>

</body>
</html>