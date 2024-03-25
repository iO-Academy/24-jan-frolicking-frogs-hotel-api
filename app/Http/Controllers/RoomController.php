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
        $hidden = ['description', 'rate'];

        return response()->json($this->responseService->getFormat(
            'Rooms successfully retrieved',
            Room::with('type: id, name')->get()->makeHidden($hidden)
        ));
    }
}
