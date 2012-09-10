<?php

class UserManager extends Nette\Object
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

    public function getAll()
    {
        $fluent = dibi::select('id, username')
                ->from('is_users')
                ->orderBy('id');
        return $fluent;
    }

    public function count()
    {
        return dibi::fetchSingle('SELECT COUNT([id]) FROM [is_users]');
    }

    public function getRow($id)
    {
        $fluent = dibi::select('username, acl')
                ->from('is_users')
                ->where('id = %i', $id);

        return $fluent->fetch();
    }
    
    public function getUserName($id)
    {
        $fluent = dibi::select('username')
                ->from('is_users')
                ->where('id = %i', $id);

        return $fluent->fetchSingle();
    }

    public function checkPass($id, $password)
    {

        $fluent = dibi::select('password')
                ->from('is_users')
                ->where('id = %i', $id);

        $hashedpass = $fluent->fetchSingle();

        if ($hashedpass == $this->authenticator->calculateHash($password)) {
            return true;
        } else {
            return false;
        }
    }

    public function create(array $newuser)
    {
        $hashedpw = $this->authenticator->calculateHash($newuser['password']);
        $newuser['password'] = $hashedpw;
 
        return dibi::insert('is_users', (array) $newuser)->execute();
    }

}