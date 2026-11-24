<?php

namespace App\Core\Settings\Models;

use App\Core\Support\CoreModel;

class Setting extends CoreModel
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
    ];
}
