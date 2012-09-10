<?php

// deploy cache
use Nette\Caching\Cache;

class SQLCacheManager extends ProductManager
{

    public function requestContent($object, $request, $tag=NULL,
            $expiration = NULL)
    {
        $cache = Nette\Environment::getCache();
        $options = array();


        if (!isset($cache[$object])) {
            $result = dibi::fetchAll($request);

            if ($expiration !== NULL)
                    $options[Nette\Caching\Cache::EXPIRE] = $expiration;

            if ($tag !== NULL) $options[Nette\Caching\Cache::TAGS] = $tag;
            $cache->save($object, $result, $options);
        }

        return $cache[$object];
    }

    public static function removeContent($object)
    {
        $cache = Nette\Environment::getCache();

        // fixed for productlist
        if ($object == 'ProductAll') {
            $cache->clean(array(Nette\Caching\Cache::TAGS =>
                array('Productlist')));
        }

        if (isset($cache[$object])) {
            $cache->save($object, NULL);
        }
    }

}