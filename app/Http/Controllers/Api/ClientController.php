<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ClientService;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    protected $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function index()
    {
        $clients = $this->clientService->getAllClients();
        return response()->json($clients, 200);
    }

    public function active()
    {
        $clients = $this->clientService->getActiveClients();
        return response()->json($clients, 200);
    }

    public function show($id)
    {
        $client = $this->clientService->getClientById($id);
        return response()->json($client, 200);
    }

    public function store(Request $request)
    {
        $client = $this->clientService->createClient($request);

        return response()->json([
            'message' => 'Client successfully created',
            'data' => $client
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $client = $this->clientService->updateClient($request, $id);

        return response()->json([
            'message' => 'Client successfully updated',
            'data' => $client
        ], 200);
    }

    public function destroy($id)
    {
        $result = $this->clientService->deleteClient($id);

        return response()->json([
            'message' => $result['message']
        ], 200);
    }

    public function updateOrder(Request $request)
    {
        $result = $this->clientService->updateOrder($request);

        return response()->json([
            'message' => $result['message']
        ], 200);
    }
}
