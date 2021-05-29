<?php

namespace Drupal\imdb_api\Plugin\ImdbItemsList;

use Drupal\imdb_api\Plugin\ImdbItemsListBase;

abstract class TopRatedList extends ImdbItemsListBase {

  protected $chartRatings = [];

  public function handleItem($item) {
    $id = $this->getPureId($item);
    $this->chartRatings[$id] = $item->chartRating;

    return $id;
  }

  private function getPureId($item) {
    if (preg_match('/(?>nm|tt)\d+/', $item->id, $matches)) {
      return $matches[0];
    }

    return FALSE;
  }

  public function getChartRating($id) {
    return $this->chartRatings[$id];
  }
}
