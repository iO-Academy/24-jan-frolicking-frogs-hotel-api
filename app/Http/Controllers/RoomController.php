<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Services\JsonResponseService;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    private JsonResponseService $responseService;

    public function __construct(JsonResponseService $responseService)
    {
        $this->responseService = $responseService;
    }

    public function all(Request $request)
    {
        $hidden = ['description', 'rate', 'booking'];

        $filter = Room::query()->with(['type:id,name', 'booking:id,start,end']);

        $filterType = $request->input('type');
        $filterCapacity = $request->input('guests');
        $filterAvailableStart = $request->input('start');
        $filterAvailableEnd = $request->input('end');
        $filterRooms = [$filterCapacity, $filterType, $filterAvailableEnd, $filterAvailableStart];

        if ($filterRooms) {

            $request->validate([
                'type' => 'exists:types,id',
                'guests' => 'integer|min:0',
                'start' => 'date',
                'end' => 'date'
            ]);

            if ($filterType) {
                $filter->whereRelation('type', 'type_id', '=', "$filterType");

            }
            if ($filterCapacity) {
                $filter->where('min_capacity', '<=', "$filterCapacity")
                    ->where('max_capacity', '>=', "$filterCapacity");
            }
            if ($filterAvailableEnd && $filterAvailableStart) {
                $filter->whereRelation('booking', 'start', '<=', $filterAvailableStart)
                    ->whereRelation('booking', 'end', '<=', $filterAvailableStart)
                    ->orWhereRelation('booking','start', '>=', $filterAvailableEnd);
            }

            return response()->json($this->responseService->getFormat(
                'Rooms successfully retrieved', $filter->get()->makeHidden($hidden)));
        }

        return response()->json($this->responseService->getFormat(
            'Rooms successfully retrieved',
            Room::with('type:id,name')->get()->makeHidden($hidden)
        ));
    }

    public function find(int $id)
    {
        $room = Room::with('type:id,name')->find($id);

        if (! $room) {
            return response()->json([
                'message' => 'Room with id '.$id.' not found',
            ], 404);
        }

        return response()->json($this->responseService->getFormat(
            'Room successfully retrieved', $room
        ), 200);

    }


}
