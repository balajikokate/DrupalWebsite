<?php

namespace Drupal\imdb_api\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Defines an interface for IMDB items list plugins.
 */
interface ImdbItemsListInterface extends PluginInspectionInterface {

  public function getList();

}
