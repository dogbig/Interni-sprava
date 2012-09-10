<?php

class Acl extends Nette\Security\Permission
{

    public function __construct()
    {

        // Define roles and resouces
        $this->addRole('guest');
        $this->addRole('n', 'guest');
        $this->addRole('a', 'n');
        $this->addRole('sa', 'a');
        $this->addResource('administration');
        $this->addResource('distribution');
        $this->addResource('userpanel');
        $this->addResource('customers');
        $this->addResource('annualservices');
        $this->addResource('actions');
        $this->addResource('todo');        
        $this->addResource('user');

        // Normal user
        $this->allow('n', 'userpanel', NULL);
        $this->allow('n', 'distribution', 'access');
        $this->allow('n', 'distribution', 'list');
        $this->allow('n', 'distribution', 'editlist');
        $this->allow('n', 'distribution', 'addlist');
        $this->allow('n', 'distribution', 'viewlist');
        $this->allow('n', 'distribution', 'deletelist');
        $this->allow('n', 'customers', 'access');
        $this->allow('n', 'customers', 'list');
        $this->allow('n', 'customers', 'viewcustomer');
        $this->allow('n', 'annualservices', 'access');
        $this->allow('n', 'annualservices', 'list');
        $this->allow('n', 'annualservices', 'add');
        $this->allow('n', 'annualservices', 'edit');
        $this->allow('n', 'annualservices', 'delete');
        $this->allow('n', 'actions', 'access');
        $this->allow('n', 'actions', 'list');
        $this->allow('n', 'actions', 'edit');
        $this->allow('n', 'actions', 'add');
        $this->allow('n', 'actions', 'delete');
        $this->allow('n', 'todo', 'access');
        $this->allow('n', 'todo', 'list');
        $this->allow('n', 'todo', 'deletemy');
        $this->allow('n', 'todo', 'editowntodo');
        $this->allow('n', 'todo', 'addown');
        $this->allow('n', 'todo', 'flagmy');
        $this->allow('n', 'user', 'access');
        $this->allow('n', 'user', 'list');

        // Admin can only remove, add and edit products.. also can add new customers        
        $this->allow('a', 'distribution', 'editproduct');
        $this->allow('a', 'distribution', 'deleteproduct');
        $this->allow('a', 'distribution', 'addproduct');
        $this->allow('a', 'customers', 'addcustomer');
        $this->allow('a', 'customers', 'import');
        $this->allow('a', 'customers', 'deletecustomer');
        $this->allow('a', 'customers', 'editcustomer');

        // SuperAdmin can fully access administration and any page
        $this->allow('sa', 'administration', NULL);
        // $this->deny('sa', 'administration', 'deleteuser');       
    }

    // Excelent simple authorize function, great...
    public function AuthorizeMe($accessRes, $accessAct)
    {

        if (!\Nette\Environment::getUser()->isAllowed($accessRes, $accessAct)) {
            throw new Nette\Application\BadRequestException(
                    $accessRes . '->' .
                    $accessAct, 403);
        }
    }
}