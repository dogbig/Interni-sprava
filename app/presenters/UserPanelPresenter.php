<?php

Use \Nette\Environment;

/**
 * UserPanel presenter.
 *
 * @author     Michal Charvát
 */

class UserPanelPresenter extends BasePresenter
{

    private $userprofile;
    private $accessRes = 'userpanel';

    public function actionDefault()
    {
        $this->context->authorizator->authorizeMe($this->accessRes, 'access');
    }

    public function actionViewProfile()
    {
        $this->userprofile = $this->context->userManager
                ->getRow($this->user->getId());
        $preMyAcl = $this->userprofile->acl;
        $aclList = array(
            'sa' => 'SuperAdmin',
            'a' => 'Admin',
            'n' => 'Standartní',
        );
        $this->template->username = $this->userprofile->username;
        $this->template->myAcl = $aclList[$preMyAcl];
        
    }

}

