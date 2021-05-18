<?php

    namespace Drupal\employee\Controller;

    use Drupal\Core\employee\Controller\ControllerBase;
    

    class EmployeeController extends  ControllerBase{
        public function createEmployee(){
            $form= \Drupal::formBuilder()->getForm('Drupal\employeee\Form\EmployeeForm');
            $renderform= \Drupal::service('renderer')->render($form);

            return [
                '#type'=>'markup',
                '#markup'=>'hello'
            ];
        }
    }

?>