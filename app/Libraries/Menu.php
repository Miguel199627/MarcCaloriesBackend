<?php

namespace App\Libraries;

use App\Models\MenuModel;
use App\Models\PerfilProgramaModel;

class Menu
{
    public static function getMenu(string $usuario_email)
    {
        $model = new PerfilProgramaModel();
        $query = $model->select("menu.*")
            ->join("menu", "menu_codigo = perfprog_menu")
            ->join("usuario", "usuario_perfil = perfprog_perfil")
            ->where("usuario_email = ?", [$usuario_email])
            ->where("perfprog_estado = ?", [1]);

        $programas = $query->all();

        // 2. Recolectar códigos únicos y subir por la jerarquía
        $todosMenus = [];
        $procesados = [];

        foreach ($programas as $menu) {
            self::subirJerarquia($menu['menu_codigo'], $todosMenus, $procesados);
        }

        // 3. Indexar por ID y construir el árbol
        $indexado = [];
        foreach ($todosMenus as $m) {
            $indexado[$m['menu_codigo']] = $m + ['children' => []];
        }

        $arbol = [];
        foreach ($indexado as &$menu) {
            $padreId = $menu['menu_padreid'];
            if ($padreId && isset($indexado[$padreId])) {
                $indexado[$padreId]['children'][] = &$menu;
            } else {
                $arbol[] = &$menu;
            }
        }

        // Ordenar recursivamente por 'menu_orden'
        self::ordenarRecursivo($arbol);

        return $arbol;
    }

    // Función auxiliar para subir la jerarquía
    private static function subirJerarquia(int $menuId, array &$todosMenus, array &$procesados)
    {
        if (isset($procesados[$menuId])) return;
        $procesados[$menuId] = true;

        $model = new MenuModel;
        $query = $model->where("menu_codigo = ?", [$menuId])
            ->where("menu_estado = ?", [1]);

        $menu = $query->first();

        if ($menu) {
            $todosMenus[$menuId] = $menu;
            if ($menu['menu_padreid'] !== 0) {
                self::subirJerarquia($menu['menu_padreid'], $todosMenus, $procesados);
            }
        }
    }

    // Función para ordenar hijos por el campo `menu_orden`
    private static function ordenarRecursivo(array &$nodos): void
    {
        usort($nodos, fn($a, $b) => $a['menu_orden'] <=> $b['menu_orden']);
        foreach ($nodos as &$nodo) {
            if (!empty($nodo['children'])) {
                self::ordenarRecursivo($nodo['children']);
            }
        }
    }
}
