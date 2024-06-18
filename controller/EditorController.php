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

    public function suggestedQuestionView($idSuggestion)
    {
        if (isset($_SESSION["usuario"]) && $_SESSION["usuario"]["userRole"] == "editor") {
            $suggestedQuestion = $this->model->getSuggestedQuestion($idSuggestion);
            $this->presenter->render("view/suggestQuestionView.mustache", ["suggestedQuestion" => $suggestedQuestion, "isEditor" => true, "action" => "/editor/acceptOrDenySuggestion"]);
        } else {
            Redirect::to("/login/read");
        }
    }
    public function acceptOrDenySuggestion()
    {
        if (isset($_SESSION["usuario"]) && $_SESSION["usuario"]["userRole"] == "editor") {
            $idSuggestion = $_POST["idSuggestion"];
            if (isset($_POST["accept"])) {
                $this->model->acceptSuggestion($idSuggestion);
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
    public function updateQuestionView($idQuestion) {
        if (isset($_SESSION["usuario"]) && $_SESSION["usuario"]["userRole"] == "editor") {
            $question = $this->model->getQuestion($idQuestion);
            $this->presenter->render("view/createUpdateQuestionView.mustache", ["question" => $question, "action" => "/editor/updateQuestion", "submitText" => "Actualizar"]);
        } else {
            Redirect::to("/login/read");
        }
    }

    public function updateQuestion() {
        if (isset($_SESSION["usuario"]) && $_SESSION["usuario"]["userRole"] == "editor") {
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
            Redirect::to("/login/read");
        }
    }

    public function deleteQuestion($idQuestion) {
        if (isset($_SESSION["usuario"]) && $_SESSION["usuario"]["userRole"] == "editor") {
            $this->model->deleteQuestion($idQuestion);
            Redirect::to("/editor/questionsView");
        } else {
            Redirect::to("/login/read");
        }
    }

}

