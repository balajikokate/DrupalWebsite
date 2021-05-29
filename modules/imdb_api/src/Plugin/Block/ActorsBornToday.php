<?php

namespace Drupal\imdb_api\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'ActorsBornTodayBlock' block.
 *
 * @Block(
 *  id = "actors_born_today",
 *  admin_label = @Translation("Actors born today"),
 * )
 */
class ActorsBornToday extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\imdb_api\Plugin\ImdbItemsListManager definition.
   *
   * @var \Drupal\imdb_api\Plugin\ImdbItemsListManager
   */
  protected $pluginManagerImdbItemsListActor;

  /**
   * The cache factory.
   *
   * @var \Drupal\Core\Cache\CacheFactoryInterface
   */
  protected $cacheFactory;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->pluginManagerImdbItemsListActor = $container->get('plugin.manager.imdb_items_list.actor');
    $instance->cacheFactory = $container->get('cache_factory');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'imdb_api__actors__with_known_for';
    $build['#attached']['library'][] = 'imdb_api/actor.teaser';

    //$cache_bin = $this->cacheFactory->get('default');

    $born_today = $this->pluginManagerImdbItemsListActor->createInstance('born_today', ['shuffle' => TRUE, 'limit' => 2, 'options' => [
      'day' => date('d'),
      'month' => date('m'),
    ]]);

    $list = $born_today->getList();
    foreach ($list as $key => $entity) {
      // Passing actor entity.
      $build['#actors'][$key]['entity'] = $entity;

      // Known For section.
      foreach ($entity->getKnownFor() as $known_for_key => $known_for) {
        $build['#actors'][$key]['known_for'][$known_for_key]['title'] = $known_for->title->title;
        $build['#actors'][$key]['known_for'][$known_for_key]['year'] = $known_for->title->year;

        $build['#actors'][$key]['known_for'][$known_for_key]['characters'] = [];
        if (isset($known_for->summary->characters)) {
          foreach ($known_for->summary->characters as $character) {
            $build['#actors'][$key]['known_for'][$known_for_key]['characters'][] = $character;
          }
        }

        // Passing Known For image.
        $build['#actors'][$key]['known_for'][$known_for_key]['image'] = $known_for->title->image->url;
      }

      // Passing actor's image.
      $build['#actors'][$key]['image'] = $entity->getImage()->url;
    }

    // Providing cache metadata.
    $build['#cache'] = [
      'bin' => 'render',
      'keys' => [
        'actors_born_today',
        date('Y-m-d'),
      ],
      'contexts' => ['imdb_api_date'],
      'tags' => [
        'imdb_api:list:actors',
        'imdb_api:list:actors:born_today',
      ],
      //'max-age' => Cache::PERMANENT,
      'max-age' => 120,
    ];

    return $build;
  }

}
