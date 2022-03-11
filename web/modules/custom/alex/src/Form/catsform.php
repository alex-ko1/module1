<?php
namespace Drupal\alex\Form;
 
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure example settings for this site.
 */
class catsform extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'cats_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['cat_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your cat\'s name:'),
      '#placeholder' => $this->t('min length - 2, max - 32 symbols'),
      '#required' => true
    ];
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add cat'),
      '#button_type' => 'primary',
    ];
    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (strlen($form_state->getValue('cat_name')) < 2) {
      $form_state->setErrorByName('cat_name', $this->t('The name is to short'));
    }
    elseif (strlen($form_state->getValue('cat_name')) > 32) {
      $form_state->setErrorByName('cat_name', $this->t('The name is to long'));
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    \Drupal::messenger()->addMessage($this->t("You added a cat!"));
  }
}
