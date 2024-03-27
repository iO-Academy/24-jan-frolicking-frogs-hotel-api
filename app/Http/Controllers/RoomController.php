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

        $hidden = ['description'];
        $search = $request->input('type');
        $data = Room::with('type:id,name')->whereRelation('type', 'type_id', '=', "$search")->get()->makeHidden($hidden);

        if ($search) {

            if ($data->isEmpty()) {
                return response()->json($this->responseService->getFormat(
                    'The selected type is invalid.'));
            }

            return response()->json(['data' => $data], 200);
        }

        $hidden = ['description', 'rate'];

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
