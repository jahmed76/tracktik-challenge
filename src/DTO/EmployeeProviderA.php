<?php

namespace App\DTO;

// Schema:
/**
 * {
 * "first_name": string,
 * "last_name": string,
 * "email_address": string,
 * "phone": string,
 * "position": string,
 * "gender": enum (male, female, binary),
 * "birth_date": string (yyyy-mm-dd)
 * }
 * 
 */

class EmployeeProviderA extends Employee
{
    private $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getProvider(): string
    {
        return 'provider_a';
    }

    public function getBirthDate(): ?string {
        return "";
    }

    public function getFirstName()
    {
        return $this->data['first_name'] ?? '';
    }

    public function getLastName()
    {
        return $this->data['last_name'] ?? '';
    }

    public function getEmail(): ?string
    {
        return $this->data['email_address'] ?? null;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->data['phone'] ?? null;
    }

    public function getJobTitle(): ?string
    {
        return $this->data['position'] ?? '';
    }

    public function getGender(): ?Gender
    {
        if (empty($this->data['gender'])) {
            return null;
        }
        switch ($this->data['gender']) {
            case 'Male':
                return Gender::M;
            case 'Female':
                return Gender::F;
            case 'Other':
                return Gender::B;
        }
        return null;
    }

    public function getBirthday(): ?string
    {
        return $this->data['birth_date'] ?? null;
    }
}