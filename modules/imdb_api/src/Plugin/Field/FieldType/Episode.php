<?php

namespace Drupal\imdb_api\Plugin\Field\FieldType;

use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'episode' field type.
 *
 * @FieldType(
 *   id = "episode",
 *   label = @Translation("Episode"),
 *   description = @Translation("Episode field type"),
 *   category = @Translation("IMDB item"),
 *   default_widget = "episode",
 *   default_formatter = "imdb_item"
 * )
 */
class Episode extends ImdbItem {

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {
    return [];
  }

}
