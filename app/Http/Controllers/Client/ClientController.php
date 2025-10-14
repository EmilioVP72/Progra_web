<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
            'photo' => 'required|string'
        ]);

        if ($validator->fails()) { 
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(), 
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        $clients = Client::create([
            'name' => $request->name,
            'lastname' => $request->lastname,
            'phone' => $request->phone, 
            'email' => $request->email,
            'password' => Hash::make($request->password), 
            'photo' => $request->photo
        ]);

        if (!$clients) {
            $data = [
                'message' => 'Error al crear el cliente',
                'status' => 500
            ];
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
            'name' => 'string',
            'lastname' => 'string',
            'phone' => 'digits:10',
            'email' => 'email|unique:clients,email,' . $id, 
            'photo' => 'string'
        ]);

        if($validator->fails()){
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        if ($request->has('name')) {
            $client->name = $request->name;
        }
        if ($request->has('lastname')) {
            $client->lastname = $request->lastname;
        }
        if ($request->has('phone')) {
            $client->phone = $request->phone;
        }
        if ($request->has('email')) {
            $client->email = $request->email;
        }
        if ($request->has('photo')) {
            $client->photo = $request->photo;
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

