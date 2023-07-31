<?php

if(!function_exists('formatCPF')) {
    function formatCPF(string $cpf): string
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);
        $formattedCPF = substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);

        return $formattedCPF;
    }
}

if(!function_exists('formatPhone')) {
    function formatPhone(string $phoneNumber): string
    {
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        $length = strlen($phoneNumber);

        if ($length == 10) {
            $formattedPhoneNumber = '(' . substr($phoneNumber, 0, 2) . ') ' . substr($phoneNumber, 2, 4) . '-' . substr($phoneNumber, 6, 4);
        } elseif ($length == 11) {
            $formattedPhoneNumber = '(' . substr($phoneNumber, 0, 2) . ') ' . substr($phoneNumber, 2, 5) . '-' . substr($phoneNumber, 7, 4);
        } else {
            $formattedPhoneNumber = $phoneNumber;
        }

        return $formattedPhoneNumber;

    }
}

if(!function_exists('formatOnlyNumber')) {
    function formatOnlyNumber(?string $text): string
    {
        return preg_replace('/[^0-9]/', '', $text);
    }
}
