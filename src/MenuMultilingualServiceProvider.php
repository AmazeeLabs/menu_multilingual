<?php

namespace Drupal\menu_multilingual;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

/**
 * Modifies the default menu tree manipulator service.
 */
class MenuMultilingualServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    $definition = $container->getDefinition('menu.tree_storage');
    $definition->setClass('Drupal\menu_multilingual\Menu\MenuTreeStorageMultilingual');
  }

}
