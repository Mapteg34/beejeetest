<?php

namespace Mapt\Beejeetest\Interfaces\Database;

interface Transactions
{
    /**
     * Стартует транзакцию
     */
    public function transactionStart();

    /**
     * Откатывает изменения
     */
    public function transactionRollback();

    /**
     * Фиксирует изменения
     */
    public function transactionCommit();
}