<?php

class ActionManager
{
    private $selectQuery = 'is_actions.action_id, is_actions.customer_id, 
            is_actions.user_id, is_actions.date, is_actions.textnote,
            is_actions.subject, is_customers.name';
    
    // How to get all actions 
    public function getAll($limit, $offset)
    {
        $fluent = dibi::select($this->selectQuery)
                ->from('is_actions')
                ->leftJoin('is_customers')->on('is_customers.id = customer_id')
                ->orderBy('date desc');

        return $fluent->fetchAll($offset, $limit);
    }  
    
    // How to get one action
    public function getAction($id)
    {
        $fluent = dibi::select($this->selectQuery)
                ->from('is_actions')
                ->leftJoin('is_customers')->on('is_customers.id = customer_id')
                ->where('is_actions.action_id = %i',$id)
                ->orderBy('action_id desc');

        return $fluent->fetch();
    }
    
    // How to get oaction for one customer
    public function getCustomerRecentActions($custId)
    {
        $fluent = dibi::select($this->selectQuery)
                ->from('is_actions')
                ->leftJoin('is_customers')->on('is_customers.id = customer_id')
                ->where('customer_id = %i',$custId)
                ->limit(3)
                ->orderBy('date desc');

        return $fluent->fetchAll();
    }

    // Count actions
    public function count()
    {
        $fluent = dibi::select('COUNT(action_id)')
                ->from('is_actions');

        return $fluent->fetchSingle();
    }

    // Inser new action
    public function create(array $data)
    {
        return dibi::insert('is_actions', $data)->execute();
    }

}