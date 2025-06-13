<?php

namespace App\Models;

use App\Config\Database\QueryDB;

class PerfilProgramaModel extends QueryDB
{
    protected string $table = "perfil_programa";

    protected string $primaryKey = "perfprog_codigo";

    protected string $fcreated = "perfprog_fcreacion";

    protected string $fupdated = "perfprog_fmodificacion";

    protected string $status = "perfprog_estado";
}
