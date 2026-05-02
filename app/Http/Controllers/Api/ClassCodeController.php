<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClassCodeResource;
use App\Models\ClassCode;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ClassCodeController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = ClassCode::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('gtin', $search);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $perPage = min((int) $request->input('per_page', 100), 1000);

        return ClassCodeResource::collection(
            $query->with('packageCodes')->paginate($perPage)
        );
    }

    public function show(string $mxik): ClassCodeResource|\Illuminate\Http\JsonResponse
    {
        $classCode = ClassCode::with('packageCodes')->find($mxik);

        if (! $classCode) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        return new ClassCodeResource($classCode);
    }
}
