<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\ContactMail;

class ContactController extends Controller
{
    public function sendMail(Request $request)
    {
        try {
            Log::info('Contact form geldi: ' . json_encode($request->all()));
            
            $data = $request->validate([
                'name'    => 'required|string|max:255',
                'email'   => 'required|email',
                'subject' => 'nullable|string|max:255',
                'message' => 'required|string',
            ]);

            // MAIL GÖNDER
            Mail::to('ibrahimparlak282@gmail.com')
                ->send(new ContactMail($data));

            Log::info('
Email sent: ' . $data['email']);

            return response()->json([
                'success' => true,
                'message' => 'Your message has been sent successfully!'
            ]);

        } catch (\Throwable $e) {
            Log::error('HATA: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}