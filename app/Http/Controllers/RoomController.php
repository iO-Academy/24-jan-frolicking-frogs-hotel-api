<?php

namespace App\Http\Controllers;

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
    public function all()
    {
        $hidden = ['description', 'rate', 'type_id'];

        return response()->json($this->responseService->getFormat(
            'Rooms successfully retrieved',
            Room::with('type:id,name')->get()->makeHidden($hidden)
        ));
    }

    public function find(int $id)
    {
        $room = Room::with('type:id,name')->find($id)->makeHidden('type_id');

        if (! $room) {
            return response()->json([
                'message' => 'Room with id ' . $id .' not found'
            ], 404);
        }

        return response()->json($this->responseService->getFormat(
            'Room successfully retrieved', $room
        ), 200);

    }
}
