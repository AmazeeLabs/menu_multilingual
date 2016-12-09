<?php

namespace Drupal\menu_multilingual\Menu;

use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\Core\Menu\MenuTreeStorage;

/**
 * Class MenuTreeStorageMultilingual.
 *
 * Used to change default menu link plugin class,
 * to add extra multilingual methods.
 */
class MenuTreeStorageMultilingual extends MenuTreeStorage {

  /**
   * {@inheritdoc}
   */
  protected function doCollectRoutesAndDefinitions(array $tree, array &$definitions) {
    foreach ($definitions as &$definition) {
      if ($definition['provider'] == 'menu_link_content') {
        $definition['class'] = 'Drupal\menu_multilingual\Plugin\Menu\MenuLinkContentMultilingual';
      }
    }
    return parent::doCollectRoutesAndDefinitions($tree, $definitions);
  }

  /**
   * {@inheritdoc}
   */
  protected function loadLinks($menu_name, MenuTreeParameters $parameters) {
    $links = parent::loadLinks($menu_name, $parameters);
    foreach ($links as &$link) {
      if ($link['provider'] == 'menu_link_content') {
        $link['class'] = 'Drupal\menu_multilingual\Plugin\Menu\MenuLinkContentMultilingual';
      }
    }
    return $links;
  }

}
