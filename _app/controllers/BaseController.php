<?php
 

class BaseController {
 
    protected function render($view, $data = []) {
 
        extract($data);
        
  
        include_once VIEWS . 'layouts/default.php';
    }
    
 
    protected function redirect($url) {
        header("Location: $url");
        exit;
    }
}
