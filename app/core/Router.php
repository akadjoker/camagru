<?php

class Router {
    public static function route($page) {
        $routes = [
            'home' => ['controller' => null, 'view' => 'home'],
            'register' => ['controller' => 'AuthController', 'method' => 'register'],
            'login' => ['controller' => 'AuthController', 'method' => 'login'],
            'confirm' => ['controller' => 'AuthController', 'method' => 'confirm'],
            'logout' => ['controller' => 'AuthController', 'method' => 'logout'],
        
            'unlike' => ['controller' => 'GalleryController', 'method' => 'unlike'],
            'like' => ['controller' => 'GalleryController', 'method' => 'like'],
            'comment' => ['controller' => 'GalleryController', 'method' => 'comment'],

         
            'delete_comment' => ['controller' => 'GalleryController', 'method' => 'deleteComment'],
            'delete_image' => ['controller' => 'GalleryController', 'method' => 'deleteImage'],


            'password_request' => ['controller' => 'PasswordController', 'method' => 'request'],
            'password_send' => ['controller' => 'PasswordController', 'method' => 'send'],
            'reset' => ['controller' => 'PasswordController', 'method' => 'reset'],
            'update_password' => ['controller' => 'PasswordController', 'method' => 'update'],
        
            'profile' => ['controller' => 'ProfileController', 'method' => 'index'],
            'gallery' => ['controller' => 'GalleryController', 'method' => 'index'],
            'image' => ['controller' => 'GalleryController', 'method' => 'viewImage'],
           
            'upload' => ['controller' => 'EditorController', 'method' => 'upload'],
            'editor' => ['controller' => 'EditorController', 'method' => 'index'],
            'delete_editor_image' => ['controller' => 'EditorController', 'method' => 'deleteImage'],


        ];
        

        if (!array_key_exists($page, $routes)) {
            $page = 'home';
        }

        $route = $routes[$page];

        if ($route['controller'] === null) {
            include "views/{$route['view']}.php";
        } else {
            require_once "controllers/{$route['controller']}.php";
            $controller = new $route['controller']();
            $method = $route['method'];
            if (method_exists($controller, $method)) {
                $controller->$method();
            } else {
                echo "Método não encontrado.";
            }
        }
    }
}
?>
