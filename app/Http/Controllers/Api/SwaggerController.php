<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use OpenApi\Attributes as OA;

#[OA\Info(
    title: "EasyKos API Documentation",
    version: "1.0.0",
    description: "Dokumentasi API untuk aplikasi EasyKos - Manajemen Kos-kosan",
    contact: new OA\Contact(email: "admin@easykos.com")
)]
#[OA\Server(
    url: "http://localhost:8000",
    description: "EasyKos API Server Local"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    name: "Authorization",
    in: "header",
    scheme: "bearer",
    bearerFormat: "JWT",
    description: "Masukkan token JWT Anda di sini. Cukup masukkan tokennya saja (tanpa kata 'Bearer ')"
)]
class SwaggerController extends Controller
{
    //
}
