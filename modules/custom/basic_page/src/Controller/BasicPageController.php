<?php

    namespace Drupal\basic_page\Controller;
    use Drupal\Core\Controller\ControllerBase;

    class BasicPageController extends ControllerBase{
        public function basicPage(){
            return [
                '#title'=>'Welcome To My Custom Module Page',
                '#markup'=>'<h2>This is our basic Page<h2>
                <p>welcome to my home page</p>' 
            ];
        }
        public function info(){
            $data= array(
                'name'=>'Balaji Kokate',
                'email'=>'bkkokate@gmail.com',
                'MobileNo'=>'7070707070',
                'insta_id'=>'balaji_kokate'
            );
           return[
                '#title'=>'Welcome',
                '#theme'=>'info-page',
                '#items'=>$data,
            ];
        }

    }

?>
