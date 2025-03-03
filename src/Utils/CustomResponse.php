<?php

namespace App\Utils;

class CustomResponse {
    public bool $success;
    public ?string $message;
    public mixed $data;

    public function __construct(bool $success, mixed $data = [], ?string $message = null) {
        $this->success = $success;
        $this->message = $message;
        $this->data = $data;
        
    }
}


?>
