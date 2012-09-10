<?php

Class ListOfProduct
{

    public function delete($id)
    {
        return dibi::delete('is_distributions')
                        ->where('id = %i', $id)->execute();
    }

    public function deleteAll($productid)
    {
        return dibi::delete('is_distributions')
                        ->where('product_id = %i', $productid)->execute();
    }

    public function save(array $data, $id)
    {
        unset($data['company']);
        return dibi::update('is_distributions', $data)
                        ->where('id = %i', $id)->execute();
    }

}