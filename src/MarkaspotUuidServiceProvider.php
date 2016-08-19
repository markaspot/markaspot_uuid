<?php
namespace Drupal\markaspot_uuid;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

class MarkaspotuuidServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   * https://www.drupal.org/node/2026959
   */
  public function register(ContainerBuilder $container) {
    // Overrides uuid class to provide customizable uuids.
    $definition = $container->getDefinition('uuid');
    $definition->setClass('Drupal\markaspot_uuid\plugin\uuid\MarkaspotUuid');
  }


}