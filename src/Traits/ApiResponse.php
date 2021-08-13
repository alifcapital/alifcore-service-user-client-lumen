<?php


namespace AlifCapital\UserServiceClient\Traits;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait ApiResponse
{
    /**
     * Building success response
     * @param int $code
     * @param null $message
     * @param array $response
     * @return JsonResponse
     */
    public function successResponse(int $code = Response::HTTP_OK, $message = null, array $response = []): JsonResponse
    {
        $response = $this->meta($code, $message) + ['response' => $response];
        return \response()->json($response, $code);
    }


    /**
     * @param int $code
     * @param null $message
     * @param array $response
     * @return JsonResponse
     */
    public function errorResponse(int $code = Response::HTTP_INTERNAL_SERVER_ERROR, $message = null, array $response = []): JsonResponse
    {
        $response = $this->meta($code, $message, true) + ['response' => $response];
        return \response()->json($response, $code);
    }

    /**
     * @param int $code
     * @param null $message
     * @param false $error
     * @return array[]
     */
    private function meta(int $code = Response::HTTP_OK, $message = null, bool $error = false): array
    {
        return [
            'meta' => [
                'error' => $error,
                'message' => $message,
                'statusCode' => $code,
            ]
        ];
    }

}
