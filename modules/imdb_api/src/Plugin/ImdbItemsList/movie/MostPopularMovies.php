<?php

namespace Drupal\imdb_api\Plugin\ImdbItemsList\movie;

use Drupal\imdb_api\Annotation\ImdbItemsList;
use Drupal\imdb_api\Plugin\ImdbItemsList\MostPopularList;

/**
 * Defines an ImdbItemsList implementation.
 *
 * @ImdbItemsList(
 *   id = "most_popular_movies",
 *   title = @Translation("Most popular movies"),
 *   description = @Translation("List of most popular movies."),
 *   entityClass = "\Imdb\Entities\Movie",
 *   method = "getMostPopularMovies",
 *   options = {
 *     "homeCountry" = "",
 *     "purchaseCountry" = "",
 *     "currentCountry" = "",
 *   },
 *   limit = "",
 *   shuffle = FALSE
 * )
 */
class MostPopularMovies extends MostPopularList {}
