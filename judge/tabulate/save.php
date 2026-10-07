<?php
session_start();
if (!isset($_SESSION["userId"])) {
    exit("Unauthorized session.");
}

if (isset($_POST['activeCategoryId']) && isset($_POST['scores'])) {
    include '../../connection/conn.php';

    $judgeId = $_SESSION["userId"];
    $categoryId = mysqli_real_escape_string($conn, $_POST['activeCategoryId']);
    $scoresData = $_POST['scores']; // array structure: scores[candId][criteriaId] = value

    $conn->begin_transaction();

    try {
        $stmtDelete = $conn->prepare("DELETE FROM tbl_scores WHERE user_id = ? AND category_id = ? AND cand_id = ?");
        $stmtInsert = $conn->prepare("INSERT INTO tbl_scores (category_id, criteria_id, user_id, cand_id, score_points) VALUES (?, ?, ?, ?, ?)");

        foreach ($scoresData as $candId => $criteriaScores) {
            $safeCandId = intval($candId);

            // Clear out old scores for this combo first
            $stmtDelete->bind_param("iii", $judgeId, $categoryId, $safeCandId);
            $stmtDelete->execute();

            // Insert new scores submitted from grid
            foreach ($criteriaScores as $criteriaId => $scoreValue) {
                if ($scoreValue === '' || !is_numeric($scoreValue)) continue;

                $safeCriteriaId = intval($criteriaId);
                $safeScore = floatval($scoreValue);

                $stmtInsert->bind_param("iiiid", $categoryId, $safeCriteriaId, $judgeId, $safeCandId, $safeScore);
                $stmtInsert->execute();
            }
        }

        $stmtDelete->close();
        $stmtInsert->close();
        $conn->commit();

        echo "Scores Successfully Saved!";
    } catch (Exception $e) {
        $conn->rollback();
        http_response_code(500);
        echo "Error saving scores: " . $e->getMessage();
    }

    $conn->close();
} else {
    http_response_code(400);
    echo "Invalid request parameters.";
}
?>