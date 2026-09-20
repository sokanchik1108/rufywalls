<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PointOfSaleController extends Controller
{
    /**
     * Список точек продаж
     */
    public function index()
    {
        $pointsOfSale = DB::table('points_of_sale')
            ->orderBy('id', 'desc')
            ->get();

        return view(
            'admin.points_of_sale.index',
            compact('pointsOfSale')
        );
    }

    /**
     * Создание точки продаж
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],
            ],
            [
                'name.required' => 'Введите название точки продаж.',
                'name.string'   => 'Название точки продаж должно быть текстом.',
                'name.max'      => 'Название точки продаж не должно превышать 255 символов.',
            ]
        );

        DB::table('points_of_sale')->insert([
            'name'       => trim($validated['name']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('admin.points-of-sale.index')
            ->with('success', 'Точка продаж успешно создана.');
    }

    /**
     * Удаление точки продаж
     */
    public function destroy($pointOfSale)
    {
        /*
        |--------------------------------------------------------------------------
        | Находим точку непосредственно в БД
        |--------------------------------------------------------------------------
        */

        $point = DB::table('points_of_sale')
            ->where('id', $pointOfSale)
            ->first();

        if (!$point) {
            return redirect()
                ->route('admin.points-of-sale.index')
                ->with(
                    'error',
                    'Точка продаж не найдена.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Проверяем, используется ли точка продаж в заказах
        |--------------------------------------------------------------------------
        */

        $ordersCount = DB::table('orders')
            ->where('point_of_sale_id', $pointOfSale)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Если используется — удалять запрещаем
        |--------------------------------------------------------------------------
        */

        if ($ordersCount > 0) {
            return redirect()
                ->route('admin.points-of-sale.index')
                ->with(
                    'error',
                    'Нельзя удалить точку продаж «' .
                    $point->name .
                    '». Она используется в ' .
                    $ordersCount .
                    ' заказах.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Удаляем напрямую из таблицы
        |--------------------------------------------------------------------------
        */

        $deleted = DB::table('points_of_sale')
            ->where('id', $pointOfSale)
            ->delete();

        /*
        |--------------------------------------------------------------------------
        | Проверяем результат удаления
        |--------------------------------------------------------------------------
        */

        if ($deleted !== 1) {
            return redirect()
                ->route('admin.points-of-sale.index')
                ->with(
                    'error',
                    'Не удалось удалить точку продаж.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Дополнительная проверка
        |--------------------------------------------------------------------------
        */

        $exists = DB::table('points_of_sale')
            ->where('id', $pointOfSale)
            ->exists();

        if ($exists) {
            return redirect()
                ->route('admin.points-of-sale.index')
                ->with(
                    'error',
                    'Точка продаж всё ещё находится в базе данных.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Успешное удаление
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('admin.points-of-sale.index')
            ->with(
                'success',
                'Точка продаж «' .
                $point->name .
                '» успешно удалена.'
            );
    }
}

