<?php
declare(strict_types=1);

namespace OncoAmazonPay;

use CI_DB_query_builder;
use CI_DB_result;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Query\QueryBuilder;
use LegacyDependencyContainer;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use StaticGXCoreLoader;

class DbAdapterNew
{

    public static function getQueryBuilder(): QueryBuilder
    {
        return self::getConnection()->createQueryBuilder();
    }

    protected static function getConnection(): Connection
    {
        return LegacyDependencyContainer::getInstance()->get(Connection::class);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public static function execute($q, $parameters = null)
    {
        self::getConnection()->executeQuery($q, $parameters ?: []);
    }

    /**
     * @param $table
     * @param $data
     * @return void
     * @throws Exception
     */
    public static function insert($table, $data)
    {
        foreach ($data as $k => $v) {
            if (strpos($k, '`') === false) {
                $data['`' . $k . '`'] = $v;
                unset($data[$k]);
            }
        }
        self::getConnection()->insert($table, $data);
    }

    /**
     * @param $table
     * @param $data
     * @return void
     * @throws Exception
     */
    public static function update($table, $data, $criteria = [])
    {
        foreach ($data as $k => $v) {
            if (strpos($k, '`') === false) {
                $data['`' . $k . '`'] = $v;
                unset($data[$k]);
            }
        }
        self::getConnection()->update($table, $data, $criteria);
    }

    public static function lastInsertId()
    {
        return self::getConnection()->lastInsertId();
    }

    public static function fetchAll($q, array $parameters = [])
    {
        $connection = self::getConnection();
        if (method_exists($connection, 'fetchAllAssociative')) {
            return $connection->fetchAllAssociative($q, $parameters);
        } else {
            $stmt = $connection->executeQuery($q, $parameters);
            return $stmt->fetchAll(FetchMode::ASSOCIATIVE);
        }
    }

    /**
     * @param $q
     * @param $parameters
     * @return false|mixed[]
     * @throws Exception
     */
    public static function fetch($q, array $parameters = [])
    {
        $connection = self::getConnection();
        if (method_exists($connection, 'fetchAssociative')) {
            return $connection->fetchAssociative($q, $parameters);
        } else {
            return $connection->fetchAssoc($q, $parameters);
        }
    }
}

class DbAdapterOld
{
    public static $lastInsertId = null;
    public static function getQueryBuilder(): CI_DB_query_builder
    {
        return StaticGXCoreLoader::getDatabaseQueryBuilder();
    }


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function execute($q, $parameters = null)
    {
        static::getQueryBuilder()->query($q, $parameters ?: []);
    }

    /**
     * @param $table
     * @param $data
     * @return void
     */
    public static function insert($table, $data)
    {
        foreach ($data as $k => $v) {
            if (strpos($k, '`') === false) {
                $data['`' . $k . '`'] = $v;
                unset($data[$k]);
            }
        }
        /** @var CI_DB_result $result */
        $result = self::getQueryBuilder()->insert($table, $data);
        if(is_object($result)) {
            static::$lastInsertId = $result->result_id;
        }else{
            static::$lastInsertId = null;
        }
    }

    /**
     * @param $table
     * @param $data
     * @param array $criteria
     * @return void
     */
    public static function update($table, $data, $criteria = [])
    {
        foreach ($data as $k => $v) {
            if (strpos($k, '`') === false) {
                $data['`' . $k . '`'] = $v;
                unset($data[$k]);
            }
        }
        self::getQueryBuilder()->update($table, $data, $criteria);
    }

    public static function lastInsertId()
    {
        return static::$lastInsertId;
    }

    public static function fetchAll($q, array $parameters = [])
    {
        /** @var CI_DB_result $result */
        $result = static::getQueryBuilder()->query($q, $parameters);
        return $result->result_array();
    }

    /**
     * @param $q
     * @param $parameters
     * @return false|mixed[]
     * @throws Exception
     */
    public static function fetch($q, array $parameters = [])
    {
        /** @var CI_DB_result $result */
        $result = static::getQueryBuilder()->query($q, $parameters);
        return $result->row_array();
    }
}

if (class_exists(QueryBuilder::class)) {
    class DbAdapter extends DbAdapterNew
    {
    }
} else {
    class DbAdapter extends DbAdapterOld
    {
    }
}