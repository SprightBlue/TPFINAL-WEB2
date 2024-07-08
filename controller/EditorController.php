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
            $this->verifyEntorno();
            $this->presenter->render("view/editorView.mustache", ['editorName' => $_SESSION["usuario"]["fullname"]]);
        }

        public function questionsView() {
            $this->verifyEditorSession();
            $this->verifyEntorno();
            $questions = $this->model->getQuestions();
            $this->presenter->render("view/questionsView.mustache", ["questions" => $questions]);
        }

        public function suggestedQuestionsView() {
            $this->verifyEditorSession();
            $this->verifyEntorno();
            $suggestedQuestions = $this->model->getSuggestedQuestions();
            $this->presenter->render("view/suggestedQuestionsView.mustache", ["suggestedQuestions" => $suggestedQuestions]);
        }

        public function suggestedQuestionView() {
            $this->verifyEditorSession();
            $this->verifyEntorno();
            $idSuggestion = isset($_GET["idSuggestion"]) ? $_GET["idSuggestion"] : null;
            $suggestedQuestion = $this->model->getSuggestedQuestion($idSuggestion);

            $categoryIsGeography = $suggestedQuestion["category"] == "Geografía";
            $categoryIsArt = $suggestedQuestion["category"] == "Arte";
            $categoryIsScience = $suggestedQuestion["category"] == "Ciencia";
            $categoryIsSport = $suggestedQuestion["category"] == "Deporte";
            $categoryIsEntertainment = $suggestedQuestion["category"] == "Entretenimiento";
            $categoryIsHistory = $suggestedQuestion["category"] == "Historia";

            $correctIs1 = $suggestedQuestion["correct"] == 1;
            $correctIs2 = $suggestedQuestion["correct"] == 2;
            $correctIs3 = $suggestedQuestion["correct"] == 3;
            $correctIs4 = $suggestedQuestion["correct"] == 4;

            $this->presenter->render("view/suggestQuestionView.mustache", [
                "suggestedQuestion" => $suggestedQuestion,
                "isEditor" => true,
                "action" => "/editor/acceptOrDenySuggestion",
                "categoryIsGeography" => $categoryIsGeography,
                "categoryIsArt" => $categoryIsArt,
                "categoryIsScience" => $categoryIsScience,
                "categoryIsSport" => $categoryIsSport,
                "categoryIsEntertainment" => $categoryIsEntertainment,
                "categoryIsHistory" => $categoryIsHistory,
                "correctIs1" => $correctIs1,
                "correctIs2" => $correctIs2,
                "correctIs3" => $correctIs3,
                "correctIs4" => $correctIs4
            ]);
        }

        public function acceptOrDenySuggestion() {
            $this->verifyEditorSession();
            $this->verifyEntorno();
            $idSuggestion = $_POST["idSuggestion"];
            if (isset($_POST["accept"])) {
                $suggestedQuestion = $this->model->getSuggestedQuestion($idSuggestion);
                $this->model->acceptSuggestion($suggestedQuestion);
            } elseif (isset($_POST["deny"])) {
                $this->model->denySuggestion($idSuggestion);
            }
            Redirect::to("/editor/suggestedQuestionsView");
        }

        public function acceptSuggestion($idSuggestion) {
            $this->verifyEditorSession();
            $this->verifyEntorno();
            $this->model->acceptSuggestion($idSuggestion);
            Redirect::to("/editor/suggestedQuestionsView");
        }

        public function denySuggestion($idSuggestion) {
            $this->verifyEditorSession();
            $this->verifyEntorno();
            $this->model->denySuggestion($idSuggestion);
            Redirect::to("/editor/suggestedQuestionsView");
        }

        public function createQuestionView() {
            $this->verifyEditorSession();
            $this->verifyEntorno();
            $this->presenter->render("view/createUpdateQuestionView.mustache", ["action" => "/editor/createQuestion", "submitText" => "Guardar"]);
        }

        public function createQuestion() {
            $this->verifyEditorSession();
            $this->verifyEntorno();
            $question = $_POST["question"];
            $category = $_POST["category"];
            $answer1 = $_POST["answer1"];
            $answer2 = $_POST["answer2"];
            $answer3 = $_POST["answer3"];
            $answer4 = $_POST["answer4"];
            $correct = $_POST["correct"];
            $this->model->createQuestion($question, $category, $answer1, $answer2, $answer3, $answer4, $correct);
            Redirect::to("/editor/questionsView");
        }

        public function updateQuestionView() {
            $this->verifyEditorSession();
            $this->verifyEntorno();
            $idQuestion = isset($_GET["idQuestion"]) ? $_GET["idQuestion"] : null;
            $question = $this->model->getQuestion($idQuestion);
            $answers = $this->model->getAnswers($idQuestion);
            $category = $question['category'];
            $correct = null;
            foreach ($answers as $index => $answer) {
                if ($answer['correct'] == 1) {
                    $correct = $index + 1;
                    break;
                }
            }
            $data = [
                        'action' => '/editor/updateQuestion',
                        'submitText' => 'Actualizar',
                        'question' => $question,
                        'answers' => $answers,
                        'category' => $category,
                        'correct' => $correct,
                        'categoryIsGeography' => $category == 'Geografía',
                        'categoryIsArt' => $category == 'Arte',
                        'categoryIsScience' => $category == 'Ciencia',
                        'categoryIsSport' => $category == 'Deporte',
                        'categoryIsEntertainment' => $category == 'Entretenimiento',
                        'categoryIsHistory' => $category == 'Historia',
                        'correctIs1' => $correct == 1,
                        'correctIs2' => $correct == 2,
                        'correctIs3' => $correct == 3,
                        'correctIs4' => $correct == 4
                    ];
            $this->presenter->render("view/createUpdateQuestionView.mustache", $data);
        }

        public function updateQuestion() {
            $this->verifyEditorSession();
            $this->verifyEntorno();
            if (isset($_POST["idQuestion"], $_POST["question"], $_POST["category"], $_POST["answer1"], $_POST["answer2"], $_POST["answer3"], $_POST["answer4"], $_POST["correct"])) {
                $idQuestion = $_POST["idQuestion"];
                $question = $_POST["question"];
                $category = $_POST["category"];
                $answer1 = $_POST["answer1"];
                $answer2 = $_POST["answer2"];
                $answer3 = $_POST["answer3"];
                $answer4 = $_POST["answer4"];
                $correct = $_POST["correct"];
                $this->model->updateQuestion($idQuestion, $question, $category, $answer1, $answer2, $answer3, $answer4, $correct);
                Redirect::to("/editor/questionsView");
            } else {
                Redirect::to("/editor/updateQuestionView?idQuestion=" . $_POST["idQuestion"]);
            }
        }

        public function deleteQuestion() {
            $this->verifyEditorSession();
            $this->verifyEntorno();
            $idQuestion = isset($_GET["idQuestion"]) ? $_GET["idQuestion"] : null;
            if ($idQuestion !== null) {
                $this->model->deleteAnswers($idQuestion);
                $this->model->deleteQuestion($idQuestion);
            }
            Redirect::to("/editor/questionsView");
        }

        public function reportedQuestionsView() {
            $this->verifyEditorSession();
            $this->verifyEntorno();
            $reportedQuestions = $this->model->getReportedQuestions();
            $this->presenter->render("view/reportedQuestionsView.mustache", ["reportedQuestions" => $reportedQuestions]);
        }

        public function ignoreReport() {
            $this->verifyEditorSession();
            $this->verifyEntorno();
            if(isset($_GET["idReport"])) {
                $idReport = $_GET["idReport"];
                $this->model->ignoreReport($idReport);
            }
            Redirect::to("/editor/reportedQuestionsView");
        }

        private function verifyEditorSession() {
            if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["userRole"] != "editor") {Redirect::to("/login/read");}  
        }

        private function verifyEntorno() {
            if (isset($_SESSION["entorno"])) {
                $idEmpresa = $_SESSION["entorno"]["idEmpresa"];
                $idUsuario = $_SESSION["usuario"]["id"];
                $currentTime = date("Y-m-d H:i:s");
                $result = $this->model->getEntorno($idEmpresa, $idUsuario, $currentTime);
                if (!$result) {$_SESSION["entorno"] = null;}
            } 
        }

    }
