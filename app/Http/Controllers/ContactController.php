<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact');
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        try {
            // Send email
            Mail::send('emails.contact', $validated, function ($message) use ($validated) {
                $message->to('hello@adasystems.uk')
                        ->subject('Contact Form: ' . $validated['subject'])
                        ->replyTo($validated['email'], $validated['name']);
            });

            return redirect()->route('contact.show')->with('success', 'Thank you for contacting us! We\'ll get back to you shortly.');
        } catch (\Exception $e) {
            return redirect()->route('contact.show')->with('error', 'Sorry, there was an error sending your message. Please try again or email us directly at hello@adasystems.uk');
        }
    }
}
