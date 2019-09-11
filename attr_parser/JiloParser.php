<?php
class ControllerToolJiloParser extends Controller {
    public function index() {

        if(isset($this->request->get['part'])){
            $part = $this->request->get['part'];
        }else{
            $part = -1;
        }

        printf('JiloParser start<br>');

        $count = $this->getProducts(-1); ?>

        <ul><?php
        for($i = 0; $i < $count; $i++) { ?>
            <li><a href="<?php echo preg_replace('/&.*/', '', $_SERVER['REQUEST_URI']); ?>&part=<?php echo $i; ?>">part: <?php echo $i; ?> </a></li>
        <?php } ?>
        </ul><?php

        printf('count return: %s<br>', $count);

        if($part != -1){
            printf('getProducts part %s start<br>', $part);
            $this->getProducts($part);
        }

        printf('JiloParser end<br>');


    }

    public function getProducts($parts)
    {

        $res = $this->db->queryNoFetch(sprintf(
                'select distinct p.product_id, pd.name
                        from %sproduct p
                        left join %sproduct_description pd ON (pd.product_id = p.product_id)
                        where p.status = 1'
                , DB_PREFIX, DB_PREFIX)
        );

        for ($set = array(); $row = $res->fetch_assoc(); $set[] = $row) ;
        $res->free();

        $products = array_chunk($set, 10000);

        if($parts == -1){
            return count($products);
        } else {

            foreach ($products[$parts] as $data) {

                preg_match('/(\d)х(\d{1,3})/', $data['name'], $matches); // достали аттрибуты

                $match = explode('х', $matches[0]); // разделили : 0 - количество жил , 1 - сечение


                if ($match[0] != '' && $match[0] != ' ' && $match[0] != '0') {

                    $parser_data = array(
                        'product_id' => $data['product_id'],
                        'attribute_id' => 41623, // 41623 - количество жил
                        'text' => $match[0]
                    );

                    $this->addAttribute($parser_data);

                }

                if ($match[1] != '' && $match[1] != ' ' && $match[1] != '0') {

                    $parser_data = array(
                        'product_id' => $data['product_id'],
                        'attribute_id' => 41624, //41624 - сечение
                        'text' => $match[1]
                    );

                    $this->addAttribute($parser_data);

                }

            }
        }
    }

    public function addAttribute($data){

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$data['product_id'] . "' AND attribute_id = '" . (int)$data['attribute_id'] . "'");

        if(!empty($query->row)) {
            $this->db->query("UPDATE " . DB_PREFIX . "product_attribute SET text = '" . $this->db->escape($data['text']) . "' WHERE product_id = '" . (int)$data['product_id'] . "' AND attribute_id = '" . (int)$data['attribute_id'] . "'");
        }else{
            $this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$data['product_id'] . "', attribute_id = '" . (int)$data['attribute_id'] . "', language_id = 1, text = '" . $this->db->escape($data['text']) . "'");
        }

    }

}