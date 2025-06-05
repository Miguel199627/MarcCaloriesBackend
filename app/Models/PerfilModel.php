<?php

namespace App\Models;

use App\Config\Database\QueryDB;

class PerfilModel extends QueryDB
{
    protected string $table = "perfil";

    protected string $primaryKey = "perfil_codigo";

    protected string $fcreated = "perfil_fcreacion";

    protected string $fupdated = "perfil_fmodificacion";

    protected string $status = "perfil_estado";
}
