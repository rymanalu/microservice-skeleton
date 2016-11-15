<?php

/*
 |--------------------------------------------------------------------------
 | App Helpers
 |--------------------------------------------------------------------------
 |
 | Here, we will define the global functions (helpers) that will be
 | used in our application. Give a try to add your awesome helpers!
 |
 | @author  Roni Yusuf Manalu  <roni.y@smooets.com>
 |
 */

if (! function_exists('log_exception')) {
    /**
     * Log the given exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    function log_exception(Exception $exception)
    {
        Log::error(
            'Message: '.$exception->getMessage().PHP_EOL.
            'Code: '.$exception->getCode().PHP_EOL.
            'File: '.$exception->getFile().PHP_EOL.
            'Line: '.$exception->getLine()
        );
    }
}

if (! function_exists('response_json')) {
    /**
     * Return a new JSON response from the application.
     *
     * @param  array  $data
     * @param  int  $status
     * @param  array  $headers
     * @param  int  $options
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function response_json(array $data = [], $status = 200, array $headers = [], $options = 0)
    {
        $isSuccessful = $status >= 200 && $status < 300;

        $defaultMessage = $isSuccessful ? 'OK' : 'Failed';

        $data = array_merge(array_except($data, 'message'), [
            'status' => [
                'message' => isset($data['message']) ? $data['message'] : $defaultMessage,
                'succeded' => $isSuccessful,
                'code' => $status,
            ],
        ]);

        return response()->json($data, $status, $headers, $options);
    }
}

if (! function_exists('transaction')) {
    /**
     * Execute a Closure within a transaction.
     *
     * @param  \Closure  $callback
     * @return mixed
     */
    function transaction(Closure $callback)
    {
        DB::beginTransaction();

        try {
            $result = $callback();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            log_exception($e);

            $result = false;
        }

        return $result;
    }
}
