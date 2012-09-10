<?php

class HomepagePresenter extends BasePresenter
{


    public function renderDefault()
    {
        if ($this->getUser()->isLoggedIn()) {
            // store backlink for further requests
            $this->template->backlink = $this->application->storeRequest();
            
            $ActionType = 'list';
            // get preview of latest actions
            $ActionManager = new ActionManager;
            $this->context->authorizator->authorizeMe('actions', $ActionType);
            $this->template->actions = $ActionManager->getAll(10,0);
            // get preview of latest customers
            $CustomerManager = new CustomerManager;
            $this->context->authorizator->authorizeMe('customers', $ActionType);
            $this->template->customers = $CustomerManager->getOffsetCust(10,0);
            
            $TodoManager = new TodoManager;
            $this->context->authorizator->authorizeMe('todo', $ActionType);
            $this->template->myTodo = $TodoManager->getMy(15,0,$this->
                    user->getId());
        }
    }

}
