<?php
class ControllerExtensionModuleLibTab extends Controller {
	public function index($setting) {
		$this->load->language('extension/module/lib_tab');

		$this->load->model('catalog/product');
		$this->load->model('catalog/category');

		$this->load->model('tool/image');

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		$data['icon_latest'] = $server . 'catalog/view/theme/emarket/image/latest.png';
		$data['icon_featured'] = $server . 'catalog/view/theme/emarket/image/picked.png';
		$data['icon_topselling'] = $server . 'catalog/view/theme/emarket/image/topselling.png';

 		// Latest Product 
 		$data['prds_latest'] = array();
		if (!$setting['latest']) {
			$setting['latest'] = 5;
		}
		$results_latest = $this->model_catalog_product->getLatestProducts($setting['latest']);

		if ($results_latest) {

			foreach ($results_latest as $result_latest) {
				if ($result_latest['image']) {
					$image = $this->model_tool_image->resize($result_latest['image'], $setting['width'], $setting['height']);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result_latest['price'], $result_latest['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if (!is_null($result_latest['special']) && (float)$result_latest['special'] >= 0) {
					$special = $this->currency->format($this->tax->calculate($result_latest['special'], $result_latest['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					$tax_price = (float)$result_latest['special'];
				} else {
					$special = false;
					$tax_price = (float)$result_latest['price'];
				}
	

				if ($this->config->get('config_review_status')) {
					$rating = $result_latest['rating'];
				} else {
					$rating = false;
				}
			
				if(mb_strlen($result_latest['name'], 'UTF-8') > 40){
						$name = utf8_substr($result_latest['name'], 0, 40) . '...';
					} else {
						$name = $result_latest['name'];
				}

				$discount = round((($result_latest['price'] -  $result_latest['special'])/$result_latest['price']) * 100 ,0). '%';

				$data['prds_latest'][] = array(
					'product_id'  => $result_latest['product_id'],
					'thumb'       => $image,
					'name'        => $name,
					'price'       => $price,
					'special'     => $special,
					'rating'      => $rating,
					'discount'    => $discount,
					'href'        => $this->url->link('product/product', 'product_id=' . $result_latest['product_id'])
				);
			}

		}

		// Featured Product 
		
		$data['prds_featured'] = array();
	
		if (!empty($setting['product'])) {
			$products = array_slice($setting['product'], 0);

			foreach ($products as $product_id) {
				$product_info = $this->model_catalog_product->getProduct($product_id);
				
				if ($product_info) {
					if ($product_info['image']) {
						$image = $this->model_tool_image->resize($product_info['image'], $setting['width'], $setting['height']);
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
					}

					if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$price = false;
					}

					if (!is_null($product_info['special']) && (float)$product_info['special'] >= 0) {
						$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
						$tax_price = (float)$product_info['special'];
					} else {
						$special = false;
						$tax_price = (float)$product_info['price'];
					}
		
					if ($this->config->get('config_tax')) {
						$tax = $this->currency->format($tax_price, $this->session->data['currency']);
					} else {
						$tax = false;
					}

					if ($this->config->get('config_review_status')) {
						$rating = $product_info['rating'];
					} else {
						$rating = false;
					}

					$discount = round((($product_info['price'] -  $product_info['special'])/$product_info['price']) * 100 ,0). '%';

					if(mb_strlen($product_info['name'], 'UTF-8') > 40){
						$produc_name = utf8_substr($product_info['name'], 0, 40) . '...';
					} else {
						$produc_name = $product_info['name'];
					}

					$data['prds_featured'][] = array(
						'product_id'  => $product_info['product_id'],
						'thumb'       => $image,
						'name'        => $produc_name,
						'price'       => $price,
						'special'     => $special,
						'rating'      => $rating,
						'discount'    => $discount,
						'href'        => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
					);
				}
			}
		}

		// Top Selling
		$data['prds_topselling'] = array();
		if (!$setting['topselling']) {
			$setting['topselling'] = 5;
		}
		$results_topselling = $this->model_catalog_product->getBestSellerProducts($setting['topselling']);

		if ($results_topselling) {

			foreach ($results_topselling as $result_topselling) {
				if ($result_topselling['image']) {
					$image = $this->model_tool_image->resize($result_topselling['image'], $setting['width'], $setting['height']);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result_topselling['price'], $result_topselling['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if (!is_null($result_topselling['special']) && (float)$result_topselling['special'] >= 0) {
					$special = $this->currency->format($this->tax->calculate($result_topselling['special'], $result_topselling['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					$tax_price = (float)$result_topselling['special'];
				} else {
					$special = false;
					$tax_price = (float)$result_topselling['price'];
				}
	

				if ($this->config->get('config_review_status')) {
					$rating = $result_topselling['rating'];
				} else {
					$rating = false;
				}
			
				if(mb_strlen($result_topselling['name'], 'UTF-8') > 40){
						$name = utf8_substr($result_topselling['name'], 0, 40) . '...';
					} else {
						$name = $result_topselling['name'];
				}
				
				$total_sales = $this->model_catalog_product->getProductSalesTotal($result_topselling['product_id']);

				$discount = round((($result_topselling['price'] -  $result_topselling['special'])/$result_topselling['price']) * 100 ,0). '%';

				$data['prds_topselling'][] = array(
					'product_id'  => $result_topselling['product_id'],
					'thumb'       => $image,
					'name'        => $name,
					'price'       => $price,
					'special'     => $special,
					'rating'      => $rating,
					'discount'    => $discount,
					'total_sales'    => $total_sales,
					'href'        => $this->url->link('product/product', 'product_id=' . $result_topselling['product_id'])
				);
			}

		}

		// Render Data To View

		return $this->load->view('extension/module/lib_tab', $data);
		
	}
}