<?php

namespace Drupal\birthday_submissions\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'DefaultBlock' block.
 *
 * @Block(
 *  id = "birthday_block_form",
 *  admin_label = @Translation("Birthday Form Block"),
 * )
 */
class DefaultBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\birthday_submissions\Form\BirthdayForm');
    return $form;
  }

}
