<?php

namespace App\DTO;

abstract class Employee
{
    public function getRequiredFields(): array
    {
        return [
            'firstName' => $this->getFirstName(),
            'lastName' => $this->getLastName(),
        ];
    }

    abstract public function getProvider();
    abstract public function getFirstName();
    abstract public function getLastName();

    public function getEmail(): ?string
    {
        return null;
    }

    public function getUsername(): ?string
    {
        return null;
    }

    public function getPassword(): ?string
    {
        return null;
    }

    public function getAddress(): ?array
    {
        return [];
    }

    public function getJobTitle(): ?string
    {
        return null;
    }

    public function getGender(): ?Gender
    {
        return null;
    }

    public function getBirthday(): ?string
    {
        return null;
    }

    public function prepare(): array
    {
        $gender = $this->getGender() ? $this->getGender()->value : null;
        $payload = array_merge($this->getRequiredFields(), [
            'email' => $this->getEmail(),
            'username' => $this->getUsername(),
            'password' => $this->getPassword(),
            'address' => $this->getAddress(),
            'jobTitle' => $this->getJobTitle(),
            'gender' => $gender,
            'birthday' => $this->getBirthday(),
        ]);

        return array_filter($payload);
    }

    public function validate(): string
    {
        if (empty($this->getFirstName())) {
            return 'FIRST_NAME_REQUIRED';
        }

        if (empty($this->getLastName())) {
            return 'LAST_NAME_REQUIRED';
        }

        if (Gender::isValid($this->getGender()))  {
            return 'GENDER_REQUIRED';
        }

        return "";
    }
}
