<?php 

$conn=mysqli_connect('localhost','kavinarvind','pass@123','UGAC_election_assignment');
if(!$conn){echo mysqli_connect_error();}


    // Function to check if a candidate has won or lost based on the given conditions
    function checkResult($yesVotes, $noVotes, $neutralVotes) {
        if ($yesVotes > $noVotes) {
            return "Elected";
        } elseif ($yesVotes + $neutralVotes > $noVotes) {
            return "Elected (Neutral Votes Added)";
        } else {
            return "Not Elected";
        }
    }

    // Function to compare candidates based on the given conditions for sorting
    function compareCandidates($candidate1, $candidate2) {
        if ($candidate1['yes_votes'] != $candidate2['yes_votes']) {
            return $candidate2['yes_votes'] - $candidate1['yes_votes']; // Sort by highest number of Yes votes
        } elseif ($candidate1['no_votes'] != $candidate2['no_votes']) {
            return $candidate1['no_votes'] - $candidate2['no_votes']; // Sort by lowest number of No votes (to put the most "NO" candidates at the bottom)
        } else {
            return $candidate2['neutral_votes'] - $candidate1['neutral_votes']; // Sort by highest number of Neutral votes (if Yes and No votes are the same)
        }
    }


?>
<!DOCTYPE html>
<head>
    <meta http-equiv="refresh" content="30">
</head>
<?php include("header.php") ?>
<h1 style="text-align: center;">Election Results</h1>
<?php for ($i = 0; $i < count($positions); $i++): ?>
        <?php
        $position = $positions[$i];
        $conn=mysqli_connect('localhost','kavinarvind','pass@123','UGAC_election_assignment');

        // Get all candidates for the current position
        $sql = "SELECT * FROM candidate_details WHERE position = '$position'";
        $result = mysqli_query($conn, $sql);
        $candidates = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // Sort the candidates based on the given conditions
        usort($candidates, 'compareCandidates');
        ?>
        <div class="container card text-center" style="padding: 1rem; margin: auto; margin-top: 1rem;">
                    <h2 style="text-align: center;"><u> <?php echo $position; ?></u></h2>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Candidate Name</th>
                        <th>Number of Yes</th>
                        <th>Number of No</th>
                        <th>Number of Neutral</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($j = 0; $j < count($candidates); $j++): ?>
                        <?php
                        $candidate = $candidates[$j];

                        // Calculate the total votes for the candidate
                        $totalVotes = $candidate['yes_votes'] + $candidate['no_votes'] + $candidate['neutral_votes'];

                        ?>

                        <tr>
                            <td><?php echo $candidate['fname'] . ' ' . $candidate['lname']; ?></td>
                            <td><?php echo $candidate['yes_votes']; ?></td>
                            <td><?php echo $candidate['no_votes']; ?></td>
                            <td><?php echo $candidate['neutral_votes']; ?></td>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
            <?php 
                    $sql_nota = "SELECT * FROM NOTA WHERE NOTA_id = '{$positions_id[$i]}';";
                    $result_nota = mysqli_query($conn, $sql_nota);
                    $nota = mysqli_fetch_all($result_nota, MYSQLI_ASSOC);

            ?>
            <p> <b>Number of NOTA votes:</b> <?php echo intval($nota[0]["votes"]) ?></p>
            <?php if(count($candidates)!=1): ?>
                <p><b>Winner:</b> <?php echo $candidates[0]["fname"]." ".$candidates[0]["lname"]; ?></p>
            <?php else: ?>
                <p>
                    <?php echo $candidates[0]["fname"]." ".$candidates[0]["lname"]; ?> is 
                    <?php echo checkResult($candidates[0]["yes_votes"],$candidates[0]["no_votes"],$candidates[0]["neutral_votes"]) ?>
                </p>
            <?php endif; ?>

        </div>
        
    <?php endfor; ?>

<?php include("footer.php") ?>
</html>

<?php mysqli_close($conn); ?>