<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FlightHistoryData;
use Auth;

class FlightHistoryController extends Controller
{
    public function save(Request $request)
    {
        // Validate request data
        $validatedData = $request->validate([
            'api_url' => 'required|array',
            'api_url.*' => 'string|max:500',
            'amount' => 'required|array',
            'amount.*' => 'numeric|max:500',
        ]);

        // Get the ID of the currently authenticated user
        $userId = Auth::id();

        // Save data to database
        $flightHistoryData = new FlightHistoryData();
        $flightHistoryData->user_id = $userId; // Assign the user ID
        $flightHistoryData->api_url = json_encode($validatedData['api_url']);
        $flightHistoryData->amount = json_encode($validatedData['amount']);
        $flightHistoryData->save();

        return response()->json(['message' => 'Data saved successfully'], 200);
    }

    public function index()
    {
        // Retrieve history data for the current user
        $flightHistoryData = FlightHistoryData::where('user_id', Auth::id())->get();
        
        // Decode the JSON fields before returning them
        foreach ($flightHistoryData as $data) {
            $data->api_url = json_decode($data->api_url);
            $data->amount = json_decode($data->amount);
        }
        
        // Return data as a JSON response
        return response()->json($flightHistoryData, 200);
    }

    public function destroy($id)
    {
        $flightHistoryData = FlightHistoryData::where('id', $id)->where('user_id', Auth::id())->first();
    
        if ($flightHistoryData) {
            $flightHistoryData->delete();
            return response()->json(['message' => 'Flight history deleted successfully'], 200);
        }
    
        return response()->json(['message' => 'Flight history not found'], 404);
    }
}
