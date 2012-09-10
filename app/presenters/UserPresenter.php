<?php

Use \Nette\Environment;

/**
 * User presenter.
 *
 * @author     Michal Charvát
 */
class UserPresenter extends BasePresenter
{
    
    private $accessRes = 'user';

    public function actionDefault()
    {
        $this->context->authorizator->authorizeMe($this->accessRes, 'access');
    }

    public function actionFwselector()
    {
        $this->context->authorizator->authorizeMe($this->accessRes, 'access');
        $this->loadUsers();
    }

    public function loadUsers()
    {
        $this->context->authorizator->authorizeMe($this->accessRes, 'list');
        $this->template->users = $this->context->userManager->getAll();
    }   

}
