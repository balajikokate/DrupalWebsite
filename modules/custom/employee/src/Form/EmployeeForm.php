<?php

namespace Drupal\employee\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Code\Database\Database;
/**
 * Implements an example form.
 */
class EmployeeForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'employee';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $genderOptions = array(
      '0'=>'Select Gender',
      'Male'=>'Male',
      'Female'=>'Female',
      'Other' =>'Other'

    );
    $form['name'] = array(
      '#type' =>'textfield',
      '#title'=>t('Name'),
      '#default_value'=>'',
      '#required'=>TRUE
    );
    $form['email'] = array(
      '#type' =>'email',
      '#title'=>t('Email_ID'),
      '#default_value'=>'',
      '#required'=>TRUE
    );
    $form['gender'] = array(
      '#type'=>'select',
      '#title'=>'Gender',
      '#options'=>$genderOptions,
      '#required'=>TRUE
      
    );
    // $form['phone_number'] = [
    //   '#type' => 'tel',
    //   '#title' => $this->t('Your phone number'),
    //   '#required'=>TRUE
    // ];
    $form['about_employee']= array(
      '#type'=>'textarea',
      '#title'=>"About Employee",
      '#default_value'=>"",
      '#required'=>TRUE
    );
    
  
    
    $form['save'] = array(
      '#type' => 'submit',
      '#value' => 'Save Employee',
      '#button_type' => 'primary',
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
        $name=$form_state->getvalue('name');
        if(trim($name) == '')
        {
         $form_state->setErrorByName('name', $this->t('Name Field Required'));
        }
  }
  //   if (strlen($form_state->getValue('phone_number')) < 3) {
  //     $form_state->setErrorByName('phone_number', $this->t('The phone number is too short. Please enter a full phone number.'));
  //   }
  // }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
   // $this->messenger()->addStatus($this->t('Your phone number is @number', ['@number' => $form_state->getValue('phone_number')]));
   $postData =$form_state->getvalues();
  //  echo "<pre>";
  //  print_r($postData);
  //  echo "</pre>";
  //  exit;
  unset($postData['save'],$postData['form_build_id'],$postData['form_token'],$postData['form_id'],$postData['op']);
  $query = \Drupal::database();
  $query->insert('employees')->fields($postData)->execute();
  drupal_set_message(t('Employee Data Saved Successfully'),'status',True);

  }

}