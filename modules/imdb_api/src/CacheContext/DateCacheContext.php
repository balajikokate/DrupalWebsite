<?php

namespace Drupal\imdb_api\CacheContext;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Cache\Context\CacheContextInterface;

/**
 * Class TodayCacheContext.
 */
class DateCacheContext implements CacheContextInterface {

  /**
   * {@inheritdoc}
   */
  public static function getLabel() {
    \Drupal::messenger()->addMessage('DateCacheContext');
  }

  /**
   * {@inheritdoc}
   */
  public function getContext() {
    return date('Y-m-d');
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheableMetadata() {
    return new CacheableMetadata();
  }

}
