<?php

namespace Drupal\imdb_api\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\imdb_api\Form\ImdbItemFormTrait;
use Drupal\imdb_api\ImdbApiInterface;
use Drupal\imdb_api\ImdbItemRenderable;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'imdb_item' formatter.
 *
 * @FieldFormatter(
 *   id = "imdb_item",
 *   label = @Translation("IMDB Item"),
 *   field_types = {
 *     "imdb_item",
 *     "episode"
 *   }
 * )
 */
class ImdbItemFormatter extends FormatterBase {

  use ImdbItemFormTrait;
  use ImdbItemRenderable;

  // @TODO get rid of duplicate constants.
  const IMDB_ITEM_DEFAULT_VIEW_MODE = 'teaser';
  const IMDB_ITEM_DEFAULT_IMAGE_STYLE = 'thumbnail';
  const IMDB_ITEM_TRUNCATE_SIZE = 200;

  /**
   * The IMDB API service.
   *
   * @var \Drupal\imdb_api\ImdbApiInterface
   */
  protected $imdbApi;

  /**
   * Constructs a FormatterBase object.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Any third party settings.
   * @param array $imdb_api
   *   The IMDB API service.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, ImdbApiInterface $imdb_api) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);

    $this->imdbApi = $imdb_api;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($plugin_id, $plugin_definition, $configuration['field_definition'], $configuration['settings'], $configuration['label'], $configuration['view_mode'], $configuration['third_party_settings'], $container->get('imdb_api.imdb_api'));
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'view_mode' => self::IMDB_ITEM_DEFAULT_VIEW_MODE,
      'image_style' => self::IMDB_ITEM_DEFAULT_IMAGE_STYLE,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = [];

    $settings = $this->getSettings();
    $this->getRenderDetailsForm($elements, $settings['view_mode'], $settings['image_style']);

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    // Implement settings summary.

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = ['#markup' => $this->viewValue($item)];
    }

    return $elements;
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {
    if (preg_match('/(?>nm|tt)\d+/', $item->value, $matches)) {
      $type = $this->getFieldSetting('imdb_item_type');

      if ($entity = $this->imdbApi->createImdbEntity($matches[0], $type)) {
        $settings = $this->getSettings();
        return $this->renderItem($entity, $type, $settings['view_mode'], $settings['image_style'], self::IMDB_ITEM_TRUNCATE_SIZE);
      }
    }

    return '';
  }

}
