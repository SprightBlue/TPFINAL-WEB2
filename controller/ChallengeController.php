<?php


class ChallengeController
{

    private $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function createChallenge()
    {
        $challengerId = $_POST['challenger_id'];
        $challengedId = $_POST['challenged_id'];

        // Crear el desafío
        $challengeId = $this->model->createChallenge($challengerId, $challengedId);

        // Redirigir al usuario a la vista de juego con el ID del desafío como un parámetro GET
       Redirect::to("/play/read?challenge_id=$challengeId");
    }

    public function acceptChallenge()
    {
        // Obtener el ID del desafío de la solicitud
        $challengeId = $_POST['challenge_id'];

        // Actualizar el estado del desafío a 'accepted'
        $this->model->updateChallengeStatus($challengeId, 'accepted');

        // Redirigir al usuario a la vista de juego con el ID del desafío como un parámetro GET
       Redirect::to("/play/read?challenge_id=$challengeId");
    }

    public function resolveChallenge()
    {
        // Obtener el ID del desafío de la solicitud
        $challengeId = $_POST['challenge_id'];

        // Comparar los puntajes de los jugadores
        $this->model->compareScores($challengeId);

        // Actualizar el estado del desafío a 'resolved'
        $this->model->updateChallengeStatus($challengeId, 'resolved');

        // Redirigir al usuario a la vista del lobby
        Redirect::to("/lobby/read");
    }
}