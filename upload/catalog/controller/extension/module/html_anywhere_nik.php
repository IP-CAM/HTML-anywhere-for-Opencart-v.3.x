<?php
class ControllerExtensionModuleHTMLAnywhereNik extends Controller {
	public function index($setting) {
	    static $module_id = 0;
		if (isset($setting['module_description'][$this->config->get('config_language_id')])) {
			$data['heading_title'] = html_entity_decode($setting['module_description'][$this->config->get('config_language_id')]['title'], ENT_QUOTES, 'UTF-8');
			$data['html'] = html_entity_decode($setting['module_description'][$this->config->get('config_language_id')]['description'], ENT_QUOTES, 'UTF-8');

			$data['module_id'] = $module_id;
			$module_id++;

			return $this->load->view('extension/module/html_anywhere_nik', $data);
		}
	}
}