<?php

class AnnualService
{

    public function delete($id)
    {
        return dibi::delete('is_anservices')
                        ->where('anservice_id = %i', $id)
                        ->execute();
    }

    public function save(array $data, $id)
    {
        return dibi::update('is_anservices', $data)
                        ->where('anservice_id = %i', $id)
                        ->execute();
    }

}