<?php

namespace Drupal\imdb_api\Twig\Extension;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Creates ImdbItemImage Twig ext.
 */
class ImdbItemImage extends AbstractExtension {

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Constructs a new ImdbItemImage object.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(ModuleHandlerInterface $module_handler) {
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'imdb_item_image';
  }

  /**
   * {@inheritdoc}
   */
  public function getFilters() {
    return [
      new TwigFilter('imdb_item_image', [$this, 'getRenderable']),
    ];
  }

  public function getRenderable($uri, $style) {
    $element = [
      '#type' => 'html_tag',
      '#tag' => 'img',
      '#attributes' => [
        'src' => $uri,
        'alt' => '',
      ],
    ];
    if ($this->moduleHandler->moduleExists('imagecache_external')) {
      $element = [
        '#theme' => 'imagecache_external',
        '#uri' => $uri,
        '#style_name' => $style,
        '#alt' => '',
      ];
    }

    return $element;
  }

}
