<?php

/**
 * Base presenter
 *
 * @author     Michal Charvát
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{

    public function beforeRender()
    {

        // Dynamicaly created menu along with user permissions
        $this->template->menuItems = array(
            'Domů' => 'Homepage:');

        if ($this->user->isAllowed('distribution', 'access')) {
            $this->template->menuItems['Distribuce produktů'] = 'Distribution:';
        }

        if ($this->user->isAllowed('customers', 'access')) {
            $this->template->menuItems['Zákazníci'] = 'Customers:';
        }

        if ($this->user->isAllowed('annualservices', 'access')) {
            $this->template->menuItems['Roční servisy'] = 'AnnualServices:';
        }


        if ($this->user->isAllowed('actions', 'access')) {
            $this->template->menuItems['Akce'] = 'Actions:';
        }

        if ($this->user->isAllowed('todo', 'access')) {
            $this->template->menuItems['Úkoly'] = 'Todo:';
        }



        if ($this->user->isAllowed('administration', 'access')) {
            $this->template->menuItems['Administrace'] = 'Administration:';
        }
    }

    public function link($destination, $args = array())
    {
        if (!is_array($args)) {
            $args = func_get_args();
            array_shift($args);
        }

        $a = strpos($destination, '#');
        if ($a === FALSE) {
            $fragment = '';
        } else {
            $fragment = substr($destination, $a);
            $destination = substr($destination, 0, $a);
        }

        if (substr($destination, -1) === '!' &&
                strpos($destination, '-') === FALSE) {
            $ref = $this->getReflection()
                    ->getMethod($this->formatSignalMethod(rtrim($destination,
                                    '!')));
            if (!$ref->getAnnotation('notoken')) {
                static $session;
                if ($session === NULL) {
                    $session = $this->getContext()->session
                            ->getSection($this->reflection->getName());
                }

                $field = 'signal' . rtrim($destination, '!');
                if (!isset($session->$field))
                        $session
                            ->$field = $token = base_convert(md5(uniqid($field,
                                            TRUE)), 16, 36);
                else $token = $session->$field;

                $args['token'] = $token;
            }
        }

        return parent::link($destination . $fragment, $args);
    }

    public function addError($message)
    {
        if ($message !== NULL) {
            $this->getPresenter()->flashMessage($message, 'fail');
        }
    }

    public function signalReceived($signal)
    {
        static $session;
        if ($session === NULL) {
            $session = $this->getContext()->session
                    ->getSection($this->reflection->getName());
        }

        $field = 'signal' . $signal;
        if ((!isset($this->params['token'])
                || !isset($session->$field) ||
                $session->$field != $this->params['token'])
                && !$this->getReflection()->getMethod($this
                                ->formatSignalMethod($signal))
                        ->getAnnotation('notoken')) {
            
        }

        unset($session->$field, $this->params['token']);
        parent::signalReceived($signal);
    }

    // Component of login form
    protected function createComponentSignInForm()
    {
        $form = new Nette\Application\UI\Form;
        $form->addText('username', 'Jméno:')
                ->setAttribute('class', 'input-mini')
                ->setAttribute('placeholder', 'jméno');
        $form->addPassword('password', 'Heslo:')
                ->setAttribute('class', 'input-mini')
                ->setAttribute('placeholder', 'heslo');
        $form->addSubmit('login', 'Přihlásit se')
                ->setAttribute('class',
                        array('btn', 'btn-success', 'btn-mini',
                    'toolTipDown'));
        $form->addCheckbox('rem', 'Zapamovat na tomto PC?');
        $form->onSuccess[] = callback($this, 'signInFormSubmitted');
        return $form;
    }

    // Login submit
    public function signInFormSubmitted($form)
    {
        $user = $this->getUser();
        $values = $form->getValues();
        try {
            if ($values->rem == TRUE) {
                $logoutOnClose = FALSE;
            } else {
                $logoutOnClose = TRUE;
            }

            // perm. login
            $user->setExpiration('+ 14 days', false);
            $user->login($values->username, $values->password);

            $this->flashMessage('Byl jste úspěšně přihlášen.', 'success');
        } catch (Nette\Security\AuthenticationException $e) {
            $this->addError($e->getMessage());
        }
    }

    // Logout action
    public function actionLogout()
    {
        $user = $this->getUser();
        if ($user->isLoggedIn()) {
            $user->logout();
            $this->flashMessage('Byl jste úspěšně odhlášen!');
            $this->redirect('Homepage:');
        }
    }

}
