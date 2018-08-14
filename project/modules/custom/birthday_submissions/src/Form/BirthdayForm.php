<?php

namespace Drupal\birthday_submissions\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class BirthdayForm.
 */
class BirthdayForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'birthday_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['firts_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First Name'),
      '#maxlength' => 40,
      '#weight' => '0',
      '#required' => TRUE,
    ];
    $form['last_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Last Name'),
      '#maxlength' => 40,
      '#weight' => '0',
      '#required' => TRUE,
    ];
    $form['birthday'] = [
      '#type' => 'date',
      '#title' => $this->t('Birthday'),
      '#weight' => '0',
      '#required' => TRUE,
    ];
    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#weight' => '0',
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $field = $form_state->getValues();
    $user = \Drupal::currentUser();
    $uid = $user->id();
    $first_name = $field['firts_name'];
    db_merge('birthday_submissions')
      ->key(['email' => $field['email']])
      ->fields([
        'first_name' => $field['firts_name'],
        'last_name' => $field['last_name'],
        'birthday' => strtotime($field['birthday']),
        'uid' => $uid,
        'status' => 'pending',
      ])
      ->execute();
    drupal_set_message($this->t("Thanks $first_name, your information successfully saved"));

  }

}
