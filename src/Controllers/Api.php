<?php

namespace Hemend\Api\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Api extends Controller
{
    public function __invoke(Request $request, $service, $version, $method) {
        $service_name 	= Str::studly($service);
        $version_name 	= str_replace('.', '_', Str::studly($version));
        $method_name 	= Str::studly(str_replace('.', '-', $method));

        $serviceClass 	= 'App\Http\Controllers\Api\\' . $service_name;
        $versionClass 	= $serviceClass . '\Version_' . $version_name;
        $methodClass 	= $versionClass . '\\' . $method_name;

        if(!class_exists($serviceClass)) {
            return response()->json([
                'status_code' => 'SERVICE_INVALID',
                'status_message' => sprintf(__('messages.Service \'%s\' was not found'), $service)
            ], 404);
        }

        if(!class_exists($versionClass)) {
            return response()->json([
                'status_code' => 'VERSION_INVALID',
                'status_message' => sprintf(__('messages.Version \'%s\' was not found on service \'%s\''), $version, $service_name)
            ], 404);
        }

        if(!class_exists($methodClass)) {
            return response()->json([
                'status_code' => 'METHOD_INVALID',
                'status_message' => sprintf(__('messages.Method \'%s\' was not found on service \'%s\''), $method, $service_name .' v'. $version)
            ], 404);
        }

        $api = new $methodClass($request);

        if(!$api instanceof $serviceClass) {
            return response()->json([
                'status_code' => 'SERVICE_INSTANCE_INVALID',
                'status_message' => sprintf(__('messages.\'%s\' is an object of class \'%s\''), $methodClass, $serviceClass)
            ], 500);
        }

        /*
        if(!$api instanceof $versionClass) {
            return response()->json([
                'status_code' => 'VERSION_INSTANCE_INVALID',
                'status_message' => sprintf(__('messages.\'%s\' is an object of class \'%s\''), $methodClass, $versionClass)
            ], 500);
        }
        */

        if (method_exists($api, '__invoke')) {
            if ($api->getRunType() == $methodClass::PRIVATE_FLAG || $api->getToken()) {
                if(!$api->hasIdentity()) {
                    return response()->json([
                        'status_code' => 'UNAUTHORIZED',
                        'status_message' => __('messages.Authentication error occurred'),
                    ], 401);
                }
            }

            try {
                $output = call_user_func_array(array($api, '__invoke'), []);
            } catch (\Throwable $e) {
                return response()->json([
                    'status_code' => 'PARAMETERS_INCORRECT',
                   'status_message' => __('messages.Your request parameters are incorrect.')
                ], 400);
            }

            return $output;
        } else {
            return response()->json([
                'status_code' => 'METHOD_NOT_FOUND',
                'status_message' => sprintf(__('messages.Method \'%s\' doesn\'t exist on class \'%s\''), '__invoke', $method),
            ], 500);
        }
    }
}
