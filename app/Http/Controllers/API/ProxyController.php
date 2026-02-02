<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProxyController extends Controller
{

    private string $baseUrl = "https://tasnif.soliq.uz";

    public function handle(Request $request, string $path = '')
    {
        $method = $request->method();

        // собираем финальный URL
        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($path, '/');

        $response = Http::withHeaders(
            $this->filterHeaders($request->headers->all())
        )
            ->send($method, $url, [
                'body'    => $request->getContent(),
                'timeout' => 30,
            ]);

        return response(
            $response->body(),
            $response->status()
        )->withHeaders($response->headers());
    }

    private function filterHeaders(array $headers): array
    {
        unset(
            $headers['host'],
            $headers['content-length']
        );

        return collect($headers)
            ->map(fn($v) => $v[0])
            ->toArray();
    }
}
