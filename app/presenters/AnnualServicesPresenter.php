<?php

Use Nette\Application\UI\Form as NetteForm;

/**
 * AnnualServices Presenter
 *
 * @author     Michal Charvát
 */

class AnnualServicesPresenter extends BasePresenter
{

    /** @persistent */
    public $itemsPerPage = 10;
    private $accessRes = 'annualservices';
    private $id, $editing;
    private $serviceData;

    public function actionDefault()
    {
        $this->context->authorizator->authorizeMe($this->accessRes, 'access');
    }

    public function actionAddEdit($id, $isEdited)
    {
        if ($isEdited == true) {
            $this->context->authorizator->authorizeMe($this->accessRes, 'edit');
            $this->id = $id;
            $AnnualServiceManager = new AnnualServiceManager;
            if (($service = $AnnualServiceManager->getService($id))==FALSE) {
                throw new Nette\Application\BadRequestException('Annual service   
               ' . $id . ' not found', 404);
            }
            $this->serviceData["customer_id"] = $service->customer_id;
            $this->serviceData["name"] = $service->name;
            $this->serviceData["datestart"] = $service->datestart;
            $this->serviceData["anservice_id"] = $service->anservice_id;
            $this->serviceData["textnote"] = $service->textnote;
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
        $this->getServices();
    }

    public function handleDelete($id)
    {
        $this->context->authorizator->authorizeMe($this->accessRes, 'delete');
        $AnnualService = new AnnualService;
        $AnnualService->delete($id);
        $this->redirect('this');
    }

    private function getServices()
    {
        $paginator = $this['pageChooser']->getPaginator();
        $paginator->setItemsPerPage($this->itemsPerPage);

        $AnnualServiceManager = new AnnualServiceManager;
        $count = $AnnualServiceManager->count();
        $this->context->authorizator->authorizeMe($this->accessRes, 'list');
        $paginator->setItemCount($count);
        $this->template->services = $AnnualServiceManager->getAll(
                $paginator->getItemsPerPage(), $paginator->getOffset());
        $this->template->servicesCount = $count;
    }

    protected function createComponentPageChooser($name)
    {
        return new \TachoScan\Addons\VisualPaginator($this, $name);
    }

    protected function createComponentAddEditService()
    {
        $form = new NetteForm;
        $form->addHidden('customer_id');
        $form->addText('customer', 'Zákazník:')
                ->setDisabled();
        $form->addDatePicker('datestart', 'Datum počátku servisu:');
        $form->addTextArea('textnote', 'Poznámka / komunikace:');
        $form->onSuccess[] = callback($this, 'AddEditServiceSubmitted');
        if ($this->editing == true) {
            $form->addSubmit('addSave', 'Uložit změny')
                    ->setAttribute('class', 'btn btn-warning');
            $form->setDefaults(array(
                'customer_id' => $this->serviceData["customer_id"],
                'customer' => $this->serviceData["name"],
                'datestart' => $this->serviceData["datestart"],
                'textnote' => $this->serviceData["textnote"],
            ));
        } else {
            $form->addSubmit('addSave', 'Přidat nový servis')
                    ->setAttribute('class', 'btn btn-success');
        }
        return $form;
    }

    public function AddEditServiceSubmitted($form)
    {
        $AnnualService = new AnnualService;
        $AnnualServiceManager = new AnnualServiceManager;
        $data = (array) $form->values;
        
        if ($data["datestart"] == NULL) {
            $data["datestart"] = date('Y-m-d');
        }

        if ($this->editing == true) {
            $AnnualService->save($data, $this->id);
        } else {
            $AnnualServiceManager->create($data);
        }
        $this->redirect('AnnualServices:');
    }

}

