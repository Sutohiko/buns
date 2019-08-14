<?php
public function Total(){ // функция возвращает кол-во товаров найденных фильтром до его применения

        $this->load->model('catalog/product');

        if (isset($this->request->post['filter'])) {
            $filter = $this->request->post['filter'];
        } else {
            $filter = false;
        }

        if (isset($this->request->post['category_id'])) {
            $category_id = $this->request->post['category_id'];
        } else {
            $category_id = false;
        }

        $json = array();

        if ($filter != false && $category_id != false) {

                $filter_data = array(
                    'filter_category_id' => $category_id,
                    'filter_filter'      => $filter,
                );

                $product_total = $this->model_catalog_product->getTotalProducts($filter_data);

                $json['success'] = $product_total;

        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }