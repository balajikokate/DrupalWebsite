<?php

namespace Drupal\imdb_api\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\MessageCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Serialization\Json;
use Drupal\imdb_api\Ajax\EpisodeUpdate;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class EpisodeForm.
 */
class EpisodeForm extends FormBase {

  /**
   * Drupal\imdb_api\ImdbApiInterface definition.
   *
   * @var \Drupal\imdb_api\ImdbApiInterface
   */
  protected $imdbApi;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->imdbApi = $container->get('imdb_api.imdb_api');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'episode_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // @todo duplicate of \Drupal\imdb_api\Plugin\Field\FieldWidget\EpisodeWidget.
    $form['set'] = [
      '#type' => 'fieldset',
      '#title' => t('Choose a TV show episode'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $form['set']['tv_show_container'] = [
      '#type' => 'container',
      '#weight' => '0',
      '#attributes' => [
        'class' => [
          'tv-show-container',
        ],
      ],
    ];

    $form['set']['tv_show_container']['tv_show'] = [
      '#type' => 'textfield',
      '#title' => $this->t('TV Show'),
    ];

    $form['set']['actions'] = [
      '#type' => 'actions',
      '#weight' => '1',
    ];
    $form['set']['actions']['find_tv_show'] = [
      '#type' => 'submit',
      '#value' => $this->t('Find TV Show'),
      '#submit' => [],
      '#ajax' => [
        'callback' => '::findTvShow',
        'event' => 'click',
      ],
      '#attributes' => [
        'tabindex' => -1,
      ],
    ];

    $form['set']['season_container'] = [
      '#type' => 'container',
      '#weight' => '2',
      '#attributes' => [
        'class' => [
          'season-container',
        ],
      ],
    ];
    $form['set']['episode_container'] = [
      '#type' => 'container',
      '#weight' => '3',
      '#attributes' => [
        'class' => [
          'episode-container',
        ],
      ],
    ];

    $form['set']['season_container']['season'] = [
      '#type' => 'select',
      '#title' => $this->t('Season'),
      '#access' => (bool) $form_state->getUserInput(),
      '#ajax' => [
        'callback' => '::findTvShow',
        'event' => 'change',
      ],
      '#attributes' => [
        'tabindex' => -1,
      ],
    ];
    $form['set']['episode_container']['episode'] = [
      '#type' => 'select',
      '#title' => $this->t('Episode'),
      '#access' => (bool) $form_state->getUserInput(),
      '#ajax' => [
        'callback' => '::findTvShow',
        'event' => 'change',
      ],
      '#attributes' => [
        'tabindex' => -1,
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    foreach ($form_state->getValues() as $key => $value) {
      // @TODO: Validate fields.
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Do nothing.
  }

  /**
   * {@inheritdoc}
   */
  public function findTvShow(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    $tv_show = $form_state->getValue('tv_show');

    $trigger = $form_state->getTriggeringElement()['#name'];
    switch ($trigger) {
      case 'season':

        if (preg_match('/(?>tt)\d+/', $tv_show, $matches)) {

          if ($entity = $this->imdbApi->createImdbEntity($matches[0], 'tv_show')) {
            $this->fetchTvShowSeasons($entity, $form, $form_state->getValue('season'));

            // Updating form_state to match the current choice in Episode field.
            $form_state->setValue('episode', $form['set']['episode_container']['episode']['#value']);

            // Getting rid of errors from the previous state.
            unset($form['set']['episode_container']['#children_errors'], $form['set']['episode_container']['episode']['#errors']);

            $response->addCommand(new ReplaceCommand('div.episode-container', $form['set']['episode_container']));

            // Update an actual widget field.
            $response->addCommand(new EpisodeUpdate($form_state->getValue('episode')));
          }
          else {
            $message = $this->t('No TV show with ID "%id" found.', ['%id' => $matches[0]]);
            $response->addCommand(new MessageCommand($message, '.form-item-tv-show', ['type' => 'warning'], TRUE));
          }
        }

        return $response;
      case 'episode':
        // Update an actual widget field.
        $response->addCommand(new EpisodeUpdate($form_state->getValue('episode')));

        return $response;
    }

    $entity = $this->imdbApi->createImdbEntity($tv_show, 'tv_show');

    if ($entity) {
      // Filling out TV show field.
      $form['set']['tv_show_container']['tv_show']['#value'] = $entity->getTitle() . ' (' . $entity->getPureId() . ')';
      $response->addCommand(new ReplaceCommand('div.tv-show-container', $form['set']['tv_show_container']));

      $this->fetchTvShowSeasons($entity, $form);

      // Updating form_state to match the current choice in Episode field.
      $form_state->setValue('episode', $form['set']['episode_container']['episode']['#value']);

      // Reset previous state of Season field.
      $form['set']['season_container']['season']['#value'] = array_key_first($form['set']['season_container']['season']['#options']);

      // Getting rid of errors from the previous state.
      unset($form['set']['season_container']['#children_errors'], $form['set']['season_container']['season']['#errors']);
      unset($form['set']['episode_container']['#children_errors'], $form['set']['episode_container']['episode']['#errors']);

      // Showing Season and Episode fields.
      $response->addCommand(new HtmlCommand('div.season-container', $form['set']['season_container']['season']));
      $response->addCommand(new HtmlCommand('div.episode-container', $form['set']['episode_container']['episode']));

      // Update an actual widget field.
      $response->addCommand(new EpisodeUpdate($form_state->getValue('episode')));
    }
    else {
      $message = $this->t('No TV show with name "%name" found.', ['%name' => $tv_show]);
      $response->addCommand(new MessageCommand($message, '.form-item-tv-show', ['type' => 'warning'], TRUE));
    }

    return $response;
  }

  private function fetchTvShowSeasons($tv_show, &$form, $season_id = 1) {
    $seasons = $tv_show->getSeasons();

    $season_options = $episode_options = [];
    foreach ($seasons as $season) {
      $season_options[$season->season] = $season->season;

      foreach ($season->episodes as $episode) {

        if (preg_match('/^\/title\/(.+)\/$/', $episode->id, $match)) {
          $episode_data = Json::encode([
            'episode' => $episode->episode,
            'title' => $episode->title,
            'pure_id' => $match[1],
            'year' => $episode->year,
            'season' => $season->season,
            'tv_show' => $tv_show->getTitle(),
            'tv_show_pure_id' => $tv_show->getPureId(),
          ]);

          $episode_options[$season->season][$episode_data] = 'Episode #' . $episode->episode . ': ' . $episode->title . ' (' . $episode->year . ')';
        }
      }
    }

    $form['set']['season_container']['season']['#options'] = $season_options;
    $form['set']['episode_container']['episode']['#options'] = $episode_options[$season_id];

    // Reset previous state of Episode field.
    $form['set']['episode_container']['episode']['#value'] = array_key_first($episode_options[$season_id]);
  }

}
