<?php

namespace Pp\Creator\Cruds;

use Pp\Creator\Generates\Crud;

class TradeDatasCrud extends Crud
{

    protected $optional = false;

    public static function menu()
    {
    }

    public function attrs()
    {

        return [
            $this->attr('id', 'id', 'Id'),
            $this->attr('trade_id', 'foreignId', 'Trade Id'),
            $this->attr('executed_at', 'timestamp', 'Fecha'),
            $this->attr('spread_id', 'foreignId', 'Spread'),
            $this->attr('side', 'enum', 'Side', [
                'enum' => ["BUY", "SELL"]
            ]),
            $this->attr('quantity', 'double', 'Qty'),
            $this->attr('effect', 'enum', 'Efecto', [
                'enum' => ["OPEN", "CLOSE"]
            ]),
            $this->attr('price', 'double', 'Precio'),
            $this->attr('net_price', 'double', 'Precio Neto'),
            $this->attr('trade_order_type_id', 'foreignId', 'Tipo'),
        ];
    }

    public function addTablesBeforeMigrate()
    {
        return [
            'spreads' => [
                ['id' => 'id', 'type' => 'id'],
                ['id' => 'name', 'type' => 'string:short'],
            ],
            'trade_order_types' => [
                ['id' => 'id', 'type' => 'id'],
                ['id' => 'name', 'type' => 'string:short'],
            ],
        ];
    }
}
