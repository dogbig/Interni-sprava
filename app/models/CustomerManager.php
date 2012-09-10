<?php

class CustomerManager
{

    public function getOffsetCust($limit, $offset, $custSearchHelper = NULL,
            $adressSearched = NULL, $hwKey = NULL, $isTrCenter = NULL)
    {

        $fluent = dibi::select('*')
                        ->from('is_customers')->orderBy('name');

        if ($custSearchHelper !== NULL) {
            $fluent->where('name')
                    ->like('%s', '%' . $custSearchHelper . '%');
        }
        if ($adressSearched !== NULL) {
            $fluent->where('adress')
                    ->like('%s', '%' . $adressSearched . '%');
        }
        if ($hwKey !== NULL) {
            $fluent->where('hwkeynum')
                    ->like('%s', '%' . $hwKey . '%');
        }

        if ($isTrCenter != NULL) {
            $fluent->where('trcenter =', $isTrCenter);
        }

        return $fluent->fetchAll($offset, $limit);
    }

    public function getRow($id)
    {
        $fluent = dibi::select('*')
                        ->from('is_customers')->where('id = %i', $id);

        return $fluent->fetch();
    }

    public function getName($id)
    {
        $fluent = dibi::select('name')
                        ->from('is_customers')->where('id = %i', $id);

        return $fluent->fetchSingle();
    }

    public function count($custSearchHelper = NULL,
            $adressSearched = NULL, $hwKey = NULL, $isTrCenter = NULL)
    {
        $fluent = dibi::select('COUNT(id)')->from('is_customers');
        
        if ($custSearchHelper !== NULL) {
            $fluent->where('name')
                    ->like('%s', '%' . $custSearchHelper . '%');
        }
        if ($adressSearched !== NULL) {
            $fluent->where('adress')
                    ->like('%s', '%' . $adressSearched . '%');
        }
        if ($hwKey !== NULL) {
            $fluent->where('hwkeynum')
                    ->like('%s', '%' . $hwKey . '%');
        }

        if ($isTrCenter != NULL) {
            $fluent->where('trcenter =', $isTrCenter);
        }

        return $fluent->fetchSingle();
    }

    public function create(array $data)
    {
        return dibi::insert('is_customers', $data)->execute();
    }

}