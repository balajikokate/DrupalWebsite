<?php

namespace Drupal\custom_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'custom' Block.
 *
 * @Block(
 *   id = "custom_block",
 *   admin_label = @Translation("custom block"),
 *   category = @Translation("custom"),
 * )
 */
class CustomBlock extends BlockBase {


  /**
   * {@inheritdoc}
   */
  public function build() {
    $data= array(
      'name'=>'Balaji Kokate',
      'email'=>'bkkokate@gmail.com',
      'MobileNo'=>'7070707070',
      'insta_id'=>'balaji_kokate'
  );
    return [
      '#theme'=>'info-page',
      '#items'=>$data,
    ];
  }

}