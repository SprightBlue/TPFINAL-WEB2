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
            $wonChallenges = $this->model->getWonChallenges($userId);
            $lostChallenges = $this->model->getLostChallenges($userId);
            $tiedChallenges = $this->model->getTiedChallenges($userId);
            $pendingChallenges = $this->model->getPendingChallenges($userId);
            $this->presenter->render("view/challengesView.mustache", ["wonChallenges"=>$wonChallenges, "lostChallenges"=>$lostChallenges, "tiedChallenges"=>$tiedChallenges, "pendingChallenges"=>$pendingChallenges]);
        }
    public function createChallenge()
    {
        $challengerId = $_POST['challenger_id'];
        $challengedId = $_POST['challenged_id'];

        // Crear el desafío
        $challengeId = $this->model->createChallenge($challengerId, $challengedId);

       Redirect::to("/play/read?challenge_id=$challengeId");
    }

    public function acceptChallenge()
    {
        $challengeId = $_POST['challenge_id'];
        $this->model->updateChallengeStatus($challengeId, 'accepted');
       Redirect::to("/play/read?challenge_id=$challengeId");
    }

}