<?php

namespace Drupal\imdb_api\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a IMDB items list item annotation object.
 *
 * @see \Drupal\imdb_api\Plugin\ImdbItemsListManager
 * @see plugin_api
 *
 * @Annotation
 */
class ImdbItemsList extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The label of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

  /**
   * The name of entities to retrieve list of.
   *
   * @var string
   */
  public $entityClass;

  /**
   * The method to retrieve list.
   *
   * @var string
   */
  public $method;

  /**
   * The options to pass to REST API.
   *
   * @var array
   */
  public $options;

  /**
   * The max number of items to retrieve.
   *
   * @var string
   */
  public $limit;

  /**
   * Whether to shuffle list items or return as it is.
   *
   * @var bool
   */
  public $shuffle;

}
