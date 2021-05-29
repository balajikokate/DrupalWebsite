<?php

namespace Drupal\imdb_api\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\imdb_api\ImdbApiInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'imdb_item' widget.
 *
 * @FieldWidget(
 *   id = "imdb_item",
 *   module = "imdb_api",
 *   label = @Translation("IMDB item"),
 *   field_types = {
 *     "imdb_item",
 *     "episode",
 *   }
 * )
 */
class ImdbItemWidget extends WidgetBase {

  /**
   * The IMDB API service.
   *
   * @var \Drupal\imdb_api\ImdbApiInterface
   */
  protected $imdbApi;

  /**
   * Constructs a WidgetBase object.
   *
   * @param string $plugin_id
   *   The plugin_id for the widget.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the widget is associated.
   * @param array $settings
   *   The widget settings.
   * @param array $third_party_settings
   *   Any third party settings.
   * @param array $imdb_api
   *   The IMDB API service.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings, ImdbApiInterface $imdb_api) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);

    $this->imdbApi = $imdb_api;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($plugin_id, $plugin_definition, $configuration['field_definition'], $configuration['settings'], $configuration['third_party_settings'], $container->get('imdb_api.imdb_api'));
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        'size' => 60,
        'placeholder' => '',
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = [];

    $elements['size'] = [
      '#type' => 'number',
      '#title' => t('Size of textfield'),
      '#default_value' => $this->getSetting('size'),
      '#required' => TRUE,
      '#min' => 1,
    ];
    $elements['placeholder'] = [
      '#type' => 'textfield',
      '#title' => t('Placeholder'),
      '#default_value' => $this->getSetting('placeholder'),
      '#description' => t('Text that will be shown inside the field until a value is entered. This hint is usually a sample value or a brief description of the expected format.'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    $summary[] = t('Textfield size: @size', ['@size' => $this->getSetting('size')]);
    if (!empty($this->getSetting('placeholder'))) {
      $summary[] = t('Placeholder: @placeholder', ['@placeholder' => $this->getSetting('placeholder')]);
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['value'] = $element + [
        '#type' => 'textfield',
        '#default_value' => isset($items[$delta]->value) ? $items[$delta]->value : NULL,
        '#size' => $this->getSetting('size'),
        '#placeholder' => $this->getSetting('placeholder'),
        '#maxlength' => $this->getFieldSetting('max_length'),
        '#element_validate' => [[$this, 'validateImdbItem']],
      ];

    return $element;
  }

  public function validateImdbItem($element, FormStateInterface $form_state, $form) {
    // @todo move this to form submit.
    $field_name = $this->fieldDefinition->getName();

    // Skip validations if we are not submitting the form.
    $trigger = $form_state->getTriggeringElement()['#type'] ?? FALSE;
    $trigger_parents = $form_state->getTriggeringElement()['#parents'] ?? FALSE;
    if ($trigger != 'submit' || in_array('add_more', $trigger_parents)) {
      return;
    }

    $name = NestedArray::getValue($form_state->getValues(), $element['#parents']);

    // Don't need an empty field to run validation on,
    // also skipping previously added items.
    // @TODO find a better to check if it's an actor item in array.
    if (empty($name) || preg_match('/(?>nm|tt)\d+/', $name)) {
      return;
    }

    $type = $this->getFieldSetting('imdb_item_type');
    $entity = $this->imdbApi->createImdbEntity($name, $type);

    if (!$entity) {
      $form_state->setError($element, $this->t('No IMDB item with name "%name" found.', ['%name' => $name['value']]));
    }
    else {
      $name = $type == 'actor' ? $entity->getName() : $entity->getTitle();
      NestedArray::setValue($form_state->getValues(), $element['#parents'], $name . ' (' . $entity->getPureId() . ')', TRUE);
    }
  }

}
