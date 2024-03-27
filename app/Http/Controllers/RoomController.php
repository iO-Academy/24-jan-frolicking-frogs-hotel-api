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
        $hidden = ['description', 'rate'];

        $filter = Room::query()->with('type:id,name');

        $searchType = $request->input('type');
        $searchCapacity = $request->input('guests');
        $search = $searchCapacity + $searchType;

        if ($search) {

            $request->validate([
                'type' => 'exists:types,id',
                'guests' => 'integer|min:0'
            ]);

            if ($searchType) {
                $filter->whereRelation('type', 'type_id', '=', "$searchType");

            }
            if ($searchCapacity) {
                $filter->where('min_capacity', '<=', "$searchCapacity")
                    ->where('max_capacity', '>=', "$searchCapacity");
            }

            return response()->json($this->responseService->getFormat(
                'Filtered Rooms retrieved', $filter->get()->makeHidden($hidden)));
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
