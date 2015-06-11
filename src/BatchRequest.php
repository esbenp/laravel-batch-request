<?php

namespace Optimus\LaravelBatch;

use DB;
use Exception;
use Request;
use Optimus\LaravelBatch\Action;
use Optimus\LaravelBatch\ResponseFormatter\ResponseFormatterInterface;
use Optimus\LaravelBatch\ResultFormatter\ResultFormatterInterface;
use Illuminate\Routing\Router;

class BatchRequest {

    private $router;

    private $result;

    private $currentRequest;

    private $responseFormatter;

    private $resultFormatter;

    /**
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->currentRequest = Request::instance();
        $this->router = $router;
    }

    /**
     * Handle an array of actions to be performed. 
     * The format:
     * [
     *     {
     *         action: "settings/1", // will be run as /settings/1
     *         method: "PUT",
     *         key: "settings", // a key used to identify the response,
     *         data: [], // key, value array of data to send with the request
     *         headers: [] // key, value array of headers to send with the request
     *     }
     * ]
     * 
     * @param  array $actions 
     * @return void          
     */
    public function request(array $actions) 
    {
        $results = array();

        // When dispatching the action we need to replace the 
        // batch's input with the data given by the request. 
        // We therefore store the batch request input data, so 
        // we are able to reset the input after all actions
        // have been run
        $originalInput = $this->currentRequest->input();

        // We begin a database transaction, so the we can rollback
        // if there are errors in one or more of the actions.
        $this->beginDatabaseTransaction();

        foreach($actions as $action) {
            // Create an action object from the data
            $action = Action::createFromArray($action);

            // Create a Illuminate request object from the action
            $request = $this->createRequest($action);

            // Add custom headers to the request
            // We also save all the old headers in the additions array
            // Headers we have overridden will be given as {header-type: value}
            // Non-existent headers, which we want to remove after the action has run
            // are given as {header-type: null}
            $additions = $this->addHeadersToRequest($this->currentRequest, $action->headers);

            // Replace the input of the batch request with the action input
            $this->replaceInput($request->input());

            try {
                // Run the action through the router
                $result = $this->router->dispatch($request);

                // Create a batch response from the action and result
                $response = $this->createResponse($action, $result);
            } catch(\Symfony\Component\HttpKernel\Exception\HttpException $e) {
                if (!config('batchrequest.catch_http_exceptions')) {
                    throw $e;
                }

                $symfonyResponse = \Symfony\Component\HttpFoundation\Response::create(
                    $e->getMessage(),
                    $e->getStatusCode(),
                    $e->getHeaders()
                );

                $response = $this->createResponse($action, $symfonyResponse);
            }

            // Remove headers added to the batch request, and readd removed ones
            $this->addHeadersToRequest($this->currentRequest, $additions);

            // Save the response in the combined result array
            // If a key is given, save the response with given key
            if ($action->key !== null) {
                $results[$action->key] = $response;
            } else {
                $results[] = $response;
            }
        }

        // Reset the batch request's input to the original input
        $this->replaceInput($originalInput);

        // Save the result in the instance
        $this->result = $results;
    }

    /**
     * Format the results into an api response.
     * If the results contained a special error, e.g. 401, 412 etc.
     * we just return the given JsonResponse with the proper status code.
     *
     * If an action was a resource (i.e. a GET request) we return the data
     * If an action was an action (i.e. POST/PUT/DELETE request) we return 
     * a normal API response
     * On PUT requests, we add the etag so it can be used client side.
     * 
     * @return array|\Illuminate\Http\Response 
     */
    public function response()
    {
        // Array of all error responses
        $errors = [];
        // Array of all successfull responses
        $successes = [];

        // Was there an error in the result collection?
        $errorneous = false;
        foreach($this->result as $key => $result) {
            if ($result->statusCode < 200 || $result->statusCode >= 300) {
                $errorneous = true;
                $errors[$key] = $this->generateResult($result);
            } else {
                $successes[$key] = $this->generateResult($result);
            }
        }

        // The result collection was errorneous. Rollback the database
        // and return all the error messages
        if ($errorneous === true) {
            if (config('batchrequest.rollback_db_transactions_on_error')) {
                $this->rollbackDatabaseTransaction();
            }

            return $this->generateResponse(
                "error",
                $successes,
                $errors
            );
        }

        // Everything ran smoothely. Commit all database transactions and 
        // return all the success responses.
        $this->commitDatabaseTransaction();

        return $this->generateResponse(
            "success",
            $successes,
            $errors
        );
    }

    public function setResponseFormatter(ResponseFormatterInterface $responseFormatter)
    {
        $this->responseFormatter = $responseFormatter;
    }

    public function setResultFormatter(ResultFormatterInterface $resultFormatter)
    {
        $this->resultFormatter = $resultFormatter;
    }

    /**
     * Add/override headers of request
     * Returns an array of old/added headers used to reset batch request 
     * headers after an action has run
     * 
     * @param \Illuminate\Http\Request $request
     * @param array                    $headers
     */
    private function addHeadersToRequest(\Illuminate\Http\Request $request, array $headers)
    {
        $additions = [];
        // Get the headers of the request (the batch request)
        $headerBag = $request->headers;

        foreach($headers as $headerType => $value) {
            // If the value is null it means we are removing action headers previously 
            // added to the batch request
            if ($value === null) {
                $headerBag->remove($headerType);
                continue;
            }

            // Save old value so it can be readded
            if ($headerBag->has($headerType)) {
                $additions[$headerType] = $headerBag->get($headerType);

            // Save as null to signal this should 
            // be removed again
            } else {
                $additions[$headerType] = null;
            }

            $request->headers->set($headerType, $value);
        }

        return $additions;
    }

    /**
     * Create a batch response object with meta data 
     * and response data. A simple DTO.
     * 
     * @param  Action $action
     * @param  \Symfony\Component\HttpFoundation\Response $result
     * @return Response        
     */
    private function createResponse(Action $action, \Symfony\Component\HttpFoundation\Response $result)
    {
        $response = new Response;
        $response->responseObject = $result;
        $response->statusCode = $result->getStatusCode();
        $response->action = $action;
        $response->data = json_decode($result->getContent());

        return $response;
    }

    /**
     * @return void
     */
    private function beginDatabaseTransaction()
    {
        DB::beginTransaction();
    }

    /**
     * @return void
     */
    private function rollbackDatabaseTransaction()
    {
        DB::rollback();
    }

    /**
     * @return void
     */
    private function commitDatabaseTransaction()
    {
        DB::commit();
    }

    /**
     * Replace the input of the batch request
     * 
     * @param  $input
     * @return void       
     */
    private function replaceInput($input)
    {
        $this->currentRequest->replace($input);
    }

    /**
     * Create an illuminate request object representing
     * the action being executed
     * 
     * @param  Action $action [description]
     * @return [type]         [description]
     */
    private function createRequest(Action $action)
    {
        return Request::create(sprintf("%s%s", config('batchrequest.url_prefix'), $action->url), $action->method, $action->data);
    }

    private function generateResponse($status, array $successes, array $errors)
    {
        return $this->responseFormatter->formatResponse($status, $successes, $errors);
    }

    private function generateResult(Response $result)
    {
        return $this->resultFormatter->formatResult($result);
    }

}