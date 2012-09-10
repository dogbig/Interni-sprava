<?php

class AnnualServiceManager
{
    // How to get all annual services 
    public function getAll($limit, $offset)
    {
        $fluent = dibi::select('is_anservices.anservice_id, 
            is_anservices.customer_id, is_anservices.datestart,
            is_anservices.textnote, is_customers.name')
                ->from('is_anservices')
                ->leftJoin('is_customers')->on('is_customers.id = customer_id')
                ->orderBy('anservice_id desc');

        return $fluent->fetchAll($offset, $limit);
    }  
    
    // How to get one annual service
    public function getService($id)
    {
        $fluent = dibi::select('is_anservices.anservice_id, 
            is_anservices.customer_id, is_anservices.datestart,
            is_anservices.textnote, is_customers.name')
                ->from('is_anservices')
                ->leftJoin('is_customers')->on('is_customers.id = customer_id')
                ->where('is_anservices.anservice_id = %i',$id)
                ->orderBy('anservice_id desc');

        return $fluent->fetch();
    }
    
    // Has service?
    public function hasService($custId)
    {
        $fluent = dibi::select('is_anservices.anservice_id, 
            is_anservices.customer_id, is_anservices.datestart')
                ->from('is_anservices')
                ->where('is_anservices.customer_id = %i',$custId);

        return $fluent->fetch();
    }

    // Count services
    public function count()
    {
        $fluent = dibi::select('COUNT(anservice_id)')
                ->from('is_anservices');

        return $fluent->fetchSingle();
    }

    // Inser new service
    public function create(array $data)
    {
        return dibi::insert('is_anservices', $data)->execute();
    }

}