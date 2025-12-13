<?php

namespace ZEngine\App\Models;

class WelcomeModel
{
    private function db()
    {
        return db();
    }

    public function getSomething(): ?array
    {
        $something = [
            'someone' => 'John Doe',
            'another' => 'Jane Doe',
        ];

        return [
            'success' => true,
            'veryImportant' => false,
            $something
        ];
    }

}
