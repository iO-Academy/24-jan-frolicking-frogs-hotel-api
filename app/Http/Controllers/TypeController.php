<?php

namespace App\Http\Controllers;

use App\Models\Type;
use App\Services\JsonResponseService;

class TypeController extends Controller
{
    private JsonResponseService $responseService;

    public function __construct(JsonResponseService $responseService)
    {
        $this->responseService = $responseService;
    }

    public function all()
    {
        $hidden = ['created_at', 'updated_at'];

        return response()->json($this->responseService->getFormat(
            'Types successfully retrieved',
            Type::get()->makeHidden($hidden)
        ));
    }
}
