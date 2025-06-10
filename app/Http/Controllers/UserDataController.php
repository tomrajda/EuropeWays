<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserData;
use Auth;

class UserDataController extends Controller
{
    public function save(Request $request)
    {
        // Validate request data
        $validatedData = $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        // Get the ID of the currently authenticated user
        $userId = Auth::id();

        // Save data to database
        $userData = new UserData();
        $userData->user_id = $userId; // Assign the user ID
        $userData->username = $validatedData['username'];
        $userData->email = $validatedData['email'];
        $userData->save();

        return response()->json(['message' => 'Data saved successfully'], 200);
    }
}
