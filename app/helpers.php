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

        $defaultMessage = $isSuccessful  ? 'OK' : 'Failed';

        $data = array_merge(array_except($data, 'message'), [
            'status' => [
                'message' => isset($data['message']) ? $data['message'] : $defaultMessage,
                'succeded' => $isSuccessful,
                'code' => $status,
            ]
        ]);

        return response()->json($data, $status, $headers, $options);
    }
}
