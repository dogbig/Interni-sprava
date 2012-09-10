<?php

class ProductManager
{

    public function getAll()
    {
        $fluent = dibi::select('*')
                ->from('is_products')
                ->orderBy('id');
        return $fluent;
    }

    public function getRow($id)
    {
        $fluent = dibi::select('*')
                ->from('is_products')
                ->where('id = %i', $id);

        return $fluent->fetch(); 
    }

    public function getName($id)
    {        
        $fluent = dibi::select('name')
                ->from('is_products')
                ->where('id = %i', $id);

        return $fluent->fetchSingle(); 
    }

    public function count($where = NULL)
    {
        return dibi::fetchSingle('SELECT COUNT([id]) FROM [is_products] %if',
                        isset($where), 'WHERE', isset($where) ? $where : array()
        );
    }

    public function create(array $product)
    {
        return dibi::insert('is_products', (array) $product)->execute();
    }

    public static function refreshAllProducts()
    {
        $SQLCacheManager = new SQLCacheManager;
        $SQLCacheManager->removeContent('ProductAll');
    }

}