<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function sendInvitationEmailToUser(Request $request) {
        try {
            if($request->email) {
                // send invitation link to user email
                $sender_email = 'walimstr218@gmail.com';
                $data = [
                    'link' => route('register')
                ];
                Mail::send('email', ['data' => $data], function ($message) use ($request, $sender_email) {
                    $message->from($sender_email);
                    $message->to($request->email)->subject("Invitation");
                });
                return collect([
                   'status' => true,
                   'message' => 'email send to user successfully ... !'
                ]);
            }
        } catch (\Exception $e) {
            return collect([
               'status' => false,
               'message' => $e->getMessage()
            ]);
        }
    }
}
