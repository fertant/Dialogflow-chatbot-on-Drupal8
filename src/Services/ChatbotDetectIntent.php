<?php

namespace Drupal\chatbot\Services;

use Google\Cloud\Dialogflow\V2\SessionsClient;
use Google\Cloud\Dialogflow\V2\TextInput;
use Google\Cloud\Dialogflow\V2\QueryInput;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Class ChatbotDetectIntent.
 */
class ChatbotDetectIntent {

  /**
   * Google client.
   *
   * @var \Google\Cloud\Dialogflow\V2\SessionsClient
   */
  private $client;

  /**
   * Constructs a new ChatbotDetectIntent object.
   * 
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->config_factory = $config_factory->get('chatbot.dialogflow_settings');
    $this->setClient();
  }

  /**
   * Authenicate client agent.
   */
  private function setClient() {
    $credentials = array('credentials' => '/var/www/html/cobalt-academy-243620-666442cad337.json');
    $this->client = new SessionsClient($credentials);
  }

  /**
   * Prepare request.
   * 
   * @param string request test 
   */
  protected function prepareRequest(string $text, string $languageCode) : QueryInput {
    // Create TextInput
    $textInput = new TextInput();
    $textInput->setText($text);
    $textInput->setLanguageCode($languageCode);

    // Create QueryInput
    $queryInput = new QueryInput();
    $queryInput->setText($textInput);
    
    return $queryInput;
  }

  /**
   * Request Dialogflow for fulfillment of user request.
   * 
   * @param string client requrest
   * @param string session ID if any
   */
  public function detectIntent(string $text, $sessionId = NULL) {
    $projectId = $this->config_factory->get('dialogflow_project');
    $languageCode = $this->config_factory->get('dialogflow_langcode');
    if (empty($sessionId) || $sessionId == 0 || $sessionId == '0') {
      $sessionId = rand(1, 1000);
    }
    $session = $this->client->sessionName($projectId, $sessionId ?: uniqid());
    $queryInput = $this->prepareRequest($text, $languageCode);

    // Request Dialogflow
    $response = $this->client->detectIntent($session, $queryInput);
    $queryResult = $response->getQueryResult();
    $queryText = $queryResult->getQueryText();
    $intent = $queryResult->getIntent();
    $displayName = $intent->getDisplayName();
    $confidence = $queryResult->getIntentDetectionConfidence();
    $parameters = $queryResult->getParameters();
    $fulfillmentText = $queryResult->getFulfillmentText();

    $this->client->close();

    return ['session' => $sessionId, 'message' => $fulfillmentText];
  }

}
