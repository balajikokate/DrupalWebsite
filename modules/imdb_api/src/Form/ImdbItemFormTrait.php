<?php

namespace Drupal\imdb_api\Form;

use Drupal\image\Entity\ImageStyle;

trait ImdbItemFormTrait {

  public function getRenderDetailsForm(&$parent, $view_mode, $image_style) {
    $module_handler = \Drupal::moduleHandler();

    $parent['imdb_item_view_mode'] = [
      '#type' => 'select',
      '#title' => t('IMDB item type'),
      '#options' => [
        'teaser' => $this->t('Teaser'),
      ],
      '#default_value' => $view_mode,
    ];
    if ($module_handler->moduleExists('imagecache_external')) {
      $parent['imdb_item_image_style'] = [
        '#type' => 'select',
        '#title' => t('IMDB item type'),
        '#options' => $this->imageStyleOptions(),
        '#default_value' => $image_style,
      ];
    }
    else {
      $parent['imagecache_external_suggestion'] = [
        '#markup' => t('It is strongly recommended to install <a href=":url">%module</a> to be able to apply image styles to external IMDB images which will improve the site performance in comparison to loading large images from IMDB right away.', [
          '%module' => 'Imagecache External',
          ':url' => 'https://www.drupal.org/project/imagecache_external',
        ]),
      ];
    }
  }

  private function imageStyleOptions() {
    $options = [];

    $styles = ImageStyle::loadMultiple();
    foreach ($styles as $key => $style) {
      $options[$key] = $style->getName();
    }

    return $options;
  }
}
