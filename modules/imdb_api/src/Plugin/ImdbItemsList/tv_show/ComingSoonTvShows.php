<?php

namespace Drupal\imdb_api\Plugin\ImdbItemsList\tv_show;

use Drupal\imdb_api\Annotation\ImdbItemsList;
use Drupal\imdb_api\Plugin\ImdbItemsList\ComingSoonList;

/**
 * Defines an ImdbItemsList implementation.
 *
 * @ImdbItemsList(
 *   id = "coming_soon_tv_shows",
 *   title = @Translation("Coming soon TV shows"),
 *   description = @Translation("List of coming soon TV shows."),
 *   entityClass = "\Imdb\Entities\TvShow",
 *   method = "getComingSoonTvShows",
 *   options = {
 *     "currentCountry" = "",
 *   },
 *   limit = "",
 *   shuffle = FALSE
 * )
 */
class ComingSoonTvShows extends ComingSoonList {}
