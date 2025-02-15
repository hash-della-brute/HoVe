<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\PetController;


// Rotas de Registro

// Middleware para verificar se utilizador está autenticado e se utilizador é administrador do sistema.
Route::middleware(['auth', 'admin'])->group(function () {

    Route::get('/register', function () {  // Rota para mostrar o formulário de registro
        return view('register.index');
    })->name('register');

    Route::post('/register', function (Request $request) {
        // Remove a formatação do CPF
        $cpf = preg_replace('/[^0-9]/', '', $request->cpf);

        // Validação dos dados
        $user = $request->validate([
            'name' => 'required|string|max:255',
            'cpf' => 'required|string|unique:users|max:14', // CPF único e com no máximo 14 caracteres
            'password' => 'required|string|min:8|confirmed', // Senha com confirmação
            'role' => 'required|string|in:admin,veterinario,atendente,user', // Papel válido
        ]);

        // Criar o Usuário
        User::create([
            'name' => $user['name'],
            'cpf' => $cpf, // Salva o CPF sem formatação
            'password' => Hash::make($user['password']),
            'role' => $user['role'], // Papel escolhido
        ]);

        return redirect('/register')->with('success', 'Usuário registrado com sucesso!');
    });

});

// Rotas para Login

// Rota para exibir o formulário de login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

// Rota para processar o login
Route::post('/login', [LoginController::class, 'login']);

// Rota para logout
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');



Route::middleware(['auth'])->group(function () {


// Rotas para menu
    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('/owners/buscar-por-cpf', [OwnerController::class, 'buscarPorCpf'])->name('owners.buscar-por-cpf');

// Rotas para Owner
    Route::resource('owners', OwnerController::class);

// Rotas para Pet
    Route::resource('pets', PetController::class);


});


