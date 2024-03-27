<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Services\JsonResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

        if ($request->end < $request->start) {
            return response()->json($this->responseService->getFormat(
                'Start date must be before the end date'), 400);
        }

        $clashingDates = Booking::query()->join('booking_room', 'booking_id', '=', 'booking_id')
            ->where('room_id', $request->room_id)
            ->where('end', '>=', $request->start)
            ->exists();

        if ($clashingDates) {
            return response()->json($this->responseService->getFormat(
                'Room unavailable for the chosen dates'), 400);

        }

        $room = Room::find($request->room_id);

        if ($room->value('max_capacity') < $request->guests | $room->value('min_capacity') > $request->guests) {
            return response()->json($this->responseService->getFormat(
                'The '.$room->value('name').' room can only accommodate between '.$room->value('min_capacity').' and '.$room->value('max_capacity').' guests'), 400);
        }

        $booking = new Booking();

        $booking->customer = $request->customer;
        $booking->guests = $request->guests;
        $booking->start = $request->start;
        $booking->end = $request->end;

        $save = $booking->save();

        $booking->rooms()->attach($request->room_id);

        if (! $save) {
            Log::error('Booking failed');

            return response()->json($this->responseService->getFormat(
                'Booking not saved'
            ), 500);
        }

        return response()->json($this->responseService->getFormat(
            'Booking Created'
        ), 201);

    }

    public function report()
    {
        $rooms = Booking::select('rooms.id', 'rooms.name', 'bookings.start', 'bookings.end')
            ->join('booking_room', 'bookings.id', '=', 'booking_room.booking_id')
            ->join('rooms', 'booking_room.room_id', '=', 'rooms.id')
            ->get();

        $reportData = [];

        foreach($rooms as $room) {
            $roomId = $room->id;
            $roomName = $room->name;
            $bookingStart = $room->start;
            $bookingEnd = $room->end;

            $startDate = new \DateTime($bookingStart);
            $endDate = new \DateTime($bookingEnd);

            $dateDiff = $startDate->diff($endDate);

            if (!isset($reportData[$roomId])) {
                $reportData[$roomId] = [
                    'id' => $roomId,
                    'name' => $roomName,
                    'total_stay_duration' => 0,
                    'booking_count' => 0,
                ];
            }

            $reportData[$roomId]['total_stay_duration'] += $dateDiff->days;
            $reportData[$roomId]['booking_count']++;
        }

        foreach ($reportData as &$roomData) {
            $roomData['average_booking_duration'] = $roomData['booking_count'] > 0 ? round($roomData['total_stay_duration'] / $roomData['booking_count']) : 0;
            unset($roomData['total_stay_duration']);
        }

        $reportData = array_values($reportData);

        $reportData = collect($reportData)->sortBy('id')->values()->all();

        return response()->json($this->responseService->getFormat(
            'report generated',
            $reportData
        ), 200);

    }
}
