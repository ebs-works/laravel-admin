<?php

namespace EscaliersSolution\LaravelAdmin\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use EscaliersSolution\LaravelAdmin\Models\ApiResource;
use EscaliersSolution\LaravelAdmin\Http\Controllers\AdminModelController;

class ApiResourceController extends AdminModelController
{
    public function viewTest(Request $request, ApiResource $apiResource)
    {
        $this->authorize('test', ApiResource::class);
        $this->viewData['title'] = $apiResource->name;
        $this->viewData['requestButtonText'] = 'Send Request';
        $this->viewData['apiResource'] = $apiResource;
        $this->viewData['fields'] = $apiResource->getFields();
        $this->viewData['pathParameters'] = [];
        preg_match_all('#\{(.*?)\}#', $apiResource->route, $this->viewData['pathParameters']);
        return view('laravel-admin::contents/api-resource/test', $this->viewData);
    }
}
