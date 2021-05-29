<?php

namespace Drupal\imdb_api\Plugin\Field\FieldWidget;

use Drupal\Component\Serialization\Json;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AppendCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\MessageCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\imdb_api\Ajax\EpisodeDelete;
use Drupal\imdb_api\Ajax\EpisodeUpdate;

/**
 * Plugin implementation of the 'episode' widget.
 *
 * @FieldWidget(
 *   id = "episode",
 *   module = "imdb_api",
 *   label = @Translation("Episode"),
 *   field_types = {
 *     "episode",
 *   }
 * )
 */
class EpisodeWidget extends ImdbItemWidget {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    // Retrieve the value of the current item as array and JSON.
    $data = [];
    $value = '';
    if ($item = $items->get($delta)) {
      $value = $item->getValue()['value'] ?? '';

      $data = $value ? Json::decode($value) : [];
    }

    // @todo refactor this.
    $parents = $form['#parents'];
    $field_name = $this->fieldDefinition->getName();
    $storage = $form_state->getStorage();
    $imdb_entity = $storage['imdb_entity'][$delta] ?? [];
    $trigger = $form_state->getTriggeringElement();
    $form_item_data = $form_state->getValue($field_name)[$delta]['value'] ?? '';
    $form_item_removed = !empty($form_state->getValue($field_name)[$delta]['removed']);
    $is_multiple = $this->fieldDefinition->getFieldStorageDefinition()->isMultiple();

    $is_add_more = isset($trigger['#array_parents']) && end($trigger['#array_parents']) == 'add_more';

    if (!$data && $form_item_data) {
      $data = Json::decode($form_item_data);
    }

    // Setting up a temporary default value to skip validation when field is marked as required.
    $value_default_value = !$form_state->getValues() ? '{}' : NULL;

    $element['value'] = $element + [
      '#type' => 'textfield',
      '#default_value' => isset($items[$delta]->value) ? $items[$delta]->value : $value_default_value,
      '#size' => $this->getSetting('size'),
      '#placeholder' => $this->getSetting('placeholder'),
      '#maxlength' => $this->getFieldSetting('max_length'),
      '#attributes' => [
        'class' => [
          'episode-field-' . $delta,
          'visually-hidden',
        ],
      ],
      '#attached' => [
        'library' => [
          'imdb_api/imdb_api.episode.widget',
        ],
      ],
    ];

    $element['removed'] = [
      '#type' => 'checkbox',
      '#default_value' => $form_item_removed,
      '#access' => $is_multiple,
      '#attributes' => [
        'class' => [
          'episode-removed',
          'episode-removed-' . $delta,
          'visually-hidden',
        ],
      ],
    ];

    $element['set'] = [
      '#type' => 'fieldset',
      '#title' => t('Choose a TV show episode'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      '#access' => !($is_add_more && $form_item_removed)
    ];

    $element['set']['tv_show_container'] = [
      '#type' => 'container',
      '#weight' => '0',
      '#attributes' => [
        'class' => [
          'tv-show-container-' . $delta,
        ],
      ],
    ];

    $element['set']['tv_show_container']['tv_show'] = [
      '#type' => 'textfield',
      '#title' => $this->t('TV Show'),
      '#default_value' => $data ? $data['tv_show'] . ' (' . $data['tv_show_pure_id'] . ')' : '',
    ];

    $element['set']['actions'] = [
      '#type' => 'actions',
      '#weight' => '1',
    ];
    $element['set']['actions']['find_tv_show'] = [
      '#type' => 'submit',
      '#name' => 'find_tv_show_' . $delta,
      '#value' => $this->t('Find TV Show'),
      '#disabled' => $data || $imdb_entity,
      '#submit' => [[$this, 'findTvShowSubmit']],
      '#ajax' => [
        'callback' => [$this, 'findTvShowAjax'],
      ],
      '#limit_validation_errors' => [array_merge($parents, [$field_name])],
      '#attributes' => [
        'tabindex' => -1,
        'class' => [
          'action-find-tv-show-' . $delta,
        ],
      ],
    ];

    $element['set']['actions']['remove_tv_show'] = [
      '#type' => 'submit',
      '#name' => 'remove_tv_show_' . $delta,
      '#value' => $this->t('Remove TV Show'),
      '#limit_validation_errors' => [array_merge($parents, [$field_name])],
      '#access' => $is_multiple,
      '#ajax' => [
        'callback' => [static::class, 'removeTvShowAjax'],
        'effect' => 'fade',
      ],
      '#attributes' => [
        'tabindex' => -1,
        'class' => [
          'action-remove-tv-show-' . $delta,
        ],
      ],
    ];

    if (!$is_multiple) {
      if (!$imdb_entity && $data && $tv_show = $this->imdbApi->createImdbEntity($data['tv_show_pure_id'], 'tv_show')) {
        $imdb_entity = [
          'title' => $tv_show->getTitle(),
          'pure_id' => $tv_show->getPureId(),
          'seasons' => $tv_show->getSeasons(),
          'season' => $data['season'],
          'episode' => $data['episode'],
          'episode_value' => Json::encode($data),
        ];
      }
    }

    if ($imdb_entity && !$is_add_more) {
      $this->generateEpisodes($element, $delta, $imdb_entity);
      $this->generateSeasons($element, $delta, $imdb_entity);
    }
    elseif ($data) {
      $this->generateStaticDetails($element, $delta, $data);
    }
    else {
      $this->generateTvShowContainers($element, $delta);
    }

    return $element;
  }

  private function generateStaticDetails(&$element, $delta, $data) {
    $element['set']['season_container'] = [
      '#type' => 'container',
      '#weight' => '2',
      '#attributes' => [
        'class' => [
          'season-container-n' . $delta,
        ],
      ],
    ];
    $element['set']['season_container']['season'] = [
      '#markup' => $this->t('Season #@season', ['@season' => $data['season']]),
    ];
    $element['set']['episode_container'] = [
      '#type' => 'container',
      '#weight' => '3',
      '#attributes' => [
        'class' => [
          'episode-container-n' . $delta,
        ],
      ],
    ];
    $element['set']['episode_container']['episode'] = [
      '#markup' => $this->t('Episode #@episode: %title (@year)', [
        '@episode' => $data['episode'],
        '%title' => $data['title'],
        '@year' => $data['year'] ?? '',
      ]),
    ];
  }

  private function generateTvShowContainers(&$element, $delta) {
    $element['set']['season_container'] = [
      '#type' => 'container',
      '#weight' => '2',
      '#attributes' => [
        'class' => [
          'season-container-n' . $delta,
          'visually-hidden',
        ],
      ],
    ];
    $element['set']['episode_container'] = [
      '#type' => 'container',
      '#weight' => '3',
      '#attributes' => [
        'class' => [
          'episode-container-n' . $delta,
          'visually-hidden',
        ],
      ],
    ];
  }

  private function generateSeasons(&$element, $delta, $data, $default_value = NULL) {
    $options = [];
    foreach ($data['seasons'] as $season) {
      $options[$season->season] = $season->season;
    }

    $element['set']['season_container'] = [
      '#type' => 'container',
      '#weight' => '2',
      '#attributes' => [
        'class' => [
          'season-container-n' . $delta,
          'visually-hidden',
        ],
      ],
    ];
    $element['set']['season_container']['season'] = [
      '#type' => 'select',
      '#title' => $this->t('Season'),
      '#options' => $options,
      '#default_value' => $default_value ? $default_value : array_key_first($data['seasons']),
      '#ajax' => [
        'callback' => [$this, 'changeSeason'],
        'event' => 'change',
      ],
      '#attributes' => [
        'tabindex' => -1,
      ],
    ];

    if (isset($data['season'])) {
      $element['set']['season_container']['season']['#default_value'] = $data['season'];
      unset($element['set']['season_container']['#attributes']['class'][1]);
    }
  }

  private function generateEpisodes(&$element, $delta, $data, $default_value = NULL) {
    list('title' => $title, 'pure_id' => $pure_id, 'seasons' => $seasons) = $data;

    $episode_value = '';

    foreach ($seasons as $season) {
      $options = [];

      foreach ($season->episodes as $episode) {
        if (preg_match('/^\/title\/(.+)\/$/', $episode->id, $match)) {
          $year = isset($episode->year) ? $episode->year : '';

          // @todo needs to be validated before submit.
          $episode_data = Json::encode([
            'episode' => $episode->episode,
            'title' => $episode->title,
            'pure_id' => $match[1],
            'year' => $year,
            'season' => $season->season,
            'tv_show' => $title,
            'tv_show_pure_id' => $pure_id,
            'delta' => $delta,
          ]);

          $options[$episode_data] = 'Episode #' . $episode->episode . ': ' . $episode->title . ' (' . $year . ')';

          if (isset($data['season']) && $data['season'] == $season->season) {
            if (isset($data['episode']) && $data['episode'] == $episode->episode) {
              $episode_value = $data['episode_value'] ?? '';
            }
          }
        }
      }

      $episode_container = 'episode_container_s' . $season->season;
      $element['set'][$episode_container] = [
        '#type' => 'container',
        '#weight' => '3',
        '#attributes' => [
          'class' => [
            'episode-container-n' . $delta . '-s',
            'episode-container-n' . $delta . '-s' . $season->season,
            'visually-hidden',
          ],
        ],
      ];
      $element['set'][$episode_container]['episode'] = [
        '#type' => 'select',
        '#title' => $this->t('Episode'),
        '#options' => $options,
        '#default_value' => $default_value ? $default_value : array_key_first($options),
        '#ajax' => [
          'callback' => [$this, 'changeEpisode'],
          'event' => 'change',
        ],
        '#attributes' => [
          'tabindex' => -1,
        ],
      ];
    }

    if (isset($data['season'])) {
      unset($element['set']['episode_container_s' . $data['season']]['#attributes']['class'][2]);
    }
    if ($episode_value) {
      $element['set']['episode_container_s' . $data['season']]['episode']['#default_value'] = $episode_value;
    }
  }

  public function findTvShowSubmit(array $form, FormStateInterface $form_state) {
    $button = $form_state->getTriggeringElement();

    // Go one level up in the form, to the widgets container.
    $element = NestedArray::getValue($form, array_slice($button['#array_parents'], 0, -4));
    $field_name = $element['#field_name'];

    preg_match('/^find_tv_show_(\d+)$/', $button['#name'], $matches);

    $set = $form_state->getValue($field_name);
    $tv_show = $set[$matches[1]]['set']['tv_show_container']['tv_show'] ?? '';

    if ($tv_show && $entity = $this->imdbApi->createImdbEntity($tv_show, 'tv_show')) {
      $imdb_entity[$matches[1]] = [
        'title' => $entity->getTitle(),
        'pure_id' => $entity->getPureId(),
        'seasons' => $entity->getSeasons(),
      ];
      $form_state->set('imdb_entity', $imdb_entity);

      $set[$matches[1]]['set']['tv_show_container']['tv_show'] = $entity->getTitle() . ' (' . $entity->getPureId() . ')';
      $form_state->setValue($field_name, $set);
    }

    $form_state->setRebuild();
  }

  private function generateTvShowAjax(FormStateInterface $form_state, $response, $element, $delta) {
    $field_name = $element['#field_name'];

    $tv_show = $form_state->getValue($field_name)[$delta]['set']['tv_show_container']['tv_show'] ?? '';

    if (!$tv_show || !preg_match('/(?>tt)\d+/', $tv_show)) {
      $message = $this->t('No TV show with name "%name" found.', ['%name' => $tv_show]);
      $response->addCommand(new MessageCommand($message, 'div.tv-show-container-' . $delta, ['type' => 'warning'], TRUE));

      return;
    }

    $element[$delta]['set']['tv_show_container']['tv_show']['#value'] = $tv_show;

    $response->addCommand(new ReplaceCommand('div.tv-show-container-' . $delta, $element[$delta]['set']['tv_show_container']));
    $response->addCommand(new ReplaceCommand('input.action-find-tv-show-' . $delta, $element[$delta]['set']['actions']['find_tv_show']));
  }

  private function generateDetailsAjax(FormStateInterface $form_state, $response, $element, $delta) {
    $seasons = $form_state->getStorage()['imdb_entity'][$delta]['seasons'] ?? [];

    if (!$seasons) {
      return;
    }

    $response->addCommand(new ReplaceCommand('div.season-container-n' . $delta, $element[$delta]['set']['season_container']));

    foreach ($seasons as $season) {
      $response->addCommand(new AppendCommand('div.episode-container-n' . $delta, $element[$delta]['set']['episode_container_s' . $season->season]));
    }

    // Update an actual widget field.
    $response->addCommand(new EpisodeUpdate($element[$delta]['set']['episode_container_s' . reset($seasons)->season]['episode']['#default_value']));

    // Show season and episode fields if TV show is chosen.
    $response->addCommand(new InvokeCommand('div.season-container-n' . $delta, 'removeClass', ['visually-hidden']));
    $response->addCommand(new InvokeCommand('div.episode-container-n' . $delta, 'removeClass', ['visually-hidden']));
    $response->addCommand(new InvokeCommand('div.episode-container-n' . $delta . '-s' . reset($seasons)->season, 'removeClass', ['visually-hidden']));
  }

  public function findTvShowAjax(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    $button = $form_state->getTriggeringElement();

    $element = NestedArray::getValue($form, array_slice($button['#array_parents'], 0, -4));

    preg_match('/^find_tv_show_(\d+)$/', $button['#name'], $matches);

    $this->generateTvShowAjax($form_state, $response, $element, $matches[1]);
    $this->generateDetailsAjax($form_state, $response, $element, $matches[1]);

    return $response;
  }

  public function changeSeason(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    $trigger = $form_state->getTriggeringElement();

    $element = NestedArray::getValue($form, array_slice($trigger['#array_parents'], 0, -4));

    $delta = $trigger['#array_parents'][2];

    $data = array_key_first($element[$delta]['set']['episode_container_s' . $trigger['#value']]['episode']['#options']);

    // Update an actual widget field.
    $response->addCommand(new EpisodeUpdate($data));

    $response->addCommand(new InvokeCommand('div.episode-container-n' . $delta . '-s', 'addClass', ['visually-hidden']));
    $response->addCommand(new InvokeCommand('div.episode-container-n' . $delta . '-s' . $trigger['#value'], 'removeClass', ['visually-hidden']));

    return $response;
  }

  public function changeEpisode(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    $trigger = $form_state->getTriggeringElement();

    // Update an actual widget field.
    $response->addCommand(new EpisodeUpdate($trigger['#value']));

    return $response;
  }

  public static function removeTvShowAjax(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    $trigger = $form_state->getTriggeringElement()['#name'];
    if (preg_match('/^remove_tv_show_(\d+)$/', $trigger, $matches)) {
      $response->addCommand(new EpisodeDelete($matches[1]));
    }

    return $response;
  }

}
