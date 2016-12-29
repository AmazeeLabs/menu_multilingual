<?php

namespace Drupal\menu_multilingual\Menu;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\menu_multilingual\Plugin\Menu\MenuLinkContentMultilingual;

/**
 * Class MenuMultilingualLinkTreeModifier.
 *
 * Used to filter out menu items.
 */
class MenuMultilingualLinkTreeModifier {

  /**
   * MenuMultilingualLinkTreeModifier constructor.
   *
   * @param bool $allow_labels
   *   The allow_label filter flag.
   * @param bool $allow_content
   *   The allow_content filter flag.
   */
  public function __construct($allow_labels = FALSE, $allow_content = FALSE) {
    $this->filter_labels  = $allow_labels;
    $this->filter_content = $allow_content;
  }

  /**
   * Pass menu links from render array of the block to the filter method.
   *
   * @param $build array
   *   The block render-able array.
   *
   * @return array
   *   The modified render-able array.
   */
  public function filterLinksInRenderArray($build) {
    $tree =& $build['content']['#items'];
    $tree = $this->filtersLinks($tree);
    // Hide block if there are no menu items.
    if (empty($tree)) {
      $build = array(
        '#markup' => '',
        '#cache' => $build['#cache'],
      );
    }
    return $build;
  }

  /**
   * Filter wrapper for either links or menu link tree.
   *
   * @param $tree array
   *
   * @return array
   *   The new menu tree.
   */
  public function filtersLinks($tree) {
    $new_tree = [];
    foreach ($tree as $key => $v) {
      if ($tree[$key]['below']) {
        $tree[$key]['below'] = $this->filtersLinks($tree[$key]['below']);
      }
      $link = $tree[$key]['original_link'];
      if ($this->hasTranslationOrIsDefaultLang($link)) {
        $new_tree[$key] = $tree[$key];
      }
    }
    return $new_tree;
  }

  /**
   * Check link for translation or current language.
   *
   * @param $link \Drupal\menu_multilingual\Plugin\Menu\MenuLinkContentMultilingual
   *
   * @return bool
   *   True if link pass a multilingual options.
   */
  protected function hasTranslationOrIsDefaultLang($link) {
    $current_lang = \Drupal::languageManager()->getCurrentLanguage()->getId();
    $result = FALSE;
    $has_translated_label  = FALSE;
    $has_translated_content = FALSE;

    if ($this->filter_labels) {
      $has_translated_label = $this->linkIsTranslated($link, $current_lang);
    }
    if ($this->filter_content) {
      $has_translated_content = $this->linkedEntityHasTranslationsOrIsDefault($link, $current_lang);
    }

    if ($this->filter_labels && $this->filter_content) {
      if ($has_translated_label && $has_translated_content) {
        $result = TRUE;
      }
    }
    else {
      if ($this->filter_labels) {
        $result = $has_translated_label;
      }
      elseif ($this->filter_content) {
        $result = $has_translated_content;
      }
    }

    return $result;
  }

  /**
   * Check link for translations or current language.
   *
   * @param $link \Drupal\menu_multilingual\Plugin\Menu\MenuLinkContentMultilingual
   *   The link that will be checked.
   * @param $lang
   *   The language id.
   *
   * @return bool
   *  True if link pass a multilingual options.
   */
  private function linkIsTranslated($link, $lang) {
    $result = FALSE;

    if (!method_exists($link, 'getLanguage')) {
      return TRUE;
    }
    if ($lang == $link->getLanguage()) {
      $result = TRUE;
    }
    elseif ($this->entityHasTranslation($link, $lang)) {
      $result = TRUE;
    }

    return $result;
  }

  /**
   * Check menu item link for translations or current language.
   *
   * @param $link \Drupal\menu_multilingual\Plugin\Menu\MenuLinkContentMultilingual
   *   The link that will be checked.
   * @param $lang
   *   The language id.
   *
   * @return bool
   *  True if link pass a multilingual options.
   */
  private function linkedEntityHasTranslationsOrIsDefault($link, $lang) {
    if (empty($link->getRouteName()) || strpos($link->getRouteName(), 'entity.') === FALSE) {
      return FALSE;
    }

    $type   = current(array_keys($link->getRouteParameters()));
    $id     = $link->getRouteParameters()[$type];
    $result = FALSE;

    if (empty($type) || empty($id)) {
      return FALSE;
    }

    /* @var \Drupal\Core\Entity\ContentEntityBase $entity */
    $entity = \Drupal::entityTypeManager()
      ->getStorage($type)
      ->load($id);

    if ($lang == $entity->get('langcode')) {
      $result = TRUE;
    }
    elseif ($this->entityHasTranslation($entity, $lang)) {
      $result = TRUE;
    }
    return $result;
  }

  /**
   * Helper method to check if entity is translateable.
   *
   * @param $entity MenuLinkContentMultilingual|ContentEntityBase
   * @param $lang
   *
   * @return bool
   */
  private function entityHasTranslation($entity, $lang) {
    // isTranslatable() will return "false" for:
    // Non-translatable entity,
    // entity with "Not specified" language,
    // entity with "Not applicable" language.
    if (!method_exists($entity, 'isTranslatable') || !$entity->isTranslatable()) {
      return TRUE;
    }
    $translation_codes = array_keys($entity->getTranslationLanguages());
    return in_array($lang, $translation_codes);
  }

}
