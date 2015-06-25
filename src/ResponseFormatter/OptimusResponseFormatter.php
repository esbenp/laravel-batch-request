<?php

namespace Optimus\BatchRequest\ResponseFormatter;

class OptimusResponseFormatter implements ResponseFormatterInterface {

    public function formatResponse($status, array $successes, array $errors)
    {
        return [
            'status' => trans('batchrequest::responses.' . $status . '_status'),
            'message' => trans('batchrequest::responses.' . $status . '_message'),
            'responses' => array_values(count($errors) > 0 ? $errors : $successes)
        ];
    }

}