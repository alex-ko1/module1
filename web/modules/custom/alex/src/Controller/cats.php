<?php
/**
 * @file
 * Contains \Drupal\alex\Controller\alex\cats.
 */
namespace Drupal\alex\Controller;
/*
 * Provides route for our custom module.
 */
class cats{
  /*
   * Display simple page.
   */
  public function content(){
    return array(
      '#markup' => 'Hello! You can add here a photo of your cat.',
    );
  }
}
