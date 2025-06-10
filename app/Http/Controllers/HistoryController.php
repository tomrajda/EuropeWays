<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HistoryData;
use Auth;

class HistoryController extends Controller
{
    public function save(Request $request)
    {
        // Validate request data
        $validatedData = $request->validate([
            'api_url' => 'required|string|max:500',
            'amount' => 'required|numeric|max:500',
        ]);

        // Get the ID of the currently authenticated user
        $userId = Auth::id();

        // Save data to database
        $historyData = new HistoryData();
        $historyData->user_id = $userId; // Assign the user ID
        $historyData->api_url = $validatedData['api_url'];
        $historyData->amount = $validatedData['amount'];
        $historyData->save();

        return response()->json(['message' => 'Data saved successfully'], 200);
    }

    public function index()
    {
        // Pobierz dane historii dla bieżącego użytkownika
        $historyData = HistoryData::where('user_id', Auth::id())->get();
        
        // Zwróć dane jako odpowiedź JSON
        return response()->json($historyData, 200);
    }
}
