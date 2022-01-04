<?php

namespace Pp\Creator\Cruds;

use Pp\Creator\Generates\Crud;

class TradesCrud extends Crud
{


    public static function menu()
    {
    }

    public function attrs()
    {

        return [
            $this->attr('id', 'id', 'Id'),
            $this->attr('title', 'string', 'titulo'),
            $this->attr('trade_risk_id', 'foreignId', 'Riesgo',  [
                'relation_type' => 'belongsTo'
            ]),
            $this->attr('operation_type_id', 'foreignId', 'Tipo Ope',  [
                'relation_type' => 'belongsTo'
            ]),
            $this->attr('open_price', 'double', 'Compra'),
            $this->attr('open_price_effect', 'double', 'Ef Compra'),
            $this->attr('open_quantity', 'double', 'Q Compra'),
            $this->attr('opened_at', 'timestamp', 'Abierto En'),

            $this->attr('close_price', 'double', 'Venta'),
            $this->attr('close_price_effect', 'double', 'Ef Venta'),
            $this->attr('close_quantity', 'double', 'Q Venta'),
            $this->attr('closed_at', 'timestamp', 'Cerrado En'),

            $this->attr('commission', 'double', 'Comision'),

            $this->attr('ticker', 'ticker', null,  [
                'relation_type' => 'belongsTo',
            ]),
            $this->attr('expires_at', 'date', 'Expira'),
            $this->attr('strike', 'double'),
            $this->attr('trade_type_id', 'foreignId', 'Tipo trade',  [
                'relation_type' => 'belongsTo'
            ]),
        ];
    }

    protected function externalRelations()
    {
        return [
            $this->externalRelation('trade_comment_id', 'hasMany'),
        ];
    }

    public function addTablesBeforeMigrate()
    {
        return [
            'operation_types' => [
                ['id' => 'id', 'type' => 'id'],
                ['id' => 'name', 'type' => 'string:short'],
            ],
            'tickers' =>  [
                [
                    'id' => 'symbol', 'props' => [
                        'col' => '$table->string' . "('symbol', 5)->primary()->index()",
                    ]
                ],
                ['id' => 'name', 'type' => 'string'],
            ],
            'trade_types' =>  [
                ['id' => 'id', 'type' => 'id'],
                ['id' => 'name', 'type' => 'string:short'],
            ],
        ];
    }
}
