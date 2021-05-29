<?php

namespace Drupal\imdb_api\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'ComingSoonMovies' block.
 *
 * @Block(
 *  id = "coming_soon_movies",
 *  admin_label = @Translation("Coming soon movies"),
 * )
 */
class ComingSoonMovies extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\imdb_api\Plugin\ImdbItemsListManager definition.
   *
   * @var \Drupal\imdb_api\Plugin\ImdbItemsListManager
   */
  protected $pluginManagerImdbItemsListMovie;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->pluginManagerImdbItemsListMovie = $container->get('plugin.manager.imdb_items_list.movie');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'homeCountry' => 'US',
      'purchaseCountry' => 'US',
      'currentCountry' => 'US',
      'limit' => NULL,
      'shuffle' => FALSE,
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['homeCountry'] = [
      '#type' => 'textfield',
      '#title' => $this->t('homeCountry'),
      '#default_value' => $this->configuration['homeCountry'],
      '#maxlength' => 64,
      '#size' => 64,
      '#weight' => '0',
    ];
    $form['purchaseCountry'] = [
      '#type' => 'textfield',
      '#title' => $this->t('purchaseCountry'),
      '#default_value' => $this->configuration['purchaseCountry'],
      '#maxlength' => 64,
      '#size' => 64,
      '#weight' => '0',
    ];
    $form['currentCountry'] = [
      '#type' => 'textfield',
      '#title' => $this->t('currentCountry'),
      '#default_value' => $this->configuration['currentCountry'],
      '#maxlength' => 64,
      '#size' => 64,
      '#weight' => '0',
    ];
    $form['limit'] = [
      '#type' => 'number',
      '#title' => $this->t('Limit'),
      '#default_value' => $this->configuration['limit'],
      '#weight' => '0',
    ];
    $form['shuffle'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Shuffle'),
      '#default_value' => $this->configuration['shuffle'],
      '#weight' => '0',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['homeCountry'] = $form_state->getValue('homeCountry');
    $this->configuration['purchaseCountry'] = $form_state->getValue('purchaseCountry');
    $this->configuration['currentCountry'] = $form_state->getValue('currentCountry');
    $this->configuration['limit'] = $form_state->getValue('limit');
    $this->configuration['shuffle'] = $form_state->getValue('shuffle');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'coming_soon_movies';
    $build['#content'][] = $this->configuration['homeCountry'];
    $build['#content'][] = $this->configuration['purchaseCountry'];
    $build['#content'][] = $this->configuration['currentCountry'];
    $build['#content'][] = $this->configuration['limit'];
    $build['#content'][] = $this->configuration['shuffle'];

    return $build;
  }

}
