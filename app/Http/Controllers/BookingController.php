<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Services\JsonResponseService;
use Illuminate\Http\Request;

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

        if ($request->end < $request->start) {
            return response()->json($this->responseService->getFormat(
                'Start date must be before the end date'), 400);
        }

        $clash = Booking::query()->join('booking_room', 'booking_id', '=', 'booking_id')
            ->where('room_id', $request->room_id)
            ->where('end', '>=', $request->start)
            ->where('start', '<=', $request->start)
            ->exists();

        if ($clash) {
            return response()->json($this->responseService->getFormat(
                'Room unavailable for the chosen dates'), 400);
        }

        $clash2 = Room::query()->select('*')
            ->where('id', $request->room_id);

        if ($clash2->value('max_capacity') < $request->guests) {
            return response()->json($this->responseService->getFormat(
                'The '.$clash2->value('name').' room can only accommodate between '.$clash2->value('min_capacity').' and '.$clash2->value('max_capacity').' guests'), 400);
        }

        $save = $booking->save();

        $booking->rooms()->attach($request->room_id);

        if (! $save) {
            return response()->json($this->responseService->getFormat(
                'Booking not saved'
            ), 500);
        }

        return response()->json($this->responseService->getFormat(
            'Booking Created'
        ), 201);
    }
}
