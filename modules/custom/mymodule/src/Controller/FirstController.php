<?php

    namespace Drupal\mymodule\Controller;
    use Drupal\Core\Controller\ControllerBase;

    class FirstController extends ControllerBase{
        public function contentPage(){
            return [
                '#title'=>'Welcome To My Custom Module Page',
                '#markup'=>'<h2>This is our basic Page<h2>
                <p>welcome to my home page</p>' 
            ];
        }

    }
?>
