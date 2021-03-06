<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../../../../UIComponent/UserInterfaceHook/Certificate/vendor/autoload.php';

/**
 * ilCertificateEventsPlugin
 *
 * @author  Stefan Wanzenried <sw@studer-raimann.ch>
 * @version $Id:
 */
class ilCertificateEventsPlugin extends ilEventHookPlugin {

	const PLUGIN_NAME = 'CertificateEvents';
	const PLUGIN_ID = 'cert_events';


	/**
	 * @param string $component
	 * @param string $event
	 * @param array  $parameters
	 */
	public function handleEvent($component, $event, $parameters) {
		// Generate certificate if course is completed
		switch ($component) {
			case 'Modules/Course':
				$course = NULL;
				if (isset($parameters['object']) && $parameters['object'] instanceof ilObjCourse) {
					$course = $parameters['object'];
				} elseif (isset($parameters['obj_id'])) {
					$course = new ilObjCourse(array_pop(ilObject::_getAllReferences($parameters['obj_id'])));
				}
				if (!$course) {
					return;
				}
				$handler = new srCertificateEventsCourseHandler($course);
				$handler->handle($event, $parameters);
				break;
			case 'Certificate/srCertificate':
				$certificate = $parameters['object'];
				$handler = new srCertificateEventsCertificateHandler($certificate);
				$handler->handle($event, $parameters);
				break;
		}
	}


	/**
	 * Get Plugin Name. Must be same as in class name il<Name>Plugin
	 * and must correspond to plugins subdirectory name.
	 *
	 * Must be overwritten in plugin class of plugin
	 * (and should be made final)
	 *
	 * @return    string    Plugin Name
	 */
	function getPluginName() {
		return self::PLUGIN_NAME;
	}


	protected function beforeUninstall() {
		// Nothing to delete
		return true;
	}
}