<?php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    /**
     * Render the exception into an HTTP response.
     */
    public function render($request)
    {
        return response()->json([
            'message' => $this->getMessage(),
            'errors' => [
                'stock' => [$this->getMessage()],
            ],
        ], 422);
    }
}
