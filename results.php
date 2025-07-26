<?php
include "config.php";

// Check if results are published
$res = $conn->query("SELECT results_published FROM election_status WHERE id = 1");
$row = $res->fetch_assoc();

if (!$row || !$row['results_published']) {
    echo "<h2 style='color:red;'>Results are not published yet by the admin.</h2>";
    exit();
}

// Fetch results grouped by position
$sql = "SELECT c.id, c.firstname, c.lastname, p.name AS position, COUNT(v.id) AS vote_count
        FROM candidates c
        JOIN positions p ON c.position_id = p.id
        LEFT JOIN votes v ON c.id = v.candidate_id
        GROUP BY c.id, p.name
        ORDER BY p.id, vote_count DESC";

$result = $conn->query($sql);

$results = [];
$totalVotes = 0;

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $position = $row['position'];
        $results[$position][] = $row;
        $totalVotes += $row['vote_count'];
    }
}
?>

<h2>Election Results</h2>

<?php if (!empty($results)): ?>
  <?php foreach ($results as $position => $candidates): ?>
    <h3><?= htmlspecialchars($position) ?></h3>
    <table border="1" cellpadding="8" cellspacing="0" style="width:100%; border-collapse: collapse;">
      <thead style="background-color: #28a745; color: white;">
        <tr>
          <th>Candidate</th>
          <th>Votes</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $maxVotes = $candidates[0]['vote_count'];
        foreach ($candidates as $candidate):
          $isWinner = $candidate['vote_count'] == $maxVotes;
        ?>
          <tr>
            <td><?= htmlspecialchars($candidate['firstname'] . ' ' . $candidate['lastname']) ?></td>
            <td><?= $candidate['vote_count'] ?></td>
            <td><?= $isWinner ? '<span style="color:green;">Winner 🏆</span>' : 'Participant' ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <br>
  <?php endforeach; ?>

  <p><strong>Total Votes Cast:</strong> <?= $totalVotes ?></p>
<?php else: ?>
  <p>No votes recorded yet.</p>
<?php endif; ?>
