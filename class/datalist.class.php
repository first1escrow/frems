<?php
require_once __DIR__ . '/traits/realtyDatalist.traits.php';
require_once __DIR__ . '/traits/scrivenerDatalist.traits.php';
require_once __DIR__ . '/traits/salesDatalist.traits.php';

class Datalist
{
    use ScrivenerDatalist, RealtyDatalist, SalesDatalist;

    public function All()
    {
        $stores = [];
        $stores = array_merge($stores, $this->Scrivener());
        $stores = array_merge($stores, $this->Realty());

        return $stores;
    }

    /**
     * $id(int): sales pId
     */
    public function SalesMember($id)
    {
        $sales = $this->Sales();
        return $sales[$id];
    }

    public function allScrivenerRealty()
    {
        $stores = [];
        $stores = array_merge($stores, $this->allStatusScrivener());
        $stores = array_merge($stores, $this->allStatusRealty());

        return $stores;
    }
}
