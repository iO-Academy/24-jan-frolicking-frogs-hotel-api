<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\JsonResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    private JsonResponseService $responseService;

    public function __construct(JsonResponseService $responseService)
    {
        $this->responseService = $responseService;
    }
    public function create(Request $request)
    {
        $hidden = ['created_at', 'updated_at'];
        $booking = new Booking();

        $booking->customer = $request->customer;
        $booking->guests = $request->guests;
        $booking->start = $request->start;
        $booking->end = $request->end;

        $booking->rooms()->attach($request->room_id);

        $clash = DB::table('rooms')
            ->where('room_id', '=', $request->room_id)
            ->where('end', '>', $request->end);

        if ($clash) {
            return response()->json($this->responseService->getFormat(
                'Room unavailable for the chosen dates'),400);
        }

        $save = $booking->save();



        if (!$save) {
            return response()->json($this->responseService->getFormat(
                'Booking not saved'
            ),500);
        }

        return response()->json($this->responseService->getFormat(
            'Booking Created'
        ),201);
    }
}
