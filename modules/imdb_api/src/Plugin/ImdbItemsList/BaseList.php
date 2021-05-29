<?php

namespace Drupal\imdb_api\Plugin\ImdbItemsList;

use Drupal\imdb_api\Plugin\ImdbItemsListBase;

abstract class BaseList extends ImdbItemsListBase {

  public function handleItem($item) {
    return $this->getPureId($item);
  }

  private function getPureId($item) {
    if (preg_match('/(?>nm|tt)\d+/', $item, $matches)) {
      return $matches[0];
    }

    return FALSE;
  }
}
