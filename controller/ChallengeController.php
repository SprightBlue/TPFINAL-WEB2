<?php


class ChallengeController
{

    private $model;
private $presenter;
    public function __construct($model, $presenter)
    {
        $this->model = $model;
        $this->presenter = $presenter;
    }


    public function readChallenges() {
        $userId = $_SESSION["usuario"]["id"];
        $allChallenges = $this->model->getAllChallenges($userId);
        $pendingChallenges = $this->model->getPendingChallenges($userId);
        $this->presenter->render("view/challengesView.mustache",["allChallenges"=>$allChallenges, "pendingChallenges"=>$pendingChallenges]);
    }
    public function createChallenge()
    {
        if (!isset($_SESSION["usuario"])) {
            Redirect::to("/login/read");
            return;
        }
        $challengerId = $_SESSION["usuario"]["id"];
        $challengedId = $_POST['challenged_id'];
        $challengeId = $this->model->createChallenge($challengerId, $challengedId);
        $_SESSION['challenge_id'] = $challengeId;
        Redirect::to("/play/read?challenge_id=$challengeId");
    }

    public function acceptChallenge()
    {
        $challengeId = $_POST['challengeId'];
        $this->model->updateChallengeStatus($challengeId, 'accepted');

        $_SESSION['challenge_id'] = $challengeId;
        Redirect::to("/play/read?challenge_id=$challengeId");
    }

}