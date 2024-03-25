<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
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
        $booking = new Booking();

        $booking->customer = $request->customer;
        $booking->guests = $request->guests;
        $booking->start = $request->start;
        $booking->end = $request->end;

        $clash = Booking::query()->join('booking_room', 'booking_id', '=', 'booking_id')
            ->where('room_id', $request->room_id)
            ->where('end', '>=', $request->start)
            ->where('start', '<=',$request->start)
            ->exists();

        if ($clash) {
            return response()->json($this->responseService->getFormat(
                'Room unavailable for the chosen dates'),400);
        }

        $save = $booking->save();

        $booking->rooms()->attach($request->room_id);

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
