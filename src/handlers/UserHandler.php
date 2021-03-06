<?php
namespace src\handlers;

use \src\models\User;
use \src\models\UserRelation;

use \src\handlers\PostHandler;

class UserHandler{
    public static function checkLogin(){
        if (!empty($_SESSION['token'])){
            $token = $_SESSION['token'];

            $data = User::select()->where('token', $token) -> one();
            if (count($data)> 0) {

                $loggedUser = new User();
                $loggedUser -> id = $data['id'];
                $loggedUser -> email = $data['email'];
                $loggedUser -> name = $data['name'];
                $loggedUser -> birthdate = $data['birthdate'];
                $loggedUser -> avatar = $data['avatar'];
                $loggedUser -> work = $data['work'];

                return $loggedUser;
            }
        }

        return false;
    }

    public static function verifyLogin($email, $password){
        $user = User::select()->where('email', $email)-> one();

        if ($user){
            if (password_verify($password, $user['password'])){
                $token = md5(time().rand(0, 9999).time());
            
                User::update()
                    ->set('token', $token)
                    ->where('email', $email)
                ->execute();

                return $token;
            }
        }
        return false;
    }

    public static function idExists($id){
        $user = User::select()->where('id', $id)-> one();
        return $user ? true : false;
    }

    public static function emailExists($email){
        $user = User::select()->where('email', $email)-> one();
        return $user ? true : false;
    }

    public static function getUser($id, $full = false){
        $data = User::select()->where('id', $id)-> one();

        if(!empty($data)){
            $user = new User();
            $user->id = $data['id'];
            $user->name = $data['name'];
            $user->city = $data['city'];
            $user->work = $data['work'];
            $user->avatar = $data['avatar'];
            $user->cover = $data['cover'];
            $user->birthdate = $data['birthdate'];
            $user->email = $data['email'];

            if($full){
                $user->followers = [];
                $user-> following = [];
                $user->photos = [];
                
                //followers:
                $followers = UserRelation::select()->where('user_to', $id)->get();
                foreach($followers as $follower){
                    $userData = User::select()->where('id', $follower['user_from'])->one();

                    $newUser = new User();
                    $newUser->id = $userData['id'];
                    $newUser->name = $userData['name'];
                    $newUser->avatar = $userData['avatar'];

                    $user->followers[] = $newUser;
                } 

                //following:
                $followings = UserRelation::select()->where('user_from', $id)->get();
                foreach($followings as $following){
                    $userData = User::select()->where('id', $following['user_to'])->one();

                    $newUser = new User();
                    $newUser->id = $userData['id'];
                    $newUser->name = $userData['name'];
                    $newUser->avatar = $userData['avatar'];

                    $user->following[] = $newUser;
                } 

                //Photos:
                $user->photos = PostHandler::getPhotosFrom($id);

                
            }

            return $user;
        }
        return false;
    }

    public static function addUser($name, $email, $password, $birthdate){
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $token = md5(time().rand(0, 9999).time());

        User::insert([
            'name' => $name,
            'email' => $email,
            'password' => $hash,
            'birthdate' => $birthdate,
            'token' => $token
        ])->execute();

        return $token;
    }

    public static function isFollowing($from, $to){
        $data = UserRelation::select()
            ->where('user_from', $from)
            ->where('user_to', $to)
        ->one();

        if($data){
            return true;
        }
        echo $data;
        return false;
    }

    public static function follow($from, $to){
        UserRelation::insert([  
            'user_to' => $to,
            'user_from' => $from
        ])->execute();
    }
    public static function unfollow($from, $to){
        UserRelation::delete()
            ->where('user_to', $to)
            ->where('user_from', $from)
        ->execute();
    }
    public static function searchUser($searchTerm){
        $users = [];

        $data = User::select()->where('name', 'like', '%'.$searchTerm.'%')->get();
        if(count($data)>0){
            foreach($data as $user){
                $newUser = new User();
                $newUser->id = $user['id'];
                $newUser->name = $user['name'];
                $newUser->avatar = $user['avatar'];

                $users[] = $newUser;
            }
        }

        return $users;

    }

    public static function compareUser($old, $new){
        $passo = false;
        if ($new->email && $new->name && $new->birthdate){
            if($new->email!=$old->email){
                if (!self::emailExists($new->email)){
                    $old->email = $new->email;
                    $passo = true;
                }
            }
            if ($old->name != $new->name){
                $old->name = $new->name;
                $passo = true;
            }
            if($old->birthdate != $new->birthdate){
                $old->birthdate = $new->birthdate;
                $passo = true;
            }
            
        } else{
            return false;
        }
        if ($new->work && $old->work != $new->work){
            $old->work = $new->work;
            $passo = true;
        }
        if ($new->city && $old->city != $new->city){
            $old->city = $new->city;
            $passo = true;
        }
        return $passo;
    }

    public static function update($user){
        if (empty($user->city)){
            $user->city = '';
        }
        if (empty($user->work)){
            $user->work = '';
        }

        User::update()
            ->set('name', $user->name)
            ->set('email', $user->email)
            ->set('birthdate', $user->birthdate)
            ->set('city', $user->city)
            ->set('work', $user->work)
            ->set('avatar', $user->avatar)
            ->set('cover', $user->cover)
            ->where('id', $user->id)
        ->execute();
    }
}