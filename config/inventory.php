<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Low Stock Threshold
    |--------------------------------------------------------------------------
    |
    | Products with stock at or below this threshold are considered low stock.
    | Can be overridden via the LOW_STOCK_THRESHOLD environment variable.
    |
    */

    'low_stock_threshold' => (int) env('LOW_STOCK_THRESHOLD', 5),

];
