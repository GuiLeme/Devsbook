<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use \src\models\User;

class ConfigController extends Controller {

    private $loggedUser;
    
    public function __construct(){
        $this -> loggedUser = UserHandler::checkLogin();
        if ($this -> loggedUser === false){
            $this -> redirect("/login");
        }
    }

    public function index() {
        $userInfo = UserHandler::getUser($this->loggedUser->id);

        if (empty($userInfo)){
            $this->redirect("/");
        }

        $this->render('config', [
            'loggedUser' => $this->loggedUser,
            'userInfo' => $userInfo,
        ]);
    }

    public function action(){
        $user = new User();

        $user->name = filter_input(INPUT_POST, 'name');
        $user->email = filter_input(INPUT_POST, 'email');
        $user->password = filter_input(INPUT_POST, 'password');
        $user->birthdate = filter_input(INPUT_POST, 'birthdate');
        $user->work = filter_input(INPUT_POST, 'work');
        $user->city = filter_input(INPUT_POST, 'city');

        $passou = false;
        //Avatar...
        if(isset($_FILES['avatar']) && !empty($_FILES['avatar']['name'])){
            $newAvatar = $_FILES['avatar'];
            

            if(in_array($newAvatar['type'], ['image/jpeg', 'image/jpg', 'image/png'])){
                $avatarName = $this->cutImage($newAvatar, 200, 200, 'media/avatars');

                $user-> avatar = $avatarName;
            }
        }

        //Cover...
        if(isset($_FILES['cover']) && !empty($_FILES['cover']['name'])){
            $newCover = $_FILES['cover'];

            if(in_array($newCover['type'], ['image/jpeg', 'image/jpg', 'image/png'])){
                $coverName = $this->cutImage($newCover, 850, 310, 'media/covers');
                $user-> cover = $coverName;
            }
        }

        if (empty($user->avatar)){
            $user->avatar = $this->loggedUser->avatar;
        }
        if (empty($user->cover)){
            $user->cover = $this->loggedUser->cover;
        }

        $user->id = $this->loggedUser->id;

        if (UserHandler::compareUser($this->loggedUser, $user)){
            UserHandler::update($user);
        }
        $this->redirect("/config");


    }

    private function cutImage($file, $w, $h, $folder){
        list($widthOrig, $heightOrig) = getimagesize($file['tmp_name']);

        $ratio = $widthOrig/$heightOrig;

        $newWidth = $w;
        $newHeight = $newWidth/$ratio;

        if ($newHeight < $h){
            $newHeight = $h;
            $newWidth = $newHeight*$ratio;
        }

        $x = $w - $newWidth;
        $y = $h - $newHeight;
        $x = $x < 0? $x/2:2;
        $y = $y < 0? $y/2:2;

        $finalImage = imagecreatetruecolor($w, $h);
        switch($file['type']){
            case 'image/jpeg':
            case 'image/jpg':
                $image = imagecreatefromjpeg($file['tmp_name']);
            break;
            case 'image/png':
                $image = imagecreatefrompng($file['tmp_name']);
            break;


        }
        imagecopyresampled(
            $finalImage, $image,
            $x, $y, 0,0,
            $newWidth, $newHeight, $widthOrig, $heightOrig
        );

        $fileName = md5(time().rand(0, 9999)).'.jpg';

        $var = imagejpeg($finalImage, $folder.'/'.$fileName);

        return $fileName;
    }
}