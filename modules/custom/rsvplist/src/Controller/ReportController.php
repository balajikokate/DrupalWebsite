<?php
/**
 * @file
 * Contains \Drupal\rsvplist\ControlleR\ReportController.
 */
namespace Drupal\rsvplist\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;

/**
 * Controller for RSVP List Report
 */

class ReportController extends ControllerBase{
    /**
     * Gets All RSVPs for All nodes
     * 
     * @return array
     */
    protected function load(){
        $select = Database::getConnection()->select('rsvplist','r');
        //joims the user table so we can get entry creaters username
        $select->join('users_field_data','u','r.uid = u.uid');
        //join the node table ,so we can get the events name 
        $select->join('node_field_data','n','r.nid = n.nid');
        //select these specific fields for output.
        $select->addField('u','name','username');
        $select->addField('n','title');
        $select->addField('r','mail');
        $entries = $select->execute()->fetchAll(\PDO::FETCH_ASSOC);
        return $entries;
    }
    /**
     * Creates the report page 
     * 
     * @return array
     * Render array  for report output
     */
    public function report(){
        $content = array();
        $content['massage'] = array(
            '#markup'=>$this->t('Below is the list of all Event RSVPs including Username,email address and name of event they attending'),

        );
        $headers = array(
            t('Name'),
            t('Event'),
            t('Email'),

        );
        $rows = array();
        
        foreach ($entries = $this->load() as $entry){
            //sanitize each entry
            $rows[] = array_map('Drupal\Component\Utility\SafeMarkup::checkPlain', $entry);
        }
        $content['table'] = array(
            '#type'=> 'table',
            '#header'=> $headers,
            '#rows'=> $rows,
            '#empty'=> t('No entries Available'),
        );
        //dont cache the page
        $content['#cache'] ['max-age'] = 0;
        return $content;
    }
}