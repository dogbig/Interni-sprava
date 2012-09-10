<?php

Use \Nette\Environment;
Use Nette\Application\UI\Form as NetteForm;

/**
 * Customers presenter.
 *
 * @author     Michal Charvát
 */
class CustomersPresenter extends BasePresenter
{

    /** @persistent */
    public $itemsPerPage = 12;
    
    /** @persistent */
    public $prevlisted = true;
    
    
    /** @persistent */
    public $selectorActive;
    
    private $accessRes = 'customers';    
    public $itemsPerPageSelector = 10;
    
    private $editing = false;
    private $id;
    private $service;
    private $data;
    
    /** @persistent */
    private $backlink;
    
    /** @persistent */
    private $backlink2;
      
    /**
    * @notoken
    */
    public function handleBack() {
        if ($this->backlink !== NULL) {
                $this->application->restoreRequest($this->backlink);
        }
        $this->redirect('Customers:');
    }
   
    public function actionDefault($customerSearched = NULL,
            $adressSearched = NULL, $hwKey = NULL, $onlyTrCenters = NULL)
    {
        $this->context->authorizator->authorizeMe($this->accessRes, 'access');
        $this->context->authorizator->authorizeMe($this->accessRes, 'list');

        if (!($this->selectorActive == true)) $this->selectorActive = false;
        
        $CustomerManager = new CustomerManager;
        if (($customerSearched === NULL) & ($adressSearched === NULL) &
                ($hwKey === NULL) & ($onlyTrCenters === NULL)) {
            $this->template->isSearch = false;
            $count = $CustomerManager->count();
        } else {
            $this->template->isSearch = true;
            $this->template->searchedCustomer = $customerSearched;
            $this->template->adressSearched = $adressSearched;
            $this->template->hwKeySearched = $hwKey;
            $count = $CustomerManager->count($customerSearched, $adressSearched,
                    $hwKey, $onlyTrCenters);
        }

        $this->template->allcustomercount = $CustomerManager->count();
        $this->template->customercount = $count;
        $paginator = $this['pageChooser']->getPaginator();
        $paginator->setItemsPerPage($this->itemsPerPage);
        $paginator->setItemCount($count);

        $customers = $CustomerManager->getOffsetCust(
                $paginator->getItemsPerPage(), $paginator->getOffset(),
                $customerSearched, $adressSearched, $hwKey, $onlyTrCenters);
        $this->template->customers = $customers;
        $this->template->backlink = $this->application->storeRequest();

    }
    
    public function actionSearchCust($selectorActive 
            = FALSE) {
        $this->selectorActive = $selectorActive;
    }

    public function actionSelector($customerSearched = NULL,
            $adressSearched = NULL, $hwKey = NULL, $onlyTrCenters = NULL)
    {
        $this->context->authorizator->authorizeMe($this->accessRes, 'access');
        $this->selectorActive = true;
        $this->actionDefault($customerSearched, $adressSearched, $hwKey,
                $onlyTrCenters);
    }

    public function actionView($id, $backlink = null)
    {
        $this->backlink = $backlink;
        $AnnualServiceManager = new AnnualServiceManager;
        if (($this->service = $AnnualServiceManager->hasService($id)) != FALSE) {
            $this->template->hasService = true;
            $this->template->serviceStarts = $this->service->datestart;
        } else {
            $this->template->hasService = false;
        }
        $this->template->backlink2 = $this->application->storeRequest();
        

        $this->context->authorizator->authorizeMe($this->accessRes,
                'viewcustomer');
        $this->prevlisted = false;
        $this->id = $id;

        $CustomerManager = new CustomerManager;

        if (($this->template->data = $CustomerManager->getRow($id)) == FALSE) {
            throw new Nette\Application\BadRequestException('Customer 
               ' . $id . ' not found', 404);
        } else {
            $ActionManager = new ActionManager;
            $this->template->recentactions=$ActionManager
                    ->getCustomerRecentActions($id);
        }
    }

    public function handleDelete($id)
    {
        $this->context->authorizator->authorizeMe($this->accessRes,
                'deletecustomer');
        $Customer = new Customer;
        $Customer->delete($id);
        $this->redirect('this');
    }

    public function actionAddEdit($id, $isEdited, $backlink2)
    {
        $this->backlink2 = $backlink2;
        if ($isEdited == true) {
            $this->context->authorizator->authorizeMe($this->accessRes,
                    'editcustomer');

            $CustomerManager = new CustomerManager;
            if (($this->data = $CustomerManager->getRow($id)) == FALSE) {
                throw new Nette\Application\BadRequestException('Customer 
               ' . $id . ' not found', 404);
            }

            $this->id = $id;
            $this->editing = true;
            $this->template->editing = $this->editing;
        } else {
            $this->context->authorizator->authorizeMe($this->accessRes,
                    'addcustomer');
            $this->editing = false;
            $this->template->editing = $this->editing;
        }
    }

    public function loadCustomersSelector()
    {
        $this->context->authorizator->authorizeMe($this->accessRes, 'list');
        $CustomerManager = new CustomerManager;
        $count = $CustomerManager->count();
        $paginator = $this['pageChooser']->getPaginator();
        $paginator->setItemsPerPage($this->itemsPerPageSelector);
        $paginator->setItemCount($count);
        $customers = $CustomerManager->getOffsetCust(
                $paginator->getItemsPerPage(), $paginator->getOffset()
        );
        $this->template->customers = $customers;
    }

    protected function createComponentSearchBoxForm()
    {
        $form = new NetteForm;
        $form->addText('cust', 'Název:')->addRule($form::MAX_LENGTH,
                'Název
                    zákazníka může být dlouhý max 80 znaků.',
                80);
        $form->addText('adr', 'Adresa:')
                ->addRule($form::MAX_LENGTH,
                        'Adresa nemůže být delší než 100 znaků.', 100);
        $form->addText('hwkey', 'HW klíč:')
                ->addRule($form::MAX_LENGTH,
                        'Číslo HW klíče nemůže být delší než 20 znaků', 20);
        $form->addCheckbox('onlyTr', 'pouze školící střediska');
        $form->addSubmit('find1', 'Hledat')
                ->setAttribute('class', 'btn btn-info');
        $form->onSuccess[] = callback($this, 'SearchBoxFormSubmitted');
        return $form;
    }

    public function SearchBoxFormSubmitted($form)
    {
        $arr = (array) $form->values;
        $customerSearched = $arr["cust"];
        $adressSearched = $arr["adr"];
        $hwKey = $arr["hwkey"];
        $onlyTr = $arr["onlyTr"];
        if ($this->selectorActive == true) {
            $this->redirect("Customers:selector", $customerSearched,
                    $adressSearched, $hwKey, $onlyTr);
        } else {
            $this->redirect("Customers:default", $customerSearched,
                    $adressSearched, $hwKey, $onlyTr);
        }
    }

    protected function createComponentAddEditCustomer()
    {
        $form = new NetteForm;
        $form->addText('name', 'Název zákazníka:')
                ->addRule($form::FILLED, 'Jméno zákazníka nemůže být prázdné!')
                ->addRule($form::MAX_LENGTH,
                        'Název
                    zákazníka může být dlouhý max 80 znaků.',
                        80);
        $form->addCheckbox('trcenter', 'školící středisko');
        $form->addText('ic', 'IČ:')
                ->addRule($form::INTEGER, 'IČ musí být číslo!')
                ->addRule($form::MAX_LENGTH,
                        'IČ nemůže být delší než 12 znaků.', 12);
        $form->addText('dic', 'DIČ:')
                ->addRule($form::MAX_LENGTH,
                        'DIČ nemůže být delší než 17 znaků.', 17);
        $form->addText('tel', 'Telefon:')
                ->addRule($form::MAX_LENGTH,
                        'Telefoní číslo nemůže být delší než 18 znaků.', 18);
        $form->addText('tel2', 'Telefon 2 (mobil):')
                ->addRule($form::MAX_LENGTH,
                        'Telefoní číslo nemůže být delší než 18 znaků.', 18);
        $form->addText('hwkeynum', 'HW klíč(e):')
                ->addRule($form::MAX_LENGTH,
                        'Číslo HW klíče(ů) nemůže být delší než 20 znaků.', 20);
        $form->addText('email', 'eMail:')
                ->addRule($form::MAX_LENGTH,
                        'Email nemůže být delší než 50 znaků.', 50);
        $form->addTextArea('adress', 'Adresa:')
                ->addRule($form::MAX_LENGTH,
                        'Adresa nemůže být delší než 150 znaků.', 150);
        $form->addTextArea('notes', 'Poznámky:')
                ->addRule($form::MAX_LENGTH,
                        'Poznámka je příliš dlouhá. 
                    Max, 800 znaků.',800);
        $form->addSubmit('add', 'Přidat');

        if ($this->editing == true) {
            $form->addSubmit('addSave', 'Uložit změny')
                    ->setAttribute('class', 'btn btn-warning');
            $form->setDefaults(array(
                'name' => $this->data->name,
                'trcenter' => $this->data->trcenter,
                'ic' => $this->data->ic,
                'dic' => $this->data->dic,
                'tel' => $this->data->tel,
                'tel2' => $this->data->tel2,
                'hwkeynum' => $this->data->hwkeynum,
                'email' => $this->data->email,
                'adress' => $this->data->adress,
                'notes' => $this->data->notes,
            ));
        } else {
            $form->addSubmit('addSave', 'Přidat nového zákazníka')
                    ->setAttribute('class', 'btn btn-success');
        }

        $form->addProtection('Vypršel časový limit, odešlete formulář znovu.');
        $form->onSuccess[] = callback($this, 'addEditCustomerSubmitted');
        return $form;
    }

    public function addEditCustomerSubmitted($form)
    {
        $Customer = new Customer;
        $CustomerManager = new CustomerManager;
        if ($this->editing == true) {
            $Customer->save((array) $form->values, $this->id);
            
            if ($this->backlink2 !== NULL) {
                    $this->application->restoreRequest($this->backlink2);
                } 
            if ($this->prevlisted == false) {
                $this->redirect('Customers:view', $this->id);
            } else {
                $this->redirect('Customers:');
            }
        } else {
            $CustomerManager->create((array) $form->values);
            $this->redirect('Customers:');
        }
    }

    protected function createComponentPageChooser($name)
    {
        return new \TachoScan\Addons\VisualPaginator($this, $name);
    }

}
