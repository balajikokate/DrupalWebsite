<?php

        namespace Drupal\basic_page\Plugin\Block;

        use Drupal\Core\Block\BlockBase;

        class BasicPageBlock extends BlockBase{

            /**
             * {@inheritdoc}
             */
            public function build(){
                return [
                    '#theme'=>'info-page',
                    '#items'=>$data,
                ];
               
            }
        }