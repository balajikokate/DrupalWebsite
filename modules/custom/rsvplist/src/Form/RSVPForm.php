<?php
    /**
     * @file
     * Contains \Drupal\rsvplist\Form\RSVPForm
     */
    namespace Drupal\rsvplist\Form;

    use Drupal\Core\Form\FormBase;
    use Drupal\Core\Form\FormStateInterface;
    use Drupal\Code\Database\Database;
    /**
    * Implements an example form.
    */
     class RSVPForm extends FormBase {

    /**
    * {@inheritdoc}
    */
    public function getFormId() {
    return 'rsvplist_email_form';
    }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state){
      $node= \Drupal::routeMatch()->getParameter('node');
        if($node instanceof \Drupal\node\NodeInterface){
            $nid=$node->id();
        
        
   
      
      $form['email'] = array(
          '#title'=>t('Email Address'),
          '#type'=>'textfield',
          '#size'=>25,
          '#description'=>t("we will send updates to the email address you provide"),
          '#required'=>TRUE,
      );
      $form['submit'] = array(
          '#type'=>'submit',
          '#value'=>t('RSVP'),

      );
      $form['nid']= array(
          '#type'=>'hidden',
          '#value'=>$nid,
      );
    }
      return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function  validateForm(array &$form, FormStateInterface $form_state)
  {
      $value = $form_state->getvalue('email');
      if($value == !\Drupal::service('email.validator')->isvalid($value)){
          $form_state->setErrorByName('email', t('The email address %mail is not valid', array('%mail'=>$value)));
      }
  }
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form,FormStateInterface $form_state){
      $user = \Drupal\user\Entity\user::load(\Drupal::currentUser()->id());
      db_insert('rsvplist')
      ->fields(array(
          'mail' => $form_state->getvalue('email'),
          'nid' => $form_state->getvalue('nid'),
          'uid' => $user->id(),
          'created' => time(),
      ))
      ->execute();
      drupal_set_message(t('thank you for RSVP, you are on the list for the event'));
}
     }