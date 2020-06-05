<?php

namespace Drupal\chatbot\Plugin\rest\resource;

use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Drupal\chatbot\Services\ChatbotDetectIntent;
use Drupal\chatbot\Services\CovidStatistics;
use Drupal\Core\Cache\CacheableMetadata;

/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "chatbot_rest_resource",
 *   label = @Translation("Dialogflow detected Intents"),
 *   uri_paths = {
 *     "canonical" = "/chatbot/get-intents"
 *   }
 * )
 */
class ChatbotRestResource extends ResourceBase {

    /**
     * A current user instance.
     *
     * @var \Drupal\Core\Session\AccountProxyInterface
     */
    protected $currentUser;

    /**
     * Intents detection service.
     *
     * @var \Drupal\chatbot\Services\ChatbotDetectIntent
     */
    protected $intents;

    /**
     * Constructs a new ChatbotRestResource object.
     *
     * @param array $configuration
     *   A configuration array containing information about the plugin instance.
     * @param string $plugin_id
     *   The plugin_id for the plugin instance.
     * @param mixed $plugin_definition
     *   The plugin implementation definition.
     * @param array $serializer_formats
     *   The available serialization formats.
     * @param \Psr\Log\LoggerInterface $logger
     *   A logger instance.
     * @param \Drupal\Core\Session\AccountProxyInterface $current_user
     *   A current user instance.
     * @param Symfony\Component\HttpFoundation\Request $current_request
     *   The current request
     * @param \Drupal\chatbot\Services\ChatbotDetectIntent $intents
     *   Chatbot request service for intent response
     * @param \Drupal\chatbot\Services\CovidStatistics $statistics
     *   Get CODIV-19 statistics by country
     */
    public function __construct(
        array $configuration,
        $plugin_id,
        $plugin_definition,
        array $serializer_formats,
        LoggerInterface $logger,
        AccountProxyInterface $current_user,
        Request $current_request,
        ChatbotDetectIntent $intents,
        CovidStatistics $statistics) {
        parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);

        $this->currentUser = $current_user;
        $this->currentRequest = $current_request;
        $this->intents = $intents;
        $this->statistics = $statistics;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->getParameter('serializer.formats'),
            $container->get('logger.factory')->get('chatbot'),
            $container->get('current_user'),
            $container->get('request_stack')->getCurrentRequest(),
            $container->get('chatbot.intents'),
            $container->get('covid.statistics')
        );
    }

    /**
     * 
     */
    protected function substituteWebHook(array $message) {
        if (strpos($message['message'], 'Infected people in your country') !== FALSE) {
            preg_match('/Infected people in your country (.*) is/', $message['message'], $country);
            $statisticsByCountry = $this->statistics->substituteCountryStatistics($country[1]);
            if (!empty($statisticsByCountry)) {
                $confirmed = $statisticsByCountry->latest->confirmed;
                $recovered = $statisticsByCountry->latest->recovered;
                $death = $statisticsByCountry->latest->deaths;
                $message['message'] .= " confirmed: $confirmed, recovered: $recovered, death: $death by $statisticsByCountry->last_updated.";
            }
            else {
                $message['message'] = 'Your country wasnt found';
            }
        }
        return $message;
    }

    /**
     * Responds to GET requests.
     *
     * @param string $payload
     *
     * @return \Drupal\rest\ResourceResponse
     *   The HTTP response object.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *   Throws exception expected.
     */
    public function get($payload) {
        if (!$this->currentUser->hasPermission('access content')) {
            throw new AccessDeniedHttpException();
        }
        $query = $this->currentRequest->query;
        $message = $query->get('message');
        $session = $query->get('session');

        $message = $this->intents->detectIntent(urldecode($message), (int) $session);
        $message = $this->substituteWebHook($message);
        $cache_metadata = new CacheableMetadata();
        $cache_metadata->setCacheTags(['node_list']);
        $cache_metadata->addCacheContexts(['url.query_args', 'url.path']);

        $response = new ResourceResponse([$message], 200);
        return $response->addCacheableDependency($cache_metadata);
    }

}
