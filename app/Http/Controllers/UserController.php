<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUsuario;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        // Aplicar middleware para verificar permisos
        $this->middleware('permission:gestionar usuarios', ['only' => ['index', 'create', 'store', 'edit', 'update', 'destroy']]);
    }

    // Mostrar todos los usuarios
    public function index()
    {
        // Ordenar usuarios por nombre completo
        $users = User::orderBy('name', 'asc')->get();
        return view('User.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Obtener todos los roles
        $roles = Role::all();
        return view('User.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUsuario $request)
    {
        // Crear el usuario sin incluir el campo 'role'
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Asignar el rol al usuario
        $user->assignRole($request->role);

        // Redirigir al index con un mensaje de éxito
        return redirect()->route('users.index')->with('success', 'Usuario creado con éxito y rol asignado.');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    // Mostrar el formulario para editar un usuario
    public function edit(User $user)
    {
        // Obtener todos los roles
        $roles = Role::all();

        return view('User.edit', compact('user', 'roles'));
    }


    // Actualizar un usuario existente
    public function update(StoreUsuario $request, User $user)
    {
        // Actualizar la información del usuario
        $user->name = $request->name;
        $user->email = $request->email;

        // Verificar si la contraseña ha sido proporcionada
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Guardar los cambios del usuario
        $user->save();

        // Sincronizar el rol del usuario
        $user->syncRoles([$request->role]);

        // Redirigir al índice con un mensaje de éxito
        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
    }


    // Eliminar un usuario
    public function destroy(User $user)
    {
        // Eliminar el usuario
        $user->delete();

        // Redirigir al índice con un mensaje de éxito
        return redirect()->route('users.index')->with('success', 'Usuario eliminado correctamente.');
    }
}
