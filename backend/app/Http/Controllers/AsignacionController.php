<?php



declare(strict_types=1);



namespace App\Http\Controllers;



use App\Http\Requests\AsignacionRequest;

use App\Services\RutaService;

use Exception;



class AsignacionController extends Controller

{

    public function __construct(

        private readonly RutaService $rutaService

    ) {}



    public function store(AsignacionRequest $request)

    {

        try {

            $resultado = $this->rutaService->crearAsignacion(

                $request->input('pedido_ids'),

                (int)$request->input('camion_id'),

                (int)request('user_id')

            );

            return response()->json(['message' => 'Asignación creada', 'data' => $resultado], 201);

        } catch (Exception $e) {

            $code = (is_int($e->getCode()) && $e->getCode() >= 100 && $e->getCode() < 600) ? $e->getCode() : 500;

            return response()->json(['message' => $e->getMessage()], $code);

        }

    }



    public function show(int $id)

    {

        $detalle = $this->rutaService->getAsignacion($id);

        return response()->json(['data' => $detalle]);

    }



    public function destroy(\Illuminate\Http\Request $request)

    {

        try {

            $pedidoIds = $request->input('pedido_ids', []);

            $this->rutaService->cancelarAsignacion($pedidoIds);

            return response()->json(['message' => 'Asignación cancelada con éxito']);

        } catch (Exception $e) {

            $code = (is_int($e->getCode()) && $e->getCode() >= 100 && $e->getCode() < 600) ? $e->getCode() : 500;

            return response()->json(['message' => $e->getMessage()], $code);

        }

    }



    public function cerrarRuta(int $camionId)

    {

        try {

            $this->rutaService->cerrarRuta($camionId);

            return response()->json(['message' => 'Ruta cerrada con éxito']);

        } catch (Exception $e) {

            $code = (is_int($e->getCode()) && $e->getCode() >= 100 && $e->getCode() < 600) ? $e->getCode() : 500;

            return response()->json(['message' => $e->getMessage()], $code);

        }

    }

}
