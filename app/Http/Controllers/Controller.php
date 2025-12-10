<?php
declare(strict_types=1);
namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Appointments Manager API Documentation",
 *     description="API documentation for the Appointments Manager application.",
 *     @OA\Contact(
 *         email="hello@frankpulido.com"
 *     ),
 *     @OA\License(
    *         name="Apache 2.0",
    *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
    *     )
 * )
 * 
 * @OA\Server(
 *     url="http://localhost:8000/api",
 *     description="Local Development Server"
 * )
 * 
 * @OA\Server(
 *     url="https://appointmentmanager-production-5057.up.railway.app/api",
 *     description="Production Server"
 * )
 * 
 * @OA\Tag(
 *     name="AppointmentManager",
 *     description="API Endpoints for Managing AppointmentManager Application"
 * )
 */

abstract class Controller
{
    //
}
