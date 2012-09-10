<?php

class Customer
{

    public function delete($id)
    {
        dibi::delete('is_customers')
                ->where('id = %i', $id)->execute();        
    }

    public function save(array $data, $id)
    {
        return dibi::update('is_customers', $data)
                        ->where('id = %i', $id)->execute();
    }

}