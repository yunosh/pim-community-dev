<?php

namespace Akeneo\Test\Integration\CRUD;

interface CRUDInterface
{
    /**
     * @param string $code
     * @param array  $data
     *
     * @return mixed
     */
    public function create(string $code, array $data = []);

    /**
     * @param mixed $object
     * @param array $data
     *
     * @return mixed
     */
    public function update($object, array $data);

    /**
     * @param string $code
     *
     * @return mixed
     */
    public function get(string $code);

    /**
     * @param mixed $object
     */
    public function save($object);

    /**
     * @param array $object
     */
    public function saveAll(array $objects);

    /**
     * @param string $code
     */
    public function delete(string $code);
}