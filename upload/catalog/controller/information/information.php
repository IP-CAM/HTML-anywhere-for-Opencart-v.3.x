<?php
class ControllerInformationInformation extends Controller {
	public function index() {
		$this->load->language('information/information');

		$this->load->model('catalog/information');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		if (isset($this->request->get['information_id'])) {
			$information_id = (int)$this->request->get['information_id'];
		} else {
			$information_id = 0;
		}

		$information_info = $this->model_catalog_information->getInformation($information_id);

		if ($information_info) {
			$this->document->setTitle($information_info['meta_title']);
			$this->document->setDescription($information_info['meta_description']);
			$this->document->setKeywords($information_info['meta_keyword']);

			$data['breadcrumbs'][] = array(
				'text' => $information_info['title'],
				'href' => $this->url->link('information/information', 'information_id=' .  $information_id)
			);

			$data['heading_title'] = $information_info['title'];

			$data['description'] = html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8');

			$data['continue'] = $this->url->link('common/home');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

            $view = $this->process_shortcodes($this->load->view('information/information', $data), $this->request->get['information_id']);

			$this->response->setOutput($view);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('information/information', 'information_id=' . $information_id)
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['heading_title'] = $this->language->get('text_error');

			$data['text_error'] = $this->language->get('text_error');

			$data['continue'] = $this->url->link('common/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}

    protected function process_shortcodes($view, $information_id) {
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
                        if ($module['display_place'] == '4') {
                            $html = $this->load->controller('extension/module/html_anywhere_nik', $module);
                            $view = str_replace($snippet_txt, $html, $view);
                        } else if ($module['display_place'] == '5') {
                            $is_this = strripos($module['selected_page'], 'information/information');
                            if ($is_this) {
                                $current_page_information_id = explode("information_id=", $module['selected_page']);
                                if ($current_page_information_id[1] == $information_id) {
                                    $html = $this->load->controller('extension/module/html_anywhere_nik', $module);
                                    $view = str_replace($snippet_txt, $html, $view);
                                } else {
                                    $view = str_replace($snippet_txt, '', $view);
                                }
                            } else {
                                $view = str_replace($snippet_txt, '', $view);
                            }
                        }
                    } else {
                        $view = str_replace($snippet_txt, '', $view);
                    }
                }
            }
        }

        return $view;
    }

	public function agree() {
		$this->load->model('catalog/information');

		if (isset($this->request->get['information_id'])) {
			$information_id = (int)$this->request->get['information_id'];
		} else {
			$information_id = 0;
		}

		$output = '';

		$information_info = $this->model_catalog_information->getInformation($information_id);

		if ($information_info) {
			$output .= html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8') . "\n";
		}

		$this->response->setOutput($output);
	}
}