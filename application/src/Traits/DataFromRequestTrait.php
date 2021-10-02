<?php
declare(strict_types=1);

namespace App\Traits;

use App\Exception\ApiException;
use App\Helper\JsonHelper;
use Symfony\Component\HttpFoundation\Request;

trait DataFromRequestTrait
{
    /**
     * @param  Request  $request
     *
     * @return mixed
     * @throws ApiException
     */
    private function getDataFromRequest(Request $request)
    {
        return JsonHelper::decode($request->getContent());
    }
}