<?php

namespace Drupal\imdb_api\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\editor\Ajax\EditorDialogSave;
use Drupal\imdb_api\ImdbApiInterface;
use Drupal\imdb_api\ImdbItemRenderable;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ImdbApiItemDialog.
 */
class ImdbApiItemDialog extends FormBase {

  use ImdbItemFormTrait;
  use ImdbItemRenderable;

  const IMDB_ITEM_DEFAULT_TYPE = 'none';
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
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The renderer service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * Constructs a WidgetBase object.
   *
   * @param array $imdb_api
   *   The IMDB API service.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler service.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   */
  public function __construct(ImdbApiInterface $imdb_api, ModuleHandlerInterface $module_handler, RendererInterface $renderer) {
    $this->imdbApi = $imdb_api;
    $this->moduleHandler = $module_handler;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('imdb_api.imdb_api'),
      $container->get('module_handler'),
      $container->get('renderer')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'imdb_api_item_dialog';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // @TODO get rid of hardcoded options.
    $form['imdb_item_type'] = [
      '#type' => 'select',
      '#title' => t('IMDB item type'),
      '#options' => [
        'none' => $this->t('None'),
        'actor' => $this->t('Actor'),
        'movie' => $this->t('Movie'),
        'episode' => $this->t('Episode'),
        'tv_show' => $this->t('TV Show'),
        'video_game' => $this->t('Video Game'),
      ],
      '#default_value' => self::IMDB_ITEM_DEFAULT_TYPE,
      '#required' => TRUE,
    ];
    $form['imdb_item_container'] = [
      '#type' => 'container',
      '#states' => [
        'invisible' => [
          ':input[name="imdb_item_type"]' => ['value' => self::IMDB_ITEM_DEFAULT_TYPE],
        ],
      ],
    ];
    $form['imdb_item_container']['imdb_item_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('IMDB Item'),
      '#maxlength' => 64,
      '#size' => 64,
    ];
    $this->getRenderDetailsForm($form['imdb_item_container'],
      self::IMDB_ITEM_DEFAULT_VIEW_MODE,
      self::IMDB_ITEM_DEFAULT_IMAGE_STYLE
    );
    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['save_modal'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#submit' => [],
      '#ajax' => [
        'callback' => '::submitForm',
        'event' => 'click',
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
    $type = $form_state->getValue('imdb_item_type');
    if ($type == self::IMDB_ITEM_DEFAULT_TYPE) {
      $form_state->setError($form['imdb_item_type'], $this->t('Please choose IMDB entity type.'));
    }

    $name = $form_state->getValue('imdb_item_name');
    if (!$name) {
      $form_state->setError($form['imdb_item_name'], $this->t('Please choose IMDB entity name.'));
    }

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    $type = $form_state->getValue('imdb_item_type');
    $name = $form_state->getValue('imdb_item_name');
    $view_mode = $form_state->getValue('imdb_item_view_mode');
    $image_style = $form_state->getValue('imdb_item_image_style');

    $entity = $this->imdbApi->createImdbEntity($name, $type);
    if ($entity) {
      $output = $this->renderItem($entity, $type, $view_mode, $image_style, self::IMDB_ITEM_TRUNCATE_SIZE);
      $response->addCommand(new EditorDialogSave(['output' => $output]));
    }
    $response->addCommand(new CloseModalDialogCommand());

    return $response;
  }

}
