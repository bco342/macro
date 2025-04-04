<?php

use App\Service\Importer\ImportRule;
use App\Service\Importer\Normalizer;

return [
    'id' => new ImportRule(
        model: 'estate',
        property: 'external_id',
        callback: 'trim'
    ),
    'Агенство Недвижимости' => new ImportRule(
        model: 'agency',
        property: 'name',
        callback: 'trim'
    ),
    'Менеджер' => new ImportRule(
        model: 'manager',
        property: 'name',
        callback: 'trim'
    ),
    'Продавец' => new ImportRule(
        model: 'contact',
        property: 'name',
        callback: 'trim'
    ),
    'Телефоны продавца' => new ImportRule(
        model: 'contact',
        property: 'phones',
        callback: [Normalizer::class, 'normalizePhones']
    ),
    'Адрес' => new ImportRule(
        model: 'estate',
        property: 'address',
        callback: 'trim'
    ),
    'Цена' => new ImportRule(
        model: 'estate',
        property: 'price',
        callback: [Normalizer::class, 'normalizePrice']
    ),
    'Комнат' => new ImportRule(
        model: 'estate',
        property: 'rooms',
        callback: 'intval'
    ),
    'Этаж' => new ImportRule(
        model: 'estate',
        property: 'floor',
        callback: 'intval'
    ),
    'Этажей' => new ImportRule(
        model: 'estate',
        property: 'house_floors',
        callback: 'intval'
    ),
    'Описание' => new ImportRule(
        model: 'estate',
        property: 'description',
        callback: 'trim'
    ),
];
