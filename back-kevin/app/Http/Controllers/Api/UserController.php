<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; // Importa el modelo User
use Illuminate\Support\Facades\Hash; // Importa Hash para las contraseñas
use Illuminate\Validation\Rule; // Para reglas de validación
use Illuminate\Support\Facades\Validator; // Para validación manual si prefieres
use App\Http\Resources\UserResource; // Importa el resource

use App\Traits\ApiResponser; // <--- Importa el Trait
use Illuminate\Http\Response; // <--- Importa Response para los códigos


class UserController extends Controller
{

    use ApiResponser; // <--- Usa el Trait

    /**
     * Muestra una lista de los usuarios.
     * GET /api/users
     */
    public function index()
    {
        // Obtiene todos los usuarios
        // $users = User::all();
        // Retorna la colección de usuarios en formato JSON
        // return response()->json($users);
        //return UserResource::collection(User::all());

        $users = User::all();

        if ($users->isEmpty()) {
             // Usa el trait para una respuesta exitosa pero sin datos
            return $this->successResponse(null, 'No users found.', Response::HTTP_OK);
        }

        // Usa el trait y el API Resource para devolver la colección
        return $this->successResponse(
            UserResource::collection($users), // Los datos formateados por el Resource
            'Users retrieved successfully'
        );

    }

    /**
     * Almacena un usuario recién creado.
     * POST /api/users
     */
    public function store(Request $request)
    {
        // Validación de los datos de entrada
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users', // email debe ser único en la tabla users
            'password' => 'required|string|min:8|confirmed', // 'confirmed' busca un campo 'password_confirmation'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422); // 422 Unprocessable Entity
        }

        // Crea el usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // ¡Importante! Hashear la contraseña
        ]);

        // Retorna el usuario creado con código de estado 201 (Created)
        return response()->json($user, 201);
    }

    /**
     * Muestra el usuario especificado.
     * GET /api/users/{user}
     */
    public function show(User $user) // Usa Route Model Binding
    {
        // Laravel automáticamente busca el User por ID o devuelve 404 si no lo encuentra
        //return response()->json($user);
        return new UserResource($user);
    }

    /**
     * Actualiza el usuario especificado.
     * PUT/PATCH /api/users/{user}
     */
    public function update(Request $request, User $user) // Usa Route Model Binding
    {
        // Validación de los datos de entrada
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255', // 'sometimes' valida solo si está presente
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id), // Ignora el email del propio usuario al validar unicidad
            ],
            'password' => 'sometimes|nullable|string|min:8|confirmed', // 'nullable' permite enviar null o no enviar el campo
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Actualiza los campos proporcionados
        if ($request->filled('name')) { // filled() verifica que no esté vacío
            $user->name = $request->name;
        }
        if ($request->filled('email')) {
            $user->email = $request->email;
        }
        // Actualiza contraseña solo si se proporciona una nueva
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save(); // Guarda los cambios

        // Retorna el usuario actualizado
        return response()->json($user);
    }

    /**
     * Elimina el usuario especificado.
     * DELETE /api/users/{user}
     */
    public function destroy(User $user) // Usa Route Model Binding
    {
        $user->delete();

        // Retorna una respuesta vacía con código 204 (No Content)
        return response()->json(null, 204);
    }
}
