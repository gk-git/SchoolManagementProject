<?php

require_once("./dbManagers/article-manager.php");
require_once("./dbManagers/person-manager.php");
require_once("./dbManagers/course-manager.php");
require_once("./dbManagers/grade-manager.php");
require_once("./dbManagers/student-manager.php");
require_once("./dbManagers/message-manager.php");


class Router
{
    public $db;
    
    public function __construct(PDO $db)
    {
        $this->db         = $db;
        $this->articleMan = new ArticleManager($db);
        $this->personMan  = new PersonManager($db);
        $this->courseMan  = new CourseManager($db);
        $this->gradeMan = new GradeManager($db);
        $this->studentMan = new StudentManager($db);
        $this->messageMan = new MessageManager($db);
        session_start();
    }
    
    public function getUrl()
    {
        return "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }
    
    public function getPathArray()
    {
        return explode('/', trim(parse_url($this->getUrl(), PHP_URL_PATH), "/"));
    }
    
    public function sqlToArray($sql)
    {
        return $sql->fetchAll();
    }
    
    public function route()
    {
        $path      = $this->getPathArray();
        $jsonArray = array();

        if ($path[0] == 'api') {
            
            
            if ($path[1] == "home") {
                if (isset($_GET["page"])) {
                    $page = $_GET["page"];
                    if ($page < 1) {
                        $page = 1;
                    }
                } else {
                    $page = 1;
                }
                
                $limit                 = $_GET["limit"];
                $data                  = $this->articleMan->getPageData($page, $limit);
                $success               = $this->articleMan->successPage($page, $limit);
                $previous              = $this->articleMan->previousPages($page, $limit);
                $next                  = $this->articleMan->nextPages($page, $limit);
                $error                 = $this->articleMan->getErrorPage($page, $limit);
                $lastPage              = $this->articleMan->getNumberOfPages($limit);
                $jsonArray['success']  = $success;
                $jsonArray['data']     = $data;
                $jsonArray['previous'] = $previous;
                $jsonArray['next']     = $next;
                $jsonArray['last']     = $lastPage;
                $jsonArray['error']    = $error;
            }
            
            else if ($path[1] == "articles") {

                if ($path[2] == "add") {

                    $title = $_POST['title'];
                    $text  = $_POST['text'];
                    $files = $_FILES['files'];
                    $this->articleMan->addArticle($title, $text, $files);
                    $success              = $this->articleMan->successAddArticle($title, $text, $files);
                    $error                = $this->articleMan->getErrorAddArticle($title, $text, $files);
                    $jsonArray['success'] = $success;
                    $jsonArray['error']   = $error;

                } else if ($path[2] == "edit") {

                    $id    = $_POST['id'];
                    $title = $_POST['title'];
                    $text  = $_POST['text'];
                    $this->articleMan->editArticle($id, $title, $text);
                    $success              = $this->articleMan->successEditArticle($title, $text);
                    $error                = $this->articleMan->getErrorEditArticle($title, $text);
                    $jsonArray['success'] = $success;
                    $jsonArray['error']   = $error;

                } else if ($path[2] == "delete") {
                    $id = $_POST['id'];
                    $this->articleMan->deleteArticle($id);
                } else {
                    $id                    = $_GET["id"];
                    $success               = $this->articleMan->successArticle($id);
                    $data                  = $this->articleMan->getArticleData($id);
                    $error                 = $this->articleMan->getErrorArticle($id);
                    $next                  = $this->articleMan->nextArticle($id);
                    $previous              = $this->articleMan->previousArticle($id);
                    $jsonArray['success']  = $success;
                    $jsonArray['data']     = $data;
                    $jsonArray['next']     = $next;
                    $jsonArray['previous'] = $previous;
                    $jsonArray['error']    = $error;
                }
                              
            } else if($path[1]=="course"){

                if($path[2]=="add"){
                    
                    $courseCode = $_POST['courseCode'];
                    $courseName = $_POST['courseName'];
                    $courseMaxGrade = $_POST['courseMaxGrade'];
                    $courseYear = $_POST['courseYear'];
                    $courseClass = $_POST['courseClass'];
                    $teacherId = $_POST['teacherId'];
                    $this->courseMan->addCourse($courseCode, $courseName, $courseMaxGrade, $courseYear, $courseClass, $teacherId );

                }else if($path[2]=="edit"){

                    $id = $_POST['id'];
                    $courseCode = $_POST['courseCode'];
                    $courseName = $_POST['courseName'];
                    $courseMaxGrade = $_POST['courseMaxGrade'];
                    $courseYear = $_POST['courseYear'];
                    $courseClass = $_POST['courseClass'];
                    $teacherId = $_POST['teacherId'];
                    $this->courseMan->editCourse($id,$courseCode, $courseName, $courseMaxGrade, $courseYear, $courseClass, $teacherId );

                }else if($path[2]=="delete"){
                    
                    $id = $_POST['id'];
                    $this->courseMan->deleteCourse($id);

                }else if($path[2]=="search"){

                    $id = $_POST['id'];
                    $courseCode = $_POST['courseCode'];
                    $courseName = $_POST['courseName'];
                    $courseMaxGrade = $_POST['courseMaxGrade'];
                    $courseYear = $_POST['courseYear'];
                    $courseClass = $_POST['courseClass'];
                    $teacherId = $_POST['teacherId'];

                    $data = $this->courseMan->search($id,$courseCode, $courseName, $courseMaxGrade, $courseYear, $courseClass, $teacherId);

                    $jsonArray['data']=$data;
                }


            } else if($path[1]=="grade"){

                if($path[2]=="add"){

                    $score = $_POST['score'];
                    $semester = $_POST['semester'];
                    $year = $_POST['year'];
                    $courseId = $_POST['courseId'];
                    $studentId = $_POST['studentId'];
                    $this->gradeMan->addGrade($score,$semester,$year,$courseId,$studentId);

                }else if($path[2]=="edit"){

                    $id = $_POST['id'];
                    $score = $_POST['score'];
                    $semester = $_POST['semester'];
                    $year = $_POST['year'];
                    $courseId = $_POST['courseId'];
                    $studentId = $_POST['studentId'];
                    $this->gradeMan->editGrade($id,$score,$semester,$year,$courseId,$studentId);

                }else if($path[2]=="delete"){

                    $id = $_POST['id'];
                    $this->gradeMan->deleteGrade($id);

                }else if($path[2]=="search"){

                   
                    $score = $_POST['score'];
                    $semester = $_POST['semester'];
                    $year = $_POST['year'];
                    $courseId = $_POST['courseId'];
                    $studentId = $_POST['studentId'];

                    $data = $this->gradeMan->search($score,$semester,$year,$courseId,$studentId);
                    $jsonArray['data'] = $data;
                }

            } else if ($path[1] == "person") {
                
                if ($path[2] == "add") {
                    
                    $id        = $_POST['id'];
                    $name      = $_POST['name'];
                    $lastName  = $_POST['lastName'];
                    $gender    = $_POST['gender'];
                    $email     = $_POST['email'];
                    $telephone = $_POST['telephone'];
                    $userType  = $_POST['userType'];
                    $username  = $_POST['username'];
                    $password  = $_POST['password'];
                    
                    $this->personMan->addPerson($id, $name, $lastName, $gender, $email, $telephone, $userType, $username, $password);
                    
                } else if ($path[2] == "edit") {
                    
                    $id        = $_POST['id'];
                    $name      = $_POST['name'];
                    $lastName  = $_POST['lastName'];
                    $gender    = $_POST['gender'];
                    $email     = $_POST['email'];
                    $telephone = $_POST['telephone'];
                    $userType  = $_POST['userType'];
                    $username  = $_POST['username'];
                    $password  = $_POST['password'];
                    
                    $this->personMan->editPerson($id, $name, $lastName, $gender, $email, $telephone, $userType, $username, $password);
                    
                } else if ($path[2] == "delete") {
                    
                    $id = $_POST['id'];
                    
                    $this->personMan->deletePerson($id);
                    
                } else if($path[2] == "info"){

                    if($path[3]=="id"){

                        $id        = $_POST['id'];
                        
                        $success=$this->personMan->personIdExists($id);
                        $data=$this->personMan->getPersonData($id);
                        $error=$this->personMan->getErrorPersonId($id);

                        $jsonArray['success']  = $success;
                        $jsonArray['data']     = $data;
                        $jsonArray['error']    = $error;

                    }  else if ($path[3]=="search"){


                        $id        = $_POST['id'];
                        $name      = $_POST['name'];
                        $lastName  = $_POST['lastName'];
                        $gender    = $_POST['gender'];
                        $email     = $_POST['email'];
                        $telephone = $_POST['telephone'];
                        $userType  = $_POST['userType'];
                        $username  = $_POST['username'];

                        $data=$this->personMan->search($id,$name, $lastName, $gender, $email, $telephone, $userType,$username);
                        $jsonArray['data']     = $data;

                    }
                    

                }else if($path[2]=="student"){

                    if($path[3]=="grades"){

                    //THE ID SHOULD BE TAKEN FROM THE SESSION AFTER TESTING
                    $id=$_GET['id'];

                    $data = $this->gradeMan->getStudentGrades($id);

                    $jsonArray['data']=$data;
                    }

                } else if($path[2]=="parent"){

                    if($path[3]=="children"){

                        //THE ID SHOULD BE TAKEN FROM THE SESSION AFTER TESTING
                        $id = $_GET['id'];

                        $data = $this->personMan->getChildren($id);

                        $jsonArray['data']=$data;

                    } else if($path[3]=="teachers"){

                        //THE ID SHOULD BE TAKEN FROM THE SESSION AFTER TESTING
                        $id = $_GET['id'];

                        $data = $this->personMan->getTeachers($id);

                        $jsonArray['data']=$data;


                } else if($path[2]=="teacher"){

                    if($path[3]=="students"){

                        //THE ID SHOULD BE TAKEN FROM THE SESSION AFTER TESTING
                        $id=$_GET['id'];

                        $data = $this->personMan->getStudents($id);

                        $jsonArray['data']=$data;

                    } 

                } else if($path[2]=="login"){

                    $username = $_POST['username'];
                    $password = $_POST['password'];

                    $success = $this->personMan->login($username,$password);
                    $error = $this->personMan->getErrorLogin($username,$password);

                    $jsonArray['success'] = $success;
                    $jsonArray['error'] = $error;

                } else if($path[2]=="logout"){

                    $this->personMan->lougout();

                } 
                
            }
            
            header('Content-Type: application/json');
            echo json_encode($jsonArray);
        }
        
        /**
         *  Front End Routes
         *
         */
        else {
            if ($path[0] == '') {
                require 'front/index.html';
                
            } else if ($path[0] == 'article') {
                require 'front/article.html';
                
            }
        }
        
        //
        
    }
    }
}
?> 
