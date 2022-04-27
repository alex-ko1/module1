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
use Drupal\Core\Url;

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
  public function catsList()
  {
    $current_user = \Drupal::currentUser();
    $roles = $current_user->getRoles();
    $admin = "administrator";
    $query= \Drupal::database();
    $result = $query->select('alex', 'a')
      ->fields('a', ['name', 'email','image', 'timestamp','id'])
      ->orderBy('id', 'DESC')
      ->execute()->fetchAll();
    $data = [];
    foreach ($result as $row) {
      $file = File::load($row->image);
      $uri = $file->getFileUri();
      $catImage = [
        '#theme' => 'image',
        '#uri' => $uri,
        '#alt' => 'Cat',
        '#width' => 125,
        '#attributes' => [
          'target'=>['_blank']
          ],
      ];
      $data[] = [
        'name' => $row->name,
        'email' => $row->email,
        'image' => [
          'data' => $catImage,
        ],
        'timestamp' => date('d/m/Y H:i:s',$row->timestamp),
      ];
      if (in_array($admin, $roles)) {
        $url = Url::fromRoute('delete.content', ['id' => $row->id]);
        $url2 = Url::fromRoute('edit.content', ['id' => $row->id]);
        $delete_link = [
          '#title' => 'Delete',
          '#type' => 'link',
          '#url' => $url,
          '#attributes' => [
            'class' => ['use-ajax'],
            'data-dialog-type' => 'modal',
          ],
          '#attached' => [
            'library' => ['core/drupal.dialog.ajax'],
          ],
        ];
        $edit_link = [
          '#title' => 'Edit',
          '#type' => 'link',
          '#url' => $url2,
          '#attributes' => [
            'class' => ['use-ajax'],
            'data-dialog-type' => 'modal',
          ],
          '#attached' => [
            'library' => ['core/drupal.dialog.ajax'],
          ],
        ];
        $links['link'] = [
          'data' => [
            "#theme" => 'operations',
            'delete' => $delete_link,
            'edit' => $edit_link,
          ],
        ];
      $data[] = $links;
      }
    }
    $build['table'] = [
      '#type' => 'table',
      '#rows' => $data,
    ];
    return $build;
  }
}
