<?php

namespace Drupal\imdb_api;

use Drupal\Component\Utility\Unicode;

trait ImdbItemRenderable {

  public function getItemRenderable($entity, $type, $view_mode, $image_style, $truncate_size) {
    // @TODO decouple this method.
    $item = [
      '#theme' => 'imdb_item__' . $view_mode,
      '#title' => '',
      '#description' => '',
      '#image' => '',
    ];

    switch ($type) {
      case 'actor':
        $item['#title'] = $entity->getName();

        if ($mini_bios = $entity->getMiniBios()) {
          $mini_bio = reset($mini_bios);
          $item['#description'] = Unicode::truncate($mini_bio->text, $truncate_size, TRUE, TRUE);
        }
        break;
      default:
        $item['#title'] = $entity->getTitle();

        if (($overview_details = $entity->getOverviewDetails()) && isset($overview_details->plotSummary->text)) {
          $item['#description'] = Unicode::truncate($overview_details->plotSummary->text, $truncate_size, TRUE, TRUE);
        }
    }

    // Since adjacent text describes image alt may be empty.
    $image_url = $entity->getImage()->url;
    $item['#image'] = [
      '#type' => 'html_tag',
      '#tag' => 'img',
      '#attributes' => [
        'src' => $image_url,
        'alt' => '',
      ],
    ];
    if (\Drupal::moduleHandler()->moduleExists('imagecache_external')) {
      $item['#image'] = [
        '#theme' => 'imagecache_external',
        '#uri' => $image_url,
        '#style_name' => $image_style,
        '#alt' => '',
      ];
    }

    return $item;
  }

  public function renderItem($entity, $type, $view_mode, $image_style, $truncate_size) {
    $item = $this->getItemRenderable($entity, $type, $view_mode, $image_style, $truncate_size);

    return \Drupal::service('renderer')->render($item);
  }

}
