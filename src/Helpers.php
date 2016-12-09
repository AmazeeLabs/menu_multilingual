<?php

namespace Drupal\menu_multilingual;

use Drupal\block\BlockInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\menu_multilingual\Menu\MenuMultilingualLinkTreeModifier;

/**
 * Various fucntions to assis menu_mutilingual block.
 */
class Helpers {

  /**
   * Enable menu_multilingual block processing.
   */
  public static function setBlockProcessing(&$build) {
    $settings = $build['#block']->getThirdPartySettings('menu_multilingual');

    if (!empty($settings['only_translated_labels']) || !empty($settings['only_translated_content'])) {
      $modifier = new MenuMultilingualLinkTreeModifier(
        $settings['only_translated_labels'],
        $settings['only_translated_content']
      );
      $build['#pre_render'][] = [$modifier, 'filterLinksInRenderArray'];
    }
  }

  /**
   * Save menu_multilingual block settings.
   */
  public static function saveBlockSettings($entity_type, BlockInterface $block, &$form, FormStateInterface $form_state) {
    $settings = $form_state->getValue(['settings', 'multilingual']);
    $elements = $form['settings']['multilingual'];

    if ($elements['only_translated_labels']['#disabled']) {
      $settings['only_translated_labels'] = FALSE;
    }
    if ($elements['only_translated_content']['#disabled']) {
      $settings['only_translated_content'] = FALSE;
    }

    $block->setThirdPartySetting(
      'menu_multilingual',
      'only_translated_labels',
      $settings['only_translated_labels']
    );
    $block->setThirdPartySetting(
      'menu_multilingual',
      'only_translated_content',
      $settings['only_translated_content']
    );
  }

  /**
   * Check entity type for transaltion capabilities.
   */
  public static function checkEntityType($type) {
    /* @var \Drupal\content_translation\ContentTranslationManager $translationManager */
    $translationManager = \Drupal::service('content_translation.manager');
    return $translationManager->isEnabled($type);
  }

  /**
   * Updater for the menu_multilingual block settings.
   */
  public static function languageContentSettingsSubmit() {
    /* @var \Drupal\content_translation\ContentTranslationManager $translationManager */
    $translationManager = \Drupal::service('content_translation.manager');
    $settings = $translationManager->getSupportedEntityTypes();
    // @todo: Add bulk change for block settings.
    // Use power of https://goo.gl/cm37vj
  }

}
