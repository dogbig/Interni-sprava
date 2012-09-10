<?php

Use \Nette\Environment as Env;
Use Nette\Application\UI\Form as NetteForm;

/**
 * Distribution presenter.
 *
 * @author     Michal Charvát
 */
class DistributionPresenter extends BasePresenter
{

    /** @persistent */
    public $itemsPerPage = 15;

    /** @persistent */
    public $prevlisted = true;
    private $name, $id, $note, $version, $idHelper, $data, $custHelper;
    private $editing = false;
    private $accessRes = 'distribution';

    public function actionDefault()
    {
        $this->context->authorizator->authorizeMe($this->accessRes, 'access');
    }

    public function renderDefault()
    {
        $this->template->distributed = 'nezjištěno';
        if ($this->getUser()->isLoggedIn()) {
            $this->loadproducts();
        }
    }

    public function actionAddEdit($id, $isEdited)
    {
        if ($isEdited == true) {
            $this->context->authorizator->authorizeMe($this->accessRes,
                    'editproduct');
            $this->id = $id;
            $ProductManager = new ProductManager;

            if (($product = $ProductManager->getRow($id)) == FALSE) {
                throw new Nette\Application\BadRequestException('Product 
               ' . $id . ' not found', 404);
            }

            $this->name = $product->name;
            $this->version = $product->version;
            $this->note = $product->note;
            $this->editing = true;
            $this->template->editing = $this->editing;
        } else {
            $this->context->authorizator->authorizeMe($this->accessRes,
                    'addproduct');
            $this->editing = false;
            $this->template->editing = $this->editing;
        }
    }

    public function actionViewList($id)
    {
        $this->context->authorizator->authorizeMe($this->accessRes, 'viewlist');
        $this->prevlisted = false;
        $this->id = $id;

        $DistributionListManager = new DistributionListManager;
        $ProductManager = new ProductManager;

        if (($list = $DistributionListManager->getRow($id)) == FALSE) {
            throw new Nette\Application\BadRequestException('List 
               ' . $id . ' not found', 404);
        }

        $this->template->listdata = $list;
        $this->template->productId = $list->product_id;

        $this->template->productName =
                $ProductManager->getName($list->product_id);
    }

    public function actionAddEditList($id, $isEdited)
    {
        if ($isEdited == true) {
            $this->context->authorizator->authorizeMe($this->accessRes,
                    'editlist');
            $this->id = $id;
            $DistributionListManager = new DistributionListManager;
            
            if (($list = $DistributionListManager->getRow($id))==FALSE) {
                 throw new Nette\Application\BadRequestException('List 
               ' . $id . ' not found', 404);
            }            
            
            $this->data = $list;
            $this->idHelper = $list->product_id;
            $this->custHelper = $list->name;
            $this->editing = true;
            $this->template->editing = $this->editing;
        } else {
            $this->context->integrityManager->checkProductExistency($id);
            $this->context->authorizator->authorizeMe($this->accessRes,
                    'addlist');
            $this->id = $id;
            $this->editing = false;
            $this->template->editing = $this->editing;
        }
    }

    public function actionList($productid)
    {
        $this->context->integrityManager->checkProductExistency($productid);
        $this->context->authorizator->authorizeMe($this->accessRes, 'list');
        $this->prevlisted = true;
        $DistributionListManager = new DistributionListManager;
        $count = $DistributionListManager->count($productid);

        $paginator = $this['pageChooser']->getPaginator();
        $paginator->setItemsPerPage($this->itemsPerPage);
        $paginator->setItemCount($count);
        $list = $DistributionListManager->getOffsetDist($productid,
                $paginator->getItemsPerPage(), $paginator->getOffset());

        $this->template->list = $list;
        $this->template->productid = $productid;
        $this->template->distcount = $count;
        $ProductManager = new ProductManager;
        $this->template->productname = $ProductManager->getName($productid);
    }

    protected function createComponentPageChooser($name)
    {
        return new \TachoScan\Addons\VisualPaginator($this, $name);
    }

    public function loadproducts()
    {
        $ProductManager = new ProductManager;
        $DistributionListManager = new DistributionListManager;
        $products = $ProductManager->getAll();
        $this->template->products = $products;
        $this->template->productCount = count($products);
        $this->template->allcount = $DistributionListManager->countAll();
    }

    /**
     * @notoken
     */
    public function handleRefresh()
    {
        $ProductManager = new ProductManager;
        $ProductManager->refreshAllProducts();
        $this->redirect('this');
    }

    /**
     * @notoken
     */
    public function handleRefreshList($id)
    {
        $DistributionListManager = new DistributionListManager;
        $DistributionListManager->refreshAllDist($id);
        $this->redirect('this');
    }

    public function handleDelete($id)
    {
        $this->context->authorizator->authorizeMe($this->accessRes,
                'deleteproduct');

        $Product = new Product;
        $Product->delete($id);
        $ProductManager = new ProductManager;
        $ProductManager->refreshAllProducts();
        $this->redirect('this');
    }

    public function handleDeleteList($id)
    {
        $this->context->authorizator->authorizeMe($this->accessRes, 'deletelist');

        $ListOfProduct = new ListOfProduct;
        $ListOfProduct->delete($id);
        $ProductManager = new ProductManager;
        $ProductManager->refreshAllProducts();
        $this->redirect('this');
    }

    protected function createComponentAddEditProduct()
    {
        $form = new NetteForm;
        $form->addText('name', 'Jméno produktu:')
                ->addRule($form::FILLED, 'Jméno produktu nemůže být prázdné!')
                ->addRule($form::MAX_LENGTH,
                        'Název produktu je příliš dlouhý (max 20 znaků)!', 20);
        $form->addText('version', 'Verze:')
                ->addRule($form::FILLED, 'Verze produktu je vyžadována!')
                ->addRule($form::MAX_LENGTH,
                        'Verze produktu může být velká max 10 znaků!', 10);
        $form->addTextArea('note', 'Poznámky:')
                ->addRule($form::MAX_LENGTH,
                        'Poznámka může být dlouhá max 200 znaků!', 200);


        if ($this->editing == true) {
            $form->addSubmit('addSave', 'Uložit změny')
                    ->setAttribute('class', 'btn btn-warning');
            $form->setDefaults(array(
                'name' => $this->name,
                'version' => $this->version,
                'note' => $this->note,
            ));
        } else {
            $form->addSubmit('addSave', 'Přidat nový produkt')
                    ->setAttribute('class', 'btn btn-success');
        }

        $form->onSuccess[] = callback($this, 'AddEditProductSubmitted');
        return $form;
    }

    public function AddEditProductSubmitted($form)
    {
        $Product = new Product;
        $ProductManager = new ProductManager;
        if ($this->editing == true) {
            $Product->save((array) $form->values, $this->id);
        } else {
            $ProductManager->create((array) $form->values);
        }
        $ProductManager->refreshAllProducts();
        $this->redirect('Distribution:');
    }

    protected function createComponentAddEditList()
    {
        $form = new NetteForm;
        $form->addHidden('company_id');
        $form->addText('company', 'Zákazník:')
                ->addRule($form::FILLED, 'Je nutno vybrat zákazníka!')
                ->setDisabled();
        $form->addDatePicker('date', 'Datum:');
        $form->addText('sw_num', 'Číslo CD (i rozsah):')
                ->addRule($form::MAX_LENGTH,
                        'Rozsah příliš dlouhý (max 11 znaků)!', 11);
        $form->addText('quantity', 'Počet:')->addRule(
                        $form::FILLED, 'Počet je vyžadován!')
                ->addRule($form::MAX_LENGTH,
                        'Počet je příliš dlouhý (max 11 znaků)!', 11);
        $form->addTextArea('note', 'Poznámky:')
                ->addRule($form::MAX_LENGTH,
                        'Poznámka může být dlouhá max 250 znaků!', 250);


        if ($this->editing == true) {
            $form->addSubmit('addSave', 'Uložit změny')
                    ->setAttribute('class', 'btn btn-warning');
            $form->setDefaults(array(
                'company_id' => $this->data->company_id,
                'company' => $this->custHelper,
                'date' => $this->data->date,
                'sw_num' => $this->data->sw_num,
                'quantity' => $this->data->quantity,
                'note' => $this->data->note,
            ));
        } else {
            $form->addSubmit('addSave', 'Přidat')
                    ->setAttribute('class', 'btn btn-success');
        }

        $form->onSuccess[] = callback($this, 'AddEditListSubmitted');
        return $form;
    }

    public function AddEditListSubmitted($form)
    {
        $DistributionListManager = new DistributionListManager;
        $data = (array) $form->values;
        
        if ($data["date"] == NULL) {
            $data["date"] = date('Y-m-d');
        }
        
        
        if ($this->editing == true) {
            
            $ListOfProduct = new ListOfProduct;
            $ListOfProduct->save($data, $this->id);
            $DistributionListManager->refreshAllDist($this->id);
            if ($this->prevlisted == false) {
                $this->redirect('Distribution:viewlist', $this->id);
            } else {
                $this->redirect('Distribution:list', $this->idHelper);
            }
            $this->application->restoreRequest($this->backlink);
        } else {
            $this->context->integrityManager->checkProductExistency($this->id);
            $DistributionListManager->create($data, $this->id);
            $DistributionListManager->refreshAllDist($this->id);
        }
        $this->redirect('Distribution:list', $this->id);
    }

}
