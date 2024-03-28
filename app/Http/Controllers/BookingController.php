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
            ->where('start', '<=', $request->end)
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

        // Loop through rooms as room to get each individual data set
        foreach ($rooms as $room) {
            $roomId = $room->id;
            $roomName = $room->name;
            $bookingStart = $room->start;
            $bookingEnd = $room->end;

            // Convert the dates into integer
            $startDate = new \DateTime($bookingStart);
            $endDate = new \DateTime($bookingEnd);

            // Calculate the difference between the converted dates
            $dateDiff = $startDate->diff($endDate);

            // If there isn't a booking - display 0 for count and total stay duration
            if (! isset($bookingData[$roomId])) {
                $bookingData[$roomId] = [
                    'id' => $roomId,
                    'name' => $roomName,
                    'total_stay_duration' => 0,
                    'booking_count' => 0,
                ];
            }

            // Sum of the days booked per room (to calc the average)
            $bookingData[$roomId]['total_stay_duration'] += $dateDiff->days;

            // Within the foreach loop, iterate through the bookings, and count the number of bookings for each room  (to calc the average)
            $bookingData[$roomId]['booking_count']++;
        }

        // Loop through each of the booking data and access the booking count and total stay duration
        foreach ($bookingData as &$roomData) {
            // Ternary operator used to check if booking count is greater than 0, then run the average calc of 'total_stay_duration' / 'booking_count'
            // Calc result is rounded to 1 decimal place, else the average returns 0
            $roomData['average_booking_duration'] = $roomData['booking_count'] > 0 ? round($roomData['total_stay_duration'] / $roomData['booking_count'], 1) : 0;
            // unset removes the 'total_stay_duration' from the results as it was only used for the average calc
            unset($roomData['total_stay_duration']);
        }

        // Returns the reportData as an array to put into the empty array
        $reportData = array_values($bookingData);

        // Sorts the reportData by id value
        $reportData = collect($reportData)->sortBy('id')->values()->all();

        return response()->json($this->responseService->getFormat(
            'report generated',
            $reportData
        ), 200);
    }

    public function all(Request $request)
    {
        $RoomFilter = $request->input('room_id');
        $hidden = ['guests', 'room_id', 'booking_id', 'guests', 'updated_at'];
        $date = today()->toDateString();

        if ($RoomFilter) {

            return response()->json($this->responseService->getFormat(
                'Bookings successfully retrieved',
                Booking::with(['rooms:id,name'])->whereRelation('rooms', 'room_id', '=', "$RoomFilter")
                    ->whereDate('end', '>=', $date)->orderBy('start', 'asc')->get()->makeHidden($hidden)), 200);

        }

        return response()->json($this->responseService->getFormat(
            'Bookings successfully retrieved',
            Booking::with('rooms:id,name')->whereDate('end', '>=', $date)
                ->orderBy('start', 'asc')->get()->makeHidden($hidden)
        ));
    }

    public function delete(int $id)
    {
        $booking = Booking::find($id);
        if (! $booking) {
            return response()->json($this->responseService
                ->getFormat('Unable to cancel booking, booking '.$id.' not found'));
        }

        $booking->delete();

        return response()->json($this->responseService
            ->getFormat('Booking '.$id.' deleted'));
    }
}
