<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response; // Importa la clase Response para los códigos de estado

trait ApiResponser
{
    /**
     * Construye una respuesta de éxito.
     *
     * @param mixed $data Los datos a devolver.
     * @param string|null $message Mensaje descriptivo.
     * @param int $statusCode Código de estado HTTP (por defecto 200 OK).
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($data, string $message = null, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ];

        return response()->json($response, $statusCode);
    }

    /**
     * Construye una respuesta de error.
     *
     * @param string|null $message Mensaje de error descriptivo.
     * @param int $statusCode Código de estado HTTP.
     * @param mixed|null $errors Detalles adicionales del error (opcional).
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse(string $message = null, int $statusCode, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        // Añade detalles del error si se proporcionan
        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Respuesta específica para "No Content" (204).
     * HTTP 204 no debe tener cuerpo de respuesta.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function noContentResponse(): JsonResponse
    {
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}