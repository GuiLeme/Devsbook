<?php
namespace src\handlers;

use \src\models\Post;
use \src\models\User;
use \src\models\UserRelation;

class PostHandler{
    
    public static function addPost($idUser, $type, $body){
        $body = trim($body);
        
        if(!empty($idUser) && !empty($body)){
            Post::insert([
                'id_user'=>$idUser,
                'type'=>$type,
                'created_at'=>date('Y-m-d H:i:s'),
                'body'=>$body
            ])->execute();
        }
    }

    public static function _postListToObject($postList, $loggedUserId){
        $posts = [];
        foreach($postList as $postItem){
            $newPost = new Post();
            $newPost->id = $postItem['id'];
            $newPost->type = $postItem['type'];
            $newPost->created_at = $postItem['created_at'];
            $newPost->body = $postItem['body'];
            $newPost->mine = false;

            if($postItem['id_user'] == $loggedUserId){
                $newPost->mine = true;
            }

            //4. Preencher as infos adicionais.
            $newUser = User::select()->where('id', $postItem['id_user'])->one();
            $newPost->user = new User();
            $newPost->user->id = $newUser['id'];
            $newPost->user->name = $newUser['name'];
            $newPost->user->avatar = $newUser['avatar'];

            // To Do: 4.1. Preencher informações de Like
            $newPost->likeCount = 0;
            $newPost->liked = false;


            // To Do: 4.2. Preencher informações de Comments
            $newPost->comments = [];

            $posts[] = $newPost;
        }

        return $posts;
    }

    public static function getUserFeed($idUser, $page, $loggedUserId){
        $perPage = 2;
        
        $postList = Post::select()
            ->where('id_user', $idUser)
            ->orderBy('created_at', 'desc')
            ->page($page, $perPage)
        ->get();

        $total = Post::select()
            ->where('id_user', $idUser)
        ->count();

        $totalPaginas = ceil($total/$perPage);

        
        //3. Transformar em objetos dos Models
        $posts = self::_postListToObject($postList, $loggedUserId);

        
        //5. Retornar o resultado.
        return [
            'posts' => $posts,
            'totalPaginas' => $totalPaginas,
        ];
    }

    public static function getHomeFeed($idUser, $page){
        $perPage = 2;

        //1. Pegar lista de usuários que eu sigo.
        $userList = UserRelation::select()->where('user_from', $idUser)->get();
        $users = [];
        foreach($userList as $userItem){
            $users[] = $userItem['user_to'];
        }
        $users[] = $idUser;


        //2. Pegar os posts ordenado por data.
        $postList = Post::select()
            ->where('id_user', 'in', $users)
            ->orderBy('created_at', 'desc')
            ->page($page, $perPage)
        ->get();

        $total = Post::select()
            ->where('id_user', 'in', $users)
        ->count();

        $totalPaginas = ceil($total/$perPage);

        
        //3. Transformar em objetos dos Models
        $posts = self::_postListToObject($postList, $idUser);

        
        //5. Retornar o resultado.
        return [
            'posts' => $posts,
            'totalPaginas' => $totalPaginas,
        ];
    }

    public static function getPhotosFrom($id){
        $photosData = Post::select()
            ->where('id_user', $id)
            ->where('type', 'photo')
        ->get();

        $photos = [];

        foreach($photosData as $photo){
            $newPhoto = new Post();
            
            $newPhoto->id = $photo['id'];
            $newPhoto->type = $photo['type'];
            $newPhoto->body = $photo['body'];
            $newPhoto->createdAt = $photo['created_at'];

            $photos[] = $newPhoto;
        }

        return $photos;
    }

}