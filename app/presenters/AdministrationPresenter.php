<?php

Use \Nette\Environment;

/**
 * Administration presenter.
 *
 * @author     Michal Charvát
 */
class AdministrationPresenter extends BasePresenter
{

    private $id;
    private $username, $_acl;
    private $editing = false;
    private $accessRes = 'administration';

    public function actionDefault()
    {
        $this->context->authorizator->authorizeMe($this->accessRes, 'access');
    }

    public function renderDefault()
    {
        // Load list of users
        $this->loadUsers();
        // Get basic info
        $this->template->appver = "Snapshot 16/05/2012";
        $this->template->phpver = phpversion();
        $this->template->nettever = "Nette Framework 2.0";
    }

    public function actionAddEditUser($id, $isEdited)
    {
        if ($isEdited == true) {
            $this->context->authorizator->authorizeMe($this->accessRes,
                    'edituser');
            $this->id = $id;
            $user = $this->context->userManager->getRow($id);
            $this->username = $user->username;
            $this->_acl = $user->acl;
            $this->editing = true;
            $this->template->editing = $this->editing;
        } else {
            $this->context->authorizator->authorizeMe($this->accessRes,
                    'adduser');
            $this->editing = false;
            $this->template->editing = $this->editing;
        }
    }

    public function loadUsers()
    {
        $users = $this->context->userManager->getAll();
        $this->template->users = $users;
    }

    public function handleDeleteUser($id)
    {

        $this->context->authorizator->authorizeMe($this->accessRes, 'deleteuser');

        if ($this->context->userManager->count() < 2) {
            $this->flashMessage('Nelze mít nultý počet uživatelů!', 'error');
        } elseif ($this->getUser()->getId() == $id) {
            $this->flashMessage('Nelze smazat sám sebe!', 'error');
        } else {

            $this->context->userModel->delete($id);
            $this->redirect('this');
            if ($this->isAjax()) {
                $this->payload->message = 'Success';
            }
        }
    }

    protected function createComponentAddEditUser()
    {
        $form = new Nette\Application\UI\Form;
        $form->addText('username', 'Nick uživatele:')->addRule(
                $form::FILLED, 'Nick uživatele nemůže být prázdný!');
        $form->addPassword('password', 'Heslo:')->addRule(
                $form::FILLED, 'Heslo je vyžadováno!');
        $acl = array(
            'n' => 'Standartní',            
            'a' => 'Admin',
            'sa' => 'SuperAdmin'
        );
        $form->addSelect('acl', 'Oprávnění:', $acl)->addRule(
                $form::FILLED, 'Uživateli musí být přidělena nějaká role!');
        
        if ($this->editing == true) {
            $form->addSubmit('addSave', 'Uložit změny')
                    ->setAttribute('class', 'btn btn-warning');
            $form->setDefaults(array(
                'username' => $this->username,
                'acl' => $this->_acl,
            ));
        } else {
            $form->addSubmit('addSave', 'Přidat uživatele')
                    ->setAttribute('class', 'btn btn-success');
        }
        
        $form->onSuccess[] = callback($this, 'addEditUserSubmitted');
        return $form;
    }

    public function addEditUserSubmitted($form)
    {
        
        if ($this->editing == true) {
            if ($this->getUser()->getId() == $this->id) {
               if ($form->values->acl == "n") {
                   $this->flashMessage('Nelze snížit svá oprávnění!', 'error');
                   $form->values->acl = "sa";
               } 
            } 
            $this->context->userModel->save((array) $form->values, $this->id); 

            
        } else {
            $this->context->userManager->create((array) $form->values);
        }
        $this->redirect('Administration:');
    }


    protected function createComponentGenerateListData()
    {
        $form = new Nette\Application\UI\Form;
        $form->addText('productid', 'ID produktu')
                ->addRule($form::FILLED, 'Nutno zadat číslo produktu!')
                ->addRule($form::INTEGER, 'ID produktu musí být číslo!');
        $form->addText('num', 'Počet:')
                ->addRule($form::FILLED, 'Nutno zadat POČET!')
                ->addRule($form::INTEGER, 'POČET musí být číslo!')
                ->addRule($form::RANGE, 'Počet musí být v rozsahu %d až %d',
                        array(1, 500));
        $form->addSubmit('go', ' Generuj ');
        $form->onSuccess[] = callback($this, 'generateListDataSubmitted');
        return $form;
    }

    public function generateListDataSubmitted($form)
    {
        $data = $form->values;

        // exist that product?
        $sqlrequest = 'SELECT id FROM [is_products] WHERE [id]=' .
                $data->productid;
        $test = dibi::fetchsingle($sqlrequest);

        if ($test !== false) {
            $DataGenerator = new DataGenerator;
            $DataGenerator->generateRandomList($data->productid, $data->num);
            $this->flashMessage('Náhodná data distribucí vygenerována.',
                    'success');
        } else {
            $this->flashMessage('Takovéto ID produktu neexistuje!', 'error');
        }
    }

    /* customer data gen. */

    protected function createComponentGenerateCustomerData()
    {
        $form = new Nette\Application\UI\Form;
        $form->addText('num', 'Počet:')
                ->addRule($form::FILLED, 'Nutno zadat POČET!')
                ->addRule($form::INTEGER, 'POČET musí být číslo!')
                ->addRule($form::RANGE, 'Počet musí být v rozsahu %d až %d',
                        array(1, 500));
        $form->addSubmit('go', ' Generuj ');
        $form->onSuccess[] = callback($this, 'generateCustomerDataSubmitted');
        return $form;
    }

    public function generateCustomerDataSubmitted($form)
    {
        $data = $form->values;
        $DataGenerator = new DataGenerator;
        $DataGenerator->generateRandomCustomers($data->num);
        $this->flashMessage('Náhodná data zákazníků vygenerována.', 'success');
    }

}
