<?php

namespace Hemend\Api\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class Api extends Controller
{
    public function __invoke(Request $request, $service, $version, $package, $endpoint) {
        $service_name 	= Str::studly($service);
        $version_name 	= Str::studly($version);
        $package_name 	  = Str::studly($package);
        $endpoint_name 	  = Str::studly($endpoint);

        $serviceClass 	= 'App\Http\Controllers\Api\\' . $service_name;
        $versionClass 	= $serviceClass . '\Version_' . $version_name;
        $packageClass 	= $versionClass . '\\' . $package_name;
        $endpointClass 	= $packageClass . '\\' . $endpoint_name;

        if(!class_exists($serviceClass)) {
            return response()->json([
                'status_code' => 'SERVICE_INVALID',
                'status_message' => sprintf(__('hemend.Service \'%s\' was not found'), $service)
            ], 404);
        }

        if(!class_exists($versionClass)) {
            return response()->json([
                'status_code' => 'VERSION_INVALID',
                'status_message' => sprintf(__('hemend.Version \'%s\' was not found on service \'%s\''), $version, $service_name)
            ], 404);
        }

        if(!class_exists($packageClass)) {
            return response()->json([
                'status_code' => 'PACKAGE_INVALID',
                'status_message' => sprintf(__('hemend.Package \'%s\' was not found on service \'%s\''), $package, $service_name .' v'. $version)
            ], 404);
        }

        if(!class_exists($endpointClass)) {
          return response()->json([
            'status_code' => 'endpoint_INVALID',
            'status_message' => sprintf(__('hemend.Endpoint \'%s\' was not found on package \'%s\''), $endpoint, $service_name .' '. $package_name)
          ], 404);
        }

        $api = new $endpointClass($request);

        if(!$api instanceof $serviceClass) {
            return response()->json([
                'status_code' => 'SERVICE_INSTANCE_INVALID',
                'status_message' => sprintf(__('hemend.\'%s\' is an object of class \'%s\''), $endpointClass, $serviceClass)
            ], 500);
        }

        /*
        if(!$api instanceof $versionClass) {
            return response()->json([
                'status_code' => 'VERSION_INSTANCE_INVALID',
                'status_message' => sprintf(__('hemend.\'%s\' is an object of class \'%s\''), $methodClass, $versionClass)
            ], 500);
        }
        */

        if (method_exists($api, '__invoke')) {
            if(!$api->hasAccess()) {
                return response()->json([
                    'status_code' => 'UNAUTHORIZED',
                    'status_message' => __('hemend.Authentication error occurred'),
                ], 401);
            }

            try {
                $output = call_user_func_array(array($api, '__invoke'), []);
            } catch (\Throwable $e) {
                return response()->json([
                    'status_code' => 'PARAMETERS_INCORRECT',
                   'status_message' => __('hemend.Your request parameters are incorrect.')
                ], 400);
            }

            return $output;
        } else {
            return response()->json([
                'status_code' => 'ENDPOINT_NOT_FOUND',
                'status_message' => sprintf(__('hemend.Endpoint \'%s\' doesn\'t exist on class \'%s\''), '__invoke', $endpoint),
            ], 500);
        }
    }
}
