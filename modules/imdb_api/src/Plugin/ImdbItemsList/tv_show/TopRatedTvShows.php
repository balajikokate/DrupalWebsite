<?php

namespace Drupal\imdb_api\Plugin\ImdbItemsList\tv_show;

use Drupal\imdb_api\Annotation\ImdbItemsList;
use Drupal\imdb_api\Plugin\ImdbItemsList\TopRatedList;

/**
 * Defines an ImdbItemsList implementation.
 *
 * @ImdbItemsList(
 *   id = "top_rated_tv_shows",
 *   title = @Translation("Top rated TV shows"),
 *   description = @Translation("List of top rated TV shows."),
 *   entityClass = "\Imdb\Entities\TvShow",
 *   method = "getTopRatedTvShows",
 *   options = {},
 *   limit = "",
 *   shuffle = FALSE
 * )
 */
class TopRatedTvShows extends TopRatedList {}
