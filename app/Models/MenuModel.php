<?php

namespace App\Models;

use App\Config\Database\QueryDB;

class MenuModel extends QueryDB
{
    protected string $table = "menu";

    protected string $primaryKey = "menu_codigo";

    protected string $fcreated = "menu_fcreacion";

    protected string $fupdated = "menu_fmodificacion";

    protected string $status = "menu_estado";
}
