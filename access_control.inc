<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$ZEND_ACL = new Zend_Acl();
$ZEND_ACL->addRole(new Zend_Acl_Role('Anonymous'))
		 ->addRole(new Zend_Acl_Role('Public'), 'Anonymous')
		 ->addRole(new Zend_Acl_Role('Staff'), 'Public')
		 ->addRole(new Zend_Acl_Role('Administrator'), 'Staff');

/**
 * Declare all the resources
 */
$ZEND_ACL->add(new Zend_Acl_Resource('index'));
$ZEND_ACL->add(new Zend_Acl_Resource('people'));
$ZEND_ACL->add(new Zend_Acl_Resource('users'));
$ZEND_ACL->add(new Zend_Acl_Resource('login'));
$ZEND_ACL->add(new Zend_Acl_Resource('configuration'));
$ZEND_ACL->add(new Zend_Acl_Resource('simulator'));
$ZEND_ACL->add(new Zend_Acl_Resource('SMSinterface'));
$ZEND_ACL->add(new Zend_Acl_Resource('scheduledSMS'));
$ZEND_ACL->add(new Zend_Acl_Resource('scheduledSMSSimulator'));


/**
 * Assign permissions to the resources
 */
$ZEND_ACL->allow(null,'login');
$ZEND_ACL->allow(null,'SMSinterface');

// Permissions for unauthenticated browsing
$ZEND_ACL->allow(null,
				array('index'),
				array('index'));

// Allow Staff to do stuff
$ZEND_ACL->allow('Staff',
				array('people'),
				array('index','view'));

// Administrator is allowed access to everything
$ZEND_ACL->allow('Administrator');
