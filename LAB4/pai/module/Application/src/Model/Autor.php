<?php

namespace Application\Model;

use Laminas\Db\Adapter as DbAdapter;
use Laminas\Db\Sql\Sql;

class Autor implements DbAdapter\AdapterAwareInterface
{
    use DbAdapter\AdapterAwareTrait;

    public function pobierzSlownik(): array
    {
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $select = $sql->select('autorzy');
        $select->order('nazwisko');
        $selectString = $sql->buildSqlString($select);
        $wyniki = $dbAdapter->query($selectString, $dbAdapter::QUERY_MODE_EXECUTE);
        $temp = [];
        foreach ($wyniki as $row) {
            $temp[$row->id] = $row->imie . ' ' . $row->nazwisko;
        }
        return $temp;
    }

    public function pobierzWszystko()
    {
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $select = $sql->select('autorzy');
        $select->order('nazwisko');
        $selectString = $sql->buildSqlString($select);
        return $dbAdapter->query($selectString, $dbAdapter::QUERY_MODE_EXECUTE);
    }

    public function dodaj($dane): bool|int
    {
        $dbAdapter = $this->adapter;

        $sql = new Sql($dbAdapter);
        $insert = $sql->insert('autorzy');
        $insert->values([
            'imie' => $dane->imie,
            'nazwisko' => $dane->nazwisko,
        ]);
        $selectString = $sql->buildSqlString($insert);
        $wynik = $dbAdapter->query($selectString, $dbAdapter::QUERY_MODE_EXECUTE);
        try {
            return $wynik->getGeneratedValue();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function pobierz(int $id)
    {
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $select = $sql->select('autorzy');
        $select->where(['id' => $id]);

        $selectString = $sql->buildSqlString($select);
        $wynik = $dbAdapter->query($selectString, $dbAdapter::QUERY_MODE_EXECUTE);

        if ($wynik->count())
            return $wynik->current();
        return [];
    }

    public function aktualizuj(int $id, $dane)
    {
        $dbAdapter = $this->adapter;

        $sql = new Sql($dbAdapter);
        $update = $sql->update('autorzy');
        $update->set([
            'imie' => $dane->imie,
            'nazwisko' => $dane->nazwisko,
        ]);
        $update->where(['id' => $id]);

        $selectString = $sql->buildSqlString($update);
        $dbAdapter->query($selectString, $dbAdapter::QUERY_MODE_EXECUTE);

        return true;
    }

    public function usun(int $id): bool //usuwanie
    {
        $dbAdapter = $this->adapter;

        $sql = new Sql($dbAdapter);

        $delete = $sql->delete('ksiazki');
        $delete->where(['id_autora' => $id]);
        $selectString = $sql->buildSqlString($delete);
        $dbAdapter->query($selectString, $dbAdapter::QUERY_MODE_EXECUTE);

        $delete = $sql->delete('autorzy');
        $delete->where(['id' => $id]);
        $selectString = $sql->buildSqlString($delete);
        $dbAdapter->query($selectString, $dbAdapter::QUERY_MODE_EXECUTE);
        return true;
    }
}