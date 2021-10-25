<?php
class ControllerCommonHome extends Controller {
	public function index() {
		$this->document->setTitle($this->config->get('config_meta_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));

		if (isset($this->request->get['route'])) {
			$this->document->addLink($this->config->get('config_url'), 'canonical');
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$view = $this->process_shortcodes($this->load->view('common/home', $data));

		$this->response->setOutput($view);
	}

    protected function process_shortcodes($view) {
        $this->load->model('setting/module');

        $matches = array();
        // get shortcode from text
        preg_match_all('/\[(.*?)\\]/s', $view, $matches);

        if(isset($matches[1])) {
            foreach ($matches[1] as $match) {
                $snippet_txt = '['. $match . ']';
                $match = explode('-', $match);
                if (isset($match[0]) && $match[0] == 'html_anywhere_nik') {
                    $module = $this->model_setting_module->getModule($match[1]);

                    if ($module['status'] == '1') {
                        if ($module['display_place'] == '1') {
                            $html = $this->load->controller('extension/module/html_anywhere_nik', $module);
                            $view = str_replace($snippet_txt, $html, $view);
                        }
                    } else {
                        $view = str_replace($snippet_txt, '', $view);
                    }
                }
            }
        }

        return $view;
    }
}
