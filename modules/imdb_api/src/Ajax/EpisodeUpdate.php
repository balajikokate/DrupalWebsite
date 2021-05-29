<?php

namespace Drupal\imdb_api\Ajax;

use Drupal\Core\Ajax\CommandInterface;

/**
 * Class EpisodeUpdate.
 */
class EpisodeUpdate implements CommandInterface {

  /**
   * Episode data.
   *
   * @var string
   */
  protected $data;

  /**
   * Constructs an EpisodeUpdate object.
   *
   * @param string $data
   *   Episode data.
   */
  public function __construct($data) {
    $this->data = $data;
  }

  /**
   * Render custom ajax command.
   *
   * @return array
   *   Command function.
   */
  public function render() {
    return [
      'command' => 'episodeUpdate',
      'data' => $this->data,
    ];
  }

}
