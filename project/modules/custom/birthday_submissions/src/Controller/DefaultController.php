<?php

namespace Drupal\birthday_submissions\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\user\Entity\User;
/**
 * Class DefaultController.
 */
class DefaultController extends ControllerBase {

  /**
   * Display.
   *
   * @return string
   *   Return Hello string.
   */
  public function display() {
    $header_table = [
      'id' => '#',
      'first_name' => $this->t('First Name'),
      'last_name' => $this->t('Last Name'),
      'birthday' => $this->t('Birthday'),
      'email' => $this->t('Email'),
      'status' => $this->t('Status'),
      'uid' => $this->t('User'),
      'operations' => $this->t('Operations'),
    ];

    $query = \Drupal::database()->select('birthday_submissions', 'bs');
    $query->leftJoin('users_field_data', 'ufd', 'ufd.uid = bs.uid');
    $query->fields('bs', [
      'id',
      'first_name',
      'last_name',
      'birthday',
      'email',
      'status',
      'uid',
    ]);
    $query->fields('ufd', ['name']);
    $results = $query->execute()->fetchAll();
    $rows = [];

    foreach ($results as $data) {
      $update = Url::fromRoute('birthday_submissions.default_controller_update', [
        'id' => $data->id,
        'status' => $data->status,
      ]);
      $rows[] = [
        'id' => $data->id,
        'first_name' => $data->first_name,
        'last_name' => $data->last_name,
        'birthday' => date("m/d/Y", $data->birthday),
        'email' => $data->email,
        'status' => $data->status,
        'uid' => $data->name,
        'operations' => \Drupal::l($this->t('Status'), $update),
      ];
    }

    $form['table'] = [
      '#type' => 'table',
      '#header' => $header_table,
      '#rows' => $rows,
      '#empty' => $this->t('No records found'),
    ];

    return $form;
  }

  public function update($id, $status) {

    switch ($status) {
      case 'pending':
        $status = 'approve';
        break;
      case 'approve':
        $status = 'reject';
        break;
      case 'reject':
        $status = 'approve';
        break;
    }

    db_merge('birthday_submissions')
      ->key(['id' => $id])
      ->fields(['status' => $status])
      ->execute();

    $email = $this->hasEmail($id);

    if ($status === 'approve' && $email !== FALSE && user_load_by_mail($email) === FALSE) {
      $this->createUser($id);
    }

    $url = \Drupal::url('birthday_submissions.default_controller_display');
    return new RedirectResponse($url);

  }

  private function createUser($id) {
    $query = \Drupal::database()->select('birthday_submissions', 'bs');
    $query->condition('bs.id', $id);
    $query->fields('bs', [
      'email',
      'first_name',
      'last_name',
      'birthday'
    ]);
    $executed = $query->execute();
    $results = $executed->fetchAll(\PDO::FETCH_OBJ);

    foreach ($results as $data) {
      $email = $data->email;
      $first_name = $data->first_name;
      $last_name = $data->last_name;
      $birthday = $data->birthday;
      $username = $this->create_username_string($last_name,$first_name, $birthday);

      $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
      $user = User::create();

      $user->setPassword($username);
      $user->enforceIsNew();
      $user->setEmail($email);
      $user->setUsername($username);
      $user->set("init", 'email');
      $user->set("langcode", $language);
      $user->set("preferred_langcode", $language);
      $user->set("preferred_admin_langcode", $language);
      $user->activate();
      $user->save();

      _user_mail_notify('register_no_approval_required', $user);

    }
  }

  private function create_username_string($last_name, $first_name, $birthday){
    $last_name = $this->RemoveSpecialChar($last_name);
    $first_name = $this->RemoveSpecialChar($first_name);
    $birthday = date('YYYYMMDD',$birthday);
    return $first_name.$last_name.$birthday;
  }

  private function RemoveSpecialChar($value){
    $result  = preg_replace('/[^a-zA-Z0-9_ -]/s','',$value);

    return $result;
  }

  private function hasEmail($id) {
    $query = \Drupal::database()->select('birthday_submissions', 'bs');
    $query->condition('bs.id', $id);
    $query->fields('bs', ['email']);
    $executed = $query->execute();
    $results = $executed->fetchAll(\PDO::FETCH_OBJ);

    foreach ($results as $data) {
      if (!empty($data->email)) {
        return $data->email;
      }
    }

    return FALSE;
  }

}
