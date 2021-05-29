<?php

namespace Drupal\imdb_api\Plugin\ImdbItemsList\movie;

use Drupal\imdb_api\Annotation\ImdbItemsList;
use Drupal\imdb_api\Plugin\ImdbItemsList\TopRatedList;

/**
 * Defines an ImdbItemsList implementation.
 *
 * @ImdbItemsList(
 *   id = "top_rated_movies",
 *   title = @Translation("Top rated movies"),
 *   description = @Translation("List of top rated movies."),
 *   entityClass = "\Imdb\Entities\Movie",
 *   method = "getTopRatedMovies",
 *   options = {},
 *   limit = "",
 *   shuffle = FALSE
 * )
 */
class TopRatedMovies extends TopRatedList {}
