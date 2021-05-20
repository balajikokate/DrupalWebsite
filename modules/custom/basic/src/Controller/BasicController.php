<?php
/**
 * place this file in src controller folder inside the lotus module
 */
namespace Drupal\basic\Controller;

use Drupal\Core\Controller\ControllerBase;

class BasicController extends ControllerBase{
    public function content(){
        return [
            '#type'=> 'markup',
            '#markup'=> $this->t('Welcome to my Website'),
        ];
    }
}