<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TenantDbConfig extends Model
{
    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'esp_superadmin';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tenant_db_config';
}