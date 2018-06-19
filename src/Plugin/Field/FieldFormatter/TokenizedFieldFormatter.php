<?php

namespace Drupal\token_formatters\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase;

/**
 * Plugin implementation of the 'tokenized_field_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "tokenized_field_formatter",
 *   label = @Translation("Tokenized field formatter"),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class TokenizedFieldFormatter extends EntityReferenceFormatterBase{

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
	'token_string'=>""
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form['token_string'] = [
      '#type'=>'textfield',
      '#title'=>t('Token to generate field markup'),
      '#default_value'=>$this->getSetting('token_string')
    ];
    if(\Drupal::moduleHandler()->moduleExists('token')) {
      $form['token_tree'] = [
        '#theme' => 'token_tree_link',
        '#token_types' => 'all',
        '#show_restricted' => TRUE,
      ];
    }
  return $form
     + parent::settingsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary[] = $this->getSetting('token_string');
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    $tokenString = $this->getSetting('token_string');
    $token_service = \Drupal::token();
    foreach ($this->getEntitiesToView($items, $langcode) as $delta => $item) {
      $elements[$delta] = ['#markup'=>$token_service->replace($tokenString, [$item->getEntityTypeId() => $item])];
    }
    return $elements;

  }

}
