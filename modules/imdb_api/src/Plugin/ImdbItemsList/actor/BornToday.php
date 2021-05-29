<?php

namespace Drupal\imdb_api\Plugin\ImdbItemsList\actor;

use Drupal\imdb_api\Annotation\ImdbItemsList;
use Drupal\imdb_api\Plugin\ImdbItemsList\BaseList;

/**
 * Defines an ImdbItemsList implementation.
 *
 * @ImdbItemsList(
 *   id = "born_today",
 *   title = @Translation("Actors born today"),
 *   description = @Translation("List of actors who were born on specified date."),
 *   method = "listBornToday",
 *   entityClass = "\Imdb\Entities\Actor",
 *   options = {
 *     "day" = "",
 *     "month" = "",
 *   },
 *   limit = "",
 *   shuffle = FALSE
 * )
 */
class BornToday extends BaseList {}
