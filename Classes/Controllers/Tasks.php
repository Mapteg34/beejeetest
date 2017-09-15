<?php

namespace Mapt\Beejeetest\Controllers;

use Mapt\Beejeetest\Exceptions\AccessDenied;
use Mapt\Beejeetest\Exceptions\NotFound;
use Mapt\Beejeetest\Models\Task;
use Mapt\Beejeetest\PageController;

class Tasks extends PageController
{

    const MAX_IMG_WIDTH = 320;
    const MAX_IMG_HEIGHT = 230;

    private function drawDataTable()
    {

        $result = [
            "draw"            => $_GET["draw"],
            "recordsTotal"    => Task::selectCnt(),
            "recordsFiltered" => 0,
            "data"            => []
        ];

        $oderDir     = isset($_GET["order"][0]["dir"]) && $_GET["order"][0]["dir"] == "asc" ? "asc" : "desc";
        $orderColumn = "tasks.id";
        if ($_GET["order"][0]["column"] == 1) {
            $orderColumn = "tasks.created";
        } elseif ($_GET["order"][0]["column"] == 2) {
            $orderColumn = "u.login";
        } elseif ($_GET["order"][0]["column"] == 3) {
            $orderColumn = "u.email";
        } elseif ($_GET["order"][0]["column"] == 4) {
            $orderColumn = "tasks.text";
        } elseif ($_GET["order"][0]["column"] == 5) {
            $orderColumn = "tasks.completed";
        }

        $params = [
            "order"  => [$orderColumn => $oderDir],
            "select" => [
                "tasks.id",
                "tasks.created",
                "u.login",
                "u.email",
                "tasks.text",
                "tasks.completed",
                "tasks.image_path"
            ]
        ];

        if ($_GET["search"]["value"]) {
            $q                         = "%".$_GET["search"]["value"]."%";
            $params["filter"]          = [
                "LOGIC"                                                            => "OR",
                "%CAST(tasks.id AS TEXT)"                                          => $q,
                "%to_char(tasks.created,'DD.MM.YYYY HH:MI:SS')"                    => $q,
                "%u.login"                                                         => $q,
                "%u.email"                                                         => $q,
                "%tasks.text"                                                      => $q,
                "%CASE WHEN tasks.completed THEN 'Completed' ELSE 'Processed' END" => $q
            ];
            $result["recordsFiltered"] = Task::selectCnt($params, true);
        } else {
            $result["recordsFiltered"] = $result["recordsTotal"];
        }

        if ($_GET["length"] != - 1) {
            $params["limit"]  = $_GET["length"];
            $params["offset"] = $_GET["start"];
        }

        $tasks = Task::select($params, true);

        foreach ($tasks as $task) {
            $result["data"][] = [
                $task->id,
                date("d.m.Y H:i:s", strtotime($task->created)),
                $task->login,
                $task->email,
                $task->text,
                $task->completed ? "Completed" : "Processed",
                "<img src=\"".$task->image_path."\" />",
                user()->isAuthorized() && user()->is_admin ? "<a href=\"/tasks/edit/".$task->id."\">Edit</a>" : ""
            ];
        }

        echo json_encode($result);
        die();
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        if (isset($_GET["draw"])) {
            return $this->drawDataTable();
        }

        $this->addCss("//cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css");
        $this->addJs("//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js");
        $this->addJs("/assets/js/tasks.js");

        return app()->includeView("tasks");
    }

    /**
     * @return string
     */
    public function actionAdd()
    {

        $params = [];
        $this->setTitle("Add task");
        if ($_SERVER["REQUEST_METHOD"] == "POST" && @$_POST["formID"] == "addForm") {
            if (!@$_POST["text"]) {
                $params["error"] = "Text empty";
            } elseif (!@$_FILES["image"] || $_FILES["image"]["error"]) {
                $params["error"] = "Image not uploaded";
            } elseif (!in_array($_FILES["image"]["type"], ["image/png", "image/gif", "image/jpeg"])) {
                $params["error"] = "Invalid image format";
            } else {
                $task             = new Task();
                $task->text       = @$_POST["text"];
                $task->image_path = "loading";
                if (user()->isAuthorized()) {
                    $task->user_id = user()->id;
                }

                db()->transactionStart();
                if ($task->save()) {
                    $task->image_path = $this->uploadAndResize(
                        $task->id,
                        $_FILES["image"]["tmp_name"],
                        $_FILES["image"]["type"]
                    );
                    if ($task->image_path === false) {
                        db()->transactionRollback();
                        $params["error"] = "Image upload problem";
                    } elseif (!$task->save()) {
                        db()->transactionRollback();
                        $params["error"] = "Image path save problem";
                    } else {
                        $params["saved"] = true;
                        db()->transactionCommit();
                    }
                } else {
                    $params["error"] = "Fail add task";
                    db()->transactionRollback();
                }
            }
        }

        $this->addJs("/assets/js/taskedit.js");

        $params["maxsize"] = self::MAX_IMG_WIDTH."x".self::MAX_IMG_HEIGHT;

        return app()->includeView("addtask", $params);
    }

    /**
     * @param int $taskId
     * @param string $filePath
     * @param string $fileType
     *
     * @return string|bool
     */
    private function uploadAndResize(int $taskId, string $filePath, string $fileType)
    {
        $imgPath = $imgPath = "/uploads/tasks_images/".$taskId;
        if ($fileType == "image/png") {
            $imgPath .= ".png";
        } elseif ($fileType == "image/gif") {
            $imgPath .= ".gif";
        } elseif ($fileType == "image/jpeg") {
            $imgPath .= ".jpeg";
        }
        $imgABSPath = app()->appRoot()."/www".$imgPath;

        $imgSizes = @getimagesize($filePath);

        if ($imgSizes === false) {
            return false;
        }

        if ($imgSizes[0] > self::MAX_IMG_WIDTH || $imgSizes[1] > self::MAX_IMG_HEIGHT) {
            $k   = max(
                $imgSizes[0] / self::MAX_IMG_WIDTH,
                $imgSizes[1] / self::MAX_IMG_HEIGHT
            );
            $img = null;
            if ($fileType == "image/png") {
                $img = @imagecreatefrompng($filePath);
            } elseif ($fileType == "image/gif") {
                $img = @imagecreatefromgif($filePath);
            } elseif ($fileType == "image/jpeg") {
                $img = @imagecreatefromjpeg($filePath);
            }
            if ($img === false) {
                return false;
            }
            $newW     = floor($imgSizes[0] / $k);
            $newH     = floor($imgSizes[1] / $k);
            $newImage = @imagecreatetruecolor($newW, $newH);
            if ($newImage === false) {
                return false;
            }

            if (@imagecopyresampled(
                    $newImage, $img,
                    0, 0,
                    0, 0,
                    $newW, $newH,
                    $imgSizes[0], $imgSizes[1]
                ) === false) {
                return false;
            }
            $res = false;
            if ($fileType == "image/png") {
                $res = @imagepng($newImage, $imgABSPath);
            } elseif ($fileType == "image/gif") {
                $imgPath .= ".gif";
                $res     = @imagegif($newImage, $imgABSPath);
            } elseif ($fileType == "image/jpeg") {
                $imgPath .= ".jpeg";
                $res     = @imagejpeg($newImage, $imgABSPath);
            }
            if ($res === false) {
                return false;
            }
        } else {
            if (@move_uploaded_file($filePath, $imgABSPath) === false) {
                return false;
            }
        }

        return $imgPath;
    }

    /**
     * @return string
     * @throws AccessDenied
     * @throws NotFound
     */
    public function actionEdit()
    {

        if (!user()->isAuthorized() || !user()->is_admin) {
            throw new AccessDenied();
        }

        $route  = explode("/", $_SERVER["REQUEST_URI"]);
        $taskid = $route[3];
        $task   = Task::selectOne(["filter" => ["id" => $taskid]]);
        if (!$task) {
            throw new NotFound();
        }

        $this->setTitle("Edit task");

        $params = [];

        if ($_SERVER["REQUEST_METHOD"] == "POST" && @$_POST["formID"] == "editForm") {
            if (!@$_POST["text"]) {
                $params["error"] = "Text empty";
            } else {
                $task->text      = @$_POST["text"];
                $task->completed = @$_POST["completed"] == "Y";

                if ($_FILES["image"]["name"]) {
                    if ($_FILES["image"]["error"]) {
                        $params["error"] = "Image not uploaded";
                    } elseif (!in_array($_FILES["image"]["type"], ["image/png", "image/gif", "image/jpeg"])) {
                        $params["error"] = "Invalid image format";
                    } else {
                        $task->image_path = $this->uploadAndResize(
                            $task->id,
                            $_FILES["image"]["tmp_name"],
                            $_FILES["image"]["type"]
                        );
                        if ($task->image_path === false) {
                            $params["error"] = "Image upload problem";
                        }
                    }
                }

                if (!@$params["error"]) {
                    if ($task->save()) {
                        $params["edited"] = true;
                    } else {
                        $params["error"] = "Fail edit task";
                    }
                }
            }
        }

        $this->addJs("/assets/js/taskedit.js");

        $params["task"] = $task;

        $params["maxsize"] = self::MAX_IMG_WIDTH."x".self::MAX_IMG_HEIGHT;

        return app()->includeView("edittask", $params);
    }

    /**
     * @param string $route
     *
     * @return string
     */
    public function getRouteAction(string $route)
    {
        if ($route == "") {
            return "index";
        }

        $route  = explode("/", $route);
        $action = $route[0];
        unset($route[0]);
        $route = implode("/", $route);
        if ($route) {
            if ($action == "edit") {
                return "edit";
            } else {
                return "404";
            }
        }

        return $action;
    }

    public function actionPreview()
    {
        $text = $_POST["text"];
        echo json_encode(["state" => "success", "html" => Task::convert($text)]);
        die();
    }
}