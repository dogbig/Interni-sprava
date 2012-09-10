<?php

class User extends Nette\Object
{

    /** @var Nette\Security\IAuthenticator */
    private $authenticator;

    public function __construct(Nette\Security\IAuthenticator $authenticator)
    {
        $this->authenticator = $authenticator;
    }

    public static function factory(Nette\DI\IContainer $context)
    {
        return new self($context->authenticator);
    }

    public function delete($id)
    {
        dibi::delete('is_users')
                ->where('id = %i', $id)->execute();
        dibi::delete('is_todo')
                ->where('user_id = %i', $id)->execute();
    }

    public function save(array $data, $id)
    {
        if (!empty($data['password'])) {
            $hashedpw = $this->authenticator->calculateHash(
                    $data['password']);
            $data['password'] = $hashedpw;
        } else {
            unset($data['oldpassword']);
            unset($data['newpassword_check']);

            if (!empty($data['newpassword'])) {    // fix for profile editing
                $hashedpw = $this->authenticator->calculateHash(
                        $data['newpassword']);
                $data['password'] = $hashedpw;
            }

            unset($data['newpassword']);
        }

        return dibi::update('is_users', $data)
                        ->where('id = %i', $id)->execute();
    }

}