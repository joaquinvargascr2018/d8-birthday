<?php

namespace Drupal\birthday_submissions\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class APIController.
 */
class APIController extends ControllerBase {

  /**
   * /birthday/get.json
   */
  public function get() {
    $query = \Drupal::database()->select('birthday_submissions', 'bs');
    $query->condition('bs.status', 'approve');
    $query->fields('bs', [
      'id',
      'email',
      'first_name',
      'last_name',
      'birthday'
    ]);
    $executed = $query->execute();
    $results = $executed->fetchAll(\PDO::FETCH_OBJ);
    $response['data'] = array();
    foreach ($results as $data) {
      $row = array(
        'id' => $data->id,
        'email' => $data->email,
        'first_name' => $data->first_name,
        'last_name' => $data->last_name,
        'birthday' => $data->birthday
      );

      array_push($response['data'], $row);

    }

    $response['method'] = 'GET';
    return new JsonResponse( $response );
  }

  public function put() {

    $response['data'] = 'Some test data to return';
    $response['method'] = 'PUT';
    return new JsonResponse( $response );
  }

  public function post() {

    $response['data'] = 'Some test data to return';
    $response['method'] = 'POST';
    return new JsonResponse( $response );
  }

  public function delete() {
    $response['data'] = 'Some test data to return';
    $response['method'] = 'DELETE';
    return new JsonResponse( $response );
  }

}
