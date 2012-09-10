<?php

use Nette\Security as NS;

class Authenticator implements NS\IAuthenticator
{
    /**
     * Autorizace uživatele
     * @param  array
     * @return Nette\Security\Identity
     * @throws Nette\Security\AuthenticationException
     */
    
    public function authenticate(array $credentials)
    {
        list($username, $password) = $credentials;

        // will select the right row 
 
        $fluent = dibi::select('*')
                ->from('is_users')
                ->where('username=%s', $username);

        $row = $fluent->fetch();

        // check, if the user exist 
        if (!$row) {
            throw new NS\AuthenticationException("Uživatel '$username'
                    nebyl nalezen.", self::IDENTITY_NOT_FOUND);
        }

        // check, if the provided password is right againts hash 
        if ($row->password !== $this->calculateHash($password)) {
            throw new NS\AuthenticationException("Nesprávné heslo.",
                    self::INVALID_CREDENTIAL);
        }

        unset($row->password);
        return new NS\Identity($row->id, $row->acl, $row);
    }

    public function calculateHash($password)
    {
        return md5($password . str_repeat('hasheric45488sdw4', 10));
    }
}
