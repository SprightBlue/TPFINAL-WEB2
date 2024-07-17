<?php

    class EditorController {

        private $model;
        private $presenter;

        public function __construct($model, $presenter) {
            $this->model = $model;
            $this->presenter = $presenter;
        }

        public function editorView() {
            $this->verifyEditorSession();
            $this->verifySessionThirdParties();
            $this->presenter->render("view/editorView.mustache", ["user" => $_SESSION["usuario"]]);
        }

        public function questionsView() {
            $this->verifyEditorSession();
            $this->verifySessionThirdParties();
            $questions = $this->model->getQuestions();
            $this->presenter->render("view/questionsView.mustache", ["user" => $_SESSION["usuario"], "questions" => $questions]);
        }

        public function suggestedQuestionsView() {
            $this->verifyEditorSession();
            $this->verifySessionThirdParties();
            $suggestedQuestions = $this->model->getSuggestedQuestions();
            $this->presenter->render("view/suggestedQuestionsView.mustache", ["user" => $_SESSION["usuario"], "suggestedQuestions" => $suggestedQuestions]);
        }

        public function suggestedQuestionView() {
            $this->verifyEditorSession();
            $this->verifySessionThirdParties();
            $idSuggestion = isset($_GET["idSuggestion"]) ? $_GET["idSuggestion"] : null;
            $suggestedQuestion = $this->model->getSuggestedQuestion($idSuggestion);

            $categoryIsGeography = ($suggestedQuestion["category"] == "Geografía");
            $categoryIsArt = ($suggestedQuestion["category"] == "Arte");
            $categoryIsScience = ($suggestedQuestion["category"] == "Ciencia");
            $categoryIsSport = ($suggestedQuestion["category"] == "Deporte");
            $categoryIsEntertainment = ($suggestedQuestion["category"] == "Entretenimiento");
            $categoryIsHistory = ($suggestedQuestion["category"] == "Historia");

            $correctIs1 = ($suggestedQuestion["correct"] == 1);
            $correctIs2 = ($suggestedQuestion["correct"] == 2);
            $correctIs3 = ($suggestedQuestion["correct"] == 3);
            $correctIs4 = ($suggestedQuestion["correct"] == 4);

            $data = [
                        "user" => $_SESSION["usuario"], "suggestedQuestion" => $suggestedQuestion, 
                        "isEditor" => true, "action" => "/editor/acceptOrDenySuggestion",
                        "categoryIsGeography" => $categoryIsGeography, "categoryIsArt" => $categoryIsArt,
                        "categoryIsScience" => $categoryIsScience, "categoryIsSport" => $categoryIsSport,
                        "categoryIsEntertainment" => $categoryIsEntertainment, "categoryIsHistory" => $categoryIsHistory,
                        "correctIs1" => $correctIs1, "correctIs2" => $correctIs2,
                        "correctIs3" => $correctIs3, "correctIs4" => $correctIs4
                    ];
            $this->presenter->render("view/suggestQuestionView.mustache", $data);
        }

        public function acceptOrDenySuggestion() {
            $this->verifyEditorSession();
            $this->verifySessionThirdParties();
            $idSuggestion = $_POST["idSuggestion"];
            if (isset($_POST["accept"])) {
                $suggestedQuestion = $this->model->getSuggestedQuestion($idSuggestion);
                $this->model->acceptSuggestion($suggestedQuestion);
            } elseif (isset($_POST["deny"])) {
                $this->model->denySuggestion($idSuggestion);
            }
            Redirect::to("/editor/suggestedQuestionsView");
        }

        public function createQuestionView() {
            $this->verifyEditorSession();
            $this->verifySessionThirdParties();
            $this->presenter->render("view/createUpdateQuestionView.mustache", ["user" => $_SESSION["usuario"], "action" => "/editor/createQuestion", "submitText" => "Guardar"]);
        }

        public function createQuestion() {
            $this->verifyEditorSession();
            $this->verifySessionThirdParties();
            if (isset($_POST["submit"])) {
                $question = $_POST["question"];
                $category = $_POST["category"];
                $answer1 = $_POST["answer1"];
                $answer2 = $_POST["answer2"];
                $answer3 = $_POST["answer3"];
                $answer4 = $_POST["answer4"];
                $correct = $_POST["correct"];
                $this->model->createQuestion($question, $category, $answer1, $answer2, $answer3, $answer4, $correct);
            } 
            Redirect::to("/editor/questionsView");
        }

        public function updateQuestionView() {
            $this->verifyEditorSession();
            $this->verifySessionThirdParties();
            $idQuestion = isset($_GET["idQuestion"]) ? $_GET["idQuestion"] : null;
            $question = $this->model->getQuestion($idQuestion);
            $answers = $this->model->getAnswers($idQuestion);
            $category = $question["category"];
            $correct = null;
            foreach ($answers as $index => $answer) {
                if ($answer["correct"] == 1) {
                    $correct = $index + 1;
                    break;
                }
            }
            $data = [
                        "user" => $_SESSION["usuario"], "action" => "/editor/updateQuestion", "submitText" => "Actualizar",
                        "question" => $question, "answers" => $answers, "category" => $category, "correct" => $correct,
                        "categoryIsGeography" => ($category == "Geografía"), "categoryIsArt" => ($category == "Arte"),
                        "categoryIsScience" => ($category == "Ciencia"), "categoryIsSport" => ($category == "Deporte"),
                        "categoryIsEntertainment" => ($category == "Entretenimiento"), "categoryIsHistory" => ($category == "Historia"),
                        "correctIs1" => ($correct == 1), "correctIs2" => ($correct == 2),
                        "correctIs3" => ($correct == 3), "correctIs4" => ($correct == 4)
                    ];
            $this->presenter->render("view/createUpdateQuestionView.mustache", $data);
        }

        public function updateQuestion() {
            $this->verifyEditorSession();
            $this->verifySessionThirdParties();
            if (isset($_POST["submit"])) {
                $idQuestion = $_POST["idQuestion"];
                $question = $_POST["question"];
                $category = $_POST["category"];
                $answer1 = $_POST["answer1"];
                $answer2 = $_POST["answer2"];
                $answer3 = $_POST["answer3"];
                $answer4 = $_POST["answer4"];
                $correct = $_POST["correct"];
                $this->model->updateQuestion($idQuestion, $question, $category, $answer1, $answer2, $answer3, $answer4, $correct);
            }
            Redirect::to("/editor/questionsView");
        }

        public function deleteQuestion() {
            $this->verifyEditorSession();
            $this->verifySessionThirdParties();
            $idQuestion = isset($_GET["idQuestion"]) ? $_GET["idQuestion"] : null;
            if ($idQuestion !== null) {
                $this->model->deleteAnswers($idQuestion);
                $this->model->deleteQuestion($idQuestion);
            }
            Redirect::to("/editor/questionsView");
        }

        public function reportedQuestionsView() {
            $this->verifyEditorSession();
            $this->verifySessionThirdParties();
            $reportedQuestions = $this->model->getReportedQuestions();
            $this->presenter->render("view/reportedQuestionsView.mustache", ["user" => $_SESSION["usuario"], "reportedQuestions" => $reportedQuestions]);
        }

        public function ignoreReport() {
            $this->verifyEditorSession();
            $this->verifySessionThirdParties();
            $idReport = isset($_GET["idReport"]) ? $_GET["idReport"] : null;
            if($idReport !== null) {
                $this->model->ignoreReport($idReport);
            }
            Redirect::to("/editor/reportedQuestionsView");
        }

        private function verifyEditorSession() {
            if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["idRole"] != 2) {
                Redirect::to("/login/read");
            }  
        }

        
        private function verifySessionThirdParties() {
            if (isset($_SESSION["modoTerceros"])) {
                $currentTime = date("Y-m-d H:i:s");
                $session = $this->model->getSessionThirdParties($_SESSION["modoTerceros"]["idEnterprise"], $_SESSION["usuario"]["id"], $currentTime);
                if ($session == false) {
                    $_SESSION["modoTerceros"] = null;
                }
            } 
        }
        

    }
