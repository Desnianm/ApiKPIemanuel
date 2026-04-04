<?php

namespace App\Http\Controllers\Api;

/**
 * @OA\Info(
 *     title="KPI Emanuel Corp API",
 *     version="1.0.0",
 *     description="API Documentation untuk Aplikasi KPI Emanuel Corp"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 *
 * @OA\Server(
 *     url="http://127.0.0.1:8000",
 *     description="Local Server"
 * )
 */
class SwaggerController
{
}