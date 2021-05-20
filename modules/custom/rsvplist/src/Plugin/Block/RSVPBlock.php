<?php
/**
 * @file
 * contains \Drupalrsplist\Plugin\Block\RSVPBlock
 */
    namespace Drupal\rsvplist\Plugin\Block;

    use Drupal\Core\Block\BlockBase;
    use Drupal\Core\Session\AccountInterface;
    use Drupal\Core\Access\AccessResult;
    

    /**
     * Provides an rsvplist Block
     * @Block(
     *  id = "rsvp_block",
     * admin_label = @Translation("RSVP Block"),
     * )
     */
    class RSVPBlock extends BlockBase{
        /**
         * {@inheritdoc}
         */

        public function build(){
            return \Drupal::formBuilder()->getForm('Drupal\rsvplist\Form\RSVPForm');

        }
        public function blockAccess(AccountInterface $account){
            /** @var \Drupal\node\Entity\Node $node */
            $node = \Drupal::routeMatch()->getParameter('node');
            
            if($node instanceof \Drupal\node\NodeInterface){
                $nid=$node->id();
            
            
           
            if(is_numeric($nid)){
                return AccessResult::allowedIfHasPermission($account,'view rsvplist');

            }
        }
            return AccessResult::forbidden();
        }
    }