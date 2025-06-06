<?php
class ControllerExtensionModuleLibSpecial extends Controller {
	public function index($setting) {
		$this->load->language('extension/module/lib_special');

		$this->load->model('catalog/product');
		$this->load->model('catalog/category');
		$this->load->model('tool/image');

		$data['products'] = array();
		$data['autoplay'] = $setting['autoplay'];
		$data['timeout'] = $setting['timeout'];
		$data['url_special'] = $this->url->link('product/special');

		$filter_data = array(
			'sort'  => 'pd.name',
			'order' => 'ASC',
			'start' => 0,
			'limit' => $setting['limit']
		);

		$results = $this->model_catalog_product->getProductSpecials($filter_data);

		if ($results) {
			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if (!is_null($result['special']) && (float)$result['special'] >= 0) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					$tax_price = (float)$result['special'];
				} else {
					$special = false;
					$tax_price = (float)$result['price'];
				}
	
				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format($tax_price, $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = $result['rating'];
				} else {
					$rating = false;
				}

				$discount = round((($result['price'] -  $result['special'])/$result['price']) * 100 ,0). '%';

				$review = $this->model_catalog_product->getProductReviewTotal($result['product_id']);


				if(mb_strlen($result['name'], 'UTF-8') > 45){
					$name = utf8_substr($result['name'], 0, 45) . '...';
					} else {
					$name = $result['name'];
				}


				$data['products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $name,
					'price'       => $price,
					'special'     => $special,
					'rating'      => $rating,
					'discount'    => $discount,
					'review'    => $review,
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
				);
			}

			return $this->load->view('extension/module/lib_special', $data);
		}
	}
}