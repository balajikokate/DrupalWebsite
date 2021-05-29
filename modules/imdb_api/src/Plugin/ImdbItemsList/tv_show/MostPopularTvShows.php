<?php

namespace Drupal\imdb_api\Plugin\ImdbItemsList\tv_show;

use Drupal\imdb_api\Annotation\ImdbItemsList;
use Drupal\imdb_api\Plugin\ImdbItemsList\MostPopularList;

/**
 * Defines an ImdbItemsList implementation.
 *
 * @ImdbItemsList(
 *   id = "most_popular_tv_shows",
 *   title = @Translation("Most popular TV shows"),
 *   description = @Translation("List of most popular TV shows."),
 *   entityClass = "\Imdb\Entities\TvShow",
 *   method = "getMostPopularTvShows",
 *   options = {
 *     "homeCountry" = "",
 *     "purchaseCountry" = "",
 *     "currentCountry" = "",
 *   },
 *   limit = "",
 *   shuffle = FALSE
 * )
 */
class MostPopularTvShows extends MostPopularList {}
