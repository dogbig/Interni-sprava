<?php

class Product
{

    public function delete($id)
    {
        $ListOfProduct = new ListOfProduct;
        $ListOfProduct->deleteAll($id); // automatic deleting of list
        return dibi::delete('is_products')
                        ->where('id = %i', $id)->execute();
    }

    public function save(array $data, $id)
    {
        return dibi::update('is_products', $data)
                        ->where('id = %i', $id)->execute();
    }

}