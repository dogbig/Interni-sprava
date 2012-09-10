<?php

Use Nette\Application\UI\Form as NetteForm;
use \Nette\Environment as Env;

/**
 * Todo presenter.
 *
 * @author     Michal Charvát
 */
class TodoPresenter extends BasePresenter
{
    
    /** @persistent */
    private $backlink;

    /** @persistent */
    public $itemsPerPage = 10;
    
    private $accessRes = 'todo';
    private $id, $editing;
    private $todoData;
    private $prevUserName;

    public function actionDefault()
    {
        $this->context->authorizator->authorizeMe($this->accessRes, 'access');
    }

    public function actionAddEdit($id, $isEdited, $backlink = null)
    {
        $this->backlink = $backlink;
        if ($isEdited == true) {
            $this->context->authorizator->authorizeMe($this->accessRes,
                    'editowntodo');
            $this->id = $id;
            $TodoManager = new TodoManager;
            if (($todo = $TodoManager->getMyTodo($id,
                    $this->user->getId()))==FALSE) {
                throw new Nette\Application\BadRequestException('Todo  
               ' . $id . ' not found', 404);
            }
            $this->todoData["subject"] = $todo->subject;
            $this->todoData["tobedone"] = $todo->tobedone;
            $this->todoData["notes"] = $todo->notes;
            $this->editing = true;
            $this->template->editing = $this->editing;

            if ($todo->prevuser_id != $this->context->user->getId() &
                    $todo->prevuser_id !== NULL) {
                $this->template->prevUserName =
                        $this->context->userManager
                        ->getUserName($todo->prevuser_id);
            }
        } else {
            $this->context->authorizator
                    ->authorizeMe($this->accessRes, 'addown');
            $this->template->editing = $this->editing = false;
        }
    }

    public function renderDefault()
    {
        $this->getMyTodo();
    }

    public function handleDeleteMy($id)
    {
        $this->context->authorizator->authorizeMe($this->accessRes, 'deletemy');
        $Todo = new Todo;
        $Todo->deleteMy($id);
        $this->redirect('this');
    }
    
    public function handleDeleteDone()
    {
        $this->context->authorizator->authorizeMe($this->accessRes, 'deletemy');
        $TodoManager = new TodoManager;
        $TodoManager->deleteDone($this->user->getId());
        $this->redirect('this');
    }

    public function handlegotDone($id, $done = true)
    {
        $this->context->authorizator->authorizeMe($this->accessRes, 'flagmy');
        $this->context->integrityManager->checkTodoExistency($id, 
                $this->user->getId());
        $Todo = new Todo;
        $Todo->done($id, $done);
        $this->redirect('this');
    }

    private function getMyTodo()
    {
        $userId = $this->user->getId();
        $paginator = $this['pageChooser']->getPaginator();
        $paginator->setItemsPerPage($this->itemsPerPage);
        $TodoManager = new TodoManager;
        $count = $TodoManager->countMy($userId);
        $this->context->authorizator->authorizeMe($this->accessRes, 'list');
        $paginator->setItemCount($count);
        $this->template->myTodo = $TodoManager->getMy(
                $paginator->getItemsPerPage(), $paginator->getOffset(),
                $userId);
        $this->template->myCount = $count;
        $this->template->myCountDone = $TodoManager->countMy($userId, true);
    }

    protected function createComponentPageChooser($name)
    {
        return new \TachoScan\Addons\VisualPaginator($this, $name);
    }

    protected function createComponentAddEditTodo()
    {
        $this->context->authorizator->authorizeMe($this->accessRes,
                'editowntodo');
        $form = new NetteForm;
        $form->addText('subject', 'Předmět:')
                ->addRule($form::FILLED, 'Předmět musí být vyplněn!')
                ->addRule($form::MAX_LENGTH,
                        'Předmět může být dlouhý max 35 znaků!', 35);
        $form->addDatePicker('tobedone', 'Datum splnění:');
        $form->addText('fwto', 'Předat úkol:')
                ->setDisabled();
        $form->addHidden('fwto_id');
        $form->addTextArea('notes', 'Popis:')
                ->addRule($form::MAX_LENGTH,
                        'Popis může být dlouhý max 500 znaků!', 500);
        $form->onSuccess[] = callback($this, 'addEditTodoSubmitted');
        if ($this->editing == true) {
            $form->addSubmit('addSave', 'Uložit změny')
                    ->setAttribute('class', 'btn btn-warning');
            $form->setDefaults(array(
                'subject' => $this->todoData["subject"],
                'tobedone' => $this->todoData["tobedone"],
                'notes' => $this->todoData["notes"],
            ));
        } else {
            $form->addSubmit('addSave', 'Přidat úkol')
                    ->setAttribute('class', 'btn btn-success');
        }
        return $form;
    }

    public function addEditTodoSubmitted($form)
    {
        $Todo = new Todo;
        $TodoManager = new TodoManager;
        $Data = (array) $form->values;

        if ($this->editing == true) {
            $Todo->save($Data, $this->id);
        } else {
            $TodoManager->createMy($Data);
        }
        
        
        
        if ($this->backlink !== NULL) {
            $this->application->restoreRequest($this->backlink);
        }
        $this->redirect('Todo:');
    }

}

