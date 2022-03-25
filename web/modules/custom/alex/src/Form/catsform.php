<?php

namespace Drupal\alex\Form;

use Drupal\Core\Ajax\MessageCommand;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

class catsform extends FormBase
{

  public function getFormId()
  {
      return 'cats_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state)
  {
      $form['cat_name'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Your cat\'s name:'),
        '#placeholder' => $this->t('min length - 2, max - 32 symbols'),
        '#required' => true,
      ];
      $form['email'] = [
        '#title' => 'Your email:',
        '#type' => 'email',
        '#required' => true,
        '#placeholder' => $this->t('A-Z, a-z, -, _.'),
        '#ajax' => [
          'callback' => '::validateEmailAjax',
          'event' => 'input',
          'progress' => [
            'type' => 'none',
          ],
        ],
        '#suffix' => '<div class="email-validation-message"></div>'
      ];
    $form['my_file'] = array(
      '#type' => 'managed_file',
      '#title' => 'Add image:',
      '#name' => 'my_custom_file',
      '#description' => $this->t('format: jpg, jpeg, png <br> max-size: 2MB'),
      '#required' => TRUE,
      '#upload_validators' => [
        'file_validate_is_image' => array(),
        'file_validate_extensions' => array('jpg jpeg png'),
        'file_validate_size' => array(2097152)
      ],
      '#upload_location' => 'public://files',
    );

    $form['actions']['#type'] = 'actions';
      $form['actions']['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Add cat'),
        '#button_type' => 'primary',
        '#ajax' => [
          'callback' => '::setMessage',
        ],
      ];
      return $form;
  }

    public function validateForm(array &$form, FormStateInterface $form_state)
  {
    if (strlen($form_state->getValue('cat_name')) < 2) {
        $form_state->setErrorByName('cat_name', $this->t('Please enter a longer name.'));
    } elseif (strlen($form_state->getValue('cat_name')) >32) {
        $form_state->setErrorByName('cat_name', $this->t('Please enter a shorter name.'));
    }
    if (strpbrk($form_state->getValue('email'), '1234567890!#$%^&*()+=:;,`~?/<>\'±§[]{}|"')){
      $form_state->setErrorByName('email', $this->t('Please enter a valid email.'));
    }
  }
  public function validateEmailAjax(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    if (strpbrk($form_state->getValue('email'), '1234567890!#$%^&*()+=:;,`~?/<>\'±§[]{}|"')) {
      $response->addCommand(new HtmlCommand('.email-validation-message', 'Invalid email'));
    }
    else {
      # Убираем ошибку если она была и пользователь изменил почтовый адрес.
      $response->addCommand(new HtmlCommand('.email-validation-message', ''));
    }
    return $response;
  }

  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $image = $form_state->getValue('image');
    //If the image is uploaded, save it in the database
    if ($image) {
      $file = File::load($image[2097152]);
      $file->setPermanent();
      $file->save();
    }
    // Database connection
    $database = \Drupal::database();
    $database->insert('alex')
      ->fields([
        'name' => $form_state->getValue('cat_name'),
        'email' => $form_state->getValue('email'),
        'image' => $image[2097152],
      ])
      ->execute();
  }
  public function setMessage(array $form, FormStateInterface $form_state)
  {
    $response = new AjaxResponse();
    if ($form_state->hasAnyErrors()) {
      foreach ($form_state->getErrors() as $errors_array) {
          $response->addCommand(new MessageCommand($errors_array, NULL, ['type'=>'error']));
      }
    }
    else {
      $response->addCommand(new MessageCommand('You added a cat!'));
    }
    \Drupal::messenger()->deleteAll();
    return $response;
  }
}
