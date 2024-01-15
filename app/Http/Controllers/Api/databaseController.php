<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CreateDatabaseRequest;
use Illuminate\Http\Request;
use App\Models\databases;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class databaseController extends Controller
{
    public function index()
    {
        $databases = databases::all();

        return response()->json($databases);
    }

    public function create(CreateDatabaseRequest $request)
    {
        $database = new databases();
        $database->name = $request->input('name');
        $database->save();
        // Jalankan php artisan migrate
        Artisan::call('migrate');
        return response()->json($database);
    }

    public function switch(Request $request)
    {
        $databaseName = $request->input('name');
        DB::connection('default')->setDatabaseName($databaseName);
        // Ubah variabel env
        env('DB_DATABASE', $databaseName);

        return response()->json([
            'success' => true,
        ]);
    }
}
