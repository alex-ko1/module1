<?php

namespace Drupal\alex\Form;

use Drupal\file\Entity\File;
use Drupal\Core\Url;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;


class CatsDelete extends ConfirmFormBase {

  protected $id;

  public function getFormId() {
    return 'delete_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {
    $this->id = $id;
    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $query = \Drupal::database();
    $result = $query->select('alex', 'a')
      ->fields('a', ['image'])
      ->condition('id', $this->id)
      ->execute()->fetch();
    File::load($result->image)->delete();
    $query->delete('alex')
      ->condition('id', $this->id)
      ->execute();
    \Drupal::messenger()->addStatus('Successfully deleted.');
    $form_state->setRedirect('alex.content');
  }

  public function getQuestion() {
    $database = \Drupal::database();
    $result = $database->select('alex', 'a')
      ->fields('a', ['id', 'name'])
      ->condition('id', $this->id)
      ->execute()->fetch();
    return $this->t('Delete cat @cat_name with id-%id ?', ['%id' => $result-> id, '@cat_name' => $result-> name]);
  }

  public function getCancelUrl() {
    return new Url('alex.content');
  }
}
