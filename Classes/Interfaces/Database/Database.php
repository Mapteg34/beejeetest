<?php

namespace Mapt\Beejeetest\Interfaces\Database;

interface Database
{
    /**
     * Выполняет переданный запрос
     * Второй аргумент - параметры для pg_query_params
     *
     * @param string $sql
     * @param array $params
     *
     * @return Result
     */
    public function query(string $sql, array $params = []);

    /**
     * Возвращает текст последнего выполненного запроса
     *
     * @return string
     */
    public function executedQuery();

    /**
     * Возвращает текст последней ошибки
     *
     * @return string
     */
    public function errorText();
}