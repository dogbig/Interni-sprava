<?php

class DistributionListManager
{

    public function getAllDist($productid)
    {
        $fluent = dibi::select('*')
                ->from('is_distributions')
                ->orderBy('id')
                ->where('product_id = %i', $productid);

        return $fluent;
    }

    public function getOffsetDist($productid, $limit, $offset)
    {
        $fluent = dibi::select('is_distributions.id, is_customers.name,
            product_id, company_id, date, sw_num, quantity, note')
                ->from('is_distributions')
                ->leftJoin('is_customers')->on('is_customers.id = company_id')
                ->where('product_id = %i', $productid)
                ->orderBy('id desc');

        return $fluent->fetchAll($offset, $limit);
    }

    public function getRow($id)
    {
        $fluent = dibi::select('is_distributions.id, is_customers.name,
            product_id, company_id, date, sw_num, quantity, note')
                ->from('is_distributions')
                ->leftJoin('is_customers')->on('is_customers.id = company_id')
                ->where('is_distributions.id = %i', $id);

        return $fluent->fetch();
    }

    public function count($productid)
    {
        $fluent = dibi::select('COUNT(id)')
                ->from('is_distributions')
                ->where('product_id = %i', $productid);

        return $fluent->fetchSingle();
    }

    public function countAll()
    {
        $fluent = dibi::select('COUNT(id)')
                ->from('is_distributions');

        return $fluent->fetchSingle();
    }

    public function create(array $list, $productid)
    {
        $list["product_id"] = $productid;
        unset($list['company']);
        return dibi::insert('is_distributions', (array) $list)->execute();
    }

    public static function refreshAllDist($productid)
    {
        $SQLCacheManager = new SQLCacheManager;
        $SQLCacheManager->removeContent('ProductDist' . $productid);
    }

}