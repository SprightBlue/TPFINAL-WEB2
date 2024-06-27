<?php
class ChallengeModel{
    private $database;

    public function __construct($database) {
        $this->database = $database;
    }

    public function getPendingChallenges($userId) {
        // Implementa la consulta SQL para obtener los desafíos pendientes
    }

    public function getWonChallenges($userId) {
        // Implementa la consulta SQL para obtener los desafíos ganados
    }

    public function getLostChallenges($userId) {
        // Implementa la consulta SQL para obtener los desafíos perdidos
    }

// En tu controlador ChallengeController.php

    public function readChallenges() {
        $userId = $_SESSION["usuario"]["id"];
        $pendingChallenges = $this->model->getPendingChallenges($userId);
        $wonChallenges = $this->model->getWonChallenges($userId);
        $lostChallenges = $this->model->getLostChallenges($userId);
        $data = [
            "pendingChallenges" => $pendingChallenges,
            "wonChallenges" => $wonChallenges,
            "lostChallenges" => $lostChallenges
        ];
        $this->presenter->render("view/challengesView.mustache", $data);
    }

    public function updateChallengeStatus($challengeId, $status) {
        $stmt = $this->database->query("UPDATE challenge SET status=:status WHERE id=:id");
        $stmt->execute(array(":id"=>$challengeId, ":status"=>$status));
    }

    public function compareScores($challengeId) {
        $stmt = $this->database->query("SELECT challenger_score, challenged_score, challenger_id, challenged_id FROM challenge WHERE id=:id");
        $stmt->execute(array(":id"=>$challengeId));
        $scores = $stmt->fetch(PDO::FETCH_ASSOC);

        $winnerId = null;
        $loserId = null;
        if ($scores["challenger_score"] > $scores["challenged_score"]) {
            $winnerId = $scores["challenger_id"];
            $loserId = $scores["challenged_id"];
        } else if ($scores["challenger_score"] < $scores["challenged_score"]) {
            $winnerId = $scores["challenged_id"];
            $loserId = $scores["challenger_id"];
        }

        if ($winnerId !== null && $loserId !== null) {
            $stmt = $this->database->query("UPDATE challenge SET winner_id=:winner_id, loser_id=:loser_id WHERE id=:id");
            $stmt->execute(array(":id"=>$challengeId, ":winner_id"=>$winnerId, ":loser_id"=>$loserId));
        }
    }

    public function isChallenger($challengeId, $userId) {
        $stmt = $this->database->query("SELECT challenger_id FROM challenge WHERE id=:id");
        $stmt->execute(array(":id"=>$challengeId));
        $challengerId = $stmt->fetchColumn();

        return $challengerId == $userId;
    }

}
