<?php

/* 
 * NOTE whole generator is obsolete in production use
 * and will be used only in dev 
 * 
 */

class DataGenerator
{

    public function generateRandomList($productid, $count)
    {
        for ($i = 1; $i <= $count; $i++) {
            $data['product_id'] = $productid;
            $data['company_id'] = rand(0, 999);
            $data['date'] = date('Y-m-d');
            $data['sw_num'] = rand(0, 50);
            $data['quantity'] = rand(0, 50);
            $data['note'] = $this->randomString(rand(20, 50));
            dibi::query('INSERT INTO [is_distributions]', (array) $data);
            $DistributionListManager = new DistributionListManager;
            $DistributionListManager->refreshAllDist($productid);
        }
    }
    
    public function generateRandomCustomers($count)
    {
        for ($i = 1; $i <= $count; $i++) {
            $data['name'] = $this->randomString(rand(20, 50));
            $data['ic'] = rand(18, 20);
            $data['dic'] = 'CZ'.rand(22, 30);            
            $data['tel'] = '+40 128 456 897';
            $data['email'] = $this->randomString(rand(5, 10)).'@'.$this->randomString(rand(10, 15).'.cz');
            dibi::query('INSERT INTO [is_customers]', (array) $data);
        }
    }

    public function randomString($l)
    {
        $random = "";
        srand((double) microtime() * 1000000);
        $char_list = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $char_list .= "abcdefghijklmnopqrstuvwxyz";
        $char_list .= "1234567890";

        for ($i = 0; $i < $l; $i++) {
            $random .= substr($char_list, (rand() % (strlen($char_list))), 1);
        }
        return $random;
    }

}