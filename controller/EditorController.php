<?php


class EditorController
{

    private $model;
    private $presenter;

    public function __construct($model, $presenter)
    {
        $this->model = $model;
        $this->presenter = $presenter;
    }

    public function editorView()
    {
        if (isset($_SESSION["usuario"]) && $_SESSION["usuario"]["userRole"] == "editor") {
            $this->presenter->render("view/editorView.mustache");
        } else {
            Redirect::to("/login/read");
        }
    }
    public function questionsView() {
        if (isset($_SESSION["usuario"]) && $_SESSION["usuario"]["userRole"] == "editor") {
            $questions = $this->model->getQuestions();
            $this->presenter->render("view/questionsView.mustache", ["questions" => $questions]);
        } else {
            Redirect::to("/login/read");
        }
    }
    public function suggestedQuestionsView()
    {
        if (isset($_SESSION["usuario"]) && $_SESSION["usuario"]["userRole"] == "editor") {
            $suggestedQuestions = $this->model->getSuggestedQuestions();
            $this->presenter->render("view/suggestedQuestionsView.mustache", ["suggestedQuestions" => $suggestedQuestions]);
        } else {
            Redirect::to("/login/read");
        }
    }

    public function suggestedQuestionView()
    {
        if (isset($_SESSION["usuario"]) && $_SESSION["usuario"]["userRole"] == "editor") {
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
        } else {
            Redirect::to("/login/read");
        }
    }
    public function acceptOrDenySuggestion()
    {
        if (isset($_SESSION["usuario"]) && $_SESSION["usuario"]["userRole"] == "editor") {
            $idSuggestion = $_POST["idSuggestion"];
            if (isset($_POST["accept"])) {
                $suggestedQuestion = $this->model->getSuggestedQuestion($idSuggestion);
                $this->model->acceptSuggestion($suggestedQuestion);
            } elseif (isset($_POST["deny"])) {
                $this->model->denySuggestion($idSuggestion);
            }
            Redirect::to("/editor/suggestedQuestionsView");
        } else {
            Redirect::to("/login/read");
        }
    }
    public function acceptSuggestion($idSuggestion)
    {
        if (isset($_SESSION["usuario"]) && $_SESSION["usuario"]["userRole"] == "editor") {
            $this->model->acceptSuggestion($idSuggestion);
            Redirect::to("/editor/suggestedQuestionsView");
        } else {
            Redirect::to("/login/read");
        }
    }

    public function denySuggestion($idSuggestion)
    {
        if (isset($_SESSION["usuario"]) && $_SESSION["usuario"]["userRole"] == "editor") {
            $this->model->denySuggestion($idSuggestion);
            Redirect::to("/editor/suggestedQuestionsView");
        } else {
            Redirect::to("/login/read");
        }
    }

    public function createQuestionView() {
        if (isset($_SESSION["usuario"]) && $_SESSION["usuario"]["userRole"] == "editor") {
            $this->presenter->render("view/createUpdateQuestionView.mustache", ["action" => "/editor/createQuestion", "submitText" => "Guardar"]);
        } else {
            Redirect::to("/login/read");
        }
    }
    public function createQuestion() {
        if (isset($_SESSION["usuario"]) && $_SESSION["usuario"]["userRole"] == "editor") {
            $question = $_POST["question"];
            $category = $_POST["category"];
            $answer1 = $_POST["answer1"];
            $answer2 = $_POST["answer2"];
            $answer3 = $_POST["answer3"];
            $answer4 = $_POST["answer4"];
            $correct = $_POST["correct"];

            $this->model->createQuestion($question, $category, $answer1, $answer2, $answer3, $answer4, $correct);
            Redirect::to("/editor/questionsView");
        } else {
            Redirect::to("/login/read");
        }
    }
    public function updateQuestionView()
    {
        if (isset($_SESSION["usuario"]) && $_SESSION["usuario"]["userRole"] == "editor") {
            if (isset($_SESSION["usuario"]) && $_SESSION["usuario"]["userRole"] == "editor") {
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
            } else {
                Redirect::to("/login/read");
            }
        }
    }


    public function updateQuestion() {
        if (isset($_SESSION["usuario"]) && $_SESSION["usuario"]["userRole"] == "editor") {
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
                // Agregar mensaje de error en la sesión
                $_SESSION["errorMessage"] = "Todos los campos son requeridos.";
                Redirect::to("/editor/updateQuestionView?idQuestion=" . $_POST["idQuestion"]);
            }
        } else {
            Redirect::to("/login/read");
        }
    }
    public function deleteQuestion() {
        if (isset($_SESSION["usuario"]) && $_SESSION["usuario"]["userRole"] == "editor") {
            $idQuestion = isset($_GET["idQuestion"]) ? $_GET["idQuestion"] : null;
            if ($idQuestion !== null) {
                $this->model->deleteAnswers($idQuestion);
                $this->model->deleteQuestion($idQuestion);
            }
            Redirect::to("/editor/questionsView");
        } else {
            Redirect::to("/login/read");
        }
    }

}

