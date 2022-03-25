<?php
/**
 * @file
 * Contains \Drupal\alex\Controller\alex\cats.
 */
namespace Drupal\alex\Controller;
/*
 * Provides route for our custom module.
 */
use Drupal\file\Entity\File;
use Drupal\Core\Controller\ControllerBase;

class CatsController{
  /*
   * Display simple page.
   */
  public function content() {
    $form = \Drupal::formBuilder()->getForm('Drupal\alex\Form\catsform');
    return [
      '#theme' => 'cats-theme',
       '#form' => $form,
      '#list'=>$this->catsList(),
    ];
  }
  public function catsList(): array
  {
    $query= \Drupal::database();
    $result = $query->select('alex', 'a')
      ->fields('a', ['name', 'email', 'timestamp'])
      ->orderBy('id', 'DESC')
      ->execute()->fetchAll();
    $data = [];
    foreach ($result as $row) {
      $data[] = [
        'name' => $row->name,
        'email' => $row->email,
        //'image' => File::load($row->image)->getFileUri(),
        'timestamp' => $row->timestamp,
      ];
    }
    $build['table'] = [
      '#type' => 'table',
      '#rows' => $data,
    ];
    return $build;
  }
}
