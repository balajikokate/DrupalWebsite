<?php

namespace Drupal\imdb_api\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ImdbApiConfigForm.
 */
class ImdbApiConfigForm extends ConfigFormBase {

    const RAPIDAPI_BASE_URL_DEFAULT = 'https://imdb8.p.rapidapi.com/';

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'imdb_api.imdbapiconfig',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'imdb_api_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('imdb_api.imdbapiconfig');

    $form['api'] = [
      '#type' => 'select',
      '#title' => $this->t('API'),
      '#description' => $this->t('Choose which API to use.'),
      '#options' => [
        'rapidapi' => 'RapidAPI',
      ],
      '#default_value' => $config->get('api'),
    ];
    $form['rapidapi'] = [
      '#type' => 'fieldset',
      '#title' => t('RapidAPI settings'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
      '#tree' => TRUE,
      '#states' => [
        'visible' => [
          ':input[name="api"]' => ['value' => 'rapidapi'],
        ],
      ],
    ];
    $form['rapidapi']['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API key'),
      '#default_value' => $config->get('rapidapi_api_key'),
    ];
    $base_url = $config->get('rapidapi_base_url');
    $form['rapidapi']['base_url'] = [
      '#type' => 'url',
      '#title' => $this->t('Base URL'),
      '#default_value' => $base_url ? $base_url : self::RAPIDAPI_BASE_URL_DEFAULT,
    ];
    $use_query_string = $config->get('rapidapi_use_query_string');
    $form['rapidapi']['use_query_string'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use query string'),
        '#default_value' => $use_query_string ? $use_query_string : TRUE,
    ];
    $form['rapidapi']['client'] = [
      '#type' => 'select',
      '#title' => $this->t('Client'),
      '#options' => [
        'apidojo' => 'ApiDojo',
      ],
      '#default_value' => $config->get('rapidapi_client'),
    ];
    $form['rapidapi']['fetcher'] = [
      '#type' => 'select',
      '#title' => $this->t('Fetcher'),
      '#options' => [
        '\Imdb\Fetcher\BaseFetcher' => $this->t('Base'),
        '\Imdb\Fetcher\StrictFetcher' => $this->t('Strict'),
      ],
      '#default_value' => $config->get('rapidapi_fetcher'),
    ];
    $form['rapidapi']['parser'] = [
      '#type' => 'select',
      '#title' => $this->t('Client'),
      '#options' => [
        '\Imdb\Parser\JsonParser' => $this->t('JSON'),
      ],
      '#default_value' => $config->get('rapidapi_parser'),
    ];

    // Caching section.
    $cache_options = ['none' => $this->t('None')];

    $extensions = array_flip(get_loaded_extensions());
    if (isset($extensions['redis'])) {
      $cache_options['\Imdb\Cache\PhpRedis'] = $this->t('PhpRedis');
    }

    $form['rapidapi']['cache'] = [
      '#type' => 'select',
      '#title' => $this->t('Cache'),
      '#options' => $cache_options,
      '#default_value' => $config->get('rapidapi_cache'),
    ];

    // Cache settings section.
    $form['rapidapi']['cache_configs'] = [
      '#type' => 'fieldset',
      '#title' => t('Cache settings'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
      '#tree' => TRUE,
      '#states' => [
        'invisible' => [
          ':input[name="rapidapi[cache]"]' => ['value' => 'none'],
        ],
      ],
    ];

    $form['rapidapi']['cache_configs']['host'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Host'),
      '#default_value' => $config->get('rapidapi_cache_host'),
    ];
    $form['rapidapi']['cache_configs']['port'] = [
      '#type' => 'number',
      '#title' => $this->t('Port'),
      '#default_value' => $config->get('rapidapi_cache_port'),
    ];
    $form['rapidapi']['cache_configs']['timeout'] = [
      '#type' => 'number',
      '#title' => $this->t('Timeout'),
      '#default_value' => $config->get('rapidapi_cache_timeout'),
    ];
    $form['rapidapi']['cache_configs']['interval'] = [
      '#type' => 'number',
      '#title' => $this->t('Interval'),
      '#default_value' => $config->get('rapidapi_cache_interval'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $rapidapi = $form_state->getValue('rapidapi');

    $this->config('imdb_api.imdbapiconfig')
      ->set('rapidapi_api_key', $rapidapi['api_key'])
      ->set('rapidapi_base_url', $rapidapi['base_url'])
      ->set('rapidapi_use_query_string', $rapidapi['use_query_string'])
      ->set('rapidapi_client', $rapidapi['client'])
      ->set('rapidapi_fetcher', $rapidapi['fetcher'])
      ->set('rapidapi_parser', $rapidapi['parser'])
      ->set('rapidapi_cache', $rapidapi['cache'])
      ->set('rapidapi_cache_host', $rapidapi['cache_configs']['host'])
      ->set('rapidapi_cache_port', $rapidapi['cache_configs']['port'])
      ->set('rapidapi_cache_timeout', $rapidapi['cache_configs']['timeout'])
      ->set('rapidapi_cache_interval', $rapidapi['cache_configs']['interval'])
      ->set('api', $form_state->getValue('api'))
      ->save();
  }

}
