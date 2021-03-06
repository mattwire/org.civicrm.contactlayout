<?php

use CRM_Contactlayout_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Contactlayout_Form_Inline_ProfileBlock extends CRM_Profile_Form_Edit {

  /**
   * Form for editing profile blocks
   */
  public function preProcess() {
    if (!empty($_GET['cid'])) {
      $this->set('id', $_GET['cid']);
    }
    parent::preProcess();
    // Suppress profile status messages like the double-opt-in warning
    CRM_Core_Session::singleton()->getStatus(TRUE);
  }

  public function buildQuickForm() {
    parent::buildQuickForm();
    $buttons = array(
      array(
        'type' => 'upload',
        'name' => E::ts('Save'),
        'isDefault' => TRUE,
      ),
      array(
        'type' => 'cancel',
        'name' => E::ts('Cancel'),
      ),
    );
    $this->addButtons($buttons);
    $this->assign('help_pre', CRM_Utils_Array::value('help_pre', $this->_ufGroup));
    $this->assign('help_post', CRM_Utils_Array::value('help_post', $this->_ufGroup));

    // Special handling for contact id element
    if ($this->elementExists('id')) {
      $cidElement = $this->getElement('id');
      $cidElement->freeze();
      $cidElement->setValue($this->_id);
    }
  }

  /**
   * Save profiles
   *
   * @throws \CiviCRM_API3_Exception
   */
  public function postProcess() {
    $values = $this->exportValues();
    unset($values['id']);
    $values['contact_id'] = $cid = $this->_id;
    $values['profile_id'] = $this->_gid;
    $result = civicrm_api3('Profile', 'submit', $values);

    // These are normally performed by CRM_Contact_Form_Inline postprocessing but this form doesn't inherit from that class.
    CRM_Core_BAO_Log::register($cid,
      'civicrm_contact',
      $cid
    );
    $this->ajaxResponse = array_merge(
      CRM_Contact_Form_Inline::renderFooter($cid),
      $this->ajaxResponse,
      CRM_Contact_Form_Inline_Lock::getResponse($cid)
    );
  }

}
