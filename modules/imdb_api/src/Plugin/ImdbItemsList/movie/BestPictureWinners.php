<?php

namespace Drupal\imdb_api\Plugin\ImdbItemsList\movie;

use Drupal\imdb_api\Annotation\ImdbItemsList;
use Drupal\imdb_api\Plugin\ImdbItemsList\BaseList;

/**
 * Defines an ImdbItemsList implementation.
 *
 * @ImdbItemsList(
 *   id = "best_picture_winners",
 *   title = @Translation("Best picture winners"),
 *   description = @Translation("List of best picture winners."),
 *   entityClass = "\Imdb\Entities\Movie",
 *   method = "getBestPictureWinners",
 *   options = {},
 *   limit = "",
 *   shuffle = FALSE
 * )
 */
class BestPictureWinners extends BaseList {}
