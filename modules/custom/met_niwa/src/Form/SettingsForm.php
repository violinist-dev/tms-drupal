<?php declare(strict_types = 1);

namespace Drupal\met_niwa\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure MET NIWA settings for this site.
 */
final class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'met_niwa_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['met_niwa.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['niwa'] = array(
      '#type' => 'fieldset',
      '#title' => t('NIWA API Connection'),
      '#collapsible' => TRUE, // Added
      '#collapsed' => FALSE,  // Added
    );

    $form['niwa']['url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('URL'),
      '#default_value' => $this->config('met_niwa.settings')->get('url'),
      ];

      $form['niwa']['endpoint'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Endpoint'),
        '#default_value' => $this->config('met_niwa.settings')->get('endpoint'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    // @todo Validate the form here.
    // Example:
    // @code
    //   if ($form_state->getValue('example') === 'wrong') {
    //     $form_state->setErrorByName(
    //       'message',
    //       $this->t('The value is not correct.'),
    //     );
    //   }
    // @endcode
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('met_niwa.settings')
      ->set('url', $form_state->getValue('url'))
      ->set('endpoint', $form_state->getValue('endpoint'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
