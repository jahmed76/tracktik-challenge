<?php

namespace App\DTO;

// Schema:
/**
 * {
 * "fname": string,
 * "lname": string,
 * "contact": object (email string, address object),
 * "phone": string,
 * "role": string,
 * "sex": enum (male, female, other),
 * "dob": object
 * }
 * 
 */
class EmployeeProviderB extends Employee
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getProvider(): string
    {
        return 'provider_b';
    }

    public function getFirstName()
    {
        return $this->data['fname'] ?? '';
    }

    public function getLastName()
    {
        return $this->data['lname'] ?? '';
    }

    public function getEmail(): ?string
    {
        return $this->data['contact']['email'] ?? null;
    }

    public function getJobTitle(): ?string
    {
        return $this->data['role'] ?? null;
    }

    public function getGender(): ?Gender
    {
        if (empty($this->data['sex'])) {
            return null;
        }
        switch ($this->data['sex']) {
            case 'male':
                return Gender::M;
            case 'female':
                return Gender::F;
            case 'other':
                return Gender::B;
        }
        return null;
    }

    public function getBirthday(): ?string
    {
        $dob = $this->data['dob'] ?? [];
        if ($dob && is_array($dob) && empty(array_diff_key(array_flip(['year', 'month','day']), $dob))) {
            return sprintf("%s-%s-%s", $dob['year'],  $dob['month'],  $dob['day']);
        }

        return null;
    }
}
