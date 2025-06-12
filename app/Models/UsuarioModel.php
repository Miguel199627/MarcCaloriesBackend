<?php

namespace App\Models;

use App\Config\Database\QueryDB;

class UsuarioModel extends QueryDB
{
    protected string $table = "usuario";

    protected string $primaryKey = "usuario_codigo";

    protected string $fcreated = "usuario_fcreacion";

    protected string $fupdated = "usuario_fmodificacion";

    protected string $status = "usuario_estado";
}
