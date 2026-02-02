<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProxyController extends Controller
{

    private string $baseUrl = "https://tasnif.soliq.uz/api/cl-api/integration-mxik/get/all/history/time-json";

    public function handle(Request $request, string $path = '')
    {
        $url = $this->baseUrl;
        $method = "GET";
        $headers = $this->prepareHeaders($request);

        return new StreamedResponse(function () use ($url, $method, $headers, $request) {
            $ch = curl_init($url);

            curl_setopt_array($ch, [
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_HTTPHEADER    => $headers,
                CURLOPT_POSTFIELDS    => $request->getContent(),
                CURLOPT_RETURNTRANSFER => false, // 🔥 STREAM
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT        => 0,
                CURLOPT_WRITEFUNCTION  => function ($ch, $data) {
                    echo $data;        // 👉 сразу клиенту
                    flush();
                    return strlen($data);
                },
            ]);

            curl_exec($ch);
            curl_close($ch);
        }, 200, [
            'Content-Type' => 'application/json',
            'Transfer-Encoding' => 'chunked',
        ]);
    }

    private function prepareHeaders(Request $request): array
    {
        $headers = [];

        foreach ($request->headers->all() as $key => $values) {
            if (in_array(strtolower($key), ['host', 'content-length'])) {
                continue;
            }
            $headers[] = ucfirst($key) . ': ' . $values[0];
        }

        return $headers;
    }
}
