<?php

    class ChallengeModel{
        
        private $database;

        public function __construct($database) {
            $this->database = $database;
        }

        public function getPendingChallenges($userId) {
            $stmt = $this->database->query("SELECT c.id, u1.username as challenger_username, u2.username as challenged_username 
                                            FROM challenge c 
                                            JOIN usuario u1 ON c.challenger_id = u1.id 
                                            JOIN usuario u2 ON c.challenged_id = u2.id 
                                            WHERE status = 'pending' AND challenged_id = :userId");
            $stmt->execute(array(":userId"=>$userId));
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getAllChallenges($userId) {
            $stmt = $this->database->query("SELECT * FROM (
                                                (SELECT c.id, c.challenger_score, c.challenged_score, u1.username as challenger_username, u2.username as challenged_username, 'won' as result
                                                FROM challenge c 
                                                JOIN usuario u1 ON c.challenger_id = u1.id 
                                                JOIN usuario u2 ON c.challenged_id = u2.id 
                                                WHERE winner_id = :userId)
                                            UNION ALL
                                                (SELECT c.id, c.challenger_score, c.challenged_score, u1.username as challenger_username, u2.username as challenged_username, 'lost' as result
                                                FROM challenge c 
                                                JOIN usuario u1 ON c.challenger_id = u1.id 
                                                JOIN usuario u2 ON c.challenged_id = u2.id 
                                                WHERE loser_id = :userId)
                                            UNION ALL
                                                (SELECT c.id, c.challenger_score, c.challenged_score, u1.username as challenger_username, u2.username as challenged_username, 'tied' as result
                                                FROM challenge c 
                                                JOIN usuario u1 ON c.challenger_id = u1.id 
                                                JOIN usuario u2 ON c.challenged_id = u2.id 
                                                WHERE is_tie = 1 AND (challenger_id = :userId OR challenged_id = :userId))
                                            ) AS derived_table
                                            ORDER BY id DESC");
            $stmt->execute(array(":userId"=>$userId));
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function updateChallengerScore($challengeId, $score) {
            $stmt = $this->database->query("UPDATE challenge 
                                            SET challenger_score = :score 
                                            WHERE id = :id");
            $stmt->execute(array(":id"=>$challengeId, ":score"=>$score));
        }

        public function updateChallengedScore($challengeId, $score) {
            $stmt = $this->database->query("UPDATE challenge 
                                            SET challenged_score = :score 
                                            WHERE id = :id");
            $stmt->execute(array(":id"=>$challengeId, ":score"=>$score));
        }

        public function updateChallengeStatus($challengeId, $status) {
            $stmt = $this->database->query("UPDATE challenge 
                                            SET status = :status 
                                            WHERE id = :id");
            $stmt->execute(array(":id"=>$challengeId, ":status"=>$status));
        }

        public function compareScores($challengeId) {
            $stmt = $this->database->query("SELECT challenger_score, challenged_score, challenger_id, challenged_id 
                                            FROM challenge 
                                            WHERE id = :id");
            $stmt->execute(array(":id"=>$challengeId));
            $scores = $stmt->fetch(PDO::FETCH_ASSOC);

            $winnerId = null;
            $loserId = null;
            $isTie = false;
            if ($scores["challenger_score"] > $scores["challenged_score"]) {
                $winnerId = $scores["challenger_id"];
                $loserId = $scores["challenged_id"];
            } else if ($scores["challenger_score"] < $scores["challenged_score"]) {
                $winnerId = $scores["challenged_id"];
                $loserId = $scores["challenger_id"];
            } else {
                $isTie = true;
            }

            if ($isTie) {
                $stmt = $this->database->query("UPDATE challenge 
                                                SET is_tie = :is_tie 
                                                WHERE id = :id");
                $stmt->execute(array(":id"=>$challengeId, ":is_tie"=>$isTie));
            } else if ($winnerId !== null && $loserId !== null) {
                $stmt = $this->database->query("UPDATE challenge 
                                                SET winner_id = :winner_id, loser_id = :loser_id 
                                                WHERE id = :id");
                $stmt->execute(array(":id"=>$challengeId, ":winner_id"=>$winnerId, ":loser_id"=>$loserId));
            }
        }

        public function isChallenger($challengeId, $userId): bool {
            $stmt = $this->database->query("SELECT challenger_id 
                                            FROM challenge 
                                            WHERE id = :id");
            $stmt->execute(array(":id"=>$challengeId));
            $challengerId = $stmt->fetchColumn();
            return ($challengerId == $userId);
        }

        public function createChallenge($challengerId, $challengedId) {
            $stmt = $this->database->query("INSERT INTO challenge (challenger_id, challenged_id) 
                                            VALUES (:challenger_id, :challenged_id)");
            $stmt->execute(array(":challenger_id"=>$challengerId, ":challenged_id"=>$challengedId));
            return $this->database->lastInsertId();
        }

        public function getChallengeStatus($challengeId) {
            $stmt = $this->database->query("SELECT status 
                                            FROM challenge 
                                            WHERE id = :id");
            $stmt->execute(array(":id"=>$challengeId));
            return $stmt->fetchColumn();
        }


        public function getSessionThirdParties($idEnterprise, $idUser, $currentTime) {
            $stmt = $this->database->query("SELECT *
                                            FROM sesionTerceros
                                            WHERE idEnterprise = :idEnterprise
                                            AND idUser = :idUser
                                            AND :currentTime BETWEEN startDate AND endDate");
            $stmt->execute(array(":idEnterprise"=>$idEnterprise, ":idUser"=>$idUser, ":currentTime"=>$currentTime));
            return ($stmt->rowCount() > 0) ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
        }
        

    }
