<?php

namespace Drupal\imdb_api\Plugin\ImdbItemsList\movie;

use Drupal\imdb_api\Annotation\ImdbItemsList;
use Drupal\imdb_api\Plugin\ImdbItemsList\BaseList;

/**
 * Defines an ImdbItemsList implementation.
 *
 * @ImdbItemsList(
 *   id = "popular_movies_by_genre",
 *   title = @Translation("Popular movies by genre"),
 *   description = @Translation("List of popular movies by genre."),
 *   entityClass = "\Imdb\Entities\Movie",
 *   method = "getPopularMoviesByGenre",
 *   options = {
 *     "genre" = "",
 *   },
 *   limit = "",
 *   shuffle = FALSE
 * )
 */
class PopularMoviesByGenre extends BaseList {}
