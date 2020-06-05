<?php

namespace Drupal\chatbot\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Menu\MenuParentFormSelectorInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configuration form definition for the dialogflow menu.
 */
class ChatbotConfigurationForm extends ConfigFormBase {

  /**
   * ChatbotConfigurationForm constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager service.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    LanguageManagerInterface $language_manager
  ) {
    parent::__construct($config_factory);
    $this->languageManager = $language_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('language_manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'chatbot.dialogflow_settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'chatbot_configuration_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('chatbot.dialogflow_settings');
    $languages = [];
    
    // @TODO Discover local issue with enabling language module.
    foreach ($this->languageManager->getLanguages() as $key => $object) {
      $languages[$key] = $key;
    }
    $languages = [
      'en-US' => 'en-US',
      'uk-UA' => 'uk-UA'
    ];

    $form['dialogflow_project'] = array(
      '#type' => 'textfield',
      '#description' => $this->t('Please type Dialogflow Project ID to your agent'),
      '#title' => $this->t('Dialogflow Project ID'),
      '#default_value' => $config->get('dialogflow_project'),
      '#required' => TRUE
    );

    $form['dialogflow_langcode'] = array(
      '#type' => 'select',
      '#description' => $this->t('Please type Dialogflow language'),
      '#title' => $this->t('Dialogflow language'),
      '#default_value' => $config->get('dialogflow_langcode'),
      '#options' => $languages,
      '#required' => TRUE
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('chatbot.dialogflow_settings')
      ->set('dialogflow_project', $form_state->getValue('dialogflow_project'))
      ->save();

    $this->config('chatbot.dialogflow_settings')
      ->set('dialogflow_langcode', $form_state->getValue('dialogflow_langcode'))
      ->save();
  }
}