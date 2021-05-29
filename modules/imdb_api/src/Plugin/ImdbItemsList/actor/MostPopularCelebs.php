<?php

namespace Drupal\imdb_api\Plugin\ActorsList\actor;

use Drupal\imdb_api\Annotation\ImdbItemsList;
use Drupal\imdb_api\Plugin\ImdbItemsList\BaseList;

/**
 * Defines an ImdbItemsList implementation.
 *
 * @ImdbItemsList(
 *   id = "most_popular_celebs",
 *   title = @Translation("Most popular celebs"),
 *   description = @Translation("List of the most popular celebs."),
 *   method = "listMostPopularCelebs",
 *   entityClass = "\Imdb\Entities\Actor",
 *   options = {
 *     "currentCountry" = "",
 *     "purchaseCountry" = "",
 *     "homeCountry" = "",
 *   },
 *   limit = "",
 *   shuffle = FALSE
 * )
 */
class MostPopularCelebs extends BaseList {}
