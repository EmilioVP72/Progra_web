<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    public function All(){
        $clients = Client::all();

        if(!$clients){
            $data = [
                'massage' => 'No hay registros de clientes',
                'status' => 404
            ];
            return response()->json($data, 404);
        }
        return response()->json($clients, 200);
    }

    public function OneClient($id){
        $client = Client::find($id);

        if(!$client){
            $data = [
                'message' => 'No se encontro el cliente',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $data = [
            'client' => $client,
            'status' => 200
        ];
        return response()->json($data, 200);
    }

    public function Store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'lastname' => 'required|string',
            'phone' => 'required|digits:10',
            'email' => 'required|email|unique:client,email', 
            'password' => 'required|string',
            'photo' => 'required|image|mimes:jpg,png,jpeg|max:5120'
        ]);

        if ($validator->fails()) { 
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(), 
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        $photoFile = $request->file('photo');
        $photoPath = $photoFile->store('clients', 'public');

        $clients = Client::create([
            'name' => $request->name,
            'lastname' => $request->lastname,
            'phone' => $request->phone, 
            'email' => $request->email,
            'password' => Hash::make($request->password), 
            'photo' => $photoPath
        ]);

        if (!$clients) {
            $data = [
                'message' => 'Error al crear el cliente',
                'status' => 500
            ];
            Storage::disk('public')->delete($photoPath);
            return response()->json($data, 500);
        }

        $data = [
            'message' => 'Cliente creado correctamente',
            'client' => $clients, 
            'status' => 201
        ];
        return response()->json($data, 201);
    }

    public function delete($id){
        $client = Client::find($id);

        if(!$client){
            $data = [
                'message' => 'No se encontro el cliente',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $client->delete();

        $data = [
            'message' => 'Cliente eliminado correctamente',
            'status' => 200
        ];
        return response()->json($data, 200);
    }

    public function Update($id, Request $request){
        $client = Client::find($id);

        if(!$client){
            $data = [
                'message' => 'No se encontro el cliente',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|required',
            'lastname' => 'sometimes|string|required',
            'phone' => 'sometimes|required|digits:10',
            'email' => 'sometimes|required|email|unique:clients,email,' . $id,
            'photo' => 'sometimes|image|mimes:jpg,png|max:5120'
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        // Actualiza solo los campos que vienen en la petición
        $client->fill($request->only(['name', 'lastname', 'phone', 'email']));

        if ($request->hasFile('photo')) {
            // Elimina la foto anterior si existe
            if ($client->photo) {
                Storage::disk('public')->delete($client->photo);
            }
            
            $photoFile = $request->file('photo');
            $photoPath = $photoFile->store('clients', 'public');
            $client->photo = $photoPath;
        }

        $client->save();

        $data = [
            'message' => 'Cliente actualizado correctamente',
            'client' => $client,
            'status' => 200
        ];
        return response()->json($data, 200);
    }
}
