<?php

class IntegrityManager
{

    public static function factory(Nette\DI\IContainer $context)
    {
        return new self($context->integrityManager);
    }

    public function checkProductExistency($idToCheck)
    {
        $ProductManager = new ProductManager;
        if (($ProductManager->getRow($idToCheck)) == FALSE) {
            throw new Nette\Application\BadRequestException('Product 
               ' . $idToCheck . ' not found', 404);
        }
    }

    public function checklistExistency($listID)
    {
        $DistributionListManager = new DistributionListManager;
        if (($DistributionListManager->getRow($listID)) == FALSE) {
            throw new Nette\Application\BadRequestException('List 
               ' . $listID . ' not found', 404);
        }
    }

    public function checkCustomerExistency($custId)
    {
        $CustomerManager = new CustomerManager;
        if (($CustomerManager->getRow($custId)) == FALSE) {
            throw new Nette\Application\BadRequestException('Customer  
               ' . $custId . ' not found', 404);
        }
    }

    public function checkTodoExistency($todoId, $userId)
    {
        $TodoManager = new TodoManager;
        if (($TodoManager->getMyTodo($todoId, $userId)) == FALSE) {
            throw new Nette\Application\BadRequestException('Todo  
               ' . $todoId . ' not found', 404);
        }
    }

    public function checkActionExistency($actionId)
    {
        $ActionManager = new ActionManager;
        if (($ActionManager->getAction($actionId)) == FALSE) {
            throw new Nette\Application\BadRequestException('Action  
               ' . $actionId . ' not found', 404);
        }
    }

}