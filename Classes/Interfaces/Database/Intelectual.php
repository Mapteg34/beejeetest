<?php

namespace Mapt\Beejeetest\Interfaces\Database;

interface Intelectual
{
    /**
     * Проводит "умную" выборку
     *
     * @param string $table таблица, по которой осуществляется выборка
     * @param array $params массив c параметрами
     *
     * @return Result
     */
    public function iSelect(string $table, array $params = []);

    /**
     * Проводит "умную" выборку и возвращает количество записей
     *
     * @param string $table таблица, по которой осуществляется выборка
     * @param array $params массив c параметрами
     *
     * @return integer
     */
    public function iSelectCnt(string $table, array $params = []);

    /**
     * Проводит "умное" добавление и возвращает ключ последней добавленной записи
     *
     * @param string $table таблица, в которую осуществляется добавление
     * @param array $arFields массив полей для добавления
     * @param string $pkey первичный ключ таблицы, который возвращается после добавления
     *
     * @return mixed
     */
    public function iInsert(string $table, array $arFields, string $pkey = "id");

    /**
     * Проводит умное "обновление" элементов
     *
     * @param string $table таблица, в которой проводится обновление
     * @param array $arFields массив полей, подлежащих обновлению
     * @param array $params массив c параметрами
     *
     * @return Result
     */
    public function iUpdate(string $table, array $arFields, array $params = []);

    /**
     * Проводит "умное" удаление
     *
     * @param string $table таблица
     * @param array $params массив c параметрами
     *
     * @return Result
     */
    public function iDelete(string $table, array $params = []);
}