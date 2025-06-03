<?php
class ControllerExtensionModuleLibCategory extends Controller {
	public function index($setting) {
		$this->load->language('extension/module/lib_category');

		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		$this->load->model('tool/image');

		$data['categories'] = array();
		if (!empty($setting['category'])) {
			$categories = array_slice($setting['category'], 0);
			foreach ($categories as $category_id) {
				$category_info = $this->model_catalog_category->getCategory($category_id);

				if ($category_info) {

					if ($category_info['image']) {
						$image = $this->model_tool_image->resize($category_info['image'], $setting['width'], $setting['height']);
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
					}
					

					$data['categories'][] = array(
						'category_id'  => $category_info['category_id'],
						'name'        => $category_info['name'],
						'thumb'       => $image,
						'href'        => $this->url->link('product/category', 'path=' . $category_info['category_id'])
						
					);

				}
			}

		}

		if ($data['categories']) {
			return $this->load->view('extension/module/lib_category', $data);
		}
	}
}