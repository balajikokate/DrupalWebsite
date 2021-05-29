<?php

namespace Drupal\imdb_api\Plugin\ImdbItemsList\movie;

use Drupal\imdb_api\Annotation\ImdbItemsList;
use Drupal\imdb_api\Plugin\ImdbItemsList\ComingSoonList;

/**
 * Defines an ImdbItemsList implementation.
 *
 * @ImdbItemsList(
 *   id = "coming_soon_movies",
 *   title = @Translation("Coming soon movies"),
 *   description = @Translation("List of coming soon movies."),
 *   entityClass = "\Imdb\Entities\Movie",
 *   method = "getComingSoonMovies",
 *   options = {
 *     "homeCountry" = "",
 *     "purchaseCountry" = "",
 *     "currentCountry" = "",
 *   },
 *   limit = "",
 *   shuffle = FALSE
 * )
 */
class ComingSoonMovies extends ComingSoonList {}
