<?php

    namespace Drupal\custom_node\Controller;

    use Drupal\Core\Controller\ControllerBase;
    use Drupal\node\Entity\Node;

    class CustomNode extends ControllerBase{

        public function showContent(){
            $node = Node::create(['type'=>'article']);
            $node -> set('title',' My first Custom node');
           
            $node -> set('uid',1);
            $node -> status = 1;
            $node -> save();
            return drupal_set_message("node with nid".$node->id());     }
    }