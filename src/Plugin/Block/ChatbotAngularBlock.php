<?php

namespace Drupal\chatbot\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'ChatbotAngularBlock' block.
 *
 * @Block(
 *  id = "chatbot_angular_block",
 *  admin_label = @Translation("ChatbotAngularBlock"),
 * )
 */
class ChatbotAngularBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'chatbot_dialogflow';

    return $build;
  }

}
