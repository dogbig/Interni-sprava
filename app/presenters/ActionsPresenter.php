<?php

Use Nette\Application\UI\Form as NetteForm;

/**
 * Actions presenter.
 *
 * @author     Michal Charvát
 */
class ActionsPresenter extends BasePresenter
{
    
    /** @persistent */
    private $backlink;

    /** @persistent */
    public $itemsPerPage = 10;
    private $accessRes = 'actions';
    private $id, $editing;
    private $actionData;
    
    public function actionDefault()
    {
        $this->context->authorizator->authorizeMe($this->accessRes, 'access');
    }

    public function actionAddEdit($id, $isEdited, $backlink = null)
    {
        $this->backlink = $backlink;
        if ($isEdited == true) {
            $this->context->authorizator->authorizeMe($this->accessRes, 'edit');
            $this->id = $id;
            $ActionManager = new ActionManager;
            if (($action = $ActionManager->getAction($id))==FALSE) {
                throw new Nette\Application\BadRequestException('Action  
               ' . $id . ' not found', 404);
            }
            $this->actionData["customer_id"] = $action->customer_id;
            $this->actionData["name"] = $action->name;
            $this->actionData["date"] = $action->date;
            $this->actionData["subject"] = $action->subject;
            $this->actionData["action_id"] = $action->action_id;
            $this->actionData["user_id"] = $action->user_id;
            $this->actionData["textnote"] = $action->textnote;
            $this->editing = true;
            $this->template->editing = $this->editing;
        } else {
            $this->context->authorizator
                    ->authorizeMe($this->accessRes, 'add');
            $this->template->editing = $this->editing = false;
        }
    }

    public function renderDefault()
    {
        $this->getActions();
    }

    public function handleDelete($id)
    {
        $this->context->authorizator->authorizeMe($this->accessRes, 'delete');
        $Action = new Action;
        $Action->delete($id);
        $this->redirect('this');
    }

    private function getActions()
    {
        $paginator = $this['pageChooser']->getPaginator();
        $paginator->setItemsPerPage($this->itemsPerPage);

        $ActionManager = new ActionManager;
        $count = $ActionManager->count();
        $this->context->authorizator->authorizeMe($this->accessRes, 'list');
        $paginator->setItemCount($count);
        $this->template->actions = $ActionManager->getAll(
                $paginator->getItemsPerPage(), $paginator->getOffset());
        $this->template->actionCount = $count;
    }

    protected function createComponentPageChooser($name)
    {
        return new \TachoScan\Addons\VisualPaginator($this, $name);
    }

    protected function createComponentAddEditAction()
    {
        $form = new NetteForm;
        $form->addHidden('customer_id');
        $form->addText('customer', 'Zákazník:')
                ->setDisabled();
        $form->addText('subject', 'Předmět:')
                ->addRule($form::FILLED, 'Předmět musí být vyplněn!')
                ->addRule($form::MAX_LENGTH,
                        'Předmět musí mít maximálně 30 znaků!', 30);
        $form->addDatePicker('date', 'Datum uskutečnění:');
        $form->addTextArea('textnote', 'Popis:');
        $form->onSuccess[] = callback($this, 'AddEditActionSubmitted');
        if ($this->editing == true) {
            $form->addSubmit('addSave', 'Uložit změny')
                    ->setAttribute('class', 'btn btn-warning');
            $form->setDefaults(array(
                'subject' => $this->actionData["subject"],
                'customer_id' => $this->actionData["customer_id"],
                'customer' => $this->actionData["name"],
                'date' => $this->actionData["date"],
                'textnote' => $this->actionData["textnote"],
            ));
        } else {
            $form->addSubmit('addSave', 'Přidat novou akci')
                    ->setAttribute('class', 'btn btn-success');
        }
        return $form;
    }

    public function AddEditActionSubmitted($form)
    {
        $Action = new Action;
        $ActionManager = new ActionManager;
        $data = (array) $form->values;
        
        $data = (array) $form->values;
        
        if ($data["date"] == NULL) {
            $data["date"] = date('Y-m-d');
        }

        if ($this->editing == true) {
            $Action->save($data, $this->id);
        } else {
            $ActionManager->create($data);
        }
        if ($this->backlink !== NULL) {
            $this->application->restoreRequest($this->backlink);
        }
        $this->redirect('Actions:');
    }

}

